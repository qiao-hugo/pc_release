<?php

class AccountPlatform extends baseapp{
	private $pagecount = 10;

	public function index(){
	        $searchflag = $_REQUEST['searchflag'];
            $pagenum   = isset($_REQUEST['pagenum'])?$_REQUEST['pagenum']:1;
            $status = $_REQUEST['status']?$_REQUEST['status']:'check';
            
            $search=array();
            switch ($_REQUEST['radiot']){
                case 'vtiger_accountplatform.idaccount':
                    $search[]=array(
                        'field'		=> "vtiger_accountplatform.idaccount##1##11403##string",
                        'operator'	=> "LIKE",
                        "value"		=> trim($_REQUEST['searchvalue']),
                        "andor"		=> "And",
                    );
                    break;
                case 'vtiger_account.accountname':
                    $search[]=array(
                        'field'		=> "vtiger_account.accountname##10##11407##reference",
                        'operator'	=> "LIKE",
                        "value"		=> trim($_REQUEST['searchvalue']),
                        "andor"		=> "And",
                    );
                    break;
                case 'vtiger_accountplatform.accountplatform':
                    $search[]=array(
                        'field'		=> "vtiger_accountplatform.accountplatform##1##11404##string",
                        'operator'	=> "LIKE",
                        "value"		=> trim($_REQUEST['searchvalue']),
                        "andor"		=> "And",
                    );
                    break;
                default:
                    break;
            }
            $search = $this->create_search_field($search);
            $params = array(
                'fieldname'=>array('pagenum' 	=> $pagenum,
                    'pagecount'  => $this->pagecount,
                    'searchField'=> $search,
                    'userid'		=> $this->userid
                ),
                'userid'		=> $this->userid
            );
            $list = $this->call('getAccountPlatformList', $params);
            $arr=array();
            if(!empty($list[1])) {
                $userlist = $this->getWeixinDepartMsg();
                $userlist = json_decode($userlist, true);
                if ($userlist['errcode'] == 0) {
                    foreach ($userlist['userlist'] as $value) {
                        $arr[md5($value['userid'])] = $value['avatar'];
                    }
                }
            }
            $this->smarty->assign('radiot',$_REQUEST['radiot']);
            $this->smarty->assign('searchvalue',$_REQUEST['searchvalue']);
            $this->smarty->assign('searchflag',$searchflag);
            $type = $_REQUEST['type'];
            $this->smarty->assign('modulestatus',$this->modulestatus);
            $this->smarty->assign('totalnum', $list[0]);
            $this->smarty->assign('list',$list[1]);
            $this->smarty->assign('USERIMGS',$arr);
            $this->smarty->assign('status', $status);
            if ($type == 'ajax') {
                $this->smarty->display('AccountPlatform/ajax.html');
            }else{
                $this->smarty->display('AccountPlatform/index.html');
            }
	}

