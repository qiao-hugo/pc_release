<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class TelStatistics_Detail_View extends Vtiger_Detail_View {
    public function __construct(){
        parent::__construct();
        $this->exposeMethod('getSoundAComments');
    }
function preProcess(Vtiger_Request $request) {
		$viewer = $this->getViewer($request);
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
	
		//获取跟进信息  gaocl add
		global $adb;
		$followSql="select * from vtiger_modcomments where modulename='VisitingOrder' and moduleid=? order by modcommentsid desc";
		$result = $adb->pquery($followSql, array($recordId));
		if ($result && $adb->num_rows($result) > 0) {
		    $followdata = array();
		    $resultrow = $adb->query_result_rowdata($result);
		    $followdata['followstatus'] = '已跟进';
		    $followdata['followtime'] = $resultrow['addtime'];
			/* $recordModel->set('followstatus','followup');
			$recordModel->set('followid',$resultrow['creatorid']);
			$recordModel->set('followtime',$resultrow['addtime']); */
		}

		echo $this->getWorkflowsM($request,$recordModel);
		
		$recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
		$structuredValues = $recordStrucure->getStructure();
		

		// 获取拜访单的签到信息
		$sql = "SELECT
				u.last_name,
				v.signtime,
				v.signaddress,
				v.visitsigntype,
				v.signnum,
				v.userid,
				IF(v.issign=1, '是', '否')  AS issign,
				IF(v.signnum=1, '一', '二')  AS signnum

			FROM
				vtiger_visitsign v
			LEFT JOIN vtiger_users u ON v.userid = u.id
			WHERE
				v.visitingorderid = ?
			";
		$visitsingArr = array();
		$t_result = $adb->pquery($sql, array($recordId));
		while($rawData = $adb->fetch_array($t_result)) {
			$visitsingArr[] = $rawData;
		}

		$t_data = array();
		foreach ($visitsingArr as $key=>$value) {
			$t_data[$value['userid']]['last_name'] = $value['last_name'];
			$t_data[$value['userid']]['visitsigntype'] = $value['visitsigntype'];
			$t_data[$value['userid']]['data'][] = $value;
		}


		$moduleModel = $recordModel->getModule();
		$viewer = $this->getViewer($request);
		$viewer->assign('followdata', $followdata);
		$viewer->assign('RECORD', $recordModel);
		$viewer->assign('RECORD_STRUCTURE', $structuredValues);
		$viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$viewer->assign('MODULE_NAME', $moduleName);
		$viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
		
		$viewer->assign('VISITSINGS', $t_data);
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
        return $viewer->view('LineItemsWorkflowsM.tpl', 'VisitingOrder',true);
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
		//2015年02月27日  添加跟进后跟新客户等级
		if(!empty($recentComments)){
		    $db = PearDatabase::getInstance();
		    $db->pquery("UPDATE `vtiger_account` SET accountrank = ( IF ( accountrank = 'chan_notv', 'forp_notv', accountrank )) WHERE accountid = ?",array($accountid));
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
		$viewer->assign('CURRENT_COMMENT', $currentCommentModel);
		$viewer->assign('COMMENTSMODE', array('拜访'));
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

            return $viewer->view('LineItemsComment.tpl', 'VisitingOrder', 'true');
        }
    }
}
	