<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class VisitingOrder_Save_Action extends Vtiger_Save_Action {


	public function process(Vtiger_Request $request) {
        $startdate=$request->get('startdate');
        $startdate=strtotime($startdate);
        $enddate=$request->get('enddate');
        $enddate=strtotime($enddate);
        $datetime=time();
        if(($startdate-$datetime)<-600 || $enddate-$startdate<1500){
            //提单时间减当前时间大于10分钟内或结束时间减开始时间小于25分钟
            echo $this->errResult('当前提交的时间已过期,请重新提交!!');
            exit;
        }
        $related_to = $request->get('related_to'); //客户id
	$tt_record = $request->get('record');//拜访单id
        //判断客户id是否来自于选择客户列表
        if(!empty($related_to)){
        	$isChecked = (new Accounts_ListView_Model())->checkVisitingOrderToRelatedId($related_to);
        	if($isChecked <= 0){
        		echo $this->errResult('请选择正确的客户!!');
        		exit;
        	}
        }

		$recordModel = $this->saveRecord($request);

		global $adb,$current_user;
        $reports_to_id=$current_user->reports_to_id;
        /*$reports_to_id = findreport($current_user);*/
$userInfo = getUserInfo($current_user->id);
        if(!empty($userInfo)){
            $reports_to_id = $userInfo['reports_to_id'];
        }

        $sql2 = "select 1 from vtiger_visitingorder where extractid = ? and related_to=? and modulestatus in('c_complete','a_normal')";
        $res = $adb->pquery($sql2,array($current_user->id,$related_to));
	 $destinationcode = $request->get('destinationcode')?$request->get('destinationcode'):$recordModel->get('destinationcode');
        if($adb->num_rows($res)){
            $query="UPDATE vtiger_visitingorder SET auditorid='{$reports_to_id}',extractid=?,destinationcode=? WHERE visitingorderid=?";
        }else{
            $query="UPDATE vtiger_visitingorder SET auditorid='{$reports_to_id}',extractid=?,isfirstvisit=1,destinationcode=? WHERE visitingorderid=?";
        }

        $adb->pquery($query,array($current_user->id,$destinationcode,$recordModel->getId()));
        if($reports_to_id>0){
            $adb->pquery('UPDATE vtiger_salesorderworkflowstages SET higherid=? WHERE salesorderid=? AND modulename=\'VisitingOrder\'',array($reports_to_id,$recordModel->getId()));
        }
		$sql = "select address from vtiger_account where accountid=?";
		$sel_result = $adb->pquery($sql, array($related_to));
		$res_cnt = $adb->num_rows($sel_result);
		if($res_cnt > 0) {
		    $row = $adb->query_result_rowdata($sel_result, 0);
		   	$addressArr = explode('#', $row['address'] );
	    	$customeraddress = implode('', $addressArr);

	    	$relatedRecordId = $recordModel->getId();
	    	$sql = "update vtiger_visitingorder set customeraddress=? where visitingorderid=?";
	    	$adb->pquery($sql, array($customeraddress, $relatedRecordId));
		}

		// 陪同人  //extractid提单人
		$accompany = $request->get('accompany');
		//$extractid = $request->get('extractid');
		$extractid = $current_user->id;

		$visitingorderid = $recordModel->getId();
        /*if($reports_to_id==38){
            $sql='UPDATE vtiger_visitingorder SET modulestatus=\'c_complete\' WHERE visitingorderid=?';
            $adb->pquery($sql,array($visitingorderid));
        }*/

        $request->set('visitingorderid',$visitingorderid);
        $recordModel->addEffectiveVisits($request,$extractid);
        if(empty($tt_record)){
            $sql = "insert into vtiger_visitsign (visitsignid, visitingorderid, userid, visitsigntype,signnum,coordinate) value (null, $visitingorderid, $extractid,'提单人', '1', '')";
            $adb->pquery($sql, array());
            $sql = "insert into vtiger_visitsign (visitsignid, visitingorderid, userid, visitsigntype,signnum,coordinate) value (null, $visitingorderid, $extractid,'提单人', '2', '')";
            $adb->pquery($sql, array());
        }
		if (! empty($accompany)) {
			$t_id_arr = explode(' |##| ', $accompany);
			if (! empty($tt_record)) {
				// 更新
				$sql = "select * from vtiger_visitsign where visitingorderid=? AND  visitsigntype=? group by userid";
				$sel_result = $adb->pquery($sql, array($visitingorderid, '陪同人'));
				$res_cnt = $adb->num_rows($sel_result);

				if($res_cnt > 0) {
					$in_db_arr = array();
					while($rawData = $adb->fetch_array($sel_result)) {
						$in_db_arr[] = $rawData['userid'];
			        }

			        $diff1 = array_diff($accompany, $in_db_arr); // 添加的
			        $diff2 = array_diff($in_db_arr, $accompany); // 删除的

			        $t_sql = array();
			        foreach($diff1 as $value) {
			            if(is_numeric($value) && $value>0 && $value!=$extractid){
                            $t_sql[] = " (null, $visitingorderid, $value,'陪同人','1','') ";
                            $t_sql[] = " (null, $visitingorderid, $value,'陪同人','2','') ";
                        }
					}
					if (count($t_sql) > 0) {
						$sql = "insert into vtiger_visitsign (visitsignid, visitingorderid, userid, visitsigntype,signnum,coordinate) VALUES " . implode(',', $t_sql);
						$adb->pquery($sql, array());
					}

					$t_userid_arr = array();
					foreach($diff2 as $value) {
						$t_userid_arr[] = $value;
					}
					if (count($t_userid_arr) > 0) {
						$sql = "delete from vtiger_visitsign where visitingorderid=? AND  visitsigntype='陪同人' AND userid in (". implode(',', $t_userid_arr) .")";
						$adb->pquery($sql, array($visitingorderid));
					}
				} else {
					$t_sql = array();
					foreach($accompany as $value) {
                        if(is_numeric($value) && $value>0 &&  $value!=$extractid) {
                            $t_sql[] = " (null, $visitingorderid, $value,'陪同人','1','') ";
                            $t_sql[] = " (null, $visitingorderid, $value,'陪同人','2','') ";
                        }
					}
					if (count($t_sql) > 0) {
						$sql = "insert into vtiger_visitsign (visitsignid, visitingorderid, userid, visitsigntype,signnum,coordinate) VALUES " . implode(',', $t_sql);
						$adb->pquery($sql, array());
					}
				}
					/*$sql = "delete from vtiger_visitsign where visitingorderid=? AND visitsigntype=?";
					$adb->pquery($sql, array($visitingorderid, '陪同人'));*/
			} else {

				$t_sql = array();
				foreach($accompany as $value) {
                    if(is_numeric($value) && $value>0 && $value!=$extractid) {
                        $t_sql[] = " (null, $visitingorderid, $value,'陪同人','1','') ";
                        $t_sql[] = " (null, $visitingorderid, $value,'陪同人','2','') ";
                    }
				}
				if (count($t_sql) > 0) {
					$sql = "insert into vtiger_visitsign (visitsignid, visitingorderid, userid, visitsigntype,signnum,coordinate) VALUES " . implode(',', $t_sql);
					$adb->pquery($sql, array());
				}
			}
		} else {
			//没有陪同人
			if (! empty($tt_record)) {  //更新操作
				$sql = "update vtiger_visitingorder set accompany=?  where visitingorderid=?";
				$adb->pquery($sql, array('', $visitingorderid));

				$sql = "delete from vtiger_visitsign where visitingorderid=? AND visitsigntype=?";
				$adb->pquery($sql, array($visitingorderid, '陪同人'));
			} else {   //添加操作
				// 如果是添加的时候 没有陪同人 也要添加到vtiger_visitsign签到表里面

			}
		}
		$adb->pquery("DELETE FROM vtiger_visitsign_mulit WHERE visitingorderid=?",array($visitingorderid));
		$sql='INSERT INTO vtiger_visitsign_mulit(visitingorderid,userid,visitsigntype,signtime,signaddress,issign,signnum,zhsignnum) SELECT visitingorderid,userid,visitsigntype,signtime,signaddress,issign,signnum,if(signnum=1,\'一\',\'二\') FROM vtiger_visitsign WHERE visitingorderid=?';
        $adb->pquery($sql,array($visitingorderid));
		if($request->get('relationOperation')) {		
			$loadUrl = $this->getParentRelationsListViewUrl($request);
		} else if ($request->get('returnToList')) {
			$loadUrl = $recordModel->getModule()->getListViewUrl();
		} else {
			$loadUrl = $recordModel->getDetailViewUrl();
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
            $recordModel->getSendWinXinUser($visitingorderid);
            header("Location: $loadUrl");
            $accompany='';
            $accompanyid=$request->get('accompany');
            if(!empty($accompanyid)){
                $query='SELECT GROUP_CONCAT(last_name) FROM vtiger_users WHERE id in('.generateQuestionMarks($accompanyid).')';
                $result=$adb->pquery($query,$accompanyid);
                if($adb->num_rows($result)) {
                    $data = $adb->raw_query_result_rowdata($result, 0);
                    $accompany=$data[0];
                }
            }
            $users = new Users();
            $report_users = $users->retrieveCurrentUserInfoFromFile($reports_to_id);
            $Subject = '拜访单审核';
            $body='与您相关的拜访单需要审核<br>';
            $body.='<table style="border-collapse: collapse;border:solid 1px #000;color:#666;font-size:12px;">
                    <tr><td style="border:solid 1px #000;font-size:12px;text-align: right;" nowrap="nowrap">主题</td><td style="border:solid 1px #000;text-align:left;color:#000;font-size:14px;" nowrap="nowrap"><a href="http://192.168.1.3/index.php?module=VisitingOrder&view=Detail&record='.$visitingorderid.'" target="_blank" style="text-decoration:none;">'.$request->get('subject').'</a></td></tr>
                    <tr><td style="border:solid 1px #000;font-size:12px;text-align: right;" nowrap="nowrap">客户</td><td style="border:solid 1px #000;text-align:left;color:#000;font-size:14px;" nowrap="nowrap">'.$request->get('related_to_display').'</td></tr>
                    <tr><td style="border:solid 1px #000;font-size:12px;text-align: right;" nowrap="nowrap">目的地</td><td style="border:solid 1px #000;text-align:left;color:#000;font-size:14px;" nowrap="nowrap">'.$request->get('destination').'</td></tr>
                    <tr><td style="border:solid 1px #000;font-size:12px;text-align: right;" nowrap="nowrap">联系人</td><td style="border:solid 1px #000;text-align:left;color:#000;font-size:14px;" nowrap="nowrap">'.$request->get('contacts').'</td></tr>
                    <tr><td style="border:solid 1px #000;font-size:12px;text-align: right;" nowrap="nowrap">拜访目的</td><td style="border:solid 1px #000;text-align:left;color:#000;font-size:14px;" nowrap="nowrap">'.$request->get('purpose').'</td></tr>
                    <tr><td style="border:solid 1px #000;font-size:12px;text-align: right;" nowrap="nowrap">提单人</td><td style="border:solid 1px #000;text-align:left;color:#000;font-size:14px;" nowrap="nowrap">'.$current_user->last_name.'</td></tr>
                    <tr><td style="border:solid 1px #000;font-size:12px;text-align: right;" nowrap="nowrap">陪同人</td><td style="border:solid 1px #000;text-align:left;color:#000;font-size:14px;" nowrap="nowrap">'.$accompany.'</td></tr>
                    <tr><td style="border:solid 1px #000;font-size:12px;text-align: right;" nowrap="nowrap">开始日期</td><td style="border:solid 1px #000;text-align:left;color:#000;font-size:14px;" nowrap="nowrap">'.$request->get('startdate').'</td></tr>
                    <tr><td style="border:solid 1px #000;font-size:12px;text-align: right;" nowrap="nowrap">结束日期</td><td style="border:solid 1px #000;text-align:left;color:#000;font-size:14px;" nowrap="nowrap">'.$request->get('enddate').'</td></tr>
                    <tr><td style="border:solid 1px #000;font-size:12px;text-align: right;" nowrap="nowrap">外出类型</td><td style="border:solid 1px #000text-align:left;color:#000;font-size:14px;" nowrap="nowrap">'.$request->get('outobjective').'</td></tr>
                    <tr><td style="border:solid 1px #000;font-size:12px;text-align: right;" nowrap="nowrap">备注</td><td style="border:solid 1px #000;text-align:left;color:#000;font-size:14px;" nowrap="nowrap">'.$request->get('remark').'</td></tr>
                    </table>';
            $address = array(array('mail' => $report_users->column_fields['email1'], 'name' => $report_users->column_fields['last_name']));
            //$address=array(array('mail'=>'steel.liu@71360.com','name'=>$report_users->column_fields['last_name']));
            Vtiger_Record_Model::sendMail($Subject, $body, $address,'ERP系统');
        }
	}

	private function errResult($msg)
	{
		return '<style type="text/css">@-webkit-keyframes appear{from{opacity:0}to{opacity:1}}@-webkit-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-webkit-keyframes contentappear{from{-webkit-transform:scale(0);opacity:0}50%{-webkit-transform:scale(.5);opacity:0}to{-webkit-transform:scale(1);opacity:1}}@-moz-keyframes appear{from{opacity:0}to{opacity:1}}@-moz-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-moz-keyframes contentappear{from{-moz-transform:scale(0);opacity:0}50%{-moz-transform:scale(.5);opacity:0}to{-moz-transform:scale(1);opacity:1}}*{margin:0;padding:0}a:active{position:relative;top:1px}html{-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover}body{width:auto;margin:0 auto 100px auto}.header{position:fixed;top:0;width:100%;height:55px;padding:0 0 0 10px;color:#fff;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border-top:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 0 13px #000;z-index:99;-webkit-animation:1s appear;-moz-animation:1s appear}p.error{color:#000;text-shadow:#fff 0 1px 0;text-align:center;font:900 25em helvetica neue;-webkit-animation:2s headline_appear_animation;-moz-animation:2s headline_appear_animation}.content{margin:auto;padding:30px 40px 40px 40px;width:570px;color:#fff;-webkit-animation:2s contentappear;-moz-animation:2s contentappear;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 3px 8px #000;border-radius:6px;font:16px;line-height:25px;font-weight:300;text-shadow:#000 0 1px 0}.content h2{text-transform:uppercase;text-align:center;padding-bottom:20px}form{height:40px}.inputform{font:12px;border:none;padding:10px;width:300px;margin:15px 0 0 75px}.button{width:100px;margin-top:1px;height:33px;border:none;text-shadow:#fff 0 1px 0;background-image:-moz-linear-gradient(top,#fff,#aaa);background-image:-o-linear-gradient(top,#fff,#aaa);background-image:-webkit-linear-gradient(top,#fff,#aaa);background-image:linear-gradient(top,#fff,#aaa);box-shadow:inset 0 1px rgba(255,255,255,1)}.button:hover{background-image:-moz-linear-gradient(top,#fff,#ccc);background-image:-o-linear-gradient(top,#fff,#ccc);background-image:-webkit-linear-gradient(top,#fff,#ccc);background-image:linear-gradient(top,#fff,#ccc);cursor:pointer}.button:active{background-image:-moz-linear-gradient(top,#ccc,#fff);background-image:-o-linear-gradient(top,#ccc,#fff);background-image:-webkit-linear-gradient(top,#ccc,#fff);background-image:linear-gradient(top,#ccc,#fff)}p.links{margin:24px 0 0 0;text-align:center}p.links a{color:#fff;margin-left:15px;margin-right:15px}p.links a:hover{text-decoration:none;text-shadow:#fff 0 0 5px;-webkit-transition:all ease-in .3s;-moz-transition:all ease-in .3s}</style><p>&nbsp;</p><div class="content"><h2>拒绝操作</h2><p class="text">'.$msg.'</p><p class="links"><a id="button" href="javascript:history.go(-1);">返回</a></p></div>';
	}
}
