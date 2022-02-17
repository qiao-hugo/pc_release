<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/9/2
 * Time: 20:41
 */
class PayApply_Record_Model extends Vtiger_Record_Model
{
    public function getPayApply($suppliercontractsid,$parentcate,$soncate){
        global $current_user;
        $db=PearDatabase::getInstance();
        $sql ="SELECT
	a.*,
	CONCAT(
	last_name,
	'[',
	IFNULL( ( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = b.id LIMIT 1 ) ), '' ),
	']',
	( IF ( `status` = 'Active', '', '[离职]' ) ) 
	) AS last_name ,
    c.parentcate as parentcatename,
    d.soncate as soncatename
FROM
	vtiger_payapply a
	LEFT JOIN vtiger_users b ON a.applyuserid = b.id 
    LEFT JOIN vtiger_parentcate c on c.parentcateid=a.parentcate
    LEFT JOIN vtiger_soncate d on d.soncateid=a.soncate
WHERE
	(";
    $where=getAccessibleUsers('SupplierContracts','List',true);
    if(!empty($where)&&$where!='1=1'){
        $where=" a.applyuserid in(".implode(',',$where).") ";
    }
    //$where=!empty($where)?$where:array(-1);
    $sql.=$where;
	$sql.=" AND a.modulestatus = 'c_complete'
            AND a.startdate <=? AND a.enddate >=? 
            AND c.parentcate=?  AND d.soncate=? and a.isused!=1 )
        ";
	if($suppliercontractsid){
            $result2 =$db->pquery("select modulestatus,payapplyids from vtiger_suppliercontracts where suppliercontractsid=?",array($suppliercontractsid));
            if($db->num_rows($result2)){
                $row = $db->fetchByAssoc($result2,0);
//                if($row['modulestatus']!='a_normal'){
                    $sql .= ' or (payapplyid in ('.$row['payapplyids'].'))';
//                }
            }
        }
        $result =$db->pquery($sql,array(date("Y-m-d"),date("Y-m-d"),$parentcate,$soncate));
        if(!$db->num_rows($result)){
            return array();
        }
        while ($row=$db->fetchByAssoc($result)){
            if($row['parentcatename']!=$parentcate || $row['soncatename']!=$soncate){
                continue;
            }
            $datas[]=$row;
        }
        return $datas;
    }

    public function getPayApplyListByIds($ids){
        $db = PearDatabase::getInstance();
        $sql = "SELECT
	a.*,
	CONCAT(
	last_name,
	'[',
	IFNULL( ( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = b.id LIMIT 1 ) ), '' ),
	']',
	( IF ( `status` = 'Active', '', '[离职]' ) ) 
	) AS last_name 
FROM
	vtiger_payapply a
	LEFT JOIN vtiger_users b ON a.applyuserid = b.id 
WHERE
	a.payapplyid in (".implode(',',$ids).')';
        $result =$db->pquery($sql,array());
        if(!$db->num_rows($result)){
            return array();
        }
        while ($row=$db->fetchByAssoc($result)){
            $datas[]=$row;
        }
        return $datas;

    }


    function mobileDetail(Vtiger_Request $request) {
        $userid = $request->get("userid");
        $id = $request->get("id");
        global $adb,$current_user;
        $user = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
        //工单详情信息
        $sql = "SELECT
	a.*,
	b.soncate AS soncatename,
	c.parentcate AS parentcatename,
	(
SELECT
	CONCAT(
	last_name,
	'[',
	IFNULL( ( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ), '' ),
	']',
	( IF ( `status` = 'Active', '', '[离职]' ) ) 
	) AS label 
FROM
	vtiger_users 
WHERE
	id = a.applyuserid 
	LIMIT 1 
	) AS applyuser 
FROM
	vtiger_payapply a
	LEFT JOIN vtiger_soncate b ON a.soncate = b.soncateid
	LEFT JOIN vtiger_parentcate c ON c.parentcateid = a.parentcate
	LEFT JOIN vtiger_users d ON d.id = a.applyuserid 
WHERE
	a.payapplyid =?";
        $sel_result = $adb->pquery($sql, array($id) );
        $res_cnt = $adb->num_rows($sel_result);
        $row = array();
        if($res_cnt > 0) {
            $row = $adb->query_result_rowdata($sel_result, 0);
        }
        $fieldname=array(
            'id' => $id,
            'module' => 'PayApply',
            'record' => $id,
        );
        // 工作流
        $tt = $this->getWorkflowsMobile(new Vtiger_Request($fieldname, $fieldname));
        return array('PayApply'=>$row, 'workflows'=>$tt);
    }
}
