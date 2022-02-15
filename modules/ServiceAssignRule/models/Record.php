<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * Vtiger Entity Record Model Class
 */
class ServiceAssignRule_Record_Model extends Vtiger_Record_Model {
	/**
	 * Function to save the current Record Model
	 */
	public function save() {
		//重复设置判断
		
		//同体系判断
		
		$this->getModule()->saveRecord($this);
	}
	
	/**
	 * 获取已成交客户数据
	 * @return 已成交客户数据
	 */
	public static function getAccountsListValues() {
// 		$db = PearDatabase::getInstance();
// 		$listQuery="select accountid,accountname from vtiger_account where not isnull(consumelevel)";
// 		$values = array();
// 		$result = $db->pquery($listQuery, array());
// 		$num_rows = $db->num_rows($result);
// 		for($i=0; $i<$num_rows; $i++) {
// 			$accountid=$db->query_result($result,$i,'accountid');
// 			$values[$accountid] = $db->query_result($result,$i,'accountname');
// 		}
// 		return $values;
	}
	
	/**
	 * 获取产品数据
	 * @return 产品数据
	 */
	public static function getProductListValues() {
		$db = PearDatabase::getInstance();
		$listQuery="select productid,productname from vtiger_products where EXISTS(select crmid from vtiger_crmentity where vtiger_crmentity.crmid=vtiger_products.productid and vtiger_crmentity.deleted=0)";
		$values = array();
		$result = $db->pquery($listQuery, array());
		$num_rows = $db->num_rows($result);
		for($i=0; $i<$num_rows; $i++) {
			$productid=$db->query_result($result,$i,'productid');
			$values[$productid] = $db->query_result($result,$i,'productname');
		}
		return $values;
	}
	
	/**
	 * 获取客户名称
	 * @return 客户名称
	 */
	public static function getAccountsName($accountid) {
// 		$db = PearDatabase::getInstance();
// 		$sqlQuery="select accountname from vtiger_account where accountid=?";
// 		$values = array();
// 		$result = $db->pquery($sqlQuery, array($accountid));
// 		return $db->query_result($result,0,'accountname');
	}
	
	/**
	 * 获取部门名称
	 * @return 部门名称
	 */
	public static function getDepartmentName($departmentid) {
// 		$db = PearDatabase::getInstance();
// 		$sqlQuery="select departmentname from vtiger_departments where departmentid=?";
// 		$values = array();
// 		$result = $db->pquery($sqlQuery, array($departmentid));
// 		return $db->query_result($result,0,'departmentname');
	}
	
	/**
	 * 获取产品名称
	 * @return 产品名称
	 */
	public static function getProductName($productid) {
		$db = PearDatabase::getInstance();
		$sqlQuery="select productname from vtiger_products where productid=?";
		$values = array();
		$result = $db->pquery($sqlQuery, array($productid));
		return $db->query_result($result,0,'productname');
	}
	
	/**
	 * 获取分配类型
	 * @return 分配类型
	 */
	public static function getAssignType($id) {
		if (empty($id)){
			return 'LBL_PRODUCT_ASSIGN';
		}
		$db = PearDatabase::getInstance();
		$sqlQuery="select assigntype from vtiger_serviceassignrule where serviceassignruleid=?";
		$values = array();
		$result = $db->pquery($sqlQuery, array($id));
		if (empty($result)){
			return 'LBL_PRODUCT_ASSIGN';
		}
		
		$assigntype=$db->query_result($result,0,'assigntype');
		if ($assigntype == 'accountby'){
			return 'LBL_PRODUCT_ASSIGN';
		}else{
			return 'LBL_ACCOUNT_ASSIGN';
		}
	}
	
	/**
	 * 返回用户根据部门分组数据 2015-2-12 gaocl
	 * @param string $departmentid
	 * @return Ambigous <multitype:, multitype:Ambigous <> >
	 */
	public static function  get_user_department_array_bydepartmentid($departmentid,$status="Active")
	{
		$db = PearDatabase::getInstance();
		$temp_result = Array();
		$query = "SELECT vtiger_users.id,vtiger_users.last_name,vtiger_user2department.departmentid,vtiger_departments.departmentname from vtiger_users
		left join vtiger_user2department on vtiger_user2department.userid=vtiger_users.id
		left join vtiger_departments on vtiger_departments.departmentid=vtiger_user2department.departmentid
		WHERE vtiger_users.status=? and vtiger_departments.departmentid=?";
		$result = $db->pquery($query, array($status,$departmentid));
	
		$num_rows = $db->num_rows($result);
		for($i=0; $i<$num_rows; $i++) {
			$userid=$db->query_result($result,$i,'id');
			$username=$db->query_result($result,$i,'last_name');
			$user_array[$userid]=$username;
		}
		return $user_array;
	}

