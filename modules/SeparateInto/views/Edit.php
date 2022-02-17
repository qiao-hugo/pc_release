<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Class SeparateInto_Edit_View extends Vtiger_Edit_View
{

    function __construct()
    {
        parent::__construct();
    }
    public function process(Vtiger_Request $request) {
        $viewer = $this->getViewer ($request);
        $moduleName = $request->getModule();
        $record = $request->get('record');
        if(!empty($record) && $request->get('isDuplicate') == true) {
            $recordModel = $this->record?$this->record:Vtiger_Record_Model::getInstanceById($record, $moduleName);
            $viewer->assign('MODE', '');
        }else if(!empty($record)) {
            $recordModel = $this->record?$this->record:Vtiger_Record_Model::getInstanceById($record, $moduleName);
            $viewer->assign('RECORD_ID', $record);
            $viewer->assign('MODE', 'edit');
        } else {
            $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
            $viewer->assign('MODE', '');
            $viewer->assign('RECORD_ID','');
        }
        if(!$this->record){
            $this->record = $recordModel;
        }
        global $adb;
        $accessibleUsers = get_username_array('1=1');  //人员信息
        $accessibleUsersDivide=get_username_array_divide('1=1');
        $getcompanysql ="SELECT owncompany FROM `vtiger_owncompany`";
        $company = $adb->pquery($getcompanysql,array());
        $owncompany = array();
        $sums=$adb->num_rows($company);
        if($sums>0){
            while($row = $adb->fetchByAssoc($company)){
                $owncompany[$row['owncompany']] = $row['owncompany'];
            }
        }
        if($record){$contracts_divide = $recordModel->servicecontracts_divide($record);}
        $viewer->assign('CONTRACTS_DIVIDE',$contracts_divide); //合同分成表
        $viewer->assign('ACCESSIBLE_USERS',$accessibleUsers);//人员
        $viewer->assign('ACCESSIBLE_USERS_DIVIDE',$accessibleUsersDivide);//人员
        $viewer->assign('OWNCOMPANY',$owncompany);//所有公司
        $recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel,'Edit');
        $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
        $viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());//UI字段生成位置

        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());//执行好多次，真是
        $viewer->assign('RECORD',$recordModel);//编辑页面显示不可编辑字段内容
        $isRelationOperation = $request->get('relationOperation');
        $viewer->assign('IS_RELATION_OPERATION', $isRelationOperation);
        if($isRelationOperation) {
            $viewer->assign('SOURCE_MODULE', $request->get('sourceModule'));
            $viewer->assign('SOURCE_RECORD', $request->get('sourceRecord'));
        }
        $viewer->view('EditView.tpl', $moduleName);
    }
}