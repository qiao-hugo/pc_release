$(function () {
 /* var A4imgH = 841.92, //后台A4纸高度
    A4imgW = 595.32, //后台A4纸高度
    imgH = 1052.4, //中间图片的高度
    imgW = 744.15; //中间图片的宽*/
   var A4imgH = 842.25, //后台A4纸高度
     A4imgW = 595.5, //后台A4纸高度
     imgH = 1052.4, //中间图片的高度
     imgW = 744.15; //中间图片的宽
    // 渲染pdf
  function renderPdf(el, params) {
    console.log(params)
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
    var realy = Math.round((A4imgH - params.ly - params.height) * (imgH / A4imgH) );
    var height = Math.round(imgW / A4imgW * params.height);
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
      /*'height:' + params.height + 'px;"' +*/
      'height:' + height + 'px;"' +
      'placeholder="' + ((params.tip==null || params.tip==undefined)?'':params.tip) + '"/>' +
      '</div>'
    $("#demo #pageContainer" + params.pageNo).css('position', 'relactive').append(inp)
  };
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
  var result = parent.parentView;
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
  if(result.enclosureList.length){
    $(result.enclosureList).each(function(i,v){
      var index = i + 1;
      $(".head-tab ul").append(
          '<li>'+v.name+'</li>'
      )
      $(".container-center").append(
          '<div id="enclosure'+index+'" style="display: none;" class="contact-container"></div>'
      )
      $(".left-container").append(
          '<div id="enclosurenavigation'+index+'" class="navigation" style="display: none;"></div>'
      )
      renderPdf('#enclosurenavigation' + index, {
        url: v.url,
        scale: 1.5,
        width: 200,
        height: 258
      })
      renderPdf('#enclosure' + index, {
        url: v.url
      })
    })
  }
  pdfh5.on("success", function (time) {
    $(result.inputs).each((i, v) => {
      renderInput(v);
    })
    ;
  })
  $('#lastTime').text(dateFormat(null, result.lastTime));
  $('.reveiver-name').text(result.reveiver.name);
  $('.reveiver-phone').text(result.reveiver.phone);
  if($('input[name="record"]',parent.document).val()>0){
    $('.contractsync').css({'visibility':'visible'});
  }
  $('.popup-titlechange').text(window.titlechange);

})