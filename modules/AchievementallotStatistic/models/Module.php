<?php 
/**
 * wangbin 2015-1-20 13:58:37 添加跟多回款的筛选项
 * */
class AchievementallotStatistic_Module_Model extends Vtiger_Module_Model {
    /**
     * 获取列表字段,0,1,2都需要获取
     */
    public function getListFields() {
        if(empty($this->listfields)){
            $fieldInfo = Vtiger_Cache::get('getListFieldsnew', $this->id);
            if($fieldInfo){
                return $fieldInfo;
            }
            $blockids=array();
            $blocks=$this->getBlocks();
            foreach($blocks as $blockid){
                $blockids[]=$blockid->id;
            }
            $adb = PearDatabase::getInstance();
            //$sql='SELECT vtiger_field.*,vtiger_fieldmodulerel.relmodule,vtiger_entityname.tablename as ntablename,vtiger_entityname.fieldname as nfieldname,vtiger_entityname.entityidfield  FROM vtiger_field LEFT JOIN vtiger_fieldmodulerel ON vtiger_fieldmodulerel.fieldid=vtiger_field.fieldid LEFT JOIN vtiger_entityname ON vtiger_entityname.modulename=vtiger_fieldmodulerel.relmodule WHERE vtiger_field.tabid=? and vtiger_field.presence in (0,2) and vtiger_field.displaytype!=4 and vtiger_field.displaytype!=0 and vtiger_field.block in ('.implode(',',$blockids).') ORDER BY vtiger_field.listpresence';
            $sql='SELECT vtiger_field.* FROM vtiger_field WHERE vtiger_field.tabid =? AND vtiger_field.isshowfield=0 AND vtiger_field.displaytype != 4 AND vtiger_field.displaytype != 0 AND vtiger_field.block IN ('.implode(', ',$blockids).') ORDER BY vtiger_field.listpresence';
            $result=$adb->pquery($sql,array($this->id));
            $rows=$adb->num_rows($result);

            for($index = 0; $index < $rows; ++$index) {
                $row=$adb->fetch_array($result);
                $fieldRecord=$this->getField($row['fieldname']);
                $row['ishidden']=(!$fieldRecord->getrolePermission('readonly')|| in_array($fieldRecord->getDisplayType(),array(3,4,5)))?1:0;
                $this->listfields[$row['fieldid']]=$row;
            }
            Vtiger_Cache::set('getListFieldsnew', $this->id, $this->listfields);
        }
        return $this->listfields;
    }
    public function getSideBarLinks($linkParams) {
                $module = Vtiger_Module_Model::getCleanInstance("ReceivedPayments");
                return $module->getSideBarLinks($linkParams);
    }
}