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
 * Departments Record Model Class
 */
class Settings_Departments_Record_Model extends Settings_Vtiger_Record_Model {

	/**
	 * Function to get the Id
	 * @return <Number> department Id
	 */
	public function getId() {
		return $this->get('departmentid');
	}

	/**
	 * Function to get the department Name
	 * @return <String>
	 */
	public function getName() {
		return $this->get('departmentname');
	}

	/**
	 * Function to get the depth of the department
	 * @return <Number>
	 */
	public function getDepth() {
		return $this->get('depth');
	}

    /**
     * function to get the departmentcode of the department
     * @return <String>
     */
	public function getCode(){
	    return $this->get('departmentcode');
    }

    /**
     * Function to get the isjurdicalperson of the department
     * @return <number>
     */
    public function getIsjuridicalPerson(){
	    return $this->get('isjuridicalperson');
    }

    /**
     * Function to get the erpaccount of the department
     * @return <Number>
     */
    public function getErpAccount(){
        return $this->get('erpaccount');
    }
    public function getPeopleID(){
        return $this->get('peopleid');
    }

	/**
	 * Function to get Parent department hierarchy as a string
	 * @return <String>
	 */
	public function getParentDepartmentString() {
		return $this->get('parentdepartment');
	}

	public function hasChild()
    {
        return $this->get('has_child');
    }

	/**
	 * Function to set the immediate parent department
	 * @return <Settings_departments_Record_Model> instance
	 */
	public function setParent($parentdepartment) {
		$this->parent = $parentdepartment;
		return $this;
	}

	/**
	 * Function to get the immediate parent department
	 * @return <Settings_departments_Record_Model> instance
	 */
	public function getParent() {
		if(!$this->parent) {
			$parentDepartmentString = $this->getParentDepartmentString();
			$parentComponents = explode('::', $parentDepartmentString);
			$noOfDepartments = count($parentComponents);
			// $currentDepartment = $parentComponents[$noOfDepartments-1];
			if($noOfDepartments > 1) {
				$this->parent = self::getInstanceById($parentComponents[$noOfDepartments-2]);
			} else {
				$this->parent = null;
			}
		}
		return $this->parent;
	}

	/**
	 * Function to get the immediate children departments
	 * @return <Array> - List of Settings_departments_Record_Model instances
	 */
	public function getChildren() {
		$db = PearDatabase::getInstance();
		if(!$this->children) {
			$parentDepartmentString = $this->getParentDepartmentString();
			$currentDepartmentDepth = $this->getDepth();

			$sql = 'SELECT * FROM vtiger_departments WHERE parentdepartment LIKE ? AND depth = ?';
			$params = array($parentDepartmentString.'::%', $currentDepartmentDepth+1);
			$result = $db->pquery($sql, $params);
			$noOfDepartments = $db->num_rows($result);
			$departments = array();
			for ($i=0; $i<$noOfDepartments; ++$i) {
				$department = self::getInstanceFromQResult($result, $i);
				$departments[$department->getId()] = $department;
			}
			$this->children = $departments;
		}
		//print_r($this->children);
		return $this->children;
	}

	public function getSameLevelDepartments() {
		$db = PearDatabase::getInstance();
		if(!$this->children) {
			$parentDepartments = getParentDepartment($this->getId());
			$currentDepartmentDepth = $this->getDepth();
			$parentDepartmentString = '';
			foreach ($parentDepartments as $key => $department) {
				if(empty($parentDepartmentString)) $parentDepartmentString = $department;
				else $parentDepartmentString = $parentDepartmentString.'::'.$department;
			}
			$sql = 'SELECT * FROM vtiger_departments WHERE parentdepartment LIKE ? AND depth = ?';
			$params = array($parentDepartmentString.'::%', $currentDepartmentDepth);
			$result = $db->pquery($sql, $params);
			$noOfDepartments = $db->num_rows($result);
			$departments = array();
			for ($i=0; $i<$noOfDepartments; ++$i) {
				$department = self::getInstanceFromQResult($result, $i);
				$departments[$department->getId()] = $department;
			}
			$this->children = $departments;
		}
		return $this->children;
	}

