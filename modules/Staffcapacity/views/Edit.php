<?php
Class Staffcapacity_Edit_View extends Vtiger_Edit_View {

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
		
		// 默认年度，月份
		$viewer->assign('NOW_DATE', date('Y-m-d'));
		

		


		$viewer->assign('DATE_ARR', $dateArr);
		$viewer->view('EditView.tpl', $moduleName);
	}


	function getDateArray($oneDay) {
		
		$t_m = date('w', strtotime($oneDay) );
		$oneDay2 = date('Y-m-d', strtotime('+'.(7 - $t_m).' days', strtotime($oneDay)));

		$oneDay3 = date('Y-m-d', strtotime('+1 days', strtotime($oneDay2)));
		$oneDay4 = date('Y-m-d', strtotime('+6 days', strtotime($oneDay3)));

		$oneDay5 = date('Y-m-d', strtotime('+1 days', strtotime($oneDay4)));
		$oneDay6 = date('Y-m-d', strtotime('+6 days', strtotime($oneDay5)));

		$oneDay7 = date('Y-m-d', strtotime('+1 days', strtotime($oneDay6)));
		// 上一个月的第一天
		$ttt = date('Y-m-01', strtotime('+1 month', strtotime($oneDay)));
		$oneDay8 = date('Y-m-d', strtotime('-1 days', strtotime($ttt)));

		$dateArray = array(
			'week1'=>array('startdate'=>$oneDay,  'enddate'=>$oneDay2, 'weekNum'=>1, 'salestargetdetailid'=>''),
			'week2'=>array('startdate'=>$oneDay3, 'enddate'=>$oneDay4, 'weekNum'=>2, 'salestargetdetailid'=>''),
			'week3'=>array('startdate'=>$oneDay5, 'enddate'=>$oneDay6, 'weekNum'=>3, 'salestargetdetailid'=>''),
			'week4'=>array('startdate'=>$oneDay7, 'enddate'=>$oneDay8, 'weekNum'=>4, 'salestargetdetailid'=>''),
		);

		return $dateArray;
	}
	
	
}