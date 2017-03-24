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

class Group
{
    private $mysqli;
    private $user;

    public function __construct($mysqli,$user)
    {
        $this->mysqli = $mysqli;
        $this->user = $user;
    }
    
    // Create group, add creator user as administrator
    public function create($userid,$name,$description) 
    {
        // Input sanitisation
        $userid = (int) $userid;
        $name = preg_replace('/[^\w\s-:]/','',$name);
        $description = preg_replace('/[^\w\s-:]/','',$description);
        
        if ($this->exists_name($name)) 
            return array('success'=>false, 'message'=>_("Group already exists"));
    
        $stmt = $this->mysqli->prepare("INSERT INTO groups (name,description) VALUES (?,?)");
        $stmt->bind_param("ss", $name, $description);
        if (!$stmt->execute()) {
            return array('success'=>false, 'message'=>_("Error creating group"));
        }
        $groupid = $this->mysqli->insert_id;
                
        if (!$this->add_user($groupid, $userid, 1)) 
            return array('success'=>false, 'message'=>_("Error adding user to group"));
        
        return array('success'=>true, 'groupid'=>$groupid, 'message'=>_("Group $groupid added"));
    }
    
    // Add user to a group if admin user knows account username and password
    public function add_user_auth($admin_userid,$groupid,$username,$password,$access) 
    {
        // Input sanitisation
        $admin_userid = (int) $admin_userid;
        $groupid = (int) $groupid;
        $access = (int) $access;
        // username and password checked within $user model
        
        if (!$this->exists($groupid)) 
            return array('success'=>false, 'message'=>_("Group does not exist"));
      
        // 1. Check that user is a group administrator
        if (!$this->is_group_admin($groupid,$admin_userid)) 
            return array('success'=>false, 'message'=>_("User is not a member or does not have access to group"));
        
        // 2. Check username and password, return
        $result = $this->user->get_apikeys_from_login($username, $password);
        if (!$result["success"]) return $result;
        $add_userid = $result["userid"];
        
        // 3. Add user to group 
        if (!$this->add_user($groupid, $add_userid, $access)) 
            return array('success'=>false, 'message'=>_("Error adding user to group"));
            
        return array('success'=>true, 'message'=>_("User $add_userid:$username added"));
    }
    
    // Send email invite to user to join a group
    public function add_user_invite($userid,$groupid,$invite_userid) 
    {
        // Input sanitisation
        $userid = (int) $userid;
        $groupid = (int) $groupid;
    }
    
    // Return list of groups for which $userid is an administrator
    public function grouplist($userid) 
    {
        // Input sanitisation
        $userid = (int) $userid;
        
        $stmt = $this->mysqli->prepare("SELECT groupid FROM group_users WHERE userid = ? AND access=1");
        $stmt->bind_param("i",$userid);
        if (!$stmt->execute()) return false;
        $stmt->bind_result($groupid);
        
        $groupids = array();
        while ($stmt->fetch()) $groupids[] = $groupid;
  
        $groups = array();
        foreach ($groupids as $groupid) {
            $result = $this->mysqli->query("SELECT * FROM groups WHERE id = $groupid");
            $row = $result->fetch_object();
            $groups[] = array(
                "groupid"=>(int)$row->id,
                "name"=>$row->name,
                "description"=>$row->description
            );  
        }
  
        return $groups;        
    }
    
    // Return list of groups for which $userid is an administrator
    public function userlist($userid,$groupid) 
    {
        // Input sanitisation
        $userid = (int) $userid;
        $groupid = (int) $groupid;

        // 1. Check that user is a group administrator
        if (!$this->is_group_admin($groupid,$userid)) 
            return array('success'=>false, 'message'=>_("User is not a member or does not have access to group"));
            
        $userlist = array();
        $result = $this->mysqli->query("SELECT userid,access FROM group_users WHERE groupid = $groupid");
        while($row = $result->fetch_object())
        {
            $u = $this->user->get($row->userid);
            $userlist[] = array(
                "userid"=>(int) $row->userid,
                "username"=>$u->username,
                "access"=>(int) $row->access
            );
        }
        return $userlist;
    }
    
    public function delete($userid,$groupid) 
    {
        // Input sanitisation
        $userid = (int) $userid;
        $groupid = (int) $groupid;
    }
    
    public function remove_user($userid,$groupid,$userid_to_remove) 
    {
        // Input sanitisation
        $userid = (int) $userid;
        $groupid = (int) $groupid;
    }

    // Basic check if group of id exists
    private function exists($groupid) 
    {
        // Input sanitisation
        $groupid = (int) $groupid;
        
        $stmt = $this->mysqli->prepare("SELECT * FROM groups WHERE id = ?");
        $stmt->bind_param("i", $groupid);
        if (!$stmt->execute()) return false;
        $stmt->store_result();
        if ($stmt->num_rows==0) return false;
        return true;
    }
    
    // Used to enforce unique group names
    private function exists_name($group_name) 
    {
        // Input sanitisation
        $group_name = preg_replace('/[^\w\s-:]/','',$group_name);
        
        $stmt = $this->mysqli->prepare("SELECT * FROM groups WHERE name = ?");
        $stmt->bind_param("s", $group_name);
        if (!$stmt->execute()) return false;
        $stmt->store_result();
        if ($stmt->num_rows==0) return false;
        return true;
    }
    
    private function add_user($groupid,$userid,$access) 
    {
        // Input sanitisation
        $userid = (int) $userid;
        $groupid = (int) $groupid;
        $access = (int) $access;
        
        // Dont add user if already a member
        if ($this->is_group_member($groupid,$userid)) return false;
        
        $stmt = $this->mysqli->prepare("INSERT INTO group_users (groupid,userid,access) VALUES (?,?,?)");
        $stmt->bind_param("iii", $groupid, $userid, $access);
        if (!$stmt->execute()) return false;
        return true;
    }
    
    private function is_group_member($groupid,$userid) 
    {
        // Input sanitisation
        $userid = (int) $userid;
        $groupid = (int) $groupid;
        
        $stmt = $this->mysqli->prepare("SELECT access FROM group_users WHERE groupid = ? AND userid = ?");
        // 1. Check if user is a member of group
        $stmt->bind_param("ii", $groupid, $userid);
        if (!$stmt->execute()) return false;
        $stmt->store_result();
        if ($stmt->num_rows==0) return false;
        return true;
    }
    
    private function is_group_admin($groupid,$userid) 
    {
        // Input sanitisation
        $userid = (int) $userid;
        $groupid = (int) $groupid;
        
        $stmt = $this->mysqli->prepare("SELECT access FROM group_users WHERE groupid = ? AND userid = ?");
        // 1. Check if user is a member of group
        $stmt->bind_param("ii", $groupid, $userid);
        if (!$stmt->execute()) return false;
        $stmt->store_result();
        if ($stmt->num_rows!=1) return false;
        // 2. If user is a member of group check if user is an administrator
        $stmt->bind_result($access);
        $stmt->fetch();
        if ($access!=1) return false;
        return true;
    }
}