	/**
	 * Function to get all the children departments
	 * @return <Array> - List of Settings_Departments_Record_Model instances
	 */
	public function getAllChildren() {
		$db = PearDatabase::getInstance();

		$parentDepartmentString = $this->getParentDepartmentString();

		$sql = 'SELECT * FROM vtiger_departments WHERE parentdepartment LIKE ?  ORDER BY parentdepartment';
		$params = array($parentDepartmentString.'::%');
		$result = $db->pquery($sql, $params);
		$noOfDepartments = $db->num_rows($result);
		$departments = array();
		for ($i=0; $i<$noOfDepartments; ++$i) {
			$department = self::getInstanceFromQResult($result, $i);
			$departments[$department->getId()] = $department;
		}
		return $departments;
	}


	/**
	 * Function to get the Edit View Url for the Department
	 * @return <String>
	 */
	public function getEditViewUrl() {
		return 'index.php?module=Departments&parent=Settings&view=Edit&record='.$this->getId();
	}

//	public function getListViewEditUrl() {
//		return '?module=Departments&parent=Settings&view=Edit&record='.$this->getId();
//	}

	/**
	 * Function to get the Create Child Department Url for the current department
	 * @return <String>
	 */
	public function getCreateChildUrl() {
		return '?module=Departments&parent=Settings&view=Edit&parent_departmentid='.$this->getId();
	}

	/**
	 * Function to get the Delete Action Url for the current department
	 * @return <String>
	 */
	public function getDeleteActionUrl() {
		return '?module=Departments&parent=Settings&view=DeleteAjax&record='.$this->getId();
	}

	/**
	 * Function to get the Popup Window Url for the current department
	 * @return <String>
	 */
	public function getPopupWindowUrl() {
		return 'module=Departments&parent=Settings&view=Popup&src_record='.$this->getId();
	}

	/**
	 * Function to add a child department to the current department
	 * @param <Settings_Departments_Record_Model> $department
	 * @return Settings_Departments_Record_Model instance
	 */
	public function addChildDepartment($department) {
        $currnetid=$department->getID();
		$department->setParent($this);
		$department->save();
        $datas['flag']=8;
		if(!empty($currnetid)){
            $datas['flag']=9;
        }
        $datas['departmentname']=$department->get('departmentname');
        $datas['departmentid']=$department->get('departmentid');
        $datas['newdepartmentid']='';
        $datas['ERPDOIT']=456321;
        $datas['parentid']=trim($this->get('departmentid'),'H');
        $recordModel=new Vtiger_Record_Model();
        $recordModel->sendWechatMessage($datas);
		return $department;
	}

	/**
	 * Function to move the current department and all its children nodes to the new parent department
	 * @param <Settings_Departments_Record_Model> $newParentDepartment
	 */
	public function moveTo($newParentDepartment,$departmentname='',$peopleid='') {
		$currentDepth = $this->getDepth();
		$currentParentDepartmentString = $this->getParentDepartmentString();

		$newDepth = $newParentDepartment->getDepth() + 1;
		$newParentDepartmentString = $newParentDepartment->getParentDepartmentString() .'::'. $this->getId();

		$depthDifference = $newDepth - $currentDepth;
		$allChildren = $this->getAllChildren();
        if($departmentname){
            $this->set('departmentname', $departmentname);
        }
        if($peopleid){
            $this->set('peopleid', $peopleid);

        }
		$this->set('depth', $newDepth);
		$this->set('parentdepartment', $newParentDepartmentString);
		$this->set('allowassignedrecordsto', $this->get('allowassignedrecordsto'));
		$this->save();
        $datas['departmentname']=$this->get('departmentname');
        $datas['departmentid']=$this->get('departmentid');
        $datas['newdepartmentid']='';
        $datas['ERPDOIT']=456321;
        $datas['flag']=9;
        $datas['parentid']=trim($newParentDepartment->get('departmentid'),'H');
//        $recordModel=new Vtiger_Record_Model();
//        $recordModel->sendWechatMessage($datas);

		foreach($allChildren as $departmentId => $departmentModel) {
			$oldChildDepth = $departmentModel->getDepth();
			$newChildDepth = $oldChildDepth + $depthDifference;

			$oldChildParentDepartmentString = $departmentModel->getParentDepartmentString();
			$newChildParentDepartmentString = str_replace($currentParentDepartmentString.'::', $newParentDepartmentString.'::', $oldChildParentDepartmentString.'::');
            $newChildParentDepartmentString=trim($newChildParentDepartmentString,'::');
			$departmentModel->set('depth', $newChildDepth);
			$departmentModel->set('parentdepartment', $newChildParentDepartmentString);
			$departmentModel->set('allowassignedrecordsto', $departmentModel->get('allowassignedrecordsto'));
			$departmentModel->save();
		}
	}

