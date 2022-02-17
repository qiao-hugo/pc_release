<?php
/*
 * 客服系统使用接口
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
    global $adb,$sault,$curlTyunWeakPasswordCheck;

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
    //_cs_logs(array("客服登录接口解析后参数:".$decrdata));
    $password = encrypt_password($user_name, $user_password, 'PHP5.3MD5');
    $sql = "SELECT vtiger_users.id AS userId,vtiger_users.usercode,vtiger_users.reports_to_id AS reportstoid,vtiger_users.user_name AS username,vtiger_users.last_name AS fullname,IF(vtiger_users.is_admin='on','1','0') AS isadmin,IFNULL(vtiger_users.email1,vtiger_users.email2) AS email,
            vtiger_user2role.roleid,vtiger_role.rolename,
			(SELECT M.usercode FROM vtiger_users M WHERE M.id=vtiger_users.reports_to_id) AS reportsusercode,
            vtiger_user2department.departmentid,vtiger_departments.departmentname
            FROM vtiger_users 
            LEFT JOIN vtiger_user2department ON(vtiger_users.id=vtiger_user2department.userid)
            LEFT JOIN vtiger_user2role ON(vtiger_users.id=vtiger_user2role.userid)
            LEFT JOIN vtiger_role ON(vtiger_role.roleid=vtiger_user2role.roleid)
            LEFT JOIN vtiger_departments ON(vtiger_user2department.departmentid=vtiger_departments.departmentid)
            WHERE vtiger_users.`status`='Active' AND vtiger_users.user_name=? AND vtiger_users.user_password=?";
    //_cs_logs(array("客服登录接口1:".$user_name.'|'.$password));
	
    $sales = $adb->pquery($sql, array($user_name, $password));
    $rows = $adb->num_rows($sales);
    if ($rows) {
        _cs_logs(array("验证成功,登录人:".$result['fullname']));
        $lists = array();
        $result = $adb->query_result_rowdata($sales, 0);
        $data=json_encode($result,JSON_UNESCAPED_UNICODE);
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
//=================查找下级员工==============================================================================

if(is_array($cs_method) && $cs_method[0]=='findSubUserId'){
    _cs_logs(array("查找下级员工接口开始"));
    global $adb;
    $userId=$_REQUEST['userId'];
    //_cs_logs(array("查找下级员工接口参数:".$userId));

    $query = "SELECT f_find_subuser_ids($userId)";
    //_cs_logs(array("查找下级员工接口执行的函数:".$query));
    $result = $adb->pquery($query, array());
    $arr=$adb->query_result_rowdata($result,0);
    //_cs_logs(array("查找下级员工接口返回:".json_encode($arr)));
    $data=json_encode($arr,JSON_UNESCAPED_UNICODE);
    $data=encrypt($data);
    echo json_encode(array("success"=>true,"data"=>$data));
    _cs_logs(array("查找下级员工接口结束"));
    exit;
}
//=================根据T云账号获取客户信息API================================================================
if(is_array($cs_method) && $cs_method[0]=='getAccountbyTyunAccunt'){
    _cs_logs(array("根据T云账号获取客户信息接口开始"));
    global $adb;

    $tyunaccunt=$_REQUEST['tyunaccunt'];

    //_cs_logs(array("根据T云账号获取客户信息接口参数:".$tyunaccunt));
    //$query = "SELECT DISTINCT customerid as accountid,customername as accountname,usercode as tyunaccunt FROM vtiger_activationcode WHERE usercode='{$tyunaccunt}' LIMIT 1";
    $query = "SELECT DISTINCT customerid as accountid,customername as accountname,usercode as tyunaccunt,comeformtyun AS accountSource FROM vtiger_activationcode WHERE classtype='buy' AND `status` IN(0,1) and usercode='{$tyunaccunt}'  LIMIT 1
			UNION ALL
			SELECT DISTINCT accountid as accountid,companyname as accountname,loginname as tyunaccunt,0 as accountSource FROM vtiger_tyunstationsale WHERE classtype='buy' AND `status` IN(0,1) and loginname='{$tyunaccunt}' LIMIT 1";
    //_cs_logs(array("根据T云账号获取客户信息接口SQL：".$query));
    $result = $adb->pquery($query, array());
    $rows = $adb->num_rows($result);
    $lists = array();

    if ($rows>0) {
        $arr=$adb->query_result_rowdata($result,0);
        //客户ID
        $lists['accountid']=$arr['accountid'];
        //客户名称
        $lists['accountname']=$arr['accountname'];
        //T云账号
        $lists['tyunaccunt']=$arr['tyunaccunt'];
        //T云账号
        $lists['accountSource']=$arr['accountSource'];

        $data=json_encode($lists,JSON_UNESCAPED_UNICODE);
        $data=encrypt($data);
        echo json_encode(array("success"=>true,"data"=>$data));
    }else{
        _cs_logs(array("没有查询到{$tyunaccunt}客户信息"));
        echo json_encode(array("success"=>false,"data"=>$lists));
    }
    _cs_logs(array("根据T云账号获取客户信息接口结束"));
    exit;
}
//=================获取客户信息API================================================================
if(is_array($cs_method) && $cs_method[0]=='getaccount'){
    _cs_logs(array("获取客户信息接口开始"));
    global $adb;
    $accountid=$_REQUEST['accountid'];
    $isIroncard=$_REQUEST['isIroncard'];//是否铁牌客户(1:是)
    $sourceType=$_REQUEST['sourceType'];

	if(empty($sourceType)){
        $sourceType = 0;
    }
	
    _cs_logs(array("获取客户信息接口参数：".$accountid.'|'.$isIroncard));
    include_once 'vtlib/Vtiger/Module.php';
    include_once 'includes/main/WebUI.php';
    include_once  'languages/zh_cn/Accounts.php';
    vglobal('default_language', $default_language);
    $currentLanguage = 'zh_cn';
    //Vtiger_Language_Handler::getLanguage();//2.语言设置
    vglobal('current_language',$currentLanguage);

    $query="SELECT 
            vtiger_account.accountid,
            vtiger_account.accountname,
            IF(vtiger_account.protected=1,'是','否') as protected,
            vtiger_account.servicetype,
            vtiger_account.producttype AS servicetype_name,
            IF(vtiger_account.sign=1,'是','否') as sign,
            vtiger_account.advancesmoney,
            IF(vtiger_account.groupbuyaccount=1,'是','否') as groupbuyaccount,
            IFNULL((select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`isdimission`=1,'[离职]',''))) as last_name from vtiger_users where vtiger_crmentity.smownerid=vtiger_users.id),'--') as smowner_name,
            vtiger_crmentity.smownerid,
            vtiger_account.accountrank,
            vtiger_account.linkname,
            vtiger_account.mobile,
            vtiger_account.phone,
            vtiger_account.website,
            vtiger_account.fax,
            vtiger_account.email1 AS email,
            vtiger_account.industry,
            vtiger_account.annual_revenue,
            REPLACE(vtiger_account.address,'#','') AS address,
            vtiger_account.makedecision,
            vtiger_account.gender,
            vtiger_account.country,
            vtiger_account.business,
            vtiger_account.regionalpartition,
            vtiger_account.makedecision,
            vtiger_account.customertype,
            vtiger_account.title,
            vtiger_account.gender AS gendertype,
            vtiger_account.leadsource,
            vtiger_account.businessarea,
            IFNULL((select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`isdimission`=1,'[离职]',''))) as last_name from vtiger_users where vtiger_account.serviceid=vtiger_users.id),'--') as service_name,
			IFNULL((select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`isdimission`=1,'[离职]',''))) as last_name from vtiger_users where vtiger_account_allot_other.csid=vtiger_users.id),'--') as service_name_other,
            vtiger_account.serviceid as serviceid,
			vtiger_account_allot_other.csid serviceid_other,
            vtiger_account.parentid as referee_id,
            IFNULL((SELECT T.accountname from vtiger_account T WHERE T.accountid=vtiger_account.parentid),'--') as referee_name,
            vtiger_account.customerproperty,
            vtiger_account.account_no,
            vtiger_account.lastfollowuptime,
            vtiger_account.saleorderlastdealtime,
            vtiger_account.protectday,
            vtiger_account.accountcategory,
            vtiger_account.visitingtimes,
            IF(vtiger_account.frommarketing=1,'是','否') as frommarketing,
            IF(vtiger_account.emailoptout=1,'是','否') as emailoptout,
            vtiger_account.accountid,
            vtiger_crmentity.createdtime,
            vtiger_crmentity.modifiedtime,
            IF(vtiger_account.sign=1,'有','无') AS sign,
            vtiger_crmentity.description,
			(SELECT vtiger_servicecomments.remark FROM vtiger_servicecomments WHERE vtiger_servicecomments.related_to=vtiger_account.accountid AND vtiger_servicecomments.remark IS NOT NULL  AND LENGTH(vtiger_servicecomments.remark)>0 ORDER BY vtiger_servicecomments.allocatetime DESC LIMIT 1) as followup_remark,
			(SELECT vtiger_servicecomments.inremark FROM vtiger_servicecomments WHERE vtiger_servicecomments.related_to=vtiger_account.accountid AND vtiger_servicecomments.inremark IS NOT NULL  AND LENGTH(vtiger_servicecomments.inremark)>0 ORDER BY vtiger_servicecomments.allocatetime DESC LIMIT 1) as followup_in_remark
            FROM vtiger_account 
            LEFT JOIN vtiger_crmentity ON vtiger_account.accountid = vtiger_crmentity.crmid
			LEFT JOIN vtiger_account_allot_other ON (vtiger_account.accountid = vtiger_account_allot_other.accountid AND source_type=?)
            WHERE vtiger_crmentity.deleted=0 AND vtiger_account.accountid=?";

    //if(!empty($isIroncard) && $isIroncard == '1'){
    //    $query.=" AND vtiger_account.accountrank='iron_isv'";
    //}else{
    //    $query.=" AND vtiger_account.accountrank NOT IN('chan_notv','iron_isv')";
    //}

    _cs_logs(array("获取客户信息接口SQL：".$query));

    $result = $adb->pquery($query, array($sourceType,$accountid));
    $rows = $adb->num_rows($result);
    $lists = array();
    if ($rows>0) {
        $arr=$adb->query_result_rowdata($result,0);
        //客户名称
        $lists['accountname']=$arr['accountname'];
        //客户编号
        $lists['account_no']=$arr['account_no'];
        //推荐人
        $lists['referee_id']=$arr['referee_id'];
        $lists['referee_name']=$arr['referee_name'];
        //信息来源
        $lists['leadsource']=$arr['leadsource'];
        $lists['leadsource_name']=vtranslate($arr['leadsource'],"Accounts");
        //团购客户
        $lists['groupbuyaccount']=$arr['groupbuyaccount'];
        //垫款额
        //$lists['advancesmoney']=$arr['advancesmoney'];
        //保护模式
        $lists['accountcategory']=vtranslate($arr['accountcategory'],"Accounts");
        //客户类型
        $lists['customertype']=$arr['customertype'];
        $lists['customertype_name']=vtranslate($arr['customertype'],"Accounts");
        //客户等级
        $lists['accountrank']=$arr['accountrank'];
        $lists['accountrank_name']=vtranslate($arr['accountrank'],"Accounts");
        //负责商务
        $lists['smownerid']=$arr['smownerid'];
        $lists['smowner_name']=$arr['smowner_name'];
        //负责客服
		if($sourceType == 1){
			$lists['serviceid']=$arr['serviceid_other'];
			$lists['service_name']=$arr['service_name_other'];
		}else{
			$lists['serviceid']=$arr['serviceid'];
			$lists['service_name']=$arr['service_name'];
		}
        //业务类型
        $lists['servicetype']=$arr['servicetype'];
        $lists['servicetype_name']=$arr['servicetype_name'];
        //$lists['servicetype_name']=vtranslate($arr['servicetype'],"Accounts");
        //行业
        $lists['industry']=vtranslate($arr['industry'],"Accounts");
        //主营业务
        $lists['business']=$arr['business'];
        //公司属性
        $lists['customerproperty']=$arr['customerproperty'];
        //创建时间
        $lists['createdtime']=$arr['createdtime'];
        //修改时间
        $lists['modifiedtime']=$arr['modifiedtime'];

        //公司座机
        $lists['phone']=$arr['phone'];
        //公司传真
        $lists['fax']=$arr['fax'];
        //国家名称
        $lists['country']=$arr['country'];
        //注册资金
        $lists['annual_revenue']=$arr['annual_revenue'];
        //官网地址
        $lists['website']=$arr['website'];
        //公司地址
        $lists['address']=$arr['address'];
        //业务主要推广区域
        $lists['businessarea']=$arr['businessarea'];
        //区域分区
        $lists['regionalpartition']=$arr['regionalpartition'];

        //首要联系人
        $lists['linkname']=$arr['linkname'];
        //职位
        $lists['title']=$arr['title'];
        //性别
        $lists['gendertype']=$arr['gendertype'];
        $lists['gendertype_name']=vtranslate($arr['gendertype'],"Accounts");
        //决策权
        $lists['makedecisiontype']=$arr['makedecision'];
        $lists['makedecisiontype_name']=vtranslate($arr['makedecision'],"Accounts");
        //邮箱
        $lists['email']=$arr['email'];
        //手机
        $lists['mobile']=$arr['mobile'];
        //拒绝邮件打扰
        $lists['emailoptout']=$arr['emailoptout'];
        //标记
        $lists['sign']=$arr['sign'];
        //备注
        $lists['description']=$arr['description'];
        //跟进备注
        $lists['followup_remark']=$arr['followup_remark'];
        //跟进内部备注
        $lists['followup_in_remark']=$arr['followup_in_remark'];
        //T云账号取得
        $lists['tyun_accounts']= getTyunAccounts($arr['accountid']);
        //T云客户来源
        //$lists['accountSource']= getTyunAccountSource($arr['accountid']);

        /* if (!empty($_GET['array'])) {
             print_r($lists);
         } else {*/
        $data=json_encode($lists,JSON_UNESCAPED_UNICODE);
        $data=encrypt($data);
        echo json_encode(array("success"=>true,"data"=>$data));
        //}
        _cs_logs(array("获取客户信息成功"));
    }else{
        _cs_logs(array("没有客户的相关信息"));
        //$lists=array('success'=>false,'msg'=>'没有客户的相关信息',JSON_UNESCAPED_UNICODE);
        //echo json_encode($lists);
        echo json_encode(array("success"=>true,"data"=>$lists));
    }
    _cs_logs(array("获取客户信息接口结束"));
    exit;
}
//=================获取客户联系人列表API================================================================
if(is_array($cs_method) && $cs_method[0]=='listAccountContacts'){
    _cs_logs(array("获取客户联系人列表接口开始"));
    global $adb;
    $accountid=$_REQUEST['accountid'];

    include_once 'vtlib/Vtiger/Module.php';
    include_once 'includes/main/WebUI.php';
    include_once  'languages/zh_cn/Accounts.php';
    vglobal('default_language', $default_language);
    $currentLanguage = 'zh_cn';
    //Vtiger_Language_Handler::getLanguage();//2.语言设置
    vglobal('current_language',$currentLanguage);

    _cs_logs(array("获取客户联系人列表接口参数：".$accountid));

    $query="SELECT  vtiger_contactdetails.contactid,
					vtiger_contactdetails.salutation,
					vtiger_contactdetails.contact_no, 
					vtiger_contactdetails.phone, 
					vtiger_contactdetails.mobile, 
					vtiger_contactdetails.accountid, 
					vtiger_contactdetails.title, 
					vtiger_contactdetails.fax, 
					vtiger_contactdetails.department, 
					vtiger_contactdetails.email, 
					vtiger_contactdetails.secondaryemail, 
					vtiger_contactdetails.donotcall, 
					vtiger_contactdetails.emailoptout,
					vtiger_contactdetails.name,
					vtiger_contactdetails.gender, 
					vtiger_contactdetails.makedecision,
					vtiger_contactdetails.weixin, 
					vtiger_contactdetails.qq,
					vtiger_contactdetails.leave_office	
				FROM vtiger_contactdetails 
				INNER JOIN vtiger_crmentity ON vtiger_contactdetails.contactid = vtiger_crmentity.crmid 
				WHERE vtiger_crmentity.deleted=0 AND vtiger_contactdetails.accountid=?";
    $query .= " ORDER BY vtiger_contactdetails.contactid DESC ";


    $result=$adb->pquery($query,array($accountid));
    $rows = $adb->num_rows($result);
    $arr_contacts=array();
    while ($row = $adb->fetchByAssoc($result)) {
        $arrayt=array();
		$arrayt['accountid'] = $row['accountid'];
		$arrayt['salutation'] = $row['salutation'];
        //联系人ID
        $arrayt['contactid'] = $row['contactid'];
        //离职状态
        if($row['leave_office'] == '1'){
            $arrayt['leaveOffice'] = '1';
        }else{
            $arrayt['leaveOffice'] = '0';
        }
		$arrayt['fax'] = $row['fax'];
		$arrayt['department'] = $row['department'];
        $arrayt['secondaryemail'] = $row['secondaryemail'];
		$arrayt['donotcall'] = $row['donotcall'];
		$arrayt['emailoptout'] = $row['emailoptout'];
        //联系人
        $arrayt['name'] = $row['name'];
        //非首要联系人
        //$arrayt['is_main_linkname'] = 0;
        //性别
        $arrayt['gendertype'] = $row['gender'];
        $arrayt['gendertype_name'] = vtranslate($row['gender'],"Contacts");
        //手机
        $arrayt['mobile'] = $row['mobile'];
        //电话
        $arrayt['phone'] = $row['phone'];
        //微信
        $arrayt['weixin'] = $row['weixin'];
		//qq
        $arrayt['qq'] = $row['qq'];
        //职务
        $arrayt['title'] = $row['title'];
        //决策权
        $arrayt['makedecision'] = $row['makedecision'];
        $arrayt['makedecisiontype_name'] = vtranslate($row['makedecision'],"Contacts");
        //邮箱
        $arrayt['email'] = $row['email'];
        $arr_contacts[]=$arrayt;
    }


    _cs_logs(array("客户联系人列表数据:".json_encode($arr_contacts)));
    $data=json_encode($arr_contacts,JSON_UNESCAPED_UNICODE);
    $data=encrypt($data);
    echo json_encode(array("success"=>true,"is_multiple"=>true,"total"=>$total,"data"=>$data));
    _cs_logs(array("获取客户联系人列表成功"));

    // _cs_logs(array("获取客户联系人列表接口结束"));
    exit;
}
//=================获取合同信息API================================================================
if(is_array($cs_method) && $cs_method[0]=='getcontract') {
    _cs_logs(array("根据客户获取合同列表接口开始"));

    include_once 'vtlib/Vtiger/Module.php';
    include_once 'includes/main/WebUI.php';
    include_once  'languages/zh_cn/Accounts.php';
    vglobal('default_language', $default_language);
    $currentLanguage = 'zh_cn';
    //Vtiger_Language_Handler::getLanguage();//2.语言设置
    vglobal('current_language',$currentLanguage);
    global $adb;
    $contractids=$_GET['contractids'];
    $accountid=$_GET['accountid'];
    $accountids=$_GET['accountids'];
    $serviceids=$_GET['serviceids'];
    $userIds=$_GET['userIds'];
    $search_field=$_GET['search_field'];
    $search_value=$_GET['search_value'];
    $field_type=$_GET['field_type'];
    $search_type=$_GET['search_type'];
    $moduleName=$_GET['moduleName'];
    $loginId = $_GET['loginId'];
    $businessDepartmentId = $_GET['businessDepartmentId'];
    $businessCenterId = $_GET['businessCenterId'];
    $businessRegionId = $_GET['businessRegionId'];
	$isNewContract = $_GET['isNewContract'];
	$sourceType = $_GET['bsourceType'];
	
    //去掉空格
    if(!empty($search_value)){
        $search_value = trim($search_value);
    }

	if(empty($sourceType)){
        $sourceType = 0;
    }
	
    $pageNum = "0";
    $pageSize = "0";
    if(!empty($_GET['pageNum'])){
        $pageNum=empty($_GET['pageNum'])?0:$_GET['pageNum'];
        $pageSize=empty($_GET['pageSize'])?10:$_GET['pageSize'];
        $p_num = 0;
        if($pageNum>0){
            $p_num = $pageNum - 1;
        }
        $pageNum = $p_num * $pageSize;
    }

    $query="SELECT DISTINCT
            vtiger_servicecontracts.servicecontractsid,
           vtiger_account.accountid AS accountid,
           (vtiger_account.accountname) as sc_related_to,
           IFNULL((SELECT sum(IFNULL(unit_price,0)) FROM vtiger_receivedpayments WHERE vtiger_receivedpayments.deleted=0 AND vtiger_receivedpayments.receivedstatus='normal' AND vtiger_receivedpayments.relatetoid=vtiger_servicecontracts.servicecontractsid),0) AS unit_price,
           IFNULL((SELECT 
            sum(IFNULL(vtiger_newinvoiceextend.totalandtaxextend,0)) 
            FROM `vtiger_newinvoiceextend` 
            LEFT JOIN vtiger_newinvoice ON vtiger_newinvoiceextend.invoiceid=vtiger_newinvoice.invoiceid 
            LEFT JOIN vtiger_crmentity AS invoicecrm ON invoicecrm.crmid=vtiger_newinvoice.invoiceid
            WHERE 
            invoicecrm.deleted=0
             AND vtiger_newinvoiceextend.deleted=0
            AND vtiger_newinvoice.modulestatus='c_complete'
            AND vtiger_newinvoiceextend.invoicestatus='normal'
            AND vtiger_newinvoice.contractid=vtiger_servicecontracts.servicecontractsid),0) AS totalandtax,
            vtiger_servicecontracts.total,
            vtiger_servicecontracts.modulestatus,
			vtiger_activationcode.classtype,
			vtiger_activationcode.usercodeid,
			IFNULL(vtiger_activationcode.comeformtyun,0) AS account_source,
            vtiger_servicecontracts.contract_type,
            IFNULL(vtiger_servicecontracts.signdate,'--') AS signdate,
			DATE_FORMAT(vtiger_servicecontracts.signfor_date,'%Y-%m-%d') AS signfor_date,
            vtiger_servicecontracts.effectivetime,
            vtiger_servicecontracts.contract_no,
            IFNULL((select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`isdimission`=1,'[离职]',''))) as last_name from vtiger_users where vtiger_account.serviceid=vtiger_users.id),'--') as service_name,
            vtiger_account.serviceid as serviceid,
			IFNULL((select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`isdimission`=1,'[离职]',''))) as last_name from vtiger_users where vtiger_servicecontracts.signid=vtiger_users.id),'--') as smowner_name,
            account_crm.smownerid,
			vtiger_servicecontracts.signid,
            IFNULL(vtiger_activationcode.usercode,vtiger_tyunstationsale.loginname) AS tyun_account,
			(CASE vtiger_activationcode.comeformtyun
			WHEN 1 THEN 
				(SELECT GROUP_CONCAT(DISTINCT productname) FROM vtiger_activationcode WHERE `status` in(0,1) AND comeformtyun=1 AND contractid=vtiger_servicecontracts.servicecontractsid)
			ELSE
				(SELECT productname FROM vtiger_products WHERE tyunproductid=vtiger_activationcode.productid LIMIT 1)
			END) AS product_type
            FROM vtiger_servicecontracts 
			INNER JOIN vtiger_crmentity ON vtiger_servicecontracts.servicecontractsid = vtiger_crmentity.crmid 
            LEFT JOIN vtiger_activationcode ON vtiger_activationcode.contractid=CONCAT(vtiger_servicecontracts.servicecontractsid) 
            LEFT JOIN vtiger_tyunstationsale ON (vtiger_tyunstationsale.contractid=vtiger_servicecontracts.servicecontractsid) 
            LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_servicecontracts.sc_related_to 
			LEFT JOIN vtiger_crmentity account_crm ON vtiger_account.accountid = account_crm.crmid
			LEFT JOIN vtiger_user2department D ON(account_crm.smownerid=D.userid)
            WHERE vtiger_crmentity.deleted=0 ";

    //直销客户，合同类型属于T云的，合同是已签收的";
	if($sourceType == 1 || $sourceType == '1'){
		$query_where = " AND vtiger_activationcode.productclass=1";
	}else{
		$query_where = " AND (vtiger_activationcode.productclass != 1 OR vtiger_activationcode.productclass IS NULL)";
	}
	
    //按商务查询
    if(!empty($businessDepartmentId) && $businessDepartmentId !='0'){
        $query_where.= " AND D.departmentid='{$businessDepartmentId}'";
    }else {
        if(!empty($businessCenterId) && $businessCenterId !='0'){
            $query_where.= " AND D.departmentid IN(SELECT departmentid FROM vtiger_departments WHERE FIND_IN_SET('{$businessCenterId}',REPLACE(parentdepartment,'::',',')))";
        }else{
            if(!empty($businessRegionId) && $businessRegionId !='0'){
                $query_where.= " AND account_crm.smownerid IN(SELECT id FROM vtiger_users WHERE companyid='{$businessRegionId}')";
            }
        }
    }

    //个人业绩录入读取已领取和已签收合同
    if(!empty($moduleName)){
        if($moduleName == 'achievementAdd'){
            $query_where .= " AND vtiger_servicecontracts.modulestatus IN ('c_complete', '已发放') ";
        }else{
            $query_where .= "  AND vtiger_servicecontracts.signfor_date IS NOT NULL AND vtiger_servicecontracts.modulestatus IN ('c_complete', 'c_cancel','c_history','c_tovoid') ";
        }
    }else{
        $query_where .= "  AND vtiger_servicecontracts.signfor_date IS NOT NULL AND vtiger_servicecontracts.modulestatus IN ('c_complete', 'c_cancel','c_history','c_tovoid') ";
    }
    $query_where .= "  AND (vtiger_activationcode.`status`IN(0,1) OR vtiger_tyunstationsale.`status` IN(0,1))";

    //针对黄玉琴、李季下单的订单，做特殊处理
    $query_where .= splicingSpecialSql($loginId);

	//新签收合同过滤掉已绑定客服合同
    if(!empty($isNewContract) && $isNewContract == '1'){
        $query_where .= " AND (vtiger_account.serviceid IS NULL OR vtiger_account.accountid NOT IN(SELECT accountid FROM `vtiger_account_allot_other`))";
    }else{
		if(!empty($serviceids)){
			$serviceids = rtrim($serviceids,',');
			$query_where .= " AND vtiger_account.serviceid IN({$serviceids})";
		}
		if(!empty($userIds)){
			$userIds = rtrim($userIds,',');
			$query_where .= " AND vtiger_account.serviceid IN({$userIds})";
		}
	}
	
    if(!empty($accountids)){
        $accountids = rtrim($accountids,',');
        $query_where .= " AND vtiger_servicecontracts.sc_related_to IN({$accountids})";
    }
    if(!empty($accountid)){
        $query_where .= " AND vtiger_servicecontracts.sc_related_to={$accountid}";
    }

    if(!empty($contractids)){
        $contractids = rtrim($contractids,',');
        $query_where .= " AND vtiger_servicecontracts.servicecontractsid IN({$contractids})";
    }
    
    if(!empty($search_value)) {
        //客户名称
        if($search_field == 'accountname'){
            $query_where .= " AND vtiger_account.accountname LIKE '%{$search_value}%'";
        }else if($search_field == 'product_type'){
            $query_where .= " AND vtiger_activationcode.productid IN(SELECT tyunproductid FROM vtiger_products WHERE productname LIKE '%{$search_value}%')";
        }else if($search_field == 'buy_type'){
            $query_where .= " AND vtiger_activationcode.classtype='{$search_value}'";
        }else{
            if($field_type == 'date' && !empty($search_value)){
                //模糊查询
                $search_value = str_replace('/', '-', $search_value);
                $query_where .= " AND vtiger_servicecontracts.{$search_field} LIKE '%{$search_value}%'";
            }
            if($field_type == 'list'){
                //精确查询
                $query_where .= " AND vtiger_servicecontracts.{$search_field}='{$search_value}'";
            }
            if($field_type == 'text'){
                if ($search_type == '1') {
                    //模糊查询
                    if(!empty($search_value)){
                        $query_where .= " AND vtiger_servicecontracts.{$search_field} LIKE '%{$search_value}%'";
                    }
                } else {
                    //精确查询
                    $query_where .= " AND vtiger_servicecontracts.{$search_field}='{$search_value}'";
                }
            }

        }
    }

    $query .= $query_where;
	
	$query .= " GROUP BY vtiger_activationcode.contractid";
    //"ORDER BY vtiger_servicecontracts.signdate DESC,vtiger_servicecontracts.servicecontractsid DESC";

    //if(!empty($moduleName)){
    $query .= " ORDER BY DATE_FORMAT(vtiger_servicecontracts.signdate,'%Y-%m-%d') DESC";
    //}

    if(bccomp($pageSize,0)> 0){
        $query .= " LIMIT {$pageNum},{$pageSize}";
    }

    $sales = $adb->pquery($query, array());
    _cs_logs(array("执行合同查询列表sql：".$query));
    _cs_logs(array("根据客户获取合同列表接口参数：".json_encode(array($accountid,$contractids,$loginId,$pageNum,$pageSize))));
    $rows = $adb->num_rows($sales);
    $ret_lists = array();
    if ($rows>0) {
        //_cs_logs(array("执行合同查询件数：".$rows));
        while($row=$adb->fetchByAssoc($sales)){
            $lists = array();
            //合同ID
            $lists['contractid']=$row['servicecontractsid'];
            //客户ID
            $lists['accountid']=$row['accountid'];
            //客户名称
            $lists['account_name']=$row['sc_related_to'];
            //回款总额
            $lists['payment_total']=$row['unit_price'];
            //开票总额
            $lists['invoice_total']=$row['totalandtax'];
            //合同金额
            if(bccomp($row['total'],0)> 0){
                $lists['contract_total']=$row['total'];
            }else{
                $lists['contract_total']='--';
            }

            //产品类型
            $lists['product_type']=$row['product_type'];
            //合同状态
            $lists['modulestatus']=$row['modulestatus'];
            $lists['modulestatus_name']=vtranslate($row['modulestatus'],"ServiceContracts");
            //新增/续费/升级/另购
            $lists['servicecontractstype']=$row['classtype'];
            $servicecontractstype_name="--";
            if($row['classtype'] == 'buy'){
                $servicecontractstype_name = '首购';
            }
            if($row['classtype'] == 'upgrade'){
                $servicecontractstype_name = '升级';
            }
            if($row['classtype'] == 'cupgrade'){
                $servicecontractstype_name = '迁移升级';
            }
            if($row['classtype'] == 'renew'){
                $servicecontractstype_name = '续费';
            }
            if($row['classtype'] == 'crenew'){
                $servicecontractstype_name = '迁移续费';
            }
            if($row['classtype'] == 'againbuy'){
                $servicecontractstype_name = '另购';
            }
            if($row['classtype'] == 'degrade'){
                $servicecontractstype_name = '降级';
            }
            if($row['classtype'] == 'cdegrade'){
                $servicecontractstype_name = '迁移降级';
            }
            $lists['servicecontractstype_name']=$servicecontractstype_name;
            //合同类型
            $lists['contract_type']=$row['contract_type'];
            //签订日期
            $lists['signdate']=$row['signdate'];
            //签收日期
            $lists['signfor_date']=$row['signfor_date'];
            //到期日期
            $lists['expire_date']=$row['effectivetime'];
            //合同编号
            $lists['contract_no']=$row['contract_no'];
            //发票比例
            if(bccomp($row['total'],0)> 0){
                $lists['invoice_scale']=bcmul(bcdiv($row['totalandtax'],$row['total'],4),100,2).'%';
                //回款比例
                $payment_scale = bcmul(bcdiv($row['unit_price'],$row['total'],4),100,2);
                $lists['payment_scale']=$payment_scale.'%';
                //是否回款不足(1:是，0：否)
                $lists['is_payment_insufficient']=(bccomp($payment_scale,50)<0)?1:0;
            }else{
                $lists['invoice_scale']='--';
                $lists['payment_scale']='--';
                //是否回款不足(1:是，0：否)
                $lists['is_payment_insufficient']=0;
            }

            //T云账号
            $lists['tyun_account']=$row['tyun_account'];
            //负责客服
            $lists['serviceid']=$row['serviceid'];
            $lists['service_name']=$row['service_name'];
            //负责商务
            $lists['smownerid']=$row['smownerid'];
			//签单商务
			$lists['signid']=$row['signid'];
            $lists['smowner_name']=$row['smowner_name'];
            $lists['accountSource']=$row['account_source'];
            $lists['usercodeid']=$row['usercodeid'];


            $ret_lists[]=$lists;
        }

        //总件数取得
        $query_total="SELECT COUNT(DISTINCT vtiger_activationcode.contractid) AS cnt
        FROM vtiger_servicecontracts 
		INNER JOIN vtiger_crmentity ON vtiger_servicecontracts.servicecontractsid = vtiger_crmentity.crmid 
		LEFT JOIN vtiger_activationcode ON vtiger_activationcode.contractid=CONCAT(vtiger_servicecontracts.servicecontractsid) 
		LEFT JOIN vtiger_tyunstationsale ON (vtiger_tyunstationsale.contractid=vtiger_servicecontracts.servicecontractsid)
		LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_servicecontracts.sc_related_to 
		LEFT JOIN vtiger_crmentity account_crm ON vtiger_account.accountid = account_crm.crmid
		LEFT JOIN vtiger_user2department D ON(account_crm.smownerid=D.userid)
		WHERE vtiger_crmentity.deleted=0 ";
        $query_total.=$query_where;
		
        _cs_logs(array("执行合同查询总件数SQL：".$query_total));

        $result_total = $adb->pquery($query_total, array());
        $row_total = $adb->query_result_rowdata($result_total);
        $total = $row_total["cnt"];
        $total = empty($total)?0:$total;
        $data=json_encode($ret_lists,JSON_UNESCAPED_UNICODE );
        $data=encrypt($data);
        echo json_encode(array("success"=>true,"is_multiple"=>true,"total"=>$total,"pageNum"=>$pageNum,"pageSize"=>$pageSize,"data"=>$data));

        _cs_logs(array("根据客户获取合同列表成功"));
    }else{
        /*_cs_logs(array("没有客户的合同信息"));
        $lists=array('success'=>false,'msg'=>'没有客户的合同信息');
        echo json_encode($lists,JSON_UNESCAPED_UNICODE);*/
        echo json_encode(array("success"=>true,"is_multiple"=>true,"total"=>0,"pageNum"=>$pageNum,"pageSize"=>$pageSize,"data"=>$ret_lists));
    }
    _cs_logs(array("根据客户获取合同列表接口结束"));
    exit;
}
//=================获取跟进信息API================================================================
if(is_array($cs_method) && $cs_method[0]=='getModComment') {
    _cs_logs(array("根据客户获取商务/客服最后一次跟进信息接口开始"));
    global $adb;
    $accountid = $_GET['accountid'];
    $serviceFlag = $_GET['serviceflag'];
	$sourceType = $_GET['sourceType'];
	if(empty($sourceType)){
        $sourceType = 0;
    }
    //_cs_logs(array("根据客户获取商务/客服最后一次跟进信息接口参数：".$accountid."|".$serviceFlag));
    $query="SELECT vtiger_modcomments.commentcontent,
                IFNULL((select CONCAT(last_name,(if(`isdimission`=1,'[离职]',''))) as last_name from vtiger_users where vtiger_modcomments.creatorid=vtiger_users.id),'--') as creater,
                IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_modcomments.creatorid LIMIT 1)),'--') as departmentname,
                vtiger_modcomments.addtime,
                vtiger_modcomments.modcommenttype, 
                vtiger_modcomments.modcommentmode,
                vtiger_modcomments.modcommentpurpose,
				vtiger_modcomments.accountintentionality,
                IFNULL((select name from vtiger_contactdetails where contactid=vtiger_modcomments.contact_id),IFNULL((select linkname from vtiger_account where accountid=vtiger_modcomments.related_to ),'-')) AS linkman
                FROM vtiger_modcomments
                WHERE vtiger_modcomments.related_to = ?";

    if($serviceFlag == '1'){
        $query .= " AND source_type={$sourceType} AND vtiger_modcomments.followrole=1";
    }else{
        $query .= " AND vtiger_modcomments.followrole=0";
    }
    $query .= " ORDER BY vtiger_modcomments.modcommentsid DESC LIMIT 1";

    //_cs_logs(array("根据客户获取商务/客服最后一次跟进信息SQL：".$query));
    $sales = $adb->pquery($query, array($accountid));

    $rows = $adb->num_rows($sales);
    $lists = array();
    if ($rows > 0) {
        $arr = $adb->query_result_rowdata($sales);
        //跟进内容
        $lists['commentcontent'] = $arr['commentcontent'];
        //创建人
        $lists['creater'] = $arr['creater'];
        //是否客服
        //if($serviceFlag == '1'){
        //	$lists['is_cs'] = "是";
        //}else{
        //	$lists['is_cs'] = "不是";
        //}
        //部门
        $lists['departmentname'] = $arr['departmentname'];
        //添加时间
        $lists['addtime'] = $arr['addtime'];
        //跟进类型
        $lists['modcommenttype'] = $arr['modcommenttype'];
        //跟进目的
        $lists['modcommentpurpose'] = $arr['modcommentpurpose'];
        //跟进方式
        $lists['modcommentmode'] = $arr['modcommentmode'];
        //联系人
        $lists['linkman'] = $arr['linkman'];
		//意向度
        $lists['accountintentionality'] = $arr['accountintentionality'];
		
        $data = json_encode($lists, JSON_UNESCAPED_UNICODE);
        $data = encrypt($data);
        echo json_encode(array("success" => true, "data" => $data));
        _cs_logs(array("根据客户获取商务/客服最后一次跟进信息成功"));
    } else {
        _cs_logs(array("没有客户的相关的商务/客服最后一次跟进信息"));
        /* $lists = array('success' => false, 'msg' => '没有客户的相关跟进信息', JSON_UNESCAPED_UNICODE);
         echo json_encode($lists);*/
        echo json_encode(array("success" => true, "data" => $lists));
    }
    _cs_logs(array("根据客户获取商务/客服最后一次跟进信息接口结束"));
    exit;
}
if(is_array($cs_method) && $cs_method[0]=='searchModCommentList') {
    _cs_logs(array("根据客户获取跟进信息接口开始"));
    global $adb;
	include_once 'vtlib/Vtiger/Module.php';
    include_once 'includes/main/WebUI.php';
    include_once 'languages/zh_cn/ModComments.php';
    vglobal('default_language', $default_language);
    $currentLanguage = 'zh_cn';
    vglobal('current_language',$currentLanguage);
    $accountid = $_GET['accountid'];
    $commentcontent = $_GET['commentcontent'];
    $isservice = $_GET['isservice'];
	$sourceType = $_GET['bsourceType'];
	if(empty($sourceType)){
        $sourceType = 0;
    }
	
	/*
    $pageNum = "0";
    $pageSize = "0";
    if(!empty($_GET['pageNum'])){
        $pageNum=empty($_GET['pageNum'])?0:$_GET['pageNum'];
        $pageSize=empty($_GET['pageSize'])?10:$_GET['pageSize'];
        $p_num = 0;
        if($pageNum>0){
            $p_num = $pageNum - 1;
        }
        $pageNum = $p_num * $pageSize;
    }
    **/
	
    //_cs_logs(array("根据客户获取跟进信息接口参数：".$accountid));
    $query="SELECT vtiger_modcomments.commentcontent,
			    IFNULL((select CONCAT(last_name,(if(`isdimission`=1,'[离职]',''))) as last_name from vtiger_users where vtiger_modcomments.creatorid=vtiger_users.id),'--') as creater,
				IFNULL((select 1 from vtiger_users where vtiger_modcomments.creatorid=vtiger_users.id and `status`!='Active'),0) as isquit,
                IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_modcomments.creatorid LIMIT 1)),'--') as departmentname,
			    vtiger_modcomments.addtime,
                vtiger_modcomments.modcommenttype, 
                vtiger_modcomments.modcommentmode,
                vtiger_modcomments.modcommentpurpose,
				vtiger_modcomments.accountintentionality,
                IFNULL((select name from vtiger_contactdetails where contactid=vtiger_modcomments.contact_id),IFNULL((select linkname from vtiger_account where accountid=vtiger_modcomments.related_to ),'-')) AS linkman
                FROM vtiger_modcomments
				LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_modcomments.related_to
                WHERE vtiger_modcomments.related_to = ? AND vtiger_modcomments.modcommenttype NOT LIKE '%首次%'";

    //总件数
    /*if(bccomp($pageSize,0)> 0){
        $query_total="SELECT COUNT(1) AS cnt
		FROM vtiger_modcomments
		LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_modcomments.related_to
		WHERE vtiger_modcomments.related_to = ? ";
    }**/

    if($sourceType == 1){
		//$query_where  = " AND ((vtiger_modcomments.source_type=1 AND vtiger_modcomments.followrole = 1) OR(vtiger_modcomments.followrole = 0))";
		$query_where  = " AND (vtiger_modcomments.source_type=1 AND vtiger_modcomments.followrole = 0)";
	}else{
		//$query_where  = " AND ((vtiger_modcomments.source_type=0 AND vtiger_modcomments.followrole = 1) OR(vtiger_modcomments.followrole = 0))";
		$query_where  = " AND (vtiger_modcomments.source_type!=1 AND vtiger_modcomments.followrole = 0)";
	}
    

    //跟进内容查询
    if(!empty($commentcontent)){
        $query_where .= " AND vtiger_modcomments.commentcontent LIKE '%{$commentcontent}%'";
    }
	
	//只取商务跟进
	$query_where .= " AND vtiger_modcomments.followrole = 0";
    //是否客服
    /*if(!empty($isservice)){
        if($isservice == '1'){
            $query_where .= " AND vtiger_modcomments.followrole = 1";
        }else{
            $query_where .= " AND vtiger_modcomments.followrole = 0";
        }
    }**/

    //总件数条件
    $query_total .= $query_where;
	/*
    if(bccomp($pageSize,0)> 0){
        $query_where .= " ORDER BY vtiger_modcomments.modcommentsid DESC LIMIT {$pageNum},{$pageSize}";
    }else{
        //获取最后一次商务跟进
        $query_where .= " AND vtiger_modcomments.followrole = 0";
        $query_where .= "  AND vtiger_modcomments.creatorid NOT IN(SELECT vtiger_account.serviceid FROM vtiger_account WHERE vtiger_account.serviceid IS NOT NULL) 
			ORDER BY vtiger_modcomments.modcommentsid DESC LIMIT 1";
    }
	**/
    $query .= $query_where;

    //_cs_logs(array("获取跟进记录信息接口SQL：".$query));
    $sales = $adb->pquery($query, array($accountid));

    $rows = $adb->num_rows($sales);
    $ret_lists = array();
    if ($rows>0) {
        //_cs_logs(array("执行跟进查询件数：".$rows));
        while($row=$adb->fetchByAssoc($sales)){
            $lists = array();
            //跟进内容
            $lists['commentcontent'] = $row['commentcontent'];
            //创建人
            $lists['creater'] = $row['creater'];
            //是否客服
            //$lists['isCs'] = $row['iscs'];
            //部门
            $lists['departmentname'] = $row['departmentname'];
            //是否离职
            $lists['isquit'] = $row['isquit'];
            //添加时间
            $lists['addtime'] = $row['addtime'];
            //跟进类型
            $lists['modcommenttype'] = $row['modcommenttype'];
            //跟进目的
            $lists['modcommentpurpose'] = $row['modcommentpurpose'];
            //跟进方式
            $lists['modcommentmode'] = $row['modcommentmode'];
			//意向度
            $lists['accountintentionality'] = vtranslate($row['accountintentionality'],"ModComments");
            //联系人
            $lists['linkman'] = $row['linkman'];

            $ret_lists[]=$lists;
        }

        //总件数取得
        $total = 0;
        /*if(bccomp($pageSize,0)> 0){
            //_cs_logs(array("执行跟进查询总件数SQL：".$query_total));

            $result_total = $adb->pquery($query_total, array($accountid));
            $row_total = $adb->query_result_rowdata($result_total);
            $total = $row_total["cnt"];
            //_cs_logs(array("执行跟进查询总件数：".$total));
        }**/

        $total = empty($total)?0:$total;
        $data=json_encode($ret_lists,JSON_UNESCAPED_UNICODE );
        $data=encrypt($data);
        //echo json_encode(array("success"=>true,"is_multiple"=>true,"total"=>$total,"pageNum"=>$pageNum,"pageSize"=>$pageSize,"data"=>$data));
		echo json_encode(array("success"=>true,"is_multiple"=>true,"total"=>$total,"pageNum"=>0,"pageSize"=>0,"data"=>$data));

        _cs_logs(array("根据客户获取跟进列表成功"));
    } else {
        _cs_logs(array("没有客户跟进列表数据"));
        echo json_encode(array("success" => true, "data" => $lists));
    }
    _cs_logs(array("根据客户获取跟进信息接口结束"));
    exit;
}
//=================获取跟进下拉项API==============================================================
if(is_array($cs_method) && $cs_method[0]=='getModCommentSelect') {
    _cs_logs(array("获取跟进下拉项接口开始"));
    global $adb;
    $lists = array();
    $accountid = $_GET['accountid'];
    //跟进目的
    $query1="SELECT modcommentpurposeid,modcommentpurposetype FROM `vtiger_modcommentpurpose` ORDER BY sortorderid";
    $arr_result=$adb->run_query_allrecords($query1);
    $list_data = array();
    if(!empty($arr_result)) {
        foreach ($arr_result as $value) {
            $list_data = array();
            //ID
            $list_data['modcommentpurposeid']=$value['modcommentpurposeid'];
            //跟进目的
            $list_data['modcommentpurposetype']=$value['modcommentpurposetype'];
            $lists['modcommentpurpose'][]=$list_data;
        }
    }
    //跟进类型
    $query2="SELECT modcommenttypeid,modcommenttype FROM `vtiger_modcommenttype` ORDER BY sortorderid";
    $arr_result=$adb->run_query_allrecords($query2);
    $list_data = array();
    if(!empty($arr_result)) {
        foreach ($arr_result as $value) {
            $list_data = array();
            //ID
            $list_data['modcommenttypeid']=$value['modcommenttypeid'];
            //跟进类型
            $list_data['modcommenttype']=$value['modcommenttype'];
            $lists['modcommenttype'][]=$list_data;
        }
    }
    //跟进方式
    $query3="SELECT modcommentmodeid,modcommentmode FROM `vtiger_modcommentmode` ORDER BY sortorderid";
    $arr_result=$adb->run_query_allrecords($query3);
    $list_data = array();
    if(!empty($arr_result)) {
        foreach ($arr_result as $value) {
            $list_data = array();
            //ID
            $list_data['modcommentmodeid']=$value['modcommentmodeid'];
            //跟进方式
            $list_data['modcommentmode']=$value['modcommentmode'];
            $lists['modcommentmode'][]=$list_data;
        }
    }
    //联系人
    $query4="SELECT vtiger_contactdetails.contactid,vtiger_contactdetails.name AS linkname,0 AS isfirst
    FROM vtiger_contactdetails WHERE EXISTS (SELECT crmid FROM	vtiger_crmentity WHERE vtiger_crmentity.crmid = vtiger_contactdetails.contactid AND vtiger_crmentity.deleted = 0)
    AND vtiger_contactdetails.accountid = {$accountid}
    UNION ALL
    SELECT accountid AS contactid,linkname,1 AS isfirst FROM vtiger_account WHERE accountid={$accountid} AND linkname!=''";
    $arr_result=$adb->run_query_allrecords($query4);
    $list_data = array();
    if(!empty($arr_result)) {
        foreach ($arr_result as $value) {
            $list_data = array();
            //ID
            $list_data['contactid']=$value['contactid'];
            //联系人
            $list_data['linkname']=$value['linkname'];
            //是否首要联系人
            $list_data['isfirst']=$value['isfirst'];
            $lists['linkname'][]=$list_data;
        }
    }
    if(count($list_data) == 0){
        $lists['linkname']= array();
    }

    $data = json_encode($lists, JSON_UNESCAPED_UNICODE);
    $data = encrypt($data);
    echo json_encode(array("success" => true, "data" => $data));
    _cs_logs(array("获取跟进下拉项成功"));

    _cs_logs(array("获取跟进下拉项接口结束"));
    exit;
}
//=================获取合同附件列表API============================================================
if(is_array($cs_method) && $cs_method[0]=='getContractsAttachment') {
    include_once 'vtlib/Vtiger/Module.php';
    include_once 'includes/main/WebUI.php';
    include_once  'languages/zh_cn/Accounts.php';
    vglobal('default_language', $default_language);
    $currentLanguage = 'zh_cn';
    //Vtiger_Language_Handler::getLanguage();//2.语言设置
    vglobal('current_language',$currentLanguage);
    _cs_logs(array("获取合同附件接口开始"));
    global $adb;
    $contractsid = $_GET['accountid'];
    $pageNum=empty($_GET['pageNum'])?0:$_GET['pageNum'];
    $pageSize=empty($_GET['pageSize'])?10:$_GET['pageSize'];
    $p_num = 0;
    if($pageNum>0){
        $p_num = $pageNum - 1;
    }
    $pageNum = $p_num * $pageSize;
    _cs_logs(array("获取合同附件接口参数：".$contractsid));
    $query = "SELECT * from vtiger_files where relationid=? AND delflag=0 AND description='ServiceContracts' AND style IN ('files_style3','files_style4','files_style5') LIMIT {$pageNum},{$pageSize}";
    $sales = $adb->pquery($query, array($contractsid));
    $rows = $adb->num_rows($sales);
    $ret_lists = array();
    if ($rows > 0) {
        while($row=$adb->fetchByAssoc($sales)){
            $lists = array();
            //附件id
            $lists['attachmentsid'] = $row['attachmentsid'];
            //名称
            $lists['name'] = $row['name'];
            //上传时间
            $lists['uploadtime'] = $row['uploadtime'];
            //附件类型
            $lists['style'] = vtranslate($row['style'],"Files");
            //附件状态
            $lists['filestate'] = vtranslate($row['filestate'],"Files");
            $ret_lists[]=$lists;
        }

        //总件数取得
        $query_total="SELECT COUNT(1) AS cnt FROM vtiger_files where relationid=? AND delflag=0 AND description='ServiceContracts'";
        $result_total = $adb->pquery($query_total, array($accountid));
        $row_total = $adb->query_result_rowdata($result_total);
        $total = $row_total["cnt"];
        $total = empty($total)?0:$total;

        $data=json_encode($ret_lists,JSON_UNESCAPED_UNICODE );
        $data=encrypt($data);
        echo json_encode(array("success"=>true,"is_multiple"=>true,"total"=>$total,"pageNum"=>$pageNum,"pageSize"=>$pageSize,"data"=>$data));
        _cs_logs(array("获取合同附件信息成功"));
    } else {
        _cs_logs(array("没有该合同附件信息"));
        /*$lists = array('success' => false, 'msg' => '没有该合同附件信息', JSON_UNESCAPED_UNICODE);
        echo json_encode($lists);*/
        echo json_encode(array("success"=>true,"is_multiple"=>true,"total"=>0,"pageNum"=>$pageNum,"pageSize"=>$pageSize,"data"=>$ret_lists));
        _cs_logs(array("获取合同附件信息成功"));
    }
    _cs_logs(array("获取合同附件接口结束"));
    exit;
}
//=================获取客户和合同下拉搜索API======================================================
if(is_array($cs_method) && $cs_method[0]=='getSearchSelectList') {
    include_once 'vtlib/Vtiger/Module.php';
    include_once 'includes/main/WebUI.php';
    include_once  'languages/zh_cn/Accounts.php';
    vglobal('default_language', $default_language);
    $currentLanguage = 'zh_cn';
    vglobal('current_language',$currentLanguage);
    _cs_logs(array("获取客户和合同下拉搜索接口开始"));
    global $adb;
    $query = "SELECT DISTINCT accountrank AS type_name,'Accounts' AS module_name,'accountrank' AS search_field,'crm' AS field_source FROM vtiger_accountrank WHERE accountrank IS NOT NULL
              UNION ALL
              SELECT DISTINCT industry AS type_name,'Accounts' AS module_name,'industry' AS search_field,'crm' AS field_source  FROM vtiger_industry WHERE industry IS NOT NULL
              UNION ALL
              SELECT DISTINCT accountcategory AS type_name,'Accounts' AS module_name,'accountcategory' AS search_field,'crm' AS field_source  FROM vtiger_accountcategory WHERE accountcategory IS NOT NULL
              UNION ALL
              SELECT DISTINCT customertype AS type_name,'Accounts' AS module_name,'customertype' AS search_field,'crm' AS field_source  FROM vtiger_customertype WHERE customertype IS NOT NULL
			  UNION ALL
              SELECT DISTINCT contract_type AS type_name,'ServiceContracts' AS module_name,'contract_type' AS search_field,'crm' AS field_source  FROM vtiger_servicecontracts WHERE LENGTH(contract_type)>0
              UNION ALL
			  SELECT DISTINCT modulestatus AS type_name,'ServiceContracts' AS module_name,'contract_status' AS search_field,'crm' AS field_source  FROM vtiger_modulestatus  WHERE modulestatus='c_complete'
			  UNION ALL
			  SELECT DISTINCT classtype AS type_name,'ContractActivaCode' AS module_name,'buy_type' AS search_field,'crm' AS field_source  FROM vtiger_classtype 
			  UNION ALL
			  SELECT DISTINCT productname AS type_name,'ServiceContracts' AS module_name,'product_type' AS search_field,'crm' AS field_source FROM vtiger_products JOIN vtiger_crmentity ON(vtiger_products.productid=vtiger_crmentity.crmid) WHERE vtiger_products.istyun=1 AND vtiger_crmentity.deleted=0
              UNION ALL
              SELECT DISTINCT customerproperty AS type_name,'Accounts' AS module_name,'customerproperty' AS search_field,'crm' AS field_source  FROM vtiger_customerproperty WHERE customerproperty IS NOT NULL";
    $sales = $adb->pquery($query, array());
    $rows = $adb->num_rows($sales);
    $ret_lists = array();
    if ($rows > 0) {
        while($row=$adb->fetchByAssoc($sales)){
            $lists = array();
            //模块名称
            $lists['module_name'] = $row['module_name'];
            //字段名称
            $lists['search_field'] = $row['search_field'];
            //类型
            $lists['type_name'] = $row['type_name'];
            //类型名称
            $lists['type_desc'] = vtranslate($row['type_name'],$row['module_name']);
            //字段来源
            $lists['field_source'] = $row['field_source'];
            //更新时间
            $lists['updatetime'] = date("Y-m-d H:i:s");
            $ret_lists[]=$lists;
        }
        $data=json_encode($ret_lists,JSON_UNESCAPED_UNICODE );
        $data=encrypt($data);
        echo json_encode(array("success"=>true,"data"=>$data));
        _cs_logs(array("获取客户和合同下拉搜索信息成功"));
    } else {
        /*_cs_logs(array("没有获取客户和合同下拉搜索信息"));
        $lists = array('success' => false, 'msg' => '没有获取客户和合同下拉搜索信息', JSON_UNESCAPED_UNICODE);
        echo json_encode($lists);*/
        echo json_encode(array("success"=>true,"data"=>$ret_lists));
    }
    _cs_logs(array("获取客户和合同下拉搜索接口结束"));
    exit;
}
//=================获取CRM员工信息API============================================================
if(is_array($cs_method) && $cs_method[0]=='getCrmUserInfo') {
    _cs_logs(array("获取用户联系方式接口开始"));
    global $adb;
    $userid = $_GET['userid'];
    $accountid = $_GET['accountid'];
	
    _cs_logs(array("获取用户联系方式接口参数：".$userid));
    $query = "SELECT 
            A.id,
			A.last_name,
			A.usercode,
			A.phone_work,
			A.phone_mobile,
			A.companyid,
			A.invoicecompany,
			IFNULL(A.email1,A.email2) AS email,
			C.departmentname
            FROM vtiger_users A
			LEFT JOIN vtiger_user2department B ON(A.id=B.userid)
			LEFT JOIN vtiger_departments C ON(B.departmentid=C.departmentid)
			WHERE A.`status` ='Active'";

    if(!empty($userid)){
        $query .=" AND A.id={$userid}";
    }

    $sales = $adb->pquery($query, array());
    $rows = $adb->num_rows($sales);
    $lists = array();
    if ($rows > 0) {
        $row = $adb->query_result_rowdata($sales);
        //联系人
        $lists['name'] = $row['last_name'];
        //手机
        $lists['mobile'] = $row['phone_mobile'];
        //固话
        $lists['phone_work'] = $row['phone_work'];
        //邮箱
        $lists['email'] = $row['email'];
        //工号
        $lists['work_number'] = $row['usercode'];
        //部门
        $lists['department'] = $row['departmentname'];
        //公司id
        $lists['companyid'] = $row['companyid'];
        //合同主体名称
        $lists['invoicecompany'] = $row['invoicecompany'];
        $data = json_encode($lists, JSON_UNESCAPED_UNICODE);
        $data = encrypt($data);
        echo json_encode(array("success" => true, "data" => $data));
        _cs_logs(array("获取用户联系方式信息成功"));
    } else {
        /* _cs_logs(array("没有用户联系方式信息"));
         $lists = array('success' => false, 'msg' => '没有用户联系方式信息', JSON_UNESCAPED_UNICODE);
         echo json_encode($lists);*/
        echo json_encode(array("success" => true, "data" => null));
    }
    _cs_logs(array("获取用户联系方式接口结束"));
    exit;
}
//=================获取CRM员工信息API============================================================
if(is_array($cs_method) && $cs_method[0]=='getCrmUserBasicInfo') {
    _cs_logs(array("获取员工基本信息接口开始"));
    global $adb;
    $lastName = $_GET['lastName'];
    _cs_logs(array("获取员工基本信息接口参数：".$lastName));
    $query = "SELECT 
			A.id,
			A.user_name,
			A.last_name,
			A.usercode,
			C.departmentname
			FROM vtiger_users A
			LEFT JOIN vtiger_user2department B ON(A.id=B.userid)
			LEFT JOIN vtiger_departments C ON(B.departmentid=C.departmentid)
			WHERE A.`status` ='Active'";

    $query .=" AND A.last_name LIKE '%{$lastName}%'";


    $sales = $adb->pquery($query, array());
    $rows = $adb->num_rows($sales);
    $ret_lists = array();
    if ($rows > 0) {
        //_cs_logs(array("获取员工基本信息件数：".$rows));
        while($row=$adb->fetchByAssoc($sales)){
            $lists = array();
            //ID
            $lists['id'] = $row['id'];
            //登录名
            $lists['userName'] = $row['user_name'];
            //中文名
            $lists['lastName'] = $row['last_name'];
            //工号
            $lists['workNumber'] = $row['usercode'];
            //部门
            $lists['departmentName'] = $row['departmentname'];
            $ret_lists[]=$lists;
        }
        //_cs_logs(array("获取员工基本信息返回:".json_encode($ret_lists)));

        $data = json_encode($ret_lists, JSON_UNESCAPED_UNICODE);
        $data = encrypt($data);
        echo json_encode(array("success" => true, "data" => $data));
        _cs_logs(array("获取员工基本信息成功"));
    } else {
        $data = json_encode($ret_lists, JSON_UNESCAPED_UNICODE);
        $data = encrypt($data);
        echo json_encode(array("success" => true, "data" => $data));
    }
    _cs_logs(array("获取员工基本信息接口结束"));
    exit;
}
//=================查询合同列表API================================================================
if(is_array($cs_method) && $cs_method[0]=='searchContractList'){
    _cs_logs(array("查询合同列表接口开始"));
    global $adb;
    $reusltdata = $_REQUEST["data"];
    _cs_logs(array("查询合同列表接口参数:".$resultdata));
    $reusltdata = trim($reusltdata);
    $reusltdata = str_replace(' ', '+', $reusltdata);
    $decrdata = decrypt($reusltdata);
    $pad = strrpos($decrdata, '}');
    $decrdata = substr($decrdata, 0, $pad + 1);
    $decodedata = json_decode($decrdata, true);
    $contractids=$decodedata['contractids'];
    $excontractids=$decodedata['excontractids'];
    $accountid=$decodedata['accountid'];
    $accountids=$decodedata['accountids'];
    $userIds=$decodedata['userIds'];
    $search_field=$decodedata['search_field'];
    $search_value=$decodedata['search_value'];
    $field_type=$decodedata['field_type'];
    $search_type=$decodedata['search_type'];
    $isNewContract=$decodedata['isNewContract'];
    $allotDepartmentids=$decodedata['allotDepartmentids'];
    $smownerids=$decodedata['smownerids'];
    $isChiefInspector=$decodedata['isChiefInspector'];
    $moduleName=$decodedata['moduleName'];
    $loginId=$decodedata['loginId'];
    $businessDepartmentId = $decodedata['businessDepartmentId'];
    $businessCenterId = $decodedata['businessCenterId'];
    $businessRegionId = $decodedata['businessRegionId'];
    $sourceType = $decodedata['bsourceType'];
	
    //去掉空格
    if(!empty($search_value)){
        $search_value = trim($search_value);
    }

	if(empty($sourceType)){
        $sourceType = 0;
    }
	
    $pageNum=empty($decodedata['pageNum'])?0:$decodedata['pageNum'];
    $pageSize=empty($decodedata['pageSize'])|| $decodedata['pageSize']=='0'?10:$decodedata['pageSize'];
    $p_num = 0;
    if($pageNum>0){
        $p_num = $pageNum - 1;
    }
    $pageNum = $p_num * $pageSize;
    _cs_logs(array("查询合同列表接口解析后参数:".$decrdata));
    //_cs_logs(array("查询合同列表接口解析后分页参数:".$pageNum.'|'.$pageSize));

    include_once 'vtlib/Vtiger/Module.php';
    include_once 'includes/main/WebUI.php';
    include_once  'languages/zh_cn/Accounts.php';
    vglobal('default_language', $default_language);
    $currentLanguage = 'zh_cn';
    //Vtiger_Language_Handler::getLanguage();//2.语言设置
    vglobal('current_language',$currentLanguage);

    $query="SELECT
            vtiger_servicecontracts.servicecontractsid,
           vtiger_account.accountid AS accountid,
           (vtiger_account.accountname) as sc_related_to,
           IFNULL((SELECT sum(IFNULL(unit_price,0)) FROM vtiger_receivedpayments WHERE vtiger_receivedpayments.deleted=0 AND vtiger_receivedpayments.receivedstatus='normal' AND vtiger_receivedpayments.relatetoid=vtiger_servicecontracts.servicecontractsid),0) AS unit_price,
           IFNULL((SELECT 
            sum(IFNULL(vtiger_newinvoiceextend.totalandtaxextend,0)) 
            FROM `vtiger_newinvoiceextend` 
            LEFT JOIN vtiger_newinvoice ON vtiger_newinvoiceextend.invoiceid=vtiger_newinvoice.invoiceid 
            LEFT JOIN vtiger_crmentity AS invoicecrm ON invoicecrm.crmid=vtiger_newinvoice.invoiceid
            WHERE 
            invoicecrm.deleted=0
             AND vtiger_newinvoiceextend.deleted=0
            AND vtiger_newinvoice.modulestatus='c_complete'
            AND vtiger_newinvoiceextend.invoicestatus='normal'
            AND vtiger_newinvoice.contractid=vtiger_servicecontracts.servicecontractsid),0) AS totalandtax,
            vtiger_servicecontracts.total,
            vtiger_servicecontracts.modulestatus,
			vtiger_activationcode.classtype,
            vtiger_servicecontracts.contract_type,
            vtiger_servicecontracts.signdate,
			DATE_FORMAT(vtiger_servicecontracts.signfor_date,'%Y-%m-%d') AS signfor_date,
            vtiger_servicecontracts.effectivetime,
            vtiger_servicecontracts.contract_no,
            IFNULL((select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`isdimission`=1,'[离职]',''))) as last_name from vtiger_users where vtiger_account.serviceid=vtiger_users.id),'--') as service_name,
            vtiger_account.serviceid as serviceid,
			IFNULL((select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`isdimission`=1,'[离职]',''))) as last_name from vtiger_users where account_crm.smownerid=vtiger_users.id),'--') as smowner_name,
            account_crm.smownerid,
            IFNULL(vtiger_activationcode.usercode,vtiger_tyunstationsale.loginname) AS tyun_account,
			vtiger_activationcode.usercodeid,
			vtiger_activationcode.productid,
			vtiger_activationcode.expiredate,
			IFNULL(vtiger_activationcode.comeformtyun,0) AS account_source,
			(CASE vtiger_activationcode.comeformtyun
			WHEN 1 THEN 
				(SELECT GROUP_CONCAT(DISTINCT productname) FROM vtiger_activationcode WHERE `status` in(0,1) AND comeformtyun=1 AND customerid=vtiger_account.accountid AND contractid=vtiger_servicecontracts.servicecontractsid)
			ELSE
				(SELECT productname FROM vtiger_products WHERE tyunproductid=vtiger_activationcode.productid LIMIT 1)
			END) AS product_type
            FROM vtiger_servicecontracts FORCE index(signdate)
			INNER JOIN vtiger_crmentity ON vtiger_servicecontracts.servicecontractsid = vtiger_crmentity.crmid 
            LEFT JOIN vtiger_activationcode ON (vtiger_activationcode.contractid=CONCAT(vtiger_servicecontracts.servicecontractsid))
			LEFT JOIN vtiger_tyunstationsale ON (vtiger_tyunstationsale.contractid=vtiger_servicecontracts.servicecontractsid)
            LEFT JOIN vtiger_account ON (vtiger_account.accountid=vtiger_servicecontracts.sc_related_to)
			INNER JOIN vtiger_crmentity account_crm ON vtiger_account.accountid = account_crm.crmid
			LEFT JOIN vtiger_user2department D ON(account_crm.smownerid=D.userid)
            WHERE vtiger_crmentity.deleted=0 ";

    //总件数取得
    $query_total="SELECT 
            COUNT(DISTINCT IFNULL(vtiger_activationcode.contractid,0)) AS cnt
            FROM vtiger_servicecontracts 
			INNER JOIN vtiger_crmentity ON vtiger_servicecontracts.servicecontractsid = vtiger_crmentity.crmid 
            LEFT JOIN vtiger_activationcode ON vtiger_activationcode.contractid=CONCAT(vtiger_servicecontracts.servicecontractsid) 
			LEFT JOIN vtiger_tyunstationsale ON (vtiger_tyunstationsale.contractid=vtiger_servicecontracts.servicecontractsid)
            LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_servicecontracts.sc_related_to 
			INNER JOIN vtiger_crmentity account_crm ON vtiger_account.accountid = account_crm.crmid
			LEFT JOIN vtiger_user2department D ON(account_crm.smownerid=D.userid)
            WHERE vtiger_crmentity.deleted=0 ";

    //直销客户，合同类型属于T云的，合同是已签收的";
    $query_where = " AND vtiger_account.accountrank NOT IN('chan_notv','iron_isv')";
	if($sourceType == 1 || $sourceType == '1'){
		$query_where = " AND vtiger_activationcode.productclass=1";
	}else{
		$query_where = " AND (vtiger_activationcode.productclass != 1 OR vtiger_activationcode.productclass IS NULL)";
	}
    //按商务查询
    if(!empty($businessDepartmentId) && $businessDepartmentId !='0'){
        $query_where.= " AND D.departmentid='{$businessDepartmentId}'";
    }else {
        if(!empty($businessCenterId) && $businessCenterId !='0'){
            $query_where.= " AND D.departmentid IN(SELECT departmentid FROM vtiger_departments WHERE FIND_IN_SET('{$businessCenterId}',REPLACE(parentdepartment,'::',',')))";
        }else{
            if(!empty($businessRegionId) && $businessRegionId !='0'){
                $query_where.= " AND account_crm.smownerid IN(SELECT id FROM vtiger_users WHERE companyid='{$businessRegionId}')";
            }
        }
    }

    //商务分配部门
	if(!empty($allotDepartmentids)) {
		$allotDepartmentids = rtrim($allotDepartmentids,',');
		$query_where.= " AND D.departmentid IN ('".str_replace(',',"','",$allotDepartmentids)."')";		
	}
	
    //个人业绩录入读取已领取和已签收合同
    if(!empty($moduleName)){
        if($moduleName == 'achievementAdd'){
            $query_where .= " AND vtiger_servicecontracts.modulestatus IN ('c_complete', '已发放') ";
        }else{
            $query_where .= "  AND vtiger_servicecontracts.signfor_date IS NOT NULL AND vtiger_servicecontracts.modulestatus IN ('c_complete', 'c_cancel','c_history') ";
        }
    }else{
        $query_where .= "  AND vtiger_servicecontracts.signfor_date IS NOT NULL AND vtiger_servicecontracts.modulestatus IN ('c_complete', 'c_cancel','c_history') ";
    }
    $query_where .= "  AND (vtiger_activationcode.`status`IN(0,1) OR vtiger_tyunstationsale.`status` IN(0,1))";

    //针对黄玉琴、李季下单的订单，做特殊处理
    $query_where .= splicingSpecialSql($loginId);

    //新签收合同过滤掉已绑定客服合同
    if(!empty($isNewContract) && $isNewContract == '1'){
		$query_where .= " AND vtiger_servicecontracts.effectivetime>=CURRENT_DATE() AND vtiger_activationcode.classtype='buy'";
		if($sourceType == 1){
			$query_where .= " AND vtiger_account.accountid NOT IN(SELECT accountid FROM vtiger_account_allot_other)";
		}else{
			$query_where .= " AND vtiger_account.serviceid IS NULL";
		}
        
    }
    if(!empty($accountids)){
        $accountids = rtrim($accountids,',');
        $query_where .= " AND vtiger_servicecontracts.sc_related_to IN({$accountids})";
    }
    if(!empty($accountid)){
        $query_where .= " AND vtiger_servicecontracts.sc_related_to={$accountid}";
    }

    if(!empty($contractids)){
        $contractids = rtrim($contractids,',');
        $query_where .= " AND vtiger_servicecontracts.servicecontractsid IN({$contractids})";
    }
    if(!empty($excontractids)){
        $excontractids = rtrim($excontractids,',');
        $query_where .= " AND vtiger_servicecontracts.servicecontractsid NOT IN({$excontractids})";
    }

    if(!empty($userIds)){
        $userIds = rtrim($userIds,',');
        $query_where .= " AND (vtiger_account.serviceid IN({$userIds}) OR vtiger_account.accountid IN(SELECT accountid FROM vtiger_account_allot_other WHERE csid IN({$userIds})))";
    }
    if(!empty($search_value)) {
        //客户名称
        if($search_field == 'accountname'){
            $query_where .= " AND vtiger_account.accountname LIKE '%{$search_value}%'";
        }else if($search_field == 'product_type'){
            $query_where .= " AND vtiger_activationcode.productid IN(SELECT tyunproductid FROM vtiger_products WHERE productname LIKE '%{$search_value}%')";
			
        }else if($search_field == 'buy_type'){
            $query_where .= " AND vtiger_activationcode.classtype='{$search_value}'";
        }else{
            if($field_type == 'date' && !empty($search_value)){
                //模糊查询
                $search_value = str_replace('/', '-', $search_value);
                $query_where .= " AND vtiger_servicecontracts.{$search_field} LIKE '%{$search_value}%'";
            }
            if($field_type == 'list'){
                //精确查询
                $query_where .= " AND vtiger_servicecontracts.{$search_field}='{$search_value}'";
            }
            if($field_type == 'text'){
                if ($search_type == '1') {
                    //模糊查询
                    if(!empty($search_value)){
                        $query_where .= " AND vtiger_servicecontracts.{$search_field} LIKE '%{$search_value}%'";
                    }
                } else {
                    //精确查询
                    $query_where .= " AND vtiger_servicecontracts.{$search_field}='{$search_value}'";
                }
            }

        }
    }
	
    $query .= $query_where;
	$query .=" GROUP BY vtiger_activationcode.contractid";
    //"ORDER BY vtiger_servicecontracts.signdate DESC,vtiger_servicecontracts.servicecontractsid DESC";

    if(!empty($moduleName)){
        if($moduleName == 'renewContracts'){
            //$query .= " ORDER BY vtiger_activationcode.expiredate DESC";
        }else{
            // $query .= " ORDER BY vtiger_servicecontracts.signdate DESC";
        }
    }else{
        //$query .= " ORDER BY vtiger_servicecontracts.signdate DESC";
    }
    $query .= " ORDER BY vtiger_servicecontracts.signfor_date DESC";


    if(bccomp($pageSize,0)> 0){
        $query .= " LIMIT {$pageNum},{$pageSize}";
    }

    _cs_logs(array("执行合同查询列表sql：".$query));
    $sales = $adb->pquery($query, array());

    $rows = $adb->num_rows($sales);
    $ret_lists = array();
    if ($rows>0) {
        //_cs_logs(array("执行合同查询列表件数：".$rows));
        while($row=$adb->fetchByAssoc($sales)){
            $lists = array();
            //合同ID
            $lists['contractid']=$row['servicecontractsid'];
            //客户ID
            $lists['accountid']=$row['accountid'];
            //客户名称
            $lists['account_name']=$row['sc_related_to'];
            //回款总额
            $lists['payment_total']=$row['unit_price'];
            //尾款
            if(bccomp($row['total'],0)> 0){
                $lists['tail_money']=bcsub($row['total'],$row['unit_price']);
            }else{
                $lists['tail_money']='--';
            }

            //开票总额
            $lists['invoice_total']=$row['totalandtax'];
            //合同金额
            if(bccomp($row['total'],0)> 0){
                $lists['contract_total']=$row['total'];
            }else{
                $lists['contract_total']='--';
            }
            //产品类型
            $lists['productid']=$row['productid'];
            $lists['product_type']=$row['product_type'];
            //合同状态
            $lists['modulestatus']=$row['modulestatus'];
            $lists['modulestatus_name']=vtranslate($row['modulestatus'],"ServiceContracts");
            //新增/续费/升级/另购
            $lists['servicecontractstype']=$row['classtype'];
            $servicecontractstype_name="--";
            if($row['classtype'] == 'buy'){
                $servicecontractstype_name = '首购';
            }
            if($row['classtype'] == 'upgrade'){
                $servicecontractstype_name = '升级';
            }
            if($row['classtype'] == 'cupgrade'){
                $servicecontractstype_name = '迁移升级';
            }
            if($row['classtype'] == 'renew'){
                $servicecontractstype_name = '续费';
            }
            if($row['classtype'] == 'crenew'){
                $servicecontractstype_name = '迁移续费';
            }
            if($row['classtype'] == 'againbuy'){
                $servicecontractstype_name = '另购';
            }
            if($row['classtype'] == 'degrade'){
                $servicecontractstype_name = '降级';
            }
            if($row['classtype'] == 'cdegrade'){
                $servicecontractstype_name = '迁移降级';
            }
            $lists['servicecontractstype_name']=$servicecontractstype_name;
            //合同类型
            $lists['contract_type']=$row['contract_type'];

            //用户id
            $lists['usercodeid']=$row['usercodeid'];

            //签订日期
            $lists['signdate']=$row['signdate'];
            //签收日期
            $lists['signfor_date']=$row['signfor_date'];
            //到期日期
            $lists['expire_date']=$row['effectivetime'];
            //合同编号
            $lists['contract_no']=$row['contract_no'];
            //发票比例
            if(bccomp($row['total'],0)> 0){
                $lists['invoice_scale']=bcmul(bcdiv($row['totalandtax'],$row['total'],4),100,2).'%';
                //回款比例
                $payment_scale = bcmul(bcdiv($row['unit_price'],$row['total'],4),100,2);
                $lists['payment_scale']=$payment_scale.'%';
                //是否回款不足(1:是，0：否)
                $lists['is_payment_insufficient']=(bccomp($payment_scale,50)<0)?1:0;
            }else{
                $lists['invoice_scale']='--';
                $lists['payment_scale']='--';
                //是否回款不足(1:是，0：否)
                $lists['is_payment_insufficient']=0;
            }
            //T云账号
            $lists['tyun_account']=$row['tyun_account'];
            //负责客服
            $lists['serviceid']=$row['serviceid'];
            $lists['service_name']=$row['service_name'];
            //负责商务
            $lists['smownerid']=$row['smownerid'];
            $lists['smowner_name']=$row['smowner_name'];
            $lists['accountSource']=$row['account_source'];


            $ret_lists[]=$lists;
        }

        $query_total.=$query_where;
        _cs_logs(array("执行合同查询总件数SQL：".$query_total));

        $result_total = $adb->pquery($query_total, array());
        $row_total = $adb->query_result_rowdata($result_total);
        $total = $row_total["cnt"];
        $total = empty($total)?0:$total;
        $data=json_encode($ret_lists,JSON_UNESCAPED_UNICODE );
        $data=encrypt($data);
        echo json_encode(array("success"=>true,"is_multiple"=>true,"total"=>$total,"pageNum"=>$pageNum,"pageSize"=>$pageSize,"data"=>$data));

        _cs_logs(array("根据客户获取合同列表成功"));
    }else{
        echo json_encode(array("success"=>true,"is_multiple"=>true,"total"=>0,"pageNum"=>$pageNum,"pageSize"=>$pageSize,"data"=>$ret_lists));
    }
    _cs_logs(array("根据客户获取合同列表接口结束"));
    exit;
}
//=================查询续费合同列表API================================================================
if(is_array($cs_method) && $cs_method[0]=='searchRenewContractList'){
    _cs_logs(array("查询续费合同列表接口开始"));
    global $adb;
    $reusltdata = $_REQUEST["data"];
    _cs_logs(array("查询续费合同列表接口参数:".$resultdata));
    $reusltdata = trim($reusltdata);
    $reusltdata = str_replace(' ', '+', $reusltdata);
    $decrdata = decrypt($reusltdata);
    $pad = strrpos($decrdata, '}');
    $decrdata = substr($decrdata, 0, $pad + 1);
    $decodedata = json_decode($decrdata, true);
    $search_type=$decodedata['searchType'];
    $content=$decodedata['content'];
    $userIds=$decodedata['userIds'];
    $accountIds=$decodedata['accountIds'];

    //去掉空格
    if(!empty($content)){
        $content = trim($content);
    }

    $pageNum=empty($decodedata['pageNum'])?0:$decodedata['pageNum'];
    $pageSize=empty($decodedata['pageSize'])|| $decodedata['pageSize']=='0'?10:$decodedata['pageSize'];
    $p_num = 0;
    if($pageNum>0){
        $p_num = $pageNum - 1;
    }
    $pageNum = $p_num * $pageSize;
    _cs_logs(array("查询续费合同列表接口解析后参数:".$decrdata));
    //_cs_logs(array("查询续费合同列表接口解析后分页参数:".$pageNum.'|'.$pageSize));

    include_once 'vtlib/Vtiger/Module.php';
    include_once 'includes/main/WebUI.php';
    include_once  'languages/zh_cn/Accounts.php';
    vglobal('default_language', $default_language);
    $currentLanguage = 'zh_cn';
    //Vtiger_Language_Handler::getLanguage();//2.语言设置
    vglobal('current_language',$currentLanguage);

    $query="SELECT DISTINCT
            vtiger_servicecontracts.servicecontractsid,
            IFNULL(D.accountid,vtiger_account.accountid) AS accountid,
            IFNULL(D.accountname,vtiger_account.accountname) as sc_related_to,
            vtiger_servicecontracts.total,
            vtiger_servicecontracts.modulestatus,
			B.classtype,
            vtiger_servicecontracts.contract_type,
            vtiger_servicecontracts.signdate,
			DATE_FORMAT(vtiger_servicecontracts.signfor_date,'%Y-%m-%d') AS signfor_date,
            vtiger_servicecontracts.effectivetime,
            vtiger_servicecontracts.contract_no,
            IFNULL((select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`isdimission`=1,'[离职]',''))) as last_name from vtiger_users where IFNULL(D.serviceid,vtiger_account.serviceid)=vtiger_users.id),'--') as service_name,
            IFNULL(D.serviceid,vtiger_account.serviceid) as serviceid,
			IFNULL((select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`isdimission`=1,'[离职]',''))) as last_name from vtiger_users where account_crm.smownerid=vtiger_users.id),'--') as smowner_name,
            account_crm.smownerid,
            B.usercode AS tyun_account,
			B.comeformtyun AS accountSource,
			B.expiredate,
			B.receivetime,
			(CASE B.comeformtyun
			WHEN 1 THEN 
				(SELECT GROUP_CONCAT(DISTINCT productname) FROM vtiger_activationcode WHERE `status` in(0,1) AND comeformtyun=1 AND contractid=vtiger_servicecontracts.servicecontractsid)
			ELSE
				(SELECT productname FROM vtiger_products WHERE tyunproductid=B.productid LIMIT 1)
			END) AS product_type
            FROM vtiger_servicecontracts 
			INNER JOIN vtiger_crmentity ON vtiger_servicecontracts.servicecontractsid = vtiger_crmentity.crmid 
            INNER JOIN vtiger_activationcode B ON (B.contractid=CONCAT(vtiger_servicecontracts.servicecontractsid))
            LEFT JOIN vtiger_activationcode C ON(C.activationcodeid=B.buyid AND C.`status` in(0,1) AND C.classtype='buy')
            LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_servicecontracts.sc_related_to 
			LEFT JOIN vtiger_account D ON (D.accountid=C.customerid) 
			LEFT JOIN vtiger_crmentity account_crm ON (vtiger_account.accountid = account_crm.crmid)
            WHERE vtiger_crmentity.deleted=0 ";

    //总件数取得
    $query_total="SELECT 
            COUNT(DISTINCT IFNULL(vtiger_servicecontracts.servicecontractsid,0)) AS cnt
            FROM vtiger_servicecontracts 
			INNER JOIN vtiger_crmentity ON vtiger_servicecontracts.servicecontractsid = vtiger_crmentity.crmid 
            INNER JOIN vtiger_activationcode B ON B.contractid=CONCAT(vtiger_servicecontracts.servicecontractsid) 
			LEFT JOIN vtiger_activationcode C ON(C.activationcodeid=B.buyid AND C.`status` in(0,1) AND C.classtype='buy')
            LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_servicecontracts.sc_related_to 
			LEFT JOIN vtiger_account D ON (D.accountid=C.customerid) 
			LEFT JOIN vtiger_crmentity account_crm ON vtiger_account.accountid = account_crm.crmid
            WHERE vtiger_crmentity.deleted=0 ";

    //直销客户、当月续费
    //$query_where = " AND vtiger_account.accountrank!='iron_isv'
    //				AND DATE_FORMAT(B.receivetime,'%Y-%m') = DATE_FORMAT(CURDATE(),'%Y-%m') AND B.classtype in('upgrade','degrade','renew') AND B.`status` in(0,1)";
    $query_where = " AND DATE_FORMAT(B.receivetime,'%Y-%m') = DATE_FORMAT(CURDATE(),'%Y-%m') AND B.classtype in('upgrade','degrade','renew','cupgrade','cdegrade','crenew') AND B.`status` in(0,1)";

    if(!empty($accountIds)){
        $accountIds = rtrim($accountIds,',');
        $query_where .= " AND IFNULL(D.accountid,B.customerid) in({$accountIds})";
    }


    if(!empty($userIds)){
        $userIds = rtrim($userIds,',');
        $query_where .= " AND IFNULL(D.serviceid,vtiger_account.serviceid) IN({$userIds})";
    }

    if(!empty($content)) {
        //客户名称
        if($search_type == '1'){
            $query_where .= " AND IFNULL(D.accountname,vtiger_account.accountname) LIKE '%{$content}%'";
        }
        //合同编号
        if($search_type == '2'){
            $query_where .= " AND vtiger_servicecontracts.contract_no LIKE '%{$content}%'";
        }
        //T云账号
        if($search_type == '3'){
            $query_where .= " AND B.usercode LIKE '%{$content}%'";
        }
    }


    $query .= $query_where;
    $query .= " ORDER BY vtiger_servicecontracts.signdate DESC";
    if(bccomp($pageSize,0)> 0){
        $query .= " LIMIT {$pageNum},{$pageSize}";
    }

    //_cs_logs(array("执行续费合同查询列表sql：".$query));
    $sales = $adb->pquery($query, array());

    $rows = $adb->num_rows($sales);
    $ret_lists = array();
    if ($rows>0) {
        //_cs_logs(array("执行续费合同查询列表件数：".$rows));
        while($row=$adb->fetchByAssoc($sales)){
            $lists = array();
            //合同ID
            $lists['contractid']=$row['servicecontractsid'];
            //客户ID
            $lists['accountid']=$row['accountid'];
            //客户名称
            $lists['account_name']=$row['sc_related_to'];

            //合同金额
            if(bccomp($row['total'],0)> 0){
                $lists['contract_total']=$row['total'];
            }else{
                $lists['contract_total']='--';
            }
            //产品类型
            //$lists['productid']=$row['productid'];
            $lists['product_type']=$row['product_type'];
            //合同状态
            $lists['modulestatus']=$row['modulestatus'];
            $lists['modulestatus_name']=vtranslate($row['modulestatus'],"ServiceContracts");
            //新增/续费/升级/另购
            $lists['servicecontractstype']=$row['classtype'];
            $servicecontractstype_name="--";
            if($row['classtype'] == 'buy'){
                $servicecontractstype_name = '首购';
            }
            if($row['classtype'] == 'upgrade'){
                $servicecontractstype_name = '升级';
            }
            if($row['classtype'] == 'cupgrade'){
                $servicecontractstype_name = '迁移升级';
            }
            if($row['classtype'] == 'renew'){
                $servicecontractstype_name = '续费';
            }
            if($row['classtype'] == 'crenew'){
                $servicecontractstype_name = '迁移续费';
            }
            if($row['classtype'] == 'againbuy'){
                $servicecontractstype_name = '另购';
            }
            if($row['classtype'] == 'degrade'){
                $servicecontractstype_name = '降级';
            }
            if($row['classtype'] == 'cdegrade'){
                $servicecontractstype_name = '迁移降级';
            }
            $lists['servicecontractstype_name']=$servicecontractstype_name;
            //合同类型
            $lists['contract_type']=$row['contract_type'];
            //签订日期
            $lists['signdate']=$row['signdate'];
            //签收日期
            $lists['signfor_date']=$row['signfor_date'];
            //到期日期
            $lists['expire_date']=$row['effectivetime'];
            //合同编号
            $lists['contract_no']=$row['contract_no'];
            //T云账号
            $lists['tyun_account']=$row['tyun_account'];
            //负责客服
            $lists['serviceid']=$row['serviceid'];
            $lists['service_name']=$row['service_name'];
            //负责商务
            $lists['smownerid']=$row['smownerid'];
            $lists['smowner_name']=$row['smowner_name'];
            //下单时间
            $lists['receivetime']=$row['receivetime'];

            $ret_lists[]=$lists;
        }

        $query_total.=$query_where;
        //_cs_logs(array("执行续费合同查询总件数SQL：".$query_total));

        $result_total = $adb->pquery($query_total, array());
        $row_total = $adb->query_result_rowdata($result_total);
        $total = $row_total["cnt"];
        $total = empty($total)?0:$total;
        $data=json_encode($ret_lists,JSON_UNESCAPED_UNICODE );
        $data=encrypt($data);
        echo json_encode(array("success"=>true,"is_multiple"=>true,"total"=>$total,"pageNum"=>$pageNum,"pageSize"=>$pageSize,"data"=>$data));

        _cs_logs(array("续费合同列表成功"));
    }else{
        echo json_encode(array("success"=>true,"is_multiple"=>true,"total"=>0,"pageNum"=>$pageNum,"pageSize"=>$pageSize,"data"=>$ret_lists));
        _cs_logs(array("没有查询到续费合同列表数据"));
    }
    _cs_logs(array("续费合同列表接口结束"));
    exit;
}
//=================查询客户列表API================================================================
if(is_array($cs_method) && $cs_method[0]=='searchAccountList'){
    _cs_logs(array("查询客户列表接口开始"));
    global $adb;
    $reusltdata = $_REQUEST["data"];
    _cs_logs(array("查询客户列表接口参数:".$resultdata));
    //$reusltdata = trim($reusltdata);
    //$reusltdata = str_replace(' ', '+', $reusltdata);
    //$decrdata = decrypt($reusltdata);
    //$pad = strrpos($decrdata, '}');
    //$decrdata = substr($decrdata, 0, $pad + 1);
    //$decodedata = json_decode($decrdata, true);
	$decodedata = json_decode($reusltdata, true);
	 _cs_logs(array("查询客户列表接口解析后参数:".$decodedata));
    $search_field=$decodedata['searchField'];
    $search_value=$decodedata['searchValue'];
    $search_type=$decodedata['searchType'];
    $field_type=$decodedata['fieldType'];
    $accountids=$decodedata['accountIds'];
    $serviceid=$decodedata['serviceid'];
    $serviceids=$decodedata['serviceids'];
    $assign_serviceids=$decodedata['assign_serviceids'];

    $excludeAccountFlag=$decodedata['excludeAccountFlag'];
    $viewIroncard=$decodedata['bViewIroncard'];
    $moduleName=$decodedata['moduleName'];
    $departmentids=$decodedata['departmentids'];
    $smownerids=$decodedata['smownerids'];
    $isChiefInspector=$decodedata['isChiefInspector'];
    $notJoinCourseTyunNames=$decodedata['notJoinCourseTyunNames'];
    $orderColumn=$decodedata['orderColumn'];
    $order=$decodedata['order'];
    $followupType=$decodedata['followupType'];
    $taskId = $decodedata['taskId'];
    $businessDepartmentId = $decodedata['businessDepartmentId'];
    $businessCenterId = $decodedata['businessCenterId'];
    $businessRegionId = $decodedata['businessRegionId'];
    $sourceType = $decodedata['bsourceType'];
    $accountrank = $decodedata['accountrank'];
	$listExpireAccountId = $decodedata['listExpireAccountId'];
	$listRemindAccountId = $decodedata['listRemindAccountId'];
	
    //去掉空格
    if(!empty($search_value)){
        $search_value = trim($search_value);
    }

	if(empty($viewIroncard)){
        $viewIroncard = 0;
    }
	
	if(empty($sourceType)){
        $sourceType = 0;
    }
	
    $pageNum=empty($decodedata['pageNum'])?0:$decodedata['pageNum'];
    $pageSize=empty($decodedata['pageSize'])|| $decodedata['pageSize']=='0'?10:$decodedata['pageSize'];
    $p_num = 0;
    if($pageNum>0){
        $p_num = $pageNum - 1;
    }
    $pageNum = $p_num * $pageSize;
   
    //_cs_logs(array("查询客户等级:".$isIroncard));
    include_once 'vtlib/Vtiger/Module.php';
    include_once 'includes/main/WebUI.php';
    include_once  'languages/zh_cn/Accounts.php';
    vglobal('default_language', $default_language);
    $currentLanguage = 'zh_cn';
    //Vtiger_Language_Handler::getLanguage();//2.语言设置
    vglobal('current_language',$currentLanguage);

    $query="SELECT TT.*,
			IFNULL((SELECT GROUP_CONCAT(DISTINCT productname) as productname FROM vtiger_activationcode WHERE `status` in(0,1) AND comeformtyun=1 AND customerid=TT.accountid),TT.producttype) as servicetype_name,
			IFNULL((select CONCAT(last_name,(if(`isdimission`=1,'[离职]',''))) as last_name from vtiger_users where TT.smownerid=vtiger_users.id),'') as smowner_name,
			(SELECT date_format(MAX(signfor_date),'%Y-%m-%d') FROM vtiger_servicecontracts WHERE sc_related_to=TT.accountid) AS signfor_date,
			IFNULL((SELECT 
            SUM(IFNULL(vtiger_newinvoiceextend.totalandtaxextend,0)) 
            FROM `vtiger_newinvoiceextend` 
            LEFT JOIN vtiger_newinvoice ON vtiger_newinvoiceextend.invoiceid=vtiger_newinvoice.invoiceid 
            LEFT JOIN vtiger_crmentity AS invoicecrm ON invoicecrm.crmid=vtiger_newinvoice.invoiceid
            WHERE 
            invoicecrm.deleted=0
			AND vtiger_newinvoice.contractid IN(SELECT contractid FROM vtiger_activationcode WHERE customerid=TT.accountid AND status IN(0,1))
            AND vtiger_newinvoiceextend.deleted=0
            AND vtiger_newinvoice.modulestatus='c_complete'
            AND vtiger_newinvoiceextend.invoicestatus='normal'
            AND vtiger_newinvoice.accountid=TT.accountid),0) AS invoiceamount,
			IFNULL((SELECT SUM(A.total) FROM vtiger_servicecontracts A
			INNER JOIN vtiger_crmentity B ON(A.servicecontractsid=B.crmid)
			WHERE B.deleted=0 
			AND A.servicecontractsid IN(SELECT contractid FROM vtiger_activationcode WHERE customerid=TT.accountid AND status IN(0,1))
			AND A.modulestatus IN ('c_complete','c_history') AND A.sc_related_to=TT.accountid),0) AS total,
            IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=TT.smownerid LIMIT 1)),'--') as sales_departmentname
			FROM (";

    $query.="SELECT DISTINCT
			vtiger_account.accountid,
			vtiger_account.accountname,
			vtiger_account.accountrank,
			vtiger_account.producttype,
			vtiger_crmentity.smownerid,
			vtiger_account.serviceid,
			date_format(vtiger_account.allottime,'%Y-%m-%d %H:%i:%S') AS allottime, 
			IF(date_format(vtiger_account.allottime,'%Y-%m-%d') > ADDDATE(CURDATE(),INTERVAL -7 DAY),1,0) AS is_new_allot,
			vtiger_account.industry,
			vtiger_account.business,
			vtiger_account.accountcategory,
			vtiger_account.customertype,
			vtiger_account.customerproperty,
			vtiger_crmentity.createdtime,
			vtiger_crmentity.modifiedtime,
			vtiger_account.email1 AS email,
			date_format((SELECT MAX(addtime) FROM vtiger_modcomments A1 WHERE A1.related_to=vtiger_account.accountid and A1.followrole=1),'%Y-%m-%d %H:%i:%S') AS last_mod_time,
			vtiger_crmentity.description,
			D.departmentid
			FROM vtiger_account 
			JOIN vtiger_crmentity ON vtiger_account.accountid = vtiger_crmentity.crmid 
			LEFT JOIN vtiger_servicecontracts ON (vtiger_account.accountid=vtiger_servicecontracts.sc_related_to AND vtiger_servicecontracts.modulestatus IN ('c_complete', 'c_cancel','c_history') 
			                                      AND vtiger_servicecontracts.signfor_date IS NOT NULL)
			LEFT JOIN vtiger_user2department D ON(vtiger_crmentity.smownerid=D.userid)
			WHERE vtiger_crmentity.deleted=0";

    $query_total="SELECT count(DISTINCT vtiger_account.accountid) AS cnt FROM vtiger_account 
			JOIN vtiger_crmentity ON vtiger_account.accountid = vtiger_crmentity.crmid 
			LEFT JOIN vtiger_servicecontracts ON (vtiger_account.accountid=vtiger_servicecontracts.sc_related_to AND vtiger_servicecontracts.modulestatus IN ('c_complete', 'c_cancel','c_history') 
			                                      AND vtiger_servicecontracts.signfor_date IS NOT NULL)
			LEFT JOIN vtiger_user2department D ON(vtiger_crmentity.smownerid=D.userid)									  
			WHERE vtiger_crmentity.deleted=0";
	
	
    //$query_where = " AND vtiger_servicecontracts.modulestatus IN ('c_complete', 'c_cancel','c_history')
    //		AND vtiger_servicecontracts.signfor_date IS NOT NULL ";
    $query_where = "";

    if($sourceType == 1){
		$query_where.= " AND vtiger_account.accountid IN(SELECT accountid FROM `vtiger_account_allot_other`) ";
	}else{
		$query_where.= " AND vtiger_account.serviceid IS NOT NULL ";
	}
	
	if(!empty($accountrank)){
		$query_where.= " AND vtiger_account.accountrank='{$accountrank}'";
	}
	
	if(!empty($listExpireAccountId)){
		$expireAccountIds = implode(',',$listExpireAccountId);
		$query_where.= " AND vtiger_account.accountid IN({$expireAccountIds})";
	}
	
	if(!empty($listRemindAccountId)){
		$remindAccountIds = implode(',',$listRemindAccountId);
		$query_where.= " AND vtiger_account.accountid IN({$remindAccountIds})";
	}
	
	
	//过滤掉黄玉琴和李季下单的客户 2020/02/17 黄玉琴微信提需求  只过滤新购和续费的客户
	//$query_where = " AND vtiger_account.accountid NOT IN (SELECT CAST(customerid AS SIGNED) FROM vtiger_activationcode WHERE classtype IN('buy','renew','crenew') AND creator IN(1179,2824) and CAST(customerid AS SIGNED)>0)";
	
    //按商务查询
    if(!empty($businessDepartmentId) && $businessDepartmentId !='0'){
        $query_where.= " AND D.departmentid='{$businessDepartmentId}'";
    }else {
        if(!empty($businessCenterId) && $businessCenterId !='0'){
            $query_where.= " AND D.departmentid IN(SELECT departmentid FROM vtiger_departments WHERE FIND_IN_SET('{$businessCenterId}',REPLACE(parentdepartment,'::',',')))";
        }else{
            if(!empty($businessRegionId) && $businessRegionId !='0'){
                $query_where.= " AND vtiger_crmentity.smownerid IN(SELECT id FROM vtiger_users WHERE companyid='{$businessRegionId}')";
            }
        }
    }

    if($search_field == 'noAllotService') {
    }else{
		_cs_logs(array("查询客户列表接口客户Ids:".$accountids));
        if(!empty($accountids)){
			$accountids = rtrim($accountids,',');
			$query_where.= " AND vtiger_account.accountid IN({$accountids})";
        }

        if(!empty($search_field)) {
            if($search_field == 'custom_service') {
                //负责客服
                //$query_where .= " AND EXISTS(select 1 from vtiger_users where vtiger_account.serviceid=vtiger_users.id and vtiger_users.last_name='{$search_value}')";
                if(!empty($search_value)){
                    //$query_where .= " AND EXISTS(select 1 from vtiger_users where vtiger_account.serviceid=vtiger_users.id and vtiger_users.last_name LIKE '%{$search_value}%')";
					$query_where .= " AND (EXISTS(select 1 from vtiger_users where vtiger_account.serviceid=vtiger_users.id and vtiger_users.last_name LIKE '%{$search_value}%')
				  OR EXISTS(select 1 from vtiger_users where vtiger_users.id IN(SELECT csid FROM vtiger_account_allot_other WHERE accountid=vtiger_account.accountid) and vtiger_users.last_name LIKE '%{$search_value}%'))";
                }
            }else if($search_field == 'custom_sales') {
                //负责商务
                //$query_where .= " AND EXISTS(select 1 from vtiger_users where vtiger_crmentity.smownerid=vtiger_users.id and vtiger_users.last_name='{$search_value}')";
                if(!empty($search_value)){
                    $query_where .= " AND EXISTS(select 1 from vtiger_users where vtiger_crmentity.smownerid=vtiger_users.id and vtiger_users.last_name LIKE '%{$search_value}%')";
                }
			}else if($search_field == 'service_status') {
                //客服状态
                if(!empty($search_value)){
                    $query_where .= " AND vtiger_account.serviceid IN(select id from vtiger_users where vtiger_users.isdimission='{$search_value}')";
                }
            }else if($search_field == 'departmentname') {
                //商务所属部门
                $query_where .= " AND vtiger_crmentity.smownerid IN(SELECT A.userid FROM vtiger_user2department A
								LEFT JOIN vtiger_departments B ON(A.departmentid=B.departmentid)
								WHERE B.departmentname LIKE '%{$search_value}%')";
            }else if($search_field == 'servicetype') {
                //业务类型
                //$query_where .= " AND EXISTS(SELECT 1 FROM vtiger_products WHERE FIND_IN_SET(productid,REPLACE(vtiger_account.servicetype,' |##| ',',')) AND vtiger_products.productname LIKE '%{$search_value}%')";
				$query_where .= " AND vtiger_account.accountid IN(SELECT customerid FROM vtiger_activationcode WHERE `status` in(0,1) AND comeformtyun=1 AND productname LIKE '%{$search_value}%')";
            }else if($search_field == 'group_name') {
                //按组名查询
                $serviceids = rtrim($serviceids,',');
                $query_where .= " AND vtiger_account.serviceid IN({$serviceids})";
            }else{
                if($field_type == 'date' && !empty($search_value)){
                    //模糊查询
                    $search_value = str_replace('/', '-', $search_value);
                    $query_where .= " AND {$search_field} LIKE '%{$search_value}%'";
                }
                if($field_type == 'list' && !empty($search_value)){
                    //精确查询
                    $query_where .= " AND {$search_field}='{$search_value}'";
                }
                if($field_type == 'text'){
                    if ($search_type == '1') {
                        //模糊查询
                        if(!empty($search_value)){
                            $query_where .= " AND {$search_field} LIKE '%{$search_value}%'";
                        }
                    } else {
                        //精确查询
                        $query_where .= " AND {$search_field}='{$search_value}'";
                    }
                }
            }
        }
    }

    if($viewIroncard == 2 || $viewIroncard == '2'){
        $query_where.=" AND vtiger_account.accountrank='iron_isv'";
    }else{
        //黄玉琴：客服经理，主管，员工，列表中的铁牌客户隐藏下,显示金银铜的客户,但是搜索客户公司名称的时候可以搜索到
		if($viewIroncard == 1 || $viewIroncard == '1' || ($search_field == 'accountname' && !empty($search_value))){
		}else{
			$query_where.=" AND vtiger_account.accountrank IN('visp_isv','gold_isv','silv_isv','bras_isv')";
		}
    }

    if(!empty($serviceid)) {
        $query_where.= " AND vtiger_account.serviceid={$serviceid}";
    }

    //未参加培训邀约的客户
    if(!empty($notJoinCourseTyunNames)){
        $query_where.= " AND vtiger_account.accountid NOT IN(SELECT DISTINCT cast(customerid as SIGNED) FROM vtiger_activationcode WHERE customerid IS NOT NULL AND FIND_IN_SET(usercode,'".$notJoinCourseTyunNames."'))";
    }

    $all_userid = array();
    //通过商务部门查找商务
    //1. 中小体系的那几个部门，分公司加一二三四营加客服部，不区分合同类型；
    //2. 其他部门，判断合同类型属于T云的，黄玉琴才可以看
    //_cs_logs(array("部门字符：". $departmentids));
    if(!empty($departmentids)) {
        $arr_departmentid = explode(";",$departmentids);
        //_cs_logs(array("部门：". implode(',',$arr_departmentid)));
        if(count($arr_departmentid)>0) {
            for ($i = 0; $i < count($arr_departmentid); $i++) {
                //_cs_logs(array("部门id为".$arr_departmentid[$i]));
                $arr_userid = getDepartmentUser($arr_departmentid[$i]);
                //_cs_logs(array("部门id为".$arr_departmentid[$i]."的商务：". implode(',',$arr_userid)));
                $all_userid = array_merge($all_userid,$arr_userid);
            }
        }
    }
    //_cs_logs(array("部门对应的商务：". implode(',',$all_userid)));
    if(count($all_userid)>0){
        if($isChiefInspector == '1'){
            if($taskId != '66'){
                //不是当日跟进
                //$query_where.= " AND (vtiger_servicecontracts.parent_contracttypeid=2 OR vtiger_crmentity.smownerid IN (".implode(',',$all_userid)."))";
                $query_where.= " AND (vtiger_servicecontracts.parent_contracttypeid=2 OR vtiger_servicecontracts.signid IS NULL OR vtiger_servicecontracts.signid IN (".implode(',',$all_userid)."))";
            }
        }else{
            //查找所属客服的客户
            if(!empty($assign_serviceids)) {
                $assign_serviceids = rtrim($assign_serviceids,',');
                //$query_where .= " AND (vtiger_crmentity.smownerid IN (".implode(',',$all_userid).") OR vtiger_account.serviceid IN({$assign_serviceids}))";
                $query_where .= " AND (vtiger_servicecontracts.signid IN (".implode(',',$all_userid).") OR vtiger_servicecontracts.signid IS NULL OR vtiger_account.serviceid IN({$assign_serviceids}))";
            }else{
                //$query_where.= " AND vtiger_crmentity.smownerid IN (".implode(',',$all_userid).")";
                $query_where.= " AND (vtiger_servicecontracts.signid IS NULL OR vtiger_servicecontracts.signid IN (".implode(',',$all_userid)."))";
            }
        }
    }

    //跟进类型
    if(!empty($followupType)){
        if($followupType == 'y_followup_7'){
            $query_where.=" AND EXISTS (SELECT 1 FROM vtiger_modcomments C WHERE C.related_to = vtiger_account.accountid  and C.followrole=1 and C.modulename='Accounts' AND C.addtime >= ADDDATE(CURDATE(),INTERVAL -7 DAY))";
        }else if($followupType == 'y_followup_15'){
            $query_where.=" AND EXISTS (SELECT 1 FROM vtiger_modcomments C WHERE C.related_to = vtiger_account.accountid and C.followrole=1 AND C.modulename='Accounts' AND C.addtime >= ADDDATE(CURDATE(),INTERVAL -15 DAY))";
        }else if($followupType == 'y_followup_30'){
            $query_where.=" AND EXISTS (SELECT 1 FROM vtiger_modcomments C WHERE C.related_to = vtiger_account.accountid and C.followrole=1 AND C.modulename='Accounts' AND C.addtime >= ADDDATE(CURDATE(),INTERVAL -30 DAY))";
        }else if($followupType == 'y_followup_90'){
            $query_where.=" AND EXISTS (SELECT 1 FROM vtiger_modcomments C WHERE C.related_to = vtiger_account.accountid and C.followrole=1 AND C.modulename='Accounts' AND C.addtime >= ADDDATE(CURDATE(),INTERVAL -90 DAY))";
        }else if($followupType == 'n_followup_7'){
            $query_where.=" AND NOT EXISTS (SELECT 1 FROM vtiger_modcomments C WHERE C.related_to = vtiger_account.accountid and C.followrole=1 AND C.modulename='Accounts' AND C.addtime >= ADDDATE(CURDATE(),INTERVAL -7 DAY))";
        }else if($followupType == 'n_followup_15'){
            $query_where.=" AND NOT EXISTS (SELECT 1 FROM vtiger_modcomments C WHERE C.related_to = vtiger_account.accountid and C.followrole=1 AND C.modulename='Accounts' AND C.addtime >= ADDDATE(CURDATE(),INTERVAL -15 DAY))";
        }else if($followupType == 'n_followup_30'){
            $query_where.=" AND NOT EXISTS (SELECT 1 FROM vtiger_modcomments C WHERE C.related_to = vtiger_account.accountid and C.followrole=1 AND C.modulename='Accounts' AND C.addtime >= ADDDATE(CURDATE(),INTERVAL -30 DAY))";
        }else if($followupType == 'n_followup_90'){
            $query_where.=" AND NOT EXISTS (SELECT 1 FROM vtiger_modcomments C WHERE C.related_to = vtiger_account.accountid and C.followrole=1 AND C.modulename='Accounts' AND C.addtime >= ADDDATE(CURDATE(),INTERVAL -90 DAY))";
        }
    }

    $query.=$query_where;
    //排序
    if(!empty($orderColumn)){
        if($order == 'descending'){
            $query.= " ORDER BY {$orderColumn} DESC";
        }else{
            $query.= " ORDER BY {$orderColumn}";
        }
    }
    $query.= " LIMIT {$pageNum},{$pageSize}";

    $query_total.=$query_where;

    $query.=") TT";

    //_cs_logs(array("执行查询sql：".$query));
    $result = $adb->pquery($query, array());
    $rows = $adb->num_rows($result);
	_cs_logs(array("执行客户查询件数：".$rows));
    $ret_lists = array();
    if ($rows>0) {
        while($row=$adb->fetchByAssoc($result)) {
            $lists = array();
            //客户ID
            $lists['accountid']=$row['accountid'];
            //客户名称
            $lists['accountname']=$row['accountname'];
            //客户等级
            $lists['accountrank']=$row['accountrank'];
            $lists['accountrank_name']=vtranslate($row['accountrank'],"Accounts");
            //负责商务
            $lists['smownerid']=$row['smownerid'];
            $lists['smowner_name']=$row['smowner_name'];
            //负责商务部门
            $lists['sales_departmentname']=$row['sales_departmentname'];
            //是否离职
            //$lists['isquit'] = $row['isquit'];
            //负责客服
            //$lists['serviceid']=$row['serviceid'];
            //$lists['service_name']=$row['service_name'];
            //分配时间
            $lists['allottime']=$row['allottime'];
            //是否新分配
            $lists['is_new_allot']=$row['is_new_allot'];
            //最后跟进时间
            $lists['last_mod_time']=$row['last_mod_time'];
            //行业
            $lists['industry']=vtranslate($row['industry'],"Accounts");
            //主营业务
            $lists['business']=$row['business'];
            //保护模式
            $lists['accountcategory']=vtranslate($row['accountcategory'],"Accounts");
            //公司属性
            $lists['customerproperty']=$row['customerproperty'];
            //客户类型
            $lists['customertype']=$row['customertype'];
            $lists['customertype_name']=vtranslate($row['customertype'],"Accounts");
            //业务类型
            $lists['servicetype_name']=$row['servicetype_name'];
            //创建时间
            $lists['createdtime']=$row['createdtime'];
            //修改时间
            $lists['modifiedtime']=$row['modifiedtime'];
            //邮箱
            $lists['email']=$row['email'];
            //备注
            $lists['description']=$row['description'];
            //签订日期
            $lists['signfor_date']=$row['signfor_date'];
            //未开票金额
            if(bccomp($row['total'],0)> 0){
                $lists['noinvoiceamount'] = bcsub($row['total'], $row['invoiceamount'], 2);
            }else{
                $lists['noinvoiceamount']='--';
            }

            //T云账号
            $lists['tyun_accounts']= getTyunAccounts($row['accountid']);

            //T云客户来源
            $lists['accountSource']= getTyunAccountSource($row['accountid']);

            $ret_lists[]=$lists;
        }

        //总件数取得
        _cs_logs(array("执行统计总件数sql：".$query_total));
        $result_total = $adb->pquery($query_total, array());
        $row_total = $adb->query_result_rowdata($result_total);
        $total = $row_total["cnt"];
        $total = empty($total)?0:$total;
        _cs_logs(array("获取客户总件数：".$total));

        $data=json_encode($ret_lists,JSON_UNESCAPED_UNICODE);
        $data=encrypt($data);
        echo json_encode(array("success"=>true,"is_multiple"=>true,"total"=>$total,"pageNum"=>$pageNum,"pageSize"=>$pageSize,"data"=>$data));
        _cs_logs(array("查询客户列表信息成功"));
    }else{
        _cs_logs(array("没有查询到相关信息"));
        /*_cs_logs(array("没有查询到相关信息"));
        $lists=array('success'=>false,'msg'=>'没有客户的相关信息',JSON_UNESCAPED_UNICODE);
        echo json_encode($lists);*/
        echo json_encode(array("success"=>true,"is_multiple"=>true,"total"=>0,"pageNum"=>$pageNum,"pageSize"=>$pageSize,"data"=>$ret_lists));
    }
    _cs_logs(array("查询客户列表信息接口结束"));
    exit;
}
//=================查询分配客户列表API================================================================
if(is_array($cs_method) && $cs_method[0]=='searchAllotAccountList'){
    _cs_logs(array("查询分配客户列表接口开始"));
    global $adb;
    $reusltdata = $_REQUEST["data"];
    _cs_logs(array("查询分配客户列表接口参数:".$resultdata));
    //$reusltdata = trim($reusltdata);
    //$reusltdata = str_replace(' ', '+', $reusltdata);
    //$decrdata = decrypt($reusltdata);
    //$pad = strrpos($decrdata, '}');
    //$decrdata = substr($decrdata, 0, $pad + 1);
    //$decodedata = json_decode($decrdata, true);
	$decodedata = json_decode($reusltdata, true);
    $search_field=$decodedata['searchField'];
    $search_value=$decodedata['searchValue'];
    $search_type=$decodedata['searchType'];
    $field_type=$decodedata['fieldType'];
    $accountids=$decodedata['accountIds'];
    $serviceid=$decodedata['serviceid'];
    $serviceids=$decodedata['serviceids'];
    $assign_serviceids=$decodedata['assign_serviceids'];

    $excludeAccountFlag=$decodedata['excludeAccountFlag'];
    $isAllotIroncard=$decodedata['isAllotIroncard'];
    $moduleName=$decodedata['moduleName'];
    $departmentids=$decodedata['departmentids'];
    $allotSmownerIds=$decodedata['allotSmownerIds'];
    $isChiefInspector=$decodedata['isChiefInspector'];
    $serviceStatus=$decodedata['serviceStatus'];
    $allotType=$decodedata['allotType'];
    $businessDepartmentId = $decodedata['businessDepartmentId'];
    $businessCenterId = $decodedata['businessCenterId'];
    $businessRegionId = $decodedata['businessRegionId'];
    $sourceType = $decodedata['bsourceType'];
    $accountrank = $decodedata['accountrank'];
    $serviceName = $decodedata['serviceName'];

    //去掉空格
    if(!empty($search_value)){
        $search_value = trim($search_value);
    }

	if(empty($sourceType)){
        $sourceType = 0;
    }
	
    $pageNum=empty($decodedata['pageNum'])?0:$decodedata['pageNum'];
    $pageSize=empty($decodedata['pageSize'])|| $decodedata['pageSize']=='0'?10:$decodedata['pageSize'];
    $p_num = 0;
    if($pageNum>0){
        $p_num = $pageNum - 1;
    }
    $pageNum = $p_num * $pageSize;
    _cs_logs(array("查询分配客户列表接口解析后参数:".$decrdata));
    _cs_logs(array("查询分配客户等级:".$isIroncard));
    include_once 'vtlib/Vtiger/Module.php';
    include_once 'includes/main/WebUI.php';
    include_once  'languages/zh_cn/Accounts.php';
    vglobal('default_language', $default_language);
    $currentLanguage = 'zh_cn';
    //Vtiger_Language_Handler::getLanguage();//2.语言设置
    vglobal('current_language',$currentLanguage);

    $query="SELECT TT.*,
			IFNULL((SELECT GROUP_CONCAT(DISTINCT productname) as productname FROM vtiger_activationcode WHERE `status` in(0,1) AND comeformtyun=1 AND customerid=TT.accountid),TT.producttype) as servicetype_name,
			IFNULL((select CONCAT(last_name,(if(`isdimission`=1,'[离职]',''))) as last_name from vtiger_users where TT.signid=vtiger_users.id),'') as smowner_name,
            IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=TT.signid LIMIT 1)),'--') as sales_departmentname,
			IFNULL((select CONCAT(last_name,(if(`isdimission`=1,'[离职]',''))) as last_name from vtiger_users where TT.smownerid=vtiger_users.id),'') as account_smowner_name,
            IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=TT.smownerid LIMIT 1)),'--') as account_sales_departmentname,
			IFNULL((SELECT date_format(MAX(signfor_date),'%Y-%m-%d') FROM vtiger_servicecontracts WHERE sc_related_to=TT.accountid),'--') AS signfor_date,
			(SELECT date_format(last_update_time,'%Y-%m-%d %H:%i:%S') FROM `vtiger_account_allot_other` WHERE accountid=TT.accountid AND source_type=1) allottime_other,
			date_format((SELECT MAX(addtime) FROM vtiger_modcomments A1 WHERE A1.related_to=TT.accountid),'%Y-%m-%d %H:%i:%S') AS last_mod_time
			FROM (";

    $query.="SELECT DISTINCT * FROM (";
    $query.="SELECT DISTINCT
			vtiger_account.accountid,
			vtiger_account.allottimestamp,
			vtiger_account.accountname,
			vtiger_account.accountrank,
			vtiger_account.producttype,
			vtiger_crmentity.smownerid,
			IFNULL(vtiger_account.serviceid,(SELECT csid FROM `vtiger_account_allot_other` WHERE accountid=vtiger_account.accountid)) serviceid,
			date_format(vtiger_account.allottime,'%Y-%m-%d %H:%i:%S') AS allottime, 
			IF(date_format(vtiger_account.allottime,'%Y-%m-%d') > ADDDATE(CURDATE(),INTERVAL -7 DAY),1,0) AS is_new_allot,
			vtiger_account.industry,
			vtiger_account.business,
			vtiger_account.accountcategory,
			vtiger_account.customertype,
			vtiger_account.customerproperty,
			vtiger_crmentity.createdtime,
			vtiger_crmentity.modifiedtime,
			vtiger_account.email1 AS email,
			(SELECT signid FROM vtiger_servicecontracts WHERE sc_related_to=C.sc_related_to AND signdate=C.signdate LIMIT 1) signid,
			D.departmentid,
			vtiger_crmentity.description
			FROM vtiger_account 
			JOIN vtiger_crmentity ON vtiger_account.accountid = vtiger_crmentity.crmid
			JOIN (SELECT DISTINCT sc_related_to,MAX(signdate) signdate FROM vtiger_servicecontracts WHERE modulestatus IN ( 'c_complete', 'c_history') GROUP BY sc_related_to) C ON(vtiger_account.accountid=C.sc_related_to)
			LEFT JOIN vtiger_user2department D ON(vtiger_crmentity.smownerid=D.userid)		
			WHERE vtiger_crmentity.deleted=0";
	if($allotType == '1') {
        //未分配客服的客户-查询负责客服为空，且没有回款不足的客户
		if($sourceType == 1){
			//$query .= " AND vtiger_account.accountid NOT IN(SELECT accountid FROM `vtiger_account_allot_other` WHERE source_type=1)
			//            AND (vtiger_account.accountid IN(SELECT DISTINCT customerid FROM vtiger_activationcode WHERE productclass=1 and `status` IN(0,1) AND customerid IS NOT NULL)
			//			      OR vtiger_account.accountid IN(SELECT DISTINCT sc_related_to FROM vtiger_servicecontracts WHERE parent_contracttypeid=4 AND modulestatus IN ( 'c_complete', 'c_history')))";
			$query .= " AND vtiger_account.accountid NOT IN(SELECT accountid FROM `vtiger_account_allot_other` WHERE source_type=1)
			            AND vtiger_account.accountid IN(SELECT DISTINCT customerid FROM vtiger_activationcode WHERE productclass=1 and `status` IN(0,1) AND customerid IS NOT NULL)";
		}else{
			$query .= " AND vtiger_account.serviceid IS NULL";
		}
    }else if($allotType == '2') {
		if($sourceType == 1){
			$query .= " AND vtiger_account.accountid IN(SELECT accountid FROM `vtiger_account_allot_other` WHERE source_type=1)";
		}else{
			$query .= " AND vtiger_account.serviceid IS NOT NULL";
		}
	}
	if($isAllotIroncard == '1' || $isAllotIroncard == 1){
		 //可分配全部客户
	}else{
		//不可分配铁牌客户
		$query .= " AND vtiger_account.accountrank NOT IN('chan_notv','iron_isv')";
	}
	 
    $query.=" UNION ALL ";
    //针对线上下单用户
    $query.="SELECT DISTINCT
			vtiger_account.accountid,
			vtiger_account.allottimestamp,
			vtiger_account.accountname,
			vtiger_account.accountrank,
			NULL AS producttype,
			vtiger_crmentity.smownerid,
			IFNULL(vtiger_account.serviceid,(SELECT csid FROM `vtiger_account_allot_other` WHERE accountid=vtiger_account.accountid)) serviceid,
			date_format(vtiger_account.allottime,'%Y-%m-%d %H:%i:%S') AS allottime, 
			IF(date_format(vtiger_account.allottime,'%Y-%m-%d') > ADDDATE(CURDATE(),INTERVAL -7 DAY),1,0) AS is_new_allot,
			vtiger_account.industry,
			vtiger_account.business,
			vtiger_account.accountcategory,
			vtiger_account.customertype,
			vtiger_account.customerproperty,
			vtiger_crmentity.createdtime,
			vtiger_crmentity.modifiedtime,
			vtiger_account.email1 AS email,
			0 AS signid,
			D.departmentid,
			vtiger_crmentity.description
			FROM vtiger_account 
			JOIN vtiger_crmentity ON vtiger_account.accountid = vtiger_crmentity.crmid 
			LEFT JOIN vtiger_user2department D ON(vtiger_crmentity.smownerid=D.userid)
			WHERE vtiger_crmentity.deleted=0
			AND vtiger_account.accountid NOT IN(SELECT sc_related_to FROM vtiger_servicecontracts)
			AND vtiger_account.accountid IN(SELECT CAST(customerid AS SIGNED) FROM vtiger_activationcode WHERE contractid=0 AND `status` in(0,1) AND comeformtyun=1 AND onoffline='line' AND customerid IS NOT NULL)";
	if($allotType == '1') {
        //未分配客服的客户-查询负责客服为空，且没有回款不足的客户
		if($sourceType == 1){
			$query .= " AND vtiger_account.accountid NOT IN(SELECT accountid FROM `vtiger_account_allot_other` WHERE source_type=1)
			            AND vtiger_account.accountid IN(SELECT DISTINCT customerid FROM vtiger_activationcode WHERE productclass=1 and `status` IN(0,1) AND customerid IS NOT NULL)";
		}else{
			$query .= " AND vtiger_account.serviceid IS NULL";
		}
    }else if($allotType == '2') {
		if($sourceType == 1){
			$query .= " AND vtiger_account.accountid IN(SELECT accountid FROM `vtiger_account_allot_other` WHERE source_type=1)";
		}else{
			$query .= " AND vtiger_account.serviceid IS NOT NULL";
		}
	}
	if($isAllotIroncard == '1' || $isAllotIroncard == 1){
		 //可分配全部客户
	}else{
		//不可分配铁牌客户
		$query .= " AND vtiger_account.accountrank NOT IN('chan_notv','iron_isv')";
	}			
    $query.=") MM WHERE 1=1 ";

    $query_total="SELECT count(DISTINCT accountid) AS cnt FROM (
			SELECT DISTINCT
			vtiger_account.accountid,
			vtiger_account.allottimestamp,
			vtiger_account.accountname,
			vtiger_account.accountrank,
			vtiger_account.producttype,
			vtiger_crmentity.smownerid,
			vtiger_account.serviceid,
			date_format(vtiger_account.allottime,'%Y-%m-%d %H:%i:%S') AS allottime, 
			IF(date_format(vtiger_account.allottime,'%Y-%m-%d') > ADDDATE(CURDATE(),INTERVAL -7 DAY),1,0) AS is_new_allot,
			vtiger_account.industry,
			vtiger_account.business,
			vtiger_account.accountcategory,
			vtiger_account.customertype,
			vtiger_account.customerproperty,
			vtiger_crmentity.createdtime,
			vtiger_crmentity.modifiedtime,
			vtiger_account.email1 AS email,
			0 signid,
			D.departmentid,
			vtiger_crmentity.description
			FROM vtiger_account 
			JOIN vtiger_crmentity ON vtiger_account.accountid = vtiger_crmentity.crmid
			JOIN (SELECT sc_related_to,MAX(signdate) signdate FROM vtiger_servicecontracts WHERE modulestatus IN ( 'c_complete', 'c_history') GROUP BY sc_related_to) C ON(vtiger_account.accountid=C.sc_related_to)
			LEFT JOIN vtiger_user2department D ON(vtiger_crmentity.smownerid=D.userid)	
			WHERE vtiger_crmentity.deleted=0";
	if($allotType == '1') {
        //未分配客服的客户-查询负责客服为空，且没有回款不足的客户
		if($sourceType == 1){
			//$query_total .= " AND vtiger_account.accountid NOT IN(SELECT accountid FROM `vtiger_account_allot_other` WHERE source_type=1)
			//            AND (vtiger_account.accountid IN(SELECT DISTINCT customerid FROM vtiger_activationcode WHERE productclass=1 and `status` IN(0,1) AND customerid IS NOT NULL)
			//			      OR vtiger_account.accountid IN(SELECT DISTINCT sc_related_to FROM vtiger_servicecontracts WHERE parent_contracttypeid=4 AND modulestatus IN ( 'c_complete', 'c_history')))";
			$query_total .= " AND vtiger_account.accountid NOT IN(SELECT accountid FROM `vtiger_account_allot_other` WHERE source_type=1)
			            AND vtiger_account.accountid IN(SELECT DISTINCT customerid FROM vtiger_activationcode WHERE productclass=1 and `status` IN(0,1) AND customerid IS NOT NULL)";
		}else{
			$query_total .= " AND vtiger_account.serviceid IS NULL";
		}
    }else if($allotType == '2') {
		if($sourceType == 1){
			$query_total .= " AND vtiger_account.accountid IN(SELECT accountid FROM `vtiger_account_allot_other` WHERE source_type=1)";
		}else{
			$query_total .= " AND vtiger_account.serviceid IS NOT NULL";
		}
	}
	if($isAllotIroncard == '1' || $isAllotIroncard == 1){
		 //可分配全部客户
	}else{
		//不可分配铁牌客户
		$query_total .= " AND vtiger_account.accountrank NOT IN('chan_notv','iron_isv')";
	}
	$query_total.=" UNION ALL ";
    //针对线上下单用户
    $query_total.="SELECT DISTINCT
			vtiger_account.accountid,
			vtiger_account.allottimestamp,
			vtiger_account.accountname,
			vtiger_account.accountrank,
			NULL AS producttype,
			vtiger_crmentity.smownerid,
			vtiger_account.serviceid,
			date_format(vtiger_account.allottime,'%Y-%m-%d %H:%i:%S') AS allottime, 
			IF(date_format(vtiger_account.allottime,'%Y-%m-%d') > ADDDATE(CURDATE(),INTERVAL -7 DAY),1,0) AS is_new_allot,
			vtiger_account.industry,
			vtiger_account.business,
			vtiger_account.accountcategory,
			vtiger_account.customertype,
			vtiger_account.customerproperty,
			vtiger_crmentity.createdtime,
			vtiger_crmentity.modifiedtime,
			vtiger_account.email1 AS email,
			0 AS signid,
			D.departmentid,
			vtiger_crmentity.description
			FROM vtiger_account 
			JOIN vtiger_crmentity ON vtiger_account.accountid = vtiger_crmentity.crmid 
			LEFT JOIN vtiger_user2department D ON(vtiger_crmentity.smownerid=D.userid)
			WHERE vtiger_crmentity.deleted=0
			AND vtiger_account.accountid NOT IN(SELECT sc_related_to FROM vtiger_servicecontracts)
			AND vtiger_account.accountid IN(SELECT CAST(customerid AS SIGNED) FROM vtiger_activationcode WHERE contractid=0 AND `status` in(0,1) AND comeformtyun=1 AND onoffline='line' AND customerid IS NOT NULL)";
	if($allotType == '1') {
        //未分配客服的客户-查询负责客服为空，且没有回款不足的客户
		if($sourceType == 1){
			$query_total .= " AND vtiger_account.accountid NOT IN(SELECT accountid FROM `vtiger_account_allot_other` WHERE source_type=1)
			            AND vtiger_account.accountid IN(SELECT DISTINCT customerid FROM vtiger_activationcode WHERE productclass=1 and `status` IN(0,1) AND customerid IS NOT NULL)";
		}else{
			$query_total .= " AND vtiger_account.serviceid IS NULL";
		}
    }else if($allotType == '2') {
		if($sourceType == 1){
			$query_total .= " AND vtiger_account.accountid IN(SELECT accountid FROM `vtiger_account_allot_other` WHERE source_type=1)";
		}else{
			$query_total .= " AND vtiger_account.serviceid IS NOT NULL";
		}
	}
	if($isAllotIroncard == '1' || $isAllotIroncard == 1){
		 //可分配全部客户
	}else{
		//不可分配铁牌客户
		$query_total .= " AND vtiger_account.accountrank NOT IN('chan_notv','iron_isv')";
	}
	$query_total .= ") MM WHERE 1=1 ";

    $query_where = "";
	//过滤掉黄玉琴和李季下单的客户 2020/02/17 黄玉琴微信提需求
	//$query_where = " AND MM.accountid NOT IN (SELECT CAST(customerid AS SIGNED) FROM vtiger_activationcode WHERE classtype IN('buy','renew','crenew') AND creator IN(1179,2824) and CAST(customerid AS SIGNED)>0)";
	
    //按商务查询
    if(!empty($businessDepartmentId) && $businessDepartmentId !='0'){
        $query_where.= " AND MM.departmentid='{$businessDepartmentId}'";
    }else {
        if(!empty($businessCenterId)&& $businessCenterId !='0'){
            $query_where.= " AND MM.departmentid IN(SELECT departmentid FROM vtiger_departments WHERE FIND_IN_SET('{$businessCenterId}',REPLACE(parentdepartment,'::',',')))";
        }else{
            if(!empty($businessRegionId)&& $businessRegionId !='0'){
                $query_where.= " AND MM.smownerid IN(SELECT id FROM vtiger_users WHERE companyid='{$businessRegionId}')";
            }
        }
    }

    //$query_where = "";
    //if($allotType == '1' || $allotType == 'true') {
        //未分配客服的客户-查询负责客服为空，且没有回款不足的客户
    //    $query_where .= " AND MM.serviceid IS NULL";
    //}
    if(!empty($accountids)){
        //if($excludeAccountFlag == '1'){
        //    $query_where.= " AND NOT EXISTS(select 1 from vtiger_account A WHERE FIND_IN_SET(A.accountid,'{$accountids}'))";
        //}else{
		$accountids = rtrim($accountids,',');
		$query_where.= " AND MM.accountid IN({$accountids})";
        //}
    }

    //客服在职状态
    if(!empty($serviceStatus)){
        if($serviceStatus == '1') {
            $query_where .= " AND EXISTS(select 1 from vtiger_users where MM.serviceid=vtiger_users.id and isdimission=0)";
        }
        if($serviceStatus == '2') {
            $query_where .= " AND EXISTS(select 1 from vtiger_users where MM.serviceid=vtiger_users.id and isdimission=1)";
        }
    }

    if(!empty($accountrank)){
		$query_where .= " AND MM.accountrank='".$accountrank."'";
	}
	if(!empty($serviceName)){
		$query_where .= " AND (EXISTS(select 1 from vtiger_users where MM.serviceid=vtiger_users.id and vtiger_users.last_name LIKE '%{$serviceName}%')
				  OR EXISTS(select 1 from vtiger_users where vtiger_users.id IN(SELECT csid FROM vtiger_account_allot_other WHERE accountid=MM.accountid) and vtiger_users.last_name LIKE '%{$serviceName}%'))";
	}
	
    if(!empty($search_field)) {
        if($search_field == 'custom_service') {
            //负责客服
            //$query_where .= " AND EXISTS(select 1 from vtiger_users where vtiger_account.serviceid=vtiger_users.id and vtiger_users.last_name='{$search_value}')";
            if(!empty($search_value)){
                $query_where .= " AND (EXISTS(select 1 from vtiger_users where MM.serviceid=vtiger_users.id and vtiger_users.last_name LIKE '%{$search_value}%')
				  OR EXISTS(select 1 from vtiger_users where vtiger_users.id IN(SELECT csid FROM vtiger_account_allot_other WHERE accountid=MM.accountid) and vtiger_users.last_name LIKE '%{$search_value}%'))";
            }
        }else if($search_field == 'custom_sales') {
            //首个签单负责商务
            //$query_where .= " AND EXISTS(select 1 from vtiger_users where vtiger_crmentity.smownerid=vtiger_users.id and vtiger_users.last_name='{$search_value}')";
            if(!empty($search_value)){
                $query_where .= " AND EXISTS(select 1 from vtiger_users where MM.signid=vtiger_users.id and vtiger_users.last_name LIKE '%{$search_value}%')";
            }
        }else if($search_field == 'departmentname') {
            //首个签单商务所属部门
            $query_where .= " AND MM.signid IN(SELECT A.userid FROM vtiger_user2department A
							LEFT JOIN vtiger_departments B ON(A.departmentid=B.departmentid)
							WHERE B.departmentname LIKE '%{$search_value}%')";
        }else if($search_field == 'group_name') {
            //按组名查询
            $serviceids = rtrim($serviceids,',');
            $query_where .= " AND MM.serviceid IN({$serviceids})";
        }else{
            if($field_type == 'date' && !empty($search_value)){
                //模糊查询
                $search_value = str_replace('/', '-', $search_value);
                $query_where .= " AND {$search_field} LIKE '%{$search_value}%'";
            }
            if($field_type == 'list'){
                //精确查询
                $query_where .= " AND {$search_field}='{$search_value}'";
            }
            if($field_type == 'text'){
                if ($search_type == '1') {
                    //模糊查询
                    if(!empty($search_value)){
                        $query_where .= " AND {$search_field} LIKE '%{$search_value}%'";
                    }
                } else {
                    //精确查询
                    $query_where .= " AND {$search_field}='{$search_value}'";
                }
            }
        }
    }

    if(!empty($serviceid)) {
        $query_where.= " AND MM.serviceid={$serviceid}";
    }


	//$all_userid = array();
	//客户对应的负责人
	_cs_logs(array("分配客户负责人：". implode(',',$allotSmownerIds)));
	//if(!empty($allotSmownerIds)) {
	//	$all_userid = explode(",",$smownerids);
	//}
	//通过商务部门查找商务
	//1. 中小体系的那几个部门，分公司加一二三四营加客服部，不区分合同类型；
	//2. 其他部门，判断合同类型属于T云的，黄玉琴才可以看
	//_cs_logs(array("部门字符：". $departmentids));
	//if(!empty($departmentids)) {
	//	$arr_departmentid = explode(";",$departmentids);
		//_cs_logs(array("部门：". implode(',',$arr_departmentid)));
	//	if(count($arr_departmentid)>0) {
	//		for ($i = 0; $i < count($arr_departmentid); $i++) {
				//_cs_logs(array("部门id为".$arr_departmentid[$i]));
	//			$arr_userid = getDepartmentUser($arr_departmentid[$i]);
				//_cs_logs(array("部门id为".$arr_departmentid[$i]."的商务：". implode(',',$arr_userid)));
	//			$all_userid = array_merge($all_userid,$arr_userid);
	//		}
	//	}
	//}
	//_cs_logs(array("部门对应的商务：". implode(',',$all_userid)));
	if(count($allotSmownerIds)>0){
		if($isChiefInspector == '1'){
			//$query_where.= " AND (vtiger_servicecontracts.parent_contracttypeid=2 OR vtiger_crmentity.smownerid IN (".implode(',',$all_userid)."))";
			//if(!empty($assign_serviceids)) {
			//	$query_where.= " AND (MM.parent_contracttypeid=2 OR MM.smownerid IN (".implode(',',$all_userid).") OR MM.serviceid IN({$assign_serviceids}))";
			//}else{
			//	$query_where.= " AND (MM.parent_contracttypeid=2 OR MM.smownerid IN (".implode(',',$all_userid)."))";
			//}
		}else{
			//查找所属客服的客户/查找已分配客户
			if(count($assign_serviceids)>0 && $allotType == '2') {
				//$query_where .= " AND (vtiger_crmentity.smownerid IN (".implode(',',$all_userid).") OR vtiger_account.serviceid IN({$assign_serviceids}))";
				if($sourceType == 1){
					$query_where .= " AND (MM.smownerid IN (".implode(',',$allotSmownerIds).") OR MM.accountid IN(SELECT accountid FROM `vtiger_account_allot_other` WHERE source_type=1 AND csid IN(".implode(',',$assign_serviceids).")))";
			    }else{
					$query_where .= " AND (MM.smownerid IN (".implode(',',$allotSmownerIds).") OR MM.serviceid IN(".implode(',',$assign_serviceids)."))";
				}
			}else{
				//$query_where.= " AND vtiger_crmentity.smownerid IN (".implode(',',$all_userid).")";
				$query_where.= " AND MM.smownerid IN (".implode(',',$allotSmownerIds).")";
			}
		}
	}
	
    if($moduleName == 'AssignAccount'){
        $query.= $query_where." LIMIT {$pageNum},{$pageSize}";
    }else{
        $query.= $query_where." ORDER BY MM.allottimestamp DESC,MM.accountid DESC LIMIT {$pageNum},{$pageSize}";
    }

    $query_total.=$query_where;

    $query.=") TT";

    _cs_logs(array("分配客户执行查询sql：".$query));
    $result = $adb->pquery($query, array());
    $rows = $adb->num_rows($result);
    $ret_lists = array();
    if ($rows>0) {
        while($row=$adb->fetchByAssoc($result)) {
            $lists = array();
            //客户ID
            $lists['accountid']=$row['accountid'];
            //客户名称
            $lists['accountname']=$row['accountname'];
            //客户等级
            $lists['accountrank']=$row['accountrank'];
            $lists['accountrank_name']=vtranslate($row['accountrank'],"Accounts");
            //负责商务
            $lists['smownerid']=$row['smownerid'];
            $lists['account_smowner_name']=$row['account_smowner_name'];
            $lists['account_sales_departmentname']=$row['account_sales_departmentname'];

            //首个签单负责商务
            $lists['smowner_name']=$row['smowner_name'];
            //首个签单负责商务部门
            $lists['sales_departmentname']=$row['sales_departmentname'];
            //是否离职
            //$lists['isquit'] = $row['isquit'];
            //负责客服
            //$lists['serviceid']=$row['serviceid'];
            //$lists['service_name']=$row['service_name'];
            //分配时间
			if($sourceType == 1){
				$lists['allottime']=$row['allottime_other'];
			}else{
				$lists['allottime']=$row['allottime'];
			}
            
            //是否新分配
            $lists['is_new_allot']=$row['is_new_allot'];
            //最后跟进时间
            $lists['last_mod_time']=$row['last_mod_time'];
            //行业
            $lists['industry']=vtranslate($row['industry'],"Accounts");
            //主营业务
            $lists['business']=$row['business'];
            //保护模式
            $lists['accountcategory']=vtranslate($row['accountcategory'],"Accounts");
            //公司属性
            $lists['customerproperty']=$row['customerproperty'];
            //客户类型
            $lists['customertype']=$row['customertype'];
            $lists['customertype_name']=vtranslate($row['customertype'],"Accounts");
            //业务类型
            $lists['servicetype_name']=$row['servicetype_name'];
            //创建时间
            $lists['createdtime']=$row['createdtime'];
            //修改时间
            $lists['modifiedtime']=$row['modifiedtime'];
            //邮箱
            $lists['email']=$row['email'];
            //备注
            $lists['description']=$row['description'];
            //签订日期
            $lists['signfor_date']=$row['signfor_date'];

            //T云账号
            $lists['tyun_accounts']= getTyunAccounts($row['accountid']);

            //T云客户来源
            //$lists['accountSource']= getTyunAccountSource($row['accountid']);


            $ret_lists[]=$lists;
        }

        //总件数取得
        _cs_logs(array("执行分配客户统计总件数sql：".$query_total));
        $result_total = $adb->pquery($query_total, array());
        $row_total = $adb->query_result_rowdata($result_total);
        $total = $row_total["cnt"];
        $total = empty($total)?0:$total;
        _cs_logs(array("获取分配客户总件数：".$total));

        $data=json_encode($ret_lists,JSON_UNESCAPED_UNICODE);
        $data=encrypt($data);
        echo json_encode(array("success"=>true,"is_multiple"=>true,"total"=>$total,"pageNum"=>$pageNum,"pageSize"=>$pageSize,"data"=>$data));
        _cs_logs(array("查询客户列表信息成功"));
    }else{
        _cs_logs(array("没有查询到相关信息"));
        /*_cs_logs(array("没有查询到相关信息"));
        $lists=array('success'=>false,'msg'=>'没有客户的相关信息',JSON_UNESCAPED_UNICODE);
        echo json_encode($lists);*/
        echo json_encode(array("success"=>true,"is_multiple"=>true,"total"=>0,"pageNum"=>$pageNum,"pageSize"=>$pageSize,"data"=>$ret_lists));
    }
    _cs_logs(array("查询分配客户列表信息接口结束"));
    exit;
}
//=================查询全部待分配客户API================================================================
if(is_array($cs_method) && $cs_method[0]=='searchWaitAllotAccount'){
    _cs_logs(array("查询待分配客户接口开始"));
    global $adb;

    $query_sql = "SELECT DISTINCT A.accountid,A.accountname,D.departmentid,B.smownerid,IFNULL(C.productid,0)AS productid,C.buyseparately,C.comeformtyun
				FROM vtiger_account A
				INNER JOIN vtiger_crmentity B ON(A.accountid=B.crmid)
				LEFT JOIN vtiger_user2department D ON(B.smownerid=D.userid)
				INNER JOIN (SELECT customerid,productid,buyseparately,comeformtyun FROM vtiger_activationcode WHERE `status`=1 AND classtype='buy') C ON(A.accountid=cast(customerid as SIGNED))
				WHERE B.deleted=0";
    //黄玉琴：本月(2019/12)能分配机会和铁牌客户,下个月就不需要了
	// 2020/03/11~2020/04/01 放开
    //$query_sql.=" AND A.accountrank NOT IN('chan_notv','iron_isv')";

    $result = $adb->pquery($query_sql, array());
    $rows = $adb->num_rows($result);
    $lists = array();
    $ret_lists = array();
    if ($rows > 0) {
        while($row=$adb->fetchByAssoc($result)) {
            $lists['accountid'] = $row['accountid'];
            $lists['accountname'] = $row['accountname'];
            $lists['productid'] = $row['productid'];
            $lists['buyseparately'] = $row['buyseparately'];
            $lists['comeformtyun'] = $row['comeformtyun'];
            $lists['departmentid'] = $row['departmentid'];
            $lists['smownerid'] = $row['smownerid'];
            $ret_lists[]=$lists;
        }

        $data = json_encode($ret_lists, JSON_UNESCAPED_UNICODE);
        $data = encrypt($data);
        echo json_encode(array("success" => true,"is_multiple"=>true, "data" => $data));
        _cs_logs(array("获取待分配客户列表成功"));
    } else {
        echo json_encode(array("success" => true, "data" => null));
    }
    _cs_logs(array("查询待分配客户接口结束"));
    exit;
}
//=================查询待分配客户数量API================================================================
if(is_array($cs_method) && $cs_method[0]=='searchWaitAllotAccountCount'){
    _cs_logs(array("查询待分配客户数量接口开始"));
    global $adb;
    $reusltdata = $_REQUEST["data"];
    _cs_logs(array("查询待分配客户数量接口参数:".$resultdata));
    $reusltdata = trim($reusltdata);
    $reusltdata = str_replace(' ', '+', $reusltdata);
    $decrdata = decrypt($reusltdata);
    $pad = strrpos($decrdata, '}');
    $decrdata = substr($decrdata, 0, $pad + 1);
    $decodedata = json_decode($decrdata, true);
    $accountids=$decodedata['accountIds'];
    $serviceid=$decodedata['serviceid'];
    $serviceids=$decodedata['serviceids'];
    $assign_serviceids=$decodedata['assign_serviceids'];

    $departmentids=$decodedata['departmentids'];
    $smownerids=$decodedata['smownerids'];
    $isChiefInspector=$decodedata['isChiefInspector'];

    $businessDepartmentId = $decodedata['businessDepartmentId'];
    $businessCenterId = $decodedata['businessCenterId'];
    $businessRegionId = $decodedata['businessRegionId'];
    $isAllotIroncard=$decodedata['isAllotIroncard'];
    $sourceType=$decodedata['bsourceType'];
	
    _cs_logs(array("查询待分配客户数量接口解析后参数:".$decrdata));

$query_total="SELECT count(DISTINCT accountid) AS cnt FROM (
			SELECT DISTINCT
			vtiger_account.accountid,
			vtiger_account.allottimestamp,
			vtiger_account.accountname,
			vtiger_account.accountrank,
			vtiger_account.producttype,
			vtiger_crmentity.smownerid,
			vtiger_account.serviceid,
			date_format(vtiger_account.allottime,'%Y-%m-%d %H:%i:%S') AS allottime, 
			IF(date_format(vtiger_account.allottime,'%Y-%m-%d') > ADDDATE(CURDATE(),INTERVAL -7 DAY),1,0) AS is_new_allot,
			vtiger_account.industry,
			vtiger_account.business,
			vtiger_account.accountcategory,
			vtiger_account.customertype,
			vtiger_account.customerproperty,
			vtiger_crmentity.createdtime,
			vtiger_crmentity.modifiedtime,
			vtiger_account.email1 AS email,
			C.signid,
			D.departmentid,
			vtiger_crmentity.description
			FROM vtiger_account 
			JOIN vtiger_crmentity ON vtiger_account.accountid = vtiger_crmentity.crmid
			JOIN vtiger_servicecontracts C ON(vtiger_account.accountid=C.sc_related_to)
			LEFT JOIN vtiger_user2department D ON(vtiger_crmentity.smownerid=D.userid)		
			WHERE vtiger_crmentity.deleted=0 AND modulestatus IN ( 'c_complete', 'c_history') AND vtiger_account.serviceid IS NULL";
	if($sourceType == 1){
		//$query_total .= " AND B.accountid NOT IN(SELECT accountid FROM `vtiger_account_allot_other` WHERE source_type=1)
		//			AND (B.accountid IN(SELECT DISTINCT customerid FROM vtiger_activationcode WHERE productclass=1 and `status` IN(0,1) AND customerid IS NOT NULL)
		//				  OR B.accountid IN(SELECT DISTINCT sc_related_to FROM vtiger_servicecontracts WHERE parent_contracttypeid=4 AND modulestatus IN ( 'c_complete', 'c_history')))";
		$query_total .= " AND vtiger_account.accountid NOT IN(SELECT accountid FROM `vtiger_account_allot_other` WHERE source_type=1)
					AND vtiger_account.accountid IN(SELECT DISTINCT customerid FROM vtiger_activationcode WHERE productclass=1 and `status` IN(0,1) AND customerid IS NOT NULL)";
	}else{
		$query_total .= " AND vtiger_account.serviceid IS NULL";
	}		
	if($isAllotIroncard == '1' || $isAllotIroncard == 1){
		 //可分配全部客户
	}else{
		//不可分配铁牌客户
		$query_total .= " AND vtiger_account.accountrank NOT IN('chan_notv','iron_isv')";
	}
	$query_total.=" UNION ALL ";
    //针对线上下单用户
    $query_total.="SELECT DISTINCT
			vtiger_account.accountid,
			vtiger_account.allottimestamp,
			vtiger_account.accountname,
			vtiger_account.accountrank,
			NULL AS producttype,
			vtiger_crmentity.smownerid,
			vtiger_account.serviceid,
			date_format(vtiger_account.allottime,'%Y-%m-%d %H:%i:%S') AS allottime, 
			IF(date_format(vtiger_account.allottime,'%Y-%m-%d') > ADDDATE(CURDATE(),INTERVAL -7 DAY),1,0) AS is_new_allot,
			vtiger_account.industry,
			vtiger_account.business,
			vtiger_account.accountcategory,
			vtiger_account.customertype,
			vtiger_account.customerproperty,
			vtiger_crmentity.createdtime,
			vtiger_crmentity.modifiedtime,
			vtiger_account.email1 AS email,
			0 AS signid,
			D.departmentid,
			vtiger_crmentity.description
			FROM vtiger_account 
			JOIN vtiger_crmentity ON vtiger_account.accountid = vtiger_crmentity.crmid 
			LEFT JOIN vtiger_user2department D ON(vtiger_crmentity.smownerid=D.userid)
			WHERE vtiger_crmentity.deleted=0 AND vtiger_account.serviceid IS NULL
			AND vtiger_account.accountid IN(SELECT CAST(customerid AS SIGNED) FROM vtiger_activationcode WHERE contractid=0 AND `status` in(0,1) AND comeformtyun=1 AND onoffline='line' AND customerid IS NOT NULL)";
	if($sourceType == 1){
		$query_total .= " AND vtiger_account.accountid NOT IN(SELECT accountid FROM `vtiger_account_allot_other` WHERE source_type=1)
					AND vtiger_account.accountid IN(SELECT DISTINCT customerid FROM vtiger_activationcode WHERE productclass=1 and `status` IN(0,1) AND customerid IS NOT NULL))";
	}else{
		$query_total .= " AND vtiger_account.serviceid IS NULL";
	}
	if($isAllotIroncard == '1' || $isAllotIroncard == 1){
		 //可分配全部客户
	}else{
		//不可分配铁牌客户
		$query_total .= " AND vtiger_account.accountrank NOT IN('chan_notv','iron_isv')";
	}
	$query_total .= ") MM WHERE 1=1 ";

    $query_where = " AND MM.serviceid IS NULL";
    if(!empty($accountids)){
        $accountids = rtrim($accountids,',');
        $query_where.= " AND MM.accountid IN({$accountids})";
    }
    //黄玉琴：本月(2019/12)能分配机会和铁牌客户,下个月就不需要了
	// 2020/03/11~2020/04/01 放开
    //$query_where.=" AND MM.accountrank NOT IN('chan_notv','iron_isv')";

	//过滤掉黄玉琴和李季下单的客户 2020/02/17 黄玉琴微信提需求
	//$query_where = " AND MM.accountid NOT IN (SELECT CAST(customerid AS SIGNED) FROM vtiger_activationcode WHERE classtype IN('buy','renew','crenew') AND creator IN(1179,2824) and CAST(customerid AS SIGNED)>0)";

    //按商务查询
    if(!empty($businessDepartmentId)&& $businessDepartmentId !='0'){
        $query_where.= " AND MM.departmentid='{$businessDepartmentId}'";
    }else {
        if(!empty($businessCenterId)&& $businessCenterId !='0'){
            $query_where.= " AND MM.departmentid IN(SELECT departmentid FROM vtiger_departments WHERE FIND_IN_SET('{$businessCenterId}',REPLACE(parentdepartment,'::',',')))";
        }else{
            if(!empty($businessRegionId)&& $businessRegionId !='0'){
                $query_where.= " AND MM.smownerid IN(SELECT id FROM vtiger_users WHERE companyid='{$businessRegionId}')";
            }
        }
    }

    $all_userid = array();
    //客户对应的负责人
    // _cs_logs(array("分配客户负责人：". $smownerids));
    if(!empty($smownerids)) {
        $all_userid = explode(",",$smownerids);
    }
    //通过商务部门查找商务
    //1. 中小体系的那几个部门，分公司加一二三四营加客服部，不区分合同类型；
    //2. 其他部门，判断合同类型属于T云的，黄玉琴才可以看
    //_cs_logs(array("部门字符：". $departmentids));
    if(!empty($departmentids)) {
        $arr_departmentid = explode(";",$departmentids);
        // _cs_logs(array("部门：". implode(',',$arr_departmentid)));
        if(count($arr_departmentid)>0) {
            for ($i = 0; $i < count($arr_departmentid); $i++) {
                //_cs_logs(array("部门id为".$arr_departmentid[$i]));
                $arr_userid = getDepartmentUser($arr_departmentid[$i]);
                //_cs_logs(array("部门id为".$arr_departmentid[$i]."的商务：". implode(',',$arr_userid)));
                $all_userid = array_merge($all_userid,$arr_userid);
            }
        }
    }
    //_cs_logs(array("部门对应的商务：". implode(',',$all_userid)));
    if(count($all_userid)>0){
        if($isChiefInspector == '1'){
        }else{
            //查找所属客服的客户
            if(!empty($assign_serviceids)) {
                $assign_serviceids = rtrim($assign_serviceids,',');
                //$query_where .= " AND (vtiger_crmentity.smownerid IN (".implode(',',$all_userid).") OR vtiger_account.serviceid IN({$assign_serviceids}))";
                $query_where .= " AND (MM.smownerid IN (".implode(',',$all_userid).") OR MM.serviceid IN({$assign_serviceids}))";
            }else{
                //$query_where.= " AND vtiger_crmentity.smownerid IN (".implode(',',$all_userid).")";
                $query_where.= " AND MM.smownerid IN (".implode(',',$all_userid).")";
            }
        }
    }

    $query_total.=$query_where;
    _cs_logs(array("查询待分配客户数量执行查询sql：".$query_total));

    //总件数取得
    $result_total = $adb->pquery($query_total, array());
    $row_total = $adb->query_result_rowdata($result_total);
    $total = $row_total["cnt"];
    $total = empty($total)?0:$total;
    _cs_logs(array("获取待分配客户数量：".$total));

    $ret_lists = array();
    $data=json_encode($ret_lists,JSON_UNESCAPED_UNICODE);
    $data=encrypt($data);
    echo json_encode(array("success"=>true,"total"=>$total,"data"=>$data));
    _cs_logs(array("查询待分配客户数量成功"));

    _cs_logs(array("查询待分配客户数量信息接口结束"));
    exit;
}
//=================查询3.0建站和小程序到期客户数量API================================================================
if(is_array($cs_method) && $cs_method[0]=='searchTyunWebSiteAccountCount'){
    _cs_logs(array("查询3.0建站和小程序到期客户数量接口开始"));
    global $adb;
    $reusltdata = $_REQUEST["data"];
    _cs_logs(array("查询3.0建站和小程序到期客户数量接口参数:".$resultdata));
    $reusltdata = trim($reusltdata);
    $reusltdata = str_replace(' ', '+', $reusltdata);
    $decrdata = decrypt($reusltdata);
    $pad = strrpos($decrdata, '}');
    $decrdata = substr($decrdata, 0, $pad + 1);
    $decodedata = json_decode($decrdata, true);
    $accountIds=$decodedata['accountIds'];
    $isChiefInspector=$decodedata['isChiefInspector'];
    _cs_logs(array("查询3.0建站和小程序到期客户数量接口解析后参数:".$decrdata));

    $query="SELECT contractcode,serviceinfo,loginname FROM vtiger_tyunstationsale WHERE classtype='buy' AND `status` IN(0,1) AND loginname IS NOT NULL AND loginname !=''";
    $query_where = " ";
    if(!empty($accountIds)){
        $accountIds = rtrim($accountIds,',');
        $query_where.= " AND accountid IN({$accountIds})";
    }
    $query.=$query_where;
    //_cs_logs(array("查询3.0建站和小程序到期客户T云账号SQL：".$query));

    $result = $adb->pquery($query, array());
    $rows = $adb->num_rows($result);
    $arr_loginname = array();
    $arr_buy_year = array();
    if ($rows>0) {
        while($row=$adb->fetchByAssoc($result)) {
            $arr_loginname[] = $row['loginname'];
            //处理服务详细
            $serviceinfo = htmlspecialchars_decode($row['serviceinfo']);
            $arr_serviceinfo = json_decode($serviceinfo,true);
            //_cs_logs(array("查询3.0建站和小程序到期客户服务信息：".json_encode($arr_serviceinfo)));
            for($a=0;$a<count($arr_serviceinfo);$a++){
                $count = $arr_serviceinfo[$a]['count'];
                $servicetype = $arr_serviceinfo[$a]['servicetype'];
                $year = $arr_serviceinfo[$a]['year'];
                //_cs_logs(array("购买年限：".$year));
                if($count > 0){
                    $arr_buy_year[$row['loginname'].'_'.$servicetype] = $year;
                }
            }
        }
    }

    //_cs_logs(array("查询3.0建站和小程序到期客户T云账号：".json_encode($arr_loginname)));
    //_cs_logs(array("查询3.0建站和小程序到期客户购买年限：".json_encode($arr_buy_year)));

    //总件数
    $total = 0;
    if(count($arr_loginname) > 0){
        $query_detail = "SELECT distinct contractcode,loginname,servicetype,MAX(execdate) execdate FROM vtiger_tyunstationsale_detail
                        WHERE FIND_IN_SET(loginname,'".implode(',',$arr_loginname)."') GROUP BY contractcode,loginname,servicetype";
        //_cs_logs(array("查询3.0建站和小程序到期客户详细SQL：".$query_detail));
        $result_detail = $adb->pquery($query_detail, array());
        $rows = $adb->num_rows($result_detail);
        if($rows > 0){
            while($row=$adb->fetchByAssoc($result_detail)) {
                $buy_year = $arr_buy_year[$row['loginname'].'_'.$row['servicetype']];
                $opendate = $row["execdate"];
                //_cs_logs(array("查询3.0建站和小程序到期客户[".$row['loginname']."]开通年月：".$opendate));
                $expirationDate = date('Y-m',strtotime("$opendate +$buy_year year"));
                $curYearMonth = date("Y-m");
                //_cs_logs(array("查询3.0建站和小程序到期客户[".$row['loginname']."]到期年月：".$expirationDate.",系统年月:".$curYearMonth));
                if($expirationDate == $curYearMonth){
                    $total ++ ;
                }
            }
        }
    }
    _cs_logs(array("查询3.0建站和小程序到期客户数量：".$total));

    $ret_lists = array();
    $data=json_encode($ret_lists,JSON_UNESCAPED_UNICODE);
    $data=encrypt($data);
    echo json_encode(array("success"=>true,"total"=>$total,"data"=>$data));
    _cs_logs(array("查询3.0建站和小程序到期客户数量成功"));

    _cs_logs(array("查询3.0建站和小程序到期客户数量接口结束"));
    exit;
}
//=================查询3.0建站和小程序到期客户列表API================================================================
if(is_array($cs_method) && $cs_method[0]=='searchTyunWebSiteAccountList'){
    _cs_logs(array("查询3.0建站和小程序到期客户数量接口开始"));
    global $adb;
    $reusltdata = $_REQUEST["data"];
    _cs_logs(array("查询3.0建站和小程序到期账户列表接口参数:".$resultdata));
    $reusltdata = trim($reusltdata);
    $reusltdata = str_replace(' ', '+', $reusltdata);
    $decrdata = decrypt($reusltdata);
    $pad = strrpos($decrdata, '}');
    $decrdata = substr($decrdata, 0, $pad + 1);
    $decodedata = json_decode($decrdata, true);
    $accountId=$decodedata['accountId'];
    $isChiefInspector=$decodedata['isChiefInspector'];
    _cs_logs(array("查询3.0建站和小程序到期T云账号列表接口解析后参数:".$decrdata));

    $query="SELECT contractcode,serviceinfo,loginname FROM vtiger_tyunstationsale WHERE classtype='buy' AND `status` IN(0,1) AND loginname IS NOT NULL AND loginname !='' AND accountid={$accountId}";
    //_cs_logs(array("查询3.0建站和小程序到期客户T云账号SQL：".$query));

    $result = $adb->pquery($query, array());
    $rows = $adb->num_rows($result);
    $arr_loginname = array();
    $arr_buy_year = array();
    if ($rows>0) {
        while($row=$adb->fetchByAssoc($result)) {
            $arr_loginname[] = $row['loginname'];
            //处理服务详细
            $serviceinfo = htmlspecialchars_decode($row['serviceinfo']);
            $arr_serviceinfo = json_decode($serviceinfo,true);
            //_cs_logs(array("查询3.0建站和小程序到期T云账号列表服务信息：".json_encode($arr_serviceinfo)));
            for($a=0;$a<count($arr_serviceinfo);$a++){
                $count = $arr_serviceinfo[$a]['count'];
                //1微信小程序标准建站 2 PC标准建站 3 移动移动标准建站 5,百度小程序标准建站 6,T云建站独立IP
                $servicetype = $arr_serviceinfo[$a]['servicetype'];
                $year = $arr_serviceinfo[$a]['year'];
                //_cs_logs(array("购买年限：".$year));
                if($count > 0){
                    $arr_buy_year[$row['loginname'].'_'.$servicetype] = $year;
                }
            }
        }
    }

    //_cs_logs(array("查询3.0建站和小程序到期T云账号列表：".json_encode($arr_loginname)));
    //_cs_logs(array("查询3.0建站和小程序到期T云账号购买年限：".json_encode($arr_buy_year)));

    $ret_lists = array();
    if(count($arr_loginname) > 0){
        $query_detail = "SELECT distinct contractcode,loginname,servicetype,
						DATE_FORMAT(MAX(execdate),'%Y-%m-%d %H:%i:%S') execdate
						FROM vtiger_tyunstationsale_detail
                        WHERE FIND_IN_SET(loginname,'".implode(',',$arr_loginname)."') GROUP BY contractcode,loginname,servicetype";
        //_cs_logs(array("查询3.0建站和小程序到期T云账号详细SQL：".$query_detail));
        $result_detail = $adb->pquery($query_detail, array());
        $rows = $adb->num_rows($result_detail);
        if($rows > 0){
            while($row=$adb->fetchByAssoc($result_detail)) {
                //1微信小程序标准建站 2 PC标准建站 3 移动移动标准建站 5,百度小程序标准建站 6,T云建站独立IP
                $servicetype = $row['servicetype'];

                $buy_year = $arr_buy_year[$row['loginname'].'_'.$servicetype];
                $opendate = $row["execdate"];
                //_cs_logs(array("查询3.0建站和小程序到期T云账号[".$row['loginname']."]开通年月：".$opendate));

                $lists = array();
                $lists["loginName"] = $row['loginname'];
                $lists["serviceName"] = "";
                if($servicetype == 1 || $servicetype == '1'){
                    $lists["serviceName"] = "微信小程序标准建站";
                }
                if($servicetype == 2 || $servicetype == '2'){
                    $lists["serviceName"] = "PC标准建站";
                }
                if($servicetype == 3 || $servicetype == '3'){
                    $lists["serviceName"] = "移动移动标准建站";
                }
                if($servicetype == 5 || $servicetype == '5'){
                    $lists["serviceName"] = "百度小程序标准建站";
                }
                if($servicetype == 6 || $servicetype == '6'){
                    $lists["serviceName"] = "T云建站独立IP";
                }
                $lists["buyYear"] = $buy_year;
                $lists["openDate"] = $row["execdate"];
                $lists["expirationDate"] = date('Y-m-d H:i:s',strtotime("$opendate +$buy_year year"));
                $lists["isSurplusDay"] = 0;
                if(strtotime($lists["expirationDate"]) < strtotime (date("y-m-d h:i:s"))){
                    $lists["isSurplusDay"] = 1;
                }

                //_cs_logs(array("查询3.0建站和小程序到期T云账号列表：".json_encode($lists)));
                $ret_lists[] = $lists;

            }
        }
    }
    if(count($ret_lists)>0){
        _cs_logs(array("查询3.0建站和小程序到期T云账号列表：".json_encode($ret_lists)));
        $data=json_encode($ret_lists,JSON_UNESCAPED_UNICODE);
        $data=encrypt($data);
        echo json_encode(array("success"=>true,"data"=>$data));
    }else{
        echo json_encode(array("success"=>true,"data"=>null));
    }

    _cs_logs(array("查询3.0建站和小程序到期T云账号列表成功"));

    // _cs_logs(array("查询3.0建站和小程序到期T云账号列表接口结束"));
    exit;
}
//=================查询客户信息列表===============================================================
if(is_array($cs_method) && $cs_method[0]=='searchAccountInfoList'){
    _cs_logs(array("查询客户列表接口开始"));
    global $adb;
    $reusltdata = $_REQUEST["data"];
    _cs_logs(array("查询客户列表接口参数:".$resultdata));
    $reusltdata = trim($reusltdata);
    $reusltdata = str_replace(' ', '+', $reusltdata);
    $decrdata = decrypt($reusltdata);
    $pad = strrpos($decrdata, '}');
    $decrdata = substr($decrdata, 0, $pad + 1);
    $decodedata = json_decode($decrdata, true);
    $search_field=$decodedata['search_field'];
    $search_value=$decodedata['search_value'];
    $search_type=$decodedata['search_type'];
    $field_type=$decodedata['field_type'];
    $isIroncard=$decodedata['isIroncard'];
    $userIds=$decodedata['userIds'];
    $accountids=$decodedata['accountIds'];
    $notJoinCourseTyunNames=$decodedata['notJoinCourseTyunNames'];

    //去掉空格
    if(!empty($search_value)){
        $search_value = trim($search_value);
    }

    $pageNum=empty($decodedata['pageNum'])?0:$decodedata['pageNum'];
    $pageSize=empty($decodedata['pageSize'])|| $decodedata['pageSize']=='0'?10:$decodedata['pageSize'];
    $p_num = 0;
    if($pageNum>0){
        $p_num = $pageNum - 1;
    }
    $pageNum = $p_num * $pageSize;
    _cs_logs(array("查询客户列表接口解析后参数:".$decrdata));

    $query="SELECT DISTINCT
			vtiger_account.accountid,
			vtiger_account.accountname,
			vtiger_account.email1 AS email,
			vtiger_account.mobile,
			IFNULL(vtiger_activationcode.comeformtyun,0) AS accountSource,
			FROM vtiger_account 
			JOIN vtiger_crmentity ON vtiger_account.accountid = vtiger_crmentity.crmid 
			LEFT JOIN vtiger_servicecontracts ON (vtiger_account.accountid=vtiger_servicecontracts.sc_related_to 
			                                        AND vtiger_servicecontracts.modulestatus IN ('c_complete', 'c_cancel','c_history') 
			                                        AND vtiger_servicecontracts.signfor_date IS NOT NULL )
			LEFT JOIN vtiger_activationcode ON (vtiger_activationcode.contractid=CONCAT(vtiger_servicecontracts.servicecontractsid))
			WHERE vtiger_crmentity.deleted=0  ";

    //$query_where = " AND vtiger_servicecontracts.modulestatus IN ('c_complete', 'c_cancel','c_history')
    //		AND vtiger_servicecontracts.signfor_date IS NOT NULL ";
    $query_where = "";

    if($isIroncard == '1' || $isIroncard == 1){
        $query_where.=" AND vtiger_account.accountrank='iron_isv'";
    }else{
        //黄玉琴：本月(2019/12)能分配机会和铁牌客户,下个月就不需要了
		// 2020/03/11~2020/04/01 放开
        //$query_where.=" AND vtiger_account.accountrank NOT IN('chan_notv','iron_isv')";
    }

    if(!empty($userIds)){
        $userIds = rtrim($userIds,',');
        $query_where .= " AND vtiger_account.serviceid IN({$userIds})";
    }

    if(!empty($accountids)){
        $accountids = rtrim($accountids,',');
        $query_where.= " AND vtiger_account.accountid IN({$accountids})";
    }

    if(!empty($search_field)) {
        if($search_field == 'custom_service') {
            //负责客服
            //$query_where .= " AND EXISTS(select 1 from vtiger_users where vtiger_account.serviceid=vtiger_users.id and vtiger_users.last_name='{$search_value}')";
            if(!empty($search_value)){
                $query_where .= " AND EXISTS(select 1 from vtiger_users where vtiger_account.serviceid=vtiger_users.id and vtiger_users.last_name LIKE '%{$search_value}%')";
            }
        }else if($search_field == 'custom_sales') {
            //负责商务
            //$query_where .= " AND EXISTS(select 1 from vtiger_users where vtiger_crmentity.smownerid=vtiger_users.id and vtiger_users.last_name='{$search_value}')";
            if(!empty($search_value)){
                $query_where .= " AND EXISTS(select 1 from vtiger_users where vtiger_crmentity.smownerid=vtiger_users.id and vtiger_users.last_name LIKE '%{$search_value}%')";
            }
        }else if($search_field == 'buy_type'){
            $query_where .= " AND vtiger_activationcode.classtype='{$search_value}'";
        }else if($search_field == 'product_type'){
            $query_where .= " AND vtiger_activationcode.productid IN(SELECT tyunproductid FROM vtiger_products WHERE productname LIKE '%{$search_value}%')";
        }else{
            if($field_type == 'date' && !empty($search_value)){
                //模糊查询
                $search_value = str_replace('/', '-', $search_value);
                $query_where .= " AND {$search_field} LIKE '%{$search_value}%'";
            }
            if($field_type == 'list'){
                //精确查询
                $query_where .= " AND {$search_field}='{$search_value}'";
            }
            if($field_type == 'text'){
                if ($search_type == '1' || $search_type == 1) {
                    //模糊查询
                    if(!empty($search_value)){
                        $query_where .= " AND {$search_field} LIKE '%{$search_value}%'";
                    }
                } else {
                    //精确查询
                    $query_where .= " AND {$search_field}='{$search_value}'";
                }
            }
        }
    }

    //未参加培训邀约的客户
    if(!empty($notJoinCourseTyunNames)){
        $query_where.= " AND FIND_IN_SET(vtiger_activationcode.usercode,'".$notJoinCourseTyunNames."'))";
    }

    $query .= $query_where;
	$query .=" GROUP BY vtiger_activationcode.contractid";
    //_cs_logs(array("执行查询sql：".$query));
    $result = $adb->pquery($query, array());
    $rows = $adb->num_rows($result);
    $ret_lists = array();
    if ($rows>0) {
        while($row=$adb->fetchByAssoc($result)) {
            $lists = array();
            //客户ID
            $lists['accountid']=$row['accountid'];
            //客户名称
            $lists['accountname']=$row['accountname'];
            //邮箱
            $lists['email']=$row['email'];
            //手机号
            $lists['mobile']=$row['mobile'];
            //客户来源
            $lists['accountSource']=$row['accountSource'];

            $ret_lists[]=$lists;
        }
        $data=json_encode($ret_lists,JSON_UNESCAPED_UNICODE);
        $data=encrypt($data);
        echo json_encode(array("success"=>true,"is_multiple"=>true,"pageNum"=>$pageNum,"pageSize"=>$pageSize,"data"=>$data));
        _cs_logs(array("查询客户列表信息成功"));
    }else{
        _cs_logs(array("没有查询到相关信息"));
        echo json_encode(array("success"=>true,"is_multiple"=>true,"pageNum"=>$pageNum,"pageSize"=>$pageSize,"data"=>$ret_lists));
    }
    _cs_logs(array("查询客户列表信息接口结束"));
    exit;
}
//回款不足客户查询API
if(is_array($cs_method) && $cs_method[0]=='searchPaymentAccountList'){
    _cs_logs(array("查询回款不足客户接口开始"));
    global $adb;
    $reusltdata = $_REQUEST["data"];
    _cs_logs(array("查询回款不足客户接口参数:".$resultdata));
    $reusltdata = trim($reusltdata);
    $reusltdata = str_replace(' ', '+', $reusltdata);
    $decrdata = decrypt($reusltdata);
    $pad = strrpos($decrdata, '}');
    $decrdata = substr($decrdata, 0, $pad + 1);
    $decodedata = json_decode($decrdata, true);
    $accountid=$decodedata['accountid'];

    _cs_logs(array("查询回款不足客户接口解析后参数:".$decrdata));
    //T云系列合同(V5除外) 回款小于等于合同金额的50%。=>T云系列客户按照50%回款导致大量客户无法分配，经和许总沟通，分配规则调整和原来一样，客户工单只要回款大于成本即可分配
    //T云WEB版 回款小于等于合同金额的50%，视为回款不足（临时设置30% 后边crm有成本按成本设置）
    //无工单：非T云系列合同+V5、回款小于成本
    //有工单：非T云系列合同+V5、财务确认节点之前视为回款不足
    //原来已经分配过客服的客服，只要他原来业务没有到期，后面回来的合同就不限制，除了是原来业务到期了，需要重新分配这样才需要设计合同款项
    $query="SELECT DISTINCT M.accountid,M.servicecontractsid,M.msg,(SELECT accountname FROM vtiger_account WHERE accountid=M.accountid) AS accountname
			FROM(
			SELECT T1.sc_related_to AS accountid,T1.servicecontractsid,'回款不足:T云系列合同(V5除外) 、回款小于等于成本' AS msg FROM(
			SELECT A.servicecontractsid,A.sc_related_to,A.total,D.totalcost,
			IFNULL((SELECT sum(IFNULL(R.unit_price,0)) FROM vtiger_receivedpayments R WHERE R.deleted=0 AND R.receivedstatus='normal' AND R.relatetoid=A.servicecontractsid),0) AS payment
			FROM vtiger_servicecontracts A
			JOIN vtiger_crmentity C ON(A.servicecontractsid=C.crmid)
			JOIN (SELECT TT.servicecontractsid,SUM(TT.totalcost) AS totalcost FROM(
			SELECT servicecontractsid,
			(CASE 
			WHEN productcomboid = 0 THEN SUM(costing)
			ELSE
			MAX(prealprice)
			END) AS totalcost
			FROM vtiger_salesorderproductsrel WHERE multistatus=1 GROUP BY servicecontractsid,productcomboid) TT GROUP BY TT.servicecontractsid) D ON(A.servicecontractsid=D.servicecontractsid) 
			WHERE C.deleted=0
			AND A.servicecontractsid IN(SELECT 1 FROM vtiger_salesorderproductsrel WHERE vtiger_salesorderproductsrel.productid NOT IN(787685,2113422,522819,2115463) AND vtiger_salesorderproductsrel.productcomboid NOT IN(787685,2113422,522819,2115463))
			AND A.parent_contracttypeid = 2 AND A.modulestatus IN('c_complete','c_history') AND A.servicecontractstype='新增'
			AND A.total>0 
			) T1 WHERE T1.payment<=T1.totalcost
			UNION ALL
			SELECT T1.sc_related_to AS accountid,T1.servicecontractsid,'回款不足:T云WEB合同 、回款小于等于合同金额30%' AS msg FROM(
			SELECT A.servicecontractsid,A.sc_related_to,A.total,
			IFNULL((SELECT sum(IFNULL(R.unit_price,0)) FROM vtiger_receivedpayments R WHERE R.deleted=0 AND R.receivedstatus='normal' AND R.relatetoid=A.servicecontractsid),0) AS payment
			FROM vtiger_servicecontracts A
			JOIN vtiger_crmentity C ON(A.servicecontractsid=C.crmid)
			WHERE C.deleted=0
			AND A.parent_contracttypeid = 2 AND A.modulestatus IN('c_complete','c_history')
			AND A.total>0 and A.contract_type='T云WEB版' AND A.servicecontractstype='新增'
			) T1 WHERE T1.payment/T1.total<=0.3
			UNION ALL
			SELECT T2.sc_related_to AS accountid,T2.servicecontractsid,'回款不足:无工单、非T云系列合同+V5、回款小于成本' AS msg FROM(
			SELECT A.servicecontractsid,
			D.totalcost,
			IFNULL((SELECT SUM(IFNULL(unit_price,0)) FROM vtiger_receivedpayments WHERE  vtiger_receivedpayments.receivedstatus='normal' AND vtiger_receivedpayments.deleted=0 AND vtiger_receivedpayments.relatetoid=A.servicecontractsid),0) AS payment,
			B.sc_related_to
			FROM vtiger_salesorderproductsrel A
			JOIN vtiger_servicecontracts B ON(A.servicecontractsid=B.servicecontractsid)
			JOIN vtiger_crmentity C ON(B.servicecontractsid=C.crmid)
			JOIN (SELECT TT.servicecontractsid,SUM(TT.totalcost) AS totalcost FROM(
			SELECT servicecontractsid,
			(CASE 
			WHEN productcomboid = 0 THEN SUM(costing)
			ELSE
			MAX(prealprice)
			END) AS totalcost
			FROM vtiger_salesorderproductsrel WHERE multistatus=1 GROUP BY servicecontractsid,productcomboid) TT GROUP BY TT.servicecontractsid) D ON(B.servicecontractsid=D.servicecontractsid) 
			WHERE A.multistatus=1
			AND (B.parent_contracttypeid != 2 OR A.productid IN(787685,2113422,522819,2115463) OR A.productcomboid IN(787685,2113422,522819,2115463))
			AND B.total>0 AND C.deleted=0 AND B.modulestatus IN('c_complete','c_history') AND B.servicecontractstype='新增'
			AND B.servicecontractsid NOT IN(SELECT vtiger_salesorder.servicecontractsid FROM vtiger_salesorder INNER JOIN vtiger_crmentity ON(vtiger_salesorder.salesorderid=vtiger_crmentity.crmid) WHERE vtiger_crmentity.deleted=0)
			GROUP BY A.servicecontractsid) T2 WHERE T2.payment<T2.totalcost
			UNION ALL
			SELECT C.sc_related_to,A.servicecontractsid,'回款不足:有工单、非T云系列合同、财务确认(V系列无财务确认节点,只需第一个节点审核完成)节点之前的工单' AS msg FROM vtiger_salesorder A
			JOIN vtiger_salesorderproductsrel B ON(A.servicecontractsid=B.servicecontractsid)
			JOIN vtiger_servicecontracts C ON(A.servicecontractsid=C.servicecontractsid)
			JOIN vtiger_crmentity D ON(C.servicecontractsid=D.crmid)
			WHERE A.performanceoftime IS NULL AND A.googlestatus !=1 AND D.deleted=0 AND B.multistatus=1 AND A.modulestatus !='c_cancel'
			AND (C.parent_contracttypeid != 2 OR B.productid IN(787685,2113422,522819,2115463) OR B.productcomboid IN(787685,2113422,522819,2115463))
			AND C.modulestatus IN('c_complete','c_history') AND C.servicecontractstype='新增'
			) M WHERE 1=1 ";

    if(!empty($accountid)){
        $query .= " AND M.accountid={$accountid}";
    }

    //_cs_logs(array("查询回款不足客户接口,执行查询sql：".$query));
    $result = $adb->pquery($query, array());
    $rows = $adb->num_rows($result);
    $ret_lists = array();
    if ($rows>0) {
        $accountid_list = array();
        $contractid_list = array();
        for ($i = 0; $i < $rows; ++$i) {
            $tmp_row = $adb->raw_query_result_rowdata($result, $i);
            $accountid_list[]=$tmp_row['accountid'];
            $contractid_list[]=$tmp_row['servicecontractsid'];
        }
        //一个客户有多个合同，不管是以前的合同还是新合同，只要有一个合同满足回款充足条件,都可以分配或切换客服
        $accountIds = join(',',$accountid_list);
        $contractIds = join(',',$contractid_list);
        $ex_query = "SELECT IFNULL(B.customerid,A.customerid) AS accountid,A.contractid FROM vtiger_activationcode A 
				LEFT JOIN (SELECT activationcodeid,customerid FROM vtiger_activationcode WHERE classtype='buy' AND `status` in(0,1)) B ON(A.buyid=B.activationcodeid)
				WHERE 
				A.usercode IN(SELECT usercode FROM vtiger_activationcode WHERE `status` in(0,1) and classtype='buy' AND customerid IN({$accountIds}) AND contractid NOT IN({$contractIds}) GROUP BY customerid) 
				AND A.expiredate>CURDATE() AND A.`status` in(0,1) AND IFNULL(B.customerid,A.customerid) IS NOT NULL AND A.contractid>0
				UNION ALL
				SELECT A.sc_related_to AS accountid,A.servicecontractsid AS contractid  FROM vtiger_servicecontracts A
				JOIN vtiger_crmentity B ON(A.servicecontractsid=B.crmid)
				WHERE B.deleted=0 AND  A.sc_related_to IN({$accountIds}) AND A.servicecontractsid NOT IN({$contractIds}) AND A.modulestatus IN('c_complete','c_history') AND A.effectivetime>=CURDATE()";

        //_cs_logs(array("查询回款不足客户接口,执行服务中合同查询sql：".$ex_query));
        $ex_result = $adb->pquery($ex_query, array());
        $ex_rows = $adb->num_rows($ex_result);
        $ex_accountid_list = array();
        if($ex_rows > 0) {
            for ($j = 0; $j < $ex_rows; ++$j) {
                $tmp_row = $adb->raw_query_result_rowdata($ex_result, $j);
                $ex_accountid_list[] = (integer)$tmp_row['accountid'];
            }
        }
        //_cs_logs(array("查询回款不足客户接口,执行服务中客户列表：".json_encode($ex_accountid_list)));
        for ($i = 0; $i < $rows; ++$i) {
            $row = $adb->raw_query_result_rowdata($result, $i);
            $lists = array();
            $p_accountid= $row['accountid'];
            //客户ID
            $lists['accountid']=$row['accountid'];
            //合同id
            $lists['servicecontractsid']=$row['servicecontractsid'];
            //提示信息
            $lists['msg']=$row['msg'];
            //客户名称
            $lists['accountname']=$row['accountname'];
            //_cs_logs(array("查询回款不足客户接口,回款不足客户id：".$p_accountid));
            if(in_array($p_accountid,$ex_accountid_list)){
            }else{
                $ret_lists[]=$lists;
            }
        }
        //(array("查询回款不足客户接口,执行服务中客户列表：".json_encode($ret_lists)));
        $data=json_encode($ret_lists,JSON_UNESCAPED_UNICODE);
        $data=encrypt($data);
        echo json_encode(array("success"=>true,"is_multiple"=>true,"data"=>$data));
        _cs_logs(array("查询回款不足客户接口成功"));
    }else{
        _cs_logs(array("没有查询回款不足客户"));
        echo json_encode(array("success"=>true,"is_multiple"=>true,"data"=>$ret_lists));
    }
    _cs_logs(array("查询回款不足客户接口结束"));
    exit;
}
//=================跟进记录API================================================================
if(is_array($cs_method) && $cs_method[0]=='addModComment') {
    _cs_logs(array("跟进记录追加接口开始"));
    global $adb, $log;
	_cs_logs($_REQUEST);
    $reusltdata = $_REQUEST["data"];
	$decodedata = json_decode($reusltdata, true);
    //$reusltdata = trim($reusltdata);
    //$decodedata = str_replace(' ', '+', $decodedata);
    //$decrdata = decrypt($reusltdata);
    //$pad = strrpos($decrdata, '}');
    //$decrdata = substr($decrdata, 0, $pad + 1);
	_cs_logs(array("客户跟进接口参数1:",$decodedata));
    //$decodedata = json_decode($reusltdata, true);
	//_cs_logs(array("客户跟进接口参数2:".$decodedata));
	_cs_logs(array("客户id=".$decodedata['accountid']));
    //if (is_array($decodedata)) {
        if (!empty($decodedata['accountid']) && is_numeric($decodedata['accountid'])) {
			$data['modcommentsid'] = $adb->getUniqueID('vtiger_modcomments');
			$data['commentcontent'] = base64_decode(str_replace(' ', '+', $decodedata['commentcontent']));
			$data['related_to'] = $decodedata['accountid'];
			$data['addtime'] = date("Y-m-d H:i:s");
			$data['creatorid'] = $decodedata['creatorid'];
			$data['modcommenttype'] = $decodedata['modcommenttype'];
			$data['modcommentmode'] = $decodedata['modcommentmode'];
			//$data['modcommenthistory']=$decodedata['modcommenthistory'];
			$data['contact_id'] = $decodedata['contact_id'];
			$data['moduleid'] = $decodedata['accountid'];
			$data['modcommentpurpose'] = $decodedata['modcommentpurpose'];
			$data['sourceType'] = $decodedata['sourceType'];

			$sql = "INSERT INTO `vtiger_modcomments` (`modcommentsid`, `commentcontent`, `related_to`, `addtime`, `creatorid`, `modcommenttype`, `modcommentmode`, `contact_id`, `modulename`, `moduleid`, `modcommentpurpose`,source_type,`commentreturnplanid`,followrole) VALUES (?,?,?,?,?,?,?,?,'Accounts',?,?,?,'0',1)";
			//_cs_logs(array("执行跟进记录追加的sql：".$sql));
			$adb->pquery($sql, array($data));

            _cs_logs(array("跟进记录追加成功"));
            $lists = array('success' => true, 'msg' => '数据保存成功!');
            echo json_encode($lists, JSON_UNESCAPED_UNICODE);
        } else {
            _cs_logs(array("跟进记录追加失败,参数错误"));
            $lists = array('success' => false, 'msg' => '没有跟进信息');
            echo json_encode($lists, JSON_UNESCAPED_UNICODE);
        }
    //} else {
    //    _cs_logs(array("跟进记录追加失败"));
    //    $lists = array('success' => false, 'msg' => '数据错误');
    //    echo json_encode($lists, JSON_UNESCAPED_UNICODE);
    //}
    _cs_logs(array("跟进记录追加接口结束"));
    exit;
}
//=================分配客服API================================================================
if(is_array($cs_method) && $cs_method[0]=='accountAllot') {
    _cs_logs(array("客户分配接口开始"));
    global $adb, $log;
    $reusltdata = $_REQUEST["data"];
    _cs_logs(array("客户分配接口参数:".$resultdata));
    $reusltdata = trim($reusltdata);
    $reusltdata = str_replace(' ', '+', $reusltdata);
    $decrdata = decrypt($reusltdata);
    $pad = strrpos($decrdata, '}');
    $decrdata = substr($decrdata, 0, $pad + 1);
    $decodedata = json_decode($decrdata, true);
    _cs_logs(array("客户分配接口解密后参数:".$decrdata));
    if(is_array($decodedata)){
        if(!empty($decodedata['serviceid'])&& is_numeric($decodedata['serviceid'])){
            $arr_account = explode(",",$decodedata['accountids']);
            //_cs_logs(array($arr_account));
            if(count($arr_account)>0){
				$sourceType = $decodedata['sourceType'];
                for($i=0;$i<count($arr_account);$i++){
					$accountid = $arr_account[$i];
					if($sourceType == 1){
						//海外客服分配
						$sql = "SELECT id FROM vtiger_account_allot_other WHERE accountid=? AND source_type=1";
						$ex_result = $adb->pquery($sql, array($accountid));
						$ex_rows = $adb->num_rows($ex_result);
						if($ex_rows > 0) {
							$tmp_row = $adb->raw_query_result_rowdata($ex_result, 0);
							$update_sql = "UPDATE vtiger_account_allot_other SET csid=?,last_update_by=?,last_update_time=NOW() WHERE id=?";	
							$adb->pquery($update_sql, array($decodedata['serviceid'],$decodedata['assignerid'],$tmp_row['id']));
						}else{
							$update_sql = "INSERT INTO vtiger_account_allot_other(accountid,csid,last_update_by,last_update_time,source_type) values(?,?,?,NOW(),1)";	
							$adb->pquery($update_sql, array($accountid,$decodedata['serviceid'],$decodedata['assignerid']));
						}
					}else{
						//国内客服分配
						$arr_servicecomments=array();
						$arr_servicecomments['assigntype']='accountby';
						$arr_servicecomments['salesorderproductsrelid']=0;
						$arr_servicecomments['related_to']=$arr_account[$i];
						$arr_servicecomments['serviceid']=$decodedata['serviceid'];
						$arr_servicecomments['assignerid']=$decodedata['assignerid'];

						//未跟进天数
						$followfrequency = getFollowfrequencyDay($arr_account[$i]['accountid']);

						$servicecommentsId = getAllotServicecommentsId($arr_servicecomments);
						_cs_logs(array("客服分配id：".$servicecommentsId));
						if($servicecommentsId > 0){
							//客服分配数据更新
							$updateSql = "update vtiger_servicecomments set serviceid=?,assignerid=?,allocatetime=sysdate(),modifiedtime=sysdate(),nofollowday=? where related_to=?";
							$parm_array=array();
							$parm_array[]=$arr_servicecomments['serviceid'];
							$parm_array[]=$arr_servicecomments['assignerid'];
							$parm_array[]=$followfrequency;
							$parm_array[]=$arr_servicecomments['related_to'];
							//_cs_logs(array("执行sql：".$updateSql));
							_cs_logs(array("执行sql参数：".json_encode($parm_array)));
							$adb->pquery($updateSql, $parm_array);
						}else{
							//字段名取得
							$columns=array_keys($arr_servicecomments);
							//字段值取得
							$values=array_values($arr_servicecomments);

							//客服分配数据插入
							$servicecommentsid= $adb->getUniqueID("vtiger_servicecomments");
							$insertSql = "insert into vtiger_servicecomments(" . implode(",", $columns) . ",addtime,allocatetime,nofollowday,servicecommentsid)
							values(" . generateQuestionMarks($values) . ",sysdate(),sysdate(),".$followfrequency.",".$servicecommentsid.")";
							//_cs_logs(array("执行sql：".$insertSql));
							_cs_logs(array("执行sql参数：".json_encode($values)));
							$adb->pquery($insertSql, $values);
						}

						//更新客户表客服字段
						$update_account_Sql = "update vtiger_account set serviceid=?,allottime=NOW(),allottimestamp=UNIX_TIMESTAMP(NOW()) where accountid=?";
						$parm_array=array();
						$parm_array[]=$arr_servicecomments['serviceid'];
						$parm_array[]=$arr_servicecomments['related_to'];
						//_cs_logs(array("执行sql：".$update_account_Sql));
						//_cs_logs(array("执行sql参数：".json_encode($parm_array)));
						$adb->pquery($update_account_Sql, $parm_array);
					}
                }
				$msg = $sourceType==1?'海外':'国内';
                _cs_logs(array($msg."客户分配成功"));
                $lists=array('success'=>true,'msg'=>$msg.'客户分配成功!');
                echo json_encode($lists,JSON_UNESCAPED_UNICODE);
            }else{
                _cs_logs(array("没有要分配的客户信息"));
                $lists=array('success'=>false,'msg'=>'没有分配的客户信息');
                echo json_encode($lists,JSON_UNESCAPED_UNICODE);
            }
        }else{
            _cs_logs(array("没有要分配的信息"));
            $lists=array('success'=>false,'msg'=>'没有分配信息');
            echo json_encode($lists,JSON_UNESCAPED_UNICODE);
        }
    }else{
        _cs_logs(array("客户分配错误"));
        $lists=array('success'=>false,'msg'=>'数据错误');
        echo json_encode($lists,JSON_UNESCAPED_UNICODE);
    }
    _cs_logs(array("客户分配接口结束"));
    exit;
}
//=================获取另购信息================================================================
if(is_array($cs_method) && $cs_method[0]=='searchAgainBuyList'){
    _cs_logs(array("查询另购信息接口开始"));
    include_once 'vtlib/Vtiger/Module.php';
    include_once 'includes/main/WebUI.php';
    include_once('modules/Vtiger/models/Record.php');

    global $adb;
    _cs_logs(array("查询另购信息接口参数:".json_encode($_REQUEST)));
    $reusltdata = $_REQUEST["data"];

    $reusltdata = trim($reusltdata);
    $reusltdata = str_replace(' ', '+', $reusltdata);
    $decrdata = decrypt($reusltdata);
    $pad = strrpos($decrdata, '}');
    $decrdata = substr($decrdata, 0, $pad + 1);
    $decodedata = json_decode($decrdata, true);

    _cs_logs(array("查询另购信息接口解析后参数:".$decodedata));
    $contractid=$decodedata['contractid'];
    $query_sql = "SELECT buyserviceinfo FROM vtiger_activationcode WHERE `status` IN(0,1) AND buyserviceinfo IS NOT NULL AND contractid={$contractid}";
    //_cs_logs(array("执行查询sql：".$query_sql));
    $result = $adb->pquery($query_sql,array());
    $data = $adb->query_result_rowdata($result);
    //_cs_logs(array("查询另购信息:".json_encode($data)));

    if($data){
        //另购服务
        $recordModel = Vtiger_Record_Model::getCleanInstance('ActivationCode');
        //_cs_logs(array("实例化接口类信息：".json_encode($recordModel)));
        $tyunAllServiceItem = $recordModel->getTyunServiceItem(new Vtiger_Request());
        //_cs_logs(array("获取另购服务信息件数：".count($tyunAllServiceItem)));
        $buyserviceinfo = $data['buyserviceinfo'];
        if(!empty($buyserviceinfo)){
            $buyserviceinfo = htmlspecialchars_decode($buyserviceinfo);
            $arr_buyserviceinfo = json_decode($buyserviceinfo,true);
            $serviceContent = "";
            for($a=0;$a<count($arr_buyserviceinfo);$a++){
                $buyCount = $arr_buyserviceinfo[$a]['BuyCount'];
                $serviceID = $arr_buyserviceinfo[$a]['ServiceID'];

                for($b=0;$b<count($tyunAllServiceItem);$b++){
                    $serviceID2 = $tyunAllServiceItem[$b]['ServiceID'];
                    $serviceName = $tyunAllServiceItem[$b]['ServiceName'];
                    $multiple = $tyunAllServiceItem[$b]["Multiple"];
                    $unit = $tyunAllServiceItem[$b]["Unit"];
                    $buyCountDisp = $buyCount.$unit;
                    if($serviceID == $serviceID2){
                        $arr_buyservice[] = array("serviceName"=>$serviceName,"buyCount"=>$buyCountDisp,"Multiple"=>$multiple,"Unit"=>$unit);
                        break;
                    }
                }
            }
            $data=json_encode($arr_buyservice,JSON_UNESCAPED_UNICODE);
            $data=encrypt($data);
            echo json_encode(array("success"=>true,"is_multiple"=>true,"total"=>0,"data"=>$data));
            _cs_logs(array("查询另购信息成功"));
        }
    }else{
        _cs_logs(array("没有查询到另购信息"));
        echo json_encode(array("success"=>true,"is_multiple"=>true,"total"=>0,"data"=>array()));
    }
    _cs_logs(array("查询另购信息接口结束"));
    exit;
}
//=================保存备注API================================================================
if(is_array($cs_method) && $cs_method[0]=='saveAccountRemarkToCrm') {
    _cs_logs(array("保存备注接口开始"));
    global $adb, $log;
    $reusltdata = $_REQUEST["data"];
    _cs_logs(array("保存备注接口传入参数:".$reusltdata));
    $reusltdata = trim($reusltdata);
    $reusltdata = str_replace(' ', '+', $reusltdata);
    $decrdata = decrypt($reusltdata);
    $pad = strrpos($decrdata, '}');
    $decrdata = substr($decrdata, 0, $pad + 1);
    $decodedata = json_decode($decrdata, true);
    _cs_logs(array("保存备接口解析后参数:".json_encode($decodedata)));
    if (is_array($decodedata)) {
        if (!empty($decodedata['accountId']) && is_numeric($decodedata['accountId'])) {
            $remarkEditFlag = $decodedata['remarkEditFlag'];
            if($remarkEditFlag == '1' || $remarkEditFlag == 1){
                _cs_logs(array("~保存客户备注~"));
                $data[] = $decodedata['userId'];
                $data[] = $decodedata['remarkContent'];
                $data[] = $decodedata['accountId'];
                $updateSql = "UPDATE vtiger_crmentity SET modifiedby=?,modifiedtime=NOW(),description=? WHERE crmid=? AND deleted=0";
            }
            if($remarkEditFlag == '2' || $remarkEditFlag == 2){
                _cs_logs(array("~保存客户跟进备注~"));

                $sql = "SELECT servicecommentsid FROM vtiger_servicecomments WHERE assigntype='accountby' AND related_to=? ORDER BY allocatetime DESC LIMIT 1";
                $querysql = $adb->pquery($sql, array($decodedata['accountId']));
                if ($adb->num_rows($querysql)>0) {
                    $servicecommentsid = $adb->query_result($querysql, 0 ,'servicecommentsid');
                    $data[] = $decodedata['remarkContent'];
                    $data[] = $servicecommentsid;
                    $updateSql = "UPDATE vtiger_servicecomments SET remark=?,updatetime=NOW() WHERE servicecommentsid=?";
                }else{
                    _cs_logs(array("没有客户跟进信息"));
                    $lists = array('success' => false, 'msg' => '没有客户跟进信息');
                    echo json_encode($lists, JSON_UNESCAPED_UNICODE);
                    exit;
                }
            }
            if($remarkEditFlag == '3' || $remarkEditFlag == 3){
                _cs_logs(array("~保存客户跟进内部备注~"));
                $sql = "SELECT servicecommentsid FROM vtiger_servicecomments WHERE assigntype='accountby' AND related_to=? ORDER BY allocatetime DESC LIMIT 1";
                $querysql = $adb->pquery($sql, array($decodedata['accountId']));
                if ($adb->num_rows($querysql)>0) {
                    $servicecommentsid = $adb->query_result($querysql, 0 ,'servicecommentsid');
                    $data[] = $decodedata['remarkContent'];
                    $data[] = $servicecommentsid;

                    $updateSql = "UPDATE vtiger_servicecomments SET inremark=?,updatetime=NOW() WHERE servicecommentsid=?";
                }else{
                    //_cs_logs(array("没有客户跟进信息"));
                    $lists = array('success' => false, 'msg' => '没有客户跟进信息');
                    echo json_encode($lists, JSON_UNESCAPED_UNICODE);
                    exit;
                }
            }
            //_cs_logs(array("执行保存备注的sql：".$updateSql));
            //_cs_logs(array("执行保存备注的参数：".json_encode($data)));
            $adb->pquery($updateSql, $data);
            _cs_logs(array("保存备注成功"));
            $lists = array('success' => true, 'msg' => '保存备注成功!');
            echo json_encode($lists, JSON_UNESCAPED_UNICODE);
        } else {
            _cs_logs(array("没有保存备注信息"));
            $lists = array('success' => false, 'msg' => '没有保存备注信息');
            echo json_encode($lists, JSON_UNESCAPED_UNICODE);
        }
    } else {
        _cs_logs(array("保存备注失败"));
        $lists = array('success' => false, 'msg' => '数据错误');
        echo json_encode($lists, JSON_UNESCAPED_UNICODE);
    }
    _cs_logs(array("保存备注接口结束"));
    exit;
}
//=================保存客户联系人标签API================================================================
if(is_array($cs_method) && $cs_method[0]=='saveAccountContactsTagToCrm') {
    _cs_logs(array("保存客户联系人标签接口开始"));
    global $adb, $log;
    $reusltdata = $_REQUEST["data"];
    _cs_logs(array("保存客户联系人标签接口传入参数:".$reusltdata));
    $reusltdata = trim($reusltdata);
    $reusltdata = str_replace(' ', '+', $reusltdata);
    $decrdata = decrypt($reusltdata);
    $pad = strrpos($decrdata, '}');
    $decrdata = substr($decrdata, 0, $pad + 1);
    $decodedata = json_decode($decrdata, true);
    _cs_logs(array("保客户联系人标签接口解析后参数:".json_encode($decodedata)));
    if (is_array($decodedata)) {
        if (!empty($decodedata['contactid']) && is_numeric($decodedata['contactid'])) {
            $data[] = $decodedata['leaveOffice'];
            $data[] = $decodedata['contactid'];


            $updateSql = "UPDATE vtiger_contactdetails SET leave_office=? WHERE contactid=?";

            //_cs_logs(array("执行客户联系人标签的sql：".$updateSql));
            //_cs_logs(array("执行客户联系人标签的参数：".json_encode($data)));
            $adb->pquery($updateSql, array($data));
            _cs_logs(array("保存客户联系人标签成功"));
            $lists = array('success' => true, 'msg' => '保存客户联系人标签成功!');
            echo json_encode($lists, JSON_UNESCAPED_UNICODE);
        } else {
            _cs_logs(array("没有保存备注信息"));
            $lists = array('success' => false, 'msg' => '没有客户联系人标签信息');
            echo json_encode($lists, JSON_UNESCAPED_UNICODE);
        }
    } else {
        _cs_logs(array("保存客户联系人标签失败"));
        $lists = array('success' => false, 'msg' => '数据错误');
        echo json_encode($lists, JSON_UNESCAPED_UNICODE);
    }
    _cs_logs(array("保存客户联系人标签接口结束"));
    exit;
}
//=================增加客户联系人API================================================================
if(is_array($cs_method) && $cs_method[0]=='addAccountContactsToCrm') {
    _cs_logs(array("保存客户联系人接口开始"));
    global $adb, $log;
    $reusltdata = $_REQUEST["data"];
    _cs_logs(array("保存客户联系人接口传入参数:".$reusltdata));
    $reusltdata = trim($reusltdata);
    $reusltdata = str_replace(' ', '+', $reusltdata);
    $decrdata = decrypt($reusltdata);
    $pad = strrpos($decrdata, '}');
    $decrdata = substr($decrdata, 0, $pad + 1);
    $decodedata = json_decode($decrdata, true);
    _cs_logs(array("保存客户联系人接口解析后参数:".json_encode($decodedata)));
    if (is_array($decodedata)) {
        if (!empty($decodedata['accountid']) && is_numeric($decodedata['accountid'])) {
			$contactid = $decodedata['contactid'];
			if(empty($contactid) || $contactid == 0 || $contactid == '0'){
				//新增
				$crmid = $adb->getUniqueID('vtiger_crmentity');
				$data[] = $crmid;
				$data[] = $decodedata['userId'];
				//_cs_logs(array("执行联系人主表参数：".json_encode($data)));
				$sql = "INSERT INTO `vtiger_crmentity` (`crmid`, `smcreatorid`, `setype`, `createdtime`) VALUES (?,?,'Contacts',NOW())";
				$adb->pquery($sql, array($data));
				//_cs_logs(array("联系人主表追加成功"));

				//追加联系人数据
				$dataContact['contactid'] = $crmid;
				$dataContact['accountid'] = $decodedata['accountid'];
				$dataContact['email'] = $decodedata['email'];
				$dataContact['phone'] = $decodedata['phone'];
				$dataContact['mobile'] = $decodedata['mobile'];
				$dataContact['title'] = $decodedata['title'];
				$dataContact['name'] = $decodedata['name'];
				$dataContact['weixin'] = $decodedata['weixin'];
				$dataContact['qq'] = $decodedata['qq'];
				$dataContact['gender'] = $decodedata['gendertype'];
				$dataContact['makedecision'] = $decodedata['makedecision'];
				//_cs_logs(array("执行联系人表参数：".json_encode($dataContact)));
				$updateSql = "INSERT INTO `vtiger_contactdetails` (`contactid`, `accountid`, `email`, phone,mobile,title,name,weixin,qq,gender,makedecision) VALUES (?,?,?,?,?,?,?,?,?,?,?)";
				$adb->pquery($updateSql, array($dataContact));
			}else {
				//编辑
				$data[] = $decodedata['userId'];
				$data[] = $contactid;
				$sql = "UPDATE `vtiger_crmentity` SET modifiedby=?,modifiedtime=NOW() WHERE crmid=?";
				$adb->pquery($sql, array($data));
				
				//联系人数据
				$dataContact['email'] = $decodedata['email'];
				$dataContact['phone'] = $decodedata['phone'];
				$dataContact['mobile'] = $decodedata['mobile'];
				$dataContact['title'] = $decodedata['title'];
				$dataContact['name'] = $decodedata['name'];
				$dataContact['weixin'] = $decodedata['weixin'];
				$dataContact['qq'] = $decodedata['qq'];
				$dataContact['gender'] = $decodedata['gendertype'];
				$dataContact['makedecision'] = $decodedata['makedecision'];
				$dataContact['contactid'] = $contactid;
				//_cs_logs(array("执行联系人表参数：".json_encode($dataContact)));
				$updateSql = "UPDATE `vtiger_contactdetails` SET email=?,phone=?,mobile=?,title=?,name=?,weixin=?,qq=?,gender=?,makedecision=? WHERE contactid=?";
				$adb->pquery($updateSql, array($dataContact));
			}
			
            //_cs_logs(array("联系人表追加成功"));
            $lists = array('success' => true, 'msg' => '保存客户联系人成功!');
            echo json_encode($lists, JSON_UNESCAPED_UNICODE);
        } else {
            _cs_logs(array("没有客户信息"));
            $lists = array('success' => false, 'msg' => '没有客户信息');
            echo json_encode($lists, JSON_UNESCAPED_UNICODE);
        }
    } else {
        _cs_logs(array("保存客户联系人失败"));
        $lists = array('success' => false, 'msg' => '数据错误');
        echo json_encode($lists, JSON_UNESCAPED_UNICODE);
    }
    _cs_logs(array("保存客户联系人接口结束"));
    exit;
}

