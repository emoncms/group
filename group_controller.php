<?php

// no direct access
defined('EMONCMS_EXEC') or die('Restricted access');

function group_controller() {
    global $session, $route, $mysqli, $redis, $user, $feed_settings;

    $result = false;

    include "Modules/feed/feed_model.php";
    $feed = new Feed($mysqli, $redis, $feed_settings);

    include "Modules/input/input_model.php";
    $input = new Input($mysqli, $redis, $feed);

    $result = $mysqli->query("SHOW TABLES LIKE 'dashboard'");
    $dashboard_module_installed = $result->num_rows > 0 ? true : false;
    if ($dashboard_module_installed === true) {
        include "Modules/dashboard/dashboard_model.php";
        $dashboard = new Dashboard($mysqli);
    }
    else {
        $dashboard = null;
    }

    $result = $mysqli->query("SHOW TABLES LIKE 'graph'");
    $graph_module_installed = $result->num_rows > 0 ? true : false;
    if ($graph_module_installed === true) {
        include "Modules/graph/graph_model.php";
        $graph = new Graph($mysqli, 'null');
    }
    else {
        $graph = null;
    }

    include "Modules/group/group_model.php";
    $group = new Group($mysqli, $redis, $user, $feed, $input, $dashboard, $graph);


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

        // group/removeuser?groupid=1&userid=1
        if ($route->action == "fullremoveuser") {
            $route->format = "json";
            $result = $group->full_remove_user($session["userid"], get("groupid"), get("userid"));
        }

        // group/delete?groupid=1
        if ($route->action == "delete") {
            $route->format = "json";
            $result = $group->delete($session["userid"], get("groupid"));
        }

        // group/setuserinfo?"groupid=12&userid=2&username=fede123&name=federico&email=federico@gmail.com&bio=&timezone=UTC&location=Machynlleth&role=2&password=mypwd&tags={system:PV,house:big}
        if ($route->action == "setuserinfo") {
            $route->format = "json";
            $result = $group->setuserinfo($session["userid"], post('groupid'), post("userid"), post('username'), post('name'), post('email'), post('bio'), post('timezone'), post('location'), post('role'), post('password'), post('tags'));
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
                    }
                    else
                        $result = "ERROR: You haven't got rights to access this user";
                } else {
                    $result = "ERROR: User is not a member of group";
                }
            }
            else {
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

        // Developemtn
        if ($route->action == "getapikeys") {
            $route->format = "json";
            $result = $group->getapikeys($session['userid'], get("groupid"));
        }
    }

    //---------------------------
    // SESSION READ
//---------------------------
    if ($session['read']) {
        // group/grouplist
        if ($route->action == "grouplist") {
            $route->format = "json";
            $result = $group->grouplist($session["userid"]);
        }

        // group/userlist?groupid=1
        if ($route->action == "userlist") {
            $route->format = "json";
            $result = $group->userlist($session["userid"], get("groupid"));
        }

        // group/getuserfeeds?groupid=1&userid=12
        if ($route->action == "getuserfeeds") {
            $route->format = "json";
            $result = $group->getuserfeeds($session["userid"], get("groupid"), get('userid'));
        }

        // group/getrole?userid=1&groupid=1
        if ($route->action == "getrole") {
            $route->format = "json";
            $result = $group->getrole(get("userid"), get("groupid"));
        }

        // group/getsessionuserrole?groupid=1
        if ($route->action == "getsessionuserrole") {
            $route->format = "json";
            $result = $group->getrole($session["userid"], get("groupid"));
        }

        // group/getsessionuserrole?groupid=1
        if ($route->action == "csvexport") {
            $route->format = "json";
            $result = $group->csvexport($session["userid"], get('groupid'), get("id"), get('start'), get('end'), get('interval'), get('timeformat'), get('name'));
        }

        // group/mygroups
        if ($route->action == "mygroups") {
            $route->format = "json";
            $result = $group->mygroups($session["userid"]);
        }

        // group/getfeed/data.json?id=111&start=1500566400000&end=1501172100000&interval=900&skipmissing=0&limitinterval=undefined"
        if ($route->action == "getfeed") {
            $route->format = "json";
            $result = $group->getfeed($session["userid"], $route->subaction, get('id'), get('start'), get('end'), get('interval'), get('skipmissing'), get('limitinterval'));
        }
    }

    return array('content' => $result, 'fullwidth' => false);
}
