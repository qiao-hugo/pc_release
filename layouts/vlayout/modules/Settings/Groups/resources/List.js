Settings_Vtiger_List_Js("Settings_Groups_List_Js",{},{getDefaultParams:function(){var pageNumber=jQuery('#pageNumber').val();var module=app.getModuleName();var parent=app.getParentModuleName();var cvId=this.getCurrentCvId();var orderBy=jQuery('#orderBy').val();var sortOrder=jQuery("#sortOrder").val();var DepartFilter=jQuery('#DepartFilter').val();var orderBy=jQuery('#orderBy').val();var sortOrder=jQuery("#sortOrder").val();var searchvalue=$('#searchvalue').val();var params={'module':module,'parent':parent,'page':pageNumber,'view':"List","search_value":searchvalue,"search_key":$('#searchtype').val(),'viewname':cvId,'orderby':orderBy,'sortorder':sortOrder}
return params;},registerEvents:function(){this._super();this.registerPageNavigationEventsK();}});