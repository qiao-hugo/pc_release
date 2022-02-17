<?php

class ContractGuarantee extends baseapp{
	private $pagecount = 10;

	public function index(){
		$pagenum   = isset($_REQUEST['pagenum'])?$_REQUEST['pagenum']:1;
		$status = isset($_REQUEST['status'])?$_REQUEST['status']:'check';
        $pagecount  = $this->pagecount;
        $pagecount  = 20;
        $search = array(
            array(
                'field'		=>"vtiger_contractguarantee.modulestatus##16##11568##picklist",
                'operator'	=>"=",
                "value"		=> 'b_actioning',
                "andor"		=>"OR",
            ),
            array(
                'field'		=> "vtiger_contractguarantee.modulestatus##16##11568##picklist",
                'operator'	=> "=",
                "value"		=> 'b_check',
                "andor"		=> "And",
            ),
        );
        $search = $this->create_search_field($search);

        $params = array(
            'fieldname'=>array('pagenum' 	=> 1,
                'pagecount'  => $this->pagecount,
                'searchField'=> $search,
                'userid'		=> $this->userid,
                'module'=>'ContractGuarantee'
            ),
            'userid'		=> $this->userid
        );
        $list = $this->call('getComListImplements', $params);
        /*echo "<pre>";
        print_r($list);die;*/
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
        $this->smarty->assign('totalnum', $list[0]);
        $this->smarty->assign('list',$list[1]);
        $this->smarty->assign('USERIMGS',$arr);

        $this->smarty->assign('status', $status);

        $type = $_REQUEST['type'];
        if ($type == 'ajax') {
        	$this->smarty->display('ContractGuarantee/ajax.html');
        } else {
        	$this->smarty->display('ContractGuarantee/index.html');
        }
	}

	// 打回
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
					'src_module'=>'ContractGuarantee',
					'action'=>'SaveAjax',
					'actionnode'=>0
				),
				'userid'			=> $this->userid
			);
			$tt = $this->call('salesorderWorkflowStagesRepulse', $params);
			echo $tt[0];die;
		}
	}

	// 审核
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
					'src_module'=>'ContractGuarantee',
					'action'=>'SaveAjax',
					'customer'=>0,
					'customername'=>''
				),
				'userid'			=> $this->userid
			);
            $result = $this->call('salesorderWorkflowStagesExamine', $params);
            $result['result']=$result[1];
            echo json_encode($result);exit();
		}
	}

	public function one() {
		$id  = $_REQUEST['id'];
		if (! empty($id)) {
			
			$params = array(
				'fieldname'=>array( 
					'id'	=> $id,
					'module'	=> 'ContractGuarantee',
					'record'=> $id,
				),
				'userid'		=> $this->userid
			);
			$result = $this->call('oneContractGuarantee', $params);
            $arr = array();
            if (!empty($result[1]['REMARKLIST'])) {
                $userlist = $this->getWeixinDepartMsg();
                $userlist = json_decode($userlist, true);
                if ($userlist['errcode'] == 0) {
                    foreach ($userlist['userlist'] as $value) {
                        $arr[md5($value['userid'])] = $value['avatar'];
                    }
                }
            }
            $this->smarty->assign('detailInfo', $result[0]);
            $this->smarty->assign('moduleStatusArray',$this->modulestatus);
            $this->smarty->assign('USERIMGS', $arr);
            $this->smarty->assign('ISROLE', $result[1]['ISROLE']);    //是否有权限审核
            $this->smarty->assign('WORKFLOWSSTAGELIST', $result[1]['WORKFLOWSSTAGELIST']); //节点列表
            $this->smarty->assign('STAGERECORDID', $result[1]['STAGERECORDID']);  //当前工作流id
            $this->smarty->assign('STAGERECORDNAME', $result[1]['STAGERECORDNAME']);  //当前审核工作流的名字
            $this->smarty->assign('SALESORDERHISTORY', $result[1]['SALESORDERHISTORY']);  //历史打回原因记录
            $this->smarty->assign('REMARKLIST', $result[1]['REMARKLIST']);  //备注记录
            $this->smarty->assign('record', $id);
			$this->smarty->display('ContractGuarantee/one.html');
		}
	}
    public function add(){
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
        $result= $this->call('getComListImplements', $params);

        $this->smarty->assign('workflows', $result[1]);
        $this->smarty->display('ContractGuarantee/add.html');
    }
    /**
     * 搜索供应商 gaocl add 2018/04/19
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
     * 搜索供应商服务合同 gaocl add 2018/04/19
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
     * 获取用户平台信息 gaocl add 2018/04/20
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
     * 获取供应商产品服服务信息 gaocl add 2018/04/23
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
     * 获取回款信息 gaocl add 2018/04/20
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
     * 充值客户账户取得
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
     * 移动端文件上传
     */
    public function upload()
    {
        $file = $_FILES['uploadfiles'];
        $flag = true;
        $message = '文件上传成功';
        switch ($file['error']) {
            case 0 :
                break;
            case 1 :
            case 2 :
                $flag = false;
                $message = "文件过大！";
                break;
            case 3 :
                $flag = false;
                $message = "错误上传文件！";
                break;
            case 4 :
                $flag = false;
                $message = "没有选择文件!";
                break;
            default :
                $flag = false;
                $message = "系统错误!";
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
                echo json_encode(array('success' => false, 'msg' => '上传失败'));
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
     * 移动端文件下载
     */
    public function download($fileid=0,$module='ServiceContracts'){
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
        if(!in_array($list[0][1],$openfileArray)) {//只有图片直接打开,其它的下载方式
            header("Content-Disposition: attachment; filename={$list[0][2]}");
        }
        header("Content-Description: PHP Generated Data");
        echo base64_decode($list[0][3]);

        exit;
    }
    /**
     * 添加备注信息
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
            echo $tt[0];exit();//
        }
    }

    /**
     * 微信取商务下的人员信息
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
            //$this->response(false,'操作过期!');
            //exit;
        }
        $_REQUEST['module']='AccountPlatform';
        $_REQUEST['record']='';
        $_REQUEST['action']='Save';
        $_REQUEST['doAction']='updataModuleStatusM';
        $_REQUEST['checkAction']='checkIdAndAccountplatformM';
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
            echo json_encode(array('success'=>false,'msg'=>'添加失败!'));
        }
    }
}

