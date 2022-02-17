<?php
class VisitAccountContract_ChangeAjax_Action extends Vtiger_Action_Controller {
    function __construct() {
        parent::__construct();
        $this->exposeMethod('saveComment');
        $this->exposeMethod('CommnetDetail');
        $this->exposeMethod('deletedCommnetDetail');
        $this->exposeMethod('updateField');
    }
    private $superid=array(1002,2110);


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

    /**
     * 保存点评
     * @param Vtiger_Request $request
     */
	public function saveComment(Vtiger_Request $request){
        $recordId=$request->get('record');
        $classic=$request->get('classic');
        $commentresult=$request->get('commentresult');
        $remark=$request->get('remark');

        global $adb,$current_user;
        $datetime=date('Y-m-d H:i:s');
        $userid=$current_user->id;
        $str='';
        $arr=array();
        foreach($remark as $key=>$value){
            $str.='(?,?,?,?,?,?),';
            $arr[]=$recordId;
            $arr[]=$datetime;
            $arr[]=$userid;
            $arr[]=$classic[$key];
            $arr[]=$commentresult[$key];
            $arr[]=$remark[$key];
            $classiccs=$classic[$key];
            $commentresultcs=$commentresult[$key];
        }
        if(!empty($arr)) {
            $str=trim($str,',');
            $Sql = 'INSERT INTO vtiger_visitaccountcontractsheet(visitaccountcontractid,commentdatetime,userid,classic,commentresult,remark) VALUES'.$str;
            //$adb->pquery($Sql, array($recordId, $datetime, $current_user->id, $classic, $commentresult, $remark));
            $adb->pquery($Sql, $arr);
            $Sql = 'update vtiger_visitaccountcontract set commentstaus=? WHERE  visitaccountcontractid=?';
            $commentstaus = ($classiccs == 'gnosound') ? 'gnosound' : 'review';
            $adb->pquery($Sql, array($commentstaus, $recordId));
        }
    }

    /**
     * 点评列表
     * @param Vtiger_Request $request
     */
    public function CommnetDetail(Vtiger_Request $request){
        $recordId=$request->get('record');
        global $adb,$current_user;
        $userid=$current_user->id;
        $result=$adb->pquery('SELECT 
                                    vtiger_visitaccountcontractsheet.visitaccountcontractsheetid AS vacsid,
                                    vtiger_visitaccountcontractsheet.commentdatetime,
                                    vtiger_visitaccountcontractsheet.classic,
                                    vtiger_visitaccountcontractsheet.commentresult,
                                    vtiger_visitaccountcontractsheet.remark,
                                    vtiger_visitaccountcontractsheet.userid,
                                    vtiger_users.last_name as username 
                                FROM vtiger_visitaccountcontractsheet 
                                LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_visitaccountcontractsheet.userid 
                                WHERE vtiger_visitaccountcontractsheet.visitaccountcontractid=? AND vtiger_visitaccountcontractsheet.deleted=0 order by visitaccountcontractsheetid desc',array($recordId));
        $data=array();
        while($row=$adb->fetch_row($result)){
            $row['classicsource']=$row['classic'];
            $row['classic']=vtranslate($row['classic'],'VisitAccountContract');
            $row['commentresultsource']=$row['commentresult'];
            $row['commentresult']=vtranslate($row['commentresult'],'VisitAccountContract');
            $row['ismodify']=($row['userid']==$userid || in_array($userid,$this->superid))?1:0;
            $row['isdeleted']=in_array($userid,$this->superid)?1:0;
            $data[]=$row;
        }
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * 删除点评
     * @param Vtiger_Request $request
     */
    public function deletedCommnetDetail(Vtiger_Request $request){
        $recordId=$request->get('record');
        global $adb,$current_user;
        $deleteddatetime=date('Y-m-d H:i:s');
        if(in_array($current_user->id,$this->superid)){
            $adb->pquery('UPDATE vtiger_visitaccountcontractsheet SET deleted=1,deleteddatetime=?  WHERE visitaccountcontractsheetid=?',array($deleteddatetime,$recordId));
        }
        $data=array();
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * 点评修改
     * @param Vtiger_Request $request
     */
    public function updateField(Vtiger_Request $request){
        $recordId=$request->get('recordid');
        $fieldname=$request->get('fieldname');
        $fieldvalue=$request->get('fieldvalue');
        global $adb,$current_user;

        $deleteddatetime=date('Y-m-d H:i:s');
        $newconect='|'.$deleteddatetime.','.$current_user->id.",".$fieldname.','.$fieldvalue;
        $sql='';
        if(!in_array($current_user->id,$this->superid)){
            $sql='userid='.$current_user->id.' AND ';
        }
        $adb->pquery('UPDATE vtiger_visitaccountcontractsheet SET '.$fieldname.'=\''.$fieldvalue.'\',updatecomment=concat(IFNULL(updatecomment,\'\'),\''.$newconect.'\')  WHERE '.$sql.' visitaccountcontractsheetid=?',array($recordId));
        $data=array();
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
}
