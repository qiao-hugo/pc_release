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

class ReceivedPaymentsCollate extends CRMEntity
{
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_receivedpaymentscollate';
    var $table_index = 'receivedpaymentscollateid';
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
    var $tab_name = Array('vtiger_crmentity', 'vtiger_receivedpaymentscollate');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_receivedpaymentscollate' => 'receivedpaymentscollateid',
    );

    /**
     * Mandatory for Listing (Related listview)
     */
    var $list_fields = Array();


    var $list_fields_name = Array();

    // Make the field link to detail view from list view (Fieldname)
    var $list_link_field = 'relmodule';

    // For Popup listview and UI type support 弹出框的列表字段
    var $search_fields = Array();
    var $search_fields_name = Array();

    // For Popup window record selection
    var $popup_fields = Array('subject');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'subject';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'subject';

    // Required Information for enabling Import feature
    var $required_fields = Array('assigned_user_id' => 1);

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('subject', 'assigned_user_id');

    // Callback function list during Importing
    var $special_functions = Array('set_import_assigned_user');

    var $default_order_by = 'subject';
    var $default_sort_order = 'ASC';

    function __construct()
    {
        global $log, $currentModule;
        $this->column_fields = getColumnFields(get_class($this));
        $this->db = PearDatabase::getInstance();
        $this->log = $log;
    }

    function save_module($module)
    {
        if ($_REQUEST['receivedpaymentsid']) {
            $recordModel=ReceivedPayments_Record_Model::getInstanceById($_REQUEST['receivedpaymentsid'],'ReceivedPayments');
            $entity = $recordModel->entity->column_fields;
            $this->db->pquery("update vtiger_receivedpaymentscollate set receivedpaymentsid=?,oldpaymentchannel=?,oldowncompany=?,oldreality_date=?,oldpaymentcode=? where receivedpaymentscollateid=?",
                array($_REQUEST['receivedpaymentsid'],$entity['paymentchannel'], $entity['owncompany'], $entity['reality_date'], $entity['paymentcode'], $this->id));

        }
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
        parent::makeWorkflows($modulename, $workflowsid, $salesorderid, $isedit = '');
        $query = "UPDATE vtiger_salesorderworkflowstages,
				 vtiger_receivedpaymentscollate
				SET vtiger_salesorderworkflowstages.modulestatus='p_process',
				    vtiger_receivedpaymentscollate.modulestatus='b_check'
				WHERE vtiger_receivedpaymentscollate.receivedpaymentscollateid=vtiger_salesorderworkflowstages.salesorderid
				AND vtiger_salesorderworkflowstages.salesorderid=?  AND  vtiger_salesorderworkflowstages.workflowsid=? ";
        $this->db->pquery($query, array($salesorderid, $workflowsid));

        //新建时 消息提醒第一审核人进行审核
        $object = new SalesorderWorkflowStages_SaveAjax_Action();
        $object->sendWxRemind(array('salesorderid' => $salesorderid, 'salesorderworkflowstagesid' => 0));
    }

    /**
     * 审核打回中处理
     * @param Vtiger_Request $request
     */
    public function backallAfter(Vtiger_Request $request)
    {
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
                AND vtiger_salesorderworkflowstages.modulename = 'ReceivedPaymentsCollate'";
        $result = $this->db->pquery($query, array($stagerecordid));

        $currentflag = $this->db->query_result($result, 0, 'workflowstagesflag');
        $workflowsid = $this->db->query_result($result, 0, 'workflowsid');
        $recordModel = Vtiger_Record_Model::getInstanceById($record, 'ReceivedPaymentsCollate');
        $entity = $recordModel->entity->column_fields;
        $this->db->pquery("DELETE FROM `vtiger_salesorderworkflowstages` WHERE vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename='ReceivedPaymentsCollate' AND vtiger_salesorderworkflowstages.workflowsid=?",array($record,$workflowsid));

        $this->db->pquery("update vtiger_receivedpaymentscollate set modulestatus=? where receivedpaymentscollateid=?", array('a_normal', $record));
    }

    function workflowcheckafter(Vtiger_Request $request)
    {
        $stagerecordid = $request->get('stagerecordid');
        $record = $request->get('record');

        $query = "SELECT
                    vtiger_workflowstages.workflowstagesflag,
                    vtiger_salesorderworkflowstages.workflowsid
                FROM
                    `vtiger_salesorderworkflowstages`
                LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid
                WHERE
                    vtiger_salesorderworkflowstages.salesorderworkflowstagesid = ?
                AND vtiger_salesorderworkflowstages.modulename = 'ReceivedPaymentsCollate'";
        $result = $this->db->pquery($query, array($stagerecordid));
        $currentflag = $this->db->query_result($result, 0, 'workflowstagesflag');
        $workflowsid = $this->db->query_result($result, 0, 'workflowsid');
        $recordModel = Vtiger_Record_Model::getInstanceById($record, 'ReceivedPaymentsCollate', TRUE);
        $entity = $recordModel->entity->column_fields;
        $currentflag = trim($currentflag);
        $datetime = date('Y-m-d H:i:s');

        if ($currentflag == 'CAIWUYUNYING') {
            $this->db->pquery("update vtiger_receivedpaymentscollate,vtiger_receivedpayments set vtiger_receivedpaymentscollate.modulestatus=?,
                                                                  vtiger_receivedpayments.paymentcode=?,vtiger_receivedpayments.reality_date=?,
                                                                  vtiger_receivedpayments.owncompany=?,vtiger_receivedpayments.paymentchannel=? 
where vtiger_receivedpaymentscollate.receivedpaymentsid= vtiger_receivedpayments.receivedpaymentsid and vtiger_receivedpaymentscollate.receivedpaymentscollateid=?",
                array('c_complete', $entity['paymentcode'], $entity['reality_date'], $entity['owncompany'], $entity['paymentchannel'], $record));
        }

        $params['salesorderid'] = $request->get('record');
        $params['workflowsid'] = $workflowsid;
        $this->hasAllAuditorsChecked($params);
    }

    function retrieve_entity_info($record, $module)
    {
        if($_REQUEST['view']=='Edit'&&!$_REQUEST['record'])
        {
            //审核的过来的不验证权限
            return true;
        }
        parent::retrieve_entity_info($record, $module);

    }

}

?>
