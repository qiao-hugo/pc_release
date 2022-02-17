<?php
class Vtiger_RelationListView_Model extends Vtiger_Base_Model {

	protected $relationModel = false;
	protected $parentRecordModel = false;

	public function setRelationModel($relation){
		$this->relationModel = $relation;
		return $this;
	}

	public function getRelationModel() {
		return $this->relationModel;
	}

	public function setParentRecordModel($parentRecord){
		$this->parentRecordModel = $parentRecord;
		return $this;
	}

	public function getParentRecordModel(){
		return $this->parentRecordModel;
	}

	public function getCreateViewUrl(){
		$relationModel = $this->getRelationModel();
		$relatedModel = $relationModel->getRelationModuleModel();
		$parentRecordModule = $this->getParentRecordModel();
		$parentModule = $parentRecordModule->getModule();

		$createViewUrl = $relatedModel->getCreateRecordUrl().'&sourceModule='.$parentModule->get('name').
								'&sourceRecord='.$parentRecordModule->getId().'&relationOperation=true';

		//To keep the reference fieldname and record value in the url if it is direct relation
		if($relationModel->isDirectRelation()) {
			$relationField = $relationModel->getRelationField();
			$createViewUrl .='&'.$relationField->getName().'='.$parentRecordModule->getId();
		}
		return $createViewUrl;
	}

	public function getCreateEventRecordUrl(){
		$relationModel = $this->getRelationModel();
		$relatedModel = $relationModel->getRelationModuleModel();
		$parentRecordModule = $this->getParentRecordModel();
		$parentModule = $parentRecordModule->getModule();

		$createViewUrl = $relatedModel->getCreateEventRecordUrl().'&sourceModule='.$parentModule->get('name').
								'&sourceRecord='.$parentRecordModule->getId().'&relationOperation=true';

		//To keep the reference fieldname and record value in the url if it is direct relation
		if($relationModel->isDirectRelation()) {
			$relationField = $relationModel->getRelationField();
			$createViewUrl .='&'.$relationField->getName().'='.$parentRecordModule->getId();
		}
		return $createViewUrl;
	}

	public function getCreateTaskRecordUrl(){
		$relationModel = $this->getRelationModel();
		$relatedModel = $relationModel->getRelationModuleModel();
		$parentRecordModule = $this->getParentRecordModel();
		$parentModule = $parentRecordModule->getModule();

		$createViewUrl = $relatedModel->getCreateTaskRecordUrl().'&sourceModule='.$parentModule->get('name').
								'&sourceRecord='.$parentRecordModule->getId().'&relationOperation=true';

		//To keep the reference fieldname and record value in the url if it is direct relation
		if($relationModel->isDirectRelation()) {
			$relationField = $relationModel->getRelationField();
			$createViewUrl .='&'.$relationField->getName().'='.$parentRecordModule->getId();
		}
		return $createViewUrl;
	}

	public function getLinks(){
		$relationModel = $this->getRelationModel();
		$actions = $relationModel->getActions();

		$selectLinks = $this->getSelectRelationLinks();
		foreach($selectLinks as $selectLinkModel) {
			$selectLinkModel->set('_selectRelation',true)->set('_module',$relationModel->getRelationModuleModel());
		}
		$addLinks = $this->getAddRelationLinks();

		$links = array_merge($selectLinks, $addLinks);
		$relatedLink = array();
		$relatedLink['LISTVIEWBASIC'] = $links;
		return $relatedLink;
	}

	public function getSelectRelationLinks() {
		$relationModel = $this->getRelationModel();
		$selectLinkModel = array();

		if(!$relationModel->isSelectActionSupported()) {
			return $selectLinkModel;
		}

		$relatedModel = $relationModel->getRelationModuleModel();

		$selectLinkList = array(
			array(
				'linktype' => 'LISTVIEWBASIC',
				'linklabel' => vtranslate('LBL_SELECT')." ".vtranslate($relatedModel->get('label')),
				'linkurl' => '',
				'linkicon' => '',
			)
		);


		foreach($selectLinkList as $selectLink) {
			$selectLinkModel[] = Vtiger_Link_Model::getInstanceFromValues($selectLink);
		}
		return $selectLinkModel;
	}
	
	//关联是否显示增加按钮 
	public function getAddRelationLinks() {
		$relationModel = $this->getRelationModel();
		$addLinkModel = array();
		if(!$relationModel->isAddActionSupported()) {
			return $addLinkModel;
		}
		$relatedModel = $relationModel->getRelationModuleModel();
	/* 	if($relatedModel->get('label') == 'Calendar'){
			$addLinkList[] = array(
					'linktype' => 'LISTVIEWBASIC',
					'linklabel' => vtranslate('LBL_ADD_EVENT'),
					'linkurl' => $this->getCreateEventRecordUrl(),
					'linkicon' => '',
			);
			$addLinkList[] = array(
					'linktype' => 'LISTVIEWBASIC',
					'linklabel' => vtranslate('LBL_ADD_TASK'),
					'linkurl' => $this->getCreateTaskRecordUrl(),
					'linkicon' => '',
			);
		}else{ */
			if(isPermitted($relatedModel->getName(),'EditView')=='yes'){
				$addLinkList=array(
					'linktype' => 'LISTVIEWBASIC',
					// NOTE: $relatedModel->get('label') assuming it to be a module name - we need singular label for Add action.
					'linklabel' => vtranslate('LBL_ADD')." ".vtranslate('SINGLE_' . $relatedModel->getName(), $relatedModel->getName()),
					'linkurl' => $this->getCreateViewUrl(),
					'linkicon' => '',
				);
				$addLinkModel[] = Vtiger_Link_Model::getInstanceFromValues($addLinkList);
			}	
		//}
		//foreach($addLinkList as $addLink) {}
		return $addLinkModel;
	}

