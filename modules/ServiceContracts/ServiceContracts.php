<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once('data/CRMEntity.php');
require_once('data/Tracker.php');

class ServiceContracts extends CRMEntity {
	var $db, $log; // Used in class functions of CRMEntity

	var $table_name = 'vtiger_servicecontracts';
	var $table_index= 'servicecontractsid';
	var $column_fields = Array();

	/** Indicator if this is a custom module or standard module */
	var $IsCustomModule = true;
	var $multirow_tables=array('vtiger_receivedpayments','vtiger_Workflows');
	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('servicecontractsid');

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	var $tab_name = Array('vtiger_crmentity', 'vtiger_servicecontracts');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	var $tab_name_index = Array('vtiger_crmentity' => 'crmid','vtiger_servicecontracts' => 'servicecontractsid');
	//var $relate_table=Array();
	/**
	 * Mandatory for Listing (Related listview)
	 */
	var $list_fields = Array (
		/* Format: Field Label => Array(tablename, columnname) */
		// tablename should not have prefix 'vtiger_'
		'Modulestatus' => Array('servicecontracts','modulestatus'),
		'Priority' => Array('servicecontracts','productid'),
		'Total' => Array('servicecontracts', 'total'),
		'sign Date' => Array('servicecontracts','signdate'),
		'Contract No' => Array('servicecontracts','contract_no'),

	);
	var $list_fields_name = Array (
		/* Format: Field Label => fieldname */
		'Modulestatus' => 'modulestatus',
		'Priority' => 'productid',
		'Total' => 'total',
		'sign Date' => 'signdate',
		'Contract No' =>  'contract_no',
		//
	);

	// Make the field link to detail view
	var $list_link_field = 'subject';

	// For Popup listview and UI type support
	var $search_fields = Array(
		/* Format: Field Label => Array(tablename, columnname) */
		// tablename should not have prefix 'vtiger_'
		//'Subject' => Array('servicecontracts', 'subject'),
		'Contract No' => Array('servicecontracts', 'contract_no'),
		'Receive Id' => Array('vtiger_crmentity','receiveid'),
		'ServiceId' => Array('vtiger_servicecomments','serviceid'),
		'accountownerid' => Array('vtiger_servicecontracts','accountownerid'),
		'Related to' => Array('servicecontracts','sc_related_to')
	);
	var $search_fields_name = Array (
		/* Format: Field Label => fieldname */
		//'Subject' => 'subject',
		'Contract No' => 'contract_no',
		//2015年4月3日 星期五 工单弹出 合同领取人改为业绩所属人
		//'Assigned To' => 'assigned_user_id',   //合同领取人
		'Receive Id'  =>'receiveid',             //业绩所属人
		'ServiceId'		=>	'serviceid',
		'accountownerid'		=>	'accountownerid',
		//end
		//'Used Units' => 'used_units',
		//'Total Units' => 'total_units'
		'Related to'=>'sc_related_to',
		'Total'=>'total'
	);

	// For Popup window record selection
	var $popup_fields = Array ('subject');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	var $sortby_fields = Array();

	// For Alphabetical search
	var $def_basicsearch_col = 'subject';

	// Column value to use on detail view record text display
	var $def_detailview_recname = 'subject';

	// Required Information for enabling Import feature
	var $required_fields = Array ('assigned_user_id'=>1);

	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	var $mandatory_fields = Array('subject','assigned_user_id');

	// Callback function list during Importing
	var $special_functions = Array('set_import_assigned_user');

	var $default_order_by = 'subject';
	var $default_sort_order='ASC';

	var $relatedmodule_list=array('ReceivedPayments','ActivationCode', 'ActivationCodeDetail', 'Files','Invoicesign','ContractsAgreement','TyunStationSale','Vmatefiles');
	var $relatedmodule_fields=array('ReceivedPayments'=>array('paytitle'=>'Paytitle','owncompany'=>'Owncompany','reality_date'=>'回款时间','unit_price'=>'金额','createtime'=>'创建时间'),
		'Invoice'=>array(),
		'ActivationCode'=>array(
			'classtype'=>'classtype',
			'customername'=>'customername',
			'productid'=>'productid',
			'productlife'=>'productlife',
			'expiredate'=>'expiredate',
			'mobile'=>'mobile',
			'salesphone'=>'salesphone',
			'usercode'=>'usercode',
			'salesname'=>'salesname'),
		'ActivationCodeDetail'=>array('activecode'=>'activecode','activetype'=>'activetype', 'startdate'=>'startdate','enddate'=>'enddate', 'remark'=>'remark',),
		'Files'=>array(
			'name'=>'name',
			'uploader'=>'uploader',
			'uploadtime'=>'uploadtime',
			'style'=>'style',
			'filestate'=>'filestate',
			'deliversuserid'=>'deliversuserid',
			'delivertime'=>'delivertime',
			'remarks'=>'remarks'
		),
		'Invoicesign'=>array('path'=>'签名'),
		'ContractsAgreement'=>array(
			'newservicecontractsno'=>'newservicecontractsno',
			'account_id'=>'Account Name',
			'assigned_user_id'=>'Assigned To'
		),
		'TyunStationSale'=>array('companyname'=>'companyname',
			'agentcode'=>'agentcode',
			'serviceinfo'=>'serviceinfo',
			'signaddress'=>'signaddress',
			'signdate'=>'signdate',
			'custphone'=>'custphone',
			'loginname'=>'loginname',
			'opendate'=>'opendate',
			'finnishdate'=>'finnishdate',
			'salesname'=>'salesname',
			'salesphone'=>'salesphone',
			'serviceloginname'=>'serviceloginname'),
		'Vmatefiles'=>array(
			'name'=>'name',
			'uploader'=>'uploader',
			'uploadtime'=>'uploadtime',
			'style'=>'style',
			'filestate'=>'filestate',
			'deliversuserid'=>'deliversuserid',
			'delivertime'=>'delivertime',
			'remarks'=>'remarks'
		),
	);


