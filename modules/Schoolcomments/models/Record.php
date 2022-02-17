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
 * ModComments Record Model
 */
class Schoolcomments_Record_Model extends Vtiger_Record_Model {

	/**
	 * Functions gets the comment id
	 */
	public function getId() {
		//TODO : check why is modcommentsid is not set 2014-10-29 young 已经解决
		$id = $this->get('id');
		if(empty($id)) {
			return $this->get('modcommentsid');
		}
		return $this->get('modcommentsid');
	}

	public function setId($id) {
		return $this->set('id', $id);
	}
	/**
	 * 获取跟进评论
	 * @return Value|string
	 */
	public function getHistory(){
		return $this->historyrecord;
	}
	public function setHistory($record){
		$this->historyrecord=$record;
	}
	public function getAlerts(){
		return $this->jobalerts;
	}
	public function setAlerts($jobalerts){
		$this->jobalerts=$jobalerts;
	}
	
	static public function getModcommentmode(){
		$db = PearDatabase::getInstance();
		$result = $db->pquery('SELECT vtiger_modcommentmode.* FROM vtiger_modcommentmode', array());
		$arr=array();
		if($db->num_rows($result)) {
			for($i=0;$i<$db->num_rows($result);$i++){
			$row = $db->query_result_rowdata($result, $i);
			$arr[]=$row['modcommentmode'];
			}
		}
 		return $arr;
	}
	static public function getModcommenttype(){
		$db = PearDatabase::getInstance();
		$result = $db->pquery('SELECT * FROM vtiger_modcommenttype', array());
		$arr=array();
		if($db->num_rows($result)) {
			for($i=0;$i<$db->num_rows($result);$i++){
			$row = $db->query_result_rowdata($result, $i);
			$arr[]=$row['modcommenttype'];
			}
		}
 		return $arr;
	}

	/**
	 * 获取联系人
	 * @return multitype:unknown
	 */
	static public function getModcommentContacts($parentId){
		$db = PearDatabase::getInstance();
		$arr=array();
		//young.yang
		$result=$db->pquery('select schoolcontactsid,schoolcontactsname from vtiger_schoolcontacts where schoolid=?',array($parentId));
		$c = $db->num_rows($result);
		if($db->num_rows($result)) {

			for($i=0;$i<$c;$i++){
				$row = $db->query_result_rowdata($result, $i);
				$tmp['contactid']=$row['schoolcontactsid'];
				$tmp['name']=$row['schoolcontactsname'];
				$arr[]=$tmp;
			}
		}
		return $arr;
	}
	
	/**
	 * 获取提醒信息
	 * @param unknown $parentIds
	 */
	static public function getAlertModcomments($parentIds){
		$db = PearDatabase::getInstance();
	
		$query="SELECT 
				vtiger_jobalerts.moduleid AS modcommentsid,
				(select GROUP_CONCAT(last_name) from vtiger_users where FIND_IN_SET(vtiger_users.id,REPLACE(vtiger_jobalerts.alertid,' |##| ',','))>0) as username,
				vtiger_jobalerts.*
				FROM vtiger_jobalerts where vtiger_jobalerts.moduleid in($parentIds) and vtiger_jobalerts.modulename='ModComments' 
				order by vtiger_jobalerts.jobalertsid desc";
		
		$result = $db->pquery($query, array());
		$arr=array();
		if($db->num_rows($result)) {
			while ($row=$db->fetch_array($result)){
				$arr[$row['modcommentsid']][]=$row;
			}
		}
		return $arr;
	}
	
