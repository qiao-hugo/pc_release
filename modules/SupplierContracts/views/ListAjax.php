<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class SupplierContracts_ListAjax_View extends Vtiger_ListAjax_View {

    function __construct() {
        parent::__construct();
        $this->exposeMethod('getQrcode');
        $this->exposeMethod('getLoginStatus');
    }
	public function process(Vtiger_Request $request) {

		$mode = $request->get('mode');
		if(!empty($mode) && $mode !='edit' ) {
		    $this->invokeExposedMethod($mode, $request);
			return;
		}
		
		$moduleName = $request->getModule();
		$mode=$request->getMode();
		$productid  = $request->get('productid');
		$success=false;
		$products=array();
		if(!empty($productid)){
			global $adb;
			if($mode=='edit'){
				$result =$adb->pquery('select *,vtiger_salesorderproductsrel.productsolution AS newsolution,vtiger_salesorderproductsrel.realmarketprice AS realmarketprice,productcomboid,IF (productcomboid IS NULL OR productcomboid = 0,\'--\',(SELECT vtiger_products.realprice FROM vtiger_products WHERE vtiger_products.productid=vtiger_salesorderproductsrel.productcomboid)) AS ptemprealprice,IF (productcomboid IS NULL OR productcomboid = 0,\'--\',(SELECT vtiger_products.unit_price FROM vtiger_products WHERE vtiger_products.productid=vtiger_salesorderproductsrel.productcomboid)) AS ptempunit_price,IFNULL(vtiger_products.minmarketprice,0) AS excost,IFNULL(vtiger_salesorderproductsrel.purchasemount,0) AS purchasemount,IF( productcomboid is NULL or productcomboid=0,vtiger_salesorderproductsrel.productid,productcomboid) as tagid from vtiger_salesorderproductsrel left join vtiger_products on  vtiger_products.productid=vtiger_salesorderproductsrel.productid  where servicecontractsid=? AND(multistatus=? OR multistatus = ?) ORDER BY tagid',array($productid,0,1));
//var_dump($result);die;
                //fulei没有=，tagid，没有套餐，有产品
				//$adb->pquery("SELECT * from vtiger_products where productid in( SELECT productid FROM vtiger_salesorderproductsrel WHERE servicecontractsid = ? ) ", array($productid));
				$customerno = $adb->pquery("select contract_no from vtiger_servicecontracts where servicecontractsid=?",array($productid));
				$customerno = $customerno->fields['0'];

				$row=$adb->num_rows($result);
			
				if($row>1) {
					$success=true;
                    $arrproductsid=array();
					for ($i=0; $i<$row; ++$i) {
						$product = $adb->fetchByAssoc($result);
						$products[]=$product;
                        $arrproductsid[]=$product['productid'];
					}
                    $products=$this->morestand($products,implode(',',$arrproductsid));

				}elseif($row==1){
					$success=true;
					$products[] = $adb->query_result_rowdata($result);
                    $products=$this->morestand($products,$products[0]['productid']);
                    $products[0]['thepackage']='--';
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
                            $product['prealprice']='NO';
                            $product['punit_price']=$punit_price;
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

    public function getQrcode(Vtiger_Request $request){
        $moduleModel = Vtiger_Module_Model::getInstance("SupplierContracts");//module相关的数据
        if(false&&!$moduleModel->exportGrouprt('SupplierContracts','Received')){   //权限验证
            return;
        }
        $status=$request->get("status");
        if(!empty($status)){
            $status='Supplier'.$status;
            $oldip=$_SESSION[$status];
            global $adb;
            $adb->pquery("DELETE FROM vtiger_qrcodelogin WHERE ercode=",array($oldip));
            $ip=getip();
            $ip=str_replace('.','',$ip);
            $ip=$ip+time();
            $_SESSION[$status]=$ip;
            $adb->pquery("insert into vtiger_qrcodelogin(ercode) VALUES(?)",array($ip));
            $qrip=$moduleModel->base64encode($ip);
            $value = 'http://m.crm.71360.com/otherlogin.php?loginid='.$qrip."&mode=".$status;//二维码内容
            $moduleModel->getQRcode($value,2,'L');
        }

    }
    /**
     * 扫码登陆
     */
    public function getLoginStatus(Vtiger_Request $request){
        $status=$request->get("status");
        $status='Supplier'.$status;
        $ip=$_SESSION[$status];

        $arr=array("success"=>false);
        if(!empty($ip)){
            global $adb;
            $result=$adb->pquery("SELECT vtiger_qrcodelogin.userid,vtiger_qrcodelogin.`status`,vtiger_users.last_name FROM vtiger_qrcodelogin LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_qrcodelogin.userid WHERE ercode=? limit 1",array($ip));
            if($adb->num_rows($result)) {
                $data = $adb->raw_query_result_rowdata($result);
                if ($data['status'] == 1) {
                    $arr = array("success" => false, 'status' => 1);
                } else if ($data['status'] == 2) {
                    $cstatus='confirm'.$status;
                    $_SESSION[$cstatus]=$data['userid'];
                    $adb->pquery('delete from vtiger_qrcodelogin where ercode=?', array($ip));
                    unset($_SESSION[$status]);
                    $arr = array("success" => true, 'status' => 2,'userid'=>$data['userid'],'username'=>$data['last_name']);
                }
            }else{
                $arr = array("success" => false, 'status' => 3);
            }

        }
        echo json_encode($arr);
    }
}