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
class ServiceMaintenance_ListView_Model extends Vtiger_ListView_Model {
	//根据参数显示数据
	public function getListViewEntries($pagingModel) {
        $db = PearDatabase::getInstance();

        $moduleName = 'ServiceMaintenance';
        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');


        if(empty($orderBy) && empty($sortOrder) && $moduleName != "Users"){
            $orderBy = 'servicemaintenanceid';
            $sortOrder = 'DESC';
        }

		$listQuery= ServiceMaintenance_Record_Model::getServiceMaintenanceListSql();
        $listQuery="SELECT  vtiger_servicemaintenance.*,vtiger_account.accountid as related_to_reference,(select accountname from vtiger_account where vtiger_account.accountid=vtiger_servicemaintenance.related_to ) as related_to,IFNULL((select productname from vtiger_products where productid=vtiger_servicemaintenance.productid),'--') as productid,IFNULL((select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where id=vtiger_servicemaintenance.disposeid),'--') as disposeid,IFNULL((select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where id=vtiger_servicemaintenance.serviceid),'--') as serviceid, IFNULL((select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where id=vtiger_servicemaintenance.ownerid),'--') as ownerid FROM (".$listQuery.") vtiger_servicemaintenance LEFT  join vtiger_account ON vtiger_account.accountid = vtiger_servicemaintenance.related_to LEFT  join vtiger_products ON vtiger_products.productid = vtiger_servicemaintenance.productid WHERE 1=1";
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

        $listViewRecordModels = array();
        while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['servicemaintenanceid'];
            $listViewRecordModels[$rawData['servicemaintenanceid']] = $rawData;
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
        	$listQuery .= ' and vtiger_servicemaintenance.serviceid'.$where;
        }

        if($_REQUEST['public']=='untreated'){
            $listQuery = $listQuery." and vtiger_servicemaintenance.processstate='untreated'";
        }if($_REQUEST['public']=='processing'){
            $listQuery = $listQuery." and vtiger_servicemaintenance.processstate='processing'";
        }elseif($_REQUEST['public']=='processed'){
            $listQuery = $listQuery." and vtiger_servicemaintenance.processstate='processed'";
        }elseif($_REQUEST['public']=='unabletoprocess'){
            $listQuery = $listQuery." and vtiger_servicemaintenance.processstate='unabletoprocess'";
        }elseif($_REQUEST['public']=='cancellation'){
            $listQuery = $listQuery." and vtiger_servicemaintenance.processstate='cancellation'";
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

        //20150430 young 自定义的sql需要重写计算总数语句
        $listQuery= ServiceMaintenance_Record_Model::getServiceMaintenanceListSql();
        $listQuery='SELECT count(1) as counts FROM ('.$listQuery.') vtiger_servicemaintenance LEFT  join vtiger_account ON vtiger_account.accountid = vtiger_servicemaintenance.related_to LEFT  join vtiger_products ON vtiger_products.productid = vtiger_servicemaintenance.productid WHERE 1=1';
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
}
