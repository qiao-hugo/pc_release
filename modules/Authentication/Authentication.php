<?php
require_once('data/CRMEntity.php');
require_once('data/Tracker.php');

class Authentication extends CRMEntity {
	var $log;
	var $db;
	var $table_name = "vtiger_authentication";
	var $table_index= 'authenticationid';
    var $tab_name_index = Array('vtiger_authentication'=>'authenticationid');//'vtiger_crmentity' => 'crmid',
	var $tab_name = Array('vtiger_authentication');
	var $column_fields = Array();
	var $sortby_fields = Array();
	var $list_fields = Array();
	var $list_fields_name = Array();
	var $list_link_field= '';
	var $search_fields = Array();
	var $search_fields_name = Array();
	var $required_fields =  array();
	var $mandatory_fields = Array();
	var $emailTemplate_defaultFields = array();
	var $default_order_by = 'createdtime';
	var $default_sort_order = 'DESC';
	// For Alphabetical search
	var $def_basicsearch_col = '';
	var $related_module_table_index = array();
	//关联模块的一些字段和数组;
    var $relatedmodule_list = array();
    var $relatedmodule_fields = array(
    );
    function __construct() {
        global $log, $currentModule;
        $this->column_fields = getColumnFields(get_class($this));
        $this->db = PearDatabase::getInstance();
        $this->log = $log;
    }

    public function save_module($module){

    }


}
?>
