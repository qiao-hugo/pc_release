<?php

/* +**********
 * 目前没什么用 
 * ******* */

abstract class Vtiger_Footer_View extends Vtiger_Header_View {

	 function __construct() {
		parent::__construct();
	}

	//Note: To get the right hook for immediate parent in PHP,
	// specially in case of deep hierarchy
	/*function preProcessParentTplName(Vtiger_Request $request) {
		return parent::preProcessTplName($request);
	}*/

	function postProcess(Vtiger_Request $request) {

        /*$viewer = $this->getViewer($request);
        $WorkFlowCheck_Confirmation_Action= new WorkFlowCheck_Confirmation_Action();

        //系统消息
        $messageLink = array(
            //统计有多少条要审核的信息
            array (
                'recordcount'=>$WorkFlowCheck_Confirmation_Action->getConfirmation(),
                'linklabel' => 'LBL_CRM_MESSAGE_CONFIR',
                'linkurl' => 'index.php?module=WorkFlowCheck&view=List',
                'target'=>'_blank',
            ),
            //统计有多少条24小时待跟进拜访单
            array (
                'recordcount'=>$WorkFlowCheck_Confirmation_Action->getVisitingOrderFollowup(),
                'linklabel' => 'LBL_CRM_MESSAGE_ORDER',
                'linkurl' => 'index.php?module=VisitingOrder&view=List&public=FollowUp',
                'target'=>'_blank',
            ),
            //统计未写工作日报的人数
            array (
                'recordcount'=>$WorkFlowCheck_Confirmation_Action->getNoWrite(),
                'linklabel' => 'LBL_CRM_MESSAGE_NOWRITER',
                'linkurl' => 'index.php?module=WorkSummarize&view=List&filter=nowrite',
                'target'=>'_blank',
            ),
            //有多少要回复的工作日报记录数
            array (
                'recordcount'=>$WorkFlowCheck_Confirmation_Action->getReplynum(),
                'linklabel' => 'LBL_CRM_MESSAGE_REPLYNUM',
                'linkurl' => 'index.php?module=WorkSummarize&view=List&filter=reply',
                'target'=>'_blank',
            ),
            //超过24小时未审核信息
            array (
                'recordcount'=>$WorkFlowCheck_Confirmation_Action->getConfirmation('outnumberday'),
                'linklabel' => 'LBL_CRM_MESSAGE_OUTNUMBERDAY',
                'linkurl' => 'index.php?module=WorkFlowCheck&view=List&public=outnumberday',
                'target'=>'_blank',
            ),
            //全部未跟进客服的信息
            array (
                'recordcount'=>$WorkFlowCheck_Confirmation_Action->getSevenCustomer(),
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
                'recordcount'=>$WorkFlowCheck_Confirmation_Action->get_noservice_receivepayment(),
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

        $viewer->assign('REMINDLINK', $remindLink);
        $viewer->assign('MESSAGELINK', $messageLink);
        $viewer->assign('REMINDLINKREADSTATE', $remindLinkReadState);*/

        parent::postProcess($request);

	}
}
