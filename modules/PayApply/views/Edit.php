<?php
Class PayApply_Edit_View extends Vtiger_Edit_View {

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
                if(!getIsEditOrDel('edit',$moduleStatus)){
                    throw new AppException('状态 '.vtranslate($moduleStatus,$moduleName).' 不允许当前的操作');
                }

            }
        }
    }
    public function process(Vtiger_Request $request) {
		$viewer = $this->getViewer ($request);
		$moduleName = $request->getModule();
		$record = $request->get('record');
		//获取开始结束申请时间
        $adb =PearDatabase::getInstance();
        $sql = "select startdate,enddate from vtiger_payapply where payapplyid=?";
        $sel_result = $adb->pquery($sql, array($record));
        $row = $adb->query_result_rowdata($sel_result, 0);
//        dd($row);
        $viewer->assign('startdate', $row['startdate']);
        $viewer->assign('enddate', $row['enddate']);
        $viewer->assign('applydate', $row['startdate'].'~'.$row['enddate']);
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
        //项目分类
        $productcategory=PayApply_Module_Model::productcategory($record);
//dd($productcategory);
        $viewer->assign('RECORD_PRODUCTSCATEGORY',$productcategory);

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
		
		global $current_user;
		$viewer->assign('LAST_NAME', $current_user->last_name);
        $viewer->assign('Invoicecompany', $current_user->invoicecompany);

		$viewer->assign('USERID', $current_user->id);
//		$viewer->assign('DATE_ARR', $dateArr);
		$viewer->view('EditView.tpl', $moduleName);
	}


	
	
	
}