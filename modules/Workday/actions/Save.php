<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
//require_once('include/database/PearDatabase.php');//数据库链接

class Workday_Save_Action extends Vtiger_Save_Action {

	public function process(Vtiger_Request $request) {
// 		$db = PearDatabase::getInstance();
// 		$strSql = "select * vtiger_workday where dateday=?";
// 		$params = array($_REQUEST["dateday"]);
// 		$result=$db->pquery($insertSql, $params);
// 		while($row == $db->fetchByAssoc($result)){
// 			exit();
// 		}
		parent::process($request);
	}
}
