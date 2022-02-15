<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
class ServiceAssignRule_SaveAjax_Action extends Vtiger_Save_Action {
	public function process(Vtiger_Request $request) {
		$db = PearDatabase::getInstance();
		$id = $request->get('record');
		
		$sqlQuery="select assigntype,related_to,serviceid,productid,departmentid,REPLACE(ownerid,' |##| ',',') as ownerids from vtiger_serviceassignrule where serviceassignruleid=?";
		$assignrule_result = $db->pquery($sqlQuery, array($id));
		if (empty($assignrule_result)){
			return;
		}
		//分配类型
		$assigntype=$db->query_result($assignrule_result,0,'assigntype');
		//客服id
		$serviceid=$db->query_result($assignrule_result,0,'serviceid');
		
		//获取登录用户信息
		$currentUser = Users_Record_Model::getCurrentUserModel();
		$userid = $currentUser->get('id');
	
		if ($assigntype =='productby'){
			//按产品分配
			$productid=$db->query_result($assignrule_result,0,'productid');
			$sqlQuery="select
					vtiger_salesorderproductsrel.salesorderproductsrelid,
					vtiger_salesorderproductsrel.accountid 
					from vtiger_salesorderproductsrel where vtiger_salesorderproductsrel.productid=?";
			$result = $db->pquery($sqlQuery, array($productid));
			if (empty($result) || $db->num_rows($result)==0){
				$response = new Vtiger_Response();
				$response->setResult(array(1));
				$response->emit();
				return;
			}
			$num_rows = $db->num_rows($result);
			for($i=0; $i<$num_rows; $i++) {
				$arr_servicecomments=array();
				$arr_servicecomments['assigntype']='productby';
				$arr_servicecomments['salesorderproductsrelid']=$db->query_result($result,$i,'salesorderproductsrelid');
				$arr_servicecomments['related_to']=$db->query_result($result,$i,'accountid');
				$arr_servicecomments['serviceid']=$serviceid;
				$arr_servicecomments['assignerid']=$userid;
				ServiceComments_Record_Model::insertServiceComments($arr_servicecomments);
			}
		}else{
			//按客户分配
			$departmentid=$db->query_result($assignrule_result,0,'departmentid');
			$strOwnerid=$db->query_result($assignrule_result,0,'ownerids');
			$accountid=$db->query_result($assignrule_result,0,'related_to');
			
			//获取客户信息
			$sqlQuery="select vtiger_account.accountid,vtiger_account.accountname,vtiger_crmentity.smownerid,
					    (select last_name from vtiger_users where id=vtiger_crmentity.smownerid) as smownername,
					    vtiger_account.accountrank,
					    IFNULL((select last_name from vtiger_users where id=(select vtiger_servicecomments.serviceid from vtiger_servicecomments where vtiger_servicecomments.related_to=cast(vtiger_account.accountid as char) LIMIT 0,1)),'') as servicename
					    from vtiger_account INNER JOIN vtiger_crmentity on(vtiger_crmentity.crmid=vtiger_account.accountid and vtiger_crmentity.deleted=0)
						where EXISTS (select vtiger_user2department.departmentid from vtiger_user2department where vtiger_user2department.userid=vtiger_crmentity.smownerid and vtiger_user2department.departmentid=?)
					    and vtiger_account.accountrank in('gold_isv','silv_isv','bras_isv')";//只获取金牌，银牌，铜牌客户
			
			$parrm_array[]=$departmentid;
			//客户
			if (!empty($accountid)){
				$sqlQuery.=" and vtiger_account.accountid=?";
				$parrm_array[]=$accountid;
			}
			//负责人
			if (!empty($strOwnerid)){
				$sqlQuery.=" and EXISTS (select vtiger_crmentity.smownerid from vtiger_crmentity where vtiger_crmentity.crmid=vtiger_account.accountid and FIND_IN_SET(vtiger_crmentity.smownerid,?)>0 and vtiger_crmentity.deleted=0)";
				$parrm_array[]=$strOwnerid;
			}
			
			$result = $db->pquery($sqlQuery, $parrm_array);
		    if (empty($result) || $db->num_rows($result)==0){
				$response = new Vtiger_Response();
				$response->setResult(array(1));
				$response->emit();
				return;
			}
			$num_rows = $db->num_rows($result);
			for($i=0; $i<$num_rows; $i++) {
				$arr_servicecomments=array();
				$arr_servicecomments['assigntype']='accountby';
				$arr_servicecomments['salesorderproductsrelid']=null;
				$arr_servicecomments['related_to']=$db->query_result($result,$i,'accountid');
				$arr_servicecomments['serviceid']=$serviceid;
				$arr_servicecomments['assignerid']=$userid;
				
				ServiceComments_Record_Model::insertServiceComments($arr_servicecomments);
			}
		}
		
		$result = array(0);
		$response = new Vtiger_Response();
		$response->setResult($result);
		$response->emit();
	    return;
	}
}
