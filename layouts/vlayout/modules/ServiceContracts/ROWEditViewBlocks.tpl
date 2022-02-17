{strip}
	{$RECORDTEMP=$RECORD_STRUCTURE_MODEL->getRecord()}
	{$TEMPINITNUM=$INITNUM}
	{$PHASE_SPLIT_DATA=$RECORDTEMP->assignContractPhaseSplit($BLOCK_FIELDS,$RECORD_ID,$INITNUM)}
	{$BODYTD=""}
	{foreach  item=BLOCK_FIELDS from=$PHASE_SPLIT_DATA}
		{if $ISSHOWHEADER && $BLOCK_FIELDS@first}{$HEADERTD='<tr>'}{/if}
		{if $TEMPINITNUM eq 0}{$INITNUM=$BLOCK_FIELDS@iteration}{/if}
		{$BODYTD=$BODYTD|cat:'<tr class="PhaseSplit'|cat:$INITNUM|cat:'">'}
		{foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}
			{$LASTCOL=''}
			{$ADDPHASESPLIT=''}
			{$SUBPHASESPLIT=''}
			{if $FIELD_MODEL@last}
				{$ADDPHASESPLIT='<td style="border-bottom: 1px solid #dddddd !important;"><button class="btn btn-small" type="button" id="addPhaseSplit"><i class=" icon-plus"></i></button></td>'}
				{$LASTCOL='</tr>'}
				{if $ISSHOWHEADER && $BLOCK_FIELDS@first}
					{$SUBPHASESPLIT='<td style="border-bottom: 1px solid #dddddd !important;"></td>'}
				{else}
					{$SUBPHASESPLIT='<td style="border-bottom: 1px solid #dddddd !important;"><button class="btn btn-small subPhaseSplit" type="button"  data-stageNum="'|cat:$INITNUM|cat:'"><i class="icon-minus"></i></button></td>'}
				{/if}
			{/if}
			{if $ISSHOWHEADER &&  $BLOCK_FIELDS@first}
				{$REDFLAG=''}
				{$WITDHSTYLE=''}{if $FIELD_MODEL->getFieldName() eq 'collectiondescription'}{$WITDHSTYLE='width:30%;'}{/if}
				{if $FIELD_MODEL->isMandatory() eq true && $isReferenceField neq "reference"} {$REDFLAG='<span class="redColor">*</span>'}{/if}
				{$PROMPT=''}{*{if $FIELD_MODEL->get('prompt') neq ''}{$PROMPT='<span class="icon-question-sign"></span>'}{/if}*}
				{$HEADERTD=$HEADERTD|cat:'<td class="fieldLabel_'|cat:$FIELD_MODEL->getFieldName()|cat:'" style="border-bottom: 1px solid #dddddd !important;'|cat:$WITDHSTYLE|cat:'" title="'|cat:$FIELD_MODEL->get('prompt')|cat:'"><label>'|cat:$REDFLAG|cat:{vtranslate($FIELD_MODEL->get("label"), $MODULE)}|cat:$PROMPT|cat:'</label></td>'|cat:$ADDPHASESPLIT|cat:$LASTCOL}
			{/if}
			{$COLDATA=''}
			{if in_array($FIELD_MODEL->getFieldName(),$SHOWFIELD)}
				{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateNameM(),$MODULE) BLOCK_FIELDS=$BLOCK_FIELDS assign=COLDATA}
			{else}
			 	{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL  MODULE="ServiceContracts" RECORD=$RECORD_STRUCTURE_MODEL->getRecord() assign=DISPLAYFIELD}
				{$COLDATA='<span class="value" data-field-type="'|cat:{$FIELD_MODEL->getFieldDataType()}|cat:'">'|cat:$DISPLAYFIELD|cat:'</span>'}
			{/if}
			{$BODYTD=$BODYTD|cat:'<td class="'|cat:{$WIDTHTYPE}|cat:'" style="border-bottom: 1px solid #dddddd !important;">'|cat:$COLDATA|cat:'</td>'|cat:$SUBPHASESPLIT|cat:$LASTCOL}
		{/foreach}
	{/foreach}
		{if $ISSHOWHEADER}
		<table class="table table-bordered blockContainer showInlineTable {$BLOCK_LABEL} {if $BLOCK_LABEL eq 'LBL_ADV'}hide tableadv{/if} detailview-table" style="overflow: auto;" data-stageNum="{$RECORDTEMP->CONTRACT_PHASE_SPLIT_NUM}">
		<thead>
		<tr>
			<th class="blockHeader" colspan="4">
				<img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;"><img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">&nbsp;&nbsp;{vtranslate($BLOCK_LABEL, $MODULE)}</th>
		</tr>
		</thead>
		<tbody>
		<tr>
		<td colspan="4" warp>
		<table  class="table table-bordered blockContainer showInlineTable CONTRACT_PHASE_SPLIT_LIST">{/if}
		{$HEADERTD}
		{$BODYTD}
		{if $ISSHOWHEADER}
				</table>
			</td>
		</tr>
		</tbody>
		</table>
		{/if}

{/strip}
