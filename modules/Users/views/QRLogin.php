<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Users_QRLogin_View extends Vtiger_View_Controller {

	function loginRequired() {
		return false;
	}
	
	function checkPermission(Vtiger_Request $request) {
		return true;
	}
	
	function preProcess(Vtiger_Request $request, $display = true) {
		$viewer = $this->getViewer($request);
		$viewer->assign('PAGETITLE', $this->getPageTitle($request));
		$viewer->assign('SCRIPTS',$this->getHeaderScripts($request));
		$viewer->assign('STYLES',$this->getHeaderCss($request));
		$viewer->assign('CURRENT_VERSION', vglobal('vtiger_current_version'));
		if($display) {
			$this->preProcessDisplay($request);
		}
	}

	function process (Vtiger_Request $request) {
        if(Vtiger_Session::get('AUTHUSERID')>0){
            header("location:/index.php");
            exit;
        }
        $type=$request->get('type');
        if($type=='QRcode'){
            $this->getQRcode();
            exit;
        }elseif($type=='status'){
            $this->getLoginStatus();
            exit;
        }
        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();
        $viewer->assign('MODULE', $moduleName);
        $viewer->view('QrLogin.tpl', 'Users');
        exit;

	}
	
	function postProcess(Vtiger_Request $request) {
		$viewer = $this->getViewer($request);
		$viewer->view('IndexPostProcess.tpl');
	}

    /**
     * 生成二维码
     */
	public function getQRcode(){
        ob_clean();
        $oldip=$_SESSION["QRcode"];
        global $adb;
        $adb->pquery("DELETE FROM vtiger_qrcodelogin WHERE ercode=",array($oldip));
        $ip=getip();
        $ip=str_replace('.','',$ip);
        $ip=$ip+time();
        $_SESSION["QRcode"]=$ip;
        $adb->pquery("insert into vtiger_qrcodelogin(ercode) VALUES(?)",array($ip));
        include './libraries/qrcode/phpqrcode.php';
        $qrip=$this->base64encode($ip);
        global $users_view_qrlogin_other_login_url;
        $value = $users_view_qrlogin_other_login_url . '?loginid=' . $qrip;//二维码内容
//        $value = 'http://m.crm.71360.com/otherlogin.php?loginid='.$qrip;//二维码内容
        //$value = 'http://192.168.40.188/studentinput.php?loginid='.$qrip;//二维码内容
        //$value = 'http://192.168.40.188/apps/studentinput.php?schoolid='.$schoolid.'&from=qrcode&schoolrecruitid='.$schoolrecruitid.'&schoolname='.$schoolname;//二维码内容
        //echo $value;
        //exit;
        $errorCorrectionLevel = 'L';//容错级别
        $matrixPointSize = 4;//生成图片大小
        //生成二维码图片
        QRcode::png($value, 'qrcode.png', $errorCorrectionLevel, $matrixPointSize, 2);
        $logo = '0.jpg';//准备好的logo图片
        $QR = 'qrcode.png';//已经生成的原始二维码图

        if ($logo !== FALSE) {
            $QR = imagecreatefromstring(file_get_contents($QR));
            $logo = imagecreatefromstring(file_get_contents($logo));
            $QR_width = imagesx($QR);//二维码图片宽度
            $QR_height = imagesy($QR);//二维码图片高度
            $logo_width = imagesx($logo);//logo图片宽度
            $logo_height = imagesy($logo);//logo图片高度
            $logo_qr_width = $QR_width / 5;
            $scale = $logo_width/$logo_qr_width;
            $logo_qr_height = $logo_height/$scale;
            $from_width = ($QR_width - $logo_qr_width) / 2;
            //重新组合图片并调整大小
            imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,
                $logo_qr_height, $logo_width, $logo_height);
        }
        //输出图片
        Header("Content-type: image/png");
        ImagePng($QR);
    }
    /**
     * 数字加密
     * @param $v
     * @return string
     */
    protected function base64encode($v){
        //加入乱字符开始
        $b=str_replace(array(0,1,2,4,5,6,7,8,9,3),array('b','c','a','f','m','n','t','o','x','q'),$v);
        $c=str_replace(array(0,1,2,4,5,6,7,8,9,3),array('ba','cd','af','df','dm','cdn','fdt','sso','ewx','ayq'),$v);
        $a=str_replace(array(0,1,2,4,5,6,7,8,9,3),array('sed','dwe','sss','ddss','derwm','werw','ghjy','ttrosso','ffs','mnbv'),$v);
        $d=md5('AccountsiD');
        $e=md5('Useridstrunlandorgnetcomcn');
        //结束
        return base64_encode($a.$d.$b.$e.$c);
    }

    /**
     * 扫码登陆
     */
    public function getLoginStatus(){
        $ip=$_SESSION["QRcode"];
        $arr=array("success"=>false);
        if(!empty($ip)){
            global $adb;
            $result=$adb->pquery("SELECT userid,`status` FROM vtiger_qrcodelogin WHERE ercode=? limit 1",array($ip));
            if($adb->num_rows($result)) {
                $data = $adb->raw_query_result_rowdata($result);

                if ($data['status'] == 1) {
                    $arr = array("success" => true, 'status' => 1);
                } else if ($data['status'] == 2) {
                    $userid = $data['userid'];
                    $user = CRMEntity::getInstance('Users');
                    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
                    $username = $current_user->user_name;
                    Vtiger_Session::set('AUTHUSERID', $userid);
                    $user->delUserprivileges($userid);
                    $user->checkUserprivileges($userid, $user->last_modifiedtime);
                    $usercode = $current_user->usercode;
                    $pickname = $current_user->last_name;;
                    $_SESSION['authenticated_user_id'] = $userid;
                    $_SESSION['app_unique_key'] = vglobal('application_unique_key');
                    $_SESSION['authenticated_user_language'] = vglobal('default_language');
                    $_SESSION['KCFINDER'] = array();
                    $_SESSION['KCFINDER']['disabled'] = false;
                    $_SESSION['KCFINDER']['uploadURL'] = "test/upload";
                    $_SESSION['KCFINDER']['uploadDir'] = "test/upload";
                    $deniedExts = implode(" ", vglobal('upload_badext'));
                    $_SESSION['KCFINDER']['deniedExts'] = $deniedExts;
                    $cookie = cookiecode($username . '##' . $userid . '##' . $usercode . '##' . $pickname, 'ENCODE');
                    setcookie("tlcrm", base64_encode($cookie), NULL, NULL, NULL, NULL, true);
                    $moduleModel = Users_Module_Model::getInstance('Users');
                    $moduleModel->saveLoginHistory($user->column_fields['user_name']);
                    $adb->pquery('delete from vtiger_qrcodelogin where ercode=?', array($ip));
                    unset($_SESSION["QRcode"]);
                    $arr = array("success" => true, 'status' => 2);
                }
            }else{
                $arr = array("success" => true, 'status' => 3);
            }
            echo json_encode($arr);
        }
    }

    /**
     * 数字解密
     * @param $v
     * @return int|mixed
     */
    protected function base64decode($v){
        $string=base64_decode($v);
        $dd=md5('Useridstrunlandorgnetcomcn');
        $e=explode($dd,$string);
        $ee=md5('AccountsiD');
        $e=explode($ee,$e[0]);
        $f=str_replace(array('b','c','a','f','m','n','t','o','x','q'),array(0,1,2,4,5,6,7,8,9,3),$e[1]);
        $f=(float)$f;
        return $f;
    }
}