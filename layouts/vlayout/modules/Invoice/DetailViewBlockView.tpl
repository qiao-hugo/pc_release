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
    {assign var=IS_TAXT_TYPE value=$RECORD_STRUCTURE['LBL_INVOICE_INFORMATION']['taxtype']->fieldvalue}
    {assign var=NEGATIVE value=$RECORD_STRUCTURE['LBL_NEGATIVE_INFORMATION']['invoicecodenegative']->fieldvalue}
	{foreach key=BLOCK_LABEL_KEY item=FIELD_MODEL_LIST from=$RECORD_STRUCTURE}
	{assign var=BLOCK value=$BLOCK_LIST[$BLOCK_LABEL_KEY]}
	{if $BLOCK eq null or $FIELD_MODEL_LIST|@count lte 0}{continue}{/if}
    {if $BLOCK_LABEL_KEY eq 'LBL_NEGATIVE_INFORMATION' && $NEGATIVE eq ''}{continue}{/if}
	{assign var=IS_HIDDEN value=$BLOCK->isHidden()}
	{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
	<input type=hidden name="timeFormatOptions" data-value='{$DAY_STARTS}' />
 {if $BLOCK_LABEL_KEY neq 'LBL_TERMS_INFORMATION'}
	<table class="table table-bordered equalSplit detailview-table {if $IS_TAXT_TYPE eq 'generalinvoice' && $BLOCK_LABEL_KEY eq 'LBL_INVOICE_INFORMATIONA'}hide{/if}">
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
							({$BASE_CURRENCY_SYMBOL})
						{/if}
					 </label>
				 </td>
				 <td class="fieldValue {$WIDTHTYPE}" id="{$MODULE}_detailView_fieldValue_{$FIELD_MODEL->getName()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
					 <span class="value" data-field-type="{$FIELD_MODEL->getFieldDataType()}">
                        {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME RECORD=$RECORD}
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
	<br>
	{/if}
	{/foreach}
    <div id="invoicelist">
	{if !empty($MOREINVOICES)}
        {literal}
            <script>
                var extendinvoice='<table class="table table-bordered blockContainer Duplicates showInlineTable  detailview-table" data-num="yesreplace"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 发票代码</label><input type="hidden" name="erecordid" {/literal}value="{$RECORD->getId()}"{literal}><input type="hidden" name="invoiceextendid" value=""></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input  type="text" class="validate[required]" name="negativeinvoicecodeextend" data-id="yesreplace" value=""></span></div></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 发票号码</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="text" class="input-large" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="negativeinvoice_noextend" data-id="yesreplace" value=""></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 实际开票抬头</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="text" class="input-large " data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="negativebusinessnamesextend" data-id="yesreplace" value=""></span></div></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 开票人</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 开票日期</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><div class="input-append row-fluid"><div class="span10 row-fluid date form_datetime"><input type="text" class="span9 billingtimerextends dateField" name="negativebillingtimerextend" data-id="yesreplace" readonly="" value="{/literal}{date('Y-m-d')}{literal}" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"><span class="add-on"><i class="icon-calendar"></i></span></div></div></span></div></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">商品名称</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="text" class="input-large " data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="negativecommoditynameextend" data-id="yesreplace" value=""></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 金额</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><div class="input-prepend"><span class="add-on">¥</span><input type="text" class="input-medium" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="negativeamountofmoneyextend" data-id="yesreplace" value="" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2"></div></span></div></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span> 税率</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><select class="chzn-select" name="negativetaxrateextend" data-id="yesreplace" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"><option value="6%" >6%</option><option value="17%" selected="selected">17%</option></select></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 税额</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><div class="input-prepend"><span class="add-on">¥</span><input type="text" class="input-medium" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="negativetaxextend" data-id="yesreplace" value="" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2"></div></span></div></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 价税合计</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><div class="input-prepend" id="closedyesreplace"><span class="add-on">¥</span><input type="text" class="input-medium validate[required]" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="negativetotalandtaxextend" value="" data-id="yesreplace"></div></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">备注</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><textarea class="span11 " name="negativeremarkextend" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"></textarea></span></div></td></tr></tbody></table>';
            </script>
        {/literal}
        {foreach key=KEYINDEX item=MORE_FIELDS from=$MOREINVOICES}
        <div style="position:relative;">
        {if $MORE_FIELDS['processstatus'] eq 2 && $MORE_FIELDS['invoicestatus'] eq 'redinvoice'}
        <div style=" position:absolute;top:30%;right:50%;border:1px solid red;width:60px;text-align:center;color:red;border-radius:5px;font-size:24px;
            transform: rotate(40deg);
            -o-transform: rotate(40deg);
            -webkit-transform: rotate(40deg);
            -moz-transform: rotate(40deg);
            filter:progid:DXImageTransform.Microsoft.BasicImage(Rotation=2);">红冲</div>
        {/if}
        {if $MORE_FIELDS['processstatus'] eq 1}

            <div style=" position:absolute;top:30%;right:55%;border:1px solid red;width:60px;text-align:center;color:red;border-radius:5px;font-size:24px;
                transform: rotate(40deg);
                -o-transform: rotate(40deg);
                -ms-transform:rotate(40deg);
                -webkit-transform: rotate(40deg);
                -moz-transform: rotate(40deg);
                filter:progid:DXImageTransform.Microsoft.BasicImage(Rotation=2);">红冲</div>
            <div style=" position:absolute;top:30%;right:50%;border:1px solid red;width:60px;text-align:center;color:red;border-radius:5px;font-size:24px;">需要处理</div>
            <div style=" position:absolute;top:30%;right:45%;border:1px solid #666666;width:60px;text-align:center;column-rule: #666666;;border-radius:5px;font-size:24px;
                transform: rotate(-40deg);
                -ms-transform:rotate(-40deg);
                -o-transform: rotate(-40deg);
                -webkit-transform: rotate(-40deg);
                -moz-transform: rotate(-40deg);
                filter:progid:DXImageTransform.Microsoft.BasicImage(Rotation=4);">作废</div>
            {/if}
        {if $MORE_FIELDS['processstatus'] eq 2 && $MORE_FIELDS['invoicestatus'] eq 'tovoid'}
            <div style=" position:absolute;top:30%;right:50%;border:1px solid #666666;width:60px;text-align:center;column-rule: #666666;;border-radius:5px;font-size:24px;
                transform: rotate(-40deg);
                -o-transform: rotate(-40deg);
                -webkit-transform: rotate(-40deg);
                -moz-transform: rotate(-40deg);
                filter:progid:DXImageTransform.Microsoft.BasicImage(Rotation=4);">作废</div>
        {/if}
        <table class="table table-bordered blockContainer Duplicates showInlineTable  detailview-table"">
            <thead>
            <tr>
                <th class="blockHeader" colspan="4">&nbsp;&nbsp;财务数据[{$KEYINDEX+1}]
                    {if $IS_FINANCE}
                        {if $MORE_FIELDS['processstatus'] neq 2}
                            {if $IS_NEGATIVEEDIT}
                                <b class="pull-right" style="margin-right:10px;"><button class="btn btn-small addnegative" type="button"  data-id="{$MORE_FIELDS['invoiceextendid']}"><i class="icon-fire" title="点击添加红冲发票"></i></button></b>
                            {/if}
                            {if $IS_TOVOID}
                                    <b class="pull-right" style="margin-right:10px;"><button class="btn btn-small addcancel" type="button"  data-id="{$MORE_FIELDS['invoiceextendid']}"><i class="icon-remove-sign" title="点击作废发票"></i></button></b>
                            {/if}
                        {/if}
                        {if $MORE_FIELDS['processstatus'] eq 1 && ($IS_NEGATIVEEDIT || $IS_TOVOID)}
                            <b class="pull-right" style="margin-right:10px;"><button class="btn btn-small addcancelflag" type="button"  data-id="{$MORE_FIELDS['invoiceextendid']}"><i class="icon-tags" title="点击取消标记"></i></button></b>
                        {/if}
                    {/if}
                </th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">  发票代码</label>
                </td>
                <td class="fieldValue medium">
                    {$MORE_FIELDS['invoicecodeextend']}
                    <input type="hidden" id="invoicecodeextend{$MORE_FIELDS['invoiceextendid']}" data-id="{$MORE_FIELDS['invoiceextendid']}" value="{$MORE_FIELDS['invoicecodeextend']}">
                </td>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px"> 发票号码</label>
                </td>
                <td class="fieldValue medium">
                    {$MORE_FIELDS['invoice_noextend']}
                    <input type="hidden" id="invoice_noextend{$MORE_FIELDS['invoiceextendid']}" data-id="{$MORE_FIELDS['invoiceextendid']}" value="{$MORE_FIELDS['invoice_noextend']}">
                </td>
            </tr>
            <tr>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px"> 实际开票抬头</label>
                </td>
                <td class="fieldValue medium">
                    {$MORE_FIELDS['businessnamesextend']}
                    <input type="hidden" id="businessnamesextend{$MORE_FIELDS['invoiceextendid']}" data-id="{$MORE_FIELDS['invoiceextendid']}" value="{$MORE_FIELDS['businessnamesextend']}">
                </td>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">  开票人</label>
                </td>
                <td class="fieldValue medium">
                    {foreach key=OWNER_ID item=OWNER_NAME from=$ACCESSIBLE_USERS}
                        {if $MORE_FIELDS['drawerextend'] eq $OWNER_ID} {$OWNER_NAME} <input type="hidden" id="drawerextend{$MORE_FIELDS['invoiceextendid']}" data-id="{$MORE_FIELDS['invoiceextendid']}" value="{$OWNER_ID}">{/if}
                {/foreach}
                </td>
            </tr>
            <tr>
                <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">  开票日期</label></td>
                <td class="fieldValue medium">
                    {$MORE_FIELDS['billingtimeextend']}
                    <input type="hidden" id="billingtimeextend{$MORE_FIELDS['invoiceextendid']}" data-id="{$MORE_FIELDS['invoiceextendid']}" value="{$MORE_FIELDS['billingtimeextend']}">
                </td>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">商品名称</label>
                </td>
                <td class="fieldValue medium">
                    {$MORE_FIELDS['commoditynameextend']}
                    <input type="hidden" id="commoditynameextend{$MORE_FIELDS['invoiceextendid']}" data-id="{$MORE_FIELDS['invoiceextendid']}" value="{$MORE_FIELDS['commoditynameextend']}">
                </td>
            </tr>
            <tr>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px"> 金额</label>
                </td>
                <td class="fieldValue medium">
                    {$MORE_FIELDS['amountofmoneyextend']}
                    <input type="hidden" id="amountofmoneyextend{$MORE_FIELDS['invoiceextendid']}" data-id="{$MORE_FIELDS['invoiceextendid']}" value="{$MORE_FIELDS['amountofmoneyextend']}">
                </td>
                <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">  税率</label></td>
                <td class="fieldValue medium">
                    {$MORE_FIELDS['taxrateextend']}
                    <input type="hidden" id="taxrateextend{$MORE_FIELDS['invoiceextendid']}" data-id="{$MORE_FIELDS['invoiceextendid']}" value="{$MORE_FIELDS['taxrateextend']}">
                </td>
            </tr>
            <tr>
                <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">  税额</label></td>
                <td class="fieldValue medium">
                    {$MORE_FIELDS['taxextend']}
                    <input type="hidden" id="taxextend{$MORE_FIELDS['invoiceextendid']}" data-id="{$MORE_FIELDS['invoiceextendid']}" value="{$MORE_FIELDS['taxextend']}">
                </td>
                <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">  价税合计</label></td>
                <td class="fieldValue medium">
                    {$MORE_FIELDS['totalandtaxextend']}
                    <input type="hidden" id="totalandtaxextend{$MORE_FIELDS['invoiceextendid']}" data-id="{$MORE_FIELDS['invoiceextendid']}" value="{$MORE_FIELDS['totalandtaxextend']}">
                </td>
            </tr>
            <tr>
                <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">备注</label></td>
                <td class="fieldValue medium" colspan="3">
                    {$MORE_FIELDS['remarkextend']}
                    <input type="hidden" id="remarkextend{$MORE_FIELDS['invoiceextendid']}" data-id="{$MORE_FIELDS['invoiceextendid']}" value="{$MORE_FIELDS['remarkextend']}">
                </td>
            </tr>
            {if $MORE_FIELDS['processstatus'] eq 2 && $MORE_FIELDS['invoicestatus'] eq 'redinvoice'}
            <tr>
                <th class="blockHeader" colspan="4">&nbsp;&nbsp;<font color="red"> 红冲数据</font>
                </th>
            </tr>
            <tr>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">  发票代码</label>
                </td>
                <td class="fieldValue medium">
                    {$MORE_FIELDS['negativeinvoicecodeextend']}
                </td>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px"> 发票号码</label>
                </td>
                <td class="fieldValue medium">
                    {$MORE_FIELDS['negativeinvoice_noextend']}
                </td>
            </tr>
            <tr>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px"> 实际开票抬头</label>
                </td>
                <td class="fieldValue medium">
                    {$MORE_FIELDS['businessnamesextend']}
                </td>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">  开票人</label>
                </td>
                <td class="fieldValue medium">
                    {foreach key=OWNER_ID item=OWNER_NAME from=$ACCESSIBLE_USERS}
                        {if $MORE_FIELDS['negativedrawerextend'] eq $OWNER_ID} {$OWNER_NAME}{/if}
                    {/foreach}
                </td>
            </tr>
            <tr>
                <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">  开票日期</label></td>
                <td class="fieldValue medium">
                    {$MORE_FIELDS['negativebillingtimerextend']}
                </td>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">商品名称</label>
                </td>
                <td class="fieldValue medium">
                    {$MORE_FIELDS['negativecommoditynameextend']}
                </td>
            </tr>
            <tr>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px"> 金额</label>
                </td>
                <td class="fieldValue medium">
                    {$MORE_FIELDS['negativeamountofmoneyextend']}
                </td>
                <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">  税率</label></td>
                <td class="fieldValue medium">
                    {$MORE_FIELDS['negativetaxrateextend']}
                </td>
            </tr>
            <tr>
                <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">  税额</label></td>
                <td class="fieldValue medium">
                    {$MORE_FIELDS['negativetaxextend']}
                </td>
                <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">  价税合计</label></td>
                <td class="fieldValue medium">
                    {$MORE_FIELDS['negativetotalandtaxextend']}
                </td>
            </tr>
            <tr>
                <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">备注</label></td>
                <td class="fieldValue medium" colspan="3">
                    {$MORE_FIELDS['negativeremarkextend']}
                </td>
            </tr>
            {/if}
            </tbody>
        </table>
        </div>
    <br>
    {/foreach}
    {/if}
    </div>
    <table class="table table-bordered blockContainer showInlineTable  detailview-table invoicelistdisplay {if empty($INVOICE_LIST)}hide{/if}">
        <thead>
        <tr>
            <th class="blockHeader" colspan="11">
                <img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;">
                <img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">&nbsp;&nbsp;合同回款记录
            </th>
        </tr>
        </thead>
        <tbody  class="invoicelist">
        {if !empty($INVOICE_LIST)}
            <tr><td></td><td>所属合同</td><td>货币类型</td><td>本位币</td><td>汇率</td><td>回款金额</td><td>回款时间</td><td>创建人</td><td>汇款抬头</td><td>备注&说明</td><td>发票号码</td></tr>
            {foreach item=VALUE from=$INVOICE_LIST}
                <tr><td></td></td><td>{$VALUE['contract_no']}</td><td>{$VALUE['currencytype']}</td><td>{$VALUE['standardmoney']}</td><td>{$VALUE['exchangerate']}</td><td>{$VALUE['unit_price']}</td><td>{$VALUE['reality_date']}</td><td>{$VALUE['createid']}</td><td>{$VALUE['paytitle']}</td></td><td>{$VALUE['overdue']}</td><td>{$VALUE['invoice_no']}</td></tr>
            {/foreach}
        {/if}
        </tbody>
    </table>
    {*
	<div class="widgetContainer_product" data-url="module=ServiceContracts&view=Detail&mode=getProducts&amp;record={$RECORD->entity->column_fields['contractid']}" data-name="Workflows">
    <div class="widget_contents"></div>
	</div>
    *}
    <script src="/libraries/jSignature/jSignature.min.noconflict.js"></script>
{/strip}