if(is_array($cs_method) && $cs_method[0]=='searchTyunOrderUser'){
    _cs_logs(array("查询T云web用户信息接口开始"));
    include_once 'vtlib/Vtiger/Module.php';
    include_once 'includes/main/WebUI.php';
    include_once('modules/Vtiger/models/Record.php');

    global $adb;
    _cs_logs(array("查询T云web用户信息接口参数:".json_encode($_REQUEST)));
    $accountId = $_GET['accountid'];
    $isTyunWeb = $_GET['isTyunWeb'];
    $usercode = $_GET['loginname'];
    $sourceType = $_GET['bsourceType'];
	
    $query_sql = "SELECT DISTINCT customerid,customername,usercode,MAX(activedate) activedate,productid,
				usercode as loginnametitle,
				IFNULL(usercodeid,0) AS usercodeid,IFNULL(usercodeid,0) AS userid,comeformtyun from vtiger_activationcode WHERE customerid={$accountId} and `status` IN(0,1) AND usercode IS NOT NULL AND usercode!=''
				AND comeformtyun !=4";
		
	if($sourceType == 100 || $sourceType == "100"){
    }else{		
		if($sourceType == 1 || $sourceType == "1"){
			$query_sql .= " and productclass=1";
		}else{
			$query_sql .= " and (productclass!=1 OR productclass IS NULL)";
		}
	}
	
	if(!empty($isTyunWeb) && $isTyunWeb == 1){
        $query_sql .= " and comeformtyun=1";
    }
    if(!empty($usercode)){
        $query_sql .= " and usercode={$usercode}";
    }
	$query_sql .= " GROUP BY customerid,usercode";
    //_cs_logs(array("执行查询sql：".$query_sql));
    $result = $adb->pquery($query_sql, array());
    $rows = $adb->num_rows($result);
    $ret_lists = array();
    if ($rows>0) {
        while($row=$adb->fetchByAssoc($result)) {
            $lists = array();
            //客户ID
            $lists['accountid']=$row['customerid'];
            $lists['customername']=$row['customername'];
            $lists['loginName']=$row['usercode'];
            $lists['loginNameTitle']=$row['loginnametitle'];
            $lists['usercodeid']=$row['usercodeid'];
            $lists['userid']=$row['userid'];
            $lists['activedate']=$row['activedate'];
            $lists['productid']=$row['productid'];
            $lists['accountSource']=$row['comeformtyun'];
            $ret_lists[]=$lists;
        }
        $data=json_encode($ret_lists,JSON_UNESCAPED_UNICODE);
        $data=encrypt($data);
        echo json_encode(array("success"=>true,"is_multiple"=>true,"data"=>$data));
    }else{
        _cs_logs(array("没有查询T云web用户信息"));
        echo json_encode(array("success"=>true,"is_multiple"=>true,"total"=>0,"data"=>array()));
    }
    _cs_logs(array("查询T云web用户信息接口结束"));
    exit;
}

