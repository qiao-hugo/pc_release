<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class SalesorderProductsrel_ListView_Model extends Vtiger_ListView_Model {

	//根据参数显示数据
	public function getListViewEntries($pagingModel) {
        $db = PearDatabase::getInstance();

        $moduleName = 'SalesorderProductsrel';
        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');

        if(empty($orderBy) && empty($sortOrder) && $moduleName != "Users"){
            $orderBy = 'salesorderproductsrelid';
            $sortOrder = 'DESC';
        }
        $this->getSearchWhere();
        $listQuery = $this->getQuery();
        $listQuery.=$this->getUserWhere();


        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();


        $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;

        $viewid = ListViewSession::getCurrentView($moduleName);

        ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,

        $listQuery .= " LIMIT $startIndex,".($pageLimit+1);
        //echo $listQuery;die();
        $listResult = $db->pquery($listQuery, array());
        $listViewRecordModels = array();
        global $current_user;
        //3.在进行一次转化，目的何在
        $index = 0;
        while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['salesorderproductsrelid'];
            $listViewRecordModels[$rawData['salesorderproductsrelid']] = $rawData;
        }
//print_r($listViewRecordModels);
        return $listViewRecordModels;
	}
    public function getUserWhere(){
        $listQuery = '';
        //追加用户条件
        $where=getAccessibleUsers();
        if($where!='1=1'){
            $listQuery .= ' and vtiger_salesorderproductsrel.ownerid '.$where;
        }

        if($_REQUEST['public']=='unaudited'){
            $listQuery .=" and vtiger_salesorderproductsrel.salesorderproductsrelstatus='unaudited'";
        }elseif($_REQUEST['public']=='reject'){
            $listQuery .=" and vtiger_salesorderproductsrel.salesorderproductsrelstatus='reject'";
        }elseif($_REQUEST['public']=='pass'){
            $listQuery .=" and vtiger_salesorderproductsrel.salesorderproductsrelstatus='pass'";
        }else{
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
        $db = PearDatabase::getInstance();
        $queryGenerator = $this->get('query_generator');

        $where=$this->getUserWhere();

        $queryGenerator->addUserWhere($where);
        $listQuery =  $queryGenerator->getQueryCount();
//echo $listQuery;die();
        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }
	
	//当前模块禁止直接添加数据 Edit by Joe @20150507
	public function getBasicLinks(){
		$basicLinks = array();
		
		return $basicLinks;
	}
	public function getListViewLinks(){
		return array();
	}
}