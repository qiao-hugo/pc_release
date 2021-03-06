<?php

class SuppContractsAgreement extends baseapp{
	private $pagecount = 10;

	public function index(){
		$pagenum   = isset($_REQUEST['pagenum'])?$_REQUEST['pagenum']:1;
		$status = $_REQUEST['status']?$_REQUEST['status']:'all';
        $pagecount  = $this->count;
        $pagecount  = 20;
        $search = array(

            array(
                'field'		=>"vtiger_suppcontractsagreement.modulestatus##16##3637##picklist",
                'operator'	=>"=",
                "value"		=>"b_check",
                "andor"		=>"OR",
                "leftParenthesesName"=>"("
            ),
            array(
                'field'		=>"vtiger_suppcontractsagreement.modulestatus##16##3637##picklist",
                'operator'	=>"=",
                "value"		=>"b_actioning",
                "andor"		=>"AND",
                "rightParenthesesName"=>")"
            ),

        );
        $searchfieldname='';
        $searchfieldvalue='';
        if(!empty($_POST['searchvalue']))
        {
            $searchfieldvalue=$_POST['searchvalue'];
            $searchfieldvalue=str_replace(",","",$searchfieldvalue);
            $searchfieldvalue=str_replace('#',"",$searchfieldvalue);
            //$searchfieldv="'%".$searchfieldvalue."%'";
            if($_POST['radiot']=='contract_no')
            {
                $search[] =array(
                    "leftParenthesesName"=>"(",
                    'field'		=>"vtiger_suppcontractsagreement.contract_no##1##541##string",
                    'operator'	=>"LIKE",
                    "value"		=>$searchfieldvalue,
                    "andor"		=>"AND",
                    "rightParenthesesName"=>")"
                );
                $searchfieldname='contract_no';
            }
            elseif($_POST['radiot']=='accountname')
            {
                $search[] = array(
                        'field'		=>"vtiger_account.accountname##10##529##reference",
                        'operator'	=>"LIKE",
                        "value"		=>$searchfieldvalue,
                        "andor"		=>"AND",
                );
                $searchfieldname='accountname';
            }
        }

        $search=$this->create_search_field($search);
        $params  = array(
            'fieldname'=>array(
                "module"     =>'SuppContractsAgreement',
                'pagenum'    => $pagenum,
                'pagecount'  => $pagecount,
                'userid'     => $this->userid,
                'pagecount'=> $pagecount,
                'searchField'=> $search,
                //'modulestatus' => $status,
            ),
            'userid'    => $this->userid
        );

        $list = $this->call('getSuppContractsAgreement', $params);

        $arr=array();
        if(!empty($list[1])) {
            $url='https://qyapi.weixin.qq.com/cgi-bin/user/list?department_id=1&fetch_child=1&status=1&access_token=';
            $userlist = $this->getWeixinDepartMsg($url);
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
        $this->smarty->assign('status', $status);

        $type = $_REQUEST['type'];
        if ($type == 'ajax') {
        	$this->smarty->display('SuppContractsAgreement/ajax.html');
        } else {
        	$this->smarty->display('SuppContractsAgreement/index.html');
        }
	}

    public function one() {
        $id  = $_REQUEST['id'];
        if (! empty($id)) {
            require_once "jssdk.php";
            $jssdk = new JSSDK("wx74d59c197d3976ee", "8afc371fd3c51ee97d3d8f93647fe219");
            $signPackage = $jssdk->GetSignPackage();
            $this->smarty->assign('signPackage',$signPackage);
            $params = array(
                'fieldname'=>array( 
                    'id'    => $id,
                    'module'    => 'SuppContractsAgreement',
                    'record'=> $id,
                ),
                'userid'        => $this->userid
            );
            $result = $this->call('oneSuppContractsAgreement', $params);
            $result = json_decode($result[0], true);
            /*echo "<pre>";
            print_r($result);
            exit;*/
            $arr=array();
            if(!empty($result['workflows']['REMARKLIST'])) {
                $url='https://qyapi.weixin.qq.com/cgi-bin/user/list?department_id=1&fetch_child=1&status=1&access_token=';
                $userlist = $this->getWeixinDepartMsg($url);
                $userlist = json_decode($userlist, true);
                if ($userlist['errcode'] == 0) {
                    foreach ($userlist['userlist'] as $value) {
                        $arr[md5($value['userid'])] = $value['avatar'];
                    }
                }
            }
            $modulestatus=array(
                'c_complete'=>'?????????',
                'c_cancelings'=>'?????????.',
                'a_normal'=>'??????',
                'a_exception'=>'?????????',
                'b_check'=>'?????????',
                'c_complete'=>'??????',
                'c_canceling'=>'?????????',
                'c_cancel'=>'??????',
                'b_actioning'=>'?????????',
                'c_recovered'=>'?????????',
                'c_stamp'=>'?????????',);
            $this->smarty->assign('USERIMGS',$arr);
            $this->smarty->assign('SuppContractsAgreement', $result[0]);
            $this->smarty->assign('modulestatus',$modulestatus);
            $this->smarty->assign('attr', $result['atta']);
            $this->smarty->assign('WORKFLOWSSTAGELIST', $result['workflows']['WORKFLOWSSTAGELIST']); //????????????
            $this->smarty->assign('ISROLE', $result['workflows']['ISROLE']);    //?????????????????????
            $this->smarty->assign('STAGERECORDID', $result['workflows']['STAGERECORDID']);  //???????????????id
            $this->smarty->assign('STAGERECORDNAME', $result['workflows']['STAGERECORDNAME']);  //??????????????????????????????
            $this->smarty->assign('SALESORDERHISTORY', $result['workflows']['SALESORDERHISTORY']);  //??????????????????
            $this->smarty->assign('REMARKLIST', $result['workflows']['REMARKLIST']);  //????????????
            $this->smarty->assign('record', $id);
            $this->smarty->assign('userid', $this->userid);
            $this->smarty->display('SuppContractsAgreement/one.html');
        }
    }
	
    /**
     * ?????????????????????????????????
     * @return mixed
     */
    public function getWeixinDepartMsg($url){
        $cache_token=@file_get_contents('./wtoken.txt');
        $token=json_decode($cache_token,true);
        //$token['access_token']='iDsGpw7vRyG-T4VBqC3YuPjbi8_5vzuCgIcg_gMnAJXi7KMedCK38jkP7s91JF-k';
        //$url='https://qyapi.weixin.qq.com/cgi-bin/user/list?access_token='.$token['access_token'].'&department_id=1&fetch_child=1&status=1';
        $url=$url.$token['access_token'];

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
    public function photograph(){
        $record=$_POST['record'];
        $style=$_POST['style'];
        $pictureid=$_POST['pictureid'];
        $url="https://api.weixin.qq.com/cgi-bin/media/get?media_id={$pictureid}&access_token=";
        $tempfile = $this->getWeixinMsg($url);
        $params = array(
            'fieldname' => array(
                'module' => 'SuppContractsAgreement',
                'filename' => "image.jpg",
                'filetype' => 'image/jpeg',
                'filesize' => 50,
                'filecontents' => base64_encode($tempfile)
            ),
            'userid' => $this->userid
        );

        $list = $this->call('mobile_upload', $params);
        if($list[0]['success']){
            $params = array(
                'fieldname' => array(
                    'module' => 'SuppContractsAgreement',
                    'relationid' => $record,
                    'style' => $style,
                    'filestate' =>'filestate1',
                    'attachmentsid' =>$list[0]['result']['id']
                ),
                'userid' => $this->userid
            );
            $this->call('contracts_photograph', $params);
        }

        //$filedata=file_get_contents();
    }
    /**
     * ?????????????????????
     */
    public function download(){
        error_reporting(0);
        $params = array(
            'fieldname' => array(
                'module' => 'SuppContractsAgreement',
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
    public function getWeixinMsg($url){
        $cache_token=trim(substr(file_get_contents("./access_token1.php"), 15));
        $token=json_decode($cache_token,true);
        //$token['access_token']='iDsGpw7vRyG-T4VBqC3YuPjbi8_5vzuCgIcg_gMnAJXi7KMedCK38jkP7s91JF-k';
        //$url='https://qyapi.weixin.qq.com/cgi-bin/user/list?access_token='.$token['access_token'].'&department_id=1&fetch_child=1&status=1';
        $url=$url.$token['access_token'];

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
                    'src_module'=>'SuppContractsAgreement',
                    'action'=>'SaveAjax',
                    'actionnode'=>0
                ),
                'userid'			=> $this->userid
            );
            $tt = $this->call('salesorderWorkflowStagesRepulse', $params);
            echo $tt[0];die;
        }
    }
    // ??????
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
                    'src_module'=>'SuppContractsAgreement',
                    'action'=>'SaveAjax',
                    'customer'=>0,
                    'customername'=>''
                ),
                'userid'			=> $this->userid
            );
            $this->call('salesorderWorkflowStagesExamine', $params);
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
                    'src_module'=>'SuppContractsAgreement',
                    'action'=>'SaveAjax',
                    'actionnode'=>0
                ),
                'userid'			=> $this->userid
            );
            $tt = $this->call('salesorderWorkflowStagesRepulse', $params);
            echo $tt[0];die;
        }
    }
}

