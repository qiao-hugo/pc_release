<?php

class Schoolvisit extends baseapp{
	private $pagecount = 10;

	#查看拜访单
	public function allList(){
		$pagenum 	= isset($_REQUEST['pagenum'])?$_REQUEST['pagenum']:1;
		$searchfilename 	= isset($_REQUEST['searchfilename'])?$_REQUEST['searchfilename']:'schoolname';
		$searchvalue='';
        $search='';
        if(isset($_REQUEST['searchvalue']) && !empty($_REQUEST['searchvalue'])){
            $searchvalue=$_REQUEST['searchvalue'];
            if($searchfilename=='startdate'){
                $search = array(
                    array(
                        'field'		=>"vtiger_schoolvisit.startdate##5##107960##date",
                        'operator'	=>">=",
                        "value"		=> $_REQUEST['searchvalue'].' 00:00:00',
                        "andor"		=>"And",
                    ),array(
                        'field'		=>"vtiger_schoolvisit.startdate##5##107961##date",
                        'operator'	=>"<=",
                        "value"		=> $_REQUEST['searchvalue'].' 23:59:59',
                        "andor"		=>"And",
                    ));
            }else if($searchfilename=='extractid'){
                $search = array(

                    array(
                        'field'		=>"vtiger_schoolvisit.extractid##53##107958##owner",
                        'operator'	=>"=",
                        "value"		=> $_REQUEST['searchvalue'],
                        "andor"		=>"And",
                    ));
            }elseif($searchfilename=='schoolname'){
            $search = array(
                array(
                    'field'		=>"vtiger_school.schoolname##10##107955##reference",
                    'operator'	=>"LIKE",
                    "value"		=> $_REQUEST['searchvalue'],
                    "andor"		=>"And",
                )
            );
            }
        }
        $search = $this->create_search_field($search);
		$params  = array(
			'fieldname'=>array('pagenum' 	=> $pagenum,
						   	   'pagecount'  => $this->pagecount,
                                'searchField'=> $search,
						   	   'userid'		=> $this->userid  
						   )
		);

		$list = $this->call('get_Schoolvisit', $params);
        $params  = array(
            'fieldname'=>array('module'=> 'Schoolvisit',
                
            ),
            'userid'=>$this->userid
        );
        $userselect = $this->call('getUserRelativeUserList', $params);


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

        $strselect='<select name="searchvalue" id="extractid" class="select2"><option value="0">请选择负责人</option>';
        if(!empty($userselect[0])){
            if($searchfilename=='extractid' && !empty($searchvalue)){
                foreach($userselect[0] as $value){
                    $strselect.='<option value="'.$value['id'].'"';
                    if($value['id']==$searchvalue){
                        $strselect.=' selected';
                    }
                    $strselect.='>'.$value['last_name'].'</option>';
                }
            }else{
                foreach($userselect[0] as $value){
                    $strselect.='<option value="'.$value['id'].'">'.$value['last_name'].'</option>';
                }
            }
        }

        $strselect.='</select>';
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
		$this->smarty->display('Schoolvisit/alllist.html');
	}


