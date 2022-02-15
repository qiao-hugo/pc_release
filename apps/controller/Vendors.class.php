<?php

class Vendors extends baseapp {
    public function one() {
        $id = $_REQUEST['id'];
        if (!empty($id)) {
            $params = array(
                'fieldname' => array(
                    'id' => $id,
                    'module' => 'Vendors',
                    'record' => $id,
                ),
                'userid' => $this->userid
            );
            $result = $this->call('oneVendors', $params);
            $arr = array();
            if (!empty($result[2]['REMARKLIST'])) {
                $userlist = $this->getWeixinMsg();
                $userlist = json_decode($userlist, true);
                if ($userlist['errcode'] == 0) {
                    foreach ($userlist['userlist'] as $value) {
                        $arr[md5($value['userid'])] = $value['avatar'];
                    }
                }
            }
            /*echo "<pre>";
            var_dump($result[0]);
            die();*/
            $vendorType=array( 'businesspurchasing'=>'业务采购',
                'administrativepurchase'=>'行政采购',
                'MediaProvider'=>'媒介',);
            $modulestatus=array(
                'c_complete'=>'已签收',
                'c_cancelings'=>'作废中.',
                'a_normal'=>'正常',
                'a_exception'=>'打回中',
                'b_check'=>'审核中',
                'c_complete'=>'完成',
                'c_canceling'=>'作废中',
                'c_cancel'=>'作废',
                'b_actioning'=>'执行中',
                'c_recovered'=>'已收回',
                'c_stamp'=>'已盖章',);
            $this->smarty->assign('vendorType',$vendorType);
            $this->smarty->assign('modulestatus',$modulestatus);
            $this->smarty->assign('detailInfo', $result[0]);
            $this->smarty->assign('productReturnPoint', $result[1]); //产品返点列表
            $this->smarty->assign('USERIMGS', $arr);
            $this->smarty->assign('WORKFLOWSSTAGELIST', $result[2]['WORKFLOWSSTAGELIST']); //节点列表
            $this->smarty->assign('BANKINFOLIST', $result[3]);// 银行账户信息列表
            $this->smarty->assign('ISROLE', $result[2]['ISROLE']);    //是否有权限审核
            $this->smarty->assign('STAGERECORDID', $result[2]['STAGERECORDID']);  //当前工作流id
            $this->smarty->assign('STAGERECORDNAME', $result[2]['STAGERECORDNAME']);  //当前审核工作流的名字
            $this->smarty->assign('SALESORDERHISTORY', $result[2]['SALESORDERHISTORY']);  //历史打回原因记录
            $this->smarty->assign('REMARKLIST', $result[2]['REMARKLIST']);  //备注记录
            $this->smarty->assign('record', $id);
            $this->smarty->display('Vendors/one.html');
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
                    'src_module' => 'Vendors',
                    'action' => 'SaveAjax',
                    'customer' => 0,
                    'customername' => ''
                ),
                'userid' => $this->userid
            );
            $this->call('salesorderWorkflowStagesExamine', $params);
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
                    'src_module' => 'Vendors',
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
                    'src_module' => 'Vendors',
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
