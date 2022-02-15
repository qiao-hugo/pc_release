<?php

class Accounts extends baseapp {
    #查找客户

    public function searchAccount() {
        $company = trim($_REQUEST['company']);
        $isPermission = $_REQUEST['ispermission'] ? true : false;
        if (!empty($company)) {
            $params = array(
                'searchModule' => 'Accounts',
                'searchValue' => $company,
                'relatedModule' => '',
                'userid' => $this->userid,
                'isPermission'=>$isPermission
            );
            $list = $this->call('com_search_list', $params);
            echo json_encode($list);
            exit;
        }
        echo "";exit;
        
        print_r($list);
    }

    #获取客户资料

    public function getAccountMsg() {
        $id = intval($_REQUEST['id']);
        if (!empty($id)) {
            $params = array(
                'id' => $id
            );
            $list = $this->call('get_account_msg', $params);

            if (!empty($list)) {
                $address = explode('#', $list[0]['address']);
                $customeraddress = implode('', $address);
                $address = !empty($address) && isset($address[3]) ? $address[3] : '';

                $return = array(
                    'accountid' => $list[0]['accountid'],
                    'accountname' => $list[0]['accountname'],
                    'linkname' => $list[0]['linkname'],
                    'username' => $list[0]['username'],
                    'userid' => $list[0]['userid'],
                    'address' => $address,
                    'customeraddress' => $customeraddress
                );
                echo json_encode($return);
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    #添加联系人

    public function addContact() {
        $account_id = trim($_REQUEST['id']);
        $params = array(
            'id' => $account_id,
        );
        $list = $this->call('get_account_msg', $params);

        if (!empty($list)) {
            $this->smarty->assign('account_id', $account_id);
            $this->smarty->assign('account_name', $list[0]['accountname']);

            $this->smarty->display('accounts/add.html');
        } else {
            echo 'error';
        }
    }

    #添加联系人

    public function doaddContact() {

        $name = trim($_REQUEST['name']);
        $account_id = intval(trim($_REQUEST['account_id']));
        $account_id_display = trim($_REQUEST['account_id_display']);
        $description = trim($_REQUEST['description']);
        $gendertype = trim($_REQUEST['gendertype']);
        $phone = trim($_REQUEST['phone']);
        $title = trim($_REQUEST['title']);
        $mobile = trim($_REQUEST['mobile']);
        $makedecisiontype = trim($_REQUEST['makedecisiontype']);
        $email = trim($_REQUEST['email']);
        $assigned_user_id = $this->userid;


        if (empty($name) || empty($phone) || empty($title) || empty($mobile) || empty($mobile)) {

            $this->response(false, '信息填写不全');
        }

        $params = array(
            'fieldname' => array(
                "sourceRecord" => $account_id,
                "account_id" => $account_id,
                "account_id_display" => $account_id_display,
                "name" => $name,
                "gendertype" => $gendertype,
                "phone" => $phone,
                "title" => $title,
                "mobile" => $mobile,
                "makedecisiontype" => $makedecisiontype, #'Effect of human',
                "assigned_user_id" => $assigned_user_id,
                "email" => $email,
                "description" => $description,
                "module" => 'Contacts',
                "action" => 'Save',
                "record" => '',
                "defaultCallDuration" => 5,
                "defaultOtherEventDuration" => 5,
                "sourceModule" => 'Accounts',
                "relationOperation" => true,
                "popupReferenceModule" => 'Accounts',
                "emailoptout" => 0,
                "donotcall" => 0,
            ),
            'userid' => $this->userid
        );
        $res = $this->call('add_contact', $params);
        if (!empty($res) && $res[0] > 0) {
            $this->response(true);
        } else {
            $this->response(false);
        }
        exit;
    }

    /*
      新建客户界面 20167-15
     */

    public function goaddAcoundUI() {
        $params = array('1' => '1');
        $res = $this->call('getAddAccountReadyData', $params);

        $this->smarty->assign('customerproperty', $res[0]);
        $this->smarty->assign('leadsource', $res[1]);
        $this->smarty->assign('title','新建客户');
        $this->smarty->display('accounts/addaccounts.html');
    }

    /**
     * 客户列表
     */
    public function listAcound() {
        $pagenum = isset($_REQUEST['pagenum']) ? $_REQUEST['pagenum'] : 1;
        $status = isset($_REQUEST['status']) ? $_REQUEST['status'] : 'check';
        $searchvalue = isset($_REQUEST['searchvalue']) ? $_REQUEST['searchvalue'] : '';
        $pagecount = 20;
        if (!empty($searchvalue)) {
            $search = array(
                array(
                    'field' => "vtiger_account.accountname##2##1##string",
                    'operator' => "LIKE",
                    "value" => $searchvalue,
                    "andor" => "And",
                )
            );
        }
        $search = $this->create_search_field($search);
//        print_r($search);exit;
        $params = array(
            'fieldname' => array(
                "module" => 'Accounts',
                'pagenum' => $pagenum,
                'pagecount' => $pagecount,
                'searchField' => $search,
                'userid' => $this->userid,
                'modulestatus' => $status,
            ),
            'userid' => $this->userid
        );
//        $params = array(
//            'fieldname' => array(
//                "module" => 'Accounts',
//                'pagenum' => $pagenum,
//                'pagecount' => $pagecount,
//                'userid' => $this->userid,
//                'modulestatus' => $status,
//            ),
//            'userid' => $this->userid
//        );
        $list = $this->call('getList', $params); //调用accounts.class.php
        $this->smarty->assign('list', $list[1]);
        $this->smarty->assign('totalnum', $list[0]);
//        print_r(json_decode(json_encode($list),TRUE));exit;
        $this->smarty->display('accounts/list.html');
    }

    /**
     * 客户详情
     */
    public function userDetail() {
        $params = array(
            'fieldname' => array(
                'record' => $_REQUEST['record'],
                'limit' => 5,
                'page' => 1,
            ),
        );
        $list = $this->call('getUserDetail', $params); //调用accounts.class.php
        $this->smarty->assign('COMMENTS', $list[0]['COMMENTS']);
        $this->smarty->assign('accountName', $list[1]);
        $this->smarty->assign('MODCOMMENTCONTACTS', $list[0]['MODCOMMENTCONTACTS']);
        $this->smarty->assign('COMMENTSTYPE', $list[0]['COMMENTSTYPE']);
        $this->smarty->assign('double_type', !empty($list[0]['double_type']) ? $list[0]['double_type'] : '');
        $this->smarty->assign('COMMENTSMODE', $list[0]['COMMENTSMODE']);
        $this->smarty->assign('MODULE_NAME', $list[0]['MODULE_NAME']);
        $this->smarty->assign('ACCOUNTID', $list[0]['ACCOUNTID']);
        $this->smarty->assign('ACCOUNTINTENTIONALITY', $list[0]['ACCOUNTINTENTIONALITY']);
        $this->smarty->assign('ROLE', $list[0]['ROLE']);
        $this->smarty->assign('taskpriority',array('High'=>'高','Medium'=>'中','Low'=>'低'));
        $this->smarty->assign('activitytype',array('Call'=>'电话','Meeting'=>'会议'));
        $this->smarty->assign('alertstatus',array('wait'=>'待办','finish'=>'完成','past'=>'完成(过期)'));
        $totalnum = 20;
        $this->smarty->assign('totalnum', $totalnum);

        $this->smarty->display('accounts/listdetail.html');
    }
    /**
     * 添加评论
     */
    public function followComment(){
       $params = array(
            'fieldname' => array(
                'modcommentsid' => $_REQUEST['modcommentsid'],
                'modcommenthistory'=>$_REQUEST['modcommenthistory'],
                'accountintentionality'=>$_REQUEST['accountintentionality'],
                'userId'=>$this->userid
            ),
        );
       $list[0]= $this->call('followComment', $params);
       echo json_encode($list[0][0]);exit();

    }
    /**
     *  添加提醒
     */
    public function addAlertData(){
        $params = array(
            'fieldname' => array(
                'modcommentsid' => $_REQUEST['modcommentsid'],
                'subject'=>$_REQUEST['subject'],
                'alertcontent'=>$_REQUEST['alertcontent'],
                'alerttime'=>$_REQUEST['alerttime'],
                'alertid'=>$_REQUEST['alertid'],
                'accountid'=>$_REQUEST['accountid'],
                'activitytype'=>$_REQUEST['activitytype'],
                'taskpriority'=>$_REQUEST['taskpriority'],
                'creatorid'=>$this->userid
            ),
        );
        $list[0]= $this->call('addAlertData', $params);
        echo json_encode($list[0][0]);exit();
    }
    /**
     *  客户添加页面
     */
    public function addAccount(){
        $params = array(
            'fieldname' => array(
                'record' => $_REQUEST['accountID'],
                'limit' => 5,
                'page' => 1,
            ),
        );

        $list = $this->call('getUserDetail', $params);
        $this->smarty->assign('COMMENTSTYPE', $list[0]['COMMENTSTYPE']);
        $this->smarty->assign('MODCOMMENTCONTACTS', $list[0]['MODCOMMENTCONTACTS']);
        $this->smarty->assign('COMMENTSMODE', $list[0]['COMMENTSMODE']);
        $this->smarty->assign('ACCOUNTINTENTIONALITY', $list[0]['ACCOUNTINTENTIONALITY']);
        $this->smarty->assign("ACCOUNTID",$_REQUEST['accountID']);
        $this->smarty->assign("accountName",$list[1]);
        $this->smarty->display('accounts/addAccount.html');
    }
    /**
     * 跟进提醒页面
     */
     public function addAlert(){
         $params = array(
             'fieldname' => array(
                 'record' => $_REQUEST['accountID'],
                 'limit' => 5,
                 'page' => 1,
             ),
         );
         $userInfo=$this->call('getGroupUsers', $params);
         $this->smarty->assign("userGroup",json_decode($userInfo[0],true));
         $list = $this->call('getUserDetail', $params);
         $this->smarty->assign("ACCOUNTID",$_REQUEST['accountID']);
         $this->smarty->assign("modcommentsid",$_REQUEST['modcommentsid']);
         $this->smarty->assign('COMMENTSTYPE', $list[0]['COMMENTSTYPE']);
         $this->smarty->assign('MODCOMMENTCONTACTS', $list[0]['MODCOMMENTCONTACTS']);
         $this->smarty->assign('COMMENTSMODE', $list[0]['COMMENTSMODE']);
         $this->smarty->assign("accountName",$list[1]);
         $this->smarty->display('accounts/addAlert.html');
     }
    /**
     * 客户添加
     */
    public function addFollowInfo() {
        $params = array(
            'fieldname' => array(
                'commentcontent' => isset($_REQUEST['commentcontents']) ? $_REQUEST['commentcontents'] : '',
                'modcommentmode' => !empty($_REQUEST['modcommentmode']) ? $_REQUEST['modcommentmode'] : '',
                'modcommenttype' => !empty($_REQUEST['modcommenttype']) ? $_REQUEST['modcommenttype'] : '',
                'modcommentpurpose' => !empty($_REQUEST['modcommentpurpose']) ? $_REQUEST['modcommentpurpose'] : '',
                'contact_id' => !empty($_REQUEST['contact_id']) ? $_REQUEST['contact_id'] : '',
                'related_to' => !empty($_REQUEST['related_to']) ? $_REQUEST['related_to'] : '',
                'module' => !empty($_REQUEST['module']) ? $_REQUEST['module'] : '',
                'modulename' => !empty($_REQUEST['modulename']) ? $_REQUEST['modulename'] : '',
                'moduleid' => !empty($_REQUEST['moduleid']) ? $_REQUEST['moduleid'] : '',
                'ifupdateservice' => isset($_REQUEST['ifupdateservice']) ? $_REQUEST['ifupdateservice'] : '',
                'is_service' => isset($_REQUEST['is_service']) ? $_REQUEST['is_service'] : '',
                'isfollowplain' => isset($_REQUEST['isfollowplain']) ? $_REQUEST['isfollowplain'] : '',
                'action' => !empty($_REQUEST['action']) ? $_REQUEST['action'] : '',
                'creatorid' => $this->userid,
                'accountid'=>$_REQUEST['accountid'],
                'followupdata'=>$_REQUEST['followupdata'],
                'accountintentionality'=>$_REQUEST['accountintentionality']
            ),
        );
        $data = $this->call('addFollowInfo', $params);
//        print_r($data);exit;
        $this->response(true);
//        $this->response(true,$data[0]['data']);
        exit;
    }

    /*
      新建客户 2016-7-18
     */

    public function doaddAccounts() {
        $accountname = trim($_REQUEST['accountname']);
        $customerproperty = trim($_REQUEST['customerproperty']);
        $leadsource = trim($_REQUEST['leadsource']);
        $phone = trim($_REQUEST['phone']);
        $province = trim($_REQUEST['province']);

        $city = trim($_REQUEST['city']);
        $area = trim($_REQUEST['area']);
        $address = trim($_REQUEST['address']);
        $business = trim($_REQUEST['business']);
        $businessarea = trim($_REQUEST['businessarea']);
        $regionalpartition = trim($_REQUEST['regionalpartition']);

        $linkname = trim($_REQUEST['linkname']);
        $title = trim($_REQUEST['title']);
        $gendertype = trim($_REQUEST['gendertype']);
        $makedecisiontype = trim($_REQUEST['makedecisiontype']);
        $email = trim($_REQUEST['email']);

        $mobile = trim($_REQUEST['mobile']);
        $weixin = trim($_REQUEST['weixin']);
        $description = trim($_REQUEST['description']);
        // 新增
        $cooperationtypesproperty = trim($_REQUEST['cooperationtypesproperty']);
        $customertype = trim($_REQUEST['customertype']);
        $fax = trim($_REQUEST['fax']);
        $country = trim($_REQUEST['country']);
        $industry = trim($_REQUEST['industry']);
        $website = trim($_REQUEST['website']);
        $annual_revenue = trim($_REQUEST['annual_revenue']);
        $educationproperty = trim($_REQUEST['educationproperty']);

        $assigned_user_id = $this->userid;

        if (empty($accountname) || empty($customerproperty) || empty($business) ||
                empty($leadsource) ||
                empty($businessarea) || empty($regionalpartition) || empty($linkname) ||
                empty($phone) || empty($title) || empty($mobile) || empty($email)) {
            $this->response(false, '信息填写不全');
        }

        $params = array(
            'fieldname' => array(
                "accountname" => $accountname,
                "customerproperty" => $customerproperty,
                "leadsource" => $leadsource,
                "phone" => $phone,
                "province" => $province,
                "city" => $city,
                "area" => $area,
                "address" => $address,
                "businessarea" => $businessarea, #'Effect of human',
                "regionalpartition" => $regionalpartition,
                "linkname" => $linkname,
                "title" => $title,
                "gendertype" => $gendertype,
                "makedecisiontype" => $makedecisiontype,
                "email" => $email,
                "mobile" => $mobile,
                "weixin" => $weixin,
                "description" => $description,
                "business" => $business,
                "module" => 'Accounts',
                "action" => 'Save',
                "record" => '',
                'email1' => $email,
                "cooperationtypesproperty"=>$cooperationtypesproperty,
                "customertype"=>$customertype,
                "fax"=>$fax,
                "country"=>$country,
                "industry"=>$industry,
                "website"=>$website,
                "annual_revenue"=>$annual_revenue,
                "educationproperty"=>$educationproperty,
                "pcType"=>1,
            /* "defaultCallDuration"=>5,
              "defaultOtherEventDuration"=>5,
              "sourceModule"		=>'Accounts',
              "relationOperation"=>true,
              "popupReferenceModule"=>'Accounts',
              "emailoptout"		=>0,
              "donotcall"			=>0, */
            ),
            'userid' => $this->userid
        );

        $res = $this->call('add_accounts', $params);
        if($res[0]["result"]=='success'){
            $this->response(true,$res[0]["message"]);exit();
        }else{
            $this->response(false,$res[0]["message"]);exit();
        }
    }

    /*
      ajax判断用户名是否重复 2016-7-18
     */

    public function check_accountname() {
        $accountname = trim($_REQUEST['accountname']);
        $params = array(
            'fieldname' => array('accountname' => $accountname),
            'userid' => $this->userid
        );

        $res = $this->call('check_accountname', $params);
        if (!empty($res) && $res[0] > 0) {
            $this->response(true);
        } else {
            $this->response(false);
        }
        exit;
    }

    /*
      客户列表
     */

    public function vlist() {
        $params = array('fieldname' => array('pagenum' => '1'), 'userid' => $this->userid);
        $res = $this->call('getAccounts', $params);
        $this->smarty->assign('accounts', $res[0]);
        $this->smarty->assign('total', $res[1]);
        $this->smarty->display('accounts/vlist.html');
    }

    /*
      客户详细信息
     */

    public function accountdDetail() {
        $accountid = trim($_REQUEST['id']);
        $params = array('fieldname' => array('accountid' => $accountid), 'userid' => $this->userid);
        $res = $this->call('getAccountsDetail', $params);
        $this->smarty->assign('accountDetail', $res[0]);
        $this->smarty->display('accounts/detail.html');
    }

    public function ajax_vlist() {
        $pagenum = trim($_REQUEST['pagenum']);
        if (empty($pagenum)) {
            $pagenum = '1';
        }
        $params = array('fieldname' => array('pagenum' => $pagenum), 'userid' => $this->userid);
        $res = $this->call('getAccounts', $params);
        echo json_encode($res[0]);
        exit;
    }

}
