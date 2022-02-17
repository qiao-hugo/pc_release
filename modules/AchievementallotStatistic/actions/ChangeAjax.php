<?php
class AchievementallotStatistic_ChangeAjax_Action extends Vtiger_Action_Controller {
    function __construct() {
        parent::__construct();
        $this->exposeMethod('confirmEnd');
        $this->exposeMethod('cancelConfirmEnd');
        $this->exposeMethod('applicationUpdateAchievement');
        $this->exposeMethod('withHoldAchievement');
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
    /**
     * 申请调整业绩金额
     */
    public function applicationUpdateAchievement(Vtiger_Request $request){
        global $adb;
        $adjustachievement=$request->get("adjustachievement");
        $remarks=$request->get("remarks");
        $record=$request->get("record");
        do{
            $sql=" SELECT * FROM  vtiger_achievementallot_statistic WHERE achievementallotid=? LIMIT 1 ";
            $detailInfo = $adb->pquery($sql,array($record));
            $result=$adb->query_result_rowdata($detailInfo,0);
            if($result['modulestatus']=='b_actioning'){
                $result=array('success'=>0,'message'=>"状态审核中不能调整业绩！");
                break;
            }
            if($result['isover']==1 || $result['status']==1){
                $result=array('success'=>0,'message'=>"已完结或者提成已计算不能调整业绩！");
                break;
            }
            if($result['arriveachievement']<$adjustachievement){
                $result=array('success'=>0,'message'=>"实际到账业绩金额不能小于业绩调整金额！");
                break;
            }
            if($result['status']==1){
                $result=array('success'=>0,'message'=>"已核算提成的不能调整到账业绩！");
                break;
            }
            //更新日志记录
            $currentTime = date('Y-m-d H:i:s');
            global $current_user;
            //更新记录
            $id = $adb->getUniqueId('vtiger_modtracker_basic');
            $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                array($id, $record, 'AchievementallotStatistic', $current_user->id,$currentTime, 0));
            if(!$result['adjustachievement']) $result['adjustachievement']=0;
            $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?),(?,?,?,?),(?,?,?,?)',
                Array($id,'adjustachievement',$result['adjustachievement'],$adjustachievement+$result['adjustachievement'],$id,'modulestatus','','c_complete',$id,'adjustremarks','',$remarks));
            $adb->pquery('UPDATE `vtiger_achievementallot_statistic` SET `adjustachievement`=adjustachievement+?, arriveachievement=arriveachievement-?,adjustremarks=? WHERE (`achievementallotid`=?) LIMIT 1',
                array($adjustachievement,$adjustachievement,$remarks,$record));
            if($result['producttype']==3){//如果是否非示的有效回款=到账业绩
                $adb->pquery('UPDATE `vtiger_achievementallot_statistic` SET `effectiverefund`=arriveachievement WHERE (`achievementallotid`=?) LIMIT 1',
                    array($record));
            }
            $adb->pquery("UPDATE vtiger_achievementsummary SET 
                        vtiger_achievementsummary.unit_price =(SELECT sum(vtiger_achievementallot_statistic.unit_price) FROM vtiger_achievementallot_statistic WHERE vtiger_achievementallot_statistic.achievementtype = vtiger_achievementsummary.achievementtype AND vtiger_achievementallot_statistic.achievementmonth =vtiger_achievementsummary.achievementmonth AND vtiger_achievementallot_statistic.receivedpaymentownid =vtiger_achievementsummary.userid AND vtiger_achievementallot_statistic.isleave=0)
                        ,vtiger_achievementsummary.effectiverefund=(SELECT sum(vtiger_achievementallot_statistic.effectiverefund) FROM vtiger_achievementallot_statistic WHERE vtiger_achievementallot_statistic.achievementtype = vtiger_achievementsummary.achievementtype AND vtiger_achievementallot_statistic.achievementmonth =vtiger_achievementsummary.achievementmonth AND vtiger_achievementallot_statistic.receivedpaymentownid =vtiger_achievementsummary.userid  AND vtiger_achievementallot_statistic.isleave=0)
                        ,vtiger_achievementsummary.realarriveachievement=(SELECT sum(vtiger_achievementallot_statistic.arriveachievement)FROM vtiger_achievementallot_statistic WHERE vtiger_achievementallot_statistic.achievementtype = vtiger_achievementsummary.achievementtype AND vtiger_achievementallot_statistic.achievementmonth =vtiger_achievementsummary.achievementmonth AND vtiger_achievementallot_statistic.receivedpaymentownid =vtiger_achievementsummary.userid AND vtiger_achievementallot_statistic.isleave=0)
                        WHERE vtiger_achievementsummary.achievementmonth=? AND vtiger_achievementsummary.achievementtype=? and userid=?",
                array($result['achievementmonth'],$result['achievementtype'],$result['receivedpaymentownid']));
            $adb->pquery('delete from vtiger_withholdroyalty where achievementallotid in(?)',array($record));//有确认暂扣发放的业绩删掉
            $preMonth=date('Y-m',strtotime('-1 months'));//当前月份-个月
            if($preMonth==$result['achievementmonth']){//下月调只能调上个月，跨多月或当月调当月的不用生成脚本
                $achievementSummary_record_model=Vtiger_Record_Model::getCleanInstance('AchievementSummary');
                $query='SELECT 1 FROM vtiger_usergraderoyalty WHERE userid=? AND staffrank=0';
                $usergraderoyaltyresut=$adb->pquery($query,array($result['receivedpaymentownid']));
                if($adb->num_rows($usergraderoyaltyresut)){//如果是商务
                    ob_start();//重新计算员工的提成
                    $achievementSummary_record_model->calulateEmployee(array('userid'=>$result['receivedpaymentownid'],'achievementmonth'=>$result['achievementmonth']));
                    $info="\n".$result['receivedpaymentownid'].'员工的提成start'.$result['achievementmonth']."rebuild\n";
                    $info.=ob_get_contents();
                    $info.="\n".$result['receivedpaymentownid'].'员工的提成end'.$result['achievementmonth']."rebuild\n";
                    $achievementSummary_record_model->comm_logs($info,'rebuildachievementallot');
                    ob_end_clean();
                }
                ob_start();//重新计算经理的提成
                $query='SELECT DISTINCT userid FROM vtiger_useractivemonthnew WHERE subordinateid=? AND activedate=? AND `status`=0';
                $resultData=$adb->pquery($query,array($result['receivedpaymentownid'],$result['achievementmonth']));
                $userids='';
                while($row=$adb->fetch_array($resultData)){
                    $userids.=$row['userid'].',';
                }
                $userids=trim($userids,',');
                $achievementSummary_record_model->calulateManager(array('userids'=>$userids,'achievementmonth'=>$result['achievementmonth']));
                $info=$userids.'员工上级的提成'.$result['achievementmonth']."rebuild\n";;
                $info.=ob_get_contents();
                $info.="\n".$userids.'员工上级的提成end'.$result['achievementmonth']."rebuild\n";
                $achievementSummary_record_model->comm_logs($info,'rebuildachievementallot');
                ob_end_clean();
                ob_start();
                $achievementSummary_record_model->noAchievementRoyalty(array(
                    'calculation_year_month'=>$result['achievementmonth'],
                    'calculation_month'=>end(explode('-',$result['achievementmonth'])),
                    'calculation_year'=>current(explode('-',$result['achievementmonth'])),
                    'assigner'=>$userids
                ));
                $info=$userids.'汇总的提成'.$result['achievementmonth']."rebuild\n";;
                $info.=ob_get_contents();
                $info.="\n".$userids.'员工上级的提成end'.$result['achievementmonth']."rebuild\n";
                $achievementSummary_record_model->comm_logs($info,'rebuildachievementallot');
                ob_end_clean();
                $achievementSummary_record_model->updateUserDepartment($result['achievementmonth']);
            }
            $result=array('success'=>1,'message'=>"修改成功",'data'=>array('adjustachievement'=>$adjustachievement,'adjustremarks'=>$remarks));

        }while(false);
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }

    /**
     * 调整业绩月份
     * @param Vtiger_Request $request
     */
    public function updateAchieveMentmonth(Vtiger_Request $request){
        global $adb;
        $achievementmonth=$request->get("achievementmonth");
        $remarks=$request->get("remarks");
        $record=$request->get("record");
        do{
            $sql=" SELECT * FROM  vtiger_achievementallot_statistic WHERE achievementallotid=? LIMIT 1 ";
            $detailInfo = $adb->pquery($sql,array($record));
            $result=$adb->query_result_rowdata($detailInfo,0);
            if($result['modulestatus']=='b_actioning'){
                $result=array('success'=>0,'message'=>"状态审核中不能调整业绩！");
                break;
            }
            if($result['isover']==1 || $result['status']==1){
                $result=array('success'=>0,'message'=>"已完结或者提成已计算不能调整业绩！");
                break;
            }
            if($result['achievementmonth']==$achievementmonth){
                $result=array('success'=>0,'message'=>"相同的业绩月份没有必要调整！");
                break;
            }
            if($result['status']==1){
                $result=array('success'=>0,'message'=>"已核算提成的不能调整到账业绩！");
                break;
            }
            //更新日志记录
            $currentTime = date('Y-m-d H:i:s');
            global $current_user;
            //更新记录
            $adjustachievement=$result['arriveachievement'];
            $adb->pquery('UPDATE `vtiger_achievementallot_statistic` SET `achievementmonth`=?,adjustremarks=? WHERE (`achievementallotid`=?) LIMIT 1',
                array($achievementmonth,$remarks,$record));
            if(0==$result['isleave']){
                $adb->pquery('UPDATE `vtiger_achievementsummary` SET realarriveachievement=realarriveachievement-? WHERE `userid`=? AND achievementmonth=? AND achievementtype=? LIMIT 1',
                    array($adjustachievement,$result['receivedpaymentownid'],$result['achievementmonth'],$result['achievementtype']));
            }


            $id = $adb->getUniqueId('vtiger_modtracker_basic');
            $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                array($id, $record, 'AchievementallotStatistic', $current_user->id,$currentTime, 0));
            if(!$result['adjustachievement']) $result['adjustachievement']=0;
            $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?),(?,?,?,?),(?,?,?,?)',
                Array($id,'achievementmonth',$result['achievementmonth'],$achievementmonth,$id,'modulestatus','','c_complete',$id,'adjustremarks','',$remarks));
            $result=array('success'=>1,'message'=>"修改成功",'data'=>array('adjustachievement'=>$adjustachievement,'adjustremarks'=>$remarks));

        }while(false);
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }

    /**
     * @param Vtiger_Request $request
     */
    public function confirmEnd(Vtiger_Request $request){
        global $adb;
        $module = $request->getModule();
        $achievementids = $request->get('achievementids');
        foreach ($achievementids as $achievementid){
            $recordModel = Vtiger_Record_Model::getInstanceById($achievementid,$module);
            $recordModel->set('achievementid',$achievementid);
            $recordModel->set('confirmstatus','confirmed');
            $recordModel->set('mode','edit');
            $recordModel->save();

            //完结详情表里面的内容
            $sql = "update vtiger_achievementallot_statistic set isover=? where receivedpaymentownid=? and achievementmonth=?";
            $adb->pquery($sql,array(1,$recordModel->get('userid'),$recordModel->get('achievementmonth')));
        }
        $response = new Vtiger_Response();
        $response->setResult(array(1));
        $response->emit();
    }

    public function cancelConfirmEnd(Vtiger_Request $request){
        global $adb;
        $module = $request->getModule();
        $achievementids = $request->get('achievementids');
        foreach ($achievementids as $achievementid){
            $recordModel = Vtiger_Record_Model::getInstanceById($achievementid,$module);
            $recordModel->set('achievementid',$achievementid);
            $recordModel->set('confirmstatus','tobeconfirm');
            $recordModel->set('mode','edit');
            $recordModel->save();
            //完结详情表里面的内容
            $sql = "update vtiger_achievementallot_statistic set isover=? where receivedpaymentownid=? and achievementmonth=?";
            $adb->pquery($sql,array(0,$recordModel->get('userid'),$recordModel->get('achievementmonth')));
        }
        $response = new Vtiger_Response();
        $response->setResult(array(1));
        $response->emit();
    }
    /**
     * 申请调整业绩金额
     */
    public function withHoldAchievement(Vtiger_Request $request){
        global $adb;
        $yearMonth=$request->get("yearMonth");
        $remarks=$request->get("remarks");
        $record=$request->get("record");
        do{
            $thisRecordModel=Vtiger_Record_Model::getCleanInstance('AchievementallotStatistic');
            if(!$thisRecordModel->personalAuthority('AchievementallotStatistic','withhold')){
                $result=array('success'=>0,'message'=>"没有权限操作！");
                break;
            }
            $sql=" SELECT * FROM  vtiger_achievementallot_statistic WHERE achievementallotid=? LIMIT 1 ";
            $detailInfo = $adb->pquery($sql,array($record));
            if($detailInfo->fields['modulestatus']=='b_actioning'){
                $result=array('success'=>0,'message'=>"状态审核中不能调整业绩！");
                break;
            }
            if($detailInfo->fields['isover']==1 || $detailInfo->fields['status']==1){
                $result=array('success'=>0,'message'=>"已完结或者提成已计算不能调整业绩！");
                break;
            }

            if($detailInfo->fields['status']==1){
                $result=array('success'=>0,'message'=>"已核算提成的不能调整到账业绩！");
                break;
            }
            if(empty($remarks)){
                $result=array('success'=>0,'message'=>"备注必填！");
                break;
            }
            $is_deduction=$detailInfo->fields['is_deduction'];
            $updateyearmonth='';
            $updateyearmonthsql="`achievementmonth`=NULL,";
            if($is_deduction){
                if(empty($yearMonth)){
                    $result=array('success'=>0,'message'=>"请填写有效的业绩月份！");
                    break;
                }
                $is_deduction=0;
                $achievementmonth=$updateyearmonth=$yearMonth;
                $updateyearmonthsql="`achievementmonth`='".$yearMonth."',";
            }else{
                $is_deduction=1;
                $achievementmonth=$detailInfo->fields['achievementmonth'];
            }
            //更新日志记录
            $currentTime = date('Y-m-d H:i:s');
            global $current_user;
            //更新记录
            $id = $adb->getUniqueId('vtiger_modtracker_basic');
            $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                array($id, $record, 'AchievementallotStatistic', $current_user->id,$currentTime, 0));
            $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?),(?,?,?,?)',
                Array($id,'achievementmonth',$detailInfo->fields['achievementmonth'],$updateyearmonth,$id,'adjustremarks','',$remarks));
            $adb->pquery('UPDATE `vtiger_achievementallot_statistic` SET '.$updateyearmonthsql.'adjustremarks=?,is_deduction=? WHERE `achievementallotid`=? LIMIT 1',
                array($remarks,$is_deduction,$record));

            if($detailInfo->fields['achievementtype']=='newadd'){
                //修改排行榜
                $recordModel = Matchreceivements_Record_Model::getCleanInstance("Matchreceivements");
                $receivePaymentsRankingType = !$is_deduction ? 'add' : 'cancel';
                $recordModel->receivePaymentsRanking($detailInfo->fields['receivedpaymentownid'],$detailInfo->fields['receivedpaymentsid'],$achievementmonth,$receivePaymentsRankingType,false);
            }


            $isinsert=0;
            if($is_deduction==0){
                $query='SELECT 1 FROM vtiger_achievementsummary WHERE userid=? AND achievementmonth=? AND achievementtype=?';
                if(!$adb->num_rows($adb->pquery($query,array($detailInfo->fields['receivedpaymentownid'],$achievementmonth,$detailInfo->fields['achievementtype'])))){
                    $isinsert=1;
                }
            }
            if($isinsert==1){
                $recordModel=Vtiger_Record_Model::getCleanInstance('AchievementSummary');
                $recordModel->newAchievementSummary(array($record));
            }else {
                $adb->pquery('UPDATE vtiger_achievementsummary SET 
                            vtiger_achievementsummary.unit_price =(SELECT sum(vtiger_achievementallot_statistic.unit_price) FROM	vtiger_achievementallot_statistic	WHERE	vtiger_achievementallot_statistic.achievementtype = vtiger_achievementsummary.achievementtype	AND vtiger_achievementallot_statistic.achievementmonth =vtiger_achievementsummary.achievementmonth AND vtiger_achievementallot_statistic.receivedpaymentownid =vtiger_achievementsummary.userid AND vtiger_achievementallot_statistic.isleave=0),
                             vtiger_achievementsummary.effectiverefund=(SELECT sum(vtiger_achievementallot_statistic.unit_price) FROM	vtiger_achievementallot_statistic	WHERE	vtiger_achievementallot_statistic.achievementtype = vtiger_achievementsummary.achievementtype	AND vtiger_achievementallot_statistic.achievementmonth =vtiger_achievementsummary.achievementmonth AND vtiger_achievementallot_statistic.receivedpaymentownid =vtiger_achievementsummary.userid AND vtiger_achievementallot_statistic.isleave=0),
                             vtiger_achievementsummary.realarriveachievement=(SELECT sum(vtiger_achievementallot_statistic.arriveachievement) FROM vtiger_achievementallot_statistic	WHERE	vtiger_achievementallot_statistic.achievementtype = vtiger_achievementsummary.achievementtype	AND vtiger_achievementallot_statistic.achievementmonth =vtiger_achievementsummary.achievementmonth 	AND vtiger_achievementallot_statistic.receivedpaymentownid =vtiger_achievementsummary.userid AND vtiger_achievementallot_statistic.isleave=0)
                             WHERE userid=? AND vtiger_achievementsummary.achievementmonth =?',
                    array($detailInfo->fields['receivedpaymentownid'], $achievementmonth));
            }
            $result=array('success'=>1,'message'=>"修改成功");
        }while(false);
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }
}
