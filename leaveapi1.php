<?php
/*
 * 请假系统使用接口
 * @Author gaochunli
 * @Version V1.0.0
 * @Date 2018/8/25
 */
header("Content-type:text/html;charset=utf-8");
error_reporting(0);
$_REQUEST['testbak']=11;
require('include/utils/UserInfoUtil.php');

if(!empty($_GET['register']) && !empty($_GET['productid'])) {
    //获取请求产品的key
    echo urlencode(cookiecode($_GET['productid'], ''));
    exit;
}
if(empty($_GET['tokenauth'])){
    $lists=array('success'=>false,'msg'=>'请先生成token',JSON_UNESCAPED_UNICODE);
    echo json_encode($lists);
    exit;
}
$cs_method = explode(',',urldecode(cookiecode($_GET['tokenauth'],'DECODE')));
_cs_logs(array("客服系统调用接口方法：".json_encode($cs_method)));
//=================登录信息API================================================================
if(is_array($cs_method) && $cs_method[0]=='cslogin') {
    _cs_logs(array("客服登录接口开始"));
    global $adb;

    $reusltdata = $_REQUEST["data"];
    _cs_logs(array("客服登录接口参数:".$resultdata));
    $reusltdata = trim($reusltdata);
    $reusltdata = str_replace(' ', '+', $reusltdata);
    $decrdata = decrypt($reusltdata);
    $pad = strrpos($decrdata, '}');
    $decrdata = substr($decrdata, 0, $pad + 1);
    $decodedata = json_decode($decrdata, true);
    $user_name=$decodedata['loginname'];
    $user_password=$decodedata['password'];
    _cs_logs(array("客服登录接口解析后参数:".$decrdata));
    $password = encrypt_password($user_name, $user_password, 'PHP5.3MD5');
    $sql = 'SELECT vtiger_users.id AS userId,vtiger_users.user_name AS username,vtiger_users.last_name AS fullname,vtiger_users.is_admin AS isadmin,
            vtiger_user2role.roleid,vtiger_role.rolename,
            vtiger_user2department.departmentid,vtiger_departments.departmentname
            FROM vtiger_users 
            LEFT JOIN vtiger_user2department ON(vtiger_users.id=vtiger_user2department.userid)
            LEFT JOIN vtiger_user2role ON(vtiger_users.id=vtiger_user2role.userid)
            LEFT JOIN vtiger_role ON(vtiger_role.roleid=vtiger_user2role.roleid)
            LEFT JOIN vtiger_departments ON(vtiger_user2department.departmentid=vtiger_departments.departmentid)
            WHERE vtiger_users.`status`=\'Active\' AND vtiger_users.isdimission=0 AND vtiger_users.user_name=? AND vtiger_users.user_password=?';
    _cs_logs(array("客服登录接口:".$user_name.'|'.$password));
    $sales = $adb->pquery($sql, array($user_name, $password));
    $rows = $adb->num_rows($sales);
    if ($rows) {
        _cs_logs(array("验证成功,登录人:".$result['last_name']));
        $lists = array();
        $result = $adb->query_result_rowdata($sales, 0);
        $data=json_encode($result);
        //$data=json_encode($result,JSON_UNESCAPED_UNICODE);
        $data=encrypt($data);
        echo json_encode(array("success"=>true,"data"=>$data));
        //echo json_encode(array("success"=>true,"data"=>$result));
    } else {
        _cs_logs(array("用户名或密码不正确"));
        $lists = array('success' => false, 'msg' => '用户名或密码不正确');
        echo json_encode($lists, JSON_UNESCAPED_UNICODE);
    }
    _cs_logs(array("客服登录接口结束"));
    exit;
}

