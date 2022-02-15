<?php
class Staypayment_Save_Action extends Vtiger_Save_Action {
    public $stayPaymentWorkFlowSid = 2430423;  //代付款在线签收id
    public function saveRecord($request) {
        $adb =PearDatabase::getInstance();
        $record=$request->get('record');
        $staypaymenttype=$request->get('staypaymenttype');
        $staypaymentjine=$request->get('staypaymentjine');
        $recordModel = $this->getRecordModelFromRequest($request);
        $autoRecordSql=' and 1=1';
        if($request->get('record')>0){
            $recordModel->set('id',$record);
            $recordModel->set('mode','edit');
            $stayRecord=Vtiger_Record_Model::getInstanceById($record,'Staypayment',true);
            $oldSurplusmoney=$stayRecord->get('surplusmoney');//原来的金额
            $oldStaypaymentjine=$stayRecord->get('staypaymentjine');
            $autoRecordSql=' and vtiger_staypayment.staypaymentid!='.$record;
        }
        //5.65需求，如果已经有模拟新建的代付款合同存在，不准新建
        $sql="select vtiger_staypayment.staypaymentid,vtiger_staypayment.surplusmoney from vtiger_staypayment left join vtiger_crmentity on  vtiger_staypayment.staypaymentid=vtiger_crmentity.crmid  where vtiger_crmentity.deleted=0 and  vtiger_staypayment.contractid=? and vtiger_staypayment.isauto=1 and vtiger_staypayment.surplusmoney>0 ".$autoRecordSql;
        $result=$adb->pquery($sql,array($request->get('contractid')));
        if($adb->num_rows($result)>0){
            //有未消耗的代付款
            $surplusmoney=$adb->query_result($result,0,'surplusmoney');
            $msg = "已存在该合同（".$request->get('contractid_display')."）模拟新建未使用完的代付款，金额是【".$surplusmoney."】不准新建";
            echo '<style type="text/css">@-webkit-keyframes appear{from{opacity:0}to{opacity:1}}@-webkit-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-webkit-keyframes contentappear{from{-webkit-transform:scale(0);opacity:0}50%{-webkit-transform:scale(.5);opacity:0}to{-webkit-transform:scale(1);opacity:1}}@-moz-keyframes appear{from{opacity:0}to{opacity:1}}@-moz-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-moz-keyframes contentappear{from{-moz-transform:scale(0);opacity:0}50%{-moz-transform:scale(.5);opacity:0}to{-moz-transform:scale(1);opacity:1}}*{margin:0;padding:0}a:active{position:relative;top:1px}html{-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover}body{width:auto;margin:0 auto 100px auto}.header{position:fixed;top:0;width:100%;height:55px;padding:0 0 0 10px;color:#fff;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border-top:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 0 13px #000;z-index:99;-webkit-animation:1s appear;-moz-animation:1s appear}p.error{color:#000;text-shadow:#fff 0 1px 0;text-align:center;font:900 25em helvetica neue;-webkit-animation:2s headline_appear_animation;-moz-animation:2s headline_appear_animation}.content{margin:auto;padding:30px 40px 40px 40px;width:570px;color:#fff;-webkit-animation:2s contentappear;-moz-animation:2s contentappear;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 3px 8px #000;border-radius:6px;font:16px;line-height:25px;font-weight:300;text-shadow:#000 0 1px 0}.content h2{text-transform:uppercase;text-align:center;padding-bottom:20px}form{height:40px}.inputform{font:12px;border:none;padding:10px;width:300px;margin:15px 0 0 75px}.button{width:100px;margin-top:1px;height:33px;border:none;text-shadow:#fff 0 1px 0;background-image:-moz-linear-gradient(top,#fff,#aaa);background-image:-o-linear-gradient(top,#fff,#aaa);background-image:-webkit-linear-gradient(top,#fff,#aaa);background-image:linear-gradient(top,#fff,#aaa);box-shadow:inset 0 1px rgba(255,255,255,1)}.button:hover{background-image:-moz-linear-gradient(top,#fff,#ccc);background-image:-o-linear-gradient(top,#fff,#ccc);background-image:-webkit-linear-gradient(top,#fff,#ccc);background-image:linear-gradient(top,#fff,#ccc);cursor:pointer}.button:active{background-image:-moz-linear-gradient(top,#ccc,#fff);background-image:-o-linear-gradient(top,#ccc,#fff);background-image:-webkit-linear-gradient(top,#ccc,#fff);background-image:linear-gradient(top,#ccc,#fff)}p.links{margin:24px 0 0 0;text-align:center}p.links a{color:#fff;margin-left:15px;margin-right:15px}p.links a:hover{text-decoration:none;text-shadow:#fff 0 0 5px;-webkit-transition:all ease-in .3s;-moz-transition:all ease-in .3s}</style><p>&nbsp;</p><div class="content"><h2>拒绝操作</h2><p class="text">' . $msg . '</p><p class="links"><a id="button" href="javascript:history.go(-1);">返回</a></p></div>';
            exit;
        }
        $sql="select vtiger_staypayment.staymentcode from vtiger_staypayment left join vtiger_crmentity on  vtiger_staypayment.staypaymentid=vtiger_crmentity.crmid  where vtiger_staypayment.modulestatus='a_normal' and vtiger_crmentity.deleted=0 and  vtiger_staypayment.contractid=?".$autoRecordSql;
        $result=$adb->pquery($sql,array($request->get('contractid')));
        if($adb->num_rows($result)>0){
            //有未消耗的代付款
            $staymentcode=$adb->query_result($result,0,'staymentcode');
            $msg = "该合同已经存在未签收的代付款申请，代付款编号是【".$staymentcode."】，无法再次新增代付款申请。";
            echo '<style type="text/css">@-webkit-keyframes appear{from{opacity:0}to{opacity:1}}@-webkit-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-webkit-keyframes contentappear{from{-webkit-transform:scale(0);opacity:0}50%{-webkit-transform:scale(.5);opacity:0}to{-webkit-transform:scale(1);opacity:1}}@-moz-keyframes appear{from{opacity:0}to{opacity:1}}@-moz-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-moz-keyframes contentappear{from{-moz-transform:scale(0);opacity:0}50%{-moz-transform:scale(.5);opacity:0}to{-moz-transform:scale(1);opacity:1}}*{margin:0;padding:0}a:active{position:relative;top:1px}html{-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover}body{width:auto;margin:0 auto 100px auto}.header{position:fixed;top:0;width:100%;height:55px;padding:0 0 0 10px;color:#fff;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border-top:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 0 13px #000;z-index:99;-webkit-animation:1s appear;-moz-animation:1s appear}p.error{color:#000;text-shadow:#fff 0 1px 0;text-align:center;font:900 25em helvetica neue;-webkit-animation:2s headline_appear_animation;-moz-animation:2s headline_appear_animation}.content{margin:auto;padding:30px 40px 40px 40px;width:570px;color:#fff;-webkit-animation:2s contentappear;-moz-animation:2s contentappear;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 3px 8px #000;border-radius:6px;font:16px;line-height:25px;font-weight:300;text-shadow:#000 0 1px 0}.content h2{text-transform:uppercase;text-align:center;padding-bottom:20px}form{height:40px}.inputform{font:12px;border:none;padding:10px;width:300px;margin:15px 0 0 75px}.button{width:100px;margin-top:1px;height:33px;border:none;text-shadow:#fff 0 1px 0;background-image:-moz-linear-gradient(top,#fff,#aaa);background-image:-o-linear-gradient(top,#fff,#aaa);background-image:-webkit-linear-gradient(top,#fff,#aaa);background-image:linear-gradient(top,#fff,#aaa);box-shadow:inset 0 1px rgba(255,255,255,1)}.button:hover{background-image:-moz-linear-gradient(top,#fff,#ccc);background-image:-o-linear-gradient(top,#fff,#ccc);background-image:-webkit-linear-gradient(top,#fff,#ccc);background-image:linear-gradient(top,#fff,#ccc);cursor:pointer}.button:active{background-image:-moz-linear-gradient(top,#ccc,#fff);background-image:-o-linear-gradient(top,#ccc,#fff);background-image:-webkit-linear-gradient(top,#ccc,#fff);background-image:linear-gradient(top,#ccc,#fff)}p.links{margin:24px 0 0 0;text-align:center}p.links a{color:#fff;margin-left:15px;margin-right:15px}p.links a:hover{text-decoration:none;text-shadow:#fff 0 0 5px;-webkit-transition:all ease-in .3s;-moz-transition:all ease-in .3s}</style><p>&nbsp;</p><div class="content"><h2>拒绝操作</h2><p class="text">' . $msg . '</p><p class="links"><a id="button" href="javascript:history.go(-1);">返回</a></p></div>';
            exit;
        }
        $recordModel->set('workflowsid', $this->stayPaymentWorkFlowSid);
        $recordModel->set('workflowstime', date('Y-m-d H:i:s'));
        $recordModel->set('workflowsnode', '代付款线上签收');
		$recordModel->save();
        if(empty($record)){
            $year=date('Y');
//            $adb->pquery("UPDATE vtiger_staypayment SET staypaymentno=CONCAT('S{$year}',right(staypaymentno,5)) WHERE staypaymentid=?",array($recordModel->getId()));
        }

        $recordId = $recordModel->getId();

        if($recordModel->get('mode')!='edit') {
            $sql = "update vtiger_staypayment set staymentcode=?,surplusmoney=? where staypaymentid=?";
            $adb->pquery($sql, array('DFK000' . $recordId, $request->get('staypaymentjine'),$recordId));
        }else{
            //编辑
            Matchreceivements_Record_Model::recordLog(array($staypaymentjine,$oldStaypaymentjine),'stay');
            if ($staypaymenttype== 'fixation' && bccomp($staypaymentjine, $oldStaypaymentjine,2) >= 0) {
                $sql = "update vtiger_staypayment set surplusmoney=? where staypaymentid=?";
                Matchreceivements_Record_Model::recordLog(array($sql),'stay');
                $adb->pquery($sql, array(bcadd($oldSurplusmoney,bcsub($staypaymentjine, $oldStaypaymentjine,2),2), $recordId));
            }
        }



        //生成工作流
        $_REQUEST['workflowsid']=$this->stayPaymentWorkFlowSid;
        $focus = CRMEntity::getInstance('Staypayment');
        $focus->makeWorkflows('Staypayment', $_REQUEST['workflowsid'], $recordId,'edit');

		return $recordModel;
	}
}
