<?php
/**
 * Created by PhpStorm.
 * User: zd-yf3131
 * Date: 2015/9/15
 * Time: 16:01
 */
date_default_timezone_set('Asia/Shanghai');
ini_set("include_path", "../");
set_time_limit(0);

require_once('config.php');
require_once('include/utils/utils.php');
require_once('include/logging.php');
global $adb;
$query = "SELECT * FROM `vtiger_systems` WHERE server_type='email' AND id=1";
$result = $adb->pquery($query, array());
if (!$adb->num_rows($result)) {
    die('服务配置错误');
}
$result = $adb->query_result_rowdata($result);
$result['from_email_field'] = $result['from_email_field'] != '' ? $result['from_email_field'] : $result['server_username'];
//查找要发送的邮件
$datetime=date("Y-m-d");
$query1 = "SELECT vtiger_account.accountid,vtiger_account.accountname,vtiger_users.email1,vtiger_users.email2,vtiger_users.last_name,vtiger_users.id FROM `vtiger_accountgonghairel` LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_accountgonghairel.accountid LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_account.accountid LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_crmentity.smownerid WHERE vtiger_account.accountid>0 AND vtiger_users.id>0 AND left(vtiger_accountgonghairel.createdtime,10)='{$datetime}'";
$result1 = $adb->run_query_allrecords($query1);
if(!empty($result1)){
    $data=array();
    foreach($result1 as $value){
        $data[$value['id']]['email1']=$value['email1'];
        $data[$value['id']]['email2']=$value['email2'];
        $data[$value['id']]['last_name']=$value['last_name'];
        $data[$value['id']]['accounts'][]=array('accountid'=>$value['accountid'],'accountname'=>$value['accountname']);
    }
}else{
    die('没有要发送的邮件');
}

require_once 'class.phpmailer.php';
$mailer=new PHPMailer();
$mailer->IsSmtp();
//$mailer->SMTPDebug = true;
$mailer->SMTPAuth=$result['smtp_auth'];
$mailer->Host=$result['server'];
//$mailer->Host='smtp.qq.com';
$mailer->SMTPSecure = "SSL";
//$mailer->Port = $result['server_port'];
$mailer->Username = $result['server_username'];//用户名
$mailer->Password = $result['server_password'];//密码
$mailer->From = $result['from_email_field'];//发件人
$mailer->FromName = 'admin';

