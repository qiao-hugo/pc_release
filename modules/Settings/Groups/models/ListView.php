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

class Settings_Groups_ListView_Model extends Vtiger_Base_Model {

	/**
	 * Function to get the Module Model
	 * @return Vtiger_Module_Model instance
	 */
	public function getModule() {
		return $this->module;
	}

	public function setModule($name) {
		$modelClassName = Vtiger_Loader::getComponentClassName('Model', 'Module', $name);
		$this->module = new $modelClassName();
		return $this;
	}

	public function setModuleFromInstance($module) {
		$this->module = $module;
		return $this;
	}

	/**
	 * Function to get the list view header
	 * @return <Array> - List of Vtiger_Field_Model instances
	 */
	public function getListViewHeaders() {
		$module = $this->getModule();
		return $module->getListFields();
	}
    
    public function getBasicListQuery() {
        $module = $this->getModule();
        return 'SELECT * FROM '. $module->getBaseTable();
    }

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
//        $queryGenerator = $this->get('query_generator');
		$listQuery = $this->getBasicListQuery();
        
		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();

        $searchKey = $this->get('search_key');
        $searchValue = trim($this->get('search_value'));
        $operator = $this->get('operator');

        $link_code = 'where';
        if(strpos($listQuery, 'WHERE')){
            $link_code = 'and';
        }
        if(!empty($searchKey) &&!empty($searchValue)) {
            if($searchKey == 'groupname'){
                $listQuery .= " {$link_code}  {$searchKey} like '%{$searchValue}%' ";
            }
            if($searchKey == 'username'){
                $listQuery = str_replace('SELECT *','SELECT a.*',$listQuery);
                $listQuery .= ' a left join vtiger_users2group b on a.groupid = b.groupid left join vtiger_users c on b.userid = c.id where c.last_name = "'.$searchValue.'"';
            }
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

			if (method_exists($record, 'getModule') && method_exists($record, 'setModule')) {
				$moduleModel = Settings_Vtiger_Module_Model::getInstance($qualifiedModuleName);
				$record->setModule($moduleModel);
			}

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
		return $listViewRecordModels;
	}
	
	public function getListViewLinks() {
		$links = array();
		$basicLinks = $this->getBasicLinks();
		
		foreach($basicLinks as $basicLink) {
			$links['LISTVIEWBASIC'][] = Vtiger_Link_Model::getInstanceFromValues($basicLink);
		}
		return $links;
	}
	
	/*
	 * Function to get Basic links
	 * @return array of Basic links
	 */
	public function getBasicLinks(){
		$basicLinks = array();
		$moduleModel = $this->getModule();
		if($moduleModel->hasCreatePermissions())
			$basicLinks[] = array(
					'linktype' => 'LISTVIEWBASIC',
					'linklabel' => 'LBL_ADD_RECORD',
					'linkurl' => $moduleModel->getCreateRecordUrl(),
					'linkicon' => ''
			);
		
		return $basicLinks;
	}

	/*	 * *
	 * Function which will get the list view count
	 * @return - number of records
	 */

	public function getListViewCount() {
		$db = PearDatabase::getInstance();

		$module = $this->getModule();
        $listQuery = $this->getBasicListQuery();

        $searchKey = $this->get('search_key');
        $searchValue = trim($this->get('search_value'));
        $operator = $this->get('operator');

        $link_code = 'where';
        if(strpos($listQuery, 'WHERE')){
            $link_code = 'and';
        }
        if(!empty($searchKey) &&!empty($searchValue)) {
            if($searchKey == 'groupname'){
                $listQuery .= " {$link_code}  {$searchKey} like '%{$searchValue}%' ";
            }
            if($searchKey == 'username'){
                $listQuery = str_replace('SELECT *','SELECT a.*',$listQuery);
                $listQuery .= ' a left join vtiger_users2group b on a.groupid = b.groupid left join vtiger_users c on b.userid = c.id where c.last_name = "'.$searchValue.'"';
            }
        }
        $orderBy = $this->getForSql('orderby');
        if (!empty($orderBy)) {
            $listQuery .= ' ORDER BY ' . $orderBy . ' ' . $this->getForSql('sortorder');
        }
//		$listQuery = 'SELECT count(*) AS count FROM ' . $module->baseTable;

		$listResult = $db->pquery($listQuery, array());
        return $db->num_rows($listResult);
	}

	/**
	 * Function to get the instance of Settings module model
	 * @return Settings_Vtiger_Module_Model instance
	 */
	public static function getInstance($name = 'Settings:Vtiger') {
		$modelClassName = Vtiger_Loader::getComponentClassName('Model', 'ListView', $name);
		$instance = new $modelClassName();
		return $instance->setModule($name);
	}
}