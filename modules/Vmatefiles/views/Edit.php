<?php
Class Vmatefiles_Edit_View extends Vtiger_Edit_View {

	public function process(Vtiger_Request $request) {
	    global $current_user;
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
			//5.6.1加入服务合同合同v只有有权限的人可以看到
			if($request->get('sourceModule')=='ServiceContracts'&&$moduleModel->exportGrouprt('ServiceContracts','Vview')){
                $viewer->assign('Vview_authority', 1);
            }
		}

		// 获取 销售目标的周信息
		if (!empty($record)) {
			
		}
        $ht_admin=$this->isHtAdmin($current_user->id);
        $viewer->assign('IS_HT_ADMIN',$ht_admin);//人员
		$viewer->assign('DATE_ARR', $dateArr);
		$viewer->view('EditView.tpl', $moduleName);
	}

    function isHtAdmin($userId){
        $sql="select 1 from vtiger_invoicecompanyuser where modulename='ht' and userid=?";
        $db=PearDatabase::getInstance();
        $result = $db->pquery($sql,array($userId));
        return $db->num_rows($result);
    }

	function getDateArray($oneDay) {
		$endDate = date("Y-m-d", strtotime("+1 month", strtotime($oneDay)));  
		$endDate = date("Y-m-d", strtotime("-1 day", strtotime($endDate)));    //最后一天

		$dateArray = array();
		for($i = 1; ; $i++) {
			$lastday = date("Y-m-d", strtotime("$oneDay Sunday"));  
			if (strtotime($endDate) <= strtotime($lastday)) {  //如果 大于 最后一天
				$lastday = $endDate;
				$k = 'week' . $i;
				$dateArray[$k] = array('startdate'=>$oneDay,  'enddate'=>$lastday, 'weekNum'=>$i, 'salestargetdetailid'=>'');
				break;
			}

			$k = 'week' . $i;
			$dateArray[$k] = array('startdate'=>$oneDay,  'enddate'=>$lastday, 'weekNum'=>$i, 'salestargetdetailid'=>'');
			$oneDay = date("Y-m-d", strtotime("+1 day", strtotime($lastday)));  
		}
		return $dateArray;
	}
	
	
}