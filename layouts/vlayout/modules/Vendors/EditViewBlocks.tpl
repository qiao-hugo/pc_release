{*<!--
/*******************************
   * 编辑或新增通用字段显示模版
  *编辑下不可编辑字段也要显示
  *增加只读字段 可新增不可编辑
  *文本域展一行 uitype 19/20
  {if !empty($PICKIST_DEPENDENCY_DATASOURCE)}<input type="hidden" name="picklistDependency" value='{Vtiger_Util_Helper::toSafeHTML($PICKIST_DEPENDENCY_DATASOURCE)}' />{/if}
 ********/
-->*}
{strip}
<div class='container-fluid editViewContainer'>
	<form class="form-horizontal recordEditView" id="EditView" name="EditView" method="post" action="index.php" enctype="multipart/form-data">
		{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
		{assign var=QUALIFIED_MODULE_NAME value={$MODULE}}
		{assign var=IS_PARENT_EXISTS value=strpos($MODULE,":")}
		{if $IS_PARENT_EXISTS}
			{assign var=SPLITTED_MODULE value=":"|explode:$MODULE}
			<input type="hidden" name="module" value="{$SPLITTED_MODULE[1]}" />
			<input type="hidden" name="parent" value="{$SPLITTED_MODULE[0]}" />
		{else}
			<input type="hidden" name="module" value="{$MODULE}" />
		{/if}
		<input type="hidden" name="action" value="Save" />
		<input type="hidden" name="record" value="{$RECORD_ID}" />
		<input type="hidden" name="defaultCallDuration" value="{$USER_MODEL->get('callduration')}" />
		<input type="hidden" name="defaultOtherEventDuration" value="{$USER_MODEL->get('othereventduration')}" />
		{if $IS_RELATION_OPERATION }
			<input type="hidden" name="sourceModule" value="{$SOURCE_MODULE}" />
			<input type="hidden" name="sourceRecord" value="{$SOURCE_RECORD}" />
			<input type="hidden" name="relationOperation" value="{$IS_RELATION_OPERATION}" />
		{/if}
		<div class="contentHeader row-fluid">
		{assign var=SINGLE_MODULE_NAME value='SINGLE_'|cat:$MODULE}
		{if $RECORD_ID neq ''}
			<h3 class="span8 textOverflowEllipsis" title="{vtranslate('LBL_EDITING', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)} {$RECORD_STRUCTURE_MODEL->getRecordName()}">{vtranslate('LBL_EDITING', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)} - {$RECORD_STRUCTURE_MODEL->getRecordName()}</h3>
		{else}
			<h3 class="span8 textOverflowEllipsis">{vtranslate('LBL_CREATING_NEW', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)}</h3>
		{/if}
			<span class="pull-right">
				<button class="btn btn-success" type="submit"><strong>{vtranslate('LBL_SAVE', $MODULE)}</strong></button>
				<a class="cancelLink" type="reset" onclick="javascript:window.history.back();">{vtranslate('LBL_CANCEL', $MODULE)}</a>
			</span>
		</div>
		{foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE name="EditViewBlockLevelLoop"}
			{if $BLOCK_FIELDS|@count lte 0}{continue}{/if}
			<table class="table table-bordered blockContainer showInlineTable">
			<tr>
				<th class="blockHeader" colspan="4">{vtranslate($BLOCK_LABEL, $MODULE)}
				{if $BLOCK_LABEL eq 'LBL_VENDOR_ADDRESS_INFORMATION'}
					<b class="pull-right">
						<button class="btn btn-small addbank" type="button">
							<span>+银行账号</span>
						</button></b>
					{/if}
				</th>
			</tr>
			<tr>
			{assign var=COUNTER value=0}
			{foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}

				{assign var="isReferenceField" value=$FIELD_MODEL->getFieldDataType()}
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
									<select id="{$MODULE}_editView_fieldName_{$FIELD_MODEL->getName()}_dropDown" class="chzn-select referenceModulesList streched" style="width:140px;">
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
							{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) COUNTER=$COUNTER MODULE=$MODULE}
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
							<span class="span10" {if $FIELD_MODEL->getFieldName() eq "bankaccount"} style="min-width: 500px" {/if}>
								{if $FIELD_MODEL->get('uitype') eq "15"}
									{assign var="FIELD_INFO" value=Zend_Json::encode($FIELD_MODEL->getFieldInfo())}
									{assign var=PICKLIST_VALUES value=$FIELD_MODEL->getPicklistValues()}
									{assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
									{assign var="FIELDNAME" value={$FIELD_MODEL->getFieldName()}}

									<select class="chzn-select" name="{$FIELDNAME}" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo='{$FIELD_INFO|escape}' {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if} data-selected-value='{$FIELD_MODEL->get('fieldvalue')}'>
											{if $FIELD_MODEL->isEmptyPicklistOptionAllowed()}<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>{/if}

										{foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$PICKLIST_VALUES}

									        <option {if $FIELDNAME eq 'vendorstate' &&  $PICKLIST_NAME eq 'not_approval' && $RECORD_ID eq null}selected{/if} value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}" {if trim(decode_html($FIELD_MODEL->get('fieldvalue'))) eq trim($PICKLIST_NAME)} selected {/if}     {if $FIELDNAME eq 'accountrank' && isset($RANKLIMIT) }{if !$RANKLIMIT[$PICKLIST_NAME]} disabled="true"{/if}{/if}>{$PICKLIST_VALUE}

									        
									        </option>

									    {/foreach}
									</select>

								{else}
									{if $FIELD_MODEL->get('uitype') eq "1" && $FIELD_MODEL->getFieldName() eq "mainplatform"}
										<select id="mainplatform_select"  multiple style="width: 224px; {if $IS_VENDORTYPE neq 1}display: none;{/if}" class=" chzn-select" name="mainplatform[]">
											{foreach from=$RECHARGEPLATFORM_DATA key=PLATFORM_KEY item=PLATFORM}
												<option {if in_array($PLATFORM_KEY, $SELECT_MAINPLATFORM)}selected{/if} value="{$PLATFORM_KEY}">{$PLATFORM}</option>
											{/foreach}
										</select>
										<input id="mainplatform_display" value="{$FIELD_MODEL->get('fieldvalue')}" name="mainplatform" type="text" {if $IS_VENDORTYPE eq 1}disabled{/if} {if $IS_VENDORTYPE eq 1}style="display: none;"{/if} />
										
									{else}
										{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) BLOCK_FIELDS=$BLOCK_FIELDS}
									{/if}
								{/if}
								{if $FIELD_MODEL->getFieldName() eq "bankaccount"}
									<font color="red" style="margin-left: 30px;">示例：xxx银行xxx分行 或 xxx银行xxx支行</font>
								{/if}
							</span>
						</div>
						{/if}
					</td>
				{/if}
				{if $BLOCK_FIELDS|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype"}
					<td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
				{/if}
				{if $MODULE eq 'Events' && $BLOCK_LABEL eq 'LBL_EVENT_INFORMATION' && $smarty.foreach.blockfields.last }
					{include file=vtemplate_path('uitypes/FollowUp.tpl',$MODULE) COUNTER=$COUNTER}
				{/if}
			{/foreach}
			</tr>
			</table>
			<br>
            {if $BLOCK_LABEL eq 'LBL_VENDOR_ADDRESS_INFORMATION'}
				<div id="bankstable">
					{assign var="VENDORBANK" value=$RECORD->getVendorBank($RECORD_ID)}
					{if !empty($VENDORBANK)}
						{foreach from=$VENDORBANK item=VENDORBANKVALUE}
							<table class="table table-bordered blockContainer showInlineTable Duplicates" id="Duplicates{$VENDORBANKVALUE['vendorbankid']}" data-num="{$VENDORBANKVALUE['vendorbankid']}">
								<tbody>
								<tr><th class="blockHeader" colspan="4">银行账户信息<b class="pull-right"><button class="btn btn-small delbank" type="button"><span>-银行账号</span></button></b></th></tr>
								<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 开户行</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10" style="min-width: 500px"><input type="text" class="input-large " data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="mbankaccount[{$VENDORBANKVALUE['vendorbankid']}]" value="{$VENDORBANKVALUE['bankaccount']}" ><font color="red" style="margin-left: 30px;">示例：xxx银行xxx分行 或 xxx银行xxx支行</font></span></div></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 开户名</label></td>
									<td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="text" class="input-large " name="mbankname[{$VENDORBANKVALUE['vendorbankid']}]" value="{$VENDORBANKVALUE['bankname']}" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"></span></div></td></tr>
								<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 账  号</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="text" class="input-large " name="mbanknumber[{$VENDORBANKVALUE['vendorbankid']}]" value="{$VENDORBANKVALUE['banknumber']}" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"></span></div></td>
									<td class="fieldLabel medium"><label class="muted pull-right marginRight10px">银行代码</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="text" class="input-large " data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="mbankcode[{$VENDORBANKVALUE['vendorbankid']}]" value="{$VENDORBANKVALUE['bankcode']}" ></span></div></td>
								</tr></tbody></table>
						{/foreach}
					{/if}
				</div>
            {/if}
		{/foreach}


		<div id="vendorsrebate" class="hide">
		<table class="table table-bordered blockContainer  detailview-table">
            <thead>
            <tr>
                <th class="blockHeader" colspan="4">
                    <img class="cursorPointer alignMiddle blockToggle hide " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;"><img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">&nbsp;&nbsp;产品返点 <b class="pull-right"><button class="btn btn-small" type="button" id="add_vendorsrebate"><i class="icon-plus" title="点击添加产品返点信息"></i></button>
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
						&nbsp;&nbsp;产品返点[{$INDEX+1}]<b class="pull-right"><button class="btn btn-small delbutton" type="button" data-id="yesreplace"><i class="icon-trash" title="产品返点删除"></i></button></b>
					</th>
				</tr>
				</thead>
				<tbody>
				<tr>
					<td class="fieldLabel medium">
						<label class="muted pull-right marginRight10px"><span class="redColor">*</span> 产品</label>
					</td>
					<td class="fieldValue medium">
						<div class="row-fluid">
							<span class="span10">
							<select class="input-large product_select chzn-select" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="productid[{$INDEX+1}]" data-id="yesreplace">
								{foreach from=$PRODUCT_DATA item=V}
									<option {if $V['productid'] eq $VALUE['productid']}selected{/if} value="{$V['productid']}">{$V['productname']}</option>
								{/foreach}
							</select>
							<input type="hidden" name="updatei[{$INDEX+1}]" value="{$VALUE['vendorsrebateid']}" data-id="yesreplace"></span><input type="hidden" class="t_productname" name="productname[{$INDEX+1}]" value="{$VALUE['productname']}" data-id="yesreplace">
						</div>
					</td>
					<td class="fieldLabel medium">
						<label class="muted pull-right marginRight10px"><span class="redColor">*</span> 返点比例</label>
					</td>
					<td class="fieldValue medium">
						<div class="row-fluid">
							<span class="span10"><input type="text" class="input-large product_rebate" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="rebate[{$INDEX+1}]" data-id="yesreplace" value="{$VALUE['rebate']}"></span>
						</div>
					</td>
				</tr>
				<tr>
					<td class="fieldLabel medium">
						<label class="muted pull-right marginRight10px"><span class="redColor">*</span> 生效时间</label>
					</td>
					<td class="fieldValue medium">
						<div class="row-fluid">
							<span class="span10"><input type="text" class="input-large t_date" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="effectdate[{$INDEX+1}]" data-id="yesreplace" value="{$VALUE['effectdate']}"></span>
						</div>
					</td>
					<td class="fieldLabel medium">
						<label class="muted pull-right marginRight10px"><span class="redColor"></span> 失效时间</label>
					</td>
					<td class="fieldValue medium">
						<div class="row-fluid">
							<span class="span10"><input type="text" class="input-large t_date" name="enddate[{$INDEX+1}]" data-id="yesreplace" value="{$VALUE['enddate']}"></span>
						</div>
					</td>
				</tr>
				<tr>
					<td class="fieldLabel medium">
						<label class="muted pull-right marginRight10px"><span class="redColor"></span> 返点说明</label>
					</td>
					<td class="fieldValue medium" colspan="3">
						<div class="row-fluid">
							<span class="span10"><textarea name="vexplain[{$INDEX+1}]" data-id="yesreplace" class="span11">{$VALUE['vexplain']}</textarea></span>
						</div>
					</td>
				</tr>
				</tbody>
				</table>
        {/foreach}
        <script type="text/javascript">
        	var vendorsrebate_html = '<table class="table table-bordered blockContainer Duplicates showInlineTable detailview-table" data-num="yesreplace"><thead><tr><th class="blockHeader" colspan="4">&nbsp;&nbsp;产品返点 <b class="pull-right"><button class="btn btn-small delbutton" type="button" data-id="yesreplace"><i class="icon-trash" title="产品返点删除"></i></button></b></th></tr></thead><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span> 产品</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><select class="input-large product_select chzn-select" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="productid[]" data-id="yesreplace">reg_select_html</select><input type="hidden" name="inserti[]" value="yesreplace" data-id="yesreplace"></span><input type="hidden" class="t_productname" name="productname[]" data-id="yesreplace"></div></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span> 返点比例</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="text" class="input-large" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="rebate[]" data-id="yesreplace" value=""></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span> 生效时间</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="text" class="input-large t_date" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="effectdate[]" data-id="yesreplace" value="" readonly="readonly"></span></div></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor"></span> 失效时间</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="text" class="input-large t_date" name="enddate[]" data-id="yesreplace" value="" readonly="readonly"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor"></span> 返点说明</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><textarea name="vexplain[]" data-id="yesreplace" class="span11" value=""></textarea></span></div></td></tr></tbody></table>';
        	var product_html = '<option value="">请选择</option>{foreach from=$PRODUCT_DATA item=value}<option value="{$value['productid']}">{$value['productname']}</option>{/foreach}';
        </script>



        <!-- 
		<table class="table table-bordered blockContainer Duplicates showInlineTable  detailview-table" data-num="yesreplace">
            <thead>
                <tr>
                    <th class="blockHeader" colspan="4">&nbsp;&nbsp;产品返点[{$KEYINDEX+1}] <b class="pull-right">
                        <button class="btn btn-small delbuttonextend" type="button"  data-id="yesreplace">
                            <i class="icon-trash" title="删除财务数据"></i>
                        </button></b>
                    </th>
                </tr>
            </thead>
            <tbody>
            <tr>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 产品</label>
                </td>
                <td class="fieldValue medium">
                    <div class="row-fluid">
                        <span class="span10">
                        	<select class="input-large" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="productid[]" data-id="yesreplace" ><option>ss</option>
                        	</select>
                        	<input type="hidden" name="productname[]" data-id="yesreplace" >
                        </span>
                    </div>
                </td>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px"><span class="redColor">*</span> 返点比例</label>
                </td>
                <td class="fieldValue medium">
                    <div class="row-fluid">
                        <span class="span10">
                            <input type="text" class="input-large " data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="rebate[]" data-id="yesreplace" value="">
                        </span>
                    </div>
                </td>
            </tr>

            <tr>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 生效时间</label>
                </td>
                <td class="fieldValue medium">
                    <div class="row-fluid">
                        <span class="span10">
                            <input  type="text" class="input-large t_date" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="effectdate[]" data-id="yesreplace" value="">
                        </span>
                    </div>
                </td>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px"><span class="redColor"></span> 结束时间</label>
                </td>
                <td class="fieldValue medium">
                    <div class="row-fluid">
                        <span class="span10">
                            <input type="text" class="input-large t_date" name="enddate[]" data-id="yesreplace" value="">
                        </span>
                    </div>
                </td>
            </tr>

            </tbody>
        </table>

        -->
        </div>
{/strip}