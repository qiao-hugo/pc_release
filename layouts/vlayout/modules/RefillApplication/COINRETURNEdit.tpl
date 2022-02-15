
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
    <!--
    All final details are stored in the first element in the array with the index name as final_details
    so we will get that array, parse that array and fill the details
    -->
    {if $RECORD_ID<1}
        <div id="insertbefore"></div>
        <div style="float: right;height: 20px;" >
            <a class="button btn-info btn-primary" style="margin: 5px;" id="batchinput"><span style="padding: 5px;font-size: 14px;">明细批量导入(单次最多20条)</span></a>   <a href="/转入明细批量导入模板.xls" style="color: red;">批量导入模板下载</a>
        </div>
        <div style="display:none;">
            <input type="file" name="batchinput" accept="application/vnd.ms-excel" id="inputrefill" />
        </div>
    <table class="table table-bordered blockContainer showInlineTable detailview-table Duplicates inputs" data-num="1">
        <thead>
        <input type="hidden" name="truncashtype[1]" value="in">
        <tr><th class="blockHeader" colspan="4"><img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;"><img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">&nbsp;&nbsp;转入明细
                <b class="pull-right">
                    <button class="btn btn-small turncashin" type="button" data-type="in">
                        <span style="color:red;"><i class="icon-plus" title=""></i>转入</span>
                    </button></b>
            </th></tr></thead><tbody>
        <tr>
            <td class="fieldLabel " title=""><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> ID</label></td>
            <td class="fieldValue ">
                <input name="popupReferenceModule" type="hidden" value="RefillApplication" />
                <input name="mid[1]" type="hidden" value="" data-cid="1" data-multiple="0" class="sourceField" data-displayvalue='' data-fieldinfo="" autocomplete="off">
                <div class="row-fluid input-prepend input-append">
                    <span class="add-on clearReferenceSelection cursorPointer"><i id="RefillApplication_editView_fieldName_did_clear" class="icon-remove-sign" title="清除"></i></span>
                    <input id="mid_display[1]" readonly="readonly" name="mid_display[1]" type="text" class=" span7 	marginLeftZero autoComplete" value="" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="" placeholder="查找.." autocomplete="off">
                    <span data-id="RefillApplication_editView_fieldName_did_select" class="add-on relatedPopupDid cursorPointer"><i id="RefillApplication_editView_fieldName_did_select" data-id="RefillApplication_editView_fieldName_did_select" class="icon-search relatedPopupDid" title="选择"></i></span>
                </div>
            </td>
            <td class="fieldLabel " title=""><label class="muted pull-right marginRight10px">账户名称</label></td><td class="fieldValue "><input id="RefillApplication_editView_fieldName_accountzh" data-cid="1" type="text" class="input-large " data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="maccountzh[1]" value="" readonly="readonly" ></td>
        </tr>
        <tr>
            <td class="fieldLabel " title=""><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 充值平台</label></td>
            <td class="fieldValue "><input name="popupReferenceModule" data-cid="1" type="hidden" value="Products"><input name="mproductid[1]" type="hidden" value="" data-multiple="0" class="sourceField" data-displayvalue="" ><div class="row-fluid input-prepend input-append"><input id="mproductid_display[1]" name="mproductid_display[1]" type="text" class=" span7 	marginLeftZero autoComplete" value="" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  placeholder="查找.." readonly="readonly"></div>
            </td>
            <td class="fieldLabel " title="由充值ID带出"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 有无服务<span class="icon-question-sign"></span></label></td>
            <td class="fieldValue "><select class="chzn-select" data-cid="1" name="misprovideservice[1]" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" "><option value="">选择一个选项</option><option value="yes">有</option><option value="no">无</option></select></td>
        </tr>
        <tr>
            <td class="fieldLabel " title=""><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 客户返点类型</label></td>
            <td class="fieldValue "><select class="chzn-select" data-cid="1" name="maccountrebatetype[1]" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"><option value="">选择一个选项</option><option value="CashBack">返现</option><option value="GoodsBack">返货</option></select></td>
            <td class="fieldLabel " title="客户返点"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 返点<span class="icon-question-sign"></span></label></td>
            <td class="fieldValue "><input  type="text" data-cid="1" class="input-large " data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="mdiscount[1]" value="" readonly="readonly"  data-typecash="in"></td>
        </tr>
        <tr>
            <td class="fieldLabel " title=""><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 转入现金</label></td>
            <td class="fieldValue "><input type="text" data-cid="1" class="input-large " data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="mcashtransfer[1]" value="" readonly="readonly"  data-typecash="in"></td>
            <td class="fieldLabel " title=""><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 转入账户币</label></td>
            <td class="fieldValue "><input type="text" data-cid="1" class="input-large " data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="maccounttransfer[1]" value="" data-typecash="in"></td></tr></tbody>
    </table>
    {/if}
    <br>

    {assign var=COINRETURN value=array('servicecontractsid','accountid','file','remarks','did','accountzh','productid','isprovideservice','accountrebatetype','discount','totalcashtransfer','totalcashin','totalturnoverofaccount','totaltransfertoaccount','cashtransfer','accounttransfer')}

    {if $RECORD_ID>0}
        {assign var=STRINGCOINRETURN value=array('accountid','file','remarks','did','accountzh','productid','isprovideservice','accountrebatetype','discount','totalcashtransfer','totalcashin','totalturnoverofaccount','totaltransfertoaccount','cashtransfer')}
        {foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE name="EditViewBlockLevelLoop"}
            {if $BLOCK_FIELDS|@count lte 0}{continue}{/if}
            {if $BLOCK_LABEL neq 'LBL_CUSTOM_INFORMATION'}{continue}{/if}

        {foreach key=row_no item=data from=$C_RECHARGESHEET}
            {if $data['turninorout'] eq 'in' && $data['seqnum'] eq 1}<div id="insertbefore"></div>{assign var=CURRENTNUN value=1}
            {else}
                {assign var=CURRENTNUN value=$row_no+2}
            {/if}
            <table class="table table-bordered blockContainer showInlineTable detailview-table  Duplicates" data-num="{$CURRENTNUN}">
                <thead>
                <tr>
                    <th class="blockHeader" colspan="4">
                        <img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;"><img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">&nbsp;&nbsp;
                        {if $data['turninorout'] eq 'in'}
                            {if $data['seqnum'] eq 1}
                                转入
                            {else}
                                <span class="label label-a_normal">转入</span>
                            {/if}明细
                            {else}
                            <span class="label label-c_stamp">转出</span>明细
                        {/if}
                        <b class="pull-right">
                        {if $data['turninorout'] eq 'in' && $data['seqnum'] eq 1}
                            <button class="btn btn-small turncashin" type="button" data-type="{$data['turninorout']}">
                                <span style="color:red;"><i class="icon-plus" title=""></i>转入</span>
                            </button>
                        {else}
                            <button class="btn btn-small delbutton" type="button" data-id="{$CURRENTNUN}"><i class="icon-trash" title="删除充值明细"></i></button>
                        {/if}
                        </b>
                        <input type="hidden" name="truncashtype[{$CURRENTNUN}]" data-cid="{$CURRENTNUN}" value="{$data['turninorout']}">
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr>

                    {assign var=COUNTER value=0}
                    {foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}
                    {if !in_array($FIELD_MODEL->getFieldName(),$COINRETURN)}{continue}{/if}
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
                    <td class="fieldLabel {$WIDTHTYPE}" title="{$FIELD_MODEL->get('prompt')}">
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
                                    <label class="muted pull-right marginRight10px">{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}{vtranslate($FIELD_MODEL->get('label'), $MODULE)}{if $FIELD_MODEL->get('prompt') neq ''}<span class="icon-question-sign"></span></label>{/if}
                                {/if}
                            {else if $FIELD_MODEL->get('uitype') eq "83"}
                                {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) COUNTER=$COUNTER}
                            {else}
                                {if in_array($FIELD_MODEL->get('label'),array('cashtransfer','accounttransfer'))}
                                {assign var=LABELNAME value=$FIELD_MODEL->get('label')|cat:$data['turninorout']}
                                    {vtranslate($LABELNAME,{$MODULE_NAME})}
                                {else}
                                    {vtranslate($FIELD_MODEL->get('label'), $MODULE)}
                                {/if}
                                {if $FIELD_MODEL->get('prompt') neq ''}<span class="icon-question-sign"></span>{/if}
                            {/if}
                            {if $isReferenceField neq "reference"}</label>{/if}
                    </td>
                    {if $FIELD_MODEL->get('uitype') neq "83"}
                        <td class="fieldValue {$WIDTHTYPE}" {if $FIELD_MODEL->get('uitype') eq '19'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if} {if $FIELD_MODEL->get('uitype') eq '20'} colspan="3"{/if}>
                            {assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
                            {assign var="FIELD_NAME" value=$FIELD_MODEL->get('name')}
                            {if $FIELD_MODEL->get('uitype') eq 1}
                                {if $FIELD_MODEL->getFieldName() eq 'did'}
                                    {assign var="FIELD_INFO" value=Zend_Json::encode($FIELD_MODEL->getFieldInfo())}
                                    <input name="popupReferenceModule" type="hidden" value="RefillApplication" />
                                    <input name="mid[{$CURRENTNUN}]" type="hidden" data-cid="{$CURRENTNUN}" value="" data-multiple="0" class="sourceField" data-displayvalue='{$FIELD_MODEL->getEditViewDisplayValue($FIELD_MODEL->get('fieldvalue'))}' data-fieldinfo='{$FIELD_INFO}' autocomplete="off">
                                    <div class="row-fluid input-prepend input-append">
                                        <span class="add-on clearReferenceSelection cursorPointer"><i id="RefillApplication_editView_fieldName_did_clear" class="icon-remove-sign" title="清除"></i></span>
                                        <input id="mid_display[{$CURRENTNUN}]" readonly="readonly" name="mid_display[{$CURRENTNUN}]" type="text" class=" span7 	marginLeftZero autoComplete" value="" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$FIELD_INFO|escape}" placeholder="查找.." autocomplete="off">
                                        <span data-id="RefillApplication_editView_fieldName_did_select" class="add-on relatedPopupDid cursorPointer"><i id="RefillApplication_editView_fieldName_did_select" data-id="RefillApplication_editView_fieldName_did_select" class="icon-search relatedPopupDid" title="选择"></i></span>
                                    </div>
                                {else}
                                    {assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
                                    {assign var="FIELD_NAME" value=$FIELD_MODEL->get('name')}
                                    {assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($FIELD_MODEL->getFieldInfo()))}
                                    <input id="{$MODULE}_editView_fieldName_{$FIELD_NAME}" type="text"
                                           class="input-large {if $FIELD_MODEL->isNameField()}nameField{/if}"
                                           data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
                                           name="m{$FIELD_MODEL->getFieldName()}[{$CURRENTNUN}]"
                                           value="{$data[$FIELD_MODEL->getFieldName()]}"
                                            {if ($FIELD_MODEL->get('uitype') eq '106' && $MODE neq '') || $FIELD_MODEL->get('uitype') eq '3'
                                            || $FIELD_MODEL->get('uitype') eq '4'|| $FIELD_MODEL->isReadOnly() ||  in_array($FIELD_MODEL->getFieldName(),$STRINGCOINRETURN)}
                                                readonly="readonly"
                                            {/if}
                                           data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if}
                                           data-cid="{$CURRENTNUN}"
                                           data-typecash="{$data['turninorout']}"
                                    />
                                {/if}
                            {elseif in_array($FIELD_MODEL->get('uitype'),array(15,16))}
                                {assign var="FIELD_INFO" value=Zend_Json::encode($FIELD_MODEL->getFieldInfo())}
                                {assign var=PICKLIST_VALUES value=$FIELD_MODEL->getPicklistValues()}
                                {assign var="FIELDNAME" value={$FIELD_MODEL->getFieldName()}}
                                <select class="chzn-select" name="m{$FIELDNAME}[{$CURRENTNUN}]" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo='{$FIELD_INFO|escape}' {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if} data-selected-value='{$FIELD_MODEL->get('fieldvalue')}'
                                        data-cid="{$CURRENTNUN}"
                                        data-typecash="{$data['turninorout']}"
                                >
                                    {foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$PICKLIST_VALUES}
                                        <option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}" {if trim(decode_html($data[$FIELD_MODEL->getFieldName()])) eq trim($PICKLIST_NAME)} selected {/if} >{$PICKLIST_VALUE}</option>
                                    {/foreach}

                                </select>
                            {elseif $FIELD_MODEL->get('uitype') eq 10}
                                {assign var="REFERENCE_LIST" value=$FIELD_MODEL->getReferenceList()}
                                {assign var="REFERENCE_LIST_COUNT" value=count($REFERENCE_LIST)}
                                {assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($FIELD_MODEL->getFieldInfo()))}
                                {if {$REFERENCE_LIST_COUNT} eq 1}
                                    <input name="popupReferenceModule" type="hidden" value="{$REFERENCE_LIST[0]}" />
                                {/if}
                                {if {$REFERENCE_LIST_COUNT} gt 1}
                                    {assign var="DISPLAYID" value=$FIELD_MODEL->get('fieldvalue')}
                                    {assign var="REFERENCED_MODULE_STRUCT" value=$FIELD_MODEL->getUITypeModel()->getReferenceModule($DISPLAYID)}
                                    {if !empty($REFERENCED_MODULE_STRUCT)}
                                        {assign var="REFERENCED_MODULE_NAME" value=$REFERENCED_MODULE_STRUCT->get('name')}
                                    {/if}
                                    {if in_array($REFERENCED_MODULE_NAME, $REFERENCE_LIST)}
                                        <input name="popupReferenceModule" type="hidden" value="{$REFERENCED_MODULE_NAME}" />
                                    {else}
                                        <input name="popupReferenceModule" type="hidden" value="{$REFERENCE_LIST[0]}" />
                                    {/if}
                                {/if}
                                <input name="m{$FIELD_MODEL->getFieldName()}[{$CURRENTNUN}]" type="hidden" value="{$FIELD_MODEL->get('fieldvalue')}" data-multiple="{$FIELD_MODEL->get('ismultiple')}" class="sourceField" data-displayvalue='{$FIELD_MODEL->getEditViewDisplayValue($FIELD_MODEL->get('fieldvalue'))}' data-fieldinfo='{$FIELD_INFO}' data-cid="{$CURRENTNUN}"
                                       data-typecash="{$data['turninorout']}"/>
                                {assign var="displayId" value=$FIELD_MODEL->get('fieldvalue')}
                                <div class="row-fluid input-prepend input-append">
                                    <input id="m{$FIELD_NAME}_display[{$CURRENTNUN}]" name="m{$FIELD_MODEL->getFieldName()}_display[{$CURRENTNUN}]" type="text" class="{if $smarty.request.view eq 'Edit'} span7 {else} span8 {/if}	marginLeftZero autoComplete" {if !empty($displayId)}readonly="true"{/if}
                                           value="{$FIELD_MODEL->getEditViewDisplayValue($displayId)}" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
                                           data-fieldinfo='{$FIELD_INFO}' placeholder="{vtranslate('LBL_TYPE_SEARCH',$MODULE)}"
                                            {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if}
                                           data-cid="{$CURRENTNUN}"
                                           data-typecash="{$data['turninorout']}"
                                    />
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
        {/foreach}
        {/foreach}
    {/if}


    <div id="insertafter"></div>
        <script>
            var COINRETURNsheet='<table class="table table-bordered blockContainer showInlineTable detailview-table Duplicates" data-num="yesreplace"><thead><input type="hidden" name="truncashtype[]" data-cid="yesreplace" value="#inorout#"><tr><th class="blockHeader" colspan="4"><img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;"><img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">&nbsp;&nbsp;inoroutname明细<b class="pull-right"><button class="btn btn-small delbutton" type="button" data-id="1"><i class="icon-trash" title="删除充值明细"></i></button></b></th></tr></thead><tbody><tr><td class="fieldLabel " title=""><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> ID</label></td><td class="fieldValue "><select class="chzn-select" name="mid[]" data-cid="yesreplace" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"><option value="">选择一个选项</option><option value="" selected=""></option></select></td><td class="fieldLabel " title=""><label class="muted pull-right marginRight10px">账户名称</label></td><td class="fieldValue "><input id="RefillApplication_editView_fieldName_accountzh" type="text" class="input-large " data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="maccountzh[]" data-cid="yesreplace" value="" readonly="readonly" ></td></tr><tr><td class="fieldLabel " title=""><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 充值平台</label></td><td class="fieldValue "><input name="popupReferenceModule" type="hidden" value="Products"><input name="mproductid[]" data-cid="yesreplace" type="hidden" value="" data-multiple="0" class="sourceField" data-displayvalue="" ><div class="row-fluid input-prepend input-append"><input id="mproductid_display[]" data-cid="yesreplace" name="mproductid_display[]" data-cid="yesreplace" type="text" class=" span7 	marginLeftZero autoComplete" value="" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  placeholder="查找.." readonly="readonly"></div></td><td class="fieldLabel " title="由充值ID带出"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 有无服务<span class="icon-question-sign"></span></label></td><td class="fieldValue "><select class="chzn-select" name="misprovideservice[]" data-cid="yesreplace" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" "><option value="">选择一个选项</option><option value="yes">有</option><option value="no">无</option></select></td></tr><tr><td class="fieldLabel " title=""><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 客户返点类型</label></td><td class="fieldValue "><select class="chzn-select" name="maccountrebatetype[]" data-cid="yesreplace" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"><option value="">选择一个选项</option><option value="CashBack">返现</option><option value="GoodsBack">返货</option></select></td><td class="fieldLabel " title="客户返点"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 返点<span class="icon-question-sign"></span></label></td><td class="fieldValue "><input  type="text" class="input-large " data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="mdiscount[]" data-cid="yesreplace" value="" readonly="readonly"  data-typecash="#inorout#"></td></tr><tr><td class="fieldLabel " title=""><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> inoroutname现金</label></td><td class="fieldValue "><input type="text" class="input-large " data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="mcashtransfer[]" data-cid="yesreplace" value="" readonly="readonly"  data-typecash="#inorout#"></td><td class="fieldLabel " title=""><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> inoroutname账户币</label></td><td class="fieldValue "><input type="text" class="input-large " data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="maccounttransfer[]" data-cid="yesreplace" value="" data-typecash="#inorout#"></td></tr></tbody></table';
        </script>
{/strip}