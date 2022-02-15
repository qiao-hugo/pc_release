<?php
/*+********
 * 工单列表权限
 * 非搜索提交加入当前人带审核审核工单
 *******/

class SalesOrder_ListView_Model extends Vtiger_ListView_Model {

	//根据参数显示数据
	public function getListViewEntries($pagingModel) {
        $db = PearDatabase::getInstance();
        $moduleName = 'SalesOrder';
        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');

        if(empty($orderBy) && empty($sortOrder)){
            $orderBy = 'vtiger_salesorder.salesorderid';
            $sortOrder = 'DESC';
        }
        $this->getSearchWhere();
        $listQuery = $this->getQuery();
        $listQuery.=$this->getUserWhere();
        //$listQuery .= $this->channelWhereSql();
        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();
        $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;
        $viewid = ListViewSession::getCurrentView($moduleName);
        ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,
        $listQuery .= " LIMIT $startIndex,".($pageLimit);
        //echo $listQuery;die;
        $listResult = $db->pquery($listQuery, array());
        $listViewRecordModels = array();
        global $current_user;
        $index = 0;

        //设置工单作废权限 gaocl add 2018/05/16
        $moduleModel=Vtiger_Module_Model::getCleanInstance($moduleName);
        $is_salesorder_tovoid = $moduleModel->exportGrouprt($moduleName,'orderCancel');

        $recordModel=Vtiger_Record_Model::getCleanInstance($moduleName);

        //正常、打回、回款不足的工单可作废
        $arr_modulestatus = array("a_normal","a_exception","c_lackpayment");
        while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['salesorderid'];
            //判断工单是否有作废权限 gaocl add 2018/05/16
            //$rawData['deleted'] = ($current_user->is_admin=='on')?1:0;
            if($recordModel->isSalesorderProductsRel($rawData['salesorderid'])){
                $rawData['is_salesorder_tovoid'] = ($is_salesorder_tovoid && in_array($rawData['modulestatus'],$arr_modulestatus));
            }else{
                $rawData['is_salesorder_tovoid'] = $is_salesorder_tovoid;
            }
            $listViewRecordModels[$rawData['salesorderid']] = $rawData;
        }
        return $listViewRecordModels;
	}

    // 如果是渠道事业部的人 只能看见渠道的客户，非渠道事业部的人 不能看见
    public function channelWhereSql() {
        global $current_user;
        $departmentdData = getChannelDepart();

        $where = '';
        if ($current_user->id != 1) {
            if (in_array($current_user->departmentid, $departmentdData) ) {
                $where = " AND vtiger_user2department.departmentid IN (";
            } else {
                $where = " AND vtiger_user2department.departmentid NOT IN (";
            }
            foreach ($departmentdData as $key=>$value) {
                $departmentdData[$key] = "'" .$value. "'";
            }
            $where .= implode(',', $departmentdData) . ")";
        }
        return $where;
    }

    public function getChecksql($user){
        $listQuery=' or (vtiger_salesorder.salesorderid in (SELECT salesorderid FROM `vtiger_salesorderworkflowstages` where modulename=\'SalesOrder\' and vtiger_salesorderworkflowstages.isvalidity=0 and vtiger_salesorderworkflowstages.ishigher=0 ';
        //$listQuery=' or (EXISTS(select salesorderid from
		 //vtiger_salesorderworkflowstages where  vtiger_salesorderworkflowstages.salesorderid=vtiger_salesorder.salesorderid AND modulename=\'SalesOrder\' and vtiger_salesorderworkflowstages.isvalidity=0 and vtiger_salesorderworkflowstages.ishigher=0 ';
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
        //$user=Users_Privileges_Model::getInstanceById($current_user->id);
        $id=$current_user->id;
        //$isproductmanager=$user->get('isproductmanager');
        $products=getMyproducts($user);
        if($products){
            if(isset($checknode)){
                $listQuery .= ' and ('.$checknode." or vtiger_salesorderworkflowstages.productid in ($products)) ";
            }else{
                $listQuery .=" and vtiger_salesorderworkflowstages.productid in ($products) ";
            }
            //select productid from vtiger_products	where find_in_set($id,REPLACE(productman,' |##| ',','))  and vtiger_products.productid=vtiger_salesorderworkflowstages.productid
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
        $where=getAccessibleUsers('SalesOrder','List',true);
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
            //young.yang 加入审核人也可以看工单列表
            $listQuery .= ' and (vtiger_crmentity.smownerid in('.$user.') ';
            $src_module=$this->get('src_module');
            if(empty($src_module)){
                $listQuery.=" OR EXISTS(SELECT 1 FROM vtiger_workflowstagesuserid WHERE vtiger_workflowstagesuserid.salesorderid=vtiger_salesorder.salesorderid AND vtiger_workflowstagesuserid.userid in({$user}) AND vtiger_workflowstagesuserid.modulename='SalesOrder')";
            }
            //steel.liu加入客户对应客服,及其上级也能查看工单列表2016-11-01
            $listQuery .= ' OR (vtiger_servicecomments.serviceid in('.$user.') AND vtiger_salesorder.accountid>0)';
            $listQuery.=' OR '.getAccessibleCompany('vtiger_salesorder.servicecontractsid');
            // echo  $sql=SalesorderWorkflowStages_Record_Model::getSalesorderSql($where);
            //获取审核权限
			if(!empty($searchDepartment)&&$searchDepartment!='H1'){ 
				$listQuery .=')';
			}else{
				$listQuery .=$this->getChecksql($user);
			}
        }
        //加入打回工单条件
        if($_REQUEST['public']=='refuse'){
            $listQuery.=" AND vtiger_salesorder.modulestatus = 'a_exception' ";
        }

        //wangbin 修改备案信息关联工单时候，只能唯一；客服任务包需要
        if($this->get('src_module')=='IdcRecords'){
            $listQuery .= ' AND vtiger_salesorder.salesorderid NOT IN (SELECT salesorder_no FROM vtiger_idcrecords)';
        }
        //技术充值加入限制(工单完成OR (执行中且已确认了收入))
        if($this->get('src_module')=='RefillApplication'){
            //$listQuery .= " AND (vtiger_salesorder.modulestatus='c_complete' OR (vtiger_salesorder.modulestatus in('b_actioning','b_check') AND vtiger_salesorder.performanceoftime IS NOT NULL))";
            $listQuery .= " AND vtiger_salesorder.israyment=1";
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
        if(0==$this->isAllCount && 0==$this->isFromMobile){
            return 0;
        }
        $db = PearDatabase::getInstance();
        $queryGenerator = $this->get('query_generator');
        $where=$this->getUserWhere();
        $queryGenerator->addUserWhere($where);
        $listQuery =  $queryGenerator->getQueryCount();
        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }
}