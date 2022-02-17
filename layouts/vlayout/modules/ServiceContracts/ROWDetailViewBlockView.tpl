{strip}
	{$PHASE_SPLIT_DATA=$RECORD->assignContractPhaseSplit($BLOCK_FIELDS,$RECORD->getId(),$INITNUM,0)}
	{if $PHASE_SPLIT_DATA|count gt 0}
	{$BODYTD=''}
	{foreach  item=BLOCK_FIELDS from=$PHASE_SPLIT_DATA}
		{if $BLOCK_FIELDS@first}{$HEADERTD='<tr>'}{/if}
		{$INITNUM=$BLOCK_FIELDS@iteration}
		{$BODYTD=$BODYTD|cat:'<tr class="PhaseSplit'|cat:$INITNUM|cat:'">'}
		{foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}
			{$LASTCOL=''}
			{$ADDPHASESPLIT=''}
			{$SUBPHASESPLIT=''}
			{if $FIELD_MODEL@last}
				{$ADDPHASESPLIT=''}
				{$LASTCOL='</tr>'}
			{/if}
			{if $INITNUM eq 1}
				{$REDFLAG=''}
				{$PROMPT=''}
				{$HEADERTD=$HEADERTD|cat:'<td class="fieldLabel_'|cat:$FIELD_MODEL->getFieldName()|cat:'" style="border-bottom: 1px solid #dddddd !important;" title="'|cat:$FIELD_MODEL->get('prompt')|cat:'"><label>'|cat:{vtranslate($FIELD_MODEL->get("label"),'ServiceContracts')}|cat:$PROMPT|cat:'</label></td>'|cat:$LASTCOL}
			{/if}
			{$COLDATA=''}
			{*{if in_array($FIELD_MODEL->getFieldName(),$SHOWFIELD)}*}
				{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),$MODULE) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME RECORD=$RECORD assign=COLDATA}
			{*{else}
				*}{*{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL  MODULE="ServiceContracts" RECORD=$RECORD_STRUCTURE_MODEL->getRecord() assign=DISPLAYFIELD}*}{*
				{$COLDATA='<span class="value" data-field-type="'|cat:{$FIELD_MODEL->getFieldDataType()}|cat:'">'|cat:$DISPLAYFIELD|cat:'</span>'}
			{/if}*}
			{$WIDTHSTYLE=''}{if $FIELD_MODEL->getFieldName() eq 'collectiondescription'}{$WIDTHSTYLE="width:150px;"}{/if}
			{$BODYTD=$BODYTD|cat:'<td class="'|cat:{$WIDTHTYPE}|cat:'" style="border-bottom: 1px solid #dddddd !important;'|cat:$WIDTHSTYLE|cat:'">'|cat:$COLDATA|cat:'</td>'|cat:$SUBPHASESPLIT|cat:$LASTCOL}
		{/foreach}
	{/foreach}

	<table class="table table-bordered blockContainer showInlineTable {$BLOCK_LABEL} {if $BLOCK_LABEL eq 'LBL_ADV'}hide tableadv{/if} detailview-table" style="overflow: auto;" data-stageNum="{$INITNUM}">
		<thead>
		<tr>
			<th class="blockHeader" colspan="4">
				<img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;"><img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">&nbsp;&nbsp;{vtranslate($BLOCK_LABEL_KEY,'ServiceContracts')}</th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td colspan="4" warp>
				<table  class="table table-bordered blockContainer showInlineTable CONTRACT_PHASE_SPLIT_LIST">
					{$HEADERTD}
					{$BODYTD}
				</table>
			</td>
		</tr>
		</tbody>
	</table>
	{/if}

{/strip}