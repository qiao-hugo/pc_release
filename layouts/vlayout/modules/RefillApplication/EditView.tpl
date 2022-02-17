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
{*{if $RECHARGESOURCE eq 'contractChanges'}
    {include file="EditViewBlocksContractChanges.tpl"|@vtemplate_path:'RefillApplication'}
{else}
    {include file="EditViewBlocks.tpl"|@vtemplate_path:'RefillApplication'}
{/if}*}
{if $RECHARGESOURCE neq 'contractChanges'}
    {include file="EditViewBlocks.tpl"|@vtemplate_path:'RefillApplication'}
    {if $RECHARGESOURCE eq 'COINRETURN' || $RECHARGESOURCE eq 'INCREASE'}
        {include file="{$RECHARGESOURCE}Edit.tpl"|@vtemplate_path:'RefillApplication'}
    {else}
        {include file="LineItemsEdit.tpl"|@vtemplate_path:'RefillApplication'}
    {/if}
    {include file="EditViewActions.tpl"|@vtemplate_path:'Vtiger'}
{else}
    {include file="EditViewBlocksContractChanges.tpl"|@vtemplate_path:'RefillApplication'}
    <span class="pull-right">
				<button class="btn btn-success" type="submit" autocomplete="on"><strong>{vtranslate('LBL_SAVE', $MODULE)}</strong></button>
				<a class="cancelLink" type="reset" onclick="javascript:window.history.back();">{vtranslate('LBL_CANCEL', $MODULE)}</a>
	</span>
{/if}
