<?php
/*+***
工作流审核列表
******/

class WorkFlowCheck_ListView_Model extends Vtiger_ListView_Model {

	//根据参数显示数据
	public function getListViewEntries($pagingModel,$searchField=array()) {

        $db = PearDatabase::getInstance();
        $moduleName = 'WorkFlowCheck';
        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');
        if(empty($orderBy) && empty($sortOrder) && $moduleName != "Users"){
            $orderBy = 'salesorderworkflowstagesid';
            $sortOrder = 'DESC';
        }
        $this->getSearchWhere();
        $listQuery = $this->getQuery();
        $listQuery.=$this->getUserWhere();
        $listQuery=str_replace('SELECT vtiger_salesorderworkflowstages.salesorder_nono','SELECT vtiger_salesorderworkflowstages.salesorder_nono,vtiger_salesorderworkflowstages.originalmoduleid',$listQuery);
        if(strstr($listQuery,'salesorder_nono')){
            $listQuery=str_replace('SELECT vtiger_salesorderworkflowstages.salesorder_nono','SELECT vtiger_salesorderworkflowstages.salesorder_nono,vtiger_salesorderworkflowstages.salesorderid as newsalesorderid,vtiger_users.email1 as email',$listQuery);
        }
        if(strstr($listQuery,'orderchargeback_no')){
            $listQuery=str_replace('LEFT JOIN vtiger_workflows ON vtiger_workflows.workflowsid=vtiger_salesorderworkflowstages.workflowsid',' LEFT JOIN vtiger_workflows ON vtiger_workflows.workflowsid=vtiger_salesorderworkflowstages.workflowsid   LEFT JOIN vtiger_orderchargeback ON vtiger_orderchargeback.orderchargebackid=vtiger_salesorderworkflowstages.salesorderid ',$listQuery);
        }
        $listQuery=str_replace('LEFT JOIN vtiger_workflows ON vtiger_workflows.workflowsid=vtiger_salesorderworkflowstages.workflowsid',' LEFT JOIN vtiger_workflows ON vtiger_workflows.workflowsid=vtiger_salesorderworkflowstages.workflowsid   LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_salesorderworkflowstages.smcreatorid ',$listQuery);
        // 如果不是我发起的条件走if  是我发起的else
        if($_REQUEST['public']!='iInitiated'){
            //steel加入合同归还时间
            /*$listQuery=str_replace('LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_salesorder.accountid','LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_salesorder.servicecontractsid LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_salesorder.accountid',$listQuery);*/
            /*$listQuery=str_replace('(vtiger_salesorder.modulestatus) as modulestatus,','(vtiger_salesorderworkflowstages.modulestatus) as modulestatus,vtiger_salesorderworkflowstages.accountid as accountidid,',$listQuery);*/
            global  $current_user;
            //如果非超级管理员加下面的条件 当前用户为节点审核角色时的数据显示出来
            if($current_user->is_admin!='on'){
                // 关联审核节点表
                $listQuery=str_replace('LEFT JOIN vtiger_workflows ON vtiger_workflows.workflowsid=vtiger_salesorderworkflowstages.workflowsid',' LEFT JOIN vtiger_workflows ON vtiger_workflows.workflowsid=vtiger_salesorderworkflowstages.workflowsid  LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid=vtiger_salesorderworkflowstages.workflowstagesid ',$listQuery);
                //加条件审核节点角色等于 当前登录用户角色
                $roleid = $current_user->roleid;
                $newSql = " and  find_in_set('".$roleid."',REPLACE(vtiger_workflowstages.isrole,' |##| ',','))  and vtiger_salesorderworkflowstages.smcreatorid ";
                $listQuery=str_replace('and vtiger_salesorderworkflowstages.smcreatorid ',$newSql,$listQuery);
            }
            $startIndex = $pagingModel->getStartIndex();
            $pageLimit = $pagingModel->getPageLimit();
            $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;
            //$viewid = ListViewSession::getCurrentView($moduleName);
            //ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,
            $listQuery .= " LIMIT $startIndex,".($pageLimit);
            //echo $listQuery;die();
            $listResult = $db->pquery($listQuery, array());
            $listViewRecordModels = array();
            //3.在进行一次转化，目的何在
            $index = 0;
            $moduleArray=array("Vendors","SupplierContracts","SuppContractsAgreement");
            while($rawData=$db->fetch_array($listResult)) {
                $rawData['id'] = $rawData['salesorderworkflowstagesid'];
                $rawData['accountModuleName'] = in_array($rawData['modulename'],$moduleArray)?'Vendors':'Accounts';
                $listViewRecordModels[$rawData['salesorderworkflowstagesid']] = $rawData;
            }
            return $listViewRecordModels;
        }else{
            $listQuery= " SELECT  *  FROM (" .$listQuery." ORDER BY vtiger_salesorderworkflowstages.actiontime DESC  ) as a  GROUP BY  newsalesorderid  ";
            $startIndex = $pagingModel->getStartIndex();
            $pageLimit = $pagingModel->getPageLimit();
            $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;
            $viewid = ListViewSession::getCurrentView($moduleName);
            ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,
            $listQuery .= " LIMIT $startIndex,".($pageLimit);
            //echo $listQuery;die();
            $listResult = $db->pquery($listQuery, array());
            $listViewRecordModels = array();
            //3.在进行一次转化，目的何在
            $index = 0;
            $moduleArray=array("Vendors","SupplierContracts","SuppContractsAgreement");
            while($rawData=$db->fetch_array($listResult)) {
                $rawData['id'] = $rawData['salesorderworkflowstagesid'];
                $rawData['accountModuleName'] = in_array($rawData['modulename'],$moduleArray)?'Vendors':'Accounts';
                if($rawData['modulestatus']=='c_complete'){
                    $rawData['workflowstagesname']='已完成';
                }
                $listViewRecordModels[$rawData['salesorderworkflowstagesid']] = $rawData;
            }

            return $listViewRecordModels;
        }



    }
	/**
	 * 条件
	 * @return string
	 */
	public function getWhereSql($where,$moduleName = ""){
		//正在审核的需要做多种判断
		//取消停单
		$listQuery =' and vtiger_salesorderworkflowstages.isvalidity=0 and vtiger_salesorderworkflowstages.isaction=1 ';
		global $current_user;
		$user=Users_Privileges_Model::getInstanceById($current_user->id);
		if($current_user->superadmin!='on'){

			$id=$current_user->id;
			$isproductmanager=$user->get('isproductmanager');
			$deparment=" and ( ";
            $sql='';

            //普通审核权限
            if($where!='1=1'){
            	$flowid=getWorkflowsByUserid();//@TODO 这里要修改成读取下级用户的所有的权限审核权限id
	            if(empty($flowid)){
                    $sql.="";
                }else{
                    $newServiceContractsStr=getAccessibleCompany('','ServiceContracts',true,$record=-1,'X-X-X',true);
                    $newInvoiceStr=getAccessibleCompany('','Newinvoice',true,$record=-1,'X-X-X',true);
                    $newSupplierContractsStr=getAccessibleCompany('','SupplierContracts',true,$record=-1,'X-X-X',true);
                    //$deparment.=" (vtiger_salesorderworkflowstages.ishigher=0 AND exists(SELECT 1 FROM vtiger_invoicecompanyuser WHERE vtiger_invoicecompanyuser.deleted=0 AND vtiger_invoicecompanyuser.invoicecompany=vtiger_salesorderworkflowstages.companycode AND vtiger_invoicecompanyuser.userid=$id)) OR";
                    $deparment.=" (vtiger_salesorderworkflowstages.ishigher=0 AND if(
            (vtiger_salesorderworkflowstages.modulename='ServiceContracts' OR vtiger_salesorderworkflowstages.modulename='RefillApplication' OR vtiger_salesorderworkflowstages.modulename='ContractsAgreement' OR vtiger_salesorderworkflowstages.modulename='SalesOrder' OR vtiger_salesorderworkflowstages.modulename='Staypayment'),
            (vtiger_salesorderworkflowstages.companycode".$newServiceContractsStr."),
            if((vtiger_salesorderworkflowstages.modulename='SupplierContracts' OR vtiger_salesorderworkflowstages.modulename='RefillApplication' OR vtiger_salesorderworkflowstages.modulename='SuppContractsAgreement'),
            (vtiger_salesorderworkflowstages.companycode".$newSupplierContractsStr."),
            if(vtiger_salesorderworkflowstages.modulename='Newinvoice',
            (vtiger_salesorderworkflowstages.companycode".$newInvoiceStr."),1=2))) AND vtiger_salesorderworkflowstages.workflowstagesid in ($flowid)) OR";
	            	$sql.="  ( vtiger_salesorderworkflowstages.ishigher=0  and vtiger_salesorderworkflowstages.smcreatorid $where  and vtiger_salesorderworkflowstages.workflowstagesid in ($flowid)) OR ";
                }
            }
            $deparment .=$sql;
			//产品负责人
			if($isproductmanager){
				$deparment .="  ( vtiger_salesorderworkflowstages.ishigher=0  and vtiger_salesorderworkflowstages.productid in ( select productid from vtiger_products	where find_in_set($id,REPLACE(productman,' |##| ',','))  and vtiger_products.productid=vtiger_salesorderworkflowstages.productid)) OR ";
			}else{
				//$deparment .=" ) OR ";
			}
			//充值申请单的处理
            if($moduleName == "RefillApplication"){
                $deparment .="  ( vtiger_salesorderworkflowstages.ishigher=0  and find_in_set($id,REPLACE(vtiger_salesorderworkflowstages.platformids,' |##| ',','))) OR ";
            }
            $deparment.=" (vtiger_salesorderworkflowstages.handleaction='maincompany' AND EXISTS(SELECT 1 FROM `vtiger_invoicecompanyuser` WHERE vtiger_invoicecompanyuser.invoicecompany=vtiger_salesorderworkflowstages.companycode AND vtiger_invoicecompanyuser.userid=".$id." AND vtiger_invoicecompanyuser.modulename='gs' AND vtiger_invoicecompanyuser.deleted=0)) OR ";
			//自己可以直接审核的
			$deparment.="  (vtiger_salesorderworkflowstages.ishigher=1 and vtiger_salesorderworkflowstages.higherid=".$id.") ) ";
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
        if($isarray){
            $whereextend1=getAccessibleUsers($moduleName,'List',false);
            $whereextend2=getAccessibleUsers('WorkFlowCheck','List',false);
        }else{
            $whereextend1='';
            $whereextend2=getAccessibleUsers('WorkFlowCheck','List',false);
        }
        if(is_array($whereextend1) && is_array($whereextend2)){
            $whereextend=array_merge($whereextend1,$whereextend2);
            $whereextend=array_unique($whereextend);
        }else{
            $whereextend=$whereextend2;
        }
        if(!$where){
            $where='';
        }else{
            $where=implode(',',$where);
			$where=' in('.$where.')';
            $wheresql='or higherid '.$where.'';
        }//end //young.yang 2015-05-08 getWhereSql掉参数*/
		//$wheresql='or higherid '.$where.'';
		$sql = "select * from vtiger_salesorderworkflowstages where modulename='".$moduleName."' and salesorderid=".$recordId." and (1=1 ".$this->getWhereSql($whereextend,$moduleName).' '.$wheresql." )";//echo $sql;
        //print_r($sql);die();
        $result = $db->pquery($sql,array());
		$allStagers=array();
		if($db->num_rows($result)){
			while($row=$db->fetch_array($result)){
				$allStagers[$row['salesorderworkflowstagesid']]=$row;
			}
		}
		return $allStagers;
	}
    /**
     * 2019-08-15 cxh
     * 获取当前活动的所有节点(仅包含普通审核权限的不包含可以直接审核的和产品负责人)
     * @param unknown $moduleName
     * @param unknown $recordId
     * @param string $isarray
     * @return multitype:Ambigous <>
     */
    public function getGeneralAudit($moduleName,$recordId,$isarray=false){
        /*$db = PearDatabase::getInstance();
        //young.yang 详细页面加入任意上级可审核
        //2015-3-5 修复$where 为空的问题，同时将创建修改为审核人的id判断，上级是否可以审核
        global $current_user;
        $where=$current_user->subordinate_users;
        $whereextend=getAccessibleUsers('WorkFlowCheck','List',false);
        if(!$where){
            $where='';
        }else{
            $where=implode(',',$where);
            $where=' in('.$where.')';
            $wheresql='or higherid '.$where.'';
        }
        //普通审核权限
        if($whereextend!='1=1'){
            $flowid=getWorkflowsByUserid();//@TODO 这里要修改成读取下级用户的所有的权限审核权限id
            $need='';
            if(empty($flowid)){
                $need.="";
            }else{
                $need.=" AND vtiger_salesorderworkflowstages.isvalidity=0 and vtiger_salesorderworkflowstages.isaction=1  AND ( vtiger_salesorderworkflowstages.ishigher=0  and vtiger_salesorderworkflowstages.smcreatorid $whereextend  AND vtiger_salesorderworkflowstages.workflowstagesid in ($flowid))";
            }
        }
        $roleid = $current_user->roleid;
        $sql = "select * from vtiger_salesorderworkflowstages  LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid=vtiger_salesorderworkflowstages.workflowstagesid  where    modulename='".$moduleName."' and salesorderid=".$recordId."  and  find_in_set('".$roleid."',REPLACE(vtiger_workflowstages.isrole,' |##| ',','))  and (1=1 ".$need."  ".$wheresql." )";//echo $sql;
        $result = $db->pquery($sql,array());
        $allStagers=array();
        if($db->num_rows($result)){
            while($row=$db->fetch_array($result)){
                $allStagers[]=$row['salesorderworkflowstagesid'];
            }
        }
        return $allStagers;*/
        $db = PearDatabase::getInstance();
        //young.yang 详细页面加入任意上级可审核
        //2015-3-5 修复$where 为空的问题，同时将创建修改为审核人的id判断，上级是否可以审核
        global $current_user;
        $where=$current_user->subordinate_users;
        $whereextend=getAccessibleUsers('WorkFlowCheck','List',false);
        if(!$where){
            $where='';
        }else{
            $where=implode(',',$where);
            $where=' in('.$where.')';
            $wheresql='or higherid '.$where.'';
        }//end //young.yang 2015-05-08 getWhereSql掉参数*/
        //$wheresql='or higherid '.$where.'';
        $sql = 'select * from vtiger_salesorderworkflowstages where modulename=\''.$moduleName.'\' and salesorderid='.$recordId.' and (1=1 '.$this->getWhereSql($whereextend,$moduleName).' '.$wheresql.' )';//echo $sql;
        //print_r($sql);die();
        $result = $db->pquery($sql,array());
        $allStagers=array();
        if($db->num_rows($result)){
            while($row=$db->fetch_array($result)){
                $allStagers[$row['salesorderworkflowstagesid']]=$row;
            }
        }
        return $allStagers;
    }
    public function getUserWhere(){
        $listQuery='';
        $where=getAccessibleUsers();
        global  $current_user;
        $userid=$current_user->id;
        if($_REQUEST['public']=='history'){
            $role = $current_user->roleid;
            $listQuery .=" and vtiger_salesorderworkflowstages.isaction=2  AND  vtiger_salesorderworkflowstages.auditorid ={$userid} ";


            if($where!='1=1'){
                $listQuery .= ' and vtiger_salesorderworkflowstages.auditorid '.$where; //判断条件错误
            }
        }else{
            $listQuery .= $this->getWhereSql($where);
            if($_REQUEST['public']=='toBeTriedByMe'){
                //一进来默认都是待审的 所以不用拼接sql 了。
                /* $listQuery.="  AND  ( vtiger_salesorderworkflowstages.departmentid=  ";*/
            }elseif($_REQUEST['public']=='iInitiated'){
                $listQuery= " AND  vtiger_salesorderworkflowstages.smcreatorid={$userid} ";
            }elseif($_REQUEST['public']=='outnumberday'){
                $listQuery .=" AND vtiger_salesorderworkflowstages.actiontime<'".date("Y-m-d H:i:s",strtotime('-1 day'))."'";
            }elseif($_REQUEST['public']=='SalesOrder'){
                $listQuery.=" AND vtiger_salesorderworkflowstages.modulename = 'SalesOrder'";
            }elseif($_REQUEST['public']=='VisitingOrder'){
                $listQuery.=" AND vtiger_salesorderworkflowstages.modulename = 'VisitingOrder'";
            }elseif($_REQUEST['public']=='Invoice'){
                $listQuery.=" AND vtiger_salesorderworkflowstages.modulename = 'Invoice'";
                $searchDepartment = $_REQUEST['department'];
	            if(!empty($searchDepartment)&&$searchDepartment!='H1'){  //20150525 柳林刚 加入
	                $userid=getDepartmentUser($searchDepartment);
	                $where=$userid;
	                $listQuery .= ' and vtiger_salesorderworkflowstages.smcreatorid in ('.implode(',',$where).')';
	            }
            }
        }//风险,一旦第一个位置的字段有变化了,这里就要出错了.
        if(isset($_REQUEST['department'])&&!empty($_REQUEST['department']) && $_REQUEST['public']!='Invoice'){  //young.yang 2015-05-08 判断不为空的情况
            $listQuery .=" AND vtiger_salesorderworkflowstages.salesorder_nono like '%".vtlib_purify(trim($_REQUEST['department']))."%' ";
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
                if($fields['fieldlabel']=='Account Name'){continue;}
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
        //$this->getSearchWhere();
        //用户条件
        $where=$this->getUserWhere();
        //$where.= ' AND accountname is NOT NULL';
        $queryGenerator->addUserWhere($where);
        $listQuery =  $queryGenerator->getQueryCount();
        if(strstr($listQuery,'orderchargeback_no')){
            $listQuery=str_replace('LEFT JOIN vtiger_workflows ON vtiger_workflows.workflowsid=vtiger_salesorderworkflowstages.workflowsid',' LEFT JOIN vtiger_workflows ON vtiger_workflows.workflowsid=vtiger_salesorderworkflowstages.workflowsid   LEFT JOIN vtiger_orderchargeback ON vtiger_orderchargeback.orderchargebackid=vtiger_salesorderworkflowstages.salesorderid ',$listQuery);
        }
        if($_REQUEST['public']!='iInitiated'){
            global  $current_user;
            // 如果非超级管理员加下面的条件 当前用户为节点审核角色时的数据显示出来
            if($current_user->is_admin!='on'){
                // 关联审核节点表
                $listQuery=str_replace('LEFT JOIN vtiger_workflows ON vtiger_workflows.workflowsid=vtiger_salesorderworkflowstages.workflowsid',' LEFT JOIN vtiger_workflows ON vtiger_workflows.workflowsid=vtiger_salesorderworkflowstages.workflowsid  LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid=vtiger_salesorderworkflowstages.workflowstagesid ',$listQuery);
                //加条件审核节点角色等于 当前登录用户角色
                $roleid = $current_user->roleid;
                $newSql = " and  find_in_set('".$roleid."',REPLACE(vtiger_workflowstages.isrole,' |##| ',','))  and vtiger_salesorderworkflowstages.smcreatorid ";
                $listQuery=str_replace('and vtiger_salesorderworkflowstages.smcreatorid ',$newSql,$listQuery);
            }
            $listResult = $db->pquery($listQuery, array());
            return $db->query_result($listResult,0,'counts');
        }else{
            $listQuery=str_replace('count(1) as counts',' vtiger_salesorderworkflowstages.* ',$listQuery);
            $listQuery= " SELECT  *  FROM (" .$listQuery." ORDER BY vtiger_salesorderworkflowstages.actiontime DESC  ) as a  GROUP BY  salesorderid  ";
            $listResult = $db->pquery($listQuery, array());
            return $db->num_rows($listResult);
        }

    }

	//模块无新增
	public function getListViewLinks($linkParams) {

		$links=array();

		return $links;
	}

}
