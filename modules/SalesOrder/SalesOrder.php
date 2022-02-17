<?php
include_once('config.php');
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');
require_once('include/utils/utils.php');
require_once('user_privileges/default_module_view.php');
// Account is used to store vtiger_account information.
class SalesOrder extends CRMEntity {
	var $log;
	var $db;
	var $table_name = "vtiger_salesorder";
	var $table_index= 'salesorderid';
	//var $tab_name = Array('vtiger_crmentity','vtiger_salesorder','vtiger_salesordercf','vtiger_inventoryproductrel');
	//var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_salesorder'=>'salesorderid','vtiger_salesordercf'=>'salesorderid','vtiger_inventoryproductrel'=>'id');
    var $tab_name = Array('vtiger_crmentity','vtiger_salesorder');
    var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_salesorder'=>'salesorderid');
    /**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('vtiger_salesordercf', 'salesorderid');
	var $entity_table = "vtiger_crmentity";
	var $billadr_table = "vtiger_sobillads";
	var $object_name = "SalesOrder";
	var $new_schema = true;
	var $update_product_array = Array();
	var $column_fields = Array();
	var $sortby_fields = Array('subject','smownerid','accountname','lastname');
	// This is used to retrieve related vtiger_fields from form posts.
	var $additional_column_fields = Array('assigned_user_name', 'smownerid', 'opportunity_id', 'case_id', 'contact_id', 'task_id', 'note_id', 'meeting_id', 'call_id', 'email_id', 'parent_name', 'member_id' );
	// This is the list of vtiger_fields that are in the lists.
	var $list_fields = Array(
				// Module Sequence Numbering
				//'Order No'=>Array('crmentity'=>'crmid'),
				'Order No'=>Array('salesorder'=>'salesorder_no'),
				// END
				'Subject'=>Array('salesorder'=>'subject'),
				'Account Name'=>Array('account'=>'accountid'),
				'Quote Name'=>Array('quotes'=>'quoteid'),
				'Total'=>Array('salesorder'=>'total'),
				'Assigned To'=>Array('crmentity'=>'smownerid'),
				'Status'=>array('salesorder'=>'sostatus')
				);

	var $list_fields_name = Array(
				        'Order No'=>'salesorder_no',
				        'Subject'=>'subject',
				        'Account Name'=>'account_id',
				        'Quote Name'=>'quote_id',
					'Total'=>'hdnGrandTotal',
				        'Assigned To'=>'assigned_user_id',
						'Status'=>'sostatus'
				      );
	var $list_link_field= 'subject';
	//弹出页面的搜索下拉字段 wangbin 
	var $search_fields = Array(
		'Order No'=>Array('salesorder'=>'salesorder_no'),'Subject'=>Array('salesorder'=>'subject'),'Quote Name'=>Array('salesorder'=>'quoteid')
			//'Account Name'=>Array('account'=>'accountid'),
	);
	//弹出页面列表字段的显示控制 wangbin
	var $search_fields_name = Array(
		'Order No'=>'salesorder_no','Subject'=>'subject',
			// 'Account Name'=>'account_id',// 'Quote Name'=>'quote_id'
	);
	// This is the list of vtiger_fields that are required.
	var $required_fields =  array("accountname"=>1);
	//Added these variables which are used as default order by and sortorder in ListView
	var $default_order_by = 'subject';
	var $default_sort_order = 'ASC';
	//var $groupTable = Array('vtiger_sogrouprelation','salesorderid');
	var $mandatory_fields = Array('subject','createdtime' ,'modifiedtime', 'assigned_user_id');
	// For Alphabetical search
	var $def_basicsearch_col = 'subject';

	// For workflows update field tasks is deleted all the lineitems.
	var $isLineItemUpdate = true;

    //2015年9月11日  wangibn添加右侧域名备案关联;
    var $relatedmodule_list=array('IdcRecords');
    var $relatedmodule_fields=array('IdcRecords'=>array('salesorder_no'=>'工单编号','idcstate'=>'状态','ipaddress'=>'IP地址'));

	/** Constructor Function for SalesOrder class
	 *  This function creates an instance of LoggerManager class using getLogger method
	 *  creates an instance for PearDatabase class and get values for column_fields array of SalesOrder class.
	 */
	function SalesOrder() {
		$this->log =LoggerManager::getLogger('SalesOrder');
		$this->db = PearDatabase::getInstance();
		$this->column_fields = getColumnFields('SalesOrder');
	}

