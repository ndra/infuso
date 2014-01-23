// @link_with_parent

inx.css(
    ".lbqku67cehdc1stye2om{padding:0px 0px 0px 10px;margin-right:10px;cursor:pointer;background:url(/reflex/res/up.gif) no-repeat left center;}",
    ".gsxma6d4ubdy9sopvh78{font-size:18px;}"
)

inx.mod.reflex.editor.breadcrumbs = inx.panel.extend({
    
    constructor:function(p) {
        p.style = {
            padding:20,
            background:"#f6f6f6",
            height:"content"
        }
        this.base(p);
    },
    
    cmd_render:function(c) {
        this.base(c);
        this.cmd("setData",this.data);
        this.pathContainer = $("<div>");
        //this.cmd("html",this.pathContainer);
        if(this.data)
            this.cmd("setData",this.data);
    },
    
    cmd_setData:function(data) {
        if(!data) return;    
        var id = this.id();
        if(!data || !data.length) return;
        
        // Если объект еще не отрендерен
        if(!this.pathContainer) {
            this.data = data;
            return;
        }
        
        this.pathContainer.html("");
        
        $("<div>").css({
            position:"absolute",
            right:5,
            top:5,
            cursor:"pointer"
        }).html("&times; закрыть").click(inx.cmd(this.owner(),"stepBack")).appendTo(this.pathContainer);        

        for(var i=0;i<data.length;i++) if(data[i]) {
            var text = (data[i] && data[i].text+"") || "";
            var text = $.trim(text);
            if(!text) text = "&mdash;";
            var e = $("<div>").appendTo(this.pathContainer).html(text);
            
            // Маленькие буквы
            if(data[i+1]) {
                e.addClass("inx-core-inlineBlock");
                e.addClass("lbqku67cehdc1stye2om");
                e.data("index",data[i]["index"]+"")
                .click(function(){
                    inx(id).bubble("editItem",$(this).data("index"));                
                })
                .mouseover(function(){ $(this).css({color:"black",textDecoration:"underline"}) })
                .mouseout(function(){ $(this).css({color:null,textDecoration:"none"}) })
            // Большие буквы
            } else {
                e.addClass("gsxma6d4ubdy9sopvh78");
            }
        }
        this.cmd("html",this.pathContainer);
    }
    
});