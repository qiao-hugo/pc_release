<?php

/**
 * Roles Record Model Class
 */
class Settings_Adminaccess_Record_Model extends Settings_Vtiger_Record_Model {

	/**
	 * Function to get the Id
	 * @return <Number> Group Id
	 */
	public function getId() {
		return $this->get('id');
	}

	public function getuids() {
		$userid=$this->get('userid');
		if(empty($userid)){
			return array();
		}	
		return explode(',',$userid);
	}

	public function getactions() {
		$userid=$this->get('setting');
		if(empty($userid)){
			return array();
		}	
		return explode(',',$userid);
	}

	public function setuids($userid) {
		return $this->set('userid', $userid);	
	}
	/**
	 * Function to set the Id
	 * @param <Number> Group Id
	 * @return <Settings_Groups_Reord_Model> instance
	 */
	public function setId($id) {
		return $this->set('id', $id);
	}

	/**
	 * Function to get the Group Name
	 * @return <String>
	 */
	public function getName() {
		return $this->get('groupname');
	}

	/**
	 * Function to get the description of the group
	 * @return <String>
	 */
	public function getDescription() {
		return $this->get('description');
	}

	/**
	 * Function to get the Edit View Url for the Group
	 * @return <String>
	 */
	public function getEditViewUrl() {
		return '?module=Adminaccess&parent=Settings&view=Edit&record='.$this->getId();
	}

	/**
	 * Function to get the Delete Action Url for the current group
	 * @return <String>
	 */
	public function getDeleteActionUrl() {
		return 'index.php?module=Adminaccess&parent=Settings&action=DeleteAjax&record='.$this->getId();
	}
    
    /**
	 * Function to get the Detail Url for the current group
	 * @return <String>
	 */
    public function getDetailViewUrl() {
        return '?module=Adminaccess&parent=Settings&view=Detail&record='.$this->getId();
    }

	/**
	 * Function to get all the members of the groups
	 * @return <Array> Settings_Profiles_Record_Model instances
	 */
	public function getMembers() {
		if (!$this->members) {
			$this->members = Settings_Groups_Member_Model::getAllByGroup($this);
		}
		return $this->members;
	}

	/**
	 * 保存和更新分组信息
	 */
	public function save() {
		$db = PearDatabase::getInstance();
		$groupId = $this->getId();
		$mode = 'edit';
		if (empty($groupId)) {
			$mode = '';
			$groupId = $db->getUniqueId('vtiger_user2setting');
			$this->setId($groupId);
		}

		if ($mode == 'edit') {
			$sql = 'UPDATE vtiger_user2setting SET groupname=?, description=?,userid=?, setting=? WHERE id=?';
			$params = array($this->getName(), $this->getDescription(),implode(',',$this->get('userid')),implode(',',$this->get('setting')),$groupId);
		} else {
			$sql = 'INSERT INTO vtiger_user2setting(id,groupname, description,userid,setting) VALUES (?,?,?,?,?)';
			$params = array($groupId,$this->getName(), $this->getDescription(),implode(',',$this->get('userid')),implode(',',$this->get('setting')));
		}
		$result=$db->pquery($sql, $params);
		$this->makeAccess();
	}

	/**
	 * Function to recalculate user priviliges files
	 * @param <Array> $oldUsersList
	 */
	public function recalculate($oldUsersList) {
		set_time_limit(vglobal('php_max_execution_time'));
		require_once('modules/Users/CreateUserPrivilegeFile.php');

		$userIdsList = array();
		foreach ($oldUsersList as $userId => $userRecordModel) {
			$userIdsList[$userId] = $userId;
		}

		$this->members = null;
		foreach ($this->getUsersList(true) as $userId => $userRecordModel) {
			$userIdsList[$userId] = $userId;
		}

		foreach ($userIdsList as $userId) {
			createUserPrivilegesfile($userId);
		}
	}

	/**
	 * Function to get all users related to this group
	 * @param <Boolean> $nonAdmin true/false
	 * @return <Array> Users models list <Users_Record_Model>
	 */
	public function getUsersList($nonAdmin = false) {
		$userIdsList = $usersList = array();
		$members = $this->getMembers();

		foreach ($members['Users'] as $memberModel) {
			$userId = $memberModel->get('userId');
			$userIdsList[$userId] = $userId;
		}

		foreach ($members['Groups'] as $memberModel) {
			$groupModel = Settings_Groups_Record_Model::getInstance($memberModel->get('groupId'));
			$groupMembers = $groupModel->getMembers();

			foreach ($groupMembers['Users'] as $groupMemberModel) {
				$userId = $groupMemberModel->get('userId');
				$userIdsList[$userId] = $userId;
			}
		}

		foreach ($members['Roles'] as $memberModel) {
			$roleModel = new Settings_Roles_Record_Model();
			$roleModel->set('roleid', $memberModel->get('roleId'));

			$roleUsers = $roleModel->getUsers();
			foreach ($roleUsers as $userId => $userRecordModel) {
				$userIdsList[$userId] = $userId;
			}
		}

		foreach ($members['RoleAndSubordinates'] as $memberModel) {
			$roleModel = new Settings_Roles_Record_Model();
			$roleModel->set('roleid', $memberModel->get('roleId'));

			$roleUsers = $roleModel->getUsers();
			foreach ($roleUsers as $userId => $userRecordModel) {
				$userIdsList[$userId] = $userId;
			}
		}

		if (array_key_exists(1, $userIdsList)) {
			unset($userIdsList[1]);
		}

		foreach ($userIdsList as $userId) {
			$userRecordModel = Users_Record_Model::getInstanceById($userId, 'Users');
			if ($nonAdmin && $userRecordModel->isAdminUser()) {
				continue;
			}
			$usersList[$userId] = $userRecordModel;
		}
		return $usersList;
	}

