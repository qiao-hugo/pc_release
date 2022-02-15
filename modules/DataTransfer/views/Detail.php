<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class DataTransfer_Detail_View extends Vtiger_Detail_View {
	protected $record = false;
	function __construct() {
		parent::__construct();
		$this->exposeMethod('showDetailViewByMode');

	}

	function checkPermission(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$recordId = $request->get('record');
		//权限判断，在通过详细页面点过来的地方要使用，
		/* global $isallow;
		$referer_module=$request->get('refer_module');//来路
		$referer_id=$request->get('referer_id');
		if(!empty($referer_module)){
			//如果有必要做一次传入referer的数据验证
			if(!empty($recordId)){//新增
				if(in_array($moduleName,$isallow)){
					$module=SalesorderWorkflowStages_Record_Model::getInstanceById(0);
					$result=$module->getPermission($moduleName, $recordId,$referer_id);//@TODO 需要判断上下级关系，即上级也可以看到这个页面但是不能审核
					if($result){
						return true;
					}
				}
			}
		} */
		//1.编辑权限，有上下级关系的，或者本人，或者有审核权限的人
		/* if(!empty($recordId)){
			if(isset($_SESSION['isyourcode'])&&$_SESSION['isyourcode']==$moduleName.$recordId){
				//偶审核权限的人，通过isyourcode值来判断
			}else{
				$user=getAccessibleUsers($moduleName,'Edit',true);
					
				$recordModule=Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
				$recordField=$recordModule->getData();
				if(isset($recordField['assigned_user_id'])){
					$id=$recordField['assigned_user_id'];
				}elseif(isset($recordField['smcreatorid'])){
					$id=$recordField['smcreatorid'];
				}
				if(is_array($user)&& !in_array($id,$user)){
					throw new AppException(vtranslate('没有访问权限'));
				}
			}
				
		} */
		//end
		$recordPermission = Users_Privileges_Model::isPermitted($moduleName, 'DetailView', $recordId);
		if(!$recordPermission) {
			throw new AppException('LBL_PERMISSION_DENIED');
		}
		return true;
	}

    function process(Vtiger_Request $request) {


        $mode = $request->getMode();
        //根据关联参数执行
        if(!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
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
        $viewer->assign('ACCOUNTINFOS', Accounts_Record_Model::getAccountInfoByDataRecordId($recordId));
        echo $viewer->view('DetailViewSummaryContents.tpl', $moduleName, true);
    }

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
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));

        return $viewer->view('DetailViewFullContents.tpl',$moduleName,true);
    }
}
