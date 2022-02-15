<?php
/* +*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ******************************************************************************** */

require_once 'include/Webservices/Retrieve.php';
require_once 'include/Webservices/Create.php';
require_once 'include/Webservices/Delete.php';
require_once 'include/Webservices/DescribeObject.php';
require_once 'includes/Loader.php';
vimport ('includes.runtime.Globals');
vimport ('includes.runtime.BaseModel');

function vtws_convertlead($entityvalues, $user) {

	global $adb, $log;
	if (empty($entityvalues['assignedTo'])) {
		$entityvalues['assignedTo'] = vtws_getWebserviceEntityId('Users', $user->id);
	}
	if (empty($entityvalues['transferRelatedRecordsTo'])) {
		$entityvalues['transferRelatedRecordsTo'] = 'Contacts';
	}


	$leadObject = VtigerWebserviceObject::fromName($adb, 'Leads');
	$handlerPath = $leadObject->getHandlerPath();
	$handlerClass = $leadObject->getHandlerClass();

	require_once $handlerPath;

	$leadHandler = new $handlerClass($leadObject, $user, $adb, $log);


	$leadInfo = vtws_retrieve($entityvalues['leadId'], $user);
	$sql = "select converted from vtiger_leaddetails where converted = 1 and leadid=?";
	$leadIdComponents = vtws_getIdComponents($entityvalues['leadId']);
	$result = $adb->pquery($sql, array($leadIdComponents[1]));
	if ($result === false) {
		throw new WebServiceException(WebServiceErrorCode::$DATABASEQUERYERROR,
				vtws_getWebserviceTranslatedString('LBL_' .
						WebServiceErrorCode::$DATABASEQUERYERROR));
	}

	$rowCount = $adb->num_rows($result);
	if ($rowCount > 0) {
        //商机已转换
		throw new WebServiceException(WebServiceErrorCode::$LEAD_ALREADY_CONVERTED,
				"Lead is already converted");
	}
    $query1="SELECT 1 AS ret FROM vtiger_account INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_account.accountid WHERE vtiger_crmentity.label = ? AND vtiger_crmentity.setype = 'Accounts' AND vtiger_crmentity.deleted = 0";
    $label=preg_replace('/\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;/u','',$entityvalues['entities']['Accounts']['accountname']);
    $result1=$adb->pquery($query1,array($label));
    $rowCount1=$adb->num_rows($result1);
    if($rowCount1>0){
        //验证客户重复
        throw new WebServiceException(WebServiceErrorCode::$LEAD_ALREADY_CONVERTED,'客户已存在不允许转化');
    }
    $accountQuery="SELECT 1 FROM vtiger_uniqueaccountname LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_uniqueaccountname.accountid WHERE vtiger_crmentity.deleted=0 AND vtiger_uniqueaccountname.accountname=?";
    $label1=preg_replace('/\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;|&quot;|　|&apos;|&amp;|&lt;|&gt;|&#039;|&ldquo;|&rdquo;|&lsquo;|&rsquo;|&hellip;|\“|\”|\‘|\〉|\〈|\’|\〖|\〗|\【|\】|\、|\·|\……|\…|\——|\＋|\－|\＝|\,|\<|\.|\>|\/|\?|\;|\:|\\\'|\"|\[|\{|\]|\}|\\|\||\`|\~|\!|\@|\#|\$|\%|\^|\\&|\*|\(|\)|\-|\_|\=|\+|\，|\＜|\．|\＞|\／|\？|\；|\：|\＇|\＂|\［|\{|\］|\}|\＼|\｜|\｀|\￣|\！|\＠|\#|\＄|\％|\＾|\＆|\＊|\（|\）|\－|\＿|\＝|\＋|\，|\《|\．|\》|\、|\？|\；|\：|\’|\”|\【|\｛|\】|\｝|\＼|\｜|\·|\～|\！|\＠|\＃|\￥|\％|\……|\…|\＆|\×|\（|\）|\－|\——|\—|\＝|\＋|\，|\《|\。|\》|\、|\？|\；|\：|\‘|\“|\【|\｛|\】|\｝|\、|\||\·|\~|\！|\@|\#|\￥|\%|\……|\…|\&|\*|\（|\）|\-|\——|\=|\+/u','',$label);

    $label1=strtoupper($label1);
    $resultaccount=$adb->pquery($accountQuery,array($label1));
    $rowCountAccount=$adb->num_rows($resultaccount);
    if($rowCountAccount>0){
    	//客户称称重复验证
        throw new WebServiceException(WebServiceErrorCode::$LEAD_ALREADY_CONVERTED,'客户已存在不允许转化');
	}
     $query2="select assigner,smownerid from vtiger_leaddetails LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_leaddetails.leadid where leadid=?";
    $result2=$adb->pquery($query2,array($leadIdComponents[1]));
    $assigner=$adb->query_result($result2,0,'assigner');//分配者
    $smownerid=$adb->query_result($result2,0,'smownerid');//负责人
    if($assigner!=$user->id && $smownerid!=$user->id){
        //当前用户是当前商机的分配者
        throw new WebServiceException(WebServiceErrorCode::$LEAD_ALREADY_CONVERTED,'只有商机的分配者或负责人才能转化客户');
    }
    $entityIds = array();
	$availableModules = array('Accounts', 'Contacts', 'Potentials');

	if (!(($entityvalues['entities']['Accounts']['create']) || ($entityvalues['entities']['Contacts']['create']))) {
		return null;
	}

	foreach ($availableModules as $entityName) {
		if ($entityvalues['entities'][$entityName]['create']) {
			$entityvalue = $entityvalues['entities'][$entityName];
			$entityObject = VtigerWebserviceObject::fromName($adb, $entityvalue['name']);
			$handlerPath = $entityObject->getHandlerPath();
			$handlerClass = $entityObject->getHandlerClass();

			require_once $handlerPath;

			$entityHandler = new $handlerClass($entityObject, $user, $adb, $log);

			$entityObjectValues = array();
			//$entityObjectValues['assigned_user_id'] = $entityvalues['assignedTo'];
			$entityObjectValues = vtws_populateConvertLeadEntities($entityvalue, $entityObjectValues, $entityHandler, $leadHandler, $leadInfo);
            $entityObjectValues['assigned_user_id'] =$entityvalues['assignedTo'];
            $entityObjectValues['accountcategory']='0';
            $entityObjectValues['accountrank']='chan_notv';
			//update potential related to property
            /*
			if ($entityvalue['name'] == 'Potentials') {
				if (!empty($entityIds['Accounts'])) {
					$entityObjectValues['related_to'] = $entityIds['Accounts'];
				}
				if (!empty($entityIds['Contacts'])) {
					$entityObjectValues['contact_id'] = $entityIds['Contacts'];
				}
			}

			//update the contacts relation
			if ($entityvalue['name'] == 'Contacts') {
				if (!empty($entityIds['Accounts'])) {
					$entityObjectValues['account_id'] = $entityIds['Accounts'];
				}
			}
            */

			try {
				$create = true;
				if ($entityvalue['name'] == 'Accounts') {
					$sql = "SELECT vtiger_account.accountid FROM vtiger_account,vtiger_crmentity WHERE vtiger_crmentity.crmid=vtiger_account.accountid AND vtiger_account.accountname=? AND vtiger_crmentity.deleted=0";
					$result = $adb->pquery($sql, array($entityvalue['accountname']));
					if ($adb->num_rows($result) > 0) {
						$entityIds[$entityName] = vtws_getWebserviceEntityId('Accounts', $adb->query_result($result, 0, 'accountid'));
						$create = false;
					}
				}
				if ($create) {
					//$entityRecord = vtws_create($entityvalue['name'], $entityObjectValues, $user);//不走这里直接生成
                    $entityRecord = override_create($entityObjectValues);
					$entityIds[$entityName] = $entityRecord['id'];
				}
			} catch (Exception $e) {
				throw new WebServiceException(WebServiceErrorCode::$UNKNOWNOPERATION,
						$e->getMessage().' : '.$entityvalue['name']);
			}
		}
	}


	try {
		$accountIdComponents = vtws_getIdComponents($entityIds['Accounts']);
		$accountId = $accountIdComponents[1];

		$contactIdComponents = vtws_getIdComponents($entityIds['Contacts']);
		$contactId = $contactIdComponents[1];

		if (!empty($accountId) && !empty($contactId) && !empty($entityIds['Potentials'])) {
			$potentialIdComponents = vtws_getIdComponents($entityIds['Potentials']);
			$potentialId = $potentialIdComponents[1];
			$sql = "insert into vtiger_contpotentialrel values(?,?)";
			$result = $adb->pquery($sql, array($contactId, $potentialIdComponents[1]));
			if ($result === false) {
				throw new WebServiceException(WebServiceErrorCode::$FAILED_TO_CREATE_RELATION,
						"Failed to related Contact with the Potential");
			}
		}

		$transfered = vtws_convertLeadTransferHandler($leadIdComponents, $entityIds, $entityvalues);

		$relatedIdComponents = vtws_getIdComponents($entityIds[$entityvalues['transferRelatedRecordsTo']]);
		vtws_getRelatedActivities($leadIdComponents[1], $accountId, $contactId, $relatedIdComponents[1]);
		vtws_updateConvertLeadStatus($entityIds, $entityvalues['leadId'], $user);
	} catch (Exception $e) {
		foreach ($entityIds as $entity => $id) {
			vtws_delete($id, $user);
		}
		return null;
	}

	return $entityIds;
}

