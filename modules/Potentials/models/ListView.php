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
 * ServiceComments ListView Model Class
 */
class Potentials_ListView_Model extends Vtiger_ListView_Model {
	//根据参数显示数据
	public function getListViewEntries($pagingModel) {
        $db = PearDatabase::getInstance();
		
        $moduleName = 'Potentials';
        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');


        if(empty($orderBy) && empty($sortOrder) && $moduleName != "Users"){
            $orderBy = 'vtiger_potential.potentialid';
            $sortOrder = 'DESC';
        }
       /*  $listQuery="SELECT
						vtiger_potential.potentialname,
						vtiger_potential.potential_no,
						(SELECT	accountname	FROM	vtiger_account WHERE vtiger_account.accountid = vtiger_potential.related_to) AS related_to,
						vtiger_potential.closingdate,
						vtiger_potential.potentialtype,
						vtiger_potential.leadsource,
						vtiger_potential.sales_stage,
						IFNULL((SELECT CONCAT(last_name,'[',IFNULL((SELECT	departmentname	FROM vtiger_departments	WHERE	departmentid = (SELECT	departmentid	FROM	vtiger_user2department WHERE	userid = vtiger_users.id LIMIT 1)),''),']',(IF (`status` = 'Active','','[离职]'))) AS last_name	FROM vtiger_users	WHERE	vtiger_crmentity.smownerid = vtiger_users.id),'--') AS smownerid,
						concat(format(vtiger_potential.probability,2),'%') AS probability,
						(SELECT	createdtime	FROM	vtiger_crmentity WHERE vtiger_crmentity.crmid = vtiger_potential.potentialid AND vtiger_crmentity.deleted = 0) AS createdtime,
						(SELECT	modifiedtime FROM vtiger_crmentity WHERE vtiger_crmentity.crmid = vtiger_potential.potentialid AND vtiger_crmentity.deleted = 0) AS modifiedtime,
						IFNULL((SELECT CONCAT(last_name,'[',IFNULL((SELECT	departmentname	FROM vtiger_departments	WHERE	departmentid = (SELECT	departmentid	FROM	vtiger_user2department WHERE	userid = vtiger_users.id LIMIT 1)),''),']',(IF (`status` = 'Active','','[离职]'))) AS last_name	FROM vtiger_users	WHERE	vtiger_crmentity.modifiedby = vtiger_users.id),'--') AS  modifiedby,
						(SELECT	description	FROM	vtiger_crmentity WHERE vtiger_crmentity.crmid = vtiger_potential.potentialid	AND vtiger_crmentity.deleted = 0) AS description,
						format(vtiger_potential.forecast_amount,2) AS forecast_amount,
						vtiger_potential.contact_id,
						vtiger_potential.rejectiontype,
						vtiger_potential.potentialid
					FROM
						vtiger_potential
					INNER JOIN vtiger_crmentity ON vtiger_potential.potentialid = vtiger_crmentity.crmid
					LEFT JOIN vtiger_users AS vtiger_usersassigned_user_id ON vtiger_crmentity.smownerid = vtiger_usersassigned_user_id.id
					LEFT JOIN vtiger_users AS vtiger_usersmodifiedby ON vtiger_crmentity.modifiedby = vtiger_usersmodifiedby.id
					WHERE
						vtiger_crmentity.deleted = 0
					AND vtiger_potential.potentialid > 0"; */
        $this->getSearchWhere();
        $listQuery = $this->getQuery();
        $listQuery.=$this->getUserWhere();

        $queryGenerator = $this->get('query_generator');
        $searchwhere=$queryGenerator->getSearchWhere();
        if(!empty($searchwhere)){
            $listQuery.=' and '.$searchwhere;
        }
		//按最后跟进日期排序(降序)
		$listQuery.=' GROUP BY  vtiger_potential.potentialid   order by '.$orderBy.' '.$sortOrder;
		//echo $listQuery;
		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();
		
	
		$viewid = ListViewSession::getCurrentView($moduleName);
	
		ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,

        $listQuery .= " LIMIT $startIndex,".($pageLimit);

		$listResult = $db->pquery($listQuery, array());

		$listViewRecordModels = array();
        while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['potentialid'];
            $listViewRecordModels[$rawData['potentialid']] = $rawData;
        }
        //echo $listQuery;
        
        return $listViewRecordModels;
	}
    public function getUserWhere(){
        $searchDepartment = $_REQUEST['department'];//部门
       
        //exit;
        //$where=getAccessibleUsers('ServiceComments','List',true);
        $userid=getDepartmentUser($searchDepartment);
        
       	$listQuery = "";
        //非管理员
//         if(!empty($searchDepartment)){
//             if(!empty($where)&&$where!='1=1'){
//                 $where=$userid;
//             }else{
//                 $where=array_intersect($where,$userid);
//             }
//             $where=implode(',',$where);
//             $listQuery .= ' and vtiger_servicecomments.serviceid'.$where;
//         }
        if(!empty($searchDepartment)&&$searchDepartment!='H1'){  //20150427 young 取消默认的H1验证
            $userid=getDepartmentUser($searchDepartment);
            $where=getAccessibleUsers('Accounts','List',true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $where=!empty($where)?$where:array(-1);
            $listQuery .= ' and vtiger_crmentity.smownerid in ('.implode(',',$where).')';
        }else{
            //getDepartment
            $where=getAccessibleUsers();

            if($where!='1=1'){
                $listQuery .= ' and vtiger_crmentity.smownerid '.$where;
            }
        }
        /* $where=getAccessibleUsers();
        if($where!='1=1'){
        	$listQuery .= ' and vtiger_crmentity.smownerid '.$where;
            //当客户掉到公海后列表不显示
			$listQuery .=' and CASE WHEN vtiger_potential.related_to>0 THEN  EXISTS ( SELECT accountid FROM vtiger_account LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_account.accountid WHERE vtiger_account.accountid = vtiger_potential.related_to AND vtiger_account.accountcategory=0 AND vtiger_crmentity.deleted = 0 AND vtiger_crmentity.smownerid '.$where.') ELSE 1=1 END ';	
        	
        } */
        
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
        $queryGenerator = $this->get('query_generator');
//print_r(debug_backtrace(0));
        //搜索条件
        //$this->getSearchWhere();
        //用户条件
        $where=$this->getUserWhere();
        //$where.= ' AND accountname is NOT NULL';
        $queryGenerator->addUserWhere($where);
        $listQuery =  $queryGenerator->getQueryCount();
        $listQuery.=' GROUP BY  vtiger_potential.potentialid ';
        $listResult = $db->pquery($listQuery, array());
        return $db->num_rows($listResult);
    }
}
