<?php
class ContractDelaySign_BasicAjax_Action extends Vtiger_BasicAjax_Action {
    public $contractDelaySignWorkflowsid = 2968621;  //代付款在线签收id
    function __construct() {
        parent::__construct();
        $this->exposeMethod('makeWorkflowStagesByOrder');
    }

    function checkPermission(Vtiger_Request $request) {
        return;
    }

    public function process(Vtiger_Request $request) {
        $mode = $request->getMode();
        if(!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
            return;
        }
    }
    /**
     * 对订单开票生成流程
     * @param $recordId
     */
    public function makeWorkflowStagesByOrder(Vtiger_Request $request){
        $recordId = $request->get('record');
        $reason = $request->get('reason');
        global $current_user;
        $recordModel=ContractDelaySign_Record_Model::getInstanceById($recordId,'ContractDelaySign');
        $ncolumn_fields=$recordModel->entity->column_fields;
        $data=array("flag"=>false);
        do {
//            if (!in_array($ncolumn_fields['contractsignstatus'], array('signed'))
//                || $ncolumn_fields['assigned_user_id'] != $current_user->id){
//                $data['msg']="不允许操作";
//                break;
//            }
            $db = $recordModel->entity->db;
            $workflowsid=$this->contractDelaySignWorkflowsid;//线上的
            $_REQUEST['workflowsid']=$workflowsid;
            $focus=CRMEntity::getInstance('ContractDelaySign');
            $focus->makeWorkflows('ContractDelaySign',$_REQUEST['workflowsid'],$recordId,'edit');
            $query="UPDATE vtiger_salesorderworkflowstages,vtiger_contractdelaysign set vtiger_salesorderworkflowstages.modulestatus='p_process' WHERE vtiger_contractdelaysign.contractdelaysignid=vtiger_salesorderworkflowstages.salesorderid AND vtiger_salesorderworkflowstages.salesorderid=?";
            $focus->db->pquery($query,array($recordId));
            $departmentid=$_SESSION['userdepartmentid'];
            $focus->setAudituid('ContractsAuditset',$departmentid,$recordId,$workflowsid);
            //新建时 消息提醒第一审核人进行审核
            $object = new SalesorderWorkflowStages_SaveAjax_Action();
            $object->sendWxRemind(array('salesorderid'=>$recordId,'salesorderworkflowstagesid'=>0));
            $sql = "select workflowstagesname from vtiger_workflowstages where workflowsid=? order by sequence LIMIT 1";
            $sel_result=$focus->db->pquery($sql, array($workflowsid));
            $res_cnt=$db->num_rows($sel_result);
            $workflowsnode='';
            if ($res_cnt > 0) {
                $row = $db->query_result_rowdata($sel_result, 0);
                $workflowsnode = $row['workflowstagesname'];
            }
            $file=$request->get('filename').'##'.$request->get('fileid');
            $focus->db->pquery("UPDATE `vtiger_contractdelaysign` SET modulestatus='b_check',applydate=?,file=?,reason=? WHERE contractdelaysignid=?", array(date("Y-m-d H:i:s"),$file,$reason,$recordId));


            if($data['fileid']){
                $db->pquery('update vtiger_files set relationid=? where attachmentsid=?',array($recordId,$data['fileid']));
            }

            $data=array("flag"=>true);

        }while(0);
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
}