	function save_module($module){
		global $configcontracttypeName,$configcontracttypeNameTYUN;
		//然并卵
		/* if(!empty($_REQUEST['notecontent'])){
			$update_query = "update vtiger_salesordercf set notecontent=? where salesorderid=?";
			$update_params = array($_REQUEST['notecontent'], $this->id);
			$this->db->pquery($update_query, $update_params);
		} */
		$productidlist=array();


		if(isset($_POST['p'])){
			$form_info=$_POST['p'];

			//if(!empty($record)){
			/* $details=$db->pquery('SELECT vtiger_salesorder_productdetail.FormInput,vtiger_salesorder_productdetail.TplId,vtiger_salesorder_productdetail.Productid,vtiger_formdesign.content_parse,vtiger_formdesign.field FROM vtiger_salesorder_productdetail LEFT JOIN vtiger_formdesign ON vtiger_formdesign.formid = vtiger_salesorder_productdetail.TplId WHERE vtiger_salesorder_productdetail.SalesOrderId=?',array($record));
			$rows=$db->num_rows($details); */
			//$details=$db->pquery('SELECT vtiger_formdesign.content_parse,vtiger_formdesign.formid as TplId,vtiger_formdesign.field FROM vtiger_formdesign WHERE vtiger_formdesign.formid in('.generateQuestionMarks($_POST['tpl']).')',$_POST['tpl']);
			$productids=array_keys($_POST['tpl']);
			$historys=array();
            $isedit = false;
			if($_REQUEST['record']){
                $isedit = true;
				$details=$this->db->pquery('SELECT p.Productid,p.FormInput,p.TplId,f.field,f.content_parse FROM vtiger_salesorder_productdetail AS p LEFT JOIN vtiger_formdesign AS f ON f.formid = p.TplId WHERE p.SalesOrderId =?',array($_REQUEST['record']));
                if(!empty($_POST['productid'])){
					$this->db->pquery('delete from vtiger_salesorder_productdetail where SalesOrderId='.$this->id);
				}
			}else{
				$details=$this->db->pquery('SELECT m.relateid as productid, f.content_parse,f.formid AS tplid,f.field FROM `vtiger_customer_modulefields` as m LEFT JOIN vtiger_formdesign as f ON f.formid = m.formid where relateid in('.generateQuestionMarks($productids).')',$productids);
			}
			$rows=$this->db->num_rows($details);

			if($rows>0){
				require 'include/utils/formparse.php';
				global $current_user;
				while($row=$this->db->fetchByAssoc($details)){
				//foreach($_POST['tpl'] as $productid=>$tplid){$row=$db->fetchByAssoc($details);$row['field']=empty($row['field'])?$productid:$row['field'];
					if($_POST['action']=='SaveAjax' && empty($_POST['tpl'][$row['productid']])){
						continue;
					}
					$datas=json_decode(str_replace('&quot;','"',$row['field']),true);
					$parse=parse_tohtml($datas,$row['content_parse'],$form_info[$row['productid']]);
					$_REQUEST['productnote'][$row['productid']]=$parse[0];
					$historys[$row['productid']]=$parse[1];
					$forminput=json_encode($parse[1]);
					$this->db->pquery('INSERT INTO  vtiger_salesorder_productdetail (SalesOrderId,Productid,FormInput,TplId) VALUES (?,?,?,?) on  DUPLICATE key UPDATE FormInput=?',array($this->id,$row['productid'],$forminput,$row['tplid'],$forminput));
					//对比修改?
					if($_REQUEST['record']){
						//$details=$this->db->pquery('SELECT vtiger_salesorder_productdetail.FormInput,vtiger_salesorder_productdetail.Productid FROM vtiger_salesorder_productdetail WHERE vtiger_salesorder_productdetail.SalesOrderId=?',array($_REQUEST['record']));
						/* $rows=$this->db->num_rows($details2);
						if($rows){ */
						//while($row1=$this->db->fetchByAssoc($details2)){
								$values=json_decode(str_replace('&quot;','"',$row['forminput']),true);
								foreach($values as $key=>$value){
									if(trim($value)==trim($parse[1][$key])){
										unset($historys[$row['productid']][$key]);
									}
								}
							//}
						//}
					}
				}
                /*print_r($_REQUEST);
                exit;*/
				if(!empty($historys)){
					foreach($historys as $key=> $history){
						if(!empty($history)){
							$param=array($this->id,$key,json_encode($history),$_POST['tpl'][$key],date("Y-m-d H:i:s"),$current_user->id);
							$this->db->pquery('INSERT INTO vtiger_salesorder_productdetail_history (SalesOrderId,Productid,FormInput,TplId,AddTime,EditId) VALUES (?,?,?,?,?,?)',$param);
						}
					}
				}
			}
		}
		
		
		if(!empty($_POST['servicecontractsid'])){
			global $current_user;
			$checkarray=array();
			$serviceid = $_REQUEST['servicecontractsid'];//合同id
			if(!$isedit) {
				$twebproduct = array();
				$query = 'SELECT vtiger_servicecontracts.contract_type,vtiger_servicecontracts.productid,vtiger_servicecontracts.extraproductid FROM `vtiger_servicecontracts` WHERE servicecontractsid=?';
				$servicecontract_result = $this->db->pquery($query, array($serviceid));
				$servicecontract_num = $this->db->num_rows($servicecontract_result);
				if ($servicecontract_num){
					$result_data = $this->db->raw_query_result_rowdata($servicecontract_result, 0);
					if (in_array($result_data['contract_type'],$configcontracttypeNameTYUN) && !empty($result_data['productid'])) {
						$productidArray = explode(',', $result_data['productid']);
						$query = "SELECT tempproducts.productid,'' AS `productform`,'' AS istyunweb,tempproducts1.productname as twebthepackage,'' AS salesorderid,tempproducts.isfillincost,tempproducts1.productname AS productcomboid,tempproducts.extracost FROM vtiger_products tempproducts
                            LEFT JOIN vtiger_seproductsrel ON vtiger_seproductsrel.crmid=tempproducts.productid 
                            LEFT JOIN vtiger_products tempproducts1 ON tempproducts1.productid=vtiger_seproductsrel.productid
                            WHERE FIND_IN_SET(?,tempproducts1.twebpackageproductid)";
						$copy_salpro = "SELECT  productid, producttype, createtime, creatorid, salesorderproductsrelstatus, ownerid, servicecontractsid, accountid, realmarketprice, marketprice, productcomboid, productsolution, producttext, productnumber, agelife, standard, thepackage, isextra, prealprice, punit_price, pmarketprice, costing, purchasemount, multistatus, vendorid, suppliercontractsid,istyunweb,extracost FROM vtiger_salesorderproductsrel WHERE servicecontractsid=? and productcomboid = ? AND ( multistatus = ? OR multistatus = ? ) limit 1";//363756
						foreach ($productidArray as $value) {
							$copy_data_result = $this->db->pquery($copy_salpro, array($serviceid,$value, 0, 1));
							if ($this->db->num_rows($copy_data_result) > 0) {
								$copy_data_data1=$this->db->fetchByAssoc($copy_data_result);
								$productidlistResult = $this->db->pquery($query, array($value));
								if ($this->db->num_rows($productidlistResult)) {
									while ($rowdata = $this->db->fetch_array($productidlistResult)) {
										if (!in_array($rowdata['productid'], $twebproduct)) {
											$twebproduct[] = $rowdata['productid'];
											$copy_data_data1['productid'] = $rowdata['productid'];
											$copy_data_data1['costing'] = 1;
											$copy_data_data1['isfillincost'] = $rowdata['isfillincost'];
											$copy_data_data1['purchasemount'] = 1;
											$copy_data_data1['createtime'] = date('Y-m-d H:i:s');
											$copy_data_data1['multistatus'] = 2;
											$copy_data_data1['salesorderproductsrelid'] = $this->db->getUniqueID('vtiger_salesorderproductsrel');
											$this->db->pquery("insert into vtiger_salesorderproductsrel (" . implode(",", array_keys($copy_data_data1)) . ") values(" . generateQuestionMarks($copy_data_data1) . ")", $copy_data_data1);
										}
									}
								}
							}
						}
					}
				}
				$copy_salpro = "SELECT  productid, producttype, createtime, creatorid, salesorderproductsrelstatus, ownerid, servicecontractsid, accountid, realmarketprice, marketprice, productcomboid, productsolution, producttext, productnumber, agelife, standard, thepackage, isextra, prealprice, punit_price, pmarketprice, costing, purchasemount, multistatus, vendorid, suppliercontractsid,istyunweb,extracost FROM vtiger_salesorderproductsrel WHERE servicecontractsid = ? AND ( multistatus = ? OR multistatus = ? )";//363756
				$copy_data = $this->db->pquery($copy_salpro, array($serviceid, 0, 1));
				if ($this->db->num_rows($copy_data) > 0 && !$isedit) {
					while ($row_copydata = $this->db->fetchByAssoc($copy_data)) {
						if ($row_copydata['istyunweb'] == 1) {
							$query = "SELECT * FROM vtiger_products WHERE FIND_IN_SET(?,twebproductid)";
							$resultdata = $this->db->pquery($query, array($row_copydata['productid']));
							$resultnum = $this->db->num_rows($resultdata);
							if ($resultnum) {
								$resultdata1 = $this->db->raw_query_result_rowdata($resultdata, 0);
								if (!in_array($resultdata1['productid'], $twebproduct)) {
									$twebproduct[] = $resultdata1['productid'];
									$row_copydata['productid'] = $resultdata1['productid'];
									$row_copydata['createtime'] = date('Y-m-d H:i:s');
									$row_copydata['multistatus'] = 2;
									$row_copydata['costing'] = 1;
									$row_copydata['purchasemount'] = 1;
									$row_copydata['salesorderproductsrelid'] = $this->db->getUniqueID('vtiger_salesorderproductsrel');
									$this->db->pquery("insert into vtiger_salesorderproductsrel (" . implode(",", array_keys($row_copydata)) . ") values(" . generateQuestionMarks($row_copydata) . ")", $row_copydata);
								}

							}
						} else {
							$row_copydata['createtime'] = date('Y-m-d H:i:s');
							$row_copydata['multistatus'] = 2;
							$row_copydata['salesorderproductsrelid'] = $this->db->getUniqueID('vtiger_salesorderproductsrel');
							$this->db->pquery("insert into vtiger_salesorderproductsrel (" . implode(",", array_keys($row_copydata)) . ") values(" . generateQuestionMarks($row_copydata) . ")", $row_copydata);
						}
					}
				}
			}
				if(!empty($_REQUEST['productnote'])){
				    $arr_salesorderproductsrelids=array();
					$datas = $_REQUEST['productnote'];
					foreach($datas as $key=>$val){
						$productidlist[]=$key;
                        if($isedit){
                            $this->db->pquery("update vtiger_salesorderproductsrel set productform=? where productid=? and servicecontractsid=? AND salesorderid=? ",array($val, $key, $serviceid, $this->id));
                            //$this->db->pquery("update vtiger_salesorderproductsrel set productform=?,salesorderid=? where productid=? and servicecontractsid=? AND multistatus=?",array($val,$this->id,$key,$serviceid,3));
                        }else{
                            $this->db->pquery("update vtiger_salesorderproductsrel set multistatus=?, productform=?,salesorderid=? where productid=? and servicecontractsid=? AND multistatus=?",array(3,$val,$this->id,$key,$serviceid,2));
                        }
					}
					//2015-2-12 新增产品负责人 
					$result = $this->db->pquery("SELECT vtiger_crmentity.smcreatorid, vtiger_products.productname,vtiger_products.productid,vtiger_products.productman FROM `vtiger_salesorderproductsrel` LEFT JOIN vtiger_products ON vtiger_products.productid = vtiger_salesorderproductsrel.productid LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_salesorderproductsrel.productid WHERE salesorderid =? ",array($this->id));
					while($product=$this->db->fetch_row($result)){
						$checkarray[]=array('workflowstagesname'=> $product['productname'].'审核','smcreatorid'=>0,'productid'=>$product['productid'],'productman'=>$product['productman']);
					}
					vglobal('checkproducts',$checkarray);
					$re=$this->db->pquery("select group_concat(cast(salesorderproductsrelid as CHAR)) as sid from vtiger_salesorderproductsrel where servicecontractsid=? group by servicecontractsid",array($serviceid));
					if($this->db->num_rows($re)){
						$arr_salesorderproductsrelids=$this->db->query_result($re, 0,'sid');
					}
					ServiceComments_Record_Model::insertServiceCommentsByProducts($arr_salesorderproductsrelids);//产品客服分配
				}
				
			//}
			//$accountid=$this->db->query_result($result, 0,'accountid');
			//客户客服分配
			ServiceComments_Record_Model::insertServiceCommentsByAccounts($this->column_fields['account_id']);
            //2016年3月9日 如果合同的客户是商机客户 就需要标记一下工单;
            $sel_fircontrac_sql = "SELECT firstfrommarket FROM vtiger_servicecontracts WHERE servicecontractsid = ?";
            $sel_fircontra_data = $this->db->pquery($sel_fircontrac_sql,array($serviceid));
            $this->db->pquery('UPDATE vtiger_salesorder SET customer_name=? WHERE salesorderid = ?',array($_REQUEST['account_id_display'],$this->id));
            if($this->db->num_rows($sel_fircontra_data)>0 && $this->db->query_result($sel_fircontra_data,0,'firstfrommarket')==1){
                $this->db->pquery('UPDATE vtiger_salesorder SET customer_name=?,isfrommarkets = ? WHERE salesorderid = ?',array($_REQUEST['account_id_display'],'1',$this->id));
            }

		}
		//内部工单 
        if(!empty($_REQUEST["productid"])){
			$productids=$_REQUEST['productids'];
			$record=$_REQUEST['record'];
			if(!empty($record)){
				//编辑模式下产品更新
				$_REQUEST['currentid']=$record;
				$this->db->pquery('delete from vtiger_salesorderproductsrel where salesorderid=?',array($record));
			}
			$products=$this->db->pquery('SELECT vtiger_products.productname, vtiger_products.productcategory, vtiger_products.realprice, vtiger_products.unit_price, vtiger_crmentity.smownerid, vtiger_products.productid,vtiger_products.extracost FROM vtiger_products INNER JOIN vtiger_crmentity ON vtiger_products.productid = vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted = 0 AND vtiger_products.productid in ('.implode(',',$productids).')',array());
			$rows=$this->db->num_rows($products);
            /*echo "<pre>";
            print_r($_REQUEST);
            exit;*/
			$checkarray=array();
			for ($i=0; $i<$rows; ++$i) {
				$product = $this->db->fetchByAssoc($products);
				$checkarray[]=array('workflowstagesname'=> $product['productname'].'审核','smcreatorid'=>$product['smownerid'],'productid'=>$product['productid']);
				$productidlist[]=$product['productid'];
				$array=array('salesorderproductsrelid'=>$this->db->getUniqueID('vtiger_salesorderproductsrel'),'productid'=>$product['productid'],'producttype'=>$product['productcategory'],'realmarketprice'=>$product['realprice'],'marketprice'=>$product['unit_price'],'extracost'=>$product['extracost'],'createtime'=>date('Y-m-d H:i:s'),'creatorid'=>$current_user->id,'salesorderproductsrelstatus'=>$status,'ownerid'=>$current_user->id,'salesorderid'=>$_REQUEST['currentid'],'accountid'=>$_REQUEST['sc_related_to'],'productform'=>$_REQUEST['productnote'][$product['productid']],'producttext'=>$_REQUEST['producttext'][$product['productid']]);
				$this->db->pquery("insert into vtiger_salesorderproductsrel (" . implode(",", array_keys($array)) . ") values(" . generateQuestionMarks($array) . ")",$array);
                //print_r($array);
                //echo "insert into vtiger_salesorderproductsrel (" . implode(",", array_keys($array)) . ") values(" . generateQuestionMarks($array) . ")";

			}
            //exit;
            vglobal('checkproducts',$checkarray);
			
		}
if($_POST['action']=='SaveAjax'){
	foreach($_REQUEST['productnote'] as $key=>$val){
		$this->db->pquery("update vtiger_salesorderproductsrel set productform=? where productid=? and salesorderid=? ",array($val,$key,$this->id));
	}
}
		
		
		if(!empty($productidlist)){
			$this->db->pquery('UPDATE vtiger_salesorder set productname =(SELECT GROUP_CONCAT(productname  separator  \'<br>\') from vtiger_products where productid in('.implode(',',$productidlist).')) where salesorderid=?',array($this->id));
		
		}
		
			/* if(!empty($_REQUEST['data'])){
				$datas=$_REQUEST['data'];
				$temp=array();
				//需要判断编辑是否更新产品
				if(!empty($datas)&&count($datas)>2){
					//@TODO 如果是编辑，在受限条件的时候需要删删除老的数据 ,如果老数据状态改变就无法
					$this->db->pquery("delete from vtiger_salesorderproductsrel where salesorderid=?",array($this->id));
					foreach ($datas as $key=>$val){
						//if($key>1){
							if(in_array($key,$temp)||empty($val['productid'])){continue;}//如果产品id已经存在则跳出
							$temp[]=$key;
							//@TODO 存储字段不够 ,产品去重
							$unid=$this->db->getUniqueID('vtiger_salesorderproductsrel');
							$sql="insert into vtiger_salesorderproductsrel(salesorderid,productid,productform,productcomboid,createtime,creatorid,schedule,salesorderproductsrelstatus) values($this->id,?,?,?,?,?,0,'unaudited')";
							
							$this->db->pquery($sql,array($val['productid'],$val['comment'],$val['productcomboid'],date('Y-m-d H:i:s',now()),$current_user->id));
						//}
					}
					//类型字段				
			} 
		 }*/
		//工作流
		//saveSalesOrderWorkflowStages($_REQUEST['workflowsid'],$this);
		
	}

