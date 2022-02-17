<?php

class OrderChargeback extends baseapp {
    public function one() {
        $id = $_REQUEST['id'];
        if (!empty($id)) {
            $params = array(
                'fieldname' => array(
                    'id' => $id,
                    'module' => 'OrderChargeback',
                    'record' => $id,
                ),
                'userid' => $this->userid
            );
            $result = $this->call('oneOrderChargeback', $params);
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

            $processingresult=array(
                'refund'=>'退款',
                'transferservice'=>'转业务',
               );
            /*die();*/
            /*echo "<pre>";
            var_dump($result[0]);
            die();*/
            $this->smarty->assign('processingresult',$processingresult);
            $this->smarty->assign('detailInfo', $result[0]);
            $this->smarty->assign('USERIMGS', $arr);
            $this->smarty->assign('detailsOfRepayment',$result[2]);
            $this->smarty->assign('DetailsOfRefundProducts',$result[3]);
            $this->smarty->assign('CostSynthesisInformation',$result[4]);
            $this->smarty->assign('DetailsOfApplicationInvoiceForRefund',$result[5]);
            $this->smarty->assign('ISROLE', $result[1]['ISROLE']);    //是否有权限审核
            $this->smarty->assign('WORKFLOWSSTAGELIST', $result[1]['WORKFLOWSSTAGELIST']); //节点列表
            $this->smarty->assign('STAGERECORDID', $result[1]['STAGERECORDID']);  //当前工作流id
            $this->smarty->assign('STAGERECORDNAME', $result[1]['STAGERECORDNAME']);  //当前审核工作流的名字
            $this->smarty->assign('SALESORDERHISTORY', $result[1]['SALESORDERHISTORY']);  //历史打回原因记录
            $this->smarty->assign('REMARKLIST', $result[1]['REMARKLIST']);  //备注记录
            $this->smarty->assign('record', $id);
            $this->smarty->display('OrderChargeback/one.html');
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
                    'src_module' => 'OrderChargeback',
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
                    'src_module' => 'OrderChargeback',
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
                    'src_module' => 'OrderChargeback',
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
