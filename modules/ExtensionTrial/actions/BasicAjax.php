<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ExtensionTrial_BasicAjax_Action extends Vtiger_Action_Controller {
    function __construct() {
        parent::__construct();
        $this->exposeMethod('addExtensionTrial');

    }

    function checkPermission(Vtiger_Request $request) {
        return;
    }


    function addExtensionTrial(Vtiger_Request $request){
        $userid = $request->get('suserid');
        $record = $request->get('srecord');
        $workflowsid=537682; //线上工作流id
        //$workflowsid=397238;
        global $current_user;

        do {
            if ($userid != $current_user->id) {
                break;
            }
            global $adb;
            // 提交申请过几次了
            $sql = "SELECT COUNT(vtiger_extensiontrial.extensiontrialid) AS ex_num FROM vtiger_extensiontrial WHERE vtiger_extensiontrial.servicecontractsid=?";
            $result=$adb->pquery($sql,array($record));
            $row = $adb->query_result_rowdata($result, 0);
            $ex_num = $row['ex_num'];
            if($ex_num == 2) {
                break;
            }
            $_REQUES['record'] = '';
            $_REQUES['servicecontractsid'] = $record;
            $_REQUEST['servicecontractsid']=$record;
            $_REQUES['workflowsid'] = $workflowsid;
            $_REQUES['extensionfrequency'] = $ex_num + 1;   // 审核次数
            $request = new Vtiger_Request($_REQUES, $_REQUES);
            //$request->set('assigned_user_id', $current_user->id); //这个变量是什么 没看懂
            $request->set('module', 'ExtensionTrial');
            $request->set('action', 'SaveAjax');
            $ressorder = new ExtensionTrial_Save_Action();
            $ressorderecord = $ressorder->saveRecord($request);

            $on_focus = CRMEntity::getInstance('ExtensionTrial');
            $on_focus->makeWorkflows('ExtensionTrial', $workflowsid, $ressorderecord->getId(), 'edit');

        }while(0);

        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array());
        $response->emit();
    }

    public function process(Vtiger_Request $request) {
        $mode = $request->getMode();
        if(!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
            return;
        }

    }

}
