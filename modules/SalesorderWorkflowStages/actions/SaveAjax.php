<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
class SalesorderWorkflowStages_SaveAjax_Action extends Vtiger_SaveAjax_Action {
    private $realoperate='herejump';
	function __construct() {
		parent::__construct();
		$this->exposeMethod('showDetailViewByMode');
		$this->exposeMethod('backSalesOrderWorkflowsstages');
		$this->exposeMethod('updateWorkflowSchedule');
		$this->exposeMethod('updateSalseorderWorkflowStages');
		$this->exposeMethod('submitremark');
		$this->exposeMethod('editremark');//修改备注函数
		$this->exposeMethod('backall');
	}
	public function checkPermission(Vtiger_Request $request) {
		//2014-12-05 是否有审核权限,管理员直接忽略
		//	如果是非管理员，流程id属于自己才能审核，没有上级代替审核
		//	非管理员，如果审核人id=自己才能审核
		//++++++++++++++++++
		
		
		$modulename = $request->get('src_module');
		$record = $request->get('record');
		$stagerecordid=$request->get('stagerecordid');
		$currentUser=Users_Record_Model::getCurrentUserModel();
		if(!$currentUser->isAdminUser()){
			$module=SalesorderWorkflowStages_Record_Model::getInstanceById(0);
			$result=$module->getWorkflowsStatus($modulename, $record,$stagerecordid);
			if(!empty($result)){
				if(!$result['success']){
					throw new AppException($result['msg']);
					return false;
				}else{
					return true;
				}
			}else{
				return false;
			}
		}
		return true;
		//end
		
	}
	//审核备注信息修改
	public function editremark(Vtiger_Request $request){
	    $salesorderhistoryid = $request->get('salesorderhistoryid');
	    $editremarkval = $request->get('editremarkval');
	    $time=getDateFormat();
	    $db=PearDatabase::getInstance();
	    $sql = "UPDATE `vtiger_salesorderremark` SET reject = ?,modifytime=? WHERE salesorderhistoryid =?";
	    $result = $db->pquery("$sql",array($editremarkval,$time,$salesorderhistoryid));
	    $response = new Vtiger_Response();
	    $response->setEmitType(Vtiger_Response::$EMIT_JSON);
	    $response->setResult($result);
	    $response->emit();
	}
	/**
	 * wangbin 2015-03-04 
	 * 工单审核时添加备注功能
	 */
	public function submitremark(Vtiger_Request $request){
            $db = PearDatabase::getInstance();
	    //备注时间  备注人  备注信息 备注节点
	    $record = $request->get('record');
	    $reject = $request->get('reject');           
	    $rejectname=$request->get('rejectname');     
	    $stagerecordid=$request->get('stagerecordid'); 
	    $isbackname = $request->get('isbackname');
	    $isbackid = $request->get('isrejectid');  
	    $modulename = $request->get('src_module');     
	    $currentUserModel = Users_Record_Model::getCurrentUserModel();
	    $userid=$currentUserModel->getId(); 


        $time = getDateFormat();
       
        $salesorderhistory = $db->pquery('insert into vtiger_salesorderremark (`reject`,`salesorderid`,`rejecttime`,`rejectid`,`rejectname`,`workflowerstagesid`,`modulename`,`rejectnameto`) values(?,?,?,?,?,?,?,?)', array($reject, $record, $time, $userid, $rejectname, $stagerecordid, $modulename, $isbackname));

        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($salesorderhistory);
        $response->emit();
    }

