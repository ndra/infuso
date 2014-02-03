// @link_with_parent

inx.mod.board.main.dayActivity = inx.panel.extend({

    constructor:function(p) {
    
        p.style = {
            background:"#ededed"
        }
        
        this.base(p);
        this.on("click","toggle");
        
        this.cmd("add",{
            type:this.info("type")+"."+"user",
            name:"me",
            showHours:true,
            style:{
                border:0
            }
        });
        
    },
    
    cmd_toggle:function() {
        if (this.expanded) {
            this.cmd("collapse");
        } else {
            this.cmd("expand");
        }
    },
    
    /**
     * Разворачивает панель
     **/
    cmd_expand:function() {
        this.expanded = true;
        this.cmd("loadUsers");
        this.items().neq("name","me").cmd("show");
    },
    
    /**
     * Сворачивает панель
     **/
    cmd_collapse:function() {
        this.expanded = false;
        this.items().neq("name","me").cmd("hide");
    },
    
    cmd_loadUsers:function() {
        if(this.usersLoaded) {
            return;
        }
        this.usersLoaded = true;
        this.call({
            cmd:"infuso/board/controller/report/getUsers"
        },[this.id(),"handleUsers"]);
    },
    
    cmd_handleUsers:function(data) {
        for(var i in data) {
            this.cmd("add",{
                type:this.info("type")+"."+"user",
                userID:data[i].userID,
                style:{
                    border:0
                }
            });
        }
    }
         
});