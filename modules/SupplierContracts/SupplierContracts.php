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

class SupplierContracts extends CRMEntity {
	var $log;
	var $db;
	var $table_name = "vtiger_suppliercontracts";
	var $table_index= 'suppliercontractsid';

	//var $tab_name = Array('vtiger_crmentity','vtiger_account','vtiger_accountbillads','vtiger_accountscf','vtiger_accountshipads');
	//var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_account'=>'accountid','vtiger_accountbillads'=>'accountaddressid','vtiger_accountscf'=>'accountid','vtiger_accountshipads'=>'accountaddressid');
    // 关联主表 这个地方必须要放该模块的表和id
   // var $tab_name = Array('vtiger_crmentity','vtiger_acctive');
    //var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_acctive'=>'errid');
    var $tab_name = Array('vtiger_crmentity', 'vtiger_suppliercontracts');
    var $tab_name_index = Array('vtiger_crmentity' => 'crmid', 'vtiger_suppliercontracts'=>'suppliercontractsid');

	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array(); //自定义字段的强制表
	var $entity_table = "vtiger_crmentity";

	//var $column_fields = Array('error_id', 'name', 'age', 'address', 'hobby');

	var $sortby_fields = Array();

	//var $groupTable = Array('vtiger_accountgrouprelation','accountid');

	// This is the list of vtiger_fields that are in the lists.
	var $list_fields = Array(
		);

	var $list_fields_name = Array(
			);
	var $list_link_field= 'name';

	var $search_fields = Array(
        'Contract No' => Array('vtiger_suppliercontracts', 'contract_no'),
        'Vendor Name'=>Array('vtiger_suppliercontracts','vendorid'),
        'Products'=>Array('vtiger_suppliercontracts','returndate')
			);

	var $search_fields_name = Array(
        'Contract No' => 'contract_no',
        '客户/供应商'=>'vendorid',
        'Products'=>'returndate'
			);

	// This is the list of vtiger_fields that are required
	var $required_fields =  array();

	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	var $mandatory_fields = Array();

	//Default Fields for Email Templates -- Pavani
	var $emailTemplate_defaultFields = array();

	//Added these variables which are used as default order by and sortorder in ListView
	var $default_order_by = 'suppliercontractsid';
	var $default_sort_order = 'ASC';

	// For Alphabetical search
	var $def_basicsearch_col = '';
	var $related_module_table_index = array(
	);
    var $relatedmodule_list=array('Files','Invoicesign');
    var $relatedmodule_fields=array(
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
    );

	function __construct() {
        global $log, $currentModule;
        $this->column_fields = getColumnFields(get_class($this));
        $this->db = PearDatabase::getInstance();
        $this->log = $log;
    }