//=================获取合同信息API================================================================
if(is_array($cs_method) && $cs_method[0]=='getUserList') {
    _cs_logs(array("根据客户获取用户列表接口开始"));

    include_once 'vtlib/Vtiger/Module.php';
    include_once 'includes/main/WebUI.php';
    include_once  'languages/zh_cn/Accounts.php';
    vglobal('default_language', $default_language);
    $currentLanguage = 'zh_cn';
    //Vtiger_Language_Handler::getLanguage();//2.语言设置
    vglobal('current_language',$currentLanguage);
    global $adb;

    $query="SELECT
                vtiger_users.user_name,
                vtiger_users.is_admin,
                vtiger_users.last_name,
                vtiger_user2role.roleid,
                vtiger_users.email1,
                vtiger_users.`status`,
                vtiger_users.title,
                vtiger_users.phone_work,
                vtiger_users.department,
                vtiger_users.phone_mobile,
                vtiger_users.reports_to_id,
                vtiger_users.phone_other,
                vtiger_users.email2,
                vtiger_users.phone_fax,
                vtiger_users.secondaryemail,
                vtiger_users.phone_home,
                vtiger_users.address_city,
                vtiger_users.address_state,
                vtiger_users.address_postalcode,
                vtiger_users.user_sys,
                vtiger_user2department.departmentid,
                vtiger_user2role.secondroleid,
                vtiger_users.usermodifiedtime,
                vtiger_users.usercode,
                vtiger_users.user_entered,
                vtiger_users.fillinsales,
                vtiger_users.brevitycode,
                vtiger_users.leavedate,
                vtiger_users.isdimission,
                vtiger_users.companyid,
                vtiger_users.id
            FROM
                vtiger_users
            INNER JOIN vtiger_user2role ON vtiger_users.id = vtiger_user2role.userid
            INNER JOIN vtiger_user2department ON vtiger_users.id = vtiger_user2department.userid
            WHERE
                vtiger_users.id >0
            AND departmentid != ''
            AND ((vtiger_users.`status`='Active') OR (leavedate>=DATE_SUB(CURDATE(), INTERVAL 3 MONTH)))
            ";
            //AND `status`='Active'";
    $sales = $adb->pquery($query, array());
    _cs_logs(array("执行用户列表查询sql：".$query));
    $rows = $adb->num_rows($sales);
    $ret_lists = array();
    if ($rows>0) {
        while($row=$adb->fetchByAssoc($sales)){
            $lists = array();
            $lists['user_name']=$row['user_name'];
            $lists['is_admin']=$row['is_admin'];
            $lists['last_name']=$row['last_name'];
            $lists['email']=$row['email1'];
            $lists['status']=$row['status'];
            $lists['title']=$row['title'];
            $lists['phone_work']=$row['phone_work'];
            $lists['department']=$row['department'];
            $lists['phone_mobile']=$row['phone_mobile'];
            $lists['department']=$row['department'];
            $lists['reports_to_id']=$row['reports_to_id'];
            $lists['phone_other']=$row['phone_other'];
            $lists['usercode']=$row['usercode'];
            $lists['user_entered']=$row['user_entered'];
            $lists['brevitycode']=$row['brevitycode'];
            $lists['leavedate']=$row['leavedate'];
            $lists['departmentid']=$row['departmentid'];
            $lists['isdimission']=$row['isdimission'];
            $lists['inactive']=$row['status'];
            $lists['companyid']=$row['companyid'];
            $lists['id']=$row['id'];
            $ret_lists[]=$lists;
        }
        //$data=json_encode($ret_lists,JSON_UNESCAPED_UNICODE );
        $data=json_encode($ret_lists);
        $data=encrypt($data);
        echo json_encode(array("success"=>true,"data"=>$data));
        //echo json_encode(array("success"=>true,"data"=>$ret_lists));
        _cs_logs(array("根据客户获取合同列表成功"));
    }else{
        /*_cs_logs(array("没有客户的合同信息"));
        $lists=array('success'=>false,'msg'=>'没有客户的合同信息');
        echo json_encode($lists,JSON_UNESCAPED_UNICODE);*/
        echo json_encode(array("success"=>false,"msg"=>'没有相关信息'));
    }
    _cs_logs(array("根据客户获取合同列表接口结束"));
    exit;
}
if(is_array($cs_method) && $cs_method[0]=='getUserListForName') {
    _cs_logs(array("用户名模糊查询列表接口开始"));

    include_once 'vtlib/Vtiger/Module.php';
    include_once 'includes/main/WebUI.php';
    include_once  'languages/zh_cn/Accounts.php';
    vglobal('default_language', $default_language);
    $currentLanguage = 'zh_cn';
    //Vtiger_Language_Handler::getLanguage();//2.语言设置
    vglobal('current_language',$currentLanguage);
    global $adb;
    //$last_name = $_GET['username'];
    $last_name = $_GET['usercode'];
    $last_name=str_pad($last_name,6,'0',STR_PAD_LEFT);
    $query="SELECT
                vtiger_users.user_name,
                vtiger_users.is_admin,
                vtiger_users.last_name,
                vtiger_user2role.roleid,
                vtiger_users.email1,
                vtiger_users.`status`,
                vtiger_users.title,
                vtiger_users.phone_work,
                vtiger_users.department,
                vtiger_users.phone_mobile,
                vtiger_users.reports_to_id,
                vtiger_users.phone_other,
                vtiger_users.email2,
                vtiger_users.phone_fax,
                vtiger_users.secondaryemail,
                vtiger_users.phone_home,
                vtiger_users.address_city,
                vtiger_users.address_state,
                vtiger_users.address_postalcode,
                vtiger_users.user_sys,
                vtiger_user2department.departmentid,
                vtiger_user2role.secondroleid,
                vtiger_users.usermodifiedtime,
                vtiger_users.usercode,
                vtiger_users.user_entered,
                vtiger_users.fillinsales,
                vtiger_users.brevitycode,
                vtiger_users.leavedate,
                vtiger_users.isdimission,
                vtiger_users.companyid,
                vtiger_users.id
            FROM
                vtiger_users
            INNER JOIN vtiger_user2role ON vtiger_users.id = vtiger_user2role.userid
            INNER JOIN vtiger_user2department ON vtiger_users.id = vtiger_user2department.userid
            WHERE
                vtiger_users.id >0
            AND departmentid != ''
            AND usercode=?
            AND `status`='Active'";
    $sales = $adb->pquery($query, array($last_name));
    _cs_logs(array("执行用户列表查询sql：".$query));
    $rows = $adb->num_rows($sales);
    $ret_lists = array();
    if ($rows>0) {
        while($row=$adb->fetchByAssoc($sales)){
            $lists = array();
            $lists['user_name']=$row['user_name'];
            $lists['is_admin']=$row['is_admin'];
            $lists['last_name']=$row['last_name'];
            $lists['email']=$row['email1'];
            $lists['status']=$row['status'];
            $lists['title']=$row['title'];
            $lists['phone_work']=$row['phone_work'];
            $lists['department']=$row['department'];
            $lists['phone_mobile']=$row['phone_mobile'];
            $lists['department']=$row['department'];
            $lists['reports_to_id']=$row['reports_to_id'];
            $lists['phone_other']=$row['phone_other'];
            $lists['usercode']=$row['usercode'];
            $lists['user_entered']=$row['user_entered'];
            $lists['brevitycode']=$row['brevitycode'];
            $lists['leavedate']=$row['leavedate'];
            $lists['isdimission']=$row['isdimission'];
            $lists['departmentid']=$row['departmentid'];
            $lists['companyid']=$row['companyid'];
            $lists['id']=$row['id'];
            $ret_lists[]=$lists;
        }
        //$data=json_encode($ret_lists,JSON_UNESCAPED_UNICODE );
        $data=json_encode($ret_lists);
        $data=encrypt($data);
        echo json_encode(array("success"=>true,"data"=>$data));
        //echo json_encode(array("success"=>true,"data"=>$ret_lists));
        _cs_logs(array("用户名模糊查询列表接口开始"));
    }else{
        /*_cs_logs(array("没有客户的合同信息"));
        $lists=array('success'=>false,'msg'=>'没有客户的合同信息');
        echo json_encode($lists,JSON_UNESCAPED_UNICODE);*/
        echo json_encode(array("success"=>false,"msg"=>'没有相关信息'));
    }
    _cs_logs(array("用户名模糊查询列表接口结束"));
    exit;
}
if(is_array($cs_method) && $cs_method[0]=='getHoliday') {
    _cs_logs(array("根据客户获取用户列表接口开始"));

    include_once 'vtlib/Vtiger/Module.php';
    include_once 'includes/main/WebUI.php';
    include_once  'languages/zh_cn/Accounts.php';
    vglobal('default_language', $default_language);
    $currentLanguage = 'zh_cn';
    //Vtiger_Language_Handler::getLanguage();//2.语言设置
    vglobal('current_language',$currentLanguage);
    global $adb;
    $year = $_GET['year'];
    $query="SELECT workdayid,dateday FROM `vtiger_workday` WHERE datetype='holiday' AND left(workdayid,4)=?";
    $sales = $adb->pquery($query, array($year));
    _cs_logs(array("获取节假日列表查询sql：".$query));
    $rows = $adb->num_rows($sales);
    $ret_lists = array();
    if ($rows>0) {
        while($row=$adb->fetchByAssoc($sales)){
            $lists = array();
            $lists['workdayid']=$row['workdayid'];
            $lists['dateday']=$row['dateday'];
            $ret_lists[]=$lists;
        }
        //$data=json_encode($ret_lists,JSON_UNESCAPED_UNICODE );
        $data=json_encode($ret_lists);
        $data=encrypt($data);
        echo json_encode(array("success"=>true,"data"=>$data));
        //echo json_encode(array("success"=>true,"data"=>$ret_lists));
        _cs_logs(array("获取节假日合同列表成功"));
    }else{
        /*_cs_logs(array("没有客户的合同信息"));
        $lists=array('success'=>false,'msg'=>'没有客户的合同信息');
        echo json_encode($lists,JSON_UNESCAPED_UNICODE);*/
        echo json_encode(array("success"=>false,"msg"=>'没有相关信息'));
    }
    _cs_logs(array("获取节假日列表接口结束"));
    exit;
}
//=================获取跟进信息API================================================================
if(is_array($cs_method) && $cs_method[0]=='getUser') {
    _cs_logs(array("获取用户详情信息接口开始"));
    global $adb;
    //$accountid = $_GET['userid'];
    $last_name = $_GET['usercode'];
    $last_name=str_pad($last_name,6,'0',STR_PAD_LEFT);
    _cs_logs(array("获取用户详情信息接口参数：".$last_name));
    $query="SELECT
                vtiger_users.user_name,
                vtiger_users.is_admin,
                vtiger_users.last_name,
                vtiger_user2role.roleid,
                vtiger_users.email1,
                vtiger_users.`status`,
                vtiger_users.title,
                vtiger_users.phone_work,
                vtiger_users.department,
                vtiger_users.phone_mobile,
                (SELECT ou.usercode FROM vtiger_users AS ou WHERE ou.id=vtiger_users.reports_to_id AND ou.`status`='Active' LIMIT 1) AS reports_to_id,
                vtiger_users.phone_other,
                vtiger_users.email2,
                vtiger_users.phone_fax,
                vtiger_users.secondaryemail,
                vtiger_users.phone_home,
                vtiger_users.address_city,
                vtiger_users.address_state,
                vtiger_users.address_postalcode,
                vtiger_users.user_sys,
                vtiger_user2department.departmentid,
                vtiger_user2role.secondroleid,
                vtiger_users.usermodifiedtime,
                vtiger_users.usercode,
                vtiger_users.user_entered,
                vtiger_users.fillinsales,
                vtiger_users.brevitycode,
                vtiger_users.leavedate,
                vtiger_users.isdimission,
                vtiger_users.companyid,
                vtiger_users.id
            FROM
                vtiger_users
            INNER JOIN vtiger_user2role ON vtiger_users.id = vtiger_user2role.userid
            INNER JOIN vtiger_user2department ON vtiger_users.id = vtiger_user2department.userid
            WHERE
                vtiger_users.id >0
            AND departmentid != ''
            AND vtiger_users.usercode=?

";
    $userdata = $adb->pquery($query, array($last_name));

    $rows = $adb->num_rows($userdata);
    $lists = array();
    if ($rows > 0) {
        $row = $adb->query_result_rowdata($userdata,0);
        //跟进内容
        $lists['user_name']=$row['user_name'];
        $lists['is_admin']=$row['is_admin'];
        $lists['last_name']=$row['last_name'];
        $lists['email']=$row['email1'];
        $lists['status']=$row['status'];
        $lists['title']=$row['title'];
        $lists['phone_work']=$row['phone_work'];
        $lists['department']=$row['department'];
        $lists['phone_mobile']=$row['phone_mobile'];
        $lists['department']=$row['department'];
        $lists['reports_to_id']=$row['reports_to_id'];
        $lists['phone_other']=$row['phone_other'];
        $lists['usercode']=$row['usercode'];
        $lists['user_entered']=$row['user_entered'];
        $lists['brevitycode']=$row['brevitycode'];
        $lists['leavedate']=$row['leavedate'];
        $lists['isdimission']=$row['isdimission'];
        $lists['departmentid']=$row['departmentid'];
        $lists['companyid']=$row['companyid'];
        $lists['id']=$row['id'];

        $data = json_encode($lists);
        $data = encrypt($data);
        echo json_encode(array("success" => true, "data" => $data));
        //echo json_encode(array("success" => true, "data" => $lists));
        _cs_logs(array("获取用户详情信息成功"));
    } else {
        _cs_logs(array("没有用户详情信息"));
       /* $lists = array('success' => false, 'msg' => '没有客户的相关跟进信息', JSON_UNESCAPED_UNICODE);
        echo json_encode($lists);*/
        echo json_encode(array("success" => false, "msg" => '没有相关信息'));
    }
    _cs_logs(array("用户详情信息接口结束"));
    exit;
}
//=================获取部门架构API================================================================
if(is_array($cs_method) && $cs_method[0]=='getOrganizational') {
    _cs_logs(array("获取部门组织接口开始"));
    global $adb;
    $lists = array();
    $query1="SELECT vtiger_departments.*,substring_index(substring_index(parentdepartment, '::', -2),'::',1) AS parentid FROM `vtiger_departments` ORDER BY parentdepartment";
    $arr_result=$adb->pquery($query1,array());
    $list_data = array();
    $rows=$adb->num_rows($arr_result);
    if ($rows > 0) {
        while($row=$adb->fetchByAssoc($arr_result)) {
            $list['departmentid'] = $row['departmentid'];
            $list['departmentname'] = $row['departmentname'];
            $list['parentdepartment'] = $row['parentdepartment'];
            $list['parentid'] = $row['parentid'];
            if ($row['departmentid'] == 'H1') {
                $list['parentid'] = 0;
            }
            $lists[]=$list;
        }
        $data = json_encode($lists, JSON_UNESCAPED_UNICODE);
        $data = encrypt($data);
        echo json_encode(array("success" => true, "data" => $data));
        //echo json_encode(array("success" => true, "data" => $lists));
        _cs_logs(array("获取部门组织接口成功的信息"));
    } else {
        _cs_logs(array("没有任何信息部门组织"));
        echo json_encode(array("success" => false, "data" => $lists));
    }
    _cs_logs(array("获取部门组织接口结束"));
    exit;
}
if(is_array($cs_method) && $cs_method[0]=='getOrganizationalForID') {
    _cs_logs(array("获取部门组织接口开始"));
    global $adb;
    $lists = array();
    $departmentid=$_GET['departmentid'];
    $query1="SELECT vtiger_departments.*,substring_index(substring_index(parentdepartment, '::', -2),'::',1) AS parentid FROM `vtiger_departments` WHERE departmentid=? ORDER BY parentdepartment";
    $arr_result=$adb->pquery($query1,array($departmentid));
    $list_data = array();
    $rows=$adb->num_rows($arr_result);
    if ($rows > 0) {
        while($row=$adb->fetchByAssoc($arr_result)) {
            $list['departmentid'] = $row['departmentid'];
            $list['departmentname'] = $row['departmentname'];
            $list['parentdepartment'] = $row['parentdepartment'];
            $list['parentid'] = $row['parentid'];
            if ($row['departmentid'] == 'H1') {
                $list['parentid'] = 0;
            }
            $lists[]=$list;
        }
        $data = json_encode($lists, JSON_UNESCAPED_UNICODE);
        $data = encrypt($data);
        echo json_encode(array("success" => true, "data" => $data));
        //echo json_encode(array("success" => true, "data" => $lists));
        _cs_logs(array("获取部门组织接口成功的信息"));
    } else {
        _cs_logs(array("没有任何信息部门组织"));
        echo json_encode(array("success" => false, "data" => $lists));
    }
    _cs_logs(array("获取部门组织接口结束"));
    exit;
}

