{*<!--/*****编辑共享规则 针对团队群组访问其他部门数据 不设置编辑权限****/-->*}
{strip}
    {assign var=RULE_MODEL_EXISTS value=true}
    {assign var=RULE_ID value=$RULE_MODEL->getId()}
    {if empty($RULE_ID)}
        {assign var=RULE_MODEL_EXISTS value=false}
    {/if}
    <div>
        <div class="modal-header contentsBackground">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>{vtranslate($MODULE_MODEL->get('name'), $MODULE)} {vtranslate('LBL_ADD_CUSTOM_RULE_TO', $QUALIFIED_MODULE)}&nbsp;</h3>
        </div>
        <form id="editCustomRule" class="form-horizontal">
            <input type="hidden" name="for_module" value="{$MODULE_MODEL->get('name')}" />
            <input type="hidden" name="record" value="{$RULE_ID}" />
			<input type="hidden" value="0" name="permission"/>
            <div class="modal-body" style="overflow:visible;">
                <div class="row-fluid">
                    <div class="control-group">
                        <label class="control-label">{vtranslate($MODULE_MODEL->get('name'), $MODULE)}&nbsp;{vtranslate('LBL_OF', $MODULE)}</label>					
                        <div class="controls">
                            <select class="chzn-select" name="source_id">
                                {*{foreach from=$ALL_RULE_MEMBERS key=GROUP_LABEL item=ALL_GROUP_MEMBERS}
								 {assign var=ID_EXISTS value='Roles:'|cat:$GROUP_LABEL}
									<option value="{$GROUP_LABEL}" {if $RULE_MODEL_EXISTS}{if $RULE_MODEL->getSourceMember()->getId() == $ID_EXISTS}selected{/if}{/if}>
										{$ALL_GROUP_MEMBERS}
									</option>
            {/foreach}*}

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
                </div>	
            </div>
            <div class="control-group">
                <label class="control-label">{vtranslate('LBL_CAN_ACCESSED_BY', $QUALIFIED_MODULE)}公司</label>
                <div class="controls">
                    <select class="chzn-select" multiple name="companyid[]">
                        {foreach from=$ALLCOMPLANY  item=COMPLANYID}
                            <option value="{$COMPLANYID['company_codeno']}" {if $RULE_MODEL_EXISTS}{if in_array($COMPLANYID['company_codeno'],$RULE_MODEL->getCompanyID())}selected{/if}{/if}>
								{$COMPLANYID['companyfullname']}
							</option>
						{/foreach}
					</select>
        </div>
    </div>
    <div class="control-group">
                <label class="control-label">{vtranslate('LBL_CAN_ACCESSED_BY', $QUALIFIED_MODULE)}</label>
                <div class="controls">
                    <select class="chzn-select" name="target_id">
                        {foreach from=$DEPARTMENT key=GROUP_LABEL item=ALL_GROUP_MEMBERS}
							{assign var=ID_EXISTS value='Department:'|cat:$GROUP_LABEL}
                            <option value="{$GROUP_LABEL}" {if $RULE_MODEL_EXISTS}{if $RULE_MODEL->getTargetMember()->getId() == $ID_EXISTS}selected{/if}{/if}>
								{$ALL_GROUP_MEMBERS}
							</option>
						{/foreach}
					</select>
        </div>	
    </div>
    <div class="control-group"><label class="control-label">{vtranslate('LBL_WITH_PERMISSIONS', $QUALIFIED_MODULE)}</label><div class="controls">
    <label class="radio">
    <input type="radio" value="0" name="permission" {if $RULE_MODEL_EXISTS} {if $RULE_MODEL->isReadOnly()} checked {/if} {else} checked {/if}/>&nbsp;{vtranslate('LBL_READ', $QUALIFIED_MODULE)}&nbsp;</label>
    <label class="radio">
    <input type="radio" value="1" name="permission" {if $RULE_MODEL->isReadWrite()} checked {/if} />&nbsp;{vtranslate('LBL_READ_WRITE', $QUALIFIED_MODULE)}&nbsp;</label></div>	
    </div>
</div>
</div>
<!--引入保存按钮-->
{include file='ModalFooter.tpl'|@vtemplate_path:'Vtiger'}
</form>
</div>
{/strip}