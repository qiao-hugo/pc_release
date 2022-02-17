{*<!--
/***列表默认搜索类型菜单**
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
*{if $CURRENT_VIEW eq 'List'}
	{assign var=PUBLICCUSTOM_VIEWS value=$CUSTOM_VIEWS['Public']}{assign var=MINECUSTOM_VIEWS value=$CUSTOM_VIEWS['Mine']}
	{if empty($PUBLICCUSTOM_VIEWS) }{assign var=PUBLICCUSTOM_VIEWS value=array()}{/if}
	{if empty($MINECUSTOM_VIEWS) }{assign var=MINECUSTOM_VIEWS value=array()}{/if}
	{if count($PUBLICCUSTOM_VIEWS)}<span class="divider">|&nbsp;&nbsp;&nbsp;</span>
	{foreach item="CUSTOM_VIEW" from=$PUBLICCUSTOM_VIEWS}<li onclick="window.location.href='?module={$MODULE}&parent=&page=1&view=List&viewname={$CUSTOM_VIEW->get('cvid')}&orderby=&sortorder=&public="  class="btn-link"  ><a  href="?module={$MODULE}&parent=&page=1&view=List&viewname={$CUSTOM_VIEW->get('cvid')}&orderby=&sortorder=&public=">{vtranslate($CUSTOM_VIEW->get('viewname'), $MODULE)}</a></li><span class="divider">&nbsp;&nbsp;&nbsp;</span>
	{/foreach}{/if}
	{if count($MINECUSTOM_VIEWS)}<span class="divider">|&nbsp;&nbsp;&nbsp;</span>{foreach item="CUSTOM_VIEW" from=$MINECUSTOM_VIEWS}<li class="btn-link" ><a href="javascript:void(0)" data-editurl="{$CUSTOM_VIEW->getEditUrl()}" data-id="{$CUSTOM_VIEW->get('cvid')}" class="historyFilter">{if $CUSTOM_VIEW->get('viewname') neq 'All'}{vtranslate($CUSTOM_VIEW->get('viewname'), $MODULE)}&nbsp;<i class="icon-remove hide" data-deleteurl="{$CUSTOM_VIEW->getDeleteUrl()}"></i>{else}{vtranslate($CUSTOM_VIEW->get('viewname'), $MODULE)}&nbsp;{vtranslate($MODULE, $MODULE)}{/if}</a></li><span class="divider">&nbsp;&nbsp;&nbsp;</span>{/foreach}{/if}
	<span class="divider">&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;</span>
	{/if}<li><a href="javascript:void(0);" data-createurl="index.php?module=CustomView&view=EditAjax&source_module={$MODULE}" data-id="{$smarty.get.viewname}" id="advsearch">搜索<i class="icon-chevron-down" id="searchicon"></i></a></li>
 ********************************************************************************/
-->*}
{strip}
<ul class="breadcrumb" style="margin-bottom:5px;">
<li><i class="icon-filter"></i>T云报表分析</li><span class="divider"></span>

</ul>   
{/strip}