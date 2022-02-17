<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ************************************************************************************/

class Matchreceivements_Module_Model extends Vtiger_Module_Model {



    public function exportGrouprt($module,$classname,$id=0){
        if($id==0)
        {
            global $current_user;
            $id = $current_user->id;
        }
        $db=PearDatabase::getInstance();
        $query="SELECT 1 FROM vtiger_exportmanage WHERE deleted=0 AND userid=? AND module=? AND classname=?";
        $result=$db->pquery($query,array($id,$module,$classname));
        $num=$db->num_rows($result);
        if($num){
            return true;
        }
        return false;
    }

    public function getSideBarLinks($linkParams) {
        $parentQuickLinks = parent::getSideBarLinks($linkParams);
        $moduleModel1 = Vtiger_Module_Model::getInstance('SearchMatch');
        $moduleModel2 = Vtiger_Module_Model::getInstance('DelayMatch');
        $userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $permission1 = $userPrivilegesModel->hasModulePermission($moduleModel1->getId());
        $permission2 = $userPrivilegesModel->hasModulePermission($moduleModel2->getId());
        if($permission1){
            $quickLink1 = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '查找匹配回款',
                'linkurl' => 'index.php?module=SearchMatch&view=List',
                'linkicon' => '',
            );
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink1);
        }
        if($permission2){
            $quickLink2 = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '延期匹配',
                'linkurl' => 'index.php?module=DelayMatch&view=List',
                'linkicon' => '',
            );
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
        }
        $recordModel=Vtiger_Record_Model::getCleanInstance('Matchreceivements');
        if ($recordModel->personalAuthority('ReceivedPayments', 'statisticsBoard')){
            $quickLink = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '回款统计看板',
                'linkurl' => 'index.php?module=Matchreceivements&view=List&public=receivedPaymentStatistics',
                'linkicon' => '',
            );
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);
        }
        return $parentQuickLinks;
    }
}
