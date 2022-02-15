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
 * Inventory Record Model Class
 */
class Receivedpaymentstatistics_Record_Model extends Vtiger_Record_Model { 
		public static function getAllReceivedPayments($records){
		$db = PearDatabase::getInstance();
		$result = $db->pquery("SELECT * FROM `vtiger_receivedpayments` where relatetoid in(?)",array($records));
		//var_dump($result);
		$stages=array();
		for($i=0; $i<$db->num_rows($result); $i++) {
			$relmodule = $db->query_result($result, $i, 'relmodule');
			$unit_price =	$db->query_result($result, $i, 'unit_price');
			$createtime = $db->query_result($result, $i, 'createtime');
			$discontinued=$db->query_result($result, $i, 'discontinued');
			$stages[]=array('relmodule'=>$relmodule,'unit_price'=>$unit_price,'createtime'=>$createtime,'discontinued'=>$discontinued);
		}
		return  $stages;
	}
	 public function getlistviewsql(){
// 	     可能需要修改;
	     $listQuery = "SELECT * FROM ( SELECT cc.reality_date, cc.owncompany, cc.paytitle, cc.owncompanys, ( SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE cc.receivedpaymentownid = vtiger_users.id ) AS receivedpaymentownid, contract_no, contract_type, ( SELECT accountname FROM vtiger_account WHERE vtiger_account.accountid = vtiger_servicecontracts.sc_related_to ) AS sc_related_to, vtiger_products.productname, cc.unit_price, marketprice, FORMAT( cc.alreadyprice * cc.scalling / 100, 2 ) AS produachieve, (marketprice - amountproduct) AS remainder, cc.receivedpaymentsid2, cc.servicecontractid AS servicecontractid2, vtiger_servicecontracts.sc_related_to AS accounted_reference, cc.receivedpaymentownid AS receivedpaymentownid2, cc.productsid AS productsid2 FROM ( SELECT aa.*, bb.* FROM ( SELECT vtiger_receivedpayments.reality_date, vtiger_receivedpayments.owncompany, vtiger_receivedpayments.checkid, vtiger_receivedpayments.createid, vtiger_receivedpayments.createtime, vtiger_receivedpayments.exchangerate, vtiger_receivedpayments.fallinto, vtiger_receivedpayments.modifiedtime, vtiger_receivedpayments.overdue, vtiger_receivedpayments.paytitle, vtiger_receivedpayments.receivementcurrencytype, vtiger_receivedpayments.standardmoney, vtiger_receivedpayments.unit_price, vtiger_receivedpayments.receivedpaymentsid AS receivedpaymentsid1, vtiger_achievementallot.achievementallotid, vtiger_achievementallot.businessunit, vtiger_achievementallot.owncompanys, vtiger_achievementallot.receivedpaymentownid, vtiger_achievementallot.scalling, vtiger_achievementallot.servicecontractid FROM vtiger_receivedpayments RIGHT JOIN vtiger_achievementallot ON vtiger_receivedpayments.receivedpaymentsid = vtiger_achievementallot.receivedpaymentsid ) aa, ( SELECT vtiger_receivedpayments.receivedpaymentsid AS receivedpaymentsid2, vtiger_receivementproducts.alreadyprice, vtiger_receivementproducts.productsid, vtiger_receivementproducts.receivedpaymentsproductsid, vtiger_receivementproducts.salesorderproductsid FROM vtiger_receivedpayments RIGHT JOIN vtiger_receivementproducts ON vtiger_receivedpayments.receivedpaymentsid = vtiger_receivementproducts.receivedpaymentid ) bb WHERE aa.receivedpaymentsid1 = bb.receivedpaymentsid2 ) cc LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid = cc.servicecontractid LEFT JOIN vtiger_products ON vtiger_products.productid = cc.productsid LEFT JOIN vtiger_salesorderproductsrel ON vtiger_salesorderproductsrel.salesorderproductsrelid = cc.salesorderproductsid ) vtiger_receivedpaymentstatistics where 1=1  ";
	     return $listQuery;
    }
}