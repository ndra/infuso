// @include inx.panel,inx.button
inx.css(
    ".inx-calendar-right{background:url(%%inx.calendar%%/right.jpg) center;width:40px;height:270px;position:absolute;left:640px;cursor:pointer;}",
    ".inx-calendar-left{background:url(%%inx.calendar%%/left.jpg) center;width:40px;height:270px;position:absolute;cursor:pointer;}",
    ".inx-calendar-now{position:absolute;top:220px;left:300px;}"
);

inx.calendar = inx.panel.extend({

    constructor:function(p) {

        var date = new Date();
        p.year = p.year*1 || date.getFullYear();
        p.month = p.month*1 || date.getMonth()+1;
        p.day = p.day*1 || date.getDate();
        
        var id = this.id();
        
        p.height = 250;
        p.autoHeight = false;
        
        if(!p.side)
            p.side = [];
        
        p.side.push({
            width:40,
            region:"left",
            style:{
                background:"#ededed url("+inx.img("left")+") center no-repeat"
            },
            listeners:{
                click:function(){inx(id).cmd("doOffset",-1)}
            }
        });
        
        p.side.push({
            width:40,
            region:"right",
            style:{
                background:"#ededed url("+inx.img("right")+") center no-repeat"
            },                
            listeners:{
                click:function(){inx(id).cmd("doOffset",1)}
            }
        });
        
        //p.height = 400;
        
        this.base(p);
        
        this.cmd("autoOffset");
    },
    
    cmd_render:function(c) {
        this.base(c);
        
        var id = this.id();
        
        this.nowContainer = $("<div>").addClass("inx-calendar-now").appendTo(this.__body);            
        var nowButton = inx({
            type:"inx.button",
            text:"Сегодня",
            icon:"calendar",
            air:true,
            onclick:[this.id(),"setNow"]
        }).cmd("render").cmd("appendTo",this.nowContainer).setOwner(this);
            
        this.mm = [];
        this.cmd("updateMonths");
    },
    
    // Возвращает число видимых месяцев
    info_visibleMonths:function() {
        return Math.max(1,Math.floor(this.info("clientWidth")/220));
    },
    
    // Возвращает число видимых месяцев
    info_monthSpacing:function() {
        var n = this.info("visibleMonths");
        var ret = (this.info("clientWidth") - n*200)/(n+1);
        return Math.max(0,ret);
    },
    
    // Рендерит месяца
    cmd_updateMonths:function() {
    
        if(!this.__body)
            return;
        
        var n = this.info("visibleMonths");       
        
        var spacing = this.info("monthSpacing");

        for(var i=0;i<n;i++) {
            if(!this.mm[i]) {
            
                var e = $("<div >").appendTo(this.__body).css({
                    position:"absolute"
                });
                
                var month = inx({
                    type:"inx.calendar.month",
                    style:{
                        border:0,
                    },
                    listeners:{
                        select:[this.id(),"handleClick"]
                    }
                }).cmd("render").cmd("appendTo",e)
                .setOwner(this)
                .data("container",e);
                
                this.mm[i] = month;
            }
            
            this.mm[i].data("container").css({
                left:spacing + i*(200+spacing)
            });
            
            var p = this.offset(i-n/2+1);
            if(p.month!=this.month || p.year!=this.year)
                p.day = 0;    
                
            this.mm[i].cmd("setData",p);
        }
        
        for(i=n;i<this.mm.length;i++) 
            if(this.mm[i]) {
                this.mm[i].task("destroy").data("container").remove();
                this.mm[i] = false;
            }
        
    },
    
    cmd_syncLayout:function() {
        this.task("updateMonths");
        this.nowContainer.css({
            left:(this.info("clientWidth") - this.nowContainer.width())/2
        });
        this.base();
    },

    // Передвигает следующие/предыдущие месяца
    cmd_doOffset:function(offset) {
        this.__offset+=offset;
        this.cmd("updateMonths");
    },
    
    offset:function(offset) {
        offset = Math.floor(offset);
        offset += this.__offset;
        return {year:Math.floor(offset/12),month:(offset%12)+1,day:this.day};
    },
    
    cmd_handleClick:function(p) {        
        this.fire("click",p);
        this.cmd("select",p);
    },
        
    cmd_select:function(p) {
    
        if(!p) p = {};
        
        var date = new Date();
        if(!p.year) p.year = date.getFullYear();
        if(!p.month) p.month = date.getMonth()+1;
        if(!p.day) p.day = date.getDate();
      
        var day = p.day*1;
        var month = p.month*1;
        var year = p.year*1;
        
        if(day!=this.day | month==this.month | year==this.year) {            
            this.year = year;
            this.month = month;
            this.day = day;            
            this.cmd("updateMonths");
        }
        
        this.fire("select",p.year+"-"+p.month+"-"+p.day);
    },
    
    cmd_setNow:function() {
        this.cmd("handleClick",0);
        this.cmd("autoOffset");
    },
    
    cmd_autoOffset:function() {
        this.__offset = this.year*12 + this.month - 1;
        this.task("updateMonths");
    }

})
