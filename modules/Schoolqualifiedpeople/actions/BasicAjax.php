<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Schoolqualifiedpeople_BasicAjax_Action extends Vtiger_Action_Controller {
	function __construct() {
		parent::__construct();
		$this->exposeMethod('getCreateuser');
		$this->exposeMethod('isCheckTow');
		$this->exposeMethod('addSchoolqualified');
		$this->exposeMethod('set_report');
		$this->exposeMethod('set_train');
		$this->exposeMethod('get_train_data');
		$this->exposeMethod('set_trainer');

	}
	
	function checkPermission(Vtiger_Request $request) {
		return;
	}

	public function get_train_data(Vtiger_Request $request) {
		$record = $request->get('record');
		$sql = "select is_train,is_assessment,is_trainok,trainstartdate,trainenddate,assessmentuser from vtiger_schoolqualifiedpeople where schoolqualifiedpeopleid=? ";
		$db = PearDatabase::getInstance();
		$sel_result = $db->pquery($sql, array($record));
		$res_cnt    = $db->num_rows($sel_result);
		$resData = array();
		if ($res_cnt > 0) {
			$resData= $db->query_result_rowdata($sel_result, 0);
		}
		$response = new Vtiger_Response();
		$response->setResult($resData);
		$response->emit();
	}

	public function set_train(Vtiger_Request $request) {
		

		$setData = array();
		$records = $request->get('records');
		$setData['is_train'] = $request->get('is_train');
		$setData['is_assessment'] = $request->get('is_assessment');
		$setData['is_trainok'] = $request->get('is_trainok');
		$setData['trainstartdate'] = $request->get('trainstartdate');
		$setData['trainenddate'] = $request->get('trainenddate');
		$setData['assessmentuser'] = $request->get('assessmentuser');

		$sql = " UPDATE vtiger_schoolqualifiedpeople set ";
		if ($records) {
			// 这块地方 还没有写  设置权限
			$db = PearDatabase::getInstance();
			foreach ($setData as $key=>$value) {
				if(!empty($value)) {
					$sql .= "{$key}=?,";
				} else {
					unset($setData[$key]);
				}
			}
			$sql = trim($sql, ',');
			$sql .= " WHERE schoolqualifiedpeopleid in({$records}) ";
			$db->pquery($sql, array_values($setData));


			// 更新简历
			$sql = "UPDATE vtiger_schoolresume SET is_train =?, is_train_ok =? WHERE schoolresumeid IN ( SELECT schoolresumeid FROM vtiger_schoolqualifiedpeople WHERE schoolqualifiedpeopleid in({$records}) LIMIT 1)";
			$db->pquery($sql, array($setData['is_train'], $setData['is_train_ok']));

			if ($setData['is_trainok'] == 1 && $setData['assessmentuser'] > 0) {
				$this->addSchoolassessment($records);
			}
		}

		$response = new Vtiger_Response();
		$response->setResult(array());
		$response->emit();
	}


	public function addSchoolassessment($records) {

		global $current_user;
		$db = PearDatabase::getInstance();
        $records=explode(',',$records);
        foreach($records as $record) {
            $sql = "SELECT vtiger_schoolqualifiedpeople.schoolqualifiedid,vtiger_schoolresume.gendertype, vtiger_schoolresume.`name`, vtiger_schoolresume.telephone, vtiger_schoolresume.email, vtiger_schoolresume.schoolresumeid, vtiger_schoolqualifiedpeople.schoolqualifiedpeopleid, vtiger_schoolqualifiedpeople.schoolresumeid, vtiger_schoolqualifiedpeople.assessmentuser FROM vtiger_schoolqualifiedpeople INNER JOIN vtiger_schoolresume ON vtiger_schoolqualifiedpeople.schoolresumeid = vtiger_schoolresume.schoolresumeid WHERE vtiger_schoolqualifiedpeople.schoolqualifiedpeopleid = '{$record}' AND vtiger_schoolqualifiedpeople.is_goassessment = 0 AND vtiger_schoolqualifiedpeople.assessmentuser > 0 AND vtiger_schoolqualifiedpeople.is_assessment=1";
            $sel_result = $db->pquery($sql, array());
            $res_cnt = $db->num_rows($sel_result);
            if ($res_cnt == 0) {
                continue;
            }

		$schoolqualifiedid = '-1';
		// 获取简历数据
		$schoolresumeData = array();
		while($rawData=$db->fetch_array($sel_result)) {
        	$schoolresumeData[$rawData['assessmentuser']][] = $rawData;
        	$schoolqualifiedid = $rawData['schoolqualifiedid'];
    	}


            // 获取 招聘计划
            $sql = "SELECT vtiger_schoolqualified.schoolrecruitid FROM vtiger_schoolqualified WHERE vtiger_schoolqualified.schoolqualifiedid=? LIMIT 1";
            $sel_result = $db->pquery($sql, array($schoolqualifiedid));
            $res_cnt = $db->num_rows($sel_result);
            if ($res_cnt == 0) {
                continue;
            }
            $row = $db->query_result_rowdata($sel_result, 0);
            $schoolrecruitid = $row['schoolrecruitid']; //招聘计划id

    	$successSchoolId = array();

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
                $sql = "UPDATE vtiger_schoolqualifiedpeople SET is_goassessment=1 WHERE schoolqualifiedpeopleid IN (" . implode(',', $successSchoolId) . ")";
                $db->pquery($sql, array());
            }
        }
	}

	public function set_report(Vtiger_Request $request) {
		$reportsdate = $request->get('reportsdate');
		$records = $request->get('records');

		if ($records && $reportsdate) {
			// 这块地方 还没有写  设置权限
			$db=PearDatabase::getInstance();
			$sql = " UPDATE vtiger_schoolqualifiedpeople set is_report=1,reportdate=? WHERE schoolqualifiedpeopleid in({$records})";
			$db->pquery($sql, array($reportsdate));
		}

		$response = new Vtiger_Response();
		$response->setResult(array());
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
	public function set_trainer(Vtiger_Request $request)
    {
        $records=$request->get('records');
        $trainer=$request->get('assessmentuser');
        $sql='UPDATE vtiger_schoolqualifiedpeople SET trainer=? WHERE schoolqualifiedpeopleid in('.$records.')';
        $db=PearDatabase::getInstance();
        $db->pquery($sql,array($trainer));
        $response = new Vtiger_Response();
        $response->setResult(array());
        $response->emit();

    }
	
}
