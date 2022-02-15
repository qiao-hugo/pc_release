<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class TaskPackage_RelationListView_Model extends Vtiger_RelationListView_Model {
	public function getEntries($pagingModel) {
		$db = PearDatabase::getInstance();
		$parentModule = $this->getParentRecordModel()->getModule();
		$relationModule = $this->getRelationModel()->getRelationModuleModel();
		$relatedColumnFields = $relationModule->getConfigureRelatedListFields();
		if(count($relatedColumnFields) <= 0){
			$relatedColumnFields = $relationModule->getRelatedListFields();
		}
		
		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();
		
		$query="SELECT
					vtiger_servicetask.taskname,
				    vtiger_servicetask.runmode,
					IF (
					vtiger_servicetask.runconditiontype = 0,
					concat(
						vtiger_servicetask.relativeday,
						'天后开始'
					),
					concat(
						'每',
						vtiger_servicetask.circulationday,
						'天执行1次,循环',
						vtiger_servicetask.circulationcount,
						'次')
					) AS runconditiontype,
					vtiger_servicetask.relativeday,
					vtiger_servicetask.circulationday,
					vtiger_servicetask.circulationcount,
					concat(vtiger_servicetask.timeconsuming,'工作日') as timeconsuming,
					vtiger_servicetask.remark,
					vtiger_servicetask.servicetaskid
				FROM
					vtiger_servicetask
				WHERE
					vtiger_servicetask.taskpackageid=?";
			
		$limitQuery = $query .' LIMIT '.$startIndex.','.$pageLimit;

		$result = $db->pquery($limitQuery, array($_REQUEST['record']));
		$relatedRecordList = array();

		for($i=0; $i< $db->num_rows($result); $i++ ) {
			$row = $db->fetch_row($result,$i);
			$newRow = array();
			foreach($row as $col=>$val){
				if(array_key_exists($col,$relatedColumnFields)){
                    $newRow[$relatedColumnFields[$col]] = $val;
                }
            }
			//To show the value of "Assigned to"
			$newRow['assigned_user_id'] = $row['smownerid'];
			$record = Vtiger_Record_Model::getCleanInstance($relationModule->get('name'));
            $record->setData($newRow)->setModuleFromInstance($relationModule);
            $record->setId($row['servicetaskid']);
			$relatedRecordList[$row['servicetaskid']] = $record;
		}
		
		$pagingModel->calculatePageRange($relatedRecordList);

		$nextLimitQuery = $query. ' LIMIT '.($startIndex+$pageLimit).' , 1';
		$nextPageLimitResult = $db->pquery($nextLimitQuery, array());
		if($db->num_rows($nextPageLimitResult) > 0){
			$pagingModel->set('nextPageExists', true);
		}else{
			$pagingModel->set('nextPageExists', false);
		}
		return $relatedRecordList;
	}

}