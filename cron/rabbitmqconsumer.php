<?php
error_reporting(0);
$dir=trim(__DIR__,DIRECTORY_SEPARATOR);
$dir=trim(__DIR__,'cron');
ini_set("include_path", $dir);
require_once('config.php');
require_once('include/utils/utils.php');
require_once('include/logging.php');
//error_reporting(0);
include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';
set_time_limit(0);
if('cli'===php_sapi_name()){
    echo 'start....'."\n";
    //创建连接和channel
    $conn = new AMQPConnection($rabbitmqConfig['config']);
    if (!$conn->connect()) {
        _logs(array('mq连接失败'));
        die("Cannot connect to the broker!\n");
    }
    $channel = new AMQPChannel($conn);
    //创建交换机
    $ex = new AMQPExchange($channel);
    $ex->setName($rabbitmqConfig['exchangeName']);
    $ex->setType(AMQP_EX_TYPE_DIRECT); //direct类型
    $ex->setFlags(AMQP_DURABLE); //持久化
    $ex->declareExchange();
    //创建队列
    $q = new AMQPQueue($channel);
    $q->setName($rabbitmqConfig['queryName']);
    $q->setFlags(AMQP_DURABLE); //持久化
    $q->declareQueue();
    //绑定交换机与队列，并指定路由键
    $q->bind($rabbitmqConfig['exchangeName'], $rabbitmqConfig['routeName']);
    //阻塞模式接收消息
	while(1){
		$q->consume('processMessage', AMQP_AUTOACK); //自动ACK应答
	}
    //$conn->disconnect();

}else{
    header("HTTP/1.0 404 Not Found");
    exit;
}
/**
 * 消费回调函数
 * 处理消息
 */
function processMessage($envelope, $queue) {
    $msg = $envelope->getBody();
    _logs($msg);
    $msgData=json_decode($msg,true);
    if(empty($msgData['module'])){
        return ;
    }
    $modulefile=DIRECTORY_SEPARATOR.trim(trim(__DIR__,DIRECTORY_SEPARATOR),'cron').DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$msgData['module'].DIRECTORY_SEPARATOR.$msgData['module'].'.php';
    if(!file_exists($modulefile)){
        _logs($modulefile);
        return ;
    }
    try{
        $recordModel=Vtiger_Record_Model::getCleanInstance($msgData['module']);
        if(!method_exists($recordModel,$msgData['action'])){
            _logs($msgData['action'],method_exists($recordModel,$msgData['action']));
            throw new Exception('');
        }
        $recordModel->$msgData['action']($msgData['mqdata']);
    }catch(Exception $exception){
        //只接收不处理防止程序Die
    }
}
function _logs($data, $file = 'logs_rabbitmq'){
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






