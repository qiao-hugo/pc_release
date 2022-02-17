<?php
include_once('config.php');
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');
require_once('include/utils/utils.php');
require_once('user_privileges/default_module_view.php');
// Account is used to store vtiger_account information.
class OrderChargeback extends CRMEntity {
	var $log;
	var $db;
	var $table_name = "vtiger_orderchargeback";
	var $table_index= 'orderchargebackid';
	//var $tab_name = Array('vtiger_crmentity','vtiger_salesorder','vtiger_salesordercf','vtiger_inventoryproductrel');
	//var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_salesorder'=>'salesorderid','vtiger_salesordercf'=>'salesorderid','vtiger_inventoryproductrel'=>'id');
    var $tab_name = Array('vtiger_crmentity','vtiger_orderchargeback');
    var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_orderchargeback'=>'orderchargebackid');
    /**
	 * Mandatory table for supporting custom fields.
	 */
	//var $customFieldTable = Array('vtiger_salesordercf', 'salesorderid');
	var $entity_table = "vtiger_crmentity";
	var $column_fields = Array();
	//var $sortby_fields = Array('subject','smownerid','accountname','lastname');
	// This is used to retrieve related vtiger_fields from form posts.
	//var $additional_column_fields = Array('assigned_user_name', 'smownerid', 'opportunity_id', 'case_id', 'contact_id', 'task_id', 'note_id', 'meeting_id', 'call_id', 'email_id', 'parent_name', 'member_id' );
	// This is the list of vtiger_fields that are in the lists.

	var $list_fields = Array(
				// Module Sequence Numbering
				//'Order No'=>Array('crmentity'=>'crmid'),

				);

	var $list_fields_name = Array(

				      );

	var $list_link_field= 'orderchargebackid';
	//弹出页面的搜索下拉字段 wangbin 
	var $search_fields = Array(
		//'Order No'=>Array('salesorder'=>'salesorder_no'),'Subject'=>Array('salesorder'=>'subject'),'Quote Name'=>Array('salesorder'=>'quoteid')
			//'Account Name'=>Array('account'=>'accountid'),
	);
	//弹出页面列表字段的显示控制 wangbin
	var $search_fields_name = Array(
		//'Order No'=>'salesorder_no','Subject'=>'subject',
			// 'Account Name'=>'account_id',// 'Quote Name'=>'quote_id'
	);
	// This is the list of vtiger_fields that are required.
	//var $required_fields =  array("accountname"=>1);
	//Added these variables which are used as default order by and sortorder in ListView
	var $default_order_by = 'orderchargebackid';
	var $default_sort_order = 'ASC';
	//var $groupTable = Array('vtiger_sogrouprelation','salesorderid');
	//var $mandatory_fields = Array('subject','createdtime' ,'modifiedtime', 'assigned_user_id');
	// For Alphabetical search
	var $def_basicsearch_col = 'orderchargeback_no';

	// For workflows update field tasks is deleted all the lineitems.
	var $isLineItemUpdate = true;
	private $refundreasondoing=array('客户重复打款','退款不终止业务');

    //2015年9月11日  wangibn添加右侧域名备案关联;
    //var $relatedmodule_list=array('IdcRecords');
    //var $relatedmodule_fields=array('IdcRecords'=>array('salesorder_no'=>'工单编号','idcstate'=>'状态','ipaddress'=>'IP地址'));

	/** Constructor Function for SalesOrder class
	 *  This function creates an instance of LoggerManager class using getLogger method
	 *  creates an instance for PearDatabase class and get values for column_fields array of SalesOrder class.
	 */
	function OrderChargeback() {
		$this->log =LoggerManager::getLogger('OrderChargeback');
		$this->db = PearDatabase::getInstance();
		$this->column_fields = getColumnFields('OrderChargeback');
	}