    /** Function to handle module specific operations when saving a entity
	*/
	function save_module($module) {
        if($_REQUEST['action']!='SaveAjax') {
			if(empty($_REQUEST['record'])){
                $this->db->pquery('UPDATE vtiger_suppliercontracts SET modulestatus=\'a_normal\' WHERE suppliercontractsid=?',array($this->id));
            }
            // 产品返点删除
            if (!empty($_REQUEST['record'])) {
                $recordid = $_REQUEST['record'];
                $sql = "update vtiger_vendorsrebate set deleted=1 where suppliercontractsid=?";
                $this->db->pquery($sql, array($this->id));
                if (!empty($_REQUEST['updatei'])) {
                    $productid = 'productid=CASE vendorsrebateid ';
                    $productname = 'productname=CASE vendorsrebateid ';
                    $rebate = 'rebate=CASE vendorsrebateid ';
                    $effectdate = 'effectdate=CASE vendorsrebateid ';
                    $enddate = 'enddate=CASE vendorsrebateid ';
                    $vexplain = 'vexplain=CASE vendorsrebateid ';
                    $rebatetype = 'rebatetype=CASE vendorsrebateid ';
                    $vendorid = 'vendorid=CASE vendorsrebateid ';
                    $deleted = 'deleted=CASE vendorsrebateid ';

                    foreach ($_REQUEST['updatei'] as $key => $value) {
                        $valueid = $value;
                        $productid .= sprintf(" WHEN %d THEN %s", $valueid, $_REQUEST['productid'][$key]);
                        $productname .= sprintf(" WHEN %d THEN '%s'", $valueid, $_REQUEST['productname'][$key]);
                        $rebate .= sprintf(" WHEN %d THEN '%s'", $valueid, $_REQUEST['rebate'][$key]);
                        $effectdate .= sprintf(" WHEN %d THEN '%s'", $valueid, $_REQUEST['effectdate'][$key]);
                        $enddate .= sprintf(" WHEN %d THEN '%s'", $valueid, $_REQUEST['enddate'][$key]);
                        $deleted .= sprintf(" WHEN %d THEN '%s'", $valueid, '0');
                        $vexplain .= sprintf(" WHEN %d THEN '%s'", $valueid, $_REQUEST['vexplain'][$key]);
                        $rebatetype .= sprintf(" WHEN %d THEN '%s'", $valueid, $_REQUEST['rebatetype'][$key]);
                        $vendorid .= sprintf(" WHEN %d THEN '%s'", $valueid, $_REQUEST['vendorid']);
                    }
                    $sql = "UPDATE vtiger_vendorsrebate SET
	                    {$productid} ELSE productid END,
	                    {$productname} ELSE productname END,
	                    {$rebate} ELSE rebate END,
	                    {$effectdate} ELSE effectdate END,
	                    {$enddate} ELSE enddate END,
	                    {$vexplain} ELSE vexplain END,
	                    {$rebatetype} ELSE rebatetype END,
	                    {$vendorid} ELSE rebatetype END,
	                    {$deleted} ELSE deleted END
	                    WHERE suppliercontractsid={$recordid}";
                    $this->db->pquery($sql, array());
                }
            }

            //产品返点 添加
            if (!empty($_REQUEST['inserti'])) {
                $invalue = '';
                foreach ($_REQUEST['inserti'] as $value) {
                    $invalue .= "(null, '{$_REQUEST['productid'][$value]}','{$_REQUEST['productname'][$value]}','{$_REQUEST['rebate'][$value]}','{$_REQUEST['effectdate'][$value]}','{$_REQUEST['enddate'][$value]}','{$_REQUEST['vendorid']}','{$_REQUEST['vexplain'][$value]}','{$_REQUEST['rebatetype'][$value]}'," . $this->id . "),";
                }
                $invalue = rtrim($invalue, ',');
                $sql = "INSERT INTO `vtiger_vendorsrebate` (`vendorsrebateid`, `productid`, `productname`, `rebate`, `effectdate`, `enddate`, `vendorid`, `vexplain`,`rebatetype`,`suppliercontractsid`) VALUES " . $invalue;
                $this->db->pquery($sql, array());
            }
            if($_REQUEST['iscomplete']=='on'){
                $query="SELECT sequence FROM vtiger_salesorderworkflowstages WHERE salesorderid=? AND workflowstagesflag='CONTRACT_ACCEPTANCE' AND modulename='SupplierContracts' AND isaction=0";
                $saleResult=$this->db->pquery($query,array($this->id));
                $modulestatus='c_complete';
                if($this->db->num_rows($saleResult)){
                    $sequence=$saleResult->fields['sequence'];
                    $query="SELECT 1 FROM vtiger_salesorderworkflowstages WHERE salesorderid=? AND isaction in(0,1) AND modulename='SupplierContracts' AND sequence<?";
                    $saleResult=$this->db->pquery($query,array($this->id,$sequence));
                    if(!$this->db->num_rows($saleResult)){
                        $modulestatus='b_actioning';
                        $this->db->pquery("UPDATE vtiger_salesorderworkflowstages SET isaction=1 WHERE salesorderid=? AND workflowstagesflag='CONTRACT_ACCEPTANCE' AND modulename='SupplierContracts' AND isaction=0",array($this->id));
                    }
                }
                $this->db->pquery('UPDATE vtiger_suppliercontracts SET modulestatus=? WHERE suppliercontractsid=?',array($modulestatus,$this->id));
            }
            $products=$this->db->pquery('SELECT vtiger_products.productname, vtiger_products.productcategory, vtiger_products.realprice, vtiger_products.unit_price, vtiger_crmentity.smownerid, vtiger_products.productid FROM vtiger_vendorsrebate INNER JOIN vtiger_products ON vtiger_vendorsrebate.productid=vtiger_products.productid INNER JOIN vtiger_crmentity ON vtiger_products.productid = vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted=0 AND vtiger_vendorsrebate.deleted=0 AND vtiger_vendorsrebate.suppliercontractsid=?',array($this->id));

            $rows=$this->db->num_rows($products);
            $checkarray=array();
            $temproductid=array();
            for ($i=0; $i<$rows; ++$i) {
                $product = $this->db->fetchByAssoc($products);
                if(!in_array($product['productid'],$temproductid)){
                    $temproductid[]=$product['productid'];
                    $checkarray[]=array('workflowstagesname'=> $product['productname'].'审核','smcreatorid'=>$product['smownerid'],'productid'=>$product['productid']);
                }

            }
            vglobal('checkproducts',$checkarray);
        }

        if($_REQUEST['soncateid']&&$_REQUEST['type']){
            //将合同主体与合同主体编码绑定
            $this->db->pquery("UPDATE vtiger_suppliercontracts,vtiger_invoicecompany SET vtiger_suppliercontracts.companycode = vtiger_invoicecompany.companycode,vtiger_suppliercontracts.type=?,vtiger_suppliercontracts.soncateid=? WHERE vtiger_invoicecompany.invoicecompany = vtiger_suppliercontracts.invoicecompany AND vtiger_suppliercontracts.suppliercontractsid =?",array($_REQUEST['type'],$_REQUEST['soncateid'],$this->id));
        }else{
            //将合同主体与合同主体编码绑定
            $this->db->pquery("UPDATE vtiger_suppliercontracts,vtiger_invoicecompany SET vtiger_suppliercontracts.companycode = vtiger_invoicecompany.companycode WHERE vtiger_invoicecompany.invoicecompany = vtiger_suppliercontracts.invoicecompany AND vtiger_suppliercontracts.suppliercontractsid =?",array($this->id));
        }


        $this->handlePayApply($this->id,$_REQUEST['payapplyids']);
        if($_REQUEST['payapplyids']){
            if(is_array($_REQUEST['payapplyids'])){
                $this->db->pquery("UPDATE vtiger_suppliercontracts SET vtiger_suppliercontracts.payapplyids = '".implode(',',$_REQUEST['payapplyids'])."' WHERE  vtiger_suppliercontracts.suppliercontractsid =?", array($this->id));
            }else{
                $this->db->pquery("UPDATE vtiger_suppliercontracts SET vtiger_suppliercontracts.payapplyids = ? WHERE  vtiger_suppliercontracts.suppliercontractsid =?", array($_REQUEST['payapplyids'],$this->id));
            }
        }

    }

