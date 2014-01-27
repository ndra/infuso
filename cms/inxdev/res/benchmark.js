inx.benchmark = function(fn) {

    var writeReport = function() {
        var e = $("<div>");
        
        var profiler = [];
        var time = 0;
        for(var i in inx.observable.debug.totalTime) {
            time+= inx.observable.debug.totalTime[i];
            profiler.push({fn:i,time:inx.observable.debug.totalTime[i]});
        }
            
        profiler.sort(function(a,b){ return b.time - a.time })
            
        for(var i in profiler)
            $("<div>").html(profiler[i].fn + " - " +profiler[i].time).appendTo(e);
            
        $("<div style='font-size:18px;' >").html(time).appendTo(e);        
            
        e.appendTo("body");
    }

    var n = 0;
    var interval;
    var test = function() {
        fn();
        n++;
        if(n>=100) {
            clearInterval(interval);
            writeReport();
        }
    }
    interval = setInterval(test,20);
}