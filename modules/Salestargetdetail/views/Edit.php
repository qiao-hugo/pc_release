<?php
Class Salestargetdetail_Edit_View extends Vtiger_Edit_View {

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
		$viewer->assign('DEFA_YEAR', date('Y'));
		$viewer->assign('DEFA_MONTH', date('m'));

		global $current_user;
		$viewer->assign('LAST_NAME', $current_user->last_name);

		if (empty($record)) {
			$viewer->assign('USERID', $current_user->id);
		}
		


		// 查看 销售目标是否可以修改
		if (!empty($record)) {
			$sql = "select * from vtiger_salestarget where salestargetid=? LIMIT 1";
			$db=PearDatabase::getInstance();
			$sel_result = $db->pquery($sql, array($record));
			$res_cnt = $db->num_rows($sel_result);
			if ($res_cnt > 0) {
				$row = $db->query_result_rowdata($sel_result, 0);
				$viewer->assign('MAIN_ISMODIFY', $row['ismodify']);


				// 当前用户是否是 该商务人员的直接上级
				$sql = "select reports_to_id from vtiger_users where id=?";
				$sel_result = $db->pquery($sql, array($row['businessid']));
				$res_cnt = $db->num_rows($sel_result);
				if ($res_cnt > 0) {
					$row = $db->query_result_rowdata($sel_result, 0);
					if ($current_user->id == $row['reports_to_id']) {
						$viewer->assign('IS_SHOW_RETURN_BUTTON', '1');
					}
				}
			}
		}
		

		// 获取 销售目标的周信息
		if (!empty($record)) {
			$sql = "select * from vtiger_salestargetdetail where salestargetid=? order by weeknum";
			$db=PearDatabase::getInstance();
			$sel_result = $db->pquery($sql, array($record));
			$res_cnt = $db->num_rows($sel_result);

			if ($res_cnt > 0) {
				$allData = array();

				$i = 1;
				while($rawData=$db->fetch_array($sel_result)) {
					if ($rawData['weekinvitationtarget'] > 0 || $rawData['weekvisittarget'] > 0 
						|| $rawData['weekachievementtargt'] > 0) {
						$rawData['show'] = '1';
					}
					$rawData['weekNum'] = $i;
		            $allData['week'. $i] = $rawData;

		            $i ++;
		        }
		        $dateArr = $allData;
			} else {
				// 当前月的第一天
				$oneDay = date('Y-m-01');
				$dateArr = $this->getDateArray($oneDay);
			}
		} else {
			// 当前月的第一天
			$oneDay = date('Y-m-01');
			$dateArr = $this->getDateArray($oneDay);
		}


		$viewer->assign('DATE_ARR', $dateArr);
		$viewer->view('EditView.tpl', $moduleName);
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