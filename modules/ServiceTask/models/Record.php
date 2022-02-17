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
 * Vtiger Entity Record Model Class
 */
class ServiceTask_Record_Model extends Vtiger_Record_Model {
	/**
	 * 获取任务包名称
	 * @param $taskpackageid 任务包id
	 * @return 任务包名称
	 */
	public static function getTaskPackageName($taskpackageid) {
		$db = PearDatabase::getInstance();
		$sqlQuery="select taskpackagename from vtiger_taskpackage where taskpackageid=?";
		$values = array();
		$result = $db->pquery($sqlQuery, array($taskpackageid));
		return $db->query_result($result,0,'taskpackagename');
	}
}