	/**
	 * 获取客户信息
	 * @param Vtiger_Request $request
	 */
	public static function getAccountInfos(Vtiger_Request $request) {
		$assigntype = $request->get('assigntype');
		$departmentid = $request->get('departmentid');
		$productid = $request->get('productid');
        $accountrank = $request->get('accountrank'); //客户等级 adatian 20150703
		
		$ownerid = $request->get('ownerid');
		$oldserviceid = $request->get('oldserviceid');
		$accountid = $request->get('accountid');

		$ownerid=implode(',', $ownerid);

		$serviceid=$request->get('serviceid');
		//不包含已分配客服的客户
		$notAssignCheckBox=$request->get('notAssignCheckBox');
		
		$db = PearDatabase::getInstance();
		$user_array = Array();
		$parrm_array = Array();
		//获取客户信息

	$sqlQuery="select vtiger_account.accountid,vtiger_account.accountname,vtiger_crmentity.smownerid,
			        (select departmentname from vtiger_departments where vtiger_departments.departmentid=(select vtiger_user2department.departmentid from vtiger_user2department where vtiger_user2department.userid=vtiger_crmentity.smownerid LIMIT 0,1)) as departmentname,
				    (select last_name from vtiger_users where id=vtiger_crmentity.smownerid) as smownername,
				    vtiger_account.accountrank,
				    IFNULL((select last_name from vtiger_users where id=(select vtiger_servicecomments.serviceid from vtiger_servicecomments where vtiger_servicecomments.related_to=cast(vtiger_account.accountid as char) LIMIT 0,1)),'') as servicename
				    from vtiger_crmentity INNER JOIN vtiger_account on(vtiger_crmentity.crmid=vtiger_account.accountid and vtiger_crmentity.deleted=0)
					where vtiger_account.accountrank in('gold_isv','silv_isv','bras_isv','iron_isv','visp_isv')";//只获取金牌，银牌，铜牌客户  //2015-4-17 young 加入铁牌
	
		//分配类型
		if ($assigntype == 'productby'){
			$sqlQuery.=" and EXISTS(select vtiger_salesorderproductsrel.accountid from vtiger_salesorderproductsrel where vtiger_salesorderproductsrel.accountid=vtiger_account.accountid and vtiger_salesorderproductsrel.productid=? LIMIT 0,1)";
			$parrm_array[]=$productid;
		}
        //部门
        if($departmentid!='H1'&& empty($ownerid)){
            // steel 2015-05-26 修改显示该部门下所有的客户
            $userid=getDepartmentUser($departmentid);
            $sqlQuery.=' and vtiger_crmentity.smownerid in ('.implode(',',$userid).')';
            //$parrm_array[]=$departmentid;
            //$sqlQuery.=' and EXISTS (select vtiger_user2department.departmentid from vtiger_user2department where vtiger_user2department.userid=vtiger_crmentity.smownerid and vtiger_user2department.departmentid=?)';
        }
        //客户等级 adatian 20150703
        if(!empty($accountrank)){
            $sqlQuery.=" and vtiger_account.accountrank=?";
            $parrm_array[]=$accountrank;
        }
		//客户
		if (!empty($accountid)){
			$sqlQuery.=" and vtiger_account.accountid=?";
			$parrm_array[]=$accountid;
		}
		//负责人
		if (!empty($ownerid)){
			$sqlQuery.=" and EXISTS (select vtiger_crmentity.smownerid from vtiger_crmentity where vtiger_crmentity.crmid=vtiger_account.accountid and FIND_IN_SET(vtiger_crmentity.smownerid,?)>0 and vtiger_crmentity.deleted=0)";
			$parrm_array[]=$ownerid;
		}
		//原客服
		if (!empty($oldserviceid)){
			$sqlQuery.=" and  EXISTS(select cast(vtiger_servicecomments.related_to as SIGNED) from vtiger_servicecomments where vtiger_servicecomments.related_to= cast(vtiger_account.accountid as char) and vtiger_servicecomments.serviceid=?)";
			$parrm_array[]=$oldserviceid;
		}
		//不包含已分配客户客服
		if ($notAssignCheckBox =='1'){
			$sqlQuery.=" and not EXISTS(select vtiger_servicecomments.related_to from vtiger_servicecomments where vtiger_servicecomments.related_to=cast(vtiger_account.accountid as char))";
		}
		$result = $db->pquery($sqlQuery, $parrm_array);
		if (empty($result) || $db->num_rows($result)<1){
			return $user_array;
		}
		$num_rows = $db->num_rows($result);
		for($i=0; $i<$num_rows; $i++) {
			$user_array[$i]['accountid']=$db->query_result($result,$i,'accountid');
			$user_array[$i]['accountname']=$db->query_result($result,$i,'accountname');
			$user_array[$i]['smownerid']=$db->query_result($result,$i,'smownerid');
			$user_array[$i]['departmentname']=$db->query_result($result,$i,'departmentname');
			$user_array[$i]['smownername']=$db->query_result($result,$i,'smownername');
			$user_array[$i]['accountrank']=vtranslate($db->query_result($result,$i,'accountrank'));
			$user_array[$i]['servicename']=vtranslate($db->query_result($result,$i,'servicename'));
		}
		
		return $user_array;
	}
	/**
	 * 获取客服分配客服信息
	 * @param $serviceid 客服id
	 */
	public static function getServiceAssignInfos($serviceid) {
		$db = PearDatabase::getInstance();
		$assignresult="";
		//获取可分配的客户数量
		$can_accountcount="100";
		//获取已分配客户数量
		$assignaccountcount="0";
        $gold_isv ='0';
        $silv_isv = '0';
        $bras_isv = '0';
        $iron_isv = '0';
		//获取客服名称
		$servicename="";
		
		/*$query = "select
					T.can_accountcount,
					T.servicename,
					count(1) as assignaccountcount
					from (
						select 
							IFNULL((select accountcount from vtiger_serviceassignset where serviceid=vtiger_servicecomments.serviceid LIMIT 0,1),
							(select accountcount from vtiger_serviceassignset_default order by accountcount desc LIMIT 0,1)) as can_accountcount,
							(select last_name from vtiger_users where vtiger_users.id=vtiger_servicecomments.serviceid) as servicename
						from vtiger_servicecomments where serviceid=?) T GROUP BY T.can_accountcount,T.servicename";*/
        $query ="SELECT T.can_accountcount, count(gold_isv) gold_isv, count(silv_isv) silv_isv, count(bras_isv) bras_isv, count(iron_isv) iron_isv, T.servicename, count(1) AS assignaccountcounta, ( count(gold_isv) + count(silv_isv) + count(bras_isv)) AS assignaccountcount FROM ( SELECT IFNULL(( SELECT accountcount FROM vtiger_serviceassignset WHERE serviceid = vtiger_servicecomments.serviceid LIMIT 0, 1 ), ( SELECT accountcount FROM vtiger_serviceassignset_default ORDER BY accountcount DESC LIMIT 0, 1 )) AS can_accountcount, ( SELECT count(accountid) AS c FROM vtiger_account WHERE vtiger_account.accountid = vtiger_servicecomments.related_to AND vtiger_account.accountrank = 'gold_isv' GROUP BY accountrank ) AS gold_isv, ( SELECT count(accountid) AS c FROM vtiger_account WHERE vtiger_account.accountid = vtiger_servicecomments.related_to AND vtiger_account.accountrank = 'silv_isv' GROUP BY accountrank ) AS silv_isv, ( SELECT count(accountid) AS c FROM vtiger_account WHERE vtiger_account.accountid = vtiger_servicecomments.related_to AND vtiger_account.accountrank = 'bras_isv' GROUP BY accountrank ) AS bras_isv, ( SELECT count(accountid) AS c FROM vtiger_account WHERE vtiger_account.accountid = vtiger_servicecomments.related_to AND vtiger_account.accountrank = 'iron_isv' GROUP BY accountrank ) AS iron_isv, ( SELECT last_name FROM vtiger_users WHERE vtiger_users.id = vtiger_servicecomments.serviceid ) AS servicename, ( SELECT accountrank FROM vtiger_account WHERE vtiger_account.accountid = vtiger_servicecomments.related_to ) FROM vtiger_servicecomments WHERE serviceid = ? ) T GROUP BY T.can_accountcount, T.servicename";
        $result = $db->pquery($query, array($serviceid));
		if ($result && $db->num_rows($result)>0){
			$can_accountcount=$db->query_result($result, 0,'can_accountcount');
			$assignaccountcount=$db->query_result($result, 0,'assignaccountcount');
			$servicename=$db->query_result($result, 0,'servicename');
            $gold_isv=$db->query_result($result, 0,'gold_isv');
            $silv_isv=$db->query_result($result, 0,'silv_isv');
            $bras_isv=$db->query_result($result, 0,'bras_isv');
            $iron_isv=$db->query_result($result, 0,'iron_isv');

		}else{
			//获取客服名称
			$query = "select IFNULL((SELECT	accountcount FROM vtiger_serviceassignset WHERE	serviceid = ? LIMIT 0,1),
				(SELECT	accountcount FROM vtiger_serviceassignset_default ORDER BY accountcount DESC LIMIT 0,1)) AS can_accountcount,
			    (select last_name from vtiger_users where id=?) as last_name from dual";
			$result = $db->pquery($query, array($serviceid,$serviceid));
			if ($result && $db->num_rows($result)>0){
				$can_accountcount=$db->query_result($result, 0,'can_accountcount');
				$servicename=$db->query_result($result, 0,'last_name');
			}
		}
$assignresult="客服：".$servicename.",可分配：".$can_accountcount."个客户,已分配：".$assignaccountcount."个有效客户,(金：".$gold_isv.",银：".$silv_isv."，铜：".$bras_isv.",铁：".$iron_isv.")";
		return $assignresult;
	}
	
	/**
	 * 客服分配处理
	 * @param Vtiger_Request $request
	 */
	public static function doServiceAssign(Vtiger_Request $request) {
		$assigntype = $request->get('assigntype');
		$departmentid = $request->get('departmentid');
		$productid = $request->get('productid');
		$ownerid = $request->get('ownerid');
		
		$ownerid=implode(' |##| ', $ownerid);
		$serviceid = $request->get('serviceid');
		$assignRadio = $request->get('assignRadio');
		$oldserviceid = $request->get('oldserviceid');
		$accountid = $request->get('accountid');
		//选择的客户
		$selectAccountids = $request->get('selectAccountids');
		
		$selectAccountids=implode(',', $selectAccountids);
		
		$db = PearDatabase::getInstance();
		
		//获取登录用户信息
		$currentUser = Users_Record_Model::getCurrentUserModel();
		$userid = $currentUser->get('id');
		
		//获取客户信息
		$account_array=self::getAccountInfos($request);
		
	    if (count($account_array)<=0){
	    	return 1;
	    }
	    $num_rows=count($account_array);
	    
	    //获取当前客服可分配客户数量
	    $canAccountcount=self::getCanAccountcount($serviceid);
	
	    //获取已分配客服的客户数量
	    $assignaccountcount='0';
	    $query = "select
					count(1) as assignaccountcount
					from vtiger_servicecomments where serviceid=?
	    		    and vtiger_servicecomments.related_to in(select vtiger_account.accountid from vtiger_account where vtiger_account.accountrank in('gold_isv','silv_isv','bras_isv','visp_isv'))";
	    $result = $db->pquery($query, array($serviceid));
	    if ($result && $db->num_rows($result)>0){
	    	$assignaccountcount=$db->query_result($result, 0,'assignaccountcount');
	    }

		if ($assigntype =='productby'){
			$accountids=array();
			for($i=0; $i<$num_rows; $i++) {
				if($assignRadio =="0" && !strstr($selectAccountids,$account_array[$i]['accountid'])){continue;}
				
				$accountids[]=$account_array[$i]['accountid'];
			}
			//按产品分配
			$sqlQuery="select
					vtiger_salesorderproductsrel.salesorderproductsrelid,
					vtiger_salesorderproductsrel.accountid
					from vtiger_salesorderproductsrel where vtiger_salesorderproductsrel.productid=?
					and FIND_IN_SET(vtiger_salesorderproductsrel.accountid,?)>0";
			
			$result = $db->pquery($sqlQuery, array($productid,implode(',', $accountids)));
			if (empty($result) || $db->num_rows($result)==0){
				return 1;
			}
			$num_rows = $db->num_rows($result);

			//超过最大客户数量时候，设置最大值
			if (count($accountids)+intval($assignaccountcount,10)>intval($canAccountcount,10)){
				return 2;
			}
			
			for($i=0; $i<$num_rows; $i++) {
				$arr_servicecomments=array();
				$arr_servicecomments['assigntype']='productby';
				$arr_servicecomments['salesorderproductsrelid']=$db->query_result($result,$i,'salesorderproductsrelid');
				$arr_servicecomments['related_to']=$db->query_result($result,$i,'accountid');
				$arr_servicecomments['serviceid']=$serviceid;
				$arr_servicecomments['assignerid']=$userid;
				ServiceComments_Record_Model::insertServiceComments($arr_servicecomments);
				//超过最大数，退出
				if ($i+intval($assignaccountcount,10)>=intval($canAccountcount,10)){
					break;
				}
			}
		}else{
			//按客户分配
			//超过最大客户数量时候，设置最大值
			$account_num=0;
			if($assignRadio =="0"){
				$account_num=count(explode(",",$selectAccountids));
			}else{
				$account_num=$num_rows;
			}
			if ($account_num+intval($assignaccountcount,10)>intval($canAccountcount,10)){
				return 2;
			}
			
			for($i=0; $i<$account_num; $i++) {
				$arr_servicecomments=array();
				$arr_servicecomments['assigntype']='accountby';
				$arr_servicecomments['salesorderproductsrelid']=0;
				if($assignRadio =="0" ){
					$arr_select_account=explode(",",$selectAccountids);
					$arr_servicecomments['related_to']=$arr_select_account[$i];
				}else{
					$arr_servicecomments['related_to']=$account_array[$i]['accountid'];
				}
				$arr_servicecomments['serviceid']=$serviceid;
				$arr_servicecomments['assignerid']=$userid;
				ServiceComments_Record_Model::insertServiceComments($arr_servicecomments);
				//超过最大数，退出
				if ($i+intval($assignaccountcount,10)>=intval($canAccountcount,10)){
					break;
				}
			}
		}
		
		//更新客服人员
// 		if (!empty($oldserviceid)){
// 			$updateSql="update vtiger_servicecomments set serviceid=? where vtiger_servicecomments.serviceid=?";
// 			$parm_update_array[]=$serviceid;
// 			$parm_update_array[]=$oldserviceid;
// 			//客户
// 			if (!empty($accountid)){
// 				$updateSql.=" and vtiger_servicecomments.related_to=?";
// 				$parm_update_array[]=$accountid;
// 			}else{
				
// 			}
// 			$db->pquery($updateSql, $parm_update_array);
// 		}
		return 0;
	}	
	
	/**
	 * 获取可分配客户数量
	 * @param $serviceid 客服id
	 */
	public static function getCanAccountcount($serviceid) {
		$db = PearDatabase::getInstance();
		$assignresult="";
		//获取可分配的客户数量
		$can_accountcount="100";
	
		$query = "select
					T.can_accountcount,
					T.servicename,
					count(1) as assignaccountcount
					from (
						select
							IFNULL((select accountcount from vtiger_serviceassignset where serviceid=vtiger_servicecomments.serviceid LIMIT 0,1),
							(select accountcount from vtiger_serviceassignset_default order by accountcount desc LIMIT 0,1)) as can_accountcount,
							(select last_name from vtiger_users where vtiger_users.id=vtiger_servicecomments.serviceid) as servicename
						from vtiger_servicecomments where serviceid=?) T GROUP BY T.can_accountcount,T.servicename";

		$result = $db->pquery($query, array($serviceid));
		if ($result && $db->num_rows($result)>0){
			$can_accountcount=$db->query_result($result, 0,'can_accountcount');
		}
		return $can_accountcount;
	}
}
