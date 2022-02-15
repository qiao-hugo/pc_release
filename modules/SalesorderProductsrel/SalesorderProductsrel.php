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

class SalesorderProductsrel extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_salesorderproductsrel';
    var $table_index= 'salesorderproductsrelid';
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
    var $tab_name = Array('vtiger_salesorderproductsrel');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
    	//'vtiger_crmentity' => 'crmid',
        'vtiger_salesorderproductsrel'   => 'salesorderproductsrelid',);

    /**
     * Mandatory for Listing (Related listview)
     */
    var $list_fields = Array (
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
    	'Productid'=> Array('salesorderproductsrel', 'productid'),
    	'Productcombo Id'=> Array('salesorderproductsrel', 'productcomboid'),
    	'Producttype'=> Array('salesorderproductsrel', 'producttype'),	
    	'Marketprice'=> Array('salesorderproductsrel', 'marketprice'),
    	'Salesorderproductsrel Status'=> Array('salesorderproductsrel', 'salesorderproductsrelstatus'),
    	'Schedule'=>Array('salesorderproductsrel','schedule'),
    	'Salesorder Id'=>Array('salesorderproductsrel', 'salesorderid'),

    );
    var $list_fields_name = Array(
        /* Format: Field Label => fieldname */
        'Productid'=> 'productid',
    	'Productcombo Id'=> 'productcomboid',
    	'Producttype'=>'producttype',
    	'Marketprice'=> 'marketprice',
    	'Salesorderproductsrel Status'=> 'salesorderproductsrelstatus',
    	'Schedule'=>'schedule',
    	'Salesorder Id'=>'salesorderid',
        
    );

    // Make the field link to detail view from list view (Fieldname)
    var $list_link_field = 'relmodule';

    // For Popup listview and UI type support
    var $search_fields = Array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
    	'Salesorderproductsrelname'=> Array('salesorderproductsrel', 'salesorderproductsrelname'),
        'Productid'=> Array('salesorderproductsrel', 'productid'),
        'Producttype'=> Array('salesorderproductsrel', 'producttype'),
        'Marketprice'=> Array('salesorderproductsrel', 'marketprice'),
        'Schedule'=>Array('salesorderproductsrel','schedule')
    );
    var $search_fields_name = Array(
        /* Format: Field Label => fieldname */
    	'Salesorderproductsrelname'=> 'salesorderproductsrelname',
        'Productid'=> 'productid',
        'Producttype'=>'producttype',
        'Marketprice'=> 'marketprice',
        'Schedule'=>'schedule'
    );

    // For Popup window record selection
    var $popup_fields = Array('salesorderproductsrelname');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'salesorderproductsrelid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'salesorderproductsrelid';

    // Required Information for enabling Import feature
    var $required_fields = Array('salesorderproductsrelid'=>1);

    // Callback function list during Importing
    var $special_functions = Array('set_import_assigned_user');

    var $default_order_by = 'productid';
    var $default_sort_order='ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('salesorderproductsrelid');

    function __construct() {
        global $log, $currentModule;
        $this->column_fields = getColumnFields(get_class($this));
        $this->db = PearDatabase::getInstance();
        $this->log = $log;
    }

    /**
     * 主表信息保存后处理
     * @param unknown $module
     */
   function save_module($module) {
   		//获取登录用户信息
// 	   	$currentUser = Users_Record_Model::getCurrentUserModel();
// 	   	$userid = $currentUser->get('id');
		
// 	   	//查询合同产品信息
// 	   	$sql="select * from vtiger_salesorderproductsrel where salesorderproductsrelid=?";
// 	   	$result=$this->db->pquery($sql,array($this->id));
// 	   	//节点状态
// 	   	$nodestatus=$this->db->query_result($result,0,'nodestatus');
	   	
// 	   	//更新成本
// 	   	if ($nodestatus == '1'){
// 		   	$update_query = "update vtiger_salesorderproductsrel set costing=?,realcosting=?,isvisible=0 where salesorderproductsrelid=?";
// 		   	$update_params = array($_REQUEST['costing'],$_REQUEST['realcosting'],$this->id);
// 		   	$this->db->pquery($update_query, $update_params);
// 	   	}
    }

}
?>
