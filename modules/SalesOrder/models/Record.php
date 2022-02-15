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
class SalesOrder_Record_Model extends Vtiger_Record_Model {

	function getCreateInvoiceUrl() {
		$invoiceModuleModel = Vtiger_Module_Model::getInstance('Invoice');

		return "index.php?module=".$invoiceModuleModel->getName()."&view=".$invoiceModuleModel->getEditViewName()."&salesorder_id=".$this->getId();
	}
	//工单流程阶段
	function getAllSalesorderWorkflowStages($records){
		$db = PearDatabase::getInstance();
		$result = $db->pquery("SELECT workflowstagesname,salesorderid,ifnull(rejectid,0) as rejectid  FROM `vtiger_salesorderworkflowstages` where salesorderid in($records) and isaction=1 GROUP BY salesorderid",array());
		$stages=array();
// 		for($i=0; $i<$db->num_rows($result); $i++) {
// 			$workflowstagesname = $db->query_result($result, $i, 'workflowstagesname');
// 			$id =	$db->query_result($result, $i, 'salesorderid');
// 			//$workflowstagesname = $db->query_result($result, $i, 'workflowstagesname');
// 			$stages[$id]=array('salesorderid'=>$id,'workflowstagesname'=>$workflowstagesname);
// 		}
		if($db->num_rows($result)){
			while($row=$db->fetch_array($result)){
				$stages[$row['salesorderid']]=$row;
			}
		}
		return  Zend_Json::encode($stages);
	}
	//解决表单问题
	function get($key) {
		$value = parent::get($key);
		if ($key === 'notecontent') {
			return decode_html($value);
		}
		return $value;
	}
	function getProducts($salesorderid){
		//return null;
		$relateProducts=SalesorderProductsrel_Record_Model::getRelateProduct($salesorderid);
		$temp = array();
		foreach($relateProducts as $rp){
			$salesorderProducts=SalesorderProductsrel_Record_Model::getInstanceById($rp['salesorderproductsrelid'],'SalesorderProductsrel');
			$salesorderData = $salesorderProducts->getData();
			$temp[]=array_merge($salesorderData,$rp);
		}
		return $temp;
	}
	//是否可以作废
	public function isDeletedable(){
		//新建或被打回状态下创建者可以删除工单
		$data=$this->getRawData();
		if(in_array($data['modulestatus'],array('a_exception','a_normal','c_lackpayment','b_actioning','b_check'))){
			return true;
		}
		return false;
	}

	public function delete() {
		$data=$this->entity->column_fields;
		global $current_user;
		if(!in_array($data['modulestatus'],array('a_exception','a_normal')) || ($current_user->is_admin!='on'&&$current_user->id!=$data['assigned_user_id'])){
			echo '当前节点不可删除！<a href="javascript:history.go(-1);">返回</a>';
			exit;
		}
		$db = PearDatabase::getInstance();
		$db->pquery('update vtiger_salesorderworkflowstages set isvalidity=1 where salesorderid=?',array($data['record_id']));
		$db->pquery('update vtiger_salesorder set servicecontractsid=0 where salesorderid=?',array($data['record_id']));
		$this->getModule()->deleteRecord($this);


	}

