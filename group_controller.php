<?php

/*
  All Emoncms code is released under the GNU Affero General Public License.
  See COPYRIGHT.txt and LICENSE.txt.

  ---------------------------------------------------------------------
  Emoncms - open source energy visualisation
  Part of the OpenEnergyMonitor project:
  http://openenergymonitor.org

  Group module has been developed by Carbon Co-op
  https://carbon.coop/

 */


// no direct access
defined('EMONCMS_EXEC') or die('Restricted access');

function group_controller() {
    global $session, $route, $mysqli, $redis, $user, $feed_settings, $log;

    $result = false;

    include "Modules/feed/feed_model.php";
    $feed = new Feed($mysqli, $redis, $feed_settings);

    include "Modules/input/input_model.php";
    $input = new Input($mysqli, $redis, $feed);

    $task = null;
    $result = $mysqli->query("SHOW TABLES LIKE 'tasks'");
    if (is_file("Modules/task/task_model")) {
        require_once "Modules/process/process_model.php";
        $process = new Process($mysqli, $input, $feed, $user->get_timezone($session['userid']));
        require_once "Modules/task/task_model.php";
        $task = new Task($mysqli, $redis, $process);
    }


    require_once "Modules/group/group_model.php";
    $group = new Group($mysqli, $redis, $user, $feed, $input, $task);


    // ------------------------------------------------------------------------------------
    // API
    // ------------------------------------------------------------------------------------
    if ($session['write']) {

        if ($route->action == "") {
            $route->format = "html";
            if (is_null($task) === true)
                $result = view("Modules/group/group_view.php", array('task_support' => false));
            else
                $result = view("Modules/group/group_view.php", array('task_support' => true));
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
            $result = $group->createuseraddtogroup($session["userid"], prop("groupid"), prop('email'), prop("username"), prop("password"), prop("role"), prop('name'));
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

        // 
        if ($route->action == "setmultifeedprocesslist") {
            $route->format = "json";
            $result = $group->set_multifeed_processlist($session["userid"], get('groupid'), get('feedids'), post("processlist"), post("name"), post("description"), post("tag"), post("frequency"), post("run_on"), post('belongs_to'));
        }

        if ($route->action == 'setprocesslist') {
            $route->format = "json";
            $result = $group->set_processlist($session['userid'], get('id'), get('userid'), get('groupid'), post('processlist'));
        }
        if ($route->action == 'deletetask') {
            $route->format = "json";
            $result = $group->delete_task($session['userid'], get('taskid'), get('userid'), get('groupid'));
        }
        if ($route->action == 'settaskenabled') {
            $route->format = "json";
            $result = $group->set_task_enabled($session['userid'], get('taskid'), get('userid'), get('groupid'), get('enabled'));
        }
        if ($route->action == 'updateemoncms') {
            if ($session['admin'] == 1) {
                $route->format = "json";
                require_once "Modules/update/update_model.php";
                $update = new Update();
                $result = $update->update('emoncms', 'nothing');
                $a = 3;
            }
        }

        // group/sendlogindetails?groupid=1&email=EMAIL&username=USERNAME&password=PASSWORD&role=1
        if ($route->action == "sendlogindetails") {
            $route->format = "json";
            $result = $group->send_login_details($session["userid"], prop("groupid"), prop("userid"), prop("password"), prop("emailsubject"), prop("template"), prop('sendcopy'));
        }
                
        // group/deleteallsgroupsfromuser
        if ($route->action == "deleteallgroupsfromuser") {
            $route->format = "json";
            $result = $group->delete_all_groups_from_user($session["userid"]);
        }
        
        
        /* if ($route->action == 'updatetheme') {
          if ($session['admin'] == 1) {
          $route->format = "json";
          require_once "Modules/update/update_model.php";
          $update = new Update();
          $result = $update->update('themes', 'nef');
          $a = 3;
          }
          } */
        // --------------------------------------------------------------------------
        // SPECIAL USER SWITCHING FUNCTIONS
        // --------------------------------------------------------------------------
        if ($route->action == 'setuser') {
            $route->format = "text";

            $groupid = (int) get('groupid');
            $cur_username = $session['username'];
            $cur_userid = $session['userid'];
            $cur_is_admin = $session['admin'] === 1 ? true : false;
            $new_userid = (int) get('userid');

            $log->info("User $cur_userid:$cur_username " .
                    ($cur_is_admin ? "(admin)" : "(not admin)") .
                    " is trying to log in as userid $new_userid");

            // 1. Check that session user is an administrator of the group requested
            if ($group->is_group_admin($groupid, $cur_userid) !== true) {
                $result = "ERROR: You are not an administrator of this group";
                $log->error($result);
                return [ 'content' => $result ];
            }

            // 2. Check that user requested is a member of group requested
            if ($group->is_group_member($groupid, $new_userid) !== true) {
                $result = "ERROR: User is not a member of group";
                $log->error($result);
                return [ 'content' => $result ];
            }

            // 3. Check that session user has full rights over user requested
            if ($group->administrator_rights_over_user($groupid, $cur_userid) !== true) {
                $result = "ERROR: You haven't got rights to access this user";
                $log->error($result);
                return [ 'content' => $result ];
            }

            // 4. Check that we don't escalate from a non-admin user to an admin-user
            if ($cur_is_admin === false) {
                $result = $mysqli->query("SELECT admin FROM users WHERE id = $new_userid");
                $row = $result->fetch_array();
                if ((int) $row['admin'] !== 0) {
                    $result = "ERROR: Can't impersonate an admin user as a non-admin user";
                    $log->error($result);
                    return [ 'content' => $result ];
                }
            }

            // Keep details of current user
            if (!$_SESSION['previous_userid']) {
                $_SESSION['previous_userid'] = $cur_userid;
                $_SESSION['previous_username'] = $cur_username;
                $_SESSION['previous_admin'] = $session['admin'];
            }

            // Set new user
            $result = $mysqli->query("SELECT * FROM users WHERE id = '$new_userid'");
            if ($result->num_rows < 1) {
                $this->logout(); // user id does not exist
            }
            else {
                $new_user = $result->fetch_object();
                $_SESSION['userid'] = $new_userid;
                $_SESSION['username'] = $new_user->username;
                $_SESSION['admin'] = $new_user->admin;
                $log->warn("Login successful - $cur_username changed to " . $new_user->username);
            }

            if (is_null(get('view')))
                header("Location: ../user/view");
            else if (get('view') == 'tasks')
                header("Location: ../task/list#" . get('tag'));
        }

        if ($route->action == 'logasprevioususer') {
            $route->format = "text";

            if ($_SESSION['previous_userid']) {
                $_SESSION['userid'] = $_SESSION['previous_userid'];
                $_SESSION['username'] = $_SESSION['previous_username'];
                $_SESSION['admin'] = $_SESSION['previous_admin'];
                unset($_SESSION['previous_userid']);
                unset($_SESSION['previous_username']);
                unset($_SESSION['previous_admin']);
            }

            header("Location: ../group");
        }

        // Developemtn
        /* if ($route->action == "getapikeys") {
          $route->format = "json";
          $result = $group->getapikeys($session['userid'], get("groupid"));
          } */
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
        /* if ($route->action == "getrole") {
          $route->format = "json";
          $result = $group->getrole(get("userid"), get("groupid"));
          } */

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
            $result = $group->getfeed($session["userid"], $route->subaction, get('id'), get('start'), get('end'), get('interval'), get('skipmissing'), get('limitinterval'), get('mode'));
        }
    }

    //---------------------------
    // NO SESSION
    //---------------------------
    if ($route->action == "getfeed") { // When displaying a public feed for example in a public dashboard
        $route->format = "json";
        $result = $group->getfeed($session["userid"], $route->subaction, get('id'), get('start'), get('end'), get('interval'), get('skipmissing'), get('limitinterval'), get('mode'));
    }

    return array('content' => $result, 'fullwidth' => false);
}
