<?php
/**
 * T云服务购买
 * @author gaocl
 *
 */
class TyunBuyService extends baseapp
{
    //CRM新API
    //====线上地址========================================================
    //升级订单创建
    //private $tyun_upgrade_url = "http://tyunapi.71360.com/CRM/SetSecretkeyUpgrade";
    //private $tyun_renew_url = "http://tyunapi.71360.com/CRM/SetSecretkeyRenewal";
    //private $tyun_againbuy_url = "http://tyunapi.71360.com/CRM/SetSecretkeyBuyService";

    //====预发布地址========================================================
    //升级订单创建
    private $tyun_upgrade_url = "http://apityun.71360.com/api/CRM/SetSecretkeyUpgrade";
    private $tyun_renew_url = "http://apityun.71360.com/api/CRM/SetSecretkeyRenewal";
    private $tyun_againbuy_url = "http://apityun.71360.com/api/CRM/SetSecretkeyBuyService";

    //===测试地址=========================================================
    //192.168.40.118:8630
    //private $tyun_upgrade_url = "http://tyunapi.arvin.com/api/CRM/SetSecretkeyUpgrade";
    //private $tyun_renew_url = "http://tyunapi.arvin.com/api/CRM/SetSecretkeyRenewal";
    //private $tyun_againbuy_url = "http://tyunapi.arvin.com/api/CRM/SetSecretkeyBuyService";

    //忽略校验合同领取人和客服负责人是否一致
    private $arr_ignore_check = array(1179,2824);
    //客服管理操作 (1179=>黄玉琴 1=>admin 1793=>高 2110=>柳 2824=李季) 1、显示购买时间 2、显示降级功能
    private $arr_cs_admin = array(1179,1,1793,2110,2824);
    //客服角色
    private $arr_service_role = array('H83','H84','H85','H113','H117','H124','H129','H136','H140');
    //特殊客服 1=>admin 1793=>高 2110=>柳
    private $arr_cs_special = array(1,1793,2110);
    //添加T云服务购买
    public function index()
    {
        /*if(empty($this->roleid)){
            $params = array(
                'fieldname'=>array(
                ),
                'userid'			=> $this->userid
            );
            $list = $this->call('getUserRole', $params);
            $this->_logs(array('角色返回结果：'.$list[0]));
            if($list[0] >= 0){
                $this->roleid = $list[0]["roleid"];
            }
        }
        $this->_logs(array("微信端登陆ID:".$this->userid .",登录角色ID：".$this->roleid));
        //是否客服管理
        $is_cs_admin = in_array($this->userid,$this->arr_cs_admin);
        $this->smarty->assign('is_cs_admin', $is_cs_admin);

        //是否客服或特殊指定
        $is_cs = in_array($this->roleid,$this->arr_service_role) || in_array($this->userid,$this->arr_cs_special);
        $this->smarty->assign('is_cs', $is_cs);

        $type = trim($_REQUEST['type']);
        $this->smarty->assign('t_type', empty($type)?0:$type);
        $this->smarty->display('TyunBuyService/index.html');*/
        $this->add();
    }

    //添加T云服务购买
    public function add()
    {
        if(empty($this->roleid)){
            $params = array(
                'fieldname'=>array(
                ),
                'userid'			=> $this->userid
            );
            $list = $this->call('getUserRole', $params);
            $this->_logs(array('角色返回结果：'.$list[0]));
            if($list[0] >= 0){
                $this->roleid = $list[0]["roleid"];
            }
        }
        $this->_logs(array("微信端登陆ID:".$this->userid .",登录角色ID：".$this->roleid));
        //是否客服管理
        $is_cs_admin = in_array($this->userid,$this->arr_cs_admin);
        $this->smarty->assign('is_cs_admin', $is_cs_admin);

        //是否客服或特殊指定
        $is_cs = in_array($this->roleid,$this->arr_service_role) || in_array($this->userid,$this->arr_cs_special);
        $this->smarty->assign('is_cs', $is_cs);

        $type = trim($_REQUEST['type']);
        $this->smarty->assign('t_type', empty($type)?0:$type);

        //忽略校验合同领取人和客服负责人是否一致
        $is_ignore_check = in_array($this->userid,$this->arr_ignore_check);
        $this->smarty->assign('is_ignore_check', $is_ignore_check);
        if($type == '1') {
            //升级
            $this->smarty->display('TyunBuyService/upgrade.html');
        }else if($type == '2') {
            //续费
            $this->smarty->display('TyunBuyService/renew.html');
        }else if($type == '3'){
            //另购
            $this->smarty->display('TyunBuyService/againbuy.html');
        }else if($type == '4'){
            //降级
            $this->smarty->display('TyunBuyService/degrade.html');
        }else{
            //首购
            $this->onload_buy_add_page();
        }
    }

