<?php
/**
 * 批量删除已离职员工的企业微信账号
 */
ini_set("include_path", "../");

require_once('config.php');
require_once('include/utils/utils.php');
require_once('include/logging.php');
header("Content-type:text/html;charset=utf-8");

include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';

global $adb;
function curl_request($url, $data = null, $curlset=array()) {
    $curl = curl_init();
    if(!empty($curlset)) {
        foreach($curlset as $key=>$value) {
            curl_setopt($curl, $key, $value);
        }
    }
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if (!empty($data)) {
        curl_setopt($curl, CURLOPT_POST, 1);//post提交方式
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}
$corpid = "wx4d2151259aa58eba";
$Secret ="9n5ih34K5fFxuwAUJRiLhGY_HPvtA9p79VPfA4ltIgdsjTCGQOTWMCF6FEANlg_d";

/*获取token*/
$cache_token = @file_get_contents('./wtoken.txt');
$flag = false;
if(!empty($cache_token)) {
    $tokens = json_decode($cache_token,true);
    if(!empty($tokens)&&isset($tokens['timeout'])&&$tokens['timeout']>time()){
        $flag = true;
    }
}

if (false===$flag) {
    $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=".$corpid."&corpsecret=".$Secret;
    $output = curl_request($url);
    $tokens = [];
    if ($output) {
        $tokens = json_decode($output,true);
        $tokens['timeout'] = time() + $data['expires_in'] - 600;
    }
    file_put_contents('./wtoken.txt', json_encode($tokens));
}

$access_token = $tokens['access_token'];

if (!$access_token) {
    exit('获取微信token失败');
}

$url = 'https://qyapi.weixin.qq.com/cgi-bin/user/simplelist?access_token=' . $access_token . '&department_id=1&fetch_child=1';
$output = curl_request($url);
$tokens = [];
if (!$output) {
    exit('获取企业微信员工列表失败');
}
$data = json_decode($output,true);
if (!isset($data['userlist'])) {
    exit('获取企业微信员工列表失败');
}
$userlist = $data['userlist'];
$userlist = array_column($userlist, null, 'userid');
$strEmalis = "'" . implode("','", array_keys($userlist)) . "'";
$query = 'SELECT id, email1, isdimission FROM vtiger_users WHERE email1 IN('.$strEmalis.')';
$result = $adb->run_query_allrecords($query);
foreach ($result as $item) {
    if ($item['isdimission'] != '1') {
        unset($userlist[$item['email1']]);
    }
}
echo '待删除企微账号：<br>';
foreach ($userlist as $key=>$item) {
    echo $item['name']. '      '. $key.'<br>';
}
exit;