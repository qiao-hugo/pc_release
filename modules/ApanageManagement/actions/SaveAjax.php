<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
class ApanageManagement_SaveAjax_Action extends Vtiger_Save_Action {
	function __construct() {
		parent::__construct();
		$this->exposeMethod('autofillvisitingorder');
	}
	
	public function checkPermission(Vtiger_Request $request) {
		return ;
	}
	public function process(Vtiger_Request $request) {
        global $current_user;
        $recordId=$request->get('record');
        if($request->get('field')=='ucityratio' || $request->get('field')=='ucityname'){
            do {
                if(!Users_Privileges_Model::isPermitted('ApanageManagement', 'EditView')) {
                    break;
                }
                $db = PearDatabase::getInstance();
                $datetime = date('Y-m-d H:i:s');

                $fileName=$request->get('field');
                $fileValue=$request->get('value');
                $fileValue=trim($fileValue);
                $query='SELECT * FROM `vtiger_ucityname` WHERE ucityname=?';
                $result=$db->pquery($query,array($fileValue));
                if(!$db->num_rows($result)){
                    break;
                }
                $citynameid=$result->fields['usercitynameid'];
                $ucityratio=$result->fields['ucityratio'];
                $query='SELECT 1 FROM vtiger_amanagementrelate WHERE userid=?';
                if($db->num_rows($db->pquery($query,array($recordId)))>0){
                    $sql='UPDATE vtiger_amanagementrelate SET ucityname=?,ucityratio=?,ucitynameid=? WHERE userid=?';
                    $db->pquery($sql,array($fileValue,$ucityratio,$citynameid,$recordId));
                }else{
                    $sql='INSERT INTO `vtiger_amanagementrelate` (`userid`,ucityname,`modifiedtime`,`usmownerid`,ucitynameid,ucityratio) VALUES (?,?,?,?,?,?)';
                    $db->pquery($sql,array($recordId,$fileValue,$datetime,$current_user->id,$citynameid,$ucityratio));
                }
            }while(0);
            $recordModel = Vtiger_Record_Model::getInstanceById($recordId, 'ApanageManagement',true);
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

                $result[$fieldName] = array('value' => $fieldValue, 'display_value' => $displayValue);
            }
            $result['_recordLabel'] = $recordModel->getName();
            $result['_recordId'] = $recordModel->getId();

            $response = new Vtiger_Response();
            $response->setEmitType(Vtiger_Response::$EMIT_JSON);
            $response->setResult($result);
            $response->emit();
            exit;
        }
	}
}
