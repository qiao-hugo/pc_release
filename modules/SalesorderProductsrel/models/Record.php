<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class SalesorderProductsrel_Record_Model extends Vtiger_Record_Model {

	public static function getRelateProduct($salesorderid){
		
		$db = PearDatabase::getInstance();
        //2014-12-20更新start
        $sql="select vtiger_salesorderproductsrel.*,vtiger_products.productname,vtiger_products.realprice,vtiger_crmentity.createdtime as crmentitycreatedtime,vtiger_users.last_name,IFNULL(( SELECT vtiger_products.productname FROM vtiger_products WHERE vtiger_products.productid = vtiger_salesorderproductsrel.productcomboid ), '--' ) AS productcomboname from vtiger_salesorderproductsrel left join vtiger_products on vtiger_salesorderproductsrel.productid=vtiger_products.productid left join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_products.productid left join vtiger_users on vtiger_crmentity.smownerid=vtiger_users.id where vtiger_salesorderproductsrel.salesorderid=?";
		//2014-12-20日更新end
        //$sql="select vtiger_salesorderproductsrel.*,vtiger_products.productname from vtiger_salesorderproductsrel left join vtiger_products on vtiger_salesorderproductsrel.productid=vtiger_products.productid where vtiger_salesorderproductsrel.salesorderid=?";
		$result=$db->pquery($sql,array($salesorderid));
		//print_r($result);die;
		$temp=array();
		for($i=0; $i<$db->num_rows($result); $i++) {
			$salesorderproductsrelid= $db->query_result($result, $i,'salesorderproductsrelid');
			$productid = $db->query_result($result, $i, 'productid');
			$productform =	$db->query_result($result, $i, 'productform');
			$productname =	$db->query_result($result, $i, 'productname');
            //2014-12-20更新start
            $marketprice =	$db->query_result($result, $i, 'realprice');
			$auditorid =	$db->query_result($result, $i, 'last_name');
			$createtime =	$db->query_result($result, $i, 'createtime');
            $productcomboname =	$db->query_result($result, $i, 'productcomboname');
            //2015-11-16
            $agelife =$db->query_result($result, $i, 'agelife');
			$productnumber =$db->query_result($result, $i, 'productnumber');
			$istyunweb =$db->query_result($result, $i, 'istyunweb');
			$thepackage =$db->query_result($result, $i, 'thepackage');
			$extracost =$db->query_result($result, $i, 'extracost');
            //2015-11-16
			//echo $createtime;
			$temp[$productid]=array('salesorderproductsrelid'=>$salesorderproductsrelid,'productid'=>$productid,'notecontent'=>$productform,'productname'=>$productname,'realprice'=>$marketprice,'auditorid'=>$auditorid,'createtime'=>$createtime,'productcomboname'=>$productcomboname,'productnumber'=>$productnumber,'agelife'=>$agelife,
                'thepackage'=>$thepackage,'istyunweb'=>$istyunweb,'extracost'=>$extracost
            );
            //2014-12-20日更新end
            /*
			$marketprice =	$db->query_result($result, $i, 'marketprice');
			$temp[$productid]=array('salesorderproductsrelid'=>$salesorderproductsrelid,'productid'=>$productid,'notecontent'=>$productform,'productname'=>$productname,'realprice'=>$marketprice);	
            */
        }
        //var_dump($temp);
		return $temp;
	}
	//解决表单问题
	function get($key) {
		$value = parent::get($key);
		if ($key === 'productform') {
			return decode_html($value);
		}
		
		return $value;
	}
	/**
	 * 状态设置
	 * @param unknown $servicecontractid
	 * @param unknown $salesorderid
	 */
	public function getStatus($servicecontractid,$salesorderid){
		
	}
	/**
	 * 数据角色权限
	 */
	public function getRole(){
		
	}
	
	
	/**
	 * 获取节点完成状态
	 * @param 合同编号 $servicecontractid
	 * @param 节点状态 $nodestatus 合同产品审核 0 ->产品成本确认 1 ->产品任务 2
	 * @return true:完成 false:未完成
	 */
	public static function getNoteFinishStatus($servicecontractid,$nodestatus){
		$sql="select * from vtiger_salesorderproductsrel where servicecontractsid=? and nodestatus=?";
		$db = PearDatabase::getInstance();
		$result=$db->pquery($sql,array($servicecontractid,$nodestatus));
		for($i=0; $i<$db->num_rows($result); $i++) {
			$finishstatus= $db->query_result($result, $i,'finishstatus');
			if ($finishstatus !='1'){
				return false;
			}
		}
		return true;
	}
	
	/**
	 * 更新节点状态
	 * @param 合同编号 $servicecontractid
	 * @param 节点状态 $nodestatus 合同产品审核 0 ->产品成本确认 1 ->产品任务 2
	 */
	public static function updateRelateProductStatus($servicecontractid,$nodestatus){
		$updateSql="update vtiger_salesorderproductsrel set nodestatus=?,isvisible=1,finishstatus=0 where servicecontractsid=?";
		$db = PearDatabase::getInstance();
		$result=$db->pquery($updateSql,array($servicecontractid,$nodestatus));
	}
	public function getName() {
		$displayName = $this->get('productid');
		
		if(empty($displayName)) {
			$list=$this->getData();
			foreach($list as $val){
				$displayName=$val;break;
			}
		}
		return Vtiger_Util_Helper::toSafeHTML(decode_html($displayName));
	}
}
