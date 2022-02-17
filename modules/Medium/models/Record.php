<?php
/*+********
 *客户信息管理
 **********/

class Medium_Record_Model extends Vtiger_Record_Model {
    /**
     * 充值明细信息
     * @param $id
     * @return array
     */
    public function getadsname($id){
        global $adb;
        $query='SELECT * FROM `vtiger_adsname` WHERE deleted=0 AND mediumid='.$id;
        return $adb->run_query_allrecords($query);

    }
    public function getfirmpolicy($id){
        global $adb;
        $query='SELECT * FROM `vtiger_firmpolicy` WHERE deleted=0 AND mediumid='.$id;
        return $adb->run_query_allrecords($query);

    }
}
