// @include inx.panel

inx.gallery = inx.panel.extend({

    constructor:function(p) {
        this.cmd("setData",p.data);
        if(p.time===undefined)
            p.time = 15000;
        this.base(p);
        this.private_allowResize = (p.allowResize === undefined) ? true : (!!p.allowResize);
        this.private_valign = p.valign || "center";
        if(p.onselect)this.on("select",p.onselect);
        if(p.onclick)this.on("click",p.onclick);
    },    

    cmd_setData:function(data) {
        if(!data) data = [];
        this.data = data;
        for(var i in this.data)
            if(!this.data[i].id) this.data[i].id = inx.id();
        this.cmd("selectFirst",0);
    },
    
    cmd_render:function(c) {
        this.base(c);
        this.__body.css({overflow:"hidden",overflowY:"hidden"}); // overflowY - для ебучего ИЕ
        var cmpid = this.id();
        this.private_imgContainer = $("<div>")
            .css({position:"absolute",left:0})
            .appendTo(this.__body)
            .click(function(){
                var c = inx(cmpid);
                c.fire("click",c.info("current"));
                var item = c.info("item",c.info("current"));
                if(item.url) window.location.href = item.url;
            });
                
        this.private_descriptionBg = $("<div>").css({
            background:"black",
            position:"absolute",
            width:"100%",
            left:0
        }).appendTo(this.__body);
        
        this.private_description = $("<div>").css({
            position:"absolute",
            left:0,
            width:"100%",
            color:"white"
        }).appendTo(this.__body);
        
        var id = this.id();
        
        this.private_left = $("<div>").appendTo(this.__body)
            .css({
                background:"url("+inx.conf.url+"/inx/gallery/left.png"+")",
                width:38,
                height:48,
                position:"absolute",                
                cursor:"pointer"
            }).mousedown(function(){ inx(id).cmd("selectPrev"); })
            
        this.private_right = $("<img>").appendTo(this.__body)
            .attr("src",inx.conf.url+"/inx/gallery/right.png")
            .css({position:"absolute",cursor:"pointer"})
            .mousedown(function(){ inx(id).cmd("selectNext"); })         
        
        if(this.info("current"))
            this.cmd("select",this.info("current"));
        else
            this.cmd("selectFirst");
        
        if(this.loader) this.cmd("load");
    },
    
    info_current:function() {
        return this.sel;
    },
    
    cmd_load:function() {
        this.call(this.loader,[this.id(),"setData"]);
    },
    
    info_item:function(id) {
        for(var i in this.data)
            if(this.data[i].id==id)
                return this.data[i];
        return null;
    },
    
    cmd_select:function(id) {
    
        this.sel = id;
        if(!this.__body) return;
        var item = this.info("item",id);
        if(!item) return;

        if(this.time) {
            clearTimeout(this.private_timeout);
            var _id = this.id();
            if(this.data.length>1)
            this.private_timeout = setTimeout(function(){ inx(_id).cmd("selectNext")}, 15000);
        }
        
        var cid = this.id();
        var ee = $("<div>").appendTo(this.private_imgContainer);
        var e = $("<img>").attr("src",item.img+"").appendTo(this.private_imgContainer)
        .css({
            position:"absolute",
            display:"block",
            left:0,
            top:0,
            opacity:0
        }).load(function(){
            $(this).data("baseWidth",$(this).width()).data("baseHeight",$(this).height());
            $(this).animate({opacity:1},1000,function(){
                $(this).prevAll().animate({opacity:0},500,function(){ $(this).remove(); })
            });
            inx(cid).task("updateImages");
        });
        
        this.cmd("setDescription",item.text);
        this.fire("select",id,item);
    },
    
    // Возвращает порядковый номер элемента
    info_itemOffset:function(id) {
        var index = 0;
        for(var i in this.data) {
            if(this.data[i].id==id)
                return index;
            index++;
        }
    },
    
    cmd_selectFirst:function() {
        var first = this.data[0];
        if(!first) return;
        this.cmd("select",first.id);
    },
    
    cmd_selectNext:function() {
        var sel = this.info("itemOffset",this.sel)+1;
        sel = sel%this.data.length;
        this.cmd("select",this.data[sel].id);
    },
    
    cmd_selectPrev:function() {
        var sel = this.info("itemOffset",this.sel)-1;
        sel = (sel+this.data.length)%this.data.length;
        this.cmd("select",this.data[sel].id);
    },
    
    cmd_updateImages:function() {
        var bw = this.info("bodyWidth");
        var bh = this.info("bodyHeight");
        var that = this;
        this.private_imgContainer.children(":visible").each(function(){
            var img = $(this);
            if(that.private_allowResize) {
                var k1 = bw/img.data("baseWidth");
                var k2 = bh/img.data("baseHeight");
                if(k1<k2)img.width(bw).height("auto");
                else img.height(bh).width("auto");
            }
            switch(that.private_valign) {
                case "top": var top = 0; break;
                case "middle":
                case "center": var top = (bh-img.height())/2; break;
                case "bottom": var top = (bh-img.height()); break;
            }
            img.css({left:(bw-img.width())/2,top:top})
        });
    },
    
    cmd_updateDescription:function() {
        var h = this.info("bodyHeight") - this.private_description.outerHeight();
        this.private_description.css({top:h});
        this.private_descriptionBg.css({top:h,height:this.private_description.outerHeight()});
    },
    
    renderer:function(e,txt) {
        $("<div>").css({padding:6}).appendTo(e).html(txt);
    },
    
    cmd_setDescription:function(html) {
        if(!this.private_description) return;
        var id = this.id();
        var render = this.renderer;
        this.private_description.stop(true)
            .animate({marginTop:40,opacity:0},1000,function(){
                $(this).html("");
                render($(this),html);
                inx(id).cmd("updateDescription");
            }).animate({marginTop:0,opacity:1},1000);
            
        this.private_descriptionBg.stop(true)
            .animate({marginTop:40,opacity:0},1000);
        if(html)
            this.private_descriptionBg.animate({marginTop:0,opacity:.7},1000);     
    },
    
    cmd_syncLayout:function() {
        this.base();
        this.private_left.css({top:this.info("bodyHeight")/2-20,left:0});
        this.private_right.css({top:this.info("bodyHeight")/2-20,left:this.info("bodyWidth")-40 });
        this.cmd("updateImages");
        this.cmd("updateDescription");
    }

});