	function __construct() {
		global $log;
		$this->column_fields = getColumnFields(get_class($this));
		$this->db = new PearDatabase();
		$this->log = $log;
	}
	//在保存合同data，自定义的保存方法
	function save_module($module) {
		global $configcontracttypeNameTYUN;
		if($_REQUEST['action']!='SaveAjax'){
			global $current_user;
			//wangbin 添加合同不需要任何回款信息
			/* if(empty($_REQUEST['record'])){

                $date_var = date("Y-m-d H:i:s");
                $value=array('receivedpaymentsid'=>$this->db->getUniqueID('vtiger_receivedpayments'),'relmodule'=>'首款','relatetoid'=>$_REQUEST['currentid']);
                $value['createid']=$current_user->id;
                $value['createtime']= $this->db->formatDate($date_var, true);
                $value['unit_price']=$_REQUEST['unit_price'];
                $value['bank']	=$_REQUEST['bank'];
                $value['discontinued']=$_REQUEST['discontinued']=='on'?1:0;
                $value['accountid']=$_REQUEST['sc_related_to'];
                if(empty($value['unit_price']) || empty($value['bank'])){
                    $value['discontinued']=0;
                }elseif($value['discontinued']==1) {
                    $value['checkid']=$value['createid'];
                    $value['checktime']=$value['createtime'];
                    $value['reality_date']=$value['createtime'];
                }

                $first="insert into vtiger_receivedpayments (" . implode(",", array_keys($value)) . ") values(" . generateQuestionMarks($value) . ")";

                $this->db->pquery($first, $value);
                $value2=array('receivedpaymentsid'=>$this->db->getUniqueID('vtiger_receivedpayments'),
                        'relmodule'=>'尾款',
                        'sort'=>1,
                        'createid'=>$value['createid'],
                        'createtime'=>$value['createtime'],
                        'accountid'=>$_REQUEST['sc_related_to'],
                        'relatetoid'=>$_REQUEST['currentid']);
                $first="insert into vtiger_receivedpayments (" . implode(",", array_keys($value2)) . ") values(" . generateQuestionMarks($value2) . ")";

                $this->db->pquery($first, $value2);





                /* foreach($productids as $productid){
                    $array=array($this->db->getUniqueID('vtiger_salesorderproductsrel'),$productid,);

                }
            } */
			//加入更新合同类型的联动id;
			$id=$_REQUEST['record']>0?$_REQUEST['record']:$this->id;
			//拜访单客户合同信息
			$this->db->pquery('UPDATE `vtiger_visitaccountcontract` SET contractid=? WHERE accountid=? AND accountid>0 AND (contractid=0 OR contractid=\'\' OR contractid IS NULL)',array($id,$_REQUEST['sc_related_to']));
			$supercollar='';
			if($_REQUEST['supercollar']>0){
				$supercollar=",supercollar=".$_REQUEST['supercollar'].' ';
			}
			$sql="UPDATE vtiger_servicecontracts SET parent_contracttypeid=?{$supercollar} WHERE servicecontractsid=?";
			$this->db->pquery($sql,array($_REQUEST['parent_contracttypeid'],$id));
			//有套餐则将相关联的价格成本写入到vtiger_contractperformancecost方便中小业绩核算
			if(!empty($_REQUEST['productid'])){
				$sql='UPDATE vtiger_contractperformancecost SET deleted=1 WHERE servicecontractsid=?';
				$this->db->pquery($sql,array($id));
				//正常合同产品的年限大于增送产品的年限,
				$agelife=empty($_REQUEST['agelife'])?1:(max($_REQUEST['agelife'])/12);
				$sql='INSERT INTO vtiger_contractperformancecost(`servicecontractsid`,`productid`,`marketpricesone`,`marketpricestwo`,`achievementcost`,`renewalfee`,`renewalcost`,agelife,`createdtime`) SELECT '.$id.',productid,unit_price,otherunit_price,tranperformance,renewalfee,renewalcost,'.$agelife.',\''.date('Y-m-d H:i:s').'\' FROM vtiger_products where productid in('.implode(',',$_REQUEST['productid']).')';
				$this->db->pquery($sql,array());
			}
			if(!empty($_REQUEST['Signid'])){
				//$user = CRMEntity::getInstance('Users');
				//$current_usero = $user->retrieveCurrentUserInfoFromFile($_REQUEST['Signid']);
				$sql='UPDATE vtiger_servicecontracts SET signdempart=(SELECT departmentid FROM vtiger_user2department WHERE userid=? limit 1) WHERE servicecontractsid=?';
				$this->db->pquery($sql,array($_REQUEST['Signid'],$this->id));
			}

			//获取合同类型
			$productcategory=$_REQUEST['productcategor'];
			if(in_array('nostd',$productcategory)){
				$producttype=1;
				$status='unaudited';
				$workflow='ServiceContractsnostd';
			}else{
				$producttype=0;
				$status='pass';
				$workflow='ServiceContracts';

			}
			$query='SELECT vtiger_invoicecompanybill.billingcontent,vtiger_contract_type.bussinesstype FROM vtiger_invoicecompanybill JOIN vtiger_contractsproductsrel ON vtiger_contractsproductsrel.relcontractsproductsid=vtiger_invoicecompanybill.relcontractsproductsid 
                    JOIN vtiger_contract_type ON vtiger_contract_type.contract_typeid=vtiger_contractsproductsrel.contract_type
                    LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.contract_type = vtiger_contract_type.contract_type
                    WHERE vtiger_invoicecompanybill.invoicecompany=vtiger_servicecontracts.invoicecompany AND vtiger_invoicecompanybill.deleted=0 AND vtiger_servicecontracts.servicecontractsid=? limit 1';
			$billResult=$this->db->pquery($query,array($id));
			$billNum=$this->db->num_rows($billResult);
			if($billNum>0){
				$billData=$this->db->query_result_rowdata($billResult,0);
				$this->db->pquery('update vtiger_servicecontracts set vtiger_servicecontracts.billcontent=?,vtiger_servicecontracts.bussinesstype=? where servicecontractsid=? ',array($billData['billingcontent'],$billData['bussinesstype'],$id));
			}else{
				$this->db->pquery('update vtiger_servicecontracts,vtiger_contract_type,vtiger_contractsproductsrel set vtiger_servicecontracts.producttype=?,vtiger_servicecontracts.billcontent=vtiger_contractsproductsrel.billingcontent,vtiger_servicecontracts.bussinesstype=vtiger_contract_type.bussinesstype where servicecontractsid=? AND vtiger_contract_type.contract_typeid=vtiger_contractsproductsrel.contract_type AND vtiger_servicecontracts.contract_type = vtiger_contract_type.contract_type',array($producttype,$id));
			}

			//$this->db->pquery('update vtiger_servicecontracts set producttype=? where servicecontractsid=?',array($producttype,$_REQUEST['currentid']));

			if($_REQUEST['iscomplete']=='on'){
				$this->db->pquery('UPDATE vtiger_servicecontracts SET modulestatus=\'c_complete\' WHERE servicecontractsid=?',array($id));
			}
			/* $productcomboid=$_REQUEST['productcomboid'];//父级id
             $unit_price=$_REQUEST['unit_price'];//合同价格(市场价)//产品表里读取的价格隐藏值
             $realmarketprice=$_REQUEST['realmarketprice'];                   //合同价格（录入实际的合同价格）*/
			$productids=$_REQUEST['productids'];//部分产品id
			//$productsearchid=implode('<br>',$_REQUEST['productid']);
			$productid =  $_REQUEST['productid']; //复选框选中的产品id
			$productName=$this->db->pquery('SELECT productname FROM vtiger_products WHERE vtiger_products.productid in ('.implode(',',$productid).')',array());
			$rowsName=$this->db->num_rows($productName);
			for ($j=0; $j<$rowsName; $j++) {
				$NewproductName = $this->db->fetchByAssoc($productName);
				$productname[] = $NewproductName['productname'];
			}
			$productsearchid=implode('<br>',$productname);
			if(empty($_REQUEST['record'])){ //新增
				$this->db->pquery('update vtiger_servicecontracts set productsearchid=? where servicecontractsid=?',array($productsearchid,$this->id));
			}else{//编辑
				$this->db->pquery('update vtiger_servicecontracts set productsearchid=? where servicecontractsid=?',array($productsearchid,$_REQUEST['record']));
			}


			$record=$_REQUEST['record'];
			if(!empty($record)){
				$_REQUEST['currentid']=$record;
				//添加工单产品表的状态
				$this->db->pquery('delete from vtiger_salesorderproductsrel where servicecontractsid=? AND(multistatus=? OR multistatus = ?)',array($record,1,0));
			}

			if(!empty($productids) && !in_array($_REQUEST['contract_type'],$configcontracttypeNameTYUN)) {
				$checkarray=array();
				foreach($productids as $value){
					$s=explode('DZE',$value);
					$products = $this->db->pquery('SELECT vtiger_products.productname, vtiger_products.productcategory, vtiger_products.realprice, vtiger_products.unit_price, vtiger_crmentity.smownerid,vtiger_products.extracost, vtiger_products.productid FROM vtiger_products INNER JOIN vtiger_crmentity ON vtiger_products.productid = vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted = 0 AND vtiger_products.productid=?', array($s[1]));
					if($this->db->num_rows($products)){
						$product = $this->db->fetchByAssoc($products);
						$checkarray[]=array('workflowstagesname'=> $product['productname'].'审核','smcreatorid'=>$product['smownerid'],'productid'=>$product['productid']);
						$array = array('salesorderproductsrelid' => $this->db->getUniqueID('vtiger_salesorderproductsrel'),
							'productid' => $product['productid'],
							'producttype' => $product['productcategory'],
							'createtime' => date('Y-m-d H:i:s'),
							'creatorid' => $current_user->id,
							'salesorderproductsrelstatus' => $status,
							'ownerid' => $current_user->id,
							'servicecontractsid' => $_REQUEST['currentid'],
							'accountid' => $_REQUEST['sc_related_to'],
							'realmarketprice' => $_REQUEST['realmarketprice'][$value],
							'marketprice' => $product['unit_price'], // 20170323 直接用数据库的数据添加 周海
							//'marketprice'=>$_REQUEST['unit_price'][$product['productid']],
							'productcomboid' => $_REQUEST['productcomboid'][$value],
							'productsolution' => $_REQUEST['productsolution'][$value],
							'producttext' => $_REQUEST['producttext'][$value],
							'productnumber' => $_REQUEST['productnumber'][$value],
							'agelife' => $_REQUEST['agelife'][$value],
							'standard' => $_REQUEST['standard'][$value],
							'standardname' => $_REQUEST['standardname'][$value],
							'thepackage' => $_REQUEST['thepackage'][$value],
							'isextra' => $_REQUEST['isextra'][$value],
							'prealprice' => $_REQUEST['prealprice'][$value],
							'punit_price' => $_REQUEST['punit_price'][$value],
							'pmarketprice' => $_REQUEST['pmarketprice'][$value],
							'costing' => $_REQUEST['realprice'][$value],
							'purchasemount' => $_REQUEST['purchasemount'][$value],
							'extracost' => $product['extracost'],
							'multistatus' => '1',
							'vendorid' => $_REQUEST['vendorid'][$value],
							'suppliercontractsid' => $_REQUEST['suppliercontractsid'][$value],
							'productname' => $_REQUEST['productname'][$value],
						);
						$this->db->pquery("insert into vtiger_salesorderproductsrel (" . implode(",", array_keys($array)) . ") values(" . generateQuestionMarks($array) . ")", $array);
					}
				}
				vglobal('checkproducts', $checkarray);
				/*
                $products = $this->db->pquery('SELECT vtiger_products.productname, vtiger_products.productcategory, vtiger_products.realprice, vtiger_products.unit_price, vtiger_crmentity.smownerid, vtiger_products.productid FROM vtiger_products INNER JOIN vtiger_crmentity ON vtiger_products.productid = vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted = 0 AND vtiger_products.productid in (' . implode(',', $newproducts) . ')', array());

            $rows=$this->db->num_rows($products);
            $checkarray=array();
            for ($i=0; $i<$rows; ++$i) {
                    $product = $this->db->fetchByAssoc($products);
                    $checkarray[]=array('workflowstagesname'=> $product['productname'].'审核','smcreatorid'=>$product['smownerid'],'productid'=>$product['productid']);
                    $insertproductid=$product['productid'];
                    $array = array('salesorderproductsrelid' => $this->db->getUniqueID('vtiger_salesorderproductsrel'),
                        'productid' => $product['productid'],
                        'producttype' => $product['productcategory'],
                        'createtime' => date('Y-m-d H:i:s'),
                        'creatorid' => $current_user->id,
                        'salesorderproductsrelstatus' => $status,
                        'ownerid' => $current_user->id,
                        'servicecontractsid' => $_REQUEST['currentid'],
                        'accountid' => $_REQUEST['sc_related_to'],
                        'realmarketprice' => $_REQUEST['realmarketprice'][$product['productid']],
                        'marketprice' => $product['unit_price'], // 20170323 直接用数据库的数据添加 周海
                        //'marketprice'=>$_REQUEST['unit_price'][$product['productid']],
                        'productcomboid' => $_REQUEST['productcomboid'][$product['productid']],
                        'productsolution' => $_REQUEST['productsolution'][$product['productid']],
                        'producttext' => $_REQUEST['producttext'][$product['productid']],
                        'productnumber' => $_REQUEST['productnumber'][$product['productid']],
                        'agelife' => $_REQUEST['agelife'][$product['productid']],
                        'standard' => $_REQUEST['standard'][$product['productid']],
                        'thepackage' => $_REQUEST['thepackage'][$product['productid']],
                        'isextra' => $_REQUEST['isextra'][$product['productid']],
                        'prealprice' => $_REQUEST['prealprice'][$product['productid']],
                        'punit_price' => $_REQUEST['punit_price'][$product['productid']],
                        'pmarketprice' => $_REQUEST['pmarketprice'][$product['productid']],
                        'costing' => $_REQUEST['realprice'][$product['productid']],
                        'purchasemount' => $_REQUEST['purchasemount'][$product['productid']],
                        'multistatus' => '1',
                        'vendorid' => $_REQUEST['vendorid'][$product['productid']],
                        'suppliercontractsid' => $_REQUEST['suppliercontractsid'][$product['productid']]
                    );
                    $this->db->pquery("insert into vtiger_salesorderproductsrel (" . implode(",", array_keys($array)) . ") values(" . generateQuestionMarks($array) . ")", $array);
                    //echo "insert into vtiger_salesorderproductsrel (" . implode(",", array_keys($array)) . ") values(" . implode(',',$array) . ")";
                }
                vglobal('checkproducts', $checkarray);*/
			}elseif(in_array($_REQUEST['contract_type'],$configcontracttypeNameTYUN)){
				$this->tyunWriteSalesorderproductsrel($record);
			}
			//}
			//$flow=$this->db->pquery('select workflowsid from vtiger_workflows where mountmodule=?',array($workflow));
			//$info=$this->db->query_result_rowdata($flow);
			//$_REQUEST['workflowsid']=$info['workflowsid'];
			//productids
		}
		$return_action = $_REQUEST['return_action'];
		$for_module = $_REQUEST['return_module'];
		$for_crmid  = $_REQUEST['return_id'];
		//ServiceContracts_Record_Model::setsalesorderandalert($_REQUEST['modulestatus']);
		if ($return_action && $for_module && $for_crmid) {
			if ($for_module == 'HelpDesk') {
				$on_focus = CRMEntity::getInstance($for_module);
				$on_focus->save_related_module($for_module, $for_crmid, $module, $this->id);
			}
		}
		//ServiceContracts_Record_Model::setSalesorderandAlert($_REQUEST['modulestatus'],array(),$id);
		//2015-11-05 周四 wangbin 回款业绩添加回款分成信息;
		$suoshugongsi = $_REQUEST['suoshugongsi'];
		$suoshuren = $_REQUEST['suoshuren'];
		$bili = $_REQUEST['bili'];
		//$sql = "INSERT INTO `vtiger_servicecontracts_divide` (owncompanys, receivedpaymentownid,scalling, servicecontractid) VALUES (?,?,?,?)";
		$sql = "INSERT INTO `vtiger_servicecontracts_divide` (owncompanys, receivedpaymentownid,scalling, servicecontractid,signdempart) SELECT ?,?,?,?,vtiger_user2department.departmentid FROM vtiger_user2department WHERE vtiger_user2department.userid=?";
		if (!empty($suoshuren)) {
			$this->db->pquery("DELETE FROM `vtiger_servicecontracts_divide` WHERE servicecontractid =?", array($id));
			for ($i = 0; $i < count($suoshuren); ++$i) {
				$this->db->pquery($sql, array($suoshugongsi[$i], $suoshuren[$i], $bili[$i], $id, $suoshuren[$i]));
			}
		}
		//end
		//2016年3月9日检查客户是否来自于市场部；;
		$accountid = $_REQUEST['sc_related_to'];
		$isLead_sql = "SELECT * FROM vtiger_account INNER JOIN vtiger_crmentity ON accountid = crmid WHERE accountid = ? AND deleted =?";
		$forLeads_sql = "SELECT * FROM vtiger_servicecontracts INNER JOIN vtiger_crmentity ON servicecontractsid = crmid WHERE  deleted = ?  AND sc_related_to = ? AND modulestatus='c_complete' AND servicecontractsid!=?";
		$isLeaddata = $this->db->pquery($isLead_sql,array($accountid,'0'));//查询是否是商机客户;


		if ($this->db->num_rows($isLeaddata) > 0 && $accountid) {
			$res = $this->db->query_result_rowdata($isLeaddata);
			$isfrommarket = $res['frommarketing'];
			if ($isfrommarket == 1) {
				$this->db->pquery('UPDATE vtiger_servicecontracts SET firstfrommarket = 1 WHERE servicecontractsid = ?', array($id));
				$this->db->pquery('UPDATE vtiger_leaddetails SET assignerstatus = ?,completetime = ? WHERE accountid = ?', array('c_complete', date('Y-m-d'), $accountid));
			}
			$Leadcontract = $this->db->pquery($forLeads_sql, array('0', $accountid, $id)); //查询是否是第一笔合同；
			$contract_num = $this->db->num_rows($Leadcontract);
			if ($contract_num == 1) {
				$this->db->pquery('UPDATE vtiger_servicecontracts SET firstcontract = 1 WHERE servicecontractsid = ?', array($id));
			}
		}


		// 2016-10-28 周海 添加服务合同时， 程序化广告事业部 自动关闭的开关默认为否
		global $current_user;
		if (empty($_REQUEST['record'])) {
			$sql = "select * FROM vtiger_custompowers where custompowerstype='addContraactsCloseStatesRole' LIMIT 1";
			$sel_result = $this->db->pquery($sql, array());
			$res_cnt = $this->db->num_rows($sel_result);
			if ($res_cnt > 0) {
				$row = $this->db->query_result_rowdata($sel_result, 0);

				$roles_arr = array();
				$user_arr = array();
				$department_arr = array();
				if (!empty($row['roles'])) {
					$roles_arr = explode(',', $row['roles']);
				}
				if (!empty($row['user'])) {
					$user_arr = explode(',', $row['user']);
				}
				if (!empty($row['department'])){
					$department_arr = explode(',', $row['department']);
				}
				if (in_array($current_user->current_user_roles, $roles_arr) || in_array($current_user->id, $user_arr) || in_array($current_user->departmentid, $department_arr)) {
					$sql = "update vtiger_servicecontracts set isautoclose='0' where servicecontractsid=? ";
					$this->db->pquery($sql, array($this->id));
				}
			}
		}
		//将合同主体与合同主体编码绑定
		$this->db->pquery("UPDATE vtiger_servicecontracts,vtiger_invoicecompany SET vtiger_servicecontracts.companycode = vtiger_invoicecompany.companycode WHERE vtiger_invoicecompany.invoicecompany = vtiger_servicecontracts.invoicecompany AND vtiger_servicecontracts.servicecontractsid =?",array($this->id));
		//$this->addServicecontractsListUser();
		if($_POST['signaturetype']=='eleccontract' && $_POST['contractattribute']=='standard'){
			$this->createServiceContractsNo($this->column_fields);
		}
	}

	/**
	 * Return query to use based on given modulename, fieldname
	 * Useful to handle specific case handling for Popup
	 */
	function getQueryByModuleField($module, $fieldname, $srcrecord) {
		// $srcrecord could be empty
	}

	/**
	 * Get list view query.
	 */
	function getListQuery($module, $where='') {
		$query = "SELECT vtiger_crmentity.*, $this->table_name.*";

		// Select Custom Field Table Columns if present
		if(!empty($this->customFieldTable)) $query .= ", " . $this->customFieldTable[0] . ".* ";

		$query .= " FROM $this->table_name";

		$query .= "	INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = $this->table_name.$this->table_index";

		// Consider custom table join as well.
		if(!empty($this->customFieldTable)) {
			$query .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
				" = $this->table_name.$this->table_index";
		}
		$query .= " LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid";
		$query .= " LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";

		$linkedModulesQuery = $this->db->pquery("SELECT distinct fieldname, columnname, relmodule FROM vtiger_field" .
			" INNER JOIN vtiger_fieldmodulerel ON vtiger_fieldmodulerel.fieldid = vtiger_field.fieldid" .
			" WHERE uitype='10' AND vtiger_fieldmodulerel.module=?", array($module));
		$linkedFieldsCount = $this->db->num_rows($linkedModulesQuery);

		for($i=0; $i<$linkedFieldsCount; $i++) {
			$related_module = $this->db->query_result($linkedModulesQuery, $i, 'relmodule');
			$fieldname = $this->db->query_result($linkedModulesQuery, $i, 'fieldname');
			$columnname = $this->db->query_result($linkedModulesQuery, $i, 'columnname');

			$other = CRMEntity::getInstance($related_module);
			vtlib_setup_modulevars($related_module, $other);

			$query .= " LEFT JOIN $other->table_name ON $other->table_name.$other->table_index =".
				"$this->table_name.$columnname";
		}

		global $current_user;
		$query .= $this->getNonAdminAccessControlQuery($module,$current_user);
		$query .= "WHERE vtiger_crmentity.deleted = 0 ".$where;
		return $query;
	}

	/**
	 * Apply security restriction (sharing privilege) query part for List view.
	 */
	function getListViewSecurityParameter($module) {
		global $current_user;
		require('user_privileges/user_privileges_'.$current_user->id.'.php');
		require('user_privileges/sharing_privileges_'.$current_user->id.'.php');

		$sec_query = '';
		$tabid = getTabid($module);

		if($is_admin==false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1
			&& $defaultOrgSharingPermission[$tabid] == 3) {

			$sec_query .= " AND (vtiger_crmentity.smownerid in($current_user->id) OR vtiger_crmentity.smownerid IN
					(
						SELECT vtiger_user2role.userid FROM vtiger_user2role
						INNER JOIN vtiger_users ON vtiger_users.id=vtiger_user2role.userid
						INNER JOIN vtiger_role ON vtiger_role.roleid=vtiger_user2role.roleid
						WHERE vtiger_role.parentrole LIKE '".$current_user_parent_role_seq."::%'
					)
					OR vtiger_crmentity.smownerid IN
					(
						SELECT shareduserid FROM vtiger_tmp_read_user_sharing_per
						WHERE userid=".$current_user->id." AND tabid=".$tabid."
					)
					OR
						(";

			// Build the query based on the group association of current user.
			if(sizeof($current_user_groups) > 0) {
				$sec_query .= " vtiger_groups.groupid IN (". implode(",", $current_user_groups) .") OR ";
			}
			$sec_query .= " vtiger_groups.groupid IN
						(
							SELECT vtiger_tmp_read_group_sharing_per.sharedgroupid
							FROM vtiger_tmp_read_group_sharing_per
							WHERE userid=".$current_user->id." and tabid=".$tabid."
						)";
			$sec_query .= ")
				)";
		}
		return $sec_query;
	}

