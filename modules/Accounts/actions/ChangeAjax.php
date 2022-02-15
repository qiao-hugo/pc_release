<?php
class Accounts_ChangeAjax_Action extends Vtiger_Action_Controller {
    function __construct() {
        parent::__construct();
        $this->exposeMethod('setAdvancesmoney');
        $this->exposeMethod('getAccountReportList');
        $this->exposeMethod('getSuangtuData');
        $this->exposeMethod('getAccountsBySmownerid');
        $this->exposeMethod('transferAccount');
        $this->exposeMethod('showRecentComments');
        $this->exposeMethod('getUsers');
    }

    public function setAdvancesmoney(Vtiger_Request $request) {

        $record = $request->get('record');
        $status = $request->get('status');
        $old_advancesmoney = $request->get('old_advancesmoney');
        do {
            // 权限判断
            if(! Users_Privileges_Model::isPermitted('Accounts', 'ConvertLead')) {
                break;
            }
            if (! is_numeric($status)) {
                break;
            }



            global  $current_user;
            $db = PearDatabase::getInstance();
            $sql = "UPDATE vtiger_account SET advancesmoney=? WHERE accountid=? LIMIT 1";
            $db->pquery($sql, array($status, $record));

            // 做更新记录
            $id = $db->getUniqueId('vtiger_modtracker_basic');
            $db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                            array($id, $record, 'Accounts', $current_user->id, date('Y-m-d H:i:s'), 0));
            $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                Array($id, 'advancesmoney', $old_advancesmoney, $status));
        } while (0);

        $data = array();
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
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

		$recordId = $request->get('record');
		if(empty($recordId)){
			exit;
		}
		$type=$request->get('type');
		//自定义查询条件
		/*VTCacheUtils::updateFieldInfo(6, 'protected', 1, 'protected', 'protected', 'vtiger_account', 2, 'V~M', 0);
		VTCacheUtils::updateFieldInfo(6, 'accountrank',2 ,'accountrank','accountrank','vtiger_account', 2, 'V~M', 0);
		VTCacheUtils::updateFieldInfo(6, 'accountcategory',3,'accountcategory','accountcategory','vtiger_account', 2, 'V~M', 0);
		VTCacheUtils::updateFieldInfo(6, 'smownerid',4,'smownerid','smownerid','vtiger_crmentity', 2, 'V~M', 0);
		*/
		//数据权限与列表一致
		vglobal('currentView','List');
        $db=PearDatabase::getInstance();
        //防止并发
        $lockresult=$db->pquery("SELECT 1 FROM vtiger_lockaccountid WHERE lockaccountid=?",array($recordId));
		if($db->num_rows($lockresult)>0){
            $result1 = array('success'=>false,'message'=>'客户已锁定,其他人正在操作！');
            echo json_encode($result1);
            exit;
        }
        $db->pquery("INSERT INTO vtiger_lockaccountid(lockaccountid) VALUES (?)",array($recordId));
		$recordModel = Vtiger_Record_Model::getInstanceById($recordId, 'Accounts');
		$moduleModel = $recordModel->getModule();
		$entity=$recordModel->entity->column_fields;
		//受保护的客户
		global  $current_user;

        //对权限控制，打的什么标签释放调入公海的客户只能什么标签的员工领取，其他部门可见但不可领取，
        $accountlabeling = $entity['accountlabeling'];
        if($accountlabeling=='qudaobu' && ( $type=='TEMPORARY' && $entity['accountcategory']==2 || $type=='SELF' && $entity['accountcategory']!=0)){
            $userRecordModel = Users_Record_Model::getCleanInstance("Users");
            $result = $db->pquery("select departmentid from vtiger_user2department where userid=?",array($current_user->id));
            $row = $db->fetchByAssoc($result,0);
            if(!$userRecordModel->isChannelUser($row['departmentid'])){
                $db->pquery("DELETE FROM vtiger_lockaccountid WHERE lockaccountid=?",array($recordId));
                $result1 = array('success'=>false,'message'=>'非渠道部员工不可领取渠道客户');
                echo json_encode($result1);
                exit();
            }
        }

		$result1 = array('success'=>true);
		if($entity['protected']==1){
			if($type=='UNPROTECTED'/* &&  !empty($current_user->viewPermission['Accounts/Protect'])*/){
			    if($recordModel->getProtectnum()>0){
                    $recordModel->updaterecord(array('protected=0'),$recordId);
                    $db=PearDatabase::getInstance();
                    $sql='DELETE FROM vtiger_accountprotect WHERE accountid=?';
                    $db->pquery($sql,array($recordId));
                    $array[0]=array('fieldname'=>'accountcategory','prevalue'=>'已保护', 'postvalue'=>'取消保护');
                    $this->setModTracker($recordId,$array);
                    $result1 = array('success' => true, 'message' => '');
                }else{
                    $result1 = array('success' => false, 'message' => '请联系管理员设置权限！');
                }
			}
		}else{
			if($type=='OVERT' && $entity['accountcategory']!=2 ){
				//自己或下属的客户放到公海，客户降级到机会客户，取消未成交销售机会
                //2015-03-11更改为放入公海不再判断客户等级
                //$recordModel->updaterecord(array('accountcategory=2'),$recordId);
                $datetime=date('Y-m-d H:i:s');

                $db->pquery("REPLACE INTO `vtiger_accountgonghaiinrel`(accountid,userid,smownerid,createdtime) VALUES(?,?,?,?)",array($recordId,$current_user->id,$entity['assigned_user_id'],$datetime));
                $id = $db->getUniqueId('vtiger_modtracker_basic');
                $db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                    array($id , $recordId, 'Accounts', $current_user->id, $datetime, 0));
                $accountcategory=$entity['accountcategory']==0?'正常 扔':($entity['accountcategory']==1?'临时区 扔':'');
                $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                    Array($id, 'accountcategory',$accountcategory, 2));
				if(strstr($entity['accountrank'],'_isv')){
					//$result1 = array('success'=>false,'message'=>'已成交客户不能转入公海！');
                    //2015-06-03 steel 更新规则为已成交客户丢公海不掉等级
                    //追加更新掉入公海时间 gaocl edit 2018/02/28
                    //$recordModel->updaterecord(array('accountcategory=2'),$recordId);

                    if($entity['accountcategory'] == 1){
                        $recordModel->updaterecord(array('accountcategory=2','fall_toovert_time=NOW()',"intentionality='zeropercentage'"),$recordId);
                    }else{
                        $recordModel->updaterecord(array('accountcategory=2','fall_toovert_time=NULL',"intentionality='zeropercentage'"),$recordId);
                    }
				}else{
                    //2015-06-03 steel 更新规则为未成交客户丢公海等级变为机会客户
                    //追加更新掉入公海时间 gaocl edit 2018/02/28
					//$recordModel->updaterecord(array('accountcategory=2',"accountrank='chan_notv'"),$recordId);
                    if($entity['accountcategory'] == 1){
                        $query="SELECT 1 FROM vtiger_user2department LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid = vtiger_departments.departmentid
                                WHERE vtiger_user2department.userid=? AND CONCAT(vtiger_departments.parentdepartment,'::') LIKE '%H3::%' limit 1";
                        if($db->num_rows($db->pquery($query,array($recordModel->get('assigned_user_id'))))){//中小客户
                            $fall_toovert_time='fall_toovert_time='.time();
                        }else{
                            $fall_toovert_time='fall_toovert_time=NULL';
                        }
                        $recordModel->updaterecord(array('accountcategory=2',"accountrank='chan_notv'",$fall_toovert_time,"intentionality='zeropercentage'"),$recordId);
                    }else{
                        $recordModel->updaterecord(array('accountcategory=2',"accountrank='chan_notv'","fall_toovert_time=NULL","intentionality='zeropercentage'"),$recordId);
                    }
				}
			}elseif ($type=='TEMPORARY' && $entity['accountcategory']==2){ //公海捡入临时区的
                /*$accountnum=$recordModel->getRankCounts(array($entity['accountrank'],1,(int)$current_user->id));
                //加入保护客户数量
                if(5>$accountnum) {
                    $recordModel->updaterecord(array('accountcategory=1', 'protectday=3'), $recordId, $current_user->id);
                }else{
                    $result1 = array('success'=>false,'message'=>'LBL_NOPROTECT');
                }*/
                if($entity['assigned_user_id']!=$current_user->id){
                    $db = PearDatabase::getInstance();
                    $resultss=$db->pquery('SELECT highseasaccountnumid FROM `vtiger_highseasaccountnum` WHERE userid=? AND createdtime=?',array($current_user->id,date('Y-m-d')));

                    if($db->num_rows($resultss)<1000) {
                        //临时区最大客户数量调整为300个
                        $result_tmp =$db->pquery('SELECT 1 FROM vtiger_account a 
                                                 JOIN vtiger_crmentity  b ON(a.accountid=b.crmid)
                                                WHERE a.accountcategory=1 AND b.deleted=0 AND b.smownerid=? ',array($current_user->id));
                        $cnt_tmp = $db->num_rows($result_tmp);
                        if($cnt_tmp > 300) {
                            $result1 = array('success'=>false,'message'=>'临时区最大客户数量不能超过300个('.$cnt_tmp.'个)');
                        }else{
                            //客户从临时区掉公海后一段时间（5天）内不允许领取(主要是防止商务频繁给客户打电话) gaocl 2018/02/28 add
                            $days = $recordModel->getFallToovertDays($recordId);
                            $query="SELECT 1 FROM vtiger_user2department LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid = vtiger_departments.departmentid
                                WHERE vtiger_user2department.userid=? AND CONCAT(vtiger_departments.parentdepartment,'::') LIKE '%H3::%' limit 1";
                            $dataflag=false;
                            if($db->num_rows($db->pquery($query,array($current_user->id)))){
                                $dataflag=true;
                            }
                            if($days>=0 && $days <= 5 && $dataflag) {
                                $result1 = array('success' => false, 'message' => '客户从临时区掉公海后5天内不允许领取！');
                            }else{
                                $sql="SELECT r.relatetoid FROM vtiger_receivedpayments as r INNER JOIN  vtiger_servicecontracts as s ON s.servicecontractsid=r.relatetoid WHERE s.sc_related_to=?";
                                $results=$db->pquery($sql,array($recordId));
                                $numrowscount=$db->num_rows($results);
                                // 如果是铁牌、银牌、铜牌、金牌、VIP 不可被领取到临时区
                                if($numrowscount>0 && in_array($entity['accountrank'],array('iron_isv','bras_isv','silv_isv','gold_isv','visp_isv'))){
                                    $result1 = array('success' => false, 'message' => vtranslate($entity['accountrank'])."客户不可以被领取到临时区。");
                                }else {
                                    $salerank = $recordModel->getSaleRank($current_user->id);
                                    $userinfo =$db->pquery("SELECT u.user_entered,ud.departmentid FROM vtiger_users as u LEFT JOIN vtiger_user2department as ud ON ud.userid=u.id WHERE id = ?", array($current_user->id));
                                    $departmentid = $db->query_result($userinfo, 0,'departmentid');
                                    $user_entered = $db->query_result($userinfo, 0,'user_entered');
                                    $result = $recordModel->getRankDays(array($salerank, $entity['accountrank'],$departmentid,$user_entered));
                                    $datetime = date('Y-m-d H:i:s');
                                    $id = $db->getUniqueId('vtiger_modtracker_basic');
                                    $db->pquery('INSERT vtiger_highseasaccountnum(userid,createdtime,accountid) VALUES(?,?,?)', array($current_user->id, date('Y-m-d'), $recordId));
                                    $db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                                        array($id, $recordId, 'Accounts', $current_user->id, $datetime, 0));
                                    $accountcategory = '公海 捡';
                                    $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                                        Array($id, 'accountcategory', $accountcategory, 1));
                                    $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                                        Array($id, 'assigned_user_id', $entity['assigned_user_id'], $current_user->id));
                                    $recordModel->updaterecord(array('accountcategory=1', "accountrank=(IF(accountrank='forp_notv','chan_notv',accountrank ))", 'protectday=3', 'effectivedays=' . $result['protectday']), $recordId, $current_user->id);
                                }
                            }
                        }
                    }else{
                        $result1 = array('success'=>false,'message'=>'今天你已领取了1000次客户,明天再来吧!');
                    }
                }else{
                    $result1 = array('success'=>false,'message'=>'不允许领取自已的客户');
                }
			}elseif ($type=='SELF' && $entity['accountcategory']!=0){
                $db=PearDatabase::getInstance();
				//领取客户限制(该等级客户保护数量和天数)
                $salerank=$recordModel->getSaleRank($current_user->id);
                $accountRankStr = vtranslate($entity['accountrank'],'RankProtect');
                $userinfo =$db->pquery("SELECT u.user_entered,ud.departmentid FROM vtiger_users as u LEFT JOIN vtiger_user2department as ud ON ud.userid=u.id WHERE id = ?", array($current_user->id));
                $departmentid = $db->query_result($userinfo, 0,'departmentid');
                $user_entered = $db->query_result($userinfo, 0,'user_entered');
                $result=$recordModel->getRankDays(array($salerank,$entity['accountrank'],$departmentid,$user_entered));
                //已领取的客户总数
                $accountnum=$recordModel->getRankCounts(array($entity['accountrank'],0,(int)$current_user->id));
                if($result['protectnum']>$accountnum){
                    //当前客户不能领取自已在公海的客户steel 2016-02-24
                    if($entity['assigned_user_id']!=$current_user->id || $entity['accountcategory']==1 || !in_array($entity['accountrank'],array('chan_notv','forp_notv','eigp_notv','sixp_notv'))){

                        if($entity['accountcategory']==2){

                            $resultss=$db->pquery('SELECT highseasaccountnumid FROM `vtiger_highseasaccountnum` WHERE userid=? AND createdtime=?',array($current_user->id,date('Y-m-d')));
                            if($db->num_rows($resultss)<100){
                                //客户从临时区掉公海后一段时间（5天）内不允许领取(主要是防止商务频繁给客户打电话) gaocl 2018/02/28 add
                                $days = $recordModel->getFallToovertDays($recordId);
                                $query="SELECT 1 FROM vtiger_user2department LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid = vtiger_departments.departmentid
                                WHERE vtiger_user2department.userid=? AND CONCAT(vtiger_departments.parentdepartment,'::') LIKE '%H3::%' limit 1";
                                $dataflag=false;
                                if($db->num_rows($db->pquery($query,array($current_user->id)))){
                                    $dataflag=true;
                                }
                                if($days>=0 && $days <= 5 && $dataflag){
                                    $result1 = array('success'=>false,'message'=>'客户从临时区掉公海后5天内不允许领取！');
                                }else{
                                    $db->pquery('INSERT vtiger_highseasaccountnum(userid,createdtime,accountid) VALUES(?,?,?)',array($current_user->id,date('Y-m-d'),$recordId));
                                    $datetime=date('Y-m-d H:i:s');
                                    $db->pquery("REPLACE INTO `vtiger_accountgonghaioutrel`( accountid,userid,createdtime) VALUES(?,?,?)",array($recordId,$current_user->id,$datetime));
                                    $id= $db->getUniqueId('vtiger_modtracker_basic');
                                    $db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                                        array($id , $recordId, 'Accounts', $current_user->id, $datetime, 0));
                                    $accountcategory='公海 捡';
                                    $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                                        Array($id, 'accountcategory',$accountcategory, 0));
                                    $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                                        Array($id, 'assigned_user_id',$entity['assigned_user_id'], $current_user->id));

                                    $recordModel->updaterecord(array('accountcategory=0',"accountrank=(IF(accountrank='forp_notv','chan_notv',accountrank ))", 'protectday=' . $result['protectday'],'followday=' . $result['followday'],'effectivedays='.$result['protectday']), $recordId, $current_user->id,true);

                                }
                            }else{
                                $result1 = array('success'=>false,'message'=>'今天你已领取了100次客户,明天再来吧!');
                            }
                        }elseif($entity['accountcategory']==1 && $entity['assigned_user_id']==$current_user->id){
                            //临时区领的客户只能自已领自已作用可以避免两人同一时间打开公海,一个先领入领时区另一个后领入正常
                            $recordModel->updaterecord(array('accountcategory=0',"accountrank=(IF(accountrank='forp_notv','chan_notv',accountrank ))",'protectday=effectivedays'),$recordId,$current_user->id,true);
                            $array[0]=array('fieldname'=>'accountcategory','prevalue'=>'临时区 领', 'postvalue'=>0);
                            $this->setModTracker($recordId,$array);
                        }else{
                            $result1 = array('success'=>false,'message'=>'该客户已经被领取了');
                        }
                    }else{
                        $result1 = array('success'=>false,'message'=>'不允许领取自已的客户');
                    }
                }else{
		    // cxh 客保数量不足日志记录 start
                    $paramers['contract_no']="公海领取客户客保数量超记录";
                    $marks=json_encode($_REQUEST);
                    $id=$current_user->id;
                    $paramers['marks']="userid".$id."已拥有账号数量".$accountnum.$marks;
                    $Matchreceivements_Record_Model=Vtiger_Record_Model::getCleanInstance("Matchreceivements");
                    $Matchreceivements_Record_Model->noCalculationAchievementRecord($paramers);
		    // end
                    $result1 = array('success'=>false,'message'=>$accountRankStr.'等级客户保护数量'.$result['protectnum'].'个，您当前已有'.$accountRankStr.'等级客户'.$accountnum.'个，已达保护数量，不可领取该等级客户。');
                }



				//修改归属人为自己
				//$recordModel->updaterecord('accountcategory',1,$recordId);
				//$value=0;
				//$value1=$current_user;
			}elseif($type=='PROTECTED'/* && !empty($current_user->viewPermission['Accounts/Protect'])*/){
                $db=PearDatabase::getInstance();
                $query="SELECT protectnum FROM vtiger_protectsetting WHERE userid=? limit 1";
                $result=$db->pquery($query,array($current_user->id));
                if($db->num_rows($result)){
                    $rowData=$db->raw_query_result_rowdata($result);
                    $query="SELECT 1 FROM vtiger_accountprotect WHERE userid=?";
                    $resultnum=$db->pquery($query,array($current_user->id));
                    if($db->num_rows($resultnum)<$rowData['protectnum']) {
                        $recordModel->updaterecord(array('protected=1'), $recordId);
                        $sql="INSERT INTO vtiger_accountprotect(accountid,userid) VALUES(?,?)";
                        $db->pquery($sql,array($recordId,$current_user->id));
                        $array[0]=array('fieldname'=>'accountcategory','prevalue'=>'未保护', 'postvalue'=>'已保护');
                        $this->setModTracker($recordId,$array);
                    }else{
                        $result1 = array('success'=>false,'message'=>'您当前只能保护'.$rowData['protectnum'].'个客户！已超过最大限度');
                    }
                }else{
                    $result1 = array('success' => false, 'message' => '请联系管理员设置权限！');
                }
			}elseif($type=='NOSIGN' && $entity['assigned_user_id']==$current_user->id){
                $recordModel->updaterecord(array('sign=0'),$recordId);
            }elseif($type=='SIGN'&& $entity['assigned_user_id']==$current_user->id){
                if($recordModel->getsignflag(array($current_user->id))<15){
                    $recordModel->updaterecord(array('sign=1'), $recordId);
                }else{
                    $result1 = array('success'=>false,'message'=>'只能打15个标记！');
                }
            }else{
				$result1 = array('success'=>false,'message'=>'错误的操作！');
			}


		}
        $db->pquery("DELETE FROM vtiger_lockaccountid WHERE lockaccountid=?",array($recordId));
		echo json_encode($result1);





	}
	public function setModTracker($recordId,$array){
        $db=PearDatabase::getInstance();
        global $current_user;
        $datetime=date('Y-m-d H:i:s');
        $id= $db->getUniqueId('vtiger_modtracker_basic');
        $db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
            array($id , $recordId, 'Accounts', $current_user->id, $datetime, 0));
        foreach($array as $value){
            $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                Array($id, $value['fieldname'],$value['prevalue'], $value['postvalue']));
        }
    }
    /**
     * 列表上新增报表链接获取用户的客户数量
     * @param Vtiger_Request $request
     */
    public function getAccountReportList(Vtiger_Request $request){
        $db=PearDatabase::getInstance();
        $dataClass=$request->get("dataClass");
        $userid=$request->get("userid");
        $datatype=$request->get("datatype");
        global $current_user;
        $datetime=date('Y-m-d');
        $query=$dataClass=='dayadd'?" AND TO_DAYS(createdtime)=TO_DAYS('{$datetime}')":$dataClass=='weekadd'?" AND yearweek(date_format(vtiger_crmentity.createdtime,'%Y-%m-%d'))=yearweek('{$datetime}')":" AND date_format(vtiger_crmentity.createdtime,'%Y-%m')=date_format('{$datetime}','%Y-%m')";
        $query.=" AND accountrank='{$datatype}' AND vtiger_crmentity.smownerid=".$userid;
        $listQuery="SELECT vtiger_account.accountname,vtiger_account.accountid FROM vtiger_account LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_account.accountid WHERE vtiger_crmentity.deleted=0".$query;
        $result=$db->pquery($listQuery,array());
        $data=array();
        while($row=$db->fetch_array($result)){
            $data[]=$row;
        }
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
    public function getSuangtuData(Vtiger_Request $request){
        global $adb;
        /*print_r($request);
        exit;*/
        $value=$request->get('value');
        if(empty($value)){
            $return=array(
              "flag"=>false,
              "msg"=>"无效输入"
            );
        }else{
            $query='SELECT * FROM `vtiger_staccountname` where staccountname like ? LIMIT 5';
            $result=$adb->pquery($query,array('%'.$value.'%'));
            $num=$adb->num_rows($result);
            if($num){
                $array=array();
                while($row=$adb->fetch_array($result)){
                    $array[]=$row['staccountname'];
                }
                $return=array("flag"=> true,
                    "data"=>$array);
                echo json_encode($return);
                exit;
            }
            $return=array(
                "flag"=>false,
                "msg"=>"没有找到相关数据"
            );
            echo json_encode($return);
            exit;
        }

        echo json_encode($return);
        //echo json_encode(array('data'=>array(),'draw'=>2,'recordsFiltered'=>54,'recordsTotal'=>57));
    }

    public function getAccountsBySmownerid(Vtiger_Request $request){
        $db=PearDatabase::getInstance();
        $smownerid=$request->get("smownerid");
        $sql = "select a.accountname,a.accountid,accountrank from vtiger_account a left join vtiger_crmentity b on a.accountid=b.crmid where b.smownerid=? and setype='Accounts' and b.deleted=0 and a.accountcategory in(0,1)";
        $result = $db->pquery($sql,array($smownerid));
        if($db->num_rows($result)){
            while ($row = $db->fetchByAssoc($result)){
                $row['accountrank'] = vtranslate($row['accountrank'],'Accounts','zh_cn');
                $data[] = $row;
            }
        }
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
    public function transferAccount(Vtiger_Request $request) {
        global $serivce_crm_url;
        $url=$serivce_crm_url.'/sys/customer-syncErpCustomer.json';
        $record = $request->get('record');
        $return=array('flag'=>false,'msg'=>'错误的ID');
        do {
            // 权限判断
            if (! is_numeric($record)) {
                break;
            }
            global  $current_user,$adb;
            $query='SELECT * FROM vtiger_account WHERE accountid=? limit 1';
            $result=$adb->pquery($query, array($record));
            $arry=array();
            $row=$adb->fetchByAssoc($result);
            $row['accountcategory']=$row['accountcategory']==0?'正常':($row['accountcategory']==1?'临时区':'公海');
            $query='SELECT columnname,uitype FROM vtiger_field WHERE tabid=6 AND uitype in(15,16,56,151)';
            $result=$adb->pquery($query, array());
            $fieldrow=array();
            while($trow=$adb->fetchByAssoc($result)){
                if(in_array($trow['uitype'],array(15,16,151))){
                    $fieldrow[15][]=$trow['columnname'];
                }else{
                    $fieldrow[56][]=$trow['columnname'];
                }
            }
            $customer=array();
            foreach($row as $key=>$value){
                if(in_array($key,$fieldrow[15])){
                    $customer[$key]=vtranslate($value,'Accounts');
                }elseif(in_array($key,$fieldrow[56])){
                    $customer[$key]=$value==1?'是':'否';
                }else{
                    $customer[$key]=$value;
                }
            }
            $arry['customer']=$customer;
            $arry['userid']=$current_user->id;
            $arry['realname']=$current_user->last_name;
            $recordModel=Vtiger_Record_Model::getCleanInstance('Accounts');
            $returnData=$recordModel->https_requestcomm($url,http_build_query($arry),null,true);
            $josnData=json_decode($returnData,true);
            if($josnData['result']=='success'){
                $SQL="UPDATE vtiger_account SET cservicetransfer='ctransfer' WHERE accountid=?";
                $adb->pquery($SQL,array($record));
                $return=array('flag'=>true,'msg'=>$josnData['message']);
            }else{
                $return=array('flag'=>false,'msg'=>$josnData['message']);
            }
        } while (0);
        $response = new Vtiger_Response();
        $response->setResult($return);
        $response->emit();
    }

    public function getUsers($request){
        $users = ReceivedPayments_Record_Model::getuserinfo(" AND `status`='Active'");
        $response = new Vtiger_Response();
        $response->setResult(array('success'=>true,'data'=>$users));
        $response->emit();
    }
}
