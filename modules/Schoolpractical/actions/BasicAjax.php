<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Schoolpractical_BasicAjax_Action extends Vtiger_Action_Controller {
	function __construct() {
		parent::__construct();
		$this->exposeMethod('addSchoolemploy');
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


	public function addSchoolemploy(Vtiger_Request $request) {
		$record = $request->get('record');
		$assessownerid = $request->get('assessownerid');
		$remarks = $request->get('remarks');

		do {
			global $current_user;
			$db = PearDatabase::getInstance();
			$sql = "SELECT vtiger_schoolpracticalpeople.schoolresumeid, vtiger_schoolpracticalpeople.schoolpracticalpeopleid, vtiger_schoolpractical.schoolrecruitid,vtiger_schoolpractical.schooleligibilityid FROM vtiger_schoolpracticalpeople LEFT JOIN vtiger_schoolpractical ON vtiger_schoolpracticalpeople.practicalpeopleid=vtiger_schoolpractical.schoolpracticalid WHERE vtiger_schoolpracticalpeople.practicalpeopleid = ? AND vtiger_schoolpracticalpeople.is_goschoolemploy = 0 AND vtiger_schoolpracticalpeople.assessmentresult = 'assessmentresult_yes'";
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
	            $assessmentRequest->set('assessownerid', $request->get('assessownerid'));
	            $assessmentRequest->set('remarks', $request->get('remarks'));
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
        		$insertData[] = $request->get('assessownerid');
        		
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

		$response = new Vtiger_Response();
		$response->setResult(array('num'=>count($successSchoolId)));
		$response->emit();
		exit;
	}
	
}
