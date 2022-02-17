<?php
/*
 * 接口
 * authkey：产品ID
 * limitday：数据时间
 * 按照产品查询已完成的工单
 * 
 */

header("Content-type:text/html;charset=utf-8");
error_reporting(0);
require('include/utils/UserInfoUtil.php');
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

if(isset($_GET['productcache'])){
	global $adb;
	$productsinfo=array();
	$procudts=$adb->pquery('select productid,productname from vtiger_products');
	while($row=$adb->fetchByAssoc($procudts)){
		echo '产品 '.$row['productname'].' ID '.$row['productid'].'<hr>';
		$productsinfo[]=$row['productid']."=>'".$row['productname']."'";
	}
	$handle=@fopen($root_directory.'crmcache/productsinfo.php',"w+");
		if($handle){
			$newbuf ="<?php\n\n\$productsinfo=array(";	
			$newbuf .= implode(',',$productsinfo).");\n";
			$newbuf .= "?>";
			fputs($handle, $newbuf);
			fclose($handle);
		}
	exit;
}

if(isset($_GET['userlist'])){

	if((time()-filemtime($root_directory.'crmcache/userlist.php'))>86400){
		global $adb;
		$userlist=$adb->pquery('SELECT id  as userid ,user_name as name,status,last_name as username,u.email1 as email,d.departmentname,d.departmentid,r.roleid,r.rolename FROM `vtiger_users` as u LEFT JOIN vtiger_user2role as ur on ur.userid=u.id LEFT JOIN vtiger_role as r on r.roleid=ur.roleid LEFT JOIN vtiger_user2department as ud on ud.userid=u.id LEFT JOIN vtiger_departments as d on d.departmentid=ud.departmentid where u.id>1');
		while($row=$adb->fetchByAssoc($userlist)){
			$user[$row['userid']]=$row;
		}
		$handle=@fopen($root_directory.'crmcache/userlist.php',"w+");
		$userlist=json_encode($user);
		if($handle){
			$newbuf ="<?php\n\$userlist='".$userlist."';\n?>";
			fputs($handle, $newbuf);
			fclose($handle);
		}	
	}else{
		include $root_directory.'crmcache/userlist.php';
	}
	if(isset($_GET['type'])){
		print_r(json_decode($userlist,true));
		exit;
	}
	echo $userlist;
	exit;
}


