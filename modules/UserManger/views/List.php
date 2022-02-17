<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class UserManger_List_View extends Vtiger_KList_View {
    function process (Vtiger_Request $request){
        $strPublic = $request->get('public');
        if($strPublic == 'AuditSettings') {
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Record_Model::getCleanInstance('UserManger');
            if(!$moduleModel->personalAuthority('UserManger', 'AuditSettings')) {
                parent::process($request);
                return;
            }
            $viewer = $this->getViewer($request);
            $viewer->assign('USER', ReceivedPayments_Record_Model::getuserinfo(" AND vtiger_users.`status`='Active' AND vtiger_users.isdimission=0"));
            $viewer->assign('RECOEDS', $moduleModel->getAuditsettings('UserManger'));
            $viewer->assign('DEPARTMENT', $moduleModel->getInvoicecompany());

            $viewer->assign('CLASSNAME', ServiceContracts_Record_Model::getSetPermissions());
            $viewer->view('auditSettings.tpl', $moduleName);
            exit;
        }
        if($strPublic == 'import') {
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Record_Model::getCleanInstance('UserManger');
            if(!$moduleModel->personalAuthority('UserManger', 'import')) {
                parent::process($request);
                return;
            }
            global $adb;
            $query="select * from vtiger_users";
            $result=$adb->pquery($query,array());
            if($adb->num_rows($result)){
                $date=date('Y-m-d H:i:s');
                while($row=$adb->fetch_array($result)){
                    $query='select 1 from vtiger_usermanger WHERE userid=?';
                    $tresult=$adb->pquery($query,array($row['id']));
                    if($adb->num_rows($tresult)==0){
                        $crmid=$adb->getUniqueID('vtiger_crmentity');
                        $sql="INSERT INTO `vtiger_usermanger` (usermangerid,`userid`, `user_name`, `user_password`, `user_hash`, `cal_color`, `first_name`, `last_name`, `reports_to_id`, `is_admin`, `currency_id`, `description`, `date_entered`, `date_modified`, `modified_user_id`, `title`, `invoicecompany`, `companyid`, `department`, `phone_home`, `phone_mobile`, `phone_work`, `phone_other`, `phone_fax`, `email1`, `email2`, `secondaryemail`, `status`, `signature`, `address_street`, `address_city`, `address_state`, `user_sys`, `address_postalcode`, `user_preferences`, `tz`, `holidays`, `namedays`, `workdays`, `weekstart`, `date_format`, `hour_format`, `start_hour`, `end_hour`, `activity_view`, `lead_view`, `imagename`, `deleted`, `confirm_password`, `internal_mailer`, `reminder_interval`, `reminder_next_time`, `crypt_type`, `accesskey`, `theme`, `language`, `time_zone`, `currency_grouping_pattern`, `currency_decimal_separator`, `currency_grouping_separator`, `currency_symbol_placement`, `no_of_currency_decimals`, `truncate_trailing_zeros`, `dayoftheweek`, `callduration`, `othereventduration`, `calendarsharedtype`, `default_record_view`, `leftpanelhide`, `rowheight`, `old_departmentid`, `old_user_password`, `usermodifiedtime`, `usercode`, `user_entered`, `fillinsales`, `brevitycode`, `leavedate`, `isdimission`,departmentid,roleid,secondroleid,employeelevel,modulestatus) 
                          SELECT ?,`id`, `user_name`, `user_password`, `user_hash`, `cal_color`, `first_name`, `last_name`, `reports_to_id`, `is_admin`, `currency_id`, `description`, `date_entered`, `date_modified`, `modified_user_id`, `title`, `invoicecompany`, `companyid`, `department`, `phone_home`, `phone_mobile`, `phone_work`, `phone_other`, `phone_fax`, `email1`, `email2`, `secondaryemail`, `status`, `signature`, `address_street`, `address_city`, `address_state`, `user_sys`, `address_postalcode`, `user_preferences`, `tz`, `holidays`, `namedays`, `workdays`, `weekstart`, `date_format`, `hour_format`, `start_hour`, `end_hour`, `activity_view`, `lead_view`, `imagename`, `deleted`, `confirm_password`, `internal_mailer`, `reminder_interval`, `reminder_next_time`, `crypt_type`, `accesskey`, `theme`, `language`, `time_zone`, `currency_grouping_pattern`, `currency_decimal_separator`, `currency_grouping_separator`, `currency_symbol_placement`, `no_of_currency_decimals`, `truncate_trailing_zeros`, `dayoftheweek`, `callduration`, `othereventduration`, `calendarsharedtype`, `default_record_view`, `leftpanelhide`, `rowheight`, `old_departmentid`, `old_user_password`, `usermodifiedtime`, `usercode`, `user_entered`, `fillinsales`, `brevitycode`, `leavedate`, `isdimission`,vtiger_user2department.departmentid,vtiger_user2role.roleid,vtiger_user2role.secondroleid,'EmployeeLevel','c_complete' FROM vtiger_users LEFT JOIN vtiger_user2department ON vtiger_users.id=vtiger_user2department.userid LEFT JOIN vtiger_user2role ON vtiger_user2role.userid=vtiger_users.id WHERE vtiger_users.id=?";
                        $adb->pquery($sql,array($crmid,$row['id']));
                        $sql="INSERT INTO `vtiger_crmentity` (`crmid`, `smcreatorid`, `smownerid`, `modifiedby`, `setype`, `description`, `createdtime`, `modifiedtime`, `viewedtime`, `status`, `version`, `presence`, `deleted`, `label`) VALUES (?, 1, '1', '1', 'UserManger', '', ?, ?, NULL, NULL, '0', '1', 0, ?)";
                        $adb->pquery($sql,array($crmid,$date,$date,$row['last_name']));
                    }
                }
            }
            echo '导入成功';
            exit;
        }
        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
        $this->viewName = $request->get('viewname');
        $viewer->assign('VIEWNAME', $this->viewName);

        if ($request->isAjax()) {
            $this->initializeListViewContents($request, $viewer);//竟然调用两次，这边其实是ajax调用的，哈哈！！
            $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
        }

        $viewer->assign('VIEW', $request->get('view'));
        $viewer->assign('MODULE_MODEL', $moduleModel);
        $viewer->assign('USER',ReceivedPayments_Record_Model::getuserinfo(" AND `status`='Active'"));

        $viewer->view('ListViewContents.tpl', $moduleName);
    }
}