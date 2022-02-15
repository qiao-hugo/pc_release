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

class Suppcontractsextension extends CRMEntity
{
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_suppcontractsextension';
    var $table_index = 'suppcontractsextensionid';
    var $tab_name = Array('vtiger_crmentity', 'vtiger_suppcontractsextension');
    var $tab_name_index = Array('vtiger_crmentity' => 'crmid', 'vtiger_suppcontractsextension' => 'suppcontractsextensionid');
    var $column_fields = Array();

    /** Indicator if this is a custom module or standard module */
    var $IsCustomModule = true;

    /**
     * Mandatory table for supporting custom fields.
     */
    var $customFieldTable = Array();


    /**
     * Mandatory for Listing (Related listview)
     */
    var $list_fields = Array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'


    );
    var $list_fields_name = Array(/* Format: Field Label => fieldname */

    );

    // Make the field link to detail view from list view (Fieldname)
    var $list_link_field = 'relmodule';

    // For Popup listview and UI type support
    var $search_fields = Array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'

    );
    var $search_fields_name = Array(/* Format: Field Label => fieldname */

    );

    // For Popup window record selection
    var $popup_fields = Array('suppcontractsextensionid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'suppcontractsextensionid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'suppcontractsextensionid';

    // Required Information for enabling Import feature
    var $required_fields = Array('suppcontractsextensionid' => 1);

    // Callback function list during Importing
    var $special_functions = Array('suppcontractsextensionid');

    var $default_order_by = 'suppcontractsextensionid';
    var $default_sort_order = 'DESC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('suppcontractsextensionid');

    function __construct()
    {
        global $log, $currentModule;
        $this->column_fields = getColumnFields(get_class($this));
        $this->db = PearDatabase::getInstance();
        $this->log = $log;
    }

    /**
     * 更新后处理
     * @param unknown $module
     */
    function save_module($module)
    {

    }



    /**
     * @审核工作流程后置触发
     * @param Vtiger_Request $request
     */
    function workflowcheckafter(Vtiger_Request $request)
    {
        $recordid = $request->get('record');
        $recordModel = Vtiger_Record_Model::getInstanceById($recordid, 'Suppcontractsextension');
        $entity = $recordModel->entity->column_fields;
        if ($entity['suppliercontractsid']) {
            //$reportsModel = Users_Privileges_Model::getInstanceById($entity['assigned_user_id']);
            //$reportsModel=Users_Privileges_Model::getInstanceById($reportsModel->reports_to_id);
            global $current_user;
            $db = PearDatabase::getInstance();
            $sql = "UPDATE vtiger_suppliercontracts SET delayuserid=?,confirmlasttime='" . date('Y-m-d H:i:s') . "' WHERE suppliercontractsid=?";
            $db->pquery($sql, array($current_user->id, $entity['suppliercontractsid']));
        }
    }
}
?>
