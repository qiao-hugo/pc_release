<?php

class InputInvoice_Save_Action extends Vtiger_Save_Action
{
    private $specialWorkFlowSid=2691499;
    private $noSpecialWorkFlowSid=2691506;
    private $afterHandSubWorkFlowSid=2691497;
    public function saveRecord($request) {
        global $current_user,$isallow;
        $request->set("createid",$current_user->id);
        if(!$request->get('smownerid')){
            $request->set("smownerid",$current_user->id);
        }

        $invoicecompany = $request->get("invoicecompany");
        $companyCode = InputInvoice_Record_Model::getInvoiceCompanyCode($invoicecompany);
//        $request->set("companycode",$companyCode);
        $billproperty = $request->get('billproperty');
        $billinvoicetype = $request->get('billinvoicetype');
        if($billproperty=='BeforeHandSub'){
            if($billinvoicetype=='SpecialInvoice'){
                $workflowsid = $this->specialWorkFlowSid;
                $workflowsnode = '发票签收-增值税专用发票';
            }else{
                $workflowsid = $this->noSpecialWorkFlowSid;
                $workflowsnode = '发票签收-非增值税专用发票';
            }
        }else{
            $workflowsid = $this->afterHandSubWorkFlowSid;
            $workflowsnode = '发票签收-事后提交';
        }
//        $request->set("createid",$current_user->id);
//        $request->set('workflowsid', $workflowsid);
//        $request->set('workflowstime', date('Y-m-d H:i:s'));
//        $request->set('workflowsnode', $workflowsnode);
        $recordModel = $this->getRecordModelFromRequest($request);

        $recordModel->save();
        $recordId = $recordModel->getId();
        global $adb;
        $adb->pquery("update vtiger_input_invoice set createid=?,workflowsid=?,workflowstime=?,workflowsnode=?,createdate=?,companycode=? where inputinvoiceid=?",
            array($current_user->id,$workflowsid,date('Y-m-d H:i:s'),$workflowsnode,date('Y-m-d H:i:s'),$companyCode,$recordId));

        $_REQUEST['workflowsid']=$workflowsid;
        //生成工作流
        $focus = CRMEntity::getInstance('InputInvoice');
        $focus->makeWorkflows('InputInvoice', $_REQUEST['workflowsid'], $recordId,'edit');
        return $recordModel;
    }
}