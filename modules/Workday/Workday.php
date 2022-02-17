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

class Workday extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_workday';
    var $table_index= 'workdayid';
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
    var $tab_name = Array('vtiger_workday');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
        'vtiger_workday'   => 'workdayid',);

    /**
     * Mandatory for Listing (Related listview)
     */
    var $list_fields = Array (
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Date Day'=> Array('workday', 'dateday'),
        'Date Type'=> Array('workday', 'datetype')

    );
    var $list_fields_name = Array(
        /* Format: Field Label => fieldname */
       'Date Day'=> 'dateday',
        'Date Type'=> 'datetype'
    );

    // Make the field link to detail view from list view (Fieldname)
    var $list_link_field = 'dateday';

    // For Popup listview and UI type support
    var $search_fields = Array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Date Day'=> Array('workday', 'dateday'),
        'Date Type'=> Array('workday', 'datetype')
    );
    var $search_fields_name = Array(
        /* Format: Field Label => fieldname */
        'Date Day'=> 'dateday',
        'Date Type'=> 'datetype'
    );

    // For Popup window record selection
    var $popup_fields = Array('dateday');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'dateday';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'dateday';

    // Required Information for enabling Import feature
    var $required_fields = Array('dateday'=>1);

    // Callback function list during Importing
    var $special_functions = Array('dateday');

    var $default_order_by = 'dateday';
    var $default_sort_order='ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('dateday');


    function save_module($module) {
    }
    function Workday() {
    	//$this->log =LoggerManager::getLogger('workday');
    	//$this->db = PearDatabase::getInstance();
    	//$this->column_fields = getColumnFields('Workday');
    }
}
?>
