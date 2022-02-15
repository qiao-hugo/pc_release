<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ModComments_SaveAjax_Action extends Vtiger_SaveAjax_Action {

	public function checkPermission(Vtiger_Request $request) {
		/* $moduleName = $request->getModule();
		$record = $request->get('related_to');
		
		//Do not allow ajax edit of existing comments
		if ($record) {
			throw new AppException('LBL_PERMISSION_DENIED');
		} */
		return true;
	}

	public function process(Vtiger_Request $request,$service=false) {
        $db = PearDatabase::getInstance();
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$request->set('creatorid',$currentUserModel->getId());
		$request->set('addtime',date('Y-m-d H:i:s' , time()));

		//客户id设置 gaocl add
		$modulename=$request->get('modulename');
		$accountid=$request->get('accountid');
		$accountintentionality = $request->get('accountintentionality');
		if($modulename != 'Accounts' && !empty($accountid)){
			$request->set('related_to',$request->get('accountid'));
		}

        //2015年9月6日10:25:12 wangbin 如果是客服跟进客户信息,关联客服任务任务包
        $ifupdateservice = $request->get('ifupdateservice');
        $modcommenttype=$request->get('modcommenttype');

        $commentcontentAccount=$commentcontent = $request->get('commentcontent');
        if($modulename=='Accounts' || $modulename=='VisitingOrder'){
            if($modcommenttype=='首次客户录入系统跟进' || $modcommenttype=='首次拜访客户后跟进'){
                $followupdata=$request->get('followupdata');
                $followupdata=array_map(function($v){$noendl=str_replace('#endl#','',$v);$explode=explode('**#**',$noendl);$keynum=$explode[0]+1;return $keynum.'*#*'.$explode[1];},$followupdata);
                $commentcontent=implode('#endl#',$followupdata);
                $followupdataAccount=array_map(function($v){ $explode=explode('*#*',$v);$trimexplode1=trim($explode[1]);if(!empty($trimexplode1)){return $explode[0].','.$explode[1];}else{return '';}},$followupdata);
                $commentcontentAccount=implode(';',$followupdataAccount);
                $commentcontentAccount=$modcommenttype.':'.trim($commentcontentAccount,';');
                $request->set('commentcontent',$commentcontent);
                $request->set('accountintentionality',$accountintentionality);
            }
        }


        $location_url = "";
        if(($modulename=='Accounts' || $modulename=='VisitingOrder') && !empty($accountid) && ($ifupdateservice === 'true'||$ifupdateservice === 'false')){//从前台接受一个参数；
            $qiantai_taskArr = AutoTask_BasicAjax_Action::service_follow($accountid);
           //var_dump($qiantai_taskArr);die;
            if(!empty($qiantai_taskArr)){
                $taskname = $qiantai_taskArr['autoworkflowtaskname'];
                $aftercomment = $commentcontent."<".$taskname.">";
                $taskid = $qiantai_taskArr['autoworkflowtaskentityid'];
                $record = $qiantai_taskArr['autoworkflowentityid'];
                $source_record = $qiantai_taskArr['autoworkflowid'];
                $clidkid = $qiantai_taskArr['autoworkflowtaskid'];
                $request->set('commentcontent',$aftercomment);
                $request->set('autoworkflowtaskentityid',$taskid);
                $request->set('accountintentionality',$accountintentionality);
                if($ifupdateservice === 'true'){
                    $location_url = "index.php?module=AutoTask&view=Detail&record="."$record"."&source_record=".$source_record."&clickid=".$clidkid."&remarkcommen=".$commentcontent;
                  //  header("Location:$location_url");
                    //AutoTask_BasicAjax_Action::closeCurrent_openNext($qiantai_taskArr,$commentcontent);
                }
            }

            //拜访单记录最新跟进时间 和跟进内容
//            $sql2 =  "select visitingorderid from vtiger_visitingorder where related_to = ?  order by starttime desc";
//            $res = $db->pquery($sql2,array($accountid));
//            if($db->num_rows($res)){
//                while ($row = $db->fetch_array($res)){
//                    $visitingorderid[] = $row['visitingorderid'];
//                }
//                $sql = "update vtiger_visitingorder set addtime = ?,commentcontent=? where visitingorderid in (".implode(',',$visitingorderid).")";
//
//                $db->pquery($sql,array(date('Y-m-d H:i:s' , time()),$commentcontent));
//            }
        }
            //die('客服跟进到此结束');
            //end

        if($modulename == 'ServiceComments') {
            //更新客服跟进天数 adatian/2015-07-01 add
            ServiceComments_Record_Model::updateServiceNofollowDay($accountid);
            //客服跟进客户如果有回访任务就要把当前任务添加到跟进表中 wangbin
            $followreturnplainid = $request->get('isfollowplain');//本次回访跟进id;
            if($followreturnplainid>0){

                ServiceComments_Record_Model::updatefollow($followreturnplainid);
                $request->set('commentreturnplanid',$followreturnplainid);
            }
        }

        $recordModel  = $this->saveRecord($request);
        $is_service = $request->get('is_service');//是否是当前客服添加的跟进；
        $modcommentid = $recordModel->get('id');  //跟进id

        //首次拜访客户后跟进 记录面谈的是KP还是非KP
        if($modulename=='Accounts' && $modcommenttype=='首次拜访客户后跟进'){
            $followupdata=$request->get('followupdata');
            $followupdata2=array_map(function($v){$noendl=str_replace('#endl#','',$v);$explode=explode('**#**',$noendl);return $explode[1];},$followupdata);
            if(in_array("非KP",$followupdata2)){
                $iskp=0;
            }else{
                $iskp=1;
            }
            $db->pquery("update vtiger_modcomments set iskp=? where modcommentsid=?",array($iskp,$modcommentid));
        }

        if(!empty($is_service)){
            $update_servicecommnets_sql = "UPDATE vtiger_servicecomments SET modcommentsid=? ,modcomment=?,modcommtnt_time=? WHERE servicecommentsid = 2001";
            $db->pquery($update_servicecommnets_sql,array($modcommentid,$commentcontent,date('Y-m-d H:i:s' , time()),$is_service));
        }
        //更新客户保护天数 gaocl add
		$id = $request->get('related_to');
		/*$select_query="SELECT protectday FROM vtiger_rankprotect WHERE accountrank = (SELECT accountrank FROM vtiger_account WHERE accountid = ?)
				 AND performancerank IN (SELECT performancerank FROM vtiger_salemanager WHERE relatetoid IN (SELECT smownerid FROM vtiger_crmentity WHERE crmid = ?))";
		$result = $db->pquery($select_query, array($id,$id));
		//保护天数
		$protectday = $db->query_result($result, 0,'protectday');
		if(!empty($protectday)){
			//保护天数更新
			$update_query="update vtiger_account set protectday=? where accountid=?";
			$db->pquery($update_query, array($protectday,$id));
		}*/
		//更新客服跟进天数 gaocl/2015-01-16 add
		$moduleid=$request->get('moduleid');//$servicecommentsid
		//if($modulename == 'ServiceComments'){
		//	ServiceComments_Record_Model::updateServiceNofollowDay($moduleid);
		//}

        //更新拜访单跟进 steel /2015-03-04
		if($modulename == 'VisitingOrder'){
			VisitingOrder_Record_Model::updateVisitingOrderFollowstatus($moduleid);
		}
        //更新客跟进后修改CRM表中的修改时间
        if($modulename == 'Accounts' || $modulename=='VisitingOrder'){
                Accounts_Record_Model::updateAccountsStatus($moduleid);
                ServiceComments_Record_Model::updateServiceNofollowDay($accountid);           
                Leads_Record_Model::leadUpdateFllowup($request);
            //$commentcontent=$request->get('commentcontent');
            if(!empty($commentcontentAccount)){
                $accountModel = Vtiger_Record_Model::getInstanceById($moduleid,'Accounts');
                $oldintentionality = $accountModel->get("intentionality");
                if($modulename=='Accounts' && ($oldintentionality!=$accountintentionality)){
                    $sql='UPDATE vtiger_account SET commentcontent=?,intentionality=?,intentionalitydate=\''.date("Y-m-d").'\' WHERE accountid=?';
                }else{
                    $sql='UPDATE vtiger_account SET commentcontent=?,intentionality=? WHERE accountid=?';
                }
                $db->pquery($sql,array($commentcontentAccount,$accountintentionality,$moduleid));
            }
			global $current_user;
            $recordModels=Vtiger_Record_Model::getCleanInstance($modulename);
            $salerank=$recordModels->getSaleRank($current_user->id);
            $results = $db->pquery("SELECT accountrank FROM vtiger_account WHERE accountid = ?", array($id));
            //保护天数
            $accountrank = $db->query_result($results, 0,'accountrank');
            $userinfo = $db->pquery("SELECT u.user_entered,ud.departmentid FROM vtiger_users as u LEFT JOIN vtiger_user2department as ud ON ud.userid=u.id WHERE id = ?", array($current_user->id));
            $departmentid = $db->query_result($userinfo, 0,'departmentid');
            $user_entered = $db->query_result($userinfo, 0,'user_entered');
            $result=$recordModels->getRankDays(array($salerank,$accountrank,$departmentid,$user_entered));
            $recordModels = Vtiger_Record_Model::getInstanceById($moduleid, 'Accounts');
            $entity = $recordModels->entity->column_fields;
            if($modulename == 'Accounts' && $result['isupdate']=='ryes' && $entity['assigned_user_id'] == $current_user->id && !in_array($entity['accountcategory'],array(1,2))){
                $update_query="update vtiger_account set protectday=?,effectivedays=? where accountid=? ";
                $db->pquery($update_query, array($result['protectday'],$result['protectday'],$id));
            }
            //新零售跟进天数设置
            if($modulename == 'Accounts' && $result['isfollow']=='ryes' && $entity['assigned_user_id'] == $current_user->id && !in_array($entity['accountcategory'],array(1,2))){
                $update_query="update vtiger_account set followday=? where accountid=? ";
                $db->pquery($update_query, array($result['followday'],$id));
            }

            //拜访单记录最新跟进时间 和跟进内容
            $sql2 =  "select visitingorderid from vtiger_visitingorder where related_to = ?  order by starttime desc";
            $res = $db->pquery($sql2,array($accountid));
            if($db->num_rows($res)){
                while ($row = $db->fetch_array($res)){
                    $visitingorderid[] = $row['visitingorderid'];
                }
                $sql = "update vtiger_visitingorder set addtime = ?,commentcontent=? where visitingorderid in (".implode(',',$visitingorderid).")";

                //$db->pquery($sql,array(date('Y-m-d H:i:s' , time()),$commentcontent));
                $db->pquery($sql,array(date('Y-m-d H:i:s' , time()),$commentcontentAccount));
            }



        }
        if($modulename == 'Accounts' || $modulename=='OvertAccounts'){
            $result2 = $db->pquery("select accountcategory from vtiger_account where accountid=? limit 1",array($accountid));
            $row2 = $db->fetchByAssoc($result2,0);
            switch ($row2['accountcategory']){
                case 1:
                    $title = '临时区';
                    break;
                case 2:
                    $title = '公海';
                    break;
                default:
                    $title = '';
                    break;
            }

        }
        //对商机跟进后,修改商机的最近跟进时间，和跟进人;
        if($modulename == 'Leads'){
            //这里处理商对商机的一些跟进
            $currentid = $currentUserModel->getId();
            $leadsSmowner_sql = "SELECT smownerid,cluefollowstatus FROM vtiger_crmentity INNER JOIN vtiger_leaddetails ON leadid=crmid WHERE crmid=? AND deleted = ?";
            $adb = PearDatabase::getInstance();
            if($moduleid){
                $LeadsmoResult = $adb->pquery($leadsSmowner_sql,array($moduleid,0));
                $smowneridss = $adb->query_result_rowdata($LeadsmoResult,0);
                $smownerid = $smowneridss['smownerid'];
                if($currentid==$smownerid){
                    $update_comment_sql = 'UPDATE vtiger_leaddetails SET  commenttime = NOW(),modcommentmode=?,cluefollowstatus=? WHERE leadid = ?';
                    if($smowneridss['cluefollowstatus']=='accounted'){
                        $adb->pquery($update_comment_sql,array($request->get('modcommentmode'),'accounted',$moduleid));
                    }else{
                        $adb->pquery($update_comment_sql,array($request->get('modcommentmode'),'bependding',$moduleid));
                    }
                }
            }
            //Leads_Record_Model::updateLeads($moduleid);
        }

        if($modulename == 'SalesDaily') {
            global $current_user;
            $adb = PearDatabase::getInstance();
            $adb->pquery("update vtiger_salesdaily_basic set isguarantee=1,latestreply=? where salesdailybasicid=?",array($commentcontent,$moduleid));
            $salesDailyRecordModel = SalesDaily_Record_Model::getInstanceById($moduleid,$modulename);
            $salesDailyRecordModel->sendWx($commentcontent,$current_user->last_name);
        }


		//$fieldModelList = $recordModel->getModule()->getFields();
		$result = array();
// 		foreach ($fieldModelList as $fieldName => $fieldModel) {
// 			$fieldValue = $recordModel->get($fieldName);
// 			$result[$fieldName] = array('value' => $fieldValue, 'display_value' => $fieldModel->getDisplayValue($fieldValue));
// 		}
		$result['id'] = $recordModel->getId();
		$result['_recordLabel'] = $recordModel->getName();
		$result['_recordId'] = $recordModel->getId();
		#移动app功能用 罗志坚 add
		if($service){
			$result['modcommentid'] = $modcommentid;
			return $result;
		}
		$response = new Vtiger_Response();
		$response->setEmitType(Vtiger_Response::$EMIT_JSON);
		//$response->setResult($result);
        $result = array('location_url'=>$location_url,'accountcategory'=>$title);
		$response->setResult($result);

		$response->emit();
	}
}