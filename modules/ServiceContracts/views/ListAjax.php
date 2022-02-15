<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ServiceContracts_ListAjax_View extends Vtiger_ListAjax_View {

	public function process(Vtiger_Request $request) {
        global $TyunWebProductid;
		$mode = $request->get('mode');
		if(!empty($mode) && $mode !='edit' ) {
		    $this->invokeExposedMethod($mode, $request);
			return;
		}
		$moduleName = $request->getModule();
		$mode=$request->getMode();
		$productid  = $request->get('productid');
		$istyun  = $request->get('istyun');
		$servicecontractsid  = $request->get('servicecontractsid');
		if($istyun==3 && $productid!=$servicecontractsid){
		    $this->getTyunProduct($request);
		    return ;
        }
        if($istyun==5 && $productid!=$servicecontractsid){
            $this->getTyunOtherProduct($request);
            return ;
        }
		$success=false;
		$products=array();
		if(!empty($productid)){
			global $adb;
			if($mode=='edit'){
				$result =$adb->pquery('select *,vtiger_salesorderproductsrel.thepackage AS twebthepackage,vtiger_salesorderproductsrel.productid as twebproductid,vtiger_salesorderproductsrel.productname as twebproductname,vtiger_salesorderproductsrel.productsolution AS newsolution,vtiger_salesorderproductsrel.realmarketprice AS realmarketprice,productcomboid,IF (productcomboid IS NULL OR productcomboid = 0,\'--\',(SELECT vtiger_products.realprice FROM vtiger_products WHERE vtiger_products.productid=vtiger_salesorderproductsrel.productcomboid)) AS ptemprealprice,IF (productcomboid IS NULL OR productcomboid = 0,\'--\',(SELECT vtiger_products.unit_price FROM vtiger_products WHERE vtiger_products.productid=vtiger_salesorderproductsrel.productcomboid)) AS ptempunit_price,IFNULL(vtiger_products.minmarketprice,0) AS excost,IFNULL(vtiger_salesorderproductsrel.purchasemount,0) AS purchasemount,IF( productcomboid is NULL or productcomboid=0,vtiger_salesorderproductsrel.productid,productcomboid) as tagid from vtiger_salesorderproductsrel left join vtiger_products on  vtiger_products.productid=vtiger_salesorderproductsrel.productid  where servicecontractsid=? AND(multistatus=? OR multistatus = ?) ORDER BY tagid',array($productid,0,1));
                //var_dump($result);die;
                //fulei没有=，tagid，没有套餐，有产品
				//$adb->pquery("SELECT * from vtiger_products where productid in( SELECT productid FROM vtiger_salesorderproductsrel WHERE servicecontractsid = ? ) ", array($productid));
				$customerno = $adb->pquery("select contract_no from vtiger_servicecontracts where servicecontractsid=?",array($productid));
				$customerno = $customerno->fields['0'];

				$row=$adb->num_rows($result);
                $data= array();
                $reocrdModel = Vtiger_Record_Model::getCleanInstance('ServiceContracts');
                $datapack=$reocrdModel->getOtherPorduct($data);
                $json_datapack=json_decode($datapack,true);
				if($row>1) {
					$success=true;
                    $arrproductsid=array();
					for ($i=0; $i<$row; ++$i) {
						$product = $adb->fetchByAssoc($result);
						$product['vendor_info'] = ServiceContracts_Record_Model::getVendorAndSupplierByProduct($product['productid']);
						$product['suppliercontracts_info'] = $this->getSuppliercontracts($product['vendorid']);
                        $product['packproduct']=$product['productcomboid'].'DZE'.$product['productid'];
						if($product['istyunweb']==1){
                            $product['productname']=$product['twebproductname'];
                            $product['thepackage']=$product['twebthepackage'];
                            $product['excost']="0";//外采成本
                            $product['ptempunit_price']=0;
                            $product['punit_price']=0;
                            $product['productcategory']="std";
                            $product['productcomboid']=$product['productcomboid'];
                            $product['productid']=$product['twebproductid'];
                            $product['realprice']="0";//成本价
                            $product['renewalcost']="0";
                            $product['renewalfee']="0";
                            $product['tagid']=$product['productcomboid'];
                            $product['prealprice']=0;//成本合计
                            $product['tranperformance']="0";
                            $product['unit_price']="0";
                            $product['version']="0";
                            $product['viewedtime']='';
                            $product['packproduct']=$product['productcomboid'].'DZE'.$product['twebproductid'];
                            if($product['twebthepackage']=='--'){
                                foreach($json_datapack['data'] as $value) {
                                    foreach ($value['Products'] as $value1) {
                                        if ($product['twebproductid'] == $value1['ProductID']) {
                                            $product['morestand']=$this->getTyunMoreStand($value1['ProductSpecifications']);
                                        }
                                    }
                                }
                            }

                        }
						$products[]=$product;
                        $arrproductsid[]=$product['productid'];
					}
                    $products=$this->morestand($products,implode(',',$arrproductsid));

				}elseif($row==1){
					$success=true;
					$products[] = $adb->query_result_rowdata($result);
					$vendor_info = ServiceContracts_Record_Model::getVendorAndSupplierByProduct($products[0]['productid']);
                    $products[0]['vendor_info'] = $vendor_info;

                    $products[0]['suppliercontracts_info'] = $this->getSuppliercontracts($products[0]['vendorid']);
                    $products=$this->morestand($products,$products[0]['productid']);
                    $products[0]['thepackage']='--';
                    $products[0]['packproduct']=$products[0]['productcomboid'].'DZE'.$products[0]['productid'];
                    if($products[0]['istyunweb']==1){
                        $products[0]['productname']=$products[0]['twebproductname'];
                        $products[0]['thepackage']=$products[0]['twebthepackage'];
                        $products[0]['excost']="0";//外采成本
                        $products[0]['ptempunit_price']=0;
                        $products[0]['punit_price']=0;
                        $products[0]['productcategory']="std";
                        $products[0]['productcomboid']=$products[0]['productcomboid'];
                        $products[0]['productid']=$products[0]['twebproductid'];
                        $products[0]['realprice']="0";//成本价
                        $products[0]['renewalcost']="0";
                        $products[0]['renewalfee']="0";
                        $products[0]['tagid']=$products[0]['productcomboid'];
                        $products[0]['prealprice']=0;//成本合计
                        $products[0]['tranperformance']="0";
                        $products[0]['unit_price']="0";
                        $products[0]['version']="0";
                        $products[0]['viewedtime']='';
                        $products[0]['packproduct']=$products[0]['productcomboid'].'DZE'.$products[0]['twebproductid'];
                        if($products[0]['twebthepackage']=='--'){
                            foreach($json_datapack['data'] as $value) {
                                foreach ($value['Products'] as $value1) {
                                    if ($products[0]['twebproductid'] == $value1['ProductID']) {
                                        $products[0]['morestand']=$this->getTyunMoreStand($value1['ProductSpecifications']);
                                    }
                                }
                            }
                        }

                    }
				}else{
                    echo json_encode(array('success'=>false,'msg'=>'没有找到该产品的相关信息','subid'=>$productid));
					return ;
				}



			}else{
				$ids=explode(',', $productid);
				foreach ($ids as $id) {
					$result = $adb->pquery("SELECT vtiger_products.*, vtiger_crmentity.*, vtiger_products.solution AS newsolution,0 as productcomboid,productid as tagid,IFNULL(vtiger_products.realprice,0) AS realprice,IFNULL(vtiger_products.unit_price,0) AS unit_price,IFNULL(vtiger_products.minmarketprice,0) AS excost,productname AS thepackage
 FROM vtiger_products INNER JOIN vtiger_crmentity ON vtiger_products.productid = vtiger_crmentity.crmid WHERE productid = ? AND vtiger_crmentity.deleted = 0
 UNION SELECT vtiger_products.*, vtiger_crmentity.*, vtiger_seproductsrel.solution AS newsolution,vtiger_seproductsrel.productid as  productcomboid,vtiger_seproductsrel.productid as  tagid,IFNULL(vtiger_products.realprice,0) AS realprice,IFNULL(vtiger_products.unit_price,0) AS unit_price,IFNULL(vtiger_products.minmarketprice,0) AS excost,(SELECT vtiger_products.productname FROM vtiger_products WHERE vtiger_products.productid=vtiger_seproductsrel.productid) AS thepackage FROM vtiger_products
 INNER JOIN vtiger_crmentity ON vtiger_products.productid = vtiger_crmentity.crmid LEFT JOIN vtiger_seproductsrel
 ON vtiger_seproductsrel.crmid = vtiger_products.productid WHERE ( vtiger_products.productid IN ( SELECT crmid FROM vtiger_seproductsrel WHERE productid = ? ))
 and vtiger_seproductsrel.productid = ? AND vtiger_crmentity.deleted = 0", array($id,$id,$id));


					$row=$adb->num_rows($result);

                    //说明是套餐
                    $arrproductsid=array();
					if($row>1) {
						$success=true;
                        $arrproductsid=array();
						for ($i=0; $i<$row; ++$i) {
							$product = $adb->fetchByAssoc($result);
							if($mode!='edit' && $product['productid']==$id){
                                //$prealprice=$product['realprice'];
                                $punit_price=$product['unit_price'];
								continue;
							}
                            $product['packproduct']=$product['productcomboid'].'DZE'.$product['productid'];
                            $product['prealprice']='NO';
                            $product['punit_price']=$punit_price;
                            $vendor_info = ServiceContracts_Record_Model::getVendorAndSupplierByProduct($product['productid']);
                        	$product['vendor_info'] = $vendor_info;
							$products[]=$product;
                            $arrproductsid[]=$product['productid'];

						}
                        $products=$this->morestand($products,implode(',',$arrproductsid));


					}elseif($row==1){
						$success=true;
						$products[] = $adb->query_result_rowdata($result);
                        //////多规格处理
                        $products=$this->morestand($products,$products[0]['productid']);

                        $products[0]['thepackage']='--';
                        $products[0]['packproduct']=$products[0]['productcomboid'].'DZE'.$products[0]['productid'];
                        $vendor_info = ServiceContracts_Record_Model::getVendorAndSupplierByProduct($products[0]['productid']);
                        $products[0]['vendor_info'] = $vendor_info;
					}else{
                        echo json_encode(array('success'=>false,'msg'=>'没有找到该产品的相关信息','subid'=>$id));
						return ;
					}

				}
			}


			
			echo json_encode(array('success'=>$success,'products'=>$products,'customerno'=>$customerno,'parentid'=>$productid));
		
		}
		
		//echo $moduleName;

		/* $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
		$baseCurrenctDetails = $recordModel->getBaseCurrencyDetails();

		$viewer = $this->getViewer($request);
		$viewer->assign('BASE_CURRENCY_NAME', 'curname' . $baseCurrenctDetails['currencyid']);
		$viewer->assign('BASE_CURRENCY_SYMBOL', $baseCurrenctDetails['symbol']);
		$viewer->assign('TAXCLASS_DETAILS', $recordModel->getTaxClassDetails());

		parent::process($request); */
	}
	// 获取采购合同
	public function getSuppliercontracts($vendorid) {
		$suppliercontracts = array();
		global $adb;
		$sql = "select suppliercontractsid,contract_no from vtiger_suppliercontracts where vendorid=?";
		$result = $adb->pquery($sql, array($vendorid));
		$row = $adb->num_rows($result);
		if ($row > 0) {
			while($rawData=$adb->fetch_array($result)) {
				$suppliercontracts[] = $rawData;
        	}
		}
		return $suppliercontracts;
	}

    /**
     * 产品的多规格
     * @author:steel
     * @time: 2015-07-09
     * @params $products array()对应产品的
     * @params $strproducts string 对应产品的ID中间用,隔开
     * @return $products $array();
     */
    private  function morestand($products,$strproducts){
        $db=PearDatabase::getInstance();
        $query='SELECT vtiger_products_standard.* FROM vtiger_products_standard WHERE vtiger_products_standard.productid  in('.$strproducts.')';
        $arrresult=$db->run_query_allrecords($query);
        //判断一下是否有多规格
        if(!empty($arrresult)){
            $arrnew=array();
            foreach($products as $value){

                if(!empty($arrresult)){
                    foreach($arrresult as $val){
                        if($val['productid']==$value['productid']){
                            $value['morestand'][]=$val;
                        }
                    }
                }
                $arrnew[]=$value;
            }
            $products=$arrnew;
        }
        return $products;
    }
    public function getTyunProduct(Vtiger_Request $request){
        global $adb;
        $productid  = $request->get('productid');
        $servicecontractsid=$request->get('servicecontractsid');
        $categoryid=$request->get('categoryid');
        $agents=$request->get('agents');
        $contract_classification=$request->get('contract_classification');
        $reocrdModel = Vtiger_Record_Model::getCleanInstance('ServiceContracts');
        if($servicecontractsid){
            $query="SELECT a.*,b.contract_classification FROM vtiger_activationcode a left join vtiger_servicecontracts b on a.contractid=b.servicecontractsid WHERE a.contractid=? AND a.status!=2 AND a.comeformtyun=1 order by a.activationcodeid desc";
            $result=$adb->pquery($query,array($servicecontractsid));
            if($adb->num_rows($result)) {
                $tflag=true;
                while ($row = $adb->fetch_row($result)){
                    $datas[] = $row;
                    if($row['classtype']=='renew'){
                        $tflag=false;
                    }
                }
                foreach ($datas as $data){
                    if ($tflag && in_array($data['classtype'],array('upgrade','degrade','buy','crenew','cupgrade','cdegrade'))) {
                        if($data['productid']!=$productid){
                            continue;
                        }
                        $datapack = $reocrdModel->getTyunWebBuy($data,$productid);
                        $json_datapack = json_decode($datapack, true);
                        $products=array();
                        if($json_datapack['code']==200) {
                            foreach ($json_datapack['data'] as $package){
                                if($package['Package']['ID']!=$productid){
                                    continue;
                                }
                                foreach ($package['ProductSpecifications'] as $value) {$temp=array();
                                    $temp['excost']="0";//外采成本
                                    $temp['ptempunit_price']=0;
                                    $temp['punit_price']=0;
                                    $temp['productcategory']="std";
                                    $temp['productcomboid']=$productid;
                                    $temp['productid']=$value['ID'];
                                    $temp['productname']=$value['ProductTitle'];
                                    $temp['realprice']="0";//成本价
                                    $temp['renewalcost']="0";
                                    $temp['renewalfee']="0";
                                    $temp['tagid']=$productid;
                                    $temp['prealprice']=0;//成本合计
                                    $temp['thepackage']=$package['Package']['Title'];
                                    $temp['tranperformance']="0";
                                    $temp['unit_price']="0";
                                    $temp['version']="0";
                                    $temp['viewedtime']='';
                                    $temp['opendate']=$data['startdate'];
                                    $temp['closedate']=$data['expiredate'];
                                    $temp['packproduct']=$productid.'DZE'.$value['ID'];
                                    $products[]=$temp;
                                }

                            }

//                        //如果没取到数据就取第二行数据
//                        if(count($products)){
//                            echo json_encode(array('success'=>true,'products'=>$products,'customerno'=>'','parentid'=>$productid));
//                            return;
//                        }
                        }
                    }elseif($data['classtype'] == 'renew'){
                        $datapack = $reocrdModel->getTyunWebRenew($data);
                        $json_datapack = json_decode($datapack, true);
                        $products=array();
                        if($json_datapack['code']==200) {
                            $json_datapack_data=$json_datapack['data'];
                            if($productid==$json_datapack_data['package']['ID'] ){
                                if(!empty($json_datapack_data['packageSpecificationList'])){
                                    foreach($json_datapack_data['packageSpecificationList'] as $value){
                                        if($value['CanRenew']){
                                            $temp=array();
                                            $temp['excost']="0";//外采成本
                                            $temp['ptempunit_price']=0;
                                            $temp['punit_price']=0;
                                            $temp['productcategory']="std";
                                            $temp['productcomboid']=$productid;
                                            $temp['productid']=$value['ProductID'];
                                            $temp['productname']=$value['ProductTitle'];
                                            $temp['realprice']="0";//成本价
                                            $temp['renewalcost']="0";
                                            $temp['renewalfee']="0";
                                            $temp['tagid']=$productid;
                                            $temp['prealprice']=0;//成本合计
                                            $temp['thepackage']=$json_datapack_data['package']['Title'];
                                            $temp['tranperformance']="0";
                                            $temp['unit_price']="0";
                                            $temp['version']="0";
                                            $temp['viewedtime']='';
                                            $temp['opendate']=$data['startdate'];
                                            $temp['closedate']=$data['expiredate'];
                                            $temp['packproduct']=$productid.'DZE'.$value['ProductID'];
                                            $products[]=$temp;
                                        }
                                    }
                                }else{
                                    $temp=array();
                                    $temp['excost']="0";//外采成本
                                    $temp['ptempunit_price']=0;
                                    $temp['punit_price']=0;
                                    $temp['productcategory']="std";
                                    $temp['productcomboid']=$productid;
                                    $temp['productid']=$json_datapack_data['package']['ID'];
                                    $temp['productname']=$json_datapack_data['package']['Title'];
                                    $temp['realprice']="0";//成本价
                                    $temp['renewalcost']="0";
                                    $temp['renewalfee']="0";
                                    $temp['tagid']=$productid;
                                    $temp['prealprice']=0;//成本合计
                                    $temp['thepackage']=$json_datapack_data['package']['Title'];
                                    $temp['tranperformance']="0";
                                    $temp['unit_price']="0";
                                    $temp['version']="0";
                                    $temp['viewedtime']='';
                                    $temp['opendate']=$data['startdate'];
                                    $temp['closedate']=$data['expiredate'];
                                    $temp['packproduct']=$productid.'DZE'.$productid;
                                    $products[]=$temp;
                                }
                            }else{
                                $flag=true;
                                foreach($json_datapack_data['packageSpecificationList'] as $value){
                                    if($value['CanRenew'] && $value['ProductID']==$productid){
                                        $temp=array();
                                        $temp['excost']="0";//外采成本
                                        $temp['ptempunit_price']=0;
                                        $temp['punit_price']=0;
                                        $temp['productcategory']="std";
                                        $temp['productcomboid']=$productid;
                                        $temp['productid']=$value['ProductID'];
                                        $temp['productname']=$value['ProductTitle'];
                                        $temp['realprice']="0";//成本价
                                        $temp['renewalcost']="0";
                                        $temp['renewalfee']="0";
                                        $temp['tagid']=$productid;
                                        $temp['prealprice']=0;//成本合计
                                        $temp['thepackage']='--';
                                        $temp['tranperformance']="0";
                                        $temp['unit_price']="0";
                                        $temp['version']="0";
                                        $temp['viewedtime']='';
                                        $temp['opendate']=$data['startdate'];
                                        $temp['closedate']=$data['expiredate'];
                                        $temp['packproduct']=$productid.'DZE'.$value['ProductID'];
                                        $products[]=$temp;
                                        $flag=false;
                                        break;
                                    }
                                }
                            }
                            if($flag){
                                foreach($json_datapack_data['productSpecificationList'] as $value){
                                    if($value['ProductID']==$productid){
                                        $temp=array();
                                        $temp['excost']="0";//外采成本
                                        $temp['ptempunit_price']=0;
                                        $temp['punit_price']=0;
                                        $temp['productcategory']="std";
                                        $temp['productcomboid']=$productid;
                                        $temp['productid']=$value['ProductID'];
                                        $temp['productname']=$value['ProductTitle'];
                                        $temp['realprice']="0";//成本价
                                        $temp['renewalcost']="0";
                                        $temp['renewalfee']="0";
                                        $temp['tagid']=$productid;
                                        $temp['prealprice']=0;//成本合计
                                        $temp['thepackage']='--';
                                        $temp['tranperformance']="0";
                                        $temp['unit_price']="0";
                                        $temp['version']="0";
                                        $temp['viewedtime']='';
                                        $temp['opendate']=$data['startdate'];
                                        $temp['closedate']=$data['expiredate'];
                                        $temp['packproduct']=$productid.'DZE'.$value['ProductID'];
                                        $products[]=$temp;
                                        break;
                                    }
                                }
                            }
                            echo json_encode(array('success'=>true,'products'=>$products,'customerno'=>'','parentid'=>$productid));
                            return;
                        }
                    }
                }
                echo json_encode(array('success'=>true,'products'=>$products,'customerno'=>'','parentid'=>$productid));
                return;

            }
        }
            $data=array(
                'productclass'=>$categoryid,
                'agents'=>$agents,
                'contract_classification'=>$contract_classification
            );
            $datapack = $reocrdModel->getTyunWebBuy($data,$productid);

            $json_datapack = json_decode($datapack, true);
            $products=array();
            if($json_datapack['code']==200) {
                foreach ($json_datapack['data'] as $package){
                    if($package['Package']['ID']!=$productid){
                        continue;
                    }
                    foreach ($package['ProductSpecifications'] as $value) {
                        $temp=array();
                        $temp['excost']="0";//外采成本
                        $temp['ptempunit_price']=0;
                        $temp['punit_price']=0;
                        $temp['productcategory']="std";
                        $temp['productcomboid']=$productid;
                        $temp['productid']=$value['ID'];
                        $temp['productname']=$value['ProductTitle'];
                        $temp['realprice']="0";//成本价
                        $temp['renewalcost']="0";
                        $temp['renewalfee']="0";
                        $temp['tagid']=$productid;
                        $temp['prealprice']=0;//成本合计
                        $temp['thepackage']=$package['Package']['Title'];
                        $temp['tranperformance']="0";
                        $temp['unit_price']="0";
                        $temp['version']="0";
                        $temp['viewedtime']='';
                        $temp['opendate']=$data['startdate'];
                        $temp['closedate']=$data['expiredate'];
                        $temp['packproduct']=$productid.'DZE'.$value['ID'];
                        $products[]=$temp;
                    }
                }

            }
            echo json_encode(array('success'=>true,'products'=>$products,'customerno'=>'','parentid'=>$productid));
            return;
//        echo json_encode(array('success'=>false,'msg'=>'没有找到该产品的相关信息','subid'=>$productid));
//        return ;
    }
    public function getTyunOtherProduct($request){
        global $adb;
        $productid  = $request->get('productid');
        $servicecontractsid=$request->get('servicecontractsid');
        $query="SELECT a.*,b.contract_classification FROM vtiger_activationcode a left join vtiger_servicecontracts b on a.contractid=b.servicecontractsid WHERE a.contractid=? AND a.status!=2 AND a.comeformtyun=1 AND (a.productid=0 or a.productid is NULL)";
//        $query="SELECT * FROM vtiger_activationcode WHERE contractid=? AND `status`!=2 AND comeformtyun=1 limit 1";
        $result=$adb->pquery($query,array($servicecontractsid));
        if($adb->num_rows($result)){
//            $data = $adb->raw_query_result_rowdata($result, 0);
            while ($row=$adb->fetch_row($result)){
                $datas[] = $row;
            }
            $reocrdModel = Vtiger_Record_Model::getCleanInstance('ServiceContracts');
            foreach ($datas as $data){
                $separates = explode(',',$data['buyseparately']);
                if(!in_array($productid,$separates)){
                    continue;
                }
                $specificationId=array();
                $productnames=json_decode($data['productnames'],true);
                foreach ($productnames as $productname){
                    $specificationId[$productname['productID']]=$productname['specificationId'];
                }
                $products = array();
                //if($data['classtype'] == 'buy' || in_array($data['classtype'],array('crenew','cupgrade','cdegrade'))){
                $datapack=$reocrdModel->getOtherPorduct($data);
                $json_datapack=json_decode($datapack,true);
                if($json_datapack['code']==200){
                    $flag=false;
                    foreach($json_datapack['data'] as $value){
                        foreach($value['Products'] as $value1){
                            if($productid==$value1['ProductID']){
                                $flag=true;
                                $temp=array();
                                $temp['excost']="0";//外采成本
                                $temp['ptempunit_price']=0;
                                $temp['punit_price']=0;
                                $temp['productcategory']="std";
                                $temp['productcomboid']=$productid;
                                $temp['productid']=$value1['ProductID'];
                                $temp['productname']=$value1['ProductTitle'];
                                $temp['realprice']="0";//成本价
                                $temp['renewalcost']="0";
                                $temp['renewalfee']="0";
                                $temp['tagid']=$productid;
                                $temp['prealprice']=0;//成本合计
                                $temp['thepackage']='--';
                                $temp['tranperformance']="0";
                                $temp['unit_price']="0";
                                $temp['version']="0";
                                $temp['viewedtime']='';
                                $temp['standard']=isset($specificationId)?$specificationId[$productid]:'';
                                $temp['opendate']=$data['startdate'];
                                $temp['closedate']=$data['expiredate'];
                                $temp['packproduct']=$productid.'DZE'.$value1['ProductID'];
                                $temp['morestand']=$this->getTyunMoreStand($value1['ProductSpecifications']);

                                $products[]=$temp;
                                break;
                            }
                        }
                        if($flag){
                            break;
                        }
                    }

                    /*}elseif($data['classtype'] == 'renew'){
                        $this->getTyunProduct($request);
                        exit;
                    }*/
                }

                if(!$data['productid'] && empty($products)){
                    $productNames = $data['productnames'];
                    $productNames = str_replace("&quot;", '"', $productNames);
                    $productNames = json_decode($productNames, true);
                    foreach ($productNames as $productName) {
                        if($productid==$productName['productID']){
                            $flag = true;
                            $temp = array();
                            $temp['excost'] = "0";//外采成本
                            $temp['ptempunit_price'] = 0;
                            $temp['punit_price'] = 0;
                            $temp['productcategory'] = "std";
                            $temp['productcomboid'] = $productName['productID'];
                            $temp['productid'] = $productName['productID'];
                            $temp['productname'] = $productName['productTitle'];
                            $temp['realprice'] = "0";//成本价
                            $temp['renewalcost'] = "0";
                            $temp['renewalfee'] = "0";
                            $temp['tagid'] = $productName['productID'];
                            $temp['prealprice'] = 0;//成本合计
                            $temp['thepackage'] = '--';
                            $temp['tranperformance'] = "0";
                            $temp['unit_price'] = "0";
                            $temp['version'] = "0";
                            $temp['viewedtime'] = '';
                            $temp['opendate'] = '';
                            $temp['closedate'] = '';
                            $temp['packproduct'] = $productName['productID'] . 'DZE' . $productName['productID'];
                            $temp['morestand']=array();
                            $products[] = $temp;
                        }
                    }
                }
            }

        }else{
            $data= array();
            $reocrdModel = Vtiger_Record_Model::getCleanInstance('ServiceContracts');
            $datapack=$reocrdModel->getOtherPorduct($data);
            $json_datapack=json_decode($datapack,true);
            if($json_datapack['code']==200){
                $flag=false;
                foreach($json_datapack['data'] as $value){
                    foreach($value['Products'] as $value1){
                        if($productid==$value1['ProductID']){
                            $flag=true;
                            $temp=array();
                            $temp['excost']="0";//外采成本
                            $temp['ptempunit_price']=0;
                            $temp['punit_price']=0;
                            $temp['productcategory']="std";
                            $temp['productcomboid']=$productid;
                            $temp['productid']=$value1['ProductID'];
                            $temp['productname']=$value1['ProductTitle'];
                            $temp['realprice']="0";//成本价
                            $temp['renewalcost']="0";
                            $temp['renewalfee']="0";
                            $temp['tagid']=$productid;
                            $temp['prealprice']=0;//成本合计
                            $temp['thepackage']='--';
                            $temp['tranperformance']="0";
                            $temp['unit_price']="0";
                            $temp['version']="0";
                            $temp['viewedtime']='';
                            $temp['opendate']=$data['startdate'];
                            $temp['closedate']=$data['expiredate'];
                            $temp['packproduct']=$productid.'DZE'.$value1['ProductID'];
                            $temp['morestand']=$this->getTyunMoreStand($value1['ProductSpecifications']);
                            $products[]=$temp;
                            break;
                        }
                    }
                    if($flag){
                        break;
                    }
                }
            }
        }
        echo json_encode(array('success'=>true,'products'=>array_unique($products),'customerno'=>'','parentid'=>$productid));
        exit;
    }
    
    public function getTyunMoreStand($productSpecifications){
        if(count($productSpecifications)<0){
            return array();
        }
        foreach ($productSpecifications as $productSpecification){
            $data[]=array(
                'standardid'=>$productSpecification['ID'],
                'standardname'=>$productSpecification['Title']
            );
        }
        return $data;
    }
}