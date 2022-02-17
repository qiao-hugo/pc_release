<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once('data/CRMEntity.php');
require_once('data/Tracker.php');

class ServiceAssignRule extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_serviceassignrule';
    var $table_index= 'serviceassignruleid';
    var $column_fields = Array();

    /** Indicator if this is a custom module or standard module */
    var $IsCustomModule = true;

    /**
     * Mandatory table for supporting custom fields.
     */
    var $customFieldTable = Array();

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    var $tab_name = Array('vtiger_serviceassignrule');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
        'vtiger_serviceassignrule'   => 'serviceassignruleid',);

    /**
     * Mandatory for Listing (Related listview)
     */
    var $list_fields = Array (
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
  

    );
    var $list_fields_name = Array(
        /* Format: Field Label => fieldname */
 
    );

    // Make the field link to detail view from list view (Fieldname)
    var $list_link_field = 'relmodule';

    // For Popup listview and UI type support
    var $search_fields = Array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
 
    );
    var $search_fields_name = Array(
        /* Format: Field Label => fieldname */
    
    );

    // For Popup window record selection
    var $popup_fields = Array('serviceassignruleid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'serviceassignruleid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'serviceassignruleid';

    // Required Information for enabling Import feature
    var $required_fields = Array('serviceassignruleid'=>1);

    // Callback function list during Importing
    var $special_functions = Array('serviceassignruleid');

    var $default_order_by = 'serviceassignruleid';
    var $default_sort_order='ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('serviceassignruleid');

    function __construct() {
        global $log, $currentModule;
        $this->column_fields = getColumnFields(get_class($this));
        $this->db = PearDatabase::getInstance();
        $this->log = $log;
    }

    /**
     * 更新后处理
     * @param unknown $module
     */
    function save_module($module) {
   		//获取登录用户信息
	   	global $current_user;
	   	//更新通用字段
	   	$insertion_mode=$this->mode;
	   	$assigntype=$_REQUEST['assigntype'];
	   
	   	if ($insertion_mode == 'edit') {
	   		$update_query = "update vtiger_serviceassignrule set modifiedby=?,modifiedtime=sysdate() where serviceassignruleid=?";
	   	}else{
	   		$update_query = "update vtiger_serviceassignrule set creatorid=?,createdtime=sysdate() where serviceassignruleid=?";
	   	}
	   	$this->db->pquery($update_query, array($current_user->id,$this->id));
    }

    //客户详细访问权限
    function retrieve_entity_info($record, $module){
        parent::retrieve_entity_info($record, $module);
        global $currentView;
        $where=getAccessibleUsers('ServiceAssignRule','',true);
        if($where!='1=1') {
            if (!in_array($this->column_fields['serviceid'], $where)) {
                if ($currentView == 'Edit' || $currentView == 'Detail'||$currentView == 'Delete') {
                    throw new AppException('你没有操作权限！');
                    exit;
                }
            }

        }
    }
}
?>
