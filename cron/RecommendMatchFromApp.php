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
    Matchreceivements_Record_Model::recordLog('开始新一轮推荐匹配','remcommend_logs');
    global $adb;
    $sql="SELECT
	receivedpaymentsid,automatchflag
FROM
	vtiger_receivedpayments
WHERE
	( relatetoid = 0 OR relatetoid IS NULL  or  relatetoid='')
	AND receivedstatus = 'normal'
	AND deleted = 0
	and createtime >'2021-11-01 18:30:00'
	and receivedpaymentsid not in (SELECT distinct receivedpaymentsid FROM vtiger_achievementallot_statistic WHERE  isover=1)
	and automatchflag in (0,1) and (pushdate is  null or pushdate<'".date('Y-m-d')."')";
    $autoMatchPaymentArray=$adb->run_query_allrecords($sql);
    //获取今天没有发过的回款
    if($autoMatchPaymentArray){
        $recommendArray=array();
        Matchreceivements_Record_Model::recordLog('有未匹配的回款','remcommend_logs');
        foreach ($autoMatchPaymentArray as $autoMatchPayment){
            if($autoMatchPayment['automatchflag']==1){
                //回款大于合同金额，通知
                recommendMatch($autoMatchPayment['receivedpaymentsid'],2);
            }else{
                //能推荐匹配的
                array_push($recommendArray,$autoMatchPayment['receivedpaymentsid']);
            }
        }
        if($recommendArray){
            //开始推荐匹配
            foreach ($recommendArray as $recommend){
                $recommend&&recommendMatch($recommend,1);

                Matchreceivements_Record_Model::recordLog('结束推荐匹配','remcommend_logs');
            }
        }
        exit();
    }else{
        Matchreceivements_Record_Model::recordLog('本轮没有可以推荐匹配的回款','remcommend_logs');
        echo '没未匹配的回款';
        exit();
    }
}

function recommendMatch($recommendArray,$type){
    global $adb;
    $sql="select receivedpaymentsid as id,owncompany,paymentchannel,paymentcode,unit_price,paytitle,maybe_account,reality_date from vtiger_receivedpayments where receivedpaymentsid=".$recommendArray;
    Matchreceivements_Record_Model::recordLog(array($sql),'remcommend_logs');
    $receivedArray=$adb->run_query_allrecords($sql);
    $maybe_account=$receivedArray[0]['maybe_account'];
    $payTitle=$receivedArray[0]['paytitle'];
    $unit_price=$receivedArray[0]['unit_price'];
    $reality_date=$receivedArray[0]['reality_date'];
    $aArray=array();
    if($maybe_account){
        //有合同的客户
        $sql="SELECT vtiger_crmentity.smownerid,vtiger_account.accountname FROM vtiger_account LEFT JOIN vtiger_crmentity ON vtiger_account.accountid=vtiger_crmentity.crmid WHERE vtiger_account.accountid=".$maybe_account;
        $accountArray=$adb->run_query_allrecords($sql);
        $result=array_merge($receivedArray[0],$accountArray[0]);
    }else{
        $sql="SELECT vtiger_crmentity.smownerid,vtiger_servicecontracts.receiveid,vtiger_servicecontracts.signid FROM vtiger_staypayment LEFT JOIN vtiger_servicecontracts ON vtiger_staypayment.contractid=vtiger_servicecontracts.servicecontractsid LEFT JOIN vtiger_account ON vtiger_servicecontracts.sc_related_to=vtiger_account.accountid LEFT JOIN vtiger_crmentity ON vtiger_account.accountid=vtiger_crmentity.crmid WHERE ((vtiger_staypayment.staypaymenttype='nofixation' AND '".$reality_date."'>=vtiger_staypayment.startdate AND '".$reality_date."'<=vtiger_staypayment.enddate) OR (vtiger_staypayment.staypaymenttype='fixation' AND '".$unit_price."'<=vtiger_staypayment.surplusmoney AND vtiger_staypayment.surplusmoney> 0)) AND ((
REPLACE (vtiger_staypayment.payer,' ','') LIKE
REPLACE (
REPLACE ('".$payTitle."',' ',''),'*','_')) OR (
REPLACE (vtiger_staypayment.staypaymentname,' ','')=
REPLACE ('".$payTitle."',' ',''))) AND vtiger_staypayment.modulestatus !='a_exception'";
        $aArray=$adb->run_query_allrecords($sql);
        $result=array_merge($receivedArray[0],array());
    }
    if($type==1){
        sendQiyeWeixin($result,$aArray);
    }else{
        sendQiyeWeixinToNotice($result,$aArray);
    }

}

