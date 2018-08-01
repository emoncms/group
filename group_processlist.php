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

// Schedule Processlist Module
class Group_ProcessList {

    private $log;

    //private $group;
    // Module required constructor, receives parent as reference
    public function __construct(&$parent) {
        $this->log = new EmonLogger(__FILE__);

        //include_once "Modules/group/group_model.php";
        //$this->group = new Group();
    }

    // Module required process configuration, $list array index position is not used, function name is used instead
    public function process_list() {
        $list = array(
            array(
                "name" => _("Source multi-feed"),
                "short" => "->multifeed",
                "argtype" => ProcessArg::VALUE,
                "function" => "ifRateGtEqualSkip",
                "datafields" => 0,
                "datatype" => DataType::UNDEFINED,
                "unit" => "",
                "group" => _("Hidden"),
                "requireredis" => false,
                "nochange" => false,
                "description" => _("<p>List of feeds to which the rest of the process list is applied</p>")
            )
        );
        return $list;
    }

    // \/ Below are functions of this module processlist, same name must exist on process_list()

    public function source_multifeed($scheduleid, $time, $value) {
        return ($result ? $value : 0);
    }

}
