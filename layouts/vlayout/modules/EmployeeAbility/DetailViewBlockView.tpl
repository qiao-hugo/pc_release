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
	<style>
		#div_toop {
			background-color:rgba(193, 223, 251, 1);
			padding: 10px;
			border: 1px solid #eeeeee;
			box-shadow: 5px 5px 5px #eeeeee;
		}

		#div_toop .title {
			width: 70px;
			display: inline-block;
			text-align: right;
			margin-right: 10px;
		}
	</style>
    <div style="background-color: white">
    <input type="hidden" name="recordid" value="{$RECORDID}"/>
        <span style="font-weight: bold">&nbsp;&nbsp;任务完成者： {$JOBHOLDER}  </span>&nbsp;&nbsp;
        <select name="stafflevel" id="stafflevel">
            <option value="all"  {if $STAFFLEVEL=='all'}selected{/if}>全部</option>
            <option value="junior" {if $STAFFLEVEL=='junior'}selected{/if}>初级</option>
            <option value="intermediate"  {if $STAFFLEVEL=='intermediate'}selected{/if}>中级</option>
            <option value="senior"  {if $STAFFLEVEL=='senior'}selected{/if}>高级</option>
        </select>
    </div>
<table class="table table-bordered blockContainer salesorderrayment_tab detailview-table" style="margin-bottom: 5px;">
	<thead>
	<tr>
		<th class="blockHeader">达标任务</th>
		<th class="blockHeader">完成方法</th>
		<th class="blockHeader">文档提交</th>
		<th class="blockHeader">状态</th>
		<th class="blockHeader">操作</th>
	</tr>
	</thead>
	<tbody>
	{assign var=STATUS value=array('inreview','completed')}
    {foreach item=DATA key=key from=$DATAS}
	<tr>
			<td style="width: 20%"><label class="target_des">{vtranslate($key, $MODULE)}</label></td>
			<td style="width: 25%"><label >{vtranslate("{$key}_text", $MODULE)} {if $COLUMNDATA[$key]['eduurl'] }<br/>复制地址:<span style="color: blue;cursor: pointer" onclick="copyText('{$COLUMNDATA[$key]['eduurl']}')">{$COLUMNDATA[$key]['eduurl']}</span>{/if}</label></td>
			<td style="width: 10%">
                <label >
                    {if count($ISMANAGER) and in_array($key,$COLLEAGECOLUMNS) and $DATA['step']=='1' && $COLUMNDATA[$key]['defaultvalue']!='1'}
                        {if  in_array($DATA['nextreviewer'],$ISMANAGER) and $DATA['status']=='underreviewer'}
                            <input class="managerinput" data-field="{$key}" style="width: 50%" type="text"
                                   oninput="if(value>100){
                                           value=100
                                   }else{
                                        value=value.replace(/[^\d]/g,'')
                                   }
                                   if(value.indexOf(0)==0){
                                    value=0
                                   }" value="{$DATA['wordsub']}">
                        {else}
                            <input class="managerinput2" data-field="{$key}" style="width: 50%" type="text" value="{$DATA['wordsub']}" disabled/>
                            {*{$DATA['wordsub']}*}
                        {/if}
                    {elseif in_array($key,$SPECIALCOLLEAGECOLUMNS)}

                    {else}
                        {if in_array($key,$COLLEAGECOLUMNS) && !$DATA['status']}

                        {else}
                            {vtranslate($DATA['wordsub'], $MODULE)}
                        {/if}
                    {/if}
                </label>
            </td>
			<td style="width: 10%">
                <label class="statusinfo" {if $DATA['status']=='reject'} style="color: red" {/if}data-status="{$DATA['status']}" data-rejectnum="{$DATA['rejectnum']}" data-reviewresult="{$DATA['reviewresult']}" data-rejectreason="{$DATA['rejectreason']}" data-rejector="{$DATA['rejector']}">
                    {vtranslate($DATA['status'], $MODULE)}
                </label>
            </td>
			<td style="width: 25%" class="operatetd">
                    {if count($ISMANAGER)}
                        {if ($DATA['status']!='completed' && in_array($DATA['nextreviewer'],$ISMANAGER) && in_array($DATA['status'],array('underreviewer','inreview')) && $DATA['step']) }
                            <a  class="operate" data-type="pass" data-field="{$key}"><span >通过</span> </a> &nbsp;&nbsp;
                            {if in_array($key,$SPECIALCOLLEAGECOLUMNS) && $DATA['nextreviewer'] == 1}
                                <span style="color: grey">驳回</span>
                            {else}
                                <a  class="operate" data-type="reject" data-field="{$key}"><span>驳回</span></a> &nbsp;&nbsp;
                            {/if}
                        {else}
                            <span style="color: grey">通过</span>&nbsp;&nbsp;<span style="color: grey">驳回</span> &nbsp;&nbsp;
                        {/if}
                    {else}
                        {if (!$DATA['nextreviewer'] && !$DATA['step'])}
                            <a class="subData" data-field="{$key}" data-fileid="{$DATA['fileid']}"><span>提交</span></a>
                        {*{elseif in_array($key,$COLLEAGECOLUMNS)}*}
                            {*/*}
                        {else}
                            <span style="color: grey">提交</span>
                        {/if} &nbsp;&nbsp;
                    {/if}
                    {*{if $DATA['wordsub']==='submitted'}*}
                        {*{$DATA['filestr']}*}
                    {*{elseif in_array($key,$COLLEAGECOLUMNS)}*}

                    {*{else}*}
                        {*<span style="color: grey">下载</span>*}
                    {*{/if}*}
			</td>
	</tr>
	{/foreach}
	</tbody>
</table>
    <div style="opacity: 0%">
    <textarea  value=""  id="input"></textarea>
    </div>
{/strip}

<script src="https://cdn.jsdelivr.net/clipboard.js/1.5.12/clipboard.min.js"></script>
<script>
    $(function () {

        $(".statusinfo").mouseover(function (e) {
            var status = $(this).attr('data-status');
             var rejectreason=$(this).attr('data-rejectreason');
             var rejectnum=$(this).attr('data-rejectnum');
            var rejector=$(this).attr('data-rejector');
            var reviewresult = $(this).attr("data-reviewresult");
            console.log(reviewresult);
            console.log(status);
            if((status=='reject' && (!rejectreason || !rejector)) || (status!=='reject' && !reviewresult)){
                return;
            }

            var div_toop = '';
            div_toop += ' <div id="div_toop" style="width: 300px;word-wrap: break-word;word-break: break-all">';
            if(status=='reject'){
                div_toop += '<span>第'+rejectnum+'次驳回</span><br>';
                div_toop += '<span>驳回人:'+rejector+'</span><br>';
                div_toop += '<span>驳回原因:'+rejectreason+'</span><br>';
            }else{
                div_toop += '<span>'+replaceAll(reviewresult)+'</span><br>';
            }

            div_toop += '</div>';

            $("body").append(div_toop);
            $("#div_toop")
                .css({
                    "top": (e.pageY + 10) + "px",
                    "position": "absolute",
                    "left": (e.pageX + 20) + "px",
                }).show("fast");
        }).mouseout(function () {
            $("#div_toop").remove();
        }).mousemove(function (e) {
            $("#div_toop")
                .css({
                    "top": (e.pageY + 10) + "px",
                    "position": "absolute",
                    "left": (e.pageX + 20) + "px",
                });
        });

        document.addEventListener("drop",function(e){
            e.preventDefault();
        });
        document.addEventListener("dragleave",function(e){
            e.preventDefault();
        });
        document.addEventListener("dragenter",function(e){
            e.preventDefault();
        });
        document.addEventListener("dragover",function(e){
            e.preventDefault();
        });

    });

    function replaceAll(str)
    {
        if(str!=null)
            str = str.replace(/#n#/g,"<br>")
        return str;
    }
    function copyText(text) {
        console.log(text);
        var input = document.getElementById("input");
        input.value = text;
        input.select();
        console.log(input.value);
        document.execCommand("copy");
        alert("复制成功");
    }


</script>