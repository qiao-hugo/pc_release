<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Leads_Module_Model extends Vtiger_Module_Model {
	/**
	 * Function to get the Quick Links for the module
	 * @param <Array> $linkParams
	 * @return <Array> List of Vtiger_Link_Model instances
	 */
	public function getSideBarLinks($linkParams) {
		$linkerks = parent::getSideBarLinks($linkParams);

		/*$quickLink = array(
			'linktype' => 'SIDEBARLINK',
			'linklabel' => 'LBL_DASHBOARD',
			'linkurl' => $this->getDashBoardUrl(),
			'linkicon' => '',
		);*/

       $quickLink4 = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '一个月未跟进商机',
            'linkurl' => $this->getListViewUrl().'&filter=moreMonth',
            'linkicon' => '',
        );
        $quickLink5 = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '一周未跟进商机',
            'linkurl' => $this->getListViewUrl().'&filter=threeMonth',
            'linkicon' => '',
        );
	$quickLink7 = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '公海商机',
            'linkurl' => $this->getListViewUrl().'&filter=overt',
            'linkicon' => '',
        );

		//Check profile permissions for Dashboards
		$moduleModel = Vtiger_Module_Model::getInstance('Dashboard');
		$userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
//		$permission = $userPrivilegesModel->hasModulePermission($moduleModel->getId());
//		if($permission) {
			//$links['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);
            $linkerks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink4);
            $linkerks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink5);
	         $linkerks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink7);
