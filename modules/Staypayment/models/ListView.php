<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Staypayment_ListView_Model extends Vtiger_ListView_Model {
	//根据参数显示数据
	public function getListViewEntries($pagingModel) {
        Matchreceivements_Record_Model::recordLog('2','exportstay');
        $db = PearDatabase::getInstance();
        $moduleName = 'Staypayment';
        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');
        Matchreceivements_Record_Model::recordLog('3','exportstay');
        if(empty($orderBy) && empty($sortOrder)){
            $orderBy = 'vtiger_staypayment.staypaymentid';
            $sortOrder = 'DESC';
        }
        $this->getSearchWhere();
        Matchreceivements_Record_Model::recordLog('4','exportstay');
        $listQuery = $this->getQuery();
        Matchreceivements_Record_Model::recordLog('5','exportstay');
//        $listQuery=str_replace('vtiger_staypayment.staypaymentname,','vtiger_staypayment.staypaymentname,vtiger_staypayment.last_sign_time,',$listQuery);
        $listQuery.=$this->getUserWhere();
        Matchreceivements_Record_Model::recordLog('6','exportstay');
        $search=array(
            'vtiger_staypayment.staypaymentname,',
            '(vtiger_account.accountname)',
            'LEFT JOIN vtiger_servicecontracts',
            '(vtiger_servicecontracts.contract_no)',
            'LEFT JOIN vtiger_account'
        );
        $replaceStr1=' LEFT JOIN vtiger_vendor ON vtiger_vendor.vendorid = vtiger_staypayment.accountid LEFT JOIN vtiger_account';
        $replaceStr2='vtiger_staypayment.staypaymentname,vtiger_staypayment.last_sign_time,';
        if($_REQUEST['public']=='Delay'){
            $replaceStr1='LEFT JOIN ( SELECT changetime,servicecontractsid FROM vtiger_receivedpayments_changedetails WHERE changetype LIKE \'%匹配%\' GROUP BY servicecontractsid) as vtiger_receivedpayments_changedetails on vtiger_receivedpayments_changedetails.servicecontractsid=vtiger_servicecontracts.servicecontractsid LEFT JOIN vtiger_vendor ON vtiger_vendor.vendorid = vtiger_staypayment.accountid LEFT JOIN vtiger_account';
            $replaceStr2='vtiger_staypayment.staypaymentname,vtiger_staypayment.last_sign_time,vtiger_staypayment.isauto,vtiger_receivedpayments_changedetails.changetime,';
        }
        $replace=array(
            $replaceStr2,
            '(SELECT accountcrm.label FROM vtiger_crmentity AS accountcrm WHERE accountcrm.crmid=vtiger_staypayment.accountid LIMIT 1)',
            'LEFT JOIN vtiger_suppliercontracts ON vtiger_suppliercontracts.suppliercontractsid = vtiger_staypayment.contractid LEFT JOIN vtiger_servicecontracts',
            '(SELECT contractcrm.label FROM vtiger_crmentity AS contractcrm WHERE contractcrm.crmid=vtiger_staypayment.contractid LIMIT 1)',
            $replaceStr1
        );
        //列表匹配多发票显示
        $listQuery=str_replace($search,$replace,$listQuery);
        $listQuery=str_replace('(vtiger_staypayment.modulestatus = \'n_complete\' AND vtiger_staypayment.modulestatus IS NOT NULL)','(IFNULL(vtiger_staypayment.modulestatus,\'\')!=\'c_complete\')',$listQuery);
        $listQuery=str_replace('(vtiger_staypayment.modulestatus LIKE \'%n_complete%\' AND vtiger_staypayment.modulestatus IS NOT NULL)','(IFNULL(vtiger_staypayment.modulestatus,\'\')!=\'c_complete\')',$listQuery);
        $listQuery=str_replace('(vtiger_staypayment.isdelay = 1 AND vtiger_staypayment.isdelay IS NOT NULL)','(vtiger_staypayment.modulestatus=\'c_complete\' and vtiger_staypayment.last_sign_time is not null and  vtiger_staypayment.last_sign_time < Now())',$listQuery);
        $listQuery=str_replace('(vtiger_staypayment.isdelay = 0 AND vtiger_staypayment.isdelay IS NOT NULL)','(vtiger_staypayment.modulestatus!=\'c_complete\' or vtiger_staypayment.last_sign_time is null or  vtiger_staypayment.last_sign_time >= Now())',$listQuery);
        $listQuery=str_replace("AND vtiger_staypayment.workflowstime IS NOT NULL","AND vtiger_staypayment.workflowstime IS NOT NULL and vtiger_staypayment.modulestatus='c_complete'",$listQuery);
        $pattern='/\(vtiger_servicecontracts.contract_no(?!,)/';
        $listQuery=preg_replace($pattern,'vtiger_staypayment.contractid IN(SELECT crm2.crmid FROM vtiger_crmentity AS crm2 WHERE crm2.setype in(\'ServiceContracts\',\'SupplierContracts\') AND crm2.deleted=0 AND crm2.label',$listQuery);
        $listQuery=str_replace('AND vtiger_servicecontracts.contract_no IS NOT NULL','',$listQuery);
        $pattern='/\(vtiger_account.accountname(?!,)/';
        $listQuery=preg_replace($pattern,'vtiger_staypayment.accountid IN(SELECT crm3.crmid FROM vtiger_crmentity AS crm3 WHERE crm3.setype in(\'Vendors\',\'Accounts\') AND crm3.deleted=0 AND crm3.label',$listQuery);
        $listQuery=str_replace('AND vtiger_account.accountname IS NOT NULL','',$listQuery);
        $listQuery=str_replace('WHERE 1=1','left join vtiger_crmentity as crm2 ON vtiger_account.accountid = crm2.crmid WHERE 1=1',$listQuery);
        Matchreceivements_Record_Model::recordLog('7','exportstay');
        if(isset($_REQUEST['pageDelay'],$_REQUEST['limitDelay'])){
            $startIndex=($_REQUEST['pageDelay']-1)*$_REQUEST['limitDelay'];
            $pageLimit=$_REQUEST['limitDelay'];
        }else{
            Matchreceivements_Record_Model::recordLog('8','exportstay');
            $startIndex = $pagingModel->getStartIndex();
            $pageLimit = $pagingModel->getPageLimit();
        }
        $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;
        $viewid = ListViewSession::getCurrentView($moduleName);
        Matchreceivements_Record_Model::recordLog($listQuery,'exportstay');
        ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,
        $oldSql=$listQuery;
        $listQuery .= " LIMIT $startIndex,".($pageLimit);
        Matchreceivements_Record_Model::recordLog('10','exportstay');
       // echo $listQuery;die;
        $listResult = $db->pquery($listQuery, array());
        $listViewRecordModels = array();
        global $current_user;
        $index = 0;
        while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['staypaymentid'];
            $rawData['deleted'] = ($current_user->is_admin=='on')?1:0;
            $listViewRecordModels[$rawData['staypaymentid']] = $rawData;
            $listViewRecordModels[$rawData['staypaymentid']]['sql']=$oldSql;
        }
        Matchreceivements_Record_Model::recordLog('11','exportstay');
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
            $companySql = " OR  vtiger_servicecontracts.companycode in('".implode("','",$data)."') ";
        }
        if(!empty($searchDepartment)&&$searchDepartment!='H1'){  //20150525 柳林刚 加入
            $userid=getDepartmentUser($searchDepartment);
            $where=getAccessibleUsers('Staypayment','List',true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $where=!empty($where)?$where:array(-1);
            //$listQuery= ' AND (EXISTS(SELECT 1 FROM vtiger_crmentity as crmtable WHERE  crmtable.deleted=0 AND crmtable.setype=\'Accounts\' AND crmtable.crmid=vtiger_servicecontracts.sc_related_to AND crmtable.smownerid in('.implode(',',$where).'))  OR vtiger_crmentity.smownerid in ('.implode(',',$where).'))';
            //处理客户导入
            $userImport=' or (crm2.smownerid in ('.implode(',',$where).') and vtiger_crmentity.smownerid = 6934)';
            $listQuery= ' AND (vtiger_crmentity.smownerid in ('.implode(',',$where).')'.$companySql.$userImport.') ';
        }else{
            $where=getAccessibleUsers();
            if($where!='1=1'){
                //$listQuery= ' AND (EXISTS(SELECT 1 FROM vtiger_crmentity as crmtable WHERE  crmtable.deleted=0 AND crmtable.setype=\'Accounts\' AND crmtable.crmid=vtiger_servicecontracts.sc_related_to AND crmtable.smownerid '.$where.')  OR vtiger_crmentity.smownerid '.$where.')';
                $userImport=' or (crm2.smownerid '.$where.' and vtiger_crmentity.smownerid = 6934)';
                $listQuery= ' AND (vtiger_crmentity.smownerid '.$where.$companySql.$userImport.')';
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
        $search=array(
            '(vtiger_account.accountname)',
            'LEFT JOIN vtiger_servicecontracts',
            '(vtiger_servicecontracts.contract_no)',
            'LEFT JOIN vtiger_account',
        );
        $replaceStr1=' LEFT JOIN vtiger_vendor ON vtiger_vendor.vendorid = vtiger_staypayment.accountid LEFT JOIN vtiger_account';
        $replaceStr2='vtiger_staypayment.staypaymentname,vtiger_staypayment.last_sign_time,';
        if($_REQUEST['public']==='Delay'){
            $replaceStr1='LEFT JOIN ( SELECT changetime,servicecontractsid FROM vtiger_receivedpayments_changedetails WHERE changetype LIKE \'%匹配%\' GROUP BY servicecontractsid) as vtiger_receivedpayments_changedetails on vtiger_receivedpayments_changedetails.servicecontractsid=vtiger_servicecontracts.servicecontractsid LEFT JOIN vtiger_vendor ON vtiger_vendor.vendorid = vtiger_staypayment.accountid LEFT JOIN vtiger_account';
            $replaceStr2='vtiger_staypayment.staypaymentname,vtiger_staypayment.last_sign_time,vtiger_receivedpayments.matchdate,';
        }
        $replace=array(
            $replaceStr2,
            'LEFT JOIN vtiger_suppliercontracts ON vtiger_suppliercontracts.suppliercontractsid = vtiger_staypayment.contractid LEFT JOIN vtiger_servicecontracts',
            '(SELECT contractcrm.label FROM vtiger_crmentity AS contractcrm WHERE contractcrm.crmid=vtiger_staypayment.contractid LIMIT 1)',
            $replaceStr1
        );
        //列表匹配多发票显示
        $listQuery=str_replace($search,$replace,$listQuery);
        $listQuery=str_replace('(vtiger_staypayment.modulestatus = \'n_complete\' AND vtiger_staypayment.modulestatus IS NOT NULL)','(IFNULL(vtiger_staypayment.modulestatus,\'\')!=\'c_complete\')',$listQuery);
        $listQuery=str_replace('(vtiger_staypayment.isdelay = 1 AND vtiger_staypayment.isdelay IS NOT NULL)','(vtiger_staypayment.modulestatus=\'c_complete\' and vtiger_staypayment.last_sign_time is not null and  vtiger_staypayment.last_sign_time < Now())',$listQuery);
        $listQuery=str_replace('(vtiger_staypayment.isdelay = 0 AND vtiger_staypayment.isdelay IS NOT NULL)','(vtiger_staypayment.modulestatus!=\'c_complete\' or vtiger_staypayment.last_sign_time is null or  vtiger_staypayment.last_sign_time >= Now())',$listQuery);
        $listQuery=str_replace("AND vtiger_staypayment.workflowstime IS NOT NULL","AND vtiger_staypayment.workflowstime IS NOT NULL and vtiger_staypayment.modulestatus='c_complete'",$listQuery);
        $pattern='/\(vtiger_servicecontracts.contract_no(?!,)/';
        $listQuery=preg_replace($pattern,'vtiger_staypayment.contractid IN(SELECT crm2.crmid FROM vtiger_crmentity AS crm2 WHERE crm2.setype in(\'ServiceContracts\',\'SupplierContracts\') AND crm2.deleted=0 AND crm2.label',$listQuery);
        $listQuery=str_replace('AND vtiger_servicecontracts.contract_no IS NOT NULL','',$listQuery);
        $pattern='/\(vtiger_account.accountname(?!,)/';
        $listQuery=preg_replace($pattern,'vtiger_staypayment.accountid IN(SELECT crm3.crmid FROM vtiger_crmentity AS crm3 WHERE crm3.setype in(\'Vendors\',\'Accounts\') AND crm3.deleted=0 AND crm3.label',$listQuery);
        $listQuery=str_replace('AND vtiger_account.accountname IS NOT NULL','',$listQuery);
        $listQuery=str_replace('WHERE 1=1','left join vtiger_crmentity as crm2 ON vtiger_account.accountid = crm2.crmid WHERE 1=1',$listQuery);
        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }
}