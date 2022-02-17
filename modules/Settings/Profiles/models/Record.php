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
 * Profiles Record Model Class
 */
class Settings_Profiles_Record_Model extends Settings_Vtiger_Record_Model {

	const PROFILE_FIELD_INACTIVE = 0;
	const PROFILE_FIELD_READONLY = 1;
	const PROFILE_FIELD_READWRITE = 2;
	private static $fieldLockedUiTypes = array('70');

	/**
	 * Function to get the Id
	 * @return <Number> Profile Id
	 */
	public function getId() {
		return $this->get('profileid');
	}
	/**
	 * Function to get the Id
	 * @return <Number> Profile Id
	 */
	protected function setId($id) {
		$this->set('profileid', $id);
		return $this;
	}

	/**
	 * Function to get the Profile Name
	 * @return <String>
	 */
	public function getName() {
		return $this->get('profilename');
	}

	/**
	 * Function to get the description of the Profile
	 * @return <String>
	 */
	public function getDescription() {
		return $this->get('description');
	}

	/**
	 * Function to get the Edit View Url for the Profile
	 * @return <String>
	 */
	public function getEditViewUrl() {
		return '?module=Profiles&parent=Settings&view=Edit&record='.$this->getId();
	}

	/**
	 * Function to get the Edit View Url for the Profile
	 * @return <String>
	 */
	public function getDuplicateViewUrl() {
		return '?module=Profiles&parent=Settings&view=Edit&from_record='.$this->getId();
	}

	/**
	 * Function to get the Detail Action Url for the Profile
	 * @return <String>
	 */
	public function getDeleteAjaxUrl() {
		return '?module=Profiles&parent=Settings&action=DeleteAjax&record='.$this->getId();
	}

	/**
	 * Function to get the Delete Action Url for the current profile
	 * @return <String>
	 */
	public function getDeleteActionUrl() {
		return 'index.php?module=Profiles&parent=Settings&view=DeleteAjax&record='.$this->getId();
	}

	public function getGlobalPermissions() {
		$db = PearDatabase::getInstance();

		if(!$this->global_permissions) {
			$globalPermissions = array();
			$globalPermissions[Settings_Profiles_Module_Model::GLOBAL_ACTION_VIEW] =
				$globalPermissions[Settings_Profiles_Module_Model::GLOBAL_ACTION_EDIT] =
					Settings_Profiles_Module_Model::GLOBAL_ACTION_DEFAULT_VALUE;

			if($this->getId()) {
				$sql = 'SELECT * FROM vtiger_profile2globalpermissions WHERE profileid=?';
				$params = array($this->getId());
				$result = $db->pquery($sql, $params);
				$noOfRows = $db->num_rows($result);
				for($i=0; $i<$noOfRows; ++$i) {
					$actionId = $db->query_result($result, $i, 'globalactionid');
					$permissionId = $db->query_result($result, $i, 'globalactionpermission');
					$globalPermissions[$actionId] = $permissionId;
				}
			}
			$this->global_permissions = $globalPermissions;
		}
		return $this->global_permissions;
	}

	public function hasGlobalReadPermission() {
		$globalPermissions = $this->getGlobalPermissions();
		$viewAllPermission = $globalPermissions[Settings_Profiles_Module_Model::GLOBAL_ACTION_VIEW];
		if($viewAllPermission == Settings_Profiles_Module_Model::IS_PERMITTED_VALUE) {
			return true;
		}
		return false;
	}

	public function hasGlobalWritePermission() {
		$globalPermissions = $this->getGlobalPermissions();
		$editAllPermission = $globalPermissions[Settings_Profiles_Module_Model::GLOBAL_ACTION_EDIT];
		if($this->hasGlobalReadPermission() &&
				$editAllPermission == Settings_Profiles_Module_Model::IS_PERMITTED_VALUE) {
			return true;
		}
		return false;

	}

	public function hasModulePermission($module) {
		$moduleModule = $this->getProfileTabModel($module);
		$modulePermissions = $moduleModule->get('permissions');
		$moduleAccessPermission = $modulePermissions['is_permitted'];
		if(isset($modulePermissions['is_permitted']) && $moduleAccessPermission == Settings_Profiles_Module_Model::IS_PERMITTED_VALUE) {
			return true;
		}
		return false;
	}

