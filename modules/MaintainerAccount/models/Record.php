<?php
/*+********
 *客户信息管理
 **********/

class MaintainerAccount_Record_Model extends Vtiger_Record_Model {
    public static function getAccSql(){
        return $query = "SELECT \n".
            " sc_related_to \n".
            "FROM `vtiger_salesorderproductsrel` \n".
            "LEFT JOIN vtiger_servicecontracts  ON vtiger_salesorderproductsrel.servicecontractsid = vtiger_servicecontracts.servicecontractsid\n".
            "LEFT JOIN vtiger_products  ON vtiger_salesorderproductsrel.productid = vtiger_products.productid\n".
            "LEFT JOIN vtiger_account   ON vtiger_account.accountid = vtiger_servicecontracts.sc_related_to\n".
            "LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_servicecontracts.sc_related_to\n".
            "LEFT JOIN vtiger_servicecomments ON related_to = sc_related_to AND assigntype = 'accountby'\n".
            "WHERE accountrank IN ('iron_isv','bras_isv','silv_isv','gold_isv') AND YEAR(signdate) >= '2013' ";
    }
    public  static function getlistviewsql(){
        return $listQuery ="SELECT \n".
            "vtiger_servicecontracts.servicecontractsid,\n".
            "sc_related_to AS '客户编号',\n".
            "accountrank AS'客户等级',\n".
            "industry AS '行业',\n".
            //"vtiger_salesorderproductsrel.endtime AS '产品到期时间',\n".
            "vtiger_servicecontracts.due_date AS '产品到期时间',\n".
            "contract_no AS'合同编号',\n".
            "( SELECT IFNULL(( SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE\n".
            "				id = (signid)),'--'))AS '签单人' ,\n".
            "signid,\n".
            "productname AS'产品名称',\n".
            "signdate AS '签单时间',\n".
            "YEAR(signdate)+1 AS '下一年签单日期',\n".
            "servicecontractstype AS '新增续费',\n".
            "(SELECT modcom.commentcontent FROM vtiger_modcomments modcom WHERE modcom.modcommentsid = \n".
            "(SELECT com.modcommentsid FROM vtiger_servicecomments com WHERE com.related_to = sc_related_to AND com.assigntype = 'accountby' ORDER BY com.addtime DESC LIMIT 1)\n".
            ") AS '最新的回访内容',\n".
            "(SELECT modcom.addtime FROM vtiger_modcomments modcom WHERE modcom.modcommentsid = \n".
            "(SELECT com.modcommentsid FROM vtiger_servicecomments com WHERE com.related_to = sc_related_to AND com.assigntype = 'accountby' ORDER BY com.addtime DESC LIMIT 1)\n".
            ") AS '最新的回访时间',\n".
            "(SELECT job.alertcontent FROM vtiger_jobalerts job LEFT JOIN vtiger_servicecomments sercom ON sercom.modcommentsid = job.moduleid WHERE sercom.assigntype = \"accountby\" AND sercom.related_to = sc_related_to ORDER BY sercom.servicecommentsid DESC LIMIT 1) AS '下次回访内容', \n".
            "(SELECT job.alerttime    FROM vtiger_jobalerts job LEFT JOIN vtiger_servicecomments sercom ON sercom.modcommentsid = job.moduleid WHERE sercom.assigntype = \"accountby\" AND sercom.related_to = sc_related_to ORDER BY sercom.servicecommentsid DESC LIMIT 1) AS '下次回访时间',    \n".
            "(SELECT com.serviceid FROM vtiger_servicecomments com WHERE com.related_to = sc_related_to AND com.assigntype = 'accountby' ORDER BY com.addtime DESC LIMIT 1) AS serviceid,\n".
            "(SELECT com.addtime FROM vtiger_servicecomments com WHERE com.related_to = sc_related_to AND com.assigntype = 'accountby' ORDER BY com.addtime DESC LIMIT 1) AS '客户登记时间',\n".
            "( SELECT IFNULL(( SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE\n".
            "				id = (SELECT com.serviceid FROM vtiger_servicecomments com WHERE com.related_to = sc_related_to AND com.assigntype = 'accountby' ORDER BY com.addtime DESC LIMIT 1)),'--'))AS '客服',\n".
            "(SELECT com.remark    FROM vtiger_servicecomments com WHERE com.related_to = sc_related_to AND com.assigntype = 'accountby' ORDER BY com.addtime DESC LIMIT 1)AS '客服备注', \n".
            "accountname, \n".
            " smownerid,\n".
            "( SELECT IFNULL(( SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE\n".
            "				id = vtiger_crmentity.smownerid),'--'))AS '商务' \n".
            "FROM `vtiger_salesorderproductsrel` \n".
            "LEFT JOIN vtiger_servicecontracts  ON vtiger_salesorderproductsrel.servicecontractsid = vtiger_servicecontracts.servicecontractsid\n".
            "LEFT JOIN vtiger_products  ON vtiger_salesorderproductsrel.productid = vtiger_products.productid\n".
            "LEFT JOIN vtiger_account   ON vtiger_account.accountid = vtiger_servicecontracts.sc_related_to\n".
            "LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_servicecontracts.sc_related_to\n".
            "LEFT JOIN vtiger_servicecomments ON related_to = sc_related_to AND assigntype = 'accountby'\n".
            "WHERE accountrank IN ('iron_isv','bras_isv','silv_isv','gold_isv') AND YEAR(signdate) >= '2013' 
            AND vtiger_salesorderproductsrel.multistatus in(0,3)";
    }
}
