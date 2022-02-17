<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class TelStatistics_Save_Action extends Vtiger_Save_Action {


	public function process(Vtiger_Request $request) {

        $startdate=$request->get('startdate');
        $startdate=strtotime($startdate);
        $enddate=$request->get('enddate');
        $enddate=strtotime($enddate);
        $datetime=time();
        if(($startdate-$datetime)<-600 || $enddate-$startdate<1500){
            //提单时间减当前时间大于10分钟内或结束时间减开始时间小于25分钟
            echo '<style type="text/css">@-webkit-keyframes appear{from{opacity:0}to{opacity:1}}@-webkit-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-webkit-keyframes contentappear{from{-webkit-transform:scale(0);opacity:0}50%{-webkit-transform:scale(.5);opacity:0}to{-webkit-transform:scale(1);opacity:1}}@-moz-keyframes appear{from{opacity:0}to{opacity:1}}@-moz-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-moz-keyframes contentappear{from{-moz-transform:scale(0);opacity:0}50%{-moz-transform:scale(.5);opacity:0}to{-moz-transform:scale(1);opacity:1}}*{margin:0;padding:0}a:active{position:relative;top:1px}html{-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover}body{width:auto;margin:0 auto 100px auto}.header{position:fixed;top:0;width:100%;height:55px;padding:0 0 0 10px;color:#fff;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border-top:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 0 13px #000;z-index:99;-webkit-animation:1s appear;-moz-animation:1s appear}p.error{color:#000;text-shadow:#fff 0 1px 0;text-align:center;font:900 25em helvetica neue;-webkit-animation:2s headline_appear_animation;-moz-animation:2s headline_appear_animation}.content{margin:auto;padding:30px 40px 40px 40px;width:570px;color:#fff;-webkit-animation:2s contentappear;-moz-animation:2s contentappear;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 3px 8px #000;border-radius:6px;font:16px;line-height:25px;font-weight:300;text-shadow:#000 0 1px 0}.content h2{text-transform:uppercase;text-align:center;padding-bottom:20px}form{height:40px}.inputform{font:12px;border:none;padding:10px;width:300px;margin:15px 0 0 75px}.button{width:100px;margin-top:1px;height:33px;border:none;text-shadow:#fff 0 1px 0;background-image:-moz-linear-gradient(top,#fff,#aaa);background-image:-o-linear-gradient(top,#fff,#aaa);background-image:-webkit-linear-gradient(top,#fff,#aaa);background-image:linear-gradient(top,#fff,#aaa);box-shadow:inset 0 1px rgba(255,255,255,1)}.button:hover{background-image:-moz-linear-gradient(top,#fff,#ccc);background-image:-o-linear-gradient(top,#fff,#ccc);background-image:-webkit-linear-gradient(top,#fff,#ccc);background-image:linear-gradient(top,#fff,#ccc);cursor:pointer}.button:active{background-image:-moz-linear-gradient(top,#ccc,#fff);background-image:-o-linear-gradient(top,#ccc,#fff);background-image:-webkit-linear-gradient(top,#ccc,#fff);background-image:linear-gradient(top,#ccc,#fff)}p.links{margin:24px 0 0 0;text-align:center}p.links a{color:#fff;margin-left:15px;margin-right:15px}p.links a:hover{text-decoration:none;text-shadow:#fff 0 0 5px;-webkit-transition:all ease-in .3s;-moz-transition:all ease-in .3s}</style><p>&nbsp;</p><div class="content"><h2>拒绝操作</h2><p class="text">当前提交的时间已过期,请重新提交!!</p><p class="links"><a id="button" href="javascript:history.go(-1);">返回</a></p></div>';
            exit;
        }
		$recordModel = $this->saveRecord($request);
        $related_to = $request->get('related_to'); //客户id
        global $adb,$current_user;
		//过滤掉商务主管这个节点
        function findreport($reportsModel){
            $reportsModel = Users_Privileges_Model::getInstanceById($reportsModel->reports_to_id);
            if($reportsModel->roleid !='H81'){
                return $reportsModel->id;
            }
            return findreport($reportsModel);
        }
        $reports_to_id = findreport($current_user);
        $query="UPDATE `vtiger_visitingorder` SET auditorid='{$reports_to_id}',extractid=? WHERE visitingorderid=?";
        $adb->pquery($query,array($current_user->id,$recordModel->getId()));
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
		$extractid = $request->get('extractid');

		$visitingorderid = $recordModel->getId();
		$tt_record = $request->get('record');
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
						$t_sql[] = " (null, $visitingorderid, $value,'陪同人','1','') ";
						$t_sql[] = " (null, $visitingorderid, $value,'陪同人','2','') ";
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
						$sql = "delete from vtiger_visitsign where visitingorderid=? AND userid in (". implode(',', $t_userid_arr) .")";
						$adb->pquery($sql, array($visitingorderid, $value));
					}
				} else {
					$t_sql = array();
					foreach($accompany as $value) {
						$t_sql[] = " (null, $visitingorderid, $value,'陪同人','1','') ";
						$t_sql[] = " (null, $visitingorderid, $value,'陪同人','2','') ";
					}
					if (count($t_sql) > 0) {
						$sql = "insert into vtiger_visitsign (visitsignid, visitingorderid, userid, visitsigntype,signnum,coordinate) VALUES " . implode(',', $t_sql);
						$adb->pquery($sql, array());
					}
				}
					/*$sql = "delete from vtiger_visitsign where visitingorderid=? AND visitsigntype=?";
					$adb->pquery($sql, array($visitingorderid, '陪同人'));*/
			} else {
				//添加
				$sql = "insert into vtiger_visitsign (visitsignid, visitingorderid, userid, visitsigntype,signnum,coordinate) value (null, $visitingorderid, $extractid,'提单人','1','')";
				$adb->pquery($sql, array());

				//添加
				$sql = "insert into vtiger_visitsign (visitsignid, visitingorderid, userid, visitsigntype,signnum,coordinate) value (null, $visitingorderid, $extractid,'提单人','2','')";
				$adb->pquery($sql, array());

				$t_sql = array();
				foreach($accompany as $value) {
					$t_sql[] = " (null, $visitingorderid, $value,'陪同人','1','') ";
					$t_sql[] = " (null, $visitingorderid, $value,'陪同人','2','') ";
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
				$sql = "insert into vtiger_visitsign (visitsignid, visitingorderid, userid, visitsigntype,signnum,coordinate) value (null, $visitingorderid, $extractid,'提单人', '1', '')";
				$adb->pquery($sql, array());
				$sql = "insert into vtiger_visitsign (visitsignid, visitingorderid, userid, visitsigntype,signnum,coordinate) value (null, $visitingorderid, $extractid,'提单人', '2', '')";
				$adb->pquery($sql, array());
			}
		}

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
            header("Location: $loadUrl");
            $this->getSendWinXinUser($visitingorderid);
        }
	}
    /**
     * 取得当前充值申请单的要提醒的用户信息
     * @param $recordid
     */
    private function getSendWinXinUser($recordid){
        /**
         * 充值申请单的微信消息提醒
         */
        $db=PearDatabase::getInstance();
        //$recordid = $request->get('record');
        $query="SELECT vtiger_salesorderworkflowstages.workflowstagesid,workflowsid,ishigher,higherid,platformids FROM vtiger_salesorderworkflowstages WHERE isaction=1 AND salesorderid= ?
                AND vtiger_salesorderworkflowstages.modulename = 'VisitingOrder'";
        $result=$db->pquery($query,array($recordid));
        $num=$db->num_rows($result);

        if($num){

            $recordModel = Vtiger_Record_Model::getInstanceById($recordid, 'VisitingOrder');
            $entity = $recordModel->entity->column_fields;
            for($j=0;$j<$num;$j++){
                $workflowstagesid=$db->query_result($result,$j,'workflowstagesid');
                $workflowsid=$db->query_result($result,$j,'workflowsid');
                $ishigher=$db->query_result($result,$j,'ishigher');
                $higherid=$db->query_result($result,$j,'higherid');
                if($ishigher==1 && $higherid>0){
                    //有指定的人员审核
                    $query="SELECT vtiger_users.email1 FROM vtiger_users LEFT JOIN vtiger_user2role ON vtiger_users.id=vtiger_user2role.userid WHERE vtiger_users.`status`='Active' AND vtiger_users.id=?";
                    $userresult=$db->pquery($query,array($higherid));
                    $usernum=$db->num_rows($userresult);
                    if($usernum){
                        $email=$db->query_result($userresult,0,'email1');
                        if($this->checkEmail(trim($email))){
                            $content='<div class=\"gray\">'.date('Y年m月d日').'</div><div class=\"normal\">与您相关的拜访单需要审核,目的地为:</div><div class=\"highlight\">'.$entity['destination'].'</div>请及时处理';
                            //$email='steel.liu@71360.com';
                            //$this->setweixincontracts(array('email'=>trim($email),'content'=>$content,'flag'=>6));
                            global $tel_statistics_actions_save_url;
                            $dataurl = $tel_statistics_actions_save_url . '/index.php?module=VisitingOrder&action=detail&record=' . $recordid;
//                            $dataurl='http://m.crm.71360.com/index.php?module=VisitingOrder&action=detail&record='.$recordid;
                            $this->setweixincontracts(array('email'=>trim($email),'description'=>$content,'dataurl'=>$dataurl,'title'=>'拜访单审核','flag'=>7));
                        }
                    }
                }else{
                    /*
                    global $root_directory;
                    //没有指定的人员审核查找该节点对应的角色
                    include $root_directory."crmcache".DIRECTORY_SEPARATOR."workflows".DIRECTORY_SEPARATOR."{$workflowsid}.php";
                    if(!empty($workflows['stage'])) {
                        foreach ($workflows['stage'] as $key=>$value){
                            //查找对应节点的审核角色
                            if($value['workflowstagesid']==$workflowstagesid){
                                if(!empty($value['isrole'])){
                                    $userrole="'";
                                    $userrole.=str_replace(' |##| ',"','",$value['isrole']);
                                    $userrole.="'";
                                    $query="SELECT vtiger_users.email1 FROM vtiger_users LEFT JOIN vtiger_user2role ON vtiger_users.id=vtiger_user2role.userid WHERE vtiger_users.`status`='Active' AND vtiger_user2role.roleid in({$userrole})";
                                    $userresult=$db->pquery($query,array());
                                    $usernum=$db->num_rows($userresult);
                                    if($usernum){
                                        $userstr='';
                                        for($i=0;$i<$usernum;$i++){
                                            $email=$db->query_result($userresult,$i,'email1');
                                            if($this->checkEmail(trim($email))){
                                                $userstr.=trim($email)."|";
                                            }
                                        }
                                        $userstr=rtrim($userstr,'|');
                                        //$userstr='steel.liu@71360.com';
                                        $content='与您相关的拜访单需要审核,目的地为:'.$entity['destination'].',请及时处理';
                                        $this->setweixincontracts(array('email'=>$userstr,'content'=>$content,'flag'=>6));
                                    }
                                }
                            }
                        }
                    }*/
                }
            }
        }
    }
    /**
     * 设置微信企业号上的成员信息
     * @param Vtiger_Request $request
     */
    private function setweixincontracts($data){
        $userkey='c0b3Ke0Q4c%2BmGXycVaQ%2BUEcbU0ldxTBeeMAgUILM0PK5Q59cEp%2B40n6qUSJiPQ';
//        $url = "http://m.crm.71360.com/api.php";
        global $tel_statistics_actions_save_url;
        $url = $tel_statistics_actions_save_url;
        $ch  = curl_init();
        $data['tokenauth']=$userkey;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_exec($ch);
        curl_close($ch);
    }

    /**
     * 邮件格式验证
     * @param $str
     * @return bool
     */
    public function checkEmail($str){
        $str=trim($str);
        $regex = '/^[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@(?:[-_a-z0-9][-_a-z0-9]*\.)*(?:[a-z0-9][-a-z0-9]{0,62})\.(?:(?:[a-z]{2}\.)?[a-z]{2,})$/i';
        if (preg_match($regex, $str)) {
            return true;
        }
        return false;
    }
	
}