    /**
     * 获取回款明细数据 gaocl add 2018/05/14
     * @param $salesorderid
     * @return array
     */
    public function getSalesorderRaymentData(Vtiger_Request $request) {
        $servicecontractid=$request->get('servicecontractid');
        $recordId = $request->get('record');
        if (empty($servicecontractid)) {
            return array();
        }
        global $adb,$current_user;
        //查找当前审核节点(必须是提单人审核几点才可获取回款数据，匹配回款)
        $work_sql = "SELECT vtiger_workflowstages.handleaction AS customer,vtiger_workflowstages.workflowstagesflag,vtiger_salesorderworkflowstages.higherid FROM	vtiger_salesorderworkflowstages
                    LEFT JOIN vtiger_workflowstages ON (vtiger_salesorderworkflowstages.workflowstagesid = vtiger_workflowstages.workflowstagesid)
                    WHERE	vtiger_salesorderworkflowstages.salesorderid=? AND isaction=1";
        $work_result=$adb->pquery($work_sql,array($recordId));
        if(empty($work_result)) return array();
        $rowdata = $adb->query_result_rowdata($work_result,0);
        $workflowstagesflag = $rowdata['workflowstagesflag'];
        $higherid = $rowdata['higherid'];
        $is_rayment_match = false;
        if($workflowstagesflag == 'RAYMENT_MATCH' && $higherid == $current_user->id){
            $is_rayment_match = true;
        }

        //查询工单关联回款数据
        $salesorder_sql = "SELECT israyment,vtiger_crmentity.smownerid,
                          (SELECT COUNT(1) FROM vtiger_salesorderrayment WHERE vtiger_salesorderrayment.deleted=0 AND vtiger_salesorderrayment.salesorderid=vtiger_salesorder.salesorderid) AS raymentcount FROM vtiger_salesorder LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_salesorder.salesorderid WHERE salesorderid=?";
        $salesorder_result=$adb->pquery($salesorder_sql,array($recordId));
        $rowdata = $adb->query_result_rowdata($salesorder_result,0);
        $israyment = $rowdata['israyment'];
        $raymentcount = $rowdata['raymentcount'];
        $smownerid = $rowdata['smownerid'];
        $data=array();
        if($israyment == '1' && $raymentcount > 0){//更改回款匹配后所以的节点都可以匹配回款

            if($current_user->id==$smownerid){
                $sql = "SELECT 
                vtiger_receivedpayments.*,
                IFNULL(vtiger_salesorderrayment.laborcost,'0.00') AS laborcost,
                IFNULL(vtiger_salesorderrayment.purchasecost,'0.00') AS purchasecost,
                vtiger_salesorderrayment.remarks as rremarks,
                0 AS israyment
                FROM vtiger_receivedpayments
                LEFT JOIN vtiger_salesorderrayment ON(
                 vtiger_salesorderrayment.deleted=0 
                 AND vtiger_salesorderrayment.receivedpaymentsid=vtiger_receivedpayments.receivedpaymentsid
                 AND vtiger_salesorderrayment.salesorderid=?
                )
                WHERE vtiger_receivedpayments.receivedstatus='normal' AND vtiger_receivedpayments.deleted=0 AND rechargeableamount>0 AND vtiger_receivedpayments.relatetoid=? ORDER BY vtiger_receivedpayments.receivedpaymentsid";
                $result=$adb->pquery($sql,array($recordId,$servicecontractid));
                while($row=$adb->fetch_array($result)){
                    $row['is_rayment_edit'] = true;
                    $row['is_rayment_save'] = true;
                    $data[]=$row;
                };
            }
            $sql = "SELECT 
                vtiger_receivedpayments.*,
                IFNULL(vtiger_salesorderrayment.laborcost,'0.00') AS laborcost,
                IFNULL(vtiger_salesorderrayment.purchasecost,'0.00') AS purchasecost,
                vtiger_salesorderrayment.remarks as rremarks,
                1 AS israyment
                FROM vtiger_salesorderrayment
                JOIN vtiger_receivedpayments ON(
                  vtiger_salesorderrayment.receivedpaymentsid=vtiger_receivedpayments.receivedpaymentsid
                )
                WHERE  vtiger_salesorderrayment.deleted=0 AND vtiger_salesorderrayment.salesorderid=? ORDER BY vtiger_receivedpayments.receivedpaymentsid";
            $result=$adb->pquery($sql,array($recordId));
            while($row=$adb->fetch_array($result)){
                $row['is_rayment_edit'] = false;
                $row['is_rayment_save'] = false;
                $data[]=$row;
            };


        }else{
            if($is_rayment_match){
                $sql = "SELECT 
                vtiger_receivedpayments.*,
                IFNULL(vtiger_salesorderrayment.laborcost,'0.00') AS laborcost,
                IFNULL(vtiger_salesorderrayment.purchasecost,'0.00') AS purchasecost,
                vtiger_salesorderrayment.remarks as rremarks,
                0 AS israyment
                FROM vtiger_receivedpayments
                LEFT JOIN vtiger_salesorderrayment ON(
                 vtiger_salesorderrayment.deleted=0 
                 AND vtiger_salesorderrayment.receivedpaymentsid=vtiger_receivedpayments.receivedpaymentsid
                 AND vtiger_salesorderrayment.salesorderid=?
                )
                WHERE vtiger_receivedpayments.receivedstatus='normal' AND vtiger_receivedpayments.deleted=0 AND rechargeableamount>0 AND vtiger_receivedpayments.relatetoid=? ORDER BY vtiger_receivedpayments.receivedpaymentsid";
                $result=$adb->pquery($sql,array($recordId,$servicecontractid));
                while($row=$adb->fetch_array($result)){
                    $row['is_rayment_edit'] = $is_rayment_match;
                    $row['is_rayment_save'] = false;
                    $data[]=$row;
                };
            }else{
                return array();
            }
        }
        return $data;
    }

