<?php

$capabilities['group'] = [
	'group_create'             => 'create groups',
	'group_edit'               => 'edit group info',
	'group_delete'             => 'delete groups',
	'group_create_user'        => 'create new users inside groups',
	'group_add_member_auth'    => 'add users to groups (with password)',
	'group_add_member_no_auth' => 'add users to groups (without password)',
	'group_add_member_invite'  => 'invite users to join groups',
	'group_delete_user'        => 'delete users',
	'group_edit_user'          => 'edit user info',
	'group_impersonate'        => 'can impersonate other users',
];
