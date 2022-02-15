<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Guarantee_BasicAjax_Action extends Vtiger_Action_Controller {
	function __construct() {
		parent::__construct();
		$this->exposeMethod('getGuarantee');

	}
	
	function checkPermission(Vtiger_Request $request) {
		return;
	}
	

	function getGuarantee(Vtiger_Request $request){
	    $salelesorder_no = $request->get('salelesorder_no');
        $salesorderidandconcatsid=Guarantee_Record_Model::getsalesoderid(array('salesorder_no'=>$salelesorder_no));
        if(!$salesorderidandconcatsid){
            $arr=array('msg'=>'没有找到该工单编号','flag'=>'no');
            $this->returnMessage($arr);
        }
        $arr['guaranteetotal']=Guarantee_Record_Model::getGuarantetotal();//能担保的总金额
        $arr['Guarantecurrentpay']=Guarantee_Record_Model::getGuarantecurrentpay();//已担保的总金额
        $receiveprice=Guarantee_Record_Model::getreceivedayprice($salesorderidandconcatsid['servicecontractsid'])-Guarantee_Record_Model::getoccupancyamount($salesorderidandconcatsid['servicecontractsid'],$salesorderidandconcatsid['salesorderid']);//对应回款的总金额
        $arr['receiveprice']=$receiveprice>=0?$receiveprice:0;//对应回款的总金额
        $arr['realprice']=Guarantee_Record_Model::getrealprice($salesorderidandconcatsid['salesorderid']);//工单对应的总成本
        $arr['salesorderguarante']=Guarantee_Record_Model::getGuarantecurrent($salesorderidandconcatsid['salesorderid']);//工单对应的总回款
        $arr['flag']='yes';
        if($arr['guaranteetotal']==0||$arr['guaranteetotal']<=$arr['Guarantecurrentpay']){
            $arr=array('msg'=>'没有足够担保金额','flag'=>'no');
            $this->returnMessage($arr);
        }
        $this->returnMessage($arr);

	}

	public function process(Vtiger_Request $request) {
		$mode = $request->getMode();
		if(!empty($mode)) {
			echo $this->invokeExposedMethod($mode, $request);
			return;
		}

	}
    public function returnMessage($arr){
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($arr);
        $response->emit();
        exit;
    }
}
