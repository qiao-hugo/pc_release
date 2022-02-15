<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Users_Record_Model extends Vtiger_Record_Model {
    public $logoutURL1='http://192.168.44.156:8080/validate/removeUserToken?userId=';//测试
    public $logoutURL2='https://pretyapi.71360.com/api/app/tcloud-gateway-erp/v1.0.0/gateway/removeUserToken?userId=';//测试
    //public $logoutURL1='http://192.168.7.195:8086/validate/removeUserToken?userId=';//线上
    //public $logoutURL2='https://tyapi.71360.com/api/app/tcloud-gateway-erp/v1.0.0/gateway/removeUserToken?userId=';//线上
	/**
	 * Gets the value of the key . First it will check whether specified key is a property if not it
	 *  will get from normal data attribure from base class
	 * @param <string> $key - property or key name
	 * @return <object>
	 */
	public function get($key) {
		if(property_exists($this, $key)) {	//检查类或者对象是否有该属性
			return $this->$key;
		}
		return parent::get($key);
	}

	/**
	 * Function to get the Detail View url for the record
	 * @return <String> - Record Detail View Url
	 */
	public function getDetailViewUrl() {
		$module = $this->getModule();
		return 'index.php?module='.$this->getModuleName().'&parent=Settings&view='.$module->getDetailViewName().'&record='.$this->getId();
	}

	/**
	 * Function to get the Detail View url for the Preferences page
	 * @return <String> - Record Detail View Url
	 */
	public function getPreferenceDetailViewUrl() {
		$module = $this->getModule();
		return 'index.php?module='.$this->getModuleName().'&view=PreferenceDetail&record='.$this->getId();
	}

	/**
	 * Function to get the url for the Profile page
	 * @return <String> - Profile Url
	 */
	public function getProfileUrl() {
		$module = $this->getModule();
		return 'index.php?module=Users&view=ChangePassword&mode=Profile';
	}

	/**
	 * Function to get the Edit View url for the record
	 * @return <String> - Record Edit View Url
	 */
	public function getEditViewUrl() {
		$module = $this->getModule();
		return 'index.php?module='.$this->getModuleName().'&parent=Settings&view='.$module->getEditViewName().'&record='.$this->getId();
	}

	/**
	 * Function to get the Edit View url for the Preferences page
	 * @return <String> - Record Detail View Url
	 */
	public function getPreferenceEditViewUrl() {
		$module = $this->getModule();
		return 'index.php?module='.$this->getModuleName().'&view=PreferenceEdit&record='.$this->getId();
	}

	/**
	 * Function to get the Delete Action url for the record
	 * @return <String> - Record Delete Action Url
	 */
	public function getDeleteUrl() {
		$module = $this->getModule();
		return 'index.php?module='.$this->getModuleName().'&parent=Settings&view='.$module->getDeleteActionName().'User&record='.$this->getId();
	}

	/**
	 * Function to check whether the user is an Admin user
	 * @return <Boolean> true/false
	 */
	public function isAdminUser() {
		$adminStatus = $this->get('is_admin');
		if ($adminStatus == 'on') {
			return true;
		}
		return false;
	}

	/**
	 * Function to get the module name
	 * @return <String> Module Name
	 */
	public function getModuleName() {
		$module = $this->getModule();
		if($module) {
			return parent::getModuleName();
		}
		//get from the class propety module_name
		return $this->get('module_name');
	}

	/**
	 * Function to save the current Record Model
	 */
	public function save() {
		parent::save();

		$this->saveTagCloud();
	}

	public function saveUserPreferences($userPreferenceData){
		$db = PearDatabase::getInstance();
		$updateQuery = 'UPDATE vtiger_users SET '. ( implode('=?,', array_keys($userPreferenceData)). '=?') . ' WHERE id = ?';
		$updateQueryParams = array_values($userPreferenceData);
		$updateQueryParams[] = $this->getId();
		$db->pquery($updateQuery, $updateQueryParams);
		require_once('modules/Users/CreateUserPrivilegeFile.php');
		createUserPrivilegesfile($this->getId());
	}

	/**
	 * Function to get all the Home Page components list
	 * @return <Array> List of the Home Page components
	 */
	public function getHomePageComponents() {
		$entity = $this->getEntity();
		$homePageComponents = $entity->getHomeStuffOrder($this->getId());
		return $homePageComponents;
	}

	/**
	 * Static Function to get the instance of the User Record model for the current user
	 * 静态方案获取当前用户的用户记录模型实例   module，entity，
	 * @return Users_Record_Model instance
	 */
	protected static $currentUserModels = array();
	public static function getCurrentUserModel() {
		//TODO : Remove the global dependency
		$currentUser = vglobal('current_user');
		if(!empty($currentUser)) {

			// Optimization to avoid object creation every-time
			// Caching is per-id as current_user can get swapped at runtime (ex. workflow)
			$currentUserModel = NULL;
			if (isset(self::$currentUserModels[$currentUser->id])) {
				$currentUserModel = self::$currentUserModels[$currentUser->id];
				//没有这个字段了 用户信息被修改了？
				/* if ($currentUser->column_fields['modifiedtime'] != $currentUserModel->get('modifiedtime')) {
					$currentUserModel = NULL;
				} */
			}
			if (!$currentUserModel) {
				$currentUserModel = self::getInstanceFromUserObject($currentUser);
				self::$currentUserModels[$currentUser->id] = $currentUserModel;
			}

			return $currentUserModel;
		}
		return new self();
	}
    public function getAllProduct($uitype='',$private="",$module = false) {
		return get_product_array();
	}
	/**
	 * Static Function to get the instance of the User Record model from the given Users object
	 * @return Users_Record_Model instance
	 */
	public static function getInstanceFromUserObject($userObject) {


		$objectProperties = get_object_vars($userObject);
		$userModel = new self();
		foreach($objectProperties as $properName=>$propertyValue){
			$userModel->$properName = $propertyValue;
		}
		//print_r($userModel);

		return $userModel->setData($userObject->column_fields)->setModule('Users')->setEntity($userObject);
	}

	/**
	 * Static Function to get the instance of all the User Record models
	 * @return <Array> - List of Users_Record_Model instances
	 */
	public static function getAll($onlyActive=true) {
		$db = PearDatabase::getInstance();

		$sql = 'SELECT id FROM vtiger_users';
		$params = array();
		if($onlyActive) {
			$sql .= ' WHERE status = ?';
			$params[] = 'Active';
		}
		$result = $db->pquery($sql, $params);

		$noOfUsers = $db->num_rows($result);
		$users = array();
		if($noOfUsers > 0) {
			$focus = new Users();
			for($i=0; $i<$noOfUsers; ++$i) {
				$userId = $db->query_result($result, $i, 'id');
				$focus->id = $userId;
				$focus->retrieve_entity_info($userId, 'Users');

				$userModel = self::getInstanceFromUserObject($focus);
				$users[$userModel->getId()] = $userModel;
			}
		}
		return $users;
	}

	/**
	 * Function returns the Subordinate users
	 * @return <Array>
	 */
	function getSubordinateUsers() {
		$privilegesModel = $this->get('privileges');

		if(empty($privilegesModel)) {
			$privilegesModel = Users_Privileges_Model::getInstanceById($this->getId());
			$this->set('privileges', $privilegesModel);
		}

		$subordinateUsers = array();
		$subordinateRoleUsers = $privilegesModel->get('subordinate_roles_users');
		if($subordinateRoleUsers) {
			foreach($subordinateRoleUsers as $role=>$users) {
				foreach($users as $user) {
					$subordinateUsers[$user] = $privilegesModel->get('first_name').' '.$privilegesModel->get('last_name');
				}
			}
		}
		return $subordinateUsers;
	}

	/**
	 * Function returns the Users Parent Role
	 * @return <String>
	 */
	function getParentRoleSequence() {
		$privilegesModel = $this->get('privileges');

		if(empty($privilegesModel)) {
			$privilegesModel = Users_Privileges_Model::getInstanceById($this->getId());
			$this->set('privileges', $privilegesModel);
		}

		return $privilegesModel->get('parent_role_seq');
	}

	/**
	 * Function returns the Users Current Role
	 * @return <String>
	 */
	function getRole() {
		$privilegesModel = $this->get('privileges');

		if(empty($privilegesModel)) {
			$privilegesModel = Users_Privileges_Model::getInstanceById($this->getId());
			$this->set('privileges', $privilegesModel);
		}

		return $privilegesModel->get('roleid');
	}

	/**
	 * Function returns List of Accessible Users for a Module
	 * @param <String> $module
	 * @return <Array of Users_Record_Model>
	 */
	public function getAccessibleUsersForModule($module) {
		$currentUser = Users_Record_Model::getCurrentUserModel();
		$curentUserPrivileges = Users_Privileges_Model::getCurrentUserPrivilegesModel();

		if($currentUser->isAdminUser() || $curentUserPrivileges->hasGlobalWritePermission()) {
			$users = $this->getAccessibleUsers("",$module);
		} else {
			$sharingAccessModel = Settings_SharingAccess_Module_Model::getInstance($module);
			if($sharingAccessModel->isPrivate()) {
				$users = $this->getAccessibleUsers('private',$module);
			} else {
				$users = $this->getAccessibleUsers("",$module);
			}
		}
		return $users;
	}

	/**
	 * Function returns List of Accessible Users for a Module
	 * @param <String> $module
	 * @return <Array of Users_Record_Model>
	 */
	public function getAccessibleGroupForModule($module) {
		$currentUser = Users_Record_Model::getCurrentUserModel();
		$curentUserPrivileges = Users_Privileges_Model::getCurrentUserPrivilegesModel();

		if($currentUser->isAdminUser() || $curentUserPrivileges->hasGlobalWritePermission()) {
			$groups = $this->getAccessibleGroups("",$module);
		} else {
			$sharingAccessModel = Settings_SharingAccess_Module_Model::getInstance($module);
			if($sharingAccessModel->isPrivate()) {
				$groups = $this->getAccessibleGroups('private',$module);
			} else {
				$groups = $this->getAccessibleGroups("",$module);
			}
		}
		return $groups;
	}

	/**
	 * Function to get Images Data
	 * @return <Array> list of Image names and paths
	 */
	public function getImageDetails() {
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$db = PearDatabase::getInstance();

		$imageDetails = array();
		$recordId = $this->getId();
		//young.yang 做什么使用的，慢
		if ($recordId&&1==2) {
			$query = "SELECT vtiger_attachments.* FROM vtiger_attachments
            LEFT JOIN vtiger_salesmanattachmentsrel ON vtiger_salesmanattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid
            WHERE vtiger_salesmanattachmentsrel.smid=?";

			$result = $db->pquery($query, array($recordId));

			$imageId = $db->query_result($result, 0, 'attachmentsid');
			$imagePath = $db->query_result($result, 0, 'path');
			$imageName = $db->query_result($result, 0, 'name');

			//decode_html - added to handle UTF-8 characters in file names
			$imageOriginalName = decode_html($imageName);

			$imageDetails[] = array(
					'id' => $imageId,
					'orgname' => $imageOriginalName,
					'path' => $imagePath.$imageId,
					'name' => $imageName
			);
		}
		return $imageDetails;
	}


    /**
	 * 列出下属用户
	 * @return <Array>
	 */
	public function getAccessibleUsers($uitype='',$private="",$module = false,$departmentid="") {
		//error_reporting(E_ALL);
		//人员多选的场合不设置条件/gaocl 2015-01-13
		if ($uitype =='54' || $uitype == '52'){
			$where="1=1_54";
		}else{
			$where=getAccessibleUsers();
		}

		$accessibleUser = Vtiger_Cache::get('vtiger-'.md5($where),'accessibleusers');

        if(empty($accessibleUser)) {
        	if ($uitype =='54' || $uitype == '52'){
        		$accessibleUser = get_user_department_array($departmentid);
        	}else{
        		$accessibleUser = get_username_array($where);
        	}
			Vtiger_Cache::set('vtiger-'.md5($where), 'accessibleusers',$accessibleUser);
		}

		return $accessibleUser;
	}


	public function getAllDepart(){

		require_once 'crmcache/departmentanduserinfo.php';

		return $cachedepartment;

	}

	/**
	 * Function to get same level and subordinates Users
	 * @return <array> Users
	 */
	public function getSameLevelUsersWithSubordinates(){
		$currentUserRoleModel = Settings_Roles_Record_Model::getInstanceById($this->getRole());
		$sameLevelRoles = $currentUserRoleModel->getSameLevelRoles();
		$sameLevelUsers = $this->getAllUsersOnRoles($sameLevelRoles);
		$subordinateUsers = $this->getRoleBasedSubordinateUsers();
		foreach ($subordinateUsers as $userId => $userName) {
			$sameLevelUsers[$userId] = $userName;
		}
		return $sameLevelUsers;
	}

	/**
	 * Function to get subordinates Users
	 * @return <array> Users
	 */
	public function getRoleBasedSubordinateUsers(){
		$currentUserRoleModel = Settings_Roles_Record_Model::getInstanceById($this->getRole());
		$childernRoles = $currentUserRoleModel->getAllChildren();
		$users = $this->getAllUsersOnRoles($childernRoles);
        $currentUserDetail = array($this->getId() => $this->get('first_name').' '.$this->get('last_name'));
        $users = $currentUserDetail + $users;
        return $users;
	}

	/**
	 * Function to get the users based on Roles
	 * @param type $roles
	 * @return <array>
	 */
	public function getAllUsersOnRoles($roles) {
		$db = PearDatabase::getInstance();
		$roleIds = array();
		foreach ($roles as $key => $role) {
			$roleIds[] = $role->getId();
		}
		$sql = 'SELECT userid FROM vtiger_user2role WHERE roleid IN ('.  generateQuestionMarks($roleIds).')';
		$result = $db->pquery($sql, $roleIds);
		$noOfUsers = $db->num_rows($result);
		$userIds = array();
		$subUsers = array();
		if($noOfUsers > 0) {
			for($i=0; $i<$noOfUsers; ++$i) {
				$userIds[] = $db->query_result($result, $i, 'userid');
			}
			$query = 'SELECT id, first_name, last_name FROM vtiger_users WHERE status = ? AND id IN ('.  generateQuestionMarks($userIds).')';
			$result = $db->pquery($query, array('ACTIVE', $userIds));
			$noOfUsers = $db->num_rows($result);
			for($j=0; $j<$noOfUsers; ++$j) {
				$userId = $db->query_result($result, $j,'id');
				$firstName = $db->query_result($result, $j, 'first_name');
				$lastName = $db->query_result($result, $j, 'last_name');
				$subUsers[$userId] = $firstName .' '.$lastName;
			}
		}
		return $subUsers;
	}

	/**
	 * Function to get all the accessible groups
	 * @return <Array>
	 */
	public function getAccessibleGroups($private="",$module = false) {
		//TODO:Remove dependence on $_REQUEST for the module name in the below API
        $accessibleGroups = Vtiger_Cache::get('vtiger-'.$private, 'accessiblegroups');
        if(!$accessibleGroups){
            $accessibleGroups = get_group_array(false, "ACTIVE", "", $private,$module);
            Vtiger_Cache::set('vtiger-'.$private, 'accessiblegroups',$accessibleGroups);
        }
		return get_group_array(false, "ACTIVE", "", $private);
	}

	/**
	 * Function to get privillage model
	 * @return $privillage model
	 */
	public function getPrivileges() {
		$privilegesModel = $this->get('privileges');

		if (empty($privilegesModel)) {
			$privilegesModel = Users_Privileges_Model::getInstanceById($this->getId());
			$this->set('privileges', $privilegesModel);
		}

		return $privilegesModel;
	}

	/**
	 * Function to get user default activity view
	 * @return <String>
	 */
	public function getActivityView() {
		$activityView = $this->get('activity_view');
		return $activityView;
	}

	/**
	 * Function to delete corresponding image
	 * @param <type> $imageId
	 */
	public function deleteImage($imageId) {
		$db = PearDatabase::getInstance();

		$checkResult = $db->pquery('SELECT smid FROM vtiger_salesmanattachmentsrel WHERE attachmentsid = ?', array($imageId));
		$smId = $db->query_result($checkResult, 0, 'smid');

		if ($this->getId() === $smId) {
			$db->pquery('DELETE FROM vtiger_attachments WHERE attachmentsid = ?', array($imageId));
			$db->pquery('DELETE FROM vtiger_salesmanattachmentsrel WHERE attachmentsid = ?', array($imageId));
			return true;
		}
		return false;
	}


	/**
	 * Function to get the Day Starts picklist values
	 * @param type $name Description
	 */
	public static function getDayStartsPicklistValues($stucturedValues){
		$fieldModel = $stucturedValues['LBL_CALENDAR_SETTINGS'];
		$hour_format = $fieldModel['hour_format']->getPicklistValues();
		$start_hour = $fieldModel['start_hour']->getPicklistValues();

		$defaultValues = array('00:00'=>'12:00 AM','01:00'=>'01:00 AM','02:00'=>'02:00 AM','03:00'=>'03:00 AM','04:00'=>'04:00 AM','05:00'=>'05:00 AM',
					'06:00'=>'06:00 AM','07:00'=>'07:00 AM','08:00'=>'08:00 AM','09:00'=>'09:00 AM','10:00'=>'10:00 AM','11:00'=>'11:00 AM','12:00'=>'12:00 PM',
					'13:00'=>'01:00 PM','14:00'=>'02:00 PM','15:00'=>'03:00 PM','16:00'=>'04:00 PM','17:00'=>'05:00 PM','18:00'=>'06:00 PM','19:00'=>'07:00 PM',
					'20:00'=>'08:00 PM','21:00'=>'09:00 PM','22:00'=>'10:00 PM','23:00'=>'11:00 PM');

		$picklistDependencyData = array();
		foreach ($hour_format as $value) {
			if($value == 24){
				$picklistDependencyData['hour_format'][$value]['start_hour'] = $start_hour;
			}else{
				$picklistDependencyData['hour_format'][$value]['start_hour'] = $defaultValues;
			}
		}
		if(empty($picklistDependencyData['hour_format']['__DEFAULT__']['start_hour'])) {
			$picklistDependencyData['hour_format']['__DEFAULT__']['start_hour'] = $defaultValues;
		}
		return $picklistDependencyData;
	}

	/**
	 * Function returns if tag cloud is enabled or not
	 */
	function getTagCloudStatus() {
		$db = PearDatabase::getInstance();
		$query = "SELECT visible FROM vtiger_homestuff WHERE userid=? AND stufftype='Tag Cloud'";
        $result=$db->pquery($query, array($this->getId()));
        if($db->num_rows($result)){
            $visibility = $db->query_result($result, 0, 'visible');
            if($visibility == 0) {
                return true;
            }
        }else{
            return true;
		}
		return false;
	}

	/**
	 * Function saves tag cloud
	 */
	function saveTagCloud() {
		$db = PearDatabase::getInstance();
		$db->pquery("UPDATE vtiger_homestuff SET visible = ? WHERE userid=? AND stufftype='Tag Cloud'",
				array($this->get('tagcloud'), $this->getId()));
	}

	/**
	 * Function to get user groups
	 * @param type $userId
	 * @return <array> - groupId's
	 */
	public static function getUserGroups($userId){
		$db = PearDatabase::getInstance();
		$groupIds = array();
		$query = "SELECT groupid FROM vtiger_users2group WHERE userid=?";
		$result = $db->pquery($query, array($userId));
		for($i=0; $i<$db->num_rows($result); $i++){
			$groupId = $db->query_result($result, $i, 'groupid');
			$groupIds[] = $groupId;
		}
		return $groupIds;
	}

	/**
	 * Function returns the users activity reminder in seconds
	 * @return string
	 */
	/**
	 * Function returns the users activity reminder in seconds
	 * @return string
	 */
	function getCurrentUserActivityReminderInSeconds() {
		$activityReminder = $this->reminder_interval;
		$activityReminderInSeconds = '';
		if($activityReminder != 'None') {
			preg_match('/([0-9]+)[\s]([a-zA-Z]+)/', $activityReminder, $matches);
			if($matches) {
				$number = $matches[1];
				$string = $matches[2];
				if($string) {
					switch($string) {
						case 'Minute':
						case 'Minutes': $activityReminderInSeconds = $number * 60;			break;
						case 'Hour'   : $activityReminderInSeconds = $number * 60 * 60;		break;
						case 'Day'    : $activityReminderInSeconds = $number * 60 * 60 * 24;break;
						default : $activityReminderInSeconds = '';
					}
				}
			}
		}
		//young.yang 2015-1-7 防止为空
		if(empty($activityReminderInSeconds)){$activityReminderInSeconds=60;}
		return $activityReminderInSeconds;
	}

    /**
     * Function to get the users count
     * @param <Boolean> $onlyActive - If true it returns count of only acive users else only inactive users
     * @return <Integer> number of users
     */
    public static function getCount($onlyActive = false) {
        $db = PearDatabase::getInstance();
        $query = 'SELECT 1 FROM vtiger_users ';
        $params = array();

        if($onlyActive) {
            $query.= ' WHERE status=? ';
            array_push($params,'active');
        }

        $result = $db->pquery($query,$params);

        $numOfUsers = $db->num_rows($result);
        return $numOfUsers;
    }

	/**
	 * Funtion to get Duplicate Record Url
	 * @return <String>
	 */
	public function getDuplicateRecordUrl() {
		$module = $this->getModule();
		return 'index.php?module='.$this->getModuleName().'&parent=Settings&view='.$module->getEditViewName().'&record='.$this->getId().'&isDuplicate=true';

	}

	/**
	 * Function to get instance of user model by name
	 * @param <String> $userName
	 * @return <Users_Record_Model>
	 */
	public static function getInstanceByName($userName) {
		$db = PearDatabase::getInstance();
		$result = $db->pquery('SELECT id FROM vtiger_users WHERE user_name = ?', array($userName));

		if ($db->num_rows($result)) {
			return Users_Record_Model::getInstanceById($db->query_result($result, 0, 'id'), 'Users');
		}
		return false;
	}

	/**
	 * Function to delete the current Record Model
	 */
	public function delete() {
		$this->getModule()->deleteRecord($this);
	}

	public function getActiveAdminUsers() {
		$db = PearDatabase::getInstance();

		$sql = 'SELECT id FROM vtiger_users WHERE status=? AND is_admin=?';
		$result = $db->pquery($sql, array('ACTIVE', 'on'));

		$noOfUsers = $db->num_rows($result);
		$users = array();
		if($noOfUsers > 0) {
			$focus = new Users();
			for($i=0; $i<$noOfUsers; ++$i) {
				$userId = $db->query_result($result, $i, 'id');
				$focus->id = $userId;
				$focus->retrieve_entity_info($userId, 'Users');

				$userModel = self::getInstanceFromUserObject($focus);
				$users[$userModel->getId()] = $userModel;
			}
		}
		return $users;
	}

	public function isFirstTimeLogin($userId) {
		$db = PearDatabase::getInstance();

		$query = 'SELECT 1 FROM vtiger_crmsetup WHERE userid = ? and setup_status = ?';
		$result = $db->pquery($query, array($userId, 1));
		if($db->num_rows($result) == 0){
			return true;
		}
		return false;
    }

    /**
     * 根据Id求用户名
     * @param $id
     * @return string
     * @author: steel.liu
     * @Date:xxx
     *
     */
    public function getUserName($id){
        global $adb;
        $query="select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_users.id=?";
        $result=$adb->pquery($query,array($id));
        $data=$adb->raw_query_result_rowdata($result,0);
        return $data['last_name'];
    }
    public function exportData(){
        global $root_directory,$current_user,$adb;
        $query="SELECT vtiger_users.leavedate,vtiger_users.last_name,vtiger_users.usercode,vtiger_users.user_name,vtiger_departments.departmentname FROM vtiger_users LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_user2department.departmentid WHERE vtiger_users.`status`='Active' AND vtiger_users.isdimission=1";
        $result=$adb->pquery($query,array());
        require_once $root_directory.'libraries/PHPExcel/PHPExcel.php';
        $phpexecl=new PHPExcel();
        $phpexecl->getProperties()->setCreator("liu ganglin")
            ->setLastModifiedBy("liu ganglin")
            ->setTitle("Office 2007 XLSX servicecontracts Document")
            ->setSubject("Office 2007 XLSX servicecontracts Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("servicecontracts");

        $phpexecl->setActiveSheetIndex(0)
            ->setCellValue('A1', '用户名')
            ->setCellValue('B1', '姓名')
            ->setCellValue('C1', '部门')
            ->setCellValue('D1', '工号')
            ->setCellValue('E1', '离职时间');

        //设置自动居中
        $phpexecl->getActiveSheet()->getStyle('A1:E1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //设置边框
        $phpexecl->getActiveSheet()->getStyle('A1:E1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $current=2;
        while($row=$adb->fetch_array($result)){
            $phpexecl->setActiveSheetIndex(0)
                ->setCellValue('A'.$current, $row['user_name'])
                ->setCellValue('B'.$current, $row['last_name'])
                ->setCellValue('C'.$current, $row['departmentname'])
                ->setCellValue('D'.$current, $row['usercode'])
                ->setCellValue('E'.$current, $row['leavedate']);
            //加上边框
            $phpexecl->getActiveSheet()->getStyle('A'.$current.':E'.$current)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $phpexecl->getActiveSheet()->getStyle('A'.$current.':E'.$current)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $current++;
        }

        // 设置工作表的名移
        $phpexecl->getActiveSheet()->setTitle('离职未禁用用户');
        $phpexecl->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($phpexecl, 'Excel2007');
        $path=$root_directory.'temp/';
        $filename=$path.'user'.$current_user->id.'.xlsx';
        !is_dir($path)&&mkdir($path,'0777',true);
        @unlink($filename);
        $objWriter->save($filename);
    }
    public function getDepartmentTree(){
        global $adb;
        $query="SELECT departmentid,parentdepartment,departmentname,SUBSTRING_INDEX(SUBSTRING_INDEX(parentdepartment,'::',-2),'::',1) AS parentid FROM vtiger_departments";
        $result=$adb->pquery($query,array());
        $retundata=array('success'=>false,'msg'=>'没有相关数据');
        if($adb->num_rows($result)){
            while($row=$adb->fetch_array($result)){
                $parentid=$row['parentid'];
                if($row['parentid']==$row['departmentid']){
                    $parentid='';
                }
                $retundatatemp[]=array('departmentid'=>$row['departmentid'],'departmentname'=>$row['departmentname'],'path'=>$row['parentdepartment'],'parentid'=>$parentid);
            }
            $retundata=$this->recursionDepartment($retundatatemp);
            $retundata=array('success'=>true,'data'=>$retundata[0]);
        }
        return $retundata;
    }
    public function recursionDepartment($arr){
        $refer = array();
        $tree = array();
        foreach($arr as $k => $v){
            $refer[$v['departmentid']] = & $arr[$k]; //创建主键的数组引用
        }
        foreach($arr as $k => $v){
            $pid = $v['parentid'];  //获取当前分类的父级id
            if($pid == ''){
                $tree[] = & $arr[$k];  //顶级栏目
            }else{
                if(isset($refer[$pid])){
                    $refer[$pid]['childdepartment'][] = & $arr[$k]; //如果存在父级栏目，则添加进父级栏目的子栏目数组中
                }
            }
        }
        return $tree;
    }

    static function getUserByIds($ids)
    {
        $db = PearDatabase::getInstance();

        $sql = 'SELECT id,last_name FROM vtiger_users WHERE status=? AND is_admin=?';
        $result = $db->pquery($sql, array('ACTIVE', 'on'));

        $noOfUsers = $db->num_rows($result);
    }

    public function getUserByEmail(Vtiger_Request $request)
    {
        $db = PearDatabase::getInstance();
        $sql = 'SELECT id,user_name,last_name FROM vtiger_users WHERE status=? AND email1 = ?';
        $result = $db->pquery($sql, array('ACTIVE', $request->get('email')));
        $noOfUsers = $db->num_rows($result);
        if (count($noOfUsers) > 0) {
            while ($row = $db->fetch_row($result)) {
                return $row;
            }
        }
        return 0;
    }

    /**
     * 密码修改
     */
    public function updateUsersPassword(Vtiger_Request $request){
        $db = PearDatabase::getInstance();
        $id = $request->get('id');
        $oldPassword = $request->get('oldPassword');
        $newPassword = $request->get('newPassword');
        $data=array();
        $query = ' SELECT a.user_name,a.user_password,a.crypt_type FROM vtiger_users a  WHERE a.id=? LIMIT 1 ';
        $result = $db->pquery($query, array($id));
        if($db->num_rows($result)){
            $resultData = $db->raw_query_result_rowdata($result, 0);
            $oldPassword = $this->encrypt_password($resultData['user_name'],$oldPassword,$resultData['crypt_type']);
            if($oldPassword==$resultData['user_password']){
                 $newPassword = $this->encrypt_password($resultData['user_name'],$newPassword,$resultData['crypt_type']);
                 $sql = ' UPDATE vtiger_users SET user_password =? WHERE id=? ';
                 $db->pquery($sql,array($newPassword,$id));
                 $data = array("success"=>true,"msg"=>'密码修改成功');
            }else{
                 $data = array("success"=>false,"msg"=>'原密码有误请重新输入！');
            }
        }else{
                 $data = array("success"=>false,"msg"=>'该用户id不存在，请先注册！');
        }
        return $data;
    }

    /**
     * 密码加密码  cxh 2019-11-13 copy from Users/actions/save
     * @param $user_name
     * @param $user_password
     * @param string $crypt_type
     * @return string
     */
    public function encrypt_password($user_name,$user_password, $crypt_type='') {
        // encrypt the password.
        $salt = mb_substr($user_name, 0, 2,'utf-8');
        // For more details on salt format look at: http://in.php.net/crypt
        if($crypt_type == 'MD5') {
            $salt = '$1$' . $salt . '$';
        } elseif($crypt_type == 'BLOWFISH') {
            $salt = '$2$' . $salt . '$';
        } elseif($crypt_type == 'PHP5.3MD5') {
            //only change salt for php 5.3 or higher version for backward
            //compactibility.
            //crypt API is lot stricter in taking the value for salt.
            $salt = '$1$' . str_pad($salt, 9, '0');
        }

        $encrypted_password = crypt($user_password, $salt);
        return $encrypted_password;
    }

    /**
     * 单点登陆接口
     * @param Vtiger_Request $request
     * @return array
     * @throws Exception
     */
    public function userlogin(Vtiger_Request $request){
        global $adb;
        $user_name=$request->get('loginname');
        $user_password=$request->get('password');
        $password = $this->encrypt_password($user_name, $user_password, 'PHP5.3MD5');
        $sql = "SELECT vtiger_users.id AS userId,vtiger_users.usercode,vtiger_users.reports_to_id AS reportstoid,vtiger_users.user_name AS username,vtiger_users.last_name AS fullname,IF(vtiger_users.is_admin='on','1','0') AS isadmin,IFNULL(vtiger_users.email1,vtiger_users.email2) AS email,
            vtiger_user2role.roleid,vtiger_role.rolename,
			(SELECT M.usercode FROM vtiger_users M WHERE M.id=vtiger_users.reports_to_id) AS reportsusercode,
            vtiger_user2department.departmentid,vtiger_departments.departmentname
            FROM vtiger_users 
            LEFT JOIN vtiger_user2department ON(vtiger_users.id=vtiger_user2department.userid)
            LEFT JOIN vtiger_user2role ON(vtiger_users.id=vtiger_user2role.userid)
            LEFT JOIN vtiger_role ON(vtiger_role.roleid=vtiger_user2role.roleid)
            LEFT JOIN vtiger_departments ON(vtiger_user2department.departmentid=vtiger_departments.departmentid)
            WHERE vtiger_users.`status`='Active' AND vtiger_users.user_name=? AND vtiger_users.user_password=? limit 1";
        $sales = $adb->pquery($sql, array($user_name, $password));
        $rows = $adb->num_rows($sales);
        if ($rows) {
            $lists = array();
            $result = $adb->query_result_rowdata($sales, 0);
            $data=json_encode($result,JSON_UNESCAPED_UNICODE);
            //$data=encrypt($data);
            $returnData=array("success"=>true,"data"=>$result);
            //echo json_encode(array("success"=>true,"data"=>$result));
        } else {
            $returnData=array('success' => false, 'msg' => '用户名或密码不正确');
        }
        return $returnData;

    }
    //向微信中添加部门只能执行一次不能
    public function createDepartmentToWeixin(){
        set_time_limit(0);
        global $adb;
        $query='SELECT *from vtiger_departments ORDER BY parentdepartment';
        $result=$adb->pquery($query,array());
        if($adb->num_rows($result)){
            while($row=$adb->fetch_array($result)){
                if(!in_array($row['departmentid'],array('H1','H2','H3'))){
                    $datas['flag']=8;
                    $datas['departmentname']=$row['departmentname'];
                    $datas['departmentid']=$row['departmentid'];
                    $datas['parentdepartment']=$row['parentdepartment'];
                    $datas['newdepartmentid']='';
                    $datas['ERPDOIT']=456321;
                    $parentidArray=explode('::',$row['parentdepartment']);
                    $arrayindex=count($parentidArray)-2;
                    $datas['parentid']=trim($parentidArray[$arrayindex],'H');
                    $this->sendWechatMessage($datas);
                }

            }

        }
    }
    //向更改微信人员所在的部门不能多次执行
    public function setUserDepartmentToWeixin(){
        set_time_limit(0);
        global $adb;
        $query='select vtiger_users.email1,vtiger_user2department.departmentid FROM vtiger_users LEFT JOIN vtiger_user2department ON vtiger_users.id=vtiger_user2department.userid WHERE `status`=\'Active\' AND isdimission=0';
        $result=$adb->pquery($query,array());
        if($adb->num_rows($result)){
            while($row=$adb->fetch_array($result)){
                $departmentid=trim($row['departmentid'],'H');
                $email=trim($row['email1']);
                if(!empty($email) && $departmentid>0){
                    $datas['flag']=11;
                    $datas['username']=$row['email1'];
                    $datas['email']=$row['email1'];
                    $datas['departmentid']=$departmentid;
                    $datas['ERPDOIT']=456321;
                    $datas['oldemail']=$row['email1'];
                    $this->sendWechatMessage($datas);
                }

            }

        }

    }
    public function getUserInfoByUcodeUname($request){
        global $adb;
        $lastname=$request->get('lastname');
        $usercode=$request->get('usercode');
        $sql = "SELECT 
                #vtiger_users.id AS userId,
                vtiger_users.usercode,
                #vtiger_users.reports_to_id AS reportstoid,
                #vtiger_users.user_name AS username,
                vtiger_users.last_name AS fullname,
                #IF(vtiger_users.is_admin='on','1','0') AS isadmin,
                #IFNULL(vtiger_users.email1,vtiger_users.email2) AS email,
                #vtiger_user2role.roleid,vtiger_role.rolename,
                #(SELECT M.usercode FROM vtiger_users M WHERE M.id=vtiger_users.reports_to_id) AS reportsusercode,
                #vtiger_user2department.departmentid,
                invoicecompany,
                vtiger_departments.departmentname
            FROM vtiger_users 
            LEFT JOIN vtiger_user2department ON(vtiger_users.id=vtiger_user2department.userid)
            LEFT JOIN vtiger_user2role ON(vtiger_users.id=vtiger_user2role.userid)
            LEFT JOIN vtiger_role ON(vtiger_role.roleid=vtiger_user2role.roleid)
            LEFT JOIN vtiger_departments ON(vtiger_user2department.departmentid=vtiger_departments.departmentid)
            WHERE vtiger_users.`status`='Active' AND vtiger_users.usercode=? AND vtiger_users.last_name like ? limit 1";
        $sales = $adb->pquery($sql, array($usercode,$lastname.'%'));
        $rows = $adb->num_rows($sales);
        if ($rows) {
            $lists = array();
            $result = $adb->query_result_rowdata($sales, 0);
            $data=json_encode($result,JSON_UNESCAPED_UNICODE);
            //$data=encrypt($data);
            $returnData=array("success"=>true,"data"=>$result);
            //echo json_encode(array("success"=>true,"data"=>$result));
        } else {
            $returnData=array('success' => false, 'msg' => '没有相关数据！');
        }
        return $returnData;
    }

    /**
     * 获取接定月份在职的人员ID
     * @param $request
     * @return array
     */
    public function getActiveUser($request){
        global $adb;
        $activedate=$request->get('currentdate');
        $departmentid=$request->get('departmentid');
        do{
            if(empty($activedate)){
                $returnData=array('success' => false, 'msg' => '没有相关数据！');
                break;
            }
            $activedate=substr($activedate,0,7);
            $query='SELECT userid FROM vtiger_useractivemonth WHERE activedate=?  AND departmentid=? UNION SELECT id AS userid FROM vtiger_users LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id WHERE LEFT(user_entered,7)=? AND vtiger_user2department.departmentid=?';
            $result=$adb->pquery($query,array($activedate,$departmentid,$activedate,$departmentid));
            if($adb->num_rows($result)){
                $data=array();
                while($row=$adb->fetch_array($result)){
                    $data[]=(int)$row['userid'];
                }
                $returnData=array("success"=>true,"data"=>$data);
            }else{
                $returnData=array('success' => false, 'msg' => '没有相关数据！');
            }
        }while(0);
       return $returnData;

    }
    /**
     * 获取所有公司名称以及id
     * @param $request
     * @return array
     */
    public function getAllDepartment($request){
        global $adb;
        $allrecord=$adb->run_query_allrecords(" SELECT * FROM vtiger_departments ");
        if($allrecord){
            $type= true;
        }else{
            $type= false;
        }
        return array("success"=>$type,"data"=>$allrecord);
    }
    /**
     * 获取所有公司名称以及id
     * @param $request
     * @return array
     */
    public function getAllCompany($request){
        global $adb;
        $allrecord=$adb->run_query_allrecords(" SELECT  a.*  FROM (SELECT companyid,invoicecompany FROM vtiger_invoicecompany ORDER BY invoicecompanyid DESC  ) as a   GROUP BY a.companyid ");
        if($allrecord){
            $type= true;
        }else{
            $type= false;
        }
        return array("success"=>$type,"data"=>$allrecord);
    }
	 /**
     * 获取所有公司名称以及id及编号
     * @param $request
     * @return array
     */
    public function getAllCompanyANo($request){
        global $adb;
        $result=$adb->pquery("SELECT  a.*  FROM (SELECT companyid,invoicecompany,companycode FROM vtiger_invoicecompany ORDER BY invoicecompanyid DESC  ) as a   GROUP BY a.companyid",array());
        if($adb->num_rows($result)){
            $type= true;
            $allrecord=array();
            while($row=$adb->fetchByAssoc($result)){
                $allrecord[]=$row;
            }
        }else{
            $type= false;
            $allrecord=array();
        }
        return array("success"=>$type,"data"=>$allrecord);
    }

    /**
     * 获取 分页数据
     */
    public function getPageCompany(){
        global $adb;
        do{
            $data=[];
            $request=file_get_contents('php://input');
            $request=json_decode($request,true);
            if(!isset($request['pageNum']) || !isset($request['pageSize']) || (!is_numeric($request['pageNum'])||strpos($request['pageNum'],".")!==false)|| (!is_numeric($request['pageSize'])||strpos($request['pageSize'],".")!==false)){
                $type= false;
                $message="参数不正确!";
                break;
            }
            if(!($request['pageNum']>0)){
                $type= false;
                $message="pageNum 参数必须大于0!";
                break;
            }
            $where='';
            $company=[];
            if(isset($request['invoicecompany']) && !empty($request['invoicecompany'])){
                $where.=" WHERE companyid= ? ";
                $values[]=$request['invoicecompany'];
                $company[]=$request['invoicecompany'];
            }
            $start=($request['pageNum']-1)*$request['pageSize']?($request['pageNum']-1)*$request['pageSize']:0;
            $end  =$request['pageSize'];
            $values[]=$start;
            $values[]=$end;
            if(empty($end)){
                $type= false;
                $message="参数 end不能为空!";
                break;
            }
            //$allrecord=$adb->run_query_allrecords(" SELECT companyid,invoicecompany FROM vtiger_invoicecompany  GROUP BY companycode LIMIT ".$start.",".$end);
            // 由于发票表中会出现id 相同的  所以必须倒序再分组
            $result=$adb->pquery(" SELECT  a.*  FROM (SELECT companyid,invoicecompany FROM vtiger_invoicecompany ".$where." ORDER BY invoicecompanyid DESC ) as a GROUP BY a.companyid LIMIT ?,? ",$values);
            $count=$adb->pquery("SELECT companyid,invoicecompany FROM vtiger_invoicecompany ".$where."  GROUP BY companyid ",array($company));
            $count=$adb->num_rows($count);
            $allrecord=[];
            while ($rowData=$adb->fetch_array($result)){
                $allrecord[]=$rowData;
            }
            if(!empty($allrecord)){
                $data['data']=$allrecord;
                $data['count']=$count;
                $type= true;
                $message="获取成功";
            }else{
                $data['data']=$data;
                $data['count']=$count;
                $type= true;
                $message="记录为空";
            }

        }while(false);
        return array("success"=>$type,"result"=>$data,"message"=>$message);
    }
    /**
     * 获取角色名称
     * @param $request
     * @return array
     */
    public function getRoleInfo($request){
        global $adb;
        $roleInfo=file_get_contents("php://input");
        $roleId= json_decode($roleInfo,true);
        if(isset($roleId['roleid']) && !empty($roleId['roleid'])){
            $allrecord=$adb->pquery("SELECT roleid,rolename FROM vtiger_role WHERE roleid=? LIMIT 1 ",array($roleId['roleid']));
            $allrecord=$adb->query_result_rowdata($allrecord,0);
            if($allrecord){
                $type= true;
                $message="获取成功！";
            }else{
                $type= false;
                $message="无记录！";
            }
        }else{
            $type= false;
            $message="缺少参数！";
        }
        return array("success"=>$type,'message'=>$message,"data"=>$allrecord);
    }
    /**
     *  //获取所有角色数据
     * @param $request
     * @return array
     *
     */
    public function getAllRoles($request){
        global $adb;
        $allrecord=$adb->run_query_allrecords(" SELECT roleid,rolename FROM vtiger_role WHERE roleid!='H1'  ");
        if($allrecord){
            $type= true;
        }else{
            $type= false;
        }
        return array("success"=>$type,"data"=>$allrecord);
    }

    function get_user_hash($input) {
        return strtolower(md5($input));
    }
    protected $temployeelevel=array(1=>'EmployeeLevel',2=>'Manageriallevel',3=>'DirectorAndAbove');
    protected $tstafftype=array(2=>'Contract',1=>'Internsh');
    protected $tstatus=array(0=>'Active',1=>'Inactive');

    /**
     * @param  $request $arrayJson=array('id'=>1000001,
     * @return array
     * @throws Exception
     */
    public  function  insertUsers($request){
       global $adb,$current_user;
       // 操作人 即 调用接口方 传过来的 操作人id
       $current_user->id=1;
       $usersInfo=file_get_contents("php://input");
       $remark=$usersInfo;
       $usersInfo= json_decode($usersInfo,true);
       $remark.=json_encode($_REQUEST);
       $adb->pquery(" INSERT INTO `vtiger_achievementallot_nocalculation` (`contract_no`, `marks`, `date`) VALUES (?,? ,?) ",array("RENYUANQINGNEW",$remark,date("Y-m-d H:i:s")));
       if(isset($usersInfo['operator']) && !empty($usersInfo['operator'])){
           $current_user->id=$usersInfo['operator'];
           unset($usersInfo['operator']);
       }else{
           $current_user->id=1;
       }
        do{
           if(!isset($usersInfo['email1']) || empty($usersInfo['email1'])){
               $result=false;
               $message="缺少邮箱！";
               break;
           }
           /*$type=$request->get("type");*/
           $newUser = new Users();
           $new_password=$usersInfo['password'];
           $user_hash = $this->get_user_hash($new_password);
           $crypt_type = 'PHP5.3MD5';
           $encrypted_new_password = $newUser->encrypt_password_new($new_password, $crypt_type,$usersInfo['user_name']);
           $usersInfo['user_password']=$encrypted_new_password;
           $usersInfo['confirm_password']=$encrypted_new_password;
           $usersInfo['user_hash']=$user_hash;
           $usersInfo['crypt_type']=$crypt_type;
           $userid=$adb->getUniqueID("vtiger_users");
           $usersInfo['id']=$userid;
           unset($usersInfo['password']);
           // userid
           if(empty($usersInfo['id']) || empty($usersInfo['companyid']) || empty($usersInfo['departmentid'])){
               $result=false;
               $message="缺少id或者companyid 或者departmentid 参数！";
               break;
           }
           $users=$adb->pquery("SELECT * FROM vtiger_users WHERE id=? OR user_name=? limit 1 ",array($usersInfo['id'],$usersInfo['user_name']));
           if($adb->num_rows($users)>0){
               $result=false;
               $message="该登录名已存在！";
               break;
           }
           $usersInfo['employeelevel']=$this->temployeelevel[$usersInfo['employeelevel']];
           $usersInfo['stafftype']=$this->tstafftype[$usersInfo['stafftype']];
           $usersInfo['status']=$this->tstatus[$usersInfo['status']];
           $usersInfo['graduatetime']=isset($usersInfo['graduatetime'])? $usersInfo['graduatetime']:'';
           // 公司名称
           $companyName=$adb->pquery("SELECT  companyfullname  FROM vtiger_company_code  WHERE companyid=?  LIMIT 1 ",array($usersInfo['companyid']));
           $companyName=$adb->query_result_rowdata($companyName,0);
           $usersInfo['invoicecompany']=$companyName['companyfullname'];
           // 部门名称
           $departmentname=$adb->pquery("SELECT  departmentname  FROM vtiger_departments  WHERE departmentid=?  LIMIT 1 ",array($usersInfo['departmentid']));
           $departmentname=$adb->query_result_rowdata($departmentname,0);
           $usersInfo['department']=$departmentname['departmentname'];
           /*$recordModule=Vtiger_Record_Model::getCleanInstance("Users");*/
           $userMangerRecordModule=Vtiger_Record_Model::getCleanInstance("UserManger");
           //负责人
           if(isset($usersInfo['assigned_user_id']) && !empty($usersInfo['assigned_user_id'])){
               $userMangerRecordModule->set("assigned_user_id",$usersInfo['assigned_user_id']);
               unset($usersInfo['assigned_user_id']);
           }else{
               $userMangerRecordModule->set("assigned_user_id",$usersInfo['operator']);
           }
           foreach ($usersInfo as $key=>$val){
               // 字段键值重组
               if($key=='id'){
                   $userMangerRecordModule->set("userid",$val);
               }else if($key=='phone_mobile'){
                   $userMangerRecordModule->set("mobile",$val);
               }else{
                   $userMangerRecordModule->set($key,$val);
               }
           }
           if(isset($usersInfo['roleid'])){
                $roleid=$usersInfo['roleid'];
                unset($usersInfo['roleid']);
           }
           if(isset($usersInfo['departmentid'])){
                $departmentid=$usersInfo['departmentid'];
                unset($usersInfo['departmentid']);
           }
            /*if(count($usersInfo) <23){
                $result=fasle;
                $message="缺少参数！";
                break;
            }else if(count($usersInfo) >23){
                $result=false;
                $message="参数多余！";
                break;
            }
             if(count($usersInfo) <22){
                 $result=false;
                 $message="缺少参数！";
                 break;
             }else if(count($usersInfo) >22){
                 $result=false;
                 $message="参数多余！";
                 break;
             }*/
           $column=array_keys($usersInfo);
            $query='DESC vtiger_users';
            $descResult=$adb->pquery($query);
            $newUsersInfo=array();//过滤非user表字段
            while($row=$adb->fetch_row($descResult)){
                if(in_array($row[0],$column)){
                    $newUsersInfo[$row[0]]=$usersInfo[$row[0]];
                }
            }
            if(count($newUsersInfo) <24){
                $result=fasle;
                $message="缺少参数！";
                break;
            }else if(count($newUsersInfo) >24){
                $result=false;
                $message="参数多余！";
                break;
            }
            $column=array_keys($newUsersInfo);
            $value=array_values($newUsersInfo);
            $column=implode(",",$column);
            $column=trim($column,',');
            $userMangerRecordModule->save();
            $userss=$adb->pquery("SELECT 1 FROM vtiger_usermanger WHERE userid=?  limit 1 ",array($userid));
            if($adb->num_rows($userss)>0){
                //$sql="INSERT INTO vtiger_users (".$column.") VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                $sql="INSERT INTO vtiger_users (".$column.") VALUES (".generateQuestionMarks($value).")";
               $adb->pquery($sql,$value);
               $adb->pquery("UPDATE vtiger_users_seq SET id=? LIMIT  1 ",array($usersInfo['id']));
               $sql="INSERT INTO `vtiger_user2role` (`userid`, `roleid`, `secondroleid`) VALUES (?, ?, '')";
               $adb->pquery($sql,array($usersInfo['id'],$roleid));
               $sql="INSERT INTO `vtiger_user2department` (`userid`, `departmentid`) VALUES (?,?)";
               $adb->pquery($sql,array($usersInfo['id'],$departmentid));
               $adb->pquery("UPDATE vtiger_usermanger set usercode=?,ownornot=0,modulestatus='c_complete' WHERE userid=? LIMIT 1 ",array($usersInfo['usercode'],$userid));
               //发送消息
               $this->setweixincontracts(array('ERPDOIT'=>456321,'flag'=>1,'departmentid'=>$departmentid,'email'=>$usersInfo['email1'],'oldemail'=>'','mobile'=>$usersInfo['phone_mobile'],'username'=>$usersInfo['last_name']));
               //$result=json_decode($result,true);
               $result=true;
               $this->updateYxTang(array('flag'=>12,'data'=>array(array("pNames"=>"珍岛岗位;".$usersInfo['title'],"pNo"=>md5($usersInfo['title'])))));
               //$this->updateYxTang(array('flag'=>11,'data'=>array('datas'=>array(array("name"=>$usersInfo['title'])))));
               $this->updateYxTang(array('flag'=>2,'data'=>array(
                   'userName'=>$usersInfo['user_name'],
                   'cnName'=>$usersInfo['last_name'],
                   'orgOuCode'=>trim($departmentid,'H'),
                   'mail'=>$usersInfo['email1'],
                   'userNo'=>$usersInfo['usercode'],
                   //'gradeName'=>$usersInfo['title'],
                   'postionNo'=>md5($usersInfo['title']),
                   'mobile'=>$usersInfo['phone_mobile'],
                   'id'=>md5($userid),
                   'isEmailValidated'=>0,
                   'isMobileValidated'=>0,
               )
               ));
               $message="添加成功";
               return array("success"=>$result,"message"=>$message,"id"=>$userid);exit();
               }else{
                   $result=false;
                   $message="添加失败！";
                   break;
               }
       }while(false);
       return array("success"=>$result,"message"=>$message);
    }
    /**
     * @param $request
     * @return array
     */
    public  function updateUsers($request){
        global $adb,$current_user;
        $usersInfo=file_get_contents("php://input");
        $remark=$usersInfo;
        $remark.=json_encode($_REQUEST);
        $usersInfo= json_decode($usersInfo,true);
        $adb->pquery(" INSERT INTO `vtiger_achievementallot_nocalculation` (`contract_no`, `marks`, `date`) VALUES (?,? ,?) ",array("RENYUANQING",$remark,date("Y-m-d H:i:s")));
        $_REQUEST['RENYUAN']='RENYUANXITONG';
        do{
            try{
                if(isset($usersInfo['operator']) && !empty($usersInfo['operator'])){
                    $operatorId=$usersInfo['operator'];
                    unset($usersInfo['operator']);
                }else{
                    $operatorId=1;
                }
                // 判断操作人是权限配置文件
                $url='user_privileges/user_privileges_'.$operatorId.'.php';
                $status=0;
                if(file_exists($url)){
                }else{
                    $status=1;
                    touch($url);
                    chmod($url,644);
                }
                $user = new Users();
                $current_user = $user->retrieveCurrentUserInfoFromFile($operatorId);
                /*$status=array(0=>'Active',1=>'Inactive');*/
                if(isset($usersInfo['employeelevel']) && !empty($usersInfo['employeelevel'])){
                    $usersInfo['employeelevel']=$this->temployeelevel[$usersInfo['employeelevel']];
                }
                if(isset($usersInfo['stafftype']) && !empty($usersInfo['stafftype'])){
                    $usersInfo['stafftype']=$this->tstafftype[$usersInfo['stafftype']];
                }
                if(isset($usersInfo['status'])){
                    $usersInfo['status']=$this->tstatus[$usersInfo['status']];
                }
                /*var_dump($usersInfo);exit();*/
                if(isset($usersInfo['id']) && !empty($usersInfo['id'])){
                }else{
                    $result=false;
                    $message="参数id必传";
                    break;
                }
                $isExistUser=$adb->pquery("SELECT * FROM vtiger_users WHERE id=?",array($usersInfo['id']));
                if(!$adb->num_rows($isExistUser)){
                    $result=false;
                    $message="该用户不存在！";
                    break;
                }
                if(isset($usersInfo['user_name'])){
                    //检查登录名是否重复
                    $isExistUserByuserName=$adb->pquery("SELECT * FROM vtiger_users WHERE user_name=? AND id<> ? ",array($usersInfo['user_name'],$usersInfo['id']));
                    if($adb->num_rows($isExistUserByuserName)>0){
                        $result=false;
                        $message="登录名重复！";
                        break;
                    }
                }
                if(isset($usersInfo['companyid']) && !empty($usersInfo['companyid'])){
                    $companyName=$adb->pquery("SELECT  companyfullname  FROM vtiger_company_code  WHERE companyid=?  LIMIT 1 ",array($usersInfo['companyid']));
                    $companyName=$adb->query_result_rowdata($companyName,0);
                    $usersInfo['invoicecompany']=$companyName['companyfullname'];
                }else if(isset($usersInfo['companyid']) && empty($usersInfo['companyid'])){
                    $result=false;
                    $message="参数companyid不能为空！";
                    break;
                }
                $userid=$usersInfo['id'];
                $recordModule=Vtiger_Record_Model::getInstanceById($userid,"Users");
                // 获取 userManager 主键id
                $userManagerInfo=$adb->pquery("SELECT * FROM vtiger_usermanger WHERE userid=?",array($userid));
                $userManagerInfo=$adb->query_result_rowdata($userManagerInfo,0);
                if(empty($userManagerInfo['usermangerid'])){
                    $result=false;
                    $message="crmid 不存在 垃圾数据！";
                    break;
                }
                /*var_dump($userManagerInfo['usermangerid']);exit();*/
                $userMangerRecordModule=Vtiger_Record_Model::getInstanceById($userManagerInfo['usermangerid'],"UserManger");
                if(empty($userMangerRecordModule)){
                    $result=false;
                    $message="该用户不存在！";
                    break;
                }
                if(empty($recordModule)){
                    $result=false;
                    $message="该用户不存在！";
                    break;
                }
                if(isset($usersInfo['last_name']) && !empty($usersInfo['last_name'])){
                    $username=$usersInfo['last_name'];
                }else{
                    $username=$recordModule->get("last_name");
                }
                if(isset($usersInfo['roleid']) && !empty($usersInfo['roleid'])){
                    $managerParam['roleid']=$usersInfo['roleid'];
                    unset($usersInfo['roleid']);
                }
                if(isset($usersInfo['departmentid']) && !empty($usersInfo['departmentid'])){
                    $result=$adb->pquery("SELECT departmentname FROM vtiger_departments WHERE departmentid=? limit 1",array($usersInfo['departmentid']));
                    $result=$adb->query_result_rowdata($result,0);
                    $managerParam['department']=$result['departmentname'];
                    $managerParam['departmentid']=$usersInfo['departmentid'];
                    unset($usersInfo['departmentid']);
                }
                // 密码修改处理
                if(isset($usersInfo['password'])){
                    // echo 123;exit();
                    if(isset($usersInfo['user_name'])){
                        $user_name=$usersInfo['user_name'];
                    }else{
                        $user_name=$recordModule->get("user_name");
                    }
                    $newUser = new Users();
                    $new_password=$usersInfo['password'];
                    $user_hash = $this->get_user_hash($new_password);
                    $crypt_type = 'PHP5.3MD5';
                    $encrypted_new_password = $newUser->encrypt_password_new($new_password, $crypt_type,$user_name);
                    $adb->pquery("UPDATE  vtiger_users SET user_password=?,confirm_password=?,user_hash=?,crypt_type=? WHERE id=? ",array($encrypted_new_password,$encrypted_new_password,$user_hash,$crypt_type,$userid));
                    $adb->pquery("UPDATE  vtiger_usermanger SET user_password=?,confirm_password=?,user_hash=?,crypt_type=? WHERE userid=? ",array($encrypted_new_password,$encrypted_new_password,$user_hash,$crypt_type,$userid));
                    unset($usersInfo['password']);
                    $result=true;
                    $message="修改成功！!";
                    // 修改密码之后直接中断 返回修改成功
                    break;
                }
                $oldemail1=$recordModule->get("email1");
                $mobile=$recordModule->get("mobile");
                //查询出所有的字段
                $sql=" DESC vtiger_users";
                $result=$adb->pquery($sql,array());
                $columnArray=array();
                while ($rowData=$adb->fetch_array($result)){
                    $columnArray[]=$rowData['field'];
                }
                $recordModule->set("mode",'edit');
                /*$userMangerRecordModule->set("mode",'edit');*/ //   cxh   去掉 usermanger。
                //循环遍历设置值传过来的值
                $strKey='';
                $values=array();
                foreach ($usersInfo as $key=>$val){
                    if(in_array($key,$columnArray)){
                        if($key!='id'){
                            if($key=='phone_mobile'){
                                $recordModule->set("mobile",$val);
                                $strKey.=" phone_mobile = ? ,";
                                $values[]=$val;
                                // 组装日志数据
                                if($val!=$userManagerInfo['phone_mobile']){
                                    $params['strArray'][]=array('fieldname'=>$key,'prevalue'=>$userManagerInfo['phone_mobile'],'postvalue'=>$val);
                                }
                                // $userMangerRecordModule->set("mobile",$val); 去掉usermanager 系统更新
                            }else{
                                $recordModule->set($key,$val);
                                $strKey.=" ".$key." = ? ,";
                                if($key=='leavedate' && empty($val)){
                                    $values[]=NULL;
                                }else{
                                    $values[]=$val;
                                }

                                // 组装日志数据
                                if($val!=$userManagerInfo[$key]){
                                    $params['strArray'][]=array('fieldname'=>$key,'prevalue'=>$userManagerInfo[$key],'postvalue'=>$val);
                                }
                                // $userMangerRecordModule->set($key,$val); 去掉usermanager 系统更新
                            }
                        }
                    }else{
                        return array("success"=>false,"message"=>"参数有误！");
                    }
                }
                if(empty($usersInfo) && empty($managerParam)){
                    $result=false;
                    $message="缺少参数！";
                    break;
                }
                //如果部门id和角色id 传了则  userManager  同时也要更新
                foreach ($managerParam as $key=>$value){
                    // 组装日志数据
                    if($value!=$userManagerInfo[$key]){
                        $strKey.=" ".$key." = ? ,";
                        $values[]=$value;
                        $params['strArray'][]=array('fieldname'=>$key,'prevalue'=>$userManagerInfo[$key],'postvalue'=>$value);
                    }
                    //$userMangerRecordModule->set($key,$value);  去掉usermanager 系统更新
                }

                if(!empty($values)){
                    $strKey=trim($strKey,",");
                    $recordModule->save();
                    $adb->pquery("UPDATE vtiger_usermanger SET ".$strKey." WHERE userid=? ",array($values,$userid));
                    // 添加日志记录
                    $params['record']=$userManagerInfo['usermangerid'];
                    $params['module']='UserManger';
                    $params['userid']=$current_user->id;
                    $params['status']=0;
                    $this->addLogs($params);
                }
                // $userMangerRecordModule->save();  去掉usermanager 系统更新
                if(isset($managerParam['roleid']) && !empty($managerParam['roleid'])){
                    $adb->pquery("UPDATE vtiger_user2role SET roleid=? WHERE userid=? ",array($managerParam['roleid'],$userid));
                }
                if(isset($managerParam['departmentid']) && !empty($managerParam['departmentid'])){
                    $adb->pquery("UPDATE vtiger_user2department SET departmentid=?  WHERE userid=? ",array($managerParam['departmentid'],$userid));
                }
                // 去掉这个 只存了 personnelpositionid  所以前面需要配置 可编辑保存才能把 vtiger_users 的 personnelposition 名称存进去  所以没有配置 就走下面的自己更新了。
                if(isset($usersInfo['personnelpositionid'])){
                    $adb->pquery("UPDATE vtiger_users SET personnelpositionid=?,personnelposition=? WHERE id=? ",array($usersInfo['personnelpositionid'],$usersInfo['personnelposition'],$userid));
                }
                if($status==1){
                    if(is_file($url)){
                        unlink($url);
                    }
                }
            }catch(\Exception $e){
                $code=$e->getCode();
                if($code==1){
                    $result=false;
                    $message="用户已删除！";
                }else{
                    $result=false;
                    $message="其他异常情况！";
                }
                break;
            }
            //企业微信 用户组织架构处理
            $departmentInfo=$adb->pquery("SELECT vtiger_users.user_name,vtiger_users.title,vtiger_users.usercode,vtiger_user2department.departmentid,vtiger_users.email1,vtiger_users.phone_mobile,vtiger_users.status,vtiger_users.isdimission  FROM vtiger_users LEFT JOIN  vtiger_user2department ON vtiger_users.id=vtiger_user2department.userid WHERE vtiger_users.id=? LIMIT 1 ",array($userid));
            $departmentInfo=$adb->query_result_rowdata($departmentInfo,0);
            $departmentid=$departmentInfo['departmentid'];
            //如果是部门修改 或者 手机号变化
            if((isset($usersInfo['phone_mobile']) && $usersInfo['phone_mobile']!=$mobile) &&  $departmentInfo['isdimission']==0 && $departmentInfo['status']=='Active' ){
                $oldemail1=$departmentInfo['email1'];
                $result=$this->setweixincontracts(array('ERPDOIT'=>456321,'flag'=>2,'departmentid'=>trim($departmentid,'H'),'email'=>$departmentInfo['email1'],'oldemail'=>$oldemail1,'mobile'=>$mobile,'username'=>$username));
            }
            if((isset($managerParam['departmentid']) && $managerParam['departmentid']!=$userManagerInfo['departmentid'])){
                $oldemail1=$departmentInfo['email1'];
                $result=$this->setweixincontracts(array('ERPDOIT'=>456321,'flag'=>4,'departmentid'=>trim($departmentid,'H'),'email'=>$departmentInfo['email1'],'oldemail'=>$oldemail1,'mobile'=>$mobile,'username'=>$username));

            }
            $departmentid=trim($departmentid,"H");
            //如果是禁用  或者删除  则同时 删除企业微信账号
            if($usersInfo['isdimission']==1||$usersInfo['status']=='Inactive'){
                $result=$this->setweixincontracts(array('ERPDOIT'=>456321,'flag'=>3,'departmentid'=>trim($departmentid,'H'),'oldemail'=>$oldemail1,'username'=>$username));
                $result=json_decode($result,true);
                if($usersInfo['isdimission']==1 && $usersInfo['status']!='Inactive'){
                    $this->sendMailToReportto($usersInfo['id']);
                }
                try {
                    $this->https_requestcomm($this->logoutURL1 . $userid);//离职后禁用用户登陆
                    $this->https_requestcomm($this->logoutURL2 . $userid);//离职后禁用用户登陆
                }catch (Exception $e){

                }
            //如果设置了邮箱且不为空 则两种可能一 新增邮箱  二 编辑邮箱
            }else if(isset($usersInfo['email1']) && !empty($usersInfo['email1'])  && $departmentInfo['isdimission']==0 && $departmentInfo['status']=='Active'){
                //判断已有的和 要编辑的是否一致
                if($oldemail1!=$usersInfo['email1']){
                    // 走修改
                    if(!empty($oldemail1)){
                        $result=$this->setweixincontracts(array('ERPDOIT'=>456321,'flag'=>2,'departmentid'=>trim($departmentid,'H'),'email'=>$usersInfo['email1'],'oldemail'=>$oldemail1,'mobile'=>$mobile,'username'=>$username));
                        $result=json_decode($result,true);
                        // 走新增
                    }else{
                        $result=$this->setweixincontracts(array('ERPDOIT'=>456321,'flag'=>1,'departmentid'=>trim($departmentid,'H'),'email'=>$usersInfo['email1'],'oldemail'=>$oldemail1,'mobile'=>$mobile,'username'=>$username));
                        $result=json_decode($result,true);
                    }
                }
                //如查理更改了手机号
            }else if(isset($usersInfo['phone_mobile']) && !empty($usersInfo['phone_mobile'])  && $departmentInfo['isdimission']==0 && $departmentInfo['status']=='Active'){
                //判断已有的和 要编辑的是否一致
                if($mobile!=$usersInfo['phone_mobile']){
                    // 走修改
                    if(!empty($oldemail1)) {
                        $result = $this->setweixincontracts(array('ERPDOIT' => 456321, 'flag' => 2, 'departmentid' => $departmentid, 'email' => $oldemail1, 'oldemail' => $oldemail1, 'mobile' => $usersInfo['phone_mobile'], 'username' => $username));
                        $result = json_decode($result, true);
                    }

                }
                //设置邮箱但是为空 ① 邮箱由有修改成无  或者 本来无修改无 里边进行判断
            }else if(isset($usersInfo['email1'])){
                if( empty($usersInfo['email1']) && empty($oldemail1) ){
                //删除旧的企业微信email账号
                }else{
                    $result=$this->setweixincontracts(array('ERPDOIT'=>456321,'flag'=>3,'departmentid'=>trim($departmentid,'H'),'email'=>$usersInfo['email1'],'oldemail'=>$oldemail1,'mobile'=>$mobile,'username'=>$username));
                    $result=json_decode($result,true);
                }
            }
            /**
             * 云课堂
             */
            if($usersInfo['isdimission']==1||$usersInfo['status']=='Inactive'){
                $this->updateYxTang(array('flag'=>3,'data'=>md5($userid)));
            }else{
                $departdata=$this->getUserDepartNameANDparentDname($departmentInfo['departmentid']);
                if(!empty($departdata)){
                    $this->updateYxTang(array('flag'=>8,'data'=>$departdata));
                }
                $this->updateYxTang(array('flag'=>12,'data'=>array(array("pNames"=>"珍岛岗位;".$departmentInfo['title'],"pNo"=>md5($departmentInfo['title'])))));
                //$this->updateYxTang(array('flag'=>11,'data'=>array('datas'=>array(array("name"=>$departmentInfo['title'])))));
                $this->updateYxTang(array('flag'=>2,'data'=>array(
                    'userName'=>$departmentInfo['user_name'],
                    'cnName'=>$username,
                    'userNo'=>$departmentInfo['usercode'],
                    //'gradeName'=>$departmentInfo['title'],
                    'postionNo'=>md5($departmentInfo['title']),
                    'orgOuCode'=>trim($departmentid,'H'),
                    'mail'=>$departmentInfo['email1'],
                    'mobile'=>$mobile,
                    'id'=>md5($userid),
                    'isEmailValidated'=>0,
                    'isMobileValidated'=>0,
                )
                ));
            }
            $result=true;
            $message="修改成功！!";
       }while(false);
        //测试系统专用线上不要加
        $result=true;
       return array("success"=>$result,"message"=>$message);
    }


    /**
     *新增部门
     * @param $request {}
     * @return array
     */
    public function insertDepartment($request){
        global $adb;
        $departmentInfo=file_get_contents("php://input");
        $adb->pquery(" INSERT INTO `vtiger_achievementallot_nocalculation` (`contract_no`, `marks`, `date`) VALUES (?,? ,?) ",array("insertDepartment",$departmentInfo,date("Y-m-d H:i:s")));
        $departmentInfo=json_decode($departmentInfo,true);
        do{
            if(empty($departmentInfo['departmentid']) || empty($departmentInfo['departmentname']) || empty($departmentInfo['parentdepartment']) || empty($departmentInfo['depth'])){
                $result=false;
                $message="参数不能为空！";
                break;
            }
            $recordModel = Settings_Departments_Record_Model::getInstanceById($departmentInfo['departmentid']);
            if($recordModel){
                $result=false;
                $message="该记录已存在！";
                break;
            }
            $isExistDepartmentName=$adb->pquery("SELECT * FROM vtiger_departments WHERE departmentname=? AND departmentid<> ? ",array($departmentInfo['departmentname'],$departmentInfo['departmentid']));
            if($adb->num_rows($isExistDepartmentName)>0){
                $result=false;
                $message="部门名称重复！";
                break;
            }
            $parentdepartmentid=$departmentInfo['parentdepartmentid'];
            if($parentdepartmentid){
                $parentRecordModel = Settings_Departments_Record_Model::getInstanceById($parentdepartmentid);
                if(!$parentRecordModel){
                    $result=false;
                    $message="父部门不存在！";
                    break;
                }
                $parentdepartmentid=trim($parentdepartmentid,'H');
            }
            unset($departmentInfo['parentdepartmentid']);
            $sql=" INSERT INTO `vtiger_departments` (`departmentid`, `departmentname`, `parentdepartment`, `depth`, `peopleid`) VALUES (?,?,?,?,?)";
            if(is_array($departmentInfo) ){
                //$values=array_values($departmentInfo);
                $this->updateYxTang(array('flag'=>8,'data'=>array("id"=>trim($departmentInfo['departmentid'],'H'),"ouName"=>$departmentInfo['departmentname'],"parentId"=>trim($parentdepartmentid,'H'))));
                $adb->pquery($sql,array($departmentInfo['departmentid'],$departmentInfo['departmentname'],$departmentInfo['parentdepartment'],$departmentInfo['depth'],$departmentInfo['peopleid']));
                if(mysql_affected_rows()>0){
                    // 企业微信组织架构添加部门
                    $result=$this->setweixincontracts(array('ERPDOIT'=>456321,'flag'=>8,'departmentname'=>$departmentInfo['departmentname'],'departmentid'=>$departmentInfo['departmentid'],'parentid'=>$parentdepartmentid));
                    $result=json_decode($result,true);
                    $result=true;
                    $message="添加成功";
                    break;
                }else{
                    $result=false;
                    $message="添加失败";
                    break;
                }
            }else{
                $result=false;
                $message="缺参数或者多参数";
                break;
            }
        }while(false);
        return array("success"=>$result,"message"=>$message);
    }

    /**
     * 更新部门
     * @param $request  都必填且不为空{"departmentid":H10000,"departmentname":"whatdsfsd","parentdepartment":2,"depth":1,"peopleid":"100"}
     * @return array
     */
    public function updateDepartment($request){
        global  $adb;
        $departmentInfo=file_get_contents("php://input");
        $adb->pquery(" INSERT INTO `vtiger_achievementallot_nocalculation` (`contract_no`, `marks`, `date`) VALUES (?,? ,?) ",array("updateDepartment",$departmentInfo,date("Y-m-d H:i:s")));
        $departmentInfo=json_decode($departmentInfo,true);
        do{
            if($departmentInfo['departmentid']=='H1'){
                if(empty($departmentInfo['departmentid']) || empty($departmentInfo['departmentname']) || empty($departmentInfo['parentdepartment'])){
                    $result=false;
                    $message="参数不能为空！";
                    break;
                }
            }else{
                if(empty($departmentInfo['departmentid']) || empty($departmentInfo['departmentname']) || empty($departmentInfo['parentdepartment']) || empty($departmentInfo['parentdepartment']) ||  empty($departmentInfo['oldparentdepartmentid'])){
                    $result=false;
                    $message="参数不能为空！";
                    break;
                }
                $parentRecordModel=Settings_Departments_Record_Model::getInstanceById($departmentInfo['oldparentdepartmentid']);
                if(empty($parentRecordModel)){
                    $result=false;
                    $message="父部门记录不存在！";
                    break;
                }
            }
            $parentDepartments = explode("::",$departmentInfo['parentdepartment']);
            $key = array_search($departmentInfo['departmentid'],$parentDepartments);

            $recordModel = Settings_Departments_Record_Model::getInstanceById($departmentInfo['departmentid']);
            $parentRole = Settings_Departments_Record_Model::getInstanceById($parentDepartments[$key-1]);
            if(empty($recordModel)){
                $result=false;
                $message="记录不存在！";
                break;
            }
            $adb->pquery("UPDATE vtiger_usermanger set department=? WHERE  departmentid=? ",array($departmentInfo['departmentname'],$departmentInfo['departmentid']));
            // 判断旧的父部门id   和 新的父部门是否一致    或者 旧的部门名称和新的部门名称是否一致如果有不一致则 更新企业微信组织架构
            $recordModel->moveTo($parentRole,$departmentInfo['departmentname'],$departmentInfo['peopleid']);
            $result=true;
            $message="修改成功";
            break;
//            $oldparentdepartment=$recordModel->get("parentdepartment");
//            $olddepartmentname=$recordModel->get("departmentname");
//            $oldParentDepartment=$recordModel->get("parentdepartment");
//            $recordModel->set("departmentname",$departmentInfo['departmentname']);
//            $recordModel->set("parentdepartment",$departmentInfo['parentdepartment']);
//            $recordModel->set("depth",$departmentInfo['depth']);
//            $recordModel->set("peopleid",$departmentInfo['peopleid']);
//            $recordModel->save();
//            $updateRow=mysql_affected_rows();
//            // 判断旧的父部门id   和 新的父部门是否一致    或者 旧的部门名称和新的部门名称是否一致如果有不一致则 更新企业微信组织架构
//            if($updateRow>0){
//                if($departmentInfo['departmentid']=='H1'){
//                    if($olddepartmentname!=$departmentInfo['departmentname']){
//                        $parentdepartmentArray=explode("::",$departmentInfo['parentdepartment']);
//                        array_pop($parentdepartmentArray);
//                        $parentdepartmentid=trim(end($parentdepartmentArray),"H");
//                        $result=$this->setweixincontracts(array('ERPDOIT'=>456321,'flag'=>9,'departmentname'=>$departmentInfo['departmentname'],'departmentid'=>$departmentInfo['departmentid'],'parentid'=>$parentdepartmentid));
//                        $this->updateYxTang(array('flag'=>9,'data'=>array("id"=>trim($departmentInfo['departmentid'],'H'),"ouName"=>$departmentInfo['departmentname'],"parentId"=>trim($parentdepartmentid,'H'))));
//                        $result=true;
//                        $message="修改成功";
//                        break;
//                    }
//                }else{
//                    if(($oldparentdepartment!=$departmentInfo['parentdepartment'])||($olddepartmentname!=$departmentInfo['departmentname']) ){
//                        $parentdepartmentArray=explode("::",$departmentInfo['parentdepartment']);
//                        array_pop($parentdepartmentArray);
//                        $parentdepartmentid=trim(end($parentdepartmentArray),"H");
//                        $this->updateYxTang(array('flag'=>9,'data'=>array("id"=>trim($departmentInfo['departmentid'],'H'),"ouName"=>$departmentInfo['departmentname'],"parentId"=>trim($parentdepartmentid,'H'))));
//                        $result=$this->setweixincontracts(array('ERPDOIT'=>456321,'flag'=>9,'departmentname'=>$departmentInfo['departmentname'],'departmentid'=>$departmentInfo['departmentid'],'parentid'=>$parentdepartmentid));
//                        // 不等 要调用企业微信组织架构部门管理更新父级部门
//                        if($oldParentDepartment!=$departmentInfo['parentdepartment']){
//                            $oldParentDepartment.="%";
//                            //更新下级部门的父部门
//                            $adb->pquery("UPDATE vtiger_departments SET  parentdepartment=CONCAT(?,'::',departmentid) WHERE parentdepartment LIKE ? ",array($departmentInfo['parentdepartment'],$oldParentDepartment));
//                        }
//                        $result=true;
//                        $message="修改成功";
//                        break;
//                    }
//                }
//
//            }
//            $result=true;
//            $message="修改成功";
        }while(false);
        return array("success"=>$result,"message"=>$message);
    }

    /**
     * 删除部门
     * @param $request
     * @return array
     */
    public function deleteDepartment($request){
         global $adb;
         $departmentInfo=file_get_contents("php://input");
        $adb->pquery(" INSERT INTO `vtiger_achievementallot_nocalculation` (`contract_no`, `marks`, `date`) VALUES (?,? ,?) ",array("deleteDepartment",$departmentInfo,date("Y-m-d H:i:s")));
        $departmentInfo =json_decode($departmentInfo,true);
         do{
             $result=$adb->pquery("SELECT * FROM  vtiger_departments WHERE departmentid=? ",array($departmentInfo['departmentid']));
             if($adb->num_rows($result)){
                 $departmentStr="%".$departmentInfo['departmentid']."::"."%";
                 $result=$adb->pquery(" SELECT * FROM  vtiger_departments  WHERE  parentdepartment  LIKE ? ",array($departmentStr));
                 if($adb->num_rows($result)>0){
                     $result=false;
                     $message="该部门有下级部门！";
                     break;
                 }
                 $result=$adb->pquery(" SELECT * FROM vtiger_user2department WHERE departmentid=? ",array($departmentInfo['departmentid']));
                 if($adb->num_rows($result)>0){
                     $result=false;
                     $message="该部门下有用户不得删除！";
                     break;
                 }else{
                     $adb->pquery(" DELETE FROM vtiger_departments WHERE departmentid=? ",array($departmentInfo['departmentid']));
                     $this->updateYxTang(array('flag'=>10,'data'=>array(trim($departmentInfo['departmentid'],'H'))));
                     if(mysql_affected_rows()>0){
                         // 删除企业微信组织架构的部门
                         $result=$this->setweixincontracts(array('ERPDOIT'=>456321,'flag'=>10,'departmentid'=>$departmentInfo['departmentid']));
                         $result=json_decode($result,true);
                        $result=true;
                        $message="删除成功！";
                        break;
                     }else{
                         $result=false;
                         $message="删除失败！";
                         break;
                     }
                 }
             }else{
                 $result=false;
                 $message="该部门记录不存在！";
                 break;
             }
         }while(false);
         return array("success"=>$result,"message"=>$message);
    }

    /**
     * 微信企业号信息
     * @param Vtiger_Request $request
     */
    private function setweixincontracts($data){
        $userkey='c0b3Ke0Q4c%2BmGXycVaQ%2BUEcbU0ldxTBeeMAgUILM0PK5Q59cEp%2B40n6qUSJiPQ';
        //$url = "http://m.crm.71360.com/api.php";
        //$url="http://mtest.crm.71360.com/api.php";
        $url = "http://www.wx2.com/api.php";// 本地开发环境只是测试   组织架构修改能调用
        $ch  = curl_init();
        $data['tokenauth']=$userkey;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $result=curl_exec($ch);
        curl_close($ch);
        return $result;
    }

 public function isZhongxiaoByUserId(Vtiger_Request $request){
        $id = $request->get('id');
        $userid =$request->get('userid');
        $users = getDepartmentUser($id);
        if(in_array($userid,$users)){
            return true;
        }
}


    /**
     * 获取用户ID，上级ID，姓名，等的信息
     * @return array|bool|false|mixed|string
     */
	public function getALLUserINFO(){
         $returnData = Vtiger_Cache::get('ALLUSERData','getALLUserINFO');
         if(!$returnData){
             global $adb;
             $query='SELECT id,reports_to_id,last_name,`status`,usercode FROM vtiger_users WHERE id>1 AND reports_to_id!=id';
             $result=$adb->pquery($query);
             $returnData=array();
             while($row=$adb->fetchByAssoc($result)){
                 $row['id']=(int) $row['id'];
                 $row['reports_to_id']=(int) $row['reports_to_id'];
                 $returnData[]=$row;
             }
             $returnData=json_encode($returnData);
             Vtiger_Cache::set('ALLUSERData','getALLUserINFO',$returnData);
         }
         return $returnData;
     }

    // cxh 2020-07-23 日志记录添加
    public function addLogs($params){
        global $adb;
        $id = $adb->getUniqueId('vtiger_modtracker_basic');
        $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
            array($id, $params['record'],$params['module'], $params['userid'], date('Y-m-d H:i:s'), $params['status']));
        $str='';
        foreach ($params['strArray'] as $key=>$value){
            $str.="(".$id.",'".$value['fieldname']."','".$value['prevalue']."','".$value['postvalue']."'),";
        }
        $str=trim($str,",");
        if($str){
            $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES'.$str, Array());
        }
    }
    public function changePasswd($request){
	    global $adb;
        $userid=$request->get('userid');
        $new_password=$request->get('password');
        $old_password=$request->get('oldpassword');
        if(!is_numeric($userid)){
            return array("success"=>false,"msg"=>'用户ID不能为空');
        }
        if(empty($new_password)){
            return array("success"=>false,"msg"=>'新密码不能为空');
        }
        if(empty($old_password)){
            return array("success"=>false,"msg"=>'原密码不能为空');
        }
        $newUser = new Users();
        $current_user = $newUser->retrieveCurrentUserInfoFromFile($userid);
        $crypt_type = 'PHP5.3MD5';
        $encrypted_old_password = $newUser->encrypt_password($old_password, $crypt_type);
        $result=$adb->pquery('SELECT * FROM vtiger_users WHERE id=?',array($userid));
        if(!$adb->num_rows($result)){
            return array("success"=>false,"msg"=>'用户不存在');
        }
        $sysold_pw=$result->fields['user_password'];
        if($sysold_pw!=$encrypted_old_password){
            return array("success"=>false,"msg"=>'原密码不正确');
        }
        $user_hash = $this->get_user_hash($new_password);
        $crypt_type = 'PHP5.3MD5';
        $encrypted_new_password = $newUser->encrypt_password($new_password, $crypt_type);
        $adb->pquery("UPDATE  vtiger_users SET user_password=?,confirm_password=?,user_hash=?,crypt_type=? WHERE id=? ",array($encrypted_new_password,$encrypted_new_password,$user_hash,$crypt_type,$userid));
        $adb->pquery("UPDATE  vtiger_usermanger SET user_password=?,confirm_password=?,user_hash=?,crypt_type=? WHERE userid=? ",array($encrypted_new_password,$encrypted_new_password,$user_hash,$crypt_type,$userid));
        return array("success"=>true,"msg"=>'修改成功');
    }

    /**
     * 修改密码发邮件
     * @param $id
     * @throws Exception
     */
    public function sendMailToReportto($id)
    {
        global $adb;
        $query = 'SELECT a.last_name AS alastname,a.user_name,a.crypt_type,b.last_name AS blastname,b.email1 AS bemail FROM vtiger_users a LEFT JOIN vtiger_users b ON a.reports_to_id=b.id WHERE a.id=?';
        $result = $adb->pquery($query, array($id));
        if ($adb->num_rows($result)) {
            $resultData = $adb->raw_query_result_rowdata($result, 0);
            $length = rand(6, 14);
            $chars = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h',
                'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's',
                't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D',
                'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O',
                'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
                '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', /*'!',
                '@', '#', '$', '%', '^', '&', '*', '(', ')', '-', '_',
                '[', ']', '{', '}', '<', '>', '~', '`', '+', '=', ',',
                '.', ';', ':', '/', '?', '|'*/);
            $password = '';
            for ($i = 0; $i < $length; $i++) {
                shuffle($chars);
                shuffle($chars);
                shuffle($chars);
                $password .= current($chars);
            }
            $newpasswd = $this->encrypt_password($resultData['user_name'], $password, $resultData['crypt_type']);
            $updateSql = "update vtiger_users set user_password=? where id=?";
            $adb->pquery($updateSql, array($newpasswd, $id));
            $Subject = '离职人员用户密码修改';
            $body = "员工：{$resultData['alastname']}<br>用户名：{$resultData['user_name']}<br>密码：{$password}<br>";
            $address = array(
                array('mail' => $resultData['bemail'], 'name' => $resultData['blastname'])
            );
            Vtiger_Record_Model::sendMail($Subject, $body, $address);
        }
    }
    public  function  getAllUsersByAccount(){
        global $adb;
        $userInfo=file_get_contents("php://input");
        $userInfo=json_decode($userInfo,true);
        if(!isset($userInfo['user_name'])){
            return array("success"=>false,"message"=>'参数必传！',"data"=>'');
        }
        $query='SELECT id,user_name,last_name FROM vtiger_users WHERE user_name LIKE ? ';
        $result=$adb->pquery($query,array("%".$userInfo['user_name']."%"));
        $returnData=array();
        while($row=$adb->fetchByAssoc($result)){
            $returnData[]=$row;
        }
        return array("success"=>true,"message"=>"获取成功！","data"=>$returnData);
    }

    function isContractManager($roleid){
        global $adb;
        $sql = "select profileid from vtiger_role2profile where roleid=? ";
        $result = $adb->pquery($sql,array($roleid));
        if($adb->num_rows($result)){
            while ($row = $adb->fetchByAssoc($result)){
                $data[] = $row['profileid'];
            }
            if(in_array(140,$data)){
                return true;
            }
        }
        return false;
    }
     /**
     * 同步信息云课堂
     * @param $data
     * @throws AMQPChannelException
     * @throws AMQPConnectionException
     * @throws AMQPExchangeException
     */
    public function updateYxTang($data){
		return false;
        /*switch($data['flag']){
            case 1:
            case 2:
                $mqdata=array('dataAction'=>'users','datas'=>array('datas'=>array($data['data'])));
                break;
            case 3:
                $mqdata=array('dataAction'=>'disabledusers','datas'=>array('datas'=>array($data['data'])));
                break;
            case 8:
            case 9:
                $mqdata=array('dataAction'=>'ous','datas'=>array('datas'=>array($data['data'])));
                break;
            case 10:
                $mqdata=array('dataAction'=>'deleteous','datas'=>array('datas'=>$data['data']));
                break;
            case 11:
                $mqdata=array('dataAction'=>'updgrade','datas'=>array('datas'=>$data['data']));
                break;
            case 12:
                $mqdata=array('dataAction'=>'position','datas'=>array('datas'=>$data['data']));
                break;

        }
        if($this->rabbitMQPublisher(json_encode(array("module"=>"UserManger","action"=>"sendYxtangByMessageQuery","mqdata"=>$mqdata)))){
            return false;
        }
        $recordModel=Vtiger_Record_Model::getCleanInstance('UserManger');
        $recordModel->sendYxtangByMessageQuery($mqdata);*/
    }

    /**
     * 取部门ID，名称，父级ID
     * @param $departmentid
     * @return array
     */
    public function getUserDepartNameANDparentDname($departmentid='H1'){
        global $adb;
        $query='SELECT departmentid,parentdepartment,departmentname FROM vtiger_departmentsonline WHERE departmentid=? LIMIT 1';
        //$query='SELECT departmentid,parentdepartment,departmentname FROM vtiger_departments WHERE departmentid=? LIMIT 1';
        $result=$adb->pquery($query,array($departmentid));
        if($adb->num_rows($result)){
            $parentdepartmentid=0;
            if($departmentid!='H1'){
                $parentdepartmentid=$result->fields['parentdepartment'];
                $parentdepartmentid=explode('::',$parentdepartmentid);
                array_pop($parentdepartmentid);
                $parentdepartmentid=end($parentdepartmentid);
            }
            return array("id"=>trim($departmentid,'H'),"ouName"=>$result->fields['departmentname'],"parentId"=>trim($parentdepartmentid,'H'));
        }
        return array();
    }
    //获取当前人所在部门的经理或者总监
    public function managerByCurrentUser($userid,$roleid){
        global $adb,$zhongxiaozongjian,$zhongxiaojingli;
        $sql = "select parentdepartment from vtiger_users a left join vtiger_user2department b on a.id=b.userid 
              left join vtiger_departments c on b.departmentid = c.departmentid
where a.id=?";
        $result = $adb->pquery($sql,array($userid));
        if(!$adb->num_rows($result)){
            return '';
        }
        $row = $adb->fetchByAssoc($result,0);
        $parentdepartment = explode("::",$row['parentdepartment']);
        if($roleid==$zhongxiaojingli){
            $departmentid=$parentdepartment[3];
        }else{
            $departmentid=$parentdepartment[2];
        }
        $sql = "select a.last_name from vtiger_users a left join vtiger_user2role b on a.id=b.userid 
  left join vtiger_user2department c on a.id=c.userid where b.roleid=? and c.departmentid=? and a.status='Active' limit 1";
        $result2 = $adb->pquery($sql,array($roleid,$departmentid));
        if(!$adb->num_rows($result2)){
            return '';
        }
        $row2 = $adb->fetchByAssoc($result2,0);
        return $row2['last_name'];
    }

    public function isChannelUser($departmentid){
        require('crmcache/departmentanduserinfo.php');
        global $channelDepartmentId,$current_user;
        $deparr=$departmentinfo[$channelDepartmentId];
        if(in_array($departmentid,$deparr)){
            return true;
        }
        return false;

    }

    public function getDepartmentIdById($userId){
        $db = PearDatabase::getInstance();
        $result =$db->pquery("select departmentid from vtiger_user2department where userid=?",array($userId));
        if(!$db->num_rows($result)){
            return '';
        }
        $row = $db->fetchByAssoc($result,0);
        return $row['departmentid'];
    }

    public function getUserCompanyId(Vtiger_Request $request){
        $db = PearDatabase::getInstance();
        $sql = 'SELECT companyid FROM vtiger_users WHERE id= ?';
        $result = $db->pquery($sql, array($request->get('userid')));
        $noOfUsers = $db->num_rows($result);
        if (count($noOfUsers) > 0) {
            while ($row = $db->fetch_row($result)) {
                return $row['companyid'];
            }
        }
        return 0;
    }

    public function getAllEmailsByDepartmentId($departmentId){
        $db = PearDatabase::getInstance();
        require('crmcache/departmentanduserinfo.php');
        $deparr=$departmentinfo[$departmentId];
        $address = array();
        $result = $db->pquery("select a.email1,a.last_name from vtiger_users a  left join vtiger_user2department b on a.id=b.userid where b.departmentid in ('".implode("','",$deparr)."')",array());
        while ($row = $db->fetchByAssoc($result)){
            $address[] = array('mail' => $row['email1'], 'name' => $row['last_name']);
        }
        return $address;
    }

    public function isZhongXiaoUser($request){
        require('crmcache/departmentanduserinfo.php');
        global $zhongxiaodepartment;
        $userIds=explode(",",$user2departmentinfo[$zhongxiaodepartment]);
        if(in_array($request->get("userid"),$userIds)){
            return true;
        }
        global $current_user;
        $user = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile($request->get("userid"));
        $matchReceiveModuleRecord = Matchreceivements_Module_Model::getCleanInstance("Matchreceivements");
        if(in_array($current_user->roleid,Matchreceivements_Record_Model::$ZHONGXIAOMANAGER) ||
            $matchReceiveModuleRecord->exportGrouprt('Matchreceivements','chooseRank',$request->get("userid")) ||
            $current_user->is_admin=='on'){
            return true;
        }
        return false;
    }

    #获取所有人
    function getAllUser(Vtiger_Request $request) {
        global $adb;
        // 1. 获取用户及部门
        $sql = "SELECT id, CONCAT( '(',IFNULL(brevitycode,''),')',last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', IF ( `status` = 'Active', '', '[离职]' )) AS last_name FROM vtiger_users WHERE status='Active' and is_admin!='on' ";
        $searchvalue = $request->get("searchvalue");
        if($searchvalue){
            $sql .=  ' and last_name like "%'.$searchvalue.'%"';
        }else{
            $pagecount = $request->get('pagecount');
            $pagenum= $request->get("pagenum");
            $sql .= " limit ".$pagenum.','.$pagecount;
        }

        $res = $adb->pquery($sql, array());
        $temp_user = array();
        if($adb->num_rows($res) > 0){
            for($i=0; $i<$adb->num_rows($res); $i++){
                $row = $adb->fetchByAssoc($res, $i);
                $temp_user[] = array(
                    "key"=>$row['id'],
                    "value"=>$row['last_name'],
                );
            }
        }

        $sql1 = "SELECT count(1) as total FROM vtiger_users WHERE status='Active' and is_admin!='on' ";
        if($searchvalue){
            $sql1 .=  ' and last_name like "%'.$searchvalue.'%"';
        }
        $result = $adb->query($sql1,array());
        $total = $adb->fetchByAssoc($result,0)['total'];
        return array($temp_user,$total);
    }

    /**
     * 获取员工列表（拜访中心调用）
     * @param Vtiger_Request $request
     */
    function getUserListForVisit(Vtiger_Request $request)
    {
        global $adb;
        $pageSize = $request->get('pageSize');
        $pageNum = $request->get('pageNum');
        $name = $request->get('name');
        $limit =' limit '.(($pageNum-1) * $pageSize).','.$pageSize;
        if (!empty($name)) {
            $field = " AND (last_name like '%$name%' OR user_name like '%$name%')";
        }
        $select = "SELECT count(1) as counts";
        $query = " FROM vtiger_users
                    INNER JOIN vtiger_user2department ON vtiger_users.id = vtiger_user2department.userid
                    INNER JOIN vtiger_departments ON vtiger_user2department.departmentid = vtiger_departments.departmentid
                    WHERE vtiger_users.id > 1 AND vtiger_user2department.departmentid != '' AND vtiger_users.isdimission = 0" . $field;
        $result = $adb->pquery($select . $query, []);
        $total = $adb->query_result($result,'counts',0);
        $data = ['list'=>[], 'total'=>0];
        if ($total <= 0) {
            return $data;
        }
        $data['total'] = $total;
        $select = 'SELECT vtiger_users.id, vtiger_users.user_name, vtiger_users.last_name, vtiger_users.email1, vtiger_users.department, vtiger_users.usercode';
        $result = $adb->pquery($select . $query . $limit, []);
        if ($adb->num_rows($result) > 0) {
            $list = [];
            while ($row = $adb->fetchByAssoc($result)) {
                $list[] = [
                    'id' => $row['id'],
                    'username' => $row['user_name'],
                    'realname' => $row['last_name'],
                    'email' => $row['email1'],
                    'department' => $row['department'],
                    'usercode' => $row['usercode']
                ];
            }
            $data['list'] = $list;
        }
        return $data;
    }

    /**
     * 根据账号获取用户信息
     * @param Vtiger_Request $request
     * @return array|null
     */
    public function getUserByUserName(Vtiger_Request $request)
    {
        $db = PearDatabase::getInstance();
        $sql = 'SELECT id, last_name, email1, department, usercode FROM vtiger_users WHERE status=? AND user_name = ?';
        $result = $db->pquery($sql, array('ACTIVE', $request->get('userName')));
        $userInfo = $db->fetchByAssoc($result);
        return $userInfo;
    }

    /**
     * @param Vtiger_Request $request 获取微信id
     */
    public function getWeChatId(Vtiger_Request $request){
        $db = PearDatabase::getInstance();
        $email=$request->get('email');
        $email=explode('|',$email);
        $placeholder=array_map(function($v){return '?';},$email);
        $sql="select wechatid from vtiger_users where status='Active' and isdimission=0 and (email1 IN(".implode(',',$placeholder).") or wechatid IN(".implode(',',$placeholder)."))";
        $emailtemp=array_merge($email,$email);
        $result=$db->pquery($sql,$emailtemp);
        $wechatid=array();
        while($row=$db->fetch_array($result)){
            $wechatid[]=$row['wechatid'];
        }
        $wechatid=array_unique($wechatid);
        $wechatid=implode('|',$wechatid);
        return array(trim($wechatid,'|'));
    }

//合同签订人工号、姓名、合同类型（服务合同、采购合同）、合同编号、合同状态

    public function getServiceContractAndInvoiceByUserId(Vtiger_Request $request){
        $db = PearDatabase::getInstance();
        $userId=$request->get('userId');
        $retrun=array('success'=>true,'errMsg'=>'查询成功');
        $retrun['data']['userId']=$userId;
        $sql="SELECT vtiger_servicecontracts.servicecontractsid as id,'服务合同' AS contract_type,vtiger_servicecontracts.contract_no,vtiger_servicecontracts.modulestatus,vtiger_users.last_name,vtiger_users.usercode FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_servicecontracts.servicecontractsid=vtiger_crmentity.crmid LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid=vtiger_users.id WHERE vtiger_users.id=".$userId." UNION ALL
SELECT vtiger_suppliercontracts.suppliercontractsid as id,'采购合同' AS contract_type,vtiger_suppliercontracts.contract_no,vtiger_suppliercontracts.modulestatus,vtiger_users.last_name,vtiger_users.usercode FROM vtiger_suppliercontracts LEFT JOIN vtiger_crmentity ON vtiger_suppliercontracts.suppliercontractsid=vtiger_crmentity.crmid LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid=vtiger_users.id WHERE vtiger_users.id=".$userId;
        $serviceLists=$db->run_query_allrecords($sql);
        $idArray=array_column($serviceLists,'id');
        $sql="select invoiceno,invoicestatus,modulestatus,contractid from vtiger_newinvoice where contractid in (".implode(',',$idArray).")";
        $invoiceLists=$db->run_query_allrecords($sql);
        $invoiceArray=array();
        foreach ($invoiceLists as $invoiceList){
            $invoiceArray[$invoiceList['contractid']][]=array('invoiceNo'=>$invoiceList['invoiceno'],'invoiceStatus'=>$invoiceList['invoicestatus'],'status'=>$invoiceList['modulestatus']);
        }
        foreach ($serviceLists as $key => $serviceList){
            $retrun['data']['userCode']=$serviceList['usercode'];
            $retrun['data']['name']=$serviceList['last_name'];
            if($invoiceArray[$serviceList['id']]){
                $retrun['data']['contractLists'][]=array('invoiceLists'=>$invoiceArray[$serviceList['id']],'contractNo'=>$serviceList['contract_no'],'status'=>$serviceList['modulestatus'],'type'=>$serviceList['contract_type']);
            }else{
                $retrun['data']['contractLists'][]=array('contractNo'=>$serviceList['contract_no'],'status'=>$serviceList['modulestatus'],'type'=>$serviceList['contract_type']);
            }
        }
        return $retrun;
    }
}
