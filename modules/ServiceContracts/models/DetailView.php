<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ServiceContracts_DetailView_Model extends Vtiger_DetailView_Model {


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
        if(0==$recordModel->entity->column_fields['isconfirm']){
            $flag=true;
        }else{
            $newtemp=$recordModel->entity->column_fields['confirmvalue'];
            $temp=explode("##",$newtemp);
            $tempn=explode(',',$temp[0]);
            $flag=true;
            if(substr($tempn[1],0,10)==date('Y-m-d')){
                $flag=false;
            }
        }
        //审查
        if(Users_Privileges_Model::isPermitted($moduleName, 'EditView', $recordId) && $moduleModel->exportGrouprt('ServiceContracts','Received') &&$recordModel->entity->column_fields['modulestatus']=='已发放'&& $flag) {
            $detailViewLinks[] = array(
                'linktype' => 'DETAILVIEWBASIC',
                'linklabel' => 'LBL_CONFIRM',
                'linkurl' =>'',
                'linkicon' => ''
            );
        }
        //更改提单人
        global $current_user,$configcontracttypeNameTYUN;
        if($recordModel->entity->column_fields['modulestatus']=='c_complete'/* && $recordModel->get('signaturetype')!='eleccontract'*/) {

//            $user=Users_Privileges_Model::getInstanceById($recordModel->entity->column_fields['Receiveid']);
//            $currentUserModel = Users_Record_Model::getCurrentUserModel();
//            $accessibleUsers = $currentUserModel->getAccessibleUsers();

//            if($current_user->is_admin=='on' || array_key_exists($recordModel->entity->column_fields['Receiveid'], $accessibleUsers)){
//            if($user->reports_to_id == $current_user->id || $current_user->is_admin=='on' || array_key_exists($current_user->id, $accessibleUsers)){
                $detailViewLinks[] = array(
                    'linktype' => 'DETAILVIEWBASIC',
                    'linklabel' => 'LBL_UPDATERECEIVED',
                    'linkurl' =>'',
                    'linkicon' => ''
                );
//            }

        }
        //作废申请
//        if(($recordModel->entity->column_fields['modulestatus']=='c_complete' || $recordModel->entity->column_fields['modulestatus']=='已发放') &&
//            ($recordModel->entity->column_fields['assigned_user_id']==$current_user->id ||
//            $recordModel->entity->column_fields['Receiveid']==$current_user->id ||
//            $recordModel->entity->column_fields['Signid']==$current_user->id
//            )&& $this->getContractVoid($recordId)) {
        if($recordModel->get('signaturetype')!='eleccontract') {
            $detailViewLinks[] = array(
                'linktype' => 'DETAILVIEWBASIC',
                'linklabel' => 'LBL_CONTRACTCANCEL',
                'linkurl' => '',
                'linkicon' => ''
            );
        }
//        }
        //出纳填写
        if($recordModel->entity->column_fields['modulestatus']=='c_cancelings' && $moduleModel->exportGrouprt('ServiceContracts','concancel')) {
            $detailViewLinks[] = array(
                'linktype' => 'DETAILVIEWBASIC',
                'linklabel' => 'LBL_CONTRACTCANCELING',
                'linkurl' =>'',
                'linkicon' => ''
            );
        }
        //特殊合同
        if($moduleModel->exportGrouprt('ServiceContracts','SPECIALCONTRACT')) {
            global $adb;
            $query='SELECT 1 FROM vtiger_specialcontract WHERE specialcontractid=?';
            $temp=$adb->pquery($query,array($recordId));
            if($adb->num_rows($temp)){
                $detailViewLinks[] = array(
                    'linktype' => 'DETAILVIEWBASIC',
                    'linklabel' => 'LBL_SPECIALCONTRACT1',
                    'linkurl' =>'',
                    'linkicon' => ''
                );
            }else{
                $detailViewLinks[] = array(
                    'linktype' => 'DETAILVIEWBASIC',
                    'linklabel' => 'LBL_SPECIALCONTRACT',
                    'linkurl' =>'',
                    'linkicon' => ''
                );
            }
        }
        //非标合同申请,合同状态是正常，第二当前登陆人是当前合同的领取人
        //第二当前合同有附件，且，附件为word类型
        if(!empty($recordModel->entity->column_fields['receiptorid']) && $recordModel->entity->column_fields['modulestatus']=='a_normal'
            && $recordModel->entity->column_fields['assigned_user_id']== $current_user->id
        )
        {
            if($this->getFileStyle($recordId)) {
                $detailViewLinks[] = array(
                    'linktype' => 'DETAILVIEWBASIC',
                    'linklabel' => 'LBL_NOSTDAPPLY',
                    'linkurl' => '',
                    'linkicon' => ''
                );
            }
        }
        /**
         * 数字威客一键签收
         */
        if($recordModel->entity->column_fields['signaturetype']!='eleccontract'
            && $recordModel->entity->column_fields['modulestatus']=='a_normal'
            //&& $recordModel->entity->column_fields['parent_contracttypeid']==11
            && $recordModel->entity->column_fields['assigned_user_id']== $current_user->id
        )
        {
             global $adb;
            if($adb->num_rows($adb->pquery('SELECT 1 FROM vtiger_servicecontracts WHERE servicecontractsid=? AND parent_contracttypeid=11',array($recordId))) ||
                $adb->num_rows($adb->pquery("SELECT 1 FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_servicecontracts.servicecontractsid=vtiger_crmentity.crmid LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_crmentity.smownerid
                                            LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_user2department.departmentid
                                            WHERE vtiger_crmentity.deleted=0 AND vtiger_departments.parentdepartment LIKE 'H1::H46::H435%' AND vtiger_servicecontracts.servicecontractsid=? AND parent_contracttypeid=2",array($recordId)))

            ){
                $detailViewLinks[] = array(
                    'linktype' => 'DETAILVIEWBASIC',
                    'linklabel' => 'LBL_WKSIGN',
                    'linkurl' => '',
                    'linkicon' => ''
                );
            }
        }

        /**
         * 确认到款
         */

        $user=Users_Privileges_Model::getInstanceById($recordModel->entity->column_fields['Receiveid']);
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $accessibleUsers = $currentUserModel->getAccessibleUsers();
        if(in_array($recordModel->entity->column_fields['contract_type'],$configcontracttypeNameTYUN) && $recordModel->entity->column_fields['signaturetype'] == 'eleccontract' &&
            in_array($recordModel->entity->column_fields['modulestatus'],array('已发放','c_complete')) && !$recordModel->entity->column_fields['ispay']) {
            if($current_user->is_admin=='on' || array_key_exists($recordModel->entity->column_fields['Receiveid'], $accessibleUsers)) {
                if ($user->reports_to_id == $current_user->id || $current_user->is_admin == 'on' || array_key_exists($current_user->id, $accessibleUsers)) {
                    global $adb,$limitDate;
                    $result = $adb->pquery('Select createdtime from vtiger_activationcode where contractid=? limit 1',array($recordId));
                    $row =$adb->fetchByAssoc($result,0);
                    if(strtotime($row['createdtime'])<strtotime($limitDate)){
                        $detailViewLinks[] = array(
                            'linktype' => 'DETAILVIEWBASIC',
                            'linklabel' => 'LBL_CONFIRMPAYMENT',
                            'linkurl' => '',
                            'linkicon' => ''
                        );
                    }
                }
            }
        }


//        if(($current_user->is_admin=='on' || array_key_exists($recordModel->entity->column_fields['Receiveid'], $accessibleUsers) ) &&!$recordModel->entity->column_fields['ispay']) {
//            if ($user->reports_to_id == $current_user->id || $current_user->is_admin == 'on' || array_key_exists($current_user->id, $accessibleUsers)) {
//                $detailViewLinks[] = array(
//                    'linktype' => 'DETAILVIEWBASIC',
//                    'linklabel' => 'LBL_MANUALCONFIRMPAYMENT',
//                    'linkurl' => '',
//                    'linkicon' => ''
//                );
//            }
//        }

//        if($recordModel->entity->column_fields['isstage'] && ($current_user->is_admin=='on' || array_key_exists($recordModel->entity->column_fields['Receiveid'], $accessibleUsers) ) ) {
//                $detailViewLinks[] = array(
//                    'linktype' => 'DETAILVIEWBASIC',
//                    'linklabel' => 'LBL_LEASTPAYMOENY',
//                    'linkurl' => '',
//                    'linkicon' => ''
//                );
//        }


        $tyunWebBuyServiceRecordModel = TyunWebBuyService_Record_Model::getCleanInstance("TyunWebBuyService");
        if($tyunWebBuyServiceRecordModel->isExistActiveOrder($recordId) && ($current_user->is_admin=='on' || array_key_exists($recordModel->entity->column_fields['Receiveid'], $accessibleUsers)) &&
            $recordModel->entity->column_fields['isstage']!=1) {
            $detailViewLinks[] = array(
                'linktype' => 'DETAILVIEWBASIC',
                'linklabel' => 'LBL_CHANGESTAGE',
                'linkurl' => '',
                'linkicon' => ''
            );
        }

        /**
         * 指定代领人
         */
        if(empty($recordModel->entity->column_fields['receiptorid']) && $recordModel->entity->column_fields['modulestatus']=='a_normal'
            && $recordModel->entity->column_fields['assigned_user_id']== $current_user->id
        )
        {
            if($this->getFileStyle($recordId)) {
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
            if($recordModel->checkCreator($recordId) || $current_user->is_admin=='on'){
                $detailViewLinks[] = array(
                    'linktype' => 'DETAILVIEWBASIC',
                    'linklabel' => 'LBL_RECEIPTOR',
                    'linkurl' => '',
                    'linkicon' => ''
                );
            }
        }
        /**
         * 激活码作废
         */
        if(in_array($recordModel->entity->column_fields['modulestatus'],array('已发放','c_recovered','c_complete'))
            && $recordModel->get('signaturetype')!='eleccontract' && $recordModel->isOrderCancel($recordId)
        ) {
            if($recordModel->entity->column_fields['assigned_user_id']== $current_user->id || $current_user->is_admin=='on'){
                $detailViewLinks[] = array(
                    'linktype' => 'DETAILVIEWBASIC',
                    'linklabel' => 'LBL_TOVOIDACTIVATIONCODE',
                    'linkurl' =>'',
                    'linkicon' => ''
                );
            }
        }
        /**
         * 合同状态打回
         */
        //注意：“签收不成功”操作不需要判断 合同的工单、充值申请单有没有作废之类的，需要屏蔽这行判断的代码
       if(in_array($recordModel->entity->column_fields['modulestatus'],array('c_recovered'))
           //&& $this->getContractVoid($recordId) 
           && $recordModel->get('signaturetype')!='eleccontract'
           && $moduleModel->exportGrouprt('ServiceContracts','Received')
           && $this->isExecute($recordId)
       ) {
           $detailViewLinks[] = array(
               'linktype' => 'DETAILVIEWBASIC',
               'linklabel' => 'LBL_TOBACKSTATUS',
               'linkurl' =>'',
               'linkicon' => ''
           );
       }
        /**
         * 确认产品交付
         */
        if($recordModel->entity->column_fields['modulestatus']=='c_complete'
            && $recordModel->get('isfulldelivery')==0
        ) {
            $detailViewLinks[] = array(
                'linktype' => 'DETAILVIEWBASIC',
                'linklabel' => 'LBL_CONFIRMDELIVERY',
                'linkurl' =>'',
                'linkicon' => ''
            );
        }
        /**
         * 撤销产品交付
         */
        if($recordModel->entity->column_fields['modulestatus']=='c_complete'
            && $recordModel->get('isfulldelivery')==1
        ) {
            $detailViewLinks[] = array(
                'linktype' => 'DETAILVIEWBASIC',
                'linklabel' => 'LBL_CONFIRMDELIVERYBACK',
                'linkurl' =>'',
                'linkicon' => ''
            );
        }
        /**
         * 捎销发送
         */
        if($recordModel->get('modulestatus')=='已发放'
            && $recordModel->get('signaturetype')=='eleccontract'
            && $recordModel->get('eleccontractstatus')=='b_elec_actioning'
            && $recordModel->checkUserPermission($recordModel->get('Receiveid'))
        ) {
            $detailViewLinks[] = array(
                'linktype' => 'DETAILVIEWBASIC',
                'linklabel' => 'LBL_REVOKESENDING',
                'linkurl' =>'',
                'linkicon' => ''
            );
        }
        /**
         * 电子合同发送失败重新发送
         */
        if($recordModel->get('modulestatus')=='已发放'
            && $recordModel->get('signaturetype')=='eleccontract'
            && $recordModel->get('eleccontractstatus')=='a_elec_actioning_fail'
            && $recordModel->checkUserPermission($recordModel->get('Receiveid'))
        ) {
            $detailViewLinks[] = array(
                'linktype' => 'DETAILVIEWBASIC',
                'linklabel' => 'LBL_RESARTSENDING',
                'linkurl' =>'',
                'linkicon' => ''
            );
        }
        /**
         * 电子合同申请作废
         */
        if($recordModel->get('modulestatus')=='已发放'
            && $recordModel->get('signaturetype')=='eleccontract'
            && $recordModel->get('eleccontractstatus')=='b_elec_actioning'
            && $recordModel->checkUserPermission($recordModel->get('Receiveid'))
        ) {
            $detailViewLinks[] = array(
                'linktype' => 'DETAILVIEWBASIC',
                'linklabel' => 'LBL_ELECDOCANCEL',
                'linkurl' =>'',
                'linkicon' => ''
            );
        }
        if($current_user->is_admin == 'on' || $this->isContractManager($current_user->id)) {
            $detailViewLinks[] = array(
                'linktype' => 'DETAILVIEWBASIC',
                'linklabel' => 'LBL_SEND_MESSAGE',
                'linkurl' => "",
                'linkicon' => ''
            );
        }
        //核对，核对编辑按钮
        if($moduleModel->exportGrouprt('ServiceContracts','COLLATE')) {
            $detailViewLinks[] = array(
                'linktype' => 'DETAILVIEWBASIC',
                'linklabel' => 'LBL_COLLATE',
                'linkurl' => '',
                'linkicon' => ''
            );
            $detailViewLinks[] = array(
                'linktype' => 'DETAILVIEWBASIC',
                'linklabel' => 'LBL_COLLATE_EDIT',
                'linkurl' => $recordModel->getEditViewUrl().'&collate=1',
                'linkicon' => ''
            );
        }

        /**
         * 领用人变更申请
         */
        if($recordModel->get('modulestatus')=='已发放'
            && $recordModel->get('signaturetype')!='eleccontract' && $recordModel->get('contractattribute')!='customized'
            && !$recordModel->hasOrder($recordId)
            && ($recordModel->get('assigned_user_id')==$current_user->id || $current_user->is_admin=='on')
        ) {
            $detailViewLinks[] = array(
                'linktype' => 'DETAILVIEWBASIC',
                'linklabel' => 'LBL_CHANGSMOWNER',
                'linkurl' =>'',
                'linkicon' => ''
            );
        }
        /*$detailViewLinks[] = array(
            'linktype' => 'DETAILVIEWBASIC',
            'linklabel' => 'LBL_SALESORDER',
            'linkurl' => '',
            'linkicon' => ''
        );*/
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
         $query='SELECT 1 FROM vtiger_receivedpayments WHERE receivedstatus = \'normal\' AND vtiger_receivedpayments.relatetoid=?
                UNION ALL
                SELECT 1 FROM vtiger_salesorder LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_salesorder.salesorderid WHERE vtiger_crmentity.deleted=0 AND vtiger_salesorder.modulestatus!=\'c_cancel\' AND vtiger_salesorder.servicecontractsid=?
                UNION ALL 
                SELECT 1 FROM vtiger_refillapplication LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_refillapplication.refillapplicationid WHERE vtiger_crmentity.deleted=0 AND vtiger_refillapplication.modulestatus<>\'c_cancel\' AND vtiger_refillapplication.rechargesource != \'contractChanges\' and vtiger_refillapplication.servicecontractsid=?
                UNION ALL
                SELECT 1 FROM vtiger_newinvoice left join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_newinvoice.invoiceid  WHERE vtiger_newinvoice.modulestatus != \'c_cancel\' AND vtiger_newinvoice.contractid=?
                ';

         $db=PearDatabase::getInstance();
        $result=$db->pquery($query,array($recordId,$recordId,$recordId,$recordId));
        $num=$db->num_rows($result);
        if($num)
        {
            return false;
        }
        return true;
    }

    /**
     * 根据订单状态来判断合同是否可以作废
     * @param $recordId
     * @return bool
     */
    public function getContractVoidToActivationcode($recordId)
    {
        //合同作废判断添加增加订单状态判断（如果有订单且订单作废时，才能合同作废）
        $codeNum = 0;
        $sql = 'SELECT  status  FROM  vtiger_activationcode  WHERE  contractid=?';
        $db=PearDatabase::getInstance();
        $codeResult = $db->pquery($sql,array($recordId));
        //如果合同没有订单时则忽略；合同有订单时，判断合同的所有订单是否都已作废
        if($db->num_rows($codeResult)){
            while ($row = $db->fetchByAssoc($codeResult)) {
                if($row['status'] != 2){
                    $codeNum = 1;
                    break;
                }
            }
        }
        if($codeNum){
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
        $query='SELECT `name` FROM vtiger_files WHERE description=\'ServiceContracts\' AND relationid=? AND style=\'files_style6\' AND delflag=0';
        $db=PearDatabase::getInstance();
        $dataResult=$db->pquery($query,array($recordId));
        if($db->num_rows($dataResult))
        {
            return true;
            /*while($row=$db->fetch_array($dataResult))
            {
                if(preg_match('/\.doc$|\.docx$/',$row['name']))
                {
                    return true;
                }
            }*/
        }
        return false;
    }
    /**
     * 合同是否执行
     * @param $record
     * @return bool
     */
    public function isExecute($record){
        global $adb;
        $selectsql="SELECT
                            1
                        FROM
                            vtiger_contracts_execution
                        LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_contracts_execution.contractexecutionid
                        LEFT JOIN vtiger_contracts_execution_detail ON vtiger_contracts_execution.contractexecutionid = vtiger_contracts_execution_detail.contractexecutionid
                        WHERE
                            vtiger_crmentity.deleted = 0
                        AND vtiger_contracts_execution_detail.executestatus='c_executed' AND vtiger_contracts_execution.contractid=? AND vtiger_contracts_execution_detail.iscancel=0";
        $issalesorder = $adb->pquery($selectsql,array($record));
        if($adb->num_rows($issalesorder)){
            return false;
        }
        return true;
    }

    public function isContractManager($userid){
        $db = PearDatabase::getInstance();
        $sql = "select 1 from vtiger_invoicecompanyuser where userid=?";
        $result = $db->pquery($sql,array($userid));
        if($db->num_rows($result)){
            return true;
        }
        return false;
    }
}
