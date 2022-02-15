<?php
/*+**********
 * 新增或编辑字段模型
 * 
 ***********/
class Vtiger_EditRecordStructure_Model extends Vtiger_RecordStructure_Model {

	/**
	 * 读取可见字段并赋值
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
		//获取区块列表
		$blockModelList = $moduleModel->getBlocks();
		foreach($blockModelList as $blockLabel=>$blockModel) {
			$fieldModelList = $blockModel->getFields();
			if (!empty ($fieldModelList)) {
				$values[$blockLabel] = array();
				foreach($fieldModelList as $fieldName=>$fieldModel) {
					if($fieldModel->isEditable() && $recordModel->get($fieldName) != 'workflowid') {
						if($recordModel->get($fieldName) != '') {
							$fieldModel->set('fieldvalue', $recordModel->get($fieldName));
						}else{
							$defaultValue = $fieldModel->getDefaultFieldValue();
							if(!empty($defaultValue) && !$recordId)
								$fieldModel->set('fieldvalue', $defaultValue);
						}
						$values[$blockLabel][$fieldName] = $fieldModel;
					}
					if($recordId && $fieldModel->get('editread')){
						//如果是只读的字段编辑时需要可见
						$fieldModel->set('fieldvalue', $recordModel->get($fieldName));
						$values[$blockLabel][$fieldName] = $fieldModel;
					}	
				}
			}
		}
		$this->structuredValues = $values;
		return $values;
	}
	public function getEdit($arr){
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
					if($fieldModel->isEditable() && in_array($fieldName, $arr)) {
						if($recordModel->get($fieldName) != '') {
							$fieldModel->set('fieldvalue', $recordModel->get($fieldName));
						}else{
							$defaultValue = $fieldModel->getDefaultFieldValue();
							if(!empty($defaultValue) && !$recordId)
								$fieldModel->set('fieldvalue', $defaultValue);
						}
						$values[$blockLabel][$fieldName] = $fieldModel;
					}
				}
			}
		}
		return $values;
	}
}