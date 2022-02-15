<?php
class SupplierStatement_Save_Action extends Vtiger_Save_Action {
    public $stayPaymentWorkFlowSid = 2695180;  //代付款在线签收id
    public function saveRecord($request) {
        $adb =PearDatabase::getInstance();
        $record=$request->get('record');
        $recordModel = $this->getRecordModelFromRequest($request);
        if($request->get('record')>0){
            $recordModel->set('id',$record);
            $recordModel->set('mode','edit');
        }


        $recordModel->set('workflowsid', $this->stayPaymentWorkFlowSid);
        $recordModel->set('workflowstime', date('Y-m-d H:i:s'));
        $recordModel->set('workflowsnode', '代付款线上签收');
        $recordModel->save();

        $recordId = $recordModel->getId();
        //生成工作流
        $_REQUEST['workflowsid']=$this->stayPaymentWorkFlowSid;
        $departmentid=$_SESSION['userdepartmentid'];
        $focus = CRMEntity::getInstance('SupplierStatement');
        $focus->makeWorkflows('SupplierStatement', $_REQUEST['workflowsid'], $recordId,'edit');
        $focus->setAudituid('SupplierStatementCAuditset',$departmentid,$recordId,$_REQUEST['workflowsid']);

		return $recordModel;
	}
}
