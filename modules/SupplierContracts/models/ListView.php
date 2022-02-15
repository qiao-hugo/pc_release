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

class SupplierContracts_ListView_Model extends Vtiger_ListView_Model {
	public function getListViewEntries($pagingModel,$searchField=null) {
        global $current_user;
        $db = PearDatabase::getInstance();

        $moduleName = 'SupplierContracts';
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
        $startIndex = $pagingModel->getStartIndex();

        $pageLimit = $pagingModel->getPageLimit();
        //$this->getSearchWhere();
        //wangbin 注释，改用自定义的列表sql 表头字段，总记录数，以及搜索字段，都需要更改。
        if(in_array($this->get('src_module'),array('ContractGuarantee','ReceivedPayments','Newinvoice','Staypayment'))) {
            $listQuery = $this->getContractGuaranteeListSQL();
            $listQuery .= " LIMIT $startIndex," . ($pageLimit);
        }else {
            $listQuery = $this->getQuery();
            //获取自定义语句拼接方法
            $this->getSearchWhere();
            $listQuery .= $this->getUserWhere();

            $queryGenerator = $this->get('query_generator');
            $searchwhere = $queryGenerator->getSearchWhere();
            if (!empty($searchwhere)) {
                $listQuery .= ' and ' . $searchwhere;
            }
            if (!empty($searchField)) {
                $listQuery = str_replace('smownerid_owner,', 'smownerid_owner,(SELECT vtiger_users.email1 FROM vtiger_users WHERE vtiger_users.id=vtiger_crmentity.smownerid LIMIT 1) as email,', $listQuery);
            }
            $src_module=$this->get('src_module');
            if(!empty($src_module)){
                $pattern='/vtiger_suppliercontracts.contract_no(?=,)/';
                $listQuery=preg_replace($pattern,'concat(IFNULL(vtiger_suppliercontracts.contract_no,\'\'),IF(vtiger_suppliercontracts.type=\'cost\',\'-->[费用合同]\',\'-->[采购合同]\')) as contract_no',$listQuery);

            }
            $listQuery=str_replace('vendorid LIKE','vtiger_vendor.vendorname LIKE',$listQuery);
            $listQuery=str_replace('vendorid IS NOT NULL','vtiger_vendor.vendorname IS NOT NULL',$listQuery);
            $listQuery.=' AND IF(EXISTS(SELECT 1 FROM `vtiger_supplierstatus` WHERE userid='.$current_user->id.'),EXISTS(SELECT 1 FROM `vtiger_supplierstatus` WHERE vtiger_supplierstatus.userid='.$current_user->id.' AND vtiger_supplierstatus.suppliercontractsstatus=vtiger_suppliercontracts.suppliercontractsstatus),1=1)';
            $viewid = ListViewSession::getCurrentView($moduleName);

            ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,
            $listQuery .= 'ORDER BY vtiger_suppliercontracts.suppliercontractsid DESC';
            $listQuery .= " LIMIT $startIndex," . ($pageLimit);
        }
        //echo $listQuery;die();
        $listResult = $db->pquery($listQuery, array());
        $listViewRecordModels = array();
        global $current_user;
        //3.在进行一次转化，目的何在
        $index = 0;
        while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['suppliercontractsid'];
            $listViewRecordModels[$rawData['suppliercontractsid']] = $rawData;
        }
        return $listViewRecordModels;


