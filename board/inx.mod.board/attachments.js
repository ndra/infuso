// @include inx.list

inx.ns("inx.mod.board").attachments = inx.list.extend({

    constructor:function(p) {
    
        p.loader = {
            cmd:"board/controller/attachment/listFiles",
            sessionHash:p.sessionHash || null,
            taskID:p.taskID
        }
        
        p.layout = "inx.layout.column";
        
        if(!p.style) {
            p.style = {};
        }
        
        p.side = [{
            type:"inx.file",
            hidden:true,
            region:"top",
            dropArea:p.dropArea,
            loader:{
                cmd:"board/controller/attachment/uploadFile",
                "sessionHash":p.sessionHash || null,
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
            border:"1px solid #ededed",
            background:"url("+data.preview+") center center no-repeat"
        }).appendTo(e);
        
        var text = $("<div>").css({
            textAlign:"center",
            "text-overflow":"ellipsis",
            opacity:.5,
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