if(is_array($cs_method) && $cs_method[0]=='searchCrmAccountNameList'){
    _cs_logs(array("查询客户名称列表接口开始"));
    include_once 'vtlib/Vtiger/Module.php';
    include_once 'includes/main/WebUI.php';
    include_once('modules/Vtiger/models/Record.php');

    global $adb;
    _cs_logs(array("查询客户名称列表接口参数:".json_encode($_REQUEST)));
    $userIds = $_GET['userIds'];

    $query_sql = "SELECT DISTINCT A.accountid customerid,A.accountname customername,A.serviceid,C.smownerid,D.companyid,E.departmentid FROM vtiger_account  A
				INNER JOIN vtiger_crmentity C ON(C.crmid=A.accountid)
				LEFT JOIN vtiger_users D ON(D.id=C.smownerid)
				LEFT JOIN vtiger_user2department E ON(C.smownerid=E.userid)
				WHERE C.deleted=0 ";

    if(!empty($userIds)){
        $userIds = rtrim($userIds,',');
        $query_sql .= " AND A.serviceid IN({$userIds})";
    }
	
    //_cs_logs(array("执行查询sql：".$query_sql));
    $result = $adb->pquery($query_sql, array());
    $rows = $adb->num_rows($result);
    $ret_lists = array();
    if ($rows>0) {
        while($row=$adb->fetchByAssoc($result)) {
            $lists = array();
            //客户ID
            $lists['accountid']=$row['customerid'];
            $lists['accountname']=$row['customername'];
            $lists['serviceid']=$row['serviceid'];
            $lists['smownerid']=$row['smownerid'];
            $lists['companyid']=$row['companyid'];
            $lists['departmentid']=$row['departmentid'];
            $ret_lists[]=$lists;
        }
        $data=json_encode($ret_lists,JSON_UNESCAPED_UNICODE);
        $data=encrypt($data);
        echo json_encode(array("success"=>true,"is_multiple"=>true,"data"=>$data));
    }else{
        _cs_logs(array("没有查询客户名称列表"));
        echo json_encode(array("success"=>true,"is_multiple"=>true,"total"=>0,"data"=>array()));
    }
    _cs_logs(array("查询客户名称列表接口结束"));
    exit;
}

