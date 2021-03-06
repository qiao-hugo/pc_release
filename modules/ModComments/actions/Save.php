<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class ModComments_Save_Action extends Vtiger_Save_Action {

	public function process(Vtiger_Request $request) {
		$recordId = $request->get('record');
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		
		//$request->set('creatorid',$currentUserModel->getId());
		//$request->set('addtime',date('Y-m-d H:i:s' , time()));
		//$recordModel = ModComments_Record_Model::getInstanceById($recordId);
		
// 		if(isset($recordModel)){
			
// 			$request->set('modcommenthistory',$recordModel->getHistory().'\n'.date('Y-m-d H:i:s' , time()).'\t'.$currentUserModel->getName().'\t'.$request->get('modcommenthistory').' ');
// 		}

		$this->saveRecord($request);

		$recordModel = ModComments_Record_Model::getInstanceById($recordId);
		$result['success'] = true;
		//$result['modcommenthistory'] = $recordModel->get('modcommenthistory');
		$result['commentcontent'] = $recordModel->get('commentcontent');
		//$result['modifiedtime'] = Vtiger_Util_Helper::formatDateDiffInStrings($recordModel->get('modifiedtime'));
		//$result['modifiedtimetitle'] = Vtiger_Util_Helper::formatDateTimeIntoDayString($recordModel->get('modifiedtime'));

		$response = new Vtiger_Response();
		$response->setEmitType(Vtiger_Response::$EMIT_JSON);
		$response->setResult($result);
		$response->emit();
	}
	
}
