<?php
ini_set("include_path", "../");

require_once('config.php');
require_once('include/utils/utils.php');
require_once('include/logging.php');
header("Content-type:text/html;charset=utf-8");
//ini_set('display_errors','on'); error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);


include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';


vglobal('default_language', $default_language);
$currentLanguage = 'zh_cn';
vglobal('current_language', $currentLanguage);
global $adb,$configcontracttypeNameTYUN;

$nowDate= date('Y-m-d',strtotime("-1 days"));
//$nowDate= '2020-08-21';
$endDate = date("Y-m-d 23:59:59");
//$endDate = '2020-08-21 23:59:59';
$sql = "select a.contractid,a.startdate,a.customerid,b.contract_no,a.creator,b.contract_type,b.modulestatus from vtiger_activationcode a left join vtiger_servicecontracts b on a.contractid=b.servicecontractsid 
where a.startdate>=? and a.startdate<=? and b.modulestatus!='c_complete'  AND NOT EXISTS( select 1 from vtiger_contractdelaysign c where c.servicecontractsid=a.contractid) group by contractid";
$result = $adb->pquery($sql,array($nowDate,$endDate));
echo '查到当天未签收的合同数量:'.$adb->num_rows($result).'<br>';
if($adb->num_rows($result)){
    while ($row = $adb->fetchByAssoc($result)){
        $lastsigndate = date("Y-m-d",strtotime($row['startdate'])+30*24*60*60);
        global $current_user;
        $user = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile($row['creator']);
        echo $row['contractid'].' '.$row['contract_no'].'<br>';
        $_REQUES['record'] = '';
        $request = new Vtiger_Request($_REQUES, $_REQUES);
        $request->set('servicecontractsid',  $row['contractid']);
        $request->set('accountid',  $row['customerid']);
        $request->set('type', 'tyun');
        $request->set('contract_type', $row['contract_type']);
        $request->set('activedate',$row['startdate']);
        $request->set('modulestatus', 'a_apply_normal');
        $request->set('hetongstatus', $row['modulestatus']);
        $request->set('contractsignstatus', 'nosign');
        $request->set('lastsigndate',$lastsigndate);
        $request->set('contract_no', $row['contract_no']);
        $request->set('creator', $row['creator']);
        $request->set('isdelay',0);
        $request->set('module', 'ContractDelaySign');
        $request->set('view', 'Edit');
        $request->set('action', 'Save');
        $ressorder = new Vtiger_Save_Action();
        $ressorderecord = $ressorder->saveRecord($request);
    }
}
echo '查到当天未签收的生成成功<br>';



//记录未签收t云合同每天延期的天数
$nowEndDate = date("Y-m-d 23:59:59");
//修改合同延期天数
$sql = "select * from vtiger_contractdelaysign where activedate<? and deleted=0 and contractsignstatus='nosign' and contract_type in('T云WEB版','T云院校版','T云集团版')";
$result = $adb->pquery($sql,array($nowEndDate));
while ($row = $adb->fetchByAssoc($result)){
    $nowDay = round((strtotime($nowEndDate)-strtotime($row['activedate']))/(24*60*60));
    if($nowDay>0){
        $adb->pquery("update vtiger_contractdelaysign set isdelay=1,delaydays=".$nowDay." where contractdelaysignid=?",array($row['contractdelaysignid']));
    }
}
echo '记录小SaaS当天延期的合同和天数<br>';


$nowEndDay=date("Y-m-d");
//修改合同延期天数
$sql = "select * from vtiger_contractdelaysign where lastsigndate=? and deleted=0  and contractsignstatus='nosign' and contract_type not in('T云WEB版','T云院校版','T云集团版')";
$result = $adb->pquery($sql,array($nowEndDay));
while ($row = $adb->fetchByAssoc($result)){
    $adb->pquery("update vtiger_contractdelaysign set isdelay=1 where contractdelaysignid=?",array($row['contractdelaysignid']));
}
echo '记录大SaaS当天延期的合同<br>';


$thirtyDate = date("Y-m-d",strtotime("+30 days"));
$fifteenDate = date("Y-m-d",strtotime("+15 days"));
$sevenDate = date("Y-m-d",strtotime("+7 days"));

$sql = "select a.*,b.contract_no,c.accountname,e.smownerid,f.smownerid as lastcreator from vtiger_contractdelaysign a 
  left join vtiger_servicecontracts b on a.servicecontractsid=b.servicecontractsid 
  left join vtiger_account c on b.sc_related_to=c.accountid
  left join vtiger_crmentity e on e.crmid=c.accountid
  left join vtiger_crmentity f on f.crmid=b.servicecontractsid
where a.deleted=0 and a.creator in (select d.creator from vtiger_contractdelaysign d where d.deleted=0 and d.lastsigndate in(?,?,?) and a.contractsignstatus='nosign') and a.contractsignstatus='nosign'";

