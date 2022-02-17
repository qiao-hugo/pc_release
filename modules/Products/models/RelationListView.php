<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Products_RelationListView_Model extends Vtiger_RelationListView_Model {
	static $relatedquerylist = array('Products'=>'SELECT vtiger_crmentity.crmid,vtiger_products.productname, vtiger_products.product_no, vtiger_products.realprice, vtiger_products.unit_price, vtiger_products.minmarketprice, vtiger_products.startdate, vtiger_products.enddate, vtiger_products.customer, vtiger_products.isdisplay,vtiger_products.productid FROM vtiger_products INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_products.productid LEFT JOIN vtiger_seproductsrel ON vtiger_seproductsrel.crmid = vtiger_products.productid AND vtiger_seproductsrel.setype=\'Products\' WHERE vtiger_crmentity.deleted = 0 AND vtiger_seproductsrel.productid = ?');
	/**
	 * Function to get the links for related list
	 * @return <Array> List of action models <Vtiger_Link_Model>
	 */
	public function getLinks() {
		$relationModel = $this->getRelationModel();
		$parentModel = $this->getParentRecordModel();
		
		$isSubProduct = false;
		if($parentModel->getModule()->getName() == $relationModel->getRelationModuleModel()->getName()) {
			$isSubProduct = $relationModel->isSubProduct($parentModel->getId());
		}
		
		if(!$isSubProduct){
			return parent::getLinks();
		}
	}
	
	public function getEntries($pagingModel){
		$relatedModuleName=$_REQUEST['relatedModule'];
		$relatedquerylist=self::$relatedquerylist;
		if(isset($relatedquerylist[$relatedModuleName])){
			$parentId = $_REQUEST['record'];
			$this->relationquery=str_replace('?',$parentId,$relatedquerylist[$relatedModuleName]);
		}
		return parent::getEntries($pagingModel);	
	}
}
