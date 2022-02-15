<?php
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
class IdcRecords_Save_Action extends Vtiger_Save_Action {

    public function process(Vtiger_Request $request) {
        $recordModel = $this->saveRecord($request);
        if($request->get('relationOperation')) {
            $loadUrl = $this->getParentRelationsListViewUrl($request);
        } else if ($request->get('returnToList')) {
            $loadUrl = $recordModel->getModule()->getListViewUrl();
        } else {
            $loadUrl = $recordModel->getDetailViewUrl();
        }

        //wangbin 跟新完IDC信息后，跟进客服任务包；
        $adb= PearDatabase::getInstance();
        $salesorderid =$request->get('salesorder_no'); //369176;
        $accountid = $request->get('related_to');
        $current_action_task =  AutoTask_BasicAjax_Action::IDC_follow($accountid);
        $webuptime = $request->get('webuptime');//网站上传时间
        $recordtime = $request->get('recordtime');//备案完成时间
        if($webuptime!==''){
            $sql1 = 'UPDATE vtiger_autoworkflowentitys SET idc_uptime = ? WHERE accountid = ?';
            $adb->pquery($sql1,array($webuptime,$accountid));
        }
        if($recordtime!==''){
            $sql2 = 'UPDATE vtiger_autoworkflowentitys SET idc_completetime = ? WHERE accountid = ?';
            $adb->pquery($sql2,array($recordtime,$accountid));
        }
        if($current_action_task){
            $record = $current_action_task['autoworkflowentityid'];
            $source_record = $current_action_task['autoworkflowid'];
            $clidkid = $current_action_task['autoworkflowtaskid'];
            $location_url = "index.php?module=AutoTask&view=Detail&record="."$record"."&source_record=".$source_record."&clickid=".$clidkid;
            header("Location:$location_url");
            die;
        }

        if(empty($loadUrl)){
            if($request->getHistoryUrl()){
                $loadUrl=$request->getHistoryUrl();
            }else{
                $loadUrl="index.php";
            }
        }
        if($request->isAjax()){

        }else{
            header("Location: $loadUrl");
        }
    }

	public function saveRecord($request){
        //保存前设置域名
        if ($_REQUEST['domainname'] != "") {
            $old_domainname = explode(PHP_EOL,$_REQUEST['domainname']);  //以回车分割成数组
            $domainname=implode(' |##| ',$old_domainname);
            $request->set('domainname', $domainname);
        }
        $domainname=$request->get('domainname');
        $record=$request->get('record');
        $recordModel = $this->getRecordModelFromRequest($request);
        $recordModel->save();
        return $recordModel;
    }
	
}
