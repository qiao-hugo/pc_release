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
class ServiceComplaints_ListView_Model extends Vtiger_ListView_Model {
	//根据参数显示数据
	public function getListViewEntries($pagingModel) {
        $db = PearDatabase::getInstance();

        $moduleName = 'ServiceComplaints';
        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');


        if(empty($orderBy) && empty($sortOrder) && $moduleName != "Users"){
            $orderBy = 'vtiger_servicecomplaints.servicecomplaintsid';
            $sortOrder = 'DESC';
        }

		//$listQuery= 'SELECT vtiger_servicecomplaints.related_to, vtiger_servicecomplaints.productid, vtiger_servicecomplaints.complaitype, vtiger_servicecomplaints.complainantid, vtiger_servicecomplaints.handleid, vtiger_servicecomplaints.handletime, vtiger_servicecomplaints.refundmoney, vtiger_servicecomplaints.refundstatus, vtiger_servicecomplaints.createid, vtiger_servicecomplaints.complaicontent, vtiger_servicecomplaints.improvementadvise, vtiger_servicecomplaints.personalinsight, vtiger_servicecomplaints.disposestatus, vtiger_servicecomplaints.disposeresult, vtiger_servicecomplaints.file, vtiger_servicecomplaints.servicecomplaintsid,vtiger_servicecomplaints.servicecomplaintsid FROM vtiger_servicecomplaints LEFT JOIN vtiger_users AS vtiger_userscomplainantid ON vtiger_servicecomplaints.complainantid = vtiger_userscomplainantid.id LEFT JOIN vtiger_users AS vtiger_usershandleid ON vtiger_servicecomplaints.handleid = vtiger_usershandleid.id LEFT JOIN vtiger_users AS vtiger_userscreateid ON vtiger_servicecomplaints.createid = vtiger_userscreateid.id WHERE 1=1 AND vtiger_servicecomplaints.servicecomplaintsid > 0';
        $listQuery="
            SELECT
                        vtiger_servicecomplaints.servicecomplaintsid,
                        vtiger_account.accountid as related_to_reference,
                        (select accountname from vtiger_account where vtiger_account.accountid=vtiger_servicecomplaints.related_to) AS related_to,
                        (select productname from vtiger_products where vtiger_products.productid=vtiger_servicecomplaints.productid) AS productid,
                        vtiger_servicecomplaints.complaitype,
                    IFNULL((SELECT CONCAT(last_name,'[',IFNULL((SELECT	departmentname	FROM vtiger_departments	WHERE	departmentid = (SELECT	departmentid	FROM	vtiger_user2department WHERE	userid = vtiger_users.id LIMIT 1)),''),']',(IF (`status` = 'Active','','[离职]'))) AS last_name	FROM vtiger_users	WHERE	vtiger_servicecomplaints.complainantid = vtiger_users.id),'--') AS complainantid,
                    IFNULL((SELECT CONCAT(last_name,'[',IFNULL((SELECT	departmentname	FROM vtiger_departments	WHERE	departmentid = (SELECT	departmentid	FROM	vtiger_user2department WHERE	userid = vtiger_users.id LIMIT 1)),''),']',(IF (`status` = 'Active','','[离职]'))) AS last_name	FROM vtiger_users	WHERE	vtiger_servicecomplaints.handleid = vtiger_users.id),'--') AS handleid,


                        vtiger_servicecomplaints.handletime,
                        vtiger_servicecomplaints.refundmoney,
                        vtiger_servicecomplaints.refundstatus,
                    IFNULL((SELECT CONCAT(last_name,'[',IFNULL((SELECT	departmentname	FROM vtiger_departments	WHERE	departmentid = (SELECT	departmentid	FROM	vtiger_user2department WHERE	userid = vtiger_users.id LIMIT 1)),''),']',(IF (`status` = 'Active','','[离职]'))) AS last_name	FROM vtiger_users	WHERE	vtiger_servicecomplaints.createid = vtiger_users.id),'--') AS createid,


                        vtiger_servicecomplaints.complaicontent,
                        vtiger_servicecomplaints.improvementadvise,
                        vtiger_servicecomplaints.personalinsight,
                        vtiger_servicecomplaints.disposestatus,
                        vtiger_servicecomplaints.disposeresult,
                        vtiger_servicecomplaints.file
                    FROM
                        vtiger_servicecomplaints
                    LEFT JOIN vtiger_users AS vtiger_userscomplainantid ON vtiger_servicecomplaints.complainantid = vtiger_userscomplainantid.id
                    LEFT JOIN vtiger_users AS vtiger_usershandleid ON vtiger_servicecomplaints.handleid = vtiger_usershandleid.id
                    LEFT JOIN vtiger_users AS vtiger_userscreateid ON vtiger_servicecomplaints.createid = vtiger_userscreateid.id
                    LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_servicecomplaints.related_to
                    LEFT JOIN vtiger_products ON vtiger_products.productid = vtiger_servicecomplaints.productid
                    WHERE
                        1 = 1
                    AND vtiger_servicecomplaints.servicecomplaintsid > 0
        ";
        //$listQuery=$this->getTableSQL();
        //$listQuery="SELECT * FROM ($listQuery) vtiger_servicecomplaints WHERE 1=1";
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
//echo $listQuery;die();
        $listResult = $db->pquery($listQuery, array());

        $listViewRecordModels = array();
        while($rawData=$db->fetch_array($listResult)) {
            if($rawData['disposestatus']==1){
                $rawData['disposestatus']='已处理';
            }else{
                $rawData['disposestatus']='未处理';
            }
            $rawData['id'] = $rawData['servicecomplaintsid'];
            $listViewRecordModels[$rawData['servicecomplaintsid']] = $rawData;
        }
        return $listViewRecordModels;
	}
    public function getUserWhere(){
        $searchDepartment = $_REQUEST['department'];//部门
        //$where=getAccessibleUsers('ServiceMaintenance','List',true);
        $userid=getDepartmentUser($searchDepartment);
        $listQuery = '';
//         if(!empty($searchDepartment)){
//             if(!empty($where)&&$where!='1=1'){
//                 $where=array_intersect($where,$userid);
//             }else{
//                 $where=$userid;
//             }
//             $where=implode(',',$where);
//             $listQuery .= ' and vtiger_servicemaintenance.serviceid'.$where;
//         }


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
        $listQuery=$this->getTableSQL();
        $listQuery="SELECT count(1) as counts FROM ($listQuery) vtiger_servicecomplaints  LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_servicecomplaints.related_to LEFT  join vtiger_products ON vtiger_products.productid = vtiger_servicecomplaints.productid WHERE 1=1";
        $this->getSearchWhere();
        $listQuery.=$this->getUserWhere();

        $queryGenerator = $this->get('query_generator');
        $searchwhere=$queryGenerator->getSearchWhere();
        if(!empty($searchwhere)){
            $listQuery.=' and '.$searchwhere;
        }
        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }
    public function getTableSQL(){
        $sql="SELECT
                        vtiger_servicecomplaints.servicecomplaintsid,
                        (select accountname from vtiger_account where vtiger_account.accountid=vtiger_servicecomplaints.related_to) AS related_to,
                        (select productname from vtiger_products where vtiger_products.productid=vtiger_servicecomplaints.productid) AS productid,
                        vtiger_servicecomplaints.complaitype,
                    IFNULL((SELECT CONCAT(last_name,'[',IFNULL((SELECT	departmentname	FROM vtiger_departments	WHERE	departmentid = (SELECT	departmentid	FROM	vtiger_user2department WHERE	userid = vtiger_users.id LIMIT 1)),''),']',(IF (`status` = 'Active','','[离职]'))) AS last_name	FROM vtiger_users	WHERE	vtiger_servicecomplaints.complainantid = vtiger_users.id),'--') AS complainantid,
                    IFNULL((SELECT CONCAT(last_name,'[',IFNULL((SELECT	departmentname	FROM vtiger_departments	WHERE	departmentid = (SELECT	departmentid	FROM	vtiger_user2department WHERE	userid = vtiger_users.id LIMIT 1)),''),']',(IF (`status` = 'Active','','[离职]'))) AS last_name	FROM vtiger_users	WHERE	vtiger_servicecomplaints.handleid = vtiger_users.id),'--') AS handleid,


                        vtiger_servicecomplaints.handletime,
                        vtiger_servicecomplaints.refundmoney,
                        vtiger_servicecomplaints.refundstatus,
                    IFNULL((SELECT CONCAT(last_name,'[',IFNULL((SELECT	departmentname	FROM vtiger_departments	WHERE	departmentid = (SELECT	departmentid	FROM	vtiger_user2department WHERE	userid = vtiger_users.id LIMIT 1)),''),']',(IF (`status` = 'Active','','[离职]'))) AS last_name	FROM vtiger_users	WHERE	vtiger_servicecomplaints.createid = vtiger_users.id),'--') AS createid,


                        vtiger_servicecomplaints.complaicontent,
                        vtiger_servicecomplaints.improvementadvise,
                        vtiger_servicecomplaints.personalinsight,
                        vtiger_servicecomplaints.disposestatus,
                        vtiger_servicecomplaints.disposeresult,
                        vtiger_servicecomplaints.file
                    FROM
                        vtiger_servicecomplaints
                    LEFT JOIN vtiger_users AS vtiger_userscomplainantid ON vtiger_servicecomplaints.complainantid = vtiger_userscomplainantid.id
                    LEFT JOIN vtiger_users AS vtiger_usershandleid ON vtiger_servicecomplaints.handleid = vtiger_usershandleid.id
                    LEFT JOIN vtiger_users AS vtiger_userscreateid ON vtiger_servicecomplaints.createid = vtiger_userscreateid.id
                    LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_servicecomplaints.related_to
                    LEFT JOIN vtiger_products ON vtiger_products.productid = vtiger_servicecomplaints.productid
                    WHERE
                        1 = 1
                    AND vtiger_servicecomplaints.servicecomplaintsid > 0";
        return $sql;
    }
}
