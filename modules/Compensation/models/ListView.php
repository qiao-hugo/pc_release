<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Compensation_ListView_Model extends Vtiger_ListView_Model {


	/**
	 * Function to get the list of Mass actions for the module
	 * @param $linkParams
	 * @return array <Array> - Associative array of Link type to List of  Vtiger_Link_Model instances for Mass Actions
	 * @internal param $ <Array> $linkParams
	 */
	public function getListViewMassActions($linkParams) {
		$massActionLinks = parent::getListViewMassActions($linkParams);
	
		$currentUserModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		$emailModuleModel = Vtiger_Module_Model::getInstance('Emails');
	
		if($currentUserModel->hasModulePermission($emailModuleModel->getId())) {
			$massActionLink = array(
					'linktype' => 'LISTVIEWMASSACTION',
					'linklabel' => 'LBL_SEND_EMAIL',
					'linkurl' => 'javascript:Vtiger_List_Js.triggerSendEmail("index.php?module='.$this->getModule()->getName().'&view=MassActionAjax&mode=showComposeEmailForm&step=step1","Emails");',
					'linkicon' => ''
			);
			$massActionLinks['LISTVIEWMASSACTION'][] = Vtiger_Link_Model::getInstanceFromValues($massActionLink);
		}
	
		$SMSNotifierModuleModel = Vtiger_Module_Model::getInstance('SMSNotifier');
		if($currentUserModel->hasModulePermission($SMSNotifierModuleModel->getId())) {
			$massActionLink = array(
					'linktype' => 'LISTVIEWMASSACTION',
					'linklabel' => 'LBL_SEND_SMS',
					'linkurl' => 'javascript:Vtiger_List_Js.triggerSendSms("index.php?module='.$this->getModule()->getName().'&view=MassActionAjax&mode=showSendSMSForm","SMSNotifier");',
					'linkicon' => ''
			);
			$massActionLinks['LISTVIEWMASSACTION'][] = Vtiger_Link_Model::getInstanceFromValues($massActionLink);
		}
	
		$moduleModel = $this->getModule();
		if($currentUserModel->hasModuleActionPermission($moduleModel->getId(), 'EditView')) {
			$massActionLink = array(
					'linktype' => 'LISTVIEWMASSACTION',
					'linklabel' => 'LBL_TRANSFER_OWNERSHIP',
					'linkurl' => 'javascript:Vtiger_List_Js.triggerTransferOwnership("index.php?module='.$moduleModel->getName().'&view=MassActionAjax&mode=transferOwnership")',
					'linkicon' => ''
			);
			$massActionLinks['LISTVIEWMASSACTION'][] = Vtiger_Link_Model::getInstanceFromValues($massActionLink);
		}
	
		return $massActionLinks;
	}
	
	/**
	 *
	 * @param $linkParams
	 * @return array
	 */
	function getListViewLinks($linkParams) {
		$links = parent::getListViewLinks($linkParams);
	
		$index=0;
		foreach($links['LISTVIEWBASIC'] as $link) {
			if($link->linklabel == 'Send SMS') {
				unset($links['LISTVIEWBASIC'][$index]);
			}
			$index++;
		}
		return $links;
	}
	
	
	//根据参数显示数据   #移动crm模拟$request请求---2015-12-16 罗志坚
	public function getListViewEntries($pagingModel,$request=array()) {
		$db = PearDatabase::getInstance();
		$moduleName ='Compensation';

		
		if(!empty($request)){
			if(isset($request['BugFreeQuery'])){
				$_REQUEST['BugFreeQuery'] = $request['BugFreeQuery'];
			}
			if(isset($request['public'])){
				$_REQUEST['public'] = $request['public'];
			}
		}

		$orderBy = $this->getForSql('orderby');
		$sortOrder = $this->getForSql('sortorder');

		//List view will be displayed on recently created/modified records
		//列表视图将显示最近的创建修改记录  ---做什么用处
		if(empty($orderBy) && empty($sortOrder)){

			$orderBy = 'compensationid';

			$sortOrder = 'DESC';
		}
		$this->getSearchWhere();
        $listQuery = $this->getQuery();
        
        $listQuery.=$this->getUserWhere();
        global $current_user;


		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();

        $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;
		
		$viewid = ListViewSession::getCurrentView($moduleName);
	
		ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,
	
		$listQuery .= " LIMIT $startIndex,".($pageLimit);

		$public = $_REQUEST['public'];
		if ($public == 'unaudited') {
			$listQuery = $this->getYearsDataSql();
		}
        //echo $listQuery;//die();
		//echo $listQuery;die;
		$listResult = $db->pquery($listQuery, array());


		$index = 0;
		while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['compensationid'];
			$listViewRecordModels[$rawData['compensationid']] = $rawData;
		}
		return $listViewRecordModels;
	}


	public function getYearsDataSql($year) {
		$where = '';
		if (! empty($year)) {
			$where = " AND left(releasedate, 4)='$year' ";
		}
		$sql = "SELECT 
				compensationid,
				CONCAT(releasedate, '合计') as releasedate,
				sum(fixedsalary) as fixedsalary,
				sum(meritpay) as meritpay,
				sum(allowancebonus) as allowancebonus,
				sum(percentage) as percentage,
				sum(debit) as debit,
				sum(wagejointventure) as wagejointventure,
				sum(individualsocialinsuran) as individualsocialinsuran,
				sum(accumulationfund) as accumulationfund,
				sum(individualincometax) as individualincometax,
				sum(taxdeductions) as taxdeductions,
				sum(realwage) as realwage,
				sum(replenishment) as replenishment
				
				FROM vtiger_compensation
				WHERE 1=1
				$where

				GROUP BY releasedate";
		return $sql;
	}
	public function getYearsDataCountSql($year) {
		$where = '';
		if (! empty($year)) {
			$where = " AND left(releasedate, 4)='$year' ";
		}
		$sql = "SELECT count(*) from (
					SELECT 
					count(1)
					FROM vtiger_compensation
					WHERE 1=1
					$where
					GROUP BY releasedate
				) as t";
		return $sql;
	}
	
    public function getListViewHeaders() {
        $sourceModule = $this->get('src_module');
        $queryGenerator = $this->get('query_generator');
        if(!empty($sourceModule)){
           return $queryGenerator->getModule()->getPopupFields();
        }else{

            $list=$queryGenerator->getModule()->getListFields();
            $temp=array();

            $public = $_REQUEST['public'];
		
            foreach($list as $fields){
            	if ($public == 'unaudited') {
					if ($fields['fieldlabel'] != 'insuredtype' && $fields['fieldlabel'] != 'name') {
            			$temp[$fields['fieldlabel']]=$fields;
            		}
				} else {
					$temp[$fields['fieldlabel']]=$fields;
				}
                
            }
            
            return $temp;
        }
        return $queryGenerator->getFocus()->list_fields_name;
        
    }
    public function getUserWhere(){
        return ;
       global $current_user;
        $searchDepartment = $_REQUEST['department'];
        $sourceModule = $this->get('src_module');
        $listQuery=' ';
        //notfollow是未跟进的拜访单的状态followup是跟进后的拜访单状态
  		if($_REQUEST['public']=='unaudited'){
            //审核中待跟进的拜访单
            $listQuery .=" and vtiger_visitingorder.workflowsid=400 and vtiger_visitingorder.followstatus='notfollow'";
            $listQuery .=" and LOCATE('b_',vtiger_visitingorder.modulestatus)>0 ";
        }elseif($_REQUEST['public']=='pass'){
            //已跟进待审核的拜访单
            $listQuery .=" and vtiger_visitingorder.workflowsid=400 and vtiger_visitingorder.followstatus='followup'";
            $listQuery .=" and LOCATE('b_',vtiger_visitingorder.modulestatus)>0 ";
            $listQuery .=" and vtiger_visitingorder.workflowsnode='提单人上级审批' ";
        }elseif($_REQUEST['public']=='FollowUp'){
            //24小时内待跟进的拜访单
            $datetime=time()-86400;
            $newdatetime=date("Y-m-d H:i:s",$datetime);
            $listQuery .=" and vtiger_visitingorder.workflowsid=400 AND  vtiger_visitingorder.followstatus='notfollow'";
            $listQuery .=" and LOCATE('b_',vtiger_visitingorder.modulestatus)>0 ";
            $listQuery .=" AND vtiger_crmentity.createdtime>'{$newdatetime}' ";
        }
        if(!empty($searchDepartment)&&$searchDepartment!='H1'){  //20150525 柳林刚 加入
            $userid=getDepartmentUser($searchDepartment);
            $where=getAccessibleUsers('VisitingOrder','List',true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            //$listQuery .= ' and vtiger_visitingorder.extractid in ('.implode(',',$where).')';

            //$listQuery .= ' and vtiger_crmentity.smownerid in ('.implode(',',$where).')';
            $listQuery .= ' and (vtiger_visitingorder.extractid in ('.implode(',',$where).') OR find_in_set('.$current_user->id.', REPLACE(vtiger_visitingorder.accompany,\' |##| \', \',\') ))';
        }else{
            $where=getAccessibleUsers();
            if($where!='1=1'){
            	//$listQuery .= ' and vtiger_visitingorder.extractid '.$where;
                //$listQuery .= ' and vtiger_crmentity.smownerid '.$where;
                $listQuery .= ' and (vtiger_visitingorder.extractid '.$where . ' OR find_in_set('.$current_user->id.', REPLACE(vtiger_visitingorder.accompany,\' |##| \', \',\') ))';
            }
        }

        //echo $listQuery;
        //exit;
        return $listQuery;
    }
    public function getListViewCount() {
        $db = PearDatabase::getInstance();
        $queryGenerator = $this->get('query_generator');
		//print_r(debug_backtrace(0));
        //搜索条件
        //$this->getSearchWhere();
        //用户条件
        $where=$this->getUserWhere();
        //$where.= ' AND accountname is NOT NULL';
        $queryGenerator->addUserWhere($where);
        $listQuery =  $queryGenerator->getQueryCount();

        $public = $_REQUEST['public'];
		if ($public == 'unaudited') {
			$listQuery = $this->getYearsDataCountSql();
		}

        //echo $listQuery.'<br>';die();
        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }

}