if(is_array($cs_method) && $cs_method[0]=='searchCrmcontractNoList'){
    _cs_logs(array("查询客户合同列表接口开始"));
    include_once 'vtlib/Vtiger/Module.php';
    include_once 'includes/main/WebUI.php';
    include_once('modules/Vtiger/models/Record.php');

    global $adb;
    _cs_logs(array("查询客户合同列表接口参数:".json_encode($_REQUEST)));
    $accountid = $_GET['accountid'];
    $loginId = $_GET['loginId'];
    $sourceType = $_GET['sourceType'];

    $query_sql = "SELECT DISTINCT * FROM(
					SELECT
					vtiger_activationcode.classtype,
					vtiger_activationcode.creator,
					vtiger_activationcode.contractid servicecontractsid,
					vtiger_activationcode.contractname contract_no,
					vtiger_activationcode.expiredate,
					vtiger_activationcode.productclass,
					vtiger_activationcode.usercode,
					vtiger_activationcode.comeformtyun,
					IFNULL(B.expiredate,vtiger_activationcode.expiredate) as buyexpiredate
					FROM vtiger_activationcode
					LEFT JOIN vtiger_activationcode B ON(B.buyid=vtiger_activationcode.activationcodeid and B.classtype='buy' AND B.`status` IN(0,1)) 
					WHERE vtiger_activationcode.`status` IN(0,1) AND vtiger_activationcode.customerid={$accountid}";
	//针对黄玉琴、李季下单的订单，做特殊处理
    $query_sql .= splicingSpecialSql($loginId);				
	$query_sql .= "	UNION ALL 
					SELECT 
					'' classtype,
					0 creator,
					A.servicecontractsid,
					A.contract_no,
					A.effectivetime expiredate,
					A.servicecontractstype productclass,
					'noTyunUser' usercode,
					0 comeformtyun,
					A.effectivetime as buyexpiredate
					FROM vtiger_servicecontracts A
					INNER JOIN vtiger_crmentity B ON(A.servicecontractsid=B.crmid)
					WHERE B.deleted=0 AND A.modulestatus IN ('c_complete', '已发放') AND A.sc_related_to={$accountid}
					AND A.servicecontractsid NOT IN(
					SELECT contractid FROM vtiger_activationcode WHERE `status` IN(0,1) AND customerid={$accountid} 
					)) T WHERE T.servicecontractsid>0 ";

    
    _cs_logs(array("执行查询sql：".$query_sql));
    $result = $adb->pquery($query_sql, array());
    $rows = $adb->num_rows($result);
    $ret_lists = array();
    if ($rows>0) {
        while($row=$adb->fetchByAssoc($result)) {
            $lists = array();
            //客户ID
            $lists['contractsid']=$row['servicecontractsid'];
            $lists['contractno']=$row['contract_no'];
            $lists['expiredate']=$row['expiredate'];
            $lists['buyexpiredate']=$row['buyexpiredate'];
            $lists['productclass']=$row['productclass'];
            $lists['usercode']=$row['usercode'];
            $lists['comeformtyun']=$row['comeformtyun'];
            $ret_lists[]=$lists;
        }
        $data=json_encode($ret_lists,JSON_UNESCAPED_UNICODE);
        $data=encrypt($data);
        echo json_encode(array("success"=>true,"is_multiple"=>true,"data"=>$data));
    }else{
        _cs_logs(array("没有查询客户合同列表"));
        echo json_encode(array("success"=>true,"is_multiple"=>true,"total"=>0,"data"=>array()));
    }
    _cs_logs(array("查询客户合同列表接口结束"));
    exit;
}

