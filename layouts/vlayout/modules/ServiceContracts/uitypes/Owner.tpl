{*<!--
/********************************************************************************
*<select class="chzn-select input-small hasinactive"><option value="active">正常</option><option value="inactive">离职</option><option value="all">所有</option></select>
-->*}
{strip}
{assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($FIELD_MODEL->getFieldInfo()))}
{assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
{assign var="D53TO54" value=array('Signid','Receiveid')}
{if $FIELD_MODEL->get('uitype') eq '53'}
	{assign var=ASSIGNED_USER_ID value=$FIELD_MODEL->get('name')}
    {assign var=CURRENT_USER_ID value=$USER_MODEL->get('id')}
	{assign var=FIELD_VALUE value=$FIELD_MODEL->get('fieldvalue')}
	
	{if $FIELD_MODEL->get('helpinfo') neq '1'}
		{if $FIELD_VALUE eq ''}
			{assign var=FIELD_VALUE value=$CURRENT_USER_ID}
		{/if}
	{/if}
	{if in_array($ASSIGNED_USER_ID,$D53TO54) && $FINANCIAL_MODIFICATION}
        {assign var=ALL_ACTIVEUSER_LIST value=$USER_MODEL->getAccessibleUsers(54,'','',$DEPARTMENTID)}
		<select id="select_{$MODULE}_{$ASSIGNED_USER_ID}"  class="chzn-select {$ASSIGNED_USER_ID}" {if !$IS_NOT_VALIDATOR}data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"{/if} data-name="{$ASSIGNED_USER_ID}" name="{$ASSIGNED_USER_ID}" data-fieldinfo='{$FIELD_INFO}' data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}>
        {foreach key=DEPARTMENTNAME item=DEPARTMENTNAME_LIST from=$ALL_ACTIVEUSER_LIST}
			<optgroup label="{$DEPARTMENTNAME}">
                {foreach key=OWNER_ID item=OWNER_NAME from=$DEPARTMENTNAME_LIST}
                    {$OWNER_ID}
					<option value="{$OWNER_ID}" data-picklistvalue= '{$OWNER_NAME}' {if $FIELD_VALUE eq $OWNER_ID} selected {/if}

							data-userId="{$CURRENT_USER_ID}">
                        {$OWNER_NAME}
					</option>
                {/foreach}
			</optgroup>
        {/foreach}
		</select>
	{else}
        {assign var=ALL_ACTIVEUSER_LIST value=$USER_MODEL->getAccessibleUsers()}

	<select class="chzn-select {$ASSIGNED_USER_ID}" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-name="{$ASSIGNED_USER_ID}" name="{$ASSIGNED_USER_ID}" data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if}>
		<optgroup label="{vtranslate('LBL_USERS')}">
			{if $FIELD_MODEL->get('helpinfo') eq '1'}
			<option value="" >选择一个选项  </option>
			{/if}
			<option value="{$FIELD_VALUE}" selected>{$USER_MODEL->getUserName($FIELD_VALUE)}</option>
			{foreach key=OWNER_ID item=OWNER_NAME from=$ALL_ACTIVEUSER_LIST}
					{if ($FIELD_VALUE neq $OWNER_ID ) and (strpos($OWNER_NAME,'离职') eq true)}
					{continue}
					{/if}
					{if $FIELD_VALUE eq $OWNER_ID}
						{continue}
					{/if}
                    {*<option value="{$OWNER_ID}" {if $FIELD_VALUE eq $OWNER_ID} selected {/if}>{$OWNER_NAME}
                    </option>*}
                	<option value="{$OWNER_ID}">{$OWNER_NAME}
                    </option>
			{/foreach}
		</optgroup> 
	</select>
    {/if}
{/if}
{* TODO - UI type 52 needs to be handled *}

{if $FIELD_MODEL->get('uitype') eq '54'}
	{assign var=ALL_ACTIVEUSER_LIST value=$USER_MODEL->getAccessibleUsers($FIELD_MODEL->get('uitype'),'','',$DEPARTMENTID)}
	 
	{assign var=ASSIGNED_USER_ID value=$FIELD_MODEL->get('name')}
    {assign var=CURRENT_USER_ID value=$USER_MODEL->get('id')}
	{assign var=FIELD_VALUE value=$FIELD_MODEL->get('fieldvalue')}

	{assign var="FIELD_VALUE_LIST" value=explode(' |##| ',$FIELD_MODEL->get('fieldvalue'))}
	{if $FIELD_VALUE eq ''}
		{assign var=FIELD_VALUE value=$CURRENT_USER_ID}
	{/if}
	<select id="select_{$MODULE}_{$ASSIGNED_USER_ID}" {if $FIELD_MODEL->ismultiple eq 0}multiple{/if}  class="chzn-select {$ASSIGNED_USER_ID}" {if !$IS_NOT_VALIDATOR}data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"{/if} data-name="{$ASSIGNED_USER_ID}" name="{$ASSIGNED_USER_ID}[]" data-fieldinfo='{$FIELD_INFO}' data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}>
		{foreach key=DEPARTMENTNAME item=DEPARTMENTNAME_LIST from=$ALL_ACTIVEUSER_LIST}
                <optgroup label="{$DEPARTMENTNAME}">
                {foreach key=OWNER_ID item=OWNER_NAME from=$DEPARTMENTNAME_LIST}
                {$OWNER_ID}
                    <option value="{$OWNER_ID}" data-picklistvalue= '{$OWNER_NAME}' {if in_array($OWNER_ID, $FIELD_VALUE_LIST)} selected {/if}
						
						data-userId="{$CURRENT_USER_ID}">
                    	{$OWNER_NAME}
                    </option>
				{/foreach}
				</optgroup>
		{/foreach}
	</select>
{/if}
{* TODO - UI type 54 needs to be handled *}
{/strip}