<?php
/**
 * 发票字段格式化（区块=》字段信息）
 */
class Inventory_EditRecordStructure_Model extends Vtiger_EditRecordStructure_Model {
	public function getStructure() {
		if(!empty($this->structuredValues)) {
			return $this->structuredValues;
		}

		$values = array();
		$recordModel = $this->getRecord();
		$recordExists = !empty($recordModel);
        $recordId = $recordModel->getId();
		$moduleModel = $this->getModule();
		$blockModelList = $moduleModel->getBlocks();
		foreach($blockModelList as $blockLabel=>$blockModel) {
			$fieldModelList = $blockModel->getFields();
			if (!empty ($fieldModelList)) {
				$values[$blockLabel] = array();
				foreach($fieldModelList as $fieldName=>$fieldModel) {
					if($fieldModel->isEditable()) {
						if($recordExists) {
							$fieldValue = $recordModel->get($fieldName,null);
                            /* if($fieldName == 'terms_conditions' && $fieldValue == '') {
								$fieldValue = $recordModel->getInventoryTermsandConditions();
							} else  */
							if($fieldValue == '') {
                                $defaultValue = $fieldModel->getDefaultFieldValue();
                                if(!empty($defaultValue) && !$recordId)
                                    $fieldValue = $defaultValue;
                            }
							$fieldModel->set('fieldvalue', $fieldValue);
						}
						$values[$blockLabel][$fieldName] = $fieldModel;
					}
				}
			}
		}
		$this->structuredValues = $values;
		return $values;
	}
}
