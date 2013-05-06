// @link_with_parent
// @include inx.list

inx.mod.board.task.attachments = inx.list.extend({

    constructor:function(p) {
    
        p.loader = {
            cmd:"board/controller/attachment/listFiles",
            taskID:p.taskID
        }
        
        p.layout = "inx.layout.column";
    
        this.base(p);
        
        this.on("itemdblclick",[this.id(),"handleDblClick"]);
        
    },
    
    info_itemConstructor:function(data) {
        var ret = this.base(data);
        ret.width = 108;
        return ret;
    },
    
    renderer:function(e,data) {
    
        var container = $("<div>").css({
            width:100,
            height:100,
            background:"url("+data.preview+") center center no-repeat"
        }).appendTo(e);
    
    },
    
    cmd_handleDblClick:function(id) {
        var url = this.info("item",id,"url");
        window.open(url);
    }
     
});