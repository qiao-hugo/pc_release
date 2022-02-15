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

class ServiceTask extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_servicetask';
    var $table_index= 'servicetaskid';
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
    var $tab_name = Array('vtiger_servicetask');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
        'vtiger_servicetask'   => 'servicetaskid',);

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
    var $popup_fields = Array('servicetaskid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'servicetaskid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'servicetaskid';

    // Required Information for enabling Import feature
    var $required_fields = Array('servicetaskid'=>1);

    // Callback function list during Importing
    var $special_functions = Array('servicetaskid');

    var $default_order_by = 'servicetaskid';
    var $default_sort_order='ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('servicetaskid');

    function __construct() {
        global $log, $currentModule;
        $this->column_fields = getColumnFields(get_class($this));
        $this->db = PearDatabase::getInstance();
        $this->log = $log;
    }

    /**
     * Retrieve record information of the module
     * @param <Integer> $record - crmid of record
     * @param <String> $module - module name
     */
    function retrieve_entity_info($record, $module) {
    	global $adb, $log, $app_strings;
    	 
    	$sql="SELECT	
			vtiger_servicetask.servicetaskid,
			(select taskpackagename from vtiger_taskpackage where taskpackageid=vtiger_servicetask.taskpackageid) as taskpackageid,
			vtiger_servicetask.taskname,
			vtiger_servicetask.runmode,
			vtiger_servicetask.runconditiontype,
			vtiger_servicetask.relativeday,
			vtiger_servicetask.circulationday,
			vtiger_servicetask.circulationcount,
			vtiger_servicetask.timeconsuming,
			vtiger_servicetask.taskcontent,
			vtiger_servicetask.helpinfo,
			vtiger_servicetask.creatorid,
			vtiger_servicetask.createdtime,
			vtiger_servicetask.modifiedby,
			vtiger_servicetask.modifiedtime,
			vtiger_servicetask.remark
			FROM
				vtiger_servicetask
			WHERE
				vtiger_servicetask.servicetaskid=?";
    	 
    	$params[] = $record;
    	$result = $adb->pquery($sql, $params);
    
    	if (!$result || $adb->num_rows($result) < 1) {
    		throw new Exception($app_strings['LBL_RECORD_NOT_FOUND'], -1);
    	}
    	$resultrow = $adb->query_result_rowdata($result);
    	$cachedModuleFields = VTCacheUtils::lookupFieldInfo_Module($module);
    	
    	foreach ($cachedModuleFields as $fieldinfo) {
    
    		$fieldvalue = '';
    		$fieldkey = $this->createColumnAliasForField($fieldinfo);

    		//Note : value is retrieved with a tablename+fieldname as we are using alias while building query
    		if (isset($resultrow[$fieldkey])) {
    			$fieldvalue = $resultrow[$fieldkey];
    		}

    		$this->column_fields[$fieldinfo['fieldname']] = $fieldvalue;
    	}
    	$this->column_fields['record_id'] = $record;
    	$this->column_fields['record_module'] = $module;
    
    }
    
    /**
     * Function returns the column alias for a field
     * @param <Array> $fieldinfo - field information
     * @return <String> field value
     */
    protected function createColumnAliasForField($fieldinfo) {
    	return strtolower($fieldinfo['fieldname']);
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

    	//条件类型
    	$runconditiontype=$_REQUEST['runconditiontype'];

    	if ($insertion_mode == 'edit') {
    		if ($runconditiontype =='0'){
    			$update_query = "update vtiger_servicetask set circulationday=null,circulationcount=null,runconditiontype=?,modifiedby=?,modifiedtime=sysdate() where servicetaskid=?";
    		}else{
    			$update_query = "update vtiger_servicetask set relativeday=null,runconditiontype=?,modifiedby=?,modifiedtime=sysdate() where servicetaskid=?";
    		}
    	}else{
    		if ($runconditiontype =='0'){
    			$update_query = "update vtiger_servicetask set circulationday=null,circulationcount=null,runconditiontype=?,creatorid=?,createdtime=sysdate() where servicetaskid=?";
    		}else{
    			$update_query = "update vtiger_servicetask set relativeday=null,runconditiontype=?,creatorid=?,createdtime=sysdate() where servicetaskid=?";
    		}
    	}
    	$this->db->pquery($update_query, array($runconditiontype,$current_user->id,$this->id));
    }

}
?>
