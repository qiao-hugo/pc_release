<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class VisitingOrder_Reference_UIType extends Vtiger_Reference_UIType {

    /**
     * Function to get the Display Value, for the current field type with given DB Insert Value
     * @param <Object> $value
     * @return <Object>
     */
    public function getReferenceModule($value) {
        $fieldModel = $this->get('field');
        $referenceModuleList = $fieldModel->getReferenceList();
        $referenceEntityType = getSalesEntityType($value);
        if(in_array($referenceEntityType, $referenceModuleList)) {
            return Vtiger_Module_Model::getInstance($referenceEntityType);
        } elseif (in_array('Users', $referenceModuleList)) {
            return Vtiger_Module_Model::getInstance('Users');
        } elseif(in_array('Accounts', $referenceModuleList)){
            return 'Accounts';
        }
        return null;
    }
	/**
	 * 编辑下多选显示
	 * @param reference record id
	 * @return link
	 */
	public function getEditViewDisplayValue($value) {
		$values=explode(',',$value);
		$referenceModule = $this->getReferenceModule($values[0]);
		$entityNames=array();
		if($referenceModule=='Accounts'){
		    global $adb;
            $nameResult = $adb->pquery('SELECT label,setype FROM vtiger_crmentity WHERE crmid=?', array($value));
            if($adb->num_rows($nameResult)) {
                return $adb->query_result($nameResult, 0, 'label').'-->['.vtranslate($adb->query_result($nameResult, 0, 'setype')).']';
            }

        }elseif($referenceModule) {
			$referenceModuleName = $referenceModule->get('name');
			foreach ($values as  $val) {
				$entityName= getEntityName($referenceModuleName, array($val));
				$entityNames[]=$entityName[$val];
			}
			return implode('、',$entityNames);
		}
		return '';
	}
    /**
     * Function to get the display value in detail view
     * @param <Integer> crmid of record
     * @return <String>
     */
    public function getDisplayValue($value) {
        $values=explode(',',$value);
        $referenceModule = $this->getReferenceModule($values[0]);
        if($referenceModule=='Accounts'){
            global $adb;
            $nameResult = $adb->pquery('SELECT label,setype FROM vtiger_crmentity WHERE crmid=?', array($value));
            if($adb->num_rows($nameResult)) {
                return " <a href='index.php?module=".$adb->query_result($nameResult, 0, 'setype').'&view=Detail&record='.$value.'\'>'.$adb->query_result($nameResult, 0, 'label').'--['.vtranslate($adb->query_result($nameResult, 0, 'setype')).']</a>';
            }

        }elseif($referenceModule && !empty($value)) {
            $referenceModuleName = $referenceModule->get('name');
            if($referenceModuleName == 'Users') {
                $db = PearDatabase::getInstance();
                $nameResult = $db->pquery('SELECT first_name, last_name FROM vtiger_users WHERE id = ?', array($value));
                if($db->num_rows($nameResult)) {
                    return $db->query_result($nameResult, 0, 'first_name').' '.$db->query_result($nameResult, 0, 'last_name');
                }
            } else {
                $linkValue='';
                foreach ($values as $val) {
                    $entityNames = getEntityName($referenceModuleName, array($val));
                    $linkValue .= " <a href='index.php?module=$referenceModuleName&view=".$referenceModule->getDetailViewName()."&record=$val'
							title='".vtranslate($referenceModuleName, $referenceModuleName)."'>$entityNames[$val]</a> ";
                    //wangbin 合同详细页面产品添加分割符号; 其实所有弹出的多选的详细页面都会受到影响;
                    $linkValue .=";";
                }
                return rtrim($linkValue,';');
            }
        }
        return '';
    }

}