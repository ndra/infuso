// @link_with_parent

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