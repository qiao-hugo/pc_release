<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class SalesOrder_ListAjax_View extends Vtiger_List_View {
	function preProcess(Vtiger_Request $request, $display = true) {
		
	}
	function process(Vtiger_Request $request){
		$moduleName = $request->getModule();
		$ids = $request->get('records');
		//$moduleModel = CR::getInstance($moduleName);//module相关的数据
		$mode = $request->get('mode');
		if($mode != "edit"){
            $modelClassName = Vtiger_Loader::getComponentClassName('Model', 'Record', $moduleName);
            //echo $modelClassName;echo $ids;
            $instance = new $modelClassName();
            $result = $instance->getAllSalesorderWorkflowStages($ids);
            echo $result;
        }else{
           	$productid  = $request->get('productid');
           	$productids=$this->getproducts($productid);
			
			
			
			echo json_encode(array('success'=>true,'products'=>$productids));
			return; 
        }
	}
	//内部工单没有合同的产品显示 新增或编辑
    function getproducts($productid){
		global $adb;
		$record=$_REQUEST['record'];
		//if($_REQUEST['relate']=='product'){
		$isEditForm=true;
		if(empty($record)){
			////新增按选择的产品
			$productid=explode(',',$productid);
			$result1=$adb->pquery("SELECT vtiger_customer_modulefields.formid as product_tplid, vtiger_products.productcategory, vtiger_products.productname,vtiger_products.product_no, vtiger_products.productid, vtiger_formdesign.content_parse as productform, vtiger_formdesign.field FROM vtiger_customer_modulefields LEFT JOIN vtiger_formdesign ON vtiger_formdesign.formid = vtiger_customer_modulefields.formid LEFT JOIN vtiger_products ON vtiger_products.productid = vtiger_customer_modulefields.relateid WHERE vtiger_customer_modulefields.relateid IN (".generateQuestionMarks($productid).")",$productid);
			//$sql="select vtiger_products.productcategory,vtiger_products.productname,vtiger_products.productid,vtiger_productcf.notecontent as solution from  vtiger_products LEFT JOIN vtiger_productcf ON vtiger_productcf.productid=vtiger_products.productid where  vtiger_products.productid in ({$productid})";
			//$result =$adb->pquery($sql,array());
		}else{
			//按已有的工单 不兼容旧的模版系统
			$sql='SELECT vtiger_salesorder_productdetail.FormInput,vtiger_salesorder_productdetail.TplId as product_tplid, vtiger_products.productname,vtiger_products.product_no, vtiger_products.productid, vtiger_formdesign.content_parse as productform,vtiger_formdesign.field FROM vtiger_salesorder_productdetail LEFT JOIN vtiger_products ON vtiger_products.productid = vtiger_salesorder_productdetail.Productid LEFT JOIN vtiger_formdesign ON vtiger_formdesign.formid = vtiger_salesorder_productdetail.TplId WHERE vtiger_salesorder_productdetail.SalesOrderId =?';
			$result1 =$adb->pquery($sql,array($record));
			$row=$adb->num_rows($result1);
			
			if(!$row){
				$isEditForm=false;
				$pid=$adb->pquery('SELECT GROUP_CONCAT(productid) as productid from vtiger_salesorderproductsrel where salesorderid=?',array($record));
				$data=$adb->query_result_rowdata($pid);
				$result1=$adb->pquery("SELECT vtiger_customer_modulefields.formid as product_tplid, vtiger_products.productcategory, vtiger_products.productname,vtiger_products.product_no, vtiger_products.productid, vtiger_formdesign.content_parse as productform, vtiger_formdesign.field FROM vtiger_customer_modulefields LEFT JOIN vtiger_formdesign ON vtiger_formdesign.formid = vtiger_customer_modulefields.formid LEFT JOIN vtiger_products ON vtiger_products.productid = vtiger_customer_modulefields.relateid WHERE vtiger_customer_modulefields.relateid IN (".$data['productid'].")");
			}
			//$sql='select vtiger_salesorderproductsrel.solution,vtiger_salesorderproductsrel.productid, from vtiger_salesorderproductsrel left join vtiger_products on  vtiger_products.productid=vtiger_salesorderproductsrel.productid  where salesorderid=?';
			//$result =$adb->pquery($sql,array($productid));	
		}
		//$row=$adb->num_rows($result);
		$row=$adb->num_rows($result1);
		if($row>0) {
			require 'include/utils/formparse.php';
			for ($i=0; $i<$row; ++$i) {
				$product = $adb->fetchByAssoc($result1);
				$datas=json_decode(str_replace('&quot;','"',$product['field']),true);
				$values=(empty($product['forminput']))?array():json_decode(str_replace('&quot;','"',$product['forminput']),true);
				$product['productform']=parse_toform($datas,$product['productform'],$values,$product['productid']);
				$product['isEditForm']=$isEditForm;
				$products[$product['productid']]=$product;
			}
		/*}elseif($row==1){
			$success=true;
			$products[] = $adb->query_result_rowdata($result1); */
		}else{
			return ;
		}
		
	return $products;	
		//print_r($products);
		//exit;
		
	$productstpl=array();
	
	foreach($products as $key=> $product){
		$datas=json_decode(str_replace('&quot;','"',$product['field']),true);
		//$values=array();
		$values=(empty($row['forminput']))?array():json_decode(str_replace('&quot;','"',$row['forminput']),true);
		$products[$key]['productform']=parse_toform($datas,$product['productform'],$values,$product['productid']);
		//$products[$key]['product_tplid']=parse_toform($datas,$product['content_parse'],$values,$product['productid']);
		//$productidlist[$row['productid']]['product_tplid']=$row['tplid'];
		//print_r($find);
		//$products[$key]['solution']=html_entity_decode(str_replace($find,$replace,$product['solution']));
		//print_r($replace);
		//$productstpl[]=array('solution'=>str_replace($find,$replace,$product['content_parse']),'productname'=>'233','productid'=>1244);

		}
		
	
		
		
		
		
		
		
		
	}
	
}