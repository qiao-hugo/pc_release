{*<!--
/***************
** 弹出窗口的搜索和分页
* 
*
*****/
-->*}
{strip}
    <input type="hidden" id="parentModule" value="{$SOURCE_MODULE}"/>
    <input type="hidden" id="module" value="{$MODULE}"/>
    <input type="hidden" id="parent" value="{$PARENT_MODULE}"/>
    <input type="hidden" id="sourceRecord" value="{$SOURCE_RECORD}"/>
    <input type="hidden" id="sourceField" value="{$SOURCE_FIELD}"/>
    <input type="hidden" id="url" value="{$GETURL}" />
    <input type="hidden" id="multi_select" value="{$MULTI_SELECT}" />
    <input type="hidden" id="currencyId" value="{$CURRENCY_ID}" />
    <input type="hidden" id="relatedParentModule" value="{$RELATED_PARENT_MODULE}"/>
    <input type="hidden" id="relatedParentId" value="{$RELATED_PARENT_ID}"/>
    <input type="hidden" id="view" value="{$VIEW}"/>
    <div class="popupContainer row-fluid">
        <div class="span12">
            <div class="row-fluid">
                <div class="span6 row-fluid">
                    <span class="logo span5"><img src="test/logo/vtiger-crm-logo.png" /></span>
                </div>
            </div>
        </div>
    </div>
    <form class="form-horizontal popupSearchContainer">
        <div class="control-group margin0px">
            <span class="paddingLeft10px"><strong>{vtranslate('LBL_SEARCH_FOR')}</strong></span>
            <span class="paddingLeft10px"></span>
            <input type="text" placeholder="{vtranslate('LBL_TYPE_SEARCH')}" id="searchvalue"/>
            <span class="paddingLeft10px"><strong>{vtranslate('LBL_IN')}</strong></span>
            <span class="paddingLeft10px help-inline pushDownHalfper">
                <select style="width: 150px;" class="chzn-select help-inline" id="searchableColumnsList">
                    <option value="systemuser">用户账号</option>
                </select>
            </span>
            <span class="paddingLeft10px cursorPointer help-inline" id="popupSearchButton"><img src="{vimage_path('search.png')}" alt="{vtranslate('LBL_SEARCH_BUTTON')}" title="{vtranslate('LBL_SEARCH_BUTTON')}" /></span>
        </div>
    </form>
    {if $SOURCE_MODULE neq 'PriceBooks'}
        <div class="popupPaging">
            <div class="row-fluid">
                {if $MULTI_SELECT}{if !empty($LISTVIEW_ENTRIES)}<span class="actions span6">&nbsp;<button class="select btn"><strong>{vtranslate('LBL_SELECT', $MODULE)}</strong></button> </span>{/if}{/if}
           
    <span class="span12">
	<span class="pull-right">&nbsp;<input type="text" name="jumppage" value="" id="jumppage" class="input-small" style="width: 50px;" placeholder="跳转">&nbsp;</span>
		<span class="pagination pull-right" id="pagination">
			<ul class="pagination-demo">

			</ul>
		</span>
		

    </span>
        
        
    </div>
</div>

{/if}
{/strip}
