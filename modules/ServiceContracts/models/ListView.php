<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * 一个数字 猜 一个单词
   2 5 1 6  3  6 9 7 3
 *************************************************************************************/

class ServiceContracts_ListView_Model extends Vtiger_ListView_Model {
	public function getListViewEntries($pagingModel, $searchField=null) {
		$db = PearDatabase::getInstance();
		$moduleName = 'ServiceContracts';
        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');

        if(!empty($searchField)){
            if(isset($searchField['BugFreeQuery'])){
                $_REQUEST['BugFreeQuery'] = $searchField['BugFreeQuery'];
            }
            if(isset($searchField['public'])){
                $_REQUEST['public'] = $searchField['public'];
            }
        }

		if(empty($orderBy) && empty($sortOrder) && $moduleName != "Users"){
			$orderBy = 'vtiger_servicecontracts.servicecontractsid';
			$sortOrder = 'DESC';
		}
        $this->getSearchWhere();
        $listQuery = $this->getQuery();
		if(strstr($listQuery,',vtiger_servicecontracts.accountownerid')){
			$listQuery = str_replace(',vtiger_servicecontracts.accountownerid',',(select last_name from vtiger_users where id= (select smownerid from vtiger_crmentity where crmid=vtiger_account.accountid limit 1)) as accountownerid',$listQuery);
		}

        $listQuery.=$this->getUserWhere();
        if(!empty($searchField)){
            $listQuery=str_replace('smownerid_owner,','smownerid_owner,(SELECT vtiger_users.email1 FROM vtiger_users WHERE vtiger_users.id=vtiger_crmentity.smownerid LIMIT 1) as email,',$listQuery);
        }
        if($this->get('src_module')=='RefillApplication'){
            $listQuery=str_replace('vtiger_servicecontracts.contract_no,',"if(vtiger_servicecontracts.contract_no='','待生成编号',vtiger_servicecontracts.contract_no) as contract_no,",$listQuery);
        }
		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();

        $listQuery .= ' AND  vtiger_crmentity.setype=\'ServiceContracts\'  ORDER BY '. $orderBy . ' ' .$sortOrder;

		$viewid = ListViewSession::getCurrentView($moduleName);

		ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,

        //待签收合同记录
        if($_REQUEST['public'] == 'NoComplete'){
            $listQuery=str_replace("vtiger_servicecontracts.last_collate_remark,","vtiger_servicecontracts.last_collate_remark,(SELECT sum(unit_price) FROM vtiger_receivedpayments WHERE vtiger_receivedpayments.relatetoid = vtiger_servicecontracts.servicecontractsid) AS receivedtotal,",$listQuery);
            $listQuery=str_replace("ORDER BY"," AND vtiger_servicecontracts.servicecontractsid in(SELECT relatetoid FROM vtiger_receivedpayments WHERE relatetoid>0) AND vtiger_servicecontracts.iscomplete = 0 and vtiger_servicecontracts.modulestatus not in('c_cancel','c_recovered','c_canceling','c_stop','c_complete') ORDER BY",$listQuery);
        }
		$listQuery .= " LIMIT $startIndex,".($pageLimit);
        $listQuery=str_replace("AND vtiger_servicecontracts.accountownerid IS NOT NULL","",$listQuery);
        $listQuery=str_replace("vtiger_servicecontracts.accountownerid LIKE","vtiger_users.last_name LIKE",$listQuery);
        $listQuery=str_replace("WHERE 1=1","LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid WHERE 1=1",$listQuery);
//		echo $listQuery;die;
		$listResult = $db->pquery($listQuery, array());
		$listViewRecordModels = array();

		//3.在进行一次转化，目的何在
		$index = 0;
        $recordModel=Vtiger_Record_Model::getCleanInstance('ServiceContracts');
        $dochargebacks=$recordModel->personalAuthority('ServiceContracts','closedContracts')?1:0;
        global $current_user;
        $userinfo=getUserInfo($current_user->id);
        $bcustomer=array();
        if(!in_array($current_user->id,array(43,1179,7871))
            && !in_array($userinfo['roleid'],array('H102','H122','H125','H127','H130','H133','H142','H162','H163','H165','H171','H178','H89','H90'))
            && strpos($userinfo['parentdepartment'],'H1::H25')===false
        ){
            $bcustomer=$recordModel->getBcustomer();
        }

        while($rawData=$db->fetch_array($listResult)) {
            if(in_array($rawData['servicecontractsid'],$bcustomer)){
                continue;
            }
            $rawData['id'] = $rawData['servicecontractsid'];
            $rawData['closedContracts'] = $dochargebacks;
            $listViewRecordModels[$rawData['servicecontractsid']] = $rawData;
		}

		return $listViewRecordModels;
	}

