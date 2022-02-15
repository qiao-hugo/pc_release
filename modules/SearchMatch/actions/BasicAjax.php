<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
class SearchMatch_BasicAjax_Action extends Vtiger_Action_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->exposeMethod('getCanMatchServiceContracts');
        $this->exposeMethod('getStayPaymentByTitle');
        $this->exposeMethod('goMatch');
        $this->exposeMethod('getAccountName');
    }

    function checkPermission(Vtiger_Request $request)
    {
        return;
    }

    /**
     * @param Vtiger_Request $request
     * @throws Exception
     */
    public function process(Vtiger_Request $request)
    {
        $mode = $request->getMode();
        if (!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
            return;
        }
    }


    public function getCanMatchServiceContracts(Vtiger_Request $request)
    {
        global $adb, $current_user;
        $receivedpaymentsid = $request->get('record');
        $currentId = $current_user->id;
        $sql = "SELECT
        t1.receivedpaymentsid,
		t1.paytitle,
		t1.unit_price,
		t1.reality_date,
		t1.paymentcode,
		t2.contract_no,
		t4.accountname,
		IFNULL((SELECT 1 FROM vtiger_shareaccount WHERE vtiger_shareaccount.userid = ? AND vtiger_shareaccount.sharestatus = 1 AND vtiger_shareaccount.accountid = t2.sc_related_to),0) AS shareuser,
		t2.servicecontractsid,
		t4.accountid
FROM
	vtiger_receivedpayments t1
	LEFT JOIN vtiger_account t4 ON t1.paytitle = t4.accountname
	LEFT JOIN vtiger_servicecontracts t2 ON  t4.accountid = t2.sc_related_to ## 回款可能客户 = 服务合同里面的客户
	LEFT JOIN vtiger_crmentity t3 ON t4.accountid = t3.crmid
WHERE
	(
		t4.serviceid = ? or t2.receiveid = ? or t3.smownerid = ? or EXISTS ( SELECT 1 FROM vtiger_shareaccount WHERE vtiger_shareaccount.userid =? AND vtiger_shareaccount.sharestatus = 1 AND vtiger_shareaccount.accountid = t2.sc_related_to)
	)
	and t2.modulestatus not in ('c_cancel','c_canceling','a_exception','c_stop','c_completeclosed')
	and (t1.relatetoid = '' OR t1.relatetoid = 0 OR t1.relatetoid IS NULL) 
	and t2.servicecontractsid != '' ## 合同不能为空
	and t2.sideagreement=0
	and t2.contract_no!=''
	and t2.contractstate = '0' ## 合同的关闭状态为正常
	and t1.receivedpaymentsid=?";
        $result = $adb->pquery($sql, array($currentId, $currentId, $currentId, $currentId, $currentId, $receivedpaymentsid));
        $data = array();
        $return = array();
        $return['data'] = array();
        if ($adb->num_rows($result) > 0) {
            for ($i = 0; $i < $adb->num_rows($result); $i++) {
                $res = $adb->fetchByAssoc($result, $i);
                $return['data']['contractList'][$res['servicecontractsid']] = $res['contract_no'];
                $data[] = $res;
            }
            $data[0]['matchtype'] = 1;
        }
        if (!$data) {
            //如果没有的话（不能直接匹配）直接查合同和基础信息
            $sql = "select receivedpaymentsid,paytitle,unit_price,reality_date,paymentcode from vtiger_receivedpayments where receivedpaymentsid=" . $receivedpaymentsid;
            $data = $adb->run_query_allrecords($sql);
            $data[0]['matchtype'] = 0;
            $data[0]['accountname'] = '--';
            //合同
            $sql = "SELECT
	vtiger_servicecontracts.servicecontractsid,
	vtiger_servicecontracts.contract_no 
FROM
	vtiger_servicecontracts
	LEFT JOIN vtiger_account ON vtiger_servicecontracts.sc_related_to = vtiger_account.accountid
	LEFT JOIN vtiger_crmentity ON vtiger_account.accountid = vtiger_crmentity.crmid 
WHERE
	(
		vtiger_crmentity.smownerid = " . $currentId . " 
		OR vtiger_account.serviceid = ".$currentId ." 
		OR vtiger_servicecontracts.receiveid = ".$currentId ." 
		OR vtiger_servicecontracts.signid = ".$currentId ." 
		OR EXISTS ( SELECT 1 FROM vtiger_shareaccount WHERE vtiger_shareaccount.userid = ".$currentId ." AND vtiger_shareaccount.sharestatus = 1 AND vtiger_shareaccount.accountid = vtiger_servicecontracts.sc_related_to ) 
	) 
	AND vtiger_servicecontracts.modulestatus NOT IN ( 'c_cancel', 'c_canceling', 'a_exception', 'c_stop', 'c_completeclosed' ) 
	AND vtiger_servicecontracts.contractstate = 0 
	AND vtiger_servicecontracts.sideagreement = 0 
	AND vtiger_servicecontracts.contract_no != ''";
            $serviceArray = $adb->run_query_allrecords($sql);
            foreach ($serviceArray as $key => $service) {
                $return['data']['contractList'][$service['servicecontractsid']] = $service['contract_no'];
            }
        }
        $data[0]['totelMoney']='--';
        $data[0]['receivedMoney']='--';
        $data[0]['remainMoney']='--';
//        if($data[0]['accountname']){
//            $recordModel=new SearchMatch_Record_Model();
//            $accountMoneyArray=$recordModel->getAccountMoneyArray($data[0]['accountid']);
//            $data[0]['totelMoney']=$accountMoneyArray['paymentTotal'];
//            $data[0]['receivedMoney']=$accountMoneyArray['paymentReceived'];
//            $data[0]['remainMoney']=$accountMoneyArray['paymentElse'];
//        }
        $data[0]['servicecontracts'] = $return['data']['contractList'];
        $response = new Vtiger_Response();
        $response->setResult($data[0]);
        $response->emit();
    }

    /**
     *查询代付款通过抬头
     * @param Vtiger_Request $request
     */
    public function getStayPaymentByTitle(Vtiger_Request $request)
    {
        global $adb;
        $servicecontractsid = $request->get('servicecontractsid');
        $payTitle = $request->get('payTitle');
        $recordId = $request->get('recordId');
        $recordModel=Vtiger_Record_Model::getInstanceById($recordId,'ReceivedPayments',true);
        $channel=$recordModel->get('paymentchannel');
        $reality_date=$recordModel->get('reality_date');
        $unit_price=$recordModel->get('unit_price');
        if($channel=='支付宝转账'){
            $sql = "SELECT
	 vtiger_staypayment.staypaymentid,
	 vtiger_staypayment.staypaymentname,
	 vtiger_staypayment.payer,
	 IF(vtiger_staypayment.staypaymenttype = 'nofixation','--',vtiger_staypayment.staypaymentjine) as staypaymentjine,
	 IF(vtiger_staypayment.staypaymenttype = 'nofixation','--',vtiger_staypayment.surplusmoney) as surplusmoney
FROM
	vtiger_staypayment
    LEFT JOIN vtiger_receivedpayments ON ( (REPLACE(vtiger_staypayment.staypaymentname,' ','') =  REPLACE(vtiger_receivedpayments.paytitle,' ','') and vtiger_staypayment.staypaymentname!='' ) or (REPLACE(vtiger_staypayment.payer,' ','') like REPLACE(REPLACE(vtiger_receivedpayments.paytitle,' ',''),'*','_')) )
WHERE
	(vtiger_staypayment.payer = '" . $payTitle . "' or vtiger_staypayment.staypaymentname = '" . $payTitle . "')
	AND vtiger_staypayment.contractid = '" . $servicecontractsid . "' 
	AND vtiger_receivedpayments.receivedpaymentsid = '" . $recordId . "'
	and vtiger_staypayment.modulestatus !='a_exception' 
	AND (
		( vtiger_staypayment.staypaymenttype = 'nofixation' AND vtiger_receivedpayments.reality_date >= vtiger_staypayment.startdate AND vtiger_receivedpayments.reality_date <= vtiger_staypayment.enddate ) 
		OR ( vtiger_staypayment.staypaymenttype = 'fixation' AND vtiger_receivedpayments.unit_price <= vtiger_staypayment.surplusmoney AND vtiger_staypayment.surplusmoney > 0 ) 
	)
    AND 
    (
        (REPLACE(vtiger_staypayment.payer,' ','') like REPLACE(REPLACE(vtiger_receivedpayments.paytitle,' ',''),'*','_'))
        or
        (REPLACE(vtiger_staypayment.staypaymentname,' ','') =  REPLACE(vtiger_receivedpayments.paytitle,' ','')) 
    )
	";
        }else{
            $sql="SELECT
	 vtiger_staypayment.staypaymentid,
	 vtiger_staypayment.staypaymentname,
	 vtiger_staypayment.payer,
	 vtiger_staypayment.staypaymenttype,
	 IF(vtiger_staypayment.staypaymenttype = 'nofixation','--',vtiger_staypayment.staypaymentjine) as staypaymentjine,
	 IF(vtiger_staypayment.staypaymenttype = 'nofixation','--',vtiger_staypayment.surplusmoney) as surplusmoney
FROM
	vtiger_staypayment
WHERE
	(vtiger_staypayment.payer = '" . $payTitle . "' or vtiger_staypayment.staypaymentname = '" . $payTitle . "')
	AND vtiger_staypayment.contractid = '" . $servicecontractsid . "' 
	and vtiger_staypayment.modulestatus !='a_exception' 
	AND (
		( vtiger_staypayment.staypaymenttype = 'nofixation' AND '".$reality_date."' >= vtiger_staypayment.startdate AND '".$reality_date."' <= vtiger_staypayment.enddate ) 
		OR ( vtiger_staypayment.staypaymenttype = 'fixation' AND '".$unit_price."' <= vtiger_staypayment.surplusmoney AND vtiger_staypayment.surplusmoney > 0 ) 
	)";
        }
        $data = $adb->run_query_allrecords($sql);;
        $response = new Vtiger_Response();
        $response->setResult($data);
//        $response->setResult(array());
        $response->emit();
    }


    /**
     * 去匹配
     * @param Vtiger_Request $request
     */
    public function goMatch(Vtiger_Request $request)
    {
        global $adb, $current_user;
        $staypaymentid = $request->get("staypaymentid");
        $contractid = $request->get('contractid');
        $total = $request->get('total');//回
        $paytitle = $request->get('paytitle');
        $source = $request->get('source');
        $payer = $request->get('payer');

        if (is_numeric($staypaymentid) && $staypaymentid == 0) {
            //需要重新生成
            $sql = "select effectivetime,sc_related_to,companycode from  vtiger_servicecontracts where servicecontractsid=" . $contractid;
            $contractInfo = $adb->run_query_allrecords($sql);
            CRMEntity::insertIntoCrmEntity('Staypayment', '');
            $stayPaymentArray['staypaymentid'] = $_REQUEST['currentid'];
            $stayPaymentArray['contractid'] = $contractid;
            $stayPaymentArray['staypaymenttype'] = 'fixation';
            $stayPaymentArray['overdue'] = '自动匹配，模拟新建';
            $stayPaymentArray['modulename'] = 'ServiceContracts';
            $stayPaymentArray['modulestatus'] = 'a_normal';
            $stayPaymentArray['companycode'] = $contractInfo[0]['companycode'];
            $stayPaymentArray['staypaymentjine'] = $total;
            $stayPaymentArray['accountid'] = $contractInfo[0]['sc_related_to'];
            $stayPaymentArray['overdute'] = $contractInfo[0]['effectivetime'];
            $stayPaymentArray['staypaymentname'] = $paytitle;
            $stayPaymentArray['surplusmoney'] = $total;
            $stayPaymentArray['payer'] = $payer;
            $stayPaymentArray['staymentcode'] = 'DFK000' . $stayPaymentArray['staypaymentid'];
            $stayPaymentArray['last_sign_time']=Staypayment_Record_Model::getLastSignTime();
            $stayPaymentArray['isauto'] = 1;
            $adb->run_insert_data('vtiger_staypayment', $stayPaymentArray);
            $request->set('staypaymentid', $_REQUEST['currentid']);
        }
        $request->set('notestype', 'notestype4');
        $matchObject = new Matchreceivements_BasicAjax_Action();
        if ($source && $source == 'app') {
            $result = $matchObject->process($request);
            $return = array('flag' => true);
            if (!$result['flag']) {
                $return['flag'] = false;
                $return['data']['errorMsg'] = $result['msg'];
            }else{
                //如果是成功的发微信通知
                $receivepayid=$request->get('receivepayid');
                $this->sendQiyeWeixin($receivepayid);
            }
            return $return;
        }
        $request->set('mode', '');
        $matchObject->process($request);
    }

    /**
     * 发送微信通知
     * @param $id
     */
    public function sendQiyeWeixin($id){
        global $adb;
        $sql="SELECT vtiger_receivedpayments.paytitle,vtiger_receivedpayments.paymentcode,vtiger_receivedpayments.reality_date,vtiger_receivedpayments.unit_price,vtiger_account.accountname,vtiger_servicecontracts.servicecontractsid,vtiger_crmentity.smownerid,vtiger_servicecontracts.signid,vtiger_servicecontracts.receiveid,vtiger_servicecontracts.sideagreement,vtiger_servicecontracts.isstage,vtiger_servicecontracts.contract_no FROM vtiger_receivedpayments LEFT JOIN vtiger_servicecontracts ON vtiger_receivedpayments.relatetoid=vtiger_servicecontracts.servicecontractsid LEFT JOIN vtiger_account ON vtiger_servicecontracts.sc_related_to=vtiger_account.accountid LEFT JOIN vtiger_crmentity ON vtiger_account.accountid=vtiger_crmentity.crmid where vtiger_receivedpayments.receivedpaymentsid=?";
        $result=$adb->pquery($sql,array($id));
        $accountName=$adb->query_result($result,0,'accountname');
        $paytitleOrPaycode=$adb->query_result($result,0,'paytitle');
        if(!$paytitleOrPaycode){
            $paytitleOrPaycode=$adb->query_result($result,0,'paymentcode');
        }
        $reality_date=$adb->query_result($result,0,'reality_date');
        $unit_price=$adb->query_result($result,0,'unit_price');
        $servicecontractsid=$adb->query_result($result,0,'servicecontractsid');
        $snowId=$adb->query_result($result,0,'smownerid');
        $signid=$adb->query_result($result,0,'signid');
        $receiveid=$adb->query_result($result,0,'receiveid');
        $contract_no=$adb->query_result($result,0,'contract_no');
        $sideagreement=$adb->query_result($result,0,'sideagreement');
        $isstage=$adb->query_result($result,0,'isstage');
        $recordModel=new SearchMatch_Record_Model();
        $accountMoneyArray=$recordModel->getAccountMoneyArray($servicecontractsid);
        $accountMoneyArray['leastPayMoney']=$accountMoneyArray['paymentElse'];;
        if($isstage){
            $recordModel=new ServiceContracts_Record_Model();
            $leastPayMoney=$recordModel->leastPayMoney($servicecontractsid);
            $accountMoneyArray['leastPayMoney']=$leastPayMoney['data'];
        }
        $accountMoneyArray=array_map(function ($v){
            return number_format($v,2);
        },$accountMoneyArray);
        $isArray=array();
        array_push($isArray,$snowId,$signid,$receiveid);
        $isArray=array_unique($isArray);
        Matchreceivements_Record_Model::recordLog(array('查邮箱',json_encode($isArray,JSON_UNESCAPED_UNICODE)),'match_log');
        $isArray=array_filter($isArray);
        $sql="select email1 from vtiger_users where id in (".implode(',',$isArray).")";
        $emailArray=$adb->run_query_allrecords($sql);
        $recordModel=new Vtiger_Record_Model();
        foreach ($emailArray as $email){
            $content="【回款匹配成功】客户到款已成功匹配，请及时确认！";
            $content.="<br>客户名称：".$accountName."<br>抬头/交易单号：".$paytitleOrPaycode."<br>回款金额：".$unit_price."<br>入账日期：".$reality_date."<br>合同编号：".$contract_no."<br>合同总金额：".$accountMoneyArray['paymentTotal']."<br>累计回款金额：".$accountMoneyArray['paymentReceived']."<br>剩余回款金额：".$accountMoneyArray['paymentElse']."<br>剩余分期付款最低可回款金额：".$accountMoneyArray['leastPayMoney'];
            $data1=array();
            $data1['title']='回款匹配成功';
            $data1['flag']=7;
            $data1['description']=$content;
            $data1['dataurl']='#';
            $data1['email']=trim($email['email1']);
            Matchreceivements_Record_Model::recordLog(array('发微信通知匹配成功',json_encode($data1,JSON_UNESCAPED_UNICODE)),'match_log');
            $recordModel->sendWechatMessage($data1);
        }
    }


    /**
     * 获取客户名
     */
    public function getAccountName(Vtiger_Request $request){
        global $adb;
        $recordId=$request->get('record');
        $sql="select vtiger_account.accountname,vtiger_account.accountid from vtiger_servicecontracts left join vtiger_account on vtiger_servicecontracts.sc_related_to=vtiger_account.accountid where vtiger_servicecontracts.servicecontractsid=?";
        $result=$adb->pquery($sql,array($recordId));
        $accountname=$adb->query_result($result,0,'accountname');
        $accountid=$adb->query_result($result,0,'accountid');
        if($accountid){
            $recordModel=new SearchMatch_Record_Model();
            $accountMoneyArray=$recordModel->getAccountMoneyArray($recordId);
        }
        $accountMoneyArray=array_map(function ($v){
            return number_format($v,2);
        },$accountMoneyArray);
        $response = new Vtiger_Response();
        $response->setResult(array('accountname'=>$accountname,'totelMoney'=>$accountMoneyArray['paymentTotal'],'receivedMoney'=>$accountMoneyArray['paymentReceived'],'remainMoney'=>$accountMoneyArray['paymentElse']));
        $response->emit();
    }

}