	// ??????
	public function repulse() {
		$stagerecordid  = $_REQUEST['stagerecordid'];
		$record  = $_REQUEST['record'];
		$repulseinfo  = $_REQUEST['repulseinfo'];
		$isbackname = $_REQUEST['isbackname'];
		if ($stagerecordid && $record) {
			$params = array(
				'fieldname'=>array( 
					'record'=> $record,
					'module'=>'SalesorderWorkflowStages',
					'mode'=>'backall',
					'reject'=>$repulseinfo,
					'isrejectid'=>$stagerecordid,
					'isbackname'=>$isbackname,
					'src_module'=>'AccountPlatform',
					'action'=>'SaveAjax',
					'actionnode'=>0
				),
				'userid'			=> $this->userid
			);
			$tt = $this->call('salesorderWorkflowStagesRepulse', $params);
			echo $tt[0];die;
		}
	}
    // ????????????
    public function applicationToModify(){
        $record  = $_REQUEST['record'];
        if($record){
            $params = array(
                'fieldname'=>array(
                    'record'=> $record,
                    'module'=>'AccountPlatform',
                    'mode'=>'Resubmit',
                    'action'=>'ChangeAjax',
                    'changeAjaxAction'=>'AccountPlatform_ChangeAjax_Action'
                ),
                'userid'=> $this->userid
            );
            $result = $this->call('applicationToModify', $params);
        }
        echo json_encode($result[1]);exit();
    }
	//??????
	public function examine() {
		$stagerecordid  = $_REQUEST['stagerecordid'];
		$record  = $_REQUEST['record'];
		if ($stagerecordid && $record) {
			$params = array(
				'fieldname'=>array( 
					'stagerecordid'	=> $stagerecordid,
					'record'=> $record,
					'module'=>'SalesorderWorkflowStages',
					'mode'=>'updateSalseorderWorkflowStages',
					'src_module'=>'AccountPlatform',
					'action'=>'SaveAjax',
					'customer'=>0,
					'customername'=>''
				),
				'userid'			=> $this->userid
			);
			$this->call('salesorderWorkflowStagesExamine', $params);
		}
	}
    // ??????????????????
    public function accountPlatformList(){
	    $recordId=$_REQUEST['id'];

	    $paramdata=array(
            'module' => 'AccountPlatform',
            'action' => 'getAccountPlatformDetailM',
            'id' => $recordId);
        if($_REQUEST['idaccount']){
            $idaccound=$_REQUEST['idaccount'];
        }else{
            $idaccound='';
        }
	    if($_REQUEST['pagenum']){
            $num=$_REQUEST['pagenum'];
        }else{
            $num=1;
        }
        $paramdata['num']=$num;
        $paramdata['idaccount']=trim($idaccound);
        $params = array(
            'fieldname' => $paramdata,
            'userid' => $this->userid
        );
        $res = $this->call('getComRecordModule', $params);
        $detailList=array();
        if($res[0]['success']==1){
            $detailList=$res[0]['result'];
        }
        if($_REQUEST['type']=='ajax'){
             if($_REQUEST['searchType']==1){
                 $this->smarty->assign("detailList",$detailList);
                 $this->smarty->display('AccountPlatform/detaillistajax.html');
                 exit();
             }else{

             }
        }
        $this->smarty->assign("idaccound",$idaccound);
        $this->smarty->assign("recordId",$recordId);
	    $this->smarty->assign("detailList",$detailList);
        $this->smarty->display('AccountPlatform/detaillist.html');
    }

    public function deleteOneDetail(){
        $recordId=$_REQUEST['recordId'];
        $id=$_REQUEST['id'];
        $paramdata=array(
            'module' => 'AccountPlatform',
            'action' => 'deleteOneDetailM',
            'recordId' => $recordId,
            'id'=>$id);
        $params = array(
            'fieldname' => $paramdata,
            'userid' => $this->userid
        );
        $res = $this->call('getComRecordModule', $params);
        echo $res;
    }
    // ?????? ?????? ????????????
    public function updateDetailOne(){
        $recordId=$_REQUEST['recordId'];
        $id=$_REQUEST['id'];
        $accountplatform=$_REQUEST['accountplatform'];
        $idaccount=$_REQUEST['idaccount'];
        $paramdata=array(
            'module' => 'AccountPlatform',
            'action' => 'updateDetailOneM',
            'recordId' => $recordId,
            'userid'=>$this->userid,
            'idaccount'=>$idaccount,
            'accountplatform'=>$accountplatform,
            'id'=>$id);
        $params = array(
            'fieldname' => $paramdata,
            'userid' => $this->userid
        );
        $res = $this->call('getComRecordModule', $params);
        if($res[0]['success']){
            $data=array("success"=>1);
        }else{
            $data=array("success"=>0,"message"=>$res[0]['message']);
        }
        echo  json_encode($data);exit();
    }
    //js??????????????????
    public function addAccountPlatform(){
        $recordId=$_REQUEST['recordId'];
        $accountplatform=$_REQUEST['accountplatform'];
        $idaccount=$_REQUEST['idaccount'];
        $paramdata=array(
            'module' => 'AccountPlatform',
            'action' => 'updateDetailOneM',
            'recordId' => $recordId,
            'userid'=>$this->userid,
            'idaccount'=>$idaccount,
            'accountplatform'=>$accountplatform,
            'id'=>0);
        $params = array(
            'fieldname' => $paramdata,
            'userid' => $this->userid
        );
        $res = $this->call('getComRecordModule', $params);
        if($res[0]['success']){
            $data=array("success"=>1);
        }else{
            $data=array("success"=>0,"message"=>$res[0]['message']);
        }
        echo  json_encode($data);exit();

    }
    //??????????????????
    public function adddetail(){
        $recordId=$_REQUEST['id'];
        $this->smarty->assign("recordId",$recordId);
        $this->smarty->display('AccountPlatform/addDetail.html');
    }

