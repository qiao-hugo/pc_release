<?php
$url = 'http://www.baidu.com/';

 

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);

curl_setopt($ch, CURLOPT_TIMEOUT, 30);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

curl_exec($ch);  // $resp = curl_exec($ch);

$curl_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

 

if ($curl_code == 200) {

    echo '连接成功，状态码：' . $curl_code;

} else {

    echo '连接失败，状态码：' . $curl_code;

}