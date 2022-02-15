<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Schoolassessment_BasicAjax_Action extends Vtiger_Action_Controller {
	function __construct() {
		parent::__construct();
		$this->exposeMethod('addSchooladopt');
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

	
	public function addSchooladopt(Vtiger_Request $request) {
		$record = $request->get('record');
		$assessownerid = $request->get('assessownerid');
		$remarks = $request->get('remarks');

		$successSchoolId = array();
		do {


			global $current_user;
			$db = PearDatabase::getInstance();
			$sql = "SELECT r.gendertype, r.`name`, r.telephone, r.email, s.assessmentpeopleid, s.schoolresumeid, s.assessmentresult, s.assessmentdate FROM vtiger_schoolassessmentpeople s INNER JOIN vtiger_schoolresume r ON s.schoolresumeid = r.schoolresumeid WHERE s.assessmentid = ? AND s.is_goadopt = 0 AND s.assessmentresult='assessmentresult_yes'";
			$sel_result = $db->pquery($sql, array($record));
			$res_cnt    = $db->num_rows($sel_result);
			if ($res_cnt == 0) {
				break;
			}
			// 获取简历数据
			$schoolresumeData = array();
			while($rawData=$db->fetch_array($sel_result)) {
            	$schoolresumeData[] = $rawData;
        	}

        	// 获取 招聘计划
        	$sql = "SELECT vtiger_schoolassessment.schoolrecruitid,vtiger_schoolassessment.schooleligibilityid FROM vtiger_schoolassessment WHERE vtiger_schoolassessment.assessmentid=? LIMIT 1";
        	$sel_result = $db->pquery($sql, array($record));
        	$res_cnt    = $db->num_rows($sel_result);
        	if ($res_cnt == 0) {
        		break;
        	}
        	$row = $db->query_result_rowdata($sel_result, 0);
        	$schoolrecruitid = $row['schoolrecruitid']; //招聘计划id
        	$schooleligibilityid = $row['schooleligibilityid']; // 社招面试通过名单 id
        	if (! ($schoolrecruitid || $schooleligibilityid) ) {
        		break;
        	}

        	
        	if ($schooleligibilityid > 0) {
        		$sql = "SELECT schooladoptid FROM vtiger_schooladopt WHERE vtiger_schooladopt.schooleligibilityid=? AND  vtiger_schooladopt.chargeperson=? LIMIT 1";
        		$sel_result = $db->pquery($sql, array($schooleligibilityid, $assessownerid));
        	} else {
        		$sql = "SELECT schooladoptid FROM vtiger_schooladopt WHERE vtiger_schooladopt.schoolrecruitid=? AND vtiger_schooladopt.chargeperson=? LIMIT 1";
	        	$sel_result = $db->pquery($sql, array($schoolrecruitid, $assessownerid));
        	}
			$res_cnt    = $db->num_rows($sel_result);
			$schooladoptid = 0;

			if ($res_cnt > 0) {
				$tt = $db->query_result_rowdata($sel_result, 0);
				$schooladoptid = $tt['schooladoptid'];
			} else {
				$requestData = array(); //assessownerid
				$assessmentRequest = new Vtiger_Request($requestData, $requestData);
	            $assessmentRequest->set('module', 'Schooladopt');
	            $assessmentRequest->set('action', 'SaveAjax');
	            $assessmentRequest->set('record', '');
	            $assessmentRequest->set('schoolrecruitid', $schoolrecruitid);
	            $assessmentRequest->set('schooleligibilityid', $schooleligibilityid);
	            $assessmentRequest->set('chargeperson', $request->get('assessownerid'));
	            $assessmentRequest->set('remarks', $request->get('remarks'));
	            $ressorder = new Vtiger_SaveAjax_Action();
	            $ressorderecord = $ressorder->saveRecord($assessmentRequest);
	            $schooladoptid = $ressorderecord->getId();
			}
			$insertData = array();
			$sql = "INSERT INTO `vtiger_schooladoptpeople` (`schooladoptpeopleid`, `schoolresumeid`, `schooladoptid`, `assessmentresult`, `assessmentdate`, `instructor`, `p_chargeperson`, `p_schoolrecruitid`) VALUES ";
			$insertData = array();
        	foreach ($schoolresumeData as $key => $value) {
        		$sql .= "(?, ?, ?, ?, ?, ?, ?, ?),";
        		$insertData[] = ''; //schooladoptpeopleid
        		$insertData[] = $value['schoolresumeid'];;
        		$insertData[] = $schooladoptid;
        		$insertData[] = $value['assessmentresult'];
        		$insertData[] = $value['assessmentdate'];
        		$insertData[] = '';

        		$insertData[] = $request->get('assessownerid');
        		$insertData[] = $schoolrecruitid;
        		
	            $successSchoolId[] = "'".$value['assessmentpeopleid']."'";
        	}
        	$sql = trim($sql, ',');
        	//print_r($sql);  print_r($insertData);die;
        	// 把添加成功的 简历合格人员改成 已考核
        	if (count($successSchoolId) > 0) {
	        	$db->pquery($sql, $insertData);

        		$sql = "UPDATE vtiger_schoolassessmentpeople SET is_goadopt=1 WHERE assessmentpeopleid IN (". implode(',', $successSchoolId) .")";
        		$db->pquery($sql, array());
        	}
        	
		} while (0);

		$response = new Vtiger_Response();
		$response->setResult(array('num'=>count($successSchoolId)));
		$response->emit();
		exit;
	}


	
}
