<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ProductProvider_Detail_View extends Vtiger_Detail_View {
    public function __construct(){
        parent::__construct();
        $this->exposeMethod('getSoundAComments');
    }
function preProcess(Vtiger_Request $request) {
		$viewer = $this->getViewer($request);
        $record = $request->get('record');
        $recordModel=Vtiger_Record_Model::getCleanInstance("ProductProvider");
        $DETAIL_INFO_LIST=$recordModel->getProductProvideDetail($record);
        $viewer->assign('DETAIL_INFO_LIST',$DETAIL_INFO_LIST);
		$viewer->assign('NO_SUMMARY', true);
		parent::preProcess($request);
	}
	/**
	 * Function returns Inventory details
	 * @param Vtiger_Request $request
	 */
	function showModuleDetailView(Vtiger_Request $request) {
		//echo parent::showModuleDetailView($request);
		//echo $this->getWorkflowsM($request);
		global $current_user;
		$recordId = $request->get('record');
		$moduleName = $request->getModule();
	
		if(!$this->record){
			$this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
		}


		$recordModel = $this->record->getRecord();

		echo $this->getWorkflowsM($request,$recordModel);
		
		$recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
		$structuredValues = $recordStrucure->getStructure();
		


		$moduleModel = $recordModel->getModule();
		$viewer = $this->getViewer($request);
		$viewer->assign('RECORD', $recordModel);
		$viewer->assign('RECORD_STRUCTURE', $structuredValues);
		$viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$viewer->assign('MODULE_NAME', $moduleName);
		$viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
		
		return $viewer->view('DetailViewFullContents.tpl',$moduleName,true);
	}
	
	/**
	 * Function returns Inventory details
	 * @param Vtiger_Request $request
	 * @return type
	 */
	function showDetailViewByMode(Vtiger_Request $request) {
		return $this->showModuleDetailView($request);
	}
	
	function showModuleBasicView($request) {
		return $this->showModuleDetailView($request);
	}
    function getWorkflowsM(Vtiger_Request $request){
        $moduleName = $request->getModule();
        $viewer = $this->getViewer($request);
        $viewer->assign('ModuleName',$moduleName); //工作流stagesid
        return $viewer->view('LineItemsWorkflowsM.tpl', 'AccountPlatform',true);
    }
	/**
	 * Function returns latest comments
	 * @param Vtiger_Request $request
	 * @return <type>
	 */
	function showRecentComments(Vtiger_Request $request) {
		$parentId = $request->get('record');

		$pageNumber = $request->get('page');
		$limit = $request->get('limit');
		$moduleName = $request->getModule();
	
		if(empty($pageNumber) || $pageNumber=='undefined') {
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
		
		return $viewer->view('RecentComments.tpl', $moduleName, 'true');
	}
	
	/**
	 * Function sends all the comments for a parent(Accounts, Contacts etc)
	 * @param Vtiger_Request $request
	 * @return <type>
	 */
	function showAllComments(Vtiger_Request $request) {
		$parentRecordId = $request->get('record');
		$commentRecordId = $request->get('commentid');
		$moduleName = $request->getModule();
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
	
		$parentCommentModels = ModComments_Record_Model::getAllParentComments($parentRecordId);
	
		if(!empty($commentRecordId)) {
			//$currentCommentModel = ModComments_Record_Model::getInstanceById($commentRecordId);
		}
	
		//获取客户id
		$accountid="";
		if ($moduleName !="Accounts"){
			if(!$this->record){
				$this->record = Vtiger_DetailView_Model::getInstance($moduleName, $parentRecordId);
			}
			$recordModel = $this->record->getRecord();
			$accountid=$recordModel->get('related_to');
		}else{
			$accountid=$parentRecordId;
		}
	
		$viewer = $this->getViewer($request);
		$viewer->assign('CURRENTUSER', $currentUserModel);
		$viewer->assign('ACCOUNTID', $accountid);
		$viewer->assign('PARENT_COMMENTS', $parentCommentModels);
		$viewer->assign('COMMENTSTYPE',ModComments_Record_Model::getModcommenttype());
		$viewer->assign('MODCOMMENTCONTACTS',ModComments_Record_Model::getModcommentContacts($accountid));
	
		return $viewer->view('ShowAllComments.tpl', $moduleName, 'true');
	}
	
	public function  getSoundAComments(Vtiger_Request $request){

        $recordId=$request->get('record');
        if(!$this->record){
            $this->record = Vtiger_DetailView_Model::getInstance('VisitingOrder', $recordId);
        }
        $extractid=$this->record->getRecord()->entity->column_fields['extractid'];
        $user=Users_Privileges_Model::getInstanceById($extractid);
        $where=getAccessibleUsers('VisitingOrder','List',true);
        global $current_user;
        if($where=='1=1' || in_array($user->reports_to_id,$where) || $current_user->is_admin=='on') {
            $result=$this->record->getRecord()->getSoundAndComments($request);
            $viewer = $this->getViewer($request);
            $viewer->assign('CURRENTCOMMENT', $result);

            return $viewer->view('LineItemsComment.tpl', 'AccountPlatform', 'true');
        }
    }
}
	