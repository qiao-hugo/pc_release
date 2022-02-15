<?php
include_once('config.php');
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');
require_once('include/utils/utils.php');
require_once('user_privileges/default_module_view.php');
// Account is used to store vtiger_account information.
class RefillApplication extends CRMEntity {
	var $log;
	var $db;
    var $prd_url = 'http://xxh-gw.71360.com';
    var $dev_url = 'http://prein-gw.71360.com';
    var $url;
	var $table_name = "vtiger_refillapplication";
	var $table_index= 'refillapplicationid';
	//var $tab_name = Array('vtiger_crmentity','vtiger_salesorder','vtiger_salesordercf','vtiger_inventoryproductrel');
	//var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_salesorder'=>'salesorderid','vtiger_salesordercf'=>'salesorderid','vtiger_inventoryproductrel'=>'id');
    var $tab_name = Array('vtiger_crmentity','vtiger_refillapplication','vtiger_rechargesheet');
    var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_refillapplication'=>'refillapplicationid','vtiger_rechargesheet'=>'refillapplicationid');
    /**
	 * Mandatory table for supporting custom fields.
	 */
	//var $customFieldTable = Array('vtiger_rechargesheet', 'refillapplicationid');
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

	var $list_link_field= 'refillapplicationid';
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
	var $default_order_by = 'refillapplicationid';
	var $default_sort_order = 'ASC';
	//var $groupTable = Array('vtiger_sogrouprelation','salesorderid');
	//var $mandatory_fields = Array('subject','createdtime' ,'modifiedtime', 'assigned_user_id');
	// For Alphabetical search
	var $def_basicsearch_col = 'refillapplicationno';

	// For workflows update field tasks is deleted all the lineitems.
	var $isLineItemUpdate = true;

    //2015年9月11日  wangibn添加右侧域名备案关联;
    //var $relatedmodule_list=array('IdcRecords');
    //var $relatedmodule_fields=array('IdcRecords'=>array('salesorder_no'=>'工单编号','idcstate'=>'状态','ipaddress'=>'IP地址'));
	var $relatedmodule_list=array('ReceivedPayments');
	var $relatedmodule_fields=array('ReceivedPayments'=>array('paytitle'=>'Paytitle','owncompany'=>'Owncompany','reality_date'=>'回款时间','unit_price'=>'金额','createtime'=>'创建时间'));

	/** Constructor Function for SalesOrder class
	 *  This function creates an instance of LoggerManager class using getLogger method
	 *  creates an instance for PearDatabase class and get values for column_fields array of SalesOrder class.
	 */
	function __construct() {
		$this->log =LoggerManager::getLogger('RefillApplication');
		$this->db = PearDatabase::getInstance();
		$this->column_fields = getColumnFields('RefillApplication');
        $this->url = $this->dev_url;
	}

	function save_module($module){
	    if($_REQUEST['rechargesource']=='contractChanges'){
            //添加工作流2019-11-05  cxh  add start
            $recordid = $this->id;
            $insertistr='';
            $refillapplicationIdStr="";
            $rowsId=array();
            if($_REQUEST['record']>0){
                $sql=" SELECT * FROM vtiger_changecontract_detail WHERE refillapplicationid=? ";
                $result = $this->db->pquery($sql,array($recordid));
                while ($rowData=$this->db->fetch_array($result)){
                    $rowsId[]=$rowData['detail_refillapplicationid'];
                }
            }
            $has="";
            foreach ($_REQUEST['contractChangeApplication'] as $key=>$value){
                // 如果已经存在则不需要再插入
                if(!in_array($value,$rowsId)){
                    $valueArray[]=$recordid;
                    $valueArray[]=$value;
                    $insertistr.='(?,?),';
                    $refillapplicationIdStr.=$value.",";
                }else{
                    $has.=",".$value;
                }
            }
            if($has){
                $has = trim($has,",");
                $sql="DELETE FROM `vtiger_changecontract_detail` WHERE  refillapplicationid=?    AND   `detail_refillapplicationid` NOT IN(".$has.")";
                $this->db->pquery($sql,array($recordid));
                $sql=" UPDATE vtiger_refillapplication  SET  modulestatus=?  WHERE  refillapplicationid IN(".$has.")";
                $this->db->pquery($sql,array('b_actioning'));
            }
            if($refillapplicationIdStr){
                $insertistr=rtrim($insertistr,',');
                $refillapplicationIdStr=trim($refillapplicationIdStr,',');
                // 把需要变更的充值申请单记录下来
                $sql = " INSERT INTO  vtiger_changecontract_detail(`refillapplicationid`,`detail_refillapplicationid`) VALUES ".$insertistr;
                $this->db->pquery($sql,$valueArray);
                $sql = " UPDATE vtiger_refillapplication  SET  modulestatus=?  WHERE  refillapplicationid IN(".$refillapplicationIdStr.")";
                $this->db->pquery($sql,array('b_actioning'));
            }
            // 下面三行是复制的 走 else 的 最下面的几行 因为有些是走不同的内容
            $this->makeRefillapplicationWorkflows($recordid);
            $object = new SalesorderWorkflowStages_SaveAjax_Action();
            $object->sendWxRemind(array('salesorderid'=>$recordid,'salesorderworkflowstagesid'=>0));
            //添加工作流2019-11-05  cxh  add end
	        //var_dump($_REQUEST);die();
        }else{
                $this->log->debug("充值申请单保存后处理......");
                global $current_user;
                $user_id=$current_user->id;
                $datetime=date('Y-m-d H:i:s');
                $recordid = $this->id;
                if(empty($_REQUEST['record'])) {
                    //$recordid = $this->id;
                    $this->db->pquery('UPDATE vtiger_rechargesheet SET isentity=1 WHERE refillapplicationid=?',array($recordid));
                }else{
                    $recordid=$_REQUEST['record'];
                    $this->db->pquery('UPDATE vtiger_rechargesheet SET deleted=1,modifiedby=?,modifiedtime=? WHERE isentity=0 AND refillapplicationid=?',array($user_id,$datetime,$recordid));
                    $this->db->pquery('UPDATE vtiger_refillapprayment SET deleted=1,modifiedby=?,modifiedtime=? WHERE refillapplicationid=?',array($user_id,$datetime,$recordid));
                }
                //$this->db->pquery('UPDATE vtiger_rechargesheet SET supprebate=? WHERE isentity=1 AND refillapplicationid=?',array($_REQUEST['supprebate'],$recordid));
                global $g_srcterminal;
                $this->log->debug("来源类型(1:移动端,2:PC端):".$g_srcterminal);
                /*
                 * 回款添加
                 */
                if(!empty($_POST['insertii'])){
                    $insertistr='';
                    $tarray=array();
                    if($_POST['rechargesource']=='TECHPROCUREMENT'){
                        foreach($_POST['insertii'] as $key=>$value){
                            $tarray[]=$_POST['salesorderid'];
                            $tarray[]=$value;
                            $tarray[]=$_POST['total'][$value];
                            $refillapptotal=$_POST['refillapptotal'][$value];
                            $tarray[]=$refillapptotal;
                            $tarray[]=$_POST['allowrefillapptotal'][$value];
                            $tarray[]=$_POST['rremarks'][$value];
                            $tarray[]=$recordid;
                            $tarray[]=$_POST['paytitle'][$value];
                            $tarray[]=$_POST['refillapptotal'][$value];
                            $tarray[]=date("Y-m-d");
                            $tarray[]=date("Y-m-d H:i:s");
                            $tarray[]='normal';
                            $insertistr.='(?,?,?,?,?,?,?,?,?,?,?,?),';
                            $this->db->pquery("UPDATE vtiger_salesorderproductsrel SET costofuse=if((costofuse+{$refillapptotal})>purchasemount,purchasemount,(costofuse+{$refillapptotal})) WHERE salesorderproductsrelid=?",
                                    array($value));
                        }

                $insertistr=rtrim($insertistr,',');
                $sql="INSERT INTO vtiger_refillapprayment(`servicecontractsid`,`receivedpaymentsid`,`total`,`refillapptotal`,`allowrefillapptotal`,`remarks`,`refillapplicationid`,`paytitle`,`backwashtotal`,`matchdate`,createdtime,receivedstatus) VALUES{$insertistr}";
                $this->db->pquery($sql,$tarray);
            }else{
                $recordModel=Vtiger_Record_Model::getInstanceById($this->id,'RefillApplication');
                $refillapplicationno=$recordModel->get('refillapplicationno');
                foreach($_POST['insertii'] as $key=>$value){
                    $tarray[]=$_POST['servicecontractsid'];
                    $tarray[]=$value;
                    $tarray[]=$_POST['total'][$value];
                    $tarray[]=$_POST['arrivaldate'][$value];
                    $refillapptotal=$_POST['refillapptotal'][$value];
                    $tarray[]=$refillapptotal;
                    $tarray[]=$_POST['allowrefillapptotal'][$value];
                    $tarray[]=$_POST['rremarks'][$value];
                    $tarray[]=$recordid;
                    $tarray[]=$_POST['paytitle'][$value];
                    $tarray[]=$_POST['refillapptotal'][$value];
                    $tarray[]=$_POST['owncompany'][$value];
                    $tarray[]=date("Y-m-d");
                    $tarray[]=date("Y-m-d H:i:s");
                    $tarray[]='normal';
                    $insertistr.='(?,?,?,?,?,?,?,?,?,?,?,?,?,?),';
                    //冲可充值金额
                    $occupationcost='';
                    if($_POST['rechargesource']=='TECHPROCUREMENT'){
                        $this->db->pquery("UPDATE `vtiger_salesorder` SET occupationamount=(occupationamount+{$refillapptotal}) WHERE salesorderid=?",
                            array($_POST['salesorderid']));
                        $occupationcost=',occupationcost=occupationcost+'.$refillapptotal;
                    }

                    $this->db->pquery("UPDATE `vtiger_receivedpayments` SET rechargeableamount=if((rechargeableamount-{$refillapptotal})>0,(rechargeableamount-{$refillapptotal}),0){$occupationcost} WHERE receivedpaymentsid=?",array($value));
                    $recordModel->setTracker('ReceivedPayments',$value,array('fieldName'=>'rechargeableamount','currentValue'=>$refillapplicationno.'使用:'.$refillapptotal),'vtiger_receivedpayments');
                }
                $insertistr=rtrim($insertistr,',');
                $sql="INSERT INTO vtiger_refillapprayment(`servicecontractsid`,`receivedpaymentsid`,`total`,`arrivaldate`,`refillapptotal`,`allowrefillapptotal`,`remarks`,`refillapplicationid`,`paytitle`,`backwashtotal`,`owncompany`,`matchdate`,createdtime,receivedstatus) VALUES{$insertistr}";
                $this->db->pquery($sql,$tarray);
            }

        }
        /**
         * 添加预充值
         */
        if(!empty($_POST['mInsertPreRecharge'])){
            $insertistr='';
            $tarray=array();
            foreach($_POST['mInsertPreRecharge'] as $key=>$value){
                $tarray[]=$recordid;
                $tarray[]=$_POST['mproductservice'][$value];
                $tarray[]=$_POST['mproductid'][$value];
                $tarray[]=$_POST['mproductid_display'][$value];
                $tarray[]=$_POST['msuppliercontractsid'][$value];
                $tarray[]=$_POST['mhavesignedcontract'][$value];
                $tarray[]=$_POST['msigndate'][$value];
                $tarray[]=$_POST['mrechargeamount'][$value];
                $tarray[]=$_POST['mprestoreadrate'][$value];
                $tarray[]=$_POST['mdiscount'][$value];
                $tarray[]=$_POST['mrebates'][$value];
                $tarray[]=$_POST['mmstatus'][$value];
                $tarray[]=$_POST['msupprebate'][$value];
                $tarray[]=$_POST['mrebatetype'][$value];
                $tarray[]=$user_id;
                $tarray[]=$datetime;
                $insertistr.='(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?),';
            }
            /*echo "<pre>";
            print_r($tarray);
            exit;*/
            $insertistr=rtrim($insertistr,',');
            $sql="INSERT INTO vtiger_rechargesheet(`refillapplicationid`,productservice,productid,`topplatform`,`suppliercontractsid`,`havesignedcontract`,signdate,`rechargeamount`,`prestoreadrate`,`discount`,`rebates`,`mstatus`,supprebate,rebatetype,`createdid`,`createdtime`) VALUES{$insertistr}";
            $this->db->pquery($sql,$tarray);
        }
        /**
         * 技术采购
         */
        if(!empty($_POST['mInserttechsheet'])){
            $insertistr='';
            $tarray=array();
            foreach($_POST['mInserttechsheet'] as $key=>$value){
                $tarray[]=$recordid;
                $tarray[]=$_POST['mproductservice'][$value];
                $tarray[]=$_POST['mproductid'][$value];
                $tarray[]=$_POST['mproductid_display'][$value];
                $tarray[]=$_POST['msuppliercontractsid'][$value];
                $tarray[]=$_POST['mhavesignedcontract'][$value];
                $tarray[]=$_POST['msigndate'][$value];
                $tarray[]=$_POST['mamountpayable'][$value];
                $tarray[]=$user_id;
                $tarray[]=$datetime;
                $insertistr.='(?,?,?,?,?,?,?,?,?,?),';
            }
            $insertistr=rtrim($insertistr,',');
            $sql="INSERT INTO vtiger_rechargesheet(`refillapplicationid`,productservice,productid,`topplatform`,`suppliercontractsid`,`havesignedcontract`,signdate,`amountpayable`,`createdid`,`createdtime`) VALUES{$insertistr}";
            $this->db->pquery($sql,$tarray);
        }
        /**
         * 打包充值
         */
        if(!empty($_POST['insertid'])){
            $insertistr='';
            $this->db->pquery("UPDATE `vtiger_packvendorlist` SET `modifiedtime`=?,`deleteid`=?,deleted=1 WHERE `prefillapplicationid`=?",array($datetime,$user_id,$recordid));
            $tarray=array();
            foreach($_POST['insertid'] as $key=>$value){
                if($_POST['totalreceivablesd'][$value]>0) {
                    $tarray[] = $recordid;
                    $tarray[] = $value;
                    $tarray[] = 0;
                    $tarray[] = $datetime;
                    $tarray[] = $user_id;
                    $insertistr .= '(?,?,?,?,?),';
                }
            }
            $insertistr=rtrim($insertistr,',');
            $sql="INSERT INTO `vtiger_packvendorlist` (`prefillapplicationid`, `refillapplicationids`, `deleted`, `createdtime`, `createdid`) VALUES{$insertistr}";
            $this->db->pquery($sql,$tarray);
        }
        if(!empty($_POST['motherprocurement'])){
            $insertistr='';
            $tarray=array();
            foreach($_POST['motherprocurement'] as $key=>$value){
                $tarray[]=$recordid;
                $tarray[]=$_POST['mproductservice'][$value];
                $tarray[]=$_POST['mproductid'][$value];
                $tarray[]=$_POST['mproductid_display'][$value];
                $tarray[]=$_POST['msuppliercontractsid'][$value];
                $tarray[]=$_POST['mhavesignedcontract'][$value];
                $tarray[]=$_POST['msigndate'][$value];
                $tarray[]=$_POST['mpurchaseamount'][$value];
                $tarray[]=$_POST['mpurchaseprice'][$value];
                $tarray[]=$_POST['mpurchasequantity'][$value];
                $tarray[]=$user_id;
                $tarray[]=$datetime;
                $insertistr.='(?,?,?,?,?,?,?,?,?,?,?,?),';
            }
            /*echo "<pre>";
            print_r($tarray);
            echo "INSERT INTO vtiger_rechargesheet(`refillapplicationid`,productservice,productid,`topplatform`,`suppliercontractsid`,`havesignedcontract`,signdate,`purchaseamount`,`mpurchaseprice`,`mpurchasequantity`,`createdid`,`createdtime`) VALUES{$insertistr}";
            exit;*/
            $insertistr=rtrim($insertistr,',');
            $sql="INSERT INTO vtiger_rechargesheet(`refillapplicationid`,productservice,productid,`topplatform`,`suppliercontractsid`,`havesignedcontract`,signdate,`purchaseamount`,`purchaseprice`,`purchasequantity`,`createdid`,`createdtime`) VALUES{$insertistr}";
            $this->db->pquery($sql,$tarray);
        }
        /**
         * 非媒体类充值
         */
        if(!empty($_POST['mnonmediaextraction'])){
            $insertistr='';
            $tarray=array();
            foreach($_POST['mnonmediaextraction'] as $key=>$value){
                $tarray[]=$recordid;
                $tarray[]=$_POST['mproductservice'][$value];
                $tarray[]=$_POST['mproductid'][$value];
                $tarray[]=$_POST['mproductid_display'][$value];
                $tarray[]=$_POST['msuppliercontractsid'][$value];
                $tarray[]=$_POST['mhavesignedcontract'][$value];
                $tarray[]=$_POST['msigndate'][$value];
                $tarray[]=$_POST['mpurchaseamount'][$value];
                $tarray[]=$_POST['mtotalgrossprofit'][$value];
                $tarray[]=$user_id;
                $tarray[]=$datetime;
                $insertistr.='(?,?,?,?,?,?,?,?,?,?,?),';
            }
            /*echo "<pre>";
            print_r($tarray);
            echo "INSERT INTO vtiger_rechargesheet(`refillapplicationid`,productservice,productid,`topplatform`,`suppliercontractsid`,`havesignedcontract`,signdate,`purchaseamount`,`mpurchaseprice`,`mpurchasequantity`,`createdid`,`createdtime`) VALUES{$insertistr}";
            exit;*/
            $insertistr=rtrim($insertistr,',');
            $sql="INSERT INTO vtiger_rechargesheet(`refillapplicationid`,productservice,productid,`topplatform`,`suppliercontractsid`,`havesignedcontract`,signdate,`purchaseamount`,totalgrossprofit,`createdid`,`createdtime`) VALUES{$insertistr}";
            $this->db->pquery($sql,$tarray);
        }
        /**
         * 客户充值外采
         */
        if(!empty($_POST['insertvendors'])){
            $insertistr='';
            $tarray=array();
            foreach($_POST['insertvendors'] as $key=>$value){
                $tarray[]=$recordid;
                $tarray[]=$_POST['mproductid'][$value];
                $tarray[]=$_POST['mproductid_display'][$value];
                $tarray[]=$_POST['msuppliercontractsid'][$value];
                $tarray[]=$_POST['mhavesignedcontract'][$value];
                $tarray[]=$_POST['msigndate'][$value];
                $tarray[]=$_POST['mrechargetype'][$value];
                $tarray[]=$_POST['mreceivementcurrencytype'][$value];
                $tarray[]=$_POST['mexchangerate'][$value];
                $tarray[]=$_POST['mrechargeamount'][$value];
                $tarray[]=$_POST['mprestoreadrate'][$value];
                $tarray[]=$_POST['mdiscount'][$value];
                $tarray[]=$_POST['mtax'][$value];
                $tarray[]=$_POST['mfactorage'][$value];
                $tarray[]=$_POST['mactivationfee'][$value];
                $tarray[]=$_POST['mtotalcost'][$value];
                $tarray[]=$_POST['mdailybudget'][$value];
                $tarray[]=$_POST['mtransferamount'][$value];
                $tarray[]=$_POST['mrebateamount'][$value];
                $tarray[]=$_POST['mtotalgrossprofit'][$value];
                $tarray[]=$_POST['mservicecost'][$value];
                $tarray[]=$_POST['mmstatus'][$value];
                $tarray[]=$_POST['mrechargetypedetail'][$value];
                $tarray[]=$_POST['mtaxation'][$value];
                $tarray[]=$_POST['misprovideservice'][$value];
                $tarray[]=$_POST['msupprebate'][$value];
                $tarray[]=$_POST['mid'][$value];
                $tarray[]=$_POST['maccountzh'][$value];
                $tarray[]=$_POST['mcustomeroriginattr'][$value];
                $tarray[]=$_POST['mrebatetype'][$value];
                $tarray[]=$_POST['maccountrebatetype'][$value];

                $tarray[]=$user_id;
                $tarray[]=$datetime;
                $insertistr.='(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?),';
            }
            $insertistr=rtrim($insertistr,',');
            $sql="INSERT INTO vtiger_rechargesheet(`refillapplicationid`,productid,`topplatform`,`suppliercontractsid`,`havesignedcontract`,signdate,`rechargetype`,receivementcurrencytype,exchangerate,`rechargeamount`,`prestoreadrate`,`discount`,`tax`,`factorage`,`activationfee`,`totalcost`,`dailybudget`,`transferamount`,`rebateamount`,`totalgrossprofit`,`servicecost`,`mstatus`,`rechargetypedetail`,taxation,isprovideservice,supprebate,did,accountzh,customeroriginattr,rebatetype,accountrebatetype,`createdid`,`createdtime`) VALUES{$insertistr}";
            $this->db->pquery($sql,$tarray);



        }
        /**
         * 退币转充
         */
        if($_POST['rechargesource']=='COINRETURN'){
            if(!empty($_POST['truncashtype'])){
                $insertistr='';
                $tarray=array();
                foreach($_POST['truncashtype'] as $key=>$value){
                    $tarray[]=$recordid;
                    $tarray[]=$_POST['mid'][$key];
                    $tarray[]=$_POST['mproductid'][$key];
                    $tarray[]=$_POST['mproductid_display'][$key];
                    $tarray[]=$_POST['maccountzh'][$key];
                    $tarray[]=$_POST['maccountrebatetype'][$key];
                    $tarray[]=$_POST['misprovideservice'][$key];
                    $tarray[]=$_POST['mdiscount'][$key];
                    $tarray[]=$_POST['mcashtransfer'][$key];
                    $tarray[]=$_POST['maccounttransfer'][$key];
                    $tarray[]=$value;

                    $tarray[]=$user_id;
                    $tarray[]=$datetime;
                    $insertistr.='(?,?,?,?,?,?,?,?,?,?,?,?,?),';
                }
                $insertistr=rtrim($insertistr,',');
                $sql="INSERT INTO vtiger_rechargesheet(`refillapplicationid`,did,productid,`topplatform`,`accountzh`,`accountrebatetype`,isprovideservice,`discount`,cashtransfer,accounttransfer,turninorout,`createdid`,`createdtime`) VALUES{$insertistr}";
                $this->db->pquery($sql,$tarray);
            }
            $this->db->pquery('UPDATE vtiger_rechargesheet SET turninorout=\'out\' WHERE isentity=1 AND refillapplicationid=?',array($recordid));
        }
        /**
         * 增款申请
         */
        if($_POST['rechargesource']=='INCREASE'){
            if(!empty($_POST['maccountrebatetype'])){
                $insertistr='';
                $tarray=array();
                foreach($_POST['maccountrebatetype'] as $key=>$value){
                    $tarray[]=$recordid;
                    $tarray[]=$_POST['mmservicecontractsid'][$key];
                    $tarray[]=$_POST['mmservicecontractsid']['display'.$key];
                    $tarray[]=$_POST['mmaccountid'][$key];
                    $tarray[]=$_POST['mmaccountid']['display'.$key];
                    $tarray[]=$value;
                    $tarray[]=$_POST['mdiscount'][$key];
                    $tarray[]=$_POST['mcashincrease'][$key];
                    $tarray[]=$_POST['mtaxrefund'][$key];
                    $tarray[]=$_POST['mcashconsumption'][$key];
                    $tarray[]=$_POST['mgrantquarter'][$key];
                    $tarray[]=$_POST['mmstatus'][$key];
                    $tarray[]=$user_id;
                    $tarray[]=$_POST['mreceivementcurrencytype'][$key];
                    $tarray[]=$datetime;
                    $insertistr.='(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?),';
                }
                $insertistr=rtrim($insertistr,',');
                $sql="INSERT INTO vtiger_rechargesheet(`refillapplicationid`,
                                                        mservicecontractsid,
                                                        mservicecontractsid_name,
                                                        `maccountid`,
                                                        `maccountid_name`,
                                                        `accountrebatetype`,
                                                        discount,
                                                        `cashincrease`,
                                                        taxrefund,
                                                        cashconsumption,
                                                        grantquarter,
                                                        mstatus,
                                                        `createdid`,
                                                        `receivementcurrencytype`,
                                                        `createdtime`) VALUES{$insertistr}";
                $this->db->pquery($sql,$tarray);
            }
            $mservicecontractsid_display=$_POST['mservicecontractsid_display'];
            $maccountid_display=$_POST['maccountid_display'];
            $this->db->pquery('UPDATE vtiger_rechargesheet SET mservicecontractsid_name=?,maccountid_name=? WHERE isentity=1 AND refillapplicationid=?',array($mservicecontractsid_display,$maccountid_display,$recordid));
        }
        //充值申请单明细保存
        if(!empty($_POST['insertiref'])){
            $insertistr='';
            $tarray=array();
            foreach($_POST['insertiref'] as $key=>$value){
                $tarray[]=$recordid;
                $tarray[]=$_POST['mproductid'][$value];
                $tarray[]=$_POST['mproductid_display'][$value];
                $tarray[]=$_POST['maccountzh'][$value];
                $tarray[]=$_POST['mid'][$value];
                $tarray[]=$_POST['mrechargetype'][$value];
		        $tarray[]=$_POST['mreceivementcurrencytype'][$value];
                $tarray[]=$_POST['mexchangerate'][$value];
                $tarray[]=$_POST['mrechargeamount'][$value];
                $tarray[]=$_POST['mprestoreadrate'][$value];
                $tarray[]=$_POST['mdiscount'][$value];
                $tarray[]=$_POST['mtax'][$value];
                $tarray[]=$_POST['mfactorage'][$value];
                $tarray[]=$_POST['mactivationfee'][$value];
                $tarray[]=$_POST['mtotalcost'][$value];
                $tarray[]=$_POST['mdailybudget'][$value];
                $tarray[]=$_POST['mtransferamount'][$value];
                $tarray[]=$_POST['mrebateamount'][$value];
                $tarray[]=$_POST['mtotalgrossprofit'][$value];
                $tarray[]=$_POST['mservicecost'][$value];
                $tarray[]=$_POST['mmstatus'][$value];
                $tarray[]=$_POST['mrechargetypedetail'][$value];
                $tarray[]=$_POST['mtaxation'][$value];
                $tarray[]=$_POST['misprovideservice'][$value];
                $tarray[]=$_POST['msupprebate'][$value];
                $tarray[]=$_POST['mcustomeroriginattr'][$value];
                $tarray[]=$_POST['mrebatetype'][$value];
                $tarray[]=$_POST['maccountrebatetype'][$value];
                $tarray[]=$user_id;
                $tarray[]=$datetime;
                $insertistr.='(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?),';
            }
            $insertistr=rtrim($insertistr,',');
            $sql="INSERT INTO vtiger_rechargesheet(`refillapplicationid`,productid,`topplatform`,`accountzh`,`did`,`rechargetype`,receivementcurrencytype,exchangerate,`rechargeamount`,`prestoreadrate`,`discount`,`tax`,`factorage`,`activationfee`,`totalcost`,`dailybudget`,`transferamount`,`rebateamount`,`totalgrossprofit`,`servicecost`,`mstatus`,`rechargetypedetail`,taxation,isprovideservice,supprebate,customeroriginattr,rebatetype,accountrebatetype,`createdid`,`createdtime`) VALUES{$insertistr}";
            $this->db->pquery($sql,$tarray);
        }

        //=============================移动端数据保存处理(开始)=========================================================
        //移动端回款明细数据保存处理
        global $receivedpaymentsData;
        $this->log->debug("回款详细更新： ".$receivedpaymentsData);
        if ($g_srcterminal == '1' && count($receivedpaymentsData) > 0) {
            $insertistr='';
            $tarray=array();
            foreach ($receivedpaymentsData as $receivedpayments) {
                $value = $receivedpayments;
                $tarray[]=$value['servicecontractsid'];
                $tarray[]=$value["receivedpaymentsid"];
                $tarray[]=$value["total"];
                $tarray[]=$value['arrivaldate'];
                $refillapptotal=$value['refillapptotal'];
                $tarray[]=$refillapptotal;
                $tarray[]=$value['allowrefillapptotal'];
                $tarray[]=$value['remarks'];
                $tarray[]=$recordid;
                $tarray[]=$value['paytitle'];
                $tarray[]=$value['refillapptotal'];
                $tarray[]=$value['owncompany'];
                $tarray[]='normal';
                //$tarray[]=date("Y-m-d");
                $insertistr.='(?,?,?,?,?,?,?,?,?,?,?,?,NOW()),';
                //冲可充值金额
                $occupationcost='';
                /*if($_POST['rechargesource']=='TECHPROCUREMENT'){
                    $this->db->pquery("UPDATE `vtiger_salesorder` SET occupationamount=(occupationamount+{$refillapptotal}) WHERE salesorderid=?",
                        array($_POST['salesorderid']));
                    $occupationcost=',occupationcost=occupationcost+'.$refillapptotal;
                }*/
                $this->db->pquery("UPDATE `vtiger_receivedpayments` SET rechargeableamount=if((rechargeableamount-{$refillapptotal})>0,(rechargeableamount-{$refillapptotal}),0){$occupationcost} WHERE receivedpaymentsid=?",array($value["receivedpaymentsid"]));
            }
            if(!empty($insertistr)){
                $insertistr=rtrim($insertistr,',');
                $sql="INSERT INTO vtiger_refillapprayment(`servicecontractsid`,`receivedpaymentsid`,`total`,`arrivaldate`,`refillapptotal`,`allowrefillapptotal`,`remarks`,`refillapplicationid`,`paytitle`,`backwashtotal`,`owncompany`,receivedstatus,`matchdate`) VALUES{$insertistr}";
                $this->db->pquery($sql,$tarray);
            }
            $receivedpaymentsData = null;
        }
        //移动端充值明细数据保存处理
        $productids_tmp = '';
        global $rechargesheetData;
        global $g_rechargesource;
        $this->log->debug("充值申请单详细更新： ".$rechargesheetData);
        if ($g_srcterminal == '1' && count($rechargesheetData) > 0) {
            $insertistr = '';
            
            foreach ($rechargesheetData as $rechargesheet) {
                $tarray = array();
                $value = $rechargesheet;

                $this->log->debug("产品id： ".$value['productid']);
                if($productids_tmp == ''){
                    $productids_tmp = $value['productid'];
                }else{
                    $productids_tmp .= ','.$value['productid'];
                }

                $tarray[] = $recordid;
                $tarray[]=$value['productid'];
                $tarray[]=$value['topplatform'];
                if($g_rechargesource=='Vendors'){
                    $this->log->debug("充值申请单详细-供应商更新开始");
                    //供应商充值
                    $tarray[]=$value['suppliercontractsid'];
                    $tarray[]=$value['havesignedcontract'];
                    $tarray[]=$value['productservice'];
                    $tarray[]=$value['signdate'];
                    $tarray[]=$value['receivementcurrencytype'];
                    $tarray[]=$value['exchangerate'];
                    $tarray[]=$value['rechargeamount'];
                    $tarray[]=$value['prestoreadrate'];
                    $tarray[]=$value['discount'];
                    $tarray[]=$value['tax'];
                    $tarray[]=$value['factorage'];
                    $tarray[]=$value['activationfee'];
                    $tarray[]=$value['totalcost'];
                    $tarray[]=$value['transferamount'];
                    $tarray[]=$value['totalgrossprofit'];
                    $tarray[]=$value['servicecost'];
                    $tarray[]=$value['rechargetypedetail'];
                    $tarray[]=$value['taxation'];
                    $tarray[]=$value['isprovideservice'];
                    $tarray[]=$value['supprebate'];
                    $tarray[]=$value['accountzh'];
                    $tarray[]=$value['did'];
                    $tarray[]=$value['customeroriginattr'];
                    $tarray[]=$value['rebatetype'];
                    $tarray[]=$value['accountrebatetype'];
                    $tarray[]=$user_id;
                    $insertistr='(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW())';
                    $sql="INSERT INTO vtiger_rechargesheet(`refillapplicationid`,productid,topplatform,`suppliercontractsid`,`havesignedcontract`,productservice,signdate,receivementcurrencytype,exchangerate,`rechargeamount`,`prestoreadrate`,`discount`,`tax`,`factorage`,`activationfee`,`totalcost`,`transferamount`,`totalgrossprofit`,`servicecost`,`rechargetypedetail`,taxation,isprovideservice,supprebate,accountzh,did,customeroriginattr,rebatetype,accountrebatetype,`createdid`,`createdtime`) VALUES{$insertistr}";
                    $this->db->pquery($sql,$tarray);
                    $this->log->debug("充值申请单详细-供应商更新成功，sql->" .$sql);
                }else{
                    //客户充值
                    $tarray[]=$value['accountzh'];
                    $tarray[]=$value['did'];
                    $tarray[]=$value['receivementcurrencytype'];
                    $tarray[]=$value['exchangerate'];
                    $tarray[]=$value['rechargeamount'];
                    $tarray[]=$value['prestoreadrate'];
                    $tarray[]=$value['discount'];
                    $tarray[]=$value['tax'];
                    $tarray[]=$value['factorage'];
                    $tarray[]=$value['activationfee'];
                    $tarray[]=$value['totalcost'];
                    $tarray[]=$value['transferamount'];
                    $tarray[]=$value['totalgrossprofit'];
                    $tarray[]=$value['servicecost'];
                    $tarray[]=$value['rechargetypedetail'];
                    $tarray[]=$value['taxation'];
                    $tarray[]=$value['isprovideservice'];
                    $tarray[]=$value['supprebate'];
                    $tarray[]=$value['customeroriginattr'];
                    $tarray[]=$value['rebatetype'];
                    $tarray[]=$value['accountrebatetype'];
                    $tarray[]=$user_id;
                    $insertistr='(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW())';
                    $sql="INSERT INTO vtiger_rechargesheet(`refillapplicationid`,productid,topplatform,`accountzh`,`did`,receivementcurrencytype,exchangerate,`rechargeamount`,`prestoreadrate`,`discount`,`tax`,`factorage`,`activationfee`,`totalcost`,`transferamount`,`totalgrossprofit`,`servicecost`,`rechargetypedetail`,taxation,isprovideservice,supprebate,customeroriginattr,rebatetype,accountrebatetype,`createdid`,`createdtime`) VALUES{$insertistr}";
                    $this->db->pquery($sql,$tarray);
                    $this->log->debug("充值申请单详细-客户更新成功，sql->" .$sql);
                }
            }
            $rechargesheetData = null;
        }
        //=============================移动端数据保存处理(结束)=========================================================
        if(!empty($_REQUEST['accountid_display'])){
            $this->db->pquery("UPDATE vtiger_refillapplication SET customer_name=? WHERE refillapplicationid=?", array($_REQUEST['accountid_display'],$recordid));
        }

        $checkarray=array();
        if ($g_srcterminal == '1'){
            $this->log->debug("移动端流程");
            global $g_productids;
            $productids = $g_productids;
            if($productids_tmp != ""){
                $productids .= ','.$productids_tmp;
            }
        }else{
            $this->log->debug("PC端流程");
            $productids=$_POST['productid'].',';
            $productids.=implode(',',$_POST['mproductid']);
            $productids=trim($productids,',');
            $productids=empty($productids)?0:$productids;
        }

        $this->log->debug("生成工作流的产品id： ".$productids);
        $result = $this->db->pquery("SELECT vtiger_products.productname,vtiger_products.productid,vtiger_products.productman FROM vtiger_products WHERE productid in({$productids})",array());
        while($product=$this->db->fetch_row($result)){
            $checkarray[]=array('workflowstagesname'=> $product['productname'].'审核','smcreatorid'=>0,'productid'=>$product['productid'],'productman'=>$product['productman']);
        }
        vglobal('checkproducts',$checkarray);

        if (empty($_REQUEST['record'])) {
            $this->addRefillApplicationSendmail($this->id);
        }
        //保存充值申请单 垫款更新

        $this->saveRefillappSetAdvancesmoney($this->id, 'save');

                //添加工作流 2017/03/07 gaocl add
                $this->makeRefillapplicationWorkflows($recordid);

                //申请单审核流处理 2017/03/03 gaocl add
                $this->SetAuditDeparment($recordid);
                //RefillApplication_Module_Model::sendRefillApplicationMail($recordid,1);
                /**
                 *  注释掉旧的消息推送
                 */
                /*//微信提醒第一个节点的审核人
                $this->getSendWinXinUser($recordid);*/
                $object = new SalesorderWorkflowStages_SaveAjax_Action();
                $object->sendWxRemind(array('salesorderid'=>$recordid,'salesorderworkflowstagesid'=>0));
        }

