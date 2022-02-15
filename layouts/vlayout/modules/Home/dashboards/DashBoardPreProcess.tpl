{strip}
{include file="HeaderV2.tpl"|vtemplate_path:$MODULE}
{include file="BasicHeaderV2.tpl"|vtemplate_path:$MODULE}
<div class="main-con">
	<!-- 左侧菜单栏 -->
	<div class="layer19 flex-col">
		{foreach item=MENU from=$MENULISTS}
			<div class="bd flex-row">
				<img class="menu-icon" src="libraries/v2/img/{$MENU['icon']}"/>
				<span class="menu-txt">{$MENU['name']}</span>
				<i class="arrow-right arrow flex-col"></i>
				<div class="menu-list">
					<div class="menu-main-con flex-col">
						<div class="menu-item">
							{foreach item=SUBMENU from=$MENU['children']}
								<a href="{$SUBMENU['url']}">{$SUBMENU['name']}</a>
							{/foreach}
						</div>
					</div>
				</div>
			</div>
		{/foreach}
	</div>
{/strip}