/*
 * populate the entity fields with the lead info.
 * if mandatory field is not provided populate with '????'
 * returns the entity array.
 */

function vtws_populateConvertLeadEntities($entityvalue, $entity, $entityHandler, $leadHandler, $leadinfo) {
	global $adb, $log;
	$column;
	$entityName = $entityvalue['name'];
	$sql = "SELECT * FROM vtiger_convertleadmapping";
	$result = $adb->pquery($sql, array());
	if ($adb->num_rows($result)) {
		switch ($entityName) {
			case 'Accounts':$column = 'accountfid';
				break;
			case 'Contacts':$column = 'contactfid';
				break;
			case 'Potentials':$column = 'potentialfid';
				break;
			default:$column = 'leadfid';
				break;
		}

		$leadFields = $leadHandler->getMeta()->getModuleFields();
		$entityFields = $entityHandler->getMeta()->getModuleFields();
		$row = $adb->fetch_array($result);
		$count = 1;
		do {
			$entityField = vtws_getFieldfromFieldId($row[$column], $entityFields);
			if ($entityField == null) {
				//user doesn't have access so continue.TODO update even if user doesn't have access
				continue;
			}
			$leadField = vtws_getFieldfromFieldId($row['leadfid'], $leadFields);
			if ($leadField == null) {
				//user doesn't have access so continue.TODO update even if user doesn't have access
				continue;
			}
			$leadFieldName = $leadField->getFieldName();
			$entityFieldName = $entityField->getFieldName();
			$entity[$entityFieldName] = $leadinfo[$leadFieldName];
			$count++;
		} while ($row = $adb->fetch_array($result));

		foreach ($entityvalue as $fieldname => $fieldvalue) {
			if (!empty($fieldvalue)) {
				$entity[$fieldname] = $fieldvalue;
			}
		}

		$entity = vtws_validateConvertLeadEntityMandatoryValues($entity, $entityHandler, $leadinfo, $entityName);
	}
	return $entity;
}

