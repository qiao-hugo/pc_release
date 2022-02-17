<?php

/**
	 * 后台缓存数据控制
	 * module/action索引权限
	 * @return success or error
	 */
	 
class Settings_DeleteData_Index_View extends Settings_Vtiger_Index_View {

	public function process(Vtiger_Request $request) {
			
		global $adb;
		echo '数据不允许删除';
		return;
		//客户信息
		$arr=array();
		$arr['vtiger_account']="truncate table  vtiger_account";
		$arr['vtiger_accountbillads']="truncate table  vtiger_accountbillads";
		$arr['vtiger_accountscf']="truncate table  vtiger_accountscf";
		$arr['vtiger_accountshipads']="truncate table  vtiger_accountshipads";
		
		$arr['vtiger_activity']="truncate table  vtiger_activity";
		$arr['vtiger_activity_reminder']="truncate table  vtiger_activity_reminder";
		$arr['vtiger_activity_reminder_popup']="truncate table  vtiger_activity_reminder_popup";
		$arr['vtiger_activitycf']="truncate table  vtiger_activitycf";
		
		
		$arr['vtiger_assets']="truncate table  vtiger_assets";
		$arr['vtiger_assetscf']="truncate table  vtiger_assetscf";
		$arr['vtiger_campaign']="truncate table  vtiger_campaign";
		
		//联系人
		$arr['vtiger_contactsubdetails']="truncate table  vtiger_contactsubdetails";
		$arr['vtiger_contactaddress']="truncate table  vtiger_contactaddress";
		$arr['vtiger_contactdetails']="truncate table  vtiger_contactdetails";
		$arr['vtiger_contactscf']="truncate table  vtiger_contactscf";
		
		//数据
		//$arr['vtiger_crmentity']="delete from  vtiger_crmentity where setype not in('Products','Project','ProjectTask','ProjectMilestone','Workflows','WorkflowStages')";
		$arr['vtiger_crmentity']="delete from  vtiger_crmentity where crmid>10000";
		$arr['vtiger_crmentityrel']="delete from  vtiger_crmentityrel where crmid not in(select crmid from vtiger_crmentity)";
		
		//合同
		$arr['vtiger_servicecontracts']="truncate table  vtiger_servicecontracts";
		
		//搜索
		$arr['vtiger_customview']="delete from vtiger_customview where viewname!='All'";
		$arr['vtiger_cvadvfilter']="delete from vtiger_cvadvfilter where cvid not in(select cvid from vtiger_customview)";
		$arr['vtiger_cvcolumnlist']="delete from vtiger_cvcolumnlist where cvid not in(select cvid from vtiger_customview)";
		$arr['vtiger_cvcolumnlist']="delete from vtiger_cvcolumnlist where cvid not in(select cvid from vtiger_customview)";
		
		//数据转移
		$arr['vtiger_datatransfer']="truncate table  vtiger_datatransfer";
		$arr['vtiger_files']="truncate table  vtiger_files";
		$arr['vtiger_notes']="truncate table  vtiger_notes";
		$arr['vtiger_notescf']="truncate table  vtiger_notescf";
		$arr['vtiger_senotesrel']="truncate table  vtiger_senotesrel";
		
		//发票
		$arr['vtiger_invoice']="truncate table  vtiger_invoice";
		$arr['vtiger_invoicecf']="truncate table  vtiger_invoicecf";
		
		//提醒
		$arr['vtiger_jobalerts']="truncate table  vtiger_jobalerts";
		$arr['vtiger_jobalertsreminder']="truncate table  vtiger_jobalertsreminder";
		
		
		//登录日志
		$arr['vtiger_loginhistory']="truncate table  vtiger_loginhistory";
		
		//跟进
		$arr['vtiger_modcomments']="truncate table  vtiger_modcomments";
		$arr['vtiger_modcommentscf']="truncate table  vtiger_modcommentscf";
		
		
		//销售机会
		$arr['vtiger_potential']="truncate table  vtiger_potential";
		$arr['vtiger_potentialscalesrel']="truncate table  vtiger_potentialscalesrel";
		$arr['vtiger_potentialscf']="truncate table  vtiger_potentialscf";
		
		//报价单
		$arr['vtiger_quotes']="truncate table  vtiger_quotes";
		$arr['vtiger_quotescf']="truncate table  vtiger_quotescf";
		
		//回款
		$arr['vtiger_receivedpayments']="truncate table  vtiger_receivedpayments";
		
		//工单
		$arr['vtiger_salesorder']="truncate table  vtiger_salesorder";
		$arr['vtiger_salesordercf']="truncate table  vtiger_salesordercf";
		$arr['vtiger_salesorderhistory']="truncate table  vtiger_salesorderhistory";
		$arr['vtiger_salesorderproductsrel']="truncate table  vtiger_salesorderproductsrel";
		$arr['vtiger_salesorderprojecttasksrel']="truncate table  vtiger_salesorderprojecttasksrel";
		$arr['vtiger_salesorderworkflowstages']="truncate table  vtiger_salesorderworkflowstages";
		$arr['vtiger_seproductsrel']="delete from vtiger_seproductsrel where setype not in('Products')";
		
		//数据日志
		$arr['vtiger_sqltimelog']="truncate table  vtiger_sqltimelog";
		
		//客服
		$arr['vtiger_servicecomments']="truncate table  vtiger_servicecomments";
		$arr['vtiger_servicecomplaints']="truncate table  vtiger_servicecomplaints";
		$arr['vtiger_servicecontracts']="truncate table  vtiger_servicecontracts";
		$arr['vtiger_servicecontractscf']="truncate table  vtiger_servicecontractscf";
		$arr['vtiger_servicemaintenance']="truncate table  vtiger_servicemaintenance";
		
		//拜访单
		$arr['vtiger_visitingorder']="truncate table  vtiger_visitingorder";
		
		//商务设置
		$arr['vtiger_salemanager']="truncate table  vtiger_salemanager";
		
		//任务包
		$arr['vtiger_servicetask']="truncate table  vtiger_servicetask";
		//任务
		$arr['vtiger_taskpackage']="truncate table  vtiger_taskpackage";
		
		//更新履历表
		$arr['vtiger_modtracker_detail']="truncate table  vtiger_modtracker_detail";
		
		//用户表
		$arr['vtiger_users']="delete from vtiger_users where user_name<>'admin'";
		
		$count=count($arr);
		$i=0;
		foreach($arr as $key=>$val){
			//$adb->pquery($val);
			$i=$i+1;
			echo "一共$count条需要清理的数据当前已经清理$i<br>";
		}
		echo '清理完成';
	
	} 
}