	/** Function to get activities associated with the Sales Order
	 *  This function accepts the id as arguments and execute the MySQL query using the id
	 *  and sends the query and the id as arguments to renderRelatedActivities() method
	 */
	function get_activities($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $singlepane_view,$currentModule,$current_user;
		$this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once("modules/$related_module/Activity.php");
		$other = new Activity();
        vtlib_setup_modulevars($related_module, $other);
		$singular_modname = vtlib_toSingular($related_module);

		$parenttab = getParentTab();

		if($singlepane_view == 'true')
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		else
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;

		$button = '';

		$button .= '<input type="hidden" name="activity_mode">';

		if($actions) {
			if(is_string($actions)) $actions = explode(',', strtoupper($actions));
			if(in_array('ADD', $actions) && isPermitted($related_module,1, '') == 'yes') {
				if(getFieldVisibilityPermission('Calendar',$current_user->id,'parent_id', 'readwrite') == '0') {
					$button .= "<input title='".getTranslatedString('LBL_NEW'). " ". getTranslatedString('LBL_TODO', $related_module) ."' class='crmbutton small create'" .
						" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\";this.form.return_module.value=\"$this_module\";this.form.activity_mode.value=\"Task\";' type='submit' name='button'" .
						" value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString('LBL_TODO', $related_module) ."'>&nbsp;";
				}
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "SELECT case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name,vtiger_contactdetails.lastname, vtiger_contactdetails.firstname, vtiger_contactdetails.contactid, vtiger_activity.*,vtiger_seactivityrel.crmid as parent_id,vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_crmentity.modifiedtime from vtiger_activity inner join vtiger_seactivityrel on vtiger_seactivityrel.activityid=vtiger_activity.activityid inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_activity.activityid left join vtiger_cntactivityrel on vtiger_cntactivityrel.activityid= vtiger_activity.activityid left join vtiger_contactdetails on vtiger_contactdetails.contactid = vtiger_cntactivityrel.contactid left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid where vtiger_seactivityrel.crmid=".$id." and activitytype='Task' and vtiger_crmentity.deleted=0 and (vtiger_activity.status is not NULL and vtiger_activity.status != 'Completed') and (vtiger_activity.status is not NULL and vtiger_activity.status !='Deferred')";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;
		return $return_value;
	}

	/** Function to get the activities history associated with the Sales Order
	 *  This function accepts the id as arguments and execute the MySQL query using the id
	 *  and sends the query and the id as arguments to renderRelatedHistory() method
	 */
	function get_history($id){
		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "SELECT vtiger_contactdetails.lastname, vtiger_contactdetails.firstname,
			vtiger_contactdetails.contactid,vtiger_activity.*, vtiger_seactivityrel.*,
			vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_crmentity.modifiedtime,
			vtiger_crmentity.createdtime, vtiger_crmentity.description, case when
			(vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname
			end as user_name from vtiger_activity
				inner join vtiger_seactivityrel on vtiger_seactivityrel.activityid=vtiger_activity.activityid
				inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_activity.activityid
				left join vtiger_cntactivityrel on vtiger_cntactivityrel.activityid= vtiger_activity.activityid
				left join vtiger_contactdetails on vtiger_contactdetails.contactid = vtiger_cntactivityrel.contactid
                                left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
				left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
			where activitytype='Task'
				and (vtiger_activity.status = 'Completed' or vtiger_activity.status = 'Deferred')
				and vtiger_seactivityrel.crmid=".$id."
                                and vtiger_crmentity.deleted = 0";
		//Don't add order by, because, for security, one more condition will be added with this query in include/RelatedListView.php
		return getHistory('SalesOrder',$query,$id);
	}



	/** Function to get the invoices associated with the Sales Order
	 *  This function accepts the id as arguments and execute the MySQL query using the id
	 *  and sends the query and the id as arguments to renderRelatedInvoices() method.
	 */
	function get_invoices($id)
	{
		global $singlepane_view;
		
		require_once('modules/Invoice/Invoice.php');

		$focus = new Invoice();

		$button = '';
		if($singlepane_view == 'true')
			$returnset = '&return_module=SalesOrder&return_action=DetailView&return_id='.$id;
		else
			$returnset = '&return_module=SalesOrder&return_action=CallRelatedList&return_id='.$id;

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "select vtiger_crmentity.*, vtiger_invoice.*, vtiger_account.accountname,
			vtiger_salesorder.subject as salessubject, case when
			(vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname
			end as user_name from vtiger_invoice
			inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_invoice.invoiceid
			left outer join vtiger_account on vtiger_account.accountid=vtiger_invoice.accountid
			inner join vtiger_salesorder on vtiger_salesorder.salesorderid=vtiger_invoice.salesorderid
			left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
			left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
			where vtiger_crmentity.deleted=0 and vtiger_salesorder.salesorderid=".$id;

		
		return GetRelatedList('SalesOrder','Invoice',$focus,$query,$button,$returnset);

	}

	/**	Function used to get the Status history of the Sales Order
	 *	@param $id - salesorder id
	 *	@return $return_data - array with header and the entries in format Array('header'=>$header,'entries'=>$entries_list) where as $header and $entries_list are arrays which contains header values and all column values of all entries
	 */
	function get_sostatushistory($id){
		

		global $adb;
		global $mod_strings;
		global $app_strings;

		$query = 'select vtiger_sostatushistory.*, vtiger_salesorder.salesorder_no from vtiger_sostatushistory inner join vtiger_salesorder on vtiger_salesorder.salesorderid = vtiger_sostatushistory.salesorderid inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_salesorder.salesorderid where vtiger_crmentity.deleted = 0 and vtiger_salesorder.salesorderid = ?';
		$result=$adb->pquery($query, array($id));
		$noofrows = $adb->num_rows($result);

		$header[] = $app_strings['Order No'];
		$header[] = $app_strings['LBL_ACCOUNT_NAME'];
		$header[] = $app_strings['LBL_AMOUNT'];
		$header[] = $app_strings['LBL_SO_STATUS'];
		$header[] = $app_strings['LBL_LAST_MODIFIED'];

		//Getting the field permission for the current user. 1 - Not Accessible, 0 - Accessible
		//Account Name , Total are mandatory fields. So no need to do security check to these fields.
		global $current_user;

		//If field is accessible then getFieldVisibilityPermission function will return 0 else return 1
		$sostatus_access = (getFieldVisibilityPermission('SalesOrder', $current_user->id, 'sostatus') != '0')? 1 : 0;
		$picklistarray = getAccessPickListValues('SalesOrder');

		$sostatus_array = ($sostatus_access != 1)? $picklistarray['sostatus']: array();
		//- ==> picklist field is not permitted in profile
		//Not Accessible - picklist is permitted in profile but picklist value is not permitted
		$error_msg = ($sostatus_access != 1)? 'Not Accessible': '-';

		while($row = $adb->fetch_array($result))
		{
			$entries = Array();

			// Module Sequence Numbering
			//$entries[] = $row['salesorderid'];
			$entries[] = $row['salesorder_no'];
			// END
			$entries[] = $row['accountname'];
			$entries[] = $row['total'];
			$entries[] = (in_array($row['sostatus'], $sostatus_array))? $row['sostatus']: $error_msg;
			$date = new DateTimeField($row['lastmodified']);
			$entries[] = $date->getDisplayDateTimeValue();

			$entries_list[] = $entries;
		}

		$return_data = Array('header'=>$header,'entries'=>$entries_list);
		return $return_data;
	}

	/*
	 * Function to get the secondary query part of a report
	 * @param - $module primary module name
	 * @param - $secmodule secondary module name
	 * returns the query string formed on fetching the related data for report for secondary module
	 */
	function generateReportsSecQuery($module,$secmodule,$queryPlanner){
		$matrix = $queryPlanner->newDependencyMatrix();
		$matrix->setDependency('vtiger_crmentitySalesOrder', array('vtiger_usersSalesOrder', 'vtiger_groupsSalesOrder', 'vtiger_lastModifiedBySalesOrder'));		
		$matrix->setDependency('vtiger_inventoryproductrelSalesOrder', array('vtiger_productsSalesOrder', 'vtiger_serviceSalesOrder'));
		$matrix->setDependency('vtiger_salesorder',array('vtiger_crmentitySalesOrder', "vtiger_currency_info$secmodule",
				'vtiger_salesordercf', 'vtiger_potentialRelSalesOrder', 'vtiger_sobillads','vtiger_soshipads', 
				'vtiger_inventoryproductrelSalesOrder', 'vtiger_contactdetailsSalesOrder', 'vtiger_accountSalesOrder',
				'vtiger_invoice_recurring_info','vtiger_quotesSalesOrder'));
		
		if (!$queryPlanner->requireTable('vtiger_salesorder', $matrix)) {
			return '';
		}
		
		$query = $this->getRelationQuery($module,$secmodule,"vtiger_salesorder","salesorderid", $queryPlanner);
		if ($queryPlanner->requireTable("vtiger_crmentitySalesOrder",$matrix)){
			$query .= " left join vtiger_crmentity as vtiger_crmentitySalesOrder on vtiger_crmentitySalesOrder.crmid=vtiger_salesorder.salesorderid and vtiger_crmentitySalesOrder.deleted=0";
		}
		if ($queryPlanner->requireTable("vtiger_salesordercf")){
			$query .= " left join vtiger_salesordercf on vtiger_salesorder.salesorderid = vtiger_salesordercf.salesorderid";
		}
		if ($queryPlanner->requireTable("vtiger_sobillads")){
			$query .= " left join vtiger_sobillads on vtiger_salesorder.salesorderid=vtiger_sobillads.sobilladdressid";
		}
		if ($queryPlanner->requireTable("vtiger_soshipads")){
			$query .= " left join vtiger_soshipads on vtiger_salesorder.salesorderid=vtiger_soshipads.soshipaddressid";
		}
		if ($queryPlanner->requireTable("vtiger_currency_info$secmodule")){
			$query .= " left join vtiger_currency_info as vtiger_currency_info$secmodule on vtiger_currency_info$secmodule.id = vtiger_salesorder.currency_id";
		}
		if ($queryPlanner->requireTable("vtiger_inventoryproductrelSalesOrder", $matrix)){
			$query .= " left join vtiger_inventoryproductrel as vtiger_inventoryproductrelSalesOrder on vtiger_salesorder.salesorderid = vtiger_inventoryproductrelSalesOrder.id";
		}
		if ($queryPlanner->requireTable("vtiger_productsSalesOrder")){
			$query .= " left join vtiger_products as vtiger_productsSalesOrder on vtiger_productsSalesOrder.productid = vtiger_inventoryproductrelSalesOrder.productid";
		}
		if ($queryPlanner->requireTable("vtiger_serviceSalesOrder")){
			$query .= " left join vtiger_service as vtiger_serviceSalesOrder on vtiger_serviceSalesOrder.serviceid = vtiger_inventoryproductrelSalesOrder.productid";
		}
		if ($queryPlanner->requireTable("vtiger_groupsSalesOrder")){
			$query .= " left join vtiger_groups as vtiger_groupsSalesOrder on vtiger_groupsSalesOrder.groupid = vtiger_crmentitySalesOrder.smownerid";
		}
		if ($queryPlanner->requireTable("vtiger_usersSalesOrder")){
			$query .= " left join vtiger_users as vtiger_usersSalesOrder on vtiger_usersSalesOrder.id = vtiger_crmentitySalesOrder.smownerid";
		}
		if ($queryPlanner->requireTable("vtiger_potentialRelSalesOrder")){
			$query .= " left join vtiger_potential as vtiger_potentialRelSalesOrder on vtiger_potentialRelSalesOrder.potentialid = vtiger_salesorder.potentialid";
		}
		if ($queryPlanner->requireTable("vtiger_contactdetailsSalesOrder")){
			$query .= " left join vtiger_contactdetails as vtiger_contactdetailsSalesOrder on vtiger_salesorder.contactid = vtiger_contactdetailsSalesOrder.contactid";
		}
		if ($queryPlanner->requireTable("vtiger_invoice_recurring_info")){
			$query .= " left join vtiger_invoice_recurring_info on vtiger_salesorder.salesorderid = vtiger_invoice_recurring_info.salesorderid";
		}
		if ($queryPlanner->requireTable("vtiger_quotesSalesOrder")){
			$query .= " left join vtiger_quotes as vtiger_quotesSalesOrder on vtiger_salesorder.quoteid = vtiger_quotesSalesOrder.quoteid";
		}
		if ($queryPlanner->requireTable("vtiger_accountSalesOrder")){
			$query .= " left join vtiger_account as vtiger_accountSalesOrder on vtiger_accountSalesOrder.accountid = vtiger_salesorder.accountid";
		}
		if ($queryPlanner->requireTable("vtiger_lastModifiedBySalesOrder")){
			$query .= " left join vtiger_users as vtiger_lastModifiedBySalesOrder on vtiger_lastModifiedBySalesOrder.id = vtiger_crmentitySalesOrder.modifiedby ";
		}
		return $query;
	}

	/*
	 * 返回关联表字段
	 */
	function setRelationTables($secmodule){
		$rel_tables = array (
			"Calendar" =>array("vtiger_seactivityrel"=>array("crmid","activityid"),"vtiger_salesorder"=>"salesorderid"),
			"Invoice" =>array("vtiger_invoice"=>array("salesorderid","invoiceid"),"vtiger_salesorder"=>"salesorderid"),
			"Documents" => array("vtiger_senotesrel"=>array("crmid","notesid"),"vtiger_salesorder"=>"salesorderid"),
		);
		return $rel_tables[$secmodule];
	}

	// 关联删除
	function unlinkRelationship($id, $return_module, $return_id) {
		if(empty($return_module) || empty($return_id)) return;

		if($return_module == 'Accounts') {
			$this->trash('SalesOrder',$id);
		}
		elseif($return_module == 'Quotes') {
			$relation_query = 'UPDATE vtiger_salesorder SET quoteid=? WHERE salesorderid=?';
			$this->db->pquery($relation_query, array(null, $id));
		}
		elseif($return_module == 'Potentials') {
			$relation_query = 'UPDATE vtiger_salesorder SET potentialid=? WHERE salesorderid=?';
			$this->db->pquery($relation_query, array(null, $id));
		}
		elseif($return_module == 'Contacts') {
			$relation_query = 'UPDATE vtiger_salesorder SET contactid=? WHERE salesorderid=?';
			$this->db->pquery($relation_query, array(null, $id));
		} else {
			$sql = 'DELETE FROM vtiger_crmentityrel WHERE (crmid=? AND relmodule=? AND relcrmid=?) OR (relcrmid=? AND module=? AND crmid=?)';
			$params = array($id, $return_module, $return_id, $id, $return_module, $return_id);
			$this->db->pquery($sql, $params);
		}
	}

	public function getJoinClause($tableName) {
		if ($tableName == 'vtiger_invoice_recurring_info') {
			return 'LEFT JOIN';
		}
		return parent::getJoinClause($tableName);
	}

	function insertIntoEntityTable($table_name, $module, $fileid = '')  {
		//Ignore relation table insertions while saving of the record
		if($table_name == 'vtiger_inventoryproductrel') {
			return;
		}
		parent::insertIntoEntityTable($table_name, $module, $fileid);
	}

	/*Function to create records in current module.
	**This function called while importing records to this module*/
	function createRecords($obj) {
		$createRecords = createRecords($obj);
		return $createRecords;
	}
    function mark_deleted($id){
        parent::mark_deleted($id);
        //伪删除工单产品更更为4
        $this->db->pquery("UPDATE vtiger_salesorderproductsrel SET vtiger_salesorderproductsrel.multistatus=4 WHERE salesorderid=?",array($id));
    }


	/*Function returns the record information which means whether the record is imported or not
	**This function called while importing records to this module*/
	function importRecord($obj, $inventoryFieldData, $lineItemDetails) {
		$entityInfo = importRecord($obj, $inventoryFieldData, $lineItemDetails);
		return $entityInfo;
	}

	/*Function to return the status count of imported records in current module.
	**This function called while importing records to this module*/
	function getImportStatusCount($obj) {
		$statusCount = getImportStatusCount($obj);
		return $statusCount;
	}

	function undoLastImport($obj, $user) {
		$undoLastImport = undoLastImport($obj, $user);
	}

	/**
	* 返回导出语句
	*/
	function create_export_query($where){
		global $current_user;
		include("include/utils/ExportUtils.php");
		//获取可见的字段
		$sql = getPermittedFieldsQuery("SalesOrder", "detail_view");
		$fields_list = getFieldsListFromQuery($sql);
		$fields_list .= getInventoryFieldsForExport($this->table_name);
		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');

		$query = "SELECT $fields_list FROM ".$this->entity_table."
				INNER JOIN vtiger_salesorder ON vtiger_salesorder.salesorderid = vtiger_crmentity.crmid
				LEFT JOIN vtiger_salesordercf ON vtiger_salesordercf.salesorderid = vtiger_salesorder.salesorderid
				LEFT JOIN vtiger_sobillads ON vtiger_sobillads.sobilladdressid = vtiger_salesorder.salesorderid
				LEFT JOIN vtiger_soshipads ON vtiger_soshipads.soshipaddressid = vtiger_salesorder.salesorderid
				LEFT JOIN vtiger_inventoryproductrel ON vtiger_inventoryproductrel.id = vtiger_salesorder.salesorderid
				LEFT JOIN vtiger_products ON vtiger_products.productid = vtiger_inventoryproductrel.productid
				LEFT JOIN vtiger_service ON vtiger_service.serviceid = vtiger_inventoryproductrel.productid
				LEFT JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_salesorder.contactid
				LEFT JOIN vtiger_invoice_recurring_info ON vtiger_invoice_recurring_info.salesorderid = vtiger_salesorder.salesorderid
				LEFT JOIN vtiger_potential ON vtiger_potential.potentialid = vtiger_salesorder.potentialid
				LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_salesorder.accountid
				LEFT JOIN vtiger_currency_info ON vtiger_currency_info.id = vtiger_salesorder.currency_id
				LEFT JOIN vtiger_quotes ON vtiger_quotes.quoteid = vtiger_salesorder.quoteid
				LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
				LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid";

		$query .= $this->getNonAdminAccessControlQuery('SalesOrder',$current_user);
		$where_auto = " vtiger_crmentity.deleted=0";

		if($where != "") {
			$query .= " where ($where) AND ".$where_auto;
		} else {
			$query .= " where ".$where_auto;
		}
		return $query;
	}

    /**节点审核时到了指定节点抓取时间
     * 后置事件
     * @param Vtiger_Request $request
     */
    function workflowcheckafter(Vtiger_Request $request){
        $stagerecordid=$request->get('stagerecordid');
        $record=$request->get('record');
        $db=PearDatabase::getInstance();
        $recordModel = Vtiger_Record_Model::getInstanceById($record, 'SalesOrder',true);
        $entity=$recordModel->entity->column_fields;
        if($entity['modulestatus']=='c_complete'){
            $query="UPDATE vtiger_salesorder SET iseditproductlist=1 WHERE vtiger_salesorder.salesorderid=?";
            $db->pquery($query,array($record));
            // cxh 2019-08-02 添加 如果该审核需要修改审核列表中的modulestatus（审核流程状态）审核完后走下面代码
            $query="SELECT
                    vtiger_workflowstages.workflowstagesflag,
        		vtiger_salesorderworkflowstages.workflowsid
                FROM
                    `vtiger_salesorderworkflowstages`
                LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid
                WHERE
                    vtiger_salesorderworkflowstages.salesorderworkflowstagesid = ?
                AND vtiger_salesorderworkflowstages.modulename = 'SalesOrder'";
            $result=$db->pquery($query,array($stagerecordid));
            $workflowsid=$db->query_result($result,0,'workflowsid');
            $params['workflowsid']=$workflowsid;
            $params['salesorderid']=$request->get('record');
            //file_put_contents("files.txt",json_encode($params));
            $this->hasAllAuditorsChecked($params);
            return '';
        }
        $query="SELECT
                    vtiger_workflowstages.workflowstagesflag,
                    vtiger_salesorderworkflowstages.workflowsid
                FROM
                    `vtiger_salesorderworkflowstages`
                LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid
                WHERE
                    vtiger_salesorderworkflowstages.salesorderworkflowstagesid = ?
                AND vtiger_salesorderworkflowstages.modulename = 'SalesOrder'";
        $result=$db->pquery($query,array($stagerecordid));
        $currentflag=$db->query_result($result,0,'workflowstagesflag');
        $workflowsid=$db->query_result($result,0,'workflowsid');
        switch($currentflag){
            case 'CHECK_TIME':
                global $current_user;
                $datetime=date("Y-m-d H:i:s");
                $query="INSERT INTO vtiger_performanceoftime(salesorderid,performanceoftime,smowerid) VALUES(?,?,?)";
                $db->pquery($query,array($record,$datetime,$current_user->id));
                break;
            //更新抓取的时间为工单的确定计算时间SERVICE_DESIGNATION
            case 'CONFIRM_TIME':
                //$query="UPDATE vtiger_salesorder SET performanceoftime=(SELECT performanceoftime FROM vtiger_performanceoftime WHERE vtiger_performanceoftime.salesorderid=vtiger_salesorder.salesorderid ORDER BY performanceoftimeid DESC LIMIT 1),iseditproductlist=1 WHERE vtiger_salesorder.salesorderid=?";
                $query="UPDATE vtiger_salesorder SET iseditproductlist=1 WHERE vtiger_salesorder.salesorderid=?";
                $db->pquery($query,array($record));
                break;
                
            case 'GOOGLE_CONF_TIME':
                //$query="UPDATE vtiger_salesorder SET performanceoftime=(SELECT performanceoftime FROM vtiger_performanceoftime WHERE vtiger_performanceoftime.salesorderid=vtiger_salesorder.salesorderid ORDER BY performanceoftimeid DESC LIMIT 1),iseditproductlist=1 WHERE vtiger_salesorder.salesorderid=?";
                $query="UPDATE vtiger_salesorder SET googlestatus=1 WHERE vtiger_salesorder.salesorderid=?";
                $db->pquery($query,array($record));
                break;
            //根据主题来确定客户客服的审核
            case 'SERVICE_DESIGNATION':
                $subject=$entity['subject'];
                $subject=explode('#',$subject);
                $subject=trim($subject[0]);
                /*$query="UPDATE vtiger_salesorderworkflowstages SET ishigher=1,higherid=(SELECT (vtiger_servicecomments.serviceid) as serviceid FROM vtiger_account LEFT JOIN vtiger_crmentity ON vtiger_account.accountid = vtiger_crmentity.crmid LEFT JOIN vtiger_servicecomments ON (vtiger_account.accountid = vtiger_servicecomments.related_to and vtiger_servicecomments.assigntype = 'accountby') WHERE 1=1 and vtiger_crmentity.deleted=0 AND vtiger_account.accountname=? LIMIT 1) WHERE vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.sequence=(SELECT * FROM (SELECT ktemp.sequence+1 FROM `vtiger_salesorderworkflowstages` ktemp WHERE ktemp.salesorderworkflowstagesid =? AND ktemp.modulename = 'SalesOrder' LIMIT 1) kktemp)";
				$db->pquery($query,array($subject,$record,$stagerecordid));*/
				$query="UPDATE vtiger_salesorderworkflowstages SET ishigher=1,higherid=(SELECT vtiger_account.serviceid FROM vtiger_account LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_account.accountid WHERE vtiger_crmentity.deleted=0 AND vtiger_account.accountname=? LIMIT 1) WHERE salesorderid=? AND workflowstagesflag='MODIFY_SERVICEID'";
				$db->pquery($query,array($subject,$record));
                break;
            case 'ACCOUNTING_TIME':
                //业绩的确认时间
            	$query="SELECT 1 FROM vtiger_salesorderworkflowstages WHERE salesorderid=? AND isaction in(0,1) AND modulename='SalesOrder' AND sequence=1";
                $dataResult=$db->pquery($query,array($record));
                if(!$db->num_rows($dataResult)){
                	// cxh 2020-05-09 修改 start   判断如果有工单负责人关联回款 节点则 先不生成 performanceoftime日期
                    $query="SELECT 1 FROM vtiger_salesorderworkflowstages WHERE salesorderid=? AND isaction in(0,1) AND modulename='SalesOrder' AND workflowstagesflag='RAYMENT_MATCH'";
                    $dataResult=$db->pquery($query,array($record));
                    if(!$db->num_rows($dataResult)){
                        $dateime=date('Y-m-d');
                        $query="UPDATE vtiger_salesorder SET performanceoftime=IFNULL(performanceoftime,'{$dateime}') WHERE salesorderid=?";
                        $db->pquery($query,array($record));
					}
                    // cxh end
				}
                $servicecontractsid=$entity['servicecontractsid'];
                if(!ServiceContracts_Record_Model::createIsWorkflows('',$servicecontractsid)){
                    //回款+担保金是否大于成本
                    if(!ServiceContracts_Record_Model::receiveDayprice($servicecontractsid,$record,false)){
                    	$db->pquery("UPDATE vtiger_salesorder SET modulestatus='c_lackpayment' WHERE vtiger_salesorder.salesorderid=?",array($record));
                        $db->pquery("UPDATE vtiger_salesorderworkflowstages SET isaction=0 WHERE salesorderid=? AND isaction=1",array($record));
                    }

                }
                break;
                // cxh 2020-05-09 start 
			case 'RAYMENT_MATCH':
                $dateime=date('Y-m-d');
                $query="UPDATE vtiger_salesorder SET srayment=1,performanceoftime=IFNULL(performanceoftime,'{$dateime}') WHERE salesorderid=?";
                $db->pquery($query,array($record));
                //并且在3号工单审核至“工单负责人关联回款审核”节点且工单流程状态完成，即可生成成本，核算业绩提成,否则不予计入当月业绩提成
                global $log;
                $log->info('salesorderid:'.$record.' “工单负责人关联回款审核”节点,进行业绩计算');
                //查询工单对应的回款
                $sql = 'select rp.receivedpaymentsid,IFNULL(ss.purchasecost,0.00) AS purchasecost from vtiger_receivedpayments as rp left join vtiger_salesorderrayment as ss on rp.receivedpaymentsid = ss.receivedpaymentsid left join vtiger_salesorder as so on so.salesorderid=ss.salesorderid where so.salesorderid=?';
                $payData = $db->pquery($sql, array($record));
                while ($row = $db->fetch_row($payData)){
                	$log->info('salesorderid:'.$record.' 回款参数： '.$row['receivedpaymentsid'].' '.$row['purchasecost']);
                	$recordModel->calcSalesorderAchievement(array(
		                'receivedpaymentsid'=>$row['receivedpaymentsid'],
		                'salesorderid'=>$record,
		                'purchasecost'=>$row['purchasecost'],
		            ));
                }
                break;
                // cxh end
            default :
                break;
        }
        // cxh 2019-08-02 添加 如果该审核需要修改审核列表中的modulestatus（审核流程状态）审核完后走下面代码
        $params['workflowsid']=$workflowsid;
        $params['salesorderid']=$request->get('record');
        $this->hasAllAuditorsChecked($params);
    }
	
	function retrieve_entity_info($record, $module){
		parent::retrieve_entity_info($record, $module);
		if(!empty($_REQUEST['realoperate'])){
			$realoperate=setoperate($record,$module);
			if($realoperate==$_REQUEST['realoperate']){
				return true;
			}
		}
		global $currentView;
		$where=getAccessibleUsers('','',true);
		if($where!='1=1'){
			if(!in_array($this->column_fields['assigned_user_id'],$where)){
				if($currentView=='Edit' || $currentView=='Detail'|| $currentView=='SaveAjax' ){
					//throw new AppException('你没有操作权限！');
					//exit;
				}
			}
		}
	}

    /**
	 * 工单打回的后置事件处理机制
	 * 此处打回后做邮件提醒
     * @param Vtiger_Request $request
     */
	public function backallAfter(Vtiger_Request $request){
		$recordid=$request->get('record');
        $stagerecordid=$request->get('isrejectid');
        $recordModel = Vtiger_Record_Model::getInstanceById($recordid, 'SalesOrder');
        $entity=$recordModel->entity->column_fields;
        $user = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile($entity['assigned_user_id']);
        $Subject='工单打回处理';
        $body='您有一张工单被打回,<br>工单单号为:'.$entity['salesorder_no'];
        $address=array(array('mail'=>$current_user->column_fields['email1'],'name'=>$current_user->column_fields['last_name']));
        Vtiger_Record_Model::sendMail($Subject,$body,$address);
        $query="SELECT
                    vtiger_workflowstages.workflowstagesflag,
                    vtiger_workflowstages.workflowsid
                FROM
                    `vtiger_salesorderworkflowstages`
                LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid
                WHERE
                    vtiger_salesorderworkflowstages.salesorderworkflowstagesid = ?
                AND vtiger_salesorderworkflowstages.modulename = 'SalesOrder'";
        $result=$this->db->pquery($query,array($stagerecordid));
        //打回后清掉业绩核算时间
        $query="UPDATE vtiger_salesorder SET performanceoftime=NULL WHERE salesorderid=?";
        $this->db->pquery($query,array($recordid));

        $workflowsid=$this->db->query_result($result,0,'workflowsid');
        $this->db->pquery("DELETE FROM `vtiger_salesorderworkflowstages` WHERE vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename='SalesOrder' AND vtiger_salesorderworkflowstages.workflowsid=?",array($recordid,$workflowsid));

		//回款处理
		$sql='SELECT * FROM vtiger_salesorderrayment WHERE deleted=0 AND salesorderid=?';
		$result=$this->db->pquery($sql,array($recordid));
		if($this->db->num_rows($result)){
			while($row=$this->db->fetch_array($result)){
				$purchasecost=$row['purchasecost'];
				$receivedpaymentsid=$row['receivedpaymentsid'];
				$this->db->pquery("UPDATE `vtiger_receivedpayments` SET occupationcost=if(occupationcost<=0,0,(occupationcost-{$purchasecost})),rechargeableamount=if((rechargeableamount+{$purchasecost})>0,(rechargeableamount+{$purchasecost}),0) WHERE receivedpaymentsid=?",array($receivedpaymentsid));

			}
			$sql='UPDATE vtiger_salesorderrayment SET deleted=1 WHERE salesorderid=?';
			$this->db->pquery($sql,array($recordid));

			$query='SELECT receivedpaymentownid,receivedpaymentsid,achievementmonth,achievementtype FROM vtiger_achievementallot_statistic WHERE salesorderid=?';
        	$result=$this->db->pquery($query,array($recordid));
			if($this->db->num_rows($result)){
				$array=array();
				while($row=$this->db->fetch_array($result)){
					$array[]=$row;
				}
				$sql='DELETE FROM vtiger_achievementallot_statistic WHERE salesorderid=?';
				$this->db->pquery($sql,array($recordid));
				foreach($array as $value){
					$sql="UPDATE vtiger_achievementsummary SET 
                        vtiger_achievementsummary.unit_price =(SELECT sum(vtiger_achievementallot_statistic.unit_price) FROM vtiger_achievementallot_statistic WHERE vtiger_achievementallot_statistic.achievementtype = vtiger_achievementsummary.achievementtype AND vtiger_achievementallot_statistic.achievementmonth =vtiger_achievementsummary.achievementmonth AND vtiger_achievementallot_statistic.receivedpaymentownid =vtiger_achievementsummary.userid)
                        ,vtiger_achievementsummary.effectiverefund=(SELECT sum(vtiger_achievementallot_statistic.effectiverefund) FROM vtiger_achievementallot_statistic WHERE vtiger_achievementallot_statistic.achievementtype = vtiger_achievementsummary.achievementtype AND vtiger_achievementallot_statistic.achievementmonth =vtiger_achievementsummary.achievementmonth AND vtiger_achievementallot_statistic.receivedpaymentownid =vtiger_achievementsummary.userid)
                        ,vtiger_achievementsummary.realarriveachievement=(SELECT sum(vtiger_achievementallot_statistic.arriveachievement)FROM vtiger_achievementallot_statistic WHERE vtiger_achievementallot_statistic.achievementtype = vtiger_achievementsummary.achievementtype AND vtiger_achievementallot_statistic.achievementmonth =vtiger_achievementsummary.achievementmonth AND vtiger_achievementallot_statistic.receivedpaymentownid =vtiger_achievementsummary.userid)
                        WHERE vtiger_achievementsummary.achievementmonth=? AND vtiger_achievementsummary.achievementtype=? and userid=?";
					$this->db->pquery($sql,array($value['achievementmonth'],$value['achievementtype'],$value['receivedpaymentownid']));
				}
				$sql='DELETE FROM vtiger_oldachievement_hasdeduction WHERE salesorderid=?';
				$this->db->pquery($sql,array($recordid));
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
		parent::makeWorkflows($modulename,$workflowsid,$salesorderid,$isedit='');
        $query=" UPDATE vtiger_salesorderworkflowstages,
				 vtiger_salesorder
				 SET vtiger_salesorderworkflowstages.accountid=vtiger_salesorder.accountid,vtiger_salesorderworkflowstages.salesorder_nono=vtiger_salesorder.salesorder_no,
				 vtiger_salesorderworkflowstages.modulestatus='p_process',
				 vtiger_salesorderworkflowstages.accountname=(SELECT vtiger_account.accountname FROM vtiger_account WHERE vtiger_account.accountid=vtiger_salesorder.accountid)
				 WHERE vtiger_salesorder.salesorderid=vtiger_salesorderworkflowstages.salesorderid
				 AND vtiger_salesorderworkflowstages.salesorderid=? AND  vtiger_salesorderworkflowstages.workflowsid=?";
        $this->db->pquery($query,array($salesorderid,$workflowsid));
        //新建时 消息提醒第一审核人进行审核     这个地方被注释掉 应为分别在 两个文件申城工作流后处理了。 一个在record-》contractsMakeWorkflows  一个在save的里边saveRecord方法里  处理了工作流
       /* $object = new SalesorderWorkflowStages_SaveAjax_Action();
        //file_put_contents('files.txt',$salesorderid);
        $object->sendWxRemind(array('salesorderid'=>$salesorderid,'salesorderworkflowstagesid'=>0));*/
	}
    /**
     * 工作流打回处理前置事件
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

        $currentflag=trim($currentflag);
        $query='SELECT 1 FROM vtiger_achievementallot_statistic WHERE (`status`=1 OR isover=1) AND salesorderid=?';
		$result=$this->db->pquery($query,array($recordid));
        if($this->db->num_rows($result)){
			$resultaa['success'] = 'false';
			$resultaa['error']['message'] = ":该工单已核算业绩,若要打回请先确认业绩!";
			echo json_encode($resultaa);
			exit;
		}
        switch($currentflag){
            case 'DO_REFUND':
                break;
            default:

        }
        $query="SELECT 1 FROM vtiger_refillapplication LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_refillapplication.refillapplicationid WHERE vtiger_crmentity.deleted=0 AND vtiger_refillapplication.salesorderid>0 AND vtiger_refillapplication.salesorderid=? AND vtiger_refillapplication.modulestatus!='c_cancel'";
    	$result=$this->db->pquery($query,array($recordid));
    	if($this->db->num_rows($result)){
            $resultaa['success'] = 'false';
            $resultaa['error']['message'] = ":该工单已提交充值申请单不允打回,若要打回请你作废充值申请单!";
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

?>
