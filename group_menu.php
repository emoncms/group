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
global $session;

$menu_dropdown_config[] = array('name' => "Groups", 'icon' => 'icon-globe', 'path' => "group", 'session' => "write", 'order' => 50);
// ----------------------------------------------------------------------------------------
// Display info about previous user logged in
// ----------------------------------------------------------------------------------------
if (isset($_SESSION) != false && key_exists('previous_userid', $_SESSION) == true) {
    $menu_left[] = array('name' => "You were originally logged as " . $_SESSION['previous_username'], 'icon' => 'icon-user icon-white', 'path' => "group/logasprevioususer", 'session' => "write", 'order' => 1000);
}

    