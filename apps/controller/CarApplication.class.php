<?php
/**
 * 用车申请
 * @author gaocl
 *
 */
class CarApplication extends baseapp
{
    //用车申请
    public function add()
    {
    	require_once "jssdk.php";
    	$jssdk = new JSSDK("wx74d59c197d3976ee", "8afc371fd3c51ee97d3d8f93647fe219");
    	$signPackage = $jssdk->GetSignPackage();
    	$this->smarty->assign('signPackage',$signPackage);

    	$this->smarty->assign('department_name', $_SESSION['departmentname']?$_SESSION['departmentname']:'');
        $this->smarty->assign('user_name', $_SESSION['last_name']?$_SESSION['last_name']:'');
    	$this->smarty->assign('userid', $this->userid);
        $this->smarty->display('CarApplication/add.html');
    }


    /**
     * 保存用车信息
     */
    public function ajaxSaveCarApplication(){
        $url ="http://192.168.44.222/app/addApplication";
        //$url ="http://192.168.40.239:8080/app/addApplication";
        $pramData =new Application();

        $pramData->department = $_REQUEST['department'];
        $pramData->user = $_REQUEST['user'];
        $pramData->reason = $_REQUEST['reason'];
        $pramData->startTime = $_REQUEST['startTime'];
        $pramData->endTime = $_REQUEST['endTime'];
        $pramData->startPlace = $_REQUEST['startPlace'];
        $pramData->destination = $_REQUEST['destination'];
        $pramData->userid = $this->userid;//创建者id

        $postData = http_build_query($pramData);//传参数

        $this->_logs(array('用车申请接口参数：', $postData));
        $res = $this->https_request($url, $postData);

        $this->_logs(array('用车申请接口返回结果：', $res));
        if($res = "success") {
            echo json_encode(array('success'=>true,'message'=>'申请成功'));
        }else{
            echo json_encode(array('success'=>false,'message'=>'申请失败'));
       }
    	exit();
    }

    public function https_request($url, $data = null){
        $curl = curl_init();
        //curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type:application/x-www-form-urlencoded"));
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);//post提交方式
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        $this->_logs(array("返回处理结果：", $output));
        curl_close($curl);
        return $output;
    }
}
class Application{
    var $department;
    var $user;
    var $userid;
    var $reason;
    var $startTime;
    var $endTime;
    var $startPlace;
    var $destination;
}