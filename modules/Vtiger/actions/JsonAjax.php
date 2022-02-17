<?php
class Vtiger_JsonAjax_Action extends Vtiger_Action_Controller {
	
	function __construct(){
		parent::__construct();
		$this->exposeMethod('getDepartments');
		$this->exposeMethod('getSalesOrderWorkflows');
		$this->exposeMethod('commoncall');
		$this->exposeMethod('getListViewCount');
	}
	function checkPermission(Vtiger_Request $request) {
		return;
	}

	public function process(Vtiger_Request $request) {
		$mode = $request->getMode();
		$type = $request->get('type');
		$result=array();
		//$module = $request->get('module');
		if(!empty($mode)){
			$result = $this->invokeExposedMethod($mode,$request);
		}
		$responsetype=Vtiger_Response::$EMIT_JSON;
		if($type=='html'){
			$responsetype=Vtiger_Response::$EMIT_JSONTEXT;
		}
		$response = new Vtiger_Response();
		$response->setResult($result);
		$response->setEmitType($responsetype);
		$response->emit();
	}
	//获得部门信息
	public function getDepartments(Vtiger_Request $request){
		//$moduleNma=$request->get('module');
		$recordIds=$request->get('records');
		$db = PearDatabase::getInstance();
		$recordIds=rtrim(implode(',',array_unique(explode(',', $recordIds))),',');//去重
		
		$sql = "SELECT userid, vtiger_departments.departmentid, departmentname, parentdepartment FROM vtiger_user2department INNER JOIN vtiger_departments ON vtiger_user2department.departmentid = vtiger_departments.departmentid WHERE vtiger_user2department.userid IN ($recordIds)";
		
		$result=$db->pquery($sql,array());
		$counts=$db->num_rows($result);
		if($db->num_rows($result)){
			while($row = $db->fetchByAssoc($result)){
				$temp[$row['userid']]=$row;
			}
		}
		return $temp;
	}
	//获得订单工作流
	public function getSalesOrderWorkflows(Vtiger_Request $request){
		$recordIds=$request->get('records');
		$db = PearDatabase::getInstance();
		$recordIds=rtrim(implode(',',array_unique(explode(',', $recordIds))),',');//去重
		$moduleName = $request->get('module');
		
		$modelClassName = Vtiger_Loader::getComponentClassName('Model', 'Record', $moduleName);
		$instance = new $modelClassName();
		$result = $instance->getAllSalesorderWorkflowStages($recordIds);
		return $result;
	}
	//获取跟进评论
	public function getSubModCommonts(Vtiger_Request $request){
		$recordIds=$request->get('records');
		$db = PearDatabase::getInstance();
		$recordIds=rtrim(implode(',',array_unique(explode(',', $recordIds))),',');//去重
		$moduleName = $request->get('module');
		
		$modelClassName = Vtiger_Loader::getComponentClassName('Model', 'Record', $moduleName);
		$instance = new $modelClassName();
		$result = $instance->getSubModcomments($recordIds);
		return $result;
	}
	//获取提醒
	public function getSubAlerts(Vtiger_Request $request){
		
	}
	
	public function invokeExposedMethod() {
		$parameters = func_get_args();
		$name = array_shift($parameters);
		if (!empty($name) && $this->isMethodExposed($name)) {
			return call_user_func_array(array($this, $name), $parameters);
		}
		throw new Exception(vtranslate('LBL_NOT_ACCESSIBLE'));
	}
	public function commoncall(Vtiger_Request $request){
		$db = PearDatabase::getInstance();
		$moduleName=$request->get('module');
		$recordIds=$request->get('records');
		//$tabinfo=Vtiger_Functions::getEntityModuleInfo($moduleName);
// 		if(!empty($tabinfo)){
// 			$sql="select %s from %s where %s";
// 			$recordIds=implode(',',array_unique(explode(',', $recordIds)));//去重
			
// 			$module = CRMEntity::getInstance($moduleName);
// 			$sql=sprintf($sql,'*',$tabinfo['tablename'],$module->table_index.' in ('.$recordIds.')');
// 			$result=$db->pquery($sql,array());
// 			//$db->query_result($result, $row)
// 		}
		
		return '';
	}
	public function getListViewCount($request){
        global $currentView;
	    $excludeModule=array('Users');
	    $moduleName=$request->get('module');
	    $parent=$request->get('parent');
        $request->set('view','List');
        $searchKey=$request->get('search_key');
        $searchValue=$request->get('search_value');
        $searchValue=trim($searchValue);
        $src_module=$request->get('src_module');
        $src_module=trim($src_module);
        if($parent=='Settings' && !in_array($moduleName,$excludeModule)){
            $listViewModel = Settings_Vtiger_ListView_Model::getInstance($parent.':'.$moduleName);
        }elseif(!empty($src_module))  {
            $listViewModel = Vtiger_ListView_Model::getInstanceForPopup($moduleName);
            $listViewModel->set('src_module', $src_module);
            $listViewModel->set('view','Popup');
        }else{
            $listViewModel = Vtiger_ListView_Model::getInstance($moduleName);
            $currentView='List';
        }
        $listViewModel->isAllCount=1;
        if(!empty($searchValue)){
            $listViewModel->set('search_key', $searchKey);
            $listViewModel->set('search_value', $searchValue);
        }
        if($parent=='Settings'){
            return $listViewModel->getListViewCount();
        }
        $listViewModel->getSearchWhere();
        $queryGenerator = $listViewModel->get('query_generator');
        $queryGenerator->getSearchWhere();
        return $listViewModel->getListViewCount();
    }
}
