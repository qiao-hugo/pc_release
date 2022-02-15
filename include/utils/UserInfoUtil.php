<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

require_once('include/database/PearDatabase.php');
require_once('include/database/Postgres8.php');
require_once('include/utils/utils.php');
require_once('include/utils/GetUserGroups.php');
include_once('config.php');
require_once("include/events/include.inc");
require_once 'includes/runtime/Cache.php';
require_once 'crmcache/modelinfo.php';
global $modelinfo;
global $log;

/** To retreive the mail server info resultset for the specified user
  * @param $user -- The user object:: Type Object
  * @returns  the mail server info resultset
 */
function getMailServerInfo($user)
{
	global $log;
	$log->debug("Entering getMailServerInfo(".$user->user_name.") method ...");
	global $adb;
        $sql = "select * from vtiger_mail_accounts where status=1 and user_id=?";
        $result = $adb->pquery($sql, array($user->id));
	$log->debug("Exiting getMailServerInfo method ...");
	return $result;
}

function parseTemplet($uitype,$rawValue){
    if($uitype == '54') {
        $valueArray = ($rawValue != "") ? explode(' |##| ',$rawValue) : array();
        //用户信息取得
        $userlist=getOwnerNameList($valueArray);
        $tmpArray = array();
        foreach($valueArray as $index => $val) {
            $tmpArray[] = $userlist[$val];
        }
        $value = implode(', ', $tmpArray);
        $value = textlength_check($value);
    }elseif($uitype == '110') {
        $valueArray = ($rawValue != "") ? explode(' |##| ',$rawValue) : array();
        //用户信息取得
        $userlist=getProductsList($valueArray);
        $tmpArray = array();
        foreach($valueArray as $index => $val) {
            $tmpArray[] = $userlist[$val];
        }
        $value = implode(', ', $tmpArray);
        $value = textlength_check($value);
    }
    return $value;
}
/** To get the Role of the specified user
  * @param $userid -- The user Id:: Type integer
  * @returns  vtiger_roleid :: Type String
 */
function fetchUserRole($userid)
{
	global $log;
	$log->debug("Entering fetchUserRole(".$userid.") method ...");
	global $adb;
	$sql = "select roleid from vtiger_user2role where userid=?";
        $result = $adb->pquery($sql, array($userid));
	$roleid=  $adb->query_result($result,0,"roleid");
	$log->debug("Exiting fetchUserRole method ...");
	return $roleid;
}
/** 制定用户的部门信息
 * @param unknown $userid
 * @return Ambigous <s, --, string, mixed>
 */
function fetchUserDepartment($userid){
	global $log;
	$log->debug("Entering fetchUserDepartment(".$userid.") method ...");
	global $adb;
	$sql = "select departmentid from vtiger_user2department where userid=?";
	$result = $adb->pquery($sql, array($userid));
	$roleid=  $adb->query_result($result,0,"departmentid");
	$log->debug("Exiting fetchUserDepartment method ...");
	return $roleid;
}
/** Function to get the lists of groupids releated with an user
 * This function accepts the user id as arguments and
 * returns the groupids related with the user id
 * as a comma seperated string
*/
function fetchUserGroupids($userid)
{
	global $log;
	$log->debug("Entering fetchUserGroupids(".$userid.") method ...");
	global $adb;
        $focus = new GetUserGroups();
        $focus->getAllUserGroups($userid);
		//Asha: Remove implode if not required and if so, also remove explode functions used at the recieving end of this function
        $groupidlists = implode(",",$focus->user_groups);
	$log->debug("Exiting fetchUserGroupids method ...");
        return $groupidlists;

}

/** Function to get all the vtiger_tab utility action permission for the specified vtiger_profile
  * @param $profileid -- Profile Id:: Type integer
  * @returns  Tab Utility Action Permission Array in the following format:
  * $tabPermission = Array($tabid1=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission),
  *                        $tabid2=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission),
  *                                |
  *                        $tabidn=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission))
  *
 */

function getTabsUtilityActionPermission($profileid)
{
	global $log;
	$log->debug("Entering getTabsUtilityActionPermission(".$profileid.") method ...");

	global $adb;
	$check = Array();
	$temp_tabid = Array();
	$sql1 = "select * from vtiger_profile2utility where profileid=? order by(tabid)";
	$result1 = $adb->pquery($sql1, array($profileid));
        $num_rows1 = $adb->num_rows($result1);
        for($i=0; $i<$num_rows1; $i++)
        {
		$tab_id = $adb->query_result($result1,$i,'tabid');
		if(! in_array($tab_id,$temp_tabid))
		{
			$temp_tabid[] = $tab_id;
			$access = Array();
		}

		$action_id = $adb->query_result($result1,$i,'activityid');
		$per_id = $adb->query_result($result1,$i,'permission');
		$access[$action_id] = $per_id;
		$check[$tab_id] = $access;


	}

	$log->debug("Exiting getTabsUtilityActionPermission method ...");
	return $check;

}
/**This Function returns the Default Organisation Sharing Action Array for all modules whose sharing actions are editable
  * The result array will be in the following format:
  * Arr=(tabid1=>Sharing Action Id,
  *      tabid2=>SharingAction Id,
  *            |
  *            |
  *            |
  *      tabid3=>SharingAcion Id)
  */

function getDefaultSharingEditAction()
{
	global $log;
	$log->debug("Entering getDefaultSharingEditAction() method ...");
	global $adb;
	//retreiving the standard permissions
	$sql= "select * from vtiger_def_org_share where editstatus=0";
	$result = $adb->pquery($sql, array());
	$permissionRow=$adb->fetch_array($result);
	do
	{
		for($j=0;$j<count($permissionRow);$j++)
		{
			$copy[$permissionRow[1]]=$permissionRow[2];
		}

	}while($permissionRow=$adb->fetch_array($result));

	$log->debug("Exiting getDefaultSharingEditAction method ...");
	return $copy;

}
/**This Function returns the Default Organisation Sharing Action Array for modules with edit status in (0,1)
  * The result array will be in the following format:
  * Arr=(tabid1=>Sharing Action Id,
  *      tabid2=>SharingAction Id,
  *            |
  *            |
  *            |
  *      tabid3=>SharingAcion Id)
  */
function getDefaultSharingAction()
{
	global $log;
	$log->debug("Entering getDefaultSharingAction() method ...");
	global $adb;
	//retreivin the standard permissions
	$sql= "select * from vtiger_def_org_share where editstatus in(0,1)";
	$result = $adb->pquery($sql, array());
	$permissionRow=$adb->fetch_array($result);
	do
	{
		for($j=0;$j<count($permissionRow);$j++)
		{
			$copy[$permissionRow[1]]=$permissionRow[2];
		}

	}while($permissionRow=$adb->fetch_array($result));
	$log->debug("Exiting getDefaultSharingAction method ...");
	return $copy;

}


/**This Function returns the Default Organisation Sharing Action Array for all modules
  * The result array will be in the following format:
  * Arr=(tabid1=>Sharing Action Id,
  *      tabid2=>SharingAction Id,
  *            |
  *            |
  *            |
  *      tabid3=>SharingAcion Id)
  */
function getAllDefaultSharingAction()
{
	global $log;
	$log->debug("Entering getAllDefaultSharingAction() method ...");
	global $adb;
	$copy=Array();
	//retreiving the standard permissions
	$sql= "select * from vtiger_def_org_share";
	$result = $adb->pquery($sql, array());
	$num_rows=$adb->num_rows($result);

	for($i=0;$i<$num_rows;$i++)
	{
		$tabid=$adb->query_result($result,$i,'tabid');
		$permission=$adb->query_result($result,$i,'permission');
		$copy[$tabid]=$permission;

	}

	$log->debug("Exiting getAllDefaultSharingAction method ...");
	return $copy;

}

/** Function to update user to vtiger_role mapping based on the userid
  * @param $roleid -- Role Id:: Type varchar
  * @param $userid User Id:: Type integer
  *
 */
function updateUser2RoleMapping($roleid,$userid)
{
global $log;
$log->debug("Entering updateUser2RoleMapping(".$roleid.",".$userid.") method ...");
  global $adb;
  //Check if row already exists
  $sqlcheck = "select * from vtiger_user2role where userid=?";
  $resultcheck = $adb->pquery($sqlcheck, array($userid));
  if($adb->num_rows($resultcheck) == 1)
  {
  	$sqldelete = "delete from vtiger_user2role where userid=?";
	$delparams = array($userid);
  	$result_delete = $adb->pquery($sqldelete, $delparams);
  }
  $sql = "insert into vtiger_user2role(userid,roleid) values(?,?)";
  $params = array($userid, $roleid);
  $result = $adb->pquery($sql, $params);
	$log->debug("Exiting updateUser2RoleMapping method ...");

}
/**
 * 
 * @param unknown $roleid
 * @param unknown $userid
 */
function updateUser2DepartmentMapping($roleid,$userid)
{
	global $log;
	$log->debug("Entering updateUser2DepartmentMapping(".$roleid.",".$userid.") method ...");
	global $adb;
	//Check if row already exists
	$sqlcheck = "select * from vtiger_user2department where userid=?";
	$resultcheck = $adb->pquery($sqlcheck, array($userid));
	if($adb->num_rows($resultcheck) == 1)
	{
		$sqldelete = "delete from vtiger_user2department where userid=?";
		$delparams = array($userid);
		$result_delete = $adb->pquery($sqldelete, $delparams);
	}
	$sql = "insert into vtiger_user2department(userid,departmentid) values(?,?)";
	$params = array($userid, $roleid);
	$result = $adb->pquery($sql, $params);
	$log->debug("Exiting updateUser2DepartmentMapping method ...");

}


/** 老框架问题
  * @param $roleid -- Role Id:: Type varchar
  * @returns $rolename -- Role Name:: Type varchar
  *
 */
function getRoleName($roleid)
{
	global $log;
	$log->debug("Entering getRoleName(".$roleid.") method ...");
	$arrRoles=Vtiger_Cache::get('vtiger_', 'roles');
	if(empty($arrRoles[$roleid])){
		global $adb;
	
		$sql1 = "select * from vtiger_role";
		$result = $adb->pquery($sql1, array());
		while($row=$adb->fetch_array($result)){
			$arrRoles[$row['roleid']]=$row;
		}
		//$rolename = $adb->query_result($result,0,"rolename");
		Vtiger_Cache::set('vtiger_', 'roles', $arrRoles);
	}
	$log->debug("Exiting getRoleName method ...");
	return $arrRoles[$roleid]['rolename'];
	//return $rolename;
}
/**
 * 新增的读取部门信息的，为毛是临时读取这样的表，效率如何保证
 * @param unknown $roleid
 * @return Ambigous <s, --, string, mixed>
 */
function getDerparmentName($roleid)
{
	global $log;
	$log->debug("Entering getRoleName(".$roleid.") method ...");
	global $adb;
	$sql1 = "select * from vtiger_departments where departmentid=?";
	$result = $adb->pquery($sql1, array($roleid));
	$rolename = $adb->query_result($result,0,"departmentname");
	$log->debug("Exiting getRoleName method ...");
	return $rolename;
}
/** Function to check if the currently logged in user is permitted to perform the specified action
  * @param $module -- Module Name:: Type varchar
  * @param $actionname -- Action Name:: Type varchar
  * @param $recordid -- Record Id:: Type integer
  * @returns yes or no. If Yes means this action is allowed for the currently logged in user. If no means this action is not allowed for the currently logged in user
  *
 */
