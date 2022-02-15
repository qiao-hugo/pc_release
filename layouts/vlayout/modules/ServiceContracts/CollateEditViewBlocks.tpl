{strip}
    <link href="libraries/icheck/blue.css" rel="stylesheet">
    <script src="libraries/icheck/icheck.min.js"></script>
{literal}

    <script>
        $(document).ready(function(){
            $('.entryCheckBox').iCheck({
                checkboxClass: 'icheckbox_minimal-blue'
            });
        });
    </script>
{/literal}

<div class='editViewContainer container-fluid' xmlns="http://www.w3.org/1999/html">
	<form class="form-horizontal recordEditView" id="EditView" name="EditView" method="post" action="index.php" enctype="multipart/form-data">
		{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
		{if !empty($PICKIST_DEPENDENCY_DATASOURCE)}
			<input type="hidden" name="picklistDependency" value='{Vtiger_Util_Helper::toSafeHTML($PICKIST_DEPENDENCY_DATASOURCE)}' />
		{/if}
        <div id="ajaxanalogy">
        </div>
		<input type="hidden" name="module" value="ServiceContracts"/>
		<input type="hidden" name="action" value="Save" />
		<input type="hidden" name="record" value="{$RECORD_ID}" />
        <input type="hidden" name="contractbuytype" value="{$CONTRACT_CLASS_TYPE}" />
		<input type="hidden" name="old_sc_related_to" value="" />
		<input type="hidden" name="old_invoice_company" value="" />
		<input type="hidden" name="sideagreement" value="{$SIDEAGREEMENT}" />
        <input type="hidden" name="hasOrder" value="{$HASORDER}" />
        <input type="hidden" name="current_modulestatus" value="{$MODULESTATUS}" />
        <input type="hidden" name="isEdit" value="{$IS_EDIT}" />
		{if $IS_RELATION_OPERATION }
			<input type="hidden" name="sourceModule" value="{$SOURCE_MODULE}" />
			<input type="hidden" name="sourceRecord" value="{$SOURCE_RECORD}" />
			<input type="hidden" name="relationOperation" value="{$IS_RELATION_OPERATION}" />
		{/if}
        {if $SIGNATURETYPEHREF eq 'eleccontract'}
        <input type="hidden" name="eleccontracttpl" value="{$RECORD_STRUCTURE_MODEL->getRecord()->get('eleccontracttpl')}" />
        <input type="hidden" name="relatedattachment" value="{$RECORD_STRUCTURE_MODEL->getRecord()->get('relatedattachment')}" />
        <input type="hidden" name="eleccontractid" value="{$RECORD_STRUCTURE_MODEL->getRecord()->get('eleccontractid')}" />
        <input type="hidden" name="eleccontracttplurl" value="" />
        <input type="hidden" name="eleccontractidurl" value="" />
        <input type="hidden" name="relatedattachmenturl" value="" />
        <input type="hidden" name="oldeleccontracttplid" value="{$RECORD_STRUCTURE_MODEL->getRecord()->get('eleccontracttplid')}" />
        <input type="hidden" name="oldeleccontractid" value="{$RECORD_STRUCTURE_MODEL->getRecord()->get('eleccontractid')}" />
        <input type="hidden" name="oldfile" value="{$RECORD_STRUCTURE_MODEL->getRecord()->get('file')}" />
        {/if}
        <input type="hidden" name="is_collate" value="1"/>
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
				<button class="btn btn-success" id="servicecontractsub" type="submit"><strong>{vtranslate('LBL_SAVE', $MODULE)}</strong></button>
				<a class="cancelLink" type="reset" onclick="javascript:window.history.back();">{vtranslate('LBL_CANCEL', $MODULE)}</a>
			</span>
		</div>
        {assign var="CONTRACTATTRIBUTE" value={$RECORD_STRUCTURE_MODEL->getRecord()->get('contractattribute')}}
        {assign var="COLLATEFIELDS" value=['contract_no','sc_related_to','parent_contracttypeid','contract_type','signdate','total','invoicecompany','categoryid','productid','extraproductid', 'isstandard', 'servicecontractstype']}
        {foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE name="EditViewBlockLevelLoop"}
            {if $BLOCK_FIELDS|@count lte 0}{continue}{/if}
            {if  ($BLOCK_LABEL eq 'CONTRACT_PHASE_SPLIT' || $BLOCK_LABEL eq 'SETTLEMENT_CLAUSE' || $BLOCK_LABEL eq 'ELECCONTRACT_INFO')}
                {continue}
            {/if}
			<table class="table table-bordered blockContainer showInlineTable {$BLOCK_LABEL} {if $BLOCK_LABEL eq 'LBL_ADV'}hide tableadv{/if} detailview-table">
			<thead>
			<tr>
				<th class="blockHeader" colspan="4">
				<img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;"><img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">&nbsp;&nbsp;{vtranslate($BLOCK_LABEL, $MODULE)}</th>
			</tr>
			</thead>
			<tbody>
			<tr>
			{assign var=COUNTER value=0}
			{foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}
                {if !in_array($FIELD_MODEL->getFieldName(), $COLLATEFIELDS)}{continue}{/if}
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
						{if $FIELD_MODEL->isMandatory() eq true && $isReferenceField neq "reference" or $FIELD_MODEL->get('name') eq 'agentname'} <span class="redColor">*</span> {/if}
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
                            <label class="muted pull-right marginRight10px">{if $FIELD_MODEL->get('name')=='sc_related_to'}<span id='tripscrelatedto'></span>{else}{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span>{/if} {/if}{vtranslate($FIELD_MODEL->get('label'), $MODULE)}</label>
	                    {/if}
						{elseif $FIELD_MODEL->get('uitype') eq "83"}
							{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) COUNTER=$COUNTER}
						{else}
							{vtranslate($FIELD_MODEL->get('label'), $MODULE)}
						{/if}
					{if $isReferenceField neq "reference"}</label>{/if}
				</td>
				{if $FIELD_MODEL->get('uitype') neq "83"}
                    {if $FIELD_MODEL->get('label') eq "Priority"}
                        <td class="PriorityName">
                                {foreach from=$RECORD_ALLPRODUCTID item=constactValue key=constactKey}
                                <div style="line-height: 30px;float: left;width: 260px; border: 1px solid  rgba(57, 15, 40, 0.18); margin: 2px;  border-radius: 5px;padding-bottom:5px;">
                                    <label class="checkbox inline">
                                        <input type="checkbox"  {foreach from=$RECORD_PARTPRODUCTID item=value key=key}{if $value eq $constactValue['productid']}checked {/if}{/foreach}value="{$constactValue['productid']}" name="productid[]" data-name="productid" data-istyun="{$constactValue['istyun']}" class="productid entryCheckBox" >
                                        &nbsp;{$constactValue['productname']}
                                        <input type="hidden" name="producttypename[{$constactValue['productid']}]" value="{$constactValue['productname']}"/>
                                    </label>
                                </div>
                                {/foreach}
                        </td>
                    {elseif $FIELD_MODEL->get('label') eq "extraproductid"}
                        <td class="extraproductidname">
                                {assign var=EXTRAPRODUCT value=explode(',',$FIELD_MODEL->get('fieldvalue'))}
                            <table class="table table-bordered">
                                <thead>
                                    <tr><td>
                                    {foreach from=$RECORD_ALLEPRODUCTID1 item=extraValue key=constactKey}
                                        <div style="line-height: 30px;float: left;width: 260px; border: 1px solid  rgba(57, 15, 40, 0.18); margin: 2px;  border-radius: 5px;padding-bottom:5px;">
                                            <label class="checkbox inline">
                                                <input type="checkbox"
                                                       {if in_array($extraValue['productid'],$EXTRAPRODUCT)}checked {/if}
                                                       value="{$extraValue['productid']}" name="extraproductid[]" data-name="extraproductid" data-istyun="{$extraValue['istyun']}" class="extraproductid entryCheckBox" >
                                                &nbsp;{$extraValue['productname']}
                                                <input type="hidden" name="eproducttypename[{$extraValue['productid']}]" value="{$extraValue['productname']}"/>

                                            </label>
                                        </div>
                                    {/foreach}
                                    </td></tr>
                                    <tr><td>
                                            {foreach from=$RECORD_ALLEPRODUCTID2 item=extraValue key=constactKey}
                                                <div style="line-height: 30px;float: left;width: 260px; border: 1px solid  rgba(57, 15, 40, 0.18); margin: 2px;  border-radius: 5px;padding-bottom:5px;">
                                                    <label class="checkbox inline">
                                                        <input type="checkbox"
                                                               {if in_array($extraValue['productid'],$EXTRAPRODUCT)}checked {/if}
                                                               value="{$extraValue['productid']}" name="extraproductid[]" data-istyun="{$extraValue['istyun']}" data-name="extraproductid" class="extraproductid entryCheckBox" >
                                                        &nbsp;{$extraValue['productname']}
                                                        <input type="hidden" name="eproducttypename[{$extraValue['productid']}]" value="{$extraValue['productname']}"/>
                                                    </label>
                                                </div>
                                            {/foreach}
                                        </td></tr>
                                    <tr><td>
                                            {foreach from=$RECORD_ALLEPRODUCTID3 item=extraValue key=constactKey}
                                                <div style="line-height: 30px;float: left;width: 260px; border: 1px solid  rgba(57, 15, 40, 0.18); margin: 2px;  border-radius: 5px;padding-bottom:5px;">
                                                    <label class="checkbox inline">
                                                        <input type="checkbox"
                                                               {if in_array($extraValue['productid'],$EXTRAPRODUCT)}checked {/if}
                                                               value="{$extraValue['productid']}" name="extraproductid[]" data-name="extraproductid" class="extraproductid entryCheckBox" >
                                                        &nbsp;{$extraValue['productname']}
                                                        <input type="hidden" name="eproducttypename[{$extraValue['productid']}]" value="{$extraValue['productname']}"/>

                                                    </label>
                                                </div>
                                            {/foreach}
                                        </td></tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </td>
                    {elseif $FIELD_MODEL->get('label') eq "categoryid"}
                        <td class="fieldValue " id="categoryid" colspan="3">
                            {if $HASORDER && $CONTRACTTYPE=='T云WEB版' }
                            <select class="chzn-select" name="categoryid"  >
                                {foreach item=CATEGORY_VALUE key=CATEGORY_NAME from=$CATEGORY}
                                    <option value="{$CATEGORY_VALUE['id']}" {if $CATEGORYID eq $CATEGORY_VALUE['id'] }selected{/if}>{$CATEGORY_VALUE['title']}</option>
                                {/foreach}
                            </select>
                            {/if}
                        </td>
                        <tr/>
                    {else}
                        <td class="fieldValue {$WIDTHTYPE}" {if $FIELD_MODEL->get('uitype') eq '19'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if} {if $FIELD_MODEL->get('uitype') eq '20'} colspan="3"{/if}>
                            {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) BLOCK_FIELDS=$BLOCK_FIELDS}
                        </td>
                   {/if}
				{/if}
				{if $BLOCK_FIELDS|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype"}
					<td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
				{/if}
			{/foreach}
			</tr></tbody>
			</table>
			<br>
		{/foreach}
        <div class="widgetContainer_servicecontractproducts" data-url="module=Workflows&amp;view=Detail&amp;mode=getWorkflowsContent&amp;record=" data-name="Workflows">
            <div class="widget_contents"> </div>
        </div>
{/strip}