	/**
	 * Create query to export the records.
	 */
	function create_export_query($where)
	{
		global $current_user,$currentModule;

		include("include/utils/ExportUtils.php");

		//To get the Permitted fields query and the permitted fields list
		$sql = getPermittedFieldsQuery('ServiceContracts', "detail_view");

		$fields_list = getFieldsListFromQuery($sql);

		$query = "SELECT $fields_list, vtiger_users.user_name AS user_name
					FROM vtiger_crmentity INNER JOIN $this->table_name ON vtiger_crmentity.crmid=$this->table_name.$this->table_index";

		if(!empty($this->customFieldTable)) {
			$query .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
				" = $this->table_name.$this->table_index";
		}

		$query .= " LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";
		$query .= " LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id and ".
			"vtiger_users.status='Active'";

		$linkedModulesQuery = $this->db->pquery("SELECT distinct fieldname, columnname, relmodule FROM vtiger_field" .
			" INNER JOIN vtiger_fieldmodulerel ON vtiger_fieldmodulerel.fieldid = vtiger_field.fieldid" .
			" WHERE uitype='10' AND vtiger_fieldmodulerel.module=?", array($thismodule));
		$linkedFieldsCount = $this->db->num_rows($linkedModulesQuery);

		for($i=0; $i<$linkedFieldsCount; $i++) {
			$related_module = $this->db->query_result($linkedModulesQuery, $i, 'relmodule');
			$fieldname = $this->db->query_result($linkedModulesQuery, $i, 'fieldname');
			$columnname = $this->db->query_result($linkedModulesQuery, $i, 'columnname');

			$other = CRMEntity::getInstance($related_module);
			vtlib_setup_modulevars($related_module, $other);

			$query .= " LEFT JOIN $other->table_name ON $other->table_name.$other->table_index = ".
				"$this->table_name.$columnname";
		}

		$query .= $this->getNonAdminAccessControlQuery($thismodule,$current_user);
		$where_auto = " vtiger_crmentity.deleted=0";

		if($where != '') $query .= " WHERE ($where) AND $where_auto";
		else $query .= " WHERE $where_auto";

		return $query;
	}

	/**
	 * Function which will give the basic query to find duplicates
	 */
	function getDuplicatesQuery($module,$table_cols,$field_values,$ui_type_arr,$select_cols='') {
		$select_clause = "SELECT ". $this->table_name .".".$this->table_index ." AS recordid, vtiger_users_last_import.deleted,".$table_cols;

		// Select Custom Field Table Columns if present
		$query='';
		if(isset($this->customFieldTable))  $query .= ", " . $this->customFieldTable[0] . ".* ";

		$from_clause = " FROM $this->table_name";

		$from_clause .= "	INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = $this->table_name.$this->table_index";

		// Consider custom table join as well.
		if(isset($this->customFieldTable)) {
			$from_clause .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
				" = $this->table_name.$this->table_index";
		}
		$from_clause .= " LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
				LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";

		$where_clause = "	WHERE vtiger_crmentity.deleted = 0";
		$where_clause .= $this->getListViewSecurityParameter($module);

		if (isset($select_cols) && trim($select_cols) != '') {
			$sub_query = "SELECT $select_cols FROM  $this->table_name AS t " .
				" INNER JOIN vtiger_crmentity AS crm ON crm.crmid = t.".$this->table_index;
			// Consider custom table join as well.
			if(isset($this->customFieldTable)) {
				$sub_query .= " INNER JOIN ".$this->customFieldTable[0]." tcf ON tcf.".$this->customFieldTable[1]." = t.$this->table_index";
			}
			$sub_query .= " WHERE crm.deleted=0 GROUP BY $select_cols HAVING COUNT(*)>1";
		} else {
			$sub_query = "SELECT $table_cols $from_clause $where_clause GROUP BY $table_cols HAVING COUNT(*)>1";
		}

		$query = $select_clause . $from_clause .
			" LEFT JOIN vtiger_users_last_import ON vtiger_users_last_import.bean_id=" . $this->table_name .".".$this->table_index .
			" INNER JOIN (" . $sub_query . ") AS temp ON ".get_on_clause($field_values,$ui_type_arr,$module) .
			$where_clause .
			" ORDER BY $table_cols,". $this->table_name .".".$this->table_index ." ASC";

		return $query;
	}

	/**
	 * Invoked when special actions are performed on the module.
	 * @param String Module name
	 * @param String Event Type
	 */
	function vtlib_handler($moduleName, $eventType) {

		require_once('include/utils/utils.php');
		global $adb;

		if($eventType == 'module.postinstall') {
			require_once('vtlib/Vtiger/Module.php');

			$moduleInstance = Vtiger_Module::getInstance($moduleName);

			$accModuleInstance = Vtiger_Module::getInstance('Accounts');
			$accModuleInstance->setRelatedList($moduleInstance,'Service Contracts',array('add'),'get_dependents_list');

			$conModuleInstance = Vtiger_Module::getInstance('Contacts');
			$conModuleInstance->setRelatedList($moduleInstance,'Service Contracts',array('add'),'get_dependents_list');

			$helpDeskInstance = Vtiger_Module::getInstance("HelpDesk");
			$helpDeskInstance->setRelatedList($moduleInstance,"Service Contracts",Array('ADD','SELECT'));

			// Initialize module sequence for the module
			$adb->pquery("INSERT into vtiger_modentity_num values(?,?,?,?,?,?)",array($adb->getUniqueId("vtiger_modentity_num"),$moduleName,'SERCON',1,1,1));

			// Make the picklist value 'Complete' for status as non-editable
			$adb->query("UPDATE vtiger_contract_status SET presence=0 WHERE contract_status='Complete'");

			// Mark the module as Standard module
			$adb->pquery('UPDATE vtiger_tab SET customized=0 WHERE name=?', array($moduleName));

		} else if($eventType == 'module.disabled') {
			$em = new VTEventsManager($adb);
			$em->setHandlerInActive('ServiceContractsHandler');

		} else if($eventType == 'module.enabled') {
			$em = new VTEventsManager($adb);
			$em->setHandlerActive('ServiceContractsHandler');

		} else if($eventType == 'module.preuninstall') {
			// TODO Handle actions when this module is about to be deleted.
		} else if($eventType == 'module.preupdate') {
			// TODO Handle actions before this module is updated.
		} else if($eventType == 'module.postupdate') {
			// TODO Handle actions after this module is updated.
		}
	}

	/**
	 * Handle saving related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	function save_related_module($module, $crmid, $with_module, $with_crmids) {

		if(!is_array($with_crmids)) $with_crmids = Array($with_crmids);
		foreach($with_crmids as $with_crmid) {
			parent::save_related_module($module, $crmid, $with_module, $with_crmid);
			if ($with_module == 'HelpDesk') {
				$this->updateHelpDeskRelatedTo($crmid,$with_crmid);
				$this->updateServiceContractState($crmid);
			}
		}
	}

	// Function to Update the parent_id of HelpDesk with sc_related_to of ServiceContracts if the parent_id is not set.
	function updateHelpDeskRelatedTo($focusId, $entityIds) {

		if(!is_array($entityIds)) $entityIds = array($entityIds);
		$selectTicketsQuery = "SELECT ticketid FROM vtiger_troubletickets
								WHERE (parent_id IS NULL OR parent_id = 0 OR contact_id IS NULL OR contact_id =0)
									AND ticketid IN (" . generateQuestionMarks($entityIds) .")";$selectTicketsResult = $this->db->pquery($selectTicketsQuery, array($entityIds));
		$noOfTickets = $this->db->num_rows($selectTicketsResult);
		for($i=0; $i < $noOfTickets; ++$i) {
			$ticketId = $this->db->query_result($selectTicketsResult,$i,'ticketid');
			$serviceContractsRelateToTypeResult = $this->db->pquery('SELECT setype FROM vtiger_crmentity WHERE crmid =
				(SELECT sc_related_to FROM vtiger_servicecontracts WHERE servicecontractsid = ?)', array($focusId));
			$serviceContractsRelateToType = $this->db->query_result($serviceContractsRelateToTypeResult, 0, 'setype');
			if($serviceContractsRelateToType == 'Accounts') {
				$updateQuery = "UPDATE vtiger_troubletickets, vtiger_servicecontracts SET parent_id=vtiger_servicecontracts.sc_related_to" .
					" WHERE vtiger_servicecontracts.sc_related_to IS NOT NULL AND vtiger_servicecontracts.sc_related_to != 0" .
					" AND vtiger_servicecontracts.servicecontractsid = ? AND vtiger_troubletickets.ticketid = ?";
				$this->db->pquery($updateQuery, array($focusId, $ticketId));
			} elseif($serviceContractsRelateToType == 'Contacts') {
				$updateQuery = "UPDATE vtiger_troubletickets, vtiger_servicecontracts SET contact_id=vtiger_servicecontracts.sc_related_to" .
					" WHERE vtiger_servicecontracts.sc_related_to IS NOT NULL AND vtiger_servicecontracts.sc_related_to != 0" .
					" AND vtiger_servicecontracts.servicecontractsid = ? AND vtiger_troubletickets.ticketid = ?";
				$this->db->pquery($updateQuery, array($focusId, $ticketId));
			}
		}
	}

	// Function to Compute and Update the Used Units and Progress of the Service Contract based on all the related Trouble tickets.
	function updateServiceContractState($focusId) {
		$this->id = $focusId;
		$this->retrieve_entity_info($focusId,'ServiceContracts');

		$contractTicketsResult = $this->db->pquery("SELECT relcrmid FROM vtiger_crmentityrel
														WHERE module = 'ServiceContracts'
														AND relmodule = 'HelpDesk' AND crmid = ?
													UNION
														SELECT crmid FROM vtiger_crmentityrel
														WHERE relmodule = 'ServiceContracts'
														AND module = 'HelpDesk' AND relcrmid = ?",
			array($focusId,$focusId));

		$noOfTickets = $this->db->num_rows($contractTicketsResult);
		$ticketFocus = CRMEntity::getInstance('HelpDesk');
		$totalUsedUnits = 0;
		for($i=0; $i < $noOfTickets; ++$i) {
			$ticketId = $this->db->query_result($contractTicketsResult, $i, 'relcrmid');
			$ticketFocus->id = $ticketId;
			if(isRecordExists($ticketId)) {
				$ticketFocus->retrieve_entity_info($ticketId, 'HelpDesk');
				if (strtolower($ticketFocus->column_fields['ticketstatus']) == 'closed') {
					$totalUsedUnits += $this->computeUsedUnits($ticketFocus->column_fields);
				}
			}
		}
		$this->updateUsedUnits($totalUsedUnits);

		$this->calculateProgress();
	}

	// Function to Upate the Used Units of the Service Contract based on the given Ticket id.
	function computeUsedUnits($ticketData, $operator='+') {
		$trackingUnit = strtolower($this->column_fields['tracking_unit']);
		$workingHoursPerDay = 24;

		$usedUnits = 0;
		if ($trackingUnit == 'incidents') {
			$usedUnits = 1;
		} elseif ($trackingUnit == 'days') {
			if(!empty($ticketData['days'])) {
				$usedUnits = $ticketData['days'];
			} elseif(!empty($ticketData['hours'])) {
				$usedUnits = $ticketData['hours'] / $workingHoursPerDay;
			}
		} elseif ($trackingUnit == 'hours') {
			if(!empty($ticketData['hours'])) {
				$usedUnits = $ticketData['hours'];
			} elseif(!empty($ticketData['days'])) {
				$usedUnits = $ticketData['days'] * $workingHoursPerDay;
			}
		}
		return $usedUnits;
	}

	// Function to Upate the Used Units of the Service Contract.
	function updateUsedUnits($usedUnits) {
		$this->column_fields['used_units'] = $usedUnits;
		$updateQuery = "UPDATE vtiger_servicecontracts SET used_units = $usedUnits WHERE servicecontractsid = ?";
		$this->db->pquery($updateQuery, array($this->id));
	}

	// Function to Calculate the End Date, Planned Duration, Actual Duration and Progress of a Service Contract
	function calculateProgress() {
		$updateCols = array();
		$updateParams = array();

		$startDate = $this->column_fields['start_date'];
		$dueDate = $this->column_fields['due_date'];
		$endDate = $this->column_fields['end_date'];

		$usedUnits = decimalFormat($this->column_fields['used_units']);
		$totalUnits = decimalFormat($this->column_fields['total_units']);

		$contractStatus = $this->column_fields['contract_status'];

		// Update the End date if the status is Complete or if the Used Units reaches/exceeds Total Units
		// We need to do this first to make sure Actual duration is computed properly
		if($contractStatus == 'Complete' || (!empty($usedUnits) && !empty($totalUnits) && $usedUnits >= $totalUnits)) {
			if(empty($endDate)) {
				$endDate = date('Y-m-d');
				$this->db->pquery('UPDATE vtiger_servicecontracts SET end_date=? WHERE servicecontractsid = ?', array(date('Y-m-d'), $this->id));
			}
		} else {
			$endDate = null;
			$this->db->pquery('UPDATE vtiger_servicecontracts SET end_date=? WHERE servicecontractsid = ?', array(null, $this->id));
		}

		// Calculate the Planned Duration based on Due date and Start date. (in days)
		if(!empty($dueDate) && !empty($startDate)) {
			$plannedDurationUpdate = " planned_duration = (TO_DAYS(due_date)-TO_DAYS(start_date)+1)";
		} else {
			$plannedDurationUpdate = " planned_duration = ''";
		}
		array_push($updateCols, $plannedDurationUpdate);

		// Calculate the Actual Duration based on End date and Start date. (in days)
		if(!empty($endDate) && !empty($startDate)) {
			$actualDurationUpdate = "actual_duration = (TO_DAYS(end_date)-TO_DAYS(start_date)+1)";
		} else {
			$actualDurationUpdate = "actual_duration = ''";
		}
		array_push($updateCols, $actualDurationUpdate);

		// Update the Progress based on Used Units and Total Units (in percentage)
		if(!empty($usedUnits) && !empty($totalUnits)) {
			$progressUpdate = 'progress = ?';
			$progressUpdateParams = floatval(($usedUnits * 100) / $totalUnits);
		} else {
			$progressUpdate = 'progress = ?';
			$progressUpdateParams = null;
		}
		array_push($updateCols, $progressUpdate);
		array_push($updateParams, $progressUpdateParams);

		if(count($updateCols) > 0) {
			$updateQuery = 'UPDATE vtiger_servicecontracts SET '. implode(",", $updateCols) .' WHERE servicecontractsid = ?';
			array_push($updateParams, $this->id);
			$this->db->pquery($updateQuery, $updateParams);
		}
	}

	/**
	 * Handle deleting related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	function delete_related_module($module, $crmid, $with_module, $with_crmid) {
		parent::delete_related_module($module, $crmid, $with_module, $with_crmid);
		if ($with_module == 'HelpDesk') {
			$this->updateServiceContractState($crmid);
		}
	}

	/**
	 * Handle getting related list information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//function get_related_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }

	/** Function to unlink an entity with given Id from another entity */
	function unlinkRelationship($id, $return_module, $return_id) {
		global $log, $currentModule;

		if($return_module == 'Accounts') {
			$focus = new $return_module;
			$entityIds = $focus->getRelatedContactsIds($return_id);
			array_push($entityIds, $return_id);
			$entityIds = implode(',', $entityIds);
			$return_modules = "'Accounts','Contacts'";
		} else {
			$entityIds = $return_id;
			$return_modules = "'".$return_module."'";
		}

		$query = 'DELETE FROM vtiger_crmentityrel WHERE (relcrmid='.$id.' AND module IN ('.$return_modules.') AND crmid IN ('.$entityIds.')) OR (crmid='.$id.' AND relmodule IN ('.$return_modules.') AND relcrmid IN ('.$entityIds.'))';
		$this->db->pquery($query, array());

		$sql = 'SELECT tabid, tablename, columnname FROM vtiger_field WHERE fieldid IN (SELECT fieldid FROM vtiger_fieldmodulerel WHERE module=? AND relmodule IN ('.$return_modules.'))';
		$fieldRes = $this->db->pquery($sql, array($currentModule));
		$numOfFields = $this->db->num_rows($fieldRes);
		for ($i = 0; $i < $numOfFields; $i++) {
			$tabId = $this->db->query_result($fieldRes, $i, 'tabid');
			$tableName = $this->db->query_result($fieldRes, $i, 'tablename');
			$columnName = $this->db->query_result($fieldRes, $i, 'columnname');
			$relatedModule = vtlib_getModuleNameById($tabId);
			$focusObj = CRMEntity::getInstance($relatedModule);

			$updateQuery = "UPDATE $tableName SET $columnName=? WHERE $columnName IN ($entityIds) AND $focusObj->table_index=?";
			$updateParams = array(null, $id);
			$this->db->pquery($updateQuery, $updateParams);
		}
	}