	function save_module($module){

        if(empty($_REQUEST['record'])){
            $recordid=$this->id;
            //发票
            if(!empty($_POST['salesorderproduct'])){
                $salesorderproductstr='';
                foreach($_POST['salesorderproduct'] as $key=>$value){
                   if(!empty($value)){
                       foreach($value as $val){

                           $sql="insert into vtiger_salesorderproductsrel (`salesorderid`,`multistatus`,`salesorderproductsrelname`,`servicecontractsid`,`productid`,`productcomboid`,`producttype`,`amountproduct`,`marketprice`,`realmarketprice`,`costing`,`realcosting`,`productform`,`auditorid`,`audittime`,`createtime`,`creatorid`,`salesorderproductsrelstatus`,`remark`,`relatorids`,`ownerid`,`backerid`,`backtime`,`backwhy`,`servicecount`,`serviceamount`,`schedule`,`starttime`,`endtime`,`modifiedtime`,`isvisible`,`nodestatus`,`finishstatus`,`accountid`,`purchasemount`,`ip`,`domain`,`space`,`producttext`,`productsolution`,`TsiteNew`,`Tsite`,`productnumber`,`standard`,`thepackage`,`isextra`,`agelife`,`prealprice`,`punit_price`,`pmarketprice`) SELECT {$recordid},5,`salesorderproductsrelname`,`servicecontractsid`,`productid`,`productcomboid`,`producttype`,`amountproduct`,`marketprice`,`realmarketprice`,`costing`,`realcosting`,`productform`,`auditorid`,`audittime`,`createtime`,`creatorid`,`salesorderproductsrelstatus`,`remark`,`relatorids`,`ownerid`,`backerid`,`backtime`,`backwhy`,`servicecount`,`serviceamount`,`schedule`,`starttime`,`endtime`,`modifiedtime`,`isvisible`,`nodestatus`,`finishstatus`,`accountid`,`purchasemount`,`ip`,`domain`,`space`,`producttext`,`productsolution`,`TsiteNew`,`Tsite`,`productnumber`,`standard`,`thepackage`,`isextra`,`agelife`,`prealprice`,`punit_price`,`pmarketprice` FROM vtiger_salesorderproductsrel WHERE salesorderproductsrelid={$val}";
                           $this->db->pquery($sql,array());
                           $newid=$this->db->getLastInsertID();
                           $salesorderproductstr.="({$recordid},{$key},{$val},{$newid},'SalesOrder'),";
                       }
                   }
                }
            }else{
                $salesorderproductstr='';
            }
            if(!empty($_POST['invocieid'])){
                foreach($_POST['invocieid'] as $key=>$value){
                    if(!empty($value)){
                        foreach($value as $val){
                            $salesorderproductstr.="({$recordid},{$key},{$val},NULL,'Invoice'),";
                        }
                    }
                }
            }
            $salesorderproductstr=rtrim($salesorderproductstr,',');
            if($salesorderproductstr!=''){
                $sql="INSERT INTO vtiger_orderchargeproducts(orderchargebackid,oldorderid,oldproductid,newproductid,setype) VALUES{$salesorderproductstr}";
                $this->db->pquery($sql,array());
            }
            $now=date("Y-m-d");
            $this->db->pquery("UPDATE vtiger_orderchargeback SET applytime=? WHERE orderchargebackid=?",array($now,$recordid));
        }else{
            $recordid=$_REQUEST['record'];
            $query="SELECT * FROM vtiger_orderchargeproducts WHERE orderchargebackid={$recordid}";
            //原数据
            $oldarray=$this->db->run_query_allrecords($query);
            $salesoldorderid=array();
            $invoiceoldorderid=array();
            $invoiceMoldorderid=array();
            if(!empty($oldarray)){
                foreach($oldarray as $value){
                    if('SalesOrder'==$value['setype']){
                        $salesoldorderid[$value['newproductid']]=$value['oldproductid'];
                    }else if('Invoice'==$value['setype']){
                        $invoiceoldorderid[]=$value['oldproductid'];
                        $invoiceMoldorderid[$value['oldproductid']]=$value['oldorderid'];
                    }
                }
            }
            $salesorderproduct=array();
            $insertsalesorderproduct=array();
            if(!empty($_POST['salesorderproduct'])){
                foreach($_POST['salesorderproduct'] as $key=>$value){
                    if(!empty($value)){
                        foreach($value as $val){
                            $salesorderproduct[]=$val;
                            $insertsalesorderproduct[$val]=$key;
                        }
                    }
                }
            }
            $invoiceproduct=array();
            $insertinvoiceproduct=array();
            if(!empty($_POST['invocieid'])){
                foreach($_POST['invocieid'] as $key=>$value){
                    if(!empty($value)){
                        foreach($value as $val){
                            $invoiceproduct[]=$val;
                            $insertinvoiceproduct[$val]=$key;
                        }
                    }
                }
            }
            $deleteoldproductid=array_diff($salesoldorderid,$salesorderproduct);
            $insertnewproductid=array_diff($salesorderproduct,$salesoldorderid);
            $deleteoldinvoiceid=array_diff($invoiceoldorderid,$invoiceproduct);
            $insertnewinvoiceid=array_diff($invoiceproduct,$invoiceoldorderid);
            if(!empty($deleteoldproductid)){
                $deleteoldproductstr='';
                $deleteoldsaleproductstr='';
                foreach($deleteoldproductid as $key=>$value){
                    $deleteoldsaleproductstr.=$key.',';
                    $deleteoldproductstr.=' (oldproductid='.$value.' AND newproductid='.$key.') OR';
                }
                if($deleteoldproductstr!=''){
                    $deleteoldproductstr=rtrim($deleteoldproductstr,'OR');
                    $sql="DELETE FROM vtiger_orderchargeproducts WHERE orderchargebackid={$recordid} AND ({$deleteoldproductstr})";
                    $deleteoldsaleproductstr.=rtrim($deleteoldsaleproductstr,',');
                    $sql1="DELETE FROM vtiger_salesorderproductsrel WHERE salesorderid={$recordid} AND salesorderproductsrelid IN ({$deleteoldsaleproductstr})";
                    $this->db->pquery($sql,array());
                    $this->db->pquery($sql1,array());
                }

            }
            if(!empty($deleteoldinvoiceid)){
                $deleteoldsaleproductstr='';
                foreach($deleteoldinvoiceid as $value){
                    $deleteoldsaleproductstr.=' (oldorderid='.$invoiceMoldorderid[$value].' AND oldproductid='.$value.') OR';
                }
                $deleteoldsaleproductstr=rtrim($deleteoldsaleproductstr,'OR');
                $sql="DELETE FROM vtiger_orderchargeproducts WHERE orderchargebackid={$recordid} AND ({$deleteoldsaleproductstr})";
                $this->db->pquery($sql,array());
            }
            $salesorderproductstr='';
            if(!empty($insertnewproductid)){
                foreach($insertnewproductid as $value){
                    $sql="insert into vtiger_salesorderproductsrel (`salesorderid`,`multistatus`,`salesorderproductsrelname`,`servicecontractsid`,`productid`,`productcomboid`,`producttype`,`amountproduct`,`marketprice`,`realmarketprice`,`costing`,`realcosting`,`productform`,`auditorid`,`audittime`,`createtime`,`creatorid`,`salesorderproductsrelstatus`,`remark`,`relatorids`,`ownerid`,`backerid`,`backtime`,`backwhy`,`servicecount`,`serviceamount`,`schedule`,`starttime`,`endtime`,`modifiedtime`,`isvisible`,`nodestatus`,`finishstatus`,`accountid`,`purchasemount`,`ip`,`domain`,`space`,`producttext`,`productsolution`,`TsiteNew`,`Tsite`,`productnumber`,`standard`,`thepackage`,`isextra`,`agelife`,`prealprice`,`punit_price`,`pmarketprice`) SELECT {$recordid},5,`salesorderproductsrelname`,`servicecontractsid`,`productid`,`productcomboid`,`producttype`,`amountproduct`,`marketprice`,`realmarketprice`,`costing`,`realcosting`,`productform`,`auditorid`,`audittime`,`createtime`,`creatorid`,`salesorderproductsrelstatus`,`remark`,`relatorids`,`ownerid`,`backerid`,`backtime`,`backwhy`,`servicecount`,`serviceamount`,`schedule`,`starttime`,`endtime`,`modifiedtime`,`isvisible`,`nodestatus`,`finishstatus`,`accountid`,`purchasemount`,`ip`,`domain`,`space`,`producttext`,`productsolution`,`TsiteNew`,`Tsite`,`productnumber`,`standard`,`thepackage`,`isextra`,`agelife`,`prealprice`,`punit_price`,`pmarketprice` FROM vtiger_salesorderproductsrel WHERE salesorderproductsrelid={$value}";
                    $this->db->pquery($sql,array());
                    $newid=$this->db->getLastInsertID();
                    $salesorderproductstr.="({$recordid},{$insertsalesorderproduct[$value]},{$value},{$newid},'SalesOrder'),";
                }
            }
            if(!empty($insertnewinvoiceid)){
                foreach($insertnewinvoiceid as $value){
                    $salesorderproductstr.="({$recordid},{$insertinvoiceproduct[$value]},{$value},NULL,'Invoice'),";
                }
            }
            $salesorderproductstr=rtrim($salesorderproductstr,',');
            if($salesorderproductstr!=''){
                $sql="INSERT INTO vtiger_orderchargeproducts(orderchargebackid,oldorderid,oldproductid,newproductid,setype) VALUES{$salesorderproductstr}";
                $this->db->pquery($sql,array());
            }
        }
        $checkarray=array();
        $result = $this->db->pquery("SELECT vtiger_crmentity.smcreatorid, vtiger_products.productname,vtiger_products.productid,vtiger_products.productman FROM `vtiger_salesorderproductsrel` LEFT JOIN vtiger_products ON vtiger_products.productid = vtiger_salesorderproductsrel.productid LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_salesorderproductsrel.productid WHERE salesorderid =? ",array($recordid));
        while($product=$this->db->fetch_row($result)){
            $checkarray[]=array('workflowstagesname'=> $product['productname'].'审核','smcreatorid'=>0,'productid'=>$product['productid'],'productman'=>$product['productman']);
        }
        vglobal('checkproducts',$checkarray);
	}

