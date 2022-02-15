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
	<link rel="stylesheet" href="resources/styles2.css" type="text/css" media="screen" />


	<link rel="stylesheet" href="libraries/jquery/select2/select2.css" />
	<link rel="stylesheet" media="screen" type="text/css" href="libraries/jquery/datepicker/css/bootstrap-datepicker.min.css" />
	<link rel="stylesheet" media="screen" type="text/css" href="libraries/jquery/datetimepicker/bootstrap-datetimepicker.min.css" />
	{* For making pages - print friendly *}
	<style type="text/css">
		@media print {
			.noprint { display:none; }
		}
	</style>
</head>

<body data-skinpath="layouts/vlayout/skins/softed" data-language="zh_cn">
{*<div id="js_strings" class="hide noprint">{Zend_Json::encode($LANGUAGE_STRINGS)}</div>*}
{assign var=CURRENT_USER_MODEL value=Users_Record_Model::getCurrentUserModel()}
<div id="page">
	<!-- container which holds data temporarly for pjax calls -->
	<div id="pjaxContainer" class="hide noprint"></div>
	<style>
		.followup11toyes{
			display: none;
		}
		.followup11tono{
			display: none;
		}
	</style>
	<div class="span4">
    <span class="span5 pull-right">
        <span class="btn-group pull-right">
        </span>
    </span>
		</span>
	</div>
</div>
<div  class="summaryWidgetContainer"  style="margin:0 20px;">
	<div class="widget_header row-fluid">
		<span class="span12"><h4 class="textOverflowEllipsis">联系人</h4></span>
	</div>
	<div>
		<ul class="unstyled">
			<div class="bs-callout bs-callout-info">
				<li>
					<div>
						<span><i>首要联系人</i> :&nbsp;<strong>{$ENTITY_FIRST['linkname']}</strong></span>
						<span class="pull-right"><p class="muted"><small title=""></small></p></span>
					</div>
					<div>
						<span><i>性别</i> :&nbsp;<strong>{vtranslate($ENTITY_FIRST['gendertype'],$MODULE_NAME)}</strong></span>
						<span class="pull-right"><p class="muted"><small title=""></small></p></span>
					</div>
					<div>
						<span><i>手机</i> :&nbsp;<strong>{vtranslate($ENTITY_FIRST['mobile'],$MODULE_NAME)}</strong></span>
						<span class="pull-right"><p class="muted"><small title=""></small></p></span>
					</div>
					<div>
						<span><i>办公电话</i> :&nbsp;<strong>{vtranslate($ENTITY_FIRST['phone'],$MODULE_NAME)}</strong></span>
						<span class="pull-right"><p class="muted"><small title=""></small></p></span>
					</div>
					
					<div>
						<span><i>职务</i> :&nbsp;<strong>{vtranslate($ENTITY_FIRST['title'],$MODULE_NAME)}</strong></span>
						<span class="pull-right"><p class="muted"><small title=""></small></p></span>
					</div>
					<div>
						<span><i>决策圈</i> :&nbsp;<strong>{vtranslate($ENTITY_FIRST['makedecisiontype'],$MODULE_NAME)}</strong></span>
						<span class="pull-right"><p class="muted"><small title=""></small></p></span>
					</div>
					<div>
						<span><i>邮箱</i> :&nbsp;<strong>{vtranslate($ENTITY_FIRST['email1'],$MODULE_NAME)}</strong></span>
						<span class="pull-right"><p class="muted"><small title=""></small></p></span>
					</div>
					
				</li>
			</div>
		</ul>
		{if !empty($ALLCONTACTS)}
			<ul class="unstyled">
				{foreach item=RECENT_ACTIVITY from=$ALLCONTACTS}
					<div class="bs-callout bs-callout-warning">
						<li>
							<div>
								<span><i>联系人</i> :<strong>{$RECENT_ACTIVITY['name']}</strong></span>
								<span class="pull-right"><p class="muted"><small title=""></small></p></span>
							</div>
							<div>
								<span><i>性别</i> :&nbsp;<strong>{vtranslate($RECENT_ACTIVITY['gender'],$MODULE_NAME)}</strong></span>
								<span class="pull-right"><p class="muted"><small title=""></small></p></span>
							</div>
							<div>
								<span><i>手机</i> :&nbsp;<strong>{vtranslate($RECENT_ACTIVITY['mobile'],$MODULE_NAME)}</strong></span>
								<span class="pull-right"><p class="muted"><small title=""></small></p></span>
							</div>
							<div>
								<span><i>办公电话</i> :&nbsp;<strong>{vtranslate($RECENT_ACTIVITY['phone'],$MODULE_NAME)}</strong></span>
								<span class="pull-right"><p class="muted"><small title=""></small></p></span>
							</div>
							
							<div>
								<span><i>职务</i> :&nbsp;<strong>{vtranslate($RECENT_ACTIVITY['title'],$MODULE_NAME)}</strong></span>
								<span class="pull-right"><p class="muted"><small title=""></small></p></span>
							</div>
							<div>
								<span><i>决策圈</i> :&nbsp;<strong>{vtranslate($RECENT_ACTIVITY['makedecision'],$MODULE_NAME)}</strong></span>
								<span class="pull-right"><p class="muted"><small title=""></small></p></span>
							</div>
							<div>
								<span><i>邮箱</i> :&nbsp;<strong>{vtranslate($RECENT_ACTIVITY['email'],$MODULE_NAME)}</strong></span>
								<span class="pull-right"><p class="muted"><small title=""></small></p></span>
							</div>
						</li>
					</div>
				{/foreach}
			</ul>
		{/if}
	</div>
	<span class="clearfix"></span>
</div>
</body>
</html>
{/strip}
