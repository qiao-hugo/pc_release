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
class ServiceComments_Record_Model extends Vtiger_Record_Model {

	/**
	 * 客服分配处理-按产品分配
	 * @param $salesorderproductsrelids 工单关联产品id(可以多个)
	 * @return true:分配成功,false:分配失败
	 */
	public static function insertServiceCommentsByProducts($salesorderproductsrelids) {
		$db = PearDatabase::getInstance();
		global $current_user;
		
		if (empty($salesorderproductsrelids)){
			return false;
		}
		
		if (is_array($salesorderproductsrelids)){
			$salesorderproductsrelids=implode(',', $salesorderproductsrelids);
		}
		
		//查询工单关联产品数据
		$sqlQuery="select
				vtiger_salesorderproductsrel.salesorderproductsrelid,
				vtiger_salesorderproductsrel.accountid,
				vtiger_salesorderproductsrel.productid 
				from vtiger_salesorderproductsrel where vtiger_salesorderproductsrel.salesorderproductsrelid in($salesorderproductsrelids)";
		$result1 = $db->pquery($sqlQuery, array());
		
		if (empty($result1)){
			return false;
		}
		
		$num_rows1 = $db->num_rows($result1);
		for($i=0; $i<$num_rows1; $i++) {
			$salesorderproductsrelid=$db->query_result($result1,$i,'salesorderproductsrelid');
			//已成交的产品id
			$arr_productids[$salesorderproductsrelid] = $db->query_result($result1,$i,'productid');
			//已成交的产品客户id
			$arr_accountids[$salesorderproductsrelid]=$db->query_result($result1,$i,'accountid');
		}
		//获取客服分配规则数据
		$productids=implode(',', $arr_productids);
		$sqlQuery="select * from vtiger_serviceassignrule where assigntype='productby' and productid in($productids)";
		$result2 = $db->pquery($sqlQuery, array());
		
		if (empty($result2)){
			return false;
		}
		
		//分配数据取得
		$num_rows2 = $db->num_rows($result2);
		
		for($i=0; $i<$num_rows2; $i++) {
			$productid=$db->query_result($result2,$i,'productid');
			//分配的产品id
			$arr_assign_productid[$productid]=$productid;
			//分配的客服id
			$arr_assign_serviceid[$productid]=$db->query_result($result2,$i,'serviceid');
		}
		
		$arr_salesorderproductsrelids=explode(',',$salesorderproductsrelids);
		for($i=0; $i<count($arr_salesorderproductsrelids); $i++) {
			$productid=$arr_productids[$arr_salesorderproductsrelids[$i]];
			if(in_array($productid, $arr_assign_productid)){
				$arr_servicecomments=array();
				$arr_servicecomments['assigntype']='productby';
				$arr_servicecomments['related_to']=$arr_accountids[$arr_salesorderproductsrelids[$i]];
				$arr_servicecomments['serviceid']=$arr_assign_serviceid[$productid];
				$arr_servicecomments['salesorderproductsrelid']=$arr_salesorderproductsrelids[$i];
				$arr_servicecomments['assignerid']=$current_user->id;
				//登录处理
				self::insertServiceComments($arr_servicecomments);
			}
		}
		return true;
	}
	
