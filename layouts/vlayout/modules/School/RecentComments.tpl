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
    <input name="schoolid" type="hidden" value="{$RECORDID}" />
	<div class="commentTitle row-fluid ">
		<div class="addCommentBlock ">
		    {*跟进目的和联系人*}
		    <div class="control-group">
				<table width="100%" class="form-inline">
	             <tr><td>
	             	<label class="control-label" for="smodcommentpurpose">{vtranslate('跟进目的','SchoolComments')} &nbsp;:&nbsp;</label>
						<select class="modcommentpurpose" name="smodcommentpurpose">
							<option value="维护感情">维护感情</option>
							<option value="沟通校招时间">沟通校招时间</option>
							<option value="沟通校招时间">确认学生报到时间</option>
						</select>
	             </td><td>
	             	<label class="control-label" for="modcommentcontacts">{vtranslate('联系人', 'SchoolComments')} &nbsp;: &nbsp;</label>
						<select class="contact_id" name="smodcommentcontacts">
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
				<textarea name="scommentcontents" class="commentcontent"  placeholder="{vtranslate('LBL_ADD_YOUR_COMMENT_HERE', $MODULE_NAME)}" rows="{$COMMENT_TEXTAREA_DEFAULT_ROWS}"></textarea>
			</div>
			<div>
			<div class="control-group">
			<table width="100%" class="form-inline">
                 <tr>
                     <!-- <td>
                          <label class="control-label" for="modcommenttype">{vtranslate('跟进类型', 'ModComments')} &nbsp;:&nbsp;</label>
                          <select class="modcommenttype" name="smodcommenttype">
								  <option value="商务签单未提供资料"> {vtranslate('商务签单未提供资料','ModComments')}</option>
                          </select>
                    </td> -->
                    <td colspan="2">
                        <label class="control-label" for="modcommentmode">{vtranslate('跟进方式', 'ModComments')} &nbsp;: &nbsp;</label>
                        <select class="modcommentmode" name="smodcommentmode">
                        	<option value="电话">{vtranslate('电话', 'ModComments')}</option>
                        	<option value="短信通知">{vtranslate('短信通知', 'ModComments')}</option>
                        	<option value="邮件">{vtranslate('邮件', 'ModComments')}</option>
                        	<option value="拜访">{vtranslate('拜访', 'ModComments')}</option>
                        	<option value="面谈">{vtranslate('面谈', 'ModComments')}</option>
                        </select>
                    </td>
                    <td></td>
                </tr>
			</table>
			</div>
			</div>
			<div class="pull-right">
				<button class="btn btn-success detailViewSaveSchoolComment" type="button" data-mode="add"><strong>{vtranslate('LBL_POST', $MODULE_NAME)}</strong></button>
			</div>
		</div>
		
	</div>
	


	<div class="commentsBody">
		
		{foreach key=index item=ITEM from=$TCOMMENTS}
			<div class="commentDetails bs-example">
					<div class="commentDiv">
						<div class="singleComment">
							<div class="commentInfoHeader row-fluid" data-commentid="{$ITEM['modcommentsid']}" data-parentcommentid="{$ITEM['modcommentsid']}">
								<div class="commentTitle">
									<div class="row-fluid">
										<div class="span1">
											<img class="alignMiddle pull-left" src="{vimage_path('DefaultUserIcon.png')}">
										</div>
										<div class="span11 commentorInfo">
											<div class="inner">
												<span class="commentorName"><strong>{$ITEM['creatorid']}&nbsp;<span class="label label-a_normal"></span></strong> </span>
												<span class="pull-right">
													<p class="muted">
													<em>
													跟进目的：
													{$ITEM['smodcommentpurpose']}

													</em>&nbsp;&nbsp;&nbsp;
													<em>
													{*
													跟进方式：
													{$ITEM['smodcommentmode']}*}

													</em>&nbsp;&nbsp;&nbsp;
													<small title="">跟进日期：{$ITEM['addtime']}</small> </p>
												</span>
												<div class="clearfix"></div>
												<div class="commentInfoContent">
													<style>
													h4{
													font-size:14px;
													font-weight:500;
													font-family: 'Helvetica Neue', Helvetica, 'Microsoft Yahei', 'Hiragino Sans GB', 'WenQuanYi Micro Hei', sans-serif;
													}
													</style>
													<div class="bs-callout bs-callout-info">
													<h4>跟进目的：{$ITEM['smodcommentpurpose']}  
													&nbsp;
													联系人:<span class="" data-field-type="reference" data-field-name="contact_id">
													{$ITEM['contact_id']} 
													</span>
													</h4>
													{$ITEM['commentcontent']} 
													
													</div>
												</div>
											</div>
										</div>

									</div>

								</div>
							</div>
						</div>
					</div>
			</div>


		{/foreach}
		




        <div style="clear:both;"></div>
	</div>
    


</div>

<script>
//Vtiger_Index_Js.registerTooltipEvents();
</script>
{/strip}