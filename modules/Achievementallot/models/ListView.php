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
class Achievementallot_ListView_Model extends Vtiger_ListView_Model {

	//去除添加按钮
	public function getListViewLinks($linkParams) {
		return $links;
		exit;
	}
	public function getListViewEntries($pagingModel) {
			
		$db = PearDatabase::getInstance();

        $moduleName = 'ReceivedPayments';
        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');

        if(empty($orderBy) && empty($sortOrder) && $moduleName != "Users"){
            $orderBy = 'vtiger_achievementallot.receivedpaymentsid';
            $sortOrder = 'DESC';
        }
        //$this->getSearchWhere();
        //wangbin 注释，改用自定义的列表sql 表头字段，总记录数，以及搜索字段，都需要更改。
        //$listQuery = $this->getQuery();
        
        //$listQuery =  Achievementallot_Record_Model::getlistviewsql();
        $recordModel=Vtiger_Record_Model::getCleanInstance("Achievementallot");
        $listQuery =  $recordModel->getListvViewSqlTyun();
        //echo $listQuery;die;
        //获取自定义语句拼接方法
        $this->getSearchWhere();
        $listQuery.=$this->getUserWhere();
        
        $queryGenerator = $this->get('query_generator');

        $searchwhere=$queryGenerator->getSearchWhere();
        if(!empty($searchwhere)){
            $listQuery.=' and '.$searchwhere;
        }

        $startIndex = $pagingModel->getStartIndex();

        $pageLimit = $pagingModel->getPageLimit();

        $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;

        $viewid = ListViewSession::getCurrentView($moduleName);
        
        ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,

        $listQuery .= " LIMIT $startIndex,".($pageLimit);
        //echo $listQuery;exit;
        $listResult = $db->pquery($listQuery, array());
        $listViewRecordModels = array();
        global $current_user;
        //3.在进行一次转化，目的何在
        $index = 0;
        while($rawData=$db->fetch_array($listResult)) {
            $listViewRecordModels[] = $rawData;
        }
       /* echo "<pre>";
        print_r($listViewRecordModels);
        exit;*/
        return $listViewRecordModels;
	
	}
	public function getUserWhere(){
	    $listQuery='';
        $searchDepartment = $_REQUEST['department'];
        if(!empty($searchDepartment)&&$searchDepartment!='H1'){  //20150525 柳林刚 加入
            $userid=getDepartmentUser($searchDepartment);
            $where=getAccessibleUsers('Achievementallot','List',true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $where=!empty($where)?$where:array(-1);
            $listQuery .= ' and vtiger_achievementallot.receivedpaymentownid in ('.implode(',',$where).')';
        }else{
            $where=getAccessibleUsers();
            if($where!='1=1'){
                $listQuery .= ' and vtiger_achievementallot.receivedpaymentownid '.$where;
            }
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
	    /* 原来的记录数计算，不敢删掉
	     * $db = PearDatabase::getInstance();
	    $queryGenerator = $this->get('query_generator');
	    $where=$this->getUserWhere();
	    $queryGenerator->addUserWhere($where);
	    $listQuery =  $queryGenerator->getQueryCount();
	       //echo $listQuery;die();
	    $listResult = $db->pquery($listQuery, array());
	    return $db->query_result($listResult,0,'counts');
         */
	    
	    
	    //2015年5月6日 星期三  wangbin  自定义的sql需要重写计算总数语句
	    $db = PearDatabase::getInstance();
	    //$listQuery =  Achievementallot_Record_Model::getlistviewsql();
        $recordModel=Vtiger_Record_Model::getCleanInstance("Achievementallot");
        $listQuery =  $recordModel->getListviewCountSqlTyun();
	    $this->getSearchWhere();
	    $listQuery.=$this->getUserWhere();
	    
	    $queryGenerator = $this->get('query_generator');
	    $searchwhere=$queryGenerator->getSearchWhere();
	    if(!empty($searchwhere)){
	        $listQuery.=' and '.$searchwhere;
	    }//end
	    $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
	}
	
}