	/**
	 * 客服分配处理-按客户分配
	 * @param $accountid 客户id
	 * @return true:分配成功,false:分配失败
	 */
	public static function insertServiceCommentsByAccounts($accountid) {
		$db = PearDatabase::getInstance();
        global $current_user;
	
		if (empty($accountid)){
			return false;
		}
	
		//查询工单关联产品数据
		$sqlQuery="select
				(select vtiger_crmentity.smownerid from vtiger_crmentity where vtiger_crmentity.crmid=vtiger_account.accountid) as smownerid,
				(select vtiger_user2department.departmentid from vtiger_user2department where vtiger_user2department.userid=
					(select vtiger_crmentity.smownerid from vtiger_crmentity where vtiger_crmentity.crmid=vtiger_account.accountid)) as departmentid
				from vtiger_account where vtiger_account.accountid=? 
				and EXISTS (select vtiger_salesorder.salesorderid from vtiger_salesorder where vtiger_salesorder.accountid=vtiger_account.accountid limit 0,1)";
		
		
		$result1 = $db->pquery($sqlQuery, array($accountid));
		if (empty($result1) || $db->num_rows($result1)==0){
			return false;
		}
		
		//负责人id
		$smownerid = $db->query_result($result1,0,'smownerid');
		//部门id
		$departmentid = $db->query_result($result1,0,'departmentid');
		
		//获取客服分配规则数据
		$sqlQuery="select * from vtiger_serviceassignrule where assigntype='accountby' and vtiger_serviceassignrule.departmentid=? 
				and FIND_IN_SET(?,REPLACE(vtiger_serviceassignrule.ownerid,' |##| ',','))>0 
				order by case when modifiedtime is null then  modifiedtime else createdtime end desc ";
		$result2 = $db->pquery($sqlQuery, array($departmentid,$smownerid));
		
		if (empty($result2) || $db->num_rows($result2)==0){
			return false;
		}
		
		//分配的客服id
		$serviceid=$db->query_result($result2,0,'serviceid');
		
		$arr_servicecomments['assigntype']='accountby';
		$arr_servicecomments['related_to']=$accountid;
		$arr_servicecomments['serviceid']=$serviceid;
		$arr_servicecomments['salesorderproductsrelid']=null;
		$arr_servicecomments['assignerid']=$current_user->id;
		//登录处理
		return self::insertServiceComments($arr_servicecomments);
	}
	
	/**
	 * 获取分配类型
	 * @return 分配类型
	 */
	public static function getAssignType($id) {
		$db = PearDatabase::getInstance();
		$sqlQuery="select assigntype from vtiger_servicecomments where servicecommentsid=?";
		$values = array();
		$result = $db->pquery($sqlQuery, array($id));
		if (empty($result)){
			return '';
		}
	
		$assigntype=$db->query_result($result,0,'assigntype');
		if ($assigntype == 'accountby'){
			return 'LBL_ACCOUNT_ASSIGN';
		}else{
			return 'LBL_PRODUCT_ASSIGN';
		}
	}
	
	/**
	 * 判断是否存在
	 * @return true:存在,false:不存在
	 */
	private static function checkRecordExists($arr_servicecomments) {
		$db = PearDatabase::getInstance();
		$parm_array=array();
		if ($arr_servicecomments['assigntype'] == 'accountby'){
			$sqlQuery="select servicecommentsid from vtiger_servicecomments where assigntype=? and related_to=? ";
			$parm_array[]=$arr_servicecomments['assigntype'];
			$parm_array[]=$arr_servicecomments['related_to'];
			//$parm_array[]=$arr_servicecomments['serviceid'];
		}else{
			$sqlQuery="select servicecommentsid from vtiger_servicecomments where assigntype=? and salesorderproductsrelid=?";
			$parm_array[]=$arr_servicecomments['assigntype'];
			$parm_array[]=$arr_servicecomments['salesorderproductsrelid'];
		//$parm_array[]=$arr_servicecomments['serviceid'];
		}
		$result = $db->pquery($sqlQuery, $parm_array);
		if (empty($result) || $db->num_rows($result)==0){
			return null;
		}
		return $db->query_result($result, 0,'servicecommentsid');
	}
	
	/**
	 * 更新未跟进天数
	 * @param $servicecommentsid
     * $servicecommentsid改为客户id $accountid
     * allnofollowday:记录跟进次数
     * User/Date: adatian/20150701
	 */
	public static function updateServiceNofollowDay($accountid) {
		global $current_user;
		
		$db = PearDatabase::getInstance();
        $followfrequency=self::getFollowfrequencyDay($accountid);
		$updateSql="update vtiger_servicecomments set nofollowday=?,allnofollowday = allnofollowday + 1,allocatetime=sysdate() where vtiger_servicecomments.serviceid=? and related_to=? ";
		$db->pquery($updateSql, array($followfrequency,$current_user->id,$accountid));
	}
	
