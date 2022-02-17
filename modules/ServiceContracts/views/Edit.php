<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Class ServiceContracts_Edit_View extends Vtiger_Edit_View
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
        $collate = $request->get('collate');
        //判断是否为核对编辑
        if ($collate) {
            $recordModel = Vtiger_Record_Model::getInstanceById($record, $moduleName);
            $moduleData = $recordModel->getData();
            if($moduleData['modulestatus'] == 'c_complete') {
                if ($moduleData['last_collate_status'] == 'fit') {
                    throw new AppException('合同已签收且核对符合,不允许编辑!');
                    exit;
                } elseif (empty($moduleData['last_collate_status']) && $moduleData['first_collate_status'] == 'fit') {
                    throw new AppException('合同已签收且核对符合,不允许编辑!');
                    exit;
                }
            }
            $viewer = $this->getViewer($request);
            $viewer->assign('is_collate', $collate);
        } else {
            //wangbin 2015年6月29日 星期一 合同被提单后不能编辑;
            $db = PearDatabase::getInstance();
            if (!empty($record)) {
//            $selectsql = "SELECT 1 FROM vtiger_achievementallot_statistic WHERE  servicecontractid=? LIMIT 1 ";
//            $issalesorder = $db->pquery($selectsql,array($record));
//            if($db->num_rows($issalesorder)){
//                throw new AppException("已经匹配过回款的合同，不能编辑");
//            }
                $selectsql = "SELECT 1 FROM vtiger_crmentity WHERE crmid = ( SELECT salesorderid FROM `vtiger_salesorder` WHERE servicecontractsid=? AND iscancel=0 LIMIT 1) AND deleted = 0";

                $issalesorder = $db->pquery($selectsql, array($record));
                if ($db->num_rows($issalesorder)) {
                    throw new AppException("该合同已提工单，不能编辑");
                }
                $selectsql = "SELECT
                            1
                        FROM
                            vtiger_contracts_execution
                        LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_contracts_execution.contractexecutionid
                        LEFT JOIN vtiger_contracts_execution_detail ON vtiger_contracts_execution.contractexecutionid = vtiger_contracts_execution_detail.contractexecutionid
                        WHERE
                            vtiger_crmentity.deleted = 0
                        AND vtiger_contracts_execution_detail.executestatus='c_executed' AND vtiger_contracts_execution.contractid=? and vtiger_contracts_execution_detail.iscancel=0";
                $issalesorder = $db->pquery($selectsql, array($record));
                if ($db->num_rows($issalesorder)) {
                    throw new AppException("该合同应收阶段已执行，不能编辑");
                }
            }

            //end
            $recordPermission = Users_Privileges_Model::isPermitted($moduleName, 'EditView', $record);
            if (!$recordPermission) {
                throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
            }


            //young.yang 2015-1-3 增加编辑页面，对于流程状态的控制，某些状态不允许编辑
            global $isallow, $current_user, $configcontracttypeName, $configcontracttypeNameTYUN;
            if (in_array($moduleName, $isallow) && $record && !$request->isAjax()) {
                $recordModel = Vtiger_Record_Model::getInstanceById($record, $moduleName);
                if (!empty($recordModel) && $recordModel) {
                    $module = $recordModel->getData();
                    $moduleStatus = $module['modulestatus'];
                    if ($recordModel->get('signaturetype') == 'eleccontract' && $recordModel->get('contract_type') == $configcontracttypeName) {
                        throw new AppException('T云电子合同,不允许编辑!');
                        exit;
                    }
                    if ($moduleStatus == 'c_complete' && $recordModel->hasOrder($record)) {
                        throw new AppException('合同签收并且已下单成功,不允许编辑!');
                        exit;
                    }
                    if ($moduleStatus == 'c_complete' && $module['last_collate_status'] == 'fit') {
                        throw new AppException('合同已签收且核对符合,不允许编辑!');
                        exit;
                    } elseif ($moduleStatus == 'c_complete' && empty($module['last_collate_status']) && $module['first_collate_status'] == 'fit') {
                        throw new AppException('合同已签收且核对符合,不允许编辑!');
                        exit;
                    }
		    
                    if ($moduleStatus == 'b_check') {
                        throw new AppException('审核中,不允许编辑!');
                        exit;
                    }
                    if (!empty($moduleStatus) && !getIsEditOrDel('edit', $moduleStatus)) {
                        $arrStatus = array('c_recovered', '已发放', 'c_complete');
                        if (in_array($moduleStatus, $arrStatus)) {    //在合同收回后财务人员可编加
                            //$userid=getDepartmentUser('H25');
                            //财务审核节点编辑
                            $moduleModel = $recordModel->getModule();
                            if ($moduleModel->exportGrouprt('ServiceContracts', 'Received')) {
                                $companycode = $module['companycode'];
                                if (!empty($companycode)) {
                                    $query = 'SELECT 1 FROM vtiger_invoicecompanyuser WHERE invoicecompany=? AND userid=?';
                                    $result = $db->pquery($query, array($companycode, $current_user->id));
                                    if ($db->num_rows($result) > 0) {
                                        return;
                                    } else {
                                        throw new AppException('合同主体:' . $module['invoicecompany'] . '的合同,不允许编辑!');
                                    }
                                }
                                return;
                            }
                        }
			            global $current_user;
                        if($current_user->id!=$recordModel->get("assigned_user_id") ||
                            ($current_user->id==$recordModel->get("assigned_user_id")&&(in_array($moduleStatus,array('c_complete','c_recovered'))) || $recordModel->hasOrder($record))
                        ){
                            throw new AppException('状态 ' . vtranslate($moduleStatus, $moduleName) . ' 不允许当前的操作');
                        }
                    }
                    if ($module['sideagreement'] == 1) {
                        throw new AppException('补充协议不允许编辑!');
                    }
                }
            }
            //end
        }
    }

    public function process(Vtiger_Request $request){

        global $configcontracttypeNameTYUN,$current_user;
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
        if($fieldList['Receivedate']){
            $fieldList['Receivedate']->fieldInfo['endDate']=date('Y-m-d');
        }
        if($fieldList['signdate']){
            $fieldList['signdate']->fieldInfo['endDate']=date('Y-m-d');
        }
        if($fieldList['Returndate']){
            $fieldList['Returndate']->fieldInfo['endDate']=date('Y-m-d');
        }
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
        $accessibleUsers = get_username_array('1=1');  //人员信息
        $accessibleUsersDivide=get_username_array_divide('1=1');
        $getcompanysql ="SELECT owncompany FROM `vtiger_owncompany`";
        $company = $this->db->pquery($getcompanysql,array());
        $owncompany = array();
        $sums=$this->db->num_rows($company);
        if($sums>0){
            while($row = $this->db->fetchByAssoc($company)){
                $owncompany[$row['owncompany']] = $row['owncompany'];
            }
        }
        if($record){$contracts_divide = ServiceContracts_Record_Model::servicecontracts_divide($record);}
        if(isset($_REQUEST['record'])){
//        if(in_array($current_user->roleid,array('H104','H90')) && isset($_REQUEST['record'])){
            $viewer->assign('IS_EDIT',1);// 是编辑
        }else{
            $viewer->assign('IS_EDIT',0);//不是
        }
        $viewer->assign('ISRECEIVED',$moduleModel->exportGrouprt('ServiceContracts','Received'));
        $viewer->assign('CONTRACTS_DIVIDE',$contracts_divide); //合同分成表
        $viewer->assign('OWNCOMPANY',$owncompany);//所有公司
        //$viewer->assign('ACCESSIBLE_USERS',$accessibleUsers);//人员
        $viewer->assign('ACCESSIBLE_USERS_DIVIDE',$accessibleUsersDivide);//员工列表
        $signaturetypehref=$request->get('signaturetypehref');
        $signaturetypehref=$signaturetypehref=='eleccontract'?'eleccontract':($recordModel->get('signaturetype')=='eleccontract'?'eleccontract':'papercontract');
        $recordModel->set('signaturetype',$signaturetypehref);
        $viewer->assign('SIGNATURETYPEHREF',$signaturetypehref);
        $viewer->assign('PICKIST_DEPENDENCY_DATASOURCE', Zend_Json::encode($picklistDependencyDatasource));
        $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
        $viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());//UI字段生成位置
        $viewer->assign('CURRENCY', $currency);
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('CURRENTDATE', date('Y-n-j'));
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign("SIDEAGREEMENT",$recordModel->get('sideagreement'));
        $isRelationOperation = $request->get('relationOperation');

        $productcategory=ServiceContracts_Record_Model::productcategory($record);//产品的分类
        $partproductid = ServiceContracts_Record_Model::getContractType($record);//获取当前的合同类型对应部分产品
        $viewer->assign('RECORD_PARTPRODUCTID', $partproductid);
        if(in_array($recordModel->get('contract_type'),$configcontracttypeNameTYUN)){
            $TyunProductsOnlineData=$recordModel->getTyunProductsOnline($record,$recordModel->get('contract_type'),$recordModel->get('servicecontractstype'),
                $recordModel->get('contract_classification'),$recordModel->get('agentid'),($recordModel->get('categoryid')?$recordModel->get('categoryid'):0));
            $allproductid=$TyunProductsOnlineData['product_list'];
            $arr_extraproduct1 = $TyunProductsOnlineData['otherproduct_list'];
            $arr_extraproduct2 = array();
            $arr_extraproduct3 = array();
        }else{
            $allproductid = ServiceContracts_Record_Model::getProductsId($record);//获取当前的合同类型对应的所有产品
            $arr_extraproduct = ServiceContracts_Record_Model::getextraproduct($record);
            $arr_extraproduct1 = array();
            $arr_extraproduct2 = array();
            $arr_extraproduct3 = array();
            for ($i=0; $i<count($arr_extraproduct); $i++) {
                if($arr_extraproduct[$i]["groupflag"] == '1'){
                    $arr_extraproduct1[] = $arr_extraproduct[$i];
                }
                if($arr_extraproduct[$i]["groupflag"] == '2'){
                    $arr_extraproduct2[] = $arr_extraproduct[$i];
                }
                if($arr_extraproduct[$i]["groupflag"] == '3'){
                    $arr_extraproduct3[] = $arr_extraproduct[$i];
                }
            }
        }
        $sc_related_to = '';
        $invoicecompany = '';
        if($record){
            $sc_related_to = $recordModel->getAccount();
            $invoicecompany = $recordModel->getInvoicecompany();
        }

        $viewer->assign('SCRELATEDTO',$sc_related_to);
        $viewer->assign('INVOICECOMPANY',$invoicecompany);
        $viewer->assign('RECORD_ALLPRODUCTID', $allproductid);
        $viewer->assign('RECORD_ALLEPRODUCTID1', $arr_extraproduct1);//额外产品
        $viewer->assign('RECORD_ALLEPRODUCTID2', $arr_extraproduct2);//额外产品
        $viewer->assign('RECORD_ALLEPRODUCTID3', $arr_extraproduct3);//额外产品
        $viewer->assign('RECORD_PRODUCTSCATEGORY',$productcategory);