    /**
     * 工作流生成模块
     * 	1.合同，那么需要生成默认产品审批流
     * 	2.报价单或者拜访单，则生成流程即可
     * 	3.工单，合同，生成流程，更新产品工单id
     * 			非合同，生成流程即可
     * @history
     * 	1.2014-12-28 生成第一个节点的数据，多余节点数据不生成
     * @param unknown $modulename
     * @param unknown $workflowsid
     * @param unknown $salesorderid
     */
    function makeWorkflows($modulename,$workflowsid,$salesorderid,$isedit=''){
        global $adb,$current_user,$isallow;
        $workflowsisedit=false;//流程是否可以修改

        $wfResoult=array();
        $isinsert= true;
        if(empty($modulename)){
            $modulename='OrderChargeback';
        }
        if(!in_array($modulename, $isallow)||$modulename=='Products'){
            return;
        }
        //工作流id防止为空
        if($workflowsid>0){
            $wfResoult = $this->db->pquery("select workflowsid from vtiger_workflows where workflowsid =? and mountmodule =?",array($workflowsid,$modulename));
            if(empty($wfResoult)){
                $wfResoult = $this->db->pquery("select workflowsid from vtiger_workflows where mountmodule =? limit 1",array($modulename));
            }
        }else{
            $wfResoult = $this->db->pquery("select workflowsid from vtiger_workflows where mountmodule =? limit 1",array($modulename));
        }
        $workflowsid=$this->db->query_result($wfResoult,0,'workflowsid');

        //删除已经存在的阶段
        if($isedit){
            $sql="select workflowsid,SUM(if(isaction=2,1,0)) as isaction from vtiger_salesorderworkflowstages where salesorderid=? and modulename=?";
            $result=$this->db->pquery($sql,array($salesorderid,$modulename));
            if($this->db->num_rows($result)){
                $isaction=$this->db->query_result($result,0,'isaction');
                if($isaction>1){
                    return ;  //如果有审核的节点，那么不重新生成，也就是在流程中间的设置
                }else{
                    $sql="delete from vtiger_salesorderworkflowstages where salesorderid=? and modulename=?";
                    $this->db->pquery($sql,array($salesorderid,$modulename));
                }
            }
        }
        $user=Users_Privileges_Model::getInstanceById($current_user->id);
        $serviceid= 0;
        $servicename='';
        $servicecontractsid=0;
        $companyCode=$this->getContractsCompanyCode2($modulename,$salesorderid);
        if($isinsert){
            //@TODO 前置条件，为扩展功能，如何触发
            //插入数据第一个节点
            $sql="SELECT * FROM vtiger_workflowstages WHERE workflowsid =? ORDER BY sequence ASC";
            $wresult=$this->db->pquery($sql,array($workflowsid));
            $nextparentid=0;
            $countrows=$this->db->num_rows($wresult);
            $sequence=0;
            if($countrows){
                //$subworkflowsid=0;
                $isaction=1;
                $workflowsid=0;
                $actiontime=date('Y-m-d H:i:s');
                $servicecontractsid_display = $_REQUEST['servicecontractsid_display'];
                $accountid=$_REQUEST['accountid'];
                $accountname = $_REQUEST['accountid_display'];
                /*$orderchargeback_no_SQL=" SELECT  vtiger_orderchargeback.orderchargeback_no FROM  vtiger_orderchargeback  WHERE vtiger_orderchargeback.orderchargebackid = ? LIMIT 1 ";
                $orderchargeback_no_result=$this->db->pquery($orderchargeback_no_SQL,array($salesorderid));
                $orderchargeback_no=$this->db->query_result($orderchargeback_no_result,0,'orderchargeback_no');*/ /*vtiger_salesorderworkflowstages.modulestatus=,*/
                //$sqlsub="INSERT INTO vtiger_salesorderworkflowstages (salesorder_nono,workflowstagesname,workflowstagesid,sequence,salesorderid,isaction,actiontime,addtime,workflowsid,modulename,smcreatorid,createdtime,productid,departmentid,ishigher,higherid) values ({$orderchargeback_no},?,?,?,?,?,?, NOW(),?,?,?,NOW(),?,?,?,?)";
                $sqlsub="INSERT INTO vtiger_salesorderworkflowstages (salesorder_nono,modulestatus,accountid,accountname,workflowstagesname,workflowstagesid,sequence,salesorderid,isaction,actiontime,addtime,workflowsid,modulename,smcreatorid,createdtime,productid,departmentid,ishigher,higherid,workflowstagesflag,handleaction,companycode) values (?,'p_process',?,?,?,?,?,?,?,?, NOW(),?,?,?,NOW(),?,?,?,?,?,?,'{$companyCode}')";
                $i=0;
                while($row=$this->db->fetch_array($wresult)){
                    //第一个节点激活
                    /*if(0==$i&&1==$isaction&&$current_user->id!=1){
                        $reportsModel = $current_user;
                        function findreport($reportsModel,&$reports_to_id=array()){
                            $reports_to_id[]=$reportsModel->reports_to_id;
                            $reportsModel = Users_Privileges_Model::getInstanceById($reportsModel->reports_to_id);
                            if($reportsModel->reports_to_id !=='38'){
                                findreport($reportsModel,$reports_to_id);
                            }
                            return $reports_to_id;
                        }
                        $report_arr = findreport($reportsModel);
                        $count_user = count($report_arr);
                        $user_sql = "SELECT IFNULL(( SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE id = ?),'--') AS name,(SELECT vtiger_role.rolename FROM vtiger_role LEFT JOIN vtiger_user2role ON vtiger_role.roleid=vtiger_user2role.roleid WHERE vtiger_user2role.userid=?) AS rolename";
                        if($count_user){
                            foreach($report_arr as $v){
                                if($i>0){
                                    $isaction=0;
                                }
                                $i++;
                                $user_data = $this->db->pquery($user_sql,array($v,$v));
                                $user_name = $this->db->query_result($user_data,0,'name');
                                $role_name = $this->db->query_result($user_data,0,'rolename');
                                if($role_name=='商务'||$role_name=='客服'){
                                    $role_name='提单人上级';
                                }
                                $this->db->pquery($sqlsub,array('['.$role_name.']-'.$user_name.'审核',0,$i,$salesorderid,$isaction,$actiontime,$row['workflowsid'],$modulename,$current_user->id,0,$user->get('current_user_parent_departments'),1,$v));
                            }
                        }
                        $isaction=0;
                    }*/
                    if($row['sequence']==15){
                        if($_REQUEST['refundamount']>20000&&$_REQUEST['refundamount']<=100000){
                            $this->db->pquery($sqlsub,array($servicecontractsid_display,$accountid,$accountname,'财务副总裁',0,$sequence+1,$salesorderid,$isaction,$actiontime,$workflowsid,$modulename,$current_user->id,0,$user->get('current_user_parent_departments'),1,3075,$row['workflowstagesflag'],$row['handleaction']));
                        }elseif($_REQUEST['refundamount']>100000){
                            $this->db->pquery($sqlsub,array($servicecontractsid_display,$accountid,$accountname,'财务副总裁',0,$sequence+1,$salesorderid,$isaction,$actiontime,$workflowsid,$modulename,$current_user->id,0,$user->get('current_user_parent_departments'),1,3075,$row['workflowstagesflag'],$row['handleaction']));
                            $this->db->pquery($sqlsub,array($servicecontractsid_display,$accountid,$accountname,'总裁审核',0,$sequence+2,$salesorderid,$isaction,$actiontime,$workflowsid,$modulename,$current_user->id,0,$user->get('current_user_parent_departments'),1,38,$row['workflowstagesflag'],$row['handleaction']));
                        }
                    }
                    if($row['workflowstagesflag']=='BUSINESS_DIRECTOR'){
                        $query='SELECT vtiger_crmentity.smownerid FROM vtiger_crmentity LEFT JOIN vtiger_orderchargeback ON vtiger_orderchargeback.accountid=vtiger_crmentity.crmid WHERE vtiger_orderchargeback.orderchargebackid=? limit 1';
                        $report_ID=$this->db->pquery($query,array($salesorderid));
                        if($this->db->num_rows($report_ID)){
                            $reprot_to_ids=$this->db->query_result($report_ID,0);
                            function findreport($reportsModel,&$reports_to_id=array()){
                                $reports_to_id[]=$reportsModel->reports_to_id;
                                $reportsModel = Users_Privileges_Model::getInstanceById($reportsModel->reports_to_id);
                                if($reportsModel->roleid!='H79'&&$reportsModel->reports_to_id !=='38'){
                                    findreport($reportsModel,$reports_to_id);
                                }
                                return $reports_to_id;
                            }
                            $reprot_to_ida=findreport( Users_Privileges_Model::getInstanceById($reprot_to_ids));
                            $reprot_to_id_last=end($reprot_to_ida);
                            $this->db->pquery($sqlsub,array($servicecontractsid_display,$accountid,$accountname,$row['workflowstagesname'],$row['workflowstagesid'],$row['sequence']+$i,$salesorderid,$isaction,$actiontime,$row['workflowsid'],$modulename,$current_user->id,0,$user->get('current_user_parent_departments'),1,$reprot_to_id_last,$row['workflowstagesflag'],$row['handleaction']));
                        }
                    }elseif($row['handleaction']=='ProductCheck' && $row['workflowstagesflag']=='USER_CHECKED') {
                        $thisQuery='SELECT vtiger_servicecontracts.* FROM vtiger_orderchargeback LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_orderchargeback.servicecontractsid WHERE vtiger_orderchargeback.orderchargebackid=?';
                        $thisResult=$adb->pquery($thisQuery,array($salesorderid));
                        if($adb->num_rows($thisResult)) {
                            $thisproductid=0;
                            $thisparent_contracttypeid=$thisResult->fields['parent_contracttypeid'];
                            $thiscontract_type=$thisResult->fields['contract_type'];
                            $thiscontract_no=$thisResult->fields['contract_no'];
                            if(stripos($thiscontract_no,'VRZ')!==false || stripos($thiscontract_no,'ZHXT')!==false){
                                $thisproductid=2722416;//百度V
                            }elseif($thisparent_contracttypeid==2){
                                $thisproductid=2622225;//线上
                            }elseif($thisparent_contracttypeid==1 || in_array($thiscontract_type,array('TSITE合同','TSITE响应式合同','TSITE新增协议','TSITE标准合同','TSITE续费合同','定制网站'))){
                                $thisproductid=2622264;//线上
                            }
                            if($thisproductid>0) {
                                $this->db->pquery($sqlsub, array($servicecontractsid_display, $accountid, $accountname, '执行部门退单成本核算业务部门审核', $row['workflowstagesid'], $row['sequence'] + $i, $salesorderid, $isaction, $actiontime, $row['workflowsid'], $modulename, $current_user->id, $thisproductid, $user->get('current_user_parent_departments'), 0, 0, $row['workflowstagesflag'],$row['handleaction']));
                            }
                        }
                    }elseif($row['handleaction']=='ProductCheck'){
                        global $checkproducts;
                        if(is_array($checkproducts)){
                            foreach ($checkproducts as $productinfo){//$current_user->id为当前人，审核，产品部门的审核变化了。
                                $this->db->pquery($sqlsub,array($servicecontractsid_display,$accountid,$accountname,str_replace('审核','',$row['workflowstagesname']).$productinfo['workflowstagesname'],$row['workflowstagesid'],$row['sequence']+$i,$salesorderid,$isaction,$actiontime,$row['workflowsid'],$modulename,$current_user->id,$productinfo['productid'],$user->get('current_user_parent_departments'),0,0,$row['workflowstagesflag'],$row['handleaction']));
                            }
                        }
                    }elseif($row['handleaction']=='MyCheck'){//自己审核
                        $this->db->pquery($sqlsub,array($servicecontractsid_display,$accountid,$accountname,'提单人确认',$row['workflowstagesid'],$row['sequence']+$i,$salesorderid,$isaction,$actiontime,$row['workflowsid'],$modulename,$current_user->id,0,$user->get('current_user_parent_departments'),1,$current_user->id,$row['workflowstagesflag'],$row['handleaction']));
                    }elseif($row['handleaction']=='UpCheck'){
                        //过滤掉某些角色,比如商务主管
                        $reports_to_id=$current_user->reports_to_id;
                        $reportsModel = Users_Privileges_Model::getInstanceById($reports_to_id);
                        if($reportsModel->current_user_roles=='H81'){
                            $reports_to_id=$reportsModel->reports_to_id;
                        }
                        $this->db->pquery($sqlsub,array($servicecontractsid_display,$accountid,$accountname,'提单人上级审批',$row['workflowstagesid'],$row['sequence']+$i,$salesorderid,$isaction,$actiontime,$row['workflowsid'],$modulename,$current_user->id,0,$user->get('current_user_parent_departments'),1,$reports_to_id,$row['workflowstagesflag'],$row['handleaction']));
                    }elseif($row['handleaction']=='ServiceCheck'){
                        //wangbin 20150930 只有中小商务的工单工作流才有客服审核
                        $if_middle_small_sal = "SELECT ff.parentdepartment FROM vtiger_salesorder aa LEFT JOIN vtiger_servicecontracts bb ON aa.servicecontractsid = bb.servicecontractsid LEFT JOIN vtiger_crmentity cc ON bb.sc_related_to = cc.crmid LEFT JOIN vtiger_user2department ee ON ee.userid = cc.smownerid LEFT JOIN vtiger_departments ff ON ff.departmentid = ee.departmentid WHERE aa.salesorderid = '?' AND ff.parentdepartment LIKE 'H1::H2::H3%'";
                        $if_zhongxiao = $this->db->pquery($if_middle_small_sal,array($salesorderid));
                        $if_zhongxiao_rows=$this->db->num_rows($if_zhongxiao);
                        if($if_zhongxiao_rows>0){
                            //young 20150519 加入客服角色的审核,条件是工单客户含有客服
                            if($serviceid>0){ //如果以及分配客服

                                $this->db->pquery($sqlsub,array($servicecontractsid_display,$accountid,$accountname,'客服'.$servicename.'审核',0,$row['sequence']+$i,$salesorderid,$isaction,$actiontime,$row['workflowsid'],$modulename,$current_user->id,0,$user->get('current_user_parent_departments'),1,$serviceid,$row['workflowstagesflag'],$row['handleaction']));
                            }else { //如果未分配客服
                                $this->db->pquery($sqlsub, array($servicecontractsid_display,$accountid,$accountname,'客服经理分配客服', $row['workflowstagesid']+$i, $row['sequence'], $salesorderid, $isaction, $actiontime, $row['workflowsid'], $modulename, $current_user->id, 0, $user->get('current_user_parent_departments'), 0, 0,$row['workflowstagesflag'],$row['handleaction']));
                            }
                        }
                    }elseif($row['handleaction']=='REVIEWER'){
                        $this->db->pquery($sqlsub,array($row['workflowstagesname'],$row['workflowstagesid'],$row['sequence'],$salesorderid,$isaction,$actiontime,$row['workflowsid'],$modulename,$current_user->id,0,$user->get('current_user_parent_departments'),1,$row['reviewer'],$row['workflowstagesflag'],$row['handleaction']));
                    }elseif($row['handleaction']=='NextCheck'){ //下个节点指定审核人
                        $this->db->pquery($sqlsub,array($servicecontractsid_display,$accountid,$accountname,'指定下个节点审核人',$row['workflowstagesid'],$row['sequence']+$i,$salesorderid,$isaction,$actiontime,$row['workflowsid'],$modulename,$current_user->id,0,$user->get('current_user_parent_departments'),0,0,$row['workflowstagesflag'],$row['handleaction']));
                    }elseif($row['handleaction']=='maincompany'){//主体公司审核
                        if($row['workflowstagesflag']=='gs'){
                            //出纳变化处理
                            $sql="select userid from vtiger_invoicecompanyuser where invoicecompany=? and modulename='gs'";
                            $result=$this->db->pquery($sql,array($companyCode));
                            $userid=$adb->query_result($result,0,'userid');
                            $this->db->pquery($sqlsub,array($servicecontractsid_display,$accountid,$accountname,$row['workflowstagesname'],$row['workflowstagesid'],$row['sequence']+$i,$salesorderid,$isaction,$actiontime,$row['workflowsid'],$modulename,$current_user->id,0,$user->get('current_user_parent_departments'),1,$userid,$row['workflowstagesflag'],$row['handleaction']));
                        }else{
                            $maincompanyResult=$this->db->pquery("SELECT userid FROM vtiger_auditinvoicecompany WHERE companycode=? AND workflowstagesflag=?",array($companyCode,$row['workflowstagesflag']));
                            if($this->db->num_rows($maincompanyResult) && $maincompanyResult->fields['userid']>0){
                                $maincompanyResultuserid=$maincompanyResult->fields['userid'];
                                $this->db->pquery($sqlsub,array($servicecontractsid_display,$accountid,$accountname,$row['workflowstagesname'],$row['workflowstagesid'],$row['sequence']+$i,$salesorderid,$isaction,$actiontime,$row['workflowsid'],$modulename,$current_user->id,0,$user->get('current_user_parent_departments'),1,$maincompanyResultuserid,$row['workflowstagesflag'],$row['handleaction']));
                            }else{
                                $this->db->pquery($sqlsub,array($servicecontractsid_display,$accountid,$accountname,$row['workflowstagesname'],$row['workflowstagesid'],$row['sequence']+$i,$salesorderid,$isaction,$actiontime,$row['workflowsid'],$modulename,$current_user->id,0,$user->get('current_user_parent_departments'),0,0,$row['workflowstagesflag'],$row['handleaction']));
                            }
                        }
                    }else{ //默认情况
                        if($row['workflowstagesflag']=='CWSH'||in_array($row['workflowstagesflag'],array('TREASURER_CODE','TREASURER_TWO'))){//财务运营经理、财务主管审核
                            $userId=$this->getDepartmentById($modulename,$salesorderid,$row['workflowstagesname']);
                            Newinvoice_Record_Model::recordLog(array($userId,$row['workflowstagesname'],$modulename,$salesorderid),'orderBack');
                            if($userId){
                                $this->db->pquery($sqlsub,array($servicecontractsid_display,$accountid,$accountname,$row['workflowstagesname'],$row['workflowstagesid'],$row['sequence']+$i,$salesorderid,$isaction,$actiontime,$row['workflowsid'],$modulename,$current_user->id,0,$user->get('current_user_parent_departments'),1,$userId,$row['workflowstagesflag'],$row['handleaction']));
                            }else{
                                $this->db->pquery($sqlsub,array($servicecontractsid_display,$accountid,$accountname,$row['workflowstagesname'],$row['workflowstagesid'],$row['sequence']+$i,$salesorderid,$isaction,$actiontime,$row['workflowsid'],$modulename,$current_user->id,0,$user->get('current_user_parent_departments'),0,0,$row['workflowstagesflag'],$row['handleaction']));
                            }
                        }else{
                            Newinvoice_Record_Model::recordLog(array($row['workflowstagesflag'],$row['workflowstagesname'],$modulename,$salesorderid),'orderBack');
                            $this->db->pquery($sqlsub,array($servicecontractsid_display,$accountid,$accountname,$row['workflowstagesname'],$row['workflowstagesid'],$row['sequence']+$i,$salesorderid,$isaction,$actiontime,$row['workflowsid'],$modulename,$current_user->id,0,$user->get('current_user_parent_departments'),0,0,$row['workflowstagesflag'],$row['handleaction']));
                        }
                    }
                    $sequence=$row['sequence']+$i;
                    //$subworkflowsid=$row['subworkflowsid'];
                    $isaction=0;
                    $actiontime='';
                    $workflowsid=$row['workflowsid'];
                }
            }
        }
        //$departmentid=!empty($_SESSION['userdepartmentid'])?$_SESSION['userdepartmentid']:$current_user->departmentid;
        $departmentid=getUserInfo($current_user->id);
        $this->setAudituid('ContractsAuditset',$departmentid,$salesorderid,$workflowsid);
        /*$sRecordModel=Vtiger_Record_Model::getCleanInstance('ServiceContracts');
        //5.6.1财务复审指定人
        if(in_array($companyCode,$sRecordModel->financialReviewNode1)){
            $userid=15542;//袁蔚华
        }elseif(in_array($companyCode,$sRecordModel->financialReviewNode2)){
            $userid=25067;//王娟娟
        }elseif(in_array($companyCode,$sRecordModel->financialReviewNode3)){
            $userid=22306;//孟昭燕
        }elseif(in_array($companyCode,$sRecordModel->financialReviewNode4)){
            $userid=11505;//刘媛媛
        }elseif(in_array($companyCode,$sRecordModel->financialReviewNode5)){
            $userid=9726;//刘笑含
        }elseif(in_array($companyCode,$sRecordModel->financialReviewNode6)){
            $userid=25135;//林旺
        }elseif(in_array($companyCode,$sRecordModel->financialReviewNode7)){
            $userid=17630;//顾瑞娟
        }
        $this->db->pquery("UPDATE vtiger_salesorderworkflowstages SET ishigher=1,higherid=? WHERE salesorderid=? AND workflowstagesflag='TREASURER_CODE' and workflowstagesname='财务主管复核'" ,array($userid,$salesorderid));*/
        $this->deleteWorkflow($salesorderid);
        //新建时 消息提醒第一审核人进行审核
        $object = new SalesorderWorkflowStages_SaveAjax_Action();
        $object->sendWxRemind(array('salesorderid'=>$salesorderid,'salesorderworkflowstagesid'=>0));
    }

