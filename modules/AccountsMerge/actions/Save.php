<?php
/* +****************
 *合同保存验证
 *新增产品必填，打回后产品不可编辑
 * ******************* */

class AccountsMerge_Save_Action extends Vtiger_Save_Action {

    public function process(Vtiger_Request $request) {

        $recordModel = $this->saveRecord($request);
        if(($_REQUEST['related_to']>0 && $_REQUEST['accountid']>0) && $_REQUEST['related_to']!= $_REQUEST['accountid']) {
            $db=PearDatabase::getInstance();
            $sql = "UPDATE vtiger_crmentity SET deleted=1 WHERE crmid=?";
            $db->pquery($sql, array($_REQUEST['related_to']));
            $sql="DELETE FROM vtiger_uniqueaccountname WHERE accountid=? limit 1";
            $db->pquery($sql, array($_REQUEST['related_to']));
        }
       //这个地方直接到列表页.不用走详情页
        $loadUrl='index.php?module=AccountsMerge&view=List';

        header("Location: $loadUrl");
    }
}
