<?php
class Vmatefiles_Record_Model extends Vtiger_Record_Model
{
    public function getFiles($attachmentsid){
        global $adb;
        $sql = "select * from vtiger_files where attachmentsid=?";
        $result = $adb->pquery($sql,array($attachmentsid));
        if($adb->num_rows($result)){
            return $adb->fetch_array($result);
        }
        return array();
    }
}