<?php
//退出不验证权限
class Users_Logout_Action extends Vtiger_Action_Controller {
	function checkPermission(Vtiger_Request $request) {
		return true;
	}
	function process(Vtiger_Request $request) {
	    global $sso_URL,$site_URL;
	    $vt_param=$_SESSION['vt_param'];
		Vtiger_Session::destroy();
		//退出日志
		$moduleName = $request->getModule();
		$moduleModel = Users_Module_Model::getInstance($moduleName);
		$moduleModel->saveLogoutHistory();
        setcookie("token", "", time() - 3600);
        header("Location: ".$sso_URL."/logout?backUrl=".urlencode($site_URL).'&token='.$vt_param);
        exit;
		//End
		//同步退出
		header ('Location: index.php?from=logout');
	}
}