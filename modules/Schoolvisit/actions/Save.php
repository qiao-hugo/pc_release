<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Schoolvisit_Save_Action extends Vtiger_Action_Controller {

	public function checkPermission(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$record = $request->get('record');

		if(!Users_Privileges_Model::isPermitted($moduleName, 'Save', $record)) {
			throw new AppException('LBL_PERMISSION_DENIED');
		}
	}


	public function process(Vtiger_Request $request) {
		
		$recordModel = $this->saveRecord($request);
		

		global $adb;

		// 陪同人  //extractid提单人
		$accompany = $request->get('accompany');
		$extractid = $request->get('extractid');

		$visitingorderid = $recordModel->getId();
		$tt_record = $request->get('record');
		if (! empty($accompany)) {
			$t_id_arr = explode(' |##| ', $accompany);
			
			if (! empty($tt_record)) {
				// 更新
				$sql = "select * from vtiger_schoolvisitsign where visitingorderid=? AND  visitsigntype=? group by userid";
				$sel_result = $adb->pquery($sql, array($visitingorderid, '陪同人'));
				$res_cnt = $adb->num_rows($sel_result);

				if($res_cnt > 0) {
					$in_db_arr = array();
					while($rawData = $adb->fetch_array($sel_result)) {
						$in_db_arr[] = $rawData['userid'];
			        }

			        $diff1 = array_diff($accompany, $in_db_arr); // 添加的
			        $diff2 = array_diff($in_db_arr, $accompany); // 删除的

			        $t_sql = array();
			        foreach($diff1 as $value) {
						$t_sql[] = " (null, $visitingorderid, $value,'陪同人','1','') ";
						$t_sql[] = " (null, $visitingorderid, $value,'陪同人','2','') ";
					}
					if (count($t_sql) > 0) {
						$sql = "insert into vtiger_schoolvisitsign (visitsignid, visitingorderid, userid, visitsigntype,signnum,coordinate) VALUES " . implode(',', $t_sql);
						$adb->pquery($sql, array());
					}

					$t_userid_arr = array();
					foreach($diff2 as $value) {
						$t_userid_arr[] = $value;
					}
					if (count($t_userid_arr) > 0) {
						$sql = "delete from vtiger_schoolvisitsign where visitingorderid=? AND userid in (". implode(',', $t_userid_arr) .")";
						$adb->pquery($sql, array($visitingorderid, $value));
					}
				} else {
					$t_sql = array();
					foreach($accompany as $value) {
						$t_sql[] = " (null, $visitingorderid, $value,'陪同人','1','') ";
						$t_sql[] = " (null, $visitingorderid, $value,'陪同人','2','') ";
					}
					if (count($t_sql) > 0) {
						$sql = "insert into vtiger_schoolvisitsign (visitsignid, visitingorderid, userid, visitsigntype,signnum,coordinate) VALUES " . implode(',', $t_sql);
						$adb->pquery($sql, array());
					}
				}
					/*$sql = "delete from vtiger_visitsign where visitingorderid=? AND visitsigntype=?";
					$adb->pquery($sql, array($visitingorderid, '陪同人'));*/
			} else {
				//添加
				$sql = "insert into vtiger_schoolvisitsign (visitsignid, visitingorderid, userid, visitsigntype,signnum,coordinate) value (null, $visitingorderid, $extractid,'提单人','1','')";
				$adb->pquery($sql, array());

				//添加
				$sql = "insert into vtiger_schoolvisitsign (visitsignid, visitingorderid, userid, visitsigntype,signnum,coordinate) value (null, $visitingorderid, $extractid,'提单人','2','')";
				$adb->pquery($sql, array());

				$t_sql = array();
				foreach($accompany as $value) {
					$t_sql[] = " (null, $visitingorderid, $value,'陪同人','1','') ";
					$t_sql[] = " (null, $visitingorderid, $value,'陪同人','2','') ";
				}
				if (count($t_sql) > 0) {
					$sql = "insert into vtiger_schoolvisitsign (visitsignid, visitingorderid, userid, visitsigntype,signnum,coordinate) VALUES " . implode(',', $t_sql);
					$adb->pquery($sql, array());
				}
				
			}
		} else {
			//没有陪同人
			if (! empty($tt_record)) {  //更新操作
				$sql = "update vtiger_schoolvisit set accompany=?  where schoolvisitid=?";
				$adb->pquery($sql, array('', $visitingorderid));

				$sql = "delete from vtiger_schoolvisitsign where visitingorderid=? AND visitsigntype=?";
				$adb->pquery($sql, array($visitingorderid, '陪同人'));
			} else {   //添加操作
				// 如果是添加的时候 没有陪同人 也要添加到vtiger_visitsign签到表里面
				$sql = "insert into vtiger_schoolvisitsign (visitsignid, visitingorderid, userid, visitsigntype,signnum,coordinate) value (null, $visitingorderid, $extractid,'提单人', '1', '')";
				$adb->pquery($sql, array());
				$sql = "insert into vtiger_schoolvisitsign (visitsignid, visitingorderid, userid, visitsigntype,signnum,coordinate) value (null, $visitingorderid, $extractid,'提单人', '2', '')";
				$adb->pquery($sql, array());
			}
		}



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

	public function getDetailViewUrl($id) {
		return 'index.php?module=Schoolvisit&view=Detail&record='.$id;
	}
	//gaocl 2015-01-05 add end
}
