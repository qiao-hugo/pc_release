var contractId = 7, // 合同id
  token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJleHAiOjE1ODk0ODEyMDN9.U79KZX6WVg8FBcC9szA5JH-GE-9U4RLI1GyYSNtaP2c';
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
      zoomEnable: false,
    });
  };
  // 获取token
  $.ajax({
    url: 'http://new.fangxinqian.cn:8090/open/token',
    data: {
      appKey: '62af61e',
      appSecret: 'ee6b5cb32ebb418aabfe361195ca6efb'
    },
    type: 'GET',
    dataType: "JSON"
  }).then(function (res) {
    if (res && res.success)
      // 合同内容
      $.ajax({
        url: 'http://new.fangxinqian.cn:8090' + '/open/contract/view',
        type: 'get',
        headers: {
          token: res.data
        },
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
            })
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
})