<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class OvertAccounts_CheckDuplicate_Action extends Vtiger_Action_Controller {

	function checkPermission(Vtiger_Request $request) {
		return;
	}

	public function process(Vtiger_Request $request) {
		vglobal('currentView','List');
		$moduleName = $request->getModule();
		$accountName = $request->get('accountname');
        $accountName=trim($accountName,'(');
		$accountName=trim($accountName,')');
		$record = $request->get('record');
		if ($record) {
			$recordModel = Vtiger_Record_Model::getInstanceById($record, $moduleName);
		} else {
			$recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
		}

		//$recordModel->set('accountname', $accountName);

		/* if (!$recordModel->checkDuplicate()) {
			$result = array('success'=>false);
		} else {
			$result = array('success'=>true, 'message'=>vtranslate('LBL_DUPLICATES_EXIST', $moduleName));
		} */
		$recordModel->set('accountname', $accountName);
		$result=$recordModel->checkDuplicate();

        //2016-3-4 wangbin 商机新建跟编辑也需要客户名称，以及商机客户的重复信息
        $fromLeads = $request->get('fromLeads');
        if($fromLeads){
            $leadId = $request->get('leadId');
            $sql ="SELECT leadid FROM vtiger_leaddetails INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_leaddetails.leadid WHERE vtiger_crmentity.label = ? AND vtiger_crmentity.setype = 'Leads'";
            $label=preg_replace('/\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;/u','',$accountName);
            $sql_arr = array($label);
            if($leadId){
                $sql.=" AND vtiger_crmentity.crmid !=?";
                $sql_arr = array($label,$leadId);
            }
            $adb = PearDatabase::getInstance();
            $leadsData = $adb->pquery($sql,$sql_arr);
            $rows = $adb->num_rows($leadsData);   //检查商机名称
            $return_data = array();
            if(!$result && !$rows){ //检查重复项重复
                $return_data = array('isdupli'=>false);
            }else{//客户或商机重复
                $return_data = array('isdupli'=>true);
            }
            $response = new Vtiger_Response();
            $response->setResult($return_data);
            $response->emit();
            die;
        }
        global $data;
        if($data['accountcategory']==2){
            $data['accountcategory']='请到公海里直接领取,原负责人:';
        }else{
            $data['accountcategory']='客户不在公海,请联系负责人:';
        }
        $accuntank=vtranslate($data['accountrank'], $moduleName);
        $name='*** 【'.$data['departmentname'].'】，客户等级：'.$accuntank;
        $str=' '.$name;



		if (!$result) {
			$result = array('success'=>false);
		} elseif($result===3){
			$result = array('success'=>true, 'message'=>vtranslate('请到公海里直接领取，原负责人：', $moduleName).$str);
		}elseif($result===2){
			$result = array('success'=>true, 'message'=>vtranslate('客户不在公海，请联系负责人：', $moduleName).$str);
		}else{
			$result = array('success'=>true, 'message'=>vtranslate('客户不在公海，请联系负责人：', $moduleName).$str);
		}
		$response = new Vtiger_Response();
		$response->setResult($result);
		$response->emit();
	}
}
