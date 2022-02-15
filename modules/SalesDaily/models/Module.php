<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ************************************************************************************/

class SalesDaily_Module_Model extends Vtiger_Module_Model {

    /**
     * Function to get the Quick Links for the module
     * @param <Array> $linkParams
     * @return <Array> List of Vtiger_Link_Model instances
     */
    public function getSideBarLinks($linkParams) {
        $parentQuickLinks = parent::getSideBarLinks($linkParams);
        $quickLink1 = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '每日新增40%的客户列表',
            'linkurl' => $this->getListViewUrl().'&public=ItemNotv',
            'linkicon' => '',
        );
        $quickLink2 = array(
        		'linktype' => 'SIDEBARLINK',
        		'linklabel' => '可成交客户列表',
        		'linkurl' => $this->getListViewUrl().'&public=CanDeal',
        		'linkicon' => '',
        );
        $quickLink3 = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '成交客户列表',
            'linkurl' => $this->getListViewUrl().'&public=DayDeal',
            'linkicon' => '',
        );
        $quickLink4 = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '次日拜访列表',
            'linkurl' => $this->getListViewUrl().'&public=NextDayVisit',
            'linkicon' => '',
        );
        $quickLink5 = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '未写日报人员(列表)',
            'linkurl' => $this->getListViewUrl().'&public=NoDaily',
            'linkicon' => '',
        );
        $quickLink6 = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '未写日报人员(日历)',
            'linkurl' => $this->getListViewUrl().'&report=NoDaily',
            'linkicon' => '',
        );
        $quickLink7 = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '部门当月日报',
            'linkurl' => $this->getListViewUrl().'&report=MonthDaily',
            'linkicon' => '',
        );

        //Check profile permissions for Dashboards
        $moduleModel = Vtiger_Module_Model::getInstance('Dashboard');
        $userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $permission = $userPrivilegesModel->hasModulePermission($moduleModel->getId());
        if($permission) {
            //$parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink1);
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink3);
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink4);
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink5);
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink6);
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink7);
        }

        return $parentQuickLinks;
    }
    public function getSearchFields(){
        if($_REQUEST['public']=='NoDaily') {
            if (empty($this->searchfields)) {
                $moduleBlockFields = Vtiger_Field_Model::getAllForModule($this);
                $this->fields = array();
                foreach ($moduleBlockFields as $moduleFields) {

                    foreach ($moduleFields as $moduleField) {
                        if($moduleField->get('name')!='dailydatetime' && $moduleField->get('name')!='smownerid')continue;
                        $block = $moduleField->get('block');
                        $searchtype = $moduleField->get('searchtype');
                        //$presence = $moduleField->get('presence');
                        $isshowfield = $moduleField->get('isshowfield');
                        $reltablename = $moduleField->get('reltablename');
                        if (empty($block)) {
                            continue;
                        }
                        if (empty($searchtype)) {
                            continue;
                        }
                        if ($isshowfield == 1) {
                            continue;
                        }
                        if (!empty($reltablename)) {
                            $moduleField->set('column', $moduleField->get('reltablename') . '.' . $moduleField->get('reltablecol'));
                        } else {
                            $moduleField->set('column', $moduleField->get('table') . '.' . $moduleField->get('column'));
                        }
                        $this->searchfields[$moduleField->get('name')] = $moduleField;
                    }
                }
            }
            return $this->searchfields;
        }else{
            return parent::getSearchFields();
        }
    }
}
