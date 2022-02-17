<?php
class IronAccount_ChangeAjax_Action extends Vtiger_Action_Controller {

	function checkPermission(Vtiger_Request $request) {
		return true;
	}

	public function process(Vtiger_Request $request) {
        $related_toID=$_REQUEST['related_toID'];
        $db=PearDatabase::getInstance();
        $sql="select vtiger_servicecomments.related_to as accountid,
				(vtiger_account.accountname) as related_to,
				vtiger_servicecomments.addtime,
				IFNULL((select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where id=vtiger_servicecomments.serviceid),'--') as serviceid,
				(select last_name from vtiger_users where id=vtiger_servicecomments.assignerid) as assignerid,
				(select accountrank from vtiger_account where vtiger_account.accountid=vtiger_servicecomments.related_to) as accountrank,
				(select departmentname from vtiger_departments where vtiger_departments.departmentid=(select departmentid from vtiger_user2department where vtiger_user2department.userid=(select vtiger_crmentity.smownerid from vtiger_crmentity where vtiger_crmentity.crmid=vtiger_servicecomments.related_to and vtiger_crmentity.deleted=0))) as departmentid
              from  vtiger_servicecomments
					LEFT  join vtiger_account ON vtiger_account.accountid = vtiger_servicecomments.related_to
					LEFT JOIN  vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_servicecomments.related_to
					where 1=1 AND vtiger_servicecomments.related_to = ? ORDER BY vtiger_servicecomments.addtime desc LIMIT 1";
        $resultdb=$db-> pquery($sql,array($related_toID));
        if($db->num_rows($resultdb)>0) {
            $related_to = $db->query_result($resultdb, 0, 'related_to');
            $departmentid = $db->query_result($resultdb, 0, 'departmentid');
            $serviceid = $db->query_result($resultdb, 0, 'serviceid');
        }
        // 需要返回人员信息的
        $result['related_to']=$related_to;
        $result['departmentid']=$departmentid;
        $result['serviceid']=$serviceid;
        //在职人员
        $query="SELECT id,last_name FROM vtiger_users WHERE `status`='Active'";
       $result['names']=$db->run_query_allrecords($query);
        //业务类型
        $typeSql="SELECT servicetype FROM `vtiger_account` WHERE accountid = ?";
        $resultType=$db-> pquery($typeSql,array($related_toID));
        if ($db->num_rows($resultType)>0) {
            $result_resultType = $db->query_result($resultType,'servicetype');
            if($result_resultType!=''){

                $servicetype=explode(' |##| ', $result_resultType);
                foreach($servicetype as $value){
                    $product_result = $db->pquery(" SELECT productid,productname FROM `vtiger_products` WHERE productid = ".$value."");
//                    $result['productid'] = $db->query_result($product_result,0,'productid');
//                    $result['productname'] = $db->query_result($product_result,0,'productname');
                    $product_list[]=$db->fetchByAssoc($product_result);
                }

            }else{
                $product_list=array();
            }
            $result['product_list'] = $product_list;
        }

		$response = new Vtiger_Response();
		$response->setEmitType(Vtiger_Response::$EMIT_JSON);
		$response->setResult($result);
		$response->emit();
		
	}
	
	
}
