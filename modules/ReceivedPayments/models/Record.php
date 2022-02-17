<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * Inventory Record Model Class
 */
class ReceivedPayments_Record_Model extends Vtiger_Record_Model {

	public static function getAllReceivedPayments($records){
		$db = PearDatabase::getInstance();
		$result = $db->pquery("SELECT vtiger_receivedpayments.*,IFNULL((SELECT sum(vtiger_receivedpayments_extra.extra_price) FROM `vtiger_receivedpayments_extra` WHERE vtiger_receivedpayments_extra.receivementid=vtiger_receivedpayments.receivedpaymentsid),0) AS sumextra_price FROM `vtiger_receivedpayments` where relatetoid in(?)",array($records));
		//var_dump($result);
		$stages=array();
		for($i=0; $i<$db->num_rows($result); $i++) {
			$relmodule = $db->query_result($result, $i, 'relmodule');
			$unit_price =	$db->query_result($result, $i, 'unit_price');
			$createtime = $db->query_result($result, $i, 'createtime');
			$discontinued=$db->query_result($result, $i, 'discontinued');
			$owncompany=$db->query_result($result, $i, 'owncompany');
			$paytitle=$db->query_result($result, $i, 'paytitle');
			$standardmoney=$db->query_result($result, $i, 'standardmoney');
			$exchangerate=$db->query_result($result, $i, 'exchangerate');
			$reality_date=$db->query_result($result, $i, 'reality_date');
			$sumextra_price=$db->query_result($result, $i, 'sumextra_price');

			$stages[]=array('relmodule'=>$relmodule,'unit_price'=>$unit_price,'createtime'=>$createtime,'discontinued'=>$discontinued,'owncompany'=>$owncompany,'paytitle'=>$paytitle,'standardmoney'=>$standardmoney,'exchangerate'=>$exchangerate,'reality_date'=>$reality_date,'sumextra_price'=>$sumextra_price);
		}
		return  $stages;
	}
    public function  getReceivedPaymentsinfo($recordid){
        $db = PearDatabase::getInstance();
        $result = $db->pquery("SELECT * FROM `vtiger_receivedpayments` where receivedpaymentsid = ?;",array($recordid));
        //var_dump($result);
        $nums = $db->num_rows($result);
        if($nums>0){
          $stages = $result->fields['relatetoid'];
        }

        return  $stages;
    }

    public function getlistviewsql(){
        $listQuery = "SELECT
vtiger_achievementallot.achievementallotid,
	vtiger_achievementallot.owncompanys,
             ( SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE vtiger_achievementallot.receivedpaymentownid = vtiger_users.id ) AS receivedpaymentownid,
	vtiger_achievementallot.businessunit,
(
		SELECT
			CONCAT(
				last_name,
				'[',
				IFNULL(
					(
						SELECT
							departmentname
						FROM
							vtiger_departments
						WHERE
							departmentid = (
								SELECT
									departmentid
								FROM
									vtiger_user2department
								WHERE
									userid = vtiger_users.id
								LIMIT 1
							)
					),
					''
				),
				']',
				(

					IF (
						`status` = 'Active',
						'',
						'[离职]'
					)
				)
			) AS last_name
		FROM
			vtiger_users
		WHERE
			vtiger_receivedpayments.createid = vtiger_users.id
	) AS createid,
	vtiger_receivedpayments.reality_date,
	vtiger_receivedpayments.createtime,
	vtiger_receivedpayments.overdue,
	vtiger_receivedpayments.unit_price,
	vtiger_receivedpayments.modifiedtime,
	(
		SELECT
			contract_no
		FROM
			vtiger_servicecontracts
		WHERE
			vtiger_servicecontracts.servicecontractsid = vtiger_receivedpayments.relatetoid
	) AS relatetoid,
	(
		SELECT
			CONCAT(
				last_name,
				'[',
				IFNULL(
					(
						SELECT
							departmentname
						FROM
							vtiger_departments
						WHERE
							departmentid = (
								SELECT
									departmentid
								FROM
									vtiger_user2department
								WHERE
									userid = vtiger_users.id
								LIMIT 1
							)
					),
					''
				),
				']',
				(

					IF (
						`status` = 'Active',
						'',
						'[离职]'
					)
				)
			) AS last_name
		FROM
			vtiger_users
		WHERE
			vtiger_receivedpayments.checkid = vtiger_users.id
	) AS checkid,
	vtiger_receivedpayments.exchangerate,
	vtiger_receivedpayments.accountscompany,
	vtiger_receivedpayments.receivementcurrencytype,
	vtiger_receivedpayments.standardmoney,
	vtiger_receivedpayments.receivedpaymentsid
FROM
	vtiger_receivedpayments
RIGHT  JOIN  vtiger_achievementallot ON vtiger_receivedpayments.receivedpaymentsid = vtiger_achievementallot.receivedpaymentsid
WHERE
	1 = 1";
        return $listQuery;
    }

