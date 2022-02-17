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
class Workflows_ListView_Model extends Vtiger_ListView_Model {
	//根据参数显示数据
	public function getListViewEntries($pagingModel) {
        $db = PearDatabase::getInstance();

        $moduleName = 'Workflows';
        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');


        if(empty($orderBy) && empty($sortOrder) && $moduleName != "Users"){
            $orderBy = 'modifiedtime';
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
	
		//$listQuery.=' order by vtiger_servicemaintenance.servicemaintenanceid desc';
		
		$viewid = ListViewSession::getCurrentView($moduleName);
		ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,
        
        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();
		$listQuery .= " LIMIT $startIndex,".($pageLimit);
		$listResult = $db->pquery($listQuery, array());
        //echo $listQuery;die();
        $listViewRecordModels = array();
        while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['workflowsid'];
            $listViewRecordModels[$rawData['workflowsid']] = $rawData;
        }
        return $listViewRecordModels;
	}
    public function getUserWhere(){
        $listQuery = '';
        $sourceModule = $this->get('src_module');

        if(!empty($sourceModule) && $sourceModule!='WorkflowStages' ){
            //2014-12-28 young.yang 增加子流程特殊验证
            $src_field=$this->get('src_field');
            if($src_field=='subworkflowsid'){
                $sourceModule='SalesorderProductsrel';
            }
            //end//已过滤
            $listQuery .= ' and mountmodule=\''.$sourceModule.'\'';
            $condition[]=$sourceModule;
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
        
        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }
}
