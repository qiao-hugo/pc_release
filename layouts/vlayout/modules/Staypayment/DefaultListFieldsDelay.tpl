{strip}
    <div class="row" style="padding: 10px 20px 0 20px;">
         <span class="span4">
                <span class="btn-group">
                    <button id="{$MODULE}_listView_basicAction_delayExport" class="btn btn-success delayExportButton"><strong>导出</strong></button>
                </span>
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