	public function hasModuleActionPermission($module, $action) {
		$actionId = false;
		if(is_object($action) && is_a($action, 'Vtiger_Action_Model')) {
			$actionId = $action->getId();
		} else {
			$action = Vtiger_Action_Model::getInstance($action);
			$actionId = $action->getId();
		}
		if(!$actionId) {
			return false;
		}

		$moduleModel = $this->getProfileTabModel($module);
		$modulePermissions = $moduleModel->get('permissions');
		$moduleAccessPermission = $modulePermissions['is_permitted'];
		if($moduleAccessPermission != Settings_Profiles_Module_Model::IS_PERMITTED_VALUE) {
			return false;
		}
		$moduleActionPermissions = $modulePermissions['actions'];
		$moduleActionPermission = $moduleActionPermissions[$actionId];
		if(isset($moduleActionPermissions[$actionId]) && $moduleActionPermission == Settings_Profiles_Module_Model::IS_PERMITTED_VALUE) {
			return true;
		}
		return false;
	}
	
	
	public function showpermission($permissions){
		if(!$this->authlist){
			$this->authlist=Vtiger_Action_Model::$authlist;
		}
		
		//return $this->authlist[$permissions];
		return 2;
	
	
	}
	
	
	//字段是否可见
	public function hasModuleFieldPermission($module, $field) {
		$fieldModel = $this->getProfileTabFieldModel($module, $field);
		$fieldPermissions = $fieldModel->get('permissions');
		if($fieldModel->isViewEnabled() && $fieldPermissions['visible'] == Settings_Profiles_Module_Model::FIELD_ACTIVE) {
			return true;
		}
		return false;
	}

	public function hasModuleFieldWritePermission($module, $field) {
		$fieldModel = $this->getProfileTabFieldModel($module, $field);
		$fieldPermissions = $fieldModel->get('permissions');
		$fieldAccessPermission = $fieldPermissions['visible'];
		$fieldReadOnlyPermission = $fieldPermissions['readonly'];
		if($fieldModel->isEditEnabled()
				&& $fieldAccessPermission == Settings_Profiles_Module_Model::FIELD_ACTIVE
				&& $fieldReadOnlyPermission == Settings_Profiles_Module_Model::FIELD_ACTIVE) {
			return true;
		}
		return false;
	}

	public function getModuleFieldPermissionValue($module, $field) {
		if(!$this->hasModuleFieldPermission($module, $field)) {
			return self::PROFILE_FIELD_INACTIVE;
		} elseif($this->hasModuleFieldWritePermission($module, $field)) {
			return self::PROFILE_FIELD_READWRITE;
		} else {
			return self::PROFILE_FIELD_READONLY;
		}
	}

	public function isModuleFieldLocked($module, $field) {
		$fieldModel = $this->getProfileTabFieldModel($module, $field);
        if(!$fieldModel->isEditable() || $fieldModel->isMandatory()
				|| in_array($fieldModel->get('uitype'),self::$fieldLockedUiTypes)) {
			return true;
		}
		return false;
	}

	public function getProfileTabModel($module) {
		$tabId = false;
		if(is_object($module) && is_a($module, 'Vtiger_Module_Model')) {
			$tabId = $module->getId();
		} else {
			$module = Vtiger_Module_Model::getInstance($module);
			$tabId = $module->getId();
		}
		if(!$tabId) {
			return false;
		}
		$allModulePermissions = $this->getModulePermissions();
		$moduleModel = $allModulePermissions[$tabId];
		return $moduleModel;
	}

	public function getProfileTabFieldModel($module, $field) {
		$profileTabModel = $this->getProfileTabModel($module);
		$fieldId = false;
		if(is_object($field) && is_a($field, 'Vtiger_Field_Model')) {
			$fieldId = $field->getId();
		} else {
			$field = Vtiger_Field_Model::getInstance($field, $profileTabModel);
			$fieldId = $field->getId();
		}
		if(!$fieldId) {
			return false;
		}
		$moduleFields = $profileTabModel->getFields();
		$fieldModel = $moduleFields[$field->getName()];
		return $fieldModel;
	}
	// 获得某权限组对模块的访问权限array(4=>0)
	public function getProfileTabPermissions() {
		if(!$this->profile_tab_permissions) {
			$profile2TabPermissions = array();
			if($this->getId()) {
				$db = PearDatabase::getInstance();
				$sql = 'SELECT * FROM vtiger_profile2tab WHERE profileid=?';
				$params = array($this->getId());
				$result = $db->pquery($sql, $params);
				$noOfRows = $db->num_rows($result);
				for($i=0; $i<$noOfRows; ++$i) {
					$tab = $db->fetchByAssoc($result, $i);
					$profile2TabPermissions[$tab['tabid']] = $tab['permissions'];
				}
			}
			$this->profile_tab_permissions = $profile2TabPermissions;
		}
		return $this->profile_tab_permissions;
	}

