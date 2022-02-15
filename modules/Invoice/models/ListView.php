<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Invoice_ListView_Model extends Vtiger_ListView_Model {
	//根据参数显示数据
	public function getListViewEntries($pagingModel) {
		$db = PearDatabase::getInstance();
		$moduleName ='Invoice';


		$orderBy = $this->getForSql('orderby');
		$sortOrder = $this->getForSql('sortorder');

		//List view will be displayed on recently created/modified records
		//列表视图将显示最近的创建修改记录  ---做什么用处
		if(empty($orderBy) && empty($sortOrder)){

			$orderBy = 'vtiger_invoice.invoiceid';
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
        $search=array(
            'vtiger_invoice.drawer=',
            'vtiger_invoice.billingtime,',
            'vtiger_invoice.invoicecode,',
            'vtiger_invoice.invoice_no,',
            'vtiger_invoice.businessnames,',
            'vtiger_invoice.taxrate,',
            'vtiger_invoice.commodityname,',
            'vtiger_invoice.totalandtax,',
            'vtiger_invoice.remark,',
            'vtiger_invoice.amountofmoney,',
            'vtiger_invoice.tax,',
            'LEFT JOIN vtiger_account',
            'vtiger_invoice.modulestatus,',
        );
        $replace=array(
            'vtiger_invoiceextend.drawerextend=',
            'vtiger_invoiceextend.billingtimeextend AS billingtime,',
            'vtiger_invoiceextend.invoicecodeextend AS invoicecode,',
            'vtiger_invoiceextend.invoice_noextend AS invoice_no,',
            'vtiger_invoiceextend.businessnamesextend AS businessnames,',
            'vtiger_invoiceextend.taxrateextend AS taxrate,',
            'vtiger_invoiceextend.commoditynameextend AS commodityname,',
            'vtiger_invoiceextend.totalandtaxextend AS totalandtax,',
            'vtiger_invoiceextend.remarkextend AS remark,',
            'vtiger_invoiceextend.amountofmoneyextend AS amountofmoney,',
            'vtiger_invoiceextend.taxextend AS tax,',
            'LEFT JOIN vtiger_invoiceextend ON vtiger_invoiceextend.invoiceid=vtiger_invoice.invoiceid LEFT JOIN vtiger_account',
            'vtiger_invoice.modulestatus,CONCAT(\'s\',vtiger_servicecontracts.modulestatus) as smodulestatus,',
        );
        //列表匹配多发票显示
        $listQuery=str_replace($search,$replace,$listQuery);
        $search=array(
            'vtiger_invoice.drawer ',
            'vtiger_invoice.billingtime ',
            'vtiger_invoice.invoicecode ',
            'vtiger_invoice.invoice_no ',
            'vtiger_invoice.businessnames ',
            'vtiger_invoice.taxrate ',
            'vtiger_invoice.commodityname ',
            'vtiger_invoice.totalandtax ',
            'vtiger_invoice.remark ',
            'vtiger_invoice.amountofmoney ',
            'vtiger_invoice.tax ',
        );
        $replace=array(
            'vtiger_invoiceextend.drawerextend ',
            'vtiger_invoiceextend.billingtimeextend ',
            'vtiger_invoiceextend.invoicecodeextend ',
            'vtiger_invoiceextend.invoice_noextend ',
            'vtiger_invoiceextend.businessnamesextend ',
            'vtiger_invoiceextend.taxrateextend ',
            'vtiger_invoiceextend.commoditynameextend ',
            'vtiger_invoiceextend.totalandtaxextend ',
            'vtiger_invoiceextend.remarkextend ',
            'vtiger_invoiceextend.amountofmoneyextend ',
            'vtiger_invoiceextend.taxextend ',

        );
        //处理搜索
        $listQuery=str_replace($search,$replace,$listQuery);
        //红冲发票
        $search=array(
            'vtiger_invoice.negativeinvoicecode,',
            'vtiger_invoice.negativeinvoice_no,',
            'vtiger_invoice.negativebusinessnames,',
            'vtiger_invoice.negativedrawer=',
            'vtiger_invoice.negativebillingtime,',
            'vtiger_invoice.negativecommodityname,',
            'vtiger_invoice.negativeamountofmoney,',
            'vtiger_invoice.negativetaxrate,',
            'vtiger_invoice.negativetotalandtax,',
            'vtiger_invoice.negativeremark,',
            'vtiger_invoice.negativetax,',
            'LEFT JOIN vtiger_account',
        );
        $replace=array(
            'vtiger_negativeinvoice.negativeinvoicecodeextend AS negativeinvoicecode,',
            'vtiger_negativeinvoice.negativeinvoice_noextend AS negativeinvoice_no,',
            'vtiger_negativeinvoice.negativebusinessnamesextend AS negativebusinessnames,',
            'vtiger_negativeinvoice.negativedrawerextend=',
            'vtiger_negativeinvoice.negativebillingtimerextend AS negativebillingtime,',
            'vtiger_negativeinvoice.negativecommoditynameextend AS negativecommodityname,',
            'vtiger_negativeinvoice.negativeamountofmoneyextend AS negativeamountofmoney,',
            'vtiger_negativeinvoice.negativetaxrateextend AS negativetaxrate,',
            'vtiger_negativeinvoice.negativetotalandtaxextend AS negativetotalandtax,',
            'vtiger_negativeinvoice.negativeremarkextend AS negativeremark,',
            'vtiger_negativeinvoice.negativetaxextend AS negativetax,',
            'LEFT JOIN vtiger_negativeinvoice ON vtiger_invoiceextend.invoiceextendid=vtiger_negativeinvoice.invoiceextendid LEFT JOIN vtiger_account',
        );
        $listQuery=str_replace($search,$replace,$listQuery);
        $search=array(
            'vtiger_invoice.negativeinvoicecode',
            'vtiger_invoice.negativeinvoice_no',
            'vtiger_invoice.negativebusinessnames',
            'vtiger_invoice.negativedrawer',
            'vtiger_invoice.negativebillingtime',
            'vtiger_invoice.negativecommodityname',
            'vtiger_invoice.negativeamountofmoney',
            'vtiger_invoice.negativetaxrate',
            'vtiger_invoice.negativetotalandtax',
            'vtiger_invoice.negativeremark',
            'vtiger_invoice.negativetax',
        );
        //匹配搜索
        $replace=array(
            'vtiger_negativeinvoice.negativeinvoicecodeextend',
            'vtiger_negativeinvoice.negativeinvoice_noextend',
            'vtiger_negativeinvoice.negativebusinessnamesextend',
            'vtiger_negativeinvoice.negativedrawerextend',
            'vtiger_negativeinvoice.negativebillingtimerextend',
            'vtiger_negativeinvoice.negativecommoditynameextend',
            'vtiger_negativeinvoice.negativeamountofmoneyextend',
            'vtiger_negativeinvoice.negativetaxrateextend',
            'vtiger_negativeinvoice.negativetotalandtaxextend',
            'vtiger_negativeinvoice.negativeremarkextend',
            'vtiger_negativeinvoice.negativetaxextend',
        );
        $listQuery=str_replace($search,$replace,$listQuery);
        //去重复的记录效率太低了
        //$listQuery.=' AND 1>(select count(1) from vtiger_invoiceextend vtiger_invoiceextendaliax where vtiger_invoiceextendaliax.invoiceid=vtiger_invoiceextend.invoiceid and vtiger_invoiceextendaliax.invoiceextendid>vtiger_invoiceextend.invoiceextendid)';
        $listQuery .= ' GROUP BY vtiger_invoice.invoiceid';
        $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;
		$viewid = ListViewSession::getCurrentView($moduleName);
	
		ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,
	
		$listQuery .= " LIMIT $startIndex,".($pageLimit);

        //echo $listQuery;//die();

		$listResult = $db->pquery($listQuery, array());


		$index = 0;
		while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['invoiceid'];
			$listViewRecordModels[$rawData['invoiceid']] = $rawData;

		}
        //print_r($listViewRecordModels);
       	return $listViewRecordModels;
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
        if(!empty($searchDepartment)&&$searchDepartment!='H1'){  //20150525 柳林刚 加入
            $userid=getDepartmentUser($searchDepartment);
            $where=getAccessibleUsers('','',true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $where=!empty($where)?$where:array(-1);
            $listQuery .= ' and vtiger_crmentity.smownerid in ('.implode(',',$where).')';
        }else{
            $where=getAccessibleUsers();
            if($where!='1=1'){
                $listQuery .= ' and vtiger_crmentity.smownerid '.$where;

            }
        }

        return $listQuery;
        //return '';
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

        //echo $listQuery.'<br>';die();
        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }

}