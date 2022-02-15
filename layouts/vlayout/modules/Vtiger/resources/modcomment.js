/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
jQuery.Class("Vtiger_Modcomment_Js",{
},{
	registerSubcomment: function() {
		$('.replyComment').on('click',function(){
			$.layer({
				type: 2,
			    shadeClose: true,
			    title: false,
			    closeBtn: [0, false],
			    shade: [0.8, '#000'],
			    border: [0],
			    offset: ['20px',''],
			    area: ['1000px', ($(window).height() - 50) +'px'],
			    iframe: {src: 'http://f2e.sentsin.com/chat'}
			});
		});
		

	},
	registerEvents: function(){
		this.registerSubcomment();
	}
});