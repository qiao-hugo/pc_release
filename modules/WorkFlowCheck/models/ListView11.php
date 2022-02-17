<?php
/*+***
工作流审核列表
******/

class WorkFlowCheck_ListView_Model extends Vtiger_ListView_Model {

	//根据参数显示数据
	public function getListViewEntries($pagingModel) {
		$db = PearDatabase::getInstance();
		$moduleName = 'WorkFlowCheck';
		$moduleFocus = CRMEntity::getInstance($moduleName);
		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
		$queryGenerator = $this->get('query_generator');
		$listViewContoller = $this->get('listview_controller');
		$searchKey = $this->get('search_key');
		$searchValue = $this->get('search_value');
		$operator = $this->get('operator');
		if(!empty($searchKey)) {
			$queryGenerator->addUserSearchConditions(array('search_field' => $searchKey, 'search_text' => $searchValue, 'operator' => $operator));
		}
		
		$orderBy = $this->getForSql('orderby');
		$sortOrder = $this->getForSql('sortorder');
		//流程列表按创建时间显示
		if(empty($orderBy) && empty($sortOrder)){
			$orderBy = 'createdtime';
			$sortOrder = 'DESC';
		}
		
		if(!empty($orderBy)){
			$columnFieldMapping = $moduleModel->getColumnFieldMapping();//array
			$orderByFieldName = $columnFieldMapping[$orderBy];
			$orderByFieldModel = $moduleModel->getField($orderByFieldName);
			if($orderByFieldModel && $orderByFieldModel->getFieldDataType() == Vtiger_Field_Model::REFERENCE_TYPE){
				//IF it is reference add it in the where fields so that from clause will be having join of the table
				$queryGenerator = $this->get('query_generator');
				$queryGenerator->addWhereField($orderByFieldName);
				//$queryGenerator->whereFields[] = $orderByFieldName;
			}
			 
		}
		$listQuery = $this->getQuery();
		//历史只要读取审核人的id即可
		if($_REQUEST['public']=='history'){
			$listQuery .=' and vtiger_salesorderworkflowstages.isaction=2';
			
			$where=getAccessibleUsers();
			if($where!='1=1'){
				$listQuery .= ' and vtiger_salesorderworkflowstages.auditorid '.$where; //判断条件错误
			}
		}else{
			$listQuery .= $this->getWhereSql();
            if($_REQUEST['public']=='outnumberday'){
				$listQuery .=" AND vtiger_salesorderworkflowstages.actiontime<'".date("Y-m-d H:i:s",strtotime('-1 day'))."'";
			}
		}//风险,一旦第一个位置的字段有变化了,这里就要出错了.
		if(!strstr($listQuery,'modulename')){
			$listQuery=str_replace('SELECT vtiger_salesorderworkflowstages.salesorder_nono','SELECT vtiger_salesorderworkflowstages.modulename,(CASE WHEN vtiger_salesorderworkflowstages.modulename = \'salesorder\' then ( SELECT vtiger_salesorder.salesorder_no FROM vtiger_salesorder WHERE salesorderid = vtiger_salesorderworkflowstages.salesorderid ) else \'--\' end) AS salesorder_nono,vtiger_salesorderworkflowstages.salesorderid',$listQuery);
		}
		$sourceModule = $this->get('src_module');
		if(!empty($sourceModule)) {
			if(method_exists($moduleModel, 'getQueryByModuleField')) {
				$overrideQuery = $moduleModel->getQueryByModuleField($sourceModule, $this->get('src_field'), $this->get('src_record'), $listQuery);
				if(!empty($overrideQuery)) {
					$listQuery = $overrideQuery;
				}
			}
		}
		//echo $listQuery;die();
		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();
		
		if(!empty($orderBy)) {
			if($orderByFieldModel && $orderByFieldModel->isReferenceField()){
				$referenceModules = $orderByFieldModel->getReferenceList();
				$referenceNameFieldOrderBy = array();
				foreach($referenceModules as $referenceModuleName) {
					$referenceModuleModel = Vtiger_Module_Model::getInstance($referenceModuleName);
					$referenceNameFields = $referenceModuleModel->getNameFields();
					$columnList = array();
					foreach($referenceNameFields as $nameField) {
						$fieldModel = $referenceModuleModel->getField($nameField);
						$columnList[] = $fieldModel->get('table').$orderByFieldModel->getName().'.'.$fieldModel->get('column');
					}
					if(count($columnList) > 1) {
						$referenceNameFieldOrderBy[] = getSqlForNameInDisplayFormat(array('first_name'=>$columnList[0],'last_name'=>$columnList[1]),'Users').' '.$sortOrder;
					} else {
						$referenceNameFieldOrderBy[] = implode('', $columnList).' '.$sortOrder ;
					}
				}
				$listQuery .= ' ORDER BY '. implode(',',$referenceNameFieldOrderBy);
			}else{
				$listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;
			}
		}
		
		$viewid = ListViewSession::getCurrentView($moduleName);
		ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,
		//echo $listQuery;die();
		$listQuery .= " LIMIT $startIndex,".($pageLimit+1);
		$listResult = $db->pquery($listQuery, array());
		$listViewRecordModels = array();
		$listViewEntries =  $listViewContoller->getListViewRecords($moduleFocus,$moduleName, $listResult);//获取视图的数据记录,这里已经获取到了数据
		
		$pagingModel->calculatePageRange($listViewEntries);
		
		if($db->num_rows($listResult) > $pageLimit){
			array_pop($listViewEntries);
			$pagingModel->set('nextPageExists', true);
		}else{
			$pagingModel->set('nextPageExists', false);
		}
		$index = 0;
		foreach($listViewEntries as $recordId => $record) {
			$rawData = $db->query_result_rowdata($listResult, $index++);
			$record['id'] = $recordId;
			$listViewRecordModels[$recordId] = $moduleModel->getRecordFromArray($record, $rawData);
			$listViewRecordModels[$recordId]->srcModule=$rawData['modulename'];
			$listViewRecordModels[$recordId]->salesorderid=$rawData['salesorderid'];
		}
		return $listViewRecordModels;
	}
	/**
	 * 条件
	 * @return string
	 */
	public function getWhereSql(){
		//正在审核的需要做多种判断
		$listQuery .=' and vtiger_salesorderworkflowstages.isaction=1';
		
		//取消停单
		$listQuery .=' and vtiger_salesorderworkflowstages.isvalidity=0 ';
			
			
		global $current_user;
		$user=Users_Privileges_Model::getInstanceById($current_user->id);
		if($current_user->superadmin!='on'){
			$current_user_parent_departments=$user->get('current_user_parent_departments');
			$id=$current_user->id;
			$user_manager=$user->get('user_manager');
			$isproductmanager=$user->get('isproductmanager');
			$deparment=" and ((vtiger_salesorderworkflowstages.ishigher=0  ";
			//当前部门//屏蔽掉自己的部门
			/*if(1==2&&!empty($current_user_parent_departments)){
				$deparment .="(vtiger_salesorderworkflowstages.departmentid like '$current_user_parent_departments%' ";
			}else{
				$deparment .="(1=1 ";
			}*/
			//负责部门
			/*
			if(!empty($user_manager)){
				$user_manager=explode(' |##| ', $user_manager);
                $deparment .="(";
                $count=count($user_manager);
                for($i=0;$i<$count;$i++){
					$deparment .=" vtiger_salesorderworkflowstages.departmentid like '$user_manager[$i]%' ";
                    if($i+1<$count){
                        $deparment .=" or ";
                    }
				}
                $deparment .=' ) and';
			}else{
                $deparment .="(vtiger_salesorderworkflowstages.departmentid like 'H2%' ) and";
            }
			*/
            $flowid=getWorkflowsByUserid();//@TODO 这里要修改成读取下级用户的所有的权限审核权限id
            if(empty($flowid)){
                //超管验证
                //if(!$current_user->superadmin){
                    //throw new AppException('您没有任何审核节点！');
                    //	exit;
                    $deparment .=' and  vtiger_salesorderworkflowstages.workflowstagesid in (0)';
                //}
            }else{
                $deparment .=' and  vtiger_salesorderworkflowstages.workflowstagesid in ('.$flowid.')';
            }
            $deparment .=' )';
			//
			//产品负责人
			if($isproductmanager){
				$deparment .=" OR vtiger_salesorderworkflowstages.productid in ( select productid from vtiger_products	where find_in_set($id,REPLACE(productman,' |##| ',','))  and vtiger_products.productid=vtiger_salesorderworkflowstages.productid)";
			}
			
		

			
			//自己可以直接审核的
			$deparment.="  or (vtiger_salesorderworkflowstages.ishigher=1 and vtiger_salesorderworkflowstages.higherid=".$id."))";
			//审核人为自己的
			//$deparment.=' and vtiger_salesorderworkflowstages.smcreatorid!='.$id;
			$listQuery .=$deparment;
		}
		return $listQuery;
	}
	/**
	 * 获取当前活动的所有节点
	 * @param unknown $moduleName
	 * @param unknown $recordId
	 * @param string $isarray
	 * @return multitype:Ambigous <>
	 */
	public function getActioning($moduleName,$recordId,$isarray=false){
		$db = PearDatabase::getInstance();
        //young.yang 详细页面加入任意上级可审核
        //2015-3-5 修复$where 为空的问题，同时将创建修改为审核人的id判断，上级是否可以审核
        global $current_user;
        $where=$current_user->subordinate_users;
        if(!$where){
            $where='';
        }else{
            $where=implode(',',$where);
            $where='or higherid in('.$where.')';
        }//end
		$sql = 'select * from vtiger_salesorderworkflowstages where modulename=\''.$moduleName.'\' and salesorderid='.$recordId.' and (1=1 '.$this->getWhereSql().' '.$where.' )';//echo $sql;
        $result = $db->pquery($sql,array());
		$allStagers=array();
		if($db->num_rows($result)){
			while($row=$db->fetch_array($result)){
				$allStagers[$row['salesorderworkflowstagesid']]=$row;
			}
		}
		return $allStagers;
	}

	
}