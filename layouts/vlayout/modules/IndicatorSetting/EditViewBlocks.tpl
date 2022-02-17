{strip}
    <div class='container-fluid editViewContainer'>
        <form class="form-horizontal recordEditView" id="EditView" name="EditView" method="post"
              enctype="multipart/form-data">
            {assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
            {assign var=QUALIFIED_MODULE_NAME value={$MODULE}}
            {assign var=IS_PARENT_EXISTS value=strpos($MODULE,":")}
            {if $IS_PARENT_EXISTS}
                {assign var=SPLITTED_MODULE value=":"|explode:$MODULE}
                <input type="hidden" name="module" value="{$SPLITTED_MODULE[1]}"/>
                <input type="hidden" name="parent" value="{$SPLITTED_MODULE[0]}"/>
            {else}
                <input type="hidden" name="module" value="{$MODULE}"/>
            {/if}
            <input type="hidden" name="action" value="Save"/>
            <input type="hidden" name="record" value="{$RECORD_ID}"/>
            <input type="hidden" name="defaultCallDuration" value="{$USER_MODEL->get('callduration')}"/>
            <input type="hidden" name="defaultOtherEventDuration" value="{$USER_MODEL->get('othereventduration')}"/>
            <input type="hidden" name="educationproperty" value=""/>
            <style>
            </style>
            <div class="contentHeader row-fluid">
                {assign var=SINGLE_MODULE_NAME value='SINGLE_'|cat:$MODULE}
                {if $RECORD_ID neq ''}
                    <h3 class="span8 textOverflowEllipsis"
                        title="{vtranslate('LBL_EDITING', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)} {$RECORD_STRUCTURE_MODEL->getRecordName()}">{vtranslate('LBL_EDITING', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)}
                        - {$RECORD_STRUCTURE_MODEL->getRecordName()}</h3>
                {else}
                    <h3 class="span8 textOverflowEllipsis">{vtranslate('LBL_CREATING_NEW', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)}</h3>
                {/if}
                <span class="pull-right">
				<button class="btn btn-success"
                        type="submit"><strong>{vtranslate('LBL_SAVE', $MODULE)}</strong></button>
				<a class="cancelLink" type="reset"
                   onclick="javascript:window.history.back();">{vtranslate('LBL_CANCEL', $MODULE)}</a>
			</span>
            </div>
            <table class="table table-bordered blockContainer showInlineTable">
                <tr>
                    <th class="blockHeader" colspan="12">基本信息</th>
                </tr>
                <tr>
                    <td colspan="2">部门</td>
                    <td colspan="4">
                        <select name="departmentid" class="chzn-select referenceModulesList streched">
                            {foreach key=index item=value from=$DEPARTMENTUSER}
                                {if !$RECORD->get('departmentid') and in_array($index,$ALREADY_SET_DEPARTMENTS)}{continue}{/if}
                                <option value="{$index}" {if $index==$RECORD->get('departmentid')} selected {/if}>{$value}</option>
                            {/foreach}
                        </select>
                    </td>
                    <td colspan="2">创建人</td>
                    <td colspan="4">
                        <input type="hidden" name="createdid" {if $RECORD->get('id')}value="{$RECORD->get('createdid')}"
                               {else}value="{$CURRENT_USER->id}{/if}">
                        <input type="text" {if $RECORD->get('id')}value="{getUserName($RECORD->get('createdid'))}"
                               {else}value="{$CURRENT_USER->user_name}"{/if} disabled>
                    </td>
                </tr>
            </table>
            {foreach key=STAFF_KEY item=STAFF_STAGE from=$STAFF_STAGES}
                <table class="table table-bordered blockContainer showInlineTable">
                    <tr>
                        <th class="blockHeader" colspan="12">设置{$STAFF_KEY}</th>
                    </tr>
                    <tr>
                        <td colspan="2" style='text-align:right'>员工阶段</td>
                        <td colspan="2">
                            <input type="hidden" name="staff_stage{$STAFF_KEY}" value="{$STAFF_KEY}">
                            <input type="text" value="{$STAFF_STAGE}" disabled>
                        </td>
                        <td colspan="2" style='text-align:right'><span class="redColor">*</span>电话数量</td>
                        <td colspan="2">
                            <input type="number" class="form-verify" name="telnumber{$STAFF_KEY}"
                                   {if !empty($RECORD)}value="{$RECORD->get('telnumber')}"{/if}>
                        </td>
                        <td colspan="4"></td>
                    </tr>
                    <tr>
                        <td colspan="2" style='text-align:right'><span class="redColor">*</span>电话时长</td>
                        <td colspan="2">
                            <input type="number" class="form-verify" name="telduration{$STAFF_KEY}"
                                   {if !empty($RECORD)}value="{$RECORD->get('telduration')}"{/if}>
                        </td>
                        <td colspan="2" style='text-align:right'><span class="redColor">*</span>意向客户数</td>
                        <td colspan="2">
                            <input type="number" class="form-verify" name="intended_number{$STAFF_KEY}"
                                   {if !empty($RECORD)}value="{$RECORD->get('intended_number')}"{/if}>
                        </td>
                        <td colspan="4"></td>
                    </tr>
                    <tr>
                        <td colspan="2" style='text-align:right'><span class="redColor">*</span>邀约数</td>
                        <td colspan="2">
                            <input type="number" class="form-verify" name="invite_number{$STAFF_KEY}"
                                   {if !empty($RECORD)}value="{$RECORD->get('invite_number')}"{/if}>
                        </td>
                        <td colspan="2" style='text-align:right'><span class="redColor">*</span>拜访量</td>
                        <td colspan="2">
                            <input type="number" class="form-verify"  min="0.01" step='0.01' name="visit_number{$STAFF_KEY}"
                                   {if !empty($RECORD)}value="{$RECORD->get('visit_number')}"{/if}>
                        </td>
                        <td colspan="4"></td>
                    </tr>
                    <tr>
                        <td colspan="2" style='text-align:right'><span class="redColor">*</span>回款</td>
                        <td colspan="2">
                            <input type="number" class="form-verify" min="0.01" step='0.01' name="returned_money{$STAFF_KEY}"
                                   {if !empty($RECORD)}value="{$RECORD->get('returned_money')}"{/if}>
                        </td>
                        <td colspan="2" style='text-align:right'>备注</td>
                        <td colspan="2">
                            <input type="text" name="remark{$STAFF_KEY}"
                                   {if !empty($RECORD)}value="{$RECORD->get('remark')}"{/if}>
                        </td>
                        <td colspan="4"></td>
                    </tr>
                    <tr>
                        <td colspan="2" style='text-align:right'>或者关系</td>
                        <td colspan="2">
                            <div class="form-group">
                                <select class="form-control chzn-select referenceModulesList streched form-select-verify"
                                        name="relationship_or{$STAFF_KEY}[]" multiple>
                                    {foreach from=$RELATION_SHIPS item=RELATION_SHIP key=RELATION_SHIP_KEY}
                                        <option value="{$RELATION_SHIP_KEY}"
                                                {if in_array($RELATION_SHIP_KEY,$RELATIONSHIP_OR)}selected{/if}>{$RELATION_SHIP}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </td>
                        <td colspan="8"></td>
                    </tr>
                    <tr style="background-color: #f1f1f1">
                        <td colspan="12">特殊条件设置</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>字段名称</td>
                        <td>关系式</td>
                        <td colspan="2">值</td>
                        <td></td>
                        <td>字段名称</td>
                        <td>运算符</td>
                        <td colspan="3">值</td>
                        <td></td>
                    </tr>
                    {if count($SPECIAL_OPERATORS)>0}

                        {foreach from=$SPECIAL_OPERATORS item=SPECIAL_OPERATOR key=KEY}
                            <tr>
                                <input type="hidden" name="special_operator_id[]" id="special_id_{$KEY}"
                                       value="{$SPECIAL_OPERATOR['id']}"/>
                                <td>IF</td>
                                <td>
                                    <select class="form-control  streched"
                                            name="staff_stage{$STAFF_KEY}_basics_column[]">
                                        {foreach from=$RELATION_SHIPS item=RELATION_SHIP key=RELATION_SHIP_KEY}
                                            <option value="{$RELATION_SHIP_KEY}"
                                                    {if $RELATION_SHIP_KEY==$SPECIAL_OPERATOR['basics_column']}selected{/if}>{$RELATION_SHIP}</option>
                                        {/foreach}
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control  streched"
                                            name="staff_stage{$STAFF_KEY}_basics_operator[]">
                                        {foreach from=$BASIC_OPERATORS item=BASIC_OPERATOR key=BASIC_OPERATOR_KEY}
                                            <option value="{$BASIC_OPERATOR_KEY}"
                                                    {if $SPECIAL_OPERATOR['basics_operator']==$BASIC_OPERATOR_KEY}selected{/if}>{$BASIC_OPERATOR}</option>
                                        {/foreach}
                                    </select>
                                </td>
                                <td colspan="2">
                                    <input type="number" value="{$SPECIAL_OPERATOR['basics_value']}" name="staff_stage{$STAFF_KEY}_basics_value[]">
                                </td>
                                <td>THEN</td>
                                <td>
                                    <select class="form-control  streched" name="staff_stage{$STAFF_KEY}_operate_column[]">
                                        {foreach from=$RELATION_SHIPS item=RELATION_SHIP key=RELATION_SHIP_KEY}
                                            <option value="{$RELATION_SHIP_KEY}"
                                                    {if $SPECIAL_OPERATOR['operate_column']==$RELATION_SHIP_KEY}selected{/if}>{$RELATION_SHIP}</option>
                                        {/foreach}
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control  streched"  name="staff_stage{$STAFF_KEY}_operate_operator[]">
                                        {foreach from=$OPERATOR_OPERATORS item=OPERATOR_OPERATOR}
                                            <option value="{$OPERATOR_OPERATOR}"
                                                    {if $SPECIAL_OPERATOR['operate_operator']==$OPERATOR_OPERATOR}selected{/if}>{$OPERATOR_OPERATOR}</option>
                                        {/foreach}
                                    </select>
                                </td>
                                <td colspan="3">
                                    <input type="number" name="staff_stage{$STAFF_KEY}_operate_value[]"
                                           value="{$SPECIAL_OPERATOR['operate_value']}">
                                </td>
                                <td>
                                    <img src="layouts/vlayout/skins/softed/images/add_search.gif" class="addfallinto"
                                         data-staff_key="{$STAFF_KEY}">
                                    <img src="layouts/vlayout/skins/softed/images/cancel_search.gif"
                                         class="deletefallinto" data-staff_key="{$STAFF_KEY}">
                                </td>
                            </tr>
                        {/foreach}
                    {else}
                        <tr>
                            <td>IF</td>
                            <td>
                                <select class="form-control streched" name="staff_stage{$STAFF_KEY}_basics_column[]">
                                    {foreach from=$RELATION_SHIPS item=RELATION_SHIP key=RELATION_SHIP_KEY}
                                        <option value="{$RELATION_SHIP_KEY}">{$RELATION_SHIP}</option>
                                    {/foreach}
                                </select>
                            </td>
                            <td>
                                <select class="form-control streched" name="staff_stage{$STAFF_KEY}_basics_operator[]">
                                    {foreach from=$BASIC_OPERATORS item=BASIC_OPERATOR key=BASIC_OPERATOR_KEY}
                                        <option value="{$BASIC_OPERATOR_KEY}">{$BASIC_OPERATOR}</option>
                                    {/foreach}
                                </select>
                            </td>
                            <td colspan="2">
                                <input type="number" value="" name="staff_stage{$STAFF_KEY}_basics_value[]">
                            </td>
                            <td>THEN</td>
                            <td>
                                <select class="form-control  streched" name="staff_stage{$STAFF_KEY}_operate_column[]">
                                    {foreach from=$RELATION_SHIPS item=RELATION_SHIP key=RELATION_SHIP_KEY}
                                        <option value="{$RELATION_SHIP_KEY}">{$RELATION_SHIP}</option>
                                    {/foreach}
                                </select>
                            </td>
                            <td>
                                <select class="form-control  streched" name="staff_stage{$STAFF_KEY}_operate_operator[]">
                                    {foreach from=$OPERATOR_OPERATORS item=OPERATOR_OPERATOR}
                                        <option value="{$OPERATOR_OPERATOR}">{$OPERATOR_OPERATOR}</option>
                                    {/foreach}
                                </select>
                            </td>
                            <td colspan="3">
                                <input type="number" name="staff_stage{$STAFF_KEY}_operate_value[]" value="">
                            </td>
                            <td>
                                <img src="layouts/vlayout/skins/softed/images/add_search.gif" class="addfallinto" data-staff_key="{$STAFF_KEY}">
                            </td>
                        </tr>
                    {/if}
                </table>
                <script>
                    var html_{$STAFF_KEY} = "<tr> <td>IF</td> <td><select class=\"form-control  streched\" name=\"staff_stage{$STAFF_KEY}_basics_column[]\">{foreach from=$RELATION_SHIPS item=RELATION_SHIP key=RELATION_SHIP_KEY}<option value=\"{$RELATION_SHIP_KEY}\">{$RELATION_SHIP}</option>{/foreach}</select></td><td><select class=\"form-control chzn-select referenceModulesList streched\"name=\"staff_stage{$STAFF_KEY}_basics_operator[]\">{foreach from=$BASIC_OPERATORS item=BASIC_OPERATOR key=BASIC_OPERATOR_KEY}<option value=\"{$BASIC_OPERATOR_KEY}\" >{$BASIC_OPERATOR}</option>{/foreach}</select></td><td colspan=\"2\"><input type=\"number\" value=\"\" name=\"staff_stage{$STAFF_KEY}_basics_value[]\"></td><td>THEN</td><td><select class=\"form-control chzn-select referenceModulesList streched\" name=\"staff_stage{$STAFF_KEY}_operate_column[]\">{foreach from=$RELATION_SHIPS item=RELATION_SHIP key=RELATION_SHIP_KEY}<option value=\"{$RELATION_SHIP_KEY}\" >{$RELATION_SHIP}</option>{/foreach}</select></td><td><select class=\"form-control chzn-select referenceModulesList streched\"name=\"staff_stage{$STAFF_KEY}_operate_operator[]\" >{foreach from=$OPERATOR_OPERATORS item=OPERATOR_OPERATOR}<option value=\"{$OPERATOR_OPERATOR}\" >{$OPERATOR_OPERATOR}</option>{/foreach}</select></td><td colspan=\"3\"><input type=\"number\" name=\"staff_stage{$STAFF_KEY}_operate_value[]\" value=\"\"></td><td><img src=\"layouts/vlayout/skins/softed/images/add_search.gif\" class=\"addfallinto\" data-staff_key=\"{$STAFF_KEY}\"><img src=\"layouts/vlayout/skins/softed/images/cancel_search.gif\" class=\"deletefallinto\"  data-staff_key=\"{$STAFF_KEY}\"></td></tr>";
                    var first_html_{$STAFF_KEY} = "<tr> <td>IF</td> <td><select class=\"form-control  streched\" name=\"staff_stage{$STAFF_KEY}_basics_column[]\">{foreach from=$RELATION_SHIPS item=RELATION_SHIP key=RELATION_SHIP_KEY}<option value=\"{$RELATION_SHIP_KEY}\">{$RELATION_SHIP}</option>{/foreach}</select></td><td><select class=\"form-control chzn-select referenceModulesList streched\"name=\"staff_stage{$STAFF_KEY}_basics_operator[]\">{foreach from=$BASIC_OPERATORS item=BASIC_OPERATOR key=BASIC_OPERATOR_KEY}<option value=\"{$BASIC_OPERATOR_KEY}\" >{$BASIC_OPERATOR}</option>{/foreach}</select></td><td colspan=\"2\"><input type=\"number\" value=\"\" name=\"staff_stage{$STAFF_KEY}_basics_value[]\"></td><td>THEN</td><td><select class=\"form-control chzn-select referenceModulesList streched\" name=\"staff_stage{$STAFF_KEY}_operate_column[]\">{foreach from=$RELATION_SHIPS item=RELATION_SHIP key=RELATION_SHIP_KEY}<option value=\"{$RELATION_SHIP_KEY}\" >{$RELATION_SHIP}</option>{/foreach}</select></td><td><select class=\"form-control chzn-select referenceModulesList streched\"name=\"staff_stage{$STAFF_KEY}_operate_operator[]\" >{foreach from=$OPERATOR_OPERATORS item=OPERATOR_OPERATOR}<option value=\"{$OPERATOR_OPERATOR}\" >{$OPERATOR_OPERATOR}</option>{/foreach}</select>  </td><td colspan=\"3\"><input type=\"number\" name=\"staff_stage{$STAFF_KEY}_operate_value[]\" value=\"\"></td><td><img src=\"layouts/vlayout/skins/softed/images/add_search.gif\" class=\"addfallinto\" data-staff_key=\"{$STAFF_KEY}\"></td></tr>";
                </script>
            {/foreach}
        </form>
    </div>
{/strip}
