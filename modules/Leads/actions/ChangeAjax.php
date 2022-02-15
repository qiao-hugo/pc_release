<?php
class Leads_ChangeAjax_Action extends Vtiger_Action_Controller {

	function checkPermission(Vtiger_Request $request) {
		return true;
	}

	public function process(Vtiger_Request $request) {
		$recordId = $request->get('record');
		$voidreason = $request->get('voidreason');
        $act=$request->get('act');

		if(empty($recordId)){
            $result1 = array('success'=>false);
            echo json_encode($result1);
			exit;
		}
       	//数据权限与列表一致
		vglobal('currentView','List');
        $recordModel=Vtiger_Record_Model::getInstanceById($recordId,'Leads');
		//受保护的客户
		global  $current_user,$adb;
        if($act=='LBL_RELATED_LEAD'){
            //强制关联的
//            if((is_admin($current_user) || isPermitted('Leads', 'Merge')=='yes')) {
                $userid=$request->get('userid');
                $accountid=$request->get('accountid');
                //已经转化了
               /* if ($recordModel->entity->column_fields['accountid'] > 0) {
                    $sql = "UPDATE vtiger_account SET frommarketing='LEADS',vtiger_account.accountcategory=0 WHERE  vtiger_account.accountid=?";
                    $adb->pquery($sql, array($recordModel->entity->column_fields['accountid']));
                    $sql = "UPDATE vtiger_crmentity SET vtiger_crmentity.smownerid=? WHERE  vtiger_crmentity.crmid=?";
                    $adb->pquery($sql, array($userid,$recordModel->entity->column_fields['accountid']));
                    $sql = "UPDATE vtiger_leaddetails SET assignerstatus='c_Forced_Related' WHERE leadid=?";
                    $adb->pquery($sql, array($recordModel->entity->column_fields['record_id']));
                    $result1 = array('success' => true);
                } else {
                    //没有转化的
                    $query = "SELECT accountid FROM vtiger_account
                        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_account.accountid
                        LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_crmentity.smownerid
                        LEFT JOIN vtiger_user2department ON vtiger_users.id=vtiger_user2department.userid LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid
                        WHERE vtiger_crmentity.label= ? and  vtiger_crmentity.setype='Accounts' AND vtiger_crmentity.deleted =0 ";
                    $label = preg_replace('/\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;/u', '', $recordModel->entity->column_fields['company']);
                    $result = $adb->pquery($query, array($label));
                    $data = $adb->query_result_rowdata($result);
                    if ($adb->num_rows($result) > 0) {*/
                        //关联商机中的客户
                    $sql = "UPDATE vtiger_leaddetails SET accountid=?,assignerstatus='c_Forced_Related',cluefollowstatus='accounted' WHERE leadid=?";
                    $adb->pquery($sql, array($accountid, $recordModel->entity->column_fields['record_id']));
                    //关联摘
                    $sql = "UPDATE vtiger_account SET vtiger_account.accountcategory=0,vtiger_account.frommarketing=1,vtiger_account.mtime=".time()." WHERE  vtiger_account.accountid=?";
                    $adb->pquery($sql, array($accountid));

                    //更新记录
                    $currentTime = date('Y-m-d H:i:s');
                    $id = $adb->getUniqueId('vtiger_modtracker_basic');
                    $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                        array($id , $recordModel->entity->column_fields['record_id'], 'Leads', $current_user->id, $currentTime, 0));
                    $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                    Array($id, 'accountid', $recordModel->entity->column_fields['accountid'], $accountid));
                    $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                    Array($id, 'assignerstatus', $recordModel->entity->column_fields['assignerstatus'], 'c_Forced_Related'));
                $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                    Array($id, 'cluefollowstatus', $recordModel->entity->column_fields['cluefollowstatus'], 'accounted'));
                    //更新合同
                    $adb->pquery('UPDATE `vtiger_servicecontracts` SET firstfrommarket=1 WHERE firstcontract=1 AND sc_related_to=?',Array($accountid));
                    //更新工单
                    $adb->pquery('UPDATE `vtiger_servicecontracts`,vtiger_salesorder SET vtiger_salesorder.isfrommarkets=1 WHERE vtiger_servicecontracts.firstcontract=1 AND vtiger_servicecontracts.firstfrommarket=1 AND vtiger_salesorder.servicecontractsid=vtiger_servicecontracts.servicecontractsid AND vtiger_servicecontracts.sc_related_to=?',Array($accountid));
                    $query="SELECT * FROM `vtiger_sendmail_leads` WHERE `status`='c_Forced_Related' AND module='Leads'";
                    $resulta=$adb->run_query_allrecords($query);
                    $tarray=array();
                    foreach($resulta as $value)
                    {
                        $tarray['userid'][]=$value['userid'];
                    }
                    $recordModelR=Vtiger_Record_Model::getInstanceById($accountid,'Accounts');
                    $tarray['userid'][]=$recordModelR->entity->column_fields['assigned_user_id'];
                    $tarray['accountname']=$recordModelR->entity->column_fields['accountname'];
                    $tarray['accountid']=$recordModelR->entity->column_fields['record_id'];
                    $tarray['leadsid']=$recordModel->entity->column_fields['record_id'];

                    $leadRecordModel= Leads_Record_Model::getCleanInstance("Leads");
                    $leadRecordModel->sendThemailRELATED($request,$tarray);

                    $result1 = array('success' => true);
                    /*}
                    $result1 = array('success' => true);
                }*/
