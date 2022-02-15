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
class ReceivedPaymentsThrow_Record_Model extends Vtiger_Record_Model { 

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
					vtiger_receivedpayments_throw.userid = vtiger_users.id
			) AS userid,
	vtiger_receivedpayments_throw.id,
  vtiger_receivedpayments_throw.date,
	vtiger_receivedpayments.unit_price,
	vtiger_receivedpayments.paytitle,
	vtiger_receivedpayments.reality_date
FROM
	vtiger_receivedpayments
RIGHT  JOIN  vtiger_receivedpayments_throw ON vtiger_receivedpayments.receivedpaymentsid = vtiger_receivedpayments_throw.receivepaymentid
WHERE
	1 = 1";
	     return $listQuery;
    }
}