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
	{assign var="MODULE_NAME" value=$MODULE_MODEL->get('name')}
	<input id="recordId" type="hidden" value="{$RECORD->getId()}" />
	<div class="detailViewContainer">
		<div class="row-fluid detailViewTitle">
			<div class="{if $NO_PAGINATION} span12 {else} span10 {/if}">
				<div class="row-fluid">
					<div class="span5">
						<div class="row-fluid">
							{include file="DetailViewHeaderTitle.tpl"|vtemplate_path:$MODULE}
						</div>
					</div>
					<div class="span7">
						<div class="pull-right detailViewButtoncontainer">
							<div class="btn-toolbar">
							</div>
						</div>
					</div>
				</div>
			</div>
			{if !{$NO_PAGINATION}}
				<div class="span2 detailViewPagingButton">
					<span class="btn-group pull-right">
						<button class="btn" id="detailViewPreviousRecordButton" {if empty($PREVIOUS_RECORD_URL)} disabled="disabled" {else} onclick="window.location.href='{$PREVIOUS_RECORD_URL}'" {/if}><i class="icon-chevron-left"></i></button>
						<button class="btn" id="detailViewNextRecordButton" {if empty($NEXT_RECORD_URL)} disabled="disabled" {else} onclick="window.location.href='{$NEXT_RECORD_URL}'" {/if}><i class="icon-chevron-right"></i></button>
					</span>
				</div>
			{/if}
		</div>
		<div class="detailViewInfo row-fluid">
			<div class="{if $NO_PAGINATION} span12 {else} span10 {/if} details">
				<form id="detailView" data-name-fields='{ZEND_JSON::encode($MODULE_MODEL->getNameFields())}'>
					<div class="contents">
{/strip}
