<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ************************************************************************************/

class ContractDelaySign_Module_Model extends Vtiger_Module_Model {
    /*
     * 列表页面的菜单链接
     * */

    public function getSideBarLinks($linkParams) {
        $parentQuickLinks = array();
        $quickLink = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '合同列表',
            'linkurl' => 'index.php?module=ServiceContracts&view=List',
            'linkicon' => '',
        );
        $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);
        $quickLink2 = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '延期签收合同表(SaaS)',
            'linkurl' => $this->getListViewUrl() ,
            'linkicon' => '',
        );
        $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);

        $quickLink3 = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '延期签收合同表(非SaaS)',
            'linkurl' => $this->getListViewUrl() .'&report=notyun',
            'linkicon' => '',
        );
        $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink3);


        return $parentQuickLinks;
    }

    public function getSearchFields(){
        if(empty($this->searchfields)){
            $moduleBlockFields = Vtiger_Field_Model::getAllForModule($this);
            $this->fields = array();
            $recordModel=ContractDelaySign_Record_Model::getCleanInstance("ContractDelaySign");
            if($_REQUEST['report']=='notyun'){
                $continueColumn = $recordModel->noTyunContinueColumn;
            }else{
                $continueColumn = $recordModel->tyunContinueColumn;
            }
            foreach($moduleBlockFields as $moduleFields){
                foreach($moduleFields as $moduleField){
                    $block = $moduleField->get('block');
                    $searchtype = $moduleField->get('searchtype');
                    //$presence = $moduleField->get('presence');
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
                        $moduleField->set('column', $moduleField->get('table').'.'.$moduleField->get('column'));
                    }
                    if(in_array($moduleField->get('name'),$continueColumn)){
                        continue;
                    }

                    $this->searchfields[$moduleField->get('name')] = $moduleField;
                }
            }
        }
        return $this->searchfields;
    }

}