    function handlePayApply($id,$payapplyids){
        $result = $this->db->pquery("select payapplyids from vtiger_suppliercontracts where suppliercontractsid=?",array($id));
        if(!$this->db->num_rows($result)){
            return;
        }
        if(!is_array($payapplyids)){
            $payapplyids=array($payapplyids);
        }
        $row = $this->db->fetchByAssoc($result,0);
        $oldpayapplyids=$row['payapplyids']?explode(",",$row['payapplyids']):array();
//        $diffArray = array_diff($payapplyids,$oldpayapplyids);
//        if(count($diffArray)==0){
//            return;
//        }
        $this->db->pquery("update vtiger_payapply set isused=0 where payapplyid in(".implode(",",$oldpayapplyids).')',array());
        $this->db->pquery("update vtiger_payapply set isused=1 where payapplyid in(".implode(",",$payapplyids).')',array());

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
                AND vtiger_salesorderworkflowstages.modulename = 'SupplierContracts'";
        $result=$this->db->pquery($query,array($stagerecordid));
        $currentflag=$this->db->query_result($result,0,'workflowstagesflag');
        $workflowsid=$this->db->query_result($result,0,'workflowsid');
        $sequence=$this->db->query_result($result,0,'sequence');
        $recordModel = Vtiger_Record_Model::getInstanceById($record,'SupplierContracts');
        $entity=$recordModel->entity->column_fields;
        $currentflag=trim($currentflag);
        $datetime=date('Y-m-d H:i:s');
        switch($currentflag){
            case 'CREATE_CODE':
                //生成合同编号
                $year=date('Y');
                $monthn=date('m');
                $day=date('d');
                //求合同主体的编码
                $query="SELECT company_codeno FROM `vtiger_company_code` WHERE companyfullname=? limit 1";
                $result=$this->db->pquery($query,array($entity['invoicecompany']));
                $company_codeno=$this->db->query_result($result,0,'company_codeno');
                $company_codeno=!empty($company_codeno)?$company_codeno:'ZD';
                $suppliercontractsstatus=$entity['suppliercontractsstatus'];
                $splitcontNO=explode('-',$entity['contract_no']);
                if(empty($entity['contract_no']) || $splitcontNO[0]!=$suppliercontractsstatus || $splitcontNO[1]!=$company_codeno) {
                    $query = "SELECT suppliercontractsstatus,invoicecompany,meter FROM vtiger_suppliercontractsnodefect WHERE suppliercontractsstatus=? AND invoicecompany=? LIMIT 1";
                    $result = $this->db->pquery($query, array($suppliercontractsstatus, $company_codeno));
                    if ($this->db->num_rows($result)) {

                        $meter = $this->db->query_result($result, 0, "meter");
                        $this->db->pquery("DELETE FROM vtiger_suppliercontractsnodefect WHERE suppliercontractsstatus=? AND invoicecompany=? AND meter=?", array($suppliercontractsstatus, $company_codeno, $meter));
                    } else {
                        $query = "SELECT suppliercontractsstatus,invoicecompany,meter FROM vtiger_suppliercontractsnometer WHERE suppliercontractsstatus=? AND invoicecompany=? LIMIT 1";
                        $result = $this->db->pquery($query, array($suppliercontractsstatus, $company_codeno));
                        if ($this->db->num_rows($result)) {
                            $meter = $this->db->query_result($result, 0, "meter");
                            $meter = 1 + $meter;
                            $meter = str_pad($meter, 4, '0', STR_PAD_LEFT);
                        } else {
                            $meter = '0001';
                        }
                        $this->db->pquery('REPLACE INTO vtiger_suppliercontractsnometer(suppliercontractsstatus,invoicecompany,meter) VALUES(?,?,?)', array($suppliercontractsstatus, $company_codeno, $meter));
                    }
                    $contract_no = $suppliercontractsstatus . '-' . $company_codeno . '-' . $year . $monthn . $day . $meter;
                    if (!empty($entity['contract_no'])) {
                        $this->db->pquery("INSERT INTO vtiger_suppliercontractsnodefect(suppliercontractsstatus,invoicecompany,meter) SELECT '{$splitcontNO[0]}','{$splitcontNO[1]}',meter FROM vtiger_suppliercontracts WHERE suppliercontractsid=?", array($record));
                    }
                    //取供应商的类型是行政采购GX还是业务采购GY
                    //按供应商类型+合同主体+年份+月份+日期+序号生成合同编号
                    $sql = "UPDATE vtiger_suppliercontracts SET contract_no=?,meter=? WHERE suppliercontractsid=?";
                    $this->db->pquery($sql, array($contract_no,$meter, $record));
                    $sql = "UPDATE vtiger_crmentity SET label=? WHERE crmid=?";
                    $this->db->pquery($sql, array($contract_no, $record));
                    $this->db->pquery(" UPDATE vtiger_salesorderworkflowstages SET vtiger_salesorderworkflowstages.salesorder_nono=? WHERE  vtiger_salesorderworkflowstages.salesorderid=? ",array($contract_no,$record));
                }
                break;
            case 'CLOSE_WORKSTREAM':
                //盖章后关闭工作流，且发邮件给领取人
                $this->db->pquery("UPDATE vtiger_suppliercontracts SET modulestatus='c_stamp' WHERE suppliercontractsid=?",array($record));
                $this->db->pquery("UPDATE vtiger_salesorderworkflowstages SET isaction=0 WHERE  vtiger_salesorderworkflowstages.salesorderid=? AND isaction=1 AND vtiger_salesorderworkflowstages.modulename='SupplierContracts'",array($record));//将工作流冻结
                //$this->db->pquery("UPDATE vtiger_servicecontracts_print SET constractsstatus='c_stamp',stamptime=? WHERE servicecontracts_no=?",array($datetime,$entity['contract_no']));
                $user = new Users();
                $current_usert = $user->retrieveCurrentUserInfoFromFile($entity['assigned_user_id']);
                $Subject='合同领取';
                $body='您提交的非标合同已通过审核且打印盖章完成,<br>合同编号:'.$entity['contract_no'].'<br>请到财务部领取';
                $address=array(array('mail'=>$current_usert->column_fields['email1'],'name'=>$current_usert->column_fields['last_name']));
                Vtiger_Record_Model::sendMail($Subject,$body,$address);
                break;
            case 'DO_PRINT':
                //global $current_user;

                //合同打印节点
                //$this->db->pquery("UPDATE vtiger_servicecontracts_print SET constractsstatus='c_print',printer=?,printtime=? WHERE servicecontracts_no=?",array($current_user->id,$datetime,$entity['contract_no']));
                break;
            case 'AUDIT_VERIFICATION':
                //第二个节点指定审核人
                $this->AuditAuditNodeJump($record,$entity['workflowsid'],$entity['assigned_user_id'],'SupplierCAuditset','SupplierContracts',1);
                /*$user = new Users();
                $current_usert = $user->retrieveCurrentUserInfoFromFile($entity['assigned_user_id']);
                $query="SELECT vtiger_auditsettings.oneaudituid,vtiger_auditsettings.towaudituid,vtiger_auditsettings.audituid3 FROM`vtiger_auditsettings` INNER JOIN (SELECT vtiger_departments.parentdepartment,vtiger_departments.departmentid FROM vtiger_departments WHERE vtiger_departments.departmentid='{$current_usert->departmentid}') AS tempdepart ON FIND_IN_SET(vtiger_auditsettings.department,REPLACE(tempdepart.parentdepartment,'::',',')) LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_auditsettings.department  WHERE vtiger_auditsettings.auditsettingtype='SupplierCAuditset' ORDER BY abs(LENGTH(IFNULL(tempdepart.parentdepartment,0))-LENGTH(IFNULL(vtiger_departments.parentdepartment,0))) LIMIT 1";
                $resultAuditSettings=$this->db->pquery($query,array());
                $oneaudituid=$this->db->query_result($resultAuditSettings,0,'oneaudituid');
                $towaudituid=$this->db->query_result($resultAuditSettings,0,'towaudituid');
                $audituid3=$this->db->query_result($resultAuditSettings,0,'audituid3');//第三个节点审核人
                if($oneaudituid==$towaudituid && $towaudituid==$audituid3)
                {//当前第一审核人与第二审核人是同一人，则第二个节点关闭直接跳到第三个工作流
                    $isaction='isaction=2,';
                    $isactionthree='isaction=2,';
                    $this->db->pquery("UPDATE vtiger_salesorderworkflowstages SET isaction=1 WHERE vtiger_salesorderworkflowstages.salesorderid=? AND sequence=4 AND vtiger_salesorderworkflowstages.modulename='SupplierContracts'",array($record));//第三个节点激活
                }
                elseif($oneaudituid==$towaudituid && $towaudituid!==$audituid3)
                {
                    $isaction='isaction=2,';
                    $isactionthree='isaction=1,';
                }
                else
                {
                    $isaction='isaction=1,';
                    $isactionthree='isaction=0,';
                }
                $sql="UPDATE vtiger_salesorderworkflowstages SET {$isaction}ishigher=1,higherid=? WHERE vtiger_salesorderworkflowstages.salesorderid=? AND sequence=2 AND vtiger_salesorderworkflowstages.modulename='SupplierContracts'";
                $this->db->pquery($sql,array($towaudituid,$record));//第二个节点
                $this->db->pquery("UPDATE vtiger_salesorderworkflowstages SET ishigher=1,higherid=16 WHERE vtiger_salesorderworkflowstages.salesorderid=? AND sequence=4 AND vtiger_salesorderworkflowstages.modulename='SupplierContracts'",array($record));//财务主管节点
                $sql="UPDATE vtiger_salesorderworkflowstages SET {$isactionthree}ishigher=1,higherid=? WHERE vtiger_salesorderworkflowstages.salesorderid=? AND sequence=3 AND vtiger_salesorderworkflowstages.modulename='SupplierContracts'";
                $this->db->pquery($sql,array($audituid3,$record));//第三个节点*/
                break;
            case 'TWO_VERIFICATION':
                //第二个节点审核人
                $this->AuditAuditNodeJump($record,$entity['workflowsid'],$entity['assigned_user_id'],'SupplierCAuditset','SupplierContracts',2);
                /*$user = new Users();
                $current_usert = $user->retrieveCurrentUserInfoFromFile($entity['assigned_user_id']);
                $query="SELECT vtiger_auditsettings.oneaudituid,vtiger_auditsettings.towaudituid,vtiger_auditsettings.audituid3 FROM`vtiger_auditsettings` LEFT JOIN (SELECT vtiger_departments.parentdepartment,vtiger_departments.departmentid FROM vtiger_departments WHERE vtiger_departments.departmentid='{$current_usert->departmentid}') AS tempdepart ON FIND_IN_SET(vtiger_auditsettings.department,REPLACE(tempdepart.parentdepartment,'::',',')) LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_auditsettings.department  WHERE vtiger_auditsettings.auditsettingtype='SupplierCAuditset' ORDER BY abs(LENGTH(IFNULL(tempdepart.parentdepartment,0))-LENGTH(IFNULL(vtiger_departments.parentdepartment,0))) LIMIT 1";
                $resultAuditSettings=$this->db->pquery($query,array());
                $oneaudituid=$this->db->query_result($resultAuditSettings,0,'oneaudituid');
                $towaudituid=$this->db->query_result($resultAuditSettings,0,'towaudituid');
                $audituid3=$this->db->query_result($resultAuditSettings,0,'audituid3');//第三个节点审核人
                if($audituid3==$towaudituid)
                {//当前第一审核人与第二审核人是同一人，则第二个节点关闭直接跳到第三个工作流
                    $isaction='isaction=2';
                    $this->db->pquery("UPDATE vtiger_salesorderworkflowstages SET isaction=1 WHERE vtiger_salesorderworkflowstages.salesorderid=? AND sequence=4 AND vtiger_salesorderworkflowstages.modulename='SupplierContracts'",array($record));//第三个节点激活
                }
                else
                {
                    $isaction='isaction=1';
                }
                $sql="UPDATE vtiger_salesorderworkflowstages SET {$isaction} WHERE vtiger_salesorderworkflowstages.salesorderid=? AND sequence=3 AND vtiger_salesorderworkflowstages.modulename='SupplierContracts'";
                $this->db->pquery($sql,array($record));//第二个节点*/
                break;
            case 'DO_CANCEL':
                //作废关闭工作流
                $datetime=date('Y-m-d H:i:s');
                $this->db->pquery("UPDATE vtiger_salesorderworkflowstages SET isaction=0 WHERE vtiger_salesorderworkflowstages.salesorderid=? AND isaction=1 AND vtiger_salesorderworkflowstages.modulename='SupplierContracts'",array($record));//关闭工单节点
                $this->db->pquery("UPDATE vtiger_suppliercontracts SET modulestatus='c_cancel' WHERE suppliercontractsid=?",array($record));
                $query='SELECT newservicecontractsid,contractsagreementid,newservicecontractsno,servicecontractsprintid FROM  vtiger_suppcontractsagreement WHERE suppliercontractsid=?';
                $resultsdata=$this->db->pquery($query,array($record));
                while($row=$this->db->fetch_array($resultsdata))
                {
                    $this->db->pquery("UPDATE vtiger_salesorderworkflowstages SET isaction=0 WHERE vtiger_salesorderworkflowstages.salesorderid=? AND isaction=1 AND vtiger_salesorderworkflowstages.modulename='SuppContractsAgreement'",array($row['contractsagreementid']));//关闭工单节点
                    $this->db->pquery("UPDATE vtiger_suppcontractsagreement SET modulestatus='c_cancel' WHERE contractsagreementid=?",array($row['contractsagreementid']));
                    $this->db->pquery("UPDATE vtiger_salesorderworkflowstages SET isaction=0 WHERE vtiger_salesorderworkflowstages.salesorderid=? AND isaction=1 AND vtiger_salesorderworkflowstages.modulename='SupplierContracts'",array($row['newservicecontractsid']));//关闭工单节点
                    $this->db->pquery("UPDATE vtiger_suppliercontracts SET modulestatus='c_cancel' WHERE suppliercontractsid=?",array($row['newservicecontractsid']));
                }
                //合同作废处理激活码信息
                // 合同归档处理
                if(!empty($entity['archive_code'])){
                    $archive_code = $entity['archive_code'];
                    $this->db->pquery("UPDATE vtiger_suppliercontracts SET archive_code=null,archive_status='archive_no' WHERE suppliercontractsid=?",[$record]);
                    $this->cancelArchiveLog($archive_code);
                }
                break;
            case 'CREATE_SIGN_ONE':
                //合同领取更新领取时间
                $this->db->pquery("UPDATE `vtiger_suppliercontracts` SET receivedate=? WHERE suppliercontractsid=?",array(date("Y-m-d"),$record));
                break;
            default :
                break;
        }
        $this->db->pquery("UPDATE vtiger_suppliercontracts SET workflowsnode=(SELECT vtiger_salesorderworkflowstages.workflowstagesname FROM `vtiger_salesorderworkflowstages` WHERE vtiger_salesorderworkflowstages.isaction=1 AND vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename='SupplierContracts' LIMIT 1) WHERE suppliercontractsid=?",array($record,$record));
        if($entity['modulestatus']=='c_complete'){
            $this->db->pquery("UPDATE `vtiger_suppliercontracts` SET returndate=? WHERE suppliercontractsid=?",array(date("Y-m-d"),$record));
            // 判断归档信息
            if(empty($entity['archive_code'])){
                $sql = "select attachmentsid from vtiger_files where style = files_style4 and relationid = ?";
                $res = $this->db->pquery($sql , [$record]);
                if($this->db->num_rows($res)){
                    $archive_status = 'archive_waiting';
                    $companycode = $entity['companycode'];
                    $archive_code = $this->makeArchiveCode($companycode);

                    $this->db->startTransaction();
                    $sql = "update vtiger_suppliercontracts set archive_code = ?,archive_status = ? where suppliercontractsid = ? ";
                    $this->db->pquery($sql,[$archive_code, $archive_status, $record]);
                    $this->updateOrInsert($archive_code, $companycode, $record);
                    $this->db->completeTransaction();
                }
            }
        }
        // cxh 2019-08-02 添加 如果该审核需要修改审核列表中的modulestatus（审核流程状态）审核完后走下面代码
        $params['salesorderid']=$request->get('record');
        $params['workflowsid']=$workflowsid;
        $this->hasAllAuditorsChecked($params);

        $nextNodeFlag = $this->getNextNodeFlag($record,$sequence);
        if($nextNodeFlag=='DO_PRINT'){
            $recordModel = Vtiger_Record_Model::getInstanceById($record,'SupplierContracts',true);
            $entity=$recordModel->entity->column_fields;
            $attachmentsids=$this->getFileStyle6Ids($record);
            //向章管家盖章并将文件信息保存下来
            $sealParams=array(
                "sealapply_id"=>$record,
                "uid"=>$entity['assigned_user_id'],
                "attachmentsids"=>$attachmentsids,
                "module"=>'SupplierContracts',
                "servicecontractsprintid"=>$record
            );
            $sContractnoGenerationRecordModel = SContractNoGeneration_Record_Model::getCleanInstance("SContractNoGeneration");
            $sContractnoGenerationRecordModel->sendFileToZhangGuanJia($sealParams);
        }
    }

