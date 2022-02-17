<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_Picklist_Index_View extends Settings_Vtiger_Index_View {
    
    public function process(Vtiger_Request $request) {
        
        $sourceModule = $request->get('source_module');
        $pickListSupportedModules = Settings_Picklist_Module_Model::getPicklistSupportedModules();//获取module
        if(empty($sourceModule)) {
            //take the first module as the source module
            $sourceModule = $pickListSupportedModules[0]->name;
        }
        //获取实例化的Settings_Picklist_Module_Model.
        $moduleModel = Settings_Picklist_Module_Model::getInstance($sourceModule);
        
        
        $viewer = $this->getViewer($request);
        $qualifiedName = $request->getModule(FALSE);
        
        $viewer->assign('PICKLIST_MODULES',$pickListSupportedModules);
        
        //@TODO: see if you needs to optimize this , since its will gets all the fields and filter picklist fields
        //这里是返回了所有的，不是一个好方法，要么缓存，要么优化
        $pickListFields = $moduleModel->getFieldsByType(array('picklist','multipicklist'));
        
        
        if(count($pickListFields) > 0) {
            $selectedPickListFieldModel = reset($pickListFields);	//默认的第一条数据,不用[0]是因为他不会引起错误
			
            $selectedFieldAllPickListValues = Vtiger_Util_Helper::getPickListValues($selectedPickListFieldModel->getName());//根据下拉选项的名字来找到对应的表读取数据
            
            
            $viewer->assign('PICKLIST_FIELDS',$pickListFields);
            $viewer->assign('SELECTED_PICKLIST_FIELDMODEL',$selectedPickListFieldModel);
            $viewer->assign('SELECTED_PICKLISTFIELD_ALL_VALUES',$selectedFieldAllPickListValues);
            $viewer->assign('ROLES_LIST', Settings_Roles_Record_Model::getAll());
        }else{
        	
            $viewer->assign('NO_PICKLIST_FIELDS',true);
            $createPicklistUrl = '';
            $settingsLinks = $moduleModel->getSettingLinks();
            foreach($settingsLinks as $linkDetails) {
                if($linkDetails['linklabel'] == 'LBL_EDIT_FIELDS') {
                    $createPicklistUrl = $linkDetails['linkurl'];
                    break;
                }
            }
            $viewer->assign('CREATE_PICKLIST_URL',$createPicklistUrl);
                
        }
        $viewer->assign('SELECTED_MODULE_NAME', $sourceModule);
        $viewer->assign('QUALIFIED_NAME',$qualifiedName);
        
		$viewer->view('Index.tpl',$qualifiedName);
    }
	
	function getHeaderScripts(Vtiger_Request $request) {
		$headerScriptInstances = parent::getHeaderScripts($request);
		$moduleName = $request->getModule();

		$jsFileNames = array(
			"modules.$moduleName.resources.$moduleName",
		);

		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
	}
}