    public  function match_account($paytitle){
        $db = PearDatabase::getInstance();
        /*$label=preg_replace('/\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;/u','',$paytitle);
        $sql = "SELECT * FROM  vtiger_crmentity  WHERE label = ? AND setype =? AND deleted = ?";
        $result = $db->pquery($sql,array($label,'Accounts','0'));*/
        $label=preg_replace('/\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;|&quot;|　|&apos;|&amp;|&lt;|&gt;|&#039;|&ldquo;|&rdquo;|&lsquo;|&rsquo;|&hellip;|\“|\”|\‘|\〉|\〈|\’|\〖|\〗|\【|\】|\、|\·|\……|\…|\——|\＋|\－|\＝|\,|\<|\.|\>|\/|\?|\;|\:|\\\'|\"|\[|\{|\]|\}|\\|\||\`|\~|\!|\@|\#|\$|\%|\^|\\&|\*|\(|\)|\-|\_|\=|\+|\，|\＜|\．|\＞|\／|\？|\；|\：|\＇|\＂|\［|\{|\］|\}|\＼|\｜|\｀|\￣|\！|\＠|\#|\＄|\％|\＾|\＆|\＊|\（|\）|\－|\＿|\＝|\＋|\，|\《|\．|\》|\、|\？|\；|\：|\’|\”|\【|\｛|\】|\｝|\＼|\｜|\·|\～|\！|\＠|\＃|\￥|\％|\……|\…|\＆|\×|\（|\）|\－|\——|\—|\＝|\＋|\，|\《|\。|\》|\、|\？|\；|\：|\‘|\“|\【|\｛|\】|\｝|\、|\||\·|\~|\！|\@|\#|\￥|\%|\……|\…|\&|\*|\（|\）|\-|\——|\=|\+/u','',$paytitle);
        $label=strtoupper($label);
        $sql = "SELECT vtiger_crmentity.* FROM vtiger_uniqueaccountname LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_uniqueaccountname.accountid WHERE vtiger_uniqueaccountname.accountname=? AND setype =? AND deleted = ? limit 1";
        $result = $db->pquery($sql,array($label,'Accounts','0'));
        $data = array();
        if($db->num_rows($result)>0){
            $data = $db->fetch_array($result);
        }
        return $data;
    }

    public static function save_modules($ReceivedPaymentid,$serviceid,$input){
        $adb = PearDatabase::getInstance();
        //2015年5月26日 星期二 wangbin 判断状态，新增/跟编辑，回款保存后，数据库插入/update产品的回款金额，
        $ifsql = "SELECT receivedpaymentsproductsid,serviceid FROM vtiger_receivementproducts WHERE receivedpaymentid = ? LIMIT 1 ";
        $ifresult = $adb->pquery($ifsql,array($ReceivedPaymentid));
        //判断回款产品表里是否含有此次回款，有跟新，无就新增。
        if(!empty($serviceid)){
            if($adb->num_rows($ifresult)>0){
                if($ifresult->fields['serviceid'] == $serviceid){
                    $updatesql = "UPDATE vtiger_receivementproducts SET alreadyprice = ? WHERE receivedPaymentid = ? AND productsid=?";
                    if(!empty($input)){
                        foreach ($input as $key=>$val){
                            $tempkey = explode(',',$key);
                            $adb->pquery($updatesql,array($val,$ReceivedPaymentid,$tempkey['1']));
                        }
                    }
                }else{
                    $adb->pquery("DELETE FROM `vtiger_receivementproducts` WHERE receivedPaymentid =?",array($ReceivedPaymentid));
                    $insertsql ="INSERT INTO vtiger_receivementproducts (receivedPaymentid,serviceid,alreadyprice,productsid,salesorderproductsid) VALUES (?,?,?,?,?)";
                    if(!empty($input)){
                        foreach ($input as $key=>$val){
                            $tempkey = explode(',',$key);
                            $adb->pquery($insertsql,array($ReceivedPaymentid,$serviceid,$val,$tempkey['1'],$tempkey['0']));
                        }
                    }
                }
            }else{
                $insertsql ="INSERT INTO vtiger_receivementproducts (receivedPaymentid,serviceid,alreadyprice,productsid,salesorderproductsid) VALUES (?,?,?,?,?)";
                if(!empty($input)){
                    foreach ($input as $key=>$val){
                        $tempkey = explode(',',$key);
                        $adb->pquery($insertsql,array($ReceivedPaymentid,$serviceid,$val,$tempkey['1'],$tempkey['0']));
                    }
                }
            }
        }
        //end
        //2015年6月5日 星期五 wangbin 在工单产品表里增加产品的回款总金额，便于回款列表的计算;冗余字段添加
        if(!empty($serviceid)){
            ReceivedPayments_Record_Model::repeatReceive($ReceivedPaymentid,$serviceid);
            $recordModel=Vtiger_Record_Model::getInstanceById($serviceid,"ServiceContracts");
            $recordModel->accountUpgrade();
            //2015年8月21日20:06:39 wangbin 回款保存后,跟新合同剩余字段;
            $updatremaingsql = "UPDATE vtiger_receivedpayments SET remaing = ( SELECT bb.total - aa.receive_total FROM ( SELECT SUM(unit_price) AS receive_total FROM vtiger_receivedpayments WHERE relatetoid = ? ) aa, ( SELECT total FROM vtiger_servicecontracts WHERE servicecontractsid =? ) bb ) WHERE relatetoid = ?";
            $adb->pquery($updatremaingsql,array($serviceid,$serviceid,$serviceid));

            $selectrecepro = "SELECT vtiger_salesorderproductsrel.salesorderproductsrelid, IFNULL(( SELECT SUM(alreadyprice) FROM vtiger_receivementproducts AS aa WHERE aa.serviceid = vtiger_salesorderproductsrel.servicecontractsid AND aa.productsid = vtiger_salesorderproductsrel.productid ), 0 ) AS already FROM vtiger_salesorderproductsrel LEFT JOIN vtiger_products ON vtiger_salesorderproductsrel.productid = vtiger_products.productid LEFT JOIN vtiger_receivementproducts ON vtiger_salesorderproductsrel.salesorderproductsrelid = vtiger_receivementproducts.salesorderproductsid WHERE vtiger_salesorderproductsrel.servicecontractsid = ? GROUP BY vtiger_salesorderproductsrel.productid";
            $updatesalesprosql = "UPDATE vtiger_salesorderproductsrel SET amountproduct = ? WHERE salesorderproductsrelid = ?";
            $reproresult = $adb->pquery($selectrecepro,array($serviceid));
            if($adb->num_rows($reproresult)>0){
                for ($i=0;$i<$adb->num_rows($reproresult);++$i){
                    $arr = $adb->fetchByAssoc($reproresult);
                    $adb->pquery($updatesalesprosql,array($arr['already'],$arr['salesorderproductsrelid']));
                }
            }
            /////steel加入回款后生成工作
            //看一下有没有工单
            $salesorderid=ServiceContracts_Record_Model::getSalesorderid($serviceid);
            if(!empty($salesorderid)){
                foreach($salesorderid as $value){
                    if($value['salesorderid']>0){
                        //回款总和是否大于回款
                        if(ServiceContracts_Record_Model::receiveDayprice($serviceid,$value['salesorderid']) || ServiceContracts_Record_Model::whetherPayment($serviceid)){
                            //有没有生成工作流没有则生成
                            if(ServiceContracts_Record_Model::getWorkflows($value['salesorderid'])){
                                //工单对应的工作流是否是标准工作流
                                if(ServiceContracts_Record_Model::getSalesorderworkflowsid($value['salesorderid'])==ServiceContracts_Record_Model::selectWorkfows()){
                                    //是否是T-clude套餐
                                    if(ServiceContracts_Record_Model::createIsWorkflows('',$serviceid)){
                                        //生成工作流
                                        ServiceContracts_Record_Model::contractsMakeWorkflows($value['salesorderid'],$serviceid,1);
                                        //删除第1,2节点
                                        ServiceContracts_Record_Model::setWorkflowNode($value['salesorderid']);
                                        // 删除没必要的审核流节点后 发消息
                                        $object = new SalesorderWorkflowStages_SaveAjax_Action();
                                        //file_put_contents('files.txt',$salesorderid);
                                        $object->sendWxRemind(array('salesorderid'=>$value['salesorderid'],'salesorderworkflowstagesid'=>0));
                                    }
                                }
                            }
                        }
						//检查标准工作流,非T云产品是否回款不足,如果是回款不足且回款大于成本则重新激活工作流
						ServiceContracts_Record_Model::noStandardToRestart($value['salesorderid'],$serviceid);
                    }

                }
            }
        }
        //end
    }

