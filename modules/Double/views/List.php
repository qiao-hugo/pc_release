<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Double_List_View extends Vtiger_KList_View {

    function __construct() {
        parent::__construct();
    }


    function process(Vtiger_Request $request){
        $where=getAccessibleUsers('Double','List',false);
        $db=PearDatabase::getInstance();
        if($where !='1=1'){
            $sql = "SELECT * FROM doubleaccount where  serviceid ".$where;
            $sql2 = "select
CONCAT(
CONCAT('普及版客户:',count(case productid when '396796' then 1 ELSE NULL END),'个   '),
CONCAT('白金版客户:',count(case productid when '396798' then 1 ELSE NULL END),'个   '),
CONCAT('黄金版客户:',count(case productid when '396797' then 1 ELSE NULL END),'个   ')
)AS 'num'
from vtiger_servicecontracts ser
LEFT JOIN 
vtiger_servicecomments com ON com.related_to = ser.sc_related_to AND com.assigntype = 'accountby' 
WHERE 
com.serviceid ".$where;
        }else{
            $sql = "SELECT * FROM doubleaccount ";
            $sql2 = "select
CONCAT(
CONCAT('普及版客户:',count(case productid when '396796' then 1 ELSE NULL END),'个   '),
CONCAT('白金版客户:',count(case productid when '396798' then 1 ELSE NULL END),'个   '),
CONCAT('黄金版客户:',count(case productid when '396797' then 1 ELSE NULL END),'个   ')
)AS 'num'
from vtiger_servicecontracts WHERE 1=1 ";
        }
		
		//echo $sql.'+'.$sql2;
    $result = $db->pquery($sql,array());
        $result_li = array();
        if($db->num_rows($result)>0){
            for($i=0;$i<$db->num_rows($result);$i++){
                $result_li[] =$db->fetchByAssoc($result);
            }
        }

$result2 = $db->pquery($sql2,array());
$result2_li = $db->fetchByAssoc($result2);
        $moduleName = $request->getModule();
        $viewer = $this->getViewer ($request);
        $viewer->assign('DATA', $result_li);
        $viewer->assign('NUM', $result2_li);
        $viewer->view('List.tpl', $moduleName);
}
}