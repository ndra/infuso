// @include inx.dialog,inx.form

inx.ns("inx.mod.admin").login = inx.dialog.extend({

    constructor:function(p) {
    
        p.title = "Вход";
        p.width = 400;
        p.height = 210;
        
        p.layout = "inx.layout.fit";
        
        p.style = {
            border:0
        }        
                
        this.superadmin = inx({
            type:"inx.mod.admin.login.superadmin",
            region:"bottom",
            autoHeight:true,
            style:{
                border:0
            },
            listeners:{
                info:[this.id(),"handleInfo"]
            }      
        });     
        p.side = [this.superadmin];   
           
        this.form = inx({
            type:"inx.mod.admin.login.logpass",
            listeners:{
                info:[this.id(),"handleInfo"]
            },
            style:{
                border:0
            }
        });
        
        p.items = [this.form];   
        p.closeButton = !p.startup;
        
        this.base(p);
    },
    
    cmd_render:function(c) {
        this.base(c);
        this.call({cmd:"admin:login:info",url:window.location.pathname},[this.id(),"handleInfo"]);
    },
    
    cmd_handleInfo:function(data) {
        this.form.cmd("handleData",data.user);
        this.superadmin.cmd("handleData",data.superadmin);
        if(this.access!==undefined && this.access!=data.access) {
            setTimeout(function(){window.location.reload();},350);
            //this.el.fadeOut("fast");
        }
        this.access = data.access;
    }
        
});