<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Vendors_Detail_View extends Vtiger_Detail_View {
	
	public function __construct(){
		parent::__construct();
	}

	function checkPermission(Vtiger_Request $request) {
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


		// 获取充值平台数据
		$viewer = $this->getViewer($request);
		$viewer->assign('RECHARGEPLATFORM_DATA', Vendors_Record_Model::getRechargeplatform());		

		//非关联信息显示
		if ($currentUserModel->get('default_record_view') === 'Summary') {
			echo $this->showModuleBasicView($request);
		} else {
			echo $this->showModuleDetailView($request);
		}	
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
		/*if(in_array($moduleName, $isallow)){
			echo $this->getWorkflowsM($request);
		}*/
		//end	
		if(!$this->record){
		$this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
		}
		$recordModel = $this->record->getRecord();
		$recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
		//var_dump($recordStrucure);die;
		$structuredValues = $recordStrucure->getStructure();
		
		
        $moduleModel = $recordModel->getModule();

        if($recordModel->entity->column_fields['workflowsid'] > 0) {
        	echo $this->getWorkflowsM($request,$recordModel);
        }
        // 主要产品转换
        $vendortype = $recordModel->entity->column_fields['vendortype'];
        $viewer = $this->getViewer($request);
        if ($vendortype == 'medium') {
        	$mainplatform = $recordModel->entity->column_fields['mainplatform'];
        	$tt = explode(' # ', $mainplatform);
        	$mainplatform_value = array();
        	$rechargeplatformData = Vendors_Record_Model::getRechargeplatform();
        	foreach ($tt as $key => $v) {
        		$mainplatform_value[] = $rechargeplatformData[$v];
        	}
        	$viewer->assign('IS_VENDORTYPE', 1);
        }

		$viewer->assign('RECORD', $recordModel);
		$viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$viewer->assign('MODULE_NAME', $moduleName);
		$viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        $viewer->assign('ACCESSIBLE_USERS',get_username_array());//人员
        global $current_user;
        $viewer->assign('FIELD_VALUE',$current_user->id);//人员

        $viewer->assign('ISVERIFY',0);
        if($recordModel->entity->column_fields['workflowsid']){
            $viewer->assign('ISVERIFY',$recordModel->isWorkFlowVerifying($recordModel->entity->column_fields['workflowsid'],$recordId));//人员
        }

        return $viewer->view('DetailViewFullContents.tpl',$moduleName,true);
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
		$viewer = $this->getViewer($request);
		$vendortype = $recordModel->entity->column_fields['vendortype'];
        if ($vendortype == 'medium') {
        	$mainplatform = $recordModel->entity->column_fields['mainplatform'];
        	$tt = explode(' # ', $mainplatform);
        	$mainplatform_value = array();
        	$rechargeplatformData = Vendors_Record_Model::getRechargeplatform();
        	foreach ($tt as $key => $v) {
        		$mainplatform_value[] = $rechargeplatformData[$v];
        	}
        	$viewer->assign('IS_VENDORTYPE', 1);
        	$viewer->assign('MAINPLATFORM_DATA', implode('，', $mainplatform_value));
        }

        if($recordModel->entity->column_fields['workflowsid'] > 0) {
        	echo $this->getWorkflowsM($request,$recordModel);
        }
        
        
		$detailViewLinkParams = array('MODULE'=>$moduleName,'RECORD'=>$recordId);
		$detailViewLinks = $this->record->getDetailViewLinks($detailViewLinkParams);

		
		$viewer->assign('RECORD', $recordModel);
		$viewer->assign('MODULE_SUMMARY', $this->showModuleSummaryView($request));

		$viewer->assign('DETAILVIEW_LINKS', $detailViewLinks);
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
		$viewer->assign('MODULE_NAME', $moduleName);

		$recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
		$structuredValues = $recordStrucure->getStructure();

        $moduleModel = $recordModel->getModule();
    		
    	// 获取产品返点数据
    	$vendorsrebateData = Vendors_Record_Model::getVendorsrebate($recordId);
    	$viewer->assign('VENDORSREBATEDATA', $vendorsrebateData);
        $viewer->assign('ACCESSIBLE_USERS',get_username_array());//人员
        global $current_user;
        $viewer->assign('FIELD_VALUE',$current_user->id);//人员

		$viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());

        $viewer->assign('ISVERIFY',0);
        if($recordModel->entity->column_fields['workflowsid']){
            $viewer->assign('ISVERIFY',$recordModel->isWorkFlowVerifying($recordModel->entity->column_fields['workflowsid'],$recordId));//人员
        }

		echo $viewer->view('DetailViewSummaryContents.tpl', $moduleName, true);
	}

}
