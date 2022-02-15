<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class SContractNoGeneration_SelectAjax_Action extends Vtiger_Action_Controller {
	
	function __construct(){
		parent::__construct();
		$this->exposeMethod('getCheckData');
	}
	function checkPermission(Vtiger_Request $request) {
		return;
	}

	public function process(Vtiger_Request $request) {
        $mode = $request->getMode();
        if(!empty($mode) && $this->isMethodExposed($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
	}
	public function getCheckData(Vtiger_Request $request){
        $db=PearDatabase::getInstance();
        $servicecontractsruleid=$request->get('servicecontractsruleid');
        $company_code=$request->get('company_code');
        $products_code=$request->get('products_code');
        $query="SELECT * FROM `vtiger_servicecontracts_rule` WHERE servicecontractsruleid=?";
        $result=$db->pquery($query,array($servicecontractsruleid));
        $num=$db->num_rows($result);
        $arr=array();
        if($num){
            $row=$db->query_result_rowdata($result);
            if(empty($products_code)){
                $_POST['products_code']='产品编码';
            }else{
                $_POST['products_code']=$products_code;
            }
            if(empty($company_code)){
                $_POST['company_code']='公司编码';
            }else{
                $_POST['company_code']=$company_code;
            }
            $MosaicSql=SContractNoGeneration_Record_Model::getMosaicSql($row);
            $query='SELECT maxnumber FROM `vtiger_scontractnogeneration` WHERE 1=1'.$MosaicSql['sql'].' ORDER BY scontractnogenerationid DESC limit 1';
            $result=$db->pquery($query,array());
            $num=$db->num_rows($result);
            $str='1';
            $max_limit=str_pad($str,$row['number'],1,STR_PAD_LEFT);
            $max_limit=$max_limit*9;
            $arr['codeprefix']=$MosaicSql['codeprefix'].'流水长度'.$row['number'];
            $arr['products_codeflag']=$MosaicSql['products_codeflag'];
            if($num){
                $scrow=$db->query_result_rowdata($result);
                $maxnumber=$scrow['maxnumber'];
                $arr['max_limit']=$max_limit-$maxnumber;
            }else{
                $arr['max_limit']=$max_limit;
            }
        }


        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($arr);
        $response->emit();
	}
}
