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
<div class="commentContainer" style="margin:10px;">
     <input id="accountId" type="hidden" value="{$ACCOUNTID}" />
		<div class="commentsBody">
		{if !empty($COMMENTS)}
			{foreach key=index item=COMMENT from=$COMMENTS}
				<div class="commentDetails bs-example">
					<div class="commentDiv">
						<div class="singleComment">
							<div class="commentInfoHeader row-fluid" data-commentid="{$COMMENT->getId()}" data-parentcommentid="{$COMMENT->getId()}">
								<div class="commentTitle">
									{assign var=PARENT_COMMENT_MODEL value=$COMMENT->getParentCommentModel()}
									
									<div class="row-fluid">
										<div class="span1">
											{assign var=IMAGE_PATH value=$COMMENT->getImagePath()}
											<img class="alignMiddle pull-left" src="{if !empty($IMAGE_PATH)}{$IMAGE_PATH}{else}{vimage_path('DefaultUserIcon.png')}{/if}">
										</div>
										<div class="span11 commentorInfo">
											{assign var=COMMENTOR value=$COMMENT->getCommentedByModel()}
											<div class="inner">
												<span class="commentorName"><strong>{$COMMENTOR->getName()}&nbsp;<span class="label label-a_normal">{$ROLE[$COMMENTOR->entity->roleid]|replace:'|—':''}</span></strong> </span>
												<span class="pull-right">
													<p class="muted">{vtranslate('LBL_modcommenttype', 'ModComments')} : {vtranslate($COMMENT->get('modcommenttype'), 'ModComments')} {if $COMMENT->get('accountintentionality') neq '' and $COMMENT->get('accountintentionality') neq 'zeropercent'} {vtranslate('LBL_intentionality', 'ModComments')} : {vtranslate($COMMENT->get('accountintentionality'), 'Accounts')}{/if} {vtranslate('LBL_modcommentmode', 'ModComments')} : {vtranslate($COMMENT->get('modcommentmode'), 'ModComments')} <em>{vtranslate('LBL_COMMENTED',$MODULE_NAME)}</em>&nbsp;
													<small title="{Vtiger_Util_Helper::formatDateTimeIntoDayString($COMMENT->getCommentedTime())}">{$COMMENT->getCommentedTime()}</small> </p>
												</span>
												<div class="clearfix"></div>
											</div>
											<div class="commentInfoContent">
												<style>
												h4{
												font-size:14px;
												font-weight:500;
												font-family: 'Helvetica Neue', Helvetica, 'Microsoft Yahei', 'Hiragino Sans GB', 'WenQuanYi Micro Hei', sans-serif;
												}
												</style>
												<div class="bs-callout bs-callout-info">
												<h4>{*跟进目的：{$COMMENT->get('modcommentpurpose')} *}
												&nbsp;
												联系人:<span class="" data-field-type="reference" data-field-name="contact_id">
												{assign var=ISCONTACT value=$COMMENT->get('lastname')}
												{assign var=ISSHOUYAO value=$COMMENT->get('shouyao')}
												{if empty($ISCONTACT)}
													{if empty($ISSHOUYAO)}
													-
													{else}
													{$COMMENT->get('shouyao')}
						
													{/if}
												{else}
												{$COMMENT->get('lastname')}
												{/if}
												</span>
												</h4>
												{if $COMMENT->get('modcommenttype') eq '首次客户录入系统跟进' || $COMMENT->get('modcommenttype') eq '首次拜访客户后跟进'}
													{$COMMENT->getFollowUp($COMMENT->get('modcommenttype'),$COMMENT->get('commentcontent'))}
												{elseif $COMMENT->get('followrole') eq 1}
													{"/(\\n+)/"|preg_replace:'<br>':("/(\*#\*)+/"|preg_replace:'：':("/(#endl#)+/"|preg_replace:'<br>':$COMMENT->get('commentcontent')))}
												{else}
													{"/(\\n+)/"|preg_replace:'<br>':$COMMENT->get('commentcontent')}

												{/if}
												</div>
											</div>
											<div class="row-fluid">
												<div class="pull-right commentActions">
														<span>
														{if $PARENT_COMMENT_MODEL neq false or $CHILD_COMMENTS_MODEL neq null}
															&nbsp;<span>|</span>&nbsp;
															{vtranslate('LBL_VIEW_THREAD',$MODULE_NAME)}
														{/if}
													</span>
												</div>
											</div>
											{assign var="COMMENT_ALERTS_ROWS" value=$COMMENT->getAlerts()}
											{if !empty($COMMENT_ALERTS_ROWS)}
												<div class="alertInfoContent ">
													跟进提醒
													{foreach key=his item=COMMENT_ALERTS_ROW from=$COMMENT->getAlerts()}
														<div class="bs-callout bs-callout-danger">
														<h4>主题：{$COMMENT_ALERTS_ROW['subject']}</h4>
														<h4>提醒时间：{$COMMENT_ALERTS_ROW['alerttime']}  提醒人:{$COMMENT_ALERTS_ROW['username']} 提醒状态:{vtranslate($COMMENT_ALERTS_ROW['alertstatus'],'JobAlerts')} 优先级:{vtranslate($COMMENT_ALERTS_ROW['taskpriority'],'JobAlerts')}</h4>
														{nl2br($COMMENT_ALERTS_ROW['alertcontent'])}
														</div>
													{/foreach}
												</div>
											{/if}
											
											
										</div>
									</div>
								</div>
							</div>
							<div class="row-fluid commentActionsContainer">
								
								<div class="row-fluid"  name="editStatus">
									
									<div class="row-fluid pushUpandDown2per">
									{assign var="PAGEHIS" value=count($COMMENT->getHistory())}
										{foreach key=his item=COMMENTHISTORY from=$COMMENT->getHistory()}
											<div>
										<div class="span2"></div>
										<div class="bs-callout bs-callout-warning span10">
										<h4>
										
										{$PAGEHIS-$his}楼：
										{$COMMENTHISTORY['createdbyer']}
														创建于 {$COMMENTHISTORY['createdtime']}  <span style="color: grey">{if $COMMENTHISTORY['accountintentionality'] neq '' and $COMMENTHISTORY['accountintentionality'] neq 'zeropercent'} {vtranslate('LBL_intentionality', 'ModComments')} : {vtranslate($COMMENTHISTORY['accountintentionality'], 'Accounts')}{/if}</span>
										
										</h4>
										{$COMMENTHISTORY['modcommenthistory']}</p>
										
										{if empty($COMMENTHISTORY['modifiedcause']) eq false}<div class="bs-example">
										<h4>最后修改时间{$COMMENTHISTORY['modifiedtime']} </h4>
										
										修改原因{$COMMENTHISTORY['modifiedcause']}</div>
										{/if}
										
										
										</div>
											</div>
										{/foreach}
										
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			{/foreach}
		{else}
			<div class="commentDetails bs-example">
				<div class="commentDiv">
					<div class="singleComment">
						<div class="commentInfoHeader row-fluid">
							<div class="commentTitle">
								<div class="row-fluid">
									<div class="span11 commentorInfo">
										<div class="commentInfoContent">
											<div class="bs-callout bs-callout-info">
												<h4>
													暂无记录
												</h4>
											</div>
										</div>
										<div class="row-fluid">
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		{/if}
        <div class="span2 pull-right">
        </div>
        <div style="clear:both;"></div>
	</div>

</div>
	</div>
</body>
</html>
{/strip}