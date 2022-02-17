<?php
/*
*定义管理语句
*/
class ServiceContracts_RelationListView_Model extends Vtiger_RelationListView_Model {
	static $relatedquerylist = array(
	'ReceivedPayments'=>'SELECT vtiger_receivedpayments.paytitle,vtiger_receivedpayments.owncompany,vtiger_receivedpayments.reality_date,vtiger_receivedpayments.unit_price,vtiger_receivedpayments.createtime, vtiger_receivedpayments.receivedpaymentsid as crmid FROM vtiger_receivedpayments WHERE vtiger_receivedpayments.deleted=0 AND vtiger_receivedpayments.relatetoid = ?',

        'ActivationCode'=>"SELECT vtiger_activationcode.activationcodeid AS crmid,vtiger_activationcode.classtype AS classtypeflag,
                        (CASE vtiger_activationcode.classtype 
                        WHEN 'upgrade' THEN '升级'
                        WHEN 'degrade' THEN '降级'
                        WHEN 'renew' THEN '续费'
                        WHEN 'againbuy' THEN '另购'
                        ELSE '首购' END
                        ) AS classtype,
                        if(vtiger_activationcode.comeformtyun=1,vtiger_activationcode.productname,IFNULL((SELECT vtiger_products.productname FROM vtiger_products WHERE vtiger_activationcode.productid=vtiger_products.tyunproductid LIMIT 1),'--')) AS productid,
                        vtiger_activationcode.productlife,
                        vtiger_activationcode.activedate,
                        vtiger_activationcode.activecode,
                        vtiger_activationcode.customername,
                        vtiger_activationcode.mobile,
                        vtiger_activationcode.expiredate,
                        vtiger_activationcode.salesname,
                        vtiger_activationcode.salesphone,
                        vtiger_activationcode.usercode,
                        vtiger_activationcode.buyserviceinfo,
                        vtiger_activationcode.buyid
                    FROM
                        vtiger_activationcode
                    LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid = vtiger_activationcode.contractid
                    WHERE
                        vtiger_activationcode.status IN(0,1)
                    AND vtiger_activationcode.contractid = ?
                    AND vtiger_activationcode.comeformtyun=0
                    ORDER BY vtiger_activationcode.activationcodeid DESC",
	'ActivationCodeDetail'=>'SELECT activecode,activetype,startdate,enddate,remark FROM `vtiger_activationcode_detail` WHERE activationcodeid IN (SELECT activationcodeid FROM `vtiger_activationcode` WHERE contractid=?)',

	'Files'=>"SELECT * from vtiger_files where relationid=? AND delflag=0",
    'Invoicesign'=>"SELECT invoicesignid as crmid,path FROM vtiger_invoicesign WHERE setype='ServiceContracts' AND vtiger_invoicesign.invoiceid=? ORDER BY crmid DESC",
    'ContractsAgreement'=>'SELECT vtiger_contractsagreement.contractsagreementid AS crmid,(vtiger_servicecontracts.contract_no) as servicecontractsid,vtiger_contractsagreement.servicecontractsid as servicecontractsid_reference, (vtiger_account.accountname) as accountid,vtiger_contractsagreement.accountid as account_id,vtiger_contractsagreement.workflowsnode,vtiger_contractsagreement.modulestatus,vtiger_crmentity.smownerid as assigned_user_id,vtiger_contractsagreement.file, (vtiger_workflows.workflowsname) as workflowsid,vtiger_contractsagreement.workflowsid as workflowsid_reference,vtiger_contractsagreement.workflowstime,vtiger_contractsagreement.receiptorid,vtiger_contractsagreement.newservicecontractsno,vtiger_contractsagreement.remarks,(select createdtime from vtiger_crmentity where vtiger_crmentity.crmid=vtiger_contractsagreement.contractsagreementid and vtiger_crmentity.deleted=0) as createdtime,(select modifiedtime from vtiger_crmentity where vtiger_crmentity.crmid=vtiger_contractsagreement.contractsagreementid and vtiger_crmentity.deleted=0) as modifiedtime,IFNULL((select CONCAT(last_name,\'[\',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),\'\'),\']\',(if(`status`=\'Active\',\'\',\'[离职]\'))) as last_name from vtiger_users where vtiger_crmentity.modifiedby=vtiger_users.id),\'--\') as modifiedby,vtiger_crmentity.modifiedby as modifiedby_reference,vtiger_contractsagreement.dateofapp,vtiger_contractsagreement.contractsagreementid FROM vtiger_contractsagreement LEFT JOIN vtiger_crmentity ON vtiger_contractsagreement.contractsagreementid = vtiger_crmentity.crmid LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid=vtiger_contractsagreement.servicecontractsid LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_contractsagreement.accountid LEFT JOIN vtiger_workflows ON vtiger_workflows.workflowsid=vtiger_contractsagreement.workflowsid WHERE vtiger_crmentity.deleted=0 AND vtiger_contractsagreement.servicecontractsid =?',
    'TyunStationSale'=>"SELECT companyname,agentcode,serviceinfo,signaddress,IFNULL(signdate,'--') AS signdate,custphone, loginname, IFNULL(opendate,'--') AS opendate, IFNULL(finnishdate,'--') AS finnishdate, salesname, salesphone, serviceloginname, stationsaleid as crmid FROM vtiger_tyunstationsale WHERE	contractid = ? ORDER BY stationsaleid DESC",
    'Vmatefiles'=>"SELECT * from vtiger_vmatefiles where relationid=? AND delflag=0",
    );

