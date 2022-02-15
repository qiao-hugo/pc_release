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
 * ServiceMaintenance ListView Model Class
 */
class IronAccount_ListView_Model extends Vtiger_ListView_Model {
	//根据参数显示数据
	public function getListViewEntries($pagingModel) {
        $db = PearDatabase::getInstance();

        $moduleName = 'IronAccount';
        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');


        if(empty($orderBy) && empty($sortOrder) && $moduleName != "Users"){
            $orderBy = 'ironid';
            $sortOrder = 'DESC';
        }



      /*  $listQuery="SELECT
                    (vtiger_account.accountname) AS accountid,
                    vtiger_ironaccount.accountid AS accountid_reference,
                    vtiger_ironaccount.ironid,
                    (SELECT last_name FROM vtiger_users WHERE id = vtiger_servicecomments.serviceid ) AS serviceid,
                    (SELECT last_name FROM vtiger_users WHERE id = vtiger_ironaccount.operater ) AS operater,
                    (SELECT accountrank 	FROM vtiger_account WHERE vtiger_account.accountid = vtiger_ironaccount.accountid ) AS accountrank,
                    (SELECT departmentname 	FROM vtiger_departments WHERE 	vtiger_departments.departmentid = ( SELECT departmentid FROM 	vtiger_user2department 	WHERE vtiger_user2department.userid = ( SELECT vtiger_crmentity.smownerid FROM vtiger_crmentity WHERE vtiger_crmentity.crmid = vtiger_ironaccount.accountid AND vtiger_crmentity.deleted = 0))) AS departmentid,
                    vtiger_ironaccount.addtime,
                    vtiger_ironaccount.lasttime
                FROM
                    vtiger_ironaccount
                LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_ironaccount.accountid
                LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_ironaccount.accountid
                LEFT JOIN vtiger_servicecomments ON (
                    vtiger_ironaccount.accountid = vtiger_servicecomments.related_to
                    AND vtiger_servicecomments.assigntype = 'accountby'
                )
                WHERE
                    1 = 1";*/

      //  $this->getSearchWhere();

        $listQuery=$this->getQuery();
        //echo $listQuery;
        $this->getSearchWhere();

        $listQuery.=$this->getUserWhere();

        $queryGenerator = $this->get('query_generator');
        $searchwhere=$queryGenerator->getSearchWhere();
        if(!empty($searchwhere)){
            $listQuery.=' and '.$searchwhere;
        }
        $listQuery.=' order by '.$orderBy.' '.$sortOrder;
		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();

		$viewid = ListViewSession::getCurrentView($moduleName);

		ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,

		$listQuery .= " LIMIT $startIndex,".($pageLimit);
//echo $listQuery;
        $listResult = $db->pquery($listQuery, array());
       // print_r($listResult);die();
        $listViewRecordModels = array();
        while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['ironid'];
            $listViewRecordModels[$rawData['ironid']] = $rawData;
        }

        return $listViewRecordModels;
	}

    public function getUserWhere(){
        $searchDepartment = $_REQUEST['department'];//部门

        $userid=getDepartmentUser($searchDepartment);
        $listQuery = '';


        $where=getAccessibleUsers();
        if($where!='1=1'){
        	$listQuery .= ' and vtiger_ironaccount.accountid'.$where;
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
       /* $db = PearDatabase::getInstance();
        $listQuery="SELECT
                    count(1) as counts
                    FROM
                        vtiger_ironaccount
                    LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_ironaccount.accountid
                    LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_ironaccount.accountid
                    LEFT JOIN vtiger_servicecomments ON (
                        vtiger_ironaccount.accountid = vtiger_servicecomments.related_to
                        AND vtiger_servicecomments.assigntype = 'accountby'
                    )
                    WHERE
                        1 = 1";
      //  $this->getSearchWhere();
        $listQuery.=$this->getUserWhere();

        $queryGenerator = $this->get('query_generator');
        $searchwhere=$queryGenerator->getSearchWhere();
        if(!empty($searchwhere)){
            $listQuery.=' and '.$searchwhere;
        }//end

        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');*/
        $db = PearDatabase::getInstance();
        $queryGenerator = $this->get('query_generator');

        $where=$this->getUserWhere();

        $queryGenerator->addUserWhere($where);
        $listQuery =  $queryGenerator->getQueryCount();
//echo $listQuery;die();
        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }


}