    #查看拜访单 分页
    public function doallList() {
        $pagenum    = isset($_REQUEST['pagenum'])?$_REQUEST['pagenum']:1;
        $searchfilename     = isset($_REQUEST['searchfilename'])?$_REQUEST['searchfilename']:'accountname';
        $searchvalue='';
        $search='';
        if(isset($_REQUEST['searchvalue']) && !empty($_REQUEST['searchvalue'])){
            $searchvalue=$_REQUEST['searchvalue'];
            if($searchfilename=='startdate'){
                $search = array(
                    array(
                        'field'     =>"vtiger_schoolvisit.startdate##6##1744##date",
                        'operator'  =>">=",
                        "value"     => $_REQUEST['searchvalue'].' 00:00:00',
                        "andor"     =>"And",
                    ),array(
                        'field'     =>"vtiger_schoolvisit.startdate##6##1744##date",
                        'operator'  =>"<=",
                        "value"     => $_REQUEST['searchvalue'].' 23:59:59',
                        "andor"     =>"And",
                    ));
            }else if($searchfilename=='extractid'){
                $search = array(

                    array(
                        'field'     =>"vtiger_schoolvisit.extractid##53##1748##owner",
                        'operator'  =>"=",
                        "value"     => $_REQUEST['searchvalue'],
                        "andor"     =>"And",
                    ));
            }elseif($searchfilename=='accountname'){
                $search = array(
                    array(
                        'field'     =>"vtiger_account.accountname##10##1740##reference",
                        'operator'  =>"LIKE",
                        "value"     => $_REQUEST['searchvalue'],
                        "andor"     =>"And",
                    )
                );
            }

        }
        $search = $this->create_search_field($search);
    
        $params  = array(
            'fieldname'=>array('pagenum'    => $pagenum,
                               'pagecount'  => $this->pagecount,
                                'searchField'=> $search,
                               'userid'     => $this->userid  
                           )
        );
        $list = $this->call('get_Schoolvisit', $params);
        if(!empty($list[1])){
            foreach($list[1] as $key=>$value){
                $list[1][$key]['email'] = md5($value['email']);
            }
        }
        echo json_encode($list[1]);
    }


    #添加拜访单
    public function add(){
        $accountid = $_GET['accountid'];
        if($accountid>0 && is_numeric($accountid)){
           $this->smarty->assign('accountid',$accountid);
        }
        $params = array('userid'=>$this->userid);
        $list = $this->call('getDepartmentsUserByUserId', $params);
        
        $this->smarty->assign('deparment_user', $list[0]);
        $this->smarty->assign('username',$_SESSION['last_name']);
        $this->smarty->assign('userid',$_SESSION['customer_id']);
        $this->smarty->display('Schoolvisit/add.html');
    }

    // 搜索学校名称
    public function searchSchool() {
        $company = trim($_REQUEST['company']);
        if(!empty($company)){
            
            $params  = array(
                'fieldname'=>array('schoolname'    => $company,
                    'userid'     => $this->userid  
                )
            );

            $list = $this->call('searchSchool', $params);
            echo json_encode($list);
            exit;
        }
        echo "";exit;
    }

    public function getSchoolMsg() {
        $id = trim($_REQUEST['id']);
        if(!empty($id)){
            $params  = array(
                'fieldname'=>array('schoolid'    => $id,
                    'userid'     => $this->userid  
                )
            );
            $list = $this->call('getSchoolMsg', $params);
            if(!empty($list)) {
                echo json_encode($list[0]);
            }
            exit;
        }
    }


    #添加拜访单
    public function doadd(){
        //$subject          = trim($_REQUEST['subject']);
        $schoolid         = trim($_REQUEST['schoolid']);
        $schoolid_display = trim($_REQUEST['schoolid_display']);
        $destination        = trim($_REQUEST['destination']);
        $contacts           = trim($_REQUEST['contacts']);
        $purpose            = trim($_REQUEST['purpose']);
        $startdate          = trim($_REQUEST['startdate']);
        $enddate            = trim($_REQUEST['enddate']);
        $outobject      = trim($_REQUEST['outobject']);
        $remark             = trim($_REQUEST['remark']);
        $schooladdress    = trim($_REQUEST['schooladdress']);
        $accompanyuser      = trim(implode(' |##| ', $_REQUEST['accompanyuser']));
        $subject  = $schoolid_display . '拜访单';
        $extractid          = $this->userid;
        if(empty($schoolid)||empty($schoolid_display)||empty($startdate)||empty($enddate)){
            $this->response(false);
        }
        $params = array(
            'fieldname'=>array( 
                                    "subject"                 =>$subject,
                                    "popupReferenceModule"      =>'School',
                                    "schoolid"                =>$schoolid,
                                    "schoolid_display"        =>$schoolid_display,
                                    "popupReferenceModule"      =>'Workflows',
                                    "workflowsid"               =>'398372',
                                    "workflowsid_display"       =>'学校拜访单审核流程',
                                    "destination"               =>$destination,
                                    "contacts"                  =>$contacts,
                                    "purpose"                   =>$purpose,
                                    "extractid"                 =>$extractid,
                                    "startdate"                 =>$startdate,
                                    "enddate"                   =>$enddate,
                                    "outobjective"              =>$outobject,
                                    "remark"                    =>$remark,
                                    "module"                    =>'Schoolvisit',
                                    "action"                    =>'Save',
                                    'record'                    =>'',
                                    "defaultCallDuration"       =>5,
                                    "defaultOtherEventDuration" =>5,
                                    'customeraddress'           =>$customeraddress,
                                    'accompany'                 =>$accompanyuser
                                ),
            'userid'    => $this->userid    
            );
        $res = $this->call('add_Schoolvisit', $params);
        if(!empty($res)&&!empty($res[0])){
            $this->response(true);
        }else{
            $this->response(false);
        }
        
    }


