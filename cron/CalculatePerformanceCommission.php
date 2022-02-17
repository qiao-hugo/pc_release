<?php
define("AUTO_TOKEN",md5(date('Y-m-d H:i:s')));
ini_set("include_path", "../");
require_once('config.php');
require_once('include/utils/utils.php');
require_once('include/logging.php');
header("Content-type:text/html;charset=utf-8");
//error_reporting(-1);
//ini_set("display_errors",1);
include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';
set_time_limit(0);
global $adb;

$needHandleReceivedpayment=getNeedHandleReceivedpayment();
if($needHandleReceivedpayment){
    //需要处理回款的
    $matchBasicAjax=new Matchreceivements_BasicAjax_Action();
    foreach ($needHandleReceivedpayment as $contractId => $handleReceivedpayment){
        Matchreceivements_Record_Model::recordLog('合同id'.$contractId,'autoAchievementallot');
        $sql="SELECT vtiger_servicecontracts.parent_contracttypeid,vtiger_servicecontracts.contract_type,vtiger_activationcode.status,vtiger_activationcode.activationcodeid FROM vtiger_servicecontracts LEFT JOIN vtiger_activationcode ON vtiger_servicecontracts.servicecontractsid=vtiger_activationcode.contractid WHERE vtiger_servicecontracts.servicecontractsid=? and vtiger_servicecontracts.modulestatus='c_complete'";
        $result=$adb->pquery($sql,array($contractId));
        if($adb->num_rows($result)>0){
            $parent_contracttypeid=$adb->query_result($result,0,'parent_contracttypeid');
            $contract_type=$adb->query_result($result,0,'contract_type');
            $orderStatus=2;
            for ($i=0;$i<$adb->num_rows($result);$i++){
                $status=$adb->query_result($result,$i,'status');
                if($status!=2){
                    $orderStatus=1;
                    break;
                }
            }
            $activationcodeid=$adb->query_result($result,0,'activationcodeid');
            $accountId=$adb->query_result($result,0,'sc_related_to');
            if($parent_contracttypeid==2&&(!$activationcodeid||$orderStatus==2)){
                //是T云web合同，无订单或者订单是废除状态不用继续往下走了
                Matchreceivements_Record_Model::recordLog('T云合同订单有问题'.$contractId,'autoAchievementallot');
                continue;
            }
            Matchreceivements_Record_Model::recordLog('开始算业绩'.$contractId,'autoAchievementallot');
            //开始算业绩
            foreach ($handleReceivedpayment as $receivedpayment){
                $isStaymentComplete=getStaymentCanComplete($contractId,$receivedpayment['receivedpaymentsid']);
                if(!$isStaymentComplete){
                    //有未签收的代付款
                    Matchreceivements_Record_Model::recordLog('有未签收的代付款'.$contractId,'autoAchievementallot');
                    continue;
                }
                $sql="select smownerid from vtiger_receivedpayments_notes where receivedpaymentsid=? order by receivedpaymentsnotesid desc limit 1";
                $result=$adb->pquery($sql,array($receivedpayment['receivedpaymentsid']));
                $smownerid=$adb->query_result($result,0,'smownerid');
                $sql="SELECT shareaccountid FROM vtiger_shareaccount WHERE vtiger_shareaccount.userid=? and vtiger_shareaccount.sharestatus=1 AND vtiger_shareaccount.accountid=?";
                $result=$adb->pquery($sql,array($smownerid,$accountId));
                $shareUser=0;
                if($adb->num_rows($result)>0){
                    $shareUser=1;
                }
                Matchreceivements_Record_Model::recordLog(array('回款',$receivedpayment['receivedpaymentsid'],$receivedpayment['unit_price'],$shareUser,$smownerid,$contractId),'autoAchievementallot');
                $matchBasicAjax->commonInsertAchievementallotStatistic($receivedpayment['receivedpaymentsid'],$receivedpayment['unit_price'],$shareUser,$smownerid,$contractId);
            }
        }else{
            //如果合同没签收，退回
            Matchreceivements_Record_Model::recordLog('合同没签收'.$contractId,'autoAchievementallot');
            continue;
        }
    }
}

/***
 * 是否该回款匹配的代付款已签收
 * @param $contractId
 */
function getStaymentCanComplete($contractId,$receivedpaymentsid){
    global $adb;
    $sql="SELECT vtiger_staypayment.staypaymentid FROM vtiger_servicecontracts LEFT JOIN vtiger_receivedpayments ON vtiger_servicecontracts.servicecontractsid=vtiger_receivedpayments.relatetoid LEFT JOIN vtiger_staypayment ON vtiger_receivedpayments.staypaymentid=vtiger_staypayment.staypaymentid WHERE vtiger_servicecontracts.servicecontractsid=? AND vtiger_staypayment.modulestatus !='c_complete' and vtiger_receivedpayments.receivedpaymentsid=? and vtiger_staypayment.modulestatus !='a_exception'";
    $result=$adb->pquery($sql,array($contractId,$receivedpaymentsid));
    if($adb->num_rows($result)>0){
        return false;
    }
    return true;
}

/**
 * 获取要处理的回款
 */
function getNeedHandleReceivedpayment(){
    global $adb;
    $sql="select * from vtiger_receivedpayments where ischeckachievement=0";
    $receivedpaymentList=$adb->run_query_allrecords($sql);
    $receivedpaymentArray=array();
    foreach ($receivedpaymentList as $receivedpayment){
        $receivedpaymentArray[$receivedpayment['relatetoid']][]=$receivedpayment;
    }
    Matchreceivements_Record_Model::recordLog(array('开始新一轮算业绩',array_keys($receivedpaymentArray)),'autoAchievementallot');
    return $receivedpaymentArray;
}