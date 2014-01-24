// @include inx.viewport,inx.tabs,inx.tree,inx.direct,inx.button,inx.dialog,inx.form,inx.list,inx.pager
/*-- /mod/bundles/reflex/inx.mod.reflex/editor.js --*/


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

/*-- /mod/bundles/reflex/inx.mod.reflex/editor/actions.js --*/


inx.mod.reflex.editor.actions = inx.button.extend({

    constructor:function(p) {
    
        p.text = "Функции";
        p.icon = "gear";
        p.air = true;        
        p.menu = [];
        p.selection = [];
        
        //------------------------------------------------------------------- Стандартные действия
        
        // Просмотр объекта
        p.menu.push({
            icon:"view",
            text:"Просмотр",
            onclick:[this.id(),"preview"]
        })
        
        // Выделить все
        p.menu.push("|");
        if(p.listData!==undefined)
            p.menu.push({
                text:"Выделить все",
                onclick:[this.id(),"selectAll"]
            })
        
        // Вырезать
        p.menu.push({
            text:"Вырезать",
            icon:"cut",
            onclick:[this.id(),"cut"]
        })
        
        // Операции с коллекциями
        if(p.listData!==undefined) {

            // Вставить        
            p.menu.push({
                text:"Вставить",
                icon:"paste",
                onclick:[this.id(),"paste"]
            })
        
            // Разделитель
            p.menu.push("|");
        
            // Вставить
            if(p.listData!==undefined)
                p.menu.push({
                    text:"Экспорт данных в CSV",
                    onclick:[this.id(),"export"]
                })
            
        }

        if(p.actions.length)
            p.menu.push("|");

        //------------------------------------------------------------------- Пользовательские действия
        
        for(var i in p.actions) {
            var action = p.actions[i];
            if(!action.onclick) action.onclick = inx.cmd(this,"action",action);
            p.menu.push(action);
        }
                                
        this.base(p);
    },
    
    cmd_cut:function() {
        inx.mod.reflex.editor.buffer = this.selection;
        inx.msg("Скопировано объектов: "+this.selection.length);
    },
    
    cmd_paste:function() {
        items = inx.mod.reflex.editor.buffer;
        if(!items) return;
        if(!items.length) return;
        var p = this.owner().owner().info("list");
        p.cmd = "reflex:editor:controller:paste";
        p.items = items;
        this.call(p,[this.id(),"handleAction"]);
        inx.mod.reflex.editor.buffer = null;
    },
    
    cmd_preview:function() {
        for(var i in this.selection)
            window.open("/reflex_editor_controller/view/id/"+this.selection[i]);
    },
    
    cmd_selectAll:function() {
        this.owner().owner().cmd("selectReallyAll");
    },
    
    cmd_sel:function(sel) {
        this.selection = sel;
    },
    
    cmd_setSerializedCollection:function(c) {
        this.serializedCollection = c;
    },
    
    cmd_action:function(p) {
    
        if(p.dlg) {
            p.dlg.ids = this.selection;            
            inx(inx.deepCopy(p.dlg)).cmd("render").setOwner(this);
            return;
        }
    
        inx({
            type:"inx.mod.reflex.editor.actions.dlg",
            action:p.action,
            ids:this.selection
        }).cmd("render").on("complete",[this.id(),"handleAction"]);         
    },
    
    cmd_handleAction:function() {    
        this.bubble("refresh");
        this.bubble("menuChanged");
    },
    
// ----------------------------------------------------------------- Экспорт
    
    cmd_export:function() {
        inx({
            type:"inx.mod.reflex.editor.actions.csv",
            serializedCollection:this.serializedCollection
        }).cmd("render");
    },
    
    cmd_handleExport:function(file) {
        window.location.href = file;
    }

// -----------------------------------------------------------------
    
});

/*-- /mod/bundles/reflex/inx.mod.reflex/editor/actions/csv.js --*/


