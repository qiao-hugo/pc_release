<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

Class Users_EditAjax_View extends Vtiger_IndexAjax_View {

	function __construct() {
		parent::__construct();
		$this->exposeMethod('changePassword');
	}

	public function process(Vtiger_Request $request) {
		$mode = $request->get('mode');
		if (!empty($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}
	}

	public function changePassword(Vtiger_Request $request) {
		$viewer = $this->getViewer($request);
		$moduleName = $request->get('module');
		$userId = $request->get('recordId');

		$viewer->assign('MODULE', $moduleName);
		$viewer->assign('USERID', $userId);
        global $current_user,$adb;
        $result=$adb->pquery('SELECT id FROM `vtiger_user2setting` WHERE FIND_IN_SET(?,userid) AND FIND_IN_SET(1,setting)',array($current_user->id));
        $OLDPWD=true;
        if($adb->num_rows($result)>0){
            $OLDPWD=false;
        }
        $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('OLDPWD',$OLDPWD);
		$viewer->view('ChangePassword.tpl', $moduleName);
	}

}