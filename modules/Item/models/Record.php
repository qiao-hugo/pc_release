<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/9/2
 * Time: 20:12
 */
class Item_Record_Model extends Vtiger_Record_Model {
    public function getParentCate(){
        $db = PearDatabase::getInstance();
        $result = $db->pquery("select parentcate from vtiger_item");
        if(!$db->num_rows($result)){
            return array();
        }
        while ($row =$db->fetchByAssoc($result)){
            $parentCates[]=$row['parentcate'];
        }
        $parentCates = array_unique($parentCates);
        return $parentCates;
    }

    public function getSonCateByParentCate($parentcate){
        $db = PearDatabase::getInstance();
        $result =  $db->pquery("select * from vtiger_item where parentcate=?",array($parentcate));
        if(!$db->num_rows($result)){
            return array();
        }
        while ($row=$db->fetchByAssoc($result)){
            $sonCates[] = $row;
        }
        return $sonCates;
    }

    public function getSonCateWorkFlows($soncateid){
        $db = PearDatabase::getInstance();
        $sql = "select a.filterworkflowstageid,b.soncate,b.parentcate,a.invoicecompany,a.companycode,a.workflowstageids,a.ceocheck,a.departmentid,a.department from vtiger_filterworkflowstage a left join vtiger_soncate b on a.sourceid=b.soncateid where b.soncateid=? and a.deleted=0 order by a.filterworkflowstageid desc";
        $result = $db->pquery($sql,array($soncateid));
        if(!$db->num_rows($result)){
            return array();
        }

        $workFlowsRecordModel = Vtiger_Record_Model::getCleanInstance("Workflows");
        while ($row=$db->fetchByAssoc($result)){
            $workflowstage = $workFlowsRecordModel->getFilterWorkFlows($row);
            $row['workflowstages']=$workflowstage;
            $sonCateWorkFlows[] =$row;
        }
        return $sonCateWorkFlows;
    }

    public function getFilterWorkFlow($soncateid,$total){

    }
}