	//获取可访问的字段(缓存所有)
	public function getProfileTabFieldPermissions($tabId=0) {
		if(!$this->profile_tab_field_permissions) {
			$profile2TabFieldPermissions = array();
			if($this->getId()) {
				$db = PearDatabase::getInstance();
				$sql = 'SELECT * FROM vtiger_profile2field WHERE profileid=?';
				$params = array($this->getId());
				$result = $db->pquery($sql, $params);
				$noOfRows = $db->num_rows($result);
				for($i=0; $i<$noOfRows; ++$i) {
					$fieldId = $db->query_result($result, $i, 'fieldid');
					$visible = $db->query_result($result, $i, 'visible');
					$readOnly = $db->query_result($result, $i, 'readonly');
					$tid = $db->query_result($result, $i, 'tabid');
					$profile2TabFieldPermissions[$tid][$fieldId]=array('visible' => $visible,'readonly'=> $readOnly);
				}
			}
			$this->profile_tab_field_permissions = $profile2TabFieldPermissions;
		}
		if($tabId>0){
			return $this->profile_tab_field_permissions[$tabId];
		}else{
			return $this->profile_tab_field_permissions;
		}	
	}
	
	//模块菜单
	public function getProfileActionPermissions() {
		if(!$this->profile_action_permissions) {
			$profile2ActionPermissions = array();
			if($this->getId()) {
				$db = PearDatabase::getInstance();
				$sql = 'SELECT * FROM vtiger_profile2standardpermissions WHERE profileid=?';
				$params = array($this->getId());
				$result = $db->pquery($sql, $params);
				$noOfRows = $db->num_rows($result);
				for($i=0; $i<$noOfRows; ++$i) {
					$row=$db->fetchByAssoc($result, $i);
					$profile2ActionPermissions[$row['tabid']][$row['operation']] = $row['permissions'];
				}
			}
			$this->profile_action_permissions = $profile2ActionPermissions;
			}
		return $this->profile_action_permissions;
	}
	
	
	
	public function  getPermissionsbyId($id){
		$this->setId($id);
		return $this->getProfileActionPermissions();
	}

