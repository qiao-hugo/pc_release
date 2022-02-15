<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Schoolqualified_BasicAjax_Action extends Vtiger_Action_Controller {
	function __construct() {
		parent::__construct();
		$this->exposeMethod('addSchoolassessment');
	}
	
	function checkPermission(Vtiger_Request $request) {
		return;
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


	public function addSchoolassessment(Vtiger_Request $request) {
		$record = $request->get('record');
		//$assessownerid = $request->get('assessownerid');  //考核人员id
		//$remarks = $request->get('remarks');

		$successSchoolId = array();
		do {
			global $current_user;
			$db = PearDatabase::getInstance();
			$sql = "SELECT vtiger_schoolresume.gendertype, vtiger_schoolresume.`name`, vtiger_schoolresume.telephone, vtiger_schoolresume.email, vtiger_schoolresume.schoolresumeid, vtiger_schoolqualifiedpeople.schoolqualifiedpeopleid, vtiger_schoolqualifiedpeople.schoolresumeid, vtiger_schoolqualifiedpeople.assessmentuser FROM vtiger_schoolqualifiedpeople INNER JOIN vtiger_schoolresume ON vtiger_schoolqualifiedpeople.schoolresumeid = vtiger_schoolresume.schoolresumeid WHERE vtiger_schoolqualifiedpeople.schoolqualifiedid = '{$record}' AND vtiger_schoolqualifiedpeople.is_goassessment = 0 AND vtiger_schoolqualifiedpeople.assessmentuser > 0 AND vtiger_schoolqualifiedpeople.is_assessment=1";
			$sel_result = $db->pquery($sql, array());
			$res_cnt    = $db->num_rows($sel_result);
			if ($res_cnt == 0) {
				break;
			}

			// 获取简历数据
			$schoolresumeData = array();
			while($rawData=$db->fetch_array($sel_result)) {
            	$schoolresumeData[$rawData['assessmentuser']][] = $rawData;
        	}

        	// 获取 招聘计划
        	$sql = "SELECT vtiger_schoolqualified.schoolrecruitid FROM vtiger_schoolqualified WHERE vtiger_schoolqualified.schoolqualifiedid=? LIMIT 1";

        	$sel_result = $db->pquery($sql, array($record));
        	$res_cnt    = $db->num_rows($sel_result);
        	if ($res_cnt == 0) {
        		break;
        	}
        	$row = $db->query_result_rowdata($sel_result, 0);
        	$schoolrecruitid = $row['schoolrecruitid']; //招聘计划id

        	foreach ($schoolresumeData as $key => $value) {
        		// 判断有没有招聘考核
	        	$sql = "SELECT assessmentid FROM vtiger_schoolassessment WHERE vtiger_schoolassessment.schoolrecruitid=? AND assessownerid=? LIMIT 1";
	        	$sel_result = $db->pquery($sql, array($schoolrecruitid, $key));
				$res_cnt    = $db->num_rows($sel_result);
				$assessmentid = 0;

				if ($res_cnt > 0) {
					$tt = $db->query_result_rowdata($sel_result, 0);
					$assessmentid = $tt['assessmentid'];
				} else {
					$requestData = array(); //assessownerid
					$assessmentRequest = new Vtiger_Request($requestData, $requestData);
		            $assessmentRequest->set('module', 'Schoolassessment');
		            $assessmentRequest->set('action', 'SaveAjax');
		            $assessmentRequest->set('record', '');
		            $assessmentRequest->set('schoolrecruitid', $schoolrecruitid);
		            $assessmentRequest->set('shool_resume_source', 'school_recruit');
		            $assessmentRequest->set('assessownerid', $key);
		            $assessmentRequest->set('remarks', '');
		            $ressorder = new Vtiger_SaveAjax_Action();
		            $ressorderecord = $ressorder->saveRecord($assessmentRequest);
		            $assessmentid = $ressorderecord->getId();
				}



				$insertData = array();
				$sql = "INSERT INTO `vtiger_schoolassessmentpeople` (`assessmentpeopleid`, `assessmentpeoplename`, `gendertype`, `telephone`, `email`, `assessmentresult`, `assessmentdate`, `assessmentdescribe`, `assessmentid`, `schoolresumeid`, `p_assessownerid`, `p_schoolrecruitid`) VALUES ";
				$insertData = array();
	        	foreach ($value as $k => $v) {
	        		$sql .= "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?),";
	        		$insertData[] = ''; //assessmentpeopleid
	        		$insertData[] = $v['name'];
	        		$insertData[] = $v['gendertype']; //assessmentpeoplename
	        		$insertData[] = $v['telephone'];
	        		$insertData[] = $v['email'];
	        		$insertData[] = ''; //assessmentresult
	        		$insertData[] = ''; //assessmentdate
	        		$insertData[] = ''; //assessmentdescribe
	        		$insertData[] = $assessmentid; //assessmentid
	        		$insertData[] = $v['schoolresumeid'];

	        		$insertData[] = $key;
	        		$insertData[] = $schoolrecruitid;

		            $successSchoolId[] = "'".$v['schoolqualifiedpeopleid']."'";
	        	}
	        	$sql = trim($sql, ',');
	        	$db->pquery($sql, $insertData);
        	}

        	// 把添加成功的 简历合格人员改成 已考核
        	if (count($successSchoolId) > 0) {
        		$sql = "UPDATE vtiger_schoolqualifiedpeople SET is_goassessment=1 WHERE schoolqualifiedpeopleid IN (". implode(',', $successSchoolId) .")";
        		$db->pquery($sql, array());
        	}
		} while (0);

		$response = new Vtiger_Response();
		$response->setResult(array('num'=>count($successSchoolId)));
		$response->emit();
		exit;
	}
	
}
