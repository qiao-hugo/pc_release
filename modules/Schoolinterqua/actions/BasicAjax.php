<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Schoolinterqua_BasicAjax_Action extends Vtiger_Action_Controller {
	function __construct() {
		parent::__construct();
		$this->exposeMethod('getCreateuser');
		$this->exposeMethod('isCheckTow');
		$this->exposeMethod('addSchoolqualified');
	}
	
	function checkPermission(Vtiger_Request $request) {
		return;
	}

	public function addSchoolqualified(Vtiger_Request $request) {
		$record = $request->get('record');
		$reportsdate = $request->get('reportsdate');
		$reportsower = $request->get('reportsower');
		$reportaddress = $request->get('reportaddress');

		global $current_user;
		$db=PearDatabase::getInstance();
		$sql = "SELECT vtiger_schoolrecruit.schoolrecruitid, vtiger_schoolrecruit.accompany, vtiger_schoolrecruit.remarks FROM vtiger_schoolrecruit WHERE schoolrecruitid=? LIMIT 1";
		$sel_result = $db->pquery($sql, array($record));
		$res_cnt    = $db->num_rows($sel_result);
		$schoolrecruit = array();
		if ($res_cnt > 0) {
			$schoolrecruit = $db->query_result_rowdata($sel_result, 0);
		}

		if (!empty($schoolrecruit)) {
			//$crmid = $db->getUniqueID("vtiger_crmentity");

			// |##| 

			$request = new Vtiger_Request($_REQUES, $_REQUES);
            $request->set('module', 'Schoolqualified');
            $request->set('action', 'SaveAjax');
            $request->set('schoolrecruitid', $schoolrecruit['schoolrecruitid']);
            $request->set('schoolrecruitsower', $current_user->id);
            $request->set('reportsower', $reportsower);
            $request->set('reportsdate', $reportsdate);
            $request->set('accompany', $schoolrecruit['accompany']);
            $request->set('reportaddress', $reportaddress);
            $request->set('remarks', $schoolrecruit['remarks']);
            $ressorder = new Vtiger_SaveAjax_Action();
            $ressorderecord = $ressorder->saveRecord($request);

            if (! empty($ressorderecord)) {
            	$schoolqualifiedid = $ressorderecord->getId();
            	
            }


			/*$schoolqualified = array(
				'schoolqualifiedid'=>$crmid,

				'schoolrecruitid'=>$schoolrecruit['schoolrecruitid'],
				'schoolrecruitsower'=>$current_user->id,

				'reportsower'=>$reportsower,
				'reportsdate'=>$reportsdate,
				'accompany'=>$schoolrecruit['accompany'],
				'reportaddress'=>$reportaddress,
				'remarks'=>$schoolrecruit['remarks'],
			);
			$divideNames = array_keys($schoolqualified);
			$divideValues = array_values($schoolqualified);
			
			$db->pquery('INSERT INTO `vtiger_schoolqualified` ('. implode(',', $divideNames).') VALUES ('. generateQuestionMarks($divideValues) .')',$divideValues);
*/
		}
	}
	

    /**
     * @param Vtiger_Request $request
     * @throws Exception
     */
	public function process(Vtiger_Request $request) {
		$mode = $request->getMode();
		if(!empty($mode)) {
			echo $this->invokeExposedMethod($mode, $request);
			return;
		}
	}

	public function isCheckTow(Vtiger_Request $request) {
		$recordId = $request->get('record'); //用户id
		$recruitname = $request->get('recruitname');

		$db=PearDatabase::getInstance();

		$datas = array('is_check'=>0);

		$sql        = "select schoolrecruitid,recruitname from vtiger_schoolrecruit where recruitname=?";
		$sel_result = $db->pquery($sql, array($recruitname));
		$res_cnt    = $db->num_rows($sel_result);

		if (empty($recordId)) {
			if ($res_cnt > 0) {
				$datas['is_check'] = 1;   //不可以修改
				$datas['message'] = "招聘计划名称重复，请重新输入。";
			}
		} else {
			$row = $db->query_result_rowdata($sel_result, 0);
			if ($res_cnt > 0 && $row['schoolid'] != $recordId) {
				$datas['is_check'] = 1;   //不可以修改
				$datas['message'] = "招聘计划名称重复，请重新输入。";
			}
		}
		$response = new Vtiger_Response();
		$response->setResult($datas);
		$response->emit();
	}


	public function getCreateuser(Vtiger_Request $request) {
		$schoolid = $request->get('schoolid'); //用户id

		$db=PearDatabase::getInstance();


		$sql = "SELECT vtiger_schoolcontacts.schoolcontactsid, vtiger_schoolcontacts.schoolcontactsname FROM vtiger_schoolcontacts WHERE vtiger_schoolcontacts.schoolid=?";
		$sel_result = $db->pquery($sql, array($schoolid));
		$res_cnt    = $db->num_rows($sel_result);

		$data = array();
		if ($res_cnt > 0) {
			while($row=$db->fetch_array($sel_result)) {
            	$data[] = $row;
			}
		}
		
		$response = new Vtiger_Response();
		$response->setResult($data);
		$response->emit();
	}

	
}
