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
	<style>
		.followup11toyes{
			display: none;
		}

		.followup11tono{
			display: none;
		}
	</style>
<div class="commentContainer">
     <input id="accountId" type="hidden" value="{$ACCOUNTID}" />
	<div class="commentTitle row-fluid ">
		<div class="addCommentBlock ">
		    {*跟进目的和联系人*}
		    <div class="control-group">
				<table width="100%" class="form-inline">
	             <tr><td>

						 <label class="control-label" for="modcommenttype">{vtranslate('LBL_modcommenttype', 'ModComments')} &nbsp;:&nbsp;</label>
						 <select class="modcommenttype" name="modcommenttype">
							 <option value="">请选择一个选项</option>
							 {foreach key=index item=COMMENTtype from=$COMMENTSTYPE}
								 <option value="{$COMMENTtype}">{vtranslate($COMMENTtype, 'ModComments')}</option>
							 {/foreach}
							 {*<!--普及版-->
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
                               {/if}*}
						 </select>
	             </td><td>
								<label class="control-label" for="modcommenttype"><span style="color: red">*</span>{vtranslate('LBL_intentionality', 'ModComments')} &nbsp;:&nbsp;</label>
								<select class="accountintentionality" name="accountintentionality">
									<option value="">请选择一个选项</option>
									{foreach key=index item=COMMENTtype from=$ACCOUNTINTENTIONALITY}
										<option value="{$index}">{vtranslate($COMMENTtype, 'ModComments')}</option>
									{/foreach}
							</td>
							</td></tr>
					</table>
				</div>
				<input type="hidden" name="is_service" class="is_service" value="{$servicecomment}">

			<div>
			<div class="control-group">
			<table width="100%" class="form-inline">
                 <tr>
                     <td>
						 <label class="control-label" for="modcommentcontacts">{vtranslate('LBL_modcommentcontacts', 'ModComments')} &nbsp;: &nbsp;</label>
						 <select class="modcommentcontacts" name="modcommentcontacts">
							 <option value="">请选择一个选项</option>
							 {foreach key=index item=MODCOMMENTContacts from=$MODCOMMENTCONTACTS}
								 <option value="{$MODCOMMENTContacts['contactid']}">{$MODCOMMENTContacts['name']}</option>
							 {/foreach}
						 </select>
                    </td>
                    <td>
                        <label class="control-label" for="modcommentmode">{vtranslate('LBL_modcommentmode', 'ModComments')} &nbsp;: &nbsp;</label>
                        <select class="modcommentmode" name="modcommentmode">
							<option value="">请选择一个选项</option>
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
			<div>
				<div id="firstInput" class="span12" style="margin:5px;display:none;">
					<div class="accordion-group">
						<div class="modal-footer" style="text-align:left;height:15px;line-height: 15px;">
							<h4 style="color: #FF9E0E;margin:0;padding:0;"><首次客户录入系统跟进>跟进内容规范提示</h4>
						</div>
						<div>
							<div class="accordion-body collapse in">
								<div class="accordion-inner">
									<table width="100%" class="form-inline">
										<tbody>
										<tr>
											<td class="span3">
												<label class="control-label" for="modcommentcontacts">1.客户资料来源</label>
											</td>
											<td class="span9"><input type="text" class="span12" data-thisid="1" name="followup[1]"/></td>
										</tr>
										<tr>
											<td>
												<label class="control-label" for="modcommentcontacts">2.客户语气态度</label>
											</td>
											<td><input type="text" class="span12"  data-thisid="2" name="followup[2]"/></td>
										</tr>
										<tr>
											<td>
												<label class="control-label" for="modcommentcontacts"> 3.是否了解过珍岛</label>

											</td>
											<td><input type="text" class="span12"  data-thisid="3" name="followup[3]"/></td>
										</tr>
										<tr>
											<td>
												<label class="control-label" for="modcommentcontacts"> 4.客户质量</label>
											</td>
											<td></td>
										</tr>
										<tr>
											<td>
												<label class="control-label" for="modcommentcontacts"> &nbsp; &nbsp;&nbsp;①注册时间</label>

											</td>
											<td><input type="text" class="span12" data-thisid="4" name="followup[4]"/></td>
										</tr>
										<tr>
											<td>
												<label class="control-label" for="modcommentcontacts"> &nbsp; &nbsp;&nbsp;①注册资金</label>

											</td>
											<td><input type="text" class="span12" data-thisid="5" name="followup[5]"/></td>
										</tr>
										<tr>
											<td>
												<label class="control-label" for="modcommentcontacts"> &nbsp; &nbsp;&nbsp;①法人还是股东</label>

											</td>
											<td><input type="text" class="span12" data-thisid="6" name="followup[6]"/></td>
										</tr>
										<tr>
											<td>
												<label class="control-label" for="modcommentcontacts"> &nbsp; &nbsp;&nbsp;②意向点</label>

											</td>
											<td><input type="text" class="span12" data-thisid="7" name="followup[7]" placeholder="是否主动让加微信，客户微信同意情况，电话通话时间，客户问的问题"/></td>
										</tr>
										<tr>
											<td>
												<label class="control-label" for="modcommentcontacts">&nbsp; &nbsp;&nbsp;③客户行业和产品</label>

											</td>
											<td><input type="text" class="span12" data-thisid="8" name="followup[8]"/></td>
										</tr>
										<tr>
											<td span="">
												<label class="control-label" for="modcommentcontacts">5.邀约是否成功</label>

											</td>
											<td>
												<input type="hidden"  for="modcommentcontacts"  class="span12" data-thisid="11" name="followup[11]"/>
												<select class="span12"   data-thisid="11" name="followupinviteres" id="followupinviteres">
													<option value="">请选择一个选项</option>
													<option value="是">是</option>
													<option value="否">否</option>
												</select>
											</td>
											{*<td><input type="text" class="span12" data-thisid="11" name="followup[11]" placeholder="（邀约成功可不写）"/></td>*}
										</tr>
										<tr class="followup11toyes">
											<td>
												<label class="control-label" for="modcommentcontacts"> &nbsp; &nbsp;&nbsp;①邀约人物</label>

											</td>
											<td><input type="text" class="span12" data-thisid="12" name="followup[12]" placeholder=""/></td>
										</tr>
										<tr class="followup11toyes">
											<td>
												<label class="control-label" for="modcommentcontacts"> &nbsp; &nbsp;&nbsp;②邀约时间</label>

											</td>
											<td><input type="text" class="span12" data-thisid="13" name="followup[13]" placeholder=""/></td>
										</tr>
										<tr class="followup11toyes">
											<td>
												<label class="control-label" for="modcommentcontacts"> &nbsp; &nbsp;&nbsp;③邀约地点</label>

											</td>
											<td><input type="text" class="span12" data-thisid="14" name="followup[14]" placeholder=""/></td>
										</tr>
										<tr  class="followup11toyes">
											<td>
												<label class="control-label" for="modcommentcontacts"> &nbsp; &nbsp;&nbsp;④所谈业务</label>

											</td>
											<td><input type="text" class="span12" data-thisid="15" name="followup[15]" placeholder=""/></td>
										</tr>

										<tr  class="followup11tono">
											<td span="">
												<label class="control-label" for="modcommentcontacts"> &nbsp; &nbsp;&nbsp;①本次未能邀约见面的原因</label>

											</td>
											<td><input type="text" class="span12" data-thisid="9" name="followup[9]" placeholder=""/></td>
										</tr>
										<tr  class="followup11tono">
											<td>
												<label class="control-label" for="modcommentcontacts"> &nbsp; &nbsp;&nbsp;②预约下次电话的时间</label>

											</td>
											<td><input type="text" class="span12" data-thisid="10" name="followup[10]" placeholder=""/></td>
										</tr>
										<tr><td>&nbsp;</td><td></td></tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div id="firstVisit" class="span12" style="margin:5px;display:none;">
					<div class="accordion-group">
						<div class="modal-footer" style="text-align:left;height:15px;line-height: 15px;">
							<h4 style="color: #FF9E0E;margin:0;padding:0;"><首次拜访客户后跟进>跟进内容规范提示：</h4>
						</div>
						<div>
							<div class="accordion-body collapse in">
								<div class="accordion-inner">
									<table width="100%" class="form-inline">
										<tbody>
										<tr>
											<td class="span3">
												<label class="control-label" for="modcommentcontacts">1.公司规模，公司大概多少人</label>
											</td>
											<td class="span9"><input type="text" class="span12" data-thisid="1" name="followupvisit[1]"/></td>
										</tr>
										<tr>
											<td>
												<label class="control-label" for="modcommentcontacts">2.拜访的负责人/老板</label>
											</td>
											<td><input type="hidden" data-thisid="2" value="KP" name="followupvisit[2]"/>
												<input type="radio"   value="KP" name="leader" checked placeholder="（若是负责人，写清楚具体职位）"/>KP    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
												<input type="radio"   value="非KP" name="leader" placeholder="（若是负责人，写清楚具体职位）"/>非KP</td>
										</tr>
										<tr>
											<td>
												<label class="control-label" for="modcommentcontacts"> 3.拜访人性格描述</label>

											</td>
											<td><input type="text" class="span12"  data-thisid="3" name="followupvisit[3]"/></td>
										</tr>
										<tr>
											<td>
												<label class="control-label" for="modcommentcontacts"> 4.拜访人年龄阶段/老家哪里</label>
											</td>
											<td><input type="text" class="span12" data-thisid="4" name="followupvisit[4]"/></td>
										</tr>
										<tr>
											<td>
												<label class="control-label" for="modcommentcontacts"> 5.客户目前的网络现状</label>

											</td>
											<td><input type="text" class="span12" data-thisid="5" name="followupvisit[5]" placeholder="比如投入了那些平台，花了多少钱，做了大概多久"/></td>
										</tr>
										<tr>
											<td>
												<label class="control-label" for="modcommentcontacts"> 6.客户问了哪些问题</label>

											</td>
											<td><input type="text" class="span12" data-thisid="6" name="followupvisit[6]"/></td>
										</tr>
										<tr>
											<td>
												<label class="control-label" for="modcommentcontacts"> 7.整个面谈过程中，客户对那几个点比较感兴趣</label>

											</td>
											<td><input type="text" class="span12" data-thisid="7" name="followupvisit[7]"/></td>
										</tr>
										<tr>
											<td>
												<label class="control-label" for="modcommentcontacts"> 8.关于我们谈判中给到客户方的信息&nbsp;: &nbsp;</label>

											</td>
											<td></td>
										</tr>
										<tr>
											<td>
												<label class="control-label" for="modcommentcontacts">&nbsp;&nbsp;①谈的什么产品/版本/年限</label>

											</td>
											<td><input type="text" class="span12" data-thisid="8" name="followupvisit[8]"/></td>
										</tr>
										<tr>
											<td>
												<label class="control-label" for="modcommentcontacts">&nbsp;&nbsp;②给客户报价多少，是否提到优惠，如果提到了，提到的优惠是什么</label>

											</td>
											<td><input type="text" class="span12" data-thisid="9" name="followupvisit[9]"/></td>
										</tr>
										<tr>
											<td>
												<label class="control-label" for="modcommentcontacts">&nbsp;&nbsp;③当时给客户介绍的案例是哪些</label>

											</td>
											<td><input type="text" class="span12" data-thisid="10" name="followupvisit[10]"/></td>
										</tr>
										<tr>
											<td>
												<label class="control-label" for="modcommentcontacts">&nbsp;&nbsp;④客户没有当场签单的原因</label>

											</td>
											<td><input type="text" class="span12" data-thisid="11" name="followupvisit[11]"/></td>
										</tr>
										<tr><td>&nbsp;</td><td></td></tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div id="firstInput1" style="display:none;border-left: 5px solid #f0ad4e;border-radius: 3px;padding-left: 20px;box-shadow:2.9px 3.99px 3px  rgba(0,0,0,.5)"><h5 style="color: #FF9E0E;"><首次客户录入系统跟进>跟进内容规范提示：</h5>
					<p style="margin:2px;">
						1.客户资料来源？<br>
						2.客户语气态度？<br>
						3.是否了解过珍岛？<br>
						4.客户质量<br>
						①注册时间，注册资金，法人还是股东？<br>
						②意向点：是否主动让加微信，客户微信同意情况，电话通话时间，客户问的问题<br>
						③客户行业和产品<br>
						5.本次未能邀约见面的原因（邀约成功可不写）<br>
						6.预约下次电话的时间（邀约成功可不写）？<br>
					</p>
				</div>
				<div id="firstVisit1" style="display:none;border-left: 5px solid #f0ad4e;border-radius: 3px;padding-left: 20px;box-shadow:2.9px 3.99px 3px  rgba(0,0,0,.5) "><h5 style="color: #FF9E0E;"><首次拜访客户后跟进>跟进内容规范提示：</h5>
					<p style="margin: 2px;">
						1.公司规模，公司大概多少人<br>
						2.拜访的负责人/老板（若是负责人，写清楚具体职位）<br>
						3.拜访人性格描述<br>
						4.拜访人年龄阶段/老家哪里<br>
						5.客户目前的网络现状，比如投入了那些平台，花了多少钱，做了大概多久<br>
						6.客户问了哪些问题<br>
						7.整个面谈过程中，客户对那几个点比较感兴趣？<br>
					</p>
				</div>
				<textarea style="border-top: 0px solid red;" name="commentcontents" class="commentcontent"  placeholder="{vtranslate('LBL_ADD_YOUR_COMMENT_HERE', $MODULE_NAME)}" rows="{$COMMENT_TEXTAREA_DEFAULT_ROWS}"></textarea>
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
												<h4>{*跟进目的：{$COMMENT->get('modcommentpurpose')} *}
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
												{if $COMMENT->get('modcommenttype') eq '首次客户录入系统跟进' || $COMMENT->get('modcommenttype') eq '首次拜访客户后跟进'}
													{$COMMENT->getFollowUp($COMMENT->get('modcommenttype'),$COMMENT->get('commentcontent'))}
												{elseif $COMMENT->get('followrole') eq 1}
													{"/(\\n+)/"|preg_replace:'<br>':("/(\*#\*)+/"|preg_replace:'：':("/(#endl#)+/"|preg_replace:'<br>':$COMMENT->get('commentcontent')))}
												{else}
													{"/(\\n+)/"|preg_replace:'<br>':$COMMENT->get('commentcontent')}

												{/if}
												</div>
											</div>
											<div class="row-fluid">
												<div class="pull-right commentActions">
													<span>
														<button class="btn alertComment" data-name="JobAlerts" type="button" data-url="index.php?module=JobAlerts&amp;view=Boxs&amp;mode=setJobAlerts&amp;src_record={$COMMENT->getId()}&amp;accountid={$ACCOUNTID}"><strong>提醒</strong></button>
														&nbsp;&nbsp;
														
														<button class="btn replyComment" data-name="ModComments" type="button" data-url="index.php?module=ModComments&amp;view=Boxs&amp;mode=setSubModComments&amp;src_record={$COMMENT->getId()}&amp;relateModule=Accounts"><strong>评论</strong></button>
														
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
														创建于 {$COMMENTHISTORY['createdtime']} <a class="replyComment" data-name="ModComments" href="javascript:void(0);" type="button" data-url="index.php?module=ModComments&amp;view=Boxs&amp;mode=setSubModComments&amp;src_record={$COMMENTHISTORY['ModCommentsid']}&amp;record={$COMMENTHISTORY['id']}&amp;relateModule=Accounts"><strong>修改</strong></a> <span style="color: grey">{if $COMMENTHISTORY['accountintentionality'] neq '' and $COMMENTHISTORY['accountintentionality'] neq 'zeropercent'} {vtranslate('LBL_intentionality', 'ModComments')} : {vtranslate($COMMENTHISTORY['accountintentionality'], 'Accounts')}{/if}</span>
										
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