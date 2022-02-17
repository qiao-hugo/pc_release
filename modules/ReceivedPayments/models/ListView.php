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
class ReceivedPayments_ListView_Model extends Vtiger_ListView_Model {

	public function getListViewEntries($pagingModel) {
		
		$db = PearDatabase::getInstance();

        $moduleName = 'ReceivedPayments';
        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');

        if(empty($orderBy) && empty($sortOrder) && $moduleName != "Users"){
            $orderBy = 'receivedpaymentsid';
            $sortOrder = 'DESC';
        }
        //$this->getSearchWhere();
        //wangbin 注释，改用自定义的列表sql 表头字段，总记录数，以及搜索字段，都需要更改。
        $listQuery = $this->getQuery();
        //获取自定义语句拼接方法
        $pattern='(vtiger_servicecontracts.contract_no) as';
        $listQuery=str_replace($pattern,'(SELECT crm.label FROM vtiger_crmentity as crm WHERE crm.crmid=vtiger_receivedpayments.relatetoid ) AS ',$listQuery);
        $this->getSearchWhere();

        $listQuery.=$this->getUserWhere();
        
        $queryGenerator = $this->get('query_generator');
        $searchwhere=$queryGenerator->getSearchWhere();
        if(!empty($searchwhere)){
            $listQuery.=' and '.$searchwhere;
        }
        $pattern='/\(vtiger_servicecontracts.contract_no(?!,)/';
        $listQuery=preg_replace($pattern,'vtiger_receivedpayments.relatetoid IN(SELECT crm2.crmid FROM vtiger_crmentity AS crm2 WHERE crm2.setype in(\'ServiceContracts\',\'SupplierContracts\') AND crm2.deleted=0 AND crm2.label',$listQuery);

        $startIndex = $pagingModel->getStartIndex();

        $pageLimit = $pagingModel->getPageLimit();
        $listQuery=str_replace('AND vtiger_servicecontracts.contract_no IS NOT NULL','',$listQuery);
        //代付款金额
        $listQuery=str_replace('payer_reference','payer_reference,vtiger_staypayment.staypaymentjine',$listQuery);
        //收据弹框内容
        if($_REQUEST['src_module'] == 'Receipt'){
            $contractid = $_REQUEST['contractid'];
            $listQuery .= " AND vtiger_receivedpayments.receivedstatus = 'deposit' AND vtiger_receivedpayments.relatetoid={$contractid} ";
            $listQuery=str_replace('WHERE','WHERE vtiger_receivedpayments.receivedpaymentsid NOT IN (SELECT receivedpaymentsid FROM vtiger_receipt WHERE receivedpaymentsid > 0 AND deleted = 0) AND vtiger_receivedpayments.relatetoid NOT IN (SELECT servicecontractsid FROM vtiger_orderchargeback) AND ',$listQuery);
        }
        $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;
        
        $viewid = ListViewSession::getCurrentView($moduleName);
        
        ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,

        $listQuery .= " LIMIT $startIndex,".($pageLimit);
        
//        echo $listQuery;die();
        $listResult = $db->pquery($listQuery, array());
        $listViewRecordModels = array();
        global $current_user;
        //3.在进行一次转化，目的何在
        $recordModel=Vtiger_Record_Model::getCleanInstance('ReceivedPayments');
        $dochargebacks=$recordModel->personalAuthority('ReceivedPayments','dochargebacks')?1:0;
        $setReceiveStatus=$recordModel->personalAuthority('ReceivedPayments','setReceiveStatus')?1:0;
        $NonPayCertificate=$recordModel->personalAuthority('ReceivedPayments','NonPayCertificate')?1:0;
        $dorechargeable=$recordModel->personalAuthority('ReceivedPayments','dorechargeable')?1:0;
        //$deleted=($current_user->is_admin=='on')?1:0;
        $index = 0;
        while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['receivedpaymentsid'];
            //$rawData['deleted'] = ($rawData['smownerid']==$current_user->id&&in_array($rawData['modulestatus'],array('a_exception','a_normal')))?1:0;
            $rawData['deleted'] = 0;
            $rawData['setReceiveStatus'] = $setReceiveStatus;
            $rawData['dochargebacks'] = $dochargebacks;
            $rawData['NonPayCertificate'] = $NonPayCertificate;
            $rawData['dorechargeable'] = $dorechargeable;
            $rawData['dobackcash'] =($rawData['receivedstatus']=='normal' && empty($rawData['relatetoid']) && $rawData['createid']==$current_user->id)?1:0;
            $listViewRecordModels[$rawData['receivedpaymentsid']] = $rawData;
        }
        return $listViewRecordModels;
	}
	public function getUserWhere(){
	    $listQuery='';
	    
        switch ($_REQUEST['filter']){
			case "sevreceived":
				$listQuery .=' and vtiger_receivedpayments.discontinued=0 and datediff(DATE(over_date),CURDATE())<7 and datediff(DATE(over_date),CURDATE())>=0';
				break;
			case "fifreceived":
				$listQuery .=' and vtiger_receivedpayments.discontinued=0 and datediff(DATE(over_date),CURDATE())<15 and datediff(DATE(over_date),CURDATE())>=0';
				break;
			case "overreceived":
				$listQuery .=' and vtiger_receivedpayments.discontinued=0 and datediff(DATE(over_date),CURDATE())<0';
				break;
			case "noreceived":
				$listQuery .=' and vtiger_receivedpayments.discontinued=0';
				break;
			case "isreceived":
				$listQuery .=' and vtiger_receivedpayments.discontinued=1';
				break;
				case "noservice":
				    $listQuery .=" and vtiger_receivedpayments.relatetoid = '' ";
				    break;
		}
        $listQueryowen='';
        $listQueryCompany=' OR '.getAccessibleCompany('vtiger_receivedpayments.relatetoid');
        $listQueryCompany.=' OR vtiger_receivedpayments.companycode '.getAccessibleCompany(-1,'ReceivedPayments',true,-1,'X-X-X',true);
        if(!empty($searchDepartment)&&$searchDepartment!='H1'){
            $userid=getDepartmentUser($searchDepartment);
            $where=getAccessibleUsers('ReceivedPayments','List',true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $where=!empty($where)?$where:array(-1);
            $listQueryowen= ' OR vtiger_receivedpayments.createid in ('.implode(',',$where).')';
            $listQueryowen.=' OR EXISTS(SELECT 1 FROM vtiger_crmentity AS crmvendor WHERE crmvendor.crmid=vtiger_receivedpayments.accountid AND crmvendor.smownerid in('.implode(',',$where).'))';
            $listQueryowen.=' OR EXISTS(SELECT 1 FROM vtiger_crmentity AS crmmaybeaccount WHERE crmmaybeaccount.crmid=vtiger_receivedpayments.maybe_account AND crmmaybeaccount.smownerid '.$where.')';
            $listQueryowen.=$listQueryCompany;
        }else{
            $where=getAccessibleUsers();
            if($where!='1=1'){
                $listQueryowen= ' OR vtiger_receivedpayments.createid '.$where;
                $listQueryowen.=' OR EXISTS(SELECT 1 FROM vtiger_crmentity AS crmvendor WHERE crmvendor.crmid=vtiger_receivedpayments.accountid AND crmvendor.smownerid '.$where.')';
                $listQueryowen.=' OR EXISTS(SELECT 1 FROM vtiger_crmentity AS crmmaybeaccount WHERE crmmaybeaccount.crmid=vtiger_receivedpayments.maybe_account AND crmmaybeaccount.smownerid '.$where.')';
                $listQueryowen.=$listQueryCompany;
            }
        }
		//
		$where=getAccessibleUsers();
			if($where!='1=1'){
				$listQuery .= ' and (exists(select crmid from vtiger_crmentity where vtiger_receivedpayments.relatetoid=vtiger_crmentity.crmid and vtiger_crmentity.deleted=0 and vtiger_crmentity.smownerid '.$where.') OR vtiger_servicecontracts.receiveid '.$where.$listQueryowen.'
				 OR EXISTS(SELECT 1 FROM vtiger_shareaccount WHERE vtiger_shareaccount.userid '.$where.' and vtiger_shareaccount.sharestatus=1 AND vtiger_shareaccount.accountid=vtiger_servicecontracts.sc_related_to))';
			}

		$listQuery .= ' AND vtiger_receivedpayments.deleted=0';

        //收据弹框内容
        if($_REQUEST['src_module'] == 'Receipt'){
            $contractid = $_REQUEST['contractid'];
            $listQuery .= " AND vtiger_receivedpayments.receivedstatus = 'deposit' AND vtiger_receivedpayments.relatetoid={$contractid} ";
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
	    $listResult = $db->pquery($listQuery, array());
	    return $db->query_result($listResult,0,'counts'); 
	    
	    
	    /* //2015年5月6日 星期三  wangbin  自定义的sql需要重写计算总数语句
	    $db = PearDatabase::getInstance();
	    $listQuery =  ReceivedPayments_Record_Model::getlistviewsql();
	    $this->getSearchWhere();
	    $listQuery.=$this->getUserWhere();
	    
	    $queryGenerator = $this->get('query_generator');
	    $searchwhere=$queryGenerator->getSearchWhere();
	    if(!empty($searchwhere)){
	        $listQuery.=' and '.$searchwhere;
	    }//end */
	    $listResult = $db->pquery($listQuery, array());
	    return $db->num_rows($listResult);
	}
	
}