function isPermitted($module,$actionname,$record_id=''){
	global $current_user;
	//require('user_privileges/user_privileges_'.$current_user->id.'.php');
	//require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
	$permission = "no";
	//用户,面板,上传（不涉及后台访问）
	$parenttab='';
	if(isset($_REQUEST['parenttab'])){
		$parenttab=$_REQUEST['parenttab'];
	}
	if(($module == 'Users' || $module == 'Home' || $module == 'uploads') && $parenttab != 'Settings'){
		return "yes";
	}
	$notPermittedModules = array('ModComments','Integration' ,'DashBoard');
	if(in_array($module, $notPermittedModules) && $actionname != 'List'){
		return 'yes';
	}
	//禁止直接新增
	if(empty($record_id) && in_array($module,array('DisposeComplaints','DisposeMaintenance','ServiceComments'))){
		return 'no';
	}
	//管理员可以访问设置相关
	if($module == 'Settings' || $module == 'Administration' || $module == 'System' || $parenttab == 'Settings'){
		if($current_user->is_admin=='on'){
			$permission = "yes";
		}
		return $permission;
	}
	
	
	
	//Retreiving the Tabid and Action Id
	//$tabid = getTabid($module);
	//$actionid=getActionid($actionname);
	$saction=Vtiger_Action_Model::$standardActions;
	$uaction=Vtiger_Action_Model::$utilityActions;
	
	$checkModule = $module;
	if($checkModule == 'Events'){
		$checkModule = 'Calendar';
	}
	//活动模块
	if(vtlib_isModuleActive($checkModule)){
		//管理员权限
		if($current_user->is_admin=='on'){
			return "yes";
		}
		//当前可访问操作
		$viewPermission=$current_user->viewPermission;
		//If no actionid, then allow action is vtiger_tab permission is available
		/* if($module=='SalesorderProductsrel' && $actionname=='Editview'){
			return 'no';
		} */
		
		if(in_array($actionname,$saction) || in_array($actionname,$uaction)){
			if(isset($viewPermission[$module.'/'.$actionname])){
				return "yes";
			}else{
				return "no";
			}
		}

		//$action = getActionname($actionid);
		//Checking for view all permission
		//Checking for edit all permission
		//全局权限判断[暂时屏蔽]
		/*if($profileGlobalPermission[1] ==0 || $profileGlobalPermission[2] ==0){
			if($actionid == 3 || $actionid == 4){
				return 'yes';
			}
		}
		if($profileGlobalPermission[2] ==0){
			if($actionid == 3 || $actionid == 4 || $actionid ==0 || $actionid ==1){
				$permission = "yes";
				return 'yes';
			}
		}*/
		//Checking for vtiger_tab permission
		if(!isset($viewPermission[$module.'/'.$actionname])){
			return 'no';
		}
		//旧的模块操作权限判断[废弃中]
		/*if(strlen($profileActionPermission[$tabid][$actionid]) <  1 && $profileActionPermission[$tabid][$actionid] == ''){
			return 'yes';
		}
		if($profileActionPermission[$tabid][$actionid] != 0 && $profileActionPermission[$tabid][$actionid] != ''){
			return 'no';
		}*/
		//新增无数据权限限制 编辑需要
		if($record_id == ''){
			return 'yes';
		}

		//If modules is Products,Vendors,Faq,PriceBook then no sharing
		//开放模块不验证 如首页 帮助 用户登录
		if($record_id != ''){
			if(getTabOwnedBy($module) == 1){
				return 'yes';
			}
		}

		//Retreiving the RecordOwnerId 获取数据所属人
		/* $recOwnType='';
		$recOwnId='';
		$recordOwnerArr=getRecordOwnerId($record_id);
		foreach($recordOwnerArr as $type=>$id){
			$recOwnType=$type;
			$recOwnId=$id;
		} */
		//Retreiving the default Organisation sharing Access
		//$others_permission_id = $defaultOrgSharingPermission[$tabid];
		
		//if($recOwnType == 'Users'){
			//检测当前用户是否为数据所有者
			/* if($current_user->id == $recOwnId){
				return 'yes';
			} */
			//Checking if the Record Owner is the Subordinate Use
			//默认可访问下级角色用户的数据
			/* foreach($subordinate_roles_users as $roleid=>$userids){
				if(in_array($recOwnId,$userids)){
					$permission='yes';
					return $permission;
				}
			} */


		//}
		//暂时数据不归属用户组
	/* 	elseif($recOwnType == 'Groups')
		{
			//Checking if the record owner is the current user's group
			if(in_array($recOwnId,$current_user_groups))
			{
				$permission='yes';
				//$log->debug("Exiting isPermitted method ...");
				return $permission;
			}
		} */

		//Checking for Default Org Sharing permission
		//默认公共模块
		/* if($others_permission_id == 0){
			if($actionid == 1 || $actionid == 0){
				if($module == 'Calendar'){
					if($recOwnType == 'Users'){
						$permission = isCalendarPermittedBySharing($record_id);
					}else{
						$permission='no';
					}
				}else{
					$permission = isReadWritePermittedBySharing($module,$tabid,$actionid,$record_id);
				}
				return $permission;
			}elseif($actionid == 2){
				return 'no';
			}else{
				return 'yes';
			}
		}elseif($others_permission_id == 1){
			if($actionid == 2){
				return 'no';
			}else{
				return 'yes';
			}
		}elseif($others_permission_id == 2){
			return 'yes';
		}elseif($others_permission_id == 3){
			if($actionid == 3 || $actionid == 4){
				if($module == 'Calendar'){
					if($recOwnType == 'Users'){
						$permission = isCalendarPermittedBySharing($record_id);
					}else{
						$permission='no';
					}
				}else{
					$permission = isReadPermittedBySharing($module,$tabid,$actionid,$record_id);
				}
				return $permission;
			}elseif($actionid ==0 || $actionid ==1){
				if($module == 'Calendar'){
					$permission='no';
				}else{
					$permission = isReadWritePermittedBySharing($module,$tabid,$actionid,$record_id);
				}
				return $permission;
			}elseif($actionid ==2){
					return 'no';
			}else{
				return 'yes';
			}
		}else{
			$permission = "yes";
		} */
	}else {
		return 'no';
	}
	return $permission;
}

/** Function to check if the currently logged in user has Read Access due to Sharing for the specified record
  * @param $module -- Module Name:: Type varchar
  * @param $actionid -- Action Id:: Type integer
  * @param $recordid -- Record Id:: Type integer
  * @param $tabid -- Tab Id:: Type integer
  * @returns yes or no. If Yes means this action is allowed for the currently logged in user. If no means this action is not allowed for the currently logged in user
 */
function isReadPermittedBySharing($module,$tabid,$actionid,$record_id)
{
	global $log;
	$log->debug("Entering isReadPermittedBySharing(".$module.",".$tabid.",".$actionid.",".$record_id.") method ...");
	global $adb;
	global $current_user;
	require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
	$ownertype='';
	$ownerid='';
	$sharePer='no';

	$sharingModuleList=getSharingModuleList();
	if(! in_array($module,$sharingModuleList))
	{
		$sharePer='no';
		return $sharePer;
	}

	$recordOwnerArr=getRecordOwnerId($record_id);
	foreach($recordOwnerArr as $type=>$id)
	{
		$ownertype=$type;
		$ownerid=$id;
	}

	$varname=$module."_share_read_permission";
	$read_per_arr=$$varname;
	if($ownertype == 'Users')
	{
		//Checking the Read Sharing Permission Array in Role Users
		$read_role_per=$read_per_arr['ROLE'];
		foreach($read_role_per as $roleid=>$userids)
		{
			if(in_array($ownerid,$userids))
			{
				$sharePer='yes';
				$log->debug("Exiting isReadPermittedBySharing method ...");
				return $sharePer;
			}

		}

		//Checking the Read Sharing Permission Array in Groups Users
		$read_grp_per=$read_per_arr['GROUP'];
		foreach($read_grp_per as $grpid=>$userids)
		{
			if(in_array($ownerid,$userids))
			{
				$sharePer='yes';
				$log->debug("Exiting isReadPermittedBySharing method ...");
				return $sharePer;
			}

		}

	}
	elseif($ownertype == 'Groups')
	{
		$read_grp_per=$read_per_arr['GROUP'];
		if(array_key_exists($ownerid,$read_grp_per))
		{
			$sharePer='yes';
			$log->debug("Exiting isReadPermittedBySharing method ...");
			return $sharePer;
		}
	}

	//Checking for the Related Sharing Permission
	$relatedModuleArray=$related_module_share[$tabid];
	if(is_array($relatedModuleArray))
	{
		foreach($relatedModuleArray as $parModId)
		{
			$parRecordOwner=getParentRecordOwner($tabid,$parModId,$record_id);
			if(sizeof($parRecordOwner) > 0)
			{
				$parModName=getTabname($parModId);
				$rel_var=$parModName."_".$module."_share_read_permission";
				$read_related_per_arr=$$rel_var;
				$rel_owner_type='';
				$rel_owner_id='';
				foreach($parRecordOwner as $rel_type=>$rel_id)
				{
					$rel_owner_type=$rel_type;
					$rel_owner_id=$rel_id;
				}
				if($rel_owner_type=='Users')
				{
					//Checking in Role Users
					$read_related_role_per=$read_related_per_arr['ROLE'];
					foreach($read_related_role_per as $roleid=>$userids)
					{
						if(in_array($rel_owner_id,$userids))
						{
							$sharePer='yes';
							$log->debug("Exiting isReadPermittedBySharing method ...");
							return $sharePer;
						}

					}
					//Checking in Group Users
					$read_related_grp_per=$read_related_per_arr['GROUP'];
					foreach($read_related_grp_per as $grpid=>$userids)
					{
						if(in_array($rel_owner_id,$userids))
						{
							$sharePer='yes';
							$log->debug("Exiting isReadPermittedBySharing method ...");
							return $sharePer;
						}

					}

				}
				elseif($rel_owner_type=='Groups')
				{
					$read_related_grp_per=$read_related_per_arr['GROUP'];
					if(array_key_exists($rel_owner_id,$read_related_grp_per))
					{
						$sharePer='yes';
						$log->debug("Exiting isReadPermittedBySharing method ...");
						return $sharePer;
					}

				}
			}
		}
	}
	$log->debug("Exiting isReadPermittedBySharing method ...");
	return $sharePer;
}



/** Function to check if the currently logged in user has Write Access due to Sharing for the specified record
  * @param $module -- Module Name:: Type varchar
  * @param $actionid -- Action Id:: Type integer
  * @param $recordid -- Record Id:: Type integer
  * @param $tabid -- Tab Id:: Type integer
  * @returns yes or no. If Yes means this action is allowed for the currently logged in user. If no means this action is not allowed for the currently logged in user
 */
function isReadWritePermittedBySharing($module,$tabid,$actionid,$record_id)
{
	global $log;
	$log->debug("Entering isReadWritePermittedBySharing(".$module.",".$tabid.",".$actionid.",".$record_id.") method ...");
	global $adb;
	global $current_user;
	require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
	$ownertype='';
	$ownerid='';
	$sharePer='no';

	$sharingModuleList=getSharingModuleList();
        if(! in_array($module,$sharingModuleList))
        {
                $sharePer='no';
                return $sharePer;
        }

	$recordOwnerArr=getRecordOwnerId($record_id);
	foreach($recordOwnerArr as $type=>$id)
	{
		$ownertype=$type;
		$ownerid=$id;
	}

	$varname=$module."_share_write_permission";
	$write_per_arr=$$varname;

	if($ownertype == 'Users')
	{
		//Checking the Write Sharing Permission Array in Role Users
		$write_role_per=$write_per_arr['ROLE'];
		foreach($write_role_per as $roleid=>$userids)
		{
			if(in_array($ownerid,$userids))
			{
				$sharePer='yes';
				$log->debug("Exiting isReadWritePermittedBySharing method ...");
				return $sharePer;
			}

		}
		//Checking the Write Sharing Permission Array in Groups Users
		$write_grp_per=$write_per_arr['GROUP'];
		foreach($write_grp_per as $grpid=>$userids)
		{
			if(in_array($ownerid,$userids))
			{
				$sharePer='yes';
				$log->debug("Exiting isReadWritePermittedBySharing method ...");
				return $sharePer;
			}

		}

	}
	elseif($ownertype == 'Groups')
	{
		$write_grp_per=$write_per_arr['GROUP'];
		if(array_key_exists($ownerid,$write_grp_per))
		{
			$sharePer='yes';
			$log->debug("Exiting isReadWritePermittedBySharing method ...");
			return $sharePer;
		}
	}
	//Checking for the Related Sharing Permission
	$relatedModuleArray=$related_module_share[$tabid];
	if(is_array($relatedModuleArray))
	{
		foreach($relatedModuleArray as $parModId)
		{
			$parRecordOwner=getParentRecordOwner($tabid,$parModId,$record_id);
			if(sizeof($parRecordOwner) > 0)
			{
				$parModName=getTabname($parModId);
				$rel_var=$parModName."_".$module."_share_write_permission";
				$write_related_per_arr=$$rel_var;
				$rel_owner_type='';
				$rel_owner_id='';
				foreach($parRecordOwner as $rel_type=>$rel_id)
				{
					$rel_owner_type=$rel_type;
					$rel_owner_id=$rel_id;
				}
				if($rel_owner_type=='Users')
				{
					//Checking in Role Users
					$write_related_role_per=$write_related_per_arr['ROLE'];
					foreach($write_related_role_per as $roleid=>$userids)
					{
						if(in_array($rel_owner_id,$userids))
						{
							$sharePer='yes';
							$log->debug("Exiting isReadWritePermittedBySharing method ...");
							return $sharePer;
						}

					}
					//Checking in Group Users
					$write_related_grp_per=$write_related_per_arr['GROUP'];
					foreach($write_related_grp_per as $grpid=>$userids)
					{
						if(in_array($rel_owner_id,$userids))
						{
							$sharePer='yes';
							$log->debug("Exiting isReadWritePermittedBySharing method ...");
							return $sharePer;
						}

					}

				}
				elseif($rel_owner_type=='Groups')
				{
					$write_related_grp_per=$write_related_per_arr['GROUP'];
					if(array_key_exists($rel_owner_id,$write_related_grp_per))
					{
						$sharePer='yes';
						$log->debug("Exiting isReadWritePermittedBySharing method ...");
						return $sharePer;
					}

				}
			}
		}
	}

	$log->debug("Exiting isReadWritePermittedBySharing method ...");
	return $sharePer;
}

