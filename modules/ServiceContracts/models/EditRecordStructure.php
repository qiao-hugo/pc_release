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
 * Vtiger Edit View Record Structure Model
 */
class ServiceContracts_EditRecordStructure_Model extends Vtiger_RecordStructure_Model {

	/**
	 * Function to get the values in stuctured format
	 * @return <array> - values in structure array('block'=>array(fieldinfo));
	 */
	public function getStructure() {

		if(!empty($this->structuredValues)) {
			return $this->structuredValues;
		}

		$values = array();
		$recordModel = $this->getRecord();
		$recordId = $recordModel->getId();
		$moduleModel = $this->getModule();
		$blockModelList = $moduleModel->getBlocks();
		foreach($blockModelList as $blockLabel=>$blockModel) {
			$fieldModelList = $blockModel->getFields();
			
			if (!empty ($fieldModelList)) {
				$values[$blockLabel] = array();
				foreach($fieldModelList as $fieldName=>$fieldModel) {
					if($fieldModel->isEditable()) {
						if(!empty($_REQUEST['record']) ){
							if($fieldName== 'workflowsid' || $fieldModel->table=='vtiger_receivedpayments')
							continue;
						}
						//if($fieldName!= 'workflowsid'){
						
						if($recordModel->get($fieldName) != '') {
							$fieldModel->set('fieldvalue', $recordModel->get($fieldName));
						}else{
							//标准合同时 在“已收回”状态时，自动带出“合同结算条款”的默认值 isstandard
							if($recordModel->get('isstandard') == 0 && $recordModel->get('modulestatus') == 'c_recovered' && (in_array($fieldName,array('settlementtype','settlementclause')))){
								$defaultArr = ['settlementtype'=>'byother','settlementclause'=>'无'];
								$defaultValue = $defaultArr[$fieldName];
								$fieldModel->set('fieldvalue', $defaultValue);
							}else{
								$defaultValue = $fieldModel->getDefaultFieldValue();
								if(!empty($defaultValue) && !$recordId)
									$fieldModel->set('fieldvalue', $defaultValue);
							}

							
						}
						$values[$blockLabel][$fieldName] = $fieldModel;
						//}
					}
				}
			}
		}
		$this->structuredValues = $values;
		return $values;
	}
}