if(!empty($_GET['register']) && !empty($_GET['productid'])){
	//获取请求产品的key
	//echo $_SERVER['REMOTE_ADDR'];
	echo urlencode(cookiecode($_GET['productid'],''));
}elseif(!empty($_GET['authkey'])){
	$productid=explode(',',urldecode(cookiecode($_GET['authkey'],'DECODE')));
	//error_reporting(E_ALL);
	include 'crmcache/productsinfo.php';
	include 'crmcache/user2departmentname.php';
	include 'crmcache/usercache.php';
	if(is_array($productid) && !empty($productsinfo)){
		if(!empty($_GET['starttime'])){
			$starttime=($_GET['starttime']<1441015200)?1441015200:$_GET['starttime'];
			$date=date('Y-m-d H',$starttime);
		}else{
			$date=date('Y-m-d');
		}
		//$date='2015-08-17 16:35';
		global $adb;
		$fields=array();
        //$sales=$adb->pquery('SELECT servicecontractsid,salesorderid,productid,createtime FROM `vtiger_salesorderproductsrel` where servicecontractsid !=\'\' and  salesorderid !=\'\' and productid=? and createtime>=?',array(362134,'2015-04-03'));
		//$sql='SELECT p.FormInput,p.SalesOrderId,p.Tplid,p.Productid,f.field,s.workflowstime,c.smownerid,w.higherid FROM `vtiger_salesorder_productdetail` as p LEFT JOIN vtiger_salesorder as s on s.salesorderid=p.SalesOrderId LEFT JOIN vtiger_formdesign as f on f.formid=p.TplId LEFT JOIN vtiger_crmentity as c on p.SalesOrderId=c.crmid LEFT JOIN vtiger_salesorderworkflowstages as w on w.salesorderid=p.SalesOrderId WHERE p.Productid in ('.generateQuestionMarks($productid).') and w.workflowstagesid=0 and s.modulestatus=\'c_complete\' AND s.workflowstime >?';
		//更新为第二个节点审核完成后抓取数据
        $sql='SELECT p.FormInput,p.SalesOrderId,p.Tplid,p.Productid,f.field,s.workflowstime,c.smownerid,w.higherid FROM `vtiger_salesorder_productdetail` as p LEFT JOIN vtiger_salesorder as s on s.salesorderid=p.SalesOrderId LEFT JOIN vtiger_formdesign as f on f.formid=p.TplId LEFT JOIN vtiger_crmentity as c on p.SalesOrderId=c.crmid LEFT JOIN vtiger_salesorderworkflowstages as w on w.salesorderid=p.SalesOrderId WHERE p.Productid in ('.generateQuestionMarks($productid).') and w.workflowstagesid=0 and s.googlestatus=1 and s.modulestatus<>\'c_cancel\' AND s.workflowstime >?';
        $productid[]=$date;
		$sales=$adb->pquery($sql,$productid);
		$rows=$adb->num_rows($sales);
		if($rows){
			$lists=array();
			while($row=$adb->fetchByAssoc($sales)){
				if(empty($fields[$row['tplid']])){
					$fields[$row['tplid']]=json_decode(str_replace('&quot;','"', $row['field']),true);
				}
				$input=json_decode(str_replace('&quot;','"', $row['forminput']),true);
				foreach($fields[$row['tplid']] as $fieldlist){
					if($fieldlist['type']=='checkebox-inline' || $fieldlist['type']=='checkebox-noinline'){
						$array=array('name'=>$fieldlist['title'],'value'=>explode('##',$input[$fieldlist['name']]),'type'=>'array');
					}elseif($fieldlist['type']=='listctrl'){
						$list=json_decode($input[$fieldlist['name']],true);
						if(!empty($list)){
							foreach($list as $k=> $l){
								$list[$k]=explode('##', $l);
							}
						}
						$array=array('name'=>$fieldlist['listname'],'value'=>$list,'type'=>'list','list'=>explode('##',$fieldlist['title']));
					}else{
						$array=array('name'=>$fieldlist['title'],'value'=>$input[$fieldlist['name']],'type'=>'strig');	
					}
					$name=trim($fieldlist['name']);
					$input[$name]=$array;	
				}
				$input['salesorderid']=$row['salesorderid'];
				$input['productname']=$productsinfo[$row['productid']];
				$input['productid']=$row['productid'];
				$input['serviceid']=$row['higherid'];
				$input['departmentname']=$user2departmentname[$row['smownerid']];
				$input['servicename']=$usercache[$row['higherid']][0];
				$input['repaytime']=$row['workflowstime'];
				
				$lists[]=$input; 
			}
			if(!empty($_GET['array'])){
				print_r($lists);
			}else{
				echo json_encode($lists);
			}	
		}
	}
    exit;
}
if(!empty($_GET['tokenauth'])){
    $productid=explode(',',urldecode(cookiecode($_GET['tokenauth'],'DECODE')));
    //844b9OqhxuC4C%2FVWSRiciD5UVv9eAP7pwt8PAzTL%2FlmuPkHw3A
   // b921iy9lnnf9YrOs%2B9OrpYNTbpFPt73zsVOOR%2Bn9a46%2FcreiNg
    if(is_array($productid) && $productid[0]=='crmprint'){
        global $adb;
        $sql='SELECT vtiger_servicecontracts_print.servicecontractsprintid,vtiger_servicecontracts_print.servicecontracts_no,vtiger_servicecontracts_print.contract_template,vtiger_company_code.numbered_accounts,vtiger_company_code.bank_account,vtiger_company_code.company_code,vtiger_company_code.address,vtiger_company_code.companyfullname,vtiger_company_code.companyname,vtiger_company_code.tax,vtiger_company_code.telphone,vtiger_company_code.website,vtiger_company_code.email,vtiger_company_code.zipcode,vtiger_company_code.taxnumber FROM vtiger_servicecontracts_print LEFT JOIN vtiger_company_code ON vtiger_servicecontracts_print.company_code=vtiger_company_code.company_code WHERE constractsstatus=\'c_generated\'';
        $sales=$adb->pquery($sql,array());
        $rows=$adb->num_rows($sales);
        if($rows){
            $lists=array();
            while($row=$adb->fetchByAssoc($sales)){

                $input['servicecontractsid']=urlencode(cookiecode($row['servicecontractsprintid'],''));
                $input['scpid']=$row['servicecontractsprintid'].'-8';
                $input['servicecontracts_no']=$row['servicecontracts_no']==null?'':$row['servicecontracts_no'];
                $input['contract_template']=$row['contract_template']==null?'':$row['contract_template'];
                $input['company_code']=$row['company_code']==null?'':$row['company_code'];
                $input['address']=$row['address']==null?'':$row['address'];
                $input['companyfullname']=$row['companyfullname']==null?'':$row['companyfullname'];
                $input['companyname']=$row['companyname']==null?'':$row['companyname'];
                $input['fax']=$row['tax']==null?'':$row['tax'];
                $input['telphone']=$row['telphone']==null?'':$row['telphone'];
                $input['numbered_accounts']=$row['numbered_accounts']==null?'':$row['numbered_accounts'];
                $input['bank_account']=$row['bank_account']==null?'':$row['bank_account'];
                $input['taxnumber'] = $row['taxnumber'] == null ? '' : $row['taxnumber'];
                $input['website']=$row['website']==null?'':$row['website'];
                $input['email']=$row['email']==null?'':$row['email'];
                $input['zipcode']=$row['zipcode']==null?'':$row['zipcode'];
                $lists[]=$input;
            }
            if(!empty($_GET['array'])){
                print_r($lists);
            }else{
                echo json_encode($lists);
            }
        }else{
            $lists=array('success'=>false);
            echo json_encode($lists);
            exit;
        }
    }elseif(is_array($productid) && $productid[0]=='crmprintsign'){  //批量修改
        global $adb;
        $datetime=dae('Y-m-d H:i:s');
        $sql='UPDATE vtiger_servicecontracts_print SET constractsstatus=\'c_print\',printtime=\''.$datetime.'\' WHERE constractsstatus=\'c_generated\'';
        $adb->pquery($sql,array());
        $lists=array('success'=>true);
        echo json_encode($lists);
        exit;
    }elseif(is_array($productid) && $productid[0]=='crmdepart'){  //部门
        global $adb;
        $sql='SELECT company_code,companyname FROM `vtiger_company_code`';
        $sales=$adb->pquery($sql,array());
        $rows=$adb->num_rows($sales);
        if($rows){
            $lists=array();
            while($row=$adb->fetchByAssoc($sales)){

                $input['company_code']=$row['company_code']==null?'':$row['company_code'];
                $input['companyname']=$row['companyname']==null?'':$row['companyname'];
                $lists[]=$input;
            }
            if(!empty($_GET['array'])){
                print_r($lists);
            }else{
                echo json_encode($lists);
            }
        }else{
            $lists=array('success'=>false);
            echo json_encode($lists);

        }
        exit;
    }elseif(is_array($productid) && $productid[0]=='crmsearch') {
        if (empty($_GET['company']) && empty($_GET['template'])) {
            $lists=array('success'=>false);
            echo json_encode($lists);
            exit;
        }
        $arr = array();
        $sql = 'SELECT vtiger_servicecontracts_print.servicecontractsprintid,vtiger_servicecontracts_print.servicecontracts_no,vtiger_servicecontracts_print.contract_template,vtiger_company_code.numbered_accounts,vtiger_company_code.bank_account,vtiger_company_code.company_code,vtiger_company_code.address,vtiger_company_code.companyfullname,vtiger_company_code.companyname,vtiger_company_code.tax,vtiger_company_code.telphone,vtiger_company_code.website,vtiger_company_code.email,vtiger_company_code.zipcode,vtiger_servicecontracts_print.contract_template,vtiger_company_code.taxnumber FROM vtiger_servicecontracts_print LEFT JOIN vtiger_company_code ON vtiger_servicecontracts_print.company_code=vtiger_company_code.company_code WHERE vtiger_servicecontracts_print.constractsstatus=\'c_generated\'';
        if (!empty($_GET['company'])) {
            $arr[] = $_GET['company'];
            $sql .= ' and vtiger_servicecontracts_print.company_code=?';
        }

        if (!empty($_GET['template'])) {
            $arr[] = $_GET['template'];
            $sql .= ' and vtiger_servicecontracts_print.contract_template=?';
        }
        if (!empty($_GET['userid'])) {
            $arr[] = $_GET['userid'];
            $sql .= ' and vtiger_servicecontracts_print.smownerid=?';
        }
        global $adb;

        $sales = $adb->pquery($sql, array($arr));
        $rows = $adb->num_rows($sales);
        if ($rows) {
            $lists = array();
            while ($row = $adb->fetchByAssoc($sales)) {
                $input['servicecontractsid']=urlencode(cookiecode($row['servicecontractsprintid'],''));
                $input['scpid']=$row['servicecontractsprintid'].'-8';
                $input['servicecontracts_no'] = $row['servicecontracts_no'] == null ? '' : $row['servicecontracts_no'];
                $input['contract_template'] = $row['contract_template'] == null ? '' : $row['contract_template'];
                $input['company_code'] = $row['company_code'] == null ? '' : $row['company_code'];
                $input['address'] = $row['address'] == null ? '' : $row['address'];
                $input['companyfullname'] = $row['companyfullname'] == null ? '' : $row['companyfullname'];
                $input['companyname'] = $row['companyname'] == null ? '' : $row['companyname'];
                $input['fax'] = $row['tax'] == null ? '' : $row['tax'];
                $input['telphone'] = $row['telphone'] == null ? '' : $row['telphone'];
                $input['numbered_accounts'] = $row['numbered_accounts'] == null ? '' : $row['numbered_accounts'];
                $input['bank_account'] = $row['bank_account'] == null ? '' : $row['bank_account'];
                $input['taxnumber'] = $row['taxnumber'] == null ? '' : $row['taxnumber'];
                $input['website'] = $row['website'] == null ? '' : $row['website'];
                $input['email'] = $row['email'] == null ? '' : $row['email'];
                $input['zipcode'] = $row['zipcode'] == null ? '' : $row['zipcode'];
                $lists[] = $input;
            }
            if (!empty($_GET['array'])) {
                print_r($lists);
            } else {
                echo json_encode($lists);
            }
        }else{
            $lists=array('success'=>false);
            echo json_encode($lists);
        }
    }elseif(is_array($productid) && $productid[0]=='crmpsiglesign'){  //单笔记录修改
        global $adb;
        $servicecontractsprintid=explode(',',urldecode(cookiecode($_GET['servicecontact'],'DECODE')));
        if(is_array($servicecontractsprintid) && is_numeric($servicecontractsprintid[0])){
            $datetime=date('Y-m-d H:i:s');
            $sql='UPDATE vtiger_servicecontracts_print SET constractsstatus=\'c_print\',printnum=printnum+1,printer=?,printtime=\''.$datetime.'\',template_version=(select version from vtiger_contract_template where vtiger_contract_template.contract_template = vtiger_servicecontracts_print.contract_template limit 1) WHERE constractsstatus=\'c_generated\' AND servicecontractsprintid=?';
            $adb->pquery($sql,array($_GET['userid'],$servicecontractsprintid[0]));
            $lists=array('success'=>true);
            echo json_encode($lists);
        }else{
            $lists=array('success'=>false);
            echo json_encode($lists);
        }

    }elseif(is_array($productid) && $productid[0]=='crmproduct'){  //产品列表
        global $adb;
        $sql='SELECT contract_template,ctname FROM vtiger_contract_template';

        $sales = $adb->pquery($sql, array());
        $rows = $adb->num_rows($sales);
        if ($rows) {
            $lists = array();
            include "languages/zh_cn/SContractNoGeneration.php";
            while ($row = $adb->fetchByAssoc($sales)) {
                $productname=$languageStrings[$row['contract_template']];
                $input['productscode'] = $row['contract_template'] == null ? '' :$row['contract_template'];
                //$input['productsname'] = empty($productname)?$row['contract_template']:$productname;
                $input['productsname'] = $row['ctname'];
                if(!empty($row['ctname'])){
                    $lists[] = $input;
                }

            }
            if (!empty($_GET['array'])) {
                print_r($lists);
            } else {
                echo json_encode($lists);
            }
        }else{
            $lists=array('success'=>false);
            echo json_encode($lists);
        }
    }elseif(is_array($productid) && $productid[0]=='checkUser')
    {
        global $adb;
        $user_name=$_GET['username'];
        $user_password=$_GET['password'];
        $password=encrypt_password($user_name,$user_password, 'PHP5.3MD5');
        $sql='SELECT id,last_name FROM vtiger_users WHERE `status`=\'Active\' AND user_name=? AND user_password=?';
        //$sql='SELECT id,last_name FROM vtiger_users WHERE `status`=\'Active\'';

        $sales = $adb->pquery($sql, array($user_name,$password));
        $rows = $adb->num_rows($sales);
        if ($rows) {
            $lists = array();
            $result=$adb->query_result_rowdata($sales,0);
            //echo json_encode(array('id'=>$result['id']));
            echo $result['id'];
        }else{
            /*$lists=array('success'=>false);
            echo json_encode($lists);*/
            echo 0;
        }
    }
    elseif(is_array($productid) && $productid[0]=='checktpl')
    {
        global $adb;
        $tpl=$_GET['tpl'];
        $sql='SELECT contract_template as tpl,`version` as vsn,extensionname FROM `vtiger_contract_template`';
        if(isset($tpl) && !empty($tpl))
        {
            $sql.="where contract_template='{$tpl}'";
        }
        //$sql='SELECT id,last_name FROM vtiger_users WHERE `status`=\'Active\'';

        $sales = $adb->pquery($sql, array());
        $rows = $adb->num_rows($sales);
        if ($rows) {
            $lists = array();
            while ($row = $adb->fetchByAssoc($sales)) {
                $input['tpl'] = $row['tpl'] == null ? '' :$row['tpl'];
                $input['vsn'] = $row['vsn'] == null ? '' :$row['vsn'];
                $input['extname'] = $row['extensionname'];
                $lists[] = $input;
            }
            if (!empty($_GET['array'])) {
                print_r($lists);
            } else {
                echo json_encode($lists);
            }
        }else{
            $lists=array('success'=>false);
            echo json_encode($lists);

        }
    }
    elseif(is_array($productid) && $productid[0]=='gettpl')
    {
        global $adb;
        $tpl=$_GET['tpl'];
        $sql='SELECT contract_template as tpl,templatecontents as contents FROM `vtiger_templatecontent` WHERE contract_template=?';
        //$sql='SELECT contract_template as tpl,templatecontents as contents FROM `vtiger_templatecontent`';


        $sales = $adb->pquery($sql, array($tpl));
        //$sales = $adb->pquery($sql, array());
        $rows = $adb->num_rows($sales);
        if ($rows) {
            $lists = array();
            $arr=$adb->query_result_rowdata($sales);
            $lists['tpl']=$arr['tpl'];
            $lists['contents']=$arr['contents'];
            /*while ($row = $adb->fetchByAssoc($sales)) {
                $input['tpl'] = $row['tpl'];
                $input['contents'] = $row['contents'];
                $lists[] = $input;
            }*/
            if (!empty($_GET['array'])) {
                print_r($lists);
            } else {
                echo json_encode($lists);
            }
        }else{
            $lists=array();
            echo json_encode($lists);

        }
    }elseif(is_array($productid) && $productid[0]=='getaccount'){
        include_once 'vtlib/Vtiger/Module.php';
        include_once 'includes/main/WebUI.php';
        include_once  'languages/zh_cn/Accounts.php';
        vglobal('default_language', $default_language);
        $currentLanguage = 'zh_cn';
        //Vtiger_Language_Handler::getLanguage();//2.语言设置
        vglobal('current_language',$currentLanguage);
        global $adb;
        $accountid=$_GET['accountid'];
        $query="SELECT 
                    vtiger_account.accountname,
                    IF(vtiger_account.protected=1,'是','否') as protected,
                    vtiger_account.servicetype,IF(vtiger_account.sign=1,'是','否') as sign,
                    vtiger_account.advancesmoney,IF(vtiger_account.groupbuyaccount=1,'是','否') as groupbuyaccount,
                    IFNULL((select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_crmentity.smownerid=vtiger_users.id),'--') as smownerid,
                    vtiger_crmentity.smownerid as smownerid_owner,
                    vtiger_account.accountrank,
                    vtiger_account.linkname,
                    vtiger_account.mobile,
                    vtiger_account.phone,
                    vtiger_account.website,
                    vtiger_account.fax,
                    vtiger_account.email1 AS email,
                    vtiger_account.industry,
                    vtiger_account.annual_revenue,
                    vtiger_account.address,
                    vtiger_account.makedecision,
                    vtiger_account.gender,
                    vtiger_account.country,
                    vtiger_account.business,
                    vtiger_account.regionalpartition,
                    vtiger_account.makedecision,
                    vtiger_account.customertype,
                    vtiger_account.title,
                    vtiger_account.leadsource,
                    vtiger_account.businessarea,
                    (select createdtime from vtiger_crmentity where vtiger_crmentity.crmid=vtiger_account.accountid and vtiger_crmentity.deleted=0) as createdtime,
                    (select modifiedtime from vtiger_crmentity where vtiger_crmentity.crmid=vtiger_account.accountid and vtiger_crmentity.deleted=0) as modifiedtime, 
                    IFNULL((select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_servicecomments.serviceid=vtiger_users.id),'--') as serviceid,
                    vtiger_servicecomments.serviceid as serviceid_reference,
                    (select description from vtiger_crmentity where vtiger_crmentity.crmid=vtiger_account.accountid and vtiger_crmentity.deleted=0) as description,
                     IFNULL((select label from vtiger_crmentity where crmid=vtiger_account.parentid),'--') as parentid,
                    vtiger_account.customerproperty,
                    vtiger_account.account_no,
                    vtiger_account.lastfollowuptime,
                    vtiger_account.saleorderlastdealtime,
                    vtiger_account.protectday,
                    vtiger_account.accountcategory,
                    vtiger_account.visitingtimes,
                    IF(vtiger_account.frommarketing=1,'是','否') as frommarketing,
                    vtiger_account.accountid 
                    FROM vtiger_account 
                    LEFT JOIN vtiger_crmentity ON vtiger_account.accountid = vtiger_crmentity.crmid 
                    LEFT JOIN vtiger_servicecomments ON (vtiger_account.accountid = vtiger_servicecomments.related_to and vtiger_servicecomments.assigntype = 'accountby' and vtiger_servicecomments.related_to>0) 
                    WHERE 
                    1=1 
                    and vtiger_crmentity.deleted=0 
                    AND vtiger_account.accountid=?";
        $result = $adb->pquery($query, array($accountid));

        $rows = $adb->num_rows($result);
        if ($rows>0) {
            $lists = array();

            $arr=$adb->query_result_rowdata($result,0);

            $lists['success']=true;
            $lists['accountname']=$arr['accountname'];
            $lists['accountcategory']=vtranslate($arr['accountcategory'],"Accounts");
            $lists['customertype']=$arr['customertype'];
            $lists['customertype_name']=vtranslate($arr['customertype'],"Accounts");
            $lists['accountrank']=$arr['accountrank'];
            $lists['accountrank_name']=vtranslate($arr['accountrank'],"Accounts");
            $lists['smownerid']=$arr['smownerid'];
            $lists['smownerid_owner']=$arr['smownerid_owner'];
            $lists['linkname']=$arr['linkname'];
            $lists['title']=$arr['title'];
            $lists['gendertype']=$arr['gendertype'];
            $lists['gendertype_name']=vtranslate($arr['gendertype'],"Accounts");
            $lists['makedecisiontype']=$arr['makedecisiontype'];
            $lists['makedecisiontype_name']=vtranslate($arr['makedecisiontype'],"Accounts");
            $lists['email']=$arr['email'];
            $lists['mobile']=$arr['mobile'];
            $lists['phone']=$arr['phone'];
            $lists['website']=$arr['website'];
            $lists['address']=$arr['address'];

            $query="SELECT 
                        vtiger_contactdetails.salutation,
                        vtiger_contactdetails.contact_no, 
                        vtiger_contactdetails.phone, 
                        vtiger_contactdetails.mobile, 
                        vtiger_contactdetails.accountid, 
                        vtiger_contactsubdetails.homephone, 
                        vtiger_contactsubdetails.otherphone, 
                        vtiger_contactdetails.title, 
                        vtiger_contactdetails.fax, 
                        vtiger_contactdetails.department, 
                        vtiger_contactsubdetails.birthday, 
                        vtiger_contactdetails.email, 
                        vtiger_contactsubdetails.assistant, 
                        vtiger_contactdetails.secondaryemail, 
                        vtiger_contactsubdetails.assistantphone, 
                        vtiger_contactdetails.donotcall, 
                        vtiger_contactdetails.emailoptout,
                        IFNULL((select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_crmentity.smownerid=vtiger_users.id),'--') as smownerid, 
                        vtiger_contactdetails.name,
                        vtiger_contactdetails.gender, 
                        vtiger_contactdetails.makedecision, 
                        vtiger_contactdetails.weixin, 
                        vtiger_contactdetails.qq,
                        vtiger_contactdetails.contactid 
                    FROM vtiger_contactdetails 
                    INNER JOIN vtiger_crmentity ON vtiger_contactdetails.contactid = vtiger_crmentity.crmid 
                    INNER JOIN vtiger_contactsubdetails ON vtiger_contactdetails.contactid = vtiger_contactsubdetails.contactsubscriptionid 
                    LEFT JOIN vtiger_users AS vtiger_usersassigned_user_id ON vtiger_crmentity.smownerid = vtiger_usersassigned_user_id.id 
                    LEFT JOIN vtiger_users AS vtiger_usersmodifiedby ON vtiger_crmentity.modifiedby = vtiger_usersmodifiedby.id 
                    WHERE vtiger_crmentity.deleted=0 AND vtiger_contactdetails.accountid=?";
            $result=$adb->pquery($query,array($accountid));
            $arrayt=array();

            while ($row = $adb->fetchByAssoc($result)) {
                $arrayt[$row['contactid']]['name'] = $row['name'];
                $arrayt[$row['contactid']]['gendertype'] = $row['gendertype'];
                $arrayt[$row['contactid']]['gendertype_name'] = vtranslate($row['gendertype'],"Contacts");
                $arrayt[$row['contactid']]['mobile'] = $row['mobile'];
                $arrayt[$row['contactid']]['phone'] = $row['phone'];
                $arrayt[$row['contactid']]['title'] = $row['title'];
                $arrayt[$row['contactid']]['makedecisiontype'] = $row['makedecisiontype'];
                $arrayt[$row['contactid']]['makedecisiontype_name'] = vtranslate($row['makedecisiontype'],"Contacts");
                $arrayt[$row['contactid']]['email'] = $row['email'];

                $arrayt[$row['contactid']+1]['name'] = $row['name'];
                $arrayt[$row['contactid']+1]['gendertype'] = $row['gendertype'];
                $arrayt[$row['contactid']+1]['gendertype_name'] = vtranslate($row['gendertype'],"Contacts");
                $arrayt[$row['contactid']+1]['mobile'] = $row['mobile'];
                $arrayt[$row['contactid']+1]['phone'] = $row['phone'];
                $arrayt[$row['contactid']+1]['title'] = $row['title'];
                $arrayt[$row['contactid']+1]['makedecisiontype'] = $row['makedecisiontype'];
                $arrayt[$row['contactid']+1]['makedecisiontype_name'] = vtranslate($row['makedecisiontype'],"Contacts");
                $arrayt[$row['contactid']+1]['email'] = $row['email'];

            }
            $lists['contacts']=$arrayt;
            if (!empty($_GET['array'])) {
                print_r($lists);
            } else {
                echo json_encode($lists,JSON_UNESCAPED_UNICODE);
            }
        }else{
            $lists=array('success'=>false,'msg'=>'没有客户的相关信息',JSON_UNESCAPED_UNICODE);
            echo json_encode($lists);

        }
    }elseif(is_array($productid) && $productid[0]=='getModComment'){
        global $adb;
        $accountid=$_GET['accountid'];
        $query="SELECT vtiger_modcomments.commentreturnplanid,vtiger_modcomments.commentcontent, vtiger_modcomments.addtime,
				vtiger_modcomments.related_to, vtiger_modcomments.creatorid, vtiger_modcomments.modcommenttype, 
                IFNULL((select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_crmentity.smownerid=vtiger_users.id),'--') as smownerid, 
                                vtiger_modcomments.modcommentmode, vtiger_modcomments.modcommenthistory, vtiger_modcomments.modcommentpurpose,
                                vtiger_modcomments.modcommentsid,vtiger_modcomments.contact_id,IFNULL((select name from vtiger_contactdetails where contactid=vtiger_modcomments.contact_id),IFNULL((select linkname from vtiger_account where accountid=vtiger_modcomments.related_to ),'-')) as lastname,
                                IFNULL((select linkname from vtiger_account where accountid=vtiger_modcomments.related_to ),'-') as shouyao 
                                FROM vtiger_modcomments
                LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_modcomments.related_to WHERE vtiger_crmentity.smownerid=vtiger_modcomments.creatorid  AND vtiger_modcomments.related_to = 85114 ORDER BY vtiger_modcomments.modcommentsid DESC LIMIT 1";
        $sales = $adb->pquery($query, array());

        $rows = $adb->num_rows($sales);
        if ($rows>0) {
            $lists = array();
            $arr=$adb->query_result_rowdata($sales);
            $lists['success']=true;
            $lists['commentcontent']=$arr['commentcontent'];
            $lists['addtime']=$arr['addtime'];

            $lists['modcommenttype']=$arr['modcommenttype'];
            $lists['modcommentpurpose']=$arr['modcommentpurpose'];
            $lists['shouyao']=$arr['shouyao'];
            $lists['smownerid']=$arr['smownerid'];

            if (!empty($_GET['array'])) {
                print_r($lists);
            } else {
                echo json_encode($lists,JSON_UNESCAPED_UNICODE );
            }
        }else{
            $lists=array('success'=>false,'msg'=>'没有客户的相关跟进信息',JSON_UNESCAPED_UNICODE );
            echo json_encode($lists);

        }
    }elseif(is_array($productid) && $productid[0]=='getcontract'){
        include_once 'vtlib/Vtiger/Module.php';
        include_once 'includes/main/WebUI.php';
        include_once  'languages/zh_cn/Accounts.php';
        vglobal('default_language', $default_language);
        $currentLanguage = 'zh_cn';
        //Vtiger_Language_Handler::getLanguage();//2.语言设置
        vglobal('current_language',$currentLanguage);
        global $adb;
        $accountid=$_GET['accountid'];
        $query="SELECT 
            IF(vtiger_servicecontracts.firstcontract=1,'是','否') as firstcontract,
            IF(vtiger_servicecontracts.firstfrommarket=1,'是','否') as firstfrommarket,
            IF(vtiger_servicecontracts.isautoclose=1,'是','否') as isautoclose,
            vtiger_servicecontracts.contract_no, 
            (vtiger_activationcode.activecode) as temp_activecode,
            vtiger_servicecontracts.temp_activecode as temp_activecode_reference, 
            (vtiger_account.accountname) as sc_related_to,
            vtiger_servicecontracts.sc_related_to as sc_related_to_reference,
            vtiger_servicecontracts.contract_type,vtiger_servicecontracts.servicecontractstype, 
            (vtiger_products.productname) as productid,
            vtiger_servicecontracts.productid as productid_reference,
            vtiger_servicecontracts.modulestatus,
            vtiger_servicecontracts.workflowsnode,
            (select last_name from vtiger_users where id= (select smownerid from vtiger_crmentity where crmid=vtiger_account.accountid limit 1)) as accountownerid,
            IF(vtiger_servicecontracts.contractstate=1,'是','否') as contractstate,
            vtiger_servicecontracts.servicecontractsprint,vtiger_servicecontracts.invoicecompany,
            IF(vtiger_servicecontracts.isstandard=1,'是','否') as isstandard,
            vtiger_servicecontracts.pagenumber,
            IFNULL((select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_crmentity.smownerid=vtiger_users.id),'--') as smownerid,
            vtiger_crmentity.smownerid as smownerid_owner,
            vtiger_servicecontracts.receivedate,
            (select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_servicecontracts.signid=vtiger_users.id) as signid,
            (select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_servicecontracts.cancelid=vtiger_users.id) as cancelid,
            vtiger_servicecontracts.accountsdue,
            vtiger_servicecontracts.signdate,
            vtiger_servicecontracts.canceltime,
            vtiger_servicecontracts.receiptnumber,
            (select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_servicecontracts.receiveid=vtiger_users.id) as receiveid, 
            (vtiger_servicecomments.serviceid) as serviceid,
            vtiger_servicecomments.serviceid as serviceid_reference,
            IF(vtiger_servicecontracts.multitype=1,'是','否') as multitype,vtiger_servicecontracts.confirmlasttime,
            (select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_servicecontracts.supercollar=vtiger_users.id) as supercollar,
            IF(vtiger_servicecontracts.cantheinvoice=1,'是','否') as cantheinvoice,
            IF(vtiger_servicecontracts.isconfirm=1,'是','否') as isconfirm,
            vtiger_servicecontracts.delayuserid,
            vtiger_servicecontracts.attachmenttype,
            IF(vtiger_servicecontracts.iscomplete=1,'是','否') as iscomplete,
            (select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_servicecontracts.receiptorid=vtiger_users.id) as receiptorid,
            IF(vtiger_servicecontracts.sideagreement=1,'是','否') as sideagreement,
            vtiger_servicecontracts.effectivetime,
            IF(vtiger_servicecontracts.isguarantee=1,'是','否') as isguarantee,
            vtiger_servicecontracts.returndate,
            vtiger_servicecontracts.currencytype,
            vtiger_servicecontracts.total,
            vtiger_servicecontracts.file,
            vtiger_servicecontracts.productsearchid,
            vtiger_servicecontracts.remark,
            vtiger_servicecontracts.pre_deposit,
            vtiger_servicecontracts.cancelvoid,
            (select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_servicecontracts.cancelfeeid=vtiger_users.id) as cancelfeeid,
            vtiger_servicecontracts.cancelremark,
            vtiger_servicecontracts.service_charge,
            vtiger_servicecontracts.firstreceivepaydate,
            vtiger_servicecontracts.account_opening_fee,
            (select createdtime from vtiger_crmentity where vtiger_crmentity.crmid=vtiger_servicecontracts.servicecontractsid and vtiger_crmentity.deleted=0) as createdtime,
            vtiger_servicecontracts.tax_point,
            IFNULL((select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_crmentity.modifiedby=vtiger_users.id),'--') as modifiedby,
            vtiger_crmentity.modifiedby as modifiedby_reference,
            (select modifiedtime from vtiger_crmentity where vtiger_crmentity.crmid=vtiger_servicecontracts.servicecontractsid and vtiger_crmentity.deleted=0) as modifiedtime, 
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
            vtiger_servicecontracts.servicecontractsid 
            FROM vtiger_servicecontracts 
            LEFT JOIN vtiger_crmentity ON vtiger_servicecontracts.servicecontractsid = vtiger_crmentity.crmid 
            LEFT JOIN vtiger_activationcode ON vtiger_activationcode.contractid=vtiger_servicecontracts.servicecontractsid 
            LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_servicecontracts.sc_related_to 
            LEFT JOIN vtiger_products ON vtiger_products.productid=vtiger_servicecontracts.productid 
            LEFT JOIN vtiger_servicecomments ON (vtiger_account.accountid = vtiger_servicecomments.related_to and vtiger_servicecomments.assigntype = 'accountby' and vtiger_servicecomments.related_to>0) 
            WHERE 1=1 and vtiger_crmentity.deleted=0 AND vtiger_servicecontracts.sc_related_to=? ORDER BY vtiger_servicecontracts.signdate DESC";
        $sales = $adb->pquery($query, array($accountid));

        $rows = $adb->num_rows($sales);
        if ($rows>0) {
            $lists = array();
            $arr=$adb->query_result_rowdata($sales);
            $lists['success']=true;
            $lists['totalandtax']=$arr['totalandtax'];
            $lists['unit_price']=$arr['unit_price'];
            $lists['totalandtax']=$arr['totalandtax'];
            $lists['total']=$arr['total'];
            $lists['modulestatus']=$arr['modulestatus'];
            $lists['modulestatus_name']=vtranslate($arr['modulestatus'],"ServiceContracts");
            $lists['servicecontractstype']=$arr['servicecontractstype'];
            $lists['servicecontractstype_name']=vtranslate($arr['servicecontractstype'],"ServiceContracts");
            $lists['contract_type']=$arr['contract_type'];
            $lists['signdate']=$arr['signdate'];
            $lists['effectivetime']=$arr['effectivetime'];
            $lists['contract_no']=$arr['contract_no'];

            if (!empty($_GET['array'])) {
                print_r($lists);
            } else {
                echo json_encode($lists,JSON_UNESCAPED_UNICODE);
            }
        }else{
            $lists=array('success'=>false,'msg'=>'没有客户的合同信息');
            echo json_encode($lists,JSON_UNESCAPED_UNICODE);

        }
    }
    exit;
}

