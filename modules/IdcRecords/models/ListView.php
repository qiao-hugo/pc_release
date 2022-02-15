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
class IdcRecords_ListView_Model extends Vtiger_ListView_Model {
	//根据参数显示数据
	public function getListViewEntries($pagingModel) {
        $db = PearDatabase::getInstance();

        $moduleName = 'IdcRecords';
        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');


        if(empty($orderBy) && empty($sortOrder) && $moduleName != "Users"){
            $orderBy = 'idcrecordsid';
            $sortOrder = 'DESC';
        }


       /* $listQuery="SELECT
                (vtiger_account.accountname) AS related_to,
                vtiger_idcrecords.related_to AS related_to_reference,
                vtiger_idcrecords.salesorder_no,
                REPLACE ( vtiger_idcrecords.domainname,  ' |##| ',  '<br>' ) AS domainname,
                (SELECT CONCAT(last_name,'[',IFNULL( ( SELECT departmentname 	FROM 	vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department	WHERE	userid = vtiger_users.id	LIMIT 1)	),''	),']',(IF (`status` = 'Active',	'','[离职]'))) AS last_name
		        FROM vtiger_users WHERE vtiger_idcrecords.smownerid = vtiger_users.id) AS smownerid,
                vtiger_idcrecords.idcstate,
                vtiger_idcrecords.registeredtime,
                vtiger_idcrecords.endtime,
                vtiger_idcrecords.space,
                vtiger_idcrecords.ipaddress,
                vtiger_idcrecords.idctype,
                vtiger_idcrecords.record_no,
                vtiger_idcrecords.recordnature,
                vtiger_idcrecords.recordfrom,
                vtiger_idcrecords.recordtype,
                vtiger_idcrecords.recordportal,
                vtiger_idcrecords.recordtime,
                vtiger_idcrecords.remark,
            IF (iscompanydomainname = 1, '是',  '否' ) AS iscompanydomainname,
             vtiger_idcrecords.idcrecordsid
            FROM vtiger_idcrecords
            LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_idcrecords.related_to
            WHERE 1 = 1
        ";*/
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
            $rawData['id'] = $rawData['idcrecordsid'];
            $listViewRecordModels[$rawData['idcrecordsid']] = $rawData;
        }

        return $listViewRecordModels;
	}

    public function getUserWhere(){
        $searchDepartment = $_REQUEST['department'];//部门

        $userid=getDepartmentUser($searchDepartment);
        $listQuery = '';


        $where=getAccessibleUsers();
        if($where!='1=1'){
        	$listQuery .= ' and vtiger_idcrecords.idcrecordsid'.$where;
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

       $listQuery="SELECT count(1) as counts FROM `vtiger_idcrecords` LEFT  JOIN vtiger_account ON vtiger_account.accountid = vtiger_idcrecords.related_to WHERE 1=1";
        $this->getSearchWhere();
        $listQuery.=$this->getUserWhere();

        $queryGenerator = $this->get('query_generator');
        $searchwhere=$queryGenerator->getSearchWhere();
        if(!empty($searchwhere)){
            $listQuery.=' and '.$searchwhere;
        }//end

        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }
    public function getTable(){
        $sql="";
    }

}
