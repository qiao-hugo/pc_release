<?php
/**
 * Api接口
 * @author Jeff
 *
 */
class TyunWebBuyServiceAPI extends baseapi
{
    private $tokensalt = "#13anljlellaoonzopaqmvaekj98jjo34943240jljljflj321";

    public function test()
    {
        $decData = $this->decrypt($_REQUEST['data']);
        $decData = trim($decData);
        $decArr = json_decode($decData, true);
        //print_r($decArr);
        //echo $this->userid . ' ' .$_SESSION['customer_id'];
        echo json_encode($decArr);
        exit();
    }

    /**
     * des加密
     * @param unknown $encrypt 原文
     * @return string
     */
    function encrypt($encrypt, $key = 'sdfesdcf\0\0\0\0\0\0\0\0')
    {
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
    function decrypt($decrypt, $key = 'sdfesdcf\0\0\0\0\0\0\0\0')
    {
        $decoded = str_replace(' ', '%20', $decrypt);
        $decoded = base64_decode($decrypt);
        $mcrypt = MCRYPT_TRIPLEDES;
        $iv = mcrypt_create_iv(mcrypt_get_iv_size($mcrypt, MCRYPT_MODE_ECB), MCRYPT_RAND);
        $decrypted = mcrypt_decrypt($mcrypt, $key, $decoded, MCRYPT_MODE_ECB, $iv);
        return $decrypted;
    }

    /**
     * 根据名称获取CID
     * 没有相同的名称则创建客户
     */
    public function tyunWebGetAccountCID()
    {
        $accountname = $_REQUEST['accountname'];
        $accountid = $_REQUEST['accountid'];
        $accountname = trim($accountname);
        $_REQUEST=array_map(function($v){return trim($v);},$_REQUEST);
        $province=$_REQUEST['province'];
        $city=$_REQUEST['city'];
        $area=$_REQUEST['area'];
        $address=$_REQUEST['address'];
        $phone=$_REQUEST['phone'];
        $linkname=$_REQUEST['linkname'];
        $title=$_REQUEST['title'];
        $email1=$_REQUEST['email1'];
        $mobile=$_REQUEST['mobile'];
        $weixin=$_REQUEST['weixin'];
        $customertype=$_REQUEST['customertype'];
        $website=$_REQUEST['website'];
        $gendertype=$_REQUEST['gendertype'];
        if (!empty($accountname)) {
            $params = array('fieldname' =>
                array(
                    'accountname'=>$accountname,
                    'accountid'=>$accountid,
                    'province'=>$province,
                    'city'=>$city,
                    'area'=>$area,
                    'address'=>$address,
                    'phone'=>$phone,
                    'linkname'=>$linkname,
                    'title'=>$title,
                    'email1'=>$email1,
                    'mobile'=>$mobile,
                    'weixin'=>$weixin,
                    'customertype'=>$customertype,
                    'website'=>$website,
                    'gendertype'=>$gendertype
                ),'userid'=>0);
            $res = $this->call('tyunWebGetAccount', $params);
            //$this->_logs(array("T云返回结果(tyunWebGetAccount)：", $res));
            if (!empty($res[0])) {
                echo json_encode(array('success' => 'true', 'code' => 200, 'data' => $res[0]), JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(array('success' => 'false', 'code' => 200, 'msg' => '没有相关客户信息!'), JSON_UNESCAPED_UNICODE);
            }
        } else {
            echo json_encode(array('success' => 'false', 'code' => 500, 'message' => '参数错误'));
        }
    }

    /**
     * 根据客户名获取CID
     */
    public function tyunWebGetACIDByAccountName()
    {
        $accountname = $_REQUEST['accountname'];

        if (!empty($accountname)) {
            $params = array('fieldname' =>
                array(
                    'accountname'=>$accountname,
                    'module' => 'TyunWebBuyService',
                    'action' => 'tyunWebGetACIDByAccountName',
                    'userid' => 0
                ),'userid'=>0);
            $res = $this->call('getComRecordModule', $params);
            //$this->_logs(array("T云返回结果(tyunWebGetAccount)：", $res));
            if (!empty($res[0])) {
                echo json_encode(array('success' => 'true', 'code' => 200, 'data' => $res[0]), JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(array('success' => 'false', 'code' => 200, 'msg' => '没有相关客户信息!'), JSON_UNESCAPED_UNICODE);
            }
        } else {
            echo json_encode(array('success' => 'false', 'code' => 500, 'message' => '参数错误'));
        }
    }
    /**
     * 根据TYUN账户去查客户ID和名称，按订单的下单时降序排取第一个返回客户名称和客户ID
     */
    public function tyunWebGetACIDByTyunUserCode()
    {
        $usercode = $_REQUEST['usercode'];

        if (!empty($usercode)) {
            $params = array('fieldname' =>
                array(
                    'usercode'=>$usercode,
                    'module' => 'TyunWebBuyService',
                    'action' => 'tyunWebGetACIDByTyunUserCode',
                    'userid' => 0
                ),'userid'=>0);
            $res = $this->call('getComRecordModule', $params);
            //$this->_logs(array("T云返回结果(tyunWebGetAccount)：", $res));
            if (!empty($res[0])) {
                echo json_encode(array('success' => 'true', 'code' => 200, 'data' => $res[0]), JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(array('success' => 'false', 'code' => 200, 'msg' => '没有相关客户信息!'), JSON_UNESCAPED_UNICODE);
            }
        } else {
            echo json_encode(array('success' => 'false', 'code' => 500, 'message' => '参数错误'));
        }
    }
    /**
     * 根据CID获取客户客服商务信息
     */
    public function getSalesANDCustomerServiceBYCID()
    {
        $body = file_get_contents('php://input');
        if(!empty($body)){
            $body=base64_encode($body);
            $params = array(
                'fieldname' => array(
                    'module' => 'TyunWebBuyService',
                    'action' => 'getSalesANDCustomerServiceBYCID',
                    'rddata' => $body,
                    'userid' => 0
                ),
                'userid' => 0
            );
            //$this->_logs(array("T云返回结果(AddbuyOrderOnLine)：", $params));
            $res = $this->call('getComRecordModule', $params);
            if (!empty($res[0])) {
                echo json_encode(array('success' => 'true', 'code' => 200, 'data' => array_values($res[0])), JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(array('success' => 'false', 'code' => 500, 'msg' => '没有相关客户信息!'), JSON_UNESCAPED_UNICODE);
            }
        } else {
            echo json_encode(array('success' => 'false', 'code' => 500, 'message' => '参数错误'));
        }
    }

    public function register()
    {
        if (!empty($_REQUEST['productid'])) {
            //获取请求产品的key
            echo urlencode($this->cookiecode($_GET['productid'], ''));
            exit;
        }
    }

    public function cookiecode($string, $operation = 'DECODE', $key = '', $expiry = 0)
    {
        $ckey_length = 4;
        $key = md5($key ? $key : md5($_SERVER['REMOTE_ADDR']));
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';
        $cryptkey = $keya . md5($keya . $keyc);
        $key_length = strlen($cryptkey);
        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
        $string_length = strlen($string);
        $result = '';
        $box = range(0, 255);
        $rndkey = array();
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }
        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        for ($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }

        if ($operation == 'DECODE') {
            if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            return $keyc . str_replace('=', '', base64_encode($result));
        }

    }

    public function checktokenauth()
    {
        $cs_method = explode(',', urldecode($this->cookiecode($_REQUEST['tokenauth'], 'DECODE')));
        if (is_array($cs_method)) {
            if (method_exists($this, $cs_method[0])) {
                $this->$cs_method[0]();
            }
        } else {
            echo json_encode(array('success' => false, 'code' => 500));
        }
    }

    public function getCrmUserBasicInfo(){
        $lastName = $_REQUEST['lastName'];
        if(!empty($lastName)){
            $params = array(
                'fieldname' => array(
                    'module' => 'TyunWebBuyService',
                    'action' => 'getCrmUserBasicInfo',
                    'lastName' => $lastName,
                    'userid' => $this->userid
                ),
                'userid' => $this->userid
            );
            $list = $this->call('getComRecordModule', $params);
            if(!empty($list[0])){
                $array=array();
                foreach($list[0] as $value){
                    $array[]=$value;
                }
                $data=json_encode($array);
                $data=$this->encrypt($data);
                echo json_encode(array("success"=>true,"data"=>$data));
            }else{
                echo json_encode(array("success"=>false,"msg"=>'没有相关数据'));
            }
            //$this->_logs($list[0]);
            exit;
        }
        echo json_encode(array("success"=>false,"msg"=>'参数错语'));
        exit;
    }
    public function PaymentCompletedOrder(){
        $lastName = $_REQUEST['lastName'];
        if(!empty($lastName)){
            $params = array(
                'fieldname' => array(
                    'module' => 'TyunWebBuyService',
                    'action' => 'PaymentCompletedOrder',
                    'lastName' => $lastName,
                    'userid' => $this->userid
                ),
                'userid' => $this->userid
            );
            $list = $this->call('getComRecordModule', $params);
            if(!empty($list[0])){
                $array=array();
                foreach($list[0] as $value){
                    $array[]=$value;
                }
                $data=json_encode($array);
                $data=$this->encrypt($data);
                echo json_encode(array("success"=>true,"data"=>$data));
            }else{
                echo json_encode(array("success"=>false,"msg"=>'没有相关数据'));
            }
            //$this->_logs($list[0]);
            exit;
        }
        echo json_encode(array("success"=>false,"msg"=>'参数错语'));
        exit;
    }

    /**
     *
     */
    public function getAccountServiceInfo(){
        $accountid = $_REQUEST['accountid'];
        $accountid = trim($accountid);
        if (!empty($accountid)) {
            $params = array(
                'fieldname' => array(
                    'module' => 'TyunWebBuyService',
                    'action' => 'getAccountServiceInfo',
                    'accountid' => $accountid,
                    'userid' => 0
                ),
                'userid' => 0
            );
            $res = $this->call('getComRecordModule', $params);
            //$this->_logs(array("T云返回结果(getComRecordModule)：", $res));
            if (!empty($res[0])) {
                echo json_encode(array('success' => 'true', 'code' => 200, 'data' => current($res[0])), JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(array('success' => 'false', 'code' => 200, 'msg' => '没有相关客户信息!'), JSON_UNESCAPED_UNICODE);
            }
        } else {
            echo json_encode(array('success' => 'false', 'code' => 500, 'message' => '参数错误'));
        }
    }


    public function listCrmUserBasic(){
        $pageNum = isset($_REQUEST['pageNum'])?$_REQUEST['pageNum']:1;
        $pageSize = isset($_REQUEST['pageSize'])?$_REQUEST['pageSize']:15;
        $content = isset($_REQUEST['content'])?$_REQUEST['content']:'';
        $ids = isset($_REQUEST['ids'])?$_REQUEST['ids']:'';
        $notIncludeUserIds = isset($_REQUEST['notIncludeUserIds'])?$_REQUEST['notIncludeUserIds']:'';
        $content = trim($content);
        $pageNum = is_numeric($pageNum)?($pageNum>0?$pageNum:1):1;
        $pageSize = is_numeric($pageSize)?(($pageSize>0&&$pageSize<100)?$pageSize:100):15;
        $params = array(
            'fieldname' => array(
                'module' => 'TyunWebBuyService',
                'action' => 'listCrmUserBasic',
                'content' => $content,
                'pageSize' => $pageSize,
                'pageNum' => $pageNum,
                'ids' => $ids,
                'notIncludeUserIds'=>$notIncludeUserIds,
                'userid' => 0
            ),
            'userid' => 0
        );
        $res = $this->call('getComRecordModule', $params);
        //$this->_logs(array("T云返回结果(listCrmUserBasic)：", $res));
        if (!empty($res[0])) {
            echo json_encode(array('success' => 'true', 'code' => 200, 'data' =>$res[0]), JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(array('success' => 'false', 'code' => 200, 'msg' => '没有相关客户信息!'), JSON_UNESCAPED_UNICODE);
        }

    }

    /**
     *
     */
    public function listCrmUserByRoleId(){
        $roleids = isset($_REQUEST['roleids'])?$_REQUEST['roleids']:'';

        $roleids = trim($roleids);

        $params = array(
            'fieldname' => array(
                'module' => 'TyunWebBuyService',
                'action' => 'listCrmUserByRoleId',
                'roleids' => $roleids,
                'userid' => 0
            ),
            'userid' => 0
        );
        $res = $this->call('getComRecordModule', $params);
        //$this->_logs(array("T云返回结果(listCrmUserBasic)：", $res));
        if (!empty($res[0])) {
            echo json_encode(array('success' => 'true', 'code' => 200, 'data' =>$res[0]), JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(array('success' => 'false', 'code' => 200, 'msg' => '没有相关信息!'), JSON_UNESCAPED_UNICODE);
        }

    }
    public function findSubUserIdByUserId(){
        $userid = isset($_REQUEST['userid'])?$_REQUEST['userid']:'';
        $isall = isset($_REQUEST['isall'])?$_REQUEST['isall']:0;
        $userid = trim($userid);
        if(is_numeric($userid) && $userid>0){
            $params = array(
                'fieldname' => array(
                    'module' => 'TyunWebBuyService',
                    'action' => 'findSubUserIdByUserId',
                    'fromuserid' => $userid,
                    'userid' => 0,
                    'isall' => $isall
                ),
                'userid' => 0
            );
            $res = $this->call('getComRecordModule', $params);
            if (!empty($res[0])) {
                $temp['listids']=array_map(function($v){return (int)$v;},$res[0]['listids']);
                echo json_encode(array('success' => 'true', 'code' => 200, 'data' =>$temp), JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(array('success' => 'false', 'code' => 200, 'msg' => '没有相关信息!'), JSON_UNESCAPED_UNICODE);
            }
        }


    }
    public function AddbuyOrderOnLine(){
        $body = file_get_contents('php://input');
        if(!empty($body)){
            $body=base64_encode($body);
            $params = array(
                'fieldname' => array(
                    'module' => 'TyunWebBuyService',
                    'action' => 'AddbuyOrderOnLine',
                    'rddata' => $body,
                    'userid' => 0
                ),
                'userid' => 0
            );
            //$this->_logs(array("T云返回结果(AddbuyOrderOnLine)：", $params));
            $res = $this->call('getComRecordModule', $params);
            if($res[0]){
                echo json_encode(array('success' => 'true', 'code' => 200, 'data' =>''), JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(array('success' => 'false', 'code' => 500, 'msg' => '没有相关信息!'), JSON_UNESCAPED_UNICODE);
            }
        }else{
            echo json_encode(array('success' => 'false', 'code' => 500, 'msg' => '没有相关信息!'), JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * 数据迁移成功后发短信
     */
    public function clientMigrationBySendSMS(){
        $body = file_get_contents('php://input');
        $ordercode = json_decode($body,true);
        if(!empty($ordercode)){
            $params = array(
                'fieldname' => array(
                    'module' => 'TyunWebBuyService',
                    'action' => 'clientMigrationBySendMail',
                    'ordercode' => $ordercode['ordercode'],
                    'userid' => 0
                ),
                'userid' => 0
            );
            $res = $this->call('getComRecordModule', $params);
            if(!empty($res[0])){
                echo json_encode(array('success' => 'true','data' =>''), JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(array('success' => 'false', 'msg' => '短信发送失败!'), JSON_UNESCAPED_UNICODE);
            }
        }else{
            echo json_encode(array('success' => 'false','msg' => '短信发送失败!'), JSON_UNESCAPED_UNICODE);
        }
    }

    public function sendVerifyCode(){
        date_default_timezone_set("Asia/Shanghai");
        $mobilecodeurl = 'http://tyunapi.71360.com/api/cms/GetMobileCode';
        $tokensalt = "#13anljlellaoonzopaqmvaekj98jjo34943240jljljflj321";
        $body = file_get_contents('php://input');
        $ordercode = json_decode($body,true);
        $currenttime = $ordercode['currenttime'];
        $tokensalt = md5($this->tokensalt.$currenttime);
        $verifyCode = $ordercode['verifyCode'];
        $token = $ordercode['token'];
        $this->_logs(array('sendVerifyCode','params'=>$ordercode,'tokensalt'=>$this->tokensalt,'date'=>date("YmdH"),'mdtoken'=>$tokensalt));
        if($token!=$tokensalt){
            echo json_encode(array('success' => 'false', 'code'=>500,'msg' => '请求失败!'), JSON_UNESCAPED_UNICODE);
            exit();
        }
        $mobile = $ordercode['mobile'];

        //为空获取验证码
        if(!$verifyCode){
            $result = $this->getVerifyCode($mobile);
            if($result['success']){
                echo json_encode(array('success'=>true,'code'=>200,'msg'=>'已发送'),JSON_UNESCAPED_UNICODE);
            }else{
                echo json_encode(array('success'=>false, 'code'=>500, 'msg'=>$result['message']),JSON_UNESCAPED_UNICODE);
            }
            exit();
        }

        //非空校验验证码
        if(strlen($verifyCode)!=5){
            echo json_encode(array('success' => 'false', 'msg' => '验证码异常'), JSON_UNESCAPED_UNICODE);
            exit();
        }
        $result = $this->checkVerifyCode($mobile,$verifyCode);
        if($result['success']){
            echo json_encode(array('success'=>true,'code'=>200,'msg'=>$result['message']),JSON_UNESCAPED_UNICODE);
        }else{
            echo json_encode(array('success'=>false, 'code'=>500,'msg'=>$result['message']),JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * 提供产品列表
     */
    public function allProducts(){
        $params = array(
            'fieldname' => array(
                'module' => 'TyunWebBuyService',
                'action' => 'allPackageAndProduct',
                'userid' => 0
            ),
            'userid' => 0
        );
        $res = $this->call('getComRecordModule', $params);
        if(!empty($res[0])){
            echo json_encode(array('success' => 'true', 'code' => 200, 'data' =>$res[0]), JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(array('success' => 'false', 'code' => 500, 'msg' => '没有相关信息!'), JSON_UNESCAPED_UNICODE);
        }

    }

    /**
     * 合同状态更改接口
     */
    public function elecContractUpdateStatus(){
        date_default_timezone_set("Asia/Shanghai");
        $body = file_get_contents('php://input');
        $ordercode = json_decode($body,true);
        $currenttime = $ordercode['currenttime'];
        $tokensalt = md5($this->tokensalt.$currenttime);
        $token = $ordercode['token'];
        $this->_logs(array('elecContractUpdateStatus','params'=>$ordercode,'tokensalt'=>$this->tokensalt,'date'=>date("YmdH"),'mdtoken'=>$tokensalt));
//        if($token!=$tokensalt){
//            $this->_logs(array('elecContractUpdateStatusFailReason'=>'token异常'));
//            echo json_encode(array('success' => false, 'code'=>500,'message' => 'token异常，请重试!'), JSON_UNESCAPED_UNICODE);
//            exit();
//        }
        $status = $ordercode['status'];
        $message = $ordercode['message'];
        $contract_number = $ordercode['contract_number'];
        $contract_url = $ordercode['contract_url'];
        $contract_id = $ordercode['contract_id'];
        $enclouses = $ordercode['enclouses'];

        //status 1拒签 2过期 3签署完成 4已撤回
        $statusArray = array(
            1=>'c_elec_cancel',
            2=>'c_elec_cancel',
            3=>'c_elec_complete',
            4=>'a_elec_withdraw',
        );

        $params = array(
            'fieldname' => array(
                'module' => 'ServiceContracts',
                'action' => 'updateModuleStatus',
                'contract_no'=>$contract_number,
                'eleccontractstatus'=>$statusArray[$status],
                'elechandreason'=>$message,
                'contract_url'=>$contract_url,
                'contract_id'=>$contract_id,
                'enclouses'=>$enclouses,
                'userid' => 0
            ),
            'userid' => 0
        );
        $res = $this->call('getComRecordModule', $params);
        $this->_logs(array('elecContractUpdateStatusResult',$res));
        if($res[0]){
            echo json_encode(array('success' => true, 'code' => 200), JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(array('success' => false, 'code' => 500, 'message' => '没有相关信息!'), JSON_UNESCAPED_UNICODE);
        }

    }

    /**
     * 合同作废
     */
    public function toVoidElecContract(){
        date_default_timezone_set("Asia/Shanghai");
        $body = file_get_contents('php://input');
        $data = json_decode($body,true);
        $tokensalt = md5($this->tokensalt.date("YmdH"));
        $token = $data['token'];
        if($token!=$tokensalt){
            echo json_encode(array('success' => false,'code'=>500, 'message' => 'token异常，请重试!'), JSON_UNESCAPED_UNICODE);
            exit();
        }
        $this->_logs(array('toVoidElecContract','params'=>$data));
        $contract_no = $data['contract_number'];
        $void_reason = $data['void_reason'];
        if(!$contract_no){
            echo json_encode(array('success' => false, 'code' => 200, 'message' => '请传入合同编号'), JSON_UNESCAPED_UNICODE);
            exit();
        }

        $params = array(
            'fieldname' => array(
                'module' => 'ServiceContracts',
                'action' => 'toVoidElecContract',
                'contract_no'=>$contract_no,
                'void_reason'=>$void_reason,
                'userid' => 0
            ),
            'userid' => 0
        );
        $res = $this->call('getComRecordModule', $params);
        $this->_logs(array('toVoidElecContractResult',$res));
        if(empty($res[0])){
            echo json_encode(array('success' => false, 'code' => 500, 'message' => '请求失败'), JSON_UNESCAPED_UNICODE);
            exit();
        }
        $res =$res[0];
        if($res['success']){
            echo json_encode(array('success' => true, 'code' => 200), JSON_UNESCAPED_UNICODE);
        }else{
            echo json_encode(array('success' => false, 'code' => 500, 'message' => '合同作废失败'), JSON_UNESCAPED_UNICODE);
        }
    }


    /**
     * 合同预览和下载
     */
    public function view(){
        date_default_timezone_set("Asia/Shanghai");
        $body = file_get_contents('php://input');
        $data = json_decode($body,true);
        $currenttime = $data['currenttime'];
        $tokensalt = md5($this->tokensalt.$currenttime);
        $token = $data['token'];
        $this->_logs(array('view','params'=>$data,'tokensalt'=>$this->tokensalt,'date'=>date("YmdH"),'mdtoken'=>$tokensalt));
        if($token!=$tokensalt){
            echo json_encode(array('success' => false,'code'=>500, 'message' => 'token异常，请重试!'), JSON_UNESCAPED_UNICODE);
            exit();
        }
        $contract_no = $data['contract_number'];
        $params = array(
            'fieldname' => array(
                'module' => 'ServiceContracts',
                'action' => 'getTPLView',
                'contract_no'=>$contract_no,
                'userid' => 0
            ),
            'userid' => 0
        );
        $res = $this->call('getComRecordModule', $params);
        $this->_logs(array('viewResult',$res));
        if(empty($res[0])){
            echo json_encode(array('success' => 'false', 'code' => 500, 'message' => '请求失败'), JSON_UNESCAPED_UNICODE);
            exit();
        }
        $res = json_decode($res[0],true);
        if($res['success']){
            echo json_encode(array('success' => true, 'code' => 200,'contractUrl'=>$res['contractUrl'],'enclosureList'=>$res['enclosureList']), JSON_UNESCAPED_UNICODE);
        }else{
            echo json_encode(array('success' => false, 'code' => 500, 'message' => "查看预览失败"), JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * T云  生成电子合同
     */
    public function createElecContract(){
        date_default_timezone_set("Asia/Shanghai");
        $body = file_get_contents('php://input');
        $data = json_decode($body,true);
        $currenttime = $data['currenttime'];
        $tokensalt = md5($this->tokensalt.$currenttime);
        $token = $data['token'];
        $this->_logs(array('createElecContract','params'=>$data,'tokensalt'=>$this->tokensalt,'date'=>date("YmdH"),'mdtoken'=>$tokensalt));
        if($token!=$tokensalt){
            echo json_encode(array('success' => false,'code'=>500, 'message' => 'token异常，请重试!'), JSON_UNESCAPED_UNICODE);
            exit();
        }
        $ordercode = $data['ordercode'];
        $clientproperty = $data['clientproperty'];
        $address = $data['address'];
        $company = $data['company'];
        $elereceiver = $data['elereceiver'];
        $elereceivermobile = $data['elereceivermobile'];
        $params = array(
            'fieldname' => array(
                'module' => 'ServiceContracts',
                'action' => 'createTyunElecServiceContracts',
                'ordercode'=>$ordercode,
                'clientproperty'=>$clientproperty,
                'accountinfo'=>array(
                    "first_address"=>$address,
                    "first_company"=>$company,
                    "first_name"=>$elereceiver,
                    "first_phone"=>$elereceivermobile,
                ),
                'userid' => 0
            ),
            'userid' => 0
        );
        $res = $this->call('getComRecordModule', $params);
        $this->_logs(array('createElecContractresult',$res));
        if($res[0]['success']){
            echo json_encode(array('success' => true, 'code' => 200,'contractUrl'=>$res[0]['contractUrl'],'contract_number'=>$res[0]['contract_number']), JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(array('success' => false, 'code' => 500, 'message' =>'申请失败，请稍后尝试'), JSON_UNESCAPED_UNICODE);
        }

    }

    /**
     * 转为正式合同
     */
    public function toFormal(){
        date_default_timezone_set("Asia/Shanghai");
        $body = file_get_contents('php://input');
        $data = json_decode($body,true);
        $currenttime = $data['currenttime'];
        $tokensalt = md5($this->tokensalt.$currenttime);
        $token = $data['token'];
        $this->_logs(array('toFormal','params'=>$data,'tokensalt'=>$this->tokensalt,'date'=>date("YmdH"),'mdtoken'=>$tokensalt));
        if($token!=$tokensalt){
            echo json_encode(array('success' => false,'code'=>500, 'message' => 'token异常，请重试!'), JSON_UNESCAPED_UNICODE);
            exit();
        }
        $contract_no = $data['contract_number'];
        $idcard = $data['idcard'];
        $name = $data['name'];
        $phone = $data['phone'];
        $type = $data['type'];
        $params = array(
            'fieldname' => array(
                'module' => 'ServiceContracts',
                'action' => 'toFormalServiceContracts',
                'contract_no'=>$contract_no,
                'idcard'=>$idcard,
                'name'=>$name,
                'phone'=>$phone,
                'type'=>$type,
                'userid' => 0
            ),
            'userid' => 0
        );
        $res = $this->call('getComRecordModule', $params);
        $this->_logs(array('toFormalResult',$res));
        if(empty($res[0])){
            echo json_encode(array('success' => false, 'code' => 500, 'message' => '请求失败'), JSON_UNESCAPED_UNICODE);
            exit();
        }
        if($res[0]['success']){
            echo json_encode(array('success' => true, 'code' => 200), JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(array('success' => false, 'code' => 500, 'message' => '转正式合同失败，请稍后尝试'), JSON_UNESCAPED_UNICODE);
//            echo json_encode(array('success' => false, 'code' => 500, 'message' => '合同转正式失败'), JSON_UNESCAPED_UNICODE);
        }
    }
    /**
     * 据据71360用户的ID获取对应的合同附件
     */
    public function getFileByUserCodeId(){
        $body = file_get_contents('php://input');
        $ordercode = json_decode($body,true);
        if(!empty($ordercode)){
            if($ordercode['ptype']==1){
                $action = 'getFileByUserCodeIdTwo';
            }else{
                $action = 'getFileByUserCodeId';
            }
            $params = array(
                'fieldname' => array(
                    'module' => 'ActivationCode',
                    'action' => $action,
                    'usercodeid' => $ordercode['usercodeid'],
                    'userid' => 0
                ),
                'userid' => 0
            );
            $res = $this->call('getComRecordModule', $params);
            if($res[0][0]){
                echo json_encode(array('success' => 'true','data' =>$res[0][1]), JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(array('success' => 'false', 'msg' => '文件获取失败!'), JSON_UNESCAPED_UNICODE);
            }
        }else{
            echo json_encode(array('success' => 'false','msg' => '文件获取失败'), JSON_UNESCAPED_UNICODE);
        }
    }

    public function AddbuyOrderOnLine2(){
        $body = file_get_contents('php://input');
        if(!empty($body)){
            $body=base64_encode($body);
            $params = array(
                'fieldname' => array(
                    'module' => 'TyunWebBuyService',
                    'action' => 'AddbuyOrderOnLine2',
                    'rddata' => $body,
                    'userid' => 0
                ),
                'userid' => 0
            );
            //$this->_logs(array("T云返回结果(AddbuyOrderOnLine)：", $params));
            $res = $this->call('getComRecordModule', $params);
            if($res[0]){
                echo json_encode(array('success' => 'true', 'code' => 200, 'data' =>''), JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(array('success' => 'false', 'code' => 500, 'msg' => '没有相关信息!'), JSON_UNESCAPED_UNICODE);
            }
        }else{
            echo json_encode(array('success' => 'false', 'code' => 500, 'msg' => '没有相关信息!'), JSON_UNESCAPED_UNICODE);
        }
    }
    /**
     * 数字威客创建客户
     */
    public function wkWebGetAccountCID(){
        $_REQUEST['accountid']=0;
        $this->checkToken('fdsasafdvczxvcghnmlbacxzdsjhkopqwlreinuw');
        $body = file_get_contents('php://input');
        $ordercode = json_decode($body,true);
        if(is_array($ordercode) && !empty($ordercode)){
            foreach($ordercode as $key=>$value){
                $_REQUEST[$key]=$value;
            }
            $this->tyunWebGetAccountCID();
        }else{
            echo json_encode(array('success' => 'false', 'code' => 500, 'msg' => '没有相关信息!'), JSON_UNESCAPED_UNICODE);
        }

    }




}
