<?php
class PayApply_Save_Action extends Vtiger_Save_Action {
    public $stayPaymentWorkFlowSid = 3068669;  //代付款在线签收id
    public function saveRecord($request) {

        $adb =PearDatabase::getInstance();
        $record=$request->get('record');
        $recordModel = $this->getRecordModelFromRequest($request);
        if($request->get('record')>0){
            $recordModel->set('id',$record);
            $recordModel->set('mode','edit');
        }
        if(!$record){
            $recordModel->set('applydate', date('Y-m-d H:i:s'));
        }
        $recordModel->set('workflowsid', $this->stayPaymentWorkFlowSid);
        $recordModel->set('workflowtime', date('Y-m-d H:i:s'));
        $recordModel->set('workflowsnode', '提单人上级审批');
        $recordModel->set('modulestatus', 'a_normal');
		$recordModel->save();
        $recordId = $recordModel->getId();
        //生成工作流
        $_REQUEST['workflowsid']=$this->stayPaymentWorkFlowSid;
        $focus = CRMEntity::getInstance('PayApply');
        $focus->makeWorkflows('PayApply', $_REQUEST['workflowsid'], $recordId,'edit');

		return $recordModel;
	}
}
