// @link_with_parent

inx.mod.board.task.extra.color = inx.list.extend({

    constructor:function(p) {
    
        p.style = {
            padding:0,
            background:"none"
        }
       
        p.layout = "inx.layout.column";
       
        p.data = [{
            id:"#ffffff",
            color:"#ffffff"
        }, {
            id:"#FFFACD",
            color:"#fffacd"
        }, {
            id:"#FFC0CB",
            color:"#ffc0Cb"
        },{
            id:"#BFEFFF",
            color:"#BFEFFF"
        }]
        
        this.base(p);
        this.private_value = p.value;
        this.on("afterdata","renderValue");
        this.on("select",function(id) {
            this.private_value = id;
        });
    },
    
    info_value:function() {
        return this.private_value;
    },
    
    cmd_setValue:function(val) {
        this.private_value = val;
        this.cmd("renderValue");
    },
    
    cmd_renderValue:function() {    
        this.cmd("select",this.info("value"));
    },
    
    info_itemConstructor:function(data) {
    
        var html = $("<div>").css({
                width:18,
                height:18,
                background:data.color,
                border:"1px solid #ccc"
            }).addClass("color");
    
        var ret = {
            type:"inx.panel",
            html:html,
            style:{
                width:20,
                height:20,
                border:0
            },
            cmd_select:function() {
                this.el.find(".color").css({
                    border:"1px solid black",
                    width:18,
                    height:18
                });
            },
            cmd_unselect:function() {
                this.el.find(".color").css({
                    width:20,
                    height:20,
                    border:"none"
                });
            }
        };
        
        return ret;
        
    }
     
});