<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Staffcapacity_BasicAjax_Action extends Vtiger_Action_Controller {
	function __construct() {
		parent::__construct();
		$this->exposeMethod('getBusinessUser');
		$this->exposeMethod('isCheckTow');
	}
	
	function checkPermission(Vtiger_Request $request) {
		return;
	}


	public function isCheckTow(Vtiger_Request $request) {
		$recordId = $request->get('record'); //用户id
		$businessid = $request->get('businessid');

		$sql = "select * from vtiger_staffcapacity where businessid=? ";

		$db=PearDatabase::getInstance();
		$sel_result = $db->pquery($sql, array($businessid));
		$res_cnt = $db->num_rows($sel_result);


		$datas = array('is_check'=>0);

		if($res_cnt > 0) {
			$row = $db->query_result_rowdata($sel_result, 0);
			if ($row['staffcapacityid'] == $recordId) {  //可以修改
				$datas['is_check'] = 0;
			} else {
				$datas['is_check'] = 1;   //不可以修改
				$datas['message'] = "该商务人员已存在记录";
			}
		} 

		$response = new Vtiger_Response();
		$response->setResult($datas);
		$response->emit();
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

	public function getBusinessUser(Vtiger_Request $request) {
    	$recordId = $request->get('record'); //用户id
	    $db=PearDatabase::getInstance();

	    $sql = "SELECT
					vtiger_users.user_entered,
					vtiger_departments.departmentname
				FROM
					vtiger_users
				LEFT JOIN vtiger_user2department ON vtiger_user2department.userid = vtiger_users.id
				LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid = vtiger_departments.departmentid
				WHERE vtiger_users.id=?";
		$sel_result = $db->pquery($sql, array($recordId));
		$res_cnt = $db->num_rows($sel_result);
			
		$datas = array();

		if($res_cnt > 0) {
		    $row = $db->query_result_rowdata($sel_result, 0);
		    $datas['date_entered'] = $row['user_entered'];
		    $datas['departmentname'] = $row['departmentname'];
		}

		$response = new Vtiger_Response();
		$response->setResult($datas);
		$response->emit();
    }

}
