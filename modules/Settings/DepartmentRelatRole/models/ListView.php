<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/*
 * Settings List View Model Class
 */

class Settings_DepartmentRelatRole_ListView_Model extends Settings_Vtiger_ListView_Model {
	/**
	 * Function to get the list view entries
	 * 获取列表实体列表
	 * @param Vtiger_Paging_Model $pagingModel
	 * @return <Array> - Associative array of record id mapped to Vtiger_Record_Model instance.
	 */
	public function getListViewEntries($pagingModel) {
		$db = PearDatabase::getInstance();

		$module = $this->getModule();
		$moduleName = $module->getName();
		$parentModuleName = $module->getParentName();
		$qualifiedModuleName = $moduleName;
		if (!empty($parentModuleName)) {
			$qualifiedModuleName = $parentModuleName . ':' . $qualifiedModuleName;
		}
		
		
		$recordModelClass = Vtiger_Loader::getComponentClassName('Model', 'Record', $qualifiedModuleName);
		$listQuery = $this->getBasicListQuery();
        
		
		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();
		$search_key=$this->get('search_key');
        if(!empty($search_key)){
            $listQuery.=' WHERE departmentid=\''.$search_key."'";
        }
		$orderBy = $this->getForSql('orderby');
		if (!empty($orderBy)) {
			$listQuery .= ' ORDER BY ' . $orderBy . ' ' . $this->getForSql('sortorder');
		}
        if($module->isPagingSupported()) {
            $nextListQuery = $listQuery.' LIMIT '.($startIndex+$pageLimit).',1';
            $listQuery .= " LIMIT $startIndex, $pageLimit";
        }
        //print_r($listQuery);die();
		$listResult = $db->pquery($listQuery, array());
		$noOfRecords = $db->num_rows($listResult);

		$listViewRecordModels = array();
		for ($i = 0; $i < $noOfRecords; ++$i) {
			$row = $db->query_result_rowdata($listResult, $i);
			$record = new $recordModelClass();
			$record->setData($row);
			$listViewRecordModels[$record->getId()] = $record;
		}
        if($module->isPagingSupported()) {
            $pagingModel->calculatePageRange($listViewRecordModels);

            $nextPageResult = $db->pquery($nextListQuery, array());
            $nextPageNumRows = $db->num_rows($nextPageResult);

            if($nextPageNumRows > 0) {
                $pagingModel->set('nextPageExists', true);
            }else{
				$pagingModel->set('nextPageExists', false);
			}
        }
        /*echo "<pre>";
        print_r($listViewRecordModels);
        exit;*/
		return $listViewRecordModels;
	}
	/*	 * * 
	 * Function which will get the list view count  
	 * @return - number of records 
	 */

	public function getListViewCount() {
		$db = PearDatabase::getInstance();

		$module = $this->getModule();
		$listQuery = 'SELECT count(1) AS count FROM ' . $module->baseTable;
        $search_key=$this->get('search_key');
        if(!empty($search_key)){
            $listQuery.=' WHERE departmentid=\''.$search_key."'";
        }


		$listResult = $db->pquery($listQuery, array());
		return $db->query_result($listResult, 0, 'count');
	}
}