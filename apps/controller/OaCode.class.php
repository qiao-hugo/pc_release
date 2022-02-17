<?php
/**
 * OA系统接口使用
 * @author gaochunli
 * @Date 2019/1/30
 */
class OaCode extends baseapp
{
    public function index(){
        $this->smarty->display('OaCode/index.html');
    }
    //证券
    public function negotiableSecurities(){
        $this->smarty->display('NegotiableSecurities/index.html');
    }
    public function oaMobile(){
        $m = $_REQUEST['method'];
        $this->_cs_logs("参数:".$m);
        $body = file_get_contents('php://input');
        $this->_cs_logs("参数1:".$body);
        $res = $this->https_request("http://192.168.40.88:8080/".$m, $body);
        echo $res;
    }

    private function https_request($url, $data = null){
        $curl = curl_init();
        $this->_cs_logs($url);
        $header=array();
        $header[]='loginId:'.$_SESSION['customer_id'];
        $header[]='reportCode:'.$_SESSION['reports_to_id'];
        $header[]='username:'.$_SESSION['customer_name'];
        $header[]='fullName:'.$this->UnicodeEncode($_SESSION['last_name']);
        $header[]='roleId:'.$_SESSION['roleid'];
        $header[]='departId:'.$_SESSION['departmentid'];
        $header[]="Content-type:application/json;charset=utf-8";
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

        //if (!empty($data)){
        curl_setopt($curl, CURLOPT_POST, 1);//post提交方式
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        //}
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        $this->_cs_logs("返回结果:".$output);
        curl_close($curl);
        return $output;
    }

    /**
     * 写日志，用于测试,可以开启关闭
     * @param data mixed
     */
    private function _cs_logs($data, $file = 'logs_'){
        $year	= date("Y");
        $month	= date("m");
        $dir	= './logs/oa/' . $year . '/' . $month . '/';
        if(!is_dir($dir)) {
            mkdir($dir,0755,true);
        }
        $file = $dir . $file . date('Y-m-d').'.txt';
        @file_put_contents($file, '----------------' . date('H:i:s') . '--------------------'.PHP_EOL.var_export($data,true).PHP_EOL, FILE_APPEND);
    }
    public function UnicodeEncode($str)
    {
        //split word
        preg_match_all('/./u', $str, $matches);

        $unicodeStr = "";
        foreach ($matches[0] as $m) {
            //拼接
            $unicodeStr .= "&#" . base_convert(bin2hex(iconv('UTF-8', "UCS-4", $m)), 16, 10);
        }
        return $unicodeStr;
    }
}
