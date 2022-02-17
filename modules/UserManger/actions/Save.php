<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
class UserManger_Save_Action extends Vtiger_Save_Action {
    public function saveRecord($request) {
        global $current_user,$UserMangerWorkflowsid,$adb,$UserMangerHeadPersonnelRole,$UserMangerPersonnelRole;
        $record=$request->get('record');
        $invoicecompany=$request->get('invoicecompany');
        $recordModel = $this->getRecordModelFromRequest($request);
        $oldentity=$recordModel->getData();
        if($record>0){
            $reports_to_id=$request->get('reports_to_id');
            $userid=$recordModel->get('userid');
            if($reports_to_id==$userid){
                $this->showMsg('不能将上级设为当前人!');
            }
        }
        $user_name_value=$request->get('user_name');
        if($recordModel->getModule()->checkDuplicateUser('user_name',$user_name_value,$record)){
            $this->showMsg('用户名重复!');
        }
        if(!in_array($current_user->roleid,$UserMangerHeadPersonnelRole) && !in_array($current_user->roleid,$UserMangerPersonnelRole) && $current_user->is_admin!='on'){
            $this->showMsg('当前角色不允许保存,请联系相关人员进行设置!');
        }
        $on_focus = CRMEntity::getInstance('UserManger');
        $query="SELECT oneaudituid,towaudituid,audituid3,audituid5 FROM `vtiger_auditsettings` WHERE department=? AND auditsettingtype='UserManger' LIMIT 1";
        $auditResult=$on_focus->db->pquery($query,array($invoicecompany));
        if($adb->num_rows($auditResult)){
            $auditData=$adb->raw_query_result_rowdata($auditResult,0);
        }else{
            $this->showMsg('请先设置审核相关信息!');
        }
        if($record>0){
            $on_focus->db->pquery('DELETE FROM vtiger_salesorderworkflowstages WHERE salesorderid=? and modulename=\'UserManger\'',array($record));
        }
        $recordModel->save();
        if($auditData['audituid5']==1) {
            //if(empty($record) || $oldentity['modulestatus']=='a_normal'){
            $on_focus->makeWorkflows('UserManger', $UserMangerWorkflowsid, $recordModel->getId(), 'edit');
            //$on_focus->setAudituid("UserManger",$departmentid,$recordModel->getId(),$UserMangerWorkflowsid);
            $sql = "UPDATE `vtiger_usermanger` SET workflowsid=?,modulestatus='b_actioning' WHERE usermangerid=?";
            $adb->pquery($sql, array($UserMangerWorkflowsid, $recordModel->getId()));
            $employeelevel = $request->get('employeelevel');
            if (in_array($current_user->roleid, $UserMangerHeadPersonnelRole)) {
                switch ($employeelevel) {
                    case 'EmployeeLevel':
                    case 'Manageriallevel':
                        $sql = 'DELETE FROM vtiger_salesorderworkflowstages WHERE sequence in(1,3) AND salesorderid=? AND modulename=\'UserManger\'';
                        break;
                    case 'DirectorAndAbove':
                        $sql = 'DELETE FROM vtiger_salesorderworkflowstages WHERE sequence=1 AND salesorderid=? AND modulename=\'UserManger\'';
                        $adb->pquery("UPDATE `vtiger_salesorderworkflowstages` SET ishigher=1,higherid=? WHERE salesorderid=? AND sequence=3 AND modulename='UserManger'",array($auditData['audituid3'],$recordModel->getId()));
                        break;
                }
                $adb->pquery($sql, array($recordModel->getId()));
                $adb->pquery("UPDATE `vtiger_salesorderworkflowstages` SET ishigher=1,higherid=? WHERE salesorderid=? AND sequence=2 AND modulename='UserManger'",array($auditData['towaudituid'],$recordModel->getId()));
                $sql = 'UPDATE vtiger_salesorderworkflowstages SET isaction=1 WHERE sequence=2 AND salesorderid=? AND modulename=\'UserManger\'';
                $adb->pquery($sql, array($recordModel->getId()));
            } elseif (in_array($current_user->roleid, $UserMangerPersonnelRole)) {
                switch ($employeelevel) {
                    case 'EmployeeLevel':
                        $sql = 'DELETE FROM vtiger_salesorderworkflowstages WHERE sequence in(2,3) AND salesorderid=? AND modulename=\'UserManger\'';
                        break;
                    case 'Manageriallevel':
                        $sql = 'DELETE FROM vtiger_salesorderworkflowstages WHERE sequence=3 AND salesorderid=? AND modulename=\'UserManger\'';
                        break;
                }
                $adb->pquery("UPDATE `vtiger_salesorderworkflowstages` SET ishigher=1,higherid=? WHERE salesorderid=? AND sequence=1 AND modulename='UserManger'",array($auditData['oneaudituid'],$recordModel->getId()));
                $adb->pquery("UPDATE `vtiger_salesorderworkflowstages` SET ishigher=1,higherid=? WHERE salesorderid=? AND sequence=2 AND modulename='UserManger'",array($auditData['towaudituid'],$recordModel->getId()));
                if ($employeelevel != 'DirectorAndAbove') {
                    $adb->pquery($sql, array($recordModel->getId()));
                }else{
                    $adb->pquery("UPDATE `vtiger_salesorderworkflowstages` SET ishigher=1,higherid=? WHERE salesorderid=? AND sequence=3 AND modulename='UserManger'",array($auditData['audituid3'],$recordModel->getId()));
                }
            }
        }else{
            $sql = "UPDATE `vtiger_usermanger` SET workflowsid=?,modulestatus='c_complete' WHERE usermangerid=?";
            $adb->pquery($sql, array($UserMangerWorkflowsid, $recordModel->getId()));
            $currentmodule=$request->get('module');
            $currentaction=$request->get('action');
            $currentrecord=$request->get('record');
            $request->set('module','Users');
            $request->set('action','Save');
            if($record>0 && $recordModel->get('userid')>0) {
                $request->set('record', $recordModel->get('userid'));
            }else{
                $request->set('record', '');
                $request->set('is_admin','0');
                $_REQUEST['is_admin']=0;
            }
            $userSaveAction=new Users_Save_Action();
            $userRecordModel=$userSaveAction->saveRecord($request);
            $request->set('module',$currentmodule);
            $request->set('action',$currentaction);
            $request->set('record',$currentrecord);
            $userSaveAction->updateCompanyId($userRecordModel->getId(),$invoicecompany);
            if(empty($record)){
                $sql='UPDATE vtiger_usermanger SET userid=? WHERE usermangerid=?';
                $adb->pquery($sql,array($userRecordModel->getId(),$recordModel->getId()));
            }
        }

        //}
        /*if($record>0 && $oldentity['modulestatus']=='c_complete'){
            $currentmodule=$request->get('module');
            $currentaction=$request->get('action');
            $currentrecord=$request->get('record');
            $request->set('module','Users');
            $request->set('action','Save');
            $request->set('record',$recordModel->get('userid'));
            $userSaveAction=new Users_Save_Action();
            $userSaveAction->saveRecord($request);
            $request->set('module',$currentmodule);
            $request->set('action',$currentaction);
            $request->set('record',$currentrecord);
        }*/
        if($request->get('relationOperation')) {
            $parentModuleName = $request->get('sourceModule');
            $parentModuleModel = Vtiger_Module_Model::getInstance($parentModuleName);
            $parentRecordId = $request->get('sourceRecord');
            $relatedModule = $recordModel->getModule();
            $relatedRecordId = $recordModel->getId();
            $relationModel = Vtiger_Relation_Model::getInstance($parentModuleModel, $relatedModule);
            $relationModel->addRelation($parentRecordId, $relatedRecordId);
        }
        return $recordModel;
    }
    public function showMsg($msg){
        echo '<style type="text/css">@-webkit-keyframes appear{from{opacity:0}to{opacity:1}}@-webkit-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-webkit-keyframes contentappear{from{-webkit-transform:scale(0);opacity:0}50%{-webkit-transform:scale(.5);opacity:0}to{-webkit-transform:scale(1);opacity:1}}@-moz-keyframes appear{from{opacity:0}to{opacity:1}}@-moz-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-moz-keyframes contentappear{from{-moz-transform:scale(0);opacity:0}50%{-moz-transform:scale(.5);opacity:0}to{-moz-transform:scale(1);opacity:1}}*{margin:0;padding:0}a:active{position:relative;top:1px}html{-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover}body{width:auto;margin:0 auto 100px auto}.header{position:fixed;top:0;width:100%;height:55px;padding:0 0 0 10px;color:#fff;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border-top:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 0 13px #000;z-index:99;-webkit-animation:1s appear;-moz-animation:1s appear}p.error{color:#000;text-shadow:#fff 0 1px 0;text-align:center;font:900 25em helvetica neue;-webkit-animation:2s headline_appear_animation;-moz-animation:2s headline_appear_animation}.content{margin:auto;padding:30px 40px 40px 40px;width:570px;color:#fff;-webkit-animation:2s contentappear;-moz-animation:2s contentappear;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 3px 8px #000;border-radius:6px;font:16px;line-height:25px;font-weight:300;text-shadow:#000 0 1px 0}.content h2{text-transform:uppercase;text-align:center;padding-bottom:20px}form{height:40px}.inputform{font:12px;border:none;padding:10px;width:300px;margin:15px 0 0 75px}.button{width:100px;margin-top:1px;height:33px;border:none;text-shadow:#fff 0 1px 0;background-image:-moz-linear-gradient(top,#fff,#aaa);background-image:-o-linear-gradient(top,#fff,#aaa);background-image:-webkit-linear-gradient(top,#fff,#aaa);background-image:linear-gradient(top,#fff,#aaa);box-shadow:inset 0 1px rgba(255,255,255,1)}.button:hover{background-image:-moz-linear-gradient(top,#fff,#ccc);background-image:-o-linear-gradient(top,#fff,#ccc);background-image:-webkit-linear-gradient(top,#fff,#ccc);background-image:linear-gradient(top,#fff,#ccc);cursor:pointer}.button:active{background-image:-moz-linear-gradient(top,#ccc,#fff);background-image:-o-linear-gradient(top,#ccc,#fff);background-image:-webkit-linear-gradient(top,#ccc,#fff);background-image:linear-gradient(top,#ccc,#fff)}p.links{margin:24px 0 0 0;text-align:center}p.links a{color:#fff;margin-left:15px;margin-right:15px}p.links a:hover{text-decoration:none;text-shadow:#fff 0 0 5px;-webkit-transition:all ease-in .3s;-moz-transition:all ease-in .3s}</style><p>&nbsp;</p><div class="content"><h2>拒绝操作</h2><p class="text">'.$msg.'</p><p class="links"><a id="button" href="javascript:history.go(-1);">返回</a></p></div>';
        exit;
    }
}
