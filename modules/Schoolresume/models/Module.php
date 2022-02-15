<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ************************************************************************************/

class Schoolresume_Module_Model extends Vtiger_Module_Model {

    /**
     * Function to get the Quick Links for the module
     * @param <Array> $linkParams
     * @return <Array> List of Vtiger_Link_Model instances
     */
    public function getSideBarLinks($linkParams) {
        $parentQuickLinks = parent::getSideBarLinks($linkParams);
        $quickLink1 = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '在岗超过一个月名单',
            'linkurl' => $this->getListViewUrl().'&public=oneMonth',
            'linkicon' => '',
        );

        $quickLink2 = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '人才库',
            'linkurl' => $this->getListViewUrl().'&public=personnel',
            'linkicon' => '',
        );

        //Check profile permissions for Dashboards
        $moduleModel = Vtiger_Module_Model::getInstance('Dashboard');
        $userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $permission = $userPrivilegesModel->hasModulePermission($moduleModel->getId());
        if($permission) {
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink1);
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
           /* $ServiceContracts_Module = new ServiceContracts_Module_Model();
            $flag=$ServiceContracts_Module->exportGrouprt('Compensation','Import');
            if ($flag) {
                $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
            }*/
            
        }
        if(isPermitted('Schoolresume', 'EditView')=='yes'){
            $quickLink8 = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '简历导入',
                'linkurl' => 'index.php?module=Schoolresume&view=Import',
                'linkicon' => '',
            );
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink8);
        }

        return $parentQuickLinks;
    }
}
