<?php
define("AUTO_TOKEN",md5(date('Y-m-d H:i:s')));
$dir=trim(__DIR__,DIRECTORY_SEPARATOR);
$dir=trim(__DIR__,'cron');
ini_set("include_path", $dir);
require_once('config.php');
require_once('include/utils/utils.php');
require_once('include/logging.php');
header("Content-type:text/html;charset=utf-8");
//error_reporting(-1);
//ini_set("display_errors",1);
include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';
vglobal('default_language', $default_language);
$currentLanguage = 'zh_cn';
vglobal('current_language', $currentLanguage);


findUnMatchPayment();
/***
 * 查询未匹配的回款条件是
 * 必须是未匹配的，状态是normal,overdue里有值
 */
function findUnMatchPayment(){
    Matchreceivements_Record_Model::recordLog('开始新一轮自动匹配');
    global $adb;
    $sql="SELECT
	receivedpaymentsid,overdue,paymentchannel,paymentcode,unit_price 
FROM
	vtiger_receivedpayments 
WHERE
	( relatetoid = 0 OR relatetoid IS NULL  or  relatetoid='') 
	AND receivedstatus = 'normal' 
	AND deleted = 0 
	and createtime >'2021-10-01 18:30:00'
	and automatchflag is null
	and unit_price>0
	and receivedpaymentsid not in (SELECT distinct receivedpaymentsid FROM vtiger_achievementallot_statistic WHERE  isover=1)";
    $autoMatchPaymentArray=$adb->run_query_allrecords($sql);
    if($autoMatchPaymentArray){
        Matchreceivements_Record_Model::recordLog('有未匹配的回款');
        //获取所有需要匹配的合同
        $needMatchContracts=getNeedMatchContracts();
        $canAutoMatchArray=confirmServiceContract($autoMatchPaymentArray,$needMatchContracts);
        //开始匹配
        $canAutoMatchArray&&autoMatchReceive($canAutoMatchArray);
        Matchreceivements_Record_Model::recordLog('结束自动匹配');
        exit();
    }else{
        Matchreceivements_Record_Model::recordLog('本轮没有可以自动匹配的回款');
        echo '没未匹配的回款';
        exit();
    }
}

//获取所有能匹的合同
function getNeedMatchContracts(){
    global $adb;
    $sql="SELECT DISTINCT contract_no FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_servicecontracts.servicecontractsid=vtiger_crmentity.crmid WHERE vtiger_servicecontracts.contractstate=0 AND vtiger_servicecontracts.sideagreement=0 AND vtiger_servicecontracts.modulestatus NOT IN ('c_cancel','c_canceling','a_exception','c_stop','c_completeclosed') AND  vtiger_servicecontracts.sc_related_to>0 AND vtiger_crmentity.createdtime>='2019-01-01 00:00:00' AND vtiger_servicecontracts.contract_no IS NOT NULL";
    $contracts=$adb->run_query_allrecords($sql);
    $needMatchArray=array();
    foreach ($contracts as $contract){
        $needMatchArray[getContractNoFromRemark($contract['contract_no'])]=$contract['contract_no'];
    }
    Matchreceivements_Record_Model::recordLog('查所有能匹的合同'.count(array_column($contracts,'contract_no')));
    return $needMatchArray;
}


/**
 * 过滤合同
 * @param $autoMatchPaymentArray
 */