inx.mod.reflex.editor.actions.csv = inx.dialog.extend({

    constructor:function(p) {
        p.title = "Экспорт в CSV";
        p.width = 400;
        p.autoHeight = true;
        this.base(p);
        this.cmd("step",1);
    },
    
    cmd_step:function(page) {
        this.call({
            cmd:"reflex:editor:export:doExport",
            collection:this.serializedCollection,
            page:page,
            name:this.filename
        },[this.id(),"handleStep"]);
    },
    
    cmd_handleStep:function(data) {    
        this.filename = data.name;    
        var html = (Math.round(data.page / data.pages *10000)/100)+"%";    
        this.cmd("html","<div style='font-size:100px;' >"+html+"</div>");        
        
        if(data.page<=data.pages) {
            this.cmd("step",data.page);
        } else {
            window.location.href = data.csv;
            this.task("destroy");
        }
    },
    
    cmd_complete:function() {
        this.fire("complete");
        this.task("destroy");
    }    
    
});

/*-- /mod/bundles/reflex/inx.mod.reflex/editor/actions/dlg.js --*/


inx.mod.reflex.editor.actions.dlg = inx.dialog.extend({

    constructor:function(p) {
        p.title = "Выполнение задания";
        p.width = 400;
        p.autoHeight = true;
        this.base(p);
        this.index = 0;
        this.cmd("step");
    },
    
    cmd_step:function() {
        var id = this.ids[this.index];
        if(!id) {
            this.task("complete");
            return;
        }
        this.call({cmd:"reflex:editor:controller:doAction",action:this.action,id:id},[this.id(),"step"]);
        this.index++;                
        
        if(this.index==1) var html = "0%";
        else var html = (Math.round(this.index / this.ids.length *10000)/100)+"%";
        this.cmd("html","<div style='font-size:100px;' >"+html+"</div>");
    },
    
    cmd_complete:function() {
        this.fire("complete");
        this.task("destroy");
    }
    
});

/*-- /mod/bundles/reflex/inx.mod.reflex/editor/breadcrumbs.js --*/


inx.css(
    ".lbqku67cehdc1stye2om{padding:0px 0px 0px 10px;margin-right:10px;cursor:pointer;background:url(/reflex/res/up.gif) no-repeat left center;}",
    ".gsxma6d4ubdy9sopvh78{font-size:18px;}"
)

inx.mod.reflex.editor.breadcrumbs = inx.panel.extend({
    
    constructor:function(p) {
        p.style = {
            padding:20,
            background:"#f6f6f6",
            height:"content"
        }
        this.base(p);
    },
    
    cmd_render:function(c) {
        this.base(c);
        this.cmd("setData",this.data);
        this.pathContainer = $("<div>");
        //this.cmd("html",this.pathContainer);
        if(this.data)
            this.cmd("setData",this.data);
    },
    
    cmd_setData:function(data) {
        if(!data) return;    
        var id = this.id();
        if(!data || !data.length) return;
        
        // Если объект еще не отрендерен
        if(!this.pathContainer) {
            this.data = data;
            return;
        }
        
        this.pathContainer.html("");
        
        $("<div>").css({
            position:"absolute",
            right:5,
            top:5,
            cursor:"pointer"
        }).html("&times; закрыть").click(inx.cmd(this.owner(),"stepBack")).appendTo(this.pathContainer);        

        for(var i=0;i<data.length;i++) if(data[i]) {
            var text = (data[i] && data[i].text+"") || "";
            var text = $.trim(text);
            if(!text) text = "&mdash;";
            var e = $("<div>").appendTo(this.pathContainer).html(text);
            
            // Маленькие буквы
            if(data[i+1]) {
                e.addClass("inx-core-inlineBlock");
                e.addClass("lbqku67cehdc1stye2om");
                e.data("index",data[i]["index"]+"")
                .click(function(){
                    inx(id).bubble("editItem",$(this).data("index"));                
                })
                .mouseover(function(){ $(this).css({color:"black",textDecoration:"underline"}) })
                .mouseout(function(){ $(this).css({color:null,textDecoration:"none"}) })
            // Большие буквы
            } else {
                e.addClass("gsxma6d4ubdy9sopvh78");
            }
        }
        this.cmd("html",this.pathContainer);
    }
    
});

