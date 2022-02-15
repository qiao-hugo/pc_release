<?php
/*
*定义管理语句
*/
class VisitDepartment_RelationListView_Model extends Vtiger_RelationListView_Model {
	static $relatedquerylist = array(
		//'VisitAccountContract'=>'SELECT vtiger_visitaccountcontract.*,vtiger_visitaccountcontract.visitimprovementid AS crmid FROM vtiger_visitaccountcontract WHERE vtiger_visitaccountcontract.visitingorderid=?'
    );

	public function getEntries($pagingModel){
		//获取关联模块查询语句
		//marketprice
		$relatedModuleName=$_REQUEST['relatedModule'];
		$relatedquerylist=self::$relatedquerylist;
		if($relatedModuleName=='VisitAccountContract'){
		    $recordModel=Vtiger_Record_Model::getInstanceById($_REQUEST['record'],'VisitDepartment');
		    $column_fields=$recordModel->entity->column_fields;
		    $yearandmonth=$column_fields['year'].'-'.$column_fields['month'];
            $userid=getDepartmentUser($column_fields['deparmentid']);
            //$relatedquerylist['VisitAccountContract']="SELECT (vtiger_account.accountname) as accountid,vtiger_visitaccountcontract.accountid as accountid_reference,vtiger_account.linkname,vtiger_account.phone,vtiger_account.address, (select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_crmentity.smownerid=vtiger_users.id) as smownerid,vtiger_crmentity.smownerid as smownerid_reference, (vtiger_servicecontracts.contract_no) as contractid,vtiger_visitaccountcontract.contractid as contractid_reference,vtiger_servicecontracts.signdate,(select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_servicecontracts.signid=vtiger_users.id) as signid, (vtiger_visitingorder.subject) as visitingorderid,vtiger_visitaccountcontract.visitingorderid as visitingorderid_reference,vtiger_visitaccountcontract.vstartdate,(select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_visitaccountcontract.vextractid=vtiger_users.id) as vextractid,vtiger_visitaccountcontract.vaccompany,vtiger_visitaccountcontract.commentstaus,vtiger_visitaccountcontract.visitaccountcontractid as crmid FROM vtiger_visitaccountcontract LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_visitaccountcontract.accountid LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_account.accountid LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_visitaccountcontract.contractid LEFT JOIN vtiger_visitingorder ON vtiger_visitingorder.visitingorderid=vtiger_visitaccountcontract.visitingorderid WHERE left(vtiger_visitaccountcontract.vstartdate,7)= '{$yearandmonth}' AND vtiger_visitaccountcontract.vextractid IN(".implode(',',$userid).") ORDER BY visitaccountcontractid DESC";
            $relatedquerylist['VisitAccountContract']="SELECT *,vtiger_visitaccountcontract.visitaccountcontractid as crmid FROM vtiger_visitaccountcontract LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_visitaccountcontract.accountid LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_account.accountid LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_visitaccountcontract.contractid LEFT JOIN vtiger_visitingorder ON vtiger_visitingorder.visitingorderid=vtiger_visitaccountcontract.visitingorderid WHERE left(vtiger_visitaccountcontract.vstartdate,7)= '{$yearandmonth}' AND vtiger_visitaccountcontract.vextractid IN(".implode(',',$userid).") ORDER BY visitaccountcontractid DESC";
        }
		if(isset($relatedquerylist[$relatedModuleName])){
			$parentId = $_REQUEST['record'];
			$this->relationquery=str_replace('?',$parentId,$relatedquerylist[$relatedModuleName]);
		}
		return parent::getEntries($pagingModel);
	}

}