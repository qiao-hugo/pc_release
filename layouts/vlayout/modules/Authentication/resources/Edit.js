Vtiger_Edit_Js("Authentication_Edit_Js",{ },{

    registerReferenceSelectionEvent : function(container) {
        this._super(container);
        var thisInstance = this;
    },

    saveData:function(){
      $(".btn-success").on("click",function (e) {
          var idcard=$("input[name='idcard']").val();
          var reg =/(^[1-9]\d{5}(18|19|([23]\d))\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$)|(^[1-9]\d{5}\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}$)/;
          if(!reg.test(idcard)){
              e.preventDefault();
              Vtiger_Helper_Js.showMessage({type:'error',text:'请输入正确的身份证号码'});
              return false;
          }
      });
    },


	registerBasicEvents : function(container) {
        this._super(container);
        this.registerReferenceSelectionEvent(container);
        this.saveData(container);
    }
});
