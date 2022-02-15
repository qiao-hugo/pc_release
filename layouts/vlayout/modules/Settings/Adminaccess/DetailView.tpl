{*<!--
/***********************************
** 后台权限详细
*
 **************************/
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
							<button class="btn" onclick="window.location.href='{$RECORD_MODEL->getEditViewUrl()}'" type="button">
								<strong>{vtranslate('LBL_EDIT_RECORD', $MODULE)}</strong>
							</button>
						</span>
					</span>
				</div><hr>
				<div class="control-group">
					<span class="control-label">
						组名 <span class="redColor">*</span>
					</span>
					<div class="controls pushDown">
						<b>{$RECORD_MODEL->getName()}</b>
					</div>
				</div>
				<div class="control-group">
					<span class="control-label">
						{vtranslate('LBL_DESCRIPTION', $QUALIFIED_MODULE)}
					</span>
					<div class="controls pushDown">
						<b>{$RECORD_MODEL->getDescription()}</b>
					</div>
				</div>
				<div class="control-group">
					<span class="control-label">
						人员
					</span>
					<div class="controls pushDown">
						<div class="row-fluid">
						<span class="span3 collectiveGroupMembers">
							<ul class="nav nav-list">
							{assign var="GROUPS" value=$RECORD_MODEL->getuids()}
							{assign var=ALL_ACTIVEUSER_LIST value=$USER_MODEL->getAccessibleUsers(54)}
							{foreach key=DEPARTMENTNAME item=DEPARTMENTNAME_LIST from=$ALL_ACTIVEUSER_LIST}
								
									 {foreach key=OWNER_ID item=OWNER_NAME from=$DEPARTMENTNAME_LIST}
									 	{if in_array($OWNER_ID,$GROUPS)}
										<li class="row-fluid">
											{$OWNER_NAME}
										</li>
										{/if}
									{/foreach}
								
							{/foreach}
							</ul>
						</span>
						</div>
					</div>
				</div>
				<div class="control-group">
					<span class="control-label">
						操作
					</span>
					<div class="controls pushDown">
						<div class="row-fluid">
						<span class="span3 collectiveGroupMembers">
							<ul class="nav nav-list">
							{assign var="ACTIONS" value=$RECORD_MODEL->getactions()}
							
									 	
									

							{foreach item=MENU from=$SETTINGS_MENUS}
							{assign var=menulist value=$MENU->getMenuItems()}
							
				                {foreach item=MENUITEM from=$menulist}
								
				               
								 {if in_array($MENUITEM->getId(),$ACTIONS)}
								 <li class="row-fluid">
								 {vtranslate($MENUITEM->get('name'), $QUALIFIED_MODULE)}
								 </li>
								  {/if}
				                    	
				                   
								{/foreach}
								
							
								
							{/foreach}
							</ul>
						</span>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
{strip}