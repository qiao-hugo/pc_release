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
 * ModComments Record Model
 */
class Visitsign_Record_Model extends Vtiger_Record_Model {
	/**
	 * 更新跟进状态
	 * @author steel
	 * @param $visitingorderid
	 */
	public static function updateVisitingOrderFollowstatus($visitingorderid) {
		global $current_user;
		$db = PearDatabase::getInstance();
		$query='SELECT createdtime from vtiger_crmentity WHERE crmid=?';
		$result=$db->pquery($query,array($visitingorderid));
		$result_time=$db->query_result($result, 0, 'createdtime');
		$createtime=strtotime($result_time)+24*3600;
		$now=time();
		$updateSql="UPDATE vtiger_visitingorder SET followstatus='followup',followtime=?,followid=?";
		$updateSql.=$createtime>$now?",dayfollowup='是'":"";
		$datetime=date('Y-m-d H:i:s');
		$updateSql.=" WHERE visitingorderid=?";
		$db->pquery($updateSql, array($datetime,$current_user->id,$visitingorderid));
	}
}