<?php
/*+********
 * 工单列表权限
 * 非搜索提交加入当前人带审核审核工单
 *******/

class RechargePlatform_ListView_Model extends Vtiger_ListView_Model {

	//根据参数显示数据
	public function getListViewEntries($pagingModel, $searchField=null) {
        $db = PearDatabase::getInstance();
        $moduleName = 'RechargePlatform';
        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');

        if(empty($orderBy) && empty($sortOrder)){
            $orderBy = 'vtiger_topplatform.topplatformid';
            $sortOrder = 'DESC';
        }
        $this->getSearchWhere();
        $listQuery = $this->getQuery();
        $listQuery.=$this->getUserWhere();


        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();
       //$listQuery .=' GROUP BY rechargeplatformid';//用分组来去重;
        $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;
        $viewid = ListViewSession::getCurrentView($moduleName);
        ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,

        $listQuery .= " LIMIT $startIndex,".($pageLimit);
        //return $listQuery;
        //echo $listQuery;die();
        $listResult = $db->pquery($listQuery, array());
        $listViewRecordModels = array();
        global $current_user;
        $index = 0;
        while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['topplatformid'];
            $rawData['deleted'] = ($current_user->is_admin=='on')?1:0;
            $listViewRecordModels[$rawData['topplatformid']] = $rawData;
        }
        return $listViewRecordModels;
	}

    public function getUserWhere(){
        $listQuery='';
		$searchDepartment = $_REQUEST['department'];
        $where=getAccessibleUsers('RechargePlatform','List',true);

		if(!empty($searchDepartment)&&$searchDepartment!='H1'){  //20150427 young 取消默认的H1验证
            $userid=getDepartmentUser($searchDepartment);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $where=!empty($where)?$where:array(-1);
		}
        if($where!='1=1'){
            $user=implode(',',$where);
            $listQuery .= ' and vtiger_crmentity.smownerid in('.$user.')';
        }
        //追加以下条件(针对移动crm) 2017/02/28 gaocl add
        $listQuery .= ' and vtiger_crmentity.deleted=0';
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
        $where=$this->getUserWhere();
        $queryGenerator->addUserWhere($where);
        $listQuery =  $queryGenerator->getQueryCount();
        //$listQuery .=' GROUP BY vtiger_refillapplication.refillapplicationid';//用分组来去重;
        $listResult = $db->pquery($listQuery, array());
        return $db->num_rows($listResult);
    }
}
