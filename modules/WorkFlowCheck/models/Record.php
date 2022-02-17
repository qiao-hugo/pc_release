<?php
class WorkFlowCheck_Record_Model extends Vtiger_Record_Model {

	/**
	 * Function to get the Detail View url for the record
	 * @return <String> - Record Detail View Url
	 */
	public function getDetailViewUrl() {
		$module = $this->getModule();
		return 'index.php?module='.$this->srcModule.'&view='.$module->getDetailViewName().'&record='.$this->salesorderid.'&refer_module=WorkFlowCheck&referer_id='.$this->getId();
	}

	
}
