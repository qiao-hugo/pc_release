<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class AccountsMerge_BasicAjax_Action extends Vtiger_Action_Controller {
	function __construct() {
		parent::__construct();
        $this->exposeMethod('getOriginalAccountCheck');
	}

	function checkPermission(Vtiger_Request $request) {
		return;
	}
    /**
     * @param Vtiger_Request $request
     * @throws Exception
     */
    public function process(Vtiger_Request $request) {
        $mode = $request->getMode();
        if(!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
            return;
        }
    }

    /**
     * @param Vtiger_Request $request
     *  原客户发票验证，原客户服务合同验证，原客户激活码验证 。
     *
     */
    public function getOriginalAccountCheck(Vtiger_Request $request) {
        $db = PearDatabase::getInstance();
        $related_to = $request->get("related_to");
        $return=array("success"=>true);
            /*$remark='合并出错!原因:';
            if($_REQUEST['related_to']== $_REQUEST['accountid']){
                $remark.='同一个客户不可以合并!';
            }
            if($_REQUEST['contacts']!='on' && $_REQUEST['salesorderproductsrel']!='on'){
                $remark.='至少要选择一项合并项!';
            }
            if($_REQUEST['related_to']<=0 || $_REQUEST['accountid']<=0){
                $remark.='不正确的操作或非法操作!';
            }
            $sql="update vtiger_accountsmerge set createdtime=?,remark=?,smownerid=?  where accountsmergeid=?";
            $this->db->pquery($sql,array($datetime,$remark,$current_user->id,$this->id));*/
        do{
            if($related_to){
                $query='SELECT 1 FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_servicecontracts.servicecontractsid WHERE vtiger_crmentity.deleted=0 AND vtiger_servicecontracts.modulestatus<>\'c_cancel\' AND vtiger_servicecontracts.sc_related_to=?';
                $result=$db->pquery($query,array($related_to));
                if($db->num_rows($result)){
                    $return = array("success"=>false,"message"=>'源客户存在相关的合同,请先处理,再合并');
                    continue;
                }
                $query='SELECT 1 FROM vtiger_activationcode WHERE `status`<>2 AND customerid=?';
                $result=$db->pquery($query,array($related_to));
                if($db->num_rows($result)){
                    $return = array("success"=>false,"message"=>'源客户领取过激活码，不允许被合并');
                    continue;
                }
                $query='SELECT 1 FROM vtiger_newinvoice LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_newinvoice.invoiceid WHERE vtiger_crmentity.deleted=0 AND vtiger_newinvoice.modulestatus<>\'c_cancel\' AND vtiger_newinvoice.accountid=?';
                $result=$db->pquery($query,array($related_to));
                if($db->num_rows($result)){
                    $return = array("success"=>false,"message"=>'源客户存在发票,请先处理,再合并');
                    continue;
                }
                $query='SELECT 1 FROM vtiger_invoice LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_invoice.invoiceid WHERE vtiger_crmentity.deleted=0 AND vtiger_invoice.modulestatus<>\'c_cancel\' AND vtiger_invoice.accountid=?';
                $result=$db->pquery($query,array($related_to));
                if($db->num_rows($result)){
                    $return = array("success"=>false,"message"=>'源客户存在发票,请先处理,再合并');
                    continue;
                }
            }
        }while(false);

        $response = new Vtiger_Response();
        $response->setResult($return);
        $response->emit();
    }

}
