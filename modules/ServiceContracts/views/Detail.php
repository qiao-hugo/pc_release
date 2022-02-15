<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ServiceContracts_Detail_View extends Vtiger_Detail_View {
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
    function showModuleDetailView(Vtiger_Request $request) {
        $lable=$request->get('tab_label');
        $recordId = $request->get('record');
        $moduleName = $request->getModule();
        //young.yang 2014-12-26 工作流
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
//        var_dump($recordStrucure);die;
        $structuredValues = $recordStrucure->getStructure();


        $moduleModel = $recordModel->getModule();
        $record = $request->get('record');
        $contracts_divide = ServiceContracts_Record_Model::servicecontracts_divide($record);
//        print_r($contracts_divide);exit;
        global $current_user;
        $sc_related_to=$recordModel->get('sc_related_to');
        if(in_array($recordModel->get('modulestatus'),array('已发放','a_normal','b_check','b_actioning','c_stamp'))
            && $recordModel->get('assigned_user_id')==$current_user->id
            && empty($sc_related_to)){
            $accountidEdit=true;
        }else{
            $accountidEdit=false;
        }

        $viewer = $this->getViewer($request);
        $viewer->assign('CONTRACTS_DIVIDE',$contracts_divide); //合同分成表
        $viewer->assign('CONTRACTS_DIVIDE_1',$this->servicecontracts_divided($record)); //合同分成表
//        $viewer->assign('CONTRACTS_RECEIVEMENT',$this->servicecontracts_receivedpayment($record)); //合同相关联提交审核要修改回款分成的回款
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        $viewer->assign('IS_EDITACCOUNT', $accountidEdit);
        $viewer->assign('SIGNATURETYPE', $recordModel->get('signaturetype'));
        $viewer->assign('ISSTAGE', $recordModel->get('isstage'));
        $viewer->assign('MODULESTATUS', $recordModel->get('modulestatus'));
        //是否显示分成单按钮（合同是已签收状态，合同签订人、合同所属人、且合同领取人、客户所属人之一是当前登录用户）
        $is_show = $recordModel->get('modulestatus')=='c_complete' && ($recordModel->get('Signid')==$current_user->id || $recordModel->get('assigned_user_id')==$current_user->id || $recordModel->get('Receiveid')==$current_user->id || $this->getSmownerid($current_user->id,$sc_related_to)) ? 'true':'false'; //负责人
        $is_show=$request->get('requestMode')=='full'?false:$is_show;//匹配回款时不显示按钮
        $viewer->assign('PERMISSIONS',$is_show );
        $users = ReceivedPayments_Record_Model::getuserinfo(" AND `status`='Active'");
        foreach ($users as $user){
            $accessUsers[$user['id']]=$user['last_name'];
        }
        $viewer->assign('ACCESSIBLE_USERS',$accessUsers);//人员
        $fileRecordModel = Files_Record_Model::getCleanInstance("Files");
        $viewer->assign('ISFENQIFILE',$fileRecordModel->isExistFile('ServiceContracts',$record,'files_style13'));
        $servicenum = ServiceContracts_Record_Model::servicecontracts_reviced($current_user->id);
        $viewer->assign('CANHANDLECONTRACTNUM',$servicenum);//人员

        $recordModel = SeparateInto_Record_Model::getCleanInstance("SeparateInto");
        $shareInfo = $recordModel->getMarketingShareInfo($recordModel->get("sc_related_to"));
        if(count($shareInfo)){
            $viewer->assign('SHAREINFO',$shareInfo);//人员

        }
        //添加选择  cby 2018-12-14
        /* $db=PearDatabase::getInstance();
         $getcompanysql ="SELECT owncompany FROM `vtiger_owncompany`";
         $company = $db->pquery($getcompanysql,array());
         $owncompany = array();
         $sums=$db->num_rows($company);
         if($sums>0){
             while($row = $db->fetchByAssoc($company)){
                 //var_dump();
                 $owncompany[$row['owncompany']] = $row['owncompany'];
             }
         }
         $accessibleUsers = get_username_array($where);  //人员信息
 //        $accessibleUsersDivide=get_username_array_divide($where);
         $viewer->assign('OWNCOMPANY',$owncompany);//所有公司
         $viewer->assign('ACCESSIBLE_USERS',$accessibleUsers);//人员
 //        $viewer->assign('ACCESSIBLE_USERS_DIVIDE',$accessibleUsersDivide);//人员*/
        if($lable=='服务合同 详细内容'){
            $recordId=$request->get('record');
            $recordModel=new SearchMatch_Record_Model();
            $accountMoneyArray=$recordModel->getAccountMoneyArray($recordId);
            if(!$accountMoneyArray['paymentReceived']){
                $accountMoneyArray['paymentReceived']='0.00';
            }
            if(!$accountMoneyArray['paymentTotal']){
                $accountMoneyArray['paymentTotal']='0.00';
            }

            $parentRecordModel = Vtiger_Record_Model::getInstanceById($recordId, 'ServiceContracts');
            $accountMoneyArray['leastPayMoney']=$accountMoneyArray['paymentElse'];
            if($parentRecordModel->get('isstage')){
                $recordModel=new ServiceContracts_Record_Model();
                $result=$recordModel->leastPayMoney($recordId);
                Matchreceivements_Record_Model::recordLog($result,'leastPay');
                $accountMoneyArray['leastPayMoney']=$result['data'];
            }
            $accountMoneyArray=array_map(function ($v){
                return number_format($v,2);
            },$accountMoneyArray);
            $accountMoneyArray['sideagreement']=$parentRecordModel->get('sideagreement');
            $viewer->assign('accountMoneyArray', $accountMoneyArray);
            $viewer->assign('TAB_LABEL', $lable);
        }

        return $viewer->view('DetailViewFullContents.tpl',$moduleName,true);
    }
    function getSmownerid($smownerid,$crmid){
        $sql = "select 1 from vtiger_crmentity where smownerid=? and crmid=?";
        $db=PearDatabase::getInstance();
        $result = $db->pquery($sql,array($smownerid,$crmid));
        return $db->num_rows($result);
    }
    function servicecontracts_divided($contractid){
        $db=PearDatabase::getInstance();
        $sql = "SELECT *,( SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE vtiger_servicecontracts_divide_tmp.receivedpaymentownid = vtiger_users.id ) AS receivedpaymentownname FROM `vtiger_servicecontracts_divide_tmp` WHERE servicecontractid =?";
        $result = $db->pquery($sql,array($contractid));
        $result_li = array();
        if($db->num_rows($result)>0){
            for($i=0;$i<$db->num_rows($result);$i++){
                $result_li[] = $db->fetchByAssoc($result);
            }
        }
       return $result_li;
    }
    function servicecontracts_receivedpayment($contractid){
        $db=PearDatabase::getInstance();

        $sql = "SELECT  * FROM  vtiger_servicecontracts_update_receivedpayments_divide_tmp WHERE servicecontractsid=?";
        $result = $db->pquery($sql,array($contractid));
        $result = $db->query_result_rowdata($result,0);
        // 关联销售业绩明细取不存在已经完结的回款明细
        $sql = "SELECT * FROM vtiger_receivedpayments WHERE receivedpaymentsid IN(".$result['receivedpaymentsids'].") AND  receivedstatus='normal' AND NOT EXISTS(SELECT 1 FROM vtiger_achievementallot_statistic WHERE receivedpaymentsid=vtiger_receivedpayments.receivedpaymentsid AND vtiger_achievementallot_statistic.isover=1) ";
        $result = $db->pquery($sql,array());
        if($db->num_rows($result)>0){
            while($rawData=$db->fetch_array($result)) {
                $rawData['receivedstatus']=vtranslate($rawData['receivedstatus'],"ReceivedPayments");
                $rawDatas[]=$rawData;
            }
            return $rawDatas;
        }else{
            return false;
        }
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

	/*wangbin 2015年5月30日17:20:39
	 * 传入合同id，读取合同下的产品信息以及回款历史
	 * 回款的编辑页面，ajax数据返回
	 * module=ServiceContracts&view=Detail&mode=getservicecontractsinfo&record=368826&receivepayid=604
	 * */
	public function getservicecontractsinfo(Vtiger_Request $request){
        //echo $request->getModule();die;
	    $requestmodule = $request->get('requestmodule');
	     $servicecontractsid =(int)$request->get('record');
	    $receivepayid = (int)$request->get('receivepayid');

	    if (empty($servicecontractsid)) {
	    	echo '';die;
	    }
	    $db=PearDatabase::getInstance();
	    /*2015年5月27日 星期三 */
	    $productsql = "SELECT vtiger_salesorderproductsrel.salesorderproductsrelid, IFNULL(( SELECT SUM(alreadyprice) FROM vtiger_receivementproducts AS aa WHERE aa.serviceid = vtiger_salesorderproductsrel.servicecontractsid AND aa.productsid = vtiger_salesorderproductsrel.productid AND aa.receivedpaymentid != ? ), 0 ) AS already, IFNULL(( SELECT alreadyprice FROM vtiger_receivementproducts AS aa WHERE aa.serviceid = vtiger_salesorderproductsrel.servicecontractsid AND aa.productsid = vtiger_salesorderproductsrel.productid AND aa.receivedpaymentid = ? LIMIT 1 ), 0 ) AS alreadyprice, vtiger_products.productid, vtiger_products.productname, vtiger_salesorderproductsrel.marketprice FROM vtiger_salesorderproductsrel LEFT JOIN vtiger_products ON vtiger_salesorderproductsrel.productid = vtiger_products.productid LEFT JOIN vtiger_receivementproducts ON vtiger_salesorderproductsrel.salesorderproductsrelid = vtiger_receivementproducts.salesorderproductsid WHERE vtiger_salesorderproductsrel.servicecontractsid = ? GROUP BY vtiger_salesorderproductsrel.productid";
	    $productresult = $db->pquery($productsql,array($receivepayid,$receivepayid,$servicecontractsid));
	    if($db->num_rows($productresult)>0){
	        for ($i=0;$i<$db->num_rows($productresult);++$i){
	            $productlis[] = $db->fetchByAssoc($productresult);
	        }
	    }
        //查询当前回款金额
	    $sql4 = "SELECT unit_price FROM vtiger_receivedpayments WHERE receivedpaymentsid = ?";
	    $unit_price = $db->pquery($sql4,array($receivepayid))->fields['0']; //本次回款金额
	    //计算产品的总金额跟已收产品总金额;
	    $mark= 0 ;
	    $alprice= 0 ;
	    $alreadyprice = 0;
	    foreach ($productlis as $key){
	         $mark += $key['marketprice']; //市场总金额;
	         $alprice +=$key['already']; //已收款总金额;
	         $alreadyprice +=$key['alreadyprice']; //收款金额;
	         $thisproductremain = $key['marketprice']-$key['already'];//当前产品剩余回款

	    }

	    foreach ($productlis as $keys=>$val){
	         //自动给每个产品添加产品分成
	        $productfenchen =  $unit_price*(($val['marketprice']-$val['already'])/($mark-$alprice));//当前产品分成金额 = （当前产品剩余/当前合同总剩余）*本次回款金额
	        $productlis[$keys]['fenchen'] = sprintf("%0.2f",$productfenchen);
	    }
	       if($unit_price-$alreadyprice){
	           $after = $unit_price-$alreadyprice;
	       }else{
	           $after = "0.00";
	       };

	       if($after<0){
	           $after = "错误的数据";
	       }
	    //end
	    //wangbin 根据合同id 读取回款表里的回款记录，加载到回款历史块
	    $sql2 = "SELECT * FROM vtiger_receivedpayments WHERE relatetoid = ?";
	    $receivedresult = $db->pquery($sql2,array($servicecontractsid));
	    if($db->num_rows($receivedresult)>0){
	        for ($i=0;$i<$db->num_rows($receivedresult);++$i){
	            $receivedlis[] = $db->fetchByAssoc($receivedresult);
	        }
	    }

	    $subtract =  sprintf("%0.2f",$mark-$alprice);
	    $viewer = $this->getViewer($request);
        $viewer->assign("afterSUBTRACT",$after);//本次回款剩余
	    $viewer->assign("SUBTRACT",$subtract);  //当前合同剩余回款
	    $viewer->assign('PRICETOTAL',$alprice);
	    $viewer->assign('RECEIVEDHISTORY',$receivedlis);
	    $viewer->assign('PRODUCTLIS',$productlis);
	    //是否是出纳;
	    $viewer->assign('ISCHUNA',$request->get('ischuna'));
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
	function getWorkflowsM(Vtiger_Request $request){
        $moduleName = $request->getModule();
        $recordId = $request->get('record');
        $viewer = $this->getViewer($request);
        if(!$this->record){
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }
        $recordModel = $this->record->getRecord();
        $viewer->assign('ModuleName',$moduleName); //工作流stagesid
        $viewer->assign('RECORD',$recordModel); //实例化对象
        return $viewer->view('LineItemsWorkflowsM.tpl', 'Vtiger',true);
    }

}
