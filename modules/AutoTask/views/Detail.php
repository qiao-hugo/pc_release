<?php
/*+***********************************************************************************
 wangbin 新增
 *************************************************************************************/

class AutoTask_Detail_View extends Vtiger_Detail_View {

    protected $record = false;

    function __construct() {
        parent::__construct();
        $this->exposeMethod('maildetail');
    }
    
    public function getHeaderScripts(Vtiger_Request $request) {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName = $request->getModule();
    
        $jsFileNames = array(
            "modules.$moduleName.resources.Detailaudit",
            "modules.Vtiger.resources.CkEditor"
        );
    
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }
    
    function process(Vtiger_Request $request) {
        global $current_user;
        $mode = $request->getMode();
        if(!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
            return;
        }
        $clickid = $request->get('clickid');
        //加载工作流的任务详细图表
       $recordId =  $request->get('record'); //前台工作流id
       $source_record=$request->get("source_record");//后台工作流id
       
       $autoworktaskmodle = Vtiger_Record_Model::getInstanceById($recordId,'AutoTask'); //根据工作流id获取详细任务id;
       $autoworktaskresult = $autoworktaskmodle->getData();  //前台所有任务集合

       $autotaskid = $autoworktaskresult['autoworkflowid'];  //自动任务id 所关联的 工作流id;
       $crmid = $autoworktaskresult['crmid'];                //关联模块主键id;
       $user_taskid = $this->usertaskid($source_record);     //当前当前用户能够看到的任务节点id;
       global $adb;
        $viewer = $this->getViewer($request);
        //读取当前流程下的所有的节点
       // $sql = 'SELECT * FROM  vtiger_autoworkflowtaskentitys WHERE autoworkflowid=? AND deleted !=? AND crmid =? ';
        $sql = 'SELECT * FROM  vtiger_autoworkflowtaskentitys WHERE  deleted !=? AND autoworkflowentityid=? ';
        $result = $adb->pquery($sql,array("1",$recordId));
        $arrModel=array();
        if($adb->num_rows($result)>0){
            for($i=0;$i<$adb->num_rows($result);$i++){
                $temp = $adb->fetchByAssoc($result);
                if(in_array($temp['autoworkflowtaskid'],$user_taskid)||$current_user->is_admin == "on"){
                    $temp['icon'] = "icon-ok";
                }else{
                    $temp['icon'] = "icon-warning-sign";
                }
                $arrModel[]=$temp;
            }
        }
        if($clickid){
            $newclickid = "window".$clickid;
            $viewer->assign('CLICKID',$newclickid);
        }
        $viewer->assign("AUTOFOLOID",$source_record);
        $viewer->assign("AutoTaskID",$recordId);
        $viewer->assign('MODULE_MODLE',$arrModel);
		$viewer->assign('CRMID',$crmid);  //关联模块主键id；
        $viewer->view('showworkflow.tpl','AutoTask');
       //end        
//bak_sql; $sql2 = "SELECT             process_from,modulename, autoworkflowtaskname, auditedtime, createdtime, vtiger_account.accountname AS accountid, actiontime, isaction, moulestatus, ( SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE vtiger_autoworkflowtaskentitys.creatorid = vtiger_users.id ) AS creatorid,taskremark FROM `vtiger_autoworkflowtaskentitys` LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_autoworkflowtaskentitys.accountid WHERE autoworkflowid = ? AND crmid=?";
           $sql2 = "SELECT autoworkflowentityid,autoworkflowid,mail_ID,mail_report,process_from,modulename, autoworkflowtaskname, auditedtime, createdtime, vtiger_account.accountname AS accountid, actiontime, isaction, moulestatus, ( SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE vtiger_autoworkflowtaskentitys.creatorid = vtiger_users.id ) AS creatorid,taskremark FROM `vtiger_autoworkflowtaskentitys` LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_autoworkflowtaskentitys.accountid WHERE autoworkflowid = ? AND crmid=?";
            $taskdata = $adb->pquery($sql2,array($source_record,$crmid));
            $arrTaskdata = array();
            if($adb->num_rows($taskdata)>0){
                while($row=$adb->fetch_array($taskdata)){
                    if (empty($row['process_from']) && $row['isaction'] =='0'){
                        $row['isaction'] = "1";
                    }
                    $arrTaskdata[]=$row;
                }
            }
        $viewer->assign("LISTTASKDATA",$arrTaskdata);
        echo $this->showModuleDetailView($request); 
    }
    
