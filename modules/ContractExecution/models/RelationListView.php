<?php
/*
*定义管理语句
*/
class ContractExecution_RelationListView_Model extends Vtiger_RelationListView_Model {
	static $relatedquerylist = array(
	'Files'=>"SELECT * from vtiger_files where relationid=? AND delflag=0",
  );

    public function getEntries($pagingModel){
        $relatedModuleName=$_REQUEST['relatedModule'];
//        $moduleName = $_REQUEST['module'];
        $moduleName = 'ServiceContracts';
        $parentId = $_REQUEST['record'];
        $recordModel = ContractExecution_Record_Model::getCleanInstance('ContractExecution');
        $parentId = $recordModel->getContractIdById($parentId);

        $relatedquerylist=self::$relatedquerylist;

        if($relatedModuleName == 'Files') {
            $relatedquerylist[$relatedModuleName] .= " AND description='$moduleName' ";
        }


        if(isset($relatedquerylist[$relatedModuleName])){
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

        //取消分页
        $limitQuery = $query;

        $result = $db->pquery($limitQuery, array());
        $relatedRecordList = array();
        for($i=0; $i< $db->num_rows($result); $i++ ) {
            $row = $db->fetch_row($result,$i);
            $record = Vtiger_Record_Model::getCleanInstance($relationModule->get('name'));

            $record->setData($row)->setModuleFromInstance($relationModule);
            if (empty($row['crmid'])) {

                if ($_REQUEST['relatedModule'] == 'Files') {
                    $record->setId($row['attachmentsid']);
                    $row['crmid'] = $row['attachmentsid'];
                }
            }

            $record->setId($row['crmid']);

            $relatedRecordList[$row['crmid']] = $record;
        }
        return $relatedRecordList;
    }
}