    /**
     * 获取总的工单成本
     * @param Vtiger_Request $request
     */
    public function getTotalSalesorderCost(Vtiger_Request $request){
        global $adb;
        $recordId = $request->get('record');
        $sql = "SELECT 
                  SUM(IFNULL(laborcost,0)) AS totallaborcost,
                  SUM(IFNULL(purchasecost,0)) AS totalpurchasecost,
                  SUM(IFNULL(laborcost,0)+IFNULL(purchasecost,0)) AS totalsalesordercost
                FROM vtiger_salesorderrayment WHERE 
                 vtiger_salesorderrayment.deleted=0 
                 AND vtiger_salesorderrayment.salesorderid=?";
        $result=$adb->pquery($sql,array($recordId));
        $rowdata = $adb->query_result_rowdata($result,0);
        if(empty($rowdata['totalsalesordercost'])) {
            $rowdata['totalsalesordercost'] = 0;
        }
        return $rowdata;
    }

    /**
     * 保存、提交工单回款数据
     * @param Vtiger_Request $request
     */
    function saveSalesorderRayment(Vtiger_Request $request){
        global $current_user,$adb;
        $type = $request->get("type");//1:只保存，2:保存并提交

        $ret_result = array("success"=>true,"message"=>"");
        $arr_salesorderrayment = $request->get("arr_salesorderrayment");
        if(count($arr_salesorderrayment) <= 0) {
            $ret_result = array("success"=>false,"message"=>"没有设置工单匹配回款数据");
            return $ret_result;
        }

        //验证合计值是否相等
        $totalcost = floatval($arr_salesorderrayment[0]['totalcost']);//工单产品明细总成本
        $salesordercost = 0;
        for($i=0;$i<count($arr_salesorderrayment);$i++) {
            $salesordercost_tmp = floatval($arr_salesorderrayment[$i]['salesordercost']);//人力成本+外采成本
            $salesordercost += $salesordercost_tmp;
        }
        if(bcsub($totalcost,$salesordercost) > 0){
            $ret_result = array("success"=>false,"message"=>"工单使用金额合计必须大于等于工单总成本(".$totalcost.")");
            return $ret_result;
        }


        //保存处理
        //工单id
        $salesorderid=$arr_salesorderrayment[0]['salesorderid'];
        if($salesorderid<=0){
            $ret_result = array("success"=>false,"message"=>"没有设置工单匹配回款数据");
            return $ret_result;
        }
        $temp=0;
        $tempArray=array();
        for($i=0;$i<count($arr_salesorderrayment);$i++){
            $receivedpaymentsid = $arr_salesorderrayment[$i]['receivedpaymentsid'];
            if($receivedpaymentsid<=0){
                continue;
            }
            ++$temp;
            $availableamount=$arr_salesorderrayment[$i]['availableamount'];
            $occupationcost=$arr_salesorderrayment[$i]['occupationcost'];
            //$laborcost=$arr_salesorderrayment[$i]['laborcost'];//人力成本
            $laborcost=0;//人力成本
            $purchasecost=$arr_salesorderrayment[$i]['purchasecost'];//外采成本
            $totalcost=$arr_salesorderrayment[$i]['totalcost'];//工单产品明细总成本
            $rremarks=$arr_salesorderrayment[$i]['rremarks'];

            //更新工单回款表
            //$adb->pquery("UPDATE vtiger_salesorderrayment SET deleted=1 WHERE salesorderid=? AND receivedpaymentsid=?",array($salesorderid,$receivedpaymentsid));
            $update_sql = "REPLACE INTO vtiger_salesorderrayment(salesorderid,receivedpaymentsid,availableamount,occupationcost,laborcost,purchasecost,totalcost,modifiedby,modifiedtime,deleted,remarks) VALUES(?,?,?,?,?,?,?,?,NOW(),0,?)";
            $adb->pquery($update_sql,array($salesorderid,$receivedpaymentsid,$availableamount,$occupationcost,$laborcost,$purchasecost,$totalcost,$current_user->id,$rremarks));

            if($type == '2'){
                //更新回款表中的已使用工单成本、可使用金额
                $salesordercost = $arr_salesorderrayment[$i]['salesordercost'];//人力成本+外采成本
                $adb->pquery("UPDATE `vtiger_receivedpayments` SET occupationcost=(occupationcost+{$salesordercost}),rechargeableamount=if((rechargeableamount-{$salesordercost})>0,(rechargeableamount-{$salesordercost}),0) WHERE receivedpaymentsid=?",array($receivedpaymentsid));
            }
            /*$tempArray[]=array(
                'receivedpaymentsid'=>$receivedpaymentsid,
                'salesorderid'=>$salesorderid,
                'purchasecost'=>$purchasecost,
            );*/
        }

        if($type == '2' && $temp>0) {
            //更新回款匹配成功标志
            $sql = "SELECT SUM(laborcost+purchasecost) AS salesordercost,MAX(totalcost) AS totalcost FROM `vtiger_salesorderrayment` WHERE salesorderid=?";
            $result = $adb->pquery($sql, array($salesorderid));
            $num = $adb->num_rows($result);
            if ($num > 0) {
                $rowdata = $adb->query_result_rowdata($result, 0);
                /*if (!empty($rowdata['totalcost']) && !empty($rowdata['salesordercost'])
                    && $rowdata['totalcost'] == $rowdata['salesordercost']) {*/
                    $dateime=date('Y-m-d');
                    $adb->pquery("UPDATE `vtiger_salesorder` SET israyment=1,performanceoftime=IFNULL(performanceoftime,'{$dateime}') WHERE salesorderid=?", array($salesorderid));
                //}
                /*foreach($tempArray as $value){
                    $this->calcSalesorderAchievement($value);
                }*/
            }
        }
        return $ret_result;
    }