/*-- /mod/bundles/reflex/inx.mod.reflex/editor/item.js --*/


inx.mod.reflex.editor.item = inx.panel.extend({

    constructor:function(p) {
    
        // Хлебные крошки
        this.breadcrumbs = inx({
            type:"inx.mod.reflex.editor.breadcrumbs",
            border:0,
            region:"top",
            height:"content"
        });  
        
        p.height = "parent";
             
        p.closable = true;
        p.side = [this.breadcrumbs];
        p.layout = "inx.layout.fit";
        
        this.base(p);
        this.cmd("requestData");
        
        this.on("show",this.id(),"handleShow");
        this.on("refresh",this.id(),"requestData");
        this.on("select",this.id(),"handleSelect");        
        
    },
   
    cmd_handleShow:function() {
        this.items().cmd("handleShow");
    },
    
    cmd_requestData:function() {
        this.call({
            cmd:"reflex:editor:controller:getItem",
            index:this.index
        },[this.id(),"handleData"]);
    },

    cmd_handleData:function(p) {
        if(p.parents)
            this.cmd("setTitle",p.parents[0].text)
        this.breadcrumbs.cmd("setData",p.parents);
        this.parents = p.parents;
        this.items().cmd("destroy");
        this.cmd("add",p.item);
    },
    
    cmd_stepBack:function() {
        this.task("destroy");        
    }

});


/*-- /mod/bundles/reflex/inx.mod.reflex/editor/item/fields.js --*/


inx.mod.reflex.editor.item.fields = inx.panel.extend({
    
    constructor:function(p) {
    
        p.title = "Редактирование"; 
              
        p.style = {
            background:"white",
            vscroll:true,
            padding:20,
            spacing:20
        }        
        
        var tbar = [];        
        for(var i in p.toolbar) {
            switch(p.toolbar[i]) {
            
                case "save":
                    tbar.push({
                        
                        style:{
                            height:32,
                            fontSize:18,
                            color:"white",
                            background:"red"
                        },
                        text:"Сохранить",
                        icon:"save",
                        //air:true,
                        onclick:[this.id(),"save"]
                    });
                    break;
                    
                case "delete":
                    tbar.push({
                        text:"Удалить",
                        icon:"delete",
                        air:true,
                        onclick:[this.id(),"deleteSelf"]
                    });
                    break; 
                    
                case "actions":
                    this.actions = inx({
                        type:"inx.mod.reflex.editor.actions",
                        actions:p.actions
                    }).cmd("sel",[p.index]);                    
                    tbar.push(this.actions); 
                    break;
                    
                default:
                    tbar.push(p.toolbar[i]);
                    break;
            }            
        }
            
        if(tbar.length) {
            p.tbar = tbar;
        }
        
        this.base(p);
        this.cmd("handleData",p.data);
    },
    
    cmd_handleData:function(data) {
        inx.hotkey("ctrl+s",[this.id(),"save"]);
        /*this.cmd("add",{
            type:"inx.button",
            text:"Сохранить",
            style:{
                height:32,
                fontSize:18
            },
            icon:"save",
            onclick:[this.id(),"save"]
        }); */
    },
    
    cmd_close:function() {
        this.owner().owner().cmd("stepBack");
    },
    
    cmd_save:function(close) {
    
        inx.mod.reflex.saveTime = new Date().getTime();
        var data = this.info("data");
        
        this.call({
            cmd:"reflex:editor:controller:save",
            index:this.index,
            data:data
        },[this.id(),"handleSave"]);
        
        return false;
    },
    
    cmd_handleSave:function(data) {
        if(!data) return;
        this.items().cmd("load");
        inx.service("reflex").action("refresh");
    },
    
    cmd_deleteSelf:function() {
    
        if(!confirm("Удалить элемент?")) {
            return;
        }
        
        this.call({
            cmd:"reflex:editor:controller:delete",
            ids:[this.index]
        },[this.id(),"handleDeleteSelf"]);
    },
    
    cmd_handleDeleteSelf:function() {
        inx.service("reflex").action("refresh");
        this.cmd("close");
    }
    
});

