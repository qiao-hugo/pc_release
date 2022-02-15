<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class DataTransfer_List_View extends Vtiger_List_View {
	/*
	 * Function to initialize the required data in smarty to display the List View Contents
	 * 初始化smarty列表显示的内容的数据
	 */
    protected $listViewEntries = false;
    protected $listViewCount = false;
    protected $listViewLinks = false;
    protected $listViewHeaders = false;

    function process (Vtiger_Request $request) {

        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
        $this->viewName = $request->get('viewname');
        $viewer->assign('VIEWNAME', $this->viewName);
        if ($request->isAjax()) {
            $this->initializeListViewContents($request, $viewer);//竟然调用两次，这边其实是ajax调用的，哈哈！！
            $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
        }

        $viewer->assign('VIEW', $request->get('view'));
        $viewer->assign('MODULE_MODEL', $moduleModel);


        $viewer->view('ListViewContents.tpl', $moduleName);
    }
//
//	public function initializeListViewContents(Vtiger_Request $request, Vtiger_Viewer $viewer) {
//		$moduleName = $request->getModule();
//		$cvId = $this->viewName;
//
//		$pageNumber = $request->get('page');//页数
//		$orderBy = $request->get('orderby');//排序
//		$sortOrder = $request->get('sortorder');//排序
//		if($sortOrder == "ASC"){
//			$nextSortOrder = "DESC";
//			$sortImage = "icon-chevron-down";
//		}else{
//			$nextSortOrder = "ASC";
//			$sortImage = "icon-chevron-up";
//		}
//
//		if(empty ($pageNumber)){
//			$pageNumber = '1';
//		}
//
//		$listViewModel = Vtiger_ListView_Model::getInstance($moduleName, $cvId);//初始化各种数据,在这里其实初始化的是module_listview_model类，次类又同时将QueryGenerator,CustomView包含了
//
//		$linkParams = array('MODULE'=>$moduleName, 'ACTION'=>$request->get('view'), 'CVID'=>$cvId);
//		$linkModels = $listViewModel->getListViewMassActions($linkParams);//删除，编辑按钮
//		$pagingModel = new Vtiger_Paging_Model();   //分页
//		$pagingModel->set('page', $pageNumber);
//
//		if(!empty($orderBy)) {
//			$listViewModel->set('orderby', $orderBy);
//			$listViewModel->set('sortorder',$sortOrder);
//		}
//
//		$searchKey = $request->get('search_key');
//		$searchValue = $request->get('search_value');
//		$operator = $request->get('operator');
//		if(!empty($operator)) {
//			$listViewModel->set('operator', $operator);
//			$viewer->assign('OPERATOR',$operator);
//			$viewer->assign('ALPHABET_VALUE',$searchValue);
//		}
//		if(!empty($searchKey) && !empty($searchValue)) {
//			$listViewModel->set('search_key', $searchKey);
//			$listViewModel->set('search_value', $searchValue);
//		}
//
//		if(!$this->listViewHeaders){
//			$this->listViewHeaders = $listViewModel->getListViewHeaders();
//		}
//
//		if(!$this->listViewEntries){
//			$this->listViewEntries = $listViewModel->getListViewEntries($pagingModel);
//		}
//
//        $db=PearDatabase::getInstance();
//		$noOfEntries = count($this->listViewEntries);
//		$viewer->assign('MODULE', $moduleName);
//		if(!$this->listViewLinks){
//			$this->listViewLinks = $listViewModel->getListViewLinks($linkParams);
//		}
//
//        $viewer->assign('LISTVIEW_LINKS', $this->listViewLinks);
//
//		$viewer->assign('LISTVIEW_MASSACTIONS', $linkModels['LISTVIEWMASSACTION']);
//
//		$viewer->assign('PAGING_MODEL', $pagingModel);
//		$viewer->assign('PAGE_NUMBER',$pageNumber);
//
//		$viewer->assign('ORDER_BY',$orderBy);
//		$viewer->assign('SORT_ORDER',$sortOrder);
//		$viewer->assign('NEXT_SORT_ORDER',$nextSortOrder);
//		$viewer->assign('SORT_IMAGE',$sortImage);
//		$viewer->assign('COLUMN_NAME',$orderBy);
//
//		$viewer->assign('LISTVIEW_ENTIRES_COUNT',$noOfEntries);
//		$viewer->assign('LISTVIEW_HEADERS', $this->listViewHeaders);
//		$viewer->assign('LISTVIEW_ENTRIES', $this->listViewEntries);
//
//		if (PerformancePrefs::getBoolean('LISTVIEW_COMPUTE_PAGE_COUNT', false)) {
//			if(!$this->listViewCount){
//				$this->listViewCount = $listViewModel->getListViewCount();
//			}
//			$totalCount = $this->listViewCount;
//			$pageLimit = $pagingModel->getPageLimit();
//			$pageCount = ceil((int) $totalCount / (int) $pageLimit);
//
//			if($pageCount == 0){
//				$pageCount = 1;
//			}
//			$viewer->assign('PAGE_COUNT', $pageCount);
//			$viewer->assign('LISTVIEW_COUNT', $totalCount);
//		}
//
//		$viewer->assign('IS_MODULE_EDITABLE', 0);
//		$viewer->assign('IS_MODULE_DELETABLE', 0);
//    }
    public function initializeListViewContents(Vtiger_Request $request, Vtiger_Viewer $viewer)
    {
        $moduleName = $request->getModule();
        $cvId = $this->viewName;

        $pageNumber = $request->get('page');//页数
        $orderBy = $request->get('orderby');//排序
        $sortOrder = $request->get('sortorder');//排序
        $pageLimit = $request->get('limit');//排序
        if ($sortOrder == "ASC") {
            $nextSortOrder = "DESC";
            $sortImage = "icon-chevron-down";
        } else {
            $nextSortOrder = "ASC";
            $sortImage = "icon-chevron-up";
        }

        if (empty ($pageNumber)) {
            $pageNumber = '1';
        }
        //20150416 young 每页显示数量
        if (empty ($pageLimit)) {
            $pageLimit = '20';
        }
        $listViewModel = Vtiger_ListView_Model::getInstance($moduleName, $cvId);//初始化各种数据,在这里其实初始化的是module_listview_model类，次类又同时将QueryGenerator,CustomView包含了
        $linkParams = array('MODULE' => $moduleName, 'ACTION' => $request->get('view'), 'CVID' => $cvId);
        $pagingModel = new Vtiger_Paging_Model();   //分页
        $pagingModel->set('page', $pageNumber);
        $pagingModel->set('limit', $pageLimit);//20150416 young 每页显示数量
        if (!empty($orderBy)) {
            $listViewModel->set('orderby', $orderBy);
            $listViewModel->set('sortorder', $sortOrder);
        }

        $searchKey = $request->get('search_key');
        $searchValue = $request->get('search_value');
        $operator = $request->get('operator');
        if (!empty($operator)) {
            $listViewModel->set('operator', $operator);
            $viewer->assign('OPERATOR', $operator);
            $viewer->assign('ALPHABET_VALUE', $searchValue);
        }
        if (!empty($searchKey) && !empty($searchValue)) {
            $listViewModel->set('search_key', $searchKey);
            $listViewModel->set('search_value', $searchValue);
        }
        if (!$this->listViewHeaders) {
            $this->listViewHeaders = $listViewModel->getListViewHeaders();
        }
        if (!$this->listViewEntries) {
            $this->listViewEntries = $listViewModel->getListViewEntries($pagingModel);
        }


        $noOfEntries = $listViewModel->getListViewCount();

        $viewer->assign('MODULE', $moduleName);

        if (!$this->listViewLinks) {
            $this->listViewLinks = $listViewModel->getListViewLinks($linkParams);
        }
        $viewer->assign('LISTVIEW_LINKS', $this->listViewLinks);

        $viewer->assign('LISTVIEW_MASSACTIONS', $linkModels['LISTVIEWMASSACTION']);

        $viewer->assign('PAGING_MODEL', $pagingModel);
        $viewer->assign('PAGE_NUMBER', $pageNumber);

        $viewer->assign('ORDER_BY', $orderBy);
        $viewer->assign('SORT_ORDER', $sortOrder);
        $viewer->assign('NEXT_SORT_ORDER', $nextSortOrder);
        $viewer->assign('SORT_IMAGE', $sortImage);
        $viewer->assign('COLUMN_NAME', $orderBy);

        $viewer->assign('LISTVIEW_ENTIRES_COUNT', $noOfEntries);
        //插入字段信息
        //20150428 young 将模板的字段验证转移到后台验证，便于控制
        $LISTVIEW_FIELDS = $listViewModel->getSelectFields();
        $listViewHeaders = $this->listViewHeaders;
        $temp = array();
        if (!empty($LISTVIEW_FIELDS)) {
            foreach ($LISTVIEW_FIELDS as $key => $val) {
                if (isset($listViewHeaders[$key])) {
                    $temp[$key] = $listViewHeaders[$key];
                }
            }
        }
        if (empty($temp)) {
            $temp = $listViewHeaders;
        }

        $viewer->assign('LISTVIEW_HEADERS', $temp);
        $viewer->assign('LISTVIEW_FIELDS', $listViewModel->getSelectFields());
        $viewer->assign('LISTVIEW_ENTRIES', $this->listViewEntries);
        //end


        $pageLimit = $pagingModel->getPageLimit();
        $pageCount = ceil((int)$noOfEntries / (int)$pageLimit);

        if ($pageCount == 0) {
            $pageCount = 1;
        }
        $viewer->assign('PAGE_COUNT', $pageCount);
        $viewer->assign('PAGE_CU', $pageNumber);
        $viewer->assign('LISTVIEW_COUNT', $noOfEntries);

    }
}