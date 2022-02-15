{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
-->*}
{strip}
{*联系人放到首页*}
<div  class="summaryWidgetContainer">
	<div class="widget_header row-fluid">
		<span class="span8"><h4 class="textOverflowEllipsis">联系人</h4></span>
		<span class="span4"></span>
	</div>
	<div>
		<ul class="unstyled">
			<div class="bs-callout bs-callout-info">
				<li>
					<div>
						<span><i>首要联系人</i> :&nbsp;<strong>{$ENTITY_FIRST['linkname']}</strong></span>
						<span class="pull-right"><p class="muted"><small title=""></small></p></span>
					</div>
					<div>
						<span><i>性别</i> :&nbsp;<strong>{vtranslate($ENTITY_FIRST['gendertype'],$MODULE_NAME)}</strong></span>
						<span class="pull-right"><p class="muted"><small title=""></small></p></span>
					</div>
					<div>
						<span><i>手机</i> :&nbsp;<strong>{vtranslate($ENTITY_FIRST['mobile'],$MODULE_NAME)}</strong></span>
						<span class="pull-right"><p class="muted"><small title=""></small></p></span>
					</div>
					<div>
						<span><i>办公电话</i> :&nbsp;<strong>{vtranslate($ENTITY_FIRST['phone'],$MODULE_NAME)}</strong></span>
						<span class="pull-right"><p class="muted"><small title=""></small></p></span>
					</div>
					
					<div>
						<span><i>职务</i> :&nbsp;<strong>{vtranslate($ENTITY_FIRST['title'],$MODULE_NAME)}</strong></span>
						<span class="pull-right"><p class="muted"><small title=""></small></p></span>
					</div>
					<div>
						<span><i>决策圈</i> :&nbsp;<strong>{vtranslate($ENTITY_FIRST['makedecisiontype'],$MODULE_NAME)}</strong></span>
						<span class="pull-right"><p class="muted"><small title=""></small></p></span>
					</div>
					<div>
						<span><i>邮箱</i> :&nbsp;<strong>{vtranslate($ENTITY_FIRST['email1'],$MODULE_NAME)}</strong></span>
						<span class="pull-right"><p class="muted"><small title=""></small></p></span>
					</div>
					
				</li>
			</div>
		</ul>
		{if !empty($ALLCONTACTS)}
			<ul class="unstyled">
				{foreach item=RECENT_ACTIVITY from=$ALLCONTACTS}
					<div class="bs-callout bs-callout-warning">
						<li>
							<div>
								<span><i>联系人</i> :&nbsp;<a href="/index.php?module=Contacts&view=Detail&record={$RECENT_ACTIVITY['contactid']}"><strong>{$RECENT_ACTIVITY['name']}</strong></a></span>
								<span class="pull-right"><p class="muted"><small title=""></small></p></span>
							</div>
							<div>
								<span><i>性别</i> :&nbsp;<strong>{vtranslate($RECENT_ACTIVITY['gender'],$MODULE_NAME)}</strong></span>
								<span class="pull-right"><p class="muted"><small title=""></small></p></span>
							</div>
							<div>
								<span><i>手机</i> :&nbsp;<strong>{vtranslate($RECENT_ACTIVITY['mobile'],$MODULE_NAME)}</strong></span>
								<span class="pull-right"><p class="muted"><small title=""></small></p></span>
							</div>
							<div>
								<span><i>办公电话</i> :&nbsp;<strong>{vtranslate($RECENT_ACTIVITY['phone'],$MODULE_NAME)}</strong></span>
								<span class="pull-right"><p class="muted"><small title=""></small></p></span>
							</div>
							
							<div>
								<span><i>职务</i> :&nbsp;<strong>{vtranslate($RECENT_ACTIVITY['title'],$MODULE_NAME)}</strong></span>
								<span class="pull-right"><p class="muted"><small title=""></small></p></span>
							</div>
							<div>
								<span><i>决策圈</i> :&nbsp;<strong>{vtranslate($RECENT_ACTIVITY['makedecision'],$MODULE_NAME)}</strong></span>
								<span class="pull-right"><p class="muted"><small title=""></small></p></span>
							</div>
							<div>
								<span><i>邮箱</i> :&nbsp;<strong>{vtranslate($RECENT_ACTIVITY['email'],$MODULE_NAME)}</strong></span>
								<span class="pull-right"><p class="muted"><small title=""></small></p></span>
							</div>
						</li>
					</div>
				{/foreach}
			</ul>
		{/if}
	</div>
	{if $PAGING_MODEL->isNextPageExists()}
		<div class="row-fluid">
			<div class="pull-right">
				<a href="javascript:void(0)" class="moreRecentUpdates">{vtranslate('LBL_MORE',$MODULE_NAME)}..</a>
			</div>
		</div>
	{/if}
	<span class="clearfix"></span>
</div>

