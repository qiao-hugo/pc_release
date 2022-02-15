/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.

 *************************************************************************************/

var Vtiger_Index_Js = {
		
	/**
	 * Function to show email preview in popup
	 */
	showEmailPreview : function(recordId, parentId) {
		var popupInstance = Vtiger_Popup_Js.getInstance();
		var params = {};
		params['module'] = "Emails";
		params['view'] = "ComposeEmail";
		params['mode'] = "emailPreview";
		params['record'] = recordId;
		params['parentId'] = parentId;
		params['relatedLoad'] = true;
		popupInstance.show(params);
	},

	registerWidgetsEvents : function() {
		var widgets = jQuery('div.widgetContainer');
		widgets.on({
				shown: function(e) {
					var widgetContainer = jQuery(e.currentTarget);
					Vtiger_Index_Js.loadWidgets(widgetContainer);
					var key = widgetContainer.attr('id');
					app.cacheSet(key, 1);
			},
				hidden: function(e) {
					var widgetContainer = jQuery(e.currentTarget);
					var imageEle = widgetContainer.parent().find('.imageElement');
					var imagePath = imageEle.data('rightimage');
					imageEle.attr('src',imagePath);
					var key = widgetContainer.attr('id');
					app.cacheSet(key, 0);
			}
		});
	},

	loadWidgets : function(widgetContainer) {
		var message = jQuery('.loadingWidgetMsg').html();

		if(widgetContainer.html() != '') {
			var imageEle = widgetContainer.parent().find('.imageElement');
			var imagePath = imageEle.data('downimage');
			imageEle.attr('src',imagePath);
			widgetContainer.css('height', 'auto');
			return;
		}

		widgetContainer.progressIndicator({'message' : message});
		var url = widgetContainer.data('url');

		var listViewWidgetParams = {
			"type":"GET", "url":"index.php",
			"dataType":"html", "data":url
		}
		AppConnector.request(listViewWidgetParams).then(
			function(data){
				widgetContainer.progressIndicator({'mode':'hide'});
				var imageEle = widgetContainer.parent().find('.imageElement');
				var imagePath = imageEle.data('downimage');
				imageEle.attr('src',imagePath);
				widgetContainer.css('height', 'auto');
				widgetContainer.html(data);
				var label = widgetContainer.closest('.quickWidget').find('.quickWidgetHeader').data('label');
				jQuery('.bodyContents').trigger('Vtiger.Widget.Load.'+label,jQuery(widgetContainer));
			}
		);
	},

	loadWidgetsOnLoad : function(){
		var widgets = jQuery('div.widgetContainer');
		widgets.each(function(index,element){
			var widgetContainer = jQuery(element);
			var key = widgetContainer.attr('id');
			var value = app.cacheGet(key);
			if(value != null){
				if(value == 1) {
					Vtiger_Index_Js.loadWidgets(widgetContainer);
					widgetContainer.addClass('in');
				} else {
					var imageEle = widgetContainer.parent().find('.imageElement');
					var imagePath = imageEle.data('rightimage');
					imageEle.attr('src',imagePath);
				}
			}

		});

	},

	/**
	 * Function to show compose email popup based on number of
	 * email fields in given module,if email fields are more than
	 * one given option for user to select email for whom mail should
	 * be sent,or else straight away open compose email popup
	 * @params : accepts params object
	 *
	 * @cb: callback function to recieve the child window reference.
	 */

	showComposeEmailPopup : function(params, cb){
		var currentModule = "Emails";
		Vtiger_Helper_Js.checkServerConfig(currentModule).then(function(data){
			if(data == true){
				var css = jQuery.extend({'text-align' : 'left'},css);
				AppConnector.request(params).then(
					function(data) {
						var cbargs = [];
						if(data) {
							data = jQuery(data);
							var form = data.find('#SendEmailFormStep1');
							var emailFields = form.find('.emailField');
							var length = emailFields.length;
							var emailEditInstance = new Emails_MassEdit_Js();
							if(length > 1) {
								app.showModalWindow(data,function(data){
									emailEditInstance.registerEmailFieldSelectionEvent();
									if( jQuery('#multiEmailContainer').height() > 300 ){
										jQuery('#multiEmailContainer').slimScroll({
											height: '300px',
											railVisible: true,
											alwaysVisible: true,
											size: '6px',
										});
									}
								},css);
							} else {
								emailFields.attr('checked','checked');
								var params = form.serializeFormData();
								// http://stackoverflow.com/questions/13953321/how-can-i-call-a-window-child-function-in-javascript
								// This could be useful for the caller to invoke child window methods post load.
								var win = emailEditInstance.showComposeEmailForm(params);
								cbargs.push(win);
							}
						}
						if (typeof cb == 'function') cb.apply(null, cbargs);
					},
					function(error,err){

					}
				);
			} else {
				Vtiger_Helper_Js.showPnotify(app.vtranslate('JS_EMAIL_SERVER_CONFIGURATION'));
			}
		})

	},

	/**
	 * Function registers event for Calendar Reminder popups
	 */
	registerActivityReminder : function() {
		var activityReminder = jQuery('#activityReminder').val();
		//activityReminder = activityReminder * 1000;
		if(activityReminder != '') {
			var currentTime = new Date().getTime()/1000;
			var nextActivityReminderCheck = app.cacheGet('nextActivityReminderCheckTime', 0);
			if((currentTime + activityReminder) > nextActivityReminderCheck) {
				//Vtiger_Index_Js.requestReminder();
				setTimeout('Vtiger_Index_Js.requestReminder()', activityReminder*1000);
				app.cacheSet('nextActivityReminderCheckTime', currentTime + parseInt(activityReminder));
			}
		}
	},

	/**
	 * Function registers event for Calendar Reminder popups
	 */
	registerActivityReminderNew : function() {
		var activityReminder = jQuery('#activityReminder').val();
		//activityReminder = activityReminder * 1000;
		if(activityReminder != '') {
			var currentTime = new Date().getTime()/1000; //当前时间戳
			var nextActivityReminderCheck = app.cacheGet('nextActivityReminderCheckTime', 0);
            app.cacheSet('documenttitle',document.title);
            if((currentTime + activityReminder) > nextActivityReminderCheck) {
               // console.log("if");
                //Vtiger_Index_Js.requestReminder();
				setTimeout('Vtiger_Index_Js.requestReminderNew()', activityReminder*1000);
				app.cacheSet('nextActivityReminderCheckTime', currentTime + parseInt(activityReminder));
			}
		}
	},
	
	/**
	 * Function request for reminder popups
	 */
	requestReminderNew : function() {
        var myDate = new Date();
       // console.log(myDate.getMinutes()+':'+myDate.getSeconds());
        var url = 'index.php?module=JobAlerts&action=ActivityReminder&mode=messageinfo';
		AppConnector.request(url).then(function(data){
				if(data.success && data.result) {
                    Vtiger_Index_Js.remindertype();
				}
			//定时开始
			var activityReminder = jQuery('#activityReminder').val();
			setTimeout('Vtiger_Index_Js.requestReminderNew()', activityReminder*1000);
		});
	},
	
	/**
	 * Function request for reminder popups
	 */
	requestReminder : function() {
		//var url = 'index.php?module=Calendar&action=ActivityReminder&mode=getReminders';
		var url = 'index.php?module=JobAlerts&action=ActivityReminder&mode=getReminders';
		AppConnector.request(url).then(function(data){
			var instace=$('#alertjobs');
			var html='';
			if (data==null || data.result.length ==0){
				//下次启动设置
				Vtiger_Index_Js.startReminderInterval();
				return;
			}
			if(data!=null){
				if(data.success && data.result) {
					
					for(i=0; i< data.result.length; i++) {
						var record  = data.result[i];
						//新增
						if(instace.is(':visible')==false){
							html += Vtiger_Index_Js.getHtmlDivContent(record);
						}else{//追加
							//如果包含
							if($('.alertjobsid_'+record.jobalertsid).length>0){
								continue;
							}else{//如果不包含
								html += Vtiger_Index_Js.getHtmlDivContent(record);
							}
						}
					}
				}
			}
			
			if(instace.is(':visible')==false){
				//新建
				var params = {
						title: '&nbsp;&nbsp;<span style="position: relative; top: 8px;">提醒信息</span>',
						text: '<div class="row-fluid" id="alertjobs" style="color:black;height:120px;overflow:auto"></div>',
						width: '30%',
						min_height: '75px',
						addclass:'vtReminder',
						icon: 'vtReminder-icon',
						hide:false,
						closer:true,
						type:'info',
						after_open:function(p) {
							jQuery(p).data('info', html);
						},
						stack: {"dir1": "up", "dir2": "left", "push": "up", "spacing1": 25, "spacing2": 25}
				
				};
				var notify = Vtiger_Helper_Js.showPnotify(params);
				
				jQuery('.delay').live('click', function() {
					var id=$(this).data('id');
					$(this).closest('div').remove();
					if($('#alertjobs').children('div').length==0){
						notify.remove();
					}
					//更新处理
					var delayurl = 'index.php?module=JobAlerts&action=ActivityReminder&mode=postpone&type=delay&record='+id;
					AppConnector.request(delayurl);
				});
				jQuery('.complete').live('click', function() {
					var id=$(this).data('id');
					$(this).closest('div').remove();
					if($('#alertjobs').children('div').length==0){
						notify.remove();
					}
					//更新处理
					var completeurl = 'index.php?module=JobAlerts&action=ActivityReminder&mode=postpone&type=complete&record='+id;
					AppConnector.request(completeurl);
				});
			}
			$('#alertjobs').append(html);
			//下次启动设置
			Vtiger_Index_Js.startReminderInterval();
			
		},function(error,err){
			//下次启动设置
			Vtiger_Index_Js.startReminderInterval();
		});
	},

    /**
     * 设置下次启动
     */
    startReminderInterval : function() {
        var activityReminder = jQuery('#activityReminder').val();
        activityReminder = activityReminder * 1000;
        if(activityReminder != '') {
            setTimeout('Vtiger_Index_Js.requestReminder()', activityReminder);
        }
    },

    /*
     * 获取详细内容
     */

	getHtmlDivContent : function(record) {
		var userid = jQuery('#current_user_id').val();

		var html  = '<div class="bs-callout bs-callout-warning alertjobsid_'+record.jobalertsid+'">';
		html  = html+'<h4>标题  :<a target="_blank" href="index.php?module=JobAlerts&view=Detail&record='+record.jobalertsid+'">'+record.subject+'</a></h4>';
		html  = html+'<p>'+record.alertcontent+'</p>';
		html  = html+'<table class="pull-right"><tr><td>';
		if (userid==record.ownerid){
			//处理人是当前用户的情况显示完成按钮
			html  = html+'<a class="pull-right complete" href=# data-id="'+record.jobalertsid+'">完成</a>';
		}
		html  = html+'</td><td>&nbsp;</td><td><a class="pull-right delay" href=# data-id="'+record.jobalertsid+'">延迟</a></td></tr></table></div>';
		return html;
	},
	/**
	 * Function display the Reminder popup
	 */
//	showReminderPopup : function(record) {
//		var params = {
//			title: '&nbsp;&nbsp;<span style="position: relative; top: 8px;">提醒信息</span>',
//			text: '<div class="row-fluid" style="color:black">\n\
//							<span class="span12">主题 : <a target="_blank" href="index.php?module=JobAlerts&view=Detail&record='+record.id+'">'+record.subject+'</a></span>',
//								
//			width: '30%',
//			min_height: '75px',
//			addclass:'vtReminder',
//			icon: 'vtReminder-icon',
//			hide:false,
//			closer:true,
//			type:'info',
//			after_open:function(p) {
//				jQuery(p).data('info', record);
//			}
//		};
//		var notify = Vtiger_Helper_Js.showPnotify(params);
//
//		jQuery('#finish_'+record.id).bind('click', function() {
//			notify.remove();
//			var url = 'index.php?module=JobAlerts&action=ActivityReminder&mode=complete&record='+record.id;
//			AppConnector.request(url);
//		});
//		
//		jQuery('#reminder_'+record.id).bind('click', function() {
//			notify.remove();
//			//var url = 'index.php?module=Calendar&action=ActivityReminder&mode=postpone&record='+record.id;
//			var url = 'index.php?module=JobAlerts&action=ActivityReminder&mode=postpone&record='+record.id;
//			AppConnector.request(url);
//		});
//	},

	/**
	 * Function to make top-bar menu responsive.
	 */
	adjustTopMenuBarItems: function() {
		var TOLERANT_MAX_GAP = 40; // px
		var menuBarWrapper = jQuery('.nav.modulesList');
		var topMenuBarWidth = menuBarWrapper.parent().outerWidth();
		var optionalBarItems = jQuery('.opttabs', menuBarWrapper), optionalBarItemsCount = optionalBarItems.length;
		var optionalBarItemIndex = optionalBarItemsCount;
		function enableOptionalTopMenuItem() {
			var opttab  = (optionalBarItemIndex > 0) ? optionalBarItems[optionalBarItemIndex-1] : null;
			if (opttab) { opttab = jQuery(opttab); opttab.hide(); optionalBarItemIndex--; }
			return opttab;
		}
		// Loop and enable hidden menu item until the tolerant width is reached.
		var stopLoop = false;
		do {
			var lastOptTab = enableOptionalTopMenuItem();
			if (lastOptTab == null || (topMenuBarWidth - menuBarWrapper.outerWidth()) > TOLERANT_MAX_GAP) {
				if(lastOptTab) lastOptTab.hide();
				stopLoop = true; break;
			}
		} while (!stopLoop);

		// Required to get the functionality of All drop-down working.
		menuBarWrapper.parent().css({'overflow':'visible'});
	},

	/**
	 * Function to trigger tooltip feature.
	 */
	registerTooltipEvents: function() {
		var references = jQuery.merge(jQuery('[data-field-type="reference"] > a'), jQuery('[data-field-type="multireference"] > a'));
		var lastPopovers = [];

		// Fetching reference fields often is not a good idea on a given page.
		// The caching is done based on the URL so we can reuse.
		var CACHE_ENABLED = false; // TODO - add cache timeout support.

		function prepareAndShowTooltipView() {
			hideAllTooltipViews();

			var el = jQuery(this);
			var url = el.attr('href')? el.attr('href') : '';
			if (url == '') {
				return;
			}

			// Rewrite URL to retrieve Tooltip view.
			url = url.replace('index.php','').replace('view=', 'xview=') + '&view=TooltipAjax';

			//var cachedView = CACHE_ENABLED ? jQuery('[data-url-cached="'+url+'"]') : null;
			var cachedView = null;
			if (1==2&&cachedView && cachedView.length) {
				showTooltip(el, cachedView.html());
			} else {
				var params={};
				params.data=url.replace('?','');
				params.type="GET";
				AppConnector.request(params).then(function(data){
					//cachedView = jQuery('<div>').css({display:'none'}).attr('data-url-cached', url);
					//cachedView.html(data);
					//jQuery('body').append(cachedView);
					showTooltip(el, data);
				});
			}
		}

		function showTooltip(el, data) {
			el.popover({
				//title: '', - Is derived from the Anchor Element (el).
				trigger: 'manual',
				html: true,
				content: data,
				animation: true,
				template: '<div class="popover popover-tooltip"><div class="arrow"></div><div class="popover-inner"><button name="vtTooltipClose" class="close" style="color:white;opacity:1;font-weight:lighter;position:relative;top:3px;right:3px;">x</button><h3 class="popover-title"></h3><div class="popover-content"><div></div></div></div></div>'
			});
			lastPopovers.push(el.popover('show'));
			registerToolTipDestroy();
		}

		function hideAllTooltipViews() {
			// Hide all previous popover
			var lastPopover = null;
			while (lastPopover = lastPopovers.pop()) {
				lastPopover.popover('hide');
			}
		}

		references.each(function(index, el){
			jQuery(el).hoverIntent({
				interval: 100,
				sensitivity: 1,
				timeout: 10,
				over: prepareAndShowTooltipView,
				out: hideAllTooltipViews
			});
		});

		function registerToolTipDestroy() {
			jQuery('button[name="vtTooltipClose"]').on('click', function(e){
				var lastPopover = lastPopovers.pop();
				lastPopover.popover('hide');
			});
		}
	},

	registerShowHideLeftPanelEvent : function() {
		jQuery('#toggleButton').click(function(e){
			e.preventDefault();
			var leftPanel = jQuery('#leftPanel');
			var rightPanel = jQuery('#rightPanel');
			var tButtonImage = jQuery('#tButtonImage');
			if (leftPanel.attr('class').indexOf(' hide') == -1) {
                var leftPanelshow = 1;
				leftPanel.addClass('hide');
				rightPanel.removeClass('span10').addClass('span12');
				tButtonImage.removeClass('icon-chevron-left').addClass("icon-chevron-right");
			} else {
                var leftPanelshow = 0;
				leftPanel.removeClass('hide');
				rightPanel.removeClass('span12').addClass('span10');
				tButtonImage.removeClass('icon-chevron-right').addClass("icon-chevron-left");
			}
            var params = {
                'module' : 'Users',
                'action' : 'IndexAjax',
                'mode' : 'toggleLeftPanel',
                'showPanel' : leftPanelshow
            }
            AppConnector.request(params);
		});
	},
	BarLinkRemove:function(){
		var  url = window.location.pathname + window.location.search;
		url=url.replace('/','');
		jQuery('.quickLinksDiv').find("a[href='" + url + "']").parent().addClass('leftmenu');
	
	
	
	},
    /**
	 * 站内信异步加载
     */
	loadWebSiteMsg:function(){
		var widgetContainer=$('.widgetContaine_footmsg');
        var contentContainer = jQuery('.widget_contents',widgetContainer);
        var urlParams = widgetContainer.data('url');

        var params = {
            'type' : 'GET',
            'dataType': 'html',
            'data' : urlParams
        };
        contentContainer.progressIndicator({});
        AppConnector.request(params).then(
            function(data){
                contentContainer.progressIndicator({'mode': 'hide'});
                contentContainer.html(data);
            },
            function(){

            }
        );
    },
	registerEvents : function(){
		//Vtiger_Index_Js.remindertype();
        this.loadWebSiteMsg();
		Vtiger_Index_Js.registerWidgetsEvents();
		Vtiger_Index_Js.loadWidgetsOnLoad();
		//Vtiger_Index_Js.registerActivityReminder();
		Vtiger_Index_Js.registerActivityReminderNew();
		Vtiger_Index_Js.adjustTopMenuBarItems();
		//2015年03月19日 星期四 wangbin 注释掉弹出工具
		//Vtiger_Index_Js.registerPostAjaxEvents();
		Vtiger_Index_Js.registerShowHideLeftPanelEvent();
		//Vtiger_Index_Js.overallClick();
		//Vtiger_Index_Js.hishref();
		//Vtiger_Index_Js.clearAll();
		//wangbin 禁止页面复制选择的js代码
		/*$('body').bind('contextmenu', function() {
		      return false;
		    });
		$('body').bind("selectstart",function(){return false;});
		this.BarLinkRemove();*/
	},
	//2015-1-19 wangbin 点击历史事件 2015-1-20 修改
	overallClick:function(){
		var that=this;
		$("a").on('click',function(){
			var value=$(this).attr('href');
			var key=$(this).text();
			var reg= /(index.php)/;
			if(reg.test(value)){
				jQuery.jStorage.set(key,value);
				that.hishref();
			}
		})
	},
	hishref:function(){
		//jQuery.jStorage.deleteKey("vtiger7.nextActivityReminderCheckTime");
		
		keys=jQuery.jStorage.index();
		kill=keys.length;
		if( kill>6){
			var jj=kill-6
        	for(i=0;i<=jj;i++){
        		if(keys[i]!='vtiger6.nextActivityReminderCheckTime'){
        			jQuery.jStorage.deleteKey(keys[i]);
        		}
        	}
        }
		var str2="<i class='icon-share-alt'></i>";                                                              
		$.each(keys,function(n,value){
			if(value!='vtiger6.nextActivityReminderCheckTime'){
			key=jQuery.jStorage.get(value);
			str2 += "<a href="+key+">"+value+"</a> > ";}
		});
		if(!str2==""){
			str3="<a href='javascript:void(0)' id='clearall'><i class='icon-trash'></i></a> "
			str2 = str2+str3;
		}
		$(".vtFooter font.span8:first").html(str2)
	},
	clearAll:function(){
		$("#clearall").live('click',function(){
			jQuery.jStorage.flush();
			$(".vtFooter font.span8:first").html("");
		});
	},
	registerPostAjaxEvents: function() {
		Vtiger_Index_Js.registerTooltipEvents();
	},

    //wangbin 添加提醒
    remindertype:function(){
        if (window.Notification){
            if(Notification.permission==='granted'){
                var notification = new Notification('消息',{body:"你有一条消息,请检查右侧通知栏"});
            }else if(Notification.permission==='denied'){
                _record =  app.cacheSet('__record',0);
                int =  Vtiger_Index_Js.clearinteval();
                // console.log(int+':'+'得道值');
                app.cacheSet("'"+csrfMagicToken+"'"+'ifclear',int);
            }else {
                    Notification.requestPermission();
            };
        }else{
            _record =  app.cacheSet('__record',0);
            int =  Vtiger_Index_Js.clearinteval();
           // console.log(int+':'+'得道值');
            app.cacheSet("'"+csrfMagicToken+"'"+'ifclear',int);
           //clearInterval(int);
        }
    },
    titleBlink:function(_record){
        myTitle = app.cacheGet('documenttitle');
        _record++;
        if( _record ==3){
            _record = 1;
        }
        if(_record==1){
            document.title='【      】'+myTitle;
        }
        if(_record==2){
            document.title='【新消息】'+myTitle;
        }
       // console.log(_record);
        app.cacheSet('__record',_record);
        //setTimeout(Vtiger_Index_Js.titleBlink(_record),500);
    },
    clearinteval:function(){
        //console.log(csrfMagicToken);
      //  console.log(app.cacheGet("'"+csrfMagicToken+"'"+'ifclear')+':'+'初始值');
        clearInterval(app.cacheGet(csrfMagicToken+'ifclear'));
       int =  setInterval('Vtiger_Index_Js.titleBlink(app.cacheGet("__record"))',500);
       //console.log(int+':'+'返回值')
        return int;
    }

    //end




}


