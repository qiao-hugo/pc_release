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
 * Sharng Access Vtiger Module Model Class
 */
class Settings_SharingAccess_Rule_Model extends Vtiger_Base_Model {

	const RULE_TYPE_GROUPS = 'GRP';
	const RULE_TYPE_ROLE = 'ROLE';
	const RULE_TYPE_ROLE_AND_SUBORDINATES = 'RS';

	const READ_ONLY_PERMISSION = 0;
	const READ_WRITE_PERMISSION = 1;

	static $allPermissions = array (
		self::READ_ONLY_PERMISSION => 'Read Only',
		self::READ_WRITE_PERMISSION => 'Read Write'
	);

	static $ruleMemberToRelationMapping = array (
		self::RULE_TYPE_GROUPS => Settings_SharingAccess_RuleMember_Model::RULE_MEMBER_TYPE_GROUPS,
		self::RULE_TYPE_ROLE => Settings_SharingAccess_RuleMember_Model::RULE_MEMBER_TYPE_ROLES,
		self::RULE_TYPE_ROLE_AND_SUBORDINATES => Settings_SharingAccess_RuleMember_Model::RULE_MEMBER_TYPE_ROLE_AND_SUBORDINATES
	);

	static $dataShareTableColArr = array (
		self::RULE_TYPE_GROUPS => array (
			self::RULE_TYPE_GROUPS => array (
				'table' => 'vtiger_datashare_grp2grp',
				'source_id' => 'share_groupid',
				'target_id' => 'to_groupid'
			),
			self::RULE_TYPE_ROLE => array (
				'table' => 'vtiger_datashare_grp2role',
				'source_id' => 'share_groupid',
				'target_id' => 'to_roleid'
			),
			self::RULE_TYPE_ROLE_AND_SUBORDINATES => array (
				'table' => 'vtiger_datashare_grp2rs',
				'source_id' => 'share_groupid',
				'target_id' => 'to_roleandsubid'
			),
		),
		self::RULE_TYPE_ROLE => array (
			self::RULE_TYPE_GROUPS => array (
				'table' => 'vtiger_datashare_role2group',
				'source_id' => 'share_roleid',
				'target_id' => 'to_groupid'
			),
			self::RULE_TYPE_ROLE => array (
				'table' => 'vtiger_datashare_role2role',
				'source_id' => 'share_roleid',
				'target_id' => 'to_roleid'
			),
			self::RULE_TYPE_ROLE_AND_SUBORDINATES => array (
				'table' => 'vtiger_datashare_role2rs',
				'source_id' => 'share_roleid',
				'target_id' => 'to_roleandsubid'
			),
		),
		self::RULE_TYPE_ROLE_AND_SUBORDINATES => array (
			self::RULE_TYPE_GROUPS => array (
				'table' => 'vtiger_datashare_rs2grp',
				'source_id' => 'share_roleandsubid',
				'target_id' => 'to_groupid'
			),
			self::RULE_TYPE_ROLE => array (
				'table' => 'vtiger_datashare_rs2role',
				'source_id' => 'share_roleandsubid',
				'target_id' => 'to_roleid'
			),
			self::RULE_TYPE_ROLE_AND_SUBORDINATES => array (
				'table' => 'vtiger_datashare_rs2rs',
				'source_id' => 'share_roleandsubid',
				'target_id' => 'to_roleandsubid'
			),
		),
	);

	/**
	 * Function to get the Id of the Sharing Access Rule
	 * @return <Number> Id
	 */
	public function getId() {
		return $this->get('shareid');
	}

	public function getRuleType() {
		$idComponents = $this->getIdComponents();
		if($idComponents && count($idComponents) > 0) {
			return $idComponents[0];
		}
		return false;
	}
	public function getCompanyID(){
	    global $adb;
        $query='SELECT * FROM vtiger_datashare_role2depart WHERE shareid=? limit 1';
        $result=$adb->pquery($query,array($this->get('shareid')));
        $companyid='';
        if($adb->num_rows($result)){
            $companyid = $adb->query_result($result, 0, 'companyid');
        }
	    return explode(',',$companyid);
    }

	public function setModule($moduleName) {
		$module = Settings_SharingAccess_Module_Model::getInstance($moduleName);
		$this->module = $module;
		return $this;
	}

	public function setModuleFromInstance($module) {
		$this->module = $module;
		return $this;
	}

	/**
	 * Function to get the Group Name
	 * @return <String>
	 */
	public function getModule() {
		return $this->module;
	}

