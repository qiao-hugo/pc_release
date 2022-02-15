<?php
/*
*定义管理语句
*/
class VisitingOrder_RelationListView_Model extends Vtiger_RelationListView_Model {
	static $relatedquerylist = array(
		'VisitImprovement'=>'SELECT vtiger_visitimprovement.*,vtiger_visitimprovement.visitimprovementid AS crmid FROM vtiger_visitimprovement WHERE vtiger_visitimprovement.visitingorderid=?'
    );

	public function getEntries($pagingModel){
		//获取关联模块查询语句
		//marketprice
		$relatedModuleName=$_REQUEST['relatedModule'];
		$relatedquerylist=self::$relatedquerylist;
		if(isset($relatedquerylist[$relatedModuleName])){
			$parentId = $_REQUEST['record'];
			$this->relationquery=str_replace('?',$parentId,$relatedquerylist[$relatedModuleName]);
		}
		return parent::getEntries($pagingModel);
	}

}