    /**
     * 获得最近的一个部门id
     * @param $modulename
     * @param $salesorderid
     * @return string
     */
    public function getDepartmentById($modulename,$salesorderid,$workflowstagesname){
        Newinvoice_Record_Model::recordLog(array('参数',$modulename,$salesorderid,$workflowstagesname),'orderBack');
        global $adb;
        $modulenameArray=array(
            'ContractsAgreement',
            'SuppContractsAgreement',
            'ServiceContracts',
            'OrderChargeback',
            'RefillApplication',
            'SupplierContracts',
            'RefundTimeoutAudit',
            'CustomerStatement',
            'SupplierStatement'
        );
        if(!in_array($modulename,$modulenameArray)){
            return '';
        }
        $sql="SELECT
	dd.departmentid
FROM
	vtiger_departments dd 
WHERE
	FIND_IN_SET( dd.departmentid, REPLACE ( ( SELECT parentdepartment FROM vtiger_departments WHERE departmentid = (SELECT vtiger_user2department.departmentid FROM vtiger_crmentity LEFT JOIN vtiger_user2department on vtiger_user2department.userid=vtiger_crmentity.smownerid WHERE vtiger_crmentity.crmid=?) ), '::', ',' ) ) 
	order by depth desc";
        $result=$adb->pquery($sql,array($salesorderid));
        if($this->db->num_rows($result)>0){
            while($row=$adb->fetch_array($result)){
                $data[]=$row['departmentid'];
            }
            $sql="select * from vtiger_auditCWSH where department in ('".implode('\',\'',$data)."') order by FIELD(department, '".implode('\',\'',$data)."')";
            $result=$adb->pquery($sql,array());
            if($this->db->num_rows($result)>0){
                if(strpos($workflowstagesname, '财务主管审核') !== false){
                    //主管
                    return $adb->query_result($result,0,'supervisor');
                }else if(strpos($workflowstagesname, '财务运营经理') !== false){
                    //经理
                    return $adb->query_result($result,0,'manager');
                }
            }else{
                Newinvoice_Record_Model::recordLog(array('没查到审核人',$sql),'orderBack');
                return '';
            }
        }else{
            Newinvoice_Record_Model::recordLog(array('没查到公司部门',$sql),'orderBack');
            return '';
        }
    }

