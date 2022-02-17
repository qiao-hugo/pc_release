<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ContractExecution_ListView_Model extends Vtiger_ListView_Model {


    //根据参数显示数据
    public function getListViewEntries($pagingModel) {
        $db = PearDatabase::getInstance();
        $moduleName = 'ContractExecution';


        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');

        //List view will be displayed on recently created/modified records
        //列表视图将显示最近的创建修改记录  ---做什么用处

        $this->getSearchWhere();
        $listQuery = $this->getQuery();

        $listQuery.=$this->getUserWhere();
        global $current_user;


        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();

        $viewid = ListViewSession::getCurrentView($moduleName);

        ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,

        $listQuery .= " order by contractexecutionid desc LIMIT $startIndex,".($pageLimit);
//        $listQuery .= "order by contractexecutionid";
//        echo $listQuery;die();
        $listQuery = str_replace("(vtiger_products.productname) as productid","IFNULL(vtiger_products.productname, vtiger_contract_type.contract_type) AS productid",$listQuery);
        $listQuery = str_replace("WHERE 1=1 ","left join vtiger_contract_type on vtiger_contract_type.contract_type = vtiger_servicecontracts.contract_type WHERE 1=1 ",$listQuery);
        $listQuery = str_replace("vtiger_servicecontracts.bussinesstype","vtiger_contract_type.bussinesstype",$listQuery);
        $listResult = $db->pquery($listQuery, array());

        while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['contractexecutionid'];

            $listViewRecordModels[$rawData['contractexecutionid']] = $rawData;
        }
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
            return $temp;
        }
        return $queryGenerator->getFocus()->list_fields_name;

    }
    public function getUserWhere(){
        $searchDepartment = $_REQUEST['department'];
        $listQuery=' ';
        if(!empty($searchDepartment)&&$searchDepartment!='H1'){  //20150525 柳林刚 加入
            $userid=getDepartmentUser($searchDepartment);
            $where=getAccessibleUsers('VisitingOrder','List',true);
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
    }
    public function getListViewCount(){
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

        //echo $listQuery.'<br>';
        //die();
        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }
}