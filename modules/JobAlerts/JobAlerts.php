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

class JobAlerts extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_jobalerts';
    var $table_index= 'jobalertsid';
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
    var $tab_name = Array('vtiger_jobalerts');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
        'vtiger_jobalerts'   => 'jobalertsid',);

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
    var $popup_fields = Array('jobalertsid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'jobalertsid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'jobalertsid';

    // Required Information for enabling Import feature
    var $required_fields = Array('jobalertsid'=>1);

    // Callback function list during Importing
    var $special_functions = Array('jobalertsid');

    var $default_order_by = 'jobalertsid';
    var $default_sort_order='ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('jobalertsid');

    function __construct() {
        global $log, $currentModule;
        $this->column_fields = getColumnFields(get_class($this));
        $this->db = PearDatabase::getInstance();
        $this->log = $log;
    }
    
    /**
     * ???????????????
     * @param unknown $module
     */
    function save_module($module) {
    	global $current_user;
    	//??????????????????
    	$insertion_mode=$this->mode;
    	if ($insertion_mode == 'edit') {
    		$update_query = "update vtiger_jobalerts set modifiedby=?,modifiedtime=sysdate() where jobalertsid=?";
    	}else{
    		$update_query = "update vtiger_jobalerts set creatorid=?,createdtime=sysdate() where jobalertsid=?";
    	}
    	$this->db->pquery($update_query, array($current_user->id,$this->id));
    	
    	$arrAlertid=$_REQUEST['alertid'];
    	if (!empty($arrAlertid)){
	    	//??????????????????(?????????????????????)
	    	$delete_query = "delete from vtiger_jobalertsreminder where jobalertsid=?";
	    	$this->db->pquery($delete_query, array($this->id));
	    	foreach($arrAlertid as $alertid) {
		    	$insert_query = "insert into vtiger_jobalertsreminder(jobalertsid,alertid)values(?,?)";
		    	$this->db->pquery($insert_query, array($this->id,$alertid));
	    	}
    	}
    }
}
?>