/*-- /mod/bundles/reflex/inx.mod.reflex/editor/item/tabs.js --*/


inx.mod.reflex.editor.item.tabs = inx.tabs.extend({
    
    constructor:function(p) {
        p.selectNew = false;
        p.height = "parent";
        this.base(p);
        if(this.items().length()<2) {
            this.cmd("hideTabs");
        }
        this.on("show",this.id(),"handleShow");        
    },
    
    cmd_handleShow:function() {
        this.items().cmd("handleShow");
    }
    
});

/*-- /mod/bundles/reflex/inx.mod.reflex/editor/list.js --*/


inx.mod.reflex.editor.list = inx.tabs.extend({

    constructor:function(p) {

        if(!p.style)
            p.style = {}
        if(p.style.border===undefined)
            p.style.border = 0;
            
        p.keepLayout = true;
        p.showHead = false;

        this.bottom = inx({
            type:"inx.panel",
            region:"bottom",
            style: {
                padding:10,
                background:"#ededed"
            },
            hidden:true
        });

        if(!p.side)
            p.side = [];
        p.side.push(this.bottom);
        
        var tbar = [];
        for(var i in p.toolbar) {
            switch(p.toolbar[i]) {
                default:
                    tbar.push(p.toolbar[i]);
                    break;
                case "add":
                    tbar.push({
                        icon:"plus",
                        help:"Добавить элемент",
                        air:true,
                        onclick:[this.id(),"createItem"]
                    });
                    break;
                    
                case "pager":
                    this.pager = inx({
                        type:"inx.pager",
                        hidden:true,
                        onchange:[this.id(),"load"]
                    });
                    tbar.push(this.pager);
                    break;
                    
                case "search":
                    this.quickSearch = inx({
                        type:"inx.textfield",
                        width:100,
                        onchange:[this.id(),"load"],
                        buttons:[{icon:"delete",onclick:function(){this.owner().cmd("setValue","")}}],
                        help:"Быстрый поиск объекта. Введите сюда то что вы хотите найти."
                    });
                    tbar.push(this.quickSearch)
                    break;
                    
                case "filters":
                    this.filters = inx({
                        type:"inx.select",
                        width:160,
                        value:0,
                        data:[{id:0,text:"Все"}],
                        onchange:[this.id(),"load"]
                    });
                    tbar.push(this.filters);
                    break;
                    
                case "actions":
                    this.actionsComponent = inx({
                        type:"inx.mod.reflex.editor.actions",
                        actions:p.actions,
                        listData:p.listData,
                        toolbar:p.toolbar
                    });
                    tbar.push(this.actionsComponent);
                    break;
                    
                case "up":
                    tbar.push({
                        icon:"up",
                        air:true,
                        onclick:[this.id(),"moveUp"],
                        help:"Переместить выделенный объект выше"
                    });
                    break;
                    
                case "down":
                    tbar.push({
                        icon:"down",
                        air:true,
                        onclick:[this.id(),"moveDown"],
                        help:"Переместить выделенный объект ниже"
                    });
                    break;
                    
                case "filter":
                    tbar.push({
                        icon:"filter",
                        air:true,
                        onclick:[this.id(),"toggleFilter"]
                    });
                    break;
                    
                case "upload":
                    var that = this;
                    var file = inx({
                        type:"inx.file",
                        icon:"upload",
                        help:"Закачать файл",
                        dropArea:this,
                        oncomplete:[this.id(),"load"],
                        loader:{
                            cmd:"reflex:editor:controller:upload",
                            listData:p.listData
                        }
                    });
                    tbar.push(file);
                    break;
                    
                case "edit":
                    tbar.push({
                        icon:"edit",
                        air:true,
                        help:"Редактировать элемент",
                        onclick:[this.id(),"editItem"]
                    });
                    break;
                    
                case "delete":
                    tbar.push({
                        icon:"delete",
                        air:true,
                        text:"Удалить",
                        onclick:[this.id(),"deleteItem"]
                    });
                    break;
                    
                case "view":
                    this.view = inx({
                        type:"inx.mod.reflex.editor.list.view",
                        itemClass:p.itemClass,
                        onchange:[this.id(),"load"],
                        modes:p.viewModes
                    });
                    tbar.push(this.view);
                    break;
                    
                case "refresh":
                    tbar.push({
                        icon:"refresh",
                        air:true,
                        onclick:[this.id(),"load"]
                    })
                    break;
                    
            }
        }
        
        p.tbar = tbar;

        p.emptyHTML = "<div style='padding:5px;color:gray;'>Нет элементов для отображения</div>";
        p.moreFieldName = null;

        this.base(p);
        this.on("data",[this.id(),"handleData"]);
        this.breadcrumbsData = [
            {text:this.className}
        ];
        this.on("show",this.id(),"handleShow");
        this.on("refresh",function() { this.cmd("load"); return false; });
        inx.hotkey("f5",[this.id(),"handleF5"]);
        this.selection = [];
        
        inx.storage.onready(this.id(),"onStorageReady");
        
        inx.on("reflex/refresh",[this.id(),"load"]);
                
        this.task("load");
    },

    cmd_beforeLoad:function(data) {
        var list = this.info("list");
        for(var i in list)
            data[i] = list[i];
    },

    cmd_load:function() {

        if(!this.info("visibleRecursive")) {
            return false;
        }

        var data = {
            cmd:"reflex:editor:controller:getList"
        };

        var list = this.info("list");
        for(var i in list)
            data[i] = list[i];

        inx(this.cmdID).cmd("destroy");
        this.cmdID = this.call(data,[this.id(),"handleData"]);
    },

    info_selection:function() {
        return inx(this).axis("selected").info("selection");
    },

    axis_collectionItems:function() {
        return inx(this.collectionItems);
    },

    /**
     * Обрабатывает данные списка
     * Создает компонент inx.list и передает ему данные
     **/
    cmd_handleData:function(data) {

        var list = this.cmd("add",{
            type:"inx.mod.reflex.editor.list.items",
            layout:data.layout,
            name:data.layout,
            sortable:data.sortable,
            enableTextSelection:this.enableTextSelection,
            onitemdblclick:[this.id(),"handleAction"],
            onitemclick:[this.id(),"handleAction"],
            listeners:{
                selectionchange:[this.id(),"handleSelectionChange"],
                sortcomplete:[this.id(),"handleSortComplete"]
            }
        });

        this.collectionItems = list;

        list.cmd("setData",data.data);
        list.cmd("setCols",data.cols);

        if(data.bbar) {
            this.bottom.cmd("show");
            this.bottom.cmd("html",data.bbar);
        } else {
            this.bottom.cmd("hide");
        }

        if(this.pager)
            this.pager.cmd("setTotal",data.pages).cmd(data.pages>1?"show":"hide");

        if(data.title)
            this.cmd("setTitle",data.title);
            
        this.viewMode = data.viewMode;
        
        if(data.filters)
            inx(this.filters).cmd("setData",data.filters);
            
        if(data.serialized)
            this.cmd("setSerializedCollection",data.serialized);

        this.cmd("planRefresh");

    },

    cmd_handleSortComplete:function() {
        var data = this.info("list");
        data.cmd = "reflex_editor_controller:sortItems";

        // Собираем массив id  элементов в порядке приоритета
        var items = inx(this).axis("collectionItems").info("data");

        var priority = [];
        for(var i in items)
            priority.push(items[i].id);
        data.priority = priority;

        this.call(data,[this.id(),"handleSortStored"]);
    },

    cmd_handleSortStored:function() {
        this.cmd("planRefresh");
        inx.service("reflex").action("refresh");
    },

    /**
     * Метод вызывается при клике (или двойном клике) на строку или ячейку
     * Красота в том, что в событии (параметр e) уже содержится информация о том что нужно сделать
     * Этот метод просто выполняет действие
     * Вся логика вынесена в php, откуда пробрасываются действия
     * Действие - строка. Мы разбираем ее и выполняем что нужно
     **/
    cmd_handleAction:function(id,e) {

        if(!e.action)
            return;
            
        inx.service("reflex").action(e.action,e);

    },

    cmd_planRefresh:function() {

        try{
            clearInterval(this.refreshInterval);
        } catch(ex) {}

        this.refreshInterval = setInterval(inx.cmd(this.id(),"load"),60*1000);
    },

    cmd_onStorageReady:function() {
        if(inx.storage.get("filterPanel"))
            this.cmd("showFilter");
    },

    cmd_keydown:function(e) {
    
        if(e.keyCode==88 && e.ctrlKey)
            inx(this.actionsComponent).cmd("cut");
            
        if(e.keyCode==86 && e.ctrlKey)
            inx(this.actionsComponent).cmd("paste");
            
        return this.base(e);
    },

    cmd_handleF5:function() {
        this.cmd("load");
        return false;
    },

    filter:function() {
        return this.filterItem || inx();
    },

    /**
     * Загружает данные фильтра
     * Как только данные будут получены, вызывает команду handleFilter - добавляющую панель с фильтром
     **/     
    cmd_loadFilter:function() {
        if(this.filterRequested)
            return;
        this.filterRequested = true;
        this.call({
            cmd:"reflex:editor:controller:getFilter",
            listData:this.listData
        },[this.id(),"handleFilter"]);
    },

    /**
     * Обработчик загрузки фильтра
     * Добавляет боковую панель
     **/
    cmd_handleFilter:function(p) {
        if(!p)
            return;
        p.listeners = {change:[this.id(),"load"]};
        this.filterItem = inx(p);
        this.cmd("addSidePanel",this.filterItem);
    },
    
    /**
     * Показывает панель фильтра.
     * Если панель не была загружена - загружает ее
     **/
    cmd_showFilter:function() {
        if(!this.filterItem)
            this.cmd("loadFilter");
        this.filter().cmd("show");
        this.filterActive = true;
        inx.storage.set("filterPanel",true);
    },

    /**
     * Скрывает панель фильтра.
     **/
    cmd_hideFilter:function() {        
        this.filter().cmd("hide");
        this.filterActive = false;
        inx.storage.set("filterPanel",false);
    },

    /**
     * Показывает / скрывает панель фильтра
     **/
    cmd_toggleFilter:function() {                
        this.filterActive ? this.cmd("hideFilter") : this.cmd("showFilter");
    },

    cmd_handleSelectionChange:function() {
        var sel = this.info("selection");
        inx(this.actionsComponent).cmd("sel",sel);
        this.selection = sel;
        this.cmd("planRefresh");
    },

    cmd_setSerializedCollection:function(collection) {
        inx(this.actionsComponent).cmd("setSerializedCollection",collection);
    },

    /**
     * Возвращает данные списка: сериализованной коллекции, страницы, фильтров и т.п.
     * Используется при отправке команл на сервер
     **/
    info_list:function() {

        var data = {};
        data.listData = this.listData;
        
        if(this.pager)
            data.page = this.pager.info("value");
            
        if(this.quickSearch)
            data.quickSearch = inx(this.quickSearch).info("value");
            
        if(this.view)    
            data.viewMode = this.view.info("value");
        
        data.filter = this.filter().info("data");

        if(this.filters)        
            data.filters = inx(this.filters).info("value");

        return data;
    },

    cmd_editItem:function(id) {
        if(typeof(id)=="object") id = this.info("selection")[0];
        if(!id) {
            inx.msg("Ничего не выделено. Для редактирования выделите объект.",1);
            return false;
        }
        this.bubble("editItem",id);
    },

    cmd_createItem:function() {
        var cmd = {cmd:"reflex:editor:controller:create"};
        this.cmd("beforeLoad",cmd);
        this.call(cmd,[this.id(),"handleAdd"]);
    },

    cmd_handleAdd:function(id) {
        if(!id) return;
        this.cmd("editItem",id);
        inx.service("reflex").action("refresh");
    },

    cmd_handleShow:function() {
        this.task("load");
    },

    cmd_deleteItem:function() {
        var sel = this.selection;
        if(!sel.length) return;
        if(!confirm("Удалить выделенные объекты?")) return;
        this.call({
            cmd:"reflex:editor:controller:delete",
            ids:sel
        },[this.id(),"handleChanges"]);
    },

    /**
     * Вызывается при изменениях данных. Запускает перерисовку левого меню
     **/
    cmd_handleChanges:function() {
        this.cmd("load");
        inx.service("reflex").action("refresh");
    },

    cmd_moveDown:function() {
        this.cmd("moveUp",1);
    },

    cmd_moveUp:function(side) {

        if(typeof(side)=="object")
            side = 0;

        var id = this.info("selection")[0];
        if(!id) return;

        var cmd = {
            cmd:"reflex:editor:controller:moveUp",
            itemID:id,
            side:side
        }
        this.cmd("beforeLoad",cmd);

        this.call(cmd,[this.id(),"handleChanges"]);
    },

    cmd_selectReallyAll:function() {
        this.cmd("selectAll");
        var cmd = {cmd:"reflex:editor:controller:getAll"};
        this.cmd("beforeLoad",cmd);
        this.call(cmd,[this.id(),"handleSelectAll"]);
    },

    cmd_handleSelectAll:function(data) {
        inx.msg("Выделено объектов: "+data.ids.length);
        var sel = [];
        for(var i in data.ids)
            sel.push(data["class"]+":"+data.ids[i]);
        this.actions.cmd("sel",sel);
        this.selection = sel;
    },

});


