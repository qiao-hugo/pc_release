<?php
class InputInvoice_ListView_Model extends Vtiger_ListView_Model {

    public function getListViewEntries($pagingModel,$request=array()) {
        $db = PearDatabase::getInstance();
        $moduleName ='InputInvoice';

        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');
        if(empty($orderBy) && empty($sortOrder)){
            $orderBy = 'inputinvoiceid';
            $sortOrder = 'DESC';
        }
        $this->getSearchWhere();
        $listQuery = $this->getQuery();
        $listQuery.=$this->getUserWhere();
        global $current_user;
        $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;

        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();
        $listQuery .= " LIMIT $startIndex,".($pageLimit);
//        echo $listQuery;die;
        $listResult = $db->pquery($listQuery, array());

        $index = 0;
        while($rawData=$db->fetch_array($listResult)) {
            $rawData['id']=$rawData['inputinvoiceid'];
            $listViewRecordModels[$rawData['id']] = $rawData;
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
        $searchDepartment = $_REQUEST['department'];//部门
        if(empty($searchDepartment)){
            $searchDepartment = 'H1';
        }
        $listQuery='';
        if(!empty($searchDepartment)&&$searchDepartment!='H1'){  //20150525 柳林刚 加入
            $userid=getDepartmentUser($searchDepartment);
            $where=getAccessibleUsers('InputInvoice','List',true);
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



    public function getListViewCount() {
        $db = PearDatabase::getInstance();
        $queryGenerator = $this->get('query_generator');
        $where=$this->getUserWhere();
        $queryGenerator->addUserWhere($where);
        $listQuery =  $queryGenerator->getQueryCount();
        $listResult = $db->pquery($listQuery, array());
        //return $db->num_rows($listResult);
        return $db->query_result($listResult,0,'counts');
    }


}