/** Function to get the Profile Global Information for the specified vtiger_profileid
  * @param $profileid -- Profile Id:: Type integer
  * @returns Profile Gloabal Permission Array in the following format:
  * $profileGloblaPermisson=Array($viewall_actionid=>permission, $editall_actionid=>permission)
 */
function getProfileGlobalPermission($profileid)
{
global $log;
$log->debug("Entering getProfileGlobalPermission(".$profileid.") method ...");
  global $adb;
  $sql = "select * from vtiger_profile2globalpermissions where profileid=?" ;
  $result = $adb->pquery($sql, array($profileid));
  $num_rows = $adb->num_rows($result);

  for($i=0; $i<$num_rows; $i++)
  {
	$act_id = $adb->query_result($result,$i,"globalactionid");
	$per_id = $adb->query_result($result,$i,"globalactionpermission");
	$copy[$act_id] = $per_id;
  }

	$log->debug("Exiting getProfileGlobalPermission method ...");
   return $copy;

}

/** Function to get the Profile Tab Permissions for the specified vtiger_profileid
  * @param $profileid -- Profile Id:: Type integer
  * @returns Profile Tabs Permission Array in the following format:
  * $profileTabPermisson=Array($tabid1=>permission, $tabid2=>permission,........., $tabidn=>permission)
 */
function getProfileTabsPermission($profileid)
{
global $log;
$log->debug("Entering getProfileTabsPermission(".$profileid.") method ...");
  global $adb;
  $sql = "select * from vtiger_profile2tab where profileid=?" ;
  $result = $adb->pquery($sql, array($profileid));
  $num_rows = $adb->num_rows($result);
  
  $copy = array();
  for($i=0; $i<$num_rows; $i++)
  {
	$tab_id = $adb->query_result($result,$i,"tabid");
	$per_id = $adb->query_result($result,$i,"permissions");
	$copy[$tab_id] = $per_id;
  }
  // TODO This is temporarily required, till we provide a hook/entry point for Emails module.
  // Once that is done, Webmails need to be removed permanently.
  $emailsTabId = getTabid('Emails');
  $webmailsTabid = getTabid('Webmails');
  if(array_key_exists($emailsTabId, $copy)) {
	  $copy[$webmailsTabid] = $copy[$emailsTabId];
  }

$log->debug("Exiting getProfileTabsPermission method ...");
   return $copy;

}


/** Function to get the Profile Action Permissions for the specified vtiger_profileid
  * @param $profileid -- Profile Id:: Type integer
  * @returns Profile Tabs Action Permission Array in the following format:
  *    $tabActionPermission = Array($tabid1=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission),
  *                        $tabid2=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission),
  *                                |
  *                        $tabidn=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission))
 */
function getProfileActionPermission($profileid)
{
global $log;
$log->debug("Entering getProfileActionPermission(".$profileid.") method ...");
	global $adb;
	$check = Array();
	$temp_tabid = Array();
	$sql1 = "select * from vtiger_profile2standardpermissions where profileid=?";
	$result1 = $adb->pquery($sql1, array($profileid));
        $num_rows1 = $adb->num_rows($result1);
        for($i=0; $i<$num_rows1; $i++)
        {
		$tab_id = $adb->query_result($result1,$i,'tabid');
		if(! in_array($tab_id,$temp_tabid))
		{
			$temp_tabid[] = $tab_id;
			$access = Array();
		}

		$action_id = $adb->query_result($result1,$i,'operation');
		$per_id = $adb->query_result($result1,$i,'permissions');
		$access[$action_id] = $per_id;
		$check[$tab_id] = $access;


	}


$log->debug("Exiting getProfileActionPermission method ...");
	return $check;
}



/** Function to get the Standard and Utility Profile Action Permissions for the specified vtiger_profileid
  * @param $profileid -- Profile Id:: Type integer
  * @returns Profile Tabs Action Permission Array in the following format:
  *    $tabActionPermission = Array($tabid1=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission),
  *                        $tabid2=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission),
  *                                |
  *                        $tabidn=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission))
 */
function getProfileAllActionPermission($profileid)
{
global $log;
$log->debug("Entering getProfileAllActionPermission(".$profileid.") method ...");
	global $adb;
	$actionArr=getProfileActionPermission($profileid);
	$utilArr=getTabsUtilityActionPermission($profileid);
	foreach($utilArr as $tabid=>$act_arr)
	{
		$act_tab_arr=$actionArr[$tabid];
		foreach($act_arr as $utilid=>$util_perr)
		{
			$act_tab_arr[$utilid]=$util_perr;
		}
		$actionArr[$tabid]=$act_tab_arr;
	}
$log->debug("Exiting getProfileAllActionPermission method ...");
	return $actionArr;
}

/** Function to get all  the vtiger_role information
  * @returns $allRoleDetailArray-- Array will contain the details of all the vtiger_roles. RoleId will be the key:: Type array
 */
function getAllRoleDetails()
{
global $log;
$log->debug("Entering getAllRoleDetails() method ...");
	global $adb;
	$role_det = Array();
	$query = "select * from vtiger_role";
	$result = $adb->pquery($query, array());
	$num_rows=$adb->num_rows($result);
	for($i=0; $i<$num_rows;$i++)
	{
		$each_role_det = Array();
		$roleid=$adb->query_result($result,$i,'roleid');
		$rolename=$adb->query_result($result,$i,'rolename');
		$roledepth=$adb->query_result($result,$i,'depth');
		$sub_roledepth=$roledepth + 1;
		$parentrole=$adb->query_result($result,$i,'parentrole');
		$sub_role='';

		//getting the immediate subordinates
		$query1="select * from vtiger_role where parentrole like ? and depth=?";
		$res1 = $adb->pquery($query1, array($parentrole."::%", $sub_roledepth));
		$num_roles = $adb->num_rows($res1);
		if($num_roles > 0)
		{
			for($j=0; $j<$num_roles; $j++)
			{
				if($j == 0)
				{
					$sub_role .= $adb->query_result($res1,$j,'roleid');
				}
				else
				{
					$sub_role .= ','.$adb->query_result($res1,$j,'roleid');
				}
			}
		}


		$each_role_det[]=$rolename;
		$each_role_det[]=$roledepth;
		$each_role_det[]=$sub_role;
		$role_det[$roleid]=$each_role_det;

	}
	$log->debug("Exiting getAllRoleDetails method ...");
	return $role_det;
}

/** Function to get the vtiger_role information of the specified vtiger_role
  * @param $roleid -- RoleId :: Type varchar
  * @returns $roleInfoArray-- RoleInfoArray in the following format:
  *       $roleInfo=Array($roleId=>Array($rolename,$parentrole,$roledepth,$immediateParent));
 */
function getRoleInformation($roleid)
{
	global $log;
	$log->debug("Entering getRoleInformation(".$roleid.") method ...");
	global $adb;
	$query = "select * from vtiger_role where roleid=?";
	$result = $adb->pquery($query, array($roleid));
	$rolename=$adb->query_result($result,0,'rolename');
	$parentrole=$adb->query_result($result,0,'parentrole');
	$roledepth=$adb->query_result($result,0,'depth');
	$parentRoleArr=explode('::',$parentrole);
	$immediateParent=$parentRoleArr[sizeof($parentRoleArr)-2];
	$roleDet=Array();
	$roleDet[]=$rolename;
	$roleDet[]=$parentrole;
	$roleDet[]=$roledepth;
	$roleDet[]=$immediateParent;
	$roleInfo=Array();
	$roleInfo[$roleid]=$roleDet;
	$log->debug("Exiting getRoleInformation method ...");
	return $roleInfo;
}
/**返回部门信息根据部门id
 * @param unknown $departmentid
 * @return multitype:multitype:unknown Ambigous <s, --, string, mixed>
 */
function getDepartmentInformation($departmentid){
	global  $log;
	$log->debug("执行方法getDepartmentInformation($departmentid)...");
	global  $adb;
	$query	=	"select * from vtiger_departments where departmentid=?";
	$result	=	$adb->pquery($query,array($departmentid));
	$departmentname	=	$adb->query_result($result, 0,'departmentname');
	$parentdepartment	=	$adb->query_result($result, 0,'parentdepartment');
	$departmentdepth	=	$adb->query_result($result,0,'depth');
	$parentRoleArr		=	explode('::', $parentdepartment);
	$immediateParent	=	$parentRoleArr[sizeof($parentRoleArr)-2];
	$departmentDet		=	array();
	$departmentDet[]	=	$departmentname;
	$departmentDet[]	=	$parentdepartment;
	$departmentDet[]	=	$departmentdepth;
	$departmentDet[]	=	$immediateParent;
	$departmentInfo		=	array();
	$departmentInfo[$departmentid]	=	$departmentDet;
	$log->debug("存在方法getDepartmentInformation");
	return $departmentInfo;
}

/** Function to get the vtiger_role related vtiger_users
  * @param $roleid -- RoleId :: Type varchar
  * @returns $roleUsers-- Role Related User Array in the following format:
  *       $roleUsers=Array($userId1=>$userName,$userId2=>$userName,........,$userIdn=>$userName));
 */
function getRoleUsers($roleId)
{
	global $log;
	$log->debug("Entering getRoleUsers(".$roleId.") method ...");
	global $adb;
	$query = "select vtiger_user2role.*,vtiger_users.* from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid where roleid=?";
	$result = $adb->pquery($query, array($roleId));
	$num_rows=$adb->num_rows($result);
	$roleRelatedUsers=Array();
	for($i=0; $i<$num_rows; $i++)
	{
		$roleRelatedUsers[$adb->query_result($result,$i,'userid')]=getFullNameFromQResult($result, $i, 'Users');
	}
	$log->debug("Exiting getRoleUsers method ...");
	return $roleRelatedUsers;


}


/** Function to get the vtiger_role related user ids
  * @param $roleid -- RoleId :: Type varchar
  * @returns $roleUserIds-- Role Related User Array in the following format:
  *       $roleUserIds=Array($userId1,$userId2,........,$userIdn);
 */

function getRoleUserIds($roleId)
{
	global $log;
	$log->debug("Entering getRoleUserIds(".$roleId.") method ...");
	global $adb;
	$query = "select vtiger_user2role.*,vtiger_users.user_name from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid where roleid=?";
	$result = $adb->pquery($query, array($roleId));
	$num_rows=$adb->num_rows($result);
	$roleRelatedUsers=Array();
	for($i=0; $i<$num_rows; $i++)
	{
		$roleRelatedUsers[]=$adb->query_result($result,$i,'userid');
	}
	$log->debug("Exiting getRoleUserIds method ...");
	return $roleRelatedUsers;


}
/**
 * 返回本部门以及下属部门的人员id
 * @param string $deparmentId
 * @return array 
 */
function getDepartmentUserIds($deparmentId){
	global $log;
	$log->debug("Entering getDepartmentUserIds(".$deparmentId.") method ...");
	global $adb;
	$query = "select vtiger_user2department.* from vtiger_user2department INNER JOIN(select * from vtiger_departments where parentdepartment LIKE(select CONCAT(parentdepartment,'::%') from vtiger_departments where departmentid=?) or departmentid=?) vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid";
	$result = $adb->pquery($query, array($deparmentId,$deparmentId));
	$num_rows=$adb->num_rows($result);
	$roleRelatedUsers=Array();
	for($i=0; $i<$num_rows; $i++)
	{
	$roleRelatedUsers[]=$adb->query_result($result,$i,'userid');
	}
	$log->debug("Exiting getRoleUserIds method ...");
	return $roleRelatedUsers;
}
/** Function to get the vtiger_role and subordinate vtiger_users
  * @param $roleid -- RoleId :: Type varchar
  * @returns $roleSubUsers-- Role and Subordinates Related Users Array in the following format:
  *       $roleSubUsers=Array($userId1=>$userName,$userId2=>$userName,........,$userIdn=>$userName));
 */
function getRoleAndSubordinateUsers($roleId)
{
	global $log;
	$log->debug("Entering getRoleAndSubordinateUsers(".$roleId.") method ...");
	global $adb;
	$roleInfoArr=getRoleInformation($roleId);
	$parentRole=$roleInfoArr[$roleId][1];
	$query = "select vtiger_user2role.*,vtiger_users.user_name from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like ?";
	$result = $adb->pquery($query, array($parentRole."%"));
	$num_rows=$adb->num_rows($result);
	$roleRelatedUsers=Array();
	for($i=0; $i<$num_rows; $i++)
	{
		$roleRelatedUsers[$adb->query_result($result,$i,'userid')]=$adb->query_result($result,$i,'user_name');
	}
	$log->debug("Exiting getRoleAndSubordinateUsers method ...");
	return $roleRelatedUsers;


}

