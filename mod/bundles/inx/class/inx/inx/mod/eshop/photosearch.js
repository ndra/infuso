// @include inx.form,inx.list
/*-- /eshop/inx.mod.eshop/photosearch.js --*/


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

/*-- /eshop/inx.mod.eshop/photosearch/results.js --*/


inx.mod.eshop.photosearch.results = inx.list.extend({

    constructor:function(p) {        
        this.base(p);
    },
    
    renderer:function(e,data) {
        var preview = data.preview;
        $("<img>").attr("src",preview+"").css({width:100}).appendTo(e);
        $("<div>").text(data.descr+"").css({fontSize:9}).appendTo(e);
        return false;
    }
     
});

/*-- /eshop/inx.mod.eshop/photosearch/selected.js --*/


inx.css(
    ".rxpvixp9iqfv2opvijcv{overflow:hidden;padding:10px;text-align:center;width:100px;vertical-align:top;font-size:10px;}"
);

inx.mod.eshop.photosearch.selected = inx.list.extend({

    constructor:function(p) {
        p.tbar = [
            {text:"Сохранить",icon:"save",onclick:[this.id(),"save"]},"|",
            {icon:"delete",onclick:[this.id(),"deleteSelectedItem"]}            
        ];
        this.base(p);
        this.previews = [];
        this.on("data","updatePreviews");
    },
    
    renderer:function(e,data) {
        e.html("");
        e.addClass("rxpvixp9iqfv2opvijcv");
        var preview = this.previews[data.url];
        if(preview) {
            $("<img>").attr("src",preview.preview).css({border:"1px solid #cccccc"}).appendTo(e);
            $("<div>").text(preview.descr+"").appendTo(e);
        } else {
            $("<div>").text(data.url+"").appendTo(e);
        }
        return false;
    }, 
    
    cmd_addPhoto:function(url) {
        this.data.push({url:url});
        this.cmd("setData",this.data);
    },
    
    cmd_updatePreviews:function() {
        var previewsToUpdate = [];
        for(var i in this.data) {
            var item = this.info("item",this.data[i].id);
            var url = item.url;
            var preview = this.previews[url];
            if(!preview) previewsToUpdate.push(url);  
        }
        if(previewsToUpdate.length)
            this.call({cmd:"eshop:yandexGrabber:photo:previews",images:previewsToUpdate},[this.id(),"handlePreviews"]);
    },
    
    cmd_handlePreviews:function(ret) {
        for(var i in ret)
            this.previews[i] = ret[i];
        this.cmd("setData",this.data);
    },
    
    cmd_save:function() {
        this.call({cmd:"eshop:yandexGrabber:photo:save",images:this.data,ids:this.ids},[this.id(),"handleSave"]);
    },
    
    cmd_handleSave:function() {
        this.bubble("refresh");
        this.owner().task("destroy");
    }
     
});

