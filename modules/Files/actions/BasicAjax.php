<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Files_BasicAjax_Action extends Vtiger_Action_Controller {
	function __construct() {
		parent::__construct();
		$this->exposeMethod('getBusinessUser');
		$this->exposeMethod('isCheckTow');
		$this->exposeMethod('weebSalesSubmit');
		$this->exposeMethod('weebSalesReturn');
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
	    //2015-1-23 wangbin 合并两次请求ajax
	    /*$recordId = $request->get('record');//合同的id
		$salesorderid = $request->get('salesorderid');//工单的id 编辑模式
	    $db=PearDatabase::getInstance();
		//查询合同下产品信息
		$sql = 'SELECT `productid`, `productform`, salesorderid, IFNULL(( SELECT vtiger_products.productname FROM vtiger_products WHERE vtiger_products.productid = vtiger_salesorderproductsrel.productcomboid ), \'--\' ) AS productcomboid FROM vtiger_salesorderproductsrel WHERE servicecontractsid =  ? AND (vtiger_salesorderproductsrel.multistatus=0 OR vtiger_salesorderproductsrel.multistatus=1)';
        $product = $db->pquery($sql,array($recordId));
	    $productids = $db->num_rows($product);

		$datas=array($productidlist,$return,$package,$isEditForm);
		$response = new Vtiger_Response();
		$response->setResult($datas);
		$response->emit();*/
	}

	// 周销售目标提交
	public function weebSalesSubmit(Vtiger_Request $request) {
		$data = array();
		$data['salestargetdetailid'] = $request->get('salestargetdetailid');
		$data['weekNum'] = $request->get('weekNum');
		$data['startdate'] = $request->get('startdate');
		$data['enddate'] = $request->get('enddate');

		$data['weekinvitationtarget'] = $request->get('weekinvitationtarget');
		$data['weekinvitationremarks'] = $request->get('weekinvitationremarks');

		$data['weekvisittarget'] = $request->get('weekvisittarget');
		$data['weekvisitrateremarks'] = $request->get('weekvisitrateremarks');

		$data['weekachievementtargt'] = $request->get('weekachievementtargt');
		$data['weekachievementremarks'] = $request->get('weekachievementremarks');
		$data['programme'] = $request->get('programme');
		$data['ismodify'] = '1';

		$tt = array();
		foreach ($data as $key => $value) {
			$tt[] = $key . "='{$value}'";
		}

		$sql = "update vtiger_salestargetdetail set " . implode(',', $tt) . " where salestargetdetailid=?";

		$db=PearDatabase::getInstance();
		$sel_result = $db->pquery($sql, array($data['salestargetdetailid']));

		// 设置销售目标为不可修改
		$salestargetid = $request->get('salestargetid');
		$sql = "update vtiger_salestarget set ismodify=1 where salestargetid=?";
		$db->pquery($sql, array($salestargetid));

		$response = new Vtiger_Response();
		$response->setResult(array('success'=>1, 'message'=>'提交成功'));
		$response->emit();
	}

	public function isCheckTow(Vtiger_Request $request) {
		$recordId = $request->get('record'); //用户id
		$year = $request->get('year');
		$month = $request->get('month');
		$businessid = $request->get('businessid');

		$sql = "select * from vtiger_salestarget where year=? AND month=? AND businessid=? ";

		$db=PearDatabase::getInstance();
		$sel_result = $db->pquery($sql, array($year, $month, $businessid));
		$res_cnt = $db->num_rows($sel_result);


		$datas = array('is_check'=>0);

		if($res_cnt > 0) {
			$row = $db->query_result_rowdata($sel_result, 0);
			if ($row['salestargetid'] == $recordId) {  //可以修改
				$datas['is_check'] = 0;
			} else {
				$datas['is_check'] = 1;   //不可以修改
				$datas['message'] = "该商务人员这个月份已经存在记录了";
			}
		} 

		$response = new Vtiger_Response();
		$response->setResult($datas);
		$response->emit();
	}

	public function weebSalesReturn(Vtiger_Request $request) {
		$salestargetdetailid = $request->get('salestargetdetailid');
		global $current_user;
		$db=PearDatabase::getInstance();
		$sql = "SELECT
					vtiger_users.reports_to_id
				FROM
					vtiger_salestarget
				LEFT JOIN vtiger_salestargetdetail ON vtiger_salestarget.salestargetid = vtiger_salestargetdetail.salestargetid
				LEFT JOIN vtiger_users ON vtiger_salestarget.businessid=vtiger_users.id
				WHERE vtiger_salestargetdetail.salestargetdetailid=? LIMIT 1";
		$sel_result = $db->pquery($sql, array($salestargetdetailid));
		$res_cnt = $db->num_rows($sel_result);
		if ($res_cnt > 0) {
			$row = $db->query_result_rowdata($sel_result, 0);
			if ($row['reports_to_id'] == $current_user->id) {
				// 修改
				$sql = "update vtiger_salestargetdetail set ismodify=0 where salestargetdetailid=?";
				$db->pquery($sql, array($salestargetdetailid));
				$response = new Vtiger_Response();
				$response->setResult(array('success'=>1, 'message'=>'驳回成功'));
				$response->emit();
			}
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
