<?php

class VisitingOrder extends baseapp{
	private $pagecount = 10;
	public function index(){
		$this->smarty->display('VisitingOrder/index.html');
	}

	#我的拜访单 七日需跟进客户
	public function vlist(){
		$this->smarty->assign('title','主页');
		$search = array(
				array(
				  'field'		=>"vtiger_visitingorder.startdate##6##1744##date",
				  'operator'	=>">=",
				  "value"		=> date('Y-m-d'),
				  "andor"		=>"And",
				),
				array(
				  'field'		=> "vtiger_visitingorder.startdate##6##1744##date",
				  'operator'	=> "<=",
				  "value"		=> date('Y-m-d',strtotime('+ 10 day')),
				  "andor"		=> "And",
				),
		);

		$search = $this->create_search_field($search);

		$params = array(
		'fieldname'=>array('pagenum' 	=> 1,
						   'pagecount'  => $this->pagecount,
						   'searchField'=> $search,
						   'userid'		=> $this->userid
						   )
		);
		#今日需拜访
		//$today_list = $this->call('get_VisitingOrder', $params);
        $arr=array();

		/*if(!empty($today_list[1])){
            $userlist = $this->getWeixinDepartMsg();
            $userlist = json_decode($userlist, true);
            if ($userlist['errcode'] == 0) {
                foreach ($userlist['userlist'] as $value) {
                    $arr[md5($value['userid'])] = $value['avatar'];
                }
            }
			$this->smarty->assign('today_sum',$today_list[0]);	
			$this->smarty->assign('today_list',$today_list[1]);			
		}else{*/
			$this->smarty->assign('today_list',array());	
			$this->smarty->assign('today_sum', 0);	
		//}
		$this->smarty->assign('USERIMGS',$arr);



        //回款列表信息;
        $payment_params = array(
        	'userid'=>$this->userid, 
        	'fieldname'=>array('pagenum'=>'1'));
        $my_payment = $this->call('get_my_receivepayment', $payment_params);

        if(empty($my_payment[1])){
            $this->smarty->assign('payment_page', 0);
        	$this->smarty->assign('my_payment', array());
        } else {
        	$this->smarty->assign('payment_page', $my_payment[0]);
        	$this->smarty->assign('my_payment', $my_payment[1]);
        }
		
		

		#7日未跟进
		$params = array(
		'fieldname'=>array('pagenum' 	=> 1,
						   'pagecount'  => $this->pagecount,
						   //'filter'		=> 'appnoseven',
						   'filter'		=> 'noseven',
						   'userid'		=> $this->userid
						   )
		);
		$seven_list = $this->call('get_my_account', $params);
		if(!empty($seven_list[1])){
			$this->smarty->assign('my_account_sum', $seven_list[0]);	
			$this->smarty->assign('list', $seven_list[1]);
		}else{
			$this->smarty->assign('my_account_sum', 0);	
			$this->smarty->assign('list', array());
		}


		$weekarray=array("日","一","二","三","四","五","六");
		$dateinfo 	= array('date'=>date('m月d日'),
							'week'=>$weekarray[date("w")],
							'apm' =>(intval(date('H')))>=12?'下午好':'上午好');
							
		$this->smarty->assign('dateinfo',$dateinfo);

		$this->smarty->assign('userid', $this->userid);
		$this->smarty->display('VisitingOrder/list.html');
	}

	# ajax获取 7日未跟进
	public function ajax_my_account_list() {
		$pagenum = isset($_REQUEST['pagenum']) ? $_REQUEST['pagenum'] : 1;
		#7日未跟进
		$params = array(
		'fieldname'=>array('pagenum' 	=> $pagenum,
						   'pagecount'  => $this->pagecount,
						   //'filter'		=> 'appnoseven',
						   'filter'		=> 'noseven',
						   'userid'		=> $this->userid
						   )
		);
		$seven_list = $this->call('get_my_account', $params);

		if(!empty($seven_list[1])){
			$this->smarty->assign('my_account_sum', $seven_list[0]);	
			$this->smarty->assign('list', $seven_list[1]);
		}else{
			$this->smarty->assign('my_account_sum', 0);	
			$this->smarty->assign('list', array());
		}
		$this->smarty->display('VisitingOrder/ajax_my_account_list.html');
	}

	# ajax获取拜访单
	public function ajax_visiting_order_list() {
		$pagenum = isset($_REQUEST['pagenum']) ? $_REQUEST['pagenum'] : 1;
		$search = array(
				array(
				  'field'		=>"vtiger_visitingorder.startdate##6##1744##date",
				  'operator'	=>">=",
				  "value"		=> date('Y-m-d'),
				  "andor"		=>"And",
				),
				array(
				  'field'		=> "vtiger_visitingorder.startdate##6##1744##date",
				  'operator'	=> "<=",
				  "value"		=> date('Y-m-d',strtotime('+ 10 day')),
				  "andor"		=> "And",
				),
		);
		$search = $this->create_search_field($search);
		$params = array(
		'fieldname'=>array('pagenum' 	=> $pagenum,
						   'pagecount'  => $this->pagecount,
						   'searchField'=> $search,
						   'userid'		=> $this->userid
						   )
		);
		$today_list = $this->call('get_VisitingOrder', $params);
		$arr=array();
		if(!empty($today_list[1])) {
            $userlist = $this->getWeixinDepartMsg();
            $userlist = json_decode($userlist, true);
            if ($userlist['errcode'] == 0) {
                foreach ($userlist['userlist'] as $value) {
                    $arr[md5($value['userid'])] = $value['avatar'];
                }
            }
        }
        $this->smarty->assign('USERIMGS',$arr);
		$this->smarty->assign('today_sum',$today_list[0]);	
		$this->smarty->assign('today_list',$today_list[1]);	
		$this->smarty->display('VisitingOrder/ajax_visiting_order_list.html');
	}
    # ajax获取拜访单
    public function ajax_visiting_order_listnew() {
        $pagenum = isset($_REQUEST['pagenum']) ? $_REQUEST['pagenum'] : 1;
        $search = array(
            array(
                'field'		=>"vtiger_visitingorder.startdate##6##1744##date",
                'operator'	=>">=",
                "value"		=> date('Y-m-d'),
                "andor"		=>"And",
            ),
            array(
                'field'		=> "vtiger_visitingorder.startdate##6##1744##date",
                'operator'	=> "<=",
                "value"		=> date('Y-m-d',strtotime('+ 10 day')),
                "andor"		=> "And",
            ),
        );
        $search = $this->create_search_field($search);
        $params = array(
            'fieldname'=>array('pagenum' 	=> $pagenum,
                'pagecount'  => $this->pagecount,
                'searchField'=> $search,
                'userid'		=> $this->userid
            )
        );
        $today_list = $this->call('get_VisitingOrder', $params);
        $arr=array();
        $this->smarty->assign('today_list',$today_list[1]);
        $data= $this->smarty->fetch('VisitingOrder/ajax_visiting_order_listnew.html');
        echo json_encode(array('success'=>!empty($today_list[1]),'data'=>$data));
    }