/** Function to get the vtiger_role and subordinate Information for the specified vtiger_roleId
  * @param $roleid -- RoleId :: Type varchar
  * @returns $roleSubInfo-- Role and Subordinates Information array in the following format:
  *       $roleSubInfo=Array($roleId1=>Array($rolename,$parentrole,$roledepth,$immediateParent), $roleId2=>Array($rolename,$parentrole,$roledepth,$immediateParent),.....);
 */
function getRoleAndSubordinatesInformation($roleId)
{
	global $log;
	$log->debug("Entering getRoleAndSubordinatesInformation(".$roleId.") method ...");
	global $adb;
	static $roleInfoCache = array();
	if(!empty($roleInfoCache[$roleId])) {
		return $roleInfoCache[$roleId];
	}
	$roleDetails=getRoleInformation($roleId);
	$roleInfo=$roleDetails[$roleId];
	$roleParentSeq=$roleInfo[1];

	$query="select * from vtiger_role where parentrole like ? order by parentrole asc";
	$result=$adb->pquery($query, array($roleParentSeq."%"));
	$num_rows=$adb->num_rows($result);
	$roleInfo=Array();
	for($i=0;$i<$num_rows;$i++)
	{
		$roleid=$adb->query_result($result,$i,'roleid');
                $rolename=$adb->query_result($result,$i,'rolename');
                $roledepth=$adb->query_result($result,$i,'depth');
                $parentrole=$adb->query_result($result,$i,'parentrole');
		$roleDet=Array();
		$roleDet[]=$rolename;
		$roleDet[]=$parentrole;
		$roleDet[]=$roledepth;
		$roleInfo[$roleid]=$roleDet;

	}
	$roleInfoCache[$roleId] = $roleInfo;
	$log->debug("Exiting getRoleAndSubordinatesInformation method ...");
	return $roleInfo;

}


/** Function to get the vtiger_role and subordinate vtiger_role ids
  * @param $roleid -- RoleId :: Type varchar
  * @returns $roleSubRoleIds-- Role and Subordinates RoleIds in an Array in the following format:
  *       $roleSubRoleIds=Array($roleId1,$roleId2,........,$roleIdn);
 */
function getRoleAndSubordinatesRoleIds($roleId)
{
	global $log;
	$log->debug("Entering getRoleAndSubordinatesRoleIds(".$roleId.") method ...");
	global $adb;
	$roleDetails=getRoleInformation($roleId);
	$roleInfo=$roleDetails[$roleId];
	$roleParentSeq=$roleInfo[1];

	$query="select * from vtiger_role where parentrole like ? order by parentrole asc";
	$result=$adb->pquery($query, array($roleParentSeq."%"));
	$num_rows=$adb->num_rows($result);
	$roleInfo=Array();
	for($i=0;$i<$num_rows;$i++)
	{
		$roleid=$adb->query_result($result,$i,'roleid');
		$roleInfo[]=$roleid;

	}
	$log->debug("Exiting getRoleAndSubordinatesRoleIds method ...");
	return $roleInfo;

}

/** Function to delete the vtiger_role related sharing rules
  * @param $roleid -- RoleId :: Type varchar
 */
function deleteRoleRelatedSharingRules($roleId)
{
	global $log;
	$log->debug("Entering deleteRoleRelatedSharingRules(".$roleId.") method ...");
        global $adb;
        $dataShareTableColArr=Array('vtiger_datashare_grp2role'=>'to_roleid',
                                    'vtiger_datashare_grp2rs'=>'to_roleandsubid',
                                    'vtiger_datashare_role2group'=>'share_roleid',
                                    'vtiger_datashare_role2role'=>'share_roleid::to_roleid',
                                    'vtiger_datashare_role2rs'=>'share_roleid::to_roleandsubid',
                                    'vtiger_datashare_rs2grp'=>'share_roleandsubid',
                                    'vtiger_datashare_rs2role'=>'share_roleandsubid::to_roleid',
                                    'vtiger_datashare_rs2rs'=>'share_roleandsubid::to_roleandsubid');

        foreach($dataShareTableColArr as $tablename=>$colname)
        {
                $colNameArr=explode('::',$colname);
                $query="select shareid from ".$tablename." where ".$colNameArr[0]."=?";
				$params = array($roleId);
                if(sizeof($colNameArr) >1)
                {
                        $query .=" or ".$colNameArr[1]."=?";
						array_push($params, $roleId);
                }

                $result=$adb->pquery($query, $params);
                $num_rows=$adb->num_rows($result);
                for($i=0;$i<$num_rows;$i++)
                {
                        $shareid=$adb->query_result($result,$i,'shareid');
                        deleteSharingRule($shareid);
                }

        }
	$log->debug("Exiting deleteRoleRelatedSharingRules method ...");
}

/** Function to delete the group related sharing rules
  * @param $roleid -- RoleId :: Type varchar
 */
function deleteGroupRelatedSharingRules($grpId)
{
	global $log;
	$log->debug("Entering deleteGroupRelatedSharingRules(".$grpId.") method ...");

        global $adb;
        $dataShareTableColArr=Array('vtiger_datashare_grp2grp'=>'share_groupid::to_groupid',
                                    'vtiger_datashare_grp2role'=>'share_groupid',
                                    'vtiger_datashare_grp2rs'=>'share_groupid',
                                    'vtiger_datashare_role2group'=>'to_groupid',
                                    'vtiger_datashare_rs2grp'=>'to_groupid');


        foreach($dataShareTableColArr as $tablename=>$colname)
        {
                $colNameArr=explode('::',$colname);
                $query="select shareid from ".$tablename." where ".$colNameArr[0]."=?";
				$params = array($grpId);
                if(sizeof($colNameArr) >1)
                {
                        $query .=" or ".$colNameArr[1]."=?";
						array_push($params, $grpId);
                }

                $result=$adb->pquery($query, $params);
                $num_rows=$adb->num_rows($result);
                for($i=0;$i<$num_rows;$i++)
                {
                        $shareid=$adb->query_result($result,$i,'shareid');
                        deleteSharingRule($shareid);
                }

        }
	$log->debug("Exiting deleteGroupRelatedSharingRules method ...");
}


/** Function to get userid and username of all vtiger_users
  * @returns $userArray -- User Array in the following format:
  * $userArray=Array($userid1=>$username, $userid2=>$username,............,$useridn=>$username);
 */
function getAllUserName()
{
	global $log;
	$log->debug("Entering getAllUserName() method ...");
	global $adb;
	$query="select * from vtiger_users where deleted=0";
	$result = $adb->pquery($query, array());
	$num_rows=$adb->num_rows($result);
	$user_details=Array();
	for($i=0;$i<$num_rows;$i++)
	{
		$userid=$adb->query_result($result,$i,'id');
		$username=getFullNameFromQResult($result, $i, 'Users');
		$user_details[$userid]=$username;

	}
	$log->debug("Exiting getAllUserName method ...");
	return $user_details;

}


/** Function to get groupid and groupname of all vtiger_groups
  * @returns $grpArray -- Group Array in the following format:
  * $grpArray=Array($grpid1=>$grpname, $grpid2=>$grpname,............,$grpidn=>$grpname);
 */
function getAllGroupName()
{
	global $log;
	$log->debug("Entering getAllGroupName() method ...");
	global $adb;
	$query="select * from vtiger_groups";
	$result = $adb->pquery($query, array());
	$num_rows=$adb->num_rows($result);
	$group_details=Array();
	for($i=0;$i<$num_rows;$i++)
	{
		$grpid=$adb->query_result($result,$i,'groupid');
		$grpname=$adb->query_result($result,$i,'groupname');
		$group_details[$grpid]=$grpname;

	}
	$log->debug("Exiting getAllGroupName method ...");
	return $group_details;

}

/** This function is to delete the organisation level sharing rule
  * It takes the following input parameters:
  *     $shareid -- Id of the Sharing Rule to be updated
  */
function deleteSharingRule($shareid)
{
	global $log;
	$log->debug("Entering deleteSharingRule(".$shareid.") method ...");
	global $adb;
	$query2="select * from vtiger_datashare_module_rel where shareid=?";
	$res=$adb->pquery($query2, array($shareid));
	$typestr=$adb->query_result($res,0,'relationtype');
	$tabname=getDSTableNameForType($typestr);
	$query3="delete from $tabname where shareid=?";
	$adb->pquery($query3, array($shareid));
	$query4="delete from vtiger_datashare_module_rel where shareid=?";
	$adb->pquery($query4, array($shareid));

	//deleting the releated module sharing permission
	$query5="delete from vtiger_datashare_relatedmodule_permission where shareid=?";
	$adb->pquery($query5, array($shareid));
	$log->debug("Exiting deleteSharingRule method ...");

}

/** Function get the Data Share Table Names
 *  @returns the following Date Share Table Name Array:
 *  $dataShareTableColArr=Array('GRP::GRP'=>'datashare_grp2grp',
 * 				    'GRP::ROLE'=>'datashare_grp2role',
 *				    'GRP::RS'=>'datashare_grp2rs',
 *				    'ROLE::GRP'=>'datashare_role2group',
 *				    'ROLE::ROLE'=>'datashare_role2role',
 *				    'ROLE::RS'=>'datashare_role2rs',
 *				    'RS::GRP'=>'datashare_rs2grp',
 *				    'RS::ROLE'=>'datashare_rs2role',
 *				    'RS::RS'=>'datashare_rs2rs');
 */
function getDataShareTableName()
{
	global $log;
	$log->debug("Entering getDataShareTableName() method ...");
	$dataShareTableColArr=Array('GRP::GRP'=>'vtiger_datashare_grp2grp',
				    'GRP::ROLE'=>'vtiger_datashare_grp2role',
				    'GRP::RS'=>'vtiger_datashare_grp2rs',
				    'ROLE::GRP'=>'vtiger_datashare_role2group',
				    'ROLE::ROLE'=>'vtiger_datashare_role2role',
				    'ROLE::RS'=>'vtiger_datashare_role2rs',
				    'RS::GRP'=>'vtiger_datashare_rs2grp',
				    'RS::ROLE'=>'vtiger_datashare_rs2role',
				    'RS::RS'=>'vtiger_datashare_rs2rs');
	$log->debug("Exiting getDataShareTableName method ...");
	return $dataShareTableColArr;

}

/** Function to get the Data Share Table Name from the speciified type string
 *  @param $typeString -- Datashare Type Sting :: Type Varchar
 *  @returns Table Name -- Type Varchar
 *
 */
function getDSTableNameForType($typeString)
{
	global $log;
	$log->debug("Entering getDSTableNameForType(".$typeString.") method ...");
	$dataShareTableColArr=getDataShareTableName();
	$tableName=$dataShareTableColArr[$typeString];
	$log->debug("Exiting getDSTableNameForType method ...");
	return $tableName;

}

/** This function is to retreive the vtiger_profiles associated with the  the specified user
  * It takes the following input parameters:
  *     $userid -- The User Id:: Type Integer
  *This function will return the vtiger_profiles associated to the specified vtiger_users in an Array in the following format:
  *     $userProfileArray=(profileid1,profileid2,profileid3,...,profileidn);
  */
function getUserProfile($userId)
{
	global $log;
	$log->debug("Entering getUserProfile(".$userId.") method ...");
	global $adb;
	$roleId=fetchUserRole($userId);
	$profArr=Array();
	$sql1 = "select profileid from vtiger_role2profile where roleid=?";
    $result1 = $adb->pquery($sql1, array($roleId));
	$num_rows=$adb->num_rows($result1);
	for($i=0;$i<$num_rows;$i++)
	{

        	$profileid=  $adb->query_result($result1,$i,"profileid");
		$profArr[]=$profileid;
	}
		$log->debug("Exiting getUserProfile method ...");
        return $profArr;

}

/** To retreive the global permission of the specifed user from the various vtiger_profiles associated with the user
  * @param $userid -- The User Id:: Type Integer
  * @returns  user global permission  array in the following format:
  *     $gloabalPerrArray=(view all action id=>permission,
			   edit all action id=>permission)							);
  */
function getCombinedUserGlobalPermissions($userId)
{
	global $log;
	$log->debug("Entering getCombinedUserGlobalPermissions(".$userId.") method ...");
	global $adb;
	$profArr=getUserProfile($userId);
	$no_of_profiles=sizeof($profArr);
	$userGlobalPerrArr=Array();

	$userGlobalPerrArr=getProfileGlobalPermission($profArr[0]);
	if($no_of_profiles != 1)
	{
			for($i=1;$i<$no_of_profiles;$i++)
		{
			$tempUserGlobalPerrArr=getProfileGlobalPermission($profArr[$i]);

			foreach($userGlobalPerrArr as $globalActionId=>$globalActionPermission)
			{
				if($globalActionPermission == 1)
				{
					$now_permission = $tempUserGlobalPerrArr[$globalActionId];
					if($now_permission == 0)
					{
						$userGlobalPerrArr[$globalActionId]=$now_permission;
					}


				}

			}

		}

	}

	$log->debug("Exiting getCombinedUserGlobalPermissions method ...");
	return $userGlobalPerrArr;

}

