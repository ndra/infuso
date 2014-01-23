// @link_with_parent
// @include inx.date

inx.mod.reflex.fieldFilters.range.datetime = inx.date.extend({

    constructor:function(p) { 
        p.time = true;
        this.base(p);
        if(p.end) {
            this.on("aftercalendar",[this.id(),"handleCalendar2"]);
        }
    },
    
    cmd_handleCalendar2:function(x) {
        this.cmd("setHours",23);
        this.cmd("setMinutes",59);
        this.cmd("setSeconds",59);
    }
    
    
})
