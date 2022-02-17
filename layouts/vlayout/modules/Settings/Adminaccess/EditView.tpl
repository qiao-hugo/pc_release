{*+********
 * 后台分配权限
 ********}
{strip}
<div class="editViewContainer container-fluid">
	<form name="Edit" action="index.php" method="post" id="EditView" class="form-horizontal">
		<input type="hidden" name="module" value="Adminaccess">
		<input type="hidden" name="action" value="Save">
		<input type="hidden" name="parent" value="Settings">
		<input type="hidden" name="record" value="{$RECORD_MODEL->getId()}">
		<input type="hidden" name="mode" value="{$MODE}">
		<div class="contentHeader row-fluid">
			<h3>
				{if !empty($MODE)}
					编辑{vtranslate('SINGLE_'|cat:$MODULE, $QUALIFIED_MODULE)} - {$RECORD_MODEL->getName()}
				{else}
					新增
				{/if}
			</h3>
            <hr>
		</div>
		<div class="control-group">
			<span class="control-label">
				<span class="redColor">*</span> 权限配置
			</span>
			<div class="controls">
				<input class="input-large" name="groupname" value="{$RECORD_MODEL->getName()}" data-validation-engine="validate[required]">
			</div>
		</div>
		<div class="control-group">
			<span class="control-label">
				{vtranslate('LBL_DESCRIPTION', $QUALIFIED_MODULE)}
			</span>
			<div class="controls">
				<input class="input-large" name="description" id="description" value="{$RECORD_MODEL->getDescription()}" />
			</div>
		</div>
		<div class="control-group">
			<span class="control-label">
				人员
			</span>
			<div class="controls">
				<div class="row-fluid">
					<span class="span6">
						{assign var="GROUP_MEMBERS" value=$RECORD_MODEL->getuids()}

						{assign var=ALL_ACTIVEUSER_LIST value=$USER_MODEL->getAccessibleUsers(54)}
						<select id="memberList" class="row-fluid members select2" multiple="true" name="members[]" data-placeholder="选择用户" data-validation-engine="validate[required]">
							
							{foreach key=DEPARTMENTNAME item=DEPARTMENTNAME_LIST from=$ALL_ACTIVEUSER_LIST}
	                			<optgroup label="{if $DEPARTMENTNAME eq ''} 用户{else} {$DEPARTMENTNAME}{/if}">
				                {foreach key=OWNER_ID item=OWNER_NAME from=$DEPARTMENTNAME_LIST}
								
				               
<option value="{$OWNER_ID}" data-member-type="Users" data-picklistvalue= '{$OWNER_NAME}' {if in_array($OWNER_ID,$GROUP_MEMBERS)} selected {/if}>
				                    	{$OWNER_NAME}
				                    </option>
								{/foreach}
								</optgroup>
								
							{/foreach}
							
						</select>
					</span>
					<span class="span3">
						
					</span>
				</div>
			</div>
		</div>
		<div class="control-group">
			<span class="control-label">
				操作
			</span>
			<div class="controls">
				<div class="row-fluid">
					<span class="span6">
						{assign var="ACTIONS" value=$RECORD_MODEL->getactions()}
						
						<select id="action" class="row-fluid select2" multiple="true" name="actions[]" data-placeholder="选择操作" data-validation-engine="validate[required]">
							
							{foreach item=MENU from=$SETTINGS_MENUS}
							{assign var=menulist value=$MENU->getMenuItems()}
							{if !empty($menulist)}
	                			<optgroup label="{vtranslate($MENU->getLabel(), $QUALIFIED_MODULE)}">
				                {foreach item=MENUITEM from=$menulist}
								
				               
								<option value="{$MENUITEM->getId()}" data-member-type="" data-picklistvalue= '{vtranslate($MENUITEM->get('name'), $QUALIFIED_MODULE)}' {if in_array($MENUITEM->getId(),$ACTIONS)} selected {/if}>
				                    	{vtranslate($MENUITEM->get('name'), $QUALIFIED_MODULE)}
				                    </option>
								{/foreach}
								</optgroup>
							{/if}
								
							{/foreach}
							
							
						</select>
					</span>
					
				</div>
			</div>
		</div>
			<div class="row-fluid">
				<div class="span5">
					<span class="pull-right">
						<button class="btn btn-success" type="submit"><strong>{vtranslate('LBL_SAVE', $QUALIFIED_MODULE)}</strong></button>
						<a class="cancelLink" type="reset" onclick="javascript:window.history.back();">{vtranslate('LBL_CANCEL', $QUALIFIED_MODULE)}</a>
					</span>
				</div>
			</div>
	</form>
</div>
{/strip}