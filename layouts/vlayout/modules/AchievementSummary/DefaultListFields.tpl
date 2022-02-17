{strip}
    <div class="row" style="padding: 10px 20px 0 20px;">
         <span class="span5">
			{if $LISTVIEW_LINKS['LISTVIEWBASIC']|@count gt 0}
				{foreach item=LISTVIEW_BASICACTION from=$LISTVIEW_LINKS['LISTVIEWBASIC']}
                    <span class="btn-group">
					<button id="{$MODULE}_listView_basicAction_{Vtiger_Util_Helper::replaceSpaceWithUnderScores($LISTVIEW_BASICACTION->getLabel())}" class="btn addButton" {if stripos($LISTVIEW_BASICACTION->getUrl(), 'javascript:')===0} onclick='{$LISTVIEW_BASICACTION->getUrl()|substr:strlen("javascript:")};'{else} onclick='window.location.href="{$LISTVIEW_BASICACTION->getUrl()}"'{/if}><i class="icon-plus icon-white"></i>&nbsp;<strong>{vtranslate($LISTVIEW_BASICACTION->getLabel(), $MODULE)}</strong></button>
					</span>
                {/foreach}
			{/if}
			{if $ISDEFAULT}
                <span><input onclick="Vtiger_List_Js.showUserFieldEdit('index.php?module={$MODULE}&view=FieldAjax');" class="btn" type="button" value="列表自定义"></span>
                {if $RECORD->personalAuthority('AchievementallotStatistic','exportCSV')}
                <span style="margin-left: 2px;"><button id="export" class="btn addButton"><strong>导出</strong></button></span>
                <span style="margin-left: 2px;"><button id="exportFinance" class="btn addButton"><strong>导出(财务)</strong></button></span>
                <span style="margin-left: 2px;"><button id="exportwithhold" class="btn addButton"><strong>导出暂扣</strong></button></span>
                <span style="margin-left: 2px;"><button id="exportgrant" class="btn addButton"><strong>导出交付发放</strong></button></span>
                {/if}
                {if $RECORD->personalAuthority('AchievementSummary','confirmend')}
                    <span style="margin-left: 2px;"><button id="confirm_end" class="btn addButton"><strong>确认完结</strong></button></span>
                <span style="margin-left: 2px;"><button id="cancel_confirm_end" class="btn addButton"><strong>撤销确认完结</strong></button></span>
                {/if}
            {/if}
        </span>
        {if $ISPAGE}
            <span class="span7" style="text-align: right">
                <span>
                <select id="limit" name="limit" style="width: 80px;margin-top: -13px;">
                    <option value="10">10</option>
                    <option value="20" selected="selected">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>&nbsp;
                </span>
                <span class="pagination" style="" id="pagination">
                    <ul class="pagination-demo">

                    </ul>
                </span>
                <span>&nbsp;<input type="text" name="jumppage" value="" id="jumppage" class="input-small" style="width: 50px;margin-top: -13px;" placeholder="跳转">&nbsp; </span>
            </span>
        {/if}
    </div>
{/strip}
