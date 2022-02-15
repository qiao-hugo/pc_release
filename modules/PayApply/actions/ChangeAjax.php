<?php
class PayApply_ChangeAjax_Action extends Vtiger_Action_Controller {
    function __construct() {
        parent::__construct();
        $this->exposeMethod('saveComment');
        $this->exposeMethod('getproductlist');
        $this->exposeMethod('getPayApply');
    }



	function checkPermission(Vtiger_Request $request) {
		return true;
	}

	public function process(Vtiger_Request $request) {
        $mode = $request->getMode();
        if(!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
            return;
        }
	}
	public function saveComment(Vtiger_Request $request){
        $recordId=$request->get('recordId');
        $fllowupdate=$request->get('fllowupdate');
        $nextdate=$request->get('nextdate');
        $hasaccess=$request->get('hasaccess');
        $currentprogess=$request->get('currentprogess');
        $nextwork=$request->get('nextwork');
        $policeindicator=$request->get('policeindicator');
        global $adb,$current_user;
        $Sql='INSERT INTO vtiger_channelcomment(channelid,fllowdate,nextdate,hasaccess,currentprogess,nextwork,policeindicator,smownerid,createdtime) VALUES(?,?,?,?,?,?,?,?,?)';
        $adb->pquery($Sql,array($recordId,$fllowupdate,$nextdate,$hasaccess,$currentprogess,$nextwork,$policeindicator,$current_user->id,date('Y-m-d H:i:s')));
        $Sql='update vtiger_channels set hasaccess=?,fllowdate=?,nextdate=? WHERE  channelid=?';
        $adb->pquery($Sql,array($hasaccess,$fllowupdate,$nextdate,$recordId));
    }

    /**
     * 产品的类型
     * @param Vtiger_Request $request
     */
    function getproductlist(Vtiger_Request $request){
        $parentcate=$request->get('parentcate');
        $db=PearDatabase::getInstance();
        $query = 'SELECT soncateid,soncate FROM vtiger_soncate WHERE deleted = 0 AND parentcate='."'".$parentcate."'";
        $arrrecords = $db->run_query_allrecords($query);
        $arrlist=array();
        if(!empty($arrrecords)){
            foreach($arrrecords as $value){
                $arrlist[]=$value;
            }
        }
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($arrlist);
        $response->emit();
    }

    //获取用户名称,ID
    public function getPayApply(Vtiger_Request $request){
        $parentcate=$request->get('parentcate');
        $soncate=$request->get('soncate');

        $payApplyRecordModel = PayApply_Record_Model::getCleanInstance("PayApply");
        $data1 = $payApplyRecordModel->getPayApply($parentcate,$soncate);
        $data['success']=true;
        $data['list']=$data1;
        echo json_encode($data);
    }
}