    /**根据回款来触发工作流
     * @param $serviceid
     */
    public function setUpdateSalesorder($serviceid){
        $salesorderid=ServiceContracts_Record_Model::getSalesorderid($serviceid);
        if(!empty($salesorderid)){
            foreach($salesorderid as $value){
                if($value['salesorderid']>0){
                    //回款总和是否大于回款
                    if(ServiceContracts_Record_Model::receiveDayprice($serviceid,$value['salesorderid']) || ServiceContracts_Record_Model::whetherPayment($serviceid)){
                        //有没有生成工作流没有则生成
                        if(ServiceContracts_Record_Model::getWorkflows($value['salesorderid'])){
                            //工单对应的工作流是否是标准工作流
                            if(ServiceContracts_Record_Model::getSalesorderworkflowsid($value['salesorderid'])==ServiceContracts_Record_Model::selectWorkfows()){
                                //是否是T-clude套餐
                                if(ServiceContracts_Record_Model::createIsWorkflows('',$serviceid)){
                                    //生成工作流
                                    ServiceContracts_Record_Model::contractsMakeWorkflows($value['salesorderid'],$serviceid,1);
                                    //删除第1,2节点
                                    ServiceContracts_Record_Model::setWorkflowNode($value['salesorderid']);
                                }
                            }
                        }
                    }
                    //检查标准工作流,非T云产品是否回款不足,如果是回款不足且回款大于成本则重新激活工作流
                    ServiceContracts_Record_Model::noStandardToRestart($value['salesorderid'],$serviceid);
                }
            }
        }
    }
    public static function repeatReceive($ReceivedPaymentid,$serviceid){
        global $adb;
        $adb->pquery("UPDATE `vtiger_newinvoicerayment` SET servicecontractsid=?,contract_no=(SELECT contract_no FROM vtiger_servicecontracts WHERE servicecontractsid=? LIMIT 1) WHERE deleted=0 AND receivedpaymentsid=?",array($serviceid,$serviceid,$ReceivedPaymentid));
    }

    /**
     * 导出权限设置
     * @param string $module
     * @return bool|mixed|string
     * @throws Exception
     */
    static public function getImportUserPermissions($module=''){
        global $current_user,$adb;
        if(empty($module)){
            $query="SELECT userid,permissions FROM vtiger_custompermtable WHERE deleted=0 AND module in('ServiceContracts','ReceivedPayments') AND userid=? limit 1";
            $result=$adb->pquery($query,array($current_user->id));
        }else{
            $query="SELECT userid,permissions FROM vtiger_custompermtable WHERE deleted=0 AND module=? AND userid=? limit 1";
            $result=$adb->pquery($query,array($module,$current_user->id));
        }
        $num=$adb->num_rows($result);
        if($num==0){
            return false;
        }
        return $adb->query_result($result,0,'permissions');

    }

    /**
     * 用户
     * @param $str
     * @return array
     */
    public static function getuserinfo($str){
        $db=PearDatabase::getInstance();
        $query="SELECT id,
         (SELECT CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]')))) as last_name
         FROM vtiger_users WHERE 1=1 {$str}";
        return $db->run_query_allrecords($query);
    }

