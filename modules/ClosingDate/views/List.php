<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ClosingDate_List_View extends Vtiger_KList_View {

    function preProcess(Vtiger_Request $request, $display = true)
    {
        $viewer = $this->getViewer($request);
        $ISCANDO=ClosingDate_Record_Model::getCleanInstance("ClosingDate");
        $ISCANDO=$ISCANDO->personalAuthority("ClosingDate",'adjust');
        $viewer->assign('ISCANDO',$ISCANDO);
        parent::preProcess($request, $display);//TODO: Change the autogenerated stub
    }
    function process(Vtiger_Request $request)
    {
        parent::process($request);
    }

}