/** To retreive the vtiger_tab permissions of the specifed user from the various vtiger_profiles associated with the user
  * @param $userid -- The User Id:: Type Integer
  * @returns  user global permission  array in the following format:
  *     $tabPerrArray=(tabid1=>permission,
  *			   tabid2=>permission)							);
  */
function getCombinedUserTabsPermissions($userId)
{
	global $log;
	$log->debug("Entering getCombinedUserTabsPermissions(".$userId.") method ...");
	global $adb;
	$profArr=getUserProfile($userId);
	$no_of_profiles=sizeof($profArr);
	$userTabPerrArr=Array();

	$userTabPerrArr=getProfileTabsPermission($profArr[0]);
	if($no_of_profiles != 1)
	{
		for($i=1;$i<$no_of_profiles;$i++)
		{
			$tempUserTabPerrArr=getProfileTabsPermission($profArr[$i]);

			foreach($userTabPerrArr as $tabId=>$tabPermission)
			{
				if($tabPermission == 1)
				{
					$now_permission = $tempUserTabPerrArr[$tabId];
					if($now_permission == 0)
					{
						$userTabPerrArr[$tabId]=$now_permission;
					}


				}

			}

		}

	}

    $homeTabid = getTabid('Home');
    if(!array_key_exists($homeTabid, $userTabPerrArr)) {
        $userTabPerrArr[$homeTabid] = 0;
    }
	$log->debug("Exiting getCombinedUserTabsPermissions method ...");
	return $userTabPerrArr;

}

/** To retreive the vtiger_tab acion permissions of the specifed user from the various vtiger_profiles associated with the user
  * @param $userid -- The User Id:: Type Integer
  * @returns  user global permission  array in the following format:
  *     $actionPerrArray=(tabid1=>permission,
  *			   tabid2=>permission);
 */
function getCombinedUserActionPermissions($userId)
{
	global $log;
	$log->debug("Entering getCombinedUserActionPermissions(".$userId.") method ...");
	global $adb;
	$profArr=getUserProfile($userId);
	$no_of_profiles=sizeof($profArr);
	$actionPerrArr=Array();

	$actionPerrArr=getProfileAllActionPermission($profArr[0]);
	if($no_of_profiles != 1)
	{
		for($i=1;$i<$no_of_profiles;$i++)
		{
			$tempActionPerrArr=getProfileAllActionPermission($profArr[$i]);

			foreach($actionPerrArr as $tabId=>$perArr)
			{
				foreach($perArr as $actionid=>$per)
				{
					if($per == 1)
					{
						$now_permission = $tempActionPerrArr[$tabId][$actionid];
						if($now_permission == 0)
						{
							$actionPerrArr[$tabId][$actionid]=$now_permission;
						}


					}
				}

			}

		}

	}
	$log->debug("Exiting getCombinedUserActionPermissions method ...");
	return $actionPerrArr;

}

/** To retreive the parent vtiger_role of the specified vtiger_role
  * @param $roleid -- The Role Id:: Type varchar
  * @returns  parent vtiger_role array in the following format:
  *     $parentRoleArray=(roleid1,roleid2,.......,roleidn);
 */
function getParentRole($roleId)
{
	global $log;
	$log->debug("Entering getParentRole(".$roleId.") method ...");
	$roleInfo=getRoleInformation($roleId);
	$parentRole=$roleInfo[$roleId][1];
	$tempParentRoleArr=explode('::',$parentRole);
	$parentRoleArr=Array();
	foreach($tempParentRoleArr as $role_id)
	{
		if($role_id != $roleId)
		{
			$parentRoleArr[]=$role_id;
		}
	}
	$log->debug("Exiting getParentRole method ...");
	return $parentRoleArr;

}

/** To retreive the subordinate vtiger_roles of the specified parent vtiger_role
  * @param $roleid -- The Role Id:: Type varchar
  * @returns  subordinate vtiger_role array in the following format:
  *     $subordinateRoleArray=(roleid1,roleid2,.......,roleidn);
 */
function getRoleSubordinates($roleId)
{
	global $log;
	$log->debug("Entering getRoleSubordinates(".$roleId.") method ...");

	// Look at cache first for information
	$roleSubordinates = VTCacheUtils::lookupRoleSubordinates($roleId);

	if($roleSubordinates === false) {
		global $adb;
		$roleDetails=getRoleInformation($roleId);
		$roleInfo=$roleDetails[$roleId];
		$roleParentSeq=$roleInfo[1];

		$query="select * from vtiger_role where parentrole like ? order by parentrole asc";
		$result=$adb->pquery($query, array($roleParentSeq."::%"));
		$num_rows=$adb->num_rows($result);
		$roleSubordinates=Array();
		for($i=0;$i<$num_rows;$i++)
		{
			$roleid=$adb->query_result($result,$i,'roleid');

			$roleSubordinates[]=$roleid;

		}
		// Update cache for re-use
		VTCacheUtils::updateRoleSubordinates($roleId, $roleSubordinates);
	}

	$log->debug("Exiting getRoleSubordinates method ...");
	return $roleSubordinates;

}

/** To retreive the subordinate vtiger_roles and vtiger_users of the specified parent vtiger_role
  * @param $roleid -- The Role Id:: Type varchar
  * @returns  subordinate vtiger_role array in the following format:
  *     $subordinateRoleUserArray=(roleid1=>Array(userid1,userid2,userid3),
                               vtiger_roleid2=>Array(userid1,userid2,userid3)
				                |
						|
			       vtiger_roleidn=>Array(userid1,userid2,userid3));
 */
function getSubordinateRoleAndUsers($roleId)
{
	global $log;
	$log->debug("Entering getSubordinateRoleAndUsers(".$roleId.") method ...");
	global $adb;
	$subRoleAndUsers=Array();
	$subordinateRoles=getRoleSubordinates($roleId);
	foreach($subordinateRoles as $subRoleId)
	{
		$userArray=getRoleUsers($subRoleId);
		$subRoleAndUsers[$subRoleId]=$userArray;

	}
	$log->debug("Exiting getSubordinateRoleAndUsers method ...");
	return $subRoleAndUsers;

}

function getCurrentUserProfileList()
{
	global $log;
	$log->debug("Entering getCurrentUserProfileList() method ...");
        global $current_user;
        require('user_privileges/user_privileges_'.$current_user->id.'.php');
        $profList = array();
        $i=0;
        foreach ($current_user_profiles as $profid)
        {
           array_push($profList, $profid);
                $i++;
        }
	$log->debug("Exiting getCurrentUserProfileList method ...");
        return $profList;

}


function getCurrentUserGroupList()
{
	global $log;
	$log->debug("Entering getCurrentUserGroupList() method ...");
        global $current_user;
        require('user_privileges/user_privileges_'.$current_user->id.'.php');
	$grpList= array();
	if(sizeof($current_user_groups) > 0)
	{
       	 	$i=0;
        	foreach ($current_user_groups as $grpid)
        	{
                	array_push($grpList, $grpid);
                	$i++;
        	}
	}
	$log->debug("Exiting getCurrentUserGroupList method ...");
       	 return $grpList;
}

function getWriteSharingGroupsList($module)
{
	global $log;
	$log->debug("Entering getWriteSharingGroupsList(".$module.") method ...");
	global $adb;
	global $current_user;
	$grp_array=Array();
	$tabid=getTabid($module);
	$query = "select sharedgroupid from vtiger_tmp_write_group_sharing_per where userid=? and tabid=?";
	$result=$adb->pquery($query, array($current_user->id, $tabid));
	$num_rows=$adb->num_rows($result);
	for($i=0;$i<$num_rows;$i++)
	{
		$grp_id=$adb->query_result($result,$i,'sharedgroupid');
		$grp_array[]=$grp_id;
	}
	$shareGrpList=constructList($grp_array,'INTEGER');
	$log->debug("Exiting getWriteSharingGroupsList method ...");
	return $shareGrpList;
}

function constructList($array,$data_type)
{
	global $log;
	$log->debug("Entering constructList(".$array.",".$data_type.") method ...");
	$list= array();
	if(sizeof($array) > 0)
	{
		$i=0;
		foreach($array as $value)
		{
			if($data_type == "INTEGER")
			{
				array_push($list, $value);
			}
			elseif($data_type == "VARCHAR")
			{
				array_push($list, "'".$value."'");
			}
			$i++;
		}
	}
	$log->debug("Exiting constructList method ...");
	return $list;
}

function getListViewSecurityParameter($module)
{
	global $log;
	$log->debug("Entering getListViewSecurityParameter(".$module.") method ...");
	global $adb;

	$tabid=getTabid($module);
	global $current_user;
	if($current_user)
	{
        	require('user_privileges/user_privileges_'.$current_user->id.'.php');
        	require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
	}
	if($module == 'Leads')
	{
		$sec_query .= " and (
						vtiger_crmentity.smownerid in($current_user->id)
						or vtiger_crmentity.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".$current_user_parent_role_seq."::%')
						or vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".$current_user->id." and tabid=".$tabid.")
						or (";

                        if(sizeof($current_user_groups) > 0)
                        {
                              $sec_query .= " vtiger_groups.groupid in (". implode(",", $current_user_groups) .") or ";
                        }
                         $sec_query .= " vtiger_groups.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".$current_user->id." and tabid=".$tabid."))) ";
	}
	elseif($module == 'Accounts')
	{
		$sec_query .= " and (vtiger_crmentity.smownerid in($current_user->id) " .
				"or vtiger_crmentity.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".$current_user_parent_role_seq."::%') " .
				"or vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".$current_user->id." and tabid=".$tabid.") or (";

                if(sizeof($current_user_groups) > 0)
                {
                	$sec_query .= " vtiger_groups.groupid in (". implode(",", $current_user_groups) .") or ";
                }
		$sec_query .= " vtiger_groups.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".$current_user->id." and tabid=".$tabid."))) ";

	}
	elseif($module == 'Contacts')
	{
		$sec_query .= " and (vtiger_crmentity.smownerid in($current_user->id) " .
				"or vtiger_crmentity.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".$current_user_parent_role_seq."::%') " .
				"or vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".$current_user->id." and tabid=".$tabid.") or (";

                if(sizeof($current_user_groups) > 0)
                {
                	$sec_query .= " vtiger_groups.groupid in (". implode(",", $current_user_groups) .") or ";
                }
		$sec_query .= " vtiger_groups.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".$current_user->id." and tabid=".$tabid."))) ";

	}
	elseif($module == 'Potentials')
	{
		$sec_query .= " and (vtiger_crmentity.smownerid in($current_user->id) " .
				"or vtiger_crmentity.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".$current_user_parent_role_seq."::%') " .
				"or vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".$current_user->id." and tabid=".$tabid.")";

		$sec_query .= " or (";

        if(sizeof($current_user_groups) > 0)
        {
        	$sec_query .= " vtiger_groups.groupid in (". implode(",", $current_user_groups) .") or ";
        }
		$sec_query .= " vtiger_groups.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".$current_user->id." and tabid=".$tabid."))) ";

	}
	elseif($module == 'HelpDesk')
	{
		$sec_query .= " and (vtiger_crmentity.smownerid in($current_user->id) or vtiger_crmentity.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".$current_user_parent_role_seq."::%') or vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".$current_user->id." and tabid=".$tabid.") ";

		$sec_query .= " or (";
                if(sizeof($current_user_groups) > 0)
                {
                	$sec_query .= " vtiger_groups.groupid in (". implode(",", $current_user_groups) .") or ";
                }
		$sec_query .= " vtiger_groups.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".$current_user->id." and tabid=".$tabid."))) ";

	}
	elseif($module == 'Emails')
	{
		$sec_query .= " and vtiger_crmentity.smownerid=".$current_user->id." ";

	}
	elseif($module == 'Calendar')
	{
		require_once('modules/Calendar/CalendarCommon.php');
		$shared_ids = getSharedCalendarId($current_user->id);
		if(isset($shared_ids) && $shared_ids != '')
			$condition = " or (vtiger_crmentity.smownerid in($shared_ids) and vtiger_activity.visibility = 'Public')";
		else
			$condition = null;
		$sec_query .= " and (vtiger_crmentity.smownerid in($current_user->id) $condition or vtiger_crmentity.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".$current_user_parent_role_seq."::%')";

		if(sizeof($current_user_groups) > 0)
		{
			$sec_query .= " or ((vtiger_groups.groupid in (". implode(",", $current_user_groups) .")))";
		}
		$sec_query .= ")";
	}
	elseif($module == 'Quotes')
	{
		$sec_query .= " and (vtiger_crmentity.smownerid in($current_user->id) or vtiger_crmentity.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".$current_user_parent_role_seq."::%') or vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".$current_user->id." and tabid=".$tabid.")";

		//Adding crteria for group sharing
		 $sec_query .= " or ((";

                if(sizeof($current_user_groups) > 0)
                {
                	$sec_query .= " vtiger_groups.groupid in (". implode(",", $current_user_groups) .") or ";
                }
		$sec_query .= " vtiger_groups.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".$current_user->id." and tabid=".$tabid.")))) ";

	}
	elseif($module == 'PurchaseOrder')
	{
		$sec_query .= " and (vtiger_crmentity.smownerid in($current_user->id) or vtiger_crmentity.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".$current_user_parent_role_seq."::%') or vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".$current_user->id." and tabid=".$tabid.") or (";

                if(sizeof($current_user_groups) > 0)
                {
                	$sec_query .= " vtiger_groups.groupid in (". implode(",", $current_user_groups) .") or ";
                }
		$sec_query .= " vtiger_groups.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".$current_user->id." and tabid=".$tabid."))) ";

	}
	elseif($module == 'SalesOrder')
	{
		$sec_query .= " and (vtiger_crmentity.smownerid in($current_user->id) or vtiger_crmentity.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".$current_user_parent_role_seq."::%') or vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".$current_user->id." and tabid=".$tabid.")";

		//Adding crteria for group sharing
		 $sec_query .= " or (";

                if(sizeof($current_user_groups) > 0)
                {
                	$sec_query .= " vtiger_groups.groupid in (". implode(",", $current_user_groups) .") or ";
                }
		$sec_query .= " vtiger_groups.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".$current_user->id." and tabid=".$tabid."))) ";

	}
	elseif($module == 'Invoice')
	{
		$sec_query .= " and (vtiger_crmentity.smownerid in($current_user->id) or vtiger_crmentity.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".$current_user_parent_role_seq."::%') or vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".$current_user->id." and tabid=".$tabid.")";

		//Adding crteria for group sharing
		 $sec_query .= " or ((";

                if(sizeof($current_user_groups) > 0)
                {
                	$sec_query .= " vtiger_groups.groupid in (". implode(",", $current_user_groups) .") or ";
                }
		$sec_query .= " vtiger_groups.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".$current_user->id." and tabid=".$tabid.")))) ";

	}
	elseif($module == 'Campaigns')
	{

		$sec_query .= " and (vtiger_crmentity.smownerid in($current_user->id) or vtiger_crmentity.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".$current_user_parent_role_seq."::%') or vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".$current_user->id." and tabid=".$tabid.") or ((";

		if(sizeof($current_user_groups) > 0)
		{
			$sec_query .= " vtiger_groups.groupid in (". implode(",", $current_user_groups) .") or ";
		}
		$sec_query .= " vtiger_groups.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".$current_user->id." and tabid=".$tabid.")))) ";


	}

	elseif($module == 'Documents')
	{
		$sec_query .= " and (vtiger_crmentity.smownerid in($current_user->id) or vtiger_crmentity.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".$current_user_parent_role_seq."::%') or vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".$current_user->id." and tabid=".$tabid.") or ((";

                if(sizeof($current_user_groups) > 0)
                {
                	$sec_query .= " vtiger_groups.groupid in (". implode(",", $current_user_groups) .") or ";
                }
		$sec_query .= " vtiger_groups.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".$current_user->id." and tabid=".$tabid.")))) ";

	}

	elseif($module == 'Products')
	{
		$sec_query .= " and (vtiger_crmentity.smownerid in($current_user->id) " .
				"or vtiger_crmentity.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".$current_user_parent_role_seq."::%') " .
				"or vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".$current_user->id." and tabid=".$tabid.")";

		$sec_query .= " or (";

        if(sizeof($current_user_groups) > 0)
        {
        	$sec_query .= " vtiger_groups.groupid in (". implode(",", $current_user_groups) .") or ";
        }
		$sec_query .= " vtiger_groups.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".$current_user->id." and tabid=".$tabid."))) ";

	}

	else
	{
		$modObj = CRMEntity::getInstance($module);
		$sec_query = $modObj->getListViewSecurityParameter($module);

	}
	$log->debug("Exiting getListViewSecurityParameter method ...");
	return $sec_query;
}