    //加载购买页面
    private function onload_buy_add_page()
    {
        require_once "jssdk.php";
        $jssdk = new JSSDK("wx74d59c197d3976ee", "8afc371fd3c51ee97d3d8f93647fe219");
        $signPackage = $jssdk->GetSignPackage();
        $this->smarty->assign('signPackage',$signPackage);
        $params = array(
            'userid'			=> $this->userid
        );
        $list = $this->call('findParentDepartment', $params);
        $this->_logs(array("findParentDepartment：".$this->userid, $list));
        $arr1 = array(
            'H1::H2::H3::H72::H4'=>10567,
            'H1::H2::H3::H72::H7'=>10568,
            'H1::H2::H3::H72::H5'=>10582,
            'H1::H2::H3::H72::H8'=>10569,
            'H1::H2::H102'=>10574,
            'H1::H2::H3::H24'=>10573,
            'H1::H2::H3::H133'=>10566,
            'H1::H2::H3::H125'=>10575,
            'H1::H2::H3::H160'=>10598,
            'H1::H2::H3::H156'=>10599,
            'H1::H2::H3::H175'=>10600,
            'H1::H2::H3::H22'=>10487,
            'H1::H2::H3::H111'=>10573,
            'H1::H2::H3::H72::H310'=>10769,
            'H1::H2::H322'=>10487,
            'H1::H2::H3::H72::H324'=>10793,
        );
        $agents = 0;
        $userarray=array(43,199);
        $threearray=array(427);//三营
        foreach ($arr1 as $k=>$v){
            if(in_array($this->userid,$userarray)){
                //$agents = 10567;
                $agents = 10642;
                break;
            }
            if(in_array($this->userid,$threearray)){
                $agents = 10582;
                break;
            }
            if(false !== strpos($list[0]['parentdepartment'], $k)){
                $agents = $v;
                break;
            }
        }
        $this->_logs(array("agents：", $agents));
        //$this->smarty->assign('agents', empty($agents)?'10642':$agents);
        $this->smarty->assign('agents', $agents);
        $this->smarty->assign('userid', $this->userid);
        $this->smarty->display('TyunBuyService/buy.html');
    }

    //获取T云升级产品
    public function searchTyunUpgradeProduct()
    {
        $parm_productid = trim($_REQUEST['p_productid']);
        $is_degrade = trim($_REQUEST['is_degrade']);
        if(!empty($parm_productid)) {
            $params = array(
                'fieldname' => array('p_productid'	=> $parm_productid,'is_degrade'	=> $is_degrade),
                'userid' => $this->userid
            );
            $list = $this->call('searchTyunUpgradeProduct', $params);
            $list = $list[0];
            if(count($list) == 0){
                if($is_degrade == '1'){
                    echo json_encode(array('success'=>false,'message'=>'未查询到对应降级版本','productList'=>null));
                }else{
                    echo json_encode(array('success'=>false,'message'=>'未查询到对应升级版本','productList'=>null));
                }
            }else{
                if(count($list) == 1){
                    echo json_encode(array('success'=>true,'message'=>'','productList'=>array($list['item'])));
                }else{
                    echo json_encode(array('success'=>true,'message'=>'','productList'=>$list));
                }
            }
            exit;
        }
    }

    /**
     * 通过T云账号查询购买信息(查询最近购买)
     */
    public function searchTyunBuyServiceInfo(){
        $tyun_account = trim($_REQUEST['tyun_account']);
        $tyun_type = trim($_REQUEST['tyun_type']);
        if(!empty($tyun_account)){
            $params = array(
                'fieldname'=>array(
                    'tyun_account'	=> $tyun_account,
                    'tyun_type'	=> $tyun_type,
                ),
                'userid'			=> $this->userid
            );
            $list = $this->call('searchTyunBuyServiceInfo', $params);
            $list = $list[0];
            if(!empty($list['item']) && count($list) == 1){
                $list = array($list['item']);
            }
            echo json_encode($list);
            exit;
        }
    }

    #查找合同编号
    public function searchTyunBuyServiceContract(){
        $contract_no = trim($_REQUEST['contract_no']);
        $customerid = trim($_REQUEST['customerid']);

        //是否客服管理
        $is_cs_admin = in_array($this->userid,$this->arr_cs_admin) || in_array($this->roleid,$this->arr_service_role) || in_array($this->userid,$this->arr_cs_special);

        if(!empty($contract_no)){
            $params = array(
                'fieldname'=>array(
                    'contract_no'	=> $contract_no,
                    'customerid'	=> $customerid,
                    'is_cs_admin'=>$is_cs_admin
                ),
                'userid'			=> $this->userid
            );
            $list = $this->call('searchTyunBuyServiceContract', $params);
            $list = $list[0];
            if(!empty($list['item']) && count($list) == 1){
                $list = array($list['item']);
            }
            echo json_encode($list);
            exit;
        }
        echo "";exit;
        //print_r($list);

    }

