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
class JobAlerts_Record_Model extends Vtiger_Record_Model {

	/**
	 * 获取客户名称
	 * @param <String> $type
	 * @return 客户名称
	 */
	public static function getAccountName($accountid) {
		$db = PearDatabase::getInstance();
		$sql="select accountname from vtiger_account where accountid=?";
		$result = $db->pquery($sql, array($accountid));
		if (!empty($result) && $db->num_rows($result)>0){
			return $db->query_result($result, 0,'accountname');
		}
		return "";
	}
	
	/**
	 * 获取提醒件数
	 * @param <String> $type
	 * @return 件数
	 */
	public static function getReminderResultCount($type='all') {
		global $current_user;
		$db = PearDatabase::getInstance();
		$listQuery="select count(1) cnt from vtiger_jobalerts where 1=1 ";
		
		//检索条件获取
		$listQuery.= JobAlerts_Record_Model::getWhereCondition($type);
		
		$result = $db->pquery($listQuery, array());
		return $db->query_result($result, 0,'cnt');
	}

    public static function getReminderResultCountReadState($type='all') {
        global $current_user;
        $db = PearDatabase::getInstance();
        $listQuery="select count(1) cnt from vtiger_jobalerts where 1=1 ";

        //检索条件获取
        $listQuery.= JobAlerts_Record_Model::getWhereConditionReadState($type);

        $result = $db->pquery($listQuery, array());
        return $db->query_result($result, 0,'cnt');
    }
	
	/**
	 * 判断是否有编辑权限
	 * @param Vtiger_Request $request
	 */
	public static function checkEditPermission(Vtiger_Request $request) {
		$db = PearDatabase::getInstance();
		
		global $current_user;
		$record=$request->get('record');
		
		$sql="select * from vtiger_jobalerts where jobalertsid=?";
		$result = $db->pquery($sql, array($record));
		$creatorid = $db->query_result($result, 0,'creatorid');
		$alertstatus = $db->query_result($result, 0,'alertstatus');
		
		//管理员的场合,有权限
		if($current_user->superadmin){
			return true;
		}
		if ($creatorid == $current_user->id && $alertstatus!='finish'){
			return true;
		}
		return false;
	}
	
	/**
	 * 判断是否有处理完成权限
	 * @param Vtiger_Request $request
	 */
	public static function checkDisposePermission(Vtiger_Request $request) {
		$db = PearDatabase::getInstance();
	
		global $current_user;
		$record=$request->get('record');
	
		$sql="select * from vtiger_jobalerts where jobalertsid=?";
		$result = $db->pquery($sql, array($record));
		$ownerid = $db->query_result($result, 0,'ownerid');
		$alertstatus = $db->query_result($result, 0,'alertstatus');
		
		//管理员的场合,有权限
		if($current_user->superadmin){
			return true;
		}
		
		if ($ownerid==$current_user->id && $alertstatus!='finish'){
			return true;
		}
		return false;
	}
	
	/**
	 * 获取检索条件
	 * @param <String> $type
	 * @return 检索条件
	 */
	public static function getWhereCondition($type='all') {
		global $current_user;
		$listQuery="";
        $where=getAccessibleUsers('JobAlerts','List');
        if($where!='1=1'){
            $sqlPermission=" or vtiger_jobalerts.jobalertsid in(select jobalertsid from vtiger_jobalertsreminder where vtiger_jobalertsreminder.alertid".$where.")";
        }else{
            $sqlPermission=" or vtiger_jobalerts.jobalertsid in(select jobalertsid from vtiger_jobalertsreminder)";//写法有问题效率太低
        }
		if($type=='new'){//我新创建的提醒
			$listQuery .=" and vtiger_jobalerts.alertstatus='wait' and STR_TO_DATE(vtiger_jobalerts.alerttime,'%Y-%m-%d')>DATE_FORMAT(SYSDATE(),'%Y-%m-%d') and vtiger_jobalerts.ownerid=".$current_user->id;
		}if($type=='wait'){//我创建的待处理的提醒
			$listQuery .=" and vtiger_jobalerts.alertstatus='wait' and STR_TO_DATE(vtiger_jobalerts.alerttime,'%Y-%m-%d')<=DATE_FORMAT(SYSDATE(),'%Y-%m-%d') and vtiger_jobalerts.ownerid=".$current_user->id;
		}elseif($type=='finish'){//我创建的已完成的提醒
			$listQuery .=" and vtiger_jobalerts.alertstatus='finish' and vtiger_jobalerts.ownerid=".$current_user->id;
		}elseif($type=='myreminder'){//我的待处理提醒
			//管理员*增加访问数据的限制
			if(!$current_user->superadmin){
				$listQuery .= " and vtiger_jobalerts.alertstatus='wait' and (vtiger_jobalerts.ownerid=".$current_user->id.$sqlPermission.")";
			}else{
				$listQuery .=" and vtiger_jobalerts.alertstatus='wait' and vtiger_jobalerts.ownerid=".$current_user->id;
			}
		}elseif($type=='relation'){//与我相关的全部提醒
			//管理员*增加访问数据的限制
			if(!$current_user->superadmin){
				$listQuery .= " and (vtiger_jobalerts.ownerid=".$current_user->id.$sqlPermission.")";
			}
		}else{//全部提醒
			//管理员*增加访问数据的限制
			if(!$current_user->superadmin){
				$listQuery .= " and (vtiger_jobalerts.ownerid=".$current_user->id.$sqlPermission.")";
			}
		}
		return $listQuery;
	}

