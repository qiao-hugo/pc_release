<?php

class ServiceContracts_Module_Model extends Vtiger_Module_Model{
    public $cancelOrderWorkFlowsid=2968114;
    public $cancelCrossMonthOrderWorkFlowsid=3071338;
    /**
     * 取消跨月订单工作流
     * @var int
     */
	 public function getSideBarLinks($linkParams) {
		$parentQuickLinks = array();
		$quickLink = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => '合同列表',
				'linkurl' => $this->getListViewUrl(),
				'linkicon' => '',
		);
         $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);
         if($this->exportGroup()) {
             $quickLink2 = array(
                 'linktype' => 'SIDEBARLINK',
                 'linklabel' => '导出数据',
                 'linkurl' => $this->getListViewUrl() . '&public=Export',
                 'linkicon' => '',
             );
             $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
         }
         if($this->exportGrouprt('ServiceContracts','ExportRI')){
             $quickLink2 = array(
                 'linktype' => 'SIDEBARLINK',
                 'linklabel' => '导出合同,回款数据',
                 'linkurl' => $this->getListViewUrl() . '&public=ExportRI',
                 'linkicon' => '',
             );
             $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
         }
        if($this->exportGrouprt('ServiceContracts','ExportRIV')){
             $quickLink2 = array(
                 'linktype' => 'SIDEBARLINK',
                 'linklabel' => '导出合同已发放清单',
                 'linkurl' => $this->getListViewUrl() . '&public=ExportRIV',
                 'linkicon' => '',
             );
             $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
        }

        if($this->exportGrouprt('ServiceContracts','ExportRIS')){
             $quickLink2 = array(
                 'linktype' => 'SIDEBARLINK',
                 'linklabel' => '导出工单人力成本',
                 'linkurl' => $this->getListViewUrl() . '&public=ExportRIS',
                 'linkicon' => '',
             );
             $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
        }

        if($this->exportGrouprt('ServiceContracts','ExportRINV')){
             $quickLink2 = array(
                 'linktype' => 'SIDEBARLINK',
                 'linklabel' => '发票,合同导出',
                 'linkurl' => $this->getListViewUrl() . '&public=ExportRINV',
                 'linkicon' => '',
             );
             $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
         }
	   if($this->exportGrouprt('ServiceContracts','ExportRM')){
             $quickLink2 = array(
                 'linktype' => 'SIDEBARLINK',
                 'linklabel' => '权限设置',
                 'linkurl' => $this->getListViewUrl() . '&public=ExportRM',
                 'linkicon' => '',
             );
             $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
         }

         if($this->exportGrouprt('ServiceContracts','Received')){
             $quickLink2 = array(
                 'linktype' => 'SIDEBARLINK',
                 'linklabel' => '合同领取',
                 'linkurl' => $this->getListViewUrl() . '&public=Received',
                 'linkicon' => '',
             );
             $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
         }
         // cxh 2020-05-09 start
         if($this->exportGrouprt('ServiceContracts','Received')){
             $quickLink2 = array(
                 'linktype' => 'SIDEBARLINK',
                 'linklabel' => '合同审查',
                 'linkurl' => $this->getListViewUrl() . '&public=contractCheck',
                 'linkicon' => '',
             );
             $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
         }
         // cxh 2020-05-09 end
         if($this->exportGrouprt('ServiceContracts','Received')){
             $quickLink2 = array(
                 'linktype' => 'SIDEBARLINK',
                 'linklabel' => '合同归还',
                 'linkurl' => $this->getListViewUrl() . '&public=Returned',
                 'linkicon' => '',
             );
             $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
         }
         if($this->exportGrouprt('ServiceContracts','Received')){
             $quickLink2 = array(
                 'linktype' => 'SIDEBARLINK',
                 'linklabel' => '合同未签归还',
                 'linkurl' => $this->getListViewUrl() . '&public=NoSignReturned',
                 'linkicon' => '',
             );
             $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
         }
         if($this->exportGrouprt('SalesOrder','OrderCancelExport')){
             $quickLink2 = array(
                 'linktype' => 'SIDEBARLINK',
                 'linklabel' => '作废工单导出',
                 'linkurl' => $this->getListViewUrl() . '&public=OrderCancelExport',
                 'linkicon' => '',
             );
             $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
         }
         if($this->exportGrouprt('ServiceContracts','AuditSettings')){
             $quickLink2 = array(
                     'linktype' => 'SIDEBARLINK',
                     'linklabel' => '合同延期审核设置',
                     'linkurl' => $this->getListViewUrl() . '&public=AuditSettings',
                     'linkicon' => '',
             );
             $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
         }
         if($this->exportGrouprt('ServiceContracts','Changelead'))
         {
             $quickLink2 = array(
                 'linktype' => 'SIDEBARLINK',
                 'linklabel' => '领用人变更',
                 'linkurl' => $this->getListViewUrl() . '&public=Changelead',
                 'linkicon' => '',
             );
             $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
         }
         if($this->exportGrouprt('ServiceContracts','ExportComplete')){
             $quickLink2 = array(
                 'linktype' => 'SIDEBARLINK',
                 'linklabel' => '导出已签合同',
                 'linkurl' => $this->getListViewUrl() . '&public=ExportComplete',
                 'linkicon' => '',
             );
             $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
         }

         if($this->exportGrouprt('ServiceContracts','dempartConfirm'))
         {
             $quickLink2 = array(
                 'linktype' => 'SIDEBARLINK',
                 'linklabel' => '非标合同设置',
                 'linkurl' => $this->getListViewUrl() . '&public=dempartConfirm',
                 'linkicon' => '',
             );
             $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
         }
         if($this->exportGrouprt('ServiceContracts','protected'))
         {
             $quickLink2 = array(
                 'linktype' => 'SIDEBARLINK',
                 'linklabel' => '合同超领设置',
                 'linkurl' => $this->getListViewUrl() . '&public=protected',
                 'linkicon' => '',
             );
             $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
         }

         if($this->exportGrouprt('ServiceContracts','ProductsCodeProductId'))
         {
             $quickLink2 = array(
                 'linktype' => 'SIDEBARLINK',
                 'linklabel' => '设置产品合同编码对应',
                 'linkurl' => $this->getListViewUrl() . '&public=setproduct2code',
                 'linkicon' => '',
             );
             $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
	     	     $quickLink2 = array(
                 'linktype' => 'SIDEBARLINK',
                 'linklabel' => '同步产品编码',
                 'linkurl' => $this->getListViewUrl() . '&public=setproduct2codenotyun',
                 'linkicon' => '',
             );
             $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
         }

         if($this->exportGrouprt('ServiceContracts','ExportCancel')){
             $quickLink2 = array(
                 'linktype' => 'SIDEBARLINK',
                 'linklabel' => '作废合同导出',
                 'linkurl' => $this->getListViewUrl() . '&public=ExportCancel',
                 'linkicon' => '',
             );
             $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
         }
	 if($this->exportGrouprt('ServiceContracts','isfulldelivery'))
         {
             $quickLink2 = array(
                 'linktype' => 'SIDEBARLINK',
                 'linklabel' => '产品交付导出',
                 'linkurl' => $this->getListViewUrl() . '&public=isfulldelivery',
                 'linkicon' => '',
             );
             $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
         }
         $quickLink2 = array(
             'linktype' => 'SIDEBARLINK',
             'linklabel' => '延期签收合同表(SaaS)',
             'linkurl' => 'index.php?module=ContractDelaySign&view=List',
             'linkicon' => '',
         );
         $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);

         $quickLink2 = array(
             'linktype' => 'SIDEBARLINK',
             'linklabel' => '延期签收合同表(非SaaS)',
             'linkurl' => 'index.php?module=ContractDelaySign&view=List&report=notyun',
             'linkicon' => '',
         );
         $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);

         $quickLink2 = array(
             'linktype' => 'SIDEBARLINK',
             'linklabel' => '待签收合同记录',
             'linkurl' => $this->getListViewUrl() . '&public=NoComplete',
             'linkicon' => '',
         );
         $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);


		return $parentQuickLinks;
	}

    /**
     * 可导出数据的权限
     * @return bool
     */
    public function exportGroup(){
		global $current_user;
        $id=$current_user->id;
        $userids=array(1,2155,323,1923);//有访问权限的
        if(in_array($id,$userids)){
            return true;
        }
        return false;
    }
    /**
     * 可导出数据的权限
     * @return bool
     */
    public function exportGroupri(){
        global $current_user;
        $id=$current_user->id;
        $db=PearDatabase::getInstance();
        //不必过滤是否在职因为离职的根本就登陆不了系统
        $query="select vtiger_user2department.userid from vtiger_user2department LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid WHERE CONCAT(vtiger_departments.parentdepartment,'::') REGEXP 'H25::'";
        $result=$db->run_query_allrecords($query);
        $userids=array();
        foreach($result as $values){
            $userids[]=$values['userid'];
        }
        $userids[]=1;
        //$userids=array(1,2155,323,1923);//有访问权限的
        if(in_array($id,$userids)){
            return true;
        }
        return false;
    }
    public function exportGrouprt($module,$classname,$id=0){
        if($id==0)
        {
            global $current_user;
            $id = $current_user->id;
        }
        $db=PearDatabase::getInstance();
        $query="SELECT 1 FROM vtiger_exportmanage WHERE deleted=0 AND userid=? AND module=? AND classname=?";
        $result=$db->pquery($query,array($id,$module,$classname));
        $num=$db->num_rows($result);
        if($num){
            return true;
        }
        return false;
    }



    /**移动端 充值申请单 搜索服务合同
     * @param $searchKey
     * @param bool $module
     * @return array
     * @throws AppException
     * @throws Exception
     */
    public function  getSearchResultApp($searchKey, $module=false){
        $searchKey=trim($searchKey);
        $db = PearDatabase::getInstance();
        $query = "SELECT vtiger_servicecontracts.signdate,vtiger_servicecontracts.effectivetime,IF(vtiger_servicecontracts.modulestatus='c_complete','alreadySigned','notSigned') AS iscontracted,vtiger_servicecontracts.servicecontractsid, vtiger_servicecontracts.contract_no, IF(vtiger_servicecontracts.agentid>0,a.accountname, vtiger_account.accountname) as accountname,  IF(vtiger_servicecontracts.agentid>0,a.accountid,vtiger_account.accountid) as accountid, vtiger_account.customertype,vtiger_servicecontracts.total FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_servicecontracts.servicecontractsid = vtiger_crmentity.crmid  LEFT JOIN vtiger_account ON vtiger_servicecontracts.sc_related_to = vtiger_account.accountid  LEFT JOIN vtiger_account a ON a.accountid = vtiger_servicecontracts.agentaccountid WHERE ( vtiger_servicecontracts.contract_no LIKE '%$searchKey%' OR vtiger_account.accountname LIKE '%$searchKey%' )";

        //$where = getAccessibleUsers();
        global $current_user;
        $userid = array($current_user->id);
        $where=getAccessibleUsers('ServiceContracts','List',true);

        if(!empty($where)&&$where!='1=1'){
            $where=array_intersect($where,$userid);
            if(empty($where)){
                $where= $userid;
            }
            $where=implode(',',$where);
        /*}else{
            $where=$userid;
        }
        $where=implode(',',$where);
        if($where!='1=1'){*/
            //$query .= ' and vtiger_servicecontracts.receiveid '.$where;
            $query .= ' and (vtiger_crmentity.smownerid in ('.$where.') or vtiger_servicecontracts.receiveid in ('.$where.') or EXISTS(SELECT 1 FROM vtiger_crmentity AS crmtable WHERE crmtable.crmid=vtiger_servicecontracts.sc_related_to AND crmtable.smownerid IN ('.$where.')) OR EXISTS(SELECT 1 FROM vtiger_shareaccount WHERE vtiger_shareaccount.userid in('.$where.') and vtiger_shareaccount.sharestatus=1 AND vtiger_shareaccount.accountid=vtiger_servicecontracts.sc_related_to))';
        }
        //追加合同状态条件 gaocl add 2018/05/11
        //$query .= " AND (vtiger_servicecontracts.modulestatus in('c_complete') OR vtiger_servicecontracts.isguarantee=1) AND vtiger_servicecontracts.effectivetime>=CURRENT_DATE()";
        $dateTime=date('Y-m-d');
        $query .= " AND (vtiger_servicecontracts.modulestatus='c_complete' OR (vtiger_servicecontracts.modulestatus NOT in('c_cancel','c_canceling','c_cancelings') AND (vtiger_servicecontracts.isguarantee=1  OR vtiger_servicecontracts.isguarantee=1 )))";
//        $query .= " AND ((vtiger_servicecontracts.modulestatus='c_complete' AND vtiger_servicecontracts.effectivetime>='{$dateTime}') OR (vtiger_servicecontracts.modulestatus NOT in('c_cancel','c_canceling','c_cancelings') AND ((vtiger_servicecontracts.isguarantee=1 AND vtiger_servicecontracts.effectivetime>='{$dateTime}') OR (vtiger_servicecontracts.isguarantee=1 AND (vtiger_servicecontracts.effectivetime IS NULL OR vtiger_servicecontracts.effectivetime='')))))";

//        return array($query);
        $result = $db->pquery($query, array());
        $noOfRows = $db->num_rows($result);

        $data = array();
        for($i=0; $i<$noOfRows; ++$i) {
            $row = $db->query_result_rowdata($result, $i);
            $data[] = $row;
            if ($i >= 50) {
                break;
            }
        }
        return $data;
    }

    public function doOrderCancelNew($userID,$contract_no){
        global $tyunweburl,$sault;
        $url=$tyunweburl.'api/micro/order-basic/v1.0.0/api/Order/CancelOrderByContractCode';
        $time=time().'123';
        $sault1=$time.$sault;
        $token=md5($sault1);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));
        $canceldata=json_encode(array("userID"=>intval($userID),"contractCode"=>$contract_no));
        $this->_logs(array('doOrderCancelNew:',$canceldata));
        $data=$this->https_request($url,$canceldata,$curlset);
        $this->_logs(array('doOrderCancelNew return data:',$url,$data));
        $jsonData=json_decode($data,true);
        if($jsonData['code']!='200'){
            return false;
        }
        return true;
    }

    public function doCancelNew($contract_no){
        global $adb;
        $query="SELECT * FROM vtiger_activationcode WHERE contractname=? AND `status`!=2";
        $type_result=$adb->pquery($query,array($contract_no));
        if($adb->num_rows($type_result)){
            $max_activationcodeid = 0;
            while ($row = $adb->fetch_row($type_result)){
                $type_result_datas[] = $row;
                $comeformtyun = $row['comeformtyun'];
                $user_id = $row['usercodeid'];
                $usercode = $row['usercode'];
                $contractid = $row['contractid'];
                $max_activationcodeid = max($row['activationcodeid'],$max_activationcodeid);
            }
            if($comeformtyun == 1){
                $query='SELECT 1 FROM vtiger_activationcode WHERE activationcodeid>? AND contractid != ? AND usercodeid=? AND `status`!=2';
                $result=$adb->pquery($query,array($max_activationcodeid,$contractid,$user_id));
                if($adb->num_rows($result)){
                    return array("success"=>false,'message'=>"请先将该账号对应的续费,或升降级合同作废掉再进行操作");
                }
                $recordModel=Vtiger_Record_Model::getCleanInstance('TyunWebBuyService');
                $Repson=$recordModel->doOrderCancelByContractNo($contract_no,$user_id,$usercode);
                $jsonData=json_decode($Repson,true);
                if($jsonData['code']=='200'){
                    $sql="UPDATE  vtiger_activationcode SET `status`=2,orderstatus='ordercancel',canceldatetime=? WHERE contractname=?";
                    $adb->pquery($sql,array(date('Y-m-d H:i:s'),$contract_no));
                    return array("success"=>true,'message'=>"合同相关的订单取消成功");
                }
                return array("success"=>false,'message'=>"合同相关的订单取消失败");
            }

        }else{
            return array("success"=>true,'message'=>"合同激活码或订单取消成功！");
        }

        $sql = "SELECT 
                IFNULL(P.activecode,M.activecode) AS activecode,
                M.usercode AS usercode,
                M.classtype AS classtype,
                M.contractname AS contractno,
				M.receivetime,
                IFNULL(P.customername,M.customername) AS customername,
                M.agents,
                (SELECT MAX(str_to_date(REPLACE(MM.expiredate,'/','-'),'%Y-%m-%d')) FROM vtiger_activationcode MM WHERE MM.status IN(0,1) AND MM.usercode=M.usercode) AS expiredate 
                FROM vtiger_activationcode M
                LEFT JOIN vtiger_activationcode P ON(M.buyid=P.activationcodeid) WHERE M.status IN(0,1) AND M.contractname=?";
        $sel_result = $adb->pquery($sql, array($contract_no));
        $res_cnt = $adb->num_rows($sel_result);

        if($res_cnt > 0) {
            $row = $adb->query_result_rowdata($sel_result, 0);
            $classtype = $row['classtype'];
            $status = $row['status'];
            $usercode = $row['usercode'];
            $receivetime = $row['receivetime'];
            $query="SELECT 1 FROM vtiger_activationcode WHERE usercode=? AND `status` IN(0,1) AND receivetime>?";
            $query_result=$adb->pquery($query,array($usercode,$receivetime));
            if($classtype!= 'buy' && $adb->num_rows($query_result)>0){
                return array("success"=>false,'message'=>"作废失败：合同存在续费或升级合同!");
            }
            if($classtype == 'buy' && $status == '1'){
                return array("success"=>false,'message'=>"作废失败：请先取消激活");
            }
            return $this->invalidContract($row);
        }
        return array("success"=>true,'message'=>"");
    }


    ////****合同作废save搬过来的*****////
    public function doCancel($contract_no)
    {
       global $adb;
       $query="SELECT * FROM vtiger_activationcode WHERE contractname=? AND `status`!=2 LIMIT 1";
       $type_result=$adb->pquery($query,array($contract_no));
       if($adb->num_rows($type_result)){
           $type_result_data=$adb->raw_query_result_rowdata($type_result,0);
           if($type_result_data['comeformtyun']==1){
               $recordModel=Vtiger_Record_Model::getCleanInstance('TyunWebBuyService');
               $Repson=$recordModel->doOrderCancel($type_result_data);
               $jsonData=json_decode($Repson,true);
               if($jsonData['code']=='200'){
                   $sql="UPDATE  vtiger_activationcode SET `status`=2,orderstatus='ordercancel',canceldatetime=? WHERE activationcodeid=?";
                   $adb->pquery($sql,array(date('Y-m-d H:i:s'),$type_result_data['activationcodeid']));
                   return array("success"=>true,'message'=>"合同相关的订单取消成功");
               }else{
                   return array("success"=>false,'message'=>"合同相关的订单取消失败");
               }
           }

       }else{
           return array("success"=>true,'message'=>"合同激活码或订单取消成功！");
       }
       $sql = "SELECT 
                IFNULL(P.activecode,M.activecode) AS activecode,
                M.usercode AS usercode,
                M.classtype AS classtype,
                M.contractname AS contractno,
				M.receivetime,
                IFNULL(P.customername,M.customername) AS customername,
                M.agents,
                (SELECT MAX(str_to_date(REPLACE(MM.expiredate,'/','-'),'%Y-%m-%d')) FROM vtiger_activationcode MM WHERE MM.status IN(0,1) AND MM.usercode=M.usercode) AS expiredate 
                FROM vtiger_activationcode M
                LEFT JOIN vtiger_activationcode P ON(M.buyid=P.activationcodeid) WHERE M.status IN(0,1) AND M.contractname=?";
        $sel_result = $adb->pquery($sql, array($contract_no));
        $res_cnt = $adb->num_rows($sel_result);

        if($res_cnt > 0) {
            $row = $adb->query_result_rowdata($sel_result, 0);
            $classtype = $row['classtype'];
            $status = $row['status'];
            $usercode = $row['usercode'];
            $receivetime = $row['receivetime'];
			$query="SELECT 1 FROM vtiger_activationcode WHERE usercode=? AND `status` IN(0,1) AND receivetime>?";
			$query_result=$adb->pquery($query,array($usercode,$receivetime));
			if($classtype!= 'buy' && $adb->num_rows($query_result)>0){
				return array("success"=>false,'message'=>"作废失败：合同存在续费或升级合同!");
			}
            if($classtype == 'buy' && $status == '1'){
                return array("success"=>false,'message'=>"作废失败：请先取消激活");
            }
            return $this->invalidContract($row);
        }
        return array("success"=>true,'message'=>"");
    }
    /**
     * 合同作废
     */
    public function invalidContract($row){
        $activecode = $row['activecode'];
        $usercode = $row['usercode'];
        $classtype = $row['classtype'];
        $contractno = $row['contractno'];
        $customername = $row['customername'];
        $agents = $row['agents'];
        $expiredate = $row['expiredate'];

        //线上地址
        //$url = "http://tyunapi.71360.com/api/CRM/";
        //预发布地址
        $url = "http://apityun.71360.com/api/CRM/";

        if($classtype == 'buy'){
            $url.='CancelSecretkey';
            $myData['OldContractCode'] = $contractno;//合同编号
            $myData['OldCompanyName'] = $customername;//原客户名称
            $myData['AgentIdentity'] = $agents;//代理商标识码
        }else if($classtype == 'upgrade'){
            $url.='CancelSecretkeyUpgrade';
            $myData['OldLoginName'] = $usercode;//原登录名
            $myData['OldSecretKeyID'] = $activecode;//原激活码
            $myData['OldContractCode'] = $contractno;//合同编号
            $myData['OldCloseDate'] = $expiredate;//到期时间
        }else if($classtype == 'renew'){
            $url.='CancelSecretkeyRenewal';
            $myData['OldLoginName'] = $usercode;//原登录名
            $myData['OldSecretKeyID'] = $activecode;//原激活码
            $myData['OldContractCode'] = $contractno;//合同编号
            $myData['OldCloseDate'] = $expiredate;//到期时间
        }else if($classtype == 'againbuy'){
            $url.='CancelSecretkeyBuyService';
            $myData['OldLoginName'] = $usercode;//原登录名
            $myData['OldSecretKeyID'] = $activecode;//原激活码
            $myData['OldContractCode'] = $contractno;//合同编号
            $myData['OldCloseDate'] = $expiredate;//到期时间
        }else if($classtype == 'degrade'){
            $url.='CancelSecretkeyDegrade';
            $myData['OldLoginName'] = $usercode;//原登录名
            $myData['OldSecretKeyID'] = $activecode;//原激活码
            $myData['OldContractCode'] = $contractno;//合同编号
            $myData['OldCloseDate'] = $expiredate;//到期时间
        }
        $this->_logs(array("T云作废接口地址：", $url));
        $this->_logs(array("data加密前数据：", $myData));
        $tempData['data'] = $this->encrypt(json_encode($myData));
        $this->_logs(array("data加密后数据：", $tempData['data']));
        $postData = http_build_query($tempData);//传参数
        $res = $this->https_request($url, $postData);
        $this->_logs(array("T云作废接口返回JSON：", $res));
        $result = json_decode($res, true);
        $result = json_decode($result, true);
        return $result;
        /* if($result['success']){
            echo json_encode(array('success'=>1, 'msg'=>$result['message']));
        }else{
            echo json_encode(array('success'=>0, 'msg'=>$result['message']));
        }
        exit(); */
    }

    /**
     * 激活信息更新
     */
    public function updateSecretInfo($contractNo, $customerName, $productLife, $productId){
        $myData['ContractCode'] = $contractNo;//合同编号
        $myData['CompanyName'] = $customerName;//客户名称
        $myData['ProductLife'] = $productLife;//年限
        $myData['ProductID'] = $productId;//产品编号
        $url = "http://tyunapi.71360.com/api/cms/UpdateSecretKey";

        $tempData['data'] = $this->encrypt(json_encode($myData));

        $postData = http_build_query($tempData);//传参数
        $res = $this->https_request($url, $postData);
        $result = json_decode($res, true);
        $result = json_decode($result, true);
        return $result;
        /* if($result['success']){
            echo json_encode(array('success'=>1, 'msg'=>$result['message']));
        }else{
            echo json_encode(array('success'=>0, 'msg'=>$result['message']));
        }
        exit(); */
    }

    /**
     * des加密
     * @param unknown $encrypt 原文
     * @return string
     */
    public function encrypt($encrypt, $key='sdfesdcf') {
        $mcrypt = MCRYPT_TRIPLEDES;
        $iv = mcrypt_create_iv(mcrypt_get_iv_size($mcrypt, MCRYPT_MODE_ECB), MCRYPT_RAND);
        $passcrypt = mcrypt_encrypt($mcrypt, $key, $encrypt, MCRYPT_MODE_ECB, $iv);
        $encode = base64_encode($passcrypt);
        return $encode;
    }

    /**
     * des解密
     * @param unknown $decrypt
     * @return string
     */
    /* public function decrypt($decrypt, $key='sdfesdcf'){
        $decoded = str_replace(' ','%20',$decrypt);
        $decoded = base64_decode($decrypt);
        $mcrypt = MCRYPT_TRIPLEDES;
        $iv = mcrypt_create_iv(mcrypt_get_iv_size($mcrypt, MCRYPT_MODE_ECB), MCRYPT_RAND);
        $decrypted = mcrypt_decrypt($mcrypt, $key, $decoded, MCRYPT_MODE_ECB, $iv);
        return $decrypted;
    } */

    public function https_request($url, $data = null){
        $curl = curl_init();
        //curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type:application/x-www-form-urlencoded"));
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);//post提交方式
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
    ////****合同作废save搬过来的*****////
    /**
     * 补充协议
     * @param Vtiger_Request $request
     * @return bool
     */
    public function checkAccount(Vtiger_Request $request)
    {
        global $adb;
        $account_id=$request->get('sc_related_to');
        $recordid=$request->get('record');
        $query='SELECT vtiger_contractsagreement.accountid FROM vtiger_contractsagreement LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_contractsagreement.contractsagreementid WHERE vtiger_crmentity.deleted=0
                AND vtiger_contractsagreement.servicecontractsid=? ';
        $result=$adb->pquery($query,array($recordid));
        if($adb->num_rows($result))
        {
            while($row=$adb->fetch_array($result))
            {
                if($row['accountid']!=$account_id)
                {
                    return true;
                }
            }

            return false;
        }
        return false;
    }
    /**
     * 用户
     * @param $str
     * @return array
     */
    public  function getUserInfo(){
        $db=PearDatabase::getInstance();
        $query="SELECT
	a.id,
  CONCAT(a.last_name,'[',c.departmentname,']') AS last_name
FROM
	vtiger_users as a
LEFT JOIN vtiger_user2department as b ON a.id = b.userid
LEFT JOIN vtiger_departments as c ON c.departmentid = b.departmentid
WHERE a.status='Active' AND a.id>1
ORDER BY
	a.id ASC";
        return $db->run_query_allrecords($query);
    }

    /**
     *
     */
    public function getProtectData(){
        $db=PearDatabase::getInstance();
        $query ="SELECT
	b.id,
  CONCAT(b.last_name,'[',d.departmentname,']') AS last_name,
	a.cnumber
FROM
	vtiger_contractexceedingnumber as a
LEFT JOIN vtiger_users as b ON b.id = a.userid
LEFT JOIN vtiger_user2department as c ON b.id = c.userid
LEFT JOIN vtiger_departments as d ON d.departmentid = c.departmentid
ORDER BY
	a.userid ASC";
        return $db->run_query_allrecords($query);
    }
    /**
     * 写日志，用于测试,可以开启关闭
     * @param data mixed
     */
    public function _logs($data, $file = 'logs_'){
        $year	= date("Y");
        $month	= date("m");
        $dir	= './logs/tyun/' . $year . '/' . $month . '/';
        if(!is_dir($dir)) {
            mkdir($dir,0755,true);
        }
        $file = $dir . $file . date('Y-m-d').'.txt';
        @file_put_contents($file, '----------------' . date('H:i:s') . '--------------------'.PHP_EOL.var_export($data,true).PHP_EOL, FILE_APPEND);
    }

    public function cancelActivationCode($contract_no){
        global $adb,$current_user;
        $query="SELECT * FROM vtiger_activationcode WHERE contractname=? AND `status`!=2";
        $type_result=$adb->pquery($query,array($contract_no));
        if($adb->num_rows($type_result)){
            $max_activationcodeid = 0;
            while ($row = $adb->fetch_row($type_result)){
                $type_result_datas[] = $row;
                $comeformtyun = $row['comeformtyun'];
                $user_id = $row['usercodeid'];
                $usercode = $row['usercode'];
                $contractid = $row['contractid'];
                $max_activationcodeid = max($row['activationcodeid'],$max_activationcodeid);
                $createdtime =$row['createdtime'];
                $activedate = $row['activedate'];
                $startdate = $row['startdate'];
                $couponcode=$row['couponcode'];
            }
            if($comeformtyun == 1){
                $query='SELECT 1 FROM vtiger_activationcode WHERE activationcodeid>? AND contractid != ? AND usercodeid=? AND `status`!=2';
                $result=$adb->pquery($query,array($max_activationcodeid,$contractid,$user_id));
                if($adb->num_rows($result)){
                    return array("success"=>false,'message'=>"请先将该账号对应的续费,或升降级合同作废掉再进行操作");
                }
                if(!$startdate || $startdate=='0000-00-00 00:00:00'){
                    $recordModel=Vtiger_Record_Model::getCleanInstance('TyunWebBuyService');
                    $Repson=$recordModel->doOrderCancelByContractNo($contract_no,$user_id,$usercode);
                    $jsonData=json_decode($Repson,true);
                    if($jsonData['code']=='200'){
                        $sql="UPDATE  vtiger_activationcode SET `status`=2,orderstatus='ordercancel',canceldatetime=? WHERE contractname=?";
                        $adb->pquery($sql,array(date('Y-m-d H:i:s'),$contract_no));
                        $this->clearCancelOrderRelations($contractid,$couponcode);
                        return array("success"=>true,'message'=>"合同相关的订单取消成功");
                    }
                    return array("success"=>false,'message'=>"合同相关的订单取消失败");
                }


                $result = $adb->pquery("select 1 from vtiger_salesorderworkflowstages where salesorderid=? and  isaction < 2 ",array($contractid));
                if($adb->num_rows($result)){
                    return array("success"=>false,'message'=>"该合同存在未完结的工作流");
                }

                $currentMonth = date("Y-m");
                $createMonth = date("Y-m",strtotime($createdtime));
                if(strtotime($currentMonth)>strtotime($createMonth)){
                    $workflowsid=$this->cancelCrossMonthOrderWorkFlowsid;
                    $messgage='请走线下邮件申请，最终审批通过之后，由对应审批人线上审批通过之后，方可作废成功';
                }else{
                    $workflowsid=$this->cancelOrderWorkFlowsid;
                    $messgage='工作流已提交,审核通过后自动取消订单';
                }

                $this->makeCancelOrderWorkFlows($contractid,$workflowsid,$current_user->id);
                return array("success"=>true,'code'=>1,'message'=>$messgage);
            }

        }else{
            return array("success"=>false,'message'=>"未查询到的该合同下有可用的订单！");
        }

        $sql = "SELECT 
                IFNULL(P.activecode,M.activecode) AS activecode,
                M.usercode AS usercode,
                M.classtype AS classtype,
                M.contractname AS contractno,
				M.receivetime,
                IFNULL(P.customername,M.customername) AS customername,
                M.agents,
                (SELECT MAX(str_to_date(REPLACE(MM.expiredate,'/','-'),'%Y-%m-%d')) FROM vtiger_activationcode MM WHERE MM.status IN(0,1) AND MM.usercode=M.usercode) AS expiredate 
                FROM vtiger_activationcode M
                LEFT JOIN vtiger_activationcode P ON(M.buyid=P.activationcodeid) WHERE M.status IN(0,1) AND M.contractname=?";
        $sel_result = $adb->pquery($sql, array($contract_no));
        $res_cnt = $adb->num_rows($sel_result);

        if($res_cnt > 0) {
            $row = $adb->query_result_rowdata($sel_result, 0);
            $classtype = $row['classtype'];
            $status = $row['status'];
            $usercode = $row['usercode'];
            $receivetime = $row['receivetime'];
            $query="SELECT 1 FROM vtiger_activationcode WHERE usercode=? AND `status` IN(0,1) AND receivetime>?";
            $query_result=$adb->pquery($query,array($usercode,$receivetime));
            if($classtype!= 'buy' && $adb->num_rows($query_result)>0){
                return array("success"=>false,'message'=>"作废失败：合同存在续费或升级合同!");
            }
            if($classtype == 'buy' && $status == '1'){
                return array("success"=>false,'message'=>"作废失败：请先取消激活");
            }
            return $this->invalidContract($row);
        }
        return array("success"=>true,'message'=>"");
    }

    public function clearCancelOrderRelations($recordId,$couponcode=''){
        $db = PearDatabase::getInstance();
        $result = $db->pquery("select modulestatus,sc_related_to from vtiger_servicecontracts where servicecontractsid=?",array($recordId));
        if(!$db->num_rows($result)){
            return;
        }
        $row = $db->fetchByAssoc($result,0);
        if($row['modulestatus']=='已发放'){
            $db->pquery("update vtiger_servicecontracts set  servicecontractstype='',signid='',receiveid='',total='',contract_type='',bussinesstype='',parent_contracttypeid='',isstage=0,sc_related_to='',old_sc_related_to=? where servicecontractsid=?",
                array($row['sc_related_to'],$recordId));
            $db->pquery("delete from vtiger_salesorderproductsrel where servicecontractsid=?",array($recordId));
            $db->pquery("DELETE FROM `vtiger_servicecontracts_divide` WHERE servicecontractid =?", array($recordId));
        }
        if($couponcode){
            $result2 = $db->pquery("select * from vtiger_coupontocontract where couponcode=? ",array($couponcode));
            if(!$db->num_rows($result2)){
                $this->_logs(array('clearCancelOrderRelations','没有找到券码对应的数据'));
                return;
            }
            $row=$db->fetchByAssoc($result2,0);
            $contractids=explode(",",$row['contractids']);
            $consumetimes=$row['consumetimes'];
            if(!in_array($recordId,$contractids)){
                $this->_logs(array('clearCancelOrderRelations','不在已使用券码的合同id中','contractid'=>$recordId,'contractIds'=>$contractids));
                return;
            }
            foreach ($contractids as $contractid){
                if($contractid==$recordId){
                    $this->_logs(array('clearCancelOrderRelations','跳过已存在的合同id','contractid'=>$recordId,'contractIds'=>$contractids));
                    continue;
                }
                $ids[]=$contractid;
            }
            $db->pquery("update vtiger_coupontocontract set consumetimes=?,contractids='".implode(",",$ids)."' where coupontocontractid=?",array(($consumetimes-1),$row['coupontocontractid']));
            $this->_logs(array('clearCancelOrderRelations','修改vtiger_coupontocontract','consumetimes'=>($consumetimes-1),'contractids'=>implode(",",$ids)));

            $result3 = $db->pquery("select contractid from vtiger_activationcode where status in(0,1) and contractid not in (".implode(",",$contractids).") and couponcode=? group by contractid",array($recordId,$couponcode));
            if(!$db->num_rows($result3)){
                return;
            }
            while ($row3=$db->fetchByAssoc($result3)){
                $ids[]=$row3['contractid'];
                $receivedContractIds[]=$row3['contractid'];
            }
            $this->_logs(array('clearCancelOrderRelations','可使用券码的合同id','contractids'=>$receivedContractIds));
            if(!count($receivedContractIds)){
                return;
            }

            $result4 = $db->pquery("select relatetoid from vtiger_receivedpayments where relatetoid in(".implode(",",$receivedContractIds).") and ismatchdepart=1 and receivedstatus='normal' order by matchdate asc",array());
            if(!$db->num_rows($result4)){
                return;
            }
            while ($row4=$db->fetchByAssoc($result4)){
                $contractRecordModel = ServiceContracts_Record_Model::getInstanceById($row4['relatetoid'],"ServiceContracts");
                $result5=$db->pquery("select sum(unit_price) as total from vtiger_receivedpayments where relatetoid=? and deleted=0 and ismatchdepart=1 and receivedstatus='normal'",array($row4['relatetoid']));
                if($db->num_rows($result5)){
                    $row5=$db->fetchByAssoc($result5,0);
                    $this->_logs(array('clearCancelOrderRelations','已回款金额与合同金额','receivedpaymentTotal'=>$row5['total'],'contractotal'=>$contractRecordModel->get("total")));
                    if($row5['total']>=$contractRecordModel->get("total")){
                        continue;
                    }
                }
                $returndata=$contractRecordModel->payAfterMatch($row4['relatetoid'],0);
                $this->_logs(array('clearCancelOrderRelations','确认支付结果','contractid'=>$row4['relatetoid'],'returndata'=>$returndata));
                if($returndata['success']){
                    return;
                }
            }
        }
    }

    public function makeCancelOrderWorkFlows($recordId,$workflowsid,$userId=0){
        global $current_user;
        if($userId){
            $user = new Users();
            $current_user = $user->retrieveCurrentUserInfoFromFile($userId);
        }
        $recordModel=ServiceContracts_Record_Model::getInstanceById($recordId,'ServiceContracts');
        $entity = $recordModel->entity->column_fields;
        $db = $recordModel->entity->db;
//        $workflowsid=$this->cancelOrderWorkFlowsid;//线上的
        $_REQUEST['workflowsid']=$workflowsid;

        //删掉以前的取消订单的工作流详情
        $db->pquery("delete from vtiger_salesorderworkflowstages where salesorderid=? and modulename=? and workflowsid=?",array($recordId,'ServiceContracts',$workflowsid));

        $focus=CRMEntity::getInstance('ServiceContracts');
        $focus->makeWorkflows('ServiceContracts',$_REQUEST['workflowsid'],$recordId);
        $query="UPDATE vtiger_salesorderworkflowstages,vtiger_servicecontracts set vtiger_salesorderworkflowstages.modulestatus='p_process' WHERE vtiger_servicecontracts.servicecontractsid=vtiger_salesorderworkflowstages.salesorderid AND vtiger_salesorderworkflowstages.salesorderid=?";
        $focus->db->pquery($query,array($recordId));
        $departmentid=$_SESSION['userdepartmentid'];
        $focus->setAudituid('ContractsAuditset',$departmentid,$recordId,$workflowsid);
        //新建时 消息提醒第一审核人进行审核
        $object = new SalesorderWorkflowStages_SaveAjax_Action();
        $object->sendWxRemind(array('salesorderid'=>$recordId,'salesorderworkflowstagesid'=>0));
        $sql = "select workflowstagesname from vtiger_workflowstages where workflowsid=? order by sequence LIMIT 1";
        $sel_result=$focus->db->pquery($sql, array($workflowsid));
        $res_cnt=$db->num_rows($sel_result);
        $workflowsnode='';
        if ($res_cnt > 0) {
            $row = $db->query_result_rowdata($sel_result, 0);
            $workflowsnode = $row['workflowstagesname'];
        }
        $focus->db->pquery("UPDATE `vtiger_servicecontracts` SET modulestatus='b_check',workflowsnode=?,backstatus=? WHERE servicecontractsid=?", array( $workflowsnode, $entity['modulestatus'],$recordId));

    }
    /**
     * 确认产品交付导出
     * @param $request
     */
    public function isFulldeliveryData($request){
        global $adb;
        $date=$request->get('datatime');
        $query="SELECT contract_no,fulldeliverytime,vtiger_account.accountname,(SELECT last_name FROM vtiger_users WHERE id=fulldeliveryid) AS username,fulldeliveryid FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_servicecontracts.servicecontractsid=vtiger_crmentity.crmid LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_servicecontracts.sc_related_to WHERE vtiger_crmentity.deleted=0 AND Left(fulldeliverytime,7)=?";
        $result=$adb->pquery($query,array($date));
        $array= array();
        $array[]=iconv('utf-8','GB18030//IGNORE','合同编号');
        $array[]=iconv('utf-8','GB18030//IGNORE','客户名称');
        $array[]=iconv('utf-8','GB18030//IGNORE','确认交付人员');
        $array[]=iconv('utf-8','GB18030//IGNORE','确认交付时间');
        $fileName ="产品确认产付_".time();  //这里定义表名。简单点的就直接  $fileName = time();

        header('Content-Type: application/vnd.ms-excel');   //header设置
        header("Content-Disposition: attachment;filename=".$fileName.".csv");
        header('Cache-Control: max-age=0');
        $fp = fopen('php://output','a');    //打开php文件句柄，php://output表示直接输出到PHP缓存,a表示将输出的内容追加到文件末尾
        fputcsv($fp,$array);  //fputcsv() 函数将行格式$head化为 CSV 并写入一个打开的文件$fp。
        if(!empty($result)){
            while($value=$adb->fetch_array($result)){
                $array=array();
                $username=$value['fulldeliveryid']==6934?'系统确认':$value['username'];
                $array[]=iconv('utf-8','GB18030//IGNORE', $value['contract_no']);
                $array[]=iconv('utf-8','GB18030//IGNORE', $value['accountname']);
                $array[]=iconv('utf-8','GB18030//IGNORE',$username);
                $array[]=iconv('utf-8','GB18030//IGNORE',$value['fulldeliverytime']);
                fputcsv($fp,$array);
            }
        }
        fclose($fp);
    }
    /**
     * 搜索使用，
     *  1.block部位空2.搜索类型！=0，3.presence 原来是0，2，列表有些字段为1的但是需要可以显示
     * @return bool
     */
    public function getNoCompleteSearchFields(){
        $noCompleteArr = array('modulestatus','contract_no','signaturetype','contract_classification','sc_related_to','contract_type','servicecontractstype','accountownerid','invoicecompany','assigned_user_id','Receivedate','Signid');
        $fieldsArr=array();
        $moduleBlockFields = Vtiger_Field_Model::getAllForModule($this);
        $this->fields = array();
        foreach($moduleBlockFields as $moduleFields){
            foreach($moduleFields as $moduleField){
                $block = $moduleField->get('block');
                $searchtype = $moduleField->get('searchtype');
                //$presence = $moduleField->get('presence');
                $isshowfield = $moduleField->get('isshowfield');
                $reltablename = $moduleField->get('reltablename');
                $listtabid = $moduleField->get('listtabid');
                if(!in_array($moduleField->get('name'),$noCompleteArr)){
                    continue;
                }
                if(empty($block)) {
                    continue;
                }
                if(empty($searchtype)) {
                    continue;
                }
                if($isshowfield==1) {
                    continue;
                }
                if(!empty($reltablename)){
                    if($listtabid!=2){
                        $moduleField->set('column', $moduleField->get('reltablename').'.'.$moduleField->get('reltablecol'));
                    }else{
                        $moduleField->set('column', $moduleField->get('table').'.'.$moduleField->get('column').'_name');
                    }

                }else{
                    $moduleField->set('column', $moduleField->get('column'));
                }

                $fieldsArr[$moduleField->get('name')] = $moduleField;
            }
        }
        return $fieldsArr;

    }
}
?>
