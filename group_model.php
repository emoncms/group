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
    private $input;
    private $task;

    public function __construct($mysqli, $redis, $user, $feed, $input, $task = null) {
        $this->log = new EmonLogger(__FILE__);
        $this->mysqli = $mysqli;
        $this->user = $user;
        $this->input = $input;
        $this->feed = $feed;
        $this->redis = $redis;
        $this->task = $task;
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
            $userfeeds = $this->feed->get_user_feeds($row->userid);
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
            $userinputs = $this->input->get_inputs($row->userid);

// Get tasks
            $user_tasks = array();
            if (is_null($this->task) === false)
                $user_tasks = $this->task->get_tasks($row->userid);

// Get user info
            $u = $this->user->get($row->userid);

// Add everything to the array
            $userlist[] = array(
                "userid" => (int) $row->userid,
                "username" => $u->username,
                // from group
                "role" => (int) $row->role,
                "admin_rights" => $row->admin_rights,
                // feeds an inputs
                "activefeeds" => (int) $active,
                "totalfeeds" => (int) $total,
                "feedslist" => $userfeeds,
                "inputslist" => $userinputs,
                // tasks
                "taskslist" => $user_tasks,
                // user info
                "name" => $u->name,
                "bio" => $u->bio,
                "location" => $u->location,
                "timezone" => $u->timezone,
                "email" => $u->email,
                "tags" => json_decode($u->tags)
            );
        }
        return $userlist;
    }

// Return list of user's groups with all the members and their data
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

