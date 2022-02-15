<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
//error_reporting(-1);
//ini_set("display_errors",1);
class ReceivedPaymentsNotes_BasicAjax_Action extends Vtiger_Action_Controller {
	function __construct() {
		parent::__construct();
        $this->exposeMethod('relieve');
        $this->exposeMethod('unboundExport');
        $this->exposeMethod('isCanChangeBinding');
        $this->exposeMethod('searchCanChangeBinding');
        $this->exposeMethod('searchStayment');
        $this->exposeMethod('confirmChangeBinding');
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

	public function relieve(Vtiger_Request $request){
        $adb = PearDatabase::getInstance();
        $recordId= $request->get('record');
        $sql="select * from vtiger_receivedpayments where receivedpaymentsid=".$recordId;
        $receivedPaymentArray=$adb->run_query_allrecords($sql);
        $receivedPaymentArray = $receivedPaymentArray[0];
        $resData = array('flag'=>true);
        if($receivedPaymentArray['matchstatus']){
            //当匹配状态有值时判断是否是财务有权限
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            if(!$moduleModel->exportGrouprt('ReceivedPaymentsNotes','confirmrelieve')){   //权限验证
                $resData = array('flag'=>false,'msg'=>'无权限解除跨月匹配，请到【服务合同】[权限设置]里进行设置');
            }
        }else{
            //当匹配状态没值时判断是否跨月，跨月的话直接打回，并且修改状态
            $matchDate=$receivedPaymentArray['matchdate'];
            if(date('Y-m')!=date('Y-m',strtotime($matchDate))){
                $sql="update vtiger_receivedpayments set matchstatus=1 where receivedpaymentsid=?";
                $adb->pquery($sql,array($recordId));
                $resData = array('flag'=>false,'msg'=>'根据财务部回款匹配制度要求，匹配后次月解除匹配须至财务部办理。<br><br>是否确认到财务部解除回款匹配？');
            }
        }
        if($resData['flag']){
            //以上条件满足开始处理
            $resData=$this->cleanReceive($request);
        }
        $response = new Vtiger_Response();
        $response->setResult($resData);
        $response->emit();
    }

    /**
        清除回款匹配
     * @param Vtiger_Request $request
     * @throws Exception
     */
    public function cleanReceive(Vtiger_Request $request){
        $request->set('from',1);
        $basicObject=new ReceivedPayments_BasicAjax_Action();
        return $basicObject->cleanReceive($request);
    }

    public function unboundExport(Vtiger_Request $request){
        set_time_limit(0);
        ini_set('memory_limit',-1);
        global $root_directory,$site_URL,$adb,$current_user;
        $path=$root_directory.'temp/';
        $filename = '回款解绑记录导出';
        $filename = (strtolower(substr(PHP_OS,0,3))=='win') ? mb_convert_encoding($filename,'gbk','UTF-8') : $filename;
        $filename=$path.$filename.date('Ymd').$current_user->id.'.csv';
        !is_dir($path)&&mkdir($path,'0777',true);
        @unlink($filename);
        $adb = PearDatabase::getInstance();
        $json= urldecode($request->get('json'));
        $jsonArray=explode('&',$json);
        $bugFreeQuery=array();
        foreach ($jsonArray as $value){
            $jsonList=explode('=',$value);
            $bugFreeQuery[$jsonList[0]]=$jsonList[1];
        }
        if(!empty($request)){
            $_REQUEST['BugFreeQuery'] = json_encode($bugFreeQuery);
            $_REQUEST['public']='Unbound';
        }
        $moduleName ='ReceivedPaymentsNotes';
        $nowMonth=date('Y-m');
        $searchRange=" and 1=1";
        if($bugFreeQuery){
            foreach ($bugFreeQuery as $key => $bugFree){
                if(strpos($bugFree,'dateequal') !== false){
                    $newKey=str_replace('field','value',$key);
                    $bugFreeQuery[$newKey]&&$nowMonth=date('Y-m',strtotime($bugFreeQuery[$newKey]));
                    $searchRange=" and left(changetime,7)='".$nowMonth."'";
                }
            }
        }
        $listViewModel=ReceivedPaymentsNotes_ListView_Model::getInstance('ReceivedPaymentsNotes');
        $listViewModel->getSearchWhere();
        $listWhere = $listViewModel->get('query_generator')->getSearchWhere();
        if($listWhere){
            $listWhere='and '.$listWhere;
        }
        $listQuery=$listViewModel->getUnboundSql($nowMonth,$searchRange);
        $listQuery .=") tmptable where 1=1 ".$listWhere;
        $viewid = ListViewSession::getCurrentView($moduleName);
        ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,
        $listResult = $adb->pquery($listQuery, array());
        $fp=fopen($filename,'w');
        $array=array_map(function ($value){
            return iconv('utf-8','gb2312',$value);
        },array('公司账号','回款抬头','支付渠道','交易单号','入账时间','回款金额','合同金额','代付款金额','上次匹配合同编号','上次匹配时间','匹配合同编号','匹配时间','匹配人','当时匹配人部门','最后回款解绑时间','最后回款解绑人','当月解绑次数','累计解绑次数','最后一次解绑是否跨月解绑','跨月解绑次数','匹配状态'));
        fputcsv($fp,$array);
        while($value=$adb->fetch_array($listResult)) {
            $newValue=array($value['owncompany'],$value['paytitle'],$value['paymentchannel'],$value['paymentcode'],$value['reality_date'],$value['unit_price'],$value['total'],$value['staypaymentjine'],$value['last_match_contract_no'],$value['last_match_time'],$value['match_contract_no'],$value['match_time'],$value['matcher'],$value['current_department'],$value['last_relive_time'],$value['last_reliver'],$value['current_month_relive_times'],$value['relive_times_count'],$value['is_last_overmonth_relive'],$value['overmonth_relive_times'],$value['match_status']);
            $newValue=array_map(function ($val){
                return iconv('utf-8','gb2312',$val)."\t";
            },$newValue);
            fputcsv($fp,$newValue);
        }
        fclose($fp);
        $response = new Vtiger_Response();
        $response->setResult(array('flag'=>true,'msg'=>'temp/回款解绑记录导出'.date('Ymd').$current_user->id.'.csv'));
        $response->emit();
    }

    /**
     * 是否能换绑
     * @param Vtiger_Request $request
     */
    public function isCanChangeBinding(Vtiger_Request $request){
        global $adb,$root_directory;
        $record=$request->get('record');
        $sql="select * FROM vtiger_achievementallot_statistic where receivedpaymentsid=? AND (status=1 or  isover=1)";
        $result= $adb->pquery($sql, array($record));
        $resultNumber = $adb->num_rows($result);
        $response = new Vtiger_Response();
        if($resultNumber==0){
            //验证是否该回款是否已完结或者
            $response->setResult(array('flag'=>false,'msg'=>'只有当回款跨月，已计算业绩提成不可进行正常解绑的情况下，放可申请将金额相同的未匹配回款进行换绑'));
        }else{
            $recordModel=Vtiger_Record_Model::getInstanceById($record,'ReceivedPayments',true);
            $contentHtml=file_get_contents($root_directory.'layouts/vlayout/modules/ReceivedPaymentsNotes/changeBinding.html');
            $needWaitChangeBindInfo['unit_price']=$recordModel->unit_price;
            $needWaitChangeBindInfo['receivedpaymentsid']=$recordModel->receivedpaymentsid;
            $needWaitChangeBindInfo['relatetoid']=$recordModel->relatetoid;
            $response->setResult(array('flag'=>true,'contentHtml'=>$contentHtml,'needWaitChangeBindInfo'=>$needWaitChangeBindInfo));
        }
        $response->emit();
    }

    /**
     * 获取换绑的回款
     * @param Vtiger_Request $request
     */
    public function searchCanChangeBinding(Vtiger_Request $request)
    {
        $adb = PearDatabase::getInstance();
        $jsonArray = $request->get('jsonArray');
        $paymentChannel = trim($jsonArray[0]['value']);
        $realityDate = trim($jsonArray[1]['value']);
        $payTitle = trim($jsonArray[2]['value']);
        $paymentCode = trim($jsonArray[3]['value']);
        $standardMoney = trim($jsonArray[4]['value']);
        if (($paymentChannel == '对公转账' && $payTitle && $realityDate && $standardMoney) || ($paymentChannel == '支付宝转账' && $payTitle && $paymentCode) || ($paymentChannel == '扫码' && $paymentCode)) {
            $sql = "select receivedpaymentsid,paymentchannel,paymentcode,paytitle,reality_date,standardmoney,unit_price from vtiger_receivedpayments WHERE 1=1 AND deleted=0 and (relatetoid=0 or relatetoid is null or relatetoid ='') and receivedstatus='normal'";
            if ($paymentChannel == '对公转账') {
                $sql .= " and paymentchannel='对公转账' and paytitle ='" . $payTitle . "' and reality_date='" . $realityDate . "' and standardmoney='" . $standardMoney . "'";
            } else if ($paymentChannel == '支付宝转账' && str_replace('*', '', $payTitle)) {
                $sql .= " and paymentchannel='支付宝转账' and '" . $payTitle . "' like  REPLACE(vtiger_receivedpayments.paytitle,'*','_') and paymentcode='" . $paymentCode . "'";
            } else {
                $sql .= " and paymentchannel='扫码' and paymentcode='" . $paymentCode . "'";
            }
            $sql .= " order by receivedpaymentsid desc ";
            $listResult = $adb->pquery($sql,array());
            $response = new Vtiger_Response();
            $listViewRecordModels=array();
            while($rawData=$adb->fetch_array($listResult)) {
                $recordArray['type']=$this->isCanMatchWithPublic($rawData['receivedpaymentsid']);
                $recordArray['id']=$rawData['receivedpaymentsid'];
                $recordArray['paymentchannel']=$rawData['paymentchannel'];
                $recordArray['paymentcode']=$rawData['paymentcode'];
                $recordArray['paytitle']=$rawData['paytitle'];
                $recordArray['reality_date']=$rawData['reality_date'];
                $recordArray['unit_price']=$rawData['unit_price'];
                $listViewRecordModels[] = $recordArray;
            }
            $response->setResult(array('flag' => true, 'list' => $listViewRecordModels,'sql'=>$sql));
            if (!$listResult) {
                $response->setResult(array('flag' => false, 'msg' => '查询无数据','sql'=>$sql));
            }
            $response->emit();
        } else {
            $response = new Vtiger_Response();
            $response->setResult(array('flag' => false, 'msg' => '请按照支付渠道对应的查询规则录入查询条件'));
            $response->emit();
        }
    }

    /**
     * 查找代付款
     */
    public function searchStayment(Vtiger_Request $request){
        global $adb;
        $recordId=$request->get('id');
        $servicecontractsid=$request->get('contractId');
        $recordModel=Vtiger_Record_Model::getInstanceById($recordId,'ReceivedPayments',true);
        $channel=$recordModel->get('paymentchannel');
        $reality_date=$recordModel->get('reality_date');
        $unit_price=$recordModel->get('unit_price');
        $payTitle = $request->get('paytitle');
        if($channel=='支付宝转账'){
            $sql = "SELECT
	 vtiger_staypayment.staymentcode,
	 IF(vtiger_staypayment.staypaymenttype = 'nofixation','无固定金额',vtiger_staypayment.staypaymentjine) as staypaymentjine,
	 IF(vtiger_staypayment.staypaymenttype = 'nofixation','无固定金额',vtiger_staypayment.surplusmoney) as surplusmoney
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
        }else {
            $sql = "SELECT
     vtiger_staypayment.staymentcode,
	 IF(vtiger_staypayment.staypaymenttype = 'nofixation','无固定金额',vtiger_staypayment.staypaymentjine) as staypaymentjine,
	 IF(vtiger_staypayment.staypaymenttype = 'nofixation','无固定金额',vtiger_staypayment.surplusmoney) as surplusmoney
FROM
	vtiger_staypayment
WHERE
	(vtiger_staypayment.payer = '" . $payTitle . "' or vtiger_staypayment.staypaymentname = '" . $payTitle . "')
	AND vtiger_staypayment.contractid = '" . $servicecontractsid . "' 
	and vtiger_staypayment.modulestatus !='a_exception' 
	AND (
		( vtiger_staypayment.staypaymenttype = 'nofixation' AND '" . $reality_date . "' >= vtiger_staypayment.startdate AND '" . $reality_date . "' <= vtiger_staypayment.enddate ) 
		OR ( vtiger_staypayment.staypaymenttype = 'fixation' AND '" . $unit_price . "' <= vtiger_staypayment.surplusmoney AND vtiger_staypayment.surplusmoney > 0 ) 
	)";
        }
        $data = $adb->run_query_allrecords($sql);;
        $response = new Vtiger_Response();
        $data[0]['staymentcode']='DFK0003324198';
        $data[0]['staypaymentjine']=22222;
        $data[0]['surplusmoney']=11111;
        $data[1]['staymentcode']='DFK0003324190';
        $data[1]['staypaymentjine']='无固定金额';
        $data[1]['surplusmoney']='无固定金额';
        $response->setResult(array('flag'=>true,'data'=>$data));
        $response->emit();
    }

    /**
     * 对公还是对私
     * @param $id
     * @return string
     */
    public function isCanMatchWithPublic($id){
        global $adb,$current_user;
        $currentId = $current_user->id;
        $sql="SELECT
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
        $result = $adb->pquery($sql, array($currentId, $currentId, $currentId, $currentId, $currentId, $id));
        if($adb->num_rows($result)>0){
            return 'public';
        }
        return  'private';
    }

    /**
     * 确认解绑
     */
    public function confirmChangeBinding(Vtiger_Request $request){
        $oldId=$request->get('record');
        $changeDetailList=$request->get('jsonData');
        $insteadMoneyTotal=0;
        $flag=true;
        $dataResult=array('flag'=>true,'data'=>$changeDetailList);
        foreach ($changeDetailList as $changeDetail){
            $insteadMoneyTotal=bcadd($insteadMoneyTotal,$changeDetail['unit_price'],2);
            if($changeDetail['type']=='private'&&!$changeDetail['staymentcode']){
                $dataResult['flag']=$flag=false;
                $dataResult['msg']='回款抬头是【'.$changeDetail['paytitle'].'】的回款请选择回款对应的代付款协议';
                break;
            }
        }
        if($flag){
            $recordModel=Vtiger_Record_Model::getInstanceById($oldId,'ReceivedPayments',true);
            $unit_price=$recordModel->get('unit_price');
            $relatetoid=$recordModel->get('relatetoid');
            if(bccomp($unit_price,$insteadMoneyTotal,2)!=0){
                //不相等
                $dataResult['flag']=$flag=false;
                $dataResult['msg']='换绑回款金额（'.$unit_price.'）与待换绑回款金额（'.$insteadMoneyTotal.'）不一样';
            }
        }
        if($flag){
            //条件都满足，开始换绑，先解除关系，再绑定关系
            //先解除旧的回款关联关系
            $this->relievingOldCorrelation($oldId);
            $this->addNewCorrelation($oldId,$changeDetailList,$relatetoid);
        }
        $response = new Vtiger_Response();
        $response->setResult($dataResult);
        $response->emit();
    }

    /**
     * 关联新的关系
     * @param $oldId
     * @param $changeDetailList
     */
    public function addNewCorrelation($oldId,$changeDetailList,$relatetoid){
        global $adb;
        $MatchBasicAjax = new Matchreceivements_BasicAjax_Action();
        $receivedModel=new Matchreceivements_Record_Model();
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $user_departments = $currentUser->get('current_user_departments');
        $currentid = $currentUser->get('id');
        $last_name = $currentUser->last_name;
        foreach ($changeDetailList as $changeDetail){
            $staypaymentid=null;
            if(isset($changeDetail['paymentcode'])){
                $sql="select vtiger_staypayment.staypaymentid,vtiger_servicecontracts.contract_no from vtiger_staypayment LEFT JOIN vtiger_servicecontracts ON vtiger_staypayment.contractid=vtiger_servicecontracts.servicecontractsid where vtiger_staypayment.paymentcode=?";
                $result=$adb->pquery($sql,array($changeDetail['paymentcode']));
                $staypaymentid=$adb->query_result($result,0,'staypaymentid');
                $contract_no=$adb->query_result($result,0,'contract_no');
            }
            $sql = "UPDATE vtiger_receivedpayments SET modulename='ServiceContracts',receivedstatus='normal',ismatchdepart=1,ismanualmatch=1,matchdate=?,relatetoid = ?,newdepartmentid=?,staypaymentid=?,matcherid=? WHERE receivedpaymentsid = ?";
            $adb->pquery($sql, array(date('Y-m-d'),$relatetoid, $user_departments,$staypaymentid, $currentid,$changeDetail['id']));
            $deltet_sql = "DELETE  FROM  vtiger_achievementallot WHERE receivedpaymentsid = ?";
            $adb->pquery($deltet_sql, array($changeDetail['id']));
            $insert_history = "INSERT INTO vtiger_receivedpayments_matchhistory  (time,creatid,contractid,receivement) VALUES(NOW(),?,?,?)";
            $adb->pquery($insert_history, array($currentid, $relatetoid, $changeDetail['id']));//匹配历史
            //日志
            $array[0]=array('fieldname'=>'overdue','prevalue'=>'', 'postvalue'=>$last_name . ' 匹配回款，合同编号=' . $contract_no);
            $MatchBasicAjax->setModTracker($changeDetail['id'],$array,'ReceivedPayments');
            $receivedModel->recordReceivedpayment($changeDetail['id'],$contract_no,$relatetoid,'匹配（换绑）',$staypaymentid,$currentid);
            //记录代付款更新日志
            if($staypaymentid){
                $recordModelStaypayment = Staypayment_Record_Model::getInstanceById($staypaymentid,'Staypayment',true);
                if($recordModelStaypayment->get('staypaymenttype')=='fixation'){
                    $prevalue = $recordModelStaypayment->get('surplusmoney');
                    $postvalue = ($recordModelStaypayment->get('surplusmoney')-$changeDetail['unit_price']);
                    $array[0]=array('fieldname'=>'surplusmoney','prevalue'=>$prevalue, 'postvalue'=>$postvalue);
                    $array[1]=array('fieldname'=>'staypaymentname','prevalue'=>'', 'postvalue'=>$changeDetail['paytitle']);
                    $MatchBasicAjax->setModTracker($staypaymentid,$array,'Staypayment');
                    $adb->pquery("update vtiger_staypayment set surplusmoney=? where staypaymentid=?",array($postvalue,$staypaymentid));
                }
                $last_sign_time=Staypayment_Record_Model::getLastSignTime();
                //最后签收时间
                $sql="update vtiger_staypayment set last_sign_time=? where staypaymentid=?";
                $adb->pquery($sql,array($last_sign_time,$staypaymentid));
            }
            //回款匹配记录
            $receivedpaymentsNotesId = $adb->getUniqueID("vtiger_receivedpayments_notes");
            $receivedpaymentsNotesData = array(
                'createtime' => date('Y-m-d H:i:s'),
                'smownerid' => $currentid,
                'receivedpaymentsid' => $changeDetail['id'],
                'notestype' =>  'notestype5',
                'receivedpaymentsnotesid' => $receivedpaymentsNotesId
            );
            $divideNames = array_keys($receivedpaymentsNotesData);
            $divideValues = array_values($receivedpaymentsNotesData);
            $adb->pquery('INSERT INTO `vtiger_receivedpayments_notes` (' . implode(',', $divideNames) . ') VALUES (' . generateQuestionMarks($divideValues) . ')', $divideValues);
            $classifyRecordModel = ReceivedPaymentsClassify_Record_Model::getCleanInstance("ReceivedPaymentsClassify");
            $classifyRecordModel->systemClassification($changeDetail['paytitle']);
            //关联新的回款
            $this->addNewinvoicerayment($oldId,$changeDetail['id']);
//            $this->addNewsaleorderrayment($oldId,$changeDetail['id']);
            $this->addRefillrayment($oldId,$changeDetail['id']);
        }
    }


    public function addNewsaleorderrayment($oldId,$newId){
        global $adb,$current_user;
        $MatchBasicAjax = new Matchreceivements_BasicAjax_Action();
        $sql="select * from vtiger_salesorderrayment where receivedpaymentsid=?";
        $result=$adb->pquery($sql,array($oldId));
        if($adb->num_rows($result)>0){
            $update_sql = "insert INTO vtiger_salesorderrayment(receivedpaymentsid,availableamount,occupationcost,laborcost,purchasecost,totalcost,modifiedby,modifiedtime,deleted,remarks) VALUES(?,?,?,?,?,?,?,?,NOW(),0,?)";
        }
    }

    /**
     * 增加充值单关联
     * @param $oldId
     * @param $newId
     */
    public function addRefillrayment($oldId,$newId){
        global $adb;
        $MatchBasicAjax = new Matchreceivements_BasicAjax_Action();
        $sql="select * from vtiger_refillapprayment where receivedpaymentsid=?";
        $result=$adb->pquery($sql,array($oldId));
        if($adb->num_rows($result)>0){
            $servicecontractsid=$adb->query_result($result,0,'servicecontractsid');
            $remarks=$adb->query_result($result,0,'remarks');
            $recordModel=Vtiger_Record_Model::getInstanceById($newId,'ReceivedPayments',true);
            $tarray[]=$servicecontractsid;
            $tarray[]=$newId;
            $tarray[]=$recordModel->get('unit_price');
            $tarray[]=$remarks;
            $tarray[]=$recordModel->get('refillapplicationid');
            $tarray[]=$recordModel->get('unit_price');
            $tarray[]=date("Y-m-d");
            $tarray[]=date('Y-m-d H:i:s');
            $tarray[]=$newId;
            $sql="INSERT INTO vtiger_refillapprayment
                  (`servicecontractsid`,`receivedpaymentsid`,`total`,`arrivaldate`,`refillapptotal`,`allowrefillapptotal`,`remarks`,`refillapplicationid`,`paytitle`,`backwashtotal`,`owncompany`,`matchdate`,createdtime,receivedstatus) 
                  SELECT ?,?,unit_price,reality_date,?,`rechargeableamount`,?,?,`paytitle`,?,`owncompany`,?,?,'normal' FROM vtiger_receivedpayments WHERE receivedpaymentsid=?";
            //做记录
            $adb->pquery($sql,$tarray);
            $array[0]=array('fieldname'=>'overdue','prevalue'=>'', 'postvalue'=>'换绑增加充值单关联关系');
            $MatchBasicAjax->setModTracker($newId,$array,'ReceivedPayments');
        }
    }

    /**
     * 增加回款关联
     * @param $oldId
     * @param $newId
     * @throws Exception
     */
    public function addNewinvoicerayment($oldId,$newId){
        global $adb;
        $MatchBasicAjax = new Matchreceivements_BasicAjax_Action();
        $sql="select * from vtiger_newinvoicerayment where receivedpaymentsid=?";
        $result=$adb->pquery($sql,array($oldId));
        if($adb->num_rows($result)>0){
            $servicecontractsid=$adb->query_result($result,0,'servicecontractsid');
            $invoicecontent=$adb->query_result($result,0,'invoicecontent');
            $remarks=$adb->query_result($result,0,'remarks');
            $invoiceid=$adb->query_result($result,0,'invoiceid');
            $contract_no=$adb->query_result($result,0,'contract_no');
            $recordModel=Vtiger_Record_Model::getInstanceById($newId,'ReceivedPayments',true);
            $data = array(
                'newinvoiceraymentid'=>'',
                'servicecontractsid'=>$servicecontractsid,
                'receivedpaymentsid'=>$newId,
                'total'=>$recordModel->get('unit_price'),
                'arrivaldate'=>$recordModel->get('reality_date'),
                'invoicetotal'=>$recordModel->get('unit_price'),
                'allowinvoicetotal'=>$recordModel->get('unit_price'),
                'invoicecontent'=>$invoicecontent,
                'remarks'=>$remarks,
                'invoiceid'=>$invoiceid,
                'contract_no'=>$contract_no,
                'surpluinvoicetotal'=>$recordModel->get('unit_price')
            );
            $divideNames = array_keys($data);
            $divideValues = array_values($data);
            $adb->pquery('INSERT INTO `vtiger_newinvoicerayment` ('. implode(',', $divideNames).') VALUES ('. generateQuestionMarks($divideValues) .')',$divideValues);
            $array[0]=array('fieldname'=>'overdue','prevalue'=>'', 'postvalue'=>'换绑增加发票关联关系');
            $MatchBasicAjax->setModTracker($newId,$array,'ReceivedPayments');
        }
    }



    /**
     * 解除旧合同关联
     * @param $oldId
     * @throws Exception
     */
    public function relievingOldCorrelation($oldId){
        global $adb,$current_user;
        $MatchBasicAjax = new Matchreceivements_BasicAjax_Action();
        $receivedModel = new Matchreceivements_Record_Model();
        $receivedBasicAjax = new ReceivedPayments_BasicAjax_Action();
        //解除绑定关系
        $sql="select * from vtiger_receivedpayments where receivedpaymentsid=".$oldId;
        $recordArray=$adb->run_query_allrecords($sql);
        $recordArray=$recordArray[0];
        $sql = "update vtiger_receivedpayments  set matchdate=null,matchstatus=null,matcherid=null, istimeoutmatch=0,iscrossmonthmatch=0,relatetoid=0,ismatchdepart=0,ismanualmatch=0,accountname='',staypaymentid='',ischeckachievement=null where receivedpaymentsid=?";
        $adb->pquery($sql, array($oldId));
        $sql = "DELETE FROM vtiger_receivedpayments_notes where receivedpaymentsid=?";
        $adb->pquery($sql, array($oldId));
        if($recordArray['staypaymentid']){
            //有代付款，解除代付款
            $result = $adb->pquery("SELECT vtiger_staypayment.surplusmoney,vtiger_staypayment.staypaymenttype,vtiger_servicecontracts.contract_no FROM vtiger_staypayment LEFT JOIN vtiger_servicecontracts ON vtiger_staypayment.contractid=vtiger_servicecontracts.servicecontractsid WHERE vtiger_staypayment.staypaymentid=? LIMIT 1",array($recordArray['staypaymentid']));
            if($adb->num_rows($result)){
                $data = $adb->fetchByAssoc($result,0);
                $array[0]=array('fieldname'=>'overdue','prevalue'=>'', 'postvalue'=>'合同编号:'.$data['contract_no'].' 换绑解除回款关联关系');
                if($data['staypaymenttype']=='fixation') {
                    $adb->pquery("update vtiger_staypayment set surplusmoney=? where staypaymentid=?",array(($data['surplusmoney'] + $recordArray['unit_price']), $recordArray['staypaymentid']));
                    $prevalue = $data['surplusmoney'];
                    $postvalue = $data['surplusmoney']+$recordArray['unit_price'];
                    $array[1]=array('fieldname'=>'surplusmoney','prevalue'=>$prevalue, 'postvalue'=>$postvalue);
                }
                $MatchBasicAjax->setModTracker($recordArray['staypaymentid'],$array,'Staypayment');
            }
        }
        //删除回款分成明细
        $sql = "delete from vtiger_achievementallot where receivedpaymentsid=? ";
        $adb->pquery($sql, array($oldId));
        //解除合同的关联
        if ($recordArray['modulename'] == 'ServiceContracts') {
            $servicecontractsid = $recordArray['relatetoid'];
            //合同已回款金额
            $sql = "select sum(unit_price) as unit_price_total from vtiger_receivedpayments where receivedstatus='normal' and deleted=0 and relatetoid=?";
            $sel_result = $adb->pquery($sql, array($servicecontractsid));
            $res_cnt = $adb->num_rows($sel_result);
            if ($res_cnt > 0) {
                $receivedpayments_row = $adb->query_result_rowdata($sel_result, 0);
            }
            //重置合同上的关闭，已回款金额
            $delSql = "DELETE FROM vtiger_contractperformancecostnew WHERE servicecontractsid=? and receivedpaymentsids=?";
            $adb->pquery($delSql, array($servicecontractsid, $oldId . ','));
            $sql = "UPDATE vtiger_contractperformancecostnew SET repuntilprice=if(repuntilprice-?<=0,0,repuntilprice-?),receivedpaymentsids=REPLACE(receivedpaymentsids,'" . $oldId . ",','') WHERE servicecontractsid=? and FIND_IN_SET(?,receivedpaymentsids)";
            $adb->pquery($sql, array($recordArray['unit_price'], $recordArray['unit_price'], $servicecontractsid, $oldId));
            //匹配记录储存匹配动作
            $receivedModel->recordReceivedpayment($oldId, $receivedpayments_row['contract_no'], $servicecontractsid, '解绑（换绑）', null, $current_user->id);
            $receivedBasicAjax->modtracker($receivedpayments_row);
        }
        $this->jobalertsreminder($oldId,$recordArray['unit_price']);
        //系统分类
        $classifyRecordModel = ReceivedPaymentsClassify_Record_Model::getCleanInstance("ReceivedPaymentsClassify");
        $classifyRecordModel->systemClassification($oldId);
        //解除回款关联,解除工单关联,解除充值单关联
        $sql="update vtiger_newinvoicerayment set deleted=1 where servicecontractsid=? and receivedpaymentsid=?";
        $adb->pquery($sql, array($servicecontractsid, $oldId));
        $array[0]=array('fieldname'=>'overdue','prevalue'=>'', 'postvalue'=>'换绑解除发票关联关系');
        $MatchBasicAjax->setModTracker($oldId,$array,'ReceivedPayments');
        $sql="update vtiger_salesorderrayment set deleted=1 where receivedpaymentsid=?";
        $adb->pquery($sql, array($oldId));
        $array[0]=array('fieldname'=>'overdue','prevalue'=>'', 'postvalue'=>'换绑解除工单关联关系');
        $MatchBasicAjax->setModTracker($oldId,$array,'ReceivedPayments');
        $sql="update vtiger_refillapprayment set deleted=1 where servicecontractsid=? and receivedpaymentsid=?";
        $adb->pquery($sql, array($servicecontractsid, $oldId));
        $array[0]=array('fieldname'=>'overdue','prevalue'=>'', 'postvalue'=>'换绑解除充值单关联关系');
        $MatchBasicAjax->setModTracker($oldId,$array,'ReceivedPayments');
    }

    //通知会计解除回款关联情况
    public function jobalertsreminder($oldId,$paytitle){
        global $current_user,$adb;
        $sql = "select user FROM vtiger_custompowers where custompowerstype='cleanReceiveJobalert'";
        $sel_result = $adb->pquery($sql, array());
        $res_cnt = $adb->num_rows($sel_result);
        if ($res_cnt > 0) {
            $row = $adb->query_result_rowdata($sel_result, 0);
            $jobalertsid = $adb->getUniqueID("vtiger_jobalerts");
            $alertData = array(
                'jobalertsid' => $jobalertsid,
                'subject' => '撤销匹配回款提醒',
                'alerttime' => date('Y-m-d H:i:s'),
                'modulename' => 'ReceivedPayments',
                'moduleid' => $oldId,
                'alertcontent' => $current_user->last_name . '撤销匹配回款.' . '回款抬头: ' . $paytitle,
                'alertid' => str_replace(',', ' |##| ', $row['user']),
                'alertstatus' => 'wait',
                'alertcount' => '0',
                'finishtime' => '',
                'activitytype' => 'Call',
                'taskpriority' => 'High',
                'remark' => '',
                'ownerid' => '1',
                'creatorid' => $current_user->id,
                'createdtime' => date('Y-m-d H:i:s'),
                'modifiedby' => '',
                'modifiedtime' => '',
                'accountid' => '',
                'state' => '0'
            );
            $divideNames = array_keys($alertData);
            $divideValues = array_values($alertData);
            $adb->pquery('INSERT INTO `vtiger_jobalerts` (' . implode(',', $divideNames) . ') VALUES (' . generateQuestionMarks($divideValues) . ')', $divideValues);
            $userids = explode(',', $row['user']);
            foreach ($userids as $value) {
                $jobalertsreminderData = array(
                    'jobalertsid' => $jobalertsid,
                    'alertid' => $value
                );
                $divideNames = array_keys($jobalertsreminderData);
                $divideValues = array_values($jobalertsreminderData);
               $adb->pquery('INSERT INTO `vtiger_jobalertsreminder` (' . implode(',', $divideNames) . ') VALUES (' . generateQuestionMarks($divideValues) . ')', $divideValues);
            }
        }
    }
}
