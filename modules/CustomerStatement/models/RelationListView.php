<?php
/*
*定义管理语句
*/
class CustomerStatement_RelationListView_Model extends Vtiger_RelationListView_Model {
    static $relatedquerylist = array(
        'Files'=>'SELECT vtiger_files.attachmentsid as crmid,vtiger_files.* from vtiger_files where relationid=? AND delflag=0',

    );

    public function getEntries($pagingModel){

        $relatedModuleName=$_REQUEST['relatedModule'];
        $moduleName = $_REQUEST['module'];
        $relatedquerylist=self::$relatedquerylist;

        if($relatedModuleName == 'Files') {
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
        //$limitQuery = $query .' LIMIT '.$startIndex.','.$pageLimit;
        //取消分页
        $limitQuery = $query;
        $result = $db->pquery($limitQuery, array());
        $relatedRecordList = array();


        for($i=0; $i< $db->num_rows($result); $i++ ) {
            $row = $db->fetch_row($result,$i);
            //$row['down_id'] = base64_encode($row['attachmentsid']);

            $record = Vtiger_Record_Model::getCleanInstance($relationModule->get('name'));

            $record->setData($row)->setModuleFromInstance($relationModule);
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