        if (in_array($_REQUEST['rechargesource'], array('Vendors', 'Accounts', 'NonMediaExtraction'))) {
            //销账系统对接
            $rechargesource = $_REQUEST['rechargesource'];
            $recordModel = Vtiger_Record_Model::getInstanceById($this->id, 'RefillApplication');
            $refillapplicationno = $recordModel->get('refillapplicationno');
            global $current_user;
            //媒体充值
            if ($rechargesource == 'Accounts') {
                global $adb;
                $query = "SELECT
	d.vendorname
FROM
	vtiger_rechargesheet AS a
LEFT JOIN vtiger_accountplatform_detail AS b ON a.did = b.idaccount
LEFT JOIN vtiger_accountplatform AS c ON b.accountplatformid = c.accountplatformid
LEFT JOIN vtiger_vendor AS d ON c.vendorid = d.vendorid
WHERE
	a.refillapplicationid = ?
LIMIT 1";
                $result=$adb->pquery($query,array($_REQUEST['currentid']));
                $vendorname =  $adb->query_result($result,0,"vendorname");
                //是否新增
                if($_REQUEST['record']>0){
                    $url = $this->url.'/write-off/mediaRecharge/updateMediaRecharge';
                }else{
                    $url = $this->url.'/write-off/mediaRecharge/insertMediaRecharge';
                }

                $params = [
                    "code" => $refillapplicationno, //申请单编号
                    "createName" => $current_user->last_name, //创建人名称
                    "createUser" => $current_user->id, //创建人id
                    'customerContractCode' => $_REQUEST["servicecontractsid_display"], //服务合同
                    "customerId" => $_REQUEST['accountid'],
                    'customer' => $_REQUEST["accountid_display"], //客户名称
                    'money' => $_REQUEST["actualtotalrecharge"], //应收款总额
                    'rechargeSource' => "0",//充值来源
                    'advance' => bccomp($_REQUEST["actualtotalrecharge"], $_REQUEST['totalrecharge']) > 0 ? '0' : '1',//是否垫款
                    'advanceMoney' => $_REQUEST["grossadvances"], //合计垫款金额
                    'accountCurrency' => $_REQUEST["totalaccountcurrency"], //合计充值账户币
                ];
                if(!$_REQUEST['record']){
                    $params['applicationTime'] = date('Y-m-d H:i:s');
                    $params['status'] = 'b_check';
                }
                //充值明细
                $params['rechargeDetailInsertVOList'][] = [
                    'accountCode' => $_REQUEST["did_display"], //ID
                    'rechargePlatform' => $_REQUEST["productid_display"], //充值平台
                    'accountName' => $_REQUEST["accountzh"], //账户名称
                    'customerRebateRatio' => $_REQUEST["discount"], //客户返点比例
                    'customerRebateType' => $_REQUEST["accountrebatetype"] == 'CashBack' ? '0' : '1',//客户返点类型
                    'money' => $_REQUEST["transferamount"], //应收款金额
                    'accountCurrency' => $_REQUEST["prestoreadrate"], //充值账户币
                    'supplierRebateRatio' => $_REQUEST["supprebate"], //供应商返点
                    'supplierRebateType' => $_REQUEST["rebatetype"] == 'CashBack' ? '0' : '1',//供应商返点类型
                    'supplierName' => $vendorname, //供应商
                ];
                $count = count($_REQUEST['mid']);
                for ($i = 1; $i <= $count; $i++) {
                    $params['rechargeDetailInsertVOList'][] = [
                        'accountCode' => $_REQUEST["mid_display"][$i], //ID
                        'rechargePlatform' => $_REQUEST["mproductid_display"][$i], //充值平台
                        'accountName' => $_REQUEST["maccountzh"][$i], //账户名称
                        'customerRebateRatio' => $_REQUEST["mdiscount"][$i], //客户返点比例
                        'customerRebateType' => $_REQUEST["maccountrebatetype"][$i] == 'CashBack' ? '0' : '1',//客户返点类型
                        'money' => $_REQUEST["mtransferamount"][$i], //应收款金额
                        'accountCurrency' => $_REQUEST["mprestoreadrate"][$i], //充值账户币
                        'supplierRebateRatio' => $_REQUEST["msupprebate"][$i], //供应商返点
                        'supplierRebateType' => $_REQUEST["mrebatetype"][$i] == 'CashBack' ? '0' : '1',//供应商返点类型
                        'supplierName' => $vendorname, //供应商
                    ];
                }
            }
            //媒体充值(外采)
            if ($rechargesource == 'Vendors') {
                //是否新增
                if($_REQUEST['record']>0){
                    $url = $this->url.'/write-off/mediaRechargeOutsource/updateMediaRechargeOutsource';
                }else{
                    $url = $this->url.'/write-off/mediaRechargeOutsource/insertMediaRechargeOutsource';
                }

                $params = [
                    "code" => $refillapplicationno, //申请单编号
                    "createName" => $current_user->last_name, //创建人名称
                    "createUser" => $current_user->id, //创建人id
                    'customerContractCode' => $_REQUEST["servicecontractsid_display"], //服务合同
                    "customerId" => $_REQUEST['accountid'],
                    'customer' => $_REQUEST["accountid_display"], //客户名称
                    'money' => $_REQUEST["actualtotalrecharge"], //应收款总额
                    'rechargeSource' => "1",//充值来源
                    'advance' => bccomp($_REQUEST["actualtotalrecharge"], $_REQUEST['totalrecharge']) > 0 ? '0' : '1',//是否垫款
                    'projectService' => $_REQUEST["productid_display"], //产品服务
                    'supplier' => $_REQUEST["vendorid_display"], //供应商
                    'bankName' => $_REQUEST["bankname"], //开户名
                    'bankDeposit' => $_REQUEST["bankaccount"], //开户行
                    'bankAccount' => $_REQUEST["banknumber"], //银行账户
                    'advanceMoney' => $_REQUEST["totaladvances"], //合计垫款金额
                    'accountCurrency' => $_REQUEST["totalaccountcurrency"], //合计充值账户币
                ];
                if(!$_REQUEST['record']){
                    $params['applicationTime'] = date('Y-m-d H:i:s');
                    $params['status'] = 'b_check';
                }

                //充值明细
                $params['rechargeDetailInsertVOList'][] = [
                    'accountCode' => $_REQUEST["did_display"], //ID
                    'accountName' => $_REQUEST["accountzh"], //账户名称
                    'money' => $_REQUEST["transferamount"], //应收款金额
                    'customerRebateRatio' => $_REQUEST["discount"], //客户返点比例
                    'customerRebateType' => $_REQUEST["accountrebatetype"] == 'CashBack' ? '0' : '1',//客户返点类型
                    'accountCurrency' => $_REQUEST["prestoreadrate"], //充值账户币
                    'supplierRebateRatio' => $_REQUEST["supprebate"], //供应商返点
                    'supplierRebateType' => $_REQUEST["rebatetype"] == 'CashBack' ? '0' : '1',//供应商返点类型
                    'supplierName' => $_REQUEST["vendorid_display"], //供应商
                    'supplierContractCode' => $_REQUEST["suppliercontractsid_display"], //供应商合同
                ];
                $count = count($_REQUEST['mid']);
                for ($i = 1; $i <= $count; $i++) {
                    $params['rechargeDetailInsertVOList'][] = [
                        'accountCode' => $_REQUEST["mid_display"][$i], //ID
                        'accountName' => $_REQUEST["maccountzh"][$i], //账户名称
                        'money' => $_REQUEST["mtransferamount"][$i], //应收款金额
                        'customerRebateRatio' => $_REQUEST["mdiscount"][$i], //客户返点比例
                        'customerRebateType' => $_REQUEST["maccountrebatetype"][$i] == 'CashBack' ? '0' : '1',//客户返点类型
                        'accountCurrency' => $_REQUEST["mprestoreadrate"][$i], //充值账户币
                        'supplierRebateRatio' => $_REQUEST["msupprebate"][$i], //供应商返点
                        'supplierRebateType' => $_REQUEST["mrebatetype"][$i] == 'CashBack' ? '0' : '1',//供应商返点类型
                        'supplierName' => $_REQUEST["vendorid_display"], //供应商
                        'supplierContractCode' => $_REQUEST["suppliercontractsid_display"], //供应商合同
                    ];
                }
            }
            //非媒体类外采
            if ($rechargesource == 'NonMediaExtraction') {
                //是否新增
                if($_REQUEST['record']>0){
                    $url = $this->url.'/write-off/noneMediaRecharge/updateNoneMediaRecharge';
                }else{
                    $url = $this->url.'/write-off/noneMediaRecharge/insertNoneMediaRecharge';
                }

                $params = [
                    "code" => $refillapplicationno, //申请单编号
                    'advance' => bccomp($_REQUEST["actualtotalrecharge"], $_REQUEST['totalrecharge']) > 0 ? '0' : '1',//是否垫款
                    "createName" => $current_user->last_name, //创建人名称
                    "createUser" => $current_user->id, //创建人id
                    'customerContractCode' => $_REQUEST["servicecontractsid_display"], //服务合同
                    "customerId" => $_REQUEST['accountid'],
                    'customer' => $_REQUEST["accountid_display"], //客户名称
                    'projectService' => $_REQUEST["productservice"], //产品服务
                    'advanceMoney' => $_REQUEST["grossadvances"], //合计垫款金额
                    'rechargeSource' => "2",//充值来源
                    'money' => $_REQUEST["actualtotalrecharge"], //应收款总额
                    'customerRebateRatio' => $_REQUEST["nonaccountrebate"], //客户返点比例
                    'customerRebateType' => $_REQUEST["nonaccountrebatetype"] == 'CashBack' ? '0' : '1',//客户返点类型
                    'supplier' => $_REQUEST["vendorid_display"], //供应商
                    'bankName' => $_REQUEST["bankname"], //开户名
                    'bankDeposit' => $_REQUEST["bankaccount"], //开户行
                    'bankAccount' => $_REQUEST["banknumber"], //银行账户
                    'isLaunchTime' => $_REQUEST["isthrowtime"]=='yes'?'1':'0', //是否有具体投放期间
                    'launchStartTime' => $_REQUEST["isthrowtime"]=='yes' ? $_REQUEST["throwtime"].'-01' : '', //投放开始时间
                    "launchEndTime" => $_REQUEST["isthrowtime"]=='yes' ? $_REQUEST["throwtime"].'-'.date('t', strtotime($_REQUEST["throwtime"])) : '', //投放结束时间
                ];
                if(!$_REQUEST['record']){
                    $params['applicationTime'] = date('Y-m-d H:i:s');
                    $params['status'] = 'b_check';
                }
                //充值明细
                $params['rechargeDetailInsertVOList'][] = [
                    'productService' => $_REQUEST["productid_display"], //产品服务
                    'rechargePlatform' => $_REQUEST["productid_display"], //充值平台
                    'supplierContractCode' => $_REQUEST["suppliercontractsid_display"], //供应商合同
                    'purchaseMoney' => $_REQUEST["purchaseamount"], //采购金额
                ];
                $count = count($_REQUEST['mproductservice']);
                for ($i = 1; $i <= $count; $i++) {
                    $params['rechargeDetailInsertVOList'][] = [
                        'productService' => $_REQUEST["mproductid_display"][$i], //产品服务
                        'rechargePlatform' => $_REQUEST["mproductid_display"][$i], //充值平台
                        'supplierContractCode' => $_REQUEST["msuppliercontractsid"]["display".$i], //供应商合同
                        'purchaseMoney' => $_REQUEST["mpurchaseamount"][$i], //采购金额
                    ];
                }
            }

            $userid = $current_user->id;
            $params = json_encode($params);
            $contractsRecordModel=Vtiger_Record_Model::getCleanInstance('RefillApplication');
            $header = array(CURLOPT_HTTPHEADER=>array(
                "Content-Type:application/json",
                "userId:".$userid
            ));
            $res = $contractsRecordModel->https_requestcomm($url,$params,$header,true);
            $resData = json_decode($res, true);
//            dd(compact('params','url','res','header'));
            if ($resData['code'] == '0') {
                $this->log->debug("销账系统充值成功，params->" . $params .'&&' . $url);
            } else {
                $this->log->debug("销账系统充值失败，params->" . $params .'&&' . $url);
            }
        }
    }

    // 保存充值申请单 垫款更新
    public function saveRefillappSetAdvancesmoney($recordid, $type) {

        /*$db = PearDatabase::getInstance();
        $sql = "select sum(rechargeamount) AS rechargeamount from vtiger_rechargesheet where refillapplicationid=?";
        $sel_result = $db->pquery($sql, array($recordid));
        $res_cnt = $db->num_rows($sel_result);
        if($res_cnt > 0) {
            $row = $db->query_result_rowdata($sel_result, 0);

            $rechargeamount = 0;
            $msg = '';
            if ($type == 'save') {
                $rechargeamount = $row['rechargeamount'];
                $msg = '(充值申请单充值)';
            } else if($type == 'backall'){
                $rechargeamount = 0 - $row['rechargeamount'];
                $msg = '(充值申请单打回)';
            }
            $recordModel = Vtiger_Record_Model::getInstanceById($recordid, 'RefillApplication');
            $entity = $recordModel->entity->column_fields;
            Accounts_Record_Model::setAdvancesmoney($entity['accountid'], $rechargeamount, $msg);
            
        }*/
        $recordModel = Vtiger_Record_Model::getInstanceById($recordid, 'RefillApplication',true);
        $rechargesource=$recordModel->get('rechargesource');
        if(in_array($rechargesource,array('Vendors','Accounts','NonMediaExtraction'))) {
            $totalrecharge=$recordModel->get('totalrecharge');//使用回款总款
            $actualtotalrecharge=$recordModel->get('actualtotalrecharge');//实际现金充值总额
            if(in_array($rechargesource,array('NonMediaExtraction'))){
                $actualtotalrecharge=$recordModel->get('totalreceivables');
            }
            $amountAvailable=bcsub($actualtotalrecharge,$totalrecharge,2);
            $grossadvances=$recordModel->get('grossadvances');
            $grossadvances*=1;
            if ($grossadvances > 0) {//有垫款
                $refillapplicationno=$recordModel->get('refillapplicationno');
                if ($type == 'save') {
                    $msg = '('.$refillapplicationno.':充值申请单充值)';
                } else if ($type == 'backall') {
                    $amountAvailable = 0 - $amountAvailable;

                    $msg = '('.$refillapplicationno.':充值申请单打回)';
                }
                $accountRecordModule = Vtiger_Record_Model::getCleanInstance("Accounts");
                $accountRecordModule->setAdvancesmoney($recordModel->get('accountid'), $amountAvailable, $msg);
                $this->db->pquery("UPDATE vtiger_refillapplication SET iscushion=1 ,customer_name=? WHERE refillapplicationid=?", array($_REQUEST['accountid_display'],$recordid));
            }
        }

    }

    /*
    添加充值申请单 发送邮件
    */
    public function addRefillApplicationSendmail($recordid) {
        global $current_user;
        $db = PearDatabase::getInstance();
        $sql = "SELECT user FROM vtiger_custompowers WHERE custompowerstype='addRefillApplicationSendmail' LIMIT 1";
        $sel_result = $db->pquery($sql, array());
        $res_cnt = $db->num_rows($sel_result);
        if($res_cnt > 0) {
            $rowData = $db->query_result_rowdata($sel_result, 0);
            $sql = "SELECT email1,last_name FROM vtiger_users WHERE id IN ({$rowData['user']}) AND `status`='Active'";
            $addrArray = array();
            $sel_result = $db->pquery($sql, array());
            while($rawData=$db->fetch_array($sel_result)) {
               $addrArray[] = array('mail'=>$rawData['email1'], 'name'=>$rawData['last_name']);
            }
            if (count($addrArray) > 0) {
                $recordModel = Vtiger_Record_Model::getInstanceById($recordid, 'RefillApplication');
                $entity = $recordModel->entity->column_fields;

                $sql = "SELECT vtiger_servicecontracts.contract_no, vtiger_account.accountname FROM vtiger_servicecontracts LEFT JOIN vtiger_account ON vtiger_servicecontracts.sc_related_to=vtiger_account.accountid
                    WHERE vtiger_servicecontracts.servicecontractsid=?";
                $sel_result = $db->pquery($sql, array($entity['servicecontractsid']));
                $res_cnt = $db->num_rows($sel_result);
                if ($res_cnt > 0) {
                    $rowData = $db->query_result_rowdata($sel_result, 0);
                    $Subject = "充值申请单添加提醒";  //主题
                    $body = $current_user->last_name.' 添加充值申请单<br/>';
                    $body .= "客户:&nbsp;&nbsp;".$rowData['accountname'].'<br/>';
                    $body .= '服务合同:&nbsp;&nbsp;'.$rowData['contract_no'].'<br/>';
                    $body .= "申请单编号:&nbsp;&nbsp;<a href='".$_SERVER['HTTP_HOST']."/index.php?module=RefillApplication&view=Detail&record={$recordid}'>{$entity['refillapplicationno']}</a><br/>";
                    $body .= "时间：&nbsp;&nbsp;".date('Y-m-d H:i:s');
                    RefillApplication_Record_Model::sendMail($Subject, $body, $addrArray);
                }
            }
        }
    }


    /*
        手机端的退款申请单审核后 垫款更改
        没有用了
    */
    public function mobile_workflowcheckafter(Vtiger_Request $request) {
        /*$recordid = $request->get('record');
        $db = PearDatabase::getInstance();
        $sql = "SELECT vtiger_salesorderworkflowstages.isaction FROM vtiger_salesorderworkflowstages 
                LEFT JOIN vtiger_refillapplication ON vtiger_refillapplication.refillapplicationid=vtiger_salesorderworkflowstages.salesorderid
                WHERE vtiger_salesorderworkflowstages.salesorderid=?  AND vtiger_salesorderworkflowstages.deleted=0 AND vtiger_salesorderworkflowstages.modulename='RefillApplication'
                AND vtiger_refillapplication.modulestatus!='c_cancel' ORDER BY sequence DESC LIMIT 1";
        $sel_result = $db->pquery($sql, array($recordid));
        $row = $db->query_result_rowdata($sel_result, 0);
        if($row['isaction'] == 1) {
            $recordModel = Vtiger_Record_Model::getInstanceById($recordid, 'RefillApplication');
            $entity = $recordModel->entity->column_fields;
            $sql = "select sum(rechargeamount) AS rechargeamount from vtiger_rechargesheet where refillapplicationid=?";
            $sel_result = $db->pquery($sql, array($recordid));
            $res_cnt = $db->num_rows($sel_result);
            if($res_cnt > 0) {
                $rowData = $db->query_result_rowdata($sel_result, 0);
                Accounts_Record_Model::setAdvancesmoney($entity['accountid'], $rowData['rechargeamount'], '(手机端充值申请单充值)');
            }
        }*/
    }


    /**
     * 充值申请单这申核后置事件
     * @param $request
     */
    public function workflowcheckafter(Vtiger_Request $request){
        $recordid = $request->get('record');
        $recordModel = Vtiger_Record_Model::getInstanceById($recordid, 'RefillApplication',true);
        /*$rechargesource=$recordModel->get('rechargesource');//使用回款总款

                if($rechargesource=='PreRecharge' && $recordModel->get('modulestatus')=='c_complete'){
                    $query="SELECT vtiger_refillapplication.rserialnumber FROM vtiger_refillapplication LEFT JOIN vtiger_crmentity ON vtiger_refillapplication.refillapplicationid=vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted=0
                      AND vtiger_refillapplication.rechargesource=? ORDER BY vtiger_refillapplication.rserialnumber DESC limit 1";
                    $result=$this->db->pquery($query,array($rechargesource));
                    $datarow=$this->db->query_result_rowdata($result,0);
                    $num=$datarow['rserialnumber']+1;
                    $sql="UPDATE vtiger_refillapplication SET rserialnumber=? WHERE refillapplicationid=?";
                    $this->db->pquery($sql,array($num,$recordid));
                }*/
        $stagerecordid=$request->get('stagerecordid');
        $query="SELECT
                    vtiger_workflowstages.workflowstagesflag,
                    vtiger_workflowstages.workflowsid,vtiger_salesorderworkflowstages.salesorderid
                FROM
                    `vtiger_salesorderworkflowstages`
                LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid
                WHERE
                    vtiger_salesorderworkflowstages.salesorderworkflowstagesid = ?
                AND vtiger_salesorderworkflowstages.modulename = 'RefillApplication'";
        $result=$this->db->pquery($query,array($stagerecordid));
        $workflowsid=$this->db->query_result($result,0,'workflowsid');
        $currentflag=$this->db->query_result($result,0,'workflowstagesflag');
        $salesorderid=$this->db->query_result($result,0,'salesorderid');

        $currentflag=trim($currentflag);
        switch($currentflag){
            case 'DO_REFUND':
                if($recordModel->get('modulestatus')=='c_complete') {
                    $recordModel->dorefundsOrTransfers($recordid);
                }
                break;
            case 'AUDIT_VERIFICATION':
                //第二个节点指定审核人
                $this->AuditAuditNodeJump($recordid,$recordModel->get('workflowsid'),$recordModel->get('assigned_user_id'),'RefillApplication','RefillApplication',1);
                break;
            case 'TWO_VERIFICATION':
                //第二个节点指定审核人
                $this->AuditAuditNodeJump($recordid,$recordModel->get('workflowsid'),$recordModel->get('assigned_user_id'),'RefillApplication','RefillApplication',2);
                break;
            case 'REVOKERELATION':
                //回款解除关联
                if($recordModel->get('modulestatus')=='c_complete') {
                    $recordModel->revokeRelation($recordid);
                }
                break;
            case 'GUARANTY_NODE'://财务充值
            case 'FINANCE_RECHARGE'://财务充值
                $rechargefinishtime = date('Y-m-d H:i:s');
                $this->db->pquery("UPDATE vtiger_refillapplication SET rechargefinishtime=? WHERE refillapplicationid=?",array($rechargefinishtime,$recordid));
                //销账系统
                $recordModel = Vtiger_Record_Model::getInstanceById($recordid, 'RefillApplication');
                $refillapplicationno = $recordModel->get('refillapplicationno');
                $rechargesource = $recordModel->get('rechargesource');
                $grossadvances = $recordModel->get('grossadvances');
                if (in_array($rechargesource, array('Accounts', 'Vendors', 'NonMediaExtraction'))) {

                    $url = $this->url.'/write-off/mediaRecharge/updateMediaRecharge2';
                    $params = [
                        "code" => $refillapplicationno, //申请单编号
                        'rechargeTime' => $rechargefinishtime,//财务充值时间
                        'advanceMoney' => $grossadvances,//垫款金额
                    ];

                    global $current_user;
                    $userid = $current_user->id;
                    $params = json_encode($params);
                    $contractsRecordModel=Vtiger_Record_Model::getCleanInstance('RefillApplication');
                    $header = array(CURLOPT_HTTPHEADER=>array(
                        "Content-Type:application/json",
                        "userId:".$userid
                    ));
                    $res = $contractsRecordModel->https_requestcomm($url,$params,$header,true);
                    $resData = json_decode($res, true);
//            dd(compact('params','url','res','header'));
                    if ($resData['code'] == '0') {
                        $this->log->debug("财务充值时间更新成功，params->" . $params .'&&' . $url);
                    } else {
                        $this->log->debug("财务充值时间更新失败，params->" . $params .'&&' . $url);
                    }
                }

                // cxh   start  2020-04-15
                $paymentperiod=$recordModel->get("paymentperiod");
                $rechargesource=$recordModel->get("rechargesource");
                if($paymentperiod=='payfirst' && in_array($rechargesource,array('Vendors','TECHPROCUREMENT','PreRecharge','NonMediaExtraction'))){
                    $dates=date("Y-m-d H:i:s");
                    $this->db->pquery("UPDATE vtiger_refillapplication SET paymentdate=? WHERE refillapplicationid=? ",array($dates,$salesorderid));
                }else if($rechargesource=='PACKVENDORS'){
                    $dates=date("Y-m-d H:i:s");
                    $refillapplicationids="SELECT
                          GROUP_CONCAT(vtiger_refillapplication.refillapplicationid) as refillapplicationid
                          FROM `vtiger_packvendorlist`
                          LEFT JOIN `vtiger_refillapplication` ON vtiger_refillapplication.refillapplicationid=vtiger_packvendorlist.refillapplicationids
                          LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_refillapplication.refillapplicationid
                          LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_refillapplication.accountid
                          LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_refillapplication.servicecontractsid
                          WHERE vtiger_crmentity.deleted=0
                          AND vtiger_packvendorlist.prefillapplicationid=?
                          AND vtiger_packvendorlist.deleted=0";
                    $refillapplicationids=$this->db->pquery($refillapplicationids,array($salesorderid));
                    $refillapplicationids=$this->db->query_result_rowdata($refillapplicationids,0);
                    $this->db->pquery("UPDATE vtiger_refillapplication SET paymentdate=? WHERE refillapplicationid IN(".$refillapplicationids['refillapplicationid'].")",array($dates));
                }
                // cxh   end
                break;
            case 'FINANCE_MANAGER':
                if($recordModel->get('rechargesource')=='INCREASE'){
                    if($recordModel->get('granttype')=='virtrefund'){
                        $this->addReceivedPayments($recordModel);
                    }
                }
                break;
            case  'CONTRACTCHANGE_A':
                $sql=" SELECT  r.*,olda.advancesmoney as oldaadvancesmoney,newa.advancesmoney as newadvancesmoney,r.refillapplicationno,r.newaccount_name  FROM vtiger_refillapplication as r LEFT JOIN vtiger_account as  olda ON olda.accountid=r.accountid LEFT JOIN vtiger_account as newa ON newa.accountid=r.newaccountid  WHERE  refillapplicationid=? ";
                $result = $this->db->pquery($sql,array($salesorderid));
                $data = $this->db->query_result_rowdata($result,0);
                global $current_user;
                $currentTime = date('Y-m-d H:i:s');
                //服务合同变更
                if($data['changecontracttype']=='ServiceContracts'){
                     //查询需要变更的充值申请单列表
                     $needSql=" SELECT  * FROM vtiger_refillapplication  WHERE  refillapplicationid IN( SELECT detail_refillapplicationid FROM vtiger_changecontract_detail WHERE refillapplicationid=? )";
                     $needResult = $this->db->pquery($needSql,array($salesorderid));
                     $dates=date("Y-m-d H:i:s");
                     $isChanged = true;
                     while($rawData=$this->db->fetch_array($needResult)){
                         if($rawData['accountid']){
                              //如果要变更合同的客户相等
                              if ($rawData['accountid']==$data['newaccountid']){
                                  //更新需要变更合同的充值申请单
                                  $changeSql=" UPDATE vtiger_refillapplication  SET  servicecontractsid=? ,servicesigndate=? ,contractamount=?,iscontracted=?,modulestatus=?  WHERE  refillapplicationid =? ";
                                  $this->db->pquery($changeSql,array($data['newcontractsid'],$data['newservicesigndate'],$data['newcontractamount'],$data['newiscontracted'],'c_complete',$rawData['refillapplicationid']));
                                  // 修改变充值申请单更记录
                                  $id = $this->db->getUniqueId('vtiger_modtracker_basic');
                                  $this->db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                                      array($id, $rawData['refillapplicationid'], 'RefillApplication', $current_user->id,$currentTime, 0));
                                  $this->db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?),(?,?,?,?),(?,?,?,?),(?,?,?,?),(?,?,?,?)',
                                      Array($id, 'servicecontractsid',$rawData['servicecontractsid'],$data['newcontractsid'],
                                            $id, 'servicesigndate',$rawData['servicesigndate'],$data['newservicesigndate'],
                                            $id, 'contractamount',$rawData['contractamount'],$data['newcontractamount'],
                                            $id, 'iscontracted',$rawData['iscontracted'],$data['newiscontracted'],
                                            $id, 'modulestatus',$rawData['modulestatus'],'c_complete'));
                              }else{
                                  //目标客户/原客户垫款变更后垫款金额
                                  $afteroldaadvancesmoney = $data['oldaadvancesmoney']-$data['grossadvances'];
                                  $afternewadvancesmoney  = $data['newadvancesmoney']+$data['grossadvances'];
                                  // 如果客户的垫款金额 已经修改了 则不需要再修改了。
                                  if($isChanged){
                                      $afteroldaadvancesmoney = $afteroldaadvancesmoney." (".$data['refillapplicationno']."合同客户变更)";
                                      //原客户垫款额修改advancesmoney
                                      $oldAdvancesSql= " UPDATE vtiger_account SET  advancesmoney=advancesmoney-? WHERE accountid=? ";
                                      $this->db->pquery($oldAdvancesSql,array($data['grossadvances'],$data['accountid']));
                                      $id = $this->db->getUniqueId('vtiger_modtracker_basic');
                                      $this->db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                                          array($id,$data['accountid'], 'Accounts', $current_user->id,$currentTime, 0));
                                      $this->db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                                          Array($id, 'advancesmoney',$data['oldaadvancesmoney'],$afteroldaadvancesmoney));
                                      //目标客户垫款额修改
                                      $newAdvancesSql= " UPDATE vtiger_account SET  advancesmoney=advancesmoney+? WHERE accountid=? ";
                                      $this->db->pquery($newAdvancesSql,array($data['grossadvances'],$data['newaccountid']));
                                      $afternewadvancesmoney = $afternewadvancesmoney." (".$data['refillapplicationno']."合同客户变更)";
                                      // 修改变充值申请单更记录
                                      $id = $this->db->getUniqueId('vtiger_modtracker_basic');
                                      $this->db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                                          array($id,$data['newaccountid'], 'Accounts', $current_user->id,$currentTime, 0));
                                      $this->db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                                          Array($id, 'advancesmoney',$data['newadvancesmoney'],$afternewadvancesmoney));
                                      $isChanged = false;
                                  }
                                  //更新需要变更合同的充值申请单
                                  $changeSql=" UPDATE vtiger_refillapplication  SET  servicecontractsid=? ,servicesigndate=? ,contractamount=?,accountid=?,customertype=?,iscontracted=?,modulestatus=?  WHERE   refillapplicationid =? ";
                                  $this->db->pquery($changeSql,array($data['newcontractsid'],$data['newservicesigndate'],$data['newcontractamount'],$data['newaccountid'],$data['newcustomertype'],$data['newiscontracted'],'c_complete',$rawData['refillapplicationid']));
                                  // 修改变充值申请单更记录
                                  $id = $this->db->getUniqueId('vtiger_modtracker_basic');
                                  $this->db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                                      array($id, $rawData['refillapplicationid'], 'RefillApplication', $current_user->id,$currentTime, 0));
                                  $this->db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?),(?,?,?,?),(?,?,?,?),(?,?,?,?),(?,?,?,?),(?,?,?,?),(?,?,?,?)',
                                      Array($id,'servicecontractsid',$rawData['servicecontractsid'],$data['newcontractsid'],
                                          $id, 'servicesigndate',$rawData['servicesigndate'],$data['newservicesigndate'],
                                          $id, 'contractamount',$rawData['contractamount'],$data['newcontractamount'],
                                          $id, 'iscontracted',$rawData['iscontracted'],$data['newiscontracted'],
                                          $id, 'modulestatus',$rawData['modulestatus'],'c_complete',
                                          $id, 'accountid',$rawData['accountid'],$data['newaccountid'],
                                          $id,'customertype',$rawData['customertype'],$data['newcustomertype']));
                              }
                         }
                     }
                //供应商合同变更
                }elseif($data['changecontracttype']=='SupplierContracts'){
                     // 查询需要变更的充值申请单列表
                     $needSql=" SELECT  *  FROM vtiger_refillapplication  WHERE  refillapplicationid IN( SELECT detail_refillapplicationid FROM vtiger_changecontract_detail WHERE refillapplicationid=? )";
                     $needResult = $this->db->pquery($needSql,array($salesorderid));
                     while($rawData=$this->db->fetch_array($needResult)){
                         //更新需要变更合同的充值申请单 详情
                         $changeSql=" UPDATE vtiger_rechargesheet  SET  suppliercontractsid=? ,signdate=? ,havesignedcontract=?  WHERE   refillapplicationid =? ";
                         $this->db->pquery($changeSql,array($data['newcontractsid'],$data['newservicesigndate'],$data['newiscontracted'],$rawData['refillapplicationid']));
                         //更新需要变更合同的充值申请单
                         $changeSql=" UPDATE vtiger_refillapplication  SET modulestatus=?  WHERE   refillapplicationid =? ";
                         $this->db->pquery($changeSql,array('c_complete',$rawData['refillapplicationid']));
                     }
                }
                //更新变更申请单的审核完成时间 即 变更合同时间。
                $changeSql=" UPDATE vtiger_refillapplication  SET  finishedtime=?  WHERE   refillapplicationid =? ";
                $this->db->pquery($changeSql,array($dates,$data['refillapplicationid']));
                break;
                // cxh start 2020-04-15
            case 'gs':
            case 'UPDATEPAYMENTDATE':
                $paymentperiod=$recordModel->get("paymentperiod");
                $rechargesource=$recordModel->get("rechargesource");
                if($paymentperiod=='payfirst' && in_array($rechargesource,array('Vendors','TECHPROCUREMENT','PreRecharge','NonMediaExtraction'))){
                    $dates=date("Y-m-d H:i:s");
                    $this->db->pquery("UPDATE vtiger_refillapplication SET paymentdate=? WHERE refillapplicationid=? ",array($dates,$salesorderid));
                }
                break;// cxh end 2020-04-15
            case 'BILL_CONFIRM':
                $completedatetime=date('Y-m-d H:i:s');
                $this->db->pquery("UPDATE vtiger_refillapprayment SET completedatetime=? WHERE completedatetime IS NULL and refillapplicationid=?",array($completedatetime,$recordid));

            default:
        }
        if($recordModel->get('modulestatus')!='c_complete'){
            $this->db->pquery("UPDATE vtiger_refillapplication SET workflowsnode=(SELECT CONCAT(vtiger_salesorderworkflowstages.workflowstagesname,',') FROM vtiger_salesorderworkflowstages WHERE vtiger_salesorderworkflowstages.salesorderid=vtiger_refillapplication.refillapplicationid AND vtiger_salesorderworkflowstages.isaction=1) WHERE refillapplicationid=?",array($recordid));
            //发送微信企业号信息  cxh  2019-08-26 这个是旧的发送模板 这里注释掉
            /*$this->sendSns($stagerecordid);*/
        }

        if($recordModel->get('modulestatus')=='c_complete'&& $recordModel->get('rechargesource')=='PACKVENDORS'){
            //$query="UPDATE vtiger_refillapplication,vtiger_packvendorlist SET ispayment='alreadypaid',rechargefinishtime='".date('Y-m-d H:i:s')."' WHERE vtiger_packvendorlist.refillapplicationids=vtiger_refillapplication.refillapplicationid AND vtiger_packvendorlist.prefillapplicationid=?";
            $query="UPDATE vtiger_refillapplication,vtiger_packvendorlist SET ispayment='alreadypaid' WHERE vtiger_packvendorlist.refillapplicationids=vtiger_refillapplication.refillapplicationid AND vtiger_packvendorlist.prefillapplicationid=?";
            $this->db->pquery($query,array($recordid));
        }
        // cxh 2020-08-18 业绩数据处理
        //日志
        global $log;
        $log->info($recordid.'--RefillApplication:判断是否进入生产业绩流程'.' '.$recordModel->get('modulestatus').' '.$recordModel->get('rechargesource'));
        if($recordModel->get('modulestatus')=='c_complete' && $recordModel->get('rechargesource')=='Accounts'){
            $log->info($recordid.'--RefillApplication:进入生产业绩流程');
            //$queryc=" SELECT * FROM vtiger_servicecontracts WHERE servicecontractsid= ? LIMIT 1";
            //$resultdatapayments=$this->db->pquery($queryc,array($recordModel->get('servicecontractsid')));
            //$servicecontracts=$this->db->query_result_rowdata($resultdatapayments,0);

            // 谷歌充值生成业绩
            //if(in_array($servicecontracts['contract_type'],array('媒介.GOOGLE',"媒介.Yandex")) && $servicecontracts['parent_contracttypeid']==3){
            //$contractNo=$servicecontracts['contract_no'];
            //if(stripos($contractNo,'google')!==false || stripos($contractNo,'yandex')!==false){
                $productQuery='SELECT * FROM vtiger_rechargesheet WHERE refillapplicationid=? AND productid in(2137321,2138055)';
                $productResult=$this->db->pquery($productQuery,array($recordid));
                if($this->db->num_rows($productResult)){
                    $query='SELECT 1 FROM `vtiger_activationcode` WHERE `status` in(0,1) AND contractid=?';
                    $activationcodeResult=$this->db->pquery($query,array($recordModel->get('servicecontractsid')));
                    if($this->db->num_rows($activationcodeResult)){//存在web订单则不生成业绩

                    }else{
                        $matchreceivements_BasicAjax_Action= new Matchreceivements_BasicAjax_Action();
                        $params['refillapplicationid']=$recordid;
                        $receivedpayments=$this->db->pquery(" SELECT  receivedpaymentsid  FROM vtiger_refillapprayment WHERE refillapplicationid=?  LIMIT 1 ",array($recordid) );
                        if($this->db->num_rows($receivedpayments)) {
                            $receivedpayments = $this->db->query_result_rowdata($receivedpayments, 0);
                            $rp=$matchreceivements_BasicAjax_Action->getReceivedpaymentsInfo($receivedpayments['receivedpaymentsid']);
                            global $adb,$current_user;
                            $remark='';
                            $matchdate=date('Y-m-d');
                            $shareuser=0;
                            $currentid=$current_user->id;
                            $total=$rp['unit_price'];
                            $matchreceivements_BasicAjax_Action->rechargeCalculation($rp,$rp['servicecontractsid'],$receivedpayments['receivedpaymentsid'],$shareuser,$total,$currentid,$remark,$adb,$params,$matchdate);
                            //$matchreceivements_BasicAjax_Action->commonInsertAchievementallotStatistic($receivedpayments['receivedpaymentsid'], 0, 0, 0, $recordModel->get('servicecontractsid'), $params);
                        }
                    }

                }
            //}
        }
        /**
         * 旧的审核消息提醒 这里注释掉   cxh 2019-08-26
         */
       /* $this->getSendWinXinUser($recordid);*/
        // cxh 2019-08-02 添加 如果该审核需要修改审核列表中的modulestatus（审核流程状态）审核完后走下面代码
        $params['workflowsid']=$workflowsid;
        $params['salesorderid']=$request->get('record');
        $this->hasAllAuditorsChecked($params);
        //虚拟回款录入
        if($recordModel->get('modulestatus')=='c_complete'&& $recordModel->get('rechargesource')=='INCREASE'){
            //销账虚拟回款
            global $current_user;
            $userid = $current_user->id;
            $recordModel = Vtiger_Record_Model::getInstanceById($recordid, 'RefillApplication');
            global $adb;

            $createName = getUserInfo($recordModel->get('assigned_user_id'))['last_name'];
            $createUser = $recordModel->get('assigned_user_id');

            $query="select * from vtiger_rechargesheet where refillapplicationid = ?";
            $result=$adb->pquery($query,array($recordid));
            $nums = $adb->num_rows($result);
            $params = [];
            if ($nums > 0) {
                for ($i = 0; $i < $nums; $i++) {
                    $cashincrease=$adb->query_result($result,$i,'cashincrease');
                    $maccountid_name=$adb->query_result($result,$i,'maccountid_name');
                    $remark=$adb->query_result($result,$i,'mstatus');
                    $params['erpReturnedMoneyInputListVOList'][$i] = [
                        "createName" => $createName, //赠款申请单的申请人
                        "createUser" => $createUser, //赠款申请单的申请人ID
                        "entryDate" => date("Y-m-d"), //审批完成日期
                        "entryMoney" => $cashincrease, //赠充现金
                        "name" => $maccountid_name, //赠款客户名称
                        "paymentMethod" => 2, //回款方式 2：虚拟回款
                        "remark" => $remark //备注
                    ];

                }

            }

            $params = json_encode($params);
            $url = $this->url."/write-off/returnedMoney/erpVirtualReturnedMoneyInput";
            $contractsRecordModel=Vtiger_Record_Model::getCleanInstance('RefillApplication');
            $header = array(CURLOPT_HTTPHEADER=>array(
                "Content-Type:application/json",
                "userId:".$userid
            ));
            $res = $contractsRecordModel->https_requestcomm($url,$params,$header,true);
//            dd(compact('params','url','res','header'));
            $resData = json_decode($res, true);
            if ($resData['code'] != '0') {
                $this->log->debug("虚拟回款录入失败，params->" . $params);
            }else{
                $this->log->debug("虚拟回款录入成功，params->" . $params);
            }
        }
        //销账系统状态更新
        $recordModel = Vtiger_Record_Model::getInstanceById($recordid, 'RefillApplication');
        $refillapplicationno = $recordModel->get('refillapplicationno');
        $rechargesource = $recordModel->get('rechargesource');
        $modulestatus = $recordModel->get('modulestatus');
        if (in_array($rechargesource, array('Accounts', 'Vendors', 'NonMediaExtraction'))) {

            $url = $this->url.'/write-off/mediaRecharge/updateMediaRecharge2';
            $params = [
                "code" => $refillapplicationno, //申请单编号
                'status' => $modulestatus,//状态
            ];

            global $current_user;
            $userid = $current_user->id;
            $params = json_encode($params);
            $contractsRecordModel=Vtiger_Record_Model::getCleanInstance('RefillApplication');
            $header = array(CURLOPT_HTTPHEADER=>array(
                "Content-Type:application/json",
                "userId:".$userid
            ));
            $res = $contractsRecordModel->https_requestcomm($url,$params,$header,true);
            $resData = json_decode($res, true);
//            dd(compact('params','url','res','header'));
            if ($resData['code'] == '0') {
                $this->log->debug("状态更新成功，params->" . $params .'&&' . $url);
            } else {
                $this->log->debug("状态更新失败，params->" . $params .'&&' . $url);
            }
        }
    }
    /**
     * 推送微信信息
     */
    public function sendSns($stagerecord) {
        $db = PearDatabase::getInstance();
        $sql = "select * from vtiger_salesorderworkflowstages where salesorderworkflowstagesid = ?";
        $result = $db->pquery($sql, array($stagerecord));
        $data = $db->fetch_row($result);
        $user_data = array();
        $salesorder_data = array();
        $refillapplication_data = array();
        if (!empty($data)) {
            $sql = "select * from vtiger_users where id = ?";
            $result = $db->pquery($sql, array($data['smcreatorid']));
            $user_data = $db->fetch_row($result);

            $sql = "select * from vtiger_salesorderworkflowstages where salesorderid = ? and isaction=1";
            $result = $db->pquery($sql, array($data['salesorderid']));
            $salesorder_data = $db->fetch_row($result);

            $sql = "select * from vtiger_refillapplication where refillapplicationid=?";
            $result = $db->pquery($sql, array($data['salesorderid']));
            $refillapplication_data = $db->fetch_row($result);
        }
        //如果已经有了，就更新如果需要，跟上边是同步进行的，
        if (!empty($salesorder_data) && !empty($user_data[last_name]) && !empty($salesorder_data[workflowstagesname])) {
            $email = $user_data[email1];
            $rechargesource = !empty($refillapplication_data[rechargesource]) ? vtranslate($refillapplication_data[rechargesource], 'RefillApplication'):'空';
            $content = "消息模板：\n 申请人:$user_data[last_name] \n 申请单号:$refillapplication_data[refillapplicationno] \n 充值来源:$rechargesource \n 待审核节点:$salesorder_data[workflowstagesname]";
            $re = $this->setweixincontracts(array('email'=>trim($email),'content'=>$content,'flag'=>6));
        }
        return;
    }
    /**
     * 前置事件
     * @param Vtiger_Request $request
     */
    public function workflowcheckbefore(Vtiger_Request $request){
    	/*$stagerecordid=$request->get('stagerecordid');
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
    				echo json_encode($resultaa);
    				exit;
    			}
    			break;
    		default :
    			break;
    	}*/
    }

    /**
     * 工作流打回处理事件
     * @param Vtiger_Request $request
     */
    public function backallBefore(Vtiger_Request $request){
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
                AND vtiger_salesorderworkflowstages.modulename = 'RefillApplication'";
        $result=$this->db->pquery($query,array($stagerecordid));
        $currentflag=$this->db->query_result($result,0,'workflowstagesflag');
        $workflowsid=$this->db->query_result($result,0,'workflowsid');
        $currentflag=trim($currentflag);
        switch($currentflag){
            case 'DO_REFUND':
                break;
            case 'BILL_CONFIRM'://提单人确认
                $resultaa['success'] = 'false';
                $resultaa['error']['message'] = "： 充值单已充值不允许打回。";
                //若果是移动端请求则走这个返回
                if( $request->get('isMobileCheck')==1){
                    return $resultaa;
                }else{
                    echo json_encode($resultaa);
                    exit;
                }
                break;
            case "REVOKERELATION":
                break;
            default:
                $this->saveRefillappSetAdvancesmoney($recordid, 'backall');
        }
        $query='SELECT auditorid FROM vtiger_salesorderworkflowstages WHERE modulename=\'RefillApplication\' AND isaction=2  AND salesorderid=? AND workflowsid=?';


        $result=$this->db->pquery($query,array($recordid,$workflowsid));
        $num=$this->db->num_rows($result);
        if($num){
            $arr=array();
            for($i=0;$i<$num;$i++){
                $arr['userid'][]=$this->db->query_result($result,$i,'auditorid');
            }
            if(!empty($arr['userid'])){
                global $current_user;
                $recordModel = Vtiger_Record_Model::getInstanceById($recordid, 'RefillApplication',true);
                $entity=$recordModel->entity->column_fields;
                $arr['refillapplicationno']=$entity['refillapplicationno'];
                $arr['username']=$current_user->last_name;
                RefillApplication_Record_Model::backallsendmail($request,$arr);
            }
        }
    }
    /**
     * 工单打回的后置事件处理机制
     * 微信提醒
     * @param Vtiger_Request $request
     */
    public function backallAfter(Vtiger_Request $request){
        $recordid=$request->get('record');
        $recordModel = Vtiger_Record_Model::getInstanceById($recordid, 'RefillApplication',true);
        $entity=$recordModel->entity->column_fields;
        $user = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile($entity['assigned_user_id']);
        $email=$current_user->column_fields['email1'];
        // cxh 2019-08-26 注释旧的打回 提醒
        /*if($this->checkEmail(trim($email))){
            $content='<div class=\"gray\">'.date('Y年m月d日').'</div>与您相关的充值申请单被打回<div class=\"highlight\">单号为:'.$entity['refillapplicationno'].'--'.$_REQUEST['reject'].'</div>请及时处理';
            //$email='steel.liu@71360.com';
            $dataurl='http://m.crm.71360.com/index.php?module=RefillApplication&action=one&id='.$recordid;
            $this->setweixincontracts(array('email'=>trim($email),'description'=>$content,'dataurl'=>$dataurl,'title'=>'充值申请单打回','flag'=>7));
        }*/
        $stagerecordid=$request->get('isrejectid');
        $query="SELECT
                    vtiger_workflowstages.workflowstagesflag,
                    vtiger_workflowstages.workflowsid
                FROM
                    `vtiger_salesorderworkflowstages`
                LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid
                WHERE
                    vtiger_salesorderworkflowstages.salesorderworkflowstagesid = ?
                AND vtiger_salesorderworkflowstages.modulename = 'RefillApplication'";
        $result=$this->db->pquery($query,array($stagerecordid));

        $workflowsid=$this->db->query_result($result,0,'workflowsid');
        $currentflag=$this->db->query_result($result,0,'workflowstagesflag');

        $currentflag=trim($currentflag);
        switch($currentflag){
            case 'DO_REFUND':
                $this->db->pquery("UPDATE vtiger_refillapplication SET modulestatus='c_complete',isbackwash=0 WHERE refillapplicationid=?",array($recordid));
                $this->db->pquery("UPDATE `vtiger_rubricrechargesheet` SET deleted=1,isbackwash=0 WHERE refillapplicationid=? AND isbackwash=1",array($recordid));
                $this->db->pquery("UPDATE vtiger_refillredrefund SET deleted=1,isshow=0 WHERE refillapplicationid=?",array($recordid));
                $this->db->pquery("UPDATE vtiger_refillapprayment SET deleted=1 WHERE refillapplicationid=? AND iscomplete=0 AND receivedstatus='SupplierRefund'",array($recordid));
                break;
            case "REVOKERELATION":
                $this->db->pquery("UPDATE vtiger_refillapplication SET modulestatus='c_complete' WHERE refillapplicationid=?",array($recordid));
                $this->db->pquery("UPDATE `vtiger_refillapprayment` SET receivedstatus='normal' WHERE refillapplicationid=?",array($recordid));
                break;
            default:
                $this->backwashReceivedPayments($recordid);//释放回款
        }
        if($currentflag!='REVOKERELATION'){
            //更新是否垫款标志 gaocl add 2018/05/11
            $this->db->pquery("UPDATE vtiger_refillapplication SET iscushion=0 WHERE refillapplicationid=?", array($recordid));
        }
        $this->db->pquery("DELETE FROM `vtiger_salesorderworkflowstages` WHERE vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename='RefillApplication' AND vtiger_salesorderworkflowstages.workflowsid=?",array($recordid,$workflowsid));
        if($entity['rechargesource']=='PACKVENDORS'){
            $query="UPDATE vtiger_refillapplication,vtiger_packvendorlist SET ispayment='unpaid' WHERE vtiger_packvendorlist.refillapplicationids=vtiger_refillapplication.refillapplicationid AND vtiger_packvendorlist.prefillapplicationid=?";
            $this->db->pquery($query,array($recordid));
        }
        if($entity['rechargesource']=='contractChanges'){
            $sql=" UPDATE vtiger_refillapplication SET  vtiger_refillapplication.modulestatus='c_complete' WHERE  refillapplicationid IN( SELECT vtiger_changecontract_detail.detail_refillapplicationid FROM vtiger_changecontract_detail WHERE vtiger_changecontract_detail.refillapplicationid=? ) ";
            $this->db->pquery($sql,array($recordid));
        }

        //销账系统状态更新
        $recordModel = Vtiger_Record_Model::getInstanceById($recordid, 'RefillApplication');
        $refillapplicationno = $recordModel->get('refillapplicationno');
        $rechargesource = $recordModel->get('rechargesource');
        $modulestatus = $recordModel->get('modulestatus');
        if (in_array($rechargesource, array('Accounts', 'Vendors', 'NonMediaExtraction'))) {

            $url = $this->url.'/write-off/mediaRecharge/updateMediaRecharge2';
            $params = [
                "code" => $refillapplicationno, //申请单编号
                'status' => $modulestatus,//状态
            ];

            global $current_user;
            $userid = $current_user->id;
            $params = json_encode($params);
            $contractsRecordModel=Vtiger_Record_Model::getCleanInstance('RefillApplication');
            $header = array(CURLOPT_HTTPHEADER=>array(
                "Content-Type:application/json",
                "userId:".$userid
            ));
            $res = $contractsRecordModel->https_requestcomm($url,$params,$header,true);
            $resData = json_decode($res, true);
//            dd(compact('params','url','res','header'));
            if ($resData['code'] == '0') {
                $this->log->debug("状态更新成功，params->" . $params .'&&' . $url);
            } else {
                $this->log->debug("状态更新失败，params->" . $params .'&&' . $url);
            }
        }
    }
    /**
     * @param $recordid
     */
    private function makeRefillapplicationWorkflows($recordid)
    {
        // 添加工作流
        $advancesmoney = 0;
        /*$result = $this->db->pquery("SELECT advancesmoney  FROM vtiger_account WHERE accountid =(SELECT vtiger_refillapplication.accountid FROM vtiger_refillapplication WHERE vtiger_refillapplication.refillapplicationid=?) ",array($recordid));
        if ($result && $this->db->num_rows($result) > 0) {
            $row = $this->db->fetch_array($result);
            $advancesmoney =  $row['advancesmoney'];
        }*/
        $recordModel = Vtiger_Record_Model::getInstanceById($recordid, 'RefillApplication');
        $totalrecharge = $recordModel->get('totalrecharge');//使用回款总款
        $actualtotalrecharge = $recordModel->get('actualtotalrecharge');//实际现金充值总额
        $paymentperiod = $recordModel->get('paymentperiod');//付款账期
        $rechargesource = $recordModel->get('rechargesource');//模块
        if (in_array($rechargesource, array('TECHPROCUREMENT', 'NonMediaExtraction'))) {
            $actualtotalrecharge = $recordModel->get('totalreceivables');//实际应付现金充值总额
        }
        $advancesmoney = $actualtotalrecharge - $totalrecharge;//只针对该单走
        if ($rechargesource == 'PACKVENDORS') {
            $query = "UPDATE vtiger_refillapplication,vtiger_packvendorlist SET ispayment='inpayment' WHERE vtiger_packvendorlist.refillapplicationids=vtiger_refillapplication.refillapplicationid AND vtiger_packvendorlist.prefillapplicationid=?";
            $this->db->pquery($query, array($recordid));
        }
        //获取工作流id
        $workflows_sql = "SELECT * FROM vtiger_codemaster WHERE codetype='refillapplication_workflows' AND rechargesource=?";
        $workflows_result = $this->db->pquery($workflows_sql, array($rechargesource));
        $num = $this->db->num_rows($workflows_result);
        $arr_workflows = array();
        if ($num) {
            for ($i = 0; $i < $num; $i++) {
                //if(in_array($rechargesource,array("Vendors","PreRecharge","NonMediaExtraction"))){
                $paymentper = trim($this->db->query_result($workflows_result, $i, 'paymentperiod'));
                $paymentper = !empty($paymentper) ? '_' . $paymentper : '';
                $arr_workflows[$this->db->query_result($workflows_result, $i, 'codename') . $paymentper] = $this->db->query_result($workflows_result, $i, 'codevalue');

                /*}else{
                    $arr_workflows[$this->db->query_result($workflows_result, $i, 'codename')] = $this->db->query_result($workflows_result, $i, 'codevalue');
                }*/
            }
        }
        if (empty($arr_workflows) || count($arr_workflows) == 0) return;

        // 申请单审核流处理
        //有垫款：走二级审核（邮件提醒审核人）
        //无垫款：走一级审核（邮件同时提醒两级审核人）
        //线上：573652 无垫款  573645  有垫款充值申请流程
        $sql = ",ispayment=''";
        if (in_array($rechargesource, array("Vendors", "PreRecharge", "NonMediaExtraction"))) {
            if ($paymentperiod == 'payfirst') {
                if ($advancesmoney > 0) {
                    //有垫款：走二级审核（邮件提醒审核人）
                    //一级审核->充值平台负责人审核->二级审核->财务充值
                    // $workflowsid = "573645";//线上;
                    //$workflowsid = "398071";//测试
                    $workflowsid = $arr_workflows["advancesmoney_yes_payfirst"];
                } else {
                    //无垫款：走一级审核（邮件同时提醒两级审核人）
                    //一级审核->充值平台负责人审核->财务充值
                    //$workflowsid = "573652";//线上;
                    //$workflowsid = '398076';//测试
                    $workflowsid = $arr_workflows["advancesmoney_no_payfirst"];
                }
            } else {
                $sql = ",ispayment='unpaid'";
                if ($advancesmoney > 0) {
                    //有垫款：走二级审核（邮件提醒审核人）
                    //一级审核->充值平台负责人审核->二级审核->财务充值
                    // $workflowsid = "573645";//线上;
                    //$workflowsid = "398071";//测试
                    $workflowsid = $arr_workflows["advancesmoney_yes_postpayment"];
                } else {
                    //无垫款：走一级审核（邮件同时提醒两级审核人）
                    //一级审核->充值平台负责人审核->财务充值
                    //$workflowsid = "573652";//线上;
                    //$workflowsid = '398076';//测试
                    $workflowsid = $arr_workflows["advancesmoney_no_postpayment"];
                }
            }
            //如果是合同变更申请
        } elseif ($rechargesource == 'contractChanges') {
            // 如果是数组中的则走工作流A
            if (in_array($_REQUEST['oldrechargesource'], array('Accounts', 'COINRETURN'))) {
                $workflowsid = $arr_workflows['advancesmoney_yes_first'];
                // 如果是数组中的则走工作流B
            } elseif (in_array($_REQUEST['oldrechargesource'], array('Vendors', 'NonMediaExtraction', 'PreRecharge', 'TECHPROCUREMENT'))) {
                $workflowsid = $arr_workflows['advancesmoney_no_two'];
            }
        } else {
            $flow_state = $recordModel->get('flow_state');
            if ($rechargesource == 'Accounts' && $flow_state == 'QuickCharge') {
                if ($advancesmoney > 0) {
                    $workflowsid = $arr_workflows["advancesmoney_yes_quickcharge"];
                } else {
                    $workflowsid = $arr_workflows["advancesmoney_no"];
                }
            } elseif ($rechargesource == 'COINRETURN') {
                $query = 'SELECT productid,accountrebatetype,discount FROM `vtiger_rechargesheet` WHERE refillapplicationid=? AND deleted=0';
                $COINRETURNDataResult = $this->db->pquery($query, array($recordid));
                $productid = 0;
                $accountrebatetype = '';
                $discount = 0;
                $COINRETURNDataFlag = true;
                $COINRETURNDataFlag1 = true;
                $productids = array();
                while ($row = $this->db->fetch_array($COINRETURNDataResult)) {
                    if (!in_array($row['productid'], $productids)) {
                        $productids[] = $row['productid'];
                    }
                    if ($productid == 0) {
                        $productid = $row['productid'];
                        $accountrebatetype = $row['accountrebatetype'];
                        $discount = $row['discount'];
                    }
                    if ($productid != $row['productid']) {
                        $COINRETURNDataFlag1 = false;
                    }
                    if (/*$productid!=$row['productid'] ||*/
                        $accountrebatetype != $row['accountrebatetype'] ||
                        $discount != $row['discount']) {
                        $COINRETURNDataFlag = false;
                        //break;
                    }
                }
                $conversiontype = $recordModel->get('conversiontype');//转充类型
                if ($COINRETURNDataFlag && $COINRETURNDataFlag1) {
                    $workflowsid = $arr_workflows["advancesmoney_no_" . $conversiontype];
                } else {
                    if ($COINRETURNDataFlag1) {//true
                        if (!$COINRETURNDataFlag) {//false
                            $workflowsid = $arr_workflows["advancesmoney_yes_" . $conversiontype];
                        }
                    } else {//false
                        if ($COINRETURNDataFlag) {//true
                            $workflowsid = $arr_workflows["advancesmoney_yesnew_" . $conversiontype];
                        } else {
                            $workflowsid = $arr_workflows["advancesmoney_yes_" . $conversiontype];
                        }

                    }

                }
                if ($conversiontype == 'ProductProvider') {
                    $products = $this->db->pquery('SELECT vtiger_products.productname, vtiger_products.productcategory, vtiger_products.realprice, vtiger_products.unit_price, vtiger_crmentity.smownerid,vtiger_products.extracost, vtiger_products.productid FROM vtiger_products INNER JOIN vtiger_crmentity ON vtiger_products.productid = vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted = 0 AND vtiger_products.productid in(' . implode(',', $productids) . ')', array());
                    $checkarray = array();
                    if ($this->db->num_rows($products)) {
                        while ($productRow = $this->db->fetch_array($products)) {
                            $checkarray[] = array('workflowstagesname' => $productRow['productname'] . '审核', 'smcreatorid' => $productRow['smownerid'], 'productid' => $productRow['productid']);
                        }
                    }
                    vglobal('checkproducts', $checkarray);
                }
            } elseif ($rechargesource == 'INCREASE') {
                if ($recordModel->get('granttype') == 'paymentout') {
                    $workflowsid = $arr_workflows["advancesmoney_yes"];
                } else {
                    $workflowsid = $arr_workflows["advancesmoney_no"];
                }
            } else {
                if ($advancesmoney > 0) {
                    //有垫款：走二级审核（邮件提醒审核人）
                    //一级审核->充值平台负责人审核->二级审核->财务充值
                    // $workflowsid = "573645";//线上;
                    //$workflowsid = "398071";//测试
                    $workflowsid = $arr_workflows["advancesmoney_yes"];
                } else {
                    //无垫款：走一级审核（邮件同时提醒两级审核人）
                    //一级审核->充值平台负责人审核->财务充值
                    //$workflowsid = "573652";//线上;
                    //$workflowsid = '398076';//测试
                    $workflowsid = $arr_workflows["advancesmoney_no"];
                }
            }
        }
        //include_once('data/CRMEntity.php');
        $on_focus = CRMEntity::getInstance('RefillApplication');
        $on_focus->makeWorkflows('RefillApplication', $workflowsid, $recordid, 'edit');
        global $current_user;
        //$departmentid=empty($current_user->departmentid)?'H1':$current_user->departmentid;
        $departmentid = $_SESSION['userdepartmentid'];
        $on_focus->setAudituid("RefillApplication", $departmentid, $recordid, $workflowsid);
        /*if(in_array($rechargesource,array('Vendors','NonMediaExtraction'))) {
            global $current_user;
            $needle='H283::';
            //$needletwo='H281::';
            $query='SELECT vtiger_departments.parentdepartment FROM vtiger_departments WHERE departmentid=?';
            //$result=$this->db->pquery($query,array($current_user->departmentid));
            $result=$this->db->pquery($query,array($departmentid));
            $data=$this->db->raw_query_result_rowdata($result,0);
            $parentdepartment=$data['parentdepartment'];
            $parentdepartment.='::';
            //if(strpos($parentdepartment,$needle)===false && strpos($parentdepartment,$needletwo)===false){
            if(strpos($parentdepartment,$needle)===false){
                $query='DELETE FROM `vtiger_salesorderworkflowstages` WHERE vtiger_salesorderworkflowstages.salesorderid =? AND vtiger_salesorderworkflowstages.modulename=\'RefillApplication\' AND vtiger_salesorderworkflowstages.workflowstagesflag=\'COMPANY_MEDIA\' AND vtiger_salesorderworkflowstages.isaction=1 AND vtiger_salesorderworkflowstages.workflowsid=?';
                $this->db->pquery($query,array($recordid,$workflowsid));
                $query='UPDATE `vtiger_salesorderworkflowstages` SET isaction=1 WHERE vtiger_salesorderworkflowstages.salesorderid =? AND vtiger_salesorderworkflowstages.modulename=\'RefillApplication\' AND vtiger_salesorderworkflowstages.sequence=2 AND vtiger_salesorderworkflowstages.workflowsid=? AND vtiger_salesorderworkflowstages.isaction=0';
                $this->db->pquery($query,array($recordid,$workflowsid));
            }else{
                //$query='UPDATE `vtiger_salesorderworkflowstages` SET ishigher=1,higherid=7629 WHERE vtiger_salesorderworkflowstages.salesorderid =? AND vtiger_salesorderworkflowstages.modulename=\'RefillApplication\' AND vtiger_salesorderworkflowstages.sequence=1 AND vtiger_salesorderworkflowstages.workflowsid=? AND vtiger_salesorderworkflowstages.isaction=1';
                //$this->db->pquery($query,array($recordid,$workflowsid));
            }
        }*/
        //如果合同变更申请
        if ($rechargesource == 'contractChanges') {
            $modulestatus = 'b_actioning';
            $query = "UPDATE vtiger_refillapplication SET workflowsid=?,modulestatus='b_actioning' WHERE refillapplicationid=?";
        } else {
            //更新申请单工作流
            $modulestatus = 'b_check';
            $query = "UPDATE vtiger_refillapplication SET modulestatus='b_check',workflowsid=?{$sql} WHERE refillapplicationid=?";
        }
        $this->db->pquery($query, array($workflowsid, $recordid));

        //-------------------获取公司主体，可依据服务合同主体或采购合同主体来判定审核的财务主管是谁-------------------------------------------
        if ($rechargesource != 'PACKVENDORS') {
            $temprecordid = $recordid;
            $thisResult = $this->db->pquery('SELECT (SELECT companycode FROM vtiger_servicecontracts WHERE vtiger_servicecontracts.servicecontractsid>0 AND vtiger_servicecontracts.servicecontractsid=vtiger_refillapplication.servicecontractsid) as companycode FROM vtiger_refillapplication WHERE refillapplicationid=?', array($recordid));
        } else {
            $thisResult = $this->db->pquery('SELECT vtiger_refillapplication.refillapplicationid,(SELECT companycode FROM vtiger_servicecontracts WHERE vtiger_servicecontracts.servicecontractsid>0 AND vtiger_servicecontracts.servicecontractsid=vtiger_refillapplication.servicecontractsid) as companycode FROM `vtiger_packvendorlist`
                                           LEFT JOIN vtiger_refillapplication ON vtiger_refillapplication.refillapplicationid=vtiger_packvendorlist.refillapplicationids WHERE prefillapplicationid=? AND deleted=0 LIMIT 1', array($recordid));
            $temprecordid = $thisResult->fields['refillapplicationid'];
        }
        $serviceCode = $thisResult->fields['companycode'];
        $query = 'SELECT vtiger_servicecontracts.companycode AS servicecompanycode,vtiger_suppliercontracts.companycode AS suppcompanycode FROM `vtiger_rechargesheet` LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_rechargesheet.mservicecontractsid 
            LEFT JOIN vtiger_suppliercontracts ON vtiger_suppliercontracts.suppliercontractsid=vtiger_rechargesheet.suppliercontractsid
            WHERE refillapplicationid=? AND deleted=0';
        $companycodeResult = $this->db->pquery($query, array($temprecordid));
        if ($this->db->num_rows($companycodeResult)) {
            if (!$serviceCode) {
                $serviceCode = $companycodeResult->fields['servicecompanycode'];
            }
            $supplierCode = $companycodeResult->fields['suppcompanycode'];
        }
        //---------------------------------------------end------------------------------------------------------------
        if(!$serviceCode&&!in_array($rechargesource, array('Vendors', 'NonMediaExtraction', 'TECHPROCUREMENT','PACKVENDORS'))){
            //如果还是没有服务主体，启用第二条件以采购合同为第一条件
            $serviceCode=$supplierCode;
        }
        if(!$serviceCode&&!$supplierCode){
            //都不存在，采用提单人所在公司主体
            $sql="select vtiger_invoicecompany.companycode from vtiger_refillapplication left join vtiger_crmentity on vtiger_refillapplication.refillapplicationid=vtiger_crmentity.crmid LEFT JOIN vtiger_users on vtiger_crmentity.smownerid=vtiger_users.id LEFT JOIN  vtiger_invoicecompany  on vtiger_invoicecompany.companyid=vtiger_users.companyid where refillapplicationid=?";
            $companycodeResult = $this->db->pquery($sql, array($temprecordid));
            $serviceCode=$companycodeResult->fields['companycode'];
        }
        $this->db->pquery('UPDATE vtiger_salesorderworkflowstages SET companycode=? WHERE salesorderid=?', array($serviceCode, $recordid));
        $sRecordModel = Vtiger_Record_Model::getCleanInstance('ServiceContracts');
        //5.6.1财务复审指定人
        if(in_array($serviceCode,$sRecordModel->financialReviewNode1)){
            $userid=15542;//袁蔚华
        }elseif(in_array($serviceCode,$sRecordModel->financialReviewNode2)){
            $userid=25067;//王娟娟
        }elseif(in_array($serviceCode,$sRecordModel->financialReviewNode3)){
            $userid=22306;//孟昭燕
        }elseif(in_array($serviceCode,$sRecordModel->financialReviewNode4)){
            $userid=11505;//刘媛媛
        }elseif(in_array($serviceCode,$sRecordModel->financialReviewNode5)){
            $userid=9726;//刘笑含
        }elseif(in_array($serviceCode,$sRecordModel->financialReviewNode6)){
            $userid=25135;//林旺
        }elseif(in_array($serviceCode,$sRecordModel->financialReviewNode7)){
            $userid=17630;//顾瑞娟
        }
        //服务合同处理
        $this->db->pquery("UPDATE vtiger_salesorderworkflowstages SET ishigher=1,higherid=? WHERE salesorderid=? AND workflowstagesflag ='TREASURER_CODE' and workflowstagesname='财务主管复核'", array($userid, $recordid));
        if (in_array($rechargesource, array('Vendors', 'NonMediaExtraction', 'TECHPROCUREMENT'))) {
            //如果是以上三个
            //5.6.1财务复审指定人
            if(in_array($supplierCode,$sRecordModel->financialReviewNode1)){
                $userid=15542;//袁蔚华
            }elseif(in_array($supplierCode,$sRecordModel->financialReviewNode2)){
                $userid=25067;//王娟娟
            }elseif(in_array($supplierCode,$sRecordModel->financialReviewNode3)){
                $userid=22306;//孟昭燕
            }elseif(in_array($supplierCode,$sRecordModel->financialReviewNode4)){
                $userid=11505;//刘媛媛
            }elseif(in_array($supplierCode,$sRecordModel->financialReviewNode5)){
                $userid=9726;//刘笑含
            }elseif(in_array($supplierCode,$sRecordModel->financialReviewNode6)){
                $userid=25135;//林旺
            }elseif(in_array($supplierCode,$sRecordModel->financialReviewNode7)){
                $userid=17630;//顾瑞娟
            }
            $this->db->pquery("UPDATE vtiger_salesorderworkflowstages SET companycode=? WHERE salesorderid=? and workflowstagesflag ='gs'", array($supplierCode, $recordid));
            $this->db->pquery("UPDATE vtiger_salesorderworkflowstages SET companycode=?,ishigher=1,higherid=? WHERE salesorderid=? and workflowstagesflag ='TREASURER_CODE' and workflowstagesname='财务主管复核' ", array($supplierCode,$userid, $recordid));
        }

    }
    /**
     * 取得当前充值申请单的要提醒的用户信息
     * @param $recordid
     */
    private function getSendWinXinUser($recordid){
        /**
         * 充值申请单的微信消息提醒
         */

        //$recordid = $request->get('record');
        $query="SELECT vtiger_salesorderworkflowstages.workflowstagesid,workflowsid,ishigher,higherid,platformids,workflowstagesname FROM vtiger_salesorderworkflowstages WHERE isaction=1 AND salesorderid= ?
                AND vtiger_salesorderworkflowstages.modulename = 'RefillApplication'";
        $result=$this->db->pquery($query,array($recordid));
        $num=$this->db->num_rows($result);
        $salesorderworkflowstages_row=$this->db->fetch_row($result);
        if($num){

            $recordModel = Vtiger_Record_Model::getInstanceById($recordid, 'RefillApplication');
            $entity = $recordModel->entity->column_fields;
            for($j=0;$j<$num;$j++){
                $workflowstagesid=$this->db->query_result($result,$j,'workflowstagesid');
                $workflowsid=$this->db->query_result($result,$j,'workflowsid');
                $ishigher=$this->db->query_result($result,$j,'ishigher');
                $higherid=$this->db->query_result($result,$j,'higherid');
                if($ishigher==1 && $higherid>0){
                    //有指定的人员审核
                    $query="SELECT vtiger_users.email1,vtiger_users.last_name  FROM vtiger_users LEFT JOIN vtiger_user2role ON vtiger_users.id=vtiger_user2role.userid WHERE vtiger_users.`status`='Active' AND vtiger_users.id=?";
                    $userresult=$this->db->pquery($query,array($higherid));
                    $usernum=$this->db->num_rows($userresult);
                    $user_row=$this->db->fetch_row($userresult);
                    if($usernum){
                        $email=$this->db->query_result($userresult,0,'email1');
                        if($this->checkEmail(trim($email))){
                            $rechargesource = !empty($entity[rechargesource]) ? vtranslate($entity[rechargesource], 'RefillApplication'):'空';
//                            $content='与您相关的充值申请单需要审核,单号为:'.$entity['refillapplicationno'].'请及时处理';
                                //$content = "消息模板：\n 申请人:$user_row[last_name] \n 申请单号:$entity[refillapplicationno] \n 充值来源:$rechargesource \n 待审核节点:$salesorderworkflowstages_row[workflowstagesname]";
                            //$email='steel.liu@71360.com';
                            //$this->setweixincontracts(array('email'=>trim($email),'content'=>$content,'flag'=>6));
                            $content='<div class=\"gray\">'.date('Y年m月d日').'</div><div class=\"normal\">与您相关的充值申请单需要审核</div><div class=\"highlight\">申请人:'.$user_row['last_name'].'</div><div class=\"highlight\">申请单号:'.$entity['refillapplicationno'].'</div><div class=\"highlight\">充值来源:'.$rechargesource.'</div><div class=\"highlight\">待审核节点:'.$salesorderworkflowstages_row['workflowstagesname'].'</div>请及时处理';
                            $dataurl='http://m.crm.71360.com/index.php?module=RefillApplication&action=one&id='.$recordid;
                            $this->setweixincontracts(array('email'=>trim($email),'description'=>$content,'dataurl'=>$dataurl,'title'=>'充值单审核','flag'=>7));
                        }
                    }
                }else{
                    //没有指定的人员审核查找该节点对应的角色
                    global $root_directory,$current_user;
                    include $root_directory."crmcache".DIRECTORY_SEPARATOR."workflows".DIRECTORY_SEPARATOR."{$workflowsid}.php";
                    $currentId=$current_user->id;
                    $assigned_user_id=$entity['assigned_user_id'];
                    if(!empty($workflows['stage'])) {
                        foreach ($workflows['stage'] as $key=>$value){
                            //查找对应节点的审核角色
                            if($value['workflowstagesid']==$workflowstagesid){
                                if(!empty($value['isrole'])){
                                    $userrole="'";
                                    $userrole.=str_replace(' |##| ',"','",$value['isrole']);
                                    $userrole.="'";
                                    $query="SELECT vtiger_users.email1,id FROM vtiger_users LEFT JOIN vtiger_user2role ON vtiger_users.id=vtiger_user2role.userid WHERE vtiger_users.`status`='Active' AND vtiger_user2role.roleid in({$userrole})";
                                    $userresult=$this->db->pquery($query,array());
                                    $usernum=$this->db->num_rows($userresult);
                                    if($usernum){
                                        $userstr='';
                                        for($i=0;$i<$usernum;$i++){
                                            $email=$this->db->query_result($userresult,$i,'email1');
                                            $id=$this->db->query_result($userresult,$i,'id');
                                            $current_user->id=$id;
                                            $user = new Users();
                                            $current_user= $user->retrieveCurrentUserInfoFromFile($id);
                                            $where=getAccessibleUsers('RefillApplication','List',true);
                                            if($this->checkEmail(trim($email))&& in_array($assigned_user_id,$where)){
                                                $userstr.=trim($email)."|";
                                            }
                                        }
                                        $user = new Users();
                                        $current_user= $user->retrieveCurrentUserInfoFromFile($currentId);
                                        $userstr=rtrim($userstr,'|');
                                        //$userstr='steel.liu@71360.com';
                                        /*$content='与您相关的充值申请单需要审核,单号为:'.$entity['refillapplicationno'].'请及时处理';
                                        $this->setweixincontracts(array('email'=>$userstr,'content'=>$content,'flag'=>6));*/
                                        $content='<div class=\"gray\">'.date('Y年m月d日').'</div><div class=\"normal\">与您相关的充值审请单需要审核:</div><div class=\"highlight\">'.$entity['refillapplicationno'].'</div>请及时处理';
                                        $dataurl='http://m.crm.71360.com/index.php?module=RefillApplication&action=one&id='.$recordid;
                                        $this->setweixincontracts(array('email'=>trim($userstr),'description'=>$content,'dataurl'=>$dataurl,'title'=>'充值单审核','flag'=>7));
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    /**
     * 设置微信企业号上的成员信息
     * @param Vtiger_Request $request
     */
    private function setweixincontracts($data){
        $userkey='c0b3Ke0Q4c%2BmGXycVaQ%2BUEcbU0ldxTBeeMAgUILM0PK5Q59cEp%2B40n6qUSJiPQ';
        $url = "http://m.crm.71360.com/api.php";
        $ch  = curl_init();
        $data['tokenauth']=$userkey;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_exec($ch);
        curl_close($ch);
    }

    /**
     * 邮件格式验证
     * @param $str
     * @return bool
     */
    public function checkEmail($str){
        $str=trim($str);
        $regex = '/^[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@(?:[-_a-z0-9][-_a-z0-9]*\.)*(?:[a-z0-9][-a-z0-9]{0,62})\.(?:(?:[a-z]{2}\.)?[a-z]{2,})$/i';
        if (preg_match($regex, $str)) {
            return true;
        }
        return false;
    }
    /**
     * 重写工作流生成
     * @param unknown $modulename
     * @param unknown $workflowsid
     * @param unknown $salesorderid
     * @param string $isedit
     */
    public function makeWorkflows($modulename,$workflowsid,$salesorderid,$isedit=''){
        global $adb,$current_user,$isallow;
        $workflowsisedit=false;//流程是否可以修改

        $wfResoult=array();
        $isinsert= true;
        if(empty($modulename)){
            $modulename='RefillApplication';
        }
        if(!in_array($modulename, $isallow)){
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
        $recordModel=Vtiger_Record_Model::getInstanceById($salesorderid,'RefillApplication',true);
        if(in_array($recordModel->get('rechargesource'),array('NonMediaExtraction','Vendors','TECHPROCUREMENT','PreRecharge'))){
            $companyCode=$this->getContractsCompanyCode('SupplierContracts',$recordModel->get('suppliercontractsid'));
        }else{
            $companyCode=$this->getContractsCompanyCode('ServiceContracts',$recordModel->get('servicecontractsid'));
        }
        if($recordModel->get('rechargesource')=='PACKVENDORS'){
            $sql = "select b.companycode from vtiger_refillapplication a left join vtiger_invoicecompany b on a.invoicecompany=b.invoicecompany where a.refillapplicationid=".$salesorderid." limit 1";
            $result  = $this->db->query($sql,array());
            if($this->db->num_rows($result)){
                $row = $this->db->fetchByAssoc($result,0);
                $companyCode = $row['companycode'];
            }
        }
        //$companyCode=$this->getContractsCompanyCode($modulename,$salesorderid);
        if($isinsert){
            //@TODO 前置条件，为扩展功能，如何触发
            //插入数据第一个节点
            $sql="SELECT * FROM vtiger_workflowstages WHERE workflowsid =? ORDER BY sequence ASC";
            $wresult=$this->db->pquery($sql,array($workflowsid));
            $nextparentid=0;
            $countrows=$this->db->num_rows($wresult);
            $sequence=0;
            if($countrows){
                $isaction=1;
                $workflowsid=0;
                $actiontime=date('Y-m-d H:i:s');
                $sqlsub="INSERT INTO vtiger_salesorderworkflowstages (workflowstagesname,workflowstagesid,sequence,salesorderid,isaction,actiontime,addtime,workflowsid,modulename,smcreatorid,createdtime,productid,departmentid,ishigher,higherid,workflowstagesflag,handleaction,companycode) values (?,?,?,?,?,?, NOW(),?,?,?,NOW(),?,?,?,?,?,?,'{$companyCode}')";
                $tempId=0;

                //----以前有其他工作流时，新加的工作流阶段排序需要进行累加
                // $otherWorkflowsSql = 'select sequence from vtiger_salesorderworkflowstages where salesorderid=? and modulename=? order by sequence desc limit 1';
                // $resultWf=$this->db->pquery($otherWorkflowsSql,array($salesorderid,$modulename));
                // if($this->db->num_rows($resultWf)){
                //     $tempId=$this->db->query_result($resultWf,0,'sequence');
                // }

                while($row=$this->db->fetch_array($wresult)){
                    if($row['workflowstagesflag']=='GUARANTY_NODE'){
                        $auditInformation=$this->setAuditInformation($salesorderid);
                        if($auditInformation['flag']){
                            $tempId++;
                            $t=1;
                            if(in_array($auditInformation['rechargesource'],array('Accounts','Vendors','NonMediaExtraction'))){
                                foreach($auditInformation['userid'] as $value){
                                    $this->db->pquery($sqlsub,array('客户担保:'.$auditInformation['advancesmoney'].'--第 '.$t.' 级审核',$row['workflowstagesid'],$row['sequence']+$tempId,$salesorderid,$isaction,$actiontime,$row['workflowsid'],$modulename,$current_user->id,0,$user->get('current_user_parent_departments'),1,$value,"",''));
                                    $tempId++;$t++;
                                }
                            }else{
                                foreach($auditInformation['userid'] as $value){
                                    $this->db->pquery($sqlsub,array('第 '.$t.' 级审核',$row['workflowstagesid'],$row['sequence']+$tempId,$salesorderid,$isaction,$actiontime,$row['workflowsid'],$modulename,$current_user->id,0,$user->get('current_user_parent_departments'),1,$value,"",''));
                                    $tempId++;$t++;
                                }
                            }

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
                            $this->db->pquery($sqlsub,array($row['workflowstagesname'],$row['workflowstagesid'],$row['sequence']+$tempId,$salesorderid,$isaction,$actiontime,$row['workflowsid'],$modulename,$current_user->id,0,$user->get('current_user_parent_departments'),1,$reprot_to_id_last,$row['workflowstagesflag'],$row['handleaction']));
                        }
                    }elseif($row['handleaction']=='ProductCheck'){
                        global $checkproducts;
                        if(is_array($checkproducts)){
                            foreach ($checkproducts as $productinfo){//$current_user->id为当前人，审核，产品部门的审核变化了。
                                $this->db->pquery($sqlsub,array(str_replace('审核','',$row['workflowstagesname']).$productinfo['workflowstagesname'],$row['workflowstagesid'],$row['sequence']+$tempId,$salesorderid,$isaction,$actiontime,$row['workflowsid'],$modulename,$current_user->id,$productinfo['productid'],$user->get('current_user_parent_departments'),0,0,$row['workflowstagesflag'],$row['handleaction']));
                            }
                        }

                    }elseif($row['handleaction']=='MyCheck'){//自己审核
                        $this->db->pquery($sqlsub,array('提单人确认',$row['workflowstagesid'],$row['sequence']+$tempId,$salesorderid,$isaction,$actiontime,$row['workflowsid'],$modulename,$current_user->id,0,$user->get('current_user_parent_departments'),1,$current_user->id,$row['workflowstagesflag'],$row['handleaction']));
                    }elseif($row['handleaction']=='UpCheck'){
                        //过滤掉某些角色,比如商务主管
                        $reports_to_id=$current_user->reports_to_id;
                        $this->db->pquery($sqlsub,array('提单人上级审批',$row['workflowstagesid'],$row['sequence']+$tempId,$salesorderid,$isaction,$actiontime,$row['workflowsid'],$modulename,$current_user->id,0,$user->get('current_user_parent_departments'),1,$reports_to_id,$row['workflowstagesflag'],$row['handleaction']));
                    }elseif($row['handleaction']=='ServiceCheck'){
                        //wangbin 20150930 只有中小商务的工单工作流才有客服审核
                        $if_middle_small_sal = "SELECT ff.parentdepartment FROM vtiger_salesorder aa LEFT JOIN vtiger_servicecontracts bb ON aa.servicecontractsid = bb.servicecontractsid LEFT JOIN vtiger_crmentity cc ON bb.sc_related_to = cc.crmid LEFT JOIN vtiger_user2department ee ON ee.userid = cc.smownerid LEFT JOIN vtiger_departments ff ON ff.departmentid = ee.departmentid WHERE aa.salesorderid = '?' AND ff.parentdepartment LIKE 'H1::H2::H3%'";
                        $if_zhongxiao = $this->db->pquery($if_middle_small_sal,array($salesorderid));
                        $if_zhongxiao_rows=$this->db->num_rows($if_zhongxiao);
                        if($if_zhongxiao_rows>0){
                            //young 20150519 加入客服角色的审核,条件是工单客户含有客服
                            if($serviceid>0){ //如果以及分配客服
                                $this->db->pquery($sqlsub,array('客服'.$servicename.'审核',0,$row['sequence']+$tempId,$salesorderid,$isaction,$actiontime,$row['workflowsid'],$modulename,$current_user->id,0,$user->get('current_user_parent_departments'),1,$serviceid,$row['workflowstagesflag'],$row['handleaction']));
                            }else { //如果未分配客服
                                $this->db->pquery($sqlsub, array('客服经理分配客服', $row['workflowstagesid']+$tempId, $row['sequence'], $salesorderid, $isaction, $actiontime, $row['workflowsid'], $modulename, $current_user->id, 0, $user->get('current_user_parent_departments'), 0, 0,$row['workflowstagesflag'],$row['handleaction']));
                            }
                        }
                    }elseif($row['handleaction']=='NextCheck'){ //下个节点指定审核人
                        $this->db->pquery($sqlsub,array('指定下个节点审核人',$row['workflowstagesid'],$row['sequence']+$tempId,$salesorderid,$isaction,$actiontime,$row['workflowsid'],$modulename,$current_user->id,0,$user->get('current_user_parent_departments'),0,0,$row['workflowstagesflag'],$row['handleaction']));
                    }elseif($row['handleaction']=='PlatformCheck'){ //充值平台审核 gaocl 2017/03/15 add
                        $topplatform_sql = "SELECT DISTINCT vtiger_rechargesheet.topplatform,vtiger_topplatform.platformman FROM vtiger_rechargesheet 
                                                    LEFT JOIN vtiger_topplatform ON(vtiger_topplatform.topplatform=vtiger_rechargesheet.topplatform)
                                                    WHERE NOT ISNULL(vtiger_topplatform.platformman) AND LENGTH(TRIM(vtiger_topplatform.platformman))>0 AND refillapplicationid=?";
                        $topplatform_result = $this->db->pquery($topplatform_sql,array($salesorderid));
                        $topplatform_rows=$this->db->num_rows($topplatform_result);
                        $sqlsub_platform="INSERT INTO vtiger_salesorderworkflowstages (workflowstagesname,workflowstagesid,sequence,salesorderid,isaction,actiontime,addtime,workflowsid,modulename,smcreatorid,createdtime,productid,departmentid,ishigher,higherid,platformids) values (?,?,?,?,?,?, NOW(),?,?,?,NOW(),?,?,?,?,?)";
                        if($topplatform_rows>0){
                            for($i=0;$i<$topplatform_rows;$i++){
                                $topplatform = $this->db->query_result($topplatform_result,$i,'topplatform');
                                $platformmanIds = $this->db->query_result($topplatform_result,$i,'platformman');
                                $this->db->pquery($sqlsub_platform,array(str_replace('审核','',$row['workflowstagesname']).'-'.$topplatform,$row['workflowstagesid'],$row['sequence']+$tempId,$salesorderid,$isaction,$actiontime,$row['workflowsid'],$modulename,$current_user->id,0,$user->get('current_user_parent_departments'),0,0,$platformmanIds,$row['workflowstagesflag']));
                            }
                        }

                    }elseif($row['handleaction']=='REVIEWER'){
                        $this->db->pquery($sqlsub,array($row['workflowstagesname'],$row['workflowstagesid'],$row['sequence'],$salesorderid,$isaction,$actiontime,$row['workflowsid'],$modulename,$current_user->id,0,$user->get('current_user_parent_departments'),1,$row['reviewer'],$row['workflowstagesflag'],$row['handleaction']));
                    }elseif($row['handleaction']=='DepartmentalAudit'){//各级部门负责人审核
                        if(!empty($_SESSION['userdepartmentid']) || !empty($current_user->departmentid)){
                            $departmentid=!empty($_SESSION['userdepartmentid'])?$_SESSION['userdepartmentid']:$current_user->departmentid;
                            $departAudituid=$this->getDepartAudituid($departmentid);
                            $currentdepartment=$departAudituid[$departmentid];
                            $parentdepartment=$currentdepartment['parentdepartment'];
                            $parentdepartment=explode('::',$parentdepartment);
                            $parentdepartment=array_reverse($parentdepartment);
                            array_pop($parentdepartment);
                            $auditunode=array('DAUDIT_VERIFICATION'=>0,'DTWO_VERIFICATION'=>1,'DTHREE_VERIFICATION'=>2,'DFOUR_VERIFICATION'=>3,'DFIVE_VERIFICATION'=>4);
                            $deptempseq=0;//节点标记
                            $peopleidflag=-1;//审核人标记
                            $workflowstagesnum=(!empty($row['workflowstagesflag']) && is_numeric($auditunode[$row['workflowstagesflag']]))?$auditunode[$row['workflowstagesflag']]:10;
                            foreach($parentdepartment as $department){
                                if($deptempseq>$workflowstagesnum){
                                    break;
                                }
                                ++$deptempseq;
                                if($peopleidflag==$departAudituid[$department]['peopleid'] || empty($departAudituid[$department]['peopleid'])){
                                    continue;
                                }
                                $peopleidflag=$departAudituid[$department]['peopleid'];
                                ++$tempId;
                                $this->db->pquery($sqlsub,array('【'.$departAudituid[$department]['departmentname'].'】负责人审核',$row['workflowstagesid'],($row['sequence']+$tempId),$salesorderid,$isaction,$actiontime,$row['workflowsid'],$modulename,$current_user->id,0,$user->get('current_user_parent_departments'),1,$departAudituid[$department]['peopleid'],$row['workflowstagesflag'],$row['handleaction']));
                                $isaction=0;
                            }
                        }else{
                            $this->db->pquery($sqlsub,array('【】负责人审核',$row['workflowstagesid'],($row['sequence']+$tempId),$salesorderid,$isaction,$actiontime,$row['workflowsid'],$modulename,$current_user->id,0,$user->get('current_user_parent_departments'),1,2110,$row['workflowstagesflag'],$row['handleaction']));
                        }
                    }else{ //默认情况
                        if($row['workflowstagesflag']=='CWSH'||in_array($row['workflowstagesflag'],array('TREASURER_CODE','FINANCE_MANAGER','TREASURER_TWO'))){//运营经理审核
                            $userId=$this->getDepartmentById($modulename,$salesorderid,$row['workflowstagesname']);
                            if($userId){
                                $this->db->pquery($sqlsub,array($row['workflowstagesname'],$row['workflowstagesid'],$row['sequence']+$tempId,$salesorderid,$isaction,$actiontime,$row['workflowsid'],$modulename,$current_user->id,0,$user->get('current_user_parent_departments'),1,$userId,$row['workflowstagesflag'],$row['handleaction']));
                            }else{
                                $this->db->pquery($sqlsub,array($row['workflowstagesname'],$row['workflowstagesid'],$row['sequence']+$tempId,$salesorderid,$isaction,$actiontime,$row['workflowsid'],$modulename,$current_user->id,0,$user->get('current_user_parent_departments'),0,0,$row['workflowstagesflag'],$row['handleaction']));
                            }
                        }else{
                            $this->db->pquery($sqlsub,array($row['workflowstagesname'],$row['workflowstagesid'],$row['sequence']+$tempId,$salesorderid,$isaction,$actiontime,$row['workflowsid'],$modulename,$current_user->id,0,$user->get('current_user_parent_departments'),0,0,$row['workflowstagesflag'],$row['handleaction']));
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

        $sql=" SELECT vtiger_refillapplication.refillapplicationno FROM vtiger_refillapplication  LEFT JOIN vtiger_crmentity  ON vtiger_crmentity.crmid=vtiger_refillapplication.refillapplicationid  WHERE vtiger_crmentity.deleted=0 AND  vtiger_refillapplication.refillapplicationid = ? ";
        $result=$this->db->pquery($sql,array($salesorderid));
        if($this->db->num_rows($result)){
        	$refillapplicationno=$this->db->query_result($result,0,'refillapplicationno');
        	$query="UPDATE vtiger_salesorderworkflowstages,
				 vtiger_refillapplication
				SET vtiger_salesorderworkflowstages.accountid=vtiger_refillapplication.accountid,vtiger_salesorderworkflowstages.salesorder_nono=?,
				 vtiger_salesorderworkflowstages.modulestatus='p_process',
				 vtiger_salesorderworkflowstages.accountname=(SELECT vtiger_account.accountname FROM vtiger_account WHERE vtiger_account.accountid=vtiger_refillapplication.accountid)
				WHERE vtiger_refillapplication.refillapplicationid=vtiger_salesorderworkflowstages.salesorderid
				AND vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.workflowsid=? ";
        	$this->db->pquery($query,array($refillapplicationno,$salesorderid,$workflowsid));
        }else{
        	$refillapplicationno=$this->db->query_result($result,0,'refillapplicationno');
        	$query="UPDATE vtiger_salesorderworkflowstages,
				 vtiger_refillapplication
				SET vtiger_salesorderworkflowstages.accountid=vtiger_refillapplication.accountid,
				 vtiger_salesorderworkflowstages.accountname=(SELECT vtiger_account.accountname FROM vtiger_account WHERE vtiger_account.accountid=vtiger_refillapplication.accountid)
				WHERE vtiger_refillapplication.refillapplicationid=vtiger_salesorderworkflowstages.salesorderid
				AND vtiger_salesorderworkflowstages.salesorderid=?  AND vtiger_salesorderworkflowstages.workflowsid=? ";
        	$this->db->pquery($query,array($salesorderid,$workflowsid));
        }
        /*//新建时 消息提醒第一审核人进行审核 save_module 方法的最后边。
        $object = new SalesorderWorkflowStages_SaveAjax_Action();
        $object->sendWxRemind(array('salesorderid'=>$salesorderid,'salesorderworkflowstagesid'=>0));*/
        
        
    }
    function retrieve_entity_info($record, $module) {
        global $adb, $log, $app_strings;

        // INNER JOIN is desirable if all dependent table has entries for the record.
        // LEFT JOIN is desired if the dependent tables does not have entry.
        $join_type = 'LEFT JOIN';

        // Tables which has multiple rows for the same record
        // will be skipped in record retrieve - need to be taken care separately.
        $multirow_tables = NULL;
        if (isset($this->multirow_tables)) {
            $multirow_tables = $this->multirow_tables;
        } else {
            $multirow_tables = array(
                'vtiger_campaignrelstatus',
                'vtiger_attachments',
                //'vtiger_inventoryproductrel',
                //'vtiger_cntactivityrel',
                'vtiger_email_track'
            );
        }

        // Lookup module field cache
        $cachedModuleFields = VTCacheUtils::lookupFieldInfo_Module($module);
        if ($cachedModuleFields === false) {
            // Pull fields and cache for further use
            $tabid = getTabid($module);

            $sql0 = "SELECT fieldname, fieldid, fieldlabel, columnname, tablename, uitype, typeofdata,presence FROM vtiger_field WHERE tabid=?";
            // NOTE: Need to skip in-active fields which we will be done later.
            $result0 = $adb->pquery($sql0, array($tabid));
            if ($adb->num_rows($result0)) {
                while ($resultrow = $adb->fetch_array($result0)) {
                    // Update cache
                    VTCacheUtils::updateFieldInfo(
                        $tabid, $resultrow['fieldname'], $resultrow['fieldid'], $resultrow['fieldlabel'], $resultrow['columnname'], $resultrow['tablename'], $resultrow['uitype'], $resultrow['typeofdata'], $resultrow['presence']
                    );
                }
                // Get only active field information
                $cachedModuleFields = VTCacheUtils::lookupFieldInfo_Module($module);
            }
        }

        if ($cachedModuleFields) {
            $column_clause = '';
            $from_clause   = '';
            $where_clause  = '';
            $limit_clause  = ' LIMIT 1'; // to eliminate multi-records due to table joins.

            $params = array();
            $required_tables = $this->tab_name_index; // copies-on-write

            foreach ($cachedModuleFields as $fieldinfo) {
                if (in_array($fieldinfo['tablename'], $multirow_tables)) {
                    continue;
                }
                // Added to avoid picking shipping tax fields for Inventory modules, the shipping tax detail are stored in vtiger_inventoryshippingrel
                // table, but in vtiger_field table we have set tablename as vtiger_inventoryproductrel.
                if(($module == 'Invoice' || $module == 'Quotes' || $module == 'SalesOrder' || $module == 'PurchaseOrder')
                    && stripos($fieldinfo['columnname'], 'shtax') !== false) {
                    continue;
                }

                // Alias prefixed with tablename+fieldname to avoid duplicate column name across tables
                // fieldname are always assumed to be unique for a module
                $column_clause .=  $fieldinfo['tablename'].'.'.$fieldinfo['columnname'].' AS '.$this->createColumnAliasForField($fieldinfo).',';
            }


            if (isset($required_tables['vtiger_crmentity'])) {
                // 2014-10-29 young 如果是单独的表，就不需要这个字段
                $column_clause .= 'vtiger_crmentity.deleted';

                $from_clause  = ' vtiger_crmentity';
                unset($required_tables['vtiger_crmentity']);
                foreach ($required_tables as $tablename => $tableindex) {
                    if (in_array($tablename, $multirow_tables)) {
                        // Avoid multirow table joins.
                        continue;
                    }
                    $from_clause .= $tablename!='vtiger_rechargesheet'?sprintf(' %s %s ON %s.%s=%s.%s', $join_type,
                        $tablename, $tablename, $tableindex, 'vtiger_crmentity', 'crmid'):
                        sprintf(' %s %s ON (%s.%s=%s.%s AND vtiger_rechargesheet.isentity=1)', $join_type,
                            $tablename, $tablename, $tableindex, 'vtiger_crmentity', 'crmid');
                }
                $where_clause .= ' vtiger_crmentity.crmid=?';
            }else{
                $column_clause .= $this->table_name.'.'.$this->table_index;
                $where_clause .= ' '.$this->table_name.'.'.$this->table_index.'=?';
                $from_clause  = $this->table_name;
            }


            $params[] = $record;

            $sql = sprintf('SELECT %s FROM %s WHERE %s %s', $column_clause, $from_clause, $where_clause, $limit_clause);
            //echo $sql;
            //exit;

            $result = $adb->pquery($sql, $params);
            if (!$result || $adb->num_rows($result) < 1) {
                throw new Exception($app_strings['LBL_RECORD_NOT_FOUND'], -1);
            } else {
                $resultrow = $adb->query_result_rowdata($result);
                if (!empty($resultrow['deleted'])) {
                    throw new Exception($app_strings['LBL_RECORD_DELETE'], 1);
                }

                foreach ($cachedModuleFields as $fieldinfo) {
                    $fieldvalue = '';
                    $fieldkey = $this->createColumnAliasForField($fieldinfo);
                    //Note : value is retrieved with a tablename+fieldname as we are using alias while building query
                    if (isset($resultrow[$fieldkey])) {
                        $fieldvalue = $resultrow[$fieldkey];
                    }
                    $this->column_fields[$fieldinfo['fieldname']] = $fieldvalue;
                }
            }
        }

        $this->column_fields['record_id'] = $record;
        $this->column_fields['record_module'] = $module;
    }
    /** Function to insert values in the specifed table for the specified module
     * @param $table_name -- table name:: Type varchar
     * @param $module -- module:: Type varchar
     */
    function insertIntoEntityTable($table_name, $module, $fileid = '') {
        global $log;
        global $current_user, $app_strings;
        $log->info("function insertIntoEntityTable " . $module . ' vtiger_table name ' . $table_name);
        global $adb;
        $insertion_mode = $this->mode;

        //Checkin whether an entry is already is present in the vtiger_table to update
        if ($insertion_mode == 'edit') {
            $tablekey = $this->tab_name_index[$table_name];
            // Make selection on the primary key of the module table to check.
            $check_query = "select $tablekey from $table_name where $tablekey=?";
            $check_result = $adb->pquery($check_query, array($this->id));

            $num_rows = $adb->num_rows($check_result);

            if ($num_rows <= 0) {
                $insertion_mode = '';
            }
        }

        $tabid = getTabid($module);
        if ($module == 'Calendar' && $this->column_fields["activitytype"] != null && $this->column_fields["activitytype"] != 'Task') {
            $tabid = getTabid('Events');
        }
        if ($insertion_mode == 'edit') {
            $update = array();
            $update_params = array();
            checkFileAccessForInclusion('user_privileges/user_privileges_' . $current_user->id . '.php');
            require('user_privileges/user_privileges_' . $current_user->id . '.php');
            //字段权限验证暂时去除[by joe at 2015/2/5]
            //if ($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0) {
            $sql = "select * from vtiger_field where tabid in (" . generateQuestionMarks($tabid) . ") and tablename=? and readonly=1 and displaytype in (1,3) and presence in (0,2) group by columnname";
            $params = array($tabid, $table_name);
        } else {
            $table_index_column = $this->tab_name_index[$table_name];

            if ($table_index_column == 'id' && $table_name == 'vtiger_users') {
                $currentuser_id = $adb->getUniqueID("vtiger_users");
                $this->id = $currentuser_id;
            }
            //2014-10-29 young
            if(!isset($this->id)||empty($this->id)){
                $currentuser_id = $adb->getUniqueID($table_name);
                $this->id = $currentuser_id;
                $this->{$this->table_index}=$currentuser_id;
            }

            $column = array($table_index_column);
            $value = array($this->id);

            $sql = "select * from vtiger_field where tabid=? and tablename=? and displaytype in (1,3,4) and vtiger_field.presence in (0,2)";
            $params = array($tabid, $table_name);
        }

        // Attempt to re-use the quer-result to avoid reading for every save operation
        // TODO Need careful analysis on impact ... MEMORY requirement might be more
        static $_privatecache = array();
        $cachekey = "{$insertion_mode}-" . implode(',', $params);

        if (!isset($_privatecache[$cachekey])) {
            $result = $adb->pquery($sql, $params);
            $noofrows = $adb->num_rows($result);

            if (CRMEntity::isBulkSaveMode()) {
                $cacheresult = array();
                for ($i = 0; $i < $noofrows; ++$i) {
                    $cacheresult[] = $adb->fetch_array($result);
                }
                $_privatecache[$cachekey] = $cacheresult;
            }
        } else { // Useful when doing bulk save
            $result = $_privatecache[$cachekey];
            $noofrows = count($result);
        }
        for ($i = 0; $i < $noofrows; $i++) {

            $fieldname = $this->resolve_query_result_value($result, $i, "fieldname");
            $columname = $this->resolve_query_result_value($result, $i, "columnname");
            $uitype = $this->resolve_query_result_value($result, $i, "uitype");
            $generatedtype = $this->resolve_query_result_value($result, $i, "generatedtype");
            $typeofdata = $this->resolve_query_result_value($result, $i, "typeofdata");

            $typeofdata_array = explode("~", $typeofdata);
            $datatype = $typeofdata_array[0];

            if(!(($_REQUEST['rechargesource']=='contractChanges' && $fieldname=='customertype')|| ($_REQUEST['rechargesource']=='COINRETURN' && $fieldname=='vendorid' && $_REQUEST['conversiontype']=='AccountPlatform'))){
                if($typeofdata_array[1]=='M' && isset($_POST[$fieldname]) && empty($this->column_fields[$fieldname]) && $this->column_fields[$fieldname] !=='0' && $fieldname !="servicecontractsid"){
                    //新增数据出错delete=0
                    if($insertion_mode != 'edit'){
                        $adb->pquery('update vtiger_crmentity set deleted=? where crmid=?',array(1,$_REQUEST['currentid']));
                    }
                    //echo $fieldname;  echo "</br>"; echo $this->column_fields[$fieldname];die;
                    throw new AppException('错误的数据格式！');
                    exit;
                }
            }




            $ajaxSave = false;
            if ((isset($_REQUEST['file']) && $_REQUEST['file'] == 'DetailViewAjax' && $_REQUEST['ajxaction'] == 'DETAILVIEW'
                    && isset($_REQUEST["fldName"]) && $_REQUEST["fldName"] != $fieldname)
                || ($_REQUEST['action'] == 'MassEditSave' && !isset($_REQUEST[$fieldname."_mass_edit_check"]))) {
                $ajaxSave = true;
            }

            if ($uitype == 4 && $insertion_mode != 'edit') {
                $fldvalue = '';
                // Bulk Save Mode: Avoid generation of module sequence number, take care later.
                if (!CRMEntity::isBulkSaveMode())
                    $fldvalue = $this->setModuleSeqNumber("increment", $module);
                $this->column_fields[$fieldname] = $fldvalue;
            }
            if (isset($this->column_fields[$fieldname])) {
                if ($uitype == 56) {
                    if ($this->column_fields[$fieldname] == 'on' || $this->column_fields[$fieldname] == 1) {
                        $fldvalue = '1';
                    } else {
                        $fldvalue = '0';
                    }
                } elseif ($uitype == 15 || $uitype == 16) {
                    if ($this->column_fields[$fieldname] == $app_strings['LBL_NOT_ACCESSIBLE']) {

                        //If the value in the request is Not Accessible for a picklist, the existing value will be replaced instead of Not Accessible value.
                        $sql = "select $columname from  $table_name where " . $this->tab_name_index[$table_name] . "=?";
                        $res = $adb->pquery($sql, array($this->id));
                        $pick_val = $adb->query_result($res, 0, $columname);
                        $fldvalue = $pick_val;
                    } else {
                        $fldvalue = $this->column_fields[$fieldname];
                    }

                    //新增状态字段默认值
                    //新增状态字段默认值
                    if($fieldname=='modulestatus' && $insertion_mode != 'edit'){
                        if(empty($fldvalue)){
                            $fldvalue='a_normal';
                        }
                    }
                    //用户多选追加/gaocl/2015-01-04 start
                } elseif ($uitype == 33 || $uitype ==54 || $uitype ==110  ||$uitype ==103) {
                    //用户多选追加/gaocl/2015-01-04 end
                    if (is_array($this->column_fields[$fieldname])) {
                        $field_list = implode(' |##| ', $this->column_fields[$fieldname]);
                    } else {
                        $field_list = $this->column_fields[$fieldname];
                    }
                    $fldvalue = $field_list;
                } elseif ($uitype == 5 || $uitype == 6 || $uitype == 23) {
                    //Added to avoid function call getDBInsertDateValue in ajax save
                    //young 2014-12-23 时间格式出错造成空值，重写验证
                    if (isset($current_user->date_format) && !$ajaxSave) {
                        //$fldvalue = getValidDBInsertDateValue($this->column_fields[$fieldname]);
                        $fldvalue = $this->column_fields[$fieldname];
                    } else {
                        $fldvalue = $this->column_fields[$fieldname];
                    }
                    //echo $this->column_fields[$fieldname];echo $fieldname;
                } elseif ($uitype == 7) {
                    //strip out the spaces and commas in numbers if given ie., in amounts there may be ,
                    $fldvalue = str_replace(",", "", $this->column_fields[$fieldname]); //trim($this->column_fields[$fieldname],",");
                } elseif ($uitype == 26) {
                    if (empty($this->column_fields[$fieldname])) {
                        $fldvalue = 1; //the documents will stored in default folder
                    } else {
                        $fldvalue = $this->column_fields[$fieldname];
                    }
                } elseif ($uitype == 28) {
                    if ($this->column_fields[$fieldname] == null) {
                        $fileQuery = $adb->pquery("SELECT filename from vtiger_notes WHERE notesid = ?", array($this->id));
                        $fldvalue = null;
                        if (isset($fileQuery)) {
                            $rowCount = $adb->num_rows($fileQuery);
                            if ($rowCount > 0) {
                                $fldvalue = decode_html($adb->query_result($fileQuery, 0, 'filename'));
                            }
                        }
                    } else {
                        $fldvalue = decode_html($this->column_fields[$fieldname]);
                    }
                } elseif ($uitype == 8) {
                    $this->column_fields[$fieldname] = rtrim($this->column_fields[$fieldname], ',');
                    $ids = explode(',', $this->column_fields[$fieldname]);
                    $json = new Zend_Json();
                    $fldvalue = $json->encode($ids);
                } elseif ($uitype == 12) {

                    // Bulk Sae Mode: Consider the FROM email address as specified, if not lookup
                    $fldvalue = $this->column_fields[$fieldname];

                    if (empty($fldvalue)) {
                        $query = "SELECT email1 FROM vtiger_users WHERE id = ?";
                        $res = $adb->pquery($query, array($current_user->id));
                        $rows = $adb->num_rows($res);
                        if ($rows > 0) {
                            $fldvalue = $adb->query_result($res, 0, 'email1');
                        }
                    }
                    // END
                } elseif ($uitype == 72 && !$ajaxSave) {
                    // Some of the currency fields like Unit Price, Totoal , Sub-total - doesn't need currency conversion during save
                    $fldvalue = CurrencyField::convertToDBFormat($this->column_fields[$fieldname], null, true);
                } elseif ($uitype == 71 && !$ajaxSave) {
                    $fldvalue = CurrencyField::convertToDBFormat($this->column_fields[$fieldname]);
                }elseif($uitype == 153){
                    $fldvalue = $this->column_fields[$fieldname];
                    /* if($_POST['attachmentsid']){
                        $fldvalue .='##'.$_POST['attachmentsid'];
                    } */
                    $fldvalue = $this->column_fields[$fieldname];
                    $newfldvalue='';
                    if(!empty($fldvalue)){
                        if(is_array($fldvalue)){
                            foreach($fldvalue as $key=>$val){
                                if($_POST['attachmentsid'][$key]){
                                    $newfldvalue .=$val.'##'.$_POST['attachmentsid'][$key].'*|*';
                                }
                            }
                            $fldvalue=rtrim($newfldvalue,'*|*');
                        }
                    }

                } else {
                    $fldvalue = $this->column_fields[$fieldname];
                }
                if ($uitype != 33 && $uitype != 8)
                    $fldvalue = from_html($fldvalue, ($insertion_mode == 'edit') ? true : false);
            }
            else {
                $fldvalue = '';
            }
            if ($fldvalue == '') {
                $fldvalue = $this->get_column_value($columname, $fldvalue, $fieldname, $uitype, $datatype);
            }

            if ($insertion_mode == 'edit') {
                if ($table_name != 'vtiger_ticketcomments' && $uitype != 4) {
                    array_push($update, $columname . "=?");
                    array_push($update_params, $fldvalue);
                }
            } else {
                array_push($column, $columname);
                array_push($value, $fldvalue);
            }
        }
        if ($insertion_mode == 'edit') {

            if (count($update) > 0) {
                $isentity=$table_name=='vtiger_rechargesheet'?' AND isentity=1':'';
                $sql1 = "update $table_name set " . implode(",", $update) . " where " . $this->tab_name_index[$table_name] . "=?".$isentity;
                //echo $sql1;die;
                array_push($update_params, $this->id);
                //var_dump($update_params);die;
                $adb->pquery($sql1, $update_params);
            }
        } else {
            $sql1 = "insert into $table_name(" . implode(",", $column) . ") values(" . generateQuestionMarks($value) . ")";
            $adb->pquery($sql1, $value);
        }

    }
    private function resolve_query_result_value($result, $index, $columnname) {
        global $adb;
        if (is_array($result))
            return $result[$index][$columnname];
        else
            return $adb->query_result($result, $index, $columnname);
    }

    /**
     * 打回回冲回款上可用充值的金额
     * @param $recordid
     */
    public function backwashReceivedPayments($recordid){
        $recordModel=Vtiger_Record_Model::getInstanceById($recordid,'RefillApplication');
        $rechargesource=$recordModel->get('rechargesource');
        $rechargesourceStr='';
        if($rechargesource=='TECHPROCUREMENT'){
            $sql="UPDATE vtiger_salesorderproductsrel,vtiger_refillapprayment SET vtiger_salesorderproductsrel.costofuse=if((vtiger_salesorderproductsrel.costofuse-vtiger_refillapprayment.refillapptotal)>0,(vtiger_salesorderproductsrel.costofuse-vtiger_refillapprayment.refillapptotal),0) WHERE vtiger_salesorderproductsrel.salesorderproductsrelid=vtiger_refillapprayment.receivedpaymentsid AND vtiger_refillapprayment.deleted=0 AND vtiger_refillapprayment.refillapplicationid=?";
            $this->db->pquery($sql,array($recordid));
            //$salesorderid=$recordModel->get('salesorderid');
            //$totalrecharge=$recordModel->get('totalrecharge');
            //$sql="UPDATE vtiger_salesorderproductsrel,vtiger_refillapprayment SET vtiger_salesorderproductsrel.costofuse=if((vtiger_salesorderproductsrel.costofuse-vtiger_refillapprayment.refillapptotal)>0,(vtiger_salesorderproductsrel.costofuse-vtiger_refillapprayment.refillapptotal),0) WHERE vtiger_salesorderproductsrel.salesorderproductsrelid=vtiger_refillapprayment.receivedpaymentsid AND vtiger_refillapprayment.servicecontractsid=?";
            //$this->db->pquery($sql,array($salesorderid));
            return ;
            //$rechargesourceStr=',vtiger_receivedpayments.occupationcost=if((vtiger_receivedpayments.occupationcost-vtiger_refillapprayment.backwashtotal)>0,(vtiger_receivedpayments.occupationcost-vtiger_refillapprayment.backwashtotal),0)';
            //$this->db->pquery("UPDATE vtiger_salesorder SET vtiger_salesorder.occupationamount=if(occupationamount-{$totalrecharge}>0,occupationamount-{$totalrecharge},0) WHERE salesorderid=?",array($salesorderid));
        }
        $refillapplicationno=$recordModel->get('refillapplicationno');
        $query='SELECT vtiger_refillapprayment.receivedpaymentsid,refillapptotal,backwashtotal FROM vtiger_refillapprayment WHERE vtiger_refillapprayment.deleted=0 AND vtiger_refillapprayment.refillapplicationid=?';
        $dataresult=$this->db->pquery($query,array($recordid));
        while($row=$this->db->fetch_array($dataresult)){
            $backwashtotal=$row['backwashtotal'];
            $receivedpaymentsid=$row['receivedpaymentsid'];
            $recordModel->setTracker('ReceivedPayments',$receivedpaymentsid,array('fieldName'=>'rechargeableamount','currentValue'=>$refillapplicationno.'打回归还:'.$backwashtotal),'vtiger_receivedpayments');
        }

        $this->db->pquery("UPDATE vtiger_receivedpayments,
                                 vtiger_refillapprayment
                                SET vtiger_receivedpayments.rechargeableamount=if((vtiger_receivedpayments.rechargeableamount+vtiger_refillapprayment.backwashtotal)>vtiger_receivedpayments.unit_price,vtiger_receivedpayments.unit_price,(vtiger_receivedpayments.rechargeableamount+vtiger_refillapprayment.backwashtotal))
                                {$rechargesourceStr}
                                WHERE
                                    vtiger_refillapprayment.receivedpaymentsid=vtiger_receivedpayments.receivedpaymentsid
                                AND vtiger_refillapprayment.deleted=0
                                AND vtiger_refillapprayment.refillapplicationid=?",
                                array($recordid));

    }
    private function setAuditInformation($recordid){
        $recordModel=Vtiger_Record_Model::getInstanceById($recordid,'RefillApplication');
        $totalrecharge=$recordModel->get('totalrecharge');//使用回款总款
        $actualtotalrecharge=$recordModel->get('actualtotalrecharge');//实际现金充值总额
        $rechargesource=$recordModel->get('rechargesource');
        if(in_array($rechargesource,array('TECHPROCUREMENT','NonMediaExtraction'))){
            $actualtotalrecharge=$recordModel->get('totalreceivables');//实际现金充值总额
        }

        $amountAvailable=$actualtotalrecharge-$totalrecharge;
        global $current_user;
        $departmentid=$_SESSION['userdepartmentid'];
        if(empty($departmentid)){
            $id=$_SESSION['authenticated_user_id'];
            if(empty($id)){
                $id=$current_user->id;
            }
            $user = new Users();
            $current_user_temp= $user->retrieveCurrentUserInfoFromFile($id);
            $departmentid=$current_user_temp->departmentid;
        }
        //global $current_user;
        if($rechargesource=='TECHPROCUREMENT' || $rechargesource=='OtherProcurement'){
            $modulenamed=$rechargesource=='TECHPROCUREMENT'?'techprocurement':'OtherProcurement';
            //$departmentid = empty($current_user->departmentid) ? 'H1' : $current_user->departmentid;
            /*$query = "SELECT vtiger_rechargeguarantee.userid,
                    vtiger_rechargeguarantee.twoleveluserid,
                    vtiger_rechargeguarantee.unitprice,
                    vtiger_rechargeguarantee.twounitprice,
                    vtiger_rechargeguarantee.threeleveluserid,
                    vtiger_rechargeguarantee.threeunitprice
                    FROM`vtiger_rechargeguarantee` 
                    INNER JOIN 
                    (SELECT vtiger_departments.parentdepartment,vtiger_departments.departmentid FROM vtiger_departments WHERE vtiger_departments.departmentid='{$departmentid}') AS tempdepart 
                    ON FIND_IN_SET(vtiger_rechargeguarantee.department,REPLACE(tempdepart.parentdepartment,'::',',')) 
                    LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_rechargeguarantee.department
                    WHERE domodule='{$modulenamed}'
                    AND vtiger_rechargeguarantee.deleted=0
                    ORDER BY abs(LENGTH(IFNULL(tempdepart.parentdepartment,0))-LENGTH(IFNULL(vtiger_departments.parentdepartment,0))) 
                    LIMIT 1";
            $result = $this->db->pquery($query, array());*/
            $accountGuarantee = $this->getRechargeGuarantee($modulenamed,$departmentid);
            if (!empty($accountGuarantee)) {
                //$accountGuarantee = $this->db->query_result_rowdata($result, 0);
                if ($totalrecharge <= $accountGuarantee['unitprice']) {
                    return array('flag' => true, 'userid' => array($accountGuarantee['userid']), 'advancesmoney' => $totalrecharge, 'rechargesource' => $rechargesource);
                } elseif ($totalrecharge <= $accountGuarantee['twounitprice']) {
                    return array('flag' => true, 'userid' => array($accountGuarantee['userid'], $accountGuarantee['twoleveluserid']), 'advancesmoney' => $totalrecharge, 'rechargesource' => $rechargesource);
                }elseif ($totalrecharge <= $accountGuarantee['threeunitprice']) {
                    return array('flag' => true, 'userid' => array($accountGuarantee['userid'], $accountGuarantee['twoleveluserid'],$accountGuarantee['threeleveluserid']), 'advancesmoney' => $totalrecharge, 'rechargesource' => $rechargesource);
                }
            }
        }elseif($rechargesource=='PreRecharge'){
            $query="SELECT sum(rechargeamount) AS rechargeamount FROM vtiger_rechargesheet WHERE refillapplicationid=? AND deleted=0";
            $result=$this->db->pquery($query,array($recordid));
            $resultData=$this->db->query_result_rowdata($result,0);
            $advancesmoney=$resultData['rechargeamount'];
            //默认担保
            //$departmentid = empty($current_user->departmentid) ? 'H1' : $current_user->departmentid;

            /*$query = "SELECT vtiger_rechargeguarantee.userid,
                    vtiger_rechargeguarantee.twoleveluserid,
                    vtiger_rechargeguarantee.unitprice,
                    vtiger_rechargeguarantee.twounitprice,
                    vtiger_rechargeguarantee.threeleveluserid,
                    vtiger_rechargeguarantee.threeunitprice
                    FROM`vtiger_rechargeguarantee` 
                    INNER JOIN 
                    (SELECT vtiger_departments.parentdepartment,vtiger_departments.departmentid FROM vtiger_departments WHERE vtiger_departments.departmentid='{$departmentid}') AS tempdepart 
                    ON FIND_IN_SET(vtiger_rechargeguarantee.department,REPLACE(tempdepart.parentdepartment,'::',',')) 
                    LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_rechargeguarantee.department
                    WHERE domodule='PreRecharge'
                    AND vtiger_rechargeguarantee.deleted=0
                    ORDER BY abs(LENGTH(IFNULL(tempdepart.parentdepartment,0))-LENGTH(IFNULL(vtiger_departments.parentdepartment,0))) 
                    LIMIT 1";
            $result = $this->db->pquery($query, array());*/
            $accountGuarantee = $this->getRechargeGuarantee('PreRecharge',$departmentid);
            if (!empty($accountGuarantee)) {
                //$accountGuarantee = $this->db->query_result_rowdata($result, 0);
                if ($advancesmoney <= $accountGuarantee['unitprice']) {
                    return array('flag' => true, 'userid' => array($accountGuarantee['userid']), 'advancesmoney' => $advancesmoney, 'rechargesource' => $rechargesource);
                } elseif ($advancesmoney <= $accountGuarantee['twounitprice']) {
                    return array('flag' => true, 'userid' => array($accountGuarantee['userid'], $accountGuarantee['twoleveluserid']), 'advancesmoney' => $advancesmoney, 'rechargesource' => $rechargesource);
                } elseif ($advancesmoney <= $accountGuarantee['threeunitprice']) {
                    return array('flag' => true, 'userid' => array($accountGuarantee['userid'], $accountGuarantee['twoleveluserid'],$accountGuarantee['threeleveluserid']), 'advancesmoney' => $advancesmoney, 'rechargesource' => $rechargesource);
                }
            }
        }else {
            if ($amountAvailable > 0) {
                $accountid = $recordModel->get('accountid');
                $result = $this->db->pquery("SELECT advancesmoney  FROM vtiger_account WHERE accountid =?", array($accountid));
                $advancesmoney = 0;
                if ($result && $this->db->num_rows($result) > 0) {
                    $row = $this->db->query_result_rowdata($result,0);
                    $advancesmoney = $row['advancesmoney'];
                }
                $result = $this->db->pquery("SELECT * FROM vtiger_accountrechargeguarantee WHERE deleted=0 AND accountid=?", array($accountid));
                //客户担保
                if ($this->db->num_rows($result)) {
                    $accountGuarantee = $this->db->query_result_rowdata($result, 0);
                    if ($advancesmoney <= $accountGuarantee['unitprice']) {
                        return array('flag' => true, 'userid' => array($accountGuarantee['userid']), 'advancesmoney' => $advancesmoney, 'rechargesource' => $rechargesource);
                    } elseif ($advancesmoney <= $accountGuarantee['twounitprice']) {
                        return array('flag' => true, 'userid' => array($accountGuarantee['userid'],$accountGuarantee['twoleveluserid']), 'advancesmoney' => $advancesmoney, 'rechargesource' => $rechargesource);
                    } elseif ($advancesmoney <= $accountGuarantee['threeunitprice']) {
                        return array('flag' => true, 'userid' => array($accountGuarantee['userid'], $accountGuarantee['twoleveluserid'],$accountGuarantee['threeleveluserid']), 'advancesmoney' => $advancesmoney, 'rechargesource' => $rechargesource);
                    }

                } else {
                    //默认担保
                    //$departmentid = empty($current_user->departmentid) ? 'H1' : $current_user->departmentid;
                    /*$query = "SELECT vtiger_rechargeguarantee.userid,
                    vtiger_rechargeguarantee.twoleveluserid,
                    vtiger_rechargeguarantee.unitprice,
                    vtiger_rechargeguarantee.twounitprice,
                    vtiger_rechargeguarantee.threeleveluserid,
                    vtiger_rechargeguarantee.threeunitprice
                    FROM`vtiger_rechargeguarantee`
                    INNER JOIN
                    (SELECT vtiger_departments.parentdepartment,vtiger_departments.departmentid FROM vtiger_departments WHERE vtiger_departments.departmentid='{$departmentid}') AS tempdepart
                    ON FIND_IN_SET(vtiger_rechargeguarantee.department,REPLACE(tempdepart.parentdepartment,'::',','))
                    LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_rechargeguarantee.department
                    WHERE domodule='rechargeguarantee'
                    AND vtiger_rechargeguarantee.deleted=0
                    ORDER BY abs(LENGTH(IFNULL(tempdepart.parentdepartment,0))-LENGTH(IFNULL(vtiger_departments.parentdepartment,0)))
                    LIMIT 1";*/
                    //$result = $this->db->pquery($query, array());
                    $accountGuarantee = $this->getRechargeGuarantee('rechargeguarantee',$departmentid);
                    if(!empty($accountGuarantee)){
                    //if ($this->db->num_rows($result)) {
                        //$accountGuarantee = $this->db->query_result_rowdata($result, 0);

                        if ($advancesmoney <= $accountGuarantee['unitprice']) {
                            return array('flag' => true, 'userid' => array($accountGuarantee['userid']), 'advancesmoney' => $advancesmoney, 'rechargesource' => $rechargesource);
                        } elseif ($advancesmoney <= $accountGuarantee['twounitprice']) {
                            return array('flag' => true, 'userid' => array($accountGuarantee['userid'], $accountGuarantee['twoleveluserid']), 'advancesmoney' => $advancesmoney, 'rechargesource' => $rechargesource);
                        } elseif ($advancesmoney <= $accountGuarantee['threeunitprice']) {
                            return array('flag' => true, 'userid' => array($accountGuarantee['userid'], $accountGuarantee['twoleveluserid'],$accountGuarantee['threeleveluserid']), 'advancesmoney' => $advancesmoney, 'rechargesource' => $rechargesource);
                        }
                    }
                }
                return array('flag' => true, 'userid' => array(38), 'advancesmoney' => $advancesmoney, 'rechargesource' => $rechargesource);
            }
        }
        return array('flag'=>false);
    }
    private function SetAuditDeparment($recordid){
        global $current_user;
        $recordModel = Vtiger_Record_Model::getInstanceById($recordid,'RefillApplication');
        $entity=$recordModel->entity->column_fields;
        //$departmentid=empty($current_user->departmentid)?'H1':$current_user->departmentid;
        //$result=$this->db->pquery("SELECT vtiger_auditsettings.* FROM `vtiger_auditsettings` INNER JOIN (SELECT vtiger_departments.parentdepartment,vtiger_departments.departmentid FROM vtiger_departments WHERE vtiger_departments.departmentid=?) AS tempdepart ON FIND_IN_SET(vtiger_auditsettings.department,REPLACE(tempdepart.parentdepartment,'::',',')) LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_auditsettings.department  WHERE vtiger_auditsettings.auditsettingtype='ContractsAgreement' ORDER BY abs(LENGTH(IFNULL(tempdepart.parentdepartment,0))-LENGTH(IFNULL(vtiger_departments.parentdepartment,0))) LIMIT 1",array($departmentid));
        //$data=$this->db->query_result_rowdata($result,0);
        /*$query="SELECT
                    vtiger_salesorderworkflowstages.*,
                    vtiger_workflowstages.workflowstagesflag
                FROM
                    `vtiger_salesorderworkflowstages`
                LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid
                WHERE
                    vtiger_salesorderworkflowstages.salesorderid = ?
                AND vtiger_salesorderworkflowstages.modulename='RefillApplication' ORDER BY salesorderworkflowstagesid";
        $result=$this->db->pquery($query,array($recordid));
        $query="UPDATE vtiger_salesorderworkflowstages SET ishigher=1,higherid=? WHERE vtiger_salesorderworkflowstages.modulename='RefillApplication' AND vtiger_salesorderworkflowstages.salesorderworkflowstagesid=?";
        while($row=$this->db->fetch_array($result)){
            if($row['workflowstagesflag']=='AUDIT_VERIFICATION'){
                //第一级审核
                $this->db->pquery($query,array($data['oneaudituid'],$row['salesorderworkflowstagesid']));
            }else if($row['workflowstagesflag']=='TWO_VERIFICATION'){
                //第二级审核
                $this->db->pquery($query,array($data['towaudituid'],$row['salesorderworkflowstagesid']));
            }else if($row['workflowstagesflag']=='THREE_VERIFICATION'){
                //第三级审核
                $this->db->pquery($query,array($data['audituid3'],$row['salesorderworkflowstagesid']));
            }
        }*/
        $query="SELECT vtiger_salesorderworkflowstages.higherid,vtiger_products.productman FROM vtiger_salesorderworkflowstages LEFT JOIN vtiger_products ON vtiger_products.productid=vtiger_salesorderworkflowstages.productid WHERE salesorderid=?";
        $result=$this->db->pquery($query,array($recordid));
        $userid=array();
        while($row=$this->db->fetch_array($result)){
            if($row['higherid']>0){
                $userid[]=$row['higherid'];
            }
            if(!empty($row['productman'])){
                $productman=explode(" |##| ",$row['productman']);
                $userid=array_merge($userid,$productman);
            }
        }
        if(!empty($userid)){
            $userids=array();
            foreach($userid as $value){
                if($value>0){
                    $userids[]=$value;
                }
            }
            $userids=implode(',',$userids);
            $userids=trim($userids,',');
            if(empty($userids))return;
            $query="SELECT vtiger_users.last_name,vtiger_users.email1 FROM vtiger_users WHERE vtiger_users.id in({$userids}) AND `status`='Active'";
            $result=$this->db->pquery($query,array());
            $emails=array();
            while($row=$this->db->fetch_array($result)){
                $emails[]=array('email'=>$row['email1'],'name'=>$row['last_name']);
            }
            $Subject = '待审核充值申请单邮件提醒';
            //内容
            $body  = '有充值申请单,需要您的审核,详情如下：<br><br> ';
            $body .= "申请单编号:&nbsp;&nbsp;<a href='".$_SERVER['HTTP_HOST']."/index.php?module=RefillApplication&view=Detail&record={$recordid}'>{$entity['refillapplicationno']}</a><br/>";
            $body .= '申请时间:&nbsp;&nbsp;'.date('Y-m-d H:i:s');
            if(!empty($emails)){
                Vtiger_Record_Model::sendMail($Subject,$body,$emails);
            }
        }
    }
    /**
     * 获取部门指定的审核人
     * @param $moduleName
     * @param $departmentid
     * @return array
     * @author: steel.liu
     * @Date:2018-07-25
     *
     */
    public function getRechargeGuarantee($moduleName,$departmentid){
        $result=$this->db->pquery("SELECT parentdepartment,departmentid FROM vtiger_departments WHERE vtiger_departments.departmentid =?",array($departmentid));
        //$returnData=array('userid'=>2110,'twoleveluserid'=>2110,'threeleveluserid'=>2110,'unitprice'=>0,'twounitprice'=>0,'threeunitprice'=>0);
        $returnData=array();
        if($this->db->num_rows($result)>0){
            $data=$this->db->raw_query_result_rowdata($result,0);
            $departmentids=explode("::",$data['parentdepartment']);
            $departmentids=array_reverse($departmentids);
            foreach($departmentids AS $value){
                $result=$this->db->pquery("SELECT vtiger_rechargeguarantee.userid,
                                                    vtiger_rechargeguarantee.twoleveluserid,
                                                    vtiger_rechargeguarantee.unitprice,
                                                    vtiger_rechargeguarantee.twounitprice,
                                                    vtiger_rechargeguarantee.threeleveluserid,
                                                    vtiger_rechargeguarantee.threeunitprice
                                                FROM`vtiger_rechargeguarantee` 
                                                WHERE domodule=?
                                                AND vtiger_rechargeguarantee.deleted=0
                                                AND vtiger_rechargeguarantee.department=?",array($moduleName,$value));
                if($this->db->num_rows($result)){
                    $returnData=$this->db->query_result_rowdata($result,0);
                    break;
                }
            }
        }
        return $returnData;
    }

    /**
     * @param $recordModel
     * @author: steel.liu
     * @Date:xxx
     * 赠款申请添加回款
     */
    public function addReceivedPayments($recordModel){
        $recordid=$recordModel->get('record_id');
        $user = new Users();
        $user_obj = $user->retrieveCurrentUserInfoFromFile($recordModel->get('assigned_user_id'));
        $departmentid=$user_obj->departmentid;
        $refillapplicationno=$recordModel->get('refillapplicationno');
        global $current_user;
        $query='SELECT * FROM vtiger_rechargesheet WHERE deleted=0 AND refillapplicationid=?';
        $result=$this->db->pquery($query,array($recordid));
        //$save = new Vtiger_Save_Action();
        while($row=$this->db->fetch_array($result)){
            $fieldname['receivedpaymentsid']=$this->db->getUniqueID('vtiger_receivedpayments');
            $fieldname['reality_date']=date('Y-m-d');
            $fieldname['unit_price']=$row['cashincrease'];
            $fieldname['standardmoney']=$row['cashincrease'];
            $fieldname['rechargeableamount']=$row['cashincrease'];
            $fieldname['allowinvoicetotal']=0;
            $fieldname['relatetoid']=$row['mservicecontractsid'];
            $fieldname['owncompany']="赠款申请";
            $fieldname['exchangerate']=1;
            if(empty($row['receivementcurrencytype'])){
                $row['receivementcurrencytype']="人民币";
            }
            $fieldname['receivementcurrencytype']=$row['receivementcurrencytype'];
            $fieldname['paytitle']=$row['maccountid_name'];
            $fieldname['maybe_account']=$row['maccountid'];
            $fieldname['receivedstatus']='virtualrefund';
            $fieldname['createdtime']=date('Y-m-d H:i:s');
            $fieldname['createtime']=date('Y-m-d H:i:s');
            $fieldname['createid']=$current_user->id;
            $fieldname['ismatchdepart']=1;
            $fieldname['checkid']=$current_user->id;
            $fieldname['modulename']='ServiceContracts';
            $fieldname['matchdate']=date('Y-m-d');
            $fieldname['departmentid']=$departmentid;
            $fieldname['rorigin']=$refillapplicationno.'_'.$row['grantquarter'];
            $fieldname['newdepartmentid']=$departmentid;
            $fieldname['paymentamount']=$row['cashincrease'];
            $fieldname['refillapplicationid']=$recordid;
            $fieldNames = array_keys($fieldname);
            $fieldValues = array_values($fieldname);
            $this->db->pquery('INSERT INTO vtiger_receivedpayments ('. implode(',', $fieldNames).') VALUES ('. generateQuestionMarks($fieldValues) .')', $fieldValues);
            //$save->saveRecord(new Vtiger_Request($fieldname, $fieldname));
        }

    }
}

?>
