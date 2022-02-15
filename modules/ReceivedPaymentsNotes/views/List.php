<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
//error_reporting(-1);
//ini_set("display_errors",1);
class ReceivedPaymentsNotes_List_View extends Vtiger_KList_View {
    function process (Vtiger_Request $request) {
        $strPublic = $request->get('public');
        if($strPublic == 'Unbound'){
            //回款解绑记录
            $viewer = $this->getViewer($request);
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            $this->viewName = $request->get('viewname');
            $viewer->assign('VIEWNAME', $this->viewName);

            if ($request->isAjax()) {
                $this->initializeListViewContents($request, $viewer);//竟然调用两次，这边其实是ajax调用的，哈哈！！
                $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
            }

            $viewer->assign('VIEW', $request->get('view'));
            $viewer->assign('MODULE_MODEL', $moduleModel);
            $viewer->view('ListViewContentsUnbound.tpl', $moduleName);
        }else{
            $viewer = $this->getViewer($request);
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            $this->viewName = $request->get('viewname');
            $viewer->assign('VIEWNAME', $this->viewName);

            if ($request->isAjax()) {
                $this->initializeListViewContents($request, $viewer);//竟然调用两次，这边其实是ajax调用的，哈哈！！
                $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
            }

            $viewer->assign('VIEW', $request->get('view'));
            $viewer->assign('MODULE_MODEL', $moduleModel);


            $viewer->view('ListViewContents.tpl', $moduleName);
        }
    }

    function preProcessTplName(Vtiger_Request $request=null) {
        $strPublic = $request->get('public');
        if($strPublic == 'Unbound'){
            return 'ListViewPreProcessUnbound.tpl';
        }
        return 'ListViewPreProcess.tpl';
    }


    function preProcess(Vtiger_Request $request, $display=true) {
        parent::preProcess($request, false);
        $viewer = $this->getViewer ($request);
        $moduleName = $request->getModule();
        global $current_user;
        $userId=$current_user->id;
        $viewer->assign('USERID',$userId);
        $listViewModel = Vtiger_ListView_Model::getInstance($moduleName);
        $linkParams = array('MODULE'=>$moduleName, 'ACTION'=>$request->get('view'));// module 和action
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        $recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($moduleModel);
        $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
        $viewer->assign('SEARCHRECORD_STRUCTURE', $moduleModel->getSearchFields());
        $viewer->assign('SEARCHRECORD_STRUCTURE_UNBOUND', $this->getUnboundSearchFields());
        $viewer->assign('MODULE_MODEL',$moduleModel);
        $viewer->assign('SOURCE_MODULE',$moduleName);

        $quickLinkModels = $listViewModel->getSideBarLinks($linkParams);
        $viewer->assign('QUICK_LINKS', $quickLinkModels);
        $this->initializeListViewContents($request, $viewer);//竟然调用两
        if($display) {
            $this->preProcessDisplay($request);
        }
    }

