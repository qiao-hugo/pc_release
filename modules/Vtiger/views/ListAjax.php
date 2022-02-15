<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Vtiger_ListAjax_View extends Vtiger_List_View {

	function __construct() {
		parent::__construct();
		$this->exposeMethod('getListViewCount');
		$this->exposeMethod('getRecordsCount');
		$this->exposeMethod('getPageCount');
        $this->exposeMethod('getQrcode');
        $this->exposeMethod('getLoginStatus');
	}

	function preProcess(Vtiger_Request $request) {
		return true;
	}

	function postProcess(Vtiger_Request $request) {
		return true;
	}

	function process(Vtiger_Request $request) {
		$mode = $request->get('mode');
		if(!empty($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}
	}
    /**
     * 获取二维码
     * @param Vtiger_Request $request
     */
    public function getQrcode(Vtiger_Request $request){
        $moduleName=$request->get('module');
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
        $status=$request->get("status");
        if(!empty($status)){
            $status=$moduleName.$status;
            $oldip=$_SESSION[$status];
            global $adb;
            $adb->pquery("DELETE FROM vtiger_qrcodelogin WHERE ercode=",array($oldip));
            $ip=getip();
            $ip=str_replace('.','',$ip);
            $ip=$ip+time();
            $_SESSION[$status]=$ip;
            $adb->pquery("insert into vtiger_qrcodelogin(ercode) VALUES(?)",array($ip));
            $qrip=$moduleModel->base64encode($ip);
            $value = 'http://m.crm.71360.com/otherlogin.php?loginid='.$qrip."&mode=".$status;//二维码内容
            $moduleModel->getQRcode($value,2,'L');
        }

    }
    /**
     * 扫码确认
     */
    public function getLoginStatus(Vtiger_Request $request){
        $moduleName=$request->get('module');
        $status=$request->get("status");
        $status=$moduleName.$status;
        $ip=$_SESSION[$status];
        $arr=array("success"=>false);
        if(!empty($ip)){
            global $adb;
            $result=$adb->pquery("SELECT vtiger_qrcodelogin.userid,vtiger_qrcodelogin.`status`,vtiger_users.last_name FROM vtiger_qrcodelogin LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_qrcodelogin.userid WHERE ercode=? limit 1",array($ip));
            if($adb->num_rows($result)) {
                $data = $adb->raw_query_result_rowdata($result);
                if ($data['status'] == 1) {
                    $arr = array("success" => false, 'status' => 1);
                } else if ($data['status'] == 2) {
                    $cstatus='confirm'.$status;
                    $_SESSION[$cstatus]=$data['userid'];
                    $adb->pquery('delete from vtiger_qrcodelogin where ercode=?', array($ip));
                    unset($_SESSION[$status]);
                    $arr = array("success" => true, 'status' => 2,'userid'=>$data['userid'],'username'=>$data['last_name']);
                }
            }else{
                $arr = array("success" => false, 'status' => 3);
            }

        }
        $arr[$status]=$ip;
        echo json_encode($arr);
    }
}