<div  class="summaryWidgetContainer">
    <div class="widget_header row-fluid">
        <span class="span8"><h4 class="textOverflowEllipsis">负责人&客  服 信息</h4></span>
        <span class="span4"></span>
    </div>
    <div>
        {if !empty($RECENT_ACTIVITIESAND)}
            <ul class="unstyled">
                <div class="bs-callout bs-callout-warning">
                    <li>
                        <div>
                            <span><i>负责人</i> :&nbsp;<strong>{$RECENT_ACTIVITIESAND['h']['last_name']}</strong></span>
                            <span class="pull-right"><p class="muted"><small title=""></small></p></span>
                        </div>
                        <div>
                            <span><i>邮箱</i> :&nbsp;<strong>{$RECENT_ACTIVITIESAND['h']['email1']}</strong></span>
                            <span class="pull-right"><p class="muted"><small title=""></small></p></span>
                        </div>
                        <div>
                            <span><i>手机</i> :&nbsp;<strong>{$RECENT_ACTIVITIESAND['h']['phone_mobile']}</strong></span>
                            <span class="pull-right"><p class="muted"><small title=""></small></p></span>
                        </div>
                        <div>
                            <span><i>办公电话</i> :&nbsp;<strong>{$RECENT_ACTIVITIESAND['h']['phone_work']}</strong></span>
                            <span class="pull-right"><p class="muted"><small title=""></small></p></span>
                        </div>
                    </li>
                </div>
            </ul>
            {if !empty($RECENT_ACTIVITIESAND['f'])}
            <ul class="unstyled">
                <div class="bs-callout bs-callout-warning">
                    <li>
                        <div>
                            <span><i>客服</i> :&nbsp;<strong>{$RECENT_ACTIVITIESAND['f']['last_name']}</strong></span>
                            <span class="pull-right"><p class="muted"><small title=""></small></p></span>
                        </div>
                        <div>
                            <span><i>邮箱</i> :&nbsp;<strong>{$RECENT_ACTIVITIESAND['f']['email1']}</strong></span>
                            <span class="pull-right"><p class="muted"><small title=""></small></p></span>
                        </div>
                        <div>
                            <span><i>手机</i> :&nbsp;<strong>{$RECENT_ACTIVITIESAND['f']['phone_mobile']}</strong></span>
                            <span class="pull-right"><p class="muted"><small title=""></small></p></span>
                        </div>
                        <div>
                            <span><i>办公电话</i> :&nbsp;<strong>{$RECENT_ACTIVITIESAND['f']['phone_work']}</strong></span>
                            <span class="pull-right"><p class="muted"><small title=""></small></p></span>
                        </div>
                    </li>
                </div>
            </ul>
            {/if}
        {/if}
    </div>
    {if $PAGING_MODEL->isNextPageExists()}
        <div class="row-fluid">
            <div class="pull-right">
                <a href="javascript:void(0)" class="moreRecentUpdates">{vtranslate('LBL_MORE',$MODULE_NAME)}..</a>
            </div>
        </div>
    {/if}
    <span class="clearfix"></span>
</div>

<div  class="summaryWidgetContainer">
	<div class="widget_header row-fluid">
		<span class="span8"><h4 class="textOverflowEllipsis">客  服</h4></span>
		<span class="span4"></span>
	</div>
	<div>
		{if !empty($RECENT_ACTIVITIES)}
			<ul class="unstyled">
				{foreach item=RECENT_ACTIVITY from=$RECENT_ACTIVITIES}
					<div class="bs-callout bs-callout-warning">
						<li>
							<div>
								<span><i>客服</i> :&nbsp;<strong>{$RECENT_ACTIVITY['last_name']}</strong></span>
								<span class="pull-right"><p class="muted"><small title=""></small></p></span>
							</div>
							{*
							<div class='font-x-small updateInfoContainer'>
								<i>服务时间</i> :&nbsp;
								
								<b>{$RECENT_ACTIVITY['starttime']}到{$RECENT_ACTIVITY['endtime']}</b>
							</div>
							*}
							<div class='font-x-small updateInfoContainer'>
								<i>备 注</i> :&nbsp;
								
								<b>{$RECENT_ACTIVITY['remark']}</b>
							</div>
						</li>
					</div>
				{/foreach}
			</ul>
			{else}
				<div class="bs-callout bs-callout-warning">
					<p class="textAlignCenter">暂未分配客服</p>
				</div>
		{/if}
	</div>
	{if $PAGING_MODEL->isNextPageExists()}
		<div class="row-fluid">
			<div class="pull-right">
				<a href="javascript:void(0)" class="moreRecentUpdates">{vtranslate('LBL_MORE',$MODULE_NAME)}..</a>
			</div>
		</div>
	{/if}
	<span class="clearfix"></span>
</div>


<div  class="summaryWidgetContainer">
	<div class="widget_header row-fluid">
		<span class="span8"><h4 class="textOverflowEllipsis">负责人</h4></span>
		<span class="span4"></span>
	</div>
	<div>
		{if !empty($RECENT_HEADS)}
			<ul class="unstyled">
				{foreach item=RECENT_HEAD from=$RECENT_HEADS}
					<div class="bs-callout bs-callout-warning">
						<li>
							<div class='font-x-small updateInfoContainer'>
								<i>负责人</i> :&nbsp;
								
								{$RECENT_HEAD['oldname']}&nbsp;&nbsp;&nbsp;更改为&nbsp;&nbsp;&nbsp;<b>{$RECENT_HEAD['newname']}</b>
								<span class="pull-right"><i>修改人</i> :&nbsp;{$RECENT_HEAD['mname']}&nbsp;<p class="muted"><small title="">{$RECENT_HEAD['createdtime']}</small></p></span>
							</div>
						</li>
					</div>
				{/foreach}
			</ul>
			{else}
				<div class="bs-callout bs-callout-warning">
					<p class="textAlignCenter">暂未记录</p>
				</div>
		{/if}
	</div>
	<span class="clearfix"></span>
</div>

{/strip}
