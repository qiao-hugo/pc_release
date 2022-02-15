<?php
$dir= __DIR__;
$dir=rtrim($dir,'/cron');
ini_set("include_path", $dir);
//ini_set("include_path", "../");
require_once('config.php');
require_once('include/utils/utils.php');
require_once('include/logging.php');
header("Content-type:text/html;charset=utf-8");
//ini_set('display_errors','on'); error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';
set_time_limit(0);
global $adb;
//定时 查询拜访单 提前三小时 发送消息提醒。
//$query=" SELECT so.performanceoftime,aas.matchdate,aas.reality_date,aas.achievementallotid,aas.scalling,aas.servicecontractid ,aas.receivedpaymentsid,aas.shareuser,s.contract_no,s.total,r.unit_price,s.productid ,s.extraproductid,s.servicecontractstype FROM vtiger_achievementallot_statistic as aas LEFT JOIN  vtiger_servicecontracts as s ON s.servicecontractsid=aas.servicecontractid  LEFT JOIN  vtiger_receivedpayments as r ON r.receivedpaymentsid=aas.receivedpaymentsid LEFT JOIN  vtiger_salesorder as so ON so.servicecontractsid=aas.servicecontractid WHERE   servicecontractid= 198994 ";
$dateTime=time()+10800;
$dateTime=date("Y-m-d H:i:s",$dateTime);
$isTrue=false;
$limit=10;
$start=0;
$i=10;
do{
    $i=$i+10;
    var_dump($i);
    $isTrue=false;
    /*$query=" SELECT visitingorderid,destination FROM  vtiger_visitingorder  WHERE  modulestatus IN('c_complete','a_normal') AND  isvisitsendremind=0  AND startdate < $dateTime ";*/
    $query=" SELECT v.visitingorderid,v.destination,v.subject, a.accountname as related_to,v.contacts ,v.purpose,u.last_name as extractid,v.accompany,v.startdate,v.enddate,v.outobjective,v.remark FROM  vtiger_visitingorder as v LEFT JOIN vtiger_account as a ON a.accountid = v.related_to  LEFT JOIN vtiger_users as u ON  v.extractid=u.id  WHERE  v.modulestatus IN('c_complete','a_normal') AND  v.isvisitsendremind=0  LIMIT $start,$limit";
    $result = $adb->run_query_allrecords($query);
    global $m_crm_domain_index_url;
    foreach($result as $row){
        //获取陪同人
        $users=$adb->pquery(" SELECT GROUP_CONCAT(u.last_name) as accompany FROM  vtiger_visitsign as v ,vtiger_users as u WHERE  v.visitingorderid =?  AND u.id=v.userid AND  v.signnum=1 AND visitsigntype='陪同人' ", array($row['visitingorderid']));
        $users=$adb->query_result_rowdata($users ,0);
        $row['accompany']=$users['accompany'];
        // 获取拜访单 拜访人
        $sql=" SELECT u.email1,u.last_name FROM vtiger_visitsign as v , vtiger_users as u  WHERE     u.id=v.userid  AND v.visitingorderid =?  AND v.signnum=1  ";
        $users=$adb->pquery($sql,array($row['visitingorderid']));
        if($adb->num_rows($users)>0){
            while ($rowDatas=$adb->fetch_array($users)){
                // 核查email
                if(Vtiger_Record_Model::checkEmail(trim($rowDatas['email1']))){
                    echo $rowDatas['email1'];
                    $dataurl=$m_crm_domain_index_url.'?module=VisitingOrder&action=detail&record='.$row['visitingorderid'];
                    $content='<div class=\"gray\">'.date('Y年m月d日').'</div><div class=\"normal\">与您相关的拜访单需要拜访,目的地为:</div><div class=\"highlight\">'.$row['destination'].'</div>请及时处理';
                    //  企业微信短信 提醒
                    $reuslt=setweixincontracts(array('email' => trim($rowDatas['email1']), 'description'=> $content, 'dataurl' => $dataurl, 'title' => '拜访单拜访提醒', 'flag' => 7));
                    $reuslt=json_decode($reuslt,true);
                    // 发送成功
                    if($reuslt['errcode']==0 && $reuslt['errmsg']=='ok'){
                        $WxSendSuccess=true;
                        // 发送失败再尝试一次
                    }else{
                        setweixincontracts($data);
                    }
                    //  发送邮箱
                    $Subject = '拜访单拜访';
                    $body='与您相关的拜访单需要拜访<br>';
                    $body.='<table style="border-collapse: collapse;border:solid 1px #000;color:#666;font-size:12px;">
                    <tr><td style="border:solid 1px #000;font-size:12px;text-align: right;" nowrap="nowrap">主题</td><td style="border:solid 1px #000;text-align:left;color:#000;font-size:14px;" nowrap="nowrap"><a href="http://192.168.1.3/index.php?module=VisitingOrder&view=Detail&record='.$row['visitingorderid'].'" target="_blank" style="text-decoration:none;">'.$row['subject'].'</a></td></tr>
                    <tr><td style="border:solid 1px #000;font-size:12px;text-align: right;" nowrap="nowrap">客户</td><td style="border:solid 1px #000;text-align:left;color:#000;font-size:14px;" nowrap="nowrap">'.$row['related_to'].'</td></tr>
                    <tr><td style="border:solid 1px #000;font-size:12px;text-align: right;" nowrap="nowrap">目的地</td><td style="border:solid 1px #000;text-align:left;color:#000;font-size:14px;" nowrap="nowrap">'.$row['destination'].'</td></tr>
                    <tr><td style="border:solid 1px #000;font-size:12px;text-align: right;" nowrap="nowrap">联系人</td><td style="border:solid 1px #000;text-align:left;color:#000;font-size:14px;" nowrap="nowrap">'.$row['contacts'].'</td></tr>
                    <tr><td style="border:solid 1px #000;font-size:12px;text-align: right;" nowrap="nowrap">拜访目的</td><td style="border:solid 1px #000;text-align:left;color:#000;font-size:14px;" nowrap="nowrap">'.$row['purpose'].'</td></tr>
                    <tr><td style="border:solid 1px #000;font-size:12px;text-align: right;" nowrap="nowrap">提单人</td><td style="border:solid 1px #000;text-align:left;color:#000;font-size:14px;" nowrap="nowrap">'.$row['extractid'].'</td></tr>
                    <tr><td style="border:solid 1px #000;font-size:12px;text-align: right;" nowrap="nowrap">陪同人</td><td style="border:solid 1px #000;text-align:left;color:#000;font-size:14px;" nowrap="nowrap">'.$row['accompany'].'</td></tr>
                    <tr><td style="border:solid 1px #000;font-size:12px;text-align: right;" nowrap="nowrap">开始日期</td><td style="border:solid 1px #000;text-align:left;color:#000;font-size:14px;" nowrap="nowrap">'.$row['startdate'].'</td></tr>
                    <tr><td style="border:solid 1px #000;font-size:12px;text-align: right;" nowrap="nowrap">结束日期</td><td style="border:solid 1px #000;text-align:left;color:#000;font-size:14px;" nowrap="nowrap">'.$row['enddate'].'</td></tr>
                    <tr><td style="border:solid 1px #000;font-size:12px;text-align: right;" nowrap="nowrap">外出类型</td><td style="border:solid 1px #000text-align:left;color:#000;font-size:14px;" nowrap="nowrap">'.$row['outobjective'].'</td></tr>
                    <tr><td style="border:solid 1px #000;font-size:12px;text-align: right;" nowrap="nowrap">备注</td><td style="border:solid 1px #000;text-align:left;color:#000;font-size:14px;" nowrap="nowrap">'.$row['remark'].'</td></tr>
                    </table>';
                    $address = array(array('mail' =>$rowDatas['email1'], 'name' =>$rowDatas['last_name']));
                    $result=Vtiger_Record_Model::sendMail($Subject, $body, $address,'ERP系统');
                    //如果邮箱发送失败 尝试重新发送一次
                    if($result!='SENT'){
                        Vtiger_Record_Model::sendMail($Subject, $body, $address,'ERP系统');
                    }
                }
            }
            $adb->pquery("UPDATE vtiger_visitingorder SET isvisitsendremind=1 WHERE visitingorderid=? ",array($row['visitingorderid']));
        }else{
            // 没获取到拜访人
            $adb->pquery("UPDATE vtiger_visitingorder SET isvisitsendremind=2 WHERE visitingorderid=? ",array($row['visitingorderid']));
        }
        $isTrue=true;
    }
}while($isTrue);


// 企业微信发送消息提醒
function setweixincontracts($data){
    $userkey='c0b3Ke0Q4c%2BmGXycVaQ%2BUEcbU0ldxTBeeMAgUILM0PK5Q59cEp%2B40n6qUSJiPQ';
    global $m_crm_domian_api_url;
    //$url = $m_crm_domian_api_url;
    $url = "http://m.crm.71360.com/api.php";
    //$url = "http://www.wx2.com/api.php"; 本地测试
    $ch  = curl_init();
    $data['tokenauth']=$userkey;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch,CURLOPT_POST,1);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}