$result5 = $adb->pquery($sql,array($thirtyDate,$fifteenDate,$sevenDate));
while ($row5=$adb->fetchByAssoc($result5)){
    $notifyData[$row5['creator']][]=array(
        'contract_no'=>$row5['contract_no'],
        'accountname'=>$row5['accountname'],
        'contract_type'=>$row5['contract_type'],
        'delaydays'=>30,
        'canapply'=>(in_array($row5['contract_type'],$configcontracttypeNameTYUN)&&canapply($row5))? true : false,
        'lastsigndate'=>$row5['lastsigndate']
    );

    if($row5['creator']!=$row5['smownerid'] && !in_array($row5['contract_type'],$configcontracttypeNameTYUN)){
        $notifyData[$row5['smownerid']][]=array(
            'contract_no'=>$row5['contract_no'],
            'accountname'=>$row5['accountname'],
            'contract_type'=>$row5['contract_type'],
            'delaydays'=>30,
            'canapply'=>(in_array($row5['contract_type'],$configcontracttypeNameTYUN)&&canapply($row5) )? true : false,
            'lastsigndate'=>$row5['lastsigndate']
        );
    }

    $contractNo .= $row5['contract_no'].',';
}
$serviceContractRecordModel = ServiceContracts_Record_Model::getCleanInstance("ServiceContracts");
if(!empty($notifyData)){
    echo '发送提醒消息的合同'.$contractNo;
    $serviceContractRecordModel->sendSignWarnToSaleEmail($notifyData);
}
echo '发送提醒消息结束';




//延期提醒
$sql="select a.*,a.creator,b.contract_no,c.accountname,b.contract_type,a.lastsigndate,d.smownerid,e.smownerid as lastcreator from vtiger_contractdelaysign a
  left join vtiger_servicecontracts b on a.servicecontractsid=b.servicecontractsid
  left join vtiger_account c on b.sc_related_to=c.accountid
  left join vtiger_crmentity d on d.crmid=c.accountid
  left join vtiger_crmentity e on e.crmid=b.servicecontractsid
where a.deleted=0 and a.contractsignstatus='nosign'";
//30天通知
$zeroDate = date("Y-m-d");
$sql4 = $sql .' and lastsigndate=?';
$result4 = $adb->pquery($sql4,array($zeroDate));
$contractNo='';
$notifyData2=array();
while ($row4 = $adb->fetchByAssoc($result4)){
    $notifyData2[$row4['creator']][]=array(
        'contract_no'=>$row4['contract_no'],
        'accountname'=>$row4['accountname'],
        'contract_type'=>$row4['contract_type'],
        'delaydays'=>0,
        'canapply'=>(in_array($row4['contract_type'],$configcontracttypeNameTYUN)&&canapply($row4) )? true : false,
        'lastsigndate'=>$row4['lastsigndate']
    );

    if($row4['creator']!=$row4['smownerid'] && !in_array($row4['contract_type'],$configcontracttypeNameTYUN)){
        $notifyData2[$row4['smownerid']][]=array(
            'contract_no'=>$row4['contract_no'],
            'accountname'=>$row4['accountname'],
            'contract_type'=>$row4['contract_type'],
            'delaydays'=>0,
            'canapply'=>(in_array($row4['contract_type'],$configcontracttypeNameTYUN)&&canapply($row4) )? true : false,
            'lastsigndate'=>$row4['lastsigndate']
        );
    }
    if(in_array($row4['contract_type'],$configcontracttypeNameTYUN)){
        $contractNos[] = $row4['contract_no'];
    }
    $contractNo .= $row4['contract_no'].',';
}
echo '当天到期时通知:'.$contractNo.'<br>';
if($contractNos){
    echo '批量通知停止未签收合同的订单'.$contractNo.'<br>';
    $resultData = $serviceContractRecordModel->batchStopOrder($contractNos);
    if(!empty($resultData)) {
        _logs(array($resultData));
    }
}
if(empty($notifyData2)){
    echo '没有通知<br>';
    return;
}

$serviceContractRecordModel->sendSignWarnToSaleEmail($notifyData2,true);



function _logs($data, $file = 'logs_tyuncontractdelaysign'){
    $year	= date("Y");
    $month	= date("m");
    $dir=trim(__DIR__,'cron');
    $dir.= 'logs/tyun/' . $year . '/' . $month . '/';
    if(!is_dir($dir)) {
        mkdir($dir,0755,true);
    }
    $file = $dir . $file . date('Y-m-d').'.txt';
    @file_put_contents($file, '----------------' . date('H:i:s') . '--------------------'.PHP_EOL.var_export($data,true).PHP_EOL, FILE_APPEND);
}

function canapply($row){
    return in_array($row['modulestatus'],array('a_apply_normal','c_apply_stop')) && !in_array($row['hetongstatus'],array('c_stop','c_cancel','c_canceling')) && $row['contractsignstatus']=='nosign';
}
