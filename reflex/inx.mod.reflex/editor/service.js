// @link_with_parent

inx.service.register("reflex",new function() {

    var caller = inx({
        type:"inx.observable"
    });
    
    this.registerViewport = function(viewport){
        this.viewport = viewport;  
    }
    
    this.getSelectedItems = function() {
        var list = inx(this.viewport).allItems().eq("type", "inx.mod.reflex.editor.list.items");
        var selectedItems = list.info("selection");
        
        return selectedItems;    
    }
    
    this.action = function(action,e) {
    
        var action = (action).split("/");

        switch(action[0]) {
        
            case "refresh":
                inx.fire("reflex/refresh");
                break;
            
            case "selectAll":
                inx.fire("reflex/selectAll");
                break;
           
           
            case "cmd":

                var cmd = action[1];
                
                if(action[2]) {
                    cmd+= ":"+action[2];
                }

                var p = {
                    cmd:cmd
                }

                for(var i in action) {
                    if(i>2) {
                        if(i%2==1) {
                            key = action[i];
                        } if(i%2==0) {
                            p[key] = action[i];
                        }
                    }
                }

                caller.call(p,function() {
                    inx.service("reflex").action("refresh");
                });

                break;

            case "edit":
                window.location.href = "#"+action[1];
                break;

            case "url":
                window.location.href = action[1];
                break;

            case "editcell":
                inx({
                    type:"inx.mod.reflex.editor.list.editCell",
                    editor:action[1],                    
                    fieldName:action[2],
                    onsave:function() {
                        inx.service("reflex").action("refresh");
                    },
                    x:e.cellX-20,
                    y:e.cellY-45
                }).cmd("render");
                break;

            case "inx":            

                var p = {
                    type:action[1],
                    event:e
                }

                for(var i in action) {
                    if(i>1) {
                        if(i%2==0) {
                            key = action[i];
                        } if(i%2==1) {
                            p[key] = action[i];
                        }
                    }
                }
                
                inx(p).cmd("render");
                break;

            case "msg":
                inx.msg(action[1]);
                break;

            default:
                inx.msg("inx.mod.reflex.editor.list: unrecognized action "+action.join("/"),1);
                break;
        }
    }
});