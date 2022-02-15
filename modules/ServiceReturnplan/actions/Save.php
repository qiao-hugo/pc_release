<?php
class ServiceReturnplan_Save_Action extends Vtiger_Save_Action {
	public function saveRecord($request) {
        $record = ($request->get('record'));
        $date_var = date("Y-m-d H:i:s");
        if($record>0){
            $request->set('modifytime',$date_var);
        }else{
            $request->set('createtime',$date_var);
            $request->set('modifytime',$date_var);
        }
        $recordModel = $this->getRecordModelFromRequest($request);
		$recordModel->save();
		return $recordModel;
	}
}
