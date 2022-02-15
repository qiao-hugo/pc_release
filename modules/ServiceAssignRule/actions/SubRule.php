<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ServiceAssignRule_SubRule_Action extends Vtiger_Action_Controller{

	function __construct() {
		$this->exposeMethod('getUserInfosByDepartment');
		$this->exposeMethod('getAccountInfos');
		$this->exposeMethod('getServiceAssignInfos');
		$this->exposeMethod('doServiceAssign');
	}

	public function checkPermission(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);

		$userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		$permission = $userPrivilegesModel->hasModulePermission($moduleModel->getId());

		if(!$permission) {
			throw new AppException('LBL_PERMISSION_DENIED');
		}
	}

	public function process(Vtiger_Request $request) {
		$mode = $request->getMode();
		if(!empty($mode) && $this->isMethodExposed($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}

	}

	/**
	 * 获取部门下的用户信息
	 * @param Vtiger_Request $request
	 */
	function getUserInfosByDepartment(Vtiger_Request $request) {
		$departmentid=$request->get('departmentid');
		//获取部门下的用户
		$result=ServiceAssignRule_Record_Model::get_user_department_array_bydepartmentid($departmentid);
		$response = new Vtiger_Response();
		$response->setResult($result);
		$response->emit();
	}

	/**
	 * 获取分配客户信息
	 * @param Vtiger_Request $request
	 */
	function getAccountInfos(Vtiger_Request $request) {

		//获取客户信息
		$result=ServiceAssignRule_Record_Model::getAccountInfos($request);
		$response = new Vtiger_Response();
		$response->setResult($result);
		$response->emit();
	}
	
	/**
	 * 获取客服的分配统计信息
     *根据人员id查询分配限制
	 * @param Vtiger_Request $request
	 */
	function getServiceAssignInfos(Vtiger_Request $request) {
		$serviceid=$request->get('serviceid');
		//获取客服的分配统计信息
		$result=ServiceAssignRule_Record_Model::getServiceAssignInfos($serviceid);
		
		$response = new Vtiger_Response();
		$response->setResult($result);
		$response->emit();
	}
	
	/**
	 * 客服分配处理
	 * @param Vtiger_Request $request
	 */
	function doServiceAssign(Vtiger_Request $request) {
		//客服分配处理
		$result = ServiceAssignRule_Record_Model::doServiceAssign($request);
		//获取客服分配信息
		$serviceid=$request->get('serviceid');
		$assignresult=ServiceAssignRule_Record_Model::getServiceAssignInfos($serviceid);

		$response = new Vtiger_Response();
		$response->setResult(array($result,$assignresult));
		$response->emit();
	}
	
}