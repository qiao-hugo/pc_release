<?php

class Newinvoice extends baseapp {
    // 发票新列表
    public function index(){
        $pagenum   = isset($_REQUEST['pagenum'])?$_REQUEST['pagenum']:1;
        $status = $_REQUEST['status']?$_REQUEST['status']:'all';
        $pagecount  = $this->count;
        $pagecount  = 20;
        $search = array();
        $searchfieldname='';
        $searchfieldvalue='';
        if(!empty($_POST['searchvalue']))
        {
            $searchfieldvalue=$_POST['searchvalue'];
            $searchfieldvalue=str_replace(",","",$searchfieldvalue);
            $searchfieldvalue=str_replace('#',"",$searchfieldvalue);
            //$searchfieldv="'%".$searchfieldvalue."%'";
            if($_POST['radiot']=='contract_no')
            {
                // 这里的条件在后台listview里会被替换成子查询 详细sql 从执行sql查看
                $search[] =array(
                'field'		=>"vtiger_servicecontracts.contract_no##10##10643##reference",
                'operator'	=>"LIKE",
                "value"		=>$searchfieldvalue,
                "andor"		=>"AND",
                );
                $searchfieldname='contract_no';
            }
            elseif($_POST['radiot']=='accountname')
            {
                $search[] = array(
                    'field'		=>"vtiger_account.accountname##10##529##reference",
                    'operator'	=>"LIKE",
                    "value"		=>$searchfieldvalue,
                    "andor"		=>"AND",
                );
                $searchfieldname='accountname';
            }else if($_POST['radiot']=='invoice_no'){
                $search[] = array(
                    'field'		=>"vtiger_newinvoice.invoiceno##4##10765##string",
                    'operator'	=>"LIKE",
                    "value"		=>$searchfieldvalue,
                    "andor"		=>"AND",
                );
                $searchfieldname='invoice_no';
            }
        }

        $search=$this->create_search_field($search);
        $params  = array(
            'fieldname'=>array(
                "module"     =>'Newinvoice',
                'pagenum'    => $pagenum,
                'pagecount'  => $pagecount,
                'userid'     => $this->userid,
                'pagecount'=> $pagecount,
                'searchField'=> $search,
                //'modulestatus' => $status,
            ),
            'userid'    => $this->userid
        );
        $list = $this->call('getNewinvoice', $params);
        $arr=array();
        /* echo "<pre>";
        var_dump($list[1]);die();*/
        if(!empty($list[1])) {
            $url='https://qyapi.weixin.qq.com/cgi-bin/user/list?department_id=1&fetch_child=1&status=1&access_token=';
            $userlist = $this->getWeixinMsg($url);
            $userlist = json_decode($userlist, true);
            if ($userlist['errcode'] == 0) {
                foreach ($userlist['userlist'] as $value) {
                    $arr[md5($value['userid'])] = $value['avatar'];
                }
            }
        }
        $this->smarty->assign('moduleStatusArray',$this->modulestatus);
        $this->smarty->assign('totalnum', $list[0]);
        $sumpage = intval(($list[0]+1)/$pagecount);
        $this->smarty->assign('list',$list[1]);
        $this->smarty->assign('USERIMGS',$arr);

        $this->smarty->assign('fieldname',$searchfieldname);
        $this->smarty->assign('fieldvalue',$searchfieldvalue);
        $this->smarty->assign('status', $status);

        $type = $_REQUEST['type'];
        if ($type == 'ajax') {
            $this->smarty->display('Newinvoice/ajax.html');
        } else {
            $this->smarty->display('Newinvoice/index.html');
        }
    }
    //审核内容详情
    public function one() {
        $id = $_REQUEST['id'];
        if (!empty($id)) {
            $invoicestatus = array('AutoCreated'=>'自动创建','Created'=>'已创建','Approved'=>'已批准',
                'Sent'=>'已寄送','Credit Invoice'=>'信用发票',
                'Paid'=>'已支付','Cancel'=>'取消','tovoid'=>'作废','returnticket'=>'退票',
                'userinvoice'=>'使用');
            $params = array(
                'fieldname' => array(
                    'id' => $id,
                    'module' => 'Newinvoice',
                    'record' => $id,
                ),
                'userid' => $this->userid
            );
            $result = $this->call('oneNewinvoice', $params);
            $arr = array();
            if (!empty($result[1]['REMARKLIST'])) {
                $userlist = $this->getWeixinMsg();
                $userlist = json_decode($userlist, true);
                if ($userlist['errcode'] == 0) {
                    foreach ($userlist['userlist'] as $value) {
                        $arr[md5($value['userid'])] = $value['avatar'];
                    }
                }
            }
            $this->smarty->assign('moduleStatusArray',$this->modulestatus);
            $this->smarty->assign('invoicestatus',$invoicestatus);
            $this->smarty->assign('detailInfo', $result[0]);
            $this->smarty->assign('RelevantPaymentInformation', $result[2]);//关联回款数据
            $this->smarty->assign('newinvoiceextend', $result[3]);//财务数据
            $this->smarty->assign('abandonedData', $result[4]);//作废数据
            $this->smarty->assign('redAbandonedData', $result[5]);//红冲作废数据
            $this->smarty->assign('contractReturnRecord', $result[6]);//合同回款记录
            $this->smarty->assign('USERIMGS', $arr);
            $this->smarty->assign('ISROLE', $result[1]['ISROLE']);    //是否有权限审核
            $this->smarty->assign('WORKFLOWSSTAGELIST', $result[1]['WORKFLOWSSTAGELIST']); //节点列表
            $this->smarty->assign('STAGERECORDID', $result[1]['STAGERECORDID']);  //当前工作流id
            $this->smarty->assign('STAGERECORDNAME', $result[1]['STAGERECORDNAME']);  //当前审核工作流的名字
            $this->smarty->assign('SALESORDERHISTORY', $result[1]['SALESORDERHISTORY']);  //历史打回原因记录
            $this->smarty->assign('REMARKLIST', $result[1]['REMARKLIST']);  //备注记录
            $this->smarty->assign('record', $id);
            $this->smarty->display('Newinvoice/one.html');
        }
    }