    /**
     * CRM新API-查询另购服务
     */
    public function getTyunServiceItem(){
        $params = array(
            'fieldname'=>array(
            ),
            'userid'			=> $this->userid
        );
        $list = $this->call('getTyunServiceItem', $params);
        $list = $list[0];
        if(count($list) == 0){
            echo json_encode(array('success'=>false,'message'=>'未查询到另购服务','data'=>array()));
        }else{
            echo json_encode(array('success'=>true,'message'=>'','data'=>$list));
        }
        exit;
    }

    /**
     * 以T云登陆账号为主，如果该账号签订过购买、升级、续费、另购、降级任何一个订单，如果订单对应的合同没有签收完成，则不可以签订第二个订单。
     */
    public function checkTyunExistBuy($loginname){
        $params = array(
            'fieldname'=>array(
                'tyun_account'	=> $loginname,
            ),
            'userid'			=> $this->userid
        );
        $list = $this->call('checkTyunExistBuy', $params);
        $this->_logs(array($loginname.'账号获取订单数，返回结果：'.$list[0]));
        if($list[0] <= 0){
            return true;
        }
        return false;
    }

    /**
     * 升级、续费、另购服务保存
     */
    public function saveTyunBuyService(){
        $type = $_REQUEST['type'];//类
        $checkFlag = $this->checkTyunExistBuy($_REQUEST['loginname']);
        if(!$checkFlag){
            echo json_encode(array('success'=>2, 'msg'=>'此存在未签收的合同，不能操作'));
            exit();
        }
        $tyunData = array();
        //登录名
        $tyunData['LoginName'] = $_REQUEST['loginname'];
        //激活码
        $tyunData['SecretKeyID'] = $_REQUEST['secretkeyid'];
        //合同编号
        $tyunData['ContractCode'] = $_REQUEST['contractname_display'];
        //原到期时间
        $tyunData['OldCloseDate'] = $_REQUEST['oldexpiredate'];
        $t_type_name = "";
        $mail_subject = "";
        if($type == '1'){
            //升级
            $url = $this->tyun_upgrade_url;
            $t_type_name = "upgrade";
            $tyunData['OldProductID'] = $_REQUEST['oldproductid'];
            $tyunData['UpgradeProductID'] = $_REQUEST['productid'];
            $tyunData['UpgradeYear'] = $_REQUEST['productlife'];
            $mail_subject = "T云版本升级通知";
        }else if($type == '2'){
            //续费
            //是否客服
            /*$is_cs = in_array($this->roleid,$this->arr_service_role) || in_array($this->userid,$this->arr_cs_special);
            if($is_cs == false){
                echo json_encode(array('success'=>2, 'msg'=>'无权续费操作'));
                exit();
            }*/

            $url = $this->tyun_renew_url;
            $t_type_name = "renew";
            $tyunData['RenewYear'] = $_REQUEST['productlife'];
            $mail_subject = "T云版本续费通知";
        }else if($type == '3'){
            //另购
            $url = $this->tyun_againbuy_url;
            $t_type_name = "againbuy";
            $mail_subject = "T云版本另购通知";
        }else if($type == '4'){
            /*$is_cs_admin = in_array($this->userid,$this->arr_cs_admin);
            if($is_cs_admin == false){
                echo json_encode(array('success'=>2, 'msg'=>'无权降级操作'));
                exit();
            }*/

            //降级
            $url = $this->tyun_degrade_url;
            $t_type_name = "degrade";
            $tyunData['OldProductID'] = $_REQUEST['oldproductid'];
            $tyunData['UpgradeProductID'] = $_REQUEST['productid'];
            $tyunData['UpgradeYear'] = $_REQUEST['productlife'];
            $mail_subject = "T云版本降级通知";
        }
        $this->_logs(array($t_type_name."数据保存开始"));
        //另购服务对象 gaocl add 2018-06-11
        //$json_crm_serviceinfo="";
        $arr_crm_serviceinfo = array();
        if(!empty($_POST['inserti'])){
            $arr_exist_serviceid = array();
            $arr_new_serviceinfo = array();
            foreach($_POST['inserti'] as $key=>$value){
                $serviceID = $_POST['ServiceID'][$value];
                //$buycount = $_POST['BuyCount'][$value];
                //$remark = $_POST['servicename_display'][$value].':'.$buycount;
                if(empty($serviceID) || $serviceID == '0'){
                    continue;
                }
                /*$tmp_serviceID1 = explode("|",$serviceID);
                if(!in_array($tmp_serviceID1[0],$arr_exist_serviceid)){
                    $arr_exist_serviceid[]=$tmp_serviceID1[0];
                    $arr_new_serviceinfo[]=array("ServiceID"=>$tmp_serviceID1[0],"BuyCount"=>$buycount,"Remark"=>$remark);
                }else{
                    for($i=0;$i<count($arr_new_serviceinfo);$i++){
                        $tmp_serviceID2 = $arr_new_serviceinfo[$i]['ServiceID'];
                        $tmp_serviceID2 = explode("|",$tmp_serviceID2);
                        $tmp_buycount2 = $arr_new_serviceinfo[$i]['BuyCount'];
                        $tmp_remark2 = $arr_new_serviceinfo[$i]['Remark'];
                        if($tmp_serviceID1[0] == $tmp_serviceID2[0]){
                            $arr_new_serviceinfo[$i]['BuyCount'] = bcadd($buycount,$tmp_buycount2);
                            $arr_new_serviceinfo[$i]['Remark'] = $tmp_remark2.';'.$remark;
                            break;
                        }
                    }
                }*/
                $arr_serviceinfo[]=array("ServiceID"=>$serviceID,"BuyCount"=>$_POST['TyunBuyCount'][$value]);
                $arr_crm_serviceinfo[]=array("ServiceID"=>$serviceID,"BuyCount"=>$_POST['BuyCount'][$value]);
                $arr_serviceinfo_display[]=array("servicename_display"=>$_POST['servicename_display'][$value],"buycount_display"=>$_POST['buycount_display'][$value]);
            }
            $this->_logs(array("另购服务数据：", $arr_serviceinfo));
            $tyunData['BuyServiceinfo'] = json_encode($arr_serviceinfo);
            //$json_crm_serviceinfo = json_encode($arr_serviceinfo);
        }else{
            $tyunData['BuyServiceinfo'] = json_encode(array());
        }
        //购买时间
        $buydate = $_REQUEST['buydate'];
        if(!empty($buydate)){
            $nowTime = date(" H:i:s", time());
            $tyunData['AddDate'] = $buydate.$nowTime;
        }else{
            $nowTime = date("Y-m-d H:i:s", time());
            $tyunData['AddDate'] = $nowTime;
        }

        //print_r($tyunData);die();
        $this->_logs(array("data加密前数据：", $tyunData));
        $tempData['data'] = $this->encrypt(json_encode($tyunData));
        $this->_logs(array("data加密后数据：", $tempData['data']));
        $postData = http_build_query($tempData);//传参数
        $res = $this->https_request($url, $postData);
        $result = json_decode($res, true);
        $result = json_decode($result, true);
        $this->_logs(array("调用".$url."接口后返回数据：", $result));
        //=====测试=======================================
        //$result['success'] = 1;
        //$result['message'] = '';
        //================================================

        //保存调用T云接口返回数据====================================
        $t_params = array(
            'fieldname' => array(
                'contractno' => $tyunData['ContractCode'],
                'classtype' => $t_type_name,
                'tyunurl' => $url,
                'crminput' => json_encode($tyunData),
                'success'=>$result['success'],
                'tyunoutput' => json_encode($result)
            ),
            'userid'			=> $this->userid
        );
        $this->_logs(array("保存调用T云接口返回数据：", $t_params));
        $this->call('saveTyunResposeData', $t_params);
        //============================================================
        if($result['success']){
            //购买id
            $acData['buyid'] = $_REQUEST['buyid'];//购买id
            $acData['expiredate'] = $result['closeDate'];//到期时间
            $acData['contractid'] = $_REQUEST['contractid'];//合同编号ID
            $acData['contractname'] = $_REQUEST['contractname_display'];//合同编号
            $acData['productlife'] = $_REQUEST['productlife'];//年限
            $acData['productid'] = $_REQUEST['productid'];//产品编号
            $acData['t_buyserviceinfo'] = json_encode($arr_crm_serviceinfo);
            $acData['t_contractamount'] = $_REQUEST['contractamount'];//合同金额
            //$acData['crm_buyserviceinfo'] = $json_crm_serviceinfo;
            $acData['t_secretkeyid'] = $_REQUEST['secretkeyid'];
            $acData['classtype'] = $t_type_name;
            $acData['usercode'] = $_REQUEST['loginname'];
            $acData['startdate'] = $_REQUEST['startdate'];
            if($type==2){
                $acData['startdate'] = $result['openDate'];
            }
            if(empty($acData['startdate'])){
                $acData['startdate']=date('Y-m-d H:i:s');
            }
            $acData['buydate'] = $tyunData['AddDate'];
            $acData['customername'] =  $_REQUEST['customername'];
            $acData['customerid'] =  $_REQUEST['customerid'];

            $ActivationCodeData = array();
            $ActivationCodeData['module'] = 'ActivationCode';
            $ActivationCodeData['action'] = 'Save';
            $ActivationCodeData['receivetime'] = $tyunData['AddDate'];
            $ActivationCodeData = array_merge($ActivationCodeData, $acData);

            $nowTime = date("Y-m-d H:i:s", time());
            $this->_logs(array('buyservice_data：', $ActivationCodeData));
            $list = $this->call('saveSecreCodeInfo', array('fieldname'=>array('ActivationCodeData'=>$ActivationCodeData, 'acData'=>$acData), 'userid'=>$this->userid));
            $this->_logs(array('返回list：', $list));

            if(!empty($list[0])){
                $arrMails = array(
                    10567 => 'ingram.ye@71360.com',
                    10568 => 'weibo.wang@71360.com',
                    10582 => 'anne.li@71360.com',
                    10569 => 'daveth.chen@71360.com',
                    10574 => 'andy.ma@71360.com',
                    10566 => 'fei.gu@71360.com',
                    10575 => 'king.yu@71360.com',
                    10598 => 'martin.que@71360.com',
                    10599 => 'david.zhang@71360.com',
                    10600 => 'summer.yu@71360.com',
                    10487 => 'Bill.ma@71360.com',
                    10573 => 'tana.huang@71360.com',
                    10769=>'wilson.xiao@71360.com',
                    10793=>'ingram.ye@71360.com',
                );
                $agents = $_REQUEST['agents'];
                $params = array(
                    'fieldname' => array(
                        'agent_mail' => $arrMails[$agents]?$arrMails[$agents]:'',
                        'customername' => $_REQUEST['customername'],
                        'contractno' => $_REQUEST['contractname_display'],
                        'productid' => $_REQUEST['productid'],
                        'productlife' => $_REQUEST['productlife'],
                        'type' => $_REQUEST['type'],
                        'classtype' => $t_type_name,
                        'oldproductid' => $_REQUEST['oldproductid'],
                        'buyserviceinfo'=>$arr_serviceinfo_display,
                        'Subject'=>$mail_subject,
                        'expiredate'=>$acData['expiredate'],
                        'nowTime' => $nowTime
                    ),
                    'userid'			=> $this->userid
                );
                $list = $this->call('sendMailAgent', $params);
                echo json_encode(array('success'=>1, 'msg'=>''));
            }else{
                echo json_encode(array('success'=>2, 'msg'=>'升级失败'));
            }
        }else{
            echo json_encode(array('success'=>0, 'msg'=>$result['message']));
        }
        exit();
    }

