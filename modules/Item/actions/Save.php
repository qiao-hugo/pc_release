<?php
class Item_Save_Action extends Vtiger_Save_Action {
    public $stayPaymentWorkFlowSid = 2723009;  //代付款在线签收id
    public function saveRecord($request) {


        $record=$request->get('record');
        $parentcate = $request->get('parentcate');

        if(empty($request->get('record'))){
            $adb =PearDatabase::getInstance();
            $sql = "select * FROM vtiger_parentcate where parentcate=?";
            $sel_result = $adb->pquery($sql, array($parentcate));
            $res_cnt = $adb->num_rows($sel_result);
            if($res_cnt > 0) {
                $rawData = $adb->query_result_rowdata($sel_result, 0);
                $request->set('parentcate',$rawData['parentcateid']);
            }else{
                $sql = "insert into vtiger_parentcate values(?, ?, ?, ?, ?)";
                $adb->pquery($sql, array('',$parentcate,'','',''));
                $parentcateid = $adb->getLastInsertID();
                $request->set('parentcate',$parentcateid);
            }
        }

        $recordModel = $this->getRecordModelFromRequest($request);

        $recordModel->save();

        return $recordModel;
    }
}