// 服务合同激活码 接口
$api_mark = $_GET['api_mark'];
if ($api_mark == 'activationcode') {
    $key = 'crm_vtiger_2016';

    $request_nowtime = empty($_GET['nowtime']) ? '' : $_GET['nowtime'];
    $request_access_token = empty($_GET['access_token']) ? '' : $_GET['access_token'];
    $request_activecode = empty($_GET['activecode']) ? '' : $_GET['activecode'];
    $request_activedate = empty($_GET['activedate']) ? '' : intval($_GET['activedate']);
    $request_expiredate =  empty($_GET['expiredate']) ? '' : intval($_GET['expiredate']);

    $access_token = sha1(md5($key . $request_activecode . $request_nowtime)); //8c5a7efa3506988cee107ae52fbc380f0599951d

    //http://www.crm.com/api.php?api_mark=activationcode&access_token=8c5a7efa3506988cee107ae52fbc380f0599951d&activecode=65d1de51532a427d92bc79aa2ca37851&activedate=1231312323&expiredate=1231312323&nowtime=123456
    $res = array('success'=>false, 'msg'=>'');
    
    if ($request_access_token == $access_token) {  // 判断口令
        if(empty($request_activecode)) {
            $res['msg'] = '激活码不能为空';
            echo json_encode($res);
            exit;
        }
        if(empty($request_activedate) || $request_activedate <= 0 ) {
            $res['msg'] = '激活时间不能为空，必须为时间格式。';
            echo json_encode($res);
            exit;
        }
        if(empty($request_expiredate) || $request_expiredate <= 0) {
            $res['msg'] = '结束时间不能为空，必须为时间格式。';
            echo json_encode($res);
            exit;
        }
        if ($request_expiredate < $request_activedate) {
            $res['msg'] = '结束时间不能大于激活时间';
            echo json_encode($res);
            exit;
        }
        $request_activedate = date('Y-m-d', $request_activedate);
        $request_expiredate = date('Y-m-d', $request_expiredate);

        global $adb;
        $sql = "select * from vtiger_activationcode where activecode=? LIMIT 1";
        $sel_result = $adb->pquery($sql, array($request_activecode));
        $res_cnt = $adb->num_rows($sel_result);
        if ($res_cnt > 0) {
            // 更新时间
            $sql = "update vtiger_activationcode set activedate=?, expiredate=? where activecode=?";
            $adb->pquery($sql, array($request_activedate, $request_expiredate, $request_activecode));
            $res['success'] = true;
            $res['msg'] = '激活码操作成功';
            echo json_encode($res);
        } else {
            // 插入
            $data = array(
                'activecode'=>$request_activecode,
                'activedate'=>$request_activedate,
                'expiredate'=>$request_expiredate
            );
            $divideNames = array_keys($data);
            $divideValues = array_values($data);
            $adb->pquery('INSERT INTO `vtiger_activationcode` ('. implode(',', $divideNames).') VALUES ('. generateQuestionMarks($divideValues) .')', $divideValues); 
            $res['success'] = true;
            $res['msg'] = '激活码操作成功';
            echo json_encode($res);
        }
    } else {
        $res['msg'] = '口令错误';
        echo json_encode($res);
    }
}
?>