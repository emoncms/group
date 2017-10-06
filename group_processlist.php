<?php
/*
 All Emoncms code is released under the GNU Affero General Public License.
 See COPYRIGHT.txt and LICENSE.txt.
 ---------------------------------------------------------------------
 Emoncms - open source energy visualisation
 Part of the OpenEnergyMonitor project: http://openenergymonitor.org
 */

// no direct access
defined('EMONCMS_EXEC') or die('Restricted access');

// Schedule Processlist Module
class Group_ProcessList
{
    private $log;
    //private $group;

    // Module required constructor, receives parent as reference
    public function __construct(&$parent)
    {
        $this->log = new EmonLogger(__FILE__);

        //include_once "Modules/group/group_model.php";
        //$this->group = new Group();
    }

    // Module required process configuration, $list array index position is not used, function name is used instead
    public function process_list()
    {
        // 0=>Name | 1=>Arg type | 2=>function | 3=>No. of datafields if creating feed | 4=>Datatype | 5=>Group | 6=>Engines | 'desc'=>Description | 'requireredis'=>true | 'nochange'=>true  | 'helpurl'=>"http://..."
        $list[] = array(_("Source multi-feed"), ProcessArg::TEXT, "source_multifeed", 0, DataType::UNDEFINED, "Hidden", 'desc'=>"<p>List of feeds to which the rest of the process list is applied</p>");
        return $list;
    }

    
    // \/ Below are functions of this module processlist, same name must exist on process_list()
    
    public function source_multifeed($scheduleid, $time, $value) {
        return ($result ? $value : 0);
    }
}
