<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class IndicatorSetting_DeleteAjax_Action extends Vtiger_DeleteAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        $moduleName = $request->get('module');
        $recordId = $request->get('record');
        $mode = $request->get('mode');

        global $current_user;
        if ($mode == 'special_operation') {
            $db = PearDatabase::getInstance();
            //记录删除历史
            IndicatorSetting_Record_Model::saveSpecialOperationHistories($recordId,$current_user->id);

            $db->pquery('DELETE FROM vtiger_special_operation WHERE id=?', array($recordId));
            $result = array('success' => true, 'message' => ('删除成功'));

            $response = new Vtiger_Response();
            $response->setResult($result);
            $response->emit();
            exit;
        }
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
        $recordModel->delete();

    }
}
