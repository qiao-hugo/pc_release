<?php
Class Receipt_Edit_View extends Vtiger_Edit_View {

    public function checkPermission(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $record = $request->get('record');
        $recordPermission = Users_Privileges_Model::isPermitted($moduleName, 'EditView', $record);
        if (!$recordPermission) {
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
        }
        if($record) {
            $adb =PearDatabase::getInstance();
            $sql = "select * FROM vtiger_receipt where receiptid=?";
            $sel_result = $adb->pquery($sql, array($record));
            $rawData = $adb->query_result_rowdata($sel_result, 0);
            $modulestatus = $rawData['modulestatus'];
            if (in_array($modulestatus, ['b_check','c_complete'])) {
                throw new AppException(vtranslate('审核中、已完成的收据禁止编辑'));
            }
        }
    }

    public function process(Vtiger_Request $request) {
		$viewer = $this->getViewer ($request);
		$moduleName = $request->getModule();
		$record = $request->get('record');

        $adb =PearDatabase::getInstance();
        $sql = "select * FROM vtiger_receipt where receiptid=?";
        $sel_result = $adb->pquery($sql, array($record));
        $rawData = $adb->query_result_rowdata($sel_result, 0);
        $applyuserid = $rawData['applyuserid'];

        global $current_user;
        if($applyuserid == $current_user->id){
            $viewer->assign('SELF_ADD', 'yes');
        } else {
            $viewer->assign('SELF_ADD', 'no');
        }
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
        //获取回款关联
        if (!empty($record)) {
            $newinvoiceData = Receipt_Record_Model::getNewinvoicerayment($record);
            $viewer->assign('NEWINVOICEDATA', $newinvoiceData);
            $viewer->assign('IS_UPDATE_NEWINVOICERAYMENT', 1);
        }

		$moduleModel = $recordModel->getModule();
		//读取模块的字段
		$fieldList = $moduleModel->getFields();

		//取交集?还不知道有什么用
		$requestFieldList = array_intersect_key($request->getAll(), $fieldList);

		if(!empty($requestFieldList)){
			foreach($requestFieldList as $fieldName=>$fieldValue){
				$fieldModel = $fieldList[$fieldName];
				$specialField = false;
				if($fieldModel->isEditable()) {
					$recordModel->set($fieldName, $fieldModel->getDBInsertValue($fieldValue));
				}
			}
		}
		$recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel,'Edit');

        $adb =PearDatabase::getInstance();
        $sql = "select * FROM vtiger_receipt where receiptid=?";
        $sel_result = $adb->pquery($sql, array($record));
        $rawData = $adb->query_result_rowdata($sel_result, 0);
        $receivedpaymentsid = $rawData['receivedpaymentsid'];

        $sql = "select * FROM vtiger_receivedpayments where receivedpaymentsid=?";
        $sel_result = $adb->pquery($sql, array($receivedpaymentsid));
        $rawData = $adb->query_result_rowdata($sel_result, 0);
        $paytitle = $rawData['paytitle'];
        $owncompany = $rawData['owncompany'];
        $unit_price = $rawData['unit_price'];
        $reality_date = $rawData['reality_date'];
        $viewer->assign('paytitle', $paytitle);
        $viewer->assign('owncompany', $owncompany);
        $viewer->assign('unit_price', $unit_price);
        $viewer->assign('reality_date', $reality_date);

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
		
		global $current_user;
		$viewer->assign('LAST_NAME', $current_user->last_name);
        $viewer->assign('Invoicecompany', $current_user->invoicecompany);
		$viewer->assign('USERID', $current_user->id);
		$viewer->view('EditView.tpl', $moduleName);
	}
	
}