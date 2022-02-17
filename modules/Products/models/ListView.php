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
 * DisposeMaintenance ListView Model Class
 */
class Products_ListView_Model extends Vtiger_ListView_Model {
	//根据参数显示数据
	public function getListViewEntries($pagingModel,$sarch=array()) {

        $db = PearDatabase::getInstance();

        $moduleName = 'Products';
        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');


        if(empty($orderBy) && empty($sortOrder) && $moduleName != "Users"){
            $orderBy = 'vtiger_crmentity.modifiedtime';
            $sortOrder = 'DESC';
        }
       // $listQuery= ServiceMaintenance_Record_Model::getServiceMaintenanceListSql();
        $listQuery=$this->getQuery();
        $this->getSearchWhere();
        $listQuery.=$this->getUserWhere();

        $queryGenerator = $this->get('query_generator');
        $searchwhere=$queryGenerator->getSearchWhere();
        if(!empty($searchwhere)){
            $listQuery.=' and '.$searchwhere;
        }
	
		$listQuery.=' order by '.$orderBy.' '.$sortOrder;
		
		$viewid = ListViewSession::getCurrentView($moduleName);
		
		ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,
        
        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();
        $sourceModule = $this->get('src_module');
        $sourceField = $this->get('src_field');
        if($sourceModule != 'ServiceContracts' && $sourceModule != 'SalesOrder'){
            if($sourceModule !== 'PriceBooks' && $sourceField !== 'priceBookRelatedList') {
                $listQuery .= " LIMIT $startIndex,".($pageLimit);
            }
        }
		$listResult = $db->pquery($listQuery, array());

        $listViewRecordModels = array();
        while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['productid'];
            $listViewRecordModels[$rawData['productid']] = $rawData;
        }
        return $listViewRecordModels;
	}
    public function getUserWhere(){
        $searchDepartment = $_REQUEST['department'];//部门
//      $where=getAccessibleUsers('DisposeMaintenance','List',true);
//        $userid=getDepartmentUser($searchDepartment);
        $listQuery = '';
        if(!empty($searchDepartment)&&$searchDepartment!='H1'){  //取消默认的H1验证
            $userid=getDepartmentUser($searchDepartment);
            $where=getAccessibleUsers('','',true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $where=!empty($where)?$where:array(-1);
            $listQuery .= ' and vtiger_crmentity.smownerid in ('.implode(',',$where).')';
        }else{
            $where=getAccessibleUsers();
            if($where!='1=1'){
                $listQuery .= ' and vtiger_crmentity.smownerid '.$where;
            }
        }
        $sourceModule = $this->get('src_module');
        if($sourceModule){ //弹出页面判断
            $listQuery .= ' and enddate>\''.date('Y-m-d 00:00:00',time()).'\'';  //去除过期时间
        }
        
        //wangbin 2015年7月6日 星期一 产品模块弹出框选择只能选择非套餐产品
        $src_record  = $this->get('src_record');
        if($sourceModule =="Products" && !empty($src_record)){
            $listQuery .=' AND ispackage != 1 AND vtiger_products.productid !='."$src_record";
        }
        $otherProduct=array("AccountPlatform","ProductProvider");
        if(in_array($sourceModule,$otherProduct)){
            $listQuery .=" AND productiscriminate='outpurchase'";
        }
        //end

         /* if(!empty($searchDepartment)){
            if(!empty($where)&&$where!='1=1'){
                $where=$userid;
            }else{
                $where=array_intersect($where,$userid);
            }
            $where=implode(',',$where);
            $listQuery .= ' and vtiger_servicemaintenance.serviceid'.$where;
        }
        
        if($_REQUEST['public']=='untreated'){
            //$listQuery = $listQuery." and vtiger_servicemaintenance.processstate='untreated'";
        }elseif($_REQUEST['public']=='processing'){
            $listQuery = $listQuery." and vtiger_servicemaintenance.processstate='processing'";
        }elseif($_REQUEST['public']=='processed'){
            $listQuery = $listQuery." and vtiger_servicemaintenance.processstate='processed'";
        }elseif($_REQUEST['public']=='unabletoprocess'){
            $listQuery = $listQuery." and vtiger_servicemaintenance.processstate='unabletoprocess'";
        }elseif($_REQUEST['public']=='cancellation'){
            $listQuery = $listQuery." and vtiger_servicemaintenance.processstate='cancellation'";
        }else{
            $listQuery = $listQuery." and vtiger_servicemaintenance.processstate='untreated'";
        }
        global $current_user;
        $id=$current_user->id;
        $where=getAccessibleUsers();
        if($where!='1=1'){
            //$listQuery .= ' and (vtiger_servicemaintenance.ownerid'.$where;
            $listQuery .= ' and ((select vtiger_crmentity.smownerid from vtiger_crmentity where vtiger_crmentity.crmid=vtiger_servicemaintenance.related_to)'.$where;

            $listQuery .= " OR vtiger_servicemaintenance.productid in (select vtiger_products.productid from vtiger_products where find_in_set($id,REPLACE(vtiger_products.productman,' |##| ',',')))";
            $listQuery .= " OR vtiger_servicemaintenance.productid in (select vtiger_products.productid from vtiger_products where find_in_set($id,REPLACE(vtiger_products.productmaintainer,' |##| ',','))))";
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
        $db = PearDatabase::getInstance();
        $queryGenerator = $this->get('query_generator');

        $where=$this->getUserWhere();

        $queryGenerator->addUserWhere($where);
        $listQuery =  $queryGenerator->getQueryCount();
        
        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }
}
