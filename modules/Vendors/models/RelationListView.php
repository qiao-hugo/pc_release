<?php
/*
*定义管理语句
*/
class Vendors_RelationListView_Model extends Vtiger_RelationListView_Model {
	static $relatedquerylist = array(
		'SupplierContracts'=>"SELECT * from vtiger_suppliercontracts LEFT JOIN vtiger_crmentity ON vtiger_suppliercontracts.suppliercontractsid=vtiger_crmentity.crmid where vendorid=? AND vtiger_crmentity.deleted=0",
        'Files'=>'SELECT vtiger_files.attachmentsid as crmid,vtiger_files.* from vtiger_files where relationid=? AND delflag=0',
        'ShareVendors'=>'SELECT vtiger_sharevendors.sharevendorsid AS crmid,vtiger_sharevendors.createdid,vtiger_sharevendors.createdtime,sharestatus,vtiger_sharevendors.userid FROM vtiger_sharevendors WHERE vtiger_sharevendors.vendorsid=?',
        'VisitingOrder'=> 'SELECT vtiger_crmentity.crmid,vtiger_visitingorder.destination, vtiger_visitingorder.contacts, vtiger_visitingorder.purpose, vtiger_visitingorder.extractid, vtiger_visitingorder.accompany, vtiger_visitingorder.startdate, vtiger_visitingorder.enddate, vtiger_visitingorder.outobjective,vtiger_visitingorder.modulestatus,vtiger_visitingorder.visitingorderid FROM vtiger_visitingorder INNER JOIN vtiger_crmentity ON vtiger_visitingorder.visitingorderid = vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted=0 AND vtiger_visitingorder.related_to =?',
        'VendorContacts'=>'SELECT vtiger_vendorcontacts.contactid AS crmid,vtiger_vendorcontacts.gender as gendertype,vtiger_vendorcontacts.makedecision AS makedecisiontype,vtiger_vendorcontacts.* FROM vtiger_vendorcontacts LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_vendorcontacts.contactid WHERE vtiger_crmentity.deleted=0 AND vtiger_vendorcontacts.vendorid =?',
        'ProductProvider'=>'SELECT vtiger_crmentity.*,vtiger_productprovider.* FROM `vtiger_productprovider` LEFT JOIN vtiger_crmentity ON vtiger_productprovider.productproviderid=vtiger_crmentity.crmid WHERE vtiger_productprovider.vendorid=?',
        'Billing'=>'SELECT billingid as crmid,taxpayers_no,registeraddress,depositbank,telephone,accountnumber,isformtable,businessnamesone,modulestatus FROM vtiger_billing WHERE vtiger_billing.accountid=?',
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
		//$limitQuery = $query .' LIMIT '.$startIndex.','.$pageLimit;
		//取消分页
		$limitQuery = $query;
		$result = $db->pquery($limitQuery, array());
		$relatedRecordList = array();


		for($i=0; $i< $db->num_rows($result); $i++ ) {
			$row = $db->fetch_row($result,$i);
			//$row['down_id'] = base64_encode($row['attachmentsid']);

			$record = Vtiger_Record_Model::getCleanInstance($relationModule->get('name'));

            $record->setData($row)->setModuleFromInstance($relationModule);
            $record->setId($row['crmid']);
			$relatedRecordList[$row['crmid']] = $record;
		}

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
    public function getCreateViewUrl(){
        $relationModel = $this->getRelationModel();
        $relatedModel = $relationModel->getRelationModuleModel();
        $parentRecordModule = $this->getParentRecordModel();
        $parentModule = $parentRecordModule->getModule();
        $createViewUrl = $relatedModel->getCreateRecordUrl().'&sourceModule='.$parentModule->get('name').
            '&sourceRecord='.$parentRecordModule->getId().'&relationOperation=true';

        //To keep the reference fieldname and record value in the url if it is direct relation

        if($parentModule->get('name')=='Vendors'&& $relatedModel->get('name')=='Billing'){
            $createViewUrl .='&account_id='.$parentRecordModule->getId();
        }elseif($relationModel->isDirectRelation()) {
            $relationField = $relationModel->getRelationField();
            $createViewUrl .='&'.$relationField->getName().'='.$parentRecordModule->getId();
        }
        return $createViewUrl;
    }
}