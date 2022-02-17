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

class SalesorderProjectTasksrel extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_salesorderprojecttasksrel';
    var $table_index= 'salesorderprojecttasksrelid';
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
    var $tab_name = Array('vtiger_crmentity', 'vtiger_salesorderprojecttasksrel');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
    	'vtiger_crmentity' => 'crmid',
        'vtiger_salesorderprojecttasksrel'   => 'salesorderprojecttasksrelid',);

    /**
     * Mandatory for Listing (Related listview)
     */
    var $list_fields = Array (
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
    	'Salesorderid'=> Array('salesorderprojecttasksrel', 'salesorderid'),
    	'Projectid'=> Array('salesorderprojecttasksrel', 'projectid'),
    	'Starttime'=>Array('salesorderprojecttasksrel','starttime'),
    	'Endtime'=>Array('salesorderprojecttasksrel','endtime'),
    	'Ownerid'=>Array('salesorderprojecttasksrel','ownerid'),
    	'Receivedpaymentsid'=>Array('salesorderprojecttasksrel','receivedpaymentsid'),
    	'Salesorderprojecttaskstatus'=>Array('salesorderprojecttasksrel','salesorderprojecttaskstatus'),
    	'Salesorderprojecttaskname'=> Array('salesorderprojecttasksrel', 'salesorderprojecttaskname'),
    );
    var $list_fields_name = Array(
        /* Format: Field Label => fieldname */
    		'Salesorderid'=> 'salesorderid',
    		'Projectid'=>'projectid',
    		'Starttime'=>'starttime',
    		'Endtime'=>'endtime',
    		'Ownerid'=>'ownerid',
    		'Receivedpaymentsid'=>'receivedpaymentsid',
    		'Salesorderprojecttaskstatus'=>'salesorderprojecttaskstatus',
    		'Salesorderprojecttaskname'=>'salesorderprojecttaskname',
    );

    // Make the field link to detail view from list view (Fieldname)
    var $list_link_field = 'relmodule';

    // For Popup listview and UI type support
    var $search_fields = Array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Salesorderprojecttaskname'=> Array('salesorderprojecttasksrel', 'salesorderprojecttaskname'),
        'Salesorderid'=> Array('salesorderprojecttasksrel', 'salesorderid'),
        'Projectid'=> Array('salesorderprojecttasksrel', 'projectid'),
        'Receivedpaymentsid'=>Array('salesorderprojecttasksrel','receivedpaymentsid')
    );
    var $search_fields_name = Array(
        /* Format: Field Label => fieldname */
        'Salesorderprojecttaskname'=>  'salesorderprojecttaskname',
        'Salesorderid'=> 'salesorderid',
        'Projectid'=> 'projectid',
        'Receivedpaymentsid'=>'receivedpaymentsid'
    );

    // For Popup window record selection
    var $popup_fields = Array('salesorderprojecttaskname');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'salesorderprojecttaskname';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'salesorderprojecttaskname';

    // Required Information for enabling Import feature
    var $required_fields = Array('salesorderprojecttaskname'=>1);

    // Callback function list during Importing
    var $special_functions = Array('set_import_assigned_user');

    var $default_order_by = 'salesorderprojecttaskname';
    var $default_sort_order='ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('productid');

    function __construct() {
        global $log, $currentModule;
        $this->column_fields = getColumnFields(get_class($this));
        $this->db = PearDatabase::getInstance();
        $this->log = $log;
    }

   function save_module($module) {
   		//更新活动时间
   		$update_query = "update vtiger_salesorderprojecttasksrel set actiontime=SYSDATE() where salesorderprojecttasksrelid=?";
   		$update_params = array($this->id);
   		$this->db->pquery($update_query, $update_params);
    }

}
?>
