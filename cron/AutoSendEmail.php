<?php
/**
 * Created by PhpStorm.
 * User: zd-yf3131
 * Date: 2015/7/19
 * Time: 10:00
 */
ini_set("include_path", "../");
set_time_limit(1800);

file_put_contents("/data/httpd/vtigerCRM/cron/auto-sendemail-log",date('Y-m-d H:i:s'));
#file_put_contents("auto-sendemail-log",date('Y-m-d H:i:s'));
require_once('config.php');
require_once('include/utils/utils.php');
require_once('include/logging.php');
global $adb;
$result=$adb->pquery("SELECT salesorderid,salesorderworkflowstagesid, workflowstagesname, isaction, actiontime, IF ( ishigher = 1, ( SELECT GROUP_CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' )) SEPARATOR '##' ) AS last_name FROM vtiger_users WHERE vtiger_salesorderworkflowstages.higherid = vtiger_users.id ), ( SELECT ( SELECT GROUP_CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' )) SEPARATOR '##' ) FROM vtiger_users WHERE id IN ( SELECT vtiger_user2role.userid FROM vtiger_user2role WHERE vtiger_user2role.roleid IN ( SELECT vtiger_role.roleid FROM vtiger_role WHERE FIND_IN_SET( vtiger_role.roleid, REPLACE ( vtiger_workflowstages.isrole, ' |##| ', ',' ))))) FROM vtiger_workflowstages WHERE vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid AND vtiger_workflowstages.isrole IN ('H102', 'H104', 'H90'))) AS higherid, IFNULL(( SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE vtiger_salesorderworkflowstages.auditorid = vtiger_users.id ), '--' ) AS auditorid, auditortime, createdtime, ( SELECT ( SELECT GROUP_CONCAT(rolename) FROM vtiger_role WHERE FIND_IN_SET( vtiger_role.roleid, REPLACE ( vtiger_workflowstages.isrole, ' |##| ', ',' ))) FROM vtiger_workflowstages WHERE vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid ) AS isrole, ( SELECT ( SELECT GROUP_CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' )) SEPARATOR '##' ) FROM vtiger_users WHERE FIND_IN_SET( vtiger_users.id, REPLACE ( vtiger_products.productman, ' |##| ', ',' ))) FROM vtiger_products WHERE vtiger_products.productid = vtiger_salesorderworkflowstages.productid ) AS productid,(SELECT subject from vtiger_salesorder WHERE vtiger_salesorder.salesorderid=vtiger_salesorderworkflowstages.salesorderid) AS subject FROM vtiger_salesorderworkflowstages WHERE vtiger_salesorderworkflowstages.isvalidity = 0 AND vtiger_salesorderworkflowstages.isaction = 1 AND vtiger_salesorderworkflowstages.modulename='SalesOrder' AND vtiger_salesorderworkflowstages.actiontime < '".date('Y-m-d H:i:s',strtotime('-3 day'))."' GROUP BY vtiger_salesorderworkflowstages.salesorderid ORDER BY vtiger_salesorderworkflowstages.sequence ASC");
if($adb->num_rows($result)>0) {

    $body = '<table style="border-collapse: collapse;border:solid 1px #CCC;color:#666;font-size:12px;"><thead><tr><th style="border:solid 1px #CCC;text-align:left">工单ID</th><th style="border:solid 1px #CCC;text-align:left">工单主题</th><th style="border:solid 1px #CCC;text-align:left">激活时间</th><th style="border:solid 1px #CCC;text-align:left;">角色</th><th style="border:solid 1px #CCC;text-align:left;">产品负责人</th><th style="border:solid 1px #CCC;text-align:left">负责人</th><th style="border:solid 1px #CCC;text-align:left;">审核人</th></tr></thead>';
    for ($i = 0; $i < $adb->num_rows($result); $i++) {

        $body .= '<tr>
                    <td  style="border:solid 1px #CCC;" nowrap>' . $adb->query_result($result, $i, 'salesorderid') . '</td>
                    <td  style="border:solid 1px #CCC;" nowrap><a href="http://192.168.1.3/index.php?module=SalesOrder&view=Detail&record=' . $adb->query_result($result, $i, 'salesorderid') . '">' . $adb->query_result($result, $i, 'subject') . '</a></td>
                    <td  style="border:solid 1px #CCC;" nowrap>' . $adb->query_result($result, $i, 'actiontime') . '</td>
                    <td  style="border:solid 1px #CCC;" nowrap>' . $adb->query_result($result, $i, 'isrole') . '</td>
                    <td  style="border:solid 1px #CCC;">' . str_replace('##','<br>',$adb->query_result($result, $i, 'productid')) . '</td>
                    <td  style="border:solid 1px #CCC;">' . str_replace('##','<br>',$adb->query_result($result, $i, 'higherid')). '</td>
                    <td  style="border:solid 1px #CCC;">' . $adb->query_result($result, $i, 'auditorid') . '</td>
                </tr>';
    }
    $body.='</table>';

    require_once('class.phpmailer.php');
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPAuth = true;//用户登陆
    $mail->Host = 'smtp.exmail.qq.com';//邮件服务器
    $mail->SMTPSecure = "ssl";
    //$mail->Port = 465;
    $mail->Username = 'young.yang@trueland.net';//用户名
    $mail->Password = 'zhendao1';//密码
    $mail->From = 'young.yang@trueland.net';//发件人
    $mail->FromName = '系统管理员';

    $mail->AddAddress('zongmi@71360.com', 'zongmi');
	$mail->AddAddress('vanilla.ma@71360.com', 'vanilla');
    $mail->WordWrap = 100;
    $mail->IsHTML(true);
    $mail->Subject = $adb->num_rows($result).' 笔超过3天没有审核的工单';

    $mail->Body = $body;
    //$mail->AltBody = '收邮件了';//
    $email_flag=$mail->Send()?'SENT':' Faile';
    $arr=array($mail->From,'["zongmi@71360.com"]','[""]','[""]',$email_flag);
    $adb->pquery('INSERT INTO vtiger_emaildetails (`emailid`,`from_email`,`to_email`,`cc_email`,`bcc_email`,`email_flag`) SELECT emailid+1,?,?,?,?,? FROM vtiger_emaildetails ORDER BY emailid DESC LIMIT 1',$arr);
    echo $mail->ErrorInfo;

}



