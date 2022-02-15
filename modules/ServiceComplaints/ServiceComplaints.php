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

class ServiceComplaints extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity
    var $table_name = 'vtiger_servicecomplaints';
    var $table_index= 'servicecomplaintsid';
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
    var $tab_name = Array('vtiger_servicecomplaints');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
        'vtiger_servicecomplaints'   => 'servicecomplaintsid',);

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
    var $popup_fields = Array('servicecomplaintsid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'servicecomplaintsid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'servicecomplaintsid';

    // Required Information for enabling Import feature
    var $required_fields = Array('servicecomplaintsid'=>1);

    // Callback function list during Importing
    var $special_functions = Array('servicecomplaintsid');

    var $default_order_by = 'servicecomplaintsid';
    var $default_sort_order='ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('servicecomplaintsid');

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
    	$currentUser = Users_Record_Model::getCurrentUserModel();
    	$userid = $currentUser->get('id');
    	
        $insertion_mode=$this->mode;
	   	if ($insertion_mode == 'edit') {

    	}else{
    		//更新创建人
    		$update_query = "update vtiger_servicecomplaints set createid=? where servicecomplaintsid=?";
    		$update_params = array($userid,$this->id);
    		$this->db->pquery($update_query, $update_params);
    	}
    }

    //客户详细访问权限
    function retrieve_entity_info($record, $module){
        parent::retrieve_entity_info($record, $module);
        global $currentView;
        $where=getAccessibleUsers('ServiceComplaints','',true);
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
