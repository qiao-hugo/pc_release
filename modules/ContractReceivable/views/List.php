<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class ContractReceivable_List_View extends Vtiger_KList_View {
    function checkPermission(Vtiger_Request $request)
    {
        return;
    }

    public function process(Vtiger_Request $request)
    {
        $strPublic = $request->get('public');
        if($strPublic=='export'){
            global $site_URL,$current_user;
            header('location:'.$site_URL.'temp/'.'合同应收明细表'.vtranslate($request->get('bussinesstype'),'ContractReceivable').date('Ymd').$current_user->id.'.csv');
            exit;
        }
        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();
        if($strPublic=='statistical'){
            include "crmcache/departmentanduserinfo.php";
            $viewer->assign('DEPARTMENTUSER',$departlevel);
            $viewer->view('ListExpand.tpl', $moduleName);
            return;
        }
        if($strPublic=='EarlyWarningSetting'){
            $recordModel=Vtiger_Record_Model::getCleanInstance($moduleName);
            if($recordModel->personalAuthority('ContractReceivable','EarlyWarningSetting')){
                $viewer->assign('ROWDATA',$recordModel->getEarlyWarningSettingData());
                $viewer->view('EarlyWarningSetting.tpl', $moduleName);
                return;
            }
        }
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
        $this->viewName = $request->get('viewname');
        if (empty($this->viewName)) {
            //If not view name exits then get it from custom view
            //This can return default view id or view id present in session
            $customView = new CustomView();
            $this->viewName = $customView->getViewId($moduleName);
        }
        $this->initializeListViewContents($request, $viewer);//竟然调用两次，
        $viewer->assign('VIEW', $request->get('view'));
        $viewer->assign('MODULE_MODEL', $moduleModel);
        $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $cv = new CustomView_EditAjax_View;
        $vr = new Vtiger_Request;
        $vr->set('source_module', $moduleName);
        $vr->set('module', 'CustomView');
        $vr->set('view', 'EditAjax');
        $vr->set('record', $request->get('viewname'));
        $cv->getSearch($vr, $viewer);
        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();
        $this->viewName = $request->get('viewname');
        require 'crmcache/departmentanduserinfo.php';
        $viewer->assign('CACHEDEPARTMENT', $cachedepartment);
        $viewer->assign('VIEWNAME', $this->viewName);
        $viewer->assign('BUSSINESSTYPE', $request->get('bussinesstype'));
        $viewer->view('ListViewContents.tpl', $moduleName);
    }
}