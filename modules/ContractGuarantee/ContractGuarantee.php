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

class ContractGuarantee extends CRMEntity
{
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_contractguarantee';
    var $table_index = 'contractguaranteeid';
    var $tab_name = Array('vtiger_crmentity', 'vtiger_contractguarantee');
    var $tab_name_index = Array('vtiger_crmentity' => 'crmid', 'vtiger_contractguarantee' => 'contractguaranteeid');
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
    var $popup_fields = Array('contractguaranteeid');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'contractguaranteeid';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'contractguaranteeid';

    // Required Information for enabling Import feature
    var $required_fields = Array('contractguaranteeid' => 1);

    // Callback function list during Importing
    var $special_functions = Array('contractguaranteeid');

    var $default_order_by = 'contractguaranteeid';
    var $default_sort_order = 'DESC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('contractguaranteeid');

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
        $contractid=$_REQUEST['contractid'];
        $id=$_REQUEST['record']>0?$_REQUEST['record']:$this->id;
        if($contractid) {
            $query='SELECT * FROM vtiger_crmentity WHERE deleted=0 AND crmid=?';
            $dataResult=$this->db->pquery($query,array($contractid));
            $data=$this->db->raw_query_result_rowdata($dataResult,0);
            $moduleName=$data['setype'];
            $recordModel = Vtiger_Record_Model::getInstanceById($contractid, $moduleName);
            if($moduleName=='ServiceContracts'){
                $accountid=$recordModel->get('sc_related_to');
            }else{
                $accountid=$recordModel->get('vendorid');
            }
            $this->db->pquery('UPDATE vtiger_contractguarantee SET accountname=(SELECT vtiger_crmentity.label FROM vtiger_crmentity WHERE vtiger_crmentity.crmid=? limit 1),accountid=?,modulename=?,modulestatus=\'b_actioning\' WHERE contractguaranteeid=?',array($accountid,$accountid,$moduleName,$id));
            //$this->db->pquery('UPDATE vtiger_contractguarantee SET accountname=?,modulename=?,modulestatus=\'b_actioning\' WHERE contractguaranteeid=?',array($accountid,$moduleName,$id));
        }
    }



    /**
     * @审核工作流程后置触发
     * @param Vtiger_Request $request
     */
    function workflowcheckafter(Vtiger_Request $request)
    {
        $recordid = $request->get('record');
        $recordModel = Vtiger_Record_Model::getInstanceById($recordid, 'ContractGuarantee');
        $entity = $recordModel->entity->column_fields;
        if ($entity['contractid'] > 0 && $entity['modulestatus'] == 'c_complete') {
            global $current_user;
            $tableName='vtiger_servicecontracts';
            $tableid='servicecontractsid';
            if($entity['modulename']=='SupplierContracts'){
                $tableName='vtiger_suppliercontracts';
                $tableid='suppliercontractsid';
            }
            $sql = "UPDATE {$tableName} SET isguarantee=1 WHERE {$tableid}=?";
            $this->db->pquery($sql, array($entity['contractid']));
        }
        // cxh 2019-08-02 添加 如果该审核需要修改审核列表中的modulestatus（审核流程状态）审核完后走下面代码
        $stagerecordid=$request->get('stagerecordid');
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
    function workflowcheckbefore(Vtiger_Request $request){

        $recordid=$request->get('record');
        $query="SELECT sequence FROM vtiger_salesorderworkflowstages WHERE isaction=1 AND salesorderid=? AND vtiger_salesorderworkflowstages.modulename='ContractGuarantee'";
        $result=$this->db->pquery($query,array($recordid));
        $data=$this->db->raw_query_result_rowdata($result,0);
        $date=date('Y-m-d H:i:s');
        global $current_user;
        if($data['sequence']==1){
            $sqlstr="oneguaranteedate='{$date}',oneconfirm=".$current_user->id;
        }else{
            $sqlstr="guaranteedate='{$date}',twoconfirm=".$current_user->id;
        }
        $sql="UPDATE `vtiger_contractguarantee` SET {$sqlstr} WHERE contractguaranteeid=?";
        $this->db->pquery($sql, array($recordid));

    }
    /**
     * 重写工作流生成
     * @param unknown $modulename
     * @param unknown $workflowsid
     * @param unknown $salesorderid
     * @param string $isedit
     */
    public function makeWorkflows($modulename,$workflowsid,$salesorderid,$isedit=true){
        global $isallow;//移动端过来不加载,手动创建一个
        $isallow=empty($isallow)?array($modulename):$isallow;
        parent::makeWorkflows($modulename,$workflowsid,$salesorderid,$isedit);
        //新增判断是什么服务合同 cxh 新增开始
        $contractid=$_REQUEST['contractid'];
        $query='SELECT * FROM vtiger_crmentity WHERE deleted=0 AND crmid=?';
        $dataResult=$this->db->pquery($query,array($contractid));
        $data=$this->db->raw_query_result_rowdata($dataResult,0);
        $moduleName=$data['setype'];
        if($moduleName=='ServiceContracts'){
            $query='SELECT * FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_servicecontracts.servicecontractsid  WHERE vtiger_crmentity.deleted=0 AND vtiger_servicecontracts.servicecontractsid=?  ';
            $dataResult=$this->db->pquery($query,array($contractid));
            $data=$this->db->raw_query_result_rowdata($dataResult,0);
            $contract_no =$data['contract_no'];
        }else{
            $query='SELECT * FROM vtiger_suppliercontracts LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_suppliercontracts.suppliercontractsid  WHERE vtiger_crmentity.deleted=0 AND vtiger_suppliercontracts.suppliercontractsid=? ';
            $dataResult=$this->db->pquery($query,array($contractid));
            $data=$this->db->raw_query_result_rowdata($dataResult,0);
            $contract_no =$data['contract_no'];
        }
        /* file_put_contents('1.txt',$contractid); */
        //新增end
        $query="UPDATE vtiger_salesorderworkflowstages,
				 vtiger_contractguarantee
				SET vtiger_salesorderworkflowstages.accountid=vtiger_contractguarantee.accountid,vtiger_salesorderworkflowstages.salesorder_nono= ?,
				  vtiger_salesorderworkflowstages.modulestatus='p_process',
				  vtiger_salesorderworkflowstages.accountname=(SELECT vtiger_crmentity.label FROM vtiger_crmentity WHERE vtiger_crmentity.crmid=vtiger_contractguarantee.accountid)
				WHERE vtiger_contractguarantee.contractguaranteeid=vtiger_salesorderworkflowstages.salesorderid
				AND vtiger_salesorderworkflowstages.salesorderid=?  AND vtiger_salesorderworkflowstages.workflowsid=? ";
        $this->db->pquery($query,array($contract_no,$salesorderid,$workflowsid));
        global $current_user;
        //$db=PearDatabase::getInstance();
        //$departmentid=empty($current_user->departmentid)?'H1':$current_user->departmentid;
        $departmentid=$_SESSION['userdepartmentid'];
        $this->setAudituid("ContractsAgreement",$departmentid,$salesorderid,$workflowsid);
        //新建时 消息提醒第一审核人进行审核
        $object = new SalesorderWorkflowStages_SaveAjax_Action();
        $object->sendWxRemind(array('salesorderid'=>$salesorderid,'salesorderworkflowstagesid'=>0));
        /*$data=$this->getAudituid('ContractsAgreement',$departmentid);
        $db->pquery("UPDATE vtiger_salesorderworkflowstages SET ishigher=1,higherid=? WHERE vtiger_salesorderworkflowstages.sequence=1 AND vtiger_salesorderworkflowstages.salesorderid=?  AND vtiger_salesorderworkflowstages.modulename='ContractGuarantee'",array($data['oneaudituid'],$salesorderid));
        if($data['oneaudituid']==$data['towaudituid']){
            $db->pquery("DELETE FROM vtiger_salesorderworkflowstages  WHERE vtiger_salesorderworkflowstages.sequence=2 AND vtiger_salesorderworkflowstages.salesorderid=?  AND vtiger_salesorderworkflowstages.modulename='ContractGuarantee'",array($salesorderid));
        }else{
            $db->pquery("UPDATE vtiger_salesorderworkflowstages SET ishigher=1,higherid=? WHERE vtiger_salesorderworkflowstages.sequence=2 AND vtiger_salesorderworkflowstages.salesorderid=?  AND vtiger_salesorderworkflowstages.modulename='ContractGuarantee'",array($data['towaudituid'],$salesorderid));
        }*/
    }
    //合同担保申请单打回后去除表中数据
    public function backallAfter(Vtiger_Request $request){
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
                AND vtiger_salesorderworkflowstages.modulename = 'ContractGuarantee'";
        $result = $this->db->pquery($query, array($stagerecordid));
        //$currentflag = $this->db->query_result($result, 0, 'workflowstagesflag');
        $workflowsid = $this->db->query_result($result, 0, 'workflowsid');
        $this->db->pquery("DELETE FROM `vtiger_salesorderworkflowstages` WHERE vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename='ContractGuarantee' AND vtiger_salesorderworkflowstages.workflowsid=?", array($record, $workflowsid));
    }
}
?>