/*-- /mod/bundles/reflex/inx.mod.reflex/editor/list/editCell.js --*/


inx.mod.reflex.editor.list.editCell = inx.dialog.extend({

    constructor:function(p) {
        p.width = 350;
        p.style = {
            padding:10,
            border:0,
            background:"#ededed"
        }
        p.autoDestroy = true;
        p.modal = false;
        p.title = "Редактирование ячейки";
        p.bbar = [
            {text:"Сохранить",icon:"save",onclick:[this.id(),"save"]},"|",
            {text:"Отмена",onclick:[this.id(),"destroy"]},
        ];
        this.base(p);
        
        this.call({
            cmd:"reflex:editor:controller:getField",
            editor:this.editor,
            name:this.fieldName
        },[this.id(),"handleField"]);   
         
        this.on("save",p.onsave);   
        this.on("submit","save");
        inx.hotkey("esc",[this.id(),"destroy"]);
    },
    
    cmd_handleField:function(data) {  
    
        if(!data || !data.editor) {
            this.task("destroy");
            return;
        }
         
        if(data.editor) {        
            data.editor.width = "parent";
            var field = inx(data.editor);        
            this.cmd("add",field);
            field.task("focus").task("select");
            this.fi = field;
        } 
    },
    
    cmd_save:function() {
        this.call({
            cmd:"reflex:editor:controller:saveField",
            editor:this.editor,
            name:this.fieldName,
            value:this.fi.info("value")
        },[this.id(),"handleSave"]);
    },
    
    cmd_handleSave:function() {
        this.fire("save");
        this.task("destroy");
    }
    
});