    /**
     * 退款金额为0,处理结果是转业务删除出纳退款和财务主管复核两项流程
     * @param $salesorderid
     * @throws Exception
     */
    public function deleteWorkflow($salesorderid){
        $sql="select * from vtiger_orderchargeback where orderchargebackid=?";
        $sel_result = $this->db->pquery($sql, array($salesorderid));
        $res_cnt = $this->db->num_rows($sel_result);
        if ($res_cnt > 0) {
            $row = $this->db->query_result_rowdata($sel_result, 0);
            if($row['refundamount']==0&&$row['processingresult']=='transferservice'){
                //退款金额为0,处理结果是转业务把最后两行流程删了
                $sql="delete from vtiger_salesorderworkflowstages where salesorderid=? AND (workflowstagesname=? or workflowstagesname=?) ";
                $this->db->pquery($sql, array($salesorderid,'出纳退款','财务主管复核'));
            }
        }
    }

    /**
     * 退款申请单这申核后置事件
     * @param $request
     */
    public function workflowcheckafter(Vtiger_Request $request){
        $recordid=$request->get('record');
        $recordModel = Vtiger_Record_Model::getInstanceById($recordid, 'OrderChargeback',true);
        $entity=$recordModel->entity->column_fields;
        if($entity['servicecontractsid']>0 && $entity['modulestatus']=='c_complete' && !in_array($entity['refundreason'],$this->refundreasondoing)){
            if($entity['refundreason']=='退款终止业务'){
                //状态改为退款
                $this->db->pquery("UPDATE vtiger_servicecontracts SET modulestatus='c_refund' WHERE servicecontractsid=?",array($entity['servicecontractsid']));
            }else if($entity['refundreason']=='转业务'){
                //状态改为转业务
                $this->db->pquery("UPDATE vtiger_servicecontracts SET modulestatus='c_tranbusiness' WHERE servicecontractsid=?",array($entity['servicecontractsid']));
            }else{
                $this->db->pquery("UPDATE vtiger_servicecontracts SET modulestatus='c_tovoid' WHERE servicecontractsid=?",array($entity['servicecontractsid']));
            }
            $this->db->pquery("UPDATE vtiger_salesorder SET modulestatus='c_refundclosure' WHERE salesorderid in(SELECT vtiger_orderchargeproducts.oldorderid FROM vtiger_orderchargeproducts WHERE setype='SalesOrder' AND vtiger_orderchargeproducts.orderchargebackid=? GROUP BY vtiger_orderchargeproducts.oldorderid)",array($recordid));
            $this->db->pquery("UPDATE vtiger_salesorderworkflowstages SET isaction=2 WHERE modulename='SalesOrder' AND salesorderid in(SELECT vtiger_orderchargeproducts.oldorderid FROM vtiger_orderchargeproducts WHERE setype='SalesOrder' AND vtiger_orderchargeproducts.orderchargebackid=? GROUP BY vtiger_orderchargeproducts.oldorderid)",array($recordid));
        }
        $stagerecordid=$request->get('stagerecordid');
        $query="SELECT
                    vtiger_salesorderworkflowstages.workflowsid,vtiger_workflowstages.workflowstagesflag,vtiger_salesorderworkflowstages.productid,vtiger_salesorderworkflowstages.auditorid
                FROM
                    `vtiger_salesorderworkflowstages`
                LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid
                WHERE
                    vtiger_salesorderworkflowstages.salesorderworkflowstagesid = ?
                AND vtiger_salesorderworkflowstages.modulename = 'OrderChargeback'";
        $result=$this->db->pquery($query,array($stagerecordid));
        $currentflag=$this->db->query_result($result,0,'workflowstagesflag');
        $productid=$this->db->query_result($result,0,'productid');
        $auditorid=$this->db->query_result($result,0,'auditorid');
        $workflowsid=$this->db->query_result($result,0,'workflowsid');
        switch($currentflag){
            //计算已执行的成本将设置要处理的发票
            case 'COST_ACCOUNTING':
                $query="UPDATE vtiger_orderchargeback SET executedcost=(SELECT sum(IFNULL(vtiger_salesorderproductsrel.costing,0)+IFNULL(vtiger_salesorderproductsrel.purchasemount,0)) FROM vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.salesorderid=vtiger_orderchargeback.orderchargebackid AND vtiger_salesorderproductsrel.multistatus=5) WHERE vtiger_orderchargeback.orderchargebackid=?";
                $this->db->pquery($query,array($recordid));
                //设置要处理的发票
                $query="UPDATE vtiger_invoiceextend SET processstatus=if(processstatus=0,1,processstatus) WHERE vtiger_invoiceextend.invoiceextendid IN(SELECT vtiger_orderchargeproducts.oldproductid FROM vtiger_orderchargeproducts WHERE vtiger_orderchargeproducts.setype='Invoice' AND vtiger_orderchargeproducts.orderchargebackid=?)";
                $this->db->pquery($query,array($recordid));
                break;
            case 'PRODUCT_SUPERIOR_CONFIRM':
                $reportsModel = Users_Privileges_Model::getInstanceById($auditorid);
                $reports_to_id=$reportsModel->reports_to_id;
                $query="UPDATE vtiger_salesorderworkflowstages SET ishigher=1,higherid=? WHERE vtiger_salesorderworkflowstages.modulename='OrderChargeback' AND vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.productid=? AND vtiger_salesorderworkflowstages.salesorderworkflowstagesid!=?";
                $this->db->pquery($query,array($reports_to_id,$recordid,$productid,$stagerecordid));
                break;
            case 'AUDIT_VERIFICATION':
                //第二个节点指定审核人
                $this->AuditAuditNodeJump($recordid,$recordModel->get('workflowsid'),$recordModel->get('assigned_user_id'),'ContractsAuditset','OrderChargeback',1);
                break;
            case 'TWO_VERIFICATION':
                //第二个节点指定审核人
                $this->AuditAuditNodeJump($recordid,$recordModel->get('workflowsid'),$recordModel->get('assigned_user_id'),'ContractsAuditset','OrderChargeback',2);
                break;
            default :
                break;
        }

        //臻信通（高级词）退款申请审核完成后，发送一条消息推送
        if($entity['modulestatus']=='c_complete'){
            $contract_info = $this->db->pquery("select contract_no from vtiger_servicecontracts WHERE servicecontractsid=?",array($entity['servicecontractsid']));
            $contract_no=$this->db->query_result($contract_info,0,'contract_no');
            
            global $tyunweburl,$sault;
            $url = $tyunweburl.'api/micro/tcloud-workflow/v1.0.0/advancedWords/voidNotice?contractCode='.$contract_no;
            $time=time().'123';
            $token=md5($time.$sault);
            $header = array(
                    "Content-Type:application/json",
                    "S-Request-Token:".$token,
                    "S-Request-Time:".$time
                );
            $recordModel->https_requestcomm($url,NULL,array(CURLOPT_HTTPHEADER=>$header));
            
            
        }
        // cxh 2019-08-02 添加 如果该审核需要修改审核列表中的modulestatus（审核流程状态）审核完后走下面代码
        $params['salesorderid']=$request->get('record');
        $params['workflowsid']=$workflowsid;
        $this->hasAllAuditorsChecked($params);
    }

