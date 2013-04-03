// @link_with_parent

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
