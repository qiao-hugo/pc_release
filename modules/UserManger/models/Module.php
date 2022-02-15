<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ************************************************************************************/

class UserManger_Module_Model extends Vtiger_Module_Model {
    public function checkDuplicateUser($querytype,$fieldvalue,$record){
        $db = PearDatabase::getInstance();
        $recordstr='';
        if($record>0){
            $recordstr=' AND usermangerid<>'.$record;
        }
        if($querytype=='user_name'){
            $query = 'SELECT 1 FROM vtiger_usermanger WHERE user_name = ?'.$recordstr;
            $result = $db->pquery($query, array($fieldvalue));
            if($db->num_rows($result) > 0){
                return true;
            }
        }elseif($querytype=='usercode'){
            $query = "SELECT 1 FROM vtiger_usermanger WHERE status='Active' and usercode = ?".$recordstr;
            $result = $db->pquery($query, array($fieldvalue));
            if($db->num_rows($result) > 0){
                return true;
            }
        }elseif($querytype=='email1'){
            $query = "SELECT 1 FROM vtiger_usermanger WHERE status='Active' and email1 = ?".$recordstr;
            $result = $db->pquery($query, array($fieldvalue));
            if($db->num_rows($result) > 0){
                return true;
            }
        }
        return false;
    }
    /**
     * 搜索使用，
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
                    $isshowfield = $moduleField->get('isshowfield');
                    $reltablename = $moduleField->get('reltablename');
                    if(in_array($moduleField->get('name'),array('departmentid','roleid'))){
                        $moduleField->set('uitype',130);
                        $moduleField->set('reltablename','vtiger_usermanger');
                        $moduleField->set('reltablecol',$moduleField->get('name'));
                    }
                    if($moduleField->get('name')=='reports_to_id'){
                        $moduleField->set('uitype',53);
                        $moduleField->set('reltablename','vtiger_usermanger');
                        $moduleField->set('reltablecol','reports_to_id');
                    }
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
                    $this->searchfields[$moduleField->get('name')] = $moduleField;
                }
            }
        }
        return $this->searchfields;
    }
    public function getSideBarLinks($linkParams) {
        $parentQuickLinks = parent::getSideBarLinks($linkParams);
        $recordModel = Vtiger_Record_Model::getCleanInstance('UserManger');
        if($recordModel->personalAuthority('UserManger','AuditSettings')){
            $quickLink4 = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '用户添加审核设置',
                'linkurl' =>'index.php?module=UserManger&view=List&public=AuditSettings',
                'linkicon' => '',
            );
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink4);

        }
        if($recordModel->personalAuthority('UserManger','import')){
            $quickLink4 = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '导入数据',
                'linkurl' =>'index.php?module=UserManger&view=List&public=import',
                'linkicon' => '',
            );
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink4);
        }
        return $parentQuickLinks;
    }
}