if(is_array($cs_method) && $cs_method[0]=='queryContractNoListByContractNo'){
    _cs_logs(array("根据合同编号查询客户合同列表接口开始"));
    include_once 'vtlib/Vtiger/Module.php';
    include_once 'includes/main/WebUI.php';
    include_once('modules/Vtiger/models/Record.php');

    global $adb;
    _cs_logs(array("根据合同编号查询客户合同列表接口参数:".json_encode($_REQUEST)));
    $contractNo = $_GET['contractNo'];
    $loginId = $_GET['loginId'];
    $sourceType = $_GET['sourceType'];

    $query_sql = "SELECT 
					A.servicecontractsid,
					A.contract_no,
					A.effectivetime expiredate,
					A.servicecontractstype productclass,
					'noTyunUser' usercode,
					0 comeformtyun,
					A.effectivetime as buyexpiredate
					FROM vtiger_servicecontracts A
					INNER JOIN vtiger_crmentity B ON(A.servicecontractsid=B.crmid)
					WHERE B.deleted=0 AND A.modulestatus IN ('已发放') AND A.contract_no LIKE '%{$contractNo}%'  
					AND A.servicecontractsid NOT IN(
					SELECT contractid FROM vtiger_activationcode WHERE `status` IN(0,1) AND contractname=A.contract_no
					)";

    //_cs_logs(array("执行查询sql：".$query_sql));
    $result = $adb->pquery($query_sql, array());
    $rows = $adb->num_rows($result);
    $ret_lists = array();
    if ($rows>0) {
        while($row=$adb->fetchByAssoc($result)) {
            $lists = array();
            //客户ID
            $lists['contractsid']=$row['servicecontractsid'];
            $lists['contractno']=$row['contract_no'];
            $lists['expiredate']=$row['expiredate'];
            $lists['buyexpiredate']=$row['buyexpiredate'];
            $lists['productclass']=$row['productclass'];
            $lists['usercode']=$row['usercode'];
            $lists['comeformtyun']=$row['comeformtyun'];
            $ret_lists[]=$lists;
        }
        $data=json_encode($ret_lists,JSON_UNESCAPED_UNICODE);
        $data=encrypt($data);
        echo json_encode(array("success"=>true,"is_multiple"=>true,"data"=>$data));
    }else{
        _cs_logs(array("没有查询客户合同列表"));
        echo json_encode(array("success"=>true,"is_multiple"=>true,"total"=>0,"data"=>array()));
    }
    _cs_logs(array("查询客户合同列表接口结束"));
    exit;
}

