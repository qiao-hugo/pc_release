/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("Files_List_Js",{},{

	downLoadFile : function(url){
		$('body').on('click','#btnDownload',function(){
			let checkedList = $('.fileCheckbox:checked');
			if (checkedList.length == 0) {
				var params = {text : '请先选择要下载的附件', title : '提示',type:'error'};
				Vtiger_Helper_Js.showMessage(params);
				return false
			}
			let ids = [];
			checkedList.each(function(){
				ids.push($(this).parents('tr').data('id'));
				});
			let url = $(this).data('url');
			let downloadA = $('#downloadA');
			downloadA.attr('href', url + '&records=' + ids.toString());
			console.log(downloadA);
			downloadA[0].click();
		}).on('click','#checkedAll',function(event){
			event.stopPropagation(); //阻止事件冒泡
			if ($(this).prop("checked")) {
				$('.fileCheckbox').attr("checked", true);
			} else {
				$('.fileCheckbox').attr("checked", false);
			}
		})
	},

	registerEvents : function(){
		this._super();
		this.downLoadFile();
	}
});