	public function getEntries($pagingModel) {
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
			//$newRow = array();
			/* foreach($row as $col=>$val){if(array_key_exists($col,$relatedColumnFields)){ $newRow[$relatedColumnFields[$col]] = $val; }} */
			//To show the value of "Assigned to"//$newRow['assigned_user_id'] = $row['smownerid'];
			$record = Vtiger_Record_Model::getCleanInstance($relationModule->get('name'));
            $record->setData($row)->setModuleFromInstance($relationModule);
			$record->setId($row['crmid']);
			$relatedRecordList[$row['crmid']] = $record;
		}
	/* 	$pagingModel->calculatePageRange($relatedRecordList);
		$nextLimitQuery = $query. ' LIMIT '.($startIndex+$pageLimit).' , 1';
		$nextPageLimitResult = $db->pquery($nextLimitQuery, array());
		if($db->num_rows($nextPageLimitResult) > 0){$pagingModel->set('nextPageExists', true);}else{$pagingModel->set('nextPageExists', false);} */
		return $relatedRecordList;
	}

	//系统获取表格头部字段
	public function getHeaders() {
		$relationModel = $this->getRelationModel();
		$relatedModuleModel = $relationModel->getRelationModuleModel();
		
		$summaryFieldsList = $relatedModuleModel->getSummaryViewFieldsList();
		
		$headerFields = array();
		if(count($summaryFieldsList) > 0) {
			foreach($summaryFieldsList as $fieldName => $fieldModel) {
				$headerFields[$fieldName] = $fieldModel;
			}
		} else {
			$headerFieldNames = $relatedModuleModel->getRelatedListFields();
			foreach($headerFieldNames as $fieldName) {
				$headerFields[$fieldName] = $relatedModuleModel->getField($fieldName);
			}
		}
		return $headerFields;
	}

	/**
	 * 详情关联sql语句读取
	 */
	public function getRelationQuery() {
		if(empty($this->relationquery)){
			$relationModel = $this->getRelationModel();
			$recordModel = $this->getParentRecordModel();
			$this->relationquery = $relationModel->getQuery($recordModel);
		}

		return $this->relationquery;
	}

	public static function getInstance($parentRecordModel, $relationModuleName, $label=false) {
		$parentModuleName = $parentRecordModel->getModule()->get('name');
		$className = Vtiger_Loader::getComponentClassName('Model', 'RelationListView', $parentModuleName);
		$instance = new $className();
		$parentModuleModel = $parentRecordModel->getModule();
		$relationModuleModel = Vtiger_Module_Model::getInstance($relationModuleName);
		$relationModel = Vtiger_Relation_Model::getInstance($parentModuleModel, $relationModuleModel, $label);
		$instance->setRelationModel($relationModel)->setParentRecordModel($parentRecordModel);
		return $instance;
	}

	/**
	 * Function to get Total number of record in this relation
	 * @return <Integer>
	 */
	public function getRelatedEntriesCount() {
		$db = PearDatabase::getInstance();
		$relationQuery = $this->getRelationQuery();
		$position = stripos($relationQuery, ' from ');
		if ($position) {
			$split = spliti(' from ', $relationQuery);
			$splitCount = count($split);
			$relationQuery = 'SELECT count(*) AS count ';
			for ($i=1; $i<$splitCount; $i++) {
				$relationQuery = $relationQuery. ' FROM ' .$split[$i];
			}
		}
		$result = $db->pquery($relationQuery, array());
		return $db->query_result($result, 0, 'count');
	}

	/**
	 * Function to update relation query
	 * @param <String> $relationQuery
	 * @return <String> $updatedQuery
	 */
	public function updateQueryWithWhereCondition($relationQuery) {
		$condition = '';

		$whereCondition = $this->get("whereCondition");
		$count = count($whereCondition);
		if ($count > 1) {
			$appendAndCondition = true;
		}

		$i = 1;
		foreach ($whereCondition as $fieldName => $fieldValue) {
			$condition .= " $fieldName = '$fieldValue' ";
			if ($appendAndCondition && ($i++ != $count)) {
				$condition .= " AND ";
			}
		}

		$pos = stripos($relationQuery, 'where');
		if ($pos) {
			$split = spliti('where', $relationQuery);
			$updatedQuery = $split[0] . ' WHERE ' . $split[1] . ' AND ' . $condition;
		} else {
			$updatedQuery = $relationQuery . ' WHERE ' . $condition;
		}
		return $updatedQuery;
	}

}