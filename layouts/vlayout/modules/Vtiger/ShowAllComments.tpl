
{* Change to this also refer: RecentComments.tpl *}

{assign var="COMMENT_TEXTAREA_DEFAULT_ROWS" value="2"}
<div class="commentContainer">
    <input id="accountId" type="hidden" value="{$ACCOUNTID}" />
	<div class="commentTitle row-fluid">
		<div class="addCommentBlock">
		    {*跟进目的和联系人*}
		    <div class="control-group">
				<table width="100%" class="form-inline">
	             <tr><td>
	             	<label class="control-label" for="modcommentpurpose">{vtranslate('LBL_modcommentpurpose', 'Modcomments')} &nbsp;:&nbsp;</label>
						<input name="modcommentpurpose" class="modcommentpurpose"></input>
	             </td><td>
	             	<label class="control-label" for="modcommentcontacts">{vtranslate('LBL_modcommentcontacts', 'Modcomments')} &nbsp;: &nbsp;</label>
						<select class="modcommentcontacts" name="modcommentcontacts">
			              {foreach key=index item=MODCOMMENTContacts from=$MODCOMMENTCONTACTS}
			              <option value="{$MODCOMMENTContacts['contactid']}">{$MODCOMMENTContacts['name']}</option>
			              {/foreach}
			            </select>
				</td><td>
				</td></tr>
				</table>
			</div>
			<div>
				<textarea name="commentcontent" rows="{$COMMENT_TEXTAREA_DEFAULT_ROWS}" class="commentcontent"  placeholder="{vtranslate('LBL_ADD_YOUR_COMMENT_HERE', $MODULE_NAME)}"></textarea>
			</div>
			<div class="control-group">
			<table width="100%" class="form-inline">
             <tr><td> <label class="control-label" for="modcommenttype">{vtranslate('LBL_modcommenttype', 'Modcomments')} &nbsp;:&nbsp;</label>
              <select class="modcommenttype" name="modcommenttype">
              {foreach key=index item=COMMENTtype from=$COMMENTSTYPE}
              <option value="{$COMMENTtype}">{vtranslate($COMMENTtype, 'Modcomments')}</option>
              {/foreach}
              </select>
            </td><td>
              <label class="control-label" for="modcommentmode">{vtranslate('LBL_modcommentmode', 'Modcomments')} &nbsp;: &nbsp;</label>
			<select class="modcommentmode" name="modcommentmode">
			{foreach key=index item=COMMENTMode from=$COMMENTSMODE}
			<option value="{$COMMENTMode}">{vtranslate($COMMENTMode, 'Modcomments')}</option>
			{/foreach}
			</select>
			</td></tr>
			</table>
			</div>
			<div class="pull-right">
				<button class="btn btn-success saveComment" type="button" data-mode="add"><strong>{vtranslate('LBL_POST', $MODULE_NAME)}</strong></button>
			</div>
		</div>
	</div>
	<div class="commentsList commentsBody">
		{include file='CommentsList.tpl'|@vtemplate_path}
	</div>
	<div class="hide basicAddCommentBlock">
		<div class="row-fluid">
			<span class="span1">&nbsp;</span>
			<div class="span11">
				<textarea class="commentcontenthidden fullWidthAlways commenthistory"" rows="{$COMMENT_TEXTAREA_DEFAULT_ROWS}" name="commentcontent" placeholder="{vtranslate('LBL_ADD_YOUR_COMMENT_HERE', $MODULE_NAME)}"></textarea>
			</div>
		</div>
		<div class="pull-right">
			<button class="btn btn-success saveComment" type="button" data-mode="edit"><strong>{vtranslate('LBL_POST', $MODULE_NAME)}</strong></button>
			<a class="cursorPointer closeCommentBlock" type="reset">{vtranslate('LBL_CANCEL', $MODULE_NAME)}</a>
		</div>
	</div>
		<div class="hide basicEditCommentBlock" style="min-height: 150px;">
		<div class="row-fluid">
			<span class="span1">&nbsp;</span>
			<div class="span11">
				<input type="text" name="reasonToEdit" placeholder="{vtranslate('LBL_REASON_FOR_CHANGING_COMMENT', $MODULE_NAME)}" class="input-block-level commentcontenthidden"/>
			</div>
		</div>
		<div class="row-fluid">
			<span class="span1">&nbsp;</span>
			<div class="span11">
				<textarea class="commentcontenthidden fullWidthAlways" name="commentcontent" rows="{$COMMENT_TEXTAREA_DEFAULT_ROWS}"></textarea>
			</div>
		</div>
		<div class="pull-right">
			<button class="btn btn-success saveComment" type="button" data-mode="edit"><strong>{vtranslate('LBL_POST', $MODULE_NAME)}</strong></button>
			<a class="cursorPointer closeCommentBlock cancelLink" type="reset">{vtranslate('LBL_CANCEL', $MODULE_NAME)}</a>
		</div>
	</div>
</div>