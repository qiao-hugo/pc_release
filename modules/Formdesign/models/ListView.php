<?php
class Formdesign_ListView_Model extends Vtiger_ListView_Model {

	public function getListViewEntries($pagingModel) {
		$db = PearDatabase::getInstance();
		$moduleName ='Formdesign';

		$listQuery = $this->getQuery();
        $this->getSearchWhere();
        $queryGenerator = $this->get('query_generator');
        $searchwhere=$queryGenerator->getSearchWhere();
        if(!empty($searchwhere)){
            $listQuery.=' and '.$searchwhere;
        }
		$listQuery.=$this->getUserWhere();
		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();
		$listQuery .= " LIMIT $startIndex,".($pageLimit);
		//global $current_user;
		//$viewid = ListViewSession::getCurrentView($moduleName);
		//ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,
		$listResult = $db->pquery($listQuery);
		$listViewRecordModels = array();

		 while ($rawData = $db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['formid'];
            $listViewRecordModels[$rawData['id']] = $rawData;
        }
		
		return $listViewRecordModels;
	}
	public function getUserWhere(){
		return ' and deleted=0';
	}

	/**
	 * 获取记录行数[先从session获取]
	 * @param Vtiger_Paging_Model $pagingModel
	 * @return <Array> - Associative array of record id mapped to Vtiger_Record_Model instance.
	 */
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
        $where=$this->getUserWhere();
        $queryGenerator->addUserWhere($where);
        $listQuery =  $queryGenerator->getQueryCount();
        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }
	
	

	
	
	
	
	
}
