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

class ServiceAssignSet extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_serviceassignset';
    var $table_index= 'serviceassignsetid';
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
    var $tab_name = Array('vtiger_serviceassignset');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
        'vtiger_serviceassignset'   => 'serviceassignsetid',);

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
    var $popup_fields = Array('serviceassignsetid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'serviceassignsetid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'serviceassignsetid';

    // Required Information for enabling Import feature
    var $required_fields = Array('serviceassignsetid'=>1);

    // Callback function list during Importing
    var $special_functions = Array('serviceassignsetid');

    var $default_order_by = 'serviceassignsetid';
    var $default_sort_order='ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('serviceassignsetid');

    function __construct() {
        global $log, $currentModule;
        $this->column_fields = getColumnFields(get_class($this));
        $this->db = PearDatabase::getInstance();
        $this->log = $log;
    }

    /** Function to insert values in the specifed table for the specified module
     * @param $table_name -- table name:: Type varchar
     * @param $module -- module:: Type varchar
     */
    function insertIntoEntityTable($table_name, $module, $fileid = '') {
    	global $log;
    	global $current_user;
    	$log->info("function insertIntoEntityTable " . $module . ' vtiger_table name ' . $table_name);
    	global $adb;
    	
    	//获取id
    	$sql="select serviceassignsetid from vtiger_serviceassignset where serviceid=? order by serviceassignsetid desc";
    	$result=$adb->pquery($sql, array($_REQUEST['serviceid']));
    	
    	if ($result && $adb->num_rows($result)>0) {
    		//获取id
    		$this->id=$adb->query_result($result, 0,'serviceassignsetid');

    		//更新的场合
    		$updateSql = "update vtiger_serviceassignset set serviceid=?,accountcount=?,modifiedby=?,modifiedtime=sysdate(),remark=? where serviceassignsetid=?";
    		$updateparams[]=$_REQUEST['serviceid'];
    		$updateparams[]=$_REQUEST['accountcount'];
    		$updateparams[]=$current_user->id;
    		$updateparams[]=$_REQUEST['remark'];
    		$updateparams[]=$this->id;
    		$adb->pquery($updateSql, $updateparams);
    	}else{
    		//新增的场合
    		$insertSql = "insert into vtiger_serviceassignset(
    		    				serviceid,accountcount,creatorid,createdtime,remark)
    		    				 values(?,?,?,sysdate(),?)";
    		$insertparams[]=$_REQUEST['serviceid'];
    		$insertparams[]=$_REQUEST['accountcount'];
    		$insertparams[]=$current_user->id;
    		$insertparams[]=$_REQUEST['remark'];
    		$adb->pquery($insertSql, $insertparams);
    		
    		//获取id
    		$result=$adb->pquery($sql, array($_REQUEST['serviceid']));
    		$this->id=$adb->query_result($result, 0,'serviceassignsetid');
    	}
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

	   	if ($insertion_mode == 'edit') {
	   		$update_query = "update vtiger_serviceassignset set modifiedby=?,modifiedtime=sysdate() where serviceassignsetid=?";
	   	}else{
	   		$update_query = "update vtiger_serviceassignset set creatorid=?,createdtime=sysdate() where serviceassignsetid=?";
	   	}
	   	$this->db->pquery($update_query, array($current_user->id,$this->id));
    }

}
?>
