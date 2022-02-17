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
{assign var=SELECTED_FIELDS value=$CUSTOMVIEW_MODEL->getSelectedFields()}
<div class="row-fluid">
	<form class="form-horizontal" id="CustomView" name="CustomView" method="post" action="index.php">
		<input type=hidden name="record" id="record" value="{$RECORD_ID}" />
		<input type="hidden" name="module" value="CustomView" />
		<input type="hidden" name="action" value="Save" />
		<input type="hidden" name="source_module" value="{$SOURCE_MODULE}"/>
		<input type="hidden" id="stdfilterlist" name="stdfilterlist" value=""/>
		<input type="hidden" id="advfilterlist" name="advfilterlist" value=""/>
        <input type="hidden" id="status" name="status" value="{$CV_PRIVATE_VALUE}"/>
		
		<input type="hidden" value="{$smarty.get.public}" id="public" class="public" name="public" />
		<input type="hidden" value="{$smarty.get.filter}" id="filter" class="filter" name="filter" />
		
		<input type="hidden" id="sourceModule" value="{$SOURCE_MODULE}">
        <input type="hidden" name="date_filters" data-value='{Vtiger_Util_Helper::toSafeHTML(ZEND_JSON::encode($DATE_FILTERS))}' />
		<div class="filterBlocksAlignment">
			
			<div class="well filterConditionsDiv" style="padding:3px;margin-bottom:5px;">
				<div class="row-fluid filterActions">
						<div class="btn-group">
								<button class="btn"  type="submit">搜索</button>
						</div>
						
		                <span class="pull-right">另存为
		                <input  type="text" id="viewname" data-validation-engine='validate[required]' name="viewname" value="{if $CUSTOMVIEW_MODEL->get('viewname')}{$CUSTOMVIEW_MODEL->get('viewname')}{else}Temp{/if}">
						<input id="setdefault" type="checkbox" name="setdefault" value="1" {if $CUSTOMVIEW_MODEL->isDefault()} checked="checked"{/if}><span class="alignMiddle"> {vtranslate('LBL_SET_AS_DEFAULT',$MODULE)}</span>
						
							<span class="hide">
			                <input id="setmetrics" name="setmetrics" type="checkbox" value="1" {if $CUSTOMVIEW_MODEL->get('setmetrics') eq '1'} checked="checked"{/if}><span class="alignMiddle"> {vtranslate('LBL_LIST_IN_METRICS',$MODULE)}</span>
			                <input id="status" name="status" type="checkbox" {if $CUSTOMVIEW_MODEL->isSetPublic()} value="{$CUSTOMVIEW_MODEL->get('status')}" checked="checked" {else} value="{$CV_PENDING_VALUE}" {/if}>
			                <span class="alignMiddle"> {vtranslate('LBL_SET_AS_PUBLIC',$MODULE)}</span>
							</span>
						
							
						&nbsp;
						<div class="btn-group">
								<button class="btn"  type="submit">保存并搜索</button>
						</div>&nbsp;
						</span>
					
					
				</div>
				<div class="row-fluid">
					<span class="span12">
						{include file='AdvanceFilter.tpl'|@vtemplate_path}
					</span>
				</div>
				
				{assign var=MANDATORY_FIELDS value=array()}
				
				<input type="hidden" name="columnslist" value='{ZEND_JSON::encode($SELECTED_FIELDS)}' />
				<input id="mandatoryFieldsList" type="hidden" value='{ZEND_JSON::encode($MANDATORY_FIELDS)}' />
			
					
				
			
			</div>
			
	
		</div>
		
	</form>
</div>
{/strip}
