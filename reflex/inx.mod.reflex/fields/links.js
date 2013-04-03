// @include inx.list

inx.ns("inx.mod.reflex.fields").links = inx.panel.extend({

    constructor:function(p) {
    
       // p.height = 200;
       // p.autoHeight = false;
        p.layout = "inx.layout.fit";
        
        p.style = {
            height:"content"
        }
        
        /*this.addObject = inx({
            type:"inx.mod.reflex.fields.links.addObject",
            width:300,
            resizable:true,
            region:"right",
            index:p.index,
            name:p.name,
            listeners:{
                addObject:[this.id(),"addObject"]
            }
        }); */
        
        this.objList = inx({
            type:"inx.mod.reflex.fields.links.objList",
            index:p.index,
            name:p.name,
            listeners:{
                deleteObject:[this.id(),"deleteObject"],
                moveUp:[this.id(),"moveObjectUp"],
                moveDown:[this.id(),"moveObjectDown"]
            }
        });
        
        p.items = [this.objList];
        //p.side = [this.addObject];
        
        this.data = [];
        
        if(p.value) {
            var values = p.value.split(" ");
        } else {
            var values = [];
        }
        
        for(var i in values) {
            this.data.push(values[i]*1);
        }        
        
        if(!this.data)
            this.data = [];
            
        this.base(p);
        this.task("updateAddedObjects");  
        
        this.objList.on("showAddObjectDlg",[this.id(),"showAddObjectDlg"]);
        
       //inx.msg(this.private_autoHeight);    
    },
    
    cmd_showAddObjectDlg:function() {
        inx({
            type:"inx.mod.reflex.fields.links.addObject",
            index:this.index,
            name:this.name,
            listeners:{
                addObject:[this.id(),"addObject"]
            }
        }).cmd("render");
    },
    
    cmd_moveObjectUp:function(id) {
        var pos = this.data.indexOf(id);
        if(pos==0) return;
        this.data.splice(pos,1);
        this.data.splice(pos-1,0,id)
        this.task("updateAddedObjects");
    },
    
    cmd_moveObjectDown:function(id) {
        var pos = this.data.indexOf(id);
        if(pos==this.data.length-1) return;
        this.data.splice(pos,1);
        this.data.splice(pos+1,0,id)
        this.task("updateAddedObjects");
    },
    
    cmd_addObject:function(id) {
        var data = [];
        var flag = true;
        for(var i in this.data) {
            if(id!=this.data[i] | flag)
                data.push(this.data[i]);
            if(id==this.data[i])
                flag = false;
        }
        if(flag) data.push(id);
        this.data = data;
        this.task("updateAddedObjects");
    },
    
    cmd_updateAddedObjects:function() {
        this.objList.cmd("setList",this.data);
    },
    
    info_value:function() {
        return this.data.join(" ");
    },
    
    info_data:function() { return null; },
    
    cmd_deleteObject:function(id) {
        var data = [];
        var flag = true;
        for(var i in this.data)
            if(id!=this.data[i])
                data.push(this.data[i]);
        this.data = data;
        this.task("updateAddedObjects");
    }
})