function confirmServiceContract($autoMatchPaymentArray,$needMatchContracts){
    global $adb;
    $receivedPaymentWithContractArray=array();
    $recordObject=new ReceivedPayments_Record_Action();
    $contractKey=array_keys($needMatchContracts);
    foreach ($autoMatchPaymentArray as $autoMatchPayment){
        if(in_array($autoMatchPayment['paymentchannel'],array('支付宝转账','扫码'))&&$autoMatchPayment['paymentcode']){
            Matchreceivements_Record_Model::recordLog($autoMatchPayment['receivedpaymentsid'].'此回款是支付宝扫码渠道');
            $sql="select vtiger_activationcode.contractid,vtiger_activationcode.contractname,vtiger_servicecontracts.total from vtiger_activationcode left join vtiger_servicecontracts on vtiger_activationcode.contractid=vtiger_servicecontracts.servicecontractsid  where  vtiger_servicecontracts.contractstate=0  and vtiger_servicecontracts.sideagreement=0 and  vtiger_servicecontracts.modulestatus not in ('c_cancel','c_canceling','a_exception','c_stop','c_completeclosed') and vtiger_activationcode.paymentno='".$autoMatchPayment['paymentcode']."'";
            $orderInfoArray=$adb->run_query_allrecords($sql);
            if($orderInfoArray){
                Matchreceivements_Record_Model::recordLog($autoMatchPayment['receivedpaymentsid'].'这个支付宝扫码渠道有订单');
                //有订单开始用订单去匹配
                $recePayMentTotal=$autoMatchPayment['unit_price'];
                foreach ($orderInfoArray as $orderInfo){
                    $sql="select receivedpaymentsid from vtiger_receivedpayments where relatetoid=".$orderInfo['contractid'];
                    $receResult=$adb->run_query_allrecords($sql);
                    if($receResult){
                        Matchreceivements_Record_Model::recordLog($orderInfo['contractid'].'合同已经匹配过了');
                        continue;
                    }
                    if(bccomp($recePayMentTotal,$orderInfo['total'],2)>=0){
                        if(bccomp($recePayMentTotal,$orderInfo['total'],2)==0){
                            //等于0，匹配刚刚好,然后匹下一个
                            $receivedPaymentWithContractArray[$autoMatchPayment['receivedpaymentsid']]=$orderInfo['contractname'];
                            continue;
                        }
                        //大于0需要拆分
                        $recordModel=Vtiger_Record_Model::getInstanceById($autoMatchPayment['receivedpaymentsid'],'ReceivedPayments',true);
                        $splitRequestMap['record']=$autoMatchPayment['receivedpaymentsid'];
                        $splitRequestMap['contract_no']='';
                        $splitRequestMap['split_money']=$orderInfo['total'];
                        $splitRequestMap['unit_price']=$recordModel->get('unit_price');
                        $splitRequestMap['t_split_money']=bcsub($recordModel->get('unit_price'),$orderInfo['total'],2);
                        Matchreceivements_Record_Model::recordLog(array($splitRequestMap,'拆分金额'));
                        $splitRequest=new Vtiger_Request($splitRequestMap);
                        $result=$recordObject->splitReceive($splitRequest);
                        if($result['flag']){
                            //拆分成功
                            Matchreceivements_Record_Model::recordLog($autoMatchPayment['receivedpaymentsid'].'拆分成功'.$result['msg']);
                            $receivedPaymentWithContractArray[$result['msg']]=$orderInfo['contractname'];
                            $recePayMentTotal=bcsub($recePayMentTotal,$orderInfo['total'],2);
                        }else{
                            Matchreceivements_Record_Model::recordLog($autoMatchPayment['receivedpaymentsid'].'拆分失败'.$result['msg']);
                        }
                    }else{
                        //回款金额小于合同金额，干掉
                        Matchreceivements_Record_Model::recordLog($autoMatchPayment['receivedpaymentsid'].'回款金额小于合同金额');
                        setAutoFlag(2,$autoMatchPayment['receivedpaymentsid']);
                    }
                }
                //做完关于支付宝微信的匹配继续循环
                continue;
            }
            //没订单走下面的
        }
        Matchreceivements_Record_Model::recordLog('合同号不符合自动或者推荐匹配');
        $remark=$autoMatchPayment['overdue'];
        //在备注中取出合同
        $contractNo=getContractNoFromRemark($remark);
        if($contractNo){
            $contractFlag=false;
            foreach ($contractKey as $contract){
                if (strstr( $contractNo, $contract) !== false ){
                    $contractFlag=true;
                    $contractNo=$needMatchContracts[$contract];
                    break;
                }
            }
            if($contractFlag){
                //不要废除或者关闭匹配状态的合同
                $sql="select * from vtiger_servicecontracts where contract_no ='".$contractNo."' and contractstate=0  and sideagreement=0 and   modulestatus not in ('c_cancel','c_canceling','a_exception','c_stop','c_completeclosed')";
                $serviceArray=$adb->run_query_allrecords($sql);
                if($serviceArray){
                    $contractId=$serviceArray[0]['servicecontractsid'];
                    $flag=true;
                    if($serviceArray[0]['contract_type']=='T云WEB版'){
                        $flag=Matchreceivements_Record_Model::isPreContractMatched($contractId);
                    }
                    if($flag){
                        $receivedPaymentWithContractArray[$autoMatchPayment['receivedpaymentsid']]=$contractNo;
                    }else{
                        //上一份合同未参与，不匹配，干掉
                        Matchreceivements_Record_Model::recordLog($autoMatchPayment['receivedpaymentsid'].'不符合自动或者推荐匹配1');
                        setAutoFlag(2,$autoMatchPayment['receivedpaymentsid']);
                    }
                }else{
                    //不符合自动或者推荐匹配，直接干掉
                    Matchreceivements_Record_Model::recordLog($autoMatchPayment['receivedpaymentsid'].'不符合自动或者推荐匹配2');
                    setAutoFlag(2,$autoMatchPayment['receivedpaymentsid']);
                }
            }else{
                //没合同走推荐匹配
                Matchreceivements_Record_Model::recordLog($autoMatchPayment['receivedpaymentsid'].'合同不匹配，走推荐');
                setAutoFlag(0,$autoMatchPayment['receivedpaymentsid']);
            }
        }else{
            //没合同走推荐匹配
            Matchreceivements_Record_Model::recordLog($autoMatchPayment['receivedpaymentsid'].'只能走推荐匹配了');
            setAutoFlag(0,$autoMatchPayment['receivedpaymentsid']);
        }
    }
    return $receivedPaymentWithContractArray;
}

