<?php
/*+*************
 * 列表基类
 * 
 * 
 * 
 * 
 * All
 **************/

class Vtiger_List_View extends Vtiger_Index_View {
	protected $listViewEntries = false;
	protected $listViewCount = false;
	protected $listViewLinks = false;
	protected $listViewHeaders = false;
	
	function __construct() {
		parent::__construct();
	}

	function preProcess(Vtiger_Request $request, $display=true) {
		
		
		parent::preProcess($request, false);
        $viewer = $this->getViewer ($request);
        $moduleName = $request->getModule();
        //$this->viewName = $request->get('viewname');
       // $viewer->assign('VIEWNAME', $this->viewName);
        global $current_user;
        $userId=$current_user->id;
        $viewer->assign('USERID',$userId);
        $listViewModel = Vtiger_ListView_Model::getInstance($moduleName);
        $linkParams = array('MODULE'=>$moduleName, 'ACTION'=>$request->get('view'));// module 和action

        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        $recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($moduleModel);
        $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
       // $viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());
        $viewer->assign('SEARCHRECORD_STRUCTURE', $moduleModel->getSearchFields());
        $viewer->assign('MODULE_MODEL',$moduleModel);
        $viewer->assign('SOURCE_MODULE',$moduleName);

        $quickLinkModels = $listViewModel->getSideBarLinks($linkParams);
        $viewer->assign('QUICK_LINKS', $quickLinkModels);




     /*    if(empty($this->viewName)){
            //If not view name exits then get it from custom view
            //This can return default view id or view id present in session
            $customView = new CustomView();
            $this->viewName = $customView->getViewId($moduleName);
        } */
        $this->initializeListViewContents($request, $viewer);//竟然调用两
        //$viewer->assign('VIEWID', $this->viewName);

        if($display) {
            $this->preProcessDisplay($request);
        }
		
		

	}

	function preProcessTplName(Vtiger_Request $request=null) {

		return 'ListViewPreProcess.tpl';
	}

	//Note : To get the right hook for immediate parent in PHP,
	// specially in case of deep hierarchy
	/*function preProcessParentTplName(Vtiger_Request $request) {
		return parent::preProcessTplName($request);
	}*/

	protected function preProcessDisplay(Vtiger_Request $request) {
		parent::preProcessDisplay($request);
	}


	function process (Vtiger_Request $request) {
		$viewer = $this->getViewer ($request);
		$moduleName = $request->getModule();
		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据



		$this->viewName = $request->get('viewname');

		if(empty($this->viewName)){
			//If not view name exits then get it from custom view
			//This can return default view id or view id present in session
			$customView = new CustomView();
			$this->viewName = $customView->getViewId($moduleName);
		}
		$this->initializeListViewContents($request, $viewer);//竟然调用两次，

		$viewer->assign('VIEW', $request->get('view'));
		$viewer->assign('MODULE_MODEL', $moduleModel);
		$viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());


		$cv=new CustomView_EditAjax_View;
		$vr=new Vtiger_Request;
		$vr->set('source_module',$moduleName);
		$vr->set('module','CustomView');
		$vr->set('view','EditAjax');
		$vr->set('record',$request->get('viewname'));
		$cv->getSearch($vr,$viewer);