    /**
     * 判断是否有工单产品
     * @param  $recordId
     */
    public function isSalesorderProductsRel($recordId){
        global $adb;
        $result=$adb->pquery("select 1 from vtiger_salesorderproductsrel where salesorderproductsrelstatus='pass' AND salesorderid=?",array($recordId));
        if($adb->num_rows($result)){
            return true;
        }
        return false;
    }
    /**
     * 判断 工单回款不足流程冻结情况下可以重新激活上一个节点修改 显示button
     */
    public function activeRepairOrder($salesorderid) {

        global $current_user;

        $db = PearDatabase::getInstance();
        $sql = "select * from  vtiger_salesorder WHERE  salesorderid=?";
        $checkproductusers = $db->pquery($sql, array($salesorderid));
        $numRows = $db->fetch_row($checkproductusers);

        $sql = "select * from vtiger_salesorderworkflowstages where salesorderid = ? ";
        $salesorderworkflowstages = $db->pquery($sql, array($salesorderid));
        $salesorderworkflowstagesRows = $db->fetch_row($salesorderworkflowstages);
        if (!empty($numRows) && $salesorderworkflowstagesRows['auditorid'] == $current_user->id) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * 工单业绩核算
     */
    public function calcSalesorderAchievement($params){
        global $adb,$current_user;
        $receivepayid=$params['receivedpaymentsid'];
        $shareuser=0;
        $salesorderid=$params['salesorderid'];
        $total=$params['purchasecost'];
        $currentid=$current_user->id;
        $matchdate=date('Y-m-d');
        $BasicAjaxAction=new Matchreceivements_BasicAjax_Action();

        $adb = PearDatabase::getInstance();

        //① 查询回款相关数据  vtiger_receivedpayments
        $queryc="SELECT s.multitype,s.contract_no,s.oldcontract_usedtime,s.oldcontractid,s.extraproductid,s.productid,s.invoicecompany,s.parent_contracttypeid,s.contract_type,s.servicecontractsid,s.total,r.owncompany,LEFT (r.createtime,10) AS createtime,r.reality_date,r.matchdate,r.paytitle,r.unit_price,d.departmentname as department,s.servicecontractstype,left(s.signdate,10) AS signdate,s.contract_no,a.accountname FROM vtiger_receivedpayments  as r LEFT JOIN vtiger_departments as d ON r.departmentid=d.departmentid LEFT JOIN vtiger_servicecontracts as s ON s.servicecontractsid=r.relatetoid LEFT JOIN vtiger_account as a ON a.accountid=s.sc_related_to  WHERE receivedpaymentsid = ?  ORDER BY receivedpaymentsid DESC LIMIT 1 ";
        $resultdatapayments=$adb->pquery($queryc,array($receivepayid));
        $rp=$adb->query_result_rowdata($resultdatapayments,0);
        $contractid=$rp['servicecontractsid'];
        $query='SELECT  *  FROM  vtiger_activationcode  WHERE  contractid='.$contractid.'  AND  comeformtyun=1 AND `status` in(0,1) LIMIT 1';//是否有T云订单如果有T云订单则不生成业绩
        $result=$adb->pquery($query,array());
        if($adb->num_rows($result)){//存在T云订单则不生成
            return;
        }

        // 如果是（4）单纯的域名空间维护费续费不计入续费业绩（① 网站建设系列->TSITE续费合同,② IDC类->(“域名、珍岛云、邮箱合同”,"服务器运行维护合同")）
        if((($rp['parent_contracttypeid']==9) ||(($rp['contract_type']=='域名、珍岛云、邮箱合同'||$rp['contract_type']=='服务器运行维护合同') && $rp['parent_contracttypeid']==1)) && (strpos($rp['contract_no'],"XF")!=false || $rp['servicecontractstype']=='续费')){
            $paramers['contract_no']=$rp['contract_no'];
            $paramers['marks']='（4）单纯的域名空间维护费续费不计入续费业绩（① 网站建设系列->TSITE续费合同,② IDC类->(“域名、珍岛云、邮箱合同”,"服务器运行维护合同")）&& (strpos($rp[\'contract_no\'],"XF")!=false || $rp[\'servicecontractstype\']==\'续费\')';
            $Matchreceivements_Record_Model=Vtiger_Record_Model::getCleanInstance("Matchreceivements");
            $Matchreceivements_Record_Model->noCalculationAchievementRecord($paramers);
            return ;
        }
        //院校版不生成业绩  start  ac.status IN(0,1)
        $collegeedition=$adb->pquery("SELECT  iscollegeedition  FROM vtiger_activationcode WHERE  contractid=? AND status IN(0,1) AND iscollegeedition=1 ",array($contractid));
        //如果是院校版订单则不生成业绩
        if($adb->num_rows($collegeedition)>0){
            $paramers['contract_no']=$rp['contract_no'];
            $paramers['marks']='院校版则不生成业绩。';
            $Matchreceivements_Record_Model=Vtiger_Record_Model::getCleanInstance("Matchreceivements");
            $Matchreceivements_Record_Model->noCalculationAchievementRecord($paramers);
            return;
        }
        //院校版不生成业绩 end
        $tsite=array(609236,609234,609237,609704,609230,609228,528504,609733,609735,25,24,136,137);
        //非saas类数组产品  头四个公式六已确认
        $noSaaS=array(361005,377277,361594,362103,361001,362103,362104,391124,2143595,2226512,2115476,462151);//现在只有三个还剩余两个（两个已确认不算了）
        if(!empty($rp['extraproductid'])){
            $extraprod=explode(",",$rp['extraproductid']);
            $otherTypeTrue=array_intersect($noSaaS,$extraprod);
            if(!empty($otherTypeTrue)){
                $otherTypeTrue=true;
            }

        }else{
            $otherTypeTrue =false;
        }
        $remark='';
        // 默认为零  只的是是否要走 数据插入处理
        $type=0;
        if(($rp['parent_contracttypeid']==4 && ($rp['contract_type']=='Yandex竞价' || $rp['contract_type']=='GOOGLE竞价合同')) ||  in_array($rp['contract_type'],array('GOOGLE竞价合同','Yandex竞价','媒介.Yandex','媒介.GOOGLE'))){//充值时生成业绩
            //如果是充值单的特殊处理
            //$BasicAjaxAction->rechargeCalculation($rp,$contractid,$receivepayid,$shareuser,$total,$currentid,$remark,$adb,$params,$matchdate);
            return ;
            //公式四 到账业绩(百度V认证)
        }else if(strpos($rp['contract_no'],"VRZ")){//回款匹配时生成业绩
            return ;
            //$data=$BasicAjaxAction->VRZCalculationAchievement($rp,$contractid,$receivepayid,$shareuser,$total,$currentid,$remark,$adb,$matchdate);
            //公式三 到账业绩（非SAAS类）
        }else if(in_array($rp['productid'],$noSaaS) || $otherTypeTrue){
            $data=$BasicAjaxAction->othersExtraCalculationAchievementsalesorder($rp,$contractid,$receivepayid,$shareuser,$total,$currentid,$remark,$adb,$matchdate,$salesorderid);
            //如果合同属于T云系列----------合同属于T云系列的，就算产品在$tsite中，也走T云业绩流程
        }else if($rp['parent_contracttypeid']==2){

            // T云非标产品
            if($rp['contract_type']=='T云系列补充协议（非标）'){
                $data=$BasicAjaxAction->tYunNonstandardCalculationAchievement($rp,$contractid,$receivepayid,$shareuser,$total,$currentid,$remark,$adb,$matchdate,$salesorderid);
            }else{//T云类的如果工单，工单权限最高，所有数据以工单为准
                return;
                $result=$adb->run_query_allrecords(" SELECT  *  FROM  vtiger_activationcode  WHERE  contractid=".$contractid."  AND  comeformtyun=1 LIMIT 1 ");
                //查询该合同是否存在T云订单
                if(empty($result)){
                    $paramers['contract_no']=$rp['contract_no'];
                    $paramers['marks']='T云系列合同但是T云WEB订单管理没生成订单,contractid'.$contractid;
                    //$Matchreceivements_Record_Model=Vtiger_Record_Model::getCleanInstance("Matchreceivements");
                    //$Matchreceivements_Record_Model->noCalculationAchievementRecord($paramers);
                    return ;
                }
                //$data=$BasicAjaxAction->tYunCalculationAchievement($receivepayid,$contractid,$shareuser,$total,$currentid,$adb,$matchdate);
                //$data=$BasicAjaxAction->tsiteCalculationAchievement($receivepayid,$contractid,$shareuser,$total,$currentid,$adb,$matchdate,$salesorderid);
            }
            //公式五 到账业绩 TSITE产品
        }else if(in_array($rp['productid'],$tsite)){
            // 做个过滤处理 如果是 搜索引擎类型google竞价合同  则不再生成业绩
            if($rp['servicecontractstype']=='续费'){
                $paramers['contract_no']=$rp['contract_no'];
                $paramers['marks']='Tsite续费合同类型的不生成业绩正常匹配判定 以防以前正常生成时生成了数据 在这里又更新';
                $Matchreceivements_Record_Model=Vtiger_Record_Model::getCleanInstance("Matchreceivements");
                $Matchreceivements_Record_Model->noCalculationAchievementRecord($paramers);
                return ;
            }
            //$data=$BasicAjaxAction->tsiteCalculationAchievement($rp,$contractid,$receivepayid,$shareuser,$total,$currentid,$remark,$adb,$matchdate,$salesorderid);
            //没有给的产品计算  计算公式都走
        }else{
            $data=$BasicAjaxAction->othersExtraCalculationAchievementsalesorder($rp,$contractid,$receivepayid,$shareuser,$total,$currentid,$remark,$adb,$matchdate,$salesorderid);
        }
        $insertValueStr=$data['insertValueStr'];
        $datavalue=$data['datavalue'];
        $defaultSQlField='owncompanys,receivedpaymentownid,scalling,servicecontractid,receivedpaymentsid,businessunit,matchdate,departmentid,owncompany,createtime,reality_date,paytitle,unit_price,unit_prices,department,groupname,departmentname,receivedpaymentown,servicecontractstype,accountname,signdate,contract_no,total,dividetotal,costing,purchasemount,worksheetcost,productlife,marketprice,dividemarketprice,costdeduction,dividecostdeduction,other,effectiverefund,arriveachievement,achievementmonth,modulestatus,productname,achievementtype,producttype,extracost,salong,waici,meijai,othercost,shareuser,remarks,generatedamount,adjustbeforearriveachievement,divideworksheetcost,dividecosting,dividepurchasemount,divideextracost,divideother,more_years_renew,renewal_commission,renewtimes,splitcontractamount,splitmarketprice,splitcost,commissionforrenewal';
        $field=!empty($data['field'])?$data['field']:$defaultSQlField;
        // 最后如果不为空则进行处理数据
        if(!empty($insertValueStr) && isset($type) && $type!=1){
            $insertValueStr = trim($insertValueStr,",");
            $addStastic_sql = "INSERT INTO vtiger_achievementallot_statistic (".$field.") VALUES ";
            //$deleteStastic_sql='DELETE  FROM  vtiger_achievementallot_statistic WHERE receivedpaymentsid = ?';
            //$adb->pquery($deleteStastic_sql,array($receivepayid));
            global $achievementMonth;
            $achievementMonth=$this->getAchievementMonth($rp['matchdate'],$salesorderid);
            $datavalue=array_map(function ($v){
                global $achievementMonth;
                $v[35]=$achievementMonth;
                return $v;
            },$datavalue);
            $adb->pquery($addStastic_sql.$insertValueStr,$datavalue);
            if($achievementMonth!=date('Y-m')){//如果不是同月重新生成提成
                $query='SELECT receivedpaymentownid FROM  vtiger_achievementallot_statistic WHERE receivedpaymentsid =?';
                $achievementResult=$adb->pquery($query,array($receivepayid));
                if($adb->num_rows($achievementResult)) {
                    while($row=$adb->fetch_array($achievementResult)) {
                        $achievementSummary_record_model = Vtiger_Record_Model::getCleanInstance('AchievementSummary');
                        $query='SELECT 1 FROM vtiger_usergraderoyalty WHERE userid=? AND staffrank=0';
                        $usergraderoyaltyresut=$adb->pquery($query,array($row['receivedpaymentownid']));
                        if($adb->num_rows($usergraderoyaltyresut)){//如果是商务
                            ob_start();//重新计算员工的提成
                            $achievementSummary_record_model->calulateEmployee(array('userid' => $row['receivedpaymentownid'], 'achievementmonth' => $achievementMonth));
                            $info = "\n" . $row['receivedpaymentownid'] . '员工的提成start' . $achievementMonth . "rebuild\n";
                            $info .= ob_get_contents();
                            $info .= "\n" . $row['receivedpaymentownid'] . '员工的提成end' .$achievementMonth . "rebuild\n";
                            $achievementSummary_record_model->comm_logs($info, 'rebuildachievementallot');
                            ob_end_clean();
                        }
                        ob_start();//重新计算经理的提成
                        $query = 'SELECT DISTINCT userid FROM vtiger_useractivemonthnew WHERE subordinateid=? AND activedate=? AND `status`=0';
                        $resultData = $adb->pquery($query, array($row['receivedpaymentownid'],$achievementMonth));
                        $userids = '';
                        while ($row = $adb->fetch_array($resultData)) {
                            $userids .= $row['userid'] . ',';
                        }
                        $userids = trim($userids, ',');
                        $achievementSummary_record_model->calulateManager(array('userids' => $userids, 'achievementmonth' => $achievementMonth));
                        $info = $userids . '员工上级的提成' . $result['achievementmonth'] . "rebuild\n";;
                        $info .= ob_get_contents();
                        $info .= "\n" . $userids . '员工上级的提成end' . $achievementMonth . "rebuild\n";
                        $achievementSummary_record_model->comm_logs($info, 'rebuildachievementallot');
                        ob_end_clean();
                        ob_start();
                        $achievementSummary_record_model->noAchievementRoyalty(array(
                            'calculation_year_month' => $achievementMonth,
                            'calculation_month' => end(explode('-', $achievementMonth)),
                            'calculation_year' => current(explode('-', $achievementMonth)),
                            'assigner' => $userids
                        ));
                        $info = $userids . '汇总的提成' . $achievementMonth . "rebuild\n";;
                        $info .= ob_get_contents();
                        $info .= "\n" . $userids . '员工上级的提成end' .$achievementMonth . "rebuild\n";
                        $achievementSummary_record_model->comm_logs($info, 'rebuildachievementallot');
                        ob_end_clean();
                        $achievementSummary_record_model->updateUserDepartment($achievementMonth);
                    }
                }
            }
            //修改排行榜
            $recordModel = Matchreceivements_Record_Model::getCleanInstance("Matchreceivements");
            $recordModel->matchToRanking($receivepayid);

        }

    }

    /**
     * @param $matchdate 回款匹配日期
     * @param $salesorderid 工单ID
     * 取业绩的业绩月份
     */
    public function getAchievementMonth($matchdate,$salesorderid){
        global $adb;
        $nowMonth = date('Ym');//当前时间也即工单关联回款的时间
        $matchdateDateMonth = date('Ym', strtotime($matchdate));//回款的匹配时间
        $AchievementMonth=date('Y-m');//业绩月份
        if($nowMonth!=$matchdateDateMonth){//回款匹配与工单匹配不同月
            if(date('j')<4){
                $sql="select createdtime from vtiger_crmentity where crmid=? and setype='SalesOrder' and createdtime<?";
                $result=$adb->pquery($sql,array($salesorderid,date('Y-m-03 00:00:00')));//2号创建
                if($adb->num_rows($result)>0){
                    $AchievementMonth= date('Y-m',strtotime('-1 month'));
                }
            }
        }
        return $AchievementMonth;
    }
    /**
     * 判断是否更换绩效月份
     * @param $reality_date
     * @param $salesorderid
     * @return bool
     */
    public function isNeedUpdateAchievementMonth($reality_date,$salesorderid){
        global $adb;
        $nowMonth = date('Ym',strtotime('-5 days'));
        $realityDateMonth = date('Ym', strtotime($reality_date));
        //匹配时间在3号前而且入账时间是上个月
        if(date('j')<4&&$nowMonth==$realityDateMonth){
            $sql="select createdtime from vtiger_crmentity where crmid=? and setype='SalesOrder' and createdtime<?";
            $result=$adb->pquery($sql,array($salesorderid,date('Y-m-03 00:00:00')));
            if($adb->num_rows($result)>0){
                return true;
            }
        }
        return false;
    }

}
