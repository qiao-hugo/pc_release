
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


    {assign var=INCREASE value=array('mservicecontractsid','maccountid','file','remarks','cashconsumptiontotal','cashincreasetotal','mservicecontractsid','maccountid','mservicecontractsid_name','maccountid_name','cashgift','taxrefund','cashconsumption','cashincrease','grantquarter','mstatus','discount','accountrebatetype','receivementcurrencytype')}
    {assign var="REFERENCE" value=array('mservicecontractsid')}
    {if $RECORD_ID>0}
        {assign var=STRINGINCREASE value=array('cashincrease')}
        {foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE name="EditViewBlockLevelLoop"}
            {if $BLOCK_FIELDS|@count lte 0}{continue}{/if}
            {if $BLOCK_LABEL neq 'LBL_CUSTOM_INFORMATION'}{continue}{/if}

        {foreach key=row_no item=data from=$C_RECHARGESHEET}
            {assign var=CURRENTNUN value=$row_no+1}
            <table class="table table-bordered blockContainer showInlineTable detailview-table  increase{$CURRENTNUN}" data-num="{$CURRENTNUN}">
                <thead>
                <tr>
                    <th class="blockHeader" colspan="4">
                        <img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;"><img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">&nbsp;&nbsp;
                        虚拟回款
                        <b class="pull-right">
                        <button class="btn btn-small delincrease" type="button" data-num="{$CURRENTNUN}">
                            <span style="color:red;"><i class="icon-minus" title=""></i>增款</span>
                        </button>
                        </b>
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr>

                    {assign var=COUNTER value=0}
                    {foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}
                    {if !in_array($FIELD_MODEL->getFieldName(),$INCREASE)}{continue}{/if}
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
                            {elseif $FIELD_MODEL->get('uitype') eq "83"}
                                {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) COUNTER=$COUNTER}
                            {else}
                                {vtranslate($FIELD_MODEL->get('label'), $MODULE)}
                                {if $FIELD_MODEL->get('prompt') neq ''}<span class="icon-question-sign"></span>{/if}
                            {/if}
                            {if $isReferenceField neq "reference"}</label>{/if}
                    </td>
                    {if $FIELD_MODEL->get('uitype') neq "83"}
                        <td class="fieldValue {$WIDTHTYPE}" {if $FIELD_MODEL->get('uitype') eq '19'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if} {if $FIELD_MODEL->get('uitype') eq '20'} colspan="3"{/if}>
                            {assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
                            {assign var="FIELD_NAME" value=$FIELD_MODEL->get('name')}
                            {if $FIELD_MODEL->get('uitype') eq 1}

                                    {assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
                                    {assign var="FIELD_NAME" value=$FIELD_MODEL->get('name')}
                                    {assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($FIELD_MODEL->getFieldInfo()))}
                                    <input id="{$MODULE}_editView_fieldName_{$FIELD_NAME}" type="text"
                                           class="input-large {if $FIELD_MODEL->isNameField()}nameField{/if}"
                                           data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
                                           name="m{$FIELD_MODEL->getFieldName()}[{$CURRENTNUN}]"
                                           value="{$data[$FIELD_MODEL->getFieldName()]}"
                                            {if ($FIELD_MODEL->get('uitype') eq '106' && $MODE neq '') || $FIELD_MODEL->get('uitype') eq '3'
                                            || $FIELD_MODEL->get('uitype') eq '4'|| $FIELD_MODEL->isReadOnly() ||  in_array($FIELD_MODEL->getFieldName(),$STRINGINCREASE)}
                                                readonly="readonly"
                                            {/if}
                                           data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if}
                                           data-num="{$CURRENTNUN}"
                                    />

                            {elseif in_array($FIELD_MODEL->get('uitype'),array(15,16))}
                                {assign var="FIELD_INFO" value=Zend_Json::encode($FIELD_MODEL->getFieldInfo())}
                                {assign var=PICKLIST_VALUES value=$FIELD_MODEL->getPicklistValues()}
                                {assign var="FIELDNAME" value={$FIELD_MODEL->getFieldName()}}
                                <select class="chzn-select" name="m{$FIELDNAME}[{$CURRENTNUN}]" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo='{$FIELD_INFO|escape}' {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if} data-selected-value='{$FIELD_MODEL->get('fieldvalue')}'
                                data-num="{$CURRENTNUN}"
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

                                <input name="m{$FIELD_MODEL->getFieldName()}[{$CURRENTNUN}]"  othername="{$FIELD_MODEL->getFieldName()}" data-num="{$CURRENTNUN}" type="hidden" value="{$data[$FIELD_MODEL->getFieldName()]}" data-multiple="{$FIELD_MODEL->get('ismultiple')}" class="sourceField" data-displayvalue='{$FIELD_MODEL->getEditViewDisplayValue($FIELD_MODEL->get('fieldvalue'))}' data-fieldinfo='{$FIELD_INFO}'
                                       />
                                {assign var="displayId" value=$FIELD_MODEL->get('fieldvalue')}
                                <div class="row-fluid input-prepend input-append">
                                    {if in_array($FIELD_MODEL->getFieldName(),$REFERENCE)}
                                    <span class="add-on clearReferenceSelection cursorPointer">
                                        <i id="{$MODULE}_editView_fieldName_{$FIELD_NAME}_clear" class='icon-remove-sign' title="{vtranslate('LBL_CLEAR', $MODULE)}"></i>
                                    </span>
                                    {/if}
                                    <input id="m{$FIELD_NAME}[display{$CURRENTNUN}]_display" name="m{$FIELD_MODEL->getFieldName()}[display{$CURRENTNUN}]_display" type="text" class="{if $smarty.request.view eq 'Edit'} span7 {else} span8 {/if}	marginLeftZero autoComplete" {if !empty($displayId)}readonly="true"{/if}
                                           value="{$data[$FIELD_MODEL->getFieldName()|cat:'_name']}" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
                                           data-fieldinfo='{$FIELD_INFO}' placeholder="{vtranslate('LBL_TYPE_SEARCH',$MODULE)}"
                                            {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if}
                                           data-num="{$CURRENTNUN}"
                                    />
                                    {if in_array($FIELD_MODEL->getFieldName(),$REFERENCE)}
                                    <span class="add-on relatedPopup cursorPointer">
                                        <i id="{$MODULE}_editView_fieldName_{$FIELD_NAME}_select" class="icon-search relatedPopup" title="{vtranslate('LBL_SELECT', $MODULE)}" ></i>
                                    </span>
                                    {/if}
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


    <div id="increaselist"></div>

{/strip}