    /*查询当前用户能看到的所有任务包id;
     * @author wangbin 2015年7月24日
     *  @param  int 工作id 
     *  @return array() 任务包id;
     */ 
    public function usertaskid($autoworkflowid){
        global $current_user;
        global $adb;
        $roleid =   $current_user->roleid;  //  liqin 角色id H87
        $userid =  $current_user->id;      //        用户id 1121
        $groupid = array();          //        所属 组 2419 2293
        $groupsql = "SELECT * FROM `vtiger_users2group` WHERE userid = ?";
        $groupids = $adb->pquery($groupsql,array($userid));
        if($adb->num_fields($groupids)>0){
            for ($i=0;$i<$adb->num_rows($groupids);$i++){
                //$groupid[]="Groups:".$adb->fetchByAssoc($groupids)['groupid'];
				$aa="Groups:".$adb->fetchByAssoc($groupids);
				$groupid[]=$aa['groupid'];
            }
        }
        if (empty($autoworkflowid)){
            $group_task_sql = "SELECT autoworkflowtaskid,autorole,autodetails FROM `vtiger_autoworkflowtasks`";
            $task_result = $adb->pquery($group_task_sql,array());
        }else{
            $group_task_sql = "SELECT autoworkflowtaskid,autorole,autodetails FROM `vtiger_autoworkflowtasks` WHERE autoworkflowid = ? ";
            $task_result = $adb->pquery($group_task_sql,array($autoworkflowid));
        }
        if($adb->num_rows($task_result)>0){
            for($i=0;$i<$adb->num_rows($task_result);$i++){
                $temp = $adb->fetchByAssoc($task_result);
                $temp['autodetails'] = explode("##", $temp['autodetails']); //拆分数组
                if($temp['autodetails']['0'] !== ""){
                    if($temp['autorole'] == "0"){
                        if(in_array($userid,$temp['autodetails'])){
                            $task_user[] = $temp['autoworkflowtaskid'];
                        }
                    }elseif ($temp['autorole'] == "1"){
                        if(array_intersect($groupid,$temp['autodetails'])){
                            $task_user[] = $temp['autoworkflowtaskid'];
                        }
                        $group[$temp['autoworkflowtaskid']] = $temp['autodetails'];
                    }elseif($temp['autorole'] == "2"){                          //判断执行人类型 0-用户id 1-组id 2-角色id
                        if(in_array($roleid,$temp['autodetails'])){
                            $task_user[] = $temp['autoworkflowtaskid'];
                        }
                    }
                }
            }
        }
        return $task_user;
    }


    //wangbin 查找后台没有分配审核人的任务包节点   2015年9月7日 17:05:29 优先级别不高，有待完善;
    public function qiantai_autodetails(){
        global $current_user;
        global $adb;
        $userid =  $current_user->id;      //        用户id 1121
        $sel_qiantaiautodetail_sql = "SELECT  autoworkflowentityid,autoworkflowtaskid FROM `vtiger_autoworkflowtaskentitys` WHERE autodetails = ?";
        $qiantai_autodetail_result = $adb->pquery($sel_qiantaiautodetail_sql,array($userid));
       $houtai_taskid_qiantai_flowid=array();
        for($i=0;$i<$adb->num_rows($qiantai_autodetail_result);$i++){
            $qiantaitemp = $adb->fetchByAssoc($qiantai_autodetail_result);
            $houtai_taskid_qiantai_flowid[$qiantaitemp['autoworkflowtaskid']] = $qiantaitemp['autoworkflowentityid'];
        }
        return $houtai_taskid_qiantai_flowid;
    }


    /*前台工作流列表权限控制*/
    public function userflowid(){
        global $adb;
        $userflowid = array();
        $usertaskid = self::usertaskid();
       // $usertaskid = array(29,30,31,32,18,19,20,23,41,42,43,44,45,46,47,48,36,37,39,40,2,3,4,5,6,7,8,9,13,11,12,14,15,16,17,38,49,21,22,24,25,26,27,28,33,34,35,50,51);
        $selectfloweridsql = "SELECT autoworkflowid FROM vtiger_autoworkflowtasks WHERE autoworkflowtaskid = ?";
        foreach ($usertaskid as $val){
            $result = $adb->pquery($selectfloweridsql,array($val));
            $resultdetail = $adb->fetchByAssoc( $result ,0);
            //array_push($userflowid, $resultdetail['autoworkflowid']);
            $userflowid[$resultdetail['autoworkflowid']] = $resultdetail['autoworkflowid'];
        }
        return $userflowid;
    }
    public function maildetail(Vtiger_Request $request){
        global $adb;
        $mailid =  $request->get('mailid');
        if(!empty($mailid)){
            $sql = "SELECT * FROM `vtiger_emaildetails` WHERE emailid = ?";
            $source_result = $adb->pquery($sql,array($mailid));
            $result = $adb->fetchByAssoc($source_result,0);
            $result['to_email'] = $this->user_email($result['to_email']); //收件人
            $result['cc_email'] = $this->user_email($result['cc_email']);//抄送人
            $result['file'] =  Vtiger_FileUpload_UIType::getDisplayValue($result['file']);
            //$str = '</span><span class="badge">'.str_replace('##','</span><span class="badge">',$str).'</span>';
            $result['custom_rece'] = '<span class="badge">'.str_replace('##','</span><span class="badge">',$result['custom_rece']).'</span>';
            $result['custom_copy'] = '<span class="badge">'.str_replace('##','</span><span class="badge">',$result['custom_copy']).'</span>';
            //自定义抄送邮箱
            //自定义收件
            //这边可能还需要一个附件下载历史的链接；
            $viewer = $this->getViewer($request);
            $viewer->assign('MAIL_DETAIL',$result);  //邮件历史内容
            $viewer->view('Mail.tpl','AutoTask');

        }
    }

    public function user_email($userid){
        $user_array = explode('##',$userid);
         $user_email = "";
        foreach($user_array as $v){
            if(strstr("$v","acc")){
                $user_email.= "<span class='badge'>客户首要联系人</span>";
            }else{
                $userModel = Vtiger_Record_Model::getInstanceById($v, 'Users');
                $userdetail=$userModel->entity->column_fields;
                //$user_receive_email[$userdetail[record_id]] = array($userdetail['last_name'],$userdetail['email1']);
                $user_email.= "<span class='badge'>".$userdetail['last_name']."</span>";
            }
        }
        return $user_email;
    }
    public function postProcess(Vtiger_Request $request) {
        parent::postProcess($request);
    }
}
