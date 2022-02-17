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
            <h3>添加共享&nbsp;</h3>
        </div>
        <form id="EditView" class="form-horizontal"  method="post">
            <input type="hidden" name="module" value="SharingAccess"/>
            <input type="hidden" name="parent" value="Settings"/>
            <input type="hidden" name="action" value="IndexAjax"/>
            <input type="hidden" name="mode" value="saveMultiRule"/>
            <div class="modal-body" style="overflow:auto;min-height:380px;">
                <div class="row-fluid">
                    <table class="table table-bordered equalSplit detailview-table"><thead>
                            </thead><tbody>
                        <tr><td style="text-align: right"><span class="redColor">*</span>团队群组
                            </td><td>
                                <label class="pull-left">
                                    <select  class="chzn-select referenceModulesList streched" name="source_id" data-validation-engine='validate[required]'>
                                    <option value="">请选择一项</option>
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
                                </label>
                            </td></tr>
                        <tr><td style="text-align: right">所属公司
                            </td><td>
                                <label class="pull-left">
                                    <select  class="chzn-select referenceModulesList streched" multiple name="compay_id[]" data-validation-engine='validate[required]'>
                                        {foreach from=$ALLCOMPLANY item=COMPLANY_ID}
                                            <option value="{$COMPLANY_ID['company_codeno']}">
                                                {$COMPLANY_ID['companyfullname']}
                                            </option>
                                        {/foreach}
                                    </select>
                                </label>
                            </td></tr>
                        <tr><td style="text-align: right"><span class="redColor">*</span>可访问部门
                            </td><td>
                                <label class="pull-left">
                                    <select  class="chzn-select referenceModulesList streched" multiple name="target_id[]" data-validation-engine='validate[required]'>
                                        {foreach from=$DEPARTMENT key=GROUP_LABEL item=ALL_GROUP_MEMBERS}
                                            {assign var=ID_EXISTS value='Department:'|cat:$GROUP_LABEL}
                                            <option value="{$GROUP_LABEL}" {if $RULE_MODEL_EXISTS}{if $RULE_MODEL->getTargetMember()->getId() == $ID_EXISTS}selected{/if}{/if}>
                                                {$ALL_GROUP_MEMBERS}
                                            </option>
                                        {/foreach}
                                    </select>
                                </label>
                            </td></tr>

                        <tr><td style="text-align: right"><span class="redColor">*</span>可访问模块
                            </td><td>
                                <label class="pull-left">
                                    <select  class="chzn-select referenceModulesList streched" multiple name="for_module[]" data-validation-engine='validate[required]'>
                                        {foreach from=$ALL_MODULES key=TABID item=MODULE_MODEL}
                                            <option value="{$MODULE_MODEL->get('name')}" {if $RULE_MODEL_EXISTS}{if $RULE_MODEL->getTargetMember()->getId() == $ID_EXISTS}selected{/if}{/if}>
                                                {if $MODULE_MODEL->getName() == 'Accounts'}
                                                    <!--客户联系人公用规则-->
                                                    {$MODULE_MODEL->get('label')|vtranslate:$QUALIFIED_MODULE}
                                                {else}
                                                    {$MODULE_MODEL->get('label')|vtranslate:$MODULE_MODEL->getName()}
                                                {/if}
                                            </option>
                                            {/foreach}
                                    </select>
                                </label>
                            </td></tr>
                        <tr><td style="text-align: right"><span class="redColor">*</span>权限
                            </td><td>
                                <label class="pull-left">
                                    <div class="control-group">
                                            <label class="radio">
                                                <input type="radio" value="0" name="permission" {if $RULE_MODEL_EXISTS} {if $RULE_MODEL->isReadOnly()} checked {/if} {else} checked {/if}/>&nbsp;{vtranslate('LBL_READ', $QUALIFIED_MODULE)}&nbsp;</label>
                                            <label class="radio">
                                                <input type="radio" value="1" name="permission" {if $RULE_MODEL->isReadWrite()} checked {/if} />&nbsp;{vtranslate('LBL_READ_WRITE', $QUALIFIED_MODULE)}&nbsp;</label></div>
                                </label>
                            </td></tr>

{*        <tr><td colspan="2" style="text-align: center"><button class="btn btn-primary" id="preview">添加</button></td></tr>*}
        </tbody></table>
                </div>
            </div>
{*            <div class="control-group"><label class="control-label">{vtranslate('LBL_WITH_PERMISSIONS', $QUALIFIED_MODULE)}</label><div class="controls">*}
{*                    <label class="radio">*}
{*                        <input type="radio" value="0" name="permission" {if $RULE_MODEL_EXISTS} {if $RULE_MODEL->isReadOnly()} checked {/if} {else} checked {/if}/>&nbsp;{vtranslate('LBL_READ', $QUALIFIED_MODULE)}&nbsp;</label>*}
{*                    <label class="radio">*}
{*                        <input type="radio" value="1" name="permission" {if $RULE_MODEL->isReadWrite()} checked {/if} />&nbsp;{vtranslate('LBL_READ_WRITE', $QUALIFIED_MODULE)}&nbsp;</label></div>*}
{*            </div>*}
            <!--引入保存按钮-->
            {include file='ModalFooter.tpl'|@vtemplate_path:'Vtiger'}
        </form>
    </div>
{/strip}