<?php

class TyunWebBuyService extends baseapp{
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
    private $GetProductActivityList;
    private $GetProductActivity;
    private $CalculationMoney;
    private $GetProductList;
    private $GetActivity;
    private $CalculationShoppingCart;
    private $AddOrder2;
    private $ALLNowActivity;
    private $crmPurchase;
    private $BindContractCode;
    private $GetUserSchoolOrderAccountCount;
    private $SendMobileCode;
    private $CheckMobileCode;
    private $GetPackageProduct;

    private $sault='multiModuleProjectDirectoryasdafdgfdhggijfgfdsadfggiytudstlllkjkgff';
    public function setConfigURL(){
        global $tyunweburl,$testtyunweburl;
        $testtyunweburl = "http://121.46.194.155/";
        $this->tyunweburl=$tyunweburl;
        $this->testtyunweburl=$testtyunweburl;
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
        $this->getAccountByName = $this->tyunweburl.'api/app/tcloud-account/v1.0.0/authentication/getAccountByName';

        $this->GetProductActivity = $this->tyunweburl.'api/micro/order-basic/v1.0.0/api/Activity/GetProductActivityByCRM';
//      $this->GetProductActivity = $this->tyunweburl.'api/micro/order-basic/v1.0.0/api/Activity/GetProductActivity';
        $this->CalculationMoney = $this->tyunweburl.'api/app/tcloud-agent/v1.0.0/api/calculationMoney';
        $this->GetProductActivityList = $this->tyunweburl.'api/micro/order-basic/v1.0.0/api/Activity/GetProductActivityList';

        $this->GetActivity = $this->tyunweburl.'api/micro/order-basic/v1.0.0/api/Activity/GetActivity';
        $this->CalculationShoppingCart = $this->tyunweburl.'api/micro/order-basic/v1.0.0//api/User_ShoppingCart/CalculationShoppingCart';
        $this->AddOrder2 = $this->tyunweburl.'api/micro/order-basic/v1.0.0/api/Order/AddOrder2';
        $this->ALLNowActivity = $this->tyunweburl.'api/micro/order-basic/v1.0.0/api/Activity/ALLNowActivity';
	    $this->crmPurchase=$this->tyunweburl."api/app/tcloud-agent/v1.0.0/api/crmPurchase";//活动下单
        $this->BindContractCode=$this->tyunweburl.'api/micro/order-basic/v1.0.0/api/Order/BindContractCode';
        $this->GetUserSchoolOrderAccountCount=$this->tyunweburl.'api/micro/order-basic/v1.0.0/api/User_Product/GetUserSchoolOrderAccountCount';
        $this->CheckMobileCode = "http://tyapi.71360.com/api/app/aggregateservice-api/v1.0.0/api/SMS/CheckMobileCode";
        $this->SendMobileCode = "http://tyapi.71360.com/pi/app/aggregateservice-api/v1.0.0/api/SMS/SendMobileCaptcha";
        $this->GetPackageProduct=$this->tyunweburl.'api/micro/order-basic/v1.0.0/api/Package/GetPackageProduct';
    }
    /**
     * 购买页面
     */
    public function index(){
        global $arr_cs_admin,$arr_ignore_check;
        $this->specialAuthority();

        $this->smarty->display('TyunWebBuyService/add.html');
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
        $this->smarty->display('TyunWebBuyService/renew.html');
    }

