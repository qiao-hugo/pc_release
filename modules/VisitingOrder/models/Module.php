<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ************************************************************************************/

class VisitingOrder_Module_Model extends Vtiger_Module_Model {

    /**
     * Function to get the Quick Links for the module
     * @param <Array> $linkParams
     * @return <Array> List of Vtiger_Link_Model instances
     */
    public function getSideBarLinks($linkParams) {
        $parentQuickLinks = parent::getSideBarLinks($linkParams);

        switch ($_REQUEST['public']){
            case 'yesterday':
                $title='<div style="border-bottom: 1px solid #006FB6;">昨日拜访单</div>';
                break;
            case 'today':
                $title='<div style="border-bottom: 1px solid #006FB6;">今日拜访单</div>';
                break;
            case 'tomorrow':
                $title='<div style="border-bottom: 1px solid #006FB6;">明日拜访单</div>';
                break;
            default:
                $title='';
                break;
        }
        $quickLink1 = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '待跟进拜访单',
            'linkurl' => $this->getListViewUrl().'&public=unaudited',
            'linkicon' => '',
        );
        $quickLink2 = array(
        		'linktype' => 'SIDEBARLINK',
        		'linklabel' => '24小时待跟进拜访单',
        		'linkurl' => $this->getListViewUrl().'&public=FollowUp',
        		'linkicon' => '',
        );
        $quickLink3 = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '已跟进待审核拜访单',
            'linkurl' => $this->getListViewUrl().'&public=pass',
            'linkicon' => '',
        );
        $quickLink4 = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '拜访单签到',
            'linkurl' =>'index.php?module=Visitsign&view=List',
            'linkicon' => '',
        );
        $quickLink5 = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => (($_REQUEST['public']=='yesterday' && $title)?$title:'昨日拜访单'),
            'linkurl' => $this->getListViewUrl().'&public=yesterday',
            'linkicon' => '',
        );
        $quickLink6 = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => (($_REQUEST['public']=='today' && $title)?$title:'今日拜访单'),
            'linkurl' => $this->getListViewUrl().'&public=today',
            'linkicon' => '',
        );
        $quickLink7 = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => (($_REQUEST['public']=='tomorrow' && $title)?$title:'明日拜访单'),
            'linkurl' => $this->getListViewUrl().'&public=tomorrow',
            'linkicon' => '',
        );
        $quickLink8 = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '<span>待我审批</span>',
            'linkurl' => $this->getListViewUrl().'&public=toapprove',
            'linkicon' => '',
        );
        $quickLink9 = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '<span>离职人员</span>',
            'linkurl' => $this->getListViewUrl().'&public=quit',
            'linkicon' => '',
        );

        //Check profile permissions for Dashboards
        $moduleModel = Vtiger_Module_Model::getInstance('Dashboard');
        $userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $permission = $userPrivilegesModel->hasModulePermission($moduleModel->getId());
        if($permission) {
            //$parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink5);
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink6);
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink7);
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink1);
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink3);
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink4);
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink9);
            global $current_user,$modelinfo;
            if($current_user->is_admin=='on' || in_array($modelinfo['VisitAccountContract']['tabid'],$current_user->profile_tabs_permission)){
                $quickLink4 = array(
                    'linktype' => 'SIDEBARLINK',
                    'linklabel' => '部门点评',
                    'linkurl' =>'index.php?module=VisitDepartment&view=List',
                    'linkicon' => '',
                );
                $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink4);
            }
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink8);
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
}