	# ajax获取我的回款
	public function ajax_my_receivepayment_list() {
		$pagenum = isset($_REQUEST['pagenum']) ? $_REQUEST['pagenum'] : 1;
		//回款列表信息;
        $payment_params = array(
        	'userid'=>$this->userid, 
        	'fieldname'=>array('pagenum'=> $pagenum));
        $my_payment = $this->call('get_my_receivepayment', $payment_params);

        if(empty($my_payment)){
            $my_payment = array();
        }
        $this->smarty->assign('payment_page', $my_payment[0]);
        $this->smarty->assign('my_payment', $my_payment[1]);
        $this->smarty->display('VisitingOrder/ajax_my_receivepayment_list.html');
	}

	#查看拜访单
	public function allList(){
	    return $this->allListnew();
		$pagenum 	= isset($_REQUEST['pagenum'])?$_REQUEST['pagenum']:1;
		$searchfilename 	= isset($_REQUEST['searchfilename'])?$_REQUEST['searchfilename']:'accountname';
		$searchvalue='';
        $search='';
        if(isset($_REQUEST['searchvalue']) && !empty($_REQUEST['searchvalue'])){
            $searchvalue=$_REQUEST['searchvalue'];
            if($searchfilename=='startdate'){
                $search = array(
                    array(
                        'field'		=>"vtiger_visitingorder.startdate##6##1744##date",
                        'operator'	=>">=",
                        "value"		=> $_REQUEST['searchvalue'].' 00:00:00',
                        "andor"		=>"And",
                    ),array(
                        'field'		=>"vtiger_visitingorder.startdate##6##1744##date",
                        'operator'	=>"<=",
                        "value"		=> $_REQUEST['searchvalue'].' 23:59:59',
                        "andor"		=>"And",
                    ));
            }else if($searchfilename=='extractid'){
                $search = array(

                    array(
                        'field'		=>"vtiger_visitingorder.extractid##53##1748##owner",
                        'operator'	=>"=",
                        "value"		=> $_REQUEST['searchvalue'],
                        "andor"		=>"And",
                    ));
            }elseif($searchfilename=='accountname'){
            $search = array(
                array(
                    'field'		=>"vtiger_visitingorder.accountnamer##1##3687##string",
                    'operator'	=>"LIKE",
                    "value"		=> $_REQUEST['searchvalue'],
                    "andor"		=>"And",
                )
            );
            }

        }
		if($_REQUEST['modulestatus']==1){
            $search[]=array(
                    'field'		=>"vtiger_visitingorder.modulestatus##15##1922##picklist",
                    'operator'	=>"=",
                    "value"		=> "a_normal",
                    "andor"		=>"And",
                );
            $search[]=array(
                'field'		=>"vtiger_visitingorder.auditorid##53##3610##owner",
                'operator'	=>"=",
                "value"		=> $this->userid,
                "andor"		=>"And",
            );
        }elseif($_REQUEST['modulestatus']==2){
            $search[]=array(
                    'field'		=>"vtiger_visitingorder.modulestatus##15##1922##picklist",
                    'operator'	=>"=",
                    "value"		=> "a_normal",
                    "andor"		=>"And"
                );
        }elseif($_REQUEST['modulestatus']==3){
            $search[]=array(
                'field'		=>"vtiger_visitingorder.modulestatus##15##1922##picklist",
                'operator'	=>"=",
                "value"		=> "c_complete",
                "andor"		=>"And"
            );
        }
        $search = $this->create_search_field($search);
		$params  = array(
			'fieldname'=>array('pagenum' 	=> $pagenum,
						   	   'pagecount'  => $this->pagecount,
                                'searchField'=> $search,
						   	   'userid'		=> $this->userid  
						   )
		);
		$list = $this->call('get_VisitingOrder', $params);
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

        $params  = array(
            'fieldname'=>array('module'=> 'VisitingOrder',
            ),
            'userid'=>$this->userid
        );
        $userselect = $this->call('getUserRelativeUserList', $params);
        $strselect='<select name="searchvalue" id="extractid" class="select2"><option value="0">请选择负责人</option>';
        if(!empty($userselect[0])){
            if($searchfilename=='extractid' && !empty($searchvalue)){
                foreach($userselect[0] as $value){
                    $strselect.='<option value="'.$value['id'].'"';
                    if($value['id']==$searchvalue){
                        $strselect.=' selected';
                    }
                    $strselect.='>'.$value['brevitycode'].$value['last_name'].'</option>';
                }
            }else{
                foreach($userselect[0] as $value){
                    $strselect.='<option value="'.$value['id'].'">'.$value['brevitycode'].$value['last_name'].'</option>';
                }
            }

        }
        $strselect.='</select>';
		$modulestatus=empty($_REQUEST['modulestatus'])?0:$_REQUEST['modulestatus'];
        $this->smarty->assign('modulestatus',$modulestatus);
        $this->smarty->assign('searchfilename',$searchfilename);
        $this->smarty->assign('searchvalue',$searchvalue);
        $this->smarty->assign('userselect',$strselect);
		$num=ceil($list[0]/$this->pagecount);
        $this->smarty->assign('totalnum',$num);
        $this->smarty->assign('USERIMGS',$arr);
		$this->smarty->assign('sum',$list[0]);
		$sumpage = intval(($list[0]+1)/$this->pagecount);
		$this->smarty->assign('sumpage',$sumpage);
	
		
		
		$this->smarty->assign('list',$list[1]);
		$this->smarty->display('VisitingOrder/alllist.html');
	}
	#查看拜访单 分页
	public function doallList() {
		$pagenum 	= isset($_REQUEST['pagenum'])?$_REQUEST['pagenum']:1;
        $searchfilename 	= isset($_REQUEST['searchfilename'])?$_REQUEST['searchfilename']:'accountname';
        $searchvalue='';
        $search='';
        if(isset($_REQUEST['searchvalue']) && !empty($_REQUEST['searchvalue'])){
            $searchvalue=$_REQUEST['searchvalue'];
            if($searchfilename=='startdate'){
                $search = array(
                    array(
                        'field'		=>"vtiger_visitingorder.startdate##6##1744##date",
                        'operator'	=>">=",
                        "value"		=> $_REQUEST['searchvalue'].' 00:00:00',
                        "andor"		=>"And",
                    ),array(
                        'field'		=>"vtiger_visitingorder.startdate##6##1744##date",
                        'operator'	=>"<=",
                        "value"		=> $_REQUEST['searchvalue'].' 23:59:59',
                        "andor"		=>"And",
                    ));
            }else if($searchfilename=='extractid'){
                $search = array(

                    array(
                        'field'		=>"vtiger_visitingorder.extractid##53##1748##owner",
                        'operator'	=>"=",
                        "value"		=> $_REQUEST['searchvalue'],
                        "andor"		=>"And",
                    ));
            }elseif($searchfilename=='accountname'){
                $search = array(
                    array(
                        'field'		=>"vtiger_visitingorder.accountnamer##1##3687##string",
                        'operator'	=>"LIKE",
                        "value"		=> $_REQUEST['searchvalue'],
                        "andor"		=>"And",
                    )
                );
            }

        }
		if($_REQUEST['modulestatus']==1){
            $search[]=array(
                    'field'		=>"vtiger_visitingorder.modulestatus##15##1922##picklist",
                    'operator'	=>"=",
                    "value"		=> "a_normal",
                    "andor"		=>"And",
                );
            $search[]=array(
                'field'		=>"vtiger_visitingorder.auditorid##53##3610##owner",
                'operator'	=>"=",
                "value"		=> $this->userid,
                "andor"		=>"And",
            );
        }elseif($_REQUEST['modulestatus']==2){
            $search[]=array(
                    'field'		=>"vtiger_visitingorder.modulestatus##15##1922##picklist",
                    'operator'	=>"=",
                    "value"		=> "a_normal",
                    "andor"		=>"And"
                );
        }elseif($_REQUEST['modulestatus']==3){
            $search[]=array(
                'field'		=>"vtiger_visitingorder.modulestatus##15##1922##picklist",
                'operator'	=>"=",
                "value"		=> "c_complete",
                "andor"		=>"And"
            );
        }
        $search = $this->create_search_field($search);
	
		$params  = array(
			'fieldname'=>array('pagenum' 	=> $pagenum,
						   	   'pagecount'  => $this->pagecount,
                                'searchField'=> $search,
						   	   'userid'		=> $this->userid  
						   )
		);
		$list = $this->call('get_VisitingOrder', $params);

        echo json_encode(array('success'=>!empty($list[1]),"data"=>$list[1]));
        exit;
        if(!empty($list[1])){
            foreach($list[1] as $key=>$value){
                $list[1][$key]['email'] = md5($value['email']);
            }
        }
		echo json_encode($list[1]);
	}

