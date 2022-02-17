{strip}
    <!DOCTYPE html>
    <html>
    <head>
        <title>
            {vtranslate($PAGETITLE, $MODULE_NAME)}
        </title>
        <link REL="SHORTCUT ICON" HREF="favicon.ico">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="renderer" content="webkit">
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />

        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

        <link rel="stylesheet" href="data/min/?b=libraries&f=jquery/chosen/chosen.css,jquery/jquery-ui/css/custom-theme/jquery-ui-1.8.16.custom.css,jquery/select2/select2.css,bootstrap/css/bootstrap.css,jquery/posabsolute-jQuery-Validation-Engine/css/validationEngine.jquery.css,guidersjs/guiders-1.2.6.css,jquery/pnotify/jquery.pnotify.default.css,jquery/pnotify/use for pines style icons/jquery.pnotify.default.icons.css" type="text/css" media="screen" />
        <!--<link rel="stylesheet" href="libraries/bootstrap/css/dataTables.bootstrap.css" type="text/css" media="screen" />-->
        <link rel="stylesheet" href="resources/styles2.css" type="text/css" media="screen" />


        <link rel="stylesheet" href="libraries/jquery/select2/select2.css" />
        <link rel="stylesheet" media="screen" type="text/css" href="libraries/jquery/datepicker/css/bootstrap-datepicker.min.css" />
        <link rel="stylesheet" media="screen" type="text/css" href="libraries/jquery/datetimepicker/bootstrap-datetimepicker.min.css" />
        {* For making pages - print friendly *}
        <style type="text/css">
            @media print {
                .noprint { display:none; }
            }
        </style>
    </head>

    <body data-skinpath="layouts/vlayout/skins/softed" data-language="zh_cn">
    {*<div id="js_strings" class="hide noprint">{Zend_Json::encode($LANGUAGE_STRINGS)}</div>*}
    {assign var=CURRENT_USER_MODEL value=Users_Record_Model::getCurrentUserModel()}
    <div id="page">
        <!-- container which holds data temporarly for pjax calls -->
        <div id="pjaxContainer" class="hide noprint"></div>
        <style>
            .followup11toyes{
                display: none;
            }
            .followup11tono{
                display: none;
            }
        </style>
<div class="span4">
    <span class="span5 pull-right">
        <span class="btn-group pull-right">
        </span>
    </span>
</span>
</div>
</div>
</div>
<div class="contents-topscroll">
    <div class="topscroll-div">
        &nbsp;
    </div>
</div>
<div class="relatedContents contents-bottomscroll">
    <div class="bottomscroll-div">
			{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
        <table class="table table-bordered listViewEntriesTable">
            <thead>
                <tr class="listViewHeaders">
                    {*{foreach item=HEADER_FIELD from=$RELATED_HEADERS}<th {if $HEADER_FIELD@last} colspan="2" {/if} nowrap class="{$WIDTHTYPE}">{if $HEADER_FIELD->get('column') eq 'access_count' or $HEADER_FIELD->get('column') eq 'idlists' }<a href="javascript:void(0);" class="noSorting">{vtranslate($HEADER_FIELD->get('label'), $RELATED_MODULE->get('name'))}</a>{elseif $HEADER_FIELD->get('column') eq 'time_start'}{else}<a href="javascript:void(0);" class="relatedListHeaderValues" data-nextsortorderval="{if $COLUMN_NAME eq $HEADER_FIELD->get('column')}{$NEXT_SORT_ORDER}{else}ASC{/if}" data-fieldname="{$HEADER_FIELD->get('column')}">{vtranslate($HEADER_FIELD->get('label'), $RELATED_MODULE->get('name'))}&nbsp;&nbsp;{if $COLUMN_NAME eq $HEADER_FIELD->get('column')}<img class="{$SORT_IMAGE} icon-white">{/if}</a>{/if}</th>{/foreach}*}
					{*HEADERFIELDS_LIST*}
					{foreach item=HEADER_FIELD from=$RELATED_HEADERS}
					<th nowrap class="{$WIDTHTYPE}"><a href="javascript:void(0);">{vtranslate($HEADER_FIELD, $RELATION_MODULENAME)}&nbsp;&nbsp;</a></th>
                    {/foreach}
					<th style="width:85px;"></th>

                </tr>
            </thead>
            {foreach item=RELATED_RECORD key=KEY from=$RELATED_RECORDS}
                {assign var=COLUMN_DATA value=$RELATED_RECORD->getData()}
                <tr class="listViewEntries" data-id='{$RELATED_RECORD->getId()}' {if $RELATED_MODULE->get('name') eq 'Contacts' AND $KEY eq 0  }{elseif $RELATED_MODULE->get('name') eq 'AutoTask'}data-recordUrl='index.php?module=AutoTask&view=Detail&record={$COLUMN_DATA["autoworkflowentityid"]}&source_record={$COLUMN_DATA["autoworkflowid"]}'{elseif $RELATED_MODULE->get('name') eq 'Potentials'}data-recordUrl='index.php?module=Potentials&view=Detail&record={$COLUMN_DATA["potentialid"]}&mode=showDetailViewByMode&requestMode=full'{else}data-recordUrl='{$RELATED_RECORD->getDetailViewUrl()}'{/if}>
				{assign var=COLUMN_FIELDNAME value=$RELATED_RECORD->getEntity()->column_fields}
                    {foreach item=HEADER_FIELD key=FIELDNAME from=$RELATED_HEADERS}
                        {assign var=RELATED_HEADERNAME value=$FIELDNAME}
							<td class="{$WIDTHTYPE}"  {if $RELATED_HEADERNAME neq 'taskremark'}nowrap{else}{/if}>
								{if isset($COLUMN_FIELDNAME[$RELATED_HEADERNAME])}
									{$RELATED_RECORD->getDisplayValue($RELATED_HEADERNAME)}
								{else}
									{if $RELATED_MODULE->get('name') eq 'AutoTask'&& $RELATED_HEADERNAME eq 'isaction'}
                                        {if $COLUMN_DATA[$RELATED_HEADERNAME] eq '0'}
                                            <span class="label label-warning">未开始</span>
                                        {elseif $COLUMN_DATA[$RELATED_HEADERNAME] eq '1'}
                                            <span class="label label-success">进行中</span>
                                        {elseif $COLUMN_DATA[$RELATED_HEADERNAME] eq '2'}
                                            <span class="label label-important">已结束</span>
                                        {/if}
                                    {else}
                                        {$COLUMN_DATA[$RELATED_HEADERNAME]}
                                     {/if}
								{/if}
							 </td>
                    {/foreach}
					<td>
					{if $RELATED_MODULE->get('name') eq 'Contacts' AND $KEY eq 0 }
                    {elseif $RELATED_MODULE->get('name') eq 'AutoTask'}
                    {else}
                        <div class="pull-right actions">
                            <span class="actionImages">

							</span>
                        </div>
					{/if}
                    </td>
                </tr>
            {/foreach}
        </table>
    </div>
</div>
</div>
</div>
</body>
<div id="dialog-message" class="hide">加载中... </div>
</html>
{/strip}
