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
class RankProtect_Module_Model extends Vtiger_Module_Model {
    /**
     * Function to get the Quick Links for the module
     * @param <Array> $linkParams
     * @return <Array> List of Vtiger_Link_Model instances
     */
    public function getSideBarLinks($linkParams) {
        global $current_user;

        $parentQuickLinks = parent::getSideBarLinks($linkParams);

        $quickLink[] = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '客户保护数量设置',
            'linkurl' => $this->getListViewUrl().'&filter=protected',
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
    /**
     * 用户
     * @param $str
     * @return array
     */
    public  function getUserInfo(){
        $db=PearDatabase::getInstance();
        $query="SELECT id,last_name FROM vtiger_users WHERE `status`='Active' AND id>1";
        return $db->run_query_allrecords($query);
    }

    /**
     * 阳
     */
    public function getProtectData(){
        $db=PearDatabase::getInstance();
        $query="SELECT id,last_name,vtiger_protectsetting.protectnum FROM vtiger_protectsetting LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_protectsetting.userid";
        return $db->run_query_allrecords($query);
    }
    /**
     * 查询所有数据
     * @return array
     */
    public static function getRankProtect(){
        $db=PearDatabase::getInstance();
        $query="SELECT a.rankid,a.accountrank,a.protectday,a.protectnum,a.performancerank,a.configurationitem,a.department as departments,d.departmentname as department,a.staff_stage,a.isupdate,a.followday,a.isfollow  FROM vtiger_rankprotect as a  LEFT JOIN vtiger_departments as d ON d.departmentid=a.department  ORDER BY rankid DESC";
        return $db->run_query_allrecords($query);
    }

}
