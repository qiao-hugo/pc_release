{strip}
	{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
	{include file='DetailViewBlockView.tpl'|@vtemplate_path:$MODULE_NAME RECORD_STRUCTURE=$RECORD_STRUCTURE MODULE_NAME=$MODULE_NAME}
	{if $MYUSER}
	<table class="table table-bordered equalSplit detailview-table">
	<thead>	
	<tr><th class="blockHeader">下属列表</th>
	</tr>
	<tr>
	<td>
	{foreach key=UID item=NAME from=$MYUSER}
		<label class="checkbox inline" id="user{$UID}"><input type="checkbox"  class="rmuser" value="{$UID}">{$NAME}</label>
	{/foreach}
	<br>
	<br>
	{assign var=USERLIST value=get_username_array('1=1')}
	<label class="checkbox">
      <input class="checkall" type="checkbox">全选
    </label>
    <div class="input-prepend">
	<button class="btn" type="button">转移到</button>
	  <select id="toid" class="chzn-select">
		{foreach key=USERID item=USERNAME from=$USERLIST}
		<option value="{$USERID}">{$USERNAME}</option>
		{/foreach}
	</select>
	<button type="button" class="btn btn-primary remove">转移</button>
    </div>
	
  </div>

	
	</td>
	</tr>
	</table>
	{/if}
    {if $VIEWUS eq 'Detail'}
	<div class="summaryWidgetContainer">
            <div class="widgetContainer_0" data-url="module=Users&action=ListAjax&record=321123&email={$EMAILD}&mode=getWeixinMessage&page=1&limit=5" data-name="ModComments">
                <div class="widget_header row-fluid">
                    <span class="span8 margin0px"><h4>移动端信息</h4></span>
                </div>
                <div class="widget_contents">
                </div>
            </div>
        </div>
        {include file='../Users/RecentActivities.tpl'|@vtemplate_path MODULE="Users"}
    {/if}	
{/strip}