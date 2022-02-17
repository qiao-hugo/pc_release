<?php 
/**
 * wangbin 2015-1-20 13:58:37 添加跟多回款的筛选项
 * */
class ReceivedPaymentsThrow_Module_Model extends Vtiger_Module_Model {
	
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
	        $sql = "SELECT vtiger_field.* FROM vtiger_field LEFT JOIN vtiger_tab ON vtiger_field.tabid=vtiger_tab.tabid

	        	WHERE  (vtiger_tab.name='ReceivedPaymentsThrow' OR vtiger_tab.name='ReceivedPayments') 
	        	AND (vtiger_field.columnname IN ('userid','date','unit_price','paytitle','reality_date'))
	        	AND vtiger_field.presence IN (0, 2) AND vtiger_field.displaytype != 4 AND vtiger_field.displaytype != 0 ORDER BY vtiger_field.listpresence";

	        $result=$adb->pquery($sql,array());
	        $rows=$adb->num_rows($result);
	        for($index = 0; $index < $rows; ++$index) {
	            $this->listfields[]=$adb->fetch_array($result);
	        }
	    }
	    //print_r($this->listfields);
	    //var_dump($this->listfields);die;
	    return $this->listfields;
	}
}