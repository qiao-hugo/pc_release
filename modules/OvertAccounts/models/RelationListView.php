<?php
/*
*定义管理语句
*/
class OvertAccounts_RelationListView_Model extends Vtiger_RelationListView_Model {
	static $relatedquerylist = array(
	'ServiceContracts'=>'SELECT vtiger_crmentity.crmid,vtiger_servicecontracts.contract_no,vtiger_servicecontracts.modulestatus,(SELECT GROUP_CONCAT(productid)  from vtiger_salesorderproductsrel where vtiger_salesorderproductsrel.servicecontractsid = vtiger_servicecontracts.servicecontractsid) as productid,vtiger_servicecontracts.total,vtiger_servicecontracts.signdate,vtiger_servicecontracts.firstreceivepaydate,SUM(vtiger_receivedpayments.unit_price) as repay FROM vtiger_servicecontracts INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_servicecontracts.servicecontractsid  LEFT JOIN vtiger_receivedpayments on vtiger_servicecontracts.servicecontractsid=vtiger_receivedpayments.relatetoid WHERE vtiger_crmentity.deleted =0 AND vtiger_servicecontracts.sc_related_to=? GROUP BY vtiger_servicecontracts.contract_no',
	//'VisitingOrder'=> 'SELECT vtiger_crmentity.crmid,vtiger_visitingorder.destination, vtiger_visitingorder.contacts, vtiger_visitingorder.purpose, vtiger_visitingorder.extractid, vtiger_visitingorder.accompany, vtiger_visitingorder.startdate, vtiger_visitingorder.enddate, vtiger_visitingorder.outobjective,vtiger_visitingorder.visitingorderid FROM vtiger_visitingorder INNER JOIN vtiger_crmentity ON vtiger_visitingorder.visitingorderid = vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted=0 AND vtiger_visitingorder.related_to =?',
	'ServiceMaintenance'=>'select vtiger_servicemaintenance.servicemaintenanceid as crmid, vtiger_servicemaintenance.servicecommentsid, vtiger_servicemaintenance.addtime, vtiger_servicemaintenance.disposeid,vtiger_servicemaintenance.serviceid,(select vtiger_crmentity.smownerid from vtiger_crmentity where vtiger_crmentity.crmid=vtiger_servicemaintenance.related_to) as ownerid, productid,  vtiger_servicemaintenance.isoptimize, vtiger_servicemaintenance.issuetype,if(isnull(vtiger_servicemaintenance.processstate),\'untreated\',vtiger_servicemaintenance.processstate) as processstate from vtiger_servicemaintenance where vtiger_servicemaintenance.related_to=?',
	'ServiceComplaints'=>'SELECT vtiger_servicecomplaints.servicecomplaintsid as crmid,vtiger_servicecomplaints.related_to, vtiger_servicecomplaints.productid,vtiger_servicecomplaints.complaitype,vtiger_servicecomplaints.complainantid, vtiger_servicecomplaints.handleid, vtiger_servicecomplaints.handletime,vtiger_servicecomplaints.refundmoney, vtiger_servicecomplaints.refundstatus, vtiger_servicecomplaints.createid,vtiger_servicecomplaints.complaicontent, vtiger_servicecomplaints.improvementadvise, vtiger_servicecomplaints.personalinsight,vtiger_servicecomplaints.servicecomplaintsid FROM vtiger_servicecomplaints LEFT JOIN vtiger_users ON vtiger_servicecomplaints.complainantid=vtiger_users.id WHERE vtiger_servicecomplaints.related_to =?',
	'Invoice'=>'SELECT vtiger_crmentity.crmid,vtiger_crmentity.smownerid AS assigned_user_id,vtiger_invoice.invoice_no,vtiger_invoice.*,vtiger_invoice.invoiceid FROM vtiger_invoice INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_invoice.invoiceid WHERE vtiger_crmentity.deleted=0 AND vtiger_invoice.accountid =?',
	'Newinvoice'=>'SELECT vtiger_crmentity.crmid,vtiger_crmentity.smownerid AS assigned_user_id,vtiger_newinvoice.invoice_no,vtiger_newinvoice.*,vtiger_newinvoice.invoiceid FROM vtiger_newinvoice INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_newinvoice.invoiceid WHERE vtiger_crmentity.deleted=0 AND vtiger_newinvoice.accountid =?',
	'Contacts'=>'SELECT vtiger_contactdetails.contactid as crmid,vtiger_contactdetails.* FROM vtiger_contactdetails WHERE EXISTS(select crmid from vtiger_crmentity where vtiger_crmentity.crmid = vtiger_contactdetails.contactid and vtiger_crmentity.deleted = 0) AND vtiger_contactdetails.accountid =?',
	'SalesorderProductsrel'=>'SELECT vtiger_salesorderproductsrel.salesorderproductsrelname,vtiger_salesorderproductsrel.producttype, vtiger_salesorderproductsrel.servicecontractsid,vtiger_salesorderproductsrel.salesorderid,vtiger_salesorderproductsrel.productcomboid, vtiger_salesorderproductsrel.productid, vtiger_salesorderproductsrel.marketprice, vtiger_salesorderproductsrel.realmarketprice, vtiger_salesorderproductsrel.costing,vtiger_salesorderproductsrel.realcosting, vtiger_salesorderproductsrel.starttime, vtiger_salesorderproductsrel.endtime,vtiger_salesorderproductsrel.servicecount,vtiger_salesorderproductsrel.producttext,vtiger_salesorderproductsrel.salesorderproductsrelid as crmid,vtiger_servicecontracts.total,vtiger_servicecontracts.modulestatus FROM vtiger_salesorderproductsrel left join vtiger_servicecontracts on vtiger_servicecontracts.servicecontractsid=vtiger_salesorderproductsrel.servicecontractsid LEFT JOIN vtiger_salesorder ON vtiger_salesorder.salesorderid=vtiger_salesorderproductsrel.salesorderid LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_salesorder.salesorderid WHERE vtiger_salesorderproductsrel.salesorderproductsrelid > 0 AND vtiger_crmentity.deleted=0 AND vtiger_salesorderproductsrel.multistatus in(0,3) AND isvisible=1 AND vtiger_salesorderproductsrel.accountid=?',
    'AutoTask'=>'SELECT autoworkflowentityid,autoworkflowid, autoworkflowtaskentityid as crmid,process_from, modulename, autoworkflowtaskname, auditedtime, createdtime, vtiger_account.accountname AS accountid, actiontime,  isaction, moulestatus, taskremark FROM `vtiger_autoworkflowtaskentitys` LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_autoworkflowtaskentitys.accountid WHERE vtiger_account.accountid = ?',
	'Billing'=>'SELECT billingid as crmid,taxpayers_no,registeraddress,depositbank,telephone,accountnumber,isformtable,businessnamesone,modulestatus FROM vtiger_billing WHERE vtiger_billing.accountid=?',
	'ShareAccount'=>'SELECT vtiger_shareaccount.shareaccountid AS crmid,vtiger_shareaccount.createdid,vtiger_shareaccount.createdtime,sharestatus,vtiger_shareaccount.userid FROM vtiger_shareaccount WHERE vtiger_shareaccount.accountid=?'
    );

