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

{* TODO: Review the order of parameters - good to eliminate $RECORD->getId, $RECORD should be used *}
{assign var=UITYPE value=$FIELD_MODEL->get('uitype')}
{if $UITYPE == 54  || $UITYPE == 110  || $UITYPE == 103}
	{assign var="FIELD_VALUE_LIST" value=explode(' |##| ',$FIELD_MODEL->get('fieldvalue'))}
	{foreach item=PICKLIST_VALUE from=$FIELD_VALUE_LIST}
		{$FIELD_MODEL->getDisplayValue($PICKLIST_VALUE, $RECORD->getId(), $RECORD)}
		{if !$PICKLIST_VALUE@last}
		,
		{/if}
	{/foreach}
{elseif $FIELD_MODEL->get('fieldname') eq "accountrank"}
	{vtranslate($FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue'), $RECORD->getId(), $RECORD),$MODULE)}
{elseif $MODULE eq 'Knowledge' && $UITYPE eq 19}
	{decode_html($FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue'), $RECORD->getId(), $RECORD))}
{* wangbin 去除回款详细页面关联合同货币类型<!--elseif $MODULE eq 'ReceivedPayments' && $UITYPE eq 71 && $FIELD_MODEL->getName() eq unit_price}
	<span>{$CURRENCY}</span>{$FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue'), $RECORD->getId(), $RECORD)-->*}
{else}
	{* {$FIELD_MODEL->get('fieldvalue')} *}
	{$FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue'), $RECORD->getId(), $RECORD)}
{/if}