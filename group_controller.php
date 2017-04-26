<?php

// no direct access
defined('EMONCMS_EXEC') or die('Restricted access');

function group_controller()
{
    global $session,$route,$mysqli,$user;

    $result = false;
    
    include "Modules/group/group_model.php";
    $group = new Group($mysqli,$user);

    // ------------------------------------------------------------------------------------
    // API
    // ------------------------------------------------------------------------------------
    if ($session['write']) {
    
        if ($route->action == "") {
            $route->format = "html";
            $result = view("Modules/group/group_view.php",array());
        }
        
        // group/create?name=test&description=test
        if ($route->action == "create") {
            $route->format = "json";
            $result = $group->create($session["userid"],get("name"),get("description"));
        }

        // group/adduserauth?groupid=1&username=USERNAME&password=PASSWORD&access=1
        // access 1: admin
        if ($route->action == "adduserauth") {
            $route->format = "json";
            $result = $group->add_user_auth($session["userid"],get("groupid"),get("username"),get("password"),get("access"));
        }
        
        // group/removeuser?groupid=1&userid=1
        if ($route->action == "removeuser") {
            $route->format = "json";
            $result = $group->remove_user($session["userid"],get("groupid"),get("userid"));
        }
        
        // group/delete?groupid=1
        if ($route->action == "delete") {
            $route->format = "json";
            $result = $group->delete($session["userid"],get("groupid"));
        }
        
        // --------------------------------------------------------------------------
        // SPECIAL USER SWITCHING FUNCTION
        // --------------------------------------------------------------------------
        if ($route->action == 'setuser' && $session['write'])
        {
            $route->format = "text";
            $groupid = (int) get('groupid');
            $userid = (int) get('userid');
            
            // 1. Check that session user is an administrator of the group requested
            if ($group->is_group_admin($groupid,$session["userid"])===true) { 
                // 2. Check that user requested is a member of group requested
                if ($group->is_group_member($groupid,$userid)===true) {
                    $_SESSION['userid'] = intval(get('userid'));
                    header("Location: ../user/view");
                } else {
                    $result = "ERROR: User is not a member of group";
                }
            } else {
                $result = "ERROR: You are not an administrator of this group";
            }
            
        }
    }

    if ($session['read']) {
        // group/grouplist
        // access 1: admin
        if ($route->action == "grouplist") {
            $route->format = "json";
            $result = $group->grouplist($session["userid"]);
        }
        
        // group/userlist?groupid=1
        // access 1: admin
        if ($route->action == "userlist") {
            $route->format = "json";
            $result = $group->userlist($session["userid"],get("groupid"));
        }
    }

    return array('content'=>$result, 'fullwidth'=>false);
}

