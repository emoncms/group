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
    private $dashboard;

    public function __construct($mysqli, $redis, $user, $feed, $input, $dashboard) {
        $this->log = new EmonLogger(__FILE__);
        $this->mysqli = $mysqli;
        $this->user = $user;
        $this->input = $input;
        $this->feed = $feed;
        $this->redis = $redis;
        $this->dashboard = $dashboard;
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


        if ($this->exists_name($name)) {
            $this->log->warn("Cannot create group,  already exists");
            return array('success' => false, 'message' => _("Cannot create group,  already exists"));
        }

        $stmt = $this->mysqli->prepare("INSERT INTO groups (name,description, organization, area, visibility, access) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param("ssssss", $name, $description, $organization, $area, $visibility, $access);
        if (!$stmt->execute()) {
            $this->log->error("Error creating group, problem with sql statement");
            return array('success' => false, 'message' => _("Error creating group"));
        }
        $groupid = $this->mysqli->insert_id;

        if (!$this->add_user($groupid, $userid, 1)) {
            $this->log->error("Error adding user to group " . $groupid);
            return array('success' => false, 'message' => _("Error adding user to group"));
        }

        $this->log->info("Group $groupid added");
        return array('success' => true, 'groupid' => $groupid, 'message' => _("Group $groupid added"));
    }

    // Edit group
    public function editgroup($admin_userid, $groupid, $name, $description, $organization, $area, $visibility, $access) {
        // Input sanitisation        
        $groupid = (int) $groupid;
        $admin_userid = (int) $admin_userid;
        $name = preg_replace('/[^\w\s-:]/', '', $name);
        $description = preg_replace('/[^\w\s-:]/', '', $description);
        $organization = preg_replace('/[^\w\s-:]/', '', $organization);
        $area = preg_replace('/[^\w\s-:]/', '', $area);
        $visibility = $visibility == 'public' ? 'public' : 'private';
        $access = $access == 'open' ? 'open' : 'closed';

        if ($this->is_group_admin($groupid, $admin_userid)) {
            $stmt = $this->mysqli->prepare("UPDATE groups SET name=?, description=?, organization=?, area=?, visibility=?, access=? WHERE id=?");
            $stmt->bind_param("ssssssi", $name, $description, $organization, $area, $visibility, $access, $groupid);
            if (!$stmt->execute()) {
                $this->log->error("Error editing group, problem with sql statement");
                return array('success' => false, 'message' => _("Error editing group"));
            }
            $this->log->info("Group edited");
            return array('success' => true, 'message' => _("Group edited"));
        }
        else {
            $this->log->warning("Error editing group, You are not administrator of the group - Session userid: " . $admin_userid);
            return array('success' => false, 'message' => _("You are not administrator of the group"));
        }
    }

    public function createuseraddtogroup($admin_userid, $groupid, $email, $username, $password, $role) {
        // Input sanitisation
        $admin_userid = (int) $admin_userid;
        $groupid = (int) $groupid;
        $role = (int) $role;
        // email, username and password checked within $user model

        if (!$this->exists($groupid)) {
            $this->log->warn("Group " . $groupid . " does not exist");
            return array('success' => false, 'message' => _("Group does not exist"));
        }

        // 1. Check that user is a group administrator
        if (!$this->is_group_admin($groupid, $admin_userid)) {
            $this->log->warn("You haven't got enough permissions to add a member to this group - Session userid: " . $admin_userid);
            return array('success' => false, 'message' => _("You haven't got enough permissions to add a member to this group"));
        }

        // 2. Check username and password, return
        $result = $this->user->register($username, $password, $email);
        if (!$result["success"]) {
            $this->log->error("Error creating user - " . var_export($result, true));
            return $result;
        }
        $add_userid = $result["userid"];

        // 3. Add user to group 
        if (!$this->add_user($groupid, $add_userid, $role, $admin_rights = 'full')) {
            $this->log->error("Error adding user to group");
            return array('success' => false, 'message' => _("Error adding user to group"));
        }

        $this->log->info("User $add_userid:$username added to group " . $groupid);
        return array('success' => true, 'message' => _("User $add_userid:$username added"));
    }

    // Add user to a group if admin user knows account username and password
    public function add_user_auth($admin_userid, $groupid, $username, $password, $role) {
        // Input sanitisation
        $admin_userid = (int) $admin_userid;
        $groupid = (int) $groupid;
        $role = (int) $role;
        // username and password checked within $user model

        if (!$this->exists($groupid)) {
            $this->log->error("Error adding user to group, group does not exist");
            return array('success' => false, 'message' => _("Group does not exist"));
        }

        // 1. Check that user is a group administrator
        if (!$this->is_group_admin($groupid, $admin_userid)) {
            $this->log->warn("You haven't got enough permissions to add a member to this group - Session userid: " . $admin_userid);
            return array('success' => false, 'message' => _("You haven't got enough permissions to add a member to this group"));
        }

        // 2. Check username and password, return
        $result = $this->user->get_apikeys_from_login($username, $password);
        if (!$result["success"]) {
            $this->log->error("Error adding user to group, username and password don't match - Session userid: " . $admin_userid);
            return $result;
        }
        $add_userid = $result["userid"];

        // 3. Add user to group 
        if (!$this->add_user($groupid, $add_userid, $role)) {
            $this->log->error("Error adding user to group");
            return array('success' => false, 'message' => _("Error adding user to group"));
        }

        $this->log->info("User $add_userid:$username added to group " . $groupid);
        return array('success' => true, 'message' => _("User $add_userid:$username added"));
    }

    // Send email invite to user to join a group
    public function add_user_invite($userid, $groupid, $invite_userid) {
        // Input sanitisation
        $userid = (int) $userid;
        $groupid = (int) $groupid;
    }

    // Return list of groups which $userid belongs and has right to list 
    public function grouplist($userid) {
        // Input sanitisation
        $userid = (int) $userid;

        $stmt = $this->mysqli->prepare("SELECT groupid, role FROM group_users WHERE userid = ?");
        $stmt->bind_param("i", $userid);
        if (!$stmt->execute())
            return false;
        $stmt->bind_result($groupid, $userrole);

        $groupids = array();
        $roleingroup = array();
        while ($stmt->fetch()) {
            if ($userrole != 0) { // if user is not a passive member
                $groupids[] = $groupid;
                $roleingroup[] = $userrole;
            }
        }

        $groups = array();
        for ($i = 0; $i < sizeof($groupids); $i++) {
            $result = $this->mysqli->query("SELECT * FROM groups WHERE id = $groupids[$i]");
            $row = $result->fetch_object();
            $groups[] = array(
                "groupid" => (int) $row->id,
                "name" => $row->name,
                "description" => $row->description,
                "role" => (int) $roleingroup[$i]
            );
        }

        return $groups;
    }

    // Return list of users for which $userid is an administrator or subadministrator
    public function userlist($session_userid, $groupid) {
        // Input sanitisation
        $session_userid = (int) $session_userid;
        $groupid = (int) $groupid;

        // 1. Check that user is a group administrator/subadministrator
        $role = (int) $this->getrole($session_userid, $groupid);
        if ($role != 1 && $role != 2) {
            $this->log->warn("You have not got access to the list of users of this group - Session userid " . $session_userid);
            return array('success' => false, 'message' => _("You have not got access to the list of users of this group"));
        }

        $userlist = array();
        $result = $this->mysqli->query("SELECT userid,role,admin_rights FROM group_users WHERE groupid = $groupid");
        while ($row = $result->fetch_object()) {
            // Get feeds and calculate number of active feeds
            $userfeeds = $this->getuserfeeds($session_userid, $groupid, $row->userid);
            $active = 0;
            $total = 0;
            $now = time();
            foreach ($userfeeds as $feed) {
                $diff = $now - $feed['time'];
                if ($diff < (3600 * 24 * 2))
                    $active++;
                $total++;
            }

            // Get inputs
            $userinputs = $this->getuserinputs($session_userid, $groupid, $row->userid);

            $u = $this->user->get($row->userid);
            $userlist[] = array(
                "userid" => (int) $row->userid,
                "username" => $u->username,
                "role" => (int) $row->role,
                "activefeeds" => (int) $active,
                "totalfeeds" => (int) $total,
                "admin_rights" => $row->admin_rights,
                "feedslist" => $userfeeds,
                "inputslist" => $userinputs
            );
        }
        return $userlist;
    }

    // Return list of user's groups with all the members and their feeds
    public function mygroups($session_userid) {
        // Input sanitisation
        $session_userid = (int) $session_userid;

        $groups = $this->grouplist($session_userid);
        foreach ($groups as &$group) {
            $group['users'] = $this->userlist($session_userid, $group['groupid']); // only groups where the session_user is admin or subadmin will return the list of users
        }
        $sdf = 1;
        return $groups;
    }

    // Return list of feeds of the given user
    public function getuserfeeds($session_userid, $groupid, $userid) {
        // Input sanitisation
        $session_userid = (int) $session_userid;
        $userid = (int) $userid;
        $groupid = (int) $groupid;

        // 1. Check that user is a group administrator or subadministrator
        $role = (int) $this->getrole($session_userid, $groupid);
        if ($role != 1 && $role != 2) {
            $this->log->warn('You have not got access to user\'s feeds of group ' . $groupid . ' - Session userid ' . $session_userid);
            return array('success' => false, 'message' => _("You have not got access to user's feeds of this group"));
        }

        // 2. Check that user is member of the group
        if (!$this->is_group_member($groupid, $userid))
            return array('success' => false, 'message' => _("The user is not member of the group"));

        // 3. Get list of feeds
        return $this->feed->get_user_feeds($userid);
    }

    // Searches for a feedid in all the groups the user has access to. If user has the right to access the feed, it returns the feed
    public function getfeed($session_userid, $subaction, $feedid, $start, $end, $interval, $skipmissing, $limitinterval) {
        // Input sanitisation
        $session_userid = (int) $session_userid;
        $feedid = (int) $feedid;
        $subaction = preg_replace('/[^\w\s-:]/', '', $subaction);
        // other inputs checked in folloing feed methods
        // Load all the groups the user has access (including users and feeds
        $groups = $this->mygroups($session_userid);

        // Search for the feed
        $feed_found = false;
        foreach ($groups as $group) {
            foreach ($group['users'] as $user) {
                foreach ($user['feedslist'] as $feed)
                    if ($feedid == $feed['id'])
                        $feed_found = true;
            }
        }

        if ($feed_found == false) {
            $this->log->warn('You have not got access to that feed ' . $feedid . ' - Session userid ' . $session_userid);
            return array('success' => false, 'message' => _("You have not access to that feed"));
        }
        elseif ($subaction == 'data') {
            $data = $this->feed->get_data($feedid, $start, $end, $interval, $skipmissing, $limitinterval);
        }
        elseif ($subaction == 'average') {
            $data = $this->feed->get_average($feedid, $start, $end, $outinterval);
        }
        return $data;
    }

    // Return list of feeds of the given user
    public function getuserinputs($session_userid, $groupid, $userid) {
        // Input sanitisation
        $session_userid = (int) $session_userid;
        $userid = (int) $userid;
        $groupid = (int) $groupid;

        // 1. Check that user is a group administrator or subadministrator
        $role = (int) $this->getrole($session_userid, $groupid);
        if ($role != 1 && $role != 2) {
            $this->log->warn('You have not got access to user\'s feeds of group ' . $groupid . ' - Session userid ' . $session_userid);
            return array('success' => false, 'message' => _("You have not got access to user's feeds of this group"));
        }

        // 2. Check that user is member of the group
        if (!$this->is_group_member($groupid, $userid))
            return array('success' => false, 'message' => _("The user is not member of the group"));

        // 3. Get list of feeds
        return $this->input->get_inputs($userid);
    }

    public function delete($userid, $groupid) {
        // Input sanitisation
        $userid = (int) $userid;
        $groupid = (int) $groupid;

        // Check that user is an admin of group
        if (!$this->is_group_admin($groupid, $userid)) {
            $this->log->error('Cannot delete group, sesion user is not admin - Session userid ' . $userid);
            return array('success' => false, 'message' => _("User is not a member or does not have access to group"));
        }

        $stmt = $this->mysqli->prepare("DELETE FROM groups WHERE id=?");
        $stmt->bind_param("i", $groupid);
        if (!$stmt->execute()) {
            $this->log->error('Query error, could not delete group ' . $groupid);
            return array('success' => false, 'message' => _("Query error, could not delete group"));
        }

        $stmt = $this->mysqli->prepare("DELETE FROM group_users WHERE groupid=?");
        $stmt->bind_param("i", $groupid);
        if (!$stmt->execute()) {
            $this->log->error('Query error, could not delete group ' . $groupid);
            return array('success' => false, 'message' => _("Query error, could not delete users from group"));
        }

        $this->log->error('Group ' . $groupid . ' deleted by session user ' . $userid);
        return array('success' => true, 'message' => _("Group deleted"));
    }

    // Remove member from group
    public function remove_user($userid, $groupid, $userid_to_remove) {
        // Input sanitisation
        $userid = (int) $userid;
        $groupid = (int) $groupid;

        // A user cant remove their own account
        if ($userid == $userid_to_remove)
            return array('success' => false, 'message' => _("Cannot remove yourself from group"));

        // Check that user is an admin of group
        if (!$this->is_group_admin($groupid, $userid)) {
            $this->log->error('Cannot delete user, sesion user is not admin - Session userid ' . $userid);
            return array('success' => false, 'message' => _("User is not a member or does not have access to group"));
        }

        // Check user belongs to group
        if (!$this->is_group_member($groupid, $userid_to_remove)) {
            $this->log->error('Cannot delete user, user to remove doesn\'t belong to the group');
            return array('success' => false, 'message' => _("The user to remove doesn't belong to the group"));
        }

        // Check that user to remove is a member of group then delete
        $stmt = $this->mysqli->prepare("DELETE FROM group_users WHERE groupid=? AND userid=?");
        $stmt->bind_param("ii", $groupid, $userid_to_remove);
        if (!$stmt->execute())
            return array('success' => false, 'message' => _("Query error"));
        return array('success' => true, 'message' => _("User removed from group"));
    }

    // Remove user from database and group
    public function full_remove_user($session_userid, $groupid, $userid_to_remove) {
        // Input sanitisation
        $session_userid = (int) $session_userid;
        $groupid = (int) $groupid;
        $userid_to_remove = (int) $userid_to_remove;

        // A user cant remove their own account
        if ($session_userid == $userid_to_remove)
            return array('success' => false, 'message' => _("Cannot delete yourself"));

        // Check that user is an admin of group
        if (!$this->is_group_admin($groupid, $session_userid)) {
            $this->log->error('Cannot delete group, sesion user is not admin - Session userid ' . $userid);
            return array('success' => false, 'message' => _("User is not a member or does not have access to group"));
        }

        // Check user belongs to group
        if (!$this->is_group_member($groupid, $userid_to_remove)) {
            $this->log->error('Cannot delete user, user to remove doesn\'t belong to the group');
            return array('success' => false, 'message' => _("The user to remove doesn't belong to the group"));
        }

        // Check session admin has full rights over user data
        $asd = $this->administrator_rights_over_user($groupid, $userid_to_remove);
        if ($this->administrator_rights_over_user($groupid, $userid_to_remove) !== true) {
            $this->log->warn('Cannot delete user data, administrator has not got right over users - Session userid ' . $session_userid);
            return array('success' => false, 'message' => _("Administrator has not got right over users"));
        }

        // Delete inputs
        $list_of_inputs = $this->input->getlist($userid_to_remove);
        foreach ($list_of_inputs as $input) {
            $this->input->delete($userid_to_remove, $input['id']);
        }

        // Delete feeds        
        $list_of_feeds = $this->feed->get_user_feeds($userid_to_remove);
        foreach ($list_of_feeds as $feed) {
            $this->feed->delete($feed['id']);
        }

        // Delete dashboards
        if (is_null($this->dashboard) == false) {
            $list_of_dashboards = $this->dashboard->get_list($userid_to_remove, false, false);
            foreach ($list_of_dashboards as $dashboard) {
                $this->dashboard->delete($dashboard['id']);
            }
        }

        // Remove from group
        $result = $this->remove_user($session_userid, $groupid, $userid_to_remove);
        if ($result['success'] == true) {
            $this->log->info('User ' . $userid_to_remove . ' completely removed - Session userid ' . $session_userid);
            return array('success' => true, 'message' => _("User completely removed"));
        }
        else
            return array('success' => false, 'message' => _("User could not be deleted"));
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
        if (!$stmt->execute()) {
            $this->log->error('Cannot add user to group, error in query');
            return false;
        }
        $this->log->info('User ' . $userid . ' added to group ' . '$groupid');
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

    // Returns true if the administrato of a group has full access to user account
    public function administrator_rights_over_user($groupid, $userid) {
        // Input sanitisation
        $userid = (int) $userid;
        $groupid = (int) $groupid;

        $stmt = $this->mysqli->prepare("SELECT admin_rights FROM group_users WHERE groupid = ? AND userid = ?");
        // 1. Check if user is a member of group
        $stmt->bind_param("ii", $groupid, $userid);
        if (!$stmt->execute())
            return false;
        $stmt->store_result();
        if ($stmt->num_rows != 1)
            return false;
        // 2. If user is a member of group check if user is an administrator
        $stmt->bind_result($admin_rights);
        $stmt->fetch();
        if ($admin_rights != 'full')
            return false;
        return true;
    }

    public function getrole($userid, $groupid) {
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
        // 2. Return role
        $stmt->bind_result($role);
        $stmt->fetch();
        return $role;
    }

    public function csvexport($session_userid, $groupid, $feedid, $start, $end, $interval, $timezone, $name) {
        // Input sanitisation
        $session_userid = (int) $session_userid;
        $groupid = (int) $groupid;
        $feedid = (int) $feedid;
        $start = (int) $start;
        $end = (int) $end;
        $interval = (int) $interval;
        $timezone = (int) $timezone;
        $name = preg_replace('/[^\w\s-:]/', '', $name);

        $feed = $this->feed->get($feedid);
        $myrole = $this->getrole($session_userid, $groupid);
        if ($myrole != 1 && $myrole != 2)
            return array('success' => false, 'message' => _("You haven't got enough rights"));
        return $this->feed->csv_export($feedid, $start, $end, $interval, $timezone, $name);
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

    // Development    
    public function getapikeys($sessions_user, $groupid) {
        // Input sanitisation
        $groupid = (int) $groupid;
        $sessions_user = (int) $sessions_user;

        $userlist = $this->userlist($sessions_user, $groupid);

        $asd = 0;
        foreach ($userlist as $user) {
            if ($this->is_group_admin($groupid, $sessions_user) && $this->administrator_rights_over_user($groupid, $user['userid']))
                $list[] = $this->user->get_apikey_write($user['userid']);
        }
        return $list;
    }

}
