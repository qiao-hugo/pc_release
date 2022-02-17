<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class CompayCode_List_View extends Vtiger_KList_View{
    public function process (Vtiger_Request $request)
    {

        $strPublic = $request->get('filter');

        if($strPublic=='leadsetting') {
            $moduleName = $request->getModule();
            $viewer = $this->getViewer($request);
            global $adb;
            $user=$adb->run_query_allrecords("select id,CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users WHERE `status`='Active'");
            $invoicecompany=$adb->run_query_allrecords("SELECT * FROM vtiger_invoicecompany");
            $LISTDUSER=$adb->run_query_allrecords("SELECT vtiger_invoicecompanyuser.*,(select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_invoicecompanyuser.userid=vtiger_users.id) as last_name FROM vtiger_invoicecompanyuser WHERE deleted=0");
            $company_code=$adb->run_query_allrecords("SELECT company_codeno,companyname FROM `vtiger_company_code`");
            $viewer->assign('USER',$user);
            $viewer->assign('INVOICECOMPANY',$invoicecompany);
            $viewer->assign('LISTDUSER',$LISTDUSER);
            $viewer->assign('COMPANYCODE',$company_code);
            $viewer->view('leadsetting.tpl', $moduleName);
            exit;
        }


        parent::process($request);
    }
}