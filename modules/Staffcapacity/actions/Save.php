<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Staffcapacity_Save_Action extends Vtiger_Action_Controller {

	public function checkPermission(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$record = $request->get('record');

		if(!Users_Privileges_Model::isPermitted($moduleName, 'Save', $record)) {
			throw new AppException('LBL_PERMISSION_DENIED');
		}
	}


	// 判断商务人员冲突
	public function checkTwo(Vtiger_Request $request) {
		$businessid = $request->get('businessid');

		$sql = "select * from vtiger_staffcapacity where businessid=? ";

		$db=PearDatabase::getInstance();
		$sel_result = $db->pquery($sql, array($businessid));
		$res_cnt = $db->num_rows($sel_result);

		if($res_cnt > 0) {
			return true;
		} 

		return false;
	}


	// 更新字段中的时间检测
	public function checkFieldUpateTime(Vtiger_Request $request, $rowData) {
		$record = $request->get('record');

		$fieldArr = array(
		'receiveinformation','businessinquiry','querylegalperson','openingwhite','voicegood', 'productintroduction', 'invitation',
		'webmastertools','networkstatus','customeranalysis');

		$tt = array();
		if (empty($record )) {  //添加
			foreach($fieldArr as $v) {
				$flag = $request->get($v);
				if ($flag == 'on') {
					$tt[$v.'date'] = date('Y-m-d');
				}
			}
		} else {  //更新
			$requestData = array();
			foreach($fieldArr as $v) {
				$requestData[$v] = $request->get($v);
			}

			// $rowData 未保存的数据
			foreach($requestData as $k=>$v) {
				if (empty($rowData[$k]) &&  $v == 'on') {
					$tt[$k.'date'] = date('Y-m-d');
				}
			}
		}

		return $tt;
	}

	public function process(Vtiger_Request $request) {
		//$request->set('entertime', '2016-11-11');
		$record = $request->get('record');
		$rowData = array();
		if (empty($record)) {
			$flag = $this->checkTwo($request);
			if ($flag) {
				header("Location: index.php");
				exit;
			}
		} else {
			$sql = "select * from vtiger_staffcapacity where staffcapacityid=? LIMIT 1";
			$db=PearDatabase::getInstance();
			$sel_result = $db->pquery($sql, array($record));
			$res_cnt = $db->num_rows($sel_result);
			if ($res_cnt > 0) {
				$rowData = $db->query_result_rowdata($sel_result, 0);
			}
		}

		$recordModel = $this->saveRecord($request);

		$setFieldArr = $this->checkFieldUpateTime($request, $rowData);
		if (count($setFieldArr) > 0) {  //更新的时候
			if (empty($record)) {
				$record = $recordModel->getId();
			}
			$sql_arr = array();
			foreach ($setFieldArr as $key=>$value) {
				$sql_arr[] = " {$key}='{$value}' ";
			}
			$sql = "update vtiger_staffcapacity set ". implode(',', $sql_arr) . " where staffcapacityid=?";
			$db=PearDatabase::getInstance();
			$sel_result = $db->pquery($sql, array($record));
		}

		if($request->get('relationOperation')) {
					
			$loadUrl = $this->getParentRelationsListViewUrl($request);
		} else if ($request->get('returnToList')) {
			$loadUrl = $recordModel->getModule()->getListViewUrl();
		} else {
			$loadUrl = $recordModel->getDetailViewUrl();
		}
		if(empty($loadUrl)){
			if($request->getHistoryUrl()){
				$loadUrl=$request->getHistoryUrl();
			}else{
				$loadUrl="index.php";
			}
		}
        if($request->isAjax()){

        }else{
            header("Location: $loadUrl");
        }
	}

	/**
	 * Function to save record
	 * @param <Vtiger_Request> $request - values of the record
	 * @return <RecordModel> - record Model of saved record
	 */
	public function saveRecord($request) {
		$recordModel = $this->getRecordModelFromRequest($request);
		$recordModel->save();
		
		if($request->get('relationOperation')) {
			$parentModuleName = $request->get('sourceModule');
			$parentModuleModel = Vtiger_Module_Model::getInstance($parentModuleName);
			$parentRecordId = $request->get('sourceRecord');
			$relatedModule = $recordModel->getModule();
			$relatedRecordId = $recordModel->getId();

			$relationModel = Vtiger_Relation_Model::getInstance($parentModuleModel, $relatedModule);
			$relationModel->addRelation($parentRecordId, $relatedRecordId);
		}
		return $recordModel;
	}

	/**
	 * Function to get the record model based on the request parameters
	 * @param Vtiger_Request $request
	 * @return Vtiger_Record_Model or Module specific Record Model instance
	 */
	protected function getRecordModelFromRequest(Vtiger_Request $request) {
		
		
		$moduleName = $request->getModule();
		$recordId = $request->get('record');
		
		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
		
		if(!empty($recordId)) {
			$recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
			$modelData = $recordModel->getData();
			$recordModel->set('modcommentsid', $recordId);

			$recordModel->set('mode', 'edit');
		} else {
			$recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
			$modelData = $recordModel->getData();
			$recordModel->set('mode', '');
		}
		
		
		$fieldModelList = $moduleModel->getFields();
	
		foreach ($fieldModelList as $fieldName => $fieldModel) {
			$fieldValue = $request->get($fieldName, null);
			$fieldDataType = $fieldModel->getFieldDataType();
			if($fieldDataType == 'time'){
				$fieldValue = Vtiger_Time_UIType::getTimeValueWithSeconds($fieldValue);
			}
			if($fieldValue !== null) {
				if(!is_array($fieldValue)) {
					$fieldValue = trim($fieldValue);
				}
				$recordModel->set($fieldName, $fieldValue);
				
			}
			
		}
		
		
		return $recordModel;
	}
	
	//gaocl 2015-01-05 add start
	/**
	 * 关联模块编辑提交后返回一览页面URL取得
	 * @param Vtiger_Request $request
	 * @return 返回一览页面URL
	 */
	public function getParentRelationsListViewUrl(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$parentModuleName = $request->get('sourceModule');
		$parentRecordId = $request->get('sourceRecord');
		return 'index.php?module='.$parentModuleName.'&relatedModule='.$moduleName.'&view=Detail&record='.$parentRecordId.'&mode=showRelatedList';
	}
	//gaocl 2015-01-05 add end
}
