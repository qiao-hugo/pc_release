<?php
header("Content-type:text/html;charset=utf-8;");
require_once('include/database/PearDatabase.php');//数据库链接
require_once('include/utils/CommonUtils.php');//统一工具
require_once('include/FormValidationUtil.php');
require_once('include/events/SqlResultIterator.inc');//迭代器

//工作日插入处理
insertWorkDays('2015-01-01','2015-12-31');

/**
 * 工作日数据插入处理
 * @param unknown $startDate
 * @param unknown $endDate
 */
function insertWorkDays($startDate,$endDate){
	global $adb;

	//插入处理
	$dateday=$startDate;
	$row=0;
	while($dateday<=$endDate){
		$result=$adb->pquery('SELECT * FROM vtiger_workday where dateday=?', array($dateday));
		if (!empty($result) && $adb->num_rows($result)>0) continue;
		
		//获取id
		$id=$adb->getUniqueID("vtiger_workday");
		$insertSql = "insert into vtiger_workday(dateday,datetype,workdayid) values(?,?,?)";
		//获取日期和类型
		$params=getDateAndTypeInfo($dateday);
		$params[]=$id;
		$adb->pquery($insertSql, $params);

		$row++;
		echo "日期:$dateday,类型:$params[1]<br>";
		//日期加1
		$dateday=date("Y-m-d",strtotime("$dateday +1 day"));
	}
	echo "保存成功!件数:[$row]";
}
/**
 * 获取日期和类型信息
 * @param unknown $date
 * @return multitype:unknown Ambigous <string>
 */
function getDateAndTypeInfo($date) {
	$datearr = explode("-",$date);     //将传来的时间使用“-”分割成数组
	$year = $datearr[0];       //获取年份
	$month = sprintf('%02d',$datearr[1]);  //获取月份
	$day = sprintf('%02d',$datearr[2]);      //获取日期
	$hour = $minute = $second = 0;   //默认时分秒均为0
	$dayofweek = mktime($hour,$minute,$second,$month,$day,$year);    //将时间转换成时间戳
	$wk = date("w",$dayofweek);      //获取星期值
	$week=array(0=>'holiday',1=>'work',2=>'work',3=>'work',4=>'work',5=>'work',6=>'holiday');
	return array($date,$week[$wk]);
}

