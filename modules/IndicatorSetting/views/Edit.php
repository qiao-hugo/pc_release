<?php

class IndicatorSetting_Edit_View extends Vtiger_Edit_View
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

        global $current_user,$adb;
        if ($current_user->is_admin != 'on') {
            if($record>0){
                $query='SELECT modifiedby FROM `vtiger_indicatorsetting` WHERE id=?';
                $result=$adb->pquery($query,array($record));
                if(empty($result->fields['modifiedby']) || $result->fields['modifiedby']==$current_user->id){
                }else{
                    function findreport($reportsModel,&$array=array()){
                        if($reportsModel->id==1 || $reportsModel->id==38){
                            return $array;
                        }
                        $reportsModel = Users_Privileges_Model::getInstanceById($reportsModel->reports_to_id);
                        $array[]=$reportsModel->id;
                        if($reportsModel->reports_to_id ==38 || $reportsModel->reports_to_id ==1 || empty($reportsModel->reports_to_id)){
                            return $array;
                        }
                        return findreport($reportsModel,$array);
                    }
                    $reportsModel = Users_Privileges_Model::getInstanceById($result->fields['modifiedby']);
                    $array=findreport($reportsModel);
                    if(!in_array($current_user->id,$array)){
                        throw new AppException('上级已确认！您没有权限进行当前操作');
                        exit;
                    }
                }
            }
            $is_skip_role = TelStatistics_Module_Model::isSkipThisRole($moduleName, 'edit', $current_user->roleid);
            if (!$is_skip_role) {
                throw new AppException('您没有权限进行当前操作');
                exit;
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
        //获取员工阶段列表
        $staff_stages = IndicatorSetting_Module_Model::$staff_stages;
        if (!$recordModel->get('staff_stage')) {
            $viewer->assign('STAFF_STAGES', $staff_stages);
        } else {
            $viewer->assign('STAFF_STAGES', array($recordModel->get('staff_stage') => $staff_stages[$recordModel->get('staff_stage')]));
        }
        //获取关系列表
        $relationships = IndicatorSetting_Module_Model::$relationships;
        $viewer->assign('RELATION_SHIPS', $relationships);
        include "crmcache/departmentanduserinfo.php";
        $viewer->assign('DEPARTMENTUSER', $record ? array($recordModel->get('departmentid') => $departlevel[$recordModel->get('departmentid')]) : $departlevel);
        $already_set_departments = IndicatorSetting_Record_Model::getAlreadySetDepartments();
        $viewer->assign('ALREADY_SET_DEPARTMENTS', $already_set_departments);
        $recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_EDIT);
        $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
        $viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());//UI字段生成位置
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('RECORD', $recordModel);//编辑页面显示不可编辑字段内容
        $viewer->assign('RELATIONSHIP_OR', explode(',', $recordModel->get('relationship_or')));
        $viewer->assign('RELATIONSHIP_AND', explode(',', $recordModel->get('relationship_and')));
        $isRelationOperation = $request->get('relationOperation');

        $viewer->assign('BASIC_OPERATORS', IndicatorSetting_Module_Model::$operations);
        $viewer->assign('OPERATOR_OPERATORS', IndicatorSetting_Module_Model::$operate_operatoes);
        //if it is relation edit
        $viewer->assign('IS_RELATION_OPERATION', $isRelationOperation);
        if ($isRelationOperation) {
            $viewer->assign('SOURCE_MODULE', $request->get('sourceModule'));
            $viewer->assign('SOURCE_RECORD', $request->get('sourceRecord'));
        }
        $viewer->assign('SPECIAL_OPERATORS', IndicatorSetting_Record_Model::getSpecialOperationByIndicatorSettingId($record));
        $viewer->view('EditView.tpl', $moduleName);
    }
}