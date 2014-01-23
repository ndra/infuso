// @include inx.tabs

inx.ns("inx.mod.lang.fields").textarea = inx.tabs.extend({

    constructor:function(p) {        
        p.autoHeight = true;
        p.selectNew = false;
        this.base(p);
        this.cmd("setValue",this.value);
        this.task("sendRequest");
    },
    
    cmd_setValue:function(v) {
        v = inx.json.decode(v);
        if(!v)
            v = {};
        if(typeof(v)!="object")
            v = {};
        this.value = v;
    },
    
    cmd_sendRequest:function() {
        if(!inx.ztcezjklsq) {
            this.call({cmd:"lang:controller:getAll"},[this.id(),"handleLangsForAll"]);
            inx.ztcezjklsq = true;
        }
        if(!inx.sdkl0of5bok5) inx.sdkl0of5bok5 = [];
            inx.sdkl0of5bok5.push(this.id())
    },
    
    cmd_handleLangsForAll:function(data) {
        for(var i in inx.sdkl0of5bok5)
            inx(inx.sdkl0of5bok5[i]).cmd("handleLangs",data);
        delete inx.sdkl0of5bok5;
        delete inx.ztcezjklsq;
    },
    
    cmd_handleLangs:function(data) {    
        for(var i in data)
            this.cmd("add",{
                title:data[i],
                type:this.editor,
                style:{
                    autoWidth:true
                },
                storage:this.storage,
                value:this.value[data[i]],
                name:data[i]
            }); 
        if(!this.items().length())
            this.cmd("add",{title:"Ошибка",html:"Не добавлено ни одного языка",autoHeight:true,padding:4,background:"#ededed"});
    },
    
    info_value:function() {
        var ret = {};
        for(var i in this.value)
            ret[i] = this.value[i];
            
        this.items().each(function() {
            var c = inx(this);
            ret[c.info("name")] = c.info("value");
        });
        
        return inx.json.encode(ret);
    },
    
    info_data:function() {
        return null;
    }
    
});