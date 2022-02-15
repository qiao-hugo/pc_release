<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ************************************************************************************/

class Sendmailer_Module_Model extends Vtiger_Module_Model {
    /**
     * Function to get the Quick Links for the module
     * @param <Array> $linkParams
     * @return <Array> List of Vtiger_Link_Model instances
     */
    public function getSideBarLinks($linkParams) {
        global $current_user;

        $parentQuickLinks = parent::getSideBarLinks($linkParams);
        /*2014-12-22更新人：steel更新内容去掉仪表盘链接
        $quickLink = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => 'LBL_DASHBOARD',
            'linkurl' => $this->getDashBoardUrl(),
            'linkicon' => '',
        );
        */
        global $current_user;
        if(in_array($current_user->id,array(2110,1793))){
            $quickLink[] = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '我的LQS',
                'linkurl' => $this->getListViewUrl().'&filter=myaccounts',
                'linkicon' => '',
            );
        }
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
