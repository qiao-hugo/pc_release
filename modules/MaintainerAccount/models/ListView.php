<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class MaintainerAccount_ListView_Model extends Vtiger_ListView_Model {
	public function getListViewMassActions($linkParams) {

	}
    function getListViewLinks($linkParams) {
        return false;
    }
	//根据参数显示数据
	public function getListViewEntries($pagingModel) {
		$db = PearDatabase::getInstance();
		$moduleName ='MaintainerAccount';
		$orderBy = $this->getForSql('orderby');
		$sortOrder = $this->getForSql('sortorder');

        global $current_user;
            $this->getSearchWhere();
            $listQuery = MaintainerAccount_Record_Model::getlistviewsql();
            $queryGenerator = $this->get('query_generator');
            $searchwhere=$queryGenerator->getSearchWhere();
            if(!empty($searchwhere)){
                $listQuery.=' and '.$searchwhere;
            }
            $listQuery.=$this->getUserWhere();
        if($_REQUEST['filter']=='ExpirationOfTheMonth'){
            $listQuery.=" AND vtiger_servicecontracts.due_date>='".date("Y-m-d",strtotime("-1 months"))."' AND vtiger_servicecontracts.due_date<='".date("Y-m-d")."'";
        }
		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();
        if(empty($orderBy) && empty($sortOrder)){
            $orderBy = 'vtiger_account.mtime';
            $sortOrder = 'DESC';
        }else{
            $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;
        }

		$viewid = ListViewSession::getCurrentView($moduleName);
	
		ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,
		$listQuery .= " LIMIT $startIndex,".($pageLimit);
        //echo $listQuery;die;
        $listResult = $db->pquery($listQuery, array());
		$index = 0;
        while ($rawData = $db->fetch_array($listResult)) {
            $listViewRecordModels[] = $rawData;
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
       global $current_user;
        $listQuery=' ';
        $where=getAccessibleUsers('Double','List',false);//权限控制；
        $userid=getDepartmentUser('H3'); //中小体系；
        if($where!='1=1'){
            $listQuery .='AND serviceid '.$where ." AND smownerid in (".implode(',',$userid).")";
        }else{
            $listQuery .= "AND smownerid in (".implode(',',$userid).")";
        }
       return $listQuery;
    }

    public function  getDistinctAcc(){
        $db = PearDatabase::getInstance();
        $listQuery =  MaintainerAccount_Record_Model::getAccSql();
        $this->getSearchWhere();
        $listQuery.=$this->getUserWhere();

        $queryGenerator = $this->get('query_generator');
        $searchwhere=$queryGenerator->getSearchWhere();
        if(!empty($searchwhere)){
            $listQuery.=' and '.$searchwhere;
        }
        $listQuery .=' GROUP BY sc_related_to';
        $listResult = $db->pquery($listQuery, array());
        return $db->num_rows($listResult);
    }

    public function getListViewCount() {
        $db = PearDatabase::getInstance();
        $listQuery =  MaintainerAccount_Record_Model::getlistviewsql();
        $this->getSearchWhere();
        $listQuery.=$this->getUserWhere();

        $queryGenerator = $this->get('query_generator');
        $searchwhere=$queryGenerator->getSearchWhere();
        if(!empty($searchwhere)){
            $listQuery.=' and '.$searchwhere;
        }
        $listResult = $db->pquery($listQuery, array());
        return $db->num_rows($listResult);
    }

}