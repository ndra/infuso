// @include inx.list

inx.css(
    ".z3ka07keb{vertical-align:top;overflow:hidden;position:relative;}"
);

inx.chart = inx.list.extend({

    constructor:function(p) {
        if(p.padding===undefined) p.padding = 30;
        if(!p.colors) this.colors = ["blue","red","green","orange"]
        //this.cols = ["income","expenditure","balance"]
        this.on("data","handleChartDataNative");
        this.base(p);
    },
    
    cmd_handleChartDataNative:function(data) {
        var max = null;        
        for(var i in data)
            for(var j in this.cols) {
                var val = Math.abs(data[i][this.cols[j]]);
                if(val>max || max===null) max = val;
            }
        this.max = max;
    },
    
    renderer:function(e,data,c) {
    
        e.addClass("inx-core-inlineBlock z3ka07keb");
        var width = this.cols.length*11;
        e.css({height:150,width:width});
        
        for(var i in this.cols) {
        
            var col = this.cols[i];    
            var val = data[col]*1 || 0;
            
            if(val>0) {
                var height = val / this.max * 100;
                var top = 100 - height;
            } else {
                height = - val / this.max*100;
                top = 100;
            }
        
            var stick = $("<div>").css({
                position:"absolute",
                width:10,
                top:top+4,
                left:i*11+10,
                height:height,
                background:[this.colors[i]]
            }).attr("title",val).appendTo(e);
        }
        
        var txt = $("<div>").css({
            position:"absolute",
            left:10,
            top:110,
            fontSize:9,
            color:"gray"
        }).html(data.text).appendTo(e);
        
    }
    
});