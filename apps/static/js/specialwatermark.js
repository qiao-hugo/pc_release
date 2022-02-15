
    window.onresize = function(){
        var watermarkText='';
        $.ajax({
            url: "index.php?module=main&action=getWaterText",
            data: {
                waterText : 1
            },
            type:'POST',
            dataType:'JSON',
            async:false,
            success: function (data) {
                var data=$.parseJSON(data);
				console.log(data.waterText);
				if(data.success==1){
				    $(".watermarks").remove();
					watermarkText=data.waterText;
					watermarkWord(watermarkText);
				}
            },error:function(){
            }
        });
    }
    var watermarkText='';
    $.ajax({
        url: "index.php?module=main&action=getWaterText",
        data: {
            waterText : 1
        },
        type:'POST',
        dataType:'JSON',
        async:false,
        success: function (data) {
			var data=$.parseJSON(data);
            console.log(data.waterText);
            if(data.success==1){
                watermarkText=data.waterText;
            }
        },error:function(){
        }
    });
    watermarkWord(watermarkText);
    function watermarkWord(watermarkText) {
        var screenHeight = window.screen.height;
        var screenWidth = window.screen.width;
        var watermarkText = watermarkText;
        if (navigator.appName == "Microsoft Internet Explorer" && navigator.appVersion.match(/9./i) == "9.") {
            var stepH = 0.2 * screenHeight;
            var stepW = 0.2 * screenWidth;
            for (var i = 0; i <= parseInt($("body").height() / stepH); i++) {
                for (var j = 0; j <= parseInt($("body").width() / stepW); j++) {
                    if(i%2==0 && j%2==0){
                    }else{
                        if(i%2!=0 && j%2!=0){
                        }else{
                            $('body').append('<div class="watermarks" style="position: fixed;pointer-events:none;z-index:99999;opacity:0.2;color:#000;top:' + stepH * (i) + 'px;left:' + (stepW * (j)+10) + 'px;font-size:0.9em;transform:rotate(-30deg); -ms-transform:rotate(-30deg); -o-tranform:rotate(-30deg); -webkit-transform:rotate(-30deg); -moz-transform:rotate(-30deg);filter:progid:DXImageTransform.Microsoft.Alpha(opacity=10));">' + watermarkText+parseInt($("body").height())+ '<br /></div>');
                        }
                    }
                }
            }
        } else {
            var stepH = 0.2 * screenHeight;
            var stepW = 0.35 * screenWidth;
            for (var i = 0; i <= parseInt($("body").height() / stepH); i++) {
                for (var j = 0; j <= parseInt($("body").width() / stepW); j++) {
                    if(i%2==0 && j%2==0){
                    }else{
                        if(i%2!=0 && j%2!=0){
                        }else{
                            $('body').append('<div class="watermarks" style="position: fixed;pointer-events:none;z-index:99999;opacity:0.2;color:#000;top:' + stepH * (i) + 'px;left:' + (stepW * (j)+10) + 'px;font-size:0.9em;transform:rotate(-30deg); -ms-transform:rotate(-30deg); -o-tranform:rotate(-30deg); -webkit-transform:rotate(-30deg); -moz-transform:rotate(-30deg);filter:progid:DXImageTransform.Microsoft.Alpha(opacity=10));">' + watermarkText + '<br /></div>');
                        }
                    }
                }
            }
        }
    }

