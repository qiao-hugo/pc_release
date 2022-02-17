<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class JobAlerts_SubSave_Action extends Vtiger_Save_Action {
	
	public function checkPermission(Vtiger_Request $request){
		return true;
	}
	
	public function process(Vtiger_Request $request) {
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$db = PearDatabase::getInstance();

		//写数据
		$edit = $request->get('edit');
		$modulename='ModComments';
		$modcommentsid=$request->get('modcommentsid');
		$subject = $request->get('subject');
		$alertcontent = $request->get('alertcontent');
		$alerttime = $request->get('alerttime');
		$alertid = $request->get('alertid');
		$alertid=implode(' |##| ', $alertid);
		$accountid=$request->get('accountid');
		$activitytype = $request->get('activitytype');
		$taskpriority = $request->get('taskpriority');

		$ownerid=$request->get('ownerid');
		$alertstatus = 'wait';//$request->get('alertstatus');
		$remark = $request->get('remark');
	
		//暂时这样解决
		//提醒时间check
		if (empty($alerttime)){
			$response = new Vtiger_Response();
			$resultResponse['success']=false;
			$response->setResult(array(2));
			$response->emit();
			return;
		}
		//提醒人check
		if (empty($alertid)){
			$response = new Vtiger_Response();
			$resultResponse['success']=false;
			$response->setResult(array(1));
			$response->emit();
			return;
		}
		
// 		if($edit){
			
// 		}else{
			//获取id
			$id=$db->getUniqueID("vtiger_jobalerts");
			$creatorid = $currentUserModel->id;
	
			$insertSql="insert into vtiger_jobalerts(
					jobalertsid,subject,alerttime,modulename,moduleid,alertcontent,alertid,alertstatus,alertcount,activitytype,taskpriority,remark,ownerid,creatorid,accountid,createdtime)
					 values(?,?,?,?,?,?,?,?,0,?,?,?,?,?,?,sysdate())";
			
			$insertparams[]=$id;
			$insertparams[]=$subject;
			$insertparams[]=$alerttime;
			$insertparams[]=$modulename;
			$insertparams[]=$modcommentsid;
			$insertparams[]=$alertcontent;
			$insertparams[]=$alertid;
			$insertparams[]=$alertstatus;
			$insertparams[]=$activitytype;
			$insertparams[]=$taskpriority;
			$insertparams[]=$remark;
			$insertparams[]=$ownerid;
			$insertparams[]=$creatorid;
			$insertparams[]=$accountid;
			$db->pquery($insertSql, $insertparams);
			
			//更新提醒人表
			$arrAlertid=$request->get('alertid');
			if (!empty($arrAlertid)){
				//更新提醒人表(插入)
				foreach($arrAlertid as $alertid) {
					$insert_query = "insert into vtiger_jobalertsreminder(jobalertsid,alertid)values(?,?)";
					$db->pquery($insert_query, array($id,$alertid));
				}
			}
// 		}
		
		$resultResponse['success']=true;
		//$result=$recordModel[$modcommentsid];

		$response = new Vtiger_Response();
		//$response->setEmitType(Vtiger_Response::$EMIT_JSON);
		$response->setResult($resultResponse);
		$response->emit();
	}
	
}
