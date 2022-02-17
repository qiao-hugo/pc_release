$(document).ready(function() {
    var waterTextContent = $("#waterTextContent").val();
    watermarkWord(waterTextContent);
    window.onresize = function () {
        $(".waterMark").remove();
        var waterTextContent = $("#waterTextContent").val();
        watermarkWord(waterTextContent);
    }

    function watermarkWord(watermarkText) {
        var screenHeight = window.screen.height;
        var screenWidth = window.screen.width;
        var watermarkText = watermarkText;
        if (navigator.appName == "Microsoft Internet Explorer" && navigator.appVersion.match(/9./i) == "9.") {
            var stepH = 0.1 * screenHeight;
            var stepW = 0.1 * screenWidth;
            for (var i = 0; i <= 8; i++) {
                for (var j = 0; j <= 10; j++) {
                    $('body').append('<div class="waterMark" style="z-index:9999999;pointer-events: none;opacity:0.2;color:#000;position:fixed;top:' + 150 * (i) + 'px;left:' + 200 * (j) + 'px;font-size:1.1em;transform:rotate(-30deg); -ms-transform:rotate(-30deg); -o-tranform:rotate(-30deg); -webkit-transform:rotate(-30deg); -moz-transform:rotate(-30deg);filter:progid:DXImageTransform.Microsoft.Alpha(opacity=10));">' + watermarkText + '<br /></div>');
                }
            }
        } else {
            var stepH = 0.13 * screenHeight;
            var stepW = 0.1 * screenWidth;
            for (var i = 0; i <= 8; i++) {
                for (var j = 0; j <= 35; j++) {
                    if (i % 2 == 0 && j % 2 == 0) {
                    } else {
                        if (i % 2 != 0 && j % 2 != 0) {
                        } else {
                            $('body').append('<div class="waterMark" style="z-index:9999999;pointer-events: none;opacity:0.2;color:#000;position:fixed;top:' + 150 * (i) + 'px;left:' + 200 * (j) + 'px;font-size:1.2em;transform:rotate(-30deg); -ms-transform:rotate(-30deg); -o-tranform:rotate(-30deg); -webkit-transform:rotate(-30deg); -moz-transform:rotate(-30deg);filter:progid:DXImageTransform.Microsoft.Alpha(opacity=10));">' + watermarkText + '<br /></div>');
                        }
                    }
                }
            }
        }
    }
});