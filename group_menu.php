<?php

$menu_dropdown_config[] = array('name' => "Groups", 'icon' => 'icon-globe', 'path' => "group", 'session' => "write", 'order' => 50);
// ----------------------------------------------------------------------------------------
// Display info about previous user logged in
// ----------------------------------------------------------------------------------------
if (key_exists('previous_userid', $_SESSION) == true) {
    $menu_left[] = array('name' => "You were originally logged as " . $_SESSION['previous_username'], 'icon' => 'icon-user icon-white', 'path' => "group/logasprevioususer", 'session' => "write", 'order' => 1000);
}

    