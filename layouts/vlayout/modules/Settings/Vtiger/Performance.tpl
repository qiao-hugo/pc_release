{strip}
<div class="container-fluid" id="PerformanceContainer">
	<div class="widget_header row-fluid">
		<div class="row-fluid"><h3>{vtranslate('LBL_PERFORMANCE', $QUALIFIED_MODULE)}</h3></div>
	</div>
	<hr>

	<div class="contents row-fluid">
		<div class="span11 padding10">
			<textarea class="input-xxlarge performanceContent textarea-autosize" rows="3" placeholder="{vtranslate('LBL_ENTER_ANNOUNCEMENT_HERE', $QUALIFIED_MODULE)}" >0,{$performance['val']}</textarea>
		</div>
		<div class="row-fluid">
			<div class="span11 padding1per">
				<button class="btn btn-success pull-right savePerformance hide"><strong>{vtranslate('LBL_SAVE', $QUALIFIED_MODULE)}</strong></button>
			</div>
		</div>
	</div>
</div>
{/strip}