    // 移动端搜索
    public function m_search($searchField) {
        $listQuery = '';
        if(!empty($searchField)){
            foreach($searchField as $key=>$value){
                foreach ($value as $k => $v) {
                    $listQuery.= " and ".$v['search_key'] .$v['operator'] . $v['search_value'];
                }
            }
        }
        return $listQuery;
    }

    public function getUserWhere(){
        $searchDepartment = $_REQUEST['department'];//部门搜索的部门
        if(empty($searchDepartment)){
            $searchDepartment = 'H1';
        }
        global $current_user;

        $where=getAccessibleUsers('ServiceContracts','List',true);
        $userid=getDepartmentUser($searchDepartment);
        $listQuery = '';
        if(!empty($searchDepartment)){
            if(!empty($where)&&$where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $where=!empty($where)?$where:array(-1);
            //$listQuery .= " AND vtiger_servicecontracts.receiveid IN(".implode(',', $where).")";   //young.yang 20150626 这一句话是做什么用的？删除了，不然商务看不到自己领取的合同
        }
		if($this->get('src_module')=='ExtensionTrial'){
            $whereE=getAccessibleUsers('ExtensionTrial','List',true);
            if($whereE!='1=1'){
                $sql=" and vtiger_crmentity.smownerid in(".implode(',',$whereE).")";
            }else{
                $sql="";
            }
            //return " and vtiger_servicecontracts.modulestatus='已发放' {$sql} and vtiger_servicecontracts.delayuserid=0 AND EXISTS(SELECT 1 FROM vtiger_extensiontrial LEFT JOIN `vtiger_crmentity` ON vtiger_crmentity.crmid=vtiger_extensiontrial.extensiontrialid WHERE vtiger_crmentity.deleted=0 AND vtiger_extensiontrial.servicecontractsid=vtiger_servicecontracts.servicecontractsid HAVING count(1)<2)";
            return " and vtiger_servicecontracts.modulestatus='已发放' {$sql} AND EXISTS(SELECT 1 FROM vtiger_extensiontrial LEFT JOIN `vtiger_crmentity` ON vtiger_crmentity.crmid=vtiger_extensiontrial.extensiontrialid WHERE vtiger_crmentity.deleted=0 AND vtiger_extensiontrial.servicecontractsid=vtiger_servicecontracts.servicecontractsid HAVING count(1)<2)";
        }

        /* if($this->get('src_module')=='SalesOrder'){
            $listQuery .= ' and servicecontractsid not in(select servicecontractsid from vtiger_salesorder ) and modulestatus="c_complete"';
        } */

        //wangbin 2015年6月3日 星期三 添加合同列表的部门搜索;
        $department = $_REQUEST['department'];

        //end
        if(!empty($where)&&getAccessibleUsers('ServiceContracts','List',true)!='1=1'){

        	if(!empty($current_user->user_manager)){
        		$searchDepartment=explode(' |##| ',$current_user->user_manager);
        		foreach($searchDepartment as $val){
        			$userid=getDepartmentUser($val);
        			$where=array_intersect($where,$userid);
        		}
        	}
            $where=!empty($where)?$where:array(-1);
        	$where=implode(',',$where);
            //2015-04-28 young 工单弹出的合同只能是业绩所属人（提单人）才能看到

            $shareaccount=' OR EXISTS(SELECT 1 FROM vtiger_shareaccount WHERE vtiger_shareaccount.userid in('.$where.') and vtiger_shareaccount.sharestatus=1 AND vtiger_shareaccount.accountid=vtiger_servicecontracts.sc_related_to)';
            $invoicecompany=' OR '.getAccessibleCompany('vtiger_servicecontracts.servicecontractsid');
            if($this->get('src_module')=='SalesOrder'){
                $listQuery .= ' and (vtiger_servicecontracts.receiveid in ('.$where.'))';
            }else if($this->get('src_module')=='OrderChargeback'){
                $listQuery .= ' and (vtiger_crmentity.smownerid in ('.$where.') or vtiger_servicecontracts.receiveid in ('.$where.') or vtiger_account.serviceid='.$current_user->id.')';
            }else if($this->get('src_module')=='Invoice'){
                $listQuery .= ' and (vtiger_crmentity.smownerid in ('.$where.') or vtiger_servicecontracts.receiveid in ('.$where.') or vtiger_account.serviceid='.$current_user->id.' or vtiger_servicecontracts.signid in ('.$where.') or EXISTS(SELECT 1 FROM vtiger_crmentity AS crmtable WHERE crmtable.crmid=vtiger_servicecontracts.sc_related_to AND crmtable.smownerid IN ('.$where.')))';
            }else if($this->get('src_module')=='Newinvoice'){
                $listQuery .= ' and (vtiger_crmentity.smownerid in ('.$where.') or vtiger_servicecontracts.receiveid in ('.$where.') or vtiger_account.serviceid='.$current_user->id.' or vtiger_servicecontracts.signid in ('.$where.') or EXISTS(SELECT 1 FROM vtiger_crmentity AS crmtable WHERE crmtable.crmid=vtiger_servicecontracts.sc_related_to AND crmtable.smownerid IN ('.$where.'))'.$shareaccount.$invoicecompany.')';
            }else if($this->get('src_module')=='RefillApplication'){
                $listQuery .= ' and (vtiger_crmentity.smownerid in ('.$where.') or vtiger_servicecontracts.receiveid in ('.$where.') or EXISTS(SELECT 1 FROM vtiger_crmentity AS crmtable WHERE crmtable.crmid=vtiger_servicecontracts.sc_related_to AND crmtable.smownerid IN ('.$where.'))'.$shareaccount.')';
            }else{
                $listQuery .= ' and (vtiger_crmentity.smownerid in ('.$where.') or vtiger_servicecontracts.receiveid in ('.$where.') OR vtiger_servicecontracts.signid IN('.$where.') '.$shareaccount.$invoicecompany.')';
            }

        }
        //2015年5月25日 星期一 wangbin 回款页面弹出的合同只能选择有客户的合同;
        //同时没有产品的合同也不能在弹出页面显示出来
        if($this->get('src_module')=='ReceivedPayments'){
            //$listQuery .=' AND sc_related_to !="" AND vtiger_servicecontracts.productid != ""';
            // 2016-10-25 周海 没有产品的合同可以 在弹出页面显示出来
            $listQuery .=' AND sc_related_to !="" ';
            $listQuery .= " AND vtiger_servicecontracts.modulestatus='c_complete'";
            //如果是商务的话，弹出的合同只能是
        }
        if($this->get('src_module')=='SalesOrder'){
            $listQuery .= ' and servicecontractsid not in(select vtiger_salesorder.servicecontractsid from vtiger_salesorder LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_salesorder.servicecontractsid LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_salesorder.salesorderid WHERE vtiger_crmentity.deleted=0 AND vtiger_servicecontracts.multitype=0 and vtiger_salesorder.iscancel=0) and vtiger_servicecontracts.modulestatus="c_complete"';
        }
        if($this->get('src_module')=='Invoice'){
            //$listQuery .= " and signdate> '2015-07-31 23:59:59'";
            // 周海 2016-9-5 发票添加筛选合同 状态改为不是作废
            $listQuery .= " AND (vtiger_servicecontracts.modulestatus!='c_cancel')";
        }
        if($this->get('src_module')=='Newinvoice'){
            //根据发票查找合同时(正常发票：只能关联已签收合同,预开票：只能关联已发放、已归还和已签收合同) gaocl add 2018/03/26
            $invoicetype = $_REQUEST['invoicetype'];

            if(isset($invoicetype)){
                //正常发票
                if($invoicetype == 'c_normal'){
                    //已签收合同
                    $listQuery .= " AND vtiger_servicecontracts.modulestatus IN('c_complete','c_refund','c_tranbusiness')";
                }
                //预开票
                if($invoicetype == 'c_billing'){
                    //已发放、已归还和已签收合同---纸质合同
                    //已签收的----电子合同
                    $listQuery .= " AND (((vtiger_servicecontracts.modulestatus IN ('已发放','c_recovered','c_complete') and vtiger_servicecontracts.signaturetype='papercontract') or (vtiger_servicecontracts.modulestatus='c_complete' and  vtiger_servicecontracts.signaturetype='eleccontract')) OR vtiger_servicecontracts.isguarantee=1)";
                }
            }

        }
        if($this->get('src_module')=='OrderChargeback'){
//            $listQuery .= "AND vtiger_servicecontracts.modulestatus in ('c_complete','c_tovoid','c_refund','c_tranbusiness')";
            $listQuery .= "AND vtiger_servicecontracts.modulestatus in ('c_complete','c_tovoid','c_refund','c_tranbusiness') AND NOT EXISTS(SELECT 1 FROM vtiger_orderchargeback LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_orderchargeback.orderchargebackid WHERE vtiger_orderchargeback.servicecontractsid=vtiger_servicecontracts.servicecontractsid AND vtiger_crmentity.deleted=0 and vtiger_orderchargeback.modulestatus!='c_complete')";
        }
        if($this->get('src_module')=='SeparateInto'){
            $listQuery .= "AND vtiger_servicecontracts.modulestatus in('已发放','c_recovered') AND NOT EXISTS(SELECT 1 FROM vtiger_separateinto LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_separateinto.separateintoid WHERE vtiger_separateinto.servicecontractsid=vtiger_servicecontracts.servicecontractsid AND vtiger_crmentity.deleted=0 AND vtiger_separateinto.modulestatus<>'a_exception')";
        }
        if($this->get('src_module')=='RefillApplication'){
            $dateTime=date('Y-m-d');
            if(!empty($_REQUEST['rechargesource']) &&$_REQUEST['rechargesource']=='INCREASE'){
                $listQuery .= " AND (vtiger_servicecontracts.modulestatus='c_complete' OR ( vtiger_servicecontracts.isguarantee = 1 AND  vtiger_servicecontracts.modulestatus NOT IN('c_cancel','c_canceling'))) ";
            }else{
                $listQuery .= "AND (vtiger_servicecontracts.modulestatus = 'c_complete' OR (vtiger_servicecontracts.modulestatus NOT IN ( 'c_cancel', 'c_canceling', 'c_cancelings' ) AND vtiger_servicecontracts.isguarantee = 1))";
                //限制有效期时间
                //                $listQuery .= " AND ((vtiger_servicecontracts.modulestatus='c_complete' AND vtiger_servicecontracts.effectivetime>='{$dateTime}') OR (vtiger_servicecontracts.modulestatus NOT in('c_cancel','c_canceling','c_cancelings') AND ((vtiger_servicecontracts.isguarantee=1 AND vtiger_servicecontracts.effectivetime>='{$dateTime}') OR (vtiger_servicecontracts.isguarantee=1 AND (vtiger_servicecontracts.effectivetime IS NULL OR vtiger_servicecontracts.effectivetime='')))))";
            }
        }
        if($this->get('src_module')=='ContractsAgreement')
        {
            $listQuery .= " AND vtiger_servicecontracts.sideagreement=0 AND vtiger_servicecontracts.modulestatus in('c_complete','已发放')";
        }
        if($this->get('src_module')=='CustomerStatement')
        {
            $listQuery .= " AND  vtiger_servicecontracts.modulestatus in('c_complete')";
        }
        //待签收合同记录
        if($_REQUEST['public'] == 'NoComplete'){
            $listQuery .= " AND vtiger_servicecontracts.servicecontractsid in(SELECT relatetoid FROM vtiger_receivedpayments WHERE relatetoid>0) AND vtiger_servicecontracts.iscomplete = 0 and vtiger_servicecontracts.modulestatus not in('c_cancel','c_recovered','c_canceling','c_stop','c_complete') ";
        }


        return $listQuery;
    }
    public function getListViewHeaders() {
        $sourceModule = $this->get('src_module');
        $queryGenerator = $this->get('query_generator');
        if(!empty($sourceModule)){
            return $queryGenerator->getModule()->getPopupFields();
        }else{

            $list=$queryGenerator->getModule()->getListFields();
            $temp=array();
            foreach($list as $fields){
                $temp[$fields['fieldlabel']]=$fields;
            }

            return $temp;
        }
        return $queryGenerator->getFocus()->list_fields_name;
    }
    public function getListViewCount() {
        if(0==$this->isAllCount && 0==$this->isFromMobile){
            return 0;
        }
        $db = PearDatabase::getInstance();
        $queryGenerator = $this->get('query_generator');
//print_r(debug_backtrace(0));
        //搜索条件
        $this->getSearchWhere();
        //用户条件
        $where=$this->getUserWhere();
        //$where.= ' AND accountname is NOT NULL';
        $queryGenerator->addUserWhere($where);
        $listQuery =  $queryGenerator->getQueryCount();

        $listQuery=str_replace("AND vtiger_servicecontracts.accountownerid IS NOT NULL","",$listQuery);
        $listQuery=str_replace("vtiger_servicecontracts.accountownerid LIKE","vtiger_users.last_name LIKE",$listQuery);
        $listQuery=str_replace("WHERE 1=1","LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid WHERE 1=1",$listQuery);
        //$listQuery .= $this->getSearchWhere();
        //echo $listQuery.'<br>';die();
        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }
}
