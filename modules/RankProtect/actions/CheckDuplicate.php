<?php
/*+***************
 * 商务等级和客户等级关联不重复
 ****************/

class RankProtect_CheckDuplicate_Action extends Vtiger_Action_Controller {

	function checkPermission(Vtiger_Request $request) {
		return true;
	}

	public function process(Vtiger_Request $request) {
		$db = PearDatabase::getInstance();
		$accountName = explode('#',$request->get('accountname'));
		$record = $request->get('record');
		$result=$db->pquery('select rankid from vtiger_rankprotect where accountrank=? and performancerank=? limit 1',$accountName);
		$return = array('success'=>false);
		if ($db->num_rows($result)) {
				$id=$db->query_result_rowdata($result);
				if(empty($record)){
						$return = array('success'=>true, 'message'=>'分类已存在！');
				}elseif($record!=$id['rankid']){
						$return = array('success'=>true, 'message'=>'分类已存在！');
				}
		}
		$response = new Vtiger_Response();
		$response->setResult($return);
		$response->emit();
	}
}