	/**
	 * Move the related records of the specified list of id's to the given record.
	 * @param String This module name
	 * @param Array List of Entity Id's from which related records need to be transfered
	 * @param Integer Id of the the Record to which the related records are to be moved
	 */
	function transferRelatedRecords($module, $transferEntityIds, $entityId) {
		global $adb,$log;
		$log->debug("Entering function transferRelatedRecords ($module, $transferEntityIds, $entityId)");

		$rel_table_arr = Array("Documents"=>"vtiger_senotesrel","Attachments"=>"vtiger_seattachmentsrel");

		$tbl_field_arr = Array("vtiger_senotesrel"=>"notesid","vtiger_seattachmentsrel"=>"attachmentsid");

		$entity_tbl_field_arr = Array("vtiger_senotesrel"=>"crmid","vtiger_seattachmentsrel"=>"crmid");

		foreach($transferEntityIds as $transferId) {
			foreach($rel_table_arr as $rel_module=>$rel_table) {
				$id_field = $tbl_field_arr[$rel_table];
				$entity_id_field = $entity_tbl_field_arr[$rel_table];
				// IN clause to avoid duplicate entries
				$sel_result =  $adb->pquery("select $id_field from $rel_table where $entity_id_field=? " .
					" and $id_field not in (select $id_field from $rel_table where $entity_id_field=?)",
					array($transferId,$entityId));
				$res_cnt = $adb->num_rows($sel_result);
				if($res_cnt > 0) {
					for($i=0;$i<$res_cnt;$i++) {
						$id_field_value = $adb->query_result($sel_result,$i,$id_field);
						$adb->pquery("update $rel_table set $entity_id_field=? where $entity_id_field=? and $id_field=?",
							array($entityId,$transferId,$id_field_value));
					}
				}
			}
		}
		parent::transferRelatedRecords($module, $transferEntityIds, $entityId);
		$log->debug("Exiting transferRelatedRecords...");
	}


	function get_payments($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $currentModule, $app_strings, $singlepane_view;

		$parenttab = getParentTab();

		$related_module = vtlib_getModuleNameById($rel_tab_id);
		$other = CRMEntity::getInstance($related_module);

		// Some standard module class doesn't have required variables
		// that are used in the query, they are defined in this generic API
		vtlib_setup_modulevars($currentModule, $this);
		vtlib_setup_modulevars($related_module, $other);

		$singular_modname = 'SINGLE_' . $related_module;

		$button = '';
		if ($actions) {
			if (is_string($actions))
				$actions = explode(',', strtoupper($actions));
			if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
				$button .= "<input title='" . getTranslatedString('LBL_SELECT') . " " . getTranslatedString($related_module) . "' class='crmbutton small edit' " .
					" type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\"" .
					" value='" . getTranslatedString('LBL_SELECT') . " " . getTranslatedString($related_module, $related_module) . "'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$button .= "<input type='hidden' name='createmode' id='createmode' value='link' />" .
					"<input title='" . getTranslatedString('LBL_ADD_NEW') . " " . getTranslatedString($singular_modname) . "' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='" . getTranslatedString('LBL_ADD_NEW') . " " . getTranslatedString($singular_modname, $related_module) . "'>&nbsp;";
			}
		}

		// To make the edit or del link actions to return back to same view.
		if ($singlepane_view == 'true')
			$returnset = "&return_module=$currentModule&return_action=DetailView&return_id=$id";
		else
			$returnset = "&return_module=$currentModule&return_action=CallRelatedList&return_id=$id";

