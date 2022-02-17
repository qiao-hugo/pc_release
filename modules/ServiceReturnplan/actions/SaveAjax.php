<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ServiceContracts_SaveAjax_Action extends Vtiger_SaveAjax_Action {

	public function process(Vtiger_Request $request) {

		$recordModel = $this->saveRecord($request);

		$fieldModelList = $recordModel->getModule()->getFields();
		$result = array();
		foreach ($fieldModelList as $fieldName => $fieldModel) {
            $recordFieldValue = $recordModel->get($fieldName);
            if(is_array($recordFieldValue) && $fieldModel->getFieldDataType() == 'FileUpload'){
                $newfldvalue='';
                foreach($recordFieldValue as $key=>$val){
                    if($_POST['attachmentsid'][$key]){
                        $newfldvalue .=$val.'##'.$_POST['attachmentsid'][$key].'*|*';
                    }
                }
                $recordFieldValue=rtrim($newfldvalue,'*|*');
            }
            if(is_array($recordFieldValue) && $fieldModel->getFieldDataType() == 'multipicklist') {
                $recordFieldValue = implode(' |##| ', $recordFieldValue);
            }
			$fieldValue = $displayValue = Vtiger_Util_Helper::toSafeHTML($recordFieldValue);
			if ($fieldModel->getFieldDataType() !== 'currency' && $fieldModel->getFieldDataType() !== 'datetime' && $fieldModel->getFieldDataType() !== 'date') { 
				$displayValue = $fieldModel->getDisplayValue($fieldValue, $recordModel->getId()); 
			}
            if($request->get('field')=='modulestatus'&&$recordModel->entity->column_fields['modulestatus']=='c_complete') {
                $displayValue='<span class="label label-warning">合同已签收不允许修改,刷新后可正常显示</span>';
            }
			$result[$fieldName] = array('value' => $fieldValue, 'display_value' => $displayValue);
		}

		//Handling salutation type
		if ($request->get('field') === 'firstname' && in_array($request->getModule(), array('Contacts', 'Leads'))) {
			$salutationType = $recordModel->getDisplayValue('salutationtype');
			$firstNameDetails = $result['firstname'];
			$firstNameDetails['display_value'] = $salutationType. " " .$firstNameDetails['display_value'];
			if ($salutationType != '--None--') $result['firstname'] = $firstNameDetails;
		}

		$result['_recordLabel'] = $recordModel->getName();
		$result['_recordId'] = $recordModel->getId();

		$response = new Vtiger_Response();
		$response->setEmitType(Vtiger_Response::$EMIT_JSON);
		$response->setResult($result);
		$response->emit();
	}
    public function saveRecord($request) {

        $recordModel = $this->getRecordModelFromRequest($request);
        //print_r($recordModel->entity->column_fields);
        //1当前提状为合同状态并且合同状态不能为完成2提当状态不为合同状态时修改
        if(($request->get('field')=='modulestatus'&&$recordModel->entity->column_fields['modulestatus']!=Workflows_Module_Model::$moudulestatus['c_complete'])||$request->get('field')!='modulestatus'){
            //合成完成后不允许修改
            $recordModel->save();

            //修改合同金额时同但修改对应的工单的金额
            if(($request->get('field')=='total'|| $request->get('field')=='remark') && $recordModel->entity->column_fields['total']!=$request->get('value')){
                ServiceContracts_Record_Model::setSalesordertotal($request->get('record'),$request->get('value'),$request->get('field'));
            }
            //当状态改变后生成提醒
            //1当前字段为合同状态,2合同状态不能为完成3,当前状态和提交状态不一样
            /*if($request->get('field')=='modulestatus'&& $recordModel->entity->column_fields['modulestatus']!=Workflows_Module_Model::$moudulestatus['c_complete'] && $recordModel->entity->column_fields['modulestatus']!=$request->get('value')){
                ServiceContracts_Record_Model::setsalesorderandalert($request->get('value'),$recordModel->entity->column_fields,$request->get('record'));
            }*/
        }
        if($request->get('relationOperation')) {
            $parentModuleName = $request->get('sourceModule');
            $parentModuleModel = Vtiger_Module_Model::getInstance($parentModuleName);
            $parentRecordId = $request->get('sourceRecord');
            $relatedModule = $recordModel->getModule();
            $relatedRecordId = $recordModel->getId();

            $relationModel = Vtiger_Relation_Model::getInstance($parentModuleModel, $relatedModule);
            $relationModel->addRelation($parentRecordId, $relatedRecordId);
        }
        return $recordModel;
    }


}