/*-- /mod/bundles/reflex/inx.mod.reflex/editor/list/filter.js --*/


inx.mod.reflex.editor.list.filter = inx.panel.extend({

    constructor:function(p) {
        p.width = 440;
        if(!p.style)
            p.style = {}
        p.style.padding = 20;
        p.style.vscroll = true;
        p.resizable = true;
        p.name = "filters";
        this.base(p);
        this.task("check");
    },    
   
    cmd_check:function() {
    
        var fn = inx.cmd(this,"check");
        setTimeout(fn,300);
        
        if(this.info("hidden"))
            return;
        
        var data = this.info("data");
        var hash = inx.json.encode(data);
        if(hash!=this.lastHash) {
            this.task("change");
            this.lastHash = hash;
        }
    },

    cmd_change:function() {
        this.fire("change");
    }
    
});

/*-- /mod/bundles/reflex/inx.mod.reflex/editor/list/items.js --*/


inx.mod.reflex.editor.list.items = inx.list.extend({

    constructor:function(p) {
        if(!p.style)
            p.style = {}
        p.style.spacing = 4;
        p.style.valign = "top";
        
        this.base(p);
        
        inx.on("reflex/selectAll", [this.id(),"reflexSelectAll"]);
    },
    
    cmd_reflexSelectAll:function() {
        if(!this.info("visibleRecursive")) {
            return false;
        }

        this.cmd("selectAll");
    },
    
});

