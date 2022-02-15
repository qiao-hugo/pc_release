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
	<div class="commentTitle row-fluid ">
		<div class="addCommentBlock ">
		    {*跟进目的和联系人*}
		    <div class="control-group">
				<table width="100%" class="form-inline">

	             <tr><td>
	             	<label class="control-label" for="modcommentpurpose">　　跟进日期 &nbsp;:&nbsp;</label>
						 <input id="fllowupdate" type="text" class="input-large nameField" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="fllowupdate" value="{'Y-m-d'|date}" readonly>
	             </td><td><input type="checkbox" class="input-large nameField" id="hasaccess" name="hasaccess" value="1" data-fieldinfo="">已接入
				</td><td>


					 </td></tr>
					<tr><td>
							<label class="control-label" for="modcommentpurpose">下次跟进日期 &nbsp;:&nbsp;</label>
							<input id="nextdate" type="text" class="input-large nameField" name="nextdate" value=""  readonly>
						</td><td>
						</td><td>
						</td></tr>
				</table>
			</div>
			<div>
				<textarea name="currentprogess" id="currentprogess" class="commentcontent"  placeholder="目前进展" rows="{$COMMENT_TEXTAREA_DEFAULT_ROWS}"></textarea>
			</div>
			<div>
				<textarea name="nextwork" id="nextwork" class="commentcontent"  placeholder="下一步工作" rows="{$COMMENT_TEXTAREA_DEFAULT_ROWS}"></textarea>
			</div>
			<div>
				<textarea name="policeindicator" id="policeindicator" class="commentcontent"  placeholder="相关政策和指标" rows="{$COMMENT_TEXTAREA_DEFAULT_ROWS}"></textarea>
			</div>
			<div>

			</div>
			<div class="pull-right">
				<button class="btn btn-success detailSaveComment" type="button" data-mode="add"><strong>{vtranslate('LBL_POST', $MODULE_NAME)}</strong></button>
			</div>
	</div>

	</div>
	


	<div class="commentsBody">
		{if !empty($COMMENTS)}
			{foreach key=index item=COMMENT from=$COMMENTS}
				<div class="commentDetails bs-example">
					<div class="commentDiv">
						<div class="singleComment">
							<div class="commentInfoHeader row-fluid">
								<div class="commentTitle">


									<div class="row-fluid">
										<div class="span1">

										</div>
										<div class="span11 commentorInfo">
											<div class="inner"><img class="alignMiddle pull-left" src="{vimage_path('DefaultUserIcon.png')}">
												<span class="commentorName"><strong>{$COMMENT['creatorid']}&nbsp;</strong> </span>
												<span class="pull-right">
													<p class="muted">跟进日期:<em>{$COMMENT['fllowdate']}</em>&nbsp;
														　　下次跟进日期:<em>{$COMMENT['nextdate']}</em>&nbsp;
														　　{if $COMMENT['hasaccess'] eq 1}已接入{else}未接入{/if}　　
													<small title="222">　　添加时间:</small> <em>{$COMMENT['createdtime']}</em></p>
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
												<h4>目前进展：<span class="" data-field-type="reference" data-field-name="contact_id">
												</span>
												</h4>
                                                    {$COMMENT['currentprogess']}

													<h4>下一步工作：<span class="" data-field-type="reference" data-field-name="contact_id">
												</span>
													</h4>
                                                    {$COMMENT['nextwork']}

													<h4>相关政策和指标：<span class="" data-field-type="reference" data-field-name="contact_id">
												</span>
													</h4>
                                                    {$COMMENT['policeindicator']}
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
		{/if}
        {*<div class="span2 pull-right"">
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
        </div>*}
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
<script type="text/javascript">
$(function(){

});

</script>

{/strip}