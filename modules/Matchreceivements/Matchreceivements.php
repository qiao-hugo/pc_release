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

class Matchreceivements extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity
    var $table_name = 'vtiger_servicecomments';
    var $table_index= 'servicecommentsid';
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
    var $tab_name = Array('vtiger_servicecomments');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
        'vtiger_servicecomments'   => 'servicecommentsid',);

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
    var $popup_fields = Array('servicecommentsid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'servicecommentsid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'servicecommentsid';

    // Required Information for enabling Import feature
    var $required_fields = Array('servicecommentsid'=>1);

    // Callback function list during Importing
    var $special_functions = Array('servicecommentsid');

    var $default_order_by = 'servicecommentsid';
    var $default_sort_order='ASC';
  
    var $mandatory_fields = Array('servicecommentsid');

    function __construct() {
        global $log, $currentModule;
        //$this->column_fields = getColumnFields(get_class($this));
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