	protected function transferOwnership($transferToGroup) {
		$db = PearDatabase::getInstance();
		$groupId = $this->getId();
		$transferGroupId = $transferToGroup->getId();

		$query = 'UPDATE vtiger_crmentity SET smownerid=? WHERE smownerid=?';
		$params = array($transferGroupId, $groupId);
		$db->pquery($query, $params);

		if (Vtiger_Utils::CheckTable('vtiger_customerportal_prefs')) {
			$query = 'UPDATE vtiger_customerportal_prefs SET prefvalue = ? WHERE prefkey = ? AND prefvalue = ?';
			$params = array($transferGroupId, 'defaultassignee', $groupId);
			$db->pquery($query, $params);

			$query = 'UPDATE vtiger_customerportal_prefs SET prefvalue = ? WHERE prefkey = ? AND prefvalue = ?';
			$params = array($transferGroupId, 'userid', $groupId);
			$db->pquery($query, $params);
		}
	}

	/**
	 * Function to delete the group
	 * @param <Settings_Groups_Record_Model> $transferToGroup
	 */
	public function delete() {
		$db = PearDatabase::getInstance();
		$groupId = $this->getId();
		$db->pquery('DELETE FROM vtiger_user2setting WHERE id=?', array($groupId));
		$this->makeAccess();
	}

	/**
	 * Function to get the list view actions for the record
	 * @return <Array> - Associate array of Vtiger_Link_Model instances
	 */
	public function getRecordLinks() {

		$links = array();
		$recordLinks = array(
			array(
				'linktype' => 'LISTVIEWRECORD',
				'linklabel' => 'LBL_EDIT_RECORD',
				'linkurl' => $this->getEditViewUrl(),
				'linkicon' => 'icon-pencil'
			),
			array(
				'linktype' => 'LISTVIEWRECORD',
				'linklabel' => 'LBL_DELETE_RECORD',
				'linkurl' => "javascript:Settings_Vtiger_List_Js.triggerDelete(event,'".$this->getDeleteActionUrl()."')",
				'linkicon' => 'icon-trash'
			)
		);
		foreach ($recordLinks as $recordLink) {
			$links[] = Vtiger_Link_Model::getInstanceFromValues($recordLink);
		}

		return $links;
	}

	/**
	 * Function to get the instance of Groups record model from query result
	 * @param <Object> $result
	 * @param <Number> $rowNo
	 * @return Settings_Groups_Record_Model instance
	 */
	public static function getInstanceFromQResult($result, $rowNo) {
		$db = PearDatabase::getInstance();
		$row = $db->query_result_rowdata($result, $rowNo);
		$role = new self();
		return $role->setData($row);
	}

	/**
	 * Function to get all the groups
	 * @return <Array> - Array of Settings_Groups_Record_Model instances
	 */


	public static function getInstance($value) {
		$db = PearDatabase::getInstance();
		if (Vtiger_Utils::isNumber($value)) {
			$sql = 'SELECT * FROM vtiger_user2setting WHERE id = ?';
			$params = array($value);
			$result = $db->pquery($sql, $params);
			if ($db->num_rows($result) > 0) {
				return self::getInstanceFromQResult($result, 0);
			}
		}
		return null;
	}


	private function makeAccess(){
		$db = PearDatabase::getInstance();
		$result = $db->pquery('SELECT userid,setting FROM vtiger_user2setting',array());
		$noOfMenus=$db->num_rows($result);
		$list=array();
		if ($noOfMenus> 0) {
			for($i=0; $i<$noOfMenus; ++$i) {
				$rowData = $db->query_result_rowdata($result, $i);
				$ids=explode(',',$rowData['userid']);
				foreach ($ids as $value) {
					$setting=explode(',',$rowData['setting']);
					foreach ($setting as  $settingid) {
						if(!in_array($settingid,$list[$value])){
							$list[$value][]=$settingid;
						}
						
					}
					
				}
			}

			foreach ($list as $key => $value) {
				$list[$key]=implode(',',$value);
			}


			

			global $root_directory;

			$hander=@fopen($root_directory.'crmcache/adminaccess.php', 'w+');
			if($hander){
				$str="<?php\n\n";
				$str.="\$adminaccess=".var_export($list,true).";\n";
				$str.="?>";
				fputs($hander, $str);
				fclose($hander);
			}


			error_reporting(E_ALL);
			
			$result = $db->pquery('SELECT fieldid,modulename FROM vtiger_settings_field',array());
			$noOfMenus=$db->num_rows($result);
			$modulenames=array();
			if ($noOfMenus> 0) {
				for($i=0; $i<$noOfMenus; ++$i) {
					$rowData = $db->query_result_rowdata($result, $i);
					$modulenames[$rowData['fieldid']]=$rowData['modulename'];
				}
				foreach ($list as $key => $value) {
						$moduleids=explode(',',$value);
						foreach ($moduleids as $model) {
							$lists[$key][]=$modulenames[$model];
						}
				}

				$hander=@fopen($root_directory.'crmcache/module2access.php', 'w+');
				if($hander){
					$str="<?php\n\n";
					$str.="\$module2access=".var_export($lists,true).";\n";
					$str.="?>";
					fputs($hander, $str);
					fclose($hander);
				}
			}
		}
	
	}

}