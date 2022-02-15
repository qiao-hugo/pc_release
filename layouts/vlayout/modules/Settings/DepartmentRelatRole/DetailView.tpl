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
    <div class="detailViewInfo">
		<div class="contents">
			<form id="detailView" class="padding20 form-horizontal">
				<div class="row-fluid">
					<span class="span6 settingsHeader">
						{$RECORD_MODEL->get('groupname')}
					</span>
					<span class="span6">
						<span class="pull-right">

						</span>
					</span>
				</div><hr>
				<div class="control-group">
					<span class="control-label">
						{vtranslate('LBL_Department_NAME', $QUALIFIED_MODULE)} <span class="redColor">*</span>
					</span>
					<div class="controls pushDown">
						<b>{$RECORD_MODEL->getName()}</b>
					</div>
				</div>
				<div class="control-group">
					<span class="control-label">
						{vtranslate('LBL_Role_NAME', $QUALIFIED_MODULE)} <span class="redColor">*</span>
					</span>
					<div class="controls pushDown">
						<b>{$RECORD_MODEL->getrolename()}</b>
					</div>
				</div>
				<div class="control-group">
					<span class="control-label">
						{vtranslate('LBL_Ramark_NAME', $QUALIFIED_MODULE)}
					</span>
					<div class="controls pushDown">
						<b>{$RECORD_MODEL->getremark()}</b>
					</div>
				</div>
			</form>
		</div>
	</div>
{strip}