<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ************************************************************************************/

class SalesorderProductsrel_Module_Model extends Vtiger_Module_Model {

	/**
	 * Function to get the Quick Links for the module
	 * @param <Array> $linkParams
	 * @return <Array> List of Vtiger_Link_Model instances
	 */
	public function getSideBarLinks($linkParams) {
		$parentQuickLinks = parent::getSideBarLinks($linkParams);

		$quickLink1 = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => '待审核',
				'linkurl' => $this->getListViewUrl().'&public=unaudited',
				'linkicon' => '',
		);
		$quickLink2 = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => '已打回',
				'linkurl' => $this->getListViewUrl().'&public=reject',
				'linkicon' => '',
		);
		$quickLink3 = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => '历史记录',
				'linkurl' => $this->getListViewUrl().'&public=pass',
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
		}
		
		return $parentQuickLinks;
	}
	
	/**
	 * Function to save a given record model of the current module
	 * @param Vtiger_Record_Model $recordModel
	 */
	public function saveRecord(Vtiger_Record_Model $recordModel) {
		$moduleName = $this->get('name');
		
		$focus = CRMEntity::getInstance($moduleName);
	
		$fields = $focus->column_fields;
	
	
		foreach($fields as $fieldName => $fieldValue) {
			$fieldValue = $recordModel->get($fieldName);
			if(is_array($fieldValue)){
				$focus->column_fields[$fieldName] = $fieldValue;
			}else if($fieldValue !== null) {
				$focus->column_fields[$fieldName] = decode_html($fieldValue);
			}
				
		}
		$focus->mode = $recordModel->get('mode');
	
		$focus->id = $recordModel->getId();
	
		$focus->save($moduleName);
	
		return $recordModel->setId($focus->id);
	}
	
	
}
