<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * ModComments Record Model
 */
class TyunUpgradeRule_Record_Model extends Vtiger_Record_Model {

    /**
     * 取当前所有的
     * T云产品列表
     * @return array
     */
    public function getProductid(){
        $db=PearDatabase::getInstance();
        $query='SELECT * FROM `vtiger_productid`';
        $result=$db->pquery($query,array());
        $data=array();
        while($row=$db->fetch_array($result)){
            $data[]=$row;
        }
        return $data;
    }
    public function isRepeatTyunProduct(Vtiger_Request $request)
    {
        $sproductid=$request->get('productid');
        $tyundownup=$request->get('tyundownup');
        $db=PearDatabase::getInstance();
        $query='SELECT 1 FROM `vtiger_tyunupgraderule` WHERE productid=? AND tyundownup=?';
        $result=$db->pquery($query,array($sproductid,$tyundownup));
        if($db->num_rows($result)){
            return true;
        }
        return false;
    }
    public function getSelectProduct($sproductid,$tyundownup)
    {
        $db=PearDatabase::getInstance();
        $query='SELECT dproduct FROM `vtiger_productdownupgrade` WHERE deleted=0 AND sproduct=? AND tyundownup=?';
        $result=$db->pquery($query,array($sproductid,$tyundownup));
        $array=array();
        while($row=$db->fetch_array($result))
        {
            $array[]=$row['dproduct'];
        }
        return $array;
    }
}