    /**
     * Notes: 合同作废时 - 作废归档日志
     * Author: Bruce.z
     * DateTime: 2022/1/28 11:33
     * @param $archice_code
     */
    private function cancelArchiveLog($archice_code)
    {
        $_companycode = substr(current(explode('-', $archice_code)),0, -6);
        $_ym = substr(current(explode('-', $archice_code)), -6);
        $_code = (int) end(explode('-', $archice_code));
        $sql = "update vtiger_archive_log set suppliercontractsid=NULL ,status=0 where code = ? and ym=? and companycode = ?";

        $this->db->pquery($sql,[$_code, $_ym, $_companycode]);
    }

    /**
     * Notes: 生成归档编号
     * 公司编码+年月-当月序号 （四位编号），次月从0开始，
     * 如1月第一个编号，ZD202201-0001；2月 第一个编号：ZD202202-0001
     * Author: Bruce.z
     * DateTime: 2022/1/27 16:35
     * @return string
     */
    public function makeArchiveCode($company_code)
    {
        global $adb;

        $sql = "select code from vtiger_archive_log
                where ym = '".date('Ym')."' and companycode = ? and status = 0 
                order by id desc limit 1";
        $res = $adb->pquery($sql,[$company_code]);
        if($adb->num_rows($res)){
            $number = $res->fields['code'];
        }else{
            $sql = "select max(code) as code from vtiger_archive_log
                where ym = '".date('Ym')."' and companycode = ?  ";
            $res = $adb->pquery($sql,[$company_code]);
            if($adb->num_rows($res)) $number = $res->fields['code'] + 1;
            else $number = 1;

        }
        $number = str_pad($number,4,'0',STR_PAD_LEFT);
        return $company_code . date('Ym') . '-' . $number;
    }

