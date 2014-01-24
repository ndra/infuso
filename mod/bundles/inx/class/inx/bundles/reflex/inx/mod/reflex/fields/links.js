// @include inx.list,inx.pager,inx.dialog
/*-- /mod/bundles/reflex/inx.mod.reflex/fields/links.js --*/


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


/*-- /mod/bundles/reflex/inx.mod.reflex/fields/links/addObject.js --*/


inx.mod.reflex.fields.links.addObject = inx.dialog.extend({

    constructor:function(p) {
    
        p.style = {
            width:320,            
            border:0
        };

        p.title = "Добавление элемента";

        this.list = inx({
            type:"inx.list",
            loader: {
                cmd:"reflex:editor:fieldController:getListItems",
                index:p.index,
                name:p.name
            },
            listeners: {
                itemdblclick:[this.id(),"addObject"]
            }, style: {
                vscroll:true,
                maxHeight:300
            }
        }) 
        
        p.items = [this.list];
        
        this.pager = inx({
            type:"inx.pager",
            onchange:[this.list.id(),"load"]
        });
        
        this.search = inx({
            type:"inx.textfield",
            width:100,
            onchange:[this.list.id(),"load"]
        });
        
        p.tbar = [
            {text:"Добавить",icon:"plus",onclick:[this.id(),"addObject"]},
            "|",
            this.search,
            {icon:"refresh",onclick:[this.list.id(),"load"]},
            this.pager
        ];
        
        this.list.on("load",[this.id(),"handleLoad"]);
        this.list.on("beforeload",[this.id(),"beforeLoad"]);
        
        p.destroyOnEscape = true;
        
        this.base(p);
    },
    
    cmd_handleLoad:function(data) {
        this.pager.cmd("setTotal",data.pages);
    },
    
    cmd_beforeLoad:function(data) {
        data.page = this.pager.info("value");
        data.search = this.search.info("value");
    },
   
    cmd_addObject:function() {
        var id = this.list.info("selection")[0];
        if(!id) return;
        this.fire("addObject",id);
        this.task("destroy");
    }
    
});

/*-- /mod/bundles/reflex/inx.mod.reflex/fields/links/objList.js --*/


inx.mod.reflex.fields.links.objList = inx.list.extend({
    constructor:function(p) {
        p.tbar = [
            {icon:"plus",text:"Добавить",onclick:[this.id(),"addObject"]},            
            {icon:"up",onclick:[this.id(),"moveUp"]},
            {icon:"down",onclick:[this.id(),"moveDown"]},
            {icon:"refresh",onclick:[this.id(),"load"]},
            "|",
            {text:"Удалить",icon:"delete",onclick:[this.id(),"deleteObject"]}

        ];
        this.base(p);
    },
    
    cmd_addObject:function() {
        this.fire("showAddObjectDlg");
    },
    
    cmd_moveUp:function() {
        var id = this.info("selection")[0];
        if(!id) return;
        this.fire("moveUp",id);
    },
    
    cmd_moveDown:function() {
        var id = this.info("selection")[0];
        if(!id) return;
        this.fire("moveDown",id);
    },
    
    cmd_deleteObject:function() {
        var id = this.info("selection")[0];
        if(!id) return;
        this.fire("deleteObject",id);
    },
    
    cmd_setList:function(ids) {
        this.cmd("setLoader",{
            ids:ids,
            cmd:"reflex:editor:fieldController:getListItems",
            index:this.index,
            name:this.name
        });
        this.cmd("load");
    }
})