//On Page Load
jQuery(document).ready(function() {
    var waterTextContent=$("#waterTextContent").val();
    watermarkWord(waterTextContent);
    window.onresize = function(){
    	$(".waterMark").remove();
        var waterTextContent=$("#waterTextContent").val();
        watermarkWord(waterTextContent);
	}
    function watermarkWord(watermarkText) {
        var screenHeight = window.screen.height;
        var screenWidth  = window.screen.width;
        var watermarkText =  watermarkText;
        if (navigator.appName == "Microsoft Internet Explorer" && navigator.appVersion.match(/9./i) == "9.") {
            var stepH = 0.1 * screenHeight;
            var stepW = 0.1 * screenWidth;
            for (var i = 0; i <= 8; i++) {
                for(var j = 0; j <= 10; j++){
                     $('body').append('<div class="waterMark" style="z-index:9999999;pointer-events: none;opacity:0.2;color:#000;position:fixed;top:' + 150 * (i) + 'px;left:'+ 200 * (j) +'px;font-size:1.1em;transform:rotate(-30deg); -ms-transform:rotate(-30deg); -o-tranform:rotate(-30deg); -webkit-transform:rotate(-30deg); -moz-transform:rotate(-30deg);filter:progid:DXImageTransform.Microsoft.Alpha(opacity=10));">' + watermarkText + '<br /></div>');
                }
            }
        } else {
            var stepH = 0.13 * screenHeight;
            var stepW = 0.1 * screenWidth;
            for (var i = 0; i <= 8; i++) {
                for(var j = 0; j <= 35; j++){
                	if(i%2==0 && j%2==0){
					}else{
                		if(i%2!=0 && j%2!=0){
						}else{
                            $('body').append('<div class="waterMark" style="z-index:9999999;pointer-events: none;opacity:0.2;color:#000;position:fixed;top:' + 150 * (i) + 'px;left:'+ 200 * (j) +'px;font-size:1.2em;transform:rotate(-30deg); -ms-transform:rotate(-30deg); -o-tranform:rotate(-30deg); -webkit-transform:rotate(-30deg); -moz-transform:rotate(-30deg);filter:progid:DXImageTransform.Microsoft.Alpha(opacity=10));">' + watermarkText + '<br /></div>');
                        }
                    }
                }
            }
        }
    }
	Vtiger_Index_Js.registerEvents();
	app.listenPostAjaxReady(function() {
		Vtiger_Index_Js.registerPostAjaxEvents();
	});
	
});


