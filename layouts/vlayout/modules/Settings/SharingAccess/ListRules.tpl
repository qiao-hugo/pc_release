{*单个模块数据共享规则*}
{strip}
	<div class="row-fluid ruleListContainer">
		<hr>
		<div class="title padding-bottom1per">
			<span class="themeLinkColor padding-left1per">
				<!-- 模块翻译-->
				<strong>
					{if $FOR_MODULE == 'Accounts'}{vtranslate($FOR_MODULE, $QUALIFIED_MODULE)}{else}{vtranslate($FOR_MODULE, $MODULE)}{/if} {vtranslate('LBL_SHARING_RULE', $QUALIFIED_MODULE)}&nbsp; :</strong>
			</span>
			<span class="pull-right padding-right1per">
				<button class="btn addButton addCustomRule" type="button" data-url="{$MODULE_MODEL->getCreateRuleUrl()}">
					<strong>{vtranslate('LBL_ADD_CUSTOM_RULE', $QUALIFIED_MODULE)}</strong></button>
			</span>
		</div>
		<div class="contents padding1per">
			{if $RULE_MODEL_LIST}
			<table class="table table-bordered table-condensed customRuleTable">
				<thead>
					<tr class="customRuleHeaders">
						<th>{vtranslate('LBL_RULE_NO', $QUALIFIED_MODULE)}</th>
						<!-- Check if the module should the for module to get the translations -->
						<th>{if $FOR_MODULE == 'Accounts'}{vtranslate($FOR_MODULE, $QUALIFIED_MODULE)}{else}{vtranslate($FOR_MODULE, $MODULE)}{/if}
							&nbsp;{vtranslate('LBL_OF', $MODULE)}</th>
						<th>{vtranslate('LBL_CAN_ACCESSED_BY', $QUALIFIED_MODULE)}</th>
						<th>所属公司</th>
						<th>{vtranslate('LBL_PRIVILEGES', $QUALIFIED_MODULE)}</th>
					</tr>
				</thead>
				<tbody>
					{foreach item=RULE_MODEL key=RULE_ID from=$RULE_MODEL_LIST name="customRuleIterator"}
					<tr class="customRuleEntries">
						<td class="sequenceNumber">
							{$smarty.foreach.customRuleIterator.index + 1}
						</td>
						
						<td>{$RULE_MODEL->getSourceMember()->getName()}
							{*<!--<a href="{$RULE_MODEL->getSourceDetailViewUrl()}"></a>-->*}
						</td>
						<td>{$RULE_MODEL->getTargetMember()->getName()}
							{*<!--<a href="{$RULE_MODEL->getTargetDetailViewUrl()}"></a>-->*}
						</td>
						<td>{$RULE_MODEL->getCompanyName()->getName()}
						</td>
						<td>
							{if $RULE_MODEL->isReadOnly()}
								{vtranslate('Read Only', $QUALIFIED_MODULE)}
							{else}
								{vtranslate('Read Write', $QUALIFIED_MODULE)}
							{/if}
							
							<div class="pull-right actions">
								<span class="actionImages">
									<a href="javascript:void(0);" class="edit" data-url="{$RULE_MODEL->getEditViewUrl()}"><i title="{vtranslate('LBL_EDIT', $MODULE)}" class="icon-pencil alignMiddle"></i></a>
									<span class="alignMiddle actionImagesAlignment"> <b>|</b></span>
									<a href="javascript:void(0);" class="delete" data-url="{$RULE_MODEL->getDeleteActionUrl()}"><i title="{vtranslate('LBL_DELETE', $MODULE)}" class="icon-trash alignMiddle"></i></a>
								</span>
							</div>
							
						</td>
					</tr>
					{/foreach}
				</tbody>
			</table>
			{*<!--<div class="recordDetails hide"><p class="textAlignCenter">{vtranslate('LBL_CUSTOM_ACCESS_MESG', $QUALIFIED_MODULE)}.</p></div>-->*}
			{else}
				<div class="recordDetails">
					<p class="textAlignCenter">{vtranslate('LBL_CUSTOM_ACCESS_MESG', $QUALIFIED_MODULE)}.<!--<a href="">{vtranslate('LBL_CLICK_HERE', $QUALIFIED_MODULE)}</a>&nbsp;{vtranslate('LBL_CREATE_RULE_MESG', $QUALIFIED_MODULE)}--></p>
				</div>
			{/if}
		</div>
	</div>
{/strip}