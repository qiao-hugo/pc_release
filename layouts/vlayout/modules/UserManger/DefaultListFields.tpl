{strip}
    <div class="row" style="padding: 10px 20px 0 20px;">
         <span class="span4">
			{if $LISTVIEW_LINKS['LISTVIEWBASIC']|@count gt 0}
				{foreach item=LISTVIEW_BASICACTION from=$LISTVIEW_LINKS['LISTVIEWBASIC']}
                    <span class="btn-group">
					<button id="{$MODULE}_listView_basicAction_{Vtiger_Util_Helper::replaceSpaceWithUnderScores($LISTVIEW_BASICACTION->getLabel())}" class="btn addButton" {if stripos($LISTVIEW_BASICACTION->getUrl(), 'javascript:')===0} onclick='{$LISTVIEW_BASICACTION->getUrl()|substr:strlen("javascript:")};'{else} onclick='window.location.href="{$LISTVIEW_BASICACTION->getUrl()}"'{/if}><i class="icon-plus icon-white"></i>&nbsp;<strong>{vtranslate($LISTVIEW_BASICACTION->getLabel(), $MODULE)}</strong></button>
					</span>
                {/foreach}
			{/if}
					{if $ISDEFAULT}

    <span>
    &nbsp;<input onclick="Vtiger_List_Js.showUserFieldEdit('index.php?module={$MODULE}&view=FieldAjax');" class="btn"
                 type="button" value="列表自定义"></span>
    <span>
    &nbsp;<input class="btn" id="clearcache"
                 type="button" value="更新缓存"></span>
                                <span style="margin-left: 2px;">
{*    &nbsp;<input class="btn" id="batch_adjust_superior" type="button" style="background-color: #3a98d8;color:white!important;" value="批量调整上级"></span>*}
					<button id="batch_adjust_superior" class="btn addButton"><strong>批量调整上级</strong></button>
                    <button id="batch_transfer" class="btn addButton" style="margin-left: 2px;"><strong>调岗</strong></button>

        </span>
                {/if}
        </span>
        {if $ISPAGE}
            <span class="span8" style="text-align: right">  <span>
           &nbsp;
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
    <span>&nbsp;<input type="text" name="jumppage" value="" id="jumppage" class="input-small"
                       style="width: 50px;margin-top: -13px;" placeholder="跳转">&nbsp;
</span>

    </span>
        {/if}
    </div>
{/strip}
