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
 * Vtiger ListView Model Class
 * 2015-1-20 18:12:01 王斌 增加客户筛选列表
 */
class DelayMatch_ListView_Model extends Vtiger_ListView_Model {

	public function getListViewEntries($pagingModel) {
		
		$db = PearDatabase::getInstance();

        $moduleName = 'DelayMatch';
        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');

        if(empty($orderBy) && empty($sortOrder)){
            $orderBy = 'receivedpaymentsid';
            $sortOrder = 'DESC';
        }
        $listQuery="SELECT vtiger_receivedpayments.receivedpaymentsid,vtiger_receivedpayments.owncompany,vtiger_receivedpayments.paytitle,vtiger_receivedpayments.paymentchannel,vtiger_receivedpayments.paymentcode,vtiger_receivedpayments.reality_date,vtiger_receivedpayments.unit_price,IF (vtiger_receivedpayments.staypaymentid> 0,'是','否') AS isstaypayment,IF (relatetoid> 0,'已匹配','未匹配') AS ismatch,vtiger_servicecontracts.contract_no,IF (istimeoutmatch=1,'是',IF (istimeoutmatch=2,'财务异常','否')) AS istimeoutmatch,IF (iscrossmonthmatch=1,'是',IF (iscrossmonthmatch=2,'财务异常','否')) AS iscrossmonthmatch,vtiger_receivedpayments.matchdate,(
SELECT CONCAT(last_name,'[',IFNULL((
SELECT departmentname FROM vtiger_departments WHERE departmentid=(
SELECT departmentid FROM vtiger_user2department WHERE userid=vtiger_users.id LIMIT 1)),''),']',IF (`status`='Active','','[离职]')) AS last_name FROM vtiger_users WHERE id=vtiger_receivedpayments.matcherid) AS username FROM vtiger_receivedpayments LEFT JOIN vtiger_servicecontracts ON vtiger_receivedpayments.relatetoid=vtiger_servicecontracts.servicecontractsid LEFT JOIN vtiger_account ON vtiger_receivedpayments.maybe_account=vtiger_account.accountid LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_account.accountid LEFT JOIN vtiger_staypayment ON vtiger_receivedpayments.staypaymentid=vtiger_staypayment.staypaymentid LEFT JOIN vtiger_account AS account2 ON vtiger_staypayment.accountid=account2.accountid LEFT JOIN vtiger_crmentity AS crm2 ON crm2.crmid=account2.accountid WHERE 1=1";
        $this->getSearchWhere();
        $queryGenerator = $this->get('query_generator');
        $searchwhere=$queryGenerator->getSearchWhere();
        if(!empty($searchwhere)){
            $listQuery.=' and '.$searchwhere;
        }
        $listQuery.=$this->getUserWhere();
        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();
        $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;
        
        $viewid = ListViewSession::getCurrentView($moduleName);
        ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,

        $listQuery .= " LIMIT $startIndex,".($pageLimit);
        
        //echo $listQuery;die();
        $listQuery=str_replace('(vtiger_receivedpayments.iscrossmonthmatch = 1 AND vtiger_receivedpayments.iscrossmonthmatch IS NOT NULL)','(vtiger_receivedpayments.iscrossmonthmatch !=0 AND vtiger_receivedpayments.iscrossmonthmatch IS NOT NULL)',$listQuery);
        $listQuery=str_replace('(vtiger_receivedpayments.istimeoutmatch = 1 AND vtiger_receivedpayments.istimeoutmatch IS NOT NULL)','(vtiger_receivedpayments.istimeoutmatch !=0 AND vtiger_receivedpayments.istimeoutmatch IS NOT NULL)',$listQuery);

        $listQuery=str_replace('(vtiger_receivedpayments.ismatchdepart = 0 AND vtiger_receivedpayments.ismatchdepart IS NOT NULL)','(vtiger_receivedpayments.relatetoid = 0 OR vtiger_receivedpayments.relatetoid IS NULL  or  vtiger_receivedpayments.relatetoid=\'\')',$listQuery);
        $listQuery=str_replace('(vtiger_receivedpayments.ismatchdepart = 1 AND vtiger_receivedpayments.ismatchdepart IS NOT NULL)','(vtiger_receivedpayments.relatetoid>0)',$listQuery);

        $listQuery=str_replace('(vtiger_receivedpayments.staypaymentid = 0 AND vtiger_receivedpayments.staypaymentid IS NOT NULL)','(vtiger_receivedpayments.relatetoid>0) and (vtiger_receivedpayments.staypaymentid = 0 OR vtiger_receivedpayments.staypaymentid IS NULL  or  vtiger_receivedpayments.staypaymentid=\'\')',$listQuery);
        $listQuery=str_replace('(vtiger_receivedpayments.staypaymentid = 1 AND vtiger_receivedpayments.staypaymentid IS NOT NULL)','(vtiger_receivedpayments.staypaymentid>0)',$listQuery);

