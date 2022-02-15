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

class ContractsProducts extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_contractsproductsrel';
    var $table_index= 'relcontractsproductsid';
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
    var $tab_name = Array('vtiger_contractsproductsrel');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
        'vtiger_contractsproductsrel'   => 'relcontractsproductsid',);

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
    var $popup_fields = Array('relcontractsproductsid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'relcontractsproductsid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'relcontractsproductsid';

    // Required Information for enabling Import feature
    var $required_fields = Array('relcontractsproductsid'=>1);

    // Callback function list during Importing
    var $special_functions = Array('relcontractsproductsid');

    var $default_order_by = 'relcontractsproductsid';
    var $default_sort_order='ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('relcontractsproductsid');

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

    
    /**
     * 更新后处理
     * @param unknown $module
     */
    function save_module($module) {
   		//获取登录用户信息
	   	global $current_user;
	   	//更新通用字段
	   	$insertion_mode=$this->mode;
	   	if ($insertion_mode == 'edit'&& $_REQUEST['record'] > 0) {
	   		$update_query = "update vtiger_contractsproductsrel set modifiedby=?,modifiedtime=sysdate() where relcontractsproductsid=?";
            $this->db->pquery($update_query, array($current_user->id,$_REQUEST['record']));

	   	}else{
	   		$update_query = "update vtiger_contractsproductsrel set creatorid=?,createdtime=sysdate() where relcontractsproductsid=?";
            $this->db->pquery($update_query, array($current_user->id,$this->id));

	   	}
    }
   }
?>
