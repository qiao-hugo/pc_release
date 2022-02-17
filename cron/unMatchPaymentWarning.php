<?php
$dir=trim(__DIR__,DIRECTORY_SEPARATOR);
$dir=trim(__DIR__,'cron');
ini_set("include_path", $dir);
require_once('config.php');
require_once('include/utils/utils.php');
require_once('include/logging.php');
header("Content-type:text/html;charset=utf-8");
error_reporting(E_ALL);
include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';
vglobal('default_language', $default_language);
$currentLanguage = 'zh_cn';
vglobal('current_language', $currentLanguage);
global $adb;

//获取过期的未回款的发票
$sql="SELECT
  vtiger_newinvoice.modulestatus,
	vtiger_newinvoice.invoicestatus,
	vtiger_newinvoice.invoiceid,
	vtiger_crmentity.smownerid,
	vtiger_users.email1 as email,
	vtiger_newinvoice.invoiceno,
	vtiger_newinvoice.trialtime,
	IFNULL((vtiger_newinvoiceremind.over_days),90) as over_days,
	IFNULL((vtiger_newinvoiceremind.days),7) as remind_day,
	vtiger_departments.parentdepartment,
	vtiger_user2department.departmentid,
	TIMESTAMPDIFF(DAY,vtiger_newinvoice.trialtime,DATE_FORMAT(NOW(), '%Y-%m-%d')) as days,
	vtiger_preinvoicedeferral.applicantdays as delaydays,
	vtiger_newinvoice.lockstatus,
	vtiger_preinvoicedeferral.modulestatus as delaystatus
FROM
	vtiger_newinvoice
	LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_newinvoice.invoiceid
	LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
	left join vtiger_user2department ON vtiger_users.id = vtiger_user2department.userid
	left join vtiger_newinvoiceremind ON vtiger_newinvoiceremind.department = vtiger_user2department.departmentid
	left join vtiger_departments ON vtiger_user2department.departmentid = vtiger_departments.departmentid
	left join vtiger_preinvoicedeferral ON vtiger_preinvoicedeferral.invoiceid = vtiger_newinvoice.invoiceid
WHERE
	vtiger_newinvoice.invoicetype = 'c_billing' 
	AND vtiger_newinvoice.matchover = 0
	and vtiger_newinvoice.matchtimeover=0
	and vtiger_newinvoice.modulestatus not in ('c_canceling','c_cancel')";
//$sql.=" and vtiger_newinvoice.invoiceid='602702'";
$result = $adb->pquery($sql,array());
if(!$adb->num_rows($result)){
    //没有要处理的
    return;
}

$sockAccounts=array();
$remindArray=array();



while ($row = $adb->fetchByAssoc($result)){
    if(!$row['email']||!$row['invoiceno']||!$row['days']||($row['modulestatus']=='c_complete'&&$row['invoicestatus']=='redinvoice')){
        //如果是红冲类型统统去掉
        continue;
    }
    $delayFlag=true;//
    if($row['delaydays']&&$row['delaystatus']=='c_complete'){

        strtotime($row['delaydays'].' 23:59:59')-time()<0&&$delayFlag=false;
    }
    //两种情况下锁定，一种延期的情况下还是延期了，还有一种在没有延期的情况下设置的天数超过了
    if((!$delayFlag&&$row['delaydays'])||(!$row['delaydays']&&($row['days']>$row['over_days']))){
        //超过时间了,锁账号并通知
        $sockAccounts[$row['email']]['invoiceId']=$row['invoiceid'];
        $sockAccounts[$row['email']]['userId']=$row['smownerid'];
        $sockAccounts[$row['email']]['departmentid']=$row['departmentid'];
        $sockAccounts[$row['email']]['parentDepartment']=$row['parentdepartment'];
        $sockAccounts[$row['email']]['invoice'][]=$row['invoiceno'];
    }else if($row['days']==($row['over_days']-$row['remind_day'])){
        //等于提醒日期发邮件
        $remindArray[$row['email']]['userId']=$row['smownerid'];
        $remindArray[$row['email']]['departmentid']=$row['departmentid'];
        $remindArray[$row['email']]['parentDepartment']=$row['parentdepartment'];
        $remindArray[$row['email']]['invoice'][]=$row['invoiceno'];
    }
}

$Subject = '账号已锁定提醒！！！';
foreach ($sockAccounts as $email => $sockAccount){
    $str = '您好!<br>';
    $str .= "    因为你有超出时间未匹配回款的数据，本账号已锁定，请联系上级解锁。<br>
            涉及数据详情为：<br> ";
    foreach ($sockAccount['invoice'] as $key => $invoice){
        $str.=($key+1).".发票编号：".$invoice."<br>";
    }
    //查一次此账号关联上级
    $ccArray=getCcEmail($sockAccount['parentDepartment'],$sockAccount['departmentid']);
    //发送邮件
//    $email='stark.tian@71360.com';
//    $ccArray=array();
//    $ccArray[0]['mail']='844577216@qq.com';
    Vtiger_Record_Model::sendMail($Subject, $str,  array(array('mail' => $email, 'name' => '')),'CRM系统','1',$ccArray);
    //发完邮件更改数据库
    updateNewInvoice($sockAccount['invoice']);
}

$Subject = '预开票待匹配提醒！！！';
foreach ($remindArray as $email => $remind){
    $str = '您好!<br>';
    $str .= "    你于".date("Y-m-d H:i:s")."，在  “ERP系统---财务模块---发票（新）”中，还有未匹配回款的数据，请及时处理。谢谢！<br>
            涉及数据详情为：<br> ";
    foreach ($remind['invoice'] as $key => $invoice){
        $str.=($key+1).".发票编号：".$invoice."<br>";
    }
    //查一次此账号关联上级
    $ccArray=getCcEmail($sockAccount['parentDepartment'],$sockAccount['departmentid']);
    //发送邮件
    Vtiger_Record_Model::sendMail($Subject, $str,  array(array('mail' => $email, 'name' => '')),'CRM系统','1',$ccArray);
}

echo '发送完成';

/**
 * 获取抄送邮件列表
 * @param $parentDepartment
 * @param $departmentid
 * @return array
 */
function getCcEmail($parentDepartment,$departmentid){
    $emailArray=array();
    global $adb;
    $parentDepartment= "('".str_replace('::','\',\'',str_replace('H1::','',rtrim($parentDepartment,'::'.$departmentid)))."')";
    $sql="select distinct  email1 from vtiger_users where id in  (select userid from vtiger_user2department where departmentid in ".$parentDepartment." )";
    $result = $adb->pquery($sql,array());
    while ($row = $adb->fetchByAssoc($result)){
        $emailArray[]['mail']=$row['email1'];
    }
    return $emailArray;
}

/**
 * 更新newinvoic表,代表已通知
 * @param $invoices
 */
function updateNewInvoice($invoices){
    global $adb;
    $invoices="('".implode('\',\'',$invoices)."')";
    $sql="update vtiger_newinvoice set matchtimeover=1,lockstatus=1  where invoiceno in ".$invoices;
    $adb->pquery($sql,array());
}