    /**
     * des加密
     * @param unknown $encrypt 原文
     * @return string
     */
    public function encrypt($encrypt, $key="sdfesdcf\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0") {
        $mcrypt = MCRYPT_TRIPLEDES;
        $iv = mcrypt_create_iv(mcrypt_get_iv_size($mcrypt, MCRYPT_MODE_ECB), MCRYPT_RAND);
        $passcrypt = mcrypt_encrypt($mcrypt, $key, $encrypt, MCRYPT_MODE_ECB, $iv);
        $encode = base64_encode($passcrypt);
        return $encode;
    }
    /**
     * des解密
     * @param unknown $decrypt
     * @return string
     */
    /* public function decrypt($decrypt, $key='sdfesdcf'){
    	$decoded = str_replace(' ','%20',$decrypt);
    	$decoded = base64_decode($decrypt);
    	$mcrypt = MCRYPT_TRIPLEDES;
    	$iv = mcrypt_create_iv(mcrypt_get_iv_size($mcrypt, MCRYPT_MODE_ECB), MCRYPT_RAND);
    	$decrypted = mcrypt_decrypt($mcrypt, $key, $decoded, MCRYPT_MODE_ECB, $iv);
    	return $decrypted;
    } */

    public function https_request($url, $data = null){
        $this->_logs(array("发送到T云服务端的url请求", $url));
        $curl = curl_init();
        //curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type:application/x-www-form-urlencoded"));
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);//post提交方式
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        $this->_logs(array("返回处理结果：", $output));
        curl_close($curl);
        return $output;
    }
}
