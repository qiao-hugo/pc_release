<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class SupplierContracts_Detail_View extends Vtiger_Detail_View {
	/* function preProcess(Vtiger_Request $request) {
		$viewer = $this->getViewer($request);
		$viewer->assign('NO_SUMMARY', true);
		parent::preProcess($request);
	} */
    function __construct(){
        parent::__construct();
        $this->exposeMethod('getservicecontractsinfo');
    }
	/**
	 * Function returns Inventory details
	 * @param Vtiger_Request $request
	 */
/* 	function showModuleDetailView(Vtiger_Request $request) {
		echo $this->getWorkflowsM($request);

		//echo parent::showModuleDetailView($request);

		$recordId = $request->get('record');
		$moduleName = $request->getModule();

		if(!$this->record){
			$this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
		}
		$recordModel = $this->record->getRecord();

		$detailViewLinkParams = array('MODULE'=>$moduleName,'RECORD'=>$recordId);
		$detailViewLinks = $this->record->getDetailViewLinks($detailViewLinkParams);
		$workflowsModel=Vtiger_Record_Model::getInstanceById($recordModel->get('workflowsid'),'Workflows');



		$viewer = $this->getViewer($request);
		$viewer->assign('RECORD', $recordModel);
		$viewer->assign('WORKFLOWS', $workflowsModel->getData());
		$viewer->assign('MODULE_SUMMARY', $this->showModuleSummaryView($request));

		$viewer->assign('DETAILVIEW_LINKS', $detailViewLinks);
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
		$viewer->assign('MODULE_NAME', $moduleName);

		$recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
		$structuredValues = $recordStrucure->getStructure();

		$moduleModel = $recordModel->getModule();

		$viewer->assign('RECORD_STRUCTURE', $structuredValues);
		$viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());

		$viewer->view('DetailViewSummaryContents.tpl', $moduleName);


		$this->showLineItemDetails($request);

	} */

	/**
	 * Function returns Inventory details
	 * @param Vtiger_Request $request
	 * @return type
	 */
	/* function showDetailViewByMode(Vtiger_Request $request) {
		return $this->showModuleDetailView($request);
	} */
    function showModuleDetailView(Vtiger_Request $request) {
        $recordId = $request->get('record');
        $moduleName = $request->getModule();
        //young.yang 2014-12-26 ?????????
        global $isallow;
        if(in_array($moduleName, $isallow)){
            echo $this->getWorkflowsM($request);
        }
        //end
        if(!$this->record){
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }
        $recordModel = $this->record->getRecord();
        $recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
        //var_dump($recordStrucure);die;
        $structuredValues = $recordStrucure->getStructure();


        $moduleModel = $recordModel->getModule();
        $record = $request->get('record');
        $contracts_divide = ServiceContracts_Record_Model::servicecontracts_divide($record);

        $viewer = $this->getViewer($request);
        $viewer->assign('CONTRACTS_DIVIDE',$contracts_divide); //???????????????
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        $viewer->assign('RSTATEMENT', $recordModel->getReimbursementStatement($recordId));
        $viewer->assign('ADDRSTATEMENT', $recordModel->personalAuthority('SupplierContracts','ADDRSTATEMENT'));
        $payApplyRecordModel = PayApply_Record_Model::getCleanInstance("PayApply");
        $payapplyids = explode(',',$recordModel->get('payapplyids'));
        $viewer->assign('PAYAPPLYLIST', $payApplyRecordModel->getPayApplyListByIds($payapplyids));
        $viewer->assign('SHOWBANKINFO', $recordModel->get("vendorid")>0?1:0);
        $viewer->assign('SHOWCOMPAREFILE', $recordModel->get("file")?1:0);
        /*// 2020-01-06  cxh  ?????????????????????????????????????????? ?????????????????????????????????????????? ?????? $recordId ?????????????????????????????????id
        $db = PearDatabase::getInstance();
        //die();
        $DetailQuery=$query="SELECT suppliercontractsid FROM `vtiger_suppcontractsagreement` WHERE  newservicecontractsid=? LIMIT 1 ";
        $DetailResult = $db->pquery($DetailQuery, array($recordId));
        $result = $db->query_result_rowdata($DetailResult,0);
        if(!empty($result) && !empty($result['suppliercontractsid'])){
            $recordId=$result['suppliercontractsid'];
        }*/
        // ????????????????????????
        $vendorsrebateData = $recordModel->getVendorsrebate($recordId);
        $viewer->assign('VENDORSREBATEDATA', $vendorsrebateData);
        return $viewer->view('DetailViewFullContents.tpl',$moduleName,true);
    }
	function showModuleBasicView($request) {
		return $this->showModuleDetailView($request);
	}
	/**
	 * Function returns Inventory Line Items
	 * @param Vtiger_Request $request
	 */
	function getProducts(Vtiger_Request $request) {
		$record = $request->get('record');
		$products=ServiceContracts_Record_Model::getProductsById($record);
		if($products){
			//error_reporting(E_ALL);
			$viewer = $this->getViewer($request);
			$viewer->assign('Product',$products);
			$viewer->view('LineItemsDetail.tpl','ServiceContracts');
		}
		/* return false;
		$recordModel = SalesOrder_Record_Model::getInstanceById($record);
		$relatedProducts = $recordModel->getProducts($record);
		//##Final details convertion started
		$finalDetails = $relatedProducts[1]['final_details'];

		//Final tax details convertion started
		$taxtype = $finalDetails['taxtype'];
		if ($taxtype == 'group') {
			$taxDetails = $finalDetails['taxes'];
			$taxCount = count($taxDetails);
			for($i=0; $i<$taxCount; $i++) {
				$taxDetails[$i]['amount'] = Vtiger_Currency_UIType::transformDisplayValue($taxDetails[$i]['amount'], null, true);
			}
			$finalDetails['taxes'] = $taxDetails;
		}
		//Final tax details convertion ended

		//Final shipping tax details convertion started
		$shippingTaxDetails = $finalDetails['sh_taxes'];
		$taxCount = count($shippingTaxDetails);
		for($i=0; $i<$taxCount; $i++) {
			$shippingTaxDetails[$i]['amount'] = Vtiger_Currency_UIType::transformDisplayValue($shippingTaxDetails[$i]['amount'], null, true);
		}
		$finalDetails['sh_taxes'] = $shippingTaxDetails;
		//Final shipping tax details convertion ended

		$currencyFieldsList = array('adjustment', 'grandTotal', 'hdnSubTotal', 'preTaxTotal', 'tax_totalamount',
				'shtax_totalamount', 'discountTotal_final', 'discount_amount_final', 'shipping_handling_charge', 'totalAfterDiscount');
		foreach ($currencyFieldsList as $fieldName) {
			$finalDetails[$fieldName] = Vtiger_Currency_UIType::transformDisplayValue($finalDetails[$fieldName], null, true);
		}

		$relatedProducts[1]['final_details'] = $finalDetails;
		//##Final details convertion ended

		//##Product details convertion started
		$productsCount = count($relatedProducts);
		for ($i=1; $i<=$productsCount; $i++) {
			$product = $relatedProducts[$i];

			//Product tax details convertion started
			if ($taxtype == 'individual') {
				$taxDetails = $product['taxes'];
				$taxCount = count($taxDetails);
				for($j=0; $j<$taxCount; $j++) {
					$taxDetails[$j]['amount'] = Vtiger_Currency_UIType::transformDisplayValue($taxDetails[$j]['amount'], null, true);
				}
				$product['taxes'] = $taxDetails;
			}
			//Product tax details convertion ended

			$currencyFieldsList = array('taxTotal', 'netPrice', 'listPrice', 'unitPrice', 'productTotal',
					'discountTotal', 'discount_amount', 'totalAfterDiscount');
			foreach ($currencyFieldsList as $fieldName) {
				$product[$fieldName.$i] = Vtiger_Currency_UIType::transformDisplayValue($product[$fieldName.$i], null, true);
			}

			$relatedProducts[$i] = $product;
		}
		//##Product details convertion ended

		$viewer = $this->getViewer($request);
		$viewer->assign('RELATED_PRODUCTS', $relatedProducts);
		$viewer->assign('RECORD', $recordModel);
		$viewer->assign('MODULE_NAME',$moduleName);


		$viewer->view('LineItemsDetail.tpl', $moduleName); */
	}
	/**
	 * !CodeTemplates.overridecomment.nonjd!
	 * @see Vtiger_Detail_View::getProductById()
	 */
	function getProductById(Vtiger_Request $request){
		$recordId = $request->get('record');
		$moduleName = $request->get('module');
		$relateModuleName= $request->get('relate_module');
		$result=array();
		$productModel = Vtiger_Record_Model::getInstanceById($recordId, 'Products');
		$subproduct=$productModel->getSubProducts();
		$productResult=$productModel->getData();
		//print_r($subproduct);die();
		
		$result = subproducts($productResult,$subproduct);
		
		$viewer = $this->getViewer($request);
		$viewer->assign('RELATED_PRODUCTS',$result);
		$viewer->assign('MODULE',$moduleName);
		echo $viewer->view('LineItemsProducts.tpl',$moduleName,true);
	}
	/**
	 * !CodeTemplates.overridecomment.nonjd!
	 * @see Vtiger_Detail_View::getProductBySalesorderid()
	 */
	public function getProductBySalesorderid(Vtiger_Request $request){
		$recordId = $request->get('record');
		$moduleName = $request->get('module');
		$relateModuleName= $request->get('relate_module');
		$result=array();
		
		
		$result=SalesorderProductsrel_Record_Model::getRelateProduct($recordId);

		$viewer = $this->getViewer($request);
		
		$viewer->assign('RELATED_PRODUCTS',$result);
		$viewer->assign('MODULE',$moduleName);
		echo $viewer->view('LineItemsProducts.tpl',$moduleName,true);
	}

	/*wangbin 2015???5???30???17:20:39
	 * ????????????id???????????????????????????????????????????????????
	 * ????????????????????????ajax????????????
	 * module=ServiceContracts&view=Detail&mode=getservicecontractsinfo&record=368826&receivepayid=604
	 * */
	public function getservicecontractsinfo(Vtiger_Request $request){
        //echo $request->getModule();die;
	    $requestmodule = $request->get('requestmodule');
	     $servicecontractsid =(int)$request->get('record');
	    $receivepayid = (int)$request->get('receivepayid');
	    $db=PearDatabase::getInstance();
	    /*2015???5???27??? ????????? */
	    $productsql = "SELECT vtiger_salesorderproductsrel.salesorderproductsrelid, IFNULL(( SELECT SUM(alreadyprice) FROM vtiger_receivementproducts AS aa WHERE aa.serviceid = vtiger_salesorderproductsrel.servicecontractsid AND aa.productsid = vtiger_salesorderproductsrel.productid AND aa.receivedpaymentid != ? ), 0 ) AS already, IFNULL(( SELECT alreadyprice FROM vtiger_receivementproducts AS aa WHERE aa.serviceid = vtiger_salesorderproductsrel.servicecontractsid AND aa.productsid = vtiger_salesorderproductsrel.productid AND aa.receivedpaymentid = ? LIMIT 1 ), 0 ) AS alreadyprice, vtiger_products.productid, vtiger_products.productname, vtiger_salesorderproductsrel.marketprice FROM vtiger_salesorderproductsrel LEFT JOIN vtiger_products ON vtiger_salesorderproductsrel.productid = vtiger_products.productid LEFT JOIN vtiger_receivementproducts ON vtiger_salesorderproductsrel.salesorderproductsrelid = vtiger_receivementproducts.salesorderproductsid WHERE vtiger_salesorderproductsrel.servicecontractsid = ? GROUP BY vtiger_salesorderproductsrel.productid";
	    $productresult = $db->pquery($productsql,array($receivepayid,$receivepayid,$servicecontractsid));
	    if($db->num_rows($productresult)>0){
	        for ($i=0;$i<$db->num_rows($productresult);++$i){
	            $productlis[] = $db->fetchByAssoc($productresult);
	        }
	    }
        //????????????????????????
	    $sql4 = "SELECT unit_price FROM vtiger_receivedpayments WHERE receivedpaymentsid = ?";
	    $unit_price = $db->pquery($sql4,array($receivepayid))->fields['0']; //??????????????????
	    //????????????????????????????????????????????????;
	    $mark= 0 ;
	    $alprice= 0 ;
	    $alreadyprice = 0;
	    foreach ($productlis as $key){
	         $mark += $key['marketprice']; //???????????????;
	         $alprice +=$key['already']; //??????????????????;
	         $alreadyprice +=$key['alreadyprice']; //????????????;
	         $thisproductremain = $key['marketprice']-$key['already'];//????????????????????????
	         
	    }
	    
	    foreach ($productlis as $keys=>$val){
	         //???????????????????????????????????????
	        $productfenchen =  $unit_price*(($val['marketprice']-$val['already'])/($mark-$alprice));//???????????????????????? = ?????????????????????/????????????????????????*??????????????????
	        $productlis[$keys]['fenchen'] = sprintf("%0.2f",$productfenchen); 
	    }
	       if($unit_price-$alreadyprice){
	           $after = $unit_price-$alreadyprice;
	       }else{
	           $after = "0.00";
	       };
	       
	       if($after<0){
	           $after = "???????????????";
	       }
	    //end
	    //wangbin ????????????id ????????????????????????????????????????????????????????????
	    $sql2 = "SELECT * FROM vtiger_receivedpayments WHERE relatetoid = ?";
	    $receivedresult = $db->pquery($sql2,array($servicecontractsid));
	    if($db->num_rows($receivedresult)>0){
	        for ($i=0;$i<$db->num_rows($receivedresult);++$i){
	            $receivedlis[] = $db->fetchByAssoc($receivedresult);
	        }
	    }
	    
	    $subtract =  sprintf("%0.2f",$mark-$alprice); 
	    $viewer = $this->getViewer($request);
        $viewer->assign("afterSUBTRACT",$after);//??????????????????
	    $viewer->assign("SUBTRACT",$subtract);  //????????????????????????
	    $viewer->assign('PRICETOTAL',$alprice);
	    $viewer->assign('RECEIVEDHISTORY',$receivedlis);
	    $viewer->assign(PRODUCTLIS,$productlis);
	    //???????????????;
	    $viewer->assign(ISCHUNA,$request->get('ischuna'));
	    $viewer->view('EditViewReceivementhistory.tpl',$requestmodule);
	}


	/**
	 * Function to get the list of Script models to be included
	 * @param Vtiger_Request $request
	 * @return <Array> - List of Vtiger_JsScript_Model instances
	 */
/* 	function getHeaderScripts(Vtiger_Request $request) {
		$headerScriptInstances = parent::getHeaderScripts($request);
	
		$moduleName = $request->getModule();
	
		//Added to remove the module specific js, as they depend on inventory files
		$modulePopUpFile = 'modules.'.$moduleName.'.resources.Popup';
		$moduleEditFile = 'modules.'.$moduleName.'.resources.Edit';
		$moduleDetailFile = 'modules.'.$moduleName.'.resources.Detail';
		unset($headerScriptInstances[$modulePopUpFile]);
		unset($headerScriptInstances[$moduleEditFile]);
		unset($headerScriptInstances[$moduleDetailFile]);
	
		$jsFileNames = array(
				'modules.Inventory.resources.Popup',
				'modules.Inventory.resources.Detail',
				'modules.Inventory.resources.Edit',
				"modules.$moduleName.resources.Detail",
		);
		$jsFileNames[] = $moduleEditFile;
		$jsFileNames[] = $modulePopUpFile;
		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
	} */


}
