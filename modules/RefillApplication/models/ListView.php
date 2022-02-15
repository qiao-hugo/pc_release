<?php
/*+********
 * 工单列表权限
 * 非搜索提交加入当前人带审核审核工单
 *******/

class RefillApplication_ListView_Model extends Vtiger_ListView_Model {

    /**
     * 模块列表页面显示链接 保留新增 Edit By Joe @20150511
     * @param <Array> $linkParams
     * @return <Array> - Associate array of Link Type to List of Vtiger_Link_Model instances
     */
    public function getListViewLinks($linkParams) {
        $basicLinks = array();
        $links=array();
        $moduleModel = $this->getModule();
        $createPermission = isPermitted($moduleModel->getName(), 'EditView');
        if($createPermission=='yes') {
            $basicLinks[] = array(
                'linktype' => 'LISTVIEWBASIC',
                'linklabel' => 'LBL_ADD_RECORD',
                'linkurl' => $moduleModel->getCreateRecordUrl(),
                'linkicon' => ''
            );
            $basicLinks[] = array(
                'linktype' => 'LISTVIEWBASIC',
                'linklabel' => 'LBL_ADD_RECORD_VENDORS',
                'linkurl' => $moduleModel->getCreateRecordUrl().'&rechargesource=Vendors',
                'linkicon' => ''
            );
            $basicLinks[] = array(
                'linktype' => 'LISTVIEWBASIC',
                'linklabel' => 'LBL_ADD_RECORD_TECHPROCUREMENT',
                'linkurl' => $moduleModel->getCreateRecordUrl().'&rechargesource=TECHPROCUREMENT',
                'linkicon' => ''
            );
            $basicLinks[] = array(
                'linktype' => 'LISTVIEWBASIC',
                'linklabel' => 'LBL_ADD_RECORD_PRERECHARGE',
                'linkurl' => $moduleModel->getCreateRecordUrl().'&rechargesource=PreRecharge',
                'linkicon' => ''
            );
            /*$basicLinks[] = array(
                'linktype' => 'LISTVIEWBASIC',
                'linklabel' => 'LBL_ADD_RECORD_OtherProcurement',
                'linkurl' => $moduleModel->getCreateRecordUrl().'&rechargesource=OtherProcurement',
                'linkicon' => ''
            );*/
            $basicLinks[] = array(
                'linktype' => 'LISTVIEWBASIC',
                'linklabel' => 'LBL_ADD_RECORD_NonMediaExtraction',
                'linkurl' => $moduleModel->getCreateRecordUrl().'&rechargesource=NonMediaExtraction',
                'linkicon' => ''
            );
            $basicLinks[] = array(
                'linktype' => 'LISTVIEWBASIC',
                'linklabel' => 'LBL_ADD_RECORD_INCREASE',
                'linkurl' => $moduleModel->getCreateRecordUrl().'&rechargesource=INCREASE',
                'linkicon' => ''
            );
            $basicLinks[] = array(
                'linktype' => 'LISTVIEWBASIC',
                'linklabel' => 'LBL_ADD_RECORD_PACKVENDORS',
                'linkurl' => $moduleModel->getCreateRecordUrl().'&rechargesource=PACKVENDORS',
                'linkicon' => ''
            );
            $basicLinks[] = array(
                'linktype' => 'LISTVIEWBASIC',
                'linklabel' => 'LBL_ADD_RECORD_COINRETURN',
                'linkurl' => $moduleModel->getCreateRecordUrl().'&rechargesource=COINRETURN',
                'linkicon' => ''
            );
            foreach($basicLinks as $basicLink) {
                $links['LISTVIEWBASIC'][] = Vtiger_Link_Model::getInstanceFromValues($basicLink);
            }
        }
        return $links;
    }
	//根据参数显示数据
	public function getListViewEntries($pagingModel, $searchField=null) {
        if($this->get('src_module')=='Rechargesheet'){
            return $this->getListViewDidEntries($pagingModel,$searchField);
        }
        $db = PearDatabase::getInstance();
        $moduleName = 'RefillApplication';
        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');

        if(empty($orderBy) && empty($sortOrder)){
            $orderBy = 'vtiger_refillapplication.refillapplicationid';
            $sortOrder = 'DESC';
        }
        $this->getSearchWhere();
        $listQuery = $this->getQuery();
        $listQuery.=$this->getUserWhere();
		if(!empty($searchField)){
			foreach($searchField as $k=>$v){
				if($v == 'checking'){
					$listQuery.= " and ".$k ." in ('b_actioning', 'a_normal', 'b_check')";
				}elseif(is_array($v)){
				    if($v['search_key']=='vtiger_products.productname'){
                        $listQuery.= " and ".$v['search_key'] .$v['operator'] . $v['search_value'];
                        $listQuery.=" AND (vtiger_refillapplication.workflowsnode LIKE '财务充值%' OR vtiger_refillapplication.workflowsnode LIKE '出纳出款审核%')";
                    }else{
                        $listQuery.= " and ".$v['search_key'] .$v['operator'] . $v['search_value'];
                    }
				}else{
					$listQuery.= " and ".$k ."='" . $v. "'";
				}
				
			}
		}
		

        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();
        $listQuery .=' GROUP BY vtiger_refillapplication.refillapplicationid';//用分组来去重;
        $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;
        $listQuery=str_replace('vtiger_refillapplication.refillapplicationno,','vtiger_refillapplication.refillapplicationno,(SELECT vtiger_users.email1 FROM vtiger_users WHERE vtiger_users.id=vtiger_crmentity.smownerid LIMIT 1) as email,',$listQuery);
        $viewid = ListViewSession::getCurrentView($moduleName);
        ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,
        // 如果是合同变更申请导出则不需要 limit 条件限制数量
        if($_REQUEST['public']=='contractChangesExport'){

        }else{
            $listQuery .= " LIMIT $startIndex,".($pageLimit);
        }

        //return $listQuery;
        //echo $listQuery;exit;
        $listResult = $db->pquery($listQuery, array());
        $listViewRecordModels = array();
        global $current_user;
        $recordModel=Vtiger_Record_Model::getCleanInstance('RefillApplication');
        $financialStateAuthority=$recordModel->personalAuthority('RefillApplication','financialstate')?1:0;
        $index = 0;
        $rechargesource=array('NonMediaExtraction','Accounts','Vendors',);
        while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['refillapplicationid'];
            $rawData['deleted'] = ($current_user->is_admin=='on')?1:0;
            $rawData['financialStateAuthority'] = $financialStateAuthority;
            $rawData['paymentsflag']=($rawData['smownerid_owner']==$current_user->id && in_array($rawData['rechargesource'],$rechargesource) && $rawData['receivedstatus']!='virtualrefund')?1:0;
            /*if($rawData['financialstate']=='是'){
                $rawData['grossadvances']=0.00;
                $rawData['totalrecharge']=$rawData['actualtotalrecharge'];
            }*/
            $listViewRecordModels[$rawData['refillapplicationid']] = $rawData;
        }
        return $listViewRecordModels;
	}

    /**
     * 选择id 2021.2.3更新
     * @param $pagingModel
     * @param $searchField
     * @return array
     */
	public function  getListViewDidEntries($pagingModel, $searchField){
        $db=PearDatabase::getInstance();
        $recordId=$this->get('src_record');
        $searchdid=$this->get('search_value');
        $return = array();
        if($recordId>0) {
            if($_REQUEST['isProvider']){
                $searchdidSQL='';
                $params=array($_REQUEST['src_vendor'],$recordId);
                if(!empty($searchdid) && $searchdid!=''){
                    $params[]='%'.$searchdid.'%';
                    $searchdidSQL=' AND vtiger_productprovider_detail.idaccount like ?';
                }
                $query="SELECT vtiger_productprovider.`productid`,
                                  `vtiger_products`.productname,
                                  vtiger_productprovider.`vendorid`,
                                  vtiger_productprovider.`suppliercontractsid`,
                                  vtiger_productprovider.`supplierrebate`,
                                  vtiger_productprovider.`servicestartdate`,
                                  vtiger_productprovider.`serviceenddate`,
                                  vtiger_productprovider.`workflowstime`,
                                  vtiger_productprovider.`workflowsnode`,
                                  vtiger_productprovider_detail.idaccount,
                                  vtiger_productprovider.accountid,
                                  vtiger_productprovider_detail.accountzh,
                                  vtiger_productprovider.accountrebate,
                                  IFNULL(vtiger_productprovider.rebatetype,'GoodsBack') AS rebatetype,
                                  IFNULL(vtiger_productprovider.accountrebatetype,'GoodsBack') AS accountrebatetype,
                                  IFNULL(vtiger_suppliercontracts.contract_no,'担保合同') AS contract_no,
                                  vtiger_suppliercontracts.modulestatus,
                                  vtiger_productprovider.customeroriginattr,
                                  vtiger_productprovider.isprovideservice,
                                  vtiger_suppliercontracts.total,
                                  vtiger_suppliercontracts.signdate
                                FROM vtiger_productprovider 
                                LEFT JOIN vtiger_products ON vtiger_products.productid=vtiger_productprovider.productid 
                                LEFT JOIN vtiger_suppliercontracts ON vtiger_suppliercontracts.suppliercontractsid=vtiger_productprovider.`suppliercontractsid`
                                LEFT JOIN vtiger_productprovider_detail ON vtiger_productprovider_detail.productproviderid=vtiger_productprovider.productproviderid
                                WHERE
                                vtiger_productprovider.vendorid=? AND vtiger_productprovider.modulestatus='c_complete' AND vtiger_productprovider.isforbidden=0
                                AND vtiger_productprovider.accountid=? AND vtiger_productprovider.accountid>0 ".$searchdidSQL;
                $startIndex = $pagingModel->getStartIndex();
                $pageLimit = $pagingModel->getPageLimit();
                $query.=" LIMIT $startIndex,".($pageLimit);
                $result = $db->pquery($query, $params);
                while ($row = $db->fetch_array($result)) {
                    //是否是开户的充值单
                    $query = 'SELECT
                            1
                        FROM
                            vtiger_refillapplication
                        LEFT JOIN vtiger_rechargesheet ON vtiger_rechargesheet.refillapplicationid = vtiger_refillapplication.refillapplicationid
                        LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_refillapplication.refillapplicationid
                        WHERE
                            vtiger_crmentity.deleted = 0
                        AND vtiger_refillapplication.vendorid =?         
                        AND vtiger_rechargesheet.did =?';
                    $thisResult = $db->pquery($query, array($row['vendorid'], $row['idaccount']));
                    $rechargetypedetail = 'OpenAnAccount';
                    if ($db->num_rows($thisResult)) {
                        $rechargetypedetail = 'renew';
                    }
                    $row['rechargetypedetail'] = $rechargetypedetail;
                    //超期合同不能选用
                    $query = 'SELECT 1 FROM vtiger_suppliercontracts LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_suppliercontracts.suppliercontractsid WHERE vtiger_suppliercontracts.suppliercontractsid=? AND vtiger_crmentity.deleted=0';
                    $datetime=date('Y-m-d');
                    $query.= " AND ((vtiger_suppliercontracts.modulestatus = 'c_complete' AND vtiger_suppliercontracts.effectivetime>='{$datetime}') OR (vtiger_suppliercontracts.isguarantee = 1 AND (vtiger_suppliercontracts.effectivetime IS NULL OR vtiger_suppliercontracts.effectivetime='' OR vtiger_suppliercontracts.effectivetime>='{$datetime}') AND vtiger_suppliercontracts.modulestatus IN('a_normal','b_check','b_actioning','c_stamp','c_recovered','c_receive')))";
                    $thisResult = $db->pquery($query, array($row['suppliercontractsid']));
                    if ($db->num_rows($thisResult) == 0) {
                        continue;
                    }
                    if($_REQUEST['rechargesource']=='COINRETURN'){
                        $trow=array();
                        $trow["accountid"]=$row["accountid"];
                        $trow["accountplatform"]=$row["accountzh"];
                        $trow["accountplatformid"]=$row["productproviderid"];
                        $trow["accountrebate"]=$row["accountrebate"];//客户返点
                        $trow["effectiveendaccount"]=$row["serviceenddate"];
                        $trow["effectivestartaccount"]=$row["servicestartdate"];
                        $trow["idaccount"]=$row["idaccount"];
                        $trow["modulestatus"]=$row["modulestatus"];
                        $trow["supplierrebate"]=$row["supplierrebate"];//供应商返点
                        $trow["topplatform"]=$row["productname"];
                        $trow["productid"]=$row["productid"];
                        $trow["vendorid"]=$row["vendorid"];
                        $trow["accountrebatetype"]=$row["accountrebatetype"];
                        $trow["rechargetypedetail"]=$rechargetypedetail;
                        $trow["customeroriginattr"]=$row["customeroriginattr"];;
                        $trow["isprovideservice"]=$row["isprovideservice"];
                        $trow["didcount"]='';
                        $trow["rebatetype"]=$row["rebatetype"];
                        $return[] = $trow;
                    }else{
                        $return[] = $row;
                    }
                }
            }else{
                $searchdidSQL='';
                $params=array($recordId);
                if(!empty($searchdid) && $searchdid!=''){
                    $params[]='%'.$searchdid.'%';
                    $searchdidSQL=' AND vtiger_accountplatform_detail.idaccount like ?';
                }
                $query = "SELECT 
                      vtiger_accountplatform.accountid,
                      vtiger_accountplatform_detail.accountplatform,
                      vtiger_accountplatform.accountplatformid,
                      vtiger_accountplatform.accountrebate,
                      vtiger_accountplatform.effectiveendaccount,
                      vtiger_accountplatform.effectivestartaccount,
                      vtiger_accountplatform_detail.idaccount,
                      vtiger_accountplatform.customeroriginattr,
                      vtiger_accountplatform.accountrebatetype,
                      vtiger_accountplatform.isprovideservice,
                      IFNULL(vtiger_accountplatform.rebatetype,'GoodsBack') AS rebatetype,
                      vtiger_accountplatform.modulestatus,
                      vtiger_accountplatform.supplierrebate,
                      (SELECT vtiger_products.productname FROM `vtiger_products` WHERE vtiger_products.productid=vtiger_accountplatform.productid) as topplatform,
                      vtiger_accountplatform.productid,
                      IF((SELECT
                            1
                        FROM
                            vtiger_refillapplication
                        LEFT JOIN vtiger_rechargesheet ON vtiger_rechargesheet.refillapplicationid = vtiger_refillapplication.refillapplicationid
                        LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_refillapplication.refillapplicationid
                        WHERE
                            vtiger_crmentity.deleted = 0
                        AND vtiger_refillapplication.accountid =vtiger_accountplatform.accountid
                        AND vtiger_rechargesheet.did =vtiger_accountplatform.idaccount limit 1)=1,1,0) AS rechargetypedetail,
                      vtiger_accountplatform.vendorid
                FROM vtiger_accountplatform 
                LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_accountplatform.accountplatformid
                LEFT JOIN vtiger_accountplatform_detail ON  vtiger_accountplatform_detail.accountplatformid=vtiger_accountplatform.accountplatformid
                WHERE vtiger_crmentity.deleted=0 
                AND vtiger_accountplatform.modulestatus='c_complete'
                AND vtiger_accountplatform.isforbidden=0
                AND vtiger_accountplatform.accountid=?".$searchdidSQL;
                if($this->get('orderby')){
                    if($this->get('sortorder')=='accountplatform'){
                        $query.=" order by vtiger_accountplatform_detail.accountplatform ".$this->get('sortorder');
                    }else{
                        $query.=" order by vtiger_accountplatform.idaccount ".$this->get('sortorder');
                    }
                }
                $startIndex = $pagingModel->getStartIndex();
                $pageLimit = $pagingModel->getPageLimit();
                $query.=" LIMIT $startIndex,".($pageLimit);
                $result = $db->pquery($query, $params);
                while ($row = $db->fetch_array($result)) {
                    $rechargetypedetail='OpenAnAccount';
                    if($row['rechargetypedetail']==1){
                        $rechargetypedetail='renew';
                    }
                    $tmp['accountid']=$row['accountid'];
                    $tmp['accountplatform']=$row['accountplatform'];
                    $tmp['accountplatformid']=$row['accountplatformid'];
                    $tmp['accountrebate']=$row['accountrebate'];
                    $tmp['effectiveendaccount']=$row['effectiveendaccount'];
                    $tmp['effectivestartaccount']=$row['effectivestartaccount'];
                    $tmp['idaccount']=$row['idaccount'];
                    $tmp['modulestatus']=$row['modulestatus'];
                    $tmp['supplierrebate']=$row['supplierrebate'];
                    $tmp['topplatform']=$row['topplatform'];
                    $tmp['productid']=$row['productid'];
                    $tmp['vendorid']=$row['vendorid'];
                    $tmp['accountrebatetype']=$row['accountrebatetype'];
                    $tmp['rechargetypedetail']=$rechargetypedetail;
                    $tmp['customeroriginattr']=$row['customeroriginattr'];
                    $tmp['isprovideservice']=$row['isprovideservice'];
                    $tmp['didcount']=$row['didcount'];
                    $tmp['rebatetype']=$row['rebatetype'];
                    $return[] = $tmp;
                }
            }
        }
        return $return;
    }

    public function getUserWhere(){
        $listQuery='';
		$searchDepartment = $_REQUEST['department'];
        $where=getAccessibleUsers('RefillApplication','List',true);

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
            $newServiceContractsStr=getAccessibleCompany('','ServiceContracts',true,$record=-1,'X-X-X',true);
            $newSupplierContractsStr=getAccessibleCompany('','SupplierContracts',true,$record=-1,'X-X-X',true);
            //$listQuery .= " and (vtiger_crmentity.smownerid in(".$user.") OR IF((vtiger_refillapplication.rechargesource = 'NonMediaExtraction' OR vtiger_refillapplication.rechargesource='Vendors' OR vtiger_refillapplication.rechargesource='TECHPROCUREMENT' OR vtiger_refillapplication.rechargesource='PreRecharge'),(vtiger_suppliercontracts.companycode ".$newSupplierContractsStr."),(vtiger_servicecontracts.companycode ".$newServiceContractsStr.")))";
            $listQuery .= " and (vtiger_crmentity.smownerid in(".$user.") OR (vtiger_refillapplication.rechargesource in('NonMediaExtraction','Vendors','TECHPROCUREMENT','PreRecharge') and vtiger_suppliercontracts.companycode ".$newSupplierContractsStr.")OR (vtiger_refillapplication.rechargesource in('Accounts','OtherProcurement','PACKVENDORS','COINRETURN','INCREASE','old','contractChanges') and vtiger_servicecontracts.companycode ".$newServiceContractsStr."))";
        }
        //追加以下条件(针对移动crm) 2017/02/28 gaocl add
        //$listQuery .= ' and vtiger_rechargesheet.deleted=0 and vtiger_rechargesheet.isentity=1';

        // cxh 修改成 不等于contractChanges（即如果是 合同 申请单）
        if($_REQUEST['rechargesource']!='contractChanges'){
            //$listQuery .= ' and vtiger_rechargesheet.deleted=0 AND  vtiger_refillapplication.rechargesource <> "contractChanges" ';'Accounts',
            $listQuery .= " and vtiger_rechargesheet.deleted=0 AND  vtiger_refillapplication.rechargesource in('NonMediaExtraction','Accounts','Vendors','PreRecharge','TECHPROCUREMENT','OtherProcurement','PACKVENDORS','COINRETURN','INCREASE','old')";
        }
        $rechargesourceArray=array('nonmediaextraction','accounts','vendors','prerecharge','techprocurement','otherprocurement','packvendors','coinreturn','increase','contractchanges');
        $flag=false;
        if(!empty($_REQUEST['rechargesource'])){
            $rechargesource=strtolower($_REQUEST['rechargesource']);
            if(in_array($rechargesource,$rechargesourceArray)){
                $flag = true;
            }
        }
        if($flag){
            $rechargesourceArrayTmp=array('nonmediaextraction'=>'NonMediaExtraction','accounts'=>'Accounts','vendors'=>'Vendors','prerecharge'=>'PreRecharge','techprocurement'=>'TECHPROCUREMENT','otherprocurement'=>'TECHPROCUREMENT','packvendors'=>'PACKVENDORS','coinreturn'=>'COINRETURN','increase'=>'INCREASE','contractchanges'=>'contractChanges');
            $listQuery.=' and vtiger_refillapplication.rechargesource=\''.$rechargesourceArrayTmp[$rechargesource].'\'';
        }
        return $listQuery;
    }

    public function getListViewHeaders() {
        $sourceModule = $this->get('src_module');
        $queryGenerator = $this->get('query_generator');
        if(!empty($sourceModule)){
            if($sourceModule=='Rechargesheet'){
                //如果是选择id 2021.2.3更新
                return  array('ID'=>'idaccount','账户名称'=>'accountplatform');
            }
            return $queryGenerator->getModule()->getPopupFields();
        }else{
            $rechargesourceListFields=array(
                'nonmediaextraction'=>array('refillapplicationno','workflowsid','workflowstime','rechargefinishtime','financialstate','iscushion','srcterminal','workflowsnode','voidreason','voiduserid','voiddatetime','rechargesource','modifiedtime','modifiedby','modulestatus','createdtime','smownerid','assigned_user_id','servicecontractsid','accountid','totalrecharge','totalreceivables','expcashadvances','iscontracted','servicesigndate','grossadvances','file','remarks','vendorid','bankaccount','bankname','banknumber','bankcode','suppliercontractsid','havesignedcontract','signdate','productid','purchaseamount','totalgrossprofit','actualtotalrecharge','paymentperiod','ispayment','paymentdate','nonaccountrebate','nonaccountrebatetype','grossadvances'),
                'accounts'=>array('refillapplicationno','workflowsid','workflowstime','rechargefinishtime','financialstate','iscushion','srcterminal','workflowsnode','voidreason','voiduserid','voiddatetime','rechargesource','modifiedtime','modifiedby','modulestatus','createdtime','smownerid','assigned_user_id','servicecontractsid','accountid','customertype','customeroriginattr','totalrecharge','actualtotalrecharge','expcashadvances','iscontracted','servicesigndate','grossadvances','file','remarks','did','accountzh','productid','isprovideservice','rechargetypedetail','receivementcurrencytype','exchangerate','prestoreadrate','rechargeamount','discount','tax','factorage','activationfee','taxation','totalcost','transferamount','servicecost','totalgrossprofit','mstatus','rebatetype','accountrebatetype','supprebate','flow_state','receivedstatus'),
                'vendors'=>array('totalreceivables','refillapplicationno','workflowsid','workflowstime','rechargefinishtime','financialstate','iscushion','srcterminal','workflowsnode','voidreason','voiduserid','voiddatetime','rechargesource','modifiedtime','modifiedby','modulestatus','createdtime','smownerid','assigned_user_id','servicecontractsid','accountid','customertype','customeroriginattr','totalrecharge','actualtotalrecharge','expcashadvances','iscontracted','servicesigndate','grossadvances','file','remarks','vendorid','bankaccount','bankname','banknumber','bankcode','suppliercontractsid','havesignedcontract','signdate','did','accountzh','productid','isprovideservice','rechargetypedetail','receivementcurrencytype','exchangerate','prestoreadrate','rechargeamount','discount','tax','factorage','activationfee','taxation','totalcost','transferamount','servicecost','totalgrossprofit','mstatus','rebatetype','accountrebatetype','paymentperiod','ispayment','supprebate','paymentdate'),
                'prerecharge'=>array('totalreceivables','refillapplicationno','workflowsid','workflowstime','rechargefinishtime','financialstate','iscushion','srcterminal','workflowsnode','voidreason','voiduserid','voiddatetime','rechargesource','modifiedtime','modifiedby','modulestatus','createdtime','smownerid','assigned_user_id','vendorid','bankaccount','bankname','banknumber','bankcode','suppliercontractsid','havesignedcontract','signdate','productid','prestoreadrate','rechargeamount','discount','rebates','mstatus','rebatetype','remarks','paymentperiod','ispayment','paymentdate'),
                'techprocurement'=>array('refillapplicationno','workflowsid','workflowstime','rechargefinishtime','financialstate','iscushion','srcterminal','workflowsnode','voidreason','voiduserid','voiddatetime','rechargesource','modifiedtime','modifiedby','modulestatus','createdtime','smownerid','assigned_user_id','salesorderid','servicecontractsid','accountid','totalrecharge','totalreceivables','humancost','purchasecost','contractamount','file','remarks','vendorid','bankaccount','bankname','banknumber','bankcode','suppliercontractsid','havesignedcontract','signdate','productid','amountpayable','paymentdate'),
                'otherprocurement'=>array('refillapplicationno','workflowsid','workflowstime','rechargefinishtime','financialstate','iscushion','srcterminal','workflowsnode','voidreason','voiduserid','voiddatetime','rechargesource','modifiedtime','modifiedby','modulestatus','createdtime','smownerid','assigned_user_id','vendorid','bankaccount','bankname','banknumber','bankcode','expecteddatepayment','expectedpaymentdeadline','beardepartment','bearratio','suppliercontractsid','havesignedcontract','signdate','productid','purchaseamount','purchaseprice','purchasequantity'),
                'packvendors'=>array('refillapplicationno','workflowsid','workflowstime','modifiedtime','rechargefinishtime','financialstate','iscushion','srcterminal','workflowsnode','voidreason','voiduserid','voiddatetime','rechargesource','modifiedby','modulestatus','createdtime','smownerid','assigned_user_id','totalreceivables','vendorid','bankaccount','bankname','banknumber','bankcode','expecteddatepayment','expectedpaymentdeadline','remarks'),
                'coinreturn'=>array('refillapplicationno','workflowsid','workflowstime','modifiedtime','rechargefinishtime','financialstate','iscushion','srcterminal','workflowsnode','voidreason','voiduserid','voiddatetime','rechargesource','modifiedby','accountid','servicecontractsid','file','modulestatus','createdtime','smownerid','assigned_user_id','totalcashtransfer','totalcashin','totalturnoverofaccount','totaltransfertoaccount','did','productid','topplatform','accountzh','accountrebatetype','isprovideservice','discount','cashtransfer','accounttransfer','turninorout','vendorid','conversiontype'),
                'increase'=>array('refillapplicationno','workflowsid','workflowstime','modifiedtime','rechargefinishtime','financialstate','iscushion','srcterminal','workflowsnode','voidreason','voiduserid','voiddatetime','rechargesource','modifiedby','file','modulestatus','createdtime','smownerid','assigned_user_id','cashconsumptiontotal','cashincreasetotal','mservicecontractsid','maccountid','mservicecontractsid_name','maccountid_name','cashgift','taxrefund','cashconsumption','cashincrease','grantquarter','mstatus','discount','accountrebatetype','mstatus'),
                'contractchanges'=>array('smownerid','refillapplicationno','createdtime','assigned_user_id','modifiedtime','workflowsid','workflowsnode','modulestatus','oldrechargesource','changecontracttype','rechargesource','contractamountrecharged','changesnumber','actualtotalrecharge','totalreceivables','grossadvances','remarks','oldcontract_no','account_name','newcontract_no','newaccount_name','iscontracted','newiscontracted','servicesigndate','newservicesigndate','customertype','newcustomertype','contractamount','newcontractamount'),
                );
            $rechargesourceArray=array('nonmediaextraction','accounts','vendors','prerecharge','techprocurement','otherprocurement','packvendors','coinreturn','increase','contractchanges');
            $flag=true;
            if(!empty($_GET['rechargesource'])){
                $rechargesource=strtolower($_GET['rechargesource']);
                if(in_array($rechargesource,$rechargesourceArray)){
                    $flag = false;
                }
            }
            $list=$queryGenerator->getModule()->getListFields();
            $temp=array();
            foreach($list as $fields){
                if($flag){
                    $temp[$fields['fieldlabel']]=$fields;
                }else{
                    if(in_array($fields['fieldname'],$rechargesourceListFields[$rechargesource])){
                        $temp[$fields['fieldlabel']]=$fields;
                    }
                }
            }
            return $temp;
        }
        return $queryGenerator->getFocus()->list_fields_name;
    }
    public function getListViewCount($searchField=null) {
        if($this->get('src_module')=='Rechargesheet' && isset($_REQUEST['isProvider'])){
            return $this->getListViewDidCount($searchField);
        }
        if(0==$this->isAllCount && 0==$this->isFromMobile){
            return 0;
        }
        $db = PearDatabase::getInstance();
        $queryGenerator = $this->get('query_generator');
        $where=$this->getUserWhere();
        $queryGenerator->addUserWhere($where);
        $listQuery =  $queryGenerator->getQueryCount();
        if(!empty($searchField)){
            foreach($searchField as $k=>$v){
                if($v == 'checking'){
                    $listQuery.= " and ".$k ." in ('b_actioning', 'a_normal', 'b_check')";
                }elseif(is_array($v)){
                    if($v['search_key']=='vtiger_products.productname'){
                        $listQuery.= " and ".$v['search_key'] .$v['operator'] . $v['search_value'];
                        $listQuery.=" AND (vtiger_refillapplication.workflowsnode LIKE '财务充值%' OR vtiger_refillapplication.workflowsnode LIKE '出纳出款审核%')";
                    }else{
                        $listQuery.= " and ".$v['search_key'] .$v['operator'] . $v['search_value'];
                    }
                }else{
                    $listQuery.= " and ".$k ."='" . $v. "'";
                }

            }
        }
//        $listQuery .=' GROUP BY vtiger_refillapplication.refillapplicationid';//用分组来去重;
        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
//        return $db->num_rows($listResult);
    }


    public function getListViewDidCount($searchField){
        $db=PearDatabase::getInstance();
        $recordId=$_REQUEST['src_record'];
        $searchdid=$_REQUEST['search_value'];
        if($recordId>0) {
            if($_REQUEST['isProvider']){
                $return=array();
                $searchdidSQL='';
                $params=array($_REQUEST['src_vendor'],$recordId);
                if(!empty($searchdid) && $searchdid!=''){
                    $params[]='%'.$searchdid.'%';
                    $searchdidSQL=' AND vtiger_productprovider_detail.idaccount like ?';
                }
                $result = $db->pquery("SELECT vtiger_productprovider.`productid`,
                                  `vtiger_products`.productname,
                                  vtiger_productprovider.`vendorid`,
                                  vtiger_productprovider.`suppliercontractsid`,
                                  vtiger_productprovider.`supplierrebate`,
                                  vtiger_productprovider.`servicestartdate`,
                                  vtiger_productprovider.`serviceenddate`,
                                  vtiger_productprovider.`workflowstime`,
                                  vtiger_productprovider.`workflowsnode`,
                                  vtiger_productprovider_detail.idaccount,
                                  vtiger_productprovider.accountid,
                                  vtiger_productprovider_detail.accountzh,
                                  vtiger_productprovider.accountrebate,
                                  IFNULL(vtiger_productprovider.rebatetype,'GoodsBack') AS rebatetype,
                                  IFNULL(vtiger_productprovider.accountrebatetype,'GoodsBack') AS accountrebatetype,
                                  IFNULL(vtiger_suppliercontracts.contract_no,'担保合同') AS contract_no,
                                  vtiger_suppliercontracts.modulestatus,
                                  vtiger_productprovider.customeroriginattr,
                                  vtiger_productprovider.isprovideservice,
                                  vtiger_suppliercontracts.total,
                                  vtiger_suppliercontracts.signdate
                                FROM vtiger_productprovider 
                                LEFT JOIN vtiger_products ON vtiger_products.productid=vtiger_productprovider.productid 
                                LEFT JOIN vtiger_suppliercontracts ON vtiger_suppliercontracts.suppliercontractsid=vtiger_productprovider.`suppliercontractsid`
                                LEFT JOIN vtiger_productprovider_detail ON vtiger_productprovider_detail.productproviderid=vtiger_productprovider.productproviderid
                                WHERE
                                vtiger_productprovider.vendorid=? AND vtiger_productprovider.modulestatus='c_complete' AND vtiger_productprovider.isforbidden=0
                                AND vtiger_productprovider.accountid=? AND vtiger_productprovider.accountid>0 ".$searchdidSQL, $params);
                while ($row = $db->fetch_array($result)) {
                    //是否是开户的充值单
                    $query = 'SELECT
                            1
                        FROM
                            vtiger_refillapplication
                        LEFT JOIN vtiger_rechargesheet ON vtiger_rechargesheet.refillapplicationid = vtiger_refillapplication.refillapplicationid
                        LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_refillapplication.refillapplicationid
                        WHERE
                            vtiger_crmentity.deleted = 0
                        AND vtiger_refillapplication.vendorid =?         
                        AND vtiger_rechargesheet.did =?';
                    $thisResult = $db->pquery($query, array($row['vendorid'], $row['idaccount']));
                    $rechargetypedetail = 'OpenAnAccount';
                    if ($db->num_rows($thisResult)) {
                        $rechargetypedetail = 'renew';
                    }
                    $row['rechargetypedetail'] = $rechargetypedetail;
                    //超期合同不能选用
                    $query = 'SELECT 1 FROM vtiger_suppliercontracts LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_suppliercontracts.suppliercontractsid WHERE vtiger_suppliercontracts.suppliercontractsid=? AND vtiger_crmentity.deleted=0';
                    $datetime=date('Y-m-d');
                    $query.= " AND ((vtiger_suppliercontracts.modulestatus = 'c_complete' AND vtiger_suppliercontracts.effectivetime>='{$datetime}') OR (vtiger_suppliercontracts.isguarantee = 1 AND (vtiger_suppliercontracts.effectivetime IS NULL OR vtiger_suppliercontracts.effectivetime='' OR vtiger_suppliercontracts.effectivetime>='{$datetime}') AND vtiger_suppliercontracts.modulestatus IN('a_normal','b_check','b_actioning','c_stamp','c_recovered','c_receive')))";
                    //return array($query);
                    $thisResult = $db->pquery($query, array($row['suppliercontractsid']));
                    if ($db->num_rows($thisResult) == 0) {
                        continue;
                    }
                    if($_REQUEST['rechargesource']=='COINRETURN'){
                        $trow=array();
                        $trow["accountid"]=$row["accountid"];
                        $trow["accountplatform"]=$row["accountzh"];
                        $trow["accountplatformid"]=$row["productproviderid"];
                        $trow["accountrebate"]=$row["accountrebate"];//客户返点
                        $trow["effectiveendaccount"]=$row["serviceenddate"];
                        $trow["effectivestartaccount"]=$row["servicestartdate"];
                        $trow["idaccount"]=$row["idaccount"];
                        $trow["modulestatus"]=$row["modulestatus"];
                        $trow["supplierrebate"]=$row["supplierrebate"];//供应商返点
                        $trow["topplatform"]=$row["productname"];
                        $trow["productid"]=$row["productid"];
                        $trow["vendorid"]=$row["vendorid"];
                        $trow["accountrebatetype"]=$row["accountrebatetype"];
                        $trow["rechargetypedetail"]=$rechargetypedetail;
                        $trow["customeroriginattr"]=$row["customeroriginattr"];;
                        $trow["isprovideservice"]=$row["isprovideservice"];
                        $trow["didcount"]='';
                        $trow["rebatetype"]=$row["rebatetype"];
                        $return[] = $trow;
                    }else{
                        $return[] = $row;
                    }
                }
                return  count($return);
            }else{
                $searchdidSQL='';
                $params=array($recordId);
                if(!empty($searchdid) && $searchdid!=''){
                    $params[]='%'.$searchdid.'%';
                    $searchdidSQL=' AND vtiger_accountplatform_detail.idaccount like ?';
                }
                $query = "SELECT 
                      vtiger_accountplatform.accountid,
                      vtiger_accountplatform_detail.accountplatform,
                      vtiger_accountplatform.accountplatformid,
                      vtiger_accountplatform.accountrebate,
                      vtiger_accountplatform.effectiveendaccount,
                      vtiger_accountplatform.effectivestartaccount,
                      vtiger_accountplatform_detail.idaccount,
                      vtiger_accountplatform.customeroriginattr,
                      vtiger_accountplatform.accountrebatetype,
                      vtiger_accountplatform.isprovideservice,
                      IFNULL(vtiger_accountplatform.rebatetype,'GoodsBack') AS rebatetype,
                      vtiger_accountplatform.modulestatus,
                      vtiger_accountplatform.supplierrebate,
                      (SELECT vtiger_products.productname FROM `vtiger_products` WHERE vtiger_products.productid=vtiger_accountplatform.productid) as topplatform,
                      vtiger_accountplatform.productid,
                      IF((SELECT
                            1
                        FROM
                            vtiger_refillapplication
                        LEFT JOIN vtiger_rechargesheet ON vtiger_rechargesheet.refillapplicationid = vtiger_refillapplication.refillapplicationid
                        LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_refillapplication.refillapplicationid
                        WHERE
                            vtiger_crmentity.deleted = 0
                        AND vtiger_refillapplication.accountid =vtiger_accountplatform.accountid
                        AND vtiger_rechargesheet.did =vtiger_accountplatform.idaccount limit 1)=1,1,0) AS rechargetypedetail,
                      vtiger_accountplatform.vendorid
                FROM vtiger_accountplatform 
                LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_accountplatform.accountplatformid
                LEFT JOIN vtiger_accountplatform_detail ON  vtiger_accountplatform_detail.accountplatformid=vtiger_accountplatform.accountplatformid
                WHERE vtiger_crmentity.deleted=0 
                AND vtiger_accountplatform.modulestatus='c_complete'
                AND vtiger_accountplatform.isforbidden=0
                AND vtiger_accountplatform.accountid=?".$searchdidSQL;
                if($this->get('orderby')){
                    if($this->get('sortorder')=='accountplatform'){
                        $query.=" order by vtiger_accountplatform_detail.accountplatform ".$this->get('sortorder');
                    }else{
                        $query.=" order by vtiger_accountplatform.idaccount ".$this->get('sortorder');
                    }
                }
                $result = $db->pquery($query, $params);
                return $db->num_rows($result);
            }
        }
        return 0;
    }

}