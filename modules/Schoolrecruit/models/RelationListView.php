<?php
/*
*定义管理语句
*/
class Schoolrecruit_RelationListView_Model extends Vtiger_RelationListView_Model {
	static $relatedquerylist = array(

		'Schoolresume'=>"select * from vtiger_schoolresume where schoolrecruitid=?",
        'Schoolvisit'=>'SELECT vtiger_schoolvisit.subject, (vtiger_school.schoolname) as schoolid,vtiger_schoolvisit.schoolid as schoolid_reference,vtiger_schoolvisit.contacts,vtiger_schoolvisit.purpose,(select CONCAT(last_name,\'[\',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),\'\'),\']\',(if(`status`=\'Active\',\'\',\'[离职]\'))) as last_name from vtiger_users where vtiger_schoolvisit.extractid=vtiger_users.id) as extractid,vtiger_schoolvisit.accompany,vtiger_schoolvisit.startdate,vtiger_schoolvisit.enddate,vtiger_schoolvisit.remark,vtiger_schoolvisit.destination,vtiger_schoolvisit.outobject, (vtiger_workflows.workflowsname) as workflowsid,vtiger_schoolvisit.workflowsid as workflowsid_reference,vtiger_schoolvisit.modulestatus, (vtiger_schoolrecruit.recruitname) as schoolrecruitid,vtiger_schoolvisit.schoolrecruitid as schoolrecruitid_reference,vtiger_schoolvisit.schoolvisitid,vtiger_crmentity.crmid FROM vtiger_schoolvisit LEFT JOIN vtiger_crmentity ON vtiger_schoolvisit.schoolvisitid = vtiger_crmentity.crmid LEFT JOIN vtiger_school ON vtiger_school.schoolid=vtiger_schoolvisit.schoolid LEFT JOIN vtiger_workflows ON vtiger_workflows.workflowsid=vtiger_schoolvisit.workflowsid LEFT JOIN vtiger_schoolrecruit ON vtiger_schoolrecruit.schoolrecruitid=vtiger_schoolvisit.schoolrecruitid WHERE vtiger_crmentity.deleted=0 AND vtiger_schoolrecruit.schoolrecruitid=? ORDER BY schoolvisitid DESC',
		'Schoolqualifiedpeople'=>"SELECT vtiger_crmentity.crmid, vtiger_schoolresume. NAME AS qualifiedpeoplename, IF ( vtiger_schoolresume.gendertype = 'MALE', '男', '女') AS gendertype, vtiger_schoolresume.telephone, vtiger_schoolresume.email, vtiger_schoolqualifiedpeople.is_report, vtiger_schoolqualifiedpeople.is_train, vtiger_schoolqualifiedpeople.is_trainok, vtiger_schoolqualifiedpeople.is_assessment, vtiger_schoolresume.schoolresumeid FROM vtiger_schoolqualifiedpeople LEFT JOIN vtiger_crmentity ON vtiger_schoolqualifiedpeople.schoolqualifiedpeopleid = vtiger_crmentity.crmid LEFT JOIN vtiger_schoolresume ON vtiger_schoolqualifiedpeople.schoolresumeid = vtiger_schoolresume.schoolresumeid WHERE vtiger_schoolresume.schoolrecruitid = ?",
        'Invoicesign'=>"SELECT ? as crmid",
    );

	public function getEntries($pagingModel){
		$relatedModuleName=$_REQUEST['relatedModule'];
		$moduleName = $_REQUEST['module'];
		$relatedquerylist=self::$relatedquerylist;
		
		if(isset($relatedquerylist[$relatedModuleName])){
			$parentId = $_REQUEST['record'];
			$this->relationquery=str_replace('?',$parentId,$relatedquerylist[$relatedModuleName]);
		}

		return $this->getEntries_implement($pagingModel);
	}


	public function getEntries_implement($pagingModel) {
		$db = PearDatabase::getInstance();
		$parentModule = $this->getParentRecordModel()->getModule();
		$relationModule = $this->getRelationModel()->getRelationModuleModel();
		$relatedColumnFields = $relationModule->getConfigureRelatedListFields();
		if(count($relatedColumnFields) <= 0){
			$relatedColumnFields = $relationModule->getRelatedListFields();
		}
		$query = $this->getRelationQuery();
		//echo $query;die;
		//取消分页
		$limitQuery = $query;
		$result = $db->pquery($limitQuery, array());
		$relatedRecordList = array();
		//客户详情联系人关联加入首要联系人
		if($relationModule->get('name')=='Contacts' && $_REQUEST['view']=='Detail'){
			$info=$db->pquery('select * from vtiger_account where accountid=? limit 1',array($_REQUEST['record']));
			$data=$db->query_result_rowdata($info);
			$add=array('account_id' => $data['accountid'],'name' => $data['linkname'],'gendertype' => $data['gender'],'phone' => $data['mobile'],'title' => $data['title'],'makedecisiontype' => $data['makedecision'],'email' =>$data['email1'],'assigned_user_id'=>$data['smownerid']);
			$record = Vtiger_Record_Model::getCleanInstance('Contacts');
            $record->setData($add)->setModuleFromInstance($relationModule);
            $record->setId($data['accountid']);
			$relatedRecordList[0] = $record;
		}

		for($i=0; $i< $db->num_rows($result); $i++ ) {
			$row = $db->fetch_row($result,$i);
			//$row['down_id'] = base64_encode($row['attachmentsid']);

			$record = Vtiger_Record_Model::getCleanInstance($relationModule->get('name'));

            $record->setData($row)->setModuleFromInstance($relationModule);
            if (empty($row['crmid'])) {

            	if ($_REQUEST['relatedModule'] == 'Files') {
            		$record->setId($row['attachmentsid']);
            		$row['crmid'] = $row['attachmentsid'];
            	}
            } 
            //echo $relationModule;die;
            if ($relationModule->get('name') == 'Schoolresume') {
            	$record->setId($row['schoolresumeid']);
				$relatedRecordList[$row['schoolresumeid']] = $record;
            } else {
            	$record->setId($row['crmid']);
			
				$relatedRecordList[$row['crmid']] = $record;
            }

            
		}
		//print_r($relatedRecordList);die;

	/* 	$pagingModel->calculatePageRange($relatedRecordList);
		$nextLimitQuery = $query. ' LIMIT '.($startIndex+$pageLimit).' , 1';
		$nextPageLimitResult = $db->pquery($nextLimitQuery, array());
		if($db->num_rows($nextPageLimitResult) > 0){$pagingModel->set('nextPageExists', true);}else{$pagingModel->set('nextPageExists', false);} */

		return $relatedRecordList;
	}

	// 根据后缀名 返回文件类型
	/*public function getFileType($flie_name) {
		$tt = array(
			'txt'=>'文本',
			'doc'=>'word',
			'docx'=>'word',
			'jpg'=>'图片',
			'gif'=>'图片',
			'png'=>'图片',
			'rar'=>'rar压缩包',
			'zip'=>'zip压缩包',
			'pdf'=>'pdf文档',
			'mp3'=>'mp3',
			'sql'=>'数据库文件',
			'xlsx'=>'execl'
		);

		$aa = explode('.', $flie_name);
		if (count($aa) > 1) {
			$b = strtolower($aa[count($aa) - 1]);
			return $tt[$b] ? $tt[$b] : $b;
		}
		return '';
	}*/
}