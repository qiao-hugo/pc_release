<?php
/**
 * 数据字典生成代码，2014-10-13 by young.yang
 */
header("Content-type:text/html;charset=utf-8;");
require_once('include/database/PearDatabase.php');//数据库链接
require_once('include/utils/CommonUtils.php');//统一工具
require_once('include/FormValidationUtil.php');
require_once('include/events/SqlResultIterator.inc');//迭代器


//读取数据库
$db = PearDatabase::getInstance();
$path="C:/Program Files/vtigercrm600/apache/htdocs/vtigerCRM/languages/zh_cn";

//获取中文翻译
function getfile($path){
	$current_dir = opendir($path);
	$arr=array();
	while (false !== ($file = readdir($current_dir))) {
		if ($file != "." && $file != ".."&&is_file($path.'/'.$file)) {
			
			include($path.'/'.$file);
			if(is_array($languageStrings)&&!empty($languageStrings)){
				$arr=array_merge($arr,$languageStrings);
				unset($languageStrings);
			}
		}
    }
	closedir($current_dir);
	return $arr;
}
 

$arr=getfile($path);

//翻译label和字段关联
$result=$db->pquery('select fieldname,fieldlabel from vtiger_field',null);
$re=array();
while($row = $db->fetchByAssoc($result)){
	//echo $row['fieldlabel'];
	if(array_key_exists($row['fieldlabel'],$arr)){
		$re[$row['fieldname']]=$arr[$row['fieldlabel']];
	}
}

//字段表注释
$fields=$db->pquery("select COLUMN_NAME,COLUMN_TYPE,COLUMN_KEY,TABLE_NAME,COLUMN_COMMENT from information_schema.COLUMNS where TABLE_SCHEMA='vtigercrm600' order by TABLE_NAME",null);
$datadic=array();
$i=0;
while($rowr = $db->fetch_array($fields)){
	if(array_key_exists($rowr[0],$re)){
		$datadic[$i]['LANG']=$re[$rowr[0]];
	}else{
		$datadic[$i]['LANG']='';
	}
	$datadic[$i]['COLUMN_NAME']=$rowr[0];
	$datadic[$i]['COLUMN_TYPE']=$rowr[1];
	$datadic[$i]['COLUMN_KEY']=$rowr[2];
	$datadic[$i]['TABLE_NAME']=$rowr[3];
	$datadic[$i]['COLUMN_COMMENT']=$rowr[4];
	$i++;
}
include_once('tables.php');
$str='<table width=100%>';
$str.='<tr><td>字段名称</td><td>类型</td><td>表名</td><td>语言翻译/表字段注释</td><td>是否主键</td></tr>';
$temptablename='abc';
foreach($datadic as $val){
	$str.="<tr><td>".$val['COLUMN_NAME']."</td><td>".$val['COLUMN_TYPE']."</td><td>".(($temptablename==$val['TABLE_NAME'])?$val['TABLE_NAME']:(empty($tables[$val['TABLE_NAME']])?$val['TABLE_NAME']:($val['TABLE_NAME'].'-'.$tables[$val['TABLE_NAME']])))."</td><td>{$val['LANG']}/{$val['COLUMN_COMMENT']}</td><td>{$val['COLUMN_KEY']}</td></tr>";
	$temptablename=$val['TABLE_NAME'];
}
$str.='</table>';

echo $str;
echo '2222';
?>
