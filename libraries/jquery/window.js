$(document).ready(function() {
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
    var dd = $('.n_dl').find("dd").length;
    if(dd == 0){
        //移除闪烁图片class样式
        $(".state_styleimg").removeClass("state_style");
        $('.n_dl').append("<dd><font color='red'>暂无信息！</font></dd>");
    }
});


/*$(document).ready(function(){
	
	var rightwin = $("#right");
	
	
	setTimeout(function () {
		var pmn=app.getParentModuleName();mvn=app.getViewName();
		//后台访问不统计提醒 Edit By Joe @20150512
		if(pmn=='Settings' || mvn=='Edit'){
			return;
		}
		var windowobj = $(document);
		var cwinwidth = rightwin.outerWidth(true);
		var cwinheight = rightwin.outerHeight(true);
		var browserwidth = windowobj.width();
		var browserheight = windowobj.height();
		var	scrollLeft = windowobj.scrollLeft();
		var	scrollTop = windowobj.scrollTop();
		var rleft = scrollLeft + browserwidth - cwinwidth;
		if ($.browser.safari) {
			rleft = rleft - 15;
		}
		if ($.browser.opera) {
			rleft = rleft + 15;
		}
		if ($.browser.msie && $.browser.version.indexOf("8") >= 0) {
			rleft = rleft - 20;
		}
        $.ajax({ url: "/index.php?module=WorkFlowCheck&action=Confirmation",  success: function(data){
            numb=eval(data);
            if(numb.result.confirmalls>0 || numb.result.FollowUp>0 || numb.result.NoWrite>0 || numb.result.Replynum>0 || numb.result.outnumber>0 || numb.result.sevencustomer>0 || numb.result.Refuse>0){
            	var msg='';
                if(numb.result.confirmalls>0){
                   msg= '<p class="text-left">您有<a href="/index.php?module=WorkFlowCheck&view=List" title="查看要审核的信息">【<span class="text-error">'+numb.result.confirmalls+'</span>】</a>条待审核的信息</p>';
                }
                if(numb.result.FollowUp>0){
                    msg=msg+'<p class="text-left">您有<a href="/index.php?module=VisitingOrder&view=List&public=FollowUp" title="查看24小时跟进拜访单">【<span class="text-error">'+numb.result.FollowUp+'</span>】</a>条24小时待跟进拜访单</p>';
                }
                if(numb.result.NoWrite>0){
                    msg=msg+'<p class="text-left">有<a href="index.php?module=WorkSummarize&view=List&filter=nowrite" title="查看未写工作总结人员">【<span class="text-error">'+numb.result.NoWrite+'</span>】</a>人未写工作总结</p>';
                }
                if(numb.result.Replynum>0){
                	msg=msg+'<p class="text-left">您有<a href="index.php?module=WorkSummarize&view=List&filter=reply" title="查看要回复工作总结">【<span class="text-error">'+numb.result.Replynum+'</span>】</a>条工作总结需要回复</p>';
                }
                if(numb.result.outnumber>0){
                	msg=msg+'<p class="text-left">您有<a href="/index.php?module=WorkFlowCheck&view=List&public=outnumberday" title="查看超过24小时未审核的信息">【<span class="text-error">'+numb.result.outnumber+'</span>】</a>条超过24小时未审核信息</p>';
                }
                if(numb.result.sevencustomer>0){
                	msg=msg+'<p class="text-left">您有<a href="/index.php?module=ServiceComments&view=List&public=allnofollowday" title="查看全部需要跟进的客服">【<span class="text-error">'+numb.result.sevencustomer+'</span>】</a>条全部未跟进客服的信息</p>';
                }
                if(numb.result.Refuse>0){
                    msg=msg+'<p class="text-left">您有<a href="/index.php?module=SalesOrder&view=List&public=refuse" title="查看被打回的工单">【<span class="text-error">'+numb.result.Refuse+'</span>】</a>笔工单被打回</p>';
                }
                
                 $('#mssageCont').html(msg);
                 rightwin.mywin({left: "right", top: "bottom"}, function() {
                rightwin.hide();
		},{left: rleft, top: scrollTop + browserheight}).dequeue();
            }
        }
        });
		
	},1000);

});*/
/**
 *@param position表示窗口的最终位置,包含两个属性，一个是left，一个是top
 *@param hidefunc表示执行窗口隐藏的方法
 *@param initPos表示窗口初始位置，包含两个属性，一个是left，一个是top
 */
