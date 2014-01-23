// @include inx.panel,inx.button
/*-- /mod/bundles/inx/src/inx/calendar.js --*/

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


/*-- /mod/bundles/inx/src/inx/calendar/month.js --*/


inx.css(
    ".inx-calendar-day{border:1px solid #ededed;cursor:pointer;background:#deded;padding:3px;width:13px;height:13px;position:absolute;text-align:center;font-size:11px;}",
    ".inx-calendar-day-selected{border:1px solid blue;color:black;background:#d9e8fb;}"
);

inx.calendar.month = inx.box.extend({

    constructor:function(p) {
        if(!p) p ={};
        p.width = 200;
        p.height = 220;        
        p.background = "none";
        this.local = {
            dayOfWeek:["вс","пн","вт","ср","чт","пт","сб"],
            month:[null,"Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь","Октябрь","Ноябрь","Декабрь"],
            startWeekFrom:1
        }
        this.base(p);        
    },
    
    cmd_render:function(c) {
        this.base(c);
        
        this.monthTitle = $("<div>")            
            .css({width:200,textAlign:"center",marginTop:20})
            .appendTo(this.el);
            
        this.days = [];
        for(var week=0;week<6;week++)
            for(var wDay=0;wDay<7;wDay++)  
                this.days[wDay+week*7] = $("<div />")
                .appendTo(this.el)
                .addClass("inx-calendar-day")
                .css({color:wDay>=5 ? "red" : ""})
                .css({left:wDay*25+14,top:week*25+50})
                .click(inx.cmd(this,"private_handleSelect"));
                
    },
    
    // Возвращает количество дней в месяце
    dayCount:function(y,m) {
        var d = new Date(y,m-1,32);
        return 32-d.getDate();
    },    
    
    redraw:function() {    
        // Обновляем название месяца
        this.monthTitle.html(this.local.month[this.month] +" "+this.year)
    
        var firstDay = new Date(this.year,this.month-1,1).getDay();
        var days = this.dayCount(this.year,this.month);
        var mDay = 1;    
                       
        for(var week=0;week<=5;week++)
        for(var wDay=0;wDay<7;wDay++)                
        if((week>0 | wDay>=(firstDay+7-this.local.startWeekFrom)%7) & mDay<=days) {        
            this.days[week*7+wDay].html(mDay);
            this.days[week*7+wDay].css("display","block");            
            if(mDay==this.day)
                this.days[week*7+wDay].addClass("inx-calendar-day-selected");
            else
                this.days[week*7+wDay].removeClass("inx-calendar-day-selected");
            mDay++;    
            
        } else {
            if(week==0 | week>=4) this.days[week*7+wDay].css("display","none");
        }
    },
    
    cmd_private_handleSelect:function(e) {
        var day = $(e.target).html()*1;
        var data = {year:this.year,month:this.month,day:day};
        this.fire("select",data);
    },
    
    cmd_setData:function(data) {
        this.year = data.year;
        this.month = data.month;
        this.day = data.day;
        this.redraw();
    }

})


