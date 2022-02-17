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
<input type="hidden" id="hidrunconditiontype" name="runconditiontype" value='0'/>
{*执行条件类型设置*}
{assign var=RUN_CONDITION_TYPE value='0'}
{foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}
	{if ($FIELD_MODEL->get('name') eq 'runconditiontype')}
		{assign var=RUN_CONDITION_TYPE value={$FIELD_MODEL->get('fieldvalue')}}
		{break}
	{/if}
{/foreach}
<table style="width:80%;text-align:right;border:0px">
	<tr>
		<td style="text-align:left;">
		<input type="radio" name="list" {if ($RUN_CONDITION_TYPE neq '1')}checked{/if} id="radio_relativeday" value="0"/>&nbsp;相对日期[上次完成后]
		</td>
		<td style="text-align:left;">
		{foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}
			{if ($FIELD_MODEL->get('name') eq 'relativeday')}
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) BLOCK_FIELDS=$BLOCK_FIELDS}&nbsp;天后开始
				{break}
			{/if}
		{/foreach}
		</td>
	</tr>
	<tr>
		<td style="text-align:left;">
		<input type="radio" name="list" {if ($RUN_CONDITION_TYPE eq '1')}checked{/if} id="radio_circulation" value="1"/>&nbsp;循环[激活后]
		</td>
		<td style="text-align:left;">
			{foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}
				{if ($FIELD_MODEL->get('name') eq 'circulationday')}
						        每&nbsp;
							{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) BLOCK_FIELDS=$BLOCK_FIELDS}
							&nbsp;天执行1次,
					{break}
				{/if}
			{/foreach}
			{foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}
				{if ($FIELD_MODEL->get('name') eq 'circulationcount')}
							循环	&nbsp;{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) BLOCK_FIELDS=$BLOCK_FIELDS}&nbsp;次
					{break}
				{/if}
			{/foreach}
		</td>
	</tr>
</table>
