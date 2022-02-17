<?php
Class ActivationCodeDetail_Edit_View extends Vtiger_Edit_View {
	public function process(Vtiger_Request $request) {
		$viewer = $this->getViewer ($request);
		$moduleName = $request->getModule();
		$record = $request->get('record');

		 if(!empty($record)) {
			$recordModel = $this->record?$this->record:Vtiger_Record_Model::getInstanceById($record, $moduleName);
            if($recordModel->get('staypaymenttype')=='自动'){
                throw new AppException("自动类型不能编辑");
            }
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
		$moduleModel = $recordModel->getModule();
		$fieldList = $moduleModel->getFields();
		$requestFieldList = array_intersect_key($request->getAll(), $fieldList);
		if(!empty($requestFieldList)){
			foreach($requestFieldList as $fieldName=>$fieldValue){
				$fieldModel = $fieldList[$fieldName];
				$specialField = false;
				// We collate date and time part together in the EditView UI handling
				// so a bit of special treatment is required if we come from QuickCreate
				if($fieldModel->isEditable() || $specialField) {
					$recordModel->set($fieldName, $fieldModel->getDBInsertValue($fieldValue));
				}
			}
		}
		$recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_EDIT);
		$viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
		$viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());//UI字段生成位置
		$viewer->assign('MODULE', $moduleName);
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
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