<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Class ReceivedPaymentsCollate_Edit_View extends Vtiger_Edit_View
{
    public function checkPermission(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $record = $request->get('record');
        $recordModel = Vtiger_Record_Model::getInstanceById($record, $moduleName);
        $moduleData = $recordModel->getData();
        if (in_array($moduleData['modulestatus'], array('c_complete', 'b_check'))) {
            throw new AppException('状态 ' . vtranslate($moduleData['modulestatus'], $moduleName) . '不允许编辑');
            exit;
        }
    }

    public function process(Vtiger_Request $request)
    {
        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();
        $record = $request->get('record');
        $sc_record=$request->get("sc_record");
        $receivedPaymentsRecordModel='';
        if($sc_record){
            $module_Model = Vtiger_Module_Model::getCleanInstance('ReceivedPayments');
            $collate_premission = $module_Model->exportGrouprt('ReceivedPayments', 'COLLATE');
            if (!$collate_premission) {
                throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
            }
            $recordModel2 = Vtiger_Record_Model::getInstanceById($sc_record, 'ReceivedPayments');
            $moduleData = $recordModel2->getData();
            if ($moduleData['last_collate_status'] == 'fit') {
                throw new AppException('回款已核对符合,不允许编辑!');
            } elseif (empty($moduleData['last_collate_status']) && $moduleData['first_collate_status'] == 'fit') {
                throw new AppException('回款已核对符合,不允许编辑!');
            }

            $receivedPaymentsRecordModel=ReceivedPayments_Record_Model::getInstanceById($sc_record,"ReceivedPayments");
        }
        if (!empty($record)) {
            $recordModel = $this->record ? $this->record : Vtiger_Record_Model::getInstanceById($record, $moduleName);
            $viewer->assign('RECORD_ID', $record);
            $viewer->assign('MODE', 'edit');
        } else {
            $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
            $viewer->assign('MODE', '');
            $viewer->assign('RECORD_ID', '');
        }
        if (!$this->record) {
            $this->record = $recordModel;
        }

        $moduleModel = $recordModel->getModule();
        //读取模块的字段
        $fieldList = $moduleModel->getFields();

        //取交集?还不知道有什么用
        $requestFieldList = array_intersect_key($request->getAll(), $fieldList);

        if (!empty($requestFieldList)) {
            foreach ($requestFieldList as $fieldName => $fieldValue) {
                $fieldModel = $fieldList[$fieldName];
                if ($fieldModel->isEditable()) {
                    $recordModel->set($fieldName, $fieldModel->getDBInsertValue($fieldValue));
                }
            }
        }
        if($receivedPaymentsRecordModel){
            $recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($receivedPaymentsRecordModel, 'Edit');
        }else{
            $recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, 'Edit');
        }

        $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
        $viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());//UI字段生成位置
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());//执行好多次，真是
        $viewer->assign('RECORD', $recordModel);//编辑页面显示不可编辑字段内容
        $viewer->assign('RECORD_ID', $record);//编辑页面显示不可编辑字段内容
        if($sc_record){
            $viewer->assign('RECEIVEDPAYMENTSID', $sc_record);//编辑页面显示不可编辑字段内容
        }else{
            $viewer->assign('RECEIVEDPAYMENTSID', $recordModel->getReceivedPaymentSidByCollate($record));//编辑页面显示不可编辑字段内容
        }
        $isRelationOperation = $request->get('relationOperation');
        $viewer->assign('IS_RELATION_OPERATION', $isRelationOperation);
        if ($isRelationOperation) {
            $viewer->assign('SOURCE_MODULE', $request->get('sourceModule'));
            $viewer->assign('SOURCE_RECORD', $request->get('sourceRecord'));
        }
        $viewer->view('EditView.tpl', $moduleName);
    }

    public function getStructure($recordModel) {
        if(!empty($this->structuredValues)) {
            return $this->structuredValues;
        }

        $values = array();
//        $recordModel = $this->getRecord();
        $recordExists = !empty($recordModel);
        $moduleModel = $this->getModule();
        $blockModelList = $moduleModel->getBlocks();
        foreach($blockModelList as $blockLabel=>$blockModel) {
            $fieldModelList = $blockModel->getFields();
            if (!empty ($fieldModelList)) {
                $values[$blockLabel] = array();
                foreach($fieldModelList as $fieldName=>$fieldModel) {
                    if($fieldModel->isViewable()) {
                        if($recordExists) {
                            $fieldModel->set('fieldvalue', $recordModel->get($fieldName));
                        }
                        $values[$blockLabel][$fieldName] = $fieldModel;
                    }
                }
            }
        }

        $this->structuredValues = $values;
        return $values;
    }
}
