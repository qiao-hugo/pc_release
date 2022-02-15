<?php
class Settings_Vtiger_Performance_Model extends Vtiger_Base_Model {
    
    const tableName  = 'vtiger_performancerank';
    
    
    public function saverank($array) {
        $db = PearDatabase::getInstance();
        $query = 'UPDATE '.self::tableName.' SET minval=?,maxval=? WHERE performancerank=?';
        foreach($array as $params){
			$db->pquery($query,$params);
		}
		
       
      
        
    }
    
    public static function getInstanceByPerformance() {
        $db = PearDatabase::getInstance();
        $query = 'SELECT GROUP_CONCAT(maxval) as val  FROM '.self::tableName;
        $result = $db->pquery($query,array());
        return $db->query_result_rowdata($result,0);
    }
}