<?php
class Channels_ChangeAjax_Action extends Vtiger_Action_Controller {
    function __construct() {
        parent::__construct();
        $this->exposeMethod('saveComment');
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
}
