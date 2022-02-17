<?php
ini_set('display_errors','on'); error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
set_time_limit(0);
ini_set("include_path", "../");
require_once('include/utils/utils.php');
require_once('include/logging.php');

global $dbconfig;
$db_port=ltrim($dbconfig['db_port'],":");
$corpid='wx4d2151259aa58eba';
$Secret='9n5ih34K5fFxuwAUJRiLhGY_HPvtA9p79VPfA4ltIgdsjTCGQOTWMCF6FEANlg_d';
$url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=".$corpid."&corpsecret=".$Secret;
$tokenJsonData=http_request($url);
$tokenData=json_decode($tokenJsonData,true);
echo $tokenJsonData;
if(isset($tokenData['errcode']) && $tokenData['errcode']==0){

}else{
    exit;
}
$url='https://qyapi.weixin.qq.com/cgi-bin/user/list?access_token='.$tokenData['access_token'].'&department_id=1&fetch_child=1&status=1';
$usersJsonData=http_request($url);
$usersData=json_decode($usersJsonData,true);
if(isset($usersData['errcode']) && $usersData['errcode']==0 && !empty($usersData['userlist'])){
    echo 'start.....';
    $mysql = mysqli_connect($dbconfig['db_server'],$dbconfig['db_username'],$dbconfig['db_password'],$dbconfig['db_name'],$db_port);
    $mysql->query("truncate table vtiger_wexinpicture");
    foreach($usersData['userlist'] as $value){
        $avatar=$value['avatar']==''?1:$value['avatar'];
        $sql="INSERT INTO vtiger_wexinpicture(userid,picturepath) SELECT id,'".$avatar."' FROM vtiger_users WHERE `status`='Active' AND LOWER(email1)='".strtolower($value['userid'])."'";
        $result=$mysql->query($sql);
    }
    //$result->close();
    $mysql->close();
    echo 'end';
}else{
    exit;
}
function http_request($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

?>