/**
 * 发送企业微信信息
 * @param $data
 */
function sendQiyeWeixinToNotice($data,$aArray){
    Matchreceivements_Record_Model::recordLog(array('推送提示信息',json_encode($data,JSON_UNESCAPED_UNICODE)),'remcommend_logs');
    global $adb;
    $isArray=array();
    array_push($isArray,$data['smownerid'],$data['receiveid'],$data['signid']);
    if($aArray){
        foreach ($aArray as $a){
            array_push($isArray,$a['smownerid'],$a['receiveid'],$a['signid']);
        }
    }
    $isArray=array_unique($isArray);
    $isArray=array_filter($isArray);
    $sql="select email1 from vtiger_users where id in (".implode(',',$isArray).")";
    $emailArray=$adb->run_query_allrecords($sql);
    $recordModel=new Vtiger_Record_Model();
    foreach ($emailArray as $email){
        $content="【回款拆分认领】有一份客户到款，回款金额比合同大，请拆分";
        $content.="<br>公司账号:".$data['owncompany']."<br>入账日期:".$data['reality_date']."<br>支付渠道:".$data['paymentchannel']."<br>回款抬头:".$data['paytitle']."<br>回款金额:".$data['unit_price'];
        $data1=array();
        $data1['title']='回款拆分认领';
        $data1['flag']=7;
        $data1['description']=$content;
        $data1['dataurl']='#';
        $data1['email']=trim($email['email1']);
        //        $email['email1']='stark.tian@71360.com';
//        $data1['email']=trim($email['email1']);
        Matchreceivements_Record_Model::recordLog(array('发微信通知拆分',json_encode($data1,JSON_UNESCAPED_UNICODE)),'remcommend_logs');
        $recordModel->sendWechatMessage($data1);
        $sql="update vtiger_receivedpayments set pushdate =? where receivedpaymentsid=?";
        $adb->pquery($sql,array(date('Y-m-d'),$data['id']));
    }
}




/**
 * 发送企业微信信息
 * @param $data
 */
function sendQiyeWeixin($data,$aArray){
    Matchreceivements_Record_Model::recordLog(array('推送信息',json_encode($data,JSON_UNESCAPED_UNICODE)),'remcommend_logs');
    global $adb,$m_crm_url;
    $isArray=array();
    array_push($isArray,$data['smownerid'],$data['receiveid'],$data['signid']);
    if($aArray){
        foreach ($aArray as $a){
            array_push($isArray,$a['smownerid'],$a['receiveid'],$a['signid']);
        }
    }
    $isArray=array_unique($isArray);
    $isArray=array_filter($isArray);
    $sql="select email1 from vtiger_users where id in (".implode(',',$isArray).")";
    $emailArray=$adb->run_query_allrecords($sql);
    $recordModel=new Vtiger_Record_Model();
    foreach ($emailArray as $email){
        $content="【回款认领】有一份客户到款，请确认并及时认领！如果不是您的客户到款，请放弃该笔回款，切勿随意匹配，匹配错误会被处罚！务必谨慎！切记！切记！切记！";
        $content.="<br>公司账号:".$data['owncompany']."<br>入账日期:".$data['reality_date']."<br>支付渠道:".$data['paymentchannel']."<br>回款抬头:".$data['paytitle']."<br>回款金额:".$data['unit_price'];
        $data1=array();
        $data1['title']='回款认领';
        $data1['flag']=7;
        $data1['description']=$content;
        $data1['dataurl']=$m_crm_url.'/index.php?module=IncomeRank&action=index#/waitMatchInfo?id='.$data['id'];
        $data1['email']=trim($email['email1']);
//        $email['email1']='stark.tian@71360.com';
//        $data1['email']=trim($email['email1']);
        Matchreceivements_Record_Model::recordLog(array('发微信通知',json_encode($data1,JSON_UNESCAPED_UNICODE)),'remcommend_logs');
        $recordModel->sendWechatMessage($data1);
        $sql="update vtiger_receivedpayments set pushdate =? where receivedpaymentsid=?";
        $adb->pquery($sql,array(date('Y-m-d'),$data['id']));
    }
}