/**
 * 在备注中取出合同
 * @param $remark
 * @return mixed|string
 */
function getContractNoFromRemark($remark){
    //只要字母和数字
    $result = preg_replace("/[^a-zA-Z0-9]+/", "", $remark);
    return $result;
}

/**
 * 匹配合同
 * @param $canAutoMatchArray
 */
function autoMatchReceive($canAutoMatchArray){
    Matchreceivements_Record_Model::recordLog(array('可以自动匹配的回款'.json_encode($canAutoMatchArray,JSON_UNESCAPED_UNICODE)));
    global $adb;
    $matchRecordModel=new Matchreceivements_Record_Model();
    foreach ($canAutoMatchArray as $receivedId =>$contractNo){
        Matchreceivements_Record_Model::recordLog('开始自动匹配的回款'.$receivedId);
        $sql="select * from vtiger_receivedpayments where receivedpaymentsid='".$receivedId."'";
        $receiveResult=$adb->run_query_allrecords($sql);
        $sql="select t1.*,t2.accountname,t2.accountid,(SELECT sum(standardmoney)  FROM vtiger_receivedpayments WHERE relatetoid=t1.servicecontractsid AND receivedstatus='normal' AND deleted=0 ) as standard_price from vtiger_servicecontracts t1 left join vtiger_account t2 on t1.sc_related_to=t2.accountid  where t1.contract_no=?";
        $result=$adb->pquery($sql,array($contractNo));
        $frameworkcontract=$adb->query_result($result,0,'frameworkcontract');
        $contractid=$adb->query_result($result,0,'servicecontractsid');
        $accountname=$adb->query_result($result,0,'accountname');
        $contractTotal=$adb->query_result($result,0,'total');//合同金额
        $totalReceivedpayments=$adb->query_result($result,0,'standard_price');//已经匹配好的回款
        $isautoclose=$adb->query_result($result,0,'isautoclose');//
        $effectivetime=$adb->query_result($result,0,'effectivetime');//合同到期时间
        $accountid=$adb->query_result($result,0,'accountid');//客户id
        $standardmoney=$receiveResult[0]['standardmoney'];//回款原币金额
        $unit_price=$receiveResult[0]['unit_price'];//回款金额
        $totalReceivedpayments=bcadd($standardmoney,$totalReceivedpayments,2);//现回款+已经匹配好的回款
        if((bccomp($contractTotal,$totalReceivedpayments,2)<0 && $contractTotal>0 && $isautoclose==1)){
            //是框架合同并且有金额回款，金额+合同中已经回款的金额>合同金额，让她拆分
            Matchreceivements_Record_Model::recordLog(array('框架合同这次回款+已经回款的总金额=合同的总匹配金额'.$contractNo,$contractTotal,$totalReceivedpayments));
            setAutoFlag(1,$receivedId);
            continue;
        }
        if($frameworkcontract=='no'&&bccomp($contractTotal,$totalReceivedpayments,2)<0){
            Matchreceivements_Record_Model::recordLog('非框架合同这次回款+已经回款的总金额=合同的总匹配金额'.$contractNo);
            //非框架合同回款金额大于合同剩余金额,请联系相关人员进行拆分!同推荐匹配
            setAutoFlag(1,$receivedId);
            continue;
        }
        //框架合同
        $paytitle=$receiveResult[0]['paytitle'];//回款抬头
        $reality_date=$receiveResult[0]['reality_date'];//入账时间
        //合同客户名称与打款抬头是否完全一致？
        if($accountname==$paytitle){
            //直接匹配
            $request=new Vtiger_Request();
            Matchreceivements_Record_Model::recordLog('抬头一样，直接匹配');
            $request->set('receivepayid',$receivedId);
            $request->set('contractid',$contractid);
            $request->set('total',$unit_price);
            $request->set('receivedstatus','normal');
            $request->set('userid',6934);
            $matchRecordModel->autoMatchRecepayment($request);
            $sql="select relatetoid from vtiger_receivedpayments where receivedpaymentsid=".$receivedId;
            $result=$adb->run_query_allrecords($sql);
            if($result[0]['relatetoid']){
                setAutoFlag(1,$receivedId);
            }else{
                setAutoFlag(0,$receivedId);
            }
        }else{
            $request=new Vtiger_Request();
            //用代付款进行处理
            //先判断有没有代付款，有的话判断金额大小
            //查询代付款中还能有余额给回款扣的
            $sql="SELECT * FROM vtiger_staypayment WHERE overdute IS NOT NULL AND modulestatus !='a_exception' AND ((staypaymenttype='nofixation' AND '".$reality_date."'>=startdate AND '".$reality_date."'<=enddate) OR (staypaymenttype='fixation' AND '".$unit_price."'<=surplusmoney AND surplusmoney> 0)) AND ((
REPLACE (payer,' ','') LIKE REPLACE ('".$paytitle."','*','_') OR
(REPLACE (staypaymentname,' ',''))=REPLACE ('".$paytitle."',' ',''))) and contractid='".$contractid."'  ORDER BY surplusmoney";
            $staymentArray=$adb->run_query_allrecords($sql);
            Matchreceivements_Record_Model::recordLog(array('查出来的代付款',$staymentArray));
            if($staymentArray&&$paytitle){
                $matchFlag=false;
                foreach ($staymentArray as $stayment){
                    if($stayment['staypaymenttype']=='fixation'){
                        $compareResult=bccomp($unit_price,$stayment['surplusmoney'],2);
                        if($compareResult<=0){
                            Matchreceivements_Record_Model::recordLog('代付款固定金额匹配'.$stayment['staypaymentid']);
                            //回款比代付款小或者正好用完代付款，下一步匹配
                            $staymentId=$stayment['staypaymentid'];
                            $matchFlag=true;
                            break;//打断
                        }
                    }else{
                        //非固定金额进行匹配，不看金额
                        Matchreceivements_Record_Model::recordLog('代付款非固定金额匹配'.$stayment['staypaymentid']);
                        $staymentId=$stayment['staypaymentid'];
                        $matchFlag=true;
                        break;//打断
                    }
                }
                //回款比代付款大，新建代付款然后通知完善
                if(!$matchFlag){
                    //新建虚拟的代付款
                    $staymentId=addStaypayment($contractid,$effectivetime,$accountid,$unit_price,$paytitle);//得到虚拟的id
                    Matchreceivements_Record_Model::recordLog('无有用回款新建虚拟代付款'.$staymentId);
                }
                $request->set('receivepayid',$receivedId);
                $request->set('contractid',$contractid);
                $request->set('total',$unit_price);
                $request->set('receivedstatus','normal');
                $request->set('staypaymentid',$staymentId);
                $request->set('userid',6934);
                //匹配
                $matchRecordModel->autoMatchRecepayment($request);
                $sql="select relatetoid from vtiger_receivedpayments where receivedpaymentsid=".$receivedId;
                $result=$adb->run_query_allrecords($sql);
                if($result[0]['relatetoid']){
                    setAutoFlag(1,$receivedId);
                }else{
                    setAutoFlag(0,$receivedId);
                }
            }else{
                //没有能用的代付款，虚拟新建一个
                $staymentId=addStaypayment($contractid,$effectivetime,$accountid,$unit_price,$paytitle);//得到虚拟的id
                Matchreceivements_Record_Model::recordLog('无回款新建虚拟代付款'.$staymentId);
                $request->set('receivepayid',$receivedId);
                $request->set('contractid',$contractid);
                $request->set('total',$unit_price);
                $request->set('receivedstatus','normal');
                $request->set('staypaymentid',$staymentId);
                $request->set('userid',6934);
                //匹配
                $matchRecordModel->autoMatchRecepayment($request);
                $sql="select relatetoid from vtiger_receivedpayments where receivedpaymentsid=".$receivedId;
                $result=$adb->run_query_allrecords($sql);
                if($result[0]['relatetoid']){
                    setAutoFlag(1,$receivedId);
                }else{
                    setAutoFlag(0,$receivedId);
                }
            }
        }
    }
}