		$query = "SELECT $other->table_name.*, $other->table_name.receivedpaymentsid as crmid";

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'vtiger_users.first_name',
			'last_name' => 'vtiger_users.last_name'), 'Users');
		$query .= ", CASE WHEN (vtiger_users.user_name NOT LIKE '') THEN $userNameSql END AS user_name";

		$more_relation = '';
		if (!empty($other->related_tables)) {
			foreach ($other->related_tables as $tname => $relmap) {
				$query .= ", $tname.*";

				// Setup the default JOIN conditions if not specified
				if (empty($relmap[1]))
					$relmap[1] = $other->table_name;
				if (empty($relmap[2]))
					$relmap[2] = $relmap[0];
				$more_relation .= " LEFT JOIN $tname ON $tname.$relmap[0] = $relmap[1].$relmap[2]";
			}
		}

		$query .= " FROM $other->table_name";
		$query .= $more_relation;
		$query .= " LEFT  JOIN vtiger_users ON vtiger_users.id = $other->table_name.createid";
		$query .= " WHERE $other->table_name.relatetoid = $id";
		$return_value = GetRelatedList($currentModule, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null)
			$return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		return $return_value;
	}




	function get_invoice($id, $cur_tab_id, $rel_tab_id, $actions=false){
		// ini_set('display_errors','on');
		//error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
		/* $parenttab = getParentTab();
        $related_module = vtlib_getModuleNameById($rel_tab_id);
        $other = CRMEntity::getInstance($related_module);

        vtlib_setup_modulevars($currentModule, $this);
        vtlib_setup_modulevars($related_module, $other);
        $singular_modname = 'SINGLE_' . $related_module;
        $button = '';
        if ($actions) {
            if (is_string($actions))
                $actions = explode(',', strtoupper($actions));
            if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
                $button .= "<input title='" . getTranslatedString('LBL_SELECT') . " " . getTranslatedString($related_module) . "' class='crmbutton small edit' " .
                    " type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\"" .
                    " value='" . getTranslatedString('LBL_SELECT') . " " . getTranslatedString($related_module, $related_module) . "'>&nbsp;";
            }
            if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
                $button .= "<input type='hidden' name='createmode' id='createmode' value='link' />" .
                    "<input title='" . getTranslatedString('LBL_ADD_NEW') . " " . getTranslatedString($singular_modname) . "' class='crmbutton small create'" .
                    " onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
                    " value='" . getTranslatedString('LBL_ADD_NEW') . " " . getTranslatedString($singular_modname, $related_module) . "'>&nbsp;";
            }
        } */
		global $currentModule, $app_strings, $singlepane_view;
		$query = "SELECT
	 vtiger_invoice.*
        FROM vtiger_invoice
        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_invoice.invoiceid
        LEFT  JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid = vtiger_invoice.contractid
        LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
        LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id
        WHERE
        	vtiger_crmentity.deleted = 0
        AND (
	vtiger_invoice.contractid =".$id.")" ;
		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);
		//echo $query;die;
		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;
		return $return_value;
	}
	function get_quotes($id, $cur_tab_id, $rel_tab_id, $actions=false){
		global $currentModule, $app_strings, $singlepane_view;
		$db = PearDatabase::getInstance();
		$quoteid = $db->pquery('SELECT quotes_no  FROM `vtiger_servicecontracts` WHERE servicecontractsid=?',array($id));
		$query = "SELECT
	                   vtiger_quotes.*
                    FROM
                    	vtiger_quotes
                        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_quotes.quoteid
                        LEFT  JOIN vtiger_servicecontracts ON vtiger_servicecontracts.quotes_no = vtiger_quotes.quoteid
                        LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
                        LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id
                    WHERE
                    	vtiger_crmentity.deleted = 0 AND vtiger_servicecontracts.quotes_no= {$quoteid->fields['0']}";
		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);
		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;
		return $return_value;
	}
	/**节点审核时到了指定节点抓取时间
	 * 后置事件
	 * @param Vtiger_Request $request
	 */
	function workflowcheckafter(Vtiger_Request $request){
		$stagerecordid=$request->get('stagerecordid');
		$record=$request->get('record');

		$query="SELECT
                    vtiger_workflowstages.workflowstagesflag,
                    vtiger_salesorderworkflowstages.workflowsid,
       vtiger_salesorderworkflowstages.sequence
                FROM
                    `vtiger_salesorderworkflowstages`
                LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid
                WHERE
                    vtiger_salesorderworkflowstages.salesorderworkflowstagesid = ?
                AND vtiger_salesorderworkflowstages.modulename = 'ServiceContracts'";
		$result = $this->db->pquery($query, array($stagerecordid));
		$currentflag = $this->db->query_result($result, 0, 'workflowstagesflag');
		$workflowsid=$this->db->query_result($result,0,'workflowsid');
		$sequence=$this->db->query_result($result,0,'sequence');
		$recordModel = Vtiger_Record_Model::getInstanceById($record, 'ServiceContracts', TRUE);
		$entity = $recordModel->entity->column_fields;
		$currentflag = trim($currentflag);
		$datetime = date('Y-m-d H:i:s');
		switch ($currentflag) {
			case 'QUALIFCATION_AUDIT':
				$sql="update vtiger_account set isnewcheck=1 where accountid=?";
				$this->db->pquery($sql,array($entity['sc_related_to']));
				$this->makeContractNo($record);
				break;
			case 'CREATE_CODE':
				//生成合同编号
				if(empty($entity['contract_no'])){
					$query="SELECT productclass,bussinesstype FROM `vtiger_contract_type` WHERE contract_type=?";
					$result=$this->db->pquery($query,array($entity['contract_type']));
					$productclass=$this->db->query_result($result,0,'productclass');
					$bussinesstype=$this->db->query_result($result,0,'bussinesstype');
					$query="SELECT company_code FROM `vtiger_company_code` WHERE companyfullname=? limit 1";
					$result=$this->db->pquery($query,array($entity['invoicecompany']));
					$company_codeno=$this->db->query_result($result,0,'company_code');
					$_POST['sc_related_to']=8;
					$_POST['quantity']=1;
					$_POST['company_code']=!empty($company_codeno)?$company_codeno:'ZD';
					$_POST['products_code']=$productclass;
					$_POST['contract_template']='ZDY';
					//$_POST['signstatus']=2;
					$request=new Vtiger_Request($_POST, $_POST);
					$request->set('module','SContractNoGeneration');
					$request->set('view','Edit');
					$request->set('action','Save');
					$ressorder=new Vtiger_Save_Action();
					$ressorder->saveRecord($request);
					$this->db->pquery("UPDATE vtiger_servicecontracts SET contract_no=(SELECT servicecontracts_no FROM vtiger_servicecontracts_print ORDER BY servicecontractsprintid DESC LIMIT 1),servicecontractsprintid=(SELECT MAX(servicecontractsprintid) FROM vtiger_servicecontracts_print LIMIT 1),servicecontractsprint=(SELECT concat(MAX(servicecontractsprintid),'-8') FROM vtiger_servicecontracts_print LIMIT 1),bussinesstype=? WHERE servicecontractsid=?",array($bussinesstype,$record));
					$this->db->pquery("UPDATE vtiger_crmentity SET label=(SELECT servicecontracts_no FROM vtiger_servicecontracts_print ORDER BY servicecontractsprintid DESC LIMIT 1) WHERE crmid=?",array($record));
					$this->db->pquery("UPDATE vtiger_servicecontracts_print SET nostand=1,smownerid=? WHERE servicecontractsprintid=(SELECT * from (SELECT max(servicecontractsprintid) FROM vtiger_servicecontracts_print LIMIT 1) as m)",array($entity['assigned_user_id']));
					// 如果合同编号生成后进行更新 vtiger_salesorderworkflowstages 合同内容
					$salesorder_nono = $this->db->pquery(" SELECT contract_no FROM vtiger_servicecontracts WHERE servicecontractsid =?  limit  1 ",array($record));
					if ( $this->db->num_rows($salesorder_nono)) {
						while ($rowdata = $this->db->fetch_array($salesorder_nono)) {
							$this->db->pquery('UPDATE `vtiger_salesorderworkflowstages` SET salesorder_nono=? WHERE vtiger_salesorderworkflowstages.salesorderid=?', array($rowdata['contract_no'],$record));
						}
					}
				}
				if($recordModel->get('signaturetype')=='eleccontract'){
					$this->elecContractSend($record);
				}
				break;
			case 'CLOSE_WORKSTREAM':
				//盖章后关闭工作流，且发邮件给领取人
				$this->db->pquery("UPDATE vtiger_servicecontracts SET modulestatus='c_stamp' WHERE servicecontractsid=?",array($record));
				$this->db->pquery("UPDATE vtiger_salesorderworkflowstages SET isaction=0 WHERE  vtiger_salesorderworkflowstages.salesorderid=? AND isaction=1 AND vtiger_salesorderworkflowstages.modulename='ServiceContracts'",array($record));//将工作流冻结
				$this->db->pquery("UPDATE vtiger_servicecontracts_print SET constractsstatus='c_stamp',stamptime=? WHERE servicecontracts_no=?",array($datetime,$entity['contract_no']));
				$user = new Users();
				$current_userT = $user->retrieveCurrentUserInfoFromFile($entity['assigned_user_id']);
				$Subject='合同领取';
				$body='您提交的非标合同已通过审核且打印盖章完成,<br>合同编号:'.$entity['contract_no'].'<br>请到财务部领取';
				$address=array(array('mail'=>$current_userT->column_fields['email1'],'name'=>$current_userT->column_fields['last_name']));
				Vtiger_Record_Model::sendMail($Subject,$body,$address);
				break;
			case 'DO_PRINT':
				global $current_user;

				//合同打印节点
				$this->db->pquery("UPDATE vtiger_servicecontracts_print SET constractsstatus='c_print',printer=?,printtime=? WHERE servicecontracts_no=?",array($current_user->id,$datetime,$entity['contract_no']));
				break;
			case 'AUDIT_VERIFICATION':
				//第二个节点指定审核人
				$query="SELECT higherid,sequence FROM vtiger_salesorderworkflowstages WHERE  modulename='ServiceContracts' AND sequence in(1,2,3) AND salesorderid=?";
				$resultAuditSettings=$this->db->pquery($query,array($record));
				if($this->db->num_rows($resultAuditSettings)){
					while($row=$this->db->fetch_array($resultAuditSettings)){
						if(1==$row['sequence']){
							$oneaudituid=$row['higherid'];
						}elseif(2==$row['sequence']){
							$towaudituid=$row['higherid'];
						}elseif(3==$row['sequence']){
							$audituid3=$row['higherid'];
						}
					}
					global $current_user;
					if($oneaudituid!=$current_user->id){
						break;
					}

					if($oneaudituid==$towaudituid && $towaudituid==$audituid3)
					{//当前第一审核人与第二审核人是同一人，则第二个节点关闭直接跳到第三个工作流
						$isaction = 'isaction=2';
						$isactionthree = 'isaction=2';
						$tempResult=$this->db->pquery("SELECT sequence FROM vtiger_salesorderworkflowstages WHERE salesorderid=? AND modulename='ServiceContracts' AND sequence>3 AND isaction=0 ORDER BY sequence ASC LIMIT 1",array($record));
						$sequence=$tempResult->fields['sequence'];
						$this->db->pquery("UPDATE vtiger_salesorderworkflowstages SET isaction=1 WHERE vtiger_salesorderworkflowstages.salesorderid=? AND sequence=? AND vtiger_salesorderworkflowstages.modulename='ServiceContracts'",array($record,$sequence));//第三个节点激活
					}
					elseif($oneaudituid==$towaudituid && $towaudituid!==$audituid3)
					{
						$isaction = 'isaction=2';
						$isactionthree = 'isaction=1';
					}
					else
					{
						$isaction = 'isaction=1';
						$isactionthree = 'isaction=0';
					}
					$sql="UPDATE vtiger_salesorderworkflowstages SET {$isaction} WHERE vtiger_salesorderworkflowstages.salesorderid=? AND sequence=2 AND vtiger_salesorderworkflowstages.modulename='ServiceContracts'";
					$this->db->pquery($sql,array($record));//第二个节点
					//$this->db->pquery("UPDATE vtiger_salesorderworkflowstages SET ishigher=1,higherid=16 WHERE vtiger_salesorderworkflowstages.salesorderid=? AND sequence=4 AND vtiger_salesorderworkflowstages.modulename='ServiceContracts'",array($record));//财务主管节点
					$sql="UPDATE vtiger_salesorderworkflowstages SET {$isactionthree} WHERE vtiger_salesorderworkflowstages.salesorderid=? AND sequence=3 AND vtiger_salesorderworkflowstages.modulename='ServiceContracts'";
					$this->db->pquery($sql,array($record));//第三个节点
					break;
				}else{
					break;
				}

			/*$user = new Users();
            $current_userT = $user->retrieveCurrentUserInfoFromFile($entity['assigned_user_id']);
            $query="SELECT vtiger_auditsettings.oneaudituid,vtiger_auditsettings.towaudituid,vtiger_auditsettings.audituid3 FROM`vtiger_auditsettings` INNER JOIN (SELECT vtiger_departments.parentdepartment,vtiger_departments.departmentid FROM vtiger_departments WHERE vtiger_departments.departmentid='{$current_userT->departmentid}') AS tempdepart ON FIND_IN_SET(vtiger_auditsettings.department,REPLACE(tempdepart.parentdepartment,'::',',')) LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_auditsettings.department  WHERE vtiger_auditsettings.auditsettingtype='ContractsAuditset' ORDER BY abs(LENGTH(IFNULL(tempdepart.parentdepartment,0))-LENGTH(IFNULL(vtiger_departments.parentdepartment,0))) LIMIT 1";
            $resultAuditSettings=$this->db->pquery($query,array());
            $oneaudituid=$this->db->query_result($resultAuditSettings,0,'oneaudituid');
            $towaudituid=$this->db->query_result($resultAuditSettings,0,'towaudituid');
            $audituid3=$this->db->query_result($resultAuditSettings,0,'audituid3');//第三个节点审核人
            if($oneaudituid==$towaudituid && $towaudituid==$audituid3)
            {//当前第一审核人与第二审核人是同一人，则第二个节点关闭直接跳到第三个工作流
                $isaction = 'isaction=2';
                $isactionthree = 'isaction=2';
                $this->db->pquery("UPDATE vtiger_salesorderworkflowstages SET isaction=1 WHERE vtiger_salesorderworkflowstages.salesorderid=? AND sequence=4 AND vtiger_salesorderworkflowstages.modulename='ServiceContracts'",array($record));//第三个节点激活
            }
            elseif($oneaudituid==$towaudituid && $towaudituid!==$audituid3)
            {
                $isaction = 'isaction=2';
                $isactionthree = 'isaction=1';
            }
            else
            {
                $isaction = 'isaction=1';
                $isactionthree = 'isaction=0';
            }
            $sql="UPDATE vtiger_salesorderworkflowstages SET {$isaction} WHERE vtiger_salesorderworkflowstages.salesorderid=? AND sequence=2 AND vtiger_salesorderworkflowstages.modulename='ServiceContracts'";
            $this->db->pquery($sql,array($record));//第二个节点
            //$this->db->pquery("UPDATE vtiger_salesorderworkflowstages SET ishigher=1,higherid=16 WHERE vtiger_salesorderworkflowstages.salesorderid=? AND sequence=4 AND vtiger_salesorderworkflowstages.modulename='ServiceContracts'",array($record));//财务主管节点
            $sql="UPDATE vtiger_salesorderworkflowstages SET {$isactionthree} WHERE vtiger_salesorderworkflowstages.salesorderid=? AND sequence=3 AND vtiger_salesorderworkflowstages.modulename='ServiceContracts'";
            $this->db->pquery($sql,array($record));//第三个节点
            break;*/
			case 'TWO_VERIFICATION':
				$query="SELECT higherid,sequence FROM vtiger_salesorderworkflowstages WHERE  modulename='ServiceContracts' AND sequence in(2,3) AND salesorderid=?";
				$resultAuditSettings=$this->db->pquery($query,array($record));
				if($this->db->num_rows($resultAuditSettings)){
					while($row=$this->db->fetch_array($resultAuditSettings)){
						if(2==$row['sequence']){
							$towaudituid=$row['higherid'];
						}elseif(3==$row['sequence']){
							$audituid3=$row['higherid'];
						}
					}
					global $current_user;
					if($towaudituid!=$current_user->id){
						break;
					}

					if($audituid3==$towaudituid)
					{//当前第一审核人与第二审核人是同一人，则第二个节点关闭直接跳到第三个工作流
						$isaction='isaction=2';
						$tempResult=$this->db->pquery("SELECT sequence FROM vtiger_salesorderworkflowstages WHERE salesorderid=? AND modulename='ServiceContracts' AND isaction=0 ORDER BY sequence ASC LIMIT 1",array($record));
						$sequence=$tempResult->fields['sequence'];
						$this->db->pquery("UPDATE vtiger_salesorderworkflowstages SET isaction=1 WHERE vtiger_salesorderworkflowstages.salesorderid=? AND sequence=? AND vtiger_salesorderworkflowstages.modulename='ServiceContracts'",array($record,$sequence));//第三个节点激活
					}
					else
					{
						$isaction='isaction=1';
					}
					$sql="UPDATE vtiger_salesorderworkflowstages SET {$isaction} WHERE vtiger_salesorderworkflowstages.salesorderid=? AND sequence=3 AND vtiger_salesorderworkflowstages.modulename='ServiceContracts'";
					$this->db->pquery($sql,array($record));//第二个节点
					break;
				}else{
					break;
				}
			/*//第二个节点审核人
            $user = new Users();
            $current_user = $user->retrieveCurrentUserInfoFromFile($entity['assigned_user_id']);
            $query="SELECT vtiger_auditsettings.oneaudituid,vtiger_auditsettings.towaudituid,vtiger_auditsettings.audituid3 FROM`vtiger_auditsettings` LEFT JOIN (SELECT vtiger_departments.parentdepartment,vtiger_departments.departmentid FROM vtiger_departments WHERE vtiger_departments.departmentid='{$current_user->departmentid}') AS tempdepart ON FIND_IN_SET(vtiger_auditsettings.department,REPLACE(tempdepart.parentdepartment,'::',',')) LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_auditsettings.department  WHERE vtiger_auditsettings.auditsettingtype='ContractsAuditset' ORDER BY abs(LENGTH(IFNULL(tempdepart.parentdepartment,0))-LENGTH(IFNULL(vtiger_departments.parentdepartment,0))) LIMIT 1";
            $resultAuditSettings=$this->db->pquery($query,array());
            $oneaudituid=$this->db->query_result($resultAuditSettings,0,'oneaudituid');
            $towaudituid=$this->db->query_result($resultAuditSettings,0,'towaudituid');
            $audituid3=$this->db->query_result($resultAuditSettings,0,'audituid3');//第三个节点审核人
            if($audituid3==$towaudituid)
            {//当前第一审核人与第二审核人是同一人，则第二个节点关闭直接跳到第三个工作流
                $isaction='isaction=2';
                $this->db->pquery("UPDATE vtiger_salesorderworkflowstages SET isaction=1 WHERE vtiger_salesorderworkflowstages.salesorderid=? AND sequence=4 AND vtiger_salesorderworkflowstages.modulename='ServiceContracts'",array($record));//第三个节点激活
            }
            else
            {
                $isaction='isaction=1';
            }
            $sql="UPDATE vtiger_salesorderworkflowstages SET {$isaction} WHERE vtiger_salesorderworkflowstages.salesorderid=? AND sequence=3 AND vtiger_salesorderworkflowstages.modulename='ServiceContracts'";
            $this->db->pquery($sql,array($record));//第二个节点
            break;*/
			case 'DO_CANCEL':
				//作废关闭工作流
				$datetime=date('Y-m-d H:i:s');
				$this->db->pquery("UPDATE vtiger_salesorderworkflowstages SET isaction=0 WHERE vtiger_salesorderworkflowstages.salesorderid=? AND isaction=1 AND vtiger_salesorderworkflowstages.modulename='ServiceContracts'",array($record));//关闭工单节点
				$this->db->pquery("UPDATE vtiger_servicecontracts SET modulestatus='c_cancel' WHERE servicecontractsid=?",array($record));
				$this->db->pquery("UPDATE vtiger_servicecontracts_print SET constractsstatus='c_cancel',canceltime=? WHERE servicecontracts_no!='' AND servicecontracts_no=?",array($datetime,$entity['contract_no']));
				$this->db->pquery("UPDATE vtiger_activationcode SET `status`=2,contractstatus=2 WHERE contractid=?",array($record));
				$query='SELECT newservicecontractsid,contractsagreementid,newservicecontractsno,servicecontractsprintid FROM vtiger_contractsagreement WHERE servicecontractsid=?';
				$resultsdata=$this->db->pquery($query,array($record));
				while($row=$this->db->fetch_array($resultsdata))
				{
					$this->db->pquery("UPDATE vtiger_salesorderworkflowstages SET isaction=0 WHERE vtiger_salesorderworkflowstages.salesorderid=? AND isaction=1 AND vtiger_salesorderworkflowstages.modulename='ContractsAgreement'",array($row['contractsagreementid']));//关闭工单节点
					$this->db->pquery("UPDATE vtiger_contractsagreement SET modulestatus='c_cancel' WHERE contractsagreementid=?",array($row['contractsagreementid']));
					$this->db->pquery("UPDATE vtiger_salesorderworkflowstages SET isaction=0 WHERE vtiger_salesorderworkflowstages.salesorderid=? AND isaction=1 AND vtiger_salesorderworkflowstages.modulename='ServiceContracts'",array($row['newservicecontractsid']));//关闭工单节点
					$this->db->pquery("UPDATE vtiger_servicecontracts SET modulestatus='c_cancel' WHERE servicecontractsid=?",array($row['newservicecontractsid']));
					$this->db->pquery("UPDATE vtiger_servicecontracts_print SET constractsstatus='c_cancel',canceltime=? WHERE servicecontracts_no!='' AND servicecontracts_no=?",array($datetime,$row['newservicecontractsno']));
				}
				//合同作废处理激活码信息
				//$recordModel->getModule()->doCancel($entity['contract_no']);
				//如果存在合同执行就去进行状态更改
				$contractExecution = $this->db->pquery("select contractexecutionid from vtiger_contracts_execution where contractid=?",array($record));
				if($this->db->num_rows($contractExecution)){
					$this->db->pquery("update vtiger_contracts_execution set status='c_execution_cancel' where contractid=?",array($record));
					$this->db->pquery("update vtiger_contracts_execution_detail set iscancel=1 where contractid=?",array($record));
					$this->db->pquery("update vtiger_contract_receivable set iscancel=1 where contractid=?",array($record));
					$this->db->pquery("UPDATE vtiger_salesorderworkflowstages SET isaction=if(isaction=1,0,isaction) WHERE modulename='ContractExecution' AND salesorderid=?",array($contractExecution->fields['contractexecutionid']));
				}
				break;
			/*case 'DO_RETURN_TCLOUD':
                $this->db->pquery("UPDATE vtiger_servicecontracts SET modulestatus='c_cancelings' WHERE servicecontractsid=?",array($record));
                break;*/
			case 'BACKCHANGESTATUS':
				//分成修改申请
				global $current_user;
				$this->db->pquery("UPDATE vtiger_servicecontracts SET modulestatus='已发放' WHERE servicecontractsid=?",array($record));
				$this->db->pquery("UPDATE vtiger_servicecontracts_print SET constractsstatus='c_receive' WHERE servicecontracts_no!='' AND servicecontracts_no=?",array($entity['contract_no']));
				$id = $this->db->getUniqueId('vtiger_modtracker_basic');
				$currentTime=date('Y-m-d H:i:s');
				$this->db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
					array($id, $record, 'ServiceContracts', $current_user->id, $currentTime, 0));
				$this->db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
					Array($id, 'modulestatus', 'b_actioning', '已发放'));
				break;
			case 'DIVIDEDMODIFICATION':
				global $current_user;
				//合同
				if ($recordModel->get('modulestatus') == 'c_complete') {
					//日志插入
					$id = $this->db->getUniqueId('vtiger_modtracker_basic');
					$currentTime = date('Y-m-d H:i:s');
					$this->db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)', array($id, $record, 'ServiceContracts', $current_user->id, $currentTime, 0));

					$resultsdata = $this->db->pquery("SELECT vsd.* , vu.last_name,vu.department,vu.title FROM vtiger_servicecontracts_divide_tmp AS vsd LEFT  JOIN vtiger_users AS vu ON vsd.receivedpaymentownid = vu.id WHERE vsd.servicecontractid =?", array($record));
					$resultsdata_1 = array();
					$data = array();
					if(!empty($resultsdata)){
						$data = $this->db->run_query_allrecords("SELECT vsd.* , vu.last_name,vu.department,vu.title FROM vtiger_servicecontracts_divide_tmp AS vsd LEFT  JOIN vtiger_users AS vu ON vsd.receivedpaymentownid = vu.id WHERE vsd.servicecontractid =$record");

						$resultsdata_1 = $this->db->pquery("SELECT vsd.* , vu.last_name,vu.department,vu.title FROM vtiger_servicecontracts_divide AS vsd JOIN vtiger_users AS vu ON vsd.receivedpaymentownid = vu.id WHERE vsd.servicecontractid=?", array($record));
					}
					if (!empty($resultsdata_1)) {
						while ($row = $this->db->fetch_array($resultsdata_1)) {
							$this->db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)', Array($id, 'remark', '', '['.$row['department'].']'.$row['last_name'].'删除分成'.$row['scalling'].'%'));
						}
					}
					if (!empty($data)) {
						foreach ($data as $row){
							$this->db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)', Array($id, 'remark', '', '['.$row['department'].']'.$row['last_name'].'添加分成'.$row['scalling'].'%'));
						}
					}
