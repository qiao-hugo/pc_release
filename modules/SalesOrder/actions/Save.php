<?php
class SalesOrder_Save_Action extends Vtiger_Save_Action {

	public function saveRecord($request) {
		$productids=$request->get('productids');
		$record=$request->get('record');
		$servicecontractsid=$request->get('servicecontractsid');
        $recordModel = $this->getRecordModelFromRequest($request);
		if($record && $servicecontractsid){
			//有合同//不考虑合同被修改//如果变更了合同
			if($recordModel->entity->column_fields['servicecontractsid']!=$servicecontractsid){
				echo '编辑中合同不允许改变';
				exit;
			}
		}
		
      	$accountid=$request->get('account_id');
      	$servicecontractsid=$request->get('servicecontractsid');
      	$workflowsid=$request->get('workflowsid');
		$issubmit=$request->get('issubmit');
		/*if($accountid>0 && $issubmit=='on'){
			Accounts_Record_Model::updateAccountsDealtime($accountid);
		}*/
        //
        if($_REQUEST['issubmit']&&!empty($_REQUEST['issubmit'])){
            if(empty($_REQUEST['p'])){
                $msginfo='工单对应的产品模板无效,请先确认';
                echo '<style type="text/css">@-webkit-keyframes appear{from{opacity:0}to{opacity:1}}@-webkit-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-webkit-keyframes contentappear{from{-webkit-transform:scale(0);opacity:0}50%{-webkit-transform:scale(.5);opacity:0}to{-webkit-transform:scale(1);opacity:1}}@-moz-keyframes appear{from{opacity:0}to{opacity:1}}@-moz-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-moz-keyframes contentappear{from{-moz-transform:scale(0);opacity:0}50%{-moz-transform:scale(.5);opacity:0}to{-moz-transform:scale(1);opacity:1}}*{margin:0;padding:0}a:active{position:relative;top:1px}html{-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover}body{width:auto;margin:0 auto 100px auto}.header{position:fixed;top:0;width:100%;height:55px;padding:0 0 0 10px;color:#fff;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border-top:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 0 13px #000;z-index:99;-webkit-animation:1s appear;-moz-animation:1s appear}p.error{color:#000;text-shadow:#fff 0 1px 0;text-align:center;font:900 25em helvetica neue;-webkit-animation:2s headline_appear_animation;-moz-animation:2s headline_appear_animation}.content{margin:auto;padding:30px 40px 40px 40px;width:570px;color:#fff;-webkit-animation:2s contentappear;-moz-animation:2s contentappear;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 3px 8px #000;border-radius:6px;font:16px;line-height:25px;font-weight:300;text-shadow:#000 0 1px 0}.content h2{text-transform:uppercase;text-align:center;padding-bottom:20px}form{height:40px}.inputform{font:12px;border:none;padding:10px;width:300px;margin:15px 0 0 75px}.button{width:100px;margin-top:1px;height:33px;border:none;text-shadow:#fff 0 1px 0;background-image:-moz-linear-gradient(top,#fff,#aaa);background-image:-o-linear-gradient(top,#fff,#aaa);background-image:-webkit-linear-gradient(top,#fff,#aaa);background-image:linear-gradient(top,#fff,#aaa);box-shadow:inset 0 1px rgba(255,255,255,1)}.button:hover{background-image:-moz-linear-gradient(top,#fff,#ccc);background-image:-o-linear-gradient(top,#fff,#ccc);background-image:-webkit-linear-gradient(top,#fff,#ccc);background-image:linear-gradient(top,#fff,#ccc);cursor:pointer}.button:active{background-image:-moz-linear-gradient(top,#ccc,#fff);background-image:-o-linear-gradient(top,#ccc,#fff);background-image:-webkit-linear-gradient(top,#ccc,#fff);background-image:linear-gradient(top,#ccc,#fff)}p.links{margin:24px 0 0 0;text-align:center}p.links a{color:#fff;margin-left:15px;margin-right:15px}p.links a:hover{text-decoration:none;text-shadow:#fff 0 0 5px;-webkit-transition:all ease-in .3s;-moz-transition:all ease-in .3s}</style><p>&nbsp;</p><div class="content"><h2>拒绝操作</h2><p class="text">'.$msginfo.'</p><p class="links"><a id="button" href="javascript:history.go(-1);">返回</a></p></div>';
                exit;
            }
            //客户升级
            if($accountid>0){
                Accounts_Record_Model::updateAccountsDealtime($accountid);
            }
            //根据合同来确定是否是T云系列确定是否要生成工作流
            /*if($servicecontractsid>0) {
                //是否是指定的工作流
                if(ServiceContracts_Record_Model::selectWorkfows()==$workflowsid){
                    //合同产品中是否有T-clude系列产品
                    if(ServiceContracts_Record_Model::createIsWorkflows('', $servicecontractsid)){
                        //回款小于成本价
                        if(!ServiceContracts_Record_Model::receiveDayprice($servicecontractsid,1)){
                            //$request->set('issubmit',0);
                            //设为0不生成工作流 CRMEntity中102行
                            $_REQUEST['issubmit']=0;//生成工作流
                            $request->set('modulestatus', 'c_lackpayment');
                            $issubmit='set';//重置条件方便判断是否生成了工作流和删除工作流对应的第1,2个节点
                        }
                    }
                }
            }*/

        }
		$recordModel->save();
		
        global $issubmit;
        if($_REQUEST['issubmit']&&!empty($_REQUEST['issubmit'])){
		//工单确定认提交后更改其状态防止通过审核时第一个节点审核人通过编辑查看工单详情导致负责人更改的情况
            global $adb;
            $adb->pquery("UPDATE vtiger_salesorder SET modulestatus=(IF((modulestatus='a_normal' OR modulestatus='a_exception'),'b_actioning',modulestatus)),iseditproductlist=0 WHERE vtiger_salesorder.salesorderid=?",array($recordModel->getId()));
            //根据合同来确定是否是T云系列
            if($servicecontractsid>0) {
                //是否是指定的工作流
                if(ServiceContracts_Record_Model::selectWorkfows()==$workflowsid) {
                    //合同产品中是否有T-clude系列产品
                    if (ServiceContracts_Record_Model::createIsWorkflows('', $servicecontractsid)) {
                        //回款大于=成本价
                        if (ServiceContracts_Record_Model::receiveDayprice($servicecontractsid,$recordModel->getId())) {
                            //走到这里说明已经生成工作流了那么要做的是,是否是额外产品,是的话,保留工作流节点,是套餐的话删除其节点
                            ServiceContracts_Record_Model::setWorkflowNode($recordModel->getId());
                            // 删除没必要的审核流节点后 发消息
                            $object = new SalesorderWorkflowStages_SaveAjax_Action();
                            //file_put_contents('files.txt',$salesorderid);
                            $object->sendWxRemind(array('salesorderid'=>$recordModel->getId(),'salesorderworkflowstagesid'=>0));
                        }
                    }
                }
            }
        }elseif($issubmit=='set'){
            //T云套餐更新流程的状态为回款不足
            global $adb;
            $adb->pquery("UPDATE vtiger_salesorder SET modulestatus=(IF((modulestatus='a_normal' OR modulestatus='a_exception'),'c_lackpayment',modulestatus)) WHERE vtiger_salesorder.salesorderid=?",array($recordModel->getId()));
        }
		return $recordModel;
	}
}