function get_current_user_access_groups($module)
{
	global $log;
	$log->debug("Entering get_current_user_access_groups(".$module.") method ...");
	global $adb,$noof_group_rows;
	$current_user_group_list=getCurrentUserGroupList();
	$sharing_write_group_list=getWriteSharingGroupsList($module);
	$query ="select groupname,groupid from vtiger_groups";
	$params = array();
	if(count($current_user_group_list) > 0 && count($sharing_write_group_list) > 0)
	{
		$query .= " where (groupid in (". generateQuestionMarks($current_user_group_list) .") or groupid in (". generateQuestionMarks($sharing_write_group_list) ."))";
		array_push($params, $current_user_group_list, $sharing_write_group_list);
		$result = $adb->pquery($query, $params);
		$noof_group_rows=$adb->num_rows($result);
	}
	elseif(count($current_user_group_list) > 0)
	{
		$query .= " where groupid in (". generateQuestionMarks($current_user_group_list) .")";
		array_push($params, $current_user_group_list);
		$result = $adb->pquery($query, $params);
		$noof_group_rows=$adb->num_rows($result);
	}
	elseif(count($sharing_write_group_list) > 0)
	{
		$query .= " where groupid in (". generateQuestionMarks($sharing_write_group_list) .")";
		array_push($params, $sharing_write_group_list);
		$result = $adb->pquery($query, $params);
		$noof_group_rows=$adb->num_rows($result);
	}
	$log->debug("Exiting get_current_user_access_groups method ...");
	return $result;
}
/** Function to get the Group Id for a given group groupname
 *  @param $groupname -- Groupname
 *  @returns Group Id -- Type Integer
 */

function getGrpId($groupname)
{
	global $log;
	$log->debug("Entering getGrpId(".$groupname.") method ...");
	global $adb;
    $groupid = Vtiger_Cache::get('group',$groupname);
    if(!$groupid && $groupid !== 0){
        $result = $adb->pquery("select groupid from vtiger_groups where groupname=?", array($groupname));
        $groupid = ($adb->num_rows($result) > 0) ? $adb->query_result($result,0,'groupid') : 0;
        Vtiger_Cache::set('group',$groupname,$groupid);
    }
	$log->debug("Exiting getGrpId method ...");
	return $groupid;
}

/** Function to check permission to access a vtiger_field for a given user
  * @param $fld_module -- Module :: Type String
  * @param $userid -- User Id :: Type integer
  * @param $fieldname -- Field Name :: Type varchar
  * @returns $rolename -- Role Name :: Type varchar
  *
 */
function getFieldVisibilityPermission($fld_module, $userid, $fieldname, $accessmode='readonly')
{
    global $log;
	$log->debug("Entering getFieldVisibilityPermission(".$fld_module.",". $userid.",". $fieldname.") method ...");

    global $adb;
    global $current_user;

    // Check if field is in-active
	$fieldActive = isFieldActive($fld_module,$fieldname);
	if($fieldActive == false) {
        return '1';
    }

	require('user_privileges/user_privileges_'.$userid.'.php');

    /* Asha: Fix for ticket #4508. Users with View all and Edit all permission will also have visibility permission for all fields */
	if($is_admin || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] ==0)
	{
        $log->debug("Exiting getFieldVisibilityPermission method ...");
        return '0';
	}
	else
	{
        //get vtiger_profile list using userid
        $profilelist = getCurrentUserProfileList();

        //get tabid
        $tabid = getTabid($fld_module);

            if (count($profilelist) > 0) {
			if($accessmode == 'readonly') {
				$query="SELECT vtiger_profile2field.visible FROM vtiger_field INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid WHERE vtiger_field.tabid=? AND vtiger_profile2field.visible=0 AND vtiger_def_org_field.visible=0  AND vtiger_profile2field.profileid in (". generateQuestionMarks($profilelist) .") AND vtiger_field.fieldname= ? and vtiger_field.presence in (0,2) GROUP BY vtiger_field.fieldid";
                } else {
				$query="SELECT vtiger_profile2field.visible FROM vtiger_field INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid WHERE vtiger_field.tabid=? AND vtiger_profile2field.visible=0 AND vtiger_profile2field.readonly=0 AND vtiger_def_org_field.visible=0  AND vtiger_profile2field.profileid in (". generateQuestionMarks($profilelist) .") AND vtiger_field.fieldname= ? and vtiger_field.presence in (0,2) GROUP BY vtiger_field.fieldid";
                }
                $params = array($tabid, $profilelist, $fieldname);

            } else {
			if($accessmode == 'readonly') {
				$query="SELECT vtiger_profile2field.visible FROM vtiger_field INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid WHERE vtiger_field.tabid=? AND vtiger_profile2field.visible=0 AND vtiger_def_org_field.visible=0  AND vtiger_field.fieldname= ? and vtiger_field.presence in (0,2) GROUP BY vtiger_field.fieldid";
                } else {
				$query="SELECT vtiger_profile2field.visible FROM vtiger_field INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid WHERE vtiger_field.tabid=? AND vtiger_profile2field.visible=0 AND vtiger_profile2field.readonly=0 AND vtiger_def_org_field.visible=0  AND vtiger_field.fieldname= ? and vtiger_field.presence in (0,2) GROUP BY vtiger_field.fieldid";
                }
                $params = array($tabid, $fieldname);
            }
            //Postgres 8 fixes
 		if( $adb->dbType == "pgsql")
 		    $query = fixPostgresQuery( $query, $log, 0);


            $result = $adb->pquery($query, $params);

            $log->debug("Exiting getFieldVisibilityPermission method ...");

            // Returns value as a string
		if($adb->num_rows($result) == 0) return '1';
		return ($adb->query_result($result,"0","visible")."");
        }
    }

/** Function to check permission to access the column for a given user
 * @param $userid -- User Id :: Type integer
 * @param $tablename -- tablename :: Type String
 * @param $columnname -- columnname :: Type String
 * @param $module -- Module Name :: Type varchar
 */
function getColumnVisibilityPermission($userid, $columnname, $module, $accessmode='readonly')
{
	global $adb,$log;
	$log->debug("in function getcolumnvisibilitypermission $columnname -$userid");
	$tabid = getTabid($module);

	// Look at cache if information is available.
	$cacheFieldInfo = VTCacheUtils::lookupFieldInfoByColumn($tabid, $columnname);
	$fieldname = false;
	if($cacheFieldInfo === false) {
		$res = $adb->pquery("select fieldname from vtiger_field where tabid=? and columnname=? and vtiger_field.presence in (0,2)", array($tabid, $columnname));
		$fieldname = $adb->query_result($res, 0, 'fieldname');
	} else {
		$fieldname = $cacheFieldInfo['fieldname'];
	}

	return getFieldVisibilityPermission($module,$userid,$fieldname,$accessmode);
}

/** 
 * 权限Function to get the permitted module name Array with presence as 0
 * @returns permitted module name Array :: Type Array
 *
 */
function getPermittedModuleNames()
{
	global $log;
	$log->debug("Entering getPermittedModuleNames() method ...");
	global $current_user;
	$permittedModules=Array();
	require('user_privileges/user_privileges_'.$current_user->id.'.php');
	include('tabdata.php');
	
	//查找有权限的module
	if($is_admin == false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1)
	{
		foreach($tab_seq_array as $tabid=>$seq_value)
		{
			if($seq_value === 0 && $profileTabsPermission[$tabid] === 0)
			{
				$permittedModules[]=getTabModuleName($tabid);
			}

		}


	}
	else//管理员权限
	{
		foreach($tab_seq_array as $tabid=>$seq_value)
		{
			if($seq_value === 0)
			{
				$permittedModules[]=getTabModuleName($tabid);
			}

		}
	}
	$log->debug("Exiting getPermittedModuleNames method ...");
	return $permittedModules;
}


/**
 * Function to get the permitted module id Array with presence as 0
 * @global Users $current_user
 * @return Array Array of accessible tabids.
 */
function getPermittedModuleIdList() {
	global $current_user;
	$permittedModules=Array();
	require('user_privileges/user_privileges_'.$current_user->id.'.php');
	include('tabdata.php');

	if($is_admin == false && $profileGlobalPermission[1] == 1 &&
			$profileGlobalPermission[2] == 1) {
		foreach($tab_seq_array as $tabid=>$seq_value) {
			if($seq_value === 0 && $profileTabsPermission[$tabid] === 0) {
				$permittedModules[]=($tabid);
			}
		}
	} else {
		foreach($tab_seq_array as $tabid=>$seq_value) {
			if($seq_value === 0) {
				$permittedModules[]=($tabid);
			}
		}
	}
    $homeTabid = getTabid('Home');
    if(!in_array($homeTabid, $permittedModules)) {
        $permittedModules[] = $homeTabid;
    }
	return $permittedModules;
}

