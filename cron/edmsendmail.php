<?php
/**
 * Created by PhpStorm.
 * User: zd-yf3131
 * Date: 2016/8/9
 * Time: 10:49
 * dec:EDM定时发邮件
 */
date_default_timezone_set('Asia/Shanghai');
$basename= dirname(__FILE__);
$basename=rtrim($basename,'cron');
ini_set("include_path", $basename);
require_once('config.php');
set_time_limit(0);
ignore_user_abort(true);
$mysql = mysqli_connect($dbconfig['db_server'],$dbconfig['db_username'],$dbconfig['db_password'],$dbconfig['db_name'],ltrim($dbconfig['db_port'],":")) or die("数据库连接失败");
mysqli_query($mysql,"set charset set utf8");
mysqli_query($mysql,"set names utf8");
$query = "SELECT * FROM `vtiger_systems` WHERE server_type='email' AND id=2";
if($res=mysqli_query($mysql,$query)){
    $result=mysqli_fetch_assoc($res);
    mysqli_free_result($res);
}else{
    mysqli_close($mysql);
    exit();
}


$result['from_email_field'] = $result['from_email_field'] != '' ? $result['from_email_field'] : $result['server_username'];

$query1 = "SELECT sendmailid,`subject`,body,inorout FROM vtiger_sendmail WHERE vtiger_sendmail.email_flag='nosender'";

if($res1=mysqli_query($mysql,$query1)){
    $arr=array();
    $ids='';
    while($row=mysqli_fetch_assoc($res1)){
        $arr[$row['sendmailid']]['subject']=$row['subject'];
        $arr[$row['sendmailid']]['body']=$row['body'];
        $ids.=$row['sendmailid'].',';
    }
    mysqli_free_result($res1);
    $ids=rtrim($ids,',');
}else{
    mysqli_close($mysql);
    die();
}

$query2="SELECT vtiger_mailaccount.mailaccountid,vtiger_mailaccount.sendmailid,vtiger_mailaccount.accountid,vtiger_mailaccount.email as email1,IFNULL(email_flag,'sendnow') AS mail_flag FROM vtiger_mailaccount WHERE email_flag IS NULL AND vtiger_mailaccount.sendmailid in({$ids})";
if($res2=mysqli_query($mysql,$query2)){
    $result2=array();
    while($row=mysqli_fetch_assoc($res2)){
        $result2[$row['sendmailid']][]=$row;
    }
    mysqli_free_result($res2);
}else{
    mysqli_close($mysql);
    die();
}
global $root_directory;
require_once $root_directory.'cron/class.phpmailer.php';
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
$mailer->FromName = '珍岛市场部';
$email_accountmailflag='';//邮件是否发送成功
$email_reason='';//失败的原因
$email_sendtime='';//发送邮件的时间
$mailaccountid='';//更新发送状态记录ID;
$sendmailid='';
foreach($arr as $key=>$value){
    $datetime=date('Y-m-d H:i:s');
    $sql="UPDATE vtiger_sendmail SET email_flag='sender',sendtime='{$datetime}' WHERE sendmailid={$key}";
    mysqli_query($mysql,$sql);
    preg_match_all("/<img(.*)(src=\"[^\"]+\")[^>]+>/isU",  $value['body'], $arrArray);
    $path=$_SERVER['DOCUMENT_ROOT'];
    for($i=0;$i<count($arrArray[2]);++$i){
        //如果图片匹配不到将以附件发送,去掉匹配不到的图片,网络地址不过滤看一下是存http
        if(stripos($value['body'],$arrArray[2][$i])&&!stripos($arrArray[2][$i],'http')){
            $img=rtrim(substr($arrArray[2][$i],strrpos($arrArray[2][$i],'/image')+1),'"');
            $mailer->addembeddedimage($path.'/ueditor/php/upload/'.$img,'myimg'.$i);
            $value['body']= str_replace($arrArray[2][$i],'src="cid:myimg'.$i.'"', $value['body']);
        }
    }

    foreach($result2[$key] as $val){

        if($val['mail_flag']!='sendnow'){
            //已发送将不再发送
            continue;
        }

        if(checkEmails(trim($val['email1']))){
            $mailer->ClearAddresses();
            $mailer->AddAddress(trim($val['email1']), '');//收件人的地址
            $mailer->WordWrap = 100;
            $mailer->IsHTML(true);
            //$mailer->addembeddedimage('./logo.jpg', 'logoimg', 'logo.jpg');
            $mailer->Subject = $value['subject'];
            //加入乱字符开始
            $b=str_replace(array(0,1,2,4,5,6,7,8,9,3),array('b','c','a','f','m','n','t','o','x','q'),$val['mailaccountid']);
            $c=str_replace(array(0,1,2,4,5,6,7,8,9,3),array('ba','cd','af','df','dm','cdn','fdt','sso','ewx','ayq'),$val['mailaccountid']);
            $a=str_replace(array(0,1,2,4,5,6,7,8,9,3),array('sed','dwe','sss','ddss','derwm','werw','ghjy','ttrosso','ffs','mnbv'),$val['mailaccountid']);
            //结束
            $readid=base64encode($val['mailaccountid']);
            $account=base64encode($val['mailaccountid']);
            $unsubscribe='';

            $site_URL='http://192.168.1.3';

            $body='<table cellpadding="0" cellspacing="0" broder="0"  background="'.$site_URL.'/read.php?readid='.$readid.'"></table>';
            $mailer->Body = $body.$value['body'].$unsubscribe;
            $mailer->AltBody = '无法显示邮件';//不支持HTML时显示
            $email_flag=$mailer->Send()?'send':'fail';
            $msg=$email_flag=='fail'?'发送失败':'';
            $datetime=date('Y-m-d H:i:s');
            $email_accountmailflag=$email_flag;
            $email_reason=$msg;
            $email_sendtime=$datetime;
            $message='发送成功';

        }else{
            $datetime=date('Y-m-d H:i:s');
            $email_accountmailflag='fail';
            $email_reason='邮箱错误';
            $email_sendtime=$datetime;
            $message='发送失败';
        }
        $sql="UPDATE vtiger_mailaccount SET email_flag='{$email_accountmailflag}',reason='{$email_reason}',sendtime='{$email_sendtime}' WHERE mailaccountid={$val['mailaccountid']}";
        mysqli_query($mysql,$sql);
    }
}
mysqli_close($mysql);



function checkEmails($str){
    $regex = '/^[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@(?:[-_a-z0-9][-_a-z0-9]*\.)*(?:[a-z0-9][-a-z0-9]{0,62})\.(?:(?:[a-z]{2}\.)?[a-z]{2,})$/i';
    if (preg_match($regex, $str)) {
        return true;
    }
    return false;
}
function base64encode($v){
    //加入乱字符开始
    $b=str_replace(array(0,1,2,4,5,6,7,8,9,3),array('b','c','a','f','m','n','t','o','x','q'),$v);
    $c=str_replace(array(0,1,2,4,5,6,7,8,9,3),array('ba','cd','af','df','dm','cdn','fdt','sso','ewx','ayq'),$v);
    $a=str_replace(array(0,1,2,4,5,6,7,8,9,3),array('sed','dwe','sss','ddss','derwm','werw','ghjy','ttrosso','ffs','mnbv'),$v);
    $d=md5('AccountsiD');
    $e=md5('Useridstrunlandorgnetcomcn');
    //结束
    return base64_encode($a.$d.$b.$e.$c);
}