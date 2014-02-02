// @link_with_parent
// @include inx.list,inx.pager

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
            cmd:"infuso:cms:reflex:controller:getList"
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
