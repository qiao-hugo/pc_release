<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Products_Detail_View extends Vtiger_Detail_View {

	function __construct() {
		parent::__construct();
		$this->exposeMethod('showDetailViewByMode');
		$this->exposeMethod('getcustomerfields');
		$this->exposeMethod('getpackstand');
		$this->exposeMethod('showproductinfo');
		$this->exposeMethod('getproductinfo');
	}
	public function showModuleDetailView(Vtiger_Request $request) {
		$recordId = $request->get('record');
		$moduleName = $request->getModule();

		$recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
		$baseCurrenctDetails = $recordModel->getBaseCurrencyDetails();

		$viewer = $this->getViewer($request);
		$viewer->assign('BASE_CURRENCY_SYMBOL', $baseCurrenctDetails['symbol']);
		$viewer->assign('TAXCLASS_DETAILS', $recordModel->getTaxClassDetails());
		$viewer->assign('IMAGE_DETAILS', $recordModel->getImageDetails());

		return parent::showModuleDetailView($request);
	}

	public function showModuleBasicView(Vtiger_Request $request) {
		return $this->showModuleDetailView($request);
	}

	public function getHeaderScripts(Vtiger_Request $request) {
		$headerScriptInstances = parent::getHeaderScripts($request);

		$jsFileNames = array(
			'~libraries/jquery/jquery.cycle.min.js'
		);

		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
	}

    //wangbin

    public function getproductinfo(Vtiger_Request $request){
        $db= PearDatabase::getInstance();
        $record = $request->get('record');

        $productsql = "SELECT productid, productname FROM vtiger_products INNER JOIN vtiger_crmentity ON vtiger_products.productid = vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted = 0 AND ispackage != 1 AND productid != ?";
        $productresult = $db->pquery("$productsql",array($record));
        if($db->num_rows($productresult)>0){
            $arrAllproducts = array();
            for($i=0;$i<$db->num_rows($productresult);$i++){
                $arrAllproducts[] = $db->query_result_rowdata($productresult, $i); //所有非套餐产品
            }
        }

        //$packagesql = "SELECT vtiger_seproductsrel.crmid AS packproductid, vtiger_seproductsrel.productid AS packid, vtiger_seproductsrel.defaultstand, vtiger_crmentity.label FROM vtiger_products INNER JOIN vtiger_crmentity ON vtiger_products.productid = vtiger_crmentity.crmid LEFT JOIN vtiger_seproductsrel ON vtiger_seproductsrel.crmid = vtiger_products.productid WHERE ( vtiger_products.productid IN ( SELECT crmid FROM vtiger_seproductsrel WHERE productid = ? )) AND vtiger_seproductsrel.productid = ? AND vtiger_crmentity.deleted = 0";
        $packagesql = "SELECT vtiger_seproductsrel.crmid AS packproductid, vtiger_seproductsrel.productid AS packid, vtiger_seproductsrel.defaultstand, vtiger_seproductsrel.choosablestand, vtiger_seproductsrel.years, vtiger_seproductsrel.defaultcost, vtiger_crmentity.label FROM vtiger_products INNER JOIN vtiger_crmentity ON vtiger_products.productid = vtiger_crmentity.crmid LEFT JOIN vtiger_seproductsrel ON vtiger_seproductsrel.crmid = vtiger_products.productid WHERE ( vtiger_products.productid IN ( SELECT crmid FROM vtiger_seproductsrel WHERE productid = ? )) AND vtiger_seproductsrel.productid = ? AND vtiger_crmentity.deleted = 0";
        $packageresult = $db->pquery("$packagesql",array($record,$record));
        if($db->num_rows($packageresult)>0){
            $arrPackage = array();
            for($i=0;$i<$db->num_rows($packageresult);$i++){
                $temp = $db->fetchByAssoc($packageresult);
                $temp['choosablestand'] = explode('##',$temp['choosablestand']);
                $arrPackage[] =$temp; //当前套餐下面的产品详细,包括年限,默认规格,默认成本,以及可选规格;
            }
        }

        // var_dump($arrPackage);die;

        $standsql = "SELECT standardid,productid,standardname FROM vtiger_products_standard WHERE `delete` !=1 ";
        $standresult = $db->pquery("$standsql",array());
        if($db->num_rows($standresult)>0){
            $arrStand = array();
            for($i=0;$i<$db->num_rows($standresult);$i++){
                $arrStand[] = $db->query_result_rowdata($standresult,$i); //读取所有产品的规格
            }
        }

        $currentstandsql = "SELECT * FROM vtiger_products_standard WHERE productid = ? AND `delete` !=1 ";
        $currentstandresult = $db->pquery("$currentstandsql",array($record));
        if($db->num_rows($currentstandresult)>0){
            $arrCurrentstand = array();
            for($i=0;$i<$db->num_rows($currentstandresult);$i++){
                $arrCurrentstand[] = $db->query_result_rowdata($currentstandresult,$i); //读取当前产品的所有规格
            }
        }
        //var_dump($arrCurrentstand);
        $viewer = $this->getViewer($request);
        $viewer->assign("ALLPRODUCTS",$arrAllproducts);
        $viewer->assign("PACKAGE",$arrPackage);
        $viewer->assign("ALLSTAND",$arrStand);
        $viewer->assign("CURRENTSTAND",$arrCurrentstand);
        return $viewer->view("widgetContainerDetail2.tpl",'Products',true);
    }
    //end





	//详细页面读取套餐跟产品规格信息
	public function  getproductinfo___bak(Vtiger_Request $request){
	    $db= PearDatabase::getInstance();
	    $record = $request->get('record');
	    $currentstandsql = "SELECT * FROM vtiger_products_standard WHERE productid = ? AND `delete`!=1";
	    $currentstandresult = $db->pquery("$currentstandsql",array($record));
	    if($db->num_rows($currentstandresult)>0){
	        $arrCurrentstand = array();
	        for($i=0;$i<$db->num_rows($currentstandresult);$i++){
	            $arrCurrentstand[] = $db->query_result_rowdata($currentstandresult,$i); //读取当前产品的规格
	        }
	    }
	    $packagesql = "SELECT vtiger_crmentity.label, ( SELECT standardname FROM vtiger_products_standard WHERE standardid = vtiger_seproductsrel.defaultstand ) AS defaultdtand FROM vtiger_products INNER JOIN vtiger_crmentity ON vtiger_products.productid = vtiger_crmentity.crmid LEFT JOIN vtiger_seproductsrel ON vtiger_seproductsrel.crmid = vtiger_products.productid WHERE ( vtiger_products.productid IN ( SELECT crmid FROM vtiger_seproductsrel WHERE productid = ? )) AND vtiger_seproductsrel.productid = ? AND vtiger_crmentity.deleted = 0";
	    $packageresult = $db->pquery("$packagesql",array($record,$record));
	    if($db->num_rows($packageresult)>0){
	        $arrPackage = array();
	        for($i=0;$i<$db->num_rows($packageresult);$i++){
	            $arrPackage[] = $db->query_result_rowdata($packageresult,$i);
	        }
	    }

	    $viewer = $this->getViewer($request);
	    $viewer->assign("STAND",$arrCurrentstand);
	    $viewer->assign("package",$arrPackage);
	    return $viewer->view("widgetContainerDetail.tpl",'Products',true);
	    
	}
	//编辑页面ajax读取套餐跟产品规格信息
	public function getpackstand(Vtiger_Request $request){
	   $db= PearDatabase::getInstance();
	   $record = $request->get('record');

	   $productsql = "SELECT productid, productname FROM vtiger_products INNER JOIN vtiger_crmentity ON vtiger_products.productid = vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted = 0 AND ispackage != 1 AND productid != ?";
        $productresult = $db->pquery("$productsql",array($record));
	   if($db->num_rows($productresult)>0){
	       $arrAllproducts = array();
	       for($i=0;$i<$db->num_rows($productresult);$i++){
	           $arrAllproducts[] = $db->query_result_rowdata($productresult, $i); //所有非套餐产品
	       }
	   }
	   
	   //$packagesql = "SELECT vtiger_seproductsrel.crmid AS packproductid, vtiger_seproductsrel.productid AS packid, vtiger_seproductsrel.defaultstand, vtiger_crmentity.label FROM vtiger_products INNER JOIN vtiger_crmentity ON vtiger_products.productid = vtiger_crmentity.crmid LEFT JOIN vtiger_seproductsrel ON vtiger_seproductsrel.crmid = vtiger_products.productid WHERE ( vtiger_products.productid IN ( SELECT crmid FROM vtiger_seproductsrel WHERE productid = ? )) AND vtiger_seproductsrel.productid = ? AND vtiger_crmentity.deleted = 0";
	   $packagesql = "SELECT vtiger_seproductsrel.crmid AS packproductid, vtiger_seproductsrel.productid AS packid, vtiger_seproductsrel.defaultstand, vtiger_seproductsrel.choosablestand, vtiger_seproductsrel.years, vtiger_seproductsrel.defaultcost, vtiger_crmentity.label FROM vtiger_products INNER JOIN vtiger_crmentity ON vtiger_products.productid = vtiger_crmentity.crmid LEFT JOIN vtiger_seproductsrel ON vtiger_seproductsrel.crmid = vtiger_products.productid WHERE ( vtiger_products.productid IN ( SELECT crmid FROM vtiger_seproductsrel WHERE productid = ? )) AND vtiger_seproductsrel.productid = ? AND vtiger_crmentity.deleted = 0";
       $packageresult = $db->pquery("$packagesql",array($record,$record));
	   if($db->num_rows($packageresult)>0){
	       $arrPackage = array();
	       for($i=0;$i<$db->num_rows($packageresult);$i++){
               $temp = $db->fetchByAssoc($packageresult);
               $temp['choosablestand'] = explode('##',$temp['choosablestand']);
	           $arrPackage[] =$temp; //当前套餐下面的产品详细,包括年限,默认规格,默认成本,以及可选规格;
	       }
	   }

       // var_dump($arrPackage);die;

   $standsql = "SELECT standardid,productid,standardname FROM vtiger_products_standard WHERE `delete` !=1 ";
	   $standresult = $db->pquery("$standsql",array());
	   if($db->num_rows($standresult)>0){
	       $arrStand = array();
	       for($i=0;$i<$db->num_rows($standresult);$i++){
	           $arrStand[] = $db->query_result_rowdata($standresult,$i); //读取所有产品的规格
	       }
	   }
	   
	   $currentstandsql = "SELECT * FROM vtiger_products_standard WHERE productid = ? AND `delete` !=1 ";
	   $currentstandresult = $db->pquery("$currentstandsql",array($record));
       if($db->num_rows($currentstandresult)>0){
           $arrCurrentstand = array();
           for($i=0;$i<$db->num_rows($currentstandresult);$i++){
               $arrCurrentstand[] = $db->query_result_rowdata($currentstandresult,$i); //读取当前产品的所有规格
           }
       }	
       //var_dump($arrCurrentstand);
	   $viewer = $this->getViewer($request);
	   $viewer->assign("ALLPRODUCTS",$arrAllproducts);
	   $viewer->assign("PACKAGE",$arrPackage);
	   $viewer->assign("ALLSTAND",$arrStand);
	   $viewer->assign("CURRENTSTAND",$arrCurrentstand);
	   return $viewer->view("PackstandDetail.tpl",'Products',true);
	}
	
	
	
	
		
	public function getcustomerfields($request){
		$viewer = $this->getViewer($request);
		$moduleName = $request->getModule();
		$recordId = $request->get('record');
		$formcontent='';
		$db = PearDatabase::getInstance();
		$formid=$request->get('formid');
	 	if(!empty($formid)){
			$result = $db->pquery('SELECT form_name,content_data from vtiger_formdesign where deleted=0 and formid=? limit 1',array($request->get('formid')));
			if($db->num_rows($result)){
				$temp=$db->query_result_rowdata($result);
				echo '<label class="control-label">'.$temp['form_name'].'</label><div class="controls">'.htmlspecialchars_decode($temp['content_data']).'</div>';
			}
			exit;
		}
		
		if(!empty($recordId)){
			
			$result = $db->pquery('SELECT formid FROM vtiger_customer_modulefields where relatedmodule=? and relateid=? limit 1',array($moduleName,$recordId));
			if($db->num_rows($result)){
				$info=$db->query_result_rowdata($result);
				$result = $db->pquery('SELECT formid,form_name,content_data from vtiger_formdesign where formid='.$info['formid']);
				$form=$db->query_result_rowdata($result);
				$viewer->assign('FORMCONTENT',$form);
			}
			
		}
		$result = $db->pquery('SELECT form_name,formid from vtiger_formdesign where deleted=0');
		$noOfresult=$db->num_rows($result);
		
		$forms=array();
		
		if($noOfresult>0){
			for ($i=0; $i<$noOfresult; ++$i) {
				$forms[] = $db->fetchByAssoc($result);
			}
			$viewer->assign('FORMLIST',$forms);
		}else{
			exit;
		}
		
		return $viewer->view('customerfields.tpl', $moduleName, true);
		exit;
	}
	
	public function showproductinfo($request){
		$viewer = $this->getViewer($request);
		$moduleName = $request->getModule();
		$recordId = $request->get('record');
		$formcontent=array();
		if(!empty($recordId)){
			$db = PearDatabase::getInstance();
			$result = $db->pquery('SELECT form_name,content_data from vtiger_formdesign where formid= (SELECT formid FROM vtiger_customer_modulefields where relatedmodule=? and relateid=? limit 1) limit 1',array($moduleName,$recordId));
			if(!empty($result)){
				$info=$db->query_result_rowdata($result);
				
				$viewer->assign('FORMINFO',$info);
				return $viewer->view('showcustomerfields.tpl', $moduleName, true);
				/* $formcontent=explode('#-#',$info['fieldinfo']);
				$temp='';
				foreach($formcontent as $key=>$json){
					$formcontent[$key]=json_decode(str_replace('&quot;','"',trim($json)),true);	
				}	 */
			}
		}
		
	
	
	
	}
	
}