    /**
     * 当前已经配置的权限用户
     * @return array
     */
    public static function getReportPermissions(){
        $db=PearDatabase::getInstance();
        $query="SELECT vtiger_custompermtable.custompermtableid as id,last_name,permissions,if(module='ReceivedPayments','回款','合同') AS module FROM vtiger_custompermtable LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_custompermtable.userid WHERE module in('ReceivedPayments','ServiceContracts') ORDER BY custompermtableid DESC";
        return $db->run_query_allrecords($query);
    }

    /**
     * 收款账号
     * @return array
     */
    public static function getowncompany()
    {
        $db=PearDatabase::getInstance();
        $query="SELECT owncompany FROM vtiger_receivedpayments WHERE owncompany IS NOT NULL AND owncompany!='' GROUP BY owncompany";
        return $db->run_query_allrecords($query);
    }

    /**
     * 获取回款使用明细
     * @param Vtiger_Request $request
     */
    public function getReceivedPaymentsUseDetail(Vtiger_Request $request){
        $recorid = $request->get('record');//回款id
        $db=PearDatabase::getInstance();
        $sql1 = "SELECT 
                '1' AS type,
                vtiger_salesorderrayment.salesorderid AS recordid,
                vtiger_salesorder.salesorder_no AS recordno,
                vtiger_crmentity.createdtime,
                IFNULL(vtiger_users.last_name,'--') AS last_name,
                IFNULL(vtiger_salesorderrayment.modifiedtime,'--') AS matchdate,
                CONCAT('人力成本: ',vtiger_salesorderrayment.laborcost,' | 外采成本: ',vtiger_salesorderrayment.purchasecost) AS detail,
                0 as totalrecharge,
                '工单外采' as rechargesource,
                0 as summoney,
                '' AS productname,
                vtiger_salesorderrayment.remarks
                FROM vtiger_salesorderrayment
                JOIN vtiger_salesorder ON(vtiger_salesorderrayment.salesorderid=vtiger_salesorder.salesorderid)
                LEFT JOIN vtiger_crmentity ON vtiger_salesorder.salesorderid=vtiger_crmentity.crmid
                LEFT JOIN vtiger_users ON(vtiger_salesorderrayment.modifiedby=vtiger_users.id)
                WHERE vtiger_salesorderrayment.receivedpaymentsid=? AND vtiger_salesorderrayment.deleted=0
                UNION ALL
                SELECT 
                '2' AS type,
                vtiger_refillapprayment.refillapplicationid AS recordid,
                vtiger_refillapplication.refillapplicationno AS recordno,
                vtiger_crmentity.createdtime,
                IFNULL(vtiger_users.last_name,'--') AS last_name,
                IFNULL(vtiger_refillapprayment.modifiedtime,'--') AS matchdate,
                CONCAT('充值金额: ',vtiger_refillapprayment.refillapptotal) AS detail,
                vtiger_refillapplication.totalrecharge,
                vtiger_refillapplication.rechargesource,
                (select SUM(vtiger_rechargesheet.prestoreadrate)  from vtiger_rechargesheet  where vtiger_rechargesheet.refillapplicationid=vtiger_refillapprayment.refillapplicationid and vtiger_rechargesheet.deleted=0) as summoney,
                (SELECT GROUP_CONCAT(vtiger_products.productname) FROM `vtiger_products`,(SELECT vtiger_rechargesheet.productid,vtiger_rechargesheet.refillapplicationid	FROM	vtiger_rechargesheet WHERE	vtiger_rechargesheet.deleted = 0 GROUP BY	vtiger_rechargesheet.refillapplicationid,vtiger_rechargesheet.productid) AS rechrename	WHERE (rechrename.productid = vtiger_products.productid AND rechrename.refillapplicationid = vtiger_refillapprayment.refillapplicationid)) AS productname,
                vtiger_refillapprayment.remarks
                FROM vtiger_refillapprayment 
                JOIN vtiger_refillapplication ON(vtiger_refillapplication.refillapplicationid=vtiger_refillapprayment.refillapplicationid)
                LEFT JOIN vtiger_crmentity ON vtiger_refillapplication.refillapplicationid=vtiger_crmentity.crmid
                LEFT JOIN vtiger_users ON(vtiger_refillapprayment.modifiedby=vtiger_users.id)
                WHERE vtiger_refillapprayment.receivedpaymentsid=? AND vtiger_refillapprayment.deleted=0 AND vtiger_refillapprayment.refillapptotal>0";
        $r_result = $db->pquery($sql1,array($recorid,$recorid));
        $num=$db->num_rows($r_result);
         if($num>0){
            for ($i=0;$i<$num;++$i){
                $receivedPaymentsUseDetail[$i] = $db->fetchByAssoc($r_result);
                $receivedPaymentsUseDetail[$i]['rate']=number_format(0,2);
                if( $receivedPaymentsUseDetail[$i]['type'] == 2){
                    $sql2 = "select * from vtiger_refillapprayment where  refillapplicationid =? and deleted=0 and receivedpaymentsid=?";
                    $r1_result = $db->pquery($sql2,array($receivedPaymentsUseDetail[$i]['recordid'],$recorid));

                    $backwashtotal=0;
                    while($res =$db->fetch_array($r1_result)) {
                            $backwashtotal = bcadd(trim($res['backwashtotal']),trim($backwashtotal),2);
                    }
                    $detail_res = explode(':', $receivedPaymentsUseDetail[$i]['detail']);
                    $receivedPaymentsUseDetail[$i]['detail'] = '充值金额: '.trim($backwashtotal);
                    $receivedPaymentsUseDetail[$i]['rate']=bcdiv(bcmul($receivedPaymentsUseDetail[$i]['summoney'],$backwashtotal,10),$receivedPaymentsUseDetail[$i]['totalrecharge'],2);
                }
            }

         }
        return $receivedPaymentsUseDetail;
    }
    /**
     * 根据回款id更改回款状态
     *
     * @param $request
     * @return array
     */
    public function openUpdateReceivedStatus($request){
        $db =PearDatabase::getInstance();
        $receivedpaymentsids = $request->get("receivedpaymentsids");
        $receivedstatus = $request->get("receivedstatus");

        $result = $db->pquery("select 1 from vtiger_receivedpayments where receivedpaymentsid in(".implode(",",$receivedpaymentsids).")",array());
        if(!$db->num_rows($result)){
            return array('success'=>false,'msg'=>'回款id异常');
        }
        if(!in_array($receivedstatus,array('deposit','NonPayCertificate','normal','RebateAmount','refund','SupplierRefund','TimeoutEntry','virtualrefund','void'))){
            return array('success'=>false,'msg'=>'回款状态不合法');
        }
        $db->pquery("update vtiger_receivedpayments set receivedstatus=? where receivedpaymentsid in(".implode(",",$receivedpaymentsids).")",array($receivedstatus));
        return array('success'=>true,'msg'=>'');
    }

