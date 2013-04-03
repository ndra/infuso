// @link_with_parent
// @include inx.list

inx.css(".yezxyar3p .inx-list-item{border:1px solid rgba(0,0,0,0)} ");
inx.css(".yezxyar3p .inx-list-item-selected{border:1px solid black} ");

inx.mod.board.task.color = inx.list.extend({

    constructor:function(p) {
    
        if(!p.style) {
            p.style = {};
        }
        p.style.background = "none";
        p.style.border = 1;
        p.style.padding = 0;
        
        p.labelAlign = "left";
        p.layout = "inx.layout.column";
        p.data = [
            {id:"#ffffff"},
            {id:"#FFFACD"},
            {id:"#FFC0CB"},
            {id:"#BFEFFF"}
        ];
        this.base(p);
        this.on("render",function() {
            this.__body.addClass("yezxyar3p");
        });
        
        if(!this.value)
            this.value = "#ffffff";
        this.cmd("setValue",this.value);
    },
    
    renderer:function(e,data) {
        e.css({
            display:"inline-block",
            width:24,
            height:24,
            marginRight:4,
            background:data.id,
            padding:0
        });
    },
    
    info_itemConstructor:function() {
        return {
            type:"inx.list.item",
            width:24+8,
            height:24+8,
            style :{
                border:1
            }
        };
    },
    
    cmd_setValue:function(val) {
        this.cmd("select",val);
    },
    
    info_value:function() {
        return this.info("selection")[0];
    }
         
});