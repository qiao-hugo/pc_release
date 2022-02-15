{*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************}
{strip}
<ul>
	{assign var="GETCATEGORYARR" value=[]}
{foreach from=$ROLE->getChildren() item=CHILD_ROLE}
	{if $CHILD_ROLE->getDepth() eq 1 && !in_array($CHILD_ROLE->getcategory(),$GETCATEGORYARR)}
		{$GETCATEGORYARR[]=$CHILD_ROLE->getcategory()}
	<li data-role="{$CHILD_ROLE->getParentRoleString()}">
		<img src="/layouts/vlayout/images/right_triangle.png" class="icon-eye-open-right open{$CHILD_ROLE->getcategory()}" title="显示" style="cursor:pointer;display:none;max-width:5%" data-id="{$CHILD_ROLE->getcategory()}">
		<img src="/layouts/vlayout/images/lower_triangle.png" class="icon-eye-close-lower close{$CHILD_ROLE->getcategory()}" title="隐藏" style="cursor:pointer;max-width:5%" data-id="{$CHILD_ROLE->getcategory()}">
		{vtranslate($CHILD_ROLE->getcategory(),"Settings:Roles")}
		<div {if $smarty.request.view != 'Popup'}class="toolbar-handle"{/if}>
		{if $smarty.request.view != 'Popup'}
			<div class="toolbar">
				&nbsp;<a href="{$CHILD_ROLE->getCreateChildUrl()}"  data-url="{$CHILD_ROLE->getCreateChildUrl()}" title="{vtranslate('LBL_ADD_RECORD', $QUALIFIED_MODULE)}"><span class="icon-plus-sign"></span></a>
			</div>
		{/if}
		</div>
	</li>
	{/if}
	<li data-role="{$CHILD_ROLE->getParentRoleString()}" data-roleid="{$CHILD_ROLE->getId()}" {if $CHILD_ROLE->getDepth() eq 1}style="padding-left:25px;"{/if} class="{$CHILD_ROLE->getcategory()}">
		<div {if $smarty.request.view != 'Popup'}class="toolbar-handle"{/if}>
			{if $smarty.request.type == 'Transfer'}
				{assign var="SOURCE_ROLE_SUBPATTERN" value='::'|cat:$SOURCE_ROLE->getId()}
				{if strpos($CHILD_ROLE->getParentRoleString(), $SOURCE_ROLE_SUBPATTERN) !== false}
					{$CHILD_ROLE->getName()}
				{else}
					<a href="{$CHILD_ROLE->getEditViewUrl()}" data-url="{$CHILD_ROLE->getEditViewUrl()}" class="btn roleEle" rel="tooltip" >{$CHILD_ROLE->getName()}</a>
				{/if}
			{else}
					<a href="{$CHILD_ROLE->getEditViewUrl()}" data-url="{$CHILD_ROLE->getEditViewUrl()}" class="btn draggable droppable" rel="tooltip" title="{vtranslate('LBL_CLICK_TO_EDIT_OR_DRAG_TO_MOVE',$QUALIFIED_MODULE)}">{$CHILD_ROLE->getName()}</a>
			{/if}
			{if $smarty.request.view != 'Popup'}
			<div class="toolbar">
				&nbsp;<a href="{$CHILD_ROLE->getCreateChildUrl()}" data-url="{$CHILD_ROLE->getCreateChildUrl()}" title="{vtranslate('LBL_ADD_RECORD', $QUALIFIED_MODULE)}"><span class="icon-plus-sign"></span></a>
				&nbsp;<a data-id="{$CHILD_ROLE->getId()}" href="javascript:;" data-url="{$CHILD_ROLE->getDeleteActionUrl()}" data-action="modal" title="{vtranslate('LBL_DELETE', $QUALIFIED_MODULE)}"><span class="icon-trash"></span></a>
			</div>
			{/if}
		</div>

		{assign var="ROLE" value=$CHILD_ROLE}
		{include file=vtemplate_path("RoleTree.tpl", "Settings:Roles")}
	</li>
{/foreach}
</ul>
{/strip}