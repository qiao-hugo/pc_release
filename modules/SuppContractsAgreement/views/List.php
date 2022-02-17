<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class SuppContractsAgreement_List_View extends Vtiger_KList_View {
    function process (Vtiger_Request $request)
    {

        $strPublic = $request->get('public');

        if($strPublic == 'dempartConfirm') {//非标合同部门负责审核设置
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            if (!$moduleModel->exportGrouprt('SuppContractsAgreement', 'dempartConfirm')) {   //权限验证
                return;
            }
            $viewer = $this->getViewer($request);
            $viewer->assign('USER', ReceivedPayments_Record_Model::getuserinfo('AND `status`=\'Active\''));
            $viewer->assign('RECOEDS', ServiceContracts_Record_Model::getAuditsettings('SuppContractsAgreement'));
            $viewer->assign('DEPARTMENT', getDepartment());
            $viewer->view('dempartConfirm.tpl', $moduleName);
            exit;
        }

        parent::process($request);
    }
}