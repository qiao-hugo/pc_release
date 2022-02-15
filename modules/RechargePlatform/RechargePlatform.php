<?php
include_once('config.php');
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');
require_once('include/utils/utils.php');
require_once('user_privileges/default_module_view.php');

class RechargePlatform extends CRMEntity {
    var $log;
    var $db;
    var $table_name = "vtiger_topplatform";
    var $table_index= 'topplatformid';
    var $tab_name = Array('vtiger_crmentity','vtiger_topplatform');
    var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_topplatform'=>'topplatformid');
    var $entity_table = "vtiger_crmentity";

    /** Indicator if this is a custom module or standard module */
    var $IsCustomModule = true;

    /**
     * Mandatory table for supporting custom fields.
     */
    var $customFieldTable = Array();

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
    var $popup_fields = Array('topplatformid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'topplatformid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'topplatformid';

    // Required Information for enabling Import feature
    var $required_fields = Array('topplatformid'=>1);

    // Callback function list during Importing
    var $special_functions = Array('topplatformid');

    var $default_order_by = 'topplatformid';
    var $default_sort_order='DESC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('topplatformid');

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

    /**
     * Function returns the column alias for a field
     * @param <Array> $fieldinfo - field information
     * @return <String> field value
     */
    protected function createColumnAliasForField($fieldinfo) {
    	return strtolower($fieldinfo['fieldname']);
    }
    

}
?>
