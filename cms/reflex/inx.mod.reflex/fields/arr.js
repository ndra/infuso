// @include inx.list

inx.ns("inx.mod.reflex.fields").arr = inx.list.extend({

    constructor:function(p) {
    
        p.style = {
            border:1
        }
        
        p.tbar = [
            {icon:"plus",text:"Добавить",onclick:[this.id(),"addItem"]},
            {icon:"delete",text:"Удалить",onclick:[this.id(),"deleteItem"]},
            "|",
            {icon:"up",onclick:[this.id(),"moveSelectedItemUp"]},           
            {icon:"down",onclick:[this.id(),"moveSelectedItemDown"]},
        ];
        
        // Элементы в массиве можно сортировать
        p.sortable = true;
        
        this.on("itemdblclick",[this.id(),"editItem"]);
        p.emptyHTML = "<div style='color:#cccccc;padding:5px;' >нет элементов</div>";
        
        p.cols = [
            {name:"key", title:"Ключ",width:200},
            {name:"val", title:"Значение",width:200}
        ];
        
        this.base(p);
        this.cmd("setValue",p.value);
    },
    
    cmd_editItem:function(id,e) {
        var col = e.col;
        if(col=="key") {
            var key = prompt("Введите имя",this.info("item",id,"key"));
            if(key!==null)
                this.cmd("set",id,{key:key});
        }
        if(col=="val") {
            var val = prompt("Введите значение",this.info("item",id,"val"));
            if(val!==null)
                this.cmd("set",id,{val:val});
        }
    },
    
    cmd_addItem:function() {
        var name = prompt("Введите имя");
        if(!name) return;        
        var val = prompt("Введите значение");
        
        name = name+"";
        val = val+"";
        
        var data = this.info("data");
        
        var set = {key:name,val:val};
        this.data.push(set);
        this.cmd("setData",this.data);
    },
    
    cmd_deleteItem:function() {    
        if(!confirm("Удалить объекты")) return;
        
        var sel = this.info("selection");    
        var data = [];  
        for(var i in this.data) {
            var del = 0;
            for(var j in sel)
                if(sel[j]==this.data[i].id)
                    del = 1;
            if(!del) data.push(this.data[i]);
        }
        this.cmd("setData",data);             
    },
    
    info_value:function() {
        var ret = {};
        for(var i in this.data)
            ret[this.data[i].data.key] = this.data[i].data.val;
        ret = inx.json.encode(ret);
        return ret;
    },
    
    cmd_setValue:function(val) {
        try { val = inx.json.decode(val); }
        catch(e) { val = {}; }
        var ret = [];
        for(var i in val)
            ret.push({key:i,val:val[i]});
            
        this.cmd("setData",ret);
    }
})