//                     print_r($data);
					if (!empty($data)) {
						$this->db->pquery("DELETE FROM `vtiger_servicecontracts_divide` WHERE servicecontractid =?", array($record));
						$this->db->pquery("DELETE FROM `vtiger_servicecontracts_divide_tmp` WHERE servicecontractid =?", array($record));
						//查询有没有要分成修改的回款分成明细数据
						$result = $this->db->pquery("SELECT  * FROM  vtiger_servicecontracts_update_receivedpayments_divide_tmp WHERE servicecontractsid=?" ,array($record));
						$paymentsRecord=array();
						if($this->db->num_rows($result)>0){
							//如果存在 则先获取有分成明细的回款
							$result=$this->db->query_result_rowdata($result,0);
							$result=$this->db->pquery("SELECT *,SUM(businessunit) as unit_price FROM  vtiger_achievementallot WHERE receivedpaymentsid IN(".$result['receivedpaymentsids'].")  AND  NOT EXISTS( SELECT 1 FROM vtiger_achievementallot_statistic WHERE vtiger_achievementallot.receivedpaymentsid=vtiger_achievementallot_statistic.receivedpaymentsid AND vtiger_achievementallot_statistic.isover=1 ) GROUP BY  receivedpaymentsid");
							//然后删除要修改的回款匹配
							$paymentid='';
							while($row=$this->db->fetch_array($result)){
								$paymentsRecord[]=$row;
								$paymentid.=",".$row['receivedpaymentsid'];
							}
							$paymentid=trim($paymentid,",");
							$this->db->pquery("DELETE FROM  vtiger_achievementallot WHERE receivedpaymentsid IN(".$paymentid.") ");
						}
						foreach ($data as $row){
							foreach ($paymentsRecord as $key=>$rowsdata){
								//查询分成人部门id
								$userInfo=$this->db->pquery("SELECT * FROM vtiger_user2department WHERE userid=? LIMIT 1 ",array($row['receivedpaymentownid']));
								$userInfo=$this->db->query_result_rowdata($userInfo,0);
								$this->db->pquery('INSERT INTO vtiger_achievementallot(owncompanys,receivedpaymentsid,receivedpaymentownid,businessunit,scalling,servicecontractid,matchdate,discount,seconddiscount,tyuncost,othercost,firstmarketprice,secondmarketprice,postingdate,workorderdate,concattotal,achievementdate,idccost,departmentid) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
									array($row['owncompanys'],$rowsdata['receivedpaymentsid'],$row['receivedpaymentownid'],$rowsdata['unit_price']*$row['scalling']/100,$row['scalling'], $rowsdata['servicecontractid'],$rowsdata['matchdate'],$rowsdata['discount'],$rowsdata['seconddiscount'],$rowsdata['tyuncost'],$rowsdata['othercost'],$rowsdata['firstmarketprice'],$rowsdata['secondmarketprice'],$rowsdata['postingdate'], $rowsdata['workorderdate'], $rowsdata['concattotal'], $rowsdata['achievementdate'], $rowsdata['idccost'], $userInfo['departmentid']));
							}
							$this->db->pquery('INSERT INTO vtiger_servicecontracts_divide(owncompanys, receivedpaymentownid, scalling, servicecontractid, signdempart,separateintoid) VALUES(?,?,?,?,?,?)', array($row['owncompanys'], $row['receivedpaymentownid'], $row['scalling'], $row['servicecontractid'], $row['signdempart'], $row['separateintoid']));
						}
						foreach ($paymentsRecord as $key=>$rowsdata){
							// 走脚本再次抓取数据的回款数据
							Matchreceivements_BasicAjax_Action::commonInsertAchievementallotStatistics($rowsdata['receivedpaymentsid'],$rowsdata['unit_price'],0,0,$rowsdata['servicecontractid'],0,$rowsdata['matchdate']);
						}
						// 删除要更改分成的回款的临时表数据
						$this->db->pquery("DELETE  FROM   vtiger_servicecontracts_update_receivedpayments_divide_tmp  WHERE  servicecontractsid = ? ",array($record));
						$query = "UPDATE vtiger_servicecontracts SET modulestatus=backstatus WHERE servicecontractsid=?";
						$this->db->pquery($query, array($record));

					}
				}
				break;
			case 'DO_CANCEL_ELEC':
				$query = "UPDATE vtiger_servicecontracts SET modulestatus='c_cancel',eleccontractstatus='a_elec_withdraw' WHERE servicecontractsid=?";
				$this->db->pquery($query, array($record));
				break;
			case 'CANCEL_ACTIVE_ORDER':
				$this->cancelActiveOrder($record,$entity['contract_no'],$entity['modulestatus']);
				break;
			case 'CHANGESMOWNER'://更改合同领取人打回
				$recordModel->changeSmowner($record);
				break;
			default :
				break;
		}
		$this->db->pquery("UPDATE vtiger_servicecontracts SET workflowsnode=(SELECT vtiger_salesorderworkflowstages.workflowstagesname FROM `vtiger_salesorderworkflowstages` WHERE vtiger_salesorderworkflowstages.isaction=1 AND vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename='ServiceContracts' LIMIT 1) WHERE servicecontractsid=?",array($record,$record));
		// cxh 2019-08-02 添加 如果该审核需要修改审核列表中的modulestatus（审核流程状态）审核完后走下面代码
		$params['salesorderid']=$request->get('record');
		$params['workflowsid']=$workflowsid;
		$this->hasAllAuditorsChecked($params);

		//如果是取消激活订单/更改提单人则恢复到原有状态
		if(in_array($currentflag,array('CANCEL_ACTIVE_ORDER','CHANGESMOWNER'))){
			$this->db->pquery("UPDATE vtiger_servicecontracts SET modulestatus=?,backstatus='' WHERE servicecontractsid=?",array($entity['backstatus'],$record));
		}

		$querySql= " SELECT isaction,salesorderid  FROM  vtiger_salesorderworkflowstages  WHERE  isaction < 2  AND salesorderid = ?  AND workflowsid =?";
		$list = $this->db->pquery($querySql,array($params['salesorderid'],$params['workflowsid']));
		$isVerfiyed = $this->db->num_rows($list);
		/**电子合同审核成功start**/
		global $createEleContractWorkflowsid;
		//电子合同的审核且审核已完成则回调操作
		if($entity['signaturetype']=='eleccontract' && $workflowsid== $createEleContractWorkflowsid && !$isVerfiyed){
//        if($entity['signaturetype']=='eleccontract' && $workflowsid== $createEleContractWorkflowsid && $entity['modulestatus']=='c_complete'){
			$sql2 = "update vtiger_servicecontracts set modulestatus=?,receivedate=?,eleccontractstatus='b_elec_actioning' where servicecontractsid=?";
			$this->db->pquery($sql2,array('已发放',date("Y-m-d"),$record));
			//发送电子合同给客户
			$res = $recordModel->sendElecContract($record);
			if(!$res){
				$sql3 = "update vtiger_servicecontracts set eleccontractstatus='a_elec_actioning_fail' where servicecontractsid=?";
				$this->db->pquery($sql3,array($record));
			}
		}
		/**电子合同审核成功end**/


		//打回通知威客系统
		$contractModule=ServiceContracts_Record_Model::getCleanInstance("ServiceContracts");
		if($workflowsid==$contractModule->elecContractWorkflowsid && $recordModel->get('signaturetype')=='eleccontract' ){
			$contractModule->syncVerifyResultToWk($record,$entity['eleccontracturl']);
		}

		$nextNodeFlag = $this->getNextNodeFlag($record,$sequence);
		if($nextNodeFlag=='DO_PRINT'){
			$recordModel = Vtiger_Record_Model::getInstanceById($record,'ServiceContracts',true);
			$entity=$recordModel->entity->column_fields;
			$attachmentsids=$this->getFileStyle6Ids($record);
			//向章管家盖章并将文件信息保存下来
			$sealParams=array(
				"sealapply_id"=>$record,
				"uid"=>$entity['assigned_user_id'],
				"attachmentsids"=>$attachmentsids,
				"module"=>'ServiceContracts',
				"servicecontractsprintid"=>$record
			);
			$sContractnoGenerationRecordModel = SContractNoGeneration_Record_Model::getCleanInstance("SContractNoGeneration");
			$sContractnoGenerationRecordModel->sendFileToZhangGuanJia($sealParams);
		}


		$contractModule->syncModuleStatusToYun($record);
	}
	/**
	 * 合同作废打回中处理
	 * @param Vtiger_Request $request
	 */
	public function backallAfter(Vtiger_Request $request)
	{
		$stagerecordid=$request->get('isrejectid');
		$record=$request->get('record');
		$query="SELECT
                    vtiger_workflowstages.workflowstagesflag,
                    vtiger_workflowstages.workflowsid
                FROM
                    `vtiger_salesorderworkflowstages`
                LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid
                WHERE
                    vtiger_salesorderworkflowstages.salesorderworkflowstagesid = ?
                AND vtiger_salesorderworkflowstages.modulename = 'ServiceContracts'";
		$result=$this->db->pquery($query,array($stagerecordid));

		$currentflag = $this->db->query_result($result, 0, 'workflowstagesflag');
		$workflowsid = $this->db->query_result($result, 0, 'workflowsid');
		$recordModel = Vtiger_Record_Model::getInstanceById($record, 'ServiceContracts');
		$entity = $recordModel->entity->column_fields;
		$currentflag = trim($currentflag);

		switch ($currentflag) {
			case 'QUALIFCATION_AUDIT':
				$this->db->pquery("UPDATE `vtiger_account` set isnewcheck=null WHERE accountid =?",array($entity['sc_related_to']));
				$this->db->pquery("DELETE FROM `vtiger_salesorderworkflowstages` WHERE vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename='ServiceContracts' AND vtiger_salesorderworkflowstages.workflowsid=?",array($record,$workflowsid));
				$this->db->pquery("UPDATE vtiger_servicecontracts SET workflowsnode='打回中',modulestatus='a_normal' WHERE servicecontractsid=?",array($record));
				break;
			case 'DIVIDEDMODIFICATION':
				$this->db->pquery("DELETE FROM `vtiger_servicecontracts_divide_tmp` WHERE servicecontractid =?",array($record));
				// 删除要更改分成的回款的临时表数据
				$this->db->pquery("DELETE  FROM   vtiger_servicecontracts_update_receivedpayments_divide_tmp  WHERE  servicecontractsid = ? ",array($record));
			case 'BACKCHANGESTATUS':
			case 'DO_CANCEL'://作废工作流打回
			case 'DO_RETURN_CANCEL':
			case 'DO_CANCEL_ELEC'://电子合同作废申请打回
			case 'CHANGESMOWNER'://更改合同领取人打回
				$this->db->pquery("DELETE FROM `vtiger_salesorderworkflowstages` WHERE vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename='ServiceContracts' AND vtiger_salesorderworkflowstages.workflowsid=?",array($record,$workflowsid));
				$this->db->pquery("UPDATE vtiger_servicecontracts SET modulestatus=backstatus,pagenumber=NULL WHERE servicecontractsid=?",array($record));
				break;
			default :
				$this->db->pquery("DELETE FROM `vtiger_salesorderworkflowstages` WHERE vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename='ServiceContracts' AND vtiger_salesorderworkflowstages.workflowsid=?",array($record,$workflowsid));
				$serviceContractsModuleModel = ServiceContracts_Module_Model::getInstance("ServiceContracts");
				//如果是取消激活订单则恢复到原有状态
				if($workflowsid==$serviceContractsModuleModel->cancelOrderWorkFlowsid){
					$this->db->pquery("UPDATE vtiger_servicecontracts SET  workflowsnode='',modulestatus=?,backstatus='' WHERE servicecontractsid=?",array($entity['backstatus'],$record));
				}else{
					$this->db->pquery("UPDATE vtiger_servicecontracts SET workflowsnode='打回中',modulestatus='a_normal' WHERE servicecontractsid=?",array($record));
				}
				break;
		}

		//打回通知威客系统
		$contractModule=ServiceContracts_Record_Model::getCleanInstance("ServiceContracts");
		/**电子合同打回start**/
		global $createEleContractWorkflowsid;
		if($entity['signaturetype']=='eleccontract' && in_array($workflowsid,array($contractModule->elecContractWorkflowsid,$createEleContractWorkflowsid))) {
//        if($entity['signaturetype']=='eleccontract' && in_array($currentflag,array('AUDIT_VERIFICATION','TWO_VERIFICATION','THREE_VERIFICATION'))) {
//        if($entity['signaturetype']=='eleccontract' && in_array($currentflag,array('DO_CANCEL','DO_CANCEL_ELEC'))) {
			if($workflowsid == $createEleContractWorkflowsid){
				$query = "SELECT * FROM vtiger_activationcode WHERE contractname=? AND `status`!=2";
				$type_result = $this->db->pquery($query, array($entity['contract_no']));
				if ($this->db->num_rows($type_result)) {
					$rowData = $this->db->query_result_rowdata($type_result,0);
					$tyunWebRecordModel = TyunWebBuyService_Record_Model::getCleanInstance("TyunWebBuyService");
					$tyunWebRecordModel->doOrderCancelByContractNo($entity['contract_no'],$rowData['usercodeid'],$rowData['usercode']);
				}
				$this->db->pquery("UPDATE vtiger_servicecontracts SET workflowsnode='已作废',modulestatus='c_cancel',eleccontractstatus='a_elec_sending' WHERE servicecontractsid=?", array($record));

				$data = array(
					'recordid'=>  $record,
					'reason'=>$request->get('reject'),
					'isPass'=>0,
				);
				//审核不通过同步到放心签平台
				$recordModel->auditStatus($data);
			}elseif($workflowsid == $recordModel->elecCancelWorkflowsid){

			}else{
				$params=array(
					"contractId"=>$recordModel->get('eleccontractid'),
					"isPass"=>0,
					"contractNumber"=>$recordModel->get('contract_no'),
					"reason"=>$request->get('reject')
				);
				$result = $recordModel->setAuditStatus($params);
				$data  = json_decode($result,true);
				if($data['success']){
					$this->db->pquery("UPDATE vtiger_servicecontracts SET eleccontractstatus='a_elec_sending' WHERE servicecontractsid=?",array($record));
					$this->db->pquery('UPDATE vtiger_files SET delflag=1 WHERE relationid=?',array($record));}
			}
		}
		/**电子合同打回end**/


		if($workflowsid==$contractModule->elecContractWorkflowsid && $entity['signaturetype']=='eleccontract' ){
			$contractModule->syncVerifyResultToWk($record,'',$request->get('reject'));
		}
		$contractModule->syncModuleStatusToYun($record);


//        //打回后消息提醒
//        //新建时 消息提醒第一审核人进行审核
//        $object = new SalesorderWorkflowStages_SaveAjax_Action();
//        $object->sendWxBackAllRemind(array('salesorderid'=>$entity['servicecontractsid'],'salesorderworkflowstagesid'=>$record));
	}
	/**
	 * @审核工作流程前置触发
	 * @指定结点有
	 * @param Vtiger_Request $request
	 */
	function workflowcheckbefore(Vtiger_Request $request){
		$stagerecordid=$request->get('stagerecordid');
		$record=$request->get('record');
		$db=PearDatabase::getInstance();

		$query="SELECT
                    vtiger_workflowstages.workflowstagesflag,
       vtiger_salesorderworkflowstages.higherid,
       vtiger_salesorderworkflowstages.sequence
                FROM
                    `vtiger_salesorderworkflowstages`
                LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid
                WHERE
                    vtiger_salesorderworkflowstages.salesorderworkflowstagesid = ?
                AND vtiger_salesorderworkflowstages.modulename = 'ServiceContracts'";
		$result=$db->pquery($query,array($stagerecordid));
		$data=$db->fetchByAssoc($result,0);
		$currentflag=$db->query_result($result, 0, 'workflowstagesflag');
		$sequence=$db->query_result($result, 0, 'sequence');

		//到发票审核节点先判断是财务部分字段否为空
		if($currentflag=='DO_RETURN_CANCEL') {
			$query = "SELECT 1 FROM `vtiger_servicecontracts` WHERE  modulestatus='c_cancelings' AND servicecontractsid={$record}";
			$sel_result = $db->pquery($query, array());
			$res_cnt = $db->num_rows($sel_result);
			if ($res_cnt) {
				$resultaa['success'] = 'false';
				$resultaa['error']['message'] = ":请先填写“出纳作废补充”再进行审核!";
				//若果是移动端请求则走这个返回
				if( $request->get('isMobileCheck')==1){
					return $resultaa;
				}else{
					echo json_encode($resultaa);
					exit;
				}
			}
		}
		if($currentflag=='DO_CANCEL'){
			$recordModel = Vtiger_Record_Model::getInstanceById($record,'ServiceContracts',true);
			$entity=$recordModel->entity->column_fields;
			if(!empty($entity['contract_no'])){
				$result = $recordModel->getModule()->doCancelNew($entity['contract_no']);
//                $result = $recordModel->getModule()->doCancel($entity['contract_no']);
				if ($result['success'] == false) {
					$resultaa['success'] = $result['success'];
					$resultaa['error']['message'] = $result['message'];
					//若果是移动端请求则走这个返回
					if( $request->get('isMobileCheck')==1){
						return $resultaa;
					}else{
						echo json_encode($resultaa);
						exit;
					}
				}else{
					$this->db->pquery("UPDATE vtiger_activationcode SET `status`=2 WHERE contractname=?",array($entity['contract_no']));
				}
			}
		}
		if($currentflag=='DO_CANCEL_ELEC'){
			$recordModel = Vtiger_Record_Model::getInstanceById($record,'ServiceContracts',true);
			$eleccontractid=$recordModel->get('eleccontractid');
			$retunJsonData=$recordModel->elecCommonTovoid($eleccontractid);
			$jsonData=json_decode($retunJsonData,true);
			if($jsonData['success']){
				$this->db->pquery("UPDATE vtiger_servicecontracts SET modulestatus='c_cancel',canceltime=?,cancelid=? WHERE servicecontractsid=?",array(date('Y-m-d H:i:s'),$current_user->id,$record));
				//作废订单
				$recordModel->cancelOrderByContractNo($recordModel->get('contract_no'),$record);
			}else{
				$resultaa['success'] = $jsonData['success'];
				$resultaa['error']['message'] = $jsonData['msg'];
				if( $request->get('isMobileCheck')==1){
					return $resultaa;
				}else{
					echo json_encode($resultaa);
					exit;
				}
			}
		}

		if($currentflag=='CHANGESMOWNER'){
			$recordModel = Vtiger_Record_Model::getInstanceById($record,'ServiceContracts',true);
			$servicenum = ServiceContracts_Record_Model::servicecontracts_reviced($data['higherid']);
			if($recordModel->get("modulestatus")=='已发放' && $servicenum){
				$resultaa['success'] = 'false';
				$resultaa['error']['message'] = '你已领取的合同已超出合同领取份额，可将现有合同归还后再次审批';
				//若果是移动端请求则走这个返回
				if ($request->get('isMobileCheck') == 1) {
					return $resultaa;
				} else {
					echo json_encode($resultaa);
					exit;
				}
			}
		}

		$recordModel = Vtiger_Record_Model::getInstanceById($record,'ServiceContracts',true);
		$entity=$recordModel->entity->column_fields;
		$nextFlag = $this->getNextNodeFlag($record,$sequence);
		if($nextFlag=='DO_PRINT'){
			$attachmentsids=$this->getFileStyle6Ids($record);
			if(empty($attachmentsids)){
				$resultaa['success'] = 'false';
				$resultaa['error']['message'] = '缺少待打印附件';
				//若果是移动端请求则走这个返回
				if ($request->get('isMobileCheck') == 1) {
					return $resultaa;
				} else {
					echo json_encode($resultaa);
					exit;
				}
			}
			//向章管家盖章并将文件信息保存下来
			$sealParams=array(
				'uid'=>$entity['assigned_user_id'],
				'name'=>$entity['contract_no'],
				'sealapply_id'=>$record,
				'sealseq'=>$entity['sealseq'],
				'invoicecompany'=>$entity['invoicecompany'],
				'companycode'=>$entity['companycode'],
				'sealplace'=>$entity['sealplace'],
			);
			$sContractnoGenerationRecordModel = SContractNoGeneration_Record_Model::getCleanInstance("SContractNoGeneration");
			$result=$sContractnoGenerationRecordModel->syncToSealHandler($sealParams,'ServiceContracts');
			if(!$result['success']){
				$resultaa['success'] = 'false';
				$resultaa['error']['message'] = $result['msg'];
				//若果是移动端请求则走这个返回
				if ($request->get('isMobileCheck') == 1) {
					return $resultaa;
				} else {
					echo json_encode($resultaa);
					exit;
				}
			}
		}

	}
	//详细的访问权限
	function retrieve_entity_info($record, $module){
		parent::retrieve_entity_info($record, $module);
		if($_REQUEST['module']=='SalesorderWorkflowStages')
		{
			//审核的过来的不验证权限
			return true;
		}
		if(!empty($_REQUEST['realoperate'])){
			$realoperate=setoperate($record,$module);
			if($realoperate==$_REQUEST['realoperate']){
				return true;
			}
		}
		global $currentView,$current_user;
		$where=getAccessibleUsers('ServiceContracts','List',true);
		if($where!='1=1'){
			//编辑或详细视图状态下非管理员的权限验证
			$query='SELECT 1 FROM vtiger_invoicecompanyuser WHERE modulename=\'ht\' AND invoicecompany=? AND userid=?';//合同主体公司的合同管理员
			$result=$this->db->pquery($query,array($this->column_fields['companycode'],$current_user->id));
			$invoicecompanyflag=true;
			if($this->db->num_rows($result)>0){
				$invoicecompanyflag=false;
			}
			$query='SELECT 1 FROM vtiger_specialcontract WHERE specialcontractid=?';//特殊合同
			$result=$this->db->pquery($query,array($record));
			if($this->db->num_rows($result)>0){
				if($invoicecompanyflag ){//合同管理员跳过
					return;
				}
				$recordModel = Vtiger_Record_Model::getCleanInstance('ServiceContracts');
				if($recordModel->personalAuthority('ServiceContracts','SPECIALCONTRACT')){
					return ;
				}
			}

			$query='SELECT 1 FROM vtiger_shareaccount WHERE vtiger_shareaccount.userid in('.implode(',',$where).') and vtiger_shareaccount.sharestatus=1 AND vtiger_shareaccount.accountid=?';//共享商务
			$result=$this->db->pquery($query,array($this->column_fields['sc_related_to']));
			$flag=true;
			if($this->db->num_rows($result)>0){
				$flag=false;
			}

			if(!in_array($this->column_fields['assigned_user_id'],$where) && !in_array($this->column_fields['Receiveid'],$where) && !in_array($this->column_fields['Signid'],$where) && $flag && !getAccessibleCompany('','',false,-1,$this->column_fields['companycode']) && $invoicecompanyflag){
				if($currentView=='Edit' || $currentView=='Detail' || $currentView=='SaveAjax' ){
					$recordModel = Vtiger_Record_Model::getCleanInstance('ServiceContracts');
					if($recordModel->personalAuthority('ServiceContracts','COLLATE')){
						return ;
					}
				}
				if($currentView=='Edit' || $currentView=='Detail'|| $currentView=='SaveAjax' ){
					throw new AppException('你没有操作权限！');
					exit;
				}
			}

		}
	}
	/**
	 * 重写工作流生成
	 * @param unknown $modulename
	 * @param unknown $workflowsid
	 * @param unknown $salesorderid
	 * @param string $isedit
	 */
	public function makeWorkflows($modulename,$workflowsid,$salesorderid,$isedit=''){
		parent::makeWorkflows($modulename, $workflowsid, $salesorderid, $isedit = '');
		$this->beforeMakeWorkFlow($workflowsid,$salesorderid);
		$query = "UPDATE vtiger_salesorderworkflowstages,
				 vtiger_servicecontracts
				SET vtiger_salesorderworkflowstages.accountid=vtiger_servicecontracts.sc_related_to,
				     vtiger_salesorderworkflowstages.modulestatus='p_process',
				     vtiger_salesorderworkflowstages.salesorder_nono=vtiger_servicecontracts.contract_no,
				 vtiger_salesorderworkflowstages.accountname=(SELECT vtiger_account.accountname FROM vtiger_account WHERE vtiger_account.accountid=vtiger_servicecontracts.sc_related_to)
				WHERE vtiger_servicecontracts.servicecontractsid=vtiger_salesorderworkflowstages.salesorderid
				AND vtiger_salesorderworkflowstages.salesorderid=?  AND  vtiger_salesorderworkflowstages.workflowsid=? ";
		$this->db->pquery($query, array($salesorderid,$workflowsid));
		//新建时 消息提醒第一审核人进行审核
		$object = new SalesorderWorkflowStages_SaveAjax_Action();
		$object->sendWxRemind(array('salesorderid'=>$salesorderid,'salesorderworkflowstagesid'=>0));
	}

	/**
	 * 在生成流程前操作
	 * @param $workflowsid
	 * @param $salesorderid
	 */
	public function beforeMakeWorkFlow($workflowsid,$salesorderid){
		global $adb;
		if($workflowsid==372){
			//如果是非标准工作流
//            $sql="select vtiger_account.file,vtiger_account.isnewcheck,vtiger_account.accountid from vtiger_servicecontracts LEFT JOIN vtiger_account on vtiger_servicecontracts.sc_related_to=vtiger_account.accountid where vtiger_servicecontracts.servicecontractsid=?";
//            $result=$adb->pquery($sql,array($salesorderid));
//            $accountid=$adb->query_result($result,0,'accountid');
//            $basicAjaxObject=new ServiceContracts_BasicAjax_Action();
//            $isNeedFlag=$basicAjaxObject->isNeedZizhiFujian($accountid);
//            if($isNeedFlag){
//                //需要加验证
//                $sql="update vtiger_account set isnewcheck=0 where accountid=?";
//                $adb->pquery($sql,array($accountid));
//            }else{
			//不需要进行资质审核,直接
			$deleteSql="delete from vtiger_salesorderworkflowstages where salesorderid=? and workflowstagesflag=?";
			$adb->pquery($deleteSql,array($salesorderid,'QUALIFCATION_AUDIT'));
			$updateSql="update vtiger_salesorderworkflowstages set isaction=1,actiontime=? where salesorderid=? and workflowstagesflag=?";
			$adb->pquery($updateSql,array(date('Y-m-d H:i:s'),$salesorderid,'AUDIT_VERIFICATION'));
//                $this->makeContractNo($salesorderid);
//            }
		}
	}

	/**
	 * 生成合同自动生成合同号
	 * @param $salesorderid
	 * @throws Exception
	 */
	public function makeContractNo($salesorderid){
		$recordModel = Vtiger_Record_Model::getInstanceById($salesorderid, 'ServiceContracts');
		$contract_no=$recordModel->get('contract_no');
		if(empty($contract_no)){
			$query="SELECT productclass,bussinesstype FROM `vtiger_contract_type` WHERE contract_type=?";
			$result=$this->db->pquery($query,array($recordModel->get('contract_type')));
			$productclass=$this->db->query_result($result,0,'productclass');
			$bussinesstype=$this->db->query_result($result,0,'bussinesstype');
			$query="SELECT company_code FROM `vtiger_company_code` WHERE companyfullname=? limit 1";
			$result=$this->db->pquery($query,array($recordModel->get('invoicecompany')));
			$company_codeno=$this->db->query_result($result,0,'company_code');
			$_POST['sc_related_to']=8;
			$_POST['quantity']=1;
			$_POST['company_code']=!empty($company_codeno)?$company_codeno:'ZD';
			$_POST['products_code']=$productclass;
			$_POST['contract_template']='ZDY';
			//$_POST['signstatus']=2;
			$request=new Vtiger_Request($_POST, $_POST);
			$request->set('module','SContractNoGeneration');
			$request->set('view','Edit');
			$request->set('action','Save');
			$ressorder=new Vtiger_Save_Action();
			$ressorder->saveRecord($request);
			$this->db->pquery("UPDATE vtiger_servicecontracts SET contract_no=(SELECT servicecontracts_no FROM vtiger_servicecontracts_print ORDER BY servicecontractsprintid DESC LIMIT 1),servicecontractsprintid=(SELECT MAX(servicecontractsprintid) FROM vtiger_servicecontracts_print LIMIT 1),servicecontractsprint=(SELECT concat(MAX(servicecontractsprintid),'-8') FROM vtiger_servicecontracts_print LIMIT 1),bussinesstype=? WHERE servicecontractsid=?",array($bussinesstype,$salesorderid));
			$this->db->pquery("UPDATE vtiger_crmentity SET label=(SELECT servicecontracts_no FROM vtiger_servicecontracts_print ORDER BY servicecontractsprintid DESC LIMIT 1) WHERE crmid=?",array($salesorderid));
			$this->db->pquery("UPDATE vtiger_servicecontracts_print SET nostand=1,smownerid=? WHERE servicecontractsprintid=(SELECT * from (SELECT max(servicecontractsprintid) FROM vtiger_servicecontracts_print LIMIT 1) as m)",array($recordModel->get('assigned_user_id')));
			// 如果合同编号生成后进行更新 vtiger_salesorderworkflowstages 合同内容
			$salesorder_nono = $this->db->pquery(" SELECT contract_no FROM vtiger_servicecontracts WHERE servicecontractsid =?  limit  1 ",array($salesorderid));
			if ( $this->db->num_rows($salesorder_nono)) {
				while ($rowdata = $this->db->fetch_array($salesorder_nono)) {
					$this->db->pquery('UPDATE `vtiger_salesorderworkflowstages` SET salesorder_nono=? WHERE vtiger_salesorderworkflowstages.salesorderid=?', array($rowdata['contract_no'],$salesorderid));
				}
			}
		}
	}

	/**
	 * tyunweb版产品保存
	 * @param $record
	 */
	public function tyunWriteSalesorderproductsrel($record){
		global $current_user;
		$productids=$_POST['productids'];
		if(!empty($record)){
			$_REQUEST['currentid']=$record;
			//添加工单产品表的状态
			$this->db->pquery('delete from vtiger_salesorderproductsrel where servicecontractsid=? AND(multistatus=? OR multistatus = ?)',array($record,1,0));
		}
		if(!empty($productids)){
			foreach ($productids as $value) {
				$productid=explode('DZE',$value);
				$array = array('salesorderproductsrelid' => $this->db->getUniqueID('vtiger_salesorderproductsrel'),
					'productid' => $productid[1],
					'producttype' => 'std',
					'createtime' => date('Y-m-d H:i:s'),
					'creatorid' => $current_user->id,
					'salesorderproductsrelstatus' => 'pass',
					'ownerid' => $current_user->id,
					'servicecontractsid' => $_REQUEST['currentid'],
					'accountid' => $_REQUEST['sc_related_to'],
					'realmarketprice' => $_REQUEST['realmarketprice'][$value],
					'marketprice' => 0,
					'productcomboid' => $_REQUEST['productcomboid'][$value],
					'productsolution' => $_REQUEST['productsolution'][$value],
					'producttext' => $_REQUEST['producttext'][$value],
					'productnumber' => $_REQUEST['productnumber'][$value],
					'agelife' => $_REQUEST['agelife'][$value],
					'standard' => $_REQUEST['standard'][$value],
					'standardname' => $_REQUEST['standardname'][$value],
					'thepackage' => $_REQUEST['thepackage'][$value],
					'isextra' => $_REQUEST['isextra'][$value],
					'prealprice' => $_REQUEST['prealprice'][$value],
					'punit_price' => $_REQUEST['punit_price'][$value],
					'pmarketprice' => $_REQUEST['pmarketprice'][$value],
					'costing' => $_REQUEST['realprice'][$value],
					'purchasemount' => $_REQUEST['purchasemount'][$value],
					'multistatus' => '1',
					'vendorid' => $_REQUEST['vendorid'][$value],
					'suppliercontractsid' => $_REQUEST['suppliercontractsid'][$value],
					'productname' => $_REQUEST['productname'][$value],
					'opendate' => $_REQUEST['opendate'][$value],
					'closedate' => $_REQUEST['closedate'][$value],
					'istyunweb' => 1
				);
				$this->db->pquery("insert into vtiger_salesorderproductsrel (" . implode(",", array_keys($array)) . ") values(" . generateQuestionMarks($array) . ")", $array);
				//echo "insert into vtiger_salesorderproductsrel (" . implode(",", array_keys($array)) . ") values(" . implode(',',$array) . ")";
			}
		}
	}
	public function createServiceContractsNo($entity){
		if(empty($entity['contract_no'])){
			$query="SELECT productclass FROM `vtiger_contract_type` WHERE contract_type=?";
			$result=$this->db->pquery($query,array($entity['contract_type']));
			$productclass=$this->db->query_result($result,0,'productclass');
			$query="SELECT company_code FROM `vtiger_company_code` WHERE companyfullname=? limit 1";
			$result=$this->db->pquery($query,array($entity['invoicecompany']));
			$company_codeno=$this->db->query_result($result,0,'company_code');
			$_POST['sc_related_to']=8;
			$_POST['quantity']=1;
			$_POST['company_code']=!empty($company_codeno)?$company_codeno:'ZD';
			$_POST['products_code']=$productclass;
			$_POST['contract_template']='ZDY';
			$request=new Vtiger_Request($_POST, $_POST);
			$request->set('module','SContractNoGeneration');
			$request->set('view','Edit');
			$request->set('action','Save');
			$ressorder=new Vtiger_Save_Action();
			$ressorder->saveRecord($request);
			$servicecontractsprintResult=$this->db->pquery('SELECT servicecontracts_no,servicecontractsprintid FROM vtiger_servicecontracts_print ORDER BY servicecontractsprintid DESC LIMIT 1',array());
			$this->db->pquery("UPDATE vtiger_servicecontracts SET contract_no=?,servicecontractsprintid=?,servicecontractsprint=? WHERE servicecontractsid=?",array($servicecontractsprintResult->fields['servicecontracts_no'],$servicecontractsprintResult->fields['servicecontractsprintid'],$servicecontractsprintResult->fields['servicecontractsprintid'].'-8',$entity['record_id']));
			$this->db->pquery("UPDATE vtiger_crmentity SET label=? WHERE crmid=?",array($servicecontractsprintResult->fields['servicecontracts_no'],$entity['record_id']));
			$this->db->pquery("UPDATE vtiger_servicecontracts_print SET nostand=1,smownerid=? WHERE servicecontractsprintid=?",array($entity['assigned_user_id'],$servicecontractsprintResult->fields['servicecontractsprintid']));
			$this->db->pquery('UPDATE `vtiger_salesorderworkflowstages` SET salesorder_nono=? WHERE vtiger_salesorderworkflowstages.salesorderid=?', array($servicecontractsprintResult->fields['servicecontracts_no'],$entity['record_id']));
			//$this->updateRelationMoudleContractNo('vtiger_servicecontracts');
		}
	}
	public function elecContractSend($record){
		global $adb;
		$recordModel = Vtiger_Record_Model::getInstanceById($record,'ServiceContracts',true);
		$contract_no = $recordModel->get('contract_no');
		if(!empty($contract_no)){
			$eleccontractid = $recordModel->get('eleccontractid');
			$arrayData = array("contractId" => $eleccontractid,//放心签平台返回的合同id
				"isPass" => 1, //审核状态，0.不通过 1.通过
				"contractNumber" => $contract_no, //珍岛生成的最终合同编号
				"reason" => "");
			$returnData = $recordModel->setAuditStatus($arrayData);//审核通过
			$returnData=json_decode($returnData,true);
			$eleccontractstatus='a_elec_actioning_fail';
			$eleccontracturl='';
			if ($returnData['success']) {
				$file=$recordModel->get('file');
				$files=explode('##',$file);
				$filesname=strtolower($files[0]);
				$filesname=trim($filesname,'.doc');
				$filesname=trim($filesname,'.docx');
				$filesname=trim($filesname,'.pdf');
				$recordModel->fileSave($returnData['data'],'files_style8','放心签待签订件');
				$recordModel->sendSMS(array('statustype'=>'','mobile'=>$recordModel->get('elereceivermobile'),'eleccontracttpl'=>$filesname,'url'=>$recordModel->elecContractUrl));
				$recordModel->sendMailFXQ();
				$eleccontractstatus='b_elec_actioning';
				$eleccontracturl=$returnData['data'];
			}
			$sql="UPDATE vtiger_servicecontracts SET modulestatus='已发放',receivedate='".date('Y-m-d')."',eleccontractstatus=?,eleccontracturl=? WHERE servicecontractsid=?";
			$adb->pquery($sql,array($eleccontractstatus,$eleccontracturl,$record));
		}
	}

	/**
	 * 审核通过后取消订单
	 *
	 * @param $recordId
	 * @param $contractNo
	 * @param $modulestatus
	 */
	public function cancelActiveOrder($recordId,$contractNo,$modulestatus){
		$query="SELECT a.*,b.sc_related_to FROM vtiger_activationcode a left join vtiger_servicecontracts b on a.contractid=b.servicecontractsid WHERE a.contractid=? AND a.status!=2";
		$type_result=$this->db->pquery($query,array($recordId));
		if($this->db->num_rows($type_result)){
			$max_activationcodeid = 0;
			while ($row = $this->db->fetch_row($type_result)){
				$type_result_datas[] = $row;
				$comeformtyun = $row['comeformtyun'];
				$user_id = $row['usercodeid'];
				$usercode = $row['usercode'];
				$contractid = $row['contractid'];
				$max_activationcodeid = max($row['activationcodeid'],$max_activationcodeid);
				$createdtime =$row['createdtime'];
				$activedate = $row['activedate'];
				$old_sc_related_to=$row['sc_related_to'];
				$couponcode=$row['couponcode'];
			}

			if($comeformtyun == 1) {
				$recordModel = Vtiger_Record_Model::getCleanInstance('TyunWebBuyService');
				$Repson = $recordModel->doOrderCancelByContractNo($contractNo, $user_id, $usercode);
				$jsonData = json_decode($Repson, true);
				if ($jsonData['code'] == '200') {
					$sql = "UPDATE  vtiger_activationcode SET `status`=2,orderstatus='ordercancel',canceldatetime=? WHERE contractname=?";
					$this->db->pquery($sql, array(date('Y-m-d H:i:s'), $contractNo));
					$model = ServiceContracts_Module_Model::getInstance("ServiceContracts");
					$model->clearCancelOrderRelations($contractid,$couponcode);
					$this->db->pquery("UPDATE vtiger_activationcode SET `status`=2 WHERE contractname=?", array($contractNo));
					if ($modulestatus == 'c_complete') {
						$this->db->pquery("update vtiger_contracts_execution_detail set iscancel=1 where contractid=?", array($recordId));
						$this->db->pquery("update vtiger_contract_receivable set iscancel=1 where contractid=?", array($recordId));
						$this->db->pquery("update vtiger_receivable_overdue set iscancel=1 where contractid=?", array($recordId));
					} else {
						//订单取消 删除应对合同收表中数据
						$this->db->pquery("delete from vtiger_contract_receivable where contractid=?", array($recordId));
					}
				}
				if($modulestatus=='已发放') {
					//清空已发放状态下的合同信息
					$this->db->pquery("update vtiger_servicecontracts set  servicecontractstype='',signid='',receiveid='',total='',contract_type='',bussinesstype='',parent_contracttypeid='',isstage=0,sc_related_to='',old_sc_related_to=? where servicecontractsid=?",
						array($old_sc_related_to, $recordId));
					$this->db->pquery("delete from vtiger_salesorderproductsrel where servicecontractsid=?", array($recordId));
					$this->db->pquery("DELETE FROM `vtiger_servicecontracts_divide` WHERE servicecontractid =?", array($recordId));
				}
			}
		}
	}

	public function changeSmowner($recordId){
		$recordModel=ServiceContracts_Record_Model::getInstanceById($recordId,'ServiceContracts');
		global $current_user,$adb;
		$_REQUEST['record'] = $recordId;
		$request=new Vtiger_Request($_REQUEST, $_REQUEST);
		$request->set('assigned_user_id',$current_user->id);
		$request->set('module','ServiceContracts');
		$request->set('Receivedate', date('Y-m-d'));
		$request->set('invoicecompany', $recordModel->get("invoicecompany"));
		$_REQUEST['action']='SaveAjax';
		$request->set('action','SaveAjax');
		$request->set('view','Edit');
		$ressorder=new ServiceContracts_Save_Action();
		$ressorder->saveRecord($request);
		$query='SELECT last_name,email1,vtiger_departments.departmentname FROM vtiger_users
                LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id
                LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_user2department.departmentid
                WHERE id=?';
		$userResult1 = $adb->pquery($query,array($recordModel->get("assigned_user_id")));
		$userResult2 = $adb->pquery($query,array($current_user->id));
		$content='合同编号：'.$recordModel->get('contract_no').'<br>原领取人：'.$userResult1->fields['last_name'].'【'.$userResult1->fields['departmentname'].'】'.'<br>新领取人：'.$userResult2->fields['last_name'].'【'.$userResult2->fields['departmentname'].'】'.'<br>变更时间：'.date('Y-m-d H:i');
		$recordModel = new Vtiger_Record_Model();
		$recordModel->sendWechatMessage(array('email'=>$userResult1->fields['email1'].'|'.$userResult2->fields['email1'],'description'=>$content,'dataurl'=>'#','title'=>'【合同变更提醒】','flag'=>7));
	}

	public function getFileStyle6Ids($record){
		$db=PearDatabase::getInstance();
		$result = $this->db->pquery("select * from vtiger_files where description='ServiceContracts' and style='files_style6' and delflag=0 and relationid=?",array($record));
		if(!$db->num_rows($result)){
			return array();
		}
		$attachmentsids=array();
		while ($row=$this->db->fetchByAssoc($result)){
			$attachmentsids[]=$row['attachmentsid'];
		}
		return $attachmentsids;
	}


	function getNextNodeFlag($record,$sequence){
		$db=PearDatabase::getInstance();
		$sql="select workflowstagesflag from vtiger_salesorderworkflowstages where salesorderid=? and sequence>? order by sequence limit 1";
		$result = $db->pquery($sql,array($record,$sequence));
		if($db->num_rows($result)){
			$row=$db->fetchByAssoc($result,0);
			return $row['workflowstagesflag'];
		}
		return '';
	}
}
?>
