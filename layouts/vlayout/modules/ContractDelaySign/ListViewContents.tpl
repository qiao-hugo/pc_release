{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
-->*}
{strip}
	<style>
		.add-execution{
			padding: 10px;
			height: 40px;
		}

		.add-execution-tip{
			float:left;
			width: 10%;
			text-align: right;
		}

		.add-execution-node{
			float: left;
			width:12%;
			text-align: right;
		}
		.add-execution-info{
			float:left;
			width: 70%;
			margin-left: 10px;
		}
	</style>

    <div id="pagehtml">
<input type="hidden" id="view" value="{$VIEW}" />
<input type="hidden" id="pageStartRange" value="" />
<input type="hidden" id="pageEndRange" value="" />
<input type="hidden" id="previousPageExist" value="" />
<input type="hidden" id="nextPageExist" value="" />
<input type="hidden" id="alphabetSearchKey" value= "" />
<input type="hidden" id="Operator" value="{$OPERATOR}" />
<input type="hidden" id="alphabetValue" value="{$ALPHABET_VALUE}" />
<input type="hidden" id="totalCount" value="{$PAGE_COUNT}" />
<input type="hidden" id="isnotyun" value="{$ISNOTYUN}" />

<input type='hidden' value="{$PAGE_NUMBER}" id='pageNumber'>
<input type='hidden' value="{$PAGING_MODEL->getPageLimit()}" id='pageLimit'>
<input type="hidden" value="{$LISTVIEW_ENTIRES_COUNT}" id="noOfEntries">

{assign var = ALPHABETS_LABEL value = vtranslate('LBL_ALPHABETS', 'Vtiger')}
{assign var = ALPHABETS value = ','|explode:$ALPHABETS_LABEL}




<div class="listViewEntriesDiv contents-bottomscroll" style="overflow:auto;">
	<div class="bottomscroll-div" >
	<input type="hidden" value="{$ORDER_BY}" id="orderBy">
	<input type="hidden" value="{$SORT_ORDER}" id="sortOrder">

	{assign var=WIDTHTYPE value=$CURRENT_USER_MODEL->get('rowheight')}
	<table class="table listViewEntriesTable">
		<thead>
			<tr class="listViewHeaders">
				{if $LISTVIEW_FIELDS}
					{foreach key=KEY item=LISTVIEW_HEADER from=$LISTVIEW_FIELDS}
						{if in_array($LISTVIEW_HEADER['columnname'],$CONTINUECOLUMN)}
							{continue}
							{/if}
				<th nowrap data-field="{$LISTVIEW_HEADERS[$KEY]['columnname']}">
					{vtranslate($KEY, $MODULE)}
				</th>
				{/foreach}
				{else}
				{foreach key=KEY item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
					{if in_array($LISTVIEW_HEADER['columnname'],$CONTINUECOLUMN)}
						{continue}
					{/if}
				<th nowrap data-field="{$LISTVIEW_HEADER['columnname']}">
					{vtranslate($KEY, $MODULE)}
				</th>
				{/foreach}
				{/if}
				<th nowrap style="width:90px">操作</th>
			</tr>
		</thead>
		{foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=listview}
		<tr class="listViewEntries"  data-id='{$LISTVIEW_ENTRY['id']}' data-recordUrl='index.php?module={$MODULE}&view=Detail&record={$LISTVIEW_ENTRY['id']}' id="{$MODULE}_listView_row_{$smarty.foreach.listview.index+1}">
            {foreach key=fkey item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS name=fieldview}
				{if in_array($LISTVIEW_HEADER['columnname'],$CONTINUECOLUMN)}
					{continue}
				{/if}
                <td class="listViewEntryValue"  nowrap>
                     {if $LISTVIEW_HEADER['columnname'] eq 'subject'}
                            <a class="btn-link" href='index.php?module={$MODULE}&view=Detail&record={$LISTVIEW_ENTRY['id']}' target="_block">{vtranslate( $LISTVIEW_ENTRY[$LISTVIEW_HEADER['columnname']],$MODULE)}</a>
                        {else}

                            {uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)}
                        {/if}
                </td>
			{/foreach}

            <td class="listViewEntryValue" >
                {if $LISTVIEW_HEADER@last}

                    <div  style="width:90px">
                        <a  href="index.php?module={$MODULE}&view=Detail&record={$LISTVIEW_ENTRY['id']}"><i title="详细信息" class="icon-th-list alignMiddle"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;

						{if in_array($LISTVIEW_ENTRY['contract_type'],$TYUNTYPE) && in_array($LISTVIEW_ENTRY['modulestatus'],array('a_apply_normal','c_apply_stop'))
						&&  !in_array($LISTVIEW_ENTRY['hetongstatus'],array('c_stop','c_cancel','c_canceling')) && $LISTVIEW_ENTRY['contractsignstatus']=='nosign' }
                        	<a   class="delayapply btn-link" data-id="{$LISTVIEW_ENTRY['id']}" data-contract_no="{$LISTVIEW_ENTRY['servicecontractsid']}" data-accountname="{$LISTVIEW_ENTRY['accountid']}"  target="_block">申请</a>&nbsp;
                        {/if}
                        {*{if $IS_MODULE_DELETABLE}*}
                        	{*<a class="deleteRecordButton"><i title="删除" class="icon-trash alignMiddle"></i></a>*}
                        {*{/if}     *}
                    </div>
                {/if}
            </td>
		</tr>
		{/foreach}

	</table>

