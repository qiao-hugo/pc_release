<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Leads_List_View extends Vtiger_KList_View {
    function process (Vtiger_Request $request)
    {

        $strPublic = $request->get('filter');
        if ($strPublic=='accountSearch' &&Leads_Module_Model::exportGrouprt('Leads', 'accountsearch')) {
            $moduleName = $request->getModule();
            $viewer = $this->getViewer($request);
            $viewer->view('accountsearch.tpl', $moduleName);
            exit;
        }elseif($strPublic=='accountDeal' &&Leads_Module_Model::exportGrouprt('Leads', 'accountdeal')) {
            $moduleName = $request->getModule();
            $viewer = $this->getViewer($request);
            $viewer->view('accountdeal.tpl', $moduleName);
            exit;
        }elseif($strPublic=='leadaccountdeal' &&Leads_Module_Model::exportGrouprt('Leads', 'leadaccountdeal')) {
            $moduleName = $request->getModule();
            $viewer = $this->getViewer($request);
            $viewer->view('leadaccountdeal.tpl', $moduleName);
            exit;
        }elseif($strPublic=='leadbatch' &&Leads_Module_Model::exportGrouprt('Leads', 'leadbatch')) {
            $moduleName = $request->getModule();
            $viewer = $this->getViewer($request);
            $user=Leads_Record_Model::selectAllUser();
            include 'crmcache/departmentanduserinfo.php';
            $viewer->assign('DEPARTMENTUSER',$cachedepartment);
            $viewer->assign('USER',$user);
            $viewer->view('leadbatch.tpl', $moduleName);
            exit;
        }elseif($strPublic=='leadsetting' && Leads_Module_Model::exportGrouprt('Leads', 'leadsetting')) {
            $moduleName = $request->getModule();
            $viewer = $this->getViewer($request);
            $user=Leads_Record_Model::selectAllUser();
            $recordModel=new Leads_Record_Model();
            $viewer->assign('USER',$user);

            include 'crmcache/departmentanduserinfo.php';
            $viewer->assign('DEPARTMENTUSER',$cachedepartment);
            $viewer->assign('FIXEDUSER',$recordModel->getSendMailFixed());
            $viewer->assign('LISTDUSER',$recordModel->getSendMailDepartment());
            $viewer->assign('GONGHAIMAIL',$recordModel->getSendMailSetting());
            $viewer->view('leadsetting.tpl', $moduleName);
            exit;
        } elseif($strPublic == 'assignpersonal') {//分配人员设置
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            if(!$moduleModel->exportGrouprt('Leads','AssignPersonal')){   //权限验证
//                return;
            }
            $viewer = $this->getViewer($request);
            $viewer->assign('RECOEDS',Leads_Record_Model::getAssignList());
            $viewer->assign('DEPARTMENT',getDepartment());
            $viewer->view('assignpersonal.tpl', $moduleName);
            exit;
        }elseif($strPublic == 'cluesharing') {//分配人员设置
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            if(!$moduleModel->exportGrouprt('Leads','ClueSharing')){   //权限验证
                return;
            }
            $recordModel = Leads_Record_Model::getCleanInstance($moduleName);
            $datas = $recordModel->getShareSetting();
            $viewer = $this->getViewer($request);
            $viewer->assign('DATAS',$datas);
            $viewer->view('cluesharing.tpl', $moduleName);
            exit;
        }


        parent::process($request);
    }
}
