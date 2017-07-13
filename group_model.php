<?php

/*
  All Emoncms code is released under the GNU Affero General Public License.
  See COPYRIGHT.txt and LICENSE.txt.

  ---------------------------------------------------------------------
  Emoncms - open source energy visualisation
  Part of the OpenEnergyMonitor project:
  http://openenergymonitor.org

 */

// Access
// 1: Admin
// no direct access
defined('EMONCMS_EXEC') or die('Restricted access');

class Group {

    private $mysqli;
    private $user;
    private $feed;
    private $redis;

    public function __construct($mysqli, $redis, $user, $feed) {
        $this->mysqli = $mysqli;
        $this->user = $user;
        $this->feed = $feed;
        $this->redis = $redis;
    }

    // Create group, add creator user as administrator
    public function create($userid, $name, $description, $organization, $area, $visibility, $access) {
        // Input sanitisation
        $userid = (int) $userid;
        $name = preg_replace('/[^\w\s-:]/', '', $name);
        $description = preg_replace('/[^\w\s-:]/', '', $description);
        $organization = preg_replace('/[^\w\s-:]/', '', $organization);
        $area = preg_replace('/[^\w\s-:]/', '', $area);
        $visibility = $visibility == 'public' ? 'public' : 'private';
        $access = $access == 'open' ? 'open' : 'closed';


        if ($this->exists_name($name))
            return array('success' => false, 'message' => _("Group already exists"));

        $stmt = $this->mysqli->prepare("INSERT INTO groups (name,description, organization, area, visibility, access) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param("ssssss", $name, $description, $organization, $area, $visibility, $access);
        if (!$stmt->execute()) {
            return array('success' => false, 'message' => _("Error creating group"));
        }
        $groupid = $this->mysqli->insert_id;

        if (!$this->add_user($groupid, $userid, 1))
            return array('success' => false, 'message' => _("Error adding user to group"));

        return array('success' => true, 'groupid' => $groupid, 'message' => _("Group $groupid added"));
    }

    // Add user to a group if admin user knows account username and password
    public function add_user_auth($admin_userid, $groupid, $username, $password, $role) {
        // Input sanitisation
        $admin_userid = (int) $admin_userid;
        $groupid = (int) $groupid;
        $role = (int) $role;
        // username and password checked within $user model

        if (!$this->exists($groupid))
            return array('success' => false, 'message' => _("Group does not exist"));

        // 1. Check that user is a group administrator
        if (!$this->is_group_admin($groupid, $admin_userid))
            return array('success' => false, 'message' => _("User is not a member or does not have access to group"));

        // 2. Check username and password, return
        $result = $this->user->get_apikeys_from_login($username, $password);
        if (!$result["success"])
            return $result;
        $add_userid = $result["userid"];

        // 3. Add user to group 
        if (!$this->add_user($groupid, $add_userid, $role))
            return array('success' => false, 'message' => _("Error adding user to group"));

        return array('success' => true, 'message' => _("User $add_userid:$username added"));
    }

    // Send email invite to user to join a group
    public function add_user_invite($userid, $groupid, $invite_userid) {
        // Input sanitisation
        $userid = (int) $userid;
        $groupid = (int) $groupid;
    }

    // Return list of groups for which $userid is an administrator
    public function grouplist($userid) {
        // Input sanitisation
        $userid = (int) $userid;

        $stmt = $this->mysqli->prepare("SELECT groupid FROM group_users WHERE userid = ? AND role=1");
        $stmt->bind_param("i", $userid);
        if (!$stmt->execute())
            return false;
        $stmt->bind_result($groupid);

        $groupids = array();
        while ($stmt->fetch())
            $groupids[] = $groupid;

        $groups = array();
        foreach ($groupids as $groupid) {
            $result = $this->mysqli->query("SELECT * FROM groups WHERE id = $groupid");
            $row = $result->fetch_object();
            $groups[] = array(
                "groupid" => (int) $row->id,
                "name" => $row->name,
                "description" => $row->description
            );
        }

        return $groups;
    }

    // Return list of groups for which $userid is an administrator
    public function userlist($userid, $groupid) {
        // Input sanitisation
        $userid = (int) $userid;
        $groupid = (int) $groupid;

        // 1. Check that user is a group administrator
        if (!$this->is_group_admin($groupid, $userid))
            return array('success' => false, 'message' => _("User is not a member or does not have access to group"));

        $userlist = array();
        $result = $this->mysqli->query("SELECT userid,role FROM group_users WHERE groupid = $groupid");
        while ($row = $result->fetch_object()) {
            // Calculate number of active feeds
            $active = 0;
            $total = 0;
            $now = time();
            $result2 = $this->mysqli->query("SELECT id FROM feeds WHERE userid=" . $row->userid);
            while ($row2 = $result2->fetch_object()) {
                $feedid = $row2->id;
                $timevalue = $this->feed->get_timevalue_seconds($feedid);
                $diff = $now - $timevalue['time'];
                if ($diff < (3600 * 24 * 2))
                    $active++;
                $total++;
            }


            $u = $this->user->get($row->userid);
            $userlist[] = array(
                "userid" => (int) $row->userid,
                "username" => $u->username,
                "role" => (int) $row->role,
                "activefeeds" => (int) $active,
                "feeds" => (int) $total
            );
        }
        return $userlist;
    }

    public function delete($userid, $groupid) {
        // Input sanitisation
        $userid = (int) $userid;
        $groupid = (int) $groupid;

        // Check that user is an admin of group
        if (!$this->is_group_admin($groupid, $userid))
            return array('success' => false, 'message' => _("User is not a member or does not have access to group"));

        $stmt = $this->mysqli->prepare("DELETE FROM groups WHERE id=?");
        $stmt->bind_param("i", $groupid);
        if (!$stmt->execute())
            return array('success' => false, 'message' => _("Query error, could not delete group"));

        $stmt = $this->mysqli->prepare("DELETE FROM group_users WHERE groupid=?");
        $stmt->bind_param("i", $groupid);
        if (!$stmt->execute())
            return array('success' => false, 'message' => _("Query error, could not delete users from group"));

        return array('success' => true, 'message' => _("Group deleted"));
    }

    public function remove_user($userid, $groupid, $userid_to_remove) {
        // Input sanitisation
        $userid = (int) $userid;
        $groupid = (int) $groupid;

        // A user cant remove their own account
        if ($userid == $userid_to_remove)
            return array('success' => false, 'message' => _("Cannot remove own user from group, please delete group"));

        // Check that user is an admin of group
        if (!$this->is_group_admin($groupid, $userid))
            return array('success' => false, 'message' => _("User is not a member or does not have access to group"));

        // Check that user to remove is a member of group then delete
        if ($this->is_group_member($groupid, $userid_to_remove)) {
            $stmt = $this->mysqli->prepare("DELETE FROM group_users WHERE groupid=? AND userid=?");
            $stmt->bind_param("ii", $groupid, $userid_to_remove);
            if (!$stmt->execute())
                return array('success' => false, 'message' => _("Query error"));
            return array('success' => true, 'message' => _("User removed from group"));
        }
    }

    // Basic check if group of id exists
    private function exists($groupid) {
        // Input sanitisation
        $groupid = (int) $groupid;

        $stmt = $this->mysqli->prepare("SELECT * FROM groups WHERE id = ?");
        $stmt->bind_param("i", $groupid);
        if (!$stmt->execute())
            return false;
        $stmt->store_result();
        if ($stmt->num_rows == 0)
            return false;
        return true;
    }

    // Used to enforce unique group names
    private function exists_name($group_name) {
        // Input sanitisation
        $group_name = preg_replace('/[^\w\s-:]/', '', $group_name);

        $stmt = $this->mysqli->prepare("SELECT * FROM groups WHERE name = ?");
        $stmt->bind_param("s", $group_name);
        if (!$stmt->execute())
            return false;
        $stmt->store_result();
        if ($stmt->num_rows == 0)
            return false;
        return true;
    }

    public function add_user($groupid, $userid, $role = 0, $admin_rights = 'full') {
        // Input sanitisation
        $userid = (int) $userid;
        $groupid = (int) $groupid;
        $role = (int) $role;
        $admin_rights = $admin_rights == 'full' ? 'full' : 'read';

        // Dont add user if already a member
        if ($this->is_group_member($groupid, $userid))
            return false;

        $stmt = $this->mysqli->prepare("INSERT INTO group_users (groupid,userid,role, admin_rights) VALUES (?,?,?,?)");
        $stmt->bind_param("iiis", $groupid, $userid, $role, $admin_rights);
        if (!$stmt->execute())
            return false;
        return true;
    }

    public function is_group_member($groupid, $userid) {
        // Input sanitisation
        $userid = (int) $userid;
        $groupid = (int) $groupid;

        $stmt = $this->mysqli->prepare("SELECT role FROM group_users WHERE groupid = ? AND userid = ?");
        // 1. Check if user is a member of group
        $stmt->bind_param("ii", $groupid, $userid);
        if (!$stmt->execute())
            return false;
        $stmt->store_result();
        if ($stmt->num_rows != 1)
            return false;
        return true;
    }

    public function is_group_admin($groupid, $userid) {
        // Input sanitisation
        $userid = (int) $userid;
        $groupid = (int) $groupid;

        $stmt = $this->mysqli->prepare("SELECT role FROM group_users WHERE groupid = ? AND userid = ?");
        // 1. Check if user is a member of group
        $stmt->bind_param("ii", $groupid, $userid);
        if (!$stmt->execute())
            return false;
        $stmt->store_result();
        if ($stmt->num_rows != 1)
            return false;
        // 2. If user is a member of group check if user is an administrator
        $stmt->bind_result($role);
        $stmt->fetch();
        if ($role != 1)
            return false;
        return true;
    }

    // --------------------------------------------------------------------
    // Aggregation
    // --------------------------------------------------------------------
    public function aggregate($userid, $groupid, $feedname) {
        // Input sanitisation
        $userid = (int) $userid;
        $groupid = (int) $groupid;
        $feedname = preg_replace('/[^\w\s-:]/', '', $feedname);

        // 1. Check that user is a group administrator
        if (!$this->is_group_admin($groupid, $userid))
            return array('success' => false, 'message' => _("User is not a member or does not have access to group"));

        $now = time();
        $total = 0;
        $count = 0;
        $result = $this->mysqli->query("SELECT userid,role FROM group_users WHERE groupid = $groupid");
        while ($row = $result->fetch_object()) {

            $userid = $row->userid;
            $feedids = $this->redis->sMembers("user:feeds:$userid");
            foreach ($feedids as $id) {
                $row = $this->redis->hGetAll("feed:$id");
                if ($row["name"] == $feedname) {
                    $lastvalue = $this->feed->get_timevalue($id);
                    if ((time() - strtotime($lastvalue['time'])) < 60) {
                        $total += ($lastvalue['value'] * 1);
                        $count ++;
                    }
                }
            }
        }
        return array("result" => $total, "count" => $count);
    }

}
