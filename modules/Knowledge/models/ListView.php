<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/


class Knowledge_ListView_Model extends Vtiger_ListView_Model {
	public function getListViewEntries($pagingModel) {
		$db = PearDatabase::getInstance();

		$moduleName = 'Knowledge';
        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');
		if(empty($orderBy) && empty($sortOrder) && $moduleName != "Users"){
			$orderBy = 'CONVERT (knowledgetop USING utf8)  DESC,knowledgedate';
			$sortOrder = 'DESC';
		}
        if($_REQUEST['filter']=='NewList'){
            $orderBy = 'knowledgedate DESC,CONVERT (knowledgetop USING utf8)  ';
            $sortOrder = 'DESC';
        }
		
        $this->getSearchWhere();
        
        $listQuery = $this->getQuery();
        $listQuery.=$this->getfilter();
        $listQuery.=$this->getUserWhere();

	
		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();


        $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;
		
		$viewid = ListViewSession::getCurrentView($moduleName);
	
		ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,
	
		$listQuery .= " LIMIT $startIndex,".($pageLimit+1);
		$listResult = $db->pquery($listQuery, array());
		$listViewRecordModels = array();
		//3.在进行一次转化，目的何在
		$index = 0;
        while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['knowledgeid'];
            $listViewRecordModels[$rawData['knowledgeid']] = $rawData;
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
        global $current_user;
        if(!empty($searchDepartment)&&$searchDepartment!='H1'){  //20150525 柳林刚 加入
            $userid=getDepartmentUser($searchDepartment);
            $where=getAccessibleUsers('Knowledge','List',true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $where=!empty($where)?$where:array(-1);
            //$listQuery .= ' and (vtiger_knowledge.author in ('.implode(',',$where).") or EXISTS (SELECT 1 FROM vtiger_users LEFT JOIN vtiger_user2department ON vtiger_users.id=vtiger_user2department.userid WHERE vtiger_user2department.departmentid IN((SELECT vtiger_departments.departmentid FROM vtiger_departments WHERE vtiger_departments.parentdepartment REGEXP IFNULL(replace(vtiger_knowledge.department,' |##| ','|'),'aa'))) AND vtiger_users.id={$current_user->id}) or vtiger_knowledge.knowledgecolumns='NewList' or vtiger_knowledge.`open`=1)";
            $listQuery .= ' and (vtiger_knowledge.author in ('.implode(',',$where).") or if((vtiger_knowledge.department<>'' AND vtiger_knowledge.isrole=''),EXISTS (SELECT 1 FROM vtiger_users LEFT JOIN vtiger_user2department ON vtiger_users.id=vtiger_user2department.userid WHERE vtiger_user2department.departmentid IN((SELECT vtiger_departments.departmentid FROM vtiger_departments WHERE vtiger_departments.parentdepartment REGEXP IFNULL(replace(vtiger_knowledge.department,' |##| ','|'),'aa'))) AND vtiger_users.id={$current_user->id}),if((vtiger_knowledge.department='' AND vtiger_knowledge.isrole<>''),find_in_set('{$current_user->roleid}',(IFNULL(replace(vtiger_knowledge.isrole,' |##| ',','),'aa'))),if((vtiger_knowledge.department<>'' AND vtiger_knowledge.isrole<>''),(EXISTS (SELECT 1 FROM vtiger_users LEFT JOIN vtiger_user2department ON vtiger_users.id=vtiger_user2department.userid WHERE vtiger_user2department.departmentid IN((SELECT vtiger_departments.departmentid FROM vtiger_departments WHERE vtiger_departments.parentdepartment REGEXP IFNULL(replace(vtiger_knowledge.department,' |##| ','|'),'aa'))) AND vtiger_users.id={$current_user->id}) AND find_in_set('{$current_user->roleid}',(IFNULL(replace(vtiger_knowledge.isrole,' |##| ',','),'aa')))),0))) or vtiger_knowledge.knowledgecolumns='NewList' or vtiger_knowledge.`open`=1 or if(vtiger_knowledge.knowledgeviewer = '',0,find_in_set('{$current_user->id}',(IFNULL(replace(vtiger_knowledge.knowledgeviewer,' |##| ',','),'aa')))))";
        }else{
            $where=getAccessibleUsers();
            if($where!='1=1'){
                $listQuery .= ' and (vtiger_knowledge.author '.$where." or if((vtiger_knowledge.department<>'' AND vtiger_knowledge.isrole=''),EXISTS (SELECT 1 FROM vtiger_users LEFT JOIN vtiger_user2department ON vtiger_users.id=vtiger_user2department.userid WHERE vtiger_user2department.departmentid IN((SELECT vtiger_departments.departmentid FROM vtiger_departments WHERE vtiger_departments.parentdepartment REGEXP IFNULL(replace(vtiger_knowledge.department,' |##| ','|'),'aa'))) AND vtiger_users.id={$current_user->id}),if((vtiger_knowledge.department='' AND vtiger_knowledge.isrole<>''),find_in_set('{$current_user->roleid}',(IFNULL(replace(vtiger_knowledge.isrole,' |##| ',','),'aa'))),if((vtiger_knowledge.department<>'' AND vtiger_knowledge.isrole<>''),(EXISTS (SELECT 1 FROM vtiger_users LEFT JOIN vtiger_user2department ON vtiger_users.id=vtiger_user2department.userid WHERE vtiger_user2department.departmentid IN((SELECT vtiger_departments.departmentid FROM vtiger_departments WHERE vtiger_departments.parentdepartment REGEXP IFNULL(replace(vtiger_knowledge.department,' |##| ','|'),'aa'))) AND vtiger_users.id={$current_user->id}) AND find_in_set('{$current_user->roleid}',(IFNULL(replace(vtiger_knowledge.isrole,' |##| ',','),'aa')))),0))) or vtiger_knowledge.knowledgecolumns='NewList' or vtiger_knowledge.`open`=1 or if(vtiger_knowledge.knowledgeviewer = '',0,find_in_set('{$current_user->id}',(IFNULL(replace(vtiger_knowledge.knowledgeviewer,' |##| ',','),'aa')))))";
            }
        }
        if($_REQUEST['public']=='undercarriage'){
            $listQuery .= ' and status=0';
        }else{
            $listQuery .= ' and status=1';
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
            	if(in_array($fields['displaytype'],array(1,2,3,5)) && $fields['fieldlabel']!='knowledgeContent'){
            	
                	$temp[$fields['fieldlabel']]=$fields;
            	}
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
        $listQuery.=$this->getfilter();
        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }
    public function getfilter() {
        if(!empty($_REQUEST['filter'])){
            return " AND vtiger_knowledge.knowledgecolumns='{$_REQUEST['filter']}' ";
        }
    }

}