/*-- /mod/bundles/reflex/inx.mod.reflex/editor/list/view.js --*/


inx.mod.reflex.editor.list.view = inx.button.extend({

    constructor:function(p) {
        p.air = true;
        p.icon = "list";
        p.menu = [];
        for(var i in p.modes)
            p.menu.push({
                text:p.modes[i].title,
                icon:p.modes[i].icon,
                onclick:inx.cmd(this.id(),"changeView",p.modes[i].id)
            });
        
        if(p.onchange)
            this.on("change",p.onchange);
        this.base(p);
        inx.storage.onready(this.id(),"handleStorage");        
    },
    
    cmd_changeView:function(view) {    
        if(this.view==view) return;    
            
        inx.storage.set("n4lrjw9zdcu2"+this.itemClass,view);
        this.view = view;
        this.task("fireChange");
        this.cmd("setIcon",this.modes[view] ? this.modes[view].icon : "list");
    },
    
    cmd_fireChange:function() {
        this.fire("change");
    },
    
    cmd_handleStorage:function() {
        var view = inx.storage.get("n4lrjw9zdcu2"+this.itemClass);
        if(view) this.cmd("changeView",view);
    },
    
    info_value:function() {
        return this.view;
    }
    
});

/*-- /mod/bundles/reflex/inx.mod.reflex/editor/menu.js --*/


