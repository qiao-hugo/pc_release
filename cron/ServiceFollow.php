<?php
ini_set("include_path", "../");

require_once('include/utils/utils.php');
require_once('include/logging.php');


//$log =& LoggerManager::getLogger('RecurringInvoice');
//$log->debug("invoked RecurringInvoice");

/*客服跟进天数更新设置*/
updateServiceNofollowday();

/*
 * 客服跟进天数更新
 */
function updateServiceNofollowday(){
	global $adb, $log;
	//客服跟进天数待更新数据取得
	$listQuery ="select * from (
					select 
						IFNULL((select vtiger_modcomments.addtime from vtiger_modcomments 
							where vtiger_modcomments.modulename='ServiceComments' 
							and vtiger_modcomments.moduleid=vtiger_servicecomments.servicecommentsid 
					    	and vtiger_modcomments.creatorid=vtiger_servicecomments.serviceid 
					    	ORDER BY vtiger_modcomments.addtime desc LIMIT 1),vtiger_servicecomments.addtime) as last_follow_time,
						vtiger_servicecomments.servicecommentsid,
						vtiger_servicecomments.updatetime
					from vtiger_servicecomments ) T  
			 	where EXISTS (select datetype from vtiger_workday where DATE_FORMAT(vtiger_workday.dateday,'%Y/%m/%d')=DATE_FORMAT(SYSDATE(),'%Y/%m/%d') and vtiger_workday.datetype='work')
				and (ISNULL(T.updatetime) or datediff(SYSDATE(),T.updatetime)>0)";
			    //and datediff(SYSDATE(),T.last_follow_time)>(SELECT followfrequency FROM vtiger_followfrequency LIMIT 1)";
	$result = $adb->pquery($listQuery, array());
	if (empty($result) || $adb->num_rows($result)==0){
		echo '没有要更新的数据!';
		return;
	}
	
	//客服跟进天数判断更新处理
	$updateSql = "update vtiger_servicecomments set nofollowday=nofollowday-1,updatetime=sysdate() where servicecommentsid=?";
	$num_rows=$adb->num_rows($result);
	for($i=0; $i<$num_rows; $i++) {
		$servicecommentsid=$adb->query_result($result,$i,'servicecommentsid');
		$adb->pquery($updateSql, array($servicecommentsid));
	}
	echo '更新成功!更新件数:'.$num_rows;
}
?>
