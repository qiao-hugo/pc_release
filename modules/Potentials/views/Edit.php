<?php
/*+******************
 *编辑页面的权限控制
 * 某些模块关联生成数据
 * 只能编辑不可新增
 **********************/

Class Potentials_Edit_View extends Vtiger_Index_View {
    protected $record = false;
	
	/* function __construct() {
	//暂时没用就屏蔽吧
		parent::__construct();
	}*/
	
	public function checkPermission(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$record = $request->get('record');
		$recordPermission = Users_Privileges_Model::isPermitted($moduleName, 'EditView', $record);
		if(!$recordPermission) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}
		//1.编辑权限，有上下级关系的，或者本人，或者有审核权限的人
		/* $isrejectid=false;
		if(!empty($record)){
			if(isset($_SESSION['isyourcode'])&&$_SESSION['isyourcode']==$moduleName.$record){
				//偶审核权限的人，通过isyourcode值来判断
				$isrejectid=true;
			}else{
				$user=getAccessibleUsers($moduleName,'Edit',true);

				$recordModule=Vtiger_Record_Model::getInstanceById($record, $moduleName);
				$recordField=$recordModule->getData();
				if(isset($recordField['assigned_user_id'])){
					$id=$recordField['assigned_user_id'];
				}elseif(isset($recordField['smcreatorid'])){
					$id=$recordField['smcreatorid'];
				}
				if(is_array($user)&& !in_array($id,$user)){
					throw new AppException(vtranslate('没有访问权限'));
				}
			}
			&&!$isrejectid
		} */

		//echo getAccessibleUsers();
		//young.yang 2015-1-3 增加编辑页面，对于流程状态的控制，某些状态不允许编辑
		global $isallow;

		if(in_array($moduleName, $isallow)&&$record&&!$request->isAjax()){   //部位ajax请求
			$recordModel=Vtiger_Record_Model::getInstanceById($record,$moduleName);
			if(!empty($recordModel)&&$recordModel){
				$module=$recordModel->getData();
				$moduleStatus=$module['modulestatus'];
                //young.yang 20150508 去掉审核人修改信息的权限。即将$_SESSION['isyourcode']关闭
				//if(isset($_SESSION['isyourcode'])&&$_SESSION['isyourcode']==$moduleName.$record){

				//else{
					if(!getIsEditOrDel('edit',$moduleStatus)){
						throw new AppException('状态 '.vtranslate($moduleStatus,$moduleName).' 不允许当前的操作');
					}
				//}
				
			}
		}
		//end
		
	}
	
	public function process(Vtiger_Request $request) {
		$viewer = $this->getViewer ($request);
		$moduleName = $request->getModule();
		$record = $request->get('record');
        if(!empty($record) && $request->get('isDuplicate') == true) {
            $recordModel = $this->record?$this->record:Vtiger_Record_Model::getInstanceById($record, $moduleName);
            $viewer->assign('MODE', '');
        }else if(!empty($record)) {
            $recordModel = $this->record?$this->record:Vtiger_Record_Model::getInstanceById($record, $moduleName);
            $viewer->assign('RECORD_ID', $record);
            $viewer->assign('MODE', 'edit');
        } else {
            $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
            $viewer->assign('MODE', '');
			$viewer->assign('RECORD_ID','');
        }
        if(!$this->record){
            $this->record = $recordModel;
        }
        
		$moduleModel = $recordModel->getModule();
		//读取模块的字段
		$fieldList = $moduleModel->getFields();
		
		//取交集?还不知道有什么用
		$requestFieldList = array_intersect_key($request->getAll(), $fieldList);

		if(!empty($requestFieldList)){
			foreach($requestFieldList as $fieldName=>$fieldValue){
				$fieldModel = $fieldList[$fieldName];
				$specialField = false;
				// We collate date and time part together in the EditView UI handling 
				// so a bit of special treatment is required if we come from QuickCreate 
			/* 	if ($moduleName == 'Calendar' && empty($record) && $fieldName == 'time_start' && !empty($fieldValue)) { 
					$specialField = true; 
					// Convert the incoming user-picked time to GMT time 
					// which will get re-translated based on user-time zone on EditForm 
					$fieldValue = DateTimeField::convertToDBTimeZone($fieldValue)->format("H:i"); 	
				}
				if ($moduleName == 'Calendar' && empty($record) && $fieldName == 'date_start' && !empty($fieldValue)) { 
					$startTime = Vtiger_Time_UIType::getTimeValueWithSeconds($requestFieldList['time_start']);
					$startDateTime = Vtiger_Datetime_UIType::getDBDateTimeValue($fieldValue." ".$startTime);
					list($startDate, $startTime) = explode(' ', $startDateTime);
					$fieldValue = Vtiger_Date_UIType::getDisplayDateValue($startDate);
				} */
				//|| $specialField
				if($fieldModel->isEditable()) {
					$recordModel->set($fieldName, $fieldModel->getDBInsertValue($fieldValue));
				}
			}
		}
		// 获取列表详情数据
        if($record){
            $db = PearDatabase::getInstance();
            //die();
            $DetailListQuery=$query="SELECT potentialdetailid,potentialid,potentialnames,probabilitys,salesstages,budgetinterval,budgetlockstart,budgetlockend,isannuallypay,dockingrole,docker,projectdetails FROM `vtiger_potential_detail` WHERE  isdelete=0 AND potentialid= ?  ORDER BY  potentialdetailid ASC ";
            $DetailListResult = $db->pquery($DetailListQuery, array($record));
            $detailInfo = array();
            while($raw=$db->fetch_array($DetailListResult)) {
                $detailInfo[]= $raw;
            }
            $COUNTDETAIL = count($detailInfo);
        }else{
            $detailInfo = array();
            $COUNTDETAIL=0;
        }


        $viewer->assign('COUNTD_DETAIL',$COUNTDETAIL);
        $viewer->assign('DETAIL_INFO_LIST',$detailInfo);
		$recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel,'Edit');
		$viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
		$viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());//UI字段生成位置
		$viewer->assign('MODULE', $moduleName);
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());//执行好多次，真是
		$viewer->assign('RECORD',$recordModel);//编辑页面显示不可编辑字段内容
		//$picklistDependencyDatasource = Vtiger_DependencyPicklist::getPicklistDependencyDatasource($moduleName);
		//$viewer->assign('PICKIST_DEPENDENCY_DATASOURCE','');然而有毛用
		//$viewer->assign('CURRENTDATE', date('Y-n-j'));还需要吗？
		//关联修改？
		$isRelationOperation = $request->get('relationOperation');
		$viewer->assign('IS_RELATION_OPERATION', $isRelationOperation);
		if($isRelationOperation) {
			$viewer->assign('SOURCE_MODULE', $request->get('sourceModule'));
			$viewer->assign('SOURCE_RECORD', $request->get('sourceRecord'));
		}
		//使用上传控件
		//$viewer->assign('MAX_UPLOAD_LIMIT_MB', Vtiger_Util_Helper::getMaxUploadSize());
		//$viewer->assign('MAX_UPLOAD_LIMIT', vglobal('upload_maxsize'));
		$viewer->view('EditView.tpl', $moduleName);
	}
}