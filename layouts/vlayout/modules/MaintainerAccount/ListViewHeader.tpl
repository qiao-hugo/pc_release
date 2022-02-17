
        {strip}
        <div class="listViewPageDiv">{if !isset($smarty.get.report)}
                <div class="listViewTopMenuDiv noprint">
                    <div class="listViewActionsDiv row-fluid hide">
				<span class="btn-toolbar span4">
				<span style="display:none;">
                {*<select id="customFilter" style="display:none;"></select>*}
                </span>
				<span class="{if empty($smarty.get.public) eq false}hide{/if}">
					<span class="btn-group listViewMassActions">
						
                            
					</span>
                    {foreach item=LISTVIEW_BASICACTION from=$LISTVIEW_LINKS['LISTVIEWBASIC']}
                        <span class="btn-group">
							<button id="{$MODULE}_listView_basicAction_{Vtiger_Util_Helper::replaceSpaceWithUnderScores($LISTVIEW_BASICACTION->getLabel())}" class="btn addButton" {if stripos($LISTVIEW_BASICACTION->getUrl(), 'javascript:')===0} onclick='{$LISTVIEW_BASICACTION->getUrl()|substr:strlen("javascript:")};'{else} onclick='window.location.href="{$LISTVIEW_BASICACTION->getUrl()}"'{/if}><i class="icon-plus icon-white"></i>&nbsp;<strong>{vtranslate($LISTVIEW_BASICACTION->getLabel(), $MODULE)}</strong></button>
						</span>
                    {/foreach}</span>
				</span>
			<span class="btn-toolbar span4">
				<span class="customFilterMainSpan btn-group">

				</span>
			</span>
                    </div>
                </div>
            {/if}


            <div>
                {if !isset($smarty.get.report)}
                <form method="post"  name="SearchBug" id="SearchBug">
                    <input type="hidden" value="1" id="queryaction" name="queryaction">
                    <input type="hidden" value="" id="queryTitle" name="queryTitle">
                    <input type="hidden" value="0" id="saveQuery" name="saveQuery">
                    <input type="hidden" value="0" id="reset" name="reset">
                    <input type="hidden" value="" id="showField" name="showField">
                    <div id="SearchBlankCover" style="background-color: #F0F0F0;">
                            <table id="searchtable" style="margin:auto">

                                <tbody>
                                <tr>
                                    <td colspan="7">
                                        <center>
                                            <input type="button" value="提交查询" id="PostQuery" name="PostQuery" class="btn">
                                            <input type="button" onclick="location.reload();"value="重置查询" class="btn">
                                        </center>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        {include file='SearchJS.tpl'|@vtemplate_path MODULE=$MODULE}
                </form>
            </div>
            {include file='DefaultListFields.tpl'|@vtemplate_path MODULE=$MODULE ISDEFAULT=true ISPAGE=true}
            {/if}
            <div class="listViewContentDiv" id="listViewContents">
{/strip}