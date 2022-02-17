<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Reporting_ListView_Model extends Vtiger_ListView_Model {

	//去除添加按钮
	 public function getListViewLinks($linkParams) {
		return $links;
		exit;
	} 
	public function getListViewEntries($pagingModel) {}
	public function getUserWhere(){}
	public function getListViewHeaders(){} 
	public function getListViewCount(){}
}