		/*$db = PearDatabase::getInstance();
		$moduleName = 'SupplierContracts';
        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');



		if(empty($orderBy) && empty($sortOrder) && $moduleName != "Users"){
			$orderBy = 'vtiger_suppliercontracts.suppliercontractsid';
			$sortOrder = 'DESC';
		}
        $this->getSearchWhere();
        $listQuery = $this->getQuery();
		if(strstr($listQuery,',vtiger_suppliercontracts.accountownerid')){
			$listQuery = str_replace(',vtiger_suppliercontracts.accountownerid',',(select last_name from vtiger_users where id= (select smownerid from vtiger_crmentity where crmid=vtiger_account.accountid limit 1)) as accountownerid',$listQuery);
		}
        //echo $listQuery;
        $listQuery.=$this->getUserWhere();
        //echo $listQuery;die;
	
	
		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();


        $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;

		$viewid = ListViewSession::getCurrentView($moduleName);
	
		ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,
	
		$listQuery .= " LIMIT $startIndex,".($pageLimit);
		//echo $listQuery;die;
		$listResult = $db->pquery($listQuery, array());
		$listViewRecordModels = array();

		//3.在进行一次转化，目的何在
		$index = 0;
        while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['suppliercontractsid'];
            $listViewRecordModels[$rawData['suppliercontractsid']] = $rawData;
		}

		return $listViewRecordModels;*/
	}
    public function getUserWhere(){
        if($this->get('src_module')=='ContractGuarantee'){
            return ;
        }
        $searchDepartment = $_REQUEST['department'];//部门搜索的部门
        if(empty($searchDepartment)){
            $searchDepartment = 'H1';
        }
        $where=getAccessibleUsers('SupplierContracts','List',true);
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

        /* if($this->get('src_module')=='SalesOrder'){
            $listQuery .= ' and servicecontractsid not in(select servicecontractsid from vtiger_salesorder ) and modulestatus="c_complete"';
        } */
        
        //wangbin 2015年6月3日 星期三 添加合同列表的部门搜索;
        $department = $_REQUEST['department'];

        //end
        if(!empty($where)&&getAccessibleUsers('SupplierContracts','List',true)!='1=1'){
        	global $current_user;
        	if(!empty($current_user->user_manager)){
        		$searchDepartment=explode(' |##| ',$current_user->user_manager);
        		foreach($searchDepartment as $val){
        			$userid=getDepartmentUser($val);
        			$where=array_intersect($where,$userid);
        		}
        	}
        	$where=implode(',',$where);
            //2015-04-28 young 工单弹出的合同只能是业绩所属人（提单人）才能看到
            /*if($this->get('src_module')=='SalesOrder'){
                $listQuery .= ' and (vtiger_suppliercontracts.signid in ('.$where.'))';
            }else if($this->get('src_module')=='OrderChargeback'){
                $listQuery .= ' and (vtiger_suppliercontracts.signid in ('.$where.') or vtiger_suppliercontracts.signid in ('.$where.') or vtiger_suppliercontracts.signid='.$current_user->id.')';
            }else if($this->get('src_module')=='Invoice'){
                $listQuery .= ' and (vtiger_suppliercontracts.signid in ('.$where.') or vtiger_suppliercontracts.signid in ('.$where.') or vtiger_suppliercontracts.signid='.$current_user->id.' or vtiger_suppliercontracts.signid in ('.$where.'))';
            }else{
                $listQuery .= ' and (vtiger_suppliercontracts.signid in ('.$where.') or vtiger_crmentity.signid in ('.$where.'))';
            }*/
            //$invoicecompany=' OR EXISTS(SELECT 1 FROM vtiger_invoicecompanyuser WHERE vtiger_invoicecompanyuser.modulename=\'ht\' AND vtiger_suppliercontracts.companycode=vtiger_invoicecompanyuser.invoicecompany AND vtiger_invoicecompanyuser.userid='.$current_user->id.')';
            $invoicecompany=' OR '.getAccessibleCompany('vtiger_suppliercontracts.companycode','SupplierContracts');
            $listQuery .= ' and (vtiger_crmentity.smownerid in('.$where.') or vtiger_suppliercontracts.signid in ('.$where.') or vtiger_suppliercontracts.receiptorid in ('.$where.')'.$invoicecompany.')';
        }
        if($this->get('src_module')=='SuppContractsAgreement'){
            $listQuery .= " and vtiger_suppliercontracts.modulestatus in('c_receive','c_recovered','c_complete','c_stamp')";
        }
        if($this->get('src_module')=='RefillApplication' ){
            //cxh 如果是合同变更 且 有原采购合同id 则修改 2019-11-15
            if($_REQUEST['rechargesource']=='contractChanges' && isset($_REQUEST['oldAccountId'])){
                $currentTime=date("Y-m-d");
                $listQuery .= " and (vtiger_suppliercontracts.modulestatus ='c_complete' OR ( vtiger_suppliercontracts.isguarantee=1 AND vtiger_suppliercontracts.modulestatus <> 'c_cancel')) AND vtiger_suppliercontracts.effectivetime > '".$currentTime."' AND vtiger_suppliercontracts.vendorid =".$_REQUEST['oldAccountId'];
            // 如果不是合同变更走下面的sql 限制 合同变更原合同选择没有限制状态
            }else if($_REQUEST['rechargesource']!='contractChanges'){
                $listQuery .= " and vtiger_suppliercontracts.modulestatus in('c_receive','c_recovered','c_complete')";
            }
        }
        if($this->get('src_module')=='ProductProvider'){
            $dateTime=date('Y-m-d');
            $listQuery .= " and (vtiger_suppliercontracts.modulestatus ='c_complete' OR vtiger_suppliercontracts.isguarantee=1) AND vtiger_suppliercontracts.effectivetime>='{$dateTime}'";
        }

        if($this->get('src_module')=='SupplierStatement')
        {
            $listQuery .= " AND  vtiger_suppliercontracts.modulestatus in('c_complete')";
        }
        /*if($this->get('src_module')=='SalesOrder'){
            $listQuery .= ' and suppliercontractsid not in(select vtiger_salesorder.suppliercontractsid from vtiger_salesorder LEFT JOIN vtiger_suppliercontracts ON vtiger_suppliercontracts.suppliercontractsid=vtiger_salesorder.suppliercontractsid LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_salesorder.salesorderid WHERE vtiger_crmentity.deleted=0 AND vtiger_suppliercontracts.multitype=0 and vtiger_salesorder.iscancel=0) and vtiger_suppliercontracts.modulestatus="c_complete"';
        }
        if($this->get('src_module')=='Invoice'){
            //$listQuery .= " and signdate> '2015-07-31 23:59:59'";
            $listQuery .= " AND (vtiger_suppliercontracts.modulestatus='c_complete' OR vtiger_suppliercontracts.cantheinvoice=1)";
        }
        if($this->get('src_module')=='OrderChargeback'){
            $listQuery .= "AND vtiger_suppliercontracts.modulestatus='c_complete' AND NOT EXISTS(SELECT 1 FROM vtiger_orderchargeback LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_orderchargeback.orderchargebackid WHERE vtiger_orderchargeback.suppliercontractsid=vtiger_suppliercontracts.suppliercontractsid AND vtiger_crmentity.deleted=0)";
        }*/
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
        if(in_array($this->get('src_module'),array('ContractGuarantee','ReceivedPayments','Newinvoice','Staypayment'))){
            $listQuery=$this->getContractGuaranteeListCountSQL();
            $listResult = $db->pquery($listQuery, array());
            $counts=$db->num_rows($listResult);

            return $counts>500?500:$counts;

        }else{
            $this->getSearchWhere();
            //用户条件
            $where=$this->getUserWhere();
            //$where.= ' AND accountname is NOT NULL';
            $queryGenerator->addUserWhere($where);
            $listQuery =  $queryGenerator->getQueryCount();
            $listQuery=str_replace('vendorid LIKE','vtiger_vendor.vendorname LIKE',$listQuery);
            $listQuery=str_replace('vendorid IS NOT NULL','vtiger_vendor.vendorname IS NOT NULL',$listQuery);
        }



        //$listQuery .= $this->getSearchWhere();
        //echo $listQuery.'<br>';die();
        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }
    public function getContractGuaranteeListSQL(){
        $searchKey=$this->get('search_key');
        if(!empty($searchKey) && $searchKey=='vendorid'){
            $ServiceContractsSearch='LEFT JOIN vtiger_crmentity as crmtemp ON crmtemp.crmid=vtiger_servicecontracts.sc_related_to';
            $SupplierContractSearch='LEFT JOIN vtiger_crmentity as crmtemp ON crmtemp.crmid=vtiger_suppliercontracts.vendorid';
        }
        if($this->get('src_module')=='ContractGuarantee'){
            $ServiceContractsWhere=" AND vtiger_servicecontracts.isguarantee=0 AND vtiger_servicecontracts.modulestatus in('c_receive','c_recovered','c_stamp','b_actioning','已发放','b_check','a_normal') AND vtiger_servicecontracts.isguarantee<>1";
            $SupplierContractWhere=" AND vtiger_suppliercontracts.isguarantee=0 AND vtiger_suppliercontracts.modulestatus in('c_receive','c_recovered','c_stamp','b_actioning','b_check','a_normal') AND vtiger_suppliercontracts.isguarantee<>1";
        }elseif($this->get('src_module')=='ReceivedPayments'){
            $ServiceContractsWhere=" AND vtiger_servicecontracts.modulestatus ='c_complete'";
            $SupplierContractWhere=" AND vtiger_suppliercontracts.modulestatus='c_complete'";
        }elseif($this->get('src_module')=='Newinvoice'){
            $ServiceContractsWhere=" AND vtiger_servicecontracts.modulestatus in('c_receive','c_recovered','已发放','c_complete')";
            $SupplierContractWhere=" AND vtiger_suppliercontracts.modulestatus in('c_receive','c_recovered','c_complete')";
            $invoicetype= $_REQUEST['invoicetype'];
            if($invoicetype == 'c_billing' ){
                $ServiceContractsWhere = " AND (vtiger_servicecontracts.modulestatus in('c_receive','c_recovered','已发放','c_complete') OR vtiger_servicecontracts.isguarantee =1) "; //cby add
                $SupplierContractWhere =" AND (vtiger_suppliercontracts.modulestatus in('c_receive','c_recovered','c_complete') OR vtiger_suppliercontracts.isguarantee =1) "; //cby add
            }
            if($invoicetype == 'c_normal'){
                $ServiceContractsWhere = "AND vtiger_servicecontracts.modulestatus = 'c_complete'";
                $SupplierContractWhere =" AND vtiger_suppliercontracts.modulestatus ='c_complete'";
            }
        }elseif($this->get('src_module')=='Staypayment'){
            $ServiceContractsWhere=" AND vtiger_servicecontracts.modulestatus NOT IN ('c_cancel','c_canceling','a_exception','c_stop','c_completeclosed')";
            $SupplierContractWhere=" AND vtiger_suppliercontracts.modulestatus='c_complete'";
        }
        $useListAuthority=$this->getUseListAuthority();
        $searchAccountForPop=$this->getSearchAccountForPop();
        $ServiceContractsQuery="SELECT vtiger_crmentity.crmid as suppliercontractsid,concat(vtiger_crmentity.label,'-->[服务合同]') as contract_no,(select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_crmentity.smownerid=vtiger_users.id) as smownerid,vtiger_crmentity.smownerid AS smownerid_id,
                  (SELECT vtiger_account.accountname FROM vtiger_account WHERE vtiger_account.accountid=vtiger_servicecontracts.sc_related_to) AS vendorid,
                  vtiger_servicecontracts.contract_type AS returndate,
                  vtiger_crmentity.setype AS modulename FROM vtiger_crmentity 
                LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_crmentity.crmid 
                {$ServiceContractsSearch}
                WHERE vtiger_crmentity.deleted=0 AND vtiger_crmentity.setype='ServiceContracts'".$ServiceContractsWhere;
        $SupplierContractsQuery="SELECT vtiger_crmentity.crmid as suppliercontractsid,concat(vtiger_crmentity.label,IF(vtiger_suppliercontracts.type='cost','-->[费用合同]','-->[采购合同]')) as contract_no,(select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_crmentity.smownerid=vtiger_users.id) as smownerid,vtiger_crmentity.smownerid AS smownerid_id,
                    (SELECT vtiger_vendor.vendorname FROM vtiger_vendor WHERE vtiger_vendor.vendorid=vtiger_suppliercontracts.vendorid) AS vendorid,
                  (SELECT vtiger_products.productname FROM vtiger_products LEFT JOIN vtiger_vendorsrebate ON vtiger_products.productid=vtiger_vendorsrebate.productid WHERE vtiger_vendorsrebate.suppliercontractsid=vtiger_suppliercontracts.suppliercontractsid LIMIT 1) AS returndate,
                  vtiger_crmentity.setype AS modulename FROM vtiger_crmentity 
                LEFT JOIN vtiger_suppliercontracts ON vtiger_suppliercontracts.suppliercontractsid=vtiger_crmentity.crmid 
                {$SupplierContractSearch}
                WHERE vtiger_crmentity.deleted=0 AND vtiger_crmentity.setype='SupplierContracts'".$SupplierContractWhere;

        return $ServiceContractsQuery.$useListAuthority['ServiceContracts'].$searchAccountForPop.' limit 500 UNION ALL '.$SupplierContractsQuery.$useListAuthority['SupplierContracts'].$searchAccountForPop;

    }
    /**
     * 拜访单客户弹出列表求合
     * @return string
     */
    public function getContractGuaranteeListCountSQL(){
        $useListAuthority=$this->getUseListAuthority();
        $searchKey=$this->get('search_key');
        if(!empty($searchKey) && $searchKey=='vendorid'){
            $ServiceContractsSearch='LEFT JOIN vtiger_crmentity as crmtemp ON crmtemp.crmid=vtiger_servicecontracts.sc_related_to';
            $SupplierContractSearch='LEFT JOIN vtiger_crmentity as crmtemp ON crmtemp.crmid=vtiger_suppliercontracts.vendorid';
        }
        if($this->get('src_module')=='ContractGuarantee'){
            $ServiceContractsWhere=" AND vtiger_servicecontracts.isguarantee=0 AND vtiger_servicecontracts.modulestatus in('c_receive','c_recovered','c_stamp','b_actioning','已发放','b_check','a_normal') AND vtiger_servicecontracts.isguarantee<>1";
            $SupplierContractWhere=" AND vtiger_suppliercontracts.isguarantee=0 AND vtiger_suppliercontracts.modulestatus in('c_receive','c_recovered','c_stamp','b_actioning','b_check','a_normal') AND vtiger_suppliercontracts.isguarantee<>1";
        }elseif($this->get('src_module')=='ReceivedPayments'){
            $ServiceContractsWhere=" AND vtiger_servicecontracts.modulestatus ='c_complete'";
            $SupplierContractWhere=" AND vtiger_suppliercontracts.modulestatus='c_complete'";
        }elseif($this->get('src_module')=='Newinvoice'){
            $ServiceContractsWhere=" AND vtiger_servicecontracts.modulestatus in('c_receive','c_recovered','已发放','c_complete')";
            $SupplierContractWhere=" AND vtiger_suppliercontracts.modulestatus in('c_receive','c_recovered','c_complete')";
        }elseif($this->get('src_module')=='Staypayment'){
            $ServiceContractsWhere=" AND vtiger_servicecontracts.modulestatus ='c_complete'";
            $SupplierContractWhere=" AND vtiger_suppliercontracts.modulestatus='c_complete'";
        }
        $searchAccountForPop=$this->getSearchAccountForPop();
        $ServiceContractsQuery="SELECT 1 FROM vtiger_crmentity 
                LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_crmentity.crmid 
                {$ServiceContractsSearch}
                WHERE vtiger_crmentity.deleted=0 AND vtiger_crmentity.setype='ServiceContracts'{$ServiceContractsWhere}
                ";
        $SupplierContractsQuery="SELECT 1 FROM vtiger_crmentity 
                LEFT JOIN vtiger_suppliercontracts ON vtiger_suppliercontracts.suppliercontractsid=vtiger_crmentity.crmid 
                {$SupplierContractSearch}
                WHERE vtiger_crmentity.deleted=0 AND vtiger_crmentity.setype='SupplierContracts'{$SupplierContractWhere}";

        return $ServiceContractsQuery.$useListAuthority['ServiceContracts'].$searchAccountForPop.' limit 500 UNION ALL '.$SupplierContractsQuery.$useListAuthority['SupplierContracts'].$searchAccountForPop;
        //return $accountQuery.$useListAuthority.$searchAccountForPop.' UNION ALL '.$vendorsQuery.$useListAuthority.$searchAccountForPop.' UNION ALL '.$schoolQuery.$useListAuthority.$searchAccountForPop;;
    }
    public function getUseListAuthority(){
        $menuModelsList = Vtiger_Menu_Model::getAll(true);
        global $current_user;
        $SupplierContractsQuery='';
        if(in_array('SupplierContracts',array_keys($menuModelsList))) {
            $tempWhere = getAccessibleUsers('SupplierContracts', 'List', true);
            if ($tempWhere != '1=1') {
                $SupplierContractsQuery=' AND (vtiger_crmentity.smownerid IN('.implode(',',$tempWhere).') OR vtiger_suppliercontracts.signid IN('.implode(',',$tempWhere).'))';
            }
        }else{
            $SupplierContractsQuery=' AND vtiger_crmentity.smownerid ='.$current_user->id;
        }
        $ServiceContractsQuery='';

        if(in_array('ServiceContracts',array_keys($menuModelsList))) {
            $tempWhere = getAccessibleUsers('ServiceContracts', 'List', true);
            if ($tempWhere != '1=1') {
                $serviceid=" OR EXISTS(SELECT 1 FROM vtiger_servicecomments WHERE vtiger_servicecomments.related_to=vtiger_servicecontracts.sc_related_to AND vtiger_servicecomments.serviceid={$current_user->id})";
                $shareaccount=" OR EXISTS(SELECT accountid FROM vtiger_shareaccount WHERE vtiger_shareaccount.userid={$current_user->id} AND vtiger_shareaccount.sharestatus=1 AND vtiger_shareaccount.accountid=vtiger_servicecontracts.sc_related_to)";
                $ServiceContractsQuery=' and (vtiger_crmentity.smownerid in ('.implode(',',$tempWhere).') or vtiger_servicecontracts.receiveid in ('.implode(',',$tempWhere).') or vtiger_servicecontracts.signid in ('.implode(',',$tempWhere).') or EXISTS(SELECT 1 FROM vtiger_crmentity AS crmtable WHERE crmtable.crmid=vtiger_servicecontracts.sc_related_to AND crmtable.smownerid IN ('.implode(',',$tempWhere).'))'.$serviceid.$shareaccount.')';

            }
        }else{
            $ServiceContractsQuery=' AND vtiger_crmentity.smownerid ='.$current_user->id;
        }

        return array('SupplierContracts'=>$SupplierContractsQuery,'ServiceContracts'=>$ServiceContractsQuery,);
    }

    /**
     * 取搜索的SQL
     * @return string
     */
    public function getSearchAccountForPop(){
        $searchKey=$this->get('search_key');
        $listQuery='';
        if(!empty($searchKey)){
            if($searchKey=='contract_no'){
                $searchValue = $this->get('search_value');
                $searchValue=$this->check_input($searchValue);
                $listQuery=empty($searchValue)?"":" AND vtiger_crmentity.label LIKE '%".$searchValue."%'";
            }elseif($searchKey=='vendorid'){
                $searchValue = $this->get('search_value');
                $searchValue=$this->check_input($searchValue);
                $listQuery=empty($searchValue)?"":" AND crmtemp.label LIKE '%".$searchValue."%'";
            }
        }

        return $listQuery;
    }
    public function check_input($data){
        //对特殊符号添加反斜杠
        $data = addslashes($data);
        //判断自动添加反斜杠是否开启
        if(get_magic_quotes_gpc()){
            //去除反斜杠
            $data = stripslashes($data);
        }
        //把'_'过滤掉
        $data = str_replace("_", "\_", $data);
        $data = str_replace("=", "", $data);
        $data = str_replace("'", "", $data);
        //把'%'过滤掉
        $data = str_replace("%", "\%", $data);
        //把'*'过滤掉
        $data = str_replace("*", "\*", $data);
        //回车转换
        $data = nl2br($data);
        //去掉前后空格
        $data = trim($data);
        //将HTML特殊字符转化为实体
        $data = htmlspecialchars($data);
        return $data;
    }
}