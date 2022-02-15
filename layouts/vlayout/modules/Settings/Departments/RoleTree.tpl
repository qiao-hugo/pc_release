{*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************}
{strip}
<ul style="list-style:none;{if $ROLE->get('depth')>0}display:none{/if}">
{foreach from=$ROLE->getChildren() item=CHILD_ROLE}
	<li data-role="{$CHILD_ROLE->getParentDepartmentString()}" data-roleid="{$CHILD_ROLE->getId()}" {if $smarty.request.view != 'Popup'}class="{$CHILD_ROLE->explodeParentDepartments($CHILD_ROLE->getParentDepartmentString())}"{/if}>
		<div {if $smarty.request.view != 'Popup'}class="toolbar-handle"{/if}>
            {if $CHILD_ROLE->hasChild()}
				<img src="/layouts/vlayout/images/right_triangle.png"  class="icon-eye-open-right open{$CHILD_ROLE->getId()}" title="显示" style="cursor:pointer;max-width:5%" data-id="{$CHILD_ROLE->getId()}">
				<img src="/layouts/vlayout/images/lower_triangle.png"
					 class="icon-eye-close-lower close{$CHILD_ROLE->getId()}" title="隐藏" style="cursor:pointer;display:none;max-width:5%" data-id="{$CHILD_ROLE->getId()}">
			{else}
				<img src="/layouts/vlayout/images/blank.png"  class="icon-eye-open-blank " title="显示" style="cursor:pointer;max-width:5%" data-id="{$CHILD_ROLE->getId()}">
			{/if}
			{if $smarty.request.type == 'Transfer'}
				{assign var="SOURCE_ROLE_SUBPATTERN" value='::'|cat:$SOURCE_ROLE->getId()}
				{if strpos($CHILD_ROLE->getParentDepartmentString(), $SOURCE_ROLE_SUBPATTERN) !== false}
					{$CHILD_ROLE->getName()}
				{else}
					{* cxh 注释点击后去编辑页面 替换成下面不能跳转的 <a href="{$CHILD_ROLE->getEditViewUrl()}" data-url="{$CHILD_ROLE->getEditViewUrl()}" class="btn roleEle" rel="tooltip" >{$CHILD_ROLE->getName()}{if $CHILD_ROLE->getCode() neq ''}&nbsp;&nbsp;&nbsp;编码:&nbsp;<span class="label label-b_actioning">{$CHILD_ROLE->getCode()}</span>{/if}{if $CHILD_ROLE->getIsjuridicalPerson() eq 1}&nbsp;&nbsp;&nbsp;ERP账套:&nbsp;<span class="label label-a_exception">{$CHILD_ROLE->getErpAccount()}</span>{/if}</a>*}
					<a  data-url="###" class="btn roleEle" rel="tooltip" >{$CHILD_ROLE->getName()}{if $CHILD_ROLE->getCode() neq ''}&nbsp;&nbsp;&nbsp;编码:&nbsp;<span class="label label-b_actioning">{$CHILD_ROLE->getCode()}</span>{/if}{if $CHILD_ROLE->getIsjuridicalPerson() eq 1}&nbsp;&nbsp;&nbsp;ERP账套:&nbsp;<span class="label label-a_exception">{$CHILD_ROLE->getErpAccount()}</span>{/if}</a>				{/if}
			{else}
				{* cxh 注释点击后去编辑页面 替换成下面不能跳转的 <a href="{$CHILD_ROLE->getEditViewUrl()}" data-url="{$CHILD_ROLE->getEditViewUrl()}" class="btn draggable droppable" rel="tooltip" title="{vtranslate('LBL_CLICK_TO_EDIT_OR_DRAG_TO_MOVE',$QUALIFIED_MODULE)}">{$CHILD_ROLE->getName()} {if $CHILD_ROLE->getCode() neq ''}&nbsp;&nbsp;&nbsp;编码:&nbsp;<span class="label label-b_actioning">{$CHILD_ROLE->getCode()}</span>{/if}{if $CHILD_ROLE->getIsjuridicalPerson() eq 1}&nbsp;&nbsp;&nbsp;ERP账套:&nbsp;<span class="label label-a_exception">{$CHILD_ROLE->getErpAccount()}</span>{/if}</a>*}
				 <a  data-url="###" class="btn draggable droppable" rel="tooltip" title="{vtranslate('LBL_CLICK_TO_EDIT_OR_DRAG_TO_MOVE',$QUALIFIED_MODULE)}">{$CHILD_ROLE->getName()} {if $CHILD_ROLE->getCode() neq ''}&nbsp;&nbsp;&nbsp;编码:&nbsp;<span class="label label-b_actioning">{$CHILD_ROLE->getCode()}</span>{/if}{if $CHILD_ROLE->getIsjuridicalPerson() eq 1}&nbsp;&nbsp;&nbsp;ERP账套:&nbsp;<span class="label label-a_exception">{$CHILD_ROLE->getErpAccount()}</span>{/if}</a>
			{/if}
			{if $smarty.request.view != 'Popup'}
				&nbsp;<a href="{$CHILD_ROLE->getCreateChildUrl()}" data-url="{$CHILD_ROLE->getCreateChildUrl()}" title="{vtranslate('LBL_ADD_RECORD', $QUALIFIED_MODULE)}">{*<span class="icon-plus-sign"></span>*}</a>
				&nbsp;<a data-id="{$CHILD_ROLE->getId()}" href="javascript:;" data-url="{$CHILD_ROLE->getDeleteActionUrl()}" data-action="modal" title="{vtranslate('LBL_DELETE', $QUALIFIED_MODULE)}">{*<span class="icon-trash"></span>*}</a>
			{/if}
		</div>

		{assign var="ROLE" value=$CHILD_ROLE}
		{include file=vtemplate_path("RoleTree.tpl", "Settings:Departments")}
	</li>
{/foreach}
</ul>
{/strip}