	public function one() {
        $id  = $_REQUEST['id'];
        if (! empty($id)) {

            $params = array(
                'fieldname'=>array(
                    'id'	=> $id,
                    'module'	=> 'AccountPlatform',
                    'record'=> $id,
                ),
                'userid'		=> $this->userid
            );
            $result = $this->call('oneAccountPlatform', $params);
            //????????????????????????????????????  $editPower[0]??????  true or false
            $editPower = $this->call('getApplicationAuthority', $params);
            $this->smarty->assign('editPower',$editPower[0]);
            $this->smarty->assign('isPosibbleToEdit',$editPower[1]);
            $this->smarty->assign('detailInfo', $result[0]);
            /*$this->smarty->assign('AccountPlatform', $result[0]);*/
            $this->smarty->assign('moduleStatusArray',$this->modulestatus);
		$this->smarty->assign('WORKFLOWSSTAGELIST', $result[1]['WORKFLOWSSTAGELIST']); //????????????
		$this->smarty->assign('ISROLE', $result[1]['ISROLE']);    //?????????????????????
		$this->smarty->assign('STAGERECORDID', $result[1]['STAGERECORDID']);  //???????????????id
		$this->smarty->assign('STAGERECORDNAME', $result[1]['STAGERECORDNAME']);  //??????????????????????????????
		$this->smarty->assign('SALESORDERHISTORY', $result[1]['SALESORDERHISTORY']);  //??????????????????
		$this->smarty->assign('REMARKLIST', $result[1]['REMARKLIST']);  //????????????
		$this->smarty->assign('record', $id);
		$this->smarty->display('AccountPlatform/one.html');
	}
	}
    public function add(){
        $id  = $_REQUEST['id'];
        if (!empty($id)){
            $params = array(
                'fieldname'=>array(
                    'id'	=> $id,
                    'module'	=> 'AccountPlatform',
                    'record'=> $id,
                ),
                'userid'		=> $this->userid
            );
            $result1 = $this->call('oneAccountPlatform', $params);
            $this->smarty->assign('detailInfo', $result1[0]);
        }
        $this->smarty->assign('record',$id);
        $token='AccountPlatform'.$this->userid;
        $this->setAddToken($token);
        $params = array(
            'fieldname'=>array(
                //'searchField'=> $search,
                'module'	=> 'Workflows',
                'userid'	=> $this->userid,
                'search_key'	=> 'mountmodule',
                'search_value'	=> 'AccountPlatform',
            ),
            'userid'			=> $this->userid
        );
        $result2= $this->call('getComListImplements', $params);
        $this->smarty->assign('workflows', $result2[1]);
        $this->smarty->display('AccountPlatform/add.html');
    }
    /**
     * ??????????????? gaocl add 2018/04/19
     */
    public function search_vendors() {
        $vendors = trim($_REQUEST['search_vendors']);

        if(!empty($vendors)){

            $params = array(
                'fieldname'=>array(
                    'searchValue'	=> $vendors,
                ),
                'userid'			=> $this->userid
            );
            $result = $this->call('search_vendors', $params);
            $res = $result[0];

            //print_r($result);die;
            if (!empty($res)) {
                echo json_encode($res);die;
            }
        }
        echo json_encode(array());
    }

    /**
     * ??????????????????????????? gaocl add 2018/04/19
     */
    public function search_vendors_servicecontracts() {
        $vendors_servicecontracts = trim($_REQUEST['search_servicecontracts']);
        if(!empty($vendors_servicecontracts)){

            $params = array(
                'fieldname'=>array(
                    'searchValue'	=> $vendors_servicecontracts,
                ),
                'userid'			=> $this->userid
            );
            $result = $this->call('search_vendors_servicecontracts', $params);
            $res = $result[0];

            //print_r($res);die;
            if (!empty($res)) {
                echo json_encode($res);die;
            }
        }
        echo json_encode(array());
    }

    /**
     * ???????????????????????? gaocl add 2018/04/20
     */
    public function search_accountplatform() {
        $accountid = trim($_REQUEST['search_value']);
        if(!empty($accountid)){

            $params = array(
                'fieldname'=>array(
                    'searchValue'	=> $accountid,
                ),
                'userid'			=> $this->userid
            );
            $result = $this->call('search_accountplatform', $params);
            $res = $result[0];

            //print_r($result);die;
            if (!empty($res)) {
                echo json_encode($res);die;
            }
        }
        echo json_encode(array());
    }

