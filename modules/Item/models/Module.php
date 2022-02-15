<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ************************************************************************************/

class Item_Module_Model extends Vtiger_Module_Model {

    /**
     * Function to get the Quick Links for the module
     * @param <Array> $linkParams
     * @return <Array> List of Vtiger_Link_Model instances
     */
    public function getSideBarLinks($linkParams) {
        $parentQuickLinks = parent::getSideBarLinks($linkParams);

        return $parentQuickLinks;
    }
    /**
     *合同关联的产品联动
     */
    static function productcategory($record){
        $db = PearDatabase::getInstance();
        //取得合同类型的第一个联动框的内容列表

        $query = 'SELECT * FROM vtiger_parentcate order by sortorderid';
        $result['parent'] = $db->run_query_allrecords($query);

        //第一个联动框已经选中的项
        $nparentcate=0;
        if($record>0){
            $query = 'SELECT parentcate,soncate,special FROM vtiger_soncate WHERE soncateid=? and deleted=0 limit 1';
            $data=$db->pquery($query,array($record));
            $nparentcate=$db->query_result($data,0,'parentcate');
            $nsoncate=$db->query_result($data,0,'soncate');
            $nspecial=$db->query_result($data,0,'special');
            //是否为新建给个1
            $result['nparentcate']=$nparentcate;
            $result['nsoncate']=$nsoncate;
            $result['nspecial']=$nspecial;
        }

        return $result;
    }
}