//=================根据组织获取员工API================================================================
if(is_array($cs_method) && $cs_method[0]=='getUserForOrganizational') {
    _cs_logs(array("获取员工部门组织接口开始"));
    global $adb;
    $lists = array();
    include_once 'vtlib/Vtiger/Module.php';
    include_once 'includes/main/WebUI.php';
    include_once  'languages/zh_cn/Accounts.php';
    vglobal('default_language', $default_language);
    $currentLanguage = 'zh_cn';
    //Vtiger_Language_Handler::getLanguage();//2.语言设置
    vglobal('current_language',$currentLanguage);
    $departmentid = $_GET['departmentid'];
    $userid=getDepartmentUser($departmentid);
    if(empty($userid)){
        $userid=array(0);
    }
    //跟进目的
    $query="SELECT
                vtiger_users.user_name,
                vtiger_users.is_admin,
                vtiger_users.last_name,
                vtiger_user2role.roleid,
                vtiger_users.email1,
                vtiger_users.`status`,
                vtiger_users.title,
                vtiger_users.phone_work,
                vtiger_users.department,
                vtiger_users.phone_mobile,
                vtiger_users.reports_to_id,
                vtiger_users.phone_other,
                vtiger_users.email2,
                vtiger_users.phone_fax,
                vtiger_users.secondaryemail,
                vtiger_users.phone_home,
                vtiger_users.address_city,
                vtiger_users.address_state,
                vtiger_users.address_postalcode,
                vtiger_users.user_sys,
                vtiger_user2department.departmentid,
                vtiger_user2role.secondroleid,
                vtiger_users.usermodifiedtime,
                vtiger_users.usercode,
                vtiger_users.user_entered,
                vtiger_users.fillinsales,
                vtiger_users.brevitycode,
                vtiger_users.leavedate,
                vtiger_users.isdimission,
                vtiger_user2department.departmentid,
                vtiger_users.companyid,
                vtiger_users.id
            FROM
                vtiger_users
            INNER JOIN vtiger_user2role ON vtiger_users.id = vtiger_user2role.userid
            INNER JOIN vtiger_user2department ON vtiger_users.id = vtiger_user2department.userid
            WHERE
                vtiger_users.id >0
            AND departmentid != ''
            AND `status`='Active'
            AND id in(".implode(',',$userid).")";
    $sales = $adb->pquery($query, array());
    _cs_logs(array("执行用户列表查询sql：".$query));
    $rows = $adb->num_rows($sales);
    $ret_lists = array();
    if ($rows>0) {
        while($row=$adb->fetchByAssoc($sales)){
            $lists = array();
            $lists['user_name']=$row['user_name'];
            $lists['is_admin']=$row['is_admin'];
            $lists['last_name']=$row['last_name'];
            $lists['email']=$row['email1'];
            $lists['status']=$row['status'];
            $lists['title']=$row['title'];
            $lists['phone_work']=$row['phone_work'];
            $lists['department']=$row['department'];
            $lists['phone_mobile']=$row['phone_mobile'];
            $lists['department']=$row['department'];
            $lists['reports_to_id']=$row['reports_to_id'];
            $lists['phone_other']=$row['phone_other'];
            $lists['usercode']=$row['usercode'];
            $lists['user_entered']=$row['user_entered'];
            $lists['brevitycode']=$row['brevitycode'];
            $lists['leavedate']=$row['leavedate'];
            $lists['leavedate']=$row['leavedate'];
            $lists['isdimission']=$row['isdimission'];
            $lists['companyid']=$row['companyid'];
            $lists['id']=$row['id'];
            $ret_lists[]=$lists;
        }
        //$data=json_encode($ret_lists,JSON_UNESCAPED_UNICODE );
        $data=json_encode($ret_lists);
        $data=encrypt($data);
        echo json_encode(array("success"=>true,"data"=>$data));
        //echo json_encode(array("success"=>true,"data"=>$ret_lists));
        _cs_logs(array("根据员工部门组织成功"));
    }else{
        /*_cs_logs(array("没有客户的合同信息"));
        $lists=array('success'=>false,'msg'=>'没有客户的合同信息');
        echo json_encode($lists,JSON_UNESCAPED_UNICODE);*/
        echo json_encode(array("success"=>false,"data"=>$ret_lists));
    }
    _cs_logs(array("根据员工部门组织接口结束"));
    exit;
}
//=================获取员工拜访信息接口API================================================================
if(is_array($cs_method) && $cs_method[0]=='getUserVisitingOrderInfo') {
    _cs_logs(array("获取员工拜访信息接口开始"));
    global $adb;
    $lists = array();
    $currentdate = $_GET['currentdate'];
    $userid = $_GET['userid'];
    //跟进目的
    /*$query="SELECT
                vtiger_visitingorder.startdate,
                vtiger_visitingorder.enddate,
                vtiger_visitsign.userid AS extractid,
                vtiger_visitsign.signtime,
                vtiger_visitsign.signnum,
                vtiger_visitsign.coordinate,
                vtiger_visitsign.signaddress,
                vtiger_visitsign.visitsigntype,
                vtiger_visitingorder.visitingorderid
            FROM vtiger_visitingorder 
            LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_visitingorder.visitingorderid
            LEFT JOIN vtiger_visitsign ON (vtiger_visitsign.visitingorderid=vtiger_visitingorder.visitingorderid AND vtiger_visitsign.signaddress NOT LIKE '%水电路682号%' AND  vtiger_visitsign.signaddress NOT LIKE '%天虹商务大厦%' AND vtiger_visitsign.signaddress NOT LIKE '%水电路680号%')
            WHERE vtiger_crmentity.deleted=0 AND 
            ? BETWEEN LEFT(vtiger_visitingorder.startdate,10) AND LEFT(enddate,10) AND 
            vtiger_visitingorder.modulestatus='c_complete' AND 
            vtiger_visitsign.userid=? AND 
            vtiger_visitsign.issign=1 GROUP BY vtiger_visitingorder.visitingorderid";*/
    $query="SELECT
                vtiger_visitingorder.startdate,
                vtiger_visitingorder.enddate,
                vtiger_visitsign_mulit.userid AS extractid,
                vtiger_visitsign_mulit.signtime,
                vtiger_visitsign_mulit.signnum,
                vtiger_visitsign_mulit.coordinate,
                vtiger_visitsign_mulit.signaddress,
                vtiger_visitsign_mulit.visitsigntype,
                vtiger_visitsign_mulit.userid,
                vtiger_visitingorder.modulestatus,
                vtiger_visitingorder.auditorid,
                vtiger_visitingorder.outobjective,
                vtiger_visitingorder.visitingorderid
            FROM vtiger_visitingorder 
            LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_visitingorder.visitingorderid
            LEFT JOIN vtiger_visitsign_mulit ON vtiger_visitsign_mulit.visitingorderid=vtiger_visitingorder.visitingorderid
            WHERE vtiger_crmentity.deleted=0 AND 
            ? BETWEEN LEFT(vtiger_visitingorder.startdate,10) AND LEFT(enddate,10) AND 
            vtiger_visitingorder.modulestatus in('c_complete','a_normal') AND 
			vtiger_visitsign_mulit.userid=? AND 
            vtiger_visitsign_mulit.issign=1";
    $sales = $adb->pquery($query, array($currentdate,$userid));
    _cs_logs(array("执行用户列表查询sql：".$query));
    $rows = $adb->num_rows($sales);
    $ret_lists = array();
    //
    if ($rows>0) {
        /*while($row=$adb->fetchByAssoc($sales)){
                $lists = array();
                $lists['startdate']=$row['startdate'];
                $lists['enddate']=$row['enddate'];
                $lists['extractid']=$row['extractid'];
                $ret_lists[]=$lists;
        }*/
        $temp=array();
        while($row=$adb->fetchByAssoc($sales)) {
            $lists = array();
            if (in_array($row['signnum'], array(1, 2)) || $row['outobjective'] == '出差') {
                if (!in_array($row['visitingorderid'], $temp)) {
                    $temp[] = $row['visitingorderid'];
                    $lists['startdate'] = $row['startdate'];
                    $lists['enddate'] = $row['enddate'];
                    $lists['extractid'] = $row['extractid'];
                    $lists['visitingorderid'] = $row['visitingorderid'];
                    $lists['modulestatus'] = $row['modulestatus'];
                    $lists['outobjective'] = ($row['outobjective']=='出差'?'business_travel':'');
                    $lists['auditorid'] = $row['auditorid'];
                    $lists['sign'][] = array(
                        'signtime' => $row['signtime'],
                        'userid' => $row['userid'],
                        'coordinate' => $row['coordinate'],
                        'signaddress' => $row['signaddress'],
                        'signnum' => $row['signnum'],
                        'visitsigntype' => $row['visitsigntype']
                    );
                    $ret_lists[$lists['visitingorderid']] = $lists;
                } else {
                    $ret_lists[$row['visitingorderid']]['sign'][] = array(
                        'userid' => $row['userid'],
                        'signtime' => $row['signtime'],
                        'userid' => $row['userid'],
                        'coordinate' => $row['coordinate'],
                        'signaddress' => $row['signaddress'],
                        'signnum' => $row['signnum'],
                        'visitsigntype' => $row['visitsigntype']
                    );
                }
            }
        }
        //$data=json_encode($ret_lists,JSON_UNESCAPED_UNICODE );
        $ret_lists=array_values($ret_lists);
        $data=json_encode($ret_lists,JSON_UNESCAPED_UNICODE);
        $data=encrypt($data);
        echo json_encode(array("success"=>true,"data"=>$data));
        //echo json_encode(array("success"=>true,"data"=>$ret_lists));
        _cs_logs(array("获取员工拜访信息接口成功"));
    }else{
        echo json_encode(array("success"=>false,"data"=>$ret_lists));
    }
    _cs_logs(array("获取员工拜访信息接口结束"));
    exit;
}

