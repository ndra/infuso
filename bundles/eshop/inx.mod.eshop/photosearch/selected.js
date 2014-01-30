// @include inx.list
// @link_with_parent

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