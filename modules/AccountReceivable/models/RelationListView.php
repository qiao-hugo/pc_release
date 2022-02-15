<?php
/*
*定义管理语句
*/
class AccountReceivable_RelationListView_Model extends Vtiger_RelationListView_Model {
	static $relatedquerylist = array(
	'ReceivedPayments'=>'SELECT vtiger_receivedpayments.paytitle,vtiger_receivedpayments.owncompany,vtiger_receivedpayments.reality_date,vtiger_receivedpayments.unit_price,vtiger_receivedpayments.createtime, vtiger_receivedpayments.receivedpaymentsid as crmid FROM vtiger_receivedpayments WHERE vtiger_receivedpayments.deleted=0 AND vtiger_receivedpayments.relatetoid = ?',
 );

	public function getEntries($pagingModel){
		$relatedModuleName=$_REQUEST['relatedModule'];
		$moduleName = $_REQUEST['module'];
		$relatedquerylist=self::$relatedquerylist;

		if($relatedModuleName == 'Files') {
			$relatedquerylist[$relatedModuleName] .= " AND description='$moduleName' ";
		}
		

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

        $relatedModuleName=$_REQUEST['relatedModule'];

        $record = Vtiger_Record_Model::getCleanInstance($relationModule->get('name'));

        if (empty($row['crmid'])) {

            if ($_REQUEST['relatedModule'] == 'Files') {
                $record->setId($row['attachmentsid']);
                $row['crmid'] = $row['attachmentsid'];
            }
        }

        $record->setId($row['crmid']);

        $relatedRecordList[$row['crmid']] = $record;

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