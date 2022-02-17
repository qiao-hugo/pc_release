<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Class RefillApplication_Edit_View extends Vtiger_Edit_View {

	public function checkPermission(Vtiger_Request $request) {
        parent::checkPermission($request);
        global $current_user;
        if(in_array($current_user->id,getDepartmentUser('H3')) && 'H85'!=$current_user->roleid){
            //echo '<style type="text/css">@-webkit-keyframes appear{from{opacity:0}to{opacity:1}}@-webkit-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-webkit-keyframes contentappear{from{-webkit-transform:scale(0);opacity:0}50%{-webkit-transform:scale(.5);opacity:0}to{-webkit-transform:scale(1);opacity:1}}@-moz-keyframes appear{from{opacity:0}to{opacity:1}}@-moz-keyframes headline_appear_animation{from{opacity:0}25%{opacity:0}to{opacity:1}}@-moz-keyframes contentappear{from{-moz-transform:scale(0);opacity:0}50%{-moz-transform:scale(.5);opacity:0}to{-moz-transform:scale(1);opacity:1}}*{margin:0;padding:0}a:active{position:relative;top:1px}html{-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover}body{width:auto;margin:0 auto 100px auto}.header{position:fixed;top:0;width:100%;height:55px;padding:0 0 0 10px;color:#fff;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border-top:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 0 13px #000;z-index:99;-webkit-animation:1s appear;-moz-animation:1s appear}p.error{color:#000;text-shadow:#fff 0 1px 0;text-align:center;font:900 25em helvetica neue;-webkit-animation:2s headline_appear_animation;-moz-animation:2s headline_appear_animation}.content{margin:auto;padding:30px 40px 40px 40px;width:570px;color:#fff;-webkit-animation:2s contentappear;-moz-animation:2s contentappear;background-image:-moz-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-o-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:-webkit-linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));background-image:linear-gradient(top,rgba(85,85,85,.7),rgba(0,0,0,1));border:1px solid #000;box-shadow:inset 0 1px rgba(255,255,255,.4),0 3px 8px #000;border-radius:6px;font:16px;line-height:25px;font-weight:300;text-shadow:#000 0 1px 0}.content h2{text-transform:uppercase;text-align:center;padding-bottom:20px}form{height:40px}.inputform{font:12px;border:none;padding:10px;width:300px;margin:15px 0 0 75px}.button{width:100px;margin-top:1px;height:33px;border:none;text-shadow:#fff 0 1px 0;background-image:-moz-linear-gradient(top,#fff,#aaa);background-image:-o-linear-gradient(top,#fff,#aaa);background-image:-webkit-linear-gradient(top,#fff,#aaa);background-image:linear-gradient(top,#fff,#aaa);box-shadow:inset 0 1px rgba(255,255,255,1)}.button:hover{background-image:-moz-linear-gradient(top,#fff,#ccc);background-image:-o-linear-gradient(top,#fff,#ccc);background-image:-webkit-linear-gradient(top,#fff,#ccc);background-image:linear-gradient(top,#fff,#ccc);cursor:pointer}.button:active{background-image:-moz-linear-gradient(top,#ccc,#fff);background-image:-o-linear-gradient(top,#ccc,#fff);background-image:-webkit-linear-gradient(top,#ccc,#fff);background-image:linear-gradient(top,#ccc,#fff)}p.links{margin:24px 0 0 0;text-align:center}p.links a{color:#fff;margin-left:15px;margin-right:15px}p.links a:hover{text-decoration:none;text-shadow:#fff 0 0 5px;-webkit-transition:all ease-in .3s;-moz-transition:all ease-in .3s}</style><p>&nbsp;</p><div class="content"><h2>拒绝操作</h2><p class="text">中小商务退款申请只有客服才能申请,请联系对应的客服进行申请!!!!!</p><p class="links"><a id="button" href="javascript:history.go(-1);">返回</a></p></div>';
            //exit;
        }

    }
    public function process(Vtiger_Request $request) {
        $viewer = $this->getViewer ($request);
        $moduleName = $request->getModule();
        $record = $request->get('record');
        if(!empty($record) && $request->get('isDuplicate') == true) {
            $recordModel = $this->record?$this->record:Vtiger_Record_Model::getInstanceById($record, $moduleName);
            $viewer->assign('MODE', '');
        }else if(!empty($record)) {
            $recordModel = $this->record?$this->record:Vtiger_Record_Model::getInstanceById($record, $moduleName);
            $viewer->assign('RECORD_ID', $record);
            $viewer->assign('MODE', 'edit');
        } else {
            $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
            $viewer->assign('MODE', '');
            $viewer->assign('RECORD_ID','');
        }
        if(!$this->record){
            $this->record = $recordModel;
        }

        $moduleModel = $recordModel->getModule();
        //读取模块的字段
        $fieldList = $moduleModel->getFields();

        //取交集?还不知道有什么用
        $requestFieldList = array_intersect_key($request->getAll(), $fieldList);
        if(!empty($requestFieldList)){
            foreach($requestFieldList as $fieldName=>$fieldValue){
                $fieldModel = $fieldList[$fieldName];
                if($fieldModel->isEditable()) {
                    $recordModel->set($fieldName, $fieldModel->getDBInsertValue($fieldValue));
                }
            }
        }
        $rechargesource=$recordModel->get('rechargesource');
        $rechargesource=($request->get('rechargesource')=='Accounts' || $rechargesource=='Accounts')?'Accounts':(($request->get('rechargesource')=='Vendors' || $rechargesource=='Vendors')?'Vendors':
            (($request->get('rechargesource')=='TECHPROCUREMENT' || $rechargesource=='TECHPROCUREMENT')?'TECHPROCUREMENT':
                (($request->get('rechargesource')=='PreRecharge' || $rechargesource=='PreRecharge')?'PreRecharge':
                    (($request->get('rechargesource')=='OtherProcurement' || $rechargesource=='OtherProcurement')?'OtherProcurement':
                        (($request->get('rechargesource')=='NonMediaExtraction' || $rechargesource=='NonMediaExtraction')?'NonMediaExtraction':
                            (($request->get('rechargesource')=='PACKVENDORS' || $rechargesource=='PACKVENDORS')?'PACKVENDORS':
                                (($request->get('rechargesource')=='COINRETURN' || $rechargesource=='COINRETURN')?'COINRETURN':
                                    (($request->get('rechargesource')=='INCREASE' || $rechargesource=='INCREASE')?'INCREASE':
                                    (($request->get('rechargesource')=='contractChanges' || $rechargesource=='contractChanges')?'contractChanges':
                                    'Accounts')))))))));
        $recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel,'Edit');
        $productserviceValue='';
        $moduleArr=array('Vendors','PreRecharge','TECHPROCUREMENT','NonMediaExtraction','COINRETURN');
        if(in_array($rechargesource,$moduleArr)){
            $productserviceValue=$recordStructureInstance->getRecord()->getModule()->getFields();
            $productserviceValue=$productserviceValue['productid'];
            $productserviceValue=$productserviceValue->getEditViewDisplayValue($recordModel->get('productservice'));
        }
        $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);

        $oldrechargesource=$recordModel->get('oldrechargesource');
        $servicecontractid = $recordModel->get('servicecontractsid');
        $changecontracttype = $recordModel->get('changecontracttype');
        // 如果是合同变更申请
        if($rechargesource=='contractChanges'){
            $records=$request->get("record");
            if(!empty($records)){
                $refillapplicationList = RefillApplication_Record_Model::getListChangeRefillapplicationDetail($request->get("record"));
                if($changecontracttype=='ServiceContracts'){
                    //仍旧没有回款的数据
                    $result = RefillApplication_Record_Model::getListAboutServiceContractRefillapplicationAginCheck($servicecontractid,$oldrechargesource,$request->get("record"));
                    foreach ($result as $key=>$val){
                        $noProblem[]=$val['refillapplicationid'];
                    }
                    // 循环遍历已经提交的需要变更的充值申请单 看看是否 数据源仍然符合 提交前获取充值申请单的条件 如果符合则正确显示在编辑列表如果不是 符合已验证的充值单id 则加个标记
                    foreach ($refillapplicationList as $key=>$value){
                        if(!in_array($value['refillapplicationid'],$noProblem)){
                            $refillapplicationList[$key]['error']=1;
                        }
                    }
                }
                $viewer->assign('REFILLAPPLICATION_LIST',$refillapplicationList);
            }
            $viewer->assign('newaccount_name',$recordModel->get('newaccount_name'));
            $viewer->assign('account_name',$recordModel->get('account_name'));
            $viewer->assign('oldcontract_no',$recordModel->get('oldcontract_no'));
            $viewer->assign('newcontract_no',$recordModel->get('newcontract_no'));
            $viewer->assign('changecontracttype',$recordModel->get('changecontracttype'));
        }

        /*if($rechargesource=='Vendors') {
            foreach ($recordStructureInstance->getStructure() as $value) {
                if (!empty($value['did'])) {
                    $value['did']->set('uitype',15);
                    $value['did']->set('typeofdata', ' V~O');
                    //break;
                }
            }
        }*/
        $rechargeSheet=RefillApplication_Record_Model::getRechargeSheet($recordModel->entity->column_fields['record_id']);
        $rechargeSheetCount=count($rechargeSheet);
        $rechargeSheetCount+=1;
        $incount=1;
        if($rechargesource=='COINRETURN'){
            if($rechargeSheetCount>2) {
                $incount = 0;
                $rechargeSheetCount = 1;
                $inarray = array();
                $outarray = array();
                foreach ($rechargeSheet as $value) {
                    if ($value['turninorout'] == 'in') {
                        $value['seqnum']=++$incount;
                        $inarray[] = $value;
                    } else {
                        $value['seqnum']=++$rechargeSheetCount;
                        $outarray[] = $value;

                    }
                }
                $rechargeSheet = array_merge($outarray, $inarray);
            }else{
                $rechargeSheetCount=1;
                $rechargeSheet[0]['seqnum']=1;
            }
        }
        $RECORD_STRUCTURE=$recordStructureInstance->getStructure();
        if($rechargesource=='COINRETURN'){
            $tempdata['vendorid']=$RECORD_STRUCTURE['VENDOR_LBL_INFO']['vendorid'];
            $tempposition=array_search('servicecontractsid',array_keys($RECORD_STRUCTURE['LBL_INFO']));
            $firstArray=array_splice($RECORD_STRUCTURE['LBL_INFO'],0,$tempposition+1);
            $RECORD_STRUCTURE['LBL_INFO']=array_merge($firstArray,$tempdata,$RECORD_STRUCTURE['LBL_INFO']);

        }
        //$viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());//UI字段生成位置
        $viewer->assign('RECORD_STRUCTURE', $RECORD_STRUCTURE);//UI字段生成位置
        $viewer->assign('RECHARGESOURCE', $rechargesource);
        $viewer->assign('RECHARGESHEETCOUNT', $rechargeSheetCount);
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('PRODUCTSERVICEVALUE', $productserviceValue);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());//执行好多次，真是
        $viewer->assign('C_RECHARGESHEET',$rechargeSheet);
        $viewer->assign('C_RECHARGERODUCT', RefillApplication_Record_Model::getRechargeProduct());
	    $viewer->assign('C_RECHARGERCURRENTTYPE', RefillApplication_Record_Model::getReceivementCurrencyType());
        $viewer->assign('RECORD',$recordModel);//编辑页面显示不可编辑字段内容
        $isRelationOperation = $request->get('relationOperation');
        $viewer->assign('IS_RELATION_OPERATION', $isRelationOperation);
        if($isRelationOperation) {
            $viewer->assign('SOURCE_MODULE', $request->get('sourceModule'));
            $viewer->assign('SOURCE_RECORD', $request->get('sourceRecord'));
        }
        //使用上传控件
        //$viewer->assign('MAX_UPLOAD_LIMIT_MB', Vtiger_Util_Helper::getMaxUploadSize());
        //$viewer->assign('MAX_UPLOAD_LIMIT', vglobal('upload_maxsize'));
        $viewer->view('EditView.tpl', $moduleName);
    }
}