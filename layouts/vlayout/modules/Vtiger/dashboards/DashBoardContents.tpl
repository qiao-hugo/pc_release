{*<!--
/********
  ** 
  就这样吧首页暂时不用挂件了
  *
***********/
-->*}
{strip}
{include file="dashboards/UpdateNotice.tpl"|@vtemplate_path:$MODULE_NAME WIDGET=$WIDGET}
<div class="gridster span" style="width: 98%;">
	<ul>
	{assign var=COLUMNS value=2}
	{assign var=ROW value=1}
	{*{foreach from=$WIDGETS item=WIDGET name=count}
		{assign var=WIDGETDOMID value=$WIDGET->get('linkid')}
		{if $WIDGET->getName() eq 'MiniList'}
			{assign var=WIDGETDOMID value=$WIDGET->get('linkid')|cat:'-':$WIDGET->get('widgetid')}
		{elseif $WIDGET->getName() eq 'Notebook'}
			{assign var=WIDGETDOMID value=$WIDGET->get('linkid')|cat:'-':$WIDGET->get('widgetid')}
		{/if}
		<li id="{$WIDGETDOMID}" {if $smarty.foreach.count.index % $COLUMNS == 0 and $smarty.foreach.count.index != 0} {assign var=ROWCOUNT value=$ROW+1} data-row="{$WIDGET->getPositionRow($ROWCOUNT)}" {else} data-row="{$WIDGET->getPositionRow($ROW)}" {/if}
			{assign var=COLCOUNT value=($smarty.foreach.count.index % $COLUMNS)+1} data-col="{$WIDGET->getPositionCol($COLCOUNT)}" data-sizex="{$WIDGET->getWidth()}" data-sizey="{$WIDGET->getHeight()}"
			class="dashboardWidget dashboardWidget_{$smarty.foreach.count.index}" data-url="{$WIDGET->getUrl()}" data-mode="open" data-name="{$WIDGET->getName()}">
		</li>
	{/foreach}*}
	</ul>
	<input type="hidden" id=row value="{$ROWCOUNT}" />
	<input type="hidden" id=col value="{$COLCOUNT}" />
	<input type="hidden" id="userDateFormat" value="{$CURRENT_USER->get('date_format')}" />
	
	<!--
	{assign var=FROM value=$smarty.get.from}
	{if $FROM eq 'login'}
		{assign var=cookie value=$smarty.cookies.tlcrm}
		{*<script src="http://192.168.7.224:88/sso.php?cookie={$cookie}"></script>
		<script src="http://dingcan.trueland.net/sso.php?cookie={$cookie}"></script>*}
        <script src="http://192.168.7.224/sso.php?cookie={$cookie}"></script>
        <script src="http://dingcan.71360.com/sso.php?cookie={$cookie}"></script>
	{/if}
	-->
</div>
