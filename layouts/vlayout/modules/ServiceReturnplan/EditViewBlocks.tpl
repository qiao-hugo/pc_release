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
                                        {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE)}
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

    <script>
         var bbbbb='<tr> <td> <span class="redColor">*</span> <input data-validation-engine="validate[required]" type="number" name="sequence[]" placeholder="排序"   style="width: 40px;"> </td> <td> <span class="redColor">*</span> <div class="input-append"> <input data-validation-engine="validate[required]" type="number" name="upperlimit[]" placeholder="上限"  style="width: 40px;"> <span class="add-on">天</span> </div> </td> <td> <span class="redColor">*</span> <div class="input-append"> <input data-validation-engine="validate[required]" type="number" name="lowerlimit[]" placeholder="下限"  style="width: 40px;"> <span class="add-on">天</span> </div> </td> <td > <textarea style="width:100%" id="test" type="textarea" name="returnplantext[]" placeholder="备注"></textarea> </td> <td class="muted pull-right marginRight10px"> <div> <button class="btn btn-small del_extra" type="button" > <i class="icon-trash"></i></button> </div> </td> </tr>';
    </script>

    <table class="table table-bordered table-striped blockContainer showInlineTable" id = "">
        <thead>
        <tr><th class="blockHeader" colspan="5"><span >客户回访计划详细</span></th></tr>
        </thead>
        <tbody id="extra_body">
        <tr>
            <td style="width:100px;"><b>回访次数</b></td>
            <td style="width:100px;"><b>间隔时间上限</b></td>
            <td style="width:100px;"><b>间隔时间下限</b></td>
            <td ><b>回访内容</b></td>
            <td style="width:100px"><b><button class="btn btn-small add_extra" type="button" ><i class=" icon-plus"></i></button></b></td>
        </tr>
            {if $RETURNPLAN_DETAIL neq ""}
                {foreach item="returnplan_detail" from="$RETURNPLAN_DETAIL"}
                    <tr>
                        <td>
                            <span class="redColor">*</span>
                            <span class="label label-info">第{$returnplan_detail['sequence']}次回访</span>
                            <input type="hidden" data-validation-engine="validate[required]" type="number" name="sequence[]" placeholder="排序" value="{$returnplan_detail['sequence']}"  style="width: 40px;">
                        </td>
                        <td>
                            <span class="redColor">*</span> <div class="input-append">
                                <input data-validation-engine="validate[required]" type="number" name="upperlimit[]" placeholder="间隔时间上限" value="{$returnplan_detail['upperlimit']}" style="width: 40px;">
                                <span class="add-on">天</span> </div>
                        </td>
                        <td>
                            <span class="redColor">*</span>
                            <div class="input-append">
                                <input data-validation-engine="validate[required]" type="number" name="lowerlimit[]" placeholder="间隔时间下限" value="{$returnplan_detail['lowerlimit']}" style="width: 40px;">
                                <span class="add-on">天</span> </div>
                        </td>
                        <td >
                            <textarea style="width:93%" type="textarea" name="returnplantext[]" placeholder="备注">{$returnplan_detail['returnplantext']}</textarea>
                        </td>
                        <td >
                            <div>
                                <button class="btn btn-small add_extra" type="button" >
                                        <i class=" icon-plus"></i></button>
                                <button class="btn btn-small del_extra" type="button" >
                                    <i class="icon-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                {/foreach}
            {/if}
        </tbody>
    </table>
{/strip}