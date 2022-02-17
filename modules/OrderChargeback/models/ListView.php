<?php
/*+********
 * 工单列表权限
 * 非搜索提交加入当前人带审核审核工单
 *******/

class OrderChargeback_ListView_Model extends Vtiger_ListView_Model {

	//根据参数显示数据
	public function getListViewEntries($pagingModel) {
        $db = PearDatabase::getInstance();
        $moduleName = 'OrderChargeback';
        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');

        if(empty($orderBy) && empty($sortOrder)){
            $orderBy = 'vtiger_orderchargeback.orderchargebackid';
            $sortOrder = 'DESC';
        }
        $this->getSearchWhere();
        $listQuery = $this->getQuery();
        $listQuery.=$this->getUserWhere();
        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();
        $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;
        $viewid = ListViewSession::getCurrentView($moduleName);
        ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,
        $listQuery .= " LIMIT $startIndex,".($pageLimit);
        //echo $listQuery;
        $listResult = $db->pquery($listQuery, array());
        $listViewRecordModels = array();
        global $current_user;
        $index = 0;
        while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['orderchargebackid'];
            $rawData['deleted'] = ($current_user->is_admin=='on')?1:0;
            $listViewRecordModels[$rawData['orderchargebackid']] = $rawData;
        }
        return $listViewRecordModels;
	}
    public function getChecksql($user){
        $listQuery=' or (vtiger_orderchargeback.orderchargebackid in (SELECT salesorderid FROM `vtiger_salesorderworkflowstages` where modulename=\'OrderChargeback\' and vtiger_salesorderworkflowstages.isvalidity=0 and vtiger_salesorderworkflowstages.ishigher=0 ';
        $key=md5($user);
        $checknodes=Vtiger_Cache::get('users','node'.$key);
        if(empty($checknodes)){
            $checknodes=getUsersRole($user);
            Vtiger_Cache::set('users','node'.$key,$checknodes);
        }
        if($checknodes){
            $where=getAccessibleUsers('WorkFlowCheck','List');
            if($where!='1=1'){
                $checknode='  (vtiger_salesorderworkflowstages.workflowstagesid in('.$checknodes.') and vtiger_salesorderworkflowstages.smcreatorid '.$where.') ';	//2015-08-22 vtiger_salesorderworkflowstages 代理原来的vtiger_crmentity
            }
        }
        global $current_user;
        $id=$current_user->id;
        $products=getMyproducts($user);
        if($products){
            if(isset($checknode)){
                $listQuery .= ' and ('.$checknode." or vtiger_salesorderworkflowstages.productid in ($products)) ";
            }else{
                $listQuery .=" and vtiger_salesorderworkflowstages.productid in ($products) ";
            }
        }else{
            if(isset($checknode)){
                $listQuery .= 'and '.$checknode;
            }else{
                $listQuery .=" and vtiger_salesorderworkflowstages.productid in (null)";
            }
        }
        $listQuery.=")))";
        return $listQuery;
    }
    public function getUserWhere(){
        $listQuery='';
		$searchDepartment = $_REQUEST['department'];
        $where=getAccessibleUsers('OrderChargeback','List',true);
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
            $listQuery .= ' and (vtiger_crmentity.smownerid in('.$user.') ';
            //获取审核权限
			if(!empty($searchDepartment)&&$searchDepartment!='H1'){ 
				$listQuery .=')';
			}else{
				$listQuery .=$this->getChecksql($user);
			}
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
        $where=$this->getUserWhere();
        $queryGenerator->addUserWhere($where);
        $listQuery =  $queryGenerator->getQueryCount();
        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }
}