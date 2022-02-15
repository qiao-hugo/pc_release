<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/


class Invoicesign_ListView_Model extends Vtiger_ListView_Model {
	public function getListViewEntries($pagingModel) {
		$db = PearDatabase::getInstance();

		$moduleName = 'Billing';
        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');
		/*if(empty($orderBy) && empty($sortOrder) && $moduleName != "Users"){
			$orderBy = 'CONVERT (knowledgetop USING utf8)  DESC,knowledgedate';
			$sortOrder = 'DESC';
		}
        if($_REQUEST['filter']=='NewList'){
            $orderBy = 'knowledgedate DESC,CONVERT (knowledgetop USING utf8)  ';
            $sortOrder = 'DESC';
        }*/
		
        $this->getSearchWhere();
        
        $listQuery = $this->getQuery();
        $listQuery.=$this->getUserWhere();

	
		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();


        //$listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;
		
		$viewid = ListViewSession::getCurrentView($moduleName);
	
		ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,
	
		$listQuery .= " LIMIT $startIndex,".($pageLimit+1);
        //echo $listQuery;
		$listResult = $db->pquery($listQuery, array());
		$listViewRecordModels = array();
		//3.在进行一次转化，目的何在
		$index = 0;
        while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['billingid'];
            $listViewRecordModels[$rawData['billingid']] = $rawData;
		}
// 		echo "<pre>";
// 		print_r($listViewRecordModels);
// 		echo "</pre>";
// 		exit;
		return $listViewRecordModels;
	}
    public function getUserWhere(){
        $searchDepartment = $_REQUEST['department'];//部门    
         $listQuery='';
        if(!empty($searchDepartment)&&$searchDepartment!='H1'){  //20150525 柳林刚 加入
            $userid=getDepartmentUser($searchDepartment);
            $where=getAccessibleUsers('Billing','List',true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $where=!empty($where)?$where:array(-1);
            $listQuery .= ' and vtiger_billing.billingid in ('.implode(',',$where).')';
        }else{
            $where=getAccessibleUsers();
            if($where!='1=1'){
                $listQuery .= ' and vtiger_billing.billingid '.$where;

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
