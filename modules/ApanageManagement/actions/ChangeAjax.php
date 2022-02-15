<?php
class ApanageManagement_ChangeAjax_Action extends Vtiger_Action_Controller {
    function __construct() {
        parent::__construct();
        $this->exposeMethod('updateUserInfo');
        $this->exposeMethod('deleteRecord');
    }
	function checkPermission(Vtiger_Request $request) {
		return true;
	}

	public function process(Vtiger_Request $request){
        $mode = $request->getMode();
        if(!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
            return;
        }
	}
    public function updateUserInfo(Vtiger_Request $request){
        global $adb,$current_user;
        $recordModel=Vtiger_Record_Model::getCleanInstance('ApanageManagement');
        $calculation_year_month=$request->get('yearMonth');
        $calculation_year_month1=str_replace('-','',$calculation_year_month);
        $url='https://in-hr.71360.com/entry/api/api/queryAllEmployee?salaryTime='.$calculation_year_month1;
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json"));
        $DataJson=$recordModel->https_requestcomm($url,"[]",$curlset);
        $data=json_decode($DataJson,true);
        $returnData=array('success'=>false,'msg'=>'更新失败请稍后重试！');
        if($data['success']==1) {
            $query='SELECT * FROM `vtiger_ucityname`';
            $result=$adb->pquery($query);
            $cityName=array();
            while($row=$adb->fetch_array($result)){
                $cityName[$row['ucityname']]=$row;
            }
            $sql='TRUNCATE TABLE vtiger_apanagemanagement';
            $adb->pquery($sql,array());
            $createdtime=date('Y-m-d H:i:s');
            $arrayData=$data['data'];
            $total=count($arrayData);
            $intorecord=1000;
            $sqlValue='';
            $i=0;
            $sql="INSERT INTO `vtiger_apanagemanagement`(`userid`, `usercode`, `employeenumber`, `invoicecompany`, `departmentid`, `cityname`, `position`, `cityratio`, `updatemonth`, `createdtime`, `smownerid`,citynameid) VALUES";
            foreach($arrayData as $value){
                $i++;
                $citynameid=$cityName[$value['cityName']]['usercitynameid']>0?$cityName[$value['cityName']]['usercitynameid']:3;
                $sqlValue.="(".$value['crmEmployeeId'].",'".$value['jobNumber']."','".$value['employeeNumber']."','".$value['companyName']."','".$value['departmentId']."','".$value['cityName']."','".$value['position']."','".$value['cityRatio']."','".$calculation_year_month."','".$createdtime."',".$current_user->id.",".$citynameid."),";
                if($i%$intorecord==0|| $i==$total){
                    $sqlValue=trim($sqlValue,',');
                    $adb->pquery($sql.$sqlValue,array());
                    $sqlValue='';
                }
            }
            $returnData=array('success'=>true,'msg'=>'更新成功，系统将自动刷新！');
        }
        $response = new Vtiger_Response();
        $response->setResult($returnData);
        $response->emit();
    }

    public function deleteRecord(Vtiger_Request $request){
        $recordId=$request->get('record');
        global $current_user;
        $db = PearDatabase::getInstance();
        $query='SELECT 1 FROM vtiger_amanagementrelate WHERE userid=?';
        if($db->num_rows($db->pquery($query,array($recordId)))>0){
            $sql='DELETE FROM vtiger_amanagementrelate WHERE userid=?';
            $db->pquery($sql,array($recordId));
            $data=array('success'=>true,'msg'=>'删除成功');
        }else{
            $data=array('success'=>false,'msg'=>'该记录不存在无需删除');
        }
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
}