	protected function getRuleComponents() {
		if(!$this->rule_details && $this->getId()) {
			$db = PearDatabase::getInstance();

			//$relationTypeComponents = explode('::', $this->get('relationtype'));
			//$sourceType = '';
			//$targetType = $relationTypeComponents[1];

			//$tableColumnInfo = self::$dataShareTableColArr[$sourceType][$targetType];
			$tableName ='vtiger_datashare_role2depart';
			$sourceColumnName = 'share_roleid';
			$targetColumnName = 'to_departmentid';
			$sql = 'SELECT * FROM '.$tableName.' WHERE shareid = ?';
			$params = array($this->getId());
			$result = $db->pquery($sql, $params);
			if($db->num_rows($result)) {
				$sourceId = $db->query_result($result, 0, $sourceColumnName);
				//$sourceMemberType = 'Groups';
				//$qualifiedSourceId = Settings_SharingAccess_RuleMember_Model::getQualifiedId($sourceMemberType, str_replace($sourceMemberType, '', $sourceId));
				$sourceMember = Settings_SharingAccess_RuleMember_Model::getInstance($sourceId);
				$this->rule_details['source_member'] = $sourceMember;

				$targetId = $db->query_result($result, 0, $targetColumnName);
				//$targetMemberType = '';
				//$qualifiedTargetId = Settings_SharingAccess_RuleMember_Model::getQualifiedId($targetMemberType, $targetId);
				$targetMember = Settings_SharingAccess_RuleMember_Model::getInstance('Department:'.$targetId);
				$this->rule_details['target_member'] = $targetMember;
				//$this->rule_details['target_member'] = $targetMember;
				$this->rule_details['permission'] = $db->query_result($result, 0, 'permission');
                $companyid=$db->query_result($result, 0, 'companyid');
                $companyids = Settings_SharingAccess_RuleMember_Model::getInstance('companyid:'.$companyid);
				$this->rule_details['companyid'] = $companyids;
				//print_r($this->rule_details);
			}

		}
		return $this->rule_details;
	}

	public function getSourceMember() {
		if($this->getId()) {
			$ruleComponents = $this->getRuleComponents();
			return $ruleComponents['source_member'];
		}
		return false;
	}

	public function getTargetMember() {
		if($this->getId()) {
			$ruleComponents = $this->getRuleComponents();
			return $ruleComponents['target_member'];
		}
		return false;
	}

	public function getPermission() {
		if($this->getId()) {
			$ruleComponents = $this->getRuleComponents();
			return $ruleComponents['permission'];
		}
		return false;
	}
    public function getCompanyName() {
        if($this->getId()) {
            $ruleComponents = $this->getRuleComponents();
            return $ruleComponents['companyid'];
        }
		return false;
	}

	public function isReadOnly() {
		if($this->getId()) {
			$permission = $this->getPermission();
			return ($permission == self::READ_ONLY_PERMISSION);
		}
		return false;
	}

	public function isReadWrite() {
		if($this->getId()) {
			$permission = $this->getPermission();
			return ($permission == self::READ_WRITE_PERMISSION);
		}
		return false;
	}

	public function getEditViewUrl() {
		return '?module=SharingAccess&parent=Settings&view=IndexAjax&mode=editRule&for_module='.$this->getModule()->getId().'&record='.$this->getId();
	}

	public function getDeleteActionUrl() {
		return '?module=SharingAccess&parent=Settings&action=IndexAjax&mode=deleteRule&for_module='.$this->getModule()->getId().'&record='.$this->getId();
	}
	
	/**
	 * Function to get the detailViewUrl for the rule member in Sharing Access Custom Rules
	 * @return DetailViewUrl
	 */
	public function getSourceDetailViewUrl() {
		$sourceMember = $this->getSourceMember()->getId();
		$sourceMemberDetails = explode(':', $sourceMember);
		
		if($sourceMemberDetails[0] == 'Groups') {
			return 'index.php?parent=Settings&module=Groups&view=Detail&record='.$sourceMemberDetails[1];
		} else if($sourceMemberDetails[0] == 'Roles') {
			return 'index.php?parent=Settings&module=Roles&view=Edit&record='.$sourceMemberDetails[1];
		} else if($sourceMemberDetails[0] == 'RoleAndSubordinates') {
			return 'index.php?parent=Settings&module=Roles&view=Edit&record='.$sourceMemberDetails[1];
		}
	}
	
	/**
	 * Function to get the detailViewUrl for the rule member in Sharing Access Custom Rules
	 * @return DetailViewUrl
	 */
	public function getTargetDetailViewUrl() {
		$targetMember = $this->getTargetMember()->getId();
		$targetMemberDetails = explode(':', $targetMember);
		
		if($targetMemberDetails[0] == 'Groups'){
			return 'index.php?parent=Settings&module=Groups&view=Detail&record='.$targetMemberDetails[1];
		} else if($targetMemberDetails[0] == 'Roles') {
			return 'index.php?parent=Settings&module=Roles&view=Edit&record='.$targetMemberDetails[1];
		} else if($targetMemberDetails[0] == 'RoleAndSubordinates') {
			return 'index.php?parent=Settings&module=Roles&view=Edit&record='.$targetMemberDetails[1];
		}
	}
	
