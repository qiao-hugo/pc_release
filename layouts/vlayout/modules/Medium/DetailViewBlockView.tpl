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
    {assign var=HIDDENFIELD value=['adsname','channelposition','majoradvertising','recentmaintenancetime','billingmode','unitprice','cpcaverageprice','cpr','consumetaskcompletion','returnproportion','salesauthority','salesdirectorauthority','vpauthority','remarks']}
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
			{if !$FIELD_MODEL->isViewableInDetailView() OR in_array($FIELD_MODEL->get('label'),$HIDDENFIELD)}
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
					 <span class="value" data-field-type="{$FIELD_MODEL->getFieldDataType()}">
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

    {foreach key=row_no item=data from=$C_CADSNAME}
		<table class="table table-bordered equalSplit detailview-table ">
			<thead>
			<tr>
				<th class="blockHeader" colspan="4">
					<img class="cursorPointer alignMiddle blockToggle  hide" src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;">
					<img class="cursorPointer alignMiddle blockToggle" src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">
					&nbsp;&nbsp;广告名称&nbsp;&nbsp;<span class="label label-a_normal">{$row_no+1}</span>
				</th>
			</tr>
			</thead>
			<tbody>
			<tr>
				<td class="fieldLabel medium">
					<label class="muted pull-right marginRight10px">
                        {vtranslate('adsname','Medium')}
					</label>
				</td>
				<td class="fieldValue medium"  >
                    {$data['adsname']}
				</td>
				<td class="fieldLabel medium">
					<label class="muted pull-right marginRight10px">
                        {vtranslate('channelposition','Medium')}
					</label>
				</td>
				<td class="fieldValue medium"  >
                    {$data['channelposition']}
				</td>

			</tr>
			<tr>
				<td class="fieldLabel medium">
					<label class="muted pull-right marginRight10px">
                        {vtranslate('majoradvertising','Medium')}
					</label>
				</td>
				<td class="fieldValue medium"  >
                    {$data['majoradvertising']}
				</td>
				<td class="fieldLabel medium">
					<label class="muted pull-right marginRight10px">
                        {vtranslate('recentmaintenancetime','Medium')}
					</label>
				</td>
				<td class="fieldValue medium"  >
                    {$data['recentmaintenancetime']}
				</td>

			</tr>

			<tr>
				<td class="fieldLabel medium">
					<label class="muted pull-right marginRight10px">
                        {vtranslate('billingmode','Medium')}
					</label>
				</td>
				<td class="fieldValue medium"  >
                    {$data['billingmode']}
				</td>
				<td class="fieldLabel medium">
					<label class="muted pull-right marginRight10px">
                        {vtranslate('unitprice','Medium')}
					</label>
				</td>
				<td class="fieldValue medium"  >
                    {$data['unitprice']}
				</td>
			</tr>
			<tr>
				<td class="fieldLabel medium">
					<label class="muted pull-right marginRight10px">
                        {vtranslate('cpcaverageprice','Medium')}
					</label>
				</td>
				<td class="fieldValue medium"  >
                    {$data['cpcaverageprice']}
				</td>
				<td class="fieldLabel medium">
					<label class="muted pull-right marginRight10px">
                        {vtranslate('cpr','Medium')}
					</label>
				</td>
				<td class="fieldValue medium"  >
                    {$data['cpr']}
				</td>
			</tr>
			</tbody>
		</table>
		<br>
    {/foreach}
    {foreach key=row_no item=data from=$C_FIRMPOLICY}
		<table class="table table-bordered equalSplit detailview-table ">

			<thead>
			<tr>
				<th class="blockHeader" colspan="4">
					<img class="cursorPointer alignMiddle blockToggle  hide" src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;">
					<img class="cursorPointer alignMiddle blockToggle" src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">
					&nbsp;&nbsp;厂商政策&nbsp;&nbsp;<span class="label label-a_normal">{$row_no+1}</span>
				</th>
			</tr>
			</thead>
			<tbody>
			<tr>
				<td class="fieldLabel medium">
					<label class="muted pull-right marginRight10px">
                        {vtranslate('consumetaskcompletion','Medium')}
					</label>
				</td>
				<td class="fieldValue medium"  >
                    {$data['consumetaskcompletion']}
				</td>
				<td class="fieldLabel medium">
					<label class="muted pull-right marginRight10px">{vtranslate('returnproportion','Medium')}
					</label>
				</td>
				<td class="fieldValue medium"  >
                    {$data['returnproportion']}
				</td>
			</tr>

			<tr>
				<td class="fieldLabel medium">
					<label class="muted pull-right marginRight10px">
                        {vtranslate('salesauthority','Medium')}
					</label>
				</td>
				<td class="fieldValue medium"  >
                    {$data['salesauthority']}
				</td>
				<td class="fieldLabel medium">
					<label class="muted pull-right marginRight10px">
                        {vtranslate('salesdirectorauthority','Medium')}
					</label>
				</td>
				<td class="fieldValue medium"  >
                    {$data['salesdirectorauthority']}
				</td>
			</tr>

			<tr>
				<td class="fieldLabel medium">
					<label class="muted pull-right marginRight10px">
                        {vtranslate('vpauthority','Medium')}
					</label>
				</td>
				<td class="fieldValue medium"  >
                    {$data['vpauthority']}
				</td>
				<td class="medium"></td>
				<td class="medium"></td>
			</tr>
			<tr>
				<td class="fieldLabel medium">
					<label class="muted pull-right marginRight10px">
                        {vtranslate('remarks','Medium')}
					</label>
				</td>
				<td class="fieldValue medium"  colspan="3">
					<div class="row-fluid">
                        {$data['remarks']}
					</div>
				</td>
			</tr>
			</tbody>
		</table>
		<br>
    {/foreach}
{/strip}