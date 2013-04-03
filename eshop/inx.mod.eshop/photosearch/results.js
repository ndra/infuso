// @include inx.list
// @link_with_parent

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