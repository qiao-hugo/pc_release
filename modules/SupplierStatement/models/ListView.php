<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class SupplierStatement_ListView_Model extends Vtiger_ListView_Model {
	//根据参数显示数据
	public function getListViewEntries($pagingModel) {
        $db = PearDatabase::getInstance();
        $moduleName = 'SupplierStatement';
        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');

        if(empty($orderBy) && empty($sortOrder)){
            $orderBy = 'vtiger_supplierstatement.supplierstatementid';
            $sortOrder = 'DESC';
        }
        $this->getSearchWhere();
        $listQuery = $this->getQuery();
        $listQuery.=$this->getUserWhere();

        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();
        $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;
        $viewid = ListViewSession::getCurrentView($moduleName);
        ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,
        $listQuery .= " LIMIT $startIndex,".($pageLimit);
        $listResult = $db->pquery($listQuery, array());
        $listViewRecordModels = array();
        global $current_user;
        $index = 0;
        while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['supplierstatementid'];
            $rawData['deleted'] = ($current_user->is_admin=='on')?1:0;
            $listViewRecordModels[$rawData['supplierstatementid']] = $rawData;
        }
        return $listViewRecordModels;
	}
    public function getUserWhere(){
		global $current_user,$adb;
        $searchDepartment = $_REQUEST['department'];
        $listQuery='';

        $query='SELECT invoicecompany FROM vtiger_invoicecompanyuser WHERE modulename=\'ht\' AND  userid=?';
        $result=$adb->pquery($query,array($current_user->id));
        $companySql = '';
        if($adb->num_rows($result)){
            while ($row = $adb->fetchByAssoc($result)){
                $data[] = $row['invoicecompany'];
            }
            $companySql = " OR  vtiger_suppliercontracts.companycode in('".implode("','",$data)."') ";
        }
        if(!empty($searchDepartment)&&$searchDepartment!='H1'){  //20150525 柳林刚 加入
            $userid=getDepartmentUser($searchDepartment);
            $where=getAccessibleUsers('SupplierStatement','List',true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $where=!empty($where)?$where:array(-1);
            //$listQuery= ' AND (EXISTS(SELECT 1 FROM vtiger_crmentity as crmtable WHERE  crmtable.deleted=0 AND crmtable.setype=\'Accounts\' AND crmtable.crmid=vtiger_servicecontracts.sc_related_to AND crmtable.smownerid in('.implode(',',$where).'))  OR vtiger_crmentity.smownerid in ('.implode(',',$where).'))';
            $listQuery= ' AND (vtiger_crmentity.smownerid in ('.implode(',',$where).')'.$companySql.') ';
        }else{
            $where=getAccessibleUsers();
            if($where!='1=1'){
                //$listQuery= ' AND (EXISTS(SELECT 1 FROM vtiger_crmentity as crmtable WHERE  crmtable.deleted=0 AND crmtable.setype=\'Accounts\' AND crmtable.crmid=vtiger_servicecontracts.sc_related_to AND crmtable.smownerid '.$where.')  OR vtiger_crmentity.smownerid '.$where.')';
                $listQuery= ' AND (vtiger_crmentity.smownerid '.$where.$companySql.')';
            }
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
        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }
}
