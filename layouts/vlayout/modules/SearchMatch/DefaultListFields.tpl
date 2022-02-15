{strip}
    <div class="row" style="padding: 10px 20px 0 20px;">
         <span class="span4" style="color: red;" >
             <p style="margin: 0">提示：输入回款必填信息，才可操作匹配回款。规则如下：</p>
             <p style="margin: 0">对公转账：入账日期+回款抬头+回款原币金额</p>
             <p style="margin: 0">支付宝转账：交易单号+回款抬头</p>
             <p style="margin: 0">扫码：交易单号</p>
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
    <span>&nbsp;<input type="text" name="jumppage" value="" id="jumppage" class="input-small" style="width: 50px;margin-top: -13px;" placeholder="跳转">&nbsp;

</span>

    </span>
        {/if}
    </div>
{/strip}
