<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Schoolpracticalpeople_BasicAjax_Action extends Vtiger_Action_Controller {
	function __construct() {
		parent::__construct();
		$this->exposeMethod('getCreateuser');
		$this->exposeMethod('isCheckTow');
		$this->exposeMethod('addSchoolqualified');
		$this->exposeMethod('setAssessmentresult');
	}
	
	function checkPermission(Vtiger_Request $request) {
		return;
	}


	public function addSchoolemployById($schoolpracticalpeopleid, $assessownerid) {
		//$record = $request->get('record');
		//$assessownerid = $request->get('assessownerid');
		$remarks = '';

		do {
			global $current_user;
			$db = PearDatabase::getInstance();
			$sql = "SELECT vtiger_schoolpracticalpeople.schoolresumeid, vtiger_schoolpracticalpeople.schoolpracticalpeopleid, vtiger_schoolpractical.schoolrecruitid,vtiger_schoolpractical.schooleligibilityid FROM vtiger_schoolpracticalpeople LEFT JOIN vtiger_schoolpractical ON vtiger_schoolpracticalpeople.practicalpeopleid=vtiger_schoolpractical.schoolpracticalid WHERE vtiger_schoolpracticalpeople.schoolpracticalpeopleid = ? AND vtiger_schoolpracticalpeople.is_goschoolemploy = 0 AND vtiger_schoolpracticalpeople.assessmentresult = 'assessmentresult_yes'";
			$sel_result = $db->pquery($sql, array($schoolpracticalpeopleid));
			$res_cnt    = $db->num_rows($sel_result);
			if ($res_cnt == 0) {
				break;
			}

			// 获取简历数据
			$schoolresumeData = array();
			while($rawData=$db->fetch_array($sel_result)) {
            	$schoolresumeData[] = $rawData;
        	}

        	$schoolrecruitid = $schoolresumeData[0]['schoolrecruitid']; //招聘计划id
        	$schooleligibilityid = $schoolresumeData[0]['schooleligibilityid']; 

        	$schoolemployid = 0;
        	if (!empty($schoolrecruitid)) {
        		$sql = "SELECT schoolemployid FROM vtiger_schoolemploy WHERE schoolrecruitid=? AND assessownerid=? LIMIT 1";
	        	$sel_result = $db->pquery($sql, array($schoolrecruitid, $assessownerid));
				$res_cnt    = $db->num_rows($sel_result);
				
        	}
        	if (!empty($schooleligibilityid)) {
        		// 判断有没有录用人员名单
	        	$sql = "SELECT schoolemployid FROM vtiger_schoolemploy WHERE schooleligibilityid=? AND assessownerid=? LIMIT 1";
	        	$sel_result = $db->pquery($sql, array($schooleligibilityid, $assessownerid));
				$res_cnt    = $db->num_rows($sel_result);
        	}

			if ($res_cnt > 0) {
				$tt = $db->query_result_rowdata($sel_result, 0);
				$schoolemployid = $tt['schoolemployid'];
			} else {
				$requestData = array(); //assessownerid
				$assessmentRequest = new Vtiger_Request($requestData, $requestData);
	            $assessmentRequest->set('module', 'Schoolemploy');
	            $assessmentRequest->set('action', 'SaveAjax');
	            $assessmentRequest->set('record', '');
	            $assessmentRequest->set('schoolrecruitid', $schoolrecruitid);
	            $assessmentRequest->set('schooleligibilityid', $schooleligibilityid);
	            $assessmentRequest->set('shool_resume_source', 'school_recruit');
	            $assessmentRequest->set('assessownerid', $assessownerid);
	            $assessmentRequest->set('remarks', $remarks);
	            $ressorder = new Vtiger_SaveAjax_Action();
	            $ressorderecord = $ressorder->saveRecord($assessmentRequest);
	            $schoolemployid = $ressorderecord->getId();
			}

	        $insertData = array();
			$sql = "INSERT INTO `vtiger_schoolemploypeople` (`schoolemploypeopleid`, `schoolresumeid`, `userid`, `schoolemployid`, `p_assessownerid`) VALUES ";
			$insertData = array();
        	foreach ($schoolresumeData as $key => $value) {
        		$sql .= "(?, ?, ?, ?, ?),";
        		$insertData[] = ''; //schoolemploypeopleid
        		$insertData[] = $value['schoolresumeid'];
        		$insertData[] = 0;
        		$insertData[] = $schoolemployid;
        		$insertData[] = $assessownerid;
        		
	            $successSchoolId[] = "'".$value['schoolpracticalpeopleid']."'";
        	}
        	$sql = trim($sql, ',');

        	// 把添加成功的 简历合格人员改成 已考核
        	if (count($successSchoolId) > 0) {
        		$db->pquery($sql, $insertData);
        		$sql = "UPDATE vtiger_schoolpracticalpeople SET is_goschoolemploy=1 WHERE schoolpracticalpeopleid IN (". implode(',', $successSchoolId) .")";
        		$db->pquery($sql, array());
        	}
        	
		} while (0);
	}

	public function setAssessmentresult(Vtiger_Request $request) {
		$record = $request->get('record');
		$status = $request->get('status');
		$assessownerid = $request->get('assessownerid');
		// 判断是否有权限
		//$flag = Users_Privileges_Model::isPermitted('Schoolassessmentpeople', 'NegativeEdit');
		$flag = true;
        global $current_user;
        $db=PearDatabase::getInstance();
        $is_admin = $current_user->is_admin == 'on' ? 1 : 0;
        $data = array('flag'=>0);
        if ($flag || $is_admin) {
        	$sql = "UPDATE vtiger_schoolpracticalpeople SET assessmentresult=?,practicalresultdate=? WHERE schoolpracticalpeopleid=? LIMIT 1";
        	$db->pquery($sql, array($status,date('Y-m-d'), $record));
			$data['flag'] = 1;

			if($status == 'assessmentresult_yes' &&  !empty($assessownerid)) {
				$this->addSchoolemployById($record, $assessownerid);
			}
        }
		$response = new Vtiger_Response();
		$response->setResult($data);
		$response->emit();
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
