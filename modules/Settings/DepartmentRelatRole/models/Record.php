<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

require_once 'include/events/include.inc';

/**
 * Roles Record Model Class
 */
class Settings_DepartmentRelatRole_Record_Model extends Settings_Vtiger_Record_Model {

	/**
	 * Function to get the Id
	 * @return <Number> Group Id
	 */
	public function getId() {
		return $this->get('departmentrelatroleid');
	}

	/**
	 * Function to set the Id
	 * @param <Number> Group Id
	 * @return <Settings_Groups_Reord_Model> instance
	 */
	public function setId($id) {
		return $this->set('departmentrelatroleid', $id);
	}

	/**
	 * Function to get the Group Name
	 * @return <String>
	 */
	public function getName() {
		return $this->get('departmentname');
	}

	/**
	 * Function to get the description of the group
	 * @return <String>
	 */
	public function getDescription() {
		return $this->get('description');
	}
    public function getremark() {
        return $this->get('remark');
    }
    public function getrolename() {
        return $this->get('rolename');
    }

	/**
	 * Function to get the Edit View Url for the Group
	 * @return <String>
	 */
	public function getEditViewUrl() {
		return '?module=DepartmentRelatRole&parent=Settings&view=Edit&record='.$this->getId();
	}

	/**
	 * Function to get the Delete Action Url for the current group
	 * @return <String>
	 */
	public function getDeleteActionUrl() {
		return 'index.php?module=DepartmentRelatRole&parent=Settings&view=DeleteAjax&record='.$this->getId();
	}
    
    /**
	 * Function to get the Detail Url for the current group
	 * @return <String>
	 */
    public function getDetailViewUrl() {
        return '?module=DepartmentRelatRole&parent=Settings&view=Detail&record='.$this->getId();
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
	public static function getAll() {
		$db = PearDatabase::getInstance();

		$sql = 'SELECT * FROM vtiger_departmentrelatrole';
		$params = array();
		$result = $db->pquery($sql, $params);
		$noOfGroups = $db->num_rows($result);
		$groups = array();
		for ($i = 0; $i < $noOfGroups; ++$i) {
			$group = self::getInstanceFromQResult($result, $i);
			$groups[$group->getId()] = $group;
		}
		return $groups;
	}

	/**
	 * Function to get the instance of Group model, given group id or name
	 * @param <Object> $value
	 * @return Settings_Groups_Record_Model instance, if exists. Null otherwise
	 */
	public static function getInstance($value) {
		$db = PearDatabase::getInstance();

		if (Vtiger_Utils::isNumber($value)) {
			$sql = 'SELECT * FROM vtiger_departmentrelatrole WHERE departmentrelatroleid = ?';
		} else {
			$sql = 'SELECT * FROM vtiger_departmentrelatrole WHERE departmentid = ?';
		}
		$params = array($value);
		$result = $db->pquery($sql, $params);
		if ($db->num_rows($result) > 0) {
			return self::getInstanceFromQResult($result, 0);
		}
		return null;
	}
    public function getRecordLinks() {
        $links = array();
        $recordLinks = array(
            array(
                'linktype' => 'LISTVIEWRECORD',
                'linklabel' => 'LBL_EDIT_RECORD',
                'linkurl' => '',
                'linkicon' => 'icon-pencil'
            ),
            array(
                'linktype' => 'LISTVIEWRECORD',
                'linklabel' => 'LBL_DELETE_RECORD',
                'linkurl' => '',
                'linkicon' => 'icon-trash'
            )
        );

        foreach($recordLinks as $recordLink) {
            $links[] = Vtiger_Link_Model::getInstanceFromValues($recordLink);
        }

        return $links;
    }
   public function getDepartmentRelatRoleByDepartmentID($departmentID){
       $db = PearDatabase::getInstance();
       $sql = 'SELECT 1 FROM vtiger_departmentrelatrole WHERE departmentid=?';

       $result = $db->pquery($sql, array($departmentID));
       if($db->num_rows($result) > 0) {
           return true;
       }
       return false;
   }
   public function getCacheDepartment(){
       include 'crmcache/departmentanduserinfo.php';
       return $cachedepartment;
   }
   public function getRole(){
       include 'crmcache/role.php';
       $return=array_map(function($value){return str_replace(array('|','—'),'',$value);},$roles);
       return $return;
   }
   public function dataChange($request){
       global $adb;
       $record=$request->get('record');
       $roleid=$request->get('role');
       $departmentid=$request->get('department');
       $remark=$request->get('remark');
       $roleidt=$roleid;
       $roleid=implode(',',$roleid);
       $departmentids=$this->getCacheDepartment();
       $roleids=$this->getRole();
       $rolename='';
       foreach($roleidt as $value){
           $rolename.=$roleids[$value].',';
       }
       $rolename=trim($rolename,',');
       if($record>0){
           $sql='UPDATE vtiger_departmentrelatrole SET roleid=?,remark=?,rolename=? WHERE departmentrelatroleid=?';
           $adb->pquery($sql,array($roleid,$remark,$rolename,$record));
       }else{
           $sql='INSERT INTO vtiger_departmentrelatrole(departmentid,roleid,departmentname,rolename,remark) VALUES(?,?,?,?,?)';
           $adb->pquery($sql,array($departmentid,$roleid,$departmentids[$departmentid],$rolename,$remark));
       }
   }
    public function deletedData($request){
        global $adb;
        $record=$request->get('record');
        if($record>0){
            $sql='delete from vtiger_departmentrelatrole WHERE departmentrelatroleid=?';
            $adb->pquery($sql,array($record));
        }
    }

}