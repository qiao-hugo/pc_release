<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ************************************************************************************/

class ServiceAssignRule_Module_Model extends Vtiger_Module_Model {

	/**
	 * Function to get the Quick Links for the module
	 * @param <Array> $linkParams
	 * @return <Array> List of Vtiger_Link_Model instances
	 */
	public function getSideBarLinks($linkParams) {
		$parentQuickLinks = parent::getSideBarLinks($linkParams);

		$quickLink1 = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => '按产品分配列表',
				'linkurl' => $this->getListViewUrl().'&public=products',
				'linkicon' => '',
		);
		$quickLink2 = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => '按客户分配列表',
				'linkurl' => $this->getListViewUrl().'&public=accounts',
				'linkicon' => '',
		);
		//steel 2015-4-22添加
		$quickLink3 = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => '未分配客服的客户列表',
				'linkurl' => $this->getListViewUrl().'&public=unaccounts',
				'linkicon' => '',
		);
		
		//Check profile permissions for Dashboards 
		$moduleModel = Vtiger_Module_Model::getInstance('Dashboard');
		$userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		$permission = $userPrivilegesModel->hasModulePermission($moduleModel->getId());
		if($permission) {
			$parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink1);
			$parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
			$parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink3);
		}
		
		return $parentQuickLinks;
	}
}
