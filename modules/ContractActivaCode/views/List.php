<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ContractActivaCode_List_View extends Vtiger_KList_View {
    function process (Vtiger_Request $request)
    {

        $strPublic = $request->get('public');

        if ($strPublic=='ExportV'){
            $moduleName = $request->getModule();//导出
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            if(!$moduleModel->exportGrouprt('ContractActivaCode','ExportV')){   //权限验证
                return;
            }
            $viewer = $this->getViewer($request);
            $viewer->assign('DEPARTMENT',getDepartment());
            $viewer->view('exportri.tpl', $moduleName);
            exit;
        }
        if ($strPublic=='ExportRID'){
            $moduleName = $request->getModule();//导出
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            if(!$moduleModel->exportGrouprt('ContractActivaCode','ExportV')){   //权限验证
                return;
            }
            $moduleModel->CAExportDataExcel($request);
            exit;
        }
        parent::process($request);
    }
}