    /**
     * 升级页面
     */
    public function upgrade(){
        $this->specialAuthority();
        global $arr_ignore_check,$arr_cs_admin,$arr_service_role,$arr_cs_special;
        $is_cs_admin = in_array($this->userid,$arr_cs_admin) || in_array($this->roleid,$arr_service_role) || in_array($this->userid,$arr_cs_special) || in_array($this->userid,$arr_ignore_check);
        $this->smarty->assign('isService',$is_cs_admin);
        $this->smarty->display('TyunWebBuyService/upgrade.html');
    }
    /**
     * 降级页面
     */
    public function degrade(){
        $this->specialAuthority();
        global $arr_ignore_check,$arr_cs_admin,$arr_service_role,$arr_cs_special;
        $is_cs_admin = in_array($this->userid,$arr_cs_admin) || in_array($this->roleid,$arr_service_role) || in_array($this->userid,$arr_cs_special) || in_array($this->userid,$arr_ignore_check);
        $this->smarty->assign('isService',$is_cs_admin);
        $this->smarty->display('TyunWebBuyService/degrade.html');

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
        if(in_array($this->userid,array(1734 ,2824 ,1179 ,7871))){
            echo json_encode(array('success'=>true,'message'=>'已发送'));
            exit();
        }
        $this->setConfigURL();
        $mobile = $_REQUEST['mobile'];
        $sault=$this->sault;
        $time=time().'123';
        $token=md5($time.$sault);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));
        $postData = array(
            "mobile"=>$mobile,
            "content"=> "【珍岛集团】验证码为（{0}），请在3分钟内验证手机。若非本人操作，请忽视此条信息。",
            'key'=>'tyunbuy'.$mobile
        );
        $this->_logs(array("getTyunWebUserCode：", $postData));
        $url = $this->SendMobileCode;
        $res = $this->https_request($url, json_encode($postData),$curlset);
        $res = json_decode($res,true);
        if($res['success']){
            echo json_encode(array('success'=>true,'message'=>'已发送'));
            exit();
        }
        echo  json_encode(array('success'=>false,'message'=>$res['message']));
    }

    public function checkVerifyCode(){
        if(in_array($this->userid,array(1734 ,2824 ,1179 ,7871))){
            echo json_encode(array('success'=>true,'message'=>'验证通过'));
            exit();
        }
        $mobile = $_REQUEST['mobile'];
        $code = $_REQUEST['code'];
        $this->setConfigURL();
        $sault=$this->sault;
        $time=time().'123';
        $token=md5($time.$sault);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));
        $postData = array(
            "mobile"=>$mobile,
            'code'=>strval($code),
            'key'=>'tyunbuy'.$mobile
        );
        $this->_logs(array('checkVerifyCodeParams',$postData));
        $url = $this->CheckMobileCode;
        $res = $this->https_request($url, json_encode($postData),$curlset);
        echo $res;
        }

    public function https_request($url, $data = null,$curlset=array(),$islog=true){
        $this->_logs(array("发送到T云服务端的url请求", $url));
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
        if($islog){
            $this->_logs(array("返回处理结果：", $output));
        }
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
            if($signaturetype=='papercontract'){
                if($servicecontractsid<=0){
                    $returnmsg['msg']='合同无效，请重新选择';
                    break;
                }
                if($accountid<=0){
                    $returnmsg['msg']='客户名称无效，请重新选择';
                    break;
                }
                if($mobilevcode==''){
                    $returnmsg['msg']='验证码无效，请重新获取';
                    break;
                }
            }

            if(!preg_match("/^1[3456789]\d{9}$/",$mobile)){
                $returnmsg['msg']='手机号码无效';
                break;
            }

            if($flag==0){
                $tyunusercode=$flag!=0?'':$_REQUEST['tyunusercodetext'];
                if(!$this->checkTyunExistBuy($tyunusercode)){
                    $returnmsg['msg']='存在未签收的合同，请先处理！';
                    break;
                }

                if(!$this->checkTyunIsPay($tyunusercode)){
                    $returnmsg['msg']='存在未支付的订单,请先处理！';
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
        //$servicecontractsid =$_REQUEST['servicecontractsid'];
        //$servicecontractsid_display =$_REQUEST['servicecontractsid_display'];
        $accountid =$_REQUEST['accountid'];
        $accountid_display =$_REQUEST['accountid_display'];
        $mobile =$_REQUEST['mobile'];
        //$mobilevcode =$_REQUEST['mobilevcode'];
        $classtype =$_REQUEST['classtype'];
        $categoryID = isset($_REQUEST['categoryID']) ? $_REQUEST['categoryID']:0;//
        $agents =$_REQUEST['agents'];
        $returnmsg=array('success'=>0);
        do{
            $checkdata=$this->checkBasicInfo(1);
            if($checkdata['flag']){
                $returnmsg['msg']=$checkdata['msg'];
                break;
            }
            $classtype=$classtype=='buy'?0:0;
            $time=time().'123';
            $sault=$time.$this->sault;
            $token=md5($sault);
            $postData=json_encode(array("agentId"=>$agents,'cid'=>$accountid,'nickName'=>$accountid_display,'phoneNumber'=>$mobile,'contractType'=>$classtype,'categoryType'=>$categoryID));
            $this->_logs(array("getTyunWebUserCode：", $postData));
            $curlset=array(CURLOPT_HTTPHEADER=>array(
                "Content-Type:application/json",
                "S-Request-Token:".$token,
                "S-Request-Time:".$time));
            $res = $this->https_request($this->getUserCode, $postData,$curlset);
            $this->_logs(array("getTyunWebUserCodeReturnData：", $res));
            $data=json_decode($res,true);
            if($data['code']==200){
                $returnmsg['success']=1;
                $temparray=array();
                foreach($data['data'] as $value){
                    $temparray[]=array('id'=>$value['id'],'loginName'=>$value['loginName'],'authenticationType'=>$value['authenticationType']);
                }
                $returnmsg['data']=$temparray;
            }else{
                $returnmsg['msg']=$data['message'];
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
     * 处理订单
     */
    public function doOrder(){
        $this->setConfigURL();
        $servicecontractsid =$_REQUEST['servicecontractsid'];//服务合同id
        $servicecontractsid_display =$_REQUEST['servicecontractsid_display'];//服务合同编号
        $accountid =$_REQUEST['accountid'];//客户id
        $accountid_display =$_REQUEST['accountid_display'];//客户名称
        $mobile =$_REQUEST['mobile'];//手机号
        $mobilevcode =$_REQUEST['mobilevcode'];//验证码
        $productclassonevalues =$_REQUEST['productclassonevalues'];//产品分类
        $productclasstwovalues =$_REQUEST['productclasstwovalues'];//购买套餐
        $buyyear =$_REQUEST['buyyear'];//合同类型购买和续费
        $classtype =$_REQUEST['classtype'];//合同类型购买和续费
        $agents =$_REQUEST['agents'];//代理商id
        $servicetotal=$_REQUEST['servicetotal'];
        $tyunusercode=$_REQUEST['tyunusercode'];
        $tyunusercodename=$_REQUEST['tyunusercodetext'];
        $buydate=empty($_POST['buydate'])?date('Y-m-d H:i:s'):date('Y-m-d',strtotime($_POST['buydate'])).date(' H:i:s');
        $oldcustomerid = $_REQUEST['oldcustomerid'];
        $oldcustomername = $_REQUEST['oldcustomername'];
        $returnmsg=array('success'=>0);
        do{
            $checkdata=$this->checkBasicInfo();
            if($checkdata['flag']){
                $returnmsg['msg']=$checkdata['msg'];
                break;
            }
            $ProductInfo = $this->handleOtherProduct($_REQUEST);
            $ProductType=1;
            $tempproductclasstwovalues=$productclasstwovalues;
            if($productclasstwovalues=='nobuypack'){  //另购的时候不购买套餐
                $productclasstwovalues=0;
                $ProductType=1;
            }
            $tyunparams=array(
                "type"=>1,//0线上1线下
                "productType"=>$ProductType,//商品类型(1购买)
                "contractCode"=>$servicecontractsid_display,//合同编号
                "userID"=>$tyunusercode,//用户编号
                "agentIdentity"=>$agents,//代理商ID
                "discount"=>1,//折扣
                "categoryID"=>$productclassonevalues,//产品分类(0国内版 1一带一路)
                "buyTerm"=>$buyyear,//购买年限
                "packageID"=>$productclasstwovalues,//套餐编号
                "productInfo"=>$ProductInfo,
                "contractMoney"=>$servicetotal,
                "addDate"=>$buydate,
                "crmOrderFlag"=>1,//ERP提交订单,
                "oldcustomerid"=>$oldcustomerid,
                "oldcustomername"=>$oldcustomername
            );

            $this->_logs(array("getTyunWebUserCode：", json_encode($tyunparams)));
            $postData=json_encode($tyunparams);
            $time=time().'123';
            $sault=$time.$this->sault;
            $token=md5($sault);
            $curlset=array(CURLOPT_HTTPHEADER=>array(
                "Content-Type:application/json",
                "S-Request-Token:".$token,
                "S-Request-Time:".$time));
            $res = $this->https_request($this->doOrder, $postData,$curlset);
            $this->_logs(array("getTyunWebUserCodereturndata ：",$res));
            $data=json_decode($res,true);
            if($tempproductclasstwovalues=='nobuypack'){
                $productclasstwovalues=0;
            }
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
                    'tyunurl'=>$this->doOrder,
                    'usercode'=>$tyunusercodename,
                    'contractprice'=>$servicetotal,
                    'usercodeid'=>$tyunusercode
                );

                $this->handleTyunResult($data,$r_params,$tyunparams,'buy');
                $returnmsg=array('success'=>1,'msg'=>'下单成功');
            }else{
                $returnmsg['msg']=$data['message'];
            }
        }while(0);
        echo json_encode($returnmsg);
        exit;
    }

    /**
     * 获取另购产品
     */
    public function getOtherPorduct(){
        $this->setConfigURL();
        $categoryID = $_REQUEST['categoryID'];
        $seriesID = $_REQUEST['seriesID'];
        $authenticationType= $_REQUEST['authenticationType'];
        $userID= $_REQUEST['userID']?$_REQUEST['userID']:0;
        $activityAgent = $_REQUEST['activityAgent'] ? $_REQUEST['activityAgent'] : 0;
        $buyTrem = $_REQUEST['buyTrem'] ? $_REQUEST['buyTrem'] : 0 ;
        $postData=json_encode(array('seriesID'=>$seriesID,'authenticationType'=>intval($authenticationType),'userID'=>intval($userID),'activityAgent'=>intval($activityAgent),'buyTrem'=>intval($buyTrem)));
        $this->_logs(array('getOtherPorduct params',$postData));
        $time=time().'123';
        $sault=$time.$this->sault;
        $token=md5($sault);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));
        $res = $this->https_request($this->getOtherPorducts, $postData,$curlset);
        $res = json_decode($res,true);

        $categorys = array();
        $categoryLists = $this->https_request($this->AllCategory, array(),$curlset);;
        $categoryLists = json_decode($categoryLists,true);
        foreach ($categoryLists['data'] as $categoryList){
            if($categoryList['ID']==2){
                continue;
            }
            $categorys[$categoryList['ID']] = $categoryList['Title'];
        }

        if($res['success']){
            $cates = array();
            foreach ($res['data'] as $key=>$data){
                if( count($data['Products'])<1){
                    continue;
                }
                $cate = $data['Products'][0]['CategoryID'];
                //“一带一路”单品，仅支持在“一带一路”大类下进行选择。 1是一带一路的分类id
                if($categoryID==1){
                    if($cate!=1){
                        continue;
                    }
                    $cates[$cate]['title'] = $categorys[$cate];
                    $cates[$cate]['data'][$key] = $data;
                }else{
                    if($cate==1 || $cate==2){
                        continue;
                    }

                    $cates[$cate]['title'] = $categorys[$cate];
                    $cates[$cate]['data'][$key] = $data;
                }

            }
        }
        $lastData = array(
            'success'=>true,
            'code'=>200,
            'data'=>$cates,
            'categoryid'=>$categoryID,
        );

        echo json_encode($lastData);

    }

    /**
     * 获取续费套餐
     */
    public function getUserRenewProductInfo(){
        $this->setConfigURL();
        $categoryID = $_REQUEST['classtyperenew'];
        $UserID = $_REQUEST['tyunusercode'];
        $tyunusername = $_REQUEST['tyunusername'];
        $classtype = $_REQUEST['classtype'];
        $buyproductid = $_REQUEST['buyproductid'];

        // cxh 获取最近购买信息 start
        $params = array(
            'fieldname'=>array(
                'tyun_account'	=> $tyunusername,
                'tyun_type'	=> $classtype,
            ),
            'userid'			=> $this->userid
        );
        $list = $this->call('searchTyunBuyServiceInfo', $params);
        $lista = $list[0];
        //  cxh  end


        if(!empty($UserID)){
//            $activecode = $lista['buyList'][0]['activecode'];
            $startdate =  $lista['buyList'][0]['startdate'];
            if($lista['buyList'][0]['classtype']=='degrade' && $startdate=='0000-00-00 00:00:00'){
                $returndata=array('code'=>500,'message'=>'降级订单未生效不可进行当前操作');
                $res=json_encode($returndata);
                echo $res;
                exit();
            }
            // 如果是院校版获取 账号数量
            if($categoryID==7 ||$categoryID==9){
                $postData=json_encode(array('userID'=>$UserID));
                $this->_logs(array('GetUserSchoolOrderAccountCount:',$postData));
                $time=time().'123';
                $sault=$time.$this->sault;
                $token=md5($sault);
                $curlset=array(CURLOPT_HTTPHEADER=>array(
                    "Content-Type:application/json",
                    "S-Request-Token:".$token,
                    "S-Request-Time:".$time));
                $res = $this->https_request($this->GetUserSchoolOrderAccountCount, $postData,$curlset);
                $countjsonData=json_decode($res,true);
            }
            $postData=json_encode(array('categoryID'=>intval($categoryID),'userID'=>intval($UserID),'authenticationType'=>1));
            $this->_logs(array('getUserRenewProductInfo:',$postData));
            $time=time().'123';
            $sault=$time.$this->sault;
            $token=md5($sault);
            $curlset=array(CURLOPT_HTTPHEADER=>array(
                "Content-Type:application/json",
                "S-Request-Token:".$token,
                "S-Request-Time:".$time));
            $res = $this->https_request($this->UserRenewProductInfo, $postData,$curlset);
            $jsonData=json_decode($res,true);
            $params = array(
                'fieldname' => array(
                    'module' => 'TyunWebBuyService',
                    'action' => 'getProductsServicescontract',
                    'tyunusercode' => $UserID,
                    'tyunusername' => $tyunusername,
                    'categoryid' => $categoryID,
                    'classtype' => $classtype,
                    'userid' => $this->userid
                ),
                'userid' => $this->userid
            );
            $list = $this->call('getComRecordModule', $params);
            if (!empty($list[0])) {
                $jsonData['contract']=current($list[0]);
            }

            $jsonData['data']['domaininpackage'] = false;
            $jsonData['data']['domaincount'] =  0;
            //根据套餐id获取该套餐是否有域名权益
            //没有传buyproductid  则默认当前套餐为购买套餐
            $resparams = array(
                'packageId'=>$buyproductid?$buyproductid:$jsonData['data']['package']["ID"],
                'productId'=>52
            );
            $postData=json_encode($resparams);
            $time=time().'123';
            $sault=$time.$this->sault;
            $token=md5($sault);
            $curlset=array(CURLOPT_HTTPHEADER=>array(
                "Content-Type:application/json",
                "S-Request-Token:".$token,
                "S-Request-Time:".$time));
            $this->_logs(array("GetPackageProduct request data:",$postData));
            $res = $this->https_request($this->GetPackageProduct, $postData,$curlset);
            $res = json_decode($res,true);
            if($res['success'] && $res['data'][0]['Count']){
                $jsonData['data']['domaininpackage'] = true;
                $jsonData['data']['domaincount'] =  $res['data'][0]['Count'];
            }

            //套餐域名 和 另购域名
            $packageDomains=array();
            $productDomains= array();
            $packageSpecificationLists = $jsonData['data']['packageSpecificationList'];
            foreach ($packageSpecificationLists as $packageSpecificationList){
                if($packageSpecificationList['ProductID']==52){
                    $packageDomains[]=$packageSpecificationList;
                }
            }

            $productSpecificationLists = $jsonData['data']['productSpecificationList'];
            foreach ($productSpecificationLists as $productSpecificationList){
                if($productSpecificationList['ProductID']==52){
                    $productDomains[]=$productSpecificationList;
                }
            }

            $jsonData['data']['domains']= count($packageDomains)>0 ? array_merge($packageDomains,$productDomains) :$productDomains;

            $tempData=$jsonData;
            $tempData['productList']=$lista['buyList'][0];
            if(!empty($countjsonData)){
                $tempData['accountNumber']=$countjsonData['data'];
            }else{
                $tempData['accountNumber']=0;
            }
            $res=json_encode($tempData);
        }else{
            $returndata=array('code'=>500,'message'=>'无效的输入');
            $res=json_encode($returndata);
        }
        echo $res;
    }

    /**
     * 续费订单
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
        $ispackage=$_REQUEST['ispackage'];//是否套餐续费
        $packageid=$_REQUEST['packageid'];//套餐ID

        $categoryid=$_REQUEST['categoryid'];//产品分ID
        $packageid=!empty($packageid)?$packageid:0;
        $packageid=$ispackage==1?$packageid:0;//是否套餐续费,如果不是套餐续费套餐ID为0
        //start续费套餐产品
        $packspecificationstitle=$_REQUEST['packspecificationstitle'];
        $packproductid=$_REQUEST['packproductid'];
        $packnumber=$_REQUEST['packnumber'];
        $packproducttitle=$_REQUEST['packproducttitle'];
        $packcategoryid=$_REQUEST['packcategoryid'];
        $packspecificationsid=$_REQUEST['packspecificationsid'];
        //end续费套餐产品
        //start续费另购产品
        $otherpackspecificationstitle=$_REQUEST['otherpackspecificationstitle'];
        $otherproduct=$_REQUEST['otherproductid'];
        $otherproducttitle=$_REQUEST['otherproducttitle'];
        $otherpackcategoryid=$_REQUEST['otherpackcategoryid'];
        $otherpacknumber=$_REQUEST['otherpacknumber'];
        $otherspecificationsid=$_REQUEST['otherspecificationsid'];
        //end续费另购产品

        $oldproductname=$_REQUEST['oldproductname'];
        $servicetotal=$_REQUEST['servicetotal'];
        $unit=$_REQUEST['unit'];
        $specificationstitle=$_REQUEST['specificationstitle'];
        $tyunusercode=$_REQUEST['tyunusercode'];
        $tyunusercodename=$_REQUEST['tyunusercodetext'];
        $agents =$_REQUEST['agents'];
        $oldcustomerid = $_REQUEST['oldcustomerid'];
        $oldcustomername =$_REQUEST['oldcustomername'];
        $buydate=empty($_POST['buydate'])?date('Y-m-d H:i:s'):date('Y-m-d',strtotime($_POST['buydate'])).date(' H:i:s');
        $returnmsg=array('success'=>0);
        do{
            $checkdata=$this->checkBasicInfo();
            if($checkdata['flag']){
                $returnmsg['msg']=$checkdata['msg'];
                break;
            }
            $ProductInfo=array();
            $oterproductids='';
            $productnames=array();
            $productnamesproduct=array();
            if($ispackage==0){
                foreach($packspecificationsid as $key=>$value){
                    $tempdata=array('userProductID'=>$value,'buyTerm'=>$buyyear);
                    $ProductInfo[]=$tempdata;
                    //$oterproductids.=','.$value;
                    $oterproductids.=','.$packproductid[$key];
                    if(!in_array($packproductid[$key],$productnamesproduct)){
                        $oterproductids.=','.$packproductid[$key];
                        $productnamesproduct[]=$packproductid[$key];
                        $productnames[$packproductid[$key]]=array("productID"=>$packproductid[$key],
                            "productTitle"=>$packproducttitle[$key],
                            "productCount"=>1,
                            "specificationId"=>$value,
                            "specificationTitle"=>$packspecificationstitle[$key],);
                    }else{
                        $productnames[$packproductid[$key]]["productCount"]=$productnames[$packproductid[$key]]["productCount"]+1;
                        $productnames[$packproductid[$key]]["specificationTitle"].=','.$value;
                        $productnames[$packproductid[$key]]["specificationId"].=','.$packspecificationstitle[$key];
                    }

                }
            }
            foreach($otherspecificationsid as $key=>$value){
                $tempdata=array('userProductID'=>$value,'buyTerm'=>$buyyear);
                $ProductInfo[]=$tempdata;
                if(!in_array($otherproduct[$key],$productnamesproduct)) {
                    $oterproductids.=','.$otherproduct[$key];
                    $productnamesproduct[]=$otherproduct[$key];
                    $productnames[$otherproduct[$key]] = array("productID" => $otherproduct[$key],
                        "productTitle" => $otherproducttitle[$key],
                        "productCount" => 1,
                        "specificationId" => $value,
                        "specificationTitle" => $otherpackspecificationstitle[$key],);
                }else{
                    $productnames[$otherproduct[$key]]["productCount"]=$productnames[$otherproduct[$key]]["productCount"]+1;
                    $productnames[$otherproduct[$key]]["specificationId"].=','.$value;
                    $productnames[$otherproduct[$key]]["specificationTitle"].=','.$otherpackspecificationstitle[$key];
                }
            }
            $oterproductids=trim($oterproductids,',');
            if($ispackage==0){
                if(empty($ProductInfo)){
                    $returnmsg['msg']='没有续费的产品';
                    break;
                }
            }

            $otherProductInfo = $this->handleOtherProduct($_REQUEST);
//            $oterproductids = trim($oterproductids.','.$return_oterproductids,',');
            $tyunparams=array(
                "type"=>1,//0线上1线下
                "productType"=>4,//商品类型(1购买)
                "contractCode"=>$servicecontractsid_display,//合同编号
                "userID"=>$tyunusercode,//用户编号
                "agentIdentity"=>$agents,//代理商ID
                "discount"=>1,//折扣
                "categoryID"=>$categoryid,//产品分类(0国内版 1一带一路)
                "buyTerm"=>$buyyear,//购买年限
                "packageID"=>$packageid,//套餐编号
                "renewproducts"=>$ProductInfo,
                "contractMoney"=>$servicetotal,
                "addDate"=>$buydate,
                "crmOrderFlag"=>1,  //ERP提交订单
                "productInfo"=>$otherProductInfo
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
//                    'buyseparately'=>$oterproductids,
                    "agentIdentity"=>$agents,//代理商ID
//                    'productnames'=>array_merge($productnames,$productERP),
                    "oldcustomerid"=>$oldcustomerid,
                    "oldcustomername"=>$oldcustomername
                );

                $this->handleTyunResult($data,$r_params,$tyunparams,'renew');
                $returnmsg['msg']='续费成功';
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

    public function checkTyunIsPay($loginname){
        $params = array(
            'fieldname'=>array(
                'tyun_account'	=> $loginname,
            ),
            'userid'			=> $this->userid
        );
        $list = $this->call('checkTyunIsPay', $params);
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
        $return=array('success'=>1);
        do{
            if(!$this->checkTyunExistBuy($tyunusercode)){
                $return=array('success'=>0,'msg'=>'存在未签的合同，请先处理');
                break;
            }

            if(!$this->checkTyunIsPay($tyunusercode)){
                $return=array('success'=>0,'msg'=>'存在未支付的订单,请先处理');
                break;
            };
        }while(0);
        echo json_encode($return);
    }

    /**
     * 升级获取产品
     */
    public function getAllProducts(){
        $this->setConfigURL();
        $userID = $_REQUEST['tyunusercode'];//用户ID
        $tyunusername = $_REQUEST['tyunusername'];//用户ID
        $categoryID = $_REQUEST['classtyperenew'];//产品分类编号(0国内版 1一带一路 2云市场 3数字媒体)
        $classtype = $_REQUEST['classtype'];//升级,续费,降级
        $type = $_REQUEST['type'];//类型(0，全部 1，单品 2 套餐)
        // cxh 获取最近购买信息 start
        $params = array(
            'fieldname'=>array(
                'tyun_account'	=> $tyunusername,
                'tyun_type'	=> $classtype,
            ),
            'userid'			=> $this->userid
        );
        $list = $this->call('searchTyunBuyServiceInfo', $params);
        $lista = $list[0];
        //  cxh  end
        //$authenticationType= $_REQUEST['authenticationType'];
        $postData=json_encode(array("userID"=>intval($userID),'categoryID'=>intval($categoryID),
            "type"=>intval($type),"pageIndex"=>1,"pageSize"=>100));
        $this->_logs(array($this->getAllProductsurl.'，：'.$postData));
        $time=time().'123';
        $sault=$time.$this->sault;
        $token=md5($sault);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));
        $res = $this->https_request($this->getAllProductsurl, $postData,$curlset);
        $this->_logs(array($this->getAllProductsurl.' data1：'.$res));
        $res_jsonData=json_decode($res,true);
        $classtypename=array('upgrade'=>'升级','degrade'=>'降级');
        if($res_jsonData['code']=='200'){
            $tempData=array();
            if(empty($res_jsonData['data'])){
                echo json_encode(array('code'=>500,'message'=>'没有可'.$classtypename[$classtype].'的产品'));
                exit;
            }
            foreach($res_jsonData['data'] as $value) {
                if ($value['CanUpgrade'] && $classtype=='upgrade') {
                    $userPackageID = $value['ProductRecordID'];
                    $postData = json_encode(array("userID" => $userID, 'userPackageID' => $userPackageID));
                    $time = time() . '123';
                    $sault = $time . $this->sault;
                    $token = md5($sault);
                    $curlset = array(CURLOPT_HTTPHEADER => array(
                        "Content-Type:application/json",
                        "S-Request-Token:" . $token,
                        "S-Request-Time:" . $time));
                    $this->_logs(array($this->userPackageUpgradeInfo . '，返回结果：', $postData));
                    $res = $this->https_request($this->userPackageUpgradeInfo, $postData, $curlset);

                    $this->_logs(array($this->userPackageUpgradeInfo . '，返回结果：' . $res));
                    $jsonData=json_decode($res,true);
                    $params = array(
                        'fieldname' => array(
                            'module' => 'TyunWebBuyService',
                            'action' => 'getProductsServicescontract',
                            'tyunusercode' => $userID,
                            'tyunusername' => $tyunusername,
                            'categoryid' => $categoryID,
                            'classtype'=>$classtype,
                            'userid' => $this->userid
                        ),
                        'userid' => $this->userid
                    );
                    $list = $this->call('getComRecordModule', $params);
                    if (!empty($list[0])) {
                        $jsonData['contract']=current($list[0]);
                    }
                    $tempData=$jsonData;
                }
                if ($value['CanDegrade'] && $classtype=='degrade') {
                    $userPackageID = $value['ProductRecordID'];
                    $postData = json_encode(array("userID" => $userID, 'userPackageID' => $userPackageID));
                    $time = time() . '123';
                    $sault = $time . $this->sault;
                    $token = md5($sault);
                    $curlset = array(CURLOPT_HTTPHEADER => array(
                        "Content-Type:application/json",
                        "S-Request-Token:" . $token,
                        "S-Request-Time:" . $time));
                    $this->_logs(array($this->userPackageDegradeInfo . '，请求：' . json_encode($postData)));
                    $res = $this->https_request($this->userPackageDegradeInfo, $postData, $curlset);
                    $this->_logs(array($this->userPackageDegradeInfo . '，返回结果：' . $res));
                    $jsonData=json_decode($res,true);
                    $params = array(
                        'fieldname' => array(
                            'module' => 'TyunWebBuyService',
                            'action' => 'getProductsServicescontract',
                            'tyunusercode' => $userID,
                            'tyunusername' => $tyunusername,
                            'categoryid' => $categoryID,
                            'classtype' => $classtype,
                            'userid' => $this->userid
                        ),
                        'userid' => $this->userid
                    );
                    $list = $this->call('getComRecordModule', $params);
                    if (!empty($list[0])) {
                        $jsonData['contract']=current($list[0]);

                    }
                    $tempData=$jsonData;
                }
            }
            // 如果是院校版获取 账号数量
            if($categoryID==7 || $categoryID==9){
                $postData=json_encode(array('userID'=>$userID));
                $this->_logs(array('GetUserSchoolOrderAccountCount:',$postData));
                $time=time().'123';
                $sault=$time.$this->sault;
                $token=md5($sault);
                $curlset=array(CURLOPT_HTTPHEADER=>array(
                    "Content-Type:application/json",
                    "S-Request-Token:".$token,
                    "S-Request-Time:".$time));
                $res = $this->https_request($this->GetUserSchoolOrderAccountCount, $postData,$curlset);
                $countjsonData=json_decode($res,true);
            }
            $tempData['productList']=$lista['buyList'][0];
            if(!empty($countjsonData)){
                $tempData['accountNumber']=$countjsonData['data'];
            }else{
                $tempData['accountNumber']=0;
            }
            if(!isset($tempData['code'])){
                $tempData['code'] =500;
                $tempData['message']='没有可'.$classtypename[$classtype].'产品';
            }

            echo json_encode($tempData);
        }else{
            echo $res;
        }
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
        $res = $this->https_request($this->userPackageUpgradeMoney, $postData,$curlset);
        $res=str_replace(array('\\r','\\n'),'',$res);
        $res=str_replace('\"','"',$res);
        $res=preg_replace('/^"|"$/','',$res);
        echo $res;
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
        $tyunusercodeid=$_REQUEST['tyunusercodeid'];
        $categoryid=$_REQUEST['categoryid'];
        $packageid=$_REQUEST['buyproduct'];
        $agents =$_REQUEST['agents'];
        $oldcustomerid = $_REQUEST['oldcustomerid'];
        $oldcustomername =$_REQUEST['oldcustomername'];
        $orderordercode=$_REQUEST['activacode'];
        $oldproductname=$_REQUEST['oldproductname'];
        $chooseuserproduct = $_REQUEST['chooseuserproduct'];

        $returnmsg=array('success'=>0);
        do{
            $checkdata=$this->checkBasicInfo();
            if($checkdata['flag']){
                $returnmsg['msg']=$checkdata['msg'];
                break;
            }
            $ProductInfo = $this->handleOtherProduct($_REQUEST);
            $tyunparams=array(
                "type"=>1,//0线上1线下
                "productType"=>2,//商品类型(2升级)
                "contractCode"=>$servicecontractsid_display,//合同编号
                "userID"=>$tyunusercodeid,//用户编号
                "agentIdentity"=>0,//代理商ID
                "discount"=>1,//折扣
                "categoryID"=>$categoryid,//产品分类(0国内版 1一带一路)
                "buyTerm"=>$buyyear,//购买年限
                "packageID"=>$packageid,//套餐编号
                "contractMoney"=>$servicetotal,
                "crmOrderFlag"=>1,  //ERP提交订单
                "productInfo"=>$ProductInfo,
                "chooseUserProduct"=>$chooseuserproduct
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
//                    'productnames'=>$productERP,
//                    'buyseparately'=>$oterproductids,
                    "oldcustomerid"=>$oldcustomerid,
                    "oldcustomername"=>$oldcustomername,
                    'oldproductname'=>$oldproductname,
                    'orderordercode'=>$orderordercode,
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
     * 注册T云账户
     */
    public function regTyunUserCode(){
        $this->setConfigURL();
        $servicecontractsid =$_REQUEST['servicecontractsid'];
        $servicecontractsid_display =$_REQUEST['servicecontractsid_display'];
        $accountid =$_REQUEST['accountid'];
        $accountid_display =$_REQUEST['accountid_display'];
        $mobile =$_REQUEST['mobile'];
        $mobilevcode =$_REQUEST['mobilevcode'];
        $classtype =$_REQUEST['classtype'];
        $usercode =$_REQUEST['usercode'];
        $agents =$_REQUEST['agents'];
        $returnmsg=array('success'=>0);
        do{
            if(empty($usercode)){
                $returnmsg['msg']='用户名为空';
                break;
            }
            if(strlen($usercode)<6){
                $returnmsg['msg']='用户名不能小于6个字';
                break;
            }
            if(!preg_match("/^[a-zA-Z0-9][a-zA-Z0-9]+[a-zA-Z0-9]$/",$usercode)){
                $returnmsg['msg']='用户名仅支持英文和数字';
                break;
            }
            if(strlen($usercode)>20){
                $returnmsg['msg']='用户名不能超过20个字';
                break;
            }
            $checkdata=$this->checkBasicInfo();
            if($checkdata['flag']){
                $returnmsg['msg']=$checkdata['msg'];
                break;
            }

            $paramData = array(
                'fieldname'=>array(
                    'accountid'	=> $accountid,
                ),
                'userid'			=> $this->userid
            );
            $list = $this->call('getAccountContent', $paramData);
            $province=$list[0]['province'];
            $city=$list[0]['city'];
            $area=$list[0]['area'];
            $classtype=$classtype=='buy'?0:1;
            $time=time().'123';
            $sault=$time.$this->sault;
            $token=md5($sault);
            $postData=json_encode(array("agentId"=>$agents,'cid'=>$accountid,'nickName'=>$accountid_display,'phoneNumber'=>$mobile,'contractType'=>$classtype,'loginName'=>$usercode,'province'=>$province,'city'=>$city,'area'=>$area));
            $this->_logs(array("regTyunUserCode：", $postData));
            $curlset=array(CURLOPT_HTTPHEADER=>array(
                "Content-Type:application/json",
                "S-Request-Token:".$token,
                "S-Request-Time:".$time));
            $res = $this->https_request($this->accountRegister, $postData,$curlset);
            $this->_logs(array("regTyunUserCodeReturnData：", $res));
            $data=json_decode($res,true);
            if($data['code']==200){
                $returnmsg['success']=1;
                $temparray=array();
                $password='';
                foreach($data['data'] as $value){
                    $temparray[]=array('id'=>$value['id'],'loginName'=>$value['loginName']);
                    if($value['loginName']==$usercode){
                        $password=$value['password'];
                    }
                }
                $params = array(
                    'fieldname' => array(
                        'module' => 'TyunWebBuyService',
                        'action' => 'putTyunUsercodeAndPasswd',
                        'tyunusercode' => $usercode,
                        'tyunpassword' => $password,
                        'userid' => $this->userid
                    ),
                    'userid' => $this->userid
                );
                $list = $this->call('getComRecordModule', $params);
                $returnmsg['data']=$temparray;
            }else{
                $returnmsg['msg']=$data['message'];
            }
        }while(0);
        echo json_encode($returnmsg);
        exit;
    }

    /**
     *获取产品分类
     */
    public function getAllCategory(){
        $this->setConfigURL();
        $time=time().'123';
        $sault=$time.$this->sault;
        $token=md5($sault);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));
        $res = $this->https_request($this->AllCategory, array(),$curlset);
        echo $res;
        exit;
    }

    /**
     * 获取降级版本
     */
    public function GetUserPackageDegradeInfoData(){
        $this->setConfigURL();
        $packageID=$_POST['productid'];
        $detailBuyTerm=$_POST['buyyear'];
        $userID=$_POST['tyunusercode'];
        $data=array("agentType"=>0,
                    "discount"=>1,
                    "packageID"=>$packageID,
                    "detailBuyTerm"=>$detailBuyTerm,
                    "userID"=>$userID
        );
        $postData=json_encode($data);
        $time=time().'123';
        $sault=$time.$this->sault;
        $token=md5($sault);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));
        $res = $this->https_request($this->GetUserPackageDegrade,$postData,$curlset);
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
        $packageid=$_REQUEST['buyproduct'];
        $oldcustomerid = $_REQUEST['oldcustomerid'];
        $oldcustomername =$_REQUEST['oldcustomername'];
        $orderordercode=$_REQUEST['activacode'];
        $oldproductname=$_REQUEST['oldproductname'];
        $returnmsg=array('success'=>0);
        $buydate=empty($_POST['buydate'])?date('Y-m-d H:i:s'):date('Y-m-d',strtotime($_POST['buydate'])).date(' H:i:s');
        do{
            $checkdata=$this->checkBasicInfo();
            if($checkdata['flag']){
                $returnmsg['msg']=$checkdata['msg'];
                break;
            }
            $specificationid=$_REQUEST['specificationid'];
            $chooseUserProducts=array();
            foreach($specificationid as $value){
                $chooseUserProducts[]=intval($value);
            }
            $ProductInfo = $this->handleOtherProduct($_REQUEST);

            $tyunparams=array(
                "type"=>1,//0线上1线下
                "productType"=>3,//商品类型(3降级)
                "contractCode"=>$servicecontractsid_display,//合同编号
                "userID"=>$tyunusercodeid,//用户编号
                "agentIdentity"=>0,//代理商ID
                "discount"=>1,//折扣
                "categoryID"=>$categoryid,//产品分类(0国内版 1一带一路)
                "buyTerm"=>$buyyear,//购买年限
                "packageID"=>$packageid,//套餐编号
                "chooseUserProducts"=>$chooseUserProducts,//可套ID
                "contractMoney"=>$servicetotal,
                "crmOrderFlag"=>1, //ERP提交订单
                "productInfo"=>$ProductInfo,
                "addDate"=>$buydate
            );
            $this->_logs(array("degardedoOrder：", json_encode($tyunparams)));
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
                    'usercode'=>$tyunusercode,
                    'oldcustomerid'=>$oldcustomerid,
                    'oldcustomername'=>$oldcustomername,
                    'oldproductname'=>$oldproductname,
                    'orderordercode'=>$orderordercode,
//                    'productnames'=>$productERP,
//                    'buyseparately'=>$oterproductids,
                );
                $this->handleTyunResult($data,$r_params,$tyunparams,'degrade');
                $returnmsg['msg']='降级成功';
                $returnmsg['success']=1;
            }else{
                $returnmsg['msg']=$data['message'];
                break;
            }
        }while(0);
        echo json_encode($returnmsg);
        exit;
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
        $postData=json_encode(array('loginName'=>$tyunaccount));
        $time=time().'123';
        $sault=$time.$this->sault;
        $token=md5($sault);
        $curlset=array(
            CURLOPT_HTTPHEADER=>array(
                "Content-Type:application/json",
                "S-Request-Token:".$token,
                "S-Request-Time:".$time
            )
        );
        $res = $this->https_request($this->getAccountByName, $postData,$curlset);
        $data = json_decode($res,true);
        do{
            if($data['code'] ==200) {
                $phone_number = $data['data']['phoneNumber'];
                if ($phone_number != $mobile) {
                    $return_data = array('success' => 0, 'msg' => '新老客户手机号不一致');
                    continue;
                }
                $return_data = array(
                    'success'=>1,
                    'accountid'=>$data['data']['accountId'],
                    'mobile'=>$data['data']['phoneNumber'],
                    'loginName'=>$data['data']['loginName'],
                    'companyName'=>$data['data']['companyName'],
                    'userid'=>$data['data']['cid']
                );
                continue;
            }
            $return_data = array('success'=>0,'msg'=>$data['message']);
            continue;
        }while(0);
        echo json_encode($return_data);
        exit;
    }

    /**
     * 续费获取金额
     */
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
        $ispackage=$_REQUEST['ispackage'];//是否套餐续费
        $packageid=$_REQUEST['packageid'];//套餐ID

        $categoryid=$_REQUEST['categoryid'];//产品分ID
        $packageid=!empty($packageid)?$packageid:0;
        $packageid=$ispackage==1?$packageid:0;//是否套餐续费,如果不是套餐续费套餐ID为0
        //start续费套餐产品
        $packspecificationstitle=$_REQUEST['packspecificationstitle'];
        $packproductid=$_REQUEST['packproductid'];
        $packnumber=$_REQUEST['packnumber'];
        $packproducttitle=$_REQUEST['packproducttitle'];
        $packcategoryid=$_REQUEST['packcategoryid'];
        $packspecificationsid=$_REQUEST['packspecificationsid'];
        //end续费套餐产品
        //start续费另购产品
        $otherpackspecificationstitle=$_REQUEST['otherpackspecificationstitle'];
        $otherproduct=$_REQUEST['otherproductid'];
        $otherproducttitle=$_REQUEST['otherproducttitle'];
        $otherpackcategoryid=$_REQUEST['otherpackcategoryid'];
        $otherpacknumber=$_REQUEST['otherpacknumber'];
        $otherspecificationsid=$_REQUEST['otherspecificationsid'];
        //end续费另购产品

        $oldproductname=$_REQUEST['oldproductname'];
        $servicetotal=$_REQUEST['servicetotal'];
        $unit=$_REQUEST['unit'];
        $specificationstitle=$_REQUEST['specificationstitle'];
        $tyunusercode=$_REQUEST['tyunusercode'];
        $tyunusercodename=$_REQUEST['tyunusercodetext'];
        $agents =$_REQUEST['agents'];
        $oldcustomerid = $_REQUEST['oldcustomerid'];
        $oldcustomername =$_REQUEST['oldcustomername'];
        $buydate=empty($_POST['buydate'])?date('Y-m-d H:i:s'):date('Y-m-d',strtotime($_POST['buydate'])).date(' H:i:s');
        $returnmsg=array('success'=>0);
        do{
            $checkdata=$this->checkBasicInfo();
            if($checkdata['flag']){
                $returnmsg['msg']=$checkdata['msg'];
                break;
            }
            $ProductInfo=array();
            $oterproductids='';
            $productnames=array();
            $productnamesproduct=array();
            if($ispackage==0){
                foreach($packspecificationsid as $key=>$value){
                    $tempdata=array('userProductID'=>$value,'buyTerm'=>$buyyear);
                    $ProductInfo[]=$tempdata;
                    $oterproductids.=','.$packproductid[$key];
                    if(!in_array($packproductid[$key],$productnamesproduct)){
                        $oterproductids.=','.$packproductid[$key];
                        $productnamesproduct[]=$packproductid[$key];
                        $productnames[$packproductid[$key]]=array("productID"=>$packproductid[$key],
                            "productTitle"=>$packproducttitle[$key],
                            "productCount"=>1,
                            "specificationId"=>$value,
                            "specificationTitle"=>$packspecificationstitle[$key],);
                    }else{
                        $productnames[$packproductid[$key]]["productCount"]=$productnames[$packproductid[$key]]["productCount"]+1;
                        $productnames[$packproductid[$key]]["specificationTitle"].=','.$value;
                        $productnames[$packproductid[$key]]["specificationId"].=','.$packspecificationstitle[$key];
                    }

                }
            }
            foreach($otherspecificationsid as $key=>$value){
                $tempdata=array('userProductID'=>$value,'buyTerm'=>$buyyear);
                $ProductInfo[]=$tempdata;
                if(!in_array($otherproduct[$key],$productnamesproduct)) {
                    $oterproductids.=','.$otherproduct[$key];
                    $productnamesproduct[]=$otherproduct[$key];
                    $productnames[$otherproduct[$key]] = array("productID" => $otherproduct[$key],
                        "productTitle" => $otherproducttitle[$key],
                        "productCount" => 1,
                        "specificationId" => $value,
                        "specificationTitle" => $otherpackspecificationstitle[$key],);
                }else{
                    $productnames[$otherproduct[$key]]["productCount"]=$productnames[$otherproduct[$key]]["productCount"]+1;
                    $productnames[$otherproduct[$key]]["specificationId"].=','.$value;
                    $productnames[$otherproduct[$key]]["specificationTitle"].=','.$otherpackspecificationstitle[$key];
                }
            }
            $oterproductids=trim($oterproductids,',');
            if($ispackage==0){
                if(empty($ProductInfo)){
                    $returnmsg['msg']='没有续费的产品';
                    break;
                }
            }

            $otherProductInfo = $this->handleOtherProduct($_REQUEST);
            $tyunparams=array(
                "type"=>1,//0线上1线下
                "productType"=>4,//商品类型(1购买)
                "contractCode"=>$servicecontractsid_display,//合同编号
                "userID"=>$tyunusercode,//用户编号
                "agentIdentity"=>$agents,//代理商ID
                "discount"=>1,//折扣
                "categoryID"=>$categoryid,//产品分类(0国内版 1一带一路)
                "buyTerm"=>$buyyear,//购买年限
                "packageID"=>$packageid,//套餐编号
                "renewproducts"=>$ProductInfo,
                "contractMoney"=>$servicetotal?$servicetotal:0,
                "addDate"=>date('Y-m-d H:i:s'),
                "crmOrderFlag"=>1,  //ERP提交订单
                "productInfo"=>$otherProductInfo
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
            $this->_logs(array("CalculationMoneyResult ：",$res));
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


    /**
     * 购买获取金额
     */
    public function buyCalculationTotal(){
        $this->setConfigURL();
        $servicecontractsid =$_REQUEST['servicecontractsid'];//服务合同id
        $servicecontractsid_display =$_REQUEST['servicecontractsid_display'];//服务合同编号
        $accountid =$_REQUEST['accountid'];//客户id
        $accountid_display =$_REQUEST['accountid_display'];//客户名称
        $mobile =$_REQUEST['mobile'];//手机号
        $mobilevcode =$_REQUEST['mobilevcode'];//验证码
        $productclassonevalues =$_REQUEST['productclassonevalues'];//产品分类
        $productclasstwovalues =$_REQUEST['productclasstwovalues'];//购买套餐
        $buyyear =$_REQUEST['buyyear'];//合同类型购买和续费
        $classtype =$_REQUEST['classtype'];//合同类型购买和续费
        $agents =$_REQUEST['agents'];//代理商id
        $servicetotal=$_REQUEST['servicetotal'];
        $tyunusercode=$_REQUEST['tyunusercode'];
        $tyunusercodename=$_REQUEST['tyunusercodetext'];
        $buydate=empty($_POST['buydate'])?date('Y-m-d H:i:s'):date('Y-m-d',strtotime($_POST['buydate'])).date(' H:i:s');
        $oldcustomerid = $_REQUEST['oldcustomerid'];
        $oldcustomername = $_REQUEST['oldcustomername'];
        $numberstudentaccounts = $_REQUEST['numberstudentaccounts'];
        $returnmsg=array('success'=>0);
        do{
            $checkdata=$this->checkBasicInfo();
            if($checkdata['flag']){
                $returnmsg['msg']=$checkdata['msg'];
                break;
            }
            $ProductInfo = $this->handleOtherProduct($_REQUEST);
            $ProductType=1;
            $tempproductclasstwovalues=$productclasstwovalues;
            if($productclasstwovalues=='nobuypack'){  //另购的时候不购买套餐
                $productclasstwovalues=0;
                $ProductType=1;
            }
            $tyunparams=array(
                "type"=>1,//0线上1线下
                "productType"=>$ProductType,//商品类型(1购买)
                "contractCode"=>$servicecontractsid_display,//合同编号
                "userID"=>$tyunusercode,//用户编号
                "agentIdentity"=>$agents,//代理商ID
                "discount"=>1,//折扣
                "categoryID"=>$productclassonevalues,//产品分类(0国内版 1一带一路)
                "buyTerm"=>$buyyear,//购买年限
                "packageID"=>$productclasstwovalues,//套餐编号
                "productInfo"=>$ProductInfo,
                "contractMoney"=>$servicetotal,
                "addDate"=>$buydate,
                "crmOrderFlag"=>1,//ERP提交订单,
                "oldcustomerid"=>$oldcustomerid,
                "oldcustomername"=>$oldcustomername,
                "accountCount"=>$numberstudentaccounts
            );

            $this->_logs(array("CalculationMoney request params：", json_encode($tyunparams)));
            $postData=json_encode($tyunparams);
            $time=time().'123';
            $sault=$time.$this->sault;
            $token=md5($sault);
            $curlset=array(CURLOPT_HTTPHEADER=>array(
                "Content-Type:application/json",
                "S-Request-Token:".$token,
                "S-Request-Time:".$time));
            $res = $this->https_request($this->CalculationMoney, $postData,$curlset);
            $this->_logs(array("CalculationMoney ：",$res));
            $data=json_decode($res,true);
            if($tempproductclasstwovalues=='nobuypack'){
                $productclasstwovalues=0;
            }
            if($data['code']==200){
                $returnmsg['data'] = $data;
                $returnmsg['msg']='查询成功';
                $returnmsg['success']=1;
            }else{
                $returnmsg['msg']=$data['message'];
            }
        }while(0);
        echo json_encode($returnmsg);
        exit;
    }

    /**
     * 获取活动的详情
     */
    public function getActivityDetail(){
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

    /**
     * 获取活动列表
     */
    public function getActivity(){
        $this->setConfigURL();
        $categoryID = $_REQUEST['categoryID'];
        $packageID = $_REQUEST['packageID'];
        $productID = $_REQUEST['productID'];
        $specificationID = $_REQUEST['specificationID'];
        $activityModel = $_REQUEST['activityModel'];
        $activityAgent = $_REQUEST['activityAgent'];
        $activitytype = $_REQUEST['activityType'];
        $isCombination = $_REQUEST['isCombination'];

        $postData=json_encode(array(
                'categoryID'=>intval($categoryID),
                'packageID'=>intval($packageID),
                'productID'=>intval($productID),
                'specificationID'=>intval($specificationID),
                'activityModel'=>intval($activityModel),
                'activityAgent'=>intval($activityAgent),
//                'activityType'=>$activitytype,
                'isCombination'=>$isCombination?$isCombination:false
            )
        );
        $time=time().'123';
        $sault=$time.$this->sault;
        $token=md5($sault);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));
        $this->_logs(array("GetProductActivityList request data:",$postData));
        $res = $this->https_request($this->GetProductActivity, $postData,$curlset);
        $this->_logs(array("GetProductActivityList result:",$res));
        echo $res;
        exit;
    }

    /**
     * 获取活动列表
     */
    public function getActivityList(){
        $this->setConfigURL();
        $categoryID = $_REQUEST['categoryID'];
        $packageID = $_REQUEST['packageID'];
        $productID = $_REQUEST['productID'];
        $specificationID = $_REQUEST['specificationID'];
        $activityModel = $_REQUEST['activityModel'];
        $activityAgent = $_REQUEST['activityAgent'];

        $postData=json_encode(array(
                'categoryID'=>intval($categoryID),
                'packageID'=>intval($packageID),
                'productID'=>intval($productID),
                'specificationID'=>intval($specificationID),
                'activityModel'=>intval($activityModel),
                'activityAgent'=>intval($activityAgent),
                'activityType'=>1,
                'isCombination'=>true
            )
        );
        $time=time().'123';
        $sault=$time.$this->sault;
        $token=md5($sault);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));
        $this->_logs(array("GetProductActivityList request data:",$postData));
        $res = $this->https_request($this->GetProductActivityList, $postData,$curlset);
        $this->_logs(array("GetProductActivityList result:",$res));
        echo $res;
        exit;
    }

    /**
     * 获取活动详情
     */
    public function GetProductActivityDetail(){
        $this->setConfigURL();
        $categoryID = $_REQUEST['categoryID'];
        $activityAgent = $_REQUEST['activityAgent'];
        $activityID = $_REQUEST['activityID'];

        $postData=json_encode(array(
                "activityID"=>intval($activityID),
            )
        );
        $time=time().'123';
        $sault=$time.$this->sault;
        $token=md5($sault);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));
        $this->_logs(array("GetActivity request data:",$postData));
        $res = $this->https_request($this->GetActivity, $postData,$curlset);
        $this->_logs(array("GetActivity result:",$res));
        echo $res;
        exit;
    }

    /*
    * 获取活动列表
    *
    */
    function getALLNowActivity(){
        $this->setConfigURL();
        $activityModel = $_REQUEST['activityModel'];
        $activityAgent = $_REQUEST['activityAgent'];
        $postData=json_encode(array(
                'activityModel'=>intval($activityModel),
                'activityAgent'=>intval($activityAgent),
                'activityRange'=>2,
            )
        );
        $time=time().'123';
        $sault=$time.$this->sault;
        $token=md5($sault);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));
        $this->_logs(array("ALLActivity request data:",$postData));
        $res = $this->https_request($this->ALLNowActivity, $postData,$curlset);
        echo $res;
        exit;
    }

    /**
     * 计算商品价格
     */
    public function CalculationShoppingCart(){
        $this->setConfigURL();
        $ProductInfo = $_REQUEST['productinfo'];
        $GiftProduct = $_REQUEST['giftproduct'];
        $agentIdentity = $_REQUEST['agentIdentity'];
        $ActivityType = $_REQUEST['activitytype'];
        $ActivityID = $_REQUEST['activityid'];
        $activitychildid = $_REQUEST['activitychildid'];
        $activitymodel = $_REQUEST['activitymodel'];
        $tyunusercodeid = $_REQUEST['tyunusercode'];
        $AssistantProduct = $_REQUEST['assistantproduct'];
        $numberstudentaccounts = $_REQUEST['numberstudentaccounts'];
        $postData=json_encode(array(
                'type'=>intval($activitymodel),
                'userID'=>intval($tyunusercodeid),
                'agentIdentity'=>intval($agentIdentity),
                'discount'=>1,
                'agentType'=>0,
                'ProductInfo'=>array(array(
                    'Type'=>intval($activitymodel),
                    'ProductList'=>$ProductInfo,
                    "ActivityType"=>intval($ActivityType),
                    "ActivityID"=>intval($ActivityID),
                    "ActivityChildID"=>$activitychildid,
                    'GiftProduct'=>$GiftProduct,
                    "AssistantProduct"=>$AssistantProduct
                )),
                "accountCount"=>$numberstudentaccounts
            )
        );
        $time=time().'123';
        $sault=$time.$this->sault;
        $token=md5($sault);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));
        $this->_logs(array("CalculationShoppingCart request data:",$postData));
        $res = $this->https_request($this->CalculationShoppingCart, $postData,$curlset);
        $this->_logs(array("CalculationShoppingCart result:",$res));
        echo $res;
    }

    /**
     * 活动下单
     */
    public function AddOrder2(){
        $this->setConfigURL();
        $ProductInfo = $_REQUEST['productinfo'];
        $GiftProduct = $_REQUEST['giftproduct'];
        $agentIdentity = $_REQUEST['agentIdentity'];
        $ActivityType = $_REQUEST['activitytype'];
        $activitymodel = $_REQUEST['activitymodel'];
        $ActivityID = $_REQUEST['activityid'];
        $activitychildid = $_REQUEST['activitychildid'];
        $otherproduct = $_REQUEST['otherproduct'];

        $servicecontractsid =$_REQUEST['servicecontractsid'];//服务合同id
        $servicecontractsid_display =$_REQUEST['servicecontractsid_display'];//服务合同编号
        $accountid =$_REQUEST['accountid'];//客户id
        $accountid_display =$_REQUEST['accountid_display'];//客户名称
        $mobile =$_REQUEST['mobile'];//手机号
        $mobilevcode =$_REQUEST['mobilevcode'];//验证码
        $productclassonevalues =$_REQUEST['productclassonevalues'];//产品分类
        $productclasstwovalues =$_REQUEST['productclasstwovalues'];//购买套餐
        $buyyear =$_REQUEST['buyyear'];//合同类型购买和续费
        $classtype =$_REQUEST['classtype'];//合同类型购买和续费
        $agents =$_REQUEST['agents'];//代理商id
        $servicetotal=$_REQUEST['servicetotal'];
        $tyunusercode=$_REQUEST['tyunusercode'];
        $tyunusercodeid=$_REQUEST['tyunusercodeid'];
        $tyunusercodename=$_REQUEST['tyunusercodetext'];
        $buydate=empty($_POST['buydate'])?date('Y-m-d H:i:s'):date('Y-m-d',strtotime($_POST['buydate'])).date(' H:i:s');
        $oldcustomerid = $_REQUEST['oldcustomerid'];
        $oldcustomername = $_REQUEST['oldcustomername'];
        $activityid = $_REQUEST['activityid'];

        $activityTitle =$_REQUEST['activitytitle'];
        $combinationprice = $_REQUEST['combinationprice'];
        $activitytypeText =$_REQUEST['activitytypetext'];
        $activityno =$_REQUEST['activityno'];
        $authenticationtype =$_REQUEST['authenticationtype'];
        $chooseuserproduct = $_REQUEST['chooseuserproduct'];
        $signaturetype = $_REQUEST['signaturetype']?$_REQUEST['signaturetype']:'papercontract';
        $AssistantProduct = $_REQUEST['assistantproduct'];
        $returnmsg=array('success'=>0);
        do{
            $checkdata=$this->checkBasicInfo();
            if($checkdata['flag']){
                $returnmsg['msg']=$checkdata['msg'];
                break;
            }
            foreach ($otherproduct as $value){
                $allotherproduct[] = $value;
            }

            if(count($allotherproduct)>0){
                $otherproductarr = array(
                    'type'=>1,
                    'productList'=>$allotherproduct,
                    "activityType"=>intval(0),
                    "activityID"=>intval(0),
                    "activityChildID"=>'',
                    'giftProduct'=>[],
                    "activityTitle"=>'',
                    "combinationPrice"=>0,
                    "meetActivity"=>false,
                    "subtotal"=>0,
                    "canParticipateActivityList"=>array(array(
                        "activityChildID"=>'',
                        "activityID"=>0,
                        "activityTitle"=>'',
                        "selection"=>false
                    ))
                );
            }

            $product =  array(
                'type'=>$activitymodel,
                'productList'=>$ProductInfo,
                "activityType"=>intval($ActivityType),
                "activityID"=>intval($ActivityID),
                "activityChildID"=>$activitychildid,
                'giftProduct'=>$GiftProduct,
                "activityTitle"=>$activityTitle,
                "combinationPrice"=>$combinationprice?$combinationprice:0,
                "meetActivity"=>true,
                "chooseUserProduct"=>$chooseuserproduct,
                "subtotal"=>0,
                "canParticipateActivityList"=>array(array(
                    "activityChildID"=>$activitychildid,
                    "activityID"=>$activityid,
                    "activityTitle"=>$activityTitle,
                    "selection"=>($activitychildid?true:false)
                )),
                'assistantProduct'=>$AssistantProduct
            );

            $resparams=array(
                "authenticationType"=> intval($authenticationtype),
                "cid"=>intval($accountid),
                "crmOrderFlag"=> 1,
                "customerName"=>$accountid_display,
                "invitationCode"=> intval($agentIdentity),
                "agentIdentity"=>intval($agentIdentity),
                "productType"=>$activitymodel,
                "transferFlag"=>0,
                'type'=>1,
                'userID'=>intval($tyunusercodeid),
				"addDate"=>$buydate,
//                'agentIdentity'=>intval($agentIdentity),
//                'discount'=>1,
//                'agentType'=>0,
                'productInfo'=>($otherproductarr?array($product,$otherproductarr):array($product)),
                'contractMoney'=>floatval($servicetotal),
                'contractCode'=>$servicecontractsid_display,
                'operatorName'=>$_SESSION['last_name'],
                'operatorId'=>$this->userid
            );
            $postData=json_encode($resparams);
            $time=time().'123';
            $sault=$time.$this->sault;
            $token=md5($sault);
            $curlset=array(CURLOPT_HTTPHEADER=>array(
                "Content-Type:application/json",
                "S-Request-Token:".$token,
                "S-Request-Time:".$time));
            $this->_logs(array("crmPurchase request data:",$postData));
            $res = $this->https_request($this->crmPurchase, $postData,$curlset);
            $data=json_decode($res,true);
            if($data['code']==200){
                $otherparams= array(
                    "userID"=>$tyunusercode,//用户编号
                    "agentIdentity"=>$agentIdentity,//代理商ID
                    "categoryID"=>$productclassonevalues,//产品分类(0国内版 1一带一路)
                    "buyTerm"=>$buyyear,//购买年限
                    "packageID"=>$productclasstwovalues,//套餐编号
                    "productInfo"=>$ProductInfo,
                    "addDate"=>$buydate,
                    "oldcustomerid"=>$oldcustomerid,
                    "oldcustomername"=>$oldcustomername,
                    "activityid"=>$activityid,
                    "activityname"=>$activityTitle,
                    "activitytype"=>$activitytypeText,
                    "activityno"=>$activityno
                );
                $resparams = array_merge($resparams,$otherparams);
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
                    'tyunurl'=>$this->doOrder,
                    'usercode'=>$tyunusercodename,
                    'contractprice'=>$servicetotal,
                    'usercodeid'=>$tyunusercodeid,
                    'signaturetype'=>$signaturetype
                );

                switch ($activitymodel){
                    case 1:
                        $handleType = 'buy';
                        $url='/index.php?module=TyunWebBuyService&action=index';
                        break;
                    case 2:
                        $handleType = 'upgrade';
                        $url='/index.php?module=TyunWebBuyService&action=upgrade';
                        break;
                    case 4:
                        $handleType = 'renew';
                        $url='/index.php?module=TyunWebBuyService&action=renew';
                        break;
                }

                $this->handleTyunResult($data,$r_params,$resparams,$handleType);
                $returnmsg=array('success'=>1,'msg'=>'下单成功','url'=>$url);
            }else{
                $returnmsg['msg']=$data['message'];
            }
        }while(0);
        echo json_encode($returnmsg);
        exit;
    }


    private function dealOtherProduct($request){
        $producttitle=$request['producttitle'];
        $productid=$request['productid'];
        $categoryid=$request['categoryids'];
        $number=$request['number'];
        $id=$request['id'];
        $price=$request['price'];
        $renewprice=$request['renewprice'];
        $marketprice=$request['marketprice'];
        $marketrenewprice=$request['marketrenewprice'];
        $unit=$request['unit'];
        $specificationstitle=$request['specificationstitle'];
        $buyyear =$request['buyyear'];//合同类型购买和续费
        $othercontractprice  = $request['othercontractprice'];
        $ProductInfo=array();
        foreach($productid as $key=>$value){
            $tempdata = array(
                'categoryID'=>intval($categoryid[$key]),
                'packageID'=>"",
                'productID'=>intval($value),
                'specificationID'=>intval($id[$key]),
                'count'=>intval($number[$key]),
                'buyTerm'=>intval($buyyear),
                "price"=>floatval($price[$key]),
                "renewPrice"=>floatval($renewprice[$key]),
                "marketPrice"=>floatval($marketprice[$key]),
                "marketRenewPrice"=>floatval($marketrenewprice[$key]),
                "activityThresholdBuyTerm"=>0,
                "activityThresholdCount"=>0,
                "activityMarketPrice"=>0,
                "activityPrice"=>0,
                "activityRenewMarketPrice"=>0,
                "activityRenewPrice"=>0,
                "packageTitle"=>'',
                "productTitle"=>$producttitle[$key],
                "specificationNumber"=>'',
                "specificationTitle"=>$specificationstitle[$key],
                "unit"=>$unit[$key],
                "contractMoney"=>$othercontractprice[$key]
            );
            $ProductInfo[]=$tempdata;
        }
        return $ProductInfo;
    }

    function handleRenewProduct($request){
        $packproductid=$request['packproductid'];
        $packcategoryid=$request['packcategoryid'];
        $packnumber=$request['packnumber'];
        $packspecificationsid=$request['packspecificationsid'];
        $packspecificationid=$request['packspecificationid'];
        $packproducttitle=$request['packproducttitle'];
        $packspecificationstitle=$request['packspecificationstitle'];
        $packrenewprice=$request['packrenewprice'];
        $packprice=$request['packprice'];
        $packmarketprice=$request['packmarketprice'];
        $packmarketrenewprice=$request['packmarketrenewprice'];
        //end续费套餐产品

        //续费套餐的信息start
        $packageprice = $request['packageprice'];
        $packagerenewprice = $request['packagerenewprice'];
        $packagemarketprice = $request['packagemarketprice'];
        $packagemarketrenewprice = $request['packagemarketrenewprice'];
        //续费套餐的信息end

        //start续费另购产品
        $otherproduct=$request['otherproductid'];
        $otherpackcategoryid=$request['otherpackcategoryid'];
        $otherpacknumber=$request['otherpacknumber'];
        $otherspecificationsid=$request['otherspecificationsid'];
        $otherspecificationid=$request['otherspecificationid'];
        $otherproducttitle=$request['otherproducttitle'];
        $otherpackspecificationstitle=$request['otherpackspecificationstitle'];
        $otherrenewprice = $request['otherrenewprice'];
        $otherprice = $request['otherprice'];
        $othermarketprice = $request['othermarketprice'];
        $othermarketrenewprice = $request['othermarketrenewprice'];
        //end续费另购产品

        $buyyear =$request['buyyear'];//合同类型购买和续费
        $ispackage=$_REQUEST['ispackage'];//是否套餐续费
        $packageid=$_REQUEST['productclasstwovalues'];//套餐ID

        $categoryid=$_REQUEST['productclassonevalues'];//产品分ID
        $packageid=!empty($packageid)?$packageid:0;
        $packageid=$ispackage==1?$packageid:0;//是否套餐续费,如果不是套餐续费套餐ID为0

        $ProductInfo=array();
        //处理续费start
        if($ispackage==0){
            foreach($packspecificationid as $key=>$value){
                $tempdata=array(
                    'categoryID'=>0,
                    'packageID'=>0,
                    'productID'=>intval($packproductid[$key]),
                    'specificationID'=>intval($value),
                    'count'=>1,
                    'buyTerm'=>intval($buyyear),
                    "price"=>floatval($packprice[$key]),
                    "renewPrice"=>floatval($packrenewprice[$key]),
                    "marketPrice"=>floatval($packmarketprice[$key]),
                    "marketRenewPrice"=>floatval($packmarketrenewprice[$key]),
                    "activityThresholdBuyTerm"=>0,
                    "activityThresholdCount"=>0,
                    "activityMarketPrice"=>0,
                    "activityPrice"=>0,
                    "activityRenewMarketPrice"=>0,
                    "activityRenewPrice"=>0,
                    "packageTitle"=>'',
                    "productTitle"=>$packproducttitle[$key],
                    "specificationNumber"=>'',
                    "specificationTitle"=>$packspecificationstitle[$key],
                    "unit"=>'',
                    'userProductID'=>intval($packspecificationsid[$key]),
                );
                $ProductInfo[]=$tempdata;
            }
        }else{
            $tempdata = array(
                'categoryID'=>intval($categoryid),
                'packageID'=>intval($packageid),
                'productID'=>"",
                'specificationID'=>"",
                'count'=>1,
                'buyTerm'=>intval($buyyear),
                "price"=>floatval($packageprice),
                "renewPrice"=>floatval($packagerenewprice),
                "marketPrice"=>floatval($packagemarketprice),
                "marketRenewPrice"=>floatval($packagemarketrenewprice),
                "activityThresholdBuyTerm"=>0,
                "activityThresholdCount"=>0,
                "activityMarketPrice"=>0,
                "activityPrice"=>0,
                "activityRenewMarketPrice"=>0,
                "activityRenewPrice"=>0,
                "packageTitle"=>'',
                "productTitle"=>"",
                "specificationNumber"=>'',
                "specificationTitle"=>"",
                "unit"=>'',
//                'userProductID'=>intval($packageid),
            );
            $ProductInfo[]=$tempdata;
        }

        foreach($otherspecificationid as $key=>$value){
            $tempdata2=array(
                'categoryID'=>0,
                'packageID'=>0,
                'productID'=>intval($otherproduct[$key]),
                'specificationID'=>intval($value),
                'count'=>1,
                'buyTerm'=>intval($buyyear),
                "price"=>floatval($otherprice[$key]),
                "renewPrice"=>floatval($otherrenewprice[$key]),
                "marketPrice"=>floatval($othermarketprice[$key]),
                "marketRenewPrice"=>floatval($othermarketrenewprice[$key]),
                "activityThresholdBuyTerm"=>0,
                "activityThresholdCount"=>0,
                "activityMarketPrice"=>0,
                "activityPrice"=>0,
                "activityRenewMarketPrice"=>0,
                "activityRenewPrice"=>0,
                "packageTitle"=>'',
                "productTitle"=>$otherproducttitle[$key],
                "specificationNumber"=>'',
                "specificationTitle"=>$otherpackspecificationstitle[$key],
                "unit"=>'',
                'userProductID'=>intval($otherspecificationsid[$key]),
            );
            $ProductInfo[]=$tempdata2;
        }
        return $ProductInfo;
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
     * 续费订单
     */
    public function order(){
        $this->setConfigURL();
        $servicecontractsid =$_REQUEST['servicecontractsid'];//服务合同id
        $servicecontractsid_display =$_REQUEST['servicecontractsid_display'];//服务合同编号
        $accountid =$_REQUEST['accountid'];//客户id
        $accountid_display =$_REQUEST['accountid_display'];//客户名称
        $mobile =$_REQUEST['mobile'];//手机号
        $mobilevcode =$_REQUEST['mobilevcode'];//验证码
        $buyyear =$_REQUEST['buyyear'];//年限
        $classtype =$_REQUEST['classtype'];//合同类型购买和续费
//        $packageid=$_REQUEST['packageid'];//套餐ID
//        $categoryid=$_REQUEST['categoryid'];//产品分ID

        $packageid=$_REQUEST['productclasstwovalues'];//套餐ID
        $categoryid=$_REQUEST['productclassonevalues'];//产品分ID

        $packageid=!empty($packageid)?$packageid:0;
        $iscollegeedition=isset($_REQUEST["iscollegeedition"])?$_REQUEST["iscollegeedition"]:0;

        $oldproductname=$_REQUEST['oldproductname'];
        $servicetotal=$_REQUEST['servicetotal'];
        $unit=$_REQUEST['unit'];
        $specificationstitle=$_REQUEST['specificationstitle'];
        $tyunusercode=$_REQUEST['tyunusercode'];
        $tyunusercodename=$_REQUEST['tyunusercodetext'];
        $agents =$_REQUEST['agents'];
        $oldcustomerid = $_REQUEST['oldcustomerid'];
        $oldcustomername =$_REQUEST['oldcustomername'];
        $buydate=empty($_POST['buydate'])?date('Y-m-d H:i:s'):date('Y-m-d',strtotime($_POST['buydate'])).date(' H:i:s');
        $orderordercode=$_REQUEST['activacode'];

        $authenticationtype = $_REQUEST['authenticationtype'];
        $chooseuserproduct = $_REQUEST['chooseuserproduct']?$_REQUEST['chooseuserproduct']:[];
        $signaturetype = $_REQUEST['signaturetype']?$_REQUEST['signaturetype']:'papercontract';
        $elereceivermobile = $_REQUEST['elereceivermobile'];
        $elereceiver = $_REQUEST['elereceiver'];
        $authenticationtype = $_REQUEST['authenticationtype'];
        $owncompany = $_REQUEST['owncompany'];
        $totalmarketprice = $_REQUEST['totalmarketprice'];
        $activitymodel = $_REQUEST['activitymodel'];
        $eleccontractid = $_REQUEST['eleccontractid'];

        $returnmsg=array('success'=>0);
        do{
            $checkdata=$this->checkBasicInfo();
            if($checkdata['flag']){
                $returnmsg['msg']=$checkdata['msg'];
                break;
            }
            if(!$this->contractCanOrder()){
                $returnmsg['msg']='合同已被使用 ';
                break;
            }
            //处理套餐start
            switch ($activitymodel){
                case 4:
                    $ispackage=$_REQUEST['ispackage'];//是否套餐续费
                    $packageid=$ispackage==1?$packageid:0;//是否套餐续费,如果不是套餐续费套餐ID为0
                    $ProductInfo = $this->handleRenewProduct($_REQUEST);
                    break;
                case 3:
                case 2:
                    $packageid=$_REQUEST['buyproduct'];
                    $ProductInfo = array(
                        array(
                            'categoryID'=>$categoryid,
                            'packageID'=>$packageid,
                            'productID'=>"",
                            'specificationID'=>"",
                            'count'=>1,
                            'buyTerm'=>$buyyear,
                            "price"=>$_REQUEST['packageprice'],
                            "renewPrice"=>$_REQUEST['packagerenewprice'],
                            "marketPrice"=>$_REQUEST['packagemarketprice'],
                            "marketRenewPrice"=>$_REQUEST['packagemarketrenewprice'],
                            "activityThresholdBuyTerm"=>0,
                            "activityThresholdCount"=>0,
                            "activityMarketPrice"=>0,
                            "activityPrice"=>0,
                            "activityRenewMarketPrice"=>0,
                            "activityRenewPrice"=>0,
                            "packageTitle"=>'',
                            "productTitle"=>"",
                            "specificationNumber"=>'',
                            "specificationTitle"=>"",
                            "unit"=>'',
                        )
                    );
                    break;
                case 1:
                    $packageid = $_REQUEST['productclasstwovalues'];
                    $categoryid = $_REQUEST['productclassonevalues'];
                    if($packageid=='nobuypack'){  //另购的时候不购买套餐
                        $packageid=0;
                        $ProductInfo = array();
                        break;
                    }
                    $ProductInfo = array(
                        array(
                            'categoryID'=>intval($categoryid),
                            'packageID'=>intval($packageid),
                            'productID'=>"",
                            'specificationID'=>"",
                            'count'=>1,
                            'buyTerm'=>intval($buyyear),
                            "price"=>floatval($_REQUEST['packageprice']),
                            "renewPrice"=>floatval($_REQUEST['packagerenewprice']),
                            "marketPrice"=>floatval($_REQUEST['packagemarketprice']),
                            "marketRenewPrice"=>floatval($_REQUEST['packagemarketrenewprice']),
                            "activityThresholdBuyTerm"=>0,
                            "activityThresholdCount"=>0,
                            "activityMarketPrice"=>0,
                            "activityPrice"=>0,
                            "activityRenewMarketPrice"=>0,
                            "activityRenewPrice"=>0,
                            "packageTitle"=>'',
                            "productTitle"=>"",
                            "specificationNumber"=>'',
                            "specificationTitle"=>"",
                            "unit"=>'',
                        )
                    );
                    break;
            }

            if($activitymodel==4 && $ispackage==0){
                if(empty($ProductInfo)){
                    $returnmsg['msg']='没有续费的产品';
                    break;
                }
            }
            //todo
            //降级时候选择的权益产品
            $chooseUserProducts=array();
            if($activitymodel==3){
                $specificationid=$_REQUEST['specificationid'];
                foreach($specificationid as $value){
                    $chooseUserProducts[]=intval($value);
                }
                $chooseuserproduct = $chooseUserProducts;
            }

            $product =  array(
                'type'=>intval($activitymodel),
                'productList'=>$ProductInfo,
                "activityType"=>0,
                "activityID"=>0,
                "activityChildID"=>"",
                'giftProduct'=>[],
                "activityTitle"=>"",
                "combinationPrice"=>0,
                "meetActivity"=>false,
                "subtotal"=>0,
                'chooseUserProduct'=>$chooseuserproduct,
//                'chooseUserProducts'=>$chooseUserProducts,
                "canParticipateActivityList"=>array(array(
                    "activityChildID"=>"",
                    "activityID"=>"",
                    "activityTitle"=>"",
                    "selection"=>false
                ))
            );
            if($activitymodel==1 && count($ProductInfo)<1){
                $product = array();
            }
            //处理套餐end

            //处理另购start
            $otherProductInfo = $this->dealOtherProduct($_REQUEST);
            $otherproductarr = array(
                'type'=>1,
                'productList'=>$otherProductInfo,
                "activityType"=>intval(0),
                "activityID"=>intval(0),
                "activityChildID"=>'',
                'giftProduct'=>[],
                "activityTitle"=>'',
                "combinationPrice"=>0,
                "meetActivity"=>false,
                "subtotal"=>0,
                "canParticipateActivityList"=>array(
                    array(
                    "activityChildID"=>'',
                    "activityID"=>0,
                    "activityTitle"=>'',
                    "selection"=>false
                ))
            );
            //处理另购end
            if(count($otherProductInfo)>0){
                if(count($product)){
                    $productinfos = array($product,$otherproductarr);
                }else{
                    $productinfos = array($otherproductarr);
                }
            }else{
                if(count($product)){
                    $productinfos = array($product);
                }else{
                    $productinfos = array();
                }
            }
            if(!count($productinfos)){
                break;
            }

            $resparams=array(
                "authenticationType"=> intval($authenticationtype),
                "cid"=>intval($accountid),
                "crmOrderFlag"=> 1,
                "customerName"=>$accountid_display,
                "invitationCode"=> intval($agents),
                "agentIdentity"=>intval($agents),
                "productType"=>intval($activitymodel),
                "transferFlag"=>0,
                'type'=>1,
                'userID'=>intval($tyunusercode),
                'productInfo'=>$productinfos,
//                'productInfo'=>(count($otherProductInfo)>0?array($product,$otherproductarr):array($product)),
                'contractMoney'=>floatval($servicetotal),
                'contractCode'=>$servicecontractsid_display,
                'addDate'=>$buydate,
                'operatorName'=>$_SESSION['last_name'],
                'operatorId'=>$this->userid
            );
            // 如果是院校版 或者集团版 会传递此参数
            if(isset($_REQUEST['numberstudentaccounts']) && !empty($_REQUEST['numberstudentaccounts'])){
                $resparams['studentCount']=$_REQUEST['numberstudentaccounts'];
            }

            $this->_logs(array("crmPurchaserequest：", json_encode($resparams)));
            $postData=json_encode($resparams);
            $time=time().'123';
            $sault=$time.$this->sault;
            $token=md5($sault);
            $curlset=array(CURLOPT_HTTPHEADER=>array(
                "Content-Type:application/json",
                "S-Request-Token:".$token,
                "S-Request-Time:".$time),
                CURLINFO_HEADER_OUT=>array(true));
            $res = $this->https_request($this->crmPurchase, $postData,$curlset);
            $this->_logs(array("crmPurchasereturndata ：",$res));
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
                    'userid'		=> $this->userid,
                    'res'           =>$res,
                    'customer_name'=>$_SESSION['customer_name'],
                    'phone_mobile'=>$_SESSION['phone_mobile'],
                    'tyunurl'=>$this->renewDoOrder,
                    'contractprice'=>$servicetotal,
                    'usercodeid'=>$tyunusercode,
                    'usercode'=>$tyunusercodename,
                    "agentIdentity"=>$agents,//代理商ID
                    "oldcustomerid"=>$oldcustomerid,
                    "oldcustomername"=>$oldcustomername,

                    'oldproductname'=>$oldproductname,
                    'orderordercode'=>$orderordercode,
                    'signaturetype'=>$signaturetype
                );

                switch ($activitymodel){
                    case 1:
                        $handleType = 'buy';
                        $url='/index.php?module=TyunWebBuyService&action=index';
                        $msg = '下单成功';
                        break;
                    case 2:
                        $handleType = 'upgrade';
                        $url='/index.php?module=TyunWebBuyService&action=upgrade';
                        $msg = '升级成功';
                        break;
                    case 3:
                        $handleType = 'degrade';
                        $url='/index.php?module=TyunWebBuyService&action=degrade';
                        $msg = '降级成功';
                        break;
                    case 4:
                        $handleType = 'renew';
                        $url='/index.php?module=TyunWebBuyService&action=renew';
                        $msg = '续费成功';
                        break;
                }
                $r_params['iscollegeedition']=$iscollegeedition;
                $this->handleTyunResult($data,$r_params,$resparams,$handleType);
                $returnmsg=array('success'=>1,'msg'=>$msg,'url'=>$url);
            }else{
                $returnmsg['msg']=$data['message'];
                break;
            }
        }while(0);
        echo json_encode($returnmsg);
        exit;
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
            $rate = bcdiv(strval($servicetotal),strval($totalmarketprice),2);
            $maxRate = bccomp($rate,'0.95',2);
            $midRate = bccomp($rate,'0.92',2);
            $minRate = bccomp($rate,'0.90',2);
            if(in_array($maxRate,array(-1,0)) && $midRate==1){
                $verifyLevel=1;
            }elseif (in_array($midRate,array(-1,0)) && $minRate==1){
                $verifyLevel=2;
            }elseif (in_array($minRate,array(-1,0))){
                $verifyLevel=3;
            }

            if(in_array($maxRate,array(-1,0))){
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
            $returnmsg=array('success'=>0,'msg'=>'获取电子合同token异常,请重试');
            echo json_encode($returnmsg);
            exit();
        }
        global $fangxinqianview;
        $postData = array(
            'contractId'=>$contractId
        );
        $curlset=array(CURLOPT_HTTPHEADER=>array(
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
        if($resparams['classtype']=='upgrade' && $resparams['activityid']){
            $packageid = $_REQUEST['packageid'];
        }

        switch ($resparams['authenticationtype']){
            case 0:
                $clientproperty = "personal";
                break;
            case 1:
                $clientproperty = "enterprise";
                break;
            case 2:
                $clientproperty = "government";
                break;
            case 3:
                $clientproperty = "otherorg";
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
                'clientproperty'=>$clientproperty,//客户属性personal个人,enterprise企业
                'totalprice'=>$resparams['servicetotal'],

                'signaturetype'=>'eleccontract',//签署类型papercontract纸质合同,eleccontract电子合同
                'elereceivermobile'=>$resparams['elereceivermobile'],//接收人手机号
                'elereceiver'=>$resparams['elereceiver'],//接收人
                'eleccontractid'=>$resparams['contractid'],//放心签平台生成的合同id
                'comeformtyun'=>1,
                'fromactivity'=>($resparams['activityid']?1:0),
                "modulestatus"=>'a_normal',
//                "modulestatus"=>($resparams['isverify']?'b_check':'a_normal'),
                "eleccontractstatus"=>'',
//                "eleccontractstatus"=>($resparams['isverify']?'a_elec_sending':'b_elec_actioning'),
                "activityenddate"=>$resparams['activityenddate'], //活动到期时间
                "parent_contracttypeid"=>10,

                'eleccontracttplid'=>$resparams['templateid'], //放心签平台合同模板id
                "eleccontracttpl"=>$eleccontracttpl,//合同模板名称
                "relatedattachmentid"=>rtrim($relatedattachmentid,','),//关联附件ID列表
                "relatedattachment"=>rtrim($relatedattachment,','),//关联附件
                "originator"=>$_SESSION['last_name'],
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
     * 匹配电子合同模板
     */
    public function matchElecContractTemplate(){
//        $returnmsg=array('success'=>1,'msg'=>'','data'=>array(array('templateId'=>7)));
//        echo json_encode($returnmsg);
//        exit();
        $token=$this->getFangXinQianToken();
        if(!$token){
            $returnmsg=array('success'=>0,'msg'=>'获取电子合同token异常,请重试');
            echo json_encode($returnmsg);
            exit();
        }

        $servicecontractstype = $_REQUEST['servicecontractstype'];
        $orderType = $_REQUEST['orderType'];
        switch ($orderType){
            case 'activity':
                $orderType=1;
                break;
            default:
                $orderType = 0;
                break;
        }
        $isPackage = $_REQUEST['isPackage'];
        $productCode = $_REQUEST['productCode'];
        if($isPackage){
            $productCodes = array_map(function($a){return 'c'.$a;},$productCode);
        }else{
            $productCodes = array_map(function($a){return 's'.$a;},$productCode);
        }

        $returnmsg=array('success'=>0,'msg'=>'无可匹配合同模板');

        global $fangxinqian_get_templates;
        $postData=array(
            "productCode"=>$productCodes?$productCodes:array(),
            "purchaseType"=>intval($servicecontractstype),
            "orderType"=>$orderType
        );
        $token = $this->getFangXinQianToken();
        $formId = $this->getFangXinQianFormId();
        $curlset=array(
            CURLOPT_HTTPHEADER=>array(
                "Content-Type:application/json",
                "token:".$token,
                "formId:".$formId,
            )
        );
        $this->_logs(array('fangxinqian_get_templates_request',$postData));
        $result = $this->https_request($fangxinqian_get_templates, json_encode($postData),$curlset);
        $res = json_decode($result,true);
        if(!$res['success']){
            $returnmsg['msg'] = $res['msg'];
            echo json_encode($returnmsg);
            exit();
        }
        $returnmsg=array('success'=>1,'msg'=>'','data'=>$res['data']);
        echo json_encode($returnmsg);
    }

    /**
     * 获取客户联系人
     */
    public function accountLink(){
        $accountid = $_REQUEST['accountid'];
        $returnmsg=array('success'=>0,'msg'=>'无可用客户联系人');
        if($accountid<1){
            echo json_encode($returnmsg);
            exit();
        }
        $params = array(
            'fieldname' => array(
                'module' => 'Accounts',
                'action' => 'accountList',
                'accountid'=>$accountid,
            ),
            'userid' => $this->userid
        );
        $list = $this->call('getComRecordModule', $params);
        if(!$list[0]){
            echo json_encode($returnmsg);
            exit();
        }
        $returnmsg=array('success'=>1,'msg'=>'','data'=>$list[0]);
        echo json_encode($returnmsg);
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
            if($order['detailProducts'][0]['ProductTypeTitle']=='套餐' && !$order['detailProducts'][0]['IsGift']){
                $packageterm =$order['detailProducts'][0]['BuyTerm'];
                $packagetotalprice = $order['detailProducts'][0]['PayMoney'];
                continue;
            }
//            $this->_logs(array('myorderdetail',$order['detail']['OrderDetail']));
//            $this->_logs(array('myorderdetail',json_decode($order['detail']['OrderDetail']))[]);
            $orderdetail = $order['detail']['OrderDetail'];
            $orderdetail = json_decode($orderdetail,true);
            foreach ($order['detailProducts'] as $key=>$orderDetailProduct){
                if($orderDetailProduct['ProductTypeTitle']=='单品') {
                    if($order['detail']['ProductType']==4){
                        //续费非套餐权益
                        $canRenew = $orderdetail['renewproducts'][$key]['Product']['CanRenew'];
                        $renewCount = $orderDetailProduct['Count'];
                        $buyCount = '/';
                    }else{
                        //另购单品
                        $canRenew = $orderdetail['products'][$key]['Product']['CanRenew'];
                        $renewCount = '/';
                        $buyCount = $orderDetailProduct['Count'];
                    }

                    $this->_logs(array($order['detail']['ProductTitle'],$canRenew));
                    if ($canRenew) {
                        $otherDataYear[] = array(
                            str_replace("<br>", " ", $orderDetailProduct['ProductTitle'].($orderDetailProduct['SpecificationTitle']?$orderDetailProduct['SpecificationTitle']:'')),
//                            str_replace("<br>", " ", $order['detail']['ProductTitle']),
                            '年限类',
                            $renewCount,
                            $buyCount,
                            strval(round($orderDetailProduct['PayMoney']/($orderDetailProduct['Count']*$orderDetailProduct['BuyTerm']),2)),
                            $orderDetailProduct['BuyTerm'],
                            strval(round($orderDetailProduct['PayMoney'],2)),
                        );
                    } else {
                        $otherDataNoYear[] = array(
//                            str_replace("<br>", " ",$order['detail']['ProductTitle']),
                            str_replace("<br>", " ", $orderDetailProduct['ProductTitle'].($orderDetailProduct['SpecificationTitle']?$orderDetailProduct['SpecificationTitle']:'')),
                            '非年限类',
                            $orderDetailProduct['Count'],
                            strval(round($orderDetailProduct['PayMoney']/($orderDetailProduct['Count']),2)),
                            strval(round($orderDetailProduct['PayMoney'],2))
                        );
                    }
                }
                if($orderDetailProduct['ProductTypeTitle']=='增值服务') {
                    if($order['detail']['ProductType']==4){
                        //另购单品
                        $canRenew2 = $orderdetail['renewproducts'][$key]['Product']['CanRenew'];
                        $renewCount = $orderDetailProduct['Count'];
                        $buyCount = '/';
                    }else{
                        //另购单品
                        $canRenew2 = $orderdetail['products'][$key]['Product']['CanRenew'];
                        $renewCount = '/';
                        $buyCount = $orderDetailProduct['Count'];
                    }
                    if ($canRenew2) {
                        $otherDataYear[] = array(
                            str_replace("<br>", " ", $orderDetailProduct['ProductTitle'].($orderDetailProduct['SpecificationTitle']?$orderDetailProduct['SpecificationTitle']:'')),
                            '年限类',
                            $renewCount,
                            $buyCount,
                            strval(round($orderDetailProduct['PayMoney']/($orderDetailProduct['Count']*$orderDetailProduct['BuyTerm']),2)),
                            $orderDetailProduct['BuyTerm'],
                            strval(round($orderDetailProduct['PayMoney'],2)),
                        );
//                        $otherIncrementDataTotalPrice += $orderDetailProduct['PayMoney'];

                    } else {
                        $otherIncrementDataNoYear[] = array(
                            str_replace("<br>", " ", $orderDetailProduct['ProductTitle'].($orderDetailProduct['SpecificationTitle']?$orderDetailProduct['SpecificationTitle']:'')),
                            '非年限类',
                            $orderDetailProduct['Count'],
                            strval(round($orderDetailProduct['PayMoney']/($orderDetailProduct['Count']),2)),
                            strval(round($orderDetailProduct['PayMoney'],2))
                        );
                        $otherIncrementDataTotalPrice += $orderDetailProduct['PayMoney'];
                    }
                }
            }
        }

        $totalpricetochina = $this->toChinaMoney($totalPrice);

        $dynamicRows = array(
             array(
                "tableIndex"=>0,
                "rowIndex"=>0,//当前表格第几行后面加内容 0.表示表格第一行
                "fontSize"=>10, //字体大小
                "color"=>"000000",
                "rows"=>$otherDataYear?$otherDataYear:[]
            ),
             array(
                "tableIndex"=>0,
                "rowIndex"=>0,//当前表格第几行后面加内容 0.表示表格第一行
                "fontSize"=>10, //字体大小
                "color"=>"000000",
                "rows"=>$otherDataNoYear?$otherDataNoYear:[]
            ),
            array(
                "tableIndex"=>0,
                "rowIndex"=>0,//当前表格第几行后面加内容 0.表示表格第一行
                "fontSize"=>10, //字体大小
                "color"=>"000000",
                "rows"=>$otherIncrementDataNoYear?$otherIncrementDataNoYear:[]
            ),
//	        array(
//                "tableIndex"=>0,
//                "rowIndex"=>0,//当前表格第几行后面加内容 0.表示表格第一行
//                "fontSize"=>10, //字体大小
//                "color"=>"000000",
//                "rows"=>$otherIncrementDataYear?$otherIncrementDataYear:[]
//            )
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
     * 电子合同 生成预览
     */
    public function elecontractPreview($requestData,$data){
        global $fangxinqian_new_contract;
        $token=$this->getFangXinQianToken();
        if(!$token){
            $returnmsg=array('success'=>0,'msg'=>'获取电子合同token异常,请重试');
            return $returnmsg;
        }
        $companyInfo = $this->getMainPartInfo($requestData['companyid']);
        if(empty($companyInfo) || !count($companyInfo)){
            $returnmsg=array('success'=>0,'msg'=>'获取主体公司信息失败,请重试');
            return $returnmsg;
        }
        $orderType = $_REQUEST['orderType'];
        switch ($orderType){
            case 'common':
                $orderType=0;
                break;
            case 'activity':
                $orderType=1;
                break;
        }
        $isPackage = $_REQUEST['isPackage'];
        $productCode = $_REQUEST['productCode'];
        $productCodes = array();
        if($isPackage){
            if($requestData['classtype']!='renew'){
                $productCodes = array_map(function($a){return 'c'.$a;},$productCode);
            }
        }else{
            $productCodes = array_map(function($a){return 's'.$a;},$productCode);
        }
        $productId = $_REQUEST['productid'];
        if($productId){
            $productCodes2 = array_map(function($a){return 's'.$a;},$productId);
            $productCodes = array_merge($productCodes,$productCodes2);
        }

        $productinfoid = $_REQUEST['productinfoid'];
        if($productinfoid){
            $productCodes3 = array_map(function($a){return 's'.$a;},$productinfoid);
            $productCodes = array_merge($productCodes,$productCodes3);
        }
        $this->_logs(array('preorder5',time()));
        $accountInfo = $this->getAccountInfo($requestData['accountid']);
        $this->_logs(array('preorder6',time()));
        $makeData = $this->makeElecContractData($data);
        $this->_logs(array('preorder7',time()));
        $unit_price = round($makeData['packagetotalprice']/$makeData['packageterm'],2);
        switch ($requestData['classtype']){
            case 'buy':
                $classtype='新增';
                break;
            case 'degrade':
                $classtype='降级';
                break;
            case 'renew':
                $classtype='续费';
                break;
            case 'upgrade':
                $classtype='升级';
                break;
        }
        $postData = array(
            //是否需要审核  0.不需要 1.需要
            "needAudit"=>0,
            //合同发起人信息(商务人员)
            "sender"=>array(
                "name"=>$_SESSION['last_name'],
                "phone"=>$_SESSION['phone_mobile']
            ),
            //接收方信息
            "receiver"=>array(
                "name"=>$requestData['elereceiver'],
                "phone"=>$requestData['elereceivermobile'],
                "type"=>$this->getAuthType($requestData['authenticationtype'])  //0.企业 1.个人
            ),
            "companyCode"=>$companyInfo['company_codeno'], //商务所属分公司编号
//            "templateId"=>3, //合同模板id
            "templateId"=>intval($requestData['templateid']), //合同模板id
            "productCode"=>$productCodes, //产品编码
            "orderType"=>$orderType,              //0普通产品 1活动产品
            "expirationTime"=>"2020-12-12", //合同过期时间
            //关键字替换的字段
            "replaces"=>array(
                "address"=>$companyInfo['address'],
                "company"=>$companyInfo['companyfullname'],
                "name"=>$_SESSION['last_name'],
                "bank"=>$companyInfo['bank_account'],
                "banknumber"=>$companyInfo['numbered_accounts'],
                "phone"=>$companyInfo['telphone'],
                "fax"=>$companyInfo['tax'],
                "email"=>$companyInfo['email'],
                "taxnumber"=>$companyInfo['taxnumber'],

                "firstaddress"=>implode('',explode('#',$accountInfo['address'])),
                "firstcompany"=>$accountInfo['accountname'],
                "firstname"=>$requestData['elereceiver'],
                "firstbank"=>$accountInfo['bank_account'],
                "firstbanknumber"=>$accountInfo['numbered_accounts'],
                "firstphone"=>$requestData['elereceivermobile'],
                "firstfax"=>$accountInfo['fax']?$accountInfo['fax']:' ',
                "firstemail"=>$accountInfo['email1']?$accountInfo['email1']:' ',
                "firsttaxnumber"=>$accountInfo['taxpayers_no']?$accountInfo['taxpayers_no'] :' ',

                'oldproduct'=>$requestData['oldproductname'] ?$requestData['oldproductname'] :'/',  //原版本,
                'product'=>$isPackage?$requestData['packagename']:'/',  //套餐名
                'oldcontractnum'=>$requestData['oldcontractcode_display']?$requestData['oldcontractcode_display']:"/",//原合同编号
                "year"=>$isPackage?$makeData['packageterm']:'/',  //套餐年限
                'ctariff'=>$isPackage?round($makeData['packagetotalprice'],2):'/', //套餐总价

                'unit'=>$isPackage?$unit_price:'/',
                'totaltariff'=>round($makeData['totalprice'],2), //合同总价
                "chinatotaltariff"=>$makeData['totalpricetochina'], //合同总价大写
                'othertotaltariff'=>round($makeData['otherproducttotalprice'],2),//另购产品总价
                'incrementtotaltariff'=>round($makeData['otherincrementtotalprice'],2),//增值产品总价
                'signdate'=>date("Y年m月d日"),
                'firstsigndate'=>date("Y年m月d日"),
                'classtype'=>$classtype,
                'tyunusercode'=>$requestData['tyunusercodetext']
            ),
            "dynamicRows"=>(count($makeData['dynamicrows'])==1 ? array($makeData['dynamicrows']):$makeData['dynamicrows'])
        );

       $formId = $this->getFangXinQianFormId();
        $this->_logs(array("fangxinqian_new_contract：", json_encode($postData)));
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "formId:".$formId,
            "token:".$token)
        );
        $this->_logs(array('preorder8',time()));
        $result = $this->https_request($fangxinqian_new_contract, json_encode($postData),$curlset);
        $res = json_decode($result,true);
        if($res['success']){
            $this->_logs(array('preorder9',time()));
            return array('success'=>1,'msg'=>'','data'=>$res['data']);
        }
        return array('success'=>0,'msg'=>$res['msg']);
    }

    public function getPDFView(){
        $token=$this->getFangXinQianToken();
        if(!$token){
            $returnmsg=array('success'=>0,'msg'=>'获取电子合同token异常,请重试');
            echo json_encode($returnmsg);
            exit();
        }
        global $fangxinqianview;
        $postData = array(
            'contractId'=>$_REQUEST['contractId']
        );
        $curlset=array(CURLOPT_HTTPHEADER=>array(
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
        $result =  $this->https_request($fileurl,'',$curlset,false);
        echo $result;
    }

    public function sendElecContract($sendEmailParams){
        $token=$this->getFangXinQianToken();
        if(!$token){
            return array('success'=>0,'msg'=>'获取电子合同token异常,请重试','data'=>'');
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

            //修改合同状态为已发送状态
            $params = array(
                'field'=>array(
                    'module' => 'ServiceContracts',
                    'action' => 'updateMobileContractStatus',
                    'servicecontractsid'=>$sendEmailParams['servicecontractsid'],
                    'eleccontractstatus'=>'b_elec_actioning',
                    'modulestatus'=>'已发放',
                    'userid'=>$this->userid
                ),
                'userid'=>$this->userid
            );
            $this->call('getComRecordModule', $params);

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
            $field2 = array(
                'module' => 'TyunWebBuyService',
                'action' => 'elecContractStatusSendMail2',
            );
            $fieldName2 = array_merge($field2,$sendEmailParams);
            $params2 = array(
                'fieldname' => $fieldName2,
                'userid' => $this->userid
            );
            $this->_logs(array('params',$params2));
            $this->call('getComRecordModule', $params2);
            return array('success'=>true,'msg'=>'','data'=>$res);
        }
        return array('success'=>false,'msg'=>$res['msg'],'data'=>'');
    }

    /**
     * 电子合同预下单接口
     *
     * 1、T云先生成不含合同编号的订单
     * 2、放心签平台生成预览合同
     */
    public function preOrder(){
        $this->_logs(array('preorder1',time()));
        $this->setConfigURL();
        $servicecontractsid =$_REQUEST['servicecontractsid'];//服务合同id
        $servicecontractsid_display =$_REQUEST['servicecontractsid_display'];//服务合同编号
        $accountid =$_REQUEST['accountid'];//客户id
        $accountid_display =$_REQUEST['accountid_display'];//客户名称
        $mobile =$_REQUEST['mobile'];//手机号
        $mobilevcode =$_REQUEST['mobilevcode'];//验证码
        $buyyear =$_REQUEST['buyyear'];//年限
        $classtype =$_REQUEST['classtype'];//合同类型购买和续费
//        $packageid=$_REQUEST['packageid'];//套餐ID
//        $categoryid=$_REQUEST['categoryid'];//产品分ID

        $packageid=$_REQUEST['productclasstwovalues'];//套餐ID
        $categoryid=$_REQUEST['productclassonevalues'];//产品分ID

        $packageid=!empty($packageid)?$packageid:0;


        $oldproductname=$_REQUEST['oldproductname'];
        $servicetotal=$_REQUEST['servicetotal'];
        $unit=$_REQUEST['unit'];
        $specificationstitle=$_REQUEST['specificationstitle'];
        $tyunusercode=$_REQUEST['tyunusercode'];
        $tyunusercodename=$_REQUEST['tyunusercodetext'];
        $agents =$_REQUEST['agents'];
        $oldcustomerid = $_REQUEST['oldcustomerid'];
        $oldcustomername =$_REQUEST['oldcustomername'];
        $buydate=empty($_POST['buydate'])?date('Y-m-d H:i:s'):date('Y-m-d',strtotime($_POST['buydate'])).date(' H:i:s');
        $orderordercode=$_REQUEST['activacode'];

        $chooseuserproduct = $_REQUEST['chooseuserproduct']?$_REQUEST['chooseuserproduct']:[];
        $signaturetype = $_REQUEST['signaturetype'];
        $elereceivermobile = $_REQUEST['elereceivermobile'];
        $elereceiver = $_REQUEST['elereceiver'];
        $authenticationtype = $_REQUEST['authenticationtype'];
        $invoicecompany = $_REQUEST['invoicecompany'];
        $totalmarketprice = $_REQUEST['totalmarketprice'];
        $activitymodel = $_REQUEST['activitymodel'];
        $eleccontractid = $_REQUEST['eleccontractid'];
        $templateid =$_REQUEST['templateid'];

        $returnmsg=array('success'=>0);
        do{
            $checkdata=$this->checkBasicInfo();
            if($checkdata['flag']){
                $returnmsg['msg']=$checkdata['msg'];
                break;
            }
            //处理套餐start
            switch ($activitymodel){
                case 4:
                    $ispackage=$_REQUEST['ispackage'];//是否套餐续费
                    $packageid=$ispackage==1?$packageid:0;//是否套餐续费,如果不是套餐续费套餐ID为0
                    $ProductInfo = $this->handleRenewProduct($_REQUEST);
                    break;
                case 3:
                case 2:
                    $packageid=$_REQUEST['buyproduct'];
                    $ProductInfo = array(
                        array(
                            'categoryID'=>$categoryid,
                            'packageID'=>$packageid,
                            'productID'=>"",
                            'specificationID'=>"",
                            'count'=>1,
                            'buyTerm'=>$buyyear,
                            "price"=>$_REQUEST['packageprice'],
                            "renewPrice"=>$_REQUEST['packagerenewprice'],
                            "marketPrice"=>$_REQUEST['packagemarketprice'],
                            "marketRenewPrice"=>$_REQUEST['packagemarketrenewprice'],
                            "activityThresholdBuyTerm"=>0,
                            "activityThresholdCount"=>0,
                            "activityMarketPrice"=>0,
                            "activityPrice"=>0,
                            "activityRenewMarketPrice"=>0,
                            "activityRenewPrice"=>0,
                            "packageTitle"=>'',
                            "productTitle"=>"",
                            "specificationNumber"=>'',
                            "specificationTitle"=>"",
                            "unit"=>'',
                        )
                    );
                    break;
                case 1:
                    $packageid = $_REQUEST['productclasstwovalues'];
                    $categoryid = $_REQUEST['productclassonevalues'];
                    if($packageid=='nobuypack'){  //另购的时候不购买套餐
                        $packageid=0;
                        $ProductInfo = array();
                        break;
                    }
                    $ProductInfo = array(
                        array(
                            'categoryID'=>intval($categoryid),
                            'packageID'=>intval($packageid),
                            'productID'=>"",
                            'specificationID'=>"",
                            'count'=>1,
                            'buyTerm'=>intval($buyyear),
                            "price"=>floatval($_REQUEST['packageprice']),
                            "renewPrice"=>floatval($_REQUEST['packagerenewprice']),
                            "marketPrice"=>floatval($_REQUEST['packagemarketprice']),
                            "marketRenewPrice"=>floatval($_REQUEST['packagemarketrenewprice']),
                            "activityThresholdBuyTerm"=>0,
                            "activityThresholdCount"=>0,
                            "activityMarketPrice"=>0,
                            "activityPrice"=>0,
                            "activityRenewMarketPrice"=>0,
                            "activityRenewPrice"=>0,
                            "packageTitle"=>'',
                            "productTitle"=>"",
                            "specificationNumber"=>'',
                            "specificationTitle"=>"",
                            "unit"=>'',
                        )
                    );
                    break;
            }

            if($activitymodel==4 && $ispackage==0){
                if(empty($ProductInfo)){
                    $returnmsg['msg']='没有续费的产品';
                    break;
                }
            }
            //todo
            //降级时候选择的权益产品
            $chooseUserProducts=array();
            if($activitymodel==3){
                $specificationid=$_REQUEST['specificationid'];
                foreach($specificationid as $value){
                    $chooseUserProducts[]=intval($value);
                }
                $chooseuserproduct = $chooseUserProducts;
            }

            $product =  array(
                'type'=>intval($activitymodel),
                'productList'=>$ProductInfo,
                "activityType"=>0,
                "activityID"=>0,
                "activityChildID"=>"",
                'giftProduct'=>[],
                "activityTitle"=>"",
                "combinationPrice"=>0,
                "meetActivity"=>false,
                "subtotal"=>0,
                'chooseUserProduct'=>$chooseuserproduct,
//                'chooseUserProducts'=>$chooseUserProducts,
                "canParticipateActivityList"=>array(array(
                    "activityChildID"=>"",
                    "activityID"=>"",
                    "activityTitle"=>"",
                    "selection"=>false
                ))
            );
            if($activitymodel==1 && count($ProductInfo)<1){
                $product = array();
            }
            //处理套餐end

            //处理另购start
            $otherProductInfo = $this->dealOtherProduct($_REQUEST);
            $otherproductarr = array(
                'type'=>1,
                'productList'=>$otherProductInfo,
                "activityType"=>intval(0),
                "activityID"=>intval(0),
                "activityChildID"=>'',
                'giftProduct'=>[],
                "activityTitle"=>'',
                "combinationPrice"=>0,
                "meetActivity"=>false,
                "subtotal"=>0,
                "canParticipateActivityList"=>array(
                    array(
                        "activityChildID"=>'',
                        "activityID"=>0,
                        "activityTitle"=>'',
                        "selection"=>false
                    ))
            );
            //处理另购end
            if(count($otherProductInfo)>0){
                if(count($product)){
                    $productinfos = array($product,$otherproductarr);
                }else{
                    $productinfos = array($otherproductarr);
                }
            }else{
                if(count($product)){
                    $productinfos = array($product);
                }else{
                    $productinfos = array();
                }
            }
            if(!count($productinfos)){
                break;
            }

            $resparams=array(
                "authenticationType"=> intval($authenticationtype),
                "cid"=>intval($accountid),
                "crmOrderFlag"=> 1,
                "customerName"=>$accountid_display,
                "invitationCode"=> intval($agents),
                "agentIdentity"=>intval($agents),
                "productType"=>intval($activitymodel),
                "transferFlag"=>0,
                'type'=>1,
                'userID'=>intval($tyunusercode),
                'productInfo'=>$productinfos,
//                'productInfo'=>(count($otherProductInfo)>0?array($product,$otherproductarr):array($product)),
                'contractMoney'=>floatval($servicetotal),
                'contractCode'=>$servicecontractsid_display,
                'electricContract'=>1,
                'addDate'=>$buydate,
                'operatorName'=>$_SESSION['last_name'],
                'operatorId'=>$this->userid
            );

            $this->_logs(array("crmPurchaserequest：", json_encode($resparams)));
            $postData=json_encode($resparams);
            $time=time().'123';
            $sault=$time.$this->sault;
            $token=md5($sault);
            $curlset=array(CURLOPT_HTTPHEADER=>array(
                "Content-Type:application/json",
                "S-Request-Token:".$token,
                "S-Request-Time:".$time),
                CURLINFO_HEADER_OUT=>array(true));
            $this->_logs(array('preorder2',time()));
            $res = $this->https_request($this->crmPurchase, $postData,$curlset);
            $this->_logs(array('preorder3',time()));
            $this->_logs(array("crmPurchasereturndata ：",$res));
            $data=json_decode($res,true);
            if($data['code']==200){
                $returnmsg = $this->elecontractPreview($_REQUEST,$data);
                $this->_logs(array('preorder10',time()));
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
        $accountid =$_REQUEST['accountid'];//客户id
        $accountid_display =$_REQUEST['accountid_display'];//客户名称
        $mobile =$_REQUEST['mobile'];//手机号
        $mobilevcode =$_REQUEST['mobilevcode'];//验证码
        $buyyear =$_REQUEST['buyyear'];//年限
        $classtype =$_REQUEST['classtype'];//合同类型购买和续费

        $packageid=$_REQUEST['productclasstwovalues'];//套餐ID
        $categoryid=$_REQUEST['productclassonevalues'];//产品分ID
        $packageid=!empty($packageid)?$packageid:0;

        $oldproductname=$_REQUEST['oldproductname'];
        $servicetotal=$_REQUEST['servicetotal'];
        $tyunusercode=$_REQUEST['tyunusercode'];
        $tyunusercodename=$_REQUEST['tyunusercodetext'];
        $agents =$_REQUEST['agents'];
        $oldcustomerid = $_REQUEST['oldcustomerid'];
        $oldcustomername =$_REQUEST['oldcustomername'];
        $orderordercode=$_REQUEST['activacode'];

        $chooseuserproduct = $_REQUEST['chooseuserproduct']?$_REQUEST['chooseuserproduct']:[];
        $signaturetype = $_REQUEST['signaturetype'];
        $authenticationtype = $_REQUEST['authenticationtype'];
        $totalmarketprice = $_REQUEST['totalmarketprice'];
        $activitymodel = $_REQUEST['activitymodel'];
        $eleccontractid = $_REQUEST['contractid'];
        $paycode = $_REQUEST['paycode'];
        $invoicecompany = $_REQUEST['invoicecompany'];
        $invoicecompanyid = $_REQUEST['invoicecompanyid'];
        $authtype = $_REQUEST['authtype'];
        $elereceivermobile = $_REQUEST['elereceivermobile'];
        $elereceiver = $_REQUEST['elereceiver'];
        $activityid = $_REQUEST['activityid'];


        $ActivityType = $_REQUEST['activitytype'];
        $activitymodel = $_REQUEST['activitymodel'];
        $ActivityID = $_REQUEST['activityid'];
        $activitychildid = $_REQUEST['activitychildid'];
        $otherproduct = $_REQUEST['otherproduct'];

        $productclassonevalues =$_REQUEST['productclassonevalues'];//产品分类
        $productclasstwovalues =$_REQUEST['productclasstwovalues'];//购买套餐
        $tyunusercodeid=$_REQUEST['tyunusercodeid'];
        $buydate=empty($_POST['buydate'])?date('Y-m-d H:i:s'):date('Y-m-d',strtotime($_POST['buydate'])).date(' H:i:s');

        $activityTitle =$_REQUEST['activitytitle'];
        $combinationprice = $_REQUEST['combinationprice'];
        $activitytypeText =$_REQUEST['activitytypetext'];
        $activityno =$_REQUEST['activityno'];
        $authenticationtype =$_REQUEST['authenticationtype'];
        $chooseuserproduct = $_REQUEST['chooseuserproduct'];
        $activityenddate = $_REQUEST['activityenddate'];
        $eleccontracturl = $_REQUEST['eleccontracturl'];
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
            echo json_encode(array('success'=>0,'msg'=>'创建电子合同失败，可能原因：所选择产品对应的合同编码不唯一'));
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
        if($data['code']==200 && !empty($data['data'])){
            $r_params = array(
                'servicecontractsid'=>$servicecontractsid,
                'servicecontractsid_display'=>$contractCode,
                'accountid'=>$accountid,
                'accountid_display'=>$accountid_display,
                'mobile'=>$mobile,
                'mobilevcode'=>$mobilevcode,
                'classtype'=>$classtype,
                'userid'		=> $this->userid,
                'res'           =>$res,
                'customer_name'=>$_SESSION['customer_name'],
                'phone_mobile'=>$_SESSION['phone_mobile'],
                'tyunurl'=>$this->renewDoOrder,
                'contractprice'=>$servicetotal,
                'usercodeid'=>$tyunusercode,
                'usercode'=>$tyunusercodename,
                "agentIdentity"=>$agents,//代理商ID
                "oldcustomerid"=>$oldcustomerid,
                "oldcustomername"=>$oldcustomername,
                'oldproductname'=>$oldproductname,
                'orderordercode'=>$orderordercode,
                'signaturetype'=>$signaturetype,
                'elereceivermobile'=>$elereceivermobile,
                'owncompany'=>$invoicecompany,
                'owncompanyid'=>$invoicecompanyid,
                "userID"=>$tyunusercode,//用户编号
                "categoryID"=>$productclassonevalues,//产品分类(0国内版 1一带一路)
                "buyTerm"=>$buyyear,//购买年限
                "packageID"=>$productclasstwovalues,//套餐编号
                "addDate"=>$buydate,
            );
            $other_params = array(
                "activityid"=>$activityid,
                "activityname"=>$activityTitle,
                "activitytype"=>$activitytypeText,
                "activityno"=>$activityno,
                'activityenddate'=>$activityenddate
            );

            switch ($activitymodel){
                case 1:
                    $handleType = 'buy';
                    $url='/index.php?module=TyunWebBuyService&action=index';
                    $msg = '下单成功';
                    break;
                case 2:
                    $handleType = 'upgrade';
                    $url='/index.php?module=TyunWebBuyService&action=upgrade';
                    $msg = '升级成功';
                    break;
                case 3:
                    $handleType = 'degrade';
                    $url='/index.php?module=TyunWebBuyService&action=degrade';
                    $msg = '降级成功';
                    break;
                case 4:
                    $handleType = 'renew';
                    $url='/index.php?module=TyunWebBuyService&action=renew';
                    $msg = '续费成功';
                    break;
            }
            $data['payCode'] = $paycode;
            $data['totalPrice'] = $servicetotal;

            $this->handleTyunResult(array('data'=>$data),$r_params,$other_params,$handleType);
            $returnmsg=array('success'=>1,'msg'=>$msg,'url'=>$url);

            $sendEmailParams = array(
                'contract_no'=>$contractCode,
                'userid'=>$this->userid,
                'customer_name'=>$accountid_display,
                'receivedate'=>date('Y-m-d H:i:s'),
                'comeformtyun'=>1,
                'fromactivity'=>($activityid?1:0),
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
                    $params = array(
                        'fieldname' => array(
                            'module' => 'ServiceContracts',
                            'action' => 'updateElecContractStatus',
                            'eleccontractstatus'=>'a_elec_actioning_fail',
                            "recordid"=>$servicecontractsid,
                            "modulestatus"=>'已发放',
                            'userid'=>$this->userid
                        ),
                        'userid'=>$this->userid
                    );
                    $list = $this->call('getComRecordModule', $params);
                    $returnmsg=array('success'=>1,'msg'=>'电子合同发送失败,请在ERP中手动发送电子合同','url'=>$url);
                }
            }else{
                //保存放心签的待签订件
                $this->saveElecContractPdf($eleccontracturl,'files_style6','-合同审核件',$contractCode,$servicecontractsid);
            }
            echo json_encode($returnmsg);
            exit();
        }
        $returnmsg=array('success'=>0,'msg'=>'绑定合同失败,请联系管理员');
        echo json_encode($returnmsg);

    }

    /**
     * 活动下单
     */
    public function preAddOrder2(){
        $this->setConfigURL();
        $ProductInfo = $_REQUEST['productinfo'];
        $GiftProduct = $_REQUEST['giftproduct'];
        $agentIdentity = $_REQUEST['agentIdentity'];
        $ActivityType = $_REQUEST['activitytype'];
        $activitymodel = $_REQUEST['activitymodel'];
        $ActivityID = $_REQUEST['activityid'];
        $activitychildid = $_REQUEST['activitychildid'];
        $otherproduct = $_REQUEST['otherproduct'];

        $servicecontractsid =$_REQUEST['servicecontractsid'];//服务合同id
        $servicecontractsid_display =$_REQUEST['servicecontractsid_display'];//服务合同编号
        $accountid =$_REQUEST['accountid'];//客户id
        $accountid_display =$_REQUEST['accountid_display'];//客户名称
        $mobile =$_REQUEST['mobile'];//手机号
        $mobilevcode =$_REQUEST['mobilevcode'];//验证码
        $productclassonevalues =$_REQUEST['productclassonevalues'];//产品分类
        $productclasstwovalues =$_REQUEST['productclasstwovalues'];//购买套餐
        $buyyear =$_REQUEST['buyyear'];//合同类型购买和续费
        $classtype =$_REQUEST['classtype'];//合同类型购买和续费
        $agents =$_REQUEST['agents'];//代理商id
        $servicetotal=$_REQUEST['servicetotal'];
        $tyunusercode=$_REQUEST['tyunusercode'];
        $tyunusercodeid=$_REQUEST['tyunusercodeid'];
        $tyunusercodename=$_REQUEST['tyunusercodetext'];
        $buydate=empty($_POST['buydate'])?date('Y-m-d H:i:s'):date('Y-m-d',strtotime($_POST['buydate'])).date(' H:i:s');
        $oldcustomerid = $_REQUEST['oldcustomerid'];
        $oldcustomername = $_REQUEST['oldcustomername'];
        $activityid = $_REQUEST['activityid'];

        $activityTitle =$_REQUEST['activitytitle'];
        $combinationprice = $_REQUEST['combinationprice'];
        $activitytypeText =$_REQUEST['activitytypetext'];
        $activityno =$_REQUEST['activityno'];
        $authenticationtype =$_REQUEST['authenticationtype'];
        $chooseuserproduct = $_REQUEST['chooseuserproduct'];
        $AssistantProduct = $_REQUEST['assistantproduct'];

        $returnmsg=array('success'=>0);
        do{
            $checkdata=$this->checkBasicInfo();
            if($checkdata['flag']){
                $returnmsg['msg']=$checkdata['msg'];
                break;
            }
            foreach ($otherproduct as $value){
                $allotherproduct[] = $value;
            }

            if(count($allotherproduct)>0){
                $otherproductarr = array(
                    'type'=>1,
                    'productList'=>$allotherproduct,
                    "activityType"=>intval(0),
                    "activityID"=>intval(0),
                    "activityChildID"=>'',
                    'giftProduct'=>[],
                    "activityTitle"=>'',
                    "combinationPrice"=>0,
                    "meetActivity"=>false,
                    "subtotal"=>0,
                    "canParticipateActivityList"=>array(array(
                        "activityChildID"=>'',
                        "activityID"=>0,
                        "activityTitle"=>'',
                        "selection"=>false
                    ))
                );
            }

            $product =  array(
                'type'=>$activitymodel,
                'productList'=>$ProductInfo,
                "activityType"=>intval($ActivityType),
                "activityID"=>intval($ActivityID),
                "activityChildID"=>$activitychildid,
                'giftProduct'=>$GiftProduct,
                "activityTitle"=>$activityTitle,
                "combinationPrice"=>$combinationprice?$combinationprice:0,
                "meetActivity"=>true,
                "chooseUserProduct"=>$chooseuserproduct,
                "subtotal"=>0,
                "canParticipateActivityList"=>array(array(
                    "activityChildID"=>$activitychildid,
                    "activityID"=>$activityid,
                    "activityTitle"=>$activityTitle,
                    "selection"=>($activitychildid?true:false)
                )),
                'assistantProduct'=>$AssistantProduct

            );

            $resparams=array(
                "authenticationType"=> intval($authenticationtype),
                "cid"=>intval($accountid),
                "crmOrderFlag"=> 1,
                "customerName"=>$accountid_display,
                "invitationCode"=> intval($agentIdentity),
                "agentIdentity"=>intval($agentIdentity),
                "productType"=>$activitymodel,
                "transferFlag"=>0,
                'type'=>1,
                'userID'=>intval($tyunusercodeid),
//                'agentIdentity'=>intval($agentIdentity),
//                'discount'=>1,
//                'agentType'=>0,
                'productInfo'=>($otherproductarr?array($product,$otherproductarr):array($product)),
                'contractMoney'=>floatval($servicetotal),
                'contractCode'=>$servicecontractsid_display,
                'electricContract'=>1,
                'addDate'=>$buydate,
                'operatorName'=>$_SESSION['last_name'],
                'operatorId'=>$this->userid
            );
            $postData=json_encode($resparams);
            $time=time().'123';
            $sault=$time.$this->sault;
            $token=md5($sault);
            $curlset=array(CURLOPT_HTTPHEADER=>array(
                "Content-Type:application/json",
                "S-Request-Token:".$token,
                "S-Request-Time:".$time));
            $this->_logs(array("crmPurchase request data:",$postData));
            $res = $this->https_request($this->crmPurchase, $postData,$curlset);
            $data=json_decode($res,true);
            if($data['code']==200){
                $returnmsg = $this->elecontractPreview($_REQUEST,$data);
                $returnmsg['data']['paycode'] = $data['data']['payCode'];
            }else{
                $returnmsg['msg']=$data['message'];
            }
        }while(0);
        echo json_encode($returnmsg);
        exit;
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

    public function getAuthType($authType){
        switch ($authType){
            case 0:
                $type = 1;
                break;
            case 1:
                $type = 0;
                break;
            case 2:
                $type = 0;
                break;
            case 3:
                $type = 0;
                break;
        }
        return $type;
    }


    public function contractCanOrder(){
        $servicecontractsid = $_REQUEST['servicecontractsid'];
        $params = array(
            'fieldname'=>array(
                'module'		=>'ServiceContracts',
                'action'		=> 'canUseToTyunWeb',
                'searchValue'	=> $servicecontractsid,
                'userid'		=> $this->userid
            ),
            'userid'			=> $this->userid
        );

        $list = $this->call('getComRecordModule', $params);
        return $list[0];
    }
}


