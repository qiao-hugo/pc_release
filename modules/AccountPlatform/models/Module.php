<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ************************************************************************************/

class AccountPlatform_Module_Model extends Vtiger_Module_Model {

    /**
     * Function to get the Quick Links for the module
     * @param <Array> $linkParams
     * @return <Array> List of Vtiger_Link_Model instances
     */
    public function getSideBarLinks($linkParams) {
        $parentQuickLinks = parent::getSideBarLinks($linkParams);
        if($this->exportGrouprt('AccountPlatform','Import')){
            $quickLink8 = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '回款导入',
                'linkurl' => 'index.php?module=AccountPlatform&view=Import',
                'linkicon' => '',
            );
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink8);
        }
        if($this->exportGrouprt('AccountPlatform','AccountExport')){
            $quickLink8 = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '账户导出',
                'linkurl' => 'index.php?module=AccountPlatform&view=AccountExport',
                'linkicon' => '',
            );
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink8);
        }
        if($this->exportGrouprt('AccountPlatform','BatchEditAccountRebate')){
            $quickLink8 = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '批量修改客户返点',
                'linkurl' => 'index.php?module=AccountPlatform&view=BatchEditAccountRebate',
                'linkicon' => '',
            );
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink8);
        }
        return $parentQuickLinks;
    }
    /**
     * 搜索使用， cxh 2020 - 04 -11  $isshowfield=1 但是需要显示在筛选条件中 所以特殊处理 $isshowfield=1的部分
     *  1.block部位空2.搜索类型！=0，3.presence 原来是0，2，列表有些字段为1的但是需要可以显示
     * @return bool
     */
    public function getSearchFields(){
        if(empty($this->searchfields)){
            $moduleBlockFields = Vtiger_Field_Model::getAllForModule($this);
            $this->fields = array();
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
                    if($isshowfield==1 && $moduleField->get('name')!='idaccount' && $moduleField->get('name')!='accountplatform') {
                        continue;
                    }
                    if(!empty($reltablename)){
                        $moduleField->set('column', $moduleField->get('reltablename').'.'.$moduleField->get('reltablecol'));
                    }else{
                        $moduleField->set('column', $moduleField->get('table').'.'.$moduleField->get('column'));
                    }
                    $this->searchfields[$moduleField->get('name')] = $moduleField;
                }
            }
        }
        return $this->searchfields;
    }
}
