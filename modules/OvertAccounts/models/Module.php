<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ************************************************************************************/

class OvertAccounts_Module_Model extends Vtiger_Module_Model {

	/**
	 * Function to get the Quick Links for the module
	 * @param <Array> $linkParams
	 * @return <Array> List of Vtiger_Link_Model instances
	 */
	public function getSideBarLinks($linkParams) {
		global $current_user;

		$parentQuickLinks = parent::getSideBarLinks($linkParams);
		/*2014-12-22更新人：steel更新内容去掉仪表盘链接	
		$quickLink = array(
			'linktype' => 'SIDEBARLINK',
			'linklabel' => 'LBL_DASHBOARD',
			'linkurl' => $this->getDashBoardUrl(),
			'linkicon' => '',
		);
		*/
		$quickLink[] = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => '我负责的客户',
				'linkurl' => $this->getListViewUrl().'&filter=myaccounts',
				'linkicon' => '',
		);
        $quickLink[] = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '意向客户池',
            'linkurl' => $this->getListViewUrl().'&filter=intentionality',
            'linkicon' => '',
        );
		$quickLink[] = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => '临时区客户',
				'linkurl' => $this->getListViewUrl().'&filter=temporary',
				'linkicon' => '',
		);

		$quickLink[] = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => '公海客户',
				'linkurl' => $this->getListViewUrl(1).'&filter=overt',
				'linkicon' => '',
		);

		
		/* //2014-12-24更新人：steel更新内容导入客户
		$quickLink[] = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => '导入客户',
				'linkurl' => 'index.php?module=Accounts&view=Import',
				'linkicon' => '',
		); */
        /* 20150502 隐藏掉导出，
		$quickLink[] = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => '导出客户',
                //'linkurl' => 'javascript:Vtiger_List_Js.triggerExportAction("'.substr($_SERVER['PATH_INFO'], 0,strripos($_SERVER['PATH_INFO'],'/')).'index.php?module=Accounts&view=Export'.'")',
				'linkurl' => substr($_SERVER['PATH_INFO'], 0,strripos($_SERVER['PATH_INFO'],'/')).'index.php?module=Accounts&view=Export',
				'linkicon' => '',
		);*/
		//2015-1-21 wangbin 添加新的筛选条件
		$quickLink[] = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => '白名单',
				'linkurl' => $this->getListViewUrl().'&filter=white',
				'linkicon' => '',
		);
		$quickLink[] = array(
    		    'linktype' => 'SIDEBARLINK',
    		    'linklabel' => '未成交客户',
    		    'linkurl' => $this->getListViewUrl().'&filter=nohooked',
    		    'linkicon' => '',
		);
		
		$quickLink[] = array(
    		    'linktype' => 'SIDEBARLINK',
    		    'linklabel' => '已成交客户',
    		    'linkurl' => $this->getListViewUrl().'&filter=ishooked',
    		    'linkicon' => '',
		);
		/*$quickLink[] = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => '7天内需跟进',
				'linkurl' => $this->getListViewUrl().'&filter=noseven',
				'linkicon' => '',
		);*/
		$quickLink[] = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => '近期需跟进客户',
				'linkurl' => $this->getListViewUrl().'&filter=nofifity',
				'linkicon' => '',
		);
		
		$quickLink[] = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '近期已跟进客户',
            'linkurl' => $this->getListViewUrl().'&filter=recentFollowUp',
            'linkicon' => '',
        );

		$quickLink[] = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => '最近7天录入客户',
				'linkurl' => $this->getListViewUrl().'&filter=addaccouns',
				'linkicon' => '',
		);
        //young 2015-04-27 作废
        /*
		$quickLink[] = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => '本月新增客户',
				'linkurl' => $this->getListViewUrl().'&filter=addaccouns',
				'linkicon' => '',
		);*/
        $quickLink[] = array(
				'linktype' => 'SIDEBARLINK',
				'linklabel' => '新增客户统计',
				'linkurl' => $this->getListViewUrl().'&report=1',
				'linkicon' => '',
		);
        $quickLink[] = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => '客户负责人变更记录',
            'linkurl' => $this->getListViewUrl().'&filter=changeHistory',
            'linkicon' => '',
        );
		//Check profile permissions for Dashboards 
		$moduleModel = Vtiger_Module_Model::getInstance('Dashboard');
		$userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		$permission = $userPrivilegesModel->hasModulePermission($moduleModel->getId());
		if($permission) {
			foreach ($quickLink as $val){
				$parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($val);
			}
		}

		return $parentQuickLinks;
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
		if (($sourceModule == 'Accounts' && $field == 'account_id' && $record)
				|| in_array($sourceModule, array('Campaigns', 'Products', 'Services', 'Emails'))) {

			if ($sourceModule === 'Campaigns') {
				$condition = " vtiger_account.accountid NOT IN (SELECT accountid FROM vtiger_campaignaccountrel WHERE campaignid = '$record')";
			} elseif ($sourceModule === 'Products') {
				$condition = " vtiger_account.accountid NOT IN (SELECT crmid FROM vtiger_seproductsrel WHERE productid = '$record')";
			} elseif ($sourceModule === 'Services') {
				$condition = " vtiger_account.accountid NOT IN (SELECT relcrmid FROM vtiger_crmentityrel WHERE crmid = '$record' UNION SELECT crmid FROM vtiger_crmentityrel WHERE relcrmid = '$record') ";
			} elseif ($sourceModule === 'Emails') {
				$condition = ' vtiger_account.emailoptout = 0';
			} else {
				$condition = " vtiger_account.accountid != '$record'";
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
	 * Function to get relation query for particular module with function name
	 * @param <record> $recordId
	 * @param <String> $functionName
	 * @param Vtiger_Module_Model $relatedModule
	 * @return <String>
	 */
	public function getRelationQuery($recordId, $functionName, $relatedModule) {
		$arr=array('get_payments'=>'vtiger_receivedpayments.receivedpaymentsid','get_products'=>'vtiger_salesorderproductsrel.salesorderproductsrelid',
		'get_servicecomplaints'=>'vtiger_servicecomplaints.servicecomplaintsid');
		if ($functionName === 'get_activities') {
			$focus = CRMEntity::getInstance($this->getName());
			$focus->id = $recordId;
			$entityIds = $focus->getRelatedContactsIds();
			$entityIds = implode(',', $entityIds);

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
								AND (vtiger_seactivityrel.crmid = ".$recordId;
			if($entityIds) {
				$query .= " OR vtiger_cntactivityrel.contactid IN (".$entityIds."))";
			} else {
				$query .= ")";
			}
			$relatedModuleName = $relatedModule->getName();
			$query .= $this->getSpecificRelationQuery($relatedModuleName);
			$nonAdminQuery = $this->getNonAdminAccessControlQueryForRelation($relatedModuleName);
			if ($nonAdminQuery) {
				$query = appendFromClauseToQuery($query, $nonAdminQuery);
			}

			// There could be more than one contact for an activity.
			$query .= ' GROUP BY vtiger_activity.activityid';
		} else if (in_array($functionName,array_keys($arr))) {
			
			$relatedModuleName = $relatedModule->getName();
	
			$focus = CRMEntity::getInstance($this->getName());
			$focus->id = $recordId;
	
			$result = $focus->$functionName($recordId, $this->getId(), $relatedModule->getId());
			$query = $result['query'] .' '. $this->getSpecificRelationQuery($relatedModuleName);
			$nonAdminQuery = $this->getNonAdminAccessControlQueryForRelation($relatedModuleName);
	
			//modify query if any module has summary fields, those fields we are displayed in related list of that module
			$relatedListFields = $relatedModule->getConfigureRelatedListFields();
			if(count($relatedListFields) > 0) {
				$currentUser = Users_Record_Model::getCurrentUserModel();
				$queryGenerator = new QueryGenerator($relatedModuleName, $currentUser);
				$queryGenerator->setFields($relatedListFields);
				$selectColumnSql = $queryGenerator->getSelectClauseColumnSQL();
				$newQuery = spliti('FROM', $query);
				$selectColumnSql = 'SELECT '.$arr[$functionName].' as crmid,'.$selectColumnSql;
				$query = $selectColumnSql.' FROM '.$newQuery[1];
			}
			if ($nonAdminQuery) {
				$query = appendFromClauseToQuery($query, $nonAdminQuery);
			}
		} elseif($functionName === 'get_contacts'){//临时解决方案这里
			$relatedModuleName = $relatedModule->getName();
	
			$focus = CRMEntity::getInstance($this->getName());
			$focus->id = $recordId;
	
			$result = $focus->$functionName($recordId, $this->getId(), $relatedModule->getId());
			$query = $result['query'] ;
			$nonAdminQuery = $this->getNonAdminAccessControlQueryForRelation($relatedModuleName);
	
			//modify query if any module has summary fields, those fields we are displayed in related list of that module
			
			if ($nonAdminQuery) {
				$query = appendFromClauseToQuery($query, $nonAdminQuery);
			}
		} else{
			$query = parent::getRelationQuery($recordId, $functionName, $relatedModule);
		}

		return $query;
	}
	/**
	 * 
	 * 2014-12-23 更新人：steel
	 * 更新内容在原有的其础上添加客户消费级别：关联工单数据表如工单数据表中有工单按1-10单为铁牌客户,11-20为铜牌客户,21-30为银牌客户,30单以上为金银客户
	 * 仍要解决的问题只要工单数据表中有记录不管该单是否OK都为客户的消费等级,不合理.需要做的是该单审核OK才能算为客户的消费等级
	 * 
	 */
	 
	static public function accountLevel($recordId){
		if(!empty($recordId)){
			$db = PearDatabase::getInstance();
			$query = "SELECT COUNT(vtiger_salesorder.accountid) AS sumid FROM vtiger_salesorder  
                      JOIN `vtiger_salesorderworkflowstages` 
                      ON `vtiger_salesorderworkflowstages`.salesorderid=vtiger_salesorder.salesorderid 
                      AND `vtiger_salesorderworkflowstages`.`sequence`=(SELECT MAX(sequence) FROM `vtiger_salesorderworkflowstages` 
                      WHERE `vtiger_salesorderworkflowstages`.`salesorderid`=vtiger_salesorder.salesorderid) 
                      AND `vtiger_salesorderworkflowstages`.`schedule`='100' 
                      WHERE accountid=?";
			$result=$db->pquery($query,array($recordId));
			$sumid=$db->query_result($result, 0, 'sumid');
			$consumelevel_query="select consumelevel from vtiger_consumelevel where consumelevelid=?";
			$query = "update vtiger_account set consumelevel=? WHERE accountid=?";
			$aquery="select consumelevel from vtiger_account where accountid=?";
			if($sumid>0){
				if(in_array($sumid,range(1,10))){
				//铁牌会员
					$result=$db->pquery($consumelevel_query,array(4));
					$result_name=$db->query_result($result, 0, 'consumelevel');
					$result=$db->pquery($aquery,array($recordId));
					$result_account=$db->query_result($result, 0, 'consumelevel');
					
					if($result_account!=$result_name){
						$db->pquery($query,array($result_name,$recordId));
					}	
				}elseif(in_array($sumid,range(11,20))){
					//铜牌会员	
					$result=$db->pquery($consumelevel_query,array(3));
					$result_name=$db->query_result($result, 0, 'consumelevel');
					$result=$db->pquery($aquery,array($recordId));
					$result_account=$db->query_result($result, 0, 'consumelevel');
					
					if($result_account!=$result_name){
						$db->pquery($query,array($result_name,$recordId));
					}	
				}elseif(in_array($sumid,range(21,30))){
					//银牌会员
					$result=$db->pquery($consumelevel_query,array(2));
					$result_name=$db->query_result($result, 0, 'consumelevel');
					$result=$db->pquery($aquery,array($recordId));
					$result_account=$db->query_result($result, 0, 'consumelevel');
					
					if($result_account!=$result_name){
						$db->pquery($query,array($result_name,$recordId));
					}	
				}else{
					//金牌会员
					$result=$db->pquery($consumelevel_query,array(1));
					$result_name=$db->query_result($result, 0, 'consumelevel');
					$result=$db->pquery($aquery,array($recordId));
					$result_account=$db->query_result($result, 0, 'consumelevel');
					
					if($result_account!=$result_name){
						$db->pquery($query,array($result_name,$recordId));
					}	
				}
			}else{
				//未成交用户
				$result=$db->pquery($consumelevel_query,array(5));
				$result_name=$db->query_result($result, 0, 'consumelevel');
				$result=$db->pquery($aquery,array($recordId));
				$result_account=$db->query_result($result, 0, 'consumelevel');
				if($result_account!=$result_name){
					$db->pquery($query,array($result_name,$recordId));
				}	
			}
		}
	}
	//是否保护客户权限
	function isprotected(){
		global $current_user;
		if(!empty($current_user->viewPermission['Accounts/Protect'])){
			return true;
		}
		return false;
	}
    /**
     *移动端添加拜访单时查询对应客户列表
     * Function searches the records in the module, if parentId & parentModule
     * is given then searches only those records related to them.
     * @param <String> $searchValue - Search value
     * @param <Integer> $parentId - parent recordId
     * @param <String> $parentModule - parent module name
     * @return <Array of Vtiger_Record_Model>
     */
    public function searchRecordAPP($searchValue, $parentId=false, $parentModule=false, $relatedModule=false) {
        if(!empty($searchValue) && empty($parentId) && empty($parentModule)) {
            $matchingRecords = $this->getSearchResultApp($searchValue, $this->getName());
        }

        return $matchingRecords;
    }

    /**移动端添加拜访单时查询对应的客户
     * @param $searchKey
     * @param bool $module
     * @return array
     * @throws AppException
     * @throws Exception
     */
    private function  getSearchResultApp($searchKey, $module=false){
        $db = PearDatabase::getInstance();

        $query = 'SELECT label, crmid, setype, createdtime FROM vtiger_crmentity  LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_crmentity.crmid WHERE label LIKE ? AND vtiger_crmentity.deleted = 0  AND vtiger_account.accountcategory=0';
        $params = array("%$searchKey%");
        $where=getAccessibleUsers();
        if($where!='1=1'){
            $query .= ' and (vtiger_crmentity.smownerid '.$where.$this->getShareAccount().')';
        }

        if($module !== false) {
            $query .= ' AND setype = ?';
            $params[] = $module;
        }
        //Remove the ordering for now to improve the speed
        //$query .= ' ORDER BY createdtime DESC';

        $result = $db->pquery($query, $params);
        $noOfRows = $db->num_rows($result);

        $moduleModels = $matchingRecords = $leadIdsList = array();
        for($i=0; $i<$noOfRows; ++$i) {
            $row = $db->query_result_rowdata($result, $i);
            if ($row['setype'] === 'Leads') {
                $leadIdsList[] = $row['crmid'];
            }
        }
        $convertedInfo = Leads_Module_Model::getConvertedInfo($leadIdsList);

        for($i=0, $recordsCount = 0; $i<$noOfRows && $recordsCount<100; ++$i) {
            $row = $db->query_result_rowdata($result, $i);
            if ($row['setype'] === 'Leads' && $convertedInfo[$row['crmid']]) {
                continue;
            }
            if(Users_Privileges_Model::isPermitted($row['setype'], 'DetailView', $row['crmid'])) {
                $row['id'] = $row['crmid'];
                $moduleName = $row['setype'];
                if(!array_key_exists($moduleName, $moduleModels)) {
                    $moduleModels[$moduleName] = Vtiger_Module_Model::getInstance($moduleName);
                }
                $moduleModel = $moduleModels[$moduleName];
                $modelClassName = Vtiger_Loader::getComponentClassName('Model', 'Record', $moduleName);
                $recordInstance = new $modelClassName();
                $matchingRecords[$moduleName][$row['id']] = $recordInstance->setData($row)->setModuleFromInstance($moduleModel);
                $recordsCount++;
            }
        }
        return $matchingRecords;
    }
    public function getShareAccount(){
        global $adb,$current_user;
        $result=$adb->pquery('SELECT accountid FROM vtiger_shareaccount WHERE userid=? AND sharestatus=1',array($current_user->id));
        $accountList='';
        if($adb->num_rows($result)){
            while($rowdata=$adb->fetch_array($result)){$accountList[]=$rowdata['accountid'];}

            $accountList=' OR vtiger_account.accountid in('.implode(',',$accountList).')';
        }
        return $accountList;
    }

    /**
     * Function to get the url for list view of the module
     * @return <string> - url
     */
    public function getListViewUrl($overtFlag=0) {
        if($overtFlag == 1){
            //公海客户
            return 'index.php?module='.$this->get('name').'&view='.$this->getListViewName();
        }else{
            //非公海客户
            return 'index.php?module=Accounts&view='.$this->getListViewName();
        }
    }
}