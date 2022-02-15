<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ************************************************************************************/

class MaintainerAccount_Module_Model extends Vtiger_Module_Model {
    public function getListFields() {
        if(empty($this->listfields)){
            $blockids=array();
            $blocks=$this->getBlocks();
            foreach($blocks as $blockid){
                $blockids[]=$blockid->id;
            }
            $adb = PearDatabase::getInstance();
            $sql = 'SELECT vtiger_field.* FROM vtiger_field WHERE  tabid=101 ORDER BY vtiger_field.listpresence';
            $result=$adb->pquery($sql,array());
            $rows=$adb->num_rows($result);
            for($index = 0; $index < $rows; ++$index) {
                $this->listfields[]=$adb->fetch_array($result);
            }
        }
        return $this->listfields;
    }

    public function getSearchFields(){
        if(empty($this->searchfields)){
            $moduleBlockFields = Vtiger_Field_Model::getAllForModule($this);
            $this->fields = array();
            foreach($moduleBlockFields as $moduleFields){
                foreach($moduleFields as $moduleField){
                    $block = $moduleField->get('block');
                    $searchtype = $moduleField->get('searchtype');
                    $isshowfield = $moduleField->get('isshowfield');
                    $reltablename = $moduleField->get('reltablename');
                    if(empty($block)) {
                        continue;
                    }
                    if(empty($searchtype)) {
                        continue;
                    }
                    if($isshowfield==1) {
                        continue;
                    }
                    if(!empty($reltablename)){
                        $moduleField->set('column', $moduleField->get('reltablename').'.'.$moduleField->get('reltablecol'));
                    }else{
                        $moduleField->set('column', $moduleField->get('column'));
                    }
                    $this->searchfields[$moduleField->get('name')] = $moduleField;
                }
            }
        }
        return $this->searchfields;
    }
    public function getSideBarLinks($linkParams) {
        global $current_user;

        $parentQuickLinks = parent::getSideBarLinks($linkParams);
        $quickLink[] = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '一个月内到期',
            'linkurl' => $this->getListViewUrl().'&filter=ExpirationOfTheMonth',
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
