<?php
/* +****************
 *合同保存验证
 *新增产品必填，打回后产品不可编辑
 * ******************* */

class SaleManager_Save_Action extends Vtiger_Save_Action {

    public function process(Vtiger_Request $request) {

        $recordModel = $this->saveRecord($request);



        if($request->get('relationOperation')) {

            $loadUrl = $this->getParentRelationsListViewUrl($request);
        } else if ($request->get('returnToList')) {
            $loadUrl = $recordModel->getModule()->getListViewUrl();
        } else {
            $loadUrl = $recordModel->getDetailViewUrl();
        }
        if(empty($loadUrl)){
            if($request->getHistoryUrl()){
                $loadUrl=$request->getHistoryUrl();
            }else{
                $loadUrl="index.php";
            }
        }
        //这个地方直接到列表页.不用走详情页
        $loadUrl='index.php?module=SaleManager&view=List';

        header("Location: $loadUrl");
    }
}
