<?php

class TyunWebBuyServiceClient extends baseapp{
    //线上
    //private $tyunweburl='https://tyapi.71360.com/';//web端地址
    //预发布
    //private $tyunweburl='https://tyapi.71360.com/';//web端地址
    //测试
    private $tyunweburl;//web端地址
    private $mobilecode_url;
    private $product;
    private $doOrder;
    private $getOtherPorducts;
    private $getUserCode;
    private $getUserPackageInfo;
    private $renewDoOrder;
    private $UserRenewProductInfo;
    private $getAllProductsurl;
    private $userPackageUpgradeInfo;
    private $userPackageDegradeInfo;
    private $UserPackageDegradeConfig;
    private $accountRegister;
    private $GetUserPackageDegrade;
    private $AllCategory;
    private $GetSecretKeySurplusMoney;
    private $GetSecretKeySurplusMoneyClient;
    private $tyunClienturls;
    private $batchImportAccountList;
    private $CalculationMoney;
    private $GetUserBuyDomainList;
    private $BindContractCode;
    private $GetPackageIDByClient;
    private $sault='multiModuleProjectDirectoryasdafdgfdhggijfgfdsadfggiytudstlllkjkgff';
    public function setConfigURL(){
        global $tyunweburl,$tyunClienturl;
        $this->tyunweburl=$tyunweburl;
        $this->tyunClienturls=$tyunClienturl;
        $this->mobilecode_url="http://tyunapi.71360.com/api/cms/GetMobileCode";
        $this->product=$this->tyunweburl."api/micro/order-basic/v1.0.0/api/Package/GetPackageList";//获取产品
        $this->doOrder=$this->tyunweburl."api/app/tcloud-agent/v1.0.0/api/crmTyunProductBuy";//购买订单
        $this->getOtherPorducts=$this->tyunweburl."api/micro/order-basic/v1.0.0/api/Product/GetProductList";//另购
        //获取T云用户名接口
        //$this->getUserCode=$this->tyunweburl."api/app/tcloud-account/v1.0.0/authentication/accountCalibration";//用户接口
        //$this->getUserCode=$this->tyunweburl."api/app/tcloud-account/v1.0.0/authentication/checkAccount";//获取用户接口
        $this->getUserCode=$this->tyunweburl."api/app/tcloud-account/v1.0.0/authentication/getAccountList";//获取用户接口
        $this->accountRegister=$this->tyunweburl."api/app/tcloud-account/v1.0.0/authentication/accountRegister";//用户注册接口
        /**T续费接口*/
        $this->getUserPackageInfo=$this->tyunweburl."api/micro/order-basic/v1.0.0/api/Package/GetUserPackageInfo";//用户结续费接口
        $this->renewDoOrder=$this->tyunweburl."api/app/tcloud-agent/v1.0.0/api/crmTyunProductBuy";//续费
        $this->UserRenewProductInfo=$this->tyunweburl."api/micro/order-basic/v1.0.0/api/Product/GetUserProductInfo";
        //$this->getAllProducts=$this->tyunweburl."api/micro/order-basic/v1.0.0/api/User_Product/GetUserPackageAndProductPageData";
        $this->getAllProductsurl=$this->tyunweburl."api/micro/order-basic/v1.0.0/api/User_Product/GetUserPackageAndProductPageData";
        $this->userPackageUpgradeInfo=$this->tyunweburl."api/micro/order-basic/v1.0.0/api/Package/GetUserPackageUpgradeInfo";
        $this->userPackageUpgradeMoney=$this->tyunweburl."api/micro/order-basic/v1.0.0/api/Package/GetUserPackageUpgradeMoney";
        $this->userPackageDegradeInfo=$this->tyunweburl."api/micro/order-basic/v1.0.0/api/Package/GetUserPackageDegradeInfo";
        $this->UserPackageDegradeConfig=$this->tyunweburl."api/micro/order-basic/v1.0.0/api/Package/GetUserPackageDegrade";
        $this->AllCategory=$this->tyunweburl.'api/micro/order-basic/v1.0.0/api/Category/AllCategory';
        $this->GetUserPackageDegrade=$this->tyunweburl.'api/micro/order-basic/v1.0.0/api/Package/GetUserPackageDegrade';
        $this->GetSecretKeySurplusMoneyClient=$tyunClienturl.'api/CRM/GetSecretKeySurplusMoney';
	    $this->GetSecretKeySurplusMoney=$this->tyunweburl.'api/micro/order-basic/v1.0.0/api/Package/GetPackageMoneyByClient';
        $this->batchImportAccountList=$this->tyunweburl.'api/app/tcloud-account/v1.0.0/accountImport/batchImportAccountList';
        $this->getLoginName = $this->tyunClienturls.'api/CRM/GetLoginNameByMobile';
        $this->CalculationMoney = $this->tyunweburl.'api/app/tcloud-agent/v1.0.0/api/calculationMoney';
        $this->GetUserBuyDomainList=$this->tyunweburl."api/User_DomainInfo/GetUserBuyDomainList";
        $this->BindContractCode=$this->tyunweburl.'api/micro/order-basic/v1.0.0/api/Order/BindContractCode';
        $this->GetPackageIDByClient=$this->tyunweburl.'api/micro/order-basic/v1.0.0/api/Package/GetPackageIDByClient';

    }
    // 公共的升级续费降级 获取金额(续费 surplusMoney 可不填或者传任意数 )
    public function getSecretKeySurplusMoney(){
        $this->setConfigURL();
        if(isset($_REQUEST['BuyTerm']) && isset($_REQUEST['ProductType']) && isset($_REQUEST['surplusMoney'])&& isset($_REQUEST['clientPackageID'])){
            /*$categoryID = $_REQUEST['categoryID'];*/
            $postData=json_encode(array('BuyTerm'=>intval($_REQUEST['BuyTerm']),'Discount'=>1,'agentType'=>0,'ProductType'=>intval($_REQUEST['ProductType']),'surplusMoney'=>floatval($_REQUEST['surplusMoney']),'clientPackageID'=>$_REQUEST['clientPackageID'],'oldSurplusMoney'=>floatval($_POST['oldSurplusMoney'])));
            /*echo $postData;die();*/
            $time=time().'123';
            $sault=$time.$this->sault;
            $token=md5($sault);
            $curlset=array(CURLOPT_HTTPHEADER=>array(
                "Content-Type:application/json",
                "S-Request-Token:".$token,
                "S-Request-Time:".$time));
            $res = $this->https_request($this->GetSecretKeySurplusMoney, $postData,$curlset);
            echo $res;exit();
        }else{
            echo json_encode(array(  "success"=> true,"code"=>500,"message"=> "操作成功"));exit();
        }

    }
    /**
     * 购买页面
     */
    public function index(){
        global $arr_cs_admin,$arr_ignore_check;
        $this->specialAuthority();
        $flag=false;
        if(in_array($this->userid,$arr_cs_admin)){
            $params = array(
                'fieldname' => array(
                    'module' => 'TyunWebBuyService',
                    'action' => 'getFirstWorkDay',
                    'userid' => $this->userid
                ),
                'userid' => $this->userid
            );
            $list = $this->call('getComRecordModule', $params);
            if($list[0]==2){
                $flag=true;
                $this->smarty->assign('MINDATE',date('Y-m',strtotime('-1 months')).'-01');
                $this->smarty->assign('MAXDATE',date('Y-m-d'));
            }
        }
        $this->smarty->assign('BUYDATESHOW',$flag);
        $this->smarty->display('TyunWebBuyServiceClient/add.html');
    }
    public function specialAuthority(){
        global $arr_ignore_check,$arr_cs_admin,$arr_service_role,$arr_cs_special;
        if(empty($this->roleid)){
            $params = array(
                'fieldname'=>array(
                ),
                'userid'			=> $this->userid
            );
            $list = $this->call('getUserRole', $params);
            if(!empty($list[0])){
                $this->roleid = $list[0]["roleid"];
            }
        }
        //是否客服管理
        $is_cs_admin = in_array($this->userid,$arr_cs_admin);
        $this->smarty->assign('is_cs_admin', $is_cs_admin);
        $is_cs = in_array($this->roleid,$arr_service_role) || in_array($this->userid,$arr_cs_special);
        $this->smarty->assign('is_cs', $is_cs);
        $this->smarty->assign('DATEC',date('Y-m-d'));
        $this->smarty->assign('agents',$this->getAgents());
        $is_ignore_check = in_array($this->userid,$arr_ignore_check)?1:0;
        $this->smarty->assign('is_ignore_check', $is_ignore_check);
    }

    /**
     * 续费页面
     */
    public function renew(){
        $this->specialAuthority();
        global $arr_ignore_check,$arr_cs_admin,$arr_service_role,$arr_cs_special;
        $is_cs_admin = in_array($this->userid,$arr_cs_admin) || in_array($this->roleid,$arr_service_role) || in_array($this->userid,$arr_cs_special) || in_array($this->userid,$arr_ignore_check);
        $this->smarty->assign('isService',$is_cs_admin);
        $this->smarty->display('TyunWebBuyServiceClient/renew.html');
    }

