<?php
error_reporting(0);
$dir= __DIR__;
$dir=rtrim($dir,'/cron');
ini_set("include_path", $dir);
set_time_limit(0);
include_once('config.php');
include_once('include/utils/utils.php');
include_once('include/logging.php');
global $adb;
function sendMail($Subject,$body,$address=array(),$fromname='CRM系统',$sysid='1',$cc=array()) {
    global $adb, $current_user;
    $query = "SELECT * FROM `vtiger_systems` WHERE server_type='email' AND id=?";
    $result = $adb->pquery($query, array($sysid));
    $result = $adb->query_result_rowdata($result, 0);
    require('class.phpmailer.php');
    $mailer=new PHPMailer();
    $mailer->IsSmtp();
    //$mailer->SMTPDebug = true;
    $mailer->SMTPAuth=$result['smtp_auth'];
    $mailer->Host=$result['server'];
    $mailer->SMTPSecure = "SSL";
    $mailer->Username = $result['server_username'];//用户名
    $mailer->Password = $result['server_password'];//密码
    $mailer->From = $result['from_email_field'];//发件人
    $mailer->FromName = $fromname;
    //收件人地址设置
    if(empty($address) || count($address) == 0) exit;
    foreach($address as $value){
        $mailer->AddAddress($value["mail"], $value["name"]);//收件人的地址
    }
    //抄送人地址设置
    if(!empty($cc) && count($cc) > 0) {
        foreach($cc as $value){
            $mailer->AddCC($value["mail"], $value["name"]);//抄送人的地址
        }
    }
    $mailer->WordWrap = 100;
    $mailer->IsHTML(true);
    $mailer->Subject = $Subject;
    $mail_body = $body;
    //$mail_body .= '<br><br>&nbsp;以上,请及时处理。<br><br>';
    $mail_body .= '&nbsp;<font color="red">系统邮件</font>';

    $mailer->Body = $mail_body;

    $email_flag=$mailer->Send()?'SENT':'Faile';
    $adb->pquery('INSERT INTO vtiger_emaildetails (`emailid`,`from_email`,`to_email`,`cc_email`,`bcc_email`,`email_flag`,module) SELECT emailid+1,\'crm@71360.com\',\'william.zhao@71360.com\',\'zongmi@71360.com\',1,?,\'smownchange\' FROM vtiger_emaildetails ORDER BY emailid DESC LIMIT 1',array($email_flag));
}
$startdate=date('Y-m-d',strtotime('-30 day')).' 00:00:00';
$starttime=strtotime('-30 day');
$result=$adb->pquery("SELECT vtiger_account.accountname,vtiger_accountsmowneridhistory.accountid FROM vtiger_accountsmowneridhistory LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_accountsmowneridhistory.accountid WHERE createdtime>? AND vtiger_accountsmowneridhistory.accountid>0 AND vtiger_account.finalnotice<? AND vtiger_accountsmowneridhistory.newsmownerid!=vtiger_accountsmowneridhistory.modifiedby GROUP BY accountid HAVING sum(1)>3",array($startdate,$starttime));
$num=$adb->num_rows($result);
if($num>0) {
    $body = '以下公司30天内划转客户超过3次<br><table style="border-collapse: collapse;border:solid 1px #CCC;color:#666;font-size:12px;"><thead></thead>';
    $currenttime=time();
    for ($i = 0; $i < $num; $i++) {
        $accountid=$adb->query_result($result,$i,'accountid');
        $adb->pquery("UPDATE `vtiger_account` SET finalnotice=".$currenttime." WHERE accountid=".$accountid,array());
        $body .= '<tr>
                    <td  style="border:solid 1px #CCC;" nowrap><a href="http://192.168.1.3/index.php?module=Accounts&view=Detail&record=' .$accountid. '" style="text-decoration: none;">' . $adb->query_result($result,$i,'accountname') . '</a></td>
                 </tr>';
    }
    $body.='</table>';

    //$address=array(array("mail"=>'steel.liu@71360.com', "name"=>'zongmi'));
    $address=array(array("mail"=>'zongmi@71360.com', "name"=>'zongmi'));
    $cc=array(array("mail"=>'william.zhao@71360.com', "name"=>'william'),array("mail"=>'charles.xu@71360.com',"name"=>'charles.xu'),array('zxjiancha@71360.com',"name"=>'zxjiancha'));

    sendMail('公司30天内划转客户超过3次',$body,$address,$fromname='ERP系统',$sysid='1',$cc);

}



