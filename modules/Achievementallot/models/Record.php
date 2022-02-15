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
class Achievementallot_Record_Model extends Vtiger_Record_Model { 
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
        $listQuery = "SELECT
       vtiger_achievementallot.scalling,
vtiger_achievementallot.achievementallotid,
	vtiger_achievementallot.owncompanys,
	vtiger_achievementallot.matchdate,
vtiger_achievementallot.departmentid,
vtiger_achievementallot.postingdate,
             ( SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE vtiger_achievementallot.receivedpaymentownid = vtiger_users.id ) AS receivedpaymentownid,
	vtiger_achievementallot.businessunit,
(
		SELECT
			CONCAT(
				last_name,
				'[',
				IFNULL(
					(
						SELECT
							departmentname
						FROM
							vtiger_departments
						WHERE
							departmentid = (
								SELECT
									departmentid
								FROM
									vtiger_user2department
								WHERE
									userid = vtiger_users.id
								LIMIT 1
							)
					),
					''
				),
				']',
				(
					IF (
						`status` = 'Active',
						'',
						'[离职]'
					)
				)
			) AS last_name
		FROM
			vtiger_users
		WHERE
			vtiger_receivedpayments.createid = vtiger_users.id
	) AS createid,
	vtiger_receivedpayments.reality_date,
	vtiger_receivedpayments.createtime,
	vtiger_receivedpayments.overdue,
	vtiger_receivedpayments.unit_price,
	vtiger_receivedpayments.modifiedtime,
    vtiger_receivedpayments.paytitle,
     vtiger_receivedpayments.owncompany,
   IF(vtiger_receivedpayments.fallinto = 1,'是','否') AS fallinto,
	vtiger_servicecontracts.contract_no AS relatetoid,
     vtiger_servicecontracts.servicecontractsid AS relatetoid_reference,       
	(
		SELECT
			CONCAT(
				last_name,
				'[',
				IFNULL(
					(
						SELECT
							departmentname
						FROM
							vtiger_departments
						WHERE
							departmentid = (
								SELECT
									departmentid
								FROM
									vtiger_user2department
								WHERE
									userid = vtiger_users.id
								LIMIT 1
							)
					),
					''
				),
				']',
				(

					IF (
						`status` = 'Active',
						'',
						'[离职]'
					)
				)
			) AS last_name
		FROM
			vtiger_users
		WHERE
			vtiger_receivedpayments.checkid = vtiger_users.id
	) AS checkid,
	vtiger_receivedpayments.exchangerate,
	vtiger_receivedpayments.accountscompany,
	vtiger_receivedpayments.receivementcurrencytype,
	vtiger_receivedpayments.standardmoney,
	vtiger_receivedpayments.receivedpaymentsid
FROM
	vtiger_receivedpayments
RIGHT  JOIN  vtiger_achievementallot ON vtiger_receivedpayments.receivedpaymentsid = vtiger_achievementallot.receivedpaymentsid
            left join vtiger_servicecontracts on vtiger_servicecontracts.servicecontractsid=vtiger_receivedpayments.relatetoid 
WHERE
	1 = 1";
	    /*  $listQuery = "SELECT
	cc.reality_date,
	cc.owncompany,
	cc.paytitle,
	cc.owncompanys,
	(
		SELECT
			CONCAT(
				last_name,
				'[',
				IFNULL(
					(
						SELECT
							departmentname
						FROM
							vtiger_departments
						WHERE
							departmentid = (
								SELECT
									departmentid
								FROM
									vtiger_user2department
								WHERE
									userid = vtiger_users.id
								LIMIT 1
							)
					),
					''
				),
				']',
				(

					IF (
						`status` = 'Active',
						'',
						'[离职]'
					)
				)
			) AS last_name
		FROM
			vtiger_users
		WHERE
			cc.receivedpaymentownid = vtiger_users.id
	) AS receivedpaymentownid,
	contract_no,
	contract_type,
	(SELECT accountname from vtiger_account WHERE vtiger_account.accountid = sc_related_to) AS sc_related_to,
	productname,
	cc.unit_price,
	marketprice,
	FORMAT(cc.alreadyprice*cc.scalling/100,2) AS produachieve,
	(marketprice - amountproduct) AS remainder
FROM
	(
		SELECT
			aa.*, bb.*
		FROM
			(
				SELECT
					vtiger_receivedpayments.reality_date,
					vtiger_receivedpayments.owncompany,
					vtiger_receivedpayments.checkid,
					vtiger_receivedpayments.createid,
					vtiger_receivedpayments.createtime,
					vtiger_receivedpayments.exchangerate,
					vtiger_receivedpayments.fallinto,
					vtiger_receivedpayments.modifiedtime,
					vtiger_receivedpayments.overdue,
					vtiger_receivedpayments.paytitle,
					vtiger_receivedpayments.receivementcurrencytype,
					vtiger_receivedpayments.standardmoney,
					vtiger_receivedpayments.unit_price,
					vtiger_receivedpayments.receivedpaymentsid AS receivedpaymentsid1,
					vtiger_achievementallot.achievementallotid,
					vtiger_achievementallot.businessunit,
					vtiger_achievementallot.owncompanys,
					vtiger_achievementallot.receivedpaymentownid,
					vtiger_achievementallot.scalling,
					vtiger_achievementallot.servicecontractid
				FROM
					vtiger_receivedpayments
				RIGHT JOIN vtiger_achievementallot ON vtiger_receivedpayments.receivedpaymentsid = vtiger_achievementallot.receivedpaymentsid
			) aa,
			(
				SELECT
					vtiger_receivedpayments.receivedpaymentsid AS receivedpaymentsid2,
					vtiger_receivementproducts.alreadyprice,
					vtiger_receivementproducts.productsid,
					vtiger_receivementproducts.receivedpaymentsproductsid,
					vtiger_receivementproducts.salesorderproductsid
				FROM
					vtiger_receivedpayments
				RIGHT JOIN vtiger_receivementproducts ON vtiger_receivedpayments.receivedpaymentsid = vtiger_receivementproducts.receivedpaymentid
			) bb
		WHERE
			aa.receivedpaymentsid1 = bb.receivedpaymentsid2
	) cc
LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid = cc.servicecontractid
LEFT JOIN vtiger_products ON vtiger_products.productid = cc.productsid
LEFT JOIN vtiger_salesorderproductsrel ON vtiger_salesorderproductsrel.salesorderproductsrelid = cc.salesorderproductsid 
	         WHERE 1 = 1"; */
	     return $listQuery;
    }

    /**
     * 求合
     * @return string
     */
    public function getListviewCountSql(){
        $listQuery = "SELECT
                      count(1) AS counts
                        FROM
                            vtiger_receivedpayments
                        RIGHT  JOIN  vtiger_achievementallot ON vtiger_receivedpayments.receivedpaymentsid = vtiger_achievementallot.receivedpaymentsid
                                    left join vtiger_servicecontracts on vtiger_servicecontracts.servicecontractsid=vtiger_receivedpayments.relatetoid 
                        WHERE
                            1 = 1";

        return $listQuery;
    }

    /**
     * T云业绩分成
     * @return string
     */
    public function getListvViewSqlTyun(){
        return "SELECT
                    vtiger_achievementallot.scalling,
                    vtiger_achievementallot.achievementallotid,
                    vtiger_achievementallot.owncompanys,
                    vtiger_achievementallot.matchdate,
                    vtiger_achievementallot.departmentid,
                    vtiger_achievementallot.postingdate,
                    (SELECT CONCAT( last_name, '[', IFNULL((SELECT departmentname FROM vtiger_departments WHERE departmentid = (SELECT departmentid FROM vtiger_user2department WHERE userid=vtiger_users.id LIMIT 1 )),''), ']', (IF(`status` = 'Active', '', '[离职]'))) AS last_name FROM vtiger_users WHERE vtiger_achievementallot.receivedpaymentownid = vtiger_users.id) AS receivedpaymentownid,
                    vtiger_achievementallot.businessunit,
                    (SELECT CONCAT(last_name,'[',IFNULL((SELECT departmentname FROM vtiger_departments WHERE departmentid =(SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1)),''), ']',(IF( `status`='Active','','[离职]')))AS last_name FROM vtiger_users WHERE vtiger_receivedpayments.createid=vtiger_users.id )AS createid,
                    vtiger_receivedpayments.reality_date,
                    vtiger_receivedpayments.createdtime,
                    vtiger_receivedpayments.overdue,
                    vtiger_receivedpayments.unit_price,
                    vtiger_receivedpayments.modifiedtime,
                    vtiger_receivedpayments.paytitle,
                    vtiger_receivedpayments.owncompany,
                    IF(vtiger_receivedpayments.fallinto = 1,'是','否') AS fallinto,
                    vtiger_servicecontracts.contract_no AS relatetoid,
                    vtiger_servicecontracts.servicecontractsid AS relatetoid_reference,       
                    (SELECT CONCAT( last_name, '[',IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE vtiger_receivedpayments.checkid = vtiger_users.id ) AS checkid,
                    vtiger_servicecontracts.total,	
                    vtiger_receivedpayments.exchangerate,
                    vtiger_receivedpayments.accountscompany,
                    vtiger_receivedpayments.receivementcurrencytype,
                    vtiger_receivedpayments.standardmoney,
                    vtiger_achievementallot.tyuncost,
                    vtiger_achievementallot.othercost,
                    vtiger_achievementallot.workorderdate,
                    vtiger_achievementallot.firstmarketprice,
                    vtiger_achievementallot.secondmarketprice,
                    vtiger_achievementallot.idccost,
                    vtiger_salesorder.modulestatus,
                    if(vtiger_servicecontracts.multitype>0,'是','否') AS multitype,
                    vtiger_products.productname AS productid,
                    vtiger_account.accountname,
                    vtiger_servicecontracts.signdate,
                    (SELECT sum(unit_price) FROM vtiger_receivedpayments AS AONE WHERE AONE.relatetoid=vtiger_receivedpayments.relatetoid AND AONE.deleted=0 AND AONE.receivedstatus='normal') AS cdtamount,
                    (SELECT FLOOR(vtiger_salesorderproductsrel.agelife/12) FROM vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.servicecontractsid=vtiger_servicecontracts.servicecontractsid AND multistatus in(0,1) LIMIT 1) AS agelife,
                    vtiger_servicecontracts.servicecontractstype,
                    vtiger_salesorder.salesorder_no,
                    vtiger_receivedpayments.receivedpaymentsid
                FROM vtiger_receivedpayments
                RIGHT JOIN vtiger_achievementallot ON vtiger_receivedpayments.receivedpaymentsid = vtiger_achievementallot.receivedpaymentsid
                LEFT JOIN vtiger_servicecontracts on vtiger_servicecontracts.servicecontractsid=vtiger_receivedpayments.relatetoid 
                LEFT JOIN vtiger_account ON vtiger_servicecontracts.sc_related_to=vtiger_account.accountid
                LEFT JOIN vtiger_products ON vtiger_products.productid=vtiger_servicecontracts.productid
                LEFT JOIN vtiger_salesorder ON (vtiger_salesorder.servicecontractsid=vtiger_servicecontracts.servicecontractsid AND vtiger_salesorder.modulestatus<>'c_cancel')
                WHERE
                    vtiger_receivedpayments.deleted=0
                AND vtiger_servicecontracts.modulestatus='c_complete'
                AND vtiger_receivedpayments.receivedstatus='normal'
                AND vtiger_servicecontracts.parent_contracttypeid=2";
    }
    /**
     * T云业绩分成之合
     * @return string
     */
    public function getListviewCountSqlTyun(){
        return "SELECT
                    count(1) AS counts
                FROM vtiger_receivedpayments
                RIGHT JOIN vtiger_achievementallot ON vtiger_receivedpayments.receivedpaymentsid = vtiger_achievementallot.receivedpaymentsid
                LEFT JOIN vtiger_servicecontracts on vtiger_servicecontracts.servicecontractsid=vtiger_receivedpayments.relatetoid 
                LEFT JOIN vtiger_account ON vtiger_servicecontracts.sc_related_to=vtiger_account.accountid
                LEFT JOIN vtiger_products ON vtiger_products.productid=vtiger_servicecontracts.productid
                LEFT JOIN vtiger_salesorder ON (vtiger_salesorder.servicecontractsid=vtiger_servicecontracts.servicecontractsid AND vtiger_salesorder.modulestatus<>'c_cancel')
                WHERE
                    vtiger_receivedpayments.deleted=0
                AND vtiger_servicecontracts.modulestatus='c_complete'
                AND vtiger_receivedpayments.receivedstatus='normal'
                AND vtiger_servicecontracts.parent_contracttypeid=2";
    }
}