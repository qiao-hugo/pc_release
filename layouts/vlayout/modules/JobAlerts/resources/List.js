/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("JobAlerts_List_Js",{},{
registerEvents : function(){
	this._super();
	//初始化删除第一项因为第一项是客户名称点分页后把为空的排除掉分页记录不对
	//removeSearchField(1);

}
});