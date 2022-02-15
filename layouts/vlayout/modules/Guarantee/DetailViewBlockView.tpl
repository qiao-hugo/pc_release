{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*<!--去除双击编辑-->
 ********************************************************************************/
-->*}
{strip}
	{*{foreach key=BLOCK_LABEL_KEY item=FIELD_MODEL_LIST from=$RECORD_STRUCTURE}
	{assign var=BLOCK value=$BLOCK_LIST[$BLOCK_LABEL_KEY]}
	{if $BLOCK eq null or $FIELD_MODEL_LIST|@count lte 0}{continue}{/if}
	{assign var=IS_HIDDEN value=$BLOCK->isHidden()}
	{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
	<input type=hidden name="timeFormatOptions" data-value='{$DAY_STARTS}' />*}
        {if !empty($GUARANTEELIST['user'])}
            <br>
            <br>
	<table class="table table-bordered  detailview-table">
		<thead>
		<tr>
            <th class="blockHeader one"><img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id=>
                <img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id=>负责人<i data-container="body" data-toggle="popover" data-placement="top"data-content="<span style=color:red;font-weight:bold;>您所担保的工单</span>" data-mode="show"></i></th>
            <th class="blockHeader">服务合同</th>
            <th class="blockHeader">工单主题</th>
            <th class="blockHeader">创建时间</th>
            <th class="blockHeader">金额</th>

		</tr>
		</thead>
		 <tbody {if $IS_HIDDEN} class="hide" {/if}>
		{assign var=COUNTER value=0}
        {assign var=SUMVAL value=[]}
        {foreach item=FIELD_MOD key=FIELD_NA from=$GUARANTEELIST['user'] name=blockfields}
        <tr>
            <td>{$FIELD_MOD['userid']}</td>
            <td>{*<a class="btn-link" href="index.php?module=ServiceContracts&view=Detail&record={$FIELD_MOD['contractid_reference']}&realoperate={setoperate($FIELD_MOD['contractid_reference'],'ServiceContracts')}" target=_blank>*}{$FIELD_MOD['contractid']}{*</a>*}</td>
            <td>{*<a class="btn-link" href="index.php?module=SalesOrder&view=Detail&record={$FIELD_MOD['salesorderid_reference']}&realoperate={setoperate($FIELD_MOD['salesorderid_reference'],'SalesOrder')}" target=_blank>*}{$FIELD_MOD['salesorderid']}{*</a>*}</td>
            <td>{$FIELD_MOD['createdtime']}</td>
            <td>{$FIELD_MOD['total']}</td>
            {$SUMVAL[]=$FIELD_MOD['total']}
		</tr>
            {if $smarty.foreach.blockfields.last}
            <tr>
                <td colspan="4" class="blockHeader">合计</td>
                <td class="blockHeader"><span class="label label-warning">{array_sum($SUMVAL)}</span></td>
            </tr>
            {/if}
        {/foreach}
		</tbody>
	</table>
{/if}
	{*{/foreach}*}
    {if !empty($GUARANTEELIST['saleorder'])}
        <br>
        <br>
        <table class="table table-bordered detailview-table">
            <thead>
            <tr>
                <th  class="blockHeader two"><img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id=>
                    <img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id=>负责人<i data-container="body" data-toggle="popover" data-placement="top"data-content="<span style=color:red;font-weight:bold;>工单担保金额记录</span>" data-mode="show"></i></th>
                <th class="blockHeader">服务合同</th>
                <th class="blockHeader">工单主题</th>
                <th class="blockHeader">创建时间</th>
                <th class="blockHeader">金额</th>

            </tr>
            </thead>
            <tbody {if $IS_HIDDEN} class="hide" {/if}>
            {assign var=SUMVAL value=[]}
            {foreach item=FIELD_MOD key=FIELD_NA from=$GUARANTEELIST['saleorder'] name=blockfields}

                <tr>

                    <td>{$FIELD_MOD['userid']}</td>
                    <td>{*<a class="btn-link" href="index.php?module=ServiceContracts&view=Detail&record={$FIELD_MOD['contractid_reference']}&realoperate={setoperate($FIELD_MOD['contractid_reference'],'ServiceContracts')}" target=_blank>*}{$FIELD_MOD['contractid']}{*</a>*}</td>
                    <td>{*<a class="btn-link" href="index.php?module=SalesOrder&view=Detail&record={$FIELD_MOD['salesorderid_reference']}&realoperate={setoperate($FIELD_MOD['salesorderid_reference'],'SalesOrder')}" target=_blank>*}{$FIELD_MOD['salesorderid']}{*</a>*}</td>
                    <td>{$FIELD_MOD['createdtime']}</td>
                    <td>{$FIELD_MOD['total']}</td>
                    {$SUMVAL[]=$FIELD_MOD['total']}

                </tr>
                {if $smarty.foreach.blockfields.last}
                    <tr>
                        <td colspan="4" class="blockHeader">合计</td>
                        <td class="blockHeader"><span class="label label-warning">{array_sum($SUMVAL)}</span></td>
                    </tr>
                {/if}
            {/foreach}
            </tbody>
        </table>
    {/if}

    {if !empty($GUARANTEELIST['saleorderhistory'])}
        <br>
        <br>
        <table class="table table-bordered  detailview-table">
            <thead>
            <tr>
                <th  class="blockHeader three"><img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id=>
                    <img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id=>负责人<i data-container="body" data-toggle="popover" data-placement="top"data-content="<span style=color:red;font-weight:bold;>工单担保冲销记录</span>" data-mode="show"></i></th>
                <th class="blockHeader">服务合同</th>
                <th class="blockHeader">工单主题</th>
                <th class="blockHeader">创建时间</th>
                <th class="blockHeader">担保金额</th>
                <th class="blockHeader">冲销时间</th>
                <th class="blockHeader">冲销金额</th>

            </tr>
            </thead>
            <tbody {if $IS_HIDDEN} class="hide" {/if}>
            {foreach item=FIELD_MOD key=FIELD_NA from=$GUARANTEELIST['saleorderhistory'] name=blockfields}
                <tr>
                    <td>{$FIELD_MOD['userid']}</td>
                    <td>{*<a class="btn-link" href="index.php?module=ServiceContracts&view=Detail&record={$FIELD_MOD['contractid_reference']}&realoperate={setoperate($FIELD_MOD['contractid_reference'],'ServiceContracts')}" target=_blank>*}{$FIELD_MOD['contractid']}{*</a>*}</td>
                    <td>{*<a class="btn-link" href="index.php?module=SalesOrder&view=Detail&record={$FIELD_MOD['salesorderid_reference']}&realoperate={setoperate($FIELD_MOD['salesorderid_reference'],'SalesOrder')}" target=_blank>*}{$FIELD_MOD['salesorderid']}{*</a>*}</td>
                    <td>{$FIELD_MOD['createdtime']}</td>
                    <td>{$FIELD_MOD['total']}</td>
                    <td>{$FIELD_MOD['deltatime']}</td>
                    <td>{$FIELD_MOD['delta']}</td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    {/if}
    {if !empty($GUARANTEELIST['alls'])}
        <br>
        <br>
        <table class="table table-bordered equalSplit detailview-table tableall">
            <thead>
            <tr>
                <th  class="blockHeader three"><img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id=>
                    <img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id=>负责人</th>
                <th class="blockHeader">总担保金额</th>

            </tr>
            </thead>
            <tbody {if $IS_HIDDEN} class="hide" {/if}>
            {assign var=COUNTER value=0}
            {foreach item=FIELD_MOD key=FIELD_NA from=$GUARANTEELIST['alls'] name=blockfields}
                <tr>
                    <td>{$FIELD_MOD['userid']}</td>
                    <td>{$FIELD_MOD['totals']}</td>
                </tr>

            {/foreach}
            </tbody>
        </table><br><br>
    {/if}
    <script type="text/javascript" src="/libraries/media/jquery.dataTables.js"></script>
{literal}
<script>
    $(function ()
    { $("[data-toggle='popover']").popover('show');
    });
    jQuery('.tableall').DataTable( {
        language: {"sProcessing":   "处理中...",	"sLengthMenu":   '显示 _MENU_ 项<i data-container="body" data-toggle="popover" data-placement="top"data-content="<span style=color:red;font-weight:bold;>担保人担保记录</span>" data-mode="show"></i>结果',"sZeroRecords":  "没有匹配结果","sInfo":         "显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",	"sInfoEmpty":    "显示第 0 至 0 项结果，共 0 项","sInfoFiltered": "(由 _MAX_ 项结果过滤)","sInfoPostFix":  "","sSearch":"当前页快速检索:","sUrl":"","sEmptyTable":     "表中数据为空","sLoadingRecords": "载入中...",
            "sInfoThousands":  ",",	"oPaginate": {"sFirst":"首页","sPrevious": "上页","sNext":"下页","sLast":"末页"},
            "oAria": {"sSortAscending":  ": 以升序排列此列","sSortDescending": ": 以降序排列此列"}},
        "scrollY":"500px",
        "bAutoHeight":true,
        "bSort": false,
        "scrollCollapse":false,
        "scrollBody":"300px",
        'sScrollX':"disabled",
        "aoColumnDefs": [
            {
                sDefaultContent: '',
                aTargets: [ '_all' ]
            }
        ],
        "bDestroy": true,
        aLengthMenu: [[5, 10, 50, -1], [5, 10, 50, "All"]],
        fnDrawCallback:function(){
        }

    } );
</script>
{/literal}
{/strip}