    /**
     * ???????????????????????????????????? gaocl add 2018/04/23
     */
    public function search_vendor_productservice() {
        $vendorid = trim($_REQUEST['search_value']);
        $accountid = trim($_REQUEST['accountid']);
        if(!empty($vendorid)){

            $params = array(
                'fieldname'=>array(
                    'searchValue'	=> $vendorid,
                    'accountid'     =>$accountid
                ),
                'userid'			=> $this->userid
            );
            $result = $this->call('search_vendor_productservice', $params);
            $res = $result[0];

            if (!empty($res)) {
                echo json_encode($res);die;
            }
        }
        echo json_encode(array());
    }

    /**
     * ?????????????????? gaocl add 2018/04/20
     */
    public function search_receivedpayments() {
        $servicecontractid = trim($_REQUEST['servicecontractid']);
        if(!empty($servicecontractid)){

            $params = array(
                'fieldname'=>array(
                    'searchValue'	=> $servicecontractid,
                ),
                'userid'			=> $this->userid
            );
            $result = $this->call('search_receivedpayments', $params);
            $res = $result[0];

            //print_r($result);die;
            if (!empty($res)) {
                echo json_encode($res);die;
            }
        }
        echo json_encode(array());
    }

	public function search_account() {
		$company = trim($_REQUEST['company']);
		if(!empty($company)){
			$params = array(
			'searchModule'		=>'Accounts',
			'searchValue'		=> $company,
			'relatedModule'		=>'',
			'userid'		=> $this->userid	
			);
			$list = $this->call('com_search_list', $params);
			echo json_encode($list);
			exit;
		}
		echo "";exit;
		print_r($list);
	}

    /**
     * ????????????????????????
     */
    public function search_accountzh() {
        $accountid = trim($_REQUEST['accountid']);
        $topplatform = trim($_REQUEST['topplatform']);
        //echo "test---".$accountid.$topplatform;
        //exit;
        if(!empty($accountid) && !empty($topplatform)){
            $params = array(
                'fieldname'=>array(
                    'accountid'	=>$accountid,
                    'topplatform'	=> $topplatform
                ),
                'userid'	=> $this->userid
            );
            $list = $this->call('search_refillapplication_accountzh', $params);
            $res = $list[0];
            echo json_encode($res);
            exit;
        }
        echo "";exit;
        //print_r($list);
    }

    /**
     * ?????????????????????
     */
    public function upload()
    {
        $file = $_FILES['uploadfiles'];
        $flag = true;
        $message = '??????????????????';
        switch ($file['error']) {
            case 0 :
                break;
            case 1 :
            case 2 :
                $flag = false;
                $message = "???????????????";
                break;
            case 3 :
                $flag = false;
                $message = "?????????????????????";
                break;
            case 4 :
                $flag = false;
                $message = "??????????????????!";
                break;
            default :
                $flag = false;
                $message = "????????????!";
                break;
        }
        if ($flag) {
            /*$tempdir=$_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'tempupload'.DIRECTORY_SEPARATOR;
            $time=time();
            $tepfilename=md5($file['name'].$time);
            $uploadstatus=move_uploaded_file($file['tmp_name'],$tempdir.$tepfilename);
            $tempfile = file_get_contents($tempdir.$tepfilename);*/
            $tempfile = file_get_contents($file['tmp_name']);
            if($file['type']=='application/octet-stream'){
                $filename=time();
                $filename=md5($filename).'.jpg';
                $filetype='image/jpeg';
            }else{
                $filename=$file['name'];
                $filetype=$file['type'];
            }
            $params = array(
                'fieldname' => array(
                    'module' => 'RefillApplication',
                    'filename' => $filename,
                    'filetype' => $filetype,
                    'filesize' => $file['size'],
                    'filecontents' => base64_encode($tempfile)
                ),
                'userid' => $this->userid
            );
            $list = $this->call('mobile_upload', $params);
            if(empty($list)){
                echo json_encode(array('success' => false, 'msg' => '????????????'));
                exit;
            }
            echo json_encode($list[0]);
            exit;
        } else {
            echo json_encode(array('success' => $flag, 'msg' => $message));
            exit;
        }


    }

