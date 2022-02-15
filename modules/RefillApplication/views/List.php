<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
class RefillApplication_List_View extends Vtiger_KList_View {
    function process (Vtiger_Request $request)
    {
        //parent::process();
        $strPublic = $request->get('public');
        if($strPublic == 'AuditSettings') {
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module��ص�����

            if (!$moduleModel->exportGrouprt('RefillApplication', 'AuditSettings')) {   //Ȩ����֤
                parent::process($request);
                return;
            }
            $viewer = $this->getViewer($request);
            $viewer->assign('USER', ReceivedPayments_Record_Model::getuserinfo(''));
           // $tt = ServiceContracts_Record_Model::getAuditsettings('RefillApplication');
            $viewer->assign('RECOEDS', ServiceContracts_Record_Model::getAuditsettings('RefillApplication'));
            $viewer->assign('DEPARTMENT', getDepartment());

            $viewer->assign('CLASSNAME', ServiceContracts_Record_Model::getSetPermissions());
            $viewer->view('auditSettings.tpl', $moduleName);
            exit;
        }
        if($strPublic == 'exportdata') {//自定义导出
            global $site_URL,$current_user;
            header('location:'.$site_URL.'temp/refillapplition'.$current_user->id.'.csv');
            exit;
        }
        if($strPublic == 'contractChangesExport'){
            $data=$this->getContractChangesExportData($request);
            RefillApplication_BasicAjax_Action::startToContractChangesExport($data);

        }

        if($strPublic == 'RefillDetailExport') {
            $moduleName = $request->getModule();
            //$moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module��ص�����

           /* if (!$moduleModel->exportGrouprt('RefillApplication', 'RefillDetailExport')) {   //Ȩ����֤
                parent::process($request);
                return;
            }*/
            $viewer = $this->getViewer($request);
            $viewer->view('RefillDetailExport.tpl', $moduleName);
            exit;
        }

        if($strPublic == 'RefillSumExport') {
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module��ص�����

            if (!$moduleModel->exportGrouprt('RefillApplication', 'RefillSumExport')) {   //Ȩ����֤
                parent::process($request);
                return;
            }
            $viewer = $this->getViewer($request);
            $viewer->view('RefillSumExport.tpl', $moduleName);
            exit;
        }
        if($strPublic == 'relationPaymentsExport') {
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);

            if (!$moduleModel->exportGrouprt('RefillApplication', 'relationPaymentsExport')) {
                parent::process($request);
                return;
            }
            $viewer = $this->getViewer($request);
            $viewer->view('relationPaymentsExport.tpl', $moduleName);
            exit;
        }
        //默认担保
        if($strPublic == 'rechargeguarantee') {
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);

