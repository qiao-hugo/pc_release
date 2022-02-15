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



	{assign var="COUNTINUFIELDS" value=['eleccontracttpl','eleccontractid','relatedattachment','file']}
	{assign var="CONTRACTATTRIBUTE" value={$RECORD->get('contractattribute')}}
	{assign var="ELECCONTRACT" value=['originator','originatormobile','elereceiver','elereceivermobile','eleccontractstatus','eleccontracttpl','relatedattachment','contractattribute','clientproperty','eleccontracttplid','eleccontractid','relatedattachmentid']}
	{foreach key=BLOCK_LABEL_KEY item=FIELD_MODEL_LIST from=$RECORD_STRUCTURE}
	{assign var=BLOCK value=$BLOCK_LIST[$BLOCK_LABEL_KEY]}
	{if $BLOCK eq null or $FIELD_MODEL_LIST|@count lte 0}{continue}{/if}
	{if $BLOCK_LABEL_KEY eq 'LBL_EXTRAPRODUCT' or $BLOCK_LABEL_KEY eq 'LBL_CUSTOM_INFORMATION'}{continue}{/if}
	{if $BLOCK_LABEL_KEY eq 'ELECCONTRACT_INFO' AND $SIGNATURETYPE neq 'eleccontract'}{continue}{/if}
	{if $BLOCK_LABEL_KEY eq 'CONTRACT_PHASE_SPLIT'}
		{include file=vtemplate_path('ROWDetailViewBlockView.tpl','ServiceContracts') BLOCK_FIELDS=$FIELD_MODEL_LIST INITNUM=1}
		{continue}
	{/if}
	{assign var=IS_HIDDEN value=$BLOCK->isHidden()}
	{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
	<input type=hidden name="timeFormatOptions" data-value='{$DAY_STARTS}' />
	<input type=hidden name="oldstage" data-value='{$ISSTAGE}' value="{$ISSTAGE}" />
	<input type=hidden name="isfenfile" data-value='{$ISFENQIFILE}' value="{$ISFENQIFILE}" />
	<input type=hidden name="maxhandlecontractnum" data-value='{$CANHANDLECONTRACTNUM}' value="{$CANHANDLECONTRACTNUM}" />

	<input type=hidden name="modulestatus" data-value='{$MODULESTATUS}' value="{$MODULESTATUS}" />
		{if $TAB_LABEL eq '服务合同 详细内容' && !$accountMoneyArray['sideagreement']}
			<div class="relatedContents contents-bottomscroll">
				<div class="bottomscroll-div">
					<table class="table table-bordered listViewEntriesTable">
						<thead>
						<tr class="listViewHeaders">
							<th class="medium" colspan="2"><a href="javascript:void(0); ">回款概要</a></th>
							<th class="medium" colspan="2"><a href="javascript:void(0);" class="pull-right" style="color: #02A7F0" id="receRefresh">刷新</a></th>
						</tr>
						</thead>
						<tbody>
						<tr class="listViewEntries">
							<td class="fieldLabel medium" style="width: 200px"><label class="muted pull-right marginRight10px">合同总金额</label></td>
							<td class="fieldValue medium" style="width: 200px"><span class="value" id="totalMoney">{$accountMoneyArray['paymentTotal']}</span></td>
							<td class="fieldLabel medium" style="width: 200px"><label class="muted pull-right marginRight10px">已回款金额</label></td>
							<td class="fieldValue medium" style="width: 200px"><span class="value" id="receivedMoney">{$accountMoneyArray['paymentReceived']}</span></td>
						</tr>
						<tr class="listViewEntries">
							<td class="fieldLabel medium" style="width: 200px"><label class="muted pull-right marginRight10px">剩余未回款金额</label></td>
							<td class="fieldValue medium" style="width: 200px"><span class="value" id="remainMoney">{$accountMoneyArray['paymentElse']}</span></td>
							<td class="fieldLabel medium" style="width: 200px"><label class="muted pull-right marginRight10px">剩余分期付款最低可回款金额</label></td>
							<td class="fieldValue medium" style="width: 200px"><span class="value" id="lowestMoney">{$accountMoneyArray['leastPayMoney']}</span></td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>
		{elseif $accountMoneyArray['sideagreement']}
			<div class="relatedContents contents-bottomscroll">
				<div class="bottomscroll-div">
					<table class="table table-bordered listViewEntriesTable">
						<thead>
						<tr class="listViewHeaders">
							<th class="medium"><a href="javascript:void(0); ">回款概要</a></th>
						</tr>
						</thead>
						<tbody>
						<tr class="listViewEntries">
							<td style="text-align: center">补充协议无回款概要</td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>

		{/if}
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
			{if $SIGNATURETYPE neq 'eleccontract' && in_array($FIELD_MODEL->getFieldName(),$ELECCONTRACT)}{continue}{/if}
			{if in_array($FIELD_MODEL->getFieldName(),$COUNTINUFIELDS)}{continue}{/if}
			{if $FIELD_MODEL->getFieldName() eq 'eleccontracttplid' && $CONTRACTATTRIBUTE eq 'customized'}{continue}{/if}
			{if !$FIELD_MODEL->isViewableInDetailView() or $FIELD_MODEL->getName() eq 'workflowsid'}
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
						{if $FIELD_MODEL->getName() neq 'isconfirm' or ($FIELD_MODEL->getName() eq 'isconfirm' and $FIELD_MODEL->get('fieldvalue') eq 0)}
						 {if $FIELD_MODEL->getName() eq 'eleccontracttplid'}
							 {$RECORD->get('eleccontracttpl')}
						 {elseif $FIELD_MODEL->getName() eq 'elatedattachmentid'}
							 {$RECORD->get('relatedattachment')}
						 {else}
							{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME RECORD=$RECORD}
						 {/if}
						 {else}
                             {assign var=CONFIRM value=explode('##',$RECORD->entity->column_fields['confirmvalue'])}
                             {assign var=TEMP value=[]}
                             {foreach item=TEMPV from=$CONFIRM}
                                 {assign var=TEMPVE value=explode(',',$TEMPV)}
                                {$TEMP[]='<span style="width:100px;display:inline-block;overflow:hidden;"><i class="icon-user"></i>'|CAT:$TEMPVE[0]:'</span><i class="icon-time"></i>':$TEMPVE[1]}
                             {/foreach}
                             <i class="icon-th-list alignMiddle" title="审查详情" data-container="body" data-toggle="popover" data-placement="right" data-content='{implode('<br>',$TEMP)}'></i>
                         {/if}
					 </span>
                     {*assign var=FIELDAJAX value=array('contract_type','productcategory','Receiveid','modulestatus')*}
					 {if $IS_EDITACCOUNT && $FIELD_MODEL->getName() eq 'sc_related_to'}
					 <span class="hide edit">
                         {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME}
						 <input type="hidden" class="fieldname" value='{$FIELD_MODEL->get('name')}' data-prev-value='{Vtiger_Util_Helper::toSafeHTML($FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue')))}' />
					 </span>
					 {/if}
                     {assign var=FIELDAJAX value=array('multitype', 'invoicecompany',"contractstate",'isautoclose','effectivetime','billcontent','total','remark')}
					  {if $FIELD_MODEL->isEditable() eq 'true' && ($FIELD_MODEL->getFieldDataType()!=Vtiger_Field_Model::REFERENCE_TYPE) && $FIELD_MODEL->isAjaxEditable() eq 'true' && in_array($FIELD_MODEL->getName(),$FIELDAJAX)}
						 <span class="hide edit">
							 {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME}
                             {if $FIELD_MODEL->getFieldDataType() eq 'multipicklist'}
                                <input type="hidden" class="fieldname" value='{$FIELD_MODEL->get('name')}[]' data-prev-value='{$FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue'))}' />
                             {else}
                                 <input type="hidden" class="fieldname" value='{$FIELD_MODEL->get('name')}' data-prev-value='{Vtiger_Util_Helper::toSafeHTML($FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue')))}' />
                             {/if}
						 </span>
					 {/if}

				 </td>
			 {/if}

		{if $FIELD_MODEL_LIST|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype" and $FIELD_MODEL->get('uitype') neq "69" and $FIELD_MODEL->get('uitype') neq "105"}
			<td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
		{/if}
		{/foreach}
		</tr>
		{if $BLOCK_LABEL_KEY eq 'LBL_SERVICE_CONTRACT_INFORMATION'}
			<td class="fieldLabel medium" id="ServiceContracts_detailView_fieldLabel_account_zizhi_file"><label class="muted pull-right marginRight10px">客户资质附件</label></td>
			<td class="fieldValue medium" id="ServiceContracts_detailView_fieldValue_account_zizhi_file">
				<span class="value" data-field-type="FileUpload" id="FileUpload">
				</span>
			</td>
		{/if}
		</tbody>
	</table>
	<br>
	{/foreach}
	<div class="widgetContainer_servicecontract" data-url="module=ServiceContracts&view=Detail&mode=getProducts&amp;record={$RECORD->getId()}" data-name="Workflows"><div class="widget_contents"></div></div>
<br/>
<br/>
    <table class="table table-bordered blockContainer showInlineTable  detailview-table" id = "fallintotable">
        <thead>
        <tr>
            <th class="blockHeader" colspan="4" >
                合同分成信息
				{if $PERMISSIONS == 'true'}
                    {if $CONTRACTS_DIVIDE_1|@count eq 0}
                        <span style="float: right; " ><a id="divided_modification" class="btn label label-info" style="outline:none;border:none" >分成修改申请</a></span>
                    {/if}
                {/if}
            </th>
        </tr>
        </thead>
        <tbody>
        <tr><td><b>所属公司</b></td><td><b>业绩所属人</b></td><td><b>比例</b></td>
        {foreach from=$CONTRACTS_DIVIDE item="divide_data" key="divide_key"}
            <tr >
                <td>{$divide_data['owncompanys']}</td>
                <td>{$divide_data['receivedpaymentownname']}</td>
                <td>{$divide_data['scalling']}%</td>
            </tr>
        {/foreach}
        </tbody>
    </table>

    {if $CONTRACTS_DIVIDE_1|@count neq 0}
        <div style="position:relative;">
                <div style='margin:0 auto; top: 50%;right:50%;position: absolute;border:1px solid red;width:60px;text-align:center;color:red;border-radius:5px;font-size:24px;transform: rotate(40deg);-o-transform: rotate(40deg);-webkit-transform: rotate(40deg);-moz-transform: rotate(40deg);filter:progid:DXImageTransform.Microsoft.BasicImage(Rotation=2);'>分成审批</div>
        <table class="table table-bordered blockContainer showInlineTable  detailview-table" id = "fallintotable_1_1"> <!-- 合同分成审批 -->
            <thead>
            <tr>
                <th class="blockHeader" colspan="4">
                    合同分成信息
                </th>
            </tr>
            </thead>
            <tbody>

            <tr><td><b>所属公司</b></td><td><b>业绩所属人</b></td><td><b>比例</b></td>
                {foreach from=$CONTRACTS_DIVIDE_1 item="divide_data" key="divide_key"}
                    <tr>
                        <td>{$divide_data['owncompanys']}</td>
                        <td>{$divide_data['receivedpaymentownname']}</td>
                        <td>{$divide_data['scalling']}%</td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
              </div>
    {/if}


        <!--     start 分割线    -------------------------------------------------->
  {*      <div id="show_tab_divided" style="display: none;">
            <table class="table table-bordered blockContainer showInlineTable  detailview-table blockContainer_tab blockContainer_tab_1" id = "fallintotable_2"  >
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
                        <td><b>所属公司</b></td>
                        <td><b>业绩所属人</b></td>
                        <td><b>比例</b></td>
                        <td class="muted pull-right marginRight10px"><b><button class="btn btn-small" type="button" id="addfallinto_1" data-num="0"><i class=" icon-plus"></i></button></b></td>
                    </tr>

                    </tbody>
                </table>
        </div>  *}
            <!--      end  分割线    -------------------------------------------------->

    <script>
		{literal}
		$(function (){
			$("[data-toggle='popover']").popover();
        });
		{/literal}
        var divideUserId = {$USER_MODEL->get('id')};
		var staffList;
	</script>
    	<script type="text/javascript">
        var accessible_users = "<select id=\"ddddd\" class=\"chzn-select\" name=\"reportsower\"> <optgroup label=\"{vtranslate('LBL_USERS')}\">     {foreach key=OWNER_ID item=OWNER_NAME from=$ACCESSIBLE_USERS} <option value=\"{$OWNER_ID}\" data-picklistvalue= '{$OWNER_ID}' {if $FIELD_VALUE eq $OWNER_ID} selected {/if} data-userId=\"{$CURRENT_USER_ID}\">{$OWNER_NAME}</option> {/foreach} </optgroup>    </select>";
	</script>
{/strip}
