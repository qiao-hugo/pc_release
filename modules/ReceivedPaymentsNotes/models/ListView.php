<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ReceivedPaymentsNotes_ListView_Model extends Vtiger_ListView_Model {


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
//		return $links;
	}
	
	
	//根据参数显示数据   #移动crm模拟$request请求---2015-12-16 罗志坚
	public function getListViewEntries($pagingModel,$request=array()) {
		$db = PearDatabase::getInstance();
		$moduleName ='ReceivedPaymentsNotes';

		
		if(!empty($request)){
			if(isset($request['BugFreeQuery'])){
				$_REQUEST['BugFreeQuery'] = $request['BugFreeQuery'];
			}
			if(isset($request['public'])){
				$_REQUEST['public'] = $request['public'];
			}
		}

		if($_REQUEST['public']=='Unbound'){
            return $this->getListViewEntriesUnbound($pagingModel,$request);
        }

		$orderBy = $this->getForSql('orderby');
		$sortOrder = $this->getForSql('sortorder');

		//List view will be displayed on recently created/modified records
		//列表视图将显示最近的创建修改记录  ---做什么用处
		if(empty($orderBy) && empty($sortOrder)){

			$orderBy = 'receivedpaymentsnotesid';

			$sortOrder = 'DESC';
		}
		$this->getSearchWhere();
        $listQuery = $this->getQuery();

        $listQuery=str_replace('FROM vtiger_receivedpayments_notes','FROM ( SELECT * FROM ( SELECT * FROM vtiger_receivedpayments_notes ORDER BY createtime DESC ) AS vtiger_receivedpayments_notes GROUP BY receivedpaymentsid ) AS vtiger_receivedpayments_notes ',$listQuery);

        $listQuery=str_replace('matchstatus = \'匹配解除中\'','matchstatus =1 ',$listQuery);

        $listQuery=str_replace('vtiger_receivedpayments.matchstatus = \'已匹配\' AND vtiger_receivedpayments.matchstatus IS NOT NULL','vtiger_receivedpayments.matchstatus is  null',$listQuery);

        $listQuery.=$this->getUserWhere();
        global $current_user;


		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();

        $listQuery=str_replace('WHERE 1=1','LEFT JOIN vtiger_account ON vtiger_account.accountid = (select IF(vtiger_servicecontracts.sc_related_to>0,vtiger_servicecontracts.sc_related_to,vtiger_servicecontracts.old_sc_related_to) from vtiger_servicecontracts where vtiger_servicecontracts.servicecontractsid = vtiger_receivedpayments.relatetoid) LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_account.accountid WHERE 1=1',$listQuery);


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

		$isShowButton=0;
        $moduleModel = Vtiger_Module_Model::getInstance('ReceivedPaymentsNotes');//module相关的数据
        if($moduleModel->exportGrouprt('ReceivedPaymentsNotes','confirmrelieve')){
            //如果是财务，只展示
            $isShowButton=1;
        }

		$index = 0;
		while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['receivedpaymentsnotesid'];
            $rawData['isShowButton'] = $isShowButton;
			$listViewRecordModels[$rawData['receivedpaymentsnotesid']] = $rawData;
		}
		return $listViewRecordModels;
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
	    global $current_user;
        $searchDepartment = $_REQUEST['department'];
        $listQuery='';
        if(!empty($searchDepartment)&&$searchDepartment!='H1'){  //20150525 柳林刚 加入
            $userid=getDepartmentUser($searchDepartment);
            $where=getAccessibleUsers('ReceivedPaymentsNotes','List',true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $where=!empty($where)?$where:array(-1);
            //$listQuery= ' AND (EXISTS(SELECT 1 FROM vtiger_crmentity as crmtable WHERE  crmtable.deleted=0 AND crmtable.setype=\'Accounts\' AND crmtable.crmid=vtiger_servicecontracts.sc_related_to AND crmtable.smownerid in('.implode(',',$where).'))  OR vtiger_crmentity.smownerid in ('.implode(',',$where).'))';
//            //处理客户匹配
            $userImport=' or (vtiger_crmentity.smownerid in ('.implode(',',$where).') and vtiger_receivedpayments_notes.smownerid = 6934)';

            $listQuery= ' AND (vtiger_receivedpayments_notes.smownerid in ('.implode(',',$where).')'.$userImport.' )';
        }else{
            $where=getAccessibleUsers();
            if($where!='1=1'){
                //$listQuery= ' AND (EXISTS(SELECT 1 FROM vtiger_crmentity as crmtable WHERE  crmtable.deleted=0 AND crmtable.setype=\'Accounts\' AND crmtable.crmid=vtiger_servicecontracts.sc_related_to AND crmtable.smownerid '.$where.')  OR vtiger_crmentity.smownerid '.$where.')';
                $userImport=' or (vtiger_crmentity.smownerid '.$where.' and vtiger_receivedpayments_notes.smownerid = 6934)';
                $listQuery= ' AND (vtiger_receivedpayments_notes.smownerid '.$where.$userImport.')';
            }
        }

        $listQuery.=" and vtiger_receivedpayments.relatetoid > 0 and vtiger_receivedpayments.receivedstatus='normal' ";
        return $listQuery;
    }
    public function getListViewCount() {
        if($_REQUEST['public']=='Unbound'){
            return $this->getListViewCountUnbound();
        }

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

        $listQuery=str_replace('FROM vtiger_receivedpayments_notes','FROM ( SELECT * FROM ( SELECT * FROM vtiger_receivedpayments_notes ORDER BY createtime DESC ) AS vtiger_receivedpayments_notes GROUP BY receivedpaymentsid ) AS vtiger_receivedpayments_notes ',$listQuery);

        $listQuery=str_replace('matchstatus = \'匹配解除中\'','matchstatus =1 ',$listQuery);

        $listQuery=str_replace('vtiger_receivedpayments.matchstatus = \'已匹配\' AND vtiger_receivedpayments.matchstatus IS NOT NULL','vtiger_receivedpayments.matchstatus is  null',$listQuery);

        $public = $_REQUEST['public'];
		if ($public == 'unaudited') {
			$listQuery = $this->getYearsDataCountSql();
		}

        //echo $listQuery.'<br>';die();
        $listQuery=str_replace('WHERE 1=1','LEFT JOIN vtiger_account ON vtiger_account.accountid = (select IF(vtiger_servicecontracts.sc_related_to>0,vtiger_servicecontracts.sc_related_to,vtiger_servicecontracts.old_sc_related_to) from vtiger_servicecontracts where vtiger_servicecontracts.servicecontractsid = vtiger_receivedpayments.relatetoid) LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_account.accountid WHERE 1=1',$listQuery);

        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }


    /**
     * 解绑列表
     */
    public function getListViewCountUnbound(){
        $db = PearDatabase::getInstance();
        $moduleName ='ReceivedPaymentsNotes';
        if(!empty($request)){
            if(isset($request['BugFreeQuery'])){
                $_REQUEST['BugFreeQuery'] = $request['BugFreeQuery'];
            }
            if(isset($request['public'])){
                $_REQUEST['public'] = $request['public'];
            }
        }
        $nowMonth=date('Y-m');
        $searchRange=" and 1=1";
        if(isset($_REQUEST['BugFreeQuery'])){
            $bugFreeQueryArray=json_decode($_REQUEST['BugFreeQuery'],true);
            foreach ($bugFreeQueryArray as $key => $bugFreeQuery){
                if(strpos($bugFreeQuery,'dateequal') !== false){
                    $newKey=str_replace('field','value',$key);
                    $bugFreeQueryArray[$newKey]&&$nowMonth=date('Y-m',strtotime($bugFreeQueryArray[$newKey]));
                    $searchRange=" and left(changetime,7)='".$nowMonth."'";
                }
            }
        }
        $this->getSearchWhere();
        $listWhere = $this->get('query_generator')->getSearchWhere();
        $listQuery=$this->getUnboundSql($nowMonth,$searchRange);
        if($listWhere){
            $listWhere='and '.$listWhere;
        }
        $listQuery .=") tmptable where 1=1 ".$listWhere;
        $viewid = ListViewSession::getCurrentView($moduleName);
        ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,
        $listResult = $db->pquery($listQuery, array());
        return $db->num_rows($listResult);
    }

    /**
     * 解绑数据
     * @param $pagingModel
     * @param $request
     */
    public function getListViewEntriesUnbound($pagingModel,$request){
        $db = PearDatabase::getInstance();
        $moduleName ='ReceivedPaymentsNotes';


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
            $orderBy = 'id';
            $sortOrder = ' DESC';
        }

        $nowMonth=date('Y-m');
        $searchRange=" and 1=1";
        if(isset($_REQUEST['BugFreeQuery'])){
            $bugFreeQueryArray=json_decode($_REQUEST['BugFreeQuery'],true);
            foreach ($bugFreeQueryArray as $key => $bugFreeQuery){
                if(strpos($bugFreeQuery,'dateequal') !== false){
                    $newKey=str_replace('field','value',$key);
                    $bugFreeQueryArray[$newKey]&&$nowMonth=date('Y-m',strtotime($bugFreeQueryArray[$newKey]));
                    $searchRange=" and left(changetime,7)='".$nowMonth."'";
                }
            }
        }



        $this->getSearchWhere();
        $listWhere = $this->get('query_generator')->getSearchWhere();

        if($listWhere){
            $listWhere='and '.$listWhere;
        }
        $listQuery=$this->getUnboundSql($nowMonth,$searchRange);
        $listQuery .=") tmptable where 1=1 ".$listWhere;
        $listQuery .= " order by ".$orderBy.$sortOrder;
        $viewid = ListViewSession::getCurrentView($moduleName);
        ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,
        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();
        $listQuery .= " LIMIT $startIndex,".($pageLimit);
        $listResult = $db->pquery($listQuery, array());
        while($rawData=$db->fetch_array($listResult)) {
            $listViewRecordModels[$rawData['id']] = $rawData;
        }
        return $listViewRecordModels;
    }

    /**
     * 获取解绑记录的sql
     * @param $nowMonth
     * @param $searchRange
     * @return string
     */
    public function getUnboundSql($nowMonth,$searchRange){
        $listQuery="select * from (SELECT
	vtiger_receivedpayments.receivedpaymentsid as id,
	vtiger_receivedpayments.owncompany as owncompany,
	vtiger_receivedpayments.paytitle as paytitle,
	vtiger_receivedpayments.paymentchannel as paymentchannel,
	vtiger_receivedpayments.paymentcode as paymentcode,
	vtiger_receivedpayments.reality_date as reality_date,
	vtiger_receivedpayments.unit_price as unit_price,
	vtiger_servicecontracts.total as total,
	vtiger_staypayment.staypaymentjine as staypaymentjine,
	(IF(vtiger_receivedpayments.relatetoid>0,(select vtiger_receivedpayments_changedetails.contract_no from vtiger_receivedpayments_changedetails where vtiger_receivedpayments_changedetails.receivedpaymentsid=vtiger_receivedpayments.receivedpaymentsid and vtiger_receivedpayments_changedetails.changetype like '%匹配%'  order by vtiger_receivedpayments_changedetails.changedetailid desc LIMIT 1,1),(select vtiger_receivedpayments_changedetails.contract_no from vtiger_receivedpayments_changedetails where vtiger_receivedpayments_changedetails.receivedpaymentsid=vtiger_receivedpayments.receivedpaymentsid and vtiger_receivedpayments_changedetails.changetype like '%匹配%'  order by vtiger_receivedpayments_changedetails.changedetailid desc LIMIT 0,1))) as  last_match_contract_no,
	(IF(vtiger_receivedpayments.relatetoid>0,(select vtiger_receivedpayments_changedetails.changetime from vtiger_receivedpayments_changedetails where vtiger_receivedpayments_changedetails.receivedpaymentsid=vtiger_receivedpayments.receivedpaymentsid and vtiger_receivedpayments_changedetails.changetype like '%匹配%'  order by vtiger_receivedpayments_changedetails.changedetailid desc LIMIT 1,1),(select vtiger_receivedpayments_changedetails.changetime from vtiger_receivedpayments_changedetails where vtiger_receivedpayments_changedetails.receivedpaymentsid=vtiger_receivedpayments.receivedpaymentsid and vtiger_receivedpayments_changedetails.changetype like '%匹配%'  order by vtiger_receivedpayments_changedetails.changedetailid desc LIMIT 0,1))) as last_match_time,
		vtiger_servicecontracts.contract_no as match_contract_no,	
		(select vtiger_receivedpayments_changedetails.changetime from vtiger_receivedpayments_changedetails where vtiger_receivedpayments_changedetails.receivedpaymentsid=vtiger_receivedpayments.receivedpaymentsid and vtiger_receivedpayments.relatetoid>0 and vtiger_receivedpayments_changedetails.changetype like '%匹配%'  order by vtiger_receivedpayments_changedetails.changedetailid desc LIMIT 1) as match_time,
		(select CONCAT(
			last_name,
		IF
			( `status` = 'Active', '', '[离职]' ) 
		) AS last_name  from vtiger_receivedpayments_changedetails LEFT JOIN vtiger_users on  vtiger_receivedpayments_changedetails.changerid=vtiger_users.id where vtiger_receivedpayments_changedetails.receivedpaymentsid=vtiger_receivedpayments.receivedpaymentsid and vtiger_receivedpayments_changedetails.changetype like '%匹配%'  order by vtiger_receivedpayments_changedetails.changedetailid desc LIMIT 1) as matcher,
		(select vtiger_users.id  from vtiger_receivedpayments_changedetails LEFT JOIN vtiger_users on  vtiger_receivedpayments_changedetails.changerid=vtiger_users.id where vtiger_receivedpayments_changedetails.receivedpaymentsid=vtiger_receivedpayments.receivedpaymentsid and vtiger_receivedpayments_changedetails.changetype like '%匹配%'  order by vtiger_receivedpayments_changedetails.changedetailid desc LIMIT 1) as matcherid,
		(select vtiger_receivedpayments_changedetails.current_department from vtiger_receivedpayments_changedetails where vtiger_receivedpayments_changedetails.receivedpaymentsid=vtiger_receivedpayments.receivedpaymentsid and vtiger_receivedpayments_changedetails.changetype like '%匹配%'  order by vtiger_receivedpayments_changedetails.changedetailid desc LIMIT 1) as current_department,
		(select vtiger_receivedpayments_changedetails.changetime from vtiger_receivedpayments_changedetails where vtiger_receivedpayments_changedetails.receivedpaymentsid=vtiger_receivedpayments.receivedpaymentsid and vtiger_receivedpayments_changedetails.changetype like '%解绑%'  order by vtiger_receivedpayments_changedetails.changedetailid desc LIMIT 1) as last_relive_time,
		(select CONCAT(
			last_name,
		IF
			( `status` = 'Active', '', '[离职]' ) 
		) AS last_name  from vtiger_receivedpayments_changedetails LEFT JOIN vtiger_users on  vtiger_receivedpayments_changedetails.changerid=vtiger_users.id where vtiger_receivedpayments_changedetails.receivedpaymentsid=vtiger_receivedpayments.receivedpaymentsid and vtiger_receivedpayments_changedetails.changetype like '%解绑%'  order by vtiger_receivedpayments_changedetails.changedetailid desc LIMIT 1) as last_reliver,
		(select vtiger_users.id  from vtiger_receivedpayments_changedetails LEFT JOIN vtiger_users on  vtiger_receivedpayments_changedetails.changerid=vtiger_users.id where vtiger_receivedpayments_changedetails.receivedpaymentsid=vtiger_receivedpayments.receivedpaymentsid and vtiger_receivedpayments_changedetails.changetype like '%解绑%'  order by vtiger_receivedpayments_changedetails.changedetailid desc LIMIT 1) as last_reliverid,
		(select count(*) from vtiger_receivedpayments_changedetails where vtiger_receivedpayments_changedetails.receivedpaymentsid=vtiger_receivedpayments.receivedpaymentsid and vtiger_receivedpayments_changedetails.changetype like '%解绑%' and left(changetime,7)='".$nowMonth."') as current_month_relive_times,
		(select count(*) from vtiger_receivedpayments_changedetails where vtiger_receivedpayments_changedetails.receivedpaymentsid=vtiger_receivedpayments.receivedpaymentsid and vtiger_receivedpayments_changedetails.changetype like '%解绑%') as relive_times_count,
		(IF((select vtiger_receivedpayments_changedetails.changetype from vtiger_receivedpayments_changedetails where vtiger_receivedpayments_changedetails.receivedpaymentsid=vtiger_receivedpayments.receivedpaymentsid and vtiger_receivedpayments_changedetails.changetype like '%解绑%' order by vtiger_receivedpayments_changedetails.changedetailid desc LIMIT 1 )='跨月解绑','是','否') ) as is_last_overmonth_relive,
		(select count(*) from vtiger_receivedpayments_changedetails where vtiger_receivedpayments_changedetails.receivedpaymentsid=vtiger_receivedpayments.receivedpaymentsid and vtiger_receivedpayments_changedetails.changetype ='跨月解绑') as overmonth_relive_times,
		(IF(vtiger_receivedpayments.relatetoid>0,'已匹配','未匹配')) as match_status,		
		(IF(vtiger_receivedpayments.istimeoutmatch=1,'是','否')) as is_over_time,
		(IF(vtiger_receivedpayments.iscrossmonthmatch=1,'是','否')) as is_over_month,
		(IF(vtiger_receivedpayments.ischeckachievement=1,'是','否')) as ischeckachievement,
		'".$nowMonth."' as relive_month 
FROM
	vtiger_receivedpayments
	LEFT JOIN vtiger_servicecontracts ON vtiger_receivedpayments.relatetoid = vtiger_servicecontracts.servicecontractsid
	LEFT JOIN vtiger_staypayment ON vtiger_receivedpayments.staypaymentid = vtiger_staypayment.staypaymentid
	where vtiger_receivedpayments.receivedpaymentsid in (select  receivedpaymentsid from  vtiger_receivedpayments_changedetails  where changetype in ('解绑','跨月解绑') ".$searchRange.")";
        return $listQuery;
    }

}