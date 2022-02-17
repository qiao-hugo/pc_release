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
	<link href="/resources/photoswipe/default-skin/default-skin.css" rel="stylesheet">
	<link href="/resources/photoswipe/photoswipe.css" rel="stylesheet">
	<script src="/resources/photoswipe/photoswipe.js"></script>
	<script src="/resources/photoswipe/photoswipe-ui-default.js"></script>

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
					 <span class="value" data-field-type="{$FIELD_MODEL->getFieldDataType()}" style="-ms-word-wrap: break-word;word-wrap: break-word;-ms-word-break: break-all;word-break: break-all;">
                        {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME RECORD=$RECORD}{if $FIELD_MODEL->name eq 'probability'}%{/if}
					 </span>

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
	{if $followdata|@count neq 0}
	<table class="table table-bordered equalSplit detailview-table">
		<thead>
			<th class="blockHeader" colspan="2"><img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="144"><img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="144">&nbsp;&nbsp;跟进详细</th>
		</thead>
		<tr><td>跟进状态</td><td>跟进时间</td></tr>
		<tr>{foreach item=da from=$followdata}<td>{$da}</td>{/foreach}</tr>
	</table>
	{/if}

	{if $ACHIEVEMENTALLOTDATA neq ""}
	<table class="table table-bordered blockContainer showInlineTable" id="achievementallottable">
			<tr><thead><th class="blockHeader" colspan="6"><span>回款业绩分配</span></th></thead></tr>
				{foreach key=BLOCK_LABEL  item=BLOCK_FIELDS from=$ACHIEVEMENTALLOTDATA name="EditViewBlockLevelLoop"}
					<tr>
					<td><label class="muted pull-right marginRight10px">业绩所属人</label></td>
					<td> <input type='hidden' name="achievementallotdata[{$smarty.foreach.EditViewBlockLevelLoop.index}][]" value="{$BLOCK_FIELDS['receivedpaymentownid']}">{$BLOCK_FIELDS['receivedpaymentownid']}</td>
					<td><label class="muted pull-right marginRight10px">事业部</label></td>
					<td> <input type='hidden' name="achievementallotdata[{$smarty.foreach.EditViewBlockLevelLoop.index}][]" value="{$BLOCK_FIELDS['businessunit']}">{$BLOCK_FIELDS['businessunit']}</td>
					<td><label class="muted pull-right marginRight10px">所属公司</label></td>
					<td> <input type='hidden' name="achievementallotdata[{$smarty.foreach.EditViewBlockLevelLoop.index}][]" value="{$BLOCK_FIELDS['owncompanys']}">{$BLOCK_FIELDS['owncompanys']}</td>
					</tr>
				{/foreach}
	</table>
	{/if}



	{*陪同人签到*}
	{if $VISITSINGS|@count neq 0}
		{$num=0}
		{foreach item=FIELD_MODEL key=FIELD_NAME from=$VISITSINGS}
	<table class="table table-bordered equalSplit detailview-table">
		<thead>
		<tr>
				<th class="blockHeader" colspan="4">
					{$FIELD_MODEL.visitsigntype} {$FIELD_MODEL.last_name}签到{if $RECORD->get('modulestatus') eq 'c_complete' && $FIELD_MODEL['data'][0]['issign'] eq '否' && $FIELD_MODEL['data'][0]['userid'] eq $CURRENT_USER_MODEL->get('id') && $FIELD_MODEL['data'][0]['isappeal'] eq 0}<b class="pull-right">
						<span class="doappeal" type="button" data-id="{$FIELD_MODEL['data'][0]['visitsignid']}">
							已拜访未签到>>><span style="color:#0000FF;cursor:pointer">申诉</span>
						</span></b>{/if}
					{if $FIELD_MODEL['data'][0]['isappeal'] eq 1}
						<div style="position:relative;">
							<div style=" position:absolute;top:30%;right:50%;border:1px solid black;width:120px;line-height:1.3;text-align:center;color:red;border-radius:5px;font-size:24px;
            transform: rotate(40deg);
            -o-transform: rotate(40deg);
            -webkit-transform: rotate(40deg);
            -moz-transform: rotate(40deg);
            filter:progid:DXImageTransform.Microsoft.BasicImage(Rotation=2);">申诉中</div>
						</div>
					{elseif $FIELD_MODEL['data'][0]['isappeal'] eq 2}
						<div style="position:relative;">
							<div style=" position:absolute;top:30%;right:50%;border:1px solid red;width:120px;line-height:1.3;text-align:center;color:red;border-radius:5px;font-size:24px;
            transform: rotate(40deg);
            -o-transform: rotate(40deg);
            -webkit-transform: rotate(40deg);
            -moz-transform: rotate(40deg);
            filter:progid:DXImageTransform.Microsoft.BasicImage(Rotation=2);">申诉成功</div>
						</div>
					{elseif $FIELD_MODEL['data'][0]['isappeal'] eq 4}
						<div style="position:relative;">
							<div style=" position:absolute;top:30%;right:50%;border:1px solid #999;width:120px;line-height:1.3;text-align:center;color:#999;border-radius:5px;font-size:24px;
            transform: rotate(40deg);
            -o-transform: rotate(40deg);
            -webkit-transform: rotate(40deg);
            -moz-transform: rotate(40deg);
            filter:progid:DXImageTransform.Microsoft.BasicImage(Rotation=2);">驳回,未通过</div>
						</div>
					{/if}
		</tr>
		</thead>
		 <tbody>
		 {foreach item=vo key=key from=$FIELD_MODEL.data}
			 {if $vo.unusualsign==1}
				 <tr>
					 <td class="fieldLabel">
						 <label class='muted pull-right marginRight10px'>{if (count($FIELD_MODEL.data)-1)==$key }签退{else}签到{/if}地点</label>
					 </td>
					 <td class="fieldValue">
						<span class="value">
							{$vo.signaddress}
						</span>
					 </td>

					 <td class="fieldLabel">
						 <label class='muted pull-right marginRight10px'>{if (count($FIELD_MODEL.data)-1)==$key }签退{else}签到{/if}时间</label>
					 </td>
					 <td class="fieldValue">
						<span class="value">
							{$vo.signtime}
						</span>
					 </td>
				 </tr>
				 {if $vo.unusualremark}
				 <tr><td class="fieldLabel"></td><td  class="fieldLabel" colspan="2" style="word-break: break-all">{$vo.unusualremark}</td><td class="fieldLabel"></td></tr>
				 {/if}
				 <tr>
					 <td class="fieldLabel"></td>
					 <td colspan="3">
						 {foreach item=vo1  from=$vo.fileurl}
							<img src="{$vo1}" class="choose-file-img" data-size="{$num}" onload="setSize(this)" data-width="" data-height="" style="max-width: 70px;max-height: 70px;margin-left: 5px;"/>
						 	{$num = $num+1}
						 {/foreach}
					 </td>
				 </tr>
			 {else}
				 <tr>
					 <td class="fieldLabel">
						 <label class='muted pull-right marginRight10px'>{if (count($FIELD_MODEL.data)-1)==$key }签退{else}签到{/if}地点</label>
					 </td>
					 <td class="fieldValue">
						<span class="value">
							{$vo.signaddress}
						</span>
					 </td>

					 <td class="fieldLabel">
						 <label class='muted pull-right marginRight10px'>{if (count($FIELD_MODEL.data)-1)==$key }签退{else}签到{/if}时间</label>
					 </td>
					 <td class="fieldValue">
						<span class="value">
							{$vo.signtime}
						</span>
					 </td>
				 </tr>

			 {/if}
		 {/foreach}

		</tbody>
	</table>


			{if !empty($STRANGEVISITHISTORY) and isset($STRANGEVISITHISTORY[$FIELD_NAME])}
				<table class="table table-bordered equalSplit detailview-table">
					<thead>
					<tr>
						<th class="blockHeader" colspan="4">
							{$FIELD_MODEL.visitsigntype} {$FIELD_MODEL.last_name}陌拜签到
						</th>
					</tr>
					</thead>
					<tbody>
					{foreach item=vo2 key=key2 from=$STRANGEVISITHISTORY[$FIELD_NAME]}
						{if $vo2.unusualsign==1}
							<tr>
								<td class="fieldLabel"></td>
								<td colspan="3">
									<span style="font-size: 14px;font-weight: bold">{$vo2.accountname}</span>
								</td>
							</tr>


							<tr>
								<td class="fieldLabel">
									<label class='muted pull-right marginRight10px'>签到地点</label>
								</td>
								<td class="fieldValue">
						<span class="value">
							{$vo2.signaddress}
						</span>
								</td>

								<td class="fieldLabel">
									<label class='muted pull-right marginRight10px'>签到时间</label>
								</td>
								<td class="fieldValue">
						<span class="value">
							{$vo2.signtime}
						</span>
								</td>
							</tr>
							{if $vo2.unusualremark}
								<tr><td class="fieldLabel"></td><td  class="fieldLabel" colspan="2" style="word-break: break-all">{$vo2.unusualremark}</td><td class="fieldLabel"></td></tr>
							{/if}
							<tr>
								<td class="fieldLabel"></td>
								<td colspan="3">
									{foreach item=vo3  from=$vo2.fileurl}
										<img src="{$vo3}" class="choose-file-img" data-size="{$num}" onload="setSize(this)" data-width="" data-height="" style="max-width: 70px;max-height: 70px;margin-left: 5px;"/>
										{$num = $num+1}
									{/foreach}
								</td>
							</tr>
						{else}
							<tr>
								<td class="fieldLabel"></td>
								<td colspan="3">
									<span style="font-size: 14px;font-weight: bold">{$vo2.accountname}</span>
								</td>
							</tr>
							<tr>
								<td class="fieldLabel">
									<label class='muted pull-right marginRight10px'>签到地点</label>
								</td>
								<td class="fieldValue">
						<span class="value">
							{$vo2.signaddress}
						</span>
								</td>
								<td class="fieldLabel">
									<label class='muted pull-right marginRight10px'>签到时间</label>
								</td>
								<td class="fieldValue">
						<span class="value">
							{$vo2.signtime}
						</span>
								</td>
							</tr>

						{/if}
						<tr><td></td><td></td><td></td><td></td></tr>
					{/foreach}

					</tbody>
				</table>
			{/if}
	{/foreach}
	{/if}
	<!-- Root element of PhotoSwipe. Must have class pswp. -->
	<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="pswp__bg"></div>
		<div class="pswp__scroll-wrap">
			<div class="pswp__container">
				<div class="pswp__item"></div>
				<div class="pswp__item"></div>
				<div class="pswp__item"></div>
			</div>
			<div class="pswp__ui pswp__ui--hidden">
				<div class="pswp__top-bar">
					<div class="pswp__counter"></div>
					{*<div style="float: right;color: white;line-height: 44px;display: inline;padding:0 10px">下拉退出</div>*}
					 <span class="pswp__button pswp__button--close" title="Close (Esc)"></span>

					<span class="pswp__button pswp__button--fs" title="Toggle fullscreen"></span>

					<span class="pswp__button pswp__button--zoom" title="Zoom in/out"></span>
					<div class="pswp__preloader">
						<div class="pswp__preloader__icn">
							<div class="pswp__preloader__cut">
								<div class="pswp__preloader__donut"></div>
							</div>
						</div>
					</div>
				</div>
				<div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
					<div class="pswp__share-tooltip"></div>
				</div>
				                 <span class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)">
				                  </span>
				                <span class="pswp__button pswp__button--arrow--right" title="Next (arrow right)">
				                </span>
				<div class="pswp__caption">
					<div class="pswp__caption__center"></div>
				</div>
			</div>
		</div>
	</div>

	<div id="qqmap" style="height:400px;text-align:center;">
		<img id="mapPage"  width="100%" height="100%" frameborder="0" src="https://apis.map.qq.com/ws/staticmap/v2/?center={$LATLNG}&zoom=16&size=1360*300&maptype=roadmap&markers=size:large|color:red|label:k|{$LATLNG}&key=YQSBZ-DN7WP-NWGDE-L7OWN-4ZYU2-GCBJU&format=png&labels=border:1|size:20|color:0xff0000|bgcolor:white|anchor:3|offset:0_-18|拜访地址|{$LATLNG}">
	</div>
	<script>
        var pswpElement = document.querySelectorAll('.pswp')[0];
        var urls=new Array();
        function setSize(obj) {
            var realHeight = obj.height;
            var realWidth = obj.width;
            var width=2000;
            var height = parseInt(width*(realHeight/realWidth));
            console.log(height);
            $(obj).attr("data-height",height);
            $(obj).attr("data-width",width);
            funcReadImgInfo();
        }
        var start = 1;
        var end =1;

	</script>
{/strip}
