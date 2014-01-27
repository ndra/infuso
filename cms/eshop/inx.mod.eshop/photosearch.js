// @include inx.form

inx.ns("inx.mod.eshop").photosearch = inx.dialog.extend({

    constructor:function(p) {
    
        p.title = "Подбор фотографий";
        p.layout = "inx.layout.fit";
                
        this.search = inx({
            type:"inx.textfield",
            onchange:[this.id(),"search"]
        });    
        
        p.width = 900;
        p.height = 500;
        p.autoHeight = false;
        if(!p.ids.length)
            this.task("destroy");
        
        this.results = inx({
            type:"inx.mod.eshop.photosearch.results",            
            width:450,
            resizable:true,
            region:"left",
            tbar:[this.search],            
            onitemdblclick:[this.id(),"addPhoto"],
            onload:[this.id(),"handleSearch"]
        });
        
        this.previews = inx({
            type:"inx.mod.eshop.photosearch.selected",
            ids:p.ids   
        });
        
        p.side = [this.results];    
        p.items = [this.previews];
        
        this.base(p);
        this.virgin = true;
        this.cmd("search");
    },
    
    cmd_search:function() {
        var q = this.search.info("value");
        this.results.cmd("setLoader",{
            cmd:"eshop:yandexGrabber:photo:search",
            query:q,
            first:this.virgin,
            itemID:this.ids[0]
        }).cmd("load");
        this.virgin = false;
    },
    
    cmd_handleSearch:function(data,meta) {
        if(meta.query) {
            this.search.cmd("suspendEvents");
            this.search.cmd("setValue",meta.query);
            this.search.cmd("unsuspendEvents");
        }
    },
    
    cmd_addPhoto:function(id) {
        var url = this.results.info("item",id,"url");
        this.previews.cmd("addPhoto",url);
    }
     
});