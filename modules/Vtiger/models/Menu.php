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
 * Vtiger Menu Model Class
 */
class Vtiger_Menu_Model extends Vtiger_Module_Model {

    /**
     * Static Function to get all the accessible menu models with/without ordering them by sequence
	 * TODO 菜单判断权限显示[列表和弹出]
     * @param <Boolean> $sequenced - true/false
     * @return <Array> - List of Vtiger_Menu_Model instances
     */
    public static function getAll($sequenced = false,$restrictedModulesList = array()) {

        $userPrivModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $restrictedModulesList = array('Emails', 'ProjectMilestone', 'ProjectTask', 'ModComments', 'Rss', 'Portal',
										'Integration', 'PBXManager', 'Dashboard', 'Home', 'vtmessages', 'vttwitter');
        global $current_user;
        $allModules = parent::getAll(array('0','2'));
		$menuModels = array();
        $moduleSeqs = Array();     // tab排序
        $moduleNonSeqs = Array();
        foreach($allModules as $module){
        	if(!$current_user->superadmin){
        		 $menulists=$current_user->viewPermission;
        		 $url_array=explode('=',$module->getDefaultUrl());
        		 $moduleinfo=explode('&',$url_array[1]);
				 if($url_array[2]=='List' || $url_array[2]=='Detail'){
        		 	$url_array[2]='DetailView';
        		 }

        		 if(empty($menulists[$moduleinfo[0].'/'.$url_array[2]])){
        		 	continue;
        		 }	
        	}
            if($module->get('tabsequence') != -1){
                $moduleSeqs[$module->get('tabsequence')] = $module;
            }else {
                $moduleNonSeqs[] = $module;
            }
        }
        ksort($moduleSeqs);
        $modules = array_merge($moduleSeqs, $moduleNonSeqs);  //合并结果

		//权限判断，如果是管理员
		foreach($modules as $module) {
            if (($current_user->superadmin ||
                    $userPrivModel->hasGlobalReadPermission() ||
                    $userPrivModel->hasModulePermission($module->getId()))& !in_array($module->getName(), $restrictedModulesList) && $module->get('parent') != '') {
                    $menuModels[$module->getName()] = $module;

            }
        }
        return $menuModels;
    }

}
