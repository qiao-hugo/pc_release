<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ************************************************************************************/

class TelStatistics_Module_Model extends Vtiger_Module_Model {

    /**
     * Function to get the Quick Links for the module
     * @param <Array> $linkParams
     * @return <Array> List of Vtiger_Link_Model instances
     */
    public function getSideBarLinks($linkParams) {
        $parentQuickLinks = parent::getSideBarLinks($linkParams);
        $quickLink1 = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '员工工作情况统计表',
            'linkurl' => $this->getListViewUrl().'&public=eworkstatistics',
            'linkicon' => '',
        );
        $quickLink2 = array(
        		'linktype' => 'SIDEBARLINK',
        		'linklabel' => '员工工作情况趋势图',
        		'linkurl' => $this->getListViewUrl().'&public=eworksituationtrends',
        		'linkicon' => '',
        );

        $quickLink3 = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '权限设置',
            'linkurl' => $this->getListViewUrl().'&public=checkpermissionset',
            'linkicon' => '',
        );
        $quickLink4 = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '员工每日工作指标设定',
            'linkurl' => 'index.php?module=IndicatorSetting&view=List',
            'linkicon' => '',
        );
        $quickLink5 = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '离职人员',
            'linkurl' => $this->getListViewUrl().'&public=quit',
            'linkicon' => '',
        );


        //Check profile permissions for Dashboards
        $moduleModel = Vtiger_Module_Model::getInstance('Dashboard');
        $userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $permission = $userPrivilegesModel->hasModulePermission($moduleModel->getId());
        if($permission) {
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink1);
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink5);
            if($this->exportGrouprtService('TelStatistics','setpermission')){
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

    /**
     * 取得当前根据部门取权限
     * @param $str
     * @return array
     */
    public static function getuserinfo($str){
        $db=PearDatabase::getInstance();
        $query="SELECT id,last_name FROM vtiger_users WHERE vtiger_users.`status`='Active' {$str}";
        return $db->run_query_allrecords($query);
    }

    public static function getDepartmentByUser($user_id)
    {
        $db = PearDatabase::getInstance();
        $query = 'SELECT departmentid FROM vtiger_user2department where userid= ?';
        $params = array($user_id);
        $results = $db->pquery($query,$params);
        return $db->query_result($results);
    }

    public function exportGrouprt($module,$classname,$roleid){
        $db=PearDatabase::getInstance();
        $query="SELECT 1 FROM vtiger_telstatistics_manage WHERE deleted=0 AND roleid=? AND module=? AND classname=?";
        $result=$db->pquery($query,array($roleid,$module,$classname));
        $num=$db->num_rows($result);
        if($num){
            return true;
        }
        return false;
    }

    public static function isSkipThisRole($module,$classname,$roleid)
    {
        $db=PearDatabase::getInstance();
        $query="SELECT 1 FROM vtiger_telstatistics_manage WHERE deleted=0 AND roleid=? AND module=? AND classname=?";
        $result=$db->pquery($query,array($roleid,$module,$classname));
        $num=$db->num_rows($result);
        if($num){
            return true;
        }
        return false;
    }

    public static function getSkipRoles($module,$classname){
        $db=PearDatabase::getInstance();
        $query="SELECT * FROM vtiger_telstatistics_manage WHERE deleted=0 AND module=? AND classname=?";
        $result=$db->pquery($query,array($module,$classname));
        $num=$db->num_rows($result);
        $data = array();
        if($num){
            while ($row = $db->fetch_row($result)){
                $data[]=$row['roleid'];
            }
        }
        return $data;
    }

}
