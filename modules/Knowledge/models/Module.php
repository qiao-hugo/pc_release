<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * Vtiger Module Model Class
 */
class Knowledge_Module_Model extends Vtiger_Module_Model {

    public function getSideBarLinks($linkParams) {
        $parentQuickLinks = array();
        $quickLink = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '记录列表',
            'linkurl' => $this->getListViewUrl(),
            'linkicon' => '',
        );
        $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);
        if($this->exportGrouprt('Knowledge','undercarriage')) {
            $quickLink2 = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '下架专区',
                'linkurl' => $this->getListViewUrl() . '&public=undercarriage',
                'linkicon' => '',
            );
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
        }


        return $parentQuickLinks;
    }


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

    public function displayRole($params){
        include 'crmcache/role.php';
        $valueArray=empty($params)?array():explode(' |##| ',$params);
        $tmpString = '';
        foreach($valueArray as $index => $val) {
            $tmpStr = str_replace('|', '', $roles[$val]);
            $tmpStr = str_replace('|', '', $tmpStr);
            $tmpStr = str_replace('—', '', $tmpStr);
            $tmpString .= $tmpStr . ',';
        }
        return trim($tmpString,',');
    }

}
