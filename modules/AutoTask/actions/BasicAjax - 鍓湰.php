<?php

/*
 * +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ***********************************************************************************
 */

/*包含自动触发任务按钮*/
include_once('include/Autotriggerevene.php');
class AutoTask_BasicAjax_Action extends Vtiger_Action_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->exposeMethod('getAudittpl');
        $this->exposeMethod('submitaudit');
    }
    //发送邮件
    /*
     * @author wangbin 2015年8月17日
     * @param  string $subject 邮件主题
     * @param  text/html $mailcontext 邮件内容
     * @param  array()    $user_receive_email 邮件收件人
     * @param  array $user_copy_email 邮件发送人
     * @param  array $file；邮件附件内容；
     */
    public function sendmail($subject,$mailcontext,$user_receive_email,$user_copy_email,$custom_receArr,$custom_copyArr,$file){
       global $adb;

        $mailserverresult=$adb->pquery("select * from vtiger_systems where server_type='email'", array());
        $mail_server = $adb->query_result($mailserverresult,0,'server');
        $mail_server_username = $adb->query_result($mailserverresult,0,'server_username');
        $mail_server_password = $adb->query_result($mailserverresult,0,'server_password');
        $smtp_auth = $adb->query_result($mailserverresult,0,'smtp_auth');

        require_once('cron/class.phpmailer.php');
        $mail = new PHPMailer();
        $mail->CharSet = "utf-8"; //设置采用utf-8中文编码(内容不会乱码)
        $mail->IsSMTP();                    //设置采用SMTP方式发送邮件
<<<<<<< .mine
        $mail->Host = "smtp.exmail.qq.com";    //设置邮件服务器的地址(若为163邮箱，则是smtp.163.com)
=======
        $mail->Host = $mail_server;    //设置邮件服务器的地址(若为163邮箱，则是smtp.163.com) "smtp.qq.com"
>>>>>>> .r3983
        $mail->SMTPSecure = 'ssl';
<<<<<<< .mine
        $mail->From = "young.yang@trueland.net"; //设置发件人的邮箱地址
        $mail->FromName = "young.yang@trueland.net";           //设置发件人的姓名(可随意)
        $mail->SMTPAuth = true;                   //设置SMTP是否需要密码验证，true表示需要
        $mail->Username="young.yang@trueland.net";
        $mail->Password = "zhendao1";
=======
        $mail->From = $mail_server_username; //设置发件人的邮箱地址
        $mail->FromName = $mail_server_username;           //设置发件人的姓名(可随意)
        $mail->SMTPAuth = $smtp_auth;                   //设置SMTP是否需要密码验证，true表示需要
        $mail->Username=$mail_server_username;
        $mail->Password = $mail_server_password;
>>>>>>> .r3983
        $mail->Subject = $subject;    //主题
        $mail->AltBody = "text/html";                                // optional, comment out and test
        $mail->Body = $mailcontext;      //内容
        $mail->IsHTML(true);
        //$mail->WordWrap = 50;                                 //设置每行的字符数
        foreach($user_receive_email as $k=>$v) {
            if(!empty($v['1'])){
            $mail->AddAddress($v['1'], $v['0']);  //设置收件的地址(to可随意)
        }
        }
        foreach($custom_receArr as $v){
            if(!empty($v)){
                $mail->AddAddress($v,"");
            }
        }

        foreach ($user_copy_email as $k=>$v){
            if(!empty($v['1'])){
                $mail->AddCC($v['1'],$v['0']);
            }
        }

        foreach($custom_copyArr as $v){
            if(!empty($v)){
                $mail->AddCC($v,"");
            }
        }
      //  $mail->AddReplyTo("384744571@qq.com","from");     //设置回复的收件人的地址(from可随意)
        if(!empty($file)){
           foreach($file as $v){
               $mail->addattachment($v['1'],$v['0']);
           }
            //$mail->addattachment('E:\CRM.update\storage\2015\September\week4\460_aG90a2V5cy50eHQ=', '3.pdf');
        }
        if($mail->Send()){
            $return  = "true";
        }else{
            $return = $mail->ErrorInfo;
        };
        return $return;
    }
    function checkPermission(Vtiger_Request $request)
    {
        return;
    }

    //收件人抄送人
    function copyreceive($mailcopyby,$copygroups,$accountli){
        global $adb;
        //管理员没有部门id; 可能会报错需要解决
        $accountid = $accountli['accountid']; //客户id
        $accountsmownerid = $accountli['smownerid'];//客户负责人
        $accountdepartmentid = $accountli['departmentid'];//客户负责人的部门id
        if(empty($accountdepartmentid)){
            return array();
        }
        $accountdepartmentdetail = Settings_Departments_Record_Model::getInstanceById("$accountdepartmentid")->get('parentdepartment');
        $accountparentdepartment = explode("::",$accountdepartmentdetail); //客户负责人的父级部门;
        krsort($accountparentdepartment);

        if($mailcopyby =='0'){
            $copyid = $copygroups;
        }elseif($mailcopyby =='1'){
            $groupsis = array();
            foreach ($copygroups as $val){
                $groupsis[]= (int)substr($val, 7);
            }
            $groupssql = "SELECT * FROM `vtiger_users2group` WHERE groupid IN (".generateQuestionMarks($groupsis).")";
            $receivedresult = $adb->pquery($groupssql,$groupsis);
            for ($i=0;$i<$adb->num_rows($receivedresult);$i++){
				$aa=$adb->fetchByAssoc($receivedresult);
                $copyid[]=$aa['userid'];
            }
        }elseif($mailcopyby =='2'){
            // var_dump($copygroups);
            $limitedrolea = array("H79","H101","H81","H85","H80"); //需要区分部门的角色; 总监:助理
            foreach($copygroups as $val2){
              //  echo $val2."<br>";
                if( in_array($val2,$limitedrolea)){
                    $roledepartArrli = array();
                    $roledepartArraysql = "SELECT a.userid, a.roleid, b.rolename, c.departmentid, d.parentdepartment,d.departmentname FROM `vtiger_user2role` a LEFT JOIN vtiger_role b ON a.roleid = b.roleid LEFT JOIN vtiger_user2department c ON a.userid = c.userid LEFT JOIN vtiger_departments d ON c.departmentid = d.departmentid WHERE a.roleid = ?";
                    $roledepartArrayresult = $adb->pquery($roledepartArraysql,array($val2)); //查找角色的部门集合数组
                    for($i2=0;$i2<$adb->num_rows($roledepartArrayresult);$i2++){
                        $temp =  $adb->fetchByAssoc($roledepartArrayresult);
                        $t[$temp['departmentid']]= $temp['userid'];
                    }
                    foreach($accountparentdepartment as $d){
                        if(!empty($t[$d])){
                            $copyid[] =  $t[$d];
                            break;
                        }
                    }
                }elseif($val2 == "H24"){  //客服  需要注意可能没有分配客服的情况；
                    $rolesql = "SELECT serviceid FROM `vtiger_servicecomments` WHERE assigntype = ? AND related_to = ? LIMIT 1";
                    $receivedresult = $adb->pquery($rolesql,array("accountby",$accountid));
					$bb=$adb->fetchByAssoc($receivedresult);
                    $copyid[] = $bb['serviceid'];
                  //  die('客服');
                }elseif($val2 == "H83"){//客服总监  直接选择李琴
                    $copyid[] = "1121";
                }else{
                    $rolesql = "SELECT * FROM `vtiger_user2role` WHERE roleid =?";//IN (".generateQuestionMarks($copygroups).")";
                    $receivedresult = $adb->pquery($rolesql,array($val2));
                    for ($i=0;$i<$adb->num_rows($receivedresult);$i++){
						$cc=$adb->fetchByAssoc($receivedresult);
                        $copyid[] = $cc['userid'];
                    }
                }
                //var_dump($copyid);
            }
                $copyid = array_unique($copyid);
        }elseif($mailcopyby =='3'){
            $copyid =array();
            /*SELECT a.userid, a.roleid, b.rolename FROM `vtiger_user2role` a LEFT JOIN vtiger_role b ON a.roleid = b.roleid;*/
        }
        return $copyid;
    }
    
    // 弹出审核页面
    function getAudittpl(Vtiger_Request $request)
    {
        global $current_user;
        global $adb;
        $permissiontaskid = AutoTask_Detail_View::usertaskid(); // 获取当前用户所能查看的所有任务节点
        $currenttaskid = $request->get('autoworkflowtaskid'); // 当前任务节点id
        $currentflowid = $request->get('source_record'); // 当前任务流id;
        $crmid = $request->get('crmid'); // 关联模块主键id;
        $remarkcommen = $request->get('remarkcommen');

        $currenttaskdetail = $this->gettaskinstance($currentflowid, $currenttaskid, $crmid);//当前任务节点所有内容；

        if($remarkcommen){
            $currenttaskdetail['taskremark'] = $remarkcommen;
        }
        global $adb;
        $iseditask = false;

        $houtaitaskdetail = $this->gethoutaitaskinstance($currenttaskid);//后台任务任务节点所有相关数据，包括邮件模版，自定义函数;
        $houtaitaskmailevent = $houtaitaskdetail['templ']['0']['jsonarray'];  //当前节点后台邮件相关
        $ifhasmial = $houtaitaskmailevent->ismail;               //是否卖身
        $mailtempli = array();                                   //邮件模板内容
        $mailtemplatesid = $houtaitaskmailevent->templates;
        if($mailtemplatesid){
            $selmailtempsql = "SELECT * FROM vtiger_emailtemplates WHERE templateid = ?";
            $mailtempresult = $adb->pquery($selmailtempsql,array($mailtemplatesid));
            if ($adb->num_rows($mailtempresult)>0){
                $mailtempli = $adb->fetchByAssoc($mailtempresult);
            }
        }
        //var_dump($mailtempli);die;
        //******************************************************************************
        //var_dump($houtaitaskmailevent);

        //获取当前客户的联系方式，客户的客服，以及客户的负责人；
        $accountid = $currenttaskdetail['accountid'];
       if($accountid) {
           $accountRelatedsql = "SELECT $accountid AS accountid, ( SELECT smownerid FROM vtiger_crmentity WHERE crmid = ?)AS userid , departmentid FROM vtiger_user2department WHERE userid = ( SELECT smownerid FROM vtiger_crmentity WHERE crmid = ? )";
           $accountresult = $adb->pquery($accountRelatedsql, array($accountid, $accountid));
           $accountli = $adb->fetchByAssoc($accountresult);//array(客户id，客户负责人，客户负责人部门);
       }

        //查找抄送人id集合;
        $mailcopyby = $houtaitaskmailevent->mailcopyby;//人员 or 组别 or 角色
        $houtaicopyid = $houtaitaskmailevent->copyids; //后台抄送人;
        $cusomdopyids =(empty($houtaitaskmailevent->cusomdopyids))?(array()):($houtaitaskmailevent->cusomreceiveids); //自定义抄送人
        $copyids =  $this->copyreceive($mailcopyby,$houtaicopyid,$accountli);//抄送人的id;
        $copyid = array_merge($copyids,$cusomdopyids);


        //查找收件人id集合;
        $mailreiveby = $houtaitaskmailevent->mailreiveby;//人员 or 组别 or 角色
        $houtaireceveid = $houtaitaskmailevent->receiveids;//后台的收件人原始数据
        $cusomreceiveids = (empty($houtaitaskmailevent->cusomreceiveids)) ? (array()) : ($houtaitaskmailevent->cusomreceiveids) ;//自定义收件人
        $receiveids = $this->copyreceive($mailreiveby, $houtaireceveid,$accountli);//收件人id
        $receiveid  = array_merge($cusomreceiveids,$receiveids);

        //******************************************************************************
         
        if ($current_user->is_admin !== "on") {            //判断当前操作人是否有权限审核
            if (in_array($currenttaskid, $permissiontaskid)) {
                $iseditask = true;
            }
        } else {
            $iseditask = true;
        }


        $viewer = new Vtiger_Viewer();
        
        if($mailcopyby == 3){
            $mailaccount_copy = true;
            $recordModel = Vtiger_Record_Model::getInstanceById($accountid, 'Accounts');
            $entity=$recordModel->entity->column_fields;
            $user_copy_email['acc##'.$entity['record_id']] = array($entity['linkname'],$entity['email1']);
        }else{
            foreach($copyid as $v){
                $userModel = Vtiger_Record_Model::getInstanceById($v, 'Users');
                $userdetail=$userModel->entity->column_fields;
                $user_copy_email[$userdetail[record_id]] = array($userdetail['last_name'],$userdetail['email1']);
            }
        }
        if($mailreiveby == 3){
            $maiaccount_receive = true;
            $recordModel = Vtiger_Record_Model::getInstanceById($accountid, 'Accounts');
            $entity=$recordModel->entity->column_fields;
            $user_receive_email['acc##'.$entity['record_id']] = array($entity['linkname'],$entity['email1']);
        }else{
            foreach($receiveid as $v){
                $userModel = Vtiger_Record_Model::getInstanceById($v, 'Users');
                $userdetail=$userModel->entity->column_fields;
                $user_receive_email[$userdetail[record_id]] = array($userdetail['last_name'],$userdetail['email1']);
            }
        }
        $user_mail = $this->check_user_email($user_receive_email,$user_copy_email); //判断默认收件人以及抄送人的邮箱是否为空

        $emptyemail = "以下人员没有邮箱请自行添加: ";
        foreach($user_mail['receive'] as $v){
            $emptyemail.="<".$v.">";
        }
        foreach($user_mail['copy'] as $v){
            $emptyemail.="<".$v.">";
        }

        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());//所有人员信息;
        $viewer->assign("COPYID",$copyid);
        $viewer->assign("RECEID",$receiveid);
        $viewer->assign("ISEDIT", $iseditask);
        $viewer->assign("DATA", $currenttaskdetail);
        $viewer->assign("CRMID", $crmid);
        $viewer->assign("MAILTEMPLI",$mailtempli);
        $viewer->assign("ISMAIL",$ifhasmial);
        $viewer->assign("BASEDATA",array($currenttaskid,$currentflowid,$crmid));
        $viewer->assign("MAIL_ACC_COPY",$mailaccount_copy);
        $viewer->assign("MAIL_ACC_RECEIVE",$maiaccount_receive);
        $viewer->assign("ACCOUNTID",$accountid);
        $viewer->assign("USER_MAIL",$user_mail);
        $viewer->assign(EMPTYEMAIL,$emptyemail);
        $viewer->view('showaudit.tpl', 'AutoTask');
    }

    /*
     * @author wangibn 2015年7月24日 根据工作流id和 任务节点id 返回该条任务节点消息信息;
     * @param int $flowid 工作流id
     * @param int $flowtaskid 任务节点id
     * @return array()
     */
    function gettaskinstance($flowid, $flowtaskid, $crmid)
    {
        //print_r(array($flowid, $flowtaskid, $crmid));die;
        global $adb;
        $sql = "SELECT * FROM `vtiger_autoworkflowtaskentitys` WHERE autoworkflowid = ? AND autoworkflowtaskid = ? AND crmid = ?";
        $taskresult = $adb->pquery($sql, array(
            $flowid,
            $flowtaskid,
            $crmid
        ));
        $currenttaskdetail = $adb->fetchByAssoc($taskresult, 0);
        return $currenttaskdetail;
    }

