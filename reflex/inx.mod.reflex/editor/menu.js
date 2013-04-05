// @link_with_parent
// @include inx.tree

inx.mod.reflex.editor.menu = inx.tree.extend({

    constructor:function(p) {
    
        p.style = {
            padding: 20
        }
        
        p.showRoot = false;  
        p.loadOnEachExpand = true; 
        
        if(!p.listeners) {
            p.listeners = {};
        }
        
        p.listeners.beforeload = [this.id(),"beforeLoad"];
        p.listeners.load = [this.id(),"handleLoad"];
        p.listeners.show = [this.id(),"reaction"];
        p.onclick = function(id,e) {
        
            var node = this.info("node",id);
            if(node.noedit) return;
            var index = (id+"").split("/");
            index = index[index.length-1];
            var url = "#"+index;
            if(index) {
                
                if(e.ctrlKey) {
                    window.open(url);
                } else {
                    window.location.href = url;
                }
            }
        }
        
        this.first = true; 
        
        this.tabs = inx({
            type:"inx.mod.reflex.editor.menu.tabs",
            region:"left",
            data:p.tabData,
            onselect:[this.id(),"setTab"]
        });
        
        p.side = [this.tabs]  
        
        this.base(p);                      
    },
    
    cmd_setTab:function(tab) {
    
        var tab = this.tabs.info("item",tab).name;
        this.cmd("setLoader",{
            cmd:"reflex:editor:controller:views",
            tab:tab
        });
        
        this.cmd("load",0);
    },
    
    cmd_planRefresh:function() {
        try{
            clearInterval(this.refreshInterval);
        } catch(ex) {}
        
        this.refreshInterval = setInterval(inx.cmd(this.id(),"reaction"),60*1000);
    },
    
    cmd_beforeLoad:function(data) {
    
        if(data.id!=0) {
            delete data.tab;
        }
    
        data.first = this.first;
        var node = this.info("node",data.id);
        var expanded = [];
        this.cmd("eachVisible",function(id){
            var node = this.info("node",id);
            if(node.expanded)
                expanded.push(node.id);
        },0,data.id);
        data.starred = data.id==0 && this.starred;
        data.expanded = expanded;
    },
    
    cmd_handleLoad:function() {
        this.first = false;
        this.cmd("planRefresh");
    },
    
    cmd_reaction:function() {
        var data = {};
        this.cmd("eachVisible",function(id) {
            var node = this.info("node",id);
            data[id] = node.dataHash;
        });
        this.call({
            cmd:"reflex:editor:controller:checkTreeChanges",
            data:data
        },[this.id(),"handleReaction"]);
    },
    
    cmd_handleReaction:function(p) {
        if(p)
            this.cmd("load",0);
    } 
        
});