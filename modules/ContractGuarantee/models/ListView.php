<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ContractGuarantee_ListView_Model extends Vtiger_ListView_Model {


	//根据参数显示数据
	public function getListViewEntries($pagingModel) {
		$db = PearDatabase::getInstance();
		$moduleName ='ContractGuarantee';


		$orderBy = $this->getForSql('orderby');
		$sortOrder = $this->getForSql('sortorder');

		//List view will be displayed on recently created/modified records
		//列表视图将显示最近的创建修改记录  ---做什么用处
		if(empty($orderBy) && empty($sortOrder)){

			$orderBy = 'vtiger_contractguarantee.contractguaranteeid';
			$sortOrder = 'DESC';
		}

        $this->getSearchWhere();
        $listQuery = $this->getQuery();
        $listQuery=str_replace(' AND vtiger_contractguarantee.contractid IS NOT NULL','',$listQuery);
        $pattern='/\(vtiger_contractguarantee.contractid(?!,)/';
        $listQuery=preg_replace($pattern,'vtiger_contractguarantee.contractid IN(SELECT crm2.crmid FROM vtiger_crmentity AS crm2 WHERE crm2.setype in(\'ServiceContracts\',\'SupplierContracts\') AND crm2.deleted=0 AND crm2.label',$listQuery);
        $pattern='/vtiger_contractguarantee.contractid(?=,)/';
        $listQuery=preg_replace($pattern,'(SELECT crm.label FROM vtiger_crmentity as crm WHERE crm.crmid=vtiger_contractguarantee.contractid) AS contractid',$listQuery);
        $listQuery=str_replace('smownerid_owner,','smownerid_owner,(SELECT vtiger_users.email1 FROM vtiger_users WHERE vtiger_users.id=vtiger_crmentity.smownerid LIMIT 1) as email,',$listQuery);
        $pattern='/\(vtiger_contractguarantee.accountid(?!,)/';
        $listQuery=preg_replace($pattern,'vtiger_contractguarantee.accountid IN(SELECT crm3.crmid FROM vtiger_crmentity AS crm3 WHERE crm3.setype in(\'Accounts\',\'Vendors\') AND crm2.deleted=0 AND crm2.label',$listQuery);
        $pattern='/vtiger_contractguarantee.accountid(?=,)/';
        $listQuery=preg_replace($pattern,'(SELECT crm4.label FROM vtiger_crmentity as crm4 WHERE crm4.crmid=vtiger_contractguarantee.accountid) AS accountid',$listQuery);

        $listQuery.=$this->getUserWhere();
        global $current_user;


        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();

        $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;
		
		$viewid = ListViewSession::getCurrentView($moduleName);
	
		ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,
	
		$listQuery .= " LIMIT $startIndex,".($pageLimit);

        //echo $listQuery;//die();

		$listResult = $db->pquery($listQuery, array());

		while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['contractguaranteeid'];

			$listViewRecordModels[$rawData['contractguaranteeid']] = $rawData;
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
        $searchDepartment = $_REQUEST['department'];
        $listQuery=' ';
		if(!empty($searchDepartment)&&$searchDepartment!='H1'){  //20150525 柳林刚 加入
            $userid=getDepartmentUser($searchDepartment);
            $where=getAccessibleUsers('VisitingOrder','List',true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $where=!empty($where)?$where:array(-1);
            $listQuery .= ' and vtiger_crmentity.smownerid in ('.implode(',',$where).')';
        }else{
            $where=getAccessibleUsers();
            if($where!='1=1'){
                $listQuery .= ' and vtiger_crmentity.smownerid '.$where;
            }
        }


        return $listQuery;
    }
    public function getListViewCount(){
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

        //echo $listQuery.'<br>';
        //die();
        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }
}