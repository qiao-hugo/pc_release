<?php

/*+*******************************************************************************
 *   The contents of this file are subject to the vtiger CRM Public License Version 1.0
 *   ("License"); You may not use this file except in compliance with the License
 *   The Original Code is:  vtiger CRM Open Source
 *   The Initial Developer of the Original Code is vtiger.
 *   Portions created by vtiger are Copyright (C) vtiger.
 *   All Rights Reserved.
 * 
 *********************************************************************************/

/**
 * @author Musavir Ahmed Khan<musavir at vtiger.com>
 */

/**
 *
 * @param WebserviceId $id
 * @param String $oldPassword
 * @param String $newPassword
 * @param String $confirmPassword
 * @param Users $user 
 * 
 */
function vtws_changePassword($id, $oldPassword, $newPassword, $confirmPassword, $user) {
	vtws_preserveGlobal('current_user',$user);
	$idComponents = vtws_getIdComponents($id);
    $db = PearDatabase::getInstance();
    global $current_user;
    $userid= $current_user->id;
    $result=$db->pquery('SELECT id FROM `vtiger_user2setting` WHERE FIND_IN_SET(?,userid) AND FIND_IN_SET(?,setting)',array($userid,1));
	$flag=false;
	if($db->num_rows($result)>0){
        $flag=true;
	}
    if($idComponents[1] == $user->id || is_admin($user) || $flag) {
		$newUser = new Users();
		$newUser->retrieve_entity_info($idComponents[1], 'Users');
		if(!is_admin($user) && !$flag) {
			if(empty($oldPassword)) {
				throw new WebServiceException(WebServiceErrorCode::$INVALIDOLDPASSWORD, 
					vtws_getWebserviceTranslatedString('LBL_'.
							WebServiceErrorCode::$INVALIDOLDPASSWORD));
			}
			if(!$newUser->verifyPassword($oldPassword)) {
				throw new WebServiceException(WebServiceErrorCode::$INVALIDOLDPASSWORD, 
					vtws_getWebserviceTranslatedString('LBL_'.
							WebServiceErrorCode::$INVALIDOLDPASSWORD));
			}
		}
		if(strcmp($newPassword, $confirmPassword) === 0) {
			$db = PearDatabase::getInstance();
			$db->dieOnError = true;
			$db->startTransaction();
			$success = $newUser->change_password($oldPassword, $newPassword, false,!$flag);
			$error = $db->hasFailedTransaction();
			$db->completeTransaction();
			if($error) {
				throw new WebServiceException(WebServiceErrorCode::$DATABASEQUERYERROR, 
					vtws_getWebserviceTranslatedString('LBL_'.
							WebServiceErrorCode::$DATABASEQUERYERROR));
			}
			if(!$success) {
				throw new WebServiceException(WebServiceErrorCode::$CHANGEPASSWORDFAILURE, 
					vtws_getWebserviceTranslatedString('LBL_'.
							WebServiceErrorCode::$CHANGEPASSWORDFAILURE));
			}
		} else {
			throw new WebServiceException(WebServiceErrorCode::$CHANGEPASSWORDFAILURE, 
					vtws_getWebserviceTranslatedString('LBL_'.
							WebServiceErrorCode::$CHANGEPASSWORDFAILURE));
		}
		VTWS_PreserveGlobal::flush();
		return array('message' => '密码修改成功!!');
	}
}


?>
