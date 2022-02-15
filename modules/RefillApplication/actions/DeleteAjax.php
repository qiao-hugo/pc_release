<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class RefillApplication_DeleteAjax_Action extends Vtiger_Delete_Action {

	public function process(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$recordId = $request->get('record');

		//销账删除
        global $current_user;
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, 'RefillApplication');
        $refillapplicationno = $recordModel->get('refillapplicationno');
        $userid = $current_user->id;

        $params = [
            'code'=>$refillapplicationno,
            "updateName"=> $current_user->user_name,
	        "updateUser"=> $current_user->id
        ];
        $params = json_encode($params);
        $url = "http://prein-gw.71360.com/write-off/recharge/deleteRechargeByRechargeCode";
        $header = array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "userId:".$userid
        ));

        $contractsRecordModel=Vtiger_Record_Model::getCleanInstance('RefillApplication');
        $res = $contractsRecordModel->https_requestcomm($url,$params,$header,true);
        $resData = json_decode($res, true);
        if ($resData['code'] != '0') {
            $this->log->debug("销账删除失败，params->" . $params);
            throw new AppException('销账删除失败！');
        }

		$recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
		$recordModel->delete();

		$cvId = $request->get('viewname');
		$response = new Vtiger_Response();
		$response->setResult(array('viewname'=>$cvId, 'module'=>$moduleName));
		$response->emit();
	}
}