// Searches for a feedid in all the groups the user has access to. If feed is public or user has the right to accessit, it returns the feed
    public function getfeed($session_userid, $subaction, $feedid, $start, $end, $interval, $skipmissing, $limitinterval) {
// Input sanitisation
        $session_userid = (int) $session_userid;
        $feedid = (int) $feedid;
        $subaction = preg_replace('/[^\w\s-:]/', '', $subaction);
// other inputs checked in folloing feed methods
// Load all the groups the user has access (including users and feeds
        $groups = $this->mygroups($session_userid);

// Check if feed is public
        $feed_is_public = false;
        $feed_found = false;
        $f = $this->feed->get($feedid);
        if ($f['public'])
            $feed_is_public = true;
        else {
// Search for the feed in user's groups
            foreach ($groups as $group) {
                foreach ($group['users'] as $user) {
                    foreach ($user['feedslist'] as $feed)
                        if ($feedid == $feed['id'])
                            $feed_found = true;
                }
            }
        }

        if ($feed_is_public == false && $feed_found == false) {
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

// Searches for a feedid in all the groups the user has access to. If user has the right to access the feed, returns true
    private function access_to_user_feed($session_userid, $feedid) {
// Input sanitisation
        $session_userid = (int) $session_userid;
        $feedid = (int) $feedid;

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

        return $feed_found;
    }

// Return list of inputs of the given user
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
        $result = $mysqli->query("SHOW TABLES LIKE 'dashboard'");
        if ($result->num_rows > 0) {
            $result = $this->mysqli->query("DELETE FROM dashboard WHERE `userid` = '$userid_to_remove'");
        }

// Delete graphs
        $result = $mysqli->query("SHOW TABLES LIKE 'graph'");
        if ($result->num_rows > 0) {
            $result = $this->mysqli->query("DELETE FROM graph WHERE `userid` = '$userid_to_remove'");
        }

// Remove from group
        $result = $this->remove_user($session_userid, $groupid, $userid_to_remove);
        if ($result['success'] == false)
            return array('success' => false, 'message' => _("User could not be deleted"));

// Remove from users table
        $result = $this->mysqli->query("DELETE FROM users WHERE `id` = '$userid_to_remove'");
        $this->log->info('User ' . $userid_to_remove . ' completely removed - Session userid ' . $session_userid);
        return array('success' => true, 'message' => _("User completely removed"));
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

    public function setuserinfo($session_userid, $groupid, $to_set_userid, $username, $name, $email, $bio, $timezone, $location, $role, $password, $tags) {
// Input sanitisation
        $session_userid = (int) $session_userid;
        $groupid = (int) $groupid;
        $to_set_userid = (int) $to_set_userid;
        $role = (int) $role;
// everything else checked within $user model

        if (!$this->is_group_admin($groupid, $session_userid)) {
            $this->log->error('Cannot edit user user, sesion user is not admin - Session userid ' . $userid);
            return array('success' => false, 'message' => _("User is not a member or does not have access to group"));
        }

// Check user belongs to group
        if (!$this->is_group_member($groupid, $to_set_userid)) {
            $this->log->error('Cannot edit user, user to remove doesn\'t belong to the group');
            return array('success' => false, 'message' => _("The user to edit doesn't belong to the group"));
        }

// Check session admin has full rights over user data
        if ($this->administrator_rights_over_user($groupid, $to_set_userid) !== true) {
            $this->log->warn('Cannot edit user data, administrator has not got right over users - Session userid ' . $session_userid);
            return array('success' => false, 'message' => _("Administrator has not got right over user"));
        }

// Get user info in order to have the other fields that we are not updating
        $user_data = $this->user->get($to_set_userid);

// Set basic user's info
        $user_data->name = $name;
        $user_data->location = $location;
        $user_data->bio = $bio;
        $user_data->timezone = $timezone;
        $user_data->tags = $tags;
        $result = $this->user->set($to_set_userid, $user_data);
        if (!$result['success'] == false) {
            $this->log->error('User info' . $to_set_userid . ' not updated ' . '$groupid');
            return $result;
        }

// Username
        if ($user_data->username != $username) {
            $result = $this->user->change_username($to_set_userid, $username);
            if ($result['success'] == false) {
                $this->log->error('User username ' . $to_set_userid . ' not updated ' . '$groupid');
                $result['message'] = 'User info updated except username, email, role and/or password - ' . $result['message'];
                return ($result);
            }
        }

// email
        if ($user_data->email != $email) {
            $result = $this->user->change_email($to_set_userid, $email);
            if ($result['success'] == false) {
                $this->log->error('User mail ' . $to_set_userid . ' not updated ' . '$groupid');
                $result['message'] = 'User info updated except email, role and/or password - ' . $result['message'];
                return ($result);
            }
        }

// Password
        if ($password != '') {
            $result = $this->user->set_password($to_set_userid, $password);
            if ($result['success'] == false) {
                $this->log->error('Password ' . $to_set_userid . ' not updated ' . '$groupid');
                $result['message'] = 'User info updated except role and/or password - ' . $result['message'];
                return ($result);
            }
        }

// role
        $query_result = $this->mysqli->query("UPDATE group_users SET role=$role WHERE userid=$to_set_userid AND groupid=$groupid");
        if ($query_result != true) {
            $result['success'] = false;
            $this->log->error('Role ' . $to_set_userid . ' not updated ' . '$groupid');
            $result['message'] = 'User info updated except role';
            return ($result);
        }

        $result['success'] = true;
        return $result;
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

    public function get_user_role($session_userid, $userid, $groupid) {
        $session_userid = (int) $session_userid;
        $userid = (int) $userid;
        $groupid = (int) $groupid;

// Check session user is group admin/subadmin
        $session_user_role = $this->getrole($session_userid, $groupid);
        if ($session_user_role == 1 || $session_user_role == 2)
            return $this->getrole($userid, $groupid);
    }

    private function getrole($userid, $groupid) {
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
        $feedid = preg_replace('/[^\d,]/', '', $feedid);
        $start = (int) $start;
        $end = (int) $end;
        $interval = (int) $interval;
        $timezone = (int) $timezone;
        $name = preg_replace('/[^\w\s-:]/', '', $name);

        $myrole = $this->getrole($session_userid, $groupid);
        if ($myrole != 1 && $myrole != 2)
            return array('success' => false, 'message' => _("You haven't got enough rights"));
        if (!$this->access_to_user_feed($session_userid, $feedid))
            return array('success' => false, 'message' => _("You haven't got enough rights"));

        if (strpos($feedid, ',') === false) // There is only one feedid
            $download = $this->feed->csv_export($feedid, $start, $end, $interval, $timezone, $name);
        else
            $download = $this->feed->csv_export_multi($feedid, $start, $end, $interval, $timezone, $name);
        $dfgd = 1;

        return $download;
    }

    public function set_multifeed_processlist($session_userid, $groupid, $feedids, $processlist, $name, $description, $tag, $frequency, $run_on) {
        if (is_null($this->task) != true) {
            $session_userid = (int) $session_userid;
            $feedids = json_decode($feedids);
            $groupid = (int) $groupid;
            $processlist = preg_replace('/([^A-Za-z0-9:,_])/', '', $processlist);

            $name = preg_replace('/[^\p{N}\p{L}_\s-:]/u', '', $name);
            $description = preg_replace('/[^\p{N}\p{L}_\s-:]/u', '', $description);
            $tag = preg_replace('/[^\p{N}\p{L}_\s-:]/u', '', $tag);
            $run_on = (preg_replace('/([^0-9])/', '', $run_on));
            $frequency = preg_replace("/[^\p{L}_\p{N}\s-.],'/u", '', $frequency);
            $enabled = 1;

            $errors = '';

            if ($this->is_group_admin($groupid, $session_userid)) {
// Fetch all users in group and their feeds
                $group_users = $this->get_group_users($groupid);
                foreach ($group_users as &$userforfeeds) {
                    $userforfeeds['feeds'] = $this->feed->get_user_feeds($userforfeeds['userid']);
                }
// Search for the feeds and if they are found create the task, this way we ensure the administrator has access to the feed
                foreach ($feedids as $feedid) {
                    $found = false;
                    foreach ($group_users as $user) {
                        foreach ($user['feeds'] as $feed) {
                            if ($feed['id'] == $feedid) {
                                $found = true;
                                if ($user['admin_rights'] != 'full') {
                                    $this->log->warn("User $session_userid is trying to create a task for user " . $user['userid'] . " but hasn't got full rights");
                                    $errors .= 'Not enough rights over ' . $user['username'];
                                }
                                else {
                                    $taskid = $this->task->create_task($user['userid'], $feed['name'] . ': ' . $name, $description, $tag, $frequency, $run_on, $enabled);
                                    if (is_numeric($taskid))  // if is not a number there was an error creating the task
                                        $result = $this->task->set_processlist($user['userid'], $taskid, "task__get_feed_id:" . $feedid . "," . $processlist); // We add the Source Feed process to the process list
                                    else {
                                        $this->log->info("Problem creating task " . $taskid['message']);
                                        $errors .= $user['username'] . ": " . $feed['name'] . ' - ' . $taskid['message'] . '\n';
                                    }
                                }
                            }
                        }
                    }
                    if ($found === false)
                        $this->log->warn("User $session_userid is trying to create a task with source feed $feedid but it doesnt belong to user of the group $groupid");
                }
            }
            else
                return array("result" => false, 'message' => 'You are not administrator of this group');
            if ($errors == '')
                return array('success' => true, 'message' => 'Task processlists created');
            else
                return array("result" => false, 'message' => $errors);
        }
    }

    public function delete_task($session_userid, $taskid, $userid, $groupid) {
        if (is_null($this->task) != true) {
            $session_userid = (int) $session_userid;
            $taskid = (int) $taskid;
            $groupid = (int) $groupid;
            $userid = (int) $userid;

            if (!$this->is_group_admin($groupid, $session_userid))
                return array("success" => false, 'message' => 'You are not administrator of this group');
            elseif (!$this->is_group_member($groupid, $userid))
                return array("success" => false, 'message' => 'User is not member of the group');
            elseif ($this->administrator_rights_over_user($groupid, $userid) != 'full')
                return array("success" => false, 'message' => 'Administrator hasn\'t got enough enough rights over user\'s data');
            elseif (!$this->task->task_belongs_to_user($taskid, $userid))
                return array("success" => false, 'message' => 'Task doesn\'t belong to user');
            else {
                $result = $this->task->delete_task($userid, $taskid);
                if ($result === true)
                    return array("success" => true, 'message' => 'Task deleted');
                else
                    return array("success" => false, 'message' => 'Task couldn\'t be deleted');
            }
        }
    }

    private function get_group_users($groupid) {
        $result = $this->mysqli->query("SELECT userid, role, admin_rights FROM group_users WHERE groupid = $groupid");
        while ($row = $result->fetch_object()) {
            $username = $this->user->get_username((int) $row->userid);
            $users[] = array(
                "userid" => (int) $row->userid,
                "role" => (int) $row->role,
                "admin_rights" => $row->admin_rights,
                "username" => $username
            );
        }
        return $users;
    }

    public function set_processlist($session_userid, $taskid, $userid, $groupid, $processlist) {
        if (is_null($this->task) != true) {
            $session_userid = (int) $session_userid;
            $taskid = (int) $taskid;
            $userid = (int) $userid;
            $processlist = preg_replace('/([^A-Za-z0-9:, _])/', '', $processlist);

            if (!$this->is_group_admin($groupid, $session_userid))
                $result = array("result" => false, 'message' => 'You are not administrator of this group');
            elseif (!$this->administrator_rights_over_user($groupid, $userid))
                $result = array("result" => false, 'message' => 'You haven\'t got enough right over user data');
            elseif (!$this->is_group_member($groupid, $userid))
                $result = array("result" => false, 'message' => 'User is not member of this group');
            else
                $result = $this->task->set_processlist($userid, $taskid, $processlist);
        }
        return $result;
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
