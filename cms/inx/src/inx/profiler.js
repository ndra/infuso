// @include inx.dialog

inx.profiler = inx.dialog.extend({

    constructor:function(p) {
        p.title = "Профайлер";
        p.modal = false;
        p.style = {
            width: 400,
            maxHeight: 400,
            vscroll:true
        }
        this.base(p);
        setInterval(inx.cmd(this,"updateData"),2000);
    },
    
    cmd_updateData:function() {
    
        var data = [];
        for(var i in inx.observable.debug.totalTime) {
            data.push({
                cmd:i,
                time:inx.observable.debug.totalTime[i]
            })
        }
        
        data.sort(function(a,b) {
            return b.time - a.time;
        });
    
        var html = "";
        for(var i in data) {
            var time = data[i].time;
            var count = inx.observable.debug.cmdCountByName[data[i].cmd];
            var time2 = Math.round(time/count*1000)/1000;
            
            html += data[i].cmd+": "+ time +"s. / " + count + " / " + time2 + "s.<br/>";
        }
        this.cmd("html",html);
    }
    
});

inx.debug = true;

inx({
    type:"inx.profiler"
}).cmd("render");