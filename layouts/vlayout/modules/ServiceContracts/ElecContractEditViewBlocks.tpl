{strip}
<br class="ELECCONTRACT_INFOFLAG">
    <input name="editinput1" type="hidden" value="">
    <input name="editinput2" type="hidden" value="">
        <table class="table table-bordered blockContainer showInlineTable ELECCONTRACT_INFO  detailview-table">
        <thead>
        <tr>
            <th class="blockHeader" colspan="4">
            <img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;"><img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">&nbsp;&nbsp;{vtranslate('ELECCONTRACT_INFO', $MODULE)}</th>
        </tr>
        </thead>
        <tbody>
        <tr>
        {assign var=COUNTER value=0}
        {assign var="COUNTINUFIELDS" value=['eleccontracttpl','eleccontractid','relatedattachment']}
        {foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}
            {if in_array($FIELD_MODEL->getFieldName(),$COUNTINUFIELDS)}{continue}{/if}
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
                <td class="fieldValue {$WIDTHTYPE}" {if $FIELD_MODEL->get('uitype') eq '19'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if} {if $FIELD_MODEL->get('uitype') eq '20'} colspan="3"{/if}>
                    {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) BLOCK_FIELDS=$BLOCK_FIELDS}
                </td>
            {/if}
            {if $BLOCK_FIELDS|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype"}
                <td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
            {/if}
        {/foreach}
        </tr></tbody>
        </table>
<script type="text/javascript">
    {foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}
    $('#ServiceContracts_editView_fieldName_{$FIELD_MODEL->getFieldName()}').validationEngine();
    {/foreach}
    $('select[name="eleccontracttplid"]').removeClass('chzn-select');
    $('select[name="relatedattachmentid"]').removeClass('chzn-select');
    $('select[name="eleccontracttplid"]').after('<button type="button" class="btn btn-info preeleccontracttpl" data-name="eleccontracttplid" style="display:inline-block;vertical-align:top">预览</button>');
    $('select[name="relatedattachmentid"]').after('<button type="button" class="btn btn-info preeleccontracttpl" data-name="relatedattachmentid" style="dispaly:inline-block;vertical-align:top">预览</button>');

</script>

{/strip}
