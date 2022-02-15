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
<hr>
<div class="commentDiv">
	<div class="singleComment">
		<div class="commentInfoHeader row-fluid" data-commentid="{$COMMENT->getId()}" data-parentcommentid="{$COMMENT->get('parent_comments')}">
			<div class="commentTitle" id="{$COMMENT->getId()}">
				{assign var=PARENT_COMMENT_MODEL value=$COMMENT->getParentCommentModel()}
				{assign var=CHILD_COMMENTS_MODEL value=$COMMENT->getChildComments()}
				<div class="row-fluid">
					<div class="span1">
						{assign var=IMAGE_PATH value=$COMMENT->getImagePath()}
						<img class="alignMiddle pull-left" src="{if !empty($IMAGE_PATH)}{$IMAGE_PATH}{else}{vimage_path('DefaultUserIcon.png')}{/if}">
					</div>
					<div class="span11 commentorInfo">
						{assign var=COMMENTOR value=$COMMENT->getCommentedByModel()}
											<div class="inner">
												<span class="commentorName"><strong>{$COMMENTOR->getName()}</strong> </span>
												<span class="pull-right">
													<p class="muted">{vtranslate('LBL_modcommenttype', 'Modcomments')} : {$COMMENT->get('modcommentmode')} {vtranslate('LBL_modcommentmode', 'Modcomments')} : {$COMMENT->get('modcommenttype')} <em>{vtranslate('LBL_COMMENTED',$MODULE_NAME)}</em>&nbsp;
													<small title="{Vtiger_Util_Helper::formatDateTimeIntoDayString($COMMENT->getCommentedTime())}">{$COMMENT->getCommentedTime()}</small> </p>
												</span>
												<div class="clearfix"></div>
											</div>
											<div class="commentInfoContent">
												{nl2br($COMMENT->get('commentcontent'))}
											</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid commentActionsContainer">
			<div class="row-fluid commentActionsDiv">
				<div class="pull-right commentActions">
					<span>
					
										<a class="cursorPointer alertComment feedback">
											<i class="icon-share-alt"></i>{vtranslate('LBL_alert','ModComments')}
										</a>
										&nbsp;&nbsp;
										<a class="cursorPointer replyComment feedback">
											<i class="icon-share-alt"></i>{vtranslate('LBL_modreplay','ModComments')}
										</a>
										</span>
										<span>
											{if $PARENT_COMMENT_MODEL neq false or $CHILD_COMMENTS_MODEL neq null}
												&nbsp;<span>|</span>&nbsp;
												<a href="javascript:void(0);" class="cursorPointer detailViewThread">{vtranslate('LBL_VIEW_THREAD',$MODULE_NAME)}</a>
											{/if}
										</span>
				</div>
			</div>
			{assign var="REASON_TO_EDIT" value=$COMMENT->get("modcommenthistory")}
								<div class="row-fluid"  name="editStatus">
									<hr style="border-color: gray;border-style: dashed;">
									<div class="row-fluid pushUpandDown2per">
										<span class="span12{if empty($REASON_TO_EDIT)} hide{/if}">
											{nl2p($REASON_TO_EDIT)}
										</span>
									</div>
								</div>
		</div>
					</div>
<div>
{/strip}

