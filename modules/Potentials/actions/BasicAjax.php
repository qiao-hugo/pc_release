<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Potentials_BasicAjax_Action extends Vtiger_Action_Controller {
	function __construct() {
		parent::__construct();
		$this->exposeMethod('getProduct');
		$this->exposeMethod('getRelateProduct');
        $this->exposeMethod('delPotentialDetailOne');
	}
	function checkPermission(Vtiger_Request $request) {
		return;
	}

	public function process(Vtiger_Request $request) {
		
		$moduleName = $request->get('module');
		$recordId = $request->get('record');
		$mode = $request->getMode();
		$result=array();
		if(!empty($mode)) {
			$result = $this->invokeExposedMethod($mode, $request);
			return;
		}else{
			$result=Products_Record_Model::getRelateProduct($recordId,$moduleName);
			
		}
		//print_r($return);
		$response = new Vtiger_Response();
		$response->setResult($result);
		$response->emit();
	}
	function getProduct(Vtiger_Request $request){
		$recordId = $request->get('record');
		$moduleName = $request->get('module');
		
		
	}
	function getRelateProduct(Vtiger_Request $request){
		$recordId = $request->get('record');
		$moduleName = $request->get('module');
		$result=array();
		$result=Products_Record_Model::getRelateProduct($recordId,$moduleName);
		if(!empty($result)){
			foreach($result as $key=>$val){
				$productModel = Vtiger_Record_Model::getInstanceById($val['productid'], 'Products');
				$subproduct=$productModel->getSubProducts();
				$temp=array();
				if(!empty($subproduct)){
					foreach($subproduct as $subval){
						$temp[$subval->getId()]=$subval->getName();
					}
				}
				$result[$key]['subproducts'] = $temp;
				unset($temp);
			}
		}	
		return $result;
	}
    /**
     * 删除一条销售/项目机会详情
     * @author: cuixiaohuai
     * @Date: 2019-06-25
     */
    public function delPotentialDetailOne(Vtiger_Request $request){
        $recordModel=Vtiger_Record_Model::getCleanInstance('Potentials');
        $data=$recordModel->delPotentialDetailOne($request);
        //return $data;
        $response=new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
}
