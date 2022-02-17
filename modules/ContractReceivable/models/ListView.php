<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ContractReceivable_ListView_Model extends Vtiger_ListView_Model {


    //根据参数显示数据
    public function getListViewEntries($pagingModel) {
        $db = PearDatabase::getInstance();
        $moduleName = 'ContractReceivable';

        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');

        $this->getSearchWhere();
        $listQuery = $this->getQuery();
        $listQuery.=$this->getUserWhere();

        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();

        $viewid = ListViewSession::getCurrentView($moduleName);

        ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,
        $bussinesstype = $_REQUEST['bussinesstype'];
        if($bussinesstype=='bigsass'){
            $listQuery .= " and vtiger_contract_receivable.bussinesstype='".$bussinesstype."' ";
        }else{
            $listQuery .= " and vtiger_contract_receivable.bussinesstype in('smallsass','smallsassdirect')";
            $listQuery = str_replace("vtiger_servicecontracts.bussinesstype","vtiger_contract_receivable.bussinesstype",$listQuery);
        }
        $listQuery .= " and vtiger_contract_receivable.iscancel=0";
        $listQuery .= " LIMIT $startIndex,".($pageLimit);
        $listQuery = str_replace('vtiger_contract_receivable.productid',"vtiger_servicecontracts.contract_type as productid",$listQuery);
        //核对结果
        $listQuery = str_replace("IF(vtiger_contract_receivable.checkresult=1,'是','否') as checkresult,",
            "IF(vtiger_receivablecheck.checkresult IS NULL,'未核对',IF(vtiger_receivablecheck.checkresult=1,'符合','不符合')) as checkresult,",
            $listQuery);
        //最后核对时间
        $listQuery = str_replace('vtiger_contract_receivable.checktime,',
            'vtiger_receivablecheck.checktime,',
            $listQuery);
        //最后核对人
        $listQuery = str_replace('vtiger_receivablecheck.collator,',
            "(select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active' AND isdimission=0,'','[离职]'))) as last_name from vtiger_users where vtiger_receivablecheck.collator=vtiger_users.id) as collator,",
            $listQuery);
        //最后核对内容
        $listQuery = str_replace('vtiger_contract_receivable.checkremark,',
            'vtiger_receivablecheck.remark AS checkremark,'
            ,$listQuery);
        /* 替换select字段 end */

        //替换from
        $listQuery = str_replace(' FROM vtiger_contract_receivable',
            " FROM vtiger_contract_receivable LEFT JOIN vtiger_receivablecheck ON vtiger_receivablecheck.type='ContractReceivable' AND vtiger_receivablecheck.relation_id = vtiger_contract_receivable.contractid",
            $listQuery);

        /* 替换where字段 start */
        //核对结果
        $listQuery = str_replace('vtiger_contract_receivable.checkresult = -1 AND vtiger_contract_receivable.checkresult IS NOT NULL',
            'vtiger_receivablecheck.checkresult IS NULL',
            $listQuery);
        $listQuery = str_replace('vtiger_contract_receivable.checkresult',
            'vtiger_receivablecheck.checkresult',
            $listQuery);
        //最后核对时间
        $listQuery = str_replace('vtiger_contract_receivable.checktime',
            'vtiger_receivablecheck.checktime',
            $listQuery);
        //最后核对人
        $listQuery = str_replace('vtiger_contract_receivable.collator',
            'vtiger_receivablecheck.collator',
            $listQuery);
        //最后跟进内容
        $listQuery = str_replace('vtiger_contract_receivable.checkremark',
            'vtiger_receivablecheck.remark',
            $listQuery);
        /* 替换where字段 end */

        $listResult = $db->pquery($listQuery, array());

        while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['contractreceivableid'];
            $listViewRecordModels[$rawData['contractreceivableid']] = $rawData;
        }
        return $listViewRecordModels;
    }

    /*public function getListView(){
        $db = PearDatabase::getInstance();
        $moduleName = 'ContractReceivable';


        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');

        //List view will be displayed on recently created/modified records
        //列表视图将显示最近的创建修改记录  ---做什么用处

        $this->getSearchWhere();
        $listQuery = $this->getQuery();

        $listQuery.=$this->getUserWhere();
        global $current_user;



        $viewid = ListViewSession::getCurrentView($moduleName);

        ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,
        $bussinesstype = $_REQUEST['bussinesstype'];
        if($bussinesstype=='bigsass'){
            $listQuery .= " and vtiger_contract_receivable.bussinesstype='".$bussinesstype."' ";
        }elseif($bussinesstype=='smallsass'){
            $listQuery .= " and vtiger_contract_receivable.bussinesstype in('smallsass','smallsassdirect')";
        }
        $listQuery = str_replace('vtiger_contract_receivable.productid,',"vtiger_servicecontracts.contract_type as productid,",$listQuery);

        $listQuery = str_replace("vtiger_contract_receivable.signid","vtiger_contract_receivable.signid,vtiger_users.last_name as signname",$listQuery);
        $listQuery = str_replace("FROM vtiger_contract_receivable","FROM vtiger_contract_receivable left join vtiger_users on vtiger_users.id=vtiger_contract_receivable.signid",$listQuery);

        $listQuery .= " and vtiger_contract_receivable.iscancel=0";

//        echo $listQuery;die;
        $listResult = $db->pquery($listQuery, array());

        while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['contractreceivableid'];
            $listViewRecordModels[$rawData['contractreceivableid']] = $rawData;
        }
        return $listViewRecordModels;
    }*/

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
    public function getUserWhere(){
        $searchDepartment = $_REQUEST['department'];
        $listQuery=' ';
        if(!empty($searchDepartment)&&$searchDepartment!='H1'){
            $userid=getDepartmentUser($searchDepartment);
            $where=getAccessibleUsers('ContractReceivable','List',true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $where=!empty($where)?$where:array(-1);
            $listQuery .= ' and vtiger_servicecontracts.signid in ('.implode(',',$where).')';
        }else{
            $where=getAccessibleUsers();
            if($where!='1=1'){
                $listQuery .= ' and vtiger_servicecontracts.signid '.$where;
            }
        }
        return $listQuery;
    }
    public function getListViewCount(){
        $db = PearDatabase::getInstance();
        $queryGenerator = $this->get('query_generator');
        $where=$this->getUserWhere();
        $queryGenerator->addUserWhere($where);
        $listQuery =  $queryGenerator->getQueryCount();
        $bussinesstype = $_REQUEST['bussinesstype'];
        if($bussinesstype=='bigsass'){
            $listQuery .= " and vtiger_contract_receivable.bussinesstype='".$bussinesstype."' ";
        }elseif ($bussinesstype=='smallsass'){
            $listQuery .= " and vtiger_contract_receivable.bussinesstype in('smallsass','smallsassdirect')";
        }
        $listQuery .= " and vtiger_contract_receivable.iscancel =0";
        //替换from
        $listQuery = str_replace(' FROM vtiger_contract_receivable',
            " FROM vtiger_contract_receivable LEFT JOIN vtiger_receivablecheck ON vtiger_receivablecheck.type='ContractReceivable' AND vtiger_receivablecheck.relation_id = vtiger_contract_receivable.contractid",
            $listQuery);
        /* 替换where字段 start */
        //核对结果
        $listQuery = str_replace('vtiger_contract_receivable.checkresult = -1 AND vtiger_contract_receivable.checkresult IS NOT NULL',
            'vtiger_receivablecheck.checkresult IS NULL',
            $listQuery);
        $listQuery = str_replace('vtiger_contract_receivable.checkresult',
            'vtiger_receivablecheck.checkresult',
            $listQuery);
        //最后核对时间
        $listQuery = str_replace('vtiger_contract_receivable.checktime',
            'vtiger_receivablecheck.checktime',
            $listQuery);
        //最后核对人
        $listQuery = str_replace('vtiger_contract_receivable.collator',
            'vtiger_receivablecheck.collator',
            $listQuery);
        //最后跟进内容
        $listQuery = str_replace('vtiger_contract_receivable.checkremark',
            'vtiger_receivablecheck.remark',
            $listQuery);
        /* 替换where字段 end */
        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }
}