/** Function to recalculate the Sharing Rules for all the vtiger_users
  * This function will recalculate all the sharing rules for all the vtiger_users in the Organization and will write them in flat vtiger_files
  *
 */
function RecalculateSharingRules()
{
	global $log;
	$log->debug("Entering RecalculateSharingRules() method ...");
	global $adb;
	require_once('modules/Users/CreateUserPrivilegeFile.php');
	$query="select id from vtiger_users where deleted=0";
	$result=$adb->pquery($query, array());
	$num_rows=$adb->num_rows($result);
	for($i=0;$i<$num_rows;$i++)
	{
		$id=$adb->query_result($result,$i,'id');
		createUserPrivilegesfile($id);
	        createUserSharingPrivilegesfile($id);
	}
	$log->debug("Exiting RecalculateSharingRules method ...");

}

/** Function to get the list of module for which the user defined sharing rules can be defined
  * @returns Array:: Type array
  *
  */
function getSharingModuleList($eliminateModules=false)
{
	global $log;

	$sharingModuleArray = Array();

	global $adb;
	if(empty($eliminateModules)) $eliminateModules = Array();

	// Module that needs to be eliminated explicitly
	if(!in_array('Calendar', $eliminateModules)) $eliminateModules[] = 'Calendar';
	if(!in_array('Events', $eliminateModules)) $eliminateModules[] = 'Events';

	$query = "SELECT name FROM vtiger_tab WHERE presence=0 AND ownedby = 0 AND isentitytype = 1";
	$query .= " AND name NOT IN('" . implode("','", $eliminateModules) . "')";

	$result = $adb->query($query);
	while($resrow = $adb->fetch_array($result)) {
		$sharingModuleArray[] = $resrow['name'];
	}

	return $sharingModuleArray;
}


function isCalendarPermittedBySharing($recordId)
{
	global $adb;
	global $current_user;
	$permission = 'no';
	$query = "select * from vtiger_sharedcalendar where userid in(select smownerid from vtiger_activity inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_activity.activityid where activityid=? and visibility='Public' and smownerid !=0) and sharedid=?";
	$result=$adb->pquery($query, array($recordId, $current_user->id));
	if($adb->num_rows($result) >0)
	{
		$permission = 'yes';
	}
	return $permission;
}

/** Function to check if the field is Active
 *  @params  $modulename -- Module Name :: String Type
 *   		 $fieldname  -- Field Name  :: String Type
 */
function isFieldActive($modulename,$fieldname){
	$fieldid = getFieldid(getTabid($modulename), $fieldname, true);
	return ($fieldid !== false);
}

/**
 *
 * @param String $module - module name for which query needs to be generated.
 * @param Users $user - user for which query needs to be generated.
 * @return String Access control Query for the user.
 */
function getNonAdminAccessControlQuery($module,$user,$scope=''){
	$instance = CRMEntity::getInstance($module);
	return $instance->getNonAdminAccessControlQuery($module,$user,$scope);
}

function appendFromClauseToQuery($query,$fromClause) {
	$query = preg_replace('/\s+/', ' ', $query);
	$condition = substr($query, strripos($query,' where '),strlen($query));
	$newQuery = substr($query, 0, strripos($query,' where '));
	$query = $newQuery.$fromClause.$condition;
	return $query;
}

//
/**
 * 返回权限控制,
 * 当前url地址调用方法 getAccessibleUsers();
 * 非当前的模块访问权限getAccessibleUsers(Module,View)
 * 	1.young.yang 2015-1-12 增加非当前模块的数据和权限访问
 * @param string $module 模块
 * @param string $view
 * @param string $all 默认返回拼接查询条件in or eq，为真时返回人员
 * @param string $record
 * @throws AppException
 * @return string|Ambigous <string, --, unknown>
 */
function getAccessibleUsers($module='',$view='',$all=false,$record='',$user=''){
	global $currentModule;
	global $currentView;
	global $currentAction;
	global $current_user;
	if($user){
	        $current_user = $user;
	}
	$userlist=array();
	$datasource=$current_user->datasource;
	$role=$current_user->current_user_roles;
	if(!empty($currentAction) && empty($currentView)){
		$currentView=$currentAction;
	}
	//young.yang 验证非当前访问的页面url地址，为里面调用的url地址
	if(empty($view)){
		$view=$currentView;
	}
	if(empty($module)){
		$module=$currentModule;
	}
	//end
	if($view=='List' || $view=='Detail' || $view=='Boxs' || $view=='ConvertLead'){
		$view='DetailView';
	}elseif($view=='Edit'){
		if(!empty($record)){
			$view='Editview';
		}else{
			$view='Save';
		}
	}
	if(!empty($datasource)){
		require('crmcache/departmentanduserinfo.php');
		foreach ($datasource as $datas) {
			if(!empty($datas[$module])){
				foreach ($datas[$module] as $departmentid=>$permission) {
				if(isset($user2departmentinfo[$departmentid])){
					$userid=explode(',', $user2departmentinfo[$departmentid]);
					foreach ($userid as $value) {
						$userlist[$value]=0;
					}
				}
				}
			}
		}
		if(!empty($userlist)){
			$userlist=array_keys($userlist);
		}
		
	}
	if(!empty($current_user->companysource)){
		require('crmcache/user2company.php');
		$user2companydata=array();
		foreach ($current_user->companysource as $datas) {
			if(!empty($datas[$module])){
				foreach ($datas[$module] as $user2companyid=>$permission) {
					if(isset($user2company[$permission])){
						$user2companytemp=explode(',', $user2company[$permission]);
						$user2companydata=array_merge($user2companydata,$user2companytemp);
					}
				}
			}
		}
		$userlist=array_merge($userlist,$user2companydata);
	}
	$action=str_replace('Ajax','',$module.'/'.$view);
	if(!$current_user->superadmin){
		$limit=$current_user->viewPermission[$action];

		if($action=='JobAlerts/DetailView' && empty($limit)){

			$limit=1;
		}
		if(empty($limit) && $all==false){
			throw new AppException(vtranslate($currentModule).' '.vtranslate('LBL_NOT_ACCESSIBLE'));
			exit;
		}
		if($limit==1){
			$user[]=$current_user->id;	
		}elseif($limit==2){
			$user=$current_user->subordinate_users;
		}elseif($limit==3){
			if(!empty($current_user->subordinate_users)){
				$user=$current_user->subordinate_users;
				//2015-1-6 数组问题
			}
			$user[]=$current_user->id;
		}elseif($limit==4){
			$userdepartmentuser=empty($current_user->departmentuser)?0:$current_user->departmentuser;
			$user=explode(',',$userdepartmentuser);	
		}elseif($limit==5){
			$sysuser=empty($current_user->sysuser)?0:$current_user->sysuser;
			$user=explode(',',$sysuser);	
		}elseif($limit==6){
			return '1=1';
		}elseif($limit==7){
			include("crmcache/user2company.php");
			$sysuser=is_numeric($current_user->companyid)?$user2company[$current_user->companyid]:-1;
			$user=explode(',',$sysuser);
		}
		//跨部门共享数据权限
		//$departmentshare=Vtiger_Cache::get('zdcrm_','departmentshare');
		//require("crmcache/departmentshare.php");
		//if(!empty($departmentshare[$module]) && !empty($departmentshare[$module][$role])){
		//	$user[]=$departmentshare[$module][$role];
		//}
		/* if(empty($user) && $all==false ){
			throw new AppException(vtranslate($module).' '.vtranslate('LBL_NOT_ACCESSIBLE'));
			exit;
		} */
	}else{
		return '1=1';
	}
		$user=array_merge($userlist,$user);
    	$user=array_unique($user);
		if($all==false){
			if(empty($user[0])){
				return ' in (\'no\')';
			}
            $count=count($user);
			if($count==1){
				$user='= '.$user[0];
			}else{
				$user=' in ('.implode(',',$user).')';
			}
		}else{
            if(empty($user[0])){
                $user=array(0);
            }
		}
		/* elseif(empty($user)){
			return '1=1';
		} */
		return $user;	
}

/**
 * 按主体公司走权限
 * @param $fieid 关联的字段名称
 * @param string $module //关联模块
 * @param bool $Relation //是否关联表
 * @param int $record //主体ID
 * @param string $companycode //公司编号
 * @return string
 */
function getAccessibleCompany($fieid,$module='ServiceContracts',$Relation=true,$record=-1,$companycode='X-X-X',$getcompancode=false,$view='List'){
	global $currentModule;
	global $currentView;
	global $currentAction;
	global $current_user;
	if(!empty($currentAction) && empty($currentView)){
		$currentView=$currentAction;
	}
	if(empty($view)){
		$view=$currentView;
	}
	if(empty($module)){
		$module=$currentModule;
	}
	//end
	if($view=='List' || $view=='Detail' || $view=='Boxs' || $view=='ConvertLead'){
		$view='DetailView';
	}elseif($view=='Edit'){
		if(!empty($record)){
			$view='Editview';
		}else{
			$view='Save';
		}
	}
	$companydata=array();
	if(!empty($current_user->companysource)){
		foreach ($current_user->companysource as $datas) {
			if(!empty($datas[$module])){
				foreach ($datas[$module] as $user2companyid=>$permission) {
					$companydata[]=$user2companyid;
				}
			}
		}
	}
	$action=str_replace('Ajax','',$module.'/'.$view);
	$query=' 1=2';
	if(!$current_user->superadmin){
		$limit=$current_user->viewPermission[$action];
		if($limit==7 || !empty($companydata)){
			if($limit==7){
				$companydata[]=$current_user->companycode;
			}
			//$companydata[]=$current_user->companycode;
			if($getcompancode){
				//return "='".$current_user->companycode."'";
				if(!empty($companydata)){
					return " in('".implode("','",$companydata)."')";
				}
				return " in('12trueland21')";//防止为空
			}
			if($record!=-1){
				if($module=='ServiceContracts'){
					$query="SELECT 1 FROM vtiger_servicecontracts WHERE vtiger_servicecontracts.servicecontractsid={$record} AND vtiger_servicecontracts.companycode in('".implode("','",$companydata)."') limit 1";
				}elseif($module=='Newinvoice'){
					$query="SELECT 1 FROM vtiger_newinvoice WHERE vtiger_newinvoice.contractid={$record} AND vtiger_newinvoice.companycode in('".implode("','",$companydata)."')  limit 1";
				}elseif($module=='SupplierContracts'){
					$query="SELECT 1 FROM vtiger_suppliercontracts WHERE vtiger_suppliercontracts.suppliercontractsid={$record} AND vtiger_suppliercontracts.companycode in('".implode("','",$companydata)."')  limit 1";
                }else{
					return false;
				}
				global $adb;
				$result=$adb->pquery($query,array());
				if($adb->num_rows($result)){
					return true;
				}
				return false;
			}
			if($companycode!='X-X-X'){
				return in_array($companycode,$companydata);
				//return $companycode==$current_user->companycode;
			}
			if($Relation){
				if($module=='ServiceContracts'){
					$query="  vtiger_servicecontracts.companycode in('".implode("','",$companydata)."')";
				}elseif($module=='Newinvoice'){
					$query=" vtiger_newinvoice.companycode in('".implode("','",$companydata)."')";
				}elseif($module=='SupplierContracts'){
					$query=" vtiger_suppliercontracts.companycode in('".implode("','",$companydata)."')";
				}
			}else {
                if ($module == 'ServiceContracts') {
                    $query = " EXISTS(SELECT 1 FROM vtiger_servicecontracts WHERE vtiger_servicecontracts.servicecontractsid={$fieid} AND vtiger_servicecontracts.companycode in('" . implode("','", $companydata) . "'))";
                } elseif ($module == 'Newinvoice') {
                    $query = " EXISTS(SELECT 1 FROM vtiger_newinvoice WHERE vtiger_newinvoice.contractid={$fieid} AND vtiger_newinvoice.companycode in('" . implode("','", $companydata) . "'))";
                } elseif ($module == 'SupplierContracts') {
                    $query = " EXISTS(SELECT 1 FROM vtiger_suppliercontracts WHERE vtiger_suppliercontracts.suppliercontractsid={$fieid} AND vtiger_suppliercontracts.companycode in('" . implode("','", $companydata) . "'))";
                }
            }
		}else{
			if($getcompancode){
				return "='1' AND 1=2";
			}
			if($record!=-1 || $companycode!='X-X-X') {
				return false;
			}
		}
	}else{
		if($getcompancode){
			return "='1' OR 1=1";
		}
		if($record!=-1 || $companycode!='X-X-X') {
			return true;
		}
		$query=' 1=1';
	}
	return $query;
}

function LinksisPermitted($currentModule,$currentView){
	global $current_user;
	/* if($currentView=='List' || $currentView=='Detail'){
		$currentView='DetailView';
	} */
	$action=$currentModule.'/'.$currentView;
	if($current_user->is_admin!='on'){
		if(empty($current_user->viewPermission[$action])){
			return false;
		}
	}
	return true;
}

