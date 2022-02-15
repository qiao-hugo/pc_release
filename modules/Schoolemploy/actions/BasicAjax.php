<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Schoolemploy_BasicAjax_Action extends Vtiger_Action_Controller {
	function __construct() {
		parent::__construct();
		$this->exposeMethod('addSchoolpractical');
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


	public function addSchoolpractical(Vtiger_Request $request) {
		$record = $request->get('record');
		//$assessownerid = $request->get('assessownerid');
		//$remarks = $request->get('remarks');

		$successSchoolId = array();

		do {
			global $current_user;
			$db = PearDatabase::getInstance();
			$sql = "SELECT vtiger_schooladoptpeople.schooladoptpeopleid, vtiger_schooladoptpeople.schooladoptid, vtiger_schooladoptpeople.schoolresumeid, vtiger_schooladoptpeople.instructor, vtiger_schooladopt.schoolrecruitid FROM vtiger_schooladoptpeople LEFT JOIN vtiger_schooladopt ON vtiger_schooladopt.schooladoptid=vtiger_schooladoptpeople.schooladoptid WHERE vtiger_schooladopt.schooladoptid=? AND vtiger_schooladoptpeople.assessmentresult='assessmentresult_yes' AND vtiger_schooladoptpeople.is_goschooladopt=0 ";
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

        	// 把简历数据 根据教官 分组
        	$instructorKeyData = array();
        	foreach ($schoolresumeData as $key=>$value) {
        		$instructorKeyData[$value['instructor']][] = $value;
        	}

        	foreach ($instructorKeyData as $key=>$value) {
        		// 判断有没有对应的实训名单
	        	$sql = "SELECT schoolpracticalid FROM vtiger_schoolpractical WHERE vtiger_schoolpractical.schoolrecruitid=? AND vtiger_schoolpractical.instructor=? LIMIT 1";
	        	$sel_result = $db->pquery($sql, array($schoolrecruitid, $key));
				$res_cnt    = $db->num_rows($sel_result);

				$schoolpracticalid = 0;
				if ($res_cnt > 0) {
					$tt = $db->query_result_rowdata($sel_result, 0);
					$schoolpracticalid = $tt['schoolpracticalid'];
				} else {
					$requestData = array();
					$assessmentRequest = new Vtiger_Request($requestData, $requestData);
		            $assessmentRequest->set('module', 'Schoolpractical');
		            $assessmentRequest->set('action', 'SaveAjax');
		            $assessmentRequest->set('record', '');
		            $assessmentRequest->set('schoolrecruitid', $schoolrecruitid);
		            $assessmentRequest->set('instructor', $key);
		            $ressorder = new Vtiger_SaveAjax_Action();
		            $ressorderecord = $ressorder->saveRecord($assessmentRequest);
		            $schoolpracticalid = $ressorderecord->getId();
				}

				$insertData = array();
				$sql = "INSERT INTO `vtiger_schoolpracticalpeople` (`schoolpracticalpeopleid`, `schoolresumeid`, `practicalpeopleid`, `assessmentresult`, `practicalresultinfo`, `practicalresultdate`, `instructor`) VALUES ";
				$insertData = array();
	        	foreach ($value as $v) {
	        		$sql .= "(?, ?, ?, ?, ?, ?, ?),";
	        		$insertData[] = ''; 
	        		$insertData[] = $v['schoolresumeid'];;
	        		$insertData[] = $schoolpracticalid;
	        		$insertData[] = '';
	        		$insertData[] = '';
	        		$insertData[] = '';
	        		$insertData[] = $key;

		            $successSchoolId[] = "'".$v['schooladoptpeopleid']."'";
	        	}
	        	$sql = trim($sql, ',');

	        	// 把添加成功的 简历合格人员改成 已考核
	        	if (count($successSchoolId) > 0) {
	        		$db->pquery($sql, $insertData);
	        		$sql = "UPDATE vtiger_schooladoptpeople SET is_goschooladopt=1 WHERE schooladoptpeopleid IN (". implode(',', $successSchoolId) .")";
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
