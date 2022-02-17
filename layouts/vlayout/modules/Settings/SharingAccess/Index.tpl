{strip}
<div class="container-fluid" id="sharingAccessContainer">
	<div class="contents">
			<div>
				<div class="widget_header row-fluid">
					<div class="span8"><h3>{vtranslate('LBL_SHARING_ACCESS', $QUALIFIED_MODULE)}</h3></div>
					<div class="span4">
						<button class="btn btn-success pull-right hide" type="submit" name="saveButton"><strong>{vtranslate('LBL_APPLY_NEW_SHARING_RULES', $QUALIFIED_MODULE)}</strong></button>
					</div>
				</div>
				<hr>
			</div>
			<div class="row-fluid">
		<span class="span6 btn-toolbar ">
					<a class="btn addButton newCustomRule"  href="javascript:app.showModalWindow(null, 'index.php?module=SharingAccess&parent=Settings&view=IndexAjax&mode=newRule')">
				<i class="icon-plus icon-white"></i>&nbsp;
				<strong>{vtranslate('LBL_ADD_RECORD', $QUALIFIED_MODULE)}</strong>
			</a>
		</span>
			</div>
			<div id="showSearch1" >
				<div class="control-group margin0px">
					<select id="search_select" class="chzn-select referenceModulesList streched">
						{foreach from=$ALL_RULE_MEMBERS key=GROUP_LABEL item=ALL_GROUP_MEMBERS}
							<optgroup label="{vtranslate($GROUP_LABEL, $QUALIFIED_MODULE)}">
								{foreach from=$ALL_GROUP_MEMBERS item=MEMBER}
									<option value="{$MEMBER->getId()}"
											{if $RULE_MODEL_EXISTS} {if $RULE_MODEL->getSourceMember()->getId() == $MEMBER->getId()}selected{/if}{/if}>
										{$MEMBER->getName()}
									</option>
								{/foreach}
							</optgroup>
						{/foreach}
					</select>
					<span class="paddingLeft10px cursorPointer help-inline" id="searchButton" style="margin-bottom: 20px;">
				<img src="layouts/vlayout/skins/softed/images/search.png" alt="搜索按钮" title="搜索按钮"></span>
{*					&nbsp;&nbsp;<span class="paddingLeft10px cursorPointer help-inline" style="color:white;border: 1px solid #5897fb;text-align: center;background-color: #5897fb;" id="groupSearchButton">查询</span>*}
				</div>

			</div>
		<form name="EditSharingAccess" action="index.php" method="post" class="form-horizontal" id="EditSharingAccess">
			<input type="hidden" name="module" value="SharingAccess" />
			<input type="hidden" name="action" value="SaveAjax" />
			<input type="hidden" name="parent" value="Settings" />
			<input type="hidden" class="dependentModules" value='{ZEND_JSON::encode($DEPENDENT_MODULES)}' />

			<table class="table table-bordered table-condensed sharingAccessDetails">
				<thead>
					<tr class="blockHeader">
						<th>{vtranslate('LBL_MODULE', $QUALIFIED_MODULE)}</th>
						<!--数据共享类型[可读/可写]!-->	
						{*{foreach from=$ALL_ACTIONS key=ACTION_ID item=ACTION_MODEL}
							<th>{$ACTION_MODEL->getName()|vtranslate:$QUALIFIED_MODULE}</th>
						{/foreach}*}
						<th colspan=4></th>
						<th nowrap="nowrap">{'LBL_ADVANCED_SHARING_RULES'|vtranslate:$QUALIFIED_MODULE}</th>
					</tr>
				</thead>
				<tbody>
					<!--日程模块!-->
					{*<!--<tr data-module-name="Calendar">
						<td>{'SINGLE_Calendar'|vtranslate:'Calendar'}</td>
						<td colspan=4 >
							<!--<div><input type="radio" style="margin-left: 25%" disabled="disabled" /></div>
						</td>
						<td >
							<div><input type="radio" style="margin-left: 25%" disabled="disabled" /></div>
						</td>
						<td>
							<div><input type="radio" style="margin-left: 25%" disabled="disabled" /></div>
						</td>
						<td>
							<div><input type="radio" style="margin-left: 25%" checked="true" disabled="disabled" /></div>-->
						</td>
						<td>
							<div class="row-fluid">
								<div class="span3">&nbsp;</div>
								<div class="span6">
									<button type="button" class="btn btn-mini vtButton arrowDown row-fluid" disabled="disabled" ><img src="layouts/vlayout/skins/images/Arrow-down.png"></img></button>
								</div>
								<div class="span3">&nbsp;</div>
							</div>
						</td>
					</tr>-->*}
					<!--模块列表!-->
					{foreach from=$ALL_MODULES key=TABID item=MODULE_MODEL}
					<tr data-module-name="{$MODULE_MODEL->get('name')}">
						<td>
							{if $MODULE_MODEL->getName() == 'Accounts'}
								<!--客户联系人公用规则-->
								{$MODULE_MODEL->get('label')|vtranslate:$QUALIFIED_MODULE}
							{else}
								{$MODULE_MODEL->get('label')|vtranslate:$MODULE_MODEL->getName()}
							{/if}
						</td>
						{*{foreach from=$ALL_ACTIONS key=ACTION_ID item=ACTION_MODEL}
						<td >
							{if $ACTION_MODEL->isModuleEnabled($MODULE_MODEL)}
								<div><input style="margin-left: 25%" type="radio" name="permissions[{$TABID}]" data-action-state="{$ACTION_MODEL->getName()}" value="{$ACTION_ID}"{if $MODULE_MODEL->getPermissionValue() eq $ACTION_ID}checked="true"{/if}></div>
							{/if}
						</td>
						{/foreach}*}
						<td colspan=4></td>
						<td class="triggerCustomSharingAccess">
							<div class="row-fluid">
								<div class="span3">&nbsp;</div>
								<div class="span6">
									<button type="button" class="btn btn-mini vtButton arrowDown row-fluid" data-handlerfor="fields" data-togglehandler="{$TABID}-rules"><img src="layouts/vlayout/skins/images/Arrow-down.png"></img></button>
									<button type="button" class="btn btn-mini vtButton arrowUp row-fluid hide" data-handlerfor="fields" data-togglehandler="{$TABID}-rules"><img src="layouts/vlayout/skins/images/Arrow-up.png"></img></button>
								</div>
								<div class="span3">&nbsp;</div>
							</div>
						</td>
					</tr>
					{/foreach}
				</tbody>
			</table>
			<div>
				<div class="pull-right">
					<button class="btn btn-success hide" type="submit" name="saveButton"><strong>{vtranslate('LBL_APPLY_NEW_SHARING_RULES', $QUALIFIED_MODULE)}</strong></button>
				</div>
			</div>
		</form>
	</div>
</div>
{/strip}