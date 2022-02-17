<?php
/*+******************
 *编辑页面的权限控制
 * 某些模块关联生成数据
 * 只能编辑不可新增
 **********************/

Class SalesDaily_Edit_View extends Vtiger_Edit_View {
    public function checkPermission(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $record = $request->get('record');
        $recordPermission = Users_Privileges_Model::isPermitted($moduleName, 'EditView', $record);
        if(!$recordPermission) {
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
            exit;
        }


        if($record>0){
            global $adb;
            $query='SELECT 1 FROM `vtiger_salesdailyfournotv` WHERE mangerid>0 AND salesdailybasicid=?';
            $result=$adb->pquery($query,array($record));
            if($adb->num_rows($result)){
                throw new AppException('上级已审查不允许修改');
                exit;
            }
        }

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

				if($fieldModel->isEditable()) {
					$recordModel->set($fieldName, $fieldModel->getDBInsertValue($fieldValue));
				}
			}
		}
		$recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel,'Edit');
        if(empty($record)){
            $this->record->setData($this->record->column_fields)->set('createdtime',date('Y-m-d H:i'))->set('dailydatetime',date('Y-m-d'));
            $viewer->assign('EDITLIST',  $this->record->getPrevCanDeal());
        }else{
            $viewer->assign('EDITLIST',  $this->record->getEditlList($record));
        }
		$viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
		$viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());//UI字段生成位置
		$viewer->assign('MODULE', $moduleName);

		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());//执行好多次，真是
		$viewer->assign('RECORD',$recordModel);//编辑页面显示不可编辑字段内容
		//$picklistDependencyDatasource = Vtiger_DependencyPicklist::getPicklistDependencyDatasource($moduleName);
		//$viewer->assign('PICKIST_DEPENDENCY_DATASOURCE','');然而有毛用
		//$viewer->assign('CURRENTDATE', date('Y-n-j'));还需要吗？
		//关联修改？
		$isRelationOperation = $request->get('relationOperation');
		$viewer->assign('IS_RELATION_OPERATION', $isRelationOperation);
		if($isRelationOperation) {
			$viewer->assign('SOURCE_MODULE', $request->get('sourceModule'));
			$viewer->assign('SOURCE_RECORD', $request->get('sourceRecord'));
		}
		//使用上传控件
		//$viewer->assign('MAX_UPLOAD_LIMIT_MB', Vtiger_Util_Helper::getMaxUploadSize());
		//$viewer->assign('MAX_UPLOAD_LIMIT', vglobal('upload_maxsize'));
		$viewer->view('EditView.tpl', $moduleName);
	}
}