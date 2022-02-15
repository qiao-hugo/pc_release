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
                    {if $smarty.get.filter neq 'changeHistory'}
    <span>
    &nbsp;<input onclick="Vtiger_List_Js.showUserFieldEdit('index.php?module={$MODULE}&view=FieldAjax');" class="btn"
                 type="button" value="列表自定义"></span>{/if}{/if}
             {if $RECONCLILITION_LIST eq 'filed'}
                 {if $FLAGEXPORTDATA}
                     <span>  <button type="button" class="btn btn-primary exportdataReconciliation">导出(会计)</button></span>
                 {/if}
                 {if $IS_FILED eq true}
                     <span>  <button type="button" class="btn btn-primary reconciliationResultAgain">对账</button></span>
                 {/if}
             {else}
                 <span> <button type="button" class="btn btn-primary exportDataReconciliationResult">导出</button></span>
             {/if}

        </span>
        {if $RECONCLILITION_LIST eq 'filed'}
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
        {/if}
    </div>
{/strip}