	/**
	 * Function to save the department
	 */
	public function save() {
		$db = PearDatabase::getInstance();
		$departmentId = $this->getId();
		$mode = 'edit';
		//echo '<script>console.log(\''.$departmentId.'\');</script>';

		if(empty($departmentId)) {
			$mode = '';
			$departmentIdNumber = $db->getUniqueId('vtiger_departments');
			$departmentId = 'H'.$departmentIdNumber;
			$this->set('departmentid',$departmentId);
		}
		$parentDepartment = $this->getParent();
		if($parentDepartment != null) {
			$this->set('depth', $parentDepartment->getDepth()+1);
			$this->set('parentdepartment', $parentDepartment->getParentDepartmentString() .'::'. $departmentId);
		}

		if($mode == 'edit') {   //allowassignedrecordsto作废
			$sql = 'UPDATE vtiger_departments SET departmentname=?, parentdepartment=?, depth=?, allowassignedrecordsto=?,`departmentcode`=?,`erpaccount`=?,`isjuridicalperson`=?,peopleid=? WHERE departmentid=?';
			$params = array($this->getName(), $this->getParentDepartmentString(), $this->getDepth(), 1,$this->getCode(),$this->getErpAccount(),$this->getIsjuridicalPerson(),$this->getPeopleID(), $departmentId);
			//header("Content-type:text/html;charset=utf-8");
            //print_r($params);
			$db->pquery($sql, $params);
		} else {
			$sql = 'INSERT INTO vtiger_departments(departmentid, departmentname, parentdepartment, depth, allowassignedrecordsto,`departmentcode`,`erpaccount`,`isjuridicalperson`,peopleid) VALUES (?,?,?,?,?,?,?,?,?)';
			$params = array($departmentId, $this->getName(), $this->getParentDepartmentString(), $this->getDepth(), 1,$this->getCode(),$this->getErpAccount(),$this->getIsjuridicalPerson(),$this->getPeopleID());
			/*echo "<pre>";
			print_r($params);*/
			//exit;

			$db->pquery($sql, $params);
		}

	}

	/**
	 * Function to delete the department
	 * @param <Settings_Departments_Record_Model> $transferToDepartment
	 */
	public function delete($transferToDepartment) {
		$db = PearDatabase::getInstance();
		$departmentId = $this->getId();


		//delete handling for sharing rules
		//deleteDepartmentRelatedSharingRules($departmentId);

		$db->pquery('DELETE FROM vtiger_departments WHERE departmentid=?', array($departmentId));
		$db->pquery('DELETE  FROM vtiger_datashare_module_rel WHERE shareid in(SELECT shareid FROM vtiger_datashare_role2depart WHERE to_departmentid=?)', array($departmentId));
		$db->pquery('DELETE FROM vtiger_datashare_role2depart WHERE to_departmentid=?', array($departmentId));

		$allChildren = $this->getAllChildren();
		$transferParentDepartmentSequence = $transferToDepartment->getParentDepartmentString();
		$currentParentDepartmentSequence = $this->getParentDepartmentString();
        $newdepartmentId=$transferToDepartment->getId();
        $db->pquery('UPDATE `vtiger_user2department` SET departmentid=? WHERE departmentid=?',array($newdepartmentId,$departmentId));
        $db->pquery('UPDATE `vtiger_usermanger` SET departmentid=? WHERE departmentid=?',array($newdepartmentId,$departmentId));
        $datas['departmentname']='deleted';
        $datas['departmentid']=$departmentId;
        $datas['newdepartmentid']=$newdepartmentId;
        $datas['ERPDOIT']=456321;
        $datas['flag']=10;
        $datas['parentid']=1;
        $recordModel=new Vtiger_Record_Model();
        $recordModel->sendWechatMessage($datas);
		foreach($allChildren as $departmentId => $departmentModel) {
			$oldChildParentDepartmentString = $departmentModel->getParentDepartmentString();
			$newChildParentDepartmentString = str_replace($currentParentDepartmentSequence, $transferParentDepartmentSequence, $oldChildParentDepartmentString);
			$newChildDepth = count(explode('::', $newChildParentDepartmentString))-1;
			$departmentModel->set('depth', $newChildDepth);
			$departmentModel->set('parentdepartment', $newChildParentDepartmentString);
			$departmentModel->save();
		}
	}

