<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * Inventory Record Model Class
 */
class SearchMatch_Record_Model extends Vtiger_Record_Model {

    public function getSearchMatchList($request){
        $adb=PearDatabase::getInstance();
        $realityDate=$request->get('realityDate');
        $paymentChannel=$request->get('paymentChannel');
        $payTitle=$request->get('payTitle');
        $paymentCode=$request->get('paymentCode');
        $standardMoney=$request->get('standardMoney');
        $pageNumber=$request->get('pageNumber');
        $size=$request->get('size');
        $userId=$request->get('userId');
        $isShow=0;//是否展示不脱敏
        $sql="select receivedpaymentsid as id,owncompany as ownCompany,paymentchannel as paymentChannel,paymentcode as paymentCode,unit_price as unitPrice,paytitle as accountName,".$pageNumber." as pageNumber,".$size." as size from vtiger_receivedpayments WHERE 1=1 AND deleted=0 and (relatetoid=0 or relatetoid is null or relatetoid ='') and receivedstatus='normal' and paymentchannel is not null and reality_date='".$realityDate."'";
        if($paymentChannel){
            $sql .=" and paymentchannel='".$paymentChannel."'";
        }
        if($payTitle){
            if($paymentChannel=='支付宝转账'&&str_replace('*','',$payTitle)){
                $sql .=" and '".$payTitle."' like  REPLACE(vtiger_receivedpayments.paytitle,'*','_')";
            }else{
                $sql .=" and paytitle =  '".$payTitle."'";
            }
        }
        if($standardMoney){
            $sql .=" and standardmoney='".$standardMoney."'";
        }
        if($paymentCode){
            $sql .=" and paymentcode='".$paymentCode."'";
        }
        if($paymentChannel=='对公转账'&&$payTitle&&$standardMoney){
            $isShow=1;
        }else if($paymentChannel=='支付宝转账'&&$paymentCode&&$payTitle){
            $isShow=1;
        }else if(($paymentChannel=='扫码')&&$paymentCode){
            $isShow=1;
        }
        $sql.=" order by receivedpaymentsid desc 	LIMIT ".($pageNumber-1)*$size.",".$size;
        $listResult = $adb->pquery($sql, array());
        $listViewRecordModels = array();
        $isSplit=$this->isCanSplit($userId);
        $recordArray=array();
        while($rawData=$adb->fetch_array($listResult)) {
            $recordArray['isSplit']=1;
            $recordArray['paymentCode']=$rawData['paymentcode'];
            $recordArray['accountName']=$rawData['accountname'];
            $recordArray['unitPrice']=$rawData['unitprice'];
            $recordArray['ownCompany']=$rawData['owncompany'];
            $recordArray['paymentChannel']=$rawData['paymentchannel'];
            $recordArray['pageNumber']=$rawData['pagenumber'];
            $recordArray['size']=$rawData['size'];
            $recordArray['id']=$rawData['id'];
            $recordArray['isShow']=$isShow;
            if(!$isShow){
                //脱敏
                $replace=substr($recordArray['paymentCode'],1,strlen($recordArray['paymentCode'])-2);
                $recordArray['paymentCode']=str_replace($replace,'*****',$recordArray['paymentCode']);
                $recordArray['accountName']=mb_substr($recordArray['accountName'],0,1,'UTF-8').'******'.mb_substr($recordArray['accountName'],-1,1,'UTF-8');
                $unitPriceArray=explode('.',$recordArray['unitPrice']);
                $lastStr='****';
                if(strlen($unitPriceArray[0])>1){
                    $lastStr.=substr($unitPriceArray[0],-1);
                }
                $recordArray['unitPrice']=$lastStr.'.'.$unitPriceArray[1];
                $recordArray['isSplit']=0;
            }
            $listViewRecordModels[] = $recordArray;
        }
        return $listViewRecordModels;
    }