    /**
     * 匹配时判断是否超时
     * $iscrossmonthmatch=1，$isMatch=1是从匹配那块来的，自动判断是否超时脚本那块不算是否要核算
     * @param $receivedpaymentsid
     */
    public function matchingWithTimeOut($receivedpaymentsid,$iscrossmonthmatch=1,$isMatch=1){
        global $adb;
        $recordModel=Vtiger_Record_Model::getInstanceById($receivedpaymentsid,'ReceivedPayments',true);
        $nowMonth=date('Y-m');
        $old_reality_date=$this->getCompareTime($receivedpaymentsid,$recordModel->get('reality_date'));
        $createTime=$recordModel->get('createtime');
        Matchreceivements_Record_Model::recordLog('开始计算超时');
        if($old_reality_date){
            $realityDateArray=explode('-',$old_reality_date);
            $monthRealityDate=$realityDateArray[0].'-'.$realityDateArray[1];
            //入账时间不是当月或者匹配时间不是第一个工作日9点
            if($iscrossmonthmatch==1&&$nowMonth!=$monthRealityDate&&!$this->isWorkDayNineHours()){
                //入账时间是否是上月最后一个自然日
                Matchreceivements_Record_Model::recordLog('入账时间是否是上月最后一个自然日');
                if($this->isPreMonthLastDay($old_reality_date)){
                    //录入时间是否是入账时间次月的1日
                    if($this->isRealityNextMonth($createTime,$old_reality_date)){
                        //当前匹配时间与录入时间是否为同一天
                        if(date('Y-m-d')==date('Y-m-d',strtotime($createTime))){
                            //是同一天那就不是跨月的
                            $iscrossmonthmatch=0;
                        }else{
                            $sql="update vtiger_receivedpayments set iscrossmonthmatch=1 where receivedpaymentsid=?";
                            $adb->pquery($sql,array($receivedpaymentsid));
                            $isMatch=2;
                            $this->recordIscheckachievement(0,$receivedpaymentsid,0);
                        }
                    }else{
                        $iscrossmonthmatch=2;
                        //不是同一天，财务操作异常
                        $sql="update vtiger_receivedpayments set iscrossmonthmatch=2 where receivedpaymentsid=?";
                        $adb->pquery($sql,array($receivedpaymentsid));
                        $this->recordIscheckachievement(0,$receivedpaymentsid,0);
                    }
                }else{
                    //录入时间是否为入账时间次日之前
                    if($this->isRealityNextDay($createTime,$old_reality_date)){
                        $sql="update vtiger_receivedpayments set iscrossmonthmatch=1 where receivedpaymentsid=?";
                        $adb->pquery($sql,array($receivedpaymentsid));
                        $isMatch=2;
                        $this->recordIscheckachievement(0,$receivedpaymentsid,0);
                    }else{
                        $iscrossmonthmatch=2;
                        //财务操作异常
                        $sql="update vtiger_receivedpayments set iscrossmonthmatch=2 where receivedpaymentsid=?";
                        $adb->pquery($sql,array($receivedpaymentsid));
                        $this->recordIscheckachievement(0,$receivedpaymentsid,0);
                    }
                }
            }
            Matchreceivements_Record_Model::recordLog(array('入账时间是当月没跨月',$iscrossmonthmatch,$isMatch,$createTime,$old_reality_date),'delay');

            if($iscrossmonthmatch==0 ||($iscrossmonthmatch==1&&$isMatch==1)){
                //录入时间是否为入账时间次日之前
                if($this->isRealityNextDay($createTime,$old_reality_date)){
                    Matchreceivements_Record_Model::recordLog('录入时间为入账时间次日之前'.$isMatch,'delay');
                    if($isMatch==0){
                        //当前时间距离入账时间是否超过5个工作日
                        if($this->isTimeOverFiveDays($old_reality_date)){
                            $sql="update vtiger_receivedpayments set istimeoutmatch=1 where receivedpaymentsid=?";
                            $adb->pquery($sql,array($receivedpaymentsid));
                        }
                    }else{
                        Matchreceivements_Record_Model::recordLog('已匹配');
                        //匹配时间距离入账时间是否超过5个工作日
                        if($this->isMatchTimeOverFiveDays($old_reality_date)){
                            Matchreceivements_Record_Model::recordLog('匹配时间距离入账时间超过5个工作日','delay');
                            $sql="update vtiger_receivedpayments set istimeoutmatch=1 where receivedpaymentsid=?";
                            $adb->pquery($sql,array($receivedpaymentsid));
                            $this->recordIscheckachievement(0,$receivedpaymentsid,0);
                        }
                    }
                }else{
                    //财务操作异常
                    Matchreceivements_Record_Model::recordLog(array('财务操作异常',$createTime,$old_reality_date,$receivedpaymentsid),'delay');
                    if($isMatch==1){
                        $this->recordIscheckachievement(0,$receivedpaymentsid,0);
                    }
                    $sql="update vtiger_receivedpayments set istimeoutmatch=2 where receivedpaymentsid=?";
                    $adb->pquery($sql,array($receivedpaymentsid));
                }
            }
        }
    }

