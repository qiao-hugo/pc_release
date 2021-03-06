<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class WorkFlowCheck_List_View extends Vtiger_KList_View {

    public function process(Vtiger_Request $request)
    {
        $mode = $request->get('mode');
        if($mode=='getNotices'){
            $this->getNotices($request);
            exit;
        }elseif($mode=='setNoticesStatus'){
            $this->setUserClickMsgStatus();
        }elseif($mode=='getNoticesansc'){
            $this->getNoticesansc($request);
            exit;
        }
        parent::process($request); // TODO: Change the autogenerated stub
    }
    /**
     * 站内信
     * @param Vtiger_Request $request
     */
    public function getNotices(Vtiger_Request $request){
        $viewer = $this->getViewer($request);
        global $root_directory;
        //echo  $root_directory.'modules'.DIRECTORY_SEPARATOR.'WorkFlowCheck'.DIRECTORY_SEPARATOR.'actions'.DIRECTORY_SEPARATOR.'Confirmation.php';
        $recordModule=Vtiger_Module_Model::getInstance('WorkFlowCheck');
        $MSGSTATUS=$recordModule->getUserClickMsgStatus();
        if($MSGSTATUS){
            //系统消息
            $data=$this->getDataList();
            $messageLink =$data[0];
            // 提醒
            $remindLink = $data[1];
            // 提醒已读未读
            $remindLinkReadState = $data[2];
            $loadData=0;
        }else{
            $messageLink = array(
                //统计有多少条要审核的信息
               );
            $remindLink = array(
                );
            $remindLinkReadState = array(
                //未读未到期提醒
               );
            $loadData=1;
        }
        $viewer->assign('REMINDLINK', $remindLink);
        $viewer->assign('LOADDATA', $loadData);
        $viewer->assign('MSGSTATUS', $MSGSTATUS);
        $viewer->assign('MESSAGELINK', $messageLink);
        $viewer->assign('REMINDLINKREADSTATE', $remindLinkReadState);
        echo $viewer->view('NoticeLayout.tpl','Vtiger',true);
    }
    public function setUserClickMsgStatus(){
        $recordModule=Vtiger_Module_Model::getInstance('WorkFlowCheck');

        $recordModule->setUserClickMsgStatus();
        exit;
    }
    public function getNoticesansc(Vtiger_Request $request){
        $viewer = $this->getViewer($request);
        $data=$this->getDataList();
        $viewer->assign('LOADDATA', 0);
        $viewer->assign('REMINDLINK', $data[1]);
        $viewer->assign('MSGSTATUS', 1);
        $viewer->assign('MESSAGELINK', $data[0]);
        $viewer->assign('REMINDLINKREADSTATE', $data[2]);
        echo $viewer->view('NoticeLayout.tpl','Vtiger',true);
    }
    public function getDataList(){
        $recordModule=Vtiger_Module_Model::getInstance('WorkFlowCheck');
        //系统消息
        $messageLink = array(
            //统计有多少条要审核的信息
            array (
                'recordcount'=>$recordModule->getConfirmation(),
                'linklabel' => 'LBL_CRM_MESSAGE_CONFIR',
                'linkurl' => 'index.php?module=WorkFlowCheck&view=List',
                'target'=>'_blank',
            ),
            //统计有多少条24小时待跟进拜访单
            array (
                'recordcount'=>$recordModule->getVisitingOrderFollowup(),
                'linklabel' => 'LBL_CRM_MESSAGE_ORDER',
                'linkurl' => 'index.php?module=VisitingOrder&view=List&public=FollowUp',
                'target'=>'_blank',
            ),
            //统计未写工作日报的人数
            array (
                'recordcount'=>$recordModule->getNoWrite(),
                'linklabel' => 'LBL_CRM_MESSAGE_NOWRITER',
                'linkurl' => 'index.php?module=WorkSummarize&view=List&filter=nowrite',
                'target'=>'_blank',
            ),
            //有多少要回复的工作日报记录数
            array (
                'recordcount'=>$recordModule->getReplynum(),
                'linklabel' => 'LBL_CRM_MESSAGE_REPLYNUM',
                'linkurl' => 'index.php?module=WorkSummarize&view=List&filter=reply',
                'target'=>'_blank',
            ),
            //超过24小时未审核信息
            array (
                'recordcount'=>$recordModule->getConfirmation('outnumberday'),
                'linklabel' => 'LBL_CRM_MESSAGE_OUTNUMBERDAY',
                'linkurl' => 'index.php?module=WorkFlowCheck&view=List&public=outnumberday',
                'target'=>'_blank',
            ),
            //全部未跟进客服的信息
            array (
                'recordcount'=>$recordModule->getSevenCustomer(),
                'linklabel' => 'LBL_CRM_MESSAGE_CUSTOMER',
                'linkurl' => 'index.php?module=ServiceComments&view=List&public=allnofollowday',
                'target'=>'_blank',
            ),
            //统计当前打回工单的记录条数
            array (
                'recordcount'=>WorkFlowCheck_Confirmation_Action :: getRefuse(),
                'linklabel' => 'LBL_CRM_MESSAGE_REFUSE',
                'linkurl' => 'index.php?module=SalesOrder&view=List&public=refuse',
                'target'=>'_blank',
            ),
            //统计当前没有合同的回款 wangbin
            array (
                'recordcount'=>$recordModule->get_noservice_receivepayment(),
                'linklabel' => 'LBL_CRM_MESSAGE_NOSERVICE_RECEIVE',
                'linkurl' => 'index.php?module=ReceivedPayments&view=List&filter=noservice',
                'target'=>'_blank',
            ),

        );
        // 提醒
        $remindLink = array(
            array (
                'linktype' => 'REMINDERLINK',
                'recordcount'=>JobAlerts_Record_Model::getReminderResultCount('new'),
                'linklabel' => 'LBL_CRM_REMINDER_NEW',
                'linkurl' => 'index.php?module=JobAlerts&view=List&public=new',
                'linkicon' => '',
                'target'=>'_blank',
            ),
            array (
                'linktype' => 'REMINDERLINK',
                'recordcount'=>JobAlerts_Record_Model::getReminderResultCount('wait'),
                'linklabel' => 'LBL_CRM_REMINDER_WAIT',
                'linkurl' => 'index.php?module=JobAlerts&view=List&public=wait',
                'linkicon' => '',
                'target'=>'_blank',
            ),
            array (
                'linktype' => 'REMINDERLINK',
                'recordcount'=>JobAlerts_Record_Model::getReminderResultCount('finish'),
                'linklabel' => 'LBL_CRM_REMINDER_FINISH',
                'linkurl' => 'index.php?module=JobAlerts&view=List&public=finish',
                'linkicon' => '',
                'target'=>'_blank',
            ),
            array (
                'linktype' => 'REMINDERLINK',
                'recordcount'=>JobAlerts_Record_Model::getReminderResultCount('myreminder'),
                'linklabel' => 'LBL_CRM_REMINDER_MY_REMINDER',
                'linkurl' => 'index.php?module=JobAlerts&view=List&public=myreminder',
                'linkicon' => '',
                'target'=>'_blank',
            ),
            array (
                'linktype' => 'REMINDERLINK',
                'recordcount'=>JobAlerts_Record_Model::getReminderResultCount('relation'),
                'linklabel' => 'LBL_CRM_REMINDER_RELATION',
                'linkurl' => 'index.php?module=JobAlerts&view=List&public=relation',
                'linkicon' => '',
                'target'=>'_blank',
            )
        );
        // 提醒已读未读
        $remindLinkReadState = array(
            //未读未到期提醒
            array (
                'recordcount'=>JobAlerts_Record_Model::getReminderResultCountReadState('new'),
                'linklabel' => 'LBL_CRM_REMINDER_NEW_NOREAD',
                'linkurl' => 'index.php?module=JobAlerts&view=List&public=new',
                'target'=>'_blank',
            ),
            //'LBL_CRM_REMINDER_WAIT_NOREAD'=>'未读待处理提醒',
            array (
                'recordcount'=>JobAlerts_Record_Model::getReminderResultCountReadState('wait'),
                'linklabel' => 'LBL_CRM_REMINDER_WAIT_NOREAD',
                'linkurl' => 'index.php?module=JobAlerts&view=List&public=wait',
                'target'=>'_blank',
            ),
            //'LBL_CRM_REMINDER_MY_REMINDER_NOREAD'=>'未读待处理全部提醒',
            array (
                'recordcount'=>JobAlerts_Record_Model::getReminderResultCountReadState('myreminder'),
                'linklabel' => 'LBL_CRM_REMINDER_MY_REMINDER_NOREAD',
                'linkurl' => 'index.php?module=JobAlerts&view=List&public=myreminder',
                'target'=>'_blank',
            ),
            // 'LBL_CRM_REMINDER_RELATION_NOREAD'=>'未读全部提醒',
            array (
                'recordcount'=>JobAlerts_Record_Model::getReminderResultCountReadState('relation'),
                'linklabel' => 'LBL_CRM_REMINDER_RELATION_NOREAD',
                'linkurl' => 'index.php?module=JobAlerts&view=List&public=relation',
                'target'=>'_blank',
            ),
        );
        return array(0=>$messageLink,1=>$remindLink,$remindLinkReadState);
    }
}