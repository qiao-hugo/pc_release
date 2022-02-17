<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Schooleligibility_BasicAjax_Action extends Vtiger_Action_Controller {
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
		//echo 111;die;
		$record = $request->get('record');
		//$assessownerid = $request->get('assessownerid');
		//$remarks = $request->get('remarks');

		$successSchoolId = array();

		do {
			global $current_user;
			$db = PearDatabase::getInstance();
			$sql = "SELECT vtiger_schoolinterqua.schoolinterquaid, vtiger_schooleligibility.schooleligibilityid,vtiger_schoolinterqua.schoolresumeid, vtiger_schoolinterqua.qsssessmentuserid FROM vtiger_schoolinterqua INNER JOIN vtiger_schooleligibility ON vtiger_schoolinterqua.schooleligibilityid=vtiger_schooleligibility.schooleligibilityid WHERE vtiger_schooleligibility.schooleligibilityid=? AND vtiger_schoolinterqua.qis_sssessment=1 AND vtiger_schoolinterqua.is_schoolassessment=0";
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

        	//$schoolrecruitid = $schoolresumeData[0]['schoolrecruitid']; //招聘计划id

        	// 把简历数据 根据考核人员 分组
        	$instructorKeyData = array();
        	foreach ($schoolresumeData as $key=>$value) {
        		$instructorKeyData[$value['qsssessmentuserid']][] = $value;
        	}

        	//print_r($instructorKeyData);die;

        	foreach ($instructorKeyData as $key=>$value) {

        		$schooleligibilityid = $value[0]['schooleligibilityid'];
        		$qsssessmentuserid = $value[0]['qsssessmentuserid'];

        		// 判断有没有对应的考核名单
	        	$sql = "SELECT assessmentid FROM vtiger_schoolassessment WHERE schooleligibilityid=? AND assessownerid=? LIMIT 1";
	        	$sel_result = $db->pquery($sql, array($schooleligibilityid, $key));
				$res_cnt    = $db->num_rows($sel_result);

				$assessmentid = 0;
				if ($res_cnt > 0) {
					$tt = $db->query_result_rowdata($sel_result, 0);
					$assessmentid = $tt['assessmentid'];
				} else {
					$requestData = array();
					$assessmentRequest = new Vtiger_Request($requestData, $requestData);
		            $assessmentRequest->set('module', 'Schoolassessment');
		            $assessmentRequest->set('action', 'SaveAjax');
		            $assessmentRequest->set('record', '');
		            $assessmentRequest->set('schoolrecruitid', '');
		            $assessmentRequest->set('shool_resume_source', 'bidding_agency');
		            $assessmentRequest->set('schooleligibilityid', $schooleligibilityid);
		            $assessmentRequest->set('assessownerid', $qsssessmentuserid);

		            
		            $ressorder = new Vtiger_SaveAjax_Action();
		            $ressorderecord = $ressorder->saveRecord($assessmentRequest);
		            $assessmentid = $ressorderecord->getId();

				}


				$insertData = array();
				$sql = "INSERT INTO `vtiger_schoolassessmentpeople` (`assessmentpeopleid`, `assessmentpeoplename`, `gendertype`, `telephone`, `email`, `assessmentresult`, `assessmentdate`, `assessmentdescribe`, `assessmentid`, `schoolresumeid`, `is_goadopt`) VALUES ";
				$insertData = array();
	        	foreach ($value as $v) {
	        		$sql .= "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?),";
	        		$insertData[] = ''; 
	        		$insertData[] = '';
	        		$insertData[] = '';
	        		$insertData[] = '';
	        		$insertData[] = '';
	        		$insertData[] = '';
	        		$insertData[] = '';
	        		$insertData[] = '';
	        		$insertData[] = $assessmentid;
	        		$insertData[] = $v['schoolresumeid'];
	        		$insertData[] = '0';

		            $successSchoolId[] = "'".$v['schoolinterquaid']."'";
	        	}
	        	$sql = trim($sql, ',');

	        	// 把添加成功的 简历合格人员改成 已考核
	        	if (count($successSchoolId) > 0) {
	        		$db->pquery($sql, $insertData);
	        		$sql = "UPDATE vtiger_schoolinterqua SET is_schoolassessment=1 WHERE schoolinterquaid IN (". implode(',', $successSchoolId) .")";
	        		$db->pquery($sql, array());
	        	}
        	}
        	
      	} while (0);

		$response = new Vtiger_Response();
		$response->setResult(array('num'=>count($successSchoolId)));
		$response->emit();
		exit;
	}
	
}
