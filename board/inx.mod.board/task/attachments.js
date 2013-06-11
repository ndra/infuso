// @link_with_parent
// @include inx.list

inx.mod.board.task.attachments = inx.list.extend({

    constructor:function(p) {
    
        p.loader = {
            cmd:"board/controller/attachment/listFiles",
            taskID:p.taskID
        }
        
        p.layout = "inx.layout.column";
        
        if(!p.style) {
            p.style = {};
        }
        
        p.style.maxHeight = 200;
        
        p.side = [{
            type:"inx.file",
            hidden:true,
            region:"top",
            dropArea:p.dropArea,
            loader:{
                cmd:"board/controller/attachment/uploadFile",
                taskID:p.taskID,
            }, oncomplete:function() {
                this.owner().cmd("load");
            }
        }]
    
        this.base(p);
        
        this.on("itemdblclick",[this.id(),"handleDblClick"]);
        
    },
    
    info_itemConstructor:function(data) {
        var ret = this.base(data);
        ret.width = 108;
        return ret;
    },
    
    renderer:function(e,data) {
    
        var preview = $("<div>").css({
            width:100,
            height:100,
            background:"url("+data.preview+") center center no-repeat"
        }).appendTo(e);
        
        var text = $("<div>").css({
            textAlign:"center",
            "text-overflow":"ellipsis",
            overflow:"hidden",
            fontSize:11,
            width:100
        }).html(data.name+"").appendTo(e);
    
    },
    
    cmd_handleDblClick:function(id) {
        var url = this.info("item",id,"url");
        window.open(url);
    }
     
});