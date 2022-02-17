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
class SearchMatch_ListView_Model extends Vtiger_ListView_Model {
    public $isShow=0;
	public function getListViewEntries($pagingModel) {
		
		$db = PearDatabase::getInstance();

        $moduleName = 'ReceivedPayments';
        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');

        if(empty($orderBy) && empty($sortOrder)){
            $orderBy = 'receivedpaymentsid';
            $sortOrder = 'DESC';
        }
        $listQuery="select * from vtiger_receivedpayments WHERE 1=1";
        $listQuery=$this->getUserWhere($listQuery);
        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();
        $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;
        
        $viewid = ListViewSession::getCurrentView($moduleName);
        ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,

        $listQuery .= " LIMIT $startIndex,".($pageLimit);
        
        //echo $listQuery;die();
        $listResult = $db->pquery($listQuery, array());
        $listViewRecordModels = array();
        while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['receivedpaymentsid'];
            $rawData['isShow']=$this->isShow;
            if(!$this->isShow){
                //脱敏
                $rawData['paymentcode']='******';
                $rawData['paytitle']='******';
                $rawData['standardmoney']='******';
                $rawData['unit_price']='******';
            }
            $listViewRecordModels[$rawData['receivedpaymentsid']] = $rawData;

        }
        return $listViewRecordModels;
	}

	public function getUserWhere($listQuery){
        $bugFreeQueryArray=json_decode($_REQUEST['BugFreeQuery'],true);
        $paymentchannel=trim($bugFreeQueryArray['paymentchannel']);
        $reality_date=trim($bugFreeQueryArray['reality_date']);
        $paytitle=trim($bugFreeQueryArray['paytitle']);
        $standardmoney=trim($bugFreeQueryArray['standardmoney']);
        $paymentcode=trim($bugFreeQueryArray['paymentcode']);
        if($paymentchannel){
            $listQuery .=" and paymentchannel='".$paymentchannel."'";
        }
        if($reality_date){
            $listQuery .=" and reality_date='".$reality_date."'";
        }
        if($paytitle){
            if($paymentchannel=='支付宝转账'&&str_replace('*','',$paytitle)){
                $listQuery .=" and '".$paytitle."' like  REPLACE(vtiger_receivedpayments.paytitle,'*','_')";
            }else{
                $listQuery .=" and paytitle =  '".$paytitle."'";
            }
        }
        if($standardmoney){
            $listQuery .=" and standardmoney='".$standardmoney."'";
        }
        if($paymentcode){
            $listQuery .=" and paymentcode='".$paymentcode."'";
        }
        if($paymentchannel=='对公转账'&& $reality_date&&$paytitle&&$standardmoney){
            $this->isShow=1;
        }else if($paymentchannel=='支付宝转账'&&$paymentcode&&$paytitle){
            $this->isShow=1;
        }else if(($paymentchannel=='扫码')&&$paymentcode){
            $this->isShow=1;
        }
		$listQuery .= " AND vtiger_receivedpayments.deleted=0 and (relatetoid=0 or relatetoid is null or relatetoid ='') and paymentchannel is not null and receivedstatus='normal'";
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
	    $listResult = $db->pquery($listQuery, array());
	    return $db->num_rows($listResult);
	}
	
}
