{strip}
<div class='editViewContainer container-fluid'>
	<form class="form-horizontal recordEditView" id="EditView" name="EditView" method="post" action="index.php" enctype="multipart/form-data">
		{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
		{if !empty($PICKIST_DEPENDENCY_DATASOURCE)}
			<input type="hidden" name="picklistDependency" value='{Vtiger_Util_Helper::toSafeHTML($PICKIST_DEPENDENCY_DATASOURCE)}' />
		{/if}
		<input type="hidden" name="module" value="{$MODULE}" />
		<input type="hidden" name="action" value="Save" />
		<input type="hidden" name="record" value="{$RECORD_ID}" />
        <input type="hidden" name="billingid" value="{$BILLINGID}" />
	<input type="hidden" name="moreinvoice" value="{if $INVOICEDISPLAY eq 1}{md5(date('Y-m-d'))}{/if}" />
		{if $IS_RELATION_OPERATION }
			<input type="hidden" name="sourceModule" value="{$SOURCE_MODULE}" />
			<input type="hidden" name="sourceRecord" value="{$SOURCE_RECORD}" />
			<input type="hidden" name="relationOperation" value="{$IS_RELATION_OPERATION}" />
		{/if}
		<div class="contentHeader row-fluid">
		{assign var=SINGLE_MODULE_NAME value='SINGLE_'|cat:$MODULE}
		{if $RECORD_ID neq ''}
			<h3 title="{vtranslate('LBL_EDITING', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)} {$RECORD_STRUCTURE_MODEL->getRecordName()}">{vtranslate('LBL_EDITING', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)} - {$RECORD_STRUCTURE_MODEL->getRecordName()}</h3>
            <hr>
        {else}
			<h3>{vtranslate('LBL_CREATING_NEW', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)}</h3>
            <hr>
		{/if}
			<span class="pull-right">
				<button class="btn btn-success" type="submit"><strong>{vtranslate('LBL_SAVE', $MODULE)}</strong></button>
				<a class="cancelLink" type="reset" onclick="javascript:window.history.back();">{vtranslate('LBL_CANCEL', $MODULE)}</a>
			</span>
		</div>
        {*当前取得当前发票的类型-----start----*}
        {assign var=IS_TAXT_TYPE value=$RECORD_STRUCTURE['LBL_INVOICE_INFORMATION']['taxtype']->fieldvalue}
        {*-----end-----*}
		{foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE name="EditViewBlockLevelLoop"}
			{if $BLOCK_FIELDS|@count lte 0}{continue}{/if}
            {if $LBL_EDIT_NEGATIVE eq 'NegativeEdit'}
                {if $BLOCK_LABEL eq 'LBL_NEGATIVE_INFORMATION'}
                <table class="table table-bordered blockContainer showInlineTable {if $BLOCK_LABEL eq 'LBL_ADV' OR $BLOCK_LABEL eq 'LBL_INVOICE_INFORMATIONA'}{if $IS_TAXT_TYPE eq 'generalinvoice' || $IS_TAXT_TYPE eq ''} hide {/if} tableadv{/if} detailview-table">
                <thead>
                <tr>
                    <th class="blockHeader" colspan="4">
                    {assign var=BLOCK_LABEL_NAME value='A_'}
                    <img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;"><img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">&nbsp;&nbsp;{vtranslate($BLOCK_LABEL, $MODULE)}{vtranslate($BLOCK_LABEL_NAME|cat:$BLOCK_LABEL, $MODULE)}</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                {assign var=COUNTER value=0}
                {foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}

                    {assign var="isReferenceField" value=$FIELD_MODEL->getFieldDataType()}
                    {if $FIELD_MODEL->get('uitype') eq "20" or $FIELD_MODEL->get('uitype') eq "19"}
                        {if $COUNTER eq '1'}
                            <td class="{$WIDTHTYPE}"></td><td class="{$WIDTH_TYPE_CLASSSES[$WIDTHTYPE]}"></td></tr><tr>
                            {assign var=COUNTER value=0}
                        {/if}
                    {/if}
                    {if $COUNTER eq 2}
                        </tr><tr>
                        {assign var=COUNTER value=1}
                    {else}
                        {assign var=COUNTER value=$COUNTER+1}
                    {/if}
                    <td class="fieldLabel {$WIDTHTYPE}">
                        {if $isReferenceField neq "reference"}<label class="muted pull-right marginRight10px">{/if}
                            {if $FIELD_MODEL->isMandatory() eq true && $isReferenceField neq "reference"} <span class="redColor">*</span> {/if}
                            {if $isReferenceField eq "reference"}
                                {assign var="REFERENCE_LIST" value=$FIELD_MODEL->getReferenceList()}
                                {assign var="REFERENCE_LIST_COUNT" value=count($REFERENCE_LIST)}
                                {if $REFERENCE_LIST_COUNT > 1}
                                    {assign var="DISPLAYID" value=$FIELD_MODEL->get('fieldvalue')}
                                    {assign var="REFERENCED_MODULE_STRUCT" value=$FIELD_MODEL->getUITypeModel()->getReferenceModule($DISPLAYID)}
                                    {if !empty($REFERENCED_MODULE_STRUCT)}
                                        {assign var="REFERENCED_MODULE_NAME" value=$REFERENCED_MODULE_STRUCT->get('name')}
                                    {/if}
                                    <span class="pull-right">
                                        {if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}
                                        <select class="chzn-select referenceModulesList streched" style="width:140px;">
                                            <optgroup>
                                                {foreach key=index item=value from=$REFERENCE_LIST}
                                                    <option value="{$value}" {if $value eq $REFERENCED_MODULE_NAME} selected {/if}>{vtranslate($value, $MODULE)}</option>
                                                {/foreach}
                                            </optgroup>
                                        </select>
                                    </span>
                                {else}
                                    <label class="muted pull-right marginRight10px">{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}{vtranslate($FIELD_MODEL->get('label'), $MODULE)}</label>
                                {/if}
                            {else if $FIELD_MODEL->get('uitype') eq "83"}
                                {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) COUNTER=$COUNTER}
                            {else}
                                {vtranslate($FIELD_MODEL->get('label'), $MODULE)}
                            {/if}
                        {if $isReferenceField neq "reference"}</label>{/if}
                    </td>
                    {if $FIELD_MODEL->get('uitype') neq "83"}
                        <td class="fieldValue {$WIDTHTYPE}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
                            {if $FIELD_MODEL->get('editread') || ($FIELD_MODEL->get('readonly') eq '0' and !empty($RECORD_ID))}
                                {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),$MODULE) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE RECORD=$RECORD}<input disabled="disabled" name="{$FIELD_MODEL->get('column')}" type="hidden" value="{$FIELD_MODEL->get('fieldvalue')}">
                            {else}
                            <div class="row-fluid">
                                <span class="span10">
                                    {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) BLOCK_FIELDS=$BLOCK_FIELDS}
                                </span>
                            </div>
                            {/if}
                        </td>
                    {/if}
                    {if $BLOCK_FIELDS|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype"}
                        <td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
                    {/if}
                {/foreach}
                </tr></tbody>
                </table>
                <br>
                {/if}
            {else}
                {if $BLOCK_LABEL eq 'LBL_NEGATIVE_INFORMATION'}{continue}{/if}
		{if $BLOCK_LABEL eq 'LBL_TERMS_INFORMATION' && $INVOICEDISPLAY neq 1}{continue}{/if}
                <table class="table table-bordered blockContainer showInlineTable {if $BLOCK_LABEL eq 'LBL_ADV' OR $BLOCK_LABEL eq 'LBL_INVOICE_INFORMATIONA'}{if $IS_TAXT_TYPE eq 'generalinvoice' || $IS_TAXT_TYPE eq ''} hide {/if} tableadv{/if} detailview-table">
                    <thead>
                    <tr>
                        <th class="blockHeader" colspan="4">
                            {assign var=BLOCK_LABEL_NAME value='A_'}
                            <img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;"><img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">&nbsp;&nbsp;{vtranslate($BLOCK_LABEL, $MODULE)}{vtranslate($BLOCK_LABEL_NAME|cat:$BLOCK_LABEL, $MODULE)} {if $BLOCK_LABEL eq 'LBL_TERMS_INFORMATION' && $INVOICEDISPLAY eq 1}<b class="pull-right"><button class="btn btn-small" type="button" id="addfallinto"><i class="icon-plus" title="点击添加多发票信息"></i></button></b>{/if}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        {assign var=COUNTER value=0}
                        {foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}
			{if $BLOCK_LABEL eq 'LBL_TERMS_INFORMATION'}{break}{/if}
                        {assign var="isReferenceField" value=$FIELD_MODEL->getFieldDataType()}
                        {if $FIELD_MODEL->get('uitype') eq "20" or $FIELD_MODEL->get('uitype') eq "19"}
                        {if $COUNTER eq '1'}
                        <td class="{$WIDTHTYPE}"></td><td class="{$WIDTH_TYPE_CLASSSES[$WIDTHTYPE]}"></td></tr><tr>
                        {assign var=COUNTER value=0}
                        {/if}
                        {/if}
                        {if $COUNTER eq 2}
                    </tr><tr>
                        {assign var=COUNTER value=1}
                        {else}
                        {assign var=COUNTER value=$COUNTER+1}
                        {/if}
                        <td class="fieldLabel {$WIDTHTYPE}">
                            {if $isReferenceField neq "reference"}<label class="muted pull-right marginRight10px">{/if}
                                {if $FIELD_MODEL->isMandatory() eq true && $isReferenceField neq "reference"} <span class="redColor">*</span> {/if}
                                {if $isReferenceField eq "reference"}
                                    {assign var="REFERENCE_LIST" value=$FIELD_MODEL->getReferenceList()}
                                    {assign var="REFERENCE_LIST_COUNT" value=count($REFERENCE_LIST)}
                                    {if $REFERENCE_LIST_COUNT > 1}
                                        {assign var="DISPLAYID" value=$FIELD_MODEL->get('fieldvalue')}
                                        {assign var="REFERENCED_MODULE_STRUCT" value=$FIELD_MODEL->getUITypeModel()->getReferenceModule($DISPLAYID)}
                                        {if !empty($REFERENCED_MODULE_STRUCT)}
                                            {assign var="REFERENCED_MODULE_NAME" value=$REFERENCED_MODULE_STRUCT->get('name')}
                                        {/if}
                                        <span class="pull-right">
                                        {if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}
                                            <select class="chzn-select referenceModulesList streched" style="width:140px;">
                                                <optgroup>
                                                    {foreach key=index item=value from=$REFERENCE_LIST}
                                                        <option value="{$value}" {if $value eq $REFERENCED_MODULE_NAME} selected {/if}>{vtranslate($value, $MODULE)}</option>
                                                    {/foreach}
                                                </optgroup>
                                            </select>
                                    </span>
                                    {else}
                                        <label class="muted pull-right marginRight10px">{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}{vtranslate($FIELD_MODEL->get('label'), $MODULE)}</label>
                                    {/if}
                                {else if $FIELD_MODEL->get('uitype') eq "83"}
                                    {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) COUNTER=$COUNTER}
                                {else}
                                    {vtranslate($FIELD_MODEL->get('label'), $MODULE)}
                                {/if}
                                {if $isReferenceField neq "reference"}</label>{/if}
                        </td>
                        {if $FIELD_MODEL->get('uitype') neq "83"}
                            <td class="fieldValue {$WIDTHTYPE}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
                                {if $FIELD_MODEL->get('editread') || ($FIELD_MODEL->get('readonly') eq '0' and !empty($RECORD_ID))}
                                    {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),$MODULE) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE RECORD=$RECORD}<input disabled="disabled" name="{$FIELD_MODEL->get('column')}" type="hidden" value="{$FIELD_MODEL->get('fieldvalue')}">
                                {else}
                                    <div class="row-fluid">
                                <span class="span10">
                                    {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) BLOCK_FIELDS=$BLOCK_FIELDS}
                                </span>
                                    </div>
                                {/if}
                            </td>
                        {/if}
                        {if $BLOCK_FIELDS|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype"}
                            <td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
                        {/if}
                        {/foreach}
                    </tr></tbody>
                </table>
                <br>
            {/if}
		{/foreach}
{if $INVOICEDISPLAY eq 1}
	{assign var=CURRENT_USER_ID value=$USER_MODEL->get('id')}
        {if !empty($MOREINVOICES)}
            {foreach key=KEYINDEX item=MORE_FIELDS from=$MOREINVOICES}
                <table class="table table-bordered blockContainer Duplicates showInlineTable  detailview-table" data-num="{$KEYINDEX+1}">
                    <thead>
                        <tr>
                            <th class="blockHeader" colspan="4">&nbsp;&nbsp;财务数据(财务录入)[{$KEYINDEX+1}] <b class="pull-right">
                                    <button class="btn btn-small delbuttonextend" type="button"  data-id="{$MORE_FIELDS['invoiceextendid']+105}">
                                        <i class="icon-trash" title="删除财务数据"></i>
                                    </button></b>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td class="fieldLabel medium">
                            <label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 发票代码</label>
                            <input type="hidden" name="updatei[{$MORE_FIELDS['invoiceextendid']+105}]" value="{$MORE_FIELDS['invoiceextendid']+105}">
                        </td>
                        <td class="fieldValue medium">
                            <div class="row-fluid">
                                <span class="span10">
                                    <input  type="text" class="input-large" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="invoicecodeextend[{$MORE_FIELDS['invoiceextendid']+105}]" data-id="{$MORE_FIELDS['invoiceextendid']+105}" value="{$MORE_FIELDS['invoicecodeextend']}">
                                </span>
                            </div>
                        </td>
                        <td class="fieldLabel medium">
                            <label class="muted pull-right marginRight10px"><span class="redColor">*</span> 发票号码</label>
                        </td>
                        <td class="fieldValue medium">
                            <div class="row-fluid">
                                <span class="span10">
                                    <input type="text" class="input-large " data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="invoice_noextend[{$MORE_FIELDS['invoiceextendid']+105}]" data-id="{$MORE_FIELDS['invoiceextendid']+105}" value="{$MORE_FIELDS['invoice_noextend']}">
                                </span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="fieldLabel medium">
                            <label class="muted pull-right marginRight10px"><span class="redColor">*</span> 实际开票抬头</label>
                        </td>
                        <td class="fieldValue medium">
                            <div class="row-fluid">
                                <span class="span10">
                                    <input type="text" class="input-large " data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="businessnamesextend[{$MORE_FIELDS['invoiceextendid']+105}]" data-id="{$MORE_FIELDS['invoiceextendid']+105}" value="{$MORE_FIELDS['businessnamesextend']}">
                                </span>
                            </div>
                        </td>
                        <td class="fieldLabel medium">
                            <label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 开票人</label>
                        </td>
                        <td class="fieldValue medium">
                            <div class="row-fluid">
                                <span class="span10">
                                    <select class="chzn-select drawerextend" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="drawerextend[{$MORE_FIELDS['invoiceextendid']+105}]" data-id="{$MORE_FIELDS['invoiceextendid']+105}">
                                        <optgroup label="用户">
                                            {foreach key=OWNER_ID item=OWNER_NAME from=$ACCESSIBLE_USERS}
                                                <option value="{$OWNER_ID}" data-picklistvalue="{$OWNER_ID}" {if $MORE_FIELDS['drawerextend'] eq $OWNER_ID} selected {/if} data-userId="{$CURRENT_USER_ID}">{$OWNER_NAME}</option>
                                            {/foreach}
                                        </optgroup>
                                    </select>
                                </span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 开票日期</label></td>
                        <td class="fieldValue medium">
                            <div class="row-fluid">
                                <span class="span10">
                                    <div class="input-append row-fluid">
                                        <div class="span10 row-fluid date form_datetime">
                                            <input type="text" class="span9 billingtimerextends dateField" name="billingtimerextend[{$MORE_FIELDS['invoiceextendid']+105}]" data-id="{$MORE_FIELDS['invoiceextendid']+105}" data-date-format="yyyy-mm-dd" readonly="readyonly" value="{$MORE_FIELDS['billingtimeextend']}" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]">
                                            <span class="add-on"><i class="icon-calendar"></i></span>
                                        </div>
                                    </div>
                                </span>
                            </div>
                        </td>
                        <td class="fieldLabel medium">
                            <label class="muted pull-right marginRight10px">商品名称</label>
                        </td>
                        <td class="fieldValue medium">
                            <div class="row-fluid">
                                <span class="span10">
                                    <input type="text" class="input-large " data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="commoditynameextend[{$MORE_FIELDS['invoiceextendid']+105}]" data-id="{$MORE_FIELDS['invoiceextendid']+105}" value="{$MORE_FIELDS['commoditynameextend']}">
                                </span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="fieldLabel medium">
                            <label class="muted pull-right marginRight10px"><span class="redColor">*</span> 金额</label>
                        </td>
                        <td class="fieldValue medium">
                            <div class="row-fluid">
                                <span class="span10">
                                    <div class="input-prepend">
                                        <span class="add-on">¥</span>
                                        <input type="text" class="input-medium" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="amountofmoneyextend[{$MORE_FIELDS['invoiceextendid']+105}]" data-id="{$MORE_FIELDS['invoiceextendid']+105}" value="{$MORE_FIELDS['amountofmoneyextend']}">
                                    </div>
                                </span>
                            </div>
                        </td>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 税率</label></td>
                        <td class="fieldValue medium">
                            <div class="row-fluid">
                                <span class="span10">
                                    <select class="chzn-select" name="taxrateextend[{$MORE_FIELDS['invoiceextendid']+105}]" data-id="{$MORE_FIELDS['invoiceextendid']+105}" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]">
                                        <option value="6%" {if $MORE_FIELDS['taxrateextend'] eq '6%'}selected{/if}>6%</option>
                                        <option value="17%" {if $MORE_FIELDS['taxrateextend'] eq '17%'}selected{/if}>17%</option>
                                    </select>
                                </span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 税额</label></td>
                        <td class="fieldValue medium">
                            <div class="row-fluid">
                                <span class="span10">
                                    <div class="input-prepend">
                                        <span class="add-on">¥</span>
                                        <input type="text" class="input-medium" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="taxextend[{$MORE_FIELDS['invoiceextendid']+105}]" data-id="{$MORE_FIELDS['invoiceextendid']+105}" value="{$MORE_FIELDS['taxextend']}">
                                    </div>
                                </span>
                            </div>
                        </td>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 价税合计</label></td>
                        <td class="fieldValue medium">
                            <div class="row-fluid">
                                <span class="span10">
                                    <div class="input-prepend">
                                        <span class="add-on">¥</span>
                                        <input type="text" class="input-medium" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="totalandtaxextend[{$MORE_FIELDS['invoiceextendid']+105}]" value="{$MORE_FIELDS['totalandtaxextend']}" data-id="{$MORE_FIELDS['invoiceextendid']+105}">
                                    </div>
                                </span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">备注</label></td>
                        <td class="fieldValue medium" colspan="3">
                            <div class="row-fluid">
                                <span class="span10">
                                    <textarea class="span11 " name="remarkextend[{$MORE_FIELDS['invoiceextendid']+105}]" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]">{$MORE_FIELDS['remarkextend']}</textarea>
                                </span>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <br>
            {/foreach}
        {/if}
        {literal}
            <script>
                var extendinvoice='<table class="table table-bordered blockContainer Duplicates showInlineTable  detailview-table" data-num="yesreplace"><thead><tr><th class="blockHeader" colspan="4">&nbsp;&nbsp;财务数据(财务录入)replaceyes <b class="pull-right"><button class="btn btn-small delbuttonextend" type="button"  data-id="yesreplace"><i class="icon-trash" title="删除财务数据"></i></button></b></th></tr></thead><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 发票代码</label><input type="hidden" name="inserti[]" value="yesreplace"></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input  type="text" class="input-large" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="invoicecodeextend[]" data-id="yesreplace" value=""></span></div></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 发票号码</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="text" class="input-large " data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="invoice_noextend[]" data-id="yesreplace" value=""></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 实际开票抬头</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="text" class="input-large " data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="businessnamesextend[]" data-id="yesreplace" value=""></span></div></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 开票人</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><select class="chzn-select drawerextend" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="drawerextend[]" data-id="yesreplace"><optgroup label="用户">{/literal}{foreach key=OWNER_ID item=OWNER_NAME from=$ACCESSIBLE_USERS} <option value="{$OWNER_ID}" data-picklistvalue="{$OWNER_ID}" {if $FIELD_VALUE eq $OWNER_ID || $CURRENT_USER_ID eq $OWNER_ID} selected {/if} data-userId="{$CURRENT_USER_ID}">{$OWNER_NAME}</option> {/foreach}{literal}</optgroup></select></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 开票日期</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><div class="input-append row-fluid"><div class="span10 row-fluid date form_datetime"><input type="text" class="span9 billingtimerextends dateField" name="billingtimerextend[]" data-id="yesreplace" readonly="" value="{/literal}{date('Y-m-d')}{literal}" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"><span class="add-on"><i class="icon-calendar"></i></span></div></div></span></div></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">商品名称</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="text" class="input-large " data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="commoditynameextend[]" data-id="yesreplace" value=""></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 金额</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><div class="input-prepend"><span class="add-on">¥</span><input type="text" class="input-medium" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="amountofmoneyextend[]" data-id="yesreplace" value="" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2"></div></span></div></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span> 税率</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><select class="chzn-select" name="taxrateextend[]" data-id="yesreplace" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"><option value="6%" selected="selected">6%</option><option value="17%">17%</option></select></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 税额</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><div class="input-prepend"><span class="add-on">¥</span><input type="text" class="input-medium" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="taxextend[]" data-id="yesreplace" value="" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2"></div></span></div></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 价税合计</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><div class="input-prepend" id="closedyesreplace"><span class="add-on">¥</span><input type="text" class="input-medium" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="totalandtaxextend[]" value="" data-id="yesreplace"></div></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">备注</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><textarea class="span11 " name="remarkextend[]" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"></textarea></span></div></td></tr></tbody></table>';
            </script>
        {/literal}
        <table class="table table-bordered blockContainer showInlineTable  detailview-table invoicelistdisplay {if empty($INVOICE_LIST) || !$INVOICE_PAYMENTS}hide{/if}">
            <thead>
            <tr>
                <th class="blockHeader" colspan="12">
                    <img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;">
                    <img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">&nbsp;&nbsp;合同回款记录
                </th>
            </tr>
            </thead>
            <tbody  class="invoicelist">
            {if !empty($INVOICE_LIST) && $INVOICE_PAYMENTS}
                <tr><td>勾选</td><td>所属合同</td><td>货币类型</td><td>本位币</td><td>汇率</td><td>回款金额</td><td>回款时间</td><td>创建人</td><td>汇款抬头</td><td>开据状态</td><td>备注&说明</td><td>发票编号</td></tr>
                {foreach item=VALUE from=$INVOICE_LIST}
                    {if $LBL_EDIT_NEGATIVE eq 'NegativeEdit'}
                        <tr><td>{if $LBL_EDIT_NEGATIVE eq 'NegativeEdit'}<input type="checkbox"  class="hide"{if $RECORD_ID eq $VALUE['invoicesid'] || $VALUE['invoicesid'] eq '--'} name="receivedid[]" value="{$VALUE['receivedid']}" {else} disabled class="hide"{/if}{if $RECORD_ID eq $VALUE['invoicesid']} checked="checked" {/if}>{else}<input type="checkbox" {if  $INVOICE_LIST_STATUS eq 'b_check'} class="hide"{/if}{if $RECORD_ID eq $VALUE['invoicesid'] || $VALUE['invoicesid'] eq '--'} name="receivedid[]" value="{$VALUE['receivedid']}" {else} disabled class="hide"{/if}{if $RECORD_ID eq $VALUE['invoicesid']} checked {/if}>{/if}</td></td><td>{$VALUE['contract_no']}</td><td>{$VALUE['currencytype']}</td><td>{$VALUE['standardmoney']}</td><td>{$VALUE['exchangerate']}</td><td>{$VALUE['unit_price']}</td><td>{$VALUE['reality_date']}</td><td>{$VALUE['createid']}</td><td>{if $RECORD_ID eq $VALUE['invoicesid'] || $VALUE['invoicesid'] eq '--'}<input name="companytwo" value="{$VALUE['paytitle']}" type="hidden" disabled><button id="getcompanyname" type="button" class="btn btn-info setcompanyname" data-name="companytwo" title="设置实际开票抬头">{$VALUE['paytitle']}</button>{else}{$VALUE['paytitle']}{/if}</td><td>{if $RECORD_ID eq $VALUE['invoicesid'] || $VALUE['invoicesid'] eq '--'}{if $VALUE['modulestatus'] eq '--'}<span class="label label-success">正常</span>{else}<span class="label label-{$VALUE['modulestatus']}">{vtranslate($VALUE['modulestatus'], $MODULE)}</span>{/if}{else}<span class="label label-warning">已开据</span>{/if}</td></td><td>{$VALUE['overdue']}</td><td>{$VALUE['invoice_no']}</td></tr>
                    {else}
                        <tr><td><input type="checkbox" {*{if  $INVOICE_LIST_STATUS eq 'b_check'} class="hide"{/if}{if $RECORD_ID eq $VALUE['invoicesid'] || $VALUE['invoicesid'] eq '--'}*} name="receivedid[]" value="{$VALUE['receivedid']}" {*{else} disabled class="hide"{/if}*}{if $RECORD_ID eq $VALUE['invoicesid']} checked {/if}></td></td><td>{$VALUE['contract_no']}</td><td>{$VALUE['currencytype']}</td><td>{$VALUE['standardmoney']}</td><td>{$VALUE['exchangerate']}</td><td>{$VALUE['unit_price']}</td><td>{$VALUE['reality_date']}</td><td>{$VALUE['createid']}</td><td>{if $RECORD_ID eq $VALUE['invoicesid'] || $VALUE['invoicesid'] eq '--'}<input name="companytwo" value="{$VALUE['paytitle']}" type="hidden" disabled><button id="getcompanyname" type="button" class="btn btn-info setcompanyname" data-name="companytwo" title="设为实际开票抬头">{$VALUE['paytitle']}</button>{else}{$VALUE['paytitle']}{/if}</td><td>{if $RECORD_ID eq $VALUE['invoicesid'] || $VALUE['invoicesid'] eq '--'}{if $VALUE['modulestatus'] eq '--'}<span class="label label-success">正常</span>{else}<span class="label label-{$VALUE['modulestatus']}">{vtranslate($VALUE['modulestatus'], $MODULE)}</span>{/if}{else}<span class="label label-warning">已开据</span>{/if}</td></td><td>{$VALUE['invoice_no']}</td><td>{$VALUE['overdue']}</td></tr>
                    {/if}
                {/foreach}
            {/if}
            </tbody>
        </table>
        <table class="table table-bordered blockContainer showInlineTable  detailview-table invoicenolistdisplay {if empty($INVOICE_LIST)}hide{/if}">
            <thead>
            <tr>
                <th class="blockHeader" colspan="8">
                    <img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;">
                    <img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">&nbsp;&nbsp;<font style="color:red">含有非正常发票</font>
                </th>
            </tr>
            </thead>
            <tbody   class="invoicenolist">
            </tbody>
        </table>
{else}
    {if !empty($MOREINVOICES)}
        {*没有权限但有数据是走这一块*}
    {foreach key=KEYINDEX item=MORE_FIELDS from=$MOREINVOICES}
        <table class="table table-bordered blockContainer Duplicates showInlineTable  detailview-table"">
            <thead>
            <tr>
                <th class="blockHeader" colspan="4">&nbsp;&nbsp;财务数据(财务录入)[{$KEYINDEX+1}] <b class="pull-right">
                </th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 发票代码</label>
                </td>
                <td class="fieldValue medium">
                    {$MORE_FIELDS['invoicecodeextend']}
                </td>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px"><span class="redColor">*</span> 发票号码</label>
                </td>
                <td class="fieldValue medium">
                    {$MORE_FIELDS['invoice_noextend']}
                </td>
            </tr>
            <tr>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px"><span class="redColor">*</span> 实际开票抬头</label>
                </td>
                <td class="fieldValue medium">
                    {$MORE_FIELDS['businessnamesextend']}
                </td>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 开票人</label>
                </td>
                <td class="fieldValue medium">
                    {foreach key=OWNER_ID item=OWNER_NAME from=$ACCESSIBLE_USERS}
                         {if $MORE_FIELDS['drawerextend'] eq $OWNER_ID} {$OWNER_NAME} {/if}
                    {/foreach}
                </td>
            </tr>
            <tr>
                <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 开票日期</label></td>
                <td class="fieldValue medium">
                    {$MORE_FIELDS['billingtimeextend']}
                </td>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">商品名称</label>
                </td>
                <td class="fieldValue medium">
                    {$MORE_FIELDS['commoditynameextend']}
                </td>
            </tr>
            <tr>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px"><span class="redColor">*</span> 金额</label>
                </td>
                <td class="fieldValue medium">
                    {$MORE_FIELDS['amountofmoneyextend']}
                </td>
                <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 税率</label></td>
                <td class="fieldValue medium">
                    {$MORE_FIELDS['taxrateextend']}
                </td>
            </tr>
            <tr>
                <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 税额</label></td>
                <td class="fieldValue medium">
                    {$MORE_FIELDS['taxextend']}
                </td>
                <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 价税合计</label></td>
                <td class="fieldValue medium">
                    {$MORE_FIELDS['totalandtaxextend']}
                </td>
            </tr>
            <tr>
                <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">备注</label></td>
                <td class="fieldValue medium" colspan="3">
                    {$MORE_FIELDS['remarkextend']}
                </td>
            </tr>
            </tbody>
        </table>
    <br>
    {/foreach}
    {/if}
    {/if}
		<div class="widgetContainer_0" data-name="Workflows">
    <div class="widget_contents"></div>
    	
    <div class="widget_content" style="margin-top:30px;"></div>
</div>
{/strip}