            if (!$moduleModel->exportGrouprt('RefillApplication', 'rechargeguarantee')) {
                parent::process($request);
                return;
            }
            $recordModule=Vtiger_Record_Model::getCleanInstance("RefillApplication");
            $viewer = $this->getViewer($request);
            $viewer->assign('DEPARTMENT', getDepartment());
            $viewer->assign('UPDATETHIS', $moduleModel->exportGrouprt('RefillApplication', 'dorechargeguarantee'));
            $viewer->assign('USER', ReceivedPayments_Record_Model::getuserinfo(" and status='Active'"));
            $viewer->assign('LISTDUSER', $recordModule->getChargeGuarantee('rechargeguarantee'));
            $viewer->assign('DOMODULE', 'rechargeguarantee');
            $viewer->view('chargeguarantee.tpl', $moduleName);
            exit;
        }
        //技术充值设置
        if($strPublic == 'techprocurement') {
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);

            if (!$moduleModel->exportGrouprt('RefillApplication', 'rechargeguarantee')) {
                parent::process($request);
                return;
            }
            $recordModule=Vtiger_Record_Model::getCleanInstance("RefillApplication");
            $viewer = $this->getViewer($request);
            $viewer->assign('DEPARTMENT', getDepartment());
            $viewer->assign('UPDATETHIS', $moduleModel->exportGrouprt('RefillApplication', 'dorechargeguarantee'));
            $viewer->assign('DOMODULE', 'techprocurement');
            $viewer->assign('USER', ReceivedPayments_Record_Model::getuserinfo(" and status='Active'"));
            $viewer->assign('LISTDUSER', $recordModule->getChargeGuarantee('techprocurement'));
            $viewer->view('chargeguarantee.tpl', $moduleName);
            exit;
        }
        //其他充值设置
        if($strPublic == 'OtherProcurement') {
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);

            if (!$moduleModel->exportGrouprt('RefillApplication', 'rechargeguarantee')) {
                parent::process($request);
                return;
            }
            $recordModule=Vtiger_Record_Model::getCleanInstance("RefillApplication");
            $viewer = $this->getViewer($request);
            $viewer->assign('DEPARTMENT', getDepartment());
            $viewer->assign('UPDATETHIS', $moduleModel->exportGrouprt('RefillApplication', 'dorechargeguarantee'));
            $viewer->assign('DOMODULE', 'OtherProcurement');
            $viewer->assign('USER', ReceivedPayments_Record_Model::getuserinfo(" and status='Active'"));
            $viewer->assign('LISTDUSER', $recordModule->getChargeGuarantee('OtherProcurement'));
            $viewer->view('chargeguarantee.tpl', $moduleName);
            exit;
        }
        //预充值设置
        if($strPublic == 'PreRecharge') {
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);

            if (!$moduleModel->exportGrouprt('RefillApplication', 'rechargeguarantee')) {
                parent::process($request);
                return;
            }
            $recordModule=Vtiger_Record_Model::getCleanInstance("RefillApplication");
            $viewer = $this->getViewer($request);
            $viewer->assign('DEPARTMENT', getDepartment());
            $viewer->assign('DOMODULE', 'PreRecharge');
            $viewer->assign('UPDATETHIS', $moduleModel->exportGrouprt('RefillApplication', 'dorechargeguarantee'));
            $viewer->assign('USER', ReceivedPayments_Record_Model::getuserinfo(" and status='Active'"));
            $viewer->assign('LISTDUSER', $recordModule->getChargeGuarantee('PreRecharge'));
            $viewer->view('chargeguarantee.tpl', $moduleName);
            exit;
        }
        //客户担保
        if($strPublic == 'accountrechargeguarantee') {
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//

            if (!$moduleModel->exportGrouprt('RefillApplication', 'rechargeguarantee')) {
                parent::process($request);
                return;
            }
            $recordModule=Vtiger_Record_Model::getCleanInstance("RefillApplication");
            $viewer = $this->getViewer($request);
            $viewer->assign('UPDATETHIS', $moduleModel->exportGrouprt('RefillApplication', 'dorechargeguarantee'));
            $viewer->assign('USER', ReceivedPayments_Record_Model::getuserinfo(" and status='Active'"));
            $viewer->assign('LISTDUSER', $recordModule->getAccountChargeGuarantee());
            $viewer->view('accountchargeguarantee.tpl', $moduleName);
            exit;
        }
        //红冲明细导出
        if($strPublic == 'hcDetailsExport') {
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
            if (!$moduleModel->exportGrouprt('RefillApplication', 'hcDetailsExport')) {
                parent::process($request);
                return;
            }
            $recordModule=Vtiger_Record_Model::getCleanInstance("RefillApplication");
            $viewer = $this->getViewer($request);
            $viewer->assign('UPDATETHIS', $moduleModel->exportGrouprt('RefillApplication', 'dorechargeguarantee'));
            $viewer->assign('USER', ReceivedPayments_Record_Model::getuserinfo(" and status='Active'"));
            $viewer->assign('LISTDUSER', $recordModule->getAccountChargeGuarantee());
            $viewer->view('hcDetailsExport.tpl', $moduleName);
            exit;
        }
        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module��ص�����
        $this->viewName = $request->get('viewname');
        $viewer->assign('VIEWNAME', $this->viewName);

        if ($request->isAjax()) {
            $this->initializeListViewContents($request, $viewer);//��Ȼ�������Σ������ʵ��ajax���õģ���������
            $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
        }
        if(isset($_REQUEST['is_advances'])){
            $viewer->assign('is_advances', $_REQUEST['is_advances']);
            $viewer->assign('contract_no', $_REQUEST['contract_no']);
            $viewer->assign('userid', $_REQUEST['userid']);
        }
        $viewer->assign('RECHARGESOURCE', $_REQUEST['rechargesource']);
        $viewer->assign('VIEW', $request->get('view'));
        $viewer->assign('MODULE_MODEL', $moduleModel);
        $viewer->assign('DOCANCEL',$moduleModel->exportGrouprt($moduleName,'docancel'));


        $viewer->view('ListViewContents.tpl', $moduleName);
    }

    /*
     *
     * 得到要导出数据列表
     */
    public function getContractChangesExportData(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $cvId = 0;

        $pageNumber = $request->get('page');//页数
        $orderBy = $request->get('orderby');//排序
        $sortOrder = $request->get('sortorder');//排序
        $pageLimit = $request->get('limit');//排序
        if($sortOrder == "ASC"){
            $nextSortOrder = "DESC";
            $sortImage = "icon-chevron-down";
        }else{
            $nextSortOrder = "ASC";
            $sortImage = "icon-chevron-up";
        }

        if(empty ($pageNumber)){
            $pageNumber = '1';
        }
        //20150416 young 每页显示数量
        if(empty ($pageLimit)){
            $pageLimit = '20';
        }
        $listViewModel = Vtiger_ListView_Model::getInstance($moduleName, $cvId);//初始化各种数据,在这里其实初始化的是module_listview_model类，次类又同时将QueryGenerator,CustomView包含了

        $linkParams = array('MODULE'=>$moduleName, 'ACTION'=>$request->get('view'), 'CVID'=>$cvId);

        $pagingModel = new Vtiger_Paging_Model();   //分页
        $pagingModel->set('page', $pageNumber);
        $pagingModel->set('limit', $pageLimit);//20150416 young 每页显示数量
        if(!empty($orderBy)) {
            $listViewModel->set('orderby', $orderBy);
            $listViewModel->set('sortorder',$sortOrder);
        }

        $searchKey = $request->get('search_key');
        $searchValue = $request->get('search_value');
        $operator = $request->get('operator');
        if(!empty($operator)) {
            $listViewModel->set('operator', $operator);
        }
        if(!empty($searchKey) && !empty($searchValue)) {
            $listViewModel->set('search_key', $searchKey);
            $listViewModel->set('search_value', $searchValue);
        }
        $this->listViewEntries = $listViewModel->getListViewEntries($pagingModel);
        return $this->listViewEntries;
    }

}