    /**
     * ?????????????????????
     */
    public function download(){
        error_reporting(0);
        $params = array(
            'fieldname' => array(
                'module' => 'RefillApplication',
                'fileid' => urldecode($_REQUEST['filename'])
            ),
            'userid' => $this->userid
        );
        $list = $this->call('mobile_download', $params);

        ob_clean();
        header("Content-type: ".$list[0][1]);
        header("Pragma: public");
        header("Cache-Control: private");
        $openfileArray=array('image/bmp','image/gif','image/jpeg','image/png','image/tiff','image/x-icon');
        if(!in_array($list[0][1],$openfileArray)) {//????????????????????????,?????????????????????
            header("Content-Disposition: attachment; filename={$list[0][2]}");
        }
        header("Content-Description: PHP Generated Data");
        echo base64_decode($list[0][3]);

        exit;
    }
    /**
     * ??????????????????
     */
	public function submitremark(){
        $stagerecordid  = $_REQUEST['stagerecordid'];
        $record  = $_REQUEST['record'];
        $reject = $_REQUEST['reject'];
        if ($stagerecordid && $record) {
            $params = array(
                'fieldname'=>array(
                    'record'=> $record,
                    'module'=>'SalesorderWorkflowStages',
                    'mode'=>'submitremark',
                    'reject'=>$reject,
                    'isrejectid'=>$stagerecordid,
                    'src_module'=>'RefillApplication',
                    'action'=>'SaveAjax',
                    'actionnode'=>0
                ),
                'userid'			=> $this->userid
            );
            $tt = $this->call('salesorderWorkflowStagesRepulse', $params);
            echo $tt[0];die;
        }
    }

    /**
     * ?????????????????????????????????
     * @return mixed
     */
    public function getWeixinDepartMsg(){
        $cache_token=@file_get_contents('./wtoken.txt');
        $token=json_decode($cache_token,true);
        //$token['access_token']='iDsGpw7vRyG-T4VBqC3YuPjbi8_5vzuCgIcg_gMnAJXi7KMedCK38jkP7s91JF-k';
        $url='https://qyapi.weixin.qq.com/cgi-bin/user/list?access_token='.$token['access_token'].'&department_id=1&fetch_child=1&status=1';

        $ch  = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
    /**
     * @author: steel.liu
     * @Date:xxx
     *
     */
    public function searchAccount(){
        $company = trim($_REQUEST['company']);
        $field=trim($_REQUEST['field']);
        $data=array();
        if(!empty($company)){
            if($field=='accountid'){
                $module='Accounts';
                $search_key='accountname';
                $userid=$this->userid;
            }else{
                $module='Products';
                $search_key='productname';
                $field='productid';
                $userid=1;
            }

            $params = array(
                'fieldname'=>array('pagenum' 	=> 1,
                    'pagecount'  => 50,
                    'src_module'		=>'AccountPlatform',
                    'module'=>$module,
                    'search_value'		=> $company,
                    'search_key'		=>$search_key,
                    'userid'		=> $userid
                )
            );
            $list = $this->call('getComListImplements', $params);
            if(!empty($list[1])){
                $data=array();
                foreach($list[1] as $value){
                    $data[]=array('id'=>$value[$field],'label'=>$value[$search_key],'value'=>$value[$search_key]);
                }
                echo json_encode($data);
                exit;
            }
        }
        echo json_encode($data);
        exit;
    }
    public function getVendorInfo(){
        $company = trim($_REQUEST['productid']);
        $data=array('countnum'=>0);
        if(!empty($company)){

            $params = array(
                'fieldname'=>array('pagenum' 	=> 1,
                    'pagecount'  => 50,
                    'module'=>'AccountPlatform',
                    'productid'		=> $company,
                    'action'		=>'getVendorInfos',
                    //'userid'		=> $this->userid
                    'userid'		=> 2110
                )
            );
            $list = $this->call('getComRecordModule', $params);
            if(!empty($list[0])){
                echo json_encode($list[0]);
                exit;
            }
        }
        echo json_encode($data);exit;
    }
    public function doadd(){
        //print_r($_POST);
        $token='AccountPlatform'.$this->userid;
        if($this->getAddToken($token)){
            //$this->response(false,'????????????!');
            //exit;
        }
        $_REQUEST['module']='AccountPlatform';
        $_REQUEST['action']='Save';
        $_REQUEST['doAction']='updataModuleStatusM';
        $_REQUEST['userid']=$this->userid;
        $params = array(
            'fieldname'=>$_REQUEST,
            'userid' 	=> $this->userid
        );
        $res = $this->call('saveRecord', $params);
        if(!empty($res)&&!empty($res[0])){
            $data=$res[0];
            if(!empty($data['success'])){
                if($data['record']>0){
                    echo json_encode(array('success'=>true,'msg'=>'','record'=>$data['record']));
                }else{
                    echo json_encode(array('success'=>false,'msg'=>$data['msg']));
                }
            }else{
                echo json_encode(array('success'=>false,'msg'=>$data['msg']));
            }

        }else{
            echo json_encode(array('success'=>false,'msg'=>'????????????!'));
        }
    }
}

