<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class SaleManager_ListView_Model extends Vtiger_ListView_Model {


	//根据参数显示数据
	public function getListViewEntries($pagingModel) {
		$db = PearDatabase::getInstance();
		$moduleName ='SaleManager';


		$orderBy = $this->getForSql('orderby');
		$sortOrder = $this->getForSql('sortorder');

		//List view will be displayed on recently created/modified records
		//列表视图将显示最近的创建修改记录  ---做什么用处
		if(empty($orderBy) && empty($sortOrder)){

			$orderBy = 'vtiger_salemanager.salerankid';
            //$orderBy = 'vtiger_crmentity.modifiedtime';
			$sortOrder = 'DESC';
		}
        $listQuery="SELECT
                      (select last_name from vtiger_users where vtiger_users.id=vtiger_salemanager.relatetoid) as relatetoid,
                      vtiger_salemanager.performancerank,
                      salerankid,
                      vtiger_salemanager.relatetoid AS sid
                    FROM vtiger_salemanager WHERE 1=1 ";


        $this->getSearchWhere();
        $listQuery.=$this->getUserWhere();

        $queryGenerator = $this->get('query_generator');
        $searchwhere=$queryGenerator->getSearchWhere();
        if(!empty($searchwhere)){
            $listQuery.=' and '.$searchwhere;
        }
        global $current_user;


		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();

        $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;
		
		$viewid = ListViewSession::getCurrentView($moduleName);
	
		ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,
	
		$listQuery .= " LIMIT $startIndex,".($pageLimit);

        //echo $listQuery;die();

		$listResult = $db->pquery($listQuery, array());

        //echo $listQuery;//exit;
		$index = 0;
		while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['salerankid'];
			$listViewRecordModels[$rawData['salerankid']] = $rawData;
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
        $searchDepartment = $_REQUEST['department'];
        $sourceModule = $this->get('src_module');
        $listQuery=' ';
         if(!empty($searchDepartment)&&$searchDepartment!='H1'){  //20150525 柳林刚 加入
            $userid=getDepartmentUser($searchDepartment);
            $where=getAccessibleUsers('','',true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $where=!empty($where)?$where:array(-1);
            $listQuery .= ' and relatetoid in ('.implode(',',$where).')';
        }else{
            $where=getAccessibleUsers();
            if($where!='1=1'){
                $listQuery .= ' and relatetoid '.$where;

            }
        }
        //if($searchDepartment!=''){
            //$listQuery.=" AND relatetoid in( select id from vtiger_users where last_name like '%$searchDepartment%')";
        //}
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

        //echo $listQuery.'<br>';die();
        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }

}