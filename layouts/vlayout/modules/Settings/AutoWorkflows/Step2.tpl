{*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************}
{strip}
	<form name="EditWorkflow" action="index.php" method="post" id="workflow_step2" class="form-horizontal" >
		<input type="hidden" name="module" value="AutoWorkflows" />
		<input type="hidden" name="action" value="Save" />
		<input type="hidden" name="parent" value="Settings" />
		<input type="hidden" class="step" value="2" />
		<input type="hidden" name="summary" value="{$WORKFLOW_MODEL->get('summary')}" />
		<input type="hidden" name="record" value="{$WORKFLOW_MODEL->get('record')}" />
		<input type="hidden" name="module_name" value="{$WORKFLOW_MODEL->get('module_name')}" />
		<input type="hidden" name="execution_condition" value="{$WORKFLOW_MODEL->get('execution_condition')}" />
		<input type="hidden" name="conditions" id="advanced_filter" value='' />
		<input type="hidden" id="olderConditions" value='{ZEND_JSON::encode($WORKFLOW_MODEL->get('conditions'))}' />
		<input type="hidden" name="filtersavedinnew" value="{$WORKFLOW_MODEL->get('filtersavedinnew')}" />
	<div class="workFlowContents" style="padding-left: 3%;padding-right: 3%">
	<div class="padding2per" style="border:1px solid #ccc;">
		 <label>
              <strong>第二步：规则条件</strong>
         </label>
         <table id="searchtable" style="margin:auto" ><tbody></tbody></table>
         <div class="tempcondition"></div>
                <br>
		<div class="pull-right">
				<button class="btn btn-danger backStep" type="button"><strong>{vtranslate('LBL_BACK', $QUALIFIED_MODULE)}</strong></button>&nbsp;&nbsp;
				<button class="btn btn-success" type="submit"><strong>{vtranslate('LBL_NEXT', $QUALIFIED_MODULE)}</strong></button>
				<a class="cancelLink" type="reset" onclick="javascript:window.history.back();">{vtranslate('LBL_CANCEL', $QUALIFIED_MODULE)}</a>
			</div>
	</div>
	</div>		
			<div class="clearfix"></div>
	</form>
{/strip}