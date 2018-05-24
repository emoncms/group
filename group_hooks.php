<?php

/*
  All Emoncms code is released under the GNU Affero General Public License.
  See COPYRIGHT.txt and LICENSE.txt.

  ---------------------------------------------------------------------
  Emoncms - open source energy visualisation
  Part of the OpenEnergyMonitor project:
  http://openenergymonitor.org

 */

// no direct access
defined('EMONCMS_EXEC') or die('Restricted access');

function group_on_delete_user($args) {
    global $mysqli;
    $userid = (int) $args['userid'];

    $result = "";
    if ($result1 = $mysqli->query("SELECT * FROM group_users WHERE `userid`='$userid'")) {
        if ($result1->num_rows > 0) {
            $result = "- group_users entry\n";
            if ($args['mode'] == "permanentdelete")
                $mysqli->query("DELETE FROM group_users WHERE `userid`='$userid'");
        }
    }
    return $result;
}

?>
