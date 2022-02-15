<?php
/*+***********************************************************************************
$db = PearDatabase::getInstance();
            $sql1= 'SELECT projectid FROM  `vtiger_SalesorderProjectTasksrel` WHERE salesorderid=?';
            $projectids= $db->pquery($sql1,array($recordId));
            $number = $db->num_rows($projectids);

            //$query =  "SELECT crm.*, ptsk.* FROM `vtiger_projecttask` ptsk LEFT JOIN vtiger_crmentity crm ON crm.crmid = ptsk.projecttaskid WHERE ptsk.projectid = ( SELECT ptskrel.projectid FROM `vtiger_SalesorderProjectTasksrel` ptskrel WHERE ptskrel.salesorderid =$recordId) AND crm.deleted = 0";
            $query =  "SELECT crm.*, ptsk.* FROM `vtiger_projecttask` ptsk LEFT JOIN vtiger_crmentity crm ON crm.crmid = ptsk.projecttaskid WHERE ";
        if($db->num_rows($projectids)){
			while($row=$db->fetch_array($projectids)){
			   $query .= "ptsk.projectid = ".$row['projectid']." OR ";
			}
		}
		$query = rtrim($query,' OR ');
 * 
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * return "SELECT crm.*, ptsk.* FROM `vtiger_projecttask` ptsk LEFT JOIN vtiger_crmentity crm ON crm.crmid = ptsk.projecttaskid WHERE ptsk.projectid = ( SELECT ptskrel.projectid FROM `vtiger_SalesorderProjectTasksrel` ptskrel WHERE ptskrel.salesorderid = 362309 ) AND crm.deleted = 0";
 *************************************************************************************/

class SalesOrder_Module_Model extends Vtiger_Module_Model{
    public function getRelationQuery($recordId, $functionName, $relatedModule) {
        // $functionName; get_projecttask
        if ($functionName === 'get_SalesorderProjectTasksrel') {
            $db = PearDatabase::getInstance();
            $query =  "SELECT
	sprotsk.salesorderprojecttasksrelid AS crmid,
	sprotsk.*
FROM
	`vtiger_SalesorderProjectTasksrel` sprotsk
LEFT JOIN vtiger_crmentity crm ON crm.crmid = sprotsk.salesorderid
WHERE
	sprotsk.salesorderid = $recordId";
        } else{
            $query = parent::getRelationQuery($recordId, $functionName, $relatedModule);
        }
    
        return $query;
    }
	 public function getSideBarLinks($linkParams) {
		//$parentQuickLinks = parent::getSideBarLinks($linkParams);
		$parentQuickLinks = array();
		$quickLink = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => '工单列表',
				'linkurl' => $this->getListViewUrl(),
				'linkicon' => '',
		);
		/* $quickLink1 = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => '待审核工单',
				'linkurl' => $this->getListViewUrl().'&public=audit',
				'linkicon' => '',
		 );*/
		$quickLink2 = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => '被打回工单',
				'linkurl' => $this->getListViewUrl().'&public=refuse',
				'linkicon' => '',
		);
	
	
		//Check profile permissions for Dashboards
// 		$moduleModel = Vtiger_Module_Model::getInstance('Dashboard');
// 		$userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
// 		$permission = $userPrivilegesModel->hasModulePermission($moduleModel->getId());
// 		if($permission) {
			$parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);
//			$parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink1);
			$parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
//		}
	
		return $parentQuickLinks;
	}
}
?>
