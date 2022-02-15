<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class OrderChargeback_Detail_View extends Vtiger_Detail_View {
    function __construct(){
        parent::__construct();
        //$this->exposeMethod('getRejectWordflow');
        //$this->exposeMethod('getProducteditlog');
    }
    function preProcess(Vtiger_Request $request) {
        $viewer = $this->getViewer($request);
        $viewer->assign('NO_SUMMARY', true);
        parent::preProcess($request);
    }
    /**
     * Function returns Inventory details
     * @param Vtiger_Request $request
     */
    function showModuleDetailView(Vtiger_Request $request) {
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

        $viewer->view('DetailViewFullContents.tpl', $moduleName);

        $this->showLineItemDetails($request);

    }
    function showModuleBasicView($request) {

        $recordId = $request->get('record');
        $moduleName = $request->getModule();
        echo $this->getWorkflowsM($request);
        if(!$this->record){
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }
        $recordModel = $this->record->getRecord();

        $detailViewLinkParams = array('MODULE'=>$moduleName,'RECORD'=>$recordId);
        $detailViewLinks = $this->record->getDetailViewLinks($detailViewLinkParams);

        $viewer = $this->getViewer($request);
        $viewer->assign('RECORD', $recordModel);
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
        echo $viewer->view('DetailViewSummaryContents.tpl', $moduleName, true);
        $this->showLineItemDetails($request);
    }
    /**
     * Function returns Inventory Line Items
     * @param Vtiger_Request $request
     */
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