    public function detail() {
        $recordid           = $_REQUEST['record'];
        $params = array(
            'fieldname'=>array( "module"    =>'Schoolvisit',
                'view'  =>'Detail',
                'record'    =>$recordid
            ),"userid"                  =>$this->userid,
        );
        $modulestatus=array('a_exception'=>'<span class="label label-danger">打回中</span>','c_complete'=>'<span class="label label-warning">完成</span>','a_normal'=>'<span class="label label-info">正常</span>','b_check'=>'<span class="label label-success">审核中</span>');
        $res = $this->call('get_record_detail', $params);

        //print_r( $res);die;

        if(empty($res[0]['extractid'])){
            echo "<script>alert('学校拜访单不存在!!');history.back();</script>";
            exit;
        }

        if (is_array($res[0]['t_accompany']) && count($res[0]['t_accompany']) > 0) {
            $this->smarty->assign('ISSIGN', 1);
        }
        $this->smarty->assign('DETAIL',$res[0]);
        $this->smarty->assign('MODULESTATUS',$modulestatus);
        $this->smarty->assign('title','拜访单详情');
        $this->smarty->display('Schoolvisit/detail.html');
    }


    //拜访单审核
    public function doWorkflowStages(){
        $record             = trim($_REQUEST['record']);
        $stagerecordid      = trim($_REQUEST['stagerecordid']);
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
                "src_module"=>"Schoolvisit",
                $checkname=>$isbackname,
                "customer"=>0,
                "customername"=>'',
                "reject"=>$reject,
                "actionnode"=>0
            ),
            'userid'    => $this->userid
        );
        $res = $this->call('do_VisitingOrderWorkflow', $params);
        echo  $res[0];
    }

    #签到
    public function sign(){
        $id= $_GET['id'];//客户id;
        if(!empty($id)){
            $this->smarty->assign('id',$id);
        }
        require_once "jssdk.php";
        $jssdk = new JSSDK("wx74d59c197d3976ee", "8afc371fd3c51ee97d3d8f93647fe219");
        $signPackage = $jssdk->GetSignPackage();
        $this->smarty->assign('signPackage',$signPackage);
        
        $this->smarty->assign('title','学校拜访签到');
        $this->smarty->assign('userid', $this->userid);
        $this->smarty->display('Schoolvisit/sign.html');
    }

    #签单操作
    public function dosign(){
       $visid = $_GET['id'];
       $adname = $_GET['adname'];
       $adcode = $_GET['adcode'];
       $time = date('Y-m-d H:i');
       $params = array('fieldname'=>array(
           'userid'=>$this->userid,
           'adname' =>$adname,
            'adcode'=>$adcode,
           'time'=>$time,
           'issign'=>'1',
           'visid' =>$visid
       ));
       $result = $this->call('schoolvisitDosign', $params);
       header("Location:index.php?module=Schoolvisit&action=detail&record=".$visid);
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

}

