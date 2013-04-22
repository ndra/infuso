// @include inx.panel

inx.css(
    ".inx-list-item{vertical-align:top;overflow:hidden;padding:4px 10px 4px 10px;cursor:pointer;}",
    ".inx-list-item-selected{background:#eaeaea;}",
    ".inx-focused .inx-list-item-selected{background:#d9e8fb}"
);

inx.list = inx.panel.extend({

    constructor:function(p) {    
    
        this.private_selection = [];
        
        // Быстрое назначение триггеров
        if(p.onselect)
            this.on("select",p.onselect);
        if(p.onitemclick)
            this.on("itemclick",p.onitemclick);
        if(p.onitemdblclick)
            this.on("itemdblclick",p.onitemdblclick);
        if(p.onload)
            this.on("load",p.onload);
        
        if(p.moreFieldName===undefined)
            p.moreFieldName = "text";
        if(!p.emptyHTML)
            p.emptyHTML = "...";    
        
        // Шапка таблицы
        this.head = inx({
            type:"inx.list.head",
            region:"top",
            hidden:true
        });
        
        // Стили о умолчанию
        if(!p.style)
            p.style = {}            
        p.style.vscroll = true;
        p.style.hscroll = true;
        if(p.style.padding === undefined) {
            p.style.padding = 10;
        }
        
        if(!p.side)
            p.side = [];
            
        p.side.push(this.head);
        
        if(p.data && !p.data.data)
            p.data.data = inx.deepCopy(p.data);            
        
        if(!p.data)
            this.data = [];
            
        this.base(p);
    },
    
    /**
     * Возвращает компонент строки по id
     **/
    info_itemComponent:function(id) {
        var cmp = 0;
        this.items().each(function() {
            if(this.data("itemID")==id)
                cmp = this;
        });
        return cmp || inx(0);
    },
    
    /**
     * Перерисовывает список
     **/
    cmd_updateAll:function() {
    
        var that = this;
        
        // Убираем старые элементы
        this.items().each(function() {
            that.cmd("remove",this.id());
        })

        var listID = this.id();        
        var colHash = inx(this).axis("head").info("hash");
        
        for(var i in this.data) {
        
            // Присваеваем элементу id, если он не был присвоен
            if(this.data[i].id === undefined)
                this.data[i].id = inx.id();
                
            var itemID = this.data[i].id;
            
            // Сюда будут складываться элементы
            if(!this.bufferz)
                this.bufferz = {};
                
            // Хэщ элемента
            // Система попытается взять элемент из буфера по хэшу.
            // Если не получится - создаст новый
            var itemHash = this.data[i].id +":"+ inx.crc32(this.data[i]) + ":" + colHash;
                
            if(!this.bufferz[itemHash]) {
            
                if(!this.data[i].data)
                    this.data[i].data = this.data[i];
            
                // Создаем элемент
                var item = this.info("itemConstructor",this.data[i]);
                item.data = this.data[i];
                item.head = this.head;
                item.list = this;                
                
                // Для элемента можно передать ширину
                if(this.data[i].width)
                    item.width = this.data[i].width;
                    
                if(this.sortable) {
                    if(!item.listeners)
                        item.listeners = {};
                    item.listeners.render = function() {
                        inx.dd.enable(this.el,that,"handleDragItem",{
                            itemID:this.id(),
                            helper:true
                        });
                    }
                }
                
                item = inx(item);
                
                item.on("mousedown",function(e) {
                    inx(listID).cmd("handleItemMousedown",inx(this).data("itemID"),e);
                })
                
                item.on("click",function(e) {
                    inx(listID).cmd("handleItemClick",inx(this).data("itemID"),e);
                })
                
                item.on("dblclick",function(e) {
                    inx(listID).cmd("handleItemDblclick",inx(this).data("itemID"),e);
                })
                
                item.data("itemID",itemID);                
                
                this.bufferz[itemHash] = item;

            }            
            
            this.cmd("add",this.bufferz[itemHash]);
            
        }
        
        this.cmd("removeOldSelection");
        this.task("updateSelection")
        this.task("updateHeadVisibility");
    
    },
    
    cmd_handleDragItem:function(params) {
       
        if(params.phase=="start") {
            this.dragItemID =  inx(params.itemID).data("itemID");
        }
    
        var position = 0;
        var minD = 99999999999;
        
        this.items().each(function(i) {
        
            var el = this.info("param","el");
            
            var x = el.offset().left  + params.elementOffset.x;
            var y = el.offset().top  + params.elementOffset.y;            
            
            var d = (y-params.event.pageY)*(y-params.event.pageY) + (x-params.event.pageX)*(x-params.event.pageX);
            d = Math.sqrt(d);
            if(d<minD) {
                minD = d;
                position = i;
            }           
            
        });
        
        this.cmd("moveItem",this.dragItemID,position);
        
        if(params.phase=="stop") {
            this.fire("sortcomplete")
        }
        
    },
    
    /**
     * @return ширина контента для скроллинга
     **/
    info_contentWidth:function() {
        if(this.info("gridMode"))
            return this.items().info("rowWidth");
        else
            return this.base();
    },

    /**
     * Дефолтный рендер данных
     **/    
    renderer:function(e,data) { 
    
        if(!data)
            data = {};
    
        if(data.separator) {
            html = "<hr>";
        } else {
            var html = data.text;
            if(data.icon)
                html = "<img src='"+inx.img(data.icon)+"' align='absmiddle' style='margin-right:8px;' />" + data.text;
        }
        
        e.html(html);
   
    },
    
    /**
     * Осб доступа к хэдеру таблицы
     **/ 
    axis_head:function() {
        return inx(this.head);
    },
    
    /**
     * Сеттер данных о колонках
     * cols = false - выключает колонки
     * cols = array() - включает колонки
     * cols = undefined() не меняет данные колонок
     **/
    cmd_setCols:function(cols) {
   
        if(cols) {
            this.head.cmd("setColData",cols);
            this.private_gridMode = true;
            this.__body && this.__body.css({whiteSpace:"nowrap"});
        }       
        
        if(cols===false) {
            this.private_gridMode = false;            
            this.__body && this.__body.css({whiteSpace:"normal"});
        }
        
        this.task("updateHeadVisibility");
    },
    
    /**
     * Обновляет видимость хэдера таблицы
     **/
    cmd_updateHeadVisibility:function() {
        var f = false;
        if(this.private_gridMode)
            f = true;
        if(this.data && !this.data.length)
            f = false;
        inx(this).axis("head").cmd(f ? "show" : "hide");
    },
    
    /**
     * Рендер компонента
     **/
    cmd_render:function() {
        this.base();
        if(this.loader)
            this.cmd("load");
            
        this.cmd("setCols",this.cols);
        this.cmd("setData",this.data);        
        
        if(!this.enableTextSelection)
            this.__body.addClass("inx-unselectable");            
        this.on("scroll",[this.id(),"updateTabsScroll"]);
    },
    
    /**
     * При синхронизации лайаута, обновляем скролл хэда
     **/
    cmd_syncLayout:function() {
        this.base();
        this.cmd("updateTabsScroll");
    },
    
    /**
     * Вызывается при прокрутке __body
     * Подстраивает под нее позицию шапки
     **/
    cmd_updateTabsScroll:function() {
        var scroll = this.info("scrollLeft") - this.style("padding");
        scroll -= this.items().info("colOffset");
        this.head.cmd("setScroll",scroll);    
    },
    
    /**
     * Меняет лоадер
     **/
    cmd_setLoader:function(loader) {
        this.loader = loader;
    },    
   
    // Ставит загрузку в очередь
    cmd_load:function() {
        this.task("private_load");
    },
    
    // Выполняет загрузку
    cmd_private_load:function() {
        if(!this.loader)
            {inx.msg("error: loader in not defined",1); return;}
        var ret = this.fire("beforeload",this.loader);
        if(ret===false)
            return;
        
        // Уничтожаем предыдущий вызов
        inx(this.privateLoadCommand).cmd("destroy");
        // Делаем новый вызов
        this.privateLoadCommand = this.call(this.loader,[this.id(),"handleLoadNative"] );
    },
    
    cmd_handleLoadNative:function(data) {    
    
        if(!data.data)
            data.data = inx.deepCopy(data);
    
        this.cmd("setData",data.data);
        this.cmd("setCols",data.cols);
        this.fire("load",data);
    },

    /**
     * Очищает выделение
     **/
    cmd_clearSelection:function() {
        this.private_selection = {};
        this.cmd("updateSelection")
    },
    
    // !mode - выбирает только этот элемент
    // mode == "toggle" - инвертирует выделение элемента
    cmd_select:function(id,mode) {
    
        if(!mode)
            this.cmd("clearSelection");    
        
        var sel;
        switch(mode) {
            case "add":
            default:
                sel = true;
                break;
            case "toggle":
                sel = !this.private_selection[id];
                break;
        }
        
        if(sel) {
            this.private_selection[id] = true;    
            this.fire("select",id);
        } else {
            delete this.private_selection[id];
        }
        
        this.fire("selectionchange",[id]);        
        this.cmd("updateSelection");
        this.cmd("scrollToItem",id);
    },
    
    cmd_updateSelection:function() {
        var list = this;
        this.items().each(function() {
            if(list.private_selection[this.data("itemID")])
                this.style("background","#d9e8fb")
            else
                this.style("background","none")
        });

    },
    
    /**
     *  Перемещает скролл на элемент с заданным id
     **/
    cmd_scrollToItem:function(id) {
        this.cmd("scrollTo",this.info("itemComponent",id));
    },
    
    /**
     * Выделяет все элементы
     **/
    cmd_selectAll:function() {
        for(var i in this.data)
            this.cmd("select",this.data[i].id,"add")
    },
    
    /**
     * Очищает массив selection, удаляя элементы которых нет в текущщем массиве данных.
     * Вызывается после загрузки данных
     **/
    cmd_removeOldSelection:function() {
        for(var i in this.private_selection)
            if(!this.info("item",i))
                delete this.private_selection[i];
    },
    
    info_selection:function() {
        var ret = [];
        for(var id in this.private_selection)
            ret.push(id);
        return ret;
    },

   /**
     * Передвигает выделение на одну строку вниз
     * mode пробрасывается в метод select
     **/    
    cmd_selectUp:function(mode) {
        var pos = this.info("position")-1;
        this.cmd("setPosition",pos,mode);
    },
    
    /**
     * Передвигает выделение на одну строку вниз
     * mode пробрасывается в метод select
     **/
    cmd_selectDown:function(mode) {
        var pos = this.info("position",true)+1;
        this.cmd("setPosition",pos,mode);
    },
    
    /**
     * Выделяет элемент по порядковому номеру
     **/
    cmd_setPosition:function(pos,mode) {
        if(pos<0)
            pos=0;
            
        var item = this.data[pos];
        
        if(!item)
            return;
        this.cmd("select",item.id,mode); 
    },
    
    /**
     * Возвращает позицию выделенного элемента
     * Нумерация с нуля
     * Если аргумент == true, возвращает позицию последнего выделенного элемента
     **/
    info_position:function(last) {
        var ret = -1;
        for(var i in this.data) {
            if(this.private_selection[this.data[i].id]) {
                ret = i*1;
                if(!last)
                    break;
            }
        }
        return ret;
    },
    
    /**
     * Возвращает массив данных элемента с заданным id
     * Если передавн второй параметр - возвращает элемент массива
     **/
    info_item:function(id,key) {
        var ret;
        for(var i in this.data)
            if(this.data[i].id==id)
                ret = this.data[i];
        if(ret && key)
            ret = ret[key];
        return ret;
    },
    
    /**
     * Используется в inx.select
     **/
    cmd_renderNodeTo:function(id,e) {
        this.renderer(e,this.info("item",id).data);
    },
    
    /**
     * Обновляет данные элемента
     **/
    cmd_set:function(id,set) {
        for(var i=0;i<this.data.length;i++)
            if(this.data[i].id==id) {
                for(var key in set)
                    this.data[i][key] = set[key];
                /**
                 this.private_updateItem(id);
                 Сделать
                 **/
            }    
    },
    
    /**
     * Возвращает массив с исходными данными списка
     **/
    info_data:function() {
        return this.data;
    },
    
    /**
     * Возвращает данные столбца
     **/
    info_col:function(col,key) {
        var col =  this.head.info("col",col);
        if(!col)
            return null;
        return col[key];
    },
    
    /**
     * Возвращает тип конструктора строки
     **/
    info_itemType:function() {
        return this.info("gridMode") ? "inx.list.gridItem" : "inx.list.item";
    },
    
    /**
     * Возвращает конструктор строки
     **/
    info_itemConstructor:function(data) {    
        
        
        if(data.inx) { 
        
            if(data.inx instanceof String) {
                return {
                    type:data.inx
                };
            }
                
            if(data.inx instanceof Object) {
                return inx.deepCopy(data.inx);
            }
                
            return {
                type:"inx.panel",
                html:"inx.list:info_itemConstructor error"
            }
            
        }    
            
        return {
            type: this.info("itemType")
        };       
    },
    
    /**
     * Устанавливает данные списка.
     * Пример данных:
     * [
     *  {
     *     id:1
     *     data:{text:123,b:456}    
     *   },
     *   {
     *     id:2
     *     data:{text:123,b:456}    
     *   }
     *  ]
     **/
    cmd_setData:function(data) {
    
        this.data = [];
        for(var i=0;i<data.length;i++) {
            row = data[i];
            
            if(!row.data)
                row.data = inx.deepCopy(row);
                
            this.data.push(row);
        }

        this.fire("data",this.data);        
        this.fire("afterdata",this.data);
        this.task("updateAll");
    },
    
    cmd_handleItemMousedown:function(itemID,e) {
        this.cmd("select",itemID, e.ctrlKey ? "toggle" : null);
        this.fire("itemmouswdown",itemID,e);
    },
    
    cmd_handleItemClick:function(itemID,e) {
        this.fire("itemclick",itemID,e);
    },
    
    cmd_handleItemDblclick:function(itemID,e) {
        this.cmd("select",itemID, e.ctrlKey ? "toggle" : null);        
        this.fire("itemdblclick",itemID,e);
    },
    
    /**
     * Включен ли табличный режим
     **/
    info_gridMode:function() {
        return !!this.private_gridMode;
    },
    
    // Реакция на нажатие клавиш
    cmd_keydown:function(e) {
    
        switch(e.keyCode) {
            case 38:
            case 37:
                this.cmd("selectUp",e.shiftKey);
                return false;
            case 39:
            case 40:
                this.cmd("selectDown",e.shiftKey);
                return false;
            case 13:
                var sel = this.info("selection")[0];
                if(sel!==undefined) {
                    //this.cmd("handleItemclick",sel,e);
                    //this.cmd("handleItemDblclick",sel,e);
                }
                return false;
            case 65:
                if(e.ctrlKey) this.cmd("selectAll"); return false;
                break;
        }
    },
    
    /**
     * Поднимает выбранный элемент на одну позицию выше
     **/    
    cmd_moveSelectedItemUp:function() {
        var sel = this.info("selection");
        for(var i=0;i<this.data.length-1;i++) {
            var a = this.data[i];
            var b = this.data[i+1];
            for(var j in sel)
                if(sel[j]==b.id) {
                    this.data[i] = b;
                    this.data[i+1] = a;
                }
        }
        this.cmd("setData",this.data);
    },
    
    cmd_moveSelectedItemDown:function() {
        var sel = this.info("selection");
        for(var i=this.data.length-1;i>0;i--) {
            var a = this.data[i];
            var b = this.data[i-1];
            for(var j in sel)
                if(sel[j]==b.id) {
                    this.data[i] = b;
                    this.data[i-1] = a;
                }
        }
        this.cmd("setData",this.data);
    },
    
    /**
     * Ставит выбранный элемент на требуемую позицию
     **/    
    cmd_moveItem:function(id,position) { 
       
        var item = this.info("item",id);
        if(!item) {
            return;
        }
        
        var xid = this.info("itemComponent",id).id();
        
        pos = this.info("position",id);
        if(pos==position)
            return;
            
        var newItems = [];
        var set = false;
        var n = 0;
        
        
        for(var i=0;i<this.private_items.length;i++) {
        
            if(n==position) {
                newItems.push(xid);
                set = true;
                n++;
            }
        
            if(this.private_items[i]!=xid) {
                newItems.push(this.private_items[i]);
                n++;
            }
                
        }
       
        if(!set)
            newItems.push(xid);
            
            
        this.private_items = newItems;
          
        this.task("updateItemsLayout");

    },
    
    cmd_updateData:function() {
        this.cmd("setData",this.private_newData);
    },
    
    cmd_deleteSelectedItem:function() {
        var data = [];
        var sel = this.info("selection");
        for(var i in this.data) {
            var keep = true;
            for(var j in sel)
                if(this.data[i].id==sel[j])
                    keep = false;
            if(keep) data.push(this.data[i]);
        }
        this.cmd("setData",data);
    },
    
    cmd_destroy:function() {
        for(var i in this.bufferz)
            inx(this.bufferz[i]).cmd("destroy");
    }
    
});