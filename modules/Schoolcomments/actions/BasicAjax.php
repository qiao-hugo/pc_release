<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Schoolcomments_BasicAjax_Action extends Vtiger_Action_Controller {
	function __construct() {
		parent::__construct();
		$this->exposeMethod('isCheckTow');
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

	public function isCheckTow(Vtiger_Request $request) {
		$recordId = $request->get('record'); //用户id
		$schoolname = $request->get('schoolname');

		$db=PearDatabase::getInstance();

		$datas = array('is_check'=>0);

		$sql        = "select schoolid,schoolname from vtiger_school where schoolname=?";
		$sel_result = $db->pquery($sql, array($schoolname));
		$res_cnt    = $db->num_rows($sel_result);

		if (empty($recordId)) {
			if ($res_cnt > 0) {
				$datas['is_check'] = 1;   //不可以修改
				$datas['message'] = "学校名称重复，请重新输入。";
			}
		} else {
			$row = $db->query_result_rowdata($sel_result, 0);
			if ($res_cnt > 0 && $row['schoolid'] != $recordId) {
				$datas['is_check'] = 1;   //不可以修改
				$datas['message'] = "学校名称重复，请重新输入。";
			}
		}
		$response = new Vtiger_Response();
		$response->setResult($datas);
		$response->emit();
	}

	
}
