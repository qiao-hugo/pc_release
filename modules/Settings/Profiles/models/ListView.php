<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_Profiles_ListView_Model extends Settings_Vtiger_ListView_Model {
    
    public function getBasicListQuery() {
        $query = parent::getBasicListQuery();
        $query .= ' WHERE directly_related_to_role=0 ';
        return $query;
    }

    /**
     * Function to get the list view entries
     * 获取列表实体列表
     * @param Vtiger_Paging_Model $pagingModel
     * @return <Array> - Associative array of record id mapped to Vtiger_Record_Model instance.
     */
    public function getListViewEntries($pagingModel) {
        $db = PearDatabase::getInstance();

        $module = $this->getModule();
        $moduleName = $module->getName();
        $parentModuleName = $module->getParentName();
        $qualifiedModuleName = $moduleName;
        if (!empty($parentModuleName)) {
            $qualifiedModuleName = $parentModuleName . ':' . $qualifiedModuleName;
        }


        $recordModelClass = Vtiger_Loader::getComponentClassName('Model', 'Record', $qualifiedModuleName);
//        $queryGenerator = $this->get('query_generator');
        $listQuery = $this->getBasicListQuery();

        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();

        $searchKey = $this->get('search_key');
        $searchValue = trim($this->get('search_value'));
        $operator = $this->get('operator');

        $link_code = 'where';
        if(strpos($listQuery, 'WHERE')){
            $link_code = 'and';
        }
        if(!empty($searchKey) &&!empty($searchValue)) {
            $listQuery .= " {$link_code}  {$searchKey} like '%{$searchValue}%' ";
        }
        $orderBy = $this->getForSql('orderby');
        if (!empty($orderBy)) {
            $listQuery .= ' ORDER BY ' . $orderBy . ' ' . $this->getForSql('sortorder');
        }
        if($module->isPagingSupported()) {
            $nextListQuery = $listQuery.' LIMIT '.($startIndex+$pageLimit).',1';
            $listQuery .= " LIMIT $startIndex, $pageLimit";
        }
        //print_r($listQuery);die();
        $listResult = $db->pquery($listQuery, array());
        $noOfRecords = $db->num_rows($listResult);

        $listViewRecordModels = array();
        for ($i = 0; $i < $noOfRecords; ++$i) {
            $row = $db->query_result_rowdata($listResult, $i);
            $record = new $recordModelClass();
            $record->setData($row);

            if (method_exists($record, 'getModule') && method_exists($record, 'setModule')) {
                $moduleModel = Settings_Vtiger_Module_Model::getInstance($qualifiedModuleName);
                $record->setModule($moduleModel);
            }

            $listViewRecordModels[$record->getId()] = $record;
        }
        if($module->isPagingSupported()) {
            $pagingModel->calculatePageRange($listViewRecordModels);

            $nextPageResult = $db->pquery($nextListQuery, array());
            $nextPageNumRows = $db->num_rows($nextPageResult);

            if($nextPageNumRows > 0) {
                $pagingModel->set('nextPageExists', true);
            }else{
                $pagingModel->set('nextPageExists', false);
            }
        }
        return $listViewRecordModels;
    }

    /*	 * *
 * Function which will get the list view count
 * @return - number of records
 */

    public function getListViewCount() {
        $db = PearDatabase::getInstance();

        $module = $this->getModule();
        $listQuery = $this->getBasicListQuery();

        $searchKey = $this->get('search_key');
        $searchValue = trim($this->get('search_value'));
        $operator = $this->get('operator');

        $link_code = 'where';
        if(strpos($listQuery, 'WHERE')){
            $link_code = 'and';
        }
        if(!empty($searchKey) &&!empty($searchValue)) {
            $listQuery .= " {$link_code}  {$searchKey} like '%{$searchValue}%' ";
        }
        $orderBy = $this->getForSql('orderby');
        if (!empty($orderBy)) {
            $listQuery .= ' ORDER BY ' . $orderBy . ' ' . $this->getForSql('sortorder');
        }
//        $listQuery = 'SELECT count(*) AS count FROM ' . $module->baseTable;
        $listResult = $db->pquery($listQuery, array());
        return $db->num_rows($listResult);
    }


}