	#待跟进拜访 
	public function unaudited(){
		$pagenum 	= isset($_REQUEST['pagenum'])?$_REQUEST['pagenum']:1;
		
		$params  = array(
			'fieldname'=>array('pagenum' 	=> $pagenum,
						   	   'pagecount'  => $this->pagecount,
						   	   'public' 	=> 'unaudited',
						   	   'userid'		=> $this->userid
						   )
		);

		$list = $this->call('get_VisitingOrder', $params);
		$this->smarty->assign('sum',$list[0]);
		$sumpage = intval(($list[0]+1)/$this->pagecount);
		$this->smarty->assign('sumpage',$sumpage);

		$this->smarty->assign('list',$list[1]);
		$this->smarty->display('VisitingOrder/unaudited.html');
	}
	#待审核拜访 
	public function pass(){
        $moduleNameArray=array('SalesOrder'=>'工单','VisitingOrder'=>'拜访单','ServiceContracts' => '服务合同',
            'Newinvoice'=>'发票(新)','ExtensionTrial'=>'延期申请','OrderChargeback'=>'退款申请',
            'RefillApplication' => '充值申请单','Vendors' => '供应商','ContractsAgreement'=>'合同补充协议',
            'SupplierContracts'=>'采购合同','SuppContractsAgreement'=>'采购合同补充协议','Accounts' => '客户',
            'ContractGuarantee'=>'合同担保','RefundTimeoutAudit'=>'超期录入审核','SeparateInto'=>'分成单','AccountPlatform'=>'媒体账户管理',
            'ProductProvider'=>'媒体外采账户管理'
        );
        /*$result = str_replace(' ',',','19155 19155 19156 19156 19159 19159 19119 19119 19122 19122 19127 19127 19127 19128 19128 19132 19132 19135 19135 19136 19136 19120 19120 19124 19124 19137 19137 19134 19134 24103 24103 24103 16566 16566 24530 24530 24530 24530 24530 24530 24530 24530 24530 24530 24530 24530 6 6 6 6 6 6 6 7 7 7 7 7 7 7 2078 2078 2078 23203 23203 24436 24436 23152 23152 23152 23820 23820 16971 16971 16971 20518 20518 22069 22069 22069 22069 22069 22069 22069 22069 22843 20550 20550 22710 22710 22710 22710 23928 23928 23928 24461 21570 23374 23374 23899 23899 23899 8693 8693 17345 17345 17345 17345 24063 24063 24075 24075 24075 24107 24107 17897 19714 19714 19714 19714 22829 22829 22829 4473 4473 22398 22398 22398 22398 7998 18774 18774 19676 19676 19676 19676 19676 19998 19998 24218 24218 24218 24218 24218 24218 24218 24443 24444 24446 24450 24450 24450 24450 24450 24450 24450 24450 24450 24450 24450 24450 8668 8668 8668 22342 24085 24085 24085 18242 18242 18242 16429 2110 2110 2110 19641 19641 16572 20698 21867 23819 23819 23640 23640 18607 18607 18607 24072 24072 7610 7610 22491 23244 23244 24078 23629 23629 23629 23629 23629 23889 4963 4963 4963 4963 17820 20369 17648 17648 17648 17648 16465 16465 16465 16465 16707 16707 16707 16707 24080 24095 24095 24095 24095 24095 24095 24095 24095 23712 23712 23712 23712 23712 24102 24102 22065 22065 22065 22065 22065 22065 22065 16520 16520 16520 16520 16520 16520 16514 16514 16514 16514 23912 24009 24009 20145 20145 23756 23756 24442 7722 22941 3629 3629 24032 24032 24109 24109 24109 18535 18535 18535 18535 18535 18535 3385 3385 3385 3385 24365 24365 24365 24365 24365 24365 24365 3384 3384 3384 3384 9864 9864 9864 9864 9864 9864 9864 9864 9864 9864 9864 9864 18161 18161 18161 18161 16798 16798 16798 16798 16798 16798 16798 16798 16798 16798 16798 16798 18032 18032 18032 18032 18032 18032 18032 18032 18032 18032 18032 18032 9865 9865 9865 9865 9865 9865 9865 9865 9865 9865 9865 9865');
		echo $result;die();*/
        $pagenum 	= isset($_REQUEST['pagenum'])?$_REQUEST['pagenum']:1;
        $ajax 	= isset($_REQUEST['ajax'])?1:0;
        $searchValue = isset($_REQUEST['searchValue'])? $_REQUEST['searchValue']:'';
        $modulestatus = isset($_REQUEST['modulestatus'])?$_REQUEST['modulestatus']:'';
        $checkstatus=empty($_REQUEST['checkstatus'])?1:$_REQUEST['checkstatus'];

        $searchWhere = $_REQUEST['searchWhere']?$_REQUEST['searchWhere']:'';
        if(isset($_REQUEST['searchWhere']) && !empty($_REQUEST['searchWhere']) && isset($_REQUEST['searchValue']) && !empty($_REQUEST['searchValue'])){
            if($_REQUEST['searchWhere'] == 'vtiger_salesorderworkflowstages.accountname'){
                $search[]=array(
                    'field'		=>"vtiger_salesorderworkflowstages.accountname##1##11285##string",
                    'operator'	=>"LIKE",
                    "value"		=> $_REQUEST['searchValue'],
                    "andor"		=>"And"
                );
            }else if($_REQUEST['searchWhere'] == 'vtiger_salesorderworkflowstages.salesorder_nono'){
                $search[]=array(
                    'field'		=>"vtiger_salesorderworkflowstages.salesorder_nono##1##2359##string",
                    'operator'	=>"LIKE",
                    "value"		=> $_REQUEST['searchValue'],
                    "andor"		=>"And"
                );
            }
        }
        $search[]=array(
            'field'		=>"vtiger_salesorderworkflowstages.modulestatus##15##2429##picklist",
            'operator'	=>"=",
            "value"		=>'p_process',
            "andor"		=>"And"
        );
        if(isset($_REQUEST['modulename']) && !empty($_REQUEST['modulename'])){
            $search[]=array(
                'field'		=>"vtiger_salesorderworkflowstages.modulename##15##1887##picklist",
                'operator'	=>"=",
                "value"		=>$_REQUEST['modulename'],
                "andor"		=>"And"
            );
            $modulename = $_REQUEST['modulename'];
        }else{
            $modulename='';
        }

        $search = $this->create_search_field($search);
        /*var_dump($search);*/
        //如果等于1待审核所有
        if($checkstatus==1){
            $params  = array(
                'fieldname'=>array('pagenum' 	=> $pagenum,
                    'pagecount'  => $this->pagecount,
                    'searchField'=> $search,
                    'userid'		=> $this->userid,
                ),
                'userid'		=> $this->userid,
            );
            // 等于2我发起
        }else if($checkstatus==2){
            $params  = array(
                'fieldname'=>array('pagenum' 	=> $pagenum,
                    'pagecount'  => $this->pagecount,
                    'searchField'=> $search,
                    'userid'		=> $this->userid,
                    'public'=>'iInitiated'
                ),
                'userid'		=> $this->userid,
            );
            // 等于 3 我已审核
        }else if($checkstatus==3){
            $params  = array(
                'fieldname'=>array('pagenum' 	=> $pagenum,
                    'pagecount'  => $this->pagecount,
                    'searchField'=> $search,
                    'userid'		=> $this->userid,
                    'public'=>'history'
                ),
                'userid'		=> $this->userid,
            );
        }else{
            $params  = array(
                'fieldname'=>array('pagenum' 	=> $pagenum,
                    'pagecount'  => $this->pagecount,
                    'searchField'=> $search,
                    'userid'		=> $this->userid,
                ),
                'userid'		=> $this->userid,
            );
        }
        $list = $this->call('getWorkFlowChecks', $params);
        if(empty($list[0])) $list[0]=0;
        $this->smarty->assign('sum',$list[2]);
        $actionModule=array('VisitingOrder',
            'RefillApplication',
            'ServiceContracts',
            'ContractsAgreement',
            'SupplierContracts',
            'SuppContractsAgreement',
            'AccountPlatform',
            'ProductProvider',
            'ContractGuarantee',
        );

		if(!empty($list[1])){
            $userlist = $this->getWeixinDepartMsgAll();
            $userlist = json_decode($userlist, true);
            if ($userlist['errcode'] == 0) {
                foreach ($userlist['userlist'] as $value) {
                    $arr[md5($value['userid'])] = $value['avatar'];
                }
            }
        }
        $this->smarty->assign('USERIMGS', $arr);
        $this->smarty->assign('modulename',$modulename);
        $this->smarty->assign('searchWhere',$searchWhere);
        $this->smarty->assign('modulestatus',$modulestatus);
        $this->smarty->assign('searchValue',$searchValue);
        $this->smarty->assign('moduleNameArray',$moduleNameArray);
        $sumpage = intval(($list[0]+1)/$this->pagecount);
        $this->smarty->assign('sumpage',$sumpage);
        $this->smarty->assign('ACTIONMODULE',$actionModule);
        $this->smarty->assign('title','待审核列表');
        $this->smarty->assign('list',$list[1]);
        /*echo "<pre>";
        var_dump($list[1]);
        die();*/

        $this->smarty->assign('checkstatus',$checkstatus);
        if($ajax==0){
            $this->smarty->display('VisitingOrder/pass.html');
        }else{
            $this->smarty->display('VisitingOrder/pass_ajax.html');
        }

	}
	#添加跟进
	public function addMod(){
		$id = intval($_REQUEST['id']);
		if($id){
			$this->smarty->assign('accountid',$id);
			$params = array('id'=>$id,'userid' => $this->userid);
			$list = $this->call('getContact', $params);

			

			$this->smarty->assign('contacts',$list);

			$this->smarty->display('VisitingOrder/addmod.html');
		}else{
			echo '找不到客户信息';
		}
	}
    #拜访单添加跟进
    public function vaddMod(){
        $id = intval($_REQUEST['id']);
        if($id){

            $params = array('id'=>$id,'userid' => $this->userid);
            $list = $this->call('visitidgetContact', $params);
            if($list[0]=='nostatus'){
//                echo '<script type="text/javascript">alert("拜访单未审核");history.back();</script>';
//                exit;
            }elseif($list[0]=='noaccount'){
                echo '<script type="text/javascript">alert("没有客户");history.back();</script>';
                exit;
            }
            $params = array(
                'fieldname' => array(
                    'record' => $_REQUEST['id'],
                    'page' => 1,
                ),
            );

            $intentionality = $this->call('getIntentionality',$params);
            $this->smarty->assign('ACCOUNTINTENTIONALITY',$intentionality[0]);
            $this->smarty->assign('accountid',$list[0]);
            $this->smarty->assign('contacts',$list[1]);
            //$this->smarty->display('VisitingOrder/addmod.html');
            $this->smarty->display('VisitingOrder/addAccount.html');
        }else{
            echo '找不到客户信息';
        }
    }
	#添加跟进
	public function doaddMod(){

		   $commentcontent 		= trim($_REQUEST['commentcontent']);
		   $modcommentmode 		= trim($_REQUEST['modcommentmode']);
		   $modcommenttype 		= trim($_REQUEST['modcommenttype']);
		   $modcommentpurpose 	= trim($_REQUEST['modcommentpurpose']);
		   $contact_id 			= trim($_REQUEST['contact_id']);
		   $accountid			= trim($_REQUEST['accountid']);
		   $accountintentionality= trim($_REQUEST['accountintentionality']);
		   if(empty($accountid)||empty($commentcontent)||empty($contact_id)){

		   	echo '数据错误';return true;
		   }
		   $params = array(
				'fieldname'=>array( 
					'commentcontent'	=> $commentcontent,
					'modcommentmode'	=> $modcommentmode,
					'modcommenttype'	=> $modcommenttype,
					'modcommentpurpose'	=> $modcommentpurpose,
					'contact_id'		=> $contact_id,
					'related_to'		=> $accountid,
					'moduleid'			=> $accountid,
					'accountid'			=> $accountid ,
					'module'			=> "ModComments",
					'modulename'		=> "Accounts",	
					'ifupdateservice'	=> false,		
					'action'			=> "SaveAjax",
					'is_service'		=> "",
                    'accountintentionality'=>$accountintentionality
					
				),
				'userid'			=> $this->userid	
			);
			$result = $this->call('addMod', $params);
			
			if(!empty($result)&&!empty($result[3])){
				$this->response(true);
			}else{
				$this->response(false);
			}
			
	}
	#添加拜访单
	public function add(){
		$token='visitingorderadd'.$this->userid;
        $this->setAddToken($token);
        $accountid = $_GET['accountid'];
        if($accountid>0 && is_numeric($accountid)){
           $this->smarty->assign('accountid',$accountid);
        }
        $params = array('userid'=>$this->userid);
		$list = $this->call('getDepartmentsUserByUserId', $params);
		$this->smarty->assign('deparment_user', $list[0]);
		$this->smarty->assign('username',$_SESSION['last_name']);
		$this->smarty->assign('userid',$_SESSION['customer_id']);
		$this->smarty->display('VisitingOrder/add.html');
	}
	#添加拜访单
	public function doadd(){
		$token='visitingorderadd'.$this->userid;
        if($this->getAddToken($token)){
            $this->response(false,'操作过期!');
            exit;
        }
		$subject 			= trim($_REQUEST['subject']);
		$related_to 		= trim($_REQUEST['related_to']);
		$related_to_display = trim($_REQUEST['related_to_display']);
		$destination 		= trim($_REQUEST['destination']);
		$contacts 			= trim($_REQUEST['contacts']);
		$purpose 			= trim($_REQUEST['purpose']);
		$startdate 			= trim($_REQUEST['startdate']);
		$enddate 			= trim($_REQUEST['enddate']);
		$outobjective 		= trim($_REQUEST['outobjective']);
		$remark 			= trim($_REQUEST['remark']);
		$customeraddress 	= trim($_REQUEST['customeraddress']);
		$accompanyuser      = trim(implode(' |##| ', $_REQUEST['accompanyuser']));

		$extractid 			= $this->userid;
		//if(empty($related_to)||empty($related_to_display)||empty($startdate)||empty($enddate)){
		if(empty($subject)||empty($purpose)||empty($startdate)||empty($enddate)){
			$this->response(false);
			exit;
		}
		$datetimes=time();
		if(strtotime($startdate)-$datetimes<-600 || strtotime($enddate)-strtotime($startdate)<1500){
            $this->response(false);
            exit;
		}
		$params = array(
			'fieldname'=>array( 
									"subject"					=>$subject,
									"popupReferenceModule"		=>'Accounts',
									"related_to" 				=>$related_to,
									"related_to_display"		=>$related_to_display,
									"popupReferenceModule"		=>'Workflows',
									"workflowsid"				=>'400',
									"workflowsid_display"		=>'拜访单审核流程',
									"destination"				=>$destination,
									"contacts"					=>$contacts,
									"purpose"					=>$purpose,
									"extractid"					=>$extractid,
									"startdate"					=>$startdate,
									"enddate"					=>$enddate,
									"outobjective"				=>$outobjective,
									"remark"					=>$remark,
									"module"					=>'VisitingOrder',
									"action"					=>'Save',
									'record'					=>'',
									"defaultCallDuration"		=>5,
									"defaultOtherEventDuration"	=>5,
									'customeraddress'           =>$customeraddress,
									'accompany'                 =>$accompanyuser
								),
			'userid' 	=> $this->userid	
			);
		$res = $this->call('add_VisitingOrder', $params);

		if(!empty($res)&&!empty($res[0])){
			$this->response(true);
		}else{
				$this->response(false);
		}
		
	}

