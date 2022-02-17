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

class ReceivedPayments extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_receivedpayments';
    var $table_index= 'receivedpaymentsid';
    var $column_fields = Array();

    /** Indicator if this is a custom module or standard module */
    var $IsCustomModule = true;

    /**
     * Mandatory table for supporting custom fields.
     */
    var $customFieldTable = Array();

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    var $tab_name = Array('vtiger_receivedpayments');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
        'vtiger_receivedpayments'   => 'receivedpaymentsid',
	);

    /**
     * Mandatory for Listing (Related listview)
     */
    var $list_fields = Array (

         // Format: Field Label => Array(tablename, columnname)
         //  tablename should not have prefix 'vtiger_'


        //'Rel Operate'=> Array('receivedpayments', 'reloperate'),
        'Rel Id'=> Array('receivedpayments', 'unit_price'),
        'Related to'=>Array('receivedpayments','relatetoid'),

    	//wangbin 2015-1-14 合同回款列表增加字段
    	'description'=>Array('receivedpayments','overdue'),
    	'RELATE DEPART'=>Array('receivedpayments','createid'),
        //'Status'=>Array('receivedpayments','status'),
    	'Reality_date'=>Array('receivedpayments','reality_date'),
    	'Modifieddate'=>Array('receivedpayments','modifiedtime'),
    	'Mfr PartNo'=>Array('receivedpayments','createtime'),
    	);


    var $list_fields_name = Array(
       //Format: Field Label => fieldname
        //'Rel Operate'=>'reloperate',
        'Rel Id'=> 'unit_price',
       	'Related to'=>'relatetoid',

    	'description'=>'overdue',
    	'RELATE DEPART'=>'createid',
    	'Reality_date'=>'reality_date',
    	'Modifieddate'=>'modifiedtime',
    	'Mfr PartNo'=>'createtime',
    );

    // Make the field link to detail view from list view (Fieldname)
    var $list_link_field = 'relmodule';

    // For Popup listview and UI type support 弹出框的列表字段
    var $search_fields = Array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Rel Name'=> Array('receivedpayments', 'relmodule'),
        'Rel Operate'=> Array('receivedpayments', 'reloperate'),
        'Rel Id'=> Array('receivedpayments', 'unit_price'),
        'Related to'=>Array('receivedpayments','relatetoid')
    );
    var $search_fields_name = Array(
        /* Format: Field Label => fieldname */
        'Rel Name'=> 'relmodule',
        'Rel Operate'=>'reloperate',
        'Rel Id'=> 'unit_price',
        'Related to'=>'relatetoid'
    );

    // For Popup window record selection
    var $popup_fields = Array('relmodule');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'relmodule';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'relmodule';

    // Required Information for enabling Import feature
    var $required_fields = Array('relmodule'=>1);

    // Callback function list during Importing
    var $special_functions = Array('set_import_assigned_user');

    var $default_order_by = 'relmodule';
    var $default_sort_order='ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('relmodule');

    function __construct() {
        global $log, $currentModule;
        $this->column_fields = getColumnFields(get_class($this));
        $this->db = PearDatabase::getInstance();
        $this->log = $log;
    }

   function save_module($module) {
        //2015年5月26日 星期二 wangbin 判断状态，新增/跟编辑，回款保存后，数据库插入/update产品的回款金额，
       $ReceivedPaymentid = $this->id; //回款id
       $serviceid = $_REQUEST['relatetoid']; //合同id
       $input = "";//$_REQUEST['inputalready']; //收款金额
       global $current_user;
       $extra_type   = $_REQUEST['extra_type'];
       $extra_price  = $_REQUEST['extra_price'];
       $extra_remark = $_REQUEST['extra_remark'];
       $extra_sql = "INSERT INTO vtiger_receivedpayments_extra ( id, receivementid, extra_type, extra_price, extra_remark ) VALUES ('', ?, ?, ?, ?)";
       $extra_delete_sql = "DELETE FROM vtiger_receivedpayments_extra WHERE receivementid = ?";
       if(!empty($extra_type)){
            $this->db->pquery($extra_delete_sql,array($ReceivedPaymentid));
           for($i=0;$i<count($extra_type);$i++){
               $this->db->pquery($extra_sql,array($ReceivedPaymentid,$extra_type[$i],$extra_price[$i],$extra_remark[$i]));
           }
       }
       if($this->isTimeoutEntry($_REQUEST['reality_date'])){
           $sql="select * from vtiger_refundtimeoutaudit where receivedpaymentsid=?";
           $result = $this->db->pquery($sql,array($this->id));
           $reality_date=$_REQUEST['old_reality_date'];
           if($reality_date!=$_REQUEST['reality_date']){
               if(empty($_REQUEST['record'])||($this->db->num_rows($result)==0)){
                   //新增超时录入
                   $old_record=$_REQUEST['record'];
                   $_REQUEST['record']=0;
                   $requestd = new Vtiger_Request($_REQUEST, $_REQUEST);
                   $requestd->set('module', 'RefundTimeoutAudit');
                   $requestd->set('view', 'Edit');
                   $requestd->set('action', 'Save');
                   $requestd->set('receivedpaymentsid', $this->id);
                   $requestd->set('workflowsid', '2156373');
                   $_REQUEST['workflowsid']=2156373;
                   $ressorder = new Vtiger_Save_Action();
                   $returnRecord=$ressorder->saveRecord($requestd);
                   //$this->db->pquery("UPDATE vtiger_refundtimeoutaudit SET modulestatus='b_actioning',workflowsid=2156373 WHERE refundtimeoutauditid=?",array($returnRecord->getId()));
                   $this->db->pquery("UPDATE vtiger_refundtimeoutaudit SET modulestatus='b_actioning',workflowsid=2156373 WHERE receivedpaymentsid=?",array($this->id));
                   $this->db->pquery("UPDATE vtiger_receivedpayments SET receivedstatus='TimeoutEntry' WHERE receivedpaymentsid=?",array($this->id));
                   $_REQUEST['record']= $old_record;
               }else{
                   //编辑超时录入
                   $refundtimeoutauditid=$this->db->query_result($result,0,'refundtimeoutauditid');
                   $recordModel=Vtiger_Record_Model::getInstanceById($refundtimeoutauditid,'RefundTimeoutAudit');
                   $recordModel->set('mode','edit');
                   $recordModel->set('unit_price',$_REQUEST['unit_price']);
                   $recordModel->set('reality_date',$_REQUEST['reality_date']);
                   $recordModel->set('owncompany',$_REQUEST['owncompany']);
                   $recordModel->set('paytitle',$_REQUEST['paytitle']);
                   $recordModel->set('workflowstime','');
                   $recordModel->set('workflowsnode','');
                   $recordModel->save();
                   //修改审核流程
                   $refundtimeoutaudit=new RefundTimeoutAudit();
                   $refundtimeoutaudit->makeWorkflows('RefundTimeoutAudit',2156373,$refundtimeoutauditid);
                   $this->db->pquery("UPDATE vtiger_refundtimeoutaudit SET modulestatus='b_actioning',workflowsid=2156373 WHERE receivedpaymentsid=?",array($this->id));
                   $this->db->pquery("UPDATE vtiger_receivedpayments SET receivedstatus='TimeoutEntry' WHERE receivedpaymentsid=?",array($this->id));
               }
           }
       }
       //2015年5月25日 星期一 wangbin 回款业绩添加回款分成信息;
       $suoshugongsi = $_REQUEST['suoshugongsi'];
       $suoshuren = $_REQUEST['suoshuren'];
       $bili = $_REQUEST['bili'];
       $fenchengjine = $_REQUEST['fenchengjine'];

       $sql = "INSERT INTO `vtiger_achievementallot` (owncompanys, receivedpaymentsid, receivedpaymentownid, businessunit,scalling, servicecontractid,matchdate) VALUES (?,?,?,?,?,?,?)";

       if(!empty($suoshuren) && !empty($serviceid)){
           $this->db->pquery("DELETE FROM `vtiger_achievementallot` WHERE receivedpaymentsid =?",array($ReceivedPaymentid));
           for ($i=0;$i<count($suoshuren);++$i){
               $this->db->pquery($sql,array($suoshugongsi[$i],$ReceivedPaymentid,$suoshuren[$i],$fenchengjine[$i],$bili[$i],$serviceid,date("Y-m-d")));
           }
       }
       //end

       $ifsql = "SELECT receivedpaymentsproductsid,serviceid FROM vtiger_receivementproducts WHERE receivedpaymentid = ? LIMIT 1 ";
       $ifresult = $this->db->pquery($ifsql,array($ReceivedPaymentid));
       //判断回款产品表里是否含有此次回款，有跟新，无就新增。
       if(!empty($serviceid)){
           if($this->db->num_rows($ifresult)>0){
                 if($ifresult->fields['serviceid'] == $serviceid){
                    $updatesql = "UPDATE vtiger_receivementproducts SET alreadyprice = ? WHERE receivedPaymentid = ? AND productsid=?";
                    if(!empty($input)){
                        foreach ($input as $key=>$val){
                            $tempkey = explode(',',$key);
                            $this->db->pquery($updatesql,array($val,$ReceivedPaymentid,$tempkey['1']));
                        }
                    }
                 }else{
                     $this->db->pquery("DELETE FROM `vtiger_receivementproducts` WHERE receivedPaymentid =?",array($ReceivedPaymentid));
                     $insertsql ="INSERT INTO vtiger_receivementproducts (receivedPaymentid,serviceid,alreadyprice,productsid,salesorderproductsid) VALUES (?,?,?,?,?)";
                     if(!empty($input)){
                         foreach ($input as $key=>$val){
                             $tempkey = explode(',',$key);
                             $this->db->pquery($insertsql,array($ReceivedPaymentid,$serviceid,$val,$tempkey['1'],$tempkey['0']));
                         }
                     }
                 }
           }else{
                $insertsql ="INSERT INTO vtiger_receivementproducts (receivedPaymentid,serviceid,alreadyprice,productsid,salesorderproductsid) VALUES (?,?,?,?,?)";
                if(!empty($input)){
                    foreach ($input as $key=>$val){
                        $tempkey = explode(',',$key);
                        $this->db->pquery($insertsql,array($ReceivedPaymentid,$serviceid,$val,$tempkey['1'],$tempkey['0']));
                    }
                }
           }

           //长期代付款
           $longagents = $_REQUEST['longagents'];
           $paytitle = $_REQUEST['paytitle'];
           $duedate = $_REQUEST['duedate'];
           $label = preg_replace('/\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;/u','',$paytitle);
           if($longagents ==='on'){
               //$staypayment_id = $this->db->getUniqueID("vtiger_crmentity");
               $_REQUES['record']='';
               $fake_request=new Vtiger_Request($_REQUES, $_REQUES);
               if(!empty( $serviceid)){
                   $return = Staypayment_Record_Model::getaccinfoBYcontractid($serviceid);
                   $accountid = $return['accountid'];
               }
               $fake_request->set('contractid',$serviceid );
               $fake_request->set('accountid',$accountid);
               $fake_request->set('staypaymentname',$paytitle);
               $fake_request->set('staypaymenttype','自动');
               $fake_request->set('createtime',date("Y-m-d H:i:s"));
               $fake_request->set('overdute',$duedate);
               $fake_request->set('createid',$current_user->id);
               $fake_request->set('module','Staypayment');
               $fake_request->set('view','Edit');
               $fake_request->set('action','Save');
               $ressorder=new Staypayment_Save_Action();
               $ressorder->saveRecord($fake_request);
           }
       }
      // 20161108周海 回款匹配了合同 更改 匹配状态 ReceivedPaymentid
      $flag = 0;
      if($serviceid>0){  //合同id存在
         $flag = 1;
      }

      //更新可开票金额(allowinvoicetotal)、可使用金额(rechargeableamount) gaocl add 2018/05/25
      $sql = "update vtiger_receivedpayments set allowinvoicetotal=?,rechargeableamount=?,paymentamount=if(old_receivedpaymentsid>0,paymentamount,".$_REQUEST['unit_price']."),ismatchdepart=? where receivedpaymentsid=?";
      $this->db->pquery($sql, array($_REQUEST['unit_price'],$_REQUEST['standardmoney'] ,$flag, $ReceivedPaymentid));
       //存入支付账号id
       if($_REQUEST['owncompany']){
           $receivedRecordModel=ReceivedPayments_Record_Model::getCleanInstance('ReceivedPayments');
           $companyaccountsid = $receivedRecordModel->getCompanyAccountsIdByOwnCompany($_REQUEST['owncompany']);
           $this->db->pquery("update vtiger_receivedpayments set companyaccountsid=? where receivedpaymentsid=?",array($companyaccountsid,$this->id));
       }
   //end
   //2015年6月5日 星期五 wangbin 在工单产品表里增加产品的回款总金额，便于回款列表的计算;冗余字段添加
   if(!empty($serviceid)){
       $recordModel=Vtiger_Record_Model::getInstanceById($serviceid,"ServiceContracts");
       $recordModel->accountUpgrade();
       //2015年8月21日20:06:39 wangbin 回款保存后,跟新合同剩余字段;
       $updatremaingsql = "UPDATE vtiger_receivedpayments SET remaing = ( SELECT bb.total - aa.receive_total FROM ( SELECT SUM(unit_price) AS receive_total FROM vtiger_receivedpayments WHERE relatetoid = ? ) aa, ( SELECT total FROM vtiger_servicecontracts WHERE servicecontractsid =? ) bb ) WHERE relatetoid = ?";
       $this->db->pquery($updatremaingsql,array($serviceid,$serviceid,$serviceid));

       $selectrecepro = "SELECT vtiger_salesorderproductsrel.salesorderproductsrelid, IFNULL(( SELECT SUM(alreadyprice) FROM vtiger_receivementproducts AS aa WHERE aa.serviceid = vtiger_salesorderproductsrel.servicecontractsid AND aa.productsid = vtiger_salesorderproductsrel.productid ), 0 ) AS already FROM vtiger_salesorderproductsrel LEFT JOIN vtiger_products ON vtiger_salesorderproductsrel.productid = vtiger_products.productid LEFT JOIN vtiger_receivementproducts ON vtiger_salesorderproductsrel.salesorderproductsrelid = vtiger_receivementproducts.salesorderproductsid WHERE vtiger_salesorderproductsrel.servicecontractsid = ? GROUP BY vtiger_salesorderproductsrel.productid";
       $updatesalesprosql = "UPDATE vtiger_salesorderproductsrel SET amountproduct = ? WHERE salesorderproductsrelid = ?";
       $reproresult = $this->db->pquery($selectrecepro,array($serviceid));
       if($this->db->num_rows($reproresult)>0){
           for ($i=0;$i<$this->db->num_rows($reproresult);++$i){
               $arr = $this->db->fetchByAssoc($reproresult);
               $this->db->pquery($updatesalesprosql,array($arr['already'],$arr['salesorderproductsrelid']));
           }
       }
       /////steel加入回款后生成工作流
       $salesorderid=ServiceContracts_Record_Model::getSalesorderid($serviceid);
       if(!empty($salesorderid)){
           foreach($salesorderid as $value){
               if($value['salesorderid']>0){
                   //回款总和是否大于回款
                   if(ServiceContracts_Record_Model::receiveDayprice($serviceid,$value['salesorderid']) || ServiceContracts_Record_Model::whetherPayment($serviceid)){
                       //有没有生成工作流没有则生成
                       if(ServiceContracts_Record_Model::getWorkflows($value['salesorderid'])){
                           //工单对应的工作流是否是标准工作流
                           if(ServiceContracts_Record_Model::getSalesorderworkflowsid($value['salesorderid'])==ServiceContracts_Record_Model::selectWorkfows()){
                               //是否是T-clude套餐
                               if(ServiceContracts_Record_Model::createIsWorkflows('',$serviceid)){
                                   //生成工作流
                                   ServiceContracts_Record_Model::contractsMakeWorkflows($value['salesorderid'],$serviceid,1);
                                   //删除第1,2节点
                                   ServiceContracts_Record_Model::setWorkflowNode($value['salesorderid']);
                               }
                           }
                       }
                   }
                   //检查标准工作流,非T云产品是否回款不足,如果是回款不足且回款大于成本则重新激活工作流
                   ServiceContracts_Record_Model::noStandardToRestart($value['salesorderid'],$serviceid);
               }
           }
       }
   }
   //end

       $classifyRecordModel = ReceivedPaymentsClassify_Record_Model::getCleanInstance("ReceivedPaymentsClassify");
       $classifyRecordModel->systemClassification($this->id);


   }

    /**
     * Return query to use based on given modulename, fieldname
     * Useful to handle specific case handling for Popup
     */
    function getQueryByModuleField($module, $fieldname, $srcrecord) {
        // $srcrecord could be empty
    }

    /**
     * Get list view query (send more WHERE clause condition if required)
     */
    function getListQuery($module, $where='') {
		$query = "SELECT vtiger_crmentity.*, $this->table_name.*";

		// Keep track of tables joined to avoid duplicates
		$joinedTables = array();

		// Select Custom Field Table Columns if present
		if(!empty($this->customFieldTable)) $query .= ", " . $this->customFieldTable[0] . ".* ";

		$query .= " FROM $this->table_name";

		//$query .= "	INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = $this->table_name.$this->table_index";

		$joinedTables[] = $this->table_name;
		$joinedTables[] = 'vtiger_crmentity';

		// Consider custom table join as well.
		if(!empty($this->customFieldTable)) {
			$query .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
				      " = $this->table_name.$this->table_index";
			$joinedTables[] = $this->customFieldTable[0];
		}
		$query .= " LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid";
		$query .= " LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";

		$joinedTables[] = 'vtiger_users';
		$joinedTables[] = 'vtiger_groups';

		$linkedModulesQuery = $this->db->pquery("SELECT distinct fieldname, columnname, relmodule FROM vtiger_field" .
				" INNER JOIN vtiger_fieldmodulerel ON vtiger_fieldmodulerel.fieldid = vtiger_field.fieldid" .
				" WHERE uitype='10' AND vtiger_fieldmodulerel.module=?", array($module));
		$linkedFieldsCount = $this->db->num_rows($linkedModulesQuery);

		for($i=0; $i<$linkedFieldsCount; $i++) {
			$related_module = $this->db->query_result($linkedModulesQuery, $i, 'relmodule');
			$fieldname = $this->db->query_result($linkedModulesQuery, $i, 'fieldname');
			$columnname = $this->db->query_result($linkedModulesQuery, $i, 'columnname');

			$other =  CRMEntity::getInstance($related_module);
			vtlib_setup_modulevars($related_module, $other);

			if(!in_array($other->table_name, $joinedTables)) {
				$query .= " LEFT JOIN $other->table_name ON $other->table_name.$other->table_index = $this->table_name.$columnname";
				$joinedTables[] = $other->table_name;
			}
		}

		global $current_user;
		$query .= $this->getNonAdminAccessControlQuery($module,$current_user);
		$query .= "	WHERE vtiger_crmentity.deleted = 0 ".$usewhere;
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
		global $current_user;

		include("include/utils/ExportUtils.php");

		//To get the Permitted fields query and the permitted fields list
		$sql = getPermittedFieldsQuery('ProjectTask', "detail_view");

		$fields_list = getFieldsListFromQuery($sql);

		$query = "SELECT $fields_list, vtiger_users.user_name AS user_name
					FROM vtiger_crmentity INNER JOIN $this->table_name ON vtiger_crmentity.crmid=$this->table_name.$this->table_index";

		if(!empty($this->customFieldTable)) {
			$query .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
				      " = $this->table_name.$this->table_index";
		}

		$query .= " LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";
		$query .= " LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id and vtiger_users.status='Active'";

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

			$query .= " LEFT JOIN $other->table_name ON $other->table_name.$other->table_index = $this->table_name.$columnname";
		}

		$query .= $this->getNonAdminAccessControlQuery($thismodule,$current_user);
		$where_auto = " vtiger_crmentity.deleted=0";

		if($where != '') $query .= " WHERE ($where) AND $where_auto";
		else $query .= " WHERE $where_auto";

		return $query;
    }

    /**
     * Transform the value while exporting
     */
    function transform_export_value($key, $value) {
        return parent::transform_export_value($key, $value);
    }

    /**
     * Function which will give the basic query to find duplicates
     */
    function getDuplicatesQuery($module,$table_cols,$field_values,$ui_type_arr,$select_cols='') {
		$select_clause = "SELECT ". $this->table_name .".".$this->table_index ." AS recordid, vtiger_users_last_import.deleted,".$table_cols;

		// Select Custom Field Table Columns if present
		if(isset($this->customFieldTable)) $query .= ", " . $this->customFieldTable[0] . ".* ";

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
				$sub_query .= " LEFT JOIN ".$this->customFieldTable[0]." tcf ON tcf.".$this->customFieldTable[1]." = t.$this->table_index";
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
     * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
     */
    function vtlib_handler($modulename, $event_type) {
		global $adb;
        if($event_type == 'module.postinstall') {
			$projectTaskResult = $adb->pquery('SELECT tabid FROM vtiger_tab WHERE name=?', array('ReceivedPayments'));
			$projecttaskTabid = $adb->query_result($projectTaskResult, 0, 'tabid');

			// Mark the module as Standard module
			$adb->pquery('UPDATE vtiger_tab SET customized=0 WHERE name=?', array($modulename));

			if(getTabid('CustomerPortal')) {
				$checkAlreadyExists = $adb->pquery('SELECT 1 FROM vtiger_customerportal_tabs WHERE tabid=?', array($projecttaskTabid));
				if($checkAlreadyExists && $adb->num_rows($checkAlreadyExists) < 1) {
					$maxSequenceQuery = $adb->query("SELECT max(sequence) as maxsequence FROM vtiger_customerportal_tabs");
					$maxSequence = $adb->query_result($maxSequenceQuery, 0, 'maxsequence');
					$nextSequence = $maxSequence+1;
					$adb->query("INSERT INTO vtiger_customerportal_tabs(tabid,visible,sequence) VALUES ($projecttaskTabid,1,$nextSequence)");
					$adb->query("INSERT INTO vtiger_customerportal_prefs(tabid,prefkey,prefvalue) VALUES ($projecttaskTabid,'showrelatedinfo',1)");
				}
			}
			/*
			$modcommentsModuleInstance = Vtiger_Module::getInstance('ModComments');
			if($modcommentsModuleInstance && file_exists('modules/ModComments/ModComments.php')) {
				include_once 'modules/ModComments/ModComments.php';
				if(class_exists('ModComments')) ModComments::addWidgetTo(array('ProjectTask'));
			}

			$result = $adb->pquery("SELECT 1 FROM vtiger_modentity_num WHERE semodule = ? AND active = 1", array($modulename));
			if (!($adb->num_rows($result))) {
				//Initialize module sequence for the module
				$adb->pquery("INSERT INTO vtiger_modentity_num values(?,?,?,?,?,?)", array($adb->getUniqueId("vtiger_modentity_num"), $modulename, 'PT', 1, 1, 1));
			}
			*/
        } else if($event_type == 'module.disabled') {
            // TODO Handle actions when this module is disabled.
        } else if($event_type == 'module.enabled') {
            // TODO Handle actions when this module is enabled.
        } else if($event_type == 'module.preuninstall') {
            // TODO Handle actions when this module is about to be deleted.
        } else if($event_type == 'module.preupdate') {
            // TODO Handle actions before this module is updated.
        } else if($event_type == 'module.postupdate') {
			/*
			$modcommentsModuleInstance = Vtiger_Module::getInstance('ModComments');
			if($modcommentsModuleInstance && file_exists('modules/ModComments/ModComments.php')) {
				include_once 'modules/ModComments/ModComments.php';
				if(class_exists('ModComments')) ModComments::addWidgetTo(array('ProjectTask'));
			}

			$result = $adb->pquery("SELECT 1 FROM vtiger_modentity_num WHERE semodule = ? AND active = 1", array($modulename));
			if (!($adb->num_rows($result))) {
				//Initialize module sequence for the module
				$adb->pquery("INSERT INTO vtiger_modentity_num values(?,?,?,?,?,?)", array($adb->getUniqueId("vtiger_modentity_num"), $modulename, 'PT', 1, 1, 1));
			}
			*/
        }
    }

	/**
	 * Function to check the module active and user action permissions before showing as link in other modules
	 * like in more actions of detail view(Projects).
	 */
	static function isLinkPermitted($linkData) {
		$moduleName = "ReceivedPayments";
		if(vtlib_isModuleActive($moduleName) && isPermitted($moduleName, 'EditView') == 'yes') {
			return true;
		}
		return false;
	}

    // 回款导入
    function importRecord($importdata_object, $fieldData){
        global $current_user;
        $adb = PearDatabase::getInstance();
        $recorid = $adb->getUniqueID('vtiger_receivedpayments');
        $fieldData['relatetoid'] = getEntityId('ServiceContracts', $fieldData['relatetoid']);
        if($fieldData['relatetoid']=='0') $fieldData['relatetoid'] = '';
        if(!empty($fieldData['reality_date']))$fieldData['reality_date'] = date("Y-m-d",strtotime($fieldData['reality_date']));
        //$fieldData['maybe_account'] = getEntityId('Accounts', $fieldData['paytitle']);
        $paytitle = trim( $fieldData['paytitle'],' ');
        $paytitle = str_replace("　",' ',$paytitle);
        $paytitle = preg_replace("/^[\s\v".chr(227).chr(128)."]+/","", $paytitle); //替换开头空字符
        $paytitle = rtrim($paytitle);
        $fieldData['paytitle'] =$paytitle; //替换结尾空字符
        $fieldData['maybe_account'] = $this->getAccountID( $fieldData['paytitle']);
        $fieldData['receivedpaymentsid'] = $recorid;

        // 这里要改变 导入的部门
        if (! empty($fieldData['departmentid'])) {
          $sql = "SELECT departmentid FROM vtiger_departments WHERE departmentname = ? LIMIT 1";
          $result = $adb->pquery($sql, array($fieldData['departmentid']) );
          $noofrows = $adb->num_rows($result);
          if ($noofrows > 0) {
              $department = $adb->fetch_array($result);
              $fieldData['departmentid'] = $department['departmentid'];
          }
        }
        // 匹配的部门
        if (! empty($fieldData['newdepartmentid'])) {
          $sql = "SELECT departmentid FROM vtiger_departments WHERE departmentname = ? LIMIT 1";
          $result = $adb->pquery($sql, array($fieldData['departmentid']) );
          $noofrows = $adb->num_rows($result);
          if ($noofrows > 0) {
              $department = $adb->fetch_array($result);
              $fieldData['newdepartmentid'] = $department['departmentid'];
          }
        }

        // 这里解决 currencytype 字段的问题
        $fieldNames = array_keys($fieldData);
        if (in_array('currencytype', $fieldNames)) {
          $fieldData['receivementcurrencytype'] = $fieldData['currencytype'];
          unset($fieldData['currencytype']);
        }
        // 这里的状态 写死了
        if(empty($fieldData['receivedstatus'])){
            $fieldData['receivedstatus'] = 'normal';
        }elseif($fieldData['receivedstatus']=='void'){
            $fieldData['receivedstatus'] = 'void';
        }elseif($fieldData['receivedstatus']=='refund'){
            $fieldData['receivedstatus'] = 'refund';
        }elseif($fieldData['receivedstatus']=='SupplierRefund'){
            $fieldData['receivedstatus'] = 'SupplierRefund';
        }elseif($fieldData['receivedstatus']=='SupplierRefund'){
            $fieldData['receivedstatus'] = 'SupplierRefund';
        }else{
            $fieldData['receivedstatus'] = 'normal';
        }
        if($this->isTimeoutEntry($fieldData['reality_date'])){
            $fieldData['receivedstatus'] = 'TimeoutEntry';
        }


        // 导入人
        $fieldData['createid'] = $current_user->id;
        // 操作人
        $fieldData['checkid'] = $current_user->id;
        $fieldData['createtime'] = date('Y-m-d H:i:s');
        // 回款导入标示
        $fieldData['overdue'] = '回款导入创建.'.$fieldData['overdue'];
        $fieldData['allowinvoicetotal'] = $fieldData['unit_price'];
        $fieldData['rechargeableamount'] = $fieldData['standardmoney'];
	$fieldData['paymentamount'] = $fieldData['unit_price'];
        $fieldNames = array_keys($fieldData);
        $fieldValues = array_values($fieldData);

        $adb->pquery('INSERT INTO vtiger_receivedpayments ('. implode(',', $fieldNames).') VALUES ('. generateQuestionMarks($fieldValues) .')', $fieldValues);
        if($fieldData['servicecharge']>0){
            $extra_sql = "INSERT INTO vtiger_receivedpayments_extra ( receivementid, extra_type, extra_price, extra_remark ) VALUES (?, ?, ?, ?)";
            $adb->pquery($extra_sql,array($recorid,'手续费',$fieldData['servicecharge'],$fieldData['overdue']));
        }
        $result = $adb->pquery('SELECT * FROM vtiger_receivedpayments WHERE receivedpaymentsid =?',array($recorid));
        if($fieldData['receivedstatus'] == 'TimeoutEntry'){
            $_REQUES['record'] = '';
            $request = new Vtiger_Request($fieldData, $fieldData);
            $request->set('module', 'RefundTimeoutAudit');
            $request->set('view', 'Edit');
            $request->set('action', 'Save');
            $request->set('receivedpaymentsid', $recorid);
            $request->set('workflowsid', '2156373');
            $_REQUEST['workflowsid']=2156373;
            $ressorder = new Vtiger_Save_Action();
            $ressorderecord = $ressorder->saveRecord($request);
            $this->db->pquery('UPDATE vtiger_refundtimeoutaudit SET modulestatus=\'b_actioning\' WHERE refundtimeoutauditid=?',array($ressorderecord->getId()));
        }
        if($adb->num_rows($result)==1){
            if($fieldData['relatetoid']){
                //自动关联合同分成信息
                $divide_arr =  ServiceContracts_Record_Model::servicecontracts_divide($fieldData['relatetoid']);
                //$sql = "INSERT INTO `vtiger_achievementallot` (owncompanys, receivedpaymentsid, receivedpaymentownid, businessunit,scalling, servicecontractid) VALUES (?,?,?,?,?,?)";
                for ($i=0;$i<count($divide_arr);++$i){
                    $divide_temp = $divide_arr[$i];
                    $divide_data['owncompanys'] = $divide_temp['owncompanys'];
                    $divide_data['receivedpaymentsid'] =$recorid;
                    $divide_data['receivedpaymentownid'] = $divide_temp['receivedpaymentownid'];
                    $divide_data['scalling'] = $divide_temp['scalling'];
                    $divide_data['servicecontractid'] = $fieldData['relatetoid'];
                    if(!empty($fieldData['unit_price'])){
                        $divide_data['businessunit'] = ($divide_temp['scalling']*$fieldData['unit_price'])/100;
                        $divideNames = array_keys($divide_data);
                        $divideValues = array_values($divide_data);
                        $adb->pquery('INSERT INTO `vtiger_achievementallot` ('. implode(',', $divideNames).') VALUES ('. generateQuestionMarks($divideValues) .')',$divideValues);
                    }
                }
            }
            return array('id'=>$recorid);
        }else{
            return "";
        }
    }

    /* 当回款匹配了 不许修改 */
/*    function retrieve_entity_info($record, $module){
        parent::retrieve_entity_info($record, $module);
        global $currentView,$current_user;*/

        /*// 上海财务的修改只给周洁一个人
        if($current_user->current_user_departments == 'H79') {
          if (!is_custompowers('shReceivedpaymentsUpdate')) {
            throw new AppException('您没有权限');
            exit;
          }
        }
*/

/*        if($currentView=='Edit') {
            if($this->column_fields['ismatchdepart'] == '1'){
              throw new AppException('匹配成功的回款不能编辑');
              exit;
            }
            if($this->column_fields['receivedstatus'] != 'normal'){
                throw new AppException('只有正常状态的合同才能编辑！');
                exit;
            }
        }
    }*/

    /**
     * Handle saving related module information.
     * NOTE: This function has been added to CRMEntity (base class).
     * You can override the behavior by re-defining it here.
     */
    // function save_related_module($module, $crmid, $with_module, $with_crmid) { }

    /**
     * Handle deleting related module information.
     * NOTE: This function has been added to CRMEntity (base class).
     * You can override the behavior by re-defining it here.
     */
    //function delete_related_module($module, $crmid, $with_module, $with_crmid) { }

    /**
     * Handle getting related list information.
     * NOTE: This function has been added to CRMEntity (base class).
     * You can override the behavior by re-defining it here.
     */
    //function get_related_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }

    /**
     * Handle getting dependents list information.
     * NOTE: This function has been added to CRMEntity (base class).
     * You can override the behavior by re-defining it here.
     */
    //function get_dependents_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }
    /**
     * @param $date
     * @return bool
     * @author: steel.liu
     * @Date:xxx
     * 是否超期
     */
    public function isTimeoutEntry($date){
        $year=date('Ymd',strtotime($date));
        $query="SELECT * FROM `vtiger_workday` WHERE workdayid>=? AND datetype='holiday' limit 20";
        $result=$this->db->pquery($query,array($year));
        $data=array();
        while($row=$this->db->fetch_array($result)){
            $data[]=$row['workdayid'];
        }
        $reality_date=date('Ymd',strtotime($date));
        $currentDay=date('Ymd');
        $legalHoliday=$this->LegalHoliday($reality_date,$data);
        if($currentDay<=$legalHoliday){
            return false;
        }
        return true;
    }

    /**
     * @param $currentday
     * @param $data
     * @return false|string
     * @author: steel.liu
     * @Date:xxx
     * 法定假日内
     */
    public function LegalHoliday($currentday,$data){
        $year=substr($currentday,0,4);
        $month=substr($currentday,4,2);
        $day=substr($currentday,6,2);
        $currentday=$year.'-'.$month.'-'.$day;
        $workdayid=date('Ymd', strtotime ($currentday." +1 day"));
        if(in_array($workdayid,$data)){
            return $this->LegalHoliday($workdayid,$data);
        }else{
            return $workdayid;
        }
    }
    public function getAccountID($accountname){
        $label=preg_replace('/\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;|&quot;|　|&apos;|&amp;|&lt;|&gt;|&#039;|&ldquo;|&rdquo;|&lsquo;|&rsquo;|&hellip;|\“|\”|\‘|\〉|\〈|\’|\〖|\〗|\【|\】|\、|\·|\……|\…|\——|\＋|\－|\＝|\,|\<|\.|\>|\/|\?|\;|\:|\\\'|\"|\[|\{|\]|\}|\\|\||\`|\~|\!|\@|\#|\$|\%|\^|\\&|\*|\(|\)|\-|\_|\=|\+|\，|\＜|\．|\＞|\／|\？|\；|\：|\＇|\＂|\［|\{|\］|\}|\＼|\｜|\｀|\￣|\！|\＠|\#|\＄|\％|\＾|\＆|\＊|\（|\）|\－|\＿|\＝|\＋|\，|\《|\．|\》|\、|\？|\；|\：|\’|\”|\【|\｛|\】|\｝|\＼|\｜|\·|\～|\！|\＠|\＃|\￥|\％|\……|\…|\＆|\×|\（|\）|\－|\——|\—|\＝|\＋|\，|\《|\。|\》|\、|\？|\；|\：|\‘|\“|\【|\｛|\】|\｝|\、|\||\·|\~|\！|\@|\#|\￥|\%|\……|\…|\&|\*|\（|\）|\-|\——|\=|\+/u','',$accountname);
        $label=strtoupper($label);
        $reuslt=$this->db->pquery('SELECT accountid FROM vtiger_uniqueaccountname LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_uniqueaccountname.accountid WHERE vtiger_crmentity.deleted=0 AND vtiger_uniqueaccountname.accountname=? LIMIT 1',array($label));
        $data=$this->db->query_result_rowdata($reuslt,0);
        return $data['accountid'];
    }
}
?>
