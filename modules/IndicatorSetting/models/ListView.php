<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class IndicatorSetting_ListView_Model extends Vtiger_ListView_Model {
	public function getListViewEntries($pagingModel,$request=array()) {
		$db = PearDatabase::getInstance();
		$moduleName ='IndicatorSetting';

		$orderBy = $this->getForSql('orderby');
		$sortOrder = $this->getForSql('sortorder');
		if(empty($orderBy) && empty($sortOrder)){
			$orderBy = 'id';
			$sortOrder = 'DESC';
		}
		$this->getSearchWhere();
        $listQuery = $this->getQuery();
        $listQuery.=$this->getUserWhere();
        global $current_user;
        $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;

		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();
      	$listQuery .= " LIMIT $startIndex,".($pageLimit);

		$listResult = $db->pquery($listQuery, array());

		$index = 0;
		while($rawData=$db->fetch_array($listResult)) {
			$listViewRecordModels[$rawData['id']] = $rawData;
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
        $listQuery='';
        $searchDepartment = $_REQUEST['department']?$_REQUEST['department'] : $current_user->departmentid;
        if(empty($_REQUEST['department']) && $current_user->roleid=='H148'){
            $searchDepartment = 'H1';
        }
        $where=getAccessibleUsers('IndicatorSetting','List');
        if(!empty($searchDepartment)&&$searchDepartment!='H1'){  //20150525 柳林刚 加入
            require('crmcache/departmentanduserinfo.php');

            if(in_array($_REQUEST['department'],$departmentinfo[$current_user->departmentid])){
                $deparr=$departmentinfo[$_REQUEST['department']];
            }else{
                $deparr=$departmentinfo[$current_user->departmentid];
            }

            if(!empty($deparr)){
                $listQuery.=' AND vtiger_indicatorsetting.departmentid IN(\''.implode("','",$deparr).'\')';
            }

            if($where!='1=1'){
                $listQuery .=' OR (vtiger_indicatorsetting.createdid'.$where.')';
            }
        }else{
            if($where!='1=1'){
                $listQuery .=' AND (vtiger_telstatistics.useid'.$where.')';
            }
        }
        return $listQuery;
    }
    public function getListViewCount() {
        $db = PearDatabase::getInstance();
        $queryGenerator = $this->get('query_generator');
        $where=$this->getUserWhere();
        $queryGenerator->addUserWhere($where);
        $listQuery =  $queryGenerator->getQueryCount();
        $listResult = $db->pquery($listQuery, array());
        //return $db->num_rows($listResult);
        return $db->query_result($listResult,0,'counts');
    }

}
