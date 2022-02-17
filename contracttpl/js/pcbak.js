$(function () {
  var A4imgH = 841.92, //后台A4纸高度
    A4imgW = 595.32, //后台A4纸高度
    imgH = 1052.4, //中间图片的高度
    imgW = 744.15; //中间图片的宽
  var contractId = 7, // 合同id
    token = '';
  // 获取token
  $.ajax({
    url: 'http://new.fangxinqian.cn:8090/open/token',
    data: {
      appKey: '62af61e',
      appSecret: 'ee6b5cb32ebb418aabfe361195ca6efb'
    },
    type: 'GET',
    dataType: "JSON",
    success:function(res){
      if (res && res.success){
        token = res.data
      }
    }
  }).then(function (params) {
    if (params && params.success)
    // 合同内容
    $.ajax({
      url: 'http://new.fangxinqian.cn:8090' + '/open/contract/view',
      type: 'get',
      headers: {
        token: token
      },
      data: {
        contractId: contractId
      },
      dataType: "JSON",
      success: function (res) {
        if (res && res.success) {
          var result = res.data;
          // contractType  0 : 标准  1 : 非标
          $('.non-standard').text(result.contractType == 0 ? '标准合同' : '非标准合同')
          renderPdf('#navigation', {
            url: result.contract,
            scale: 1.5,
            width: 200,
            height: 258
          })
          var pdfh5 = renderPdf('#demo', {
            url: result.contract,
            lazy: false
          })
          renderPdf('#enclosurenavigation', {
            url: result.enclosure,
            scale: 1.5,
            width: 200,
            height: 258
          })
          renderPdf('#enclosure', {
            url: result.enclosure,
          })
          pdfh5.on("success", function (time) {
            $(result.inputs).each((i, v) => {
              renderInput(v);
            });
          })
          $('#lastTime').text(dateFormat(null, result.lastTime));
          $('.reveiver-name').text(result.reveiver.name);
          $('.reveiver-phone').text(result.reveiver.phone);
        } else {
          alert(res.msg)
        }
      },
      error: function (err) {
        var msg = err && err.msg ? err.msg : '服务器错误'
        alert(msg)
      }
    });
  })
  // 渲染pdf
  function renderPdf(el, params) {
    var width = params.width ? params.width : imgW;
    var height = params.height ? params.height : imgH;
    var scale = params.scale ? params.scale : 1.5;
    var lazy = params.lazy === false ? params.lazy : true;
    return new Pdfh5(el, {
      pdfurl: params.url,
      renderType: "canvas",
      lazy: lazy,
      scale: scale,
      loadingBar: false,
      pageNum: true,
      backTop: false,
      URIenable: false,
      zoomEnable: false,
      width: width,
      height: height
    });
  };
  // 渲染输入框
  function renderInput(params) {
    var realx = Math.round(params.lx * imgW / A4imgW);
    var realy = Math.round((A4imgH - params.ly) * imgH / A4imgH - params.height);
    var inp = '<div class="edit-input" style="' +
      'font-size:' + params.fontSize + 'px;' +
      'z-index: 102;' +
      'position:absolute;' +
      'left:' + realx + 'px;' +
      'top:' + realy + 'px">' +
      '<input type="text" data-id="' + params.id + '"' +
      'id="editinput' + params.id + '"' +
      'value=""' +
      'style="width:' + params.width + 'px;' +
      'height:' + params.height + 'px;"' +
      'placeholder="' + params.tip + '"/>' +
      '</div>'
    $("#demo #pageContainer" + params.pageNo).css('position', 'relactive').append(inp)
  };
  // 附件切换
  $('.head-tab ul li').on('click', function () {
    $('.head-tab ul li').removeClass('active').eq($(this).index()).addClass('active');
    $('.contact-container').hide().eq($(this).index()).show();
    $('.navigation').hide().eq($(this).index()).show();
  });
  // 导航栏点击事件
  $(".navigation").on('click','.pageContainer',function(){
    $(".contact-container .viewerContainer:visible").animate({scrollTop:($(this).attr('data-page')-1)*imgH},500)
  })
  // 时间格式化
  function dateFormat(fmt, date) { //author: meizz
    var date = date ? date : new Date();
    var fmt = fmt ? fmt : 'yyyy-MM-dd HH:mm:ss';
    var time = new Date(date);
    var o = {
      "M+": time.getMonth() + 1, //月份
      "d+": time.getDate(), //日
      "H+": time.getHours(), //小时
      "m+": time.getMinutes(), //分
      "s+": time.getSeconds(), //秒
      "q+": Math.floor((time.getMonth() + 3) / 3), //季度
      "S": time.getMilliseconds() //毫秒
    };
    if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (time.getFullYear() + "").substr(4 - RegExp.$1.length));
    for (var k in o)
      if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
    return fmt;
  }
  // 同步
  $(".contract-sync").on('click', function () {
    $.ajax({
      url: 'http://new.fangxinqian.cn:8090' + '/open/contract/last_input_text',
      type: 'GET',
      headers: {
        token: token
      },
      data: {
        contractId: contractId
      },
      dataType: "json",
      success: function (res) {
        if (res && res.success) {
          $(res.data).each((i, v) => {
            console.log(v)
            $("#editinput" + v.inputTextId).val(v.textValue)
          });
        } else {
          alert(res.msg)
        }
      },
      error: function (err) {
        alert(err)
      }
    })
  });
  // 确认发送
  /*$('.contract-send').click(function () {
    $('.ensrue-popup').show();
  });*/
  // 关闭弹框
 /* $('.popup-cancel').click(function () {
    $('.ensrue-popup').hide();
  });*/
  // 确认提交
  $('.popup-confirm').click(function () {
    $(this).prop('disabled', true)
    var that = this;
    var data = {
      contractId: contractId,
      itd: []
    }
    $("input[id^=editinput]").each((i, v) => {
      data.itd.push({
        positionId: $(v).attr('data-id'),
        value: $(v).val()
      })
    })
    $.ajax({
      url: 'http://new.fangxinqian.cn:8090' + '/open/contract/send',
      type: "POST",
      headers: {
        token: token,
        "content-Type": "application/json;charset=UTF-8"
      },
      data: JSON.stringify(data),
      dataType: "json",
      success: function (res) {
        if (res && res.success) {
          alert("成功")
          $(that).prop('disabled', false)
          $('.ensrue-popup').hide();
        } else {
          alert(res.msg)
        }
      },
      error: function () {
        $(that).prop('disabled', false)
      }
    })
  })
})