	/**
	 * 获取子类
	 * @param unknown $parentIds
	 */
	static public function getSubModcomments($parentIds){
		$db = PearDatabase::getInstance();
		
		//echo 'SELECT vtiger_submodcomments.modcommentsid,a.first_name AS createdby, b.first_name AS modifiedby, modcommenthistory, vtiger_submodcomments.createdtime, vtiger_submodcomments.modifiedtime, vtiger_submodcomments.modifiedcause FROM `vtiger_submodcomments` LEFT JOIN vtiger_users AS a ON vtiger_submodcomments.creatorid = a.id LEFT JOIN vtiger_users AS b ON vtiger_submodcomments.modifiedtime = b.id WHERE modcommentsid ='.$parentIds;die();
		$result = $db->pquery("SELECT vtiger_submodcomments.id,vtiger_submodcomments.modcommentsid,vtiger_submodcomments.creatorid,vtiger_submodcomments.modifiedby,(select last_name from vtiger_users where vtiger_submodcomments.creatorid = vtiger_users.id ) AS createdbyer, (select last_name from vtiger_users where vtiger_submodcomments.modifiedtime = vtiger_users.id  ) AS modifiedbyer, modcommenthistory, vtiger_submodcomments.createdtime, vtiger_submodcomments.modifiedtime, vtiger_submodcomments.modifiedcause FROM `vtiger_submodcomments` LEFT JOIN vtiger_users AS a ON vtiger_submodcomments.creatorid = a.id LEFT JOIN vtiger_users AS b ON vtiger_submodcomments.modifiedtime = b.id WHERE modcommentsid in($parentIds) order by vtiger_submodcomments.id desc", array());
		$arr=array();
		if($db->num_rows($result)) {
			while ($row=$db->fetch_array($result)){
				$arr[$row['modcommentsid']][]=$row;
			}
		}
		
		return $arr;
	}
	
	/**
	 * 返回子评论根据id
	 * @param unknown $id
	 * @return Ambigous <multitype:, unknown, s, --, string, mixed>
	 */
	static public function getSubModcommentsById($id){
		$db = PearDatabase::getInstance();
		$result = $db->pquery('SELECT vtiger_submodcomments.modcommentsid,vtiger_submodcomments.creatorid,vtiger_submodcomments.modifiedby,a.first_name AS createdbyer, b.first_name AS modifiedbyer, modcommenthistory, vtiger_submodcomments.createdtime, vtiger_submodcomments.modifiedtime, vtiger_submodcomments.modifiedcause FROM `vtiger_submodcomments` LEFT JOIN vtiger_users AS a ON vtiger_submodcomments.creatorid = a.id LEFT JOIN vtiger_users AS b ON vtiger_submodcomments.modifiedtime = b.id WHERE vtiger_submodcomments.id=? limit 1', array($id));
		$arr=array();
		if($db->num_rows($result)) {
			$arr=$db->query_result_rowdata($result,0);
		}
		return $arr;
	}
	/**
	 * Function returns url to get child comments
	 * @return <String> - url
	 */
	public function getChildCommentsUrl() {
		return $this->getDetailViewUrl().'&mode=showChildComments';
	}
	
	public function getImagePath() {
		$commentor = $this->getCommentedByModel();
		if($commentor) {
			$customer = $this->get('customer');
			if (!empty($customer)) {
				return 'CustomerPortal.png';
			} else {
				$imagePath = $commentor->getImageDetails();
				if (!empty($imagePath[0]['name'])) {
					return '../' . $imagePath[0]['path'] . '_' . $imagePath[0]['name'];
				}
			}
		}
		return false;
	}
	
