<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
vimport('~~/include/Webservices/ConvertLead.php');

class Leads_SaveConvertLead_View extends Vtiger_View_Controller {

	function checkPermission(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);

		$currentUserPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		/*if(!$currentUserPrivilegesModel->hasModuleActionPermission($moduleModel->getId(), 'ConvertLead')) {
			throw new AppException(vtranslate('LBL_CONVERT_LEAD_PERMISSION_DENIED', $moduleName));
		}*/
        if(!Users_Privileges_Model::isPermitted($moduleModel->getName(), 'ConvertLead', $moduleModel->getId()) || !Users_Privileges_Model::isPermitted($moduleModel->getName(), 'EditView', $moduleModel->getId())) {
            throw new AppException(vtranslate('LBL_CONVERT_LEAD_PERMISSION_DENIED', $moduleName));
        }

    }

	public function preProcess(Vtiger_Request $request) {
	}

	public function process(Vtiger_Request $request) {
        if($request->get('accountname')=='未提供'){
            echo '<<未提供>>的客户名称不充许<a href="javascript:history.go(-1);">返回</a>';
            exit;
        }
		$recordId = $request->get('record');
		$modules = $request->get('modules');
		$assignId = $request->get('assigned_user_id');
        $address[]=$request->get('province');
        $address[]=$request->get('city');
        $address[]=$request->get('area');
        $address[]=$request->get('address');
        $request->set('address',implode('#',$address));
	$accountrecordmodel=Vtiger_Record_Model::getCleanInstance("Accounts");
        $rank='chan_notv';
        $userid=$_REQUEST['assigned_user_id'];
        $salerank=$accountrecordmodel->getSaleRank($userid);
        global  $adb;
        $userinfo =$adb->pquery("SELECT u.user_entered,ud.departmentid FROM vtiger_users as u LEFT JOIN vtiger_user2department as ud ON ud.userid=u.id WHERE id = ?", array($userid));
        $departmentid = $adb->query_result($userinfo, 0,'departmentid');
        $user_entered = $adb->query_result($userinfo, 0,'user_entered');
        $result=$accountrecordmodel->getRankDays(array($salerank,$rank,$departmentid,$user_entered));
    	$_REQUEST['protectday']=$result['protectday'];
		$currentUser = Users_Record_Model::getCurrentUserModel();

		$entityValues = array();

		$entityValues['transferRelatedRecordsTo'] = $request->get('transferModule');
		$entityValues['assignedTo'] = vtws_getWebserviceEntityId(vtws_getOwnerType($assignId), $assignId);
		$entityValues['leadId'] =  vtws_getWebserviceEntityId($request->getModule(), $recordId);

		$recordModel = Vtiger_Record_Model::getInstanceById($recordId, $request->getModule());
        $convertLeadFields = $recordModel->getConvertLeadFields();
		//$availableModules = array('Accounts', 'Contacts', 'Potentials');
		$availableModules = array('Accounts');
		foreach ($availableModules as $module) {
			if(vtlib_isModuleActive($module)&& in_array($module, $modules)) {
				$entityValues['entities'][$module]['create'] = true;
				$entityValues['entities'][$module]['name'] = $module;

				foreach ($convertLeadFields[$module] as $fieldModel) {
					$fieldName = $fieldModel->getName();
					$fieldValue = $request->get($fieldName);

					//Potential Amount Field value converting into DB format
					if ($fieldModel->getFieldDataType() === 'currency') {
						$fieldValue = Vtiger_Currency_UIType::convertToDBFormat($fieldValue);
					} elseif ($fieldModel->getFieldDataType() === 'date') {
						$fieldValue = DateTimeField::convertToDBFormat($fieldValue);
					} elseif ($fieldModel->getFieldDataType() === 'reference' && $fieldValue) {
						$ids = vtws_getIdComponents($fieldValue);
						if (count($ids) === 1) {
							$fieldValue = vtws_getWebserviceEntityId(getSalesEntityType($fieldValue), $fieldValue);
						}
					}
					$entityValues['entities'][$module][$fieldName] = $fieldValue;
				}
			}
		}
        /*echo "<pre>";
        print_r($entityValues);
        echo "</pre>";
        exit;*/
		try {
			$result = vtws_convertlead($entityValues, $currentUser);
		} catch(Exception $e) {
			$this->showError($request, $e);
			exit;
		}

		if(!empty($result['Accounts'])) {
			$accountIdComponents = vtws_getIdComponents($result['Accounts']);
			$accountId = $accountIdComponents[1];
		}
        /*
		if(!empty($result['Contacts'])) {
			$contactIdComponents = vtws_getIdComponents($result['Contacts']);
			$contactId = $contactIdComponents[1];
		}*/

		if(!empty($accountId)) {
            //更新客户表里的是否来自市场部
            Leads_Record_Model::updateAccountMarket($accountId,$recordId);
			header("Location: index.php?view=Detail&module=Accounts&record=$accountId");
		} elseif (!empty($contactId)) {
			header("Location: index.php?view=Detail&module=Contacts&record=$contactId");
		} else {
			$this->showError($request);
			exit;
		}
	}

	function showError($request, $exception=false) {
		$viewer = $this->getViewer($request);
		if($exception != false) {
			$viewer->assign('EXCEPTION', $exception->getMessage());
		}

		$moduleName = $request->getModule();
		$currentUser = Users_Record_Model::getCurrentUserModel();

		$viewer->assign('CURRENT_USER', $currentUser);
		$viewer->assign('MODULE', $moduleName);
		$viewer->view('ConvertLeadError.tpl', $moduleName);
	}
}
