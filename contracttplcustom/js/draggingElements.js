Vue.component('sign-area', {
  template: `
    <div class="vdr"  @touchstart='touchStart' @touchmove='touchMove' @touchend='touchEnd' :class="{ draggable: draggable && !disable, resizable: resizable && !disable, active , dragging, resizing}" @mousedown.stop="elmDown" tabindex="0" :style="style">
    <!-- 如果可改变大小为真 -->
      <template v-if="resizable && !disable">
        <!-- 待优化 -->
        <div class="handle handle-ml" v-if="resizable === true || resizable === 1 || resizable === 2" @mousedown.stop.prevent="handleDown('ml')"></div>
        <div class="handle handle-mr" v-if="resizable === true || resizable === 1 || resizable === 3" @mousedown.stop.prevent="handleDown('mr')">
        </div>
        <div class="handle handle-tm" v-if="resizable === true || resizable === 1 || resizable === 4" @mousedown.stop.prevent="handleDown('tm')"></div>
        <div class="handle handle-bm" v-if="resizable === true || resizable === 1 || resizable === 5" @mousedown.stop.prevent="handleDown('bm')">
        </div>
        <div class="handle handle-br" v-if="resizable === true || resizable === 1" @mousedown.stop.prevent="handleDown('br')">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 17 17">
            <defs></defs>
            <g data-name="图层 2">
              <g data-name="图层 2">
                <rect class="cls-1" x="2.63" y="-0.2" width="1" height="6.65" transform="translate(-1.3 3.13) rotate(-45)"/>
                <path class="cls-1" d="M.5,5.78a.5.5,0,0,1-.5-.5V0H5.28a.5.5,0,0,1,.5.5.5.5,0,0,1-.5.5H1V5.28A.5.5,0,0,1,.5,5.78Z"/>
                <path class="cls-1" d="M16.3,5.78a.5.5,0,0,1-.5-.5V1H11.52a.5.5,0,0,1,0-1H16.8V5.28A.5.5,0,0,1,16.3,5.78Z"/>
                <path class="cls-1" d="M5.28,16.8H0V11.52a.5.5,0,0,1,1,0V15.8H5.28a.5.5,0,0,1,.5.5A.5.5,0,0,1,5.28,16.8Z"/>
                <path class="cls-1" d="M16.22,16.72a.52.52,0,0,1-.35-.14l-4.7-4.7a.5.5,0,0,1,.71-.71l4.7,4.7a.51.51,0,0,1,0,.71A.54.54,0,0,1,16.22,16.72Z"/>
                <path class="cls-1" d="M17,17H11.72a.5.5,0,1,1,0-1H16V11.72a.5.5,0,1,1,1,0Z"/>
              </g>
            </g>
          </svg>
        </div>
      </template>
      <slot></slot>
    </div>
  `,
    replace: true,
    name: 'draggingElements',
    props: {
      index: {
        //组件序号
        type: Number,
        default: 0,
        validator: (val) => {
          return (typeof val === 'number' && (val > 0 || val == 0))
        }
      },
      draggable: {
        // 是否可被拖动
        type: [Boolean, Number],
        default: true
      },
      resizable: {
        // 是否可改变大小
        type: [Boolean, Number],
        default: true
      },
      w: {
        // 宽度
        type: Number,
        default: 200,
        validator: (val) => {
          return (typeof val === 'number' && val > 0)
        }
      },
      h: {
        // 高度
        type: [Number,String],
        default: 200,
        validator: (val) => {
          return (typeof val === 'number' && val > 0)
        }
      },
      minw: {
        // 最小宽度
        type: Number,
        default: 50,
        validator: function (val) {
          return val > 0
        }
      },
      minh: {
        // 最小高度
        type: Number,
        default: 50,
        validator: function (val) {
          return val > 0
        }
      },
      x: {
        // 距父元素左上角X轴偏移量
        type: Number,
        default: 0
      },
      y: {
        // 距父元素左上角Y轴偏移量
        type: Number,
        default: 0
      },
      zoom: {
        type: Number,
        default: 1
      },
      grid: {
        type: Array,
        default: () => {
          return [1, 1]
        }
      },
      restrain: {
        // 约束组件大小
        type: Number,
        default: 0
      },
      parent: {
        type: Boolean, default: false
      },
      disable: {
        type: Boolean, default: false
      }
    },
    data () {
      return {
        top: this.y,
        left: this.x,
        width: this.w,
        height: this.h,
        resizing: false,
        dragging: false,
        active: false,
        handle: null,
        parentX: 0,
        parentW: 9999,
        parentY: 0,
        parentH: 9999,
        mouseX: 0,
        mouseY: 0,
        lastMouseX: 0,
        lastMouseY: 0,
        mouseOffX: 0,
        mouseOffY: 0,
        elmX: 0,
        elmY: 0,
        elmW: 0,
        elmH: 0,
        startX:0,//开始触摸的位置
        startY:0,
        moveX:0,//滑动时的位置
        moveY:0,
        endX:0,//结束触摸的位置
        endY:0,
        disX:0,//移动距离
        disY:0
      }
    },
    mounted () {
      // 初始化控件宽高
      //if (this.minw > this.w) this.width = this.minw
      //if (this.minh < this.h) this.height = this.minh
      // 判断是否只能在父级元素中拖动
      if (this.parent) this.calculationParent()
      // 判断浏览器是否支持passive
      try {
        Object.defineProperty({}, "passive", {
          get: function() {
            this.passiveSupported = true;
          }
        });
      } catch(err) {}
      this.$emit('resizing', this.left, this.top, this.width, this.height,this.index)
    },
    methods: {
      touchStart(ev) {
          ev = ev || event;
          ev.preventDefault();
          if(ev.touches.length == 1) {    //tounches类数组，等于1时表示此时有只有一只手指在触摸屏幕
              this.startX = ev.touches[0].clientX; // 记录开始位置
              this.startY = ev.touches[0].clientY; // 记录开始位置
              // console.log("start===="+this.startX,this.startY)
          }
      },
      touchMove:function(ev) {
          ev = ev || event;
          ev.preventDefault();
          // console.log("move")
          if(ev.touches.length == 1) {
              //滑动时距离浏览器左侧的距离
              this.moveX = ev.touches[0].clientX;
              this.moveY = ev.touches[0].clientY;
              // console.log(this.moveX,this.moveY)
              //实时的滑动的距离-起始位置=实时移动的位置
              this.disX = this.moveX-this.startX;
              this.disY = this.moveY-this.startY;
              this.top += this.disY; //move过程中实时改变top及left值
              this.left += this.disX;
              this.startX += this.disX; //每一次位置改变后touchmove的开始操作位置也需要改变
              this.startY += this.disY;
              //以下对其边缘做判断，拖拽块不会跑出父元素区域
              if(this.top<0){
                this.top = 0
              }
              if(this.left<0){
                this.left = 0;
              }
              if(this.left+this.w>this.parentW){
                this.left = this.parentW - this.w;
              }
              if(this.top+this.h>this.parentH){
                this.top = this.parentH - this.h;
              }
              this.$emit('dragging', this.left, this.top, this.index)
          }
      },
      touchEnd:function(ev){
          ev = ev || event;
          ev.preventDefault();
          // console.log("end")
          if(ev.changedTouches.length == 1) {
              let endX = ev.changedTouches[0].clientX;
              let endY = ev.changedTouches[0].clientY;
              this.disX = endX-this.startX;
              this.disY = endY-this.startY;
              // console.log("偏移了==="+this.disX,this.disY)
              this.top += this.disY;
              this.left += this.disX;
              //以下对其边缘做判断，拖拽块不会跑出父元素区域
              if(this.top<0){
                this.top = 0
              }
              if(this.left<0){
                this.left = 0;
              }
              if(this.left+this.w>this.parentW){
                this.left = this.parentW - this.w;
              }
              if(this.top+this.h>this.parentH){
                this.top = this.parentH - this.h;
              }
              this.$emit('dragging', this.left, this.top, this.index)
          }
      },
      calculationParent () {
        this.parentW = parseInt(this.$el.parentNode.clientWidth, 10)
        this.parentH = parseInt(this.$el.parentNode.clientHeight, 10)
        if (this.w > this.parentW) this.width = this.parentW
        if (this.h > this.parentH) this.height = this.parentH
        if ((this.x + this.width) > this.parentW) this.left = this.parentW - this.width
        if ((this.y + this.height) > this.parentH) this.top = this.parentH - this.height
      },
      elmDown (e) { // 组件被按下事件
        // 阻止默认事件
        //e.preventDefault()
        const passiveSupported = this.passiveSupported
        // 判断是否支持拖动
        if (this.disable || !this.draggable) return
        const target = e.target || e.srcElement
        // 确保事件发生在组件内部
        if (this.$el.contains(target)) {
          if (!this.active) {
            this.lastMouseX = e.pageX || e.clientX + document.documentElement.scrollLeft
            this.lastMouseY = e.pageY || e.clientY + document.documentElement.scrollTop
            document.documentElement.addEventListener('mousemove', this.handleMove, passiveSupported ? { passive: true } :true)
            document.documentElement.addEventListener('mousedown', this.deselect, passiveSupported ? { passive: true } :true)
            document.documentElement.addEventListener('mouseup', this.handleUp, passiveSupported ? { passive: true } :true)
            this.active = true
            this.$emit('activated')
          }
          this.elmX = parseInt(this.left)
          this.elmY = parseInt(this.top)
          this.elmW = this.$el.offsetWidth || this.$el.clientWidth
          this.elmH = this.$el.offsetHeight || this.$el.clientHeight
          this.dragging = true
        }
      },
      deselect (e) { // 取消选择事件
        this.mouseX = e.pageX || e.clientX + document.documentElement.scrollLeft
        this.mouseY = e.pageY || e.clientY + document.documentElement.scrollTop
        this.lastMouseX = this.mouseX
        this.lastMouseY = this.mouseY
        const target = e.target || e.srcElement
        const regex = new RegExp('handle-([trmbl]{2})', '')
        if (!this.$el.contains(target) && !regex.test(target.className)) {
          if (this.active) {
            document.documentElement.removeEventListener('mousemove', this.handleMove, true)
            document.documentElement.removeEventListener('mousedown', this.deselect, true)
            document.documentElement.removeEventListener('mouseup', this.handleUp, true)
            this.active = false
            this.$emit('deactivated')
          }
        }
      },
      handleDown (handle) { // 拖动点按下事件
        // 将handle设置为当前元素
        this.handle = handle
        this.resizing = true
      },
      handleMove (e) {
        // 鼠标在页面上的坐标
        this.mouseX = e.pageX || e.clientX + document.documentElement.scrollLeft
        this.mouseY = e.pageY || e.clientY + document.documentElement.scrollTop
        // diffX =  当前鼠标位置 - 上次鼠标位置 + ？？
        let diffX = (this.mouseX - this.lastMouseX + this.mouseOffX) / this.zoom
        let diffY = (this.mouseY - this.lastMouseY + this.mouseOffY) / this.zoom
        this.mouseOffX = this.mouseOffY = 0
        this.lastMouseX = this.mouseX
        this.lastMouseY = this.mouseY
        if (this.resizing) {
          if (this.handle.indexOf('t') >= 0) {
            if (this.elmH - diffY < this.minh) this.mouseOffY = (diffY - (diffY = this.elmH - this.minh))
            else if (this.elmY + diffY < this.parentY) this.mouseOffY = (diffY - (diffY = this.parentY - this.elmY))
            this.elmY += diffY
            this.elmH -= diffY
          }
          if (this.handle.indexOf('b') >= 0) {
            if (this.elmH + diffY < this.minh) this.mouseOffY = (diffY - (diffY = this.minh - this.elmH))
            else if (this.elmY + this.elmH + diffY > this.parentH) this.mouseOffY = (diffY - (diffY = this.parentH - this.elmY - this.elmH))
            this.elmH += diffY
          }
          if (this.handle.indexOf('l') >= 0) {
            if (this.elmW - diffX < this.minw) this.mouseOffX = (diffX - (diffX = this.elmW - this.minw))
            else if (this.elmX + diffX < this.parentX) this.mouseOffX = (diffX - (diffX = this.parentX - this.elmX))
            this.elmX += diffX
            this.elmW -= diffX
          }
          if (this.handle.indexOf('r') >= 0) {
            if (this.elmW + diffX < this.minw) this.mouseOffX = (diffX - (diffX = this.minw - this.elmW))
            else if (this.elmX + this.elmW + diffX > this.parentW) this.mouseOffX = (diffX - (diffX = this.parentW - this.elmX - this.elmW))
            this.elmW += diffX
          }
          this.left = (Math.round(this.elmX / this.grid[0]) * this.grid[0])
          this.top = (Math.round(this.elmY / this.grid[1]) * this.grid[1])
          this.width = (Math.round(this.elmW / this.grid[0]) * this.grid[0])
          //this.height = (Math.round(this.elmH / this.grid[1]) * this.grid[1])
          this.$emit('resizing', this.left, this.top, this.width, this.height, this.index)
        } else if (this.dragging) {
          if (this.parent) {
            if (this.elmX + diffX < this.parentX) this.mouseOffX = (diffX * this.zoom - (diffX = this.parentX - this.elmX))
            else if (this.elmX + this.elmW + diffX > this.parentW) this.mouseOffX = (diffX * this.zoom - (diffX = this.parentW - this.elmX - this.elmW))
            if (this.elmY + diffY < this.parentY) this.mouseOffY = (diffY * this.zoom - (diffY = this.parentY - this.elmY))
            else if (this.elmY + this.elmH + diffY > this.parentH) this.mouseOffY = (diffY * this.zoom - (diffY = this.parentH - this.elmY - this.elmH))
          }
          this.elmX += diffX
          this.elmY += diffY
          if (this.draggable === 2 || this.draggable === 1 || this.draggable === true) {
            this.left = (Math.round(this.elmX / this.grid[0]) * this.grid[0])
          }
          if (this.draggable === 3 || this.draggable === 1 || this.draggable === true) {
            this.top = (Math.round(this.elmY / this.grid[1]) * this.grid[1])
          }
          this.$emit('dragging', this.left, this.top, this.index)
        }
      },
      getRestrain (num) {
        const restrain = this.restrain
        return (num / restrain).toFixed(0) * restrain
      },
      handleUp (e) {
        this.handle = null
        // 约束
        const restrain = this.restrain
        if (restrain && restrain > 0) {
          this.left = this.getRestrain(this.left)
          this.top = this.getRestrain(this.top)
          this.width = this.getRestrain(this.width)
          this.height = this.getRestrain(this.height)
        }
        if (this.resizing) {
          this.resizing = false
          this.$emit('resizestop', [this.left, this.top, this.width, this.height])
        }
        if (this.dragging) {
          this.dragging = false
          this.$emit('dragstop', [this.left, this.top])
        }
        this.elmX = this.left
        this.elmY = this.top
      }
    },
    watch: {
      x (newVal) {
        if (isNaN(newVal)) {
          // console.error('传入x值为空!')
        } else {
          this.left = newVal
        }
      },
      y (newVal) {
        if (isNaN(newVal)) {
          // console.error('传入y值为空!')
        } else {
          this.top = newVal
        }
      },
      w (newVal) {
        if (isNaN(newVal)) {
          // console.error('传入w值为空!')
        } else {
          this.width = newVal
        }
      },
      h (newVal) {
        if (isNaN(newVal)) {
          // console.error('传入h值为空!')
        } else {
          this.height = newVal
        }
      }
    },
    computed: {
      style () {
        const w = this.width === 0? 'auto': this.width + 'px'
        const h = this.height === 0? 'auto': this.height + 'px'
        return {
          top: this.top + 'px',
          left: this.left + 'px',
          width: w,
          height: h,
          border: "1px solid rgba(120,120,120,0.5)"
        }
      }
    }
})