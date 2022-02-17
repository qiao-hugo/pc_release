<?php
class Billing_ChangeAjax_Action extends Vtiger_Action_Controller {

	function checkPermission(Vtiger_Request $request) {
		return true;
	}

	public function process(Vtiger_Request $request)
    {
        $recordId = $request->get('record');
        if (empty($recordId)) {
            exit;
        }
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, 'Billing');
        $moduleModel = $recordModel->getModule();
        //受保护的客户
        global $current_user;
        $userids=getDepartmentUser('H25');
        if(in_array($current_user->id,$userids) || $current_user->id==1){
            $db=PearDatabase::getInstance();
            $date=date('Y-m-d H:i:s');
            $query="update vtiger_billing set financeor=?,locktime=?,modulestatus=? where billingid=?";
            $db->pquery($query,array($current_user->id,$date,'c_complete',$recordId));
        }
    }
}