    /**
     * 升级页面
     */
    public function upgrade(){
        $this->specialAuthority();
        global $arr_ignore_check,$arr_cs_admin,$arr_service_role,$arr_cs_special;
        $is_cs_admin = in_array($this->userid,$arr_cs_admin) || in_array($this->roleid,$arr_service_role) || in_array($this->userid,$arr_cs_special) || in_array($this->userid,$arr_ignore_check);
        $this->smarty->assign('isService',$is_cs_admin);
        $this->smarty->display('TyunWebBuyServiceClient/upgrade.html');
    }
    /**
     * 降级页面
     */
    public function degrade(){
        $this->specialAuthority();
        global $arr_ignore_check,$arr_cs_admin,$arr_service_role,$arr_cs_special;
        $is_cs_admin = in_array($this->userid,$arr_cs_admin) || in_array($this->roleid,$arr_service_role) || in_array($this->userid,$arr_cs_special) || in_array($this->userid,$arr_ignore_check);
        $this->smarty->assign('isService',$is_cs_admin);
        $this->smarty->display('TyunWebBuyServiceClient/degrade.html');

    }
    public function searchTyunBuyServiceContract(){
        global $arr_cs_admin,$arr_service_role,$arr_cs_special,$arr_ignore_check;
        $this->specialAuthority();
        $contract_no = trim($_REQUEST['contract_no']);
        $customerid = trim($_REQUEST['customerid']);
        $tempid = trim($_REQUEST['tempid']);
        $classtype = trim($_REQUEST['classtype']);
        //是否客服管理
        $is_cs_admin = in_array($this->userid,$arr_cs_admin) || in_array($this->roleid,$arr_service_role) || in_array($this->userid,$arr_cs_special) || in_array($this->userid,$arr_ignore_check);
        if($classtype=='buy' && !in_array($this->userid,$arr_ignore_check)){
            $is_cs_admin='';
        }
        if(!empty($contract_no)){
            if($tempid=='accountid'){
                $params = array(
                    'fieldname'=>array(
                        'module'		=>'TyunWebBuyService',
                        'action'		=> 'getAccountList',
                        'searchValue'   =>$contract_no,
                        'customerid' => $customerid,
                        'is_cs_admin'=>$is_cs_admin,
                        'userid'		=> $this->userid
                    ),
                    'userid'			=> $this->userid
                );

            }elseif($tempid=='servicecontractsid'){
                $params = array(
                    'fieldname'=>array(
                        'module'		=>'TyunWebBuyService',
                        'action'		=> 'getServiceContractsList',
                        'searchValue'	=> $contract_no,
                        'customerid'	=> $customerid,
                        'is_cs_admin'	=> $is_cs_admin,
                        'userid'		=> $this->userid
                    ),
                    'userid'			=> $this->userid
                );
            }else{
                echo "";exit;
            }
            $list = $this->call('getComRecordModule', $params);
            echo json_encode($list[0]);
            exit;
        }
        echo "";exit;

    }
    /**
     * 获取手机验证码
     */
    public function getMobileVerify(){
        if(!session_id()){session_start();}
        $this->setConfigURL();
        $mobile = $_REQUEST['mobile'];
        $data['data'] = $this->encrypt($mobile);
        $postData = http_build_query($data);//传参数
        $res = $this->https_request($this->mobilecode_url, $postData);
        $result = json_decode($res, true);
        $result = json_decode($result, true);
        if($result['success']){
            if(!session_id()){session_start();}
            $_SESSION['setMCodeVerify'] = $result['message'];
            //echo json_encode(array('success'=>1,'data'=>$result['message'], 'msg'=>'已发送'));
            echo json_encode(array('success'=>1,'msg'=>'已发送'));
        }else{
            echo json_encode(array('success'=>0, 'msg'=>$result['message']));
        }
        exit;
    }
    public function https_request($url, $data = null,$curlset=array()){
        $this->_logs(array("发送到T云服务端的url请求", $url));
//        $this->_logs(array("发送到T云服务端的data", $data));
        $curl = curl_init();
        if(!empty($curlset)){
            foreach($curlset as $key=>$value){
                curl_setopt($curl, $key, $value);
            }
        }
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
    public function checkBasicInfo($flag=0){
        $servicecontractsid =$_REQUEST['servicecontractsid'];
        $servicecontractsid_display =$_REQUEST['servicecontractsid_display'];
        $accountid =$_REQUEST['accountid'];
        $accountid_display =$_REQUEST['accountid_display'];
        $mobile =$_REQUEST['mobile'];
        $mobilevcode =$_REQUEST['mobilevcode'];
        $signaturetype = $_REQUEST['signaturetype'];
        $returnmsg['flag']=true;
        do{
            if($signaturetype=='papercontract') {

                if ($servicecontractsid <= 0) {
                    $returnmsg['msg'] = '合同无效，请重新选择';
                    break;
                }
                if ($accountid <= 0) {
                    $returnmsg['msg'] = '客户名称无效，请重新选择';
                    break;
                }
              if($mobilevcode==''){
                $returnmsg['msg']='验证码无效，请重新获取';
                break;
            }
            if($_SESSION['setMCodeVerify']!=$mobilevcode){
                $returnmsg['msg']='验证码无效，请重新获取';
                break;
            }
            }
            if(!preg_match("/^1[3456789]\d{9}$/",$mobile)){
                $returnmsg['msg']='手机号码无效';
                break;
            }

            if($flag==0){
                $tyunusercode=$flag!=0?'':$_REQUEST['tyunusercode'];
                if(!$this->checkTyunExistBuy($tyunusercode)){
                    $returnmsg['msg']='存在未签收的合同，请先处理！';
                    break;
                }

            }
            $returnmsg['flag']=false;
        }while(0);
        return $returnmsg;
    }
    /**
     * 获取T云账户
     */
    public function getTyunUserCode(){
        $this->setConfigURL();
        $accountid =$_REQUEST['accountid'];
        $returnmsg=array('success'=>0);
        do{
            $checkdata=$this->checkBasicInfo(1);
            if($checkdata['flag']){
                $returnmsg['msg']=$checkdata['msg'];
                break;
            }
            if($accountid>0){
                $returnmsg=$this->getClientMigration();
            }else {
                $returnmsg = array('success' => 0, 'msg' => '请先选择客户');
            }
        }while(0);
        echo json_encode($returnmsg);
        exit;
    }

    /**
     * @return 代理商ID
     */
    public function getAgents(){
        $params = array(
            'userid'			=> $this->userid
        );
        $list = $this->call('getagentid', $params);
        return $list[0];
    }
    public function getproduct(){
        $this->setConfigURL();
        $categoryID = $_REQUEST['categoryID'];
        $postData=json_encode(array('categoryID'=>$categoryID));
        $time=time().'123';
        $sault=$time.$this->sault;
        $token=md5($sault);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));
        $res = $this->https_request($this->product, $postData,$curlset);
        echo $res;
        exit;
    }
    /**
     * 获取续费套餐
     */
    public function getUserRenewProductInfo(){
        $tyunusername = $_REQUEST['tyunusername'];//用户ID
        $classtype = $_REQUEST['classtype'];//升级,续费,降级

        $params = array(
            'fieldname'=>array(
                'tyun_account'	=> $tyunusername,
                'tyun_type'	=> $classtype,
            ),
            'userid'			=> $this->userid
        );
        $list = $this->call('searchTyunBuyServiceInfo', $params);
        $lista = $list[0];
        $lista['buyList']=$lista['buyList'][0];
        $realproductid='';
        $productid = $lista['buyList']['productid'];
        if($productid){
            $postData=json_encode(array(
                'clientPackageID'=>$productid,
            ));
            $this->_logs(array($this->GetPackageIDByClient.'，请求数据：'.$postData));
            $curlset=array(CURLOPT_HTTPHEADER=>array(
                "Content-Type:application/json"
            ));
            $result = $this->https_request($this->GetPackageIDByClient.'?'.http_build_query($postData),'',$curlset);
            $res = json_decode($result,true);
            $realproductid = $res['data']['packageID'];
        }
        $lista['buyList']['realproductid'] = $realproductid;
        //获取可用的域名列表
        $userid = $_REQUEST['userid'];
        $resdata = array('userID'=>$userid);
        $data['data'] = $this->encrypt(json_encode($resdata));
        $postData = http_build_query($data);//传参数
        $res = $this->https_request($this->GetUserBuyDomainList, $postData);
        $result = json_decode($res, true);
        $lista['domains'] = $result['data'];

        echo json_encode($lista);
        exit;
    }
   
    // 续费升降级 获取用户id
    private  function  batchImportAccountList($parmas){
        $this->setConfigURL();
        $time=time().'123';
        $sault=$time.$this->sault;
        $token=md5($sault);
        $postData=json_encode(array(array("loginName"=>$parmas['loginName'],'nickName'=>$parmas['nickName'],'phoneNumber'=>$parmas['phoneNumber'],'invitationCode'=>$parmas['invitationCode'],'cid'=>$parmas['cid'],'status'=>$parmas['status'],'accountSource'=>1)));
        $this->_logs(array("batchImportAccountList：".$this->batchImportAccountList, $postData));
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));
        $res = $this->https_request($this->batchImportAccountList, $postData,$curlset);
        $this->_logs(array("batchImportAccountList：", $res));
        return $res;
    }
    /**
     * 续费和降级都使用到了这个订单   续费和 降级共用的接口
     */
    public function renewdoOrder(){
        $this->setConfigURL();
        $servicecontractsid =$_REQUEST['servicecontractsid'];//服务合同id
        $servicecontractsid_display =$_REQUEST['servicecontractsid_display'];//服务合同编号
        $accountid =$_REQUEST['accountid'];//客户id
        $accountid_display =$_REQUEST['accountid_display'];//客户名称
        $mobile =$_REQUEST['mobile'];//手机号
        $mobilevcode =$_REQUEST['mobilevcode'];//验证码
        $buyyear =$_REQUEST['buyyear'];//年限
        $classtype =$_REQUEST['classtype'];//合同类型购买和续费
        $clientPackageID=$_REQUEST['clientPackageID'];// 产品id
        $categoryid=$_REQUEST['categoryid'];//产品分ID
        $oldproductname=$_REQUEST['oldproductname'];
        $servicetotal=$_REQUEST['servicetotal'];
        $tyunusercode=$_REQUEST['tyunusercode'];
        $tyunusercodename=$_REQUEST['tyunusercodetext'];
        $agents =$_REQUEST['agents'];
        $expiredate=$_REQUEST['expiredate'];
        $returnmsg=array('success'=>0);
        $customerstype=$_REQUEST['customerstype'];
        $oldproductid=$_REQUEST['oldproductid'];
        $orderordercode=$_REQUEST['activacode'];
        $oldcustomerid = $_REQUEST['oldcustomerid'];
        $oldcustomername =$_REQUEST['oldcustomername'];
        $chooseUserProducts = $_REQUEST['chooseuserproduct'];
        $buydate=empty($_POST['buydate'])?date('Y-m-d H:i:s'):date('Y-m-d',strtotime($_POST['buydate'])).date(' H:i:s');
        // 判断是降级还是续费
        if(isset($_REQUEST['is_degrade']) && $_REQUEST['is_degrade']==1){
            $productType=6;
            $type = 'degrade';
            $type_name = '降级';
        }else{
            $productType=7;
            $type = 'renew';
            $type_name = '续费';
        }
        do{
            $checkdata=$this->checkBasicInfo();
            if($checkdata['flag']){
                $returnmsg['msg']=$checkdata['msg'];
                break;
            }
            $par = array("loginName"=>$tyunusercodename,'nickName'=>$accountid_display,'phoneNumber'=>$mobile,'invitationCode'=>$agents,'cid'=>$accountid,'status'=>1);
            //用户接口
            $userinfo=$this->batchImportAccountList($par);
            /*echo $userinfo;die();*/
            $userinfo = json_decode($userinfo,true);
            if($userinfo['code']==511 || $userinfo['code']==200){
                $tyunusercode=$userinfo['data']['id'];
            }else{
                $returnmsg['msg']=$userinfo['$userinfo'];
                break;
            }
            $ProductInfo = $this->handleOtherProduct($_REQUEST);

            $tyunparams=array(
                "type"=>1,//0线上1线下
                "productType"=>$productType,//商品类型(5升级 6降级 7 续费购买)
                "contractCode"=>$servicecontractsid_display,//合同编号
                "userID"=>$tyunusercode,//用户编号
                "agentIdentity"=>$agents,//代理商ID
                "discount"=>1,//折扣
                "categoryID"=>$categoryid,//产品分类(0国内版 1一带一路)
                "buyTerm"=>$buyyear,//购买年限
                "clientPackageID"=>$clientPackageID,//套餐编号'fb016797-4296-11e6-ad98-00155d069461'
                "contractMoney"=>$servicetotal,
                "addDate"=>date('Y-m-d H:i:s'),
                "crmOrderFlag"=>1,//ERP提交订单
                'oldCloseDate'=>$expiredate,
                "productInfo"=>$ProductInfo,
                "chooseUserProducts"=>$chooseUserProducts,//可套ID
            );
            $this->_logs(array("renewdoOrder：", json_encode($tyunparams)));
            $postData=json_encode($tyunparams);
            $time=time().'123';
            $sault=$time.$this->sault;
            $token=md5($sault);
            $curlset=array(CURLOPT_HTTPHEADER=>array(
                "Content-Type:application/json",
                "S-Request-Token:".$token,
                "S-Request-Time:".$time),
                CURLINFO_HEADER_OUT=>array(true));
            $res = $this->https_request($this->renewDoOrder, $postData,$curlset);
            $this->_logs(array("renewdoOrderreturndata ：",$res));
            $data=json_decode($res,true);
            if($data['code']==200){
                $r_params = array(
                    'servicecontractsid'=>$servicecontractsid,
                    'servicecontractsid_display'=>$servicecontractsid_display,
                    'accountid'=>$accountid,
                    'accountid_display'=>$accountid_display,
                    'mobile'=>$mobile,
                    'mobilevcode'=>$mobilevcode,
                    'classtype'=>$classtype,
                    'module'		=>'TyunWebBuyService',
                    'action'		=> 'AddbuyOrder',
                    'userid'		=> $this->userid,
                    'res'           =>$res,
                    'customer_name'=>$_SESSION['customer_name'],
                    'phone_mobile'=>$_SESSION['phone_mobile'],
                    'tyunurl'=>$this->renewDoOrder,
                    'contractprice'=>$servicetotal,
                    'usercodeid'=>$tyunusercode,
                    'usercode'=>$tyunusercodename,
                    "agentIdentity"=>$agents,//代理商ID
                    'customerstype'=>$customerstype,
                    'oldproductid'=>$oldproductid,
                    'oldproductname'=>$oldproductname,
                    'orderordercode'=>$orderordercode,
                    'oldcustomerid'=>$oldcustomerid,
                    'oldcustomername'=>$oldcustomername
                );

                $this->handleTyunResult($data,$r_params,$tyunparams,$type);

                $returnmsg['msg']=$type_name.'成功';
                $returnmsg['success']=1;
            }else{
                $returnmsg['msg']=$data['message'];
                break;
            }
        }while(0);
        echo json_encode($returnmsg);
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
        //$this->_logs(array($loginname.'账号获取订单数，返回结果：'.$list[0]));
        if($list[0] <= 0){
            return true;
        }
        return false;
    }

    /**
     * 是否存在未签合同
     */
    public function checkTyunExistBuyReturn(){
        $tyunusercode=$_REQUEST['tyunusercode'];
        if($this->checkTyunExistBuy($tyunusercode)){
            $return=array('success'=>1);

        }else{
            $return=array('success'=>0,'msg'=>'存在未签的合同，请先处理');
        }
        echo json_encode($return);
    }


    public function getRealProductId(){
        $clientproductid = $_REQUEST['buyproductid'];
        $postData=json_encode(array(
            'clientPackageID'=>$clientproductid,
            ));
        $this->_logs(array($this->GetPackageIDByClient.'，请求数据：'.$postData));
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json"
        ));
        $res = $this->https_request($this->GetPackageIDByClient.'?'.http_build_query($postData), '',$curlset);
