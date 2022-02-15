<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class RankProtect_BasicAjax_Action extends Vtiger_Action_Controller {
	function __construct() {
		parent::__construct();
        $this->exposeMethod('add');
        $this->exposeMethod('addnum');
        $this->exposeMethod('deleted');
        $this->exposeMethod('deletednum');
        $this->exposeMethod('getDepartment');
        $this->exposeMethod('updateRankProtect');
	}
	
	function checkPermission(Vtiger_Request $request) {
		return;
	}
    /**
     * @param Vtiger_Request $request
     * @throws Exception
     */
	public function process(Vtiger_Request $request) {
		$mode = $request->getMode();
		if(!empty($mode)) {
			echo $this->invokeExposedMethod($mode, $request);
			return;
		}
	}

    function add(Vtiger_Request $request){
        $db=PearDatabase::getInstance();
        $accountrank=$request->get("accountrank");
        $protectday=$request->get("protectday");
        $protectnum=$request->get("protectnum");
        $performancerank=$request->get("performancerank");
        $configurationitem=$request->get("configurationitem");
        $department=$request->get("department");
        $staff_stage=$request->get("staff_stage");
        $isupdate=$request->get("isupdate");
        $followday=$request->get("followday");
        $isfollow=$request->get("isfollow");
        $rankid=$db->getUniqueID("vtiger_rankprotect");
        $data='添加失败';
        do {
            $sql="INSERT INTO vtiger_rankprotect(accountrank,protectday,protectnum,performancerank,configurationitem,department,staff_stage,isupdate,rankid,followday,isfollow) VALUES(?,?,?,?,?,?,?,?,?,?,?)";
            $db->pquery($sql,array($accountrank,$protectday,$protectnum,$performancerank,$configurationitem,$department,$staff_stage,$isupdate,$rankid,$followday,$isfollow));
            $data='添加成功';
        }while(0);
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();

    }
    function deleted(Vtiger_Request $request){
        $id=$request->get("id");
        $delsql="DELETE FROM vtiger_rankprotect WHERE rankid=?";
        $db=PearDatabase::getInstance();
        $db->pquery($delsql,array($id));
        $data='更新成功';
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();

    }
    public function  getDepartment(){
        $db=PearDatabase::getInstance();
        $accountrank=$db->run_query_allrecords("SELECT * FROM vtiger_accountrank");
        foreach ($accountrank as $key=>$value){
            $accountranks[$value['accountrank']]=vtranslate($value['accountrank']);
        }
        $staff_stage=$db->run_query_allrecords("SELECT * FROM  vtiger_staff_stage");
        foreach ($staff_stage as $key=>$value){
            $staff_stages[$value['staff_stage']]=vtranslate($value['staff_stage']);
        }
        $performancerank=$db->run_query_allrecords("SELECT * FROM vtiger_performancerank");
        foreach ($performancerank as $key=>$value){
            $performanceranks[$value['performancerank']]=vtranslate($value['performancerank']);
        }
        return json_encode(array("department"=>getDepartment(),'accountrank'=>$accountranks,'staff_stage'=>$staff_stages,'performancerank'=>$performanceranks));
    }
    public  function updateRankProtect(Vtiger_Request $request){
        $db=PearDatabase::getInstance();
        $accountrank=$request->get("accountrank");
        $protectday=$request->get("protectday");
        $protectnum=$request->get("protectnum");
        $performancerank=$request->get("performancerank");
        $configurationitem=$request->get("configurationitem");
        $department=$request->get("department");
        $staff_stage=$request->get("staff_stage");
        $isupdate=$request->get("isupdate");
        $rankid=$request->get("rankid");
        $followday=$request->get("followday");
        $isfollow=$request->get("isfollow");

        $db->pquery(" UPDATE vtiger_rankprotect set accountrank=?,protectday=?,protectnum=?,performancerank=?,configurationitem=?,department=?,staff_stage=?,isupdate=?,followday=?,isfollow=? WHERE rankid=? ",array($accountrank,$protectday,$protectnum,$performancerank,$configurationitem,$department,$staff_stage,$isupdate,$followday,$isfollow,$rankid));
        echo  json_encode(array("success"=>1,"message"=>'修改成功'));
	}
	public function addnum(Vtiger_Request $request){
        $userid=$request->get("userid");
        $protectnum=$request->get("protectnum");
        $data='添加失败';
        do {
            if(empty($userid)){
                break;
            }
            if(empty($protectnum)){
                break;
            }
            $sql="INSERT INTO vtiger_protectsetting(userid,protectnum) VALUES(?,?)";
            $delsql="DELETE FROM vtiger_protectsetting WHERE userid=?";
            $db=PearDatabase::getInstance();
            $db->pquery($delsql,array($userid));
            $db->pquery($sql,array($userid,$protectnum));
            $data='添加成功';
        }while(0);
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();

    }
    public function deletednum(Vtiger_Request $request){
        $id=$request->get("id");
        $delsql="DELETE FROM vtiger_protectsetting WHERE userid=?";
        $db=PearDatabase::getInstance();
        $db->pquery($delsql,array($id));
        $data='更新成功';
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();

    }
}
