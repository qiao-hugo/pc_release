<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class OvertAccounts_SaveAjax_Action extends Vtiger_SaveAjax_Action {

	public function process(Vtiger_Request $request) {
        global $current_user;
        if($request->get('field')=='accountname' || $request->get('field')=='accountrank'){
            $recordId=$request->get('record');
            $recordModel = Vtiger_Record_Model::getInstanceById($recordId, 'Accounts');
            $entity=$recordModel->entity->column_fields;
            $value=$request->get('value');
            do {

                if ($entity[$request->get('field')] == $value) {
                    break;
                }

                $db = PearDatabase::getInstance();
                $datetime = date('Y-m-d H:i:s');
                if ($request->get('field') == 'accountname') {
                    if(!$recordModel->getsupperaccountupdate('accountname')){
                        break;
                    }
                    $checkDuplicate=$recordModel->checkDuplicate();
                    if (!empty($checkDuplicate)) {
                        break;
                    }
                    $label=str_replace('\\','',$value);
                    $newaccountname=preg_replace('/\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;|&quot;|　|&apos;|&amp;|&lt;|&gt;|&#039;|&ldquo;|&rdquo;|&lsquo;|&rsquo;|&hellip;|\“|\”|\‘|\〉|\〈|\’|\〖|\〗|\【|\】|\、|\·|\……|\…|\——|\＋|\－|\＝|\,|\<|\.|\>|\/|\?|\;|\:|\\\'|\"|\[|\{|\]|\}|\\|\||\`|\~|\!|\@|\#|\$|\%|\^|\\&|\*|\(|\)|\-|\_|\=|\+|\，|\＜|\．|\＞|\／|\？|\；|\：|\＇|\＂|\［|\{|\］|\}|\＼|\｜|\｀|\￣|\！|\＠|\#|\＄|\％|\＾|\＆|\＊|\（|\）|\－|\＿|\＝|\＋|\，|\《|\．|\》|\、|\？|\；|\：|\’|\”|\【|\｛|\】|\｝|\＼|\｜|\·|\～|\！|\＠|\＃|\￥|\％|\……|\…|\＆|\×|\（|\）|\－|\——|\—|\＝|\＋|\，|\《|\。|\》|\、|\？|\；|\：|\‘|\“|\【|\｛|\】|\｝|\、|\||\·|\~|\！|\@|\#|\￥|\%|\……|\…|\&|\*|\（|\）|\-|\——|\=|\+/u','',$label);
                    $newaccountname=strtoupper($newaccountname);
                    $db->pquery('UPDATE vtiger_account SET accountname=? WHERE accountid=?',array($value, $recordId));
                    $db->pquery('UPDATE vtiger_uniqueaccountname SET accountname=? WHERE accountid=?',array($newaccountname,$recordId));
                    $db->pquery('UPDATE vtiger_crmentity SET label=?,modifiedtime=? WHERE crmid=?',array($value,$datetime, $recordId));
                }
                if($request->get('field')=='accountrank'){
                    if(!$recordModel->getsupperaccountupdate('accountrank')){
                        break;
                    }
                    $db->pquery('UPDATE vtiger_account SET accountrank=? WHERE accountid=?',array($value, $recordId));
                }


                $id = $db->getUniqueId('vtiger_modtracker_basic');
                $db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                    array($id, $recordId, 'Accounts', $current_user->id, $datetime, 0));
                $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                    Array($id, $request->get('field'), $entity[$request->get('field')], $value));

                $recordModel = Vtiger_Record_Model::getInstanceById($recordId, 'Accounts');

            }while(0);
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
            exit;
        }
		parent::process($request);
	}


}