//=================获取员工拜访信息接所有不管签到没签到口API================================================================
if(is_array($cs_method) && $cs_method[0]=='getUserVisitingOrderAllInfo') {
    _cs_logs(array("获取员工拜访信息接口开始"));
    global $adb;
    $lists = array();
    $currentdate = $_GET['currentdate'];
    $userid = $_GET['userid'];
    //跟进目的
    /*$query="SELECT
                vtiger_visitingorder.startdate,
                vtiger_visitingorder.enddate,
                vtiger_visitsign.userid AS extractid,
                vtiger_visitsign.signtime,
                vtiger_visitsign.signnum,
                vtiger_visitsign.coordinate,
                vtiger_visitsign.signaddress,
                vtiger_visitsign.visitsigntype,
                vtiger_visitingorder.visitingorderid
            FROM vtiger_visitingorder
            LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_visitingorder.visitingorderid
            LEFT JOIN vtiger_visitsign ON vtiger_visitsign.visitingorderid=vtiger_visitingorder.visitingorderid
            WHERE vtiger_crmentity.deleted=0 AND
            ? BETWEEN LEFT(vtiger_visitingorder.startdate,10) AND LEFT(enddate,10) AND
            vtiger_visitingorder.modulestatus='c_complete' AND
            vtiger_visitsign.userid=? AND
            vtiger_visitsign.issign=1 GROUP BY vtiger_visitingorder.visitingorderid";*/
    $query="SELECT
                vtiger_visitingorder.startdate,
                vtiger_visitingorder.enddate,
                vtiger_visitsign_mulit.userid AS extractid,
                vtiger_visitsign_mulit.signtime,
                vtiger_visitsign_mulit.signnum,
                vtiger_visitsign_mulit.coordinate,
                vtiger_visitsign_mulit.signaddress,
                vtiger_visitsign_mulit.visitsigntype,
                vtiger_visitsign_mulit.userid,
                vtiger_visitingorder.modulestatus,
                vtiger_visitingorder.auditorid,
                vtiger_visitingorder.outobjective,
                vtiger_visitingorder.visitingorderid
            FROM vtiger_visitingorder 
            LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_visitingorder.visitingorderid
            LEFT JOIN vtiger_visitsign_mulit ON vtiger_visitsign_mulit.visitingorderid=vtiger_visitingorder.visitingorderid
            WHERE vtiger_crmentity.deleted=0 AND 
            ? BETWEEN LEFT(vtiger_visitingorder.startdate,10) AND LEFT(enddate,10) AND 
            vtiger_visitingorder.modulestatus in('c_complete','a_normal') AND 
			vtiger_visitsign_mulit.userid=? ";
    $sales = $adb->pquery($query, array($currentdate,$userid));
    _cs_logs(array("执行用户列表查询sql：".$query));
    $rows = $adb->num_rows($sales);
    $ret_lists = array();
    //
    if ($rows>0) {
        /*while($row=$adb->fetchByAssoc($sales)){
                $lists = array();
                $lists['startdate']=$row['startdate'];
                $lists['enddate']=$row['enddate'];
                $lists['extractid']=$row['extractid'];
                $ret_lists[]=$lists;
        }*/
        $temp=array();
        while($row=$adb->fetchByAssoc($sales)) {
            $lists = array();
            if (in_array($row['signnum'], array(1, 2)) || $row['outobjective'] == '出差') {
                if (!in_array($row['visitingorderid'], $temp)) {
                    $temp[] = $row['visitingorderid'];
                    $lists['startdate'] = $row['startdate'];
                    $lists['enddate'] = $row['enddate'];
                    $lists['extractid'] = $row['extractid'];
                    $lists['visitingorderid'] = $row['visitingorderid'];
                    $lists['modulestatus'] = $row['modulestatus'];
                    $lists['outobjective'] = ($row['outobjective']=='出差'?'business_travel':'');
                    $lists['auditorid'] = $row['auditorid'];
                    $lists['sign'][] = array(
                        'signtime' => $row['signtime'],
                        'userid' => $row['userid'],
                        'coordinate' => $row['coordinate'],
                        'signaddress' => $row['signaddress'],
                        'signnum' => $row['signnum'],
                        'visitsigntype' => $row['visitsigntype']
                    );
                    $ret_lists[$lists['visitingorderid']] = $lists;
                } else {
                    $ret_lists[$row['visitingorderid']]['sign'][] = array(
                        'userid' => $row['userid'],
                        'signtime' => $row['signtime'],
                        'userid' => $row['userid'],
                        'coordinate' => $row['coordinate'],
                        'signaddress' => $row['signaddress'],
                        'signnum' => $row['signnum'],
                        'visitsigntype' => $row['visitsigntype']
                    );
                }
            }
        }
        //$data=json_encode($ret_lists,JSON_UNESCAPED_UNICODE );
        $ret_lists=array_values($ret_lists);
        $data=json_encode($ret_lists,JSON_UNESCAPED_UNICODE);
        $data=encrypt($data);
        echo json_encode(array("success"=>true,"data"=>$data));
        //echo json_encode(array("success"=>true,"data"=>$ret_lists));
        _cs_logs(array("获取员工拜访信息接口成功"));
    }else{
        echo json_encode(array("success"=>false,"data"=>$ret_lists));
    }
    _cs_logs(array("获取员工拜访信息接口结束"));
    exit;
}

