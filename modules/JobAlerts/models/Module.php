<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ************************************************************************************/

class JobAlerts_Module_Model extends Vtiger_Module_Model {

	/**
	 * Function to get the Quick Links for the module
	 * @param <Array> $linkParams
	 * @return <Array> List of Vtiger_Link_Model instances
	 */
	public function getSideBarLinks($linkParams) {
		$parentQuickLinks = parent::getSideBarLinks($linkParams);

		$quickLink1 = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => '我创建的未到期提醒(<font size="2" color="red">'.JobAlerts_Record_Model::getReminderResultCount('new').'件</font>)',
				'linkurl' => $this->getListViewUrl().'&public=new',
				'linkicon' => '',
		);
		
		$quickLink2 = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => '我创建的待处理提醒(<font size="2" color="red">'.JobAlerts_Record_Model::getReminderResultCount('wait').'件</font>)',
				'linkurl' => $this->getListViewUrl().'&public=wait',
				'linkicon' => '',
		);
		$quickLink3 = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => '我创建的已处理提醒(<font size="2" color="red">'.JobAlerts_Record_Model::getReminderResultCount('finish').'件</font>)',
				'linkurl' => $this->getListViewUrl().'&public=finish',
				'linkicon' => '',
		);
		$quickLink4 = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => '我的待处理全部提醒(<font size="2" color="red">'.JobAlerts_Record_Model::getReminderResultCount('myreminder').'件</font>)',
				'linkurl' => $this->getListViewUrl().'&public=myreminder',
				'linkicon' => '',
		);
		$quickLink5 = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => '与我相关的全部提醒(<font size="2" color="red">'.JobAlerts_Record_Model::getReminderResultCount('relation').'件</font>)',
				'linkurl' => $this->getListViewUrl().'&public=relation',
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
		}
		
		return $parentQuickLinks;
	}
}
