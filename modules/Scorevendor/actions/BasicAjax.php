<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Scorevendor_BasicAjax_Action extends Vtiger_Action_Controller {
	function __construct() {
		parent::__construct();
		$this->exposeMethod('makeScorepapers');
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

	public function makeScorepapers(Vtiger_Request $request) {
		$recordId = $request->get('record'); //用户id
		$db=PearDatabase::getInstance();
		
		// 1. 判断当前的供应商模型是否 生效
		$sql = "SELECT * FROM vtiger_scoremodel WHERE vtiger_scoremodel.scoremodelid = (
						SELECT vtiger_scorevendor.scoremodelid FROM vtiger_scorevendor WHERE vtiger_scorevendor.scorevendorid=?)";

		$datas = array();
		$sel_result = $db->pquery($sql, array($recordId));
		$res_cnt = $db->num_rows($sel_result);
		if($res_cnt > 0) {
		    $row = $db->query_result_rowdata($sel_result, 0);
		    if($row['is_effect'] == '0') { //没有生效
		    	$datas['flag'] = false;
		    	$datas['msg'] = '供应商模型没有激活，不能生成评分问卷';
		    } else {
		    	$datas['flag'] = true;
		    }
		}

		$response = new Vtiger_Response();
		$response->setResult($datas);
		$response->emit();
	}

	// 生成试卷
	public function makePaper() {
		
	}

	
}
