<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Schoolcomments_SaveAjax_Action extends Vtiger_SaveAjax_Action {

	public function checkPermission(Vtiger_Request $request) {
		/* $moduleName = $request->getModule();
		$record = $request->get('related_to');
		
		//Do not allow ajax edit of existing comments
		if ($record) {
			throw new AppException('LBL_PERMISSION_DENIED');
		} */
		return true;
	}

	public function process(Vtiger_Request $request,$service=false) {
        $db = PearDatabase::getInstance();
        global $current_user;
		//客户id设置 gaocl add
		$smodcommentpurpose  =  $request->get('smodcommentpurpose');
		$smodcommentcontacts =  $request->get('smodcommentcontacts');
        $scommentcontents    =  $request->get('scommentcontents');
        $smodcommenttype     =  $request->get('smodcommenttype');
        $smodcommentmode     =  $request->get('smodcommentmode');
        $schoolid            =  $request->get('schoolid');
		

		$data = array();
        $data['modcommentsid'] = '';
        $data['commentcontent'] = $scommentcontents;
        $data['related_to'] = $schoolid;
        $data['addtime'] = date('Y-m-d H:i:s');
        $data['creatorid'] = $current_user->id;
        $data['smodcommenttype'] = $smodcommenttype;
        $data['smodcommentmode'] = $smodcommentmode;
        $data['smodcommenthistory'] = '';
        $data['contact_id'] = $smodcommentcontacts;

        $data['modulename'] = 'School';
        $data['moduleid'] = $schoolid;
        $data['smodcommentpurpose'] = $smodcommentpurpose;

        $divideNames = array_keys($data);
        $divideValues = array_values($data);
        $db->pquery('INSERT INTO `vtiger_schoolcomments` ('. implode(',', $divideNames).') VALUES ('. generateQuestionMarks($divideValues) .')',$divideValues);

		$response = new Vtiger_Response();
		//$response->setEmitType(Vtiger_Response::$EMIT_JSON);
		$response->setResult(array());
		$response->emit();
	}
}