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

