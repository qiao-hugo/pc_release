<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ServiceComments_RelationListView_Model extends Vtiger_RelationListView_Model {
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
		
		$query="select
				vtiger_servicemaintenance.servicemaintenanceid,
				vtiger_servicemaintenance.addtime,
    			vtiger_servicecomments.serviceid,
				vtiger_salesorderproductsrel.productid,
    			vtiger_salesorderproductsrel.ownerid,
    			vtiger_servicecomments.related_to,
				vtiger_servicemaintenance.isoptimize,
				vtiger_servicemaintenance.issuetype,
				vtiger_servicemaintenance.content,
				(if(ISNULL(finishtime),null,
				ROUND(TIMESTAMPDIFF(SECOND,STR_TO_DATE(vtiger_servicemaintenance.addtime,'%Y-%m-%d %H:%i:%s'),STR_TO_DATE(vtiger_servicemaintenance.finishtime,'%Y-%m-%d %H:%i:%s'))/60/60,2)
				)) as timeconsuming,
				if(isnull(vtiger_servicemaintenance.processstate),'未处理',vtiger_servicemaintenance.processstate) as processstate,
    			vtiger_servicemaintenance.salesorderserviceamount,
				vtiger_servicemaintenance.remark
				from vtiger_servicecomments
    			left JOIN vtiger_servicemaintenance on(vtiger_servicemaintenance.servicecommentsid=vtiger_servicecomments.servicecommentsid)
				left JOIN vtiger_salesorderproductsrel on(vtiger_salesorderproductsrel.salesorderproductsrelid=vtiger_servicecomments.salesorderproductsrelid)
				where 1=1 and vtiger_servicemaintenance.servicecommentsid=? and vtiger_servicecomments.assigntype='productby'";
			
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
            $record->setId($row['servicemaintenanceid']);
			$relatedRecordList[$row['servicemaintenanceid']] = $record;
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