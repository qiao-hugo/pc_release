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

	{foreach key=BLOCK_LABEL_KEY item=FIELD_MODEL_LIST from=$RECORD_STRUCTURE}
	{assign var=BLOCK value=$BLOCK_LIST[$BLOCK_LABEL_KEY]}
	{if $BLOCK eq null or $FIELD_MODEL_LIST|@count lte 0}{continue}{/if}
	{assign var=IS_HIDDEN value=$BLOCK->isHidden()}
	{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
	<input type=hidden name="timeFormatOptions" data-value='{$DAY_STARTS}' />
	<table class="table table-bordered equalSplit detailview-table">
		<thead>
		<tr>
				<th class="blockHeader" colspan="4">
						<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id={$BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id')}>
						<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id={$BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id')}>
						&nbsp;&nbsp;{vtranslate({$BLOCK_LABEL_KEY},{$MODULE_NAME})}
				</th>
		</tr>
		</thead>
		 <tbody {if $IS_HIDDEN} class="hide" {/if}>
		{assign var=COUNTER value=0}
		<tr>
		{foreach item=FIELD_MODEL key=FIELD_NAME from=$FIELD_MODEL_LIST}
			{if !$FIELD_MODEL->isViewableInDetailView()}
				 {continue}
			 {/if}
			 {if $FIELD_MODEL->get('uitype') eq "83"}
				{foreach item=tax key=count from=$TAXCLASS_DETAILS}
				{if $tax.check_value eq 1}
					{if $COUNTER eq 2}
						</tr><tr>
						{assign var="COUNTER" value=1}
					{else}
						{assign var="COUNTER" value=$COUNTER+1}
					{/if}
					<td class="fieldLabel {$WIDTHTYPE}">
					<label class='muted pull-right marginRight10px'>{vtranslate($tax.taxlabel, $MODULE)}(%)</label>
					</td>
					 <td class="fieldValue {$WIDTHTYPE}">
						 <span class="value">
							 {$tax.percentage}
						 </span>
					 </td>
				{/if}
				{/foreach}
			{else if $FIELD_MODEL->get('uitype') eq "69" || $FIELD_MODEL->get('uitype') eq "105"}
				{if $COUNTER neq 0}
					{if $COUNTER eq 2}
						</tr><tr>
						{assign var=COUNTER value=0}
					{/if}
				{/if}
				<td class="fieldLabel {$WIDTHTYPE}"><label class="muted pull-right marginRight10px">{vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}</label></td>
				<td class="fieldValue {$WIDTHTYPE}">
					<div id="imageContainer" width="300" height="200">
						{foreach key=ITER item=IMAGE_INFO from=$IMAGE_DETAILS}
							{if !empty($IMAGE_INFO.path) && !empty({$IMAGE_INFO.orgname})}
								<img src="{$IMAGE_INFO.path}_{$IMAGE_INFO.orgname}" width="300" height="200">
							{/if}
						{/foreach}
					</div>
				</td>
				{assign var=COUNTER value=$COUNTER+1}
			{else}
				{if $FIELD_MODEL->get('uitype') eq "20" or $FIELD_MODEL->get('uitype') eq "19"}
					{if $COUNTER eq '1'}
						<td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td></tr><tr>
						{assign var=COUNTER value=0}
					{/if}
				{/if}
				 {if $COUNTER eq 2}
					 </tr><tr>
					{assign var=COUNTER value=1}
				{else}
					{assign var=COUNTER value=$COUNTER+1}
				 {/if}
				 <td class="fieldLabel {$WIDTHTYPE}" id="{$MODULE}_detailView_fieldLabel_{$FIELD_MODEL->getName()}">
					 <label class="muted pull-right marginRight10px">
						 {vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}
						 {if ($FIELD_MODEL->get('uitype') eq '72') && ($FIELD_MODEL->getName() eq 'unit_price')}
							{$BASE_CURRENCY_SYMBOL}
						{/if}
					 </label>
				 </td>
				 <td class="fieldValue {$WIDTHTYPE}" id="{$MODULE}_detailView_fieldValue_{$FIELD_MODEL->getName()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
                     {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'}
                             <textarea id="editView_fieldName_{$FIELD_NAME}" class="span12">{$FIELD_MODEL->get('fieldvalue')}</textarea>
                     {else}
                     <span class="value" data-field-type="{$FIELD_MODEL->getFieldDataType()}">
                        {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME RECORD=$RECORD}{if $FIELD_MODEL->name eq 'probability'}%{/if}
					 </span>
                         {if $FIELD_NAME eq 'inorout'}<input id="inorout" type="hidden" value="{$FIELD_MODEL->get('fieldvalue')}">{/if}
                         {assign var=FIELDAJAX value=array('email_flag')}
                         {if $FIELD_MODEL->isEditable() eq 'true' && ($FIELD_MODEL->getFieldDataType()!=Vtiger_Field_Model::REFERENCE_TYPE) && $FIELD_MODEL->isAjaxEditable() eq 'true' && in_array($FIELD_MODEL->getName(),$FIELDAJAX) && ($FIELD_MODEL->get('fieldvalue') eq 'nosender' OR $FIELD_MODEL->get('fieldvalue') eq 'notsender' OR $FIELD_MODEL->get('fieldvalue') eq '')}
                             <span class="hide edit">
                                {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME}
                                {if $FIELD_MODEL->getFieldDataType() eq 'multipicklist'}
                                    <input type="hidden" class="fieldname" value='{$FIELD_MODEL->get('name')}[]' data-prev-value='{$FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue'))}' />
                                {else}
                                    <input type="hidden" class="fieldname" value='{$FIELD_MODEL->get('name')}' data-prev-value='{Vtiger_Util_Helper::toSafeHTML($FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue')))}' />
                                {/if}
						    </span>
                         {/if}
					 {/if}
				 </td>
			 {/if}

		{if $FIELD_MODEL_LIST|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype" and $FIELD_MODEL->get('uitype') neq "69" and $FIELD_MODEL->get('uitype') neq "105"}
			<td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
		{/if}
		{/foreach}
		</tr>
		</tbody>
	</table>
	{/foreach}
    {if $ALLCOUNTS['counts']>0}
	<table class="table table-bordered equalSplit detailview-table">

		<tr><th><div class="pull-right"><div id="mainok" style="height:260px;width:250px;border:1px solid #ccc;"></div></div></div></th><th><div style="margin:0 auto;text-align:center;"><div id="mainread" style="height:260px;width:250px;border:1px solid #ccc;margin:0 auto;"></div></div></th><th><div class="pull-left"><div id="mainreadtimes" style="height:260px;width:250px;border:1px solid #ccc;"></div></div></th></tr>

	</table>

    <div class="table table-bordered blockContainer showInlineTable">
        <div style="height:37px;line-height:37px;background-color:#F1F1F1;color: #333;font-size: 14px;font-weight:bold;padding-left:5px;">收件人</div>
        <div class="msg"></div>
        <div  style="margin:0 auto;width:100%;overflow: hidden;" id="div_account_detail">

            <table id="tbl_ServiceAssignRule_Account_Detail" class="table listViewEntriesTable" style="width:100%;min-height=520px">
            {if $THISVALUE['inorout'] eq 'outer'}
            <thead>
                <tr><th style="white-space: nowrap;"><b>客户</b></th><th style="white-space: nowrap;"><b>行业</b></th><th style="white-space: nowrap;"><b>客户等级</b></th><th style="white-space: nowrap;"><b>区域</b></th><th style="white-space: nowrap;"><b>公司所在地</b></th><th style="white-space: nowrap;"><b>部门</b></th><th style="white-space: nowrap;"><b>负责人</b></th><th style="white-space: nowrap;"><b>邮箱</b></th><th style="white-space: nowrap;"><b>状态</b></th><th style="white-space: nowrap;"><b>发送时间</b></th><th style="white-space: nowrap;"><b>操作</b></th></tr>
            </thead>
            <tbody>

            {foreach item=EMAIL_L from=$EMAIL_LIST}
                <tr><td nowrap >{$EMAIL_L['accountname']}</td><td nowrap>{$EMAIL_L['industry']}</td><td nowrap>{$EMAIL_L['accountrank']}</td><td nowrap>{$EMAIL_L['businessarea']}</td><td nowrap>{$EMAIL_L['address']}</td><td nowrap>{$EMAIL_L['departmentname']}</td><td nowrap>{$EMAIL_L['smownername']}</td><td nowrap>{$EMAIL_L['email']}</td><td>{if $EMAIL_L['email_flag'] eq 'read'}<span>已打开,最后打开时间:{$EMAIL_L['readdatetime']},查看次数:{$EMAIL_L['readtimes']}</span>{elseif $EMAIL_L['email_flag'] eq 'send'}<span>已发送</span>{elseif $EMAIL_L['email_flag'] eq 'fail'}<span>发送失败,{$EMAIL_L['reason']}</span>{else}未发送{/if}</td><td nowrap>{$EMAIL_L['sendtime']}</td><td nowrap>{if $EMAIL_L['flag'] neq 'send'}{$EMAIL_L['nowsend']}{/if}</td></tr>
            {/foreach}
            </tbody>
            {else}
                <thead>
                <tr><th style="white-space: nowrap;"><b>姓名</b></th><th style="white-space: nowrap;"><b>部门</b></th><th style="white-space: nowrap;"><b>职位</b></th><th style="white-space: nowrap;"><b>邮箱</b></th><th style="white-space: nowrap;"><b>状态</b></th><th style="white-space: nowrap;"><b>发送时间</b></th><th style="white-space: nowrap;"><b>操作</b></th></tr>
                </thead>
                <tbody>
                {foreach item=EMAIL_L from=$EMAIL_LIST}
                    <tr><td nowrap>{$EMAIL_L['accountname']}</td><td nowrap>{$EMAIL_L['departmentname']}</td><td nowrap>{$EMAIL_L['roleid']}</td><td nowrap>{$EMAIL_L['email1']}</td><td>{$EMAIL_L['email_flag']}</td><td nowrap>{$EMAIL_L['sendtime']}</td><td nowrap>{if $EMAIL_L['flag'] neq 'send'}{$EMAIL_L['nowsend']}{/if}</td></tr>
                {/foreach}
                </tbody>
            {/if}
            </table>
        </div>

    </div>
    <script type="text/javascript" src="/libraries/media/jquery.dataTables.js"></script>



        <script>


        {literal}
            /*jQuery('.listViewEntriesTable').DataTable( {
                language: {"sProcessing":   "处理中...",	"sLengthMenu":   "显示 _MENU_ 项结果","sZeroRecords":  "没有匹配结果","sInfo":         "显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",	"sInfoEmpty":    "显示第 0 至 0 项结果，共 0 项","sInfoFiltered": "(由 _MAX_ 项结果过滤)","sInfoPostFix":  "","sSearch":"当前页快速检索:","sUrl":"","sEmptyTable":     "表中数据为空","sLoadingRecords": "载入中...",
                    "sInfoThousands":  ",",	"oPaginate": {"sFirst":"首页","sPrevious": "上页","sNext":"下页","sLast":"末页"},
                    "oAria": {"sSortAscending":  ": 以升序排列此列","sSortDescending": ": 以降序排列此列"}},
                "scrollY":"500px",
                "bAutoHeight":true,
                "bSort": false,
                "scrollCollapse":false,
                "scrollBody":"500px",
                'sScrollX':"disabled",
                aLengthMenu: [ 50, 100, 500, 1500 ],
                "processing": true,
                "serverSide": true,
                "ajax": "/index.php?module=Sendmailer&action=SelectAjax&mode=getAccountInfos&recordid="+$('#recordId').val()+'&inorout='+$('#inorout').val(),
                fnDrawCallback:function(){
                    jQuery('.msg').html('<font  color=red>数据加载完成</font>');
                }

            } );*/


            jQuery('.listViewEntriesTable').DataTable({
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
                scrollY: "580px",
                sScrollX: "disabled",
                aLengthMenu: [ 50, 100, 500, 1500 ],
                fnDrawCallback: function () {
                }
            });
            {/literal}

        </script>

    <script type="text/javascript" src="/libraries/echarts/echarts.js"></script>
    {literal}
        <script type="text/javascript">
            require.config({
                paths: {
                    echarts: '/libraries/echarts'
                }
            });
            require(
                    [
                        'echarts',
                        'echarts/chart/bar',
                        'echarts/chart/line',
                        'echarts/chart/gauge'
                    ],
                    function (ec) {
                        var myChart = ec.init(document.getElementById('mainok'));
                        myChart.setOption({
                            tooltip : {
                                formatter: "{a} <br/>{b} : {c}"
                            },
                            toolbox: {
                                show : false,
                                feature : {
                                    restore : {show: true}
                                }
                            },

                            series : [
                                {
                                    name:'成功数',
                                    type:'gauge',
                                    min:0,
                                    {/literal}
                                    max:{$ALLCOUNTS['counts']},
                                    {assign var=SNUM value=[11,10,9,8,7,6,5,4,3,2,1]}
                                    {assign var=FLAG value=1}
                                    splitNumber:{foreach item=VALUE from=$SNUM}{if $ALLCOUNTS['counts']%$VALUE eq 0} {assign var=FLAG value=2}{$VALUE}{break}{/if}{/foreach}{if $FLAG eq 1}1{/if},
                                    {literal}

                                    detail : {formatter:'{value}'},
                                    data:[{value:{/literal} {$ALLCOUNTS['send']}{literal}, name: '成功数'}],
                                    axisLine:{show:!0,lineStyle:{color:[[.2,"#ff4500"],[.8,"#48b"],[1,"#228b22"]],width:30}}
                                }
                            ]
                        });
                        var myChart1 = ec.init(document.getElementById('mainread'));
                        myChart1.setOption({
                            tooltip : {
                                formatter: "{a} <br/>{b} : {c}"
                            },
                            toolbox: {
                                show : false,
                                feature : {
                                    mark : {show: true},
                                    restore : {show: true},
                                    saveAsImage : {show: true}
                                }
                            },
                            series : [
                                {
                                    name:'阅读数',
                                    type:'gauge',
                                    min:0,
                                    {/literal}
                                    max:{$ALLCOUNTS['counts']},
                                    {assign var=SNUM value=[11,10,9,8,7,6,5,4,3,2,1]}
                                    {assign var=FLAG value=1}
                                    splitNumber:{foreach item=VALUE from=$SNUM}{if $ALLCOUNTS['counts']%$VALUE eq 0} {assign var=FLAG value=2}{$VALUE}{break}{/if}{/foreach}{if $FLAG eq 1}1{/if},
                                    {literal}

                                    detail : {formatter:'{value}'},
                                    data:[{value:{/literal} {$ALLCOUNTS['reader']}{literal}, name: '阅读数'}],
                                    axisLine:{show:!0,lineStyle:{color:[[.2,"#ff4500"],[.8,"#48b"],[1,"#228b22"]],width:30}}
                                }
                            ]
                        });
                        var myChart2 = ec.init(document.getElementById('mainreadtimes'));
                        myChart2.setOption({
                            tooltip2 : {
                                formatter: "{a} <br/>{b} : {c}"
                            },
                            toolbox: {
                                show : false,
                                feature : {
                                    mark : {show: true},
                                    restore : {show: true},
                                    saveAsImage : {show: true}
                                }
                            },
                            series : [
                                {
                                    name:'阅读次数',
                                    type:'gauge',
                                    min:0,
                                    {/literal}
                                    max:{if $ALLCOUNTS['readtimes'] eq 0}1{assign var=READTIMES value=1}{else}{assign var=READTIMES value=$ALLCOUNTS['readtimes']}{$ALLCOUNTS['readtimes']}{/if},
                                    {assign var=SNUM value=[11,10,9,8,7,6,5,4,3,2,1]}
                                    {assign var=FLAG value=1}
                                    splitNumber:{foreach item=VALUE from=$SNUM}{if $READTIMES%$VALUE eq 0} {assign var=FLAG value=2}{$VALUE}{break}{/if}{/foreach}{if $FLAG eq 1}1{/if},
                                    {literal}

                                    detail : {formatter:'{value}'},
                                    data:[{value:{/literal} {$ALLCOUNTS['readtimes']}{literal}, name: '阅读次数'}],
                                    axisLine:{show:!0,lineStyle:{color:[[.2,"#ff4500"],[.8,"#48b"],[1,"#228b22"]],width:30}}
                                }
                            ]
                        });
                    }
            );
        </script>
    {/literal}
    {/if}
{/strip}