		$viewer->view('ListViewContents.tpl', $moduleName);
		
	}

	function postProcess(Vtiger_Request $request) {
		$viewer = $this->getViewer ($request);
		$moduleName = $request->getModule();

		$viewer->view('ListViewPostProcess.tpl', $moduleName);
		parent::postProcess($request);
	}

	/**
	 * Function to get the list of Script models to be included
	 * @param Vtiger_Request $request
	 * @return <Array> - List of Vtiger_JsScript_Model instances
	 */
	function getHeaderScripts(Vtiger_Request $request) {
		$headerScriptInstances = parent::getHeaderScripts($request);
		$moduleName = $request->getModule();

		$jsFileNames = array(
			'modules.Vtiger.resources.List',
			"modules.$moduleName.resources.List",
			//'modules.CustomView.resources.CustomView',
			//"modules.$moduleName.resources.CustomView",
			//"modules.Emails.resources.MassEdit",邮件的
			"modules.Vtiger.resources.CkEditor"
		);

		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
	}

	/*
	 * Function to initialize the required data in smarty to display the List View Contents
	 * 初始化smarty列表显示的内容的数据
	 */
	public function initializeListViewContents(Vtiger_Request $request, Vtiger_Viewer $viewer) {
		$moduleName = $request->getModule();
		$cvId = $this->viewName;
		
		$pageNumber = $request->get('page');//页数
		$orderBy = $request->get('orderby');//排序
		$sortOrder = $request->get('sortorder');//排序
		if($sortOrder == "ASC"){
			$nextSortOrder = "DESC";
			$sortImage = "icon-chevron-down";
		}else{
			$nextSortOrder = "ASC";
			$sortImage = "icon-chevron-up";
		}

		if(empty ($pageNumber)){
			$pageNumber = '1';
		}

		$listViewModel = Vtiger_ListView_Model::getInstance($moduleName, $cvId);//初始化各种数据,在这里其实初始化的是module_listview_model类，次类又同时将QueryGenerator,CustomView包含了

		$linkParams = array('MODULE'=>$moduleName, 'ACTION'=>$request->get('view'), 'CVID'=>$cvId);
		$linkModels = $listViewModel->getListViewMassActions($linkParams);//删除，编辑按钮
		$pagingModel = new Vtiger_Paging_Model();   //分页
		$pagingModel->set('page', $pageNumber);

		if(!empty($orderBy)) {
			$listViewModel->set('orderby', $orderBy);
			$listViewModel->set('sortorder',$sortOrder);
		}

		$searchKey = $request->get('search_key');
		$searchValue = $request->get('search_value');
		$operator = $request->get('operator');
		if(!empty($operator)) {
			$listViewModel->set('operator', $operator);
			$viewer->assign('OPERATOR',$operator);
			$viewer->assign('ALPHABET_VALUE',$searchValue);
		}
		if(!empty($searchKey) && !empty($searchValue)) {
			$listViewModel->set('search_key', $searchKey);
			$listViewModel->set('search_value', $searchValue);
		}
		if(!$this->listViewHeaders){
			$this->listViewHeaders = $listViewModel->getListViewHeaders();
		}
		if(!$this->listViewEntries){
			$this->listViewEntries = $listViewModel->getListViewEntries($pagingModel);
		}
		$db=PearDatabase::getInstance();
// 		wangbin2014年12月23日14:43:53 新增 工单阶段修改
// 		foreach($this->listViewEntries as $value){
// 			$id = $value->getId();
// 			$value->salordertype;

// 			$workflowerstagename = $db->pquery('SELECT workflowstagesname from vtiger_salesorderworkflowstages where salesorderid=? and isaction=?', array($id,1));
// 			$resjecting = $db->pquery('select auditorid from vtiger_salesorderworkflowstages where salesorderid=? and isrejecting=?',array($id,1));
// // 			$isreject =$db->pquery('select salesorderhistoryid from vtiger_salesorderhistory where salesorderid=? limit 1',array($id));

// 			if($resjecting->fields){
// 				$value->salordertype="打回";
// 			}elseif($workflowerstagename->fields[0]){
// 				/* $value->salordertype="正常"; */
// 				$value->salordertype=$workflowerstagename->fields[0];
// 			}else{
// 				$value->salordertype="完成";
// 			}
// 		}

		$noOfEntries = count($this->listViewEntries);

		$viewer->assign('MODULE', $moduleName);

		if(!$this->listViewLinks){
			$this->listViewLinks = $listViewModel->getListViewLinks($linkParams);
		}
		$viewer->assign('LISTVIEW_LINKS', $this->listViewLinks);

		$viewer->assign('LISTVIEW_MASSACTIONS', $linkModels['LISTVIEWMASSACTION']);

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

		if (PerformancePrefs::getBoolean('LISTVIEW_COMPUTE_PAGE_COUNT', false)) {
			if(!$this->listViewCount){
				$this->listViewCount = $listViewModel->getListViewCount();
			}
			$totalCount = $this->listViewCount;
			$pageLimit = $pagingModel->getPageLimit();
			$pageCount = ceil((int) $totalCount / (int) $pageLimit);

			if($pageCount == 0){
				$pageCount = 1;
			}
			$viewer->assign('PAGE_COUNT', $pageCount);
			$viewer->assign('LISTVIEW_COUNT', $totalCount);
		}

		$viewer->assign('IS_MODULE_EDITABLE', $listViewModel->getModule()->isPermitted('EditView'));
		$viewer->assign('IS_MODULE_DELETABLE', $listViewModel->getModule()->isPermitted('Delete'));
	}

	/**
	 * Function returns the number of records for the current filter
	 * @param Vtiger_Request $request
	 */
	function getRecordsCount(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$cvId = $request->get('viewname');
		$count = $this->getListViewCount($request);

		$result = array();
		$result['module'] = $moduleName;
		$result['viewname'] = $cvId;
		$result['count'] = $count;

		$response = new Vtiger_Response();
		$response->setEmitType(Vtiger_Response::$EMIT_JSON);
		$response->setResult($result);
		$response->emit();
	}

	/**
	 * Function to get listView count
	 * @param Vtiger_Request $request
	 */
	function getListViewCount(Vtiger_Request $request){
		$moduleName = $request->getModule();
		$cvId = $request->get('viewname');
		if(empty($cvId)) {
			$cvId = '0';
		}

		$searchKey = $request->get('search_key');
		$searchValue = $request->get('search_value');

		$listViewModel = Vtiger_ListView_Model::getInstance($moduleName, $cvId);
		$listViewModel->set('search_key', $searchKey);
		$listViewModel->set('search_value', $searchValue);
		$listViewModel->set('operator', $request->get('operator'));

		$count = $listViewModel->getListViewCount();

		return $count;
	}



	/**
	 * Function to get the page count for list
	 * @return total number of pages
	 */
	function getPageCount(Vtiger_Request $request){
		$listViewCount = $this->getListViewCount($request);
		$pagingModel = new Vtiger_Paging_Model();
		$pageLimit = $pagingModel->getPageLimit();
		$pageCount = ceil((int) $listViewCount / (int) $pageLimit);

		if($pageCount == 0){
			$pageCount = 1;
		}
		$result = array();
		$result['page'] = $pageCount;
		$result['numberOfRecords'] = $listViewCount;
		$response = new Vtiger_Response();
		$response->setResult($result);
		$response->emit();
	}
}