// @link_with_parent

inx.mod.reflex.editor.item = inx.panel.extend({

    constructor:function(p) {
    
        // Хлебные крошки
        this.breadcrumbs = inx({
            type:"inx.mod.reflex.editor.breadcrumbs",
            border:0,
            region:"top",
            height:"content"
        });  
        
        p.height = "parent";
             
        p.closable = true;
        p.side = [this.breadcrumbs];
        p.layout = "inx.layout.fit";
        
        this.base(p);
        this.cmd("requestData");
        
        this.on("show",this.id(),"handleShow");
        this.on("refresh",this.id(),"requestData");
        this.on("select",this.id(),"handleSelect");        
        
    },
   
    cmd_handleShow:function() {
        this.items().cmd("handleShow");
    },
    
    cmd_requestData:function() {
        this.call({
            cmd:"infuso:cms:reflex:controller:getItem",
            index:this.index
        },[this.id(),"handleData"]);
    },

    cmd_handleData:function(p) {
        if(p.parents)
            this.cmd("setTitle",p.parents[0].text)
        this.breadcrumbs.cmd("setData",p.parents);
        this.parents = p.parents;
        this.items().cmd("destroy");
        this.cmd("add",p.item);
    },
    
    cmd_stepBack:function() {
        this.task("destroy");        
    }

});
