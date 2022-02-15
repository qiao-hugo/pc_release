<?php

class RefillApplication extends baseapp{
	private $pagecount = 10;

	public function index(){
		$pagenum   = isset($_REQUEST['pagenum'])?$_REQUEST['pagenum']:1;
		$status = $_REQUEST['status']?$_REQUEST['status']:'check';
        $pagecount  = $this->count;
        $pagecount  = 20;
        $params  = array(
            'fieldname'=>array(
                "module"     =>'RefillApplication',
                'pagenum'    => $pagenum,
                'pagecount'  => $pagecount,
                'userid'     => $this->userid,
                'pagecount'=> $pagecount,
            	'modulestatus' => $status,
            ),
            'userid'    => $this->userid
        );
        $searchfieldname='';
        $searchfieldvalue='';
        $searchflag=1;
        if(!empty($_REQUEST['searchvalue'])){
            $searchfieldname=$fieldname=isset($_REQUEST['radiot'])?$_REQUEST['radiot']:'vtiger_refillapplication.refillapplicationno';
            $searchfieldvalue=$_REQUEST['searchvalue'];
            $params['fieldname']['filter']['search_key']=$searchfieldname;
            $params['fieldname']['filter']['search_value']="'%".$searchfieldvalue."%'";
		    $params['fieldname']['filter']['operator']=' like ';
            $searchflag=2;

        }
        $list = $this->call('getRefillApplication', $params);
        //print_r($list);die;
        //$num=ceil($list[0]/$pagecount);
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
        $sumpage = intval(($list[0]+1)/$pagecount);
        $this->smarty->assign('list',$list[1]);
        $this->smarty->assign('USERIMGS',$arr);

        $this->smarty->assign('fieldname',$searchfieldname);
        $this->smarty->assign('fieldvalue',$searchfieldvalue);
        $this->smarty->assign('searchflag',$searchflag);
        $this->smarty->assign('status', $status);

        $type = $_REQUEST['type'];
        if ($type == 'ajax') {
        	$this->smarty->display('RefillApplication/ajax.html');
        } else {
        	$this->smarty->display('RefillApplication/index.html');
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
					'src_module'=>'RefillApplication',
					'action'=>'SaveAjax',
					'customer'=>0,
					'customername'=>''
				),
				'userid'			=> $this->userid
			);
			$this->call('salesorderWorkflowStagesExamine', $params);
		}
	}

	public function one() {
		$id  = $_REQUEST['id'];
		if (! empty($id)) {
			
			$params = array(
				'fieldname'=>array( 
					'id'	=> $id,
					'module'	=> 'RefillApplication',
					'record'=> $id,
				),
				'userid'		=> $this->userid
			);
			$result = $this->call('oneRefillApplication', $params);
           $arr=array();
            if(!empty($result[2]['REMARKLIST'])) {
                $userlist = $this->getWeixinDepartMsg();
                $userlist = json_decode($userlist, true);
                if ($userlist['errcode'] == 0) {
                    foreach ($userlist['userlist'] as $value) {
                        $arr[md5($value['userid'])] = $value['avatar'];
                    }
                }
            }


            $rechargesourceArray=array('Vendors'=>'媒体充值(外采)',
                'TECHPROCUREMENT'=>'工单外采','PreRecharge'=>'预充值',
                'NonMediaExtraction'=>'非媒体类外采','PACKVENDORS'=>'打包充值',
                'COINRETURN'=>'退币转充','INCREASE'=>'赠款申请',
                'contractChanges'=>'合同变更申请','Accounts'=>'媒体充值'
                );
            // 这样写是因为 合同变更申请包含所有的其他的只是包含 直客渠道
            $customersType=array('StraightCustomers'=>'直客','ChannelCustomers'=>'渠道','businesspurchasing'=>'业务采购','administrativepurchase'=>'行政采购','MediaProvider'=>'媒介');
            $result[0]['t_customertype']=in_array($result[0]['t_customertype'],$customersType)? $result[0]['t_customertype']:$customersType[$result[0]['t_customertype']] ;
            $this->smarty->assign('refillApplication', $result[0]);
            $this->smarty->assign('rechargesheet', $result[1]);
            //$this->smarty->assign('advancesmoney', $result[3]);//获得垫款
            $this->smarty->assign('refillapprayment', $result[3]); //回款明细
            $this->smarty->assign('refundlist', $result[4]); //红充明细
            $this->smarty->assign('vendorlist', $result[5]); //打包充值明细
            $this->smarty->assign('refillapplicationList', $result[6]); //合同变更充值申请单的详情列表
            $this->smarty->assign('customersType',$customersType);
            $this->smarty->assign('rechargesourceArray',$rechargesourceArray);
            $tmp_rechargesource = $result[0]['rechargesource'];
            $rechargesource_name = "";
            if ($tmp_rechargesource == 'Vendors'){
                $rechargesource_name = "【媒体充值(外采)】";
            }else if($tmp_rechargesource == 'TECHPROCUREMENT'){
                $rechargesource_name = "【工单外采】";
            }else if($tmp_rechargesource == 'PreRecharge'){
                $rechargesource_name = "【预充值】";
            }else if($tmp_rechargesource == 'NonMediaExtraction'){
                $rechargesource_name = "【非媒体类外采】";
            }else if($tmp_rechargesource == 'PACKVENDORS'){
                $rechargesource_name = "【打包充值】";
            }else if($tmp_rechargesource == 'COINRETURN'){
                $rechargesource_name = "【退币转充】";
            }else if($tmp_rechargesource == 'INCREASE'){
                $rechargesource_name = "【赠款申请】";
            }else if($tmp_rechargesource == 'contractChanges'){
                $rechargesource_name = "【合同变更申请】";
            } else {
                $rechargesource_name = "【媒体充值】";
            }
            $this->smarty->assign('moduleStatusArray',$this->modulestatus);
            $this->smarty->assign('rechargesource', $tmp_rechargesource);//申请单类型
            $this->smarty->assign('rechargesource_name', $rechargesource_name);//申请单类型名称

            $this->smarty->assign('USERIMGS',$arr);
			$this->smarty->assign('WORKFLOWSSTAGELIST', $result[2]['WORKFLOWSSTAGELIST']); //节点列表
			$this->smarty->assign('ISROLE', $result[2]['ISROLE']);    //是否有权限审核
			$this->smarty->assign('STAGERECORDID', $result[2]['STAGERECORDID']);  //当前工作流id
			$this->smarty->assign('STAGERECORDNAME', $result[2]['STAGERECORDNAME']);  //当前审核工作流的名字
			$this->smarty->assign('SALESORDERHISTORY', $result[2]['SALESORDERHISTORY']);  //回款历史记录
			$this->smarty->assign('REMARKLIST', $result[2]['REMARKLIST']);  //备注记录
			$this->smarty->assign('record', $id);
			$this->smarty->display('RefillApplication/one.html');
		}
	}
	
	/**
	 * 回款
	 */
	public function receive(){
		$id  = $_REQUEST['id'];//服务合同id
		if(!empty($id)){
			$params = array(
				'fieldname'=>array(
					'id'	=> $id,
				),
				'userid'			=> $this->userid
			);
			//print_r($params);
			$result = $this->call('receiveReceivedPayments', $params);
			$result = $result[0];
			$this->smarty->assign('receiveData', $result);
			$this->smarty->display('RefillApplication/receive.html');
			//print_r($result);
		}
	}

	public function search_servicecontracts() {
		$servicecontracts = trim($_REQUEST['search_servicecontracts']);
		if(!empty($servicecontracts)){

			$params = array(
				'fieldname'=>array( 
					'searchValue'	=> $servicecontracts,
				),
				'userid'			=> $this->userid	
			);
			$result = $this->call('search_servicecontracts', $params);
			$res = $result[0];

			//print_r($result);die;
			if (!empty($res)) {
				echo json_encode($res);die;
			}
		}
		echo json_encode(array());
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
        //如果是退币转冲 传参数Type=1
        $type=trim($_REQUEST['type']);
        if(!empty($vendorid)){
            $params = array(
                'fieldname'=>array(
                    'searchValue'	=> $vendorid,
                    'accountid'     =>$accountid,
                    'type'          =>$type
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
    public function download(){
        error_reporting(0);
        $fileid=base64_encode($_REQUEST['filename']);
        $params = array(
            'fieldname' => array(
                'module' => 'RefillApplication',
                'fileid' => urldecode($fileid)
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
	public function doadd() {
        $token='refillapplication'.$this->userid;
        if($this->getAddToken($token)){
            echo json_encode(array('success'=>2, 'msg'=>'已提交!'));
            exit;
        }
		// 组装数据
		$refillApplicationData = array();
	    $refillApplicationData['module'] = 'RefillApplication';
	    $refillApplicationData['action'] = 'Save';
	    $refillApplicationData['workflowsid'] = '397103';
	    $refillApplicationData['popupReferenceModule'] = 'Workflows';
	    $refillApplicationData['servicecontractsid'] = $_POST['servicecontractsid'];
        $refillApplicationData['accountid'] = $_POST['accountid'];
        $refillApplicationData['customertype'] = $_POST['customertype'];
	    //$refillApplicationData['customeroriginattr'] = $_POST['customeroriginattr'];
        $refillApplicationData['contractamount'] = $_POST['contractamount'];
	    $refillApplicationData['remarks'] = $_POST['remarks'];
	    $refillApplicationData['flow_state'] = $_POST['flow_state'];
		/*if(!empty($_POST['files'])){
            $refillApplicationData['file'] = trim(implode('*|*',$_POST['files']),'*|*');
        }*/
        $refillApplicationData['file']=$_POST['fileupload'];
        $refillApplicationData['srcterminal'] = 1;//1:移动端,2:PC端
        //申请单类型
        $rechargesourced=$_POST['rechargesource'];
        $refillApplicationData['rechargesource'] = $rechargesourced;
        $refillApplicationData['receivedstatus'] = $_POST['receivedstatus'];
        if($rechargesourced=='Vendors'){
            $refillApplicationData['paymentperiod'] = $_POST['paymentperiod'];
        }
        //客户类型
        $refillApplicationData['customertype'] = $_POST['customertype'];
        //合同签订日期
        $refillApplicationData['servicesigndate'] = $_POST['servicesigndate'];
        //垫款预计回款日期
        $refillApplicationData['expcashadvances'] = $_POST['expcashadvances'];
        //合同垫款金额
        $refillApplicationData['grossadvances'] = $_POST['grossadvances'];
        //现金总额
        $refillApplicationData['totalrecharge'] = $_POST['totalrecharge'];
        //应收款总额
        $refillApplicationData['actualtotalrecharge'] = $_POST['actualtotalrecharge'];
        //应付款总额
        $refillApplicationData['totalreceivables'] = $_POST['totalreceivables'];
        //是否签订合同
        $refillApplicationData['iscontracted'] = $_POST['iscontracted'];
        //供应商id
        $refillApplicationData['vendorid'] = $_POST['vendorid'];
        $refillApplicationData['bankaccount'] = $_POST['bankaccount'];
        $refillApplicationData['bankname'] = $_POST['bankname'];
        $refillApplicationData['banknumber'] = $_POST['banknumber'];

        //回款明细
        if(!empty($_POST['insertii'])){
            $receivedpaymentsData = array();
            foreach($_POST['insertii'] as $key=>$value){
                $refillapptotal = $_POST['refillapptotal'][$value];
                if(empty($refillapptotal) || $refillapptotal == '0'){
                    continue;
                }
                $tarray=array();
                $tarray['servicecontractsid'] = $_POST['servicecontractsid'];
                $tarray['receivedpaymentsid']=$_POST['insertii'][$value]; //回款id
                $tarray['total']=$_POST['unit_price'][$value]; //入账金额
                $tarray['arrivaldate']=$_POST['reality_date'][$value]; //入账日期
                $tarray['refillapptotal']=$_POST['refillapptotal'][$value]; //充值现金
                $tarray['allowrefillapptotal']=$_POST['rechargeableamount'][$value]; //可使用充值金额
                $tarray['paytitle']=$_POST['paytitle'][$value]; //回款信息
                $tarray['owncompany']=$_POST['owncompany'][$value]; //公司
                $tarray['remarks']=$_POST['remarkss'][$value]; //备注
                $receivedpaymentsData[] = $tarray;
            }
        }

	    //充值明细
        if(!empty($_POST['inserti'])){
            $rechargesheetData = array();
            foreach($_POST['inserti'] as $key=>$value){
            	$tarray=array();
            	if($refillApplicationData['rechargesource'] == 'Vendors'){
                    //充值明细-供应商
                    $tarray['supprebate']=$_POST['supprebate'][$value];
                    $tarray['topplatform']=$_POST['productid_display'][$value];
                    $tarray['customeroriginattr'] = $_POST['customeroriginattr'][$value]; //客户来源属性
                    $tarray['productid']=$_POST['productid'][$value];
                    $tarray['productservice']=$_POST['productid'][$value];
                    $tarray['suppliercontractsid']=$_POST['suppliercontractsid'][$value];
                    $tarray['havesignedcontract']=$_POST['havesignedcontract'][$value];
                    $tarray['signdate']=$_POST['signdate'][$value];
                    $tarray['isprovideservice']=$_POST['isprovideservice'][$value];
                    $tarray['rechargetypedetail']=$_POST['rechargetypedetail'][$value];
                    $tarray['receivementcurrencytype']=$_POST['receivementcurrencytype'][$value];
                    $tarray['exchangerate']=$_POST['exchangerate'][$value];
                    $tarray['prestoreadrate']=$_POST['prestoreadrate'][$value];
                    $tarray['rechargeamount']=$_POST['rechargeamount'][$value];
                    $tarray['discount']=$_POST['discount'][$value];
                    $tarray['tax']=$_POST['tax'][$value];
                    $tarray['factorage']=$_POST['factorage'][$value];
                    $tarray['activationfee']=$_POST['activationfee'][$value];
                    $tarray['taxation']=$_POST['taxation'][$value];
                    $tarray['totalcost']=$_POST['totalcost'][$value];
                    $tarray['transferamount']=$_POST['transferamount'][$value];
                    $tarray['servicecost']=$_POST['servicecost'][$value];
                    $tarray['totalgrossprofit']=$_POST['totalgrossprofit'][$value];
                    $tarray['accountzh']=$_POST['accountzh'][$value];
                    $tarray['rebatetype']=$_POST['rebatetype'][$value];
                    $tarray['accountrebatetype']=$_POST['accountrebatetype'][$value];
                    $tarray['did']=$_POST['did'][$value];
                }else{
                    //充值明细-客户
                    $tarray['supprebate']=$_POST['supprebate'][$value];
                    $tarray['topplatform']=$_POST['productid_display'][$value];
                    $tarray['customeroriginattr'] = $_POST['customeroriginattr'][$value]; //客户来源属性
                    $tarray['accountzh']=$_POST['accountzh'][$value];
                    $tarray['did']=$_POST['did'][$value];
                    $tarray['productid']=$_POST['productid'][$value];
                    $tarray['isprovideservice']=$_POST['isprovideservice'][$value];
                    $tarray['rechargetypedetail']=$_POST['rechargetypedetail'][$value];
                    $tarray['receivementcurrencytype']=$_POST['receivementcurrencytype'][$value];
                    $tarray['exchangerate']=$_POST['exchangerate'][$value];
                    $tarray['prestoreadrate']=$_POST['prestoreadrate'][$value];
                    $tarray['rechargeamount']=$_POST['rechargeamount'][$value];
                    $tarray['discount']=$_POST['discount'][$value];
                    $tarray['tax']=$_POST['tax'][$value];
                    $tarray['factorage']=$_POST['factorage'][$value];
                    $tarray['activationfee']=$_POST['activationfee'][$value];
                    $tarray['factorage']=$_POST['factorage'][$value];
                    $tarray['totalcost']=$_POST['totalcost'][$value];
                    $tarray['transferamount']=$_POST['transferamount'][$value];
                    $tarray['servicecost']=$_POST['servicecost'][$value];
                    $tarray['taxation']=$_POST['taxation'][$value];
                    $tarray['totalgrossprofit']=$_POST['totalgrossprofit'][$value];
                    $tarray['rebatetype']=$_POST['rebatetype'][$value];
                    $tarray['accountrebatetype']=$_POST['accountrebatetype'][$value];
                }
                $tarray['createdid']=$this->userid;
                $tarray['createdtime']=date('Y-m-d H:i:s');
                $rechargesheetData[] = $tarray;
            }
        }

        if (count($rechargesheetData) > 0) {
        	$refillApplicationData = array_merge($refillApplicationData, $rechargesheetData[0]);
        	unset($rechargesheetData[0]);
        }

        //充值明细数据设置
        $refillApplicationData['receivedpaymentsData'] = $receivedpaymentsData;
        $refillApplicationData['rechargesheetData'] = $rechargesheetData;

        //print_r($refillApplicationData);die();
        $params = array(
            'fieldname' => array('refillApplicationData'=>$refillApplicationData, 'rechargesheetData'=>$rechargesheetData),
            'userid' => $this->userid
        );

        $result=$this->call('addRefillApplication', $params);
        echo json_encode($result[0]);
        exit();
        //echo json_encode(array($result[0], $result[1]));die();
	}

    /**
     * @author: steel.liu
     * @Date:xxx
     * 退币转充
     */
    public function doaddCOINRETURN() {
        $token='doaddCOINRETURN'.$this->userid;
        if($this->getAddToken($token)){
            echo json_encode(array('success'=>2, 'msg'=>'已提交!'));
            exit;
        }
        // 组装数据
        $refillApplicationData = array();
        $refillApplicationData['module'] = 'RefillApplication';
        $refillApplicationData['action'] = 'Save';
        $refillApplicationData['createdworkflows'] = 1;
        $refillApplicationData['workflowsid'] = '397103';
        $refillApplicationData['popupReferenceModule'] = 'Workflows';
        $refillApplicationData['servicecontractsid'] = $_POST['servicecontractsid'];
        $refillApplicationData['accountid'] = $_POST['accountid'];
        $refillApplicationData['customertype'] = $_POST['customertype'];
        $refillApplicationData['contractamount'] = $_POST['contractamount'];
        $refillApplicationData['totalcashin'] = $_POST['totalcashin'];
        $refillApplicationData['totalcashtransfer'] = $_POST['totalcashtransfer'];
        $refillApplicationData['totaltransfertoaccount'] = $_POST['totaltransfertoaccount'];
        $refillApplicationData['totalturnoverofaccount'] = $_POST['totalturnoverofaccount'];
        $refillApplicationData['cashtransfer'] = $_POST['cashtransfer'];
        $refillApplicationData['accounttransfer'] = $_POST['accounttransfer'];
        $refillApplicationData['remarks'] = $_POST['remarks'];

        $refillApplicationData['file']=$_POST['fileupload'];
        $refillApplicationData['srcterminal'] = 1;//1:移动端,2:PC端
        //申请单类型
        $refillApplicationData['rechargesource'] = $_POST['rechargesource'];
        //客户类型
        $refillApplicationData['did'] = $_POST['did'];
        $refillApplicationData['accountzh'] = $_POST['accountzh'];
        $refillApplicationData['productid'] = $_POST['productid'];
        $refillApplicationData['isprovideservice'] = $_POST['isprovideservice'];
        $refillApplicationData['isprovideservice_display'] = $_POST['isprovideservice_display'];
        $refillApplicationData['accountrebatetype'] = $_POST['accountrebatetype'];
        $refillApplicationData['discount'] = $_POST['discount'];
        $refillApplicationData['cashtransfer'] = $_POST['cashtransfer'];
        $refillApplicationData['accounttransfer'] = $_POST['accounttransfer'];
        $refillApplicationData['conversiontype'] = $_POST['conversiontype'];
        $refillApplicationData['vendorid'] = $_POST['vendorid'];
        $tarray=array();
        foreach($_POST['mdid'] as $key=>$value){
            $tarray['mid'][$key]=$value;
            $tarray['truncashtype'][$key]=$_POST['mtruncashtype'][$key];
            $tarray['mproductid'][$key]=$_POST['mproductid'][$key];
            $tarray['mproductid_display'][$key] = $_POST['mproductid_display'][$key]; //客户来源属性
            $tarray['maccountzh'][$key]=$_POST['maccountzh'][$key];
            $tarray['maccountrebatetype'][$key]=$_POST['maccountrebatetype'][$key];
            $tarray['misprovideservice'][$key]=$_POST['misprovideservice'][$key];
            $tarray['mdiscount'][$key]=$_POST['mdiscount'][$key];
            $tarray['mcashtransfer'][$key]=$_POST['mcashtransfer'][$key];
            $tarray['maccounttransfer'][$key]=$_POST['maccounttransfer'][$key];
        }
        $refillApplicationData = array_merge($refillApplicationData, $tarray);
        $refillApplicationData['record']='';
        $refillApplicationData['userid']=$this->userid;
        $params = array(
            'fieldname' => $refillApplicationData,
            'userid' => $this->userid
        );
        $result=$this->call('saveRecord', $params);
        echo json_encode($result[0]);
        exit();

    }
	public function add(){
        $token='refillapplication'.$this->userid;
        $this->setAddToken($token);
		$params = array(
			'fieldname'=>array( 
				'searchValue'	=> '',
			),
			'userid'			=> $this->userid	
		);
		$topplatform = $this->call('refill_application_topplatform', $params);
        require_once "jssdk.php";
        //$jssdk = new JSSDK("wx74d59c197d3976ee", "8afc371fd3c51ee97d3d8f93647fe219");
        $jssdk = new JSSDK("wx4d2151259aa58eba", "9n5ih34K5fFxuwAUJRiLhGY_HPvtA9p79VPfA4ltIgdsjTCGQOTWMCF6FEANlg_d");
        $signPackage = $jssdk->GetSignPackage();
        $this->smarty->assign('signPackage',$signPackage);
        $this->smarty->assign('userid', $this->userid);

		//print_r($topplatform);die;
		$this->smarty->assign('topplatform', $topplatform[0]);
		$this->smarty->display('RefillApplication/add.html');
	}
	public function addlist(){
        $this->smarty->display('RefillApplication/addgrid.html');
    }
    public function addcoinreturn(){
        $token='doaddCOINRETURN'.$this->userid;
        $this->setAddToken($token);
        require_once "jssdk.php";
        //$jssdk = new JSSDK("wx74d59c197d3976ee", "8afc371fd3c51ee97d3d8f93647fe219");
        $jssdk = new JSSDK("wx4d2151259aa58eba", "9n5ih34K5fFxuwAUJRiLhGY_HPvtA9p79VPfA4ltIgdsjTCGQOTWMCF6FEANlg_d");
        $signPackage = $jssdk->GetSignPackage();
        $this->smarty->assign('signPackage',$signPackage);
        $this->smarty->assign('userid', $this->userid);
        $this->smarty->display('RefillApplication/addcoinreturn.html');
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
            echo $tt[0];die;
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
     *验证一下客户担保是否需要二级审核
     */
    public function checkAuditInformation(){
        $accountid=$_POST['accountid'];
        $advancesmoney=$_POST['advancesmoney'];
        if($advancesmoney>0){
            $params = array(
                'fieldname'=>array(
                    'accountid'	=> $accountid,
                    'advancesmoney'=>$advancesmoney
                ),
                'userid'			=> $this->userid
            );
            $result = $this->call('checkAuditInformation', $params);
            $res = $result[0];
            if (!empty($res)) {
                echo json_encode($res);die;
            }
        }
        echo json_encode(array());
    }
    public function photograph(){
        $result=$this->file_upload(array('module' => 'RefillApplication'));
        //$result['result']['id']=urlencode(base64_encode($result['result']['id']));
        echo json_encode($result);
        exit;
    }
    /*public function photograph(){
        $record=$_POST['record'];
        $style=$_POST['style'];
        $pictureid=$_POST['pictureid'];
        $url="https://api.weixin.qq.com/cgi-bin/media/get?media_id={$pictureid}&access_token=";
        $tempfile = $this->getWeixinMsg($url);
        $params = array(
            'fieldname' => array(
                'module' => 'RefillApplication',
                'filename' => "image.jpg",
                'filetype' => 'image/jpeg',
                'filesize' => 50,
                'filecontents' => base64_encode($tempfile)
            ),
            'userid' => $this->userid
        );

        $list = $this->call('mobile_upload', $params);
        echo json_encode($list[0]);
        exit;
    }*/
}

