<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ************************************************************************************/

class ServiceComments_Module_Model extends Vtiger_Module_Model {

	/**
	 * Function to get the Quick Links for the module
	 * @param <Array> $linkParams
	 * @return <Array> List of Vtiger_Link_Model instances
	 */
	public function getSideBarLinks($linkParams) {
	    if(!empty($_REQUEST['filter']) && $_REQUEST['filter']=='groupbuy'){
            $quickLink9 = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '团购客户列表',
                'linkurl' => '/index.php?module=GroupBuyAccount&view=List',
                'linkicon' => '',
            );
	        $quickLink10 = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '当天需跟进',
                'linkurl' => '/index.php?module=GroupBuyAccount&view=List&public=todayneedfollow',
                'linkicon' => '',
            );
            $quickLink11 = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '当天未跟进',
                'linkurl' => '/index.php?module=GroupBuyAccount&view=List&public=todaynofollow',
                'linkicon' => '',
            );
            $quickLink12 = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '超期未跟进',
                'linkurl' => '/index.php?module=GroupBuyAccount&view=List&public=overnofollow',
                'linkicon' => '',
            );
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink9);
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink10);
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink11);
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink12);
            return $parentQuickLinks;
        }
		$parentQuickLinks = parent::getSideBarLinks($linkParams);

 	/*	$quickLink1 = array(
 				'linktype' => 'SIDEBARLINK',
 				'linklabel' => '按产品分配客服列表',
 				'linkurl' => $this->getListViewUrl().'&public=productby',
 				'linkicon' => '',
 		);
 		$quickLink2 = array(
 				'linktype' => 'SIDEBARLINK',
 				'linklabel' => '按客户分配客服列表',
 				'linkurl' => $this->getListViewUrl().'&public=accountby',
 				'linkicon' => '',
 		);
		
 		$quickLink3 = array(
 				'linktype' => 'SIDEBARLINK',
 				'linklabel' => '全部已跟进',
 				'linkurl' => $this->getListViewUrl().'&public=follow',
 				'linkicon' => '',
 		);
 		$quickLink4 = array(
 				'linktype' => 'SIDEBARLINK',
 				'linklabel' => '全部未跟进',
 				'linkurl' => $this->getListViewUrl().'&public=nofollow',
 				'linkicon' => '',
 		);*/
		
		$quickLink5 = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => '7天未跟进',
				'linkurl' => $this->getListViewUrl().'&public=7daynofollow',
				'linkicon' => '',
		);
		$quickLink6 = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => '15天未跟进',
				'linkurl' => $this->getListViewUrl().'&public=15daynofollow',
				'linkicon' => '',
		);
		$quickLink7 = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => '30天未跟进',
				'linkurl' => $this->getListViewUrl().'&public=30daynofollow',
				'linkicon' => '',
		);
		$quickLink8 = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => '过期未跟进',
				'linkurl' => $this->getListViewUrl().'&public=exceednofollow',
				'linkicon' => '',
		);
         $quickLink9 = array(
 				'linktype' => 'SIDEBARLINK',
 				'linklabel' => '全部未跟进',
 				'linkurl' => $this->getListViewUrl().'&public=allnofollowday',
 				'linkicon' => '',
 		);
        $quickLink10 = array(
 				'linktype' => 'SIDEBARLINK',
 				'linklabel' => '当天需跟进',
 				'linkurl' => $this->getListViewUrl().'&public=todayneedfollow',
 				'linkicon' => '',
 		);
		$quickLink11 = array(
 				'linktype' => 'SIDEBARLINK',
 				'linklabel' => '当天未跟进',
 				'linkurl' => $this->getListViewUrl().'&public=todaynofollow',
 				'linkicon' => '',
 		);
		$quickLink12 = array(
 				'linktype' => 'SIDEBARLINK',
 				'linklabel' => '超期未跟进',
 				'linkurl' => $this->getListViewUrl().'&public=overnofollow',
 				'linkicon' => '',
 		);

		//Check profile permissions for Dashboards 
		$moduleModel = Vtiger_Module_Model::getInstance('Dashboard');
		$userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		$permission = $userPrivilegesModel->hasModulePermission($moduleModel->getId());
		if($permission) {
			//$parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);
// 			$parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink1);
// 			$parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink2);
// 			$parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink3);
// 			$parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink4);
//			$parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink5);
//			$parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink6);
//			$parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink7);
//			$parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink8);
//            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink9);

            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink10);
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink11);
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink12);
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
	
	
		if (!stristr($recordModel->getId(),'N')){
			$focus->id = $recordModel->getId();
		}
	
		$focus->save($moduleName);
	
		return $recordModel->setId($focus->id);
	}
}