    // 审核
    public function examine() {
        $stagerecordid = $_REQUEST['stagerecordid'];
        $record = $_REQUEST['record'];
        if ($stagerecordid && $record) {
            $params = array(
                'fieldname' => array(
                    'stagerecordid' => $stagerecordid,
                    'record' => $record,
                    'module' => 'SalesorderWorkflowStages',
                    'mode' => 'updateSalseorderWorkflowStages',
                    'src_module' => 'Newinvoice',
                    'action' => 'SaveAjax',
                    'customer' => 0,
                    'customername' => ''
                ),
                'userid' => $this->userid
            );
            $result=$this->call('salesorderWorkflowStagesExamine', $params);
            $result['result']=$result[1];
            echo json_encode($result);exit();
        }
    }

    // 打回
    public function repulse() {
        $stagerecordid = $_REQUEST['stagerecordid'];
        $record = $_REQUEST['record'];
        $repulseinfo = $_REQUEST['repulseinfo'];
        $isbackname = $_REQUEST['isbackname'];
        if ($stagerecordid && $record) {
            $params = array(
                'fieldname' => array(
                    'record' => $record,
                    'module' => 'SalesorderWorkflowStages',
                    'mode' => 'backall',
                    'reject' => $repulseinfo,
                    'isrejectid' => $stagerecordid,
                    'isbackname' => $isbackname,
                    'src_module' => 'Newinvoice',
                    'action' => 'SaveAjax',
                    'actionnode' => 0
                ),
                'userid' => $this->userid
            );
            $tt = $this->call('salesorderWorkflowStagesRepulse', $params);
            echo $tt[0];die;

        }
    }

    /**
     * 添加备注信息
     */
    public function submitremark() {
        $stagerecordid = $_REQUEST['stagerecordid'];
        $record = $_REQUEST['record'];
        $reject = $_REQUEST['reject'];
        if ($stagerecordid && $record) {
            $params = array(
                'fieldname' => array(
                    'record' => $record,
                    'module' => 'SalesorderWorkflowStages',
                    'mode' => 'submitremark',
                    'reject' => $reject,
                    'isrejectid' => $stagerecordid,
                    'src_module' => 'Newinvoice',
                    'action' => 'SaveAjax',
                    'actionnode' => 0
                ),
                'userid' => $this->userid
            );
            $tt = $this->call('salesorderWorkflowStagesRepulse', $params);
            echo $tt[0];
            die;
        }
    }

}