	#查看拜访单
    public function detail(){
        $recordid 			= $_REQUEST['record'];
        $params = array(
            'fieldname'=>array( "module"	=>'VisitingOrder',
                'view'	=>'Detail',
                'record'	=>$recordid
            ),"userid"					=>$this->userid,
        );
        $modulestatus=array('c_cancel'=>'作废','c_canceling'=>'作废中','a_exception'=>'打回中','c_complete'=>'完成','a_normal'=>'正常','b_check'=>'审核中');
        $res = $this->call('get_record_detail', $params);

        if(empty($res[0]['extractid'])){
            echo "<script>alert('拜访单不存在!!');history.back();</script>";
            exit;
        }

        if (is_array($res[0]['t_accompany']) && count($res[0]['t_accompany']) > 0) {
        	$this->smarty->assign('ISSIGN', 1);
        }
        $ISREVOKE=($res[0]['extractid']==$this->userid && $res[0]['issign']==0 && in_array($res[0]['modulestatus'],array('a_normal','c_complete')))?true:false;
        $arr = array();
        if (!empty($res[0]['Workflows']['REMARKLIST'])) {
            $userlist = $this->getWeixinDepartMsg();
            $userlist = json_decode($userlist, true);
            if ($userlist['errcode'] == 0) {
                foreach ($userlist['userlist'] as $value) {
                    $arr[md5($value['userid'])] = $value['avatar'];
                }
            }
        }
        /*echo "<pre>";
        var_dump($res[0]);die();*/
        $this->smarty->assign('detailInfo', $res[0]);
        $this->smarty->assign('ISREVOKE',$ISREVOKE);
        $this->smarty->assign('MODULESTATUS',$modulestatus);
        $this->smarty->assign('moduleStatusArray',$this->modulestatus);
        $this->smarty->assign('USERIMGS', $arr);
        $this->smarty->assign('ISROLE', $res[0]['Workflows']['ISROLE']);    //是否有权限审核
        $this->smarty->assign('WORKFLOWSSTAGELIST', $res[0]['Workflows']['WORKFLOWSSTAGELIST']); //节点列表
        $this->smarty->assign('STAGERECORDID', $res[0]['Workflows']['STAGERECORDID']);  //当前工作流id
        $this->smarty->assign('STAGERECORDNAME', $res[0]['Workflows']['STAGERECORDNAME']);  //当前审核工作流的名字
        $this->smarty->assign('SALESORDERHISTORY', $res[0]['Workflows']['SALESORDERHISTORY']);  //历史打回原因记录
        $this->smarty->assign('REMARKLIST', $res[0]['Workflows']['REMARKLIST']);  //备注记录
        $this->smarty->assign('record', $recordid);
        $this->smarty->assign('ISCANCANCEL', $res[0]['cancancel']);
        $this->smarty->display('VisitingOrder/detail.html');
    }
    //拜访单审核
    public function doWorkflowStages(){
        $record 			= trim($_REQUEST['record']);
        $stagerecordid 		= trim($_REQUEST['stagerecordid']);
        $mode=trim($_REQUEST['mode']);
        $stagerekey=trim($_REQUEST['stagerekey']);
        $checkname=trim($_REQUEST['checkname']);
        $isbackname=trim($_REQUEST['isbackname']);
        $reject=trim($_REQUEST['reject']);

        $params = array(
            'fieldname'=>array(
                "record"=>$record,
                $stagerekey=>$stagerecordid,
                "action"=>"SaveAjax",
                "module"=>"SalesorderWorkflowStages",
                "mode"=>$mode,
                "src_module"=>"VisitingOrder",
                $checkname=>$isbackname,
                "customer"=>0,
                "customername"=>'',
                "reject"=>$reject,
                "actionnode"=>0
            ),
            'userid' 	=> $this->userid
        );
        $res = $this->call('do_VisitingOrderWorkflow', $params);
        echo  $res[0];
    }
	#签到
	public function sign(){
        global $corpid,$Secret;
        $id= $_GET['id'];//客户id;
        if(!empty($id)){
            $this->smarty->assign('id',$id);
        }
        require_once "jssdk.php";
		$jssdk = new JSSDK($corpid, $Secret);
		$signPackage = $jssdk->GetSignPackage();
		$this->smarty->assign('signPackage',$signPackage);
		
		$this->smarty->assign('title','拜访签到');
		$this->smarty->assign('userid', $this->userid);
		$this->smarty->display('VisitingOrder/sign.html');
	}
	#签到
	public function signs(){
        global $corpid,$Secret;
        $id= $_GET['id'];//客户id;
        if(!empty($id)){
            $this->smarty->assign('id',$id);
        }
        require_once "jssdk.php";
		$jssdk = new JSSDK($corpid, $Secret);
		$signPackage = $jssdk->GetSignPackage();
		$this->smarty->assign('signPackage',$signPackage);
		
		$this->smarty->assign('title','拜访签到');
		$this->smarty->assign('userid', $this->userid);
		$this->smarty->display('VisitingOrder/signd.html');
	}
    #签单操作
    public function dosign(){
       $visid = $_GET['id'];
       $adname = $_GET['adname'];
       $adcode = $_GET['adcode'];
       $time = date('Y-m-d H:i');
       $type = $_GET['type'];
       $params = array('fieldname'=>array(
            'userid'=>$this->userid,
            'adname' =>$adname,
            'adcode'=>$adcode,
            'time'=>$time,
            'issign'=>'1',
            'visid' =>$visid
        ));
       // 提交前验证是否是提单人或者陪同人点击的签到
       if($type==1){
           $result = $this->call('dosignYz', $params);
           echo json_encode($result[0]);exit();
       }else{
           $result = $this->call('dosign', $params);
           header("Location:index.php?module=VisitingOrder&action=detail&record=".$visid);
       }

    }
    #图片签到
    public function picture(){
        $id = intval($_REQUEST['id']);
        if($id){
            $this->smarty->assign('accountid',$id);
            $params = array('id'=>$id,'userid' => $this->userid);
            $list = $this->call('getContact', $params);
            $this->smarty->assign('contacts',$list);
            $this->smarty->display('VisitingOrder/picture.html');
        }
    }
    #插入图片
    public function dopicture(){
        $record = intval($_POST['record']);
        $file = $_FILES['fileField'];
        if(!empty($file)){
            /*$params = array('fieldname'=>array(
                'name'=>$file['name'],
                'type'=>$file['type'],
                'tmp_name'=>$file['tmp_name'],
                'error'=>$file['error'],
                'size'=>$file['size']
        ));*/
            $params = array(
                'fieldname'=>$file,
                'basic'=>array(
                    'userid'=>$this->userid,
                    'record'=>$record
                )
            );
            $result =  $this->call('uppicture',$params);
        }else{
            echo ('请选择图片');
        }
        // var_dump($params);die;
        //var_dump($result);
        //header("Location:index.php?module=VisitingOrder&action=alllist");
        header("Location:index.php?module=VisitingOrder&action=detail&record=".$record);
    }
    /**
     * 微信取商务下的人员信息
     * @return mixed
     */
    public function getWeixinDepartMsg(){
        $cache_token=@file_get_contents('./wtoken.txt');
        $token=json_decode($cache_token,true);
        //$token['access_token']='Uz1RF8TqXwjLmUGkhn8LQO29PWZjJSYSCCR0lY2HPXWZ8rPH4plUAqYu51B2Bz1R';
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
    public function doRevoke(){
        $params = array('fieldname'=>array(
            'userid'=>$this->userid,
            'record' =>$_POST['record']
        ));
        $result = $this->call('doVisitingOrderRevoke', $params);
        print_r($result);
    }
    public function searchAccount(){
        $company = trim($_REQUEST['company']);
        if(!empty($company)){
            $params = array(
                'fieldname'=>array('pagenum' 	=> 1,
                    'pagecount'  => 50,
                    'search_value'		=> $company,
                    'src_module'		=>'VisitingOrder',
                    'search_key'		=>'accountname',
                    'userid'		=> $this->userid
                )
            );
            $list = $this->call('get_my_account', $params);
            if(!empty($list[1])){
                $data=array();
                foreach($list[1] as $value){
                    $data[]=array('id'=>$value['accountid'],'label'=>$value['accountname'],'value'=>$value['accountname']);
                }
                echo json_encode($data);
                exit;
            }
            echo "";
            exit;
        }
        echo "";exit;
    }
    #获取客户资料
    public function getAccountMsg(){
        $id = intval($_REQUEST['id']);
        if(!empty($id)){
            $params = array(
                'id'=>$id
            );
            $list = $this->call('get_address_msg', $params);
            if(!empty($list)){
                $address = explode('#', $list[0][0]['address']);
                $customeraddress = implode('', $address);
                $address = !empty($address)&&isset($address[3])?$address[3]:'';
                $return = array(
                    'accountid'		=>$id,
                    'accountname'	=>'',
                    'linkname'		=>$list[0][1][0]['name'],
                    'username'		=>'',
                    'userid'		=>'',
                    'address'		=>$address,
                    'customeraddress'=>$customeraddress
                );
                echo  json_encode($return);
            }else{
                return array();
            }
        }else{
            return array();
        }
    }
    // 审核
    public function examine() {
        $stagerecordid = $_REQUEST['stagerecordid'];
        $record = $_REQUEST['record'];
        if ($stagerecordid && $record) {
            $params = array(
                'fieldname' => array(
                    'stagerecordid' => $stagerecordid,
                    'record' => $record,
                    'module' => 'SalesorderWorkflowStages',
                    'mode' => 'updateSalseorderWorkflowStages',
                    'src_module' => 'VisitingOrder',
                    'action' => 'SaveAjax',
                    'customer' => 0,
                    'customername' => ''
                ),
                'userid' => $this->userid
            );
            $result = $this->call('salesorderWorkflowStagesExamine', $params);
            $result['result']=$result[1];
            echo json_encode($result);exit();
        }
    }
    // 打回
    public function repulse() {
        $stagerecordid = $_REQUEST['stagerecordid'];
        $record = $_REQUEST['record'];
        $repulseinfo = $_REQUEST['repulseinfo'];
        $isbackname = $_REQUEST['isbackname'];
        if ($stagerecordid && $record) {
            $params = array(
                'fieldname' => array(
                    'record' => $record,
                    'module' => 'SalesorderWorkflowStages',
                    'mode' => 'backall',
                    'reject' => $repulseinfo,
                    'isrejectid' => $stagerecordid,
                    'isbackname' => $isbackname,
                    'src_module' => 'VisitingOrder',
                    'action' => 'SaveAjax',
                    'actionnode' => 0
                ),
                'userid' => $this->userid
            );
            $tt = $this->call('salesorderWorkflowStagesRepulse', $params);
            echo $tt[0];exit();

        }
    }
    /**
     * 添加备注信息
     */
    public function submitremark() {
        $stagerecordid = $_REQUEST['stagerecordid'];
        $record = $_REQUEST['record'];
        $reject = $_REQUEST['reject'];
        if ($stagerecordid && $record) {
            $params = array(
                'fieldname' => array(
                    'record' => $record,
                    'module' => 'SalesorderWorkflowStages',
                    'mode' => 'submitremark',
                    'reject' => $reject,
                    'isrejectid' => $stagerecordid,
                    'src_module' => 'VisitingOrder',
                    'action' => 'SaveAjax',
                    'actionnode' => 0
                ),
                'userid' => $this->userid
            );
            $tt = $this->call('salesorderWorkflowStagesRepulse', $params);
            echo $tt[0];
            exit();
        }
    }

    #查看拜访单
    public function allListnew(){
        $pagenum 	= isset($_REQUEST['pagenum'])?$_REQUEST['pagenum']:1;
        $searchfilename 	= isset($_REQUEST['searchfilename'])?$_REQUEST['searchfilename']:'accountname';
        $searchvalue='';
        $search='';
        if(isset($_REQUEST['searchvalue']) && !empty($_REQUEST['searchvalue'])){
            $searchvalue=$_REQUEST['searchvalue'];
            if($searchfilename=='startdate'){
                $search = array(
                    array(
                        'field'		=>"vtiger_visitingorder.startdate##6##1744##date",
                        'operator'	=>">=",
                        "value"		=> $_REQUEST['searchvalue'].' 00:00:00',
                        "andor"		=>"And",
                    ),array(
                        'field'		=>"vtiger_visitingorder.startdate##6##1744##date",
                        'operator'	=>"<=",
                        "value"		=> $_REQUEST['searchvalue'].' 23:59:59',
                        "andor"		=>"And",
                    ));
            }else if($searchfilename=='extractid'){
                $search = array(

                    array(
                        'field'		=>"vtiger_visitingorder.extractid##53##1748##owner",
                        'operator'	=>"=",
                        "value"		=> $_REQUEST['searchvalue'],
                        "andor"		=>"And",
                    ));
            }elseif($searchfilename=='accountname'){
                $search = array(
                    array(
                        'field'		=>"vtiger_visitingorder.accountnamer##1##3687##string",
                        'operator'	=>"LIKE",
                        "value"		=> $_REQUEST['searchvalue'],
                        "andor"		=>"And",
                    )
                );
            }
        }
        if($_REQUEST['modulestatus']==1){
            $search[]=array(
                'field'		=>"vtiger_visitingorder.modulestatus##15##1922##picklist",
                'operator'	=>"=",
                "value"		=> "a_normal",
                "andor"		=>"And",
            );
            $search[]=array(
                'field'		=>"vtiger_visitingorder.auditorid##53##3610##owner",
                'operator'	=>"=",
                "value"		=> $this->userid,
                "andor"		=>"And",
            );
        }elseif($_REQUEST['modulestatus']==2){
            $search[]=array(
                'field'		=>"vtiger_visitingorder.modulestatus##15##1922##picklist",
                'operator'	=>"=",
                "value"		=> "a_normal",
                "andor"		=>"And"
            );
        }elseif($_REQUEST['modulestatus']==3){
            $search[]=array(
                'field'		=>"vtiger_visitingorder.modulestatus##15##1922##picklist",
                'operator'	=>"=",
                "value"		=> "c_complete",
                "andor"		=>"And"
            );
        }
        $search = $this->create_search_field($search);
        $params  = array(
            'fieldname'=>array('pagenum' 	=> $pagenum,
                'pagecount'  => $this->pagecount,
                'searchField'=> $search,
                'userid'		=> $this->userid
            )
        );
        $list = $this->call('get_VisitingOrder', $params);
        $modulestatus=empty($_REQUEST['modulestatus'])?0:$_REQUEST['modulestatus'];
        $this->smarty->assign('modulestatus',$modulestatus);
        $this->smarty->assign('searchfilename',$searchfilename);
        $this->smarty->assign('searchvalue',$searchvalue);
        $this->smarty->assign('list',$list[1]);
        $this->smarty->display('VisitingOrder/alllistnew.html');
    }

    /**
     * 加载用户列表
     */
    public function getUserRelativeUserList(){
        $params  = array(
            'fieldname'=>array('module'=> 'VisitingOrder'
            ),
            'userid'=>$this->userid
        );
        $userselect = $this->call('getUserRelativeUserList', $params);
        if(!empty($userselect[0])){
            echo json_encode(array('success'=>true,'data'=>$userselect[0]));
        }else{
            echo json_encode(array('success'=>false,'data'=>''));
        }
        exit;
        $strselect='<select name="searchvalue" id="extractid" class="select2"><option value="0">请选择负责人</option>';
        if(!empty($userselect[0])){
            if(!empty($searchvalue)){
                foreach($userselect[0] as $value){
                    $strselect.='<option value="'.$value['id'].'"';
                    if($value['id']==$searchvalue){
                        $strselect.=' selected';
                    }
                    $strselect.='>'.$value['brevitycode'].$value['last_name'].'</option>';
                }
            }else{
                foreach($userselect[0] as $value){
                    $strselect.='<option value="'.$value['id'].'">'.$value['brevitycode'].$value['last_name'].'</option>';
                }
            }
            $strselect.='</select>';
        }
    }
    /**
     * 加载用户微信头像
     */
    public function getWeixinMsgdata(){
        $useremail=$_REQUEST['useremail'];
        $url = "https://qyapi.weixin.qq.com/cgi-bin/user/get?userid=".$useremail."&access_token=";
        $jsonData=$this->getWeixinMsg($url);
        $arrayData=json_decode($jsonData,true);
        if(isset($arrayData["errcode"]) && $arrayData["errcode"]==0){
            echo file_get_contents($arrayData["avatar"]);
        }else{
            $img=__DIR__.'/../static/img/trueland.png';
            echo file_get_contents($img);
        }
    }
    
        /**
     * 完成状态下特殊作废
     */
    public function doSpecialCancel(){
        $params = array('fieldname'=>array(
            'userid'=>$this->userid,
            'record' =>$_POST['record'],
            'remark'=>$_POST['cancelreson']
        ));
        $result = $this->call('doVisitingOrderCancel', $params);
        print_r($result);
    }
}