$sqlvalue="";
$mailer->addembeddedimage(substr(dirname(__FILE__),0,-4).'libraries/bootstrap/css/images/logo.jpg', 'logo_img', 'logo.jpg', 'base64', 'image/jpeg');
$mailer->addembeddedimage(substr(dirname(__FILE__),0,-4).'libraries/bootstrap/css/images/line.jpg', 'line_img', 'line.jpg', 'base64', 'image/jpeg');
foreach($data as $value){
    $emailone=checkEmails(trim($value['email1']));
    $emailtwo=checkEmails(trim($value['email2']));
    $body='<p class="MsoNormal"><span style="font-family:宋体;color:#1F497D"></span><span style="color:#1F497D" lang="EN-US" xml:lang="EN-US">
    <o:p>&nbsp;</o:p>
</span>亲:</p><pre>	您有客户掉公海请即时跟进,避免客户丢失</pre><p class="MsoNormal">&nbsp;</p><p class="MsoNormal"><span style="font-family:宋体;color:#1F497D"></span></p><p class="MsoNormal"><span style="color:#1F497D" lang="EN-US" xml:lang="EN-US"><o:p>';
    $body.='<table cellpadding="0" cellspacing="0" broder="1">';
    foreach($value['accounts'] as $v){
        $body.='<tr><td><a href="http://192.168.1.3/index.php?module=Accounts&view=Detail&record='.$v['accountid'].'">'.$v['accountname'].'</a></td></tr>';
    }
    $body.="</table>";
    $body.='</o:p></span></p><pre><p>&nbsp;</p></pre><pre><p style="font-family:"微软雅黑","sans-serif";color:#7C7C7C">系统邮件,请勿回复</p></pre>
<p class="MsoNormal" align="left" style="text-align:left">
<span style="font-family:"微软雅黑","sans-serif";color:#1F497D" lang="EN-US" xml:lang="EN-US">Best Regards<o:p></o:p></span></p>
<p class="MsoNormal" align="left" style="text-align:left"><span style="font-size:12.0pt;font-family:宋体;color:#7C7C7C" lang="EN-US" xml:lang="EN-US">
<img width="450" height="1" src="cid:line_img" />
<o:p></o:p></span></p>
<p class="MsoNormal" align="left" style="margin-left:5.25pt;
mso-para-margin-left:.5gd;text-align:left;mso-line-height-alt:8.0pt"><b>
<span style="font-family:"微软雅黑","sans-serif";color:black">CRM系统</span><span style="font-family:"微软雅黑","sans-serif";color:#7C7C7C" lang="EN-US" xml:lang="EN-US">
<o:p></o:p></span></b></p><p class="MsoNormal" align="left" style="margin-left:5.25pt;mso-para-margin-left:.5gd;text-align:left;mso-line-height-alt:0pt"><span style="font-family:"微软雅黑","sans-serif";color:#7C7C7C" lang="EN-US" xml:lang="EN-US">Tel:&nbsp;+8621&nbsp;&nbsp;66080765&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fax:&nbsp;+8621&nbsp;36397801&nbsp;<o:p></o:p></span></p><p class="MsoNormal" align="left" style="margin-left:5.25pt;mso-para-margin-left:.5gd;text-align:left;mso-line-height-alt:0pt"><span style="font-family:"微软雅黑","sans-serif";color:#7C7C7C" lang="EN-US" xml:lang="EN-US">Mobile: +86 18121286592<o:p></o:p></span></p><p class="MsoNormal" align="left" style="margin-left:5.25pt;mso-para-margin-left:.5gd;text-align:left;mso-line-height-alt:0pt"><span style="font-family:"微软雅黑","sans-serif";color:#7C7C7C" lang="EN-US" xml:lang="EN-US">E-Mail: </span><span style="font-family:"微软雅黑","sans-serif";color:#1F497D" lang="EN-US" xml:lang="EN-US"><a href="mailto:'.$result['from_email_field'].'">'.$result['from_email_field'].'</a></span><span style="font-family:"微软雅黑","sans-serif";color:#7C7C7C" lang="EN-US" xml:lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal" align="left" style="margin-left:5.25pt;mso-para-margin-left:.5gd;text-align:left;mso-line-height-alt:0pt"><span style="font-family:"微软雅黑","sans-serif";color:#7C7C7C" lang="EN-US" xml:lang="EN-US">Web:&nbsp; </span><span style="font-family:"微软雅黑","sans-serif";color:#1F497D" lang="EN-US" xml:lang="EN-US"><a href="http://www.71360.com/">www.71360.com</a></span><span style="font-family:"微软雅黑","sans-serif";color:#7C7C7C" lang="EN-US" xml:lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal" align="left" style="margin-left:5.25pt;mso-para-margin-left:.5gd;text-align:left;mso-line-height-alt:0pt"><span style="font-family:"微软雅黑","sans-serif";color:#7C7C7C" lang="EN-US" xml:lang="EN-US">Add:&nbsp;</span><span style="font-family:"微软雅黑","sans-serif";color:#7C7C7C">上海市虹口区水电路<span lang="EN-US" xml:lang="EN-US">682</span>号天虹商务大厦<span lang="EN-US" xml:lang="EN-US">6F</span>、<span lang="EN-US" xml:lang="EN-US">7F</span>、<span lang="EN-US" xml:lang="EN-US">11F&nbsp;&nbsp; </span></span><span style="font-family:"微软雅黑","sans-serif";color:#7C7C7C" lang="EN-US" xml:lang="EN-US">PC</span><span style="font-family:"微软雅黑","sans-serif";color:#7C7C7C">：</span><span style="font-family:"微软雅黑","sans-serif";color:#7C7C7C" lang="EN-US" xml:lang="EN-US">200083</span><span style="font-family:"微软雅黑","sans-serif";color:#7C7C7C" lang="EN-US" xml:lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal" align="left" style="text-align:left;mso-line-height-alt:0pt"><span lang="EN-US" xml:lang="EN-US"><a href="http://www.trueland.net/"><span style="font-family:"微软雅黑","sans-serif";color:black;text-decoration:none"><img border="0" width="452" height="73" id="" src="cid:logo_img" /></span></a></span><span style="font-family:宋体;color:#7C7C7C" lang="EN-US" xml:lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US" xml:lang="EN-US"><o:p>&nbsp;</o:p></span></p>
';
    $datetime=date('Y-m-d H:i:s');
    if($emailone||$emailtwo){
        if($emailone){
            $sendMailAddress=$value['email1'];
        }else{
            $sendMailAddress=$value['email2'];
        }
        $mailer->ClearAddresses();
        $mailer->AddAddress($sendMailAddress, $value['last_name']);//收件人的地址
        //$mailer->AddAddress('steel.liu@trueland.org', $value['last_name']);//收件人的地址
        $mailer->WordWrap = 100;
        $mailer->IsHTML(true);
        $mailer->Subject = '客户掉公海提醒';
        $mailer->Body = $body;
        $mail->AltBody = '无法显示邮件';//不去持HTML时显示
        $email_flag=$mailer->Send()?'send':'fail';
        $sqlvalue.="('{$result['from_email_field']}','{$sendMailAddress}','客户掉公海提醒','{$body}','{$email_flag}','{$datetime}'),";
        //sleep(1);
    }else{
        $sqlvalue.="('{$result['from_email_field']}','email@error','客户掉公海提醒','{$body}','fail','{$datetime}'),";
    }
}
$sqlvalue=rtrim($sqlvalue,',');
$sql='INSERT INTO vtiger_emaildetails (`from_email`,`to_email`,`subject`,`body`,`email_flag`,sendtime) VALUES '.$sqlvalue;
$adb->pquery($sql,array());


function checkEmails($str){
    $regex = '/^[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@(?:[-_a-z0-9][-_a-z0-9]*\.)*(?:[a-z0-9][-a-z0-9]{0,62})\.(?:(?:[a-z]{2}\.)?[a-z]{2,})$/i';
    if (preg_match($regex, $str)) {
        return true;
    }
    return false;
}

