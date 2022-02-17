<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Leads_Edit_View extends Vtiger_Edit_View {

	public function process(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$recordId = $request->get('record');
        $recordModel = $this->record;
        if(!$recordModel){
            if (!empty($recordId)) {
                $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
            } else {
                $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
            }
        }

		$viewer = $this->getViewer($request);

		$salutationFieldModel = Vtiger_Field_Model::getInstance('salutationtype', $recordModel->getModule());
		$salutationFieldModel->set('fieldvalue', $recordModel->get('salutationtype'));
		$viewer->assign('SALUTATION_FIELD_MODEL', $salutationFieldModel);
		$viewer->assign('ALLUSER', Leads_Record_Model::selectAllUser());
		$viewer->assign('LEADSOURCE', $recordModel->get("leadsource"));
		$viewer->assign('BELONGDEPARTMENTS', $recordModel->getDepartmentsByDepth());
		$viewer->assign('LEADBELONGSYSTEM', $recordModel->get('leadbelongsystem'));
		$viewer->assign('LOCATIONPROVINCE', $recordModel->get("locationprovince"));
		$viewer->assign('SMOWNERID', $recordModel->get("assigned_user_id"));
		$viewer->assign('SELECTREADONLY', array('leadsource','leadsourcetnum','sourcecategory','leadstype','mobile','mapcreattime'));

		parent::process($request);
	}

}