{*<!--
/*********************************************************************************

*<td>
	是否符合Tsite{$LINE_ITEM_DETAIL["Tsite"]}是否符合Tsite新动力{$LINE_ITEM_DETAIL["TsiteNew"]}</br>
</td>
********************************************************************************/
-->*}
<table class="table table-bordered blockContainer detailview-table" id="lineItemNextDayVisit" border="1">
    <thead>
    <tr>
        <th colspan="8" class="blockHeader">
            <img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;">
            <img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">
            &nbsp;&nbsp;汇总分析
        </th>
    </tr>


    </thead>
    <tbody>
    <tr>
        <td nowrap>拜访单数量</td>
        <td nowrap>已点评拜访单数量</td>
        <td nowrap>类型</td>
        <td nowrap>点评结果</td>
        <td nowrap>数量</td>
        <td nowrap>比例</td>
    </tr>
    {assign var=Count value=$RECORDCOMMENTANALYSIS['data']|count}
    {foreach item=NEXTDAYVISIT from=$RECORDCOMMENTANALYSIS['data'] key=KEYL}
        <tr>

            {if $KEYL eq 0}
            <td rowspan="{$Count}">{$NEXTDAYVISIT['visitingnum']}</td>
            <td rowspan="{$Count}">{$NEXTDAYVISIT['visitingcommnum']}</td>
            {/if}
            {if $NEXTDAYVISIT['classic'] neq $TEMPFIELD}
                {assign var=TEMPFIELD value=$NEXTDAYVISIT['classic']}
            <td  rowspan="{$RECORDCOMMENTANALYSIS['nums'][$NEXTDAYVISIT['classic']]}">{vtranslate($NEXTDAYVISIT['classic'],'VisitAccountContract')}</td>
            {/if}
            <td>{vtranslate($NEXTDAYVISIT['commentresult'],'VisitAccountContract')}</td>
            <td>{$NEXTDAYVISIT['poornumber']}</td>
            <td>{($NEXTDAYVISIT['poornumber']/$NEXTDAYVISIT['visitingcommnum']*100)|number_format:2}%</td>

        </tr>
    {/foreach}


    </tbody>
</table>
<table class="table table-bordered blockContainer detailview-table" id="lineItemConcat" border="1">
    <thead>
    {*<tr>
        <th colspan="13" class="blockHeader">
            <img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;">
            <img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">
            &nbsp;&nbsp;点评详情
        </th>
    </tr>*}
    <tr>
        <th nowrap>提单人</th>
        <th nowrap>拜访单状态</th>
        <th nowrap>客户名称</th>
        <th nowrap>主题</th>
        <th nowrap>开始日期</th>
        <th nowrap>结束日期</th>
        <th nowrap>拜访目的</th>
        <th nowrap>已点评</th>
        <th nowrap>音频类型</th>
        <th nowrap>点评结果</th>
        <th nowrap>点评详情</th>
        <th nowrap>点评人员</th>
        <th nowrap>点评日期</th>
    </tr>

    </thead>
    <tbody>

    {foreach item=DATADETAIL from=$RECORDCOMMENTANALYSIS['datadetail'] key=KEYL}
        <tr>

            <td nowrap>{$DATADETAIL['username']}</td>
            <td nowrap>{vtranslate($DATADETAIL['modulestatus'],'Vtiger')}</td>
            <td nowrap>{$DATADETAIL['accname']}</td>
            <td nowrap>{$DATADETAIL['subject']}</td>
            <td nowrap>{$DATADETAIL['startdate']}</td>
            <td nowrap>{$DATADETAIL['enddate']}</td>
            <td nowrap>{$DATADETAIL['purpose']}</td>
            <td nowrap>{vtranslate($DATADETAIL['commentstaus'],'VisitAccountContract')}</td>
            <td>{vtranslate($DATADETAIL['classic'],'VisitAccountContract')}</td>
            <td >{vtranslate($DATADETAIL['commentresult'],'VisitAccountContract')}</td>
            <td >{$DATADETAIL['remark']}</td>
            <td nowrap>{$DATADETAIL['dusername']}</td>
            <td nowrap>{$DATADETAIL['commentdatetime']}</td>

        </tr>
    {/foreach}


    </tbody>
</table>
<br>
<br>
<script type="text/javascript" src="/libraries/media/jquery.dataTables.js"></script>
{literal}
<style type="text/css">
    .table  thead th{
        white-space: nowrap;
    }
</style>
<script type="text/javascript">
    jQuery('#lineItemConcat').DataTable({
        language: {
            "sProcessing": "处理中...",
            "sLengthMenu": "显示 _MENU_ 项结果",
            "sZeroRecords": "没有匹配结果",
            "sInfo": "显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",
            "sInfoEmpty": "显示第 0 至 0 项结果，共 0 项",
            "sInfoFiltered": "(由 _MAX_ 项结果过滤)",
            "sInfoPostFix": "",
            "sSearch": "当前页快速检索:",
            "sUrl": "",
            "sEmptyTable": "表中数据为空",
            "sLoadingRecords": "载入中...",
            "sInfoThousands": ",",
            "oPaginate": {"sFirst": "首页", "sPrevious": "上页", "sNext": "下页", "sLast": "末页"},
            "oAria": {"sSortAscending": ": 以升序排列此列", "sSortDescending": ": 以降序排列此列"}
        },
        scrollY: "300px",
        sScrollX: "disabled",
        aLengthMenu: [ 50, 100, 500, 1500 ],
        fnDrawCallback: function () {
        }
    });

</script>
{/literal}
