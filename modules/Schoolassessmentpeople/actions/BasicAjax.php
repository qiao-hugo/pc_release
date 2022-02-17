<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Schoolassessmentpeople_BasicAjax_Action extends Vtiger_Action_Controller {
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

	private function addSchooladoptById($assessmentpeopleid, $assessownerid) {
		//$record = $request->get('record');
		//$assessownerid = $request->get('assessownerid');
		$remarks = '';

		$successSchoolId = array();
		do {
			global $current_user;
			$db = PearDatabase::getInstance();
			$sql = "SELECT r.gendertype, r.`name`, r.telephone, r.email, s.assessmentpeopleid, s.schoolresumeid, s.assessmentresult, s.assessmentdate,s.assessmentid FROM vtiger_schoolassessmentpeople s INNER JOIN vtiger_schoolresume r ON s.schoolresumeid = r.schoolresumeid WHERE s.assessmentpeopleid = ? AND s.is_goadopt = 0 AND s.assessmentresult='assessmentresult_yes'";
			$sel_result = $db->pquery($sql, array($assessmentpeopleid));
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
        	$sel_result = $db->pquery($sql, array($schoolresumeData[0]['assessmentid']));
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
	            $assessmentRequest->set('chargeperson', $assessownerid);
	            $assessmentRequest->set('remarks', $remarks);
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

        		$insertData[] = $assessownerid;
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

		
	}


	public function setAssessmentresult(Vtiger_Request $request) {
		$records = $request->get('records');
		$status = $request->get('status');
		$assessmentdate = $request->get('assessmentdate');
		$assessownerid = $request->get('assessownerid');
		// 判断是否有权限
		//$flag = Users_Privileges_Model::isPermitted('Schoolassessmentpeople', 'NegativeEdit');
		$flag = true;
        global $current_user;
        $db=PearDatabase::getInstance();
        $is_admin = $current_user->is_admin == 'on' ? 1 : 0;
        $data = array('flag'=>0);
        if ($flag || $is_admin) {
            $records=explode(',',$records);
            foreach($records as $record){
        	$sql = "UPDATE vtiger_schoolassessmentpeople SET assessmentresult=?,assessmentdate=? WHERE assessmentpeopleid=? LIMIT 1";
        	$db->pquery($sql, array($status, $assessmentdate, $record));
			$data['flag'] = 1;

                if (!empty($assessownerid) && $status == 'assessmentresult_yes') {
                    // 更新 考核通过名单
                    $this->addSchooladoptById($record, $assessownerid);
                }
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
