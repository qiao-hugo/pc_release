<?php
/*+******
 * 保存体系数据
*****/

class Settings_TissueEditor_Save_Action extends Settings_Vtiger_Index_Action {

	public function process(Vtiger_Request $request) {
		$selectedModulesList = $request->get('selectedModulesList');
		file_put_contents('crmcache/TissueEditor.php', "<?php\n\$TissueLists=" . var_export($selectedModulesList, true) . ";");
		Vtiger_Cache::set('zdcrm_','TissueLists',$selectedModulesList);
		$loadUrl = 'index.php?module=TissueEditor&parent=Settings&view=Index';
		header("Location: $loadUrl");
	}

}
