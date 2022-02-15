<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Accounts_ListAjax_View extends Vtiger_ListAjax_View {

	function __construct() {
		parent::__construct();
        $this->exposeMethod('showRecentComments');
        $this->exposeMethod('showRelatedList');
        $this->exposeMethod('getActivities');
	}

	function preProcess(Vtiger_Request $request) {
		return true;
	}

	function postProcess(Vtiger_Request $request) {
		return true;
	}

	function process(Vtiger_Request $request) {
		$mode = $request->get('mode');
		if(!empty($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}
	}
    public function showRecentComments(Vtiger_Request $request){
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
        if ($moduleName !="Accounts"){
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
        $viewer->assign('COMMENTSCOUNTS', Accounts_Record_Model::getModcommentCount($accountid));
//        print_r($recentComments);exit;
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
        echo $viewer->view('RecentCommentslink.tpl', $moduleName, 'true');
    }
    /**
     * 加载关联模块下RelatedList
     * 关联记录 修改优先级
     * @param Vtiger_Request $request
     * @return <type>
     */
    public function showRelatedList(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $relatedModuleName = $request->get('relatedModule');
        //产品套餐特殊处理
        if($relatedModuleName=='Products' && $moduleName=='Products'){
            $relatedModuleName='ProductBundles';
        }
        $instance=CRMEntity::getInstance($moduleName);
        if(!empty($instance->relatedmodule_list) && in_array($relatedModuleName,$instance->relatedmodule_list) && isPermitted($relatedModuleName,'DetailView')=='yes'){
            $targetControllerClass = null;
            // Added to support related list view from the related module, rather than the base module.
            try {
                $targetControllerClass = Vtiger_Loader::getComponentClassName('View', 'RelatedList', $moduleName);
            }catch(AppException $e) {
                try {
                    // If any module wants to have same view for all the relation, then invoke this.
                    echo $targetControllerClass = Vtiger_Loader::getComponentClassName('View', 'In'.$moduleName.'Relation', $relatedModuleName);
                }catch(AppException $e) {
                    // Default related list
                    $targetControllerClass = Vtiger_Loader::getComponentClassName('View', 'InRelation', $relatedModuleName);
                    //$targetControllerClass = Vtiger_Loader::getComponentClassName('View', 'RelatedList', $moduleName);
                }
            }
            if($targetControllerClass) {
                $targetController = new $targetControllerClass(); //Vtiger_RelatedList_View
                return $targetController->process($request);
            }
        }else{
            die('error related');
        }

    }

    public function getActivities(Vtiger_Request $request) {
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
        $viewer = $this->getViewer($request);
        /////将客户表的联系人拿出来
        $recordModel = Vtiger_Record_Model::getInstanceById($parentRecordId, 'Accounts');
        $entity=$recordModel->entity->column_fields;
        ///////读客户对应联系人表里的联系人信息
        $allcontacts = Accounts_Record_Model::getContactsToIndex($parentRecordId);
        $viewer->assign('ENTITY_FIRST',$entity);
        $viewer->assign('ALLCONTACTS',$allcontacts);
        $viewer->assign('MODULE_NAME', $moduleName);
        echo $viewer->view('RelatedActivitieslink.tpl', $moduleName, true);

    }
}