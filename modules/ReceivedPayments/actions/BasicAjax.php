<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ReceivedPayments_BasicAjax_Action extends Vtiger_Action_Controller {
	function __construct() {
		parent::__construct();
        $this->exposeMethod('preview');
        $this->exposeMethod('add');
        $this->exposeMethod('delete');
        $this->exposeMethod('setReceivedstatus');
        $this->exposeMethod('getSplitServiceContracts');   // 必要要加 不然报错 拒绝访问 郁闷
        $this->exposeMethod('splitReceive');
        $this->exposeMethod('cleanReceive');
        $this->exposeMethod('repeatReceive');
        $this->exposeMethod('setAllowinvoicetotal');
        $this->exposeMethod('chargebacks');
        $this->exposeMethod('NonPayCertificate');
        $this->exposeMethod('changerechargeableamount');
        $this->exposeMethod('dobackcash');
        $this->exposeMethod('splitBatchReceive');
        $this->exposeMethod('collate');//核对合同
        $this->exposeMethod('collateLog');//核对记录
        $this->exposeMethod('batchCollate');//批量核对合同
        $this->exposeMethod('exportData');//导出数据
        $this->exposeMethod('exportFile');//导出数据
        $this->exposeMethod('getCompanyAccountsByChannel');
        $this->exposeMethod('delReceive');
	}

	function checkPermission(Vtiger_Request $request) {
		return;
	}
    /**
     * @param Vtiger_Request $request
     * @throws Exception
     */
	public function process(Vtiger_Request $request) {
		$mode = $request->getMode();
		if(!empty($mode)) {
			echo $this->invokeExposedMethod($mode, $request);
			return;
		}
	}

    public function checkEmails($str){
        $str=trim($str);
        $regex = '/^[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@(?:[-_a-z0-9][-_a-z0-9]*\.)*(?:[a-z0-9][-a-z0-9]{0,62})\.(?:(?:[a-z]{2}\.)?[a-z]{2,})$/i';
        if (preg_match($regex, $str)) {
            return true;
        }
        return false;
    }

    public function log($name, $log) {
        return false;
    }


    // 修改可开发票金额
    public function setAllowinvoicetotal(Vtiger_Request $request) {
        $recordId = $request->get('record');  // 回款id
        $allowinvoicetotal = $request->get('allowinvoicetotal');  // 金额
        $recordModel=Vtiger_Record_Model::getInstanceById($recordId,'ReceivedPayments');
        $unit_price=$recordModel->get('unit_price');//入账金额
        if ($allowinvoicetotal > 0 &&
            $recordId > 0 &&
            $unit_price>0 &&
            bccomp($unit_price,$allowinvoicetotal,2)>=0
        ) {
            global $adb,$current_user;
            $sql = "select allowinvoicetotal from vtiger_receivedpayments where receivedpaymentsid=? LIMIT 1";
            $result = $adb->pquery($sql, array($recordId));
            $num = $adb->num_rows($result);
            if ($num > 0) {
                $row = $adb->query_result_rowdata($result, 0);

                $sql = "update vtiger_receivedpayments set allowinvoicetotal=? where receivedpaymentsid=?";
                $adb->pquery($sql, array($allowinvoicetotal, $recordId));


                $datetime = date('Y-m-d H:i:s');
                $id = $adb->getUniqueId('vtiger_modtracker_basic');
                $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                    array($id, $recordId, 'ReceivedPayments', $current_user->id, $datetime, 0));
                $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                    array($id, 'allowinvoicetotal', $row['allowinvoicetotal'], $allowinvoicetotal));
            }



        }
        $response = new Vtiger_Response();
        $response->setResult(array());
        $response->emit();
    }

    // 发送邮件
    public function sendMail($receivedpayments_row) {
        global $adb, $current_user;
        $query = "SELECT * FROM `vtiger_systems` WHERE server_type='email' AND id=2";
        $result = $adb->pquery($query, array());
        $result = $adb->query_result_rowdata($result, 0);

        // 获取需要发送的人的邮箱地址
        $query = "select user from vtiger_custompowers where custompowerstype='cleanReceiveSendmail'";
        $sel_result = $adb->pquery($query, array());
        $res_cnt = $adb->num_rows($sel_result);
        if($res_cnt != 1) {return false;}
        $rowResult = $adb->query_result_rowdata($sel_result, 0);
        $query = "select email1,email2,last_name from vtiger_users where id IN (".$rowResult['user'].") AND status='Active'";
        $sel_result = $adb->pquery($query, array());
        $res_cnt = $adb->num_rows($sel_result);
        if($res_cnt < 1) {return false;}
        $sendMailAddress = array();
        while($rawData=$adb->fetch_array($sel_result)) {
            $sendMailAddress[] = $rawData;
        }


        $path = dirname(dirname(dirname(__FILE__)));
        require_once $path.'/Emails/class.phpmailer.php';
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
        $mailer->FromName = '珍岛财务部';
        //$mailer->AddAddress('393058262@qq.com', 'zhouhai');//收件人的地址
        foreach($sendMailAddress as $v){
            $v['email1'] = $v['email1'] != '' ? trim($v['email1']) : trim($v['email2']);
            if ($this->checkEmails($v['email1'])) {
                $mailer->AddAddress($v['email1'], $v['last_name']);//收件人的地址
            }
        }

        $Subject = '撤销回款匹配邮件提醒';
        $mailer->WordWrap = 100;
        $mailer->IsHTML(true);
        //$mailer->addembeddedimage('./logo.jpg', 'logoimg', 'logo.jpg');
        $mailer->Subject = $Subject;
        $body  = '回款抬头:'.$receivedpayments_row['paytitle'].' ';
        $body .= '金额:'.$receivedpayments_row['unit_price'].' ';
        $body .= '入职日期:'.$receivedpayments_row['reality_date'].' ';
        $body .= '已被 '.$current_user->last_name.' 取消合同,编号为'.$receivedpayments_row['contract_no'].' ';
        $body .= '时间:'.date('Y-m-d H:i:s');

        $mailer->Body = $body;

        //$mail->AltBody = '收邮件了';//
        $email_flag=$mailer->Send()?'SENT':'Faile';
    }


    // 添加回款记录
    public function modtracker($receivedpayments_row) {
        global $adb, $current_user;
        $datetime = date('Y-m-d H:i:s');

        $id = $adb->getUniqueId('vtiger_modtracker_basic');
        $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
            array($id, $receivedpayments_row['receivedpaymentsid'], 'ReceivedPayments', $current_user->id, $datetime, 0));
        $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
            array($id, 'overdue', '', '合同编号:'.$receivedpayments_row['contract_no'].' 撤销回款匹配'));
    }

    /**
        清除回款匹配
     * @param Vtiger_Request $request
     * @throws Exception
     */
    public function cleanReceive(Vtiger_Request $request){
        $recordId = $request->get('record');  // 回款id
        $from=$request->get('from');  //来源，有的来源
        $resData = array('flag'=>false);
        //判断是否有权限
        global $current_user;
        $adb = PearDatabase::getInstance();
        do {
            if($from!=1){
                $sql = "select * FROM vtiger_custompowers where custompowerstype='receivedpaymentsEdit' AND  FIND_IN_SET(?,user) LIMIT 1";
                $sel_result = $adb->pquery($sql, array($current_user->id));
                $res_cnt = $adb->num_rows($sel_result);
                if($res_cnt==0){
                    //权限验证
                    $resData['msg']='暂无权限操作，请联系运营人员';
                    break;
                }
            }else{
                $res_cnt=1;
            }
            $sql="select * FROM vtiger_achievementallot_statistic where receivedpaymentsid=? AND (status=1 or  isover=1)";
            $result= $adb->pquery($sql, array($recordId));
            $resultNumber = $adb->num_rows($result);
            if($resultNumber>0){
                //验证是否该回款是否已完结或者
                $resData['msg']='回款已计算业绩提成，请进行回款换绑';
                break;
            }
            $recordModel = Vtiger_Record_Model::getInstanceById($recordId, 'ReceivedPayments');
            $allowinvoicetotal=$recordModel->get('allowinvoicetotal');//可开票金额
            $rechargeableamount=$recordModel->get('rechargeableamount');//可使用金额
            $occupationcost=$recordModel->get('occupationcost');//已占用工单成本
            $unit_price=$recordModel->get('unit_price');//入账金额
            $chargebacks=$recordModel->get('chargebacks');//扣款金额
            $relatetoid=$recordModel->get('relatetoid');//合同ＩＤ
            $receivedstatus=$recordModel->get('receivedstatus');
            $standardmoney=$recordModel->get('standardmoney');//原币金额
            if('normal'!=$receivedstatus){
                $resData['msg']='回款状态出现异常，请联系技术人员处理!';
                break;
            }
            if(empty($relatetoid)){
                $resData['msg']='合同出现异常，请联系技术人员处理!';
                break;
            }
            if(bccomp($standardmoney,$rechargeableamount,2)!=0 ||
                bccomp($unit_price,$allowinvoicetotal,2)!=0 ||
                $occupationcost!=0 ||
                $chargebacks!=0
            ){
                $resData['msg']='已进入开票/充值等环节，金额被占用，无法清除匹配，请找财务处理';
                break;
            }
            $modulename = $recordModel->get('modulename');
            if ($modulename == 'ServiceContracts') {
                $sql = "SELECT 
                vtiger_receivedpayments.receivedpaymentsid, 
                vtiger_receivedpayments.staypaymentid, 
                vtiger_receivedpayments.paytitle, 
                vtiger_receivedpayments.unit_price, 
                vtiger_receivedpayments.reality_date, 
                vtiger_receivedpayments.owncompany, 
                vtiger_receivedpayments.matchstatus, 
                vtiger_servicecontracts.sc_related_to, 
                vtiger_servicecontracts.contract_no,
                vtiger_servicecontracts.servicecontractsid, 
                vtiger_servicecontracts.multitype, 
                vtiger_servicecontracts.contract_type
                FROM vtiger_receivedpayments 
                INNER JOIN vtiger_servicecontracts 
                ON vtiger_receivedpayments.relatetoid = 
                vtiger_servicecontracts.servicecontractsid WHERE receivedpaymentsid = ? LIMIT 1";
            } else {
                $sql = "SELECT 
                vtiger_receivedpayments.receivedpaymentsid, 
                vtiger_receivedpayments.staypaymentid, 
                vtiger_receivedpayments.paytitle, 
                vtiger_receivedpayments.unit_price, 
                vtiger_receivedpayments.reality_date, 
                vtiger_receivedpayments.owncompany, 
                vtiger_receivedpayments.matchstatus, 
                vtiger_suppliercontracts.vendorid AS sc_related_to, 
                vtiger_suppliercontracts.contract_no,
                vtiger_suppliercontracts.suppliercontractsid AS servicecontractsid, 
                0 AS multitype 
                FROM vtiger_receivedpayments 
                INNER JOIN vtiger_suppliercontracts 
                ON vtiger_receivedpayments.relatetoid = 
                vtiger_suppliercontracts.suppliercontractsid WHERE receivedpaymentsid = ? LIMIT 1";
            }
            $sel_result3 = $adb->pquery($sql, array($recordId));
            $res_cnt3 = $adb->num_rows($sel_result3);
            if($res_cnt3 != 1){
                //合同必需存在
                $resData['msg'] = '合同出现异常，请联系技术人员处理!';
                break;
            }else{
               //合同反查订单，判断订单是否已激活
                $contract_type=$adb->query_result($sel_result3,0,'contract_type');
                if($modulename == 'ServiceContracts'&&$contract_type=='T云WEB版'){
                    $servicecontractsid=$adb->query_result($sel_result3,0,'servicecontractsid');
                    $sql="select * from vtiger_activationcode where startdate!=0 and  status=1 and contractid=".$servicecontractsid;
                    Matchreceivements_Record_Model::recordLog(array($sql));
                    $orderArray=$adb->run_query_allrecords($sql);
                    if($orderArray){
                        $resData['msg'] = '订单已激活，不允许解绑，请先到服务合同处取消激活';
                        break;
                    }
                }
            }
            // 判断回款对应的合同是否已经提单
            $receivedpayments_row = array();
            $res_cnt4 = 0;
            if ($res_cnt3 > 0) {
                $receivedpayments_row = $adb->query_result_rowdata($sel_result3, 0);
                $oldMatchStatus=$receivedpayments_row['matchstatus'];
                if ($receivedpayments_row['multitype'] != '1') { //单工单类型
                    $sql = "SELECT 1 FROM vtiger_salesorder WHERE vtiger_salesorder.servicecontractsid=? AND vtiger_salesorder.modulestatus!='c_cancel' AND vtiger_salesorder.servicecontractsid>0 LIMIT 1";
                    $sel_result4 = $adb->pquery($sql, array($receivedpayments_row['servicecontractsid']));
                    $res_cnt4 = $adb->num_rows($sel_result4);
                    if ($res_cnt4 > 0) {
                        $resData['msg'] = '回款已存在工单，请作废工单后再取消匹配回款！';
                        break;
                    }
                }
            }
            if ($res_cnt > 0  && $res_cnt3 == 1 && $res_cnt4 == 0) {
                Matchreceivements_Record_Model::recordLog('7','stay');
                if($from!=1){
                    $row = $adb->query_result_rowdata($sel_result, 0);
                    $roles_arr = explode(',', $row['roles']);
                    $user_arr = explode(',', $row['user']);
                }
                if (in_array($current_user->current_user_roles, $roles_arr) || in_array($current_user->id, $user_arr)||$from==1) {
                    //先去撤销T云回款
                    $serviceContractsRecordModel = ServiceContracts_Record_Model::getCleanInstance("ServiceContracts");
                    $serviceContractsRecordModel->cancelPayToTyun($receivedpayments_row['servicecontractsid'],-$unit_price);
                    // 更改回款匹配状态 删除服务合同id
                    $sql = "update vtiger_receivedpayments  set matchdate=null,matchstatus=null,matcherid=null, istimeoutmatch=0,iscrossmonthmatch=0,relatetoid=0,ismatchdepart=0,ismanualmatch=0,accountname='',staypaymentid='',ischeckachievement=null where receivedpaymentsid=?";
                    $adb->pquery($sql, array($recordId));
					$sql = "DELETE FROM vtiger_receivedpayments_notes where receivedpaymentsid=?";
                    $adb->pquery($sql, array($recordId));
                    $resData['flag'] = true;
		    //清除回款后 更新排行榜信息
                    $matchRecordModel = Matchreceivements_Record_Model::getCleanInstance("Matchreceivements");
                    $matchRecordModel->cancelMatchUpdateRank($recordId);
                    //更改代付款的剩余金额
                    if($receivedpayments_row['staypaymentid']){
                        $result = $adb->pquery("select surplusmoney,staypaymenttype from vtiger_staypayment where staypaymentid=? limit 1",
                            array($receivedpayments_row['staypaymentid']));
                        if($adb->num_rows($result)){
                            $data = $adb->fetchByAssoc($result,0);
                            $array[0]=array('fieldname'=>'overdue','prevalue'=>'', 'postvalue'=>'合同编号:'.$receivedpayments_row['contract_no'].' 撤销回款匹配');
                            if($data['staypaymenttype']=='fixation') {
                                $adb->pquery("update vtiger_staypayment set surplusmoney=? where staypaymentid=?",
                                    array(($data['surplusmoney'] + $receivedpayments_row['unit_price']), $receivedpayments_row['staypaymentid']));
                                $prevalue = $data['surplusmoney'];
                                $postvalue = $data['surplusmoney']+$receivedpayments_row['unit_price'];
                                $array[1]=array('fieldname'=>'surplusmoney','prevalue'=>$prevalue, 'postvalue'=>$postvalue);
                            }
                            $MatchBasicAjax = new Matchreceivements_BasicAjax_Action();
                            $MatchBasicAjax->setModTracker($receivedpayments_row['staypaymentid'],$array,'Staypayment');
                        }
                    }
                    //删除回款分成明细
                    $sql = "delete from vtiger_achievementallot where receivedpaymentsid=? ";
                    $adb->pquery($sql, array($recordId));
                    // 删除提成业绩开始
                    //删除提成业绩
                    $adb->pquery("DELETE FROM vtiger_withholdroyalty WHERE vtiger_withholdroyalty.achievementallotid IN( SELECT  vtiger_achievementallot_statistic.achievementallotid FROM vtiger_achievementallot_statistic WHERE  vtiger_achievementallot_statistic.receivedpaymentsid=? ) ", array($recordId));

                    $sqlquery=" SELECT  achievementallotid FROM vtiger_achievementallot_statistic WHERE receivedpaymentsid=? AND achievementmonth > 0 ";
                    $result = $adb->pquery($sqlquery,array($recordId));
                    $deleteArray=[];
                    while ($rowdatas=$adb->fetch_array($result)){
                        $deleteArray[]=$rowdatas['achievementallotid'];
                    }
                    //如果本来有有效数据 则删除汇总表数据
                    if(!empty($deleteArray)){
                        AchievementSummary_Record_Model::delAchievementSummary($deleteArray);
                        $deleteStastic_sql='DELETE  FROM  vtiger_achievementallot_statistic WHERE receivedpaymentsid = ?';
                        $adb->pquery($deleteStastic_sql,array($recordId));
                    }
                    // 删除提成结束

                    if ($modulename == 'ServiceContracts') {
                        $servicecontractsid = $receivedpayments_row['servicecontractsid'];
                        //合同已回款金额
                        $unit_price_total = 0;
                        $sql = "select sum(unit_price) as unit_price_total from vtiger_receivedpayments where receivedstatus='normal' and deleted=0 and relatetoid=?";
                        $sel_result = $adb->pquery($sql, array($servicecontractsid));
                        $res_cnt = $adb->num_rows($sel_result);
                        if ($res_cnt > 0) {
                            $receivedpayments_row = $adb->query_result_rowdata($sel_result, 0);
                            $unit_price_total = $receivedpayments_row['unit_price_total'];
                        }
                        //重置合同上的关闭，已回款金额
                        $sql = "UPDATE vtiger_servicecontracts SET contractstate=0, accountsdue=? WHERE servicecontractsid=?";
                        $adb->pquery($sql, array($unit_price_total, $servicecontractsid));
                        $delSql = "DELETE FROM vtiger_contractperformancecostnew WHERE servicecontractsid=? and receivedpaymentsids=?";
                        $adb->pquery($delSql, array($servicecontractsid, $recordId . ','));
                        $sql = "UPDATE vtiger_contractperformancecostnew SET repuntilprice=if(repuntilprice-?<=0,0,repuntilprice-?),receivedpaymentsids=REPLACE(receivedpaymentsids,'" . $recordId . ",','') WHERE servicecontractsid=? and FIND_IN_SET(?,receivedpaymentsids)";
                        $adb->pquery($sql, array($unit_price, $unit_price, $servicecontractsid, $recordId));
                        //匹配记录储存匹配动作
                        $changeType='解绑';
                        Matchreceivements_Record_Model::recordLog($receivedpayments_row['matchstatus'],'match');
                        if($oldMatchStatus==1){
                            $changeType='跨月解绑';
                        }
                        $receivedModel = new Matchreceivements_Record_Model();
                        $receivedModel->recordReceivedpayment($recordId, $receivedpayments_row['contract_no'], $servicecontractsid, $changeType, null, $current_user->id);
                    }
                    // 更新记录
                    $this->modtracker($receivedpayments_row);
                    // 发送邮件
                    $this->sendMail($receivedpayments_row);
                    // 客户的垫款更新
                    //Accounts_Record_Model::setAdvancesmoney($receivedpayments_row['sc_related_to'], $receivedpayments_row['unit_price'], '(撤销回款匹配)');

                    //自动提醒会计
                    $sql = "select user FROM vtiger_custompowers where custompowerstype='cleanReceiveJobalert'";
                    $sel_result = $adb->pquery($sql, array());
                    $res_cnt = $adb->num_rows($sel_result);
                    $row = array();
                    $paytitle = $receivedpayments_row['paytitle'];
                    if ($res_cnt > 0) {
                        $row = $adb->query_result_rowdata($sel_result, 0);
                        $jobalertsid = $adb->getUniqueID("vtiger_jobalerts");
                        $alertData = array(
                            'jobalertsid' => $jobalertsid,
                            'subject' => '撤销匹配回款提醒',
                            'alerttime' => date('Y-m-d H:i:s'),
                            'modulename' => 'ReceivedPayments',
                            'moduleid' => $recordId,
                            'alertcontent' => $current_user->last_name . '撤销匹配回款.' . '回款抬头: ' . $paytitle,
                            'alertid' => str_replace(',', ' |##| ', $row['user']),
                            'alertstatus' => 'wait',
                            'alertcount' => '0',
                            'finishtime' => '',
                            'activitytype' => 'Call',
                            'taskpriority' => 'High',
                            'remark' => '',
                            'ownerid' => '1',
                            'creatorid' => $current_user->id,
                            'createdtime' => date('Y-m-d H:i:s'),
                            'modifiedby' => '',
                            'modifiedtime' => '',
                            'accountid' => '',
                            'state' => '0'
                        );

                        $divideNames = array_keys($alertData);
                        $divideValues = array_values($alertData);
                        $ttt = $adb->pquery('INSERT INTO `vtiger_jobalerts` (' . implode(',', $divideNames) . ') VALUES (' . generateQuestionMarks($divideValues) . ')', $divideValues);

                        $userids = explode(',', $row['user']);
                        foreach ($userids as $value) {
                            $jobalertsreminderData = array(
                                'jobalertsid' => $jobalertsid,
                                'alertid' => $value
                            );
                            $divideNames = array_keys($jobalertsreminderData);
                            $divideValues = array_values($jobalertsreminderData);
                            $ttt = $adb->pquery('INSERT INTO `vtiger_jobalertsreminder` (' . implode(',', $divideNames) . ') VALUES (' . generateQuestionMarks($divideValues) . ')', $divideValues);
                        }
                    }

                    //系统分类
                    $classifyRecordModel = ReceivedPaymentsClassify_Record_Model::getCleanInstance("ReceivedPaymentsClassify");
                    $classifyRecordModel->systemClassification($recordId);
                }
            }
        }while(0);
        if($from==1){
            return $resData;
        }
        $response = new Vtiger_Response();
        $response->setResult($resData);
        $response->emit();
    }
    public function cleanReceiveBak(Vtiger_Request $request){
        //$this->modtracker(array('receivedpaymentsid'=>'1712'));die;
        //$this->sendMail(array('paytitle'=>'11', 'unit_price'=>'222', 'reality_date'=>'333','contract_no'=>'555'));die;

        $recordId = $request->get('record');  // 回款id
        $resData = array('flag'=>0);
        //判断是否有权限
        global $current_user;
        $adb = PearDatabase::getInstance();
        $sql = "select * FROM vtiger_custompowers where custompowerstype='receivedpaymentsEdit' LIMIT 1";
        $sel_result = $adb->pquery($sql, array());
        $res_cnt = $adb->num_rows($sel_result);

        $recordModel=Vtiger_Record_Model::getInstanceById($recordId,'ReceivedPayments');
        $modulename=$recordModel->get('modulename');
        if($modulename=='ServiceContracts'){
            $leftjoincontact='INNER JOIN vtiger_servicecontracts ON vtiger_receivedpayments.relatetoid = vtiger_servicecontracts.servicecontractsid';
            $sc_related_to='vtiger_servicecontracts.sc_related_to,';
            $contract_no='vtiger_servicecontracts.contract_no,';
        }else{
            $leftjoincontact='INNER JOIN vtiger_suppliercontracts ON vtiger_receivedpayments.relatetoid = vtiger_suppliercontracts.suppliercontractsid';
            $sc_related_to='vtiger_suppliercontracts.vendorid AS sc_related_to,';
            $contract_no='vtiger_suppliercontracts.contract_no,';

        }
        $sql = "SELECT vtiger_receivedpayments.receivedpaymentsid, 
                vtiger_receivedpayments.paytitle, 
                vtiger_receivedpayments.unit_price, 
                vtiger_receivedpayments.reality_date, 
                {$sc_related_to}{$contract_no}
                vtiger_receivedpayments.owncompany 
                FROM vtiger_receivedpayments {$leftjoincontact} WHERE receivedpaymentsid = ? AND relatetoid > 0 AND vtiger_receivedpayments.allowinvoicetotal = vtiger_receivedpayments.unit_price AND vtiger_receivedpayments.rechargeableamount = vtiger_receivedpayments.unit_price LIMIT 1";
        /*$sql = "SELECT vtiger_newinvoice.invoiceid FROM vtiger_newinvoicerayment LEFT JOIN vtiger_newinvoice ON vtiger_newinvoice.invoiceid=vtiger_newinvoicerayment.invoiceid
            LEFT JOIN vtiger_crmentity ON vtiger_newinvoice.invoiceid=vtiger_crmentity.crmid
             WHERE vtiger_newinvoicerayment.receivedpaymentsid=?
            AND vtiger_newinvoicerayment.deleted=0
            AND vtiger_crmentity.deleted=0 LIMIT 1";*/
        $sel_result2 = $adb->pquery($sql, array($recordId));
        $res_cnt2 = $adb->num_rows($sel_result2);
        if($modulename=='ServiceContracts') {
            $sql = "SELECT 
                vtiger_receivedpayments.receivedpaymentsid, 
                vtiger_receivedpayments.paytitle, 
                vtiger_receivedpayments.unit_price, 
                vtiger_receivedpayments.reality_date, 
                vtiger_receivedpayments.owncompany , 
                vtiger_servicecontracts.sc_related_to, 
                vtiger_servicecontracts.contract_no,
                vtiger_servicecontracts.servicecontractsid, 
                vtiger_servicecontracts.multitype 
                FROM vtiger_receivedpayments 
                INNER JOIN vtiger_servicecontracts 
                ON vtiger_receivedpayments.relatetoid = 
                vtiger_servicecontracts.servicecontractsid WHERE receivedpaymentsid = ? LIMIT 1";
        }else{
            $sql = "SELECT 
                vtiger_receivedpayments.receivedpaymentsid, 
                vtiger_receivedpayments.paytitle, 
                vtiger_receivedpayments.unit_price, 
                vtiger_receivedpayments.reality_date, 
                vtiger_receivedpayments.owncompany , 
                vtiger_suppliercontracts.vendorid AS sc_related_to, 
                vtiger_suppliercontracts.contract_no,
                vtiger_suppliercontracts.suppliercontractsid AS servicecontractsid, 
                0 AS multitype 
                FROM vtiger_receivedpayments 
                INNER JOIN vtiger_suppliercontracts 
                ON vtiger_receivedpayments.relatetoid = 
                vtiger_suppliercontracts.suppliercontractsid WHERE receivedpaymentsid = ? LIMIT 1";
        }
        $sel_result3 = $adb->pquery($sql, array($recordId));
        $res_cnt3 = $adb->num_rows($sel_result3);


        // 判断回款对应的合同是否已经提单
        $receivedpayments_row = array();
        $res_cnt4 = 0;
        if($res_cnt3 > 0) {
            $receivedpayments_row = $adb->query_result_rowdata($sel_result3, 0);
            if ($receivedpayments_row['multitype'] != '1') { //单工单类型
                $sql = "SELECT * FROM vtiger_salesorder WHERE vtiger_salesorder.servicecontractsid=? AND vtiger_salesorder.modulestatus!='c_cancel' AND vtiger_salesorder.servicecontractsid>0 LIMIT 1";
                $sel_result4 = $adb->pquery($sql, array($receivedpayments_row['servicecontractsid']));
                $res_cnt4 = $adb->num_rows($sel_result4);
                if ($res_cnt4 > 0) {
                    $resData['msg'] = '回款已存在工单，请作废工单后再取消匹配回款！';
                }
            }
        }
        if($res_cnt > 0 && $res_cnt2 == 1 && $res_cnt3==1 && $res_cnt4==0) {


            $row = $adb->query_result_rowdata($sel_result, 0);
            $roles_arr = explode(',', $row['roles']);
            $user_arr = explode(',', $row['user']);
            if (in_array($current_user->current_user_roles, $roles_arr) || in_array($current_user->id, $user_arr)) {
                // 更改回款匹配状态 删除服务合同id
                $sql = "update vtiger_receivedpayments set relatetoid=0,ismatchdepart=0 where receivedpaymentsid=?";
                $adb->pquery($sql, array($recordId));
                $resData['flag'] = '1';

                //删除回款分成明细
                $sql = "delete from vtiger_achievementallot where receivedpaymentsid=? ";
                $adb->pquery($sql, array($recordId));

                //重置合同上的关闭
                $servicecontractsid=empty($receivedpayments_row['servicecontractsid'])?0:$receivedpayments_row['servicecontractsid'];
                $sql = "UPDATE vtiger_servicecontracts SET contractstate=0 WHERE servicecontractsid=?";
                $adb->pquery($sql, array($servicecontractsid));

                // 更新记录
                $this->modtracker($receivedpayments_row);

                // 发送邮件
                $this->sendMail($receivedpayments_row);

                // 客户的垫款更新
                //Accounts_Record_Model::setAdvancesmoney($receivedpayments_row['sc_related_to'], $receivedpayments_row['unit_price'], '(撤销回款匹配)');

                //自动提醒会计
                $sql = "select user FROM vtiger_custompowers where custompowerstype='cleanReceiveJobalert'";
                $sel_result = $adb->pquery($sql, array());
                $res_cnt = $adb->num_rows($sel_result);
                $row = array();
                $paytitle = $receivedpayments_row['paytitle'];
                if ($res_cnt > 0) {
                    $row = $adb->query_result_rowdata($sel_result, 0);
                    $jobalertsid = $adb->getUniqueID("vtiger_jobalerts");
                    $alertData = array(
                        'jobalertsid'=>$jobalertsid,
                        'subject'=>'撤销匹配回款提醒',
                        'alerttime'=>date('Y-m-d H:i:s'),
                        'modulename'=>'ReceivedPayments',
                        'moduleid'=>$recordId,
                        'alertcontent'=>$current_user->last_name . '撤销匹配回款.' . '回款抬头: '.$paytitle,
                        'alertid'=>str_replace(',', ' |##| ', $row['user']),
                        'alertstatus'=>'wait',
                        'alertcount'=>'0',
                        'finishtime'=>'',
                        'activitytype'=>'Call',
                        'taskpriority'=>'High',
                        'remark'=>'',
                        'ownerid'=>'1',
                        'creatorid'=>$current_user->id,
                        'createdtime'=>date('Y-m-d H:i:s'),
                        'modifiedby'=>'',
                        'modifiedtime'=>'',
                        'accountid'=>'',
                        'state'=>'0'
                    );

                    //print_r($alertData);die;
                    $divideNames = array_keys($alertData);
                    $divideValues = array_values($alertData);
                    $ttt = $adb->pquery('INSERT INTO `vtiger_jobalerts` ('. implode(',', $divideNames).') VALUES ('. generateQuestionMarks($divideValues) .')', $divideValues);


                    $userids = explode(',' , $row['user']);
                    foreach ($userids as $value) {
                        $jobalertsreminderData = array(
                            'jobalertsid'=>$jobalertsid,
                            'alertid'=>$value
                        );
                        $divideNames = array_keys($jobalertsreminderData);
                        $divideValues = array_values($jobalertsreminderData);
                        $ttt = $adb->pquery('INSERT INTO `vtiger_jobalertsreminder` ('. implode(',', $divideNames).') VALUES ('. generateQuestionMarks($divideValues) .')', $divideValues);
                    }
                }

            }
        }
        $response = new Vtiger_Response();
        $response->setResult($resData);
        $response->emit();
    }
    /**
     *重新匹配
     * @param Vtiger_Request $request
     * @throws Exception
     */
    public function repeatReceive(Vtiger_Request $request){
        //cxh 2020-09-16 注释
        //   return false;
        $recordId = $request->get('record');  // 回款id
        $serviceNo = $request->get('serviceid');  // 合同编号
        $serviceNo = trim($serviceNo);  // 合同编号
        $resData = array('flag'=>0);
        //判断是否有权限
        do {
            global $current_user;
            $adb = PearDatabase::getInstance();
            if(empty($serviceNo)){
                $resData['msg'] = '合同编号不能为空';
                break;
            }
            $sql = "select * FROM vtiger_custompowers where custompowerstype='receivedpaymentsRepeat' AND  FIND_IN_SET(?,user) LIMIT 1";
            $sel_result = $adb->pquery($sql, array($current_user->id));
            $res_cnt = $adb->num_rows($sel_result);
            if($res_cnt==0){
                $resData['msg'] = '没有权限!';
                break;
            }
            $sql="select * FROM vtiger_achievementallot_statistic where receivedpaymentsid=? AND (status=1 or  isover=1)";
            $result= $adb->pquery($sql, array($recordId));
            $resultNumber = $adb->num_rows($result);
            if($resultNumber>0){
                //验证是否该回款是否已完结或者
                $resData['msg']='该回款绩效已经完结或者提成已核算，不允许该操作!';
                break;
            }
            $row = $adb->query_result_rowdata($sel_result, 0);
            $roles_arr = explode(',', $row['roles']);
            $user_arr = explode(',', $row['user']);
            if (!in_array($current_user->current_user_roles, $roles_arr) && !in_array($current_user->id, $user_arr)) {
                $resData['msg'] = '没有权限!';
                break;
            }
            $query = "SELECT vtiger_servicecontracts.contract_type,vtiger_servicecontracts.servicecontractsid,vtiger_servicecontracts.contract_no,vtiger_servicecontracts.sc_related_to,vtiger_servicecontracts.signid FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_servicecontracts.servicecontractsid WHERE vtiger_crmentity.deleted=0 AND vtiger_servicecontracts.modulestatus='c_complete' AND vtiger_servicecontracts.contract_no=? LIMIT 1";
            $sel_result1 = $adb->pquery($query, array($serviceNo));
            $res_cnt5 = $adb->num_rows($sel_result1);
            if($res_cnt5==0){
                $resData['msg'] = '不存在的合同编号!';
                break;
            }

            $sql = "SELECT vtiger_receivedpayments.receivedpaymentsid, vtiger_receivedpayments.paytitle, vtiger_receivedpayments.unit_price, vtiger_receivedpayments.reality_date, vtiger_receivedpayments.owncompany , vtiger_servicecontracts.sc_related_to, vtiger_servicecontracts.contract_no FROM vtiger_receivedpayments INNER JOIN vtiger_servicecontracts ON vtiger_receivedpayments.relatetoid = vtiger_servicecontracts.servicecontractsid WHERE receivedpaymentsid = ? AND relatetoid > 0 AND vtiger_receivedpayments.allowinvoicetotal = vtiger_receivedpayments.unit_price LIMIT 1";

            $sel_result2 = $adb->pquery($sql, array($recordId));
            $res_cnt2 = $adb->num_rows($sel_result2);
            if($res_cnt2!=0){
                $resData['msg'] = '请先取消匹配!';
                break;
            }

            $sql = "SELECT vtiger_receivedpayments.receivedpaymentsid, vtiger_receivedpayments.paytitle, vtiger_receivedpayments.unit_price,vtiger_receivedpayments.matchdate, vtiger_receivedpayments.reality_date, vtiger_receivedpayments.owncompany , vtiger_servicecontracts.sc_related_to, vtiger_servicecontracts.contract_no,vtiger_servicecontracts.servicecontractsid, vtiger_servicecontracts.multitype FROM vtiger_receivedpayments INNER JOIN vtiger_servicecontracts ON vtiger_receivedpayments.relatetoid = vtiger_servicecontracts.servicecontractsid WHERE receivedpaymentsid = ? LIMIT 1";
            $sel_result3 = $adb->pquery($sql, array($recordId));
            $res_cnt3 = $adb->num_rows($sel_result3);


            // 判断回款对应的合同是否已经提单
            $receivedpayments_row = array();
            $res_cnt4 = 0;
            if ($res_cnt3 > 0) {
                $receivedpayments_row = $adb->query_result_rowdata($sel_result3, 0);
                if ($receivedpayments_row['multitype'] != '1') { //单工单类型
                    $sql = "SELECT * FROM vtiger_salesorder WHERE vtiger_salesorder.servicecontractsid=? AND vtiger_salesorder.modulestatus!='c_cancel' AND vtiger_salesorder.servicecontractsid>0 LIMIT 1";
                    $sel_result4 = $adb->pquery($sql, array($receivedpayments_row['servicecontractsid']));
                    $res_cnt4 = $adb->num_rows($sel_result4);
                    if ($res_cnt4 > 0) {
                        $resData['msg'] = '回款已存在工单，请作废工单后再取消匹配回款！';
                        break;
                    }
                }
            }
            $rowData = $adb->query_result_rowdata($sel_result1, 0);
            $serviceid = $rowData['servicecontractsid'];

            if($rowData['contract_type']=='T云WEB版'&&!Matchreceivements_Record_Model::isPreContractMatched($serviceid)){
                $resData['msg'] = '上份合同回款未完成，不允许匹配!';
                break;
            }


            //判断匹配的合同是否和发票匹配的合同一致 gaocl add 2018/04/03
            /*$sql = "SELECT 1 FROM  vtiger_newinvoicerayment WHERE receivedpaymentsid=? AND deleted=0";
            $sel_result5 = $adb->pquery($sql, array($recordId,$serviceid));
            $res_cnt5 = $adb->num_rows($sel_result5);
            if ($res_cnt5 > 0) {
                $sql = "SELECT 1 FROM (
                     SELECT invoiceid,contract_no,COUNT(1) as cnt FROM vtiger_newinvoicerayment WHERE receivedpaymentsid=? GROUP BY invoiceid) T
                     WHERE T.cnt>1 AND T.contract_no=?";
                $sel_result5 = $adb->pquery($sql, array($recordId,$serviceid));
                $res_cnt5 = $adb->num_rows($sel_result5);
                if ($res_cnt5 <= 0) {
                    $resData['msg'] = '重新匹配的合同与发票申请单关联的合同不一致，不能重新匹配！';
                    break;
                }
            }*/

            //回款重新匹配合同功能调整：重新匹配必须匹配到原合同客户抬头下已签收的合同，且同步发票申请单上的服务合同以及回款关联信息里边的回款信息修改。 gaocl add 2018/05/28
            $new_accountid = $rowData['sc_related_to'];
            $new_servicecontractsid = $rowData['servicecontractsid'];
            $query_sql = "SELECT vtiger_servicecontracts.servicecontractsid,vtiger_servicecontracts.contract_no,vtiger_servicecontracts.sc_related_to FROM vtiger_servicecontracts 
                  LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_servicecontracts.servicecontractsid 
                  WHERE vtiger_crmentity.deleted=0 AND vtiger_servicecontracts.modulestatus='c_complete' 
                 AND vtiger_servicecontracts.servicecontractsid=(SELECT relatetoid FROM vtiger_receivedpayments WHERE receivedpaymentsid=? AND deleted=0) LIMIT 1";
            $sel_result6 = $adb->pquery($query_sql, array($recordId));
            $res_cnt6 = $adb->num_rows($sel_result6);
            if($res_cnt6==0){
                $resData['msg'] = '不存在的合同编号!';
                break;
            }
            $rowData6 = $adb->query_result_rowdata($sel_result6, 0);
            $old_accountid = $rowData6['sc_related_to'];
            $old_servicecontractsid = $rowData6['servicecontractsid'];
            $old_contract_no = $rowData6['contract_no'];
            if($new_accountid != $old_accountid){
                $resData['msg'] = '必须匹配到原合同客户抬头下已签收的合同,请确认!';
                break;
            }
            // 更改回款匹配状态 删除服务合同id
            $sql = "update vtiger_receivedpayments set relatetoid=0,ismatchdepart=0,matcherid=null where receivedpaymentsid=?";
            $adb->pquery($sql, array($recordId));
            $resData['flag'] = '1';

            //删除回款分成明细
            $sql = "delete from vtiger_achievementallot where receivedpaymentsid=? ";
            $adb->pquery($sql, array($recordId));
            // 删除提成业绩开始
            //删除提成业绩
            $sqlquery=" SELECT  achievementallotid FROM vtiger_achievementallot_statistic WHERE receivedpaymentsid=? AND achievementmonth > 0 ";
            $result = $adb->pquery($sqlquery,array($recordId));
            $deleteArray=[];
            while ($rowdatas=$adb->fetch_array($result)){
                $deleteArray[]=$rowdatas['achievementallotid'];
            }
            //如果本来有有效数据 则删除汇总表数据
            if(!empty($deleteArray)){
                AchievementSummary_Record_Model::delAchievementSummary($deleteArray);
                $deleteStastic_sql='DELETE  FROM  vtiger_achievementallot_statistic WHERE receivedpaymentsid = ?';
                $adb->pquery($deleteStastic_sql,array($recordId));
            }
            // 删除提成结束
            $servicecontractsid=empty($receivedpayments_row['servicecontractsid'])?0:$receivedpayments_row['servicecontractsid'];
            //合同已回款金额
            $unit_price_total = 0;
            $sql = "select sum(unit_price) as unit_price_total from vtiger_receivedpayments where receivedstatus='normal' and deleted=0 and relatetoid=?";
            $sel_result = $adb->pquery($sql, array($servicecontractsid));
            $res_up = $adb->num_rows($sel_result);
            if ($res_up > 0) {
                $up_row = $adb->query_result_rowdata($sel_result, 0);
                $unit_price_total = $up_row['unit_price_total'];
            }
            //重置合同上的关闭，已回款金额
            $sql = "UPDATE vtiger_servicecontracts SET contractstate=0, accountsdue=? WHERE servicecontractsid=?";
            $adb->pquery($sql, array($unit_price_total, $servicecontractsid));

            // 更新记录
            $this->modtracker($receivedpayments_row);

            $recordModel=new ReceivedPayments_Record_Model();
            $recordModel->matchingWithTimeOut($recordId,1,1);

            //匹配记录储存匹配动作
            $receivedModel=new Matchreceivements_Record_Model();
            $receivedModel->recordReceivedpayment($recordId,$serviceNo,$servicecontractsid,'重新匹配',null,$current_user->id);

            // 发送邮件
            //$this->sendMail($receivedpayments_row);

            // 客户的垫款更新
            //Accounts_Record_Model::setAdvancesmoney($receivedpayments_row['sc_related_to'], $receivedpayments_row['unit_price'], '(撤销回款匹配)');
            // 重新匹配合同
            $this->replaceReciveData($recordId,$rowData['servicecontractsid'],$receivedpayments_row['unit_price'],$rowData['signid']);
            // 匹配完合同然后计算业绩 处理
            $Matchreceivements_BasicAjax_Action=new Matchreceivements_BasicAjax_Action();
            //$Matchreceivements_BasicAjax_Action->commonInsertAchievementallotStatistic($recordId,$receivedpayments_row['unit_price'],0,0,$rowData['servicecontractsid']);
            $Matchreceivements_BasicAjax_Action->commonInsertAchievementallotStatisticjioaben($recordId,$receivedpayments_row['unit_price'],0,0,$receivedpayments_row['servicecontractid'],0,$receivedpayments_row['matchdate']);
            //同步发票申请单上的服务合同以及回款关联信息里边的回款信息修改 gaocl add 2018/05/28
            //更新发票关联回款表
            $adb->pquery("UPDATE `vtiger_newinvoicerayment` SET servicecontractsid=?,contract_no=? WHERE deleted=0 AND receivedpaymentsid=?",array($rowData['servicecontractsid'],$rowData['contract_no'],$recordId));
            //更新充值申请单关联回款表
            $adb->pquery("UPDATE vtiger_refillapprayment SET servicecontractsid=? WHERE deleted=0 AND receivedpaymentsid=?",array($rowData['servicecontractsid'],$recordId));
            //更新发票对应合同
            $query_invoice_sql = "SELECT invoiceid FROM vtiger_newinvoicerayment WHERE receivedpaymentsid=? AND deleted=0 LIMIT 1";
            $sel_result7 = $adb->pquery($query_invoice_sql, array($recordId));
            $res_cnt7 = $adb->num_rows($sel_result7);
            $invoiceid = 0;
            if($res_cnt7 > 0){
                $rowData7 = $adb->query_result_rowdata($sel_result7, 0);
                $invoiceid = $rowData7['invoiceid'];
            }
            $adb->pquery("UPDATE vtiger_newinvoice SET contractid=? WHERE invoiceid=?",array($rowData['servicecontractsid'],$invoiceid));
            //更新充值申请单对应合同
            $adb->pquery("UPDATE vtiger_refillapplication SET servicecontractsid=? WHERE refillapplicationid=(SELECT refillapplicationid FROM vtiger_refillapprayment WHERE receivedpaymentsid=? AND deleted=0 LIMIT 1)",array($rowData['servicecontractsid'],$recordId));
            //发票对应合同变更记录表
            $adb->pquery("INSERT INTO vtiger_newinvoice_history (`invoiceid`, `oldcontract_id`, `oldcontract_no`, `newcontract_id`, `newcontract_no`, `modifiedby`, `modifiedtime`,`remark`) VALUES (?,?,?,?,?,?,NOW(),?)",array($invoiceid,$old_servicecontractsid,$old_contract_no,$new_servicecontractsid,$serviceNo,$current_user->id,'回款重新匹配合同'));

            $resData['msg'] = '重新匹配成功';

            //系统分类
            $classifyRecordModel = ReceivedPaymentsClassify_Record_Model::getCleanInstance("ReceivedPaymentsClassify");
            $classifyRecordModel->systemClassification($recordId);
        }while(0);
        $response = new Vtiger_Response();
        $response->setResult($resData);
        $response->emit();
    }

    public function splitBatchReceive(Vtiger_Request $request) {
        $recordId = $request->get('record');  // 回款id
        $splitMoneyArray=explode('&',str_replace('split_money%5B%5D=','',$request->get('split_money')));//拆分金额
        $recordObject=new ReceivedPayments_Record_Action();
        $retrun=array('flag'=>true);
        foreach ($splitMoneyArray as $key=>$splitMoney){
            $retrun['msg'][$key]="";
            $recordModel=Vtiger_Record_Model::getInstanceById($recordId,'ReceivedPayments',true);
            $splitRequestMap['record']=$recordId;
            $splitRequestMap['contract_no']='';
            $splitRequestMap['split_money']=$splitMoney;
            $splitRequestMap['unit_price']=$recordModel->get('unit_price');
            $splitRequestMap['t_split_money']=bcsub($recordModel->get('unit_price'),$splitMoney,2);
            $splitRequest=new Vtiger_Request($splitRequestMap);
            $result=$recordObject->splitReceive($splitRequest);
            if(!$result['flag']){
                $retrun['flag']=false;
                $retrun['msg'][$key]=$result['msg'];
            }
        }
        $response = new Vtiger_Response();
        $response->setResult($retrun);
        $response->emit();
    }


    /**
        拆分合同
     * @param Vtiger_Request $request
     * @throws Exception
     */
    public function splitReceive(Vtiger_Request $request) {
        $recordObject=new ReceivedPayments_Record_Action();
        $retrun=$recordObject->splitReceive($request);
        $response = new Vtiger_Response();
        $response->setResult($retrun);
        $response->emit();
    }
    public function splitReceivebak(Vtiger_Request $request) {
        $recordId = $request->get('record');  // 回款id
        $contract_no = $request->get('contract_no'); //合同编号 id
        $split_money = $request->get('split_money');   //拆分金额
        $t_split_money = $request->get('t_split_money');   //拆分后的原始金额
        $unit_price = $request->get('unit_price');   //原款金额
        $t_split_money=$unit_price-$split_money;
        if (empty($contract_no)) {
            $contract_no = '';
        }
        if(!is_numeric($split_money) || $split_money <= 0) {
            $response = new Vtiger_Response();
            $response->setResult(array());
            $response->emit();
            exit;
        }
        if(!is_numeric($t_split_money) || $t_split_money < 0) {
            $response = new Vtiger_Response();
            $response->setResult(array());
            $response->emit();
            exit;
        }

        global $current_user;
        $db = PearDatabase::getInstance();

        // 如果回款 开立了发票 不能拆分
       /* $sql = "SELECT vtiger_newinvoice.invoiceid FROM vtiger_newinvoicerayment LEFT JOIN vtiger_newinvoice ON vtiger_newinvoice.invoiceid=vtiger_newinvoicerayment.invoiceid
            LEFT JOIN vtiger_crmentity ON vtiger_newinvoice.invoiceid=vtiger_crmentity.crmid
            WHERE vtiger_newinvoicerayment.receivedpaymentsid=?
            AND vtiger_newinvoicerayment.deleted=0 AND vtiger_newinvoicerayment.allowinvoicetotal>0
            AND vtiger_crmentity.deleted=0 LIMIT 1";*/
        /*$sel_result2 = $db->pquery($sql, array($recordId));
        $res_cnt2 = $db->num_rows($sel_result2);
        if ($res_cnt2 > 0) {
            $response = new Vtiger_Response();
            $response->setResult(array('flag'=>0));
            $response->emit();
            exit;
        }*/

        // 1. 获取 回款

        $sql = "SELECT *  FROM vtiger_receivedpayments WHERE receivedpaymentsid=?";
        $sel_result = $db->pquery($sql, array($recordId));
        $res_cnt = $db->num_rows($sel_result);
        $unit_price = 0;
        if($res_cnt > 0) {
            $oldRow = $db->query_result_rowdata($sel_result, 0);
            $row =  $oldRow;

            //回款拆分功能：回款申请过开票就不能拆分。gaocl add 2018/05/29
            if(bccomp($row['unit_price'],$row['allowinvoicetotal']) !=0){
                $response = new Vtiger_Response();
                $response->setResult(array('flag'=>0,'msg'=>'发票已关联回款，不能拆分'));
                $response->emit();
                exit;
            }

            //回款拆分功能：充值或工单使用此回款不能拆分。gaocl add 2018/06/26
            if(bccomp($row['unit_price'],$row['rechargeableamount'],2) !=0){
                $response = new Vtiger_Response();
                $response->setResult(array('flag'=>0,'msg'=>'充值或工单已使用此回款，不能拆分'));
                $response->emit();
                exit;
            }

            $receivedpaymentsid = $db->getUniqueID('vtiger_ReceivedPayments');
            $row['receivedpaymentsid'] = $receivedpaymentsid;
            $row['unit_price'] = $split_money;
            $row['standardmoney'] = $split_money;
            $row['createtime'] = date('Y-m-d H:i:s');
            $row['createtime'] = date('Y-m-d H:i:s');
            $row['relatetoid'] = $contract_no;
            $row['old_receivedpaymentsid'] = $recordId;
            $row['overdue'] = $row['overdue'] . ' | 拆分回款';
            $row['allowinvoicetotal'] = $split_money;
            $row['rechargeableamount'] = $split_money;
            if($row['relatetoid'] > 0) {
                $row['ismatchdepart'] = 1;
            } else {
                $row['ismatchdepart'] = 0;
            }

            foreach ($row as $key => $value) {
                if (is_numeric($key)) {
                    unset($row[$key]);
                }
            }

            //$current_id = $db->getUniqueID("vtiger_receivedpayments");
            $current_id = $receivedpaymentsid;
            $row['receivedpaymentsid'] = $current_id;
            // 添加数据
            $divideNames = array_keys($row);
            $divideValues = array_values($row);
            /*echo 'INSERT INTO `vtiger_receivedpayments` ('. implode(',', $divideNames).') VALUES ('. generateQuestionMarks($divideValues) .')';
            exit;*/
            foreach ($divideValues as $k=>$v) {
                if (empty($v)) {
                    $divideValues[$k] = '';
                }
            }
            /*$sql = 'INSERT INTO `vtiger_receivedpayments` ('. implode(',', $divideNames).') VALUES (' . implode(',', $divideValues) . ')';
            echo $sql;
            exit;*/

            $db->pquery('INSERT INTO `vtiger_receivedpayments` ('. implode(',', $divideNames).') VALUES ('. generateQuestionMarks($divideValues) .')', $divideValues);


            $id = $db->getUniqueId('vtiger_modtracker_basic');
            $db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                array($id, $current_id, 'ReceivedPayments', $current_user->id, date('Y-m-d H:i:s'), 0));
            $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                Array($id, 'overdue', '', "回款拆分".$t_split_money.'=>'.$split_money));

            // 更新原始回款
            $sql = "UPDATE vtiger_receivedpayments set unit_price=?, standardmoney=?, old_receivedpaymentsid=?, allowinvoicetotal=if(allowinvoicetotal-{$split_money}>0,(allowinvoicetotal-{$split_money}),0),rechargeableamount=if(rechargeableamount-{$split_money}>0,(rechargeableamount-{$split_money}),0) WHERE receivedpaymentsid=?";
            $db->pquery( $sql, array($t_split_money, $t_split_money, $recordId, $recordId));


            // 更新回款中的分成比例
            if($row['relatetoid'] > 0) { //如果回款已经匹配
                // 新生成的回款
                $divide_arr =  ServiceContracts_Record_Model::servicecontracts_divide($row['relatetoid']);
                for ($i=0;$i<count($divide_arr);++$i){
                    $divide_temp = $divide_arr[$i];
                    $divide_data['owncompanys'] = $divide_temp['owncompanys'];
                    $divide_data['receivedpaymentsid'] = $current_id;
                    $divide_data['receivedpaymentownid'] = $divide_temp['receivedpaymentownid'];
                    $divide_data['scalling'] = $divide_temp['scalling'];
                    $divide_data['servicecontractid'] = $row['relatetoid'];
                    if(!empty($row['unit_price'])){
                        $divide_data['businessunit'] = ($divide_temp['scalling'] * $row['unit_price'])/100;
                        $divideNames = array_keys($divide_data);
                        $divideValues = array_values($divide_data);
                        $db->pquery('INSERT INTO `vtiger_achievementallot` ('. implode(',', $divideNames).') VALUES ('. generateQuestionMarks($divideValues) .')',$divideValues);
                    }
                }
                $receiveRecordModel=Vtiger_Record_Model::getCleanInstance("ReceivedPayments");
                $receiveRecordModel->setUpdateSalesorder($row['relatetoid']);
            }

            // 老的回款 更新回款记录
            // 删除老的回款分成比例 在重新添加
            if($oldRow['relatetoid'] > 0) { //老的回款已经匹配
                $sql = "delete from vtiger_achievementallot where receivedpaymentsid=?";
                $db->pquery($sql, array($oldRow['receivedpaymentsid']));

                //重新添加
                $divide_arr =  ServiceContracts_Record_Model::servicecontracts_divide($oldRow['relatetoid']);
                for ($i=0;$i<count($divide_arr);++$i){
                    $divide_temp = $divide_arr[$i];
                    $divide_data['owncompanys'] = $divide_temp['owncompanys'];
                    $divide_data['receivedpaymentsid'] = $oldRow['receivedpaymentsid'];
                    $divide_data['receivedpaymentownid'] = $divide_temp['receivedpaymentownid'];
                    $divide_data['scalling'] = $divide_temp['scalling'];
                    $divide_data['servicecontractid'] = $oldRow['relatetoid'];
                    if(!empty($oldRow['unit_price'])){
                        $divide_data['businessunit'] = ($divide_temp['scalling'] * $t_split_money)/100;
                        $divideNames = array_keys($divide_data);
                        $divideValues = array_values($divide_data);
                        $db->pquery('INSERT INTO `vtiger_achievementallot` ('. implode(',', $divideNames).') VALUES ('. generateQuestionMarks($divideValues) .')',$divideValues);

                    }
                }
            }

            // 如果回款已经匹配 新拆分的回款添加回款匹配记录
            if($row['relatetoid'] > 0) { //如果回款已经匹配
                $t_id = $db->getUniqueID("vtiger_receivedpayments_notes");
                $sql = "INSERT INTO `vtiger_receivedpayments_notes` (`receivedpaymentsnotesid`, `createtime`, `smownerid`, `receivedpaymentsid`, `notestype`) VALUES ('{$t_id}', '". date('Y-m-d H:i:s') ."', '{$current_user->id}', '{$current_id}', 'notestype2')";
                $db->pquery($sql, array());
            }
            $invoiceReceiveQuery="SELECT vtiger_newinvoicerayment.*,vtiger_newinvoiceextend.invoicestatus FROM `vtiger_newinvoicerayment` LEFT JOIN vtiger_newinvoiceextend ON vtiger_newinvoiceextend.invoiceid=vtiger_newinvoicerayment.invoiceid WHERE vtiger_newinvoicerayment.deleted=0 AND vtiger_newinvoicerayment.receivedpaymentsid=? ORDER BY vtiger_newinvoicerayment.allowinvoicetotal DESC";
            $dataresult=$db->pquery($invoiceReceiveQuery,array($recordId));
            $invoiceReceiveNum=$db->num_rows($dataresult);
            if($invoiceReceiveNum){
                $DifferenceInAmount=0;
                $array=array("redinvoice","tovoid");
                $flag=true;
                $newInvoicePayid=0;
                for($i=0;$i<$invoiceReceiveNum;$i++){
                    $dataTemp=$db->raw_query_result_rowdata($dataresult,$i);

                    if(!in_array($dataTemp["invoicestatus"],$array)){
                        $DifferenceInAmount=$DifferenceInAmount==0?$dataTemp["allowinvoicetotal"]-$t_split_money:$DifferenceInAmount;
                        if($DifferenceInAmount>0){
                            $newInvoicePayid=$newInvoicePayid==0?$dataTemp["newinvoiceraymentid"]:$newInvoicePayid;
                            $allowinvoicetotal=$dataTemp["allowinvoicetotal"]-$DifferenceInAmount;
                            if($allowinvoicetotal>=$dataTemp["invoicetotal"]){
                                $db->pquery("UPDATE vtiger_newinvoicerayment SET total=?,allowinvoicetotal=? WHERE vtiger_newinvoicerayment.newinvoiceraymentid=?",
                                    array($t_split_money,$allowinvoicetotal,$dataTemp["newinvoiceraymentid"]));
                            }else{
                                if($flag){
                                    //$invoicetotal=$dataTemp["invoicetotal"]-$allowinvoicetotal;
                                    $db->pquery("UPDATE vtiger_newinvoicerayment SET total=?,allowinvoicetotal=?,invoicetotal=?,surpluinvoicetotal=? WHERE vtiger_newinvoicerayment.newinvoiceraymentid=?",
                                        array($t_split_money,$allowinvoicetotal,$allowinvoicetotal,$allowinvoicetotal,$dataTemp["newinvoiceraymentid"]));
                                }else{
                                    $db->pquery("UPDATE vtiger_newinvoicerayment SET deleted=1 WHERE vtiger_newinvoicerayment.newinvoiceraymentid=?",
                                        array($dataTemp["newinvoiceraymentid"]));
                                }
                            }
                        }
                    }
                }
                if($DifferenceInAmount>0){
                    $db->pquery("INSERT INTO `vtiger_newinvoicerayment` (`servicecontractsid`, `receivedpaymentsid`, `total`, `arrivaldate`, `invoicetotal`, `allowinvoicetotal`, `invoicecontent`, `remarks`, `invoiceid`, `contract_no`, `deleted`, `modifiedby`, `modifiedtime`, `paytitle`, `surpluinvoicetotal`) 
                        SELECT  {$contract_no}, {$receivedpaymentsid}, {$DifferenceInAmount}, `arrivaldate`, {$DifferenceInAmount}, {$DifferenceInAmount}, `invoicecontent`, `remarks`, `invoiceid`, (SELECT contract_no FROM vtiger_servicecontracts WHERE servicecontractsid={$contract_no} LIMIT 1), `deleted`, `modifiedby`, `modifiedtime`, `paytitle`, {$DifferenceInAmount}
                        FROM vtiger_newinvoicerayment WHERE newinvoiceraymentid=?",
                        array($newInvoicePayid));
                    $sql = "UPDATE vtiger_receivedpayments set allowinvoicetotal=allowinvoicetotal-{$DifferenceInAmount} WHERE receivedpaymentsid=?";
                    $db->pquery( $sql, array($receivedpaymentsid));
                }
            }

            $response = new Vtiger_Response();
            $response->setResult(array('flag'=>1));
            $response->emit();
        } else {
            $response = new Vtiger_Response();
            $response->setResult(array('flag'=>0,'msg'=>'没有查询到数据'));
            $response->emit();
        }
    }

    /**
        获取当前回款客户的所有合同信息 和 回款的金额
     * @param Vtiger_Request $request
     * @throws Exception
     */
    public function getSplitServiceContracts(Vtiger_Request $request) {
        global $current_user;
        $db = PearDatabase::getInstance();
        $recordId = $request->get('record');  // 回款id
        $query="SELECT receivedpaymentsid FROM vtiger_achievementallot_statistic WHERE receivedpaymentsid= ? AND isover=1 ";
        $resultdata=$db->pquery($query,array($recordId));
        if($db->num_rows($resultdata)>0){
            $response = new Vtiger_Response();
            $response->setResult(array('isover'=>1));
            $response->emit();
            exit();
        }
        /*$sql = "SELECT
                vtiger_servicecontracts.*
            FROM
                vtiger_receivedpayments
            LEFT JOIN vtiger_servicecontracts ON maybe_account = sc_related_to ## 回款可能客户 = 服务合同里面的客户
            LEFT JOIN vtiger_crmentity ON maybe_account = crmid
            WHERE
                (
                    vtiger_receivedpayments.receivedstatus = 'normal'  ## 回款合同状态
                    AND maybe_account != ''  ## 可能用户不为空
                    AND vtiger_receivedpayments.receivedpaymentsid = ?
                )";*/
        $sql="SELECT vtiger_servicecontracts.* FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_servicecontracts.servicecontractsid WHERE vtiger_crmentity.deleted = 0 AND vtiger_servicecontracts.modulestatus = 'c_complete' AND vtiger_servicecontracts.sc_related_to = (SELECT sertemp.sc_related_to FROM vtiger_receivedpayments LEFT JOIN vtiger_servicecontracts sertemp ON sertemp.servicecontractsid = vtiger_receivedpayments.relatetoid WHERE vtiger_receivedpayments.receivedstatus in('normal','virtualrefund') AND vtiger_receivedpayments.receivedpaymentsid =?)";

        $sel_result = $db->pquery($sql, array($recordId));
        $res_cnt = $db->num_rows($sel_result);

        $contract_no_arr = array();
        if($res_cnt > 0) {
            while($rawData = $db->fetch_array($sel_result)) {
                if (!empty($rawData['contract_no'])) {
                    $contract_no_arr[] = array(
                        'servicecontractsid'=>$rawData['servicecontractsid'],
                        'contract_no'=>$rawData['contract_no']
                    );
                }


            }
        }

        // 获取回款的金额
        $sql = "SELECT unit_price  FROM vtiger_receivedpayments WHERE receivedpaymentsid=? limit 1";
        $sel_result = $db->pquery($sql, array($recordId));
        $res_cnt = $db->num_rows($sel_result);
        $unit_price = 0;
        if($res_cnt > 0) {
            $row =  $db->query_result_rowdata($sel_result, 0);
            $unit_price = $row['unit_price'];
        }


        $response = new Vtiger_Response();
        $response->setResult(array('isover'=>0,'contract_no'=>$contract_no_arr, 'unit_price'=>$unit_price));
        $response->emit();
    }

    /**
        修改回款状态
     * @param Vtiger_Request $request
     * @throws Exception
     */
    public function setReceivedstatus(Vtiger_Request $request) {
        global $current_user;
        $recordId = $request->get('record');
        $status = $request->get('status');
        $recordModel=Vtiger_Record_Model::getInstanceById($recordId,'ReceivedPayments');
        // 只有系统管理员才能修改
        /*if ($current_user->is_admin == 'on') {
            $sql = "UPDATE vtiger_receivedpayments set receivedstatus=? where receivedpaymentsid=?";
            $db = PearDatabase::getInstance();
            $db->pquery($sql, array($status, $recordId));
        }*/
        $data = array('flag'=>false,'msg'=>'');
        do{
            if(!$recordModel->personalAuthority('ReceivedPayments','setReceiveStatus')) {
                $data = array('flag'=>false,'msg'=>'没有权限操作');
                break;
            }
            $unit_price=$recordModel->get('unit_price');//入账金额
            $rechargeableamount=$recordModel->get('rechargeableamount');//可使用金额
            $allowinvoicetotal=$recordModel->get('allowinvoicetotal');//可开票金额
            $receivedstatus=$recordModel->get('receivedstatus');
            $occupationcost=$recordModel->get('occupationcost');//已占用工单成本
            $chargebacks=$recordModel->get('chargebacks');//扣款金额
            $relatetoid=$recordModel->get('relatetoid');//合同ＩＤ
            $standardmoney=$recordModel->get('standardmoney');//原币金额


            if($receivedstatus==$status){
                $data = array('flag'=>false,'msg'=>'相同状态不需要修改!');
                break;
            }
            if('virtualrefund'==$receivedstatus){
                $data['msg']='虚拟回款不允许修改状态!';
                break;
            }
            if($status=='deposit'){
                if($receivedstatus!='normal'){
                    $data['msg']='正常状态的回款才能转为保证金!';
                    break;
                }
                if($relatetoid>0){
                    $data['msg']='匹配过的回款不允许转保证金!';
                    break;
                }

            }
            if($receivedstatus!='NonPayCertificate'){
                if(bccomp($standardmoney,$rechargeableamount,2)!=0 ||
                    bccomp($unit_price,$allowinvoicetotal,2)!=0 ||
                    $occupationcost!=0 ||
                    $chargebacks!=0
                ){
                    $data = array('flag'=>false,'msg'=>'回款已开票或已充值,不能修改状态');
                    break;
                }
            }
            $db = PearDatabase::getInstance();
            $accountidStr='';
            if($status=='SupplierRefund' || $status=='RebateAmount'){
                $accountname=$recordModel->get('paytitle');
                $label=preg_replace('/\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;|&quot;|　|&apos;|&amp;|&lt;|&gt;|&#039;|&ldquo;|&rdquo;|&lsquo;|&rsquo;|&hellip;|\“|\”|\‘|\〉|\〈|\’|\〖|\〗|\【|\】|\、|\·|\……|\…|\——|\＋|\－|\＝|\,|\<|\.|\>|\/|\?|\;|\:|\\\'|\"|\[|\{|\]|\}|\\|\||\`|\~|\!|\@|\#|\$|\%|\^|\\&|\*|\(|\)|\-|\_|\=|\+|\，|\＜|\．|\＞|\／|\？|\；|\：|\＇|\＂|\［|\{|\］|\}|\＼|\｜|\｀|\￣|\！|\＠|\#|\＄|\％|\＾|\＆|\＊|\（|\）|\－|\＿|\＝|\＋|\，|\《|\．|\》|\、|\？|\；|\：|\’|\”|\【|\｛|\】|\｝|\＼|\｜|\·|\～|\！|\＠|\＃|\￥|\％|\……|\…|\＆|\×|\（|\）|\－|\——|\—|\＝|\＋|\，|\《|\。|\》|\、|\？|\；|\：|\‘|\“|\【|\｛|\】|\｝|\、|\||\·|\~|\！|\@|\#|\￥|\%|\……|\…|\&|\*|\（|\）|\-|\——|\=|\+/u','',$accountname);
                $label=strtoupper($label);
                $query="SELECT vendorid FROM `vtiger_uniquevendorname` WHERE vendorname=?";
                $result=$db->pquery($query,array($label));
                if($db->num_rows($result)){
                    $data=$db->raw_query_result_rowdata($result,0);
                    $accountidStr=',accountid='.$data['vendorid'];
                }else{
                    $data = array('flag'=>false,'msg'=>'没有找到汇款抬头对应的供应商,不能修改状态');
                    break;
                }
            }
            if($receivedstatus=='NonPayCertificate'){
                if(empty($relatetoid)){
                    $resData['msg']='合同必需存在!';
                    break;
                }
                if($status=='normal'){
                    $accountidStr=',relatetoid=0';
                }else{
                    $data = array('flag'=>false,'msg'=>'状态未提供代付款证明的只能转为正常');
                    break;
                }
            }
            if($receivedstatus=='deposit'){
                if($status=='normal'){
                    $accountidStr=',relatetoid=0';
                }else{
                    $data = array('flag'=>false,'msg'=>'保证金不能更改为其他类型！');
                    break;
                }
            }
            $sql = "UPDATE vtiger_receivedpayments set receivedstatus=?{$accountidStr} where receivedpaymentsid=?";
            $db->pquery($sql, array($status, $recordId));
            $id = $db->getUniqueId('vtiger_modtracker_basic');
            $datetime=date('Y-m-d H:i:s');
            $db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                array($id, $recordId, 'ReceivedPayments', $current_user->id, $datetime, 0));
            $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                array($id, 'receivedstatus',$receivedstatus,$status));
            if(in_array($receivedstatus,array('NonPayCertificate','deposit'))){
                $relatetoid=$recordModel->get('relatetoid');
                $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                    Array($id, 'relatetoid', $relatetoid, 0));
            }
            $data = array('flag'=>true,'msg'=>'更改回款状态成功');


            if($status=='void'){
                $sql = "UPDATE vtiger_refundtimeoutaudit set modulestatus='c_cancel' where receivedpaymentsid=?";
                $db->pquery($sql, array($recordId));
                //工作流表
                $sql = "select * from  vtiger_refundtimeoutaudit  where receivedpaymentsid=?";
                $sel_result = $db->pquery($sql, array($recordId));
                $res_cnt = $db->num_rows($sel_result);
                if($res_cnt > 0) {
                    $row = $db->query_result_rowdata($sel_result, 0);
                    $sql = "UPDATE vtiger_salesorderworkflowstages set isaction=0 where salesorderid=?";
                    $db->pquery($sql, array($row['refundtimeoutauditid']));
                }
            }

            if($receivedstatus=='deposit' && $status=='normal'){
                $db->pquery("update vtiger_receivedpayments set staypaymentid='',ismanualmatch=0 where receivedpaymentsid=?",array($recordId));
                $result = $db->pquery("select staypaymentid from vtiger_receivedpayments where receivedpaymentsid=?",array($recordId));
                if($db->num_rows($result)){
                    $row = $db->fetchByAssoc($result,0);
                    if($row['staypaymentid']){
                        $db->pquery("update vtiger_staypayment set surplusmoney=surplusmoney+? where staypaymentid=?",array($standardmoney,$row['staypaymentid']));
                    }
                }
            }
        }while(0);
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * 关联开票信息
     * @param Vtiger_Request $request
     */
    public function preview(Vtiger_Request $request){
        $startdate=$request->get('datatime');
        $enddatatime=$request->get('enddatatime');
        $cmodule=$request->get('cmodule');
        do{
            if ($cmodule == 1) {
                $cmodulename = 'ServiceContracts';
            } elseif ($cmodule == 2) {
                $cmodulename = 'ReceivedPayments';
            } else {
                break;
            }
            $Permissions = ReceivedPayments_Record_Model::getImportUserPermissions($cmodulename);
            //$Permissions='H4,H25';
            if ($Permissions) {
                if (strtotime($startdate) > strtotime($enddatatime)) {
                    $Temptime = $startdate;
                    $startdate = $enddatatime;
                    $enddatatime = $Temptime;
                }
                $Permissionstemp = explode(',', $Permissions);
                if ($cmodule == 1) {
                    $timechecked=$request->get('timeselected');
                    if($timechecked==1){
                        $checkedfield='vtiger_servicecontracts.signdate';
                        //$checkedlabel='签订日期';
                    }else{
                        $checkedfield='vtiger_servicecontracts.returndate';
                        //$checkedlabel='归还日期';
                    }
                    $Temparray = array();
                    foreach ($Permissionstemp as $value) {
                        $userids = getDepartmentUser($value);
                        $Temparray = array_merge($Temparray, $userids);
                    }
                    $Temparray = array_unique($Temparray);
                    $ServiceContractsSql = "SELECT vtiger_servicecontracts.contract_no, vtiger_account.accountname, vtiger_servicecontracts.contract_type, vtiger_servicecontracts.servicecontractstype, ( vtiger_products.productname ) AS productid, vtiger_servicecontracts.modulestatus, IFNULL( ( SELECT CONCAT( last_name, '[', IFNULL( ( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ) ) ) AS last_name FROM vtiger_users WHERE vtiger_crmentity.smownerid = vtiger_users.id ), '--' ) AS smownerid, vtiger_servicecontracts.receivedate, ( SELECT CONCAT( last_name, '[', IFNULL( ( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ) ) ) AS last_name FROM vtiger_users WHERE vtiger_servicecontracts.signid = vtiger_users.id ) AS signid, vtiger_servicecontracts.signdate, ( SELECT CONCAT( last_name, '[', IFNULL( ( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ) ) ) FROM vtiger_users WHERE vtiger_servicecontracts.receiveid = vtiger_users.id ) AS receiveid, IF ( firstcontract = 1, '是', '否') AS firstcontract, vtiger_servicecontracts.returndate, vtiger_servicecontracts.currencytype, vtiger_servicecontracts.total, vtiger_servicecontracts.productsearchid, vtiger_servicecontracts.remark, vtiger_servicecontracts.firstreceivepaydate, vtiger_servicecontracts.servicecontractsid, (SELECT vtiger_users.last_name FROM vtiger_receivedpayments_notes LEFT JOIN vtiger_users ON vtiger_receivedpayments_notes.smownerid=vtiger_users.id WHERE vtiger_receivedpayments_notes.receivedpaymentsid=vtiger_receivedpayments.receivedpaymentsid LIMIT 1) AS receivedpaymentsnotes, (SELECT vtiger_receivedpayments.createdtime FROM vtiger_receivedpayments_notes LEFT JOIN vtiger_users ON vtiger_receivedpayments_notes.smownerid=vtiger_users.id WHERE vtiger_receivedpayments_notes.receivedpaymentsid=vtiger_receivedpayments.receivedpaymentsid LIMIT 1) AS receivedpaymentcreatedtime, IF(vtiger_account.frommarketing='1','是','否') AS frommarketing FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_servicecontracts.servicecontractsid = vtiger_crmentity.crmid LEFT JOIN vtiger_receivedpayments ON vtiger_receivedpayments.relatetoid=vtiger_servicecontracts.servicecontractsid LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_servicecontracts.sc_related_to LEFT JOIN vtiger_products ON vtiger_products.productid = vtiger_servicecontracts.productid LEFT JOIN vtiger_servicecomments ON ( vtiger_account.accountid = vtiger_servicecomments.related_to AND vtiger_servicecomments.assigntype = 'accountby') WHERE 1=1 and vtiger_crmentity.deleted=0 AND vtiger_servicecontracts.modulestatus='c_complete' AND LEFT({$checkedfield},10) between '{$startdate}' and '{$enddatatime}' AND vtiger_servicecontracts.receiveid in(" . implode(',', $Temparray) . ")";
                    global $adb;
                    require 'crmcache/departmentanduserinfo.php';
                    $result = $adb->run_query_allrecords($ServiceContractsSql);
                    $data = '<table id="tbl_Detail" class="table listViewEntriesTable" width="100%"><thead><tr>
                        <th nowrap><b>合同编号</b></th>
                        <th nowrap><b>客户</b></th>
                        <th nowrap><b>合同类型</b></th>
                        <th nowrap><b>领取日期</b></th>
                        <th nowrap><b>签订日期</b></th>
                        <th nowrap><b>归还日期</b></th>
                        <th nowrap><b>提单人</b></th>
                        <th nowrap><b>领取人</b></th>
                        <th nowrap><b>签订人</b></th>
                        <th nowrap><b>新增/续费</b></th>
                        <th nowrap><b>合同总额</b></th>
                        <th nowrap><b>合同状态</b></th>
                        <th nowrap><b>备注</b></th>
                        <th nowrap><b>匹配人</b></th>
                        <th nowrap><b>匹配时间</b></th>
                        <th nowrap><b>是否来自市场部</b></th>
                       </tr>
                        </thead><tbody>';
                    $datacontents = '';
                    foreach ($result as $value) {
                        $datacontents .= '<tr><td nowrap>'
                            . $value['contract_no'] . '</td><td nowrap>'
                            . $value['accountname'] . '</td><td nowrap>'
                            . $value['contract_type'] . '</td><td nowrap>'
                            . $value['receivedate'] . '</td><td nowrap>'
                            . $value['signdate'] . '</td><td nowrap>'
                            . $value['returndate'] . '</td><td nowrap>'
                            . $value['receiveid'] . '</td><td nowrap>'
                            . $value['signid'] . '</td><td nowrap>'
                            . $value['smownerid'] . '</td><td nowrap>'
                            . $value['servicecontractstype'] . '</td><td nowrap>'
                            . $value['total'] . '</td><td nowrap>已签收</td><td nowrap>'
                            . $value['remark'] . '</td><td nowrap>'
                            . $value['receivedpaymentsnotes'] . '</td><td nowrap>'
                            . $value['receivedpaymentcreatedtime'] . '</td><td nowrap>'
                            . $value['frommarketing'] . '</td></tr>';
                    }

                    $data .= $datacontents . "</tbody></table>";
                } else {
                    $childsql = '';
                    foreach ($Permissionstemp as $val) {
                        $childsql .= " parentdepartment like concat((SELECT parentdepartment FROM `vtiger_departments` WHERE departmentid='{$val}'),'%') OR";
                    }
                    $childsql = rtrim($childsql, ' OR');
                    $sql = " AND (vtiger_receivedpayments.departmentid in(SELECT departmentid FROM vtiger_departments WHERE {$childsql}) OR vtiger_receivedpayments.newdepartmentid in(SELECT departmentid FROM vtiger_departments WHERE {$childsql}))";
                    $paymentssql = "SELECT
                        vtiger_receivedpayments.unit_price,
                    vtiger_receivedpayments.reality_date,
                    vtiger_receivedpayments.departmentid,
                    vtiger_receivedpayments.paytitle,
                    vtiger_receivedpayments.newrenewa,
                    vtiger_receivedpayments.owncompany,
                    vtiger_receivedpayments.overdue,
                    vtiger_receivedpayments.newdepartmentid,
                    vtiger_receivedpayments.ismatchdepart,
                    vtiger_servicecontracts.contract_no,
                    vtiger_account.accountname
                    FROM
                        vtiger_receivedpayments
                    LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid = vtiger_receivedpayments.relatetoid
                    LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_receivedpayments.maybe_account
                    WHERE
                        LEFT(vtiger_receivedpayments.reality_date,10) between '{$startdate}' and '{$enddatatime}' {$sql}";
                    global $adb;
                    require 'crmcache/departmentanduserinfo.php';
                    $result = $adb->run_query_allrecords($paymentssql);
                    $data = '<table id="tbl_Detail" class="table listViewEntriesTable" width="100%"><thead><tr>
                        <th nowrap><b>原部门</b></th>
                        <th nowrap><b>匹配部门</b></th>
                        <th nowrap><b>汇款抬头</b></th>
                        <th nowrap><b>回款类型</b></th>
                        <th nowrap><b>公司账号</b></th>
                        <th nowrap><b>金额</b></th>
                        <th nowrap><b>入财日期</b></th>
                        <th nowrap><b>合同编号</b></th>
                        <th nowrap><b>可能客户</b></th>
                        <th nowrap><b>备注</b></th>
                       </tr>
                        </thead><tbody>';
                    $datacontents = '';
                    foreach ($result as $value) {
                        $datacontents .= '<tr><td>' . $cachedepartment[$value['departmentid']] . '</td><td>' . $cachedepartment[$value['newdepartmentid']] . '</td><td>' . $value['paytitle'] . '</td><td>' . $value['newrenewa'] . '</td><td>' . $value['owncompany'] . '</td><td>' . $value['unit_price'] . '</td><td>' . $value['reality_date'] . '</td><td>' . $value['contract_no'] . '</td><td>' . $value['accountname'] . '</td><td>' . $value['overdue'] . '</td></tr>';
                    }

                    $data .= $datacontents . "</tbody></table>";
                }
            } else {
                break;
            }
        }while(0);

        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
    function add(Vtiger_Request $request){
        $userid=$request->get("userid");
        $cmoduler=$request->get("cmoduler");
        $cmodules=$request->get("cmodules");
        $dempartcontracts=$request->get("dempartcontracts");
        $dempartpayments=$request->get("dempartpayments");
        $data='添加失败';
        do {
            if(empty($userid)){
                break;
            }
            if(($cmodules != 'ServiceContracts' || $dempartcontracts=='null')&& ($cmoduler != 'ReceivedPayments'|| $dempartpayments=='null')) {
                break;
            }
            $value='';
            if ($cmoduler=='ReceivedPayments' && $dempartpayments!='null'){
                $value.="({$userid},'".implode(',',$dempartpayments)."','ReceivedPayments'),";
            }
            if ($cmodules=='ServiceContracts' && $dempartcontracts!='null'){
                $value.="({$userid},'".implode(',',$dempartcontracts)."','ServiceContracts')";
            }
            $value=rtrim($value,',');
            $sql="INSERT INTO vtiger_custompermtable(userid,permissions,`module`) VALUES{$value}";
            $delsql="DELETE FROM vtiger_custompermtable WHERE userid=?";
            $db=PearDatabase::getInstance();
            $db->pquery($delsql,array($userid));
            $db->pquery($sql,array());
            $data='添加成功';
        }while(0);
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();

    }
    function delete(Vtiger_Request $request){
        $id=$request->get("id");
        $delsql="DELETE FROM vtiger_custompermtable WHERE custompermtableid=?";
        $db=PearDatabase::getInstance();
        $db->pquery($delsql,array($id));
        $data='更新成功';
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();

    }

    /**
     * 重新匹配回款
     * @param $receivepayid
     * @param $contractid
     * @param $total
     * @param $Signid
     */
    public function replaceReciveData($receivepayid,$contractid,$total,$Signid){
        global $adb;
        $user = CRMEntity::getInstance('Users');
        $currentUser = $user->retrieveCurrentUserInfoFromFile($Signid);
        $currentid = $currentUser->id;
        $last_name = $currentUser->last_name;
        $user_departments = $currentUser->departmentid;//匹配部门

        $input = array();
        $sql = "UPDATE vtiger_receivedpayments SET ismatchdepart=1,matchdate='".date('Y-m-d')."',relatetoid = ?,newdepartmentid=?,matcherid=? WHERE receivedpaymentsid = ?";
        $sql_type = "UPDATE vtiger_receivedpayments SET newrenewa = ? WHERE receivedpaymentsid = ?";
        $update_achieve = "INSERT INTO vtiger_achievementallot (achievementallotid,owncompanys,receivedpaymentownid,scalling,servicecontractid,receivedpaymentsid,businessunit,matchdate,departmentid)
                            SELECT NULL ,owncompanys,receivedpaymentownid,scalling,servicecontractid,?,?*(scalling/100),'".date('Y-m-d')."',signdempart FROM vtiger_servicecontracts_divide WHERE servicecontractid = ? ";
        $deltet_sql = "DELETE  FROM  vtiger_achievementallot WHERE receivedpaymentsid = ?";
        $insert_history = "INSERT INTO vtiger_receivedpayments_matchhistory  (time,creatid,contractid,receivement) VALUES(NOW(),?,?,?)";

        if($receivepayid && $contractid && $total){
            $receivepayment_data = Vtiger_Record_Model::getInstanceById($receivepayid,'ReceivedPayments');
            $ttt=$receivepayment_data->getdata();
            $reality_date = $ttt['reality_date']; //回款的信息；
            $contra_data = Vtiger_Record_Model::getInstanceById($receivepayid,'ReceivedPayments');
            $tttt = $contra_data->getdata();
            $contract_type = $tttt['servicecontractstype'];
            $adb->pquery($sql,array($contractid,$user_departments,$currentid,$receivepayid));
            $adb->pquery($deltet_sql,array($receivepayid));
            $adb->pquery($update_achieve,array($receivepayid,$total,$contractid));//跟新分成历史
            $adb->pquery($insert_history,array($currentid,$contractid,$receivepayid));//匹配历史

            //更新首次回款时间;
            if(!empty($contractid)){
                if($adb->num_rows($adb->pquery('SELECT * FROM vtiger_receivedpayments WHERE relatetoid = ?',array($contractid)))==1){
                    $adb->pquery('UPDATE vtiger_servicecontracts SET firstreceivepaydate = ? WHERE servicecontractsid = ?',array($reality_date,$contractid));
                }

                if($contract_type=='新增' && $adb->num_rows($adb->pquery('SELECT * FROM vtiger_receivedpayments WHERE relatetoid = ?',array($contractid)))==0 ){
                    $adb->pquery($sql_type,array('新增',$receivepayid));
                }else{
                    $adb->pquery($sql_type,array('续费',$receivepayid));
                }

            }
            ReceivedPayments_Record_Model::save_modules($receivepayid,$contractid,$input);

            //回款总金额
            $unit_price_total = 0;
            $sql = "select sum(unit_price) as unit_price_total from vtiger_receivedpayments where receivedstatus='normal' and deleted=0 and relatetoid=?";
            $sel_result = $adb->pquery($sql, array($contractid));
            $res_cnt = $adb->num_rows($sel_result);
            if ($res_cnt > 0) {
                $receivedpayments_row = $adb->query_result_rowdata($sel_result, 0);
                $unit_price_total = $receivedpayments_row['unit_price_total'];
            }
            //更新合同已回款金额
            $sql = "update vtiger_servicecontracts set accountsdue=? where servicecontractsid=?";
            $adb->pquery($sql, array($unit_price_total, $contractid));

            // 更新 是否正常状态 2016-10-10 周海
            // 1)   当合同金额>0的情况下，若合同的收款金额>=合同金额，则合同自动关闭
            $sql = "select * from vtiger_servicecontracts where servicecontractsid=? AND isautoclose='1'";
            $sel_result = $adb->pquery($sql, array($contractid));
            $res_cnt = $adb->num_rows($sel_result);
            if($res_cnt > 0) {
                $row = $adb->query_result_rowdata($sel_result, 0);

                // 回款的金额
                /*$sql = "select sum(unit_price) AS unit_price_total  from vtiger_receivedpayments where relatetoid=?";
                $sel_result = $adb->pquery($sql, array($contractid));
                $res_cnt = $adb->num_rows($sel_result);
                if ($res_cnt > 0) {
                    $receivedpayments_row = $adb->query_result_rowdata($sel_result, 0);
                }*/

                // 合同金额>0  回款金额>=合同金额
                if (intval($row['total']) > 0 && intval($unit_price_total) >= intval($row['total']) ) {
                    // 合同自动关闭
                    $sql = "update vtiger_servicecontracts set contractstate=? where servicecontractsid=? AND isautoclose='1'";
                    $adb->pquery($sql,array('1', $contractid));
                }
            }

            // 匹配回款时，对回款做更新记录 vtiger_modtracker_basic
            $modtrackerBasicData = array();
            global $current_user;
            $modtrackerBasicId = $adb->getUniqueID("vtiger_modtracker_basic");
            $modtrackerBasicData['id'] = $modtrackerBasicId;
            $modtrackerBasicData['crmid'] = $receivepayid;
            $modtrackerBasicData['module'] = 'ReceivedPayments';
            $modtrackerBasicData['whodid'] = $current_user->id;
            $modtrackerBasicData['changedon'] = date('Y-m-d H:i:s');
            $modtrackerBasicData['status'] = '0';
            $divideNames = array_keys($modtrackerBasicData);
            $divideValues = array_values($modtrackerBasicData);
            $adb->pquery('INSERT INTO `vtiger_modtracker_basic` ('. implode(',', $divideNames).') VALUES ('. generateQuestionMarks($divideValues) .')',$divideValues);

            $sql = "select * from vtiger_servicecontracts where servicecontractsid=?";
            $sel_result = $adb->pquery($sql, array($contractid));
            $res_cnt = $adb->num_rows($sel_result);
            $contract_no = '';
            $accountid = 0;
            if($res_cnt > 0) {
                $row = $adb->query_result_rowdata($sel_result, 0);
                $contract_no = $row['contract_no'];
                $accountid = $row['sc_related_to'];
                $invoicecompany = $row['invoicecompany'];
            }

            // vtiger_modtracker_detail
            $modtrackerDetailData = array();
            $modtrackerDetailData['id'] = $modtrackerBasicId;
            $modtrackerDetailData['fieldname'] = 'overdue';
            $modtrackerDetailData['prevalue'] = '';
            $modtrackerDetailData['postvalue'] = $last_name.' 重新匹配回款，合同编号='.$contract_no;


            $divideNames = array_keys($modtrackerDetailData);
            $divideValues = array_values($modtrackerDetailData);
            $adb->pquery('INSERT INTO `vtiger_modtracker_detail` ('. implode(',', $divideNames).') VALUES ('. generateQuestionMarks($divideValues) .')',$divideValues);

            // 回款记录
            $receivedpaymentsNotesId = $adb->getUniqueID("vtiger_receivedpayments_notes");
            $receivedpaymentsNotesData = array(
                'createtime'=>date('Y-m-d H:i:s'),
                'smownerid'=>$currentid,
                'receivedpaymentsid'=>$receivepayid,
                'notestype'=>'notestype1',
                'receivedpaymentsnotesid'=>$receivedpaymentsNotesId
            );
            $divideNames = array_keys($receivedpaymentsNotesData);
            $divideValues = array_values($receivedpaymentsNotesData);
            $adb->pquery('INSERT INTO `vtiger_receivedpayments_notes` ('. implode(',', $divideNames).') VALUES ('. generateQuestionMarks($divideValues) .')',$divideValues);
            // 回款匹配修改客户的垫款
            if ($accountid > 0) {
                //Accounts_Record_Model::setAdvancesmoney($accountid, - $total, '(回款直接匹配合同)');
            }
        }
    }

    /**
     * @param Vtiger_Request $request
     * @author: steel.liu
     * @Date:xxx
     * 手动扣款
     */
    public function chargebacks(Vtiger_Request $request){
        $recordId=$request->get('record');
        $chargebacks=$request->get('chargebacksvalue');
        $chargebacksremark=$request->get('chargebacksremark');
        $recordModel=Vtiger_Record_Model::getInstanceById($recordId,'ReceivedPayments');
        $rechargeableamount=$recordModel->get('rechargeableamount');
        $oldchargebacks=$recordModel->get('chargebacks');
        $chargebacksremak=$recordModel->get('chargebacksremak');


        do{
            if(!$recordModel->personalAuthority('ReceivedPayments','dochargebacks')){
                //权限验证
                $data=array('flag'=>false,'msg'=>'没有权限');
                break;
            }

            if(bccomp($chargebacks,0)<=0){
                $data=array('flag'=>false,'msg'=>'扣款金额必需大于0');
                //扣款金额大于0
                break;
            }
            if(bccomp($rechargeableamount,$chargebacks)<0){
                //扣款金额小于等于可使用金额
                $data=array('flag'=>false,'msg'=>'扣款金额大于可充值金额!');
                break;
            }
            $changerechargeableamount=bcsub($rechargeableamount,$chargebacks,2);
            $currentchargebacks=bcadd($chargebacks,$oldchargebacks,2);
            global $adb,$current_user;
            $datetime=date('Y-m-d H:i:s');
            $chargebacksmsg='*|*'.$current_user->id.'##'.$datetime.'##'.$chargebacks.'##'.$chargebacksremark;
            $query="UPDATE vtiger_receivedpayments SET rechargeableamount=?,chargebacks=?,chargebacksremak=?,chargebacksmsg=CONCAT(IFNULL(chargebacksmsg,''),'{$chargebacksmsg}') WHERE receivedpaymentsid=?";
            $adb->pquery($query,array($changerechargeableamount,$currentchargebacks,$chargebacksremark,$recordId));
            $data=array('flag'=>true);
            $currentTime = date('Y-m-d H:i:s');
            $id = $adb->getUniqueId('vtiger_modtracker_basic');
            $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                array($id, $recordId, 'ReceivedPayments', $current_user->id, $currentTime, 0));
            $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                Array($id, 'chargebacks', $oldchargebacks, $currentchargebacks));
            $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                Array($id, 'rechargeableamount', $rechargeableamount, $changerechargeableamount));
            $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                Array($id, 'chargebacksremak', $chargebacksremak, $chargebacksremark));
        }while(0);
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();

    }
    /**
     * 设为未提供代付款证明
     * @param Vtiger_Request $request
     * @author: steel.liu
     * @Date:xxx
     *
     */
    public function NonPayCertificate(Vtiger_Request $request){
        $contractno=$request->get('contractno');
        $recordid=$request->get('record');
        $recordModel=Vtiger_Record_Model::getInstanceById($recordid,'ReceivedPayments');
        $relatetoid=$recordModel->get('relatetoid');
        $receivedstatus=$recordModel->get('receivedstatus');
        $returnMsg=array('flag'=>true,'msg'=>'');
        do{
            if(!$recordModel->personalAuthority('ReceivedPayments','NonPayCertificate')){
                $returnMsg=array('flag'=>false,'msg'=>'没有操作权限!');
                break;
            }
            if($relatetoid>0){
                $returnMsg=array('flag'=>false,'msg'=>'已匹配的回款不允许操作!');
                break;
            }
            if($receivedstatus!='normal'){
                $returnMsg=array('flag'=>false,'msg'=>'状态为正常的合同才能匹配!');
                break;
            }
            global $adb,$current_user;
            $contractno=trim($contractno);
            $query="SELECT vtiger_servicecontracts.* FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_servicecontracts.servicecontractsid WHERE vtiger_crmentity.deleted=0 AND vtiger_servicecontracts.modulestatus='c_complete' AND vtiger_servicecontracts.contractstate=0 AND vtiger_servicecontracts.contract_no=?";
            $result=$adb->pquery($query,array($contractno));
            if(!$adb->num_rows($result)){
                $returnMsg=array('flag'=>false,'msg'=>'该合同编号不允许操作,只有已签收且未关闭的才能操作!');
                break;
            }
            $data=$adb->raw_query_result_rowdata($result,0);
            $adb->pquery("UPDATE vtiger_receivedpayments SET relatetoid=?,receivedstatus='NonPayCertificate' WHERE receivedpaymentsid=?",array($data['servicecontractsid'],$recordid));
            $currentTime = date('Y-m-d H:i:s');
            $id = $adb->getUniqueId('vtiger_modtracker_basic');
            $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                array($id, $recordid, 'ReceivedPayments', $current_user->id, $currentTime, 0));
            $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                Array($id, 'relatetoid', $relatetoid, $data['servicecontractsid']));
            $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                Array($id, 'receivedstatus', $receivedstatus, 'NonPayCertificate'));
        }while(0);
        $response = new Vtiger_Response();
        $response->setResult($returnMsg);
        $response->emit();

    }

    /**
     * @param Vtiger_Request $request
     * @author: steel.liu
     * @Date:xxx
     * 修改可使用金额
     */
    public function changerechargeableamount(Vtiger_Request $request){
        $recordId=$request->get('record');
        $inputrechargeable=$request->get('rechargeableamount');
        $recordModel=Vtiger_Record_Model::getInstanceById($recordId,'ReceivedPayments');
        $rechargeableamount=$recordModel->get('rechargeableamount');//可使用金额
        $oldchargebacks=$recordModel->get('chargebacks');//扣款金额
        $unit_price=$recordModel->get('unit_price');//入账金额
        $standardmoney=$recordModel->get('standardmoney');//原币金额
        $occupationcost=$recordModel->get('occupationcost');//工单成本
        $maxChangeMoney=bcsub($standardmoney,$oldchargebacks,2);//剩余的可使用金额，原币金额
        $maxChangeMoney=bcsub($maxChangeMoney,$occupationcost,2);//剩余的可使用金额


        do{
            if(!$recordModel->personalAuthority('ReceivedPayments','dorechargeable')){
                //权限验证
                $data=array('flag'=>false,'msg'=>'没有权限');
                break;
            }

            if($inputrechargeable<0){
                $data=array('flag'=>false,'msg'=>'可使用金额必需大于等于0');
                //扣款金额大于0
                break;
            }
            if(bccomp($maxChangeMoney,$inputrechargeable,2)<0){
                //扣款金额小于等于可使用金额
                $data=array('flag'=>false,'msg'=>'可充值金额大于入账金额扣款金额之差!');
                break;
            }
            global $adb,$current_user;
            $query="UPDATE vtiger_receivedpayments SET rechargeableamount=? WHERE receivedpaymentsid=?";
            $adb->pquery($query,array($inputrechargeable,$recordId));
            $data=array('flag'=>true);
            $currentTime = date('Y-m-d H:i:s');
            $id = $adb->getUniqueId('vtiger_modtracker_basic');
            $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                array($id, $recordId, 'ReceivedPayments', $current_user->id, $currentTime, 0));
            $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                Array($id, 'rechargeableamount', $rechargeableamount, $inputrechargeable));

        }while(0);
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * @param Vtiger_Request $request
     * @author: steel.liu
     * @Date:xxx
     * 回款状态更改为返点款
     */
    public function dobackcash(Vtiger_Request $request){
        $record=$request->get('record');
        $recordModel=Vtiger_Record_Model::getInstanceById($record,'ReceivedPayments');
        global $current_user,$adb;
        $data=array('flag'=>false,'msg'=>'非法操作');
        $relatetoid=$recordModel->get('relatetoid');
        $unit_price=$recordModel->get('unit_price');//入账金额
        $standardmoney=$recordModel->get('standardmoney');//原币金额
        $rechargeableamount=$recordModel->get('rechargeableamount');//可使用金额
        $allowinvoicetotal=$recordModel->get('allowinvoicetotal');//可开票金额
        $receivedstatus=$recordModel->get('receivedstatus');
        $occupationcost=$recordModel->get('occupationcost');//已占用工单成本
        $chargebacks=$recordModel->get('chargebacks');//扣款金额
        if($recordModel->get('receivedstatus')=='normal'&&
            empty($relatetoid)&&
            $current_user->id==$recordModel->get('createid')&&
            bccomp($standardmoney,$rechargeableamount,2)!=0 &&
            bccomp($unit_price,$allowinvoicetotal,2)!=0 &&
            $occupationcost!=0 &&
            $chargebacks!=0){
            $accountname=$recordModel->get('paytitle');
            $label=preg_replace('/\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;|&quot;|　|&apos;|&amp;|&lt;|&gt;|&#039;|&ldquo;|&rdquo;|&lsquo;|&rsquo;|&hellip;|\“|\”|\‘|\〉|\〈|\’|\〖|\〗|\【|\】|\、|\·|\……|\…|\——|\＋|\－|\＝|\,|\<|\.|\>|\/|\?|\;|\:|\\\'|\"|\[|\{|\]|\}|\\|\||\`|\~|\!|\@|\#|\$|\%|\^|\\&|\*|\(|\)|\-|\_|\=|\+|\，|\＜|\．|\＞|\／|\？|\；|\：|\＇|\＂|\［|\{|\］|\}|\＼|\｜|\｀|\￣|\！|\＠|\#|\＄|\％|\＾|\＆|\＊|\（|\）|\－|\＿|\＝|\＋|\，|\《|\．|\》|\、|\？|\；|\：|\’|\”|\【|\｛|\】|\｝|\＼|\｜|\·|\～|\！|\＠|\＃|\￥|\％|\……|\…|\＆|\×|\（|\）|\－|\——|\—|\＝|\＋|\，|\《|\。|\》|\、|\？|\；|\：|\‘|\“|\【|\｛|\】|\｝|\、|\||\·|\~|\！|\@|\#|\￥|\%|\……|\…|\&|\*|\（|\）|\-|\——|\=|\+/u','',$accountname);
            $label=strtoupper($label);
            $query="SELECT vendorid FROM `vtiger_uniquevendorname` WHERE vendorname=?";
            $result=$adb->pquery($query,array($label));
            if($adb->num_rows($result)){
                $data=$adb->raw_query_result_rowdata($result,0);
                $accountidStr=',accountid='.$data['vendorid'];
            }

            $query="UPDATE vtiger_receivedpayments SET receivedstatus='RebateAmount'{$accountidStr} WHERE receivedpaymentsid=?";
            $adb->pquery($query,array($record));
            $currentTime = date('Y-m-d H:i:s');
            $id = $adb->getUniqueId('vtiger_modtracker_basic');
            $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                array($id, $record, 'ReceivedPayments', $current_user->id, $currentTime, 0));
            $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                Array($id, 'receivedstatus', 'normal', 'RebateAmount'));
            $data=array('flag'=>true);
        }
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();

    }

    /**
     * 单个核对
     */
    public function collate(Vtiger_Request $request)
    {
        $recordid = $request->get('recordid');
        $checkresult = $request->get('checkresult');
        $remark = $request->get('remark');
        global $current_user,$adb;
        $query = 'SELECT collate_num, first_collate_status FROM vtiger_receivedpayments WHERE receivedpaymentsid=?';
        $result = $adb->pquery($query, [$recordid]);
        $num = $adb->num_rows($result);
        if ($num >0) {
            $recordModel=ReceivedPayments_Record_Model::getCleanInstance("ReceivedPayments");
            if($recordModel->isCollateInReview($recordid)){
                $data = ['status' => 'error', 'msg' => '回款核对审核中,不能操作'];
                $response = new Vtiger_Response();
                $response->setResult($data);
                $response->emit();
                exit();
            }

            $now = date('Y-m-d H:i:s');
            $contract = $adb->query_result_rowdata($result);
            //判断是否是首次核对
            if ( $contract['collate_num']>=1) {
                $query = 'UPDATE vtiger_receivedpayments SET collate_num=?, last_collate_status=?, last_collate_time=?, last_collate_operator=?, last_collate_remark=? WHERE receivedpaymentsid=?';
                $adb->pquery($query, [$contract['collate_num']+1, $checkresult, $now, $current_user->id, $remark, $recordid]);
            } else {
                $query = 'UPDATE vtiger_receivedpayments SET collate_num=?, first_collate_status=?, first_collate_time=?, first_collate_operator=?, first_collate_remark=? WHERE receivedpaymentsid=?';
                $adb->pquery($query, [1, $checkresult, $now, $current_user->id, $remark, $recordid]);
            }
            //插入核对日志
            $query = 'INSERT INTO vtiger_receivedpayments_collate_log(recordid, status, collate_time, remark, collator) VALUES (?, ?, ?, ?, ?)';
            $adb->pquery($query, [$recordid, $checkresult, $now, $remark, $current_user->id]);
            if($checkresult=='unfit'){
                $adb->pquery("UPDATE vtiger_crmentity,
vtiger_receivedpaymentscollate 
SET deleted = 1 
WHERE
	receivedpaymentscollateid = crmid 
	AND receivedpaymentsid =? 
	AND deleted = 0 ",
                    array($recordid));
            }
            $data = ['status' => 'success', 'msg' => '成功核对'];
        } else {
            $data = ['status' => 'error', 'msg' => '合同不存在'];
        }
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * 核对记录
     * @param Vtiger_Request $request
     */
    public function collateLog(Vtiger_Request $request)
    {
        global $adb;
        $recordid = $request->get('recordid');
        $query = "SELECT id, IF(status='fit', '符合', '不符合') AS status, remark, (SELECT CONCAT(last_name,'[',IFNULL((SELECT departmentname FROM vtiger_departments WHERE departmentid = (SELECT departmentid FROM vtiger_user2department WHERE userid=vtiger_users.id LIMIT 1)),''),']',(IF(`status`='Active' AND isdimission=0,'','[离职]'))) AS last_name FROM vtiger_users WHERE vtiger_receivedpayments_collate_log.collator=vtiger_users.id) AS collator, collate_time FROM vtiger_receivedpayments_collate_log WHERE recordid = ? ORDER BY id DESC";
        $result = $adb->pquery($query, [$recordid]);
        $num = $adb->num_rows($result);
        $list = [];
        if ($num > 0) {
            while ($row = $adb->fetchByAssoc($result)) {
                $list[] = $row;
            }
        }
        $response = new Vtiger_Response();
        $response->setResult($list);
        $response->emit();
    }

    /**
     * 批量核对
     */
    public function batchCollate(Vtiger_Request $request)
    {
        global $current_user, $adb, $currentView;
        $currentView = 'List';
        $checkresult = $request->get('checkresult');
        $remark = $request->get('remark');
        $listViewModel = Vtiger_ListView_Model::getInstance('ReceivedPayments');
        $listViewModel->getSearchWhere();
        $queryGenerator =$listViewModel->get('query_generator');
        //用户条件
        $where = $listViewModel->getUserWhere();
        $queryGenerator->addUserWhere($where);
        $listQuery = $queryGenerator->getQueryCount();
        $pattern = '/\(vtiger_servicecontracts.contract_no(?!,)/';
        $listQuery = preg_replace($pattern,'vtiger_receivedpayments.relatetoid IN(SELECT crm2.crmid FROM vtiger_crmentity AS crm2 WHERE crm2.setype in(\'ServiceContracts\',\'SupplierContracts\') AND crm2.deleted=0 AND crm2.label',$listQuery);
        $listQuery = str_replace('AND vtiger_servicecontracts.contract_no IS NOT NULL', '', $listQuery);
        $listQuery = str_replace('count(1) as counts', 'receivedpaymentsid, vtiger_receivedpayments.collate_num', $listQuery);
        $result = $adb->pquery($listQuery, []);
        $num = $adb->num_rows($result);
        $checkNum=0;
        $failNum=0;
        if ($num <= 0) {
            $data = ['status'=>'error', 'msg'=>'未查到需核对的数据'];
        } elseif($num>1000) {
            $data = ['status'=>'error', 'msg'=>sprintf('当前共%d条数据,超过单次允许核对的最大记录数(1000)', $num)];
        } else {
            $now = date('Y-m-d H:i:s');
            $recordModel=ReceivedPayments_Record_Model::getCleanInstance("ReceivedPayments");
            while ($row = $adb->fetchByAssoc($result)) {
                if($recordModel->isCollateInReview( $row['receivedpaymentsid'])){
                    $failNum++;
                    continue;
                }
                //判断之前是否核对过
                if ($row['collate_num'] >= 1) {
                    $query = 'UPDATE vtiger_receivedpayments SET collate_num=?, last_collate_status=?, last_collate_time=?, last_collate_operator=?, last_collate_remark=? WHERE receivedpaymentsid=?';
                    $adb->pquery($query, [$row['collate_num']+1, $checkresult, $now, $current_user->id, $remark, $row['receivedpaymentsid']]);
                } else {
                    $query = 'UPDATE vtiger_receivedpayments SET collate_num=?, first_collate_status=?, first_collate_time=?, first_collate_operator=?, first_collate_remark=? WHERE receivedpaymentsid=?';
                    $adb->pquery($query, [1, $checkresult, $now, $current_user->id, $remark,  $row['receivedpaymentsid']]);
                }
                //插入核对日志
                $query = 'INSERT INTO vtiger_receivedpayments_collate_log(recordid, status, collate_time, remark, collator) VALUES (?, ?, ?, ?, ?)';
                $adb->pquery($query, [$row['receivedpaymentsid'], $checkresult, $now, $remark, $current_user->id]);

                if($checkresult=='unfit'){
                    $adb->pquery("UPDATE vtiger_crmentity,
vtiger_receivedpaymentscollate 
SET deleted = 1 
WHERE
	receivedpaymentscollateid = crmid 
	AND receivedpaymentsid =? 
	AND deleted = 0 ",
                        array($row['receivedpaymentsid']));
                }
                $checkNum++;
            }
            $msg = "成功核对".$checkNum."条数据";
            if($failNum){
                $msg .= ', 失败条数'.$failNum.',原因是回款核对审核中';
            }
            $data = ['status'=>'success', 'msg'=>$msg];
        }
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * 导出数据
     * @param Vtiger_Request $request
     */
    public function exportData(Vtiger_Request $request)
    {
        set_time_limit(0);
        global $current_user,$root_directory,$adb,$currentView;
        $currentView = 'List';
        $listViewModel = Vtiger_ListView_Model::getInstance('ReceivedPayments');
        $listQuery = $listViewModel->getQuery();
        $listViewModel->getSearchWhere();
        $listQuery .= $listViewModel->getUserWhere();
        $queryGenerator = $listViewModel->get('query_generator');
        //获取自定义语句拼接方法
        $pattern='(vtiger_servicecontracts.contract_no) as';
        $listQuery=str_replace($pattern,'(SELECT crm.label FROM vtiger_crmentity as crm WHERE crm.crmid=vtiger_receivedpayments.relatetoid ) AS ',$listQuery);
        $searchwhere=$queryGenerator->getSearchWhere();
        if(!empty($searchwhere)){
            $listQuery.=' and '.$searchwhere;
        }
        $pattern='/\(vtiger_servicecontracts.contract_no(?!,)/';
        $listQuery=preg_replace($pattern,'vtiger_receivedpayments.relatetoid IN(SELECT crm2.crmid FROM vtiger_crmentity AS crm2 WHERE crm2.setype in(\'ServiceContracts\',\'SupplierContracts\') AND crm2.deleted=0 AND crm2.label',$listQuery);
        $listQuery = str_replace('AND vtiger_servicecontracts.contract_no IS NOT NULL', '', $listQuery);
        $LISTVIEW_FIELDS = $listViewModel->getSelectFields();
        $listViewHeaders = $listViewModel->getListViewHeaders();
        $temp = array();
        if (!empty($LISTVIEW_FIELDS)) {
            foreach ($LISTVIEW_FIELDS as $key => $val) {
                if (isset($listViewHeaders[$key])) {
                    if($listViewHeaders[$key]['ishidden']){
                        continue;
                    }
                    $temp[$key] = $listViewHeaders[$key];
                }
            }
        }
        if(empty($temp)) {
            $temp = $listViewHeaders;
        }
        $headerArray = $temp;
        ini_set('memory_limit','1024M');
        $path = $root_directory.'temp/';
        !is_dir($path) && mkdir($path,'0755',true);
        $filename = $path.'回款'.date('Ymd').$current_user->id.'.csv';
        $array= array();
        foreach($headerArray as $key=>$value) {
            $array[] = iconv('utf-8','gb2312',vtranslate($key,'ReceivedPayments'));
        }
        $fp = fopen($filename,'w');
        fputcsv($fp, $array);
        $limit = 5000;
        $i = 0;
        while(true){
            $limitSQL = " limit " . $i * $limit . ",". $limit;
            $i++;
            $result = $adb->pquery($listQuery . $limitSQL, array());
            if($adb->num_rows($result)){
                while ($value = $adb->fetch_array($result)) {
                    $array = array();
                    foreach ($headerArray as $keyheader => $valueheader) {
                        if (in_array($valueheader['columnname'], ['first_collate_status', 'last_collate_status'])) {
                            if ($value[$valueheader['columnname']]=='fit') {
                                $currnetValue = '符合';
                            } elseif($value[$valueheader['columnname']]=='unfit') {
                                $currnetValue = '不符合';
                            } else {
                                $currnetValue = '';
                            }
                        } else {
                            $currnetValue = uitypeformat($valueheader, $value[$valueheader['columnname']], 'ReceivedPayments');
                        }
                        $currnetValue=preg_replace('/<[^>]*>/','',$currnetValue);
                        $currnetValue = iconv('utf-8', 'GBK//IGNORE', $currnetValue);
                        $array[] = $currnetValue;
                    }
                    fputcsv($fp, $array);
                }
                ob_flush();
                flush();
            }else{
                break;
            }
        }
        fclose($fp);
        $response=new Vtiger_Response();
        $response->setResult(array());
        $response->emit();
    }

    public function exportFile()
    {
        global $site_URL,$current_user;
        header('location:'.$site_URL.'temp/'.'回款'.date('Ymd').$current_user->id.'.csv');
        exit;
    }


    /**
     * 通过渠道获取公司账户
     * @param Vtiger_Request $request
     */
    public function getCompanyAccountsByChannel(Vtiger_Request $request){
        global $adb;
        $channel=$request->get('channel');
        $data=array('flag'=>false);
        if($channel){
            $datas=array();
            $sql="select company,CONCAT(IF(bank is null,'',bank),IF(subbank is null,'',CONCAT('-',subbank)),IF(account IS NULL,'',CONCAT('（',account,'）'))) as item from vtiger_companyaccounts where channel='".$channel."'";
            $lists=$adb->run_query_allrecords($sql);
            foreach ($lists as $list){
                $datas[$list['company']].=str_replace(PHP_EOL, '', $list['item']) .'|';
            }
            $datasString="[{name:'--请选择--',subname:'--请选择--'},";
            foreach ($datas as $key => $data){
                $datasString.="{name:'".$key."',subname: '".rtrim($data,'|')."'},";
            }
            $datasString.="]";
            $data=array('flag'=>true,'dataString'=>$datasString);
        }
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * 删除回款
     * @param Vtiger_Request $request
     */
    public function delReceive(Vtiger_Request $request){
        global $adb;
        $recordId=$request->get('record');
        $data=array('flag'=>false,'msg'=>'删除失败');
        if($recordId){
            $sql="select receivedpaymentsid from vtiger_receivedpayments where receivedstatus=? and ( relatetoid = 0 OR relatetoid IS NULL  or  relatetoid='') and deleted = 0 and receivedpaymentsid not in (SELECT distinct receivedpaymentsid FROM vtiger_achievementallot_statistic WHERE  isover=1) and receivedpaymentsid=?";
            $result=$adb->pquery($sql,array('normal',$recordId));
            if($adb->num_rows($result)>0){
                //删除
                $sql="update vtiger_receivedpayments set vtiger_receivedpayments.deleted=1 where  vtiger_receivedpayments.receivedpaymentsid=?";
                $adb->pquery($sql,array($recordId));
                $data=array('flag'=>true);
            }else{
                $sql="select * from vtiger_receivedpayments where receivedpaymentsid=?";
                $result=$adb->pquery($sql,array($recordId));
                $relatetoid=$adb->query_result($result,0,'relatetoid');
                $allowinvoicetotal=$adb->query_result($result,0,'allowinvoicetotal');
                $unit_price=$adb->query_result($result,0,'unit_price');
                $rechargeableamount=$adb->query_result($result,0,'rechargeableamount');
                if($relatetoid>0){
                    $data=array('flag'=>false,'msg'=>'已匹配合同，无法删除');
                }else if(bccomp($unit_price,$allowinvoicetotal,2)!=0){
                    $data=array('flag'=>false,'msg'=>'已开发票，无法删除');
                }else if(bccomp($unit_price,$rechargeableamount,2)!=0){
                    $data=array('flag'=>false,'msg'=>'充值申请单或工单等已使用，无法删除');
                }
            }
        }
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

}