	public function getEntries($pagingModel){
		//获取关联模块查询语句
		//marketprice
		$relatedModuleName=$_REQUEST['relatedModule'];
		$relatedquerylist=self::$relatedquerylist;
		if(isset($relatedquerylist[$relatedModuleName])){
			$parentId = $_REQUEST['record'];
			$this->relationquery=str_replace('?',$parentId,$relatedquerylist[$relatedModuleName]);
		}
		return parent::getEntries($pagingModel);
	/* 	
		$db = PearDatabase::getInstance();
		$parentModule = $this->getParentRecordModel()->getModule();
		$relationModule = $this->getRelationModel()->getRelationModuleModel();
		
		$relatedColumnFields = $relationModule->getRelatedListFields();
		
		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();
		//$query= ServiceMaintenance_Record_Model::getServiceMaintenanceListSql();
		
		$limitQuery='select vtiger_servicemaintenance.servicemaintenanceid, vtiger_servicemaintenance.servicecommentsid, vtiger_servicemaintenance.addtime, vtiger_servicemaintenance.disposeid,vtiger_servicemaintenance.serviceid,(select vtiger_crmentity.smownerid from vtiger_crmentity where vtiger_crmentity.crmid=vtiger_servicemaintenance.related_to) as ownerid, productid,  vtiger_servicemaintenance.isoptimize, vtiger_servicemaintenance.issuetype,if(isnull(vtiger_servicemaintenance.processstate),\'untreated\',vtiger_servicemaintenance.processstate) as processstate from vtiger_servicemaintenance where 1=1 and vtiger_servicemaintenance.related_to=? LIMIT '.$startIndex.','.$pageLimit;
	//IFNULL((select vtiger_products.productmaintainer from vtiger_products where vtiger_products.productid=vtiger_servicemaintenance.productid),'--') as productmaintainer
	//IFNULL((select vtiger_products.productman from vtiger_products where vtiger_products.productid=vtiger_servicemaintenance.productid),'--') as productman,
		$result = $db->pquery($limitQuery, array($_REQUEST['record']));
		$relatedRecordList = array();
		for($i=0; $i< $db->num_rows($result); $i++ ) {
			$row = $db->fetch_row($result,$i);
			$newRow = array();
			foreach($row as $col=>$val){
				if(array_key_exists($col,$relatedColumnFields)){
                    $newRow[$relatedColumnFields[$col]] = $val;
                }
            }
			//To show the value of "Assigned to"
			$newRow['assigned_user_id'] = $row['smownerid'];
			$record = Vtiger_Record_Model::getCleanInstance($relationModule->get('name'));
            $record->setData($newRow)->setModuleFromInstance($relationModule);
            $record->setId($row['servicemaintenanceid']);
			$relatedRecordList[$row['servicemaintenanceid']] = $record;
		}
		$pagingModel->calculatePageRange($relatedRecordList);

		$nextLimitQuery = $query. ' LIMIT '.($startIndex+$pageLimit).' , 1';
		$nextPageLimitResult = $db->pquery($nextLimitQuery, array());
		if($db->num_rows($nextPageLimitResult) > 0){
			$pagingModel->set('nextPageExists', true);
		}else{
			$pagingModel->set('nextPageExists', false);
		}
		return $relatedRecordList; */
	}

}