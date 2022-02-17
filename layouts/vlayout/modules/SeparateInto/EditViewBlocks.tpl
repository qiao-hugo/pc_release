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
		<input type="hidden" name="module" value="{$MODULE}" />
		<input type="hidden" name="action" value="Save" />
		<input type="hidden" name="record" value="{$RECORD_ID}" />
		<input type="hidden" name="salesharing" value="" />
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
		{foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE name="EditViewBlockLevelLoop"}
			{if $BLOCK_FIELDS|@count lte 0}{continue}{/if}
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
                    {if $FIELD_MODEL->get('label') eq "Priority"}
                        <td class="PriorityName">
                            {*{if ($FIELD_MODEL->getEditViewDisplayValue($FIELD_MODEL->get('fieldvalue')) eq '') }
                                <font color="red">没有相对应的产品信息!</font>
                                {else}*}

                                {foreach from=$RECORD_ALLPRODUCTID item=constactValue key=constactKey}
                                <div style="line-height: 30px;float: left;width: 290px; border: 1px solid  rgba(57, 15, 40, 0.18); margin: 2px;  border-radius: 5px;padding-bottom:5px;">
                                    <label class="checkbox inline">
                                        <input type="checkbox"  {foreach from=$RECORD_PARTPRODUCTID item=value key=key}{if $value eq $constactValue['productid']}checked {/if}{/foreach}value="{$constactValue['productid']}" name="productid[]" data-name="productid" class="productid entryCheckBox" >
                                        &nbsp;{$constactValue['productname']}

                                    </label>
                                </div>
                                {/foreach}

                                {*<input name="{$FIELD_MODEL->getFieldName()}" type="hidden" value="{$FIELD_MODEL->get('fieldvalue')}" data-multiple="{$FIELD_MODEL->get('ismultiple')}" class="sourceField" data-displayvalue='{$FIELD_MODEL->getEditViewDisplayValue($FIELD_MODEL->get('fieldvalue'))}' data-fieldinfo='{$FIELD_INFO}' />*}

                            {*{/if}*}
                        </td>
                    {elseif $FIELD_MODEL->get('label') eq "extraproductid"}
                        <td class="extraproductidname">
                            {*
                            {if ($FIELD_MODEL->get('fieldvalue') eq '') }
                                <font color="red">没有相对应的额外产品信息!111</font>
                            {else}*}
                                {assign var=EXTRAPRODUCT value=explode(',',$FIELD_MODEL->get('fieldvalue'))}
                            <table class="table table-bordered">
                                <thead>
                                    <tr><td>
                                    {foreach from=$RECORD_ALLEPRODUCTID1 item=extraValue key=constactKey}
                                        <div style="line-height: 30px;float: left;width: 260px; border: 1px solid  rgba(57, 15, 40, 0.18); margin: 2px;  border-radius: 5px;padding-bottom:5px;">
                                            <label class="checkbox inline">
                                                <input type="checkbox"
                                                       {if in_array($extraValue['productid'],$EXTRAPRODUCT)}checked {/if}
                                                       value="{$extraValue['productid']}" name="extraproductid[]" data-name="extraproductid" class="extraproductid entryCheckBox" >
                                                &nbsp;{$extraValue['productname']}

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
                                                               value="{$extraValue['productid']}" name="extraproductid[]" data-name="extraproductid" class="extraproductid entryCheckBox" >
                                                        &nbsp;{$extraValue['productname']}

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

                                                    </label>
                                                </div>
                                            {/foreach}
                                        </td></tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>


                                {*<input name="{$FIELD_MODEL->getFieldName()}" type="hidden" value="{$FIELD_MODEL->get('fieldvalue')}" data-multiple="{$FIELD_MODEL->get('ismultiple')}" class="sourceField" data-displayvalue='{$FIELD_MODEL->getEditViewDisplayValue($FIELD_MODEL->get('fieldvalue'))}' data-fieldinfo='{$FIELD_INFO}' />*}

                            {*{/if}*}
                        </td>
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
        <script>
            var aaaaa="<tr><td>{if $FIELD_VALUE eq ''}{assign var=FIELD_VALUE value=$USER_MODEL->get('id')}{/if}	<select class=\"chzn-select\" name=\"suoshuren\[\]\"> <optgroup label=\"{vtranslate('LBL_USERS')}\"> 	{foreach key=OWNER_ID item=OWNER_NAME from=$ACCESSIBLE_USERS_DIVIDE} <option value=\"{$OWNER_NAME['id']}\" data-company='{$OWNER_NAME['invoicecompany']}' data-picklistvalue= '{$OWNER_NAME['id']}' {if $FIELD_VALUE eq $OWNER_NAME['id']} selected {/if} data-userId=\"{$CURRENT_USER_ID}\">{$OWNER_NAME['last_name']}</option> {/foreach} </optgroup>	</select></td><td><select  class=\"chzn-select\" disabled=\"disabled\" name=\"suoshugongsi\[\]\"> <optgroup label=\"{vtranslate('LBL_USERS')}\"><option value=\"\" data-picklistvalue=\"\"></option>{foreach key=OWNER_ID item=OWNER_NAME from=$OWNCOMPANY}<option value=\"{$OWNER_ID}\" data-picklistvalue= '{$OWNER_NAME}' {if $USER_MODEL->get('invoicecompany') eq $OWNER_ID} selected {elseif $OWNER_ID eq "上海珍岛信息技术有限公司"}selected{/if}  	data-userId=\"{$CURRENT_USER_ID}\">{$OWNER_NAME} </option> {/foreach} </optgroup> </select></td><td><div class=\"input-append\"> <input name=\"bili\[\]\" type=\"text\" placeholder = \"请输入比例\" class=\"scaling\" ><span class=\"add-on\">%</i></span></div></td><td class=\"muted pull-right marginRight10px\"> <b><button class=\"btn btn-small deletefallinto\" type=\"button\"><i class=\" icon-trash\"></i></button></b></td></tr>";
        </script>
        <table class="table table-bordered blockContainer showInlineTable  detailview-table" id = "fallintotable">
            <thead>
            <tr>
                <th class="blockHeader" colspan="4" >
                    <img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">&nbsp;&nbsp;
                    合同分成信息
                </th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><b>业绩所属人</b></td>
                <td><b>所属公司</b></td>
                <td><b>比例</b></td>
                <td class="muted pull-right marginRight10px"><b><button class="btn btn-small" type="button" id="addfallinto"><i class=" icon-plus"></i></button></b></td>
            </tr>
            {if $CONTRACTS_DIVIDE neq ""}
                {foreach from=$CONTRACTS_DIVIDE item="divide_data" key="divide_key"}
                    <tr >
                        <td>
                            <select class="chzn-select" name="suoshuren[]">
                                <optgroup label="{vtranslate('LBL_USERS')}">
                                    {foreach key=OWNER_ID item=OWNER_NAME from=$ACCESSIBLE_USERS_DIVIDE}
                                        <option value="{$OWNER_NAME['id']}" data-picklistvalue= '{$OWNER_NAME['id']}' data-company='{$OWNER_NAME['invoicecompany']}' {if $divide_data['receivedpaymentownid'] eq $OWNER_NAME['id']} selected {/if}">{$OWNER_NAME['last_name']}</option>
                                    {/foreach}
                                </optgroup>
                            </select>
                        </td>
                        <td>
                            <select class="chzn-select" disabled="disabled" name="suoshugongsi[]">
                                <optgroup label="{vtranslate('LBL_USERS')}">
                                    {foreach key=OWNER_ID item=OWNER_NAME from=$OWNCOMPANY}
                                        <option value="{$OWNER_ID}"  {if $divide_data['owncompanys'] eq $OWNER_ID} selected {elseif $OWNER_ID eq "上海珍岛信息技术有限公司"}selected{/if}">{$OWNER_NAME}</option>
                                    {/foreach}
                                </optgroup>
                            </select>
                        </td>
                        <td>
                            <div class="input-append">
                                <input name="bili[]" type="text" placeholder = "请输入比例" class="scaling" value="{$divide_data['scalling']}" ><span class="add-on">%</i></span></div></td>
                            <td class="muted pull-right marginRight10px">
                           {if $divide_key neq '0'} <b><button class="btn btn-small deletefallinto" type="button"><i class=" icon-trash"></i></button></b>{/if}
                        </td>
                    </tr>
                {/foreach}
            {/if}
            </tbody>
        </table>

{/strip}
