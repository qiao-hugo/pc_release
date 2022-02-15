<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ServiceAssignRule_ListView_Model extends Vtiger_ListView_Model {
	
	
	//根据参数显示数据
	public function getListViewEntries($pagingModel) {
		
		$db = PearDatabase::getInstance();
		$moduleName ='ServiceAssignRule';


		$orderBy = $this->getForSql('orderby');
		$sortOrder = $this->getForSql('sortorder');

		//List view will be displayed on recently created/modified records
		//列表视图将显示最近的创建修改记录  ---做什么用处
		if(empty($orderBy) && empty($sortOrder)){

			//$orderBy = 'vtiger_account.accountid';
            $orderBy = 'vtiger_serviceassignrule.serviceassignruleid';
			$sortOrder = 'DESC';
		}
        $listQuery1 = "SELECT
                        vtiger_serviceassignrule.serviceassignruleid,
                        vtiger_serviceassignrule.assigntype,
                        if(vtiger_serviceassignrule.assigntype='productby','--',(select accountname from vtiger_account where vtiger_account.accountid=vtiger_serviceassignrule.related_to)) as related_to,
                        if(vtiger_serviceassignrule.assigntype='productby','--',(select departmentname from vtiger_departments where vtiger_departments.departmentid=vtiger_serviceassignrule.departmentid)) as departmentid,
                         if(vtiger_serviceassignrule.assigntype='accountby','--',IFNULL((select productname from vtiger_products where productid=vtiger_serviceassignrule.productid),'--')) as productid,
                        IFNULL((SELECT GROUP_CONCAT(last_name,'[',IFNULL((SELECT departmentname	FROM	vtiger_departments	WHERE	departmentid = (SELECT departmentid	FROM vtiger_user2department	WHERE	userid = vtiger_users.id
								LIMIT 1)),''),']',(IF (`status` = 'Active',	'','[离职]'))) AS last_name FROM vtiger_users WHERE FIND_IN_SET(vtiger_users.id,REPLACE(vtiger_serviceassignrule.ownerid,' |##| ',',')) ),'--') AS ownerid,
                         IFNULL((SELECT CONCAT(last_name,'[',IFNULL((SELECT departmentname	FROM	vtiger_departments	WHERE	departmentid = (SELECT departmentid	FROM vtiger_user2department	WHERE	userid = vtiger_users.id
								LIMIT 1)),''),']',(IF (`status` = 'Active',	'','[离职]'))) AS last_name FROM vtiger_users WHERE vtiger_serviceassignrule.serviceid = vtiger_users.id ),'--') AS serviceid,
                         IFNULL((SELECT CONCAT(last_name,'[',IFNULL((SELECT departmentname	FROM	vtiger_departments	WHERE	departmentid = (SELECT departmentid	FROM vtiger_user2department	WHERE	userid = vtiger_users.id
								LIMIT 1)),''),']',(IF (`status` = 'Active',	'','[离职]'))) AS last_name FROM vtiger_users WHERE vtiger_serviceassignrule.oldserviceid = vtiger_users.id ),'--') AS oldserviceid,
                         IFNULL((SELECT CONCAT(last_name,'[',IFNULL((SELECT departmentname	FROM	vtiger_departments	WHERE	departmentid = (SELECT departmentid	FROM vtiger_user2department	WHERE	userid = vtiger_users.id
								LIMIT 1)),''),']',(IF (`status` = 'Active',	'','[离职]'))) AS last_name FROM vtiger_users WHERE vtiger_serviceassignrule.creatorid = vtiger_users.id ),'--') AS  creatorid,
                        vtiger_serviceassignrule.createdtime,
                         IFNULL((SELECT CONCAT(last_name,'[',IFNULL((SELECT departmentname	FROM	vtiger_departments	WHERE	departmentid = (SELECT departmentid	FROM vtiger_user2department	WHERE	userid = vtiger_users.id
								LIMIT 1)),''),']',(IF (`status` = 'Active',	'','[离职]'))) AS last_name FROM vtiger_users WHERE vtiger_serviceassignrule.modifiedby = vtiger_users.id ),'--') AS modifiedby,
                        vtiger_serviceassignrule.modifiedtime,
                        vtiger_serviceassignrule.remark
                    FROM
                        vtiger_serviceassignrule
                    WHERE
                        vtiger_serviceassignrule.serviceassignruleid > 0";
       // echo $listQuery1;die();
       $listQuery = $listQuery1;
       //$listQuery=$this->getQuery();
        //是未分配客服的客户列表则走这里
       if($_REQUEST['public']=='unaccounts'){
       	$listQuery="SELECT vtiger_account.accountid AS serviceassignruleid,
							vtiger_account.accountname AS related_to,IFNULL((SELECT CONCAT(last_name,'[',IFNULL((SELECT departmentname	FROM	vtiger_departments	WHERE	departmentid = (SELECT departmentid	FROM vtiger_user2department	WHERE	userid = vtiger_users.id
								LIMIT 1)),''),']',(IF (`status` = 'Active',	'','[离职]'))) AS last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_crmentity.smownerid ),'--') AS ownerid,vtiger_account.accountrank as remark
						FROM	vtiger_account
						JOIN vtiger_crmentity ON (vtiger_account.accountid=vtiger_crmentity.crmid AND vtiger_crmentity.deleted=0)
						WHERE
							vtiger_account.accountrank IN ('iron_isv','bras_isv','silv_isv','gold_isv')
						AND
	 					NOT EXISTS (SELECT
							1
						FROM
							vtiger_servicecomments
						WHERE
							vtiger_servicecomments.related_to IS NOT NULL AND
							vtiger_servicecomments.related_to = vtiger_account.accountid
						) ";
	       	if(!empty($_REQUEST['department'])){
	       		$listQuery.=" AND vtiger_account.accountname like '%{$_REQUEST['department']}%'";
	       	}
       }
        $this->getSearchWhere();
        $listQuery.=$this->getUserWhere();
       
        global $current_user;
        $queryGenerator = $this->get('query_generator');
        $searchwhere=$queryGenerator->getSearchWhere();
        if(!empty($searchwhere)){
        	$listQuery.=' and '.$searchwhere;
        }
        

		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();
		if($_REQUEST['public']!='unaccounts'){
        	$listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;
		}else{
			$listQuery.=' ORDER BY vtiger_account.accountid DESC ';
		}
		
		$viewid = ListViewSession::getCurrentView($moduleName);
	
		ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,
		
			$listQuery .= " LIMIT $startIndex,".($pageLimit);
		

        //echo $listQuery;die();

		$listResult = $db->pquery($listQuery, array());


		$index = 0;
		while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['serviceassignruleid'];
			$listViewRecordModels[$rawData['serviceassignruleid']] = $rawData;
		}
		//echo $listQuery;
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
            	//print_r($fields);
            	if($_REQUEST['public']=='unaccounts' )
            	{	if($fields['columnname']=='related_to' || $fields['columnname']=='ownerid' || $fields['columnname']=='remark'){
	            		$temp[$fields['fieldlabel']]=$fields;
	            	}
            	}else{
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
        if($_REQUEST['public']=='products'){
        	$listQuery.=" and assigntype='productby'";
        }elseif($_REQUEST['public']=='accounts'){
        	$listQuery.=" and assigntype='accountby'";
        }
        return $listQuery;
        //return '';
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
		//单独拿出来不通过自动生成
    	if($_REQUEST['public']=='unaccounts'){
        	$listQuery="select ((SELECT count(1) as counts
						FROM	vtiger_account
						JOIN vtiger_crmentity ON (vtiger_account.accountid=vtiger_crmentity.crmid AND vtiger_crmentity.deleted=0 )
						WHERE
							accountrank IN ('iron_isv', 'bras_isv','silv_isv','gold_isv'))-(SELECT
							COUNT(DISTINCT vtiger_servicecomments.related_to)
						FROM
							vtiger_servicecomments
						LEFT JOIN vtiger_account as t ON t.accountid=vtiger_servicecomments.related_to
						WHERE accountrank IN ('iron_isv', 'bras_isv','silv_isv','gold_isv')

					)) As counts";//试了很多个只有这个执行效率高
        	if(!empty($_REQUEST['department'])){
        		$listQuery="SELECT count(1) as counts
						FROM	vtiger_account
						JOIN vtiger_crmentity ON (vtiger_account.accountid=vtiger_crmentity.crmid AND vtiger_crmentity.deleted=0)
						WHERE
							vtiger_account.accountid IN (SELECT vtiger_account.accountid FROM vtiger_account WHERE vtiger_account.accountrank='iron_isv'
								UNION SELECT vtiger_account.accountid FROM vtiger_account WHERE vtiger_account.accountrank='bras_isv'
								UNION SELECT vtiger_account.accountid FROM vtiger_account WHERE vtiger_account.accountrank='silv_isv'
								UNION SELECT vtiger_account.accountid FROM vtiger_account WHERE vtiger_account.accountrank='gold_isv'
								)
						AND
	 					NOT EXISTS (SELECT
							1
						FROM
							vtiger_servicecomments
						WHERE
							vtiger_servicecomments.related_to IS NOT NULL AND
							vtiger_servicecomments.related_to = vtiger_account.accountid
						)  AND vtiger_account.accountname like '%{$_REQUEST['department']}%'";
        	}
        }
        //echo $listQuery;
        $listResult = $db->pquery($listQuery, array());
        
        return $db->query_result($listResult,0,'counts');
    }

}