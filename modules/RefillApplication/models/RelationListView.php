<?php
/*
*定义管理语句
*/
class RefillApplication_RelationListView_Model extends Vtiger_RelationListView_Model {
	static $relatedquerylist = array(
	'ReceivedPayments'=>'SELECT vtiger_receivedpayments.paytitle,vtiger_receivedpayments.owncompany,vtiger_receivedpayments.reality_date,vtiger_receivedpayments.unit_price,vtiger_receivedpayments.createtime, vtiger_receivedpayments.receivedpaymentsid as crmid FROM vtiger_receivedpayments WHERE vtiger_receivedpayments.relatetoid = (SELECT servicecontractsid FROM `vtiger_refillapplication` WHERE refillapplicationid=?) ORDER BY vtiger_receivedpayments.createdtime DESC LIMIT 20',
    
    );

	public function getEntries($pagingModel){
		$relatedModuleName=$_REQUEST['relatedModule'];
		$moduleName = $_REQUEST['module'];
		$relatedquerylist=self::$relatedquerylist;
		
		if(isset($relatedquerylist[$relatedModuleName])){
			$parentId = $_REQUEST['record'];
			$this->relationquery=str_replace('?',$parentId,$relatedquerylist[$relatedModuleName]);
		}
		
		return $this->getEntries_implement($pagingModel);
	}
	
	public function getEntries_implement($pagingModel) {
		$db = PearDatabase::getInstance();
		$parentModule = $this->getParentRecordModel()->getModule();
		$relationModule = $this->getRelationModel()->getRelationModuleModel();
		$relatedColumnFields = $relationModule->getConfigureRelatedListFields();
		if(count($relatedColumnFields) <= 0){
			$relatedColumnFields = $relationModule->getRelatedListFields();
		}
		$query = $this->getRelationQuery();
		/*if ($this->get('whereCondition')) {
		 $query = $this->updateQueryWithWhereCondition($query);
			}*/
		//$startIndex = $pagingModel->getStartIndex();
		//$pageLimit = $pagingModel->getPageLimit();
		//$orderBy = $this->getForSql('orderby');
		//$sortOrder = $this->getForSql('sortorder');
		/*if($orderBy) {
		 $orderByFieldModuleModel = $relationModule->getFieldByColumn($orderBy);
		 if($orderByFieldModuleModel && $orderByFieldModuleModel->isReferenceField()) {
		 //If reference field then we need to perform a join with crmentity with the related to field
		 $queryComponents = $split = spliti(' where ', $query);
		 $selectAndFromClause = $queryComponents[0];
		 $whereCondition = $queryComponents[1];
		 $qualifiedOrderBy = 'vtiger_crmentity'.$orderByFieldModuleModel->get('column');
		 $selectAndFromClause .= ' LEFT JOIN vtiger_crmentity AS '.$qualifiedOrderBy.' ON '.
		 $orderByFieldModuleModel->get('table').'.'.$orderByFieldModuleModel->get('column').' = '.
		 $qualifiedOrderBy.'.crmid ';
		 $query = $selectAndFromClause.' WHERE '.$whereCondition;
		 $query .= ' ORDER BY '.$qualifiedOrderBy.'.label '.$sortOrder;
		 /*  }
		 elseif($orderByFieldModuleModel && $orderByFieldModuleModel->isOwnerField()) {
		 $query .= ' ORDER BY CONCAT(vtiger_users.first_name, " ", vtiger_users.last_name) '.$sortOrder; */
		/*} else{
		 // Qualify the the column name with table to remove ambugity
		 $qualifiedOrderBy = $orderBy;
		 $orderByField = $relationModule->getFieldByColumn($orderBy);
		 if ($orderByField) {
		 $qualifiedOrderBy = $relationModule->getOrderBySql($qualifiedOrderBy);
		 }
		 $query = "$query ORDER BY $qualifiedOrderBy $sortOrder";
		 }
		 }*/
		//$limitQuery = $query .' LIMIT '.$startIndex.','.$pageLimit;
		//取消分页
		$limitQuery = $query;
		
		
		$result = $db->pquery($limitQuery, array());
		$relatedRecordList = array();
		//客户详情联系人关联加入首要联系人
		if($relationModule->get('name')=='Contacts' && $_REQUEST['view']=='Detail'){
			$info=$db->pquery('select * from vtiger_account where accountid=? limit 1',array($_REQUEST['record']));
			$data=$db->query_result_rowdata($info);
			$add=array('account_id' => $data['accountid'],'name' => $data['linkname'],'gendertype' => $data['gender'],'phone' => $data['mobile'],'title' => $data['title'],'makedecisiontype' => $data['makedecision'],'email' =>$data['email1'],'assigned_user_id'=>$data['smownerid']);
			$record = Vtiger_Record_Model::getCleanInstance('Contacts');
			$record->setData($add)->setModuleFromInstance($relationModule);
			$record->setId($data['accountid']);
			$relatedRecordList[0] = $record;
		}
		
		
		
	
		for($i=0; $i< $db->num_rows($result); $i++ ) {
			$row = $db->fetch_row($result,$i);
			//$row['down_id'] = base64_encode($row['attachmentsid']);
	
			$record = Vtiger_Record_Model::getCleanInstance($relationModule->get('name'));
	
			$record->setData($row)->setModuleFromInstance($relationModule);
			if (empty($row['crmid'])) {
	
				if ($_REQUEST['relatedModule'] == 'Files') {
					$record->setId($row['attachmentsid']);
					$row['crmid'] = $row['attachmentsid'];
				}
			}
	
			$record->setId($row['crmid']);
				
			$relatedRecordList[$row['crmid']] = $record;
		}
	
		/* 	$pagingModel->calculatePageRange($relatedRecordList);
			$nextLimitQuery = $query. ' LIMIT '.($startIndex+$pageLimit).' , 1';
			$nextPageLimitResult = $db->pquery($nextLimitQuery, array());
			if($db->num_rows($nextPageLimitResult) > 0){$pagingModel->set('nextPageExists', true);}else{$pagingModel->set('nextPageExists', false);} */
		return $relatedRecordList;
	}
}