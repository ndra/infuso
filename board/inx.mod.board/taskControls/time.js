// @link_with_parent

inx.mod.board.taskControls.time = inx.panel.extend({

    constructor:function(p) {  
    
        p.style = {
            width:240,
            height:36
        }
        this.base(p);
        this.cmd("handleData",p.data);
        
        console.log(p.data);
        
    },
    
    cmd_handleData:function() {
    
        var width = this.info("width") - 2;
        var height = this.info("height") - 2;
        
        var percent = .66;
    
        var e = $("<div>").css({
            width:width,
            height:height,
            border:"1px solid #ccc",
            position:"relative"
        });
        
        $("<div>").css({
            position:"absolute",
            left:7,
            top:7,
            color:"black",
            whiteSpace:"nowrap"
        }).html("<b>В бэклоге</b>, протрачено 12 из 16.7 ч.").appendTo(e);
        
        var blue = $("<div>").css({
            background:"blue",
            width:width*percent,
            height:height,
            position:"absolute",
            overflow:"hidden",
            left:0,
            top:0
        }).appendTo(e);
        
        $("<div>").css({
            position:"absolute",
            left:7,
            top:7,
            color:"white",
            whiteSpace:"nowrap"
        }).html("<b>В бэклоге</b>, протрачено 12 из 16.7 ч.").appendTo(blue);
    
        this.cmd("html",e);
    }
         
});