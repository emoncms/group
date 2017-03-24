<?php

    $schema['groups'] = array(
        'id' => array('type' => 'int(11)', 'Null'=>'NO', 'Key'=>'PRI', 'Extra'=>'auto_increment'),
        'name' => array('type' => 'text'),
        'description' => array('type' => 'text')
    );
    
    /*
    $schema['group_owners'] = array(
        'groupid' => array('type' => 'int(11)'),
        'userid' => array('type' => 'int(11)')
    );
    */
    
    // access:
    // 0 - user
    // 1 - administrator
    
    $schema['group_users'] = array(
        'groupid' => array('type' => 'int(11)'),
        'userid' => array('type' => 'int(11)'),
        'access'=> array('type' => 'int(11)')
    );
