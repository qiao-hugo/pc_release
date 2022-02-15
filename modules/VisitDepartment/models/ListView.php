<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class VisitDepartment_ListView_Model extends Vtiger_ListView_Model {
	

	public function getListViewEntries($pagingModel,$request=array()) {
		$db = PearDatabase::getInstance();
		$moduleName ='VisitDepartment';

		
		if(!empty($request)){
			if(isset($request['BugFreeQuery'])){
				$_REQUEST['BugFreeQuery'] = $request['BugFreeQuery'];
			}
			if(isset($request['public'])){
				$_REQUEST['public'] = $request['public'];
			}
		}

		$orderBy = $this->getForSql('orderby');
		$sortOrder = $this->getForSql('sortorder');

		//List view will be displayed on recently created/modified records
		//列表视图将显示最近的创建修改记录  ---做什么用处
		if(empty($orderBy) && empty($sortOrder)){

			$orderBy = 'visitdepartmentid';

			$sortOrder = 'DESC';
		}
		$this->getSearchWhere();
        $listQuery = $this->getQuery();
        
        $listQuery.=$this->getUserWhere();
        global $current_user;


		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();

        //$listQuery = str_replace('(vtiger_crmentity.smownerid) as smownerid,','(select CONCAT(last_name,\'[\',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),\'\'),\']\',(if(`status`=\'Active\',\'\',\'[离职]\'))) as last_name from vtiger_users where vtiger_crmentity.smownerid=vtiger_users.id) as smownerid,',$listQuery);
        $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;

		$viewid = ListViewSession::getCurrentView($moduleName);
	
		ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,
	
		$listQuery .= " LIMIT $startIndex,".($pageLimit);


        //echo $listQuery;//die();
		//echo $listQuery;die;
		$listResult = $db->pquery($listQuery, array());


		$index = 0;
		while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['visitdepartmentid'];
			$listViewRecordModels[$rawData['visitdepartmentid']] = $rawData;
		}
		return $listViewRecordModels;
	}


	

	
    public function getListViewHeaders() {
        $sourceModule = $this->get('src_module');
        $queryGenerator = $this->get('query_generator');
        if(!empty($sourceModule)){
           return $queryGenerator->getModule()->getPopupFields();
        }else{

            $list=$queryGenerator->getModule()->getListFields();
            $temp=array();

            $public = $_REQUEST['public'];
		
            foreach($list as $fields){
            	if ($public == 'unaudited') {
					if ($fields['fieldlabel'] != 'insuredtype' && $fields['fieldlabel'] != 'name') {
            			$temp[$fields['fieldlabel']]=$fields;
            		}
				} else {
					$temp[$fields['fieldlabel']]=$fields;
				}
                
            }
            
            return $temp;
        }
        return $queryGenerator->getFocus()->list_fields_name;
        
    }
    public function getUserWhere(){
       global $current_user;
        $searchDepartment = $_REQUEST['department'];
        $listQuery=' ';
        $parentdepartment=getDepartmentInformation($current_user->departmentid);
        $tempstr='';
        if(!empty($parentdepartment[$current_user->departmentid][1])){
            $tempstr="EXISTS(SELECT 1 FROM vtiger_departments WHERE LOCATE(vtiger_visitdepartment.deparmentid,vtiger_departments.parentdepartment) AND vtiger_departments.parentdepartment like '{$parentdepartment[$current_user->departmentid][1]}%') OR ";
        }
        if(!empty($searchDepartment)&&$searchDepartment!='H1'){  
            $userid=getDepartmentUser($searchDepartment);
            $where=getAccessibleUsers('VisitDepartment','List', true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
                $where=empty($where)?array($current_user->id):$where;
                $listQuery .= ' and ('.$tempstr.'vtiger_visitdepartment.userid in ('.implode(',',$where).'))';
            }
        }else{
            $where=getAccessibleUsers();
            if($where!='1=1'){
                $listQuery .= ' and ('.$tempstr.'vtiger_visitdepartment.userid '.$where . ' )';
            }
        }
        return $listQuery;
    }
    public function getListViewCount() {
        $db = PearDatabase::getInstance();
        $queryGenerator = $this->get('query_generator');
		//print_r(debug_backtrace(0));
        //搜索条件
        //$this->getSearchWhere();
        //用户条件
        $where=$this->getUserWhere();
        //$where.= ' AND accountname is NOT NULL';
        $queryGenerator->addUserWhere($where);
        $listQuery =  $queryGenerator->getQueryCount();
        //echo $listQuery.'<br>';die();
        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }

}