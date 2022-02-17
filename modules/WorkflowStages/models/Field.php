<?php
class WorkflowStages_Field_Model extends Users_Field_Model {
	//no use
	public function getspecialPicklistValuesnouse() {
		if($this->getName() == 'isrole'){
			require_once 'crmcache/role.php';
			return $roles;
		}else{
			return parent::getPicklistValues();
		}
    }   
}