function vtws_validateConvertLeadEntityMandatoryValues($entity, $entityHandler, $leadinfo, $module) {

	$mandatoryFields = $entityHandler->getMeta()->getMandatoryFields();
	foreach ($mandatoryFields as $field) {
		if (empty($entity[$field])) {
			$fieldInfo = vtws_getConvertLeadFieldInfo($module, $field);
			if (($fieldInfo['type']['name'] == 'picklist' || $fieldInfo['type']['name'] == 'multipicklist'
				|| $fieldInfo['type']['name'] == 'date' || $fieldInfo['type']['name'] == 'datetime')
				&& ($fieldInfo['editable'] == true)) {
				$entity[$field] = $fieldInfo['default'];
			} else {
				$entity[$field] = '????';
			}
		}
	}
	return $entity;
}

function vtws_getConvertLeadFieldInfo($module, $fieldname) {
	global $adb, $log, $current_user;
	$describe = vtws_describe($module, $current_user);
	foreach ($describe['fields'] as $index => $fieldInfo) {
		if ($fieldInfo['name'] == $fieldname) {
			return $fieldInfo;
		}
	}
	return false;
}

//function to handle the transferring of related records for lead
function vtws_convertLeadTransferHandler($leadIdComponents, $entityIds, $entityvalues) {

	try {
		$entityidComponents = vtws_getIdComponents($entityIds[$entityvalues['transferRelatedRecordsTo']]);
		vtws_transferLeadRelatedRecords($leadIdComponents[1], $entityidComponents[1], $entityvalues['transferRelatedRecordsTo']);
	} catch (Exception $e) {
		return false;
	}

	return true;
}