inx.mod.reflex.editor.menu = inx.tree.extend({

    constructor:function(p) {
    
        p.style = {
            padding: 20,
            vscroll:true
        }
        
        p.showRoot = false;  
        p.loadOnEachExpand = true; 
        
        if(!p.listeners) {
            p.listeners = {};
        }
        
        p.listeners.beforeload = [this.id(),"beforeLoad"];
        p.listeners.load = [this.id(),"handleLoad"];
        p.listeners.show = [this.id(),"refresh"];
        p.onclick = function(id,e) {
        
            var node = this.info("node",id);
            if(node.noedit) return;
            var index = (id+"").split("/").pop();
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
        
        inx.on("reflex/refresh",[this.id(),"refresh"]);        
                      
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
        
        this.refreshInterval = setInterval(inx.cmd(this.id(),"refresh"),60*1000);
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
            if(node.expanded) {
                expanded.push(node.id);
            }
        },0,data.id);
        data.starred = data.id==0 && this.starred;
        data.expanded = expanded;

        
    },
    
    cmd_handleLoad:function() {
        this.first = false;
        this.cmd("planRefresh");
    },
    
    cmd_refresh:function() {
    
        var data = {};
        this.cmd("eachVisible",function(id) {
            var node = this.info("node",id);
            data[id] = node.dataHash;
        });
        this.call({
            cmd:"reflex:editor:controller:checkTreeChanges",
            data:data
        },[this.id(),"handleRefreshData"]);
    },
    
    cmd_handleRefreshData:function(p) {
        if(p)
            this.cmd("load",0);
    } 
        
});

/*-- /mod/bundles/reflex/inx.mod.reflex/editor/menu/tabs.js --*/


inx.mod.reflex.editor.menu.tabs = inx.list.extend({

    constructor:function(p) {
        p.width = 110;
        this.base(p);   
        this.on("data","selectFirst");
    },
    
    renderer:function(e,data) {
        e = $("<center>").appendTo(e);
        $("<img>").attr("src",data.icon).appendTo(e);
        $("<div>").html(data.text+"").css({fontSize:10}).appendTo(e);
    },
    
    cmd_selectFirst:function() {        
        this.task("selectFirst2",100);
    },
    
    cmd_selectFirst2:function() {        
        this.cmd("setPosition",0);
    }
        
});

/*-- /mod/bundles/reflex/inx.mod.reflex/editor/service.js --*/


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

