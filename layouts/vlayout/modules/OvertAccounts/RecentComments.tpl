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
{* Change to this also refer: AddCommentForm.tpl *}
{assign var="COMMENT_TEXTAREA_DEFAULT_ROWS" value="2"}
<div class="commentContainer">
     <input id="accountId" type="hidden" value="{$ACCOUNTID}" />
	<div class="commentTitle row-fluid ">
		<div class="addCommentBlock ">
		    {*跟进目的和联系人*}
		    <div class="control-group">
				<table width="100%" class="form-inline">
	             <tr><td>
	             	<label class="control-label" for="modcommentpurpose">{vtranslate('LBL_modcommentpurpose', 'ModComments')} &nbsp;:&nbsp;</label>
						<select class="modcommentpurpose" name="modcommentpurpose">
							<option value="邀约拜访">邀约拜访</option>
							<option value="商谈合同">商谈合同</option>
							<option value="签订合同">签订合同</option>
							<option value="项目收账">项目收账</option>
							<option value="启动通知">启动通知</option>
							<option value="日常维护">日常维护</option>
							<option value="客服甩单">客服甩单</option>
							<option value="问题处理">问题处理</option>
						</select>
	             </td><td>
	             	<label class="control-label" for="modcommentcontacts">{vtranslate('LBL_modcommentcontacts', 'ModComments')} &nbsp;: &nbsp;</label>
						<select class="modcommentcontacts" name="modcommentcontacts">
			              {foreach key=index item=MODCOMMENTContacts from=$MODCOMMENTCONTACTS}
			              <option value="{$MODCOMMENTContacts['contactid']}">{$MODCOMMENTContacts['name']}</option>
			              {/foreach}
			            </select>
				</td><td>
				</td></tr>
				</table>
			</div>
			<input type="hidden" name="is_service" class="is_service" value="{$servicecomment}">
			<div>
				<textarea name="commentcontents" class="commentcontent"  placeholder="{vtranslate('LBL_ADD_YOUR_COMMENT_HERE', $MODULE_NAME)}" rows="{$COMMENT_TEXTAREA_DEFAULT_ROWS}"></textarea>
			</div>
			<div>
			<div class="control-group">
			<table width="100%" class="form-inline">
                 <tr>
                     <td>
                          <label class="control-label" for="modcommenttype">{vtranslate('LBL_modcommenttype', 'ModComments')} &nbsp;:&nbsp;</label>
                          <select class="modcommenttype" name="modcommenttype">
							  {foreach key=index item=COMMENTtype from=$COMMENTSTYPE}
									<option value="{$COMMENTtype}">{vtranslate($COMMENTtype, 'ModComments')}</option>
							  {/foreach}
								  <!--普及版-->
								{if $double_type eq 'common'}							   	  
								  <option value="商务签单未提供资料"> {vtranslate('商务签单未提供资料','ModComments')}</option>
								  <option value="收到资料的时间"> {vtranslate('收到资料的时间','ModComments')}</option>
								  <option value="商务/客户已提交信息收集表"> {vtranslate('商务/客户已提交信息收集表', 'ModComments')}</option>
								  <option value="给客户开通双推发布宝账号">  {vtranslate('给客户开通双推发布宝账号', 'ModComments')}</option>
								 {else if $double_type eq 'yellow_glod'} 								  
								  <!--黄金版本--> 								  
								   <option value="商务签单未提供云发布信息收集表">{vtranslate('商务签单未提供云发布信息收集表', 'ModComments')}</option>
								   <option value="收到资料的时间"> {vtranslate('收到资料的时间','ModComments')}</option>
								   <option value="商务/客户已提交信息收集表">{vtranslate('商务/客户已提交信息收集表', 'ModComments')}</option>
								   <option value="拓词否">拓词否</option>
								   <option value="操作部门拓词中">{vtranslate('操作部门拓词中', 'ModComments')}</option>
								   <option value="与客户确认关键词">{vtranslate('与客户确认关键词', 'ModComments')}</option>
								   <option value="拓词完成时间">{vtranslate('拓词完成时间', 'ModComments')}</option>
								   <option value="完成拓词，项目操作中">{vtranslate('完成拓词，项目操作中', 'ModComments')}</option>
								   <option value="双推发布宝项目截稿">{vtranslate('双推发布宝项目截稿', 'ModComments')}</option>
								   <option value="正常维护">{vtranslate('正常维护', 'ModComments')}</option>
								  {else if $double_type eq 'white_gold'}
								  <!--白金版本-->								 
								  <option value="商务签单未提供云发布信息收集表">{vtranslate('商务签单未提供云发布信息收集表', 'ModComments')}</option>
								  <option value="收到资料的时间"> {vtranslate('收到资料的时间','ModComments')}</option>
								  <option value="商务/客户已提交信息收集表">{vtranslate('商务/客户已提交信息收集表', 'ModComments')}</option>
								  <option value="拓词否">拓词否</option>
								  <option value="操作部门拓词中">{vtranslate('操作部门拓词中', 'ModComments')}</option>
								  <option value="与客户确认关键词">{vtranslate('与客户确认关键词', 'ModComments')}</option>
								  <option value="拓词完成时间">{vtranslate('拓词完成时间', 'ModComments')}</option>								 
								 <option value="完成拓词，项目操作中">{vtranslate('完成拓词，项目操作中', 'ModComments')}</option>
								  <option value="双推发布宝项目截稿">{vtranslate('双推发布宝项目截稿', 'ModComments')}</option>
								  <option value="正常维护">{vtranslate('正常维护', 'ModComments')}</option>
								  <option value="商务签单未提供手机站/PC站资料">{vtranslate('商务签单未提供手机站/PC站资料', 'ModComments')}</option>
								  <option value="手机站/PC站资料提供">{vtranslate('手机站/PC站资料提供', 'ModComments')}</option>
								  <option value="手机站/PC站操作中">{vtranslate('手机站/PC站操作中', 'ModComments')}</option>
								  <option value="网站上传">{vtranslate('网站上传', 'ModComments')}</option>
								  {/if}
                          </select>
                    </td>
                    <td>
                        <label class="control-label" for="modcommentmode">{vtranslate('LBL_modcommentmode', 'ModComments')} &nbsp;: &nbsp;</label>
                        <select class="modcommentmode" name="modcommentmode">
                        {foreach key=index item=COMMENTMode from=$COMMENTSMODE}
                        <option value="{$COMMENTMode}">{vtranslate($COMMENTMode, 'ModComments')}</option>
                        {/foreach}
                        </select>
                    </td>
                    <td></td>
                </tr>
               {if $TASKNAME neq ''} <tr>
                    <td colspan="3"><div class="bs-callout bs-callout-warning">您本次跟进有T-云任务 <span class="label label-a_normal">{$TASKNAME}</span>,跟进请勾选 
                          <input class="updateautotask" type="checkbox" name="updateautotask" checked><label class="control-label"></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						  <h4>本次跟进任务清单:</h4>{$REMARK}</div>
                    </td>
                </tr>{/if}
			</table>
			</div>
			</div>
			<div class="pull-right">
				<button class="btn btn-success detailViewSaveComment" type="button" data-mode="add"><strong>{vtranslate('LBL_POST', $MODULE_NAME)}</strong></button>
			</div>
		</div>
		
	</div>
	


	<div class="commentsBody">
		{if !empty($COMMENTS)}
			{foreach key=index item=COMMENT from=$COMMENTS}
		
				<div class="commentDetails bs-example">
					<div class="commentDiv">
						<div class="singleComment">
							<div class="commentInfoHeader row-fluid" data-commentid="{$COMMENT->getId()}" data-parentcommentid="{$COMMENT->getId()}">
								<div class="commentTitle">
									{assign var=PARENT_COMMENT_MODEL value=$COMMENT->getParentCommentModel()}
									
									<div class="row-fluid">
										<div class="span1">
											{assign var=IMAGE_PATH value=$COMMENT->getImagePath()}
											<img class="alignMiddle pull-left" src="{if !empty($IMAGE_PATH)}{$IMAGE_PATH}{else}{vimage_path('DefaultUserIcon.png')}{/if}">
										</div>
										<div class="span11 commentorInfo">
											{assign var=COMMENTOR value=$COMMENT->getCommentedByModel()}
											<div class="inner">
												<span class="commentorName"><strong>{$COMMENTOR->getName()}&nbsp;<span class="label label-a_normal">{$ROLE[$COMMENTOR->entity->roleid]|replace:'|—':''}</span></strong> </span>
												<span class="pull-right">
													<p class="muted">{vtranslate('LBL_modcommenttype', 'ModComments')} : {vtranslate($COMMENT->get('modcommenttype'), 'ModComments')} {if $COMMENT->get('accountintentionality') neq '' and $COMMENT->get('accountintentionality') neq 'zeropercent'} {vtranslate('LBL_intentionality', 'ModComments')} : {vtranslate($COMMENT->get('accountintentionality'), 'Accounts')}{/if} {vtranslate('LBL_modcommentmode', 'ModComments')} : {vtranslate($COMMENT->get('modcommentmode'), 'ModComments')} <em>{vtranslate('LBL_COMMENTED',$MODULE_NAME)}</em>&nbsp;
													<small title="{Vtiger_Util_Helper::formatDateTimeIntoDayString($COMMENT->getCommentedTime())}">{$COMMENT->getCommentedTime()}</small> </p>
												</span>
												<div class="clearfix"></div>
											</div>
											<div class="commentInfoContent">
												<style>
												h4{
												font-size:14px;
												font-weight:500;
												font-family: 'Helvetica Neue', Helvetica, 'Microsoft Yahei', 'Hiragino Sans GB', 'WenQuanYi Micro Hei', sans-serif;
												}
												</style>
												<div class="bs-callout bs-callout-info">
												<h4>跟进目的：{$COMMENT->get('modcommentpurpose')}   
												&nbsp;
												联系人:<span class="" data-field-type="reference" data-field-name="contact_id">
												{assign var=ISCONTACT value=$COMMENT->get('lastname')}
												{assign var=ISSHOUYAO value=$COMMENT->get('shouyao')}
												{if empty($ISCONTACT)}
													{if empty($ISSHOUYAO)}
													-
													{else}
													<a href="?module=Accounts&amp;view=Detail&amp;record={$COMMENT->get('contact_id')}" onclick="return false;" data-original-title="{$COMMENT->get('shouyao')}">{$COMMENT->get('shouyao')}</a>
						
													{/if}
												{else}
												<a href="?module=Contacts&amp;view=Detail&amp;record={$COMMENT->get('contact_id')}" onclick="return false;" data-original-title="{$COMMENT->get('lastname')}">{$COMMENT->get('lastname')}</a>
												{/if}
												</span>
												</h4>
												{$COMMENT->get('commentcontent')}
												</div>
											</div>
											<div class="row-fluid">
												<div class="pull-right commentActions">
													<span>
														<button class="btn alertComment" data-name="JobAlerts" type="button" data-url="index.php?module=JobAlerts&amp;view=Boxs&amp;mode=setJobAlerts&amp;src_record={$COMMENT->getId()}&amp;accountid={$ACCOUNTID}"><strong>提醒</strong></button>
														&nbsp;&nbsp;
														
														<button class="btn replyComment" data-name="ModComments" type="button" data-url="index.php?module=ModComments&amp;view=Boxs&amp;mode=setSubModComments&amp;src_record={$COMMENT->getId()}"><strong>评论</strong></button>
														
														</span>
														<span>
														{if $PARENT_COMMENT_MODEL neq false or $CHILD_COMMENTS_MODEL neq null}
															&nbsp;<span>|</span>&nbsp;
															<a href="javascript:void(0);" class="cursorPointer detailViewThread">{vtranslate('LBL_VIEW_THREAD',$MODULE_NAME)}</a>
														{/if}
													</span>
												</div>
											</div>
											{assign var="COMMENT_ALERTS_ROWS" value=$COMMENT->getAlerts()}
											{if !empty($COMMENT_ALERTS_ROWS)}
												<div class="alertInfoContent ">
													跟进提醒
													{foreach key=his item=COMMENT_ALERTS_ROW from=$COMMENT->getAlerts()}
														<div class="bs-callout bs-callout-danger">
														<h4>主题：<a target="_blank" href="index.php?module=JobAlerts&view=Detail&record={$COMMENT_ALERTS_ROW['jobalertsid']}">{$COMMENT_ALERTS_ROW['subject']}</a></h4>
														<h4>提醒时间：{$COMMENT_ALERTS_ROW['alerttime']}  提醒人:{$COMMENT_ALERTS_ROW['username']} 提醒状态:{vtranslate($COMMENT_ALERTS_ROW['alertstatus'],'JobAlerts')} 优先级:{vtranslate($COMMENT_ALERTS_ROW['taskpriority'],'JobAlerts')}</h4>
														{nl2br($COMMENT_ALERTS_ROW['alertcontent'])}
														</div>
													{/foreach}
												</div>
											{/if}
											
											
										</div>
									</div>
								</div>
							</div>
							<div class="row-fluid commentActionsContainer">
								
								<div class="row-fluid"  name="editStatus">
									
									<div class="row-fluid pushUpandDown2per">
									{assign var="PAGEHIS" value=count($COMMENT->getHistory())}
										{foreach key=his item=COMMENTHISTORY from=$COMMENT->getHistory()}
										<div class="bs-callout bs-callout-warning">
										<h4>
										
										{$PAGEHIS-$his}楼：
										{$COMMENTHISTORY['createdbyer']}
										创建于 {$COMMENTHISTORY['createdtime']} <a class="replyComment" data-name="ModComments" href="javascript:void(0);" type="button" data-url="index.php?module=ModComments&amp;view=Boxs&amp;mode=setSubModComments&amp;src_record={$COMMENTHISTORY['ModCommentsid']}&amp;record={$COMMENTHISTORY['id']}&amp;relateModule=Accounts"><strong>修改</strong></a><span style="color: grey">{if $COMMENTHISTORY['accountintentionality'] neq '' and $COMMENTHISTORY['accountintentionality'] neq 'zeropercent'} {vtranslate('LBL_intentionality', 'ModComments')} : {vtranslate($COMMENTHISTORY['accountintentionality'], 'Accounts')}{/if}</span>
										
										</h4>
										
										{$COMMENTHISTORY['modcommenthistory']}</p>
										
										{if empty($COMMENTHISTORY['modifiedcause']) eq false}<div class="bs-example">
										<h4>最后修改时间{$COMMENTHISTORY['modifiedtime']} </h4>
										
										修改原因{$COMMENTHISTORY['modifiedcause']}</div>
										{/if}
										
										
										</div>
										{/foreach}
										
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			{/foreach}
		{else}
			
		{/if}
        <div class="span2 pull-right"">
        {if !is_bool($PAGING_MODEL->isNextPageExists()) && $COMMENTSCOUNTS/$PAGING_MODEL->getCurrentPage() neq $PAGING_MODEL->getPageLimit()}
            <div class="pull-right">
                <a href="javascript:void(0)" class="moreRecentComments nexttopage"><span class="btn"  title="下一页"><i class="icon-chevron-right" title="下一页"></i></a>
            </div>
        {/if}
        <input type="hidden" value="{$PAGING_MODEL->getCurrentPage()}" class="nextpage" />
        {if !is_bool($PAGING_MODEL->isPrevPageExists())}
            <div class="pull-right">
                <a href="javascript:void(0)" class="moreRecentComments uptopage"><span class="btn" title="上一页"><i class="icon-chevron-left" title="上一页"></i></span></a>
            </div>
        {/if}
        </div>
        <div style="clear:both;"></div>
	</div>
    
    
    {*<input type="hidden" value="{$PAGING_MODEL->getCurrentPage()}" class="nextpage" />
	{if $PAGING_MODEL->isNextPageExists()}
		<div class="row-fluid">
			<div class="pull-right">
				<a href="javascript:void(0)" class="moreRecentComments nexttopage">下一页</a>
				
			</div>
		</div>
	{/if}
	{if $PAGING_MODEL->isPrevPageExists()}
		<div class="row-fluid">
			<div class="pull-right">
				<a href="javascript:void(0)" class="moreRecentComments uptopage">上一页</a>
			</div>
		</div>
	{/if}
    *}

</div>
<script>
//Vtiger_Index_Js.registerTooltipEvents();
</script>

{/strip}