	//按钮权限
	public function getProfileUtilityPermissions() {
		if(!$this->profile_utility_permissions) {
			$profile2UtilityPermissions = array();
			if($this->getId()) {
				$db = PearDatabase::getInstance();
				$sql = 'SELECT * FROM vtiger_profile2utility WHERE profileid=?';
				$params = array($this->getId());
				$result = $db->pquery($sql, $params);
				$noOfRows = $db->num_rows($result);
				for($i=0; $i<$noOfRows; ++$i) {
					$tabId = $db->query_result($result, $i, 'tabid');
					$utility = $db->query_result($result, $i, 'activityid');
					$permissionId = $db->query_result($result, $i, 'permission');
					$profile2UtilityPermissions[$tabId][$utility] = $permissionId;
				}
			}
			$this->profile_utility_permissions = $profile2UtilityPermissions;
		}
		return $this->profile_utility_permissions;
	}
	
	
	/**返回的是module的权限(循环得到访问权限和操作权限)
	 * @return Ambigous <boolean, Vtiger_Module_Model>
	 */
	public function getModulePermissions() {	
		if(!$this->module_permissions) {
			//返回所有可用的模块(去除隐藏的模块)
			$allModules = Vtiger_Module_Model::getAll(array(0), Settings_Profiles_Module_Model::getNonVisibleModulesList());
			//事件模块特殊处理
			$eventModule = Vtiger_Module_Model::getInstance('Events');
			$allModules[$eventModule->getId()] = $eventModule;
			$profileTabPermissions = $this->getProfileTabPermissions();//获取模块权限
			$profileActionPermissions = $this->getProfileActionPermissions();//获取操作权限
			$profileUtilityPermissions = $this->getProfileUtilityPermissions();//按钮权限
			$allTabActions = Vtiger_Action_Model::getAll(true);//获取模块下的操作；
			$allFieldPermissions = $this->getProfileTabFieldPermissions();//权限字段
			foreach($allModules as $id => $moduleModel) {
				$permissions = array();
				if(isset($profileTabPermissions[$id])) {
					$permissions['is_permitted'] = $profileTabPermissions[$id];
				}else{
					$permissions['is_permitted'] = Settings_Profiles_Module_Model::IS_PERMITTED_VALUE;
				}
				$permissions['actions'] = array();
				foreach($allTabActions as $actionModel) {
					$actionId = $actionModel->getId();
					if(isset($profileActionPermissions[$id][$actionId])) {
						$permissions['actions'][$actionId] = $profileActionPermissions[$id][$actionId];
					} elseif(isset($profileUtilityPermissions[$id][$actionId])) {
						$permissions['actions'][$actionId] = $profileUtilityPermissions[$id][$actionId];
					} else {
						$permissions['actions'][$actionId] = Settings_Profiles_Module_Model::NOT_PERMITTED_VALUE;
					}
				}
				$moduleFields = $moduleModel->getFields();
				//print_r($allFieldPermissions);
				foreach($moduleFields as $fieldName => $fieldModel) {
					$fieldPermissions = array();
					$fieldId = $fieldModel->getId();
					$fieldPermissions['visible'] = Settings_Profiles_Module_Model::FIELD_ACTIVE;
					if(isset($allFieldPermissions[$id][$fieldId]['visible'])) {
						$fieldPermissions['visible'] = $allFieldPermissions[$id][$fieldId]['visible'];
					}
					$fieldPermissions['readonly'] = Settings_Profiles_Module_Model::FIELD_ACTIVE;
					if(isset($allFieldPermissions[$id][$fieldId]['readonly'])) {
						$fieldPermissions['readonly'] = $allFieldPermissions[$id][$fieldId]['readonly'];
					}
					$fieldModel->set('permissions', $fieldPermissions);
				}
				$moduleModel->set('permissions', $permissions);
			}
			$this->module_permissions = $allModules;
		}
		return $this->module_permissions;
	}

	
	//清除权限组
	public function delete($transferToRecord) {
		$db = PearDatabase::getInstance();
		$profileId = $this->getId();
		$transferProfileId = $transferToRecord->getId();
		$db->pquery('DELETE FROM vtiger_profile2globalpermissions WHERE profileid=?', array($profileId));
		$db->pquery('DELETE FROM vtiger_profile2tab WHERE profileid=?', array($profileId));
		$db->pquery('DELETE FROM vtiger_profile2standardpermissions WHERE profileid=?', array($profileId));
		$db->pquery('DELETE FROM vtiger_profile2utility WHERE profileid=?', array($profileId));
		$db->pquery('DELETE FROM vtiger_profile2field WHERE profileid=?', array($profileId));
		$checkSql = 'SELECT roleid, count(profileid) AS profilecount FROM vtiger_role2profile
							WHERE roleid IN (select roleid FROM vtiger_role2profile WHERE profileid=?) GROUP BY roleid';
		$checkParams = array($profileId);
		$checkResult = $db->pquery($checkSql, $checkParams);
		$noOfRoles = $db->num_rows($checkResult);
		for($i=0; $i<$noOfRoles; ++$i) {
			$roleId = $db->query_result($checkResult, $i, 'roleid');
			$profileCount = $db->query_result($checkResult, $i, 'profilecount');
			if($profileCount > 1) {
				$sql = 'DELETE FROM vtiger_role2profile WHERE roleid=? AND profileid=?';
				$params = array($roleId, $profileId);
			} else {
				$sql = 'UPDATE vtiger_role2profile SET profileid=? WHERE roleid=? AND profileid=?';
				$params = array($transferProfileId, $roleId, $profileId);
			}
			$db->pquery($sql, $params);
		}

		$db->pquery('DELETE FROM vtiger_profile WHERE profileid=?', array($profileId));
	}
	
	
	/*
	*修改新增不去查询已有权限
	*/
	public function save() {
		$db = PearDatabase::getInstance();
		$modulePermissions = $this->getModulePermissions();//？有什么用？
		$profileName = $this->get('profilename');
		$description = $this->get('description');
		$profilePermissions = $this->get('profile_permissions');
		global $modelinfo;
		$calendarModuleid =$modelinfo['Calendar']['tabid'];
		$eventModuleid = $modelinfo['Events']['tabid'];
		
		$eventFieldsPermissions = $profilePermissions[$eventModuleid]['fields'];
		$profilePermissions[$eventModuleid] = $profilePermissions[$calendarModuleid];
		$profilePermissions[$eventModuleid]['fields'] = $eventFieldsPermissions;

        $isProfileDirectlyRelatedToRole = 0;
		$isNewProfile = false;
        if($this->has('directly_related_to_role')){
            $isProfileDirectlyRelatedToRole = $this->get('directly_related_to_role');
        }
		
		//print_r(Vtiger_Action_Model::getAll(true));
		//exit;
	 	$profileId = $this->getId();
		//新增 还是 更新
	
		if(!$profileId) {
			$profileId = $db->getUniqueId('vtiger_profile');
			$this->setId($profileId);
			$sql = 'INSERT INTO vtiger_profile(profileid, profilename, description, directly_related_to_role) VALUES (?,?,?,?)';
			$params = array($profileId, $profileName, $description, $isProfileDirectlyRelatedToRole);
			$isNewProfile = true;
		} else {
			$sql = 'UPDATE vtiger_profile SET profilename=?, description=?, directly_related_to_role=? WHERE profileid=?';
			$params = array($profileName, $description, $isProfileDirectlyRelatedToRole, $profileId);
			
			$db->pquery('DELETE FROM vtiger_profile2globalpermissions WHERE profileid=?', array($profileId));
			$db->pquery('DELETE FROM vtiger_profile2tab WHERE profileid=?', array($profileId));
			$db->pquery('DELETE FROM vtiger_profile2field WHERE profileid=?',array($profileId));
		}
		$db->pquery($sql, $params);
		//全局的变量为编辑和查看
		$sql = 'INSERT INTO vtiger_profile2globalpermissions(profileid, globalactionid, globalactionpermission) VALUES (?,?,?)';
		$params = array($profileId, Settings_Profiles_Module_Model::GLOBAL_ACTION_VIEW, $this->tranformInputPermissionValue($this->get('viewall')));
		$db->pquery($sql, $params);

		$sql = 'INSERT INTO vtiger_profile2globalpermissions(profileid, globalactionid, globalactionpermission) VALUES (?,?,?)';
		$params = array($profileId, Settings_Profiles_Module_Model::GLOBAL_ACTION_EDIT, $this->tranformInputPermissionValue($this->get('editall')));
		$db->pquery($sql, $params);

		$allModuleModules = Vtiger_Module_Model::getAll(array(0), Settings_Profiles_Module_Model::getNonVisibleModulesList());
		
		$allModuleModules[$eventModuleid] = Vtiger_Module_Model::getInstance('Events');;
		
		
		
		if(count($allModuleModules) > 0) {
			$actionModels = Vtiger_Action_Model::getAll(true);
			foreach($allModuleModules as $tabId => $moduleModel) {
				if($moduleModel->isActive()) { //presence in (0,2)
					//传参确认是否新增
					$this->saveModulePermissions($moduleModel, $profilePermissions[$moduleModel->getId()],$isNewProfile);
				} else {
					$permissions = array();
					$permissions['is_permitted'] = Settings_Profiles_Module_Model::IS_PERMITTED_VALUE;
					if($moduleModel->isEntityModule()) {
						$permissions['actions'] = array();
						foreach($actionModels as $actionModel) {
							if($actionModel->isModuleEnabled($moduleModel)) {
								$permissions['actions'][$actionModel->getId()] = Settings_Profiles_Module_Model::IS_PERMITTED_VALUE;
							}
						}
						$permissions['fields'] = array();
						$moduleFields = $moduleModel->getFields();
						foreach($moduleFields as $fieldModel) {
							if($fieldModel->isEditEnabled()) {
								$permissions['fields'][$fieldModel->getId()] = Settings_Profiles_Record_Model::PROFILE_FIELD_READWRITE;
							} elseif ($fieldModel->isViewEnabled()) {
								$permissions['fields'][$fieldModel->getId()] = Settings_Profiles_Record_Model::PROFILE_FIELD_READONLY;
							} else {
								$permissions['fields'][$fieldModel->getId()] = Settings_Profiles_Record_Model::PROFILE_FIELD_INACTIVE;
							}
						}
					}
					$this->saveModulePermissions($moduleModel, $permissions,$isNewProfile);
					
				}
			}
		}
		if($isNewProfile){
			$this->saveUserAccessbleFieldsIntoProfile2Field();
		}
		
        //$this->recalculate(); 生成所有的用户文档，卡死了。
        return $profileId;
	}
	//保存模块操作权限
	protected function saveModulePermissions($moduleModel, $permissions,$isNewProfile) {
		$db = PearDatabase::getInstance();
		$profileId = $this->getId();
		$tabId = $moduleModel->getId();
		//获取已有操作权限
		//新增模块编辑原有权限插入处理
		if(!$isNewProfile){
			$isNewProfile=$this->isNewActionadd($profileId,$tabId);
		}
		//if(!$isNewProfile){$profileActionPermissions = $this->getProfileActionPermissions();$profileActionPermissions = $profileActionPermissions[$tabId];}else{$profileActionPermissions=array();}
		
		$actionPermissions = array();
		$actionPermissions = $permissions['actions'];
		$actionEnabled = false;	//逻辑验证防止有菜单无权限
		$no_PERMITTED=Settings_Profiles_Module_Model::NOT_PERMITTED_VALUE;
		if($moduleModel->isEntityModule()) {
			if($actionPermissions) {
				$actionIdsList = Vtiger_Action_Model::$standardActions;
				//array('0' => 'Save','1' => 'EditView','2' => 'Delete','3' => 'index','4' => 'DetailView');
				unset($actionIdsList[3]);// 3为标准action中的index，不需要设置
				$actionPermissions[0] = $actionPermissions[1];//编辑和新增相同权限
				//Dividing on actions
				$actionsIdsList = $utilityIdsList = array();
				//分离操作
				foreach($actionPermissions as $actionId => $permission) {
					$permission=$this->tranformInputPermissionValue($permission);
					if(isset($actionIdsList[$actionId])) {
						$actionsIdsList[$actionId] = $permission;
					} else {
						$utilityIdsList[$actionId] = $permission;
					}
				}
				//更新
				if (!$isNewProfile) {
					$actionsUpdateQuery = 'UPDATE vtiger_profile2standardpermissions SET permissions = CASE ';
					foreach ($actionsIdsList as $actionId => $permission) {
							if($permission != $no_PERMITTED) {	//
								$actionEnabled = true;
							}
							$actionsUpdateQuery .= " WHEN operation = $actionId THEN $permission ";
					}
					$actionsUpdateQuery .= 'ELSE permissions END WHERE profileid = ? AND tabid = ?';
					
					if ($actionsIdsList) {
						$db->pquery($actionsUpdateQuery, array($profileId, $tabId));
					}
					//应该是按钮吧，数据库和action要保持一致
					foreach (Vtiger_Action_Model::$utilityActions as $utilityActionId => $utilityActionName) {
						if(!isset($utilityIdsList[$utilityActionId])) {
							$utilityIdsList[$utilityActionId] = $no_PERMITTED;
						}
					}
					//Utility permissions
				 	$utilityUpdateQuery = 'UPDATE vtiger_profile2utility SET permission = CASE ';
					foreach($utilityIdsList as $actionId => $permission) {
						$utilityUpdateQuery .= " WHEN activityid = $actionId THEN $permission ";
					}
					if ($utilityIdsList) {
						$utilityUpdateQuery .= 'ELSE ? END WHERE profileid = ? AND tabid = ?';
						$db->pquery($utilityUpdateQuery, array(1, $profileId, $tabId));
					}
					
					//删除已有字段权限
					$i = 0;
					$count = count($utilityIdsList);
					$utilityInsertQuery .= 'INSERT INTO vtiger_profile2utility(profileid, tabid, activityid, permission) VALUES ';
					foreach($utilityIdsList as $actionId => $permission) {
						$utilityInsertQuery .= "($profileId, $tabId, $actionId, $permission)";
						if ($i !== $count-1) {
							$utilityInsertQuery .= ', ';
						}
						$i++;
					}
					if ($utilityIdsList) {
						$db->pquery($utilityInsertQuery, array());
					}
					
					
					
				} else {
					//新增
					$i = 0;
					$count = count($actionsIdsList);
					$actionsInsertQuery .= 'INSERT INTO vtiger_profile2standardpermissions(profileid, tabid, operation, permissions) VALUES ';
					foreach ($actionsIdsList as $actionId => $permission) {
						$actionsInsertQuery .= "($profileId, $tabId, $actionId, $permission)";
						if ($i !== $count-1) {
							$actionsInsertQuery .= ', ';
						}
						$i++;
					}
					if ($actionsIdsList) {
						$actionEnabled = true;
						$db->pquery($actionsInsertQuery, array());
					}

					//Utility permissions
					$i = 0;
					$count = count($utilityIdsList);
					$utilityInsertQuery .= 'INSERT INTO vtiger_profile2utility(profileid, tabid, activityid, permission) VALUES ';
					foreach($utilityIdsList as $actionId => $permission) {
						$utilityInsertQuery .= "($profileId, $tabId, $actionId, $permission)";
						if ($i !== $count-1) {
							$utilityInsertQuery .= ', ';
						}
						$i++;
					}
					if ($utilityIdsList) {
						$db->pquery($utilityInsertQuery, array());
					}
				}
			} elseif ($this->isRestrictedModule($moduleModel->getName())) {
				//邮件等模块无需权限控制
				$actionEnabled = true;
			}
		} else {
			$actionEnabled = true;
		}

		// Enable module permission in profile2tab table only if either its an extension module or the entity module has atleast 1 action enabled
		if($actionEnabled) {
			$isModulePermitted = $this->tranformInputPermissionValue($permissions['is_permitted']);
		} else {
			$isModulePermitted = $no_PERMITTED;
		}
		$sql = 'INSERT INTO vtiger_profile2tab(profileid, tabid, permissions) VALUES (?,?,?)';
		$params = array($profileId, $tabId, $isModulePermitted);
		$db->pquery($sql, $params);
		//字段权限
		$fieldPermissions = $permissions['fields'];
		if(is_array($fieldPermissions)) {
			$i = 0;
			$count = count($fieldPermissions);
			$fieldsInsertQuery = 'INSERT INTO vtiger_profile2field(profileid, tabid, fieldid, visible, readonly) VALUES ';
			foreach($fieldPermissions as $fieldId => $stateValue) {
				if($stateValue == Settings_Profiles_Record_Model::PROFILE_FIELD_INACTIVE) {
					$visible = Settings_Profiles_Module_Model::FIELD_INACTIVE;
					$readOnly = Settings_Profiles_Module_Model::IS_PERMITTED_VALUE;
				} elseif($stateValue == Settings_Profiles_Record_Model::PROFILE_FIELD_READONLY) {
					$visible = Settings_Profiles_Module_Model::FIELD_ACTIVE;
					$readOnly = Settings_Profiles_Module_Model::FIELD_READONLY;
				} else {
					$visible = Settings_Profiles_Module_Model::FIELD_ACTIVE;
					$readOnly = Settings_Profiles_Module_Model::FIELD_READWRITE;
				}
				$fieldsInsertQuery.= "($profileId, $tabId, $fieldId, $visible, $readOnly)";
				if ($i !== $count-1) {
					$fieldsInsertQuery .= ', ';
				}
				$i++;
			}
			$db->pquery($fieldsInsertQuery, array());
		}
	}
	
	
	//限制提交数据合法
	protected function tranformInputPermissionValue($value) {
		if(!$this->authlist){
			$this->authlist=Vtiger_Action_Model::$authlist;
		}
		if($value == 'on') {
			return Settings_Profiles_Module_Model::IS_PERMITTED_VALUE;
		}elseif(!empty($this->authlist[$value])){
			return  $value;
		} else {
			return Settings_Profiles_Module_Model::NOT_PERMITTED_VALUE;
		}
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
				'linklabel' => 'LBL_DUPLICATE_RECORD',
				'linkurl' => $this->getDuplicateViewUrl(),
				'linkicon' => 'icon-share'
			),
			array(
				'linktype' => 'LISTVIEWRECORD',
				'linklabel' => 'LBL_DELETE_RECORD',
				'linkurl' => "javascript:Settings_Vtiger_List_Js.triggerDelete(event,'".$this->getDeleteActionUrl()."')",
				'linkicon' => 'icon-trash'
			)
		);
		foreach($recordLinks as $recordLink) {
			$links[] = Vtiger_Link_Model::getInstanceFromValues($recordLink);
		}

		return $links;
	}

	public static function getInstanceFromQResult($result, $rowNo=0) {
		$db = PearDatabase::getInstance();
		$row = $db->query_result_rowdata($result, $rowNo);
		$profile = new self();
		return $profile->setData($row);
	}

	/**
	 * Function to get all the profiles linked to the given role
	 * @param <String> - $roleId
	 * @return <Array> - Array of Settings_Profiles_Record_Model instances
	 */
	public static function getAllByRole($roleId) {
		$db = PearDatabase::getInstance();

		$sql = 'SELECT vtiger_profile.*
					FROM vtiger_profile
					INNER JOIN
						vtiger_role2profile ON vtiger_profile.profileid = vtiger_role2profile.profileid
						AND
						vtiger_role2profile.roleid = ?';
		$params = array($roleId);
		$result = $db->pquery($sql, $params);
		$noOfProfiles = $db->num_rows($result);
		$profiles = array();
		for ($i=0; $i<$noOfProfiles; ++$i) {
			$profile = self::getInstanceFromQResult($result, $i);
			$profiles[$profile->getId()] = $profile;
		}
		return $profiles;
	}

	/**
	 * Function to get all the profiles
	 * @return <Array> - Array of Settings_Profiles_Record_Model instances
	 */
	public static function getAll() {
		$db = PearDatabase::getInstance();

		$sql = 'SELECT * FROM vtiger_profile';
		$params = array();
		$result = $db->pquery($sql, $params);
		$noOfProfiles = $db->num_rows($result);
		$profiles = array();
		for ($i=0; $i<$noOfProfiles; ++$i) {
			$profile = self::getInstanceFromQResult($result, $i);
			$profiles[$profile->getId()] = $profile;
		}
		return $profiles;
	}

	/**
	 * Function to get the instance of Profile model, given profile id
	 * @param <Integer> $profileId
	 * @return Settings_Profiles_Record_Model instance, if exists. Null otherwise
	 */
	public static function getInstanceById($profileId) {
		$db = PearDatabase::getInstance();

		$sql = 'SELECT * FROM vtiger_profile WHERE profileid = ?';
		$params = array($profileId);
		$result = $db->pquery($sql, $params);
		if($db->num_rows($result) > 0) {
			return self::getInstanceFromQResult($result);
		}
		return null;
	}
	

    public static function getInstanceByName($profileName , $checkOnlyDirectlyRelated=false, $excludedRecordId = array()) {
        $db = PearDatabase::getInstance();
        $query = 'SELECT * FROM vtiger_profile WHERE profilename=?';
        $params = array($profileName);
        if($checkOnlyDirectlyRelated) {
            $query .=' AND directly_related_to_role=1';
        }
		if(!empty($excludedRecordId)) {
           $query .= ' AND profileid NOT IN ('.generateQuestionMarks($excludedRecordId).')';
           $params = array_merge($params,$excludedRecordId);
       }
	   
        $result = $db->pquery($query, $params);
        if($db->num_rows($result)> 0 ){
            return self::getInstanceFromQResult($result);
        }
        return null;
    }

	/**
	 * Function to get the Detail Url for the current group
	 * @return <String>
	 */
    public function getDetailViewUrl() {
        return '?module=Profiles&parent=Settings&view=Detail&record=' . $this->getId();
    }

	/**
	 * Function to check whether the profiles is directly related to role
	 * @return Boolean
	 */
    public function isDirectlyRelated() {
		$isDirectlyRelated = $this->get('directly_related_to_role');
		if($isDirectlyRelated == 1){
			return true;
		} else {
			return false;
		}
    }

	/**
	 * Function to check whether module is restricted for to show actions and field access
	 * @param <String> $moduleName
	 * @return <boolean> true/false
	 */
	public function isRestrictedModule($moduleName) {
		return in_array($moduleName, array('Emails'));
	}

	/**
	 * Function recalculate the sharing rules
	 */
	public function recalculate() {
		set_time_limit(vglobal('php_max_execution_time'));
		require_once('modules/Users/CreateUserPrivilegeFile.php');

		$userIdsList = $this->getUsersList();
		if ($userIdsList) {
			foreach ($userIdsList as $userId) {
				createUserPrivilegesfile($userId);
			}
		}
	}

	/**
	 * Function to get Users list from this Profile
	 * @param <Boolean> $allUsers
	 * @return <Array> list of user ids
	 */
	public function getUsersList($allUsers = false) {
		$db = PearDatabase::getInstance();
		$params = array(0);
		$query = 'SELECT id FROM vtiger_users
					INNER JOIN vtiger_user2role ON vtiger_user2role.userid = vtiger_users.id
					INNER JOIN vtiger_role2profile ON vtiger_role2profile.roleid = vtiger_user2role.roleid
					WHERE vtiger_users.deleted = ?';

		if (!$allUsers) {
			$query .= ' AND vtiger_role2profile.profileid = ?';
			$params[] = $this->getId();
		}
		$result = $db->pquery($query, $params);
		$numOfRows = $db->num_rows($result);

		$userIdsList = array();
		for($i=0; $i<$numOfRows; $i++) {
			$userIdsList[] = $db->query_result($result, $i, 'id');
		}
		return $userIdsList;
	}
	
	/**
	 * Function to save user fields in vtiger_profile2field table
	 * We need user field values to generating the Email Templates variable valuues.
	 * @param type $profileId
	 */
	public function saveUserAccessbleFieldsIntoProfile2Field(){
		$profileId = $this->getId();
		if(!empty($profileId)){
			$db = PearDatabase::getInstance();
			$userRecordModel = Users_Record_Model::getCurrentUserModel();
			$module = $userRecordModel->getModuleName();
			$tabId = getTabid($module);
			$userModuleModel = Users_Module_Model::getInstance($module);
			$moduleFields = $userModuleModel->getFields();

			$userAccessbleFields = array();
			$skipFields = array(98,115,116,31,32);
			foreach ($moduleFields as $fieldName => $fieldModel) {
				if($fieldModel->getFieldDataType() == 'string' || $fieldModel->getFieldDataType() == 'email' || $fieldModel->getFieldDataType() == 'phone') {
					if(!in_array($fieldModel->get('uitype'), $skipFields) && $fieldName != 'asterisk_extension'){
						$userAccessbleFields[$fieldModel->get('id')] .= $fieldName;
					}
				}
			}

			//Added user fields into vtiger_profile2field and vtiger_def_org_field
			//We are using this field information in Email Templates.
			foreach ($userAccessbleFields as $fieldId => $fieldName) {
				$insertQuery = 'INSERT INTO vtiger_profile2field VALUES(?,?,?,?,?)';
				$db->pquery($insertQuery, array($profileId, $tabId, $fieldId,  Settings_Profiles_Module_Model::FIELD_ACTIVE, Settings_Profiles_Module_Model::FIELD_READWRITE));
			}
			
			$sql = 'SELECT fieldid FROM vtiger_def_org_field WHERE tabid = ?';
			$result1 = $db->pquery($sql, array($tabId));
			$def_org_fields = array();
			for($j=0; $j<$db->num_rows($result1); $j++) {
				array_push($def_org_fields, $db->query_result($result1, $j, 'fieldid'));
			}
			foreach ($userAccessbleFields as $fieldId => $fieldName) {
				if(!in_array($fieldId, $def_org_fields)){
					$insertQuery = 'INSERT INTO vtiger_def_org_field VALUES(?,?,?,?)';
					$db->pquery($insertQuery, array($tabId,$fieldId,0,0));
				}
			}
		}
	}
	
	//增加模块后编辑原有权限需要判断  Edit By Joe @20150418
	public function isNewActionadd($profileid,$actionid){
		$db = PearDatabase::getInstance();
		$sql = 'SELECT * FROM vtiger_profile2standardpermissions WHERE profileid=? and tabid=?';
		$params = array($profileid,$actionid);
		$result = $db->pquery($sql, $params);
		$noOfRows = $db->num_rows($result);
		if($noOfRows>0){
			return false;
		}
		return true;	
	}
}