/**
 * 返回当前用户有权限的工单审核角色,包含自己和下级
 * 1.个人，下属，相关，部门，全部
 * @return multitype:
 */
function getWorkflowsByUserid(){
	global $current_user;
	$current_user_roles=$current_user->current_user_roles;
	$second_user_roles=$current_user->second_user_roles;
	require('crmcache/roleandworkflows.php');
	$temp=array();
	/* if(isset($subordinate_roles)&&isset($roleandworkflowsstages)){
		array_push($subordinate_roles, $current_user_roles);
		//print_r($roleandworkflows);
		foreach($subordinate_roles as $role){
			//echo $role;
			if(!empty($roleandworkflows[$role])){
				//array_push($temp,$roleandworkflows[$role]);
				$temp=$temp.rtrim($roleandworkflows[$role],',').',';
			}
		}
	} */
	
	if(isset($roleandworkflowsstages)){
		$temp=isset($roleandworkflowsstages[$current_user_roles])?$roleandworkflowsstages[$current_user_roles].',':'';
		if(!empty($second_user_roles)){
			$secondroles=explode(' |##| ', $second_user_roles);
			foreach($secondroles as $role){
				if(isset($roleandworkflowsstages[$role])){
					//$temp=$temp.rtrim($roleandworkflows[$role],',');
                    $temp=$temp.rtrim($roleandworkflowsstages[$role],',').',';
					//$temp=array_push($temp,$roleandworkflowsstages[$role]);
					
				}
			}
		}	
	}
	return rtrim($temp,',');
}
/**
 * 审核阶段的权限
 */
function getMyWorkflows(){
	global $current_user;
	$userid=$current_user->id;
	checkFileAccessForInclusion('user_privileges/user_privileges_'.$userid.'.php');
	require('user_privileges/user_privileges_'.$userid.'.php');
	require('crmcache/roleandworkflows.php');
	$temp='';
	if(isset($current_user_roles)&&isset($roleandworkflowsstages)){
		if(isset($roleandworkflowsstages[$current_user_roles])){
			$temp=$roleandworkflowsstages[$current_user_roles];
		}
	}
	
	return $temp;
}
/**
 * 1.create(新建),approve(审批),reject(打回),rejectapprove(重新审批),finished(结束),cancelled(取消),refund(退款)
 * delete(删除)=create,reject
 * edit(编辑)=create,reject
 * audit(审核)=approve,rejectapprove
 * noaction(不能任何操作)=finish,cancelled,refund
 * performance(业绩)=(cancelled,refund-),(approve,rejectapprove,finished+)
 * 作废
 */
function getWorkflowsStatus($operation,$status){
	$statuses=array('create','approve','reject','rejectapprove','finished','cancelled','refund');
	$actions=array('delete'=>array('create','reject'),'edit'=>array('create','reject'),'audit'=>array('approve','rejectapprove'),'performance'=>array('+'=>array('approve','rejectapprove','finished'),'-'=>array('cancelled','refund')));
	if(in_array($status,$actions[$operation])){
		return $actions[$operation];
	}
	return null;
}

function getIsEditOrDel($operation,$status){
	//$operation=array('delete','edit','reject');
	if(strstr($status,'a_')){
		return true;
	}
	if(strstr($status,'b_')){
		if($operation=='reject'){
			return true;
		}else{
			return false;
		}
	}else{
		return false;
	}
	
	
}


function getRoles()
{
	include 'crmcache/role.php';
	return $roles;
}

function getDepartment(){
	include 'crmcache/departmentanduserinfo.php';
	return $departlevel;
}
function getDepartmentName(){
	include 'crmcache/departmentanduserinfo.php';
	return $cachedepartment;
}
function getDepartmentNameAndParentName(){
	include 'crmcache/departmentanduserinfo.php';
	return $cacheparentdepartment;
}

function getDepartmentUser($id='H1'){
	include 'crmcache/departmentanduserinfo.php';
	if(empty($user2departmentinfo[$id])){
	
		return array();
	}
	return explode(',',$user2departmentinfo[$id]);
}
/**
 * 部门及下属子部门
 * @param $id
 * @return array
 */
function getChildDepartment($id){
	include 'crmcache/departmentanduserinfo.php';
	if(empty($departmentinfo[$id])){
		return array();
	}
	return $departmentinfo[$id];
}
function getSuperirosDepartments($departmentid){
	$adb = PearDatabase::getInstance();
	$sql = 'select parentdepartment from vtiger_departments where departmentid = ?';
	$departments = $adb->pquery($sql, array($departmentid));
	while ($row = $adb->fetch_array($departments)) {
		$list = $row;
	}
	return explode('::', $list[0]);
}


function authuser_code($type='set',$value=''){
	$ip=md5($_SERVER['REMOTE_ADDR']);
	if($type=='set'){
		$cookie=base64_encode($value);
		$cookie.=base64_encode($ip);
		$cookie=base64_encode($cookie);
		setcookie("tlcrm",$cookie,NULL,NULL,NULL,NULL,true); 
	}else{
		$cookie= $_COOKIE['tlcrm'];
		if(empty($cookie)){
			return false;
		}else{
			$cookie=base64_decode($cookie);
			$cookie=str_replace(base64_encode($ip),'',$cookie);
			return base64_decode($cookie);
		}
	}

}
//获取下属角色信息
function getUsersRole($user){

	if(!empty($user)){
		require('crmcache/roleandworkflows.php');
		$checklist=array();
		global $adb,$current_user;
		$result=$adb->pquery('SELECT roleid FROM `vtiger_user2role` where userid in('.$user.')');
		$num=$adb->num_rows($result);
		for($i=0; $i<$num; $i++){
			$roleid = $adb->query_result($result,$i,'roleid');
			if(!empty($roleandworkflowsstages[$roleid])){
				$checklist[]=$roleandworkflowsstages[$roleid];
			}
			
		}
		//2015-08-22获取第二角色的审核节电
		$second_user_roles=$current_user->second_user_roles;
		if(!empty($second_user_roles)){
			$secondroles=explode(' |##| ', $second_user_roles);
			foreach($secondroles as $role){
				if(isset($roleandworkflowsstages[$role])){
					$checklist[]=$roleandworkflowsstages[$role];
					//$temp=array_push($temp,$roleandworkflowsstages[$role]);
					
				}
			}
		}
		$node=array_unique(explode(',',implode(',', $checklist)));
		return implode(',', $node);
	}
}

//获取负责的产品(包含下属)
function getMyproducts($user){
	if(!empty($user)){
		$user=array_flip(explode(',',$user));
		require('crmcache/user2product.php');
		$checklist='';
		foreach ($user2product as $key => $value) {
			if(isset($user[$key])){
				if(empty($checklist)){
					$checklist=$value;
				}else{
					$checklist.=','.$value;
				}
			}
			
		}
		if(!empty($checklist)){
			$node=array_unique(explode(',',$checklist));
			return implode(',', $node);
		}
	}
	return '';
}


//查询直属汇报对象 By Joe@20050417
function getMyuser($id){
	global $adb;
	$result=$adb->pquery('SELECT id,last_name FROM `vtiger_users` where vtiger_users.status=\'Active\' AND reports_to_id=?',array($id));
	$num=$adb->num_rows($result);
	$list=array();
	for($i=0; $i<$num; $i++){
		$user = $adb->fetchByAssoc($result);
		$list[$user['id']]=$user['last_name'];	
	}
	return $list;
}



function cookiecode($string, $operation = 'DECODE', $key = '', $expiry = 0) {   
    $ckey_length = 4;    
    $key = md5($key ? $key : md5($_SERVER['REMOTE_ADDR']));   
    $keya = md5(substr($key, 0, 16));   
    $keyb = md5(substr($key, 16, 16));   
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
    $cryptkey = $keya.md5($keya.$keyc);   
    $key_length = strlen($cryptkey);    
    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;   
    $string_length = strlen($string);    
    $result = '';   
    $box = range(0, 255);   
    $rndkey = array();   
    for($i = 0; $i <= 255; $i++) {   
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);   
    }     
    for($j = $i = 0; $i < 256; $i++) {   
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;   
        $tmp = $box[$i];   
        $box[$i] = $box[$j];   
        $box[$j] = $tmp;   
    }    
    for($a = $j = $i = 0; $i < $string_length; $i++) {   
        $a = ($a + 1) % 256;   
        $j = ($j + $box[$a]) % 256;   
        $tmp = $box[$a];   
        $box[$a] = $box[$j];   
        $box[$j] = $tmp;   
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));   
    }   
      
    if($operation == 'DECODE') {   
        if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {   
            return substr($result, 26);   
        } else {   
                return '';   
            }   
    } else {   
        return $keyc.str_replace('=', '', base64_encode($result));   
    }   
      
}  

	function setoperate($id,$module,$key='crm',$type='e'){
		if($type=='e'){
			global $current_user;
			return base64_encode(md5($module.$id.$key.$current_user->id));
		}
	}
/**
 * 获取用户的直属下级如果没有下级则返回0
 * @param int $id
 * @param bool $returntype false返回字符串,true为数组
 * @return int
 */
	function getSubordinate($returntype=false,$id=0){
        global $current_user;
        require('crmcache/userunderling.php');
        if($id==0){
            $userdata=empty($underling[$current_user->id])?array(0):$underling[$current_user->id];
		}else{
            $userdata=empty($underling[$id])?array(0):$underling[$id];
		}
		return $returntype?$userdata:implode(',',$userdata);
	}

/**
 *
 * 获取当前用户的所有上级
 * 默认为当前用户的所有上级
 * @param string $id
 * @return array
 */
function getAllSuperiorIds($id=''){
	if(!$id){
		global $current_user;
		$id = $current_user->id;
	}
	$data = array();
	global $adb;
	while (true){
		$result=$adb->pquery('SELECT reports_to_id FROM `vtiger_users` where  id=?',array($id));
		$num = $adb->num_rows($result);
		if($num){
			for($i=0; $i<$num; $i++){
				$user = $adb->fetchByAssoc($result);
				if(!$user['reports_to_id'] || $user['reports_to_id'] == $id){
					return $data;
				}
				$id = $user['reports_to_id'];
				$data[] = $user['reports_to_id'];
			}
		}else{
			return $data;
		}
	}
}

function getDepartmentinfo($departmentid='H1'){
	include 'crmcache/departmentanduserinfo.php';
	return $departmentinfo[$departmentid];
}


/**
 * 判断是不是email
 *
 * @param $email_str
 * @return bool
 */
function is_email( $email_str ) {
    $search = '/^([a-z0-9+_-]+)(.[a-z0-9+_-]+)*@([a-z0-9-]+.)+[a-z]{2,6}$/ix';
	if ( preg_match( $search, $email_str ) ) {
			 return  true ;
	} else {
			 return  false ;
	}
}

/**
 * 判断是不是email
 */
function is_mobile( $text ) {
     $search = '/^0?1[3|4|5|6|7|8][0-9]\d{8}$/';
	 if ( preg_match( $search, $text ) ) {
			return  true ;
	 } else {
			 return  false ;
	 }
}

function getFangXinQianToken(){
    $cache_token=@file_get_contents('./fangxinqiantoken.txt');
    $tokens = json_decode($cache_token,true);
    if($tokens['success'] &&$tokens['timeout']>time()){
        return $tokens['data'];
    }
    global $fangxinqianAppKey,$fangxinqianAppSecrect,$fangxinqiangettokenurl;
    $postData = array(
        "appKey"=>$fangxinqianAppKey,
        "appSecret"=>$fangxinqianAppSecrect
    );
    $curlset=array(
        CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json"
        )
    );
    $result = $this->https_request($fangxinqiangettokenurl."?".http_build_query($postData),"",$curlset);
    $res = json_decode($result,true);
    if(!$res['success']){
        return false;
    }
    $res['timeout'] = time()+55*60;
    file_put_contents('./fangxinqiantoken.txt', json_encode($res));
    return $res['data'];
}

/**
 * 获取用户的信息
 * @param $userid
 */
function getUserInfo($userid){
	global $adb;
	$query='SELECT vtiger_users.*,vtiger_role.*,vtiger_departments.* FROM vtiger_users LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id LEFT JOIN vtiger_user2role ON vtiger_user2role.userid=vtiger_users.id
			LEFT JOIN vtiger_role ON vtiger_user2role.roleid=vtiger_role.roleid LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_user2department.departmentid WHERE vtiger_users.id=?';
	$result=$adb->pquery($query,array($userid));
	return $result->fields;
}

function getFangXinQianFormId(){
    global $fangxinqiangetformidurl;
    $token = $this->getFangXinQianToken();
    $curlset=array(CURLOPT_HTTPHEADER=>array(
        "Content-Type:application/json",
        "token:".$token)
    );
    $result = $this->https_request($fangxinqiangetformidurl, "",$curlset);
    $res = json_decode($result,true);
    if($res['success']){
        return $res['data'];
    }
    return '';
}

?>
