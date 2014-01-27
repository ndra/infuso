// @include inx.viewport,inx.tabs,inx.tree,inx.direct

inx.ns("inx.mod.reflex").editor = inx.viewport.extend({

    constructor:function(p) {
    
        p.style.border = 0;
    
        this.tabs = inx({
            type:"inx.tabs",
            showHead:false,
            onselect:[this.id(),"handleSelectItem"],
            listeners:{
                add:[this.id(),"cleanTabs"],
                select:[this.id(),"cleanTabs"]
            },
            style:{
                border:0,
                height:"parent"
            }
        });        
                
        p.items = [this.tabs];
        
        if(p.menu!=="hide") {
            p.side = [{
                type:"inx.mod.reflex.editor.menu",
                region:"left",
                resizable:true,
                width:400,
                name:"menu",
                tabData:p.tabData
            }]
        }

        this.base(p);
        this.on("editItem",[this.id(),"editItem"]);
        inx.direct.bind(this.id(),"onDirect");
        
        inx.service("reflex").registerViewport(this);
    },     
    
    cmd_handleSelectItem:function(id) {
        var name = inx(id).info("name");
        inx.direct.set(name);
    },
    
    cmd_onDirect:function(p) {
    
        index = p.segments[0];
    
        if(!index) {
            return;
        }
        
        var tab = this.tabs.cmd("add",{
            type:"inx.mod.reflex.editor.item",
            index:index,
            title:name,
            name:index
        });
        
        // Отправляем вкладке сообщение о том что пользователь выбрал ее пункт меню
        // Если внутри редактора происходили какие-то переключения состояния,
        // то реагируя на это сообщения можно вернуть редактор в исходное состояние
        tab.fire("userSelect");
        
    },
      
    cmd_editItem:function(index) {
        inx.direct.set(index);
    },
    
    cmd_cleanTabs:function(parents) {

        // Убираем вкладки слева, если их больше 10
        var k = this.tabs.items().length();
        this.tabs.items().each(function(n) {
            if(n<=k-10)
                this.cmd("destroy")
        });
        
        // Убираем вкладки справа от активной
        var sel = this.tabs.axis("selected").id();
        var del = false;
        this.tabs.items().each(function(n) {        
            if(del)
                this.task("destroy");        
            if(this.id()==sel)
                del = true;
        });
        
    }


});