	/**
	 * Function to get the list view actions for the record
	 * @return <Array> - Associate array of Vtiger_Link_Model instances
	 */
	public function getRecordLinks() {

		$links = array();
		if($this->getParent()) {
			$recordLinks = array(
				array(
					'linktype' => 'LISTVIEWRECORD',
					'linklabel' => 'LBL_EDIT_RECORD',
					'linkurl' => $this->getCreateChildUrl(),
					'linkicon' => 'icon-plus-sign'
				),
				array(
					'linktype' => 'LISTVIEWRECORD',
					'linklabel' => 'LBL_DELETE_RECORD',
					'linkurl' => 'javascript:Vtiger_List_Js.deleteRecord('.$this->getId().');',
					'linkicon' => 'icon-trash'
				)
			);
			foreach($recordLinks as $recordLink) {
				$links[] = Vtiger_Link_Model::getInstanceFromValues($recordLink);
			}
		}

		return $links;
	}

	/**
	 * Function to get the instance of Departments record model from query result
	 * @param <Object> $result
	 * @param <Number> $rowNo
	 * @return Settings_Departments_Record_Model instance
	 */
	public static function getInstanceFromQResult($result, $rowNo) {
		$db = PearDatabase::getInstance();
		$row = $db->query_result_rowdata($result, $rowNo);
		$sql = 'SELECT * from vtiger_departments where parentdepartment like "'.$row['parentdepartment'].'::%"';
        $has_result = $db->pquery($sql);
        $noOfDepartments = $db->num_rows($has_result);
        if($noOfDepartments>0){
            $row['has_child'] = 1;
        }
		$department = new self();
		return $department->setData($row);
	}

	/**
	 * Function to get all the departments
	 * @param <Boolean> $baseDepartment
	 * @return <Array> list of Department models <Settings_Departments_Record_Model>
	 */
	public static function getAll($baseDepartment = false) {
		$db = PearDatabase::getInstance();
		$params = array();

		$sql = 'SELECT vtiger_departments.*,vtiger_users.last_name FROM vtiger_departments LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_departments.erpaccount';
		if (!$baseDepartment) {
			$sql .= ' WHERE depth != ?';
			$params[] = 0;
		}
		$sql .= ' ORDER BY parentdepartment';

		$result = $db->pquery($sql, $params);
		$noOfDepartments = $db->num_rows($result);

		$departments = array();
		for ($i=0; $i<$noOfDepartments; ++$i) {
			$department = self::getInstanceFromQResult($result, $i);
			$departments[$department->getId()] = $department;
		}
		return $departments;
	}

	/**
	 * Function to get all the departments
	 * @param <Boolean> $baseDepartment
	 * @return <Array> list of Department models <Settings_Departments_Record_Model>
	 */
	public static function getAllToArray($baseDepartment = false) {
		$db = PearDatabase::getInstance();
		$params = array();

		$sql = 'SELECT vtiger_departments.*,vtiger_users.last_name FROM vtiger_departments LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_departments.erpaccount';
		if (!$baseDepartment) {
			$sql .= ' WHERE depth != ?';
			$params[] = 0;
		}
		$sql .= ' ORDER BY parentdepartment';

		$result = $db->pquery($sql, $params);
		$noOfDepartments = $db->num_rows($result);

		$departments = array();
		for ($i=0; $i<$noOfDepartments; ++$i) {
			$department = $db->query_result_rowdata($result, $i);
			$departments[$department['departmentid']] = $department;
		}
		return $departments;
	}