    public function getUnboundSearchFields(){
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $searchFields['paytitle']['fieldtype']='string';
        $searchFields['paytitle']['column']='paytitle';
        $searchFields['paytitle']['uitype']=1;
        $searchFields['paytitle']['id']=1;
        $searchFields['paytitle']['label']='paytitle';
        $searchFields['owncompany']['fieldtype']='string';
        $searchFields['owncompany']['column']='owncompany';
        $searchFields['owncompany']['uitype']=1;
        $searchFields['owncompany']['id']=2;
        $searchFields['owncompany']['label']='owncompany';
        $searchFields['paymentchannel']['fieldtype']='picklist';
        $searchFields['paymentchannel']['column']='paymentchannel';
        $searchFields['paymentchannel']['uitype']=16;
        $searchFields['paymentchannel']['id']=3;
        $searchFields['paymentchannel']['picklistvalues']=array("对公转账"=>"对公转账","支付宝转账"=>"支付宝转账","扫码"=>"扫码");
        $searchFields['paymentchannel']['label']='paymentchannel';
        $searchFields['paymentcode']['fieldtype']='string';
        $searchFields['paymentcode']['column']='paymentcode';
        $searchFields['paymentcode']['uitype']=1;
        $searchFields['paymentcode']['id']=4;
        $searchFields['paymentcode']['label']='paymentcode';
        $searchFields['reality_date']['fieldtype']='date';
        $searchFields['reality_date']['column']='reality_date';
        $searchFields['reality_date']['uitype']=6;
        $searchFields['reality_date']['id']=5;
        $searchFields['reality_date']['label']='reality_date';
        $searchFields['unit_price']['fieldtype']='currency';
        $searchFields['unit_price']['column']='unit_price';
        $searchFields['unit_price']['uitype']=155;
        $searchFields['unit_price']['id']=6;
        $searchFields['unit_price']['label']='unit_price';
        $searchFields['total']['fieldtype']='currency';
        $searchFields['total']['column']='total';
        $searchFields['total']['uitype']=155;
        $searchFields['total']['id']=7;
        $searchFields['total']['label']='total';
        $searchFields['staypaymentjine']['fieldtype']='currency';
        $searchFields['staypaymentjine']['column']='staypaymentjine';
        $searchFields['staypaymentjine']['uitype']=155;
        $searchFields['staypaymentjine']['id']=8;
        $searchFields['staypaymentjine']['label']='staypaymentjine';
        $searchFields['last_match_contract_no']['fieldtype']='string';
        $searchFields['last_match_contract_no']['column']='last_match_contract_no';
        $searchFields['last_match_contract_no']['uitype']=1;
        $searchFields['last_match_contract_no']['id']=9;
        $searchFields['last_match_contract_no']['label']='last_match_contract_no';
        $searchFields['last_match_time']['fieldtype']='date';
        $searchFields['last_match_time']['column']='last_match_time';
        $searchFields['last_match_time']['uitype']=6;
        $searchFields['last_match_time']['id']=10;
        $searchFields['last_match_time']['label']='last_match_time';
        $searchFields['match_contract_no']['fieldtype']='string';
        $searchFields['match_contract_no']['column']='match_contract_no';
        $searchFields['match_contract_no']['uitype']=1;
        $searchFields['match_contract_no']['id']=11;
        $searchFields['match_contract_no']['label']='match_contract_no';
        $searchFields['match_time']['fieldtype']='date';
        $searchFields['match_time']['column']='match_time';
        $searchFields['match_time']['uitype']=6;
        $searchFields['match_time']['id']=12;
        $searchFields['match_time']['label']='match_time';
        $searchFields['matcherid']['fieldtype']='owner';
        $searchFields['matcherid']['column']='matcherid';
        $searchFields['matcherid']['uitype']=53;
        $searchFields['matcherid']['id']=13;
        $searchFields['matcherid']['label']='matcherid';
        $searchFields['last_relive_time']['fieldtype']='date';
        $searchFields['last_relive_time']['column']='last_relive_time';
        $searchFields['last_relive_time']['uitype']=6;
        $searchFields['last_relive_time']['id']=14;
        $searchFields['last_relive_time']['label']='last_relive_time';
        $searchFields['last_reliverid']['fieldtype']='owner';
        $searchFields['last_reliverid']['column']='last_reliverid';
        $searchFields['last_reliverid']['uitype']=53;
        $searchFields['last_reliverid']['id']=15;
        $searchFields['last_reliverid']['label']='last_reliverid';
        $searchFields['relive_month']['fieldtype']='dateequal';
        $searchFields['relive_month']['column']='relive_month';
        $searchFields['relive_month']['uitype']=6;
        $searchFields['relive_month']['id']=16;
        $searchFields['relive_month']['label']='relive_month';
        $searchFields['relive_times_count']['fieldtype']='integer';
        $searchFields['relive_times_count']['column']='relive_times_count';
        $searchFields['relive_times_count']['uitype']=1;
        $searchFields['relive_times_count']['id']=17;
        $searchFields['relive_times_count']['label']='relive_times_count';
        $searchFields['is_last_overmonth_relive']['fieldtype']='boolean';
        $searchFields['is_last_overmonth_relive']['column']='is_last_overmonth_relive';
        $searchFields['is_last_overmonth_relive']['uitype']=56;
        $searchFields['is_last_overmonth_relive']['id']=18;
        $searchFields['is_last_overmonth_relive']['label']='is_last_overmonth_relive';
        $searchFields['overmonth_relive_times']['fieldtype']='integer';
        $searchFields['overmonth_relive_times']['column']='overmonth_relive_times';
        $searchFields['overmonth_relive_times']['uitype']=1;
        $searchFields['overmonth_relive_times']['id']=19;
        $searchFields['overmonth_relive_times']['label']='overmonth_relive_times';
        $searchFields['match_status']['fieldtype']='picklist';
        $searchFields['match_status']['column']='match_status';
        $searchFields['match_status']['uitype']=16;
        $searchFields['match_status']['id']=20;
        $searchFields['match_status']['label']='match_status';
        $searchFields['match_status']['picklistvalues']=array("已匹配"=>"已匹配","未匹配"=>"未匹配");
        $searchFields['matcherid']['picklistvalues']['userslist']=$searchFields['last_reliverid']['picklistvalues']['userslist']=$currentUser->getAccessibleUsers();
        return $searchFields;
    }
}