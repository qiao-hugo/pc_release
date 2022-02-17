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
	{foreach key=BLOCK_LABEL_KEY item=FIELD_MODEL_LIST from=$RECORD_STRUCTURE}
	{assign var=BLOCK value=$BLOCK_LIST[$BLOCK_LABEL_KEY]}
	{if $BLOCK eq null or $FIELD_MODEL_LIST|@count lte 0}{continue}{/if}
	{assign var=IS_HIDDEN value=$BLOCK->isHidden()}
	{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
	<input type=hidden name="timeFormatOptions" data-value='{$DAY_STARTS}' />
	<input type=hidden name="servicecontractsid" value='{$RECORD->get("servicecontractsid")}' />
	<table class="table table-bordered equalSplit detailview-table {$BLOCK_LABEL_KEY}{if (($WORKFLOWS['iscontract'] eq 0) && ($BLOCK_LABEL_KEY eq 'LBL_ADV')) || (($BLOCK_LABEL_KEY eq 'LBL_CUSTOM_INFORMATION') && ($WORKFLOWS['iscontract'] eq 1))} hide{/if}">
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
			 
				{if $FIELD_MODEL->get('uitype') eq "21" or $FIELD_MODEL->get('uitype') eq "19"}
					 <td class="fieldLabel {$WIDTHTYPE}"  colspan="4" align="centor" id="{$MODULE}_detailView_fieldLabel_{$FIELD_MODEL->getName()}">
					 <label class="muted" style="text-align:center">
						 {vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}
					 </label></td></tr><tr>
					<td class="fieldValue {$WIDTHTYPE}" id="{$MODULE}_detailView_fieldValue_{$FIELD_MODEL->getName()}" colspan="4">
					 <span class="value" data-field-type="{$FIELD_MODEL->getFieldDataType()}">
                        {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME RECORD=$RECORD}
					 </span>
				 	</td></tr><tr>
					{assign var=COUNTER value=0}{continue}
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
					 </label>
				 </td>
				 <td class="fieldValue {$WIDTHTYPE}" id="{$MODULE}_detailView_fieldValue_{$FIELD_MODEL->getName()}">
					 <span class="value" data-field-type="{$FIELD_MODEL->getFieldDataType()}">
                        {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME RECORD=$RECORD}
					 </span>
				 </td>
		{/foreach}
		</tr>
		</tbody>
	</table>
	<br>

	<!--工单回款明细追加 gaocl add 2018/05/14 -->
	{if !empty($SALESORDER_RAYMENT_DATA) && $BLOCK_LABEL_KEY eq 'LBL_ADV'}
        {if $ISRAYMENT_FLAG neq '1' && $IS_RAYMENT_EDIT}
			<table class="table table-bordered blockContainer salesorderrayment_title_tab detailview-table" data-num="{$KEYINDEX + 1}" style="margin-bottom: 1px;">
				<thead>
				<tr>
					<th class="blockHeader" colspan="10">
						&nbsp;&nbsp;工单关联回款信息(提单人操作)
							{*<b class="pull-right" style="margin-right:10px;"><button class="btn btn-small submit_salesorderrayment btn-info" type="button" >匹配回款</button></b>*}
					</th>
				</tr>
				</thead>
			</table>
        {/if}
        {foreach key=KEYINDEX item=D from=$SALESORDER_RAYMENT_DATA}
			{assign var="ISRAYMENT_FLAG" value=$D['israyment']}
			{assign var="IS_RAYMENT_EDIT" value=$D['is_rayment_edit']}
			{assign var="IS_RAYMENT_SAVE" value=$D['is_rayment_save']}
			<table class="table table-bordered blockContainer salesorderrayment_tab detailview-table" data-num="{$KEYINDEX + 1}" style="margin-bottom: 5px;">
				<thead>
				<tr>
					<th class="blockHeader" colspan="10">
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;关联回款信息(提单人录入)[{$KEYINDEX + 1}]
						{if $ISRAYMENT_FLAG neq '1'  && $IS_RAYMENT_EDIT}
							{if $IS_RAYMENT_SAVE}
							<b class="pull-right"><button class="btn btn-small save_salesorderrayment addButton" type="button" data-id="{$D['receivedpaymentsid']}">关联回款</button></b>
							{*<b class="pull-right" style="margin-right:10px;"><button class="btn btn-small submit_salesorderrayment btn-info" type="button"  data-id="{$D['receivedpaymentsid']}">保存</button></b>*}
							{else}
								<b class="pull-right"><button class="btn btn-small deleted_salesorderrayment" type="button" data-id="{$D['receivedpaymentsid']}"><i class="icon-trash" title="移除关联回款信息"></i></button></b>
							{/if}
							<input type=hidden class="cls_receivedpaymentsid" value="{$D['receivedpaymentsid']}" />
						{/if}
					</th>
				</tr>
				</thead>
				<tbody>
				<tr>
					<td style="width: 25%"><label class="muted">回款信息</label></td>
					<td style="width: 10%"><label class="muted"><span class="redColor">*</span> 入账金额(￥)</label></td>
					<td style="width: 10%"><label class="muted"><span class="redColor">*</span> 入账日期</label></td>
					<td style="width: 10%"><label class="muted"><span class="redColor">*</span> 可使用金额(￥){if $ISRAYMENT_FLAG neq '1'  && $IS_RAYMENT_EDIT}<span class="rpaymentid" data-receivedpaymentsid="{$D['receivedpaymentsid']}" title="点击查看明细"><span class="icon-question-sign"></span></span>{/if}</label></td>
					<!--<td style="width: 10%"><label class="muted"><span class="redColor">*</span> 已使用工单金额(￥)</label></td> -->
					<!--<td style="width: 10%"><label class="muted"><span class="redColor">*</span> 人力成本(￥)</label></td>-->
                    <td style="width: 10%"><label class="muted"><span class="redColor">*</span>工单使用金额</label></td>
                    <td style="width: 15%"><label class="muted"><span class="redColor"></span> 备注</label></td>
				</tr>
				<tr>
					<td>
						<div class="row-fluid"><span class="span10">
								{if $ISRAYMENT_FLAG neq '1'  && $IS_RAYMENT_EDIT}
									<select class="chzn-select t_tab_salesorderrayment_id" name="paytitle{$D['receivedpaymentsid']}" data-id="{$D['receivedpaymentsid']}" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]">
										<option value="{$D['paytitle']}">{$D['owncompany']}{$D['paytitle']}</option>
									</select>
								{else}
                                    {$D['owncompany']}{$D['paytitle']}
								{/if}
							</span>
						</div>
					</td>
					<td>
						<div class="row-fluid">
							<span class="span10">
								{if $ISRAYMENT_FLAG neq '1' && $IS_RAYMENT_EDIT}
									<input type="number" style="width: 80px;min-width: 60px" class="input-large total" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="total{$D['receivedpaymentsid']}" data-id="{$D['receivedpaymentsid']}" readonly value="{$D['unit_price']}">
                                {else}
                                    {$D['unit_price']}
                                {/if}
							</span>
						</div>
					</td>
					<td>
						<div class="row-fluid">
							<span class="span10">
								{if $ISRAYMENT_FLAG neq '1' && $IS_RAYMENT_EDIT}
									<input type="text" style="width: 80px;min-width: 60px" class="input-large total" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="arrivaldate{$D['receivedpaymentsid']}" data-id="{$D['receivedpaymentsid']}" readonly value="{$D['reality_date']}">
                                {else}
                                    {$D['reality_date']}
                                {/if}
							</span>
						</div>
					</td>
					<td>
						<div class="row-fluid">
							<span class="span10">
								{if $ISRAYMENT_FLAG neq '1' && $IS_RAYMENT_EDIT}
									<input type="number" style="width: 80px;min-width: 60px" class="input-large total" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="rechargeableamount{$D['receivedpaymentsid']}" data-id="{$D['receivedpaymentsid']}" readonly value="{$D['rechargeableamount']}">
									<input type="hidden" style="width: 80px;min-width: 60px" class="input-large total" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="occupationcost{$D['receivedpaymentsid']}" data-id="{$D['receivedpaymentsid']}" readonly value="{$D['occupationcost']}">
								{else}
                                    {$D['rechargeableamount']}
                                {/if}
							</span>
						</div>
					</td>
					<!--<td>
						<div class="row-fluid">
							<span class="span10">
								{if $ISRAYMENT_FLAG neq '1' && $IS_RAYMENT_EDIT}
									<input type="number" style="width: 80px;min-width: 60px" class="input-large total" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="occupationcost{$D['receivedpaymentsid']}" data-id="{$D['receivedpaymentsid']}" readonly value="{$D['occupationcost']}">
                                {else}
                                    {$D['occupationcost']}
                                {/if}
							</span>
						</div>
					</td>-->

					<!--<td>
						<div class="row-fluid">
							<span class="span10">
								{if $ISRAYMENT_FLAG neq '1' && $IS_RAYMENT_EDIT}
									<input type="number" style="width: 80px;min-width: 60px" class="input-large total" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="laborcost{$D['receivedpaymentsid']}" data-id="{$D['receivedpaymentsid']}"  value="{$D['laborcost']}">
								{else}
                                    {$D['laborcost']}
                                {/if}
							</span>
						</div>
					</td>-->

                    <td>
                        <div class="row-fluid">
							<span class="span10">
								{if $ISRAYMENT_FLAG neq '1' && $IS_RAYMENT_EDIT}
                                    <input type="number" style="width: 80px;min-width: 60px" class="input-large total" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="purchasecost{$D['receivedpaymentsid']}" data-id="{$D['receivedpaymentsid']}"  value="{$D['purchasecost']}">
                                {else}
                                    {$D['purchasecost']}
                                {/if}
							</span>
                        </div>
                    </td>
					<td>
						<div class="row-fluid">
							<span class="span10">
								{if $ISRAYMENT_FLAG neq '1' && $IS_RAYMENT_EDIT}
									<textarea class="span11" data-id="{$D['receivedpaymentsid']}" name="rremarks{$D['receivedpaymentsid']}">{$D['rremarks']}</textarea>
                                {else}
                                    {$D['rremarks']}
                                {/if}
							</span>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
        {/foreach}
	{/if}
	{/foreach}
{/strip}