<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class VendorProduct_ListView_Model extends Vtiger_ListView_Model {
    /**
     * 模块列表页面显示链接 保留新增 Edit By Joe @20150511
     * @param <Array> $linkParams
     * @return <Array> - Associate array of Link Type to List of Vtiger_Link_Model instances
     */
    public function getListViewLinks($linkParams) {
        $links=array();
        return $links;
    }
	
	//根据参数显示数据   #移动crm模拟$request请求---2015-12-16 罗志坚
	public function getListViewEntries($pagingModel,$request=array()) {
		$db = PearDatabase::getInstance();
		$moduleName ='ProductProvider';

		
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

			$orderBy = 'vendorsrebateid';

			$sortOrder = 'DESC';
		}
		$this->getSearchWhere();
        $listQuery = $this->getQuery();
        
        $listQuery.=$this->getUserWhere();
        global $current_user;
        $listQuery=str_replace('ON vtiger_vendorsrebate.vendorsrebateid = vtiger_crmentity.crmid','ON vtiger_vendorsrebate.vendorid = vtiger_crmentity.crmid',$listQuery);

		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();

        $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;
		
		$viewid = ListViewSession::getCurrentView($moduleName);
	
		ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,
	
		$listQuery .= " LIMIT $startIndex,".($pageLimit);


        //echo $listQuery;//die();
		//echo $listQuery;die;
		$listResult = $db->pquery($listQuery, array());


		$index = 0;
		while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['vendorsrebateid'];
			$listViewRecordModels[$rawData['vendorsrebateid']] = $rawData;
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
				$temp[$fields['fieldlabel']]=$fields;

            }
            
            return $temp;
        }
        return $queryGenerator->getFocus()->list_fields_name;
        
    }
    public function getUserWhere(){
       global $current_user;
        $searchDepartment = $_REQUEST['department'];
        $sourceModule = $this->get('src_module');
        $listQuery=' ';

        if (!empty($searchDepartment) && $searchDepartment != 'H1') {
            $userid = getDepartmentUser($searchDepartment);
            $where = getAccessibleUsers('Vendors', 'List', true);
            if ($where != '1=1') {
                $where = array_intersect($where, $userid);
            } else {
                $where = $userid;
            }
            $where=!empty($where)?$where:array(-1);
            $listQuery .= ' and vtiger_crmentity.crmid in (' . implode(',', $where) . ')';
        }else{
            $where = getAccessibleUsers('Vendors', 'List');
            if ($where != '1=1') {
                $listQuery .= ' and vtiger_crmentity.crmid ' . $where . ' ';
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
        $listQuery=str_replace('ON vtiger_vendorsrebate.vendorsrebateid = vtiger_crmentity.crmid','ON vtiger_vendorsrebate.vendorid = vtiger_crmentity.crmid',$listQuery);
        //echo $listQuery.'<br>';die();
        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }

}