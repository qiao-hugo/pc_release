<?php
/*+***************
 * 详情页面的关联列表显示
 * 
 * 
 **********/

class Vtiger_RelatedList_View extends Vtiger_Index_View {
	function process(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$relatedModuleName = $request->get('relatedModule');
		$parentId = $request->get('record');
		$label = $request->get('tab_label');
		$requestedPage = $request->get('page');
		if(empty($requestedPage)) {
			$requestedPage = 1;
		}
		$pagingModel = new Vtiger_Paging_Model();
		$pagingModel->set('page',$requestedPage);
		//获取当前记录数据验证权限
		$parentRecordModel = Vtiger_Record_Model::getInstanceById($parentId, $moduleName);
		//$relationModel = $this->getRelationModel();
		//$relatedModuleModel = $relationModel->getRelationModuleModel();
		
		//产品关联
		if($moduleName=='Products' && $relatedModuleName=='ProductBundles'){
			$relatedModuleName='Products';
			$label='Product Bundles';			
		}
		
		$relationListView = Vtiger_RelationListView_Model::getInstance($parentRecordModel,$relatedModuleName,$label);
		
		/*$orderBy = $request->get('orderby');
		$sortOrder = $request->get('sortorder');
		if($sortOrder == 'ASC') {
			$nextSortOrder = 'DESC';
			$sortImage = 'icon-chevron-down';
		} else {
			$nextSortOrder = 'ASC';
			$sortImage = 'icon-chevron-up';
		}
		if(!empty($orderBy)) {
			$relationListView->set('orderby', $orderBy);
			$relationListView->set('sortorder',$sortOrder);
		}*/
		$models = $relationListView->getEntries($pagingModel);
		$links = $relationListView->getLinks();
		$header = $relationListView->getHeaders();
		
		
		
		$noOfEntries = count($models);

		$relationModel = $relationListView->getRelationModel();
		$relatedModuleModel = $relationModel->getRelationModuleModel();
		$relationField = $relationModel->getRelationField();

		$viewer = $this->getViewer($request);
		$viewer->assign('RELATED_RECORDS' , $models);
		$viewer->assign('PARENT_RECORD', $parentRecordModel);
		$viewer->assign('RELATED_LIST_LINKS', $links);
		
		$viewer->assign('RELATED_MODULE', $relatedModuleModel);
		$viewer->assign('RELATED_ENTIRES_COUNT', $noOfEntries);
		$viewer->assign('RELATION_FIELD', $relationField);
		$viewer->assign('RELATION_MODULENAME', $relatedModuleName);
		$relatedmodule_fields=$parentRecordModel->getEntity()->relatedmodule_fields;
		//$viewer->assign('NEWHEADERFIELDS_LIST',$relatedmodule_fields[$relatedModuleName]);
		
		$viewer->assign('RELATED_HEADERS', $relatedmodule_fields[$relatedModuleName]);
		if (PerformancePrefs::getBoolean('LISTVIEW_COMPUTE_PAGE_COUNT', false)) {
			$totalCount = $relationListView->getRelatedEntriesCount();
			$pageLimit = $pagingModel->getPageLimit();
			$pageCount = ceil((int) $totalCount / (int) $pageLimit);

			if($pageCount == 0){
				$pageCount = 1;
			}
			$viewer->assign('PAGE_COUNT', $pageCount);
			$viewer->assign('TOTAL_ENTRIES', $totalCount);
			$viewer->assign('PERFORMANCE', true);
		}

		$viewer->assign('MODULE', $moduleName);
		$viewer->assign('PAGING', $pagingModel);

		

		$viewer->assign('IS_EDITABLE', $relationModel->isEditable());
		$viewer->assign('IS_DELETABLE', $relationModel->isDeletable());
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$viewer->assign('VIEW', $request->get('view'));
		return $viewer->view('RelatedList.tpl', $moduleName,true);
	}
}