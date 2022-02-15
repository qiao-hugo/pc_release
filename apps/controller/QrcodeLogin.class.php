<?php

class QrcodeLogin extends baseapp{
    //public $sso_url='http://192.168.40.66:8080/login/wxLogin';
    public $sso_url='http://192.168.7.195:8086/login/wxLogin';
	public function index(){
	    $loginip=$_GET['loginid'];
	    $loginid=$_GET['loginid'];
	    $backurl=$_GET['backurl'];
	    if(!empty($loginid)){
	        if(strpos($loginip,':')==false){
                $loginip=$this->base64decode($loginip);
                $params = array('fieldname'=>array(
                    'userid'=>$this->userid,
                    'status' =>'scan',
                    'loginid'=>$loginip,
                    'flag'=>2
                ));
                $this->call('qrcodelogin', $params);
            }
            //$loginid=$this->base64decode($loginip);
            /*$params = array('fieldname'=>array(
                'userid'=>$this->userid,
                'status' =>'scan',
                'loginid'=>$loginid
            ));*/
            $data=$this->getWeixinUserInfo();
            $backurl=$this->askii_encode($backurl);
            //$this->call('qrcodelogin', $params);
            $this->smarty->assign('wximg',$data['avatar']);
            $this->smarty->assign('loginid',$loginid);
            $this->smarty->assign('backurl',$backurl);
            $this->smarty->assign('lastname',$_SESSION['last_name']);
            $this->smarty->display('QrcodeLogin/index.html');
        }

        //echo $this->base64encode(20723249202);

	}

    /**
     * 扫码处理
     */
    public function doset(){

        $loginip=$_GET['loginid'];
        $loginid=$_GET['loginid'];
        $backurl=$_GET['backurl'];
        if(!empty($loginid)) {
            $flag=1;
            if(strpos($loginip,':')==false) {
                $loginid = $this->base64decode($loginip);
                $flag=2;
            }
            $status = $_GET['status'];
            $params = array('fieldname' => array(
                'userid' => $this->userid,
                'status' => $status,
                'loginid' => $loginid,
                'flag'=>$flag
            ));
            $result=$this->call('qrcodelogin', $params);
            if($flag==1) {
                if ($result[0]['success'] == 1) {
                    $url=$this->askii_decode($backurl);
                    if(empty($url)){
                        $url=$this->sso_url;
                    }
                    $this->dologin($url, array('loginId' => $loginid, 'data' => $result[0]['data']));
                }
            }

        }
    }

    /**
     * 数字解密
     * @param $v
     * @return int|mixed
     */
    protected function base64decode($v){
        $string=base64_decode($v);
        $dd=md5('Useridstrunlandorgnetcomcn');
        $e=explode($dd,$string);
        $ee=md5('AccountsiD');
        $e=explode($ee,$e[0]);
        $f=str_replace(array('b','c','a','f','m','n','t','o','x','q'),array(0,1,2,4,5,6,7,8,9,3),$e[1]);
        $f=(float)$f;
        return $f;
    }
    /**
     * 数字加密
     * @param $v
     * @return string
     */
    protected function base64encode($v){
        //加入乱字符开始
        $b=str_replace(array(0,1,2,4,5,6,7,8,9,3),array('b','c','a','f','m','n','t','o','x','q'),$v);
        $c=str_replace(array(0,1,2,4,5,6,7,8,9,3),array('ba','cd','af','df','dm','cdn','fdt','sso','ewx','ayq'),$v);
        $a=str_replace(array(0,1,2,4,5,6,7,8,9,3),array('sed','dwe','sss','ddss','derwm','werw','ghjy','ttrosso','ffs','mnbv'),$v);
        $d=md5('AccountsiD');
        $e=md5('Useridstrunlandorgnetcomcn');
        //结束
        return base64_encode($a.$d.$b.$e.$c);
    }
    /**
     * 微信取商务下的人员信息
     * @return mixed
     */
    public function getWeixinUserInfo(){
        $cache_token=@file_get_contents('./wtoken.txt');
        $token=json_decode($cache_token,true);
        //$token['access_token']='Uz1RF8TqXwjLmUGkhn8LQO29PWZjJSYSCCR0lY2HPXWZ8rPH4plUAqYu51B2Bz1R';
        $url='https://qyapi.weixin.qq.com/cgi-bin/user/get?access_token='.$token['access_token'].'&userid='.$_SESSION['email'];

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
        $output=json_decode($output,true);
        if($output['errmsg']!='ok'){
            $output=array();
        }
        return $output;
    }
    public function dologin($url,$data){
        $ch  = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_HEADER, 0);
        //curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch, CURLOPT_POST, true);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        //curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $output = curl_exec($ch);
        //$this->_logs($output);
        curl_close($ch);
    }

    /**
     * 将字符串按位转ASCII码不足3位前补0不足4位后补A
     * @param $s
     * @return string
     */
    private function askii_encode($s) {
        $temp='';
        for($i=0; $i<strlen($s); $i++) {
            $thisstr=str_pad(ord($s[$i]),3,'0',STR_PAD_LEFT );
            $temp.=str_pad($thisstr,4,'A');
        }
        return trim($temp,'A');
    }

    /**
     * 字符串还原
     * @param $s
     * @return string
     */
    private function askii_decode($s) {
        $tempstr=explode('A',$s);
        return implode('',array_map(function($v){return chr(ltrim($v,0));},$tempstr));
    }
}

