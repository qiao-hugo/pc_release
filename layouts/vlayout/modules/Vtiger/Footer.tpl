{*<!--
/***************
 * 底部文件提醒
 *************/
-->*}
{strip}
		<input id='activityReminder' class='hide noprint' type="hidden" value="{$ACTIVITY_REMINDER}"/>
		<footer class="noprint">
        <div class="vtFooter"><font style="" >{vtranslate('POWEREDBY')} {$VTIGER_VERSION} &nbsp;&copy; {date('Y')} &nbsp&nbsp;</font></div>
		</footer>
		{* 加载JS *}
		{include file='JSResources.tpl'|@vtemplate_path}
		</div>
	{*<div class="window" id="right" style="background-color: #E7F4FE;width:250px;height:185px;margin: 5px;display:none;position:absolute;bottom:15px;z-index:1029;right:12px;-webkit-border-radius: 4px 0 4px 0;
     -moz-border-radius: 4px 0 4px 0;
          border-radius: 4px 0 4px 0;">
		<div class="title" style="padding: 4px;font-size: 14px;">
			<span class="icon-remove wtitle" style="position:relative;top:4px;left:228px;cursor:pointer;"></span>
			系统消息
		</div>
		<div class="content" id="mssageCont" style="height:132px;background-color: white;border: 2px solid #D0DEF0;padding: 2px;overflow: auto;padding:20px 20px 0 20px;z-index:1029;">
			<p class="text-center">暂无消息</p>
		</div>
	</div>*}
    <div class="widgetContaine_footmsg" data-url="module=WorkFlowCheck&amp;view=List&amp;mode=getNotices" data-name="">
        <div class="widget_contents" id="footmsg">
        </div>
    </div>

</body>
<div id="dialog-message" class="hide">加载中... </div>
</html>
{/strip}
