<?php
class PurchaseInvoice_ChangeAjax_Action extends Vtiger_Action_Controller {

	function checkPermission(Vtiger_Request $request) {
		return true;
	}

	public function process(Vtiger_Request $request) {
        $swid=$_REQUEST['record'];
        $db=PearDatabase::getInstance();

        //young 2015-05-20 做一步兼容，在当前为非产品的节点的时候走工作流的客服分配判断
        // ServiceCheck 分配下属和指定客户 可给客服部
        // NextCheck 分配下属和指定上级审核 可给技术部门
        global $current_user;
        $sql="SELECT vtiger_workflowstages.workflowstagesflag FROM  vtiger_salesorderworkflowstages left JOIN vtiger_workflowstages ON (vtiger_salesorderworkflowstages.workflowstagesid=vtiger_workflowstages.workflowstagesid) WHERE vtiger_salesorderworkflowstages.salesorderworkflowstagesid =? LIMIT 1";
        $resultdb=$db-> pquery($sql, array($swid));
        $res = array();
        $workflowstagesflag = '';
        if($db->num_rows($resultdb)>0) {
            $workflowstagesflag = $db->query_result($resultdb, 0, 'workflowstagesflag');
            $res['month'] = date('m');
        }
        $res['flag'] = $workflowstagesflag;

        $response = new Vtiger_Response();
        $response->setResult($res);
        $response->emit();
        
	}
	
	
}
