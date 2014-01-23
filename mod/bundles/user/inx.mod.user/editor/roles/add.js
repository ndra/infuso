// @include inx.dialog
// @link_with_parent

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