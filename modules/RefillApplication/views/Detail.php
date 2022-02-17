<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class RefillApplication_Detail_View extends Vtiger_Detail_View {

    /**
     * Function shows the entire detail for the record
     * @param Vtiger_Request $request
     * @return <type>
     * 显示详细信息，两个地方都会显示
     */
    function showModuleDetailView(Vtiger_Request $request) {



        $recordId = $request->get('record');
        $moduleName = $request->getModule();
        //young.yang 2014-12-26 工作流
        global $isallow,$current_user;
        if(in_array($moduleName, $isallow)){
            echo $this->getWorkflowsM($request);
        }
        //end
        if(!$this->record){
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId,true);
        }
        $recordModel = $this->record->getRecord();
        /*if($recordModel->get('financialstate')==1){
            $recordModel->set('grossadvances',0.00);
            $recordModel->set('totalrecharge',$recordModel->get('actualtotalrecharge'));
        }*/
        $recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
        //var_dump($recordStrucure);die;
        $structuredValues = $recordStrucure->getStructure();


        $moduleModel = $recordModel->getModule();

        $moduleStatus=$recordModel->get('modulestatus');
        $receiveStatus=($moduleStatus=='c_complete'&&
                        /*$recordModel->get('financialstate')==0 &&*/
                        $recordModel->get('isbackwash')==0 &&
                        $current_user->id==$recordModel->get('assigned_user_id')&&
                        $recordModel->get('actualtotalrecharge')>$recordModel->get('totalrecharge')
                        )?true:false;


        $rechargesource=$recordModel->get('rechargesource');
        $rechargesource=($rechargesource=='Accounts')?'Accounts':(($rechargesource=='Vendors')?'Vendors':
            ($rechargesource=='TECHPROCUREMENT'?'TECHPROCUREMENT':
                ($rechargesource=='PreRecharge'?'PreRecharge':
                    ($rechargesource=='OtherProcurement'?'OtherProcurement':
                        ($rechargesource=='NonMediaExtraction'?'NonMediaExtraction':
                            ($rechargesource=='PACKVENDORS'?'PACKVENDORS':
                                ($rechargesource=='COINRETURN'?'COINRETURN':
                                    ($rechargesource=='INCREASE'?'INCREASE':
                                        'Accounts'))))))));
        $refundsOrTransfers=($moduleStatus=='c_complete'&&
            //$moduleModel->exportGrouprt('RefillApplication',"dobackwash")&&
            $current_user->id==$recordModel->get('assigned_user_id') &&
            /*$recordModel->get('isbackwash')==0 &&*/
            /*$recordModel->get('financialstate')==0 &&*/
            in_array($rechargesource,array('Vendors','TECHPROCUREMENT','Accounts'))
            //$current_user->id==$recordModel->get('assigned_user_id')
        )?true:false;
        if($rechargesource=='COINRETURN'){
            $tempdata['vendorid']=$structuredValues['VENDOR_LBL_INFO']['vendorid'];
            $tempposition=array_search('servicecontractsid',array_keys($structuredValues['LBL_INFO']));
            $firstArray=array_splice($structuredValues['LBL_INFO'],0,$tempposition+1);
            $structuredValues['LBL_INFO']=array_merge($firstArray,$tempdata,$structuredValues['LBL_INFO']);
        }
        //$C_RECHARGESHEET=RefillApplication_Record_Model::getRechargeSheet($recordModel->entity->column_fields['record_id']);
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
        $viewer = $this->getViewer($request);
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('RECHARGESOURCE', $rechargesource);
        $viewer->assign('RECEIVESTATUS', $receiveStatus);
        $viewer->assign('REFUNDSORTRANSFERSTUTAS', $refundsOrTransfers);
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('PAYMENTSLIST', $recordModel->getMatchPaymentsList());
        $viewer->assign('REFUNDLIST',$recordModel->getRubricreChargesheet($recordId));
        if($rechargesource=='PACKVENDORS'){
            $viewer->assign('VENDORLIST',$recordModel->getDetailVendorList($recordId));
        }
        $revokeRelation=false;
        // cxh start 2020-04-15
        if(in_array($rechargesource,array('Vendors','Accounts'))){
            $revokeRelation=($moduleStatus=='c_complete'&&
                $recordModel->get('receivedstatus')!='virtualrefund' &&
                $current_user->id==$recordModel->get('assigned_user_id')
            )?true:false;
        //cxh end 2020-04-15
        }else if(in_array($rechargesource,array('NonMediaExtraction'))){
            $revokeRelation=($moduleStatus=='c_complete'&&
                $recordModel->get('isbackwash')==0 &&
                $recordModel->get('receivedstatus')!='virtualrefund' &&
                $current_user->id==$recordModel->get('assigned_user_id')
            )?true:false;
        }
        $viewer->assign('C_RECHARGESHEET', $rechargeSheet);
        $viewer->assign('INCOUNT', $incount);
        $viewer->assign('RECHARGESHEETCOUNT', $rechargeSheetCount);
        $viewer->assign('REVOKERELATION',$revokeRelation);
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        //$viewer->assign('C_RECHARGESHEET', $C_RECHARGESHEET);
        return $viewer->view('DetailViewFullContents.tpl',$moduleName,true);
    }


    /**
     * Function shows basic detail for the record
     * @param <type> $request
     */
    function showModuleBasicView($request) {

        $recordId = $request->get('record');
        $moduleName = $request->getModule();
        global $isallow,$current_user;
        if(in_array($moduleName, $isallow)){
            echo $this->getWorkflowsM($request);
        }
        if(!$this->record){
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId,true);
        }
        $recordModel = $this->record->getRecord();
        /*if($recordModel->get('financialstate')==1){
            $recordModel->set('grossadvances',0.00);
            $recordModel->set('totalrecharge',$recordModel->get('actualtotalrecharge'));
        }*/
        $detailViewLinkParams = array('MODULE'=>$moduleName,'RECORD'=>$recordId);
        $detailViewLinks = $this->record->getDetailViewLinks($detailViewLinkParams);
        $moduleModel = $recordModel->getModule();

        $modulestatus=$recordModel->get('modulestatus');
        $receiveStatus=($modulestatus=='c_complete'&&
            /*$recordModel->get('financialstate')==0 &&*/
            $recordModel->get('isbackwash')==0 &&
            $current_user->id==$recordModel->get('assigned_user_id')&&
            $recordModel->get('actualtotalrecharge')>$recordModel->get('totalrecharge')
        )?true:false;
        $rechargesource=$recordModel->get('rechargesource');
        $rechargesource=($rechargesource=='Accounts')?'Accounts':(($rechargesource=='Vendors')?'Vendors':
            ($rechargesource=='TECHPROCUREMENT'?'TECHPROCUREMENT':
                ($rechargesource=='PreRecharge'?'PreRecharge':
                    ($rechargesource=='OtherProcurement'?'OtherProcurement':
                        ($rechargesource=='NonMediaExtraction'?'NonMediaExtraction':
                            ($rechargesource=='PACKVENDORS'?'PACKVENDORS':
                                ($rechargesource=='COINRETURN'?'COINRETURN':
                                    ($rechargesource=='INCREASE'?'INCREASE':
                                        ($rechargesource=='contractChanges'?'contractChanges':
                                        'Accounts')))))))));
        $refundsOrTransfers=($modulestatus=='c_complete'&&
            //$moduleModel->exportGrouprt('RefillApplication',"dobackwash")&&
            $current_user->id==$recordModel->get('assigned_user_id') &&
            /*$recordModel->get('isbackwash')==0 &&*/
            /*$recordModel->get('financialstate')==0 &&*/
            in_array($rechargesource,array('Vendors','TECHPROCUREMENT','Accounts'))
            //$current_user->id==$recordModel->get('assigned_user_id')
        )?true:false;
        $detailDisplayList=array('productid'=>'topplatform','suppliercontractsid'=>'suppliercontractsname');
        $checkValueList=array('havesignedcontract','isprovideservice');
        $viewer = $this->getViewer($request);
        if($rechargesource=='contractChanges'){
            if(!empty($recordId)){
                $refillapplicationList = RefillApplication_Record_Model::getListChangeRefillapplicationDetail($request->get("record"));
                $viewer->assign('REFILLAPPLICATION_LIST',$refillapplicationList);
            }
        }

        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('MODULE_SUMMARY', $this->showModuleSummaryView($request));
        $viewer->assign('RECHARGESOURCE', $rechargesource);

        $viewer->assign('DISPLAYFIELD', array_keys($detailDisplayList));
        $viewer->assign('DISPLAYVALUE', $detailDisplayList);
        $viewer->assign('CHECKVALUELIST', $checkValueList);
        $viewer->assign('DETAILVIEW_LINKS', $detailViewLinks);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('PAYMENTSLIST', $recordModel->getMatchPaymentsList());
        $viewer->assign('REFUNDLIST',$recordModel->getRubricreChargesheet($recordId));
        $recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
        $structuredValues = $recordStrucure->getStructure();
        $viewer->assign('REFUNDSORTRANSFERSTUTAS', $refundsOrTransfers);
        $viewer->assign('RECEIVESTATUS', $receiveStatus);
        if($rechargesource=='PACKVENDORS'){
            $viewer->assign('VENDORLIST',$recordModel->getDetailVendorList($recordId));
        }
        $revokeRelation=false;
        if(in_array($rechargesource,array('Vendors','Accounts','NonMediaExtraction'))){
            $revokeRelation=($modulestatus=='c_complete'&&
                $recordModel->get('isbackwash')==0 &&
                $recordModel->get('receivedstatus')!='virtualrefund' &&
                $current_user->id==$recordModel->get('assigned_user_id')
            )?true:false;
        }
        $viewer->assign('REVOKERELATION',$revokeRelation);
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
        if($rechargesource=='COINRETURN'){
            $tempdata['vendorid']=$structuredValues['VENDOR_LBL_INFO']['vendorid'];
            $tempposition=array_search('servicecontractsid',array_keys($structuredValues['LBL_INFO']));
            $firstArray=array_splice($structuredValues['LBL_INFO'],0,$tempposition+1);
            $structuredValues['LBL_INFO']=array_merge($firstArray,$tempdata,$structuredValues['LBL_INFO']);
        }
        $viewer->assign('C_RECHARGESHEET', $rechargeSheet);
        $viewer->assign('INCOUNT', $incount);
        $viewer->assign('RECHARGESHEETCOUNT', $rechargeSheetCount);
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        echo $viewer->view('DetailViewSummaryContents.tpl', $moduleName, true);
    }


}
