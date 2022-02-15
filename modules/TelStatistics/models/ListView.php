<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class TelStatistics_ListView_Model extends Vtiger_ListView_Model {
    //根据参数显示数据   #移动crm模拟$request请求---2015-12-16 罗志坚
	public function getListViewEntries($pagingModel,$request=array()) {
		$db = PearDatabase::getInstance();
		$moduleName ='TelStatistics';

		$orderBy = $this->getForSql('orderby');
		$sortOrder = $this->getForSql('sortorder');
		if(empty($orderBy) && empty($sortOrder)){
			$orderBy = 'telstatisticsid';
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
      	if($_REQUEST['public']=='quit'){
      	    $listQuery = str_replace(" FROM vtiger_telstatistics "," FROM vtiger_telstatistics left join vtiger_users on vtiger_users.id=vtiger_telstatistics.useid ",$listQuery);
      	    $listQuery = str_replace(" WHERE 1=1 "," WHERE 1=1 and vtiger_users.status='Inactive' ",$listQuery);
      	}
		$listResult = $db->pquery($listQuery, array());


		$index = 0;
		while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['telstatisticsid'];
			$listViewRecordModels[$rawData['telstatisticsid']] = $rawData;
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
        $searchDepartment = $_REQUEST['department'];
        $where=getAccessibleUsers('Accounts','List');
        if(!empty($searchDepartment)&&$searchDepartment!='H1'){  //20150525 柳林刚 加入
            require('crmcache/departmentanduserinfo.php');
            $deparr=$departmentinfo[$searchDepartment];
            if($where!='1=1'){
                $listQuery .=' AND (vtiger_telstatistics.useid'.$where.' OR vtiger_telstatistics.smownerid='.$current_user->id.')';
            }
            if(!empty($deparr)){
                $listQuery.=' AND vtiger_telstatistics.departmentid IN(\''.implode("','",$deparr).'\')';
            }
        }else{
            if($where!='1=1'){
                $listQuery .=' AND (vtiger_telstatistics.useid'.$where.' OR vtiger_telstatistics.smownerid='.$current_user->id.')';
            }
        }

        if($_REQUEST['public']=='quit'){
            $listQuery .= " and vtiger_users.status='Inactive'";
        }
        return $listQuery;
    }
    public function getListViewCount() {
        $db = PearDatabase::getInstance();
        $queryGenerator = $this->get('query_generator');
        $where=$this->getUserWhere();
        $queryGenerator->addUserWhere($where);
        $listQuery =  $queryGenerator->getQueryCount();
        if($_REQUEST['public']=='quit'){
            $listQuery = str_replace(" FROM vtiger_telstatistics "," FROM vtiger_telstatistics left join vtiger_users on vtiger_users.id=vtiger_telstatistics.useid ",$listQuery);
        }
//        echo $listQuery;die;
        $listResult = $db->pquery($listQuery, array());
        //return $db->num_rows($listResult);
        return $db->query_result($listResult,0,'counts');
    }

}