<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class OvertAccounts_Detail_View extends Vtiger_Detail_View {

	/**
	 * Function to get activities
	 * @param Vtiger_Request $request
	 * @return <List of activity models>
	 */
	public function getActivities(Vtiger_Request $request) {
        /*
		$moduleName = 'Calendar';
		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
		
		$currentUserPriviligesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		if($currentUserPriviligesModel->hasModulePermission($moduleModel->getId())) {
			$moduleName = $request->getModule();
			$recordId = $request->get('record');

			$pageNumber = $request->get('page');
			if(empty ($pageNumber)) {
				$pageNumber = 1;
			}
			$pagingModel = new Vtiger_Paging_Model();
			$pagingModel->set('page', $pageNumber);
			$pagingModel->set('limit', 10);

			if(!$this->record) {
				$this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
			}
			$recordModel = $this->record->getRecord();
			$moduleModel = $recordModel->getModule();

			$relatedActivities = $moduleModel->getCalendarActivities('', $pagingModel, 'all', $recordId);

			$viewer = $this->getViewer($request);
			$viewer->assign('RECORD', $recordModel);
			$viewer->assign('MODULE_NAME', $moduleName);
			$viewer->assign('PAGING_MODEL', $pagingModel);
			$viewer->assign('PAGE_NUMBER', $pageNumber);
			$viewer->assign('ACTIVITIES', $relatedActivities);
            */
            $parentRecordId = $request->get('record');
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

			$recentActivities = OvertAccounts_Record_Model::getservicecomments($parentRecordId, $pagingModel);
            $recentActivitiesandsmowner = OvertAccounts_Record_Model::getservicecommentsandsmower($parentRecordId, $pagingModel);
			$RECENT_HEADS = OvertAccounts_Record_Model::getheads($parentRecordId, $pagingModel);
			$pagingModel->calculatePageRange($recentActivities);
			$viewer = $this->getViewer($request);
            /////将客户表的联系人拿出来
			$recordModel = Vtiger_Record_Model::getInstanceById($parentRecordId, 'OvertAccounts');
			$moduleModel = $recordModel->getModule();
			$entity=$recordModel->entity->column_fields;

			///////读客户对应联系人表里的联系人信息
			$allcontacts = OvertAccounts_Record_Model::getContactsToIndex($parentRecordId);
			$viewer->assign('ENTITY_FIRST',$entity);
			$viewer->assign('ALLCONTACTS',$allcontacts);
			$viewer->assign('RECENT_ACTIVITIES', $recentActivities);
            $viewer->assign('RECENT_ACTIVITIESAND', $recentActivitiesandsmowner);
			$viewer->assign('RECENT_HEADS', $RECENT_HEADS);
			$viewer->assign('MODULE_NAME', $moduleName);
			$viewer->assign('PAGING_MODEL', $pagingModel);
			return $viewer->view('RelatedActivities.tpl', $moduleName, true);
	
	}
	/**
	 * 2014-12-23 更新人：steel
	 * 更新内容在原有的其础上添加客户消费级别：关联工单数据表如工单数据表中有工单按1-10单为铁牌客户,11-20为铜牌客户,21-30为银牌客户,30单以上为金银客户
	 * @see Vtiger_Detail_View::showModuleDetailView()
	 */
	function showModuleDetailView(Vtiger_Request $request) {
			$recordId = $request->get('record');
			$moduleName = $request->getModule();
			//start
			//OvertAccounts_Module_Model::accountLevel($recordId);
			//end
			return parent::showModuleDetailView($request);
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
        $viewer->assign('NAME_FLAG', OvertAccounts_Record_Model::getsupperaccountupdate('accountname'));
        $viewer->assign('RANK_FLAG', OvertAccounts_Record_Model::getsupperaccountupdate('accountrank'));
    
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        $viewer->assign('SUMMARY_RECORD_STRUCTURE', $recordStrucure->getStructure());
        $viewer->assign('RELATED_ACTIVITIES', $this->getActivities($request));
        $viewer->assign('RANKLIMIT', $recordModel->getRankLimit());
        return $viewer->view('ModuleSummaryView.tpl', $moduleName, true);
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
        //$accountRecordModel=Vtiger_Record_Model::getCleanInstance($moduleName, $parentId);
        //$recentComments = $accountRecordModel->getRecentComments($parentId, $pagingModel,$moduleName);
        $recentComments = ModComments_Record_Model::getRecentComments($parentId, $pagingModel,$moduleName);
        $pagingModel->calculatePageRange($recentComments);
        $currentUserModel = Users_Record_Model::getCurrentUserModel();

        //获取客户id
        $accountid="";
        if ($moduleName !="OvertAccounts"){
            if(!$this->record){
                $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $parentId);
            }
            $recordModel = $this->record->getRecord();
            $accountid=$recordModel->get('related_to');
        }else{
            $accountid=$parentId;
        }
        require_once 'crmcache/role.php';

        //wangbin 2015-9-7 客服回访的任务包
        global $current_user;
        global $adb;
        $userid = $current_user->id;
         //$current_user->is_admin;
        $if_updateTask1 = false;
        $task_name = "";
        $sel_serviceidsql = "SELECT serviceid,servicecommentsid FROM `vtiger_servicecomments` WHERE assigntype = 'accountby' AND related_to = ? limit 1";
        $serviceid_result = $adb->pquery($sel_serviceidsql,array($accountid));
        $serviceid = $adb->fetchByAssoc($serviceid_result, 0);//当前客户分配的客服;
        if($userid == $serviceid['serviceid']){//如果当前登录用户是该客户的客服，就去查找任务包
            $if_updateTask1 = true;
            $qiantai_taskArr = AutoTask_BasicAjax_Action::service_follow($accountid);
            $task_name = $qiantai_taskArr['autoworkflowtaskname'];

            $taskid = $qiantai_taskArr['autoworkflowtaskid'];
            $remarkArray = $adb->pquery("SELECT * FROM `vtiger_autoworkflowtasks` WHERE autoworkflowtaskid = ?",array($taskid));
			$remarkname = $adb->fetch_array($remarkArray,0);
            $remarkname = $remarkname['remark'];
        }
        //end

        $double_type =  ServiceContracts_Record_Model::search_double($accountid);  //双推产品的类型
        $viewer = $this->getViewer($request);
        //wangbin 判断当前客户购买的产品类型
        if($userid == $serviceid['serviceid']){
            $viewer->assign('double_type',$double_type);
            $viewer->assign('servicecomment',$serviceid['servicecommentsid']);//判断客服跟进把最近的跟进记录添加到客服分配表中去
        }
        $viewer->assign('COMMENTSCOUNTS', OvertAccounts_Record_Model::getModcommentCount($accountid));
        $viewer->assign('COMMENTS', $recentComments);
        $viewer->assign('ROLE', $roles);
        $viewer->assign('ACCOUNTID', $accountid);
        $viewer->assign('COMMENTSMODE', ModComments_Record_Model::getModcommentmode());
        $viewer->assign('COMMENTSTYPE',ModComments_Record_Model::getModcommenttype());
        $viewer->assign('MODCOMMENTCONTACTS',ModComments_Record_Model::getModcommentContacts($accountid));
        $viewer->assign('CURRENTUSER', $currentUserModel);
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('PAGING_MODEL', $pagingModel);
        if($if_updateTask1){
            $viewer->assign('TASKNAME',$task_name);
            $viewer->assign('REMARK',$remarkname);
        }
        return $viewer->view('RecentComments.tpl', $moduleName, 'true');
    }
}
