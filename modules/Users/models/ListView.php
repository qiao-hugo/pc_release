<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Users_ListView_Model extends Vtiger_ListView_Model {

	/**
	 * Function to get the list of listview links for the module
	 * @param <Array> $linkParams
	 * @return <Array> - Associate array of Link Type to List of Vtiger_Link_Model instances
	 */
	public function getListViewLinks($linkParams) {
		$linkTypes = array('LISTVIEWBASIC', 'LISTVIEW', 'LISTVIEWSETTING');
		$links = Vtiger_Link_Model::getAllByType($this->getModule()->getId(), $linkTypes, $linkParams);

		$basicLinks = array(
			array(
				'linktype' => 'LISTVIEWBASIC',
				'linklabel' => 'LBL_ADD_RECORD',
				'linkurl' => $this->getModule()->getCreateRecordUrl(),
				'linkicon' => ''
			)
		);
		foreach($basicLinks as $basicLink) {
			$links['LISTVIEWBASIC'][] = Vtiger_Link_Model::getInstanceFromValues($basicLink);
		}

		return $links;
	}

	/**
	 * Function to get the list of Mass actions for the module
	 * @param <Array> $linkParams
	 * @return <Array> - Associative array of Link type to List of  Vtiger_Link_Model instances for Mass Actions
	 */
	public function getListViewMassActions($linkParams) {
		return array();
	}

	/**
	 * 返回模块访问时列表数据
	 * @return string
	 */
	public function getQuery() {
		$listQuery = parent::getQuery();
        //remove the status active condition since in users list view we need to consider inactive users as well
        $listQueryComponents = explode(" WHERE vtiger_users.status='Active' AND",$listQuery);
        $listQuery = implode(' WHERE ', $listQueryComponents);
		//其他模块弹出时
		$sourcemodule=$this->get('src_module');
		$src_field=$this->get('src_field');
		if($sourcemodule=='Users'){
            $listQuery.="  AND vtiger_users.status='Active'";
        }
		if(empty($sourcemodule)){
			$sourcemodule='Users';
		}
        if($sourcemodule=='SaleManager'){
        	$listQuery.=' and id not in (SELECT relatetoid from vtiger_salemanager)';
        }
		if($sourcemodule!='Users'){
			$where=getAccessibleUsers($sourcemodule);	
			if($where!='1=1'){
				$listQuery .= ' and vtiger_users.id '.$where;	
			}	
		}else{
			if(empty($src_field)){
				$listQuery .=" and departmentid !=''";
			}
			
		}
		return $listQuery;
	}

	/**
	 * Function to get the list view entries
	 * @param Vtiger_Paging_Model $pagingModel
	 * @return <Array> - Associative array of record id mapped to Vtiger_Record_Model instance.
	 */
	public function getListViewEntries($pagingModel) {	
		$db = PearDatabase::getInstance();
		$queryGenerator = $this->get('query_generator');
		
		$searchKey = $this->get('search_key');
		$searchValue = $this->get('search_value');
		$operator = $this->get('operator');

		if(!empty($searchKey)) {
			$queryGenerator->addUserSearchConditions(array('search_field' => $searchKey, 'search_text' => $searchValue, 'operator' => $operator));
		}

		$listViewContoller = $this->get('listview_controller');
		// Added as Users module do not have custom filters and id column is added by querygenerator.
		$fields = $queryGenerator->getFields();
		$fields[] = 'id';
		$queryGenerator->setFields($fields);
		$moduleFocus = CRMEntity::getInstance('Users');
		$moduleModel = Vtiger_Module_Model::getInstance('Users');
		$listQuery = $this->getQuery();
        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();
        $listQuery .= " LIMIT $startIndex,".($pageLimit);
		
		$listResult = $db->pquery($listQuery, array());
		
		$listViewRecordModels = array();
		$listViewEntries =  $listViewContoller->getListViewRecords($moduleFocus,'Users', $listResult);//获取视图的数据记录,这里已经获取到了数据

		//3.在进行一次转化，目的何在
		$index = 0;
		foreach($listViewEntries as $recordId => $record) {
			$rawData = $db->query_result_rowdata($listResult, $index++);
			$record['id'] = $recordId;
			$listViewRecordModels[$recordId] = $moduleModel->getRecordFromArray($record, $rawData);
		}


		
		return $listViewRecordModels;

		//return parent::getListViewEntries($pagingModel);
	}
}
