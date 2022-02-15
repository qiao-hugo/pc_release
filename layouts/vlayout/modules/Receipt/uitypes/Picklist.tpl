{*<!--
/**
  *下拉，保护数量增加限制
 ******/
-->*}
{strip}
{assign var="FIELD_INFO" value=Zend_Json::encode($FIELD_MODEL->getFieldInfo())}
{assign var=PICKLIST_VALUES value=$FIELD_MODEL->getPicklistValues()}
{assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
{assign var="FIELDNAME" value={$FIELD_MODEL->getFieldName()}}
{assign var="NOCHZN" value=['eleccontracttplid']}
 {if $FIELDNAME eq 'parentcate'}
    {if $RECORD_ID>0}
        <select class="chzn-select" name="parentcate" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"   data-selected-value='{$FIELD_MODEL->get('fieldvalue')}' style="width:150px;">
            {if $FIELD_MODEL->isEmptyPicklistOptionAllowed()}<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>{/if}
            {foreach item=PICKLIST_P  from=$RECORD_PRODUCTSCATEGORY['parent']}
                <option value="{$PICKLIST_P['parentcateid']}" {if $PICKLIST_P['parentcateid'] eq $RECORD_PRODUCTSCATEGORY['nparentcate']} selected {/if}>{$PICKLIST_P['parentcate']}</option>
            {/foreach}
        </select>
        <select class="chzn-select" name="soncate" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  data-selected-value='soncate' style="width:150px;">
            {if $FIELD_MODEL->isEmptyPicklistOptionAllowed()}<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>{/if}
            {foreach item=PICKLIST_S  from=$RECORD_PRODUCTSCATEGORY['ischild']}
                <option value="{$PICKLIST_S['soncateid']}" {if $PICKLIST_S['soncateid'] eq $RECORD_PRODUCTSCATEGORY['nsoncate']} selected {/if}>{$PICKLIST_S['soncate']}</option>
            {/foreach}
        </select>
        {*{if $FIELD_MODEL->get('fieldvalue')=='T云WEB版' && !$HASORDER}*}
        {*<select class="chzn-select" name="categoryid"  style="width:150px;">*}
            {*{foreach item=CATEGORY_VALUE key=CATEGORY_NAME from=$CATEGORY}*}
                {*<option value="{$CATEGORY_VALUE['id']}" {if $CATEGORYID eq $CATEGORY_VALUE['id'] }selected{/if}>{$CATEGORY_VALUE['title']}</option>*}
            {*{/foreach}*}
        {*</select>*}
        {*{/if}*}


    {else}
        <select class="chzn-select" name="parentcate" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"   data-selected-value='{$FIELD_MODEL->get('fieldvalue')}' style="width:150px;">
            {if $FIELD_MODEL->isEmptyPicklistOptionAllowed()}<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>{/if}
            {foreach item=PICKLIST_P  from=$RECORD_PRODUCTSCATEGORY['parent']}
                <option value="{$PICKLIST_P['parentcateid']}">{$PICKLIST_P['parentcate']}   </option>
            {/foreach}
        </select>
    {/if}
 {else}
     {if $FIELDNAME eq 'invoicecompany'}
         {if $RECORD_ID>0}
             <select class="chzn-select" name="{$FIELDNAME}" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo='{$FIELD_INFO|escape}' {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if} data-selected-value='{$FIELD_MODEL->get('fieldvalue')}'>
                 {if $FIELD_MODEL->isEmptyPicklistOptionAllowed()}<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}{$FIELD_MODEL->get('fieldvalue')}</option>{/if}
                 {foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$PICKLIST_VALUES}
                     <option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}" {if trim(decode_html($FIELD_MODEL->get('fieldvalue'))) eq trim($PICKLIST_NAME)} selected {/if}     {if $FIELDNAME eq 'accountrank' && isset($RANKLIMIT) }{if !$RANKLIMIT[$PICKLIST_NAME]} disabled="true"{/if}{/if}>{$PICKLIST_VALUE}{$Invoicecompany}</option>
                 {/foreach}
             </select>
         {else}
             <select class="chzn-select" name="{$FIELDNAME}" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo='{$FIELD_INFO|escape}' {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if} data-selected-value='{$FIELD_MODEL->get('fieldvalue')}'>
                 {if $FIELD_MODEL->isEmptyPicklistOptionAllowed()}<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}{$FIELD_MODEL->get('fieldvalue')}</option>{/if}
                 {foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$PICKLIST_VALUES}
                     <option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}" {if $Invoicecompany eq trim($PICKLIST_NAME)} selected {/if}  {if $FIELDNAME eq 'accountrank' && isset($RANKLIMIT) }{if !$RANKLIMIT[$PICKLIST_NAME]} disabled="true"{/if}{/if}>{$PICKLIST_VALUE}</option>
                 {/foreach}
             </select>
         {/if}

     {/if}

 {/if}
{/strip}