<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Class SupplierContracts_Edit_View extends Vtiger_Edit_View
{
    protected $record = false;

    function __construct()
    {
        parent::__construct();
    }

    public function checkPermission(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $record = $request->get('record');
        
        //end
        $recordPermission = Users_Privileges_Model::isPermitted($moduleName, 'EditView', $record);
        if (!$recordPermission) {
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
        }
        
        //1.编辑权限，有上下级关系的，或者本人，或者有审核权限的人
        /* $isrejectid=false;
        if(!empty($record)){
            if(isset($_SESSION['isyourcode'])&&$_SESSION['isyourcode']==$moduleName.$record){
                //偶审核权限的人，通过isyourcode值来判断
                $isrejectid=true;
            }else{
                $user=getAccessibleUsers($moduleName,'Edit',true);

                $recordModule=Vtiger_Record_Model::getInstanceById($record, $moduleName);
                $recordField=$recordModule->getData();
                if(isset($recordField['assigned_user_id'])){
                    $id=$recordField['assigned_user_id'];
                }elseif(isset($recordField['smcreatorid'])){
                    $id=$recordField['smcreatorid'];
                }
                if(is_array($user)&& !in_array($id,$user)){
                    throw new AppException(vtranslate('没有访问权限'));
                }
            }
            &&!$isrejectid
        } */

        //echo getAccessibleUsers();
        //young.yang 2015-1-3 增加编辑页面，对于流程状态的控制，某些状态不允许编辑
        global $isallow,$current_user,$adb;
        if (in_array($moduleName, $isallow) && $record && !$request->isAjax()) {
            $recordModel = Vtiger_Record_Model::getInstanceById($record, $moduleName);
            if (!empty($recordModel) && $recordModel) {
                $module = $recordModel->getData();
                $moduleStatus = $module['modulestatus'];
                if (!empty($moduleStatus) && !getIsEditOrDel('edit', $moduleStatus))
                {
                    $arrStatus=array('c_recovered','c_complete');//'c_receive',
                    if(in_array($moduleStatus,$arrStatus))
                    {    //在合同收回后财务人员可编加
                        //$userid=getDepartmentUser('H25');
                        //财务审核节点编辑
                        $moduleModel=$recordModel->getModule();
                        if($moduleModel->exportGrouprt('SupplierContracts','Received')){
                            $companycode=$module['companycode'];
                            if(!empty($companycode)){
                                $query='SELECT 1 FROM vtiger_invoicecompanyuser WHERE invoicecompany=? AND userid=?';
                                $result=$adb->pquery($query,array($companycode,$current_user->id));
                                if($adb->num_rows($result)>0){
                                    return ;
                                }else{
                                    throw new AppException('合同主体:'.$module['invoicecompany'].'的合同,不允许编辑!');
                                }
                            }
                            return ;
                        }
                    }
                    throw new AppException('状态 ' . vtranslate($moduleStatus, $moduleName) . ' 不允许当前的操作');
                }
                if($module['sideagreement']==1)
                {
                    throw new AppException('补充协议不允许编辑!');
                }
            }
        }
        //end
        
    }

    public function process(Vtiger_Request $request){
        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();
        $record = $request->get('record');
        if (!empty($record) && $request->get('isDuplicate') == true) { //作废
            $recordModel = $this->record ? $this->record : Vtiger_Record_Model::getInstanceById($record, $moduleName);
            $viewer->assign('MODE', '');
        } else if (!empty($record)) { //编辑
            $recordModel = $this->record ? $this->record : Vtiger_Record_Model::getInstanceById($record, $moduleName);
            $viewer->assign('RECORD_ID', $record);
            $viewer->assign('MODE', 'edit');
        } else { //新增
            $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
            $viewer->assign('MODE', '');
        }
        if (!$this->record) {
            $this->record = $recordModel;
        }

        $moduleModel = $recordModel->getModule();
        $fieldList = $moduleModel->getFields();

        $requestFieldList = array_intersect_key($request->getAll(), $fieldList); //这边取得交集竟然是空的。下面的循环就出错了。
        foreach ($requestFieldList as $fieldName => $fieldValue) {
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
                $startDateTime = Vtiger_Datetime_UIType::getDBDateTimeValue($fieldValue . " " . $startTime);
                list($startDate, $startTime) = explode(' ', $startDateTime);
                $fieldValue = Vtiger_Date_UIType::getDisplayDateValue($startDate);
            }
            if ($fieldModel->isEditable() || $specialField) {
                $recordModel->set($fieldName, $fieldModel->getDBInsertValue($fieldValue));
            }
        }

        $recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_EDIT);

        $picklistDependencyDatasource = Vtiger_DependencyPicklist::getPicklistDependencyDatasource($moduleName);

        $currency = ServiceContracts_Record_Model::getcurrencytype($record);//获取当前人民币的字段类型

        //wangbin 2015年11月5日 15:15:18 添加合同分成信息；
        $this->db = PearDatabase::getInstance();
        //$accessibleUsers = get_username_array($where);  //人员信息
        $getcompanysql ="SELECT owncompany FROM `vtiger_owncompany`";
        $company = $this->db->pquery($getcompanysql,array());
        $owncompany = array();
        $sums=$this->db->num_rows($company);
        if($sums>0){
            while($row = $this->db->fetchByAssoc($company)){
                //var_dump();
                $owncompany[$row['owncompany']] = $row['owncompany'];
            }
        }

        //if($record){$contracts_divide = ServiceContracts_Record_Model::servicecontracts_divide($record);}
        //$viewer->assign('CONTRACTS_DIVIDE',$contracts_divide); //合同分成表
        //$viewer->assign('OWNCOMPANY',$owncompany);//所有公司
        //$viewer->assign('ACCESSIBLE_USERS',$accessibleUsers);//人员
        $viewer->assign('PICKIST_DEPENDENCY_DATASOURCE', Zend_Json::encode($picklistDependencyDatasource));
        $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
        $viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());//UI字段生成位置
        $viewer->assign('CURRENCY', $currency);
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('CURRENTDATE', date('Y-n-j'));
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $isRelationOperation = $request->get('relationOperation');
        //财务修改合同
        $viewer->assign("FINANCIAL_MODIFICATION",$recordModel->personalAuthority('SupplierContracts','Received'));


        $viewer->assign('MULTIPLE', $moduleModel->exportGrouprt($moduleName,'PayApply'));
        $viewer->assign('PAYAPPLYIDS', $recordModel->get('payapplyids'));

        //$productcategory=ServiceContracts_Record_Model::productcategory($record);//产品的分类
        //$partproductid = ServiceContracts_Record_Model::getContractType($record);//获取当前的合同类型对应部分产品
        //$viewer->assign('RECORD_PARTPRODUCTID', $partproductid);
        //$allproductid = ServiceContracts_Record_Model::getProductsId($record);//获取当前的合同类型对应的所有产品
        //$viewer->assign('RECORD_ALLPRODUCTID', $allproductid);
        //$viewer->assign('RECORD_ALLEPRODUCTID', ServiceContracts_Record_Model::getextraproduct($record));//额外产品
        //$viewer->assign('RECORD_PRODUCTSCATEGORY',$productcategory);

