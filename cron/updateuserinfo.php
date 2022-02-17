<?php
/**
 * 更新账户信息
 */

    ini_set('display_errors', 'off');
    error_reporting(0);
    $dir = __DIR__;
    $dir = rtrim($dir, '/cron');
    ini_set("include_path", $dir);

    require_once('config.php');
    require_once('include/utils/utils.php');
    require_once('include/logging.php');

    header("Content-type:text/html;charset=utf-8");
//error_reporting(0);
//ini_set('display_errors','on'); error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

    include_once 'vtlib/Vtiger/Module.php';
    include_once 'includes/main/WebUI.php';
    ini_set('memory_limit', '1024M');
    set_time_limit(0);
    /**
     *  下面是调取员工的级别的处理
     */
    function http_request($url, $data = null, $curlset = array())
    {
        $curl = curl_init();
        if (!empty($curlset)) {
            foreach ($curlset as $key => $value) {
                curl_setopt($curl, $key, $value);
            }
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_TIMEOUT, 120);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);//post提交方式
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    function cbc_decrypt($data, $key, $iv)
    {
        $data = base64_decode($data);
        $data = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, $iv);
        $padding = ord($data[strlen($data) - 1]);
        return substr($data, 0, -$padding);
    }

    echo '--------start---------', "\n";
    updateUserInfo();
    echo '--------end---------', "\n";

    function updateUserInfo()
    {
        global $adb;
        //$url='https://prein-gw.71360.com/employee-common-service/employeeAllInfo/getAllEmployee?sign=453c02843968f87c41cd963a3d4a8bf8';
        //$url = '192.168.7.231:18686/employeeAllInfo/getAllEmployee?sign=1';
        $url = 'https://xxhoa.71360.com/cache/employeeAllInfo/getAllEmployee?sign=453c02843968f87c41cd963a3d4a8bf8';
        //echo $url;
        $DataJson = http_request($url);
        $data = json_decode($DataJson, true);
        if ($data['success'] == 1) {
            $jsonData = cbc_decrypt($data['data'], 'f4k9f5w7f8g4er26', '5e8y6w45juem1234');
            $arrayData = json_decode($jsonData, true);
            $temparray = array();
            $i = 1;
            $countNum = count($arrayData);
            $paramsNnum = 51;//数据长度-1,字段个数*2-1
            $SqlSplit = "(" . str_pad("", $paramsNnum, '?,') . "),";
            $adb->pquery('truncate table vtiger_userstemp', array());
            $SqlSplitJoin = '';
            $sql = 'INSERT INTO `vtiger_userstemp` (`userid`,`user_name`,`last_name`,`reports_to_id`,`title`,`department`,`phone_mobile`,`phone_work`,`email1`,`status`,`usercode`,
`user_entered`,`leavedate`,`isdimission`,`departmentid`,`roleid`,`invoicecompany`,`companyid`,idcard,`employeenumber`,wechatid,personnelposition,personnelpositionid,employeelevel,stafftype,graduatetime) VALUES';
            foreach ($arrayData as $value) {
                $temparray[] = $value['crmEmployeeId'];//crmID
                $temparray[] = $value['loginName'];//登陆名
                $temparray[] = $value['name'];//姓名
                $temparray[] = $value['crmImmediateSuperiorId'];//上级ID
                $temparray[] = $value['position'];//职务
                $temparray[] = $value['departmentName'];//职务
                $temparray[] = $value['phoneNumber'];//个人手机
                $temparray[] = $value['workPhone'];//工作手机
                $temparray[] = $value['companyEmail'];//邮箱
                $temparray[] = $value['state'] == 0 ? 'Active' : 'Inactive';//账号状态，0启用，1 禁用
                $temparray[] = $value['jobNumber'];//工号
                $temparray[] = date('Y-m-d', (int)($value['entryTime'] / 1000));//入职时间
                $temparray[] = $value['departureTime'] > 0 ? date('Y-m-d', (int)($value['departureTime'] / 1000)) : '';//离职时间
                $temparray[] = $value['postState'];//是否在职（0在职，1离职）
                $temparray[] = $value['departmentId'];//部门ID
                $temparray[] = $value['erpRoleId'];//角色ID
                $temparray[] = $value['companyName'];//角色ID
                $temparray[] = $value['companyId'];//角色ID
                $temparray[] = $value['idNumber'];//身份证
                $temparray[] = $value['employeeNumber'];//职工编号
                $temparray[] = $value['wechatId'];//微信ID
                $temparray[] = $value['classificationId'];//分类识别名称
                $temparray[] = $value['classificationTitle'];//分类识别id
                $temparray[] = $value['level'];//员工等级
                $temparray[] = $value['type'] == 2 ? 'Contract' : 'Internsh';//员工类型 $tstafftype=array(2=>'Contract',1=>'Internsh');
                $temparray[] = $value['graduateTime'] > 0 ? date('Y-m-d', (int)($value['graduateTime'] / 1000)) : '';//毕业时间
                $SqlSplitJoin .= $SqlSplit;
                if ($i % 1000 == 0 || $i == $countNum) {
                    $SqlSplitJoin = trim($SqlSplitJoin, ',');
                    $sqls = $sql . $SqlSplitJoin;
                    $adb->pquery($sqls, $temparray);
                    $SqlSplitJoin = '';
                    $temparray = array();
                }
                $i++;
            }

            $sql = 'UPDATE vtiger_users,vtiger_userstemp,vtiger_usermanger SET 
                vtiger_users.email1=vtiger_userstemp.email1,
                vtiger_users.`user_name`=vtiger_userstemp.`user_name`,
                vtiger_users.`last_name`=vtiger_userstemp.`last_name`,
                vtiger_users.`reports_to_id`=vtiger_userstemp.`reports_to_id`,
                vtiger_users.`title`=vtiger_userstemp.`title`,
                vtiger_users.`department`=vtiger_userstemp.`department`,
                vtiger_users.`phone_mobile`=vtiger_userstemp.`phone_mobile`,
                vtiger_users.`phone_work`=vtiger_userstemp.`phone_work`,
                vtiger_users.`email1`=vtiger_userstemp.`email1`,
                vtiger_users.`status`=vtiger_userstemp.`status`,
                vtiger_users.`usercode`=vtiger_userstemp.`usercode`,
                vtiger_users.`user_entered`=vtiger_userstemp.`user_entered`,
                vtiger_users.`isdimission`=vtiger_userstemp.`isdimission`,
                vtiger_users.`invoicecompany`=vtiger_userstemp.`invoicecompany`,
                vtiger_users.`default_record_view`=\'Summary\',
                vtiger_users.`companyid`=vtiger_userstemp.`companyid`,
                vtiger_users.`leavedate`=vtiger_userstemp.`leavedate`,
                vtiger_users.idcard=vtiger_userstemp.idcard,
                vtiger_users.wechatid=vtiger_userstemp.wechatid,
                vtiger_users.personnelposition=vtiger_userstemp.personnelposition,
                vtiger_users.personnelpositionid=vtiger_userstemp.personnelpositionid,
                vtiger_users.employeelevel=vtiger_userstemp.employeelevel,
                vtiger_users.graduatetime=vtiger_userstemp.graduatetime,
                vtiger_users.stafftype=vtiger_userstemp.stafftype,
                vtiger_users.`employeenumber`=vtiger_userstemp.employeenumber
                WHERE vtiger_users.id=vtiger_userstemp.userid AND vtiger_usermanger.userid=vtiger_userstemp.userid AND vtiger_usermanger.ownornot=0';
            $adb->pquery($sql, array());
            $sql = 'UPDATE vtiger_usermanger,vtiger_userstemp SET 
                vtiger_usermanger.email1=vtiger_userstemp.email1,
                vtiger_usermanger.`user_name`=vtiger_userstemp.`user_name`,
                vtiger_usermanger.`last_name`=vtiger_userstemp.`last_name`,
                vtiger_usermanger.`reports_to_id`=vtiger_userstemp.`reports_to_id`,
                vtiger_usermanger.`title`=vtiger_userstemp.`title`,
                vtiger_usermanger.`department`=vtiger_userstemp.`department`,
                vtiger_usermanger.`phone_mobile`=vtiger_userstemp.`phone_mobile`,
                vtiger_usermanger.`phone_work`=vtiger_userstemp.`phone_work`,
                vtiger_usermanger.`email1`=vtiger_userstemp.`email1`,
                vtiger_usermanger.`status`=vtiger_userstemp.`status`,
                vtiger_usermanger.`usercode`=vtiger_userstemp.`usercode`,
				vtiger_usermanger.`departmentid`=vtiger_userstemp.`departmentid`,
				vtiger_usermanger.`roleid`=vtiger_userstemp.`roleid`,
                vtiger_usermanger.`user_entered`=vtiger_userstemp.`user_entered`,
                vtiger_usermanger.`isdimission`=vtiger_userstemp.`isdimission`,
                vtiger_usermanger.`invoicecompany`=vtiger_userstemp.`invoicecompany`,
                vtiger_usermanger.personnelposition=vtiger_userstemp.personnelposition,
                vtiger_usermanger.personnelpositionid=vtiger_userstemp.personnelpositionid,
                vtiger_usermanger.`leavedate`=vtiger_userstemp.`leavedate`,
                vtiger_usermanger.employeelevel=vtiger_userstemp.employeelevel,
                vtiger_usermanger.graduatetime=vtiger_userstemp.graduatetime,
                vtiger_usermanger.stafftype=vtiger_userstemp.stafftype,
                vtiger_usermanger.`companyid`=vtiger_userstemp.`companyid`
                WHERE vtiger_usermanger.userid=vtiger_userstemp.userid  AND vtiger_usermanger.ownornot=0';
            $adb->pquery($sql, array());
            $sql = 'UPDATE vtiger_user2department,vtiger_userstemp SET vtiger_user2department.departmentid=vtiger_userstemp.departmentid WHERE vtiger_user2department.userid=vtiger_userstemp.userid';
            $adb->pquery($sql, array());
            $sql = 'UPDATE vtiger_user2role,vtiger_userstemp SET vtiger_user2role.roleid=vtiger_userstemp.roleid WHERE vtiger_user2role.userid=vtiger_userstemp.userid';
            $adb->pquery($sql, array());
            //$query='select max(id) as maxid from vtiger_users';
            //$result=$adb->pquery($query,array());
            //$id=$result->fields['maxid'];
            //$query='SELECT * FROM vtiger_userstemp WHERE userid>?';
            //$result=$adb->pquery($query,array($id));
            $query = 'SELECT * FROM vtiger_userstemp WHERE userid NOT IN(SELECT id FROM vtiger_users)';
            $result = $adb->pquery($query, array());
            if ($adb->num_rows($result)) {
                $datetime = date('Y-m-d H:i:s');
                $idArray = array();
                while ($row = $adb->fetch_array($result)) {
                    $maxuserid = $row['userid'];
                    $idArray[] = $row['userid'];
                    $recordid = $adb->getUniqueID('vtiger_crmentity');
                    $sql = "INSERT INTO vtiger_usermanger (
                    usermangerid,userid,`user_name`,`last_name`,`reports_to_id`,`title`,`department`,`phone_mobile`,`phone_work`,`email1`,`status`,`usercode`,`user_entered`,`isdimission`,`departmentid`,`roleid`,`invoicecompany`,`companyid`,employeelevel,personnelposition,personnelpositionid,graduatetime,`modulestatus`,ownornot) SELECT 
                    " . $recordid . ",vtiger_userstemp.userid,vtiger_userstemp.`user_name`,vtiger_userstemp.`last_name`,vtiger_userstemp.`reports_to_id`,vtiger_userstemp.`title`,
                    vtiger_userstemp.`department`,vtiger_userstemp.`phone_mobile`,vtiger_userstemp.`phone_work`,vtiger_userstemp.`email1`,vtiger_userstemp.`status`,vtiger_userstemp.`usercode`,vtiger_userstemp.`user_entered`,vtiger_userstemp.`isdimission`,vtiger_userstemp.`departmentid`,vtiger_userstemp.`roleid`,vtiger_userstemp.`invoicecompany`,vtiger_userstemp.`companyid`,employeelevel,personnelposition,personnelpositionid,graduatetime,'c_complete',0 FROM vtiger_userstemp WHERE vtiger_userstemp.userid=?";
                    $adb->pquery($sql, array($row['userid']));
                    $sql = "INSERT INTO `vtiger_crmentity` (`crmid`, `smcreatorid`, `smownerid`, `modifiedby`, `setype`, `description`, `createdtime`, `modifiedtime`, `viewedtime`, `status`, `version`, `presence`, `deleted`, `label`) VALUES 
                        (?, ?, ?, ?, 'UserManger', NULL, ?, ?, NULL, NULL, '0', '1', '0', ?)";
                    $adb->pquery($sql, array($recordid, 6934, 6934, 6934, $datetime, $datetime, $row['last_name']));
                }
                $sql = "INSERT INTO vtiger_users (
                    id,`user_name`,`last_name`,`reports_to_id`,`title`,`department`,`phone_mobile`,`phone_work`,`email1`,`status`,`usercode`,`user_entered`,`isdimission`,`invoicecompany`,`companyid`,idcard,employeenumber,employeelevel,personnelposition,personnelpositionid,graduatetime,wechatid,default_record_view
                    ) SELECT 
                    vtiger_userstemp.userid,vtiger_userstemp.`user_name`,vtiger_userstemp.`last_name`,vtiger_userstemp.`reports_to_id`,vtiger_userstemp.`title`,
                    vtiger_userstemp.`department`,vtiger_userstemp.`phone_mobile`,vtiger_userstemp.`phone_work`,vtiger_userstemp.`email1`,vtiger_userstemp.`status`,vtiger_userstemp.`usercode`,vtiger_userstemp.`user_entered`,vtiger_userstemp.`isdimission`,vtiger_userstemp.`invoicecompany`,vtiger_userstemp.`companyid`,vtiger_userstemp.idcard, vtiger_userstemp.employeenumber,employeelevel,personnelposition,personnelpositionid,graduatetime,vtiger_userstemp.wechatid,'Summary'
                     FROM vtiger_userstemp WHERE vtiger_userstemp.userid IN(" . implode(',', $idArray) . ")";
                $adb->pquery($sql, array());
                $sql = 'replace INTO vtiger_user2department(userid,departmentid) SELECT userid,departmentid FROM vtiger_userstemp WHERE userid IN(' . implode(',', $idArray) . ')';
                $adb->pquery($sql, array());
                $sql = 'replace INTO vtiger_user2role(userid,roleid) SELECT userid,roleid FROM vtiger_userstemp WHERE userid IN(' . implode(',', $idArray) . ')';
                $adb->pquery($sql, array());
                $sql = 'UPDATE vtiger_users_seq SET id=?';
                $adb->pquery($sql, array($maxuserid));
            }
        }
    }







