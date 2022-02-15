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
		<input type="hidden" name="old_vendorid" value="{$VENDORID}" />
		<input type="hidden" name="limitprice" value="" />
		<input type="hidden" name="compareprice" value="" />
		<input type="hidden" name="ismultiple" value="{$MULTIPLE}" />
		<input type="hidden" name="oldpayapplyids" value="{$PAYAPPLYIDS}" />
		<input type="hidden" name="current_modulestatus" value="{$MODULESTATUS}" />
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
			<table class="mytable table table-bordered blockContainer showInlineTable {if in_array($BLOCK_LABEL,array('LBL_ADV','LBL_COMPARE_INFO'))}hide tableadv{/if} detailview-table"
				   {if $BLOCK_LABEL=='LBL_BANK_INFO'}id="bankinfo"{/if}
				   {if $BLOCK_LABEL=='LBL_COMPARE_INFO'}id="compareinfo"{/if}>
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
				{if $FIELD_MODEL->getFieldName()== 'actualeffectivetime'}{continue}{/if}
				{if $FIELD_MODEL->get('name') eq 'residualamount' || $FIELD_MODEL->get('name') eq 'amountpaid'}{continue}{/if}
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
					{if $isReferenceField neq "reference" || in_array($FIELD_MODEL->get('label'),array('bankaccount','vendors','bankname','banknumber'))}<label class="muted pull-right marginRight10px">{/if}
						{if $FIELD_MODEL->isMandatory() eq true && $isReferenceField neq "reference"} <span class="redColor">*</span>
						{elseif in_array($FIELD_MODEL->get('label'),array('bankaccount','vendors','bankname','banknumber'))}<span class="redColor hide {$FIELD_MODEL->get('label')}">*</span>
						{/if}
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
                                <font color="red">没有相对应的项目信息!</font>
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
                                <font color="red">没有相对应的额外项目信息!111</font>
                            {else}*}
                                {assign var=EXTRAPRODUCT value=explode(',',$FIELD_MODEL->get('fieldvalue'))}

                                {foreach from=$RECORD_ALLEPRODUCTID item=extraValue key=constactKey}
                            <div style="line-height: 30px;float: left;width: 260px; border: 1px solid  rgba(57, 15, 40, 0.18); margin: 2px;  border-radius: 5px;padding-bottom:5px;">
                                    <label class="checkbox inline">
                                        <input type="checkbox"
                                               {if in_array($extraValue['productid'],$EXTRAPRODUCT)}checked {/if}
                                               value="{$extraValue['productid']}" name="extraproductid[]" data-name="extraproductid" class="extraproductid entryCheckBox" >
                                        &nbsp;{$extraValue['productname']}

                                    </label>
                                </div>
                                {/foreach}

                                {*<input name="{$FIELD_MODEL->getFieldName()}" type="hidden" value="{$FIELD_MODEL->get('fieldvalue')}" data-multiple="{$FIELD_MODEL->get('ismultiple')}" class="sourceField" data-displayvalue='{$FIELD_MODEL->getEditViewDisplayValue($FIELD_MODEL->get('fieldvalue'))}' data-fieldinfo='{$FIELD_INFO}' />*}

                            {*{/if}*}
                        </td>
                    {else}
                    <td class="fieldValue {$WIDTHTYPE}" {if $FIELD_MODEL->get('uitype') eq '19'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if} {if $FIELD_MODEL->get('uitype') eq '20'} colspan="3"{/if} {if in_array($FIELD_MODEL->get('name'),array('soncate','parentcate'))}id="{$FIELD_MODEL->get('name')}"{/if}>
						{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) BLOCK_FIELDS=$BLOCK_FIELDS}
						{if $FIELD_MODEL->get('name') eq 'invoicecompany'}
							<br>
							<span style="color: red">如果合同主体错误需作废重新提单，请谨慎填写</span>
						{/if}
					</td>
                   {/if}
				{/if}
				{if $BLOCK_FIELDS|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype"}
					<td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
				{/if}
			{/foreach}
			</tr>
			{if $BLOCK_LABEL=='LBL_CATE_INFO'}
			<tr>
				<td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 支出申请名称</label></td>
				<td class="fieldValue medium" colspan="4">
					<div class="row-fluid">
							<span class="span10" id="applylist">
							<select style="width: 87%" class="input-large product_select chzn-select " data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="payapplyids">
								{*{foreach from=$PRODUCT_DATA item=V}*}
								{*<option {if $V['productid'] eq $VALUE['productid']}selected{/if} value="{$V['productid']}">{$V['productname']}</option>*}
								{*{/foreach}*}
							</select>
							</span>
					</div>
				</td>
			</tr>
			{/if}
			</tbody>
			</table>

			<br>

		{/foreach}
		<div id="vendorsrebate">
			<table class="table table-bordered blockContainer  detailview-table">
				<thead>
				<tr>
					<th class="blockHeader" colspan="4">
						<img class="cursorPointer alignMiddle blockToggle hide " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;"><img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">&nbsp;&nbsp;项目返点 <b class="pull-right"><button class="btn btn-small" type="button" id="add_vendorsrebate"><i class="icon-plus" title="点击添加项目返点信息"></i></button>
						</b>
					</th>
				</tr>
				</thead>
			</table>

            {foreach from=$VENDORSREBATEDATA item=VALUE key=INDEX}
				<table class="table table-bordered blockContainer Duplicates showInlineTable detailview-table" data-num="{$INDEX+1}">
					<thead>
					<tr>
						<th class="blockHeader" colspan="4">
							&nbsp;&nbsp;项目返点[{$INDEX+1}]<b class="pull-right"><button class="btn btn-small delbutton" type="button" data-id="yesreplace"><i class="icon-trash" title="项目返点删除"></i></button></b>
						</th>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td class="fieldLabel medium">
							<label class="muted pull-right marginRight10px"><span class="redColor">*</span> 项目</label>
						</td>
						<td class="fieldValue medium">
							<div class="row-fluid">
							<span class="span10">
							<select class="input-large product_select chzn-select" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="productid[{$INDEX+1}]" data-id="{$INDEX+1}">
								{foreach from=$PRODUCT_DATA item=V}
									<option {if $V['productid'] eq $VALUE['productid']}selected{/if} value="{$V['productid']}">{$V['productname']}</option>
                                {/foreach}
							</select>
							<input type="hidden" name="updatei[{$INDEX+1}]" value="{$VALUE['vendorsrebateid']}" data-id="yesreplace"></span><input type="hidden" class="t_productname" name="productname[{$INDEX+1}]" value="{$VALUE['productname']}" data-id="{$INDEX+1}">
							</div>
						</td>
						<td class="fieldLabel medium">
							<label class="muted pull-right marginRight10px"><span class="redColor">*</span> 返点比例</label>
						</td>
						<td class="fieldValue medium">
							<div class="row-fluid">
								<span class="span10"><input type="text" class="input-large product_rebate" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="rebate[{$INDEX+1}]" data-id="{$INDEX+1}" value="{$VALUE['rebate']}"></span>
							</div>
						</td>
					</tr>
					<tr>
						<td class="fieldLabel medium">
							<label class="muted pull-right marginRight10px"><span class="redColor">*</span> 生效时间</label>
						</td>
						<td class="fieldValue medium">
							<div class="row-fluid">
								<span class="span10"><input type="text" class="input-large dateField t_date" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="effectdate[{$INDEX+1}]" data-id="{$INDEX+1}" value="{$VALUE['effectdate']}" data-date-format="yyyy-mm-dd" readonly="readonly"></span>
							</div>
						</td>
						<td class="fieldLabel medium">
							<label class="muted pull-right marginRight10px"><span class="redColor"></span> 失效时间</label>
						</td>
						<td class="fieldValue medium">
							<div class="row-fluid">
								<span class="span10"><input type="text" class="input-large dateField  t_date" name="enddate[{$INDEX+1}]" data-id="{$INDEX+1}" value="{$VALUE['enddate']}" data-date-format="yyyy-mm-dd" readonly="readonly"></span>
							</div>
						</td>
					</tr>
					<tr>
						<td class="fieldLabel medium">
							<label class="muted pull-right marginRight10px"><span class="redColor">*</span> 返点类型</label>
						</td>
						<td class="fieldValue medium">
							<div class="row-fluid">
								<span class="span10">
									<select class="input-large product_select chzn-select" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="rebatetype[{$INDEX+1}]" data-id="{$INDEX+1}">

										<option {if $VALUE['rebatetype'] eq 'CashBack'}selected{/if} value="CashBack">{vtranslate('CashBack', $MODULE)}</option>
										<option {if $VALUE['rebatetype'] eq 'GoodsBack'}selected{/if} value="GoodsBack">{vtranslate("GoodsBack", $MODULE)}</option>
									</select>
								</span>
							</div>
						</td>
						<td class="fieldLabel medium">
						</td>
						<td class="fieldValue medium">
						</td>
					</tr>
					<tr>
						<td class="fieldLabel medium">
							<label class="muted pull-right marginRight10px"><span class="redColor"></span> 返点说明</label>
						</td>
						<td class="fieldValue medium" colspan="3">
							<div class="row-fluid">
								<span class="span10"><textarea name="vexplain[{$INDEX+1}]" data-id="{$INDEX+1}" class="span11">{$VALUE['vexplain']}</textarea></span>
							</div>
						</td>
					</tr>
					</tbody>
				</table>
            {/foreach}
			<script type="text/javascript">
                var vendorsrebate_html = '<table class="table table-bordered blockContainer Duplicates showInlineTable detailview-table" data-num="yesreplace"><thead><tr><th class="blockHeader" colspan="4">&nbsp;&nbsp;项目返点 <b class="pull-right"><button class="btn btn-small delbutton" type="button" data-id="yesreplace"><i class="icon-trash" title="项目返点删除"></i></button></b></th></tr></thead><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span> 项目</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><select class="input-large product_select chzn-select" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="productid[]" data-id="yesreplace">reg_select_html</select><input type="hidden" name="inserti[]" value="yesreplace" data-id="yesreplace"></span><input type="hidden" class="t_productname" name="productname[]" data-id="yesreplace"></div></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span> 返点比例</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="text" class="input-large" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="rebate[]" data-id="yesreplace" value=""></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span> 生效时间</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="text" class="input-large t_date" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="effectdate[]" data-id="yesreplace" value="" readonly="readonly"></span></div></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span> 失效时间</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="text" class="input-large t_date" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="enddate[]" data-id="yesreplace" value="" readonly="readonly"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span> 返点类型</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><select class="input-large product_select chzn-select" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="rebatetype[]" data-id="yesreplace"><option value="CashBack">{vtranslate('CashBack', $MODULE)}</option><option  value="GoodsBack">{vtranslate('GoodsBack', $MODULE)}</option></select></span></div></td><td class="fieldLabel medium"></td><td class="fieldValue medium"></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor"></span> 返点说明</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><textarea name="vexplain[]" data-id="yesreplace" class="span11" value=""></textarea></span></div></td></tr></tbody></table>';
                var product_html = '<option value="">请选择</option>{foreach from=$PRODUCT_DATA item=value}<option value="{$value['productid']}">{$value['productname']}</option>{/foreach}';
			</script>
		</div>
        <div class="widgetContainer_servicecontractproducts" data-url="module=Workflows&amp;view=Detail&amp;mode=getWorkflowsContent&amp;record=" data-name="Workflows">
            <div class="widget_contents"> </div>
        </div>
        <script>
            var aaaaa="<tr ><td><select  class=\"chzn-select\" name=\"suoshugongsi\[\]\"> <optgroup label=\"{vtranslate('LBL_USERS')}\">{foreach key=OWNER_ID item=OWNER_NAME from=$OWNCOMPANY}<option value=\"{$OWNER_ID}\" data-picklistvalue= '{$OWNER_NAME}' {if $FIELD_VALUE eq $OWNER_ID} selected {elseif $OWNER_ID eq "上海珍岛信息技术有限公司"}selected{/if}  	data-userId=\"{$CURRENT_USER_ID}\">{$OWNER_NAME} </option> {/foreach} </optgroup> </select> </td><td> {if $FIELD_VALUE eq ''}{assign var=FIELD_VALUE value=$USER_MODEL->get('id')}{/if}	<select class=\"chzn-select\" name=\"suoshuren\[\]\"> <optgroup label=\"{vtranslate('LBL_USERS')}\"> 	{foreach key=OWNER_ID item=OWNER_NAME from=$ACCESSIBLE_USERS} <option value=\"{$OWNER_ID}\" data-picklistvalue= '{$OWNER_ID}' {if $FIELD_VALUE eq $OWNER_ID} selected {/if} data-userId=\"{$CURRENT_USER_ID}\">{$OWNER_NAME}</option> {/foreach} </optgroup>	</select></td><td>	<div class=\"input-append\"> <input name=\"bili\[\]\" type=\"text\" placeholder = \"请输入比例\" class=\"scaling\" ><span class=\"add-on\">%</i></span></div></td><td class=\"muted pull-right marginRight10px\"> <b><button class=\"btn btn-small deletefallinto\" type=\"button\"><i class=\" icon-trash\"></i></button></b> </td> </tr>";
        </script>
{/strip}