        $listResult = $db->pquery($listQuery, array());
        $listViewRecordModels = array();
        while($rawData=$db->fetch_array($listResult)) {
            if($rawData['ismatch']=='未匹配'){
                unset($rawData['matchdate'],$rawData['username']);
            }
            $rawData['id'] = $rawData['receivedpaymentsid'];
            $listViewRecordModels[$rawData['receivedpaymentsid']] = $rawData;

        }
        return $listViewRecordModels;
	}





	public function getUserWhere(){
        global $current_user;
        $searchDepartment = $_REQUEST['department'];
        $listQuery='';
        if(!empty($searchDepartment)&&$searchDepartment!='H1'){  //20150525 柳林刚 加入
            $userid=getDepartmentUser($searchDepartment);
            $where=getAccessibleUsers('DelayMatch','List',true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $where=!empty($where)?$where:array(-1);
            //$listQuery= ' AND (EXISTS(SELECT 1 FROM vtiger_crmentity as crmtable WHERE  crmtable.deleted=0 AND crmtable.setype=\'Accounts\' AND crmtable.crmid=vtiger_servicecontracts.sc_related_to AND crmtable.smownerid in('.implode(',',$where).'))  OR vtiger_crmentity.smownerid in ('.implode(',',$where).'))';
            $listQuery= ' AND (vtiger_crmentity.smownerid in ('.implode(',',$where).')  or crm2.smownerid  in ('.implode(',',$where).') )';
        }else{
            $where=getAccessibleUsers();
            if($where!='1=1'){
                //$listQuery= ' AND (EXISTS(SELECT 1 FROM vtiger_crmentity as crmtable WHERE  crmtable.deleted=0 AND crmtable.setype=\'Accounts\' AND crmtable.crmid=vtiger_servicecontracts.sc_related_to AND crmtable.smownerid '.$where.')  OR vtiger_crmentity.smownerid '.$where.')';
                $listQuery= ' AND (vtiger_crmentity.smownerid '.$where.' or crm2.smownerid '.$where.')';
            }
        }

		$listQuery .= " AND vtiger_receivedpayments.deleted=0 and (vtiger_receivedpayments.istimeoutmatch!=0 or vtiger_receivedpayments.iscrossmonthmatch!=0) and vtiger_receivedpayments.receivedstatus='normal'";
	    return $listQuery;
	}


	public function getListViewHeaders() {
	    $sourceModule = $this->get('src_module');
	    $queryGenerator = $this->get('query_generator');
	    if(!empty($sourceModule)){
	        return $queryGenerator->getModule()->getPopupFields();
	    }else{
	        $list=$queryGenerator->getModule()->getListFields();
	        foreach($list as $fields){
	            $temp[$fields['fieldlabel']]=$fields;
	        }
	        return $temp;
	    }
	    return $queryGenerator->getFocus()->list_fields_name;
	}

	public function getListViewCount() {
	    // 原来的记录数计算，不敢删掉
	      $db = PearDatabase::getInstance();
	    $queryGenerator = $this->get('query_generator');
	
	    $where=$this->getUserWhere();
	
	    $queryGenerator->addUserWhere($where);
	    $listQuery =  $queryGenerator->getQueryCount();
	    //echo $listQuery;die();


        $listQuery=str_replace('WHERE 1=1','LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_account.accountid LEFT JOIN vtiger_staypayment ON vtiger_receivedpayments.staypaymentid=vtiger_staypayment.staypaymentid LEFT JOIN vtiger_account AS account2 ON vtiger_staypayment.accountid=account2.accountid LEFT JOIN vtiger_crmentity AS crm2 ON crm2.crmid=account2.accountid WHERE 1=1',$listQuery);
        $listQuery=str_replace('(vtiger_receivedpayments.iscrossmonthmatch = 1 AND vtiger_receivedpayments.iscrossmonthmatch IS NOT NULL)','(vtiger_receivedpayments.iscrossmonthmatch !=0 AND vtiger_receivedpayments.iscrossmonthmatch IS NOT NULL)',$listQuery);
        $listQuery=str_replace('(vtiger_receivedpayments.istimeoutmatch = 1 AND vtiger_receivedpayments.istimeoutmatch IS NOT NULL)','(vtiger_receivedpayments.istimeoutmatch !=0 AND vtiger_receivedpayments.istimeoutmatch IS NOT NULL)',$listQuery);

        $listQuery=str_replace('(vtiger_receivedpayments.ismatchdepart = 0 AND vtiger_receivedpayments.ismatchdepart IS NOT NULL)','(vtiger_receivedpayments.relatetoid = 0 OR vtiger_receivedpayments.relatetoid IS NULL  or  vtiger_receivedpayments.relatetoid=\'\')',$listQuery);
        $listQuery=str_replace('(vtiger_receivedpayments.ismatchdepart = 1 AND vtiger_receivedpayments.ismatchdepart IS NOT NULL)','(vtiger_receivedpayments.relatetoid>0)',$listQuery);

        $listQuery=str_replace('(vtiger_receivedpayments.staypaymentid = 0 AND vtiger_receivedpayments.staypaymentid IS NOT NULL)','(vtiger_receivedpayments.relatetoid>0) and (vtiger_receivedpayments.staypaymentid = 0 OR vtiger_receivedpayments.staypaymentid IS NULL  or  vtiger_receivedpayments.staypaymentid=\'\')',$listQuery);
        $listQuery=str_replace('(vtiger_receivedpayments.staypaymentid = 1 AND vtiger_receivedpayments.staypaymentid IS NOT NULL)','(vtiger_receivedpayments.staypaymentid>0)',$listQuery);


	    $listResult = $db->pquery($listQuery, array());
	    return $db->query_result($listResult,0,'counts'); 
	    $listResult = $db->pquery($listQuery, array());
	    return $db->num_rows($listResult);
	}

}
