// @link_with_parent

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