if(is_array($cs_method) && $cs_method[0]=='searchAchievementContractList'){
    _cs_logs(array("查询个人业绩相关合同列表接口开始"));
    include_once 'vtlib/Vtiger/Module.php';
    include_once 'includes/main/WebUI.php';
    include_once  'languages/zh_cn/Accounts.php';
    $currentLanguage = 'zh_cn';
    //Vtiger_Language_Handler::getLanguage();//2.语言设置
    vglobal('current_language',$currentLanguage);

    global $adb;
    //_cs_logs(array("查询个人业绩相关合同列表接口参数:".json_encode($_REQUEST)));
    $contractids = $_GET['contractids'];

    $query_sql = "SELECT 
					vtiger_servicecontracts.servicecontractsid,
					vtiger_servicecontracts.sc_related_to AS accountid,
					vtiger_account.accountname,
					vtiger_servicecontracts.total,
					vtiger_servicecontracts.modulestatus,
					IFNULL(vtiger_activationcode.classtype,vtiger_servicecontracts.servicecontractstype) classtype,
					vtiger_servicecontracts.contract_type,
					vtiger_servicecontracts.signdate,
					DATE_FORMAT(vtiger_servicecontracts.signfor_date,'%Y-%m-%d') AS signfor_date,
					vtiger_servicecontracts.contract_no,
					account_crm.smownerid,
					vtiger_activationcode.usercodeid,
					IFNULL(vtiger_activationcode.expiredate,vtiger_servicecontracts.effectivetime) expiredate,
					IFNULL(vtiger_activationcode.comeformtyun,0) AS account_source
			  FROM vtiger_servicecontracts
			  INNER JOIN vtiger_crmentity ON vtiger_servicecontracts.servicecontractsid = vtiger_crmentity.crmid 
			  LEFT JOIN vtiger_activationcode ON (vtiger_activationcode.contractid=CONCAT(vtiger_servicecontracts.servicecontractsid) AND vtiger_activationcode.contractid IS NOT NULL)
			  LEFT JOIN vtiger_tyunstationsale ON (vtiger_tyunstationsale.contractid=vtiger_servicecontracts.servicecontractsid)
			  LEFT JOIN vtiger_account ON (vtiger_account.accountid=vtiger_servicecontracts.sc_related_to)
			  LEFT JOIN vtiger_crmentity account_crm ON vtiger_account.accountid = account_crm.crmid
			  WHERE vtiger_crmentity.deleted=0";

    if(!empty($contractids)){
        $contractids = rtrim($contractids,',');
        $query_sql .= " AND vtiger_servicecontracts.servicecontractsid IN({$contractids})";
		$query_sql .=" GROUP BY vtiger_servicecontracts.servicecontractsid";
    }else{
		$query_sql .=" GROUP BY vtiger_servicecontracts.servicecontractsid";
        $query_sql .= " LIMIT 10";
    }

    //_cs_logs(array("执行查询sql：".$query_sql));
    $result = $adb->pquery($query_sql, array());
    $rows = $adb->num_rows($result);
    $ret_lists = array();
    if ($rows>0) {
        while($row=$adb->fetchByAssoc($result)) {
            $lists = array();
            $lists['servicecontractsid']=$row['servicecontractsid'];
            $lists['accountid']=$row['accountid'];
            $lists['accountname']=$row['accountname'];
            $lists['total']=$row['total'];
            $lists['modulestatus']=$row['modulestatus'];
            $lists['classtype']=$row['classtype'];
            $lists['contract_type']=$row['contract_type'];
            $lists['signdate']=$row['signdate'];
            $lists['signfor_date']=$row['signfor_date'];
            $lists['contract_no']=$row['contract_no'];
            $lists['expiredate']=$row['expiredate'];
            $lists['account_source']=$row['account_source'];

            $servicecontractstype_name=$row['classtype'];
            if($row['classtype'] == 'buy'){
                $servicecontractstype_name = '首购';
            }else if($row['classtype'] == 'upgrade'){
                $servicecontractstype_name = '升级';
            }
            if($row['classtype'] == 'cupgrade'){
                $servicecontractstype_name = '迁移升级';
            }else if($row['classtype'] == 'renew'){
                $servicecontractstype_name = '续费';
            }else if($row['classtype'] == 'crenew'){
                $servicecontractstype_name = '迁移续费';
            }else if($row['classtype'] == 'againbuy'){
                $servicecontractstype_name = '另购';
            }else if($row['classtype'] == 'degrade'){
                $servicecontractstype_name = '降级';
            }else if($row['classtype'] == 'cdegrade'){
                $servicecontractstype_name = '迁移降级';
            }else {
				$servicecontractstype_name = $row['contract_type'];
			}
            $lists['servicecontractstype_name']=$servicecontractstype_name;
            //合同状态
            $lists['modulestatus_name']=vtranslate($row['modulestatus'],"ServiceContracts");

            $ret_lists[]=$lists;
        }
        $data=json_encode($ret_lists,JSON_UNESCAPED_UNICODE);
        $data=encrypt($data);
        echo json_encode(array("success"=>true,"is_multiple"=>true,"data"=>$data));
    }else{
        _cs_logs(array("没有查询个人业绩相关合同列表"));
        echo json_encode(array("success"=>true,"is_multiple"=>true,"total"=>0,"data"=>array()));
    }
    _cs_logs(array("查询个人业绩相关合同列表接口结束"));
    exit;
}

