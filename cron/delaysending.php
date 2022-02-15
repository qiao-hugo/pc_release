<?php
$dir= __DIR__;
$dir=rtrim($dir,'/cron');
ini_set("include_path", $dir);

require_once('config.php');
require_once('include/utils/utils.php');
require_once('include/logging.php');

header("Content-type:text/html;charset=utf-8");
//error_reporting(0);
include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';
set_time_limit(0);
ini_set('display_errors','on'); error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
global $rabbitmqConfig,$adb;
if(!class_exists('AMQPConnection')){
    exit;
}
try {

    $query="UPDATE `vtiger_sendmail`,vtiger_visitingorder,vtiger_crmentity SET vtiger_sendmail.email_flag='c_cancel' 
        WHERE vtiger_visitingorder.visitingorderid=vtiger_sendmail.moduleid AND 
        vtiger_visitingorder.visitingorderid= vtiger_crmentity.crmid AND
        vtiger_sendmail.moduleid>0 AND
        (vtiger_visitingorder.modulestatus in('c_cancel','c_canceling','a_exception') OR vtiger_crmentity.deleted=1) AND 
        email_flag='nosender' AND module in('sendWechatMessage','sendMailByMessageQuery')";
    $adb->pquery($query,array());
    $query="SELECT sendmailid,body,module FROM `vtiger_sendmail` WHERE email_flag='nosender' AND module in('sendWechatMessage','sendMailByMessageQuery') AND sendtime<=?";
    $result=$adb->pquery($query,array(date('Y-m-d H:i')));
    if($adb->num_rows($result)){
        $conn = new AMQPConnection($rabbitmqConfig['config']);
        if ($conn->connect()) {
            $channel = new AMQPChannel($conn);
            $ex = new AMQPExchange($channel);
            $ex->setName($rabbitmqConfig['exchangeName']);
            $ex->setType(AMQP_EX_TYPE_DIRECT); //direct类型
            $ex->setFlags(AMQP_DURABLE); //持久化
            $ex->declareExchange();
            $sendmailid='';
            while($row=$adb->fetch_array($result)){
                $data=base64_decode($row['body']);
                $data=json_decode($data);
                $mqdata=array('module'=>'Users',
                    'action'=>$row['module'],
                    'mqdata'=>$data
                );
                $jsonData=json_encode($mqdata);
                $ex->publish($jsonData, $rabbitmqConfig['routeName']);
                $sendmailid.=$row['sendmailid'].',';
            }
            $sendmailid=trim($sendmailid,',');
            $sql="UPDATE vtiger_sendmail SET email_flag='sender', updatetime=? WHERE sendmailid in(".$sendmailid.")";
            $adb->pquery($sql,array(date('Y-m-d H:i:s')));
            $conn->disconnect();
        }
    }

}catch (Exception $e){

}







