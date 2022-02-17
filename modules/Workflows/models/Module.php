<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ************************************************************************************/

class Workflows_Module_Model extends Vtiger_Module_Model {
//流程状态
    public static $moudulestatus=array('c_complete'=>'c_complete','c_contract_n_account'=>'c_contract_n_account','c_account_n_contract'=>'c_account_n_contract');

    /* public function getSideBarLinks($linkParams) {
        $linkTypes = array('SIDEBARLINK', 'SIDEBARWIDGET');
        $links = parent::getSideBarLinks($linkParams);

        $quickLinks = array(
            array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => 'LBL_TASKS_LIST',
                'linkurl' => $this->getTasksListUrl(),
                'linkicon' => '',
            ),
        );
        foreach($quickLinks as $quickLink) {
            $links['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);
        }

        return $links;
    }

    public function getTasksListUrl() {
        $taskModel = Vtiger_Module_Model::getInstance('ProjectTask');
        return $taskModel->getListViewUrl();
    } */
    public function getSideBarLinks($linkParams) {
        $parentQuickLinks = array();
        $quickLink = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '工作流列表',
            'linkurl' => $this->getListViewUrl(),
            'linkicon' => '',
        );
        $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);

        $quickLink = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '主体公司审核设置',
            'linkurl' => $this->getListViewUrl() . '&filter=leadsetting',
            'linkicon' => '',
        );
        $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);

        if($this->exportGrouprt('SalesOrder','CWSH')){
            $quickLink = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '财务运营审核设置',
                'linkurl' => $this->getListViewUrl() . '&filter=CWSH',
                'linkicon' => '',
            );
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);
        }
        $module_Model = Vtiger_Module_Model::getCleanInstance('Workflows');
        if($module_Model->exportGrouprt('SalesOrder', 'REFUND_REVIEW')){
            $quickLink = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '财务主管复核设置',
                'linkurl' => $this->getListViewUrl() . '&filter=REFUND_REVIEW',
                'linkicon' => '',
            );
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);
        }

        return $parentQuickLinks;
    }
}