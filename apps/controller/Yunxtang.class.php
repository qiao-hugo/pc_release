<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/9/23
 * Time: 16:30
 */

class Yunxtang extends baseapp
{
    public $REQUEST_URL = "https://api-qidac1.yunxuetang.cn/v1/users/thirdtokens";
    public $API_KEY = "37ad4e8f-2bb7-4411-87c1-16f9b32f971d";
    public $SECRET_KEY = "b6e328ae-6e5a-496e-9962-8723d05a389f";
    public function getRandom($length){
        $d=rand();
        for($i=0;$i<$length;$i++){
            $d=$d*10;
        }
        return substr($d,0,$length);
    }
    public function SHA256Encrypt($orignal){
        return hash("sha256",$orignal);
    }
    public function index(){
        $salt=$this->getRandom(4);
        $usrname=$_SESSION['customer_name'];
        $signature=$this->SECRET_KEY.$this->API_KEY.$salt.$usrname;
        $array=array("apiKey"=>$this->API_KEY,"salt"=>$salt,"signature"=>$this->SHA256Encrypt($signature),"userName"=>$usrname);
        $headers[]="Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";
        $headers[]="Accept-Language:en-us,en;q=0.5";
        $headers[]="Content-Type:application/json";
        $jsonData=$this->curlTransferData($this->REQUEST_URL,json_encode($array),$headers,'POST');
        $jsonArrayData=json_decode($jsonData,true);
        if(!empty($jsonArrayData['error'])){
            echo "请联系管理员！";
            //print_r($jsonArrayData);
        }else{
            header("location:".$jsonArrayData['url']);
        }
        exit;
    }
}