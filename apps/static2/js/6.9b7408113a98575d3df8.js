webpackJsonp([6],{NWqo:function(e,a,n){"use strict";function t(e){n("a09G")}Object.defineProperty(a,"__esModule",{value:!0});var o=n("4YfN"),i=n.n(o),l=n("w3K0"),d=n("Q/W4"),r=n("KiN3"),A=n("Xyyc"),p=n("dJYW"),s=n("BSzM"),m=n("13kN"),f=n("gyMJ"),c=n("HVJf"),u=(l.a,d.a,r.a,A.a,p.a,s.a,m.a,i()({},Object(c.b)(["applyLeaveList"])),{name:"overTimeLeave",components:{PopupPicker:l.a,Group:d.a,XInput:r.a,PopupRadio:A.a,Datetime:p.a,XTextarea:s.a,Loading:m.a},data:function(){return{Officialseal:[],loading:!1,startTime:"",endTime:"",form:{type:12,companyId:"",loanItemId:"",loanTime:"",returnTime:"",reason:""}}},methods:{cancelTask:function(){var e={taskId:this.taskId},a=this;this.$vux.confirm.show({title:"提示",content:"是否取消申请",onConfirm:function(){Object(f.cancelTask)(e).then(function(e){a.$vux.toast.show({text:e.message,type:"success"}),a.$router.go(-1)})}})},changeLoanTime:function(e){this.form.loanTime=e+":00"},changeReturnTime:function(e){this.form.returnTime=e+":00"},showseal:function(){this.Officialseal=[],this.getOfficialsealList()},getOfficialsealList:function(){var e=this,a=[],n={companyId:this.form.companyId,type:1};Object(f.e)(n).then(function(n){a=n.result,a.forEach(function(a){e.Officialseal.push({key:a.id,value:a.name})})})},startLeave:function(){var e=this;Object(f.m)(this.form).then(function(a){e.$vux.toast.show({text:a.message,type:"success"}),e.$router.go(-1)})}},computed:i()({},Object(c.b)(["applyLeaveList"]))}),v=function(){var e=this,a=e.$createElement,n=e._self._c||a;return n("div",{staticClass:"apply-leave"},[n("group",[n("popup-radio",{attrs:{title:"选择公司：",options:e.applyLeaveList},model:{value:e.form.companyId,callback:function(a){e.$set(e.form,"companyId",a)},expression:"form.companyId"}},[n("p",{staticClass:"select-header",attrs:{slot:"popup-header"},slot:"popup-header"},[e._v("\n        请选择\n      ")])]),e._v(" "),n("popup-radio",{attrs:{title:"公章名称：",options:e.Officialseal},on:{"on-show":e.showseal},model:{value:e.form.loanItemId,callback:function(a){e.$set(e.form,"loanItemId",a)},expression:"form.loanItemId"}},[n("p",{staticClass:"select-header",attrs:{slot:"popup-header"},slot:"popup-header"},[e._v("\n        请选择\n      ")])]),e._v(" "),n("x-textarea",{attrs:{title:"用途："},model:{value:e.form.reason,callback:function(a){e.$set(e.form,"reason",a)},expression:"form.reason"}}),e._v(" "),n("datetime",{attrs:{title:"外借时间：",format:"YYYY-MM-DD HH:mm","minute-list":["00","15","30","45"]},on:{"on-change":e.changeLoanTime},model:{value:e.startTime,callback:function(a){e.startTime=a},expression:"startTime"}}),e._v(" "),n("datetime",{attrs:{title:"归还时间：",format:"YYYY-MM-DD HH:mm","minute-list":["00","15","30","45"]},on:{"on-change":e.changeReturnTime},model:{value:e.endTime,callback:function(a){e.endTime=a},expression:"endTime"}})],1),e._v(" "),n("p",{staticClass:"save-btn"},[n("button",{staticClass:"x-button primary",on:{click:e.startLeave}},[e._v("提交")])]),e._v(" "),n("loading",{attrs:{show:e.loading,text:"加载中..."}})],1)},C=[],B={render:v,staticRenderFns:C},h=B,E=n("C7Lr"),y=t,b=E(u,h,!1,y,"data-v-0d1d281a",null);a.default=b.exports},a09G:function(e,a,n){var t=n("pagJ");"string"==typeof t&&(t=[[e.i,t,""]]),t.locals&&(e.exports=t.locals);n("FIqI")("d7139e9c",t,!0,{})},pagJ:function(e,a,n){a=e.exports=n("UTlt")(!0),a.push([e.i,'\n.apply-leave[data-v-0d1d281a] .vux-label {\n  font-weight: normal;\n  font-size: 0.36rem;\n}\n.apply-leave[data-v-0d1d281a] .weui-label {\n  font-weight: normal;\n  font-size: 0.36rem;\n}\n.apply-leave[data-v-0d1d281a] .weui-input {\n  color: #999;\n  font-size: 0.36rem;\n}\n.apply-leave[data-v-0d1d281a] .weui-textarea {\n  border: 1px solid #ccc;\n  font-size: 0.36rem;\n}\n.apply-leave .vux-datetime[data-v-0d1d281a] {\n  font-size: 0.36rem;\n}\n.apply-leave .weui-cell[data-v-0d1d281a] {\n  font-size: 0.36rem;\n  padding: 0.2rem 0.3rem;\n}\n.apply-leave .file-upload[data-v-0d1d281a] {\n  padding: 0.2rem 0.3rem;\n  position: relative;\n  font-size: 0.36rem;\n}\n.apply-leave .file-upload[data-v-0d1d281a]::before {\n  content: " ";\n  position: absolute;\n  left: 0;\n  top: 0;\n  right: 0;\n  height: 1px;\n  border-top: 1px solid #D9D9D9;\n  color: #D9D9D9;\n  -webkit-transform-origin: 0 0;\n  transform-origin: 0 0;\n  -webkit-transform: scaleY(0.5);\n  transform: scaleY(0.5);\n  left: 15px;\n}\n.apply-leave .file-upload .file-title[data-v-0d1d281a] {\n  width: 6em;\n  float: left;\n}\n.apply-leave .file-upload .file-name[data-v-0d1d281a] {\n  max-width: 3rem;\n  float: left;\n  overflow: hidden;\n  text-overflow: ellipsis;\n  white-space: nowrap;\n}\n.apply-leave .file-upload > div[data-v-0d1d281a] {\n  display: inline-block;\n  position: relative;\n}\n.apply-leave .file-upload input[data-v-0d1d281a] {\n  width: 1.5rem;\n  height: 0.6rem;\n  z-index: 3;\n  position: absolute;\n  left: 0;\n  top: 0;\n  opacity: 0;\n}\n.apply-leave .file-upload .upload-btn[data-v-0d1d281a] {\n  border-radius: 3px;\n  background-color: #2293fb;\n  color: #fff;\n  width: 1.5rem;\n  height: 0.6rem;\n  display: inline-block;\n  font-size: 0.24rem;\n  text-align: center;\n  line-height: 0.6rem;\n}\n.apply-leave .upload-tip[data-v-0d1d281a] {\n  padding: 0.2rem 0.3rem;\n  font-size: 0.28rem;\n}\n.apply-leave .upload-tip .red[data-v-0d1d281a] {\n  color: red;\n  display: block;\n}\n',"",{version:3,sources:["G:/oa-seal-web/src/views/leave/overTimeLeave.vue"],names:[],mappings:";AACA;EACE,oBAAoB;EACpB,mBAAmB;CACpB;AACD;EACE,oBAAoB;EACpB,mBAAmB;CACpB;AACD;EACE,YAAY;EACZ,mBAAmB;CACpB;AACD;EACE,uBAAuB;EACvB,mBAAmB;CACpB;AACD;EACE,mBAAmB;CACpB;AACD;EACE,mBAAmB;EACnB,uBAAuB;CACxB;AACD;EACE,uBAAuB;EACvB,mBAAmB;EACnB,mBAAmB;CACpB;AACD;EACE,aAAa;EACb,mBAAmB;EACnB,QAAQ;EACR,OAAO;EACP,SAAS;EACT,YAAY;EACZ,8BAA8B;EAC9B,eAAe;EACf,8BAA8B;EAC9B,sBAAsB;EACtB,+BAA+B;EAC/B,uBAAuB;EACvB,WAAW;CACZ;AACD;EACE,WAAW;EACX,YAAY;CACb;AACD;EACE,gBAAgB;EAChB,YAAY;EACZ,iBAAiB;EACjB,wBAAwB;EACxB,oBAAoB;CACrB;AACD;EACE,sBAAsB;EACtB,mBAAmB;CACpB;AACD;EACE,cAAc;EACd,eAAe;EACf,WAAW;EACX,mBAAmB;EACnB,QAAQ;EACR,OAAO;EACP,WAAW;CACZ;AACD;EACE,mBAAmB;EACnB,0BAA0B;EAC1B,YAAY;EACZ,cAAc;EACd,eAAe;EACf,sBAAsB;EACtB,mBAAmB;EACnB,mBAAmB;EACnB,oBAAoB;CACrB;AACD;EACE,uBAAuB;EACvB,mBAAmB;CACpB;AACD;EACE,WAAW;EACX,eAAe;CAChB",file:"overTimeLeave.vue",sourcesContent:['\n.apply-leave[data-v-0d1d281a] .vux-label {\n  font-weight: normal;\n  font-size: 0.36rem;\n}\n.apply-leave[data-v-0d1d281a] .weui-label {\n  font-weight: normal;\n  font-size: 0.36rem;\n}\n.apply-leave[data-v-0d1d281a] .weui-input {\n  color: #999;\n  font-size: 0.36rem;\n}\n.apply-leave[data-v-0d1d281a] .weui-textarea {\n  border: 1px solid #ccc;\n  font-size: 0.36rem;\n}\n.apply-leave .vux-datetime[data-v-0d1d281a] {\n  font-size: 0.36rem;\n}\n.apply-leave .weui-cell[data-v-0d1d281a] {\n  font-size: 0.36rem;\n  padding: 0.2rem 0.3rem;\n}\n.apply-leave .file-upload[data-v-0d1d281a] {\n  padding: 0.2rem 0.3rem;\n  position: relative;\n  font-size: 0.36rem;\n}\n.apply-leave .file-upload[data-v-0d1d281a]::before {\n  content: " ";\n  position: absolute;\n  left: 0;\n  top: 0;\n  right: 0;\n  height: 1px;\n  border-top: 1px solid #D9D9D9;\n  color: #D9D9D9;\n  -webkit-transform-origin: 0 0;\n  transform-origin: 0 0;\n  -webkit-transform: scaleY(0.5);\n  transform: scaleY(0.5);\n  left: 15px;\n}\n.apply-leave .file-upload .file-title[data-v-0d1d281a] {\n  width: 6em;\n  float: left;\n}\n.apply-leave .file-upload .file-name[data-v-0d1d281a] {\n  max-width: 3rem;\n  float: left;\n  overflow: hidden;\n  text-overflow: ellipsis;\n  white-space: nowrap;\n}\n.apply-leave .file-upload > div[data-v-0d1d281a] {\n  display: inline-block;\n  position: relative;\n}\n.apply-leave .file-upload input[data-v-0d1d281a] {\n  width: 1.5rem;\n  height: 0.6rem;\n  z-index: 3;\n  position: absolute;\n  left: 0;\n  top: 0;\n  opacity: 0;\n}\n.apply-leave .file-upload .upload-btn[data-v-0d1d281a] {\n  border-radius: 3px;\n  background-color: #2293fb;\n  color: #fff;\n  width: 1.5rem;\n  height: 0.6rem;\n  display: inline-block;\n  font-size: 0.24rem;\n  text-align: center;\n  line-height: 0.6rem;\n}\n.apply-leave .upload-tip[data-v-0d1d281a] {\n  padding: 0.2rem 0.3rem;\n  font-size: 0.28rem;\n}\n.apply-leave .upload-tip .red[data-v-0d1d281a] {\n  color: red;\n  display: block;\n}\n'],sourceRoot:""}])}});
//# sourceMappingURL=6.9b7408113a98575d3df8.js.map