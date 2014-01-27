// @include inx.form

inx.ns("inx.mod.user.editor").tools = inx.form.extend({

    constructor:function(p) {
        p.items = [
            {label:"Пароль",name:"password",password:true},
            {label:"Повтор пароля",name:"password2",password:true},
            {labelAlign:"left",label:"&nbsp;",type:"inx.button",text:"Изменить пароль",onclick:[this.id(),"changePassword"]},
            {label:"Электронная почта",name:"email",value:p.email},
            {labelAlign:"left",label:"&nbsp;",type:"inx.button",text:"Изменить электронную почту",onclick:[this.id(),"changeEmail"]}
        ];
        this.base(p);
    },
    
    cmd_changePassword:function() {    
        var data = this.info("data");
        var p1 = $.trim(data.password);
        var p2 = $.trim(data.password2);
        if(p1!=p2) {
            inx.msg("Пароль и подтверждение не совпадают",1);
            return false;
        }    
        this.call({
            userID:this.userID,
            password:p1,
            cmd:"user_manager:changePassword"
        });        
    },
    
    cmd_changeEmail:function() {
        var data = this.info("data");
        data = {cmd:"user_manager:changeEmail",userID:this.userID,email:data.email};
        this.call(data,[this.id(),"updateList"]);
    },
    
    cmd_verify:function() {
        this.call({cmd:"user_manager:verify",userID:this.userID,flag:1},
        [this.id(),"updateList"]);
    },
    
    cmd_unverify:function() {
        this.call({cmd:"user_manager:verify",userID:this.userID,flag:0},
        [this.id(),"updateList"]);
    },
    
    cmd_updateList:function() { this.bubble("userListReload"); }

});
