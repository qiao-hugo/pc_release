<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Home_Module_Model extends Vtiger_Module_Model {

	/**
	 * Function returns the default view for the Home module
	 * @return <String>
	 */
	public function getDefaultViewName() {
		return 'DashBoard';
	}

	/**
	 * Function returns latest comments across CRM
	 * @param <Vtiger_Paging_Model> $pagingModel
	 * @return <Array>
	 */
	public function getComments($pagingModel) {
		$db = PearDatabase::getInstance();

		$nonAdminAccessQuery = Users_Privileges_Model::getNonAdminAccessControlQuery('ModComments');

		$result = $db->pquery('SELECT *, vtiger_crmentity.createdtime AS createdtime, vtiger_crmentity.smownerid AS smownerid,
						crmentity2.crmid AS parentId, crmentity2.setype AS parentModule FROM vtiger_modcomments
						INNER JOIN vtiger_crmentity ON vtiger_modcomments.modcommentsid = vtiger_crmentity.crmid
							AND vtiger_crmentity.deleted = 0
						INNER JOIN vtiger_crmentity crmentity2 ON vtiger_modcomments.related_to = crmentity2.crmid
							AND crmentity2.deleted = 0
                        LEFT JOIN vtiger_crmentity crmentity3 ON vtiger_modcomments.customer = crmentity3.crmid 
                            AND crmentity3.deleted = 0
						 '.$nonAdminAccessQuery.'
						ORDER BY vtiger_crmentity.crmid DESC LIMIT ?, ?',
				array($pagingModel->getStartIndex(), $pagingModel->getPageLimit()));

		$comments = array();
		for($i=0; $i<$db->num_rows($result); $i++) {
			$row = $db->query_result_rowdata($result, $i);
			if(Users_Privileges_Model::isPermitted($row['setype'], 'DetailView', $row['related_to'])){
				$commentModel = Vtiger_Record_Model::getCleanInstance('ModComments');
				$commentModel->setData($row);
				$time = $commentModel->get('createdtime');
				$comments[$time] = $commentModel;
			}
		}

		return $comments;
	}

	/**
	 * Function returns comments and recent activities across CRM
	 * @param <Vtiger_Paging_Model> $pagingModel
	 * @param <String> $type - comments, updates or all
	 * @return <Array>
	 */
	public function getHistory($pagingModel, $type=false) {
		if(empty($type)) {
			$type = 'all';
		}
		//TODO: need to handle security
		$comments = array();
		if($type == 'all' || $type == 'comments') {
			$comments = $this->getComments($pagingModel);
			if($type == 'comments') {
				return $comments;
			}
		}
		$db = PearDatabase::getInstance();
		global $current_user;
		//As getComments api is used to get comment infomation,no need of getting
		//comment information again,so avoiding from modtracker
		$result = $db->pquery('SELECT vtiger_modtracker_basic.*
								FROM vtiger_modtracker_basic
								INNER JOIN vtiger_crmentity ON vtiger_modtracker_basic.crmid = vtiger_crmentity.crmid
									AND deleted = 0 AND module != "ModComments" AND vtiger_modtracker_basic.whodid=?
								ORDER BY vtiger_modtracker_basic.id DESC LIMIT ?, ?',
				array($current_user->id,$pagingModel->getStartIndex(), $pagingModel->getPageLimit()));

		$activites = array();
		for($i=0; $i<$db->num_rows($result); $i++) {
			$row = $db->query_result_rowdata($result, $i);
			$moduleName = $row['module'];
			$recordId = $row['crmid'];
			if(Users_Privileges_Model::isPermitted($moduleName, 'DetailView', $recordId)){
				$modTrackerRecorModel = new ModTracker_Record_Model();
				$modTrackerRecorModel->setData($row)->setParent($recordId, $moduleName);
				$time = $modTrackerRecorModel->get('changedon');
				$activites[$time] = $modTrackerRecorModel;
			}
		}

		$history = array_merge($activites, $comments);

		foreach($history as $time=>$model) {
			$dateTime[] = $time;
		}

		if(!empty($history)) {
			array_multisort($dateTime,SORT_DESC,SORT_STRING,$history);
			return $history;
		}
		return false;
	}

	/**
	 * Function returns the Calendar Events for the module
	 * @param <String> $mode - upcoming/overdue mode
	 * @param <Vtiger_Paging_Model> $pagingModel - $pagingModel
	 * @param <String> $user - all/userid
	 * @param <String> $recordId - record id
	 * @return <Array>
	 */
	function getCalendarActivities($mode, $pagingModel, $user,$recordId = false) {
		$currentUser = Users_Record_Model::getCurrentUserModel();
		$db = PearDatabase::getInstance();

		if (!$user) {
			$user = $currentUser->getId();
		}

		$nowInUserFormat = Vtiger_Datetime_UIType::getDisplayDateTimeValue(date('Y-m-d H:i:s'));
		$nowInDBFormat = Vtiger_Datetime_UIType::getDBDateTimeValue($nowInUserFormat);
		list($currentDate, $currentTime) = explode(' ', $nowInDBFormat);

		$query = "SELECT vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_crmentity.setype, vtiger_activity.* FROM vtiger_activity
					INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_activity.activityid
					LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";

		$query .= Users_Privileges_Model::getNonAdminAccessControlQuery('Calendar');

		$query .= " WHERE vtiger_crmentity.deleted=0
					AND (vtiger_activity.activitytype NOT IN ('Emails'))
					AND (vtiger_activity.status is NULL OR vtiger_activity.status NOT IN ('Completed', 'Deferred'))
					AND (vtiger_activity.eventstatus is NULL OR vtiger_activity.eventstatus NOT IN ('Held'))";

		if ($mode === 'upcoming') {
			$query .= " AND due_date >= '$currentDate'";
		} elseif ($mode === 'overdue') {
			$query .= " AND due_date < '$currentDate'";
		}

		$params = array();
		if($user != 'all' && $user != '') {
			if($user === $currentUser->id) {
				$query .= " AND vtiger_crmentity.smownerid = ?";
				$params[] = $user;
			}
		}

		$query .= " ORDER BY date_start, time_start LIMIT ?, ?";
		$params[] = $pagingModel->getStartIndex();
		$params[] = $pagingModel->getPageLimit()+1;

		$result = $db->pquery($query, $params);
		$numOfRows = $db->num_rows($result);

		$activities = array();
		for($i=0; $i<$numOfRows; $i++) {
			$row = $db->query_result_rowdata($result, $i);
			$model = Vtiger_Record_Model::getCleanInstance('Calendar');
			$model->setData($row);
			$model->setId($row['crmid']);
			$activities[] = $model;
		}

		$pagingModel->calculatePageRange($activities);
		if($numOfRows > $pagingModel->getPageLimit()){
			array_pop($activities);
			$pagingModel->set('nextPageExists', true);
		} else {
			$pagingModel->set('nextPageExists', false);
		}

		return $activities;
	}
    
    static public function getAccountNoSeven(){
		global $current_user;
		$listQuery="SELECT
			vtiger_account.accountname,
			vtiger_account.servicetype,
			vtiger_account.protected,
			(
				SELECT
					smownerid
				FROM
					vtiger_crmentity
				WHERE
					vtiger_crmentity.crmid = vtiger_account.accountid
				AND vtiger_crmentity.deleted = 0
			) AS smownerid,
			vtiger_account.accountrank,
			vtiger_account.linkname,
			vtiger_account.mobile,
			vtiger_account.phone,
			vtiger_account.website,
			vtiger_account.fax,
			vtiger_account.email1,
			vtiger_account.industry,
			vtiger_account.annual_revenue,
			vtiger_account.address,
			vtiger_account.makedecision,
			vtiger_account.country,
			vtiger_account.gender,
			vtiger_account.business,
			vtiger_account.regionalpartition,
			(
				SELECT
					modifiedby
				FROM
					vtiger_crmentity
				WHERE
					vtiger_crmentity.crmid = vtiger_account.accountid
				AND vtiger_crmentity.deleted = 0
			) AS modifiedby,
			vtiger_account.title,
			vtiger_account.leadsource,
			vtiger_account.businessarea,
			(
				SELECT
					createdtime
				FROM
					vtiger_crmentity
				WHERE
					vtiger_crmentity.crmid = vtiger_account.accountid
				AND vtiger_crmentity.deleted = 0
			) AS createdtime,
			(
				SELECT
					modifiedtime
				FROM
					vtiger_crmentity
				WHERE
					vtiger_crmentity.crmid = vtiger_account.accountid
				AND vtiger_crmentity.deleted = 0
			) AS modifiedtime,
			(
				SELECT
					description
				FROM
					vtiger_crmentity
				WHERE
					vtiger_crmentity.crmid = vtiger_account.accountid
				AND vtiger_crmentity.deleted = 0
			) AS description,
			vtiger_account.parentid,
			vtiger_account.customerproperty,
			vtiger_account.account_no,
			vtiger_account.accountid,
            vtiger_account.protectday,
			vtiger_account.accountid
		FROM
			vtiger_account
		INNER JOIN vtiger_crmentity ON vtiger_account.accountid = vtiger_crmentity.crmid
		LEFT JOIN vtiger_users AS vtiger_usersassigned_user_id ON vtiger_crmentity.smownerid = vtiger_usersassigned_user_id.id
		LEFT JOIN vtiger_users AS vtiger_usersmodifiedby ON vtiger_crmentity.modifiedby = vtiger_usersmodifiedby.id
		WHERE
			vtiger_crmentity.deleted = 0
		AND vtiger_account.accountid > 0
		AND vtiger_account.accountcategory = 0 AND vtiger_account.protected=0";
		//$where=getAccessibleUsers('Accounts','List');
		//if($where!='1=1'){
		//	$listQuery .= ' AND vtiger_crmentity.smownerid '.$where;
		//}
		$listQuery .= ' AND vtiger_crmentity.smownerid = '.$current_user->id;
		$listQuery.=" AND vtiger_account.protectday<=7 ORDER BY vtiger_account.protectday ASC LIMIT 100";
		
		$db = PearDatabase::getInstance();
		$listResult = $db->pquery($listQuery, array());
		$rows = $db->num_rows($listResult);
		$recordInstances=array();
		for ($i=0; $i<$rows; $i++) {
			$row = $db->query_result_rowdata($listResult, $i);
			$recordInstances[]= $row;
		}
		return $recordInstances;
	}
}
