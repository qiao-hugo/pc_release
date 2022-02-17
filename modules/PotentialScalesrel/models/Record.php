<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class PotentialScalesrel_Record_Model extends Vtiger_Record_Model {

	public static function getRelateScales($record){
		
		$db = PearDatabase::getInstance();
		$sql="select t.*,concat(first_name,last_name) as username from (select * from vtiger_potentialscalesrel where potentialsid=? and scalestatus='actioning') as t left join vtiger_users on t.scaleid=vtiger_users.id";
		
		$result=$db->pquery($sql,array($record));
		$temp=array();
		for($i=0; $i<$db->num_rows($result); $i++) {
			$potentialscalename = $db->query_result($result, $i, 'potentialscalename');
			$scaleid =	$db->query_result($result, $i, 'scaleid');
			
			$username =	$db->query_result($result, $i, 'username');
			$scaletime =	$db->query_result($result, $i, 'scaletime');
			$scale =	$db->query_result($result, $i, 'scale');
			$temp[]=array('potentialscalename'=>$potentialscalename,'scaleid'=>$scaleid,'username'=>$username,'scaletime'=>$scaletime,'scale'=>$scale);	
		}
		return $temp;
	}
	
}
