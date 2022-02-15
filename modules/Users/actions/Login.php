<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Users_Login_Action extends Vtiger_Action_Controller {

	function loginRequired() {
		return false;
	}

	function process(Vtiger_Request $request) {
	
		
		$username = $request->get('username');
		$password = $request->get('password');
		$password=str_replace(md5(getip()),'',$password);
		$password=str_split($password,2);
		$password = '%'.implode('%',$password);
		$password=urldecode($password);
		
		$user = CRMEntity::getInstance('Users');
		$user->column_fields['user_name'] = $username;
		
		if ($user->doLogin($password)) {
			$userinfo=$user->retrieve_user_info($username);
			$userid =$userinfo['id']; //$user->retrieve_user_id($username);
			Vtiger_Session::set('AUTHUSERID', $userid);
			$user->delUserprivileges($userid);
			$user->checkUserprivileges($userid,$user->last_modifiedtime);
			
			$usercode=$userinfo['usercode'];
			$pickname=$userinfo['last_name'];
			/* //登录用户信息文件不存在需要创建
			if(!file_exists('user_privileges/user_privileges_'.$userid.'.php')){
				require_once('modules/Users/CreateUserPrivilegeFile.php');
				createUserPrivilegesfile($userid);
				createUserSharingPrivilegesfile($userid);
			}
			//防止异常的发生
			if($user->last_modifiedtime!=$user_info['usermodifiedtime']){
				require_once('modules/Users/CreateUserPrivilegeFile.php');
				createUserPrivilegesfile($userid);
				createUserSharingPrivilegesfile($userid);
				
			} */
			// For Backward compatability
			// TODO Remove when switch-to-old look is not needed
			$_SESSION['authenticated_user_id'] = $userid;
			$_SESSION['app_unique_key'] = vglobal('application_unique_key');
			$_SESSION['authenticated_user_language'] = vglobal('default_language');
            $_SESSION['userdepartmentid']=$userinfo['departmentid'];
            
            		//Enabled session variable for KCFINDER 
            		$_SESSION['KCFINDER'] = array(); 
            		$_SESSION['KCFINDER']['disabled'] = false; 
            		$_SESSION['KCFINDER']['uploadURL'] = "test/upload"; 
            		$_SESSION['KCFINDER']['uploadDir'] = "test/upload";
			$deniedExts = implode(" ", vglobal('upload_badext'));
			$_SESSION['KCFINDER']['deniedExts'] = $deniedExts;
			$cookie=cookiecode($username.'##'.$userid.'##'.$usercode.'##'.$pickname,'ENCODE');
			setcookie("tlcrm",base64_encode($cookie),NULL,NULL,NULL,NULL,true); 
			
			// End

			//Track the login History
			$moduleModel = Users_Module_Model::getInstance('Users');
			$moduleModel->saveLoginHistory($user->column_fields['user_name']);
			//End
			header("Location: index.php?from=login");
			//header ('Location: index.php?module=Users&parent=Settings&view=SystemSetup');
			exit();
		} else {
			header ('Location: index.php?module=Users&parent=Settings&view=Login&error=1');
			exit;
		}

	}
}
