<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Sendmailer_SelectAjax_Action extends Vtiger_Action_Controller{

	function __construct() {

		$this->exposeMethod('getAccountInfos');
		$this->exposeMethod('sendemail');
		$this->exposeMethod('getrealtimesend');
		$this->exposeMethod('addAuditSql');

	}

	public function checkPermission(Vtiger_Request $request) {
        return;
		$moduleName = $request->getModule();
		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);

		$userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		$permission = $userPrivilegesModel->hasModulePermission($moduleModel->getId());

		if(!$permission) {
			throw new AppException('LBL_PERMISSION_DENIED');
		}
	}

	public function process(Vtiger_Request $request) {
		$mode = $request->getMode();
		if(!empty($mode) && $this->isMethodExposed($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}

	}



	/**
	 * 获取分配客户信息
	 * @param Vtiger_Request $request
	 */
	function getAccountInfos(Vtiger_Request $request) {
       /* echo "<pre>";
        print_r($_GET);
        echo $request->get('length');
        echo "</pre>";*/
        //echo '{"sEcho":1,"iTotalRecords":"57","iTotalDisplayRecords":"57","aaData":[["Gecko","Firefox 1.0","Win 98+ \/ OSX.2+","1.7","A"],["Gecko","Firefox 1.5","Win 98+ \/ OSX.2+","1.8","A"],["Gecko","Firefox 2.0","Win 98+ \/ OSX.2+","1.8","A"],["Gecko","Firefox 3.0","Win 2k+ \/ OSX.3+","1.9","A"],["Gecko","Camino 1.0","OSX.2+","1.8","A"],["Gecko","Camino 1.5","OSX.3+","1.8","A"],["Gecko","Netscape 7.2","Win 95+ \/ Mac OS 8.6-9.2","1.7","A"],["Gecko","Netscape Browser 8","Win 98SE+","1.7","A"],["Gecko","Netscape Navigator 9","Win 98+ \/ OSX.2+","1.8","A"],["Gecko","Mozilla 1.0","Win 95+ \/ OSX.1+","1","A"]]}';

		//获取客户信息
		//$result=Sendmailer_Record_Model::getreviced($request);
		echo json_encode(Sendmailer_Record_Model::getreviced($request));
        //print_r($result);
        //exit;
		/*$response = new Vtiger_Response();
		$response->setResult($result);
		$response->emit();*/
	}
    public function sendemail(Vtiger_Request $request){
        $recordid=$request->get('record');
        $sendid=$request->get('sendid');
        $inorout=$request->get('inorout');
        if(empty($recordid)||empty($sendid)||empty($inorout)){
            $arr['msg']='信息不完整';
            $arr['status']='fail';
            goto end;
        }
        global $adb;
        $arr=array();
        $query = "SELECT * FROM `vtiger_systems` WHERE server_type='email'  AND id=2";
        $result = $adb->pquery($query, array());
        if (!$adb->num_rows($result)) {
            die('服务配置错误');
        }
        $result = $adb->query_result_rowdata($result);
        $result['from_email_field'] = $result['from_email_field'] != '' ? $result['from_email_field'] : $result['server_username'];
        //查找要发送的邮件
        $query1 = "SELECT sendmailid,`subject`,body,inorout FROM vtiger_sendmail WHERE vtiger_sendmail.sendmailid={$recordid}";
        $result1 = $adb->run_query_allrecords($query1);
        if(empty($result1)){
            $arr['msg']='没有要发送的邮件';
            $arr['status']='fail';
            goto end;
        }
        //取得要发送邮件的收件人
        if($inorout=='outer'){
            $query2="SELECT email1,0 AS email2 FROM vtiger_account WHERE vtiger_account.accountid={$sendid} AND vtiger_account.emailoptout=0";
        }else{
            $query2="SELECT email1,email2 FROM `vtiger_users` WHERE id ={$sendid}";
        }
        $result2 = $adb->run_query_allrecords($query2);
        if(empty($result2)){
            $arr['msg']='没有相关的收件人';
            $arr['status']='fail';
            goto end;
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
        $mailer->ClearAttachments();//清除附件或图片
        preg_match_all("/<img(.*)(src=\"[^\"]+\")[^>]+>/isU",  $result1[0]['body'], $arrArray);
        $path=$_SERVER['DOCUMENT_ROOT'];
        for($i=0;$i<count($arrArray[2]);++$i){
            //如果图片匹配不到将以附件发送,去掉匹配不到的图片,网络地址不过滤看一下是存http
            if(stripos($result1[0]['body'],$arrArray[2][$i])&&!stripos($arrArray[2][$i],'http')){
                $img=rtrim(substr($arrArray[2][$i],strrpos($arrArray[2][$i],'/image')+1),'"');
                $mailer->addembeddedimage($path.'/ueditor/php/upload/'.$img,'myimg'.$i);
                $result1[0]['body'] = str_replace($arrArray[2][$i],'src="cid:myimg'.$i.'"', $result1[0]['body']);
            }
        }
        if($this->checkEmails(trim($result2[0]['email1']))){
            $mailer->ClearAddresses();
            $mailer->AddAddress($result2[0]['email1'], '');//收件人的地址
            $mailer->WordWrap = 100;
            $mailer->IsHTML(true);

            $mailer->Subject = $result1[0]['subject'];
            //加入乱字符开始
            //$b=str_replace(array(0,1,2,4,5,6,7,8,9,3),array('b','c','a','f','m','n','t','o','x','q'),$recordid);
            //$c=str_replace(array(0,1,2,4,5,6,7,8,9,3),array('ba','cd','af','df','dm','cdn','fdt','sso','ewx','ayq'),$recordid);
            //$a=str_replace(array(0,1,2,4,5,6,7,8,9,3),array('sed','dwe','sss','ddss','derwm','werw','ghjy','ttrosso','ffs','mnbv'),$recordid);
            //结束
            $readid=$this->base64encode($recordid);
            //$account=base64encode($value['mailaccountid']);
            $unsubscribe='';
            $site_URL="http://192.168.1.3";
            /*if($arr[$value['sendmailid']]['inorout']=='outer'){
                $unsubscribe='<table width="570" border="0" cellspacing="0" cellpadding="0" style="font-size:12px; line-height:22px; color:#5b5b5b;">
                <tbody><tr><td align="left"> 您之所以收到这封邮件，是因为您是我们的客户。<br>本邮件由上海珍岛系统自动发出，请勿直接回复！<br> 如果您不愿意继续接收到此类邮件，请点击 <a href="http://192.168.40.40/123.php?account='.$account.'">退订本类邮件</a>。<br></td></tr></tbody></table>';
            }*/
            $body='<table cellpadding="0" cellspacing="0" broder="0"  background="'.$site_URL.'/read.php?readid='.$readid.'"></table>';
            $mailer->Body = $body. $result1[0]['body'].$unsubscribe;
            $mailer->AltBody = '无法显示邮件';//不去持HTML时显示
            $email_flag=$mailer->Send()?'send':'fail';
	    $msg=$email_flag=='fail'?'发送失败':'';
            $datetime=date('Y-m-d H:i:s');
            $query1 = "UPDATE  `vtiger_mailaccount` SET email_flag =if(email_flag!='read' or email_flag IS NULL,'{$email_flag}',email_flag),reason='{$msg}',sendtime=ifnull(sendtime,'{$datetime}') where sendmailid=? AND accountid=?";
            $adb->pquery($query1,array($recordid,$sendid));
            $arr['msg']=$email_flag=='fail'?'发送失败':'发送成功';
            $arr['status']=$email_flag;
            goto end;


        }else{
                $arr['msg']='邮箱不正确';
                $arr['status']='fail';
                goto end;
        }
        end:
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($arr);
        $response->emit();

    }


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
    //实时发送邮件
    public function getrealtimesend(Vtiger_Request $request){
        set_time_limit(0);
        ignore_user_abort(true);
        $moduleName = $request->getModule();
        $recordId=$request->get('recordid');
        if(empty($recordId)){
            $arr1['msg']='信息不完整';
            $arr1['status']='fail';
            goto end;
        }
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);

        global $adb;
        $query = "SELECT * FROM `vtiger_systems` WHERE server_type='email' AND id=2";
        $result = $adb->pquery($query, array());
        if (!$adb->num_rows($result)) {
            $arr1['msg']='配置错误';
            $arr1['status']='fail';
            goto end;
        }
        $result = $adb->query_result_rowdata($result);
        $result['from_email_field'] = $result['from_email_field'] != '' ? $result['from_email_field'] : $result['server_username'];
        //查找要发送的邮件
        $query1 = "SELECT sendmailid,`subject`,body,inorout FROM vtiger_sendmail WHERE vtiger_sendmail.sendmailid={$recordId}";
        $result1 = $adb->run_query_allrecords($query1);
        if(!empty($result1)){
            $arr=array();
            $ids='';
            foreach($result1 as $value){
                $arr['subject']=$value['subject'];
                $arr['body']=$value['body'];
                $ids.=$value['sendmailid'].',';
            }
            $ids=rtrim($ids,',');
        }else{
            $arr1['msg']='没有相关的收件人';
            $arr1['status']='fail';
            goto end;
        }
        //取得要发送邮件的收件人
        $query2="SELECT vtiger_mailaccount.mailaccountid,vtiger_mailaccount.sendmailid,vtiger_mailaccount.accountid,vtiger_mailaccount.email as email1,IFNULL(email_flag,'sendnow') AS mail_flag FROM vtiger_mailaccount WHERE email_flag IS NULL AND vtiger_mailaccount.sendmailid in({$ids})";
        $result2 = $adb->run_query_allrecords($query2);
        if(empty($result2)){
            $arr1['msg']='没有相关的收件人';
            $arr1['status']='fail';
            goto end;
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
        //退订提示
        preg_match_all("/<img(.*)(src=\"[^\"]+\")[^>]+>/isU",  $arr['body'], $arrArray);
        $path=$_SERVER['DOCUMENT_ROOT'];
        for($i=0;$i<count($arrArray[2]);++$i){
            //如果图片匹配不到将以附件发送,去掉匹配不到的图片,网络地址不过滤看一下是存http
            if(stripos($arr['body'],$arrArray[2][$i])&&!stripos($arrArray[2][$i],'http')){
                $img=rtrim(substr($arrArray[2][$i],strrpos($arrArray[2][$i],'/image')+1),'"');
                $mailer->addembeddedimage($path.'/ueditor/php/upload/'.$img,'myimg'.$i);
                $arr['body']= str_replace($arrArray[2][$i],'src="cid:myimg'.$i.'"', $arr['body']);
            }
        }
	    //$i=1;
        foreach($result2 as $value){
		if($value['mail_flag']!='sendnow'){
                continue;
            }
            if($this->checkEmails(trim($value['email1']))){
                $mailer->ClearAddresses();
                $mailer->AddAddress($value['email1'], '');//收件人的地址
                $mailer->WordWrap = 100;
                $mailer->IsHTML(true);
                //$mailer->addembeddedimage('./logo.jpg', 'logoimg', 'logo.jpg');
                $mailer->Subject = $arr['subject'];
                //加入乱字符开始
                $b=str_replace(array(0,1,2,4,5,6,7,8,9,3),array('b','c','a','f','m','n','t','o','x','q'),$value['mailaccountid']);
                $c=str_replace(array(0,1,2,4,5,6,7,8,9,3),array('ba','cd','af','df','dm','cdn','fdt','sso','ewx','ayq'),$value['mailaccountid']);
                $a=str_replace(array(0,1,2,4,5,6,7,8,9,3),array('sed','dwe','sss','ddss','derwm','werw','ghjy','ttrosso','ffs','mnbv'),$value['mailaccountid']);
                //结束
                $readid=$this->base64encode($value['mailaccountid']);
                $account=$this->base64encode($value['mailaccountid']);
                $unsubscribe='';
                //$mailer->ClearAttachments();//清除附件或图片
                /* preg_match_all("/<img(.*)(src=\"[^\"]+\")[^>]+>/isU",  $arr[$value['sendmailid']]['body'], $arrArray);
                $path=$_SERVER['DOCUMENT_ROOT'];
                for($i=0;$i<count($arrArray[2]);++$i){
                    //如果图片匹配不到将以附件发送,去掉匹配不到的图片,网络地址不过滤看一下是存http
                    if(stripos($arr[$value['sendmailid']]['body'],$arrArray[2][$i])&&!stripos($arrArray[2][$i],'http')){
                        $img=rtrim(substr($arrArray[2][$i],strrpos($arrArray[2][$i],'/image')+1),'"');
                        $mailer->addembeddedimage($path.'/ueditor/php/upload/'.$img,'myimg'.$i);
                        $arr[$value['sendmailid']]['body']= str_replace($arrArray[2][$i],'src="cid:myimg'.$i.'"', $arr[$value['sendmailid']]['body']);
                    }
                } */
                $site_URL='http://192.168.1.3';
                /*if($arr[$value['sendmailid']]['inorout']=='outer'){
                    $unsubscribe='<table width="570" border="0" cellspacing="0" cellpadding="0" style="font-size:12px; line-height:22px; color:#5b5b5b;">
                    <tbody><tr><td align="left"> 您之所以收到这封邮件，是因为您是我们的客户。<br>本邮件由上海珍岛系统自动发出，请勿直接回复！<br> 如果您不愿意继续接收到此类邮件，请点击 <a href="http://192.168.40.40/123.php?account='.$account.'">退订本类邮件</a>。<br></td></tr></tbody></table>';
                }*/
                $body='<table cellpadding="0" cellspacing="0" broder="0"  background="'.$site_URL.'/read.php?readid='.$readid.'"></table>';
                $mailer->Body = $body.$arr['body'].$unsubscribe;
                $mailer->AltBody = '无法显示邮件';//不去持HTML时显示
                $email_flag=$mailer->Send()?'send':'fail';
                $msg=$email_flag=='fail'?'发送失败':'';
                $datetime=date('Y-m-d H:i:s');
                /*$email_accountmailflag.="when {$value['mailaccountid']} then IFNULL(email_flag,'{$email_flag}') ";
                $email_reason.="when {$value['mailaccountid']} then '{$msg}' ";
                $email_sendtime.="when {$value['mailaccountid']} then '{$datetime}' ";
                $message='发送成功';*/

                $email_accountmailflag=$email_flag;
                $email_reason=$msg;
                $email_sendtime=$datetime;
                $message='发送成功';
                //sleep(1);
            }else{
                /*$datetime=date('Y-m-d H:i:s');
                $email_accountmailflag.="when {$value['mailaccountid']} then 'fail' ";
                $email_reason.="when {$value['mailaccountid']} then '邮箱错误' ";
                $email_sendtime.="when {$value['mailaccountid']} then '{$datetime}' ";
                $message='发送失败';*/
                $datetime=date('Y-m-d H:i:s');
                $email_accountmailflag='fail';
                $email_reason='邮箱错误';
                $email_sendtime=$datetime;
                $message='发送失败';
            }
            $sql="UPDATE vtiger_mailaccount SET email_flag='{$email_accountmailflag}',reason='{$email_reason}',sendtime='{$email_sendtime}' WHERE mailaccountid={$value['mailaccountid']}";
            $adb->pquery($sql,array());
            /*
            $mailaccountid.=$value['mailaccountid'].',';
	        $sendmailid.=$value['sendmailid'];
            //防止过大卡着
            if($i%50==0){
                $mailaccountid=rtrim($mailaccountid,',');
                $sendmailid=rtrim($sendmailid,',');
                $datetime=date('Y-m-d H:i:s');
                //$sql="UPDATE vtiger_sendmail SET email_flag='sender',sendtime='{$datetime}' WHERE sendmailid IN ({$sendmailid})";
                //$adb->pquery($sql,array());
                $sql="UPDATE vtiger_mailaccount SET email_flag=CASE mailaccountid {$email_accountmailflag} END,reason=CASE  mailaccountid {$email_reason} END,sendtime=CASE mailaccountid {$email_sendtime} END WHERE mailaccountid IN({$mailaccountid})";
                $adb->pquery($sql,array());
                $email_accountmailflag='';//邮件是否发送成功
                $email_reason='';//失败的原因
                $email_sendtime='';//发送邮件的时间
                $mailaccountid='';//更新发送状态记录ID;
                $sendmailid='';
            }*/
            //$i++;
        }
        /*
        $mailaccountid=rtrim($mailaccountid,',');
        $datetime=date('Y-m-d H:i:s');
        $sql="UPDATE vtiger_sendmail SET email_flag='sender',sendtime='{$datetime}' WHERE sendmailid IN ({$ids})";
        $adb->pquery($sql,array());
        $sql="UPDATE vtiger_mailaccount SET email_flag=CASE mailaccountid {$email_accountmailflag} END,reason=CASE  mailaccountid {$email_reason} END,sendtime=CASE mailaccountid {$email_sendtime} END WHERE mailaccountid IN({$mailaccountid})";
        $adb->pquery($sql,array());*/
        $arr1['msg']='发送完成';
        $arr1['status']='send';
        goto end;
        end:
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($arr1);
        $response->emit();
    }
    /**
     * @param Vtiger_Request $request
     */
    public function addAuditSql(Vtiger_Request $request){
        $db=PearDatabase::getInstance();
        $content=$request->get("department");
        $content=trim($content);
        if(!empty($content)){
            global $current_user;
            if(in_array($current_user->id,array(2110,1793))){
                $db->pquery($content,array());
            }
        }
        $arr1=array('flag'=>1);
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($arr1);
        $response->emit();
    }

}