//        echo $res;
        $data = json_decode($res,true);
        echo json_encode(array('success'=>true,'data'=>array('realproductid'=>$data['packageID'])));
    }

    /**
     * 升级获取产品
     */
    public function getAllProducts(){
        $this->setConfigURL();
        $tyunusername = $_REQUEST['tyunusername'];//用户ID
        $classtype = $_REQUEST['classtype'];//升级,续费,降级
        $params = array(
            'fieldname'=>array(
                'tyun_account'	=> $tyunusername,
                'tyun_type'	=> $classtype,
            ),
            'userid'			=> $this->userid
        );
        $list = $this->call('searchTyunBuyServiceInfo', $params);
        $lista = $list[0];
        if(!empty($lista)){
            $parm_productid = trim($lista['buyList'][0]['productid']);
            $is_degrade = $classtype=='upgrade'?0:1;
            if(!empty($parm_productid)) {
                $params = array(
                    'fieldname' => array(
                        'module' => 'TyunWebBuyService',
                        'action' => 'searchTyunUpgradeProduct',
                        'p_productid'	=> $parm_productid,
                        'is_getname'	=> 0,
                        'is_degrade'	=>$is_degrade
                    ),
                    'userid' => $this->userid
                );
                $list = $this->call('getComRecordModule', $params);

                $list = $list[0];
                if(count($list) == 0){
                    if($is_degrade == '1'){
                        echo json_encode(array('success'=>false,'code'=>500,'message'=>'未查询到对应降级版本','productList'=>$list));
                    }else{
                        echo json_encode(array('success'=>false,'code'=>500,'message'=>'未查询到对应升级版本','productList'=>$list));
                    }
                }else{
                    //获取可用的域名列表
                    $userid = $_REQUEST['tyunusercode'];
                    $resdata = array('userID'=>$userid);
                    $data['data'] = $this->encrypt(json_encode($resdata));
                    $postData = http_build_query($data);//传参数
                    $res = $this->https_request($this->GetUserBuyDomainList, $postData);
                    $result = json_decode($res, true);
                    $domains = $result['data'];

                    $startdate=time();
                    $enddate=strtotime("2013-4-05");
                    $days=round(($enddate-$startdate)/3600/24) ;
                    if($days>0){
                        $miniupgradeyear=ceil($days/366);
                    }else{
                        $miniupgradeyear=1;
                    }
                    if(count($list) == 1){
                        echo json_encode(array('success'=>true,'code'=>200,'message'=>'','productList'=>$lista['buyList'][0],'miniupgradeyear'=>$miniupgradeyear,'listp'=>array(current($list)),'domains'=>$domains));
                    }else{
                        echo json_encode(array('success'=>true,'code'=>200,'message'=>'','productList'=>$lista['buyList'][0],'miniupgradeyear'=>$miniupgradeyear,'listp'=>$list,'domains'=>$domains));
                    }
                }
                exit;
            }
        }
        $list['code']=500;
        echo json_encode($list);
        exit;
    }

    /**
     * 获取升级周期
     */
    public function getUpgardeCycle(){
        $this->setConfigURL();
        $userID = $_REQUEST['tyunusercode'];//用户ID
        $productid=$_POST['productid'];
        $buyyear=$_POST['buyyear'];
        $SecretKeyID=$_POST['SecretKeyID'];
        $ContractCode=$_POST['ContractCode'];
        $OldCloseDate=$_POST['OldCloseDate'];
        $oldSurplusMoney=$_POST['oldSurplusMoney'];
        $OldProductID=$_POST['OldProductID'];
        $postData=json_encode(array('LoginName'=>$userID,
            'SecretKeyID'=>$SecretKeyID,
            'ContractCode'=>$ContractCode,
            'OldProductID'=>$OldProductID,
            'OldCloseDate'=>$OldCloseDate,
            'UpgradeProductID'=>$productid,
            'UpgradeYear'=>(int)$buyyear,
            'AddDate'=>date('Y-m-d')));
        $this->_logs(array($this->GetSecretKeySurplusMoneyClient.'，请求数据：'.$postData));
        $tempData['data'] = $this->encrypt($postData);
        $this->_logs(array($this->GetSecretKeySurplusMoneyClient.'，返回结果：'.$tempData['data']));
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json"
            ));
        $res = $this->https_request($this->GetSecretKeySurplusMoneyClient, json_encode($tempData),$curlset);
        $res=str_replace(array('\\r','\\n'),'',$res);
        $res=str_replace('\"','"',$res);
        $res=preg_replace('/^"|"$/','',$res);
        $data=json_decode($res,true);
        if($data['success']){
            $params = array(
                'fieldname' => array(
                    'module' => 'TyunWebBuyService',
                    'action' => 'calcSurplusMoney',
                    'usercode'	=> $userID
                ),
                'userid' => $this->userid
            );
            $list = $this->call('getComRecordModule', $params);
            $data['surplusMoney']=$list[0];
            echo json_encode($data);
        }else{
            echo $res;
        }

    }

    /**
     * 升级订单添加
     */
    public function upgardedoOrder(){
        $this->setConfigURL();
        $servicecontractsid =$_REQUEST['servicecontractsid'];//服务合同id
        $servicecontractsid_display =$_REQUEST['servicecontractsid_display'];//服务合同编号
        $accountid =$_REQUEST['accountid'];//客户id
        $accountid_display =$_REQUEST['accountid_display'];//客户名称
        $mobile =$_REQUEST['mobile'];//手机号
        $mobilevcode =$_REQUEST['mobilevcode'];//验证码
        $buyyear =$_REQUEST['buyyear'];//年限
        $classtype =$_REQUEST['classtype'];//合同类型购买和续费
        $servicetotal=$_REQUEST['servicetotal'];
        $tyunusercode=$_REQUEST['tyunusercode'];
        $tyunusercodetext = $_REQUEST['tyunusercodetext'];
        $categoryid=$_REQUEST['categoryid'];
        $clientPackageID=$_REQUEST['buyproduct'];
        $unusedamount=$_REQUEST['unusedamount'];
        $upgradecost=$_REQUEST['upgradecost'];
        $oldCloseDate=$_REQUEST['oldexpiredate_display'];
        $orderordercode=$_REQUEST['activacode'];
        $oldproductname=$_REQUEST['oldproductname'];
        $oldSurplusMoney=$_REQUEST['oldSurplusMoney'];
        $oldproductid=$_REQUEST['oldproductid'];
        $customerstype='clientmigration';
        $agents =$_REQUEST['oldagents'];
        $chooseUserProducts = $_REQUEST['chooseuserproduct'];
        $buydate=empty($_POST['buydate'])?date('Y-m-d H:i:s'):date('Y-m-d',strtotime($_POST['buydate'])).date(' H:i:s');
        $returnmsg=array('success'=>0);
        do{
            $checkdata=$this->checkBasicInfo();
            if($checkdata['flag']){
                $returnmsg['msg']=$checkdata['msg'];
                break;
            }
            $params=array("loginName"=>$tyunusercodetext,
                'phoneNumber'=>$mobile,
                'invitationCode'=>$agents,//代理商ID
                'nickName'=>$accountid_display,
                'cid'=>$accountid,
                'status'=>1);
            $getUserCode=$this->batchImportAccountList($params);
            $userCodeJsonData=json_decode($getUserCode,true);
            $tyunusercodeid=$userCodeJsonData['data']['id'];
            if(empty($tyunusercodeid)){
                $returnmsg['msg']='账户创建失败';
                break;
            }
            $ProductInfo= $this->handleOtherProduct($_REQUEST);
            $tyunparams=array(
                "type"=>1,//0线上1线下
                "productType"=>5,//商品类型(5迁移升级 6迁移降级 7迁移续费)
                "contractCode"=>$servicecontractsid_display,//合同编号
                "userID"=>$tyunusercodeid,//用户编号
                "agentIdentity"=>0,//代理商ID
                "discount"=>1,//折扣
                "categoryID"=>$categoryid,//产品分类(0国内版 1一带一路)
                "buyTerm"=>$buyyear,//购买年限
                "clientPackageID"=>$clientPackageID,//套餐编号
                "contractMoney"=>$servicetotal,
                "oldCloseDate"=>$oldCloseDate,
                "surplusMoney"=>$unusedamount,
                "oldSurplusMoney"=>$oldSurplusMoney,
                "crmOrderFlag"=>1,//ERP提交订单
                "productInfo"=>$ProductInfo,
                "chooseUserProducts"=>$chooseUserProducts,//可套ID
                "addDate"=>$buydate,
            );
            $this->_logs(array("upgardedoOrder：", json_encode($tyunparams)));
            $postData=json_encode($tyunparams);
            $time=time().'123';
            $sault=$time.$this->sault;
            $token=md5($sault);
            $curlset=array(CURLOPT_HTTPHEADER=>array(
                "Content-Type:application/json",
                "S-Request-Token:".$token,
                "S-Request-Time:".$time));
            $res = $this->https_request($this->renewDoOrder, $postData,$curlset);
            $this->_logs(array($this->renewDoOrder."upgardedoOrderreturndata ：",$res));
            $data=json_decode($res,true);
            if($data['code']==200){
                $r_params = array(
                    'servicecontractsid'=>$servicecontractsid,
                    'servicecontractsid_display'=>$servicecontractsid_display,
                    'accountid'=>$accountid,
                    'accountid_display'=>$accountid_display,
                    'mobile'=>$mobile,
                    'mobilevcode'=>$mobilevcode,
                    'classtype'=>$classtype,
                    'module'		=>'TyunWebBuyService',
                    'action'		=> 'AddbuyOrder',
                    'userid'		=> $this->userid,
                    'res'           =>$res,
                    'customer_name'=>$_SESSION['customer_name'],
                    'phone_mobile'=>$_SESSION['phone_mobile'],
                    'tyunurl'=>$this->renewDoOrder,
                    'contractprice'=>$servicetotal,
                    'surplusmoney'=>$unusedamount,
                    'upgradecost'=>$upgradecost,
                    'usercodeid'=>$tyunusercodeid,
                    "agentIdentity"=>$agents,//代理商ID
                    'usercode'=>$tyunusercode,
                    'customerstype'=>$customerstype,
                    'orderordercode'=>$orderordercode,
                    'oldproductname'=>$oldproductname,
                    'oldproductid'=>$oldproductid,
                );
                $this->handleTyunResult($data,$r_params,$tyunparams,'upgrade');
                $returnmsg['msg']='升级成功';
                $returnmsg['success']=1;
            }else{
                $returnmsg['msg']=$data['message'];
                break;
            }
        }while(0);
        echo json_encode($returnmsg);
        exit;
    }

    /**
     * 获取升级差价
     */
    public function getUserPackageDegrade(){
        $this->setConfigURL();
        $userID = $_REQUEST['tyunusercode'];//用户ID
        $productid=$_POST['productid'];
        $buyyear=$_POST['buyyear'];
        $type = 0;//类型0 直销 1 渠道
        $postData=json_encode(array("agentType"=>$type,'userID'=>$userID,"packageID"=>$productid,"detailBuyTerm"=>$buyyear,"discount"=>1));
        $this->_logs(array($this->userPackageUpgradeMoney.'，返回结果：'.$postData));
        $time=time().'123';
        $sault=$time.$this->sault;
        $token=md5($sault);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));
        $res = $this->https_request($this->UserPackageDegradeConfig, $postData,$curlset);
        echo $res;
    }
    /**
     *获取产品分类
     */
    public function getAllCategory(){
        /*$this->setConfigURL();
        $time=time().'123';
        $sault=$time.$this->sault;
        $token=md5($sault);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));
        $res = $this->https_request($this->AllCategory, array(),$curlset);*/
        $res = '{
          "success": true,
          "statuscode": 200,
          "code": 200,
          "message": "操作成功",
          "data": [
            {
              "ID": 0,
              "Title": "T云国内版",
              "IsPackage": true,
              "Prefix": "T",
              "Sort": 0,
              "Status": 0
            }]
        }';
        echo $res;
        exit;
    }
    /**
     * 添加降级订单
     */
    public function degardedoOrder(){
        $this->setConfigURL();
        $servicecontractsid =$_REQUEST['servicecontractsid'];//服务合同id
        $servicecontractsid_display =$_REQUEST['servicecontractsid_display'];//服务合同编号
        $accountid =$_REQUEST['accountid'];//客户id
        $accountid_display =$_REQUEST['accountid_display'];//客户名称
        $mobile =$_REQUEST['mobile'];//手机号
        $mobilevcode =$_REQUEST['mobilevcode'];//验证码
        $buyyear =$_REQUEST['buyyear'];//年限
        $classtype =$_REQUEST['classtype'];//合同类型购买和续费
        $servicetotal=$_REQUEST['servicetotal'];
        $tyunusercode=$_REQUEST['tyunusercode'];
        $tyunusercodeid=$_REQUEST['tyunusercodeid'];
        $categoryid=$_REQUEST['categoryid'];
        $clientPackageID=$_REQUEST['buyproduct'];
        $customerstype='clientmigration';
        $agents =$_REQUEST['agents'];
        $returnmsg=array('success'=>0);
        $oldcustomerid = $_REQUEST['oldcustomerid'];
        $oldcustomername =$_REQUEST['oldcustomername'];
        do{
            $checkdata=$this->checkBasicInfo();
            if($checkdata['flag']){
                $returnmsg['msg']=$checkdata['msg'];
                break;
            }
            $ProductInfo = $this->handleOtherProduct($_REQUEST);
            $tyunparams=array(
                "type"=>1,//0线上1线下
                "productType"=>6,//商品类型(5迁移升级 6迁移降级 7迁移续费)
                "contractCode"=>$servicecontractsid_display,//合同编号
                "userID"=>$tyunusercodeid,//用户编号
                "agentIdentity"=>0,//代理商ID
                "discount"=>1,//折扣
                "categoryID"=>$categoryid,//产品分类(0国内版 1一带一路)
                "buyTerm"=>$buyyear,//购买年限
                "clientPackageID"=>$clientPackageID,//套餐编号
                "contractMoney"=>$servicetotal,
                "crmOrderFlag"=>1,//ERP提交订单
                "productInfo"=>$ProductInfo
            );
            $this->_logs(array("upgardedoOrder：", json_encode($tyunparams)));
            $postData=json_encode($tyunparams);
            $time=time().'123';
            $sault=$time.$this->sault;
            $token=md5($sault);
            $curlset=array(CURLOPT_HTTPHEADER=>array(
                "Content-Type:application/json",
                "S-Request-Token:".$token,
                "S-Request-Time:".$time));
            $res = $this->https_request($this->renewDoOrder, $postData,$curlset);
            $this->_logs(array($this->renewDoOrder."upgardedoOrderreturndata ：",$res));
            $data=json_decode($res,true);
            if($data['code']==200){
                $r_params = array(
                    'servicecontractsid'=>$servicecontractsid,
                    'servicecontractsid_display'=>$servicecontractsid_display,
                    'accountid'=>$accountid,
                    'accountid_display'=>$accountid_display,
                    'mobile'=>$mobile,
                    'mobilevcode'=>$mobilevcode,
                    'classtype'=>$classtype,
                    'module'		=>'TyunWebBuyService',
                    'action'		=> 'AddbuyOrder',
                    'userid'		=> $this->userid,
                    'res'           =>$res,
                    'customer_name'=>$_SESSION['customer_name'],
                    'phone_mobile'=>$_SESSION['phone_mobile'],
                    'tyunurl'=>$this->renewDoOrder,
                    'contractprice'=>$servicetotal,
                    'usercodeid'=>$tyunusercodeid,
                    "agentIdentity"=>$agents,//代理商ID
                    'usercode'=>$tyunusercode,
                    'customerstype'=>$customerstype,
                    'oldcustomerid'=>$oldcustomerid,
                    'oldcustomername'=>$oldcustomername
                );
                $this->handleTyunResult($data,$r_params,$tyunparams,'degrade');
                $returnmsg['msg']='升级成功';
                $returnmsg['success']=1;
            }else{
                $returnmsg['msg']=$data['message'];
                break;
            }
        }while(0);
        echo json_encode($returnmsg);
        exit;
    }

    /**
     * 获取客户usercode
     * @return array
     */
    public function getClientMigration() {
        $this->setConfigURL();
        $mobile = $_REQUEST['mobile'];
        $accountid=$_REQUEST['accountid'];
        $params = array(
            'fieldname' => array(
                'module' => 'TyunWebBuyService',
                'action' => 'getClientMigration',
                'accountid' => $accountid,
                'userid' => $this->userid
            ),
            'userid' => $this->userid
        );
        $list = $this->call('getComRecordModule', $params);
        if (!empty($list[0])) {
            $mydata=array('Mobile'=>$mobile);
            $tempData['data'] = $this->encrypt(json_encode($mydata));
            $postData = http_build_query($tempData);//传参数
            $res = $this->https_request($this->getLoginName, $postData);
            $res=str_replace(array('\\r','\\n'),'',$res);
            $res=str_replace('\"','"',$res);
            $res=preg_replace('/^"|"$/','',$res);
            $ress = json_decode($res,true);
            if($ress['success']) {
                $loginNames = array();
                foreach ($ress['data'] as $value) {
                    $loginNames[] = $value['LoginName'];
                }
                $diff_array = array_intersect($list[0],$loginNames);
                if($diff_array){
                    $data = array();
                    foreach ($diff_array as $value){
                        $data[] = array(
                            'id'=>$accountid,
                            'loginName'=>$value
                        );
                    }
                    return array('success'=>1,'data'=>$data);
                }
            }
        }
        return array('success'=>0,'msg'=>'没有找到相关迁移信息');

    }

    private function handleOtherProduct($request){
        $producttitle=$request['producttitle'];
        $productid=$request['productid'];
        $categoryid=$request['categoryids'];
        $number=$request['number'];
        $id=$request['id'];
        $price=$request['price'];
        $renewprice=$request['renewprice'];
        $unit=$request['unit'];
        $specificationstitle=$request['specificationstitle'];
        $buyyear =$request['buyyear'];//合同类型购买和续费
        $ProductInfo=array();
        foreach($productid as $key=>$value){
            $tempdata=array("ID"=>"",
                "productID"=>$value,
                "productTitle"=>$producttitle[$key],
                "specification"=>array(
                    "id"=>$id[$key],
                    "title"=>$specificationstitle[$key],
                    "count"=>$number[$key],
                    "price"=>$price[$key],
                    "renewprice"=>$renewprice[$key],
                    "termUnit"=>$unit[$key]
                ),
                "categoryID"=>$categoryid[$key],
                "count"=>$number[$key],
                "buyTerm"=>$buyyear
            );
            $ProductInfo[]=$tempdata;
        }
        return $ProductInfo;
    }

    private function handleTyunResult($data,$request_params,$tyunparams,$type){
        $tempdata=$data['data'];
        if(!count($tempdata)){
            return;
        }

        $fieldname = array(
            'module'		=>'TyunWebBuyService',
            'action'        =>'NewAddbuyOrder',
            'tempdata'          =>$tempdata,
            'request_params'=>$request_params,
            'tyunparams'    =>$tyunparams,
            'userid'        =>$request_params['userid'],
            'type'     =>$type
        );
        $erpdata = array(
            'fieldname'=>$fieldname,
            'userid' => $request_params['userid']
        );

        $this->_logs(array("renewdoOrderreturndataerpdata ：",$erpdata));
        $this->call('getComRecordModule', $erpdata);
    }


    public function checkOldTyunAccount(){

        $this->setConfigURL();
        $tyunaccount = trim($_REQUEST['tyunaccount']);
        $mobile = $_REQUEST['mobile'];
        $mydata=array('Mobile'=>$mobile);
        $this->_logs($mydata);
        $tempData['data'] = $this->encrypt(json_encode($mydata));
        $postData = http_build_query($tempData);//传参数
        $res = $this->https_request($this->getLoginName, $postData);
        $res=str_replace(array('\\r','\\n'),'',$res);
        $res=str_replace('\"','"',$res);
        $res=preg_replace('/^"|"$/','',$res);
        $data = json_decode($res,true);
        $this->_logs($data);
        do{
            if($data['success']) {
                $loginNames = array();
                foreach ($data['data'] as $value){
                    $loginNames[] = $value['LoginName'];
                }
                if(!in_array($tyunaccount,$loginNames)){
                    $return_data = array('success' => 0, 'msg' => '新老客户手机号不一致');
                    continue;
                }

                $params = array(
                    'fieldname' => array(
                        'module' => 'TyunWebBuyService',
                        'action' => 'getOldClientMigration',
                        'usercode' => $tyunaccount,
                        'userid' => $this->userid,
                    ),
                    'userid' => $this->userid
                );
                $list = $this->call('getComRecordModule', $params);
                $data = $list[0];
                if (!empty($data)) {
                    $return_data = array(
                        'success'=>1,
                        'accountid'=>$data['customerid'],
                        'loginName'=>$tyunaccount,
                        'companyName'=>$data['customername'],
                        'userid'=>$this->userid
                    );
                    continue;
                }else{
                    $return_data = array('success'=>0,'msg'=>'不存在该T云账号或新老客户手机号不一致');
                    continue;
                }
            }
            $return_data = array('success'=>0,'msg'=>$data['message']);
        }while(0);
        echo json_encode($return_data);
        exit;
    }

    public function calculationTotal(){
        $this->setConfigURL();
        $servicecontractsid =$_REQUEST['servicecontractsid'];//服务合同id
        $servicecontractsid_display =$_REQUEST['servicecontractsid_display'];//服务合同编号
        $accountid =$_REQUEST['accountid'];//客户id
        $accountid_display =$_REQUEST['accountid_display'];//客户名称
        $mobile =$_REQUEST['mobile'];//手机号
        $mobilevcode =$_REQUEST['mobilevcode'];//验证码
        $buyyear =$_REQUEST['buyyear'];//年限
        $classtype =$_REQUEST['classtype'];//合同类型购买和续费
        $clientPackageID=$_REQUEST['clientPackageID'];// 产品id
        $categoryid=$_REQUEST['categoryid'];//产品分ID
        $servicetotal=$_REQUEST['servicetotal'];
        $tyunusercode=$_REQUEST['tyunusercode'];
        $tyunusercodename=$_REQUEST['tyunusercodetext'];
        $agents =$_REQUEST['agents'];
        $expiredate=$_REQUEST['expiredate'];
        $returnmsg=array('success'=>0);
        $customerstype=$_REQUEST['customerstype'];
        $oldproductid=$_REQUEST['oldproductid'];
        $orderordercode=$_REQUEST['activacode'];
        $oldproductname=$_REQUEST['oldproductname'];

        $oldcustomerid = $_REQUEST['oldcustomerid'];
        $oldcustomername =$_REQUEST['oldcustomername'];
        $buydate=empty($_POST['buydate'])?date('Y-m-d H:i:s'):date('Y-m-d',strtotime($_POST['buydate'])).date(' H:i:s');
        // 判断是降级还是续费
        if(isset($_REQUEST['is_degrade']) && $_REQUEST['is_degrade']==1){
            $productType=6;
            $type = 'degrade';
            $type_name = '降级';
        }else{
            $productType=7;
            $type = 'renew';
            $type_name = '续费';
        }
        do{
            $checkdata=$this->checkBasicInfo();
            if($checkdata['flag']){
                $returnmsg['msg']=$checkdata['msg'];
                break;
            }
            $par = array("loginName"=>$tyunusercodename,'nickName'=>$accountid_display,'phoneNumber'=>$mobile,'invitationCode'=>$agents,'cid'=>$accountid,'status'=>1);
            //用户接口
            $userinfo=$this->batchImportAccountList($par);
            $userinfo = json_decode($userinfo,true);
            if($userinfo['code']==511 || $userinfo['code']==200){
                $tyunusercode=$userinfo['data']['id'];
            }else{
                $returnmsg['msg']=$userinfo['$userinfo'];
                break;
            }
            $ProductInfo = $this->handleOtherProduct($_REQUEST);

            $tyunparams=array(
                "type"=>1,//0线上1线下
                "productType"=>$productType,//商品类型(5升级 6降级 7 续费购买)
                "contractCode"=>$servicecontractsid_display,//合同编号
                "userID"=>$tyunusercode,//用户编号
                "agentIdentity"=>$agents,//代理商ID
                "discount"=>1,//折扣
                "categoryID"=>$categoryid,//产品分类(0国内版 1一带一路)
                "buyTerm"=>$buyyear,//购买年限
                "clientPackageID"=>$clientPackageID,//套餐编号'fb016797-4296-11e6-ad98-00155d069461'
                "contractMoney"=>$servicetotal,
                "addDate"=>$buydate,
                "crmOrderFlag"=>1,//ERP提交订单
                'oldCloseDate'=>$expiredate,
                "productInfo"=>$ProductInfo
            );
            $this->_logs(array("CalculationMoney：", json_encode($tyunparams)));
            $postData=json_encode($tyunparams);
            $time=time().'123';
            $sault=$time.$this->sault;
            $token=md5($sault);
            $curlset=array(CURLOPT_HTTPHEADER=>array(
                "Content-Type:application/json",
                "S-Request-Token:".$token,
                "S-Request-Time:".$time),
                CURLINFO_HEADER_OUT=>array(true));
            $res = $this->https_request($this->CalculationMoney, $postData,$curlset);
            $this->_logs(array("CalculationMoney ：",$res));
            $data=json_decode($res,true);
            if($data['code']==200){
                $returnmsg['data'] = $data;
                $returnmsg['msg']='查询成功';
                $returnmsg['success']=1;
            }else{
                $returnmsg['msg']=$data['message'];
                break;
            }
        }while(0);
        echo json_encode($returnmsg);
        exit;
    }


    public function preRenewDoOrder(){
        $this->setConfigURL();
        $servicecontractsid = $_REQUEST['servicecontractsid'];//服务合同id
        $servicecontractsid_display = $_REQUEST['servicecontractsid_display'];//服务合同编号
        $accountid = $_REQUEST['accountid'];//客户id
        $accountid_display = $_REQUEST['accountid_display'];//客户名称
        $mobile = $_REQUEST['mobile'];//手机号
        $mobilevcode = $_REQUEST['mobilevcode'];//验证码
        $buyyear = $_REQUEST['buyyear'];//年限
        $classtype = $_REQUEST['classtype'];//合同类型购买和续费
        $clientPackageID = $_REQUEST['clientPackageID'];// 产品id
        $categoryid = $_REQUEST['categoryid'];//产品分ID
        $oldproductname = $_REQUEST['oldproductname'];
        $servicetotal = $_REQUEST['servicetotal'];
        $tyunusercode = $_REQUEST['tyunusercode'];
        $tyunusercodename = $_REQUEST['tyunusercodetext'];
        $agents = $_REQUEST['agents'];
        $expiredate = $_REQUEST['expiredate'];
        $returnmsg = array('success' => 0);
        $customerstype = $_REQUEST['customerstype'];
        $oldproductid = $_REQUEST['oldproductid'];
        $orderordercode = $_REQUEST['activacode'];
        $oldcustomerid = $_REQUEST['oldcustomerid'];
        $oldcustomername = $_REQUEST['oldcustomername'];
        $chooseUserProducts = $_REQUEST['chooseuserproduct'];
        $buydate = empty($_POST['buydate']) ? date('Y-m-d H:i:s') : date('Y-m-d', strtotime($_POST['buydate'])) . date(' H:i:s');
        // 判断是降级还是续费
        if (isset($_REQUEST['is_degrade']) && $_REQUEST['is_degrade'] == 1) {
            $productType = 6;
            $type = 'degrade';
            $type_name = '降级';
        } else {
            $productType = 7;
            $type = 'renew';
            $type_name = '续费';
        }
        do {
            $checkdata = $this->checkBasicInfo();
            if ($checkdata['flag']) {
                $returnmsg['msg'] = $checkdata['msg'];
                break;
            }
            $par = array("loginName" => $tyunusercodename, 'nickName' => $accountid_display, 'phoneNumber' => $mobile, 'invitationCode' => $agents, 'cid' => $accountid, 'status' => 1);
            //用户接口
            $userinfo = $this->batchImportAccountList($par);
            /*echo $userinfo;die();*/
            $userinfo = json_decode($userinfo, true);
            if ($userinfo['code'] == 511 || $userinfo['code'] == 200) {
                $tyunusercode = $userinfo['data']['id'];
            } else {
                $returnmsg['msg'] = $userinfo['$userinfo'];
                break;
            }
            $ProductInfo = $this->handleOtherProduct($_REQUEST);

            $tyunparams = array(
                "type" => 1,//0线上1线下
                "productType" => $productType,//商品类型(5升级 6降级 7 续费购买)
                "contractCode" => $servicecontractsid_display,//合同编号
                "userID" => $tyunusercode,//用户编号
                "agentIdentity" => $agents,//代理商ID
                "discount" => 1,//折扣
                "categoryID" => $categoryid,//产品分类(0国内版 1一带一路)
                "buyTerm" => $buyyear,//购买年限
                "clientPackageID" => $clientPackageID,//套餐编号'fb016797-4296-11e6-ad98-00155d069461'
                "contractMoney" => $servicetotal,
                "addDate" => $buydate,
                "crmOrderFlag" => 1,//ERP提交订单
                'oldCloseDate' => $expiredate,
                "productInfo" => $ProductInfo,
                "chooseUserProducts" => $chooseUserProducts,//可套ID
                'electricContract'=>1 //电子合同下单
            );
            $this->_logs(array("renewdoOrder：", json_encode($tyunparams)));
            $postData = json_encode($tyunparams);
            $time = time() . '123';
            $sault = $time . $this->sault;
            $token = md5($sault);
            $curlset = array(CURLOPT_HTTPHEADER => array(
                "Content-Type:application/json",
                "S-Request-Token:" . $token,
                "S-Request-Time:" . $time),
                CURLINFO_HEADER_OUT => array(true));
            $res = $this->https_request($this->renewDoOrder, $postData, $curlset);
            $this->_logs(array("renewdoOrderreturndata ：", $res));
            $data = json_decode($res, true);
            if ($data['code'] == 200) {
                $returnmsg = $this->elecontractPreview($_REQUEST,$data);
                $this->_logs(array('renewdoOrderreturndatacode',$data['code'],$returnmsg));
                $returnmsg['data']['paycode'] = $data['data']['payCode'];
            }
        }while(0);
        echo json_encode($returnmsg);
        exit;
    }

    public function preUpgardeDoOrder(){
        $this->setConfigURL();
        $servicecontractsid =$_REQUEST['servicecontractsid'];//服务合同id
        $servicecontractsid_display =$_REQUEST['servicecontractsid_display'];//服务合同编号
        $accountid =$_REQUEST['accountid'];//客户id
        $accountid_display =$_REQUEST['accountid_display'];//客户名称
        $mobile =$_REQUEST['mobile'];//手机号
        $mobilevcode =$_REQUEST['mobilevcode'];//验证码
        $buyyear =$_REQUEST['buyyear'];//年限
        $classtype =$_REQUEST['classtype'];//合同类型购买和续费
        $servicetotal=$_REQUEST['servicetotal'];
        $tyunusercode=$_REQUEST['tyunusercode'];
        $tyunusercodetext = $_REQUEST['tyunusercodetext'];
        $categoryid=$_REQUEST['categoryid'];
        $clientPackageID=$_REQUEST['buyproduct'];
        $unusedamount=$_REQUEST['unusedamount'];
        $upgradecost=$_REQUEST['upgradecost'];
        $oldCloseDate=$_REQUEST['oldexpiredate_display'];
        $orderordercode=$_REQUEST['activacode'];
        $oldproductname=$_REQUEST['oldproductname'];
        $oldSurplusMoney=$_REQUEST['oldSurplusMoney'];
        $oldproductid=$_REQUEST['oldproductid'];
        $customerstype='clientmigration';
        $agents =$_REQUEST['agents'];
        $chooseUserProducts = $_REQUEST['chooseuserproduct'];
        $buydate=empty($_POST['buydate'])?date('Y-m-d H:i:s'):date('Y-m-d',strtotime($_POST['buydate'])).date(' H:i:s');
        $returnmsg=array('success'=>0);
        do{
            $checkdata=$this->checkBasicInfo();
            if($checkdata['flag']){
                $returnmsg['msg']=$checkdata['msg'];
                break;
            }
            $params=array("loginName"=>$tyunusercodetext,
                'phoneNumber'=>$mobile,
                'invitationCode'=>$agents,//代理商ID
                'nickName'=>$accountid_display,
                'cid'=>$accountid,
                'status'=>1);
            $getUserCode=$this->batchImportAccountList($params);
            $userCodeJsonData=json_decode($getUserCode,true);
            $tyunusercodeid=$userCodeJsonData['data']['id'];
            if(empty($tyunusercodeid)){
                $returnmsg['msg']='账户创建失败';
                break;
            }

            $ProductInfo= $this->handleOtherProduct($_REQUEST);
            $tyunparams=array(
                "type"=>1,//0线上1线下
                "productType"=>5,//商品类型(5迁移升级 6迁移降级 7迁移续费)
                "contractCode"=>$servicecontractsid_display,//合同编号
                "userID"=>$tyunusercodeid,//用户编号
                "agentIdentity"=>0,//代理商ID
                "discount"=>1,//折扣
                "categoryID"=>$categoryid,//产品分类(0国内版 1一带一路)
                "buyTerm"=>$buyyear,//购买年限
                "clientPackageID"=>$clientPackageID,//套餐编号
                "contractMoney"=>$servicetotal,
                "oldCloseDate"=>$oldCloseDate,
                "surplusMoney"=>$unusedamount,
                "oldSurplusMoney"=>$oldSurplusMoney,
                "crmOrderFlag"=>1,//ERP提交订单
                "productInfo"=>$ProductInfo,
                "chooseUserProducts"=>$chooseUserProducts,//可套ID
                "addDate"=>$buydate,
                'electricContract'=>1
            );
            $this->_logs(array("upgardedoOrder：", json_encode($tyunparams)));
            $postData=json_encode($tyunparams);
            $time=time().'123';
            $sault=$time.$this->sault;
            $token=md5($sault);
            $curlset=array(CURLOPT_HTTPHEADER=>array(
                "Content-Type:application/json",
                "S-Request-Token:".$token,
                "S-Request-Time:".$time));
            $res = $this->https_request($this->renewDoOrder, $postData,$curlset);
            $this->_logs(array($this->renewDoOrder."upgardedoOrderreturndata ：",$res));
            $data=json_decode($res,true);
            if($data['code']==200){
                $returnmsg = $this->elecontractPreview($_REQUEST,$data);
                $returnmsg['data']['paycode'] = $data['data']['payCode'];
            }else{
                $returnmsg['msg']=$data['message'];
                break;
            }
        }while(0);
        echo json_encode($returnmsg);
        exit;
    }

    /**
     * 电子合同下单
     *
     * 1、ERP生成电子合同
     * 2、T云订单绑定合同编号
     * 3、存储相关订单信息
     */
    public function elecContractAddOrder(){
        $this->setConfigURL();
        $accountid = $_REQUEST['accountid'];//客户id
        $accountid_display = $_REQUEST['accountid_display'];//客户名称
        $mobile = $_REQUEST['mobile'];//手机号
        $mobilevcode = $_REQUEST['mobilevcode'];//验证码
        $buyyear = $_REQUEST['buyyear'];//年限
        $classtype = $_REQUEST['classtype'];//合同类型购买和续费
        $clientPackageID = $_REQUEST['clientPackageID'];// 产品id
        $categoryid = $_REQUEST['categoryid'];//产品分ID
        $servicetotal = $_REQUEST['servicetotal'];
        $tyunusercodename = $_REQUEST['tyunusercodetext'];
        $agents = $_REQUEST['agents'];
        $expiredate = $_REQUEST['expiredate'];
        $returnmsg = array('success' => 0);
        $customerstype = $_REQUEST['customerstype']?$_REQUEST['customerstype']:'clientmigration';
        $oldcustomerid = $_REQUEST['oldcustomerid'];
        $oldcustomername = $_REQUEST['oldcustomername'];
        $chooseUserProducts = $_REQUEST['chooseuserproduct'];
        $buydate = empty($_POST['buydate']) ? date('Y-m-d H:i:s') : date('Y-m-d', strtotime($_POST['buydate'])) . date(' H:i:s');
        $paycode = $_REQUEST['paycode'];
        $activitymodel = $_REQUEST['activitymodel'];
        $eleccontractid = $_REQUEST['contractid'];
        $paycode = $_REQUEST['paycode'];
        $invoicecompany = $_REQUEST['invoicecompany'];
        $invoicecompanyid = $_REQUEST['invoicecompanyid'];
        $authtype = $_REQUEST['authtype'];
        $elereceivermobile = $_REQUEST['elereceivermobile'];
        $elereceiver = $_REQUEST['elereceiver'];
        $signaturetype = $_REQUEST['signaturetype'];
        $totalmarketprice = $_REQUEST['totalmarketprice'];
        $tyunusercode=$_REQUEST['tyunusercode'];
        $unusedamount=$_REQUEST['unusedamount'];
        $upgradecost=$_REQUEST['upgradecost'];
        $oldCloseDate=$_REQUEST['oldexpiredate_display'];
        $orderordercode=$_REQUEST['activacode'];
        $oldproductname=$_REQUEST['oldproductname'];
        $oldSurplusMoney=$_REQUEST['oldSurplusMoney'];
        $oldproductid=$_REQUEST['oldproductid'];
        $checkdata=$this->checkBasicInfo();
        if($checkdata['flag']){
            $returnmsg = array(
                'success'=>0,
                'msg'=>$checkdata['msg']
            );
            echo json_encode($returnmsg);
            exit();
        }
        //创建电子合同
        list($servicecontractsid,$contractCode) = $this->elecContractOrder($_REQUEST);
        if(!$servicecontractsid || !$contractCode){
            echo json_encode(array('success'=>0,'msg'=>'创建电子合同失败'));
            exit();
        }

        //绑定合同编号
        $postData=json_encode(array(
                'userID'=>intval($tyunusercode),
                'payCode'=>$paycode,
                'contractCode'=>$contractCode,
            )
        );
        $time=time().'123';
        $sault=$time.$this->sault;
        $token=md5($sault);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));
        $this->_logs(array("BindContractCode request data:",$postData));
        $res = $this->https_request($this->BindContractCode, $postData,$curlset);
        $data = json_decode($res,true);
        if($data['code']==200){
            $r_params = array(
                'servicecontractsid'=>$servicecontractsid,
                'servicecontractsid_display'=>$contractCode,
                'accountid'=>$accountid,
                'accountid_display'=>$accountid_display,
                'mobile'=>$mobile,
                'mobilevcode'=>$mobilevcode,
                'classtype'=>$classtype,
                'module'		=>'TyunWebBuyService',
                'action'		=> 'AddbuyOrder',
                'userid'		=> $this->userid,
                'res'           =>$res,
                'customer_name'=>$_SESSION['customer_name'],
                'phone_mobile'=>$_SESSION['phone_mobile'],
                'tyunurl'=>$this->renewDoOrder,
                'contractprice'=>$servicetotal,
                'usercodeid'=>$tyunusercode,
                'usercode'=>$tyunusercodename,
                "agentIdentity"=>$agents,//代理商ID
                'customerstype'=>$customerstype,
                'oldproductid'=>$oldproductid,
                'oldproductname'=>$oldproductname,
                'orderordercode'=>$orderordercode,
                'oldcustomerid'=>$oldcustomerid,
                'oldcustomername'=>$oldcustomername,
                'signaturetype'=>$signaturetype,
                'elereceivermobile'=>$elereceivermobile,
                'owncompany'=>$invoicecompany,
                'owncompanyid'=>$invoicecompanyid,
                'surplusmoney'=>$unusedamount,
                'upgradecost'=>$upgradecost,
            );
            $data['payCode'] = $paycode;
            $data['totalPrice'] = $servicetotal;
            switch ($activitymodel){
                case 5:
                    $handleType = 'upgrade';
                    $url='/index.php?module=TyunWebBuyServiceClient&action=upgrade';
                    $msg = '升级成功';
                    break;
                case 6:
                    $handleType = 'degrade';
                    $url='/index.php?module=TyunWebBuyServiceClient&action=degrade';
                    $msg = '降级成功';
                    break;
                case 7:
                    $handleType = 'renew';
                    $url='/index.php?module=TyunWebBuyServiceClient&action=renew';
                    $msg = '续费成功';
                    break;
            }
            $this->handleTyunResult(array('data'=>$data),$r_params,array(),$handleType);

            $returnmsg=array('success'=>1,'msg'=>$msg,'url'=>$url);

            $sendEmailParams = array(
                'contract_no'=>$contractCode,
                'userid'=>$this->userid,
                'customer_name'=>$accountid_display,
                'receivedate'=>date('Y-m-d H:i:s'),
                'comeformtyun'=>1,
                'fromactivity'=>0,
                'servicecontractstype'=>$classtype,
                'elereceiver'=>$elereceiver,
                'elereceivermobile'=>$elereceivermobile,
                'eleccontractid'=>$eleccontractid,
                'servicecontractsid'=>$servicecontractsid,
            );

            //是否创建工作流
            $workflowresult = $this->createWorkflows($servicecontractsid,$totalmarketprice,$servicetotal);
            if(!$workflowresult){
                //非中小或者中小但不用审核 直接发送电子合同
                $sendresult= $this->sendElecContract($sendEmailParams);
                if(!$sendresult['success']){
                    $returnmsg=array('success'=>1,'msg'=>'电子合同发送失败,请在ERP中手动发送电子合同','url'=>$url);
                }
            }
            echo json_encode($returnmsg);
            exit();
        }
        $returnmsg=array('success'=>0,'msg'=>'绑定合同失败,请联系管理员');
        echo json_encode($returnmsg);

    }

    public function lowRateTip(){
        global $zhongxiaodepartment;
        $params = array(
            'fieldname' => array(
                'module' => 'Users',
                'action' => 'isZhongxiaoByUserId',
                "id"=>$zhongxiaodepartment,
                'userid'=>$this->userid
            ),
            'userid'=>$this->userid
        );
        $list = $this->call('getComRecordModule', $params);
        return $list[0];
    }


    public function createWorkflows($servicecontractsid,$totalmarketprice,$servicetotal){
        global $eleContractWorkflowsid;
        $isZhongXiao = $this->lowRateTip();
        if($isZhongXiao){
            $rate = $servicetotal/$totalmarketprice;
            if($rate<=0.95 && $rate>0.92){
                $verifyLevel=1;
            }elseif ($rate>0.90 && $rate<=0.92){
                $verifyLevel=2;
            }elseif($rate<=0.90){
                $verifyLevel=3;
            }
            if($rate<=0.95){
                $params = array(
                    'fieldname' => array(
                        'module' => 'ServiceContracts',
                        'action' => 'elecContractVerify',
                        "eleContractWorkflowsid"=>$eleContractWorkflowsid,
                        "servicecontractsid"=>$servicecontractsid,
                        'userdepartmentid'=>"",
                        "verifyLevel"=>$verifyLevel,
                        'userid'=>$this->userid
                    ),
                    'userid'=>$this->userid
                );
                $list = $this->call('getComRecordModule', $params);
                return true;
            }
        }
        return false;
    }

    public function getViewInfo($contractId){
        //获取放心前token
        $token=$this->getFangXinQianToken();
        if(!$token){
            $returnmsg=array('success'=>0,'msg'=>'获取放心签token异常,请重试');
            echo json_encode($returnmsg);
            exit();
        }
        global $fangxinqianview;
        $postData = array(
            'contractId'=>$contractId
        );
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "host:fxq-order-test.oss-cn-hangzhou.aliyuncs.com",
            "token:".$token));
        $res = $this->https_request($fangxinqianview."?".http_build_query($postData), '',$curlset);
        return json_decode($res,true);
    }

    /**
     * 处理电子合同下单
     */
    public function elecContractOrder($resparams){
        $viewInfo = $this->getViewInfo($resparams['contractid']);
        $eleccontracttpl = $viewInfo['data']['contractTitle'];
        $enclosureLists = $viewInfo['data']['enclosureList'];
        $relatedattachment = '';
        $relatedattachmentid = '';
        foreach ($enclosureLists as $enclosureList){
            $relatedattachmentid .= $enclosureList['id'].',';
            $relatedattachment .= $enclosureList['name'].($enclosureList['enclosureVersion']?'('.$enclosureList['enclosureVersion'].')':'').',';
        }

        $packageid=$_REQUEST['productclasstwovalues'];//套餐ID
        $packageid=!empty($packageid)?$packageid:0;

        switch ($resparams['classtype']){
            case 'buy':
                $servicecontractstype='新增';
                break;
            case 'renew':
                $servicecontractstype='续费';
                break;
            case 'upgrade':
            case 'degrade':
                $servicecontractstype = $resparams['classtype'];
                $packageid = $resparams['buyproduct'];
                break;
        }

        //1 生成电子合同
        $params = array(
            'fieldname' => array(
                'module' => 'ServiceContracts',
                'action' => 'createServiceContracts',
                'userid' => $this->userid,
                'productid'=>$packageid,
                'invoicecompany'=>$resparams['invoicecompany'], //所属公司
                'contractbuytype'=>$resparams['classtype'],//购买类型
                'servicecontractstype'=>$servicecontractstype,//购买类型
                'classtype'=>$resparams['classtype'],     //购买类型
                'contract_template'=>'TYUN',
                'paycode'=>$resparams['paycode'],
                'productlife'=>$resparams['buyyear'],
                'accountid'=>$resparams['accountid'],//客户id
                'contractattribute'=>'standard',//合同属性custommade定制,standard标准
                'clientproperty'=>$resparams['authenticationtype']==0?'personal':'enterprise',//客户属性personal个人,enterprise企业
                'totalprice'=>$resparams['servicetotal'],

                'signaturetype'=>'eleccontract',//签署类型papercontract纸质合同,eleccontract电子合同
                'elereceivermobile'=>$resparams['elereceivermobile'],//接收人手机号
                'elereceiver'=>$resparams['elereceiver'],//接收人
                'eleccontractid'=>$resparams['contractid'],//放心签平台生成的合同id
                'comeformtyun'=>1,
                'fromactivity'=>($resparams['activityid']?1:0),
                "modulestatus"=>($resparams['isverify']?'b_check':'已发放'),
                "eleccontractstatus"=>($resparams['isverify']?'a_elec_sending':'b_elec_actioning'),
                "activityenddate"=>$resparams['activityenddate'], //活动到期时间
                "parent_contracttypeid"=>10,

                'eleccontracttplid'=>$resparams['templateid'], //放心签平台合同模板id
                "eleccontracttpl"=>$eleccontracttpl,//合同模板名称
                "relatedattachmentid"=>rtrim($relatedattachmentid,','),//关联附件ID列表
                "relatedattachment"=>rtrim($relatedattachment,','),//关联附件
                "originator"=>$_SESSION['customer_name'],
                "originatormobile"=>$_SESSION['phone_mobile'],
                "ispackage"=>$_REQUEST['ispackage'],
                "otherproductids"=>$_REQUEST['productid']
            ),
            'userid' => $this->userid
        );
        $this->_logs(array("createServiceContracts",$params));
        $list = $this->call('getComRecordModule', $params);
        $this->_logs(array("createServiceContractsResult",$list));
        if(!$list[0]){
            return array();
        }
        return array($list[0]['id'],$list[0]['contracts_no']);
    }


    /**
     * 合同pdf页面
     */
    public function pdf(){
        $contractId = $_REQUEST['contractid'];
        $this->smarty->assign("accountid",$contractId);
        $this->smarty->display('TyunWebBuyService/pdf.html');
    }

    /**
     * 获取放心签token
     */
    public function getFangXinQianToken(){
        $cache_token=@file_get_contents('./fangxinqiantoken.txt');
        $tokens = json_decode($cache_token,true);
        if($tokens['success'] &&$tokens['timeout']>time()){
            return $tokens['data'];
        }
        global $fangxinqianAppKey,$fangxinqianAppSecrect,$fangxinqiangettokenurl;
        $postData = array(
            "appKey"=>$fangxinqianAppKey,
            "appSecret"=>$fangxinqianAppSecrect
        );
        $curlset=array(
            CURLOPT_HTTPHEADER=>array(
                "Content-Type:application/json"
            )
        );
        $result = $this->https_request($fangxinqiangettokenurl."?".http_build_query($postData),"",$curlset);
        $res = json_decode($result,true);
        if(!$res['success']){
            return false;
        }
        $res['timeout'] = time()+55*60;
        file_put_contents('./fangxinqiantoken.txt', json_encode($res));
        return $res['data'];
    }

    /**
     *获取formId
     */
    public function getFangXinQianFormId(){
        global $fangxinqiangetformidurl;
        $token = $this->getFangXinQianToken();
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "token:".$token)
        );
        $result = $this->https_request($fangxinqiangetformidurl, "",$curlset);
        $res = json_decode($result,true);
        if($res['success']){
            return $res['data'];
        }
        return '';
    }

    /**
     * 发起签署合同验证
     */
    public function elecContractSignCheck(){
        $totalmarketprice = $_REQUEST['totalmarketprice'];
        $servicetotal = $_REQUEST['servicetotal'];
        global $zhongxiaominrate;
        $isZhongXiao = $this->lowRateTip();
        $isShowTip = false;
        if($isZhongXiao){
            $rate = bcdiv(strval($servicetotal),strval($totalmarketprice),2);
            if($rate<=$zhongxiaominrate){
                $isShowTip =true;
            }
        }

        $returnmsg=array('success'=>1,'msg'=>'','data'=>$isShowTip);
        echo json_encode($returnmsg);
    }

    function toChinaMoney($num){
        $c1 = "零壹贰叁肆伍陆柒捌玖";
        $c2 = "分角元拾佰仟万拾佰仟亿";
        //精确到分后面就不要了，所以只留两个小数位
        $num = round($num, 2);
        //将数字转化为整数
        $num = $num * 100;
        if (strlen($num) > 10) {
            return "金额太大，请检查";
        }
        $i = 0;
        $c = "";
        while (1) {
            if ($i == 0) {
                //获取最后一位数字
                $n = substr($num, strlen($num)-1, 1);
            } else {
                $n = $num % 10;
            }
            //每次将最后一位数字转化为中文
            $p1 = substr($c1, 3 * $n, 3);
            $p2 = substr($c2, 3 * $i, 3);
            if ($n != '0' || ($n == '0' && ($p2 == '亿' || $p2 == '万' || $p2 == '元'))) {
                $c = $p1 . $p2 . $c;
            } else {
                $c = $p1 . $c;
            }
            $i = $i + 1;
            //去掉数字最后一位了
            $num = $num / 10;
            $num = (int)$num;
            //结束循环
            if ($num == 0) {
                break;
            }
        }
        $j = 0;
        $slen = strlen($c);
        while ($j < $slen) {
            //utf8一个汉字相当3个字符
            $m = substr($c, $j, 6);
            //处理数字中很多0的情况,每次循环去掉一个汉字“零”
            if ($m == '零元' || $m == '零万' || $m == '零亿' || $m == '零零') {
                $left = substr($c, 0, $j);
                $right = substr($c, $j + 3);
                $c = $left . $right;
                $j = $j-3;
                $slen = $slen-3;
            }
            $j = $j + 3;
        }
        //这个是为了去掉类似23.0中最后一个“零”字
        if (substr($c, strlen($c)-3, 3) == '零') {
            $c = substr($c, 0, strlen($c)-3);
        }
        //将处理的汉字加上“整”
        if (empty($c)) {
            return "零元整";
        }else{
            return $c . "整";
        }
    }

    public function makeElecContractData($data){
        $orders = $data['data']['data'];
        $totalPrice = $orders[0]['order']['SubtotalContractMoney'];
        $otherDataYear = array();
        $otherDataNoYear = array();
        $otherIncrementDataTotalPrice = 0;
        foreach ($orders as $order){
            //判断是套餐还是另购单品
            if($order['detailProducts'][0]['ProductTypeTitle']=='套餐'){
                $packageterm =$order['detailProducts'][0]['BuyTerm'];
                $packagetotalprice = $order['detailProducts'][0]['PayMoney'];
                continue;
            }

            if($order['detailProducts'][0]['ProductTypeTitle']=='单品') {
                //另购单品
                $canRenew = $order['detail']['OrderDetail']['products'][0]['Product']['CanRenew'];
                if ($canRenew) {
                    $otherDataYear[] = array(
                        $order['detail']['ProductTitle'],
                        '年限类',
                        $order['detailProducts'][0]['Count'],
                        $order['detailProducts'][0]['BuyTerm'],
                        $order['detailProducts'][0]['PayMoney'],
                    );
                } else {
                    $otherDataNoYear[] = array(
                        $order['detail']['ProductTitle'],
                        '非年限类',
                        $order['detailProducts'][0]['Count'],
                        $order['detailProducts'][0]['PayMoney']
                    );
                }
            }
            if($order['detailProducts'][0]['ProductTypeTitle']=='增值服务') {
                $canRenew2 = $order['detail']['OrderDetail']['products'][0]['Product']['CanRenew'];
                if ($canRenew2) {
                    $otherIncrementDataYear[] = array(
                        $order['detail']['ProductTitle'],
                        '年限类',
                        $order['detailProducts'][0]['Count'],
                        $order['detailProducts'][0]['BuyTerm'],
                        $order['detailProducts'][0]['PayMoney'],
                    );
                } else {
                    $otherIncrementDataNoYear[] = array(
                        $order['detail']['ProductTitle'],
                        '非年限类',
                        $order['detailProducts'][0]['Count'],
                        $order['detailProducts'][0]['PayMoney']
                    );
                }
                $otherIncrementDataTotalPrice += $order['detailProducts'][0]['PayMoney'];
            }
        }

        $totalpricetochina = $this->toChinaMoney($totalPrice);

        $dynamicRows = array(
            (!empty($otherDataYear) ? array(
                "tableIndex"=>0,
                "rowIndex"=>9,//当前表格第几行后面加内容 0.表示表格第一行
                "fontSize"=>10, //字体大小
                "color"=>"000000",
                "rows"=>$otherDataYear
            ):''),
            (!empty($otherDataNoYear) ? array(
                "tableIndex"=>1,
                "rowIndex"=>9,//当前表格第几行后面加内容 0.表示表格第一行
                "fontSize"=>10, //字体大小
                "color"=>"000000",
                "rows"=>$otherDataNoYear
            ):''),
            (!empty($otherIncrementDataYear) ? array(
                "tableIndex"=>2,
                "rowIndex"=>9,//当前表格第几行后面加内容 0.表示表格第一行
                "fontSize"=>10, //字体大小
                "color"=>"000000",
                "rows"=>$otherIncrementDataYear
            ):''),
            (!empty($otherIncrementDataNoYear) ? array(
                "tableIndex"=>3,
                "rowIndex"=>9,//当前表格第几行后面加内容 0.表示表格第一行
                "fontSize"=>10, //字体大小
                "color"=>"000000",
                "rows"=>$otherIncrementDataNoYear
            ):''),
        );
        return array(
            'dynamicrows'=>array_filter($dynamicRows),
            "packageterm"=>$packageterm,  //套餐年限
            'packagetotalprice'=>round($packagetotalprice,2), //套餐总价
            'totalprice'=>round($totalPrice,2), //合同金额合计
            "totalpricetochina"=>$totalpricetochina,    //合同金额大写
            'otherproducttotalprice'=>round(($totalPrice-$packagetotalprice-$otherIncrementDataTotalPrice),2), //另购产品合计
            'otherincrementtotalprice'=>round($otherIncrementDataTotalPrice,2) //增值产品合计
        );
    }

    public function getAccountInfo($accountid){
        $params = array(
            'fieldname' => array(
                'module' => 'Accounts',
                'action' => 'getAccountInfo',
                'accountid'=>$accountid,
                'userid'=>$this->userid
            ),
            'userid' => $this->userid
        );
        $list = $this->call('getComRecordModule', $params);
        if($list[0]){
            return $list[0];
        }
        return array();
    }

    /**
     * 获取合同主体列表
     */
    public function getMainPart(){
        $params = array(
            'fieldname' => array(
                'module' => 'ServiceContracts',
                'action' => 'allCompany',
                'userid'=>$this->userid
            ),
            'userid' => $this->userid
        );
        $list = $this->call('getComRecordModule', $params);

        $returnmsg=array('success'=>0,'msg'=>'获取失败','data'=>'');
        if($list[0]){
            $returnmsg = array(
                'success'=>1,
                'msg'=>'获取成功',
                'data'=>$list[0]
            );
            echo json_encode($returnmsg);
            exit;
        }
        echo json_encode($returnmsg);

    }

    /**
     *
     */
    public function getMainPartInfo($companyid){
        $params = array(
            'fieldname' => array(
                'module' => 'ServiceContracts',
                'action' => 'companyInfo',
                'companyid'=>$companyid,
                'userid'=>$this->userid
            ),
            'userid' => $this->userid
        );
        $list = $this->call('getComRecordModule', $params);
        if($list[0]){
            return $list[0];
        }
        return array();
    }


    /**
     * 电子合同 生成预览
     */
    public function elecontractPreview($requestData,$data){
        global $fangxinqian_new_contract;
        $token=$this->getFangXinQianToken();
        if(!$token){
            $returnmsg=array('success'=>0,'msg'=>'获取放心签token异常,请重试');
            return $returnmsg;
        }
        $companyInfo = $this->getMainPartInfo($requestData['companyid']);
        if(empty($companyInfo) || !count($companyInfo)){
            $returnmsg=array('success'=>0,'msg'=>'获取主体公司信息失败,请重试');
            return $returnmsg;
        }

        $accountInfo = $this->getAccountInfo($requestData['accountid']);
        $makeData = $this->makeElecContractData($data);
        $unit_price = round($makeData['totalprice']/$makeData['packageterm'],2);
        $postData = array(
            //是否需要审核  0.不需要 1.需要
            "needAudit"=>0,
            //合同发起人信息(商务人员)
            "sender"=>array(
                "name"=>$_SESSION['customer_name'],
                "phone"=>$_SESSION['phone_mobile']
            ),
            //接收方信息
            "receiver"=>array(
                "name"=>$requestData['elereceiver'],
                "phone"=>$requestData['elereceivermobile'],
                "type"=>$requestData['authenticationtype']  //0.企业 1.个人
            ),
            "companyCode"=>$companyInfo['company_code'], //商务所属分公司编号
            "templateId"=>2, //合同模板id
//            "templateId"=>intval($requestData['templateid']), //合同模板id
            "expirationTime"=>"2020-12-12", //合同过期时间
            //关键字替换的字段
            "replaces"=>array(
                "address"=>$companyInfo['address'],
                "company"=>$companyInfo['companyfullname'],
                "name"=>$_SESSION['customer_name'],
                "bank"=>$companyInfo['bank_account'],
                "banknumber"=>$companyInfo['numbered_accounts'],
                "phone"=>$companyInfo['telphone'],
                "fax"=>$companyInfo['tax'],
                "email"=>$companyInfo['email'],

                "first_address"=>implode('',explode('#',$accountInfo['address'])),
                "first_company"=>$accountInfo['accountname'],
                "first_name"=>$requestData['elereceiver'],
                "first_bank"=>$accountInfo['bank_account'],
                "first_banknumber"=>$accountInfo['numbered_accounts'],
                "first_phone"=>$requestData['elereceivermobile'],
                "first_fax"=>$accountInfo['fax'],
                "first_email"=>$accountInfo['email1'],

                'old_product'=>$requestData['oldproductname'],  //原版本,
                'product'=>$requestData['packagename'],  //套餐名
                'old_contract_num'=>$requestData['oldcontractcode_display'],//原合同编号
                "year"=>$makeData['packageterm'],  //套餐年限
                'package_total_price'=>round($makeData['packagetotalprice'],2), //套餐总价

                'unit_price'=>$unit_price,
                'total_price'=>round($makeData['totalprice'],2), //合同总价
                "china_total_price"=>$makeData['totalpricetochina'], //合同总价大写
                'other_total price'=>round($makeData['otherproducttotalprice'],2),//另购产品总价
                'increment_total_price'=>round($makeData['otherincrementtotalprice'],2),//增值产品总价

            ),
            "dynamicRows"=>$makeData['dynamicrows']
        );

        $formId = $this->getFangXinQianFormId();
        $this->_logs(array("fangxinqian_new_contract：", $postData));
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "formId:".$formId,
            "token:".$token)
        );
        $result = $this->https_request($fangxinqian_new_contract, json_encode($postData),$curlset);
        $res = json_decode($result,true);
        if($res['success']){
            //保存放心签的待签订件
            $this->saveElecContractPdf($res['data']['contractUrl'],'files_style5','合同审核件','','');
            return array('success'=>1,'msg'=>'','data'=>$res['data']);
        }
        return array('success'=>0,'msg'=>'发起电子合同失败');
    }

    public function getPDFView(){
        $token=$this->getFangXinQianToken();
        if(!$token){
            $returnmsg=array('success'=>0,'msg'=>'获取放心签token异常,请重试');
            echo json_encode($returnmsg);
            exit();
        }
        global $fangxinqianview;
        $postData = array(
            'contractId'=>$_REQUEST['contractId']
        );
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "host:fxq-order-test.oss-cn-hangzhou.aliyuncs.com",
            "token:".$token));
        $res = $this->https_request($fangxinqianview."?".http_build_query($postData), '',$curlset);
        $res = json_decode($res,true);
        $res['data']['contract'] = "index.php?module=TyunWebBuyService&action=getTransferPDFView&fileurl=".base64_encode($res['data']['contract']);

        echo json_encode($res);
    }

    public function getTransferPDFView(){
        $fileurl = base64_decode($_REQUEST['fileurl']);
        $fileurlData=parse_url($fileurl);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "host:".$fileurlData['host']));
        $result =  $this->https_request($fileurl,'',$curlset);
        echo $result;
    }

    public function sendElecContract($sendEmailParams){
        $token=$this->getFangXinQianToken();
        if(!$token){
            return array('success'=>0,'msg'=>'获取放心签token异常,请重试','data'=>'');
        }
        global $fangxinqian_send;
        $postData = array(
            'contractId'=>intval($sendEmailParams['eleccontractid']),
            'number'=>$sendEmailParams['contract_no']
        );
        $formId = $this->getFangXinQianFormId();
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "formId:".$formId,
            "token:".$token));
        $this->_logs(array('fangxinqiansend',$postData));
        $result = $this->https_request($fangxinqian_send, json_encode($postData),$curlset);
        $res = json_decode($result,true);
        if($res['success']){
            //保存放心签的审核件
            $this->saveElecContractPdf($res['data'],'files_style5','-单方合同',$sendEmailParams['contract_no'],$sendEmailParams['servicecontractsid']);
            //发送签署短信
            $field = array(
                'module' => 'ServiceContracts',
                'action' => 'sendSMS2',
            );

            $fieldName = array_merge($field,array('recordid'=>$sendEmailParams['servicecontractsid']));
            $params = array(
                'fieldname' => $fieldName,
                'userid' => $this->userid
            );
            $this->call('getComRecordModule', $params);

            //"邮件通知商务，电子合同已发送给客户"
            $field = array(
                'module' => 'TyunWebBuyService',
                'action' => 'elecContractStatusSendMail2',
            );
            $fieldName = array_merge($field,$sendEmailParams);
            $params = array(
                'fieldname' => $fieldName,
                'userid' => $this->userid
            );
            $this->call('getComRecordModule', $params);
            return array('success'=>true,'msg'=>'','data'=>$res);
        }
        return array('success'=>false,'msg'=>$res['msg'],'data'=>'');
    }
    public function saveElecContractPdf($filePath,$fileState,$fileName,$contractNo,$contractId){
        $params = array(
            'fieldname' => array(
                'module' => 'ServiceContracts',
                'action' => 'mobileFileSave',
                "filepath"=>$filePath,
                "filestate"=>$fileState,
                'fileName'=>$fileName,
                "contract_no"=>$contractNo,
                "recordid"=>$contractId,
                'userid'=>$this->userid
            ),
            'userid'=>$this->userid
        );
        $list = $this->call('getComRecordModule', $params);
    }

}