	/**
	 * Function to get the instance of Department model, given department id
	 * @param <Integer> $departmentId
	 * @return Settings_Departments_Record_Model instance, if exists. Null otherwise
	 */
	public static function getInstanceById($departmentId) {
		$db = PearDatabase::getInstance();

		$sql = 'SELECT * FROM vtiger_departments WHERE departmentid = ?';
		$params = array($departmentId);
		$result = $db->pquery($sql, $params);
		if($db->num_rows($result) > 0) {
			return self::getInstanceFromQResult($result, 0);
		}
		return null;
	}
    /**
     * 查看该字段的值是否有重复的
     * @param $name字段名称
     * @param $value字段的值
     * @param $checkid部门的ID
     * @return int重复则返回1不重复返回null
     */
    public static function getIsRepeatField($name,$value,$checkid){
        if(empty($value)){
            //如果为空则不验证是否重复
            return NUll;
        }
        $db = PearDatabase::getInstance();


        $sql = 'SELECT 1 FROM vtiger_departments WHERE departmentid != ? AND '.$name.'=?';
        $params = array($checkid,$value);
        $result = $db->pquery($sql, $params);
        if($db->num_rows($result) > 0) {
            return 1;
        }
        return null;
    }

	/**
	 * Function to get the instance of Base Department model
	 * @return Settings_Departments_Record_Model instance, if exists. Null otherwise
	 */
	public static function getBaseDepartment() {
		$db = PearDatabase::getInstance();

		$sql = 'SELECT * FROM vtiger_departments WHERE depth=0 LIMIT 1';
		$params = array();
		$result = $db->pquery($sql, $params);
		if($db->num_rows($result) > 0) {
			return self::getInstanceFromQResult($result, 0);
		}
		return null;
	}

	/* Function to get the instance of the department by Name
    * @param type $name -- name of the department
    * @return null/department instance
    */
   public static function getInstanceByName($name, $excludedRecordId = array()) {
       $db = PearDatabase::getInstance();
       $sql = 'SELECT * FROM vtiger_departments WHERE departmentname=?';
       $params = array($name);
       if(!empty($excludedRecordId)){
           $sql.= ' AND departmentid NOT IN ('.generateQuestionMarks($excludedRecordId).')';
           $params = array_merge($params,$excludedRecordId);
       }
       $result = $db->pquery($sql, $params);
       if($db->num_rows($result) > 0) {
		   return self::getInstanceFromQResult($result, 0);
	   }
	   return null;
   }

    /**
     * 取当前所有在在职的用户名及ID,(用户名中包含对应的部门)
     * @return
     */
   public function getUserDepartmentInfo(){
       $db=PearDatabase::getInstance();

       $query="SELECT id,CONCAT(last_name,'[',IFNULL((SELECT departmentname	FROM vtiger_departments WHERE	departmentid =(SELECT	departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id	LIMIT 1)),''),']',(IF(`status` = 'Active','','[离职]'))) AS last_name FROM vtiger_users	WHERE vtiger_users.deleted=0 AND vtiger_users.`status`='Active'";
       $result=$db->pquery($query,array());
       $num_rows=$db->num_rows($result);

       $resultArray=array();
       for($i=0;$i<$num_rows;$i++){
            $id=$db->query_result($result,$i,'id');
            $last_name=$db->query_result($result,$i,'last_name');
            $resultArray[$id]=$last_name;
       }
       return $resultArray;
   }
    /**
     * 取当前部门的层次parentdepartments
     * js做显示和隐藏的类的值
     * @param $parentdepartments
     * @return mixed
     */
   public function explodeParentDepartments($parentdepartments){
       $departmentids=substr($parentdepartments,0,strripos($parentdepartments,'::'));
       return str_replace('::',' ',$departmentids);

   }


}
