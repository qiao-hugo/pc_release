<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Schooladoptpeople_BasicAjax_Action extends Vtiger_Action_Controller {
	function __construct() {
		parent::__construct();
		$this->exposeMethod('getCreateuser');
		$this->exposeMethod('isCheckTow');
		$this->exposeMethod('addSchoolqualified');
		$this->exposeMethod('get_adoptpeople_data');
		$this->exposeMethod('set_assessmentresult');
	}
	
	function checkPermission(Vtiger_Request $request) {
		return;
	}

	public function set_assessmentresult(Vtiger_Request $request) {
		$records = $request->get('records');
		$assessmentdate = $request->get('assessmentdate');
		$assessmentresult = $request->get('assessmentresult');
		$instructor = $request->get('instructor');
		$records=explode(',',$records);
		foreach($records as $record) {
            $sql = "update vtiger_schooladoptpeople set instructor=?,assessmentresult=?,assessmentdate=? where schooladoptpeopleid=?";
            $db = PearDatabase::getInstance();
            $db->pquery($sql, array($instructor, $assessmentresult, $assessmentdate, $record));
            if ($assessmentresult == 'assessmentresult_yes') {
                $this->addSchoolpracticalById($record);
            }
        }
		$response = new Vtiger_Response();
		$response->setResult(array());
		$response->emit();
	}

	public function get_adoptpeople_data(Vtiger_Request $request) {
		$record = $request->get('record');
		$sql = "select instructor,assessmentresult,assessmentdate from vtiger_schooladoptpeople where schooladoptpeopleid=?";
		$db=PearDatabase::getInstance();
		$sel_result = $db->pquery($sql, array($record));
		$res_cnt    = $db->num_rows($sel_result);
		$schooladoptpeople = array();
		if ($res_cnt > 0) {
			$schooladoptpeople = $db->query_result_rowdata($sel_result, 0);
		}
		$response = new Vtiger_Response();
		$response->setResult($schooladoptpeople);
		$response->emit();
	}


	private function addSchoolpracticalById($schooladoptpeopleid) {
		//$record = $request->get('record');
		//$assessownerid = $request->get('assessownerid');
		//$remarks = $request->get('remarks');

		$successSchoolId = array();

		do {
			global $current_user;
			$db = PearDatabase::getInstance();
			$sql = "SELECT vtiger_schooladoptpeople.schooladoptpeopleid, vtiger_schooladoptpeople.schooladoptid, vtiger_schooladoptpeople.schoolresumeid, vtiger_schooladoptpeople.instructor, vtiger_schooladopt.schoolrecruitid,vtiger_schooladopt.schooleligibilityid FROM vtiger_schooladoptpeople LEFT JOIN vtiger_schooladopt ON vtiger_schooladopt.schooladoptid=vtiger_schooladoptpeople.schooladoptid WHERE vtiger_schooladoptpeople.schooladoptpeopleid=? AND vtiger_schooladoptpeople.assessmentresult='assessmentresult_yes' AND vtiger_schooladoptpeople.is_goschooladopt=0 ";

			$sel_result = $db->pquery($sql, array($schooladoptpeopleid));
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

        	if ( ! ( $schoolrecruitid || $schooleligibilityid )) {
        		break;
        	}
        	
        	

        	// 把简历数据 根据教官 分组
        	$instructorKeyData = array();
        	foreach ($schoolresumeData as $key=>$value) {
        		$instructorKeyData[$value['instructor']][] = $value;
        	}

        	foreach ($instructorKeyData as $key=>$value) {
        		if (!empty($schoolrecruitid)) {
        			$_sql = " vtiger_schoolpractical.schoolrecruitid=? AND vtiger_schoolpractical.instructor=? ";
        			$sqlArr = array($schoolrecruitid, $key);
        		}
        		if (!empty($schooleligibilityid)) {
	        		$_sql = " vtiger_schoolpractical.instructor=? AND vtiger_schoolpractical.schooleligibilityid=? ";
	        		$sqlArr = array($key, $schooleligibilityid);
	        	}
        		// 判断有没有对应的实训名单
	        	$sql = "SELECT schoolpracticalid FROM vtiger_schoolpractical WHERE {$_sql}  LIMIT 1";
	        	$sel_result = $db->pquery($sql, $sqlArr);
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
		            $assessmentRequest->set('schooleligibilityid', $schooleligibilityid);
		            $assessmentRequest->set('instructor', $key);
		            $ressorder = new Vtiger_SaveAjax_Action();
		            $ressorderecord = $ressorder->saveRecord($assessmentRequest);
		            $schoolpracticalid = $ressorderecord->getId();
				}

				$insertData = array();
				$sql = "INSERT INTO `vtiger_schoolpracticalpeople` (`schoolpracticalpeopleid`, `schoolresumeid`, `practicalpeopleid`, `assessmentresult`, `practicalresultinfo`, `practicalresultdate`, `instructor`, `p_schoolrecruitid`) VALUES ";
				$insertData = array();
	        	foreach ($value as $v) {
	        		$sql .= "(?, ?, ?, ?, ?, ?, ?, ?),";
	        		$insertData[] = ''; 
	        		$insertData[] = $v['schoolresumeid'];;
	        		$insertData[] = $schoolpracticalid;
	        		$insertData[] = '';
	        		$insertData[] = '';
	        		$insertData[] = '';
	        		$insertData[] = $key;
	        		$insertData[] = $schoolrecruitid;
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
