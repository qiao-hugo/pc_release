<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ContractDelaySign_ListView_Model extends Vtiger_ListView_Model {
	//根据参数显示数据
	public function getListViewEntries($pagingModel) {

        $db = PearDatabase::getInstance();
        $moduleName = 'ContractDelaySign';
        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');

        if(empty($orderBy) && empty($sortOrder)){
            $orderBy = 'vtiger_contractdelaysign.contractdelaysignid';
            $sortOrder = 'DESC';
        }
        $this->getSearchWhere();
        $listQuery = $this->getQuery();
        $listQuery.=$this->getUserWhere();
        global $configcontracttypeNameTYUN;
        if($_REQUEST['report']=='notyun'){
            $listQuery .= ' and vtiger_contractdelaysign.contract_type not in (\''.implode("','",$configcontracttypeNameTYUN).'\') and vtiger_contractdelaysign.contract_type is not null and vtiger_contractdelaysign.contract_type!=""';
        }else{
            $listQuery .= ' and vtiger_contractdelaysign.contract_type in (\''.implode("','",$configcontracttypeNameTYUN).'\')';
        }

        $listQuery = str_replace("LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_contractdelaysign.accountid",
            "LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_contractdelaysign.accountid LEFT JOIN vtiger_crmentity a ON vtiger_servicecontracts.servicecontractsid = a.crmid ",$listQuery);

        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();
        $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;
        $viewid = ListViewSession::getCurrentView($moduleName);
        ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,
        $listQuery .= " LIMIT $startIndex,".($pageLimit);
//        echo $listQuery;die;
        $listResult = $db->pquery($listQuery, array());
        $listViewRecordModels = array();
        global $current_user;
        $index = 0;
        while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['contractdelaysignid'];
            $listViewRecordModels[$rawData['contractdelaysignid']] = $rawData;
        }
        return $listViewRecordModels;
	}
    public function getUserWhere(){
        $searchDepartment = $_REQUEST['department'];//部门搜索的部门
        if(empty($searchDepartment)){
            $searchDepartment = 'H1';
        }
        global $current_user;

        $where=getAccessibleUsers('ServiceContracts','List',true);
        $userid=getDepartmentUser($searchDepartment);
        $listQuery = '';
        if(!empty($searchDepartment)){
            if(!empty($where)&&$where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $where=!empty($where)?$where:array(-1);
        }


        //wangbin 2015年6月3日 星期三 添加合同列表的部门搜索;
        $department = $_REQUEST['department'];

        //end
        if(!empty($where)&&getAccessibleUsers('ServiceContracts','List',true)!='1=1'){

            if(!empty($current_user->user_manager)){
                $searchDepartment=explode(' |##| ',$current_user->user_manager);
                foreach($searchDepartment as $val){
                    $userid=getDepartmentUser($val);
                    $where=array_intersect($where,$userid);
                }
            }
            $where=!empty($where)?$where:array(-1);
            $where=implode(',',$where);
            //2015-04-28 young 工单弹出的合同只能是业绩所属人（提单人）才能看到

            $shareaccount=' OR EXISTS(SELECT 1 FROM vtiger_shareaccount WHERE vtiger_shareaccount.userid in('.$where.') and vtiger_shareaccount.sharestatus=1 AND vtiger_shareaccount.accountid=vtiger_servicecontracts.sc_related_to)';
            $invoicecompany=' OR '.getAccessibleCompany('vtiger_servicecontracts.servicecontractsid');
            $listQuery .= ' and (a.smownerid in ('.$where.') or vtiger_servicecontracts.receiveid in ('.$where.') or vtiger_account.serviceid='.$current_user->id.' or EXISTS(SELECT 1 FROM vtiger_crmentity AS crmtable WHERE crmtable.crmid=vtiger_servicecontracts.sc_related_to AND crmtable.smownerid IN ('.$where.'))'.$shareaccount.$invoicecompany.')';

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
        $queryGenerator = $this->get('query_generator');
        $where=$this->getUserWhere();
        $queryGenerator->addUserWhere($where);
        $listQuery =  $queryGenerator->getQueryCount();
        $listQuery = str_replace("LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_contractdelaysign.accountid",
            "LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_contractdelaysign.accountid LEFT JOIN vtiger_crmentity a ON vtiger_servicecontracts.servicecontractsid = a.crmid ",$listQuery);
        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }
}
