<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class SalesDaily_Detail_View extends Vtiger_Detail_View {

	function preProcess(Vtiger_Request $request, $display=true) {
		parent::preProcess($request, false);
		$recordId = $request->get('record');
		$moduleName = $request->getModule();
		if(!$this->record){
			$this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
		}

		$recordModel = $this->record->getRecord();
		$recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
		$summaryInfo = array();

		// Take first block information as summary information
		$stucturedValues = $recordStrucure->getStructure();
		foreach($stucturedValues as $blockLabel=>$fieldList) {
			$summaryInfo[$blockLabel] = $fieldList;
			break;
		}

		$detailViewLinkParams = array('MODULE'=>$moduleName,'RECORD'=>$recordId);

		$detailViewLinks = $this->record->getDetailViewLinks($detailViewLinkParams);
		$navigationInfo = null;//ListViewSession::getListViewNavigation($recordId);  // 20150611 young 这里造成详细页面错误，暂时为null

		$viewer = $this->getViewer($request);
		$viewer->assign('RECORD', $recordModel);
	//	$viewer->assign('NAVIGATION', $navigationInfo);
		//Intially make the prev and next records as null
		$prevRecordId = null;
		$nextRecordId = null;
		$found = false;
		if ($navigationInfo) {
			foreach($navigationInfo as $page=>$pageInfo) {
				foreach($pageInfo as $index=>$record) {
					//If record found then next record in the interation
					//will be next record
					if($found) {
						$nextRecordId = $record;
						break;
					}
					if($record == $recordId) {
						$found = true;
					}
					//If record not found then we are assiging previousRecordId
					//assuming next record will get matched
					if(!$found) {
						$prevRecordId = $record;
					}
				}
				//if record is found and next record is not calculated we need to perform iteration
				if($found && !empty($nextRecordId)) {
					break;
				}
			}
		}

		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
		if(!empty($prevRecordId)) {
			$viewer->assign('PREVIOUS_RECORD_URL', $moduleModel->getDetailViewUrl($prevRecordId));
		}
		if(!empty($nextRecordId)) {
			$viewer->assign('NEXT_RECORD_URL', $moduleModel->getDetailViewUrl($nextRecordId));
		}

        $reportsModel = Users_Privileges_Model::getInstanceById($this->record->getRecord()->entity->column_fields['smownerid']);
        $viewer->assign("REPORTID",$reportsModel->reports_to_id);
        $viewer->assign("CURRENTMONTHSA",$recordModel->getCurrentMonth($this->record->getRecord()->entity->column_fields));
        $viewer->assign("DETAILLIST",$recordModel->getDetailList($recordId));
		$viewer->assign('MODULE_MODEL', $this->record->getModule());
		$viewer->assign('DETAILVIEW_LINKS', $detailViewLinks);

		$viewer->assign('IS_EDITABLE', $this->record->getRecord()->isEditable($moduleName));
		$viewer->assign('IS_DELETABLE', $this->record->getRecord()->isDeletable($moduleName));

		//客户统计
        $viewer->assign("ACCOUNTSTATICTICS",SalesDaily_Record_Model::getAccountStatisticsByRecord($recordId));

		$linkParams = array('MODULE'=>$moduleName, 'ACTION'=>$request->get('view'));
		$linkModels = $this->record->getSideBarLinks($linkParams);
		$viewer->assign('QUICK_LINKS', $linkModels);

		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$viewer->assign('DEFAULT_RECORD_VIEW', $currentUserModel->get('default_record_view'));
		
		if($display) {
			$this->preProcessDisplay($request);
		}

	}


	
	
	function process(Vtiger_Request $request) {
		$mode = $request->getMode();
		//根据关联参数执行
		if(!empty($mode)) {
			echo $this->invokeExposedMethod($mode, $request);
			return;
		}
        if($request->get('mode')=='showRecentComments'){
            echo $this->showRecentComments($request);
            return;
        }
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		//非关联信息显示
		if ($currentUserModel->get('default_record_view') === 'Summary') {
			echo $this->showModuleBasicView($request);
		} else {
			echo $this->showModuleDetailView($request);
		}	
	}





	function showDetailViewByMode($request) {
		$requestMode = $request->get('requestMode');
		if($requestMode == 'full') {
			return $this->showModuleDetailView($request);
		}
		return $this->showModuleBasicView($request);
	}

	/**
	 * Function shows the entire detail for the record
	 * @param Vtiger_Request $request
	 * @return <type>
     * 显示详细信息，两个地方都会显示
	 */
	function showModuleDetailView(Vtiger_Request $request) {
		
		

		$recordId = $request->get('record');
		$moduleName = $request->getModule();
		//young.yang 2014-12-26 工作流
		global $isallow;
		if(in_array($moduleName, $isallow)){
			echo $this->getWorkflowsM($request);
		}
		//end	
		if(!$this->record){
		$this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
		}
		$recordModel = $this->record->getRecord();
		$recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
		//var_dump($recordStrucure);die;
		$structuredValues = $recordStrucure->getStructure();
		
		
        $moduleModel = $recordModel->getModule();
		
		$viewer = $this->getViewer($request);

        $reportsModel = Users_Privileges_Model::getInstanceById($this->record->getRecord()->entity->column_fields['smownerid']);

        $viewer->assign("REPORTID",$reportsModel->reports_to_id);
        $viewer->assign("CURRENTMONTHSA",$recordModel->getCurrentMonth($this->record->getRecord()->entity->column_fields));
        $viewer->assign("DETAILLIST",$recordModel->getDetailList($recordId));
		$viewer->assign('RECORD', $recordModel);
		$viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$viewer->assign('MODULE_NAME', $moduleName);
		$viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        $viewer->assign("ACCOUNTSTATICTICS",SalesDaily_Record_Model::getAccountStatisticsByRecord($recordId));

        return $viewer->view('DetailViewFullContents.tpl',$moduleName,true);
	}
	
	function showModuleSummaryView($request) {
		$recordId = $request->get('record');
		$moduleName = $request->getModule();

		if(!$this->record){
			$this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
		}
		$recordModel = $this->record->getRecord();
		$recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_SUMMARY);

        $moduleModel = $recordModel->getModule();
		$viewer = $this->getViewer($request);
		$viewer->assign('RECORD', $recordModel);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());

		$viewer->assign('MODULE_NAME', $moduleName);
		$viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
		$viewer->assign('SUMMARY_RECORD_STRUCTURE', $recordStrucure->getStructure());
		$viewer->assign('RELATED_ACTIVITIES', $this->getActivities($request));

		return $viewer->view('ModuleSummaryView.tpl', $moduleName, true);
	}

	/**
	 * Function shows basic detail for the record
	 * @param <type> $request
	 */
	function showModuleBasicView($request) {

		$recordId = $request->get('record');
		$moduleName = $request->getModule();
		
		if(!$this->record){
			$this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
		}
		$recordModel = $this->record->getRecord();
		
		$detailViewLinkParams = array('MODULE'=>$moduleName,'RECORD'=>$recordId);
		$detailViewLinks = $this->record->getDetailViewLinks($detailViewLinkParams);

		$viewer = $this->getViewer($request);
		$viewer->assign('RECORD', $recordModel);
		$viewer->assign('MODULE_SUMMARY', $this->showModuleSummaryView($request));

		$viewer->assign('DETAILVIEW_LINKS', $detailViewLinks);
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
		$viewer->assign('MODULE_NAME', $moduleName);

		$recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
		$structuredValues = $recordStrucure->getStructure();

        $moduleModel = $recordModel->getModule();
        
		$viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
		echo $viewer->view('DetailViewSummaryContents.tpl', $moduleName, true);
	}

    /**
     * 跟进信息
     */
    function showRecentComments(Vtiger_Request $request) {
        $parentId = $request->get('record');
        $pageNumber =(int)$request->get('page');
        $limit = $request->get('limit');
        $moduleName = $request->getModule();
        if(empty($pageNumber)){
            $pageNumber = 1;
        }

        $pagingModel = new Vtiger_Paging_Model();
        $pagingModel->set('page', $pageNumber);
        if(!empty($limit)) {
            $pagingModel->set('limit', $limit);
        }

        $recentComments = ModComments_Record_Model::getRecentComments($parentId, $pagingModel,$moduleName);

        $pagingModel->calculatePageRange($recentComments);
        $currentUserModel = Users_Record_Model::getCurrentUserModel();

        //获取客户id
        $accountid="";
        if ($moduleName !="Accounts"){
            if(!$this->record){
                $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $parentId);
            }
            $recordModel = $this->record->getRecord();
            $accountid=$recordModel->get('related_to');
        }else{
            $accountid=$parentId;
        }

        $viewer = $this->getViewer($request);
        $viewer->assign('COMMENTS', $recentComments);
        $viewer->assign('ACCOUNTID', $accountid);
        $viewer->assign('COMMENTSMODE', ModComments_Record_Model::getModcommentmode());
        $viewer->assign('COMMENTSTYPE',ModComments_Record_Model::getModcommenttype());
        $viewer->assign('MODCOMMENTCONTACTS',ModComments_Record_Model::getModcommentContacts($accountid));
        $viewer->assign('CURRENTUSER', $currentUserModel);
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('PAGING_MODEL', $pagingModel);
        $viewer->assign("ISSHOWREPLYFRAME",false);
        global $current_user;
        $allSup = getAllSuperiorIds($recordModel->get("smownerid"));
        if(($key = array_search($current_user->id,$allSup))){
            unset($allSup[$key]);
        }
        if($current_user->is_admin=='on' || in_array($current_user->id,$allSup)){
            $viewer->assign("ISSHOWREPLYFRAME",true);
        }

        //看了属于自己的批复 改变状态
        global $adb;
        $sql = "UPDATE vtiger_modcomments,vtiger_salesdaily_basic set islook=1 where vtiger_modcomments.moduleid=vtiger_salesdaily_basic.salesdailybasicid and 
	vtiger_modcomments.moduleid=? and vtiger_modcomments.modulename='SalesDaily' and  vtiger_salesdaily_basic.smownerid=?";
        $adb->pquery($sql, array($parentId, $current_user->id));

        return $viewer->view('RecentComments.tpl', $moduleName, 'true');
    }

}
