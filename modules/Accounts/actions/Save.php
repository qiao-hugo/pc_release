<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Accounts_Save_Action extends Vtiger_Save_Action {



	public function saveRecord($request) {

		$address[]=$request->get('province');
		$address[]=$request->get('city');
		$address[]=$request->get('area');
		$address[]=$request->get('address');
		$record=$request->get('record');
		$isEdit=$request->get('isedit')?$request->get('isedit'):'';
		$request->set('address',implode('#',$address));
        $pcType=$request->get("pcType");
        $province=$request->get('province');
        $annual_revenue=$request->get("annual_revenue");
        $annual_revenue=floatval($annual_revenue);
        $request->set("annual_revenue",$annual_revenue);
        //去掉首尾空格
        if(empty($record)){
            $accountname=$request->get('accountname');
            $accountname=preg_replace('/^(\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;)+|(\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;)+$/u','',$accountname);
            $accountname=trim($accountname);
            $request->set('accountname',$accountname);
        }
        //去掉首尾空格
        $mobile= $request->get('mobile');
        /* 根据地址获取经纬度 start */
        $addressStr = implode('', $address);
        if ($addressStr) {
            $vtiger_Record_Model = new Vtiger_Record_Model();
            $key = 'IYRBZ-LN7AP-NT7DP-VMCCZ-DYTXZ-QMBNZ';
            $url = 'https://apis.map.qq.com/ws/geocoder/v1/?address=' . $addressStr . '&key=' . $key;
            $geocoderRes = $vtiger_Record_Model->https_requestcomm($url, null, null, true);
            if($geocoderRes) {
                $jsonData = json_decode($geocoderRes,true);
                if ($jsonData && isset($jsonData['result']['location'])) {
                    $request->set('longitude', $jsonData['result']['location']['lng']);
                    $request->set('latitude', $jsonData['result']['location']['lat']);
                }
            }
        }
        /* 根据地址获取经纬度 end*/
		$recordModel = $this->getRecordModelFromRequest($request);
        $entity=$recordModel->entity->column_fields;
		//当前登陆人的可以领取的客户数最
		$limit=$recordModel->getRankLimit();
		if(empty($record)){
            $rank='chan_notv';
        }else{
            //$recordModel = Vtiger_Record_Model::getInstanceById($record, 'Accounts');

            $rank=$entity['accountrank'];
	    $accountcurrentuser=$entity['assigned_user_id'];//当前客户的负责人
        }
		if($recordModel->checkDuplicate() && !$isEdit){
            //判断是否是移动端提交添加
            if($pcType==1){
                return array("result"=>'error','status'=>false, 'message'=> "公司名重复");exit();
            }
			//echo '当前客户重复不允许添加<a href="javascript:history.go(-1);">返回</a>';
			echo '<style type="text/css">@-webkit-keyframes appear{from{opacity:0}to{opacity:1}}@-webkit-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-webkit-keyframes contentappear{from{-webkit-transform:scale(0);opacity:0}50%{-webkit-transform:scale(.5);opacity:0}to{-webkit-transform:scale(1);opacity:1}}@-moz-keyframes appear{from{opacity:0}to{opacity:1}}@-moz-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-moz-keyframes contentappear{from{-moz-transform:scale(0);opacity:0}50%{-moz-transform:scale(.5);opacity:0}to{-moz-transform:scale(1);opacity:1}}*{margin:0;padding:0}a:active{position:relative;top:1px}html{-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover}body{width:auto;margin:0 auto 100px auto}.header{position:fixed;top:0;width:100%;height:55px;padding:0 0 0 10px;color:#fff;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border-top:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 0 13px #000;z-index:99;-webkit-animation:1s appear;-moz-animation:1s appear}p.error{color:#000;text-shadow:#fff 0 1px 0;text-align:center;font:900 25em helvetica neue;-webkit-animation:2s headline_appear_animation;-moz-animation:2s headline_appear_animation}.content{margin:auto;padding:30px 40px 40px 40px;width:570px;color:#fff;-webkit-animation:2s contentappear;-moz-animation:2s contentappear;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 3px 8px #000;border-radius:6px;font:16px;line-height:25px;font-weight:300;text-shadow:#000 0 1px 0}.content h2{text-transform:uppercase;text-align:center;padding-bottom:20px}form{height:40px}.inputform{font:12px;border:none;padding:10px;width:300px;margin:15px 0 0 75px}.button{width:100px;margin-top:1px;height:33px;border:none;text-shadow:#fff 0 1px 0;background-image:-moz-linear-gradient(top,#fff,#aaa);background-image:-o-linear-gradient(top,#fff,#aaa);background-image:-webkit-linear-gradient(top,#fff,#aaa);background-image:linear-gradient(top,#fff,#aaa);box-shadow:inset 0 1px rgba(255,255,255,1)}.button:hover{background-image:-moz-linear-gradient(top,#fff,#ccc);background-image:-o-linear-gradient(top,#fff,#ccc);background-image:-webkit-linear-gradient(top,#fff,#ccc);background-image:linear-gradient(top,#fff,#ccc);cursor:pointer}.button:active{background-image:-moz-linear-gradient(top,#ccc,#fff);background-image:-o-linear-gradient(top,#ccc,#fff);background-image:-webkit-linear-gradient(top,#ccc,#fff);background-image:linear-gradient(top,#ccc,#fff)}p.links{margin:24px 0 0 0;text-align:center}p.links a{color:#fff;margin-left:15px;margin-right:15px}p.links a:hover{text-decoration:none;text-shadow:#fff 0 0 5px;-webkit-transition:all ease-in .3s;-moz-transition:all ease-in .3s}</style><p>&nbsp;</p><div class="content"><h2>拒绝操作</h2><p class="text">当前客户名称重复不允许添加!!</p><p class="links"><a id="button" href="javascript:history.go(-1);">返回</a></p></div>';
            exit;
		}
        $current_user_id=$request->get('assigned_user_id');//提交过来的负责人
        if(empty($current_user_id)||!is_numeric($current_user_id)||$current_user_id<=0){
            //echo '当前负责人丢失！<a href="javascript:history.go(-1);">返回</a>';
            /*echo '<style type="text/css">@-webkit-keyframes appear{from{opacity:0}to{opacity:1}}@-webkit-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-webkit-keyframes contentappear{from{-webkit-transform:scale(0);opacity:0}50%{-webkit-transform:scale(.5);opacity:0}to{-webkit-transform:scale(1);opacity:1}}@-moz-keyframes appear{from{opacity:0}to{opacity:1}}@-moz-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-moz-keyframes contentappear{from{-moz-transform:scale(0);opacity:0}50%{-moz-transform:scale(.5);opacity:0}to{-moz-transform:scale(1);opacity:1}}*{margin:0;padding:0}a:active{position:relative;top:1px}html{-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover}body{width:auto;margin:0 auto 100px auto}.header{position:fixed;top:0;width:100%;height:55px;padding:0 0 0 10px;color:#fff;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border-top:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 0 13px #000;z-index:99;-webkit-animation:1s appear;-moz-animation:1s appear}p.error{color:#000;text-shadow:#fff 0 1px 0;text-align:center;font:900 25em helvetica neue;-webkit-animation:2s headline_appear_animation;-moz-animation:2s headline_appear_animation}.content{margin:auto;padding:30px 40px 40px 40px;width:570px;color:#fff;-webkit-animation:2s contentappear;-moz-animation:2s contentappear;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 3px 8px #000;border-radius:6px;font:16px;line-height:25px;font-weight:300;text-shadow:#000 0 1px 0}.content h2{text-transform:uppercase;text-align:center;padding-bottom:20px}form{height:40px}.inputform{font:12px;border:none;padding:10px;width:300px;margin:15px 0 0 75px}.button{width:100px;margin-top:1px;height:33px;border:none;text-shadow:#fff 0 1px 0;background-image:-moz-linear-gradient(top,#fff,#aaa);background-image:-o-linear-gradient(top,#fff,#aaa);background-image:-webkit-linear-gradient(top,#fff,#aaa);background-image:linear-gradient(top,#fff,#aaa);box-shadow:inset 0 1px rgba(255,255,255,1)}.button:hover{background-image:-moz-linear-gradient(top,#fff,#ccc);background-image:-o-linear-gradient(top,#fff,#ccc);background-image:-webkit-linear-gradient(top,#fff,#ccc);background-image:linear-gradient(top,#fff,#ccc);cursor:pointer}.button:active{background-image:-moz-linear-gradient(top,#ccc,#fff);background-image:-o-linear-gradient(top,#ccc,#fff);background-image:-webkit-linear-gradient(top,#ccc,#fff);background-image:linear-gradient(top,#ccc,#fff)}p.links{margin:24px 0 0 0;text-align:center}p.links a{color:#fff;margin-left:15px;margin-right:15px}p.links a:hover{text-decoration:none;text-shadow:#fff 0 0 5px;-webkit-transition:all ease-in .3s;-moz-transition:all ease-in .3s}</style><p>&nbsp;</p><div class="content"><h2>拒绝操作</h2><p class="text">当前客户负责人丢失请确认是否填写!!</p><p class="links"><a id="button" href="javascript:history.go(-1);">返回</a></p></div>';
            exit;*/
            if(empty($record)){
                global $current_user;
                $current_user_id=$current_user->id;
            }else{
                $current_user_id=$accountcurrentuser;
            }
        }
        $_REQUEST['ranks']=$rank;
        //当前负责人可以领取的最大用户
        $limitm=$recordModel->getRankLimitm($current_user_id);
		//if(empty($limit[$rank])||$limit[$rank]<=0||empty($limitm[$rank])||$limitm[$rank]<=0){
        if(empty($limitm[$rank])||$limitm[$rank]<=0){
            //判断是否是pc端调用
            if($pcType==1){
                return array("result"=>'error','status'=>false, 'message'=> vtranslate($rank,'Accounts','zh_cn').'等级客户保护数量'.$limitm['rankProtectNum'][$rank].'个，您当前已有'.vtranslate($rank,'Accounts','zh_cn').'等级客户'.$limitm['havingRankProtectNum'][$rank].'个，已达保护数量');exit();
            }
	    // cxh 添加客保数不足日志 start
            $paramers['contract_no']="新建或编辑";
            $marks=json_encode($_REQUEST);
            if(!empty($limitm)){
                $limitmsss=json_encode($limitm);
            }else{
                $limitmsss='';
            }
            $paramers['marks']="userid".$current_user_id."已拥有账号数量".$limitmsss.$marks;
            $Matchreceivements_Record_Model=Vtiger_Record_Model::getCleanInstance("Matchreceivements");
            $Matchreceivements_Record_Model->noCalculationAchievementRecord($paramers);
	    // end
            //throw new AppException('当前等级客户保护数量不足！');
            //echo '当前等级客户保护数量不足！<a href="javascript:history.go(-1);">返回</a>';
            echo '<style type="text/css">@-webkit-keyframes appear{from{opacity:0}to{opacity:1}}@-webkit-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-webkit-keyframes contentappear{from{-webkit-transform:scale(0);opacity:0}50%{-webkit-transform:scale(.5);opacity:0}to{-webkit-transform:scale(1);opacity:1}}@-moz-keyframes appear{from{opacity:0}to{opacity:1}}@-moz-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-moz-keyframes contentappear{from{-moz-transform:scale(0);opacity:0}50%{-moz-transform:scale(.5);opacity:0}to{-moz-transform:scale(1);opacity:1}}*{margin:0;padding:0}a:active{position:relative;top:1px}html{-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover}body{width:auto;margin:0 auto 100px auto}.header{position:fixed;top:0;width:100%;height:55px;padding:0 0 0 10px;color:#fff;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border-top:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 0 13px #000;z-index:99;-webkit-animation:1s appear;-moz-animation:1s appear}p.error{color:#000;text-shadow:#fff 0 1px 0;text-align:center;font:900 25em helvetica neue;-webkit-animation:2s headline_appear_animation;-moz-animation:2s headline_appear_animation}.content{margin:auto;padding:30px 40px 40px 40px;width:570px;color:#fff;-webkit-animation:2s contentappear;-moz-animation:2s contentappear;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 3px 8px #000;border-radius:6px;font:16px;line-height:25px;font-weight:300;text-shadow:#000 0 1px 0}.content h2{text-transform:uppercase;text-align:center;padding-bottom:20px}form{height:40px}.inputform{font:12px;border:none;padding:10px;width:300px;margin:15px 0 0 75px}.button{width:100px;margin-top:1px;height:33px;border:none;text-shadow:#fff 0 1px 0;background-image:-moz-linear-gradient(top,#fff,#aaa);background-image:-o-linear-gradient(top,#fff,#aaa);background-image:-webkit-linear-gradient(top,#fff,#aaa);background-image:linear-gradient(top,#fff,#aaa);box-shadow:inset 0 1px rgba(255,255,255,1)}.button:hover{background-image:-moz-linear-gradient(top,#fff,#ccc);background-image:-o-linear-gradient(top,#fff,#ccc);background-image:-webkit-linear-gradient(top,#fff,#ccc);background-image:linear-gradient(top,#fff,#ccc);cursor:pointer}.button:active{background-image:-moz-linear-gradient(top,#ccc,#fff);background-image:-o-linear-gradient(top,#ccc,#fff);background-image:-webkit-linear-gradient(top,#ccc,#fff);background-image:linear-gradient(top,#ccc,#fff)}p.links{margin:24px 0 0 0;text-align:center}p.links a{color:#fff;margin-left:15px;margin-right:15px}p.links a:hover{text-decoration:none;text-shadow:#fff 0 0 5px;-webkit-transition:all ease-in .3s;-moz-transition:all ease-in .3s}</style><p>&nbsp;</p><div class="content"><h2>拒绝操作</h2><p class="text">'.vtranslate($rank,"Accounts").'等级客户保护数量'.$limitm['rankProtectNum'][$rank].'个，您当前已有'.vtranslate($rank,"Accounts").'等级客户'.$limitm['havingRankProtectNum'][$rank].'个，已达保护数量</p><p class="links"><a id="button" href="javascript:history.go(-1);">返回</a></p></div>';
            //echo '<style type="text/css">@-webkit-keyframes appear{from{opacity:0}to{opacity:1}}@-webkit-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-webkit-keyframes contentappear{from{-webkit-transform:scale(0);opacity:0}50%{-webkit-transform:scale(.5);opacity:0}to{-webkit-transform:scale(1);opacity:1}}@-moz-keyframes appear{from{opacity:0}to{opacity:1}}@-moz-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-moz-keyframes contentappear{from{-moz-transform:scale(0);opacity:0}50%{-moz-transform:scale(.5);opacity:0}to{-moz-transform:scale(1);opacity:1}}*{margin:0;padding:0}a:active{position:relative;top:1px}html{-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover}body{width:auto;margin:0 auto 100px auto}.header{position:fixed;top:0;width:100%;height:55px;padding:0 0 0 10px;color:#fff;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border-top:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 0 13px #000;z-index:99;-webkit-animation:1s appear;-moz-animation:1s appear}p.error{color:#000;text-shadow:#fff 0 1px 0;text-align:center;font:900 25em helvetica neue;-webkit-animation:2s headline_appear_animation;-moz-animation:2s headline_appear_animation}.content{margin:auto;padding:30px 40px 40px 40px;width:570px;color:#fff;-webkit-animation:2s contentappear;-moz-animation:2s contentappear;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 3px 8px #000;border-radius:6px;font:16px;line-height:25px;font-weight:300;text-shadow:#000 0 1px 0}.content h2{text-transform:uppercase;text-align:center;padding-bottom:20px}form{height:40px}.inputform{font:12px;border:none;padding:10px;width:300px;margin:15px 0 0 75px}.button{width:100px;margin-top:1px;height:33px;border:none;text-shadow:#fff 0 1px 0;background-image:-moz-linear-gradient(top,#fff,#aaa);background-image:-o-linear-gradient(top,#fff,#aaa);background-image:-webkit-linear-gradient(top,#fff,#aaa);background-image:linear-gradient(top,#fff,#aaa);box-shadow:inset 0 1px rgba(255,255,255,1)}.button:hover{background-image:-moz-linear-gradient(top,#fff,#ccc);background-image:-o-linear-gradient(top,#fff,#ccc);background-image:-webkit-linear-gradient(top,#fff,#ccc);background-image:linear-gradient(top,#fff,#ccc);cursor:pointer}.button:active{background-image:-moz-linear-gradient(top,#ccc,#fff);background-image:-o-linear-gradient(top,#ccc,#fff);background-image:-webkit-linear-gradient(top,#ccc,#fff);background-image:linear-gradient(top,#ccc,#fff)}p.links{margin:24px 0 0 0;text-align:center}p.links a{color:#fff;margin-left:15px;margin-right:15px}p.links a:hover{text-decoration:none;text-shadow:#fff 0 0 5px;-webkit-transition:all ease-in .3s;-moz-transition:all ease-in .3s}</style><p>&nbsp;</p><div class="content"><h2>拒绝操作</h2><p class="text">当前客户负责人对应的客户保护数量不足!!</p><p class="links"><a id="button" href="javascript:history.go(-1);">返回</a></p></div>';
            exit;
		}

        $accountcategory=$request->get('accountcategory');
		if(empty($record)){
			//$protectday=$limit[$rank];
			global  $current_user, $adb;
			$salerank=$recordModel->getSaleRank($current_user->id);
            $userinfo =$adb->pquery("SELECT u.user_entered,ud.departmentid FROM vtiger_users as u LEFT JOIN vtiger_user2department as ud ON ud.userid=u.id WHERE id = ?", array($current_user->id));
            $departmentid = $adb->query_result($userinfo, 0,'departmentid');
            $user_entered = $adb->query_result($userinfo, 0,'user_entered');
			$result=$recordModel->getRankDays(array($salerank,$rank,$departmentid,$user_entered));
            $followday = $result['followday'];
            $isfollow = $result['isfollow'];

            if(0==$accountcategory){
                $_REQUEST['protectday']=$result['protectday'];
            }else{
                //公海和临时区新建的客户都为3天;
                $_REQUEST['protectday']=3;
            }
		}else{
			$values= $recordModel->entity->column_fields;
			if(strstr($values['accountrank'],'_isv') && strstr($request->get('accountrank'),'_notv')){
				echo '已成交客户不能转为未成交状态<a href="javascript:history.go(-1);">返回</a>';
				exit;
			}
			if($values['accountcategory']==2){
				echo '公海客户不允许编辑<a href="javascript:history.go(-1);">返回</a>';
				exit;
			}
		}

		$recordModel->save();
        //修改客户如果是临时区的保护天数为3
		global $adb;
        $groupbuyaccount=$request->get('groupbuyaccount');
		if(!empty($groupbuyaccount) && $groupbuyaccount=='on'){
            $adb->pquery("UPDATE vtiger_account SET accountrank=if(accountrank in('chan_notv','eigp_notv','forp_notv'),'bras_isv',accountrank) WHERE accountid=?",array($recordModel->getId()));
        }
        $province=$request->get('province');
        $city=$request->get('city');
        $area=$request->get('area');
        $adb->pquery("UPDATE vtiger_account SET followday=?,isfollow=? WHERE accountid=?",array($followday,$isfollow,$recordModel->getId()));
        $adb->pquery("UPDATE vtiger_account SET province=?,city=?,area=? WHERE accountid=?",array($province,$city,$area,$recordModel->getId()));
        $adb->pquery("UPDATE vtiger_account SET protectday=if(protectday>3,3,protectday) WHERE accountcategory=1 AND protected=0 AND accountid=?",array($recordModel->getId()));
        global  $current_user;
        if(!empty($record) && ($recordModel->RestoreCustomerProtection() || $recordModel->personalAuthority('Accounts','AccountProtectM')) && $accountcurrentuser!=$current_user_id && $values['accountcategory']==0 && 0 == $accountcategory){
            $salerank = $recordModel->getSaleRank($current_user_id);
            $userinfo = $adb->pquery("SELECT u.user_entered,ud.departmentid FROM vtiger_users as u LEFT JOIN vtiger_user2department as ud ON ud.userid=u.id WHERE id = ?", array($current_user_id));
            $departmentid = $adb->query_result($userinfo, 0,'departmentid');
            $user_entered = $adb->query_result($userinfo, 0,'user_entered');
            $result = $recordModel->getRankDays(array($salerank, $rank,$departmentid,$user_entered));
            $adb->pquery("UPDATE vtiger_account SET protectday=".$result['protectday'].",effectivedays=".$result['protectday']." WHERE protected=0 AND accountid=?",array($recordModel->getId()));
            $id = $adb->getUniqueId('vtiger_modtracker_basic');
            $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                array($id, $record, 'Accounts', $current_user->id, date('Y-m-d H:i:s'), 0));
            $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                Array($id, 'description', '客保天数'.$values['protectday'], $result['protectday']));
            //修改对应客户的相关的负责人
            $recordModel->updaterecord(array("AccountEdit",$current_user->id),$record,$current_user_id,false);
        }
        if(!empty($record) && $accountcategory==0 && $values['accountcategory']==1){
            $adb->pquery("UPDATE vtiger_account SET protectday=effectivedays WHERE protected=0 AND accountid=?",array($recordModel->getId()));
        }
        if(empty($record)){
            $accountname=$request->get('accountname');
            $label=preg_replace('/\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;|&quot;|　|&apos;|&amp;|&lt;|&gt;|&#039;|&ldquo;|&rdquo;|&lsquo;|&rsquo;|&hellip;|\“|\”|\‘|\〉|\〈|\’|\〖|\〗|\【|\】|\、|\·|\…|\——|\＋|\－|\＝|\,|\<|\.|\>|\/|\?|\;|\:|\\\'|\"|\[|\{|\]|\}|\\|\||\`|\~|\!|\@|\#|\$|\%|\^|\\&|\*|\(|\)|\-|\_|\=|\+|\，|\＜|\．|\＞|\／|\？|\；|\：|\＇|\＂|\［|\{|\］|\}|\＼|\｜|\｀|\￣|\！|\＠|\#|\＄|\％|\＾|\＆|\＊|\（|\）|\－|\＿|\＝|\＋|\，|\《|\．|\》|\、|\？|\；|\：|\’|\”|\【|\｛|\】|\｝|\＼|\｜|\·|\～|\！|\＠|\＃|\￥|\％|\…|\＆|\×|\（|\）|\－|\——|\—|\＝|\＋|\，|\《|\。|\》|\、|\？|\；|\：|\‘|\“|\【|\｛|\】|\｝|\、|\||\·|\~|\！|\@|\#|\￥|\%|\&|\*|\（|\）|\-|\——|\=|\+/u','',$accountname);
            $label=strtoupper($label);
            $adb->pquery("UPDATE `vtiger_uniqueaccountname` SET accountname='{$label}' WHERE accountid=?",array($recordModel->getId()));

        }
        // 如果$pcType=1是移动端提交  start
        if($pcType==1){
            $id = $recordModel->getId();
            if($id>0){
                return   array("result"=>'success','status'=>true, 'message'=> "新建成功",'accountid'=>$id);
            }
        }
        if(!empty($record) && $mobile!=$entity['mobile']){
            function findreport($reportsModel,&$array=array()){
                if($reportsModel->id==1 || $reportsModel->id==38){
                    return $array;
                }
                $reportsModel = Users_Privileges_Model::getInstanceById($reportsModel->reports_to_id);
                $array['id'][]=$reportsModel->id;
                $array['email']=$array['email'].'|'.$reportsModel->email1;
                $array['reportemail'][$reportsModel->id]['mail']=$reportsModel->email1;
                $array['reportemail'][$reportsModel->id]['name']=$reportsModel->last_name;
                if($reportsModel->reports_to_id ==38 || $reportsModel->reports_to_id ==1 || empty($reportsModel->reports_to_id)){
                    return $array;
                }
                return findreport($reportsModel,$array);
            }
            $reportsModel = Users_Privileges_Model::getInstanceById($recordModel->get('assigned_user_id'));
            $array=findreport($reportsModel);
            if(!empty($array) && in_array(43,$array['id'])){
                $query="SELECT  GROUP_CONCAT(departmentid,'|',departmentname) AS departmentidandname FROM vtiger_departments WHERE departmentid in('".$reportsModel->departmentid."','".$current_user->departmentid."')";
                $departmentsResult=$adb->pquery($query,array());
                $accountName=$reportsModel->last_name;
                $currentUserName=$current_user->last_name;
                if($adb->num_rows($departmentsResult)){
                    $departmentidandname=$departmentsResult->fields['departmentidandname'];
                    $departmentidandnameArrayTemp=explode(',',$departmentidandname);
                    $departmentidandnameArray=array();
                    foreach($departmentidandnameArrayTemp as $value){
                        $temp=explode('|',$value);
                        $departmentidandnameArray[$temp[0]]=$temp[1];
                    }
                    $accountName=$reportsModel->last_name.'【'.$departmentidandnameArray[$reportsModel->departmentid].'】';
                    $currentUserName=$current_user->last_name.'【'.$departmentidandnameArray[$current_user->departmentid].'】';
                }
                $email=trim($array['email'],'|');
                $content='客户名称:'.$recordModel->get('accountname').'<br>客户负责人:'.$accountName.'<br>修改操作人:'.$currentUserName.'<br>修改前手机号:'.$entity['mobile'].'<br>修改后手机号:'.$mobile;
                //$recordModel->sendWechatMessage(array('email' => trim($email), 'content' => $content,  'flag' => 6));
                $recordModel->sendWechatMessage(array('email'=>trim($email),'description'=>$content,'dataurl'=>'#','title'=>'修改手机号提醒！！'.$currentUserName,'flag'=>7));
                $recordModel->sendMail('修改手机号提醒！！-'.$currentUserName,$content,$array['reportemail'],'ERP系统');
            }
        }
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

}