	/**
	 * Function to get the Member Name from the Rule Model
	 * @return Name of the rule Member
	 */
	public function getSourceMemberName() {
		$sourceMember = $this->getSourceMember()->getId();
		$sourceMemberDetails = explode(':', $sourceMember);
		return $sourceMemberDetails[0];
	}
	
	/**
	 * Function to get the Member Name from the Rule Model
	 * @return Name of the rule Member
	 */
	public function getTargetMemberName() {
		$targetMember = $this->getTargetMember()->getId();
		$targetMemberDetails = explode(':', $targetMember);
		return $targetMemberDetails[0];
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
				'linkurl' => 'javascript:app.showModalWindow(null, "'.$this->getEditViewUrl().'");',
				'linkicon' => 'icon-pencil'
			),
			array(
				'linktype' => 'LISTVIEWRECORD',
				'linklabel' => 'LBL_DELETE_RECORD',
				'linkurl' => 'javascript:app.showModalWindow(null, "'.$this->getDeleteActionUrl().'");',
				'linkicon' => 'icon-trash'
			)
		);
		foreach($recordLinks as $recordLink) {
			$links[] = Vtiger_Link_Model::getInstanceFromValues($recordLink);
		}

		return $links;
	}

	public function save() {
	//保存共享规则
		$db = PearDatabase::getInstance();
		$ruleId = $this->getId();
		$tableName = 'vtiger_datashare_role2depart';
		if(!$ruleId) {
			$ruleId = $db->getUniqueId('vtiger_datashare_module_rel');
			$this->set('shareid', $ruleId);
			$db->pquery("INSERT INTO vtiger_datashare_module_rel(shareid, tabid) VALUES(?,?)",
					array($ruleId, $this->getModule()->getId()));
		} else {
			//$relationTypeComponents = explode('::', $this->get('relationtype'));
			//$sourceType = $relationTypeComponents[0];
			//$targetType = $relationTypeComponents[1];
			//$tableColumnInfo = self::$dataShareTableColArr[$sourceType][$targetType];
			//$tableName = $tableColumnInfo['table'];
			//$sourceColumnName = $tableColumnInfo['source_id'];
			//$targetColumnName = $tableColumnInfo['target_id'];
			$db->pquery("DELETE FROM $tableName WHERE shareid=?", array($ruleId));
		}
		$sourceId = $this->get('source_id');
		//$sourceIdComponents = Settings_SharingAccess_RuleMember_Model::getIdComponentsFromQualifiedId($sourceId);
		//$sourceType = array_search($sourceIdComponents[0], self::$ruleMemberToRelationMapping);
		$targetId = $this->get('target_id');
		$companyid = $this->get('companyid');
		//$targetIdComponents = Settings_SharingAccess_RuleMember_Model::getIdComponentsFromQualifiedId($targetId);
		//$targetType = array_search($targetIdComponents[0], self::$ruleMemberToRelationMapping);
		//$tableColumnName = self::$dataShareTableColArr[$sourceType][$targetType];
		
		$sourceColumnName = 'share_roleid';
		$targetColumnName = 'to_departmentid';
		//$this->set('relationtype', implode('::', array($sourceType, $targetType)));
		$permission = $this->get('permission');
		$sql = "INSERT INTO $tableName (shareid, $sourceColumnName, $targetColumnName, permission,companyid) VALUES (?,?,?,?,?)";
		$params = array($ruleId, $sourceId, $targetId, $permission,$companyid);
		$db->pquery($sql, $params);
		//echo $sql;
		//$sql = 'UPDATE vtiger_datashare_module_rel SET relationtype=? WHERE shareid=?';
		//$params = array($this->get('relationtype'), $ruleId);
		//$db->pquery($sql, $params);
       // Settings_SharingAccess_Module_Model::recalculateSharingRules();
	}

	public function delete() {
		$db = PearDatabase::getInstance();
		$ruleId = $this->getId();
		//$relationTypeComponents = explode('::', $this->get('relationtype'));
		//$sourceType = $relationTypeComponents[0];
		//$targetType = $relationTypeComponents[1];
		//$tableColumnInfo = self::$dataShareTableColArr[$sourceType][$targetType];
		//$tableName = $tableColumnInfo['table'];
		$db->pquery("DELETE FROM vtiger_datashare_role2depart WHERE shareid=?", array($ruleId));
		$db->pquery('DELETE FROM vtiger_datashare_module_rel WHERE shareid=?', array($ruleId));
        //Settings_SharingAccess_Module_Model::recalculateSharingRules();//生成缓存文件
	}

	/**
	 * Function to get all the rules
	 * @return <Array> - Array of Settings_Groups_Record_Model instances
	 */
	public static function getInstance($moduleModel, $ruleId) {
		$db = PearDatabase::getInstance();

		$sql = 'SELECT * FROM vtiger_datashare_module_rel WHERE tabid = ? AND shareid = ?';
		$params = array($moduleModel->getId(), $ruleId);
		$result = $db->pquery($sql, $params);

		if($db->num_rows($result)) {
			$row = $db->query_result_rowdata($result, 0);
			$ruleModel = new self();
			return $ruleModel->setData($row)->setModuleFromInstance($moduleModel);
		}
		return false;
	}

	/**
	 * Function to get all the rules
	 * @return <Array> - Array of Settings_Groups_Record_Model instances
	 */
	public static function getAllByModule($moduleModel) {
		$db = PearDatabase::getInstance();

		$sql = 'SELECT * FROM vtiger_datashare_module_rel WHERE tabid = ?';
		$params = array($moduleModel->getId());
		$result = $db->pquery($sql, $params);
		$noOfRules = $db->num_rows($result);
		$ruleModels = array();
		for($i=0; $i<$noOfRules; ++$i) {
			$row = $db->query_result_rowdata($result, $i);
			$query='SELECT share_roleid,to_departmentid FROM vtiger_datashare_role2depart WHERE shareid = ? limit 1';
			$role2departresult=$db->pquery($query,array($row['shareid']));
			if(!$db->num_rows($role2departresult)){
			    continue;
            }
            $share_roleid = $db->raw_query_result_rowdata($role2departresult,0);
            $shareroleid=explode(':',$share_roleid['share_roleid']);
            $query='SELECT 1 FROM vtiger_groups WHERE groupid =?';
            $groupsResut=$db->pquery($query,array($shareroleid[1]));
            if(!$db->num_rows($groupsResut)){
                continue;
            }
            $sql = 'SELECT 1 FROM vtiger_departments WHERE departmentid = ?';
            $departmentidresult = $db->pquery($sql, array($share_roleid['to_departmentid']));
            if(!$db->num_rows($departmentidresult)){
                continue;
            }
            $ruleModel = new self();
            $ruleModels[$row['shareid']] = $ruleModel->setData($row)->setModuleFromInstance($moduleModel);
		}
		return $ruleModels;
	}

	public static function getAllByShareRole($share_roleid){
        $db = PearDatabase::getInstance();
        $sql = 'SELECT * FROM vtiger_datashare_role2depart  a left join vtiger_datashare_module_rel  b on a.shareid = b.shareid left join vtiger_tab c on c.tabid = b.tabid left join vtiger_departments d on d.departmentid = a.to_departmentid WHERE a.share_roleid = ? and b.tabid !=""';
        $params = array($share_roleid);
        $result = $db->pquery($sql, $params);
        $noOfRules = $db->num_rows($result);

        $ruleModels = array();
        for($i=0; $i<$noOfRules; ++$i) {
            $row = $db->query_result_rowdata($result, $i);
            $ruleModel = new self();
            $moduleModel = Settings_SharingAccess_Module_Model::getInstance($row['name']);
            $ruleModels[$row['departmentname']][] = $ruleModel->setData($row)->setModuleFromInstance($moduleModel);
        }
        return $ruleModels;
    }

    public static function getRuleByShareRoleIdAndToDepartmentId($moduleModel,$share_roleid,$to_departmentid){
        $db = PearDatabase::getInstance();
        $sql = 'SELECT * FROM vtiger_datashare_role2depart a left join vtiger_datashare_module_rel b on a.shareid=b.shareid WHERE  a.share_roleid = ? and a.to_departmentid = ? and b.tabid = ?';
        $params = array($share_roleid,$to_departmentid,$moduleModel->getId());
        $result = $db->pquery($sql, $params);

        if($db->num_rows($result)) {
            $row = $db->query_result_rowdata($result, 0);
            $ruleModel = new self();
            return $ruleModel->setData($row)->setModuleFromInstance($moduleModel);
        }
        return false;
	}
	public function getCompanyNameByID($departmentid){
        $db = PearDatabase::getInstance();
        $sql = 'SELECT * FROM vtiger_invoicecompany WHERE companycode=? LIMIT 1';
        $params = array($departmentid);
        $result = $db->pquery($sql, $params);
        if($db->num_rows($result)) {
            $row=$db->query_result_rowdata($result, 0);
            return $row['invoicecompany'];
        }
        return '';
    }
}
