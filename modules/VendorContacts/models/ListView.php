<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class VendorContacts_ListView_Model extends Vtiger_ListView_Model {
	public function getListViewEntries($pagingModel,$request=array()) {
		$db = PearDatabase::getInstance();
		$moduleName ='VisitingOrder';

		
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
			$orderBy = 'contactid';
			$sortOrder = 'DESC';
		}
		$this->getSearchWhere();
        $listQuery = $this->getQuery();
        
        $listQuery.=$this->getUserWhere();
        global $current_user;


		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();

		$viewid = ListViewSession::getCurrentView($moduleName);
	
		ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,
	
		$listQuery .= " LIMIT $startIndex,".($pageLimit);

        //echo $listQuery;die();

		$listResult = $db->pquery($listQuery, array());


		$index = 0;
		while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['contactid'];
			$listViewRecordModels[$rawData['contactid']] = $rawData;
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
            foreach($list as $fields){

                $temp[$fields['fieldlabel']]=$fields;
            }
           return $temp;
        }
        return $queryGenerator->getFocus()->list_fields_name;
        
    }
    public function getUserWhere(){
	    return ;
       global $current_user;
        $searchDepartment = $_REQUEST['department'];
        $sourceModule = $this->get('src_module');
        $listQuery=' ';
        //notfollow是未跟进的拜访单的状态followup是跟进后的拜访单状态

        if(!empty($searchDepartment)&&$searchDepartment!='H1'){  //20150525 柳林刚 加入
            $userid=getDepartmentUser($searchDepartment);
            $where=getAccessibleUsers('VisitingOrder','List',true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $where=!empty($where)?$where:array(-1);
            //$listQuery .= ' and vtiger_visitingorder.extractid in ('.implode(',',$where).')';

            //$listQuery .= ' and vtiger_crmentity.smownerid in ('.implode(',',$where).')';
            $listQuery .= ' and (vtiger_visitingorder.extractid in ('.implode(',',$where).') OR find_in_set('.$current_user->id.', REPLACE(vtiger_visitingorder.accompany,\' |##| \', \',\') ))';
        }else{
            $where=getAccessibleUsers();
            if($where!='1=1'){
            	//$listQuery .= ' and vtiger_visitingorder.extractid '.$where;
                //$listQuery .= ' and vtiger_crmentity.smownerid '.$where;
                $listQuery .= ' and (vtiger_visitingorder.extractid '.$where . ' OR find_in_set('.$current_user->id.', REPLACE(vtiger_visitingorder.accompany,\' |##| \', \',\') ))';
            }
        }

        //echo $listQuery;
        //exit;
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
        //$listQuery=str_replace('vtiger_visitsign.visitingorderid=vtiger_visitingorder.visitingorderid','(vtiger_visitsign.visitingorderid=vtiger_visitingorder.visitingorderid AND vtiger_visitsign.visitsigntype=\'陪同人\')',$listQuery);

        //echo $listQuery.'<br>';die();
        //$listQuery.='GROUP BY vtiger_visitingorder.visitingorderid';//加上分组来去重
        $listResult = $db->pquery($listQuery, array());
        //return $db->num_rows($listResult);
        return $db->query_result($listResult,0,'counts');
    }

}