/**
 * 新增模拟代付款
 * @param $contractid
 * @param $effectivetime
 * @param $accountid
 * @param $unit_price
 * @param $paytitle
 * @return mixed
 */
function addStaypayment($contractid,$effectivetime,$accountid,$unit_price,$paytitle){
    global $current_user;
    $adb=PearDatabase::getInstance();
    $current_user->id=6934;//客户导入id
    $stayPaymentObject=new CRMEntity();
    $companyCode=$stayPaymentObject->getContractsCompanyCode('Staypayment',$contractid);
    $stayPaymentObject->insertIntoCrmEntity('Staypayment','');
    $stayPaymentArray['staypaymentid']=$_REQUEST['currentid'];
    $stayPaymentArray['contractid']=$contractid;
    $stayPaymentArray['accountid']=$accountid;
    $stayPaymentArray['overdute']=$effectivetime;
    $stayPaymentArray['staypaymenttype']='fixation';
    $stayPaymentArray['overdue']='自动匹配，模拟新建';
    $stayPaymentArray['modulename']='ServiceContracts';
    $stayPaymentArray['modulestatus']='a_normal';
    $stayPaymentArray['companycode']=$companyCode;
    $stayPaymentArray['staypaymentjine']=$unit_price;
    $stayPaymentArray['staypaymentname']=$paytitle;
    $stayPaymentArray['surplusmoney']=$unit_price;
    $stayPaymentArray['staymentcode']='DFK000'.$stayPaymentArray['staypaymentid'];
    $stayPaymentArray['isauto']=1;
    $adb->run_insert_data('vtiger_staypayment',$stayPaymentArray);
    return $_REQUEST['currentid'];
}

/**
 *修改状态
 * @param $autoFlag
 * @param $receivedpaymentsid
 */
function setAutoFlag($autoFlag,$receivedpaymentsid){
    $adb=PearDatabase::getInstance();
    $sql="update vtiger_receivedpayments set `automatchflag` =? where receivedpaymentsid=?";
    $adb->pquery($sql,array($autoFlag,$receivedpaymentsid));
}