</div>
</div>
    </div>
	<script>
        $(".delayapply").on("click",function () {
            var recordid=$(this).data('id');
            var accountname=$(this).data('accountname');
            var contractno=$(this).data('contract_no');
            str = '<div id="myModal" class="modal" style="">\n' +
                '\t<div class="modal-dialog">\n' +
                '\t\t<div class="modal-content">\n' +
                '\t\t\t<div class="modal-header">\n' +
                '\t\t\t\t<button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>\n' +
                '\t\t\t\t<h4 class="modal-title">申请延期签收</h4>\n' +
                '\t\t\t\t<div style="margin-top: 20px;" id="supervisor">\n';
            str += '\t\t\t\t<div class="confirm tc">\n';
            str += '<input type="hidden" name="contractdelaysignid" value="'+recordid+'"/>';


            str += '<div class="add-execution"><div class="add-execution-tip"><span style="color: white">*</span></div><div class="add-execution-node">合同编号</div><div class="add-execution-info"><input name="contractno" type="text" value="'+contractno+'" readonly></div></div>';
            str += '<div class="add-execution"><div class="add-execution-tip"><span style="color: white">*</span></div><div class="add-execution-node">客户名称</div><div class="add-execution-info"><input name="accountname" type="text" value="'+accountname+'" readonly></div></div>';
            str += '<div class="add-execution"><div class="add-execution-tip"><span style="color: red">*</span></div><div class="add-execution-node">延长的理由</div><div class="add-execution-info"><textarea name="reason"  placeholder="请输入延长的理由" ></textarea></div></div>';
            str += '<div class="add-execution" style="height: 100px;" xmlns="http://www.w3.org/1999/html"><div class="add-execution-tip"><span style="color: red">*</span></div><div class="add-execution-node">合同扫描件</div><div class="add-execution-info">'+
                '    <div class="add-execution-info"><div class="fileUploadContainer" xmlns="http://www.w3.org/1999/html">\n' +
                '                                <div class="upload">\n' +
                '                                    <input type="button" id="uploadButton" value="上传"  title="支持pdf/png/jpg文件，不超过3M" />\n' +
                '<span style="font-size:8px;color:gray">支持pdf/png/jpg文件不超过3M</span>'+
                '                                    <div style="display:inline-block;margin-top: 15px;" id="fileall">\n' +
                '                                            <input class="ke-input-text filedelete" type="hidden" name="file" id="file" value="" readonly="readonly" />\n' +
                '                                            <input class="filedelete" type="hidden" name="attachmentsid" value="">\n' +
                '                                    </div>\n' +
                '                                </div>\n' +
                '                            </div></div>'
                +'</div></div>';

            str +=                        '\n' +
                '\t\t\t\t</div>\n' +
                '\t\t\t</div>\n' +
                '\t\t\t<div class="modal-footer">\n' +
                '\t\t\t\t<div class=" pull-right cancelLinkContainer"><a class="cancelLink" type="reset" data-dismiss="modal">取消</a></div>\n' +
                '\t\t\t\t<button class="btn btn-success" id="transferPost" type="submit">确定</button>\n' +
                '\t\t\t</div>\n' +
                '\t\t</div>\n' +
                '\t</div>\n' +
                '</div>';
            app.showModalWindow(str);
            $("#uploadButton").trigger('click');
            $('.modal-backdrop').css({
                "opacity":"0.6",
                "z-index":"0"
            });
        })
	</script>
{/strip}