    /**
     * 判断是否第一天工作日
     */
    public function isWorkDayNineHours(){
        global $adb;
        $matchDay=date('j');
        $matchHour=date('G');
        if($matchDay==1&&$matchHour<9){
            //第一天且小于9时
            return true;
        }else{
            if($matchDay>1&&$matchHour<9){
                //不是第一天,算算今天前面的假期有几天
                $sql="select * from vtiger_workday where dateday like '".date('Y-m')."%' and dateday>='".date('Y-m')."-01' and dateday<'".date('Y-m-d')."' and  datetype='holiday' ";
                $holidayArray=$adb->run_query_allrecords($sql);//获取所有节假日
                if(count($holidayArray)==$matchDay){
                    return  true;
                }
            }
        }
        return false;
    }

    /**
     * 入账时间是否是上月最后一个自然日
     * @param $reality_date
     */
    public function isPreMonthLastDay($reality_date){
        $year=date('Y');
        $month=date('n');
        if($month=='1'){
            $month='12';
            $year=$year-1;
        }else{
            $month=$month-1;
        }
        $days=date('t',strtotime($year.'-'.$month.'-1'));//获取上个月的实际天数
        if(date('Y-m-d',strtotime($year.'-'.$month.'-'.$days))==$reality_date){
            return true;
        }
        return false;
    }

    /**
     * 录入时间是否入账时间次月的1日
     */
    public function isRealityNextMonth($createtime,$reality_date){
        $year=date('Y',strtotime($reality_date));
        $month=date('n',strtotime($reality_date));
        if($month=='12'){
            $month='1';
            $year=$year+1;
        }else{
            $month=$month+1;
        }
        if(date('Y-m-d',strtotime($year.'-'.$month.'-1'))==date('Y-m-d',strtotime($createtime))){
            return true;
        }
        return false;
    }

    /**
     * 录入时间是否为入账时间次日之前
     * @param $createTime
     * @param $old_reality_date
     */
    public function isRealityNextDay($createTime,$old_reality_date){
        if(strtotime($createTime)-strtotime($old_reality_date)<=24*3600*2){
            return true;
        }
        return false;
    }

    /**
     * 匹配时间距离入账时间是否超过5个工作日
     * @param $old_reality_date
     */
    public function isMatchTimeOverFiveDays($old_reality_date){
        global $adb;
        $overDays=(strtotime(date('Y-m-d'))-strtotime($old_reality_date))/3600/24;//相差时间
        $dayArray=array();
        for ($i=0;$i<=$overDays;$i++){
            array_push($dayArray,date('Y-m-d',strtotime('+'.$i.' days',strtotime($old_reality_date))));
        }
        $sql="select * from vtiger_workday where dateday in ('".implode('\',\'',$dayArray)."') and datetype='holiday'";
        $holidayArray=$adb->run_query_allrecords($sql);//获取所有节假日
        Matchreceivements_Record_Model::recordLog('开始比较');
        if(count(array_diff($dayArray,array_column($holidayArray,'dateday')))>5){//去除节假日后依然大于5天的
            Matchreceivements_Record_Model::recordLog('大于5天');
            return true;
        }
        return false;
    }

    /**
     * 当前时间距离入账时间是否超过5个工作日
     * @param $old_reality_date
     * @return bool
     */
    public function isTimeOverFiveDays($old_reality_date){
        global $adb;
        $overDays=(strtotime(date('Y-m-d'))-strtotime($old_reality_date))/3600/24;//相差时间
        $dayArray=array();
        for ($i=0;$i<=$overDays;$i++){
            array_push($dayArray,date('Y-m-d',strtotime('+'.($i-1).' days',strtotime($old_reality_date))));
        }
        $sql="select * from vtiger_workday where dateday in ('".implode('\',\'',$dayArray)."') and datetype='holiday'";
        $holidayArray=$adb->run_query_allrecords($sql);//获取所有节假日
        if(count(array_diff($dayArray,array_column($holidayArray,'dateday')))>5){//去除节假日后依然大于5天的
            return true;
        }
        return false;
    }

