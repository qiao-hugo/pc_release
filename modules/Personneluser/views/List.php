<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Personneluser_List_View extends Vtiger_List_View {
    function preProcess(Vtiger_Request $request, $display=true) {
        header('Location:index.php?module=Users&parent=Settings&view=List&block=1&fieldid=1');
        exit;
    }
}