	/**
	 * 客服数据登录
	 * @param $arr_servicecomments
	 * 参数及字段说明:</br>
	 * $arr_servicecomments:数组(array('字段名')=>'value')</br>
	 * assigntype:(productby:按产品分配,accountby:按客户分配)</br>
	 * related_to:客户id</br>
	 * salesorderproductsrelid:工单关联产品id,按客户分配的场合可以设置为null</br>
	 * serviceid:客服id</br>
	 * assignerid:分配人id
	 */
	public static function insertServiceComments($arr_servicecomments) {
		$db = PearDatabase::getInstance();
		//字段名取得
		$columns=array_keys($arr_servicecomments);
		//字段值取得
		$values=array_values($arr_servicecomments);
		
		//存在性判断
	    if ($arr_servicecomments['assigntype'] == 'accountby'){
	    	$servicecommentsid=self::checkRecordExists($arr_servicecomments);
	    }else{
	    	$servicecommentsid=self::checkRecordExists($arr_servicecomments);
	    }
        $followfrequency=self::getFollowfrequencyDay($arr_servicecomments['related_to']);
        //客户客服数据的跟新历史记录数组构造
        $modtracker_Arr = array();
        $modtrackerid = $db->getUniqueID('vtiger_modtracker_basic');
        $modtracker_Arr[]=$modtrackerid;
        $modtracker_Arr[]=$arr_servicecomments['related_to'];
        $modtracker_Arr[]="Accounts";
        $modtracker_Arr[]=$arr_servicecomments['assignerid'];
        $modtracker_Arr[]='0';
        $in_tracker_sql = "INSERT INTO vtiger_modtracker_basic VALUES(?,?,?,?,SYSDATE(),?)";


        $modtracker_detail_Arr = array();
        $modtracker_detail_Arr[] =$modtrackerid;
        $modtracker_detail_Arr[] ='serviceid';



        $in_trackerdetail_sql = "insert INTO `vtiger_modtracker_detail` VALUES(?,?,?,?)";
		if (!empty($servicecommentsid)) {
			//客服跟进数据更新
			$updateSql = "update vtiger_servicecomments set assigntype=?,related_to=?,salesorderproductsrelid=?
					,serviceid=?,assignerid=?,allocatetime=sysdate(),modifiedtime=sysdate(),nofollowday=? where servicecommentsid=?";
			$parm_array[]=$arr_servicecomments['assigntype'];
			$parm_array[]=$arr_servicecomments['related_to'];
			$parm_array[]=$arr_servicecomments['salesorderproductsrelid'];
			$parm_array[]=$arr_servicecomments['serviceid'];
			$parm_array[]=$arr_servicecomments['assignerid'];
            $parm_array[]=$followfrequency;
            $parm_array[]=$servicecommentsid;


            $orgin_data = $db->run_query_allrecords("SELECT IFNULL(( SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE serviceid = vtiger_users.id ), '--' ) AS smownerid FROM vtiger_servicecomments WHERE servicecommentsid = {$servicecommentsid} LIMIT 1");
            $orgin_servicename = $orgin_data[0][0];
            $modtracker_detail_Arr[] = $orgin_servicename;
            $modtracker_detail_Arr[] = self::getnamebyusid($arr_servicecomments['serviceid']);
			$db->pquery($updateSql, $parm_array);
		}else{
			//客服跟进数据插入
            $servicecommentsid= $db->getUniqueID(vtiger_servicecomments);
            $insertSql = "insert into vtiger_servicecomments(" . implode(",", $columns) . ",addtime,allocatetime,modifiedtime,nofollowday,servicecommentsid)
				values(" . generateQuestionMarks($values) . ",sysdate(),sysdate(),sysdate(),".$followfrequency.",".$servicecommentsid.")";
            $modtracker_detail_Arr[] = '--';
            $modtracker_detail_Arr[] = self::getnamebyusid($arr_servicecomments['serviceid']);
			$db->pquery($insertSql, $values);
		}
        $accountassignserverSql="UPDATE vtiger_account SET serviceid=? WHERE accountid=?";
        $db->pquery($accountassignserverSql,array($arr_servicecomments['serviceid'],$arr_servicecomments['related_to']));

        $db->pquery($in_tracker_sql,$modtracker_Arr);
        $db->pquery($in_trackerdetail_sql,$modtracker_detail_Arr);
        //wangbin 2016-4-18 根据客户最近的购买的产品选择自动关联客服回访计划;
        $date = strtotime(date("Y-m-d",time()));
        $accountid =  $arr_servicecomments['related_to'];
        $serplanid_sql = "SELECT vtiger_servicereturnplan.servicereturnplanid FROM vtiger_servicecontracts INNER JOIN vtiger_crmentity ON servicecontractsid = crmid LEFT JOIN vtiger_parent_contracttype ON vtiger_parent_contracttype.parent_contracttypeid = vtiger_servicecontracts.parent_contracttypeid LEFT JOIN vtiger_servicereturnplan ON vtiger_servicereturnplan.producttype = parent_contracttype WHERE sc_related_to = ? AND deleted = ? AND vtiger_servicecontracts.parent_contracttypeid != '' ORDER BY createdtime DESC LIMIT 1";
        $servicereturnplanid = $db->pquery($serplanid_sql,array($accountid,0));
        if($db->num_rows($servicereturnplanid)==1){
            $returnidarray = $db->query_result_rowdata($servicereturnplanid,0); //查找关联详细的回访计划的ID；
            $returnid = $returnidarray['servicereturnplanid']; //查找关联详细的回访计划的ID；
        }
        $servicereturnplan_detail = $db->pquery("SELECT * FROM vtiger_servicereturnplan_detail WHERE returnplanid= ?",array($returnid));
        $servicereturnplan_detail_result = array();

        $check_commreturn = $db->pquery("SELECT * FROM vtiger_servicecomments_returnplan WHERE accountid = ? AND isfollow=?",array($accountid,'0'));
        if($db->num_rows($check_commreturn)>0){
            //如果客户的客服以及回访计划有变动留着下次修改；
        }else{
            while($row=$db->fetchByAssoc($servicereturnplan_detail)){
                $servicereturnplan_detail_result['uppertime']=date("Y-m-d",strtotime("+".$row['upperlimit']."days",time()));
                $servicereturnplan_detail_result['lowertime']=date("Y-m-d",strtotime("+".$row['lowerlimit']."days",time()));
                $servicereturnplan_detail_result['reviewcontent']=$row['returnplantext'];
                $servicereturnplan_detail_result['sort']=$row['sequence'];
                $servicereturnplan_detail_result['accountid']=$arr_servicecomments['related_to'];
                $servicereturnplan_detail_result['isfollow']='0';
                $servicereturnplan_detail_result['commentsid']=$servicecommentsid;
                $insert_commentsnplan = "INSERT INTO vtiger_servicecomments_returnplan ( uppertime, lowertime,reviewcontent,sort,accountid,isfollow,commentsid) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $db->pquery($insert_commentsnplan,$servicereturnplan_detail_result) ;
            }
        }
        //wangbin 在这里需要再一次进行前台工作流列表数据进行跟新
        $accountid = $parm_array[]=$arr_servicecomments['related_to'];
        AutoTask_BasicAjax_Action::service_assign($accountid);
		return true;
	}

