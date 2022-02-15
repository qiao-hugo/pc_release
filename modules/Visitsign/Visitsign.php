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

class Visitsign extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_visitsign';
    var $table_index= 'visitingorderid';
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
    var $tab_name = Array('vtiger_visitsign','vtiger_visitingorder');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
        'vtiger_visitsign'   =>'visitingorderid',
        'vtiger_visitingorder'=>'visitingorderid');

    /**
     * Mandatory for Listing (Related listview)
     */
    var $list_fields = Array (
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
    	//'Subject'=> Array('visitingorder', 'subject'),

    );
    var $list_fields_name = Array(
        /* Format: Field Label => fieldname */
    	//'Subject'=> 'subject',
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
    var $popup_fields = Array('visitsignid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'visitsignid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'visitsignid';

    // Required Information for enabling Import feature
    var $required_fields = Array('visitsignid'=>1);

    // Callback function list during Importing
    var $special_functions = Array('visitsignid');

    var $default_order_by = 'visitsignid';
    var $default_sort_order='ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('visitsignid');

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
   		
    }
   
}
?>