function vtws_updateConvertLeadStatus($entityIds, $leadId, $user) {
	global $adb, $log;
	$leadIdComponents = vtws_getIdComponents($leadId);
	if ($entityIds['Accounts'] != '' || $entityIds['Contacts'] != '') {
        	$leadModifiedTime = $adb->formatDate(date('Y-m-d H:i:s'), true);
		$sql = "UPDATE vtiger_leaddetails SET converted = 1,assignerstatus='c_transformation',conversiontime=? where leadid=?";
		$result = $adb->pquery($sql, array($leadModifiedTime,$leadIdComponents[1]));
		if ($result === false) {
			throw new WebServiceException(WebServiceErrorCode::$FAILED_TO_MARK_CONVERTED,
					"Failed mark lead converted");
		}
		//updating the campaign-lead relation --Minnie
		$sql = "DELETE FROM vtiger_campaignleadrel WHERE leadid=?";
		$adb->pquery($sql, array($leadIdComponents[1]));

		$sql = "DELETE FROM vtiger_tracker WHERE item_id=?";
		$adb->pquery($sql, array($leadIdComponents[1]));

		//update the modifiedtime and modified by information for the record

		$crmentityUpdateSql = "UPDATE vtiger_crmentity SET modifiedtime=?, modifiedby=? WHERE crmid=?";
		$adb->pquery($crmentityUpdateSql, array($leadModifiedTime, $user->id, $leadIdComponents[1]));
	}

    /*$moduleArray = array('Accounts','Contacts','Potentials');
    $moduleArray = array('Accounts');
    //客户表中已把isconvertedfromlead字段去掉了这里不要了
    foreach($moduleArray as $module){
        if(!empty($entityIds[$module])) {
            $idComponents = vtws_getIdComponents($entityIds[$module]);

            $id = $idComponents[1];
            $webserviceModule = vtws_getModuleHandlerFromName($module, $user);

            $meta = $webserviceModule->getMeta();

            $fields = $meta->getModuleFields();

            $field = $fields['isconvertedfromlead'];
            $tablename = $field->getTableName();
            $tableList = $meta->getEntityTableIndexList();
            $tableIndex = $tableList[$tablename];
            $adb->pquery("UPDATE $tablename SET isconvertedfromlead = ? WHERE $tableIndex = ?",array(1,$id));
        }
    }*/

}
/**
*直接生成客户
**/
function override_create($element){
	global $adb;
    $assigned_user_id=vtws_getIdComponents($element['assigned_user_id']);
    $element['assigned_user_id']=$assigned_user_id[1];
    $_REQUES=$element;
    $request=new Vtiger_Request($_REQUES, $_REQUES);
    $request->set('module','Accounts');
    $request->set('view','Edit');
    $request->set('action','Save');
    $ressorder=new Vtiger_Save_Action();
    $res=$ressorder->saveRecord($request);
    $recordModel=Vtiger_Record_Model::getInstanceById($res->getId(),'Accounts');
    $entity=$recordModel->entity->column_fields;
    $entity['id']='11x'.$entity['record_id'];
    $entity['assigned_user_id']='19x'.$entity['assigned_user_id'];//保留扩展用
    $entity['modifiedby']='19x'.$entity['modifiedby'];//保留扩展用
    $label=preg_replace('/\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;|&quot;|　|&apos;|&amp;|&lt;|&gt;|&#039;|&ldquo;|&rdquo;|&lsquo;|&rsquo;|&hellip;|\“|\”|\‘|\〉|\〈|\’|\〖|\〗|\【|\】|\、|\·|\……|\…|\——|\＋|\－|\＝|\,|\<|\.|\>|\/|\?|\;|\:|\\\'|\"|\[|\{|\]|\}|\\|\||\`|\~|\!|\@|\#|\$|\%|\^|\\&|\*|\(|\)|\-|\_|\=|\+|\，|\＜|\．|\＞|\／|\？|\；|\：|\＇|\＂|\［|\{|\］|\}|\＼|\｜|\｀|\￣|\！|\＠|\#|\＄|\％|\＾|\＆|\＊|\（|\）|\－|\＿|\＝|\＋|\，|\《|\．|\》|\、|\？|\；|\：|\’|\”|\【|\｛|\】|\｝|\＼|\｜|\·|\～|\！|\＠|\＃|\￥|\％|\……|\…|\＆|\×|\（|\）|\－|\——|\—|\＝|\＋|\，|\《|\。|\》|\、|\？|\；|\：|\‘|\“|\【|\｛|\】|\｝|\、|\||\·|\~|\！|\@|\#|\￥|\%|\……|\…|\&|\*|\（|\）|\-|\——|\=|\+/u','',$entity['accountname']);
    $label=strtoupper($label);
    $adb->pquery('INSERT INTO vtiger_uniqueaccountname(accountid,accountname) VALUES(?,?)',array($entity['record_id'],$label));
    return $entity;
}

?>
