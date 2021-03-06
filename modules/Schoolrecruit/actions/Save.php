<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Schoolrecruit_Save_Action extends Vtiger_Action_Controller {

	public function checkPermission(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$record = $request->get('record');

		if(!Users_Privileges_Model::isPermitted($moduleName, 'Save', $record)) {
			throw new AppException('LBL_PERMISSION_DENIED');
		}
	}


	public function process(Vtiger_Request $request) {
		
		$recordModel = $this->saveRecord($request);
		

		if($request->get('relationOperation')) {
					
			$loadUrl = $this->getParentRelationsListViewUrl($request);
		} else if ($request->get('returnToList')) {
			$loadUrl = $recordModel->getModule()->getListViewUrl();
		} else {
			$loadUrl = $this->getDetailViewUrl($recordModel->getId());
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
		
		$record = $request->get('record');

		global $adb;
		$accompany = $request->get('accompany');
		if (empty($record)) {
			$schoolcontacts = $request->get('schoolcontacts');
			
			$query = "update vtiger_schoolrecruit set schoolcontacts='$schoolcontacts' where schoolrecruitid='".$recordModel->getId()."'";
			$adb->pquery($query, array());

			$createuserid = $request->get('createuserid');
			$sql = "INSERT INTO `vtiger_schoolrecruitsign` (`schoolrecruitsignid`, `schoolrecruitid`, `userid`, `signtype`, `signtime`, `signaddress`, `issign`, `signnum`, `coordinate`) VALUES (NULL, ?, ?, ?, '', '', '', '1', '')";
			$insertData = array($recordModel->getId(), $createuserid, '?????????');

			if (!empty($accompany)) {
				foreach ($accompany as $value) {
					$insertData[] = $recordModel->getId();
					$insertData[] = $value;
					$insertData[] = '?????????';

					$sql .= ",(NULL, ?, ?, ?, '', '', '', '1', '')";
				}
			}
			$adb->pquery($sql, $insertData);
		} else {
			// ??????
			$sql = "select * from vtiger_schoolrecruitsign where schoolrecruitid=? AND  signtype=? group by userid";
			$sel_result = $adb->pquery($sql, array($record, '?????????'));
			$res_cnt = $adb->num_rows($sel_result);
			$accompany = empty($accompany) ? array() : $accompany;

			if($res_cnt > 0) {
				$in_db_arr = array();
				while($rawData = $adb->fetch_array($sel_result)) {
					$in_db_arr[] = $rawData['userid'];
		        }

		        
		        $diff1 = array_diff($accompany, $in_db_arr); // ?????????
		        $diff2 = array_diff($in_db_arr, $accompany); // ?????????

		        $t_sql = array();
		        foreach($diff1 as $value) {
					$t_sql[] = " (NULL, '$record', '$value', '?????????', '', '', '', '1', '') ";
				}
				if (count($t_sql) > 0) {
					$sql = "INSERT INTO `vtiger_schoolrecruitsign` (`schoolrecruitsignid`, `schoolrecruitid`, `userid`, `signtype`, `signtime`, `signaddress`, `issign`, `signnum`, `coordinate`) VALUES  " . implode(',', $t_sql);
					$adb->pquery($sql, array());
				}

				$t_userid_arr = array();
				foreach($diff2 as $value) {
					$t_userid_arr[] = $value;
				}
				if (count($t_userid_arr) > 0) {
					$sql = "delete from vtiger_schoolrecruitsign where schoolrecruitid=? AND userid in (". implode(',', $t_userid_arr) .")";
					$adb->pquery($sql, array($record, $value));
				}
			} else {
				$t_sql = array();
				foreach($accompany as $value) {
					$t_sql[] = " (NULL, '$record', '$value', '?????????', '', '', '', '1', '') ";
				}
				if (count($t_sql) > 0) {
					$sql = "INSERT INTO `vtiger_schoolrecruitsign` (`schoolrecruitsignid`, `schoolrecruitid`, `userid`, `signtype`, `signtime`, `signaddress`, `issign`, `signnum`, `coordinate`) VALUES   " . implode(',', $t_sql);
					$adb->pquery($sql, array());
				}
			}

		}
		

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
	 * ?????????????????????????????????????????????URL??????
	 * @param Vtiger_Request $request
	 * @return ??????????????????URL
	 */
	public function getParentRelationsListViewUrl(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$parentModuleName = $request->get('sourceModule');
		$parentRecordId = $request->get('sourceRecord');
		return 'index.php?module='.$parentModuleName.'&relatedModule='.$moduleName.'&view=Detail&record='.$parentRecordId.'&mode=showRelatedList';
	}

	public function getDetailViewUrl($id) {
		return 'index.php?module=Schoolrecruit&view=Detail&record='.$id;
	}
	//gaocl 2015-01-05 add end
}
