{strip}
<!--菜单列表-->
	<div class="navbar" style="margin-bottom:0;">
		<div class="navbar-inner" id="nav-inner">
			<div class="menuBar row-fluid">
				{* overflow+height is required to avoid flickering UI due to responsive handling, overflow will be dropped later *}
				<div class="span10">
					<ul class="nav ">
						<li class="tabs">
							<a class="alignMiddle {if $MODULE eq 'Home'} selected {/if}" href="{$HOME_MODULE_MODEL->getDefaultUrl()}"><img src="{vimage_path('home.png')}" alt="{vtranslate('LBL_HOME','Vtiger')}" title="{vtranslate('LBL_HOME','Vtiger')}" /></a>
						</li>
						{$MYMENU}
						<li class="dropdown"><a class="dropdown-toggle" href="http://192.168.44.130" target="_blank">珍岛问答系统</a></li>
						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#">OA办公系统<b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="http://192.168.7.231/" target="_blank">假务系统</a></li>
								<li><a href="http://192.168.7.231:8081/" target="_blank">证券系统</a></li>
								<li><a href="http://192.168.7.201:9999/" target="_blank">报销系统</a></li>
							</ul>
						</li>
						<li class="dropdown"><a class="dropdown-toggle" href="http://192.168.44.157:81/" target="_blank">客服系統</a></li>
						<li class="dropdown"><a class="dropdown-toggle" href="http://192.168.7.231:8301/" target="_blank">招聘系統</a></li>
						<li class="dropdown"><a class="dropdown-toggle" href="http://192.168.7.231:8501/" target="_blank">人事系统</a></li>
						<li class="dropdown"><a class="dropdown-toggle" href="http://192.168.7.231:8901/" target="_blank">中小管理系统</a></li>
						<li class="dropdown"><a class="dropdown-toggle" href="https://predmc.71360.com/clue/index?token={$token}" target="_blank">臻寻客</a></li>
						<li class="dropdown"><a class="dropdown-toggle" href="https://prein-gw.71360.com/visit-center/login?__vt_param__={$token}&callback={urlencode('https://prein-web.71360.com/visitcenterweb?original=crm')}" target="_blank">拜访中心</a></li>
						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#">SCRM<b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="/index.php?module=Vtiger&action=LinkToJump&type=1" target="_blank">专业版</a></li>
								<li><a href="/index.php?module=Vtiger&action=LinkToJump&type=2" target="_blank">中小版</a></li>
								<li><a href="/index.php?module=Vtiger&action=LinkToJump&type=3" target="_blank">商业云</a></li>
								<li><a href="/index.php?module=Vtiger&action=LinkToJump&type=4" target="_blank">招聘版</a></li>
							</ul>
						</li>
					</ul>
				</div>
				<input type="hidden" id="waterTextContent" value="{$waterText}" />
				<div class="span2" id="headerLinks" >
					<ul class="nav nav-pills pull-right" style="float:right">
						{foreach key=index item=obj from=$HEADER_LINKS}
							{assign var="src" value=$obj->getIconPath()}
							{assign var="icon" value=$obj->getIcon()}
							{assign var="title" value=$obj->getLabel()}
							{assign var="childLinks" value=$obj->getChildLinks()}
							{assign var="href" value=$obj->getUrl()}
							{assign var="linktype" value=$obj->getType()}

							<li class="dropdown">
									{if !empty($src)}

											<a id="menubar_item_right_{$title}" class="dropdown-toggle" data-toggle="dropdown" href="#">
											<img src="{$src}" alt="{vtranslate($title,$MODULE)}" title="{vtranslate($title,$MODULE)}" />
											{vtranslate($title,$MODULE)}
											{if $linktype eq 'REMINDERLINK'}
											{assign var="myremindercount" value=$obj->myremindercount}
											<span class="badge badge-warning">{$myremindercount}</span>
											{/if}
											</a>

									{else}
											{assign var=title value=$USER_MODEL->get('first_name')}
											{if empty($title)}
												{assign var=title value=$USER_MODEL->get('last_name')}
											{/if}
										<span class="dropdown-toggle" data-toggle="dropdown" href="#">
											<a id="menubar_item_right_{$title}"  class="userName textOverflowEllipsis span" title="{$title}">{$title} <i class="caret"></i> </a> </span>
									{/if}
									{if !empty($childLinks)}
										<ul class="dropdown-menu">
											{foreach key=index item=obj from=$childLinks}
												{if $obj->getLabel() eq NULL}
													<li class="divider">&nbsp;</li>
												{else}
													{assign var="id" value=$obj->getId()}
													{assign var="href" value=$obj->getUrl()}
													{assign var="label" value=$obj->getLabel()}
													{if $linktype eq 'REMINDERLINK'}
													{assign var="recordcount" value=$obj->recordcount}
													{else}
													{assign var="recordcount" value=0}
													{/if}
													{assign var="onclick" value=""}
													{if stripos($obj->getUrl(), 'javascript:') === 0}
														{assign var="onclick" value="onclick="|cat:$href}
														{assign var="href" value="javascript:;"}
													{/if}
													<li>
														{if $recordcount neq ''}
															<a target="{$obj->target}" id="menubar_item_right_{Vtiger_Util_Helper::replaceSpaceWithUnderScores($label)}" {if $label=='Switch to old look'}switchLook{/if} href="{$href}" {$onclick}><font size="2">{vtranslate($label,$MODULE)}</font>(<font size="2" color="red">{$recordcount}件</font>)</a>
														{else}
															{if $label eq 'WorkSummarize'}
															<a id="menubar_item_right_{Vtiger_Util_Helper::replaceSpaceWithUnderScores($label)}" {if $label=='Switch to old look'}switchLook{/if} href="{$href}" {$onclick}>{vtranslate($label,$MODULE)}</a>
															{else}
															<a id="menubar_item_right_{Vtiger_Util_Helper::replaceSpaceWithUnderScores($label)}" {if $label=='Switch to old look'}switchLook{/if} href="{$href}" {$onclick}>{vtranslate($label,$MODULE)}</a>
															{/if}

														{/if}
													</li>
												{/if}
											{/foreach}
										</ul>
									{/if}
							</li>
						{/foreach}

					</ul>
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
	{*{assign var="announcement" value=$ANNOUNCEMENT->get('announcement')}*}

	<input type='hidden' value="{$MODULE}" id='module' name='module'/>
	<input type="hidden" value="{$PARENT_MODULE}" id="parent" name='parent' />
	<input type='hidden' value="{$VIEW}" id='view' name='view'/>
{/strip}
