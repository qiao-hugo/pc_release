<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class ContractGuarantee_List_View extends Vtiger_KList_View {
    function process (Vtiger_Request $request)
    {

        $strPublic = $request->get('public');
        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();
        if ($strPublic == 'ExportRI') {               //导出
            $viewer->assign('DEPARTMENT',getDepartment());
            $viewer->view('exportrinv.tpl', $moduleName);
            exit;
        }elseif($strPublic == 'ExportRID'){
            $recordModel=Vtiger_Record_Model::getCleanInstance('ContractGuarantee');
            $recordModel->exportData($request);
            exit;
        }
        global $current_user ;
        //这里和发票走同一个处理
        $viewer->assign('IS_EXPORTABLE',Newinvoice_Module_Model::exportGrouprt($moduleName,'is_exportable',$current_user->id));
        parent::process($request);
    }
}