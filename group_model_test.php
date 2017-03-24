<?php
die;

define('EMONCMS_EXEC', 1);

chdir("/var/www/master");
require "process_settings.php";
require "Lib/EmonLogger.php";

require("Modules/user/user_model.php");
include "Modules/group/group_model.php";

$mysqli = @new mysqli($server,$username,$password,$database,$port);
$redis = false;

$user = new User($mysqli,$redis);

$group = new Group($mysqli,$user);

$userid = 1;

// 1. Test creating a group
// $result = $group->create($userid,"MyGroup","Energy Sharing Group");

// 2. Test adding a user to a group
// $result = $group->add_user_auth($userid,1,"test","test",1); 

// 3. Test group list
// $result = $group->grouplist($userid); 

// 2. Test user list
// $result = $group->userlist($userid,1); 

print json_encode($result);
