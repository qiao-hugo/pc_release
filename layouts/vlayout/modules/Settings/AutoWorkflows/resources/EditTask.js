/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
Settings_Vtiger_Edit_Js("Settings_AutoWorkflows_EditTask_Js",{},{
	init : function() {
		this.registerEvents();
	},
	registerEventForSelect2Element : function(){
		$(".chzn-select").select2();
	},
	//任务属性相关设置
	autorolechange : function(){
		$("body").on("change","#autorole",function(){
			/*if($(this).val()==0){
				$("#allotbyperson,#allotbygroup,#allotbyrole").removeClass("hide").find("select").removeClass("disabled");
				$("#allotbygroup,#allotbyrole").addClass("hide").find("select").addClass("disabled");
			}else if($(this).val()==1){
				$("#allotbyperson,#allotbygroup,#allotbyrole").removeClass("hide").find("select").removeClass("disabled");
				$("#allotbyperson,#allotbyrole").addClass("hide").find("select").addClass("disabled");
			}else if($(this).val()==2){
				$("#allotbyperson,#allotbygroup,#allotbyrole").removeClass("hide").find("select").removeClass("disabled");
				$("#allotbygroup,#allotbyperson").addClass("hide").find("select").addClass("disabled");
			}*/
			if($(this).val()==0){
				$("#allotbyperson,#allotbygroup,#allotbyrole").removeClass("hide").find("select").removeAttr("disabled");
				$("#allotbygroup,#allotbyrole").addClass("hide").find("select").attr("disabled","disabled");
			}else if($(this).val()==1){
				$("#allotbyperson,#allotbygroup,#allotbyrole").removeClass("hide").find("select").removeAttr("disabled");
				$("#allotbyperson,#allotbyrole").addClass("hide").find("select").attr("disabled","disabled");
			}else if($(this).val()==2){
				$("#allotbyperson,#allotbygroup,#allotbyrole").removeClass("hide").find("select").removeAttr("disabled");
				$("#allotbygroup,#allotbyperson").addClass("hide").find("select").attr("disabled","disabled");
			}
			
		});
	},
	//end
	//任务细节设置
     mailreceivebychange : function(){
    	 $("body").on("change","#mailreiveby",function(){
				$("#mailreceivebyperson,#mailreceivebygroup,#mailreceivebyrole").removeClass("hide").find("select").removeAttr("disabled");
			if($(this).val()==0){
				$("#mailreceivebygroup,#mailreceivebyrole").addClass("hide").find("select").attr("disabled","disabled");
			}else if($(this).val()==1){
				$("#mailreceivebyperson,#mailreceivebyrole").addClass("hide").find("select").attr("disabled","disabled");
			}else if($(this).val()==2){
				$("#mailreceivebyperson,#mailreceivebygroup").addClass("hide").find("select").attr("disabled","disabled");
			}else if($(this).val()==3){
				$("#mailreceivebyperson,#mailreceivebygroup,#mailreceivebyrole").addClass("hide").find("select").attr("disabled","disabled");
			}
    	 });
     },	
     mailcopybychange : function(){
    	 $("body").on("change","#mailcopyby",function(){
				$("#mailcopybyperson,#mailcopybygroup,#mailcopybyrole").removeClass("hide").find("select").removeAttr("disabled");
			if($(this).val()==0){
				$("#mailcopybygroup,#mailcopybyrole").addClass("hide").find("select").attr("disabled","disabled");
			}else if($(this).val()==1){
				$("#mailcopybyperson,#mailcopybyrole").addClass("hide").find("select").attr("disabled","disabled");
			}else if($(this).val()==2){
				$("#mailcopybyperson,#mailcopybygroup").addClass("hide").find("select").attr("disabled","disabled");
			}else if($(this).val()==3){
				$("#mailcopybyperson,#mailcopybygroup,#mailcopybyrole").addClass("hide").find("select").attr("disabled");
			}
    	 });
     },	
	registerEvents:function(){
		this.registerEventForSelect2Element();
		this.autorolechange();
		this.mailreceivebychange();
        this.mailcopybychange();
	}
});