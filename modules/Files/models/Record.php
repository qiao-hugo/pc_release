<?php
class Files_Record_Model extends Vtiger_Record_Model
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

    public function isExistFile($modulename,$relationid,$style){
        $db=PearDatabase::getInstance();
        $result = $db->pquery("select 1 from vtiger_files where description=? and relationid=? and style=? and delflag=0",array($modulename,$relationid,$style));
        return $db->num_rows($result);
    }
}