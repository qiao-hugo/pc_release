<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_DepartmentRelatRole_List_View extends Settings_Vtiger_List_View {

	function __construct() {
		parent::__construct();
	}

	/*
	 * Function to initialize the required data in smarty to display the List View Contents
	 */
	public function initializeListViewContents(Vtiger_Request $request, Vtiger_Viewer $viewer) {
		$moduleName = $request->getModule();
		$qualifiedModuleName = $request->getModule(false);
		$pageNumber = $request->get('page');
		$orderBy = $request->get('orderby');
		$sortOrder = $request->get('sortorder');
		$sourceModule = $request->get('sourceModule');
		$forModule = $request->get('formodule');
		
		$searchKey = $request->get('search_key');
		$searchValue = $request->get('search_value');
		
		if($sortOrder == "ASC"){
			$nextSortOrder = "DESC";
			$sortImage = "icon-chevron-down";
		}else{
			$nextSortOrder = "ASC";
			$sortImage = "icon-chevron-up";
		}
		if(empty($pageNumber)) {
			$pageNumber = 1;
		}

		$listViewModel = Settings_Vtiger_ListView_Model::getInstance($qualifiedModuleName);
		$pagingModel = new Vtiger_Paging_Model();
		$pagingModel->set('page', $pageNumber);

		if(!empty($searchKey) && !empty($searchValue)) {
			$listViewModel->set('search_key', $searchKey);
			$listViewModel->set('search_value', $searchValue);
		}
        if(!empty($searchKey)) {
            $listViewModel->set('search_key', $searchKey);
        }
		
		if(!empty($orderBy)) {
			$listViewModel->set('orderby', $orderBy);
			$listViewModel->set('sortorder',$sortOrder);
		}
		if(!empty($sourceModule)) {
			$listViewModel->set('sourceModule', $sourceModule);
		}
		if(!empty($forModule)) {
			$listViewModel->set('formodule', $forModule);
		}
		if(!$this->listViewHeaders){
			$this->listViewHeaders = $listViewModel->getListViewHeaders();
		}
		if(!$this->listViewEntries){
			$this->listViewEntries = $listViewModel->getListViewEntries($pagingModel);
		}
		$noOfEntries = count($this->listViewEntries);
		if(!$this->listViewLinks){
			$this->listViewLinks = $listViewModel->getListViewLinks();
		}
		$viewer->assign('LISTVIEW_LINKS', $this->listViewLinks);
		
		$viewer->assign('MODULE', $moduleName);
		$viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
		$viewer->assign('MODULE_MODEL', $listViewModel->getModule());

		$viewer->assign('PAGING_MODEL', $pagingModel);
		$viewer->assign('PAGE_NUMBER',$pageNumber);

		$viewer->assign('ORDER_BY',$orderBy);
		$viewer->assign('SORT_ORDER',$sortOrder);
		$viewer->assign('NEXT_SORT_ORDER',$nextSortOrder);
		$viewer->assign('SORT_IMAGE',$sortImage);
		$viewer->assign('COLUMN_NAME',$orderBy);

		$viewer->assign('LISTVIEW_ENTIRES_COUNT',$noOfEntries);
		$viewer->assign('LISTVIEW_HEADERS', $this->listViewHeaders);
		$viewer->assign('LISTVIEW_ENTRIES', $this->listViewEntries);
		$viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $recordModel =new Settings_DepartmentRelatRole_Record_Model;

        if(!$this->listViewCount){
            $this->listViewCount = $listViewModel->getListViewCount();
        }
        $totalCount = $this->listViewCount;
        $pageLimit = $pagingModel->getPageLimit();
        $pageCount=$totalCount / 20;
        $pageCount = ceil($pageCount);
        if($pageCount == 0){
            $pageCount = 1;
        }
        $viewer->assign('ROLE', json_encode($recordModel->getRole(),JSON_UNESCAPED_UNICODE));
        $viewer->assign('DEPARTMENT', json_encode($recordModel->getCacheDepartment(),JSON_UNESCAPED_UNICODE));
        $viewer->assign('PAGE_COUNT', $pageCount);
        $viewer->assign('DEPARTMENTDATAS', $recordModel->getCacheDepartment());
        $viewer->assign('LISTVIEW_COUNT', $totalCount);
	}

}
