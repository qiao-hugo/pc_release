<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
vimport('~~/vtlib/Vtiger/Module.php');

/**
 * Vtiger Module Model Class
 */
class ServiceTask_Module_Model extends Vtiger_Module_Model {
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
			
			//任务包id设置
			if ($fieldName =='taskpackageid'){
				$focus->column_fields[$fieldName]=$_REQUEST['sourceRecord'];
			}
		}
		
		$focus->mode = $recordModel->get('mode');
		
		
		$focus->id = $recordModel->getId();
		
		
		$focus->save($moduleName);
		
		return $recordModel->setId($focus->id);
	}
}
