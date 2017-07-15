<?php

// no direct access
defined('EMONCMS_EXEC') or die('Restricted access');

function group_controller() {
    global $session, $route, $mysqli, $redis, $user, $feed_settings;

    $result = false;

    include "Modules/feed/feed_model.php";
    $feed = new Feed($mysqli, $redis, $feed_settings);

    include "Modules/group/group_model.php";
    $group = new Group($mysqli, $redis, $user, $feed);

    // ------------------------------------------------------------------------------------
    // API
    // ------------------------------------------------------------------------------------
    if ($session['write']) {

        if ($route->action == "") {
            $route->format = "html";
            $result = view("Modules/group/group_view.php", array());
        }

        // group/create?name=test&description=test
        if ($route->action == "create") {
            $route->format = "json";
            $result = $group->create($session["userid"], get("name"), get("description"), get("organization"), get("area"), get("visibility"), get("access"));
        }
        
        // group/editgroup?name=test&description=test
        if ($route->action == "editgroup") {
            $route->format = "json";
            $result = $group->editgroup($session["userid"], get('groupid'), get("name"), get("description"), get("organization"), get("area"), get("visibility"), get("access"));
        }

        // group/addmemberauth?groupid=1&username=USERNAME&password=PASSWORD&role=1
        if ($route->action == "addmemberauth") {
            $route->format = "json";
            $result = $group->add_user_auth($session["userid"], get("groupid"), get("username"), get("password"), get("role"));
        }

        // group/createuseraddtogroup?groupid=1&email=EMAIL&username=USERNAME&password=PASSWORD&role=1
        if ($route->action == "createuseraddtogroup") {
            $route->format = "json";
            $result = $group->createuseraddtogroup($session["userid"], get("groupid"), get('email'), get("username"), get("password"), get("role"));
        }

        // group/removeuser?groupid=1&userid=1
        if ($route->action == "removeuser") {
            $route->format = "json";
            $result = $group->remove_user($session["userid"], get("groupid"), get("userid"));
        }

        // group/delete?groupid=1
        if ($route->action == "delete") {
            $route->format = "json";
            $result = $group->delete($session["userid"], get("groupid"));
        }

        // --------------------------------------------------------------------------
        // SPECIAL USER SWITCHING FUNCTIONS
        // --------------------------------------------------------------------------
        if ($route->action == 'setuser') {
            $route->format = "text";
            $groupid = (int) get('groupid');
            $userid = (int) get('userid');

            // 1. Check that session user is an administrator of the group requested
            if ($group->is_group_admin($groupid, $session["userid"]) === true) {
                // 2. Check that user requested is a member of group requested
                if ($group->is_group_member($groupid, $userid) === true) {
                    // 3. Check that session user has full rights over user requested
                    if ($group->administrator_rights_over_user($groupid, $userid) === true) {
                        $_SESSION['previous_userid'] = $session['userid'];
                        $_SESSION['previous_username'] = $session['username'];
                        $_SESSION['userid'] = $userid;
                        header("Location: ../user/view");
                    } else
                        $result = "ERROR: You haven't got rights to access this user";
                } else {
                    $result = "ERROR: User is not a member of group";
                }
            } else {
                $result = "ERROR: You are not an administrator of this group";
            }
        }

        if ($route->action == 'logasprevioususer') {
            $route->format = "text";
            
            $_SESSION['userid'] = $_SESSION['previous_userid'];
            unset($_SESSION['previous_userid']);
            unset($_SESSION['previous_username']);
            header("Location: ../group");
        }
    }

    //---------------------------
    // SESSION READ
//---------------------------
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
            $result = $group->userlist($session["userid"], get("groupid"));
        }

        // group/getrole?groupid=1
        // access 1: admin
        if ($route->action == "getrole") {
            $route->format = "json";
            $result = $group->getrole($session["userid"], get("groupid"));
        }
    }

    return array('content' => $result, 'fullwidth' => false);
}
