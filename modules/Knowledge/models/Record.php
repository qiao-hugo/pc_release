<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * Vtiger Entity Record Model Class
 */
class Knowledge_Record_Model extends Vtiger_Record_Model {
	
	static function getlistCategory(){
		$db=PearDatabase::getInstance();
		$sql="select knowledgecolumnsid,knowledgecolumns,pid from vtiger_knowledgecolumns  ORDER BY sortorderid";
		
		$result=$db->run_query_allrecords($sql);
		return self::getCategory($result);
	
	}
	static function getCategory($data,$res=array(),$pid='0'){
		foreach ($data as $k => $v){
			if($v['pid']==$pid){
				$res[$v['knowledgecolumnsid']]['info']=$v;
				$res[$v['knowledgecolumnsid']]['child']=self::getCategory($data,array(),$v['knowledgecolumnsid']);
			}
		}
		return $res;
	}
	static function getindexlist(){
		global $adb;
		$query="SELECT vtiger_knowledge.knowledgetitle, vtiger_knowledge.knowledgetop,  vtiger_knowledge.author, vtiger_knowledge.knowledgecolumns, vtiger_knowledge.knowledgedate, vtiger_knowledge.knowledgecount,vtiger_knowledge.cmdtime,

	(SELECT	CONCAT(	last_name,'[',IFNULL((SELECT departmentname	FROM vtiger_departments	WHERE departmentid = (SELECT departmentid FROM	vtiger_user2department	WHERE	userid = vtiger_users.id LIMIT 1)),	''),']',(IF (`status` = 'Active','','[离职]'))) AS last_name
		FROM
			vtiger_users
WHERE vtiger_users.id=vtiger_knowledge.author
	) AS last_name,vtiger_knowledge.knowledgeid, IFNULL((select departmentname from vtiger_departments where departmentid = vtiger_knowledge.authordepartment),'') as authordepartment FROM vtiger_knowledge LEFT JOIN vtiger_users AS vtiger_usersauthor ON vtiger_knowledge.author = vtiger_usersauthor.id WHERE vtiger_knowledge.knowledgeid > 0 AND knowledgecolumns='NewList' ORDER BY knowledgedate DESC,
	knowledgetop DESC LIMIT 0,7";
		$result=$adb->run_query_allrecords($query);
		return $result;
	}
	
	function get($key) {
		$value = parent::get($key);
		if ($key === 'knowledgecontent') {
			return decode_html($value);
		}
		return $value;
	}
}
