$(function () {
  // 渲染pdf
  function renderPdf(el, params) {
    var lazy = params.lazy === false ? params.lazy : true;
    return new Pdfh5(el, {
      pdfurl: params.url,
      lazy: lazy,
      renderType: "canvas",
      loadingBar: false,
      pageNum: true,
      backTop: false,
      URIenable: false,
      zoomEnable: true,
    });
  };

    // 合同内容
    $.ajax({
        url: '/index.php?module=TyunWebBuyService&action=getPDFView',
        type: 'get',
        data: {
            contractId: contractId
        },
        dataType: "JSON",
        success: function (res) {
            if (res && res.success) {
                var result = res.data;
                var pdfh5 = renderPdf('#demo', {
                    url: result.contract,
                    lazy: false
                });
                if(result.enclosureList.length){
                    $(result.enclosureList).each(function(i,v){
                        var index = i + 1;
                        $(".conter-tab ul").append(
                            '<li data-url='+v.url+'>附件'+index+'</li>'
                        );
                        $(".conter-body").append(
                            '<div id="enclosure'+index+'" style="display: none;" class="contact-container"></div>'
                        )
                    })
                }
            } else {
                alert(res.msg)
            }
        },
        error: function (err) {
            var msg = err && err.msg ? err.msg : '服务器错误';
            alert(msg)
        }
    });
    var arr = [];
    // 附件切换
    $('.conter-tab ul').on('click', 'li', function () {
        $('.conter-tab ul li').removeClass('active').eq($(this).index()).addClass('active');
        $('.contact-container').hide().eq($(this).index()).show();
        if($(this).index()!=0 && arr.indexOf($(this).index()) == -1){
            arr.push($(this).index())
            renderPdf('#enclosure' + $(this).index(), {
                url: $(this).attr('data-url'),
                lazy: false
            })
        }
    });
})