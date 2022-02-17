<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Vtiger_Delete_Action extends Vtiger_Action_Controller {

	function checkPermission(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$record = $request->get('record');

		$currentUserPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		if(!$currentUserPrivilegesModel->isPermitted($moduleName, 'Delete', $record)) {
			throw new AppException('LBL_PERMISSION_DENIED');
		}
		if(!$currentUserPrivilegesModel->isAdminUser()&&!in_array($moduleName,array('SalesOrder','ServiceContracts','ReceivedPayments'))){
			throw new AppException('不允许删除数据');
		}
		//1.编辑权限，有上下级关系的，或者本人，或者有审核权限的人
		/* if(!empty($recordId)){
			//if(isset($_SESSION['isyourcode'])&&$_SESSION['isyourcode']==$moduleName.$recordId){
				//偶审核权限的人，通过isyourcode值来判断
			//}else{
				$user=getAccessibleUsers($moduleName,'Edit',true);
					
				$recordModule=Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
				$recordField=$recordModule->getData();
				if(isset($recordField['assigned_user_id'])){
					$id=$recordField['assigned_user_id'];
				}elseif(isset($recordField['smcreatorid'])){
					$id=$recordField['smcreatorid'];
				}
				if(is_array($user)&& !in_array($id,$user)){
					throw new AppException(vtranslate('没有访问权限'));
				}
			//}
		
		} */
		//young.yang 2015-1-3 增加编辑页面，对于流程状态的控制，某些状态不允许编辑
		//	修复bug#7526,因为修改审核状态造成
		global $isallow;
		if(in_array($moduleName, $isallow)){
			$record=Vtiger_Record_Model::getInstanceById($record,$moduleName);
			if(!empty($record)){
				$module=$record->getData();
				$moduleStatus=$module['modulestatus'];
				if(!getIsEditOrDel('delete',$moduleStatus)){
					throw new AppException('状态'.vtranslate($moduleStatus,$moduleName).'不允许当前的操作');
				}
			}
		}
		//end
	}

	public function process(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$recordId = $request->get('record');
		$ajaxDelete = $request->get('ajaxDelete');
		
		$recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
		$moduleModel = $recordModel->getModule();

		$recordModel->delete();

		$listViewUrl = $moduleModel->getListViewUrl();
		if($ajaxDelete) {
			$response = new Vtiger_Response();
			$response->setResult($listViewUrl);
			return $response;
		} else {
			header("Location: $listViewUrl");
		}
	}
}