//通过$autoworkflowentityid 查找当前前台的任务包id:
    function gettaskinstanceby($autoworkflowentityid,$taskid){
        global $adb;
        $sql = "SELECT * FROM `vtiger_autoworkflowtaskentitys` WHERE autoworkflowentityid = ? AND autoworkflowtaskid=? ";
        $taskresult = $adb->pquery($sql, array($autoworkflowentityid,$taskid));
        $currenttaskdetail = $adb->fetchByAssoc($taskresult, 0);
        return $currenttaskdetail;
    }
    /**
     * 根据后台taskid,查找后台任务详细信息;
     * @param unknown $flowtaskid
     * @return Ambigous <NULL, multitype:, unknown>
     */
    function gethoutaitaskinstance($flowtaskid)
    {
        global $adb;
        $sql1 = "SELECT * FROM `vtiger_autoworkflowtasks`WHERE autoworkflowtaskid = ?";
        $houtaitaskresult = $adb->pquery($sql1, array(
            $flowtaskid
        ));
        $houtaitaskdetail = $adb->fetchByAssoc($houtaitaskresult, 0);
        
        if ($houtaitaskdetail['autodetails']) {
            $houtaitaskdetail['autodetails'] = explode("##", $houtaitaskdetail['autodetails']);
        }
        // var_dump($houtaitaskdetail);die;
        $sql2 = "SELECT * FROM `vtiger_autoworkflowtasks_tasktemplets` WHERE autoworkflowtaskid = ?";
        $houtaitempletsresults = $adb->pquery($sql2, array(
            $flowtaskid
        ));
        $templets = array();
        if ($adb->num_rows($houtaitempletsresults) > 0) {
            for ($i = 0; $i < $adb->num_rows($houtaitempletsresults); $i ++) {
                $temp = $adb->fetchByAssoc($houtaitempletsresults);
                // var_dump($temp);die;
                $json = $temp['contentjson'];
                $temp['jsonarray'] = json_decode(str_replace('&quot;', '"', $json));
                $templets[$temp['tasktypeid']] = $temp;
            }
        }
        // var_dump($templets);
        $houtaitaskdetail["templ"] = $templets;
        // var_dump($houtaitaskdetail);
        return $houtaitaskdetail;
    }


    //对当前邮箱联系人的验证
    function check_user_email($user_receive_email,$user_copy_email){
        foreach($user_receive_email as $k=>$v){
            if(empty($v[1])){
                if(strstr("$k","acc")){
                    $empty_mail['receive'][]="客户首要联系人".$v[0];
                }else{
                    $empty_mail['receive'][]=$v[0];
                }
            }
        }
        foreach($user_copy_email as $k=>$v){
            if(empty($v[1])){
                if(strstr("$k","acc")){
                    $empty_mail['copy'][]="客户首要联系人".$v[0];
                }else{
                    $empty_mail['copy'][] = $v[0];
                }
            }
        }
        if(empty($empty_mail['receive']) && empty($empty_mail['copy'])){
            $type_email = 0;
        }else{
            if(empty($empty_mail['receive'])){
                $type_email = 2;
            }elseif(empty($empty_mail['copy'])){
                $type_email = 1;
            }else{
                $type_email = 3;
            }
        }
        $empty_mail['type'][] = $type_email;
        return $empty_mail;
    }

    // 保存相关审核的相关操作
    function submitaudit(Vtiger_Request $request)
    {
        global $adb;
        global $current_user;
        $userid = $current_user->id; //当前登录用户
        $crmid = $request->get('crmid'); //当前关联模块id;
        $taskremark = $request->get('taskremark'); // 备注内容
        //$autoworkflowid = $request->get('autoflowid'); // 工作流id;
        $autoworkflowid = $request->get('flowid'); // 工作流id;
        $autoworkflowtaskid = $request->get('autoflowtaskid'); // 任务节点id;

       /* $houtaitaskdetail = $this->gethoutaitaskinstance($autoworkflowtaskid);//后台任务任务节点所有相关数据，包括邮件模版，自定义函数;
        $houtaitaskmailevent = $houtaitaskdetail['templ']['0']['jsonarray'];  //当前节点后台邮件相关
        $ifhasmial = $houtaitaskmailevent->ismail;               //是否邮件
        $mailtempli = array();                                   //邮件模板内容
        $mailtemplatesid = $houtaitaskmailevent->templates;*/

        $currenttask = $this->gettaskinstance($autoworkflowid, $autoworkflowtaskid, $crmid); // 当前记录详细
        $accountid = $currenttask['accountid'];//当前节点客户id
        $module = $currenttask['modulename'];//所关联的模块
        $moduleid = $currenttask['crmid'];//所关联的模块的主键id
        $currenttaskprimaryid = $currenttask['autoworkflowtaskentityid'];
        $date_var = date("Y-m-d H:i:s");
        $date = $adb->formatDate($date_var, true); //时间
        // var_dump($currenttask);

        //准备发送邮件;
        $postmaildata = $request->get('mail');
        $issendit = $postmaildata['issendit'];//是否发送邮件
        $subject =  $postmaildata['subject'];//邮件主题
        $receiveids = $postmaildata['receiveids'];//邮件收件人
        $copyids = $postmaildata['copyids'];//邮件抄送人
        $custom_rece = $postmaildata['custom_rece'];//自定义收件人
        $custom_copy = $postmaildata['custom_copy'];//自定义抄送人
        $mailcontext = $postmaildata['mailcontext'];//邮件内容


        //对当前内容处理,插入邮件历史表中去;
        $receiveidstring =   implode('##',$receiveids);
        $copyidstring = implode('##',$copyids);

        if(!empty($custom_rece)){
            $custom_receArr = explode("##",$custom_rece);
        }
        if(!empty($custom_copy)){
            $custom_copyArr = explode("##",$custom_copy);
        }
        //userid->email 这边需要一个人员信息跟邮件的一个对应表;
        foreach($receiveids as $v){
            if(strstr("$v","acc")){
                $recordModel = Vtiger_Record_Model::getInstanceById($accountid, 'Accounts');
                $entity=$recordModel->entity->column_fields;
                $user_receive_email['acc##'.$entity['record_id']] = array($entity['linkname'],$entity['email1']);
            }else{
                $userModel = Vtiger_Record_Model::getInstanceById($v, 'Users');
                $userdetail=$userModel->entity->column_fields;
                $user_receive_email[$userdetail[record_id]] = array($userdetail['last_name'],$userdetail['email1']);
            }
        }
        foreach($copyids as $v){
            if(strstr("$v","acc")){
                $recordModel = Vtiger_Record_Model::getInstanceById($accountid, 'Accounts');
                $entity=$recordModel->entity->column_fields;
                $user_copy_email['acc##'.$entity['record_id']] = array($entity['linkname'],$entity['email1']);
            }else{
                $userModel = Vtiger_Record_Model::getInstanceById($v, 'Users');
                $userdetail=$userModel->entity->column_fields;
                $user_copy_email[$userdetail[record_id]] = array($userdetail['last_name'],$userdetail['email1']);
            }
        }

        //end

        //测试邮箱为空的情况
        if($request->get('test')){
            $empty_mail = $this->check_user_email($user_receive_email,$user_copy_email);
            $response = new Vtiger_Response();
            $response->setEmitType(Vtiger_Response::$EMIT_JSON);
            $response->setResult($empty_mail);
            $response->emit();
            die;
        }


        if (($currenttask['isaction'] !== "2" && $currenttask['process_from'] == "") || $currenttask['isaction'] == "1") { // 只有当前节点是激活状态或者是第一个没有关闭的节点才能审核；
            $saveremarsksql = "UPDATE vtiger_autoworkflowtaskentitys SET pauseaudit = ?, taskremark = ?, auditedtime = ?,auditorid=?,isaction=? WHERE autoworkflowid = ? AND autoworkflowtaskid = ? AND crmid=?";
            
            if ($request->get('pauseaudit') == "on") { // 是否暂停审核
                $adb->pquery($saveremarsksql, array(
                    "1",
                    $taskremark,
                    $date,
                    $userid,
                    "1",
                    $autoworkflowid,
                    $autoworkflowtaskid,
                    $crmid
                ));
                
                //判断后台是否需要发送邮件，需要 能够审核，就发送邮件。
            } else {
                // 这边放置前置任务，用于判断当前任务节点能否被审核;// end
                if($issendit == "on"){ //如果勾选发送邮件选项；
                    //print_r($request);die;

                    $file_id_array = $request->get('attachmentsid');//附件id
                    $file_name_array = $request->get('file'); //附件名称
                    $file_path_array = $request->get('filepath'); //附件的路径
                    $file_array = array();
                    $sql_file = array();
                    if(!empty($file_id_array)){
                        foreach($file_id_array as $k=>$v){
                            $sql_file[] = $v;
                            $file_array[$v] = array($file_name_array[$k],$file_path_array[$k]);
                        }
                    }
                   $sting_file = implode('##',$sql_file);
                   $returnmailstring =  $this->sendmail($subject,$mailcontext,$user_receive_email,$user_copy_email,$custom_receArr,$custom_copyArr,$file_array);
                   // echo $returnmailstring;die;
                    //对当前内容处理,插入邮件历史表中去;
                   if($returnmailstring == "true"){
                       $insertsql = 'INSERT INTO vtiger_emaildetails ( from_email, to_email, cc_email, `subject`, body, assigned_user_email, taskid,custom_rece,custom_copy,`module`,moduleid,sendtime,file) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)';
                       $adb->pquery($insertsql,array('1797072465@qq.com',$receiveidstring,$copyidstring,$subject,$mailcontext,$userid,$currenttaskprimaryid,$custom_rece,$custom_copy,$module,$moduleid,$date,$sting_file));
                   }else{
                    //邮箱发送失败的处理,待完善
                    //   return $returnmailstring;
                   }
                }
                // die($returnmailstring);

//                关闭当前节点,激活下一个节点;
                $adb->pquery($saveremarsksql, array(
                    "0",
                    $taskremark,
                    $date,
                    $userid,
                    "2",
                    $autoworkflowid,
                    $autoworkflowtaskid,
                    $crmid
                ));
                $current_next = explode(",", $currenttask['process_to']);
                if (! empty($current_next['0'])) {
                    foreach ($current_next as $val1) {
                        $ifactive_current_next = true; // 当前节点的下一个节点能否激活
                        $current_next_data = $this->gettaskinstance($autoworkflowid, $val1, $crmid); // 下一个节点详细
                        $current_next_prev = explode(",", $current_next_data['process_from']);
                        
                        foreach ($current_next_prev as $val2) {
							$aa=$this->gettaskinstance($autoworkflowid, $val2, $crmid);
                            if ($aa['isaction'] !== "2") {
                                $ifactive_current_next = false;
                            }
                        }
                        $current_next_houtaitaskdetail = $this->gethoutaitaskinstance($val1);
                        // var_dump($current_next_houtaitaskdetail['autodetails']);  待完善 **需要注意一下,此客户可能没有分配客服

                        if (empty($current_next_houtaitaskdetail['autodetails'])) { // 如果下一节点没有审核人,就指定客户负责人
                            //$selupdate = "UPDATE vtiger_autoworkflowtasks SET autorole = 0, autodetails = ( SELECT serviceid FROM `vtiger_servicecomments` WHERE assigntype = ? AND related_to = ? ) WHERE autoworkflowtaskid = ? AND autoworkflowid = ?";
                            $selupdate = "UPDATE vtiger_autoworkflowtaskentitys SET autorole = 0, autodetails = ( SELECT serviceid FROM `vtiger_servicecomments` WHERE assigntype = ? AND related_to = ? ) WHERE autoworkflowtaskid = ? AND autoworkflowid = ?";
                            $adb->pquery($selupdate, array(
                                "accountby",
                                $current_next_data['accountid'],
                                $val1,
                                $autoworkflowid
                            ));
                        }
                        
                        if ($ifactive_current_next) {
                            // 这边可以用来放置激活事件。
                            $fun = $current_next_houtaitaskdetail['templ']['2']['jsonarray']->middle;
                            if (function_exists("$fun")){ 
                                $fun();
                            }
                            // end
                            
                            // 激活下一个节点
                            $active_current_next_sql = "UPDATE vtiger_autoworkflowtaskentitys SET isaction = ?, activetime = ? WHERE autoworkflowid = ? AND autoworkflowtaskid = ? AND crmid= ?";
                            $adb->pquery($active_current_next_sql, array(
                                "1",
                                $date,
                                $autoworkflowid,
                                $val1,
                                $crmid
                            ));
                            
                            // 是否通知执行人,等等
                        }
                    }
                }
                // 这边可以用来放置后置任务
            }
        }
    }
    //从 submitaudit() 中抽离出来 用于 关闭当前节点,激活下一节点;
    public function closeCurrent_openNext($qiantai_taskArr,$commentcontent){
        //确定客服任务的task 主键id(这里就是合同ID)，工作流id, 后台taskid 。
        global $adb;
        global $current_user;
        $userid = $current_user->id; //当前登录用户
        $taskremark = $commentcontent; // 备注内容

        $currenttask =  $qiantai_taskArr;// 当前记录详细
        $autoworkflowentityid = $currenttask['autoworkflowentityid'];
        $currenttaskprimaryid = $currenttask['autoworkflowtaskentityid'];

        // var_dump($currenttask);die;

        $accountid = $currenttask['accountid'];//当前节点客户id
        $module = $currenttask['modulename'];//所关联的模块
        $moduleid = $currenttask['crmid'];//所关联的模块的主键id
        $date_var = date("Y-m-d H:i:s");
        $date = $adb->formatDate($date_var, true); //时间
        // var_dump($currenttask);

        if (($currenttask['isaction'] !== "2" && $currenttask['process_from'] == "") || $currenttask['isaction'] == "1") { // 只有当前节点是激活状态或者是第一个没有关闭的节点才能审核；
            $saveremarsksql = "UPDATE vtiger_autoworkflowtaskentitys SET pauseaudit = ?, taskremark = ?, auditedtime = ?,auditorid=?,isaction=? WHERE autoworkflowtaskentityid =?";
                // 这边放置前置任务，用于判断当前任务节点能否被审核;

                // end
//注意这里有一点点修改？？？？                关闭当前节点,激活下一个节点;
                $adb->pquery($saveremarsksql, array(
                    "0",
                    $taskremark,
                    $date,
                    $userid,
                    "2",
                    $currenttaskprimaryid
                ));
                $current_next = explode(",", $currenttask['process_to']);
                if (! empty($current_next['0'])) {
                    foreach ($current_next as $val1) {
                        $ifactive_current_next = true; // 当前节点的下一个节点能否激活
                        $current_next_data = self::gettaskinstanceby($autoworkflowentityid,$val1); // 下一个节点详细
                        $current_next_prev = explode(",", $current_next_data['process_from']);
                        foreach ($current_next_prev as $val2) {
							$ttt=self::gettaskinstanceby($autoworkflowentityid,$val2);
                            if ($ttt['isaction'] !== "2") {
                                $ifactive_current_next = false;
                            }
                        }
                        $current_next_houtaitaskdetail = self::gethoutaitaskinstance($val1);
                        /*// var_dump($current_next_houtaitaskdetail['autodetails']);  待完善 **需要注意一下,此客户可能没有分配客服
                        if (empty($current_next_houtaitaskdetail['autodetails'])) { // 如果下一节点没有审核人,就指定客户负责人
                            $selupdate = "UPDATE vtiger_autoworkflowtasks SET autorole = 0, autodetails = ( SELECT serviceid FROM `vtiger_servicecomments` WHERE assigntype = ? AND related_to = ? ) WHERE autoworkflowtaskid = ? AND autoworkflowid = ?";
                            $adb->pquery($selupdate, array(
                                "accountby",
                                $current_next_data['accountid'],
                                $val1,
                                $autoworkflowid
                            ));
                        }*/
                        if ($ifactive_current_next) {
                            // 这边可以用来放置激活事件。
                           /* $fun = $current_next_houtaitaskdetail['templ']['2']['jsonarray']->middle;
                            if (function_exists("$fun")){
                                $fun();
                            }*/
                            // end

                            // 激活下一个节点
                            $active_current_next_sql = "UPDATE vtiger_autoworkflowtaskentitys SET isaction = ?, activetime = ? WHERE autoworkflowentityid=? AND autoworkflowtaskid = ? ";
                            $adb->pquery($active_current_next_sql, array(
                                "1",
                                $date,
                                $autoworkflowentityid,
                                $val1,
                            ));

                            // 是否通知执行人,等等
                        }
                    }
                }
                // 这边可以用来放置后置任务
        }
    }

    /*@author wangbin 2015年9月7日 17:39:39 查找《客户--客服》 激活额任务包节点（客服对客户跟进时，会用到。）
     * 根据客户id查找，当前关联当前客户的客服任务包；
     * */
    public function service_follow($accountid){
        global $adb;
        $select_qiantai_tasksql = "SELECT vtiger_autoworkflowtaskentitys.* FROM vtiger_autoworkflowtaskentitys  WHERE vtiger_autoworkflowtaskentitys.accountid = ? AND vtiger_autoworkflowtaskentitys.isaction = ? AND vtiger_autoworkflowtaskentitys.relationmodule=?";
        $result = $adb->pquery($select_qiantai_tasksql,array($accountid,1,servicecommen));
        return $adb->fetchByAssoc($result,0);
    }


    //IDC备案保存时查询需要跟进的客服任务包;
    public function IDC_follow($salesorderid){
        global $adb;
        $sql =  "SELECT * FROM vtiger_autoworkflowtaskentitys WHERE salesorderid=? AND relationmodule = ? AND isaction = ? ";
        $result = $adb->pquery($sql,array($salesorderid,'IdcRecords',1));
        return $adb->fetchByAssoc($result,0);
    }



    public function process(Vtiger_Request $request)
    {
        $mode = $request->getMode();
        if (! empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
            return;
        }
    }
    //判断文件是否存在，2.方法是否存在
}