    /**
     * @param $accountid 客户id 获取保护频率
     * @return int
     * User/Date: adatian/20150701
     * @throws Exception
     */
    public static function getFollowfrequencyDay($accountid){
        $db = PearDatabase::getInstance();
        $sql = "SELECT
                        followfrequency
                    FROM
                        vtiger_followfrequency
                    WHERE
                        vtiger_followfrequency.accountrank = (
                           SELECT accountrank FROM vtiger_account WHERE accountid= ?
                          )";
        $querysql = $db->pquery($sql, array($accountid));
        if ($db->num_rows($querysql)>0) {
            $followfrequency = $db->query_result($querysql, 0 ,'followfrequency');
        }else{
            $followfrequency = 31;
        }
        return $followfrequency;

    }
    public static function updatefollow ($commentreturnplanid){
       $db =  PearDatabase::getInstance();
       $sql = "UPDATE vtiger_servicecomments_returnplan SET isfollow=? WHERE commentreturnplanid = ?";
       $db->pquery($sql,array(1,$commentreturnplanid));
    }

    public function getnamebyusid($uid){
        $adb =  PearDatabase::getInstance();
        if($uid>0){
            $sql = "SELECT IFNULL(( SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE vtiger_users.id = {$uid} ), '--' ) AS smownerid";
            $data = $adb->run_query_allrecords($sql);
            return $data[0]['smownerid'];
        }
    }
	
}