//        print_r($productname);die;
        //if it is relation edit
        //查看当前合同是否可以更改采购合同供应商
        $vendorid='';
        if($record){
            $vendorid = $recordModel->getVendorid();
        }

        $viewer->assign('VENDORID',$vendorid);
        $viewer->assign('IS_RELATION_OPERATION', $isRelationOperation);
        if ($isRelationOperation) {
            $viewer->assign('SOURCE_MODULE', $request->get('sourceModule'));
            $viewer->assign('SOURCE_RECORD', $request->get('sourceRecord'));
        }

        // 获取充值平台数据
        $viewer->assign('RECHARGEPLATFORM_DATA', Vendors_Record_Model::getRechargeplatform());

        // 获取产品数据
        $viewer->assign('PRODUCT_DATA', Vendors_Record_Model::getProducts());

        // 获取产品返点数据
        $vendorsrebateData = $recordModel->getVendorsrebate($record);
        $viewer->assign('VENDORSREBATEDATA', $vendorsrebateData);
        //使用上传控件
        $viewer->assign('MAX_UPLOAD_LIMIT_MB', Vtiger_Util_Helper::getMaxUploadSize());
        $viewer->assign('MAX_UPLOAD_LIMIT', vglobal('upload_maxsize'));
        //合同状态
        $viewer->assign('MODULESTATUS', $recordModel->get('modulestatus'));
        if(!$record || $recordModel->get("sealseq")){
            $viewer->assign('SHOWSEALSEQ', 1);
        }
        $viewer->view('EditView.tpl', $moduleName);
    }
}