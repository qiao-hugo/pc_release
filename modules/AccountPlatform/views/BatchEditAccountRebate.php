<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
class AccountPlatform_BatchEditAccountRebate_View extends Vtiger_Export_View {
    function __construct() {
        parent::__construct();
    }

    function checkPermission(Vtiger_Request $request) {
        return true;
    }

    function process(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $public=$request->get('public');
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
        if(!$moduleModel->exportGrouprt('AccountPlatform','BatchEditAccountRebate')){   //权限验证
            parent::process($request);
            return;
        }
        if($public=='findNum'){
            $this->findNum($request);
            exit();
        }
        if($public=='change'){
            $this->change($request);
            exit();
        }
        $viewer = $this->getViewer($request);
        //获取所有工作流
        $viewer->assign('workflowList', $this->getAccountWorkflowList());
        $viewer->assign('productList',  $this->getProductListList());
        $viewer->view('BatchEditAccountRebate.tpl', $moduleName);
    }

    /**
     * 获取工作流列表
     * @return array
     */
    public function getAccountWorkflowList(){
        $db=PearDatabase::getInstance();
        $sql="select workflowsid,workflowsname from vtiger_workflows where mountmodule='AccountPlatform'";
        $accountWorkflowList=$db->run_query_allrecords($sql);
        return $accountWorkflowList;
    }

    /**
     * 获取产品列表
     * @return array
     */
    public function getProductListList(){
        $db=PearDatabase::getInstance();
        $sql="select productid,productname from vtiger_products where productiscriminate='outpurchase'";
        $productList=$db->run_query_allrecords($sql);
        return $productList;
    }


    /**
     * 查询账号数量
     * @param $request
     */
    public function findNum($request){
        global $current_user;
        $db=PearDatabase::getInstance();
        $workflow=$request->get('workflow');
        $accountid=$request->get('accountid');
        $product=$request->get('product');
        $accountRebateType=$request->get('accountRebateType');
        $discount=$request->get('discount');
        $sql="select * from vtiger_accountplatform t1 left join vtiger_crmentity t2 on t1.accountplatformid=t2.crmid  where t1.workflowsid=? and t1.accountid=? and t1.productid=? and t1.accountrebatetype=? and t1.accountrebate=? and t2.setype='AccountPlatform' and t2.smownerid=?";
        $result=$db->pquery($sql,array($workflow,$accountid,$product,$accountRebateType,$discount,$current_user->id));
        $response = new Vtiger_Response();
        $response->setResult(array('num'=>$db->num_rows($result)));
        $response->emit();
    }

    /**
     * 修改客户返利
     * @param $request
     */
    public function change($request){
        global $current_user;
        $db=PearDatabase::getInstance();
        $workflow=$request->get('workflow');
        $accountid=$request->get('accountid');
        $product=$request->get('product');
        $accountRebateType=$request->get('accountRebateType');
        $discount=$request->get('discount');
        $accountRebate=$request->get('accountRebate');
        $sql="update vtiger_accountplatform t1 left join vtiger_crmentity t2 on t1.accountplatformid=t2.crmid set t1.accountrebate=? where t1.workflowsid=? and t1.accountid=? and t1.productid=? and t1.accountrebatetype=? and t1.accountrebate=? and t2.setype='AccountPlatform' and t2.smownerid=? ";
        $db->pquery($sql,array($accountRebate,$workflow,$accountid,$product,$accountRebateType,$discount,$current_user->id));
        $response = new Vtiger_Response();
        $response->setResult(array());
        $response->emit();
    }

}
