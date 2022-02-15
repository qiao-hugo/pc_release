<?php
/* +****************
 *合同保存验证
 *新增产品必填，打回后产品不可编辑
 * ******************* */

class ContractsProducts_Save_Action extends Vtiger_Save_Action {

/*    public function checkPermission(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $record = $request->get('record');

        if(!Users_Privileges_Model::isPermitted($moduleName, 'Save', $record)) {
            throw new AppException('LBL_PERMISSION_DENIED');
        }
    }
*/
    public function process(Vtiger_Request $request) {
        $recordId = $request->get('record');
        $relproductid=implode(' |##| ',$_REQUEST['relproductid']);
        if(!empty($relproductid)){
            $request->set('relproductid',$relproductid);
        }
        if(!empty($_REQUEST['contract_type'])){
            $request->set('contract_type',$_REQUEST['contract_type']);
        }

        $recordModel = $this->saveRecord($request);
        $loadUrl = $recordModel->getDetailViewUrl();
        if(empty($loadUrl)){
            $loadUrl="index.php";
        }
        header("Location: $loadUrl");
    }
}
