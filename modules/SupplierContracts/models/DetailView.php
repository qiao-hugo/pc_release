<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class SupplierContracts_DetailView_Model extends Vtiger_DetailView_Model {


	/**
	 * 详细页面加上受控制的连接
	 * @param <array> $linkParams - parameters which will be used to calicaulate the params
	 * @return <array> - array of link models in the format as below
	 *                   array('linktype'=>list of link models);
	 */
	public function getDetailViewLinks($linkParams) {
		$linkTypes = array('DETAILVIEWBASIC','DETAILVIEW');
		$moduleModel = $this->getModule();
		$recordModel = $this->getRecord();

		$moduleName = $moduleModel->getName();
		$recordId = $recordModel->getId();

		$detailViewLink = array();
       	if(Users_Privileges_Model::isPermitted($moduleName, 'EditView')) {
			$detailViewLinks[] = array(
					'linktype' => 'LISTVIEWBASIC',
					'linklabel' => 'LBL_ADD_RECORD',
					'linkurl' => $moduleModel->getCreateRecordUrl(),
					'linkicon' => ''
			);
		}
		if(Users_Privileges_Model::isPermitted($moduleName, 'EditView', $recordId)) {
			$detailViewLinks[] = array(
					'linktype' => 'DETAILVIEWBASIC',
					'linklabel' => 'LBL_EDIT',
					'linkurl' => $recordModel->getEditViewUrl(),
					'linkicon' => ''
			);
		}

        //审查
        if(Users_Privileges_Model::isPermitted($moduleName, 'EditView', $recordId)&&self::exportGroupri()&&$recordModel->entity->column_fields['modulestatus']=='已发放'&& $flag) {
            $detailViewLinks[] = array(
                'linktype' => 'DETAILVIEWBASIC',
                'linklabel' => 'LBL_CONFIRM',
                'linkurl' =>'',
                'linkicon' => ''
            );
        }
        global $current_user;
        //生成工作流
        if($recordModel->entity->column_fields['modulestatus']=='a_normal' &&
            $recordModel->entity->column_fields['assigned_user_id']==$current_user->id &&
            !empty($recordModel->entity->column_fields['receiptorid']) &&
            $this->getFileStyle($recordId) &&
            $recordModel->get('isvirtualnumber')==0
            ) {
            $detailViewLinks[] = array(
                'linktype' => 'DETAILVIEWBASIC',
                'linklabel' => 'LBL_NOSTDAPPLY',
                'linkurl' =>'',
                'linkicon' => ''
            );
        }
        //领取签名
        if(Users_Privileges_Model::isPermitted($moduleName, 'DuplicatesHandling', $recordId)
            && $recordModel->checksign($recordId)
            && $recordModel->checkWorkflows($recordId,'CREATE_SIGN_ONE') ) {
            $detailViewLinks[] = array(
                'linktype' => 'DETAILVIEWBASIC',
                'linklabel' => 'LBL_SIGN',
                'linkurl' => '',
                'linkicon' => ''
            );
        }
        //归还签名
        if(Users_Privileges_Model::isPermitted($moduleName, 'DuplicatesHandling', $recordId)
            && $recordModel->checksign($recordId,'SupplierContractTwo')
            && $recordModel->checkWorkflows($recordId,'CREATE_SIGN_TWO') ){
            $detailViewLinks[] = array(
                'linktype' => 'DETAILVIEWBASIC',
                'linklabel' => 'LBL_SIGN',
                'linkurl' => '',
                'linkicon' => ''
            );
        }
        //作废申请
        if(($recordModel->entity->column_fields['modulestatus']=='c_complete' || $recordModel->entity->column_fields['modulestatus']=='c_receive') &&
            ($recordModel->entity->column_fields['assigned_user_id']==$current_user->id ||
                /*$recordModel->entity->column_fields['Receiveid']==$current_user->id ||*/
                $recordModel->entity->column_fields['Signid']==$current_user->id
            )&& $this->getContractVoid($recordId)) {
            $detailViewLinks[] = array(
                'linktype' => 'DETAILVIEWBASIC',
                'linklabel' => 'LBL_CONTRACTCANCEL',
                'linkurl' =>'',
                'linkicon' => ''
            );
        }
        /**
         * 虚拟编号生成
         */
        $contract_no=$recordModel->get('contract_no');
        if($recordModel->get('modulestatus')=='a_normal' && empty($contract_no)&& $recordModel->get('isvirtualnumber')==0 &&
            ($recordModel->entity->column_fields['assigned_user_id']==$current_user->id ||
                /*$recordModel->entity->column_fields['Receiveid']==$current_user->id ||*/
                $recordModel->entity->column_fields['Signid']==$current_user->id
            )) {
            $detailViewLinks[] = array(
                'linktype' => 'DETAILVIEWBASIC',
                'linklabel' => 'LBL_VIRTUALVUMBER',
                'linkurl' =>'',
                'linkicon' => ''
            );
        }
        //出纳填写
        if($recordModel->entity->column_fields['modulestatus']=='c_cancelings' && $moduleModel->exportGrouprt('SupplierContracts','concancel')) {
            $detailViewLinks[] = array(
                'linktype' => 'DETAILVIEWBASIC',
                'linklabel' => 'LBL_CONTRACTCANCELING',
                'linkurl' =>'',
                'linkicon' => ''
            );
        }

        /**
         * 指定代领人
         */
        if($recordModel->get('isvirtualnumber')==0 && empty($recordModel->entity->column_fields['receiptorid']) && in_array($recordModel->entity->column_fields['modulestatus'],array('a_normal'))
            && $recordModel->entity->column_fields['assigned_user_id']== $current_user->id
        )
        {
            if($this->getFileStyle($recordId) || $recordModel->get('sideagreement')==1) {
                $detailViewLinks[] = array(
                    'linktype' => 'DETAILVIEWBASIC',
                    'linklabel' => 'LBL_RECEIPTOR',
                    'linkurl' => '',
                    'linkicon' => ''
                );
            }
        }
        //更改代领人

        if(in_array($recordModel->entity->column_fields['modulestatus'],array('c_stamp'))) {
            if($recordModel->checkCreator($recordId) || $current_user->is_admin=='on' ||
                ($recordModel->entity->column_fields['assigned_user_id']== $current_user->id && $recordModel->get('sideagreement')==1) ||
                $recordModel->personalAuthority('SupplierContracts','Received')){
                $detailViewLinks[] = array(
                    'linktype' => 'DETAILVIEWBASIC',
                    'linklabel' => 'LBL_RECEIPTOR_MODIFY',
                    'linkurl' =>'',
                    'linkicon' => ''
                );
            }
        }

		if(!empty($detailViewLinks)){
			foreach ($detailViewLinks as $detailViewLink) {
				$linkModelList['DETAILVIEWBASIC'][] = Vtiger_Link_Model::getInstanceFromValues($detailViewLink);
			}
		}

		//$linkModelListDetails = Vtiger_Link_Model::getAllByType($moduleModel->getId(),$linkTypes,$linkParams);
		//Mark all detail view basic links as detail view links.
		//Since ui will be look ugly if you need many basic links
		//$detailViewBasiclinks = $linkModelListDetails['DETAILVIEWBASIC'];
		//unset($linkModelListDetails['DETAILVIEWBASIC']);
		//删除去掉 gaocl 2015/01/30
// 		if(Users_Privileges_Model::isPermitted($moduleName, 'Delete', $recordId)) {
// 			$deletelinkModel = array(
// 					'linktype' => 'DETAILVIEW',
// 					'linklabel' => sprintf("%s %s", getTranslatedString('LBL_DELETE', $moduleName), vtranslate('SINGLE_'. $moduleName, $moduleName)),
// 					'linkurl' => 'javascript:Vtiger_Detail_Js.deleteRecord("'.$recordModel->getDeleteUrl().'")',
// 					'linkicon' => ''
// 			);
// 			$linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($deletelinkModel);
// 		}

		//复制去掉 gaocl 2015/01/30
// 		if(Users_Privileges_Model::isPermitted($moduleName, 'EditView', $recordId)) {
// 			$duplicateLinkModel = array(
// 						'linktype' => 'DETAILVIEWBASIC',
// 						'linklabel' => 'LBL_DUPLICATE',
// 						'linkurl' => $recordModel->getDuplicateRecordUrl(),
// 						'linkicon' => ''
// 				);
// 			$linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($duplicateLinkModel);
// 		}

	/* 	if(!empty($detailViewBasiclinks)) {
			foreach($detailViewBasiclinks as $linkModel) {
				// Remove view history, needed in vtiger5 to see history but not in vtiger6
				if($linkModel->linklabel == 'View History') {
					continue;
				}
				$linkModelList['DETAILVIEW'][] = $linkModel;
			}
		} */

		$relatedLinks = $this->getDetailViewRelatedLinks();
		foreach($relatedLinks as $relatedLinkEntry) {
			$relatedLink = Vtiger_Link_Model::getInstanceFromValues($relatedLinkEntry);
			$linkModelList[$relatedLink->getType()][] = $relatedLink;
		}

		$linkModelList['DETAILVIEWRELATED']=$moduleModel->makeRelatedurl($recordId);

		$widgets = $this->getWidgets();
		foreach($widgets as $widgetLinkModel) {
			$linkModelList['DETAILVIEWWIDGET'][] = $widgetLinkModel;
		}
		//前台屏蔽设置链接
		/* $currentUserModel = Users_Record_Model::getCurrentUserModel();
		if($currentUserModel->isAdminUser()) {
			$settingsLinks = $moduleModel->getSettingLinks();
			foreach($settingsLinks as $settingsLink) {
				$linkModelList['DETAILVIEWSETTING'][] = Vtiger_Link_Model::getInstanceFromValues($settingsLink);
			}
		} */

		return $linkModelList;
	}
    /**
     * 可导出数据的权限
     * @return bool
     */
     static public function exportGroupri(){
        global $current_user;
        $id=$current_user->id;
        if($current_user->column_fields['roleid']=='H104'){
            return true;
        }
        $db=PearDatabase::getInstance();
        //不必过滤是否在职因为离职的根本就登陆不了系统
        $query="select vtiger_user2department.userid from vtiger_user2department LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid WHERE CONCAT(vtiger_departments.parentdepartment,'::') REGEXP 'H25::'";
        $result=$db->run_query_allrecords($query);
        $userids=array();
        foreach($result as $values){
            $userids[]=$values['userid'];
        }
        $userids[]=1;
        //$userids=array(1,2155,323,1923);//有访问权限的
        if(in_array($id,$userids)){
            return true;
        }
        return false;
    }
    /**
     * 合同是否可以作废
     * @param $recordId
     * @return bool
     */
    public function getContractVoid($recordId)
    {
        return true;
         $query='SELECT 1 FROM vtiger_newinvoice LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_newinvoice.invoiceid WHERE  vtiger_crmentity.deleted=0 AND contractid=?
                UNION ALL 
                SELECT 1 FROM vtiger_receivedpayments WHERE receivedstatus = \'normal\' AND vtiger_receivedpayments.relatetoid=?
                UNION ALL
                SELECT 1 FROM vtiger_salesorder LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_salesorder.salesorderid WHERE vtiger_crmentity.deleted=0 AND vtiger_salesorder.servicecontractsid=?';
        $db=PearDatabase::getInstance();
        $result=$db->pquery($query,array($recordId,$recordId,$recordId));
        $num=$db->num_rows($result);
        if($num)
        {
            return false;
        }
        return true;
    }
    /**
     * 根据合同的ID判断附件类型是否合法
     * @param $recordId
     * @return bool
     */
    public function getFileStyle($recordId)
    {
        $query='SELECT `name` FROM vtiger_files WHERE description=\'SupplierContracts\' AND relationid=? AND delflag=0';
        $db=PearDatabase::getInstance();
        $dataResult=$db->pquery($query,array($recordId));
        if($db->num_rows($dataResult))
        {
            return true;
        }
        return false;
    }

}
