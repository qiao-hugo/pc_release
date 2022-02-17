<?php 
/**
 * wangbin 2015-1-20 13:58:37 添加跟多回款的筛选项
 * */
class Receivedpaymentstatistics_Module_Model extends Vtiger_Module_Model {
	/**获取列表字段
	 * wangbin 增加自定义列表字段
	 */
	public function getListFields() {
	    if(empty($this->listfields)){
	        $blockids=array();
	        $blocks=$this->getBlocks();
	        foreach($blocks as $blockid){
	            $blockids[]=$blockid->id;
	        }
	        $adb = PearDatabase::getInstance();
	        //$sql = 'SELECT vtiger_field.*, vtiger_fieldmodulerel.relmodule, vtiger_entityname.tablename AS ntablename, vtiger_entityname.fieldname AS nfieldname, vtiger_entityname.entityidfield FROM vtiger_field LEFT JOIN vtiger_fieldmodulerel ON vtiger_fieldmodulerel.fieldid = vtiger_field.fieldid LEFT JOIN vtiger_entityname ON vtiger_entityname.modulename = vtiger_fieldmodulerel.relmodule WHERE (vtiger_field.tabid = 51 OR vtiger_field.tabid = 79) AND vtiger_field.presence IN (0, 2) AND vtiger_field.displaytype != 4 AND vtiger_field.displaytype != 0 AND vtiger_field.block IN (131,186) ORDER BY vtiger_field.listpresence';
	        $sql = 'SELECT vtiger_field.* FROM vtiger_field WHERE vtiger_field.tabid = 80 AND vtiger_field.presence IN (0, 2) AND vtiger_field.displaytype != 4 AND vtiger_field.displaytype != 0 ORDER BY vtiger_field.listpresence';
	        $result=$adb->pquery($sql,array());
	        $rows=$adb->num_rows($result);
	        for($index = 0; $index < $rows; ++$index) {
	            $this->listfields[]=$adb->fetch_array($result);
	        }
	    }
	    //var_dump($this->listfields);die;
	    return $this->listfields;
	}
}