<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/9/2
 * Time: 20:41
 */
class Receipt_Record_Model extends Vtiger_Record_Model
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
	) AS last_name 
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


    /*
    获取回款关联
*/
    static public function getNewinvoicerayment($invoiceid) {
        if (empty($invoiceid)) {
            return array();
        }
        $db=PearDatabase::getInstance();

        $sql = "SELECT  '1' AS data_flag,vtiger_receiptrayment.voidorredtotal,vtiger_receiptrayment.surpluinvoicetotal, vtiger_receiptrayment.receiptraymentid, vtiger_receiptrayment.servicecontractsid, vtiger_receiptrayment.receivedpaymentsid, vtiger_receiptrayment.total , vtiger_receiptrayment.arrivaldate, vtiger_receiptrayment.invoicetotal, vtiger_receiptrayment.allowinvoicetotal, vtiger_receiptrayment.invoicecontent, vtiger_receiptrayment.remarks , vtiger_receiptrayment.invoiceid, vtiger_receiptrayment.contract_no, vtiger_receiptrayment.paytitle AS t_paytitle, vtiger_receiptrayment.paytitle AS t_paytitle, CONCAT( IF(vtiger_receivedpayments.paytitle!='',vtiger_receivedpayments.paytitle,vtiger_staypayment.payer) , '[', vtiger_receivedpayments.unit_price, ']' ) AS paytitle  FROM vtiger_receiptrayment LEFT JOIN vtiger_receivedpayments ON vtiger_receiptrayment.receivedpaymentsid = vtiger_receivedpayments.receivedpaymentsid left join vtiger_staypayment on vtiger_staypayment.staypaymentid=vtiger_receivedpayments.staypaymentid WHERE invoiceid = ? AND vtiger_receiptrayment.deleted = 0";


        $sel_result = $db->pquery($sql, array($invoiceid));
        $res_cnt = $db->num_rows($sel_result);

        $data = array();
        if ($res_cnt > 0) {
            while($rawData=$db->fetch_array($sel_result)) {
                $data[] = $rawData;
            }
        }

        return $data;
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
