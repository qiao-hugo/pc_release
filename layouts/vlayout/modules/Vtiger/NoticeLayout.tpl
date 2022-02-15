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
<div class="notice_layout">
	<ul class="notice_list" data-loadingdata="{$LOADDATA}">

			<li {if $MSGSTATUS}class="hover state_display"{/if}><a href="#" class="state_style state_styleimg"><img src="layouts/vlayout/skins/softed/images/noticeico.png"></a>
				<div class="n_parent state_displays" {if $MSGSTATUS}style="display:block;"{/if}>
					<div class="n_t">{vtranslate('LBL_CRM_STSTEM_MESSAGE')}<span class="n_close">×</span></div>
					<dl class="n_dl">
						{if $LOADDATA}
							<img src="/layouts/vlayout/skins/softed/images/loading.gif" width="25" height="25">
						{else}
                        {foreach key=keymessage item=itemessage from=$MESSAGELINK}
                            {if $itemessage['recordcount'] eq 0 }
                            {else}
								<dd><a href="{$itemessage['linkurl']}" target="{$itemessage['target']}">您有【<em>{$itemessage['recordcount']}</em>】{vtranslate($itemessage['linklabel'])}</a></dd>
                            {/if}
                        {/foreach}
						{/if}
					</dl>
				</div>
				<i class="n_fg"></i>
			</li>
			<li><a href="#"><img src="layouts/vlayout/skins/softed/images/timeico.png"></a>
				<div class="n_parent">
					<div class="n_t">{vtranslate('LBL_CRM_REMINDER')}</div>
					<dl class="n_dl">
						{if $LOADDATA}
						<img src="/layouts/vlayout/skins/softed/images/loading.gif" width="25" height="25">
						{else}
                        {foreach key=keyremind item=itemremind from=$REMINDLINK}
                            {if $itemremind['recordcount'] eq 0 }
                            {else}
								<dd><a href="{$itemremind['linkurl']}" target="{$itemremind['target']}">{vtranslate($itemremind['linklabel'])}(<em>{$itemremind['recordcount']}件</em>)</a></dd>
                            {/if}
                        {/foreach}
						{/if}
					</dl>
				</div>
				<i class="n_fg"></i>
			</li>
			<li><a href="#"><img src="layouts/vlayout/skins/softed/images/letterico.png"></a>
				<div class="n_parent">
					<div class="n_t">{vtranslate('LBL_CRM_STANDING_INFORMATION')}</div>
					<dl class="n_dl">
						{if $LOADDATA}
						<img src="/layouts/vlayout/skins/softed/images/loading.gif" width="25" height="25">
						{else}
                        {foreach key=keyremindreadstate item=itemremindreadstate from=$REMINDLINKREADSTATE}
                            {if $itemremindreadstate['recordcount'] eq 0 }
                            {else}
								<dd><a href="{$itemremindreadstate['linkurl']}" target="{$itemremindreadstate['target']}">{vtranslate($itemremindreadstate['linklabel'])}(<em>{$itemremindreadstate['recordcount']}件</em>)</a></dd>
                            {/if}
                        {/foreach}
						{/if}
					</dl>
				</div>
				<i class="n_fg"></i>
			</li>
	</ul>
	</div>

<script type="text/javascript">
{literal}
        $(document).ready(function(){
			var loadingdata=$('.notice_list').data('loadingdata');
			var isloading=0
        	$('.notice_list').hover(function(){
				if(loadingdata){
					if(isloading==0){
						isloading=1;
						$.ajax({type: "GET",
							url: "/index.php?module=WorkFlowCheck&view=List&mode=getNoticesansc",
							success: function(data){
								$('#footmsg').html(data);
							}
						});
					}
				}
			});
            $('.notice_list >li').mouseover(function(){
                $(this).addClass('hover');
                $(this).find('.n_parent').css('display','block');
            });

            $('.notice_list >li').mouseout(function(){
                $(this).removeClass('hover');
                $(this).find('.n_parent').css('display','none');
            });
            $('.n_close').click(function(){
                $(this).parent().parent().css('display','none');
            });
            //为空时，设置 暂无信息
			if(loadingdata){
				var dd = 1;
			}else{
				var dd = $('.n_dl').find("dd").length;
			}

            if(dd == 0){
                //移除闪烁图片class样式
                $(".state_styleimg").removeClass("state_style");
                $(".state_display").removeClass("hover");
                $(".state_displays").css("display","none");
                //$('.n_dl').append("<dd><font color='red'>暂无信息！</font></dd>");
            }
            $(".n_close").click(function(){
                $.ajax({type: "GET",
                    url: "/index.php?module=WorkFlowCheck&view=List&mode=setNoticesStatus",
                    success: function(msg){
                        //alert( "Data Saved: " + msg );
                    }
                })
			});
        })
{/literal}
</script>
{/strip}