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

class RefundTimeoutAudit extends CRMEntity
{
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_refundtimeoutaudit';
    var $table_index = 'refundtimeoutauditid';
    var $tab_name = Array('vtiger_crmentity', 'vtiger_refundtimeoutaudit');
    var $tab_name_index = Array('vtiger_crmentity' => 'crmid', 'vtiger_refundtimeoutaudit' => 'refundtimeoutauditid');
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
    var $popup_fields = Array('refundtimeoutauditid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'refundtimeoutauditid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'refundtimeoutauditid';

    // Required Information for enabling Import feature
    var $required_fields = Array('refundtimeoutauditid' => 1);

    // Callback function list during Importing
    var $special_functions = Array('refundtimeoutauditid');

    var $default_order_by = 'refundtimeoutauditid';
    var $default_sort_order = 'DESC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('refundtimeoutauditid');

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
        $stagerecordid=$request->get('stagerecordid');
        $recordModel = Vtiger_Record_Model::getInstanceById($recordid, 'RefundTimeoutAudit');
        $entity = $recordModel->entity->column_fields;
        /*$query="SELECT
                    vtiger_workflowstages.workflowstagesflag
                FROM
                    `vtiger_salesorderworkflowstages`
                LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid
                WHERE
                    vtiger_salesorderworkflowstages.salesorderworkflowstagesid = ?
                AND vtiger_salesorderworkflowstages.modulename = 'RefundTimeoutAudit'";
        $result=$this->db->pquery($query,array($stagerecordid));
        $currentflag=$this->db->query_result($result,0,'workflowstagesflag');
        $currentflag=trim($currentflag);
        $datetime=date('Y-m-d H:i:s');
        switch($currentflag) {
            case 'AUDIT_VERIFICATION':
                $this->AuditAuditNodeJump($recordid,$entity['workflowsid'],$entity['assigned_user_id'],'ContractsAgreement','ContractGuarantee',1);
                break;
            case 'TWO_VERIFICATION':
                $this->AuditAuditNodeJump($recordid,$entity['workflowsid'],$entity['assigned_user_id'],'ContractsAgreement','ContractGuarantee',2);
                break;
            default:

        }*/
        if ($entity['modulestatus'] == 'c_complete') {
            $sql = "UPDATE vtiger_receivedpayments SET receivedstatus='normal' WHERE receivedpaymentsid=?";
            $this->db->pquery($sql, array($entity['receivedpaymentsid']));
        }
        // cxh 2019-08-02 添加 如果该审核需要修改审核列表中的modulestatus（审核流程状态）审核完后走下面代码
        $query="SELECT
		      vtiger_salesorderworkflowstages.workflowsid
		      FROM
		      `vtiger_salesorderworkflowstages`
		      WHERE
	         vtiger_salesorderworkflowstages.salesorderworkflowstagesid = ? ";
        $result=$this->db->pquery($query,array($stagerecordid));
        $workflowsid=$this->db->query_result($result,0,'workflowsid');
        $params['workflowsid']=$workflowsid;
        $params['salesorderid']=$request->get('record');
        $this->hasAllAuditorsChecked($params);

    }

    /**
     * 重写工作流生成
     * @param unknown $modulename
     * @param unknown $workflowsid
     * @param unknown $salesorderid
     * @param string $isedit
     */
    public function makeWorkflows($modulename,$workflowsid,$salesorderid,$isedit=true){
        parent::makeWorkflows($modulename,$workflowsid,$salesorderid,$isedit);
        $query=" UPDATE vtiger_salesorderworkflowstages,
				 vtiger_refundtimeoutaudit
				 SET  vtiger_salesorderworkflowstages.accountid=vtiger_refundtimeoutaudit.accountid,
				      vtiger_salesorderworkflowstages.salesorder_nono=vtiger_refundtimeoutaudit.paytitle,
				     vtiger_salesorderworkflowstages.modulestatus='p_process'
				 WHERE vtiger_refundtimeoutaudit.refundtimeoutauditid=vtiger_salesorderworkflowstages.salesorderid
				 AND vtiger_salesorderworkflowstages.salesorderid=?  AND  vtiger_salesorderworkflowstages.workflowsid=?  ";
        $this->db->pquery($query,array($salesorderid,$workflowsid));
        //新建时 消息提醒第一审核人进行审核
        $object = new SalesorderWorkflowStages_SaveAjax_Action();
        $object->sendWxRemind(array('salesorderid'=>$salesorderid,'salesorderworkflowstagesid'=>0));
    }
    public function backallBefore(Vtiger_Request $request){
        $resultaa['success'] = 'false';
        $resultaa['error']['message'] = "： 导入生成不允许打回。";
        //若果是移动端请求则走这个返回
        if( $request->get('isMobileCheck')==1){
            return $resultaa;
        }else{
            echo json_encode($resultaa);
            exit;
        }
    }
}
?>
