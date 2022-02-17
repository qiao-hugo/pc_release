<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Class JobAlerts_Edit_View extends Vtiger_Edit_View {
    
	public function process(Vtiger_Request $request) {
		$record = $request->get('record');
		if (!empty($record)){
			//判断是否可以编辑
			$checkResult=JobAlerts_Record_Model::checkEditPermission($request);
			if (!$checkResult){
				throw new AppException('LBL_JOBALERTS_PERMISSION_EDIT_DELETE');
			}
		}
		parent::process($request);
	}
}