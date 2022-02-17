<?php
/*+***************
 * 详情页面的关联列表显示
 *
 *
 **********/

class CustomerStatement_RelatedList_View extends Vtiger_RelatedList_View {
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




		if($relatedModuleName == 'Files') {
			// 判断该用户是否有权限 附件签收权限
	        global $current_user;
	        $adb = PearDatabase::getInstance();
	        /*$sql = "select * FROM vtiger_custompowers where custompowerstype='filesdelivert' LIMIT 1";
	        $sel_result = $adb->pquery($sql, array());
	        $res_cnt = $adb->num_rows($sel_result);
	        if($res_cnt > 0) {
	        	$rawData = $adb->query_result_rowdata($sel_result, 0);
	        	$roles_arr = explode(',', $rawData['roles']);
	            $user_arr = explode(',', $rawData['user']);
	            if (in_array($current_user->current_user_roles, $roles_arr) || in_array($current_user->id, $user_arr)) {
                    $viewer->assign('IS_FILESDELIVERT', '1');
                }
	        }*/
	        // is_custompowers 是一个自定义权限的方法
	        if(is_custompowers('filesdelivert')) {
	        	$viewer->assign('IS_FILESDELIVERT', '1');
	        }

	        $viewer->assign('IS_FILES', '1');  //是否可以签收

	        $sql = "SELECT CONCAT( last_name, '[', IFNULL( ( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ) ) ) AS last_name FROM vtiger_users WHERE vtiger_users.id=?";
			$sel_result = $adb->pquery($sql, array($current_user->id));
	        $res_cnt = $adb->num_rows($sel_result);
	        if($res_cnt > 0) {
	        	$rawData = $adb->query_result_rowdata($sel_result, 0);
	        	$viewer->assign('LAST_NAME', $rawData['last_name']);  //是否可以签收
	        }
	        global $current_user;
            $viewer->assign('IS_ROLEID', !in_array($current_user->roleid,array('H101','H79','H80','H81','H82')));
            $modulestatus = $parentRecordModel->get('modulestatus');
            $viewer->assign('MODULESTATUS',$modulestatus);
	        // 当前用户是否是 财务总监
	        if(is_custompowers('contractsFilesDelete')) {
	        	$viewer->assign('IS_CONTRACTSFILESDELETE', '1');
	        }
		}
		return $viewer->view('RelatedList.tpl', $moduleName,true);
	}
}
