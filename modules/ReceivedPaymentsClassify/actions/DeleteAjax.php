<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 *************************************************************************************/

class ReceivedPaymentsClassify_DeleteAjax_Action extends Vtiger_DeleteAjax_Action {
    public function process(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$recordId = $request->get('record');

		$db=PearDatabase::getInstance();
		$isreceived=$db->pquery('select discontinued from vtiger_receivedpayments where receivedpaymentsid=?',array($recordId));
		if($isreceived->fields['0']==1){
		    throw new AppException(vtranslate($moduleName).' '.vtranslate('LBL_NOT_ACCESS'));
		    exit;
		}
		$recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
		$recordModel->delete();

		$cvId = $request->get('viewname');
		$response = new Vtiger_Response();
		$response->setResult(array('viewname'=>$cvId, 'module'=>$moduleName));
		$response->emit();
	}
}