function encrypt_password($user_name,$user_password, $crypt_type='') {
    // encrypt the password.
    $salt = mb_substr($user_name, 0, 2,'utf-8');

    // Fix for: http://trac.vtiger.com/cgi-bin/trac.cgi/ticket/4923
    if($crypt_type == '') {
        // Try to get the crypt_type which is in database for the user
        $crypt_type = $this->get_user_crypt_type();
    }

    // For more details on salt format look at: http://in.php.net/crypt
    if($crypt_type == 'MD5') {
        $salt = '$1$' . $salt . '$';
    } elseif($crypt_type == 'BLOWFISH') {
        $salt = '$2$' . $salt . '$';
    } elseif($crypt_type == 'PHP5.3MD5') {
        //only change salt for php 5.3 or higher version for backward
        //compactibility.
        //crypt API is lot stricter in taking the value for salt.
        $salt = '$1$' . str_pad($salt, 9, '0');
    }

    $encrypted_password = crypt($user_password, $salt);
    return $encrypted_password;
}
function encrypt($encrypt, $key="sdfesdcf\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0") {
    $mcrypt = MCRYPT_TRIPLEDES;
    $iv = mcrypt_create_iv(mcrypt_get_iv_size($mcrypt, MCRYPT_MODE_ECB), MCRYPT_RAND);
    $passcrypt = mcrypt_encrypt($mcrypt, $key, $encrypt, MCRYPT_MODE_ECB, $iv);
    $encode = base64_encode($passcrypt);
    return $encode;
}
function decrypt($decrypt, $key="sdfesdcf\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0"){
    $decoded = str_replace(' ','%20',$decrypt);
    $decoded = base64_decode($decoded);
    $mcrypt = MCRYPT_TRIPLEDES;
    $iv = mcrypt_create_iv(mcrypt_get_iv_size($mcrypt, MCRYPT_MODE_ECB), MCRYPT_RAND);
    $decrypted = mcrypt_decrypt($mcrypt, $key, $decoded, MCRYPT_MODE_ECB, $iv);
    return $decrypted;
}
/**
 * 写日志，用于测试,可以开启关闭
 * @param data mixed
 */
function _cs_logs($data, $file = 'logs_'){
    $year	= date("Y");
    $month	= date("m");
    $dir	= './logs/leave/' . $year . '/' . $month . '/';
    if(!is_dir($dir)) {
        mkdir($dir,0755,true);
    }
    $file = $dir . $file . date('Y-m-d').'.txt';
    @file_put_contents($file, '----------------' . date('H:i:s') . '--------------------'.PHP_EOL.var_export($data,true).PHP_EOL, FILE_APPEND);
}
?>