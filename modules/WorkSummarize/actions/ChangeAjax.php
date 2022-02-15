<?php
class WorkSummarize_ChangeAjax_Action extends Vtiger_Action_Controller {

	function checkPermission(Vtiger_Request $request) {
		return true;
	}

	public function process(Vtiger_Request $request) {
		
		//自定义查询条件
		/*VTCacheUtils::updateFieldInfo(6, 'protected', 1, 'protected', 'protected', 'vtiger_account', 2, 'V~M', 0);
		VTCacheUtils::updateFieldInfo(6, 'accountrank',2 ,'accountrank','accountrank','vtiger_account', 2, 'V~M', 0);
		VTCacheUtils::updateFieldInfo(6, 'accountcategory',3,'accountcategory','accountcategory','vtiger_account', 2, 'V~M', 0);
		VTCacheUtils::updateFieldInfo(6, 'smownerid',4,'smownerid','smownerid','vtiger_crmentity', 2, 'V~M', 0);
		*/
		//数据权限与列表一致
		vglobal('currentView','List');
		$result =WorkSummarize_Record_Model::getNoWrite();
		if(empty($result)){
			echo 0;
		}else{
			echo json_encode($result);
		}
	}
}
