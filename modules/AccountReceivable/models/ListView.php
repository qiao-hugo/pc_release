<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class AccountReceivable_ListView_Model extends Vtiger_ListView_Model {


    //根据参数显示数据
    public function getListViewEntries($pagingModel) {
        $db = PearDatabase::getInstance();
        $moduleName = 'AccountReceivable';

        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');

        $this->getSearchWhere();
        $listQuery = $this->getQuery();

        $listQuery.=$this->getUserWhere();

        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();

        $viewid = ListViewSession::getCurrentView($moduleName);

        ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,

        $listQuery .= " LIMIT $startIndex,".($pageLimit);
        $listQuery = $this->replaceSQL($listQuery);
        $listResult = $db->pquery($listQuery, array());

        while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['accountreceivableid'];
            $listViewRecordModels[$rawData['accountreceivableid']] = $rawData;
        }
        return $listViewRecordModels;
    }

    /*public function getListView(){
        $db = PearDatabase::getInstance();
        $moduleName = 'AccountReceivable';


        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');

        //List view will be displayed on recently created/modified records
        //列表视图将显示最近的创建修改记录  ---做什么用处

        $this->getSearchWhere();
        $listQuery = $this->getQuery();
        //$listQuery=$this->replaceSQL($listQuery);
        $listQuery.=$this->getUserWhere();
        $listQuery=$this->replaceSQL($listQuery);
        global $current_user;


        $viewid = ListViewSession::getCurrentView($moduleName);

        ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,

//        echo $listQuery;die;
        $listResult = $db->pquery($listQuery, array());

        while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['accountreceivableid'];
            $listViewRecordModels[$rawData['accountreceivableid']] = $rawData;
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
        $searchDepartment = $_REQUEST['department'];//部门搜索的部门
        if(empty($searchDepartment)){
            $searchDepartment = 'H1';
        }

        $where=getAccessibleUsers('AccountReceivable','List',true);
        $userid=getDepartmentUser($searchDepartment);
        $listQuery = '';
        if($searchDepartment!='H1'){
            if(!empty($where)&&$where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $where=!empty($where)?$where:array(-1);
            $listQuery .= " AND vtiger_crmentity.smownerid IN(".implode(',', $where).")";   //young.yang 20150626 这一句话是做什么用的？删除了，不然商务看不到自己领取的合同
        }else{
            if(!empty($where)&&$where!='1=1'){
                $listQuery .= " AND vtiger_crmentity.smownerid IN(".implode(',', $where).")";
            }
        }

        return $listQuery;
    }
    public function getListViewCount(){
        $db = PearDatabase::getInstance();
        $queryGenerator = $this->get('query_generator');
        //搜索条件
        //$this->getSearchWhere();
        //用户条件
        $where=$this->getUserWhere();
        $queryGenerator->addUserWhere($where);
        $listQuery =  $queryGenerator->getQueryCount();
        $listQuery=$this->replaceSQL($listQuery);
        //echo $listQuery.'<br>';
        //die();
        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }

    public function replaceSQL($listQuery) {
        $listQuery=str_replace('LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_account_receivable.accountid',
            "LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_account_receivable.accountid LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_account_receivable.accountid LEFT JOIN vtiger_receivablecheck ON vtiger_receivablecheck.type='AccountReceivable' AND vtiger_receivablecheck.relation_id = vtiger_account_receivable.accountid",
            $listQuery);
        $listQuery=str_replace('vtiger_account_receivable.smownerid =',
            'vtiger_crmentity.smownerid=',
            $listQuery);
        $listQuery=str_replace('vtiger_account_receivable.smownerid=',
            'vtiger_crmentity.smownerid=',
            $listQuery);
        $listQuery=str_replace('vtiger_account_receivable.smownerid IS NOT NULL',
            'vtiger_crmentity.smownerid IS NOT NULL',
            $listQuery);
        //核对结果
        $listQuery = str_replace("IF(vtiger_account_receivable.checkresult=1,'是','否') as checkresult,",
            "IF(vtiger_receivablecheck.checkresult IS NULL,'未核对',IF(vtiger_receivablecheck.checkresult=1,'符合','不符合')) as checkresult,",
            $listQuery);
        //最后核对时间
        $listQuery = str_replace('vtiger_account_receivable.checktime,',
            'vtiger_receivablecheck.checktime,',
            $listQuery);
        //最后核对人
        $listQuery = str_replace('vtiger_receivablecheck.collator,',
            "(select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active' AND isdimission=0,'','[离职]'))) as last_name from vtiger_users where vtiger_receivablecheck.collator=vtiger_users.id) as collator,",
            $listQuery);
        //最后核对内容
        $listQuery = str_replace('vtiger_account_receivable.checkremark,',
            'vtiger_receivablecheck.remark AS checkremark,'
            ,$listQuery);

        //核对结果
        $listQuery = str_replace('vtiger_account_receivable.checkresult = -1 AND vtiger_account_receivable.checkresult IS NOT NULL',
            'vtiger_receivablecheck.checkresult IS NULL',
            $listQuery);
        $listQuery = str_replace('vtiger_account_receivable.checkresult',
            'vtiger_receivablecheck.checkresult',
            $listQuery);
        //最后核对时间
        $listQuery = str_replace('vtiger_account_receivable.checktime',
            'vtiger_receivablecheck.checktime',
            $listQuery);
        //最后核对人
        $listQuery = str_replace('vtiger_account_receivable.collator',
            'vtiger_receivablecheck.collator',
            $listQuery);
        //最后跟进内容
        $listQuery = str_replace('vtiger_account_receivable.checkremark',
            'vtiger_receivablecheck.remark',
            $listQuery);
        /* 替换where字段 end */
        return $listQuery;
    }
}