if(is_array($cs_method) && $cs_method[0]=='getContractInfo') {
    _cs_logs(array("获取合同信息接口开始"));
    global $adb;

    include_once 'vtlib/Vtiger/Module.php';
    include_once 'includes/main/WebUI.php';
    include_once  'languages/zh_cn/Accounts.php';
    $currentLanguage = 'zh_cn';
    //Vtiger_Language_Handler::getLanguage();//2.语言设置
    vglobal('current_language',$currentLanguage);

    _cs_logs(array("获取合同信息接口参数:".json_encode($_REQUEST)));
    $contractid = $_GET['contractid'];

    $query_sql = "SELECT 
            vtiger_servicecontracts.servicecontractsid,
			vtiger_servicecontracts.sc_related_to AS accountid,
			vtiger_servicecontracts.total,
			vtiger_servicecontracts.modulestatus,
			IFNULL(vtiger_activationcode.classtype,vtiger_servicecontracts.servicecontractstype) classtype,
			vtiger_servicecontracts.contract_type,
			vtiger_servicecontracts.signdate,
			DATE_FORMAT(vtiger_servicecontracts.signfor_date,'%Y-%m-%d') AS signfor_date,
			vtiger_servicecontracts.contract_no,
			account_crm.smownerid,
			IFNULL(vtiger_activationcode.productclass,'-1') AS productclass,
			vtiger_activationcode.usercode,
			vtiger_activationcode.usercodeid,
			IFNULL(vtiger_activationcode.expiredate,vtiger_servicecontracts.effectivetime) expiredate,
			IFNULL(vtiger_activationcode.comeformtyun,0) AS account_source
      FROM vtiger_servicecontracts
	  INNER JOIN vtiger_crmentity ON vtiger_servicecontracts.servicecontractsid = vtiger_crmentity.crmid 
      LEFT JOIN vtiger_activationcode ON (vtiger_activationcode.contractid=CONCAT(vtiger_servicecontracts.servicecontractsid) AND vtiger_activationcode.contractid IS NOT NULL)
	  LEFT JOIN vtiger_tyunstationsale ON (vtiger_tyunstationsale.contractid=vtiger_servicecontracts.servicecontractsid)
      LEFT JOIN vtiger_account ON (vtiger_account.accountid=vtiger_servicecontracts.sc_related_to)
	  INNER JOIN vtiger_crmentity account_crm ON vtiger_account.accountid = account_crm.crmid
      WHERE vtiger_crmentity.deleted=0 AND vtiger_servicecontracts.servicecontractsid={$contractid}";

    $query_sql .=" GROUP BY vtiger_activationcode.contractid";
    $result = $adb->pquery($query_sql, array());
    $rows = $adb->num_rows($result);
    $ret_lists = array();
    if ($rows>0) {
        $row = $adb->query_result_rowdata($result, 0);
        $servicecontractstype_name=$row['classtype'];
        if($row['classtype'] == 'buy'){
            $servicecontractstype_name = '首购';
        }
        if($row['classtype'] == 'upgrade'){
            $servicecontractstype_name = '升级';
        }
        if($row['classtype'] == 'cupgrade'){
            $servicecontractstype_name = '迁移升级';
        }
        if($row['classtype'] == 'renew'){
            $servicecontractstype_name = '续费';
        }
        if($row['classtype'] == 'crenew'){
            $servicecontractstype_name = '迁移续费';
        }
        if($row['classtype'] == 'againbuy'){
            $servicecontractstype_name = '另购';
        }
        if($row['classtype'] == 'degrade'){
            $servicecontractstype_name = '降级';
        }
        if($row['classtype'] == 'cdegrade'){
            $servicecontractstype_name = '迁移降级';
        }
		if($row['classtype'] == '新增'){
            $row['classtype'] = 'buy';
        }
		if($row['classtype'] == '续费'){
            $row['classtype'] = 'renew';
        }
        $row['servicecontractstype_name']=$servicecontractstype_name;

        //合同状态
        $row['modulestatus_name']=vtranslate($row['modulestatus'],"ServiceContracts");

        $data=json_encode($row,JSON_UNESCAPED_UNICODE);
        $data=encrypt($data);
        echo json_encode(array("success"=>true,"is_multiple"=>false,"data"=>$data));
    }
    _cs_logs(array("获取合同信息接口结束"));
    exit;
}

