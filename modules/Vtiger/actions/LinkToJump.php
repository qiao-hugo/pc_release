<?php
/*+************
 * 独立文件上传
 *20141222
 **************/

class Vtiger_LinkToJump_Action extends Vtiger_Save_Action {



	public function checkPermission(Vtiger_Request $request) {
	    return true;
	}

	public function process(Vtiger_Request $request) {
	    global $new_gateway_url,$new_gateway_appId,$new_gateway_appSecret;
	    $type=$request->get('type');
        $json=file_get_contents($new_gateway_url.'/getAccessToken?appId='.$new_gateway_appId.'&appSecret='.$new_gateway_appSecret);
        $jsonArray=json_decode($json,true);
        if($jsonArray['errcode']=='200'&&isset($jsonArray['data']['access_token'])){
            $access_token=$jsonArray['data']['access_token'];
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $new_gateway_url.'/tcloud-account/suite/erpAuthLogin?access_token='.$access_token,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>'{"token": "'.$_SESSION['vt_param'].'","type":'.$type.'}',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            $responseArray=json_decode($response,true);
            if($responseArray['errcode']=='200'&&isset($responseArray['data']['url'])){
                Matchreceivements_Record_Model::recordLog($responseArray,'token');
                if($responseArray['data']['token']){
                    Vtiger_Session::set('X-Token', $responseArray['data']['token']);
                    date_default_timezone_set('Asia/Shanghai');
                    setcookie("X-Token",$responseArray['data']['token'],time()+3600*24,NULL,'.71360.com');
                }
                Matchreceivements_Record_Model::recordLog($responseArray['data']['url'].'?token='.$responseArray['data']['token'],'token');
                header('Location: '.$responseArray['data']['url'].'?token='.$responseArray['data']['token']);
            }else{
                echo $responseArray['errmsg'];
            }
        }else{
            echo $jsonArray['errmsg'];
        }
        exit();
	}
}