    /**
     * 前置事件
     * @param Vtiger_Request $request
     */
    public function workflowcheckbefore(Vtiger_Request $request){
        $recordid=$request->get('record');
        $stagerecordid=$request->get('stagerecordid');
        $query="SELECT
                    vtiger_workflowstages.workflowstagesflag
                FROM
                    `vtiger_salesorderworkflowstages`
                LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid
                WHERE
                    vtiger_salesorderworkflowstages.salesorderworkflowstagesid = ?
                AND vtiger_salesorderworkflowstages.modulename = 'OrderChargeback'";
        $result=$this->db->pquery($query,array($stagerecordid));
        $currentflag=$this->db->query_result($result,0,'workflowstagesflag');
        switch($currentflag){
            //发票处理判断
            case 'INVOICE_CHECKED':
                $query="SELECT 1 FROM vtiger_invoiceextend LEFT JOIN vtiger_orderchargeproducts ON (vtiger_orderchargeproducts.oldorderid=vtiger_invoiceextend.invoiceid AND vtiger_orderchargeproducts.oldproductid=vtiger_invoiceextend.invoiceextendid) WHERE vtiger_orderchargeproducts.setype='Invoice' AND vtiger_invoiceextend.processstatus=1 AND vtiger_orderchargeproducts.orderchargebackid=?";
                $result=$this->db->pquery($query,array($recordid));
                $invoicenum=$this->db->num_rows($result);
                if($invoicenum>0){
                    $resultaa['success'] = 'false';
                    $resultaa['error']['message'] = ":请先将发票处理后再进行一步的操作!";
                    //若果是移动端请求则走这个返回
                    if( $request->get('isMobileCheck')==1){
                        return $resultaa;
                    }else{
                        echo json_encode($resultaa);
                        exit;
                    }
                }
                break;
            default :
                break;
        }
        $query='SELECT 1 FROM vtiger_salesorderworkflowstages WHERE salesorderid=? AND salesorderworkflowstagesid>?';
        $thisResult=$this->db->pquery($query,array($recordid,$stagerecordid));
        //状态检查
        $queryFlag = 'SELECT workflowstagesflag FROM vtiger_salesorderworkflowstages WHERE salesorderid=? AND salesorderworkflowstagesid>?';
        $flagResult = $this->db->pquery($queryFlag,array($recordid,$stagerecordid));
        $workflowstagesflag = $this->db->query_result($flagResult, 0, 'workflowstagesflag');

        if(!$this->db->num_rows($thisResult) || $workflowstagesflag == 'USER_CHECKED'){
            $recordModel = Vtiger_Record_Model::getInstanceById($recordid, 'OrderChargeback',true);
            if(!in_array($recordModel->get('refundreason'),$this->refundreasondoing) && $recordModel->get('servicecontractsid')>0){
                $returnData=$this->cancel71360Order($recordModel);
                if(!$returnData['success']){
                    if($request->get('isMobileCheck')==1){
                        return $returnData;
                    }else{
                        echo json_encode($returnData);
                        exit;
                    }
                }
            }
        }
    }
    public function backallAfter(Vtiger_Request $request){
        $recordid=$request->get('record');
        $stagerecordid=$request->get('isrejectid');
        $query="SELECT
                    vtiger_workflowstages.workflowstagesflag,
                    vtiger_workflowstages.workflowsid
                FROM
                    `vtiger_salesorderworkflowstages`
                LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid
                WHERE
                    vtiger_salesorderworkflowstages.salesorderworkflowstagesid = ?
                AND vtiger_salesorderworkflowstages.modulename = 'OrderChargeback'";
        $result=$this->db->pquery($query,array($stagerecordid));
        $workflowsid=$this->db->query_result($result,0,'workflowsid');
        $this->db->pquery("UPDATE vtiger_orderchargeback SET modulestatus='a_normal' WHERE orderchargebackid=?",array($recordid));
        $this->db->pquery("DELETE FROM `vtiger_salesorderworkflowstages` WHERE vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename='OrderChargeback' AND vtiger_salesorderworkflowstages.workflowsid=?",array($recordid,$workflowsid));
    }
    public function getContractsCompanyCode($modulename,$salesorderid){
        try{
            global $adb,$current_user;
            $recordModel=Vtiger_Record_Model::getInstanceById($salesorderid,'OrderChargeback',true);
            $contractsid=$recordModel->get('servicecontractsid');
            if($contractsid){
                $query='SELECT if(vtiger_servicecontracts.companycode IS NOT null AND vtiger_servicecontracts.companycode!=\'\',vtiger_servicecontracts.companycode,vtiger_suppliercontracts.companycode) AS companycode FROM 
                vtiger_crmentity
                LEFT JOIN vtiger_servicecontracts ON (vtiger_servicecontracts.servicecontractsid=vtiger_crmentity.crmid AND vtiger_crmentity.setype=\'ServiceContracts\') 
                LEFT JOIN vtiger_suppliercontracts ON (vtiger_suppliercontracts.suppliercontractsid=vtiger_crmentity.crmid AND vtiger_crmentity.setype=\'SupplierContracts\')
                WHERE vtiger_crmentity.deleted=0 AND vtiger_crmentity.crmid=? limit 1';
                $result=$adb->pquery($query,array($contractsid));
                if($adb->num_rows($result)){
                    $data=$adb->raw_query_result_rowdata($result,0);
                    return $data['companycode'];
                }
            }else{
                if(!empty($current_user->companycode)){
                    return $current_user->companycode;
                }
                $current_user->companyid;
                $query='SELECT * FROM `vtiger_invoicecompany` WHERE invoicecompanyid=?';
                $result=$adb->pquery($query,array($current_user->companyid));
                if($adb->num_rows($result)){
                    return $result->feilds['companycode'];
                }
            }
        }catch(Exception $e){

        }
        return '';
    }

    /**
     * 5.58新增需求
     * @param $modulename
     * @param $salesorderid
     * @return int|mixed|string|string[]|null
     * @throws Exception
     */
    public function getContractsCompanyCode2($modulename,$salesorderid){
        global $adb;
        $recordModel=Vtiger_Record_Model::getInstanceById($salesorderid,'OrderChargeback',true);
        $invoicecompany=$recordModel->get('invoicecompany');
        $sql="select companycode from  vtiger_invoicecompany where invoicecompany =?";
        $result=$adb->pquery($sql,array($invoicecompany));
        return $adb->query_result($result,0,'companycode');
    }

    /**
     * 退款通知71360平台
     * @param $recordModel
     * @return mixed
     * @throws Exception
     */
    public function cancel71360Order($recordModel){
        global $adb;
        $actiationCodeResult = $adb->pquery("select usercodeid,usercode,contractname from vtiger_activationcode where contractid=? AND status in(0,1)",array($recordModel->get('servicecontractsid')));
        if(!$adb->num_rows($actiationCodeResult)){
            $resultaa['success']=true;
            return $resultaa;
        }
        $activationCodeData = $adb->query_result_rowdata($actiationCodeResult,0);
        $TyunWebRecordModel = Vtiger_Record_Model::getCleanInstance("TyunWebBuyService");
        $returnData=$TyunWebRecordModel->doOrderRefundByContractNo($activationCodeData['contractname'],$activationCodeData['usercodeid'],$activationCodeData['usercode'],$recordModel->get('refundamount'),$recordModel->get('changebackdescribe'),false);
        if($returnData['code']==200){
            $resultaa['success']=true;
            return $resultaa;
        }else{
            $resultaa['success']=false;
            $resultaa['error']['message'] = "请先将71360订单作废!";
            return $resultaa;
        }
    }
}

?>