	public function getEntries($pagingModel){
		$relatedModuleName=$_REQUEST['relatedModule'];
		$moduleName = $_REQUEST['module'];
		$relatedquerylist=self::$relatedquerylist;

		if($relatedModuleName == 'Files') {
			$relatedquerylist[$relatedModuleName] .= " AND description='$moduleName' ";
		}

        if($relatedModuleName == 'Vmatefiles') {
            $relatedquerylist[$relatedModuleName] .= " AND description='$moduleName' ";
        }
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

        $relatedModuleName=$_REQUEST['relatedModule'];

        $arr_servicetype = array(
            '1'=>'云建站3.0小程序标准建站',
            '2'=>'云建站3.0PC标准建站',
            '3'=>'云建站3.0移动标准建站'
        );

		for($i=0; $i< $db->num_rows($result); $i++ ) {
			$row = $db->fetch_row($result,$i);

            if($relatedModuleName == 'TyunStationSale') {
                $serviceinfo = htmlspecialchars_decode($row['serviceinfo']);
                //$serviceinfo = str_replace('&quot;','"',$row['serviceinfo']);
                $arr_serviceinfo = json_decode($serviceinfo,true);
                $buyContent = "";
                for($a=0;$a<count($arr_serviceinfo);$a++){
                    $count = $arr_serviceinfo[$a]['count'];
                    $servicetype = $arr_serviceinfo[$a]['servicetype'];
                    $year = $arr_serviceinfo[$a]['year'];
                    if($count > 0){
                        $buyContent .=  '购买服务：'. $arr_servicetype[$servicetype] .', 购买数量：'. $count .'个, 购买时长：'.$year .'年<br>';
                    }
                }
                $row['serviceinfo'] = $buyContent;
            }
            if($relatedModuleName == 'ActivationCode') {
                $classtype = $row['classtypeflag'];
                $activationcodeid = $row['crmid'];

                if($classtype != 'buy'){

                    $sql="SELECT
                        M.activationcodeid AS crmid,
                        (CASE M.classtype 
                        WHEN 'upgrade' THEN '升级'
                        WHEN 'renew' THEN '续费'
                        WHEN 'againbuy' THEN '另购'
                        ELSE '首购' END
                        ) AS classtype,
                      (SELECT MAX(vtiger_products.productname) FROM vtiger_products WHERE vtiger_products.tyunproductid=
                      (IF(LENGTH(M.productid)=0 OR ISNULL(M.productid),
                      (SELECT MM.productid FROM vtiger_activationcode MM WHERE MM.status IN(0,1) AND (MM.buyid=M.buyid OR MM.activationcodeid=M.buyid) AND MM.classtype IN('buy','upgrade','degrade') ORDER BY MM.receivetime DESC LIMIT 1)
                      ,M.productid))) AS productid,
                        M.productlife,
                        P.activedate,
                        P.activecode,
                        P.customername,
                        P.mobile,
                        P.expiredate,
                        P.salesname,
                        P.salesphone,
                        P.usercode,
                        M.buyserviceinfo,
                        M.buyid
                        FROM vtiger_activationcode M
                        LEFT JOIN vtiger_activationcode P ON(M.buyid=P.activationcodeid)
                        WHERE M.status IN(0,1) AND M.activationcodeid =? LIMIT 1";

                    $info=$db->pquery($sql,array($activationcodeid));
                    $row = $db->query_result_rowdata($info,0);
                }
            }
			$record = Vtiger_Record_Model::getCleanInstance($relationModule->get('name'));

            $record->setData($row)->setModuleFromInstance($relationModule);
            if (empty($row['crmid'])) {

            	if ($_REQUEST['relatedModule'] == 'Files') {
            		$record->setId($row['attachmentsid']);
            		$row['crmid'] = $row['attachmentsid'];
            	}
                if ($_REQUEST['relatedModule'] == 'Vmatefiles') {
                    $record->setId($row['vmateattachmentsid']);
                    $row['crmid'] = $row['vmateattachmentsid'];
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

	// 根据后缀名 返回文件类型
	/*public function getFileType($flie_name) {
		$tt = array(
			'txt'=>'文本',
			'doc'=>'word',
			'docx'=>'word',
			'jpg'=>'图片',
			'gif'=>'图片',
			'png'=>'图片',
			'rar'=>'rar压缩包',
			'zip'=>'zip压缩包',
			'pdf'=>'pdf文档',
			'mp3'=>'mp3',
			'sql'=>'数据库文件',
			'xlsx'=>'execl'
		);

		$aa = explode('.', $flie_name);
		if (count($aa) > 1) {
			$b = strtolower($aa[count($aa) - 1]);
			return $tt[$b] ? $tt[$b] : $b;
		}
		return '';
	}*/
}