    /**
     * Notes: 更新/插入 log
     * Author: Bruce.z
     * DateTime: 2022/1/27 18:15
     * @param $code
     * @param $company_code
     * @param $suppliercontractsid
     */
    private function updateOrInsert($code, $company_code, $suppliercontractsid)
    {
        global $adb;

        $_code = (int) end(explode('-', $code));
        $sql = "select id from vtiger_archive_log
                where ym = '".date('Ym')."' and code = ?  and companycode = ? and status = 0
                order by id desc limit 1";
        $res = $adb->pquery($sql,[$_code, $company_code]);
        if($adb->num_rows($res)){
            $sql = "update vtiger_archive_log set suppliercontractsid = ? , status = 1 where id = ?";
            $adb->pquery($sql,[$suppliercontractsid, $res->fields['id']]);
        }else{
            $sql = "insert into vtiger_archive_log (ym,code,suppliercontractsid,companycode,create_time) values (?,?,?,?,?)";
            $adb->pquery($sql,[date('Ym'),$_code, $suppliercontractsid, $company_code, time()]);
        }
    }

    /**
     * @审核工作流程触发
     * @前置事件
     * @指定结点有
     * @param Vtiger_Request $request
     */

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

    function workflowcheckbefore(Vtiger_Request $request){
        $stagerecordid=$request->get('stagerecordid');
        $record=$request->get('record');
        $db=PearDatabase::getInstance();

        $query="SELECT
                    vtiger_workflowstages.workflowstagesflag,
                    vtiger_salesorderworkflowstages.sequence
                FROM
                    `vtiger_salesorderworkflowstages`
                LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid
                WHERE
                    vtiger_salesorderworkflowstages.salesorderworkflowstagesid = ?
                AND vtiger_salesorderworkflowstages.modulename = 'SupplierContracts'";
        $result=$db->pquery($query,array($stagerecordid));
        $currentflag=$db->query_result($result, 0, 'workflowstagesflag');
        $sequence=$db->query_result($result, 0, 'sequence');

        $recordModel = Vtiger_Record_Model::getInstanceById($record,'SupplierContracts');
        $recordModule=Vtiger_Record_Model::getCleanInstance('SupplierContracts');
        if($currentflag=='CREATE_SIGN_TWO'){
            if ($recordModule->checksign($record,'SupplierContractTwo')) {
                $resultaa['success'] = 'false';
                $resultaa['error']['message'] = ":归还请先签名!!";
                //若果是移动端请求则走这个返回
                if( $request->get('isMobileCheck')==1){
                    return $resultaa;
                }else{
                    echo json_encode($resultaa);
                    exit;
                }
            }
        }

        if($currentflag=='CONTRACT_ACCEPTANCE'&&$recordModel->get('frameworkcontract')=='no'){
            $sql="select attachmentsid from vtiger_files where relationid=".$record." and style='files_style12'";
            if(!$this->db->run_query_allrecords($sql)){
                $resultaa['success'] = 'false';
                $resultaa['error']['message'] = ":合同验收请先上传验收单";
                //若果是移动端请求则走这个返回
                if( $request->get('isMobileCheck')==1){
                    return $resultaa;
                }else{
                    echo json_encode($resultaa);
                    exit;
                }
            }
        }
        if($currentflag=='CREATE_SIGN_ONE'){

            if ($recordModule->checksign($record,'SupplierContractOne')) {
                $resultaa['success'] = 'false';
                $resultaa['error']['message'] = ":领取,请先签名!!";
                //若果是移动端请求则走这个返回
                if( $request->get('isMobileCheck')==1){
                    return $resultaa;
                }else{
                    echo json_encode($resultaa);
                    exit;
                }
            }
        }
        //到发票审核节点先判断是财务部分字段否为空
        if($currentflag=='DO_RETURN_CANCEL') {
            $query = "SELECT 1 FROM `vtiger_suppliercontracts` WHERE  modulestatus='c_cancelings' AND suppliercontractsid={$record}";
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


        $recordModel = Vtiger_Record_Model::getInstanceById($record,'SupplierContracts',true);
        $entity=$recordModel->entity->column_fields;
        $nextFlag = $this->getNextNodeFlag($record,$sequence);
        if($nextFlag=='DO_PRINT'){
            $attachmentsids=$this->getFileStyle6Ids($record);
            if(empty($attachmentsids)){
                $resultaa['success'] = 'false';
                $resultaa['error']['message'] = "缺少待打印附件";
                //若果是移动端请求则走这个返回
                if( $request->get('isMobileCheck')==1){
                    return $resultaa;
                }else{
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
                'sealplace'=>$entity['sealplace'],
                'invoicecompany'=>$entity['invoicecompany'],
            );
            $sContractnoGenerationRecordModel = SContractNoGeneration_Record_Model::getCleanInstance("SContractNoGeneration");
            $result=$sContractnoGenerationRecordModel->syncToSealHandler($sealParams,'SupplierContracts');
            if(!$result['success']){
                $resultaa['success'] = 'false';
                $resultaa['error']['message'] = $result['msg'];
                //若果是移动端请求则走这个返回
                if( $request->get('isMobileCheck')==1){
                    return $resultaa;
                }else{
                    echo json_encode($resultaa);
                    exit;
                }
            }
        }
    }

    public function getFileStyle6Ids($record){
        $db=PearDatabase::getInstance();
        $result = $this->db->pquery("select * from vtiger_files where description='SupplierContracts' and style='files_style6' and delflag=0 and relationid=?",array($record));
        if(!$db->num_rows($result)){
            return array();
        }
        $attachmentsids=array();
        while ($row=$this->db->fetchByAssoc($result)){
            $attachmentsids[]=$row['attachmentsid'];
        }
        return $attachmentsids;
    }
    /**
     * 合同作废打回后置处理
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
                AND vtiger_salesorderworkflowstages.modulename = 'SupplierContracts'";
        $result=$this->db->pquery($query,array($stagerecordid));

        $currentflag=$this->db->query_result($result,0,'workflowstagesflag');
        $workflowsid=$this->db->query_result($result,0,'workflowsid');
        $recordModel = Vtiger_Record_Model::getInstanceById($record,'SupplierContracts');
        $entity=$recordModel->entity->column_fields;
        $currentflag=trim($currentflag);
        switch($currentflag){
            case 'DO_CANCEL':
            case 'DO_RETURN_CANCEL':
                //作废工作流打回
                $this->db->pquery("DELETE FROM `vtiger_salesorderworkflowstages` WHERE vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename='SupplierContracts' AND vtiger_salesorderworkflowstages.workflowsid=?",array($record,$workflowsid));
                $this->db->pquery("UPDATE vtiger_suppliercontracts SET modulestatus=backstatus,pagenumber=NULL WHERE suppliercontractsid=?",array($record));
                break;
            default :
                $this->db->pquery("DELETE FROM `vtiger_salesorderworkflowstages` WHERE vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename='SupplierContracts' AND vtiger_salesorderworkflowstages.workflowsid=?",array($record,$workflowsid));
                //$this->db->pquery("UPDATE vtiger_salesorderworkflowstages SET isaction=0 WHERE modulename='SupplierContracts' AND salesorderid=? AND workflowsid=?",array($record,$workflowsid));
                $this->db->pquery("UPDATE vtiger_suppliercontracts SET workflowsnode='打回中',modulestatus='a_normal',receiptorid='' WHERE suppliercontractsid=?",array($record));
                break;
        }


        //打回后 将对应的支出申请单有效期自动增加30天
        if ($entity['payapplyids']) {
            $payapplyids = explode(',',$entity['payapplyids']);
            if(count($payapplyids)>1){
                $this->db->pquery("update vtiger_payapply set isused=0,enddate=DATE_ADD(enddate, INTERVAL 30 DAY) where payapplyid in (" . $entity['payapplyids'] . ")");
            }
        }

    }
    //详细的访问权限
    function retrieve_entity_info($record, $module){
        parent::retrieve_entity_info($record, $module);
        if(!empty($_REQUEST['module']) && $_REQUEST['module']=='SalesorderWorkflowStages')
        {
            //审核的过来的不验证权限
            return true;
        }
        global $currentView,$current_user;
        $where=getAccessibleUsers('SupplierContracts','List',true);
        if($where!='1=1'){
            $query='SELECT 1 FROM vtiger_invoicecompanyuser WHERE modulename=\'ht\' AND invoicecompany=? AND userid=?';
            $result=$this->db->pquery($query,array($this->column_fields['companycode'],$current_user->id));
            $invoicecompanyflag=true;
            if($this->db->num_rows($result)>0){
                $invoicecompanyflag=false;
            }
            //编辑或详细视图状态下非管理员的权限验证
            if(!in_array($this->column_fields['assigned_user_id'],$where) && !in_array($this->column_fields['Signid'],$where) && !in_array($this->column_fields['receiptorid'],$where) && !getAccessibleCompany('','',false,-1,$this->column_fields['companycode']) && $_REQUEST['module']!='SalesorderWorkflowStages' && $invoicecompanyflag){
                if($currentView=='Edit' || $currentView=='Detail'|| $currentView=='SaveAjax' || $_REQUEST['realoperate']!='herejump'){
                    if(!empty($_REQUEST['realoperate'])){
                        $realoperate=setoperate($record,$module);
                        if($realoperate==$_REQUEST['realoperate']){
                            return true;
                        }
                    }
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
    public function makeWorkflows($modulename,$workflowsid,$salesorderid,$isedit='',$type='',$sourceid='',$ceocheck='')
    {
        /*if ($type == 'purchase') {
            parent::makeWorkflows($modulename, $workflowsid, $salesorderid, $isedit = '');
        } else {*/
            parent::makeWorkfilterflows($modulename, $workflowsid, $salesorderid, $sourceid, $ceocheck, $isedit = '');
        //}
        $query = "UPDATE vtiger_salesorderworkflowstages,
				 vtiger_suppliercontracts
				SET vtiger_salesorderworkflowstages.accountid=vtiger_suppliercontracts.vendorid,
				    vtiger_salesorderworkflowstages.salesorder_nono=vtiger_suppliercontracts.contract_no,
				    vtiger_salesorderworkflowstages.modulestatus='p_process',
				 vtiger_salesorderworkflowstages.accountname=(SELECT vtiger_vendor.vendorname FROM vtiger_vendor WHERE vtiger_vendor.vendorid=vtiger_suppliercontracts.vendorid)
				WHERE vtiger_suppliercontracts.suppliercontractsid=vtiger_salesorderworkflowstages.salesorderid
				AND vtiger_salesorderworkflowstages.salesorderid=?  AND  vtiger_salesorderworkflowstages.workflowsid=?";
        $this->db->pquery($query, array($salesorderid, $workflowsid));
        if ($type == 'purchase') {
            global $current_user;
            $needletwo = 'H283::';
            $query = 'SELECT vtiger_departments.parentdepartment FROM vtiger_departments WHERE departmentid=?';
            $result = $this->db->pquery($query, array($current_user->departmentid));
            $data = $this->db->raw_query_result_rowdata($result, 0);
            $parentdepartment = $data['parentdepartment'];
            $parentdepartment .= '::';
            if (strpos($parentdepartment, $needletwo) === false || $_POST['suppliercontractsstatus'] == 'GX') {
                $deletedSql = "DELETE FROM vtiger_salesorderworkflowstages WHERE salesorderid=? AND workflowstagesflag=? AND workflowsid=?";
                $this->db->pquery($deletedSql, array($salesorderid, 'COMPANY_MEDIA', $workflowsid));
            } else {
                $updateSql = "UPDATE vtiger_salesorderworkflowstages,
                 vtiger_workflowstages
                SET vtiger_salesorderworkflowstages.ishigher = 1,
                 vtiger_salesorderworkflowstages.higherid =?
                WHERE
                    vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid
                AND vtiger_salesorderworkflowstages.salesorderid =?
                AND vtiger_workflowstages.workflowstagesflag='COMPANY_MEDIA'
                AND vtiger_salesorderworkflowstages.workflowsid = vtiger_workflowstages.workflowsid
                AND vtiger_salesorderworkflowstages.workflowsid =?";
                $this->db->pquery($updateSql, array(7629, $salesorderid, $workflowsid));
            }
            $this->setContractAcceptance($salesorderid);
        }else{
            $deletedSql = "DELETE FROM vtiger_salesorderworkflowstages WHERE salesorderid=? AND workflowstagesflag=? AND workflowsid=?";
            $this->db->pquery($deletedSql, array($salesorderid, 'CONTRACT_ACCEPTANCE', $workflowsid));
            $updateSql='UPDATE vtiger_salesorderworkflowstages SET isaction=1 WHERE 
            sequence=(SELECT seq FROM (SELECT min(strd.sequence) as seq FROM  vtiger_salesorderworkflowstages strd WHERE strd.salesorderid=? AND strd.workflowsid=?) sdd) AND salesorderid=? AND workflowsid=?';
            $this->db->pquery($updateSql, array($salesorderid,$workflowsid,$salesorderid,$workflowsid));
        }
            //新建时 消息提醒第一审核人进行审核
            $object = new SalesorderWorkflowStages_SaveAjax_Action();
            $object->sendWxRemind(array('salesorderid' => $salesorderid, 'salesorderworkflowstagesid' => 0));

    }

    /**
     * 设置服务合同验收人
     * @param $salesorderid
     */
    public function setContractAcceptance($salesorderid){
        $sql="SELECT vtiger_user2department.departmentid,vtiger_user2department.userid FROM vtiger_suppliercontracts LEFT JOIN vtiger_crmentity ON vtiger_suppliercontracts.suppliercontractsid=vtiger_crmentity.crmid LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_crmentity.smownerid where vtiger_suppliercontracts.suppliercontractsid=?";
        $result=$this->db->pquery($sql,array($salesorderid));
        $userid=$this->db->query_result($result,0,'userid');
        $departmentid=$this->db->query_result($result,0,'departmentid');
        $sql="select vtiger_user2department.userid from vtiger_user2department left join vtiger_users on vtiger_user2department.userid=vtiger_users.id  where vtiger_users.status='Active' and   vtiger_user2department.departmentid='".$departmentid."' and vtiger_user2department.userid!=".$userid;
        $userArray=$this->db->run_query_allrecords($sql);
        if(!$userArray){
            $sql="select reports_to_id from vtiger_users where id=?";
            $result1=$this->db->pquery($sql,array($userid));
            $userId=$this->db->query_result($result1,0,'reports_to_id');
        }else{
            $userId=$userArray[array_rand(array_column($userArray,'userid'))]['userid'];
        }
        $sql="update vtiger_salesorderworkflowstages set ishigher=1,higherid=? where salesorderid=? and workflowstagesflag='CONTRACT_ACCEPTANCE'";
        $this->db->pquery($sql,array($userId,$salesorderid));
    }
}
?>
