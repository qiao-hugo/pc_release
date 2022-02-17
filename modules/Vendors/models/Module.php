<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ************************************************************************************/

class Vendors_Module_Model extends Vtiger_Module_Model {

    /**
     * Function to get the Quick Links for the module
     * @param <Array> $linkParams
     * @return <Array> List of Vtiger_Link_Model instances
     */
    public function getSideBarLinks($linkParams) {
        $parentQuickLinks = parent::getSideBarLinks($linkParams);
        $quickLink1 = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '我的供应商',
            'linkurl' => $this->getListViewUrl().'&filter=myvendors',
            'linkicon' => '',
        );
        $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink1);
        $quickLink1 = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '供应商查询',
            'linkurl' => $this->getListViewUrl().'&public=search',
            'linkicon' => '',
        );
        $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink1);

        $quickLink2 = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '供应商报表',
            'linkurl' => $this->getListViewUrl().'&public=sale',
            'linkicon' => '',
        );

        $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
        if($this->exportGrouprt('Vendors','dempartConfirm'))
        {
            $quickLink2 = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '供应商审核设置',
                'linkurl' => $this->getListViewUrl() . '&public=dempartConfirm',
                'linkicon' => '',
            );
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
        }
        return $parentQuickLinks;
    }

    /**
     * @param $module
     * @param $classname
     * @param int $id
     * @return bool
     */
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
    public function getAuditsettings($auditsettingtype="ServiceContracts") {
        $db=PearDatabase::getInstance();
        $sql = "SELECT auditsettingsid, '供应商审核' AS auditsettingtype,
   (select vtiger_departments.departmentname FROM vtiger_departments WHERE vtiger_departments.departmentid=vtiger_auditsettings.department) AS department,
   (SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_auditsettings.oneaudituid) AS oneaudituid, 
   IFNULL((SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_auditsettings.towaudituid ),'--') AS towaudituid, 
   IFNULL((SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_auditsettings.audituid3 ),'--') AS audituid3
   FROM vtiger_auditsettings WHERE auditsettingtype=? ORDER BY auditsettingsid DESC";
        //return $db->run_query_allrecords($sql,array($auditsettingtype));
        return $db->pquery($sql,array($auditsettingtype));
    }
}
