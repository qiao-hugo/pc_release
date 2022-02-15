{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/
-->*}
{strip}
<div class="contentsDiv span10 marginLeftZero">
	<input type="hidden" name="module" value="{$FOR_MODULE}" />
	<table style="margin-left: auto;margin-right: auto;width: 100%;" class="searchUIBasic" cellspacing="12">
		<tr>
			<td class="font-x-large" align="left" colspan="2">
				<strong>{'LBL_IMPORT'|@vtranslate:$MODULE} {$FOR_MODULE|@vtranslate:$FOR_MODULE}</strong>
			</td>
		</tr>
		<tr>
			<td class="leftFormBorder1 importContents" width="40%" valign="top">
				{include file='NewImport_Step1.tpl'|@vtemplate_path:'ReceivedPayments'}
			</td>
		</tr>
		<tr>
			<td align="right" colspan="2">
			{include file='NewImport_Basic_Buttons.tpl'|@vtemplate_path:'ReceivedPayments'}
			</td>
		</tr>
	</table>
	<table style="margin-left: auto;margin-right: auto;width: 100%;" class="searchUIBasic" cellspacing="12">
		<tr>
			<td class="leftFormBorder1 importContents" style="width:40%; white-space: pre-line;"  valign="top" id="failReason">

			</td>
		</tr>
	</table>
</div>
{/strip}
<script type="text/javascript" src="libraries/jquery/kindeditor/kindeditor-all-min.js"></script>
<link rel="stylesheet" media="screen" type="text/css" href="libraries/jquery/kindeditor/themes/default/default.css" />
<script>
	var uploadbutton;

	KindEditor.ready(function(K) {
		uploadbutton = K.uploadbutton({
			button : K('#uploadImport')[0],
			fieldName : 'importFile',
			extraParams :{
				__vtrftk:csrfMagicToken,
			},
			url : 'index.php?module='+$("input[name='module']").val()+'&action=Import',
			afterUpload : function(data) {
				$("#newImportButton").enable();
				$("#fileName").html('');
				if(data.success){
					$("#failReason").html(data.result);
					{*Vtiger_Helper_Js.showMessage({type:'success',text:"导入成功"});*}
				}else{
					{*Vtiger_Helper_Js.showMessage({type:'error',text:"导入失败"});*}
				}
			},
			afterError : function(str) {
				{*Vtiger_Helper_Js.showMessage({type:'error',text:"导入失败"});*}
			}
		});

		uploadbutton.fileBox.change(function(e) {
			var filePath=$('input[name=importFile]').val();
			if(filePath.indexOf(".xlsx")!=-1){
				//设置文件路劲
				$("#fileName").html(filePath);
			}else{
				alert("您上传文件类型有误");
				return false
			}
		});
	});

	/**
	 *导入
	 */
	$("#newImportButton").click(function () {
		if($('input[name=importFile]').val()==''){
			alert("您未上传文件");
			return false
		}
		$("#failReason").html('');
		$("#newImportButton").disable();
		uploadbutton.submit();
	});
</script>