/*
$.fn.mywin = function(position, hidefunc, initPos) {
	if (position && position instanceof Object) {
		var positionleft = position.left;
		var positiontop = position.top;
		
		var left;
		var top;
		var windowobj = $(window);
		var currentwin = this;
		var cwinwidth;
		var cwinheight;

		var browserwidth;
		var browserheight;
		var scrollLeft;
		var scrollTop;
		//计算浏览器当前可视区域的宽和高，以及滚动条左边界，上边界的值
		function getBrowserDim() {
			browserwidth = windowobj.width();
			browserheight = windowobj.height();
			scrollLeft = windowobj.scrollLeft();
			scrollTop = windowobj.scrollTop();	
		}		
		//计算窗口真实的左边界值
		function calLeft(positionleft, scrollLeft, browserwidth, cwinwidth) {
			if (positionleft && typeof positionleft == "string") {
				if (positionleft == "center") {
					left = scrollLeft + (browserwidth - cwinwidth) / 2;	
				} else if (positionleft == "left") {
					left = scrollLeft;	
				} else if (positionleft == "right") {
					left = scrollLeft + browserwidth - cwinwidth;
					if ($.browser.safari) {
						left = left - 15;
					}
					if ($.browser.opera) {
						left = left + 15;
					}
					if ($.browser.msie && $.browser.version.indexOf("8") >= 0) {
						left = left - 20;
					}
				} else  {
					left = scrollLeft + (browserwidth - cwinwidth) / 2;	
				}
			} else if (positionleft && typeof positionleft == "number") {
				left = positionleft;
			} else {
				left = 0;
			}
		}
		//计算窗口真实的上边界值		
		function calTop(positiontop, scrollTop, browserheight, cwinheight) {
			if (positiontop && typeof positiontop == "string") {
				if (positiontop == "center") {
					top = scrollTop + (browserheight - cwinheight) / 2;
				} else if (positiontop == "top") {
					top = scrollTop;
				} else if (positiontop == "bottom") {
					top = scrollTop + browserheight - cwinheight-15;
					if ($.browser.opera) {
						top = top - 25;
					}
				} else {
					top = scrollTop + (browserheight - cwinheight) / 2;
				}
			} else if (positiontop && typeof positiontop == "number") {
				top = positiontop;
			} else {
				top = 0;
			}
		}
		//移动窗口的位置
		function moveWin() {
			calLeft(currentwin.data("positionleft"), scrollLeft, browserwidth, cwinwidth);
			calTop(currentwin.data("positiontop"), scrollTop, browserheight, cwinheight);
			currentwin.animate({
				left: left,
				top: top
			},600);	
		}
		
		//定义关闭按钮的动作
		currentwin.children(".title").children(".wtitle").click(function() {
			if (!hidefunc) {
				currentwin.hide("slow")	;
			} else {
				hidefunc();
			}
		});

		if (initPos && initPos instanceof Object) {
			var initLeft = initPos.left;
			var initTop = initPos.top;
			if (initLeft && typeof initLeft == "number") {
				currentwin.css("left", initLeft);	
			} else {
				currentwin.css("left", -10);
			}
			if (initTop && typeof initTop == "number") {
				currentwin.css("bottom", initTop);	
			} else {
				currentwin.css("bottom", 50);
			}
			currentwin.show();
		}
		cwinwidth = currentwin.outerWidth(true);
		cwinheight = currentwin.outerHeight(true);
		currentwin.data("positionleft", positionleft);
		currentwin.data("positiontop", positiontop);
		getBrowserDim();
		moveWin();

		var scrollTimeout;
		//浏览器滚动条滚动时，移动窗口的位置
		$(window).scroll(function(){
			//判断一下当前窗口是否可见
			if (!currentwin.is(":visible")) {
				return;	
			}
			clearTimeout(scrollTimeout);
			scrollTimeout = setTimeout(function(){
				getBrowserDim();		
				moveWin();
			},300);
		});
		//浏览器大小改变时，移动窗口的位置
		$(window).resize(function(){
			//判断一下当前窗口是否可见
			if (!currentwin.is(":visible")) {
				return;	
			}
			getBrowserDim();	
			moveWin();	
		});
		//返回当前对象，以便可以级联的执行其他方法
		return currentwin;
	}
}*/
