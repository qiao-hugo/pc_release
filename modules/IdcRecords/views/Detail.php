<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class IdcRecords_Detail_View extends Vtiger_Detail_View {

    protected function preProcessDisplay(Vtiger_Request $request) {
        $viewer = $this->getViewer($request);
        $recordId = $request->get('record');
        $viewer = $this->getViewer($request);
        //类型
        $viewer->assign('IDCRECORDS_TYPE', IdcRecords_Record_Model::getIdcRecordsType($recordId));
        //域名
        $viewer->assign('IDCRECORDS_NAME', IdcRecords_Record_Model::getIdcRecordsName($recordId));
        $displayed = $viewer->view($this->preProcessTplName($request), $request->getModule());
    }


}
