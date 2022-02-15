<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
vimport('~~/vtlib/Vtiger/Module.php');

/**
 * Vtiger Module Model Class
 */
class Billing_Module_Model extends Vtiger_Module_Model {
    public function getSideBarLinks($linkParams) {
        $quickLink[] = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '',
            'linkurl' => $this->getListViewUrl().'',
            'linkicon' => '',
        );
        //Check profile permissions for Dashboards
        $moduleModel = Vtiger_Module_Model::getInstance('Dashboard');
        $userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $permission = $userPrivilegesModel->hasModulePermission($moduleModel->getId());
        if($permission) {
            foreach ($quickLink as $val){
                $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($val);
            }
        }
        return $parentQuickLinks;
    }

}
