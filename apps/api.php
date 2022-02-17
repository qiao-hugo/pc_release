<?php
/**
 * Created by PhpStorm.
 * User: zd-yf3131
 * Date: 2016/8/5
 * Time: 10:24
 */
error_reporting(0);
/**
 * 生成,解密tokenkey
 * @param $string
 * @param string $operation
 * @param string $key
 * @param int $expiry
 * @return string
 */
function cookiecode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
    $ckey_length = 4;
    $key = md5($key ? $key : md5($_SERVER['REMOTE_ADDR']));
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
    $cryptkey = $keya.md5($keya.$keyc);
    $key_length = strlen($cryptkey);
    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
    $string_length = strlen($string);
    $result = '';
    $box = range(0, 255);
    $rndkey = array();
    for($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }
    for($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }
    for($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }

    if($operation == 'DECODE') {
        if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc.str_replace('=', '', base64_encode($result));
    }

}
/*if(!empty($_GET['register']) && !empty($_GET['productid'])){
    //获取请求产品的key
    //echo $_SERVER['REMOTE_ADDR'];
    echo urlencode(cookiecode($_GET['productid'],''));
}*/
/**
 * curl 请求
 * @param $url
 * @return bool
 */
function curlpost($data){
    $ch  = curl_init();
    curl_setopt($ch, CURLOPT_URL, $data['url']);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	_logs($data['querystr']);
	_logs($data['url']);
    if(isset($data['querystr']) && !empty($data['querystr'])){
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data['querystr']);
    }
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $output = curl_exec($ch);
	_logs($output);
    curl_close($ch);
    return $output;
    /*$userinfo = json_decode($output,true);
    if($userinfo['errcode']==0){
        return true;
    }
    return false;*/
}
if(!empty($_POST['tokenauth'])){
    if($_POST['tokenauth']=='c0b3Ke0Q4c%2BmGXycVaQ%2BUEcbU0ldxTBeeMAgUILM0PK5Q59cEp%2B40n6qUSJiPQ'){
        $corpid = "wx4d2151259aa58eba";
        $Secret ="9n5ih34K5fFxuwAUJRiLhGY_HPvtA9p79VPfA4ltIgdsjTCGQOTWMCF6FEANlg_d";


        /*获取token*/
        $cache_token=@file_get_contents('./wtoken.txt');
        $flag = false;
        if(!empty($cache_token)){
            $tokens = json_decode($cache_token,true);
            if(!empty($tokens)&&isset($tokens['timeout'])&&$tokens['timeout']>time()){
                $flag = true;
            }
        }

        if(false===$flag){
            $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=".$corpid."&corpsecret=".$Secret;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            $output = curl_exec($ch);
            curl_close($ch);
            $data = json_decode($output,true);
            $data['timeout'] = time()+$data['expires_in']-600;
            file_put_contents('./wtoken.txt', json_encode($data));
        }
        $cache_token=@file_get_contents('./wtoken.txt');
        $tokens = json_decode($cache_token,true);
        $access_token = $tokens['access_token'];
        $flag=$_POST['flag'];
        $username=$_POST['username'];
        $email=trim($_POST['email']);
		$mobile=trim($_POST['mobile']);
        $oldemail=trim($_POST['oldemail']);
        $email1=!empty($email)?$email:$oldemail;
        $email2=explode('|',$email1);
        $tempflag=$flag;
        if(!in_array($flag,array(5,6,7))){
            //no send message
            if($_POST['ERPDOIT']!=456321){
                echo '{"success":false,"msg":"not found"}';
                exit;
            }
        }
        if(($flag>1 && $flag<8) || $flag==11){
            if($flag==2 || $flag==3){
                $email2=array($oldemail);
            }
            foreach($email2 as $value){
                $url = "https://qyapi.weixin.qq.com/cgi-bin/user/get?access_token=$access_token&userid=$value";
                $datajson=curlpost(array('url'=>$url));
                $datass=json_decode($datajson,true);
                if($datass['errcode']!=0){
                    $tempflag=100;
                }else{
                    break;
                }
            }
            if($flag==2 && $tempflag==100){
                $tempflag=1;
            }
        }elseif($flag>7 && $flag<11){
            $name=$_POST['departmentname'];
            $name_en=$departmentid=$_POST['departmentid'];
            $departmentid=trim($departmentid,'H');
            $parentid=$_POST['parentid'];
        }
        $flag=$tempflag;
        switch($flag){
            case 1://新建成员
                $departmentid=$_POST['departmentid'];
                $departmentid=trim($departmentid,'H');
                $url = "https://qyapi.weixin.qq.com/cgi-bin/user/create?access_token=$access_token";
                $querystr='{"userid": "'.$email.'","name": "'.$username.'","department": ['.$departmentid.'],"email": "'.$email.'","mobile": "'.$mobile.'"}';
                echo curlpost(array('url'=>$url,'querystr'=>$querystr));
                break;
            case 2://更新成员
                echo '{"errcode":1,"errmsg":"该功能已禁用请联系统管理员！"}';
                break;
                //$url = "https://qyapi.weixin.qq.com/cgi-bin/user/get?access_token={$access_token}&userid={$oldemail}";
                //if(curlget($url)){
                //直接删除然后重建
                $url = "https://qyapi.weixin.qq.com/cgi-bin/user/delete?access_token=$access_token&userid=$oldemail";
                curlpost(array('url'=>$url));
                //}
                $departmentid=$_POST['departmentid'];
                $url = "https://qyapi.weixin.qq.com/cgi-bin/user/create?access_token={$access_token}";
                $querystr='{"userid": "'.$email.'","name": "'.$username.'","department": ['.$departmentid.'],"email": "'.$email.'","mobile": "'.$mobile.'"}';
                echo curlpost(array('url'=>$url,'querystr'=>$querystr));
                break;
            case 3://删除成员
                $url = "https://qyapi.weixin.qq.com/cgi-bin/user/delete?access_token=$access_token&userid=$oldemail";
                echo curlpost(array('url'=>$url));
                $sessionid=trim($_POST['sessionid']);
                if($sessionid!=1){
                    session_id($sessionid);
                    session_start();
                    session_destroy();
                }
                break;
            case 4://更新用户
                $departmentid=$_POST['departmentid'];
                $departmentid=trim($departmentid,'H');
                $url = "https://qyapi.weixin.qq.com/cgi-bin/user/update?access_token={$access_token}";
                $querystr='{"userid": "'.$email.'","name": "'.$username.'","department": ['.$departmentid.']}';
                echo curlpost(array('url'=>$url,'querystr'=>$querystr));
                break;
            case 5://获取成员信息
                $url = "https://qyapi.weixin.qq.com/cgi-bin/user/get?access_token=$access_token&userid=$oldemail";
                echo curlpost(array('url'=>$url));
                break;
			case 6://微信消息提醒
				$content=$_POST['content'];
                $url = "https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token={$access_token}";
                $querystr='{"touser": "'.$email.'","toparty": "","totag": "","msgtype": "text","agentid": 0,"text": {"content": "'.$content.'"},"safe":0}';
                echo curlpost(array('url'=>$url,'querystr'=>$querystr));
                break;
            case 7://微信消息提醒卡片提示
                $content=$_POST['content'];
                $title=$_POST['title'];
                $description=$_POST['description'];
                $btntxt=!empty($_POST['btntxt'])?$_POST['btntxt']:'详情';
                $dataurl=$_POST['dataurl'];
                $url = "https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token={$access_token}";
                $querystr='{"touser": "'.$email.'","toparty": "","totag": "","msgtype": "textcard","agentid": 0,"textcard": {"title": "'.$title.'","description":"'.$description.'","url":"'.$dataurl.'","btntxt":"'.$btntxt.'"}}';
                echo curlpost(array('url'=>$url,'querystr'=>$querystr));
                /**
                 * 参数	是否必须	说明
                touser	否	成员ID列表（消息接收者，多个接收者用‘|’分隔，最多支持1000个）。特殊情况：指定为@all，则向关注该企业应用的全部成员发送
                toparty	否	部门ID列表，多个接收者用‘|’分隔，最多支持100个。当touser为@all时忽略本参数
                totag	否	标签ID列表，多个接收者用‘|’分隔，最多支持100个。当touser为@all时忽略本参数
                msgtype	是	消息类型，此时固定为：textcard
                agentid	是	企业应用的id，整型。企业内部开发，可在应用的设置页面查看；第三方服务商，可通过接口 获取企业授权信息 获取该参数值
                title	是	标题，不超过128个字节，超过会自动截断
                description	是	描述，不超过512个字节，超过会自动截断
                url	是	点击后跳转的链接。
                btntxt	否	按钮文字。 默认为“详情”， 不超过4个文字，超过自动截断。
                 */
                break;
            case 8://create department
                $url = "https://qyapi.weixin.qq.com/cgi-bin/department/create?access_token=$access_token";
                $querystr='{"name": "'.$name.'", "name_en": "'.$name_en.'","parentid": '.$parentid.',"id": '.$departmentid.'}';
                echo curlpost(array('url'=>$url,'querystr'=>$querystr));
                break;
            case 9://modifity department
                $url = "https://qyapi.weixin.qq.com/cgi-bin/department/update?access_token=$access_token";
                $querystr='{"id": '.$departmentid.',"name": "'.$name.'","name_en": "'.$name_en.'","parentid": '.$parentid.'}';
                echo curlpost(array('url'=>$url,'querystr'=>$querystr));
                break;
            case 10://deleted department
                $newdepartmentid=$_POST['newdepartmentid'];
                $newdepartmentid=trim($newdepartmentid,'H');
                $url='https://qyapi.weixin.qq.com/cgi-bin/department/list?access_token='.$access_token.'&id='.trim($departmentid,'H');
                $returnData=curlpost(array('url'=>$url));
                $jsonData=json_decode($returnData,true);
                if(isset($jsonData['errcode']) && $jsonData['errcode']==0){
                    $url = "https://qyapi.weixin.qq.com/cgi-bin/department/update?access_token=$access_token";
                    if(!empty($jsonData['department'])){
                        foreach($jsonData['department'] as $value){
                            if($value['parentid']==$departmentid){
                                $querystr='{"id": '.$value['id'].',"name": "'.$value['name'].'","parentid": '.$newdepartmentid.'}';
                                curlpost(array('url'=>$url,'querystr'=>$querystr));
                            }
                        }
                    }
                    $url='https://qyapi.weixin.qq.com/cgi-bin/user/simplelist?access_token='.$access_token.'&department_id='.trim($departmentid,'H').'&fetch_child=0';
                    $returnData=curlpost(array('url'=>$url));
                    $jsonData=json_decode($returnData,true);
                    if($jsonData['errcode']==0){
                        if(!empty($jsonData['userlist'])){
                            $url='https://qyapi.weixin.qq.com/cgi-bin/user/update?access_token='.$access_token;
                            foreach($jsonData['userlist'] as $value){
                                $querystr='{"userid": '.$value['userid'].',"department":['.$newdepartmentid.']}';
                                curlpost(array('url'=>$url,'querystr'=>$querystr));
                            }
                        }
                    }
                    $url = "https://qyapi.weixin.qq.com/cgi-bin/department/delete?access_token=$access_token&id=".$departmentid;
                    echo curlpost(array('url'=>$url));
                }
                break;
            case 11://update users
                $url = "https://qyapi.weixin.qq.com/cgi-bin/user/get?access_token=$access_token&userid=$email";
                $datajson=curlpost(array('url'=>$url));
                $datass=json_decode($datajson,true);
                $departmentid=$_POST['departmentid'];
                $departmentid=trim($departmentid,'H');
                $querystr='{"userid": "'.$email.'","name": "'.$username.'","department": ['.$departmentid.'],"email": "'.$email.'","mobile": "'.$mobile.'"}';
                //curlpost(array('url'=>$url,'querystr'=>$querystr));
                if($datass['errcode']==0){
                    $url='https://qyapi.weixin.qq.com/cgi-bin/user/update?access_token='.$access_token;
                    //$querystr='{"userid": "'.$email.'","department":['.$departmentid.']}';
                    echo curlpost(array('url'=>$url,'querystr'=>$querystr));
                }else{
                    $url = "https://qyapi.weixin.qq.com/cgi-bin/user/create?access_token=$access_token";
                    echo curlpost(array('url'=>$url,'querystr'=>$querystr));
                }

                break;
            case 12://微信图文消息
                $content=$_POST['content'];
                $title=$_POST['title'];
                $description=$_POST['description'];
                $dataurl=$_POST['dataurl'];
                $picurl=$_POST['picurl'];
                $url = "https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token={$access_token}";
                $querystr='{"touser": "'.$email.'","toparty": "","totag": "","msgtype": "news","agentid": 0,"enable_duplicate_check":1,"duplicate_check_interval":10,"news": {"articles": [{"title":"'.$title.'","description":"'.$description.'","url":"'.$dataurl.'","picurl":"'.$picurl.'"}]}}';
                echo curlpost(array('url'=>$url,'querystr'=>$querystr));
                break;
            default :

                break;
        }

    }
    exit;
}
function _logs($data, $file = 'logs_weix'){
		$year	= date("Y");
		$month	= date("m");
		$dir	= './Logs/' . $year . '/' . $month . '/';
		if(!is_dir($dir)) {
			mkdir($dir,0755,true);
		}
		$file = $dir . $file . date('Y-m-d').'.txt';
		@file_put_contents($file, '----------------' . date('H:i:s') . '--------------------'.PHP_EOL.var_export($data,true).PHP_EOL, FILE_APPEND);
	}
