<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Newinvoice_ListView_Model extends Vtiger_ListView_Model {
	//根据参数显示数据
	public function getListViewEntries($pagingModel) {
	    //根据订单系统获取
        if($this->get('src_field')=='systemuser'){
            return $this->getSystemUserEntriesOrCount($pagingModel,'Entries');
        }
		$db = PearDatabase::getInstance();
		$moduleName ='Newinvoice';


		$orderBy = $this->getForSql('orderby');
		$sortOrder = $this->getForSql('sortorder');

		//List view will be displayed on recently created/modified records
		//列表视图将显示最近的创建修改记录  ---做什么用处
		if(empty($orderBy) && empty($sortOrder)){

			$orderBy = 'vtiger_newinvoice.invoiceid';
            //$orderBy = 'vtiger_crmentity.modifiedtime';
			$sortOrder = 'DESC';
		}

        //合同是以ID显示的此处更改为显示合同号码
        /* $listQuery="SELECT
                        (SELECT CONCAT(last_name,'[',IFNULL((SELECT departmentname FROM vtiger_departments WHERE departmentid = (SELECT	departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ),''),']',(IF (`status` = 'Active','','[离职]'))) AS last_name FROM vtiger_users WHERE vtiger_invoice.receiveid = vtiger_users.id) AS receiveid,
                        vtiger_invoice.receivedate,
                        vtiger_invoice.remark,
                        (SELECT	workflowsname FROM vtiger_workflows WHERE vtiger_workflows.workflowsid = vtiger_invoice.workflowsid ) AS workflowsid,
                        vtiger_invoice.workflowsid AS workflowsid_reference,
                        vtiger_invoice.businessnamesone,
                        (SELECT GROUP_CONCAT(label) FROM vtiger_crmentity WHERE vtiger_crmentity.crmid IN ( REPLACE ( vtiger_invoice.accountid, ' |##| ', ','))) AS accountid,
                        vtiger_invoice.invoice_no,
                        vtiger_invoice.billingtime,
                        (SELECT CONCAT(last_name, '[',IFNULL((SELECT departmentname FROM vtiger_departments WHERE departmentid = (SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1)),''),']',(IF (`status` = 'Active','','[离职]'))) AS last_name FROM vtiger_users WHERE vtiger_invoice.drawer = vtiger_users.id) AS drawer,
                        IFNULL((SELECT contract_no FROM vtiger_servicecontracts WHERE vtiger_servicecontracts.servicecontractsid=vtiger_invoice.contractid),'--') AS contractid,
                        vtiger_invoice.invoicecompany,
                        vtiger_invoice.taxtype,
                        IFNULL((SELECT CONCAT(last_name,'[',IFNULL((SELECT departmentname FROM vtiger_departments WHERE departmentid = (SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1)),''),']',(IF (`status` = 'Active','','[离职]'))) AS last_name FROM vtiger_users WHERE vtiger_crmentity.smownerid = vtiger_users.id),'--') AS smownerid,
                        vtiger_crmentity.smownerid AS smownerid_owner,
                        vtiger_invoice.accountnumber,
                        vtiger_invoice.companyname,
                        vtiger_invoice.taxrate,
                        vtiger_invoice.taxpayers_no,
                        vtiger_invoice.taxtotal,
                        vtiger_invoice.depositbank,
                        vtiger_invoice.telephone,
                        vtiger_invoice.registeraddress,
                      IF (isformtable = 1, '是', '否') AS isformtable,
                     vtiger_invoice.trialtime,
                     vtiger_invoice.invoicecode,
                     vtiger_invoice.businessnames,
                     vtiger_invoice.tax,
                     vtiger_invoice.totalandtax,
                     vtiger_invoice.amountofmoney,
                     vtiger_invoice.commodityname,
                     vtiger_invoice.modulestatus,
                     vtiger_invoice.workflowstime,
                     vtiger_invoice.workflowsnode,
                     vtiger_invoice.file,
                     vtiger_invoice.invoicestatus,
                     vtiger_invoice.invoiceid
                    FROM
                        vtiger_invoice
                    LEFT JOIN vtiger_crmentity ON vtiger_invoice.invoiceid = vtiger_crmentity.crmid
                    LEFT JOIN vtiger_invoicebillads ON vtiger_invoice.invoiceid = vtiger_invoicebillads.invoicebilladdressid
                    LEFT JOIN vtiger_invoiceshipads ON vtiger_invoice.invoiceid = vtiger_invoiceshipads.invoiceshipaddressid
                    LEFT JOIN vtiger_invoicecf ON vtiger_invoice.invoiceid = vtiger_invoicecf.invoiceid
                    LEFT JOIN vtiger_inventoryproductrel ON vtiger_invoice.invoiceid = vtiger_inventoryproductrel.id
                    WHERE
                        1 = 1
                    AND vtiger_crmentity.deleted = 0";
        $this->getSearchWhere();
        $listQuery.=$this->getUserWhere();

        global $current_user;
        $queryGenerator = $this->get('query_generator');
        $searchwhere=$queryGenerator->getSearchWhere();
        if(!empty($searchwhere)){
            $listQuery.=' and '.$searchwhere;
        } */

        $this->getSearchWhere();
        $listQuery = $this->getQuery();
        //echo $listQuery;
        //echo "<hr>";

        $listQuery.=$this->getUserWhere();

		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();
        $listQuery=$this->replace_SQL($listQuery);
        //去重复的记录效率太低了
        //$listQuery.=' AND 1>(select count(1) from vtiger_invoiceextend vtiger_invoiceextendaliax where vtiger_invoiceextendaliax.invoiceid=vtiger_invoiceextend.invoiceid and vtiger_invoiceextendaliax.invoiceextendid>vtiger_invoiceextend.invoiceextendid)';
        $listQuery .= ' GROUP BY vtiger_newinvoice.invoiceid';
        $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;
		$viewid = ListViewSession::getCurrentView($moduleName);

		ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,

		$listQuery .= " LIMIT $startIndex,".($pageLimit);

        //echo $listQuery;die();

		$listResult = $db->pquery($listQuery, array());


		$index = 0;
		while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['invoiceid'];
            $rawData['invoicefileid'] =explode('##',$rawData['invoicefile'])[1];
            $rawData['fileid'] = base64_encode($rawData['invoicefileid']);
			$listViewRecordModels[$rawData['invoiceid']] = $rawData;

		}
        //print_r($listViewRecordModels);
       	return $listViewRecordModels;
	}

    /**
     * 匹配SQL
     * @param $listQuery
     * @return mixed
     */
    public function replace_SQL($listQuery){
        $search=array(
            'vtiger_newinvoice.drawer=',
            'vtiger_newinvoice.billingtime,',
            'vtiger_newinvoice.invoicecode,',
            'vtiger_newinvoice.invoice_no,',
            'vtiger_newinvoice.businessnames,',
            'vtiger_newinvoice.taxrate,',
            'vtiger_newinvoice.commodityname,',
            'vtiger_newinvoice.totalandtax,',
            'vtiger_newinvoice.remark,',
            'vtiger_newinvoice.amountofmoney,',
            'vtiger_newinvoice.tax,',
            'LEFT JOIN vtiger_account',
            'vtiger_newinvoice.modulestatus,',
            '(vtiger_account.accountname)',
            'LEFT JOIN vtiger_servicecontracts',
            '(vtiger_servicecontracts.contract_no)'
        );
        $replace=array(
            'vtiger_newinvoiceextend.drawerextend=',
            'vtiger_newinvoiceextend.billingtimeextend AS billingtime,',
            'vtiger_newinvoiceextend.invoicecodeextend AS invoicecode,',
            'vtiger_newinvoiceextend.invoice_noextend AS invoice_no,',
            'vtiger_newinvoiceextend.businessnamesextend AS businessnames,',
            'vtiger_newinvoiceextend.taxrateextend AS taxrate,',
            'vtiger_newinvoiceextend.commoditynameextend AS commodityname,',
            'vtiger_newinvoiceextend.totalandtaxextend AS totalandtax,',
            'vtiger_newinvoiceextend.remarkextend AS remark,',
            'vtiger_newinvoiceextend.amountofmoneyextend AS amountofmoney,',
            'vtiger_newinvoiceextend.taxextend AS tax,',
            'LEFT JOIN vtiger_newinvoiceextend ON vtiger_newinvoiceextend.invoiceid=vtiger_newinvoice.invoiceid LEFT JOIN vtiger_account',
            'vtiger_newinvoice.modulestatus,vtiger_servicecontracts.modulestatus as smodulestatus,',
            '(SELECT accountcrm.label FROM vtiger_crmentity AS accountcrm WHERE accountcrm.crmid=vtiger_newinvoice.accountid LIMIT 1)',
            'LEFT JOIN vtiger_suppliercontracts ON vtiger_suppliercontracts.suppliercontractsid = vtiger_newinvoice.contractid LEFT JOIN vtiger_servicecontracts',
            '(SELECT contractcrm.label FROM vtiger_crmentity AS contractcrm WHERE contractcrm.crmid=vtiger_newinvoice.contractid LIMIT 1)'

        );
        //列表匹配多发票显示
        $listQuery=str_replace($search,$replace,$listQuery);
        $search=array(
            'vtiger_newinvoice.drawer ',
            'vtiger_newinvoice.billingtime ',
            'vtiger_newinvoice.invoicecode ',
            'vtiger_newinvoice.invoice_no ',
            'vtiger_newinvoice.businessnames ',
            'vtiger_newinvoice.taxrate ',
            'vtiger_newinvoice.commodityname ',
            'vtiger_newinvoice.totalandtax ',
            'vtiger_newinvoice.remark ',
            'vtiger_newinvoice.amountofmoney ',
            'vtiger_newinvoice.tax ',
        );
        $replace=array(
            'vtiger_newinvoiceextend.drawerextend ',
            'vtiger_newinvoiceextend.billingtimeextend ',
            'vtiger_newinvoiceextend.invoicecodeextend ',
            'vtiger_newinvoiceextend.invoice_noextend ',
            'vtiger_newinvoiceextend.businessnamesextend ',
            'vtiger_newinvoiceextend.taxrateextend ',
            'vtiger_newinvoiceextend.commoditynameextend ',
            'vtiger_newinvoiceextend.totalandtaxextend ',
            'vtiger_newinvoiceextend.remarkextend ',
            'vtiger_newinvoiceextend.amountofmoneyextend ',
            'vtiger_newinvoiceextend.taxextend ',

        );
        //处理搜索
        $listQuery=str_replace($search,$replace,$listQuery);
        //红冲发票
        $search=array(
            'vtiger_newinvoice.negativeinvoicecode,',
            'vtiger_newinvoice.negativeinvoice_no,',
            'vtiger_newinvoice.negativebusinessnames,',
            'vtiger_newinvoice.negativedrawer=',
            'vtiger_newinvoice.negativebillingtime,',
            'vtiger_newinvoice.negativecommodityname,',
            'vtiger_newinvoice.negativeamountofmoney,',
            'vtiger_newinvoice.negativetaxrate,',
            'vtiger_newinvoice.negativetotalandtax,',
            'vtiger_newinvoice.negativeremark,',
            'vtiger_newinvoice.negativetax,',
            'LEFT JOIN vtiger_account',
        );
        $replace=array(
            'vtiger_newnegativeinvoice.negativeinvoicecodeextend AS negativeinvoicecode,',
            'vtiger_newnegativeinvoice.negativeinvoice_noextend AS negativeinvoice_no,',
            'vtiger_newnegativeinvoice.negativebusinessnamesextend AS negativebusinessnames,',
            'vtiger_newnegativeinvoice.negativedrawerextend=',
            'vtiger_newnegativeinvoice.negativebillingtimerextend AS negativebillingtime,',
            'vtiger_newnegativeinvoice.negativecommoditynameextend AS negativecommodityname,',
            'vtiger_newnegativeinvoice.negativeamountofmoneyextend AS negativeamountofmoney,',
            'vtiger_newnegativeinvoice.negativetaxrateextend AS negativetaxrate,',
            'vtiger_newnegativeinvoice.negativetotalandtaxextend AS negativetotalandtax,',
            'vtiger_newnegativeinvoice.negativeremarkextend AS negativeremark,',
            'vtiger_newnegativeinvoice.negativetaxextend AS negativetax,',
            'LEFT JOIN vtiger_vendor ON vtiger_vendor.vendorid = vtiger_newinvoice.accountid LEFT JOIN vtiger_newnegativeinvoice ON vtiger_newinvoiceextend.invoiceextendid=vtiger_newnegativeinvoice.invoiceextendid LEFT JOIN vtiger_account',
        );
        $listQuery=str_replace($search,$replace,$listQuery);
        $search=array(
            'vtiger_newinvoice.negativeinvoicecode',
            'vtiger_newinvoice.negativeinvoice_no',
            'vtiger_newinvoice.negativebusinessnames',
            'vtiger_newinvoice.negativedrawer',
            'vtiger_newinvoice.negativebillingtime',
            'vtiger_newinvoice.negativecommodityname',
            'vtiger_newinvoice.negativeamountofmoney',
            'vtiger_newinvoice.negativetaxrate',
            'vtiger_newinvoice.negativetotalandtax',
            'vtiger_newinvoice.negativeremark',
            'vtiger_newinvoice.negativetax',
        );
        //匹配搜索
        $replace=array(
            'vtiger_newnegativeinvoice.negativeinvoicecodeextend',
            'vtiger_newnegativeinvoice.negativeinvoice_noextend',
            'vtiger_newnegativeinvoice.negativebusinessnamesextend',
            'vtiger_newnegativeinvoice.negativedrawerextend',
            'vtiger_newnegativeinvoice.negativebillingtimerextend',
            'vtiger_newnegativeinvoice.negativecommoditynameextend',
            'vtiger_newnegativeinvoice.negativeamountofmoneyextend',
            'vtiger_newnegativeinvoice.negativetaxrateextend',
            'vtiger_newnegativeinvoice.negativetotalandtaxextend',
            'vtiger_newnegativeinvoice.negativeremarkextend',
            'vtiger_newnegativeinvoice.negativetaxextend',
        );
        $listQuery=str_replace($search,$replace,$listQuery);
        $pattern='/\(vtiger_servicecontracts.contract_no(?!,)/';
        $listQuery=preg_replace($pattern,'vtiger_newinvoice.contractid IN(SELECT crm2.crmid FROM vtiger_crmentity AS crm2 WHERE crm2.setype in(\'ServiceContracts\',\'SupplierContracts\') AND crm2.deleted=0 AND crm2.label',$listQuery);
        $listQuery=str_replace('AND vtiger_servicecontracts.contract_no IS NOT NULL','',$listQuery);
        $pattern='/\(vtiger_account.accountname(?!,)/';
        $listQuery=preg_replace($pattern,'vtiger_account.accountid IN(SELECT crm3.crmid FROM vtiger_crmentity AS crm3 WHERE crm3.setype in(\'Accounts\',\'Vendors\') AND crm3.deleted=0 AND crm3.label',$listQuery);
        $listQuery=str_replace('AND vtiger_account.accountname IS NOT NULL','',$listQuery);

        return $listQuery;
    }

    public function getListViewHeaders() {
        $sourceModule = $this->get('src_module');
        $queryGenerator = $this->get('query_generator');
        if(!empty($sourceModule)){
            if($this->get('src_field')=='systemuser'){
                return  array('id'=>'id','用户账号'=>'loginName');
            }
           return $queryGenerator->getModule()->getPopupFields();
        }else{
            $list=$queryGenerator->getModule()->getListFields();
            $temp=array();
            foreach($list as $fields){
                $temp[$fields['fieldlabel']]=$fields;
            }
            $temp['sModulestatus']=array('tabid'=>'23','fieldid'=>'2260','columnname'=>'smodulestatus','tablename'=>'vtiger_servicecontracts','generatedtype'=>'1','uitype'=>'16','fieldname'=>'smodulestatus','fieldlabel'=>'sModulestatus','readonly'=>'1','presence'=>'2','defaultvalue'=>'','maximumlength'=>'100','sequence'=>'11','block'=>'71','displaytype'=>'2','typeofdata'=>'V~O','quickcreate'=>'1','quickcreatesequence'=>'','info_type'=>'BAS','masseditable'=>'1','helpinfo'=>'','summaryfield'=>'0','ismultiple'=>'0','listpresence'=>'30','fieldtype'=>'picklist','searchtype'=>'0','reltablename'=>'','reltablefield'=>'','relfieldmodule'=>'','reltablecol'=>'','isshowfield'=>'0','listtabid'=>'','reldefaultfield'=>'','relentityidfield'=>'','relleftjoin'=>'');
            return $temp;
        }
        return $queryGenerator->getFocus()->list_fields_name;

    }
    public function getUserWhere(){
       global $current_user;
        $searchDepartment = $_REQUEST['department'];
        $sourceModule = $this->get('src_module');
        $listQuery=' ';
        //$invoicecompany=' OR EXISTS(SELECT 1 FROM vtiger_invoicecompanyuser WHERE vtiger_invoicecompanyuser.modulename=\'fp\' AND vtiger_newinvoice.companycode=vtiger_invoicecompanyuser.invoicecompany AND vtiger_invoicecompanyuser.userid='.$current_user->id.')';
        $invoicecompany=' OR '.getAccessibleCompany('vtiger_newinvoice.companycode','Newinvoice');
        if(!empty($searchDepartment)&&$searchDepartment!='H1'){  //20150525 柳林刚 加入
            $userid=getDepartmentUser($searchDepartment);
            $where=getAccessibleUsers('','',true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $where=!empty($where)?$where:array(-1);
            $listQuery .= ' and (vtiger_crmentity.smownerid in ('.implode(',',$where).')'.$invoicecompany.')';
        }else{
            $where=getAccessibleUsers();
            if($where!='1=1'){
                $listQuery .= ' and (vtiger_crmentity.smownerid '.$where.$invoicecompany.')';

            }
        }

        return $listQuery;
        //return '';
    }
    public function getListViewCount() {
        if($_REQUEST['src_field']=='systemuser'){
            return  $this->getSystemUserEntriesOrCount(null,'Count');
        }
        if(0==$this->isAllCount && 0==$this->isFromMobile){
            return 0;
        }
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
        $listQuery=$this->replace_SQL($listQuery);
        //echo $listQuery.'<br>';die();
        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }

    /**
     * 调用洞察力系统获取用户名接口
     * @param $pagingModel
     * @param $type
     * @return array|int|mixed
     */
    public function getSystemUserEntriesOrCount($pagingModel,$type){
        global $tyunweburl;
        $sault='multiModuleProjectDirectoryasdafdgfdhggijfgfdsadfggiytudstlllkjkgff';
        $time=time().'123';
        $token=md5($time.$sault);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));
        $startIndex =$_REQUEST['page'];
        $pageLimit = $pagingModel?$pagingModel->getPageLimit():20;
        $postData = array(
            "loginName"=>$this->get('search_value'),
            'pageNum'=>intval($startIndex),
            'pageSize'=>intval($pageLimit)
        );
        $url =$tyunweburl.'api/app/tcloud-account/v1.0.0/dclAccount/queryDclAccountListByCondition';
        $res = json_decode($this->https_request($url, json_encode($postData),$curlset),true);
        $data=array();
        $count=0;
        if($res['success']){
            $count=$res['data']['total'];
            if($count){
                $data=$res['data']['list'];
            }
        }
        if($type=='Entries'){
            return $data;
        }
        return $count;
    }

    /**
     * 洞察力系统请求
     * @param $url
     * @param null $data
     * @param array $curlset
     * @return bool|string
     */
    public function https_request($url, $data = null,$curlset=array()){
        $curl = curl_init();
        if(!empty($curlset)){
            foreach($curlset as $key=>$value){
                curl_setopt($curl, $key, $value);
            }
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);//post提交方式
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }


}
