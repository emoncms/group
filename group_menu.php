<?php
// setup menu item
$menu['sidebar']['emoncms'][] = array(
    'text' => _("Groups"),
    'path' => 'group',
    'icon' => 'users',
    'data'=> array('sidebar' => '#sidebar_group')
);

// Display info about previous user logged in
// ----------------------------------------------------------------------------------------
if (isset($_SESSION) != false && key_exists('previous_userid', $_SESSION) == true) {
    $menu['sidebar']['includes']['setup']['group'][] = array(
        'text' => $_SESSION['previous_username'] . " => " . $_SESSION['username'],
        'path' => 'group/logasprevioususer',
    );
}

// sub menu
$menu['sidebar']['includes']['emoncms']['group'][] = array(
    'text' => _('New Group'),
    'id' => 'groupcreate',
    'icon' => 'plus',
    'href' => '#new',
    'order'=> 99
);
$menu['sidebar']['includes']['emoncms']['group'][] = view('Modules/group/Views/search.php',array());
$menu['sidebar']['includes']['emoncms']['group'][] = view('Modules/group/Views/grouplist.php',array());