<?php
class Receipt_Save_Action extends Vtiger_Save_Action {
    public $stayPaymentWorkFlowSid = 3072642;
    public function saveRecord($request) {
        global $adb;
        $adb =PearDatabase::getInstance();
        $record=$request->get('record');
        $recordModel = $this->getRecordModelFromRequest($request);
        $modulestatus = '';
        if($record>0) {
            $recordModel->set('id',$record);
            $recordModel->set('mode','edit');

            $sql = "select * FROM vtiger_receipt where receiptid=?";
            $sel_result = $adb->pquery($sql, array($record));
            $rawData = $adb->query_result_rowdata($sel_result, 0);
            $modulestatus = $rawData['modulestatus'];
        }
        if(empty($request->get('receiptno'))){
            $recordModel->set('receiptno', '');
        }
        $recordModel->set('modulestatus', 'a_normal');

        $recordModel->set('workflowsid', $this->stayPaymentWorkFlowSid);
        $recordModel->set('workflowstime', date('Y-m-d H:i:s'));
        $recordModel->set('workflowsnode', '已提交');
        $recordModel->set('modulestatus', 'a_normal');
		$recordModel->save();
        $recordId = $recordModel->getId();

        if(empty($record) || $modulestatus == 'a_exception') {
            //生成工作流
            $_REQUEST['workflowsid'] = $this->stayPaymentWorkFlowSid;
            $focus = CRMEntity::getInstance('Receipt');
            $focus->makeWorkflows('Receipt', $_REQUEST['workflowsid'], $recordId, 'edit');
            if($modulestatus == 'a_exception'){
                $applyuserid=$request->get('applyuserid');
                $adb->pquery("UPDATE vtiger_salesorderworkflowstages SET smcreatorid=? WHERE salesorderid=?", array($applyuserid,$recordId));
            }
        }

        return $recordModel;
	}
}
