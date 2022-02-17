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
 * Vtiger Record Structure Model
 */
class ServiceComments_RecordStructure_Model extends Vtiger_RecordStructure_Model {
	public function getRecordName() {
		return $this->record->getDisplayValue('productid',$this->record->getName());
		//return $this->record->getName();
	}
}