//        print_r($productname);die;
        //if it is relation edit
        $viewer->assign('IS_RELATION_OPERATION', $isRelationOperation);
        if ($isRelationOperation) {
            $viewer->assign('SOURCE_MODULE', $request->get('sourceModule'));
            $viewer->assign('SOURCE_RECORD', $request->get('sourceRecord'));
        }

        //  取出产品明细里面的供应商
        $productVendor = ServiceContracts_Record_Model::getProductVendor();
        $viewer->assign('PRODUCTVENDOR', json_encode($productVendor));

//        var_dump($recordModel->getContractBuyType($record));

        //获取购买类型
        $viewer->assign("CONTRACT_CLASS_TYPE",$recordModel->getContractBuyType($record));
        //财务修改合同
        $viewer->assign("FINANCIAL_MODIFICATION",$recordModel->personalAuthority('ServiceContracts','Received'));

        $viewer->assign('MAX_UPLOAD_LIMIT_MB', Vtiger_Util_Helper::getMaxUploadSize());
        $viewer->assign('MAX_UPLOAD_LIMIT', vglobal('upload_maxsize'));
        $viewer->assign('AGENTID',$recordModel->get('agentid'));
        $viewer->assign('CHECK_COUPON',$recordModel->getCheckCoupon($record));
        $viewer->assign('CHECK_ACCOUNT_AND_TOTAL',$recordModel->getCheckAccountAndTotal($record));
        $viewer->assign('CONTRACTTYPE',$recordModel->get("contract_type"));
        $viewer->assign('HASORDER',$recordModel->hasOrder($record));
        $viewer->assign('CATEGORY',$recordModel->getTyunWebCategory()['data']);
        $viewer->assign('CATEGORYID',$recordModel->get('categoryid'));
        $viewer->assign('MODULESTATUS', $recordModel->get('modulestatus'));
        if($record){
            $wkExtendInfo=$recordModel->getWkExtendInfo($record);
            $viewer->assign('WKCODE', $wkExtendInfo['wkcode']);
            $viewer->assign('WKCONTACTNAME',$wkExtendInfo['wkcontactname']);
            $viewer->assign('WKCONTACTPHONE',$wkExtendInfo['wkcontactphone']);
        }else{
            $viewer->assign('WKCODE', $request->get('wkcode'));
            $viewer->assign('WKCONTACTNAME', $request->get('wkcontactname'));
            $viewer->assign('WKCONTACTPHONE', $request->get('wkcontactphone'));
        }

        $viewer->view('EditView.tpl', $moduleName);
    }
}