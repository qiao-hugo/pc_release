<?php

class InputInvoice_Edit_View extends Vtiger_Edit_View
{
    public function checkPermission(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $record = $request->get('record');
        $recordPermission = Users_Privileges_Model::isPermitted($moduleName, 'EditView', $record);
        if (!$recordPermission) {
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
            exit;
        }
        global $isallow,$current_user;

        if(in_array($moduleName, $isallow)&&$record){
            $recordModel=Vtiger_Record_Model::getInstanceById($record,$moduleName);
            if(!empty($recordModel)&&$recordModel){
                $module=$recordModel->entity->column_fields;
                $moduleStatus=$module['modulestatus'];
                if(in_array($moduleStatus,array('b_check' ,'c_complete'))){
                    throw new AppException('状态 '.vtranslate($moduleStatus,$moduleName).' 不允许当前的操作');
                    exit;
                }
            }
        }
    }

    public function process(Vtiger_Request $request)
    {
        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();
        global $current_user;

        $record = $request->get('record');
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

        $viewer->assign('CURRENT_USER', $current_user);

        include "crmcache/departmentanduserinfo.php";
        $viewer->assign('DEPARTMENTUSER', $record ? array($recordModel->get('departmentid') => $departlevel[$recordModel->get('departmentid')]) : $departlevel);
        $recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_EDIT);
        $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
        $viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());//UI字段生成位置
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('RECORD', $recordModel);//编辑页面显示不可编辑字段内容
        $viewer->assign('RELATIONSHIP_OR', explode(',', $recordModel->get('relationship_or')));
        $viewer->assign('RELATIONSHIP_AND', explode(',', $recordModel->get('relationship_and')));
        $isRelationOperation = $request->get('relationOperation');
        $viewer->assign("BILLPROPERTY",$request->get("billproperty"));
        $viewer->assign("APPLICATIONTYPE",$request->get("applicationtype"));
        //if it is relation edit
        $viewer->assign('IS_RELATION_OPERATION', $isRelationOperation);
        $correspondingIds = CompayCode_Record_Model::companyUserIds("co");
        $responsibility = CompayCode_Record_Model::companyUserIds("rs");
        $viewer->assign('IS_ACCOUNT', false);
        if(in_array($current_user->id,$correspondingIds) || in_array($current_user->id,$responsibility)){
            $viewer->assign('IS_ACCOUNT', true);
        }
        if ($isRelationOperation) {
            $viewer->assign('SOURCE_MODULE', $request->get('sourceModule'));
            $viewer->assign('SOURCE_RECORD', $request->get('sourceRecord'));
        }

        $viewer->view('EditView.tpl', $moduleName);
    }
}