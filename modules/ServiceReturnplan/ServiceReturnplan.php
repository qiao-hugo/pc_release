<?php
include_once('config.php');
require_once('include/logging.php');
require_once('modules/Contacts/Contacts.php');
require_once('modules/Potentials/Potentials.php');
require_once('modules/Calendar/Activity.php');
require_once('modules/Documents/Documents.php');
require_once('modules/Emails/Emails.php');
require_once('include/utils/utils.php');
require_once('user_privileges/default_module_view.php');

class ServiceReturnplan extends CRMEntity {
	var $log;
	var $db;
	var $table_name = "vtiger_servicereturnplan";
	var $table_index= 'servicereturnplanid';
    var $tab_name_index = Array('vtiger_servicereturnplan'=>'servicereturnplanid');//'vtiger_crmentity' => 'crmid',
	var $tab_name = Array('vtiger_servicereturnplan');
	var $column_fields = Array();
	var $sortby_fields = Array();
	var $list_fields = Array();
	var $list_fields_name = Array();
	var $list_link_field= 'serviceReturnname';
	var $search_fields = Array();
	var $search_fields_name = Array();
	var $required_fields =  array();
	var $mandatory_fields = Array();
	var $emailTemplate_defaultFields = array();
	var $default_order_by = 'serviceReturnname';
	var $default_sort_order = 'ASC';
	// For Alphabetical search
	var $def_basicsearch_col = 'serviceReturnname';
	var $related_module_table_index = array();
	//关联模块的一些字段和数组;
	var $relatedmodule_list=array();
	var $relatedmodule_fields=array();
    function __construct() {
        global $log, $currentModule;
        $this->column_fields = getColumnFields(get_class($this));
        $this->db = PearDatabase::getInstance();
        $this->log = $log;
    }
	/** Function to handle module specific operations when saving a entity
	*/
	function save_module($module) {
        //echo $this->id;$_REQUEST['record'];
        $id = $this->id;
        $upperlimit=$_REQUEST['upperlimit'];
        $lowerlimit=$_REQUEST['lowerlimit'];
        $returnplantext=$_REQUEST['returnplantext'];
        $sequence=$_REQUEST['sequence'];
        if($sequence && $id){
            $del_sql = "DELETE  FROM vtiger_servicereturnplan_detail WHERE returnplanid=?";
            $this->db->pquery($del_sql,array($this->id));
            $sql = "INSERT INTO `vtiger_servicereturnplan_detail` (upperlimit, lowerlimit, returnplantext, sequence, returnplanid) VALUES (?,?,?,?,?)";
           foreach($sequence as $key=>$value){
                $this->db->pquery($sql,array($upperlimit[$key],$lowerlimit[$key],$returnplantext[$key],$sequence[$key],$this->id));
           }
        }
    }

    function transferRelatedRecords(){
    }
}
?>
