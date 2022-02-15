<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Achievementallot_List_View extends Vtiger_KList_View {
		/*function __construct() {
		parent::__construct();
	}
		function preProcess(Vtiger_Request $request, $display=true) {
		parent::preProcess($request, false);
        $viewer = $this->getViewer ($request);
        //wangbin 关联回款的回款分成模块强制调用回款模块
        $moduleName = "Achievementallot";
        $this->viewName = $request->get('viewname');
        $viewer->assign('VIEWNAME', $this->viewName);

        $listViewModel = Vtiger_ListView_Model::getInstance($moduleName);
        $linkParams = array('MODULE'=>$moduleName, 'ACTION'=>$request->get('view'));// module 和action

        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        $recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($moduleModel);
        $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
        $viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());
        $viewer->assign('SEARCHRECORD_STRUCTURE', $moduleModel->getSearchFields());
        $viewer->assign('MODULE_MODEL',$moduleModel);
        $viewer->assign('SOURCE_MODULE',$moduleName);

        $quickLinkModels = $listViewModel->getSideBarLinks($linkParams);
        $viewer->assign('QUICK_LINKS', $quickLinkModels);

         if(empty($this->viewName)){
            //If not view name exits then get it from custom view
            //This can return default view id or view id present in session
            $customView = new CustomView();
            $this->viewName = $customView->getViewId($moduleName);
        }
        $this->initializeListViewContents($request, $viewer);//竟然调用两
        $viewer->assign('VIEWID', $this->viewName);

        if($display) {
            $this->preProcessDisplay($request);
        }
    }*/
}