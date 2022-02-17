<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Workflows_List_View extends Vtiger_KList_View {

    public function process (Vtiger_Request $request)
    {

        $strPublic = $request->get('filter');
        if($strPublic=='leadsetting') {
            $recordModel=Vtiger_Record_Model::getCleanInstance('Workflows');
            if($recordModel->personalAuthority('ServiceContracts','maincompanyset')) {
                $moduleName = $request->getModule();
                $viewer = $this->getViewer($request);
                global $adb;
                $user = $adb->run_query_allrecords("select id,CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users WHERE `status`='Active'");
                $invoicecompany = $adb->run_query_allrecords("SELECT * FROM vtiger_invoicecompany");
                $LISTDUSER = $adb->run_query_allrecords("SELECT vtiger_auditinvoicecompany.*,(SELECT vtiger_invoicecompany.invoicecompany FROM vtiger_invoicecompany where vtiger_invoicecompany.companycode=vtiger_auditinvoicecompany.companycode limit 1) as invoicecompany,(select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_auditinvoicecompany.userid=vtiger_users.id) as last_name FROM vtiger_auditinvoicecompany");
                $company_code = $adb->run_query_allrecords("SELECT company_codeno,companyname FROM `vtiger_company_code`");
                $viewer->assign('USER', $user);
                $viewer->assign('INVOICECOMPANY', $invoicecompany);
                $viewer->assign('LISTDUSER', $LISTDUSER);
                $viewer->assign('COMPANYCODE', $company_code);
                $viewer->view('leadsetting.tpl', $moduleName);
                return;
            }
        }else if($strPublic=='CWSH'){
            $moduleName = $request->getModule();
            $moduleModel=Vtiger_Module_Model::getInstance($moduleName);
            if(!$moduleModel->exportGrouprt('SalesOrder','CWSH')){   //权限验证
                return;
            }
            global $adb;
            $viewer = $this->getViewer($request);
            $user = $adb->run_query_allrecords("select id,CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users WHERE `status`='Active'");
            $LISTDUSER = $adb->run_query_allrecords("SELECT
    vtiger_auditCWSH.auditCWSHid,
	vtiger_departments.departmentname as department,
	(select last_name from vtiger_users where id=vtiger_auditCWSH.supervisor) as supervisor,
	(select last_name from vtiger_users where id=vtiger_auditCWSH.manager) as manager
FROM
	vtiger_auditCWSH
	LEFT JOIN vtiger_departments ON vtiger_auditCWSH.department = vtiger_departments.departmentid");
            $viewer->assign('LISTDUSER', $LISTDUSER);
            $viewer->assign('USER', $user);
            $viewer->assign('DEPARTMENT',getDepartment());
            $viewer->view('CWSH.tpl', $moduleName);
            return;
        }else if($strPublic=='REFUND_REVIEW'){
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
            if(!$moduleModel->exportGrouprt('SalesOrder', 'REFUND_REVIEW')){
                return;
            }
            $viewer = $this->getViewer($request);
            global $adb;
            $user = $adb->run_query_allrecords("select id,CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users WHERE `status`='Active'");
            $invoicecompany = $adb->run_query_allrecords("SELECT * FROM vtiger_invoicecompany");
            $LISTDUSER = $adb->run_query_allrecords("SELECT vtiger_auditinvoicecompany.*,(SELECT vtiger_invoicecompany.invoicecompany FROM vtiger_invoicecompany where vtiger_invoicecompany.companycode=vtiger_auditinvoicecompany.companycode limit 1) as invoicecompany,(select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_auditinvoicecompany.userid=vtiger_users.id) as last_name FROM vtiger_auditinvoicecompany WHERE workflowstagesflag = 'REFUND_REVIEW'");
            $company_code = $adb->run_query_allrecords("SELECT company_codeno,companyname FROM `vtiger_company_code`");
            $viewer->assign('USER', $user);
            $viewer->assign('INVOICECOMPANY', $invoicecompany);
            $viewer->assign('LISTDUSER', $LISTDUSER);
            $viewer->assign('COMPANYCODE', $company_code);
            $viewer->view('refund_review.tpl', $moduleName);
            return;
        }
        parent::process($request);
    }
}