//            }
        }else if($act=='LBL_cancelled_LEAD'){
            //作废功能
            if((is_admin($current_user) || isPermitted('Leads', 'ConvertLead')=='yes')){
                $adb->pquery("update vtiger_leaddetails set assignerstatus='c_cancelled',voidreason=? WHERE leadid=?",array($voidreason,$recordId));
		$currentTime = date('Y-m-d H:i:s');
                $id = $adb->getUniqueId('vtiger_modtracker_basic');
                $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                    array($id , $recordModel->entity->column_fields['record_id'], 'Leads', $current_user->id, $currentTime, 0));
                $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                    Array($id, 'assignerstatus', $recordModel->entity->column_fields['assignerstatus'], 'c_cancelled'));
            }
            $result1 = array('success'=>true);
        }else if($act=='LBL_Accounts_LEAD'){
            //强制转换显示信息
            $result1 = array('success' => true,'msg'=>"");
            /*if ($recordModel->entity->column_fields['accountid'] > 0) {
                $query = "SELECT accountname,accountcategory AS category,accountrank AS rank,concat(vtiger_users.last_name,'[',IFNULL(vtiger_departments.departmentname,''),']') AS name,vtiger_crmentity.smownerid AS id FROM vtiger_account
                        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_account.accountid
                        LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_crmentity.smownerid
                        LEFT JOIN vtiger_user2department ON vtiger_users.id=vtiger_user2department.userid LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid
                        WHERE vtiger_crmentity.crmid= ? and  vtiger_crmentity.setype='Accounts' AND vtiger_crmentity.deleted =0 ";
                $result=$adb->pquery($query, array($recordModel->entity->column_fields['accountid']));
                $data = $adb->query_result_rowdata($result);
                $data['category']=$data['category']==1?'临时区':($data['category']==2?'公海':'正常');
                $data['rank']=vtranslate($data['rank']);
                $allUser=Leads_Record_Model::selectAllUser();
                $result1 = array('success' => true,'data'=>$data,'users'=>$allUser);
            } else {
                $query = "SELECT accountname,accountcategory AS category,accountrank AS rank,concat(vtiger_users.last_name,'[',IFNULL(vtiger_departments.departmentname,''),']') AS name,vtiger_crmentity.smownerid AS id FROM vtiger_account
                        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_account.accountid
                        LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_crmentity.smownerid
                        LEFT JOIN vtiger_user2department ON vtiger_users.id=vtiger_user2department.userid LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid
                        WHERE vtiger_crmentity.label= ? and  vtiger_crmentity.setype='Accounts' AND vtiger_crmentity.deleted =0 ";
                $label = preg_replace('/\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;/u', '', $recordModel->entity->column_fields['company']);
                $result = $adb->pquery($query, array($label));
                $data = $adb->query_result_rowdata($result);
                if($adb->num_rows($result)>0){
                    $data['category']=$data['category']==1?'临时区':($data['category']==2?'公海':'正常');
                    $data['rank']=vtranslate($data['rank']);
                    $allUser=Leads_Record_Model::selectAllUser();
                    $result1 = array('success' => true,'data'=>$data,'users'=>$allUser);
                }else{
                    $result1 = array('success' => false,'msg'=>"没有找到对应的客户,请确认客户是否存在!!!!");
                }

            }*/
	}elseif($act=='LBL_ACTIVATION_LEAD'){
            //关联商机中的客户
            if((is_admin($current_user) || isPermitted('Leads', 'ConvertLead')=='yes')){
                $sql = "UPDATE vtiger_leaddetails SET assignerstatus='c_allocated' WHERE leadid=?";
                $adb->pquery($sql, array($recordModel->entity->column_fields['record_id']));

                //更新记录
                $currentTime = date('Y-m-d H:i:s');
                $id = $adb->getUniqueId('vtiger_modtracker_basic');
                $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                    array($id , $recordModel->entity->column_fields['record_id'], 'Leads', $current_user->id, $currentTime, 0));
                $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                    Array($id, 'assignerstatus', $recordModel->entity->column_fields['assignerstatus'], 'c_allocated'));
            }
        }

		echo json_encode($result1);
	}
}
