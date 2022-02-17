
{strip}
{include file="Header.tpl"|vtemplate_path:$MODULE_NAME}
{include file="BasicHeader.tpl"|vtemplate_path:$MODULE_NAME}

<div class="bodyContents">
	<div class="mainContainer row-fluid">
		{assign var=LEFTPANELHIDE value=$CURRENT_USER_MODEL->get('leftpanelhide')}
		<div class="contentsDiv {if $LEFTPANELHIDE neq '1'} span12{/if}marginLeftZero" id="rightPanel">
			{include file="SideBar.tpl"|vtemplate_path:$MODULE_NAME}
				{assign var="MODULE_NAME" value=$MODULE_MODEL->get('name')}
		<input id="recordId" type="hidden" value="{$RECORD->getId()}" />
		<div class="detailViewContainer">
			<div class="row-fluid detailViewTitle">
			
				
			</div>
			<div class="detailViewInfo row-fluid">
				<div class="{if $NO_PAGINATION} span12 {else} span10 {/if} details">
					<form id="detailView" data-name-fields='{ZEND_JSON::encode($MODULE_MODEL->getNameFields())}'>
						<div class="contents">

{/strip}