    /**
     * wangbin 2015-03-05 13:45:19
     * 修改审核时打回到最初审核节点
     * @param Vtiger_Request $request
     */
    public function backall(Vtiger_Request $request) {
        // 判断是移动端
        $isMobileCheck = $_REQUEST['isMobileCheck'];
        $request->set('isMobileCheck',$isMobileCheck);
        
        $record = $request->get('record');            //工单id
        $reject = $request->get('reject');           //打回原激活某个节点因
        $rejectname = $request->get('rejectname');     //打回节点名称
        $stagerecordid = $request->get('stagerecordid'); //打回节点id
        $isbackname = $request->get('isbackname');   //正在被打回的节点名称
        $isbackid = $request->get('isrejectid');     //正在被打回的节点id
        $modulename = $request->get('src_module');     //打回模块名称
        $actionnode = $request->get('actionnode');     //激活节点
        $db = PearDatabase::getInstance();
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $userid = $currentUserModel->getId();

        $time = getDateFormat();
        $focus = CRMEntity::getInstance($modulename);
        if (method_exists($focus, 'backallBefore')) {
            $error = $focus->backallBefore($request);
            if($isMobileCheck==1 && $error['success']=='false'){
                return $error;
            }
        }

        //工作节点打回到1并修改打回状态
        $sql7 = "SELECT sequence,workflowsid FROM vtiger_salesorderworkflowstages WHERE salesorderworkflowstagesid = ? AND salesorderid = ? AND modulename = ?";
        $sequence1 = $db->pquery($sql7,array($isbackid,$record,$modulename));
        if($db->num_rows($sequence1)){
            $sequ1 = $sequence1->fields['0'];  //大
            $workflowsid=$sequence1->fields['1'];
            $sqlWorkFlows=$workflowsid>0?" AND workflowsid={$workflowsid} ":'';//大
        }else{  //如果不是当前的节点时不允许提交的,防止手工提交审核
            if($isMobileCheck==1){
                $error['success']='false';
                $error['error']['message']='节点错误，请不要提交信息';
                return $error;
            }
            $response = new Vtiger_Response();
            //$response->setEmitType(Vtiger_Response::$EMIT_JSON);
            $response->setError('节点错误，请不要提交信息');
            $response->emit();
            return ;
        }

	    // 打回历史插入打回原因
        if(empty($reject)){
            $reject = '节点被重新激活';
        }
	    $db->startTransaction();
	    $db->pquery('insert into vtiger_salesorderhistory (`reject`,`salesorderid`,`rejecttime`,`rejectid`,`rejectname`,`workflowerstagesid`,`modulename`,`rejectnameto`) values(?,?,?,?,?,?,?,?)', array($reject,$record,$time,$userid,$rejectname,$stagerecordid,$modulename,$isbackname));

        if($actionnode){  //激活某个节点,同时激活次数加１，
            $sql6 = "UPDATE vtiger_salesorderworkflowstages SET isaction = 1,actiontime='".$time."',actioncount=actioncount+1 WHERE salesorderworkflowstagesid = ? AND salesorderid = ? AND modulename = ? ".$sqlWorkFlows;
            $result = $db->pquery($sql6,array($isbackid,$record,$modulename));
            $response = new Vtiger_Response();
            $response->setResult('激活成功');
            $response->emit();
            return ;
        }else{ //打回所有的节点
            $sql6 = "UPDATE vtiger_salesorderworkflowstages SET isaction = 0 WHERE salesorderid = ? AND modulename = ? AND sequence <= ?".$sqlWorkFlows;
            $db->pquery($sql6,array($record,$modulename,$sequ1));
            $sql7 = "UPDATE vtiger_salesorderworkflowstages SET isaction = 1 WHERE salesorderid = ? AND modulename = ? AND sequence = 1".$sqlWorkFlows;
            $db->pquery($sql7,array($record,$modulename));
        }
        //打回后消息提醒
        $this->sendWxBackAllRemind(array('salesorderid'=>$record,'salesorderworkflowstagesid'=>$isbackid));
        //去除工单审核节点名称 wangbin 2015年3月30日 星期一
	    if($modulename=='SalesOrder'){
            //追加更新是否匹配回款标志 gaocl add 2018/05/16
	        //$sql8 = "UPDATE `vtiger_salesorder` SET workflowsnode = '--' WHERE salesorderid = ?";
            $sql8 = "UPDATE `vtiger_salesorder` SET israyment=0,workflowsnode = '--' WHERE salesorderid = ?";
	        $db->pquery($sql8,array($record));

	        //工单匹配回款相关处理 gaocl add 2018/05/16
	        //还原回款数据
            $db->pquery("UPDATE vtiger_receivedpayments INNER JOIN vtiger_salesorderrayment ON(vtiger_salesorderrayment.receivedpaymentsid=vtiger_receivedpayments.receivedpaymentsid)
                        SET vtiger_receivedpayments.occupationcost=vtiger_receivedpayments.occupationcost-vtiger_salesorderrayment.laborcost-vtiger_salesorderrayment.purchasecost,
                        vtiger_receivedpayments.rechargeableamount=vtiger_receivedpayments.rechargeableamount+vtiger_salesorderrayment.laborcost+vtiger_salesorderrayment.purchasecost
                        WHERE vtiger_salesorderrayment.salesorderid=?",array($record));
	        //清除工单匹配回款数据
            $db->pquery("DELETE FROM vtiger_salesorderrayment WHERE salesorderid=?",array($record));
	    }
        //发票打回把领取人清完
        if($modulename=='Invoice'){
            $sql="UPDATE vtiger_invoice SET receiveid='',receivedate='' WHERE invoiceid=?";
            $db->pquery($sql,array($record));
        }
	    
        //重新修改打回状态,改变状态
        $sql5 = $this->getSql($modulename);
        $result = $db->pquery($sql5,array('a_exception',$record));
        $db->completeTransaction();
	    if(method_exists($focus,'backallAfter')){
            $focus->backallAfter($request);
        }

        //返回ajax
 	    $response = new Vtiger_Response();
 	    $response->setEmitType(Vtiger_Response::$EMIT_JSON);
 	    $response->setResult($result);
 	    $response->emit();
	}
    /**
     *   打回审核节点微信消息提醒
     */
    public function sendWxBackAllRemind($params){
        /**
         * 当前审核节点审核后 通知发起人审核进度
         */
        $db = PearDatabase::getInstance();
        $salesorderid = $params['salesorderid'];
        $salesorderworkflowstagesid=$params['salesorderworkflowstagesid'];
        $currentStageSql="SELECT u.email1,s.salesorderworkflowstagesid,s.productid,s.higherid,s.ishigher,s.salesorderid,w.isrole,s.workflowstagesname,s.salesorder_nono,s.accountname,s.modulename,actiontime,(SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS label FROM vtiger_users WHERE id=s.smcreatorid limit 1 ) as smcreator FROM  vtiger_salesorderworkflowstages as s LEFT JOIN vtiger_workflowstages as w ON s.workflowstagesid = w.workflowstagesid  LEFT JOIN vtiger_users as u ON u.id=s.smcreatorid  WHERE  1=1    AND s.salesorderid=?  AND  s.salesorderworkflowstagesid = ? LIMIT 1  ";
        $result = $db->pquery($currentStageSql, array($salesorderid,$salesorderworkflowstagesid));
        if($db->num_rows($result)){
            $userEmailStr = '';
            $email = $db->query_result($result,0,'email1');
            $actiontime = $db->query_result($result,0,'actiontime');
            $module = $db->query_result($result,0,'modulename');
            $modulename = vtranslate($module,'Vtiger','zh_cn');
            $smcreator  = $db->query_result($result,0,'smcreator');
            $salesorder_nono = $db->query_result($result,0,'salesorder_nono');
            $accountname = $db->query_result($result,0,'accountname');
            $workflowstagesname = $db->query_result($result,0,'workflowstagesname');
            $db->pquery(" INSERT INTO vtiger_emailsends (email,senttype,description) values(?,?,?) ",array($email,6,'有邮箱'));
            //邮箱验证
            if($this->checkEmail(trim($email))){
                $userEmailStr = trim($email);
                $title="您的申请被打回";
                // 公共审核消息发送模板
                $db->pquery(" INSERT INTO vtiger_emailsends (email,senttype,description) values(?,?,?) ",array($userEmailStr,5,'打回通知发起人审核进度'));
                $this->sendMsgToUser($title,$userEmailStr,$actiontime,$modulename,$smcreator,$salesorder_nono,$accountname,$workflowstagesname,$module,$salesorderid,'',1);
            }
        }
    }

    /**
     * 公共模板消息发送
     */
    public function sendMsgToUser($title,$userEmailStr,$actiontime,$modulename,$smcreator,$salesorder_nono,$accountname,$workflowstagesname,$module,$salesorderid,$nextworkflowstagesname='',$isCheked=0){
        global  $m_crm_url;
        $db = PearDatabase::getInstance();
        switch ($module){
            case 'VisitingOrder':
                $dataurl=$m_crm_url.'/index.php?module='.$module.'&action=detail&record='.$salesorderid."&issendmsg=1";
                break;
            default :
                $dataurl=$m_crm_url.'/index.php?module='.$module.'&action=one&id='.$salesorderid."&issendmsg=1";
                break;
        }
        if($module=='SeparateInto'){
            $workflowstagesname= str_replace("&lt;","<",$workflowstagesname);
            $workflowstagesname= str_replace("&gt;",">",$workflowstagesname);
        }
        //提示审核人待审核
        if($isCheked==2){
            if($module=='ClosingDate'){
                $content=$actiontime.'<br>模块:'.$modulename.'<br>申请人:'.$smcreator.'<br>待审流程节点:'.$workflowstagesname;
            }else{
                $content=$actiontime.'<br>模块:'.$modulename.'<br>申请人:'.$smcreator.'<br>编号:'.$salesorder_nono.'<br>客户/供应商:'.$accountname.'<br>待审流程节点:'.$workflowstagesname;
            }
        //提示打回
        }elseif($isCheked==1){
            if($module=='ClosingDate'){
                $content=$actiontime.'<br>模块:'.$modulename.'<br>申请人:'.$smcreator.'<br>打回流程节点:'.$workflowstagesname;
            }else{
                $content=$actiontime.'<br>模块:'.$modulename.'<br>申请人:'.$smcreator.'<br>编号:'.$salesorder_nono.'<br>客户/供应商:'.$accountname.'<br>打回流程节点:'.$workflowstagesname;
            }
            //有下一节点 提示（已审核+待审核）
        }elseif(!empty($nextworkflowstagesname)){
            if($module=='ClosingDate'){
                $content=$actiontime.'<br>模块:'.$modulename.'<br>申请人:'.$smcreator.'<br>已审流程节点:'.$workflowstagesname.'<br>待审流程节点:'.$nextworkflowstagesname;
            }else{
                $content=$actiontime.'<br>模块:'.$modulename.'<br>申请人:'.$smcreator.'<br>编号:'.$salesorder_nono.'<br>客户/供应商:'.$accountname.'<br>已审流程节点:'.$workflowstagesname.'<br>待审流程节点:'.$nextworkflowstagesname;
            }
        //如果下一节点没传则且是已审审核节点的（审核）
        }else{
            if($module=='ClosingDate'){
                $content=$actiontime.'<br>模块:'.$modulename.'<br>申请人:'.$smcreator.'<br>已审流程节点:'.$workflowstagesname;
            }else{
                $content=$actiontime.'<br>模块:'.$modulename.'<br>申请人:'.$smcreator.'<br>编号:'.$salesorder_nono.'<br>客户/供应商:'.$accountname.'<br>已审流程节点:'.$workflowstagesname;
            }
        }
        $db = PearDatabase::getInstance();
        $db->pquery(" INSERT INTO vtiger_emailsends (email,senttype,description) values(?,?,?) ",array('',7,'发送地址'.$dataurl));
        /*echo ;*/
        //$userEmailStr="xiaohuai.cui@71360.com";
        $this->setweixincontracts(array('email' => trim($userEmailStr), 'description' => $content, 'dataurl' => $dataurl, 'title' => $title, 'flag' => 7));
    }
    /**
     * 申请审核消息通过提醒 提单人 和 下一节点审核人审核
     * @param $request
     */
    function sendWxRemind($request){
        $this->_logs(array('sendWxRemind',$request));
        global $current_user;
        $db = PearDatabase::getInstance();
        $salesorderid = $request['salesorderid'];
        $salesorderworkflowstagesid = $request['salesorderworkflowstagesid'];
        //和当前同级的审核是否还存在
        $currentHas=false;
        /**
         * 当前审核节点审核后 通知发起人审核进度
         */
        if($salesorderworkflowstagesid){
            $currentStageSql="SELECT u.email1,s.workflowsid,s.sequence,s.salesorderworkflowstagesid,s.productid,s.higherid,s.ishigher,s.salesorderid,w.isrole,s.workflowstagesname,s.salesorder_nono,s.accountname,s.modulename,actiontime,(SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS label FROM vtiger_users WHERE id=s.smcreatorid limit 1 ) as smcreator FROM  vtiger_salesorderworkflowstages as s LEFT JOIN vtiger_workflowstages as w ON s.workflowstagesid = w.workflowstagesid  LEFT JOIN vtiger_users as u ON u.id=s.smcreatorid  WHERE  1=1   AND s.isaction=2  AND  s.salesorderworkflowstagesid = ? LIMIT 1  ";
            $result = $db->pquery($currentStageSql, array($salesorderworkflowstagesid));
            $currentworkflowstagesname='';
            $nextworkflowstagesname='';
            if($db->num_rows($result)){
                $workflowsid = $db->query_result($result,0,'workflowsid');
                $sequence = $db->query_result($result,0,'sequence');
                // 查询判断该 squence 下还有没有审核流 ①如果有则不用发送给发起人审核进度② 如果有则下面后面的发送给审核人提醒的就不用发了
                $hasOthers="SELECT u.email1,s.workflowsid,s.sequence,s.salesorderworkflowstagesid,s.productid,s.higherid,s.ishigher,s.salesorderid,w.isrole,s.workflowstagesname,s.salesorder_nono,s.accountname,s.modulename,actiontime,(SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS label FROM vtiger_users WHERE id=s.smcreatorid limit 1 ) as smcreator FROM  vtiger_salesorderworkflowstages as s LEFT JOIN vtiger_workflowstages as w ON s.workflowstagesid = w.workflowstagesid  LEFT JOIN vtiger_users as u ON u.id=s.smcreatorid  WHERE  1=1   AND s.isaction=1  AND  s.sequence = ?  AND  s.workflowsid = ? AND  s.salesorderid=?  ";
                $hasOthers=$db->pquery($hasOthers, array($sequence,$workflowsid,$salesorderid));
                if($db->num_rows($hasOthers)){
                    $currentHas=true;
                    //如果不存在 该squence 未审的 则 发送消息提醒
                }else{
                    $allHasCheck="SELECT u.email1,s.workflowsid,s.sequence,s.salesorderworkflowstagesid,s.productid,s.higherid,s.ishigher,s.salesorderid,w.isrole,s.workflowstagesname,s.salesorder_nono,s.accountname,s.modulename,actiontime,(SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS label FROM vtiger_users WHERE id=s.smcreatorid limit 1 ) as smcreator FROM  vtiger_salesorderworkflowstages as s LEFT JOIN vtiger_workflowstages as w ON s.workflowstagesid = w.workflowstagesid  LEFT JOIN vtiger_users as u ON u.id=s.smcreatorid  WHERE  1=1   AND s.isaction=2  AND  s.sequence = ?  AND  s.workflowsid = ? AND  s.salesorderid=?  ";
                    $allHasCheck = $db->pquery($allHasCheck, array($sequence,$workflowsid,$salesorderid));
                    while ($hasOthersRow = $db->fetch_array($allHasCheck)){
                        $currentworkflowstagesname .=",".$hasOthersRow['workflowstagesname'];
                    }
                    $currentworkflowstagesname = trim($currentworkflowstagesname,',');
                    $userEmailStr = '';
                    $email = $db->query_result($result,0,'email1');
                    $actiontime = $db->query_result($result,0,'actiontime');
                    $module = $db->query_result($result,0,'modulename');
                    $modulename = vtranslate($module,'Vtiger','zh_cn');
                    $smcreator  = $db->query_result($result,0,'smcreator');
                    $salesorder_nono = $db->query_result($result,0,'salesorder_nono');
                    $accountname = $db->query_result($result,0,'accountname');
                    $workflowstagesname = $db->query_result($result,0,'workflowstagesname');
                    $db->pquery(" INSERT INTO vtiger_emailsends (email,senttype,description) values(?,?,?) ",array($email,6,'有邮箱'));
                    //下一节点待审核
                    $nextHasCheck="SELECT u.email1,s.workflowsid,s.sequence,s.salesorderworkflowstagesid,s.productid,s.higherid,s.ishigher,s.salesorderid,w.isrole,s.workflowstagesname,s.salesorder_nono,s.accountname,s.modulename,actiontime,(SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS label FROM vtiger_users WHERE id=s.smcreatorid limit 1 ) as smcreator FROM  vtiger_salesorderworkflowstages as s LEFT JOIN vtiger_workflowstages as w ON s.workflowstagesid = w.workflowstagesid  LEFT JOIN vtiger_users as u ON u.id=s.smcreatorid  WHERE  1=1   AND s.isaction=1   AND  s.workflowsid = ? AND  s.salesorderid=?  ";
                    $nextHasCheck = $db->pquery($nextHasCheck, array($workflowsid,$salesorderid));
                    while ($nextHasOthersRow = $db->fetch_array($nextHasCheck)){
                        $nextworkflowstagesname .=",".$nextHasOthersRow['workflowstagesname'];
                    }
                    $nextworkflowstagesname =trim($nextworkflowstagesname,',');
                    //邮箱验证
                    if($this->checkEmail(trim($email))){
                        $userEmailStr = trim($email);
                        $title="您的申请审核更新啦";
                        // 公共审核消息发送模板
                        $db->pquery(" INSERT INTO vtiger_emailsends (email,senttype,description) values(?,?,?) ",array($userEmailStr,1,'通知发起人审核进度'));
                        $this->sendMsgToUser($title,$userEmailStr,$actiontime,$modulename,$smcreator,$salesorder_nono,$accountname,$currentworkflowstagesname,$module,$salesorderid,$nextworkflowstagesname);
                    }
                }


            }
        }
        /**
         * 下面是 即将审核人提醒
         */
        // 如果不存在 当前节点同级其他没有审核的 则 提醒下一节点审核人审核。
        if(!$currentHas){
            $this->_logs(array('sendWxRemindcurrentHas',$currentHas));
            //获取该审核流的那个节点数据
            $stageSql = " SELECT s.salesorderworkflowstagesid,s.productid,s.higherid,s.ishigher,s.salesorderid,w.isrole,s.workflowstagesname,s.salesorder_nono,s.accountname,s.modulename,actiontime,(SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS label FROM vtiger_users WHERE id=s.smcreatorid limit 1 ) as smcreator  FROM  vtiger_salesorderworkflowstages as s LEFT JOIN vtiger_workflowstages as w ON s.workflowstagesid = w.workflowstagesid  WHERE  1=1  AND s.isvalidity=0 AND s.isaction=1  AND  s.salesorderid = ? ";
            /*$stageResult = $db->pquery($stageSql, array());*/
            $stageResult = $db->pquery($stageSql, array($salesorderid));
            $this->_logs(array('stageResultNum',$db->num_rows($stageResult)));
            $title="您有待审核的信息";
            while ($row = $db->fetch_array($stageResult)){
                $workflowstagesname = $row['workflowstagesname'];
                $salesorder_nono =$row['salesorder_nono'];
                $accountname = $row['accountname'];
                $module=$row['modulename'];
                $modulename = vtranslate($row['modulename'],'Vtiger','zh_cn');
                $actiontime = $row['actiontime'];
                $smcreator = $row['smcreator'];
                $ishigher = $row['ishigher'];
                $higherid = $row['higherid'];
                $productid = $row['productid'];
                $roleStr = $row['isrole'];
                $salesorderworkflowstagesid = $row['salesorderworkflowstagesid'];
                /*if($module=='RefillApplication'){
                   echo "<pre>";
                   var_dump($row);
                   echo $row['higherid'];
                }*/
                //判断是否有指定审核人
                $this->_logs(array('sendWxRemindisHigher',$ishigher));
                if($ishigher == 1){
                    /*if($module=='RefillApplication'){
                        echo "<pre>";
                        var_dump($row);
                        echo $row['higherid'];
                    }*/
                    // 获取 指定审核人信息
                    $query="SELECT vtiger_users.email1,vtiger_users.last_name  FROM vtiger_users LEFT JOIN vtiger_user2role ON vtiger_users.id=vtiger_user2role.userid WHERE vtiger_users.`status`='Active' AND vtiger_users.isdimission=0 AND vtiger_users.id=?";
                    $userresult=$db->pquery($query,array($higherid));
                    $usernum=$db->num_rows($userresult);
                    /*$user_row=$db->fetch_row($userresult);*/
                    // 如果指定审核人存在发送消息提醒
                    $this->_logs(array('第一个审核人',$userresult));
                    if($usernum){
                        $email=$db->query_result($userresult,0,'email1');
                        $this->_logs(array('第一个审核人的邮箱',$email));
                        //邮箱验证
                        $db->pquery(" INSERT INTO vtiger_emailsends (email,senttype,description) values(?,?,?) ",array($email,6,'有邮箱'));

                        if($this->checkEmail(trim($email))){
                            $userEmailStr = trim($email);
                            $this->_logs(array('第一个审核人的邮箱',$userEmailStr));
                            $db->pquery(" INSERT INTO vtiger_emailsends (email,senttype,description) values(?,?,?) ",array($userEmailStr,2,'发给指定审核人审核'));
                            // 公共审核消息发送模板
                            $this->sendMsgToUser($title,$userEmailStr,$actiontime,$modulename,$smcreator,$salesorder_nono,$accountname,$workflowstagesname,$module,$salesorderid,'',2);
                        }
                    }
                    //产品id是否存在存在发送消息提醒
                } elseif ($productid){
                    $productSql = " select REPLACE(productman,' |##| ',',') as productman from vtiger_products	where  vtiger_products.productid= ? LIMIT 1 ";
                    $productResult = $db->pquery($productSql, array($productid));
                    $productman = $db->query_result($productResult, 0, 'productman');
                    //获取产品负责人列表信息
                    $emailQuery="SELECT vtiger_users.email1,vtiger_users.last_name  FROM vtiger_users LEFT JOIN vtiger_user2role ON vtiger_users.id=vtiger_user2role.userid WHERE vtiger_users.`status`='Active' AND vtiger_users.isdimission=0 AND vtiger_users.id  IN(".$productman.")";
                    $emailResult = $db->pquery($emailQuery, array());
                    $usernum=$db->num_rows($emailResult);
                    // 产品负责人存在
                    if($usernum){
                        $userEmailStr='';
                        while ($rows = $db->fetch_array($emailResult)) {
                            $db->pquery(" INSERT INTO vtiger_emailsends (email,senttype,description) values(?,?,?) ",array($rows['email1'],6,'有邮箱'));
                            if($this->checkEmail(trim($rows['email1']))){
                                $userEmailStr.=trim($rows['email1'])."|";
                            }
                        }
                        $userEmailStr=rtrim($userEmailStr,'|');
                        if($userEmailStr){
                            // 公共审核消息发送模板
                            $db->pquery(" INSERT INTO vtiger_emailsends (email,senttype,description) values(?,?,?) ",array($userEmailStr,3,'通知产品负责人审核进度'));
                            $this->sendMsgToUser($title,$userEmailStr,$actiontime,$modulename,$smcreator,$salesorder_nono,$accountname,$workflowstagesname,$module,$salesorderid,'',2);
                        }
                    }
                    //如果前面都没有那么寻找角色能审核的
                }else{
                    //获取角色的查询条件
                    $roleStr = explode(' |##| ', $roleStr);
                    $str = '';
                    $data=array();
                    foreach ($roleStr as $key=>$value){
                        if(!empty($str)){
                            $str.=",?";
                        }else{
                            $str = "?";
                        }
                        $data[]=trim($value);
                    }
                    //角色存在
                    if($data){
                        //获取这些角色下的所有用户
                        $selectSql="SELECT vtiger_users.email1,id FROM vtiger_users LEFT JOIN vtiger_user2role ON vtiger_users.id=vtiger_user2role.userid WHERE vtiger_users.`status`='Active' AND vtiger_users.isdimission=0 AND vtiger_user2role.roleid in(".$str.")";
//                      $selectSql = " SELECT ur.userid FROM  vtiger_user2role as ur INNER JOIN  vtiger_users as u ON u.id =ur.userid WHERE  u.status='Active'  AND ur.roleid IN (".$str.")";
                        $selectResult = $db->pquery($selectSql, array($data));
                        $current_user_id = $current_user->id;
                        $userEmailStr='';
                        try{
                            $workObj=new WorkFlowCheck_ListView_Model();
                            while ($rows = $db->fetch_array($selectResult)) {
                                //更换当前用户查看是否有权限显示数据
                                $current_user->id = $rows['id'];
                                $user = new Users();
                                $current_user = $user->retrieveCurrentUserInfoFromFile($rows['id']);
                                //管理员或有下级审核权限的显示审核
                                $allStagers = $workObj->getGeneralAudit($module,$salesorderid);
                                if(isset($allStagers[$salesorderworkflowstagesid]) && $this->checkEmail(trim($rows['email1']))){
                                    $userEmailStr.=trim($rows['email1'])."|";
                                }
                                $has = '有邮箱:'.$rows['email1']."allStagers:".json_encode($allStagers)."salesorderworkflowstagesid:".$salesorderworkflowstagesid;
                                $db->pquery(" INSERT INTO vtiger_emailsends (email,senttype,description) values(?,?,?) ",array($rows['email1'],6,$has));
                            }
                        }catch(Exception $e){

                        }
                        //把当前用户重置回来
                        $user = new Users();
                        $current_user = $user->retrieveCurrentUserInfoFromFile($current_user_id);
                        $userEmailStr=rtrim($userEmailStr,'|');
                        if($userEmailStr){
                            $db->pquery(" INSERT INTO vtiger_emailsends (email,senttype,description) values(?,?,?) ",array($userEmailStr,4,'通知角色审核'));
                            // 公共审核消息发送模板
                            $this->sendMsgToUser($title,$userEmailStr,$actiontime,$modulename,$smcreator,$salesorder_nono,$accountname,$workflowstagesname,$module,$salesorderid,'',2);
                        }
                    }
                }
            }
        }

    }
    /**
     * 
     * young.yang 2014年12月22日11:41:00
     * 	2014-12-26 young.yang 增加关联模块
     * @param Vtiger_Request $request
     */
    public function backSalesOrderWorkflowsstages(Vtiger_Request $request) {
        $record = $request->get('record');            //工单id
        $reject = $request->get('reject');           //打回原因
        $rejectname = $request->get('rejectname');     //打回节点名称
        $stagerecordid = $request->get('stagerecordid'); //打回节点id
        $isbackname = $request->get('isbackname'); //正在被打回的节点名称
        $isbackid = $request->get('isrejectid');     //正在被打回的节点id
        $modulename = $request->get('src_module');     //打回模块名称
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $userid = $currentUserModel->getId();
        $db->startTransaction();

// 		向表中工单历史中插入打回原因
		$time=getDateFormat();
		$db=PearDatabase::getInstance();

		$db->startTransaction();
		$salesorderhistory = $db->pquery('insert into vtiger_salesorderhistory (`reject`,`salesorderid`,`rejecttime`,`rejectid`,`rejectname`,`workflowerstagesid`,`modulename`,`rejectnameto`) values(?,?,?,?,?,?,?,?)', array($reject,$record,$time,$userid,$rejectname,$stagerecordid,$modulename,$isbackname));
// 		工作节点打回到1并修改打回状态

		$sql7 = "SELECT sequence FROM vtiger_salesorderworkflowstages WHERE salesorderworkflowstagesid = ?";
		$sequence1 = $db->pquery($sql7,array($isbackid));
		$sequence2 = $db->pquery($sql7,array($stagerecordid));
		$sequ1 = $sequence1->fields['0'];  //大
		$sequ2 = $sequence2->fields['0'];  //小
		
		$sql6 = "UPDATE vtiger_salesorderworkflowstages SET isaction = 0 WHERE salesorderid = ? AND modulename = 'SalesOrder' AND sequence <= ? AND sequence > ?";
        $result1 = $db->pquery("$sql6",array($record,$sequ1,$sequ2));
        $result3 = $db->pquery('UPDATE vtiger_salesorderworkflowstages SET isaction = 1 WHERE salesorderworkflowstagesid=?',array($stagerecordid));
		
		//重新修改打回状态
		//改变状态
		$sql5=$this->getSql($modulename);
		$db->pquery($sql5,array('a_exception',$record));
		$db->pquery('DELETE FROM vtiger_workflowstagesuserid WHERE salesorderid=? AND modulename=?',array($record,$modulename));
		
		//@TODO如何获取当前信息的创建人的信息?
		//young.yang 2015-1-3 加入打回提醒
		//$db->pquery("insert into vtiger_jobalerts(subject,alerttime,modulename,moduleid,alertcontent,alertid) values(?,?,?,?,?,?)",array('打回信息提醒',getDateFormat(),$modulename,$record,$reject,0));
		//$arr=getEntityFieldNames($modulename);
		
		//$result=$db->pquery("select ")
		
		//$arr_reminder=array('subject'=>'你的工单',alerttime,modulename,moduleid,alertcontent,alertid);
		//JobAlerts_Record_Model::createReminder($arr_reminder);
		
		//end
		$db->completeTransaction();
// 		返回ajax		
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($result);
        $response->emit();
    }

    /**
     * 邮件格式验证
     * @param $str
     * @return bool
     */
    public function checkEmail($str){
        $str=trim($str);
        $regex = '/^[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@(?:[-_a-z0-9][-_a-z0-9]*\.)*(?:[a-z0-9][-a-z0-9]{0,62})\.(?:(?:[a-z]{2}\.)?[a-z]{2,})$/i';
        if (preg_match($regex, $str)) {
            return true;
        }
        return false;
    }

    /**
     * 微信企业号信息
     * @param Vtiger_Request $request
     */
    private function setweixincontracts($data){
        $recordModel=new Vtiger_Record_Model();
        $recordModel->sendWechatMessage($data);
        return;
        $this->_logs(array('setweixincontracts',$data));
        $userkey='c0b3Ke0Q4c%2BmGXycVaQ%2BUEcbU0ldxTBeeMAgUILM0PK5Q59cEp%2B40n6qUSJiPQ';
        $url = "http://m.crm.71360.com/api.php";
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
    }

    public function process(Vtiger_Request $request) {
        //+ 权限判断--解决
        $_REQUEST['realoperate'] = $this->realoperate;
        $mode = $request->getMode();
        if (!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
            return;
        }
    }

    /**
     * 更新进度
     * @param Vtiger_Request $request
     */
    public function updateWorkflowSchedule(Vtiger_Request $request) {
        $db = PearDatabase::getInstance();

        $currentUserModel = Users_Record_Model::getCurrentUserModel();

        $stagerecordid = $request->get('stagerecordid');
        $schedule = $request->get('schedule');

        $db->pquery('update vtiger_salesorderworkflowstages set schedule=? where salesorderworkflowstagesid=?', array($schedule, $stagerecordid));

        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($result);
        $response->emit();
    }

    /**
     * 2014-12-27 young.yang 审核
     * @history
     * 	2014-12-28 节点审核当前判断判断是否有下个节点，并且根据条件触发
     * @param Vtiger_Request $request
     * @throws AppException
     */
    function updateSalseorderWorkflowStages(Vtiger_Request $request) {
        // $isMobileCheck  是否是移动端做的请求=1 是
        global $current_user;
        $isMobileCheck = $_REQUEST['isMobileCheck'];
        $request->set('isMobileCheck',$isMobileCheck);
	    $db = PearDatabase::getInstance();
		$record = $request->get('record');
		$customer=$request->get('customer');
        $assigncustomer=$request->get('assigncustomer');
		$moduleName = $request->getModule();
		$srcmoduleName = $request->get('src_module');
		$stagerecord = $request->get('stagerecordid');
        $customername=$request->get('customername');
        $modulestatus = 'b_check';
        $dataNum=$db->pquery('SELECT vtiger_salesorderworkflowstages.salesorderworkflowstagesid,vtiger_salesorderworkflowstages.sequence,vtiger_salesorderworkflowstages.ishigher,vtiger_salesorderworkflowstages.higherid FROM vtiger_salesorderworkflowstages  WHERE salesorderworkflowstagesid =? AND isaction=1 LIMIT 1',array($stagerecord));
        if($db->num_rows($dataNum)==0){//已经审核过的节点不允许多次审核
            $resultaa['success'] = 'false';
            $resultaa['error']['message'] = ":节点已审核请刷新后再试!";
            if($isMobileCheck==1){
                 return $resultaa;
            }else{
                echo json_encode($resultaa);
                exit;
            }
        }
        $sequence=$dataNum->fields['sequence'];
        $currentNodeReviewer=($dataNum->fields['ishigher']==1 && $dataNum->fields['higherid']>0)?$dataNum->fields['higherid']:0;//当前节点的审核
        $dataNum=$db->pquery('SELECT 1 FROM vtiger_salesorderworkflowstages  WHERE salesorderid=? AND sequence<? AND isaction in(0,1) LIMIT 1',array($record,$sequence));
        if($db->num_rows($dataNum)){//防止跳节点审核
            $resultaa['success'] = 'false';
            $resultaa['error']['message'] = ":有未审核的节点请刷新后再试!";
            if($isMobileCheck==1){
                return $resultaa;
            }else{
                echo json_encode($resultaa);
                exit;
            }
        }
        $dataResult=$db->pquery('SELECT higherid,smcreatorid,handleaction FROM vtiger_salesorderworkflowstages  WHERE salesorderid=? AND salesorderworkflowstagesid =? LIMIT 1',array($record,$stagerecord));
        if($db->num_rows($dataResult)){//禁止审核自提的工作流
            $resultaa['success'] = 'false';
            $resultaa['error']['message'] = ":当前节点审核人为工作流创建人，禁止审批，请打回后更换创建人申请或由其他审核人审核。";
            if($dataResult->fields['smcreatorid']==$current_user->id && !in_array($dataResult->fields['handleaction'],array('MyCheck','ProductCheck','REVIEWER'))){
                if($isMobileCheck==1){
                    return $resultaa;
                }else{
                    if($_COOKIE[$stagerecord]){
                        $sql="select reports_to_id from vtiger_users where id=?";
                        $reportsResult=$db->pquery($sql,array($current_user->id));
                        $reports_to_id=$current_user->id==1?1:$db->query_result($reportsResult,0,'reports_to_id');
                        $resultaa['error']['message'] = ":当前节点审核人为工作流创建人，禁止审批，已自动转为上级审批";
                        $sql="update vtiger_salesorderworkflowstages set ishigher=1,higherid=? where salesorderworkflowstagesid=?";
                        $db->pquery($sql,array($reports_to_id,$stagerecord));
                        unset($_COOKIE[$stagerecord]);
                        setcookie ($stagerecord, "", time() - 3600);
                    }else{
                        setcookie($stagerecord,true);
                    }
                    echo json_encode($resultaa);
                    exit;
                }
            }
        }
		global $root_directory,$current_user;
        //steel加入审核之前要走的自定义方法
        
        
          //发送微信企业号信息
        $focus = CRMEntity::getInstance($srcmoduleName);
        if(method_exists($focus,'workflowcheckbefore')){
            $error = $focus->workflowcheckbefore($request);
            if($isMobileCheck==1 && $error['success'] == 'false' && $request->get('isnextchecked')==1){
                echo json_encode(array('success'=>true,'_recordId'=>$record,'_recordLabel'=>"SalesorderWorkflowStages"));
                exit;
            }
            // 如果是移动端 有待验证的信息需要返回给移动端
            if($isMobileCheck==1 && $error['success'] == 'false'){
                return $error;
            }
        }
        //是否是发票模块
        /* if($srcmoduleName=='Invoice'){
            $this->invoiceReceive($stagerecord,$record);
        } */
        //防止前台直接修改节点的属性，从这里进行一次验证
        $sql = "SELECT vtiger_workflowstages.handleaction as handleaction,vtiger_salesorderworkflowstages.workflowsid,vtiger_salesorderworkflowstages.workflowstagesname,vtiger_salesorderworkflowstages.salesorderid,vtiger_salesorderworkflowstages.sequence,vtiger_workflowstages.modulestatus,vtiger_workflowstages.iseditdata,vtiger_workflowstages.isnextnode 	FROM 	vtiger_salesorderworkflowstages left JOIN vtiger_workflowstages ON (vtiger_salesorderworkflowstages.workflowstagesid=vtiger_workflowstages.workflowstagesid) WHERE vtiger_salesorderworkflowstages.salesorderworkflowstagesid =? LIMIT 1";
        $resultdb = $db->pquery($sql, array($stagerecord));
        if ($db->num_rows($resultdb) > 0) {
            $handleaction = $db->query_result($resultdb, 0, 'handleaction');
            $iseditdata = $db->query_result($resultdb, 0, 'iseditdata');
            $isnextnode = $db->query_result($resultdb, 0, 'isnextnode');
            $workflowsid = $db->query_result($resultdb, 0, 'workflowsid');
            // 产品负责人的节点，没有办法跟数据和指定下个节点同时有效，只能增加两个节点来判断是否将他指定
            if($iseditdata){
                $handleaction= 'DataCheck';
            }
            if($isnextnode){
                $handleaction= 'NextCheck';
            }
            $salesorderid =  $db->query_result($resultdb, 0, 'salesorderid');
            $sequence =  $db->query_result($resultdb, 0, 'sequence');
            $modulestatus =  $db->query_result($resultdb, 0, 'modulestatus');
        }else{
            $handleaction = '';
            $salesorderid = 0;
            $sequence = 0;
        }
        //steel加入并行工作流各工作流节点不影响
        $sqlWorkFlows=$workflowsid>0?" AND workflowsid={$workflowsid} ":'';
        // ServiceCheck 分配下属和指定客户 可给客服部
		if($handleaction=='ServiceCheck'){
            //young 2015-05-22根据父级来判断是否已经生成了下个节点
			$sql="select salesorderworkflowstagesid from vtiger_salesorderworkflowstages where parentsalesorderworkflowstagesid=?";
			$resulton=$db->pquery($sql,array($stagerecord));
			$resultone=$db->query_result($resulton,0,'salesorderworkflowstagesid'); //审核节点的id
			//如果已经有了就更新，这里在打回的时候是否可以再次分配还是说直接过掉
            if($resultone>0){
                /* young 2015-05-22 先屏蔽掉更新，如果需要在放开
				$query="UPDATE vtiger_salesorderworkflowstages
							SET workflowstagesname = CONCAT('客服/{$customername}--','审核'),
							 ishigher = 1,
							 higherid = {$customer},
							 smcreatorid={$current_user->id}
							WHERE
								vtiger_salesorderworkflowstages.salesorderworkflowstagesid = ?";
				$db->pquery($query,array($resultone));*/
			}else{
                //直接审核则不用生成下一个节点
                if($customer){
                    // 将此节点之后的节点排序加1
                    $sql='update vtiger_salesorderworkflowstages set sequence=sequence+1 where salesorderid=? and sequence>?'.$sqlWorkFlows;
                    $db->pquery($sql,array($salesorderid,$sequence));
                    // 在当前节点之后插入节点
                    $query="INSERT INTO vtiger_salesorderworkflowstages(workflowstagesname,workflowsid,modulename,productid,salesorderid,createdtime,sequence,smcreatorid,addtime,ishigher,higherid,parentsalesorderworkflowstagesid) SELECT CONCAT( '客服/{$customername}--', '审核' ),workflowsid,modulename,productid,salesorderid,now(),?,{$current_user->id},now(),1,{$customer},{$stagerecord} FROM vtiger_salesorderworkflowstages WHERE salesorderworkflowstagesid= ?";
                    $db->pquery($query,array($sequence+1,$stagerecord));
                }
			}
            //更新客户负责人的
            if($assigncustomer){
                $sql ="select * from vtiger_servicecomments where related_to=(select accountid from vtiger_salesorder where
                        salesorderid = (select salesorderid from vtiger_salesorderworkflowstages where salesorderworkflowstagesid = ? limit 1) limit 1
                      ) and assigntype='accountby'";
                $result = $db->pquery($sql,array($stagerecord));
                //如果已经有了，就更新如果需要，跟上边是同步进行的，
                if($db->num_rows($result)){

                }else{ //如果没有就插入
                      $sql = "INSERT INTO vtiger_servicecomments(assigntype,related_to,addtime,allocatetime,serviceid,assignerid,modifiedtime,nofollowday,salesorderproductsrelid) SELECT 'accountby',accountid,now(),now(),{$customer},{$current_user->id},now(),1,0 FROM (SELECT accountid FROM vtiger_salesorder WHERE salesorderid=(SELECT salesorderid FROM vtiger_salesorderworkflowstages WHERE salesorderworkflowstagesid=? LIMIT 1) LIMIT 1) tt";
                    $db->pquery($sql,array($stagerecord));
                }
            }
		}

        // NextCheck 制定下属和指定上级审核 可给技术部门
        if($handleaction=='NextCheck'&&$customer>0){
            //将后续节点统一加一
            $sql ='update vtiger_salesorderworkflowstages set sequence=sequence+1 where salesorderid=? and sequence>?'.$sqlWorkFlows;
            $db->pquery($sql,array($salesorderid,$sequence));

            // 在当前节点之后插入节点
            $query="INSERT INTO vtiger_salesorderworkflowstages ( workflowstagesname, workflowsid, modulename, productid, salesorderid, createdtime, sequence, smcreatorid, addtime, ishigher, higherid, parentsalesorderworkflowstagesid ) SELECT CONCAT('{$customername}', '审核'), workflowsid, modulename, productid, salesorderid, now(), ?, {$current_user->id}, now(), 1, {$customer}, {$stagerecord} FROM vtiger_salesorderworkflowstages WHERE salesorderworkflowstagesid = ?";
            $db->pquery($query,array($sequence+1,$stagerecord));
        }
        //exit;
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $userid = $currentUserModel->getId();
        $request->set('auditorid', $currentUserModel->getId());
        $request->set('auditortime', date('Y-m-d H:i:s', time()));
        $request->set('schedule', '100');
        $request->set('isaction', '2'); //1活动中2完成0打回
        $request->set('isrejecting', '0'); //wangbin 2014-12-24 新增

        $isstd = false;
        $modulename = $request->get('src_module');
        $checkname = $request->get('checkname');


        $result = $db->pquery("SELECT sequence,workflowstagesid,workflowsid,productid,salesorderid,workflowstagesname,workflowstagesflag,smcreatorid,ishigher FROM `vtiger_salesorderworkflowstages` where salesorderid =? and salesorderworkflowstagesid=? and modulename=? and isaction=1" . $sqlWorkFlows, array($record, $stagerecord, $modulename));
        $sequence = $db->query_result($result, 0, 'sequence');
        //$parentworkflowstagesid = $db->query_result($result, 0, 'parentworkflowstagesid');
        $workflowstagesid = $db->query_result($result, 0, 'workflowstagesid');
        $ishigher = $db->query_result($result, 0, 'ishigher');
        $smcreatorid = $db->query_result($result, 0, 'smcreatorid');
// 		/* if($smcreatorid==$userid&&$ishigher==0){
// 			//商务经理不能审核自己的提交的信息
// 			$reports_to_id=$currentUserModel->get('reports_to_id');
// 			$db->pquery("update vtiger_salesorderworkflowstages set ishigher=1 and higherid=?",array($reports_to_id));
// 			throw new  AppException('不能审核自己创建的信息，自动转移到上级审核');
// 		} */
        $workflowsid = $db->query_result($result, 0, 'workflowsid');
        
        $workflowstagesname= $db->query_result($result, 0, 'workflowstagesname');
         $workflowstagesflag= $db->query_result($result, 0, 'workflowstagesflag');
        
        //更新时间  
   
        if(($workflowstagesname =='财务充值退款审核'|| $workflowstagesname =='财务充值退款审核' || $workflowstagesname =='财务退款')&&$workflowstagesflag=='DO_REFUND'){
            $sql = "update vtiger_salesorderworkflowstages set auditortime=? where salesorderid =? and salesorderworkflowstagesid=? and modulename=?" ;
            $params = array(date('Y-m-d H:i:s', time()), $record, $stagerecord, $modulename);
            $db->pquery($sql, $params);
            
            $sql = "update vtiger_rubricrechargesheet set refundtime=? where refillapplicationid =? and deleted=0  AND isbackwash=1 and refundtime is NULL" ;
            $params = array(date('Y-m-d H:i:s', time()), $record);
            $db->pquery($sql, $params);
        }
        
		//1.是否有记录
		if($sequence){
			//$db->startTransaction();
			
			//2.更新当前记录
			$sql="update vtiger_salesorderworkflowstages set auditorid=?,auditortime=?,schedule=?,isaction=?,isrejecting=? where salesorderid =? and salesorderworkflowstagesid=? and modulename=?".$sqlWorkFlows;
			$params=array($request->get('auditorid'),$request->get('auditortime'),$request->get('schedule'),$request->get('isaction'),$request->get('isrejecting'),$record,$stagerecord,$modulename);//wangbin 2014年12月24日 新增
            //充值申请单平台负责是同一人的情况，同时审核 2017/03/17 gaocl add
            /*$is_admin = $current_user->is_admin == 'on' ? 1 : 0;
            if($modulename == "RefillApplication" && $sequence == 2 && $is_admin == 0){
                $sql="update vtiger_salesorderworkflowstages set auditorid=?,auditortime=?,schedule=?,isaction=?,isrejecting=? where salesorderid =? and sequence=? and modulename=? and FIND_IN_SET(?, REPLACE(vtiger_salesorderworkflowstages.platformids, ' |##| ', ','))";
                $params=array($request->get('auditorid'),$request->get('auditortime'),$request->get('schedule'),$request->get('isaction'),$request->get('isrejecting'),$record,$sequence,$modulename,$request->get('auditorid'));
            }*/
            $db->pquery($sql,$params);
			//@TODO 事件触发,当有action=2时触发当前的阶段的事件,阶段id，模块名，id，以及产品id
			
			
			//查找同级阶段是否都已经审核				
			$sql3="SELECT * FROM vtiger_salesorderworkflowstages WHERE salesorderid = ? AND modulename =? AND sequence =?{$sqlWorkFlows} ORDER BY sequence ASC";
			$result3=$db->pquery($sql3,array($record,$modulename,$sequence));
			if($db->num_rows($result3)){
				$isall=true;
				while($row=$db->fetch_array($result3)){
					if($row['isaction']!=2){
							$isall=false;break;
					}
				}
				//审核下个节点	
				if($isall){
					/*$db->pquery("update vtiger_salesorderworkflowstages set isaction=1,actiontime=NOW() where workflowstagesid = ( select workflowstagesid from(
					SELECT workflowstagesid FROM vtiger_salesorderworkflowstages WHERE salesorderid = ? AND
 					modulename =? AND isaction=0  ORDER BY sequence ASC LIMIT 1 ) as temp ) and salesorderid = ? AND
 					modulename =?",array($record,$modulename,$record,$modulename));*/
                    //将原来的通过原来的流程排序判断修改成按照生成之后的判断
                    $db->pquery("update vtiger_salesorderworkflowstages set isaction=1,actiontime=NOW() where sequence = (select sequence FROM ( select sequence FROM vtiger_salesorderworkflowstages WHERE salesorderid = ? AND
 					modulename =? AND isaction=0  ORDER BY sequence ASC LIMIT 1 ) as  tt)  and salesorderid = ? AND
 					modulename =?{$sqlWorkFlows}",array($record,$modulename,$record,$modulename));
                    //将激活的节点后面置0
                    $db->pquery("update vtiger_salesorderworkflowstages set isaction=0 where sequence>(select sequence FROM ( select sequence FROM vtiger_salesorderworkflowstages WHERE salesorderid = ? AND
 					modulename =? AND isaction=1  ORDER BY sequence ASC LIMIT 1 ) as  tt)  and salesorderid = ? AND
 					modulename =?{$sqlWorkFlows}",array($record,$modulename,$record,$modulename));
					
				}
			}
			//2015-1-22  wangbin 添加审核历史 添加审核提醒
			$time = $time=getDateFormat();
			$salesordercheckhistory = $db->pquery('insert into vtiger_salesordercheckhistory (`salesorderid`,`checktime`,`checkid`,`checkname`,`workflowerstagesid`,modulename) values(?,?,?,?,?,?)', array($record,$time,$userid,$checkname,$stagerecord,$modulename));