    /**
     * 是否能拆分回款
     * @param $userId
     * @return bool
     */
    public function isCanSplit($userId){
        global $adb, $current_user;
        $user = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile($userId);
        $sql = "select * FROM vtiger_custompowers where custompowerstype='split_received_rayments'";
        $sel_result = $adb->pquery($sql, array());
        $res_cnt = $adb->num_rows($sel_result);
        if($res_cnt > 0) {
            while($row=$adb->fetch_array($sel_result)) {
                $roles_arr = explode(',', $row['roles']);
                $user_arr = explode(',', $row['user']);
                if (in_array($current_user->current_user_roles, $roles_arr) || in_array($userId, $user_arr)) {
                    if($row['custompowerstype'] =='split_received_rayments'){
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * 拆分回款
     * @param $request
     * @return bool[]
     * @throws Exception
     */
    public function spiltReceivedPayment($request){
        global $current_user;
        $userId = $request->get('userId');
        $user = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile($userId);
        $recordId = $request->get('id');  // 回款id
        $splitMoney = $request->get('splitMoney');
        $recordObject=new ReceivedPayments_Record_Action();
        $return=array('flag'=>true);
        $splitRequestMap=array();
        $recordModel=Vtiger_Record_Model::getInstanceById($recordId,'ReceivedPayments',true);
        $splitRequestMap['record']=$recordId;
        $splitRequestMap['split_money']=$splitMoney;
        $splitRequestMap['contract_no']='';
        $splitRequestMap['unit_price']=$recordModel->get('unit_price');
        $splitRequestMap['t_split_money']=bcsub($recordModel->get('unit_price'),$splitMoney,2);
        $splitRequest=new Vtiger_Request($splitRequestMap);
        $result=$recordObject->splitReceive($splitRequest);
        if(!$result['flag']){
            $return['flag']=false;
            $return['data']['errorMsg']=$result['msg'];
        }
        return $return;
    }

    /**
     * 获取等待匹配信息
     * @param $request
     * @return array
     */
    public function getWaitMatchInfo($request){
        global $current_user,$adb;
        $userId = $request->get('userId');
        $user = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile($userId);
        $recordId = $request->get('id');  // 回款id
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
	LEFT JOIN vtiger_servicecontracts t2 ON  t1.maybe_account = t2.sc_related_to ## 回款可能客户 = 服务合同里面的客户
	LEFT JOIN vtiger_crmentity t3 ON t1.maybe_account = t3.crmid
	LEFT JOIN vtiger_account t4 ON t1.maybe_account = t4.accountid 
WHERE
	(
		t4.serviceid = ? or t2.receiveid = ? or t3.smownerid = ? or EXISTS ( SELECT 1 FROM vtiger_shareaccount WHERE vtiger_shareaccount.userid =? AND vtiger_shareaccount.sharestatus = 1 AND vtiger_shareaccount.accountid = t2.sc_related_to)
	)
	and t2.modulestatus not in ('c_cancel','c_canceling','a_exception','c_stop','c_completeclosed')
	and (t1.relatetoid = '' OR t1.relatetoid = 0 OR t1.relatetoid IS NULL) 
	and t1.receivedstatus = 'normal'
	and t2.servicecontractsid != '' ## 合同不能为空
	and t2.sideagreement=0
	and t2.contract_no!=''
	and t2.contractstate = '0' ## 合同的关闭状态为正常
	and t1.maybe_account != '' 
	and t1.receivedpaymentsid=?";
        $result = $adb->pquery($sql,array($userId,$userId, $userId, $userId, $userId, $recordId));
        $data = array();
        $return=array('flag'=>true);
        $return['data']=array();
        if($adb->num_rows($result)>0){
            for($i=0;$i<$adb->num_rows($result);$i++){
                $res = $adb->fetchByAssoc($result,$i);
                $return['data']['contractList'][$i]['contractId']=$res['servicecontractsid'];
                $return['data']['contractList'][$i]['contractNo']=$res['contract_no'];
                $data[] = $res;
            }
            $data[0]['matchtype']=1;
        }
        if(!$data){
            //如果没有的话（不能直接匹配）直接查合同和基础信息
            $sql="select receivedpaymentsid,paytitle,unit_price,reality_date,paymentcode from vtiger_receivedpayments where receivedpaymentsid=".$recordId;
            $data=$adb->run_query_allrecords($sql);
            $data[0]['matchtype']=0;
            //合同
            $sql="SELECT
	vtiger_servicecontracts.servicecontractsid,
	vtiger_servicecontracts.contract_no 
FROM
	vtiger_servicecontracts
	LEFT JOIN vtiger_account ON vtiger_servicecontracts.sc_related_to = vtiger_account.accountid
	LEFT JOIN vtiger_crmentity ON vtiger_account.accountid = vtiger_crmentity.crmid 
WHERE
	(
		vtiger_crmentity.smownerid = " . $userId . " 
		OR vtiger_account.serviceid = ".$userId ." 
		OR vtiger_servicecontracts.receiveid = ".$userId ." 
		OR vtiger_servicecontracts.signid = ".$userId ." 
		OR EXISTS ( SELECT 1 FROM vtiger_shareaccount WHERE vtiger_shareaccount.userid = ".$userId ." AND vtiger_shareaccount.sharestatus = 1 AND vtiger_shareaccount.accountid = vtiger_servicecontracts.sc_related_to ) 
	) 
	AND vtiger_servicecontracts.modulestatus NOT IN ( 'c_cancel', 'c_canceling', 'a_exception', 'c_stop', 'c_completeclosed' ) 
	AND vtiger_servicecontracts.contractstate = 0 
	AND vtiger_servicecontracts.sideagreement = 0 
	AND vtiger_servicecontracts.contract_no != ''";
            $serviceArray=$adb->run_query_allrecords($sql);
            foreach ($serviceArray as $key=>$service){
                $return['data']['contractList'][$key]['contractId']=$service['servicecontractsid'];
                $return['data']['contractList'][$key]['contractNo']=$service['contract_no'];
            }
        }
        $return['data']['payTitle']=$data[0]['paytitle'];
        $return['data']['unitPrice']=$data[0]['unit_price'];
        $return['data']['paymentCode']=$data[0]['paymentcode'];
        $return['data']['realityDate']=$data[0]['reality_date'];
        $return['data']['accountName']=$data[0]['accountname'];
        $return['data']['shareUser']=$data[0]['shareuser'];
//        if($data[0]['accountid']){
//            $accountMoneyArray=$this->getAccountMoneyArray($data[0]['accountid']);
//            $return['data']['totelMoney']=$accountMoneyArray['paymentTotal'];
//            $return['data']['receivedMoney']=$accountMoneyArray['paymentReceived'];
//            $return['data']['remainMoney']=$accountMoneyArray['paymentElse'];
//        }
        $return['data']['matchType']=$data[0]['matchtype'];
        return $return;
    }

    /**
     * 获取客户名下金额和未匹配金额
     */
    public function getAccountMoneyArray($contractId){
        global $adb;
        $sql="SELECT
	(select SUM(unit_price) from vtiger_receivedpayments where vtiger_receivedpayments.relatetoid=vtiger_servicecontracts.servicecontractsid and vtiger_receivedpayments.receivedstatus= 'normal' and  vtiger_receivedpayments.deleted=0) as unit_price,
	vtiger_servicecontracts.total
FROM
	vtiger_servicecontracts
WHERE
	vtiger_servicecontracts.servicecontractsid=".$contractId;
        $moneyArray=$adb->run_query_allrecords($sql);
        $accountMoneyArray=array();
        $accountMoneyArray['paymentReceived']=$moneyArray[0]['unit_price'];
        $accountMoneyArray['paymentTotal']=$moneyArray[0]['total'];
        $accountMoneyArray['paymentElse']=bcsub($accountMoneyArray['paymentTotal'],$accountMoneyArray['paymentReceived'],2);
        if($accountMoneyArray['paymentElse']<0){
            $accountMoneyArray['paymentElse']=0;
        }
        return $accountMoneyArray;
    }

    /**
     *获取分成人接口
     * @param $request
     * @return bool[]
     */
    public function getDivideInfo($request){
        global $adb;
        $matchType=$request->get('matchType');
        $contractId=$request->get('contractId');
        $userId=$request->get('userId');
        $sql="SELECT
	vtiger_users.last_name,
	vtiger_servicecontracts_divide.scalling
FROM
	vtiger_servicecontracts_divide
	LEFT JOIN vtiger_users ON vtiger_servicecontracts_divide.receivedpaymentownid = vtiger_users.id
	where servicecontractid=".$contractId;
        $divideInfoList=$adb->run_query_allrecords($sql);
        $return=array('flag'=>true);
        foreach ($divideInfoList as $key=>$divideInfo){
            $return['data']['divideInfoList'][$key]['divideName']=$divideInfo['last_name'];
            $return['data']['divideInfoList'][$key]['divideRate']=$divideInfo['scalling'];
        }
        $accountMoneyArray=$this->getAccountMoneyArray($contractId);
        $return['data']['totelMoney']=bcsub($accountMoneyArray['paymentTotal'],0,2);
        $return['data']['receivedMoney']=bcsub($accountMoneyArray['paymentReceived'],0,2);
        $return['data']['remainMoney']=bcsub($accountMoneyArray['paymentElse'],0,2);
//        if(!$matchType){
            //代付款接口
            $sql="SELECT vtiger_account.accountid,vtiger_account.accountname,IFNULL((
SELECT 1 FROM vtiger_shareaccount WHERE vtiger_shareaccount.userid=".$userId." AND vtiger_shareaccount.sharestatus=1 AND vtiger_shareaccount.accountid=vtiger_servicecontracts.sc_related_to),0) AS shareuser FROM vtiger_servicecontracts LEFT JOIN vtiger_account ON vtiger_servicecontracts.sc_related_to=vtiger_account.accountid WHERE vtiger_servicecontracts.servicecontractsid=".$contractId;
            $accountArray=$adb->run_query_allrecords($sql);
            $return['data']['accountName']=$accountArray[0]['accountname'];
            $return['data']['shareUser']=$accountArray[0]['shareuser'];
//        }
        return $return;
    }

    /**
     * 根据抬头搜索代付款
     * @param $request
     * @return bool[]
     */
    public function searchPayTitle($request){
        global $adb;
        $contractId=$request->get('contractId');
        $id=$request->get('id');
        $payTitle=$request->get('payTitle');
        $recordModel=Vtiger_Record_Model::getInstanceById($id,'ReceivedPayments',true);
        $channel=$recordModel->get('paymentchannel');
        $reality_date=$recordModel->get('reality_date');
        $unit_price=$recordModel->get('unit_price');
        if($channel=='支付宝转账'){
            $sql="SELECT
     vtiger_receivedpayments.unit_price, 
	 vtiger_staypayment.staypaymentid,
	 vtiger_staypayment.staypaymentname,
	 vtiger_staypayment.payer,
	 vtiger_staypayment.surplusmoney,
	 vtiger_staypayment.staypaymentjine,
	 vtiger_staypayment.staypaymenttype
FROM
	vtiger_staypayment
    LEFT JOIN vtiger_receivedpayments ON  ( (REPLACE(vtiger_staypayment.staypaymentname,' ','') =  REPLACE(vtiger_receivedpayments.paytitle,' ','') and vtiger_staypayment.staypaymentname!='' ) or (REPLACE(vtiger_staypayment.payer,' ','') like REPLACE(REPLACE(vtiger_receivedpayments.paytitle,' ',''),'*','_')) )
WHERE
	(vtiger_staypayment.payer = '" . $payTitle . "' or vtiger_staypayment.staypaymentname = '" . $payTitle . "')
	AND vtiger_staypayment.contractid = '".$contractId."' 
	AND vtiger_receivedpayments.receivedpaymentsid = '".$id."'
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
	AND vtiger_staypayment.contractid = '" . $contractId . "' 
	and vtiger_staypayment.modulestatus !='a_exception' 
	AND (
		( vtiger_staypayment.staypaymenttype = 'nofixation' AND '".$reality_date."' >= vtiger_staypayment.startdate AND '".$reality_date."' <= vtiger_staypayment.enddate ) 
		OR ( vtiger_staypayment.staypaymenttype = 'fixation' AND '".$unit_price."' <= vtiger_staypayment.surplusmoney AND vtiger_staypayment.surplusmoney > 0 ) 
	)";
        }

        $dataArray=$adb->run_query_allrecords($sql);
        $return=array('flag'=>true);
        foreach ($dataArray as $key=>$data){
            $return['data']['stayPaymentInfoList'][$key]['stayPaymentId']=$data['staypaymentid'];
            $return['data']['stayPaymentInfoList'][$key]['stayPaymentName']=$data['staypaymentname'];
            $return['data']['stayPaymentInfoList'][$key]['payer']=$data['payer'];
            $return['data']['stayPaymentInfoList'][$key]['surplusMoney']='--';
            $return['data']['stayPaymentInfoList'][$key]['stayPaymentMoney']='--';
            if($data['staypaymenttype']=='fixation'){
                $return['data']['stayPaymentInfoList'][$key]['surplusMoney']=$data['surplusmoney'];
                $return['data']['stayPaymentInfoList'][$key]['stayPaymentMoney']=$data['staypaymentjine'];
            }

        }
        if(!$return['data']){
            $return=array('flag'=>false,'errorMsg'=>'无相关代付款协议');
        }
        return $return;
    }

    /**
     * 做匹配
     * @param $request
     */
    public function doMatch($request){
        global $current_user;
        $userId=$request->get('userId');
        $user = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile($userId);
        $contractId=$request->get('contractId');
        $id=$request->get('id');
        $unitPrice=$request->get('unitPrice');
        $stayPaymentId=$request->get('stayPaymentId');
        $shareUser=$request->get('shareUser');
        $payTitle=$request->get('payTitle');
        $payer=$request->get('payer');
        $request=new Vtiger_Request();
        $request->set('staypaymentid',$stayPaymentId);
        $request->set('contractid',$contractId);
        $request->set('total',$unitPrice);
        $request->set('receivepayid',$id);
        $request->set('paytitle',$payTitle);
        $request->set('shareuser',$shareUser);
        $request->set('source','app');//来源
        $request->set('payer',$payer);//来源
        $searchBasicObject=new SearchMatch_BasicAjax_Action();
        return $searchBasicObject->goMatch($request);
    }
}