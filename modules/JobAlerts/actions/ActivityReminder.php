<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class JobAlerts_ActivityReminder_Action extends Vtiger_Action_Controller{

	function __construct() {
		$this->exposeMethod('getReminders');
		$this->exposeMethod('postpone');
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

	function getReminders(Vtiger_Request $request) {
		//$recordModels = $this->getCalendarReminder();
		
		$recordCount= array (
				'newCount'=>JobAlerts_Record_Model::getReminderResultCount('new'),
				'waitCount'=>JobAlerts_Record_Model::getReminderResultCount('wait'),
				'finishCount'=>JobAlerts_Record_Model::getReminderResultCount('finish'),
				'relationCount'=>JobAlerts_Record_Model::getReminderResultCount('relation'),
				'myreminderCount'=>JobAlerts_Record_Model::getReminderResultCount('myreminder')
				);
		
		$response = new Vtiger_Response();
		$response->setResult($recordCount);
		$response->emit();
	}

	/**
	 * Function returns Calendar Reminder record models
	 * @return <Array of Calendar_Record_Model>
	 */
	function getCalendarReminder() {
		$db = PearDatabase::getInstance();
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$activityReminder = $currentUserModel->getCurrentUserActivityReminderInSeconds();
		$recordModels = array();
	
		if($activityReminder != '' ) {
			$reminderSql = "SELECT * FROM vtiger_jobalerts
					WHERE (FIND_IN_SET(?,REPLACE(alertid,' |##| ',',')) or ownerid=?)
					and STR_TO_DATE(alerttime,'%Y-%m-%d %H:%i')<=SYSDATE()
					and alertstatus='wait'";
			$result = $db->pquery($reminderSql, array($currentUserModel->getId(),$currentUserModel->getId()));
			$rows = $db->num_rows($result);
			for($i=0; $i<$rows; $i++) {
				$recordModels[]  = $db->query_result_rowdata($result, $i);
			}
		}
		return $recordModels;
	}
	
	/**
	 * 提醒状态更新
	 * @param Vtiger_Request $request
	 */
	function postpone(Vtiger_Request $request) {
		$recordId = $request->get('record');
		$db = PearDatabase::getInstance();
		$type=$request->get('type');
		if($type=='delay'){
			//推迟提醒次数设置
			$db->pquery("UPDATE vtiger_jobalerts set alertcount=alertcount+1 where jobalertsid = ?", array($recordId));
		}else if($type=='complete'){
			//完成状态和时间设定
			$db->pquery("UPDATE vtiger_jobalerts set finishtime=sysdate(),alertstatus='finish' where jobalertsid = ?", array($recordId));
		}else{
		}
	}
	
}