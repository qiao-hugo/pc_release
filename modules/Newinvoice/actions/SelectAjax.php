<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * 
 *************************************************************************************/

class Newinvoice_SelectAjax_Action extends Vtiger_Action_Controller {
    public function __construct(){
        parent::__construct();
        $this->exposeMethod('autofillBilling');
    }
    function checkPermission(Vtiger_Request $request) {
        return true;
    }
    public function process(Vtiger_Request $request) {
		$mode=$request->getMode();
        if(!empty($mode)){
            echo $this->invokeExposedMethod($mode,$request);
            exit;
        }
	}
    //打开时加载
    public function autofillBilling(Vtiger_Request $request){
        $accountid=abs((int)$request->get('accountid'));
        $db=PearDatabase::getInstance();
        $query="SELECT taxpayers_no,registeraddress,depositbank,telephone,accountnumber,isformtable,businessnamesone FROM vtiger_billing WHERE vtiger_billing.accountid=?";
        //echo $query;
        $result=$db->pquery($query,array($accountid));
        $num=$db->num_rows($result);
        $arr=array();
        //清除多空格,'",这些符串会影响前端Json的解析
        $search ='/ |\s|\'|\"|(&quot;)|(&#039;)|\/|&nbsp;|　|\x{3000}|\x{00a0}|\x{0020}|[\n\t\r]|&nbsp;/u';
        $replace = "";
        if($num>0){
            for($i=0;$i<$num;$i++){
                $arr[$i]['taxpayers_no']=str_replace('\\','',preg_replace($search,$replace,$db->query_result($result,$i,'taxpayers_no')));
                $arr[$i]['registeraddress']=str_replace('\\','',preg_replace($search,$replace,$db->query_result($result,$i,'registeraddress')));
                $arr[$i]['depositbank']=str_replace('\\','',preg_replace($search,$replace,$db->query_result($result,$i,'depositbank')));
                $arr[$i]['telephone']=str_replace('\\','',preg_replace($search,$replace,$db->query_result($result,$i,'telephone')));
                $arr[$i]['accountnumber']=str_replace('\\','',preg_replace($search,$replace,$db->query_result($result,$i,'accountnumber')));
                $arr[$i]['businessnamesone']=str_replace('\\','',preg_replace($search,$replace,$db->query_result($result,$i,'businessnamesone')));
                $arr[$i]['isformtable']=str_replace('\\','',preg_replace($search,$replace,$db->query_result($result,$i,'isformtable')));
            }
        }
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($arr);
        $response->emit();
    }

}
