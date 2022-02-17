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
class ContractsProducts_ListView_Model extends Vtiger_ListView_Model {
	//根据参数显示数据
	public function getListViewEntries($pagingModel) {
        $db = PearDatabase::getInstance();

        $moduleName = 'ContractsProducts';
        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');


        if(empty($orderBy) && empty($sortOrder) && $moduleName != "Users"){
            $orderBy = 'relcontractsproductsid';
            $sortOrder = 'DESC';
        }

        $listQuery=$this->getTableSQL();
       // $listQuery="SELECT * FROM ($listQuery) vtiger_serviceassignset WHERE 1=1";
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
            $rawData['id'] = $rawData['relcontractsproductsid'];
            $listViewRecordModels[$rawData['relcontractsproductsid']] = $rawData;
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

        $where=getAccessibleUsers();
        if($where!='1=1'){
        	$listQuery .= ' and vtiger_contractsproductsrel.relcontractsproductsid'.$where;
        }
//
//        if($_REQUEST['public']=='untreated'){
//            $listQuery = $listQuery." and vtiger_servicemaintenance.processstate='untreated'";
//        }if($_REQUEST['public']=='processing'){
//            $listQuery = $listQuery." and vtiger_servicemaintenance.processstate='processing'";
//        }elseif($_REQUEST['public']=='processed'){
//            $listQuery = $listQuery." and vtiger_servicemaintenance.processstate='processed'";
//        }elseif($_REQUEST['public']=='unabletoprocess'){
//            $listQuery = $listQuery." and vtiger_servicemaintenance.processstate='unabletoprocess'";
//        }elseif($_REQUEST['public']=='cancellation'){
//            $listQuery = $listQuery." and vtiger_servicemaintenance.processstate='cancellation'";
//        }
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

        //20150430 young 自定义的sql需要重写计算总数语句
       // $listQuery= ServiceMaintenance_Record_Model::getServiceMaintenanceListSql();
        //$listQuery=$this->getTableSQL();
        //$listQuery=$this->getQuery();
        //$listQuery='SELECT count(1) as counts FROM ('.$listQuery.') vtiger_contractsproductsrel LEFT JOIN vtiger_contract_type ON vtiger_contract_type.contract_typeid=vtiger_contractsproductsrel.contract_type WHERE 1=1';
        $listQuery="SELECT count(1) AS counts FROM
                    ( SELECT vtiger_contractsproductsrel.contract_type,
                    ( SELECT CONCAT( last_name, '[', IFNULL((
                    SELECT departmentname FROM vtiger_departments WHERE departmentid = (
                    SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']',
                     ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE vtiger_contractsproductsrel.creatorid = vtiger_users.id ) AS creatorid,
                    ( SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']',
                    ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE vtiger_contractsproductsrel.modifiedby = vtiger_users.id ) AS modifiedby,
                    vtiger_contractsproductsrel.modifiedtime,
                    vtiger_contractsproductsrel.createdtime,
                    vtiger_contractsproductsrel.relcontractsproductsid,
		    vtiger_contractsproductsrel.billingcontent,
                    vtiger_contractsproductsrel.relproductid
                    FROM vtiger_contractsproductsrel WHERE 1 = 1 ) vtiger_contractsproductsrel LEFT JOIN vtiger_contract_type ON vtiger_contract_type.contract_typeid = vtiger_contractsproductsrel.contract_type
                    LEFT  JOIN vtiger_products ON vtiger_products.productid = vtiger_contractsproductsrel.relproductid
                    WHERE 1 = 1";
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
    public function getTableSQL(){
        $sql="SELECT vtiger_contractsproductsrel.billingcontent,(SELECT	contract_type FROM	vtiger_contract_type WHERE	vtiger_contract_type.contract_typeid = vtiger_contractsproductsrel.contract_type) AS contract_type,(	SELECT	productname FROM	vtiger_products WHERE	vtiger_products.productid = vtiger_contractsproductsrel.relproductid) AS relproductid,(select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_contractsproductsrel.creatorid=vtiger_users.id) as creatorid,(select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_contractsproductsrel.modifiedby=vtiger_users.id) as modifiedby,vtiger_contractsproductsrel.createdtime,vtiger_contractsproductsrel.relcontractsproductsid,vtiger_contractsproductsrel.modifiedtime FROM vtiger_contractsproductsrel LEFT JOIN vtiger_contract_type ON vtiger_contract_type.contract_typeid=vtiger_contractsproductsrel.contract_type LEFT  JOIN vtiger_products ON vtiger_products.productid = vtiger_contractsproductsrel.relproductid WHERE 1=1";
        return $sql;
    }
}