    public static function getWhereConditionReadState($type='all') {
        global $current_user;
        $listQuery="";
        $listQuery .=" and state = 0";
        $sqlPermission='';
        $where=getAccessibleUsers('JobAlerts','List');
        if($where!='1=1'){
            $sqlPermission=" or vtiger_jobalerts.jobalertsid in(select jobalertsid from vtiger_jobalertsreminder where vtiger_jobalertsreminder.alertid".$where.")";
        }
        if($type=='new'){//我新创建的提醒
            $listQuery .=" and vtiger_jobalerts.alertstatus='wait' and STR_TO_DATE(vtiger_jobalerts.alerttime,'%Y-%m-%d')>DATE_FORMAT(SYSDATE(),'%Y-%m-%d') and vtiger_jobalerts.ownerid=".$current_user->id;
        }if($type=='wait'){//我创建的待处理的提醒
            $listQuery .=" and vtiger_jobalerts.alertstatus='wait' and STR_TO_DATE(vtiger_jobalerts.alerttime,'%Y-%m-%d')<=DATE_FORMAT(SYSDATE(),'%Y-%m-%d') and vtiger_jobalerts.ownerid=".$current_user->id;
        }elseif($type=='finish'){//我创建的已完成的提醒
            $listQuery .=" and vtiger_jobalerts.alertstatus='finish' and vtiger_jobalerts.ownerid=".$current_user->id;
        }elseif($type=='myreminder'){//我的待处理提醒
            //管理员*增加访问数据的限制
            if(!$current_user->superadmin){
                $listQuery .= " and vtiger_jobalerts.alertstatus='wait' and (vtiger_jobalerts.ownerid=".$current_user->id.$sqlPermission.")";
            }else{
                $listQuery .=" and vtiger_jobalerts.alertstatus='wait' and vtiger_jobalerts.ownerid=".$current_user->id;
            }
        }elseif($type=='relation'){//与我相关的全部提醒
            //管理员*增加访问数据的限制
            if(!$current_user->superadmin){
                $listQuery .= " and (vtiger_jobalerts.ownerid=".$current_user->id.$sqlPermission.")";
            }
        }else{//全部提醒
            //管理员*增加访问数据的限制
            if(!$current_user->superadmin){
                $listQuery .= " and (vtiger_jobalerts.ownerid=".$current_user->id.$sqlPermission.")";
            }
        }
        return $listQuery;
    }
	
	/**
     *  记录邮件发送提醒
	 * 创建工作提醒
	 * @param $arr_reminder
	 * 参数及字段说明:</br>
	 * $arr_reminder:数组(array('字段名')=>'value')</br>
	 * subject:主题</br>
	 * alerttime:提醒时间</br>
	 * modulename:模块名称</br>
	 * moduleid:模块id</br>
	 * alertcontent:提醒内容</br>
	 * alertid:提醒人(多个提醒人用逗号分隔,例:a,b)</br>
	 * activitytype:类型(电话:Call,会议:Meeting,任务:Task)</br>
	 * taskpriority:优先级(高:High,中: Medium,低:Low)</br>
	 * accountid:客户id</br>
	 * remark:备注--作废
	 */
	public static function createReminder($arr_reminder) {
		$db = PearDatabase::getInstance();
		global $current_user;
		//创建人
		$arr_reminder['creatorid']=$current_user->id;
		
		//字段名取得
		$columns=array_keys($arr_reminder);
		//字段值取得
		$values=array_values($arr_reminder);
		
// 		$insertSql = "insert into vtiger_jobalerts(
//     				  subject,alerttime,modulename,moduleid,alertcontent,alertid,activitytype,taskpriority,remark,ownerid,creatorid,createdtime)
//     				  values(?,?,?,?,?,?,?,?,?,?,sysdate())";
		$insertSql = "insert into vtiger_jobalerts(" . implode(",", $columns) . ",createdtime) values(" . generateQuestionMarks($values) . ",sysdate())";
		$result = $db->pquery($insertSql, $values);

	}
    /**
     * 生成提醒
     * @param array $arr
     */
    static public function saveAlert($arr=array()){

        global $current_user, $adb;
        $jobalertsid=$adb->getUniqueID('vtiger_jobalerts');//取得表的id
        $alerttime=date('Y-m-d H:i:s');//提醒时间
        $creatorid= $current_user->id;;//标题的创建人
        $sql="insert into vtiger_jobalerts(jobalertsid,taskpriority,alertstatus,alertcontent,alerttime,subject,alertid,ownerid,accountid,`creatorid`,`createdtime`)
              values({$jobalertsid},'High','wait',?,'{$alerttime}',?,?,?,?,{$creatorid},'{$alerttime}')";
        $adb->pquery($sql,array($arr));
    }
}
