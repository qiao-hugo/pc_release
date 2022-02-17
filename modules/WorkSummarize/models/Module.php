<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ************************************************************************************/

class WorkSummarize_Module_Model extends Vtiger_Module_Model {

	/**
	 * Function to get the Quick Links for the module
	 * @param <Array> $linkParams
	 * @return <Array> List of Vtiger_Link_Model instances
	 */
	public function getSideBarLinks($linkParams) {
		$parentQuickLinks = parent::getSideBarLinks($linkParams);
		$quickLink[] = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => '我的工作总结',
				'linkurl' => $this->getListViewUrl().'&filter=owner',
				'linkicon' => '',
		);
		$quickLink[] = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => '未写工作总结人员',
				'linkurl' => $this->getListViewUrl().'&filter=nowrite',
				'linkicon' => '',
		);
		$quickLink[] = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => '要回复的工作总结',
				'linkurl' => $this->getListViewUrl().'&filter=reply',
				'linkicon' => '',
		);
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
}
