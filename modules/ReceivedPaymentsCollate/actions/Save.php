<?php

class ReceivedPaymentsCollate_Save_Action extends Vtiger_Save_Action {
    public $COLLATEID=3075417;
    public function process(Vtiger_Request $request) {

        $_REQUEST['workflowsid']=$this->COLLATEID;
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
        if($request->isAjax()){

        }else{
            header("Location: $loadUrl");
        }
    }
}
