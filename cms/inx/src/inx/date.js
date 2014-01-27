// @include inx.textfield

inx.date = inx.textfield.extend({

    constructor:function(p) {
    
        this.mask = p.time ? "d.m.y h:i:s" : "d.m.y";
        
        var width = p.time ? 125 : 70;
        width+=20;
        
        if(!p.style)
            p.style = {};
            
        p.style.width = width;
        
        p.buttons = [{
            icon:"calendar",
            onclick:[this.id(),"expand"]
        }]
        
        // Если value отсутствует, устанавливаем текущее время
        if(p.value===undefined) {
            p.value = new Date();
        }
        
   
        this.base(p);
        this.on("blur","normalizeDate");
    },
    
    dialog:function() {
    
        if(!this.private_dialog) {
        
            // Год, месяц и день для календаря
            // Если поле пустое, используем сегодняшние
            if(this.info("value")) {
                var year = this.info("year");
                var month = this.info("month");
                var day = this.info("day");
            } else {
                var date = new Date();
                var year = date.getFullYear();
                var month = date.getMonth()+1;
                var day = date.getDate();
            }        
        
            this.private_dialog = inx({
                type:"inx.dialog",
                modal:false,
                clipToOwner:true,
                showTitle:false,
                autoHide:true,
                width:320,
                height:250,
                items:[{
                    type:"inx.calendar",
                    year:year,
                    month:month,
                    day:day,                    
                    style:{
                        width:320,
                        border:0
                    },
                    listeners:{
                        select:[this.id(),"handleCalendarNative"]
                    }
                }]
            }).setOwner(this).cmd("render");
        }
        return this.private_dialog;
    },
    
    cmd_handleCalendarNative:function(p) {
        this.fire("calendar",{value:p});
        this.cmd("setValue",p);
        this.task("focus");
        this.fire("aftercalendar",{value:p});
    },

    cmd_expand:function(e) {
        this.dialog().cmd("show").cmd("focus");
    },
    
    cmd_collapse:function() {
        this.dialog().cmd("hide");
    },

    /**
     * Устанавливает текущую дату
     **/
    cmd_setNow:function() {
        this.cmd("setValue",new Date());
    },

    private_split:function() {
        return $.trim((this.info("textValue")+"").replace(/[^1234567890]+/g," ")).split(" ");
    },
    
    /**
     * Приводит строку в соответствие маске
     **/
    private_normalizedString:function() {
        if(!this.info("textValue")) return "";
        var s = this.private_split();
        var ret = "";
        var index = 0;
        for(var i=0;i<this.mask.length;i++) {
            var c = this.mask[i];
            var part = s[index] || "";
            switch(c) {
                case "d":
                case "m":
                case "h":
                case "i":
                case "s":
                    ret+= inx.strPadRight(part.substr(0,2),2);
                    index++;
                    break;
                case "y":
                    ret+= inx.strPadRight(part.substr(0,4),4);                
                    index++;
                    break;
                default:
                    ret+= c;
                    break;
            }
        }
        return ret;        
    },
    
    private_date:function() {
        var date = {
            hours:0,
            minutes:0,
            seconds:0
        };
        var s = this.private_split();
        var index = 0;
        for(var i=0;i<this.mask.length;i++) {
            var c = this.mask[i];
            var part = s[index]*1 || "";
            switch(c) {
                case "y": date.year = part; index++; break;
                case "m": date.month = part-1; index++; break;
                case "d": date.day = part; index++; break;
                case "h": date.hours = part; index++; break;
                case "i": date.minutes = part; index++; break;
                case "s": date.seconds = part; index++; break;
            }
        }
        
        return new Date(
            date.year,
            date.month,
            date.day,
            date.hours,
            date.minutes,
            date.seconds
        );
    },
    
    /**
     * Возвращает расчитанное текстовое значение поля
     * Расчитанное значение может отличаться от того, что находжится в данный момент в текстовом поле
     * Расчитанное значение используется для обновления текстового поля
     **/
    info_calculatedTextValue:function() {
    
        var val = this.private_value;
        var ret = "";
            
        for(var i=0;i<2;i++)
        if(val) {
    
            val = $.trim((val+"").replace(/[^1234567890]+/g," ")).split(" ");
            var date = new Date(val[0],val[1]-1,val[2],val[3]||0,val[4]||0,val[5]||0);
            
            var index = 0;
            for(var i=0;i<this.mask.length;i++) {
                var c = this.mask[i];
                switch(c) {
                    case "d": ret+= inx.strPadLeft(date.getDate(),2); index++;break;
                    case "m": ret+= inx.strPadLeft(date.getMonth()+1,2); index++;break;
                    case "y": ret+= date.getFullYear(); index++;break;
                    case "h": ret+= inx.strPadLeft(date.getHours(),2); index++;break;
                    case "i": ret+= inx.strPadLeft(date.getMinutes(),2); index++;break;
                    case "s": ret+= inx.strPadLeft(date.getSeconds(),2); index++;break;
                    default: ret+= c; break;
                }
            }
        }
        
        return ret;
        
    },
    
    cmd_setValue:function(val) {
    
        if(val instanceof Date) {
            var ret = "";
            ret+= val.getFullYear()+"-";
            ret+= (val.getMonth()+1)+"-";
            ret+= val.getDate()+" ";
            ret+= val.getHours()+":";
            ret+= val.getMinutes()+":";
            ret+= val.getSeconds();
            this.cmd("setValue",ret);
            return;
        }

        this.private_value = val;
        
        var text = this.info("calculatedTextValue");
        this.cmd("setTextValue",text);
        
        this.task("normalizeDate");
    },
    
    info_value:function() {
    
        // Если поле пустое, компонент возвращает null
        if(!$.trim(this.info("textValue")))
            return null;
    
        var date = this.private_date();
        
        var ret = 
            inx.strPadLeft(date.getFullYear(),4)+"-"+
            inx.strPadLeft(date.getMonth()+1,2)+"-"+
            inx.strPadLeft(date.getDate(),2);
        
        if(this.time) {
            ret+= " " + inx.strPadLeft(date.getHours(),2)+":"+
            inx.strPadLeft(date.getMinutes(),2)+":"+
            inx.strPadLeft(date.getSeconds(),2);
        }            
        
        return ret;
    
    },
    
    info_year:function() {
        return this.private_date().getFullYear();
    },
    
    info_month:function() {
        return this.private_date().getMonth()+1;
    },
    
    info_day:function() {
        return this.private_date().getDate();
    },    
    
    /**
     * Устанавливает часы
     **/
    cmd_setHours:function(h) {
        var date = this.private_date();
        date.setHours(h);
        this.cmd("setValue",date);
    },
    
    /**
     * Устанавливает минуты
     **/
    cmd_setMinutes:function(m) {
        var date = this.private_date();
        date.setMinutes(m);
        this.cmd("setValue",date);
    },
    
    /**
     * Устанавливает секунды
     **/
    cmd_setSeconds:function(m) {
        var date = this.private_date();
        date.setSeconds(m);
        this.cmd("setValue",date);
    },

    cmd_normalizeDate:function() {
        var s = this.private_normalizedString();        
        this.cmd("setTextValue",s);
    },    
    
    cmd_destroy:function() {
        this.dialog().task("destroy");
        this.base();
    }
    
})
