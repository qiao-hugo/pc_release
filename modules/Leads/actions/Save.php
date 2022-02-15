<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Leads_Save_Action extends Vtiger_Save_Action {

	public function process(Vtiger_Request $request) {

		//To stop saveing the value of salutation as '--None--'
		$salutationType = $request->get('salutationtype');
		if ($salutationType === '--None--') {
			$request->set('salutationtype', '');
		}
		parent::process($request);
	}
    public function saveRecord($request) {
        $address[]=$request->get('province');
        $address[]=$request->get('city');
        $address[]=$request->get('area');
        $address[]=$request->get('address');
        $request->set('address',implode('#',$address));
        if($request->get('assignerstatus')=='c_allocated'){
            $request->set('allocatetime',date('Y-m-d H:i:s'));
        }
        $accountname = $request->get('company');
        $record = $request->get('record');
        //$return = Accounts_CheckDuplicate_Action::process($request);
        if(!$request->get('mapcreattime')){
            $request->set('mapcreattime',date("Y-m-d H:i:s"));
        }

        $recordModel = $this->getRecordModelFromRequest($request);
        $oldmodulestatus=$recordModel->entity->column_fields['assignerstatus'];//取得未更改之前的ID
        $leadcategroy=$recordModel->entity->column_fields['leadcategroy'];
        $olduserid=$recordModel->entity->column_fields['assigned_user_id'];//原来的负责人
        if($leadcategroy==2){
            if($request->get("isFromMobile")){
                return array("success"=>false,'msg'=>'公海商机不允许编辑','company'=>$request->get("company"));
            }
            echo '公海商机不允许编辑<a href="javascript:history.go(-1);">返回</a>';
            exit;
        }


        $db = PearDatabase::getInstance();
        if($request->get("leadsource")=='SCRM'&&$request->get("locationcity") && !$request->get('assigned_user_id')){
            $LeadRecordModel=Leads_Record_Model::getCleanInstance("Leads");
            $rules = $LeadRecordModel->allocateRules($request->get("locationcity"));
            if(!empty($rules)){
                if($rules['assignnum']==($rules['periodassignnum']+1)){
                    $db->pquery("update vtiger_leadassignpersonnel set periodassignnum=0,period=? where leadassignpersonnelid=?",array(($rules['period']+1),$rules['leadassignpersonnelid']));
                }else{
                    $db->pquery("update vtiger_leadassignpersonnel set periodassignnum=? where leadassignpersonnelid=?",array(($rules['periodassignnum']+1),$rules['leadassignpersonnelid']));
                }
                $recordModel->set('assigned_user_id',$rules['userid']);
                $recordModel->set('cluefollowstatus','tobecontact');
                $recordModel->set('assignerstatus','c_allocated');
                $request->set('assignerstatus','c_allocated');
                $request->set('assigned_user_id',$rules['userid']);
                $request->set('cluefollowstatus','tobecontact');
                global $LEADDEFAULTHOLDER;
                $recordModel->set("assigner",$LEADDEFAULTHOLDER);
                $request->set('assigner',$LEADDEFAULTHOLDER);
            }
        }
        if(!$request->get('assigned_user_id')){
            $request->set('cluefollowstatus','nostatus');
            $request->set('assignerstatus','a_not_allocated');
        }
        if($request->get('assigned_user_id')) {
            $request->set('cluefollowstatus','tobecontact');
            $request->set('assignerstatus','c_allocated');
        }
        $recordModel = $this->getRecordModelFromRequest($request);

        $recordModel->save();
        //保存时状发生改变
        //if($oldmodulestatus!=$request->get('assignerstatus')&&$request->get('assignerstatus')=='c_allocated'){
        if($request->get('assignerstatus')=='c_allocated'){
		$newuserod=$request->get('assigned_user_id');//新的负责人
            if($olduserid != $newuserod){//邮件通知原负责人//负责人更改后才发邮件
                $request->set('olduserid','');
                if(!empty($record)){
                    $request->set('olduserid',$olduserid);
                }
                if(!$request->get('noSend')){
                    $recordModel->sendThemail($request,$recordModel->getId());

                    if($olduserid){
                        $recordModel->sendChangeMailToOldOwner($olduserid,$request->get('company'));
                    }
                }

                global $current_user;
                $sql = "update vtiger_leaddetails set allocatetime=?,cluefollowstatus=?,assigner=?,assignerstatus=? where leadid=?";
                $db->pquery($sql, array(date("Y-m-d H:i:s"), $request->get("cluefollowstatus"),$request->get("assigner"),
                    $request->get("assignerstatus"),$recordModel->getId()));
                $datetime=date('Y-m-d H:i:s');
                $db=PearDatabase::getInstance();
                $id = $db->getUniqueId('vtiger_modtracker_basic');
                $db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                    array($id , $recordModel->getId(), 'Leads', $current_user->id, $datetime, 0));
                $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                    Array($id, 'leadcategroy',2, 0));


            }
        }

        // 分配日期
//        if (empty($record)) {
//            $db = PearDatabase::getInstance();
//            $sql = "update vtiger_leaddetails set allocatetime=? where leadid=?";
//            $db->pquery($sql, array(date("Y-m-d H:i:s"), $recordModel->getId()));
//        }

        if($request->get("isFromMobile")){
            return array("success"=>true,'msg'=>'保存成功','company'=>$request->get("company"));
        }


        return $recordModel;
    }
}
