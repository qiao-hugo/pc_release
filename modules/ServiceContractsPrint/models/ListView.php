<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ServiceContractsPrint_ListView_Model extends Vtiger_ListView_Model {


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

	
	
	//????????????????????????   #??????crm??????$request??????---2015-12-16 ?????????
	public function getListViewEntries($pagingModel,$request=array()) {
		$db = PearDatabase::getInstance();
		$moduleName ='ServiceContractsPrint';

		

		$orderBy = $this->getForSql('orderby');
		$sortOrder = $this->getForSql('sortorder');

		//List view will be displayed on recently created/modified records
		//????????????????????????????????????????????????  ---???????????????
		if(empty($orderBy) && empty($sortOrder)){

			$orderBy = 'servicecontractsprintid';
			$sortOrder = 'DESC';
		}
		$this->getSearchWhere();
        $listQuery = $this->getQuery();
        $listQuery.=$this->getUserWhere();
		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();

        $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;
		
		$viewid = ListViewSession::getCurrentView($moduleName);
	
		ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session??????????????????,
	
		$listQuery .= " LIMIT $startIndex,".($pageLimit);

        //echo $listQuery;//die();

		$listResult = $db->pquery($listQuery, array());


		$index = 0;
		while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['servicecontractsprintid'];
            $rawData['cstatus'] = ($rawData['constractsstatus']=='c_print')?1:0;
			$listViewRecordModels[$rawData['servicecontractsprintid']] = $rawData;
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
            foreach($list as $fields){
                $temp[$fields['fieldlabel']]=$fields;
            }
           return $temp;
        }
        return $queryGenerator->getFocus()->list_fields_name;
        
    }
    public function getUserWhere(){
        return '';
        global $current_user;
        $searchDepartment = $_REQUEST['department'];
        $sourceModule = $this->get('src_module');
        $listQuery=' ';
        if(!empty($searchDepartment)&&$searchDepartment!='H1'){  //20150525 ????????? ??????
            $userid=getDepartmentUser($searchDepartment);
            $where=getAccessibleUsers('SContractNoGeneration','List',true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $where=!empty($where)?$where:array(-1);
            //$listQuery .= ' and vtiger_scontractnogeneration.smownerid in ('.implode(',',$where).')';
        }else{
            $where=getAccessibleUsers();
            if($where!='1=1'){
                //$listQuery .= ' and vtiger_scontractnogeneration.smownerid '.$where;
            }
        }
        return $listQuery;

    }
    public function getListViewCount() {
        $db = PearDatabase::getInstance();
        $queryGenerator = $this->get('query_generator');
//print_r(debug_backtrace(0));
        //????????????
        //$this->getSearchWhere();
        //????????????
        $where=$this->getUserWhere();
        //$where.= ' AND accountname is NOT NULL';
        $queryGenerator->addUserWhere($where);
        $listQuery =  $queryGenerator->getQueryCount();

        //echo $listQuery.'<br>';die();
        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }
    public function getListViewLinks($linkParams) {
        $basicLinks = array();
        $links=array();
        $moduleModel=Vtiger_Module_Model::getInstance("ServiceContracts");
        if($moduleModel->exportGrouprt("ServiceContracts","addcontractno")){
            $basicLinks[] = array(
                'linktype' => 'LISTVIEWBASIC',
                'linklabel' => 'LBL_ADD_RECORD',
                'linkurl' => $moduleModel->getCreateRecordUrl(),
                'linkicon' => ''
            );
            foreach($basicLinks as $basicLink) {
                $links['LISTVIEWBASIC'][] = Vtiger_Link_Model::getInstanceFromValues($basicLink);
            }
        }

        return $links;

    }
}