    /**
     * app方面设置回款金额
     * @param Vtiger_Request $request
     */
    public function setKLLReceivedPayment(Vtiger_Request $request){
        $db =PearDatabase::getInstance();
        $changeList = $request->get("changeList");
        $userId = $request->get("userId");
        $userName = $request->get("userName");
        $return=array('flag'=>true);
        $changeArray=json_decode(base64_decode($changeList),true);
        $flag=true;
        foreach ($changeArray as $key => $change){
            $recordModel=Vtiger_Record_Model::getInstanceById($change['id'],'ReceivedPayments',true);
            $rechargeableamount=$recordModel->get('rechargeableamount');//可使用金额
            if(bcsub($rechargeableamount,$change['remainingAmount'],2)!=0){
                $flag=false;
                $return['flag']=false;
                $return['msg'][$key]=$change['id'].'剩余可使用金额不对，erp中剩余可使用金额'.$rechargeableamount;
            }
        }
        if($flag){
            foreach ($changeArray as $key => $change){
                //如果剩余金额一样那就下一步
                $rechargeableamountNew=bcsub($change['remainingAmount'],$change['useMoney'],2);
                $updateSql="update vtiger_receivedpayments set rechargeableamount=? where receivedpaymentsid=?";
                $db->pquery($updateSql,array($rechargeableamountNew,$change['id']));
                $modtrackerBasicData = array();
                $modtrackerBasicId = $db->getUniqueID("vtiger_modtracker_basic");
                $modtrackerBasicData['id'] = $modtrackerBasicId;
                $modtrackerBasicData['crmid'] = $change['id'];
                $modtrackerBasicData['module'] = 'ReceivedPayments';
                $modtrackerBasicData['whodid'] = $userId;
                $modtrackerBasicData['changedon'] = date('Y-m-d H:i:s');
                $modtrackerBasicData['status'] = '0';
                $divideNames = array_keys($modtrackerBasicData);
                $divideValues = array_values($modtrackerBasicData);
                $db->pquery('INSERT INTO `vtiger_modtracker_basic` (' . implode(',', $divideNames) . ') VALUES (' . generateQuestionMarks($divideValues) . ')', $divideValues);
                // vtiger_modtracker_detail
                $modtrackerDetailData = array();
                $modtrackerDetailData['id'] = $modtrackerBasicId;
                $modtrackerDetailData['fieldname'] = 'rechargeableamount';
                $modtrackerDetailData['prevalue'] = $change['remainingAmount'];
                $modtrackerDetailData['postvalue'] = $userName . ' 通过凯丽隆接口修改回款剩余可使用金额' . $rechargeableamountNew;
                $divideNames = array_keys($modtrackerDetailData);
                $divideValues = array_values($modtrackerDetailData);
                $db->pquery('INSERT INTO `vtiger_modtracker_detail` (' . implode(',', $divideNames) . ') VALUES (' . generateQuestionMarks($divideValues) . ')', $divideValues);
                $return['msg'][$key]=$change['id'].'修改成功，剩余可使用金额'.$rechargeableamountNew;
            }
        }
        return $return;
    }

    /**
     * 从资金系统插入回款
     * @param Vtiger_Request $request
     */
    public function addReceivedPaymentsFromCBS(Vtiger_Request $request){
        global $adb;
        $bankAccount=$request->get('bankAccount');
        $currency=$request->get('currency');
        $exchangeRate=$request->get('exchangeRate');
        $entryDate=$request->get('entryDate');
        $payTitle=$request->get('payTitle');
        $paymentCode=$request->get('paymentCode');
        $money=$request->get('money');
        $remark=$request->get('remark');
        $data['paymentchannel']='对公转账';
        $sql="select * from vtiger_companyaccounts where account=? and channel='对公转账'";
        $result=$adb->pquery($sql,array($bankAccount));
        if($adb->num_rows($result)>0){
            $companyResultArray=$adb->query_result_rowdata($result,0);
            $data['receivedpaymentsid']=$adb->getUniqueID('vtiger_receivedpayments');
            $data['owncompany']=$companyResultArray['company'].'##'.$companyResultArray['bank'].'-'.$companyResultArray['subbank'].'（'.$companyResultArray['account'].'）';
            $data['receivementcurrencytype']=$currency;
            $data['exchangerate']=$exchangeRate;
            $data['reality_date']=$entryDate;
            $data['paytitle']=$payTitle;
            $data['standardmoney']=$money;
            $data['unit_price']=$data['allowinvoicetotal']=$data['rechargeableamount']=bcmul($money,$exchangeRate,2);
            $data['paymentcode']=$paymentCode;
            $data['overdue']=$remark.'（CBS系统推送）';
            $data['createtime']=$data['modifiedtime']=date('Y-m-d H:i:s');
            $data['checkid']=$data['createid']=6934;
            $data['maybe_account']=0;
            $accountdata = ReceivedPayments_Record_Model::match_account(trim($payTitle));
            if($accountdata['crmid']){
                $data['maybe_account']=$accountdata['crmid'];
            }
            $adb->run_insert_data('vtiger_receivedpayments',$data);
            return array('success'=>true);
        }else{
            //没有这个账号
            return array('success'=>false,'errMsg' => '系统中没有此银行账号');
        }
    }

    /**
     * @param $isDeleteAchievement 是否需要删除之前的业绩
     * @param $receivedpaymentsid 回款id
     */
    public function recordIscheckachievement($isDeleteAchievement,$receivedpaymentsid,$achievementmonth){
        global $adb;
        if($isDeleteAchievement){
            //删除业绩
            $deleteStastic_sql='DELETE  FROM  vtiger_achievementallot_statistic WHERE achievementmonth=? and arriveachievement>=0 and  receivedpaymentsid = ?';
            $adb->pquery($deleteStastic_sql,array($achievementmonth,$receivedpaymentsid));
            Matchreceivements_Record_Model::recordLog($receivedpaymentsid.'业绩还未记录，删除','achievement');
        }
        Matchreceivements_Record_Model::recordLog($receivedpaymentsid.'做计算记录','achievement');
        $sql="update vtiger_receivedpayments set ischeckachievement=0 where receivedpaymentsid=?";
        $adb->pquery($sql,array($receivedpaymentsid));
    }

