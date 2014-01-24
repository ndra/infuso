// @include inx.dialog,inx.form
/*-- /mod/bundles/admin/inx.mod.admin/login.js --*/


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

/*-- /mod/bundles/admin/inx.mod.admin/login/logpass.js --*/


inx.mod.admin.login.logpass = inx.form.extend({

    constructor:function(p) {
    
        if(!p.style)
            p.style = {}
            
        p.autoHeight = true;
        p.style.background = "none";
        p.style.border = 0;        
        
        this.base(p);
        this.on("submit","login");
    },
    
    cmd_login:function() {
        var data = this.info("data");
        this.call({
            cmd:"admin_login:standartLogin",
            email:data.email,
            password:data.password,
            keep:data.keep,
            url:window.location.pathname
        },[this.id(),"handleAction"]);
    },
    
    cmd_handleData:function(data) {
        this.items().cmd("destroy");
        if(data.email) {
            this.cmd("add",{
                labelAlign:"top",
                type:"inx.panel",
                html:"Вы &mdash; "+data.email,
                style: {
                    height:20,
                    border:0
                }
            });
            this.cmd("add",{labelAlign:"top",type:"inx.button",text:"Выйти",onclick:[this.id(),"logout"]});            
        } else {
        
            // Email
            var email = inx({
                labelAlign:"left",
                type:"inx.textfield",
                label:"Электронная почта",
                width:150,
                name:"email"
            });
            this.cmd("add",email);
            email.cmd("focus");
            
            // Пароль
            this.cmd("add",{
                labelAlign:"left",
                label:"Пароль",
                width:150,
                name:"password",
                password:true
            });
            
            // Пароль
            this.cmd("add",{
                type:"inx.checkbox",
                labelAlign:"left",
                label:"Запомнить меня",
                name:"keep"
            });
            
            this.cmd("add",{
                type:"inx.button",
                text:"Войти",
                onclick:[this.id(),"login"],
                label:"&nbsp;",
                labelAlign:"left"
            });
            
        }
    },
    
    cmd_logout:function() {
        this.call({cmd:"admin_login:standartLogout",url:window.location.pathname},[this.id(),"handleAction"]);
    },
    
    cmd_handleAction:function(data) {
        this.fire("info",data);
    }
        
});


/*-- /mod/bundles/admin/inx.mod.admin/login/superadmin.js --*/


inx.mod.admin.login.superadmin = inx.panel.extend({

    constructor:function(p) {
        p.autoHeight = true;
        if(!p.style)
            p.stylr = {};
        p.style.padding = 20;
        p.layout = "inx.layout.absolute";
        this.base(p);
    },

    cmd_render:function(c) {        
        this.base(c);
        this.on("submit",[this.id(),"login"]);
    },
    
    cmd_showSuperadminForm:function() {
        this.items().cmd("destroy");
        var pass = inx({
            type:"inx.textfield",
            password:true,
            width:150,
            x:20,
            y:0,
            name:"password"
        });
        this.cmd("add",pass);
        pass.task("focus");
        this.cmd("add",{
            type:"inx.button",
            text:"OK",
            onclick:[this.id(),"login"],
            x:171,
            y:0
        })
    },
    
    cmd_handleData:function(zuper) {
        this.items().cmd("destroy");        
        if(!zuper) {
            this.style("background","#dcdbdc");
            this.cmd("add",{type:"inx.button",text:"Администраторский пароль",onclick:[this.id(),"showSuperadminForm"],scope:this,x:20,y:0});
        }
        if(zuper) {
            this.style("background","#f2eaa4");
            this.cmd("add",{type:"inx.button",text:"Администраторский пароль введен",onclick:[this.id(),"logout"],x:20,y:0});
        }
    },
    
    cmd_logout:function() {
        this.call({cmd:"admin_login:superadminLogout",url:window.location.pathname},[this.id(),"handleAction"]);
    },
    
    cmd_login:function() {
        this.call({cmd:"admin_login:superadminLogin",password:this.info("data").password,url:window.location.pathname},[this.id(),"handleAction"]);
    },
    
    cmd_handleAction:function(data) {
        this.fire("info",data);
    }

});

