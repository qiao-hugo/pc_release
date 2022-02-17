<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Vtiger_RoleTree_UIType extends Vtiger_Base_UIType {

	/**
	 * Function to get the Template name for the current UI Type object
	 * @return <String> - Template Name
	 */
	public function getTemplateName() {
		return 'uitypes/RoleTree.tpl';
	}

	/**
	 * Function to get the Display Value, for the current field type with given DB Insert Value
	 * @param 多选
	 * @return <Object>
	 */
	public function getDisplayValue($value) {
		$fieldvalues=$this->get('field')->getspecialPicklistValues();
        if(!is_array($value)){
            $value = explode(' |##| ', $value);
        }
        
        if(!empty( $value)){
        	foreach ( $value as $key=>$val){
        		$value[$key]=str_replace(array('|','—'), '', $fieldvalues[$val]);
        	}
        	
        }
		return strip_tags(implode(', ', $value));
	}
    
    public function getDBInsertValue($value) {
		if(is_array($value)){
            $value = implode(' |##| ', $value);
        }
        return $value;
	}
}