			//$alertcontent=$checkname."已通过";
			//$db->pquery("insert into vtiger_jobalerts(subject,alerttime,modulename,moduleid,alertcontent,alertid) values(?,?,?,?,?,?)",array('审核信息提醒',$time,$modulename,$record,$alertcontent,0));
			//end  wangbin 
			
			//young.yang 2015-1-26 是否结束
			$sql4="select GROUP_CONCAT(workflowstagesname) as workflowstagesname from vtiger_salesorderworkflowstages where salesorderid = ? AND
 					modulename =? AND isaction=1 GROUP BY salesorderid";
			$result4=$db->pquery($sql4,array($record,$modulename));
			$sendMail=0;
			if($db->num_rows($result4)==0){
                //if(empty($modulestatus)){
                    $modulestatus = 'c_complete';
                //}
				$sql5=$this->getSql($modulename,'已完成');
				$db->pquery($sql5,array($modulestatus,'已完成',$record));
			}else{
				$workflowstagesname=$db->query_result($result4, 0,'workflowstagesname');
				$sql5=$this->getSql($modulename,$workflowstagesname);
                if(empty($modulestatus)){
                    $modulestatus = 'b_check';
                }
				$db->pquery($sql5,array($modulestatus,$workflowstagesname,$record));
                $sendMail=1;
                //充值申请单待审核人邮件提醒 2017/03/04 gaocl add

			}
		    //$db->completeTransaction(); //事务完成
            if($modulename == "RefillApplication" && $sendMail==1){
                RefillApplication_Module_Model::sendRefillApplicationMail($record);
            }
            //steel加入审核之后走的模块方法
            if(method_exists($focus,'workflowcheckafter')){
                $focus->workflowcheckafter($request);
            }


            
            $_REQUEST['actualrecorid']=$request->get('record');
            //审核通过消息提醒
            $this->sendWxRemind(array('salesorderid'=>$record,'salesorderworkflowstagesid'=>$stagerecord));
            $dataresult=$db->pquery('SELECT 1 FROM `vtiger_workflowstagesuserid` WHERE salesorderid=? AND userid=? AND modulename=?',array($record,$current_user->id,$srcmoduleName));
            if(!$db->num_rows($dataresult)){
                $db->pquery('INSERT INTO `vtiger_workflowstagesuserid` (`salesorderid`, `userid`, `modulename`) VALUES (?, ?, ?)',array($record,$current_user->id,$srcmoduleName));
            }
            $query='SELECT vtiger_salesorderworkflowstages.salesorderworkflowstagesid,vtiger_salesorderworkflowstages.sequence,vtiger_salesorderworkflowstages.ishigher,vtiger_salesorderworkflowstages.higherid FROM vtiger_salesorderworkflowstages WHERE salesorderid=? AND isaction=1 LIMIT 1';
			$dataResult=$db->pquery($query,array($record));
            if($db->num_rows($dataResult)){
                if(1==$dataResult->fields['ishigher'] && $currentNodeReviewer==$dataResult->fields['higherid']){
                    $request->set('stagerecordid',$dataResult->fields['salesorderworkflowstagesid']);
                    $request->set('isnextchecked',1);//标记为多次审核，下次审核，节点有错误也返回正确，前端页面能刷新
                    $_REQUEST['isMobileCheck']=1;//将来源改为移动端,代码不die
                    $flag=false;
                    $this->updateSalseorderWorkflowStages($request);

                }else{
                    $flag=true;
                }
            }else{
                $flag=true;
            }
		}else{
            $flag=true;
        }
        if($flag){
            $resultaa['_recordLabel'] = $moduleName;
            $resultaa['_recordId'] = $record;
            $response = new Vtiger_Response();
            $response->setEmitType(Vtiger_Response::$EMIT_JSON);
            $response->setResult($resultaa);
            $response->emit();
        }
	}
	/**
	 * 返回sql
	 * @param unknown $modulename
	 * @return string
	 */
	public function getSql($modulename,$workflowstagesname=''){
		$arr=getEntityFieldNames($modulename,$field='');
		if(empty($field)){
			$field='modulestatus=?';
		}
		if(!empty($workflowstagesname)){
			$field.=',workflowsnode=?,workflowstime=\''.getDateFormat().'\'';
		}
		$sql="update ".$arr['tablename']." set ".$field." where ".$arr['entityidfield']."= ?";
		return $sql;
	}
	public function handler($module,$action){
		
	}
	public function handlerVisitingOrder(){
		
	}

    /**
     * 通用作废
     * @param Vtiger_Request $request
     */
    public function canceledworkflow(Vtiger_Request $request){
        $db = PearDatabase::getInstance();
        $record = $request->get('record');
        $modulename=$request->get('src_module');
        $modulestatus = 'c_cancel';
        $stagerecord = $request->get('stagerecordid');
        $salesorderid =0;

        //权限

        //防止前台直接修改节点的属性，从这里进行一次验证
        $sql="SELECT vtiger_workflowstages.handleaction as handleaction,vtiger_salesorderworkflowstages.salesorderid,vtiger_salesorderworkflowstages.sequence,vtiger_workflowstages.modulestatus 	FROM 	vtiger_salesorderworkflowstages left JOIN vtiger_workflowstages ON (vtiger_salesorderworkflowstages.workflowstagesid=vtiger_workflowstages.workflowstagesid) WHERE vtiger_salesorderworkflowstages.salesorderworkflowstagesid =? LIMIT 1";

        $resultdb=$db-> pquery($sql,array($stagerecord));
        if($db->num_rows($resultdb)==0){
            throw new AppException('无效的节点');exit;
        }else{
            $salesorderid =  $db->query_result($resultdb, 0, 'salesorderid');
            if($salesorderid!=$record){  // 防止人工修改数据
                throw new AppException('无效的节点');exit;
            }
        }
        //流程设置作废
        $sql5=$this->getSql($modulename,'作废');
        $db->pquery($sql5,array($modulestatus,'作废',$record));
        //节点设置作废
        $db->pquery('update vtiger_salesordercheckhistory  set isvalidity=1 where salesorderid = ? AND modulename =?', array($record,$modulename));
        //自定义作废,作为一个钩子函数使用
        $entityInstance = CRMEntity::getInstance($modulename);
        if(method_exists($entityInstance,'canceledWorkflow')){
            $entityInstance->canceledWorkflow($record);  //自定义作废方法
        }
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->emit();

    }

    /**
     * steel 2015-05-26
     * 发票领取结点!!!关联发票领取人
     **/
    public function invoiceReceive($stagerecordid,$record){
        /*
        $db=PearDatabase::getInstance();
        $query="SELECT
                    sequence AS currentsequence,
                    (
                        SELECT
                            MAX(sequence)
                        FROM
                            vtiger_workflowstages
                        WHERE
                            vtiger_workflowstages.workflowsid = vtiger_salesorderworkflowstages.workflowsid
                    ) AS maxsequence
                FROM
                    `vtiger_salesorderworkflowstages`
                WHERE
                    vtiger_salesorderworkflowstages.salesorderworkflowstagesid =?
                   AND vtiger_salesorderworkflowstages.modulename = 'Invoice'";
        $result=$db->pquery($query,array($stagerecordid));
        $currentsequence=$db->query_result($result,0,'currentsequence');
        $maxsequence=$db->query_result($result,0,'maxsequence');
        //到发票审核节点先判断是财务部分字段否为空
        if(($currentsequence==1 && $maxsequence==3)||($maxsequence-$currentsequence)==4){
            $query="SELECT invoicecode,invoice_no FROM vtiger_invoice WHERE invoiceid=?";
            $result=$db->pquery($query,array($record));
            $invoicecode=$db->query_result($result,0,'invoicecode');
            $invoice_no=$db->query_result($result,0,'invoice_no');
            if(empty($invoicecode) || empty($invoice_no)){
                $resultaa['success'] = 'false';
                $resultaa['error']['message'] = ':请先完成财务信息再进行审核!';
                echo json_encode($resultaa);
                exit;

            }
        }
        //发票领取时走的结点
        if((($currentsequence-3)==0 && $maxsequence==3)||($maxsequence-$currentsequence)==2){
            global $current_user;
            $sql="UPDATE vtiger_invoice SET receiveid=?,receivedate=? WHERE invoiceid=?";
            $db->pquery($sql,array($current_user->id,date('Y-m-d H:i:s'),$record));
        }
    */
    }

    /**
     * 写日志，用于测试,可以开启关闭
     * @param data mixed
     */
    public function _logs($data, $file = 'wxlogs_'){
        $year	= date("Y");
        $month	= date("m");
        $dir	= './logs/tyun/' . $year . '/' . $month . '/';
        if(!is_dir($dir)) {
            mkdir($dir,0755,true);
        }
        $file = $dir . $file . date('Y-m-d').'.txt';
        @file_put_contents($file, '----------------' . date('H:i:s') . '--------------------'.PHP_EOL.var_export($data,true).PHP_EOL, FILE_APPEND);
    }
}
