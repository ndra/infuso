// @include inx.panel,inx.list

inx.select = inx.box.extend({

    constructor:function(p) {
    
        if(!p.labelAlign) {
            p.labelAlign = "left";
        }    
            
        p.height = 22;
        p.autoHeight = false;
        
        if(!p.width) {
            p.width = 300;
        }
            
        p.style = {
            border:0,
            background:"none"
        }
        
        this.base(p);
        if(p.onchange)
            this.on("change",p.onchange);
    },
    
    cmd_render:function(c) {
    
        this.base(c);        
        this.cmd("createList");
        this.suspendEvents();
        if(this.value!==undefined) {
            this.cmd("setValue",this.value);
        }
        this.unsuspendEvents();
        this.el.css({cursor:"pointer",overflow:"hidden"});
        this.content = $("<div>").css({position:"absolute",height:16,padding:"3px 4px",overfloe:"hidden"}).appendTo(this.el);
        this.trigger = $("<img>").attr("src",inx.img("trigger")).appendTo(this.el).css({position:"absolute",top:2});
        
        this.suspendEvents();
        if(this.value!==undefined) this.cmd("setValue",this.value);
        this.unsuspendEvents();
    },    
    
    cmd_syncLayout:function() {
    
        this.content.css({left:16}).width(this.info("width")-16);
        this.trigger.css({left:0});
        
        this.dialog().cmd("width",this.info("width"));
        this.base();
        
    },
    
    cmd_createList:function() {
        this.private_list = inx({
            type:"inx.list",
            loader:this.loader,    
            data:this.data,    
            xx:this.xx,    
            vscroll:true,
            style:{
                border:0,
                maxHeight:200
            },
            listeners:{
                itemclick:[this.id(),"native_handleSelect"],
                afterdata:[this.id(),"handleData"]
            }
        });
        
        //this.private_list.cmd("setData",this.data);
        
        this.private_dialog = inx({
            type:"inx.dialog",
            background:"white",
            showTitle:false,
            modal:false,
            style:{
                border:0
            },
            clipTo:this.el,
            items:[this.private_list]            
        }).cmd("hide").cmd("render").setOwner(this);
    },
    
    axis_list:function() {
        return this.private_list;
    },
    
    cmd_load:function() {
        inx(this).axis("list").cmd("load");
    },
    
    cmd_handleSmoothBlur:function() {
        this.cmd("collapse");
    },

    cmd_collapse:function() {
        this.private_dialog.cmd("hide");
    },
    
    cmd_expand:function() {
        this.private_dialog.cmd("show");
        this.list().task("focus").cmd("select",this.info("value"));
    },
    
    list:function() {
        return this.private_list || inx();
    },
    
    dialog:function() {
        return this.private_dialog || inx();
    },
    
    cmd_setData:function(data) {
    
        
        
        this.list().cmd("setData",data);
        //this.cmd("setText",this.idToValue(this.info("value")));
    },
    
    cmd_handleData:function(a,b) {
        var v = this.idToValue(this.info("value"));
        this.cmd("setText",v);
        this.fire("data",a,b);
    },
    
    cmd_native_handleSelect:function(id) {
        this.cmd("setValue",id);
        this.cmd("collapse");
        this.task("focus");
    },
    
    cmd_setText:function(txt) {
    
        if(!this.content)
            return;
        this.content.html("");
        if(typeof(text)=="object")
            txt.appendTo(this.content)
        else
            this.content.html(txt);
    },
    
    idToValue:function(id) {
        var item = this.list().info("item",id);
        if(item) {            
            var e = $("<div>");
            this.list().cmd("renderNodeTo",id,e);            
            return e;
        } else {
            return "&mdash;";
        }
    },
    
    info_value:function() { return this.value; },
    
    cmd_setValue:function(val) {
        if(this.value!=val) {
            this.value = val;
            this.fire("change",val);
        }        
        this.cmd("setText",this.idToValue(val));
    },    

    cmd_keydown:function(e) {
        
        switch(e.keyCode) {
            case 27:
                this.cmd("collapse");
                this.task("focus");
                break;
            case 13:
            case 38:
            case 40:
                this.cmd("expand");
                return false;
        }
        this.base(e);
    },
    
    cmd_mousedown:function() {
        this.cmd("expand");
    },
    
    cmd_destroy:function() {
        inx(this.private_dialog).cmd("destroy");
        this.base();
    }
    
});
