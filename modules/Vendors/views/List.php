<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/ 
class Vendors_List_View extends Vtiger_KList_View {

	function __construct() {
		parent::__construct();
	}

	function process (Vtiger_Request $request) {
		$strPublic = $request->get('public');
		if($strPublic == 'search') {
			$moduleName = $request->getModule();
			$productData = Vendors_Record_Model::getProducts();

            $viewer = $this->getViewer($request);
            $viewer->assign('effectdate_start', date('Y-m-d', strtotime('-3 month')));
            $viewer->assign('PRODUCTDATA', $productData);
            $viewer->view('exportrm.tpl', $moduleName);
            exit;
		} else if($strPublic == 'R_search') {
			$moduleName = $request->getModule();


			$productid = $request->get('productid');
			$effectdate_start = $request->get('effectdate_start');
			$effectdate_end = $request->get('effectdate_end');
			$enddate_start = $request->get('enddate_start');
			$enddate_end = $request->get('enddate_end');

			$productData = Vendors_Record_Model::getProducts();

			$vendorData = Vendors_Record_Model::searchVendor($productid, $effectdate_start, $effectdate_end, $enddate_start, $enddate_end);

            $viewer = $this->getViewer($request);

            $viewer->assign('effectdate_start', $effectdate_start);
            $viewer->assign('effectdate_end', $effectdate_end );
            $viewer->assign('enddate_start', $enddate_start );
            $viewer->assign('enddate_end',   $enddate_end );
            $viewer->assign('productid',     $productid );


            $viewer->assign('PRODUCTDATA', $productData);
            $viewer->assign('VEBDORDATA', $vendorData);
            $viewer->view('exportrm.tpl', $moduleName);
            exit;
		} else if($strPublic == 'sale')  {
			//Vendors_Record_Model::_logs(array('-----111------'));
			$moduleName = $request->getModule();
			$productData = Vendors_Record_Model::getProducts();
			//Vendors_Record_Model::_logs($productData);
            $viewer = $this->getViewer($request);
            $viewer->assign('effectdate_start', date('Y-m-d', strtotime('-3 month')));
            $viewer->assign('PRODUCTDATA', $productData);
            //Vendors_Record_Model::_logs(array('-----'.$moduleName.'-----'));
			$viewer->view('sale.tpl', $moduleName);
			exit;
		} else if($strPublic == 'R_sale') {
			$moduleName = $request->getModule();


			$productid = $request->get('productid');
			$effectdate_start = $request->get('effectdate_start');
			$effectdate_end = $request->get('effectdate_end');
			$enddate_start = $request->get('enddate_start');
			$enddate_end = $request->get('enddate_end');

			$productData = Vendors_Record_Model::getProducts();

			$vendorData = Vendors_Record_Model::vendorSale();

            $viewer = $this->getViewer($request);

            $viewer->assign('effectdate_start', $effectdate_start);
            $viewer->assign('effectdate_end', $effectdate_end );
            $viewer->assign('enddate_start', $enddate_start );
            $viewer->assign('enddate_end',   $enddate_end );
            $viewer->assign('productid',     $productid );


            $viewer->assign('PRODUCTDATA', $productData);
            $viewer->assign('VEBDORDATA', $vendorData);
            $viewer->view('sale.tpl', $moduleName);
            exit;
		}elseif($strPublic == 'dempartConfirm') {//非标合同部门负责审核设置
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            if(!$moduleModel->exportGrouprt('Vendors','dempartConfirm')){   //权限验证
                return;
            }
            $viewer = $this->getViewer($request);
            $viewer->assign('USER',ReceivedPayments_Record_Model::getuserinfo(" AND `status`='Active'"));
            $viewer->assign('RECOEDS',$moduleModel->getAuditsettings('VendorsAuditset'));
            $viewer->assign('DEPARTMENT',getDepartment());
            $viewer->view('dempartConfirm.tpl', $moduleName);
            exit;
        }


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

		// 获取供应商状态
		$vendorstateData = Vendors_Record_Model::getVendorstate();
		$viewer->assign('VENDORSTATE_DATA', $vendorstateData);

		/*$cv=new CustomView_EditAjax_View;
		$vr=new Vtiger_Request;
		$vr->set('source_module',$moduleName);
		$vr->set('module','CustomView');
		$vr->set('view','EditAjax');
		$vr->set('record',$request->get('viewname'));
		$cv->getSearch($vr,$viewer);*/

		$viewer->view('ListViewContents.tpl', $moduleName);
		
	}
    public function initializeListViewContents(Vtiger_Request $request, Vtiger_Viewer $viewer) {
        $moduleName = $request->getModule();
        $cvId = 0;

        $pageNumber = $request->get('page');//页数
        $orderBy = $request->get('orderby');//排序
        $sortOrder = $request->get('sortorder');//排序
        $pageLimit = $request->get('limit');//排序
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
        //20150416 young 每页显示数量
        if(empty ($pageLimit)){
            $pageLimit = '20';
        }
        $listViewModel = Vtiger_ListView_Model::getInstance($moduleName, $cvId);//初始化各种数据,在这里其实初始化的是module_listview_model类，次类又同时将QueryGenerator,CustomView包含了

        $linkParams = array('MODULE'=>$moduleName, 'ACTION'=>$request->get('view'), 'CVID'=>$cvId);

        $pagingModel = new Vtiger_Paging_Model();   //分页
        $pagingModel->set('page', $pageNumber);
        $pagingModel->set('limit', $pageLimit);//20150416 young 每页显示数量
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


        $noOfEntries = $listViewModel->getListViewCount();
        if($moduleName=='MaintainerAccount'){
            $num_acc = $listViewModel->getDistinctAcc();
            if(!$num_acc){
                $num_acc = 0;
            }
        }
        $viewer->assign('MODULE', $moduleName);

        if(!$this->listViewLinks){
            $this->listViewLinks = $listViewModel->getListViewLinks($linkParams);
        }
        $viewer->assign('LISTVIEW_LINKS', $this->listViewLinks);

        // $viewer->assign('LISTVIEW_MASSACTIONS', $linkModels['LISTVIEWMASSACTION']);

        $viewer->assign('PAGING_MODEL', $pagingModel);
        $viewer->assign('PAGE_NUMBER',$pageNumber);

        $viewer->assign('ORDER_BY',$orderBy);
        $viewer->assign('SORT_ORDER',$sortOrder);
        $viewer->assign('NEXT_SORT_ORDER',$nextSortOrder);
        $viewer->assign('SORT_IMAGE',$sortImage);
        $viewer->assign('COLUMN_NAME',$orderBy);

        $viewer->assign('LISTVIEW_ENTIRES_COUNT',$noOfEntries);
        $viewer->assign('NUM_ACC',$num_acc);
        //插入字段信息
        //20150428 young 将模板的字段验证转移到后台验证，便于控制
        $LISTVIEW_FIELDS = $listViewModel->getSelectFields();
        $listViewHeaders = $this->listViewHeaders;
        $temp = array();
        if(!empty($LISTVIEW_FIELDS)){
            foreach($LISTVIEW_FIELDS as $key=>$val){
                if(isset($listViewHeaders[$key])){
                    $temp[$key]=$listViewHeaders[$key];
                }
            }
        }
        if(empty($temp)){
            $temp = $listViewHeaders;
        }

        $viewer->assign('LISTVIEW_HEADERS', $temp);
        //$viewer->assign('LISTVIEW_FIELDS', $listViewModel->getSelectFields());
        $viewer->assign('LISTVIEW_ENTRIES', $this->listViewEntries);
        //end



        $pageLimit = $pagingModel->getPageLimit();
        $pageCount = ceil((int) $noOfEntries / (int) $pageLimit);

        if($pageCount == 0){
            $pageCount = 1;
        }
        $viewer->assign('PAGE_COUNT', $pageCount);
        $viewer->assign('PAGE_CU', $pageNumber);
        $viewer->assign('LISTVIEW_COUNT', $noOfEntries);
        $viewer->assign('IS_MODULE_EDITABLE', $listViewModel->getModule()->isPermitted('EditView',1));
        $viewer->assign('IS_MODULE_DELETABLE', $listViewModel->getModule()->isPermitted('Delete'));
        $viewer->assign('IS_MODULE_LISTBTNEDIT', $listViewModel->getModule()->isPermitted('ListBtnEDIT'));

    }
}