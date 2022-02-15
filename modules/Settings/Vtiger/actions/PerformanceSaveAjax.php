<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_Vtiger_PerformanceSaveAjax_Action extends Settings_Vtiger_Basic_Action {
    
    public function process(Vtiger_Request $request) {
        
		$array=array();
		$value=explode(',',$request->get('announcement'));
		if(!empty($value)){
			foreach($value as $k=>$v){
				if(!empty($value[$k+1])){
					$array[]=array('minval'=>$v,'maxval'=>$value[$k+1],'performance'=>$k+1);
				}
			}
		
		}
		
		Settings_Vtiger_Performance_Model::saverank($array);
		
        $responce = new Vtiger_Response();
        $responce->setResult(array('success'=>true));
        $responce->emit();
    }
}