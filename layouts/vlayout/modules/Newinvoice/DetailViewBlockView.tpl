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

    <input type="hidden" name="account_id" value="{$ACCOUNTID}">
        <input type="hidden" name="contractid" value="{$t_contractid}">
        <input type="hidden" name="modulestatus" value="{$t_modulestatus}">
    <input type="hidden" name="record_id" value="{$smarty.get.record}">
    <input type="hidden" name="invoicecompany" value="{$t_invoicecompany}">
	<input type=hidden name="timeFormatOptions" value='{$DAY_STARTS}' />
        <input type=hidden name="d_invoicetype" value='{$INVOICETYPE}' />
        <input type=hidden name="is_void_flow" value='{$IS_VOID_FLOW}' />
        <input type=hidden id="billingsourcedata" value='{$billingsourcedata}' />
 {if $BLOCK_LABEL_KEY neq 'LBL_TERMS_INFORMATION'}
	<table class="table table-bordered equalSplit detailview-table ">
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


    {*{if $INVOICETYPE eq 'c_billing'}*}
    {literal}
    <script type="text/javascript">
        var newinvoicerayment_html1 = '<table class="table table-bordered blockContainer newinvoicerayment_tab detailview-table" data-num="yesreplace"><thead><tr><th class="blockHeader" colspan="4">&nbsp;&nbsp;关联回款信息(申请人录入)[]<b class="pull-right"><button class="btn btn-small savebuttonnewinvoicerayment" type="button" data-id="yesreplace"><i class="icon-ok-circle" title="保存关联回款信息"></i></button>&nbsp;<button class="btn btn-small deleted_newinvoicerayment" type="button" data-id="yesreplace"><i class="icon-trash" title="删除回款"></i></button></b></th></tr></thead><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span> 回款信息</label><input type="hidden" name="insertii[]" value="yesreplace"><input type="hidden" class="receivedpaymentsid_display" name="receivedpaymentsid_display[]" data-id="yesreplace" value=""> <input type="hidden" class="invoicecompany" data-id="yesreplace" value=""></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="hidden" class="receivedpaymentsid" name="receivedpaymentsid[]" value=""><input type="text" class="input-large receivedpaymentsid_display" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" readonly name="receivedpaymentsid_display[]" data-id="yesreplace" value=""></span></div></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span> 所属合同</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="hidden" class="servicecontractsid" name="servicecontractsid[]" value=""><input type="text" class="input-large servicecontractsid_display" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" readonly name="servicecontractsid_display[]" data-id="yesreplace" value=""></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span> 入账金额</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="text" class="input-large total" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="total[]" data-id="yesreplace" readonly value=""></span></div></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span> 入账日期</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="text" class="input-large arrivaldate" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" readonly name="arrivaldate[]" data-id="yesreplace" value=""></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span> 可开票金额</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="text" class="input-large allowinvoicetotal" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" readonly name="allowinvoicetotal[]" data-id="yesreplace" value=""></span></div></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span> 使用开票金额</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="text" class="input-large invoicetotal receivedpayments_invoicetotal" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="invoicetotal[]" data-id="yesreplace" value=""></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span> 开票内容</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="text" class="input-large invoicecontent" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" readonly name="invoicecontent[]" data-id="yesreplace" value=""></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor"></span> 备注</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><textarea class="span11 remarks" data-id="yesreplace" name="remarks[]"></textarea></span></div></td></tr></tbody></table>';
        var newinvoicerayment_html2 = '<table class="table table-bordered blockContainer newinvoicerayment_tab detailview-table" data-num="yesreplace"><thead><tr><th class="blockHeader" colspan="4">&nbsp;&nbsp;关联回款信息(申请人录入)[]<b class="pull-right"><button class="btn btn-small deleted_newinvoicerayment" type="button" data-id="yesreplace"><i class="icon-trash" title="点击解除关联回款信息"></i></button></b></th></tr></thead><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> 回款信息</label><input class="tab_newinvoicerayment_id" type="hidden" name="updateii[]" value="yesreplace"><input class="t_tab_newinvoicerayment_id" type="hidden" value="yesreplace"></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10 receivedpaymentsid_display">yesreplace</span></div></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> 所属合同</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10 servicecontractsid_display">yesreplace</span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> 入账金额</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10 total">yesreplace</span></div></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> 入账日期</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10 arrivaldate">yesreplace</span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> 可开发票金额</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10 allowinvoicetotal">yesreplace</span></div></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> 使用开票金额</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10 invoicetotal">yesreplace</span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> 开票内容</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10 invoicecontent">yesreplace</span></div></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> </label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> 备注</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10 remarks">yesreplace</span></div></td></tr></tbody></table>';
    </script>
    {/literal}
    {*{/if}*}
    {*关联订单信息*}
    <div class="linkedOrder_div">
        {foreach key=DONGCHALI_KEY item=DONGCHALI_VALUE from=$DONGCHALILIST}
            <table class="table table-bordered blockContainer linkedOrder_tab detailview-table" data-num="{$DONGCHALI_KEY+1}">
                <thead>
                <tr>
                    <th class="blockHeader" colspan="4">
                        &nbsp;&nbsp;订单信息{{$DONGCHALI_KEY+1}}
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="fieldLabel medium">
                        <label class="muted pull-right marginRight10px"><span class="redColor">*</span>订单编号</label>
                    </td>
                    <td class="fieldValue medium">
                        <div class="row-fluid">
                            <span class="span10"><input type="text" class="input-large" readonly value="{$DONGCHALI_VALUE['ordercode']}"></span>
                        </div>
                    </td>
                    <td class="fieldLabel medium">
                        <label class="muted pull-right marginRight10px"><span class="redColor">*</span>订单支付时间</label>
                    </td>
                    <td class="fieldValue medium">
                        <div class="row-fluid">
                            <span class="span10"><input type="text" class="input-large" readonly value="{$DONGCHALI_VALUE['paydate']}"></span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="fieldLabel medium">
                        <label class="muted pull-right marginRight10px"><span class="redColor">*</span>商品名称</label>
                    </td>
                    <td class="fieldValue medium">
                        <div class="row-fluid">
                            <span class="span10"><input type="text" class="input-large"  readonly value="{$DONGCHALI_VALUE['producttitle']}"></span>
                        </div>
                    </td>
                    <td class="fieldLabel medium">
                        <label class="muted pull-right marginRight10px"><span class="redColor">*</span>金额</label>
                    </td>
                    <td class="fieldValue medium">
                        <div class="row-fluid">
                            <span class="span10"><input type="text" class="input-large" id="orderMoney[{$DONGCHALI_KEY}]" readonly value="{$DONGCHALI_VALUE['money']}"></span>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        {/foreach}
    </div>
    <div class="newinvoicerayment_div">
        {if $INVOICETYPE eq 'c_billing' && $IS_ADD_NEWINVOICEAYMENT}
            <table class="table table-bordered blockContainer  detailview-table">
            <thead>
            <tr>
                <th class="blockHeader" colspan="4">
                    {*<img class="cursorPointer alignMiddle blockToggle hide " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;"><img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">&nbsp;&nbsp;关联回款信息<b class="pull-right"><button class="btn btn-small" type="button" id="add_newinvoicerayment"><i class="icon-plus" title="点击添加关联回款信息"></i></button></b>*}
                    <img class="cursorPointer alignMiddle blockToggle hide " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;"><img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">&nbsp;&nbsp;关联回款信息<b class="pull-right"></b>{if $INVOICETYPE neq 'c_normal' && $t_modulestatus eq 'c_complete' }<b class="pull-right">未带出合同回款信息？>>><a href="?module=Knowledge&view=Detail&record=1901" style="font-size:12px;color:blue">  前往查看可能原因</a>{/if}
                </th>
            </tr>
            </thead>
            </table>
        {/if}
{*        {if $INVOICETYPE neq 'c_normal' && $t_modulestatus eq 'c_complete' }*}
{*        <table class="table table-bordered blockContainer newinvoicerayment_tab detailview-table" ">*}
{*            <thead>*}
{*            <tr>*}
{*                <th class="blockHeader" colspan="4">*}
{*                    &nbsp;&nbsp;关联回款信息<b class="pull-right">未带出回款信息>>><a href="?module=Knowledge&view=Detail&record=1901" style="font-size:12px;color:blue">  前往查看可能原因</a></b>*}
{*                </th>*}
{*            </tr>*}
{*            </thead>*}
{*            </table>*}
{*        {/if}*}

        {* 关联回款信息 *}
	{if !empty($NEWINVOICEDATA) && $INVOICETYPE eq 'c_normal' }
    {foreach key=KEYINDEX item=D from=$NEWINVOICEDATA}
    <table class="table table-bordered blockContainer newinvoicerayment_tab detailview-table" data-num="{$KEYINDEX + 1}">
        <thead>
        <tr>
            <th class="blockHeader" colspan="4">
                &nbsp;&nbsp;关联回款信息(申请人录入)[{$KEYINDEX + 1}] {if $INVOICETYPE eq 'c_billing' && $UNLINKPAYMENT}<b class="pull-right"><button class="btn btn-small deleted_newinvoicerayment" type="button" data-id="{$D['newinvoiceraymentid']}"><i class="icon-trash" title="点击解除关联回款信息"></i></button></b>{/if}
            </th>
        </tr>
        </thead>
        <tbody>

        <tr>
            <td class="fieldLabel medium">
                <label class="muted pull-right marginRight10px"> 回款信息</label>
                <input type="hidden" name="updateii[]" value="{$D['newinvoiceraymentid']}">
                <input class="t_tab_newinvoicerayment_id" type="hidden" value="{$D['newinvoiceraymentid']}">
            </td>
            <td class="fieldValue medium">
                <div class="row-fluid">
                    <span class="span10">
                    {$D['paytitle']}
                    </span>
                </div>
            </td>
            <td class="fieldLabel medium">
                <label class="muted pull-right marginRight10px"> 所属合同</label>
            </td>
            <td class="fieldValue medium">
                <div class="row-fluid">
                    <span class="span10">{$D['contract_no']}</span>
                </div>
            </td>
        </tr>
        <tr>
            <td class="fieldLabel medium">
                <label class="muted pull-right marginRight10px"> 入账金额</label>
            </td>
            <td class="fieldValue medium">
                <div class="row-fluid">
                    <span class="span10">{$D['total']}</span>
                </div>
            </td>
            <td class="fieldLabel medium">
                <label class="muted pull-right marginRight10px"> 入账日期</label>
            </td>
            <td class="fieldValue medium">
                <div class="row-fluid">
                    <span class="span10">{$D['arrivaldate']}</span>
                </div>
            </td>
        </tr>
        <tr>
            <td class="fieldLabel medium">
                <label class="muted pull-right marginRight10px"> 可开发票金额</label>
            </td>
            <td class="fieldValue medium">
                <div class="row-fluid">
                    <span class="span10">{$D['allowinvoicetotal']}</span>
                </div>
            </td>
            <td class="fieldLabel medium">
                <label class="muted pull-right marginRight10px"> 使用开票金额</label>
            </td>
            <td class="fieldValue medium">
                <div class="row-fluid">
                    <span class="span10">{$D['invoicetotal']}</span>
                </div>
            </td>
        </tr>
        <tr>
            <td class="fieldLabel medium">
                <label class="muted pull-right marginRight10px"> 开票内容</label>
            </td>
            <td class="fieldValue medium">
                <div class="row-fluid">
                    <span class="span10">{$D['invoicecontent']}</span>
                </div>
            </td>
            <td class="fieldLabel medium">
                <label class="muted pull-right marginRight10px"> </label>
            </td>
            <td class="fieldValue medium">
                <div class="row-fluid">
                    <span class="span10"></span>
                </div>
            </td>
        </tr>
        <tr>
            <td class="fieldLabel medium">
                <label class="muted pull-right marginRight10px"> 备注</label>
            </td>
            <td class="fieldValue medium" colspan="3">
                <div class="row-fluid">
                    <span class="span10">{$D['remarks']}</span>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
    {/foreach}
    {/if}
    {* 关联回款信息 结束 *}
    </div>


    <div id="invoicelist">
	{if !empty($MOREINVOICES)}
        <script>
              var makeRedReceivedpayments =
              {if count($NEWINVOICEDATA) neq 0}
              '<div style="max-height:200px;overflow-y:auto;"><table class="table table-bordered blockContainer showInlineTable detailview-table negativeinvoiceextend" id="fallintotable"><thead><tr><th class="blockHeader" colspan="7"><span class="redColor">发票红冲</span></th></tr></thead><tbody><tr><td><b>所属合同</b></td><td><b>入账日期</b></td><td><b>入账金额</b></td><td><b>可开票金额</b></td><td><b>此次开票金额</b></td><td><b>剩余此次开票金额</b></td><td><b>作废金额</b></td></tr>            {foreach key=KEYINDEX item=D from=$NEWINVOICEDATA}<tr ><td>{$D["contract_no"]}<input type="hidden" name="servicecontractsid[{$KEYINDEX + 1}]" value={$D["servicecontractsid"]}><input type="hidden" name="contract_no[{$KEYINDEX + 1}]" value={$D["contract_no"]}><input type="hidden" name="receivedpaymentsid[{$KEYINDEX + 1}]" value={$D["receivedpaymentsid"]}></td><td>{$D["arrivaldate"]}</td><td>{$D["total"]}<input type="hidden" class="tovoid_t_total" value={$D["total"]} /><input type="hidden" name="total[{$KEYINDEX + 1}]" value={$D["total"]}></td><td>{$D["allowinvoicetotal"]}<input type="hidden" class="tovoid_tovoie_allowinvoicetotal" value={$D["allowinvoicetotal"]} /><input type="hidden" name="allowinvoicetotal[{$KEYINDEX + 1}]" value={$D["allowinvoicetotal"]}/></td><td>                {$D["invoicetotal"]}<input type="hidden" name="tovoidform[]" value="{$KEYINDEX + 1}"><input type="hidden" name="newinvoiceraymentid[{$KEYINDEX + 1}]" value={$D["newinvoiceraymentid"]}><input type="hidden" name="invoicetotal[{$KEYINDEX + 1}]" value={$D["invoicetotal"]}> <input type="hidden" class="neg_invoicetotal"  value={$D["invoicetotal"]}> </td><td>{$D["surpluinvoicetotal"]}<input type="hidden" class="t_surpluinvoicetotal" value={$D["surpluinvoicetotal"]}><td><input type="number" class="tovoid_tovoie_total" value={if count($NEWINVOICEDATA) eq 1 }reg_totalandtaxextend{else}0{/if} min="0.0" step="0.01" name="tovoie_total[{$KEYINDEX + 1}]" style="width: 100px; height:15px;"/></td></tr>            {/foreach}</tbody></table></div>';


            {else}
                '';
            {/if}
        </script>


        {literal}
            <script>
                var extendinvoice='<input type="hidden" name="module" value="Newinvoice"><input type="hidden" name="action" value="Tovoid"><input type="hidden" name="mode" value="addRedInvoice"> <table class="table table-bordered blockContainer Duplicates showInlineTable  detailview-table" data-num="yesreplace"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 发票代码</label><input type="hidden" name="erecordid" {/literal}value="{$RECORD->getId()}"{literal}><input type="hidden" name="invoiceextendid" value=""></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input  type="text" class="validate[required]" name="negativeinvoicecodeextend" data-id="yesreplace" value=""></span></div></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 发票号码</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="text" class="input-large" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="negativeinvoice_noextend" data-id="yesreplace" value=""></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 实际开票抬头</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="text" class="input-large " data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="negativebusinessnamesextend" data-id="yesreplace" value="" readonly="readonly"></span></div></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 开票人</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 开票日期</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><div class="input-append row-fluid"><div class="span10 row-fluid date form_datetime"><input type="text" class="span9 billingtimerextends dateField" name="negativebillingtimerextend" data-id="yesreplace" readonly="" value="{/literal}{date('Y-m-d')}{literal}" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"><span class="add-on"><i class="icon-calendar"></i></span></div></div></span></div></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">商品名称</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="text" class="input-large " readonly="readonly" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="negativecommoditynameextend" data-id="yesreplace" value=""></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 金额</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><div class="input-prepend"><span class="add-on">¥</span><input type="text" class="input-medium" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="negativeamountofmoneyextend" data-id="yesreplace" value="" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2"></div></span></div></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span> 税率</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><select readonly="readonly" class="chzn-select" name="negativetaxrateextend" data-id="yesreplace" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"><option value="6%" >6%</option><option value="17%" selected="selected">17%</option></select></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 税额</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><div class="input-prepend"><span class="add-on">¥</span><input type="text" class="input-medium" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="negativetaxextend" data-id="yesreplace" value="" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2"></div></span></div></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 价税合计</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><div class="input-prepend" id="closedyesreplace"><span class="add-on">¥</span><input type="hidden" class="t_invoice_totalandtaxextend" name="totalandtaxextend" value="reg_totalandtaxextend"> <input type="text" class="input-medium validate[required] tt_negativetotalandtaxextend" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="negativetotalandtaxextend" value="" data-id="yesreplace"><input type="hidden" class="tt_record"  name="record" value="reg_record"><input type="hidden" name="invoiceextendid" class="invoice_invoiceextendid" value="reg_invoiceextendid"></div></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">备注</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><textarea class="span11 " name="negativeremarkextend" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"></textarea></span></div></td></tr></tbody></table>';
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


            {if $IS_VOID_FLOW eq 2}
            <div style=" position:absolute;top:30%;right:55%;border:1px solid red;width:60px;text-align:center;color:red;border-radius:5px;font-size:24px;
                transform: rotate(40deg);
                -o-transform: rotate(40deg);
                -ms-transform:rotate(40deg);
                -webkit-transform: rotate(40deg);
                -moz-transform: rotate(40deg);
                filter:progid:DXImageTransform.Microsoft.BasicImage(Rotation=2);">红冲</div>
            {/if}
            <div style=" position:absolute;top:30%;right:50%;border:1px solid red;width:60px;text-align:center;color:red;border-radius:5px;font-size:24px;">需要处理</div>
            {if $IS_VOID_FLOW eq 1}
            <div style=" position:absolute;top:30%;right:45%;border:1px solid #666666;width:60px;text-align:center;column-rule: #666666;;border-radius:5px;font-size:24px;
                transform: rotate(-40deg);
                -ms-transform:rotate(-40deg);
                -o-transform: rotate(-40deg);
                -webkit-transform: rotate(-40deg);
                -moz-transform: rotate(-40deg);
                filter:progid:DXImageTransform.Microsoft.BasicImage(Rotation=4);">作废</div>
            {/if}
            {/if}
        {if $MORE_FIELDS['processstatus'] eq 2 && $MORE_FIELDS['invoicestatus'] eq 'tovoid'}
            <div style=" position:absolute;top:30%;right:50%;border:1px solid #666666;width:60px;text-align:center;column-rule: #666666;;border-radius:5px;font-size:24px;
                transform: rotate(-40deg);
                -o-transform: rotate(-40deg);
                -webkit-transform: rotate(-40deg);
                -moz-transform: rotate(-40deg);
                filter:progid:DXImageTransform.Microsoft.BasicImage(Rotation=4);">作废</div>
        {/if}
        <table class="table table-bordered blockContainer Duplicates showInlineTable  detailview-table" style="margin-bottom:0">
            <thead>
            <tr>
                <th class="blockHeader" colspan="4">&nbsp;&nbsp;财务数据[{$KEYINDEX+1}]
                    
                    {*{if $IS_FINANCE}*}
                        {if $MORE_FIELDS['invoicestatus'] neq 'tovoid' && $MORE_FIELDS['surplusnewnegativeinvoice'] gt 0}
{*                            {if $IS_NEGATIVEEDIT || $IS_ADMIN}*}
{*                                <b class="pull-right" style="margin-right:10px;"><button class="btn btn-small addnegative" type="button"  data-id="{$MORE_FIELDS['invoiceextendid']}"><i class="icon-fire" title="点击添加红冲发票"></i></button></b>*}
{*                            {/if}*}
                        {/if}
                        {if $MORE_FIELDS['processstatus'] neq 2}
                            

{*                            {if $IS_TOVOID || $IS_ADMIN}*}
{*                                <b class="pull-right" style="margin-right:10px;"><button class="btn btn-small tovoid_show_button" type="button"  data-id="{$MORE_FIELDS['invoiceextendid']}"><i class="icon-remove-sign" title="点击作废发票"></i></button></b>*}
{*                            {/if}*}

                        {/if}
                        {if $MORE_FIELDS['processstatus'] eq 1 && ($IS_NEGATIVEEDIT || $IS_TOVOID)}
                            <b class="pull-right" style="margin-right:10px;"><button class="btn btn-small addcancelflag" type="button"  data-id="{$MORE_FIELDS['invoiceextendid']}"><i class="icon-tags" title="点击取消标记"></i></button></b>
                        {/if}
                    {*{/if}*}
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
                    <input type="hidden" class="invoice_totalandtaxextend" id="totalandtaxextend{$MORE_FIELDS['invoiceextendid']}" data-id="{$MORE_FIELDS['invoiceextendid']}" value="{$MORE_FIELDS['totalandtaxextend']}">


                </td>
            </tr>
            <tr>
                <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">  剩余价税合计</label></td>
                <td class="fieldValue medium">
                    {$MORE_FIELDS['surplusnewnegativeinvoice']}
                    <input type="hidden" class="invoice_surplusnewnegativeinvoice" value="{$MORE_FIELDS['surplusnewnegativeinvoice']}">

                </td>
                <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">备注</label></td>
                <td class="fieldValue medium">
                    {$MORE_FIELDS['remarkextend']}
                    <input type="hidden" id="remarkextend{$MORE_FIELDS['invoiceextendid']}" data-id="{$MORE_FIELDS['invoiceextendid']}" value="{$MORE_FIELDS['remarkextend']}">
                </td>
            </tr>
            <tr>
                <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">红字信息表</label></td>
                <td class="fieldValue medium">
                    {include file=vtemplate_path('uitypes/ExtendFileUpload.tpl', $MODULE)}
                </td>
            <tr>

            {if $MORE_FIELDS['processstatus'] eq 2 && $MORE_FIELDS['invoicestatus'] eq 'redinvoice'}
            {foreach from=$MORE_FIELDS['newnegativeinvoice'] item=NEGATIVE}
            <tr>
                <th class="blockHeader" colspan="4">&nbsp;&nbsp;<font color="red"> 红冲数据</font>
                </th>
            </tr>
            <tr>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">  发票代码</label>
                </td>
                <td class="fieldValue medium">
                    {$NEGATIVE['negativeinvoicecodeextend']}
                </td>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px"> 发票号码</label>
                </td>
                <td class="fieldValue medium">
                    {$NEGATIVE['negativeinvoice_noextend']}
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
                        {if $NEGATIVE['negativedrawerextend'] eq $OWNER_ID} {$OWNER_NAME}{/if}
                    {/foreach}
                </td>
            </tr>
            <tr>
                <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">  开票日期</label></td>
                <td class="fieldValue medium">
                    {$NEGATIVE['negativebillingtimerextend']}
                </td>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">商品名称</label>
                </td>
                <td class="fieldValue medium">
                    {$NEGATIVE['negativecommoditynameextend']}
                </td>
            </tr>
            <tr>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px"> 金额</label>
                </td>
                <td class="fieldValue medium">
                    -{$NEGATIVE['negativeamountofmoneyextend']}
                </td>
                <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">  税率</label></td>
                <td class="fieldValue medium">
                    {$NEGATIVE['negativetaxrateextend']}
                </td>
            </tr>
            <tr>
                <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">  税额</label></td>
                <td class="fieldValue medium">
                    -{$NEGATIVE['negativetaxextend']}
                </td>
                <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">  价税合计</label></td>
                <td class="fieldValue medium">
                    -{$NEGATIVE['negativetotalandtaxextend']}
                </td>
            </tr>
            <tr>
                <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">备注</label></td>
                <td class="fieldValue medium" colspan="3">
                    {$NEGATIVE['negativeremarkextend']}
                </td>
            </tr>
            {/foreach}
            {/if}
            </tbody>
        </table>

        {assign var=NEWINVOICETOVOID_ITEM value=$NEWINVOICETOVOID[$MORE_FIELDS['invoiceextendid']]}
        {if $MORE_FIELDS['processstatus'] eq 2 && $MORE_FIELDS['invoicestatus'] eq 'redinvoice' && count($NEWINVOICETOVOID_ITEM) neq 0}
        <table  class="table table-bordered blockContainer  detailview-table">
            <thead>
            <tr>
                <th class="blockHeader" colspan="6" >
                    <span class="redColor">红冲作废数据</span>
                </th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><b>所属合同或订单</b></td>
                <td><b>入账金额</b></td>
                <td><b>可开票金额</b></td>
                <td><b>此次开票金额</b></td>
                <td><b>剩余此次开票金额</b></td>
                <td><b>作废金额</b></td>
            </tr>
            
            {foreach key=KEYINDEX item=TOVOID_ITEM from=$NEWINVOICETOVOID_ITEM}
            <tr>
                <td><b>{$TOVOID_ITEM['contract_no']}</b></td>
                <td><b>{$TOVOID_ITEM['total']}</b></td>
                <td><b>{$TOVOID_ITEM['allowinvoicetotal']}</b></td>
                <td><b>{$TOVOID_ITEM['invoicetotal']}</b></td>
                <td><b>{$TOVOID_ITEM['surpluinvoicetotal']}</b></td>
                <td><b>{$TOVOID_ITEM['tovoidtotal']}</b></td>
            </tr>
            {/foreach}
            </tbody>
        </table>
        {/if}

        {assign var=NEWINVOICETOVOID_ITEM value=$NEWINVOICETOVOID_DIRECT[$MORE_FIELDS['invoiceextendid']]}
        {assign var=NEWINVOICEORDERTOVOID_ITEM value=$NEWINVOICEORDERTOVOID_DIRECT[$MORE_FIELDS['invoiceextendid']]}
        {if $MORE_FIELDS['processstatus'] eq 2 && $MORE_FIELDS['invoicestatus'] eq 'tovoid' && (count($NEWINVOICETOVOID_ITEM) neq 0 || count($NEWINVOICEORDERTOVOID_ITEM) neq 0)}
         <table  class="table table-bordered blockContainer  detailview-table">
            <thead>
            <tr>
                <th class="blockHeader" colspan="6" >
                    <span class="redColor">作废数据</span>
                </th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><b>所属合同或订单</b></td>
                <td><b>入账金额</b></td>
                <td><b>可开票金额</b></td>
                <td><b>此次开票金额</b></td>
                <td><b>剩余此次开票金额</b></td>
                <td><b>作废金额</b></td>
            </tr>
            {if $billingsourcedata eq 'ordersource'}
                {foreach key=KEYINDEX item=D from=$NEWINVOICEORDERTOVOID_ITEM}
                    <tr>
                        <td><b>{$D['ordercode']}</b></td>
                        <td><b>{$D['total']}</b></td>
                        <td><b>{$D['allowinvoicetotal']}</b></td>
                        <td><b>{$D['invoicetotal']}</b></td>
                        <td><b>0.00</b></td>
                        <td><b>{$D['tovoidtotal']}</b></td>
                    </tr>
                {/foreach}

            {else}
                {foreach key=KEYINDEX item=TOVOID_ITEM from=$NEWINVOICETOVOID_ITEM}
                    <tr>
                        <td><b>{$TOVOID_ITEM['contract_no']}</b></td>
                        <td><b>{$TOVOID_ITEM['total']}</b></td>
                        <td><b>{$TOVOID_ITEM['allowinvoicetotal']}</b></td>
                        <td><b>{$TOVOID_ITEM['invoicetotal']}</b></td>
                        <td><b>{$TOVOID_ITEM['surpluinvoicetotal']}</b></td>
                        <td><b>{$TOVOID_ITEM['tovoidtotal']}</b></td>
                    </tr>
                {/foreach}
            {/if}

            </tbody>
        </table>
        {/if}

        <!--作废中审核流程展示当流程是作废或者删除,无实际意义-->
        {if $MORE_FIELDS['processstatus'] eq 1 && $MORE_FIELDS['invoicestatus'] eq 'normal' && in_array($IS_VOID_FLOW,array(1,2))}
            <table  class="table table-bordered blockContainer  detailview-table" id="flowProcess{$MORE_FIELDS['invoiceextendid']}" style="margin-bottom: 0px">
                <thead>
                <tr>
                    <th class="blockHeader" colspan="6" >
                        {if $IS_VOID_FLOW eq 2}
                            <span class="redColor">红冲中审核数据</span>
                        {else}
                            <span class="redColor">作废中审核数据</span>
                        {/if}
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><b>所属合同或订单</b></td>
                    <td><b>入账金额</b></td>
                    <td><b>可开票金额</b></td>
                    <td><b>此次开票金额</b></td>
                    <td><b>剩余此次开票金额</b></td>
                    <td><b>申请作废金额</b></td>
                </tr>
                {if $billingsourcedata eq 'ordersource'}
                    {foreach key=KEYINDEX item=D from=$DONGCHALILIST}
                        <tr>
                            <td><b>{$D['ordercode']}</b></td>
                            <td><b>{$D['money']}</b></td>
                            <td><b>{$D['invoicemoney']}</b></td>
                            <td><b>{$D['invoicemoney']}</b></td>
                            <td><b>{$D['remainingmoney']}</b></td>
                            <td><b>{$D['voidmoney']}</b></td>
                        </tr>
                    {/foreach}

                {else}
                    {foreach key=KEYINDEX item=D from=$NEWINVOICEDATA}
                        <tr>
                            <td><b>{$D['contract_no']}</b></td>
                            <td><b>{$D['total']}</b></td>
                            <td><b>{$D['allowinvoicetotal']}</b></td>
                            <td><b>{$D['invoicetotal']}</b></td>
                            <td><b>{$D['surpluinvoicetotal']}</b></td>
                            <td><b>{$D['voidorredtotal']}</b></td>
                        </tr>
                    {/foreach}

                {/if}
                </tbody>
            </table>
            {if $IS_VOID_FLOW eq 2}
            <table class="table table-bordered blockContainer Duplicates showInlineTable  detailview-table" id="redInvoice{$MORE_FIELDS['invoiceextendid']}">
                <thead><tr><th class="blockHeader" colspan="6"><span class="redColor">红冲中审核数据</span></th></tr></thead>
                <tbody>
                    <tr>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 发票代码</label><input type="hidden" name="invoiceextendid" value="{$MORE_FIELDS['invoiceextendid']}"></td>
                        <td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="text"  name="negativeinvoicecodeextend{$MORE_FIELDS['invoiceextendid']}" data-id="yesreplace" value=""></span></div></td>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 发票号码</label></td>
                        <td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="text" class="input-large"  name="negativeinvoice_noextend{$MORE_FIELDS['invoiceextendid']}" data-id="yesreplace" value=""></span></div></td>
                    </tr>
                    <tr>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 实际开票抬头</label></td>
                        <td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="text" class="input-large" name="negativebusinessnamesextend{$MORE_FIELDS['invoiceextendid']}" data-id="yesreplace" value="{$MORE_FIELDS['businessnamesextend']}" readonly="readonly"></span></div></td>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 开票人</label></td>
                        <td class="fieldValue medium">
                            <div class="row-fluid">
                                <span class="span10">
                                    {foreach key=OWNER_ID item=OWNER_NAME from=$ACCESSIBLE_USERS}
                                        {if $MORE_FIELDS['drawerextend'] eq $OWNER_ID} {$OWNER_NAME} <input type="hidden" name="drawerextend{$MORE_FIELDS['invoiceextendid']}" data-id="{$MORE_FIELDS['invoiceextendid']}" value="{$OWNER_ID}">{/if}
                                    {/foreach}
                                </span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 开票日期</label></td>
                        <td class="fieldValue medium"><div class="row-fluid"><span class="span10"><div class="input-append row-fluid"><div class="span10 row-fluid date form_datetime"><input type="text" class="span9"  name="negativebillingtimerextend{$MORE_FIELDS['invoiceextendid']}" data-id="yesreplace" readonly="" value="{date('Y-m-d')}"><span class="add-on"><i class="icon-calendar"></i></span></div></div></span></div></td>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">商品名称</label></td>
                        <td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="text" class="input-large " readonly="readonly"  name="negativecommoditynameextend{$MORE_FIELDS['invoiceextendid']}" data-id="yesreplace" value="{$MORE_FIELDS['commoditynameextend']}"></span></div></td>
                    </tr>
                    <tr>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 金额</label></td>
                        <td class="fieldValue medium"><div class="row-fluid"><span class="span10"><div class="input-prepend"><span class="add-on">¥</span><input data-extend="{$MORE_FIELDS['invoiceextendid']}" type="text" class="input-medium"  name="negativeamountofmoneyextend{$MORE_FIELDS['invoiceextendid']}" data-id="yesreplace" value=""  data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2"></div></span></div></td>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span> 税率</label></td>
                        <td class="fieldValue medium"><div class="row-fluid"><span class="span10"><select readonly="readonly" class="chzn-select" data-extend="{$MORE_FIELDS['invoiceextendid']}"  name="negativetaxrateextend{$MORE_FIELDS['invoiceextendid']}" data-id="yesreplace"><option value="6%">6%</option><option value="17%">17%</option></select></span></div></td>
                    </tr>
                    <tr>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 税额</label></td>
                        <td class="fieldValue medium"><div class="row-fluid"><span class="span10"><div class="input-prepend"><span class="add-on">¥</span><input type="text" class="input-medium" data-extend="{$MORE_FIELDS['invoiceextendid']}" name="negativetaxextend{$MORE_FIELDS['invoiceextendid']}" data-id="yesreplace" value="" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2"></div></span></div></td>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 价税合计</label></td>
                        <td class="fieldValue medium"><div class="row-fluid"><span class="span10"><div class="input-prepend" name="closedyesreplace{$MORE_FIELDS['invoiceextendid']}"><span class="add-on">¥</span><input type="hidden" class="t_invoice_totalandtaxextend" name="totalandtaxextend{$MORE_FIELDS['invoiceextendid']}" value=""> <input type="text" data-fee="{$MORE_FIELDS['totalandtaxextend']}" data-extend="{$MORE_FIELDS['invoiceextendid']}" class="input-medium validate[required] tt_negativetotalandtaxextend"  name="negativetotalandtaxextend{$MORE_FIELDS['invoiceextendid']}" value="" data-id="yesreplace"><input type="hidden" class="tt_record" name="record"  value="{$MORE_FIELDS['invoiceid']}"><input type="hidden" name="invoiceextendid" class="invoice_invoiceextendid" value="{$MORE_FIELDS['invoiceextendid']}"></div></span></div></td>
                    </tr>
                    <tr>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">备注</label></td>
                        <td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><textarea class="span11 " name="negativeremarkextend{$MORE_FIELDS['invoiceextendid']}"></textarea></span></div></td>
                    </tr>
                    <tr>
                        <td colspan="6"><button  type="button" class="btn btn-success pull-right saveRedInvoice"  data-id="{$MORE_FIELDS['invoiceextendid']}">确认</button></td>
                    </tr>
                </tbody>
            </table>
            {/if}
        {/if}

        <!-- 这个没有什么用 但是不要删除 主要是为了获取 __vtrftk -->
        <form class="tovoid_form"  method="post" action="index.php" style="display: none;">
            <input type="hidden" name="module" value="Newinvoice">
            <input type="hidden" name="action" value="Tovoid">
            <input type="hidden" name="record" value="{$smarty.get.record}">
            <input type="hidden" name="invoiceextendid" value="{$MORE_FIELDS['invoiceextendid']}">
            <input type="hidden" class="tovoid_negativetotalandtaxextend" value="{$MORE_FIELDS['totalandtaxextend']}">
        </form>

        {* 作废的回款信息 *}
        {if !empty($NEWINVOICEDATA)}
        <form></form>
        <form class="tovoid_form" id="tovoid_id_{$MORE_FIELDS['invoiceextendid']}" method="post" action="index.php" style="display: none;">
        <input type="hidden" name="module" value="Newinvoice">
        <input type="hidden" name="action" value="Tovoid">
        <input type="hidden" name="record" value="{$smarty.get.record}">
        <input type="hidden" name="invoiceextendid" value="{$MORE_FIELDS['invoiceextendid']}">
        <input type="hidden" class="tovoid_negativetotalandtaxextend" value="{$MORE_FIELDS['totalandtaxextend']}">
        <table class="table table-bordered blockContainer showInlineTable  detailview-table" id = "fallintotable">
	        <thead>
	        <tr>
	            <th class="blockHeader" colspan="7" >
	                <span class="redColor">发票作废</span>
	            </th>
	        </tr>
	        </thead>
	        <tbody>
	        <tr>
	        	<td><b>所属合同</b></td>
	        	<td><b>入账日期</b></td>
	        	<td><b>入账金额</b></td>
	        	<td><b>可开票金额</b></td>
	        	<td><b>此次开票金额</b></td>
                <td><b>剩余此次开票金额</b></td>
	        	<td><b>作废金额</b></td>
	        </tr>
	    	{foreach key=KEYINDEX item=D from=$NEWINVOICEDATA}
	        <tr >
	            <td>{$D['contract_no']} <input type="hidden" name="servicecontractsid[{$KEYINDEX + 1}]" value="{$D['servicecontractsid']}"><input type="hidden" name="contract_no[{$KEYINDEX + 1}]" value="{$D['contract_no']}">
                    <input type="hidden" name="receivedpaymentsid[{$KEYINDEX + 1}]" value="{$D['receivedpaymentsid']}">
                </td>
	            <td>{$D['arrivaldate']} </td>
	            <td>{$D['total']} <input type="hidden" class="tovoid_t_total" value="{$D['total']}" />
                    <input type="hidden" name="total[{$KEYINDEX + 1}]" value="{$D['total']}">
                </td>
	            <td>{$D['allowinvoicetotal']} <input type="hidden" class="tovoid_tovoie_allowinvoicetotal" value="{$D['allowinvoicetotal']}" />
                <input type="hidden" name="allowinvoicetotal[{$KEYINDEX + 1}]" value="{$D['allowinvoicetotal']}" />
                </td>
	            <td>
	            {$D['invoicetotal']}
	            	<input type="hidden" name="tovoidform[]" value="{$KEYINDEX + 1}">
	            	<input type="hidden" name="newinvoiceraymentid[{$KEYINDEX + 1}]" value="{$D['newinvoiceraymentid']}">
                    <input type="hidden" class="tovoid_invoicetotal" name="invoicetotal[{$KEYINDEX + 1}]" value="{$D['invoicetotal']}">
	            </td>
                <td>{$D['surpluinvoicetotal']}
                    <input type="hidden" class="c_surpluinvoicetotal" value="{$D['surpluinvoicetotal']}">
                </td>
	            <td><input type="number" class="tovoid_tovoie_total" value="{if count($NEWINVOICEDATA) eq 1 }{$MORE_FIELDS['totalandtaxextend']}{else}0{/if}" min="0.0" step="0.01" name="tovoie_total[{$KEYINDEX + 1}]" style="width: 100px; height:15px;"/></td>
	        </tr>
	        {/foreach}
	        <tr> 
	        	<td colspan="6" style="text-align: right;">
	        		<button class="btn btn-success tovoid_button" type="submit"><strong>保存</strong></button>
	        	</td>
	        </tr>
	        </tbody>
	    </table>
	    </form>
    	{/if}

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
    <style type="text/css">
        .modal-body{
            max-height: none;
            overflow-y: auto;
        }
    </style>
    <script src="/libraries/jSignature/jSignature.min.noconflict.js"></script>
{/strip}