    /**
     * 修改业绩提成
     * @param $contract_no
     */
    public function modifyAchievement($contract_no){
        global $adb;
        $sql="SELECT vtiger_receivedpayments.receivedpaymentsid FROM vtiger_servicecontracts LEFT JOIN vtiger_receivedpayments ON vtiger_servicecontracts.servicecontractsid=vtiger_receivedpayments.relatetoid WHERE vtiger_servicecontracts.contract_no='".$contract_no."'";
        $idArray=array_column($adb->run_query_allrecords($sql),'receivedpaymentsid');
        Matchreceivements_Record_Model::recordLog($contract_no.'查看所有匹配的回款','achievement');
        //获取已匹配这里的所有回款，没有就不处理
        if($idArray){
            foreach ($idArray as $id){
                $sql='select * FROM vtiger_achievementallot_statistic where receivedpaymentsid='.$id;
                $resultAll=$adb->run_query_allrecords($sql);
                //没业绩，不处理
                if($resultAll){
                    Matchreceivements_Record_Model::recordLog($id.'处理此回款','achievement');
                    foreach ($resultAll as $result){
                        foreach ($result as $key =>$value){
                            if(is_numeric($key)){
                                unset($result[$key]);
                            }
                        }
                        Matchreceivements_Record_Model::recordLog(array($result,$id.'看一下回款金额'),'achievement');
                        Matchreceivements_Record_Model::recordLog($id.'此回款的业绩提成已发放','achievement');
                        $status=$result['status'];
                        $isover=$result['isover'];
                        $achievementmonth=$result['achievementmonth'];
                        if($status||$isover){
                            if(!$result['istwentyroyalty']){
                                //未发放20%的业绩
                                $sql="update vtiger_achievementallot_statistic set istwentyroyalty=?,remarks=? where achievementallotid=?";
                                $adb->pquery($sql,array(1,$result['remarks'].'。未发放20%的提成取消激活订单',$result['achievementallotid']));
                            }else{
                                //已发放20%的业绩
                                $commissionforrenewal=bcadd($result['commissionforrenewal'],$result['twentyroyalty'],2);
                                $result['remarks'].='。已发放20%的业绩'.$commissionforrenewal;
                                $result['commissionforrenewal']=$commissionforrenewal;
                            }
                            Matchreceivements_Record_Model::recordLog($id.'此回款的业绩提成已发放2','achievement');
                            //业绩提成已发放,新增业绩当负数
                            unset($result['achievementallotid']);
                            $result['achievementmonth']=date('Y-m');
                            $result['arriveachievement'] *=-1;
                            $result['effectiverefund'] *=-1;
                            $result['adjustbeforearriveachievement'] *=-1;
                            $result['remarks'].='。提成已发放订单取消激活';
                            Matchreceivements_Record_Model::recordLog($adb->sql_insert_data('vtiger_achievementallot_statistic',$result),'achievement');
                            $adb->run_insert_data('vtiger_achievementallot_statistic',$result);
                            $this->recordIscheckachievement(0,$id,$achievementmonth);
                        }else{
                            //有业绩没发放，删掉业绩等重新计算
                            Matchreceivements_Record_Model::recordLog($id.'此回款没业绩','achievement');
                            $this->recordIscheckachievement(1,$id,$achievementmonth);
                        }
                    }
                }
            }
        }
    }

    /**
     * 算是否超时的比较时间，如果未解绑返回入账时间，解绑了返回上次解绑时间
     * @param $id
     */
    public function getCompareTime($id,$reality_date){
        global $adb;
        $sql="select changetime from vtiger_receivedpayments_changedetails where receivedpaymentsid=? and changetype='解绑' order by changedetailid desc limit 1";
        $result=$adb->pquery($sql,array($id));
        if($adb->num_rows($result)>0){
            return date('Y-m-d',strtotime($adb->query_result($result,0,'changetime')));
        }
        return $reality_date;
    }
    
    
    public function getCompanyAccountsIdByOwnCompany($ownCompany){
        if(!$ownCompany){
            return '';
        }
        $ownCompanyArr = explode("##",$ownCompany);
        $ownCompanyStr=$ownCompanyArr[1];
        $pos = strpos($ownCompanyStr,'（');
        $str = substr($ownCompanyStr,$pos);
        $str = ltrim($str,'（');
        $str = rtrim($str,'）');
        $db = PearDatabase::getInstance();
        $result = $db->pquery("select id from vtiger_companyaccounts where account in(?,?)",array(trim($str),$str."）"));
        if($db->num_rows($result)){
            $row = $db->fetchByAssoc($result,0);
            return $row['id'];
        }
        return '';
    }

    public function financialstate($record){
        $db=PearDatabase::getInstance();
        $result =$db->pquery("select a.receivedpaymentscollateid from vtiger_receivedpaymentscollate  a left join vtiger_crmentity  b 
  on a.receivedpaymentscollateid=b.crmid where a.receivedpaymentsid=? and b.deleted=0",array($record));
        if(!$db->num_rows($result)){
            return '';
        }
        $row=$db->fetchByAssoc($result,0);

        return $row['receivedpaymentscollateid'];
    }

    public function isCollateInReview($record){
        $db=PearDatabase::getInstance();
        $result =$db->pquery("select a.modulestatus from vtiger_receivedpaymentscollate  a left join vtiger_crmentity  b 
  on a.receivedpaymentscollateid=b.crmid where a.receivedpaymentsid=? and b.deleted=0",array($record));
        if(!$db->num_rows($result)){
            return false;
        }
        $row=$db->fetchByAssoc($result,0);

        if($row['modulestatus']=='b_check'){
            return true;
        }
        return false;
    }
}
