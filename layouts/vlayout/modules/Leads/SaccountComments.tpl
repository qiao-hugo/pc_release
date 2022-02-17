
	<div class="commentsBody">


        <div class="commentDetails bs-example">
            <div class="commentDiv">
                <div class="singleComment">
                    <div class="commentInfoHeader row-fluid">
                        <div class="commentTitle">

                            <div class="row-fluid">

                                <div class="span11 commentorInfo">
                                    {assign var=COMMENTOR value=$COMMENT->getCommentedByModel()}
                                    <div class="inner">
                                        <span class="commentorName"><strong>'+value.username+'&nbsp;</strong> </span>
												<span class="pull-right">
													<p class="muted">跟进类型 : '+value.modcommenttype+' 跟进方式 : '+value.modcommentmode+' <em>跟进时间</em>&nbsp;
                                                        <small title="">'+value.addtime+'</small> </p>
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
                                            <h4>跟进目的：'+value.modcommentpurpose+'
                                                &nbsp;
                                                联系人:<span class="" data-field-type="reference" data-field-name="contact_id">
												'+value.contactname+'
												</span>
                                            </h4>
                                            '+value.commentcontent+'
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
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

