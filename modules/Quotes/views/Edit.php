<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Class Quotes_Edit_View extends Vtiger_Edit_View {

	public function process(Vtiger_Request $request) {
	    //ini_set('display_errors','on'); error_reporting(E_ALL);
	    $viewer = $this->getViewer($request);
		$moduleName = $request->getModule();
		$record = $request->get('record');
		$sourceRecord = $request->get('sourceRecord');
		$sourceModule = $request->get('sourceModule');
		//判断是否是通过销售机会进来的
		if((int)$sourceRecord>0){
			$recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
			
			$parentRecordModel = Vtiger_Record_Model::getInstanceById($sourceRecord, $sourceModule);
			//$recordModel->setParentRecordData($parentRecordModel);
		}
		parent::process($request);
	}
}