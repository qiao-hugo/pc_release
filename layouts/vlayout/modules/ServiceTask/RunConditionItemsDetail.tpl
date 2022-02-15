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
{*执行条件类型设置*}
{assign var=RUN_CONDITION_TYPE value=0}
{foreach item=FIELD_MODEL key=FIELD_NAME from=$FIELD_MODEL_LIST}
	{if ($FIELD_MODEL->get('name') eq 'runconditiontype')}
		{assign var=RUN_CONDITION_TYPE value={$FIELD_MODEL->get('fieldvalue')}}
		{break}
	{/if}
{/foreach}

{if ($RUN_CONDITION_TYPE eq '0')}
	
	{foreach item=FIELD_MODEL key=FIELD_NAME from=$FIELD_MODEL_LIST}
		{if ($FIELD_MODEL->get('name') eq 'relativeday')}
		    &nbsp;相对日期[上次完成后]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			{$FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue'), $RECORD->getId(), $RECORD)}&nbsp;天后开始
			{break}
		{/if}
	{/foreach}
{else}
	{foreach item=FIELD_MODEL key=FIELD_NAME from=$FIELD_MODEL_LIST}
		{if ($FIELD_MODEL->get('name') eq 'circulationday')}
		    &nbsp;循环[激活后]&nbsp;&nbsp;&nbsp;&nbsp;每&nbsp;
			{$FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue'), $RECORD->getId(), $RECORD)}
			&nbsp;天执行1次,
			{break}
		{/if}
	{/foreach}
	{foreach item=FIELD_MODEL key=FIELD_NAME from=$FIELD_MODEL_LIST}
		{if ($FIELD_MODEL->get('name') eq 'circulationcount')}
			循环	&nbsp;{$FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue'), $RECORD->getId(), $RECORD)}&nbsp;次
			{break}
		{/if}
	{/foreach}
{/if}
