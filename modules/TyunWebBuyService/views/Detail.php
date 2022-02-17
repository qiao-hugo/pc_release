<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class TyunWebBuyService_Detail_View extends Vtiger_Detail_View {
    public function __construct(){
        parent::__construct();
        $this->exposeMethod('getSoundAComments');
    }
    function preProcess(Vtiger_Request $request, $display = true) {
		$viewer = $this->getViewer($request);
		$viewer->assign('NO_SUMMARY', true);
		parent::preProcess($request);
	}
	/**
	 * Function returns Inventory details
	 * @param Vtiger_Request $request
	 */
	function showModuleDetailView(Vtiger_Request $request) {
		//echo parent::showModuleDetailView($request);
		//echo $this->getWorkflowsM($request);
		global $current_user;
		$recordId = $request->get('record');
		$moduleName = $request->getModule();
	
		if(!$this->record){
			$this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
		}
	
	
		$recordModel = $this->record->getRecord();
	
		//获取跟进信息  gaocl add
		$recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
		$structuredValues = $recordStrucure->getStructure();
        $column_fields=$recordModel->getEntity()->column_fields;
		$moduleModel = $recordModel->getModule();
		// 如果是归档列表调过来的 cxh 获取归档数据信息
	        if($_REQUEST['orderType']=='filed'){
	            $filedData=$recordModel->getOneFiledData($recordId);
	        }
		//print_r($recordModel->getReceivedPaymentsList($column_fields['servicecontractsid']));
		//exit;
		$viewer = $this->getViewer($request);
		$viewer->assign('FILEDDATA',$filedData);
		$viewer->assign('INVOICELIST', $recordModel->getInvoiceList($column_fields['contractid']));
		$viewer->assign('RECEIVEPAYMENTSLIST', $recordModel->getReceivedPaymentsList($column_fields['contractid']));
		$viewer->assign('RECORD', $recordModel);
		$viewer->assign('SERVICECONTRACTID', $column_fields['contractid']);
		$viewer->assign('RECORD_STRUCTURE', $structuredValues);
		$viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$viewer->assign('MODULE_NAME', $moduleName);
		$viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
	        // 判断是否是归档数据时使用如果是归档数据显示归档数据模板
	        $viewer->assign('IS_FILED',$_REQUEST['orderType']);
		return $viewer->view('DetailViewFullContents.tpl',$moduleName,true);
	}
	
	/**
	 * Function returns Inventory details
	 * @param Vtiger_Request $request
	 * @return type
	 */
	function showDetailViewByMode($request) {
		return $this->showModuleDetailView($request);
	}
	
	function showModuleBasicView($request) {
		return $this->showModuleDetailView($request);
	}


    function showLineItemDetails(Vtiger_Request $request) {
        $record = $request->get('record'); //1586 工单id
        $moduleName = $request->getModule(); //SalesOrder
        $recordModel = OrderChargeback_Record_Model::getInstanceById($record);
        $relatedProducts = $recordModel->getProducts($record);

        $productsCount = count($relatedProducts);
        //2014-12-20添加
        $sumprice=0;
        $realsumpice=0;
        $totalcosting=0;
        $totalpurchasemount=0;
        $db = PearDatabase::getInstance();
        //查找工单产品所属套餐

        for ($i=1; $i<=$productsCount; $i++) {
            $productid = $relatedProducts[$i-1]['productid'];
            $salesorderid=$relatedProducts[$i-1][salesorderid];
            //2015-03-13 wangbin 工单产品审核人

            $checkproductid = "SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE id = ( SELECT auditorid FROM vtiger_salesorderworkflowstages WHERE modulename = 'SalesOrder' AND salesorderid = ? AND productid = ? AND auditorid is not NULL ORDER BY sequence DESC LIMIT 1 )";
            //echo $productid;die;
            $checkproductusers=$db->pquery($checkproductid,array($record,$productid));

            $numRows = $db->num_rows($checkproductusers);
            if($numRows>0){
                $checkproductuser = $checkproductusers->fields['last_name'];
            }else{
                $checkproductuser = '--';
            }
            //end
            //2015-03-12 wangbin 产品资料详细及规则
            $productnotes = $db->pquery('SELECT productsolution,productform,remark,costing,purchasemount,Tsite,TsiteNew FROM `vtiger_salesorderproductsrel` WHERE productid=? and salesorderid=?',array($productid,$salesorderid));
            //var_dump($productnotes);die;
            $relatedProducts[$i-1]['notes']=$productnotes->fields['1'];
            $relatedProducts[$i-1]['productsolution']=$productnotes->fields['0'];
            $relatedProducts[$i-1]['remark']=$productnotes->fields['2'];
            $relatedProducts[$i-1]['costing']=$productnotes->fields['3'];
            $relatedProducts[$i-1]['purchasemount']=$productnotes->fields['4'];
            $relatedProducts[$i-1]['Tsite']=$productnotes->fields['5'];
            $relatedProducts[$i-1]['TsiteNew']=$productnotes->fields['6'];

            $relatedProducts[$i-1]['checkproductuser']=$checkproductuser;
            $product = $relatedProducts[$i];
            $sumprice+=$relatedProducts[$i-1]['marketprice'];//12-20添加 2015-01-26 wangbin 修改
            $realsumpice+=$relatedProducts[$i-1]['realmarketprice'];
            $totalcosting+=$relatedProducts[$i-1]['costing'];//成本价格
            $totalpurchasemount+=$relatedProducts[$i-1]['purchasemount'];//外采费价格
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
            //wangbin 2015-01-27 注释
        }
        $TOTALCOST=$totalcosting+$totalpurchasemount;
        $sum=number_format($sumprice,2);
        $realsum = number_format($realsumpice,2);
        $viewer = $this->getViewer($request);
        $viewer->assign('SUM', $sum);
        $viewer->assign('TOTALCOSTING', array('TOTALCOST'=>$TOTALCOST,'totalcosting'=>$totalcosting,'totalpurchasemount'=>$totalpurchasemount));
        $viewer->assign('REALSUM', $realsum);
        $viewer->assign('RELATED_PRODUCTS', $relatedProducts);
        $viewer->assign('C_NEWPORDUCT', OrderChargeback_Record_Model::getInvoiceSalesorderL($recordModel->entity->column_fields['servicecontractsid']));
        $viewer->assign('C_OLDPRODUCT', OrderChargeback_Record_Model::getorderinvoicesaleorderlist($record));
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('MODULE_NAME',$moduleName);
        $viewer->view('LineItemsDetail.tpl', $moduleName);
    }
}
	