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

class InputInvoice extends CRMEntity
{
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_input_invoice';
    var $table_index = 'inputinvoiceid';
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
    var $tab_name = Array('vtiger_crmentity','vtiger_input_invoice');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
        'vtiger_crmentity' =>'crmid',
        'vtiger_input_invoice' => 'inputinvoiceid',
    );
    var $entity_table = "vtiger_crmentity";

    /**
     * Mandatory for Listing (Related listview)
     */
    var $list_fields = Array();
    var $list_fields_name = Array(
        /* Format: Field Label => fieldname */
        //'Subject'=> 'subject',
    );


    // For Popup listview and UI type support
    var $search_fields = Array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'

    );
    var $search_fields_name = Array(/* Format: Field Label => fieldname */

    );

    // For Popup window record selection
    var $popup_fields = Array();

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = '';

    // Column value to use on detail view record text display
    var $def_detailview_recname = '';

    // Required Information for enabling Import feature
    var $required_fields = Array();

    // Callback function list during Importing
    var $special_functions = Array();

    var $default_order_by = 'inputinvoiceid';
    var $default_sort_order = 'DESC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array();

    function __construct()
    {
        global $log, $currentModule;
        $this->column_fields = getColumnFields(get_class($this));
        $this->db = PearDatabase::getInstance();
        $this->log = $log;
    }

    /** Function to handle module specific operations when saving a entity
     */
    function save_module($module) {

    }

    /*Function to create records in current module.
**This function called while importing records to this module*/
    function createRecords($obj)
    {
        $createRecords = createRecords($obj);
        return $createRecords;
    }


    /**
     * 重写工作流生成
     * @param unknown $modulename
     * @param unknown $workflowsid
     * @param unknown $salesorderid
     * @param string $isedit
     */
    public function makeWorkflows($modulename, $workflowsid, $salesorderid, $isedit = '')
    {
        parent::makeWorkflows($modulename, $workflowsid, $salesorderid, true);
        $query = "UPDATE vtiger_salesorderworkflowstages,
				 vtiger_input_invoice
				SET vtiger_salesorderworkflowstages.modulestatus='p_process',
				    vtiger_input_invoice.modulestatus='b_check',
				    vtiger_input_invoice.workflowsnode=vtiger_salesorderworkflowstages.workflowstagesname
				WHERE vtiger_input_invoice.inputinvoiceid=vtiger_salesorderworkflowstages.salesorderid
				AND vtiger_salesorderworkflowstages.salesorderid=? AND  vtiger_salesorderworkflowstages.workflowsid=? ";
        $this->db->pquery($query, array($salesorderid, $workflowsid));
        //新建时 消息提醒第一审核人进行审核
        $object = new SalesorderWorkflowStages_SaveAjax_Action();
        $object->sendWxRemind(array('salesorderid' => $salesorderid, 'salesorderworkflowstagesid' => 0));
    }



    /**
     * 审核打回中处理
     * @param Vtiger_Request $request
     */
    public function backallAfter(Vtiger_Request $request) {
        $stagerecordid = $request->get('isrejectid');
        $record = $request->get('record');
        $query = "SELECT
                    vtiger_workflowstages.workflowstagesflag,
                    vtiger_workflowstages.workflowsid
                FROM
                    `vtiger_salesorderworkflowstages`
                LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid
                WHERE
                    vtiger_salesorderworkflowstages.salesorderworkflowstagesid = ?
                AND vtiger_salesorderworkflowstages.modulename = 'InputInvoice'";
        $result = $this->db->pquery($query, array($stagerecordid));

        $currentflag = $this->db->query_result($result, 0, 'workflowstagesflag');
        $workflowsid = $this->db->query_result($result, 0, 'workflowsid');

        $this->db->pquery("DELETE FROM `vtiger_salesorderworkflowstages` WHERE vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename='InputInvoice' AND vtiger_salesorderworkflowstages.workflowsid=?", array($record, $workflowsid));
        $this->db->pquery("UPDATE vtiger_input_invoice SET workflowsnode='打回中',modulestatus='a_exception' WHERE inputinvoiceid=?", array($record));

    }
}

?>
