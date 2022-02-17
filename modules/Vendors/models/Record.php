<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Vendors_Record_Model extends Vtiger_Record_Model {

	function getCreatePurchaseOrderUrl() {
		$purchaseOrderModuleModel = Vtiger_Module_Model::getInstance('PurchaseOrder');

		return "index.php?module=".$purchaseOrderModuleModel->getName()."&view=".$purchaseOrderModuleModel->getEditViewName()."&vendor_id=".$this->getId();
	}

	/**
	 * Function to get List of Fields which are related from Vendors to Inventyory Record
	 * @return <array>
	 */
	public function getInventoryMappingFields() {
		return array(
				//Billing Address Fields
				array('parentField'=>'city', 'inventoryField'=>'bill_city', 'defaultValue'=>''),
				array('parentField'=>'street', 'inventoryField'=>'bill_street', 'defaultValue'=>''),
				array('parentField'=>'state', 'inventoryField'=>'bill_state', 'defaultValue'=>''),
				array('parentField'=>'postalcode', 'inventoryField'=>'bill_code', 'defaultValue'=>''),
				array('parentField'=>'country', 'inventoryField'=>'bill_country', 'defaultValue'=>''),
				array('parentField'=>'pobox', 'inventoryField'=>'bill_pobox', 'defaultValue'=>''),

				//Shipping Address Fields
				array('parentField'=>'street', 'inventoryField'=>'ship_street', 'defaultValue'=>''),
				array('parentField'=>'city', 'inventoryField'=>'ship_city', 'defaultValue'=>''),
				array('parentField'=>'state', 'inventoryField'=>'ship_state', 'defaultValue'=>''),
				array('parentField'=>'postalcode', 'inventoryField'=>'ship_code', 'defaultValue'=>''),
				array('parentField'=>'country', 'inventoryField'=>'ship_country', 'defaultValue'=>''),
				array('parentField'=>'pobox', 'inventoryField'=>'ship_pobox', 'defaultValue'=>'')
		);
	}

	static public function getVendorstate() {
		$sql = "select vendorstate from vtiger_vendorstate  order by sortorderid";
		global $adb;
		$listResult = $adb->pquery($sql, array());
		$data = array();
		while($rawData=$adb->fetch_array($listResult)) {
            $data[] = $rawData['vendorstate'];
        }
        return $data;
	}

	/*
	返回充值平台
	*/
	static public function getRechargeplatform() {
		$sql = "select topplatformid,topplatform from vtiger_topplatform";
		global $adb;
		$listResult = $adb->pquery($sql, array());
		$data = array();
		while($rawData=$adb->fetch_array($listResult)) {
            $data[$rawData['topplatformid']] = $rawData['topplatform'];
        }
        return $data;
	}

	// 获取供应商类型
	static public function getVendortype() {
		$sql = "select vendortype from vtiger_vendortype order by sortorderid";
		global $adb;
		$listResult = $adb->pquery($sql, array());
		$data = array();
		while($rawData=$adb->fetch_array($listResult)) {
            $data[] = $rawData['vendortype'];
        }
        return $data;
	} 

	// 供应商报表
	static public function vendorSale() {

		$sql = "SELECT vtiger_vendor.vendorname, vtiger_vendor.address, vtiger_vendor.linkman, vtiger_vendor.linkphone, vtiger_vendor.vendortype , vtiger_suppliercontracts.contract_no, IF(ISNULL(SUM(vtiger_salesorderproductsrel.purchasemount)), 0, SUM(vtiger_salesorderproductsrel.purchasemount)) AS purchasemount, ( SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id = vtiger_crmentity.smownerid ) AS last_name, ( SELECT vtiger_departments.departmentname FROM vtiger_departments LEFT JOIN vtiger_user2department ON vtiger_user2department.departmentid = vtiger_departments.departmentid WHERE vtiger_user2department.userid = vtiger_crmentity.smownerid ) AS departmentname FROM vtiger_vendor LEFT JOIN vtiger_crmentity ON vtiger_vendor.vendorid = vtiger_crmentity.crmid LEFT JOIN vtiger_salesorderproductsrel ON vtiger_salesorderproductsrel.vendorid = vtiger_vendor.vendorid LEFT JOIN vtiger_suppliercontracts ON vtiger_suppliercontracts.vendorid = vtiger_vendor.vendorid WHERE vtiger_crmentity.deleted = 0 GROUP BY vtiger_vendor.vendorname, vtiger_suppliercontracts.contract_no ORDER BY vtiger_vendor.vendorid DESC";
		global $adb;
		$listResult = $adb->pquery($sql, array());
		$data = array();
		while($rawData=$adb->fetch_array($listResult)) {
            $data[] = $rawData;
        }
        return $data;
	}

	// 根据供应商名称和类型进行搜索
	static public function searchVendor($productid, $effectdate_start, $effectdate_end, $enddate_start, $enddate_end) {
		$where = "";
		if(! empty($productid)) {
			$where .= " AND vtiger_vendorsrebate.productid='{$productid}'";
		}
		if(! empty($effectdate_start)) {
			$where .= " AND STR_TO_DATE('{$effectdate_start}', '%Y-%m-%d') <=  STR_TO_DATE(effectdate, '%Y-%m-%d') ";
		}
		if(! empty($effectdate_end)) {
			$where .= " AND STR_TO_DATE('{$effectdate_end}', '%Y-%m-%d') >=  STR_TO_DATE(effectdate, '%Y-%m-%d') ";
		}

		if(! empty($enddate_start)) {
			$where .= " AND STR_TO_DATE('{$enddate_start}', '%Y-%m-%d') <=  STR_TO_DATE(enddate, '%Y-%m-%d') ";
		}
		if(! empty($enddate_end)) {
			$where .= " AND STR_TO_DATE('{$enddate_end}', '%Y-%m-%d') >=  STR_TO_DATE(enddate, '%Y-%m-%d') ";
		}


		$sql = "SELECT
					vtiger_vendor.vendorname,
					vtiger_vendorsrebate.rebate,
					vtiger_vendorsrebate.effectdate,
					vtiger_vendorsrebate.enddate,
					vtiger_vendorsrebate.productname,
					vtiger_vendorsrebate.vexplain
				FROM
					vtiger_vendor
				LEFT JOIN vtiger_crmentity ON vtiger_vendor.vendorid = vtiger_crmentity.crmid
				INNER JOIN vtiger_vendorsrebate ON vtiger_vendorsrebate.vendorid=vtiger_vendor.vendorid
				WHERE
					vtiger_crmentity.deleted=0
					{$where}
				ORDER BY
					vtiger_vendorsrebate.rebate DESC
				LIMIT 10";
		//echo $sql;die;
		global $adb;
		$listResult = $adb->pquery($sql, array());
		$data = array();
		while($rawData=$adb->fetch_array($listResult)) {
            $data[] = $rawData;
        }
        return $data;
	}

	// 获取产品列表
	static public function getProducts() {
		$sql = "SELECT vtiger_products.productid, vtiger_products.productname FROM vtiger_products INNER JOIN vtiger_crmentity ON vtiger_products.productid = vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted = 0 AND vtiger_products.productiscriminate='outpurchase'";
		global $adb;
		$listResult = $adb->pquery($sql, array());
		$data = array();
		while($rawData=$adb->fetch_array($listResult)) {
            $data[] = $rawData;
        }
        return $data;
	}


	// 获取产品返点
	static public function getVendorsrebate($vendorid) {
		$sql = "select * from vtiger_vendorsrebate LEFT JOIN vtiger_products ON vtiger_products.productid=vtiger_vendorsrebate.productid where vtiger_vendorsrebate.vendorid=? AND vtiger_vendorsrebate.deleted=0";
		global $adb;
		$listResult = $adb->pquery($sql, array($vendorid));
		$res_cnt = $adb->num_rows($listResult);
		$data = array();
		if($res_cnt > 0) {
			while($rawData=$adb->fetch_array($listResult)) {
	            $data[] = $rawData;
	        }
		}
        return $data;
	}

	static public function _logs($data, $file = 'logs_'){
        $year   = date("Y");
        $month  = date("m");
        $dir    = $root_directory.'test_logs/' . $year . '/' . $month . '/';
        if(!is_dir($dir)) {
            mkdir($dir,0755,true);
        }
        $file = $dir . $file . date('Y-m-d').'.txt';
        @file_put_contents($file, '----------------' . date('H:i:s') . '--------------------'.PHP_EOL.var_export($data, true).PHP_EOL, FILE_APPEND);
    }
    /**
     * 检查供应商名称是否重复
     * @param Vtiger_Request $request
     */
    public function checkVendorName(Vtiger_Request $request){
        $db=PearDatabase::getInstance();
        $recordid=$request->get('record');
        $vendorname=$request->get('vendorname');
        $sql='';
        if(!empty($recordid)){
            $sql=' vendorid!='.$recordid.' AND';
        }
        $label=str_replace('\\','',$vendorname);
        $newaccountname=preg_replace('/\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;|&quot;|　|&apos;|&amp;|&lt;|&gt;|&#039;|&ldquo;|&rdquo;|&lsquo;|&rsquo;|&hellip;|\“|\”|\‘|\〉|\〈|\’|\〖|\〗|\【|\】|\、|\·|\……|\…|\——|\＋|\－|\＝|\,|\<|\.|\>|\/|\?|\;|\:|\\\'|\"|\[|\{|\]|\}|\\|\||\`|\~|\!|\@|\#|\$|\%|\^|\\&|\*|\(|\)|\-|\_|\=|\+|\，|\＜|\．|\＞|\／|\？|\；|\：|\＇|\＂|\［|\{|\］|\}|\＼|\｜|\｀|\￣|\！|\＠|\#|\＄|\％|\＾|\＆|\＊|\（|\）|\－|\＿|\＝|\＋|\，|\《|\．|\》|\、|\？|\；|\：|\’|\”|\【|\｛|\】|\｝|\＼|\｜|\·|\～|\！|\＠|\＃|\￥|\％|\……|\…|\＆|\×|\（|\）|\－|\——|\—|\＝|\＋|\，|\《|\。|\》|\、|\？|\；|\：|\‘|\“|\【|\｛|\】|\｝|\、|\||\·|\~|\！|\@|\#|\￥|\%|\……|\…|\&|\*|\（|\）|\-|\——|\=|\+/u','',$label);
        $newaccountname=strtoupper($newaccountname);
        $query='SELECT vendorid FROM `vtiger_uniquevendorname` WHERE'.$sql.' vendorname=? limit 1';
        $result=$db->pquery($query,array($newaccountname));
        if($db->num_rows($result)){
            $record=$db->query_result($result,0,'vendorid');
            $query="SELECT (select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_crmentity.smownerid=vtiger_users.id) as username FROM vtiger_vendor LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_vendor.vendorid WHERE vtiger_crmentity.deleted=0 AND vtiger_vendor.vendorid=?";
            $userResult=$db->pquery($query,array($record));
            $username=$db->query_result($userResult,0,'username');
            $data=array("flag"=>true,"msg"=>'供应商已经存在,负责人:'.$username);
        }else{
            $data=array("flag"=>false);
        }
        return $data;
    }
    /**
     * 取银行账户
     * @param $vendorid
     * @return array
     * @author: steel.liu
     * @Date:xxx
     *
     */
    public function getVendorBank($vendorid){
        global $adb;
        $query='SELECT vendorbankid,vendorid,bankaccount,bankname,banknumber,bankcode,allowtransaction,deleted,createdtime,createdid,deletedid,deletedtime FROM vtiger_vendorbank WHERE deleted=0 and vendorid=?';
        $result=$adb->pquery($query,array($vendorid));
        $data=array();
        while($row=$adb->fetch_array($result)){
            $temp['vendorbankid']=$row['vendorbankid'];
            $temp['bankaccount']=$row['bankaccount'];
            $temp['bankname']=$row['bankname'];
            $temp['banknumber']=$row['banknumber'];
            $temp['bankcode']=$row['bankcode'];
            $temp['allowtransaction']=$row['allowtransaction'];
            $data[]=$temp;
        }
        return $data;
    }
    /**
     * 获取供应商名称及ID
     * @param $request
     * @return array
     */
    public function getVendorByName($request){
        global $adb;
        $accountName=$request->get('vendorName');
        $query='SELECT vtiger_vendor.vendorname,vtiger_vendor.vendorid FROM vtiger_vendor LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_vendor.vendorid WHERE vtiger_crmentity.deleted=0 AND vtiger_vendor.modulestatus=\'c_complete\' AND vendorname like ? LIMIT 50';
        $result=$adb->pquery($query,array($accountName.'%'));
        $retrunData=array('success'=>false,'msg'=>'没有供应商信息');
        if($adb->num_rows($result)){
            $rowData=array();
            while($row=$adb->fetch_array($result)){
                $rowData[]=array('vendorid'=>(int)$row['vendorid'],'vendorname'=>$row['vendorname']);
            }
            $retrunData=array('success'=>true,'data'=>$rowData);
        }
        return $retrunData;
    }

    public function isUploadQualificationFiles($record){
        $db = PearDatabase::getInstance();
        $result = $db->pquery("select 1 from vtiger_files where description='Vendors' and relationid=? and style='files_style11' and delflag=0",array($record));
        return $db->num_rows($result)>0? true:false;
    }

    public function isWorkFlowVerifying($workflowsid,$record){
        $db = PearDatabase::getInstance();
        $sql = "select * from vtiger_salesorderworkflowstages where workflowsid=? and salesorderid=? and isaction!=2";
        $result = $db->pquery($sql,array($workflowsid,$record));
        return $db->num_rows($result)>0?1:0;
    }

    public function doChangeSmowner($recordId,$newSmownerid){
        $db = PearDatabase::getInstance();
        $db->pquery("update vtiger_crmentity set smownerid=? where crmid=?",array($newSmownerid,$recordId));
        $recordModel=Vtiger_Record_Model::getInstanceById($recordId,'Vendors');
        $array = array(
            'assigned_user_id'=>array(
                'oldValue'=>$recordModel->get("assigned_user_id"),
                'currentValue'=>$newSmownerid
            )
        );
        $this->setModTracker('Vendors',$recordId,$array);
        return array("success"=>true,'msg'=>'修改成功');
    }
}
