{*<!--/*****编辑共享规则 针对团队群组访问其他部门数据 不设置编辑权限****/-->*}
{strip}
    <div>
        <div class="modal-header contentsBackground">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>{$GROUP->get('groupname')}已共享记录</h3>
        </div>
            <div class="modal-body" style="overflow:scroll;height: 400px">
                <div class="row-fluid">
                    <table class="table table-bordered equalSplit detailview-table"><thead>
                        <th style="text-align: center">所属公司</th><th style="text-align: center">可访问部门</th><th style="text-align: center">可访问模块</th>
                        </thead><tbody>

                        {foreach item=RULE_MODEL key=DEPARTMENT_NAME from=$RULE_MODEL_LIST}
                            {assign var=COMPANYIDS value=[]}
                        <tr><td style="text-align: center">{foreach item=MODULE_MODEL key=DEPARTMENT from=$RULE_MODEL}
                                    {if $MODULE_MODEL->get('companyid') neq ''}
                                        <span class="label label-success">
                                        {if $MODULE_MODEL->get('name') == 'Accounts'}
                                            <!--客户联系人公用规则-->
                                            {$MODULE_MODEL->get('name')|vtranslate:$QUALIFIED_MODULE}
                                        {else}
                                            {$MODULE_MODEL->get('name')|vtranslate:$MODULE_MODEL->get('name')}
                                        {/if}</span>&nbsp;
                                        {*{if !in_array($MODULE_MODEL->get('companyid'),$COMPANYIDS)}*}
                                            {assign var=COMPANYIDARRAY value=explode(',',$MODULE_MODEL->get('companyid'))}
                                            {foreach item=VALURDS from=$COMPANYIDARRAY}<span class="label label-info">
                                            {$MODULE_MODEL->getCompanyNameByID($VALURDS)}</span>
                                            {*{$COMPANYIDS[]=$MODULE_MODEL->get('companyid')}*}
                                            {/foreach}
                                        {*{/if}*}

                                    {/if}
                                    <br>
                                {/foreach}
                            </td><td style="text-align: center">{$DEPARTMENT_NAME}
                            </td><td>
                               {foreach item=MODULE_MODEL key=DEPARTMENT from=$RULE_MODEL}
{*                                   {$MODULE_MODEL->get('name')|vtranslate:$QUALIFIED_MODULE};*}
{*                                    {$MODULE_MODEL->get('name')};*}
                                   {if $MODULE_MODEL->get('name') == 'Accounts'}
                                       <!--客户联系人公用规则-->
                                       {$MODULE_MODEL->get('name')|vtranslate:$QUALIFIED_MODULE};
                                   {else}
                                       {$MODULE_MODEL->get('name')|vtranslate:$MODULE_MODEL->get('name')};
                                   {/if}
                               {/foreach}
                            </td></tr>
                        {/foreach}
                        </tbody></table>
                </div>
            </div>
    </div>
{/strip}