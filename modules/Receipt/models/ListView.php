<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Receipt_ListView_Model extends Vtiger_ListView_Model {

	public function getListViewEntries($pagingModel,$request=array()) {
		$db = PearDatabase::getInstance();
		$moduleName ='Receipt';
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

		if(empty($orderBy) && empty($sortOrder)){

			$orderBy = 'receiptid';

			$sortOrder = 'DESC';
		}
		$this->getSearchWhere();
        $listQuery = $this->getQuery();

        $listQuery.=$this->getUserWhere();
        global $current_user;

		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();

        $listQuery .= ' AND vtiger_receipt.deleted=0 ';

        $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;

		$viewid = ListViewSession::getCurrentView($moduleName);

		ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,

		$listQuery .= " LIMIT $startIndex,".($pageLimit);
		$listResult = $db->pquery($listQuery, array());

		$index = 0;
		while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['receiptid'];
			$listViewRecordModels[$rawData['receiptid']] = $rawData;
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
        $sourceModule = $this->get('src_module');
        $listQuery=' ';

        if(!empty($searchDepartment)&&$searchDepartment!='H1'){
            $userid=getDepartmentUser($searchDepartment);
            $where=getAccessibleUsers('Receipt','List', true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $where=!empty($where)?$where:array(-1);
            $listQuery .= ' and vtiger_crmentity.smcreatorid in ('.implode(',',$where).')';
        }else{
            $where=getAccessibleUsers();
            if($where!='1=1'){

                $listQuery .= ' and vtiger_crmentity.smcreatorid '.$where . ' ';
            }
        }
        return $listQuery;
    }
    public function getListViewCount() {
        $db = PearDatabase::getInstance();
        $queryGenerator = $this->get('query_generator');
        //搜索条件
        //$this->getSearchWhere();
        //用户条件
        $where=$this->getUserWhere();
        //$where.= ' AND accountname is NOT NULL';
        $queryGenerator->addUserWhere($where);
        $listQuery =  $queryGenerator->getQueryCount();
        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }

}
