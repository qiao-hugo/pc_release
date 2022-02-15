<?php
/*+******************
 *编辑页面的权限控制
 * 某些模块关联生成数据
 * 只能编辑不可新增
 **********************/

Class UserManger_Edit_View extends Vtiger_Edit_View {
	public function checkPermission(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$record = $request->get('record');
		$recordPermission = Users_Privileges_Model::isPermitted($moduleName, 'EditView', $record);
		if(!$recordPermission) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}
		global $isallow;

		if(in_array($moduleName, $isallow)&&$record&&!$request->isAjax()){   //部位ajax请求
			$recordModel=Vtiger_Record_Model::getInstanceById($record,$moduleName);
			if(!empty($recordModel)&&$recordModel){
				$module=$recordModel->getData();
				$moduleStatus=$module['modulestatus'];
				if($moduleStatus=='c_complete'){
				    return ;
                }
                if(!getIsEditOrDel('edit',$moduleStatus)){
                    throw new AppException('状态 '.vtranslate($moduleStatus,$moduleName).' 不允许当前的操作');
                }
			}
		}
		//end
		
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