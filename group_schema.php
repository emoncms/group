<?php

/* * ******************************************************
 * groups table
 * 
 *  - visibility:
 *      - public: can be found on searches
 *      - private: cannot be found in searches
 *  - access:
 *      - open: everybody can join
 *      - closed: join by requests
 *      
 * ***************************************************** */
$schema['groups'] = array(
    'id' => array('type' => 'int(11)', 'Null' => 'NO', 'Key' => 'PRI', 'Extra' => 'auto_increment'),
    'name' => array('type' => 'varchar(64)'),
    'description' => array('type' => 'varchar(256)'),
    'organization' => array('type' => 'varchar(64)'),
    'area' => array('type' => 'varchar(64)'),
    'visibility' => array('type' => 'varchar(16)'),
    'access' => array('type' => 'varchar(16)')
);

/* * ******************************************************
 * group_users table
 * 
 *  - role:
 *       0 - passive member: just a normal user with no access to anything in the group. The aim of the user is to be managed by the group administrator
 *       1 - administrator: full access (create user, add member, create group feeds, dashboards graphs, etc)
 *       2 - sub-administrator (community member): access to the list of members, group dashboards and group graphs modules.
 *       3 - member: view access to dashboards
 *  - admin_rights: administrator's rights over user's data
 *      - full: Full access to member's account (login, change password, create feeds, etc)
 *      - read: Read and list access to member's public feeds
 *      
 * ***************************************************** */

$schema['group_users'] = array(
    'groupid' => array('type' => 'int(11)'),
    'userid' => array('type' => 'int(11)'),
    'role' => array('type' => 'int(1)'),
    'admin_rights' => array('type' => 'varchar(16)')
);
