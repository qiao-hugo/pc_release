<?php

class UserInfoAPI extends baseapi
{
    public function getUserInfo()
    {
        $email = $_REQUEST['email'];
        if(!$email){
            echo json_encode(array('success' => 'false', 'code' => 200, 'msg' => '没有相关信息!'), JSON_UNESCAPED_UNICODE);
        }

        $params = array(
            'fieldname' => array(
                'module' => 'Users',
                'action' => 'getUserByEmail',
                'email' => $email,
            ),
            'userid' => 0
        );
        $res = $this->call('getComRecordModule', $params);
        $this->_logs(array("返回结果(getUserInfo)：", $res));
        if (!empty($res[0])) {
            echo json_encode(array('success' => 'true', 'code' => 200, 'data' => $res[0]), JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(array('success' => 'false', 'code' => 200, 'msg' => '没有相关信息!'.$res), JSON_UNESCAPED_UNICODE);
        }
    }
    /**
     * 单点登陆调用
     */
    public function userlogin(){
        $loginname=$_REQUEST['loginname'];
        $password=$_REQUEST['password'];
        $params = array(
            'fieldname' => array(
                'module' => 'Users',
                'action' => 'userlogin',
                'loginname' => $loginname,
                'password' => $password,
            ),
            'userid' => 0
        );
        $res = $this->call('getComRecordModule', $params);
        if (!empty($res[0]['success'])) {
            echo json_encode(array('success' => 'true', 'code' => 200, 'data' => $res[0]['data']), JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(array('success' => 'false', 'code' => 200, 'msg' => '没有相关信息!'), JSON_UNESCAPED_UNICODE);
        }
    }
    public function getUserInfoByUcodeUname(){
        $fullname=$_REQUEST['fullname'];
        $ucode=$_REQUEST['ucode'];
        $params = array(
            'fieldname' => array(
                'module' => 'Users',
                'action' => 'getUserInfoByUcodeUname',
                'lastname' => $fullname,
                'usercode' => $ucode,
            ),
            'userid' => 0
        );
        $res = $this->call('getComRecordModule', $params);
        if (!empty($res[0]['success'])) {
            echo json_encode(array('success' => 'true', 'code' => 200, 'data' => $res[0]['data']), JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(array('success' => 'false', 'code' => 200, 'msg' => '没有相关信息!'), JSON_UNESCAPED_UNICODE);
        }
    }
    /**
     * 获取用户ID，上级ID，姓名，等的信息
     * @return array|bool|false|mixed|string
     */
    public function getALLUserINFO(){
        $params = array(
            'fieldname' => array(
                'module' => 'Users',
                'action' => 'getALLUserINFO'
            ),
            'userid' => 0
        );
        $res = $this->call('getComRecordModule', $params);
        if (!empty($res[0])) {
            echo json_encode(array('success' => 'true', 'code' => 200, 'data'=>json_decode($res[0],true)), JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(array('success' => 'false', 'code' => 200, 'msg' => '没有相关信息!'), JSON_UNESCAPED_UNICODE);
        }
    }
    /**
     * 修改密码
     * @return array|bool|false|mixed|string
     */
    public function changePasswd(){
        $token=$_REQUEST['token'];
        if($token=='aasdf34341221380432sdwewe2234121'){
            $params = array(
                'fieldname' => array(
                    'module' => 'Users',
                    'action' => 'changePasswd',
                    'userid'=>$_REQUEST['userid'],
                    'password'=>$_REQUEST['password'],
                    'oldpassword'=>$_REQUEST['oldpassword']
                ),
                'userid' => 0
            );
            $res = $this->call('getComRecordModule', $params);
            if (!empty($res[0])) {
                echo json_encode($res[0], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(array('success' => 'false', 'msg' => '没有相关信息!'), JSON_UNESCAPED_UNICODE);
            }
        }
    }

    /**
     * 获取企业微信部门信息
     */
    public function getQYWXdepartment(){
        set_time_limit(0);
        $this->checkToken('13anljle34324llao2on32zopaqmvaekj98jjo34943240jljljflj321');
        $token=$this->getQYWXToken();
        $URL='https://qyapi.weixin.qq.com/cgi-bin/department/list?access_token='.$token.'&id=1';
        echo $this->https_request($URL);
    }

    /**
     * 企业微信员工信息
     */
    public function getQYWXAllUser(){
        set_time_limit(0);
        $this->checkToken('13anljle34324llao2on32zopaqmvaekj98jjo34943240jljljflj321');
        $token=$this->getQYWXToken();
        $URL='https://qyapi.weixin.qq.com/cgi-bin/user/list?access_token='.$token.'&department_id=1&fetch_child=1';
        echo $this->https_request($URL);
    }

    /**
     * 获取公司信息
     */
    public function getAllCompany(){
        $params = array(
            'fieldname' => array(
                'module' => 'Users',
                'action' => 'getAllCompany'
            ),
            'userid' => 0
        );
        $res = $this->call('getComRecordModule', $params);
        $this->_logs(array("返回结果(getUserInfo)：", $res));
        if (!empty($res[0])) {
            echo json_encode($res[0], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(array('success' => 'false','msg' => '没有相关信息!'), JSON_UNESCAPED_UNICODE);
        }
    }
}