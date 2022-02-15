<?php
class SalesOrder_ChangeAjax_Action extends Vtiger_Action_Controller {

	function checkPermission(Vtiger_Request $request) {
		return true;
	}

	public function process(Vtiger_Request $request) {
		$swid=$_REQUEST['record'];
		$db=PearDatabase::getInstance();

        //young 2015-05-20 做一步兼容，在当前为非产品的节点的时候走工作流的客服分配判断
        // ServiceCheck 分配下属和指定客户 可给客服部
        // NextCheck 分配下属和指定上级审核 可给技术部门
        global $current_user;
        $sql="SELECT vtiger_workflowstages.handleaction as customer,iseditdata,isnextnode,vtiger_workflowstages.workflowstagesflag 	FROM 	vtiger_salesorderworkflowstages left JOIN vtiger_workflowstages ON (vtiger_salesorderworkflowstages.workflowstagesid=vtiger_workflowstages.workflowstagesid) WHERE vtiger_salesorderworkflowstages.salesorderworkflowstagesid =? LIMIT 1";
        $resultdb=$db-> pquery($sql,array($swid));
        if($db->num_rows($resultdb)>0) {
            $customer = $db->query_result($resultdb, 0, 'customer');
            $iseditdata = $db->query_result($resultdb, 0, 'iseditdata');
            $isnextnode = $db->query_result($resultdb, 0, 'isnextnode');
            $workflowstagesflag = $db->query_result($resultdb, 0, 'workflowstagesflag');
            // 产品负责人的节点，没有办法跟数据和指定下个节点同时有效，只能增加两个节点来判断是否将他指定
            if($iseditdata){
                $customer= 'DataCheck';
            }
            if($isnextnode){
                $customer= 'NextCheck';
            }
        }
        // 需要返回人员信息的
		$result['customer']=$customer;
        $result['iseditdata']=$iseditdata;
        $result['workflowstagesflag']=$workflowstagesflag;
		if(in_array($customer,array('NextCheck'))){ //下个节点只能是自己和上级
			$where=getAccessibleUsers('ServiceContracts','List');
			if($where!='1=1'){ //非管理员的，可以指定下属，自己和自己的直属上级
			 	$query="SELECT id,CONCAT(last_name,'[直属上级]') as last_name FROM vtiger_users WHERE  id= (select tt.reports_to_id from vtiger_users as tt where tt.id=".$current_user->id.") UNION SELECT id,last_name FROM vtiger_users WHERE `status`='Active' AND id {$where}";
			}else{
                $query="SELECT id,last_name FROM vtiger_users WHERE `status`='Active'";
            }
			$result['names']=$db->run_query_allrecords($query);
		}elseif(in_array($customer,array('ServiceCheck'))){  //客服分配可以给到公司所有人员的查看权限
            $query="SELECT id,last_name FROM vtiger_users WHERE `status`='Active'";
            $result['names']=$db->run_query_allrecords($query);
        }else{
            $result['names'] = array();
        }
		$response = new Vtiger_Response();
		$response->setEmitType(Vtiger_Response::$EMIT_JSON);
		$response->setResult($result);
		$response->emit();
		
	}
	
	
}
