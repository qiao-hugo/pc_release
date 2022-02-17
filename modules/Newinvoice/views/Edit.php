<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Class Newinvoice_Edit_View extends Vtiger_Edit_View {

    public function checkPermission(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $record = $request->get('record');
        $recordPermission = Users_Privileges_Model::isPermitted($moduleName, 'EditView', $record);
        if(!$recordPermission) {
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
            exit;
        }
        if(!empty($record)) {
            global $current_user;
            // 判断当前
            $recordModel=Vtiger_Record_Model::getInstanceById($record,$moduleName);
            $module=$recordModel->entity->column_fields;
            $moduleStatus=$module['modulestatus'];
            if($current_user->is_admin=='on'){
                return ;
            }
            if(in_array($moduleStatus,array('a_exception','a_normal'))){
                if($current_user->id==$module['assigned_user_id']){
                    return ;
                }else{
                    throw new AppException('只有发票申请人可编辑！');
                    exit;
                }
            }
            $sql = "SELECT vtiger_workflowstages.workflowstagesflag,vtiger_workflowstages.isrole, vtiger_salesorderworkflowstages.salesorderworkflowstagesid  FROM `vtiger_salesorderworkflowstages` LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid WHERE vtiger_salesorderworkflowstages.salesorderid = ? AND vtiger_salesorderworkflowstages.modulename = 'Newinvoice' AND vtiger_salesorderworkflowstages.isaction = 1 AND vtiger_workflowstages.workflowstagesflag='open_invoice'";
            global $adb;
            $result = $adb->pquery($sql,array($record));
            $res_cnt = $adb->num_rows($result);
            if($res_cnt > 0) {
                $isrole=$adb->query_result($result, 0, 'isrole');
                $t_arr = explode(' |##| ', $isrole);
                if (!in_array($current_user->roleid, $t_arr)) {
                    throw new AppException('状态 '.vtranslate($moduleStatus,$moduleName).' 不允许当前的操作');
                    exit;
                }
                /*$workObj=new WorkFlowCheck_ListView_Model();
                $allStagers = $workObj->getActioning($moduleName, $record);
                if(!isset($allStagers[$salesorderworkflowstagesid])) {
                    throw new AppException('状态 '.vtranslate($moduleStatus,$moduleName).' 不允许当前的操作');
                    exit;
                }*/
            } else {
                throw new AppException('状态 '.vtranslate($moduleStatus,$moduleName).' 不允许当前的操作');
                exit;
            }
        }
        

        /*global $isallow,$current_user;
        if(in_array($moduleName, $isallow)&&$record){
            $recordModel=Vtiger_Record_Model::getInstanceById($record,$moduleName);
            if(!empty($recordModel)&&$recordModel){
                $module=$recordModel->entity->column_fields;
                $moduleStatus=$module['modulestatus'];

                if($request->get('Negative')=='NegativeEdit'&& $recordModel->getModule()->isPermitted('NegativeEdit')){
                    //开票负数
                    return;
                }
                if($request->get('isDuplicate') == true && $recordModel->getModule()->isPermitted('DuplicatesHandling')){
                    //复制
                    return ;
                }
                if(!getIsEditOrDel('edit', $moduleStatus)){
                    //加入财务部门可以在审核中修改发票
                    if(!Invoice_Record_Model::nodeCheck()){
                        throw new AppException('状态 '.vtranslate($moduleStatus,$moduleName).' 不允许当前的操作');
                        exit;
                    }
                }
            }
        }*/
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
        }
        if(!$this->record){
            $this->record = $recordModel;
        }

        $moduleModel = $recordModel->getModule();
        $fieldList = $moduleModel->getFields();
        $requestFieldList = array_intersect_key($request->getAll(), $fieldList);

        foreach($requestFieldList as $fieldName=>$fieldValue){
            $fieldModel = $fieldList[$fieldName];
            $specialField = false;
            // We collate date and time part together in the EditView UI handling
            // so a bit of special treatment is required if we come from QuickCreate
            if ($moduleName == 'Calendar' && empty($record) && $fieldName == 'time_start' && !empty($fieldValue)) {
                $specialField = true;
                // Convert the incoming user-picked time to GMT time
                // which will get re-translated based on user-time zone on EditForm
                $fieldValue = DateTimeField::convertToDBTimeZone($fieldValue)->format("H:i");

            }

            if ($moduleName == 'Calendar' && empty($record) && $fieldName == 'date_start' && !empty($fieldValue)) {
                $startTime = Vtiger_Time_UIType::getTimeValueWithSeconds($requestFieldList['time_start']);
                $startDateTime = Vtiger_Datetime_UIType::getDBDateTimeValue($fieldValue." ".$startTime);
                list($startDate, $startTime) = explode(' ', $startDateTime);
                $fieldValue = Vtiger_Date_UIType::getDisplayDateValue($startDate);
            }
            if($fieldModel->isEditable() || $specialField) {
                $recordModel->set($fieldName, $fieldModel->getDBInsertValue($fieldValue));
            }
        }

        $recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_EDIT);

        $picklistDependencyDatasource = Vtiger_DependencyPicklist::getPicklistDependencyDatasource($moduleName);
        $taxType = $recordModel->entity->column_fields['taxtype'];
        /* 判断是否是开立发票审核节点 */
        $open_invoice=false;
        if ($taxType=='invoice') {
            $sql = "SELECT vtiger_salesorderworkflowstages.salesorderworkflowstagesid FROM `vtiger_salesorderworkflowstages` 
            LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid 
            WHERE vtiger_salesorderworkflowstages.salesorderid = ? AND vtiger_salesorderworkflowstages.modulename = 'Newinvoice' 
            AND vtiger_salesorderworkflowstages.isaction = 1 AND vtiger_workflowstages.workflowstagesflag='open_invoice'";
            global $adb;
            $result = $adb->pquery($sql,array($record));
            if($adb->num_rows($result) > 0) {
                $open_invoice = true;
            }
        }
        $viewer->assign('open_invoice', $open_invoice);
        $viewer->assign('PICKIST_DEPENDENCY_DATASOURCE',Zend_Json::encode($picklistDependencyDatasource));
        $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
        //$yyy = $recordStructureInstance->getStructure();
        $viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());//UI字段生成位置
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('CURRENTDATE', date('Y-n-j'));
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('BILLINGID',$recordModel->entity->column_fields['billingid']);
        $viewer->assign('INVOICE_LIST',Newinvoice_Record_Model::editInoviceList($recordModel->entity->column_fields['contractid']));
        $viewer->assign('INVOICE_PAYMENTS',$recordModel->getModule()->isPermitted('ReturnTicket'));
        $viewer->assign('INVOICE_LIST_STATUS',$recordModel->entity->column_fields['modulestatus']);
        $viewer->assign('TAXTYPE', $taxType);
        $isRelationOperation = $request->get('relationOperation');
        $viewer->assign('RECORD',$recordModel);
		
        $temp=$recordStructureInstance->getStructure();
        $tinvoicedisplay=$temp['LBL_TERMS_INFORMATION']['invoicecode'];
        $invoicedisplay=(!is_object($tinvoicedisplay)||$tinvoicedisplay->get('editread') || ($tinvoicedisplay->get('readonly') == 0 and !empty($record)))?2:1;
        $viewer->assign('INVOICEDISPLAY',$invoicedisplay);//是否可编辑
        $accessibleUsers = get_username_array('');
        $viewer->assign('ACCESSIBLE_USERS',$accessibleUsers);
        $viewer->assign('MOREINVOICES',Newinvoice_Record_Model::getMoreinvoice($record));
        //if it is relation edit
        $viewer->assign('IS_RELATION_OPERATION', $isRelationOperation);
        if($isRelationOperation) {
            $viewer->assign('SOURCE_MODULE', $request->get('sourceModule'));
            $viewer->assign('SOURCE_RECORD', $request->get('sourceRecord'));
        }

        global $current_user;
        if (empty($record)) {
            $viewer->assign('USERID', $current_user->id);
        } else {
            $viewer->assign('MODULESTATUS', $recordModel->entity->column_fields['modulestatus']);  //流程状态
        }
 
        $invoicetype = $recordModel->entity->column_fields['invoicetype']; 
        if (!empty($record)) {
            $newinvoiceData = Newinvoice_Record_Model::getNewinvoicerayment($record);
            $viewer->assign('NEWINVOICEDATA', $newinvoiceData);
            
            if($invoicetype == 'c_normal') {
                $viewer->assign('IS_UPDATE_NEWINVOICERAYMENT', 1);
            }
            $account_id = $recordModel->entity->column_fields['account_id']; 
            $invoicerayment = Newinvoice_Record_Model::getNewinvoiceraymentInfo($account_id);
            $rayment_json_data = json_encode($invoicerayment);

            $viewer->assign('RAYMENT_JSON_DATA', $rayment_json_data);
            $viewer->assign('RAYMENT_INFO', $invoicerayment);

            //获取订单渠道信息
            $dongchaliList=Newinvoice_Record_Model::getDongchaliList($record);
            $viewer->assign('DONGCHALILIST', $dongchaliList);
        }

        // 获取
        $viewer->assign('ALL_BILLINGCONTENT', Newinvoice_Record_Model::getAllBillingcontent());
        
        $viewer->assign('MAX_UPLOAD_LIMIT_MB', Vtiger_Util_Helper::getMaxUploadSize());
        $viewer->assign('LBL_EDIT_NEGATIVE', $request->get('Negative'));
        $viewer->assign('MAX_UPLOAD_LIMIT', vglobal('upload_maxsize'));
        $viewer->assign('INVOICEDTOTAL',$recordModel->get("taxtotal"));
        $viewer->assign('IS_FP_ADMIN', Newinvoice_Record_Model::isFpAdmin($current_user->id,$recordModel->entity->column_fields['companycode']));//判断是否管理员
        $realOperate = $_SERVER['HTTP_REFERER'];
        if(strstr($realOperate,'realoperate')){
            //判断是否从待审核页面来的
            $viewer->assign('IS_REAL_OPERATE',1);
        }
        $viewer->view('EditView.tpl', $moduleName);
    }
}