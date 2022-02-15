<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ReceivableOverdue_ListView_Model extends Vtiger_ListView_Model {


    //根据参数显示数据
    public function getListViewEntries($pagingModel) {
        $db = PearDatabase::getInstance();
        $moduleName = 'ReceivableOverdue';

        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');

        $this->getSearchWhere();
        $listQuery = $this->getQuery();
        $listQuery.=$this->getUserWhere();
        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();

        $viewid = ListViewSession::getCurrentView($moduleName);

        ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,
        $listQuery .= " and vtiger_receivable_overdue.iscancel=0";

        $listQuery .= " LIMIT $startIndex,".($pageLimit);

        /* 替换select字段 start */
        $listQuery = str_replace('(vtiger_products.productname) as productid',' ifnull( vtiger_products.productname, vtiger_servicecontracts.contract_type) AS productid',$listQuery);
        //最后跟进内容
        $listQuery = str_replace('vtiger_receivable_overdue.commentcontent,',
            'vtiger_modcomments.commentcontent as commentcontent,',$listQuery);
        //最后跟进时间
        $listQuery = str_replace('vtiger_receivable_overdue.lastfollowtime,',
            'vtiger_modcomments.addtime AS lastfollowtime,vtiger_receivable_overdue.contractid,vtiger_receivable_overdue.stage,'
            ,$listQuery);
        //核对结果
        $listQuery = str_replace("IF(vtiger_receivable_overdue.checkresult=1,'是','否') as checkresult,",
            "IF(vtiger_receivablecheck.checkresult IS NULL,'未核对',IF(vtiger_receivablecheck.checkresult=1,'符合','不符合')) as checkresult,",
            $listQuery);
        //最后核对时间
        $listQuery = str_replace('vtiger_receivable_overdue.checktime,',
            'vtiger_receivablecheck.checktime,',
            $listQuery);
        //最后核对人
        $listQuery = str_replace('vtiger_receivablecheck.collator,',
            "(select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active' AND isdimission=0,'','[离职]'))) as last_name from vtiger_users where vtiger_receivablecheck.collator=vtiger_users.id) as collator,",
            $listQuery);
        //最后核对内容
        $listQuery = str_replace('vtiger_receivable_overdue.checkremark,',
            'vtiger_receivablecheck.remark AS checkremark,'
            ,$listQuery);
        /* 替换select字段 end */

        //替换from
        $listQuery = str_replace(' FROM vtiger_receivable_overdue',
            " FROM vtiger_receivable_overdue LEFT JOIN vtiger_receivablecheck ON vtiger_receivablecheck.type='ReceivableOverdue' AND vtiger_receivablecheck.relation_id = vtiger_receivable_overdue.contractid AND vtiger_receivablecheck.stage = vtiger_receivable_overdue.stage LEFT JOIN (SELECT * FROM (SELECT addtime, commentcontent, moduleid, modcommentpurpose FROM vtiger_modcomments WHERE modulename='ServiceContracts' ORDER BY addtime DESC) vtiger_modcomments GROUP BY moduleid,modcommentpurpose) vtiger_modcomments ON vtiger_modcomments.moduleid = vtiger_receivable_overdue.contractid AND vtiger_modcomments.modcommentpurpose = vtiger_receivable_overdue.stageshow",
            $listQuery);

        /* 替换where字段 start */
        //最后跟进内容
        $listQuery = str_replace('vtiger_receivable_overdue.commentcontent',
            'vtiger_modcomments.commentcontent',
            $listQuery);
        //最后跟进时间
        $listQuery = str_replace('vtiger_receivable_overdue.lastfollowtime',
            'vtiger_modcomments.addtime',
            $listQuery);
        //核对结果
        $listQuery = str_replace('vtiger_receivable_overdue.checkresult = -1 AND vtiger_receivable_overdue.checkresult IS NOT NULL',
            'vtiger_receivablecheck.checkresult IS NULL',
            $listQuery);
        $listQuery = str_replace('vtiger_receivable_overdue.checkresult',
            'vtiger_receivablecheck.checkresult',
            $listQuery);
        //最后核对时间
        $listQuery = str_replace('vtiger_receivable_overdue.checktime',
            'vtiger_receivablecheck.checktime',
            $listQuery);
        //最后核对人
        $listQuery = str_replace('vtiger_receivable_overdue.collator',
            'vtiger_receivablecheck.collator',
            $listQuery);
        //最后跟进内容
        $listQuery = str_replace('vtiger_receivable_overdue.checkremark',
            'vtiger_receivablecheck.remark',
            $listQuery);
        /* 替换where字段 end */

        $listResult = $db->pquery($listQuery, array());
        while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['receivableoverdueid'];
            $listViewRecordModels[$rawData['receivableoverdueid']] = $rawData;
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

    /*public function getListView() {
        echo 'getListView';exit;
        $db = PearDatabase::getInstance();
        $moduleName = 'ReceivableOverdue';

        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');


        $this->getSearchWhere();
        $listQuery = $this->getQuery();

        $listQuery.=$this->getUserWhere();
        global $current_user;

        $viewid = ListViewSession::getCurrentView($moduleName);

        ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);//session缓存查询条件,
        $listQuery .= " and vtiger_receivable_overdue.iscancel=0";

        //替换select字段
        $listQuery = str_replace('vtiger_receivable_overdue.commentcontent,',
            'vtiger_modcomments.commentcontent as commentcontent,',$listQuery);
        $listQuery = str_replace('vtiger_receivable_overdue.lastfollowtime,',
            'vtiger_modcomments.addtime AS lastfollowtime,vtiger_receivable_overdue.contractid,'
            ,$listQuery);
        //替换from
        $listQuery = str_replace(' FROM vtiger_receivable_overdue',' FROM vtiger_receivable_overdue 
            left join (SELECT * FROM (SELECT addtime, commentcontent, moduleid, modcommentpurpose FROM vtiger_modcomments ORDER BY addtime DESC) vtiger_modcomments GROUP BY moduleid,modcommentpurpose) vtiger_modcomments ON vtiger_modcomments.moduleid = vtiger_receivable_overdue.contractid AND vtiger_modcomments.modcommentpurpose = vtiger_receivable_overdue.stageshow
            left join vtiger_users on vtiger_users.id=vtiger_receivable_overdue.signid',$listQuery);

        //替换where字段
        $listQuery = str_replace('vtiger_receivable_overdue.commentcontent',
            'vtiger_modcomments.commentcontent'
            ,$listQuery);
        $listQuery = str_replace('vtiger_receivable_overdue.lastfollowtime',
            'vtiger_modcomments.addtime'
            ,$listQuery);

        $listResult = $db->pquery($listQuery, array());

        while($rawData=$db->fetch_array($listResult)) {
            $rawData['id'] = $rawData['receivableoverdueid'];
            $listViewRecordModels[$rawData['receivableoverdueid']] = $rawData;
        }
        return $listViewRecordModels;
    }*/

    public function getUserWhere(){
        $searchDepartment = $_REQUEST['department'];
        $listQuery=' ';
        if(!empty($searchDepartment)&&$searchDepartment!='H1'){  //20150525 柳林刚 加入
            $userid=getDepartmentUser($searchDepartment);
            $where=getAccessibleUsers('ReceivableOverdue','List',true);
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
                $listQuery .= ' and vtiger_servicecontracts.signid'.$where;
            }
        }
        return $listQuery;
    }
    public function getListViewCount() {
        $db = PearDatabase::getInstance();
        $queryGenerator = $this->get('query_generator');
        //用户条件
        $where = $this->getUserWhere();
        $queryGenerator->addUserWhere($where);
        $listQuery =  $queryGenerator->getQueryCount();
        $listQuery .= " and vtiger_receivable_overdue.iscancel=0";
        //替换from
        $listQuery = str_replace(' FROM vtiger_receivable_overdue',
            " FROM vtiger_receivable_overdue LEFT JOIN vtiger_receivablecheck ON vtiger_receivablecheck.type='ReceivableOverdue' AND vtiger_receivablecheck.relation_id = vtiger_receivable_overdue.contractid AND vtiger_receivablecheck.stage = vtiger_receivable_overdue.stage LEFT JOIN (SELECT * FROM (SELECT addtime, commentcontent, moduleid, modcommentpurpose FROM vtiger_modcomments WHERE modulename='ServiceContracts' ORDER BY addtime DESC) vtiger_modcomments GROUP BY moduleid,modcommentpurpose) vtiger_modcomments ON vtiger_modcomments.moduleid = vtiger_receivable_overdue.contractid AND vtiger_modcomments.modcommentpurpose = vtiger_receivable_overdue.stageshow",
            $listQuery);
        /* 替换where字段 start */
        //最后跟进内容
        $listQuery = str_replace('vtiger_receivable_overdue.commentcontent',
            'vtiger_modcomments.commentcontent',
            $listQuery);
        //最后跟进时间
        $listQuery = str_replace('vtiger_receivable_overdue.lastfollowtime',
            'vtiger_modcomments.addtime',
            $listQuery);
        //核对结果
        $listQuery = str_replace('vtiger_receivable_overdue.checkresult = -1 AND vtiger_receivable_overdue.checkresult IS NOT NULL',
            'vtiger_receivablecheck.checkresult IS NULL',
            $listQuery);
        $listQuery = str_replace('vtiger_receivable_overdue.checkresult',
            'vtiger_receivablecheck.checkresult',
            $listQuery);
        //最后核对时间
        $listQuery = str_replace('vtiger_receivable_overdue.checktime',
            'vtiger_receivablecheck.checktime',
            $listQuery);
        //最后核对人
        $listQuery = str_replace('vtiger_receivable_overdue.collator',
            'vtiger_receivablecheck.collator',
            $listQuery);
        //最后跟进内容
        $listQuery = str_replace('vtiger_receivable_overdue.checkremark',
            'vtiger_receivablecheck.remark',
            $listQuery);
        /* 替换where字段 end */
        //exit($listQuery);
        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult,0,'counts');
    }
}