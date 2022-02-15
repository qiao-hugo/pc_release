<?php
class UserManger_ChangeAjax_Action extends Vtiger_Action_Controller {
    function __construct() {
        parent::__construct();
        $this->exposeMethod('userExists');
        $this->exposeMethod('getRoles');
        $this->exposeMethod('changePassword');
        $this->exposeMethod('addAuditsettings');
        $this->exposeMethod('deletedAuditsettings');
        $this->exposeMethod('updateWexinInfo');
        $this->exposeMethod('updateUserStatus');
        $this->exposeMethod('addUsers');
        $this->exposeMethod('removeuser');
        $this->exposeMethod('multiUpdateSupervisor');
        $this->exposeMethod('getUsers');
        $this->exposeMethod('transfer');
        $this->exposeMethod('doTransfer');
        $this->exposeMethod('checkStaffType');
	$this->exposeMethod('updateYXTInfo');

    }

	function checkPermission(Vtiger_Request $request) {
		return true;
	}

	public function process(Vtiger_Request $request) {
        $mode = $request->getMode();
        if(!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
            return;
        }
	}
    public function userExists(Vtiger_Request $request){
        $module = $request->getModule();
        $record=$request->get('record');
        $querytype = $request->get('querytype');
        $fieldvalue = $request->get('fieldvalue');
        $userModuleModel = Vtiger_Module_Model::getCleanInstance($module);
        $status = $userModuleModel->checkDuplicateUser($querytype,$fieldvalue,$record);
        $response = new Vtiger_Response();
        $response->setResult($status);
        $response->emit();
    }
    public function getRoles(Vtiger_Request $request){
        $module = $request->getModule();
        $recordModel = Vtiger_Record_Model::getCleanInstance($module);
        $status = $recordModel->getRoles($request);
        $response = new Vtiger_Response();
        $response->setResult($status);
        $response->emit();
    }
    /**
     * 更改密码
     * @param Vtiger_Request $request
     */
    public function changePassword(Vtiger_Request $request){
        $module = $request->getModule();
        $recordId=$request->get('record');
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId,$module);
        $status = $recordModel->changePassword($request);
        $response = new Vtiger_Response();
        $response->setResult($status);
        $response->emit();
    }
    //用户添加审核
    public function addAuditsettings(Vtiger_Request $request) {
        $auditsettingtype ='UserManger';
        $department = $request->get("department");
        $oneaudituid = $request->get("oneaudituid");
        $towaudituid = $request->get("towaudituid");
        $audituid3 = $request->get("audituid3");
        $audituid5 = $request->get("audituid5");
        $id = $request->get("id");
        $data = array('flag'=>'0', 'msg'=>'添加失败');
        do {
            $recordModel=Vtiger_Record_Model::getCleanInstance('UserManger');
            if(!$recordModel->personalAuthority('UserManger','AuditSettings')){   //权限验证
                break;
            }
            if (empty($auditsettingtype)) {
                break;
            }
            if (empty($department) && empty($id)) {
                break;
            }
            if (empty($oneaudituid)) {
                break;
            }
            if (empty($towaudituid)) {
                break;
            }
            if (empty($audituid3)) {
                break;
            }
            global $current_user;
            $db=PearDatabase::getInstance();
            if($id>0){
                $sql2 = "UPDATE `vtiger_auditsettings` SET `oneaudituid`=?, `towaudituid`=?,`audituid3`=?,audituid5=? where `auditsettingsid`=?";
                $db->pquery($sql2, array($oneaudituid, $towaudituid, $audituid3,$audituid5,$id));
                $data = array('flag'=>'1', 'msg'=>'添加成功');
                break;
            }
            $query='SELECT auditsettingsid FROM vtiger_auditsettings WHERE auditsettingtype=? AND department=?';
            $result=$db->pquery($query,array($auditsettingtype,$department));
            if($db->num_rows($result)){
                $auditsettingsid=$db->raw_query_result_rowdata($result,0);
                $sql2 = "UPDATE `vtiger_auditsettings` SET `oneaudituid`=?, `towaudituid`=?,`audituid3`=?,audituid5=? where `auditsettingsid`=?";
                $db->pquery($sql2, array($oneaudituid, $towaudituid, $audituid3,$audituid5,$auditsettingsid['auditsettingsid']));
            }else{
                $sql2 = "INSERT INTO `vtiger_auditsettings` (`auditsettingsid`, `auditsettingtype`, `department`, `oneaudituid`, `towaudituid`,`audituid3`,audituid5, `createtime`, `createid`) VALUES (NULL, ?,?,?,?, ?,?,?,?);";
                $db->pquery($sql2, array($auditsettingtype, $department, $oneaudituid, $towaudituid, $audituid3,$audituid5,date('Y-m-d H:i:s'), $current_user->id));
            }
            $data = array('flag'=>'1', 'msg'=>'添加成功');
        } while (0);
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
    public function deletedAuditsettings(Vtiger_Request $request) {
        $recordModel=Vtiger_Record_Model::getCleanInstance('UserManger');
        $data='无权操作';
        if($recordModel->personalAuthority('UserManger','AuditSettings')){   //权限验证
            $data='更新成功';
            global $current_user;
            $id=$request->get("id");
            $delsql="delete from vtiger_auditsettings where auditsettingsid=?";
            $db=PearDatabase::getInstance();
            $db->pquery($delsql,array($id));
        }
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * 导入用户
     */
    public function addUsers(){
        global $adb;
        $query="select * from vtiger_users";
        $result=$adb->pquery($query,array());
        if($adb->num_rows($result)){
            $date=date('Y-m-d H:i:s');
            while($row=$adb->fetch_array($result)){
                $query='select 1 from vtiger_usermanger WHERE userid=?';
                $tresult=$adb->pquery($query,array($row['id']));
                if($adb->num_rows($tresult)==0){
                    $crmid=$adb->getUniqueID('vtiger_crmentity');
                    $sql="INSERT INTO `vtiger_usermanger` (usermangerid,`userid`, `user_name`, `user_password`, `user_hash`, `cal_color`, `first_name`, `last_name`, `reports_to_id`, `is_admin`, `currency_id`, `description`, `date_entered`, `date_modified`, `modified_user_id`, `title`, `invoicecompany`, `companyid`, `department`, `phone_home`, `phone_mobile`, `phone_work`, `phone_other`, `phone_fax`, `email1`, `email2`, `secondaryemail`, `status`, `signature`, `address_street`, `address_city`, `address_state`, `user_sys`, `address_postalcode`, `user_preferences`, `tz`, `holidays`, `namedays`, `workdays`, `weekstart`, `date_format`, `hour_format`, `start_hour`, `end_hour`, `activity_view`, `lead_view`, `imagename`, `deleted`, `confirm_password`, `internal_mailer`, `reminder_interval`, `reminder_next_time`, `crypt_type`, `accesskey`, `theme`, `language`, `time_zone`, `currency_grouping_pattern`, `currency_decimal_separator`, `currency_grouping_separator`, `currency_symbol_placement`, `no_of_currency_decimals`, `truncate_trailing_zeros`, `dayoftheweek`, `callduration`, `othereventduration`, `calendarsharedtype`, `default_record_view`, `leftpanelhide`, `rowheight`, `old_departmentid`, `old_user_password`, `usermodifiedtime`, `usercode`, `user_entered`, `fillinsales`, `brevitycode`, `leavedate`, `isdimission`,departmentid,roleid,secondroleid,modulestatus) 
                          SELECT ?,`id`, `user_name`, `user_password`, `user_hash`, `cal_color`, `first_name`, `last_name`, `reports_to_id`, `is_admin`, `currency_id`, `description`, `date_entered`, `date_modified`, `modified_user_id`, `title`, `invoicecompany`, `companyid`, `department`, `phone_home`, `phone_mobile`, `phone_work`, `phone_other`, `phone_fax`, `email1`, `email2`, `secondaryemail`, `status`, `signature`, `address_street`, `address_city`, `address_state`, `user_sys`, `address_postalcode`, `user_preferences`, `tz`, `holidays`, `namedays`, `workdays`, `weekstart`, `date_format`, `hour_format`, `start_hour`, `end_hour`, `activity_view`, `lead_view`, `imagename`, `deleted`, `confirm_password`, `internal_mailer`, `reminder_interval`, `reminder_next_time`, `crypt_type`, `accesskey`, `theme`, `language`, `time_zone`, `currency_grouping_pattern`, `currency_decimal_separator`, `currency_grouping_separator`, `currency_symbol_placement`, `no_of_currency_decimals`, `truncate_trailing_zeros`, `dayoftheweek`, `callduration`, `othereventduration`, `calendarsharedtype`, `default_record_view`, `leftpanelhide`, `rowheight`, `old_departmentid`, `old_user_password`, `usermodifiedtime`, `usercode`, `user_entered`, `fillinsales`, `brevitycode`, `leavedate`, `isdimission`,vtiger_user2department.departmentid,vtiger_user2role.roleid,vtiger_user2role.secondroleid,'c_complete' FROM vtiger_users LEFT JOIN vtiger_user2department ON vtiger_users.id=vtiger_user2department.userid LEFT JOIN vtiger_user2role ON vtiger_user2role.roleid=vtiger_users.id WHERE vtiger_users.id=?";
                    $adb->pquery($sql,array($crmid,$row['id']));
                    $sql="INSERT INTO `vtiger_crmentity` (`crmid`, `smcreatorid`, `smownerid`, `modifiedby`, `setype`, `description`, `createdtime`, `modifiedtime`, `viewedtime`, `status`, `version`, `presence`, `deleted`, `label`) VALUES (?, 1, '1', '1', 'UserManger', '', ?, ?, NULL, NULL, '0', '1', 0, ?)";
                    $adb->pquery($sql,array($crmid,$date,$date,$row['last_name']));
                }
            }

        }
    }

    /**
     * 更新微信状态
     * @param Vtiger_Request $request
     */
    public function updateWexinInfo(Vtiger_Request $request){
        $record=$request->get('record');
        $recordModel=Vtiger_Record_Model::getInstanceById($record,'UserManger');
        $data['username']=$recordModel->get('last_name');
        $data['email']=$recordModel->get('email1');
        $data['oldemail']=$recordModel->get('email1');
        $data['departmentid']=trim($recordModel->get('departmentid'),'H');
        $data['flag']=2;
        $data['ERPDOIT']=456321;
        $data['mobile']=$recordModel->get('mobile');
        $userkey='c0b3Ke0Q4c%2BmGXycVaQ%2BUEcbU0ldxTBeeMAgUILM0PK5Q59cEp%2B40n6qUSJiPQ';
        global $m_crm_domian_api_url;
        $url = $m_crm_domian_api_url;
        $ch  = curl_init();
        $data['tokenauth']=$userkey;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_exec($ch);
        curl_close($ch);
        /* 记录操作日志 start */
        global $adb, $current_user;
        $datetime = date('Y-m-d H:i:s');
        $id = $adb->getUniqueId('vtiger_modtracker_basic');
        $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?, ?, ?, ?, ?, ?)',
            array($id, $record, 'UserManger', $current_user->id, $datetime, 0));
        $adb->pquery('INSERT INTO vtiger_modtracker_detail(id, fieldname, prevalue, postvalue) VALUES(?, ?, ?, ?)',
            Array($id, 'email1', '', '更新微信状态'));
        /* 记录操作日志 end */
        $response = new Vtiger_Response();
        $response->setResult(array());
        $response->emit();
    }
    public function updateUserStatus(Vtiger_Request $request){
        $record=$request->get('record');
        $leavedate=$request->get('leavedate');
        $userstatus=$request->get('userstatus');
        $saveAjaxAction=new Vtiger_SaveAjax_Action();
        $recordModel=$saveAjaxAction->getRecordModelFromRequest($request);
        $modelModule=$recordModel->getModule();
        $email1=$recordModel->get('email1');
        if(in_array($userstatus,array(2,3))){
            if(empty($leavedate)){
                $response = new Vtiger_Response();
                $response->setError('','请填写离职日期!');
                $response->emit();
                exit;
            }
        }
        if(5==$userstatus && $modelModule->checkDuplicateUser('email1',$email1,$record)){
            //重启账号时不允许邮箱重复
            $response = new Vtiger_Response();
            $response->setError('','邮箱重复!');
            $response->emit();
            exit;
        }
        $array=array();
        $req=new Vtiger_Request($array,$array);
        $dontmodify=array('user_password','confirm_password');
        $entity=$recordModel->getData();
        foreach($entity as $key=>$value){
            if($key!='record'){
                $req->set($key,$value);
                $_REQUEST[$key]=$value;
            }
            if(in_array($key,$dontmodify)){
                continue;
            }
        }
        switch($userstatus){
            case 1://还原,针对账户未禁用离职状态的
                $recordModel->set('isdimission',0);
                $req->set('isdimission',0);
                $_REQUEST['isdimission']=0;
                $recordModel->set('leavedate','');
                $req->set('leavedate','');
                $_REQUEST['leavedate']='';
                break;
            case 2://离职,针对账户未禁用
                $recordModel->set('isdimission',1);
                $req->set('isdimission',1);
                $_REQUEST['isdimission']=1;
                $recordModel->set('leavedate',$leavedate);
                $req->set('leavedate',$leavedate);
                $_REQUEST['leavedate']=$leavedate;
                break;
            case 3://禁用,针对账户未禁用
                $recordModel->set('isdimission',1);
                $req->set('isdimission',1);
                $_REQUEST['isdimission']=1;
                $recordModel->set('status','Inactive');
                $req->set('status','Inactive');
                $_REQUEST['isdimission']='Inactive';
                $recordModel->set('leavedate',$leavedate);
                $req->set('leavedate',$leavedate);
                $_REQUEST['leavedate']=$leavedate;
                break;
            case 5://启用,针对账户禁用
                $recordModel->set('isdimission',0);
                $req->set('isdimission',0);
                $recordModel->set('leavedate','');
                $req->set('leavedate','');
                $recordModel->set('status','Active');
                $req->set('status','Active');
                break;
            default :
                $response = new Vtiger_Response();
                $response->setError('','输入有误!');
                $response->emit();
                exit;
        }
        $req->set('module','Users');
        $req->set('id',$entity['userid']);
        $req->set('action','Save');
        $req->set('record',$entity['userid']);
        $userSaveAction=new Users_Save_Action();
        $userSaveAction->saveRecord($req);
        $recordModel->set('action','Save');
        $recordModel->save();
        $response = new Vtiger_Response();
        $response->setResult(array());
        $response->emit();
    }

    /**
     * 用户上级转移
     * @param $request
     */
    public function removeuser($request){
        $toid= $request->get('toid');
        $removeuser= $request->get('removeuser');
        if(!in_array($toid,$removeuser)){
            $db = PearDatabase::getInstance();
            $db->pquery('update vtiger_usermanger set reports_to_id=? where userid in('.implode(',',$removeuser).')',array($toid));
            $db->pquery('update vtiger_users set reports_to_id=? where id in('.implode(',',$removeuser).')',array($toid));
        }
        echo 1;
    }

    /**
     * ；批量修改用户上级
     */
    public function multiUpdateSupervisor($request){
        $reports_to_id = $request->get('reports_to_id');
        $ids = $request->get('ids');
        if(count($ids)<1 || !$reports_to_id){
            $response = new Vtiger_Response();
            $response->setResult(array('success'=>false,'msg'=>'参数错误'));
            $response->emit();
            exit;
        }
        foreach ($ids as $id){
            $recorderModel = Vtiger_Record_Model::getInstanceById($id,'UserManger');
            $userid = $recorderModel->get('userid');
            $recorderModel2 = Vtiger_Record_Model::getInstanceById($userid,'Users');
            $recorderModel2->set('id',$userid);
            $recorderModel2->set('reports_to_id',$reports_to_id);
            $recorderModel2->set('mode','edit');
            $recorderModel2->save();
            $recorderModel->set('id',$id);
            $recorderModel->set('reports_to_id',$reports_to_id);
            $recorderModel->set('mode','edit');
            $recorderModel->save();
        }
        $response = new Vtiger_Response();
        $response->setResult(array('success'=>true,'msg'=>'更新成功'));
        $response->emit();
    }

    public function getUsers($request){
        $users = ReceivedPayments_Record_Model::getuserinfo(" AND `status`='Active'");
        $response = new Vtiger_Response();
        $response->setResult(array('success'=>true,'data'=>$users));
        $response->emit();
    }

    public function transfer($request){
        require 'crmcache/role.php';
        require 'crmcache/departmentanduserinfo.php';
        $users = ReceivedPayments_Record_Model::getuserinfo(" AND `status`='Active'");

        $moduleModel = Vtiger_Record_Model::getCleanInstance('UserManger');

        $response = new Vtiger_Response();
        $response->setResult(
            array(
                'success'=>true,
                'reports_to_id'=>$users,
                'invoicecompany'=>$moduleModel->getInvoicecompany(),
                'roleid'=>$moduleModel->getRole(),
                'employeelevel'=>$moduleModel->getEmployeelevel(),
                'departmentid'=>$departlevel
                )
        );
        $response->emit();
    }

    public function doTransfer($request){
        $type = $request->get('type');
        $effectivetime = $request->get('effectivetime');
        $ids = $request->get('userids');
        if(count($ids)<1 || !$effectivetime || !$type){
            $response = new Vtiger_Response();
            $response->setResult(array('success'=>false,'msg'=>'参数错误'));
            $response->emit();
            exit;
        }
        $status = 0;
        if(strtotime($effectivetime)<time()){
            $status = 1;
        }

        global $current_user;
        $db = PearDatabase::getInstance();
        try{
            foreach ($ids as $id) {
                $recorderModel = Vtiger_Record_Model::getInstanceById($id, 'UserManger');
                $userid = $recorderModel->get('userid');
                $recorderModel2 = Vtiger_Record_Model::getInstanceById($userid,'Users');

                $sql = 'insert into vtiger_transfer_history (userid,usermangerid,original_reports_to_id,original_invoicecompany,original_companyid,original_departmentid,original_roleid,original_department,original_title,original_employeelevel,reports_to_id,invoicecompany,companyid,departmentid,roleid,department,title,employeelevel,type,createdtime,effectivetime,creatorid,status) values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
                $params = array(
                    $userid,
                    $id,
                    $recorderModel->get('reports_to_id'),
                    $recorderModel->get('invoicecompany'),
                    $request->get('old_companyid'),
                    $recorderModel->get('departmentid'),
                    $recorderModel->get('roleid'),
                    $recorderModel->get('department'),
                    $recorderModel->get('title'),
                    $recorderModel->get('employeelevel'),
                    $request->get('reports_to_id'),
                    $request->get('invoicecompany'),
                    $request->get('companyid'),
                    $request->get('departmentid'),
                    $request->get('roleid'),
                    $request->get('department'),
                    $request->get('title'),
                    $request->get('employeelevel'),
                    $type,
                    date("Y-m-d H:i:s"),
                    $effectivetime,
                    $current_user->id,
                    $status
                );
                $db->pquery($sql,$params);

                //生效时间小于当前时间则直接生效 修改对应字段的值
                if($status){
                    $recorderModel2->set('id',$userid);
                    $recorderModel2->set('reports_to_id',$request->get('reports_to_id')?$request->get('reports_to_id'):$recorderModel->get('reports_to_id'));
                    $recorderModel2->set('invoicecompany',$request->get('invoicecompany')?$request->get('invoicecompany'):$recorderModel->get('invoicecompany'));
                    $recorderModel2->set('departmentid',$request->get('departmentid')?$request->get('departmentid'):$recorderModel->get('departmentid'));
                    $recorderModel2->set('roleid',$request->get('roleid')?$request->get('roleid'):$recorderModel->get('roleid'));
                    $recorderModel2->set('department',$request->get('department')?$request->get('department'):$recorderModel->get('department'));
                    $recorderModel2->set('title',$request->get('title')?$request->get('title'):$recorderModel->get('title'));
                    $recorderModel2->set('employeelevel',$request->get('employeelevel')?$request->get('employeelevel'):$recorderModel->get('employeelevel'));
                    $recorderModel2->set('companyid',$request->get('companyid')?$request->get('companyid'):$recorderModel->get('companyid'));
                    $recorderModel2->set('transfertype',$type?$type:$recorderModel->get('transfertype'));
                    if($type=='barrack'){
                        $recorderModel2->set('barracksintime',$effectivetime?$effectivetime:$recorderModel->get('barracksintime'));
                    }else{
                        $recorderModel2->set('postintime',$effectivetime?$effectivetime:$recorderModel->get('postintime'));
                    }
                    $recorderModel2->set('mode','edit');
                    $recorderModel2->save();
                }else{
                    //当前未到调岗时间修改后台调岗时间值
                    $recorderModel->set('modulestatus','b_actioning');
                }

                //修改后台用户管理
                $recorderModel->set('id',$id);
                $recorderModel->set('reports_to_id',$request->get('reports_to_id')?$request->get('reports_to_id'):$recorderModel->get('reports_to_id'));
                $recorderModel->set('invoicecompany',$request->get('invoicecompany')?$request->get('invoicecompany'):$recorderModel->get('invoicecompany'));
                $recorderModel->set('departmentid',$request->get('departmentid')?$request->get('departmentid'):$recorderModel->get('departmentid'));
                $recorderModel->set('roleid',$request->get('roleid')?$request->get('roleid'):$recorderModel->get('roleid'));
                $recorderModel->set('department',$request->get('department')?$request->get('department'):$recorderModel->get('department'));
                $recorderModel->set('title',$request->get('title')?$request->get('title'):$recorderModel->get('title'));
                $recorderModel->set('employeelevel',$request->get('employeelevel')?$request->get('employeelevel'):$recorderModel->get('employeelevel'));
                $recorderModel->set('companyid',$request->get('companyid')?$request->get('companyid'):$recorderModel->get('companyid'));
                $recorderModel->set('transfertype',$type?$type:$recorderModel->get('transfertype'));
                if($type=='barrack'){
                    $recorderModel->set('barracksintime',$effectivetime?$effectivetime:$recorderModel->get('barracksintime'));
                }else{
                    $recorderModel->set('postintime',$effectivetime?$effectivetime:$recorderModel->get('postintime'));
                }
                $recorderModel->set('mode','edit');
                $recorderModel->save();
            }
            $response = new Vtiger_Response();
            $response->setResult(array('success'=>true,'msg'=>'执行成功'));
            $response->emit();
        }catch (Exception $e){
            $response = new Vtiger_Response();
            $response->setResult(array('success'=>false,'msg'=>$e->getMessage()));
            $response->emit();
        }
    }

    public function checkStaffType(Vtiger_Request $request){
        $record = $request->get('record');
        $stafftype = $request->get('stafftype');
        $graduatetime =$request->get('graduatetime');
        $recorderModel = Vtiger_Record_Model::getInstanceById($record, 'UserManger');
        if($recorderModel->get('stafftype') != $stafftype && $recorderModel->get('graduatetime') == $graduatetime){
            $response = new Vtiger_Response();
            $response->setResult(array('success'=>false,'msg'=>'员工类型已发生变化，确认已更新实际毕业日期?'));
            $response->emit();
            exit;
        }
        $response = new Vtiger_Response();
        $response->setResult(array('success'=>true,'msg'=>'成功'));
        $response->emit();
        exit;

    }
        /**
     * 同步云课堂
     */
    public function updateYXTInfo(Vtiger_Request $request){
        $record=$request->get('record');
        $recordModel=Vtiger_Record_Model::getInstanceById($record,'UserManger');
        $data['userName']=$recordModel->get('user_name');
        $data['cnName']=$recordModel->get('last_name');
        $data['mail']=$recordModel->get('email1');
        $data['mobile']=$recordModel->get('mobile');
        $data['id']=md5($recordModel->get('userid'));
        $data['orgOuCode']=trim($recordModel->get('departmentid'),'H');
        $data['isEmailValidated']=0;
        $data['isMobileValidated']=0;
        $datas=array('dataAction'=>'ous');
        $datas['datas']=array('datas'=>array($data),"islink"=>1);
        $yxTangToke=$recordModel->yxtangtoke();
        $yxTangData=array_merge($yxTangToke,$datas['datas']);
        $headers[]='Content-Type: application/json';
        $returnData=$recordModel->https_requestcomm2("https://api-qidac1.yunxuetang.cn/v1/udp/sy/users",$yxTangData,$headers,true);
        $josn_data=json_decode($returnData[0],true);
        if($josn_data['result']==1){
            $data=array('falg'=>true,"msg"=>$josn_data['msg']);
        }else{
            $data=array('falg'=>false,"msg"=>$josn_data['msg']);
        }
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
    //同步部门到云课堂
    public function offlineAddDepartmentid(){
        return;
        global $adb;
        $query='SELECT * FROM `vtiger_departmentsonline` where depth>0 ORDER BY depth';
        $result=$adb->pquery($query,array());
        $recordModel=Vtiger_Record_Model::getCleanInstance('UserManger');
        echo '<pre>';
        while($row=$adb->fetch_array($result)){
            $parentdepartmentArray=explode("::",$row['parentdepartment']);
            array_pop($parentdepartmentArray);
            $parentdepartmentid=trim(end($parentdepartmentArray),"H");
            $departmentid=trim($row['departmentid'],"H");
            $departmentname=$row['departmentname'];
            $mqdata=array('dataAction'=>'ous','datas'=>array('datas'=>array(array("id"=>trim($departmentid,'H'),"ouName"=>$departmentname,"parentId"=>trim($parentdepartmentid,'H')))));
            $returnData=$recordModel->sendYxtangByMessageQuery($mqdata);
        }
    }

    /**
     * 同步用户信息
     * @return bool
     */
    public function offlineAddUser(){
        return false;
        set_time_limit(0);
        global $adb;
        $query='SELECT vtiger_usersonline.*,vtiger_user2departmentonline.* FROM vtiger_usersonline LEFT JOIN vtiger_user2departmentonline ON vtiger_user2departmentonline.userid=vtiger_usersonline.id WHERE id>1 AND vtiger_usersonline.`status`=\'Active\'';
        $result=$adb->pquery($query,array());
        $recordModel=Vtiger_Record_Model::getCleanInstance('UserManger');
        echo '<pre>';
        while($row=$adb->fetch_array($result)){
            $mqdata=array('dataAction'=>'users','datas'=>array('datas'=>array(array(
                'userName'=>$row['user_name'],
                'cnName'=>$row['last_name'],
                'orgOuCode'=>trim($row['departmentid'],'H'),
                'mail'=>$row['email1'],
                'mobile'=>$row['phone_mobile'],
                'id'=>md5($row['id']),
                'isEmailValidated'=>0,
                'isMobileValidated'=>0,
            ))));
            $returnData=$recordModel->sendYxtangByMessageQuery($mqdata);
        }
    }
}
