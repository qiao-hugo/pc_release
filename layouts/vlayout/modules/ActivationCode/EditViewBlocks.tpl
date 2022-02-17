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
		<input type="hidden" name="receivetimeflag" value="{$OLD_RECORD['receivetimeflag']}" />
	    <input type="hidden" name="customerid" value="{$RECORD->get('customerid')}" />
	    <input type="hidden" name="oldusercode" value="{$RECORD->get('usercode')}" />
		{if $IS_RELATION_OPERATION }
			<input type="hidden" name="sourceModule" value="{$SOURCE_MODULE}" />
			<input type="hidden" name="sourceRecord" value="{$SOURCE_RECORD}" />
			<input type="hidden" name="relationOperation" value="{$IS_RELATION_OPERATION}" />
		{/if}
	    <input type="hidden" name="buyid" value="{$RECORD->get('buyid')}" />
		<div class="contentHeader row-fluid">
		{assign var=SINGLE_MODULE_NAME value='SINGLE_'|cat:$MODULE}
		{if $RECORD_ID neq ''}
			<h3 class="span8 textOverflowEllipsis" title="{vtranslate('LBL_EDITING', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)} {$RECORD_STRUCTURE_MODEL->getRecordName()}">{vtranslate('LBL_EDITING', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)} - {$RECORD_STRUCTURE_MODEL->getRecordName()}
			</h3>
		{else}
			<h3 class="span8 textOverflowEllipsis">{vtranslate('LBL_CREATING_NEW', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)}
			</h3>

		{/if}
		<br/>
		


			<span class="pull-right">
				<button class="btn btn-success" type="submit"><strong>{vtranslate('LBL_SAVE', $MODULE)}</strong></button>
				<a class="cancelLink" type="reset" onclick="javascript:window.history.back();">{vtranslate('LBL_CANCEL', $MODULE)}</a>
			</span>
		</div>

		{*<div style="font-size:20px; color: red; font-weight: bold; text-align: center; line-height: 20px; height: 50px;">使用扫二维码形式录入激活码，请确认当前输入法为英文（中文状态shift下就好）</div>
*}
		{foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE name="EditViewBlockLevelLoop"}
			{if $BLOCK_FIELDS|@count lte 0}{continue}{/if}
			<table class="table table-bordered blockContainer showInlineTable">
			<tr>
				<th class="blockHeader" colspan="4">{vtranslate($BLOCK_LABEL, $MODULE)}</th>
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
							<span class="span10">
								{if $FIELD_MODEL->get('name') eq 'productid'}
									<select id="{$MODULE}_editView_fieldName_{$FIELD_MODEL->getName()}_dropDown" class="chzn-select referenceModulesList streched" name="productid" >
										<optgroup>
											{foreach key=index item=value from=$TYUN_PRODUCT_LIST}
												<option value="{$value['productid']}" {if $FIELD_MODEL->get('fieldvalue') eq $value['productid']} selected {/if}>{$value['productname']}</option>
                                            {/foreach}
										</optgroup>
									</select>
								{else}
                                	{if $FIELD_MODEL->get('name') eq 'productlife'}
										<select id="{$MODULE}_editView_fieldName_{$FIELD_MODEL->getName()}_dropDown" class="chzn-select referenceModulesList streched" name="productlife">
										<optgroup>
											{if $RECORD->get('classtype') eq 'againbuy'}
												<option value="0" selected >无</option>
											{else}
												<option value="1" {if $FIELD_MODEL->get('fieldvalue') eq '1'} selected {/if}>1年</option>
												<option value="2" {if $FIELD_MODEL->get('fieldvalue') eq '2'} selected {/if}>2年</option>
												<option value="3" {if $FIELD_MODEL->get('fieldvalue') eq '3'} selected {/if}>3年</option>
												<option value="4" {if $FIELD_MODEL->get('fieldvalue') eq '4'} selected {/if}>4年</option>
												<option value="5" {if $FIELD_MODEL->get('fieldvalue') eq '5'} selected {/if}>5年</option>
												<option value="6" {if $FIELD_MODEL->get('fieldvalue') eq '6'} selected {/if}>6年</option>
												<option value="7" {if $FIELD_MODEL->get('fieldvalue') eq '7'} selected {/if}>7年</option>
												<option value="8" {if $FIELD_MODEL->get('fieldvalue') eq '8'} selected {/if}>8年</option>
												<option value="9" {if $FIELD_MODEL->get('fieldvalue') eq '9'} selected {/if}>9年</option>
												<option value="10" {if $FIELD_MODEL->get('fieldvalue') eq '10'} selected {/if}>10年</option>
											{/if}
										</optgroup>
									</select>
									{else}
                                        {if $FIELD_MODEL->get('name') eq 'classtype'}
											<select name="classtype" class="chzn-select referenceModulesList streched">
											<optgroup>
										        {if $FIELD_MODEL->get('fieldvalue') eq 'buy'}<option value="buy" selected>首购</option>{/if}
												{if $FIELD_MODEL->get('fieldvalue') eq 'upgrade'}<option value="upgrade" selected>升级</option>{/if}
                                                {if $FIELD_MODEL->get('fieldvalue') eq 'degrade'}<option value="degrade" selected>降级</option>{/if}
                                                {if $FIELD_MODEL->get('fieldvalue') eq 'renew'}<option value="renew" selected>续费</option>{/if}
                                                {if $FIELD_MODEL->get('fieldvalue') eq 'againbuy'}<option value="againbuy" selected>另购</option>{/if}
											</optgroup>
											</select>
                                        {else}
											{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE)}
                                        {/if}
                                    {/if}
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
		{/foreach}
	<table class="table table-bordered blockContainer Duplicates showInlineTable  detailview-table cls_tbl_buyservice">
		<thead>
		<tr>
			<th class="blockHeader" colspan="4">
				<img class="cursorPointer alignMiddle blockToggle  hide " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;"><img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">&nbsp;另购服务<b class="pull-right"><button class="btn btn-small" type="button" id="addBuyService"><i class="icon-plus" title="点击添加另购服务"></i></button></b></th>
		</tr>
		<tr>
			<th class="blockHeader" style="width: 50%">服务名称</th>
			<th class="blockHeader" style="width: 25%">数量</th>
			<th class="blockHeader" style="width: 25%">操作</th>
		</tr>
		<tbody>
        {if !empty($TYUN_BUY_SERVICE_LIST)}
			{foreach key=KEYINDEX item=DATA from=$TYUN_BUY_SERVICE_LIST}
				<tr data-num="{$KEYINDEX+1}">
					<td>
						<input type="hidden" name="buyindex[]" value="{$KEYINDEX+1}"/>
						<input type="hidden" id="tyunBuyCount{$KEYINDEX+1}" name="TyunBuyCount[{$KEYINDEX+1}]" value="{$DATA['Multiple']*$DATA['BuyCount']}" />
						<select  data-num="{$KEYINDEX+1}" class="chzn-select referenceModulesList streched select_buy_service" name="ServiceID[{$KEYINDEX+1}]" style="width:320px;">
							<optgroup>
								{foreach key=index item=value from=$TYUN_ALL_BUY_SERVICE_LIST}
									<option data-unit="{$value['Unit']}" data-multiple="{$value['Multiple']}" value="{$value['ServiceID']}" {if $value['ServiceID'] eq $DATA['ServiceID']} selected="selected"{/if}>{$value['ServiceName']}</option>
								{/foreach}
							</optgroup>
						</select>
					</td>
					<td>
						{*<input type="number" class="input-large" name="BuyCount[{$KEYINDEX+1}]" value="{$DATA['BuyCount']}" maxlength="2" step="1" max="10" ><span style="color: red;padding-left: 10px;font-weight: bold;" id="display_unit{$KEYINDEX+1}"></span>*}
						<select id="buycount{$KEYINDEX+1}" data-num="{$KEYINDEX+1}" class="chzn-select referenceModulesList streched select_buy_count" name="BuyCount[{$KEYINDEX+1}]" style="width:200px;">
								<option data-unit="{$DATA['Unit']}" value="1" tyun-value="{$DATA['Multiple']*1}" {if $DATA['BuyCount'] eq '1'} selected {/if}>1{$DATA['Unit']}</option>
								<option data-unit="{$DATA['Unit']}" value="2" tyun-value="{$DATA['Multiple']*2}" {if $DATA['BuyCount'] eq '2'} selected {/if}>2{$DATA['Unit']}</option>
								<option data-unit="{$DATA['Unit']}" value="3" tyun-value="{$DATA['Multiple']*3}" {if $DATA['BuyCount'] eq '3'} selected {/if}>3{$DATA['Unit']}</option>
								<option data-unit="{$DATA['Unit']}" value="4" tyun-value="{$DATA['Multiple']*4}" {if $DATA['BuyCount'] eq '4'} selected {/if}>4{$DATA['Unit']}</option>
								<option data-unit="{$DATA['Unit']}" value="5" tyun-value="{$DATA['Multiple']*5}" {if $DATA['BuyCount'] eq '5'} selected {/if}>5{$DATA['Unit']}</option>
								<option data-unit="{$DATA['Unit']}" value="6" tyun-value="{$DATA['Multiple']*6}" {if $DATA['BuyCount'] eq '6'} selected {/if}>6{$DATA['Unit']}</option>
								<option data-unit="{$DATA['Unit']}" value="7" tyun-value="{$DATA['Multiple']*7}" {if $DATA['BuyCount'] eq '7'} selected {/if}>7{$DATA['Unit']}</option>
								<option data-unit="{$DATA['Unit']}" value="8" tyun-value="{$DATA['Multiple']*8}" {if $DATA['BuyCount'] eq '8'} selected {/if}>8{$DATA['Unit']}</option>
								<option data-unit="{$DATA['Unit']}" value="9" tyun-value="{$DATA['Multiple']*9}" {if $DATA['BuyCount'] eq '9'} selected {/if}>9{$DATA['Unit']}</option>
								<option data-unit="{$DATA['Unit']}" value="10" tyun-value="{$DATA['Multiple']*10}" {if $DATA['BuyCount'] eq '10'} selected {/if}>10{$DATA['Unit']}</option>
						</select>
					</td>
					<td><i class="icon-trash deleteBuyService" title="删除另购服务" style="cursor: pointer" ></i></td>
				</tr>
			{/foreach}
        {/if}
		</tbody>
		</thead>
	</table>

{/strip}