if(is_array($cs_method) && $cs_method[0]=='searchExpireAccountList'){
    _cs_logs(array("查询到期客户列表接口开始"));
    include_once 'vtlib/Vtiger/Module.php';
    include_once 'includes/main/WebUI.php';
    include_once('modules/Vtiger/models/Record.php');

    global $adb;
    _cs_logs(array("查询到期客户列表接口参数:".json_encode($_REQUEST)));
    $userIds = $_GET['userIds'];
    $startDate = $_GET['startDate'];
    $endDate = $_GET['endDate']." 23:59:59";

	$where_sql = '';
	if(!empty($userIds)){
        $userIds = rtrim($userIds,',');
        $where_sql = " AND T5.serviceid IN({$userIds})";
    }
    $query_sql = "SELECT 
				T1.customerid,
				T1.customername,
				T1.expiredate,
				T5.serviceid,
				T2.smownerid,
				T3.departmentid,
				T4.departmentname,
				T6.companyid,
				T1.contractid
				FROM (
				SELECT DISTINCT 
				IF(A.customerid='',B.customerid,A.customerid) as customerid,A.expiredate,A.contractid,
				IF(A.customername='',IFNULL(B.customername,'未指定'),IFNULL(A.customername,'未指定')) as customername
				FROM vtiger_activationcode A
				LEFT JOIN vtiger_activationcode B ON(A.buyid=B.activationcodeid)
				WHERE A.`status` IN(0,1) AND A.expiredate BETWEEN ? AND ?) T1
				INNER JOIN vtiger_crmentity T2 ON(cast(T1.customerid as SIGNED)=T2.crmid)
				LEFT JOIN vtiger_users T6 ON(T6.id=T2.smownerid)
				LEFT JOIN vtiger_user2department T3 ON(T2.smownerid=T3.userid)
				LEFT JOIN vtiger_departments T4 ON(T4.departmentid=T3.departmentid)
				LEFT JOIN vtiger_account T5 ON(cast(T1.customerid as SIGNED)=T5.accountid)
				WHERE 1=1 ";
	if(!empty($where_sql)){
		$query_sql.= $where_sql;
	}
	$query_sql .= " UNION ALL ";
	$query_sql .= " SELECT 
				T5.accountid customerid,
				T5.accountname customername,
				T1.effectivetime expiredate,
				T5.serviceid,
				T2.smownerid,
				T3.departmentid,
				T4.departmentname,
				T6.companyid,
				T1.servicecontractsid contractid
				FROM vtiger_servicecontracts T1
				INNER JOIN vtiger_crmentity T8 ON(T1.servicecontractsid=T8.crmid)
				LEFT JOIN vtiger_account T5 ON(T1.sc_related_to=T5.accountid)
				LEFT JOIN vtiger_crmentity T2 ON(T5.accountid=T2.crmid)
				LEFT JOIN vtiger_users T6 ON(T6.id=T2.smownerid)
				LEFT JOIN vtiger_user2department T3 ON(T2.smownerid=T3.userid)
				LEFT JOIN vtiger_departments T4 ON(T4.departmentid=T3.departmentid)
				WHERE T1.parent_contracttypeid!=2 and T8.deleted=0 and T1.modulestatus IN('c_complete') and T5.accountid IS NOT NULL AND T1.effectivetime BETWEEN ? AND ? ";
	if(!empty($where_sql)){
		$query_sql.= $where_sql;
	}

    _cs_logs(array("执行查询sql：".$query_sql));
    $result = $adb->pquery($query_sql, array($startDate,$endDate,$startDate,$endDate));
    $rows = $adb->num_rows($result);
    $ret_lists = array();
    if ($rows>0) {
        while($row=$adb->fetchByAssoc($result)) {
            $lists = array();
            //客户ID
            $lists['accountid']=$row['customerid'];
            $lists['accountname']=$row['customername'];
            $lists['expiredate']=$row['expiredate'];
            $lists['serviceid']=$row['serviceid'];
            $lists['smownerid']=$row['smownerid'];
            $lists['departmentid']=$row['departmentid'];
            $lists['departmentname']=$row['departmentname'];
            $lists['companyid']=$row['companyid'];
            $lists['contractid']=$row['contractid'];


            $ret_lists[]=$lists;
        }
        //_cs_logs(array("查询到期客户数量：".count($ret_lists)));
        $data=json_encode($ret_lists,JSON_UNESCAPED_UNICODE);
        _cs_logs(array("转json成功"));
        $data=encrypt($data);
        _cs_logs(array("加密成功"));
        echo json_encode(array("success"=>true,"is_multiple"=>true,"data"=>$data));
    }else{
        _cs_logs(array("没有查询到期客户列表"));
        echo json_encode(array("success"=>true,"is_multiple"=>true,"total"=>0,"data"=>array()));
    }
    _cs_logs(array("查询到期客户列表接口结束"));
    exit;
}
if(is_array($cs_method) && $cs_method[0]=='searchDepartmentsListByDepth'){
	global $adb;
	$depth = $_GET['depth'];
	$query_sql = " SELECT A.departmentid,
					A.departmentname,
					A.parentdepartment,
					(SELECT GROUP_CONCAT(departmentid) FROM vtiger_departments WHERE parentdepartment LIKE CONCAT(A.parentdepartment,'%')) as subdepartmentids
				FROM vtiger_departments A WHERE A.depth=4";
	$result = $adb->pquery($query_sql, array());
    $rows = $adb->num_rows($result);
    $ret_lists = array();
    if ($rows>0) {
        while($row=$adb->fetchByAssoc($result)) {
            $lists = array();
            $lists['departmentid']=$row['departmentid'];
            $lists['departmentname']=$row['departmentname'];
			$lists['parentdepartment']=$row['parentdepartment'];
			$lists['subdepartmentids']=$row['subdepartmentids'];
            $ret_lists[]=$lists;
        }
        $data=json_encode($ret_lists,JSON_UNESCAPED_UNICODE);
        $data=encrypt($data);
        echo json_encode(array("success"=>true,"is_multiple"=>true,"data"=>$data));
    }else{
        _cs_logs(array("没有查询到部门列表"));
        echo json_encode(array("success"=>true,"is_multiple"=>true,"total"=>0,"data"=>array()));
    }
    _cs_logs(array("查询部门列表接口结束"));
    exit;
}
if(is_array($cs_method) && $cs_method[0]=='searchCustomServiceDepartments'){
	global $adb;
	$depth = $_GET['depth'];
	$query_sql = "SELECT departmentid,parentdepartment,departmentname,SUBSTRING_INDEX(SUBSTRING_INDEX(parentdepartment,'::',-2),'::',1) AS parentid FROM vtiger_departments WHERE departmentname like '%客服%'";
	$result = $adb->pquery($query_sql, array());
    $rows = $adb->num_rows($result);
    $ret_lists = array();
    if ($rows>0) {
        while($row=$adb->fetchByAssoc($result)) {
            $lists = array();
            $lists['departmentid']=$row['departmentid'];
			$lists['parentdepartment']=$row['parentdepartment'];
            $lists['departmentname']=$row['departmentname'];
			$lists['parentid']=$row['parentid'];
            $ret_lists[]=$lists;
        }
        $data=json_encode($ret_lists,JSON_UNESCAPED_UNICODE);
        $data=encrypt($data);
        echo json_encode(array("success"=>true,"is_multiple"=>true,"data"=>$data));
    }else{
        _cs_logs(array("没有查询到客服部门列表"));
        echo json_encode(array("success"=>true,"is_multiple"=>true,"total"=>0,"data"=>array()));
    }
    _cs_logs(array("查询客服部门列表接口结束"));
    exit;
}
if(is_array($cs_method) && $cs_method[0]=='searchSubDepartmentsList'){
    _cs_logs(array("查询下级部门列表接口开始"));
    include_once 'vtlib/Vtiger/Module.php';
    include_once 'includes/main/WebUI.php';
    include_once('modules/Vtiger/models/Record.php');

    global $adb;
    _cs_logs(array("查询下级部门列表接口参数:".json_encode($_REQUEST)));
    $departmentids = $_GET['departmentid'];
	$region = $_GET['region'];
    $depth = $_GET['depth'];

    if(empty($departmentids)) {
        _cs_logs(array("没有查询下级部门列表"));
        echo json_encode(array("success"=>true,"is_multiple"=>true,"total"=>0,"data"=>array()));
        _cs_logs(array("查询下级部门列表接口结束"));
        exit;
    }

	if(!empty($region)){
		$query_sql = " SELECT departmentid,departmentname 
					  FROM vtiger_departments WHERE departmentname like '{$region}%' and depth={$depth} ";
	}else{
		$arr_departmentid = explode(";",$departmentids);
		$query_sql = '';
		if(count($arr_departmentid)>0) {
			for ($i = 0; $i < count($arr_departmentid); $i++) {
				if($query_sql == ''){
					$query_sql = " SELECT departmentid,departmentname 
					  FROM vtiger_departments WHERE FIND_IN_SET('{$arr_departmentid[$i]}',REPLACE(parentdepartment,'::',',')) and depth={$depth} ";
				}else{
					$query_sql .= " UNION ALL ";
					$query_sql .= " SELECT departmentid,departmentname 
					  FROM vtiger_departments WHERE FIND_IN_SET('{$arr_departmentid[$i]}',REPLACE(parentdepartment,'::',',')) and depth={$depth} ";
				}
			}
		}
		$query_sql="SELECT * FROM (".$query_sql.") T ORDER BY T.departmentname";
	}
	
    _cs_logs(array("执行查询sql：".$query_sql));
    $result = $adb->pquery($query_sql, array());
    $rows = $adb->num_rows($result);
    $ret_lists = array();
    if ($rows>0) {
        while($row=$adb->fetchByAssoc($result)) {
            $lists = array();
            $lists['departmentid']=$row['departmentid'];
            $lists['departmentname']=$row['departmentname'];
            $ret_lists[]=$lists;
        }
        $data=json_encode($ret_lists,JSON_UNESCAPED_UNICODE);
        $data=encrypt($data);
        echo json_encode(array("success"=>true,"is_multiple"=>true,"data"=>$data));
    }else{
        _cs_logs(array("没有查询下级部门列表"));
        echo json_encode(array("success"=>true,"is_multiple"=>true,"total"=>0,"data"=>array()));
    }
    _cs_logs(array("查询下级部门列表接口结束"));
    exit;
}
//=================增加驳回合同到CRMAPI================================================================
if(is_array($cs_method) && $cs_method[0]=='addRejectContractToCrm') {
    _cs_logs(array("增加驳回合同到CRM接口开始"));
    global $adb, $log;
    $reusltdata = $_REQUEST["data"];
    _cs_logs(array("增加驳回合同到CRM接口传入参数:".$reusltdata));
    $reusltdata = trim($reusltdata);
    $reusltdata = str_replace(' ', '+', $reusltdata);
    $decrdata = decrypt($reusltdata);
    $pad = strrpos($decrdata, '}');
    $decrdata = substr($decrdata, 0, $pad + 1);
    $decodedata = json_decode($decrdata, true);
    _cs_logs(array("增加驳回合同到CRM接口解析后参数:".json_encode($decodedata)));
    if (is_array($decodedata)) {

        $servicecontractsid = $decodedata['servicecontractid'];
        if (!empty($servicecontractsid) && is_numeric($servicecontractsid)) { 	
            $relationid = $decodedata['relationid'];
            $dataContact['servicecontractsid'] = $servicecontractsid;
            $dataContact['relationid'] = $relationid;
            $dataContact['rejectid'] = $decodedata['rejectid'];
            $dataContact['reason'] = $decodedata['reason'];

            $query_sql = "SELECT 1 FROM vtiger_servicecontract_relation WHERE relationid ={$relationid}";
            $result = $adb->pquery($query_sql, array());
            $rows = $adb->num_rows($result);

            if($rows>0){
                //已经存在不添加
                _cs_logs(array("已经推送过此驳回合同到CRM,不能重复推送"));
                $lists = array('success' => false, 'msg' => '已经推送过此驳回合同到CRM,不能重复推送!');
                echo json_encode($lists, JSON_UNESCAPED_UNICODE);
            }else{
                //追加联系人数据
                //_cs_logs(array("执行联系人表参数：".json_encode($dataContact)));
                $updateSql = "INSERT INTO vtiger_servicecontract_relation(servicecontractsid,relationid,rejectid,reason,rejecttime,status) VALUES (?,?,?,?,NOW(),0)";
                $adb->pquery($updateSql, array($dataContact));
                _cs_logs(array("增加驳回合同到CRM成功"));
                $lists = array('success' => true, 'msg' => '增加驳回合同到CRM成功!');
                echo json_encode($lists, JSON_UNESCAPED_UNICODE);
            }
        } else {
            //_cs_logs(array("合同id错误"));
            $lists = array('success' => false, 'msg' => '合同id错误');
            echo json_encode($lists, JSON_UNESCAPED_UNICODE);
        }
    } else {
        _cs_logs(array("增加驳回合同到CRM失败"));
        $lists = array('success' => false, 'msg' => '数据错误');
        echo json_encode($lists, JSON_UNESCAPED_UNICODE);
    }
    _cs_logs(array("增加驳回合同到CRM接口结束"));
    exit;
}
//=================获取客户已开票金额================================================================
if(is_array($cs_method) && $cs_method[0]=='searchAccountInvoiceList'){
    _cs_logs(array("获取客户已开票金额列表接口开始"));
    include_once 'vtlib/Vtiger/Module.php';
    include_once 'includes/main/WebUI.php';
    include_once('modules/Vtiger/models/Record.php');

    global $adb;

	$query_sql = "SELECT 
			vtiger_newinvoice.accountid,
			SUM(IFNULL(vtiger_newinvoiceextend.totalandtaxextend,0)) invoiceamount
			FROM `vtiger_newinvoiceextend` 
			LEFT JOIN vtiger_newinvoice ON vtiger_newinvoiceextend.invoiceid=vtiger_newinvoice.invoiceid 
			LEFT JOIN vtiger_crmentity AS invoicecrm ON invoicecrm.crmid=vtiger_newinvoice.invoiceid
			WHERE 
			invoicecrm.deleted=0
			AND vtiger_newinvoiceextend.deleted=0
			AND vtiger_newinvoice.modulestatus='c_complete'
			AND vtiger_newinvoiceextend.invoicestatus='normal'
			AND vtiger_newinvoice.accountid IS NOT NULL
			GROUP BY vtiger_newinvoice.accountid";
	
    $result = $adb->pquery($query_sql, array());
    $rows = $adb->num_rows($result);
    $ret_lists = array();
    if ($rows>0) {
        while($row=$adb->fetchByAssoc($result)) {
            $lists = array();
            $lists['accountid']=$row['accountid'];
            $lists['invoiceamount']=$row['invoiceamount'];
            $ret_lists[]=$lists;
        }
        $data=json_encode($ret_lists,JSON_UNESCAPED_UNICODE);
        $data=encrypt($data);
        echo json_encode(array("success"=>true,"is_multiple"=>true,"data"=>$data));
    }else{
        _cs_logs(array("没有客户已开票金额列表"));
        echo json_encode(array("success"=>true,"is_multiple"=>true,"total"=>0,"data"=>array()));
    }
    _cs_logs(array("查询客户已开票金额列表接口结束"));
    exit;
}
//=================查询客服拜访次数统计================================================================
if(is_array($cs_method) && $cs_method[0]=='searchVisitsCountList'){
    _cs_logs(array("查询客服拜访次数统计接口开始"));
    global $adb;
    $reusltdata = $_REQUEST["data"];
    _cs_logs(array("查询客服拜访次数统计接口参数:".$resultdata));
    $reusltdata = trim($reusltdata);
    $reusltdata = str_replace(' ', '+', $reusltdata);
    $decrdata = decrypt($reusltdata);
    $pad = strrpos($decrdata, '}');
    $decrdata = substr($decrdata, 0, $pad + 1);
    $decodedata = json_decode($decrdata, true);
    $userIds=$decodedata['userIds'];
	$userIds = rtrim($userIds,',');
	
	$query_sql = "SELECT A.extractid userid,count(visitingorderid) cnt FROM vtiger_visitingorder A
				LEFT JOIN vtiger_crmentity B ON (B.crmid=A.visitingorderid)
				WHERE B.deleted=0 AND modulestatus='c_complete'
				AND A.extractid IN({$userIds})
				AND A.issign=1
				GROUP BY A.extractid";
    $result = $adb->pquery($query_sql, array());
    $rows = $adb->num_rows($result);
    $ret_lists = array();
    if ($rows>0) {
        while($row=$adb->fetchByAssoc($result)) {
            $lists = array();
            $lists['userId']=$row['userid'];
            $lists['cnt']=$row['cnt'];
            $ret_lists[]=$lists;
        }
        $data=json_encode($ret_lists,JSON_UNESCAPED_UNICODE);
        $data=encrypt($data);
        echo json_encode(array("success"=>true,"is_multiple"=>true,"data"=>$data));
    }else{
        _cs_logs(array("没有查询客服拜访次数列表"));
        echo json_encode(array("success"=>true,"is_multiple"=>true,"total"=>0,"data"=>array()));
    }
    _cs_logs(array("查询客服拜访次数统计接口结束"));
    exit;
}
function splicingSpecialSql($loginId){
    //黄玉琴、李季、刘光翠、黄绥、许健康下单的订单，合同数据均在客服系统做隐藏；
	if($loginId == 1179 || $loginId == '1179' || $loginId == 43 || $loginId == '43'){
        //黄玉琴、许健康登录,不限制
    }else if($loginId == 2824 || $loginId == '2824'){
        //李季登录
        return " AND ((vtiger_activationcode.classtype IN('buy','renew','crenew') AND vtiger_activationcode.creator NOT IN(1179,7871,1734,43))
						OR (vtiger_activationcode.classtype IN('degrade','cdegrade') AND vtiger_activationcode.creator NOT IN(1179,7871,1734,43)))
						OR vtiger_activationcode.creator IS NULL
						OR vtiger_activationcode.classtype IN('upgrade','cupgrade','againbuy'))";
	}else if($loginId == 1734 || $loginId == '1734'){
        //刘光翠登录
        return " AND ((vtiger_activationcode.classtype IN('buy','renew','crenew') AND vtiger_activationcode.creator NOT IN(1179,7871,43))
						OR (vtiger_activationcode.classtype IN('degrade','cdegrade') AND vtiger_activationcode.creator NOT IN(1179,7871,43)))
						OR vtiger_activationcode.creator IS NULL
						OR vtiger_activationcode.classtype IN('upgrade','cupgrade','againbuy'))";	
    }else{
        //首购、续费：黄玉琴、李季下单的订单，合同数据均在客服系统做隐藏；
        //升级：客服不可以代商务下单
        //降级：黄玉琴的下单数据客服系统做隐藏，李季的下单数据不在客服系统做隐藏；
        return " AND ((vtiger_activationcode.classtype IN('buy','renew','crenew') AND vtiger_activationcode.creator NOT IN(1179,2824,7871,1734,43))
						OR (vtiger_activationcode.classtype IN('degrade','cdegrade') AND vtiger_activationcode.creator NOT IN(1179,7871,1734,43))
						OR vtiger_activationcode.creator IS NULL
						OR vtiger_activationcode.classtype IN('upgrade','cupgrade','againbuy'))";
    }
    return "";
}
/**
 * 根据客户id获取T云账号
 * @param $accountId
 * @return array|string
 */
function getTyunAccounts($accountId){
    _cs_logs(array("获取T云账号接口传入参数:".$accountId));
    global $adb;
    $queryTyunAccunt = "SELECT DISTINCT usercode as tyunaccunt FROM vtiger_activationcode WHERE classtype='buy' AND `status` IN(0,1) AND usercode IS NOT NULL AND usercode !='' AND customerid='{$accountId}'
                        UNION ALL
                        SELECT DISTINCT loginname as tyunaccunt FROM vtiger_tyunstationsale WHERE classtype='buy' AND `status` IN(0,1) AND loginname IS NOT NULL AND loginname !='' AND accountid='{$accountId}'";
    //_cs_logs(array("获取T云账号接口sql:".$queryTyunAccunt));
    $result = $adb->pquery($queryTyunAccunt, array());
    $rows = $adb->num_rows($result);
    $lists = array();
    if ($rows>0) {
        while($row=$adb->fetchByAssoc($result)) {
            $lists[]=$row['tyunaccunt'];
        }
        //_cs_logs(array("获取T云账号数据:",json_encode($lists)));
        //_cs_logs(array("获取T云账号接口返回:".implode(",", $lists)));
        return implode(",", $lists);
    }else{
        _cs_logs(array("没有获取到对应的T云账号"));
        return "";
    }
}
/**
 * 根据客户id获取T云来源
 * @param $accountId
 * @return 来源(1:T云web版,0:T云客户端)
 */
function getTyunAccountSource($accountId){
    _cs_logs(array("获取T云客户来源接口传入参数:".$accountId));
    global $adb;
    //$queryTyunAccunt = "SELECT IFNULL(comeformtyun,0) as account_source FROM vtiger_activationcode WHERE classtype='buy' AND `status` IN(0,1) AND customerid=? ORDER BY adddate DESC LIMIT 1";
    $queryTyunAccunt = "SELECT 1 as account_source FROM vtiger_activationcode WHERE `status` IN(0,1) AND customerid=? AND comeformtyun=1 LIMIT 1";
    _cs_logs(array("获取T云客户来源接口sql:".$queryTyunAccunt));
    $result = $adb->pquery($queryTyunAccunt, array($accountId));
    $rows = $adb->num_rows($result);
    _cs_logs(array("获取T云客户来源接口件数:".$rows));
    if ($rows>0) {
        $row = $adb->query_result_rowdata($result);
        //_cs_logs(array("获取T云客户来源数据:".json_encode($row)));
        //return intval($row['account_source']);
        return 1;
    } else{
        _cs_logs(array("没有获取到对应的客户来源"));
        return 0;
    }
}
/**
 * 获取客服分配id
 * @param $arr
 * @return int|null|string|string[]
 * @throws Exception
 */
function getAllotServicecommentsId($arr){
    global $adb;
    $sql = "SELECT servicecommentsid FROM vtiger_servicecomments WHERE assigntype='accountby' AND related_to=?";
    $querysql = $adb->pquery($sql, array($arr['related_to']));
    if ($adb->num_rows($querysql)>0) {
        return $adb->query_result($querysql, 0 ,'servicecommentsid');
    }else{
        return 0;
    }
}
/**
 * @param $accountid 客户id 获取保护频率
 * @return int
 * User/Date: adatian/20150701
 * @throws Exception
 */
function getFollowfrequencyDay($accountid){
    global $adb,$log;
    $sql = "SELECT
                        followfrequency
                    FROM
                        vtiger_followfrequency
                    WHERE
                        vtiger_followfrequency.accountrank = (
                           SELECT accountrank FROM vtiger_account WHERE accountid= ?
                          )";
    $querysql = $adb->pquery($sql, array($accountid));
    if ($adb->num_rows($querysql)>0) {
        $followfrequency = $adb->query_result($querysql, 0 ,'followfrequency');
    }else{
        $followfrequency = 31;
    }
    return $followfrequency;

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
    $dir	= './logs/cs/' . $year . '/' . $month . '/';
    if(!is_dir($dir)) {
        mkdir($dir,0755,true);
    }
    $file = $dir . $file . date('Y-m-d').'.txt';
    @file_put_contents($file, '----------------' . date('H:i:s') . '--------------------'.PHP_EOL.var_export($data,true).PHP_EOL, FILE_APPEND);
}
/**
* @param $url
* @param null $data
* @param array $curlset
 * @return bool|string
 */
function https_request($url, $data = null,$curlset=array()){
        $curl = curl_init();
        if(!empty($curlset)){
            foreach($curlset as $key=>$value){
                curl_setopt($curl, $key, $value);
            }
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);//post提交方式
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
}

?>
