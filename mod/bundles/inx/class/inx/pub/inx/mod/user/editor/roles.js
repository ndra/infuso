// @include inx.list,inx.dialog
/*-- /user/inx.mod.user/editor/roles.js --*/


inx.ns("inx.mod.user.editor").roles = inx.list.extend({

    constructor:function(p) {
    
        p.bbar = [{
            icon:"plus",
            text:"Добавить роль",
            onclick:[this.id(),"addRole"]
        },"|",{
            icon:"delete",
            text:"Удалить роль",
            onclick:[this.id(),"removeRole"]
        }];
        
        p.loader = {
            cmd:"user_manager:getRoles",
            userID:p.userID
        };
        
        this.base(p);         
    },
    
    cmd_removeRole:function() {    
        var sel = this.info("selection");
        if(!sel.length)
            return;
            
        if(!confirm("Удалить выбранные роли?"))
            return;
            
        this.call({
            cmd:"user_manager:deleteRoles",
            userID:this.userID,
            roles:sel
        },[this.id(),"load"])
            
    },
    
    cmd_addRole:function() {
        
        inx({
            type:"inx.mod.user.editor.roles.add",
            listeners:{
                selectRole:[this.id(),"handleSelectRole"]
            }
        }).cmd("render");
        
    },
    
    cmd_handleSelectRole:function(code) {
        this.call({
            cmd:"user_manager:addRole",
            userID:this.userID,
            role:code
        },[this.id(),"load"])
    }

})

/*-- /user/inx.mod.user/editor/roles/add.js --*/


inx.mod.user.editor.roles.add = inx.dialog.extend({

    constructor:function(p) {

        p.title = "Какую роль добавить?";
        p.width = 320;
        
        p.items = [{
            type:"inx.list",
            loader:{
                cmd:"user_manager:enumRoles"
            },
            onitemclick:function(code) {
                this.owner().fire("selectRole",code);
                this.owner().task("destroy");
            },
            style:{
                border:0,
                maxHeight:300
            }
        }] 
        
        this.base(p);         
    }
    
})

