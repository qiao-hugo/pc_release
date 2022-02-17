<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ************************************************************************************/

class IndicatorSetting_Module_Model extends Vtiger_Module_Model
{

    /**
     * Function to get the Quick Links for the module
     * @param <Array> $linkParams
     * @return <Array> List of Vtiger_Link_Model instances
     */
    static $staff_stages = array(
        1 => '1个月内',
        2 => '1~3个月',
        3 => '3~6个月',
        4 => '6~12个月',
        5 => '12个月以上'
    );

    static $relationships = array(
        'telnumber' => '电话量',
        'telduration' => '电话时长',
        'intended_number' => '意向客户数',
        'invite_number' => '邀约数',
        'visit_number' => '拜访量',
        'returned_money' => '回款'
    );

    static $operations = array(
//        1=>'=',
//        2=>'>',
//        3=>'<',
        4 => '>=',
//        5=>'<='
    );

    static $operate_operatoes = array(
        '-'
//        '+', '-', '*', '/'
    );

    public function getSideBarLinks($linkParams)
    {
        $parentQuickLinks = parent::getSideBarLinks($linkParams);
        $quickLink1 = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '员工工作情况统计表',
            'linkurl' => 'index.php?module=TelStatistics&view=List&public=eworkstatistics',
            'linkicon' => '',
        );
        $quickLink2 = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '员工工作情况趋势图',
            'linkurl' => 'index.php?module=TelStatistics&view=List&public=eworksituationtrends',
            'linkicon' => '',
        );

        $quickLink3 = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '权限设置',
            'linkurl' => 'index.php?module=TelStatistics&view=List&public=checkpermissionset',
            'linkicon' => '',
        );
        $quickLink4 = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '员工每日工作指标设定',
            'linkurl' => $this->getListViewUrl(),
            'linkicon' => '',
        );
        $quickLink5 = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '电话量统计列表 ',
            'linkurl' => 'index.php?module=TelStatistics&view=List',
            'linkicon' => '',
        );


        //Check profile permissions for Dashboards
        $moduleModel = Vtiger_Module_Model::getInstance('Dashboard');
        $userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $permission = $userPrivilegesModel->hasModulePermission($moduleModel->getId());
        if ($permission) {
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink5,$_REQUEST['public']);
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink1);
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
            if($this->exportGrouprtService('TelStatistics','setpermission')) {
                $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink3);
            }
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink4);
        }

        return $parentQuickLinks;
    }

    public function exportGrouprtService($module,$classname,$id=0){
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


    public static function vtranslateRelation($relationship, $module)
    {
        $rawValues = $relationship;
        if (!is_array($relationship)) {
            $rawValues = explode(',', $relationship);
        }
        $string = '';
        foreach ($rawValues as $rawValue) {
            $string .= vtranslate($rawValue, $module) . ',';
        }
        return rtrim($string, ',');
    }
}
