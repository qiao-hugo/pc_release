<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Leads_ListView_Model extends Vtiger_ListView_Model {

	/**
	 * Function to get the list of Mass actions for the module
	 * @param <Array> $linkParams
	 * @return <Array> - Associative array of Link type to List of  Vtiger_Link_Model instances for Mass Actions
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

		return $massActionLinks;
	}

	/**
	 * Function to get the list of listview links for the module
	 * @param <Array> $linkParams
	 * @return <Array> - Associate array of Link Type to List of Vtiger_Link_Model instances
	 */
	function getListViewLinks($linkParams) {
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$links = parent::getListViewLinks($linkParams);

		$index=0;
        $moduleModel = Vtiger_Module_Model::getInstance('Leads');//module相关的数据
//        foreach($links['LISTVIEWBASIC'] as $link) {
//			if($link->linklabel == 'Send SMS'||(!Users_Privileges_Model::isPermitted("Leads", 'ListBtnADD')&& $link->linklabel =='LBL_ADD_RECORD')) {
//				unset($links['LISTVIEWBASIC'][$index]);
//			}
//            if(!$moduleModel->exportGrouprt('Leads','AddRecord') && $link->linklabel =='LBL_ADD_RECORD'){   //权限验证
//                unset($links['LISTVIEWBASIC'][$index]);
//            }
//			$index++;
//		}
		return $links;
	}
    public function getListViewEntries($pagingModel) {
        $db = PearDatabase::getInstance();

        $moduleName = 'Leads';
        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');



        if(empty($orderBy) && empty($sortOrder) && $moduleName != "Users"){
            $orderBy = 'vtiger_leaddetails.allocatetime';
            $sortOrder = 'DESC';
        }
        $this->getSearchWhere();
        $listQuery = $this->getQuery();
        //echo $listQuery;
        $listQuery.=$this->getUserWhere();
        //echo $listQuery;die;


        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();

        $listQuery .= " ORDER BY field(vtiger_leaddetails.assignerstatus,'a_not_allocated','c_allocated','c_transformation','c_complete','c_Related','c_Forced_Related','c_cancelled'),". $orderBy . ' ' .$sortOrder . ', mapcreattime DESC ';

        $viewid = ListViewSession::getCurrentView($moduleName);

        ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,

        $listQuery .= " LIMIT $startIndex,".($pageLimit);
        $listQuery =str_replace('vtiger_leaddetails.locationcity','concat(concat(vtiger_leaddetails.locationprovince,"-"),vtiger_leaddetails.locationcity) as locationcity',$listQuery);
//        echo $listQuery;die;
        $listResult = $db->pquery($listQuery, array());
        $listViewRecordModels = array();

        $recordModel = Leads_Record_Model::getCleanInstance("Leads");
        $departments = $recordModel->getDepartmentsByDepth();

        //3.在进行一次转化，目的何在
        $index = 0;
        while($rawData=$db->fetch_array($listResult)) {
            $rawData['qq']=$rawData['qq']?$rawData['qq']:'';
            $rawData['leadbelongsystem']=$departments[$rawData['leadbelongsystem']];
            $rawData['id'] = $rawData['leadid'];
            $listViewRecordModels[$rawData['leadid']] = $rawData;

            // 获取最近跟进的信息
            $sql = "SELECT commentcontent FROM vtiger_modcomments WHERE related_to=? ORDER BY modcommentsid DESC";
            $sel_result = $db->pquery($sql, array($rawData['leadid']));
            $res_cnt = $db->num_rows($sel_result);
            if($res_cnt > 0) {
                $row = $db->query_result_rowdata($sel_result, 0);
                $listViewRecordModels[$rawData['leadid']]['modcomments'] = $row['commentcontent'];
            }
        }

        return $listViewRecordModels;
    }
    public function getUserWhere(){
	$listQuery = '';

        if($_REQUEST['filter']=='overt'){
            $listQuery .=' and vtiger_leaddetails.leadcategroy=2';
        }else{
        global $current_user;
        $searchDepartment = $_REQUEST['department'];//部门搜索的部门


        if(empty($searchDepartment)){
            $searchDepartment = 'H1';
        }
        $where=getAccessibleUsers('Leads','List',true);
        $userid=getDepartmentUser($searchDepartment);
        $listQuery = '';
        if(!empty($searchDepartment)){
            if(!empty($where)&&$where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $where=!empty($where)?$where:array(-1);
            $listQuery .= ' and (vtiger_crmentity.smownerid in ('.implode(',',$where).') or vtiger_leaddetails.assigner='.$current_user->id.')';
        }else{
            $where=getAccessibleUsers();
            if($where!='1=1'){
                $listQuery .= ' and (vtiger_crmentity.smownerid '.$where.' or vtiger_leaddetails.assigner='.$current_user->id.')';
            }
        }
        switch ($_REQUEST['filter']) {
            case "threeMonth":
                $listQuery .= " and vtiger_leaddetails.mapcreattime >=DATE_SUB( CURDATE(), INTERVAL 3 MONTH ) and vtiger_leaddetails.assignerstatus != 'c_cancelled' and  vtiger_leaddetails.assignerstatus != 'c_complete' and vtiger_leaddetails.commenttime <= DATE_SUB( CURDATE(), INTERVAL 7 DAY ) ";
                break;
            case "moreMonth":
                $listQuery .= " and vtiger_leaddetails.mapcreattime >=DATE_SUB( CURDATE(), INTERVAL 3 MONTH ) and vtiger_leaddetails.assignerstatus != 'c_cancelled' and  vtiger_leaddetails.assignerstatus != 'c_complete' and vtiger_leaddetails.commenttime <= DATE_SUB( CURDATE(), INTERVAL 1 MONTH ) ";
                break;
	}
            $listQuery .= ' and vtiger_leaddetails.leadcategroy=0';
        }
        return $listQuery;
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
    public function getListViewCount() {
        $db = PearDatabase::getInstance();
        $queryGenerator = $this->get('query_generator');
        //print_r(debug_backtrace(0));
        //搜索条件
        $this->getSearchWhere();
        //用户条件
        $where=$this->getUserWhere();
        //$where.= ' AND accountname is NOT NULL';
        $queryGenerator->addUserWhere($where);
        $listQuery =  $queryGenerator->getQueryCount();


        //$listQuery .= $this->getSearchWhere();
        //echo $listQuery.'<br>';die();
        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }
}
