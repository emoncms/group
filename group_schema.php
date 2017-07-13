<?php

$schema['groups'] = array(
    'id' => array('type' => 'int(11)', 'Null' => 'NO', 'Key' => 'PRI', 'Extra' => 'auto_increment'),
    'name' => array('type' => 'varchar(64)'),
    'description' => array('type' => 'varchar(256)'),
    'organization' => array('type' => 'varchar(64)'),
    'area' => array('type' => 'varchar(64)'),
    'visibility' => array('type' => 'varchar(16)'),
    'access' => array('type' => 'varchar(16)')
);

// access:
// 0 - passive member: just a normal user with no access to anything in the group. The aim of the user is to be managed by the group administrator
// 1 - administrator: full access (create user, add member, create group feeds, dashboards graphs, etc)
// 2 - community member: access to the list of members, group dashboards and group graphs modules.
// 3 - anonymous member: view access to dashboards

$schema['group_users'] = array(
    'groupid' => array('type' => 'int(11)'),
    'userid' => array('type' => 'int(11)'),
    'access' => array('type' => 'int(1)'),
    'admin_rights' => array('type' => 'varchar(16)')
);
