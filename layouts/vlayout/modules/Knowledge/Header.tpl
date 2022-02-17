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
<!DOCTYPE html>
<html>
	<head>
		<title>
			{vtranslate($PAGETITLE, $MODULE_NAME)}
		</title>
		<link REL="SHORTCUT ICON" HREF="favicon.ico">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="renderer" content="webkit">
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

		<link rel="stylesheet" href="data/min/?b=libraries&f=jquery/chosen/chosen.css,jquery/jquery-ui/css/custom-theme/jquery-ui-1.8.16.custom.css,jquery/select2/select2.css,bootstrap/css/bootstrap.css,jquery/posabsolute-jQuery-Validation-Engine/css/validationEngine.jquery.css,guidersjs/guiders-1.2.6.css,jquery/pnotify/jquery.pnotify.default.css,jquery/pnotify/use for pines style icons/jquery.pnotify.default.icons.css" type="text/css" media="screen" />
		<!--<link rel="stylesheet" href="libraries/bootstrap/css/dataTables.bootstrap.css" type="text/css" media="screen" />-->
		<link rel="stylesheet" href="resources/styles.css" type="text/css" media="screen" />
	

		<link rel="stylesheet" href="libraries/jquery/select2/select2.css" />
		
		
		
		{foreach key=index item=cssModel from=$STYLES}
			<link rel="{$cssModel->getRel()}" href="{$cssModel->getHref()}?&v={$VTIGER_VERSION}" type="{$cssModel->getType()}" media="{$cssModel->getMedia()}" />
		{/foreach}
<link rel="stylesheet" media="screen" type="text/css" href="libraries/jquery/datepicker/css/bootstrap-datepicker.min.css" />
        <link rel="stylesheet" media="screen" type="text/css" href="libraries/jquery/datetimepicker/bootstrap-datetimepicker.min.css" />
		{* For making pages - print friendly *}
		<style type="text/css">
		@media print {
		.noprint { display:none; }
		}
		</style>

		{* This is needed as in some of the tpl we are using jQuery.ready *}
		<script type="text/javascript" src="libraries/jquery/jquery.min.js"></script>
		
		
		{* ends *}

		{* ADD <script> INCLUDES in JSResources.tpl - for better performance *}
	</head>

	<body data-skinpath="layouts/vlayout/skins/softed" data-language="zh_cn"{if $VIEW eq 'Detail'} ondragstart="return false" oncontextmenu="return false" onselectstart="return false" oncopy="return false" oncut="return false" style="-moz-user-select:none;"{/if}>
		{*<div id="js_strings" class="hide noprint">{Zend_Json::encode($LANGUAGE_STRINGS)}</div>*}
		{assign var=CURRENT_USER_MODEL value=Users_Record_Model::getCurrentUserModel()}
		<input type="hidden" id="start_day" value="{$CURRENT_USER_MODEL->get('dayoftheweek')}" />
		<input type="hidden" id="row_type" value="{$CURRENT_USER_MODEL->get('rowheight')}" />
		<input type="hidden" id="current_user_id" value="{$CURRENT_USER_MODEL->get('id')}" />
		<div id="page">
			<!-- container which holds data temporarly for pjax calls -->
			<div id="pjaxContainer" class="hide noprint"></div>
{/strip}