//		}
        if(self::exportGrouprt('Leads', 'accountsearch')){
            $quickLink6 = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '客户搜索',
                'linkurl' => $this->getListViewUrl().'&filter=accountSearch',
                'linkicon' => '',
            );
            $linkerks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink6);
        }

        if(self::exportGrouprt('Leads', 'accountdeal')){
            $quickLink6 = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '成交客户列表',
                'linkurl' => $this->getListViewUrl().'&filter=accountDeal',
                'linkicon' => '',
            );
            $linkerks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink6);
        }
	if(self::exportGrouprt('Leads', 'leadaccountdeal')){
            $quickLink6 = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '商机成交客户列表',
                'linkurl' => $this->getListViewUrl().'&filter=leadaccountdeal',
                'linkicon' => '',
            );
            $linkerks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink6);
        }
        if(self::exportGrouprt('Leads', 'leadsetting')){
            $quickLink6 = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '商机系统设置',
                'linkurl' => $this->getListViewUrl().'&filter=leadsetting',
                'linkicon' => '',
            );
            $linkerks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink6);
        }
        if(self::exportGrouprt('Leads', 'leadbatch')){
            $quickLink6 = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '商机批量操作',
                'linkurl' => $this->getListViewUrl().'&filter=leadbatch',
                'linkicon' => '',
            );
            $linkerks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink6);
        }
        if(self::exportGrouprt('Leads', 'assignpersonal')) {

            $quickLink7 = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '分配人员设置',
                'linkurl' => $this->getListViewUrl() . '&filter=assignpersonal',
                'linkicon' => '',
            );
            $linkerks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink7);
        }
        if(self::exportGrouprt('Leads', 'ClueSharing')) {

            $quickLink8 = array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => '线索分成设置',
                'linkurl' => $this->getListViewUrl() . '&filter=cluesharing',
                'linkicon' => '',
            );
            $linkerks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink8);
        }
		return $linkerks;
	}

    /**
	 * Function returns Settings Links
	 * @return Array
	 */
	public function getSettingLinks() {
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$settingLinks = parent::getSettingLinks();

		if($currentUserModel->isAdminUser()) {
			$settingLinks[] = array(
					'linktype' => 'LISTVIEWSETTING',
					'linklabel' => 'LBL_CUSTOM_FIELD_MAPPING',
					'linkurl' => 'index.php?parent=Settings&module=Leads&view=MappingDetail',
					'linkicon' => '');
			$settingLinks[] =	array(
					'linktype' => 'LISTVIEWSETTING',
					'linklabel' => 'LBL_WEBFORMS',
					'linkurl' => 'index.php?module=Webforms&parent=Settings&view=Edit',
					'linkicon' => '');
		}
		return $settingLinks;
	}

    /**
    * Function returns deleted records condition
    */
    public function getDeletedRecordCondition() {
       return 'vtiger_crmentity.deleted = 0 AND vtiger_leaddetails.converted = 0';
    }

    /**
	 * Function to get the list of recently visisted records
	 * @param <Number> $limit
	 * @return <Array> - List of Vtiger_Record_Model or Module Specific Record Model instances
	 */
	public function getRecentRecords($limit=10) {
		$db = PearDatabase::getInstance();

		$currentUserModel = Users_Record_Model::getCurrentUserModel();
        $deletedCondition = $this->getDeletedRecordCondition();
		$query = 'SELECT * FROM vtiger_crmentity '.
            ' INNER JOIN vtiger_leaddetails ON
                vtiger_leaddetails.leadid = vtiger_crmentity.crmid
                WHERE setype=? AND '.$deletedCondition.' AND modifiedby = ? ORDER BY modifiedtime DESC LIMIT ?';
		$params = array($this->get('name'), $currentUserModel->id, $limit);
		$result = $db->pquery($query, $params);
		$noOfRows = $db->num_rows($result);

		$recentRecords = array();
		for($i=0; $i<$noOfRows; ++$i) {
			$row = $db->query_result_rowdata($result, $i);
			$row['id'] = $row['crmid'];
			$recentRecords[$row['id']] = $this->getRecordFromArray($row);
		}
		return $recentRecords;
	}

	/**
	 * Function returns the Number of Leads created per week
	 * @param type $data
	 * @return <Array>
	 */
	public function getLeadsCreated($owner, $dateFilter) {
		$db = PearDatabase::getInstance();

		$ownerSql = $this->getOwnerWhereConditionForDashBoards($owner);
		if(!empty($ownerSql)) {
			$ownerSql = ' AND '.$ownerSql;
		}

		$params = array();
		if(!empty($dateFilter)) {
			$dateFilterSql = ' AND createdtime BETWEEN ? AND ? ';
			//client is not giving time frame so we are appending it
			$params[] = $dateFilter['start']. ' 00:00:00';
			$params[] = $dateFilter['end']. ' 23:59:59';
		}

		$result = $db->pquery('SELECT COUNT(*) AS count, date(createdtime) AS time FROM vtiger_leaddetails
						INNER JOIN vtiger_crmentity ON vtiger_leaddetails.leadid = vtiger_crmentity.crmid
						AND deleted=0 '.Users_Privileges_Model::getNonAdminAccessControlQuery($this->getName()).$ownerSql.' '.$dateFilterSql.' AND converted = 0 GROUP BY week(createdtime)',
					$params);

		$response = array();
		for($i=0; $i<$db->num_rows($result); $i++) {
			$row = $db->query_result_rowdata($result, $i);
			$response[$i][0] = $row['count'];
			$response[$i][1] = $row['time'];
		}
		return $response;
	}

	/**
	 * Function returns Leads grouped by Status
	 * @param type $data
	 * @return <Array>
	 */
	public function getLeadsByStatus($owner,$dateFilter) {
		$db = PearDatabase::getInstance();

		$ownerSql = $this->getOwnerWhereConditionForDashBoards($owner);
		if(!empty($ownerSql)) {
			$ownerSql = ' AND '.$ownerSql;
		}

		$params = array();
		if(!empty($dateFilter)) {
			$dateFilterSql = ' AND createdtime BETWEEN ? AND ? ';
			//client is not giving time frame so we are appending it
			$params[] = $dateFilter['start']. ' 00:00:00';
			$params[] = $dateFilter['end']. ' 23:59:59';
		}

// 		$result = $db->pquery('SELECT COUNT(*) as count, CASE WHEN vtiger_leadstatus.leadstatus IS NULL OR vtiger_leadstatus.leadstatus = "" THEN "" ELSE
// 						vtiger_leadstatus.leadstatus END AS leadstatusvalue FROM vtiger_leaddetails
// 						INNER JOIN vtiger_crmentity ON vtiger_leaddetails.leadid = vtiger_crmentity.crmid
// 						AND deleted=0 AND converted = 0 '.Users_Privileges_Model::getNonAdminAccessControlQuery($this->getName()). $ownerSql .' '.$dateFilterSql.
// 						'INNER JOIN vtiger_leadstatus ON vtiger_leaddetails.leadstatus = vtiger_leadstatus.leadstatus
// 						GROUP BY leadstatusvalue ORDER BY vtiger_leadstatus.sortorderid', $params);
        //客户等级统计
        $sql='select * from (SELECT COUNT(*) as count, CASE WHEN vtiger_account.accountrank IS NULL OR vtiger_account.accountrank = "" THEN "" ELSE
						vtiger_account.accountrank END AS leadstatusvalue FROM vtiger_account
						INNER JOIN vtiger_crmentity ON vtiger_account.accountid = vtiger_crmentity.crmid
						AND vtiger_crmentity.deleted=0 '.Users_Privileges_Model::getNonAdminAccessControlQuery($this->getName()). $ownerSql .' '.$dateFilterSql.' GROUP BY leadstatusvalue) T ORDER BY count DESC LIMIT 1,10';
        $result = $db->pquery($sql, $params);
		$response = array();

		for($i=0; $i<$db->num_rows($result); $i++) {
			$row = $db->query_result_rowdata($result, $i);
			$response[$i][0] = $row['count'];
			$leadStatusVal = $row['leadstatusvalue'];
			if($leadStatusVal == '') {
				$leadStatusVal = 'LBL_BLANK';
			}
			$response[$i][1] = vtranslate($leadStatusVal, $this->getName());
			$response[$i][2] = $leadStatusVal;
		}
		return $response;
	}

	/**
	 * Function returns Leads grouped by Source
	 * @param type $data
	 * @return <Array>
	 */
	public function getLeadsBySource($owner,$dateFilter) {
		$db = PearDatabase::getInstance();

		$ownerSql = $this->getOwnerWhereConditionForDashBoards($owner);
		if(!empty($ownerSql)) {
			$ownerSql = ' AND '.$ownerSql;
		}

		$params = array();
		if(!empty($dateFilter)) {
			$dateFilterSql = ' AND createdtime BETWEEN ? AND ? ';
			//client is not giving time frame so we are appending it
			$params[] = $dateFilter['start']. ' 00:00:00';
			$params[] = $dateFilter['end']. ' 23:59:59';
		}

// 		$result = $db->pquery('SELECT COUNT(*) as count, CASE WHEN vtiger_leaddetails.leadsource IS NULL OR vtiger_leaddetails.leadsource = "" THEN ""
// 						ELSE vtiger_leaddetails.leadsource END AS leadsourcevalue FROM vtiger_leaddetails
// 						INNER JOIN vtiger_crmentity ON vtiger_leaddetails.leadid = vtiger_crmentity.crmid
// 						AND deleted=0 AND converted = 0 '.Users_Privileges_Model::getNonAdminAccessControlQuery($this->getName()). $ownerSql .' '.$dateFilterSql.
// 						'INNER JOIN vtiger_leadsource ON vtiger_leaddetails.leadsource = vtiger_leadsource.leadsource
// 						GROUP BY leadsourcevalue ORDER BY vtiger_leadsource.sortorderid', $params);

		//客户来源统计
		$sql='select * from (SELECT COUNT(*) as count, CASE WHEN vtiger_account.leadsource IS NULL OR vtiger_account.leadsource = "" THEN "" ELSE
						vtiger_account.leadsource END AS leadsourcevalue FROM vtiger_account
						INNER JOIN vtiger_crmentity ON vtiger_account.accountid = vtiger_crmentity.crmid
						AND vtiger_crmentity.deleted=0 '.Users_Privileges_Model::getNonAdminAccessControlQuery($this->getName()). $ownerSql .' '.$dateFilterSql.' GROUP BY leadsourcevalue) T ORDER BY count DESC LIMIT 1,10';
		$result = $db->pquery($sql, $params);

		$response = array();
		for($i=0; $i<$db->num_rows($result); $i++) {
			$row = $db->query_result_rowdata($result, $i);
			$response[$i][0] = $row['count'];
			$leadSourceVal =  $row['leadsourcevalue'];
			if($leadSourceVal == '') {
				$leadSourceVal = 'LBL_BLANK';
			}
			$response[$i][1] = vtranslate($leadSourceVal, $this->getName());
			$response[$i][2] = $leadSourceVal;
		}
		return $response;
	}

	/**
	 * Function returns Leads grouped by Industry
	 * @param type $data
	 * @return <Array>
	 */
	public function getLeadsByIndustry($owner,$dateFilter) {
		$db = PearDatabase::getInstance();

		$ownerSql = $this->getOwnerWhereConditionForDashBoards($owner);
		if(!empty($ownerSql)) {
			$ownerSql = ' AND '.$ownerSql;
		}

		$params = array();
		if(!empty($dateFilter)) {
			$dateFilterSql = ' AND createdtime BETWEEN ? AND ? ';
			//client is not giving time frame so we are appending it
			$params[] = $dateFilter['start']. ' 00:00:00';
			$params[] = $dateFilter['end']. ' 23:59:59';
		}

// 		$result = $db->pquery('SELECT COUNT(*) as count, CASE WHEN vtiger_leaddetails.industry IS NULL OR vtiger_leaddetails.industry = "" THEN ""
// 						ELSE vtiger_leaddetails.industry END AS industryvalue FROM vtiger_leaddetails
// 						INNER JOIN vtiger_crmentity ON vtiger_leaddetails.leadid = vtiger_crmentity.crmid
// 						AND deleted=0 AND converted = 0 '.Users_Privileges_Model::getNonAdminAccessControlQuery($this->getName()). $ownerSql .' '.$dateFilterSql.'
// 						INNER JOIN vtiger_industry ON vtiger_leaddetails.industry = vtiger_industry.industry
// 						GROUP BY industryvalue ORDER BY vtiger_industry.sortorderid', $params);
		//客户行业统计
		$sql='select * from (SELECT COUNT(*) as count, CASE WHEN vtiger_account.industry IS NULL OR vtiger_account.industry = "" THEN "" ELSE
						vtiger_account.industry END AS industryvalue FROM vtiger_account
						INNER JOIN vtiger_crmentity ON vtiger_account.accountid = vtiger_crmentity.crmid
						AND vtiger_crmentity.deleted=0 '.Users_Privileges_Model::getNonAdminAccessControlQuery($this->getName()). $ownerSql .' '.$dateFilterSql.' GROUP BY industryvalue) T ORDER BY count DESC LIMIT 1,10';
		$result = $db->pquery($sql, $params);

		$response = array();
		for($i=0; $i<$db->num_rows($result); $i++) {
			$row = $db->query_result_rowdata($result, $i);
			$response[$i][0] = $row['count'];
			$industyValue = $row['industryvalue'];
			if($industyValue == '') {
				$industyValue = 'LBL_BLANK';
			}
			$response[$i][1] = vtranslate($industyValue, $this->getName());
			$response[$i][2] = $industyValue;
		}
		return $response;
	}

	/**
	 * Function to get relation query for particular module with function name
	 * @param <record> $recordId
	 * @param <String> $functionName
	 * @param Vtiger_Module_Model $relatedModule
	 * @return <String>
	 */
	public function getRelationQuery($recordId, $functionName, $relatedModule) {
		if ($functionName === 'get_activities') {
			$userNameSql = getSqlForNameInDisplayFormat(array('first_name' => 'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');

			$query = "SELECT CASE WHEN (vtiger_users.user_name not like '') THEN $userNameSql ELSE vtiger_groups.groupname END AS user_name,
						vtiger_crmentity.*, vtiger_activity.activitytype, vtiger_activity.subject, vtiger_activity.date_start, vtiger_activity.time_start,
						vtiger_activity.recurringtype, vtiger_activity.due_date, vtiger_activity.time_end, vtiger_seactivityrel.crmid AS parent_id,
						CASE WHEN (vtiger_activity.activitytype = 'Task') THEN (vtiger_activity.status) ELSE (vtiger_activity.eventstatus) END AS status
						FROM vtiger_activity
						INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_activity.activityid
						LEFT JOIN vtiger_seactivityrel ON vtiger_seactivityrel.activityid = vtiger_activity.activityid
						LEFT JOIN vtiger_cntactivityrel ON vtiger_cntactivityrel.activityid = vtiger_activity.activityid
						LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
						LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
							WHERE vtiger_crmentity.deleted = 0 AND vtiger_activity.activitytype <> 'Emails'
								AND vtiger_seactivityrel.crmid = ".$recordId;

			$relatedModuleName = $relatedModule->getName();
			$query .= $this->getSpecificRelationQuery($relatedModuleName);
			$nonAdminQuery = $this->getNonAdminAccessControlQueryForRelation($relatedModuleName);
			if ($nonAdminQuery) {
				$query = appendFromClauseToQuery($query, $nonAdminQuery);
			}
		} else {
			$query = parent::getRelationQuery($recordId, $functionName, $relatedModule);
		}

		return $query;
	}

	/**
	 * Function to get Converted Information for selected records
	 * @param <array> $recordIdsList
	 * @return <array> converted Info
	 */
	public static function getConvertedInfo($recordIdsList = array()) {
		$convertedInfo = array();
		if ($recordIdsList) {
			$db = PearDatabase::getInstance();
			$result = $db->pquery("SELECT converted FROM vtiger_leaddetails WHERE leadid IN (".generateQuestionMarks($recordIdsList).")", $recordIdsList);
			$numOfRows = $db->num_rows($result);

			for ($i=0; $i<$numOfRows; $i++) {
				$convertedInfo[$recordIdsList[$i]] = $db->query_result($result, $i, 'converted');
			}
		}
		return $convertedInfo;
	}

	/**
	 * Function to get list view query for popup window
	 * @param <String> $sourceModule Parent module
	 * @param <String> $field parent fieldname
	 * @param <Integer> $record parent id
	 * @param <String> $listQuery
	 * @return <String> Listview Query
	 */
	public function getQueryByModuleField($sourceModule, $field, $record, $listQuery) {
		if (in_array($sourceModule, array('Campaigns', 'Products', 'Services', 'Emails'))) {
			switch ($sourceModule) {
				case 'Campaigns'	: $tableName = 'vtiger_campaignleadrel';	$fieldName = 'leadid';	$relatedFieldName ='campaignid';	break;
				case 'Products'		: $tableName = 'vtiger_seproductsrel';		$fieldName = 'crmid';		$relatedFieldName ='productid';		break;
			}

			if ($sourceModule === 'Services') {
				$condition = " vtiger_leaddetails.leadid NOT IN (SELECT relcrmid FROM vtiger_crmentityrel WHERE crmid = '$record' UNION SELECT crmid FROM vtiger_crmentityrel WHERE relcrmid = '$record') ";
			} elseif ($sourceModule === 'Emails') {
				$condition = ' vtiger_leaddetails.emailoptout = 0';
			} else {
				$condition = " vtiger_leaddetails.leadid NOT IN (SELECT $fieldName FROM $tableName WHERE $relatedFieldName = '$record')";
			}

			$position = stripos($listQuery, 'where');
			if($position) {
				$split = spliti('where', $listQuery);
				$overRideQuery = $split[0] . ' WHERE ' . $split[1] . ' AND ' . $condition;
			} else {
				$overRideQuery = $listQuery. ' WHERE ' . $condition;
			}
			return $overRideQuery;
		}
	}
	/**
     * 个人权限配置
     * @param $module 模块名称
     * @param $classname 方法
     * @return bool
     */
    public function exportGrouprt($module,$classname){
        global $current_user;
        $id=$current_user->id;
        $db=PearDatabase::getInstance();
        $query="SELECT 1 FROM vtiger_exportmanage WHERE deleted=0 AND userid=? AND module=? AND classname=?";
        $result=$db->pquery($query,array($id,$module,$classname));
        $num=$db->num_rows($result);
        if($num){
            return true;
        }
        return false;
    }
}
