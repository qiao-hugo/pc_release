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

class Receipt_DeleteAjax_Action extends Vtiger_DeleteAjax_Action {
    public function process(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$recordId = $request->get('record');
		$db=PearDatabase::getInstance();
		$sql="update vtiger_receipt set deleted = 1 where receiptid=?";
        $db->pquery($sql,array($recordId));
		$response = new Vtiger_Response();
		$response->setResult(array());
		$response->emit();
	}

    function checkPermission(Vtiger_Request $request) {
        $recordId = $request->get('record');
        $adb =PearDatabase::getInstance();
        $sql = "select * FROM vtiger_receipt where receiptid=?";
        $sel_result = $adb->pquery($sql, array($recordId));
        $rawData = $adb->query_result_rowdata($sel_result, 0);
        if($rawData['modulestatus'] == 'b_check' || $rawData['modulestatus'] == 'c_complete'){
            throw new AppException('当前状态不允许当前的操作');
        }

//        $applyuserid = $rawData['applyuserid'];
//        $moduleName = $request->getModule();
//        $recordId = $request->get('record');
//
//        $currentUserPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
//        if(!$currentUserPrivilegesModel->isPermitted($moduleName, 'Delete', $recordId)) {
//            throw new AppException('LBL_PERMISSION_DENIED');
//        }
//        global $isallow;
//        if(in_array($moduleName, $isallow)){
//            $record=Vtiger_Record_Model::getInstanceById($recordId,$moduleName);
//            if(!empty($record)){
//                $module=$record->getData();
//                $moduleStatus=$module['modulestatus'];
//                if(!getIsEditOrDel('delete',$moduleStatus)){
//                    throw new AppException('状态'.vtranslate($moduleStatus,$moduleName).'不允许当前的操作');
//                }
//            }
//        }
        //end
    }
}
