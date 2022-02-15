{*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************}
{strip}
<div class="container-fluid">
	<div class="widget_header row-fluid">
		<div class="span8">
			<h3>{vtranslate($MODULE, $QUALIFIED_MODULE)}</h3>
	</div>	
	</div>
	<hr>
	<div class="clearfix treeView">
		<ul style="list-style:none">
			<li data-role="{$ROOT_ROLE->getParentDepartmentString()}" data-roleid="{$ROOT_ROLE->getId()}">
				<div class="toolbar-handle">
					<a href="javascript:;" class="btn btn-inverse draggable droppable">{$ROOT_ROLE->getName()}{if $ROOT_ROLE->getCode() neq ''}&nbsp;&nbsp;&nbsp;编码:&nbsp;<span class="label label-b_actioning">{$ROOT_ROLE->getCode()}</span>{/if}{if $ROOT_ROLE->getIsjuridicalPerson() eq 1}&nbsp;&nbsp;&nbsp;法人:&nbsp;<span class="label label-a_exception">{$ROOT_ROLE->getUserName()}</span>{/if}</a>
					<div class="toolbar" title="{vtranslate('LBL_ADD_RECORD', $QUALIFIED_MODULE)}">
						{*  注释掉ERP关闭功能 &nbsp;<a href="{$ROOT_ROLE->getCreateChildUrl()}" data-url="{$ROOT_ROLE->getCreateChildUrl()}" data-action="modal"><span class="icon-plus-sign"></span></a>*}
					</div>
				</div>
				{assign var="ROLE" value=$ROOT_ROLE}
				{include file=vtemplate_path("RoleTree.tpl", "Settings:Departments")}
			</li>
		</ul>
	</div>
</div>
{/strip}