	/**
	 * Function to create an instance of ModComment_Record_Model
	 * @param <Integer> $record
	 * @return ModComment_Record_Model
	 */
	public static function getInstanceById($record) {
		$db = PearDatabase::getInstance();
		$result = $db->pquery('SELECT vtiger_modcomments.* FROM vtiger_modcomments 
					WHERE modcommentsid = ? ', array($record));
		if($db->num_rows($result)) {
			$row = $db->query_result_rowdata($result, $i);
			$self = new self();
			$self->setData($row);
			return $self;
		}
		return false;
	}

	/**
	 * 返回父类信息
	 * @history
	 * 	2014-10-27 young 返回false
	 * @return boolean|Ambigous <ModComment_Record_Model, boolean, ModComments_Record_Model>
	 */
	public function getParentCommentModel() {
		return false;
		$recordId = $this->get('parent_comments');
		if(!empty($recordId))
			return ModComments_Record_Model::getInstanceById($recordId, 'ModComments');

		return false;
	}

	/**
	 * Function returns the parent Record Model(Contacts, Accounts etc)
	 * @return <Vtiger_Record_Model>
	 */
	public function getParentRecordModel() {
		$parentRecordId = $this->get('related_to');
		if(!empty($parentRecordId))
		return Vtiger_Record_Model::getInstanceById($parentRecordId);

		return false;
	}

	/**
	 * Function returns the commentor Model (Users Model)
	 * @return <Vtiger_Record_Model>
	 */
	public function getCommentedByModel() {
		$customer = $this->get('customer');
		if(!empty($customer)) {
			return Vtiger_Record_Model::getInstanceById($customer, 'Contacts');
		} else {
			$commentedBy = $this->get('creatorid');
			if($commentedBy)
			return Vtiger_Record_Model::getInstanceById($commentedBy, 'Users');
		}
		return false;
	}

	/**
	 * Function returns the commented time
	 * @return <String>
	 */
	public function getCommentedTime() {
		$commentTime = $this->get('addtime');
		return $commentTime;
	}

	/**
	 * Function returns the commented time
	 * @return <String>
	 */
	public function getModifiedTime() {
		$commentTime = $this->get('modifiedtime');
		return $commentTime;
	}
	/**
	 * Function returns latest comments for parent record
	 * @param <Integer> $parentRecordId - parent record for which latest comment need to retrieved
	 * @param <Vtiger_Paging_Model> - paging model
	 * @return ModComments_Record_Model if exits or null
	 */
	public static function getRecentComments($parentRecordId, $pagingModel,$moduleName=''){
		$db = PearDatabase::getInstance();
		//$startIndex = $pagingModel->getStartIndex();
		//$limit = $pagingModel->getPageLimit();
		
		/* $listView = Vtiger_ListView_Model::getInstance('ModComments');
		$queryGenerator = $listView->get('query_generator');
		$queryGenerator->setFields(array('commentcontent', 'addtime', 'related_to', 'creatorid',
									'modcommenttype', 'modcommentmode', 'modcommenthistory','modcommentpurpose'));

		$query = $queryGenerator->getQuery(); */
		
		$query = "SELECT vtiger_schoolcomments.modcommentsid, ( SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id = vtiger_schoolcomments.creatorid ) AS creatorid, ( SELECT vtiger_schoolcontacts.schoolcontactsname FROM vtiger_schoolcontacts WHERE vtiger_schoolcontacts.schoolcontactsid=vtiger_schoolcomments.contact_id ) AS contact_id, vtiger_schoolcomments.commentcontent, vtiger_schoolcomments.smodcommenttype, vtiger_schoolcomments.smodcommentmode, vtiger_schoolcomments.smodcommentpurpose, vtiger_schoolcomments.addtime FROM vtiger_schoolcomments WHERE vtiger_schoolcomments.related_to =? ORDER BY vtiger_schoolcomments.addtime DESC";
		$result = $db->pquery($query, array($parentRecordId));
		$rows = $db->num_rows($result);
		$data = array();
		for ($i=0; $i<$rows; $i++) {
			$row = $db->query_result_rowdata($result, $i);
			$data[] = $row;
		}
		return $data;
	}

	/**
	 * Function returns all the parent comments model
	 * @param <Integer> $parentId
	 * @return ModComments_Record_Model(s)
	 */
	public static function getAllParentComments($parentId) {
		$db = PearDatabase::getInstance();

		$listView = Vtiger_ListView_Model::getInstance('ModComments');
		$queryGenerator = $listView->get('query_generator');
		$queryGenerator->setFields(array('commentcontent', 'addtime', 'related_to', 'creatorid',
									'modcommenttype', 'modcommentmode', 'modcommenthistory','modcommentsid'));
		$query = $queryGenerator->getQuery();
		//客户判断
		if($moduleName == 'Accounts'){
			$query = $query ." AND related_to = ?  ORDER BY addtime DESC
			LIMIT $startIndex, $limit";
		}else{
			$query = $query ." AND moduleid = ?  ORDER BY addtime DESC
			LIMIT $startIndex, $limit";
		}
		//Condition are directly added as query_generator transforms the
		//reference field and searches their entity names
		$query = $query ."  ORDER BY addtime DESC";
		//echo $query;die();
		$result = $db->pquery($query, array());
		$rows = $db->num_rows($result);
		
		
		
		for ($i=0; $i<$rows; $i++) {
			$row = $db->query_result_rowdata($result, $i);
			$recordInstance = new self();
			$recordInstance->setData($row);
			//$recordInstance->setHistory(empty($subcomments[$row['modcommentsid']])?array():$subcomments[$row['modcommentsid']]);
			$recordInstances[] = $recordInstance;
		}
		
		
		return $recordInstances;
	}

	

	/**
	 * Function to get details for user have the permissions to do actions
	 * @return <Boolean> - true/false
	 */
	public function isEditable() {
		return false;
	}

	/**
	 * Function to get details for user have the permissions to